<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\PembelianItem;
use App\Models\CicilanPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $query = Pembelian::with(['items', 'cicilan'])->latest();

        if ($status) {
            $query->where('status_pembayaran', $status);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_order' => 'required|unique:pembelians,no_order',
            'penerima_nama' => 'required|string',
            'penerima_alamat' => 'required|string',
            'penerima_telepon' => 'nullable|string',
            'tgl_transaksi' => 'required|date',
            'status_pengiriman' => 'required|in:dikirim,menunggu,cicilan',
            'status_pembayaran' => 'required|in:cicilan,lunas,belum_lunas',
            'grand_total' => 'required|numeric',
            'total_cicilan' => 'nullable|numeric', // DP
            'tenor' => 'nullable|integer|min:1|required_if:status_pembayaran,cicilan',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric',
            'items.*.total_harga' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $dp = (int) ($request->total_cicilan ?? 0);
            $grandTotal = (int) $request->grand_total;
            $tenor = (int) ($request->tenor ?? 0);
            $sisaTagihan = $grandTotal - $dp;

            $pembelian = Pembelian::create([
                'no_order' => $request->no_order,
                'penerima_nama' => $request->penerima_nama,
                'penerima_alamat' => $request->penerima_alamat,
                'penerima_telepon' => $request->penerima_telepon,
                'tgl_transaksi' => $request->tgl_transaksi,
                'status_pengiriman' => $request->status_pengiriman,
                'status_pembayaran' => $request->status_pembayaran,
                'grand_total' => $grandTotal,
                'total_cicilan' => $dp,          // DP ASLI
                'sisa_cicilan' => $sisaTagihan, // FIX
                'tenor' => $tenor,
                'nilai_cicilan' => 0,             // diupdate nanti
            ]);

            foreach ($request->items as $item) {
                PembelianItem::create([
                    'pembelian_id' => $pembelian->id,
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga' => $item['total_harga'],
                ]);

                // Deduct stock quantity when purchase is created
                $stokItem = \App\Models\Stok::where('nama_barang', $item['nama_barang'])->first();
                if ($stokItem) {
                    $newQuantity = max(0, $stokItem->jumlah - $item['jumlah']);
                    $stokItem->update(['jumlah' => $newQuantity]);
                }
            }

            // ===== GENERATE CICILAN =====
            if ($request->status_pembayaran === 'cicilan' && $tenor > 0) {
                $monthlyBase = intdiv($sisaTagihan, $tenor);
                $remainder = $sisaTagihan % $tenor;

                $tanggal = Carbon::parse($request->tgl_transaksi);

                for ($i = 1; $i <= $tenor; $i++) {
                    $jumlah = $monthlyBase;
                    if ($i === $tenor) {
                        $jumlah += $remainder;
                    }

                    CicilanPembelian::create([
                        'pembelian_id' => $pembelian->id,
                        'cicilan_ke' => $i,
                        'tanggal_jatuh_tempo' => $tanggal->copy()->addMonths($i),
                        'jumlah_cicilan' => $jumlah,
                        'status' => 'belum_lunas',
                    ]);
                }

                $pembelian->update([
                    'nilai_cicilan' => $monthlyBase,
                    'sisa_cicilan' => $sisaTagihan,
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Pembelian berhasil disimpan',
                'data' => $pembelian->load(['items', 'cicilan'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal menyimpan pembelian',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::with(['items', 'cicilan'])->find($id);
        if (!$pembelian) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        return response()->json($pembelian);
    }

    public function update(Request $request, $id)
    {
        $pembelian = Pembelian::find($id);
        if (!$pembelian) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            $dp = (int) ($request->total_cicilan ?? 0);
            $grandTotal = (int) $request->grand_total;
            $tenor = (int) ($request->tenor ?? 0);
            $sisaTagihan = $grandTotal - $dp;

            $pembelian->update([
                'penerima_nama' => $request->penerima_nama,
                'penerima_alamat' => $request->penerima_alamat,
                'penerima_telepon' => $request->penerima_telepon,
                'tgl_transaksi' => $request->tgl_transaksi,
                'status_pengiriman' => $request->status_pengiriman,
                'status_pembayaran' => $request->status_pembayaran,
                'grand_total' => $grandTotal,
                'total_cicilan' => $dp,
                'sisa_cicilan' => $sisaTagihan,
                'tenor' => $tenor,
            ]);

            PembelianItem::where('pembelian_id', $pembelian->id)->delete();
            foreach ($request->items as $item) {
                PembelianItem::create([
                    'pembelian_id' => $pembelian->id,
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga' => $item['total_harga'],
                ]);
            }

            // Cek apakah ada cicilan yang sudah lunas (safety check)
            $hasPaidInstallments = CicilanPembelian::where('pembelian_id', $pembelian->id)
                ->where('status', 'lunas') // Asumsi status 'lunas' menandakan sudah bayar
                ->exists();

            if ($request->status_pembayaran === 'cicilan' && $tenor > 0) {
                // If NO installments are paid yet, we can safely regenerate the schedule
                // This allows correcting DP/Tenor mistakes
                if (!$hasPaidInstallments) {
                    // Delete existing unpaid installments
                    CicilanPembelian::where('pembelian_id', $pembelian->id)->delete();

                    $monthlyBase = intdiv($sisaTagihan, $tenor);
                    $remainder = $sisaTagihan % $tenor;

                    $tanggal = Carbon::parse($request->tgl_transaksi);

                    for ($i = 1; $i <= $tenor; $i++) {
                        $jumlah = $monthlyBase;
                        if ($i === $tenor) {
                            $jumlah += $remainder;
                        }

                        CicilanPembelian::create([
                            'pembelian_id' => $pembelian->id,
                            'cicilan_ke' => $i,
                            'tanggal_jatuh_tempo' => $tanggal->copy()->addMonths($i),
                            'jumlah_cicilan' => $jumlah,
                            'status' => 'belum_lunas',
                        ]);
                    }

                    $pembelian->update([
                        'nilai_cicilan' => $monthlyBase,
                        'sisa_cicilan' => $sisaTagihan,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Pembelian berhasil diupdate',
                'data' => $pembelian->load(['items', 'cicilan'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal update pembelian',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $pembelian = Pembelian::find($id);
        if (!$pembelian) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $pembelian->delete();
        return response()->json(['message' => 'Pembelian berhasil dihapus']);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pembelians,id',
        ]);

        Pembelian::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Data terpilih berhasil dihapus']);
    }

}
