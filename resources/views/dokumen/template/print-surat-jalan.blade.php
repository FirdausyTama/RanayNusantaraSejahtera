<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <title>Cetak Surat Jalan | RNS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- App css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('assets/js/head.js') }}"></script>

    <style>
      body {
      }

      @media print {
        @page {
          size: A4 portrait;
          margin: 15mm;
        }

        .bg-light {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .no-print,
        .btn,
        .footer,
        .breadcrumb,
        [data-bs-toggle="tooltip"],
        .content.position-relative,
        .navbar-custom,
        .left-side-menu {
          display: none !important;
          visibility: hidden !important;
        }

        body, html {
          margin: 0 !important;
          padding: 0 !important;
          height: 100% !important;
          width: 100% !important;
          background: white !important;
          -webkit-print-color-adjust: exact;
        }
        
        /* Reset Layout Containers */
        #app-layout, .content-page, .content, .container-fluid, .card, .card-body {
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
        }

        .card {
          border: none !important;
          box-shadow: none !important;
        }
        
        /* Override Bootstrap utility */
        .p-5 {
            padding: 0 !important;
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
            <!-- Header halaman -->
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
              <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Detail Surat Jalan</h4>
              </div>
              <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                  <li class="breadcrumb-item"><a href="/surat-jalan">Halaman</a></li>
                  <li class="breadcrumb-item active">Surat Jalan Detail</li>
                </ol>
              </div>
            </div>

            <!-- Button Kembali -->
            <div class="mb-3 no-print">
                <a href="{{ url('/surat-jalan') }}" class="btn btn-light">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <!-- Card utama -->
            <div class="card shadow-sm border-0">
              <div class="card-body p-5">

                  <!-- Kop Surat -->
                  <div class="text-center border-bottom border-3 border-dark pb-3 mb-4">
                    <img src="{{ asset('assets/images/kopsurat.png') }}" alt="Logo RNS" style="width: 100%; max-height: 150px; object-fit: contain;">
                  </div>

                  <!-- Info Penerima & Keterangan -->
                  <div class="mb-4" style="font-size: 14px; color: #000;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="width: 150px; font-weight: bold;">KEPADA YTH</td>
                            <td style="width: 10px;">:</td>
                            <td id="printNamaPenerima">Loading...</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">ALAMAT</td>
                            <td>:</td>
                            <td id="printAlamatPenerima">Loading...</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">TELP.COSTUMER</td>
                            <td>:</td>
                            <td id="printTelpPenerima">Loading...</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">KETERANGAN</td>
                            <td>:</td>
                            <td id="printKeterangan">Loading...</td>
                        </tr>
                    </table>
                  </div>

                  <!-- Judul & Tanggal -->
                  <table style="width: 100%; margin-bottom: 10px; border: none;">
                    <tr>
                        <td style="width: 30%;"></td>
                        <td style="width: 40%; text-align: center;">
                            <h4 class="fw-bold text-uppercase m-0" style="letter-spacing: 2px; color: #000; font-size: 20px;">SURAT JALAN</h4>
                        </td>
                        <td style="width: 30%; text-align: right; font-size: 14px; color: #000; vertical-align: bottom;">
                            Tanggal : <span id="printTanggal">Loading...</span>
                        </td>
                    </tr>
                  </table>

                  <!-- Tabel Barang -->
                  <div class="table-responsive">
                    <table class="table table-bordered border-dark mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 5%;">NO</th>
                                <th>NAMA BARANG / JASA</th>
                                <th class="text-center" style="width: 15%;">QTY</th>
                                <th class="text-center" style="width: 15%;">JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1.</td>
                                <td id="printNamaBarang">Loading...</td>
                                <td class="text-center" id="printQty">Loading...</td>
                                <td class="text-center" id="printJumlah">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                  </div>

                  <!-- Footer / Tanda Tangan -->
                  <table style="width: 100%; margin-top: 50px; border: none;">
                    <tr>
                        <td style="width: 40%; text-align: center; vertical-align: top;">
                            <p class="mb-0 fw-medium">COSTUMER / PIHAK RS</p>
                            <div style="height: 80px;"></div>
                            <p class="fw-bold m-0" id="printNamaPenerimaSign">RS KENCANA SERANG</p>
                        </td>
                        <td style="width: 20%;"></td>
                        <td style="width: 40%; text-align: center; vertical-align: top;">
                            <p class="mb-0 fw-medium">PENGIRIM</p>
                            <div style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                <img id="printSignature" src="" alt="Tanda Tangan" style="max-height: 80px; max-width: 100%; object-fit: contain;">
                            </div>
                            <p class="fw-bold m-0 text-uppercase" id="printSignerName">Loading...</p>
                        </td>
                    </tr>
                  </table>
                <!-- /sj-main -->

              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col fs-13 text-muted text-center">
                &copy;
                <script>document.write(new Date().getFullYear())</script>
                - Made with <span class="mdi mdi-heart text-danger"></span> by
                <a href="#!" class="text-reset fw-semibold">TI UMY 22</a>
              </div>
            </div>
          </div>
        </footer>

        <!-- Tombol Print Mengambang -->
        <div class="content position-relative">
          <button
            type="button"
            class="btn btn-primary rounded-circle shadow-lg"
            style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px;"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="Print Surat Jalan"
            onclick="window.print()"
          >
            <i class="mdi mdi-printer fs-3 text-white"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Vendor -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!-- External JS -->
    <script src="{{ asset('assets/js/print-surat-jalan.js') }}"></script>
  </body>
</html>
