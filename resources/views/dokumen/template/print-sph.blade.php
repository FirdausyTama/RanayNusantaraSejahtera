<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <title>Surat Penawaran Harga | RNS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

  <!-- App css -->
  <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
  <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
  <script src="{{ asset('assets/js/head.js') }}"></script>

  <style>
    /* DASHBOARD UI STYLE */
    body {
      background-color: #f7f8fa;
    }

    /* PAPER STYLE (SCREEN) */
    .paper-container {
      width: 210mm;
      min-height: 297mm;
      background: #fff;
      margin: 30px auto;
      padding: 15mm;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    /* DOCUMENT CONTENT STYLE (Inside Paper) */
    .doc-body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
      /* Reduced to 12px */
      line-height: 1.3;
      /* Tightened line height */
      color: #000;
    }

    .kop-surat img {
      width: 100%;
      height: auto;
      max-height: 90px;
      /* Reduced to 90px */
      object-fit: contain;
    }

    /* TABLE STYLE */
    .table-custom {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
      /* Reduced to 12px */
      margin-top: 5px;
      /* Reduced margin */
    }

    .table-custom th,
    .table-custom td {
      border: 1px solid #777;
      padding: 4px 6px;
      /* Compact padding */
    }

    .table-custom th {
      text-align: center;
      font-weight: bold;
      background-color: #fff !important;
      border-bottom: 2px solid #555;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .fw-bold {
      font-weight: bold;
    }

    .terbilang {
      font-style: italic;
      font-weight: bold;
      text-align: right;
      margin-top: 5px;
      font-size: 11px;
      /* Reduced to 11px */
    }

    .ttd-img {
      height: 60px;
      /* Reduced to 60px */
      margin: 2px 0;
    }

    .signer-name {
      text-decoration: underline;
      font-weight: bold;
    }

    /* FOOTER STYLE - Full width, bottom position */
    .footer-surat {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      width: 100%;
      margin: 0;
      padding: 0;
    }

    .footer-surat img {
      width: 100%;
      height: auto;
      display: block;
    }

    /* PRINT MEDIA QUERY */
    @media print {
      @page {
        size: A4;
        margin: 0; /* Remove all page margins for edge-to-edge */
      }

      body {
        background: white;
        margin: 0;
      }

      /* HIDE UI ELEMENTS */
      .navbar-custom,
      .left-side-menu,
      .footer,
      .no-print,
      .breadcrumb,
      #app-layout>.content-page>.content>.container-fluid>.py-3 {
        display: none !important;
      }

      #app-layout,
      .content-page,
      .content,
      .container-fluid {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
      }

      /* SHOW PAPER FULLSCREEN */
      .paper-container {
        box-shadow: none;
        margin: 0;
        width: 100%;
        min-height: 100vh;
        padding: 10mm;
        padding-bottom: 0 !important; /* No bottom padding */
        position: relative;
        /* Minimal print padding */
      }

      /* Footer absolute at bottom for reliable printing */
      .footer-surat {
        position: absolute !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        page-break-inside: avoid !important;
      }

      .footer-surat img {
        width: 100% !important;
        height: auto !important;
        display: block !important;
        max-height: none !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
    }

    /* ATTACHMENT STYLES (User Final Request) */
    .attachment-page {
      page-break-before: always;
      display: block;
      /* Ensure it renders */
    }

    .attachment-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    .attachment-grid img {
      width: 100%;
      height: 220px;
      object-fit: contain;
      object-fit: contain;
    }
  </style>
</head>

<body data-menu-color="light" data-sidebar="default">

  <!-- DASHBOARD WRAPPER -->
  <div id="app-layout">

    <!-- Sidebar & Navbar (Included) -->
    @include('navbar.navbar')

    <div class="content-page">
      <div class="content">
        <div class="container-fluid">

          <!-- PAGE HEADER (Screen Only) -->
          <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column no-print">
            <div class="flex-grow-1">
              <h4 class="fs-18 fw-semibold m-0">Detail Surat Penawaran</h4>
            </div>
            <div class="text-end">
              <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/sph') }}">SPH</a></li>
                <li class="breadcrumb-item active">Detail</li>
              </ol>
            </div>
          </div>

          <!-- Button Kembali -->
          <div class="mb-3 no-print">
            <a href="{{ url('/sph') }}" class="btn btn-light">
              <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
          </div>

          <!-- Floating Print Button -->
          <button onclick="window.print()" class="btn btn-primary rounded-circle shadow-lg no-print"
            style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 9999; display: flex; align-items: center; justify-content: center;">
            <i class="mdi mdi-printer fs-24"></i>
          </button>

          <!-- LOADING -->
          <div id="loading" class="text-center py-5 no-print">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2">Memuat dokumen...</p>
          </div>

          <!-- THE OFFICIAL DOCUMENT (Paper Look) -->
          <div id="paper-wrapper" style="display:none;">
            <div class="paper-container doc-body">

              <!-- 1. KOP SURAT -->
              <div class="kop-surat mb-1">
                <img src="{{ asset('assets/images/kopsurat.png') }}" alt="Kop Surat">
              </div>

              <!-- 2. DATE & INFO -->
              <div class="text-end mb-2">
                <span id="tempat-sph">Banten</span>, <span id="tanggal-sph">-</span>
              </div>

              <table style="width: 100%; margin-bottom: 10px;">
                <tr>
                  <td style="width: 60px;">No.</td>
                  <td>: <span id="nomor-sph">-</span></td>
                </tr>
                <tr>
                  <td>Hal.</td>
                  <td class="fw-bold" style="text-decoration: underline;">: Penawaran Harga</td>
                </tr>
              </table>

              <!-- 3. RECIPIENT -->
              <div class="mb-2">
                <strong>Kepada.</strong><br>
                <strong>Yth</strong><br>
                <strong id="jabatan-tujuan">Direktur</strong><br>
                <strong id="nama-perusahaan-sph"></strong><br>
                <span id="alamat-perusahaan"></span>
              </div>

              <!-- 4. OPENING -->
              <div class="mb-2" style="text-align: justify;">
                Dengan Hormat,<br>
                Perihal Penawaran Harga, Bersama ini kami sampaikan penawaran harga
                <strong id="nama-barang-summary">Alat Kesehatan</strong>
                di <span id="nama-perusahaan-text">-</span> sebagai berikut :
              </div>

              <!-- 5. TABLE -->
              <table class="table-custom">
                <thead>
                  <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Barang</th>
                    <th style="width: 80px;">Qty</th>
                    <th style="width: 130px;">Harga</th>
                  </tr>
                </thead>
                <tbody id="items-tbody-sph"></tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" class="text-center fw-bold">Total harga</td>
                    <td class="text-center fw-bold text-right" id="total-harga-display">-</td>
                  </tr>
                </tfoot>
              </table>

              <!-- 6. TERBILANG -->
              <div class="terbilang" id="terbilang-text"></div>

              <!-- 7. NOTES (Rich Text) -->
              <div class="mt-2">
                <div id="keterangan-sph" style="font-size: 13px;"></div>
              </div>

              <!-- 9. CLOSING -->
              

              <!-- 10. SIGNATURE -->
              <div class="ttd-section">
                <p class="mb-0">Hormat kami<br>
                  <strong>PT. RANAY NUSANTARA SEJAHTERA</strong>
                </p>

                <img id="ttd-image-sph" class="ttd-img" src="" alt="Tanda Tangan">
                <br>

                <span class="signer-name" id="penandatangan-sph">-</span>
              </div>

              <!-- 11. FOOTER IMAGE -->
              <div class="footer-surat mt-4">
                <img src="{{ asset('assets/images/footerrns.png') }}" alt="Footer RNS" style="width: 100%; height: auto;">
              </div>

            </div> <!-- End Paper Container (Main) -->

            <!-- Container for dynamic extra pages (Attachments) -->
            <div id="attachments-wrapper"></div>

          </div> <!-- End Paper Wrapper -->

          <div style="height: 50px;"></div>

        </div>
      </div>
    </div>

  </div>

  <!-- Vendor JS -->
  <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
  <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const pathParts = window.location.pathname.split('/');
      const sphId = pathParts[pathParts.length - 1];
      loadSPH(sphId);
    });

    function loadSPH(id) {
      const token = localStorage.getItem('token');
      fetch(`http://127.0.0.1:8000/api/surat-penawaran/${id}`, {
        method: 'GET',
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
      })
        .then(res => res.json())
        .then(data => renderSPH(data))
        .catch(err => {
          document.getElementById('loading').innerHTML = '<p class="text-danger">Gagal memuat data.</p>';
        });
    }

    function renderSPH(data) {
      document.getElementById('loading').style.display = 'none';
      document.getElementById('paper-wrapper').style.display = 'block';

      // -- FILL TEXT --
      setText('tempat-sph', data.tempat || 'Banten');
      setText('tanggal-sph', formatDate(data.tanggal));
      setText('nomor-sph', data.nomor_sph);
      setText('jabatan-tujuan', data.jabatan_tujuan);
      setText('nama-perusahaan-sph', data.nama_perusahaan);
      setText('nama-perusahaan-text', data.nama_perusahaan);
      setText('alamat-perusahaan', data.alamat);

      const firstItemName = data.detail_barang && data.detail_barang.length > 0
        ? data.detail_barang[0].nama
        : 'Alat Kesehatan';
      setText('nama-barang-summary', firstItemName);

      // TABLE
      const tbody = document.getElementById('items-tbody-sph');
      tbody.innerHTML = '';
      if (data.detail_barang) {
        data.detail_barang.forEach((item, i) => {
          tbody.innerHTML += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td>${item.nama}</td>
                            <td class="text-center">${item.jumlah} Unit</td>
                            <td class="text-right">${formatRupiah(item.total)}</td> 
                        </tr>
                    `;
        });
      }

      // TOTAL & TERBILANG
      const totalVal = data.total_keseluruhan || 0;
      setText('total-harga-display', formatRupiah(totalVal));
      setText('terbilang-text', terbilang(totalVal) + ' Rupiah');

      // KETERANGAN
      if (data.keterangan) {
        document.getElementById('keterangan-sph').innerHTML = data.keterangan;
      } else {
        document.getElementById('keterangan-sph').innerHTML = '-';
      }

      // TANDA TANGAN
      setText('penandatangan-sph', data.penandatangan);
      const ttdImg = document.getElementById('ttd-image-sph');
      const baseUrl = window.location.origin;

      if (data.penandatangan && data.penandatangan.toLowerCase().includes('dewi')) {
        ttdImg.src = `${baseUrl}/assets/images/ttdDewi.png`;
      } else {
        ttdImg.src = `${baseUrl}/assets/images/ttdHeri.png`;
      }

      // ================= LAMPIRAN GAMBAR AUTO PAGE (User Final Logic) =================
      const attachmentWrapper = document.getElementById('attachments-wrapper');
      attachmentWrapper.innerHTML = '';

      if (data.lampiran_gambar_urls && data.lampiran_gambar_urls.length > 0) {
        const BASE_URL = window.location.origin;
        const imagesPerPage = 4; // 2x2 layout

        for (let i = 0; i < data.lampiran_gambar_urls.length; i += imagesPerPage) {
          const pageImages = data.lampiran_gambar_urls.slice(i, i + imagesPerPage);

          const pageDiv = document.createElement('div');
          pageDiv.className = 'paper-container doc-body attachment-page';
          // Add margin-top if needed relative to previous pages
          pageDiv.style.marginTop = '30px';

          let html = `
            <div class="kop-surat mb-3">
              <img src="${BASE_URL}/assets/images/kopsurat.png">
            </div>

            <div class="attachment-grid">
          `;

          pageImages.forEach(img => {
            // Check if img.url is absolute or relative
            const imgSrc = img.url.startsWith('http') ? img.url : `${BASE_URL}${img.url}`;

            html += `
              <div>
                <img src="${imgSrc}">
              </div>
            `;
          });

          html += `</div>`;
          
          // Add footer to each attachment page
          html += `
            <div class="footer-surat">
              <img src="${BASE_URL}/assets/images/footerrns.png" alt="Footer RNS">
            </div>
          `;
          
          pageDiv.innerHTML = html;
          attachmentWrapper.appendChild(pageDiv);
        }
      }



    }

    function setText(id, val) {
      const el = document.getElementById(id);
      if (el) el.textContent = val || '';
    }
    function formatRupiah(angka) {
      return new Intl.NumberFormat('id-ID').format(angka) + ',-';
    }
    function formatDate(dateString) {
      if (!dateString) return '-';
      return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }
    function terbilang(nilai) {
      nilai = Math.abs(nilai);
      var simpanNilai = String(nilai);
      var huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
      var temp = "";
      if (nilai < 12) temp = " " + huruf[nilai];
      else if (nilai < 20) temp = terbilang(nilai - 10) + " Belas";
      else if (nilai < 100) temp = terbilang(Math.floor(nilai / 10)) + " Puluh" + terbilang(nilai % 10);
      else if (nilai < 200) temp = " Seratus" + terbilang(nilai - 100);
      else if (nilai < 1000) temp = terbilang(Math.floor(nilai / 100)) + " Ratus" + terbilang(nilai % 100);
      else if (nilai < 2000) temp = " Seribu" + terbilang(nilai - 1000);
      else if (nilai < 1000000) temp = terbilang(Math.floor(nilai / 1000)) + " Ribu" + terbilang(nilai % 1000);
      else if (nilai < 1000000000) temp = terbilang(Math.floor(nilai / 1000000)) + " Juta" + terbilang(nilai % 1000000);
      else if (nilai < 1000000000000) temp = terbilang(Math.floor(nilai / 1000000000)) + " Milyar" + terbilang(nilai % 1000000000);
      return temp;
    }
  </script>
</body>

</html>