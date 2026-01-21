window.API_URL = window.API_URL || "http://127.0.0.1:8000/api/stoks";
const API_PEMBELIAN_URL = "http://127.0.0.1:8000/api/pembelians";


function getToken() {
    const token = localStorage.getItem("token");
    if (!token) console.error("Token tidak ditemukan!");
    return token;
}


document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("stok-table-body")) {
        loadStok();
        loadTotalTerjual();
        loadExpenditureStats(); // Fetch expenditure stats from API
    }
});

// Load stats pengeluaran real (db history)
async function loadExpenditureStats() {
    const token = getToken();
    if (!token) return;

    try {
        const res = await fetch("http://127.0.0.1:8000/api/stoks/expenditure-stats", {
            method: "GET",
            headers: {
                "Authorization": "Bearer " + token,
                "Accept": "application/json"
            }
        });

        if (!res.ok) throw new Error("Gagal load expenditure stats");
        const data = await res.json();

        // Update UI
        const elYearValue = document.getElementById("totalStokMasukYearValue");
        if (elYearValue) elYearValue.textContent = formatRupiah(data.year_expenditure || 0);

        const elMonthValue = document.getElementById("totalStokMasukMonthValue");
        if (elMonthValue) elMonthValue.textContent = formatRupiah(data.month_expenditure || 0);

        // Update Total Qty Masuk (Optional, if we want to show total items purchased)
        // const elTotalMasuk = document.getElementById("totalStokMasuk");
        // if (elTotalMasuk) elTotalMasuk.textContent = (data.year_qty || 0); 
        // Note: User request specific "total stok masuk per bln dan per thn" usually implies VALUE, but widget title says "Nilai Stok Masuk".
        // The widget "Total Stok Masuk" (top left) is usually just Qty of CURRENT stock or Total In history? 
        // Based on existing logic: `totalStokMasuk` was sum of CURRENT stock + Sold.
        // Let's keep `totalStokMasuk` as is (Total Volume handled), but update the VALUES to use history.

    } catch (err) {
        console.error("Error loading expenditure stats:", err);
    }
}


async function loadTotalTerjual() {
    const token = getToken();
    if (!token) return;

    try {
        const res = await fetch(API_PEMBELIAN_URL, {
            method: "GET",
            headers: {
                "Authorization": "Bearer " + token,
                "Accept": "application/json"
            }
        });

        if (!res.ok) throw new Error("Gagal memuat data pembelian");

        const data = await res.json();


        let totalTerjual = 0;
        if (Array.isArray(data)) {
            data.forEach(transaksi => {
                if (transaksi.items && Array.isArray(transaksi.items)) {
                    transaksi.items.forEach(item => {
                        totalTerjual += parseInt(item.jumlah) || 0;
                    });
                }
            });
        }


        const elmKeluar = document.getElementById("totalStokKeluar");
        const elmMasuk = document.getElementById("totalStokMasuk");

        if (elmKeluar) {
            elmKeluar.textContent = totalTerjual + " Pcs";
        }


        if (elmMasuk) {
            const totalMasuk = (window.currentTotalStock || 0) + totalTerjual;
            elmMasuk.textContent = totalMasuk + " Pcs";
        }

    } catch (err) {
        console.error("Gagal menghitung total terjual:", err);
    }
}



window.currentTotalStock = 0;

function updateSummary(data) {
    if (!Array.isArray(data)) data = [];

    const totalKeseluruhan = data.reduce((sum, item) => sum + Number(item.jumlah || 0), 0);
    window.currentTotalStock = totalKeseluruhan;

    const elmKeseluruhan = document.getElementById("totalStokKeseluruhan");
    if (elmKeseluruhan) elmKeseluruhan.textContent = totalKeseluruhan + " Pcs";

    // REMOVED: Client-side logic for Month/Year Value based on current stock.
    // Now handled by loadExpenditureStats() fetching from backend history.
}


let allStok = [];
let filteredStok = [];
let currentPage = 1;
const rowsPerPage = 5;


async function loadStok() {
    const body = document.getElementById("stok-table-body");
    if (!body) return;

    const token = getToken();


    try {

        const res = await fetch(window.API_URL, {
            method: "GET",
            headers: {
                Authorization: "Bearer " + token,
                Accept: "application/json",
            },
        });

        if (!res.ok) {
            throw new Error("API Failed");
        }

        const data = await res.json();
        allStok = data.data || [];
    } catch (err) { }
    filteredStok = [...allStok];
    renderTable(1);
    updateSummary(allStok);
    loadTotalTerjual();

    const paginationContainer = document.getElementById("pagination-container");
    if (paginationContainer) {
        paginationContainer.style.display =
            allStok.length > 0 ? "flex" : "none";
    }
}


function renderTable(page = 1) {
    const body = document.getElementById("stok-table-body");
    if (!body) return;

    currentPage = page;
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedItems = filteredStok.slice(start, end);

    body.innerHTML = "";

    if (paginatedItems.length === 0) {
        body.innerHTML = `<tr><td colspan="7" class="text-center py-3 text-muted">Tidak ada data stok.</td></tr>`;
        return;
    }

    paginatedItems.forEach((item) => {
        // Robust Image Logic
        let fotoPath = "assets/images/logo-sm.png";

        if (item.images && item.images.length > 0) {
            fotoPath = `http://127.0.0.1:8000/storage/${item.images[0].image_path}`;
        } else if (item.stok_fotos && item.stok_fotos.length > 0) {
            // Handle stok_fotos structure (could be objects or strings)
            const first = item.stok_fotos[0];
            const path = typeof first === 'object' ? (first.filename || first.foto) : first;
            fotoPath = `http://127.0.0.1:8000/storage/${path}`;
        } else if (item.foto) {
            fotoPath = `http://127.0.0.1:8000/storage/${item.foto}`;
        }

        const hargaNumber = Number(item.harga) || 0;
        const jumlahNumber = Number(item.jumlah) || 0;

        let badge = "";
        if (jumlahNumber >= 5)
            badge = `<span class="badge bg-success-subtle text-success fw-semibold px-3 py-2">Stok Aman</span>`;
        else if (jumlahNumber > 0)
            badge = `<span class="badge bg-warning-subtle text-warning fw-semibold px-3 py-2">Stok Menipis</span>`;
        else
            badge = `<span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-2">Stok Habis</span>`;

        body.innerHTML += `
        <tr>
            <td class="text-center">
                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input stok-checkbox" type="checkbox" value="${item.id}" onchange="updateBulkDeleteButton()">
                </div>
            </td>
            <td class="text-center">
                <img src="${fotoPath}"
                     class="rounded"
                     width="60" height="60"
                     style="object-fit: cover;"
                     onerror="this.onerror=null; this.src='assets/images/logo-sm.png';">
            </td>
            <td>
                <h6 class="fw-semibold mb-1 text-dark">${item.nama_barang || "-"
            }</h6>
                <small class="text-muted">${item.deskripsi || ""}</small>
            </td>
            <td class="text-center">${formatTanggal(item.tgl_masuk)}</td>
            <td class="text-center fw-semibold">Rp${hargaNumber.toLocaleString(
                "id-ID"
            )}</td>
            <td class="text-center">${badge}</td>
            <td class="text-center fw-semibold">${jumlahNumber} ${item.satuan || ""
            }</td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    <button class="btn btn-sm btn-light border" onclick="openEditModal(${item.id})" title="Edit">
                        <i class="mdi mdi-square-edit-outline text-primary"></i>
                    </button>
                    <button class="btn btn-sm btn-light border" onclick="openDetailModal(${item.id})" title="Detail">
                        <i class="mdi mdi-eye-outline text-info"></i>
                    </button>
                    <button class="btn btn-sm btn-light border" onclick="deleteStok(${item.id})" title="Hapus">
                        <i class="mdi mdi-delete text-danger"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    });

    setupPagination();
}


function setupPagination() {
    const paginationControls = document.getElementById("pagination-controls");
    const paginationInfo = document.getElementById("pagination-info");

    if (!paginationControls || !paginationInfo) return;

    const totalItems = filteredStok.length;
    const totalPages = Math.ceil(totalItems / rowsPerPage);


    const startItem =
        totalItems === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1;
    const endItem = Math.min(currentPage * rowsPerPage, totalItems);
    paginationInfo.innerText = `Menampilkan ${startItem}-${endItem} dari ${totalItems} transaksi`;

    paginationControls.innerHTML = "";

    if (totalPages <= 1) return;


    const prevLi = document.createElement("li");
    prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
    prevLi.innerHTML = `<a class="page-link" href="javascript:void(0);" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
    prevLi.onclick = () => {
        if (currentPage > 1) renderTable(currentPage - 1);
    };
    paginationControls.appendChild(prevLi);


    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement("li");
        li.className = `page-item ${currentPage === i ? "active" : ""}`;
        li.innerHTML = `<a class="page-link" href="javascript:void(0);">${i}</a>`;
        li.onclick = () => renderTable(i);
        paginationControls.appendChild(li);
    }


    const nextLi = document.createElement("li");
    nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""
        }`;
    nextLi.innerHTML = `<a class="page-link" href="javascript:void(0);" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
    nextLi.onclick = () => {
        if (currentPage < totalPages) renderTable(currentPage + 1);
    };
    paginationControls.appendChild(nextLi);
}


function searchProduct() {
    applyFilters();
}

// ==========================================
// BULK DELETE LOGIC
// ==========================================

function toggleSelectAllStok(source) {
    const checkboxes = document.querySelectorAll('.stok-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const selectedCount = document.querySelectorAll('.stok-checkbox:checked').length;
    const btn = document.getElementById('btnBulkDelete');
    const countSpan = document.getElementById('selectedCount');
    const selectAllCb = document.getElementById('selectAllStok');

    if (btn && countSpan) {
        countSpan.innerText = selectedCount;
        if (selectedCount > 0) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    }

    if (selectAllCb) {
        const totalCheckboxes = document.querySelectorAll('.stok-checkbox').length;
        if (totalCheckboxes > 0 && selectedCount === totalCheckboxes) {
            selectAllCb.checked = true;
            selectAllCb.indeterminate = false;
        } else if (selectedCount > 0) {
            selectAllCb.checked = false;
            selectAllCb.indeterminate = true;
        } else {
            selectAllCb.checked = false;
            selectAllCb.indeterminate = false;
        }
    }
}

function bulkDeleteStok() {
    const selectedCheckboxes = document.querySelectorAll('.stok-checkbox:checked');
    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (ids.length === 0) return;

    Swal.fire({
        title: 'Hapus item terpilih?',
        html: `Anda akan menghapus <strong>${ids.length}</strong> data stok.<br>Data yang dihapus tidak dapat dikembalikan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = getToken();

            fetch(`${API_URL}/bulk-delete`, {
                method: "POST",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ ids: ids })
            })
                .then(async res => {
                    if (!res.ok) {
                        const text = await res.text();
                        throw new Error(text);
                    }
                    return res.json();
                })
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Data terpilih berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    const selectAllCb = document.getElementById('selectAllStok');
                    if (selectAllCb) {
                        selectAllCb.checked = false;
                        selectAllCb.indeterminate = false;
                    }
                    updateBulkDeleteButton();
                    loadStok();
                })
                .catch(err => {
                    console.error("Error bulk delete:", err);
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat menghapus data.',
                        'error'
                    );
                });
        }
    });
}


function openDetailModal(id) {

    const apiUrl = `http://127.0.0.1:8000/api/stoks/${id}`;

    const modal = new bootstrap.Modal(
        document.getElementById("detailStokModal")
    );
    const contentDiv = document.getElementById("detailStokContent");


    contentDiv.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Memuat detail produk...</p>
        </div>
    `;

    modal.show();


    const token = localStorage.getItem("token");

    fetch(apiUrl, {
        headers: {
            Authorization: "Bearer " + token,
            Accept: "application/json",
        },
    })
        .then((res) => {
            if (!res.ok) throw new Error("Gagal memuat data");
            return res.json();
        })
        .then((response) => {
            if (response.data) {
                renderDetailStokModal(response.data, id);
            } else {
                throw new Error("Data tidak ditemukan");
            }
        })
        .catch((err) => {
            console.error(err);
            contentDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="mdi mdi-alert"></i> Terjadi kesalahan: ${err.message}
            </div>
        `;
        });
}


function renderDetailStokModal(data, id) {

    const storageBaseUrl = `http://127.0.0.1:8000/storage`;
    const contentDiv = document.getElementById("detailStokContent");

    let fotos = [];
    if (data.images && Array.isArray(data.images) && data.images.length > 0) {
        fotos = data.images.map(img => img.image_path);
    } else if (data.stok_fotos && Array.isArray(data.stok_fotos)) {
        fotos = data.stok_fotos.map(f => typeof f === 'object' ? (f.filename || f.foto) : f);
    } else if (data.fotos && Array.isArray(data.fotos)) {
        fotos = data.fotos;
    } else if (data.foto) {
        fotos = [data.foto];
    }

    const hasFoto = fotos.length > 0;

    const hasVideo = data.video ? true : false;
    const hasMedia = hasFoto || hasVideo;

    // Use first photo as main if available
    const fotoUrl = hasFoto ? `${storageBaseUrl}/${fotos[0]}` : "";
    const videoUrl = data.video ? `${storageBaseUrl}/${data.video}` : "";


    const styles = `
    <style>
        .product-image,
        .product-video {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            object-fit: cover;
            max-height: 300px;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-aman {
            background: #d1f2eb;
            color: #0f5132;
        }

        .badge-menipis {
            background: #fff3cd;
            color: #997404;
        }

        .badge-habis {
            background: #f8d7da;
            color: #842029;
        }

        .media-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .media-item {
            flex: 1;
            min-width: 100%;
        }
    </style>
    `;


    const getStatusBadgeHtml = (jumlah) => {
        jumlah = Number(jumlah) || 0;
        if (jumlah >= 5) return '<span class="status-badge badge-aman">Stok Aman</span>';
        if (jumlah > 0) return '<span class="status-badge badge-menipis">Stok Menipis</span>';
        return '<span class="status-badge badge-habis">Stok Habis</span>';
    };

    contentDiv.innerHTML = styles + `
        <div class="row">
            <!-- Kiri: Informasi Produk -->
            <div class="col-lg-8">
                <!-- Informasi Produk -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body p-3">
                        <h5 class="card-title mb-3 fs-16">Informasi Produk</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Nama Barang</small>
                                <div class="fw-medium text-truncate">${data.nama_barang || "Loading..."}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Kode SKU</small>
                                <div class="fw-medium">${data.kode_sku || "-"}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Merek</small>
                                <div class="fw-medium">${data.merek || "-"}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Tanggal Masuk</small>
                                <div class="fw-medium">${formatTanggal(data.tgl_masuk)}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Harga & Stok -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title fs-16 m-0">Harga & Stok</h5>
                            <div>${getStatusBadgeHtml(data.jumlah)}</div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Harga Jual</small>
                                <div class="fw-bold text-success">${formatRupiah(data.harga || 0)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Jumlah Stok</small>
                                <div class="fw-medium">${data.jumlah || 0} ${data.satuan || 'Pcs'}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dimensi & Berat -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body p-3">
                        <h5 class="card-title mb-3 fs-16">Dimensi & Berat</h5>
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <small class="text-muted d-block mb-1">Panjang</small>
                                <div class="fw-medium">${data.panjang || "-"} cm</div>
                            </div>
                            <div class="col-md-3 col-6">
                                <small class="text-muted d-block mb-1">Lebar</small>
                                <div class="fw-medium">${data.lebar || "-"} cm</div>
                            </div>
                            <div class="col-md-3 col-6">
                                <small class="text-muted d-block mb-1">Tinggi</small>
                                <div class="fw-medium">${data.tinggi || "-"} cm</div>
                            </div>
                            <div class="col-md-3 col-6">
                                <small class="text-muted d-block mb-1">Berat</small>
                                <div class="fw-medium">${data.berat || "-"} gr</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Media & Aksi -->
            <div class="col-lg-4">
                <!-- Media Produk -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Media Produk</h5>
                        <div class="media-gallery">
                            ${hasFoto ? `
                            <div class="media-item">
                                <small class="text-muted d-block mb-2">Foto Produk (${fotos.length})</small>
                                <div class="d-flex overflow-auto gap-2" style="max-width: 100%;">
                                    ${fotos.map(f => `
                                        <img src="${storageBaseUrl}/${f}" class="product-image" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                    `).join('')}
                                </div>
                                <div class="mt-2 text-center text-muted small"><i class="mdi mdi-information-outline"></i> Klik gambar untuk memperbesar</div>
                            </div>` : ''}
                            
                            ${hasVideo ? `
                            <div class="media-item">
                                <small class="text-muted d-block mb-2">Video Produk</small>
                                <video controls class="product-video">
                                    <source src="${videoUrl}" type="video/mp4">
                                </video>
                            </div>` : ''}

                            ${!hasMedia ? `
                            <div class="media-item text-center w-100">
                                <div class="border border-2 border-dashed rounded p-5 text-muted bg-light">
                                    <i class="mdi mdi-image-off fs-1 d-block mb-2"></i> Tidak ada media
                                </div>
                            </div>` : ''}
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
    `;
}


function formatRupiah(angka) {
    return "Rp " + Number(angka).toLocaleString("id-ID");
}

function formatRupiahInput(input) {
    let value = input.value.replace(/[^,\d]/g, "").toString();
    let split = value.split(",");
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }

    rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
    input.value = rupiah;
}

function formatTanggal(tanggal) {
    if (!tanggal) return "-";
    return new Date(tanggal).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

function getStatusBadge(jumlah) {
    jumlah = Number(jumlah) || 0;
    if (jumlah >= 5)
        return '<span class="badge bg-success-subtle text-success fw-semibold px-3 py-2">Stok Aman</span>';
    if (jumlah > 0)
        return '<span class="badge bg-warning-subtle text-warning fw-semibold px-3 py-2">Stok Menipis</span>';
    return '<span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-2">Stok Habis</span>';
}


function editStokFromModal(id) {
    const modalEl = document.getElementById("detailStokModal");
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    setTimeout(() => {
        openEditModal(id);
    }, 300);
}


function deleteStokFromModal(id) {
    const modalEl = document.getElementById("detailStokModal");
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    setTimeout(() => {
        deleteStok(id);
    }, 300);
}


async function loadStokSummary() {
    const token = getToken();
    if (!token) return;

    try {
        const res = await fetch("http://127.0.0.1:8000/api/stoks/summary", {
            method: "GET",
            headers: {
                Authorization: "Bearer " + token,
                Accept: "application/json",
            },
        });

        if (!res.ok) throw new Error("Gagal memuat summary stok");

        const data = await res.json();

        const elmMasuk = document.getElementById("totalStokMasuk");
        const elmKeluar = document.getElementById("totalStokKeluar");
        const elmKeseluruhan = document.getElementById("totalStokKeseluruhan");

        if (elmMasuk) elmMasuk.textContent = data.total_masuk + " Produk";

        if (elmKeseluruhan) elmKeseluruhan.textContent = data.total_keseluruhan;
    } catch (err) {
        console.error("Gagal memuat summary:", err);
    }
}

function increaseQty() {
    let input = document.getElementById("jumlah");
    input.value = parseInt(input.value) + 1;
}

function decreaseQty() {
    let input = document.getElementById("jumlah");
    let current = parseInt(input.value) || 0;
    // Allow stock to be 0 or negative
    input.value = Math.max(0, current - 1);
}

function handleVideoUpload(input) {
    const file = input.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            alert("Ukuran video maksimal 10MB!");
            input.value = "";
            return;
        }

        const validFormats = ["video/mp4", "video/avi", "video/quicktime"];
        if (!validFormats.includes(file.type)) {
            alert("Format video harus MP4, AVI, atau MOV!");
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById("videoElement").src = e.target.result;
            document.getElementById("videoFileNamePreview").textContent =
                file.name;
            document.getElementById("videoPlaceholder").style.display = "none";
            document.getElementById("videoPreview").classList.add("show");
        };
        reader.readAsDataURL(file);
    }
}

// Global array to store selected files for multiple upload
let selectedFotoFiles = [];

function handleFotoUpload(input) {
    const files = Array.from(input.files);
    const validFormats = ["image/jpeg", "image/png", "image/webp"];

    if (files.length === 0) return;

    files.forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
            alert(`File ${file.name} terlalu besar! Maksimal 5MB.`);
            return;
        }
        if (!validFormats.includes(file.type)) {
            alert(`Format file ${file.name} tidak valid! Gunakan JPG, PNG, atau WEBP.`);
            return;
        }

        // Add to global array
        selectedFotoFiles.push(file);
    });

    renderFotoPreviews();

    // Reset input value to allow selecting the same file again if needed (and to not hold state there)
    input.value = "";
}

function renderFotoPreviews() {
    const container = document.getElementById("fotoPreviewContainer");
    const placeholder = document.getElementById("fotoPlaceholder");

    if (selectedFotoFiles.length > 0) {
        placeholder.style.display = "none";
        container.style.display = "flex"; // Ensure it's treated as a row
        container.innerHTML = ""; // Clear current

        selectedFotoFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement("div");
                col.className = "col-4 position-relative";
                col.innerHTML = `
                    <div class="border rounded p-1" style="height: 100px; overflow: hidden; position: relative;">
                        <img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;" class="rounded">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 d-flex justify-content-center align-items-center" 
                                style="width: 20px; height: 20px; border-radius: 50%; font-size: 12px; margin: 2px;"
                                onclick="removeFotoByIndex(${index})">×</button>
                    </div>
                    <small class="d-block text-truncate mt-1" style="font-size: 10px;">${file.name}</small>
                `;
                container.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    } else {
        container.style.display = "none";
        placeholder.style.display = "block";
    }
}

function removeFotoByIndex(index) {
    selectedFotoFiles.splice(index, 1);
    renderFotoPreviews();
}

// Kept for backward compatibility if needed, though replaced
function removeFoto(event) {
    event.stopPropagation();
    selectedFotoFiles = [];
    renderFotoPreviews();
}



function handleEditVideoUpload(input) {
    const file = input.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            alert("Ukuran video maksimal 10MB!");
            input.value = "";
            return;
        }

        const validFormats = ["video/mp4", "video/avi", "video/quicktime"];
        if (!validFormats.includes(file.type)) {
            alert("Format video harus MP4, AVI, atau MOV!");
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById("editVideoElement").src = e.target.result;
            document.getElementById("editVideoName").textContent = file.name;
            document.getElementById("editVideoPlaceholder").style.display =
                "none";
            document.getElementById("editVideoPreview").style.display = "block";
        };
        reader.readAsDataURL(file);
    }
}

// Function to remove video in edit modal
function removeEditVideo(event) {
    event.stopPropagation();

    // Reset video input
    const videoInput = document.getElementById("editUploadVideo");
    if (videoInput) videoInput.value = "";

    // Clear video preview
    const videoElement = document.getElementById("editVideoElement");
    if (videoElement) videoElement.src = "";

    // Reset display
    const videoPreview = document.getElementById("editVideoPreview");
    if (videoPreview) videoPreview.style.display = "none";

    const videoPlaceholder = document.getElementById("editVideoPlaceholder");
    if (videoPlaceholder) videoPlaceholder.style.display = "block";

    const videoName = document.getElementById("editVideoName");
    if (videoName) videoName.textContent = "";
}

// Old functions removed to support multiple file upload (see end of file)

const modalTambahStok = document.getElementById("modalTambahStok");
if (modalTambahStok) {
    modalTambahStok.addEventListener("hidden.bs.modal", function () {
        document.getElementById("formTambahStok").reset();

        document.getElementById("videoElement").src = "";
        document.getElementById("videoFileNamePreview").textContent = "";
        document.getElementById("videoPreview").classList.remove("show");
        document.getElementById("videoPlaceholder").style.display = "block";

        document.getElementById("fotoElement").src = "";
        document.getElementById("fotoElement").src = "";
        document.getElementById("fotoFileNamePreview").textContent = "";
        // Reset multiple files
        selectedFotoFiles = [];
        const previewContainer = document.getElementById("fotoPreviewContainer");
        if (previewContainer) {
            previewContainer.innerHTML = "";
            previewContainer.style.display = "none";
        }
        document.getElementById("fotoPlaceholder").style.display = "block";
    });
}

var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

function formatTrend(percent) {
    if (percent > 0) {
        return `<i class="mdi mdi-arrow-up-bold text-success me-1"></i> +${percent}%`;
    } else if (percent < 0) {
        return `<i class="mdi mdi-arrow-down-bold text-danger me-1"></i> ${percent}%`;
    } else {
        return `<i class="mdi mdi-minus text-secondary me-1"></i> 0%`;
    }
}

async function loadWeeklySummary() {
    try {
        const res = await fetch(`${API_URL}/weekly-summary`);
        if (!res.ok) throw new Error("Gagal memuat summary mingguan");

        const data = await res.json();


        if (document.getElementById("totalStokMasuk7Hari")) {
            document.getElementById("totalStokMasuk7Hari").innerHTML =
                formatTrend(data.persen_masuk);
        }


        if (document.getElementById("totalStokKeluar7Hari")) {
            document.getElementById("totalStokKeluar7Hari").innerHTML =
                formatTrend(data.persen_keluar);
        }


        if (document.getElementById("totalKeseluruhanPersen")) {
            document.getElementById("totalKeseluruhanPersen").innerHTML =
                formatTrend(data.persen_total);
        }
    } catch (error) {
        console.error("Error:", error);
    }
}

async function getStokDetail(id) {
    const token = getToken();
    if (!token) return alertError("Token tidak ditemukan!");

    try {
        const response = await fetch(`${API_URL}/stoks/${id}`, {
            method: "GET",
            headers: {
                Authorization: "Bearer " + token,
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log("Detail Stok:", data);

        return data;
    } catch (error) {
        console.error("Gagal mengambil detail stok:", error);
        alertError("Gagal mengambil detail stok!");
    }
}

function deleteStok(id) {
    alertConfirmDelete(() => {
        const token = getToken();
        if (!token) return alertError("Token tidak ditemukan!");

        fetch(`${API_URL}/stoks/${id}`, {
            method: "DELETE",
            headers: {
                Authorization: "Bearer " + token,
                Accept: "application/json",
            },
        })
            .then(async (res) => {
                if (!res.ok) {
                    const text = await res.text();
                    console.error("RESPON ERROR:", text);
                    throw new Error("Gagal menghapus stok");
                }
                return res.json();
            })
            .then((res) => {
                alertSuccess(res.message || "Stok berhasil dihapus!");
                loadStok();
            })
            .catch((err) => {
                console.error("Error:", err);
                alertError("Gagal menghapus stok!");
            });
    });
}

async function openEditModal(id) {
    const token = getToken();
    if (!token) return alertError("Token tidak ditemukan!");

    try {
        const res = await fetch(`${API_URL}/stoks/${id}`, {
            method: "GET",
            headers: {
                Authorization: "Bearer " + token,
                Accept: "application/json",
            },
        });

        if (!res.ok) throw new Error("Gagal mengambil detail stok");

        const data = await res.json();

        document.getElementById("editId").value = data.data.id || "";
        document.getElementById("editNamaBarang").value =
            data.data.nama_barang || "";


        let harga = data.data.harga || "";
        if (harga) {
            harga = parseFloat(harga).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        document.getElementById("editHargaJual").value = harga;

        document.getElementById("editJumlah").value = data.data.jumlah || "";
        document.getElementById("editTglMasuk").value =
            data.data.tgl_masuk || "";
        document.getElementById("editSatuan").value = data.data.satuan || "";


        document.getElementById("editKodeSKU").value = data.data.kode_sku || "";
        document.getElementById("editMerek").value = data.data.merek || "";
        document.getElementById("editPanjang").value = data.data.panjang || "";
        document.getElementById("editLebar").value = data.data.lebar || "";
        document.getElementById("editTinggi").value = data.data.tinggi || "";
        document.getElementById("editBerat").value = data.data.berat || "";

        // Handling Fotos (Multiple)
        const editPreviewContainer = document.getElementById("editFotoPreviewContainer");
        const editPlaceholder = document.getElementById("editFotoPlaceholder");

        // Reset new files
        selectedEditFotoFiles = [];
        editPreviewContainer.innerHTML = "";

        // Load existing images from 'images' relation
        const existingImages = data.data.images || [];

        if (existingImages.length > 0 || data.data.foto) {
            editPlaceholder.style.display = "none";
            editPreviewContainer.style.display = "flex";

            // Render from 'images' relation
            if (existingImages.length > 0) {
                existingImages.forEach(img => {
                    const col = document.createElement("div");
                    col.className = "col-4 position-relative existing-image-item";
                    col.id = `existing-img-${img.id}`;
                    col.innerHTML = `
                        <div class="border rounded p-1" style="height: 100px; overflow: hidden; position: relative;">
                            <img src="http://127.0.0.1:8000/storage/${img.image_path}" style="width: 100%; height: 100%; object-fit: cover;" class="rounded">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 d-flex justify-content-center align-items-center" 
                                    style="width: 20px; height: 20px; border-radius: 50%; font-size: 12px; margin: 2px;"
                                    onclick="deleteStokImage(${img.id})" title="Hapus Foto">
                                <i class="mdi mdi-delete"></i>
                            </button>
                            <span class="badge bg-secondary position-absolute bottom-0 start-0 m-1" style="font-size: 10px;">Tersimpan</span>
                        </div>
                    `;
                    editPreviewContainer.appendChild(col);
                });
            } else if (data.data.foto) {
                // Fallback for singular foto
                const col = document.createElement("div");
                col.className = "col-4 position-relative existing-image-item";
                col.innerHTML = `
                    <div class="border rounded p-1" style="height: 100px; overflow: hidden; position: relative;">
                        <img src="http://127.0.0.1:8000/storage/${data.data.foto}" style="width: 100%; height: 100%; object-fit: cover;" class="rounded">
                        <span class="badge bg-secondary position-absolute bottom-0 start-0 m-1" style="font-size: 10px;">Tersimpan</span>
                    </div>
                `;
                editPreviewContainer.appendChild(col);
            }
        } else {
            editPlaceholder.style.display = "block";
            editPreviewContainer.style.display = "none";
        }

        // Hide legacy elements if they exist
        if (document.getElementById("editFotoPreview")) document.getElementById("editFotoPreview").style.display = "none";
        if (document.getElementById("editFotoElement")) document.getElementById("editFotoElement").src = "";

        if (data.data.video) {
            document.getElementById(
                "editVideoElement"
            ).src = `http://127.0.0.1:8000/storage/${data.data.video}`;
            document.getElementById("editVideoName").textContent =
                data.data.video.split("/").pop();
            document.getElementById("editVideoPreview").style.display = "block";
            document.getElementById("editVideoPlaceholder").style.display =
                "none";
        }

        const modal = new bootstrap.Modal(
            document.getElementById("modalEditStok")
        );
        modal.show();
    } catch (err) {
        console.error(err);
        alertError("Gagal memuat data untuk diedit");
    }
}

async function submitUpdateStok() {
    const token = getToken();
    if (!token) return alertError("Token tidak ditemukan!");

    const id = document.getElementById("editId").value;

    const formData = new FormData();
    formData.append(
        "nama_barang",
        document.getElementById("editNamaBarang").value
    );


    let harga = document.getElementById("editHargaJual").value;
    harga = harga.replace(/\./g, "");
    formData.append("harga", harga);

    formData.append("jumlah", document.getElementById("editJumlah").value);
    formData.append("tgl_masuk", document.getElementById("editTglMasuk").value);
    formData.append("user_id", 1);

    formData.append("kode_sku", document.getElementById("editKodeSKU").value);
    formData.append("merek", document.getElementById("editMerek").value);
    formData.append("satuan", document.getElementById("editSatuan").value);
    formData.append("panjang", document.getElementById("editPanjang").value);
    formData.append("lebar", document.getElementById("editLebar").value);
    formData.append("tinggi", document.getElementById("editTinggi").value);
    formData.append("berat", document.getElementById("editBerat").value);

    // [FIX] Multiple images upload for Edit
    if (selectedEditFotoFiles.length > 0) {
        // Send singular 'foto' for validation (using first new file)
        formData.append("foto", selectedEditFotoFiles[0]);

        selectedEditFotoFiles.forEach((file, index) => {
            formData.append("fotos[]", file);
        });
    }

    const video = document.getElementById("editUploadVideo")?.files[0];
    if (video) formData.append("video", video);

    try {
        const response = await fetch(`${API_URL}/stoks/${id}`, {
            method: "POST",
            headers: {
                "X-HTTP-Method-Override": "PUT",
                Authorization: "Bearer " + token,
                Accept: "application/json",
            },
            body: formData,
        });

        if (!response.ok) {
            const text = await response.text();
            console.log("Server Response:", text);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        alertSuccess("Stok berhasil diperbarui!");
        bootstrap.Modal.getInstance(
            document.getElementById("modalEditStok")
        ).hide();
        loadStok();
    } catch (err) {
        console.error(err);
        alertError("Gagal memperbarui stok!");
    }
}

function editIncreaseQty() {
    const input = document.getElementById("editJumlah");
    let current = parseInt(input.value) || 0;
    input.value = current + 1;
}

function editDecreaseQty() {
    const input = document.getElementById("editJumlah");
    let current = parseInt(input.value) || 0;
    // Allow stock to be 0 or negative  
    input.value = Math.max(0, current - 1);
}

document.addEventListener("DOMContentLoaded", () => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const dd = String(today.getDate()).padStart(2, "0");
    const todayStr = `${yyyy}-${mm}-${dd}`;
    const tglMasuk = document.getElementById("tgl_masuk");
    if (tglMasuk) {
        tglMasuk.value = todayStr;
        tglMasuk.max = todayStr;
    }

    const editTglMasuk = document.getElementById("editTglMasuk");
    if (editTglMasuk) {
        editTglMasuk.max = todayStr;
    }
});

function submitTambahStok() {
    const token = localStorage.getItem("token");
    if (!token) return alertError("Token tidak ditemukan!");

    const formData = new FormData();

    formData.append("nama_barang", document.getElementById("namaBarang").value);


    let harga = document.getElementById("hargaJual").value;
    harga = harga.replace(/\./g, "");
    formData.append("harga", harga);

    formData.append("jumlah", document.getElementById("jumlah").value);
    formData.append(
        "tgl_masuk",
        document.getElementById("tgl_masuk").value ||
        new Date().toISOString().split("T")[0]
    );
    formData.append("user_id", 1);

    formData.append(
        "kode_sku",
        document.getElementById("kodeSKU")?.value || ""
    );
    formData.append("merek", document.getElementById("merek")?.value || "");
    formData.append("satuan", document.getElementById("satuan")?.value || "");
    formData.append(
        "deskripsi",
        document.getElementById("deskripsi")?.value || ""
    );
    formData.append("panjang", document.getElementById("panjang")?.value || "");
    formData.append("lebar", document.getElementById("lebar")?.value || "");
    formData.append("tinggi", document.getElementById("tinggi")?.value || "");
    formData.append("berat", document.getElementById("berat")?.value || "");

    // [FIX] Multiple images upload
    if (selectedFotoFiles.length > 0) {
        // Send the first file as 'foto' (singular) to satisfy backend validation
        formData.append("foto", selectedFotoFiles[0]);

        // Send all files as 'foto[]' for multiple support
        selectedFotoFiles.forEach((file, index) => {
            formData.append("fotos[]", file);
        });
    }

    const video = document.getElementById("uploadVideo").files[0];
    if (video) formData.append("video", video);

    fetch("http://127.0.0.1:8000/api/stoks", {
        method: "POST",
        headers: {
            Authorization: "Bearer " + token,
            Accept: "application/json",
        },
        body: formData,
    })
        .then(async (res) => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal menambah stok");
            }
            return res.json();
        })
        .then((res) => {
            alertSuccess(res.message || "Stok berhasil ditambahkan!");

            document.getElementById("formTambahStok").reset();
            selectedFotoFiles = []; // Clear stored files
            document.getElementById("fotoPreviewContainer").innerHTML = "";
            document.getElementById("fotoPreviewContainer").style.display = "none";
            document.getElementById("fotoPlaceholder").style.display = "block";

            const modal = bootstrap.Modal.getInstance(
                document.getElementById("modalTambahStok")
            );
            modal.hide();

            setTimeout(() => {
                location.reload();
            }, 500);
        })
        .catch((err) => {
            console.error("Error:", err);
            alertError("Gagal menambah stok!");
        });
}

function formatDateToYYYYMMDD(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
}

document
    .getElementById("modalTambahStok")
    .addEventListener("show.bs.modal", function () {
        const today = new Date();
        document.getElementById("tgl_masuk").value =
            formatDateToYYYYMMDD(today);
    });

document
    .getElementById("modalEditStok")
    .addEventListener("show.bs.modal", function () {
        const today = new Date();
        const editTanggal = document.getElementById("editTglMasuk");

        if (!editTanggal.value) {
            editTanggal.value = formatDateToYYYYMMDD(today);
        }
    });

let activeTimeFilter = "Semua Waktu";
let activeStatusFilter = "Semua Status";

function setFilter(filterName) {
    activeTimeFilter = filterName;
    const filterLabel = document.getElementById("selectedFilter");
    if (filterLabel) {
        filterLabel.textContent = filterName;
    }
    applyFilters();
}

function setStockStatusFilter(status) {
    activeStatusFilter = status;
    const filterLabel = document.getElementById("selectedStatusFilter");
    if (filterLabel) {
        filterLabel.textContent = status;
    }
    applyFilters();
}

function applyFilters() {
    // 1. Get search term
    const input = document.getElementById("searchInput");
    const term = input ? input.value.toLowerCase() : "";

    // 2. Determine date range based on activeTimeFilter
    const now = new Date();
    let startDate, endDate;

    switch (activeTimeFilter) {
        case "Hari Ini":
            startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            endDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
            break;
        case "Minggu Ini":
            const day = now.getDay() || 7;
            const diffToMon = (now.getDay() + 6) % 7;
            const startOfWeek = new Date(now);
            startOfWeek.setDate(now.getDate() - diffToMon);
            startOfWeek.setHours(0, 0, 0, 0);

            startDate = startOfWeek;
            endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 7);
            break;
        case "Bulan Ini":
            startDate = new Date(now.getFullYear(), now.getMonth(), 1);
            endDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
            break;
        case "Semua Waktu":
        default:
            startDate = null;
            endDate = null;
    }

    // 3. Filter the data cumulatively
    filteredStok = allStok.filter((item) => {
        // A. Search Filter
        const matchesSearch =
            (item.nama_barang && item.nama_barang.toLowerCase().includes(term)) ||
            (item.kode_sku && item.kode_sku.toLowerCase().includes(term)) ||
            (item.merek && item.merek.toLowerCase().includes(term));

        if (!matchesSearch) return false;

        // B. Date Filter
        if (startDate && endDate) {
            if (!item.tgl_masuk) return false;
            const itemDate = new Date(item.tgl_masuk);
            if (itemDate < startDate || itemDate >= endDate) return false;
        }

        // C. Status Filter
        if (activeStatusFilter !== "Semua Status") {
            const jumlah = Number(item.jumlah) || 0;
            if (activeStatusFilter === "Stok Aman") {
                if (jumlah < 5) return false;
            } else if (activeStatusFilter === "Stok Menipis") {
                if (jumlah <= 0 || jumlah >= 5) return false;
            } else if (activeStatusFilter === "Stok Habis") {
                if (jumlah > 0) return false;
            }
        }

        return true;
    });

    renderTable(1);
}

function setFilter_OLD(filterName) {
    document.getElementById("selectedFilter").textContent = filterName;

    const now = new Date();
    let startDate, endDate;






    switch (filterName) {
        case "Hari Ini":
            startDate = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate()
            );
            endDate = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate() + 1
            );
            break;
        case "Minggu Ini":
            const day = now.getDay() || 7;
            if (day !== 1) now.setHours(-24 * (day - 1));
            startDate = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate()
            );
            endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 7);
            break;
        case "Bulan Ini":
            startDate = new Date(now.getFullYear(), now.getMonth(), 1);
            endDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
            break;
        case "Semua Waktu":
        default:
            startDate = null;
            endDate = null;
    }

    if (!startDate || !endDate) {
        filteredStok = [...allStok];
    } else {
        filteredStok = allStok.filter((item) => {
            if (!item.tgl_masuk) return false;
            const itemDate = new Date(item.tgl_masuk);
            return itemDate >= startDate && itemDate < endDate;
        });
    }

    renderTable(1);
}

// Global array for Edit Modal files
let selectedEditFotoFiles = [];

function handleEditFotoUpload(input) {
    const files = Array.from(input.files);
    const validFormats = ["image/jpeg", "image/png", "image/webp"];

    if (files.length === 0) return;

    files.forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
            alert(`File ${file.name} terlalu besar! Maksimal 5MB.`);
            return;
        }
        if (!validFormats.includes(file.type)) {
            alert(`Format file ${file.name} tidak valid! Gunakan JPG, PNG, atau WEBP.`);
            return;
        }
        selectedEditFotoFiles.push(file);
    });

    renderEditFotoPreviews();
    input.value = "";
}

function renderEditFotoPreviews() {
    const container = document.getElementById("editFotoPreviewContainer");
    const placeholder = document.getElementById("editFotoPlaceholder");

    // Preserve existing items
    const existingItems = [...container.querySelectorAll('.existing-image-item')];
    container.innerHTML = "";
    existingItems.forEach(item => container.appendChild(item));

    if (selectedEditFotoFiles.length > 0 || existingItems.length > 0) {
        placeholder.style.display = "none";
        container.style.display = "flex";

        selectedEditFotoFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement("div");
                col.className = "col-4 position-relative new-image-item";
                col.innerHTML = `
                    <div class="border rounded p-1" style="height: 100px; overflow: hidden; position: relative;">
                        <img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;" class="rounded">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 d-flex justify-content-center align-items-center" 
                                style="width: 20px; height: 20px; border-radius: 50%; font-size: 12px; margin: 2px;"
                                onclick="removeEditFotoByIndex(${index})">×</button>
                        <span class="badge bg-info position-absolute bottom-0 start-0 m-1" style="font-size: 10px;">Baru</span>
                    </div>
                    <small class="d-block text-truncate mt-1" style="font-size: 10px;">${file.name}</small>
                `;
                container.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    } else {
        container.style.display = "none";
        placeholder.style.display = "block";
    }
}

function removeEditFotoByIndex(index) {
    selectedEditFotoFiles.splice(index, 1);
    renderEditFotoPreviews();
}

function removeEditFoto(event) {
    event.stopPropagation();
    selectedEditFotoFiles = [];
    renderEditFotoPreviews();
}

function deleteStokImage(imageId) {
    if (!confirm("Apakah Anda yakin ingin menghapus foto ini secara permanen?")) return;

    const token = getToken();
    // Assuming endpoint: DELETE /api/stoks/images/{id}
    fetch(`${API_URL}/images/${imageId}`, {
        method: "DELETE",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        }
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                throw new Error(text || "Gagal hapus foto");
            }
            return res.json();
        })
        .then(res => {
            alertSuccess(res.message || "Foto berhasil dihapus");
            const el = document.getElementById(`existing-img-${imageId}`);
            if (el) el.remove();
            renderEditFotoPreviews(); // Check placeholder
        })
        .catch(err => {
            console.error(err);
            alertError("Gagal menghapus foto. " + err.message);
        });
}
