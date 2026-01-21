const API_URL = "http://127.0.0.1:8000/api";
const token = localStorage.getItem("token");

// ===== LOGIN =====
async function loginUser(email, password) {
    const res = await fetch(`${API_URL}/login`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify({ email, password }),
    });

    const data = await res.json().catch(() => null);
    if (!res.ok)
        throw {
            message: data?.message || "Terjadi kesalahan saat login",
            status: res.status,
        };
    if (data.user?.status?.toLowerCase() === "pending")
        throw { message: "Akun Anda belum disetujui oleh owner!", status: 403 };

    return data;
}

// ===== REGISTER =====
async function registerUser(name, email, password, password_confirmation) {
    const res = await fetch(`${API_URL}/register`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify({ name, email, password, password_confirmation }),
    });

    const data = await res.json().catch(() => null);

    if (!res.ok) {
        throw {
            status: res.status,
            message: data?.message || "Registrasi gagal",
        };
    }

    return data;
}

// ===== FETCH ADMINS =====
async function fetchAdmins() {
    try {
        const res = await fetch(`${API_URL}/admins`, {
            headers: { Authorization: `Bearer ${token}` },
        });
        if (!res.ok) throw new Error("Gagal mengambil data admin");
        const data = await res.json();
        return data.data || data;
    } catch (err) {
        console.error("fetchAdmins error:", err);
        return [];
    }
}

// Expose fetchAdmins globally
window.fetchAdmins = fetchAdmins;

// ===== ADMIN ACTIONS =====
function approveAdmin(id, name) {
    Swal.fire({
        title: `Setujui admin ${name}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, Setujui",
        cancelButtonText: "Batal"
    }).then((res) => {
        if (res.isConfirmed)
            fetch(`${API_URL}/admins/${id}/approve`, {
                method: "PUT",
                headers: { Authorization: `Bearer ${token}` },
            })
                .then(() => {
                    Swal.fire("Berhasil!", `Admin ${name} telah disetujui.`, "success");
                    if (typeof window.populateAdminTable === 'function') {
                        window.populateAdminTable(); 
                    } else {
                        location.reload();
                    }
                })
                .catch(() =>
                    Swal.fire("Gagal!", "Gagal menyetujui admin.", "error")
                );
    });
}

function rejectAdmin(id, name) {
    Swal.fire({
        title: `Tolak permintaan admin ${name}?`,
        text: "Data akan dihapus permanen.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Tolak",
        cancelButtonText: "Batal",
        confirmButtonColor: "#d33"
    }).then((res) => {
        if (res.isConfirmed)
            fetch(`${API_URL}/admins/${id}/reject`, {
                method: "DELETE",
                headers: { Authorization: `Bearer ${token}` },
            })
                .then(() => {
                    Swal.fire("Ditolak!", `Permintaan admin ${name} telah ditolak.`, "success");
                    if (typeof window.populateAdminTable === 'function') {
                        window.populateAdminTable(); 
                    } else {
                        location.reload();
                    }
                })
                .catch(() =>
                    Swal.fire("Gagal!", "Gagal menolak admin.", "error")
                );
    });
}

function deleteAdmin(id, name) {
    Swal.fire({
        title: `Hapus admin ${name}?`,
        text: "Tindakan ini tidak dapat dibatalkan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
        confirmButtonColor: "#d33"
    }).then((res) => {
        if (res.isConfirmed) {
            fetch(`${API_URL}/admins/${id}/reject`, {
                method: "DELETE",
                headers: { Authorization: `Bearer ${token}` },
            })
                .then(() => {
                    Swal.fire("Dihapus!", `Admin ${name} telah dihapus.`, "success");
                    if (typeof window.populateAdminTable === 'function') {
                        window.populateAdminTable(); 
                    } else {
                        location.reload();
                    }
                })
                .catch(() =>
                    Swal.fire("Gagal!", "Gagal menghapus admin.", "error")
                );
        }
    });
}

// ===== DOM CONTENT LOADED =====
document.addEventListener("DOMContentLoaded", () => {
    // ELEMENT ALERT
    const alertBox = document.getElementById("alertBox");

    function showAlert(type, message) {
        if (!alertBox) return;
        alertBox.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }

    // ===== LOGIN BUTTON =====
    document
        .querySelector(".btn-login")
        ?.addEventListener("click", async (e) => {
            e.preventDefault();

            const email = document.getElementById("email")?.value;
            const password = document.getElementById("password")?.value;

            alertBox.innerHTML = ""; // reset alert

            if (!email || !password) {
                showAlert("danger", "Email dan password wajib diisi.");
                return;
            }

            try {
                const data = await loginUser(email, password);

                if (data.user?.status?.toLowerCase() === "pending") {
                    showAlert(
                        "warning",
                        "Akun Anda belum disetujui oleh owner!"
                    );
                    return;
                }

                showAlert("success", "Login berhasil!");
                localStorage.setItem("token", data.token);
                localStorage.setItem("user", JSON.stringify(data.user));
                localStorage.setItem("role", data.user.role);
                localStorage.setItem("user_name", data.user.name);
                localStorage.setItem("user_role", data.user.role);

                setTimeout(() => {
                    window.location.href = "/dashboard";
                }, 1500);
            } catch (err) {
                if (err.status === 401) {
                    showAlert("danger", "Email atau password salah!");
                    return;
                }
                if (err.status === 403) {
                    showAlert(
                        "warning",
                        err.message || "Akun belum disetujui!"
                    );
                    return;
                }
                showAlert(
                    "danger",
                    err.message || "Terjadi kesalahan saat login."
                );
            }
        });

    // ===== REGISTER BUTTON =====
    document
        .querySelector(".btn-register")
        ?.addEventListener("click", async (e) => {
            e.preventDefault();

            const name = document.getElementById("name")?.value;
            const email = document.getElementById("email")?.value;
            const password = document.getElementById("password")?.value;
            const password_confirmation = document.getElementById(
                "password_confirmation"
            )?.value;

            alertBox.innerHTML = "";

            if (!name || !email || !password || !password_confirmation) {
                showAlert("danger", "Semua field wajib diisi.");
                return;
            }

            if (password !== password_confirmation) {
                showAlert("danger", "Silakan masukkan password yang sama.");
                return;
            }

            try {
                const res = await registerUser(
                    name,
                    email,
                    password,
                    password_confirmation
                );

                showAlert("success", "Register berhasil!");
                setTimeout(() => (window.location.href = "/"), 1500);
            } catch (err) {
                const errorStatus = err.status || err?.statusCode;
                if (errorStatus === 422) {
                    showAlert(
                        "danger",
                        "Email sudah terdaftar, silahkan ganti email!"
                    );
                    return;
                }
                showAlert("danger", "Registrasi gagal, silahkan coba lagi!");
            }
        });

    // ===== USER AVATAR & ROLE CHECK =====
    setTimeout(() => {
        const nameEl = document.getElementById("user-name");
        const avatarEl = document.getElementById("user-avatar");

        const userStr = localStorage.getItem("user");
        
        if (userStr) {
            const user = JSON.parse(userStr);

            const userName = user.name || "User";
            const userRole = user.role || "admin";

            if (nameEl) nameEl.textContent = userName;
            if (avatarEl) {
                const initials = userName
                    .split(" ")
                    .map((n) => n[0].toUpperCase())
                    .join("")
                    .substring(0, 2);
                avatarEl.textContent = initials || "U";
            }

            if (userRole !== "owner") {
                document.querySelectorAll('[data-role="owner"]').forEach(el => el.style.display = "none");
            }
        }
    }, 100);
});