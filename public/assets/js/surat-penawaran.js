const API_SPH = "http://127.0.0.1:8000/api/surat-penawaran";

function getToken() {
    return localStorage.getItem("token");
}




// Safe helper to set value without crashing if element doesn't exist
function setValue(selector, value) {
    const el = document.querySelector(selector);
    if (el) {
        el.value = value ?? '';
    } else {
        console.warn('Element tidak ditemukan:', selector);
    }
}




let currentPage = 1;
const itemsPerPage = 10;
let allSPHData = [];


function loadSPH() {
    const token = getToken();
    if (!token) {
        console.error("Token tidak ditemukan!");
        return;
    }

    fetch(`${API_SPH}?t=${new Date().getTime()}`, {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        },
        mode: "cors"
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal memuat data SPH");
            }
            return res.json();
        })
        .then(res => {
            console.log("Data SPH berhasil dimuat:", res);
            allSPHData = res.data || res;

            // Sort by ID descending (Newest first)
            allSPHData.sort((a, b) => b.id - a.id);

            filterData();

            // ✅ SOLUSI FINAL (POSISI HALAMAN TETAP)
            const totalItems = filteredSPHData.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);

            // Jika halaman saat ini kosong (misal setelah hapus item), mundur 1 halaman
            if (currentPage > totalPages) {
                currentPage = totalPages || 1;
            }

            renderSPH(currentPage);
        })
        .catch(err => {
            console.error("Error:", err);
            const body = document.getElementById("sph-table-body");
            if (body) {
                body.innerHTML = `<tr><td colspan="7" class="text-center py-3 text-danger">Gagal memuat data SPH.</td></tr>`;
            }
        });
}


function formatTanggalIndonesia(dateString) {
    const bulan = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    const date = new Date(dateString);
    const day = date.getDate();
    const month = bulan[date.getMonth()];
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
}


function renderSPH(page = 1) {
    const body = document.getElementById("sph-table-body");
    if (!body) {
        console.error("Element sph-table-body tidak ditemukan!");
        return;
    }

    body.innerHTML = "";


    // Data to render is always filteredSPHData. 
    // filterData process ensures filteredSPHData is correct (even if empty).
    const dataToRender = filteredSPHData;

    if (!dataToRender || dataToRender.length === 0) {
        body.innerHTML = `<tr><td colspan="7" class="text-center py-3 text-muted">Tidak ada data SPH yang sesuai filter.</td></tr>`;
        renderPagination(0, 1, 0, 0, 0);
        return;
    }

    const totalItems = dataToRender.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = dataToRender.slice(startIndex, endIndex);

    let no = startIndex + 1;

    paginatedData.forEach(item => {

        const totalRaw = item.total_keseluruhan || 0;
        const total = parseInt(totalRaw.toString().replace(/\D/g, "")) || 0;


        const currentStatus = item.status || "Menunggu";


        let bgStyle = "";
        let textStyle = "";

        if (currentStatus === "Diterima") {
            bgStyle = "#d1fae5";
            textStyle = "#065f46";
        } else if (currentStatus === "Ditolak") {
            bgStyle = "#fee2e2";
            textStyle = "#991b1b";
        } else {
            bgStyle = "#fef3c7";
            textStyle = "#92400e";
        }

        body.innerHTML += `
        <tr>
            <td class="text-center">
                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input sph-checkbox" type="checkbox" value="${item.id}" onchange="updateBulkDeleteButton()">
                </div>
            </td>
            <td class="text-center">${no++}</td>
            <td><strong>${item.nomor_sph || "-"}</strong></td>
            <td class="text-center">${formatTanggalIndonesia(item.tanggal)}</td>
            <td>${item.nama_perusahaan || "-"}</td>
            <td class="text-center fw-semibold">
                Rp${total.toLocaleString("id-ID")}
            </td>
            <td class="text-center">
                <span class="badge bg-light text-dark border">${item.user ? item.user.name : '-'}</span>
            </td>
            <td class="text-center">
                <select class="form-select form-select-sm status-dropdown" 
                        data-id="${item.id}" 
                        onchange="updateStatusDropdown(this)"
                        style="
                            min-width: 140px; 
                            font-weight: 600; 
                            text-align: center; 
                            border: none; 
                            border-radius: 8px;
                            padding: 8px 12px;
                            background-color: ${bgStyle}; 
                            color: ${textStyle};
                            cursor: pointer;
                            appearance: none; /* Remove default arrow for cleaner look */
                            -webkit-appearance: none;
                        ">
                    <option value="Menunggu" ${currentStatus === 'Menunggu' ? 'selected' : ''} style="background: white; color: black;">⏳ Menunggu</option>
                    <option value="Diterima" ${currentStatus === 'Diterima' ? 'selected' : ''} style="background: white; color: black;">✅ Diterima</option>
                    <option value="Ditolak" ${currentStatus === 'Ditolak' ? 'selected' : ''} style="background: white; color: black;">❌ Ditolak</option>
                </select>
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    <!-- EDIT: Blue Soft -->
                    <button class="btn btn-sm" onclick="editSPH(${item.id})" title="Edit" 
                        style="background-color: #e0f2fe; color: #0284c7; border: none; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                        <i class="mdi mdi-square-edit-outline" style="font-size: 14px;"></i>
                    </button>
                    
                    <!-- PRINT: Dark/Black Soft -->
                    <button class="btn btn-sm" onclick="printSPH(${item.id})" title="Print" 
                        style="background-color: #f3f4f6; color: #1f2937; border: none; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                        <i class="mdi mdi-printer" style="font-size: 14px;"></i>
                    </button>

                    <!-- DELETE: Red Soft -->
                    <button class="btn btn-sm" onclick="deleteSPH(${item.id})" title="Hapus" 
                        style="background-color: #fee2e2; color: #dc2626; border: none; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                        <i class="mdi mdi-delete" style="font-size: 14px;"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    });

    renderPagination(totalPages, page, startIndex + 1, endIndex > totalItems ? totalItems : endIndex, totalItems);
}


function renderPagination(totalPages, currentPageNum, startItem, endItem, totalItems) {
    const paginationInfo = document.querySelector('.d-flex.justify-content-between.align-items-center.mt-3 small');
    const paginationNav = document.querySelector('.d-flex.justify-content-between.align-items-center.mt-3 nav ul');

    if (!paginationInfo || !paginationNav) return;

    if (totalItems === 0) {
        paginationInfo.textContent = 'Tidak ada data SPH';
    } else {
        paginationInfo.textContent = `Menampilkan ${startItem}–${endItem} dari ${totalItems} SPH`;
    }

    paginationNav.innerHTML = '';

    if (totalPages === 0) return;

    const prevDisabled = currentPageNum === 1 ? 'disabled' : '';
    paginationNav.innerHTML += `
        <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" onclick="changePage(${currentPageNum - 1}); return false;">‹</a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        const active = i === currentPageNum ? 'active' : '';
        paginationNav.innerHTML += `
            <li class="page-item ${active}">
                <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
            </li>
        `;
    }

    const nextDisabled = currentPageNum === totalPages ? 'disabled' : '';
    paginationNav.innerHTML += `
        <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#" onclick="changePage(${currentPageNum + 1}); return false;">›</a>
        </li>
    `;
}


function changePage(page) {
    const totalPages = Math.ceil(filteredSPHData.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderSPH(currentPage);
    document.getElementById('sph-table-body').scrollIntoView({ behavior: 'smooth', block: 'start' });
}


let filteredSPHData = [];


let currentFilter = 'Semua Waktu';
let currentStatusFilter = 'Semua Status'; // Added status filter
let currentSearch = '';

function setFilter(filter) {
    currentFilter = filter;
    const filterLabel = document.getElementById('selectedFilter');
    if (filterLabel) {
        filterLabel.innerText = filter;
    }
    currentPage = 1;
    applyFilter();
}

function setStatusFilter(status) {
    currentStatusFilter = status;
    const filterLabel = document.getElementById('selectedStatusFilter');
    if (filterLabel) {
        filterLabel.innerText = status;
    }
    currentPage = 1;
    applyFilter();
}

function searchSPH() {
    currentSearch = document.getElementById('searchInput')?.value?.toLowerCase() || '';
    currentPage = 1;
    applyFilter();
}

// ... (previous code)

function applyFilter() {
    filterData();
    currentPage = 1;
    renderSPH(currentPage);
}

function filterData() {
    filteredSPHData = allSPHData.filter(item => {
        // 1. Filter Waktu
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

        // 2. Filter Search
        let passSearch = true;
        if (currentSearch) {
            const nomorSph = (item.nomor_sph || '').toLowerCase();
            const namaPerusahaan = (item.nama_perusahaan || '').toLowerCase();
            passSearch = nomorSph.includes(currentSearch) || namaPerusahaan.includes(currentSearch);
        }

        // 3. Filter Status
        let passStatus = true;
        if (currentStatusFilter !== 'Semua Status') {
            passStatus = (item.status || 'Menunggu') === currentStatusFilter;
        }

        return passTime && passSearch && passStatus;
    });
}
// ... (rest of code)

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

window.searchSPH = searchSPH;
window.setFilter = setFilter;
window.setStatusFilter = setStatusFilter;


window.deleteSPH = function (id) {
    const sphData = allSPHData.find(item => item.id === id);
    const sphName = sphData?.nomor_sph || `ID: ${id}`;

    Swal.fire({
        title: 'Hapus Surat Penawaran?',
        html: `Anda akan menghapus SPH:<br><strong class="text-danger">${sphName}</strong><br><br>Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            executeDeleteSPH(id);
        }
    });
}



function executeDeleteSPH(id) {
    const token = getToken();
    if (!token) {
        alert("Token tidak ditemukan! Silakan login kembali.");
        return;
    }

    fetch(`${API_SPH}/${id}`, {
        method: "DELETE",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        },
        mode: "cors"
    })
        .then(async res => {
            if (!res.ok) throw new Error("Gagal menghapus SPH");
            return res.json();
        })
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'SPH berhasil dihapus!',
                timer: 1500,
                showConfirmButton: false
            });
            loadSPH();
        })
        .catch(err => {
            console.error("DELETE ERROR:", err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal menghapus SPH!'
            });
        });
}



window.printSPH = function (id) {
    window.location.href = '/print-sph/' + id;
}

// Edit SPH Function
window.editSPH = function (id) {
    const token = getToken();
    if (!token) {
        alert("Token tidak ditemukan! Silakan login kembali.");
        return;
    }

    // Fetch SPH data by ID
    fetch(`${API_SPH}/${id}`, {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        },
        mode: "cors"
    })
        .then(async res => {
            if (!res.ok) throw new Error("Gagal memuat data SPH");
            return res.json();
        })
        .then(response => {
            const data = response.data || response;
            console.log("Data SPH untuk edit:", data);

            // Set edit ID
            document.getElementById('sphEditId').value = id;

            // Change modal title
            document.getElementById('modalTambahSPHLabel').innerHTML =
                '<i class="mdi mdi-pencil me-2"></i>Edit Surat Penawaran Harga';

            // Populate form fields using safe setValue
            setValue('input[name="tanggal"]', data.tanggal);
            setValue('input[name="tempat"]', data.tempat || 'Banten');
            setValue('input[name="jabatan_tujuan"]', data.jabatan_tujuan || 'Direktur');
            setValue('input[name="nama_perusahaan"]', data.nama_perusahaan);
            setValue('textarea[name="alamat"]', data.alamat || '');
            setValue('select[name="penandatangan"]', data.penandatangan);


            // Populate rich text editor (keterangan)
            const editor = document.getElementById('editor');
            const keteranganInput = document.getElementById('keteranganInput');
            if (data.keterangan) {
                editor.innerHTML = data.keterangan;
                keteranganInput.value = data.keterangan;
            }

            // Clear existing items and populate with data
            const container = $('#itemContainer');
            // Keep the first row template, remove others
            container.find('.item-row').not(':first').remove();

            const firstRow = container.find('.item-row:first');

            if (data.detail_barang && data.detail_barang.length > 0) {
                // Populate items
                data.detail_barang.forEach((item, index) => {
                    let row;
                    if (index === 0) {
                        row = firstRow;
                    } else {
                        // Clone first row
                        row = firstRow.clone();
                        // Clear values
                        row.find('input').val('');
                        container.append(row);
                    }

                    // Set values
                    // Note: .trigger('change') is important if you have onchange listeners for price/calculation
                    row.find('.select-barang').val(item.nama).trigger('change');
                    row.find('.jumlah-barang').val(item.jumlah);

                    // If you have a custom price input that isn't readonly or needs setting:
                    // row.find('.harga-barang').val(item.harga); 

                    // Trigger calculation
                    row.find('.jumlah-barang').trigger('input');
                });
            } else {
                // Reset first row if empty
                firstRow.find('input').val('');
                firstRow.find('.select-barang').val('').trigger('change');
            }
            // Handle existing photos
            // Use global helper function from sph.blade.php to render existing photos in the legacy UI
            if (typeof window.populatePhotos === 'function') {
                window.populatePhotos(data.lampiran_gambar_urls || []);
            } else {
                console.warn("Fungsi populatePhotos tidak ditemukan");
            }

            // Open modal
            const modal = new bootstrap.Modal(document.getElementById('modalTambahSPH'));
            modal.show();
        })
        .catch(err => {
            console.error("Error fetching SPH:", err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal memuat data SPH untuk diedit!'
            });
        });
}

// Function to delete attachment
window.deleteLampiranSPH = function (id, btnElement) {
    const token = getToken();
    Swal.fire({
        title: 'Hapus Lampiran?',
        text: "Lampiran akan dihapus permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/surat-penawaran/lampiran/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    Swal.fire('Terhapus!', 'Lampiran berhasil dihapus.', 'success');
                    // Remove element from DOM
                    const colDiv = btnElement.closest('.col-6');
                    if (colDiv) colDiv.remove();

                    // If simplified to empty check
                    const photosContainer = document.getElementById('existingPhotosContainer');
                    if (photosContainer && photosContainer.children.length === 0) {
                        photosContainer.innerHTML = '<div class="col-12"><p class="text-muted small fst-italic py-2">Tidak ada foto lampiran.</p></div>';
                    }
                })
                .catch(err => {
                    console.error("Delete Error:", err);
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus lampiran.', 'error');
                });
        }
    });
};



document.addEventListener("DOMContentLoaded", function () {
    loadSPH();


    const tableBody = document.getElementById("sph-table-body");
    if (tableBody) {
        tableBody.addEventListener("click", function (e) {
            const target = e.target.closest("button");
            if (!target) return;


            const row = target.closest("tr");
            const deleteBtn = target.closest(".btn-danger");
            const printBtn = target.closest(".btn-primary");

            if (deleteBtn) {

                const onclickAttr = deleteBtn.getAttribute("onclick");
                const match = onclickAttr?.match(/deleteSPH\((\d+)\)/);
                if (match) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.deleteSPH(parseInt(match[1]));
                }
            }

            if (printBtn) {
                const onclickAttr = printBtn.getAttribute("onclick");
                const match = onclickAttr?.match(/printSPH\((\d+)\)/);
                if (match) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.printSPH(parseInt(match[1]));
                }
            }
        });
    }
});




window.updateStatusDropdown = function (selectElement) {
    const id = parseInt(selectElement.dataset.id);
    const newStatus = selectElement.value;
    const originalStatus = allSPHData.find(item => item.id === id)?.status || "Menunggu";

    console.log("UPDATE STATUS (API):", { id, status: newStatus, url: `${API_SPH}/${id}` });

    const token = getToken();
    if (!token) {
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak',
            text: 'Token tidak ditemukan! Silakan login kembali.'
        });
        selectElement.value = originalStatus;
        return;
    }


    Swal.fire({
        title: 'Ubah Status SPH?',
        text: `Anda akan mengubah status menjadi "${newStatus}".`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (!result.isConfirmed) {
            selectElement.value = originalStatus;
            return;
        }


        selectElement.disabled = true;
        selectElement.style.opacity = "0.6";


        fetch(`${API_SPH}/${id}`, {
            method: "PUT",
            headers: {
                "Authorization": "Bearer " + token,
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            mode: "cors",
            body: JSON.stringify({ status: newStatus })
        })
            .then(async res => {
                const text = await res.text();



                if (!res.ok) {
                    throw new Error(text || "Gagal mengupdate status SPH");
                }
                return text ? JSON.parse(text) : {};
            })
            .then(() => {

                const sphIndex = allSPHData.findIndex(item => item.id === id);
                if (sphIndex !== -1) {
                    allSPHData[sphIndex].status = newStatus;
                }


                if (newStatus === "Diterima") {
                    selectElement.style.backgroundColor = "#d1fae5";
                    selectElement.style.color = "#065f46";
                } else if (newStatus === "Ditolak") {
                    selectElement.style.backgroundColor = "#fee2e2";
                    selectElement.style.color = "#991b1b";
                } else {
                    selectElement.style.backgroundColor = "#fef3c7";
                    selectElement.style.color = "#92400e";
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Status SPH berhasil diperbarui',
                    timer: 1500,
                    showConfirmButton: false
                });

                console.log("Status berhasil diupdate ke database:", { id, status: newStatus });
            })
            .catch(err => {
                console.error("UPDATE ERROR:", err);
                selectElement.value = originalStatus;
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengupdate status SPH'
                });
            })
            .finally(() => {
                selectElement.disabled = false;
                selectElement.style.opacity = "1";
            });
    });
}


window.submitFormSPH = function (formElement) {
    const token = getToken();
    if (!token) {
        alert("Token tidak ditemukan! Silakan login kembali.");
        return;
    }

    // Check if we're in edit mode
    const editId = document.getElementById('sphEditId').value;
    const isEditMode = editId && editId !== '';

    // RE-CONSTRUCT FormData to ensure everything is correct specially the nested items
    const formData = new FormData();

    // Basic fields
    formData.append('tanggal', document.querySelector('[name="tanggal"]').value);
    formData.append('tempat', document.querySelector('[name="tempat"]').value);
    formData.append('lampiran', document.querySelector('[name="lampiran"]').value);
    formData.append('hal', document.querySelector('[name="hal"]').value);
    formData.append('jabatan_tujuan', document.querySelector('[name="jabatan_tujuan"]').value);
    formData.append('nama_perusahaan', document.querySelector('[name="nama_perusahaan"]').value);
    formData.append('alamat', document.querySelector('[name="alamat"]').value || '');
    formData.append('penandatangan', document.querySelector('[name="penandatangan"]').value);
    formData.append('keterangan', document.querySelector('[name="keterangan"]').value);

    // Only set default status if creating new SPH
    if (!isEditMode) {
        formData.append('status', 'Menunggu');
    }


    // Total
    const totalKeseluruhanInput = document.getElementById('totalKeseluruhanValue');
    formData.append('total_keseluruhan', totalKeseluruhanInput ? parseInt(totalKeseluruhanInput.value) : 0);

    // Items (Manual Construction to ensure index continuity)
    let itemIndex = 0;
    document.querySelectorAll("#itemContainer .item-row").forEach((row) => {
        const namaSelect = row.querySelector(".select-barang");
        const hargaInput = row.querySelector(".harga-satuan-value");
        const jumlahInput = row.querySelector(".jumlah-barang");
        const totalInput = row.querySelector(".total-item-value");

        const nama = namaSelect ? namaSelect.value : "";
        const jumlah = jumlahInput ? parseInt(jumlahInput.value) || 0 : 0;

        // Logic harga (same as before)
        let harga = hargaInput ? parseInt(hargaInput.value) || 0 : 0;
        if (harga === 0 && namaSelect) {
            const selectedOption = namaSelect.options[namaSelect.selectedIndex];
            harga = selectedOption ? parseInt(selectedOption.getAttribute('data-harga')) || 0 : 0;
        }

        // Logic total (same as before)
        let total = totalInput ? parseInt(totalInput.value) || 0 : 0;
        if (total === 0) {
            total = harga * jumlah;
        }

        if (nama && jumlah > 0) {
            // Append formatted for Laravel array validation
            formData.append(`detail_barang[${itemIndex}][nama]`, nama);
            formData.append(`detail_barang[${itemIndex}][harga_satuan]`, harga);
            formData.append(`detail_barang[${itemIndex}][jumlah]`, jumlah);
            formData.append(`detail_barang[${itemIndex}][total]`, total);
            itemIndex++;
        }
    });

    if (itemIndex === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Harap tambahkan minimal satu barang!'
        });
        return;
    }

    // Files (Fotos)
    const fileInputs = document.querySelectorAll('input[name="lampiran_gambar[]"]');
    fileInputs.forEach(input => {
        if (input.files.length > 0) {
            for (let i = 0; i < input.files.length; i++) {
                formData.append('lampiran_gambar[]', input.files[i]);
            }
        }
    });

    // For Laravel PUT method workaround
    if (isEditMode) {
        formData.append('_method', 'PUT');
    }

    console.log("Sending FormData...", isEditMode ? "UPDATE MODE" : "CREATE MODE");

    // Determine URL and method
    const url = isEditMode ? `${API_SPH}/${editId}` : API_SPH;
    const method = "POST"; // Always POST, but with _method=PUT for update

    // Fetch call with FormData
    fetch(url, {
        method: method,
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        },
        mode: "cors",
        body: formData
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("API Error Response:", text);
                let textParsed;
                try { textParsed = JSON.parse(text); } catch (e) { }
                throw new Error(textParsed?.message || text || `Gagal ${isEditMode ? 'mengupdate' : 'menyimpan'} SPH`);
            }
            return res.json();
        })
        .then((response) => {

            const modalEl = document.getElementById('modalTambahSPH');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            $('#modalTambahSPH').trigger('hidden.bs.modal');

            const nomorSPH = response?.data?.nomor_sph || response?.nomor_sph || "Baru";

            Swal.fire({
                icon: 'success',
                title: isEditMode ? 'SPH Berhasil Diperbarui!' : 'SPH Berhasil Dibuat!',
                html: `<span style="color:#6b7280;">Nomor Surat:</span><br><strong style="font-size:18px; color:#1f2937;">${nomorSPH}</strong>`,
                confirmButtonText: 'OK, Mengerti'
            });

            loadSPH();
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: `Gagal ${isEditMode ? 'mengupdate' : 'menambahkan'} SPH: ` + err.message
            });
        });
}

window.showSuccessModal = function (title, identifier) {
    const oldModal = document.getElementById('successModalOverlay');
    if (oldModal) oldModal.remove();


    const overlay = document.createElement('div');
    overlay.id = 'successModalOverlay';
    Object.assign(overlay.style, {
        position: 'fixed', top: '0', left: '0', right: '0', bottom: '0',
        background: 'rgba(0,0,0,0.5)', backdropFilter: 'blur(4px)',
        zIndex: '99999', display: 'flex', alignItems: 'center', justifyContent: 'center',
        animation: 'fadeIn 0.3s'
    });


    const content = document.createElement('div');
    Object.assign(content.style, {
        background: 'white', borderRadius: '16px', padding: '32px',
        maxWidth: '420px', width: '90%', boxShadow: '0 20px 60px rgba(0,0,0,0.3)',
        textAlign: 'center'
    });


    const iconContainer = document.createElement('div');
    iconContainer.innerHTML = `
        <svg class="success-checkmark" xmlns="http:
            <circle class="success-checkmark-circle" cx="26" cy="26" r="25" fill="none" stroke="#10b981" stroke-width="2"/>
            <path class="success-checkmark-check" fill="none" stroke="#10b981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
    `;


    const animationStyle = document.createElement('style');
    animationStyle.textContent = `
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        @keyframes circleAnimation {
            0% { stroke-dashoffset: 166; transform: rotate(0deg); }
            50% { stroke-dashoffset: 0; transform: rotate(180deg); }
            100% { stroke-dashoffset: 0; transform: rotate(360deg); }
        }
        @keyframes checkAnimation {
            0% { stroke-dashoffset: 48; }
            100% { stroke-dashoffset: 0; }
        }
        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-checkmark {
            animation: scaleIn 0.5s ease-out;
        }
        .success-checkmark-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            animation: circleAnimation 0.8s ease-out forwards;
            transform-origin: center;
        }
        .success-checkmark-check {
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: checkAnimation 0.4s ease-out 0.5s forwards;
        }
    `;
    overlay.appendChild(animationStyle);


    const header = document.createElement('h5');
    header.textContent = `${title} Berhasil Dibuat!`;
    Object.assign(header.style, {
        margin: '0 0 12px 0', fontSize: '22px', fontWeight: '600',
        color: '#065f46'
    });


    const sphNumber = document.createElement('div');
    sphNumber.innerHTML = `<span style="color:#6b7280;">Nomor Surat:</span><br><strong style="font-size:18px; color:#1f2937;">${identifier}</strong>`;
    Object.assign(sphNumber.style, {
        background: '#f0fdf4', padding: '16px', borderRadius: '12px',
        margin: '16px 0 24px 0', border: '1px solid #bbf7d0'
    });


    const btnOK = document.createElement('button');
    btnOK.textContent = 'OK, Mengerti';
    btnOK.type = 'button';
    Object.assign(btnOK.style, {
        padding: '14px 32px', border: 'none',
        background: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
        color: 'white', borderRadius: '10px',
        fontSize: '15px', fontWeight: '600', cursor: 'pointer', width: '100%'
    });
    btnOK.addEventListener('click', function (e) {
        e.preventDefault();
        overlay.remove();
    });


    content.appendChild(iconContainer);
    content.appendChild(header);
    content.appendChild(sphNumber);
    content.appendChild(btnOK);
    overlay.appendChild(content);


    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) overlay.remove();
    });

    document.body.appendChild(overlay);
}

// Reset modal when closed
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('modalTambahSPH');
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function () {
            // Clear edit ID
            document.getElementById('sphEditId').value = '';

            // Reset modal title
            document.getElementById('modalTambahSPHLabel').innerHTML =
                '<i class="mdi mdi-file-document-edit-outline me-2"></i>Tambah Surat Penawaran Harga';

            // Reset form
            document.getElementById('formTambahSPH').reset();

            // Clear rich text editor
            const editor = document.getElementById('editor');
            const keteranganInput = document.getElementById('keteranganInput');
            if (editor && keteranganInput) {
                editor.innerHTML = `Catatan :<br>
                    - Kondisi alat second layak pakai dan masih sangat bagus.<br>
                    - Harga sudah termasuk ongkir, Instal, Uji Fungsi, Uji Kesesuaian,
                    Uji Paparan Ruangan dan Perijinan<br>
                    - Garansi service X-Ray 3 Bulan, Garansi tidak berlaku, jika terjadi
                    keadaan memaksa (force majeure), yaitu keadaan di luar kemampuan
                    seperti bencana alam, konsleting listrik, banjir, kebakaran,
                    mobilisasi, pemogokan, blokade, revolusi, huru hara, sabotase<br>
                    - Cara pembayaran:<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;Pembayaran Pertama DP 50% Setelah PO atau
                    SPK kami terima<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;Pembayaran Ke Dua 50% Setelah Alat terinstal
                    dengan baik Pelunasan.<br><br>
                    Pembayaran Bisa Di Tranfer Melalui Rek Bank BSI (BANK SYARIAH
                    INDONESIA) :<br><br>
                    No Rek : 1101198975<br>
                    Atas Nama : PT RANAY NUSANTARA SEJAHTERA<br>
                    Kode bank : 451`;
                keteranganInput.value = editor.innerHTML;
            }

            // Clear items - remove all except first row and reset it
            $('#itemContainer .item-row').not(':first').remove();
            const firstRow = $('#itemContainer .item-row:first');
            firstRow.find('.select-barang').val('').trigger('change');
            firstRow.find('input[type="text"], input[type="number"]').val('');
        });
    }
});


// ==========================================
// BULK DELETE LOGIC
// ==========================================

function toggleSelectAllSPH(source) {
    const checkboxes = document.querySelectorAll('.sph-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const selectedCount = document.querySelectorAll('.sph-checkbox:checked').length;
    const btn = document.getElementById('btnBulkDelete');
    const countSpan = document.getElementById('selectedCount');
    const selectAllCb = document.getElementById('selectAllSPH');

    if (btn && countSpan) {
        countSpan.innerText = selectedCount;
        if (selectedCount > 0) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    }

    // Update "Select All" checkbox state based on individual selections
    if (selectAllCb) {
        const totalCheckboxes = document.querySelectorAll('.sph-checkbox').length;
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

function bulkDeleteSPH() {
    const selectedCheckboxes = document.querySelectorAll('.sph-checkbox:checked');
    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (ids.length === 0) return;

    Swal.fire({
        title: 'Hapus item terpilih?',
        html: `Anda akan menghapus <strong>${ids.length}</strong> data SPH.<br>Data yang dihapus tidak dapat dikembalikan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = getToken();

            fetch(`${API_SPH}/bulk-delete`, {
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

                    // Uncheck "Select All"
                    const selectAllCb = document.getElementById('selectAllSPH');
                    if (selectAllCb) {
                        selectAllCb.checked = false;
                        selectAllCb.indeterminate = false;
                    }
                    // Hide delete button
                    updateBulkDeleteButton();
                    // Reload data
                    loadSPH();
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