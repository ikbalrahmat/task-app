<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow — @yield('title', 'Login')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-[#0f1117] font-sans px-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-4"
                 style="background: linear-gradient(135deg, #4f80ff, #a78bfa);">
                <span class="text-2xl">⚡</span>
            </div>
            <h1 class="text-2xl font-bold text-white">TaskFlow</h1>
            <p class="text-sm text-slate-400 mt-1">Project Manager</p>
        </div>

        {{-- Card --}}
        <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-8 shadow-2xl">
            @yield('content')
        </div>
    </div>

</body>
</html>
