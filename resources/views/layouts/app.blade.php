<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - KeuanganKeluarga</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN & Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; }
            .sidebar, .topbar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Mobile Header -->
    <header class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-40 no-print">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-sm font-bold text-lg">
                💰
            </div>
            <div>
                <span class="font-extrabold text-slate-900 tracking-tight text-base">Keuangan<span class="text-blue-600">Keluarga</span></span>
            </div>
        </a>
        <button id="mobileMenuBtn" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </header>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out md:static md:h-screen md:shrink-0 no-print">
        <div>
            <!-- Logo Brand -->
            <div class="p-6 border-b border-slate-100 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 text-xl font-bold">
                    💰
                </div>
                <div>
                    <h1 class="font-extrabold text-slate-900 text-lg tracking-tight leading-none">Keuangan<span class="text-blue-600">Keluarga</span></h1>
                    <p class="text-xs text-slate-400 mt-1 font-medium"></p>
                </div>
            </div>

            <!-- Quick Action Button -->
            <div class="p-4">
                <a href="{{ route('transactions.create') }}" class="flex items-center justify-center space-x-2 w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-sm shadow-blue-600/30 transition-all hover:shadow-md hover:scale-[1.01] active:scale-[0.99] text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Catat Transaksi</span>
                </a>
            </div>

            <!-- Menu Links -->
            <nav class="px-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-lg">📊</span>
                    <span>Ringkasan Dashboard</span>
                </a>

                <a href="{{ route('transactions.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('transactions.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-lg">💳</span>
                    <span>Daftar Transaksi</span>
                </a>

                <a href="{{ route('budgets.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('budgets.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-lg">🎯</span>
                    <span>Anggaran Bulanan</span>
                </a>

                <a href="{{ route('savings-goals.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('savings-goals.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-lg">🌱</span>
                    <span>Rencana Tabungan</span>
                </a>

                <a href="{{ route('wallets.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('wallets.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-lg">🏦</span>
                    <span>Dompet & Rekening</span>
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pengaturan</p>
                </div>

                <a href="{{ route('categories.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-lg">🏷️</span>
                    <span>Kategori Keuangan</span>
                </a>

                <a href="{{ route('family-members.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('family-members.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-lg">👨‍👩‍👧‍👦</span>
                    <span>Anggota Keluarga</span>
                </a>

                <a href="{{ route('reports.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-lg">📄</span>
                    <span>Laporan & Cetak</span>
                </a>
            </nav>
        </div>

        <!-- Family Footer Status & Lock Button -->
        <div class="p-3 m-3 space-y-2">
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    🏡
                </div>
                <div class="text-xs">
                    <p class="font-bold text-slate-800">Keluarga Bahagia</p>
                    <p class="text-[10px] text-emerald-600 font-semibold">● Brankas Terbuka</p>
                </div>
            </div>

            <!-- Lock / Logout Form -->
            <form method="POST" action="{{ route('pin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 py-2 px-3 text-xs font-bold text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors border border-slate-200/60">
                    <span>🔒</span>
                    <span>Kunci Brankas</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay backdrop for mobile -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden md:hidden no-print"></div>

    <!-- Main Content Area -->
    <div class="main-content flex-1 flex flex-col min-h-screen overflow-x-hidden">

        <!-- Topbar -->
        <header class="topbar bg-white border-b border-slate-200 px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sticky top-0 z-30 shadow-xs no-print">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">@yield('page_title', 'Ringkasan Finansial')</h2>
                <p class="text-xs text-slate-500 mt-0.5">@yield('page_subtitle', 'Pantau arus kas, alokasi anggaran, dan target impian keluarga Anda')</p>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-3 w-full sm:w-auto justify-end">
                <!-- Date Indicator -->
                <div class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-2 rounded-xl border border-slate-200 flex items-center space-x-1.5">
                    <span>📅</span>
                    <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                </div>

                <!-- Quick Lock Button -->
                <form method="POST" action="{{ route('pin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" title="Kunci Brankas" class="px-2.5 py-2 text-xs font-bold text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-xl border border-slate-200 bg-white transition-colors flex items-center space-x-1 shadow-xs">
                        <span>🔒</span>
                        <span class="hidden sm:inline">Kunci</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Messages -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start space-x-3 shadow-xs">
                    <span class="text-lg">✅</span>
                    <div class="text-sm font-medium pt-0.5">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start space-x-3 shadow-xs">
                    <span class="text-lg">⚠️</span>
                    <div class="text-sm font-medium pt-0.5">{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 shadow-xs">
                    <div class="flex items-center space-x-2 font-semibold text-sm mb-1">
                        <span>⚠️</span>
                        <span>Harap periksa kesalahan berikut:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 text-amber-800">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-white border-t border-slate-200 px-6 py-4 text-center text-xs text-slate-400 no-print">
            &copy; {{ date('Y') }} KeuanganKeluarga - Aplikasi Manajemen Anggaran & Keuangan Rumah Tangga.
        </footer>
    </div>

    <!-- Mobile Drawer Script -->
    <script>
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (mobileBtn && sidebar && backdrop) {
            mobileBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                backdrop.classList.toggle('hidden');
            });

            backdrop.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
