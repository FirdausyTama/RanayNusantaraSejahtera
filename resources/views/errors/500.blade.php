<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koneksi Terputus</title>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        h1 { color: #dc3545; margin-bottom: 10px; }
        p { color: #6c757d; font-size: 1.1rem; }
        .icon { font-size: 4rem; margin-bottom: 20px; color: #dc3545; }
        .btn {
            background-color: #0d6efd;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
            transition: background 0.3s;
        }
        .btn:hover { background-color: #0b5ed7; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚠️</div>
        <h1>Koneksi Database Gagal</h1>
        <p>Maaf, sistem tidak dapat terhubung ke database. <br>Mohon pastikan server database (Laragon/MySQL) sudah aktif.</p>
        <a href="/" class="btn">Coba Muat Ulang</a>
    </div>
</body>
</html>
