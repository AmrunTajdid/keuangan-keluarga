@extends('layouts.app')

@section('title', 'Dashboard Keuangan Keluarga')
@section('page_title', 'Ringkasan Finansial Keluarga')
@section('page_subtitle', 'Periode ' . $monthName)

@section('content')
<div class="space-y-6">

    <!-- Header Filter & Quick Action -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="flex items-center space-x-2">
                <label for="month" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bulan:</label>
                <select name="month" id="month" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3 py-1.5 font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <label for="year" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tahun:</label>
                <select name="year" id="year" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3 py-1.5 font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @for($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>

        <div class="flex items-center space-x-2 w-full md:w-auto justify-end">
            <a href="{{ route('budgets.index', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                🎯 Kelola Anggaran Bulan Ini
            </a>
            <a href="{{ route('transactions.create') }}" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors">
                + Tambah Transaksi
            </a>
        </div>
    </div>

    <!-- 4 Main Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Total Net Worth / Balance -->
        <div class="bg-blue-600 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-5 text-white shadow-md shadow-blue-500/20 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-blue-100 uppercase tracking-wider">Total Saldo Semua Kas</span>
                <span class="p-2 rounded-xl bg-white/10 text-white text-base">🏦</span>
            </div>
            <h3 class="text-2xl font-black tracking-tight mb-2">@rupiah($totalBalance)</h3>
            <p class="text-xs text-blue-100 flex items-center space-x-1">
                <span>Total saldo di {{ $wallets->count() }} dompet & rekening</span>
            </p>
        </div>

        <!-- Monthly Income -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemasukan Bulan Ini</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 text-base font-bold">📥</span>
            </div>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">@rupiah($monthlyIncome)</h3>
            <p class="text-xs text-emerald-600 font-semibold flex items-center space-x-1">
                <span>{{ $monthlyIncome > 0 ? '✓ Arus kas masuk tercatat' : 'Belum ada pemasukan' }}</span>
            </p>
        </div>

        <!-- Monthly Expense -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengeluaran Bulan Ini</span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600 text-base font-bold">📤</span>
            </div>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">@rupiah($monthlyExpense)</h3>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">Batas Anggaran:</span>
                <span class="font-bold text-slate-700">@rupiah($totalBudgetLimit)</span>
            </div>
        </div>

        <!-- Net Cashflow & Savings Rate -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sisa Tabungan (Net)</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 text-base font-bold">💎</span>
            </div>
            <h3 class="text-2xl font-black {{ $monthlyNet >= 0 ? 'text-indigo-600' : 'text-rose-600' }} tracking-tight mb-2">
                @rupiah($monthlyNet)
            </h3>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">Savings Rate:</span>
                <span class="font-bold {{ $savingsRate >= 20 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $savingsRate }}%</span>
            </div>
        </div>

    </div>

    <!-- Visual Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Chart 1: 6-Month Cashflow Trend -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h4 class="font-bold text-slate-900 text-base">Tren Arus Kas (6 Bulan Terakhir)</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Perbandingan Pemasukan vs Pengeluaran bulanan</p>
                </div>
                <div class="flex items-center space-x-3 text-xs font-medium">
                    <span class="inline-flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5"></span> Pemasukan</span>
                    <span class="inline-flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-1.5"></span> Pengeluaran</span>
                </div>
            </div>
            <div class="h-72">
                <canvas id="cashflowTrendChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Expense Breakdown by Category -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <h4 class="font-bold text-slate-900 text-base mb-1">Proporsi Kategori</h4>
                <p class="text-xs text-slate-400 mb-4">Pengeluaran terbesar di {{ $monthName }}</p>
                
                @if($categoriesExpense->count() > 0)
                    <div class="h-56 relative flex items-center justify-center">
                        <canvas id="categoryExpenseChart"></canvas>
                    </div>
                @else
                    <div class="h-56 flex flex-col items-center justify-center text-center text-slate-400">
                        <span class="text-3xl mb-2">🍽️</span>
                        <p class="text-xs">Belum ada pengeluaran pada periode ini</p>
                    </div>
                @endif
            </div>

            <!-- List Categories Top 3 -->
            @if($categoriesExpense->count() > 0)
                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                    @foreach($categoriesExpense->take(3) as $cat)
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center space-x-2 text-slate-700 font-medium truncate max-w-[150px]">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cat['color'] }}"></span>
                                <span class="truncate">{{ $cat['name'] }}</span>
                            </span>
                            <span class="font-bold text-slate-900">@rupiah($cat['total'])</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Row 3: Budget Monitoring & Member Contribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Budget vs Actual Progress (2 columns) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="font-bold text-slate-900 text-base">Batas Anggaran vs Realisasi</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Pantau pengeluaran agar tidak melewati alokasi bulanan</p>
                </div>
                <a href="{{ route('budgets.index', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($budgets->isEmpty())
                <div class="text-center py-8 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <p class="text-slate-500 text-sm mb-3">Belum ada batas anggaran yang ditetapkan untuk bulan {{ $monthName }}.</p>
                    <a href="{{ route('budgets.index', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition-colors">
                        + Buat Anggaran Sekarang
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($budgets->take(5) as $budget)
                        @php
                            $spent = $budget->spent_amount;
                            $limit = (float) $budget->amount;
                            $pct = $budget->usage_percentage;
                            $isOver = $spent > $limit;
                            $barColor = $isOver ? 'bg-rose-500' : ($pct >= 80 ? 'bg-amber-500' : 'bg-emerald-500');
                            $badgeColor = $isOver ? 'bg-rose-50 text-rose-700 border-rose-200' : ($pct >= 80 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                        @endphp
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 hover:bg-slate-100/50 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2.5">
                                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $budget->category->color ?? '#3B82F6' }}"></span>
                                    <span class="font-bold text-slate-900 text-sm">{{ $budget->category->name }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-lg border {{ $badgeColor }}">
                                        {{ $pct }}%
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        @rupiah($spent) / <span class="font-semibold text-slate-800">@rupiah($limit)</span>
                                    </span>
                                </div>
                            </div>
                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                                <div class="{{ $barColor }} h-full rounded-full transition-all duration-500" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                            @if($isOver)
                                <p class="text-[11px] font-semibold text-rose-600 mt-1.5 flex items-center space-x-1">
                                    <span>⚠️ Melebihi anggaran sebesar @rupiah($spent - $limit)!</span>
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Family Member Breakdown (1 column) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Pengeluaran Keluarga</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Proporsi per anggota keluarga</p>
                    </div>
                    <a href="{{ route('family-members.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                        Kelola
                    </a>
                </div>

                @if($familyExpense->count() > 0)
                    <div class="h-44 relative flex items-center justify-center mb-4">
                        <canvas id="familyExpenseChart"></canvas>
                    </div>
                    <div class="space-y-2">
                        @foreach($familyExpense as $fe)
                            <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100 last:border-none">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $fe['color'] }}"></span>
                                    <span class="text-slate-700 font-medium">{{ $fe['name'] }}</span>
                                </div>
                                <span class="font-bold text-slate-900">@rupiah($fe['total'])</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-slate-400">
                        <p class="text-xs">Belum ada pengeluaran per anggota</p>
                    </div>
                @endif
            </div>

            <!-- Wallets Quick Preview -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Dompet & Rekening</span>
                    <a href="{{ route('wallets.index') }}" class="text-xs text-blue-600 font-semibold">Semua &rarr;</a>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($wallets->take(4) as $w)
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <p class="text-[11px] text-slate-500 truncate font-medium">{{ $w->name }}</p>
                            <p class="text-xs font-bold text-slate-800 mt-0.5">@rupiah($w->current_balance)</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- Row 4: Savings Goals & Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Savings Goals (1 Column) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="font-bold text-slate-900 text-base">🎯 Target Tabungan</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Rencana masa depan keluarga</p>
                </div>
                <a href="{{ route('savings-goals.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($savingsGoals->isEmpty())
                <div class="text-center py-8 text-slate-400 text-xs">
                    Belum ada target tabungan impian.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($savingsGoals as $goal)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-slate-800 text-xs">{{ $goal->name }}</span>
                                <span class="text-xs font-extrabold text-blue-600">{{ $goal->percentage }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden mb-2">
                                <div class="h-full rounded-full" style="background-color: {{ $goal->color }}; width: {{ $goal->percentage }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span>Terkumpul: <strong class="text-slate-700">@rupiah($goal->current_amount)</strong></span>
                                <span>Target: @rupiah($goal->target_amount)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Transactions Table (2 Columns) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="font-bold text-slate-900 text-base">💳 Transaksi Terakhir</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Catatan aktivitas keuangan paling baru</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Semua Transaksi &rarr;
                </a>
            </div>

            @if($recentTransactions->isEmpty())
                <div class="text-center py-10 text-slate-400 text-xs">
                    Belum ada transaksi tercatat.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold text-[10px]">
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">Keterangan</th>
                                <th class="py-2.5 px-3">Anggota</th>
                                <th class="py-2.5 px-3">Dompet</th>
                                <th class="py-2.5 px-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($recentTransactions as $tx)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 px-3 whitespace-nowrap text-slate-500 font-medium">
                                        {{ $tx->transaction_date->format('d M Y') }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-semibold text-slate-900">{{ $tx->description }}</div>
                                        <div class="text-[11px] text-slate-400 flex items-center space-x-1.5 mt-0.5">
                                            @if($tx->type === 'transfer')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    Transfer
                                                </span>
                                                <span>{{ $tx->wallet->name }} &rarr; {{ $tx->toWallet->name ?? 'Tujuan' }}</span>
                                            @else
                                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $tx->category->color ?? '#94A3B8' }}"></span>
                                                <span>{{ $tx->category->name ?? 'Umum' }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($tx->familyMember)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium" style="background-color: {{ $tx->familyMember->avatar_color }}15; color: {{ $tx->familyMember->avatar_color }}">
                                                {{ $tx->familyMember->name }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap text-slate-500 font-medium">
                                        {{ $tx->wallet->name }}
                                    </td>
                                    <td class="py-3 px-3 text-right whitespace-nowrap font-extrabold {{ $tx->type === 'income' ? 'text-emerald-600' : ($tx->type === 'expense' ? 'text-rose-600' : 'text-indigo-600') }}">
                                        {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '-' : '') }}@rupiah($tx->amount)
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Cashflow Trend Chart (6 Months)
        const ctxCashflow = document.getElementById('cashflowTrendChart');
        if (ctxCashflow) {
            new Chart(ctxCashflow, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($cashflowLabels) !!},
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: {!! json_encode($cashflowIncomeData) !!},
                            backgroundColor: '#10B981',
                            borderRadius: 8,
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode($cashflowExpenseData) !!},
                            backgroundColor: '#EF4444',
                            borderRadius: 8,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value >= 1000000 ? (value / 1000000) + ' Jt' : value.toLocaleString('id-ID'));
                                }
                            },
                            grid: { color: '#F1F5F9' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // 2. Category Expense Chart (Doughnut)
        const ctxCategory = document.getElementById('categoryExpenseChart');
        if (ctxCategory) {
            const catData = {!! json_encode($categoriesExpense) !!};
            if (catData.length > 0) {
                new Chart(ctxCategory, {
                    type: 'doughnut',
                    data: {
                        labels: catData.map(c => c.name),
                        datasets: [{
                            data: catData.map(c => c.total),
                            backgroundColor: catData.map(c => c.color || '#3B82F6'),
                            borderWidth: 2,
                            borderColor: '#FFFFFF',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        cutout: '70%',
                    }
                });
            }
        }

        // 3. Family Member Expense Chart (Doughnut)
        const ctxFamily = document.getElementById('familyExpenseChart');
        if (ctxFamily) {
            const famData = {!! json_encode($familyExpense) !!};
            if (famData.length > 0) {
                new Chart(ctxFamily, {
                    type: 'doughnut',
                    data: {
                        labels: famData.map(f => f.name),
                        datasets: [{
                            data: famData.map(f => f.total),
                            backgroundColor: famData.map(f => f.color || '#EC4899'),
                            borderWidth: 2,
                            borderColor: '#FFFFFF',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        cutout: '65%',
                    }
                });
            }
        }
    });
</script>
@endpush
