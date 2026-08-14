<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENTIMEN | @yield('title', 'Login')</title>
    <link rel="icon" type="image/png" href="/images/logo.png"  media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" href="/images/logo1.png" media="(prefers-color-scheme: light)">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background: #f8f9fa;
            min-height: 100vh;
            position: relative;
            margin: 0;
            padding: 0;
        }

        .auth-container {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            min-height: 100vh;
            gap: 0;
            position: relative;
            align-items: stretch;
        }

        .auth-container::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(to right, transparent 55%, #1e3a8a 75%, #2563eb 85%, #3b82f6 100%);
            pointer-events: none;
            z-index: 1;
        }

        .auth-main {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px 40px;
            background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
            position: relative;
            z-index: 5;
        }

        .auth-main::before {
            content: '';
            position: absolute;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .auth-form-container {
            width: 100%;
            max-width: 380px;
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(30, 58, 138, 0.12);
            position: relative;
            z-index: 15;
            border: 1px solid #e0e7ff;
        }

        .auth-sidebar {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.92) 0%, rgba(37, 99, 235, 0.85) 100%), url('/images/bg-login.jpg') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 40px;
            color: white;
            position: relative;
            overflow: hidden;
            z-index: 10;
        }

        .auth-sidebar::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: 20%;
            right: -50px;
            pointer-events: none;
        }

        .auth-sidebar::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: 10%;
            left: -30px;
            pointer-events: none;
        }

        .sidebar-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 280px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 20px 50px rgba(37, 99, 235, 0.3);
        }

        .sidebar-content h1 {
            font-size: 36px;
            font-weight: 700;
            margin: 0 0 16px 0;
            letter-spacing: -0.8px;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-content p {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.8;
            max-width: 300px;
            margin: 0 auto 45px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 20px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.5;
        }

        .feature-item span {
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 2px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1e3a8a;
            margin: 0 0 10px 0;
            letter-spacing: -0.5px;
        }

        .form-header p {
            font-size: 14px;
            color: #2563eb;
            margin: 0;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
            font-size: 14px;
            border: 2px solid #e0e7ff;
            border-radius: 10px;
            background: #f0f4ff;
            color: #1a1f3a;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder {
            color: #93c5fd;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-group input.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .error-message {
            font-size: 12px;
            color: #dc2626;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-icon {
            flex-shrink: 0;
            font-size: 16px;
            margin-top: 2px;
        }

        .alert-success {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #2563eb;
            border-radius: 4px;
        }

        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-size: 13px;
            color: #1e3a8a;
            font-weight: 500;
        }

        .forgot-password {
            font-size: 13px;
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .forgot-password:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            padding: 13px 24px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            color: white;
            font-weight: 700;
            font-size: 13px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
        }

        .back-link a {
            font-size: 13px;
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .back-link a:hover {
            color: #1e40af;
        }

        @media (max-width: 1024px) {
            .auth-container {
                grid-template-columns: 1fr;
            }

            .auth-sidebar {
                padding: 40px 20px;
                min-height: 300px;
                clip-path: polygon(0 0, 100% 0, 100% 95%, 0 100%);
            }

            .feature-list {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 12px;
                justify-content: center;
                margin-top: 30px;
            }

            .feature-item {
                flex: 1;
                min-width: 150px;
                justify-content: center;
                text-align: center;
            }

            .auth-main {
                padding: 40px 30px;
            }

            .auth-form-container {
                max-width: 100%;
                padding: 40px;
            }
        }

        @media (max-width: 640px) {
            .auth-main {
                padding: 30px 20px;
            }

            .form-header h2 {
                font-size: 26px;
            }

            .sidebar-content h1 {
                font-size: 32px;
            }

            .auth-form-container {
                max-width: 100%;
                padding: 30px 20px;
                border-radius: 16px;
            }

            .auth-sidebar {
                clip-path: none;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        {{-- Main Form --}}
        <div class="auth-main">
            <div class="auth-form-container">
                @yield('content')
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="auth-sidebar">
            <div class="sidebar-content">
                <div class="logo-icon" style="background: transparent; box-shadow: none; padding: 0;">
                    <img src="/images/logo.png" alt="SENTIMEN Logo"
                         style="width: 80px; height: 80px; object-fit: contain; border-radius: 16px;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                    <div style="display:none; width:80px; height:80px; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 24px; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 20px 50px rgba(37,99,235,0.3);">
                        <svg style="width: 42px; height: 42px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
                <h1>SENTIMEN</h1>
                <p style="font-size:11px; color:rgba(255,255,255,0.55); letter-spacing:0.3px; margin-bottom:8px; margin-top:-10px;">Sistem Evaluasi dan Notifikasi Terintegrasi Monitoring</p>
                <p>Platform manajemen project terpadu untuk efisiensi dan kolaborasi tim yang lebih baik.</p>

                <div class="feature-list">
                    <div class="feature-item">
                        <span>
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div>Manajemen Program & List</div>
                    </div>
                    <div class="feature-item">
                        <span>
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div>Kolaborasi Tim & Delegasi Task</div>
                    </div>
                    <div class="feature-item">
                        <span>
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div>Pantau Progress secara Real-time</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
