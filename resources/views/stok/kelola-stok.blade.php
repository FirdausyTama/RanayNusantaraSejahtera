<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Stok | RNS - Ranay Nusantara Sejathera</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/js/head.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .preview-image,
        .preview-video {
            display: none;
            margin-top: 10px;
            position: relative;
        }

        .preview-image.show,
        .preview-video.show {
            display: block;
        }

        .preview-image img {
            max-width: 100%;
            max-height: 150px;
            object-fit: contain;
            border-radius: 4px;
        }

        .preview-video video {
            max-width: 100%;
            max-height: 150px;
            border-radius: 4px;
        }

        .remove-media {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 14px;
            line-height: 1;
            cursor: pointer;
            z-index: 5;
        }

        .remove-media:hover {
            background: #c82333;
        }

        .upload-box {
            cursor: pointer;
        }
    </style>
</head>

<body data-menu-color="light" data-sidebar="default">
    @include('navbar.navbar')
    <div id="app-layout">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-18 fw-semibold m-0">Kelola Stok</h4>
                        </div>

                        <div class="text-end">
                            <ol class="breadcrumb m-0 py-0">
                                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Halaman</a></li>
                                <li class="breadcrumb-item active">Kelola Stok</li>
                            </ol>
                        </div>
                    </div>

                    <div class="row">
                        <!-- 1. Total Stok Masuk (Qty) -->
                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="p-2 border border-success border-opacity-10 bg-success-subtle rounded-2 me-2">
                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                <i class="mdi mdi-check-circle-outline text-white fs-5"></i>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-dark fs-15 fw-semibold">Total Stok Masuk (Qty)</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalStokMasuk">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Total Stok Masuk (Rp Bulan Ini) -->
                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="p-2 border border-purple border-opacity-10 rounded-2 me-2" style="background-color: #f3e6ff;">
                                            <div class="bg-purple rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px; background-color: #6f42c1 !important;">
                                                <i class="mdi mdi-cash-plus text-white fs-5"></i>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-dark fs-15 fw-semibold">Nilai Stok Masuk (Bln)</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalStokMasukMonthValue">Rp 0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Total Stok Masuk (Rp Tahun Ini) -->
                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="p-2 border border-purple border-opacity-10 rounded-2 me-2" style="background-color: #f3e6ff;">
                                            <div class="bg-purple rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px; background-color: #6f42c1 !important;">
                                                <i class="mdi mdi-cash-multiple text-white fs-5"></i>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-dark fs-15 fw-semibold">Nilai Stok Masuk (Thn)</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalStokMasukYearValue">Rp 0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Total Stok Keluar -->
                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="p-2 border border-warning border-opacity-10 bg-warning-subtle rounded-2 me-2">
                                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                <i class="mdi mdi-alert-outline text-white fs-5"></i>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-dark fs-15 fw-semibold">Total Stok Keluar</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalStokKeluar">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Total Stok Keseluruhan -->
                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="p-2 border border-primary border-opacity-10 bg-primary-subtle rounded-2 me-2">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                <i class="mdi mdi-package-variant-closed text-white fs-5"></i>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-dark fs-15 fw-semibold">Total Stok Keseluruhan</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalStokKeseluruhan">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                                <div>
                                    <h5 class="fw-semibold mb-1">Daftar Stok Produk</h5>
                                    <p class="text-muted mb-0">Kelola dan pantau stok produk Anda</p>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <!-- Bulk Delete Button (Hidden by default) -->
                                    <button type="button" id="btnBulkDelete" class="btn btn-danger text-white border-0 shadow-sm" style="display: none;" onclick="bulkDeleteStok()">
                                        <i class="mdi mdi-delete-sweep me-1"></i> Hapus (<span id="selectedCount">0</span>)
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-light border dropdown-toggle" type="button"
                                            id="filterStatus" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-filter-variant me-1"></i>
                                            <span id="selectedStatusFilter">Semua Status</span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="filterStatus">
                                            <li><a class="dropdown-item" href="#" onclick="setStockStatusFilter('Semua Status')">Semua Status</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" onclick="setStockStatusFilter('Stok Aman')">Stok Aman</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="setStockStatusFilter('Stok Menipis')">Stok Menipis</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="setStockStatusFilter('Stok Habis')">Stok Habis</a></li>
                                        </ul>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-light border dropdown-toggle" type="button"
                                            id="filterWaktu" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-calendar-range me-1"></i>
                                            <span id="selectedFilter">Semua Waktu</span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="filterWaktu">
                                            <li><a class="dropdown-item" href="#" onclick="setFilter('Hari Ini')">Hari
                                                    Ini</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="setFilter('Minggu Ini')">Minggu Ini</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="setFilter('Bulan Ini')">Bulan
                                                    Ini</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="setFilter('Semua Waktu')">Semua Waktu</a></li>
                                        </ul>
                                    </div>
                                    <form class="app-search">
                                        <div class="position-relative topbar-search">
                                            <input type="text" class="form-control ps-4" placeholder="Search..."
                                                style="min-width: 200px;" id="searchInput" onkeyup="searchProduct()" />
                                            <i
                                                class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle table-hover" id="productTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 40px;">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" id="selectAllStok" onchange="toggleSelectAllStok(this)">
                                                </div>
                                            </th>
                                            <th scope="col" class="text-center" style="width: 80px;">Foto</th>
                                            <th scope="col">Nama Produk</th>
                                            <th scope="col" class="text-center">Tanggal</th>
                                            <th scope="col" class="text-center">Harga</th>
                                            <th scope="col" class="text-center">Status Stok</th>
                                            <th scope="col" class="text-center">Jumlah</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stok-table-body">
                                        <tr>
                                            <td colspan="7" class="text-center py-3 text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                            <div class="row align-items-center mt-3" id="pagination-container" style="display: none;">
                                <div class="col-sm-6">
                                    <p class="text-muted mb-0" id="pagination-info">Menampilkan 0-0 dari 0 transaksi</p>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="pagination pagination-rounded justify-content-end mb-0"
                                        id="pagination-controls">

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col fs-13 text-muted text-center">
                            &copy;
                            <script>
                                document.write(new Date().getFullYear())
                            </script> - Made with <span class="mdi mdi-heart text-danger"></span> by <a href="#!"
                                class="text-reset fw-semibold">TI UMY 22</a>
                        </div>
                    </div>
                </div>
            </footer>

            <button type="button" class="btn btn-primary rounded-circle shadow-lg"
                style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 1000;"
                data-bs-toggle="modal" data-bs-target="#modalTambahStok" title="Tambah Stok">
                <i class="mdi mdi-plus fs-3 text-white"></i>
            </button>
        </div>
    </div>


    <div class="modal fade" id="modalTambahStok" tabindex="-1" aria-labelledby="modalTambahStokLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-semibold" id="modalTambahStokLabel">
                        <i class="mdi mdi-package-variant-plus text-warning me-2"></i>Input Stok
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formTambahStok" enctype="multipart/form-data">
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-muted">Informasi Dasar</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="namaBarang" class="form-label">Nama Barang <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="namaBarang"
                                        placeholder="Masukkan nama barang">
                                </div>
                                <div class="col-md-6">
                                    <label for="kodeSKU" class="form-label">Kode SKU</label>
                                    <input type="text" class="form-control" id="kodeSKU" placeholder="SKU001">
                                </div>
                                <div class="col-md-6">
                                    <label for="merek" class="form-label">Merek</label>
                                    <input type="text" class="form-control" id="merek" placeholder="Nama merek">
                                </div>
                                <div class="col-md-6">
                                    <label for="satuan" class="form-label">Satuan</label>
                                    <select class="form-select" id="satuan">
                                        <option selected>Pilih satuan</option>
                                        <option value="pcs">Pcs</option>
                                        <option value="box">Box</option>
                                        <option value="unit">Unit</option>
                                        <option value="pack">Pack</option>
                                        <option value="kg">Kg</option>
                                        <option value="liter">Liter</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-muted">Foto/Video Barang</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border border-2 border-dashed rounded p-4 text-center upload-box"
                                        onclick="document.getElementById('uploadVideo').click()"
                                        style="cursor: pointer;">
                                        <input type="file" id="uploadVideo" name="video"
                                            accept="video/mp4,video/avi,video/mov" style="display: none;"
                                            onchange="handleVideoUpload(this)">
                                        <div id="videoPlaceholder">
                                            <i class="mdi mdi-video-outline fs-1 text-muted d-block mb-2"></i>
                                            <span class="text-muted" id="videoFileName">Tambah Video</span>
                                        </div>
                                        <div class="preview-video" id="videoPreview">
                                            <button type="button" class="remove-media"
                                                onclick="removeVideo(event)">×</button>
                                            <video id="videoElement" controls style="width: 100%;"></video>
                                            <small class="text-muted d-block mt-2" id="videoFileNamePreview"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border border-2 border-dashed rounded p-4 text-center upload-box"
                                        onclick="document.getElementById('uploadFoto').click()"
                                        style="cursor: pointer;">
                                        <input type="file" id="uploadFoto" name="fotos[]" accept="image/*" multiple
                                            style="display: none;" onchange="handleFotoUpload(this)">
                                        <div id="fotoPlaceholder">
                                            <i class="mdi mdi-camera-outline fs-1 text-muted d-block mb-2"></i>
                                            <span class="text-muted" id="fotoFileName">Tambah Foto (Bisa banyak)</span>
                                        </div>
                                        <div class="row g-2 mt-2" id="fotoPreviewContainer" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-muted">Harga & Stok</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="tanggal" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tgl_masuk">
                                </div>
                                <div class="col-md-4">
                                    <label for="hargaJual" class="form-label">Harga Jual <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" oninput="formatRupiahInput(this)"
                                            id="hargaJual" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="jumlah" class="form-label">Jumlah</label>
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">
                                            <i class="mdi mdi-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" id="jumlah" value="0"
                                            min="0">
                                        <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">
                                            <i class="mdi mdi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-semibold mb-3 text-muted">Dimensi & Berat (optional)</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="panjang" class="form-label">Panjang (cm)</label>
                                    <input type="number" class="form-control" id="panjang" placeholder="0">
                                </div>
                                <div class="col-md-3">
                                    <label for="lebar" class="form-label">Lebar (cm)</label>
                                    <input type="number" class="form-control" id="lebar" placeholder="0">
                                </div>
                                <div class="col-md-3">
                                    <label for="tinggi" class="form-label">Tinggi (cm)</label>
                                    <input type="number" class="form-control" id="tinggi" placeholder="0">
                                </div>
                                <div class="col-md-3">
                                    <label for="berat" class="form-label">Berat (gr)</label>
                                    <input type="number" class="form-control" id="berat" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitTambahStok()">
                        <i class="mdi mdi-content-save-outline me-1"></i>Tambah Barang
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalEditStok" tabindex="-1" aria-labelledby="modalEditStokLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-semibold" id="modalEditStokLabel">
                        <i class="mdi mdi-square-edit-outline text-primary me-2"></i>Edit Stok
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <form id="formEditStok" enctype="multipart/form-data">
                        <input type="hidden" id="editId">
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-muted">Informasi Dasar</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="editNamaBarang">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kode SKU</label>
                                    <input type="text" class="form-control" id="editKodeSKU">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Merek</label>
                                    <input type="text" class="form-control" id="editMerek">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Satuan</label>
                                    <select class="form-select" id="editSatuan">
                                        <option>Pilih satuan</option>
                                        <option value="pcs">Pcs</option>
                                        <option value="box">Box</option>
                                        <option value="unit">Unit</option>
                                        <option value="pack">Pack</option>
                                        <option value="kg">Kg</option>
                                        <option value="liter">Liter</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-muted">Foto/Video Barang</h6>
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="border border-2 border-dashed rounded p-4 text-center upload-box"
                                        onclick="document.getElementById('editUploadVideo').click()">
                                        <input type="file" id="editUploadVideo" accept="video/*" style="display:none;"
                                            onchange="handleEditVideoUpload(this)">
                                        <div id="editVideoPlaceholder">
                                            <i class="mdi mdi-video-outline fs-1 text-muted d-block mb-2"></i>
                                            <span class="text-muted">Edit Video</span>
                                        </div>
                                        <div class="preview-video" id="editVideoPreview">
                                            <button type="button" class="remove-media"
                                                onclick="removeEditVideo(event)">×</button>
                                            <video id="editVideoElement" controls style="width: 100%;"></video>
                                            <small class="text-muted d-block mt-2" id="editVideoName"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border border-2 border-dashed rounded p-4 text-center upload-box"
                                        onclick="document.getElementById('editUploadFoto').click()">
                                        <input type="file" id="editUploadFoto" name="fotos[]" accept="image/*" multiple
                                            style="display:none;" onchange="handleEditFotoUpload(this)">

                                        <div id="editFotoPlaceholder">
                                            <i class="mdi mdi-camera-outline fs-1 text-muted d-block mb-2"></i>
                                            <span class="text-muted">Edit Foto (Bisa banyak)</span>
                                        </div>

                                        <div class="row g-2 mt-2" id="editFotoPreviewContainer" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-muted">Harga & Stok</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="editTglMasuk">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Harga Jual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" id="editHargaJual"
                                            oninput="formatRupiahInput(this)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jumlah</label>
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="editDecreaseQty()">
                                            <i class="mdi mdi-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" id="editJumlah" value="0">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="editIncreaseQty()">
                                            <i class="mdi mdi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-semibold mb-3 text-muted">Dimensi & Berat (optional)</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Panjang (cm)</label>
                                    <input type="number" class="form-control" id="editPanjang">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Lebar (cm)</label>
                                    <input type="number" class="form-control" id="editLebar">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tinggi (cm)</label>
                                    <input type="number" class="form-control" id="editTinggi">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Berat (gr)</label>
                                    <input type="number" class="form-control" id="editBerat">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" onclick="submitUpdateStok()">
                        <i class="mdi mdi-content-save-edit-outline me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="detailStokModal" tabindex="-1" aria-labelledby="detailStokLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="detailStokLabel">Detail Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="detailStokContent">

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        function alertSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                showConfirmButton: false,
                timer: 1800
            });
        }

        function alertError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: message,
                showConfirmButton: true
            });
        }
        function alertConfirmDelete(callback) {
            Swal.fire({
                title: 'Hapus Data?',
                text: "Data tidak dapat dikembalikan setelah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }
    </script>
    <script src="{{ asset('assets/js/stok.js') }}"></script>
</body>

</html>
