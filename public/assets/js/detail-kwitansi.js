document.addEventListener("DOMContentLoaded", function () {
    const pathArray = window.location.pathname.split('/');
    const id = pathArray[pathArray.length - 1];

    if (id && !isNaN(id)) {
        loadDetailKwitansi(id);

        const btnUpdate = document.getElementById("btnUpdateKwitansi");
        if (btnUpdate) {
            btnUpdate.addEventListener("click", function () {
                const form = document.getElementById("formEditKwitansi");
                if (form.checkValidity()) {
                    const formData = new FormData(form);
                    updateKwitansi(id, formData);
                } else {
                    form.reportValidity();
                }
            });
        }
    } else {
        console.error("ID Kwitansi tidak valid");
        alert("ID Kwitansi tidak valid");
    }
});

const API_KWITANSI = "/api/kwitansi";

function getToken() {
    return localStorage.getItem("token");
}

function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function loadDetailKwitansi(id) {
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
        method: "GET",
        headers: headers
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal memuat detail Kwitansi: " + res.status + " " + res.statusText);
            }
            return res.json();
        })
        .then(res => {
            console.log("Response Detail:", res);
            const data = res.data || res;
            renderDetailKwitansi(data);
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Gagal memuat detail kwitansi! " + err.message);
        });
}

function renderDetailKwitansi(data) {
    let signer = data.penandatangan;
    let cleanKeterangan = data.keterangan || "";

    if (cleanKeterangan.includes('[SIG:Dewi]')) {
        signer = "Dewi Sulistiowati";
        cleanKeterangan = cleanKeterangan.replace(' [SIG:Dewi]', '').replace('[SIG:Dewi]', '');
    }
    setText("nomor_kwitansi", data.nomor_kwitansi);
    setText("tanggal", formatDate(data.tanggal));
    setText("nama_penerima", data.nama_penerima);
    setText("alamat_penerima", data.alamat_penerima);
    setText("total_bilangan", data.total_bilangan);
    setText("keterangan", cleanKeterangan); 
    setText("created_at", data.created_at ? new Date(data.created_at).toLocaleString('id-ID') : '-');

    const total = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.total_pembayaran);
    setText("total_pembayaran", total);
    setVal("editNomorKwitansi", data.nomor_kwitansi);

    const dateValue = data.tanggal ? new Date(data.tanggal).toISOString().split('T')[0] : '';
    setVal("editTanggal", dateValue);

    setVal("editNamaPenerima", data.nama_penerima);
    setVal("editAlamat", data.alamat_penerima);
    setVal("editTotalBilangan", data.total_bilangan);
    setVal("editKeterangan", cleanKeterangan); 
    setVal("editTotalPembayaran", parseInt(data.total_pembayaran));
    setVal("editPenandatangan", signer || "Heri Pirdaus, S.Tr.Kes Rad (MRI)");
}

function updateKwitansi(id, formData) {
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

    let ket = data.keterangan || '';
    ket = ket.replace(' [SIG:Dewi]', '').replace('[SIG:Dewi]', '');
    ket = ket.replace(' [SIG:Heri]', '').replace('[SIG:Heri]', '');

    if (data.penandatangan && data.penandatangan.includes('Dewi')) {
        ket += ' [SIG:Dewi]';
    } else if (data.penandatangan && data.penandatangan.includes('Heri')) {
        ket += ' [SIG:Heri]';
    }
    data.keterangan = ket;

    if (data.total_pembayaran) {
        data.total_pembayaran = data.total_pembayaran.toString().replace(/\./g, '');
    }

    fetch(`${API_KWITANSI}/${id}`, {
        method: "PUT",
        headers: headers,
        body: JSON.stringify(data)
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal mengupdate Kwitansi");
            }
            return res.json();
        })
        .then(res => {
            console.log("Update Success:", res);
            wal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Kwitansi berhasil diupdate!',
                timer: 1500,
                showConfirmButton: false
            });

            const modalEl = document.getElementById('modalEditKwitansi');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            loadDetailKwitansi(id);
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal mengupdate Kwitansi'
            });
        });
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
