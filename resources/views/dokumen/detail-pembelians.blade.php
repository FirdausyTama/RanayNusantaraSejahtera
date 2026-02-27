<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detai Penjualan | RNS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .content-page {
            padding: 0 20px 20px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 19px;
            top: 45px;
            bottom: -15px;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
        }
    </style>
</head>
<body data-menu-color="light" data-sidebar="default">
    
    
    <div id="app-layout">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    
                    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-3">
                                
                                <a href="#" onclick="history.back()" class="btn btn-light border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Kembali">
                                    <i class="mdi mdi-arrow-left fs-5"></i>
                                </a>
                                <div>
                                    <h4 class="fs-18 fw-semibold m-0">Detail Penjualan</h4>
                                    <small class="text-muted">Informasi lengkap transaksi penjualan</small>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <ol class="breadcrumb m-0 py-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">Kelola Penjualan</a></li>
                                <li class="breadcrumb-item active">Detail Penjualan</li>
                            </ol>
                        </div>
                    </div>

                    <div class="row">
                        
                        <div class="col-lg-5">
                            
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <span class="badge bg-primary bg-opacity-10 text-primary mb-2">TRX-2025-001</span>
                                            <h5 class="fw-bold mb-1">Transaksi Penjualan</h5>
                                            <small class="text-muted">17 Februari 2025</small>
                                        </div>
                                        <span class="badge bg-warning text-white px-3 py-2">Cicilan</span>
                                    </div>
                                    <hr>
                                    <div class="mb-0">
                                        <h6 class="fw-semibold mb-3">Informasi Pesanan</h6>
                                        <table class="table table-sm mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted border-0 ps-0" style="width: 45%;">No. Order</td>
                                                    <td class="fw-semibold border-0">ORJ-2025-001</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted border-0 ps-0">Nomor Transaksi</td>
                                                    <td class="fw-semibold border-0">TRX-2025-001</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted border-0 ps-0">Tanggal Penjualan</td>
                                                    <td class="fw-semibold border-0">17 Februari 2025</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted border-0 ps-0">Status Pembayaran</td>
                                                    <td class="border-0">
                                                        <span class="badge bg-warning bg-opacity-10 text-warning">Cicilan</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-3">Data Customer</h6>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <i class="mdi mdi-account text-primary fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">Dr. Ahmad Hidayat</h6>
                                            <small class="text-muted">Customer</small>
                                        </div>
                                    </div>
                                    <div class="border-top pt-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="mdi mdi-phone text-muted me-2"></i>
                                            <small class="text-muted">No. Telepon</small>
                                        </div>
                                        <p class="fw-semibold mb-0">0812-3456-7890</p>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-3">Ringkasan Pembayaran</h6>
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <div class="border rounded p-3 text-center">
                                                <i class="mdi mdi-cash-check text-success fs-3 mb-2"></i>
                                                <h6 class="mb-0 text-success">Rp 250.000.000</h6>
                                                <small class="text-muted">Terbayar</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-3 text-center">
                                                <i class="mdi mdi-cash-clock text-warning fs-3 mb-2"></i>
                                                <h6 class="mb-0 text-warning">Rp 135.000.000</h6>
                                                <small class="text-muted">Sisa</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">Total Transaksi</small>
                                            <h5 class="fw-bold text-primary mb-0">Rp 385.000.000</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-lg-7">
                            
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-semibold mb-0">Detail Barang</h6>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">3 Item</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Nama Barang</th>
                                                    <th class="text-center" style="width: 100px;">Jumlah</th>
                                                    <th class="text-end" style="width: 140px;">Harga</th>
                                                    <th class="text-end" style="width: 140px;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td>
                                                        <div class="fw-semibold">Mesin Kursi Gigi</div>
                                                        <small class="text-muted">SKU003</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark">1 Unit</span>
                                                    </td>
                                                    <td class="text-end">Rp 300.000.000</td>
                                                    <td class="text-end fw-bold">Rp 300.000.000</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td>
                                                        <div class="fw-semibold">Batre Alat</div>
                                                        <small class="text-muted">SKU001</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark">5 Pcs</span>
                                                    </td>
                                                    <td class="text-end">Rp 1.000.000</td>
                                                    <td class="text-end fw-bold">Rp 5.000.000</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">3</td>
                                                    <td>
                                                        <div class="fw-semibold">Mesin Ronsen</div>
                                                        <small class="text-muted">SKU002</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark">2 Unit</span>
                                                    </td>
                                                    <td class="text-end">Rp 40.000.000</td>
                                                    <td class="text-end fw-bold">Rp 80.000.000</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bold">Total Keseluruhan</td>
                                                    <td class="text-end fw-bold text-primary fs-5">Rp 385.000.000</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-3">Riwayat Pembayaran</h6>
                                    <div class="timeline">
                                        <div class="timeline-item mb-3">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="mdi mdi-check text-success"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1 fw-semibold">Pembayaran Cicilan 1</h6>
                                                    <p class="text-muted mb-1 small">Pembayaran awal transaksi</p>
                                                    <small class="text-muted">17 Feb 2025, 10:30 WIB</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success bg-opacity-10 text-success">Rp 100.000.000</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="timeline-item mb-3">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="mdi mdi-check text-success"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1 fw-semibold">Pembayaran Cicilan 2</h6>
                                                    <p class="text-muted mb-1 small">Cicilan tahap kedua</p>
                                                    <small class="text-muted">17 Mar 2025, 14:20 WIB</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success bg-opacity-10 text-success">Rp 150.000.000</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="mdi mdi-clock-outline text-warning"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1 fw-semibold">Cicilan Terakhir</h6>
                                                    <p class="text-muted mb-1 small">Menunggu pembayaran</p>
                                                    <small class="text-muted">Jatuh tempo: 17 Apr 2025</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">Rp 135.000.000</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="#" onclick="history.back()" class="btn btn-outline-secondary">
                                            <i class="mdi mdi-arrow-left me-1"></i>Kembali
                                        </a>
                                        <button class="btn btn-primary flex-grow-1" onclick="alert('Edit Transaksi')">
                                            <i class="mdi mdi-square-edit-outline me-1"></i>Edit Transaksi
                                        </button>

                                        <button class="btn btn-outline-secondary" onclick="alert('Share')">
                                            <i class="mdi mdi-share-variant-outline"></i>
                                        </button>
                                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
