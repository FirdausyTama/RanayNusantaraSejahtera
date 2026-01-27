<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Kwitansi | RNS - Ranay Nusantara Sejahtera</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Daftar Kwitansi Pembayaran RNS" />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <script src="assets/js/head.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  </head>

  <body data-menu-color="light" data-sidebar="default">
    @include('navbar.navbar')

    
    <div id="app-layout">
      <div class="content-page">
        <div class="content">
          <div class="container-fluid">
            
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
              <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Kwitansi</h4>
              </div>

              <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Halaman</a></li>
                  <li class="breadcrumb-item active">Kwitansi</li>
                </ol>
              </div>
            </div>

            
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                  <div>
                    <h5 class="fw-semibold mb-1">Daftar Kwitansi Pembayaran</h5>
                    <p class="text-muted mb-0">Kelola dan pantau seluruh kwitansi transaksi Anda</p>
                  </div>
                  
                  
                  <div class="d-flex flex-wrap align-items-center gap-2">
                    
                    <button id="btnBulkDelete" class="btn btn-danger me-2" style="display: none;" onclick="bulkDeleteKwitansi()">
                        <i class="mdi mdi-delete me-1"></i>Hapus (<span id="selectedCount">0</span>)
                    </button>

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
                        <input type="text" class="form-control ps-4" placeholder="Cari kwitansi..." style="min-width: 200px;" id="searchInput" onkeyup="searchKwitansi()" />
                        <i class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                      </div>
                    </form>
                  </div>
                </div>

                <div class="table-responsive">
                  <table id="tabelKwitansi" class="table table-bordered align-middle table-hover">
                    <thead class="table-light">
                      <tr>
                        <th class="text-center" style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllKwitansi" onclick="toggleSelectAllKwitansi(this)">
                        </th>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Nomor Kwitansi</th>
                        <th class="text-center">Tanggal</th>
                        <th>Nama Klien</th>
                        <th>Keterangan Pembayaran</th>
                        <th class="text-center">Total Pembayaran</th>
                        <th class="text-center">Dibuat Oleh</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                  <small class="text-muted" id="paginationInfo"></small>
                  <nav>
                    <ul class="pagination pagination-sm mb-0" id="paginationContainer">
                      
                    </ul>
                  </nav>
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
                  document.write(new Date().getFullYear());
                </script>
                - Made with <span class="mdi mdi-heart text-danger"></span> by
                <a href="#!" class="text-reset fw-semibold">TI UMY 22</a>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>

    
    <button
      type="button"
      id="btnTambahKwitansi"
      class="btn btn-primary rounded-circle shadow-lg"
      style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 1000;"
      data-bs-toggle="modal"
      data-bs-target="#modalTambahKwitansi"
      title="Tambah Kwitansi"
    >
      <i class="mdi mdi-plus fs-3 text-white"></i>
    </button>

    
    <div class="modal fade" id="modalTambahKwitansi" tabindex="-1" aria-labelledby="modalTambahKwitansiLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalTambahKwitansiLabel">
              <i class="mdi mdi-receipt me-2"></i>Tambah Kwitansi Baru
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formKwitansi">
              <div class="row">
                
                <div class="col-md-6 mb-3">
                  <label for="nomorKwitansi" class="form-label fw-semibold">
                    Nomor Kwitansi <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    class="form-control bg-light" 
                    id="nomorKwitansi" 
                    name="nomor_kwitansi"
                    placeholder="Otomatis"
                    readonly
                  />
                </div>

                
                <div class="col-md-6 mb-3">
                  <label for="tanggalKwitansi" class="form-label fw-semibold">
                    Tanggal <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="date" 
                    class="form-control" 
                    id="tanggalKwitansi" 
                    name="tanggal"
                    required
                  />
                </div>

                
                <div class="col-12 mb-3">
                  <label for="pembelianId" class="form-label fw-semibold">
                    Pilih Data Pembelian
                  </label>
                  <select class="form-select" id="pembelianId" name="pembelian_id">
                    <option value="">-- Pilih Pembelian --</option>
                  </select>
                </div>

                
                <div class="col-12 mb-3">
                  <label for="namaPenerima" class="form-label fw-semibold">
                    Nama Pelanggan <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="namaPenerima" 
                    name="nama_penerima"
                    placeholder="Contoh: PT Medika Sejahtera"
                    required
                  />
                </div>

                
                <div class="col-12 mb-3">
                  <label for="alamatPenerima" class="form-label fw-semibold">
                    Alamat <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="alamatPenerima" 
                    name="alamat_penerima"
                    placeholder="Jakarta, Indonesia"
                    required
                  />
                </div>

                
                <div class="col-12 mb-3">
                  <label for="totalBilangan" class="form-label fw-semibold">
                    Banyaknya Uang <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    class="form-control bg-light" 
                    id="totalBilangan" 
                    name="total_bilangan"
                    placeholder="Otomatis"
                    readonly
                  />
                </div>

                
                <div class="col-12 mb-3">
                  <label for="keteranganPembayaran" class="form-label fw-semibold">
                    Untuk Pembayaran <span class="text-danger">*</span>
                  </label>
                  <textarea 
                    class="form-control" 
                    id="keteranganPembayaran" 
                    name="keterangan"
                    rows="3"
                    placeholder="Jelaskan detail pembayaran..."
                    required
                  ></textarea>
                </div>

                
                <div class="col-md-6 mb-3">
                  <label for="totalPembayaran" class="form-label fw-semibold">
                    Total Jumlah <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="totalPembayaran" 
                    name="total_pembayaran"
                    placeholder="50000000"
                    required
                  />
                </div>



                
                <div class="col-12 mb-3">
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                      <h6 class="mb-0 fw-semibold"><i class="mdi mdi-pencil-outline me-2"></i>Penandatangan</h6>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                          <label class="form-label">Nama Penandatangan <span class="text-danger">*</span></label>
                          <select class="form-select" name="penandatangan" required>
                            <option value="">Pilih penandatangan...</option>
                            <option value="Dewi Sulistiowati">Dewi Sulistiowati</option>
                            <option value="Heri Pirdaus, S.Tr.Kes Rad (MRI)">Heri Pirdaus, S.Tr.Kes Rad (MRI)</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="mdi mdi-close me-1"></i>Batal
            </button>
            <button type="button" class="btn btn-primary" id="btnSimpanKwitansi">
              <i class="mdi mdi-content-save me-1"></i>Simpan Kwitansi
            </button>
          </div>
        </div>
      </div>
    </div>

    
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    
    <script src="assets/js/app.js"></script>

    
    <script src="{{ asset('assets/js/kwitansi.js') }}?v={{ time() }}"></script>
    <script>
      $(document).ready(function() {
        // Format angka dengan titik pemisah ribuan
        $('#totalPembayaran').on('keyup', function() {
          let value = $(this).val().replace(/\./g, '');
          if (!isNaN(value) && value !== '') {
            $(this).val(parseInt(value).toLocaleString('id-ID'));
          }
        });

        // Set tanggal hari ini sebagai default
        const today = new Date().toISOString().split('T')[0];
        $('#tanggalKwitansi').val(today);
      });
    </script>
  </body>
</html>