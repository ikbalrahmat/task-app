<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENTIMEN | Terjadi Kesalahan</title>
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fff1f2 0%, #fce7f3 50%, #fef3c7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: white;
            border-radius: 24px;
            padding: 56px 48px;
            text-align: center;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(220,38,38,0.10);
        }
        .icon { font-size: 4rem; margin-bottom: 20px; }
        .code { font-size: 5rem; font-weight: 900; color: #fee2e2; line-height: 1; margin-bottom: 8px; }
        h1 { font-size: 1.4rem; font-weight: 700; color: #991b1b; margin-bottom: 12px; }
        p { color: #64748b; font-size: 0.9rem; line-height: 1.7; margin-bottom: 32px; }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            padding: 12px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.85; }
        .brand { margin-top: 32px; font-size: 0.75rem; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⚠️</div>
        <div class="code">500</div>
        <h1>Terjadi Kesalahan pada Server</h1>
        <p>Sistem mengalami gangguan sementara. Tim administrator telah diberitahu. Silakan coba beberapa saat lagi.</p>
        <a href="{{ url('/dashboard') }}" class="btn">← Kembali ke Dashboard</a>
        <div class="brand">SENTIMEN — Sistem Evaluasi & Monitoring</div>
    </div>
</body>
</html>
