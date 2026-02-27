document.addEventListener("DOMContentLoaded", function () {
    loadSuratJalan();

    const btnSimpan = document.getElementById("btnSimpanSuratJalan");
    if (btnSimpan) {
        btnSimpan.addEventListener("click", submitFormSuratJalan);
    }

    const telpInput = document.getElementById("telpPenerima");
    if (telpInput) {
        telpInput.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "");
        });
    }
});


let allData = [];
let filteredData = [];
let currentPage = 1;
let itemsPerPage = 10;
let currentFilter = 'Semua Waktu';
let currentSearch = '';

const API_SURAT_JALAN = "/api/surat-jalan";
let kwitansiData = [];

function getToken() {
    return localStorage.getItem("token");
}

function setFilter(filter) {
    currentFilter = filter;
    document.getElementById('selectedFilter').innerText = filter;
    currentPage = 1;
    applyFilterAndRender();
}

function searchSuratJalan() {
    currentSearch = document.getElementById('searchInput').value;
    currentPage = 1;
    applyFilterAndRender();
}

function applyFilterAndRender() {

    filteredData = allData.filter(item => {

        let passTime = true;
        const itemDate = new Date(item.tanggal);
        const today = new Date();

        if (currentFilter === 'Hari Ini') {
            passTime = isSameDay(itemDate, today);
        } else if (currentFilter === 'Minggu Ini') {
            passTime = isSameWeek(itemDate, today);
        } else if (currentFilter === 'Bulan Ini') {
            passTime = isSameMonth(itemDate, today);
        }


        let passSearch = true;
        if (currentSearch) {
            const searchLower = currentSearch.toLowerCase();
            const no = (item.nomor_surat_jalan || '').toLowerCase();
            const pengirim = (item.nama_pengirim || '').toLowerCase();
            const penerima = (item.nama_penerima || '').toLowerCase();
            const barang = (item.nama_barang_jasa || '').toLowerCase();
            passSearch = no.includes(searchLower) || pengirim.includes(searchLower) || penerima.includes(searchLower) || barang.includes(searchLower);
        }

        return passTime && passSearch;
    });


    renderCurrentPage();
}

function renderCurrentPage() {
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = filteredData.slice(start, end);

    renderTable(pageData, start + 1);
    renderPagination();
}


function isSameDay(d1, d2) {
    return d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth() &&
        d1.getDate() === d2.getDate();
}

function isSameMonth(d1, d2) {
    return d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth();
}

function isSameWeek(d1, d2) {
    const oneDay = 24 * 60 * 60 * 1000;
    const diffDays = Math.round(Math.abs((d1 - d2) / oneDay));
    return diffDays <= 7;
}

function loadSuratJalan() {
    const token = getToken();
    const headers = {
        "Accept": "application/json"
    };
    if (token) {
        headers["Authorization"] = "Bearer " + token;
    }


    fetch(API_SURAT_JALAN, {
        method: "GET",
        headers: headers
    })
        .then(res => {
            if (!res.ok) throw new Error("Gagal memuat data surat jalan");
            return res.json();
        })
        .then(res => {
            const data = res.data || res;
            if (Array.isArray(data)) {
                allData = data;
            } else {
                allData = [];
            }
            applyFilterAndRender();
        })
        .catch(err => {
            console.error("Error:", err);
            const tbody = document.querySelector("#tabelSuratJalan tbody");
            if (tbody) tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Gagal memuat data!</td></tr>`;
        });
}

function renderTable(data, startNo = 1) {
    const tbody = document.querySelector("#tabelSuratJalan tbody");
    if (!tbody) return;

    tbody.innerHTML = "";
    let no = startNo;

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="text-center">Tidak ada data surat jalan</td></tr>`;
        return;
    }

    data.forEach(item => {
        tbody.innerHTML += `
        <tr>
            <td class="text-center">
                <input type="checkbox" class="form-check-input surat-jalan-checkbox" value="${item.id}" onclick="updateBulkDeleteButton()">
            </td>
            <td class="text-center">${no++}</td>
            <td class="text-center">${formatDate(item.tanggal)}</td>
            <td>${item.nama_pengirim || "-"}</td>
            <td>${item.nama_penerima || "-"}</td>
            <td>${item.alamat_penerima || "-"}</td>
            <td>${item.nama_barang_jasa || "-"}</td>
            <td class="text-center">${item.qty || "0"}</td>
            <td class="text-center">
                <span class="badge bg-light text-dark border">${item.user ? item.user.name : '-'}</span>
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    <a href="print-surat-jalan/${item.id}" class="btn btn-sm btn-light border" title="Print Surat Jalan">
                        <i class="mdi mdi-printer text-dark"></i>
                    </a>
                    <button class="btn btn-sm btn-light border" onclick="deleteSuratJalan(${item.id})" title="Hapus">
                        <i class="mdi mdi-delete text-danger"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    });
}

function renderPagination() {
    const container = document.getElementById('paginationContainer');
    const info = document.getElementById("paginationInfo");
    if (!container) return;

    container.innerHTML = '';

    const totalItems = filteredData.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const startItem = (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(startItem + itemsPerPage - 1, totalItems);

    if (info) {
        if (totalItems === 0) {
            info.innerText = `Menampilkan 0 surat jalan`;
        } else {
            info.innerText = `Menampilkan ${startItem}–${endItem} dari ${totalItems} surat jalan`;
        }
    }

    if (totalPages <= 1) return;


    const prevDisabled = currentPage === 1 ? 'disabled' : '';
    container.innerHTML += `
        <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" onclick="event.preventDefault(); changePage(${currentPage - 1})" aria-label="Previous">
                <i class="mdi mdi-chevron-left"></i>
            </a>
        </li>
    `;


    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            const active = i === currentPage ? 'active' : '';
            container.innerHTML += `
                <li class="page-item ${active}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); changePage(${i})">${i}</a>
                </li>
            `;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            container.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }


    const nextDisabled = currentPage === totalPages ? 'disabled' : '';
    container.innerHTML += `
        <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#" onclick="event.preventDefault(); changePage(${currentPage + 1})" aria-label="Next">
                <i class="mdi mdi-chevron-right"></i>
            </a>
        </li>
    `;
}

function changePage(page) {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderCurrentPage();
}

function submitFormSuratJalan() {
    const form = document.getElementById("formSuratJalan");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const telpPenerima = document.getElementById("telpPenerima").value;
    if (!/^\d+$/.test(telpPenerima)) {
        Swal.fire({
            icon: 'error',
            title: 'Input Tidak Valid',
            text: 'Nomor telepon harus berupa angka!',
        });
        return;
    }

    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });



    const token = getToken();
    const headers = {
        "Accept": "application/json",
        "Content-Type": "application/json"
    };
    if (token) {
        headers["Authorization"] = "Bearer " + token;
    }


    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        headers["X-CSRF-TOKEN"] = csrfToken;
    }

    fetch(API_SURAT_JALAN, {
        method: "POST",
        headers: headers,
        body: JSON.stringify(data)
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal menyimpan surat jalan");
            }
            return res.json();
        })
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Surat Jalan berhasil disimpan!',
                timer: 1500,
                showConfirmButton: false
            });
            const modalEl = document.getElementById('modalTambahSuratJalan');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            form.reset();
            loadSuratJalan();
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyimpan surat jalan! ' + err.message
            });
        });
}

function deleteSuratJalan(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data surat jalan akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = getToken();
            const headers = {
                "Accept": "application/json"
            };
            if (token) {
                headers["Authorization"] = "Bearer " + token;
            }


            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                headers["X-CSRF-TOKEN"] = csrfToken;
            }

            fetch(`${API_SURAT_JALAN}/${id}`, {
                method: "DELETE",
                headers: headers
            })
                .then(res => {
                    if (!res.ok) throw new Error("Gagal menghapus data");
                    return res.json();
                })
                .then(res => {
                    Swal.fire(
                        'Terhapus!',
                        'Data surat jalan berhasil dihapus.',
                        'success'
                    );
                    loadSuratJalan();
                })
                .catch(err => {
                    console.error("Error:", err);
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat menghapus data.',
                        'error'
                    );
                });
        }
    });
}

function formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

// ==========================================
// BULK DELETE LOGIC
// ==========================================

function toggleSelectAllSuratJalan(source) {
    const checkboxes = document.querySelectorAll('.surat-jalan-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const selectedCount = document.querySelectorAll('.surat-jalan-checkbox:checked').length;
    const btn = document.getElementById('btnBulkDelete');
    const countSpan = document.getElementById('selectedCount');
    const selectAllCb = document.getElementById('selectAllSuratJalan');

    if (btn && countSpan) {
        countSpan.innerText = selectedCount;
        if (selectedCount > 0) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    }

    if (selectAllCb) {
        const totalCheckboxes = document.querySelectorAll('.surat-jalan-checkbox').length;
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

function bulkDeleteSuratJalan() {
    const selectedCheckboxes = document.querySelectorAll('.surat-jalan-checkbox:checked');
    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (ids.length === 0) return;

    Swal.fire({
        title: 'Hapus data terpilih?',
        html: `Anda akan menghapus <strong>${ids.length}</strong> data surat jalan.<br>Data yang dihapus tidak dapat dikembalikan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = getToken();

            fetch(`${API_SURAT_JALAN}/bulk-delete`, {
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

                    const selectAllCb = document.getElementById('selectAllSuratJalan');
                    if (selectAllCb) {
                        selectAllCb.checked = false;
                        selectAllCb.indeterminate = false;
                    }
                    updateBulkDeleteButton();
                    loadSuratJalan();
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
