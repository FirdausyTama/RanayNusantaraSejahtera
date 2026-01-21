<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logout | PT. Ranay Nusantara Sejahtera</title>
  <link rel="stylesheet" href="assets/css/app.min.css">
  <link rel="stylesheet" href="assets/css/icons.min.css">
  <link rel="shortcut icon" href="assets/images/favicon.ico">
  <style>
    body {
      background-color: #0d47a1;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      font-family: "Poppins", sans-serif;
    }

    .logout-card {
      background-color: #fff;
      color: #333;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      padding: 40px;
      text-align: center;
      max-width: 420px;
      width: 90%;
    }

    .logout-card img {
      width: 110px;
      margin-bottom: 20px;
    }

    .logout-card h2 {
      color: #0d47a1;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .logout-card p {
      color: #555;
      margin-bottom: 25px;
      font-size: 14px;
    }

    .btn-login {
      background-color: #f5b301;
      border: none;
      color: #fff;
      padding: 12px 20px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      width: 100%;
    }

    .btn-login:hover {
      background-color: #e0a700;
      transform: scale(1.02);
    }

    footer {
      position: fixed;
      bottom: 15px;
      font-size: 13px;
      color: #ccc;
      text-align: center;
      width: 100%;
    }
  </style>
</head>
<body>

  <div class="logout-card">
    <img src="assets/images/logo-rns-bg.png" alt="Logo PT. Ranay Nusantara Sejahtera">
    <h2>Anda Telah Keluar</h2>
    <p>Terima kasih telah menggunakan sistem PT. Ranay Nusantara Sejahtera.</p>
    <button onclick="window.location.href='/'" class="btn-login">Kembali ke Halaman Login</button>
  </div>

  <footer>
    © 2025 PT. Ranay Nusantara Sejahtera. All rights reserved.
  </footer>

</body>
</html>
