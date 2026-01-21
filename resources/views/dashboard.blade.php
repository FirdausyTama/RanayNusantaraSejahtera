<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title>Dashboard | RNS - Ranay Nusantara Sejathera</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc."/>
        <meta name="author" content="Zoyothemes"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        
        <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

        
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

        <script src="assets/js/head.js"></script>


    </head>
    

    
    <body data-menu-color="light" data-sidebar="default">
        @include('navbar.navbar')

        
        <div id="app-layout">
            
       

            
            
            

            <div class="content-page">
                <div class="content">

                    
                    <div class="container-fluid">
                        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="col-md-6 col-lg-4 col-xl">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="widget-first">

                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    class="p-2 border border-primary border-opacity-10 bg-primary-subtle rounded-2 me-2">
                                                    <div class="bg-primary rounded-circle widget-size text-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                            viewBox="0 0 24 24">
                                                            <path fill="#ffffff"
                                                                d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15">Total Pelanggan</p>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="mb-0 fs-22 text-dark me-3" id="totalPelanggan">0</h3>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="widget-first">

                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    class="p-2 border border-secondary border-opacity-10 bg-secondary-subtle rounded-2 me-2">
                                                    <div class="bg-secondary rounded-circle widget-size text-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                            viewBox="0 0 24 24">
                                                            <path fill="#ffffff"
                                                                d="m10 17l-5-5l1.41-1.42L10 14.17l7.59-7.59L19 8m-7-6A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15">Pembayaran Tertunda</p>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-baseline">
                                                    <h3 class="mb-0 fs-22 text-dark me-2" id="totalPendingPayment">0</h3>
                                                    <span class="text-secondary fw-medium fs-14" id="totalPendingPaymentValue">(Rp 0)</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="widget-first">

                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    class="p-2 border border-danger border-opacity-10 bg-danger-subtle rounded-2 me-2">
                                                    <div class="bg-danger rounded-circle widget-size text-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                            viewBox="0 0 24 24">
                                                            <path fill="#ffffff"
                                                                d="M22 19H2v2h20zM4 15c0 .5.2 1 .6 1.4s.9.6 1.4.6V6c-.5 0-1 .2-1.4.6S4 7.5 4 8zm9.5-9h-3c0-.4.1-.8.4-1.1s.6-.4 1.1-.4c.4 0 .8.1 1.1.4c.2.3.4.7.4 1.1M7 6v11h10V6h-2q0-1.2-.9-2.1C13.2 3 12.8 3 12 3q-1.2 0-2.1.9T9 6zm11 11c.5 0 1-.2 1.4-.6s.6-.9.6-1.4V8c0-.5-.2-1-.6-1.4S18.5 6 18 6z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15">Total Dokumen</p>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="mb-0 fs-22 text-dark me-3" id="totalDocuments">0</h3>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="widget-first">

                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    class="p-2 border border-purple border-opacity-10 rounded-2 me-2" style="background-color: #f3e6ff;">
                                                    <div class="bg-purple rounded-circle widget-size text-center d-flex align-items-center justify-content-center" style="background-color: #6f42c1 !important;">
                                                        <i class="mdi mdi-cash-plus text-white fs-4" style="line-height: normal;"></i>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15">Total Stok Masuk (Bulan Ini)</p>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="mb-0 fs-22 text-dark me-3" id="totalStokMasukMonth">Rp 0</h3>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-6 col-xl">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="widget-first">

                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    class="p-2 border border-warning border-opacity-10 bg-warning-subtle rounded-2 me-2">
                                                    <div class="bg-warning rounded-circle widget-size text-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                            viewBox="0 0 24 24">
                                                            <path fill="#ffffff"
                                                                d="M7 15h2c0 1.08 1.37 2 3 2s3-.92 3-2c0-1.1-1.04-1.5-3.24-2.03C9.64 12.44 7 11.78 7 9c0-1.79 1.47-3.31 3.5-3.82V3h3v2.18C15.53 5.69 17 7.21 17 9h-2c0-1.08-1.37-2-3-2s-3 .92-3 2c0 1.1 1.04 1.5 3.24 2.03C14.36 11.56 17 12.22 17 15c0 1.79-1.47 3.31-3.5 3.82V21h-3v-2.18C8.47 18.31 7 16.79 7 15" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-dark fs-15">Penjualan Bulan Ini</p>
                                            </div>


                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="mb-0 fs-22 text-dark me-3" id="totalSales">Rp 0</h3>


                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-8">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h5 class="card-title mb-0">Statistik Penjualan</h5>
                                                <small class="text-muted" id="totalYearSalesDisplay"></small>
                                            </div>
                                            <div class="ms-auto">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm bg-light border dropdown-toggle fw-medium" type="button" id="yearDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                                        2025 <i class="mdi mdi-chevron-down ms-1 fs-14"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end" id="yearDropdownMenu">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="sales-overview" class=""></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 col-xl-4">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <h5 class="card-title mb-0">Transaksi Terakhir</h5>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <ul class="list-group list-group-flush list-group-no-gutters" id="recentTransactionsList">
                                        </ul>
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
                                    &copy; <script>document.write(new Date().getFullYear())</script> - Made with <span class="mdi mdi-heart text-danger"></span> by <a href="#!" class="text-reset fw-semibold">TI UMY 22</a> </div>
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

        
        <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

        
        

        
        <script src="assets/js/app.js"></script>

        
        <script src="assets/js/dashboard.js"></script>

    </body>

</html>