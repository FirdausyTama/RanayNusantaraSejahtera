<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Pembelian;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return response()->json(
            Invoice::with(['items', 'user'])->orderBy('id', 'desc')->get()
        );
    }

    /**
     * Get list of pembelian for dropdown
     */
    public function getPembelianList()
    {
        $pembelians = Pembelian::with('items')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($pembelians);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal_invoice' => 'nullable|date',
            'nama_penerima' => 'required|string',
            'pembelian_id' => 'nullable|exists:pembelians,id',

            'items' => 'nullable|array',
            'items.*.nama_barang' => 'required_without:pembelian_id|string',
            'items.*.qty' => 'required_without:pembelian_id|integer|min:1',
            'items.*.harga_satuan' => 'required_without:pembelian_id|numeric|min:0',


            'berat_total' => 'nullable|numeric|min:0',
            'harga_per_kg' => 'nullable|numeric|min:0',
            'estimasi_ongkir' => 'nullable|numeric|min:0',

            'penandatangan' => 'nullable|string',
        ]);

        $tanggal = $data['tanggal_invoice'] ?? now()->format('Y-m-d');

        /* =======================
         * GENERATE NOMOR INVOICE
         * ======================= */
        $bulan = date('n', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        $bulanRomawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$bulan];
        $kode = 'INV-SPH';

        $lastInvoice = Invoice::whereYear('tanggal_invoice', $tahun)
            ->whereMonth('tanggal_invoice', $bulan)
            ->where('nomor_invoice', 'LIKE', "%/{$kode}/{$bulanRomawi}/{$tahun}")
            ->latest('id')
            ->first();

        $newNumber = $lastInvoice
            ? str_pad(((int) explode('/', $lastInvoice->nomor_invoice)[0]) + 1, 2, '0', STR_PAD_LEFT)
            : '01';

        $nomorInvoice = "{$newNumber}/{$kode}/{$bulanRomawi}/{$tahun}";

        /* =======================
         * ONGKIR
         * ======================= */
        $beratTotal = $data['berat_total'] ?? null;
        $hargaPerKg = $data['harga_per_kg'] ?? null;

        $estimasiOngkir = ($beratTotal !== null && $hargaPerKg !== null)
            ? $beratTotal * $hargaPerKg
            : ($data['estimasi_ongkir'] ?? 0);

        $penandatangan = $data['penandatangan'] ?? 'Dewi Sulistiowati';

        /* =======================
         * CREATE INVOICE
         * ======================= */
        $invoice = Invoice::create([
            'tanggal_invoice' => $tanggal,
            'nomor_invoice' => $nomorInvoice,
            'nama_penerima' => $data['nama_penerima'],
            'pembelian_id' => $data['pembelian_id'] ?? null,
            'total_pembayaran' => 0,

            'berat_total' => $beratTotal,
            'harga_per_kg' => $hargaPerKg,
            'estimasi_ongkir' => $estimasiOngkir,

            'penandatangan' => $penandatangan,
            'user_id' => auth()->id(),
        ]);

        $totalPembayaran = 0;

        /* =======================
         * ITEMS
         * ======================= */
        if (!empty($data['pembelian_id'])) {
            $pembelian = Pembelian::with('items')->findOrFail($data['pembelian_id']);

            foreach ($pembelian->items as $item) {
                $invoice->items()->create([
                    'nama_barang' => $item->nama_barang,
                    'qty' => $item->jumlah,
                    'harga_satuan' => $item->harga_satuan,
                    'total_harga' => $item->total_harga,
                ]);

                $totalPembayaran += $item->total_harga;
            }
        } elseif (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $totalHarga = $item['qty'] * $item['harga_satuan'];

                $invoice->items()->create([
                    'nama_barang' => $item['nama_barang'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga' => $totalHarga,
                ]);

                $totalPembayaran += $totalHarga;
            }
        }

        /* =======================
         * TOTAL + ONGKIR
         * ======================= */
        $totalPembayaran += $estimasiOngkir;
        $invoice->update(['total_pembayaran' => $totalPembayaran]);

        return response()->json([
            'message' => 'Invoice berhasil dibuat',
            'data' => $invoice->load('items')
        ], 201);
    }

    public function show($id)
    {
        return response()->json(
            Invoice::with(['items', 'user'])->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        $data = $request->validate([
            'tanggal_invoice' => 'nullable|date',
            'nama_penerima' => 'required|string',

            'items' => 'required|array',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.nama_barang' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',

            'berat_total' => 'nullable|numeric|min:0',
            'harga_per_kg' => 'nullable|numeric|min:0',
            'estimasi_ongkir' => 'nullable|numeric|min:0',

            'penandatangan' => 'nullable|string',
        ]);

        $beratTotal = $data['berat_total'] ?? $invoice->berat_total;
        $hargaPerKg = $data['harga_per_kg'] ?? $invoice->harga_per_kg;

        $estimasiOngkir = ($beratTotal !== null && $hargaPerKg !== null)
            ? $beratTotal * $hargaPerKg
            : ($data['estimasi_ongkir'] ?? $invoice->estimasi_ongkir ?? 0);

        $penandatangan = $data['penandatangan'] ?? $invoice->penandatangan;

        $invoice->update([
            'tanggal_invoice' => $data['tanggal_invoice'] ?? $invoice->tanggal_invoice,
            'nama_penerima' => $data['nama_penerima'],
            'berat_total' => $beratTotal,
            'harga_per_kg' => $hargaPerKg,
            'estimasi_ongkir' => $estimasiOngkir,
            'penandatangan' => $penandatangan,
        ]);

        $totalPembayaran = 0;
        $itemIds = [];

        foreach ($data['items'] as $item) {
            $totalHarga = $item['qty'] * $item['harga_satuan'];
            $totalPembayaran += $totalHarga;

            if (isset($item['id'])) {
                $invoiceItem = $invoice->items()->find($item['id']);
                if ($invoiceItem) {
                    $invoiceItem->update([
                        'nama_barang' => $item['nama_barang'],
                        'qty' => $item['qty'],
                        'harga_satuan' => $item['harga_satuan'],
                        'total_harga' => $totalHarga,
                    ]);
                    $itemIds[] = $invoiceItem->id;
                }
            } else {
                $newItem = $invoice->items()->create([
                    'nama_barang' => $item['nama_barang'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga' => $totalHarga,
                ]);
                $itemIds[] = $newItem->id;
            }
        }

        $invoice->items()->whereNotIn('id', $itemIds)->delete();

        $totalPembayaran += $estimasiOngkir;
        $invoice->update(['total_pembayaran' => $totalPembayaran]);

        return response()->json([
            'message' => 'Invoice berhasil diperbarui',
            'data' => $invoice->load('items')
        ]);
    }

    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:invoices,id',
        ]);

        Invoice::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Data terpilih berhasil dihapus']);
    }

}
