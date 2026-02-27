document.addEventListener("DOMContentLoaded", function () {

    const pathArray = window.location.pathname.split('/');
    const id = pathArray[pathArray.length - 1];

    if (id && !isNaN(id)) {
        loadPrintData(id);
    } else {
        alert("ID Kwitansi tidak valid");
    }
});

const API_KWITANSI = "/api/kwitansi";

function getToken() {
    return localStorage.getItem("token");
}

function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID') + ',-';
}

function formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function loadPrintData(id) {
    const token = getToken();

    const headers = {
        "Accept": "application/json"
    };
    if (token) {
        headers["Authorization"] = "Bearer " + token;
    }

    fetch(`${API_KWITANSI}/${id}`, {
        method: "GET",
        headers: headers
    })
        .then(res => res.json())
        .then(res => {
            const data = res.data || res;
            populatePrintView(data);
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Gagal memuat data print!");
        });
}

function populatePrintView(data) {

    setText("printNamaPenerima", data.nama_penerima);
    setText("printAlamatPenerima", data.alamat_penerima);
    setText("printTanggal", formatDate(data.tanggal));
    setText("printNomor", data.nomor_kwitansi);



    let signer = data.penandatangan;
    let cleanKeterangan = data.keterangan || "";

    if (cleanKeterangan.includes('[SIG:Dewi]')) {
        signer = "Dewi Sulistiowati";
        cleanKeterangan = cleanKeterangan.replace(' [SIG:Dewi]', '').replace('[SIG:Dewi]', '');
    }


    setText("printTerimaDari", data.nama_penerima);
    setText("printTerbilang", data.total_bilangan);
    setText("printKeterangan", cleanKeterangan);
    setText("printTotal", formatRupiah(data.total_pembayaran));


    const signatureImg = document.getElementById('printSignature');
    const signerName = document.getElementById('printSignerName');


    if (signer && signer.toLowerCase().includes('dewi')) {
        signatureImg.src = '/assets/images/ttd dewi.jpeg';
    } else {

        signatureImg.src = '/assets/images/ttd heri.png';
    }

    setText("printSignerName", signer || "Heri Pirdaus, S.Tr.Kes Rad (MRI)");


    // Auto-print removed as per user request to show preview first
    // setTimeout(() => {
    //     window.print();
    // }, 1000);
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value || "-";
}
