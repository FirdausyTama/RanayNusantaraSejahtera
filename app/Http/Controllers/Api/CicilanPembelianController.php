<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CicilanPembelian;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CicilanPembelianController extends Controller
{
    public function update(Request $request, $id)
    {
        $cicilan = CicilanPembelian::find($id);
        if (!$cicilan) {
            return response()->json(['error' => 'Data cicilan tidak ditemukan'], 404);
        }

        if ($cicilan->status === 'lunas') {
            return response()->json(['message' => 'Cicilan sudah lunas'], 200);
        }

        DB::beginTransaction();
        try {

            $cicilan->update([
                'status' => 'lunas',
                'tanggal_bayar' => Carbon::now(),
                'keterangan' => $request->keterangan
            ]);


            $pembelian = Pembelian::find($cicilan->pembelian_id);
            if ($pembelian) {
                $pembelian->decrement('sisa_cicilan', $cicilan->jumlah_cicilan);
                $pembelian->increment('total_cicilan', $cicilan->jumlah_cicilan);


                $unpaid = CicilanPembelian::where('pembelian_id', $pembelian->id)
                    ->where('status', 'belum_lunas')
                    ->exists();

                if (!$unpaid) {
                    $pembelian->update(['status_pembayaran' => 'lunas']);
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Cicilan berhasil dibayar',
                'data' => $cicilan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal update cicilan', 'detail' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'pembelian_id' => 'required|exists:pembelians,id',
            'jumlah_cicilan' => 'required|numeric|min:1',
            'tanggal_jatuh_tempo' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $pembelian = Pembelian::findOrFail($request->pembelian_id);

            // Cegah cicilan melebihi sisa
            if ($request->jumlah_cicilan > $pembelian->sisa_cicilan) {
                return response()->json([
                    'message' => 'Jumlah cicilan melebihi sisa cicilan'
                ], 422);
            }

            $cicilan = CicilanPembelian::create([
                'pembelian_id' => $pembelian->id,
                'jumlah_cicilan' => $request->jumlah_cicilan,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'status' => 'belum_lunas',
                'keterangan' => $request->keterangan
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Cicilan berhasil ditambahkan',
                'data' => $cicilan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal menambahkan cicilan',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

}
