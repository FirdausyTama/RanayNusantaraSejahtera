<div class="topbar-custom">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center position-relative">

      <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
        <li>
          <button class="button-toggle-menu nav-link">
            <i data-feather="menu" class="noti-icon"></i>
          </button>
        </li>
        <li class="d-none d-lg-block">
          <h5 class="mb-0">Selamat Datang, RNS</h5>
        </li>

        <li class="d-block d-lg-none text-center w-100 mobile-logo">
          <img
            src="{{ asset('assets/images/hp-logo.png') }}"
            alt="Logo HP"
            class="hp-logo" />
        </li>
      </ul>

      <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

        <li class="d-none d-sm-flex">
          <button type="button" class="btn nav-link" data-toggle="fullscreen">
            <i data-feather="maximize" class="align-middle fullscreen noti-icon"></i>
          </button>
        </li>
        <li class="d-none d-sm-flex">
          <button type="button" class="btn nav-link" id="light-dark-mode">
            <i data-feather="moon" class="align-middle dark-mode"></i>
            <i data-feather="sun" class="align-middle light-mode"></i>
          </button>
        </li>

<li class="dropdown notification-list topbar-dropdown">
  <a class="nav-link dropdown-toggle nav-user me-0 d-flex align-items-center"
     data-bs-toggle="dropdown"
     href="#"
     role="button"
     aria-haspopup="false"
     aria-expanded="false">

    
    <div id="user-avatar"
      class="rounded-circle text-center bg-primary text-white fw-bold"
      style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
      U
    </div>

    
    <span id="user-name" class="ms-1 fw-semibold text-dark d-lg-inline">
      User
    </span>
  </a>

  <div class="dropdown-menu dropdown-menu-end profile-dropdown">
    <a href="#" class="dropdown-item notify-item" id="logoutBtn">
      <i class="mdi mdi-location-exit fs-16 align-middle"></i>
      <span>Logout</span>
    </a>
  </div>
</li>

      </ul>
    </div>
  </div>
</div>

<div class="app-sidebar-menu">
  <div class="h-100" data-simplebar>
    <div id="sidebar-menu">

      <div class="logo-box">
        <a href="/" class="logo logo-light">
          <span class="logo-sm d-flex align-items-center">
            <img
              src="{{ asset('assets/images/logo-rns-bg.png') }}"
              alt="Logo Light"
              height="40"
              class="me-2" />
            <span class="fw-bold text-white fs-5">Dashbor</span>
          </span>
        </a>

        <a href="/" class="logo logo-dark">
          <span class="logo-sm d-flex align-items-center">
            <img
              src="{{ asset('assets/images/logo-rns-bg.png') }}"
              alt="Logo Dark"
              height="40"
              class="me-2" />
            <span class="fw-bold text-dark fs-5">Dashbor</span>
          </span>
        </a>
      </div>

      <ul id="side-menu">
        <li class="menu-title">Menu</li>

        <li>
          <a href="/dashboard" class="{{ Request::is('dashboard') ? 'active' : '' }}">
            <i data-feather="home"></i>
            <span>Dashbor</span>
          </a>
        </li>

        <li>
          <a href="/kelola-stok" class="{{ Request::is('kelola-stok*') ? 'active' : '' }}">
            <i data-feather="box"></i>
            <span>Kelola Stok</span>
          </a>
        </li>

        <li>
          <a href="/kelola-pembelian" class="{{ Request::is('kelola-pembelian*') ? 'active' : '' }}">
            <i data-feather="shopping-cart"></i>
            <span>Kelola Penjualan</span>
          </a>
        </li>

        <li>
          <a href="/riwayat-pembelian" class="{{ Request::is('riwayat-pembelian*') ? 'active' : '' }}">
            <i data-feather="file-text"></i>
            <span>Riwayat Penjualan</span>
          </a>
        </li>

        <li>
          <a href="#sidebarDokumen" data-bs-toggle="collapse">
            <i data-feather="folder"></i>
            <span>Dokumen</span>
            <span class="menu-arrow"></span>
          </a>
          <div class="collapse" id="sidebarDokumen">
            <ul class="nav-second-level">
              <li><a href="/sph" class="tp-link">Surat Penawaran Harga</a></li>
              <li><a href="/invoice" class="tp-link">Surat Invoice</a></li>
              <li><a href="/kwitansi" class="tp-link">Surat Kwitansi</a></li>
              <li><a href="/surat-jalan" class="tp-link">Surat Jalan</a></li>
            </ul>
          </div>
        </li>

        <li data-role="owner">
          <span class="menu-title">Autentikasi</span>
          <a href="#sidebarAuth" data-bs-toggle="collapse">
            <i data-feather="users"></i>
            <span>Kelola Admin</span>
            <span class="menu-arrow"></span>
          </a>
          <div class="collapse" id="sidebarAuth">
            <ul class="nav-second-level">
              <li><a href="kelola-admin" class="tp-link">List Admin</a></li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/auth.js') }}"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) {
      logoutBtn.addEventListener("click", function(e) {
        e.preventDefault();
        Swal.fire({
          title: "Apakah Anda yakin ingin logout?",
          text: "Anda akan keluar dari sesi ini.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Ya, Logout",
          cancelButtonText: "Batal",
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "/logout";
          }
        });
      });
    }
  });
</script>
<script>
  const role = localStorage.getItem("role");
  if (role !== "owner") {
    document.querySelectorAll('[data-role="owner"]').forEach(el => {
      el.style.display = "none";
    });
  }
</script>
<style>
  .mobile-logo {
    pointer-events: none;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -45%);
    z-index: 0;
  }

  .hp-logo {
    height: 36px;
    width: auto;
    object-fit: contain;
  }

  @media (min-width: 992px) {
    .mobile-logo {
      display: none !important;
    }
  }

  @media (max-width: 991px) {
    .topbar-custom {
      position: relative;
      padding-top: 8px;
      padding-bottom: 8px;
    }

    .content-page {
      margin-top: 0 !important;
    }

    .topbar-custom h5 {
      margin-bottom: 0;
      font-size: 14px;
    }

    .pro-user-name {
      display: none !important;
    }
  }
</style>