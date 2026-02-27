document.addEventListener("DOMContentLoaded", function () {
    loadDashboardData();
});

const API_BASE_URL = "/api";
const ENDPOINT_PEMBELIAN = `${API_BASE_URL}/pembelians`;
const ENDPOINT_STOK = `${API_BASE_URL}/stoks`;

let globalPembelianData = [];
let globalStokData = [];
let selectedYear = new Date().getFullYear();

function getToken() {
    return localStorage.getItem("token");
}

function formatRupiah(number) {
    return 'Rp ' + Number(number).toLocaleString('id-ID');
}

function loadDashboardData() {
    const token = getToken();
    if (!token) return;

    // Fetch Pembelian
    fetch(ENDPOINT_PEMBELIAN, {
        headers: { "Authorization": "Bearer " + token, "Accept": "application/json" }
    })
        .then(res => res.json())
        .then(data => {
            globalPembelianData = data;
            initDashboard();
        })
        .catch(err => console.error("Error fetching pembelian:", err));

    // Fetch Stok
    fetch(ENDPOINT_STOK, {
        headers: { "Authorization": "Bearer " + token, "Accept": "application/json" }
    })
        .then(res => res.json())
        .then(response => {
            // Adjust based on actual API response structure (stok.js uses response.data)
            globalStokData = response.data || response;
            updateStokWidget();
        })
        .catch(err => console.error("Error fetching stok:", err));
}

function initDashboard() {
    updateWidgets();
    renderYearFilter();
    updateSalesChart();
    renderRecentTransactions();
}

// Separate function for Stok Widget to be safe
function updateStokWidget() {
    const now = new Date();
    const currentMonth = now.getMonth();
    const currentYear = now.getFullYear();

    const totalStokMasukMonth = globalStokData
        .filter(item => {
            if (!item.tgl_masuk) return false;
            const d = new Date(item.tgl_masuk);
            return d.getMonth() === currentMonth && d.getFullYear() === currentYear;
        })
        .reduce((sum, item) => {
            const harga = parseFloat(item.harga || 0);
            const jumlah = parseFloat(item.jumlah || 0);
            return sum + (harga * jumlah);
        }, 0);

    const elStokMasuk = document.getElementById('totalStokMasukMonth');
    if (elStokMasuk) elStokMasuk.innerText = formatRupiah(totalStokMasukMonth);
}

function renderYearFilter() {
    const container = document.getElementById('yearDropdownMenu');
    const btnText = document.getElementById('yearDropdownBtn');
    if (!container || !btnText) return;

    const startYear = 2025;
    const endYear = 2030;

    btnText.innerHTML = `${selectedYear} <i class="mdi mdi-chevron-down ms-1 fs-14"></i>`;

    container.innerHTML = '';
    for (let y = startYear; y <= endYear; y++) {
        const item = document.createElement('a');
        item.classList.add('dropdown-item');
        item.href = '#';
        item.innerText = y;

        item.onclick = (e) => {
            e.preventDefault();
            selectedYear = y;
            btnText.innerHTML = `${selectedYear} <i class="mdi mdi-chevron-down ms-1 fs-14"></i>`;
            updateSalesChart();
        };
        container.appendChild(item);
    }
}

function updateWidgets() {
    const customers = new Set();
    globalPembelianData.forEach(item => {
        const name = item.penerima_nama || item.nama_perusahaan;
        if (name) customers.add(name.trim().toLowerCase());
    });

    const pendingList = globalPembelianData.filter(item =>
        (item.status_pembayaran === 'cicilan' || item.status_pembayaran === 'belum_lunas') &&
        item.status_pembelian !== 'batal'
    );

    const totalPendingValue = pendingList.reduce((sum, item) => sum + parseFloat(item.grand_total || 0), 0);

    // Calculate Sales for Current Month Only
    const now = new Date();
    const currentMonth = now.getMonth();
    const currentYear = now.getFullYear();

    const totalSalesMonth = globalPembelianData
        .filter(item => {
            if (item.status_pembelian === 'batal') return false;
            const d = new Date(item.tgl_transaksi);
            return d.getMonth() === currentMonth && d.getFullYear() === currentYear;
        })
        .reduce((sum, item) => sum + parseFloat(item.grand_total || 0), 0);

    const elPelanggan = document.getElementById('totalPelanggan');
    if (elPelanggan) elPelanggan.innerText = customers.size;

    const elPending = document.getElementById('totalPendingPayment');
    if (elPending) elPending.innerText = pendingList.length.toLocaleString('id-ID');

    const elPendingValue = document.getElementById('totalPendingPaymentValue');
    if (elPendingValue) elPendingValue.innerText = `(${formatRupiah(totalPendingValue)})`;

    const elDocs = document.getElementById('totalDocuments');
    if (elDocs) elDocs.innerText = globalPembelianData.length.toLocaleString('id-ID');

    const elSales = document.getElementById('totalSales');
    if (elSales) elSales.innerText = formatRupiah(totalSalesMonth);

    // Ensure stok widget is updated if data arrived before this call
    if (globalStokData.length > 0) updateStokWidget();
}

function updateSalesChart() {
    const chartLunas = new Array(12).fill(0);
    const chartCicilan = new Array(12).fill(0);
    const chartBelum = new Array(12).fill(0);
    let totalYearlySales = 0;

    globalPembelianData.forEach(item => {
        if (item.status_pembelian !== 'batal') {
            const date = new Date(item.tgl_transaksi);
            if (date.getFullYear() === selectedYear) {
                const amount = parseFloat(item.grand_total || 0);
                const month = date.getMonth();
                const status = item.status_pembayaran;

                totalYearlySales += amount;

                if (status === 'lunas') {
                    chartLunas[month] += amount;
                } else if (status === 'cicilan') {
                    chartCicilan[month] += amount;
                } else {
                    chartBelum[month] += amount;
                }
            }
        }
    });

    const elTotalYear = document.getElementById('totalYearSalesDisplay');
    if (elTotalYear) {
        elTotalYear.innerText = `Total ${selectedYear}: ${formatRupiah(totalYearlySales)}`;
    }

    renderSalesChart(chartLunas, chartCicilan, chartBelum);
}

function renderRecentTransactions() {
    const elRecentList = document.getElementById('recentTransactionsList');
    if (!elRecentList) return;

    const recentTransactions = globalPembelianData
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 6);

    elRecentList.innerHTML = '';

    if (recentTransactions.length === 0) {
        elRecentList.innerHTML = `
            <div class="text-center p-4">
                <div class="avatar-sm mx-auto mb-3">
                    <div class="avatar-title bg-light rounded-circle text-primary fs-20">
                        <i class="mdi mdi-calendar-remove"></i>
                    </div>
                </div>
                <h5 class="text-muted fs-14">Belum ada transaksi</h5>
            </div>
        `;
        return;
    }

    recentTransactions.forEach(item => {
        const date = new Date(item.tgl_transaksi).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

        let statusColor = 'text-secondary';
        let bgClass = 'bg-light';
        let icon = 'mdi-cash';

        if (item.status_pembayaran === 'lunas') {
            statusColor = 'text-success';
            bgClass = 'bg-success-subtle';
            icon = 'mdi-check-circle-outline';
        } else if (item.status_pembayaran === 'cicilan') {
            statusColor = 'text-warning';
            bgClass = 'bg-warning-subtle';
            icon = 'mdi-clock-outline';
        } else {
            statusColor = 'text-danger';
            bgClass = 'bg-danger-subtle';
            icon = 'mdi-alert-circle-outline';
        }

        elRecentList.innerHTML += `
           <li class="list-group-item border-0 px-0">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title ${bgClass} ${statusColor} rounded-circle fs-18">
                            <i class="mdi ${icon}"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fs-14 mb-1 text-dark">${item.penerima_nama || 'Tanpa Nama'}</h6>
                        <span class="text-muted fs-12 d-block">Order #${item.no_order}</span>
                    </div>
                    <div class="flex-shrink-0 text-end">
                         <h6 class="fs-14 mb-1 text-dark">${formatRupiah(item.grand_total)}</h6>
                         <span class="badge ${bgClass} ${statusColor} fs-11">${item.status_pembayaran.replace(/_/g, ' ').toUpperCase()}</span>
                    </div>
                </div>
            </li>
        `;
    });
}

function renderSalesChart(lunas, cicilan, belum) {
    const container = document.querySelector("#sales-overview");
    if (!container) return;
    container.innerHTML = "";

    const monthlyTotals = lunas.map((val, index) => val + cicilan[index] + belum[index]);
    const maxVal = Math.max(...monthlyTotals);
    const maxY = maxVal === 0 ? 50000000 : undefined;

    var options = {
        series: [
            { name: "Lunas", data: lunas },
            { name: "Cicilan", data: cicilan },
            { name: "Belum Lunas", data: belum }
        ],
        chart: {
            type: "bar",
            height: 350,
            stacked: true,
            toolbar: { show: false },
            parentHeightOffset: 0,
        },
        dataLabels: { enabled: false },
        colors: ["#10c469", "#f9c851", "#ff5b5b"],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '40%',
                borderRadius: 4,
            },
        },
        xaxis: {
            categories: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"],
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            max: maxY,
            labels: {
                formatter: (val) => {
                    if (val >= 1000000000) return (val / 1000000000).toFixed(0) + "M";
                    if (val >= 1000000) return (val / 1000000).toFixed(0) + "jt";
                    return val;
                },
                style: { colors: '#adb5bd' }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            padding: { top: -20, right: 0, bottom: 0, left: 10 }
        },
        tooltip: {
            y: { formatter: (val) => formatRupiah(val) },
            theme: 'dark'
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            offsetY: -20
        }
    };

    var chart = new ApexCharts(container, options);
    chart.render();
}
