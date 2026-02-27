<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <title>Detail Surat Jalan | RNS - Ranay Nusantara Sejahtera</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Halaman detail surat jalan RNS." />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('assets/js/head.js') }}"></script>

    <style>
        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
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
                <h4 class="fs-18 fw-semibold m-0">Detail Surat Jalan</h4>
              </div>
              <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                  <li class="breadcrumb-item"><a href="/surat-jalan">Halaman</a></li>
                  <li class="breadcrumb-item active">Surat Jalan Detail</li>
                </ol>
              </div>
            </div>

            
            <div class="mb-3 d-flex justify-content-between">
                <a href="{{ url('/surat-jalan') }}" class="btn btn-light">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali
                </a>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditSuratJalan">
                    <i class="mdi mdi-pencil me-1"></i> Edit Surat Jalan
                </button>
            </div>

                <div class="row">
                    
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="fw-semibold mb-4">Informasi Surat Jalan</h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="info-label">Tanggal</div>
                                        <div class="info-value" id="detailTanggal">Loading...</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Nama Pengirim</div>
                                        <div class="info-value" id="detailNamaPengirim">Loading...</div> 
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="info-label">Keterangan</div>
                                    <div class="info-value" id="detailKeterangan">Loading...</div>
                                </div>

                                <hr class="my-4">

                                <h5 class="fw-semibold mb-4">Detail Barang</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered border-dark mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-center" style="width: 5%;">NO</th>
                                                <th>NAMA BARANG / JASA</th>
                                                <th class="text-center" style="width: 15%;">JUMLAH BARANG</th>
                                                <th class="text-center" style="width: 15%;">JUMLAH</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1.</td>
                                                <td id="detailNamaBarang">-</td>
                                                <td class="text-center" id="detailQty">-</td>
                                                <td class="text-center" id="detailJumlah">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="fw-semibold mb-4">Data Penerima</h5>

                                <div class="mb-3">
                                    <div class="info-label">Nama Penerima</div>
                                    <div class="info-value" id="detailNamaPenerima">Loading...</div>
                                </div>

                                <div class="mb-3">
                                    <div class="info-label">Alamat</div>
                                    <div class="info-value" id="detailAlamatPenerima">Loading...</div>
                                </div>

                                <div class="mb-3">
                                    <div class="info-label">No. Telepon</div>
                                    <div class="info-value" id="detailTelpPenerima">Loading...</div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h5 class="fw-semibold mb-3">Penandatangan</h5>
                                <div class="text-center">
                                    <p class="mb-1 fw-medium">ENGINEER</p>
                                    <div style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                        <img id="detailSignature" src="" alt="Tanda Tangan" style="max-height: 80px; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <p class="fw-bold m-0 text-uppercase" id="detailSignerName">Loading...</p>
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
                &copy;
                <script>document.write(new Date().getFullYear())</script>
                - Made with <span class="mdi mdi-heart text-danger"></span> by
                <a href="#!" class="text-reset fw-semibold">TI UMY 22</a>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>

    
    <div class="modal fade" id="modalEditSuratJalan" tabindex="-1" aria-labelledby="modalEditSuratJalanLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning text-white">
            <h5 class="modal-title text-white" id="modalEditSuratJalanLabel">
              <i class="mdi mdi-pencil me-2"></i>Edit Surat Jalan
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditSuratJalan">
              <div class="row">
                
                <input type="hidden" id="editNomor" name="nomor_surat_jalan" />

                <div class="col-md-12 mb-3">
                  <label for="editTanggal" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" id="editTanggal" name="tanggal" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="editNamaPengirim" class="form-label fw-semibold">Nama Pengirim <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="editNamaPengirim" name="nama_pengirim" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="editNamaPenerima" class="form-label fw-semibold">Nama Penerima <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="editNamaPenerima" name="nama_penerima" required />
                </div>
                <div class="col-12 mb-3">
                  <label for="editAlamatPenerima" class="form-label fw-semibold">Alamat Penerima <span class="text-danger">*</span></label>
                  <textarea class="form-control" id="editAlamatPenerima" name="alamat_penerima" rows="2" required></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="editTelpPenerima" class="form-label fw-semibold">No. Telepon Penerima</label>
                  <input type="text" class="form-control" id="editTelpPenerima" name="telp_penerima" />
                </div>
                <div class="col-12"><hr></div>
                <div class="col-md-6 mb-3">
                  <label for="editNamaBarang" class="form-label fw-semibold">Nama Barang / Jasa <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="editNamaBarang" name="nama_barang_jasa" required />
                </div>
                <div class="col-md-3 mb-3">
                  <label for="editQty" class="form-label fw-semibold">Jumlah Barang <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" id="editQty" name="qty" required />
                </div>
                <div class="col-md-3 mb-3">
                    <label for="editJumlah" class="form-label fw-semibold">Jumlah (Rp)</label>
                    <input type="number" class="form-control" id="editJumlah" name="jumlah" />
                </div>
                <div class="col-12 mb-3">
                  <label for="editKeterangan" class="form-label fw-semibold">Keterangan</label>
                  <textarea class="form-control" id="editKeterangan" name="keterangan" rows="2"></textarea>
                </div>
                <div class="col-12 mb-3">
                  <label for="editPenandatangan" class="form-label fw-semibold">Nama Penandatangan <span class="text-danger">*</span></label>
                  <select class="form-select" id="editPenandatangan" name="penandatangan" required>
                    <option value="">Pilih penandatangan...</option>
                    <option value="Dewi Sulistiowati">Dewi Sulistiowati</option>
                    <option value="Heri Pirdaus, S.Tr.Kes Rad (MRI)">Heri Pirdaus, S.Tr.Kes Rad (MRI)</option>
                    <option value="MUHAMMAD ARYA">MUHAMMAD ARYA</option>
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="mdi mdi-close me-1"></i>Batal
            </button>
            <button type="button" class="btn btn-warning text-white" id="btnUpdateSuratJalan">
              <i class="mdi mdi-content-save me-1"></i>Update Surat Jalan
            </button>
          </div>
        </div>
      </div>
    </div>

    
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>

    
    <script src="{{ asset('assets/js/app.js') }}"></script>

    
    <script src="{{ asset('assets/js/detail-surat-jalan.js') }}"></script>
  </body>
</html>
