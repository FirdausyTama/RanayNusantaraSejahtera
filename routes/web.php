<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('landing');
});
Route::get('/login', function () {
    return view('Auth.login');
})->name('login');

Route::get('/register', function () {
    return view('Auth.register');
});
Route::get('/logout', function () {
    return view('Auth.logout');
});


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard.dashboard');
});


/*
|--------------------------------------------------------------------------
| KELOLA DATA (Stok & Pembelian)
|--------------------------------------------------------------------------
*/
Route::get('/kelola-stok', function () {
    return view('stok.kelola-stok');
});
Route::get('/riwayat-pembelian', function () {
    return view('pembelian.riwayat-pembelian');
});
Route::get('/kelola-pembelian', function () {
    return view('pembelian.kelola-pembelian');
});


/*
|--------------------------------------------------------------------------
| DOKUMEN PAGES
|--------------------------------------------------------------------------
*/
Route::get('/sph', function () {
    return view('dokumen.sph');
});
Route::get('/surat-jalan', function () {
    return view('dokumen.surat-jalan');
});
Route::get('/kwitansi', function () {
    return view('dokumen.kwitansi');
});
Route::get('/invoice', function () {
    return view('dokumen.invoice');
});


/*
|--------------------------------------------------------------------------
| DETAIL PAGES (Stok & Dokumen)
|--------------------------------------------------------------------------
*/
Route::get('/stok/detail-stok/{id}', function ($id) {
    return view('stok.detail-stok', ['id' => $id]);
})->name('stok.detail');





Route::get('/detail-kwitansi/{id}', function ($id) {
    if (!is_numeric($id)) {
        return redirect('/kwitansi')->with('error', 'ID Kwitansi tidak valid');
    }
    return view('dokumen.detail-kwitansi', compact('id'));
})->name('detail.kwitansi')->where('id', '[0-9]+');

Route::get('/detail-surat-jalan/{id}', function ($id) {
    if (!is_numeric($id)) {
        return redirect('/surat-jalan')->with('error', 'ID Surat Jalan tidak valid');
    }
    return view('dokumen.detail-surat-jalan', compact('id'));
});

Route::get('/detail-sph/{id}', function ($id) {
    return view('dokumen.template.detail-sph');
});
Route::get('/detail-invoice/{id}', function ($id) {
    return view('dokumen.template.detail-invoice');
});


/*
|--------------------------------------------------------------------------
| PRINT TEMPLATE
|--------------------------------------------------------------------------
*/
Route::get('/print-sph/{id?}', function () {
    return view('dokumen.template.print-sph');
});
Route::get('/print-invoice/{id?}', function () {
    return view('dokumen.template.print-invoice');
});
Route::get('/print-kwitansi/{id?}', function () {
    return view('dokumen.template.print-kwitansi');
});
Route::get('/print-surat-jalan/{id?}', function () {
    return view('dokumen.template.print-surat-jalan');
});


/*
|--------------------------------------------------------------------------
| ADMIN PAGES
|--------------------------------------------------------------------------
*/
Route::get('/kelola-admin', function () {
    return view('kelola-admin');
});


/*
|--------------------------------------------------------------------------
| ERROR PAGE
|--------------------------------------------------------------------------
*/
Route::get('/eror-404', function () {
    return view('eror-404');
});

