<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Invoice | RNS - Ranay Nusantara Sejahtera</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Halaman daftar dan pengelolaan surat invoice RNS." />
  <meta name="author" content="Zoyothemes" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  
  <link rel="shortcut icon" href="assets/images/favicon.ico" />

  
  <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

  
  <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

  <script src="assets/js/head.js"></script>

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
              <h4 class="fs-18 fw-semibold m-0">Surat Invoice</h4>
            </div>
            <div class="text-end">
              <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Halaman</a></li>
                <li class="breadcrumb-item active">Invoice</li>
              </ol>
            </div>
          </div>
        </div>

        
        <div class="row">
          <div class="container-fluid">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                  <div>
                    <h5 class="fw-semibold mb-1">Daftar Surat Invoice</h5>
                    <p class="text-muted mb-0">Kelola dan pantau seluruh surat invoice Anda</p>
                  </div>

                  
                  <div class="d-flex flex-wrap align-items-center gap-2">
                    
                    <button id="btnBulkDelete" class="btn btn-danger me-2" style="display: none;" onclick="bulkDeleteInvoice()">
                        <i class="mdi mdi-delete me-1"></i>Hapus (<span id="selectedCount">0</span>)
                    </button>

                    <div class="dropdown">
                      <button class="btn btn-light border dropdown-toggle" type="button" id="filterWaktu"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-calendar-range me-1"></i>
                        <span id="selectedFilter">Semua Waktu</span>
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="filterWaktu">
                        <li><a class="dropdown-item" href="#" onclick="setFilter('Hari Ini')">Hari Ini</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setFilter('Minggu Ini')">Minggu Ini</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setFilter('Bulan Ini')">Bulan Ini</a></li>
                        <li>
                          <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" onclick="setFilter('Semua Waktu')">Semua Waktu</a></li>
                      </ul>
                    </div>

                    
                    <form class="app-search">
                      <div class="position-relative topbar-search">
                        <input type="text" class="form-control ps-4" placeholder="Cari invoice..."
                          style="min-width: 200px;" id="searchInput" onkeyup="searchInvoice()" />
                        <i
                          class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                      </div>
                    </form>
                  </div>
                </div>

                <div class="table-responsive">
                  <table id="tabelInvoice" class="table table-bordered align-middle table-hover">
                    <thead class="table-light">
                      <tr>
                        <th class="text-center" style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllInvoice" onclick="toggleSelectAllInvoice(this)">
                        </th>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Nomor Invoice</th>
                        <th class="text-center">Tanggal</th>
                        <th>Nama Perusahaan</th>
                        <th class="text-center">Nilai Tagihan</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                  <small class="text-muted" id="paginationInfo">Menampilkan 0 dari 0 invoice</small>
                  <nav>
                    <ul class="pagination pagination-sm mb-0" id="paginationContainer">
                      
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
              &copy;
              <script>document.write(new Date().getFullYear())</script> - Made with
              <span class="mdi mdi-heart text-danger"></span> by
              <a href="#!" class="text-reset fw-semibold">TI UMY 22</a>
            </div>
          </div>
        </div>
      </footer>

      
      <div class="content position-relative">
        <button type="button" id="btnTambahInvoice" class="btn btn-primary rounded-circle shadow-lg"
          style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 1000;"
          data-bs-toggle="modal" data-bs-target="#modalTambahInvoice" title="Tambah Invoice">
          <i class="mdi mdi-plus fs-3 text-white"></i>
        </button>
      </div>
    </div>
  </div>

  
  <div class="modal fade" id="modalTambahInvoice" tabindex="-1" aria-labelledby="modalTambahInvoiceLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalTambahInvoiceLabel">
            <i class="mdi mdi-file-document me-2"></i>Tambah Surat Invoice Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        
        <div class="modal-body">
          <form id="formInvoice">
            
            <div class="mb-4">
              <h6 class="fw-bold text-primary mb-3">
                <i class="mdi mdi-information-outline me-1"></i>Informasi Invoice
              </h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="nomorInvoice" class="form-label fw-semibold">
                    Nomor Invoice <span class="text-danger">*</span>
                  </label>
                  <input type="text" class="form-control" id="nomorInvoice" name="nomorInvoice"
                    placeholder="INV/004/RNS/2025" readonly />
                </div>

                <div class="col-md-4 mb-3">
                  <label for="tanggalInvoice" class="form-label fw-semibold">
                    Tanggal Invoice <span class="text-danger">*</span>
                  </label>
                  <input type="date" class="form-control" id="tanggalInvoice" name="tanggalInvoice" required />
                </div>
              </div>
            </div>

            
            <div class="mb-4">
              <h6 class="fw-bold text-primary mb-3">
                <i class="mdi mdi-domain me-1"></i>Informasi Pelanggan
              </h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="pembelianId" class="form-label fw-semibold">
                    Pilih Data Pembelian <span class="text-danger">*</span>
                  </label>
                  <select class="form-select" id="pembelianId" name="pembelianId" required>
                    <option value="">-- Pilih Pembelian --</option>
                    
                  </select>
                  <small class="text-muted">Pilih data pembelian untuk mengisi invoice</small>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="namaPerusahaan" class="form-label fw-semibold">
                    Nama Perusahaan <span class="text-danger">*</span>
                  </label>
                  <input type="text" class="form-control" id="namaPerusahaan" name="namaPerusahaan"
                    placeholder="Terisi otomatis dari pembelian" readonly required style="background-color: #e9ecef;" />
                </div>
              </div>
            </div>

            
            <div class="mb-4" id="sectionDetailItem">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-primary mb-0">
                  <i class="mdi mdi-clipboard-list me-1"></i>Detail Item Invoice
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahItem" disabled
                  style="display: none;">
                  <i class="mdi mdi-plus-circle me-1"></i>Tambah Item
                </button>
              </div>

              
              <div id="itemPlaceholder" class="text-center py-4 border rounded bg-light">
                <i class="mdi mdi-package-variant-closed fs-1 text-muted"></i>
                <p class="text-muted mb-0 mt-2">Silakan pilih <strong>Data Pembelian</strong> di atas untuk mengisi item
                  invoice</p>
                <small class="text-muted">Item akan otomatis terisi dari data pembelian yang dipilih</small>
              </div>

              
              <div class="table-responsive" id="itemTableContainer" style="display: none;">
                <table class="table table-bordered" id="tabelDetailItem">
                  <thead class="table-light">
                    <tr>
                      <th style="width: 5%;">No</th>
                      <th style="width: 30%;">Nama Item</th>
                      <th style="width: 15%;">Qty</th>
                      <th style="width: 20%;">Harga Satuan</th>
                      <th style="width: 20%;">Subtotal</th>
                      <th style="width: 10%;" class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="itemContainer">
                    
                  </tbody>
                </table>
              </div>
            </div>

            
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-primary mb-0">
                  <i class="mdi mdi-calculator me-1"></i>Ringkasan Pembayaran
                </h6>
                <div class="form-check ms-3">
                  <input type="checkbox" id="gunakanOngkir" class="form-check-input">
                  <label for="gunakanOngkir" class="form-check-label fw-semibold text-primary">Gunakan Ongkir</label>
                </div>
              </div>

              <div class="row">
                
                <div class="col-md-6 mb-3">
                  <label for="subtotalInvoice" class="form-label fw-semibold">Subtotal</label>
                  <input type="text" class="form-control" id="subtotalInvoice" readonly>
                </div>

                
                <div class="col-12 mb-3 p-3 rounded border bg-light" id="blokOngkir"
                  style="opacity: 0.5; pointer-events: none;">
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label for="beratBarang" class="form-label fw-semibold">Berat Total (kg)</label>
                      <input type="number" class="form-control" id="beratBarang" name="beratBarang"
                        placeholder="Masukkan total berat barang" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="hargaPerKg" class="form-label fw-semibold">Harga per Kg (Rp)</label>
                      <input type="text" class="form-control" id="hargaPerKg" placeholder="Contoh: 8000">
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="estimasiOngkir" class="form-label fw-semibold">Estimasi Ongkir</label>
                      <input type="text" class="form-control" id="estimasiOngkir" readonly>
                    </div>
                  </div>
                </div>

                
                <div class="col-md-6 mb-3">
                  <label for="totalInvoice" class="form-label fw-semibold">Total Tagihan</label>
                  <input type="text" class="form-control fw-bold text-primary" id="totalInvoice" readonly>
                </div>

                
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
          <button type="button" class="btn btn-primary" id="btnSimpanInvoice">
            <i class="mdi mdi-content-save me-1"></i>Simpan Invoice
          </button>
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

  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  
  <script src="assets/js/app.js"></script>

  
  <script src="assets/js/invoice.js"></script>

  
  <script>
    // Helper functions for form interactivity (Calculations, etc.)
    // These are UI specific, separate from API logic in invoice.js

    $(document).ready(function () {
      let itemCounter = 1;

      function formatRupiah(angka) {
        if (!angka) return '0';
        return parseInt(angka.toString().replace(/\D/g, '')).toLocaleString('id-ID');
      }

      function parseRupiah(rupiah) {
        return parseInt(rupiah.replace(/\D/g, '')) || 0;
      }

      const today = new Date().toISOString().split('T')[0];
      $('#tanggalInvoice').val(today);

      function hitungSubtotal(row) {
        const qty = parseInt($(row).find('.qty-input').val()) || 0;
        const harga = parseRupiah($(row).find('.harga-input').val());
        const subtotal = qty * harga;
        $(row).find('.subtotal-input').val(formatRupiah(subtotal));
        hitungTotal();
      }

      function hitungTotal() {
        let total = 0;
        $('.subtotal-input').each(function () {
          total += parseRupiah($(this).val());
        });

        $('#subtotalInvoice').val('Rp ' + formatRupiah(total));

        let ongkir = 0;
        if ($('#gunakanOngkir').is(':checked')) {
          const berat = parseFloat($('#beratBarang').val()) || 0;
          const hargaPerKg = parseRupiah($('#hargaPerKg').val());
          ongkir = berat * hargaPerKg;
          $('#estimasiOngkir').val('Rp ' + formatRupiah(ongkir));
        } else {
          $('#estimasiOngkir').val('Rp 0');
        }

        const grandTotal = total + ongkir;
        $('#totalInvoice').val('Rp ' + formatRupiah(grandTotal));
      }

      $('#gunakanOngkir').on('change', function () {
        const aktif = $(this).is(':checked');
        const blok = $('#blokOngkir');

        if (aktif) {
          blok.css({ opacity: '1', 'pointer-events': 'auto', 'background-color': '#ffffff' });
        } else {
          blok.css({ opacity: '0.5', 'pointer-events': 'none', 'background-color': '#f8f9fa' });
          $('#beratBarang, #hargaPerKg, #estimasiOngkir').val('');
        }
        hitungTotal();
      });

      $('#beratBarang, #hargaPerKg').on('input change keyup', function () {
        hitungTotal();
      });

      $(document).on('keyup', '.harga-input', function () {
        const value = $(this).val().replace(/\D/g, '');
        $(this).val(formatRupiah(value));
        hitungSubtotal($(this).closest('tr'));
      });

      $(document).on('change', '.qty-input', function () {
        hitungSubtotal($(this).closest('tr'));
      });

      $('#btnTambahItem').on('click', function () {
        itemCounter++;
        const newRow = `
            <tr class="item-row">
              <td class="text-center">${itemCounter}</td>
              <td><input type="text" class="form-control form-control-sm" name="namaItem[]" placeholder="Nama barang/jasa" required></td>
              <td><input type="number" class="form-control form-control-sm qty-input" name="qty[]" placeholder="0" min="1" value="1" required></td>
              <td><input type="text" class="form-control form-control-sm harga-input" name="hargaSatuan[]" placeholder="0" required></td>
              <td><input type="text" class="form-control form-control-sm subtotal-input" name="subtotal[]" placeholder="0" readonly></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-hapus-item">
                  <i class="mdi mdi-delete"></i>
                </button>
              </td>
            </tr>`;
        $('#itemContainer').append(newRow);
        updateDeleteButtons();
      });

      $(document).on('click', '.btn-hapus-item', function () {
        $(this).closest('tr').remove();
        updateItemNumbers();
        hitungTotal();
        updateDeleteButtons();
      });

      function updateItemNumbers() {
        $('#itemContainer tr').each(function (index) {
          $(this).find('td:first').text(index + 1);
        });
        itemCounter = $('#itemContainer tr').length;
      }

      function updateDeleteButtons() {
        const rowCount = $('#itemContainer tr').length;
        $('.btn-hapus-item').prop('disabled', rowCount <= 1);
      }

      $('#modalTambahInvoice').on('hidden.bs.modal', function () {
        $('#formInvoice')[0].reset();
        // Reset items section to initial state
        $('#itemContainer').html('');
        $('#itemPlaceholder').show();
        $('#itemTableContainer').hide();
        // Remove readonly from nama perusahaan
        $('#namaPerusahaan').removeAttr('readonly');
        itemCounter = 1;
        $('#subtotalInvoice, #estimasiOngkir, #totalInvoice').val('');
        $('#blokOngkir').css({ opacity: '0.5', 'pointer-events': 'none' });
      });
    });
  </script>
</body>

</html>