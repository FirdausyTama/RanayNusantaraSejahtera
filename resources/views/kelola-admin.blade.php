<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8" />
  <title>Admin | RNS - Ranay Nusantara Sejathera</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
  <meta name="author" content="Zoyothemes" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  
  <link rel="shortcut icon" href="assets/images/favicon.ico">

  
  <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

  
  <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

  <script src="assets/js/head.js"></script>

  <style>
    .avatar-initial {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background-color: #d9edf7;
      /* warna latar (ubah sesuai tema) */
      color: #31708f;
      /* warna teks */
      font-weight: bold;
      font-size: 14px;
      text-transform: uppercase;
    }
  </style>

</head>



<body data-menu-color="light" data-sidebar="default">
  @include('navbar.navbar')
  
  <div id="app-layout">
    
    
    

    <div class="content-page">
      <div class="content">

        
        <div class="container-fluid">

          <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
              <h4 class="fs-18 fw-semibold m-0">Kelola Admin</h4>
            </div>

            <div class="text-end">
              <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Halaman</a></li>
                <li class="breadcrumb-item active">Kelola Admin</li>
              </ol>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
              <input type="text" id="searchInput" class="form-control w-auto" placeholder="Cari admin...">
            </div>
          </div>

        </div> 
        
        
        <div class="row">
          <div class="col-12">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table align-middle" id="datatable_admin">
                    <thead class="table-light">
                      <tr>
                        <th>Foto</th>
                        <th>Nama Admin</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Terakhir Aktif</th>
                        <th class="text-end">Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="admin-table-body">
                      <tr>
                        <td colspan="6" class="text-center text-muted py-3">Memuat data...</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 

  <footer class="footer">
    <div class="container-fluid">
      <div class="row">
        <div class="col fs-13 text-muted text-center">
          &copy; <script>
            document.write(new Date().getFullYear())
          </script> - Made with <span class="mdi mdi-heart text-danger"></span> by <a href="#!" class="text-reset fw-semibold">TI UMY 22</a>
        </div>
      </div>
    </div>
  </footer>
  </div>
  </div>

  <script src="assets/libs/jquery/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/libs/simplebar/simplebar.min.js"></script>
  <script src="assets/libs/node-waves/waves.min.js"></script>
  <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
  <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
  <script src="assets/libs/feather-icons/feather.min.js"></script>

  
  <script src="assets/js/app.js"></script>
  
  <script>
    document.addEventListener("DOMContentLoaded", async () => {
      const dataTableElement = document.querySelector("#datatable_admin tbody");
      const paginationElement = document.createElement("ul");
      paginationElement.className = "pagination pagination-rounded justify-content-end mb-0";
      paginationElement.id = "pagination";
      
      // Insert pagination after table
      dataTableElement.closest('.card-body').appendChild(paginationElement);

      let allAdmins = [];
      let filteredAdmins = [];
      let currentPage = 1;
      const rowsPerPage = 10;

      function renderAdminRow(admin) {
        const initials = admin.name
          .split(" ")
          .map(n => n[0])
          .join("")
          .substring(0, 2)
          .toUpperCase();

        let statusLabel = "";
        let statusClass = "";

        if (admin.status.toLowerCase() === "active") {
          statusLabel = "Aktif";
          statusClass = "bg-success-subtle text-success";
        } else if (admin.status.toLowerCase() === "pending") {
          statusLabel = "Menunggu ACC";
          statusClass = "bg-warning-subtle text-warning";
        } else {
          statusLabel = "Tidak Aktif";
          statusClass = "bg-secondary-subtle text-muted";
        }

        const lastActive = admin.updated_at ?
          new Date(admin.updated_at).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "long",
            year: "numeric"
          }) : "-";

        return `
          <tr>
            <td><div class="avatar-initial">${initials}</div></td>
            <td>${admin.name}</td>
            <td>${admin.email}</td>
            <td><span class="badge ${statusClass}">${statusLabel}</span></td>
            <td>${lastActive}</td>
            <td class="text-end">
              ${admin.status === "pending" ? `
                <button class="btn btn-sm bg-success-subtle me-1" onclick="approveAdmin('${admin.id}', '${admin.name.replace(/'/g,"\\'")}')">
                  <i class="mdi mdi-check fs-14 text-success"></i>
                </button>
                <button class="btn btn-sm bg-danger-subtle" onclick="rejectAdmin('${admin.id}', '${admin.name.replace(/'/g,"\\'")}')">
                  <i class="mdi mdi-close fs-14 text-danger"></i>
                </button>` :
                admin.status === "active" ? `
                <button class="btn btn-sm bg-danger-subtle" onclick="deleteAdmin('${admin.id}', '${admin.name.replace(/'/g,"\\'")}')">
                  <i class="mdi mdi-delete fs-14 text-danger"></i>
                </button>` : "-"
              }
            </td>
          </tr>
        `;
      }

      function renderTable(page = 1) {
        currentPage = page;
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedItems = filteredAdmins.slice(start, end);

        dataTableElement.innerHTML = paginatedItems.length ? 
          paginatedItems.map(renderAdminRow).join("") : 
          `<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data ditemukan</td></tr>`;

        setupPagination();
      }

      function setupPagination() {
        const totalPages = Math.ceil(filteredAdmins.length / rowsPerPage);
        paginationElement.innerHTML = "";

        if (totalPages <= 1) return;

        // Prev Button
        const prevLi = document.createElement("li");
        prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
        prevLi.innerHTML = `<a class="page-link" href="javascript:void(0);" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
        prevLi.onclick = () => { if (currentPage > 1) renderTable(currentPage - 1); };
        paginationElement.appendChild(prevLi);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
          const li = document.createElement("li");
          li.className = `page-item ${currentPage === i ? "active" : ""}`;
          li.innerHTML = `<a class="page-link" href="javascript:void(0);">${i}</a>`;
          li.onclick = () => renderTable(i);
          paginationElement.appendChild(li);
        }

        // Next Button
        const nextLi = document.createElement("li");
        nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""}`;
        nextLi.innerHTML = `<a class="page-link" href="javascript:void(0);" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
        nextLi.onclick = () => { if (currentPage < totalPages) renderTable(currentPage + 1); };
        paginationElement.appendChild(nextLi);
      }

      // Search Logic
      const searchInput = document.querySelector("#searchInput");
      if (searchInput) {
        searchInput.addEventListener("input", (e) => {
          const term = e.target.value.toLowerCase();
          filteredAdmins = allAdmins.filter(admin => 
            admin.name.toLowerCase().includes(term) || 
            admin.email.toLowerCase().includes(term)
          );
          renderTable(1); // Reset to page 1 on search
        });
      }

      async function populateAdminTable() {
        dataTableElement.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-3">Memuat data...</td></tr>`;
        allAdmins = await fetchAdmins();
        filteredAdmins = [...allAdmins]; // Initialize filtered list
        renderTable(1);
        
        // Expose function globally for auth.js to call
        window.populateAdminTable = populateAdminTable;
      }

      await populateAdminTable();
    });


  </script>
</body>

</html>
