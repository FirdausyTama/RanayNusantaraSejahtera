document.addEventListener("DOMContentLoaded", function () {
    const pathArray = window.location.pathname.split('/');
    const id = pathArray[pathArray.length - 1];

    if (id && !isNaN(id)) {
        loadStokData().then(() => {
            loadDetailInvoice(id);
        });

        const btnPrint = document.getElementById("btnPrintInvoice");
        if (btnPrint) {
            btnPrint.href = `/print-invoice/${id}`;
        }

        const btnUpdate = document.getElementById("btnUpdateInvoice");
        if (btnUpdate) {
            btnUpdate.addEventListener("click", function () {
                const form = document.getElementById("formEditInvoice");
                if (form.checkValidity()) {
                    updateInvoice(id);
                } else {
                    form.reportValidity();
                }
            });
        }
    } else {
        console.error("ID Invoice tidak valid");
        alert("ID Invoice tidak valid");
    }
});

const API_INVOICE = "http://127.0.0.1:8000/api/invoice";
const API_STOK = "http://127.0.0.1:8000/api/stoks";
let editInvoiceItemsData = [];
let stokData = [];

function getToken() {
    return localStorage.getItem("token");
}

function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function loadStokData() {
    const token = getToken();
    if (!token) return Promise.resolve();

    return fetch(API_STOK, {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        }
    })
        .then(res => res.json())
        .then(res => {
            stokData = res.data || res || [];
            console.log("Stok data loaded:", stokData);
        })
        .catch(err => {
            console.error("Error loading stok:", err);
        });
}

function loadDetailInvoice(id) {
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

    fetch(`${API_INVOICE}/${id}`, {
        method: "GET",
        headers: headers
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal memuat detail Invoice: " + res.status + " " + res.statusText);
            }
            return res.json();
        })
        .then(res => {
            console.log("Response Detail:", res);
            const data = res.data || res;
            renderDetailInvoice(data);
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Gagal memuat detail invoice! " + err.message);
        });
}

function renderDetailInvoice(data) {
    setText("nomor_invoice", data.nomor_invoice);
    setText("tanggal_invoice", formatDate(data.tanggal_invoice));
    setText("nama_perusahaan", data.nama_perusahaan || data.nama_penerima);
    setText("total_tagihan", formatRupiah(data.total_tagihan));
    setText("penandatangan", data.penandatangan);
    setText("created_at", data.created_at ? new Date(data.created_at).toLocaleString('id-ID') : '-');

    const statusEl = document.getElementById("status");
    if (statusEl) {
        let statusHtml = '';
        const status = (data.status || "").toLowerCase();
        if (status === 'lunas') {
            statusHtml = '<span class="badge bg-success-subtle text-success fw-semibold px-3 py-2">Lunas</span>';
        } else if (status === 'belum-lunas') {
            statusHtml = '<span class="badge bg-warning-subtle text-warning fw-semibold px-3 py-2">Belum Lunas</span>';
        } else if (status === 'dibatalkan') {
            statusHtml = '<span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-2">Dibatalkan</span>';
        } else {
            statusHtml = `<span class="badge bg-secondary-subtle text-secondary fw-semibold px-3 py-2">${data.status || '-'}</span>`;
        }
        statusEl.innerHTML = statusHtml;
    }


    const tbody = document.getElementById("items-tbody");
    if (tbody) {
        tbody.innerHTML = '';

        const items = data.items || data.detail_barang || [];

        if (items.length > 0) {
            items.forEach((item, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama_barang || item.nama || '-'}</td>
                        <td>${item.qty || item.jumlah || '-'}</td>
                        <td>${formatRupiah(item.harga_satuan || 0)}</td>
                        <td>${formatRupiah(item.subtotal || item.total || 0)}</td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            if (data.estimasi_ongkir && data.estimasi_ongkir > 0) {
                tbody.innerHTML += `
                    <tr>
                        <td></td>
                        <td class="text-end">Estimasi Ongkir</td>
                        <td>${data.berat_barang ? data.berat_barang + ' Kg' : '-'}</td>
                        <td>-</td>
                        <td>${formatRupiah(data.estimasi_ongkir)}</td>
                    </tr>
                `;
            }

            tbody.innerHTML += `
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">Total Tagihan</td>
                    <td>${formatRupiah(data.total_tagihan || 0)}</td>
                </tr>
            `;
        }
    }

    setVal("editTanggalInvoice", data.tanggal_invoice);
    setVal("editNamaPerusahaan", data.nama_perusahaan || data.nama_penerima);
    setVal("editStatus", data.status);
    setVal("editPenandatangan", data.penandatangan);
    setVal("editOngkir", data.estimasi_ongkir || 0);
    setVal("editBerat", data.berat_barang || "");

    editInvoiceItemsData = items || [];
    populateEditInvoiceItems();

    const ongkirInput = document.getElementById('editOngkir');
    if (ongkirInput) {
        const newOngkirInput = ongkirInput.cloneNode(true);
        ongkirInput.parentNode.replaceChild(newOngkirInput, ongkirInput);

        newOngkirInput.addEventListener('input', updateEditInvoiceTotal);
    }
}



function createItemDropdown(selectedValue = '') {
    let options = '<option value="">Pilih barang...</option>';
    stokData.forEach(item => {
        const selected = item.nama_barang === selectedValue ? 'selected' : '';
        options += `<option value="${item.nama_barang}" data-harga="${item.harga}" ${selected}>${item.nama_barang}</option>`;
    });
    return options;
}

function populateEditInvoiceItems() {
    const tbody = document.getElementById("editInvoiceItemsBody");
    if (!tbody) return;

    tbody.innerHTML = '';

    editInvoiceItemsData.forEach((item, index) => {
        addEditInvoiceItemRowWithData(item, index);
    });

    updateEditInvoiceTotal();
}

function addEditInvoiceItemRowWithData(item, index) {
    const tbody = document.getElementById("editInvoiceItemsBody");
    if (!tbody) return;

    const row = document.createElement('tr');

    const namaBarangCell = stokData.length > 0
        ? `<select class="form-select form-select-sm select-barang-edit" data-index="${index}" required>
                ${createItemDropdown(item.nama_barang || item.nama)}
           </select>`
        : `<input type="text" class="form-control form-control-sm" value="${item.nama_barang || item.nama || ''}" data-index="${index}" placeholder="Nama Barang" required>`;

    row.innerHTML = `
        <td>${namaBarangCell}</td>
        <td><input type="number" class="form-control form-control-sm qty-edit" value="${item.qty || item.jumlah || 1}" min="1" data-index="${index}" required></td>
        <td><input type="number" class="form-control form-control-sm harga-satuan-edit" value="${item.harga_satuan || 0}" min="0" data-index="${index}" required></td>
        <td><input type="text" class="form-control form-control-sm" value="${formatNumber(item.subtotal || item.total || 0)}" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeEditInvoiceItemRow(${index})"><i class="mdi mdi-delete"></i></button></td>
    `;

    tbody.appendChild(row);

    if (stokData.length > 0) {
        const selectEl = row.querySelector('.select-barang-edit');
        selectEl.addEventListener('change', function () {
            onInvoiceItemSelectChange(this);
        });
    } else {
        const inputEl = row.querySelector('input[type="text"]');
        inputEl.addEventListener('input', function () {
            if (editInvoiceItemsData[index]) {
                editInvoiceItemsData[index].nama_barang = this.value;
            }
        });
    }

    const qtyEl = row.querySelector('.qty-edit');
    const hargaEl = row.querySelector('.harga-satuan-edit');

    qtyEl.addEventListener('input', function () {
        onInvoiceItemQuantityChange(this);
    });

    hargaEl.addEventListener('input', function () {
        onInvoiceItemPriceChange(this);
    });
}

function onInvoiceItemSelectChange(selectElement) {
    const index = parseInt(selectElement.getAttribute('data-index'));
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const itemName = selectedOption.value;
    const itemHarga = selectedOption.getAttribute('data-harga');

    if (editInvoiceItemsData[index]) {
        editInvoiceItemsData[index].nama_barang = itemName;
        editInvoiceItemsData[index].harga_satuan = parseFloat(itemHarga) || 0;
        recalculateEditInvoiceItem(index);
    }
}

function onInvoiceItemQuantityChange(inputElement) {
    const index = parseInt(inputElement.getAttribute('data-index'));
    const value = parseFloat(inputElement.value) || 0;

    if (editInvoiceItemsData[index]) {
        editInvoiceItemsData[index].qty = value;
        recalculateEditInvoiceItem(index);
    }
}

function onInvoiceItemPriceChange(inputElement) {
    const index = parseInt(inputElement.getAttribute('data-index'));
    const value = parseFloat(inputElement.value) || 0;

    if (editInvoiceItemsData[index]) {
        editInvoiceItemsData[index].harga_satuan = value;
        recalculateEditInvoiceItem(index);
    }
}

function addEditInvoiceItemRow() {
    const newItem = { nama_barang: '', qty: 1, harga_satuan: 0, subtotal: 0 };
    editInvoiceItemsData.push(newItem);
    populateEditInvoiceItems();
}

function recalculateEditInvoiceItem(index) {
    if (editInvoiceItemsData[index]) {
        const qty = parseFloat(editInvoiceItemsData[index].qty) || 0;
        const hargaSatuan = parseFloat(editInvoiceItemsData[index].harga_satuan) || 0;
        editInvoiceItemsData[index].subtotal = qty * hargaSatuan;

        populateEditInvoiceItems();
    }
}

function removeEditInvoiceItemRow(index) {
    if (confirm('Hapus item ini?')) {
        editInvoiceItemsData.splice(index, 1);
        populateEditInvoiceItems();
    }
}

function updateEditInvoiceTotal() {
    const total = calculateInvoiceTotal();
    const display = document.getElementById("editInvoiceTotalDisplay");
    if (display) {
        display.textContent = formatNumber(total);
    }
}

function updateInvoice(id) {
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

    const data = {
        tanggal_invoice: document.getElementById("editTanggalInvoice").value,
        nama_perusahaan: document.getElementById("editNamaPerusahaan").value,
        status: document.getElementById("editStatus").value,
        penandatangan: document.getElementById("editPenandatangan").value,
        estimasi_ongkir: document.getElementById("editOngkir").value || 0,
        berat_barang: document.getElementById("editBerat").value || "",
        items: editInvoiceItemsData,
        total_tagihan: calculateInvoiceTotal()
    };

    console.log("Data to update:", data);

    fetch(`${API_INVOICE}/${id}`, {
        method: "PUT",
        headers: headers,
        body: JSON.stringify(data)
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal mengupdate Invoice");
            }
            return res.json();
        })
        .then(res => {
            console.log("Update Success:", res);
            alert("Invoice berhasil diupdate!");

            const modalEl = document.getElementById('modalEditInvoice');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            loadDetailInvoice(id);
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Gagal mengupdate Invoice: " + err.message);
        });
}

function calculateInvoiceTotal() {
    let total = 0;
    editInvoiceItemsData.forEach(item => {
        total += parseFloat(item.subtotal || item.total) || 0;
    });

    const ongkirVal = document.getElementById("editOngkir") ? parseFloat(document.getElementById("editOngkir").value) || 0 : 0;
    total += ongkirVal;

    return total;
}

function formatNumber(num) {
    return Number(num).toLocaleString('id-ID');
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value || "-";
}

function setVal(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value || "";
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