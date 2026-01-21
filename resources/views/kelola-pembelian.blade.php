<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Penjualan | RNS - Ranay Nusantara Sejathera</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc."/>
        <meta name="author" content="Zoyothemes"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        
        <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

        
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

        <script src="assets/js/head.js"></script>

        <style>
            /* Floating Button Style */
            .floating-btn {
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 60px;
                height: 60px;
                background: #0d6efd;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
                cursor: pointer;
                transition: all 0.3s ease;
                z-index: 1000;
                border: none;
            }

            .floating-btn:hover {
                background: #0b5ed7;
                transform: scale(1.1);
                box-shadow: 0 6px 16px rgba(13, 110, 253, 0.5);
            }

            .floating-btn i {
                color: white;
                font-size: 28px;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .floating-btn {
                    width: 50px;
                    height: 50px;
                    bottom: 20px;
                    right: 20px;
                }

                .floating-btn i {
                    font-size: 24px;
                }
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
                                <h4 class="fs-18 fw-semibold m-0">Kelola Penjualan</h4>
                            </div>
            
                            <div class="text-end">
                                <ol class="breadcrumb m-0 py-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Halaman</a></li>
                                    <li class="breadcrumb-item active">Kelola Penjualan</li>
                                </ol>
                            </div>
                        </div>

                        <div class="container-fluid">
                            <div class="row mb-3">
                                <!-- 1. Total Penjualan (Belum Lunas) -->
                                <div class="col-md-6 col-lg-4 col-xl">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="p-2 border border-danger border-opacity-10 rounded-2 me-2" style="background-color: #ffeaea;">
                                                    <div class="bg-danger rounded-circle widget-size text-center d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                        <i class="mdi mdi-cash-minus text-white fs-5"></i>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15 fw-semibold">Penjualan (Belum Lunas)</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalPenjualanBelumLunas">Rp 0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Total Penjualan (Cicilan) -->
                                <div class="col-md-6 col-lg-4 col-xl">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="p-2 border border-warning border-opacity-10 rounded-2 me-2" style="background-color: #fff8e1;">
                                                    <div class="bg-warning rounded-circle widget-size text-center d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                        <i class="mdi mdi-cash-clock text-white fs-5"></i>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15 fw-semibold">Penjualan (Cicilan)</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalPenjualanCicilan">Rp 0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Total Penjualan (Lunas) -->
                                <div class="col-md-6 col-lg-4 col-xl">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="p-2 border border-success border-opacity-10 rounded-2 me-2" style="background-color: #d1e7dd;">
                                                    <div class="bg-success rounded-circle widget-size text-center d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                        <i class="mdi mdi-cash-check text-white fs-5"></i>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15 fw-semibold">Penjualan (Lunas)</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalPenjualanLunas">Rp 0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. Total Penjualan (Tahun Ini) -->
                                <div class="col-md-6 col-lg-4 col-xl">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="p-2 border border-purple border-opacity-10 rounded-2 me-2" style="background-color: #f3e6ff;">
                                                    <div class="bg-purple rounded-circle widget-size text-center d-flex align-items-center justify-content-center" style="width:40px;height:40px; background-color: #6f42c1 !important;">
                                                        <i class="mdi mdi-cash-multiple text-white fs-5"></i>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15 fw-semibold">Total Penjualan (Thn)</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalPenjualanYear">Rp 0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. Total Penjualan (Bulan Ini) -->
                                <div class="col-md-6 col-lg-4 col-xl">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="p-2 border border-purple border-opacity-10 rounded-2 me-2" style="background-color: #f3e6ff;">
                                                    <div class="bg-purple rounded-circle widget-size text-center d-flex align-items-center justify-content-center" style="width:40px;height:40px; background-color: #6f42c1 !important;">
                                                        <i class="mdi mdi-cash-plus text-white fs-5"></i>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15 fw-semibold">Total Penjualan (Bln)</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="mb-0 fs-22 text-dark me-3 mt-2" id="totalPenjualanMonth">Rp 0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                                        <div>
                                            <h5 class="fw-semibold mb-1">Kelola Penjualan</h5>
                                            <p class="text-muted mb-0">Lihat dan kelola riwayat penjualan produk dari customer</p>
                                        </div>
                                        
                                        
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            
                                            <!-- Bulk Delete Button (Hidden by default) -->
                                            <button type="button" id="btnBulkDelete" class="btn btn-danger text-white border-0 shadow-sm" style="display: none;" onclick="bulkDeletePembelian()">
                                                <i class="mdi mdi-delete-sweep me-1"></i> Hapus (<span id="selectedCount">0</span>)
                                            </button>

                                            <div class="dropdown">
                                                <button class="btn btn-light border dropdown-toggle" type="button" id="filterStatus" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-filter-variant me-1"></i>
                                                    <span id="selectedStatusFilter">Semua Status</span>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="filterStatus">
                                                    <li><a class="dropdown-item" href="#" onclick="setStatusFilter('Semua Status')">Semua Status</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="#" onclick="setStatusFilter('Belum Lunas')">Belum Lunas</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="setStatusFilter('Cicilan')">Cicilan</a></li>
                                                </ul>
                                            </div>

                                            <div class="dropdown">
                                                <button class="btn btn-light border dropdown-toggle" type="button" id="filterWaktu" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-calendar-range me-1"></i>
                                                    <span id="selectedFilter">Semua Waktu</span>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="filterWaktu">
                                                    <li><a class="dropdown-item" href="#" onclick="setFilter('Hari Ini')">Hari Ini</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="setFilter('Minggu Ini')">Minggu Ini</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="setFilter('Bulan Ini')">Bulan Ini</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="#" onclick="setFilter('Semua Waktu')">Semua Waktu</a></li>
                                                </ul>
                                            </div>
                                            
                                            
                                            <form class="app-search">
                                                <div class="position-relative topbar-search">
                                                    <input type="text" class="form-control ps-4" placeholder="Cari transaksi..." style="min-width: 200px;" id="searchInput" onkeyup="searchTransaction()" />
                                                    <i class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table align-middle table-hover" id="transactionTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center" style="width: 40px;">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input" type="checkbox" id="selectAllPembelian" onchange="toggleSelectAllPembelian(this)">
                                                        </div>
                                                    </th>
                                                    <th scope="col" class="text-center" style="width: 60px;">No</th>
                                                    <th scope="col">Nomor Transaksi</th>
                                                    <th scope="col" class="text-center">Tanggal</th>
                                                    <th scope="col">Customer</th>
                                                    <th scope="col" class="text-center">Total Penjualan</th>
                                                    <th scope="col" class="text-center">Status Pembayaran</th>
                                                    <th scope="col" class="text-center" >Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stok-table-body">
                                        <tr>
                                            <td colspan="6" class="text-center py-3 text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <small class="text-muted" id="paginationInfo">Menampilkan 0–0 dari 0 transaksi</small>
                                        <nav>
                                            <ul class="pagination pagination-sm mb-0" id="paginationList">
                                                
                                            </ul>
                                        </nav>
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
                                &copy; <script>document.write(new Date().getFullYear())</script> - Made with <span class="mdi mdi-heart text-danger"></span> by <a href="#!" class="text-reset fw-semibold">TI UMY 22</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        
        <button class="floating-btn" data-bs-toggle="modal" data-bs-target="#inputPembelianModal" title="Tambah Pembelian">
            <i class="mdi mdi-plus"></i>
        </button>

        @include('tambah-pembelian')

        
        <div class="modal fade" id="detailPembelianModal" tabindex="-1" aria-labelledby="detailPembelianLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold" id="detailPembelianLabel">Detail Penjualan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h6 class="text-muted mb-1">Nomor Transaksi</h6>
                                <h4 class="fw-bold text-primary mb-0" id="detailNoOrder">Loading...</h4>
                            </div>
                            <div class="text-end">
                                <h6 class="text-muted mb-1">Tanggal</h6>
                                <p class="fw-semibold mb-0" id="detailTanggal">-</p>
                            </div>
                        </div>

                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="mdi mdi-account-circle text-primary fs-4 me-2"></i>
                                        <h6 class="fw-semibold mb-0">Informasi Customer</h6>
                                    </div>
                                    <hr class="my-2">
                                    <p class="mb-1"><span class="text-muted">Nama:</span> <span class="fw-medium" id="detailNama">-</span></p>
                                    <p class="mb-1"><span class="text-muted">Telepon:</span> <span class="fw-medium" id="detailTelepon">-</span></p>
                                    <p class="mb-0"><span class="text-muted">Alamat:</span> <span class="fw-medium" id="detailAlamat">-</span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="mdi mdi-credit-card-outline text-success fs-4 me-2"></i>
                                        <h6 class="fw-semibold mb-0">Status & Pengiriman</h6>
                                    </div>
                                    <hr class="my-2">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Pembayaran:</small>
                                        <div id="detailStatusBadge">
                                            <span class="badge bg-secondary">Loading...</span>
                                        </div>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Pengiriman:</small>
                                        <span class="fw-medium" id="detailStatusPengiriman">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div id="detailCicilanContainer" class="row g-3 mb-4 d-none">
                            <div class="col-12">
                                <div class="p-3 bg-warning-subtle rounded-3 border border-warning">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="mdi mdi-cash-clock text-warning fs-4 me-2"></i>
                                        <h6 class="fw-semibold mb-0 text-warning-emphasis">Informasi Cicilan</h6>
                                    </div>
                                    <hr class="my-2 border-warning">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <p class="mb-0 text-muted small">Cicilan per Bulan</p>
                                            <h5 class="fw-bold text-primary mb-0" id="detailCicilanPerBulan">Rp 0</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0 text-muted small">Sisa Tagihan</p>
                                            <h5 class="fw-bold text-danger mb-0" id="detailSisaCicilan">Rp 0</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0 text-muted small">Total Terbayar</p>
                                            <h5 class="fw-bold text-success mb-0" id="detailTotalTerbayar">Rp 0</h5>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="mt-3 bg-white rounded-3 p-3 border">
                                        <h6 class="fw-semibold text-muted mb-3">Jadwal Pembayaran Cicilan</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless table-striped mb-0">
                                                <thead class="text-muted small">
                                                    <tr>
                                                        <th>Ke</th>
                                                        <th>Jatuh Tempo</th>
                                                        <th>Jumlah</th>
                                                        <th>Tgl Bayar</th>
                                                        <th>Status</th>
                                                        <th class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="listCicilanBody">
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        
                        <h6 class="fw-semibold mb-3">Daftar Barang</h6>
                        <div class="table-responsive border rounded-3 mb-4">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">Nama Barang</th>
                                        <th class="text-center" style="width: 100px;">Jumlah</th>
                                        <th class="text-end" style="width: 150px;">Harga Satuan</th>
                                        <th class="text-end pe-3" style="width: 150px;">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detailItemsBody">
                                    
                                </tbody>
                                <tfoot class="bg-light border-top">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3">Grand Total</td>
                                        <td class="text-end fw-bold text-primary fs-5 pe-3 py-3" id="detailGrandTotal">Rp 0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
        <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>
        <script src="assets/js/app.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        
        <script src="{{ asset('assets/js/pembelian.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                loadPembelian(null, 'lunas');
            });
        </script>
    </body>
</html>