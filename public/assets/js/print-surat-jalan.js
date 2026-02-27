document.addEventListener("DOMContentLoaded", function () {

    const pathArray = window.location.pathname.split('/');
    const id = pathArray[pathArray.length - 1];

    if (id && !isNaN(id)) {
        loadPrintSuratJalan(id);
    } else {
        alert("ID Surat Jalan tidak valid");
    }
});


const API_SURAT_JALAN = "/api/surat-jalan";

function getToken() {
    return localStorage.getItem("token");
}

function loadPrintSuratJalan(id) {
    const token = getToken();
    const headers = {
        "Accept": "application/json"
    };
    if (token) {
        headers["Authorization"] = "Bearer " + token;
    }

    fetch(`${API_SURAT_JALAN}/${id}`, {
        method: "GET",
        headers: headers
    })
        .then(async res => {
            if (!res.ok) {
                throw new Error("Gagal memuat data surat jalan: " + res.status);
            }
            return res.json();
        })
        .then(res => {
            const data = res.data || res;
            renderPrintSuratJalan(data);
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Gagal memuat data surat jalan! " + err.message);
        });
}

function renderPrintSuratJalan(data) {
    setText("printNamaPenerima", data.nama_penerima);
    setText("printAlamatPenerima", data.alamat_penerima);
    setText("printTelpPenerima", data.telp_penerima);
    setText("printTanggal", formatDate(data.tanggal));


    setText("printNamaBarang", data.nama_barang_jasa);
    setText("printQty", data.qty);
    setText("printJumlah", data.qty);
    setText("printNamaPenerimaSign", data.nama_penerima);


    let signer = data.penandatangan;
    let cleanKeterangan = data.keterangan || "";

    if (cleanKeterangan.includes('[SIG:Dewi]')) {
        signer = "Dewi Sulistiowati";
        cleanKeterangan = cleanKeterangan.replace(' [SIG:Dewi]', '').replace('[SIG:Dewi]', '');
    } else if (cleanKeterangan.includes('[SIG:Heri]')) {
        signer = "Heri Pirdaus, S.Tr.Kes Rad (MRI)";
        cleanKeterangan = cleanKeterangan.replace(' [SIG:Heri]', '').replace('[SIG:Heri]', '');
    }

    setText("printKeterangan", cleanKeterangan);


    const signatureImg = document.getElementById('printSignature');
    if (signatureImg) {
        signatureImg.style.display = 'none';
        signatureImg.src = '';
    }


    const signerName = data.penandatangan || data.nama_pengirim || "PENGIRIM";
    setText("printSignerName", signerName);




    // setTimeout(() => window.print(), 1000);
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value || "-";
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
