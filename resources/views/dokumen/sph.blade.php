<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>SPH | RNS - Ranay Nusantara Sejathera</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />


    <link rel="shortcut icon" href="assets/images/favicon.ico">


    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />


    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <script src="assets/js/head.js"></script>
</head>



<body data-menu-color="light" data-sidebar="default">
    @include('navbar.navbar')

    <div id="app-layout">
        <div class="content-page">
            <div class="content">

                <div class="container-fluid">
                    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-18 fw-semibold m-0">Surat Penawaran Harga</h4>
                        </div>

                        <div class="text-end">
                            <ol class="breadcrumb m-0 py-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Halaman</a></li>
                                <li class="breadcrumb-item active">SPH</li>
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
                                        <h5 class="fw-semibold mb-1">Daftar Surat Penawaran Harga</h5>
                                        <p class="text-muted mb-0">Kelola dan pantau seluruh surat penawaran Anda</p>
                                    </div>


                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <!-- Bulk Delete Button (Hidden by default) -->
                                        <button type="button" id="btnBulkDelete" class="btn btn-danger text-white border-0 shadow-sm" style="display: none;" onclick="bulkDeleteSPH()">
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
                                                <li><a class="dropdown-item" href="#" onclick="setStatusFilter('Menunggu')">Menunggu</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="setStatusFilter('Diterima')">Diterima</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="setStatusFilter('Ditolak')">Ditolak</a></li>
                                            </ul>
                                        </div>

                                        <div class="dropdown">
                                            <button class="btn btn-light border dropdown-toggle" type="button"
                                                id="filterWaktu" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-calendar-range me-1"></i>
                                                <span id="selectedFilter">Semua Waktu</span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="filterWaktu">
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="setFilter('Hari Ini')">Hari Ini</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="setFilter('Minggu Ini')">Minggu Ini</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="setFilter('Bulan Ini')">Bulan Ini</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="setFilter('Semua Waktu')">Semua Waktu</a></li>
                                            </ul>
                                        </div>


                                        <form class="app-search">
                                            <div class="position-relative topbar-search">
                                                <input type="text" class="form-control ps-4" placeholder="Cari SPH..."
                                                    style="min-width: 200px;" id="searchInput" onkeyup="searchSPH()" />
                                                <i
                                                    class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="selectAllSPH" onchange="toggleSelectAllSPH(this)">
                                                    </div>
                                                </th>
                                                <th class="text-center" style="width: 50px;">No</th>
                                                <th>Nomor SPH</th>
                                                <th class="text-center">Tanggal</th>
                                                <th>Nama Perusahaan</th>
                                                <th class="text-center">Nilai Penawaran</th>
                                                <th class="text-center">Dibuat Oleh</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center" style="width: 100px;">Aksi</th>
                                            </tr>
                                        </thead>


                                        <tbody id="sph-table-body">

                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">Menampilkan 1–3 dari 50 SPH</small>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0">
                                            <li class="page-item"><a class="page-link" href="#">‹</a></li>
                                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                            <li class="page-item"><a class="page-link" href="#">›</a></li>
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
                            <script>document.write(new Date().getFullYear())</script> - Made with <span
                                class="mdi mdi-heart text-danger"></span> by <a href="#!"
                                class="text-reset fw-semibold">TI UMY 22</a>
                        </div>
                    </div>
                </div>
            </footer>



            <div class="modal fade" id="modalTambahSPH" tabindex="-1" aria-labelledby="modalTambahSPHLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalTambahSPHLabel">
                                <i class="mdi mdi-file-document-edit-outline me-2"></i>Tambah Surat Penawaran Harga
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formTambahSPH">
                                <!-- Hidden field to track edit mode -->
                                <input type="hidden" id="sphEditId" value="">


                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-semibold"><i
                                                class="mdi mdi-information-outline me-2"></i>Informasi Surat</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Nomor SPH</label>
                                                <input type="text" class="form-control" name="nomor_sph"
                                                    placeholder="Otomatis dibuat sistem" value="Otomatis dibuat sistem"
                                                    readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                                <small class="text-muted">
                                                    <i class="mdi mdi-information-outline"></i>
                                                    Sistem akan auto-generate: XX/SPH/XRAY/RNS-XX/2025
                                                </small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Tanggal <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="tanggal" id="tanggalSPH"
                                                    required>
                                                <small class="text-muted">
                                                    <i class="mdi mdi-information-outline"></i>
                                                    Tanggal tidak boleh di masa lalu
                                                </small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Tempat</label>
                                                <input type="text" class="form-control" name="tempat" value="Banten"
                                                    placeholder="Banten">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Lampiran</label>
                                                <input type="text" class="form-control" name="lampiran"
                                                    value="Otomatis dari sistem" disabled>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">Hal <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="hal"
                                                    value="Penawaran Harga" readonly style="background-color: #e9ecef;">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-semibold"><i
                                                class="mdi mdi-account-outline me-2"></i>Informasi Penerima</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Kepada (Jabatan) <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="jabatan_tujuan"
                                                    value="Direktur" placeholder="Direktur" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Nama Perusahaan/Instansi <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nama_perusahaan"
                                                    placeholder="Contoh: RSUD Tobat Balaraja" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label class="form-label">Alamat Lengkap</label>
                                                <textarea class="form-control" name="alamat" rows="3" 
                                                    placeholder="Contoh: Jl. Cut Nya Dien, Cipoko, Kec. Kuningan, Kabupaten Kuningan, Jawa Barat 45513"></textarea>
                                                <small class="text-muted">Alamat lengkap perusahaan/instansi penerima</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold"><i
                                                class="mdi mdi-format-list-bulleted me-2"></i>Detail Penawaran</h6>
                                        <button type="button" class="btn btn-primary btn-sm" id="btnTambahItem">
                                            <i class="mdi mdi-plus"></i> Tambah Barang
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="itemContainer">
                                            <div class="item-row border rounded p-3 mb-3 bg-light">
                                                <div class="row g-2 align-items-end">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Nama Barang <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select select-barang" name="items[0][nama]"
                                                            required>
                                                            <option value="">Pilih barang...</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Harga Satuan</label>
                                                        <input type="text" class="form-control harga-satuan"
                                                            name="items[0][harga_display]" placeholder="Rp 0" readonly
                                                            style="background-color: #e9ecef;">
                                                        <input type="hidden" class="harga-satuan-value"
                                                            name="items[0][harga_satuan]">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Jumlah <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" class="form-control jumlah-barang"
                                                            name="items[0][jumlah]" placeholder="1" min="1" value="1"
                                                            required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Total</label>
                                                        <input type="text" class="form-control total-item"
                                                            name="items[0][total_display]" placeholder="Rp 0" readonly
                                                            style="background-color: #e9ecef;">
                                                        <input type="hidden" class="total-item-value"
                                                            name="items[0][total]">
                                                    </div>
                                                    <div class="col-md-1 text-center">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm btn-hapus-item"
                                                            style="display:none;">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-8"></div>
                                            <div class="col-md-4">
                                                <div class="card bg-primary text-white">
                                                    <div class="card-body p-3">
                                                        <label class="form-label text-white mb-1">Total
                                                            Keseluruhan</label>
                                                        <h4 class="mb-0" id="totalKeseluruhan">Rp 0</h4>
                                                        <input type="hidden" name="total_keseluruhan"
                                                            id="totalKeseluruhanValue">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-semibold"><i
                                                class="mdi mdi-text-box-outline me-2"></i>Keterangan Tambahan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">Keterangan / Catatan</label>

                                                <!-- Rich Text Editor Toolbar -->
                                                <div
                                                    class="border rounded-top p-2 bg-light d-flex gap-2 align-items-center">
                                                    <button type="button" id="btnBold"
                                                        class="btn btn-sm btn-outline-primary fw-bold"
                                                        onclick="formatDoc('bold')" title="Bold">B</button>
                                                    <button type="button" id="btnItalic"
                                                        class="btn btn-sm btn-outline-primary fst-italic"
                                                        onclick="formatDoc('italic')" title="Italic">I</button>
                                                    <button type="button" id="btnUnderline"
                                                        class="btn btn-sm btn-outline-primary text-decoration-underline"
                                                        onclick="formatDoc('underline')" title="Underline">U</button>
                                                    <div class="vr"></div>
                                                    <select id="fontSizeSelect"
                                                        class="form-select form-select-sm w-auto"
                                                        onchange="setFontSize(this.value);" title="Ukuran Font">
                                                        <option value="">Ukuran</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="14">14</option>
                                                        <option value="16">16</option>
                                                        <option value="18">18</option>
                                                        <option value="20">20</option>
                                                        <option value="22">22</option>
                                                        <option value="24">24</option>
                                                        <option value="26">26</option>
                                                        <option value="28">28</option>
                                                        <option value="36">36</option>
                                                        <option value="48">48</option>
                                                        <option value="72">72</option>
                                                    </select>
                                                </div>

                                                <!-- Editable Area -->
                                                <div id="editor" class="form-control rounded-0 rounded-bottom"
                                                    style="min-height: 200px; overflow-y: auto;" contenteditable="true">
                                                    <b>Catatan:</b><br>
                                                    - Kondisi alat second layak pakai dan masih sangat bagus.<br>
                                                    - Harga sudah termasuk ongkir, Instal, Uji Fungsi, Uji Kesesuaian, Uji Paparan Ruangan dan Perijinan<br>
                                                    - Garansi service X-Ray 3 Bulan, Garansi tidak berlaku, jika terjadi keadaan memaksa (force majeure), yaitu keadaan di luar kemampuan seperti bencana alam, konsleting listrik, banjir, kebakaran, mobilisasi, pemogokan, blokade, revolusi, huru hara, sabotase<br>
                                                    - <b>Cara pembayaran:</b><br>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;Pembayaran Pertama <b>DP 50%</b> Setelah PO atau SPK kami terima<br>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;Pembayaran <b>Ke Dua 50%</b> Setelah Alat terinstal dengan baik Pelunasan.<br><br>
                                                    Pembayaran Bisa Di Tranfer Melalui <b>Rek Bank BSI (BANK SYARIAH INDONESIA) :</b><br><br>
                                                    <b>No Rek : 1101198975</b><br>
                                                    <b>Atas Nama : PT RANAY NUSANTARA SEJAHTERA</b><br>
                                                    <b>Kode bank : 451</b><br><br>
                                                    Demikian surat penawaran ini kami buat, apabila ada informasi yang perlu diketahui lebih lanjut mengenai penawaran ini, maka dapat menghubungi kami <b>(Bpk Heri Pirdaus : 085273435980)</b>.<br>
                                                    Atas perhatiannya dan kerjasamanya kami ucapkan terimakasih.
                                                </div>

                                                <!-- Hidden Input for Form Submission -->
                                                <textarea name="keterangan" id="keteranganInput"
                                                    style="display:none;"></textarea>

                                                <script>
                                                    function formatDoc(cmd, value = null) {
                                                        if (value) {
                                                            document.execCommand(cmd, false, value);
                                                        } else {
                                                            document.execCommand(cmd);
                                                        }
                                                        document.getElementById('editor').focus();
                                                        updateToolbar(); // Update state immediately
                                                        updateHiddenInput();
                                                    }

                                                    function setFontSize(size) {
                                                        if (!size) return;
                                                        // Use a marker size (7) that we can easily find and replace
                                                        document.execCommand("fontSize", false, "7");

                                                        const fontElements = document.getElementById("editor").getElementsByTagName("font");
                                                        // Convert live list to array to avoid skipping elements as we modify them (if any)
                                                        // changing an attribute might affect the live collection in some browsers
                                                        for (let i = 0; i < fontElements.length; i++) {
                                                            if (fontElements[i].size == "7") {
                                                                fontElements[i].removeAttribute("size");
                                                                fontElements[i].style.fontSize = size + "pt";
                                                            }
                                                        }
                                                        updateHiddenInput();
                                                        updateToolbar();
                                                    }

                                                    function updateToolbar() {
                                                        const selection = window.getSelection();

                                                        // 1. Check if selection is inside editor
                                                        let isInside = false;
                                                        if (selection.rangeCount > 0) {
                                                            const anchorNode = selection.anchorNode;
                                                            if (anchorNode) {
                                                                const parent = anchorNode.nodeType === 3 ? anchorNode.parentElement : anchorNode;
                                                                const editor = document.getElementById('editor');
                                                                if (editor.contains(parent) || editor === parent) {
                                                                    isInside = true;
                                                                }
                                                            }
                                                        }

                                                        if (!isInside) {
                                                            // Clicked outside: Reset everything
                                                            toggleButtonState('btnBold', false);
                                                            toggleButtonState('btnItalic', false);
                                                            toggleButtonState('btnUnderline', false);
                                                            document.getElementById('fontSizeSelect').value = "";
                                                            return;
                                                        }

                                                        // 2. UPDATE BUTTON STATES (B, I, U)
                                                        toggleButtonState('btnBold', document.queryCommandState('bold'));
                                                        toggleButtonState('btnItalic', document.queryCommandState('italic'));
                                                        toggleButtonState('btnUnderline', document.queryCommandState('underline'));

                                                        const anchorNode = selection.anchorNode;
                                                        const parent = anchorNode.nodeType === 3 ? anchorNode.parentElement : anchorNode;

                                                        // 3. UPDATE FONT SIZE DROPDOWN
                                                        const computedStyle = window.getComputedStyle(parent);
                                                        const fontSizePx = parseFloat(computedStyle.fontSize);

                                                        // Convert px to pt
                                                        const fontSizePt = Math.round(fontSizePx * 0.75);

                                                        const select = document.getElementById('fontSizeSelect');
                                                        let found = false;
                                                        for (let i = 0; i < select.options.length; i++) {
                                                            if (parseInt(select.options[i].value) === fontSizePt) {
                                                                select.value = select.options[i].value;
                                                                found = true;
                                                                break;
                                                            }
                                                        }
                                                        if (!found) select.value = "";
                                                    }

                                                    function toggleButtonState(btnId, isActive) {
                                                        const btn = document.getElementById(btnId);
                                                        if (btn) {
                                                            if (isActive) {
                                                                btn.classList.remove('btn-outline-primary');
                                                                btn.classList.add('btn-primary'); // Blue color
                                                            } else {
                                                                btn.classList.remove('btn-primary');
                                                                btn.classList.add('btn-outline-primary');
                                                            }
                                                        }
                                                    }

                                                    // Sync content
                                                    const editor = document.getElementById('editor');
                                                    editor.addEventListener('input', updateHiddenInput);
                                                    editor.addEventListener('keyup', updateToolbar);
                                                    editor.addEventListener('mouseup', updateToolbar);
                                                    editor.addEventListener('click', updateToolbar);

                                                    // Global selection change to catch clicking outside
                                                    document.addEventListener('selectionchange', updateToolbar);

                                                    function updateHiddenInput() {
                                                        document.getElementById('keteranganInput').value = editor.innerHTML;
                                                    }

                                                    // Initialize
                                                    window.addEventListener('load', function () {
                                                        updateHiddenInput();
                                                        updateToolbar();
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-semibold"><i
                                                class="mdi mdi-image-multiple-outline me-2"></i>Lampiran Foto</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="form-label">Upload Foto</label>
                                                    <div id="foto-container">
                                                        <!-- Dynamic Photo Inputs will appear here -->
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                                        id="btnTambahFoto">
                                                        <i class="mdi mdi-plus"></i> Tambah Foto
                                                    </button>
                                                    <small class="text-muted d-block mt-2">Format: JPG, PNG,
                                                        JPEG.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- LAMPIRAN GAMBAR Removed (Integrated to Lampiran Foto) -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-semibold"><i
                                                class="mdi mdi-pencil-outline me-2"></i>Penandatangan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">Nama Penandatangan <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" name="penandatangan" required>
                                                    <option value="">Pilih penandatangan...</option>
                                                    <option value="Dewi Sulistiowati">Dewi Sulistiowati</option>
                                                    <option value="Heri Pirdaus, S.Tr.Kes Rad (MRI)">Heri Pirdaus,
                                                        S.Tr.Kes Rad (MRI)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="mdi mdi-close"></i> Batal
                            </button>
                            <button type="button" class="btn btn-primary" id="btnSimpanSPH">
                                <i class="mdi mdi-content-save"></i> Simpan SPH
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="content position-relative">
                <button type="button" class="btn btn-primary rounded-circle shadow-lg"
                    style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 999;"
                    data-bs-toggle="modal" data-bs-target="#modalTambahSPH" title="Tambah SPH Baru">
                    <i class="mdi mdi-plus fs-3 text-white"></i>
                </button>
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


    <script>
        // setFilter and searchSPH are now in surat-penawaran.js

        // searchSPH function is now in surat-penawaran.js

        $(document).ready(function () {
            let itemCounter = 1;
            // Variable to store stock options HTML
            let stokOptionsHTML = '<option value="">Pilih barang...</option>';

            // Load Stock Options
            function loadStokForDropdown() {
                const token = localStorage.getItem("token"); // Use global getToken if available or this
                // If API_URL is not defined in this scope, we can reconstruct it or use the one from stok.js if loaded.
                // Assuming standard API path:
                const API_STOK = "http://127.0.0.1:8000/api/stoks";

                fetch(API_STOK, {
                    method: "GET",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/json"
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error("Gagal mengambil data stok");
                        return response.json();
                    })
                    .then(data => {
                        const stoks = data.data || [];
                        stokOptionsHTML = '<option value="">Pilih barang...</option>';

                        stoks.forEach(item => {
                            // Assuming item has nama_barang and harga
                            const nama = item.nama_barang || "Unnamed Item";
                            const harga = item.harga || 0;
                            stokOptionsHTML += `<option value="${nama}" data-harga="${harga}">${nama}</option>`;
                        });

                        // Update existing dropdowns
                        $('.select-barang').each(function () {
                            const currentVal = $(this).val();
                            $(this).html(stokOptionsHTML);
                            if (currentVal) $(this).val(currentVal);
                        });
                    })
                    .catch(err => console.error("Error loading stok:", err));
            }

            // Call loadStok initially
            loadStokForDropdown();

            // Format Rupiah
            function formatRupiah(angka) {
                return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
            }

            // Event delegation untuk select barang
            $(document).on('change', '.select-barang', function () {
                updateHargaBarang($(this));
            });

            // Event delegation untuk jumlah barang
            $(document).on('input', '.jumlah-barang', function () {
                hitungTotalItem($(this));
            });

            // Update harga saat pilih barang
            function updateHargaBarang(select) {
                const row = select.closest('.item-row');
                const selectedOption = select.find('option:selected');
                const harga = selectedOption.attr('data-harga') || 0;
                const hargaSatuan = row.find('.harga-satuan');
                const hargaSatuanValue = row.find('.harga-satuan-value');
                const jumlah = row.find('.jumlah-barang');
                const totalItem = row.find('.total-item');
                const totalItemValue = row.find('.total-item-value');

                hargaSatuan.val(formatRupiah(parseInt(harga)));
                hargaSatuanValue.val(harga);

                const total = parseInt(harga) * parseInt(jumlah.val() || 1);
                totalItem.val(formatRupiah(total));
                totalItemValue.val(total);

                hitungTotalKeseluruhan();
            }

            // Fungsi untuk menghitung total per item
            function hitungTotalItem(input) {
                const row = input.closest('.item-row');
                const select = row.find('.select-barang');
                const selectedOption = select.find('option:selected');
                const harga = parseInt(selectedOption.attr('data-harga') || 0);
                const jumlah = parseInt(input.val()) || 0;
                const totalItem = row.find('.total-item');
                const totalItemValue = row.find('.total-item-value');

                const total = harga * jumlah;
                totalItem.val(formatRupiah(total));
                totalItemValue.val(total);

                hitungTotalKeseluruhan();
            }

            // Fungsi untuk menghitung total keseluruhan
            function hitungTotalKeseluruhan() {
                let totalSemua = 0;
                $('.item-row').each(function () {
                    const select = $(this).find('.select-barang');
                    const selectedOption = select.find('option:selected');
                    const jumlah = parseInt($(this).find('.jumlah-barang').val()) || 0;
                    const harga = parseInt(selectedOption.attr('data-harga') || 0);
                    totalSemua += harga * jumlah;
                });
                $('#totalKeseluruhan').text(formatRupiah(totalSemua));
                $('#totalKeseluruhanValue').val(totalSemua);
            }

            // Update tampilan tombol hapus
            function updateRemoveButtons() {
                const items = $('.item-row');
                items.each(function () {
                    const btnHapus = $(this).find('.btn-hapus-item');
                    if (items.length > 1) {
                        btnHapus.show();
                    } else {
                        btnHapus.hide();
                    }
                });
            }

            // Tambah item baru
            $('#btnTambahItem').click(function () {
                itemCounter++;
                // Use stokOptionsHTML variable here
                const newItem = `
                <div class="item-row border rounded p-3 mb-3 bg-light">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <select class="form-select select-barang" name="items[${itemCounter}][nama]" required>
                                ${stokOptionsHTML}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Harga Satuan</label>
                            <input type="text" class="form-control harga-satuan" name="items[${itemCounter}][harga_display]" placeholder="Rp 0" readonly style="background-color: #e9ecef;">
                            <input type="hidden" class="harga-satuan-value" name="items[${itemCounter}][harga_satuan]">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" class="form-control jumlah-barang" name="items[${itemCounter}][jumlah]" placeholder="1" min="1" value="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control total-item" name="items[${itemCounter}][total_display]" placeholder="Rp 0" readonly style="background-color: #e9ecef;">
                            <input type="hidden" class="total-item-value" name="items[${itemCounter}][total]">
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-item">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
                $('#itemContainer').append(newItem);
                updateRemoveButtons();
            });

            // Hapus item
            $(document).on('click', '.btn-hapus-item', function () {
                if ($('.item-row').length > 1) {
                    $(this).closest('.item-row').remove();
                    hitungTotalKeseluruhan();
                    updateRemoveButtons();
                } else {
                    alert('Minimal harus ada 1 item!');
                }
            });

            // Simpan SPH
            $('#btnSimpanSPH').click(function () {
                const form = $('#formTambahSPH');
                if (form[0].checkValidity()) {
                    // Validasi tanggal tidak boleh di masa lalu
                    const tanggalSPH = $('input[name="tanggal"]').val();
                    const today = new Date().toISOString().split('T')[0];

                    if (new Date(tanggalSPH) > new Date(today)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tanggal Tidak Valid',
                            text: 'Tanggal SPH tidak boleh melebihi hari ini!'
                        });
                        // Optionally reset the date input to today if it's in the future
                        $('input[name="tanggal"]').val(today);
                        return;
                    }
                    if (tanggalSPH < today) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tanggal Tidak Valid',
                            text: 'Tanggal SPH tidak boleh di masa lalu! Silakan pilih tanggal hari ini atau yang akan datang.'
                        });
                        return;
                    }

                    // Validasi minimal 1 barang dipilih
                    let adaBarang = false;
                    $('.select-barang').each(function () {
                        if ($(this).val()) {
                            adaBarang = true;
                        }
                    });

                    if (!adaBarang) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Harap tambahkan minimal satu barang!'
                        });
                        return;
                    }

                    // Ambil data form
                    const formData = new FormData(form[0]);

                    // Tampilkan data yang akan disimpan (untuk testing)
                    console.log('Data SPH:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    // Kirim data ke server via API
                    submitFormSPH(formData);
                } else {
                    form[0].reportValidity();
                }
            });


            // ==========================================
            // DYNAMIC PHOTO INPUTS (Logic Baru & Edit Support)
            // ==========================================

            let photoCounter = 0;
            window.isEditingSPH = false; // Flag untuk mencegah reset saat modal shown

            window.addPhotoRow = function() {
                photoCounter++;
                const rowId = `photo-row-${photoCounter}`;

                const html = `
                    <div class="photo-row border rounded p-2 mb-2 bg-white" id="${rowId}" style="transition: all 0.3s;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <input type="file" class="form-control photo-input" name="lampiran_gambar[]" accept="image/*" onchange="previewPhoto(this)">
                            </div>
                            <div style="width: 60px; height: 60px; background: #f8f9fa; border: 1px dashed #ced4da; border-radius: 4px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <img src="" class="img-preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                <i class="mdi mdi-image text-muted icon-placeholder"></i>
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger btn-sm btn-hapus-foto" onclick="removePhotoRow('${rowId}')">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                $('#foto-container').append(html);
                updatePhotoRemoveButtons();
            }

            // Fungsi Global untuk Populate Foto Existing (Dipanggil dari surat-penawaran.js)
            window.populatePhotos = function(images) {
                $('#foto-container').empty();
                window.isEditingSPH = true; // Set flag edit mode

                if (images && images.length > 0) {
                    images.forEach(img => {
                         const html = `
                            <div class="photo-row border rounded p-2 mb-2 bg-light existing-photo-row">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="flex-grow-1">
                                        <div class="text-muted small fw-bold"><i class="mdi mdi-check-circle text-success me-1"></i>Tersimpan</div>
                                        <div class="text-truncate small" style="max-width: 200px;">${img.nama_file}</div>
                                    </div>
                                    <div style="width: 60px; height: 60px; background: #fff; border: 1px solid #ced4da; border-radius: 4px; overflow: hidden;">
                                        <a href="${img.url}" target="_blank">
                                            <img src="${img.url}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </a>
                                    </div>
                                    <div>
                                        <!-- Tombol Hapus Panggil API -->
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteLampiranSPH(${img.id}, this)" title="Hapus Permanen">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#foto-container').append(html);
                    });
                }
                
                // Selalu tambahkan 1 baris kosong untuk upload baru
                addPhotoRow();
            }

            window.removePhotoRow = function (rowId) {
                // Jangan hapus jika itu satu-satunya baris input baru AND tidak ada foto existing
                const inputRows = $('#foto-container .photo-row').not('.existing-photo-row');
                const existingRows = $('#foto-container .existing-photo-row');

                if (inputRows.length > 1 || existingRows.length > 0) {
                     $(`#${rowId}`).remove();
                } else {
                    // Reset saja
                    const row = $(`#${rowId}`);
                    row.find('input').val('');
                    row.find('.img-preview').attr('src', '').hide();
                    row.find('.icon-placeholder').show();
                }
                updatePhotoRemoveButtons();
            }

            window.previewPhoto = function (input) {
                const row = $(input).closest('.photo-row');
                const file = input.files[0];
                const imgPreview = row.find('.img-preview');
                const iconPlaceholder = row.find('.icon-placeholder');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imgPreview.attr('src', e.target.result).show();
                        iconPlaceholder.hide();
                    }
                    reader.readAsDataURL(file);
                } else {
                    imgPreview.attr('src', '').hide();
                    iconPlaceholder.show();
                }
            }

            function updatePhotoRemoveButtons() {
                // Logic update button remove (optional/simplified)
                // Kita biarkan enable selalu, kecuali logic removePhotoRow menangani limit
                const rows = $('#foto-container .photo-row').not('.existing-photo-row');
                if (rows.length === 1 && $('#foto-container .existing-photo-row').length === 0) {
                    rows.find('.btn-hapus-foto').prop('disabled', true);
                } else {
                    rows.find('.btn-hapus-foto').prop('disabled', false);
                }
            }

            // Event Listener Tombol Tambah
            $('#btnTambahFoto').off('click').on('click', function () {
                addPhotoRow();
            });

            // Reset form & init photo row saat modal dibuka/tutup
            $('#modalTambahSPH').on('hidden.bs.modal', function () {
                $('#formTambahSPH')[0].reset();

                // Reset Barang
                itemCounter = 1;
                // ... (reset barang code omitted for brevity as it's not changing logic much but part of replace block)
                // We trust existing logic for barang reset
                 $('#itemContainer .item-row').not(':first').remove();
                const firstRow = $('#itemContainer .item-row:first');
                firstRow.find('.select-barang').val('');
                firstRow.find('.harga-satuan').val('');
                firstRow.find('.jumlah-barang').val('1');
                firstRow.find('.total-item').val('');
                $('#totalKeseluruhan').text('Rp 0');
                
                // Reset Foto
                $('#foto-container').empty();
                addPhotoRow(); // Tambah 1 baris kosong
                window.isEditingSPH = false; // Reset flag
            });

            // Init pertama kali
            $('#modalTambahSPH').on('shown.bs.modal', function () {
                const today = new Date().toISOString().split('T')[0];
                const tanggalInput = $('input[name="tanggal"]');
                // Only set today if empty or not editing? 
                // Logic existing: always set min today. But for edit, we might keep old date.
                // Assuming editSPH sets value AFTER show? No, editSPH sets value BEFORE show.
                // But this listener runs AFTER show.
                // If isEditingSPH, don't overwrite value, but strict min date might be annoying for old data.
                if (!window.isEditingSPH) {
                    tanggalInput.val(today);
                }
                 tanggalInput.attr('min', today); // Keep min constraint

                loadStokForDropdown();

                // Pastikan ada minimal 1 row foto (jika tidak sedang edit atau kosong)
                if (!window.isEditingSPH) {
                    if ($('#foto-container .photo-row').length === 0) {
                        addPhotoRow();
                    }
                }
            });

        });
    </script>


    <script src="{{ asset('assets/js/surat-penawaran.js') }}?v={{ time() }}"></script>

</body>

</html>