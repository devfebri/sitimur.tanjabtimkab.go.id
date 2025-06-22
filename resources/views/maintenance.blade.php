<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance | Website Kami</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #007BFF, #00C6FF);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .container {
            max-width: 600px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(10px);
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-in-out;
        }

        p {
            font-size: 1.2em;
            margin-bottom: 30px;
            animation: fadeIn 3s ease-in-out;
        }

        .spinner {
            border: 6px solid rgba(255, 255, 255, 0.3);
            border-top: 6px solid #fff;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #eee;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h1>Website Sedang Maintenance</h1>
        <p>Kami sedang melakukan peningkatan sistem untuk pelayanan yang lebih baik. Silakan kembali lagi nanti.</p>
        <footer>&copy; 2025 UKPBJ Kab. Tanjung Jabung Timur</footer>
    </div>
</body>
</html>
