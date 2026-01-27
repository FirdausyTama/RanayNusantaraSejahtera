<?php

namespace App\Http\Controllers\Api;

use App\Models\SPH;
use App\Models\SphLampiranGambar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SPHController extends Controller
{
    public function index()
    {
        return response()->json(SPH::with('user')->latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'nullable|date',
            'tempat' => 'nullable|string',
            'lampiran' => 'nullable|string',
            'hal' => 'nullable|string',
            'jabatan_tujuan' => 'nullable|string',
            'nama_perusahaan' => 'required|string',
            'alamat' => 'nullable|string',
            'detail_barang' => 'required|array',
            'total_keseluruhan' => 'required|numeric',
            'penandatangan' => 'required|string',
            'status' => 'nullable|in:Menunggu,Diterima,Ditolak',
            'keterangan' => 'nullable|string',
            'lampiran_gambar' => 'nullable|array',
            'lampiran_gambar.*' => 'file|image|max:10240',
        ]);

        $data['tanggal'] = $data['tanggal'] ?? Carbon::now()->toDateString();
        $data['user_id'] = auth()->id();

        $tahun = date('Y', strtotime($data['tanggal']));
        $bulan = date('n', strtotime($data['tanggal']));

        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ][$bulan];

        $kodeSurat = 'SPH';
        $kodeDivisi = 'XRAY';
        $kodePerusahaan = 'RNS';

        $lastSph = SPH::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->where('nomor_sph', 'LIKE', "%/{$kodeSurat}/{$kodeDivisi}/{$kodePerusahaan}-%/$tahun")
            ->latest('id')
            ->first();

        if ($lastSph) {
            $lastNumber = (int) explode('/', $lastSph->nomor_sph)[0];
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }

        $nomorSurat = "{$newNumber}/{$kodeSurat}/{$kodeDivisi}/{$kodePerusahaan}-{$bulanRomawi}/{$tahun}";

        $data['nomor_sph'] = $nomorSurat;

        // Remove lampiran_gambar from data before creating SPH
        $lampiranGambar = $data['lampiran_gambar'] ?? null;
        unset($data['lampiran_gambar']);

        $sph = SPH::create($data);

        // DEBUG: Cek apa yang diterima backend
        \Illuminate\Support\Facades\Log::info('Upload Request:', [
            'has_file' => $request->hasFile('lampiran_gambar'),
            'file_count' => $request->hasFile('lampiran_gambar') ? count($request->file('lampiran_gambar')) : 0,
            'files' => $request->file('lampiran_gambar')
        ]);

        // Handle file uploads
        if ($request->hasFile('lampiran_gambar')) {
            foreach ($request->file('lampiran_gambar') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . uniqid() . '_' . $originalName;
                $path = $file->storeAs('sph', $fileName, 'public');


                SphLampiranGambar::create([
                    'sph_id' => $sph->id,
                    'path_gambar' => 'sph/' . $fileName,
                    'nama_file' => $originalName,
                ]);
            }
        }

        // Hitung jumlah lampiran foto
        $jumlahLampiran = SphLampiranGambar::where('sph_id', $sph->id)->count();

        // Update field lampiran otomatis
        $sph->update([
            'lampiran' => $jumlahLampiran > 0
                ? $jumlahLampiran . ' Lembar'
                : '-'
        ]);

        // Load relationship
        $sph->load('lampiranGambar');

        return response()->json([
            'message' => 'SPH berhasil dibuat',
            'nomor_sph' => $nomorSurat,
            'data' => $sph
        ], 201);
    }


    public function show($id)
    {
        $sph = SPH::with(['lampiranGambar', 'user'])->findOrFail($id);

        // DEBUG: Uncomment line below to check data
        // dd($sph->lampiranGambar);

        // Transform lampiran gambar to include full URLs
        $sph->lampiran_gambar_urls = $sph->lampiranGambar->map(function ($lampiran) {
            return [
                'id' => $lampiran->id,
                'url' => url('storage/' . $lampiran->path_gambar),
                'nama_file' => $lampiran->nama_file,
            ];
        });

        return response()->json($sph);
    }


    public function update(Request $request, $id)
    {
        $sph = SPH::findOrFail($id);

        $data = $request->validate([
            'tanggal' => 'nullable|date',
            'tempat' => 'nullable|string',
            'lampiran' => 'nullable|string',
            'hal' => 'nullable|string',
            'jabatan_tujuan' => 'nullable|string',
            'nama_perusahaan' => 'nullable|string',
            'alamat' => 'nullable|string',
            'detail_barang' => 'nullable|array',
            'total_keseluruhan' => 'nullable|numeric',
            'penandatangan' => 'nullable|string',
            'status' => 'nullable|in:Menunggu,Diterima,Ditolak',
            'keterangan' => 'nullable|string',
            'lampiran_gambar' => 'nullable|array',
            'lampiran_gambar.*' => 'file|image|max:10240',
        ]);

        $data['tanggal'] = $data['tanggal'] ?? $sph->tanggal;

        unset($data['nomor_sph']);

        // Remove lampiran_gambar from data before updating SPH
        $lampiranGambar = $data['lampiran_gambar'] ?? null;
        unset($data['lampiran_gambar']);

        $sph->update($data);

        // Handle file uploads
        if ($request->hasFile('lampiran_gambar')) {
            foreach ($request->file('lampiran_gambar') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . uniqid() . '_' . $originalName;
                $path = $file->storeAs('sph', $fileName, 'public');


                SphLampiranGambar::create([
                    'sph_id' => $sph->id,
                    'path_gambar' => 'sph/' . $fileName,
                    'nama_file' => $originalName,
                ]);
            }
        }

        // Hitung jumlah lampiran foto
        $jumlahLampiran = SphLampiranGambar::where('sph_id', $sph->id)->count();

        // Update field lampiran otomatis
        $sph->update([
            'lampiran' => $jumlahLampiran > 0
                ? $jumlahLampiran . ' Lembar'
                : '-'
        ]);

        // Load relationship
        $sph->load('lampiranGambar');

        return response()->json([
            'message' => 'Data SPH berhasil diperbarui',
            'data' => $sph
        ]);
    }


    public function getAccepted()
    {
        $data = SPH::where('status', 'Diterima')
                    ->latest()
                    ->get();
        return response()->json([
            'message' => 'Data SPH diterima berhasil diambil',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        SPH::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function deleteLampiran($id)
    {
        $lampiran = SphLampiranGambar::findOrFail($id);
        $sphId = $lampiran->sph_id;
        
        // Hapus file fisik
        if (Storage::disk('public')->exists($lampiran->path_gambar)) {
            Storage::disk('public')->delete($lampiran->path_gambar);
        }

        // Hapus record
        $lampiran->delete();

        // Update jumlah lampiran di parent SPH
        $jumlahLampiran = SphLampiranGambar::where('sph_id', $sphId)->count();
        $sph = SPH::findOrFail($sphId);
        $sph->update([
            'lampiran' => $jumlahLampiran > 0 ? $jumlahLampiran . ' Lembar' : '-'
        ]);

        return response()->json([
            'message' => 'Lampiran berhasil dihapus',
            'sisa_lampiran' => $jumlahLampiran
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surat_penawarans,id',
        ]);

        SPH::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Data terpilih berhasil dihapus']);
    }
}
