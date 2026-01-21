<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <title>Detail Kwitansi | RNS - Ranay Nusantara Sejahtera</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Halaman detail kwitansi RNS." />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- App css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('assets/js/head.js') }}"></script>

    <style>


      /* Header: Logo lengkap */
      .kw-header {
        text-align: center;
        margin-bottom: 20px;
      }
      .kw-header img {
        width: 100%;
        max-height: 160px;
        object-fit: contain;
      }

      /* Area utama kwitansi */
      .kw-main {
        margin-top: 10px;
      }

      /* Dua kolom atas */
      .kw-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 16px;
      }

      .kw-left {
        flex: 1 1 60%;
        background: #dbeaff !important;
        border: 1px solid #000;
        padding: 10px;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .kw-left b {
        display: block;
        background: #bcd9ff;
        text-align: center;
        font-weight: bold;
        border-bottom: 1px solid #000;
        padding: 4px 0;
        margin-bottom: 6px;
      }

      .kw-left .address {
        font-size: 13px;
        line-height: 1.4;
        color: #000;
      }

      .kw-right {
        flex: 0 0 32%;
        border: 1px solid #000;
        background: #fff;
        padding: 6px 10px;
      }

      .kw-right table {
        width: 100%;
        font-size: 13px;
      }

      .kw-right td {
        padding: 3px 4px;
      }

      /* Judul receipt */
      .kw-subtitle {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 2px;
      }

      .kw-title {
        text-align: center;
        letter-spacing: 8px;
        font-size: 13px;
        margin-bottom: 14px;
      }

      /* Isi form */
      .kw-form {
        width: 100%;
        font-size: 14px;
        margin-top: 10px;
      }

      .kw-form td {
        padding: 6px 4px;
        vertical-align: top;
      }

      .kw-field {
        background: #eef7ff;
        border: 1px solid #cfe8ff;
        padding: 8px;
        min-height: 34px;
      }

      /* Total box */
      .kw-total {
        border: 1px solid #000;
        background: #dbeaff;
        padding: 8px 20px;
        font-weight: bold;
        display: inline-block;
        margin-top: 6px;
      }

      /* Signature */
      .kw-sign {
        margin-top: 40px;
        text-align: right;
        font-size: 13px;
        line-height: 1.5;
      }

      .kw-sign img {
        height: 80px;
        display: inline-block;
        margin: 10px 0 5px 0;
      }

      @media print {
        @page {
          size: A4 portrait;
          margin: 15mm;
        }

        body, html {
          width: 100%;
          height: 100%;
          margin: 0;
          padding: 0;
          background: white;
        }

        .no-print,
        .btn,
        [data-bs-toggle="tooltip"],
        .content.position-relative,
        .footer,
        .navbar-custom,
        .left-side-menu,
        .breadcrumb,
        .topbar {
          display: none !important;
          visibility: hidden !important;
        }

        #app-layout,
        .content-page,
        .content,
        .container-fluid,
        .card,
        .card-body {
          width: 100% !important;
          max-width: 100% !important;
          margin: 0 !important;
          padding: 0 !important;
          box-shadow: none !important;
          border: none !important;
        }

        .kw-main {
          margin: 0 !important;
          width: auto !important;
        }

        /* Ensure background colors print */
        * {
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
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
                <h4 class="fs-18 fw-semibold m-0">Detail Kwitansi</h4>
              </div>
              <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                  <li class="breadcrumb-item"><a href="/kwitansi">Halaman</a></li>
                  <li class="breadcrumb-item active">Kwitansi Detail</li>
                </ol>
              </div>
            </div>

            <!-- Button Kembali -->
            <div class="mb-3 no-print">
                <a href="{{ url('/kwitansi') }}" class="btn btn-light">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <!-- Card utama -->
            <div class="card shadow-sm border-0">
              <div class="card-body">

                <!-- KWITANSI CONTENT -->
                <div class="kw-main">

                  <!-- Kop Surat -->
                  <div class="kw-header">
                    <img src="{{ asset('assets/images/kopsurat.png') }}" alt="Kop Surat RNS" />
                  </div>

                  <!-- Kotak kiri-kanan -->
                  <div class="kw-row">
                    <!-- Kiri: Kwitansi To -->
                    <div class="kw-left">
                      <b>KWITANSI TO</b>
                      <div class="address">
                        <strong id="printNamaPenerima">Loading...</strong><br />
                        <span id="printAlamatPenerima">Loading...</span>
                      </div>
                    </div>

                    <!-- Kanan: Tanggal & No -->
                    <div class="kw-right">
                      <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                          <td style="background: #bcd9ff; border: 1px solid #000; padding: 2px 5px; font-weight: bold; text-align: center; width: 40%;">Tanggal</td>
                          <td style="border: 1px solid #000; padding: 2px 5px; text-align: center;" id="printTanggal">Loading...</td>
                        </tr>
                        <tr>
                          <td style="background: #bcd9ff; border: 1px solid #000; padding: 2px 5px; font-weight: bold; text-align: center;">No Kwitansi</td>
                          <td style="border: 1px solid #000; padding: 2px 5px; text-align: center;" id="printNomor">Loading...</td>
                        </tr>
                      </table>
                    </div>
                  </div>

                  <!-- Judul -->
                  <div class="kw-subtitle">R E C E I P T</div>
                  <div class="kw-title" style="font-weight: bold; font-size: 18px; margin-bottom: 20px;">KWITANSI</div>

                  <!-- Form -->
                  <table class="kw-form">
                    <tr>
                      <td style="width:22%; font-weight: bold;">Received From<br><span style="font-weight: normal; font-style: italic;">Sudah Terima Dari</span></td>
                      <td style="width:2%; text-align: center;">:</td>
                      <td><div class="kw-field" style="background: #dbeaff; border: 1px solid #8cbbf1;"><strong id="printTerimaDari">Loading...</strong></div></td>
                    </tr>

                    <tr>
                      <td style="font-weight: bold;">Amount in Words<br><span style="font-weight: normal; font-style: italic;">Banyaknya Uang</span></td>
                      <td style="text-align: center;">:</td>
                      <td><div class="kw-field" style="background: #dbeaff; border: 1px solid #8cbbf1;"><em id="printTerbilang">Loading...</em></div></td>
                    </tr>

                    <tr>
                      <td style="font-weight: bold;">For Payment of<br><span style="font-weight: normal; font-style: italic;">Untuk Pembayaran</span></td>
                      <td style="text-align: center;">:</td>
                      <td><div class="kw-field" style="background: #dbeaff; border: 1px solid #8cbbf1;" id="printKeterangan">Loading...</div></td>
                    </tr>

                    <tr>
                      <td style="vertical-align:middle; font-weight: bold;">Total<br><span style="font-weight: normal; font-style: italic;">Jumlah</span></td>
                      <td style="vertical-align:middle; text-align: center;"></td>
                      <td><div class="kw-total" style="background: #dbeaff; border: 1px solid #8cbbf1; min-width: 200px;" id="printTotal">Loading...</div></td>
                    </tr>
                  </table>

                  <!-- Blok tanda tangan -->
                  <div class="kw-sign">
                    <div class="kw-sign-right" style="display: inline-block; text-align: center; min-width: 200px;">
                      <p style="margin-bottom: 5px;">Hormat Kami</p>
                      <p style="margin-bottom: 10px;">PT.Ranay Nusantara Sejahtera</p>
                      <!-- Dynamic Signature Image -->
                      <img id="printSignature" src="" alt="Tanda Tangan" class="kw-sign-img" style="height: 80px; display: block; margin: 0 auto;" />
                      <p class="sign-name" style="margin-top: 5px; font-weight: bold; text-decoration: underline;" id="printSignerName">Loading...</p>
                    </div>
                  </div>
                <!-- /kw-main -->
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
            title="Print Kwitansi"
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
    <script src="{{ asset('assets/js/print-kwitansi.js') }}"></script>
  </body>
</html>