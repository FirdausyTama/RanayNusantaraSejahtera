document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("inputPembelianModal");
    if (modal) {
        modal.addEventListener("shown.bs.modal", function () {
            const editId = document.getElementById("editPembelianId").value;
            if (!editId) {
                if (!document.getElementById("tanggalPembelian").value) {
                    const today = new Date().toISOString().split("T")[0];
                    document.getElementById("tanggalPembelian").value = today;
                }
                generateNoOrder();
            }
            loadBarangOptions();
            loadAcceptedSPH(); // Load accepted SPHs
        });
        modal.addEventListener("hidden.bs.modal", function () {
            resetForm();
            // Reset Select2
            $('#selectSPH').val(null).trigger('change');
            // Clean up dynamic select2 instances if any (optional)
        });
    }

    const containerBarang = document.getElementById("containerBarang");
    if (containerBarang) {
        containerBarang.addEventListener("change", function (e) {
            if (e.target.classList.contains("select-barang")) {
                updateHargaBarang(e.target);
            }
        });
        containerBarang.addEventListener("input", function (e) {
            if (e.target.classList.contains("jumlah-barang")) {
                hitungTotalItem(e.target);
            }
        });
    }

    const btnTambah = document.getElementById("btnTambahBarang");
    if (btnTambah) {
        btnTambah.addEventListener("click", tambahItemBaru);
    }

    const radiosStatus = document.getElementsByName("statusPembayaran");
    radiosStatus.forEach((radio) => {
        radio.addEventListener("change", toggleCicilan);
    });

    const tenorRadios = document.getElementsByName("tenor");
    tenorRadios.forEach((radio) => {
        radio.addEventListener("change", calculateInlineCicilan);
    });

    const inputDeposit = document.getElementById("calcDeposit");
    if (inputDeposit) {
        inputDeposit.addEventListener("keyup", function (e) {
            formatCurrencyInput(this);
            calculateInlineCicilan();
        });
    }

    const telpInput = document.getElementById("noTelepon");
    if (telpInput) {
        telpInput.addEventListener("input", function (e) {
            this.value = this.value.replace(/\D/g, "");
        });
    }
});

const API_PEMBELIAN_URL = "/api/pembelians";
const API_STOK_URL = "/api/stoks";
const API_CICILAN_URL = "/api/cicilan-pembelians";
let allPembelianData = [];
let baseData = [];
let filteredData = [];
let activeTimeFilter = "Semua Waktu";
let activeStatusFilter = "Semua Status";
let itemCounter = 1;

let globalBarangList = [];

let currentPage = 1;
let itemsPerPage = 10;
let acceptedSPHList = []; // Store SPH data globally

function getToken() {
    const token = localStorage.getItem("token");
    if (!token) console.error("Token tidak ditemukan!");
    return token;
}

function loadPembelian(status = null, excludeStatus = null) {
    const token = getToken();
    if (!token) return;

    let url = API_PEMBELIAN_URL;
    if (status) {
        url += `?status=${status}`;
    }

    fetch(url, {
        method: "GET",
        headers: {
            Authorization: "Bearer " + token,
            Accept: "application/json",
        },
    })
        .then(async (res) => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal memuat data pembelian");
            }
            return res.json();
        })
        .then((data) => {
            allPembelianData = [...data];
            updateSummary(allPembelianData);

            if (excludeStatus) {
                data = data.filter(
                    (item) => item.status_pembayaran !== excludeStatus
                );
            }

            data.sort((a, b) => b.no_order.localeCompare(a.no_order));

            baseData = data;
            applyFilters();
        })
        .catch((err) => {
            console.error("Error:", err);
            const body = document.getElementById("stok-table-body");
            if (body)
                body.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Gagal memuat data pembelian!</td></tr>`;
        });
}

function setFilter(period) {
    activeTimeFilter = period;
    const filterLabel = document.getElementById("selectedFilter");
    if (filterLabel) {
        filterLabel.textContent = period;
    }
    applyFilters();
}

function setStatusFilter(status) {
    activeStatusFilter = status;
    const filterLabel = document.getElementById("selectedStatusFilter");
    if (filterLabel) {
        filterLabel.textContent = status;
    }
    applyFilters();
}

function searchTransaction() {
    applyFilters();
}

function applyFilters() {
    if (!baseData) return;

    const searchInput = document.getElementById("searchInput");
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : "";
    const today = new Date();

    filteredData = baseData.filter((item) => {
        const itemDate = new Date(item.tgl_transaksi);

        let timeMatch = true;
        if (activeTimeFilter === "Hari Ini") {
            timeMatch = itemDate.toDateString() === today.toDateString();
        } else if (activeTimeFilter === "Minggu Ini") {
            const currentDay = today.getDay();
            const diffToMon = currentDay === 0 ? 6 : currentDay - 1;

            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - diffToMon);
            startOfWeek.setHours(0, 0, 0, 0);

            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            endOfWeek.setHours(23, 59, 59, 999);
            const d = new Date(itemDate);
            d.setHours(0, 0, 0, 0);
            const s = new Date(startOfWeek);
            s.setHours(0, 0, 0, 0);
            const e = new Date(endOfWeek);
            e.setHours(23, 59, 59, 999);
            timeMatch = d >= s && d <= e;
        } else if (activeTimeFilter === "Bulan Ini") {
            timeMatch =
                itemDate.getMonth() === today.getMonth() &&
                itemDate.getFullYear() === today.getFullYear();
        }

        let statusMatch = true;
        if (activeStatusFilter === "Belum Lunas") {
            statusMatch = item.status_pembayaran === "belum_lunas";
        } else if (activeStatusFilter === "Cicilan") {
            statusMatch = item.status_pembayaran === "cicilan";
        }

        const noOrder = item.no_order ? item.no_order.toLowerCase() : "";
        const nama = item.penerima_nama ? item.penerima_nama.toLowerCase() : "";
        const searchMatch =
            noOrder.includes(searchTerm) || nama.includes(searchTerm);

        return timeMatch && statusMatch && searchMatch;
    });

    currentPage = 1;
    renderPagination();
    renderPageData();
}

function renderPagination() {
    const totalItems = filteredData.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const paginationList = document.getElementById("paginationList");
    const paginationInfo = document.getElementById("paginationInfo");

    if (!paginationList || !paginationInfo) return;

    const startItem =
        totalItems === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(currentPage * itemsPerPage, totalItems);
    paginationInfo.textContent = `Menampilkan ${startItem}–${endItem} dari ${totalItems} transaksi`;

    let html = "";

    html += `<li class="page-item ${currentPage === 1 ? "disabled" : ""}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1
        }); return false;">‹</a>
             </li>`;

    for (let i = 1; i <= totalPages; i++) {
        if (
            i === 1 ||
            i === totalPages ||
            (i >= currentPage - 1 && i <= currentPage + 1)
        ) {
            html += `<li class="page-item ${i === currentPage ? "active" : ""}">

                        <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                     </li>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    html += `<li class="page-item ${currentPage === totalPages || totalPages === 0 ? "disabled" : ""
        }">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1
        }); return false;">›</a>

             </li>`;

    paginationList.innerHTML = html;
}

function changePage(page) {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;

    currentPage = page;
    renderPagination();
    renderPageData();
}

function renderPageData() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageData = filteredData.slice(startIndex, endIndex);

    renderPembelian(pageData, startIndex);
}

function renderPembelian(data, startIndex = 0) {
    const body = document.getElementById("stok-table-body");
    if (!body) return;

    body.innerHTML = "";

    if (!data || data.length === 0) {
        body.innerHTML = `<tr><td colspan="7" class="text-center py-3 text-muted">Tidak ada data pembelian.</td></tr>`;
        return;
    }

    data.forEach((item, index) => {
        let badgePembayaran = "";
        switch (item.status_pembayaran) {
            case "lunas":
                badgePembayaran = `<span class="badge bg-success-subtle text-success">Lunas</span>`;
                break;
            case "cicilan":
                badgePembayaran = `<span class="badge bg-warning-subtle text-warning">Cicilan</span>`;
                break;
            case "belum_lunas":
                badgePembayaran = `<span class="badge bg-danger-subtle text-danger">Belum Lunas</span>`;
                break;
        }

        const tanggal = new Date(item.tgl_transaksi).toLocaleDateString(
            "id-ID",
            {
                year: "numeric",
                month: "short",
                day: "numeric",
            }
        );

        body.innerHTML += `
            <tr>
                <td class="text-center">
                    <div class="form-check d-flex justify-content-center">
                        <input class="form-check-input pembelian-checkbox" type="checkbox" value="${item.id}" onchange="updateBulkDeleteButton()">
                    </div>
                </td>
                <td class="text-center fw-semibold">${startIndex + index + 1
            }</td>
                <td>
                    <h6 class="fw-semibold mb-1 text-dark">${item.no_order}</h6>
                </td>
                <td class="text-center">${tanggal}</td>
                <td>
                    <h6 class="fw-semibold mb-1">${item.penerima_nama}</h6>
                </td>
                <td class="text-center fw-semibold text-primary">
                    Rp ${Number(item.grand_total).toLocaleString("id-ID")}
                </td>
                <td class="text-center">${badgePembayaran}</td>
                <td class="text-center">
                    <span class="badge bg-light text-dark border">${item.user ? item.user.name : '-'}</span>
                </td>
                <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    <button class="btn btn-sm btn-light border" onclick="detailPembelian(${item.id
            })" title="Detail">
                        <i class="mdi mdi-eye-outline text-info"></i>
                    </button>
                    ${!document.title.includes("Riwayat")
                ? `
                    <button class="btn btn-sm btn-light border" onclick="editPembelian(${item.id})" title="Edit">
                        <i class="mdi mdi-square-edit-outline text-primary"></i>
                    </button>`
                : ""
            }
                    <button class="btn btn-sm btn-light border" onclick="deletePembelian(${item.id
            })" title="Hapus">
                        <i class="mdi mdi-delete text-danger"></i>
                    </button>
                </div>
                </td>
            </tr>
        `;
    });
}

function generateNoOrder() {
    const today = new Date();
    const year = today.getFullYear();
    const prefix = `TRX-${year}-`;

    const currentYearOrders = allPembelianData.filter(
        (item) => item.no_order && item.no_order.startsWith(prefix)
    );

    let maxSequence = 0;
    currentYearOrders.forEach((item) => {
        const parts = item.no_order.split("-");
        if (parts.length === 3) {
            const seq = parseInt(parts[2]);
            if (!isNaN(seq) && seq > maxSequence) {
                maxSequence = seq;
            }
        }
    });

    const nextSequence = (maxSequence + 1).toString().padStart(3, "0");
    const noOrder = `${prefix}${nextSequence}`;

    const inputNoOrder = document.getElementById("noOrder");
    if (inputNoOrder) inputNoOrder.value = noOrder;
}

function loadBarangOptions() {
    const token = getToken();
    if (!token) return;

    fetch(API_STOK_URL, {
        method: "GET",
        headers: {
            Authorization: "Bearer " + token,
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((res) => {
            globalBarangList = res.data || [];
            updateDropdownBarang(globalBarangList);
        })
        .catch((err) => console.error("Error loading barang:", err));
}


function updateDropdownBarang(barangList) {




    const dropdowns = document.querySelectorAll(".select-barang");
    dropdowns.forEach((dropdown) => {
        // Destroy existing Select2 if initialized to update options
        if ($(dropdown).hasClass("select2-hidden-accessible")) {
            $(dropdown).select2('destroy');
        }

        if (dropdown.classList.contains("preserve-options")) return;

        const currentVal = dropdown.value;
        const currentText = dropdown.options[dropdown.selectedIndex]
            ? dropdown.options[dropdown.selectedIndex].text
            : "";
        const currentPrice = dropdown.options[dropdown.selectedIndex]
            ? dropdown.options[dropdown.selectedIndex].getAttribute(
                "data-harga"
            )
            : "";
        const currentStock = dropdown.options[dropdown.selectedIndex]
            ? dropdown.options[dropdown.selectedIndex].getAttribute("data-stok")
            : "";

        dropdown.innerHTML = '<option value="">Pilih barang...</option>';

        if (barangList && barangList.length > 0) {
            barangList.forEach((barang) => {
                const price = barang.harga_jual || barang.harga;
                dropdown.innerHTML += `<option value="${barang.id}" data-harga="${price}" data-stok="${barang.jumlah}">${barang.nama_barang}</option>`;
            });
        }

        if (currentVal) {
            const exists = Array.from(dropdown.options).some(
                (opt) => opt.value == currentVal
            );

            if (!exists) {
                const opt = document.createElement("option");
                opt.value = currentVal;
                opt.text = currentText;
                if (currentPrice) opt.setAttribute("data-harga", currentPrice);
                if (currentStock) opt.setAttribute("data-stok", currentStock);

                opt.selected = true;
                dropdown.add(opt);
            }

            dropdown.value = currentVal;
        }

        // Re-init Select2
        initSelect2(dropdown);
    });
}

function tambahItemBaru(skipLoad = false) {
    itemCounter++;
    const container = document.getElementById("containerBarang");
    const newItem = document.createElement("div");
    newItem.className = "item-row";
    newItem.setAttribute("data-item", itemCounter);
    newItem.innerHTML = `
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
                <button type="button" class="btn-remove-item" onclick="hapusItem(this)">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    updateRemoveButtons();

    if (globalBarangList && globalBarangList.length > 0) {
        const select = newItem.querySelector(".select-barang");
        select.innerHTML = '<option value="">Pilih barang...</option>';
        globalBarangList.forEach((barang) => {
            select.innerHTML += `<option value="${barang.id}" data-harga="${barang.harga_jual || barang.harga
                }" data-stok="${barang.jumlah}">${barang.nama_barang}</option>`;
        });
    }

    if (!skipLoad && globalBarangList.length === 0) {
        loadBarangOptions();
    } else {
        // If not reloading options, existing select needs init
        const select = newItem.querySelector(".select-barang");
        initSelect2(select);
    }
}

function hapusItem(button) {
    const item = button.closest(".item-row");
    item.remove();
    hitungTotalKeseluruhan();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const items = document.querySelectorAll(".item-row");
    items.forEach((item) => {
        const btnHapus = item.querySelector(".btn-remove-item");
        if (items.length > 1) {
            btnHapus.style.display = "flex";
        } else {
            btnHapus.style.display = "none";
        }
    });
}

// Helper to init Select2 on a specific element
function initSelect2(element) {
    $(element).select2({
        dropdownParent: $('#inputPembelianModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih barang...'
    });

    // Bind change event manually because Select2 triggers jQuery change
    $(element).on('change', function () {
        // Trigger native change for vanilla listeners
        this.dispatchEvent(new Event('change', { bubbles: true }));
        updateHargaBarang(this);
    });
}

function updateSummary(data) {
    if (!Array.isArray(data)) data = [];

    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth();

    let totalYear = 0;
    let totalMonth = 0;
    let totalBelumLunas = 0;
    let totalCicilan = 0;
    let totalLunas = 0;

    data.forEach(item => {
        // Based on dashboard.js, there is `status_pembelian`.
        if (item.status_pembelian === 'batal') return;

        const date = new Date(item.tgl_transaksi);
        const year = date.getFullYear();
        const month = date.getMonth();
        const amount = parseFloat(item.grand_total || 0);
        const status = item.status_pembayaran;

        // Status Based Totals
        if (status === 'belum_lunas') {
            totalBelumLunas += amount;
        } else if (status === 'cicilan') {
            totalCicilan += amount;
        } else if (status === 'lunas') {
            totalLunas += amount;
        }

        // Time Based Totals (Only for valid sales? Or all?)
        // Usually dashboards show total regardless of payment status unless specified "Paid Sales".
        // Assuming Total Penjualan means Total Order Value being processed.
        if (year === currentYear) {
            totalYear += amount;
            if (month === currentMonth) {
                totalMonth += amount;
            }
        }
    });

    const elYear = document.getElementById("totalPenjualanYear");
    if (elYear) elYear.textContent = formatRupiah(totalYear);

    const elMonth = document.getElementById("totalPenjualanMonth");
    if (elMonth) elMonth.textContent = formatRupiah(totalMonth);

    const elBelumLunas = document.getElementById("totalPenjualanBelumLunas");
    if (elBelumLunas) elBelumLunas.textContent = formatRupiah(totalBelumLunas);

    const elCicilan = document.getElementById("totalPenjualanCicilan");
    if (elCicilan) elCicilan.textContent = formatRupiah(totalCicilan);

    const elLunas = document.getElementById("totalPenjualanLunas");
    if (elLunas) elLunas.textContent = formatRupiah(totalLunas);
}

function formatRupiah(angka) {
    return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function updateHargaBarang(select) {
    const row = select.closest(".item-row");
    const selectedOption = select.options[select.selectedIndex];
    const harga = selectedOption.getAttribute("data-harga") || 0;
    const hargaSatuan = row.querySelector(".harga-satuan");
    const jumlah = row.querySelector(".jumlah-barang");
    const totalItem = row.querySelector(".total-item");

    hargaSatuan.value = formatRupiah(parseInt(harga));

    const total = parseInt(harga) * parseInt(jumlah.value || 1);
    totalItem.value = formatRupiah(total);

    hitungTotalKeseluruhan();
}

function hitungTotalItem(input) {
    const row = input.closest(".item-row");
    const select = row.querySelector(".select-barang");
    const selectedOption = select.options[select.selectedIndex];
    const harga = parseInt(selectedOption.getAttribute("data-harga") || 0);
    const jumlah = parseInt(input.value) || 0;
    const totalItem = row.querySelector(".total-item");

    const total = harga * jumlah;
    totalItem.value = formatRupiah(total);

    hitungTotalKeseluruhan();
}

function hitungTotalKeseluruhan() {
    let totalSemua = 0;
    document.querySelectorAll(".item-row").forEach((row) => {
        const select = row.querySelector(".select-barang");
        if (select && select.selectedIndex >= 0) {
            const selectedOption = select.options[select.selectedIndex];
            const jumlah =
                parseInt(row.querySelector(".jumlah-barang").value) || 0;
            const harga = parseInt(
                selectedOption.getAttribute("data-harga") || 0
            );
            totalSemua += harga * jumlah;
        }
    });

    const elTotal = document.getElementById("totalKeseluruhan");

    if (elTotal) {
        elTotal.textContent = formatRupiah(totalSemua);
    }

    setTimeout(() => {
        calculateInlineCicilan();
    }, 100);
}

function simpanPesanan() {
    const form = document.getElementById("formPembelian");

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Validasi No Telepon
    const noTelepon = document.getElementById("noTelepon").value;
    if (!/^[0-9]+$/.test(noTelepon)) {
        Swal.fire({
            icon: 'error',
            title: 'Input Tidak Valid',
            text: 'Nomor telepon harus berupa angka!',
        });
        return;
    }

    const token = getToken();
    if (!token) return;

    const barangDipilih = document.querySelectorAll(".select-barang");

    let items = [];
    let stockItemsToUpdate = [];

    barangDipilih.forEach((select) => {
        if (select.value) {
            const row = select.closest(".item-row");
            const selectedOption = select.options[select.selectedIndex];
            const harga = parseFloat(selectedOption.getAttribute("data-harga"));
            const jumlah = parseInt(row.querySelector(".jumlah-barang").value);

            items.push({
                nama_barang: selectedOption.text,
                jumlah: jumlah,
                harga_satuan: harga,
                total_harga: harga * jumlah,
            });

            stockItemsToUpdate.push({
                id: select.value,
                qty: jumlah,
            });
        }
    });

    if (items.length === 0) {
        Swal.fire("Error", "Pilih minimal 1 barang!", "error");
        return;
    }

    const grandTotal = items.reduce((sum, item) => sum + item.total_harga, 0);

    const statusPembayaranEl = document.querySelector(
        'input[name="statusPembayaran"]:checked'
    );
    const statusPengirimanEl = document.querySelector(
        'input[name="statusPengiriman"]:checked'
    );

    if (!statusPembayaranEl || !statusPengirimanEl) {
        Swal.fire(
            "Error",
            "Harap pilih Status Pembayaran dan Status Pengiriman!",
            "error"
        );
        return;
    }

    const statusPembayaran = statusPembayaranEl.value;
    const statusPengiriman = statusPengirimanEl.value;

    const editId = document.getElementById("editPembelianId").value;
    const isEdit = !!editId;

    let monthlyVal = 0;
    let dpVal = 0;
    let remainingVal = 0;

    if (statusPembayaran === "cicilan") {
        const dpStr = document.getElementById("calcDeposit").value;
        const dp = parseInt(dpStr.replace(/[^0-9]/g, "")) || 0;

        if (dp > grandTotal) {
            Swal.fire(
                "Error",
                "DP tidak boleh lebih besar dari total pembelian!",
                "error"
            );
            return;
        }

        const tenorEl = document.querySelector('input[name="tenor"]:checked');
        const tenor = tenorEl ? parseInt(tenorEl.value) : 6;

        dpVal = dp;
        remainingVal = grandTotal - dp;
    }

    const tenorEl = document.querySelector('input[name="tenor"]:checked');
    const tenorVal = tenorEl ? parseInt(tenorEl.value) : 6;

    let cicilanDetails = [];
    if (statusPembayaran === "cicilan") {
        const startDate = new Date(
            document.getElementById("tanggalPembelian").value
        );
        const amountPerMonth =
            tenorVal > 0 ? Math.ceil((grandTotal - dpVal) / tenorVal) : 0;

        for (let i = 1; i <= tenorVal; i++) {
            let dueDate = new Date(startDate);
            dueDate.setMonth(dueDate.getMonth() + i);

            cicilanDetails.push({
                jatuh_tempo: dueDate.toISOString().split("T")[0],
                jumlah_cicilan: amountPerMonth,
                keterangan: `Cicilan ke-${i}`,
            });
        }
    }

    const data = {
        no_order: document.getElementById("noOrder").value,
        penerima_nama: document.getElementById("namaCustomer").value,
        penerima_alamat: document.getElementById("alamatCustomer").value,
        penerima_telepon: document.getElementById("noTelepon").value,
        tgl_transaksi: document.getElementById("tanggalPembelian").value,
        status_pengiriman: statusPengiriman,
        status_pembayaran: statusPembayaran,
        total_cicilan: statusPembayaran === "cicilan" ? dpVal : 0,
        sisa_cicilan: statusPembayaran === "cicilan" ? remainingVal : 0,
        tenor: statusPembayaran === "cicilan" ? tenorVal : null,
        cicilan_details: cicilanDetails,
        grand_total: grandTotal,
        items: items,
    };

    const url = isEdit ? `${API_PEMBELIAN_URL}/${editId}` : API_PEMBELIAN_URL;
    const method = isEdit ? "PUT" : "POST";

    fetch(url, {
        method: method,
        headers: {
            Authorization: "Bearer " + token,
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    })
        .then(async (res) => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal menyimpan pembelian");
            }
            return res.json();
        })

        .then(async (res) => {
            if (!isEdit && stockItemsToUpdate.length > 0) {
                try {
                    await updateStokItems(stockItemsToUpdate);
                } catch (stockError) {
                    console.error("Gagal update stok:", stockError);
                    Swal.fire(
                        "Warning",
                        "Pembelian disimpan tapi gagal update stok!",
                        "warning"
                    );
                }
            }

            // [FIX] Reverted manual installment creation due to 404

            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text:
                    res.message ||
                    (isEdit
                        ? "Pembelian berhasil diupdate!"
                        : "Pembelian berhasil disimpan!"),
                timer: 1500,
                showConfirmButton: false,
            });

            const modalEl = document.getElementById("inputPembelianModal");
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            resetForm();
            if (document.title.includes("Riwayat")) {
                loadPembelian("lunas");
            } else {
                loadPembelian(null, "lunas");
            }
        })
        .catch((err) => {
            console.error("Error:", err);
            Swal.fire("Error", "Gagal menyimpan pembelian!", "error");
        });
}

async function updateStokItems(items) {
    const token = getToken();
    const updates = items.map(async (item) => {
        try {
            const res = await fetch(`${API_STOK_URL}/${item.id}`, {
                headers: {
                    Authorization: "Bearer " + token,
                    Accept: "application/json",
                },
            });

            if (!res.ok) throw new Error(`Failed to fetch stock ${item.id}`);
            const stockData = await res.json();
            const currentStock = stockData.data;

            const newQty = Math.max(
                0,
                parseInt(currentStock.jumlah) - parseInt(item.qty)
            );

            const formData = new FormData();
            formData.append("nama_barang", currentStock.nama_barang);
            formData.append("harga", currentStock.harga);
            formData.append("jumlah", newQty);
            formData.append("tgl_masuk", currentStock.tgl_masuk);
            formData.append("user_id", currentStock.user_id || 1);
            formData.append("kode_sku", currentStock.kode_sku || "");
            formData.append("merek", currentStock.merek || "");
            formData.append("satuan", currentStock.satuan || "");
            formData.append("panjang", currentStock.panjang || "");
            formData.append("lebar", currentStock.lebar || "");
            formData.append("tinggi", currentStock.tinggi || "");
            formData.append("berat", currentStock.berat || "");

            const updateRes = await fetch(`${API_STOK_URL}/${item.id}`, {
                method: "POST",
                headers: {
                    "X-HTTP-Method-Override": "PUT",
                    Authorization: "Bearer " + token,
                    Accept: "application/json",
                },
                body: formData,
            });

            if (!updateRes.ok)
                throw new Error(`Failed to update stock ${item.id}`);

            return true;
        } catch (err) {
            console.error(`Error updating stock for item ${item.id}:`, err);
            throw err;
        }
    });

    await Promise.all(updates);
}

async function createInstallments(pembelianId, details) {
    // Removed to prevent 404
}

function resetForm() {
    document.getElementById("formPembelian").reset();
    document.getElementById("editPembelianId").value = "";
    document.getElementById("inputPembelianModalLabel").innerHTML =
        '<i class="mdi mdi-clipboard-text"></i>Input Pembelian';

    const container = document.getElementById("containerBarang");
    const items = container.querySelectorAll(".item-row");

    items.forEach((item, index) => {
        if (index > 0) {
            item.remove();
        }
    });

    const firstItem = container.querySelector(".item-row");

    if (firstItem) {
        firstItem.querySelector(".select-barang").value = "";
        firstItem.querySelector(".harga-satuan").value = "";
        firstItem.querySelector(".jumlah-barang").value = "1";
        firstItem.querySelector(".total-item").value = "";
    }

    document.getElementById("totalKeseluruhan").textContent = "Rp 0";

    itemCounter = 1;

    updateRemoveButtons();

    toggleCicilan();

    document
        .querySelectorAll('input[name="statusPengiriman"]')
        .forEach((el) => (el.checked = false));
    document
        .querySelectorAll('input[name="statusPembayaran"]')
        .forEach((el) => (el.checked = false));

    document.getElementById("calcDeposit").value = "";
    document.getElementById("calcCicilanPerBulan").textContent = "Rp 0";
    document.getElementById("calcSisaTagihan").textContent = "Rp 0";
}

async function detailPembelian(id) {
    const token = getToken();
    if (!token) return;

    try {
        const response = await fetch(`${API_PEMBELIAN_URL}/${id}`, {
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
        console.log(
            "Detail Pembelian Raw Data:",
            JSON.stringify(data, null, 2)
        );
        console.log(
            `Check vars: GrandTotal=${data.grand_total}, TotalCicilan(DP)=${data.total_cicilan}, Tenor=${data.tenor}`
        );




        document.getElementById("detailNoOrder").textContent = data.no_order;
        document.getElementById("detailTanggal").textContent = new Date(
            data.tgl_transaksi
        ).toLocaleDateString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
        document.getElementById("detailNama").textContent = data.penerima_nama;
        document.getElementById("detailTelepon").textContent =
            data.penerima_telepon || "-";
        document.getElementById("detailAlamat").textContent =
            data.penerima_alamat || "-";

        const statusPengiriman = data.status_pengiriman || "-";
        document.getElementById("detailStatusPengiriman").textContent =
            statusPengiriman.charAt(0).toUpperCase() +
            statusPengiriman.slice(1);

        let badgeStatus = "";

        switch (data.status_pembayaran) {
            case "lunas":
                badgeStatus =
                    '<span class="badge bg-success-subtle text-success fs-6">Lunas</span>';
                break;
            case "cicilan":
                badgeStatus =
                    '<span class="badge bg-warning-subtle text-warning fs-6">Cicilan</span>';
                break;
            case "belum_lunas":
                badgeStatus =
                    '<span class="badge bg-danger-subtle text-danger fs-6">Belum Lunas</span>';
                break;
            default:
                badgeStatus = '<span class="badge bg-secondary">Unknown</span>';
        }
        document.getElementById("detailStatusBadge").innerHTML = badgeStatus;
        const cicilanContainer = document.getElementById(
            "detailCicilanContainer"
        );
        if (data.status_pembayaran === "cicilan") {
            cicilanContainer.classList.remove("d-none");
            const totalPaid = parseFloat(data.total_cicilan);
            const grandTotal = parseFloat(data.grand_total);

            const transactionDate = new Date(
                data.created_at || data.tgl_transaksi
            );
            const remaining = grandTotal - totalPaid;
            let cicilans =
                data.cicilan_pembelians ||
                data.cicilan ||
                data.installments ||
                [];

            let tenor = parseInt(data.tenor) || cicilans.length || 0;

            let monthly = 0;
            let originalDP = totalPaid; // Default assumption

            if (tenor > 0) {
                // If backend updates total_cicilan, use: const originalDP = totalPaid - paidSum;
                // If backend static, use: const originalDP = totalPaid;
                // To be safe against negative DP if backend is static:
                const paidSum = cicilans
                    .filter((c) => c.status === "lunas")
                    .reduce((sum, c) => sum + parseFloat(c.jumlah_cicilan), 0);

                // Heuristic: If totalPaid < paidSum, it means totalPaid is static DP.
                // If totalPaid >= paidSum, it MIGHT be updated total.
                // Given the bug, let's assume totalPaid is current total (DP + Installments) 
                // BUT clamp originalDP to not be negative?
                // Actually, if originalDP varies, monthly varies. Safe bet: use grandTotal logic?

                // Let's stick to the current logic for monthly, but FIX the Displayed Total Paid.
                originalDP = totalPaid - paidSum;

                // Fallback if originalDP < 0 (implies static total_cicilan)
                if (originalDP < 0) originalDP = totalPaid;

                const initialPrincipal = grandTotal - originalDP;

                monthly = Math.ceil(initialPrincipal / tenor);
            } else if (cicilans.length > 0) {
                let refItem = cicilans.find((c) => c.jumlah_cicilan > 0);
                if (refItem) monthly = parseFloat(refItem.jumlah_cicilan);
            }

            if (cicilans.length === 0 && tenor > 0) {
                for (let i = 1; i <= tenor; i++) {
                    let dueDate = new Date(transactionDate);
                    dueDate.setMonth(dueDate.getMonth() + i);

                    cicilans.push({
                        id: "virtual-" + i,
                        jatuh_tempo: dueDate.toISOString(),
                        jumlah_cicilan: monthly,
                        status: "belum_lunas",
                        tanggal_bayar: null,
                        is_virtual: true,
                    });
                }
            }

            const unpaidCount = cicilans.filter(
                (c) => c.status !== "lunas"
            ).length;

            const calculatedRemaining = unpaidCount * monthly;

            // Fix: Calculate Projected Total (DP + All Installments)
            // Note: Use the logic that derived 'monthly' to reconstruct the full cost
            const initialPrincipalReconstructed = monthly * tenor;
            const totalProjected = originalDP + initialPrincipalReconstructed;

            // Total Paid = Projected - Remaining
            const calculatedTotalPaid = totalProjected - calculatedRemaining;

            document.getElementById("detailCicilanPerBulan").textContent =
                "Rp " + Number(monthly).toLocaleString("id-ID");
            document.getElementById("detailSisaCicilan").textContent =
                "Rp " + Number(calculatedRemaining).toLocaleString("id-ID");
            document.getElementById("detailTotalTerbayar").textContent =
                "Rp " + Number(calculatedTotalPaid).toLocaleString("id-ID");

            const listBody = document.getElementById("listCicilanBody");
            listBody.innerHTML = "";

            if (cicilans.length > 0) {
                cicilans.forEach((cicilan, index) => {
                    const tglJatuhTempo = new Date(
                        cicilan.jatuh_tempo || cicilan.tanggal_jatuh_tempo
                    ).toLocaleDateString("id-ID", {
                        day: "numeric",
                        month: "short",
                        year: "numeric",
                    });
                    const tglBayar = cicilan.tanggal_bayar
                        ? new Date(cicilan.tanggal_bayar).toLocaleDateString(
                            "id-ID",
                            {
                                day: "numeric",
                                month: "short",
                                year: "numeric",
                            }
                        )
                        : "-";

                    const jumlah = Number(monthly).toLocaleString("id-ID");

                    let statusBadge =
                        '<span class="badge bg-danger-subtle text-danger" style="font-size: 0.75rem;">Belum Lunas</span>';

                    let actionBtn = "";

                    if (!cicilan.is_virtual) {
                        actionBtn = `
                            <button class="btn btn-sm btn-success py-0 px-2" onclick="bayarCicilan(${cicilan.id}, ${data.id})" title="Tandai Pembayaran">
                                <i class="mdi mdi-check"></i>
                            </button>`;
                    } else {
                        actionBtn =
                            '<span class="text-muted small" title="Simpan data dulu">(Preview)</span>';
                    }

                    if (cicilan.status === "lunas") {
                        statusBadge =
                            '<span class="badge bg-success-subtle text-success" style="font-size: 0.75rem;">Lunas</span>';
                        actionBtn =
                            '<i class="mdi mdi-check-circle text-success fs-5"></i>';
                    }

                    listBody.innerHTML += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${tglJatuhTempo}</td>
                            <td>Rp ${jumlah}</td>
                            <td>${tglBayar}</td>
                            <td>${statusBadge}</td>
                            <td class="text-center">${actionBtn}</td>
                        </tr>
                    `;
                });
            } else {
                listBody.innerHTML =
                    '<tr><td colspan="6" class="text-center text-muted">Belum ada jadwal cicilan.</td></tr>';
            }
        } else {
            cicilanContainer.classList.add("d-none");
        }

        const itemsBody = document.getElementById("detailItemsBody");
        itemsBody.innerHTML = "";
        data.items.forEach((item) => {
            itemsBody.innerHTML += `
                <tr>
                    <td class="ps-3">
                        <div class="fw-semibold">${item.nama_barang}</div>
                    </td>
                    <td class="text-center">${item.jumlah}</td>
                    <td class="text-end">Rp ${Number(
                item.harga_satuan
            ).toLocaleString("id-ID")}</td>
                    <td class="text-end pe-3 fw-semibold">Rp ${Number(
                item.total_harga
            ).toLocaleString("id-ID")}</td>
                </tr>
            `;
        });

        document.getElementById("detailGrandTotal").textContent =
            "Rp " + Number(data.grand_total).toLocaleString("id-ID");

        const modalEl = document.getElementById("detailPembelianModal");

        if (!modalEl.classList.contains("show")) {
            const modalInstance =
                bootstrap.Modal.getInstance(modalEl) ||
                new bootstrap.Modal(modalEl);

            modalInstance.show();
        }

        return data;
    } catch (err) {
        console.error("Error detail pembelian:", err);
        Swal.fire("Error", "Gagal memuat detail pembelian", "error");
    }
}

function toggleCicilan() {
    const isCicilan = document.querySelector(
        'input[name="statusPembayaran"][value="cicilan"]'
    ).checked;
    const container = document.getElementById("containerCicilan");

    if (isCicilan) {
        container.style.display = "block";

        calculateInlineCicilan();
    } else {
        container.style.display = "none";
    }
}

function calculateInlineCicilan() {
    const container = document.getElementById("containerCicilan");
    if (!container || container.style.display === "none") return;
    let totalSemua = 0;
    document.querySelectorAll(".item-row").forEach((row) => {
        const select = row.querySelector(".select-barang");
        if (select && select.selectedIndex >= 0) {
            const selectedOption = select.options[select.selectedIndex];
            const jumlah =
                parseInt(row.querySelector(".jumlah-barang").value) || 0;
            const harga = parseInt(
                selectedOption.getAttribute("data-harga") || 0
            );
            totalSemua += harga * jumlah;
        }
    });

    const total = totalSemua;

    const dpInput = document.getElementById("calcDeposit");

    let dpStr = dpInput.value;
    let dp = parseInt(dpStr.replace(/[^0-9]/g, "")) || 0;

    const dpError = document.getElementById("dpError");
    if (dp > total) {
        if (dpError) dpError.classList.remove("d-none");
    } else {
        if (dpError) dpError.classList.add("d-none");
    }

    const remaining = Math.max(0, total - dp);

    const tenorEl = document.querySelector('input[name="tenor"]:checked');
    const tenor = tenorEl ? parseInt(tenorEl.value) : 6;

    let monthly = 0;
    if (tenor > 0) {
        monthly = Math.ceil(remaining / tenor);
    }

    document.getElementById("calcCicilanPerBulan").textContent =
        formatRupiah(monthly);
    document.getElementById("calcSisaTagihan").textContent =
        formatRupiah(remaining);

    document.querySelectorAll('input[name="tenor"]').forEach((rb) => {
        const label = document.querySelector(`label[for="${rb.id}"]`);
        if (rb.checked) {
            label.classList.remove("btn-outline-secondary");
            label.classList.add("btn-outline-primary");
        } else {
            label.classList.add("btn-outline-secondary");
            label.classList.remove("btn-outline-primary");
        }
    });
}

function formatCurrencyInput(input) {
    let value = input.value.replace(/[^0-9]/g, "");
    if (value) {
        input.value = formatRupiah(value);
    } else {
        input.value = "";
    }
}

async function editPembelian(id) {
    const token = getToken();
    if (!token) return;

    try {
        const response = await fetch(`${API_PEMBELIAN_URL}/${id}`, {
            method: "GET",
            headers: {
                Authorization: "Bearer " + token,
                Accept: "application/json",
            },
        });

        if (!response.ok) throw new Error("Gagal mengambil data");
        const data = await response.json();

        document.getElementById("editPembelianId").value = data.id;
        document.getElementById("inputPembelianModalLabel").innerHTML =
            '<i class="mdi mdi-pencil"></i> Edit Pesanan';

        document.getElementById("noOrder").value = data.no_order;
        document.getElementById("tanggalPembelian").value = data.tgl_transaksi;
        document.getElementById("namaCustomer").value = data.penerima_nama;
        document.getElementById("noTelepon").value = data.penerima_telepon;
        document.getElementById("alamatCustomer").value = data.penerima_alamat;

        const statusPengiriman = document.querySelector(
            `input[name="statusPengiriman"][value="${data.status_pengiriman}"]`
        );

        if (statusPengiriman) statusPengiriman.checked = true;

        const statusPembayaran = document.querySelector(
            `input[name="statusPembayaran"][value="${data.status_pembayaran}"]`
        );
        if (statusPembayaran) statusPembayaran.checked = true;

        const container = document.getElementById("containerBarang");
        container.innerHTML = "";
        itemCounter = 0;

        const stokResponse = await fetch(API_STOK_URL, {
            headers: {
                Authorization: "Bearer " + token,
                Accept: "application/json",
            },
        });
        const stokData = await stokResponse.json();
        const barangList = stokData.data;
        globalBarangList = barangList;

        console.log("Edit Data Items:", data.items);
        data.items.forEach((item, index) => {
            console.log(`Processing Item ${index}:`, item);
            tambahItemBaru(true);
            const rows = container.querySelectorAll(".item-row");
            const currentRow = rows[rows.length - 1];

            const select = currentRow.querySelector(".select-barang");

            select.classList.add("preserve-options");

            select.innerHTML = '<option value="">Pilih barang...</option>';

            let matched = false;
            barangList.forEach((b) => {
                select.innerHTML += `<option value="${b.id}" data-harga="${b.harga_jual || b.harga
                    }" data-stok="${b.jumlah}">${b.nama_barang}</option>`;
            });

            const targetName = (item.nama_barang || "").trim();
            console.log(`Matching for: '${targetName}'`);

            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].text.trim() === targetName) {
                    select.selectedIndex = i;
                    matched = true;

                    break;
                }
            }
            if (!matched) {
                console.warn(
                    "Item not found in stock list, using fallback:",
                    targetName
                );
                const opt = document.createElement("option");

                opt.value = `virtual-${item.id || index}`;
                opt.text = targetName
                    ? `${targetName} (Arsip)`
                    : "Item Tanpa Nama";
                opt.setAttribute("data-harga", item.harga_satuan);
                opt.setAttribute("data-stok", "9999");

                select.add(opt);
                select.value = opt.value;
            }

            currentRow.querySelector(".jumlah-barang").value = item.jumlah;
            currentRow.querySelector(".harga-satuan").value = formatRupiah(
                item.harga_satuan
            );
            currentRow.querySelector(".total-item").value = formatRupiah(
                item.total_harga
            );
        });

        if (data.status_pembayaran === "cicilan") {
            toggleCicilan();

            const inputDeposit = document.getElementById("calcDeposit");
            if (inputDeposit) {
                const rawDp = parseInt(data.total_cicilan) || 0;
                inputDeposit.value = formatRupiah(rawDp);
            }

            const tenor = data.tenor || 6;
            const tenorRadio = document.querySelector(
                `input[name="tenor"][value="${tenor}"]`
            );
            if (tenorRadio) {
                tenorRadio.checked = true;

                document
                    .querySelectorAll('input[name="tenor"]')
                    .forEach((rb) => {
                        const label = document.querySelector(
                            `label[for="${rb.id}"]`
                        );
                        if (label) {
                            if (rb.checked) {
                                label.classList.remove("btn-outline-secondary");
                                label.classList.add("btn-outline-primary");
                            } else {
                                label.classList.add("btn-outline-secondary");
                                label.classList.remove("btn-outline-primary");
                            }
                        }
                    });
            }

            setTimeout(() => {
                calculateInlineCicilan();
            }, 500);
        } else {
            const containerCicilan =
                document.getElementById("containerCicilan");
            if (containerCicilan) containerCicilan.style.display = "none";
        }

        hitungTotalKeseluruhan();

        const modal = new bootstrap.Modal(
            document.getElementById("inputPembelianModal")
        );

        modal.show();
    } catch (error) {
        console.error("Error:", error);
        Swal.fire("Error", "Gagal memuat data untuk edit", "error");
    }
}

function deletePembelian(id) {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data Penjualan akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            const token = getToken();
            if (!token) return;

            fetch(`${API_PEMBELIAN_URL}/${id}`, {
                method: "DELETE",
                headers: {
                    Authorization: "Bearer " + token,
                    Accept: "application/json",
                },
            })
                .then(async (res) => {
                    if (!res.ok) throw new Error("Gagal menghapus");
                    return res.json();
                })
                .then((res) => {
                    Swal.fire(
                        "Terhapus!",
                        "Data pembelian telah dihapus.",
                        "success"
                    );

                    if (document.title.includes("Riwayat")) {
                        loadPembelian("lunas");
                    } else {
                        loadPembelian(null, "lunas");
                    }
                })
                .catch((err) => {
                    console.error("Error:", err);
                    Swal.fire("Error", "Gagal menghapus data!", "error");
                });
        }
    });
}

function filterByStatus(status) {
    let backendStatus = null;
    switch (status) {
        case "Lunas":
            backendStatus = "lunas";
            break;
        case "Cicilan":
            backendStatus = "cicilan";
            break;
        case "Belum Lunas":
            backendStatus = "belum_lunas";
            break;
    }
    loadPembelian(backendStatus);
}

function bayarCicilan(id, parentId) {
    Swal.fire({
        title: "Konfirmasi Pembayaran",
        text: "Tandai cicilan ini sebagai Lunas?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, Lunas!",
    }).then((result) => {
        if (!result.isConfirmed) return;
        const token = getToken();

        fetch(`/api/cicilan-pembelians/${id}`, {
            method: "PUT",
            headers: {
                Authorization: "Bearer " + token,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                status: "lunas",
                keterangan: "Dibayar via Dashboard",
            }),
        })
            .then(async (res) => {
                if (!res.ok) {
                    const text = await res.text();
                    throw new Error(text || "Gagal update status");
                }
                return res.json();
            })
            .then((data) => {
                Swal.fire("Berhasil", "Status cicilan diperbarui!", "success");
                detailPembelian(parentId);
                loadPembelian(
                    null,
                    document.title.includes("Riwayat") ? null : "lunas"
                );
            })
            .catch((err) => {
                console.error("Error bayar cicilan:", err);
                Swal.fire("Error", "Gagal memproses pembayaran.", "error");
            });
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("containerBarang");
    if (container) {
        container.addEventListener("input", function (e) {
            if (e.target.classList.contains("jumlah-barang")) {
                validateStock(e.target);
                hitungTotalItem(e.target);
            }
        });
        container.addEventListener("change", function (e) {
            if (e.target.classList.contains("select-barang")) {
                const row = e.target.closest(".item-row");
                const quantityInput = row.querySelector(".jumlah-barang");

                updateHargaBarang(e.target);
                validateStock(quantityInput);
            }
        });
    }
});

function loadAcceptedSPH() {
    const token = getToken();
    const selectSPH = document.getElementById('selectSPH');
    if (!selectSPH) return;

    // Reset dropdown
    selectSPH.innerHTML = '<option value="">-- Pilih SPH Diterima --</option>';

    fetch('/api/surat-penawaran/accepted', {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    })
        .then(res => res.json())
        .then(res => {
            acceptedSPHList = res.data || [];
            acceptedSPHList.forEach(sph => {
                const option = document.createElement('option');
                option.value = sph.id;
                // Format: [Tgl] Nama Perusahaan - Hal
                const tgl = new Date(sph.tanggal).toLocaleDateString('id-ID');
                option.text = `[${tgl}] ${sph.nama_perusahaan} - ${sph.hal || '-'}`;
                selectSPH.appendChild(option);
            });
        })
        .then(() => {
            // Init Select2 for SPH
            $('#selectSPH').select2({
                dropdownParent: $('#inputPembelianModal'),
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Pilih SPH Diterima --'
            });
        })
        .catch(err => console.error('Gagal load SPH:', err));
}

function loadSPHData() {
    const selectSPH = document.getElementById('selectSPH');
    if (!selectSPH || !selectSPH.value) {
        Swal.fire('Warning', 'Silakan pilih SPH terlebih dahulu', 'warning');
        return;
    }

    const sphId = parseInt(selectSPH.value);
    const sph = acceptedSPHList.find(item => item.id === sphId);

    if (!sph) return;

    // Populate Fields
    document.getElementById('namaCustomer').value = sph.nama_perusahaan || '';
    document.getElementById('alamatCustomer').value = sph.alamat || '';

    // Clear existing items
    const container = document.getElementById("containerBarang");
    container.innerHTML = ''; // Clear all
    itemCounter = 0; // Reset counter

    // Populate Items
    if (sph.detail_barang && Array.isArray(sph.detail_barang)) {
        sph.detail_barang.forEach(item => {
            tambahItemBaru(true); // Add row without reloading options (optimized)

            // Get last added row
            const rows = container.getElementsByClassName('item-row');
            const lastRow = rows[rows.length - 1];

            if (lastRow) {
                const select = lastRow.querySelector('.select-barang');
                const qtyInput = lastRow.querySelector('.jumlah-barang');

                // Match Item Logic (ByName)
                // We need to match 'item.nama' from SPH with 'barang.nama_barang' from globalBarangList
                const sphItemName = (item.nama_barang || item.nama || '').toLowerCase().trim();

                let matchedStock = null;
                if (globalBarangList) {
                    matchedStock = globalBarangList.find(stock =>
                        stock.nama_barang.toLowerCase().trim() === sphItemName ||
                        stock.nama_barang.toLowerCase().trim().includes(sphItemName) // Loose match
                    );
                }

                if (matchedStock) {
                    // Set value and trigger change for Select2
                    $(select).val(matchedStock.id).trigger('change');
                } else {
                    console.warn('Stok tidak ditemukan untuk SPH item:', sphItemName);
                }

                if (qtyInput) {
                    qtyInput.value = item.jumlah || 1;
                    hitungTotalItem(qtyInput);
                }
            }
        });
    }

    Swal.fire({
        icon: 'success',
        title: 'Data Terisi',
        text: 'Data pelanggan dan item berhasil dimuat dari SPH.',
        timer: 1500,
        showConfirmButton: false
    });
}


function validateStock(input) {
    const row = input.closest(".item-row");
    const select = row.querySelector(".select-barang");
    const selectedOption = select.options[select.selectedIndex];

    if (!selectedOption || !selectedOption.value) return;

    const maxStock = parseInt(selectedOption.getAttribute("data-stok") || 0);
    let currentQty = parseInt(input.value) || 0;

    if (currentQty > maxStock) {
        Swal.fire({
            icon: "warning",
            title: "Stok Tidak Cukup",
            text: `Stok tersedia hanya ${maxStock}. Jumlah akan disesuaikan.`,
            timer: 2000,
            showConfirmButton: false,
        });
        input.value = maxStock;
    }
}

// ==========================================
// BULK DELETE LOGIC
// ==========================================

function toggleSelectAllPembelian(source) {
    const checkboxes = document.querySelectorAll('.pembelian-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const selectedCount = document.querySelectorAll('.pembelian-checkbox:checked').length;
    const btn = document.getElementById('btnBulkDelete');
    const countSpan = document.getElementById('selectedCount');
    const selectAllCb = document.getElementById('selectAllPembelian');

    if (btn && countSpan) {
        countSpan.innerText = selectedCount;
        if (selectedCount > 0) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    }

    if (selectAllCb) {
        const totalCheckboxes = document.querySelectorAll('.pembelian-checkbox').length;
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

function bulkDeletePembelian() {
    const selectedCheckboxes = document.querySelectorAll('.pembelian-checkbox:checked');
    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (ids.length === 0) return;

    Swal.fire({
        title: 'Hapus data terpilih?',
        html: `Anda akan menghapus <strong>${ids.length}</strong> data pembelian.<br>Data yang dihapus tidak dapat dikembalikan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = getToken();

            fetch(`${API_PEMBELIAN_URL}/bulk-delete`, {
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

                    const selectAllCb = document.getElementById('selectAllPembelian');
                    if (selectAllCb) {
                        selectAllCb.checked = false;
                        selectAllCb.indeterminate = false;
                    }
                    updateBulkDeleteButton();
                    loadPembelian();
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
