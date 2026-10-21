@extends('layouts.app')

@section('title', 'Anggaran Bulanan')
@section('page_title', 'Perencanaan Anggaran Bulanan')
@section('page_subtitle', 'Tetapkan batas pengeluaran keluarga dan pantau realisasinya setiap bulan')

@section('content')
<div class="space-y-6">

    <!-- Header Filter & Action -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('budgets.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
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

        <div class="flex items-center space-x-2 w-full md:w-auto justify-end">
            <!-- Copy from Previous Month -->
            <form method="POST" action="{{ route('budgets.copy') }}" onsubmit="return confirm('Salin semua batas anggaran dari bulan sebelumnya ke bulan ini?');">
                @csrf
                <input type="hidden" name="month" value="{{ $selectedMonth }}">
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                    📋 Salin dari Bulan Lalu
                </button>
            </form>
            <button onclick="document.getElementById('modalAddBudget').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors">
                + Pasang Anggaran Kategori
            </button>
        </div>
    </div>

    <!-- Summary KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 bg-white border border-slate-200/80 rounded-3xl shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Alokasi Anggaran</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">@rupiah($totalBudget)</h3>
            <p class="text-xs text-slate-500 mt-1">Total batas untuk {{ $budgets->count() }} kategori</p>
        </div>

        <div class="p-5 bg-white border border-slate-200/80 rounded-3xl shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Realisasi Pengeluaran</span>
            <h3 class="text-2xl font-black text-rose-600 mt-1">@rupiah($totalSpent)</h3>
            <p class="text-xs text-slate-500 mt-1">
                @if($totalBudget > 0)
                    Terpakai {{ round(($totalSpent / $totalBudget) * 100, 1) }}% dari alokasi
                @else
                    Belum ada anggaran
                @endif
            </p>
        </div>

        <div class="p-5 bg-white border border-slate-200/80 rounded-3xl shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sisa Anggaran Tersedia</span>
            <h3 class="text-2xl font-black {{ ($totalBudget - $totalSpent) >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                @rupiah($totalBudget - $totalSpent)
            </h3>
            <p class="text-xs text-slate-500 mt-1">
                {{ ($totalBudget - $totalSpent) >= 0 ? 'Kondisi finansial aman terkendali' : '⚠️ Melebihi total batas anggaran!' }}
            </p>
        </div>
    </div>

    <!-- Budget List Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($categories as $cat)
            @php
                $budget = $budgets->get($cat->id);
                $hasBudget = !is_null($budget);
                $limit = $hasBudget ? (float) $budget->amount : 0;
                $spent = $hasBudget ? $budget->spent_amount : (float) \App\Models\Transaction::where('category_id', $cat->id)->where('type', 'expense')->whereMonth('transaction_date', $selectedMonth)->whereYear('transaction_date', $selectedYear)->sum('amount');
                $pct = $limit > 0 ? round(($spent / $limit) * 100, 1) : 0;
                $isOver = $limit > 0 && $spent > $limit;
                $remaining = $limit - $spent;
            @endphp
            <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-3.5 h-3.5 rounded-full shrink-0" style="background-color: {{ $cat->color }}"></span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm leading-tight">{{ $cat->name }}</h4>
                                @if($hasBudget && $budget->notes)
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $budget->notes }}</p>
                                @endif
                            </div>
                        </div>

                        @if($hasBudget)
                            <div class="flex items-center space-x-1">
                                <button onclick="editBudget('{{ $cat->id }}', '{{ $cat->name }}', '{{ $budget->amount }}', '{{ $budget->notes }}')" class="p-1 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Ubah Batas">
                                    ✏️
                                </button>
                                <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('Hapus batas anggaran kategori ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus Anggaran">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        @else
                            <button onclick="editBudget('{{ $cat->id }}', '{{ $cat->name }}', '0', '')" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 bg-blue-50 px-2.5 py-1 rounded-xl">
                                + Pasang
                            </button>
                        @endif
                    </div>

                    <!-- Progress Bar -->
                    @if($hasBudget)
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-bold {{ $isOver ? 'text-rose-600' : ($pct >= 80 ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $pct }}% Terpakai
                                </span>
                                <span class="text-slate-500 font-medium">
                                    Sisa: <strong class="{{ $remaining < 0 ? 'text-rose-600' : 'text-slate-700' }}">@rupiah($remaining)</strong>
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $isOver ? 'bg-rose-500' : ($pct >= 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="my-4 py-2 px-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <p class="text-xs text-slate-400">Belum ada batas anggaran ditetapkan</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Spent vs Limit -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Terpakai</span>
                        <span class="font-black text-slate-800">@rupiah($spent)</span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Batas Anggaran</span>
                        <span class="font-black text-slate-800">
                            {{ $hasBudget ? 'Rp ' . number_format($limit, 0, ',', '.') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Modal Set/Edit Budget -->
<div id="modalAddBudget" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-slate-900 text-base" id="modalBudgetTitle">Pasang Batas Anggaran</h4>
            <button onclick="document.getElementById('modalAddBudget').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('budgets.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $selectedMonth }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">

            <div>
                <label for="form_category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori Pengeluaran</label>
                <select name="category_id" id="form_category_id" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="form_amount" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batas Nominal (Rupiah)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" step="any" min="0" name="amount" id="form_amount" required placeholder="0" class="w-full pl-9 pr-3 py-2.5 text-sm font-bold rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="form_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan / Rencana (Opsional)</label>
                <input type="text" name="notes" id="form_notes" placeholder="Contoh: Belanja mingguan dan sayur mayur" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3">
                <button type="button" onclick="document.getElementById('modalAddBudget').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                    Simpan Anggaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editBudget(catId, catName, amount, notes) {
        document.getElementById('modalBudgetTitle').textContent = 'Atur Anggaran: ' + catName;
        document.getElementById('form_category_id').value = catId;
        document.getElementById('form_amount').value = amount;
        document.getElementById('form_notes').value = notes;
        document.getElementById('modalAddBudget').classList.remove('hidden');
    }
</script>
@endsection
