@extends('layouts.app')

@section('title', 'Pengaturan Database')
@section('page_title', 'Status & Konfigurasi Database')
@section('page_subtitle', 'Kelola koneksi penyimpanan data agar tidak hilang saat logout')

@section('content')
<div class="space-y-6 max-w-4xl">

    <!-- Status Banner -->
    @if($isSqlite && $isVercel)
        <div class="p-5 rounded-3xl bg-amber-50 border-2 border-amber-300 text-amber-950 shadow-sm">
            <div class="flex items-start space-x-3">
                <span class="text-2xl">⚠️</span>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-amber-900">Peringatan: Mode Penyimpanan Sementara (Vercel SQLite)</h3>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        Saat ini website berjalan di <strong>Vercel Serverless</strong> dengan file SQLite sementara di <code>/tmp</code>. 
                        Karena Vercel tidak memiliki harddisk permanen, <strong>data transaksi & anggaran yang Anda input akan di-reset (hilang) setiap kali server tertidur atau Anda logout</strong>.
                    </p>
                    <p class="text-xs font-semibold text-amber-900 pt-1">
                        👉 Ikuti panduan di bawah untuk menyambungkan ke <strong>Cloud Database (Neon PostgreSQL)</strong> gratis selamanya agar data tersimpan permanen!
                    </p>
                </div>
            </div>
        </div>
    @elseif(!$isSqlite)
        <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">✅</span>
                <div>
                    <h3 class="text-base font-bold text-emerald-900">Database Cloud Terhubung!</h3>
                    <p class="text-xs text-emerald-800">
                        Koneksi database saat ini: <span class="font-mono font-bold uppercase bg-emerald-100 px-2 py-0.5 rounded text-emerald-900">{{ $connection }}</span>. Data tersimpan permanen dan tidak akan hilang saat logout.
                    </p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full bg-emerald-200 text-emerald-800 text-xs font-bold">Permanen</span>
        </div>
    @else
        <div class="p-5 rounded-3xl bg-blue-50 border border-blue-200 text-blue-950 shadow-sm">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">💻</span>
                <div>
                    <h3 class="text-base font-bold text-blue-900">Mode Lokal (Localhost / XAMPP)</h3>
                    <p class="text-xs text-blue-800">
                        Database SQLite tersimpan di harddisk lokal komputer Anda. Data tersimpan permanen selama dijalankan di komputer ini.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Data Summary Cards -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Informasi Database Aktif</h3>
                <p class="text-xs text-slate-500">Detail status tabel dan data yang saat ini tersimpan</p>
            </div>
            <div class="text-right">
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">Driver: {{ $connection }}</span>
            </div>
        </div>

        @if($dbStatus === 'ok')
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 pt-1">
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <span class="text-[11px] text-slate-500 font-medium">Transaksi</span>
                    <p class="text-lg font-black text-slate-800">{{ $tableCounts['transactions'] ?? 0 }}</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <span class="text-[11px] text-slate-500 font-medium">Anggaran</span>
                    <p class="text-lg font-black text-slate-800">{{ $tableCounts['budgets'] ?? 0 }}</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <span class="text-[11px] text-slate-500 font-medium">Dompet/Akun</span>
                    <p class="text-lg font-black text-slate-800">{{ $tableCounts['wallets'] ?? 0 }}</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <span class="text-[11px] text-slate-500 font-medium">Kategori</span>
                    <p class="text-lg font-black text-slate-800">{{ $tableCounts['categories'] ?? 0 }}</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <span class="text-[11px] text-slate-500 font-medium">Anggota</span>
                    <p class="text-lg font-black text-slate-800">{{ $tableCounts['family_members'] ?? 0 }}</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <span class="text-[11px] text-slate-500 font-medium">Target Tabungan</span>
                    <p class="text-lg font-black text-slate-800">{{ $tableCounts['savings_goals'] ?? 0 }}</p>
                </div>
            </div>
        @else
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs">
                <strong>Error Koneksi Database:</strong> {{ $dbError }}
            </div>
        @endif

        <!-- Tombol Eksekusi Migrasi -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold text-slate-800">Inisialisasi / Perbarui Tabel Database</p>
                <p class="text-[11px] text-slate-500">Jalankan migrasi jika Anda baru saja menyambungkan database baru</p>
            </div>
            <form method="POST" action="{{ route('system.migrate') }}" onsubmit="return confirm('Apakah Anda yakin ingin menjalankan migrasi dan inisialisasi tabel?')">
                @csrf
                <input type="hidden" name="seed" value="1">
                <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center space-x-2">
                    <span>⚡</span>
                    <span>Jalankan Migrasi & Data Awal</span>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
