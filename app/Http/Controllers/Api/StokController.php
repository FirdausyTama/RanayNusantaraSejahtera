<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stok;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StokController extends Controller
{
    /**
     * Tampilkan semua stok
     */
    public function index(Request $request)
    {
        $stok = Stok::with(['user', 'images'])
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Data stok berhasil diambil.',
            'data' => $stok
        ]);
    }

    /**
     * Simpan stok baru
     */
    public function store(Request $request)
    {
        // Sanitize foto input: if it's not a file (e.g. formatting string "null"), remove it
        if ($request->has('foto') && !$request->hasFile('foto')) {
            $request->request->remove('foto');
        }

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'fotos' => 'nullable|array',
            'fotos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'video' => 'nullable|mimetypes:video/mp4,video/avi,video/mov|max:10240',
            'harga' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'nullable|string|in:pcs,box,unit,pack,kg,liter',
            'merek' => 'nullable|string|max:255',
            'kode_sku' => 'nullable|string|max:255',
            'panjang' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'berat' => 'nullable|numeric|min:0',
            'tgl_masuk' => 'required|date',
            'tgl_keluar' => 'nullable|date|after_or_equal:tgl_masuk',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('stok/foto', 'public');
        } elseif ($request->hasFile('fotos') && count($request->file('fotos')) > 0) {
            // Jika foto utama tidak ada tapi fotos array ada, ambil yg pertama jadi foto utama
            $validated['foto'] = $request->file('fotos')[0]->store('stok/foto', 'public');
        }

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('stok/video', 'public');
        }

        $validated['user_id'] = auth()->id();
        $stok = Stok::create($validated);

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $photo) {
                $path = $photo->store('stok/foto', 'public');
                $stok->images()->create(['image_path' => $path]);
            }
        }

        // Record History
        $this->recordHistory($stok, 'tambah_baru', $stok->jumlah, $stok->harga, 'Stok awal');

        return response()->json([
            'message' => 'Stok berhasil ditambahkan',
            'data' => $stok->load(['user', 'images'])
        ], 201);
    }

    /**
     * Tampilkan detail stok
     */
    public function show($id)
    {
        $stok = Stok::with(['user', 'images'])->findOrFail($id);

        return response()->json([
            'message' => 'Detail stok berhasil diambil.',
            'data' => $stok
        ]);
    }

    /**
     * Update stok
     */
    public function update(Request $request, $id)
    {
        $stok = Stok::findOrFail($id);
        $oldJumlah = $stok->jumlah; // Simpan jumlah lama sebelum update

        // Sanitize foto input
        if ($request->has('foto') && !$request->hasFile('foto')) {
            $request->request->remove('foto');
        }

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'fotos' => 'nullable|array',
            'fotos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'video' => 'nullable|mimetypes:video/mp4,video/avi,video/mov|max:10240',
            'harga' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'nullable|string|in:pcs,box,unit,pack,kg,liter',
            'merek' => 'nullable|string|max:255',
            'kode_sku' => 'nullable|string|max:255',
            'panjang' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'berat' => 'nullable|numeric|min:0',
            'tgl_masuk' => 'required|date',
            'tgl_keluar' => 'nullable|date|after_or_equal:tgl_masuk', // Fixed validation rule
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('stok/foto', 'public');
        }

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('stok/video', 'public');
        }

        $validated['user_id'] = auth()->id();
        $stok->update($validated);

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $photo) {
                $path = $photo->store('stok/foto', 'public');
                $stok->images()->create(['image_path' => $path]);
            }
        }

        // Logic History: Cek jika ada penambahan stok (Restock)
        // Kita bandingkan input baru dengan oldJumlah
        $newJumlah = intval($request->jumlah);
        $diff = $newJumlah - $oldJumlah;

        if ($diff > 0) {
            // Ada penambahan stok
             $this->recordHistory($stok, 'restock', $diff, $stok->harga, 'Restock / Koreksi Tambah');
        }

        return response()->json([
            'message' => 'Stok berhasil diperbarui',
            'data' => $stok->load(['user', 'images'])
        ]);
    }

    /**
     * Hapus stok
     */
    public function destroy($id)
    {
        $stok = Stok::findOrFail($id);
        
        // Delete associated images if any
        if ($stok->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($stok->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($stok->image);
        }

        $stok->delete();

        return response()->json([
            'message' => 'Stok berhasil dihapus'
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:stoks,id',
        ]);

        $stoks = Stok::whereIn('id', $request->ids)->get();

        foreach ($stoks as $stok) {
            // Delete associated images if any
            if ($stok->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($stok->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($stok->image);
            }
            $stok->delete();
        }

        return response()->json(['message' => 'Data terpilih berhasil dihapus']);
    }


    /**
     * Summary stok masuk, keluar, dan total
     */
    public function summary()
    {
        $totalMasuk = Stok::whereNotNull('tgl_masuk')->sum('jumlah');
        $totalKeluar = Stok::whereNotNull('tgl_keluar')->sum('jumlah');

        return response()->json([
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'total_keseluruhan' => $totalMasuk - $totalKeluar,
        ]);
    }

    public function weeklySummary()
    {
        $now = Carbon::now();
        $sevenDaysAgo = Carbon::now()->subDays(7);


        $totalMasuk = Stok::whereNotNull('tgl_masuk')->sum('jumlah');
        $totalKeluar = Stok::whereNotNull('tgl_keluar')->sum('jumlah');
        $totalSekarang = $totalMasuk - $totalKeluar;


        $totalMasukLalu = Stok::where('tgl_masuk', '<', $sevenDaysAgo)->sum('jumlah');
        $totalKeluarLalu = Stok::where('tgl_keluar', '<', $sevenDaysAgo)->sum('jumlah');
        $total7HariLalu = $totalMasukLalu - $totalKeluarLalu;


        $persenTotal = ($total7HariLalu != 0)
            ? (($totalSekarang - $total7HariLalu) / abs($total7HariLalu)) * 100
            : 100;

        return response()->json([
            'masuk_7hari' => $masuk7Hari ?? 0,
            'keluar_7hari' => $keluar7Hari ?? 0,
            'persen_masuk' => round($persenMasuk ?? 0, 1),
            'persen_keluar' => round($persenKeluar ?? 0, 1),

            'persen_total' => round($persenTotal, 1),
        ]);
    }

    /**
     * Hapus gambar spesifik stok
     */
    public function destroyImage($id)
    {
        $image = \App\Models\StokImage::find($id);

        if (!$image) {
            return response()->json([
                'message' => 'Foto tidak ditemukan'
            ], 404);
        }

        // Hapus file fisik
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json([
            'message' => 'Foto berhasil dihapus'
        ]);
    }

    /**
     * Get Expenditure Statistics (Total Spend per Month & Year)
     */
    public function getExpenditureStats()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Hitung pengeluaran (tambah_baru & restock)
        // Gunakan whereIn untuk jenis yang dianggap 'pengeluaran'
        $queryBase = RiwayatStok::whereIn('jenis', ['tambah_baru', 'restock', 'koreksi_tambah']);

        $monthlyExpenditure = (clone $queryBase)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_harga');

        $yearlyExpenditure = (clone $queryBase)
            ->whereYear('created_at', $currentYear)
            ->sum('total_harga');
        
        // Total Stok Masuk (Quantity)
        $monthlyQty = (clone $queryBase)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('jumlah');
            
         $yearlyQty = (clone $queryBase)
            ->whereYear('created_at', $currentYear)
            ->sum('jumlah');

        return response()->json([
            'month_expenditure' => (float) $monthlyExpenditure,
            'year_expenditure' => (float) $yearlyExpenditure,
            'month_qty' => (int) $monthlyQty, // Optional: if needed to show detailed qty
            'year_qty' => (int) $yearlyQty
        ]);
    }

    /**
     * Helper to record stock history
     */
    private function recordHistory($stok, $jenis, $jumlah, $harga_satuan, $keterangan = null)
    {
        if ($jumlah <= 0) return;

        RiwayatStok::create([
            'stok_id' => $stok->id,
            'user_id' => auth()->id() ?? ($stok->user_id ?? 1), // Fallback to avoid error
            'jenis' => $jenis,
            'jumlah' => $jumlah,
            'harga_satuan' => $harga_satuan,
            'total_harga' => $jumlah * $harga_satuan,
            'keterangan' => $keterangan
        ]);
    }
}
