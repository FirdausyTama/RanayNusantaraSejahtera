document.addEventListener("DOMContentLoaded", function () {
    loadKwitansi();

    // Chain loading to ensure Invoice data is ready before Pembelian list
    loadInvoices().then(() => {
        loadPembelianList();
    });


    const btnSimpan = document.getElementById("btnSimpanKwitansi");
    if (btnSimpan) {
        btnSimpan.addEventListener("click", function () {
            const form = document.getElementById("formKwitansi");
            if (form.checkValidity()) {
                const formData = new FormData(form);
                submitFormKwitansi(formData);
            } else {
                form.reportValidity();
            }
        });
    }


    // Event listener for modal trigger handled inside loadPembelianList now with Select2
});


let allData = [];
let pembelianData = [];
let filteredData = [];
let currentPage = 1;
let itemsPerPage = 10;
let currentFilter = 'Semua Waktu';
let currentSearch = '';

const API_KWITANSI = "http://127.0.0.1:8000/api/kwitansi";
const API_PEMBELIAN_LIST = "http://127.0.0.1:8000/api/pembelians";
const API_INVOICE = "http://127.0.0.1:8000/api/invoice";
let allInvoiceData = [];

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
            pembelianData = data;
            const select = $('#pembelianId'); // Use jQuery for Select2
            if (select.length) {
                select.empty();
                select.append('<option value="">-- Pilih Pembelian --</option>');

                // User requested to show ALL purchases regardless of status
                const availablePurchases = data;

                availablePurchases.forEach(item => {
                    const noOrder = item.no_order || `Order #${item.id}`;
                    const nama = item.penerima_nama || item.nama_perusahaan || 'Tanpa Nama';
                    const tanggal = item.tgl_transaksi ? new Date(item.tgl_transaksi).toLocaleDateString('id-ID') : '-';

                    // Check for associated invoice
                    const invoice = allInvoiceData.find(inv => inv.pembelian_id == item.id);
                    let displayAmount = item.grand_total;
                    let infoText = "";

                    if (invoice) {
                        displayAmount = invoice.total_pembayaran || invoice.total_tagihan || 0;
                        infoText = ` [INV: ${invoice.nomor_invoice}]`;
                    }

                    const amount = displayAmount ? `Rp ${Number(displayAmount).toLocaleString('id-ID')}` : '';

                    select.append(`<option value="${item.id}" data-has-invoice="${!!invoice}">${noOrder}${infoText} - ${nama} - ${tanggal} (${amount})</option>`);
                });

                // Initialize Select2
                select.select2({
                    theme: "bootstrap-5",
                    width: '100%',
                    dropdownParent: $('#modalTambahKwitansi'),
                    placeholder: '-- Pilih Pembelian --',
                    allowClear: true,
                    templateResult: function (data) {
                        if (!data.id) return data.text;
                        var $element = $(data.element);
                        var hasInvoice = $element.data('has-invoice');
                        if (hasInvoice) {
                            return $('<span>' + data.text + ' <span class="badge bg-success ms-1">Invoice</span></span>');
                        }
                        return data.text;
                    }
                });

                // Handle Change Event via Select2
                select.on('select2:select change', function () {
                    const pembelianId = $(this).val();
                    if (pembelianId) {
                        const selected = pembelianData.find(p => p.id == pembelianId);
                        if (selected) {
                            const nama = selected.penerima_nama || selected.nama_perusahaan || '';
                            const alamat = selected.penerima_alamat || selected.alamat_perusahaan || '';

                            // Cari data invoice yang sesuai dengan pembelian (pembelian_id)
                            const invoice = allInvoiceData.find(inv => inv.pembelian_id == pembelianId);

                            let total = 0;
                            let keteranganStr = "";

                            if (invoice) {
                                // Prioritize Invoice Amount
                                total = invoice.total_pembayaran || invoice.total_tagihan || invoice.grand_total || 0;
                                keteranganStr = `Pembayaran Invoice ${invoice.nomor_invoice}`;

                                // Override nama/alamat from invoice if available
                                if (invoice.nama_penerima) $('#namaPenerima').val(invoice.nama_penerima);
                                else $('#namaPenerima').val(nama);

                                // Alamat might not be in invoice payload fully, keep purchase address or check invoice
                                // Invoice model has nama_penerima but maybe not alamat_penerima explicitly unless added?
                                // Based on InvoiceController, it has 'nama_penerima' but 'alamat_penerima' not in top level of store payload
                                // So we keep purchase address
                                $('#alamatPenerima').val(alamat);

                            } else {
                                total = selected.grand_total || 0;
                                keteranganStr = `Pembayaran Pembelian ${selected.no_order || ''}`;
                                $('#namaPenerima').val(nama);
                                $('#alamatPenerima').val(alamat);
                            }

                            $('#totalPembayaran').val(parseInt(total).toLocaleString('id-ID'));
                            $('#keteranganPembayaran').val(keteranganStr);

                            // Trigger terbilang update logic manually if needed (not seen in snippet but good practice)
                            $('#totalBilangan').val(terbilang(total) + ' Rupiah');
                        }
                    } else {
                        $('#namaPenerima').val('');
                        $('#alamatPenerima').val('');
                        $('#totalPembayaran').val('');
                        $('#totalBilangan').val('');
                        $('#keteranganPembayaran').val('');
                    }
                });
            }
        })
        .catch(err => console.error("Error loading pembelian list:", err));
}

// Helper function for Terbilang (Simple version)
function terbilang(a) { a = Math.abs(a); var b = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"]; var c = ""; if (a < 12) { c = " " + b[a] } else if (a < 20) { c = terbilang(a - 10) + " Belas" } else if (a < 100) { c = terbilang(Math.floor(a / 10)) + " Puluh" + terbilang(a % 10) } else if (a < 200) { c = " Seratus" + terbilang(a - 100) } else if (a < 1e3) { c = terbilang(Math.floor(a / 100)) + " Ratus" + terbilang(a % 100) } else if (a < 2e3) { c = " Seribu" + terbilang(a - 1e3) } else if (a < 1e6) { c = terbilang(Math.floor(a / 1e3)) + " Ribu" + terbilang(a % 1e3) } else if (a < 1e9) { c = terbilang(Math.floor(a / 1e6)) + " Juta" + terbilang(a % 1e6) } else if (a < 1e12) { c = terbilang(Math.floor(a / 1e9)) + " Milyar" + terbilang(a % 1e9) } else if (a < 1e15) { c = terbilang(Math.floor(a / 1e12)) + " Triliun" + terbilang(a % 1e12) } return c }

function loadInvoices() {
    const token = getToken();
    if (!token) return Promise.reject("No Token"); // Return promise

    return fetch(API_INVOICE, {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        }
    })
        .then(res => res.json())
        .then(data => {
            allInvoiceData = data;
            return data; // Return data for chaining
        })
        .catch(err => console.error("Error loading invoice list:", err));
}

function setFilter(filter) {
    currentFilter = filter;
    document.getElementById('selectedFilter').innerText = filter;
    currentPage = 1;
    applyFilterAndRender();
}

function searchKwitansi() {
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
            const no = (item.nomor_kwitansi || '').toLowerCase();
            const nama = (item.nama_penerima || '').toLowerCase();
            const ket = (item.keterangan || '').toLowerCase();
            passSearch = no.includes(searchLower) || nama.includes(searchLower) || ket.includes(searchLower);
        }

        return passTime && passSearch;
    });


    renderCurrentPage();
}

function renderCurrentPage() {
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = filteredData.slice(start, end);

    renderKwitansi(pageData, start + 1);
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



function getToken() {
    return localStorage.getItem("token");
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

function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function loadKwitansi() {
    const token = getToken();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const headers = {
        "Accept": "application/json"
    };
    if (token) {
        headers["Authorization"] = "Bearer " + token;
    }
    if (csrfToken) {
        headers["X-CSRF-TOKEN"] = csrfToken;
    }



    const params = new URLSearchParams();
    params.append('per_page', 1000);

    fetch(`${API_KWITANSI}?${params.toString()}`, {
        method: "GET",
        headers: headers
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                throw new Error("Gagal memuat data Kwitansi");
            }
            return res.json();
        })
        .then(res => {
            console.log("Response dari API:", res);

            let data = [];
            if (Array.isArray(res)) {
                data = res;
            } else if (res.data && Array.isArray(res.data)) {
                data = res.data;
            }

            // Sort by ID descending (latest first)
            data.sort((a, b) => b.id - a.id);

            allData = data;

            applyFilterAndRender();
        })
        .catch(err => {
            console.error("Error:", err);
            const body = document.querySelector("#tabelKwitansi tbody");
            if (body) body.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Gagal memuat data Kwitansi!</td></tr>`;
        });
}

function renderKwitansi(data, startNo = 1) {
    const body = document.querySelector("#tabelKwitansi tbody");
    if (!body) return;

    body.innerHTML = "";

    if (!data || data.length === 0) {
        body.innerHTML = `<tr><td colspan="8" class="text-center py-3 text-muted">Tidak ada data Kwitansi.</td></tr>`;
        return;
    }

    let no = startNo;

    data.forEach(item => {
        body.innerHTML += `
        <tr>
            <td class="text-center">
                <input type="checkbox" class="form-check-input kwitansi-checkbox" value="${item.id}" onclick="updateBulkDeleteButton()">
            </td>
            <td class="text-center">${no++}</td>
            <td><strong>${item.nomor_kwitansi || "-"}</strong></td>
            <td class="text-center">${formatDate(item.tanggal)}</td>
            <td>${item.nama_penerima || "-"}</td>
            <td>${(item.keterangan || "-").replace(' [SIG:Dewi]', '').replace('[SIG:Dewi]', '')}</td>
            <td class="text-center fw-semibold">
                ${formatRupiah(item.total_pembayaran || 0)}
            </td>
            <td class="text-center">
                <span class="badge bg-light text-dark border">${item.user ? item.user.name : '-'}</span>
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    <a href="print-kwitansi/${item.id}" class="btn btn-sm btn-light border" title="Print Kwitansi">
                        <i class="mdi mdi-printer text-dark"></i>
                    </a>
                    <button class="btn btn-sm btn-light border" onclick="deleteKwitansi(${item.id})" title="Hapus">
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
            info.innerText = `Menampilkan 0 kwitansi`;
        } else {
            info.innerText = `Menampilkan ${startItem}–${endItem} dari ${totalItems} kwitansi`;
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

function submitFormKwitansi(formData) {
    const token = getToken();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const headers = {
        "Accept": "application/json",
        "Content-Type": "application/json"
    };
    if (token) {
        headers["Authorization"] = "Bearer " + token;
    }
    if (csrfToken) {
        headers["X-CSRF-TOKEN"] = csrfToken;
    }

    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });


    if (data.penandatangan && data.penandatangan.includes('Dewi')) {
        data.keterangan = (data.keterangan || '') + ' [SIG:Dewi]';
    }

    if (data.total_pembayaran) {
        data.total_pembayaran = data.total_pembayaran.replace(/\./g, '');
    }
    delete data.status;

    fetch(API_KWITANSI, {
        method: "POST",
        headers: headers,
        body: JSON.stringify(data)
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                try {
                    const json = JSON.parse(text);
                    if (json.message) throw new Error(json.message);
                } catch (e) { }
                throw new Error("Gagal menyimpan Kwitansi: " + text.substring(0, 100));
            }
            return res.json();
        })
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Kwitansi berhasil disimpan!',
                timer: 1500,
                showConfirmButton: false
            });

            const modalEl = document.getElementById('modalTambahKwitansi');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            document.getElementById("formKwitansi").reset();
            loadKwitansi();
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal menyimpan Kwitansi! ' + err.message
            });
        });
}

window.deleteKwitansi = function (id) {
    const item = allData.find(d => d.id === id);
    const nomor = item ? item.nomor_kwitansi : ("ID: " + id);

    Swal.fire({
        title: 'Hapus Kwitansi?',
        html: `Anda akan menghapus Kwitansi:<br><strong class="text-danger">${nomor}</strong><br><br>Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = getToken();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const headers = {
                "Accept": "application/json"
            };
            if (token) {
                headers["Authorization"] = "Bearer " + token;
            }
            if (csrfToken) {
                headers["X-CSRF-TOKEN"] = csrfToken;
            }

            fetch(`${API_KWITANSI}/${id}`, {
                method: "DELETE",
                headers: headers
            })
                .then(async res => {
                    if (!res.ok) {
                        const text = await res.text();
                        throw new Error("Gagal menghapus Kwitansi");
                    }
                    return res.json();
                })
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data kwitansi berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadKwitansi();
                })
                .catch(err => {
                    console.error("Error:", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menghapus data.'
                    });
                });
        }
    });
}

// ==========================================
// BULK DELETE LOGIC
// ==========================================

function toggleSelectAllKwitansi(source) {
    const checkboxes = document.querySelectorAll('.kwitansi-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const selectedCount = document.querySelectorAll('.kwitansi-checkbox:checked').length;
    const btn = document.getElementById('btnBulkDelete');
    const countSpan = document.getElementById('selectedCount');
    const selectAllCb = document.getElementById('selectAllKwitansi');

    if (btn && countSpan) {
        countSpan.innerText = selectedCount;
        if (selectedCount > 0) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    }

    if (selectAllCb) {
        const totalCheckboxes = document.querySelectorAll('.kwitansi-checkbox').length;
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

function bulkDeleteKwitansi() {
    const selectedCheckboxes = document.querySelectorAll('.kwitansi-checkbox:checked');
    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (ids.length === 0) return;

    Swal.fire({
        title: 'Hapus data terpilih?',
        html: `Anda akan menghapus <strong>${ids.length}</strong> data kwitansi.<br>Data yang dihapus tidak dapat dikembalikan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = getToken();

            fetch(`${API_KWITANSI}/bulk-delete`, {
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

                    const selectAllCb = document.getElementById('selectAllKwitansi');
                    if (selectAllCb) {
                        selectAllCb.checked = false;
                        selectAllCb.indeterminate = false;
                    }
                    updateBulkDeleteButton();
                    loadKwitansi();
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