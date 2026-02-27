<style>
    .modal-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 16px 24px;
    }

    .modal-title {
        font-size: 16px;
        font-weight: 600;
        color: #212529;
        display: flex;
        align-items: center;
    }

    .modal-title i {
        color: #ffc107;
        margin-right: 8px;
        font-size: 20px;
    }

    .modal-body {
        padding: 24px;
        background: #f8f9fa;
    }

    .section-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 14px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e9ecef;
    }

    /* Form Styles */
    .form-label {
        font-size: 12px;
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .form-control, .form-select {
        font-size: 13px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 8px 12px;
        height: auto;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .form-control:read-only {
        background-color: #e9ecef;
    }

    .items-container {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 16px;
    }

    .item-row {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 16px;
        margin-bottom: 12px;
    }

    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .item-label {
        font-size: 12px;
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .btn-add-item {
        background: #0d6efd;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-add-item:hover {
        background: #0b5ed7;
    }

    .btn-remove-item {
        background: #dc3545;
        color: white;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0;
    }

    .btn-remove-item:hover {
        background: #bb2d3b;
    }

    .total-section {
        background: #fff;
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .total-label {
        font-size: 14px;
        font-weight: 600;
        color: #495057;
    }

    .total-value {
        font-size: 18px;
        font-weight: 700;
        color: #0d6efd;
    }

    .radio-group {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .radio-item input[type="radio"] {
        margin: 0;
        cursor: pointer;
    }

    .radio-item label {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
        color: #495057;
        cursor: pointer;
    }

    /* Modal Footer */
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e9ecef;
        background: #fff;
    }

    .modal-footer .btn {
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
    }
</style>


<div class="modal fade" id="inputPembelianModal" tabindex="-1" aria-labelledby="inputPembelianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inputPembelianModalLabel">
                    <i class="mdi mdi-clipboard-text"></i>Input Pesanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPembelian">
                    <input type="hidden" id="editPembelianId">
                    
                    
                    <div class="alert alert-info border-0 d-flex align-items-center mb-3">
                        <i class="mdi mdi-information-outline fs-4 me-2"></i>
                        <div class="flex-grow-1">
                            Isi data otomatis dari SPH yang sudah diterima
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                         <div class="col-9">
                            <select class="form-select" id="selectSPH">
                                <option value="">-- Pilih SPH Diterima --</option>
                            </select>
                        </div>
                        <div class="col-3">
                             <button type="button" class="btn btn-primary w-100" onclick="loadSPHData()">
                                <i class="mdi mdi-download me-1"></i>Load
                            </button>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-title">Informasi Pesanan</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="noOrder" class="form-label">No. Order</label>
                                <input type="text" class="form-control" id="noOrder" placeholder="TRX-2025-001" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggalPembelian" class="form-label">Tanggal Pesanan</label>
                                <input type="date" class="form-control" id="tanggalPembelian" required>
                            </div>
                        </div>
                    </div>

                    
                    <div class="section-card">
                        <div class="section-title">Data Pelanggan</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="namaCustomer" class="form-label">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="namaCustomer" placeholder="Masukkan nama customer" required>
                            </div>
                            <div class="col-md-6">
                                <label for="noTelepon" class="form-label">No. Telepon</label>
                                <input type="tel" class="form-control" id="noTelepon" placeholder="08xx-xxxx-xxxx" required>
                            </div>
                            <div class="col-12">
                                <label for="alamatCustomer" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" id="alamatCustomer" rows="2" placeholder="Masukkan alamat lengkap customer" required></textarea>
                            </div>
                        </div>
                    </div>

                    
                    <div class="items-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title mb-0">Pilih Barang</div>
                            <button type="button" class="btn-add-item" id="btnTambahBarang">
                                <i class="mdi mdi-plus"></i>Tambah Barang
                            </button>
                        </div>
                        <div id="containerBarang">
                            
                            <div class="item-row" data-item="1">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <div class="item-label">Nama Barang</div>
                                        <select class="form-select select-barang" required>
                                            <option value="">Pilih barang...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="item-label">Harga Satuan</div>
                                        <input type="text" class="form-control harga-satuan" placeholder="Rp 0" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="item-label">Jumlah</div>
                                        <input type="number" class="form-control jumlah-barang" placeholder="1" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="item-label">Total</div>
                                        <input type="text" class="form-control total-item" placeholder="Rp 0" readonly>
                                    </div>
                                    <div class="col-md-1 text-center">
                                        <button type="button" class="btn-remove-item" onclick="hapusItem(this)" style="display:none;">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="total-section">
                        <span class="total-label">Total Keseluruhan:</span>
                        <span class="total-value" id="totalKeseluruhan">Rp 0</span>
                    </div>

                    
                    <div class="section-card">
                        <div class="section-title">Status Pesanan</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label d-block mb-2">Status Pengiriman</label>
                                <div class="radio-group">
                                    <div class="radio-item">
                                        <input type="radio" id="statusDikirim" name="statusPengiriman" value="dikirim">
                                        <label for="statusDikirim">Dikirim</label>
                                    </div>
                                    <div class="radio-item">
                                        <input type="radio" id="statusMenunggu" name="statusPengiriman" value="menunggu">
                                        <label for="statusMenunggu">Menunggu</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block mb-2">Status Pembayaran</label>
                                <div class="radio-group">
                                    <div class="radio-item">
                                        <input type="radio" id="statusLunas" name="statusPembayaran" value="lunas">
                                        <label for="statusLunas">Lunas</label>
                                    </div>
                                    <div class="radio-item">
                                        <input type="radio" id="statusCicil" name="statusPembayaran" value="cicilan">
                                        <label for="statusCicil">Cicilan</label>
                                    </div>
                                    <div class="radio-item">
                                        <input type="radio" id="statusBelumLunas" name="statusPembayaran" value="belum_lunas">
                                        <label for="statusBelumLunas">Belum Lunas</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div id="containerCicilan" class="mt-3 p-3 bg-light rounded-3 border" style="display: none;">
                            <h6 class="fw-semibold text-primary mb-3"><i class="mdi mdi-calculator me-1"></i>Kalkulator Cicilan</h6>
                            
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Deposit Pembayaran (DP)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="calcDeposit" placeholder="0" autocomplete="off">
                                </div>
                                <div class="form-text text-danger d-none" id="dpError">DP tidak boleh melebihi total pesanan.</div>
                            </div>

                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Tenor Cicilan</label>
                                <div class="row g-2">
                                    <div class="col-3">
                                        <input type="radio" class="btn-check" name="tenor" id="tenor3" value="3" autocomplete="off">
                                        <label class="btn btn-outline-secondary w-100 btn-sm" for="tenor3">3 Bln</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="radio" class="btn-check" name="tenor" id="tenor6" value="6" autocomplete="off" checked>
                                        <label class="btn btn-outline-primary w-100 btn-sm" for="tenor6">6 Bln</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="radio" class="btn-check" name="tenor" id="tenor9" value="9" autocomplete="off">
                                        <label class="btn btn-outline-secondary w-100 btn-sm" for="tenor9">9 Bln</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="radio" class="btn-check" name="tenor" id="tenor12" value="12" autocomplete="off">
                                        <label class="btn btn-outline-secondary w-100 btn-sm" for="tenor12">12 Bln</label>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                <div>
                                    <small class="text-muted d-block">Cicilan per bulan:</small>
                                    <span class="fw-bold text-primary fs-5" id="calcCicilanPerBulan">Rp 0</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Sisa Tagihan:</small>
                                    <span class="fw-semibold text-danger" id="calcSisaTagihan">Rp 0</span>
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
                <button type="button" class="btn btn-primary" onclick="simpanPesanan()">
                    <i class="mdi mdi-check me-1"></i>Simpan Pesanan
                </button>
            </div>
        </div>
    </div>
</div>
