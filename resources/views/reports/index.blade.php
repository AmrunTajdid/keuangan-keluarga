@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('page_title', 'Laporan & Analisis Finansial')
@section('page_subtitle', 'Laporan berkala arus kas keluarga yang dapat dicetak atau diarsipkan dalam bentuk PDF')

@section('content')
<div class="space-y-6">

    <!-- Filter & Print Action (Hidden on Print) -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4 no-print">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="flex items-center space-x-2">
                <label for="month" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bulan:</label>
                <select name="month" id="month" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2 font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <label for="year" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tahun:</label>
                <select name="year" id="year" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2 font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @for($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>

        <button onclick="window.print()" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center space-x-2 transition-colors">
            <span>🖨️</span>
            <span>Cetak Laporan / Simpan PDF</span>
        </button>
    </div>

    <!-- Printable Report Container -->
    <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200/80 shadow-xs space-y-8">
        
        <!-- Report Header -->
        <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2.5 mb-1">
                    <span class="text-2xl">💰</span>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">LAPORAN KEUANGAN KELUARGA</h2>
                </div>
                <p class="text-xs text-slate-500 font-medium">Periode Laporan: <strong class="text-slate-800 font-bold uppercase">{{ $monthName }}</strong></p>
            </div>
            <div class="text-left sm:text-right text-xs text-slate-400">
                <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
                <p class="font-semibold text-slate-600 mt-0.5">Status: Buku Kas Terverifikasi</p>
            </div>
        </div>

        <!-- KPI 3 Summary Boxes -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Arus Masuk (Income)</span>
                <h4 class="text-xl font-black text-emerald-600 mt-1">@rupiah($totalIncome)</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $incomeTransactions->count() }} transaksi pemasukan</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Arus Keluar (Expense)</span>
                <h4 class="text-xl font-black text-rose-600 mt-1">@rupiah($totalExpense)</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $expenseTransactions->count() }} transaksi pengeluaran</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sisa Tabungan Bersih (Net)</span>
                <h4 class="text-xl font-black {{ $netCashflow >= 0 ? 'text-blue-600' : 'text-rose-600' }} mt-1">
                    @rupiah($netCashflow)
                </h4>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Rasio tabungan: <strong class="text-slate-700">{{ $totalIncome > 0 ? round(($netCashflow / $totalIncome) * 100, 1) : 0 }}%</strong>
                </p>
            </div>
        </div>

        <!-- 2 Columns: Category Breakdown & Family Member Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Category Breakdown Table -->
            <div class="space-y-3">
                <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Rincian Pengeluaran per Kategori</h4>
                @if($expensesByCategory->isEmpty())
                    <p class="text-xs text-slate-400">Tidak ada pengeluaran pada bulan ini.</p>
                @else
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-slate-400 uppercase text-[10px] font-bold border-b border-slate-100">
                                <th class="pb-2">Kategori</th>
                                <th class="pb-2 text-right">Nominal</th>
                                <th class="pb-2 text-right">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($expensesByCategory as $cat)
                                @php
                                    $pct = $totalExpense > 0 ? round(($cat['total'] / $totalExpense) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td class="py-2.5 font-medium text-slate-800 flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cat['color'] }}"></span>
                                        <span>{{ $cat['name'] }}</span>
                                    </td>
                                    <td class="py-2.5 text-right font-bold text-slate-900">@rupiah($cat['total'])</td>
                                    <td class="py-2.5 text-right font-semibold text-slate-500">{{ $pct }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Family Member Contribution Table -->
            <div class="space-y-3">
                <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Rincian Pengeluaran per Anggota</h4>
                @if($expensesByMember->isEmpty())
                    <p class="text-xs text-slate-400">Tidak ada data anggota keluarga.</p>
                @else
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-slate-400 uppercase text-[10px] font-bold border-b border-slate-100">
                                <th class="pb-2">Anggota</th>
                                <th class="pb-2 text-right">Nominal</th>
                                <th class="pb-2 text-right">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($expensesByMember as $mem)
                                @php
                                    $pct = $totalExpense > 0 ? round(($mem['total'] / $totalExpense) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td class="py-2.5 font-medium text-slate-800 flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $mem['color'] }}"></span>
                                        <span>{{ $mem['name'] }}</span>
                                    </td>
                                    <td class="py-2.5 text-right font-bold text-slate-900">@rupiah($mem['total'])</td>
                                    <td class="py-2.5 text-right font-semibold text-slate-500">{{ $pct }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>

        <!-- Detailed Transaction Table for the month -->
        <div class="space-y-3 pt-4 border-t border-slate-200">
            <h4 class="font-bold text-slate-900 text-sm">Buku Catatan Transaksi Bulan {{ $monthName }}</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px]">
                            <th class="py-2 px-3">Tgl</th>
                            <th class="py-2 px-3">Jenis</th>
                            <th class="py-2 px-3">Kategori</th>
                            <th class="py-2 px-3">Keterangan</th>
                            <th class="py-2 px-3">Anggota</th>
                            <th class="py-2 px-3">Dompet</th>
                            <th class="py-2 px-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $allTx = $incomeTransactions->concat($expenseTransactions)->sortBy('transaction_date');
                        @endphp
                        @forelse($allTx as $tx)
                            <tr>
                                <td class="py-2 px-3 whitespace-nowrap text-slate-500 font-medium">{{ $tx->transaction_date->format('d/m') }}</td>
                                <td class="py-2 px-3 whitespace-nowrap">
                                    <span class="font-bold text-[10px] {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $tx->type === 'income' ? 'MASUK' : 'KELUAR' }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 whitespace-nowrap text-slate-700 font-medium">{{ $tx->category ? $tx->category->name : '-' }}</td>
                                <td class="py-2 px-3 text-slate-900 font-medium">{{ $tx->description }}</td>
                                <td class="py-2 px-3 whitespace-nowrap text-slate-600">{{ $tx->familyMember ? $tx->familyMember->name : '-' }}</td>
                                <td class="py-2 px-3 whitespace-nowrap text-slate-500">{{ $tx->wallet->name }}</td>
                                <td class="py-2 px-3 text-right whitespace-nowrap font-bold {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $tx->type === 'income' ? '+' : '-' }}@rupiah($tx->amount)
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400">Tidak ada catatan transaksi pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Signature section for print -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-600">
            <div>
                <p class="font-bold mb-12">Kepala Keluarga</p>
                <p class="font-semibold underline">( Ayah / Budi )</p>
            </div>
            <div>
                <p class="font-bold mb-12">Bendahara Keluarga</p>
                <p class="font-semibold underline">( Ibu / Siti )</p>
            </div>
        </div>

    </div>

</div>
@endsection
