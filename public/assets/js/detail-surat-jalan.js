document.addEventListener("DOMContentLoaded", function () {

    const pathArray = window.location.pathname.split('/');
    const id = pathArray[pathArray.length - 1];

    if (id && !isNaN(id)) {
        loadDetailSuratJalan(id);


        const btnCetak = document.getElementById("btnCetak");
        if (btnCetak) {
            btnCetak.href = `/print-surat-jalan/${id}`;
        }

        const telpInput = document.getElementById("editTelpPenerima");
        if (telpInput) {
            telpInput.addEventListener("input", function () {
                this.value = this.value.replace(/\D/g, "");
            });
        }


        const btnUpdate = document.getElementById("btnUpdateSuratJalan");
        if (btnUpdate) {
            btnUpdate.addEventListener("click", function () {
                const form = document.getElementById("formEditSuratJalan");
                if (form.checkValidity()) {
                    const telp = document.getElementById("editTelpPenerima").value;
                    if (!/^\d+$/.test(telp)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Input Tidak Valid',
                            text: 'Nomor telepon harus berupa angka!',
                        });
                        return;
                    }
                    const formData = new FormData(form);
                    updateSuratJalan(id, formData);
                } else {
                    form.reportValidity();
                }
            });
        }
    } else {
        alert("ID Surat Jalan tidak valid");
    }
});

const API_SURAT_JALAN = "http://127.0.0.1:8000/api/surat-jalan";

function getToken() {
    return localStorage.getItem("token");
}

function loadDetailSuratJalan(id) {
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
            renderDetailSuratJalan(data);
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Gagal memuat data surat jalan! " + err.message);
        });
}

function renderDetailSuratJalan(data) {

    let signer = data.penandatangan;
    let cleanKeterangan = data.keterangan || "";


    if (cleanKeterangan.includes('[SIG:Dewi]')) {
        signer = "Dewi Sulistiowati";
        cleanKeterangan = cleanKeterangan.replace(' [SIG:Dewi]', '').replace('[SIG:Dewi]', '');
    } else if (cleanKeterangan.includes('[SIG:Heri]')) {
        signer = "Heri Firdaus, S.Tr.Kes Rad (MRI)";
        cleanKeterangan = cleanKeterangan.replace(' [SIG:Heri]', '').replace('[SIG:Heri]', '');
    }


    setText("detailTanggal", formatDate(data.tanggal));
    setText("detailNamaPengirim", data.nama_pengirim);
    setText("detailNamaPenerima", data.nama_penerima);
    setText("detailAlamatPenerima", data.alamat_penerima);
    setText("detailTelpPenerima", data.telp_penerima);
    setText("detailKeterangan", cleanKeterangan);

    setText("detailNamaBarang", data.nama_barang_jasa);
    setText("detailQty", data.qty);
    setText("detailJumlah", data.qty);




    const signatureImg = document.getElementById('detailSignature');


    if (signer && signer.toLowerCase().includes('dewi')) {
        signatureImg.src = '/assets/images/ttd dewi.jpeg';
    } else if (signer && signer.toLowerCase().includes('heri')) {
        signatureImg.src = '/assets/images/ttd heri.png';
    } else if (signer && signer.toLowerCase().includes('arya')) {
        signatureImg.src = '/assets/images/ttd arya.png';
    } else {

        signatureImg.src = '/assets/images/ttd arya.png';
    }

    setText("detailSignerName", signer || "MUHAMMAD ARYA");


    setVal("editNomor", data.nomor_surat_jalan);
    const dateValue = data.tanggal ? new Date(data.tanggal).toISOString().split('T')[0] : '';
    setVal("editTanggal", dateValue);
    setVal("editNamaPengirim", data.nama_pengirim);
    setVal("editNamaPenerima", data.nama_penerima);
    setVal("editAlamatPenerima", data.alamat_penerima);
    setVal("editTelpPenerima", data.telp_penerima);
    setVal("editNamaBarang", data.nama_barang_jasa);
    setVal("editQty", data.qty);
    setVal("editJumlah", data.jumlah);
    setVal("editKeterangan", cleanKeterangan);
    setVal("editPenandatangan", signer || "MUHAMMAD ARYA");
}

function updateSuratJalan(id, formData) {
    const token = getToken();
    const headers = {
        "Accept": "application/json",
        "Content-Type": "application/json"
    };
    if (token) {
        headers["Authorization"] = "Bearer " + token;
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

    fetch(`${API_SURAT_JALAN}/${id}`, {
        method: "PUT",
        headers: headers,
        body: JSON.stringify(data)
    })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.error("RESPON ERROR:", text);
                throw new Error("Gagal mengupdate Surat Jalan");
            }
            return res.json();
        })
        .then(res => {
            console.log("Update Success:", res);
            wal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Surat Jalan berhasil diupdate!',
                timer: 1500,
                showConfirmButton: false
            });


            const modalEl = document.getElementById('modalEditSuratJalan');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            loadDetailSuratJalan(id);
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal mengupdate Surat Jalan'
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