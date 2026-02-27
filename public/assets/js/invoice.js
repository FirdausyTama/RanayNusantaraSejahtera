document.addEventListener("DOMContentLoaded", function () {
    loadInvoice();
    loadPembelianList();
});

const API_INVOICE = "/api/invoice";
const API_PEMBELIAN_LIST = "/api/invoice/pembelian-list";

function getToken() {
    const token = localStorage.getItem("token");
    if (!token) console.error("Token tidak ditemukan!");
    return token;
}


let pembelianData = [];




function loadPembelianList() {
    const token = getToken();
    if (!token) return;

    fetch(API_PEMBELIAN_LIST, {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        }
    })
        .then(res => res.json())
        .then(data => {
            console.log("Pembelian List:", data);
            pembelianData = data;
            const select = $('#pembelianId'); // Use jQuery for Select2
            if (select.length) {
                select.empty();
                select.append('<option value="">-- Pilih Pembelian --</option>');

                const availablePurchases = data.filter(item =>
                    (item.status_pembayaran || '').toLowerCase() !== 'lunas'
                );

                availablePurchases.forEach(item => {
                    const tanggal = item.tgl_transaksi ? new Date(item.tgl_transaksi).toLocaleDateString('id-ID') : '-';
                    const nama = item.penerima_nama || item.nama_perusahaan || 'Tanpa Nama';
                    const noOrder = item.no_order || `Order #${item.id}`;
                    const items = item.items || item.detail_barang || [];
                    const totalItems = items.length;
                    select.append(`<option value="${item.id}">${noOrder} - ${nama} - ${tanggal} (${totalItems} item)</option>`);
                });

                // Initialize Select2
                select.select2({
                    theme: "bootstrap-5",
                    width: '100%',
                    dropdownParent: $('#modalTambahInvoice'),
                    placeholder: '-- Pilih Pembelian --',
                    allowClear: true
                });

                // Handle Change Event via Select2
                select.on('select2:select change', function () { // Listen to both just in case
                    const pembelianId = $(this).val();
                    if (pembelianId) {
                        const selectedPembelian = pembelianData.find(p => p.id == pembelianId);
                        if (selectedPembelian && selectedPembelian.items) {
                            autoFillItemsFromPembelian(selectedPembelian);
                        }
                    }
                });
            }
        })
        .catch(err => {
            console.error("Error loading pembelian list:", err);
        });
}

// Remove old vanilla event listener block as it's now handled inside loadPembelianList with Select2
// document.addEventListener("DOMContentLoaded", function () { ... });


function autoFillItemsFromPembelian(pembelian) {
    const container = document.getElementById('itemContainer');
    const placeholder = document.getElementById('itemPlaceholder');
    const tableContainer = document.getElementById('itemTableContainer');
    const namaPerusahaanInput = document.getElementById('namaPerusahaan');

    if (!container || !pembelian.items || pembelian.items.length === 0) return;


    if (placeholder) placeholder.style.display = 'none';
    if (tableContainer) tableContainer.style.display = 'block';


    if (namaPerusahaanInput) {
        const namaPerusahaan = pembelian.penerima_nama || pembelian.nama_perusahaan || '';
        namaPerusahaanInput.value = namaPerusahaan;
        namaPerusahaanInput.setAttribute('readonly', true);
    }


    container.innerHTML = '';


    pembelian.items.forEach((item, index) => {
        const harga = item.harga_satuan || item.harga || 0;
        const qty = item.jumlah || item.qty || 1;
        const subtotal = harga * qty;
        const formattedHarga = parseInt(harga).toLocaleString('id-ID');
        const formattedSubtotal = parseInt(subtotal).toLocaleString('id-ID');

        const row = `
            <tr class="item-row">
              <td class="text-center">${index + 1}</td>
              <td><input type="text" class="form-control form-control-sm" name="namaItem[]" value="${item.nama_barang || ''}" readonly></td>
              <td><input type="number" class="form-control form-control-sm qty-input" name="qty[]" value="${qty}" min="1" readonly></td>
              <td><input type="text" class="form-control form-control-sm harga-input" name="hargaSatuan[]" value="${formattedHarga}" readonly></td>
              <td><input type="text" class="form-control form-control-sm subtotal-input" name="subtotal[]" value="${formattedSubtotal}" readonly></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-secondary" disabled>
                  <i class="mdi mdi-lock"></i>
                </button>
              </td>
            </tr>`;
        container.innerHTML += row;
    });


    recalculateTotals();


    console.log(`Loaded ${pembelian.items.length} items from Pembelian ID: ${pembelian.id}`);
}



function recalculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.subtotal-input').forEach(input => {
        const value = parseInt(input.value.replace(/\D/g, '')) || 0;
        subtotal += value;
    });

    const subtotalInput = document.getElementById('subtotalInvoice');
    const totalInput = document.getElementById('totalInvoice');
    const ongkirInput = document.getElementById('estimasiOngkir');
    const gunakanOngkir = document.getElementById('gunakanOngkir');

    let ongkir = 0;
    if (gunakanOngkir && gunakanOngkir.checked && ongkirInput) {
        ongkir = parseInt(ongkirInput.value.replace(/\D/g, '')) || 0;
    }

    const grandTotal = subtotal + ongkir;

    if (subtotalInput) subtotalInput.value = 'Rp ' + subtotal.toLocaleString('id-ID');
    if (totalInput) totalInput.value = 'Rp ' + grandTotal.toLocaleString('id-ID');
}

let latestInvoiceNumber = 0;

function loadInvoice() {
    const token = getToken();
    if (!token) return;

    fetch(API_INVOICE, {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        }
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal memuat data Invoice");
            }
            return res.json();
        })
        .then(res => {
            console.log("Response dari API:", res);


            if (res && res.length > 0) {
                const numbers = res.map(item => {


                    const match = item.nomor_invoice.match(/(\d+)/);
                    return match ? parseInt(match[0]) : 0;
                });
                latestInvoiceNumber = Math.max(...numbers);
            } else {
                latestInvoiceNumber = 0;
            }

            allInvoiceData = res;
            filterData();
            renderPaginatedInvoice();
        })
        .catch(err => {
            console.error("Error:", err);
            const body = document.getElementById("tabelInvoice").getElementsByTagName("tbody")[0];
            body.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Gagal memuat data Invoice!</td></tr>`;
        });
}

function generateNextInvoiceNumber() {
    const nextNumber = latestInvoiceNumber + 1;
    const year = new Date().getFullYear();




    const paddedNumber = String(nextNumber).padStart(3, '0');
    return `INV/${paddedNumber}/RNS/${year}`;
}


document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById('modalTambahInvoice');
    if (modal) {
        modal.addEventListener('show.bs.modal', function () {
            const nextInvoice = generateNextInvoiceNumber();
            document.getElementById('nomorInvoice').value = nextInvoice;
        });
    }
});

function formatTanggalIndonesia(tanggal) {
    if (!tanggal) return "-";

    const bulanIndo = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    const date = new Date(tanggal);
    const hari = date.getDate();
    const bulan = bulanIndo[date.getMonth()];
    const tahun = date.getFullYear();

    return `${hari} ${bulan} ${tahun}`;
}


let currentPage = 1;
const itemsPerPage = 10;
let filteredInvoiceData = [];

let currentFilter = 'Semua Waktu';
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

function searchInvoice() {
    currentSearch = document.getElementById('searchInput')?.value?.toLowerCase() || '';
    currentPage = 1;
    applyFilter();
}

function applyFilter() {
    filterData();
    currentPage = 1;
    renderPaginatedInvoice();
}

function filterData() {
    filteredInvoiceData = allInvoiceData.filter(item => {
        // 1. Filter Waktu
        let passTime = true;

        // Note: invoice date field might be tanggal_invoice or tanggal. Check API response or existing code.
        // In existing renderPaginatedInvoice: item.tanggal_invoice || item.tanggal
        const dateStr = item.tanggal_invoice || item.tanggal;
        const itemDate = new Date(dateStr);
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
            const nomorInvoice = (item.nomor_invoice || '').toLowerCase();
            const namaPerusahaan = (item.nama_perusahaan || '').toLowerCase();
            // Also check for nama_penerima/penerima_nama if relevant
            const namaLain = (item.nama_penerima || item.penerima_nama || '').toLowerCase();
            passSearch = nomorInvoice.includes(currentSearch) || namaPerusahaan.includes(currentSearch) || namaLain.includes(currentSearch);
        }

        return passTime && passSearch;
    });
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

window.searchInvoice = searchInvoice;
window.setFilter = setFilter; // Expose to global scope for HTML onclick

// Removing old renderInvoice as it is replaced by logic in loadInvoice and applyFilter
// function renderInvoice(data) {
//     filteredInvoiceData = data || [];
//     currentPage = 1;
//     renderPaginatedInvoice();
// }


function renderPaginatedInvoice() {
    const body = document.getElementById("tabelInvoice").getElementsByTagName("tbody")[0];
    body.innerHTML = "";

    if (!filteredInvoiceData || filteredInvoiceData.length === 0) {
        body.innerHTML = `<tr><td colspan="7" class="text-center py-3 text-muted">Tidak ada data Invoice.</td></tr>`;
        updatePaginationInfo(0, 0, 0);
        renderPaginationControls(0);
        return;
    }


    const totalItems = filteredInvoiceData.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
    const paginatedData = filteredInvoiceData.slice(startIndex, endIndex);


    let no = startIndex + 1;
    paginatedData.forEach(item => {

        const nama = item.nama_perusahaan || item.nama_penerima || item.penerima_nama || "-";
        const total = item.total_pembayaran || item.total_tagihan || item.grand_total || item.total_harga || 0;

        body.innerHTML += `
        <tr>
            <td class="text-center">
                <input type="checkbox" class="form-check-input invoice-checkbox" value="${item.id}" onclick="updateBulkDeleteButton()">
            </td>
            <td class="text-center">${no++}</td>
            <td><strong>${item.nomor_invoice || "-"}</strong></td>
            <td class="text-center">${formatTanggalIndonesia(item.tanggal_invoice || item.tanggal)}</td>
            <td>${nama}</td>
            <td class="text-center fw-semibold">
                Rp${Number(total).toLocaleString("id-ID")}
            </td>
            <td class="text-center">
                <span class="badge bg-light text-dark border">${item.user ? item.user.name : '-'}</span>
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    <button class="btn btn-sm btn-light border" onclick="printInvoice(${item.id})" title="Print">
                        <i class="mdi mdi-printer text-dark"></i>
                    </button>
                    <button class="btn btn-sm btn-light border" onclick="deleteInvoice(${item.id})" title="Hapus">
                        <i class="mdi mdi-delete text-danger"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    });


    updatePaginationInfo(startIndex + 1, endIndex, totalItems);
    renderPaginationControls(totalPages);
}

function updatePaginationInfo(start, end, total) {
    const info = document.getElementById('paginationInfo');
    if (info) {
        if (total === 0) {
            info.textContent = 'Menampilkan 0 dari 0 invoice';
        } else {
            info.textContent = `Menampilkan ${start}–${end} dari ${total} invoice`;
        }
    }
}

function renderPaginationControls(totalPages) {
    const container = document.getElementById('paginationContainer');
    if (!container) return;

    container.innerHTML = '';


    const pages = totalPages || 1;


    container.innerHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${currentPage - 1}); return false;">‹</a>
        </li>
    `;


    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(pages, startPage + maxVisiblePages - 1);

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        container.innerHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a>
            </li>
        `;
    }


    container.innerHTML += `
        <li class="page-item ${currentPage === pages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${currentPage + 1}); return false;">›</a>
        </li>
    `;
}

function goToPage(page) {
    const totalPages = Math.ceil(filteredInvoiceData.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderPaginatedInvoice();
}






let allInvoiceData = [];


window.deleteInvoice = function (id) {
    const invoiceData = allInvoiceData.find(item => item.id === id);
    const invoiceName = invoiceData?.nomor_invoice || `ID: ${id}`;

    Swal.fire({
        title: 'Hapus Invoice?',
        html: `Anda akan menghapus Invoice:<br><strong class="text-danger">${invoiceName}</strong><br><br>Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            executeDeleteInvoice(id);
        }
    });
}


function executeDeleteInvoice(id) {
    const token = getToken();
    if (!token) {
        alert("Token tidak ditemukan! Silakan login kembali.");
        return;
    }

    fetch(`${API_INVOICE}/${id}`, {
        method: "DELETE",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        }
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal menghapus Invoice");
            }
            return res.json();
        })
        .then(res => {
            console.log("Invoice berhasil dihapus:", res);
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Invoice berhasil dihapus!',
                timer: 1500,
                showConfirmButton: false
            });
            loadInvoice();
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal menghapus Invoice!'
            });
        });
}







document.addEventListener("DOMContentLoaded", function () {
    const btnSimpan = document.getElementById('btnSimpanInvoice');
    if (btnSimpan) {
        btnSimpan.replaceWith(btnSimpan.cloneNode(true));
        document.getElementById('btnSimpanInvoice').addEventListener('click', submitFormInvoice);
    }
});

function submitFormInvoice() {
    const form = document.getElementById('formInvoice');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const token = getToken();
    if (!token) {
        alert("Token tidak ditemukan! Silakan login kembali.");
        return;
    }

    const items = [];
    const rows = document.querySelectorAll('#itemContainer tr');
    rows.forEach(row => {
        const namaInput = row.querySelector('input[name="namaItem[]"]');
        const qtyInput = row.querySelector('input[name="qty[]"]');
        const hargaInput = row.querySelector('.harga-input');
        const subtotalInput = row.querySelector('.subtotal-input');

        if (namaInput && qtyInput && hargaInput) {
            items.push({
                nama_barang: namaInput.value || '',
                qty: qtyInput.value || 0,
                harga_satuan: hargaInput.value?.replace(/\D/g, '') || 0,
                subtotal: subtotalInput?.value?.replace(/\D/g, '') || 0
            });
        }
    });


    const pembelianIdSelect = document.getElementById('pembelianId');
    const pembelianId = pembelianIdSelect?.value ? parseInt(pembelianIdSelect.value) : null;


    const data = {
        tanggal_invoice: document.getElementById('tanggalInvoice')?.value || null,
        nama_penerima: document.getElementById('namaPerusahaan')?.value || '',
        pembelian_id: pembelianId,


        items: pembelianId ? [] : items.map(item => ({
            nama_barang: item.nama_barang || '',
            qty: parseInt(item.qty) || 0,
            harga_satuan: parseInt(item.harga_satuan) || 0
        })),

        nomor_invoice: document.getElementById('nomorInvoice')?.value || '',
        nama_perusahaan: document.getElementById('namaPerusahaan')?.value || '',
        penandatangan: document.querySelector('select[name="penandatangan"]')?.value || 'Dewi Sulistiowati',
        berat_total: parseFloat(document.getElementById('beratBarang')?.value) || 0,
        harga_per_kg: parseInt(document.getElementById('hargaPerKg')?.value?.replace(/\D/g, '')) || 0,
        estimasi_ongkir: parseInt(document.getElementById('estimasiOngkir')?.value?.replace(/\D/g, '')) || 0,
        total_tagihan: parseInt(document.getElementById('totalInvoice')?.value?.replace(/\D/g, '')) || 0
    };

    console.log("Data Invoice to send:", data);

    fetch(API_INVOICE, {
        method: "POST",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json",
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal menyimpan Invoice");
            }
            return res.json();
        })
        .then(response => {
            console.log("Invoice berhasil disimpan:", response);

            const modalEl = document.getElementById('modalTambahInvoice');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();


            const nomorInvoice = response?.data?.nomor_invoice || response?.nomor_invoice || document.getElementById('nomorInvoice').value;

            Swal.fire({
                icon: 'success',
                title: 'Invoice Tersimpan',
                text: `Invoice ${nomorInvoice} berhasil disimpan!`,
                confirmButtonText: 'OK'
            });

            loadInvoice();
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal menyimpan Invoice!'
            });
        });
}



window.printInvoice = function (id) {
    window.location.href = '/print-invoice/' + id;
}


// searchInvoice is now defined above

// ==========================================
// BULK DELETE LOGIC
// ==========================================

function toggleSelectAllInvoice(source) {
    const checkboxes = document.querySelectorAll('.invoice-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const selectedCount = document.querySelectorAll('.invoice-checkbox:checked').length;
    const btn = document.getElementById('btnBulkDelete');
    const countSpan = document.getElementById('selectedCount');
    const selectAllCb = document.getElementById('selectAllInvoice');

    if (btn && countSpan) {
        countSpan.innerText = selectedCount;
        if (selectedCount > 0) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    }

    if (selectAllCb) {
        const totalCheckboxes = document.querySelectorAll('.invoice-checkbox').length;
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

function bulkDeleteInvoice() {
    const selectedCheckboxes = document.querySelectorAll('.invoice-checkbox:checked');
    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (ids.length === 0) return;

    Swal.fire({
        title: 'Hapus data terpilih?',
        html: `Anda akan menghapus <strong>${ids.length}</strong> data invoice.<br>Data yang dihapus tidak dapat dikembalikan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = getToken();

            fetch(`${API_INVOICE}/bulk-delete`, {
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

                    const selectAllCb = document.getElementById('selectAllInvoice');
                    if (selectAllCb) {
                        selectAllCb.checked = false;
                        selectAllCb.indeterminate = false;
                    }
                    updateBulkDeleteButton();
                    loadInvoice();
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
