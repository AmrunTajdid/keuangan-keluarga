@extends('layouts.app')

@section('title', 'Rencana Tabungan Impian')
@section('page_title', 'Rencana & Target Tabungan Keluarga')
@section('page_subtitle', 'Tetapkan tujuan tabungan bersama (Dana Darurat, Qurban, Pendidikan, Liburan) dan capai targetnya step-by-step')

@section('content')
<div class="space-y-6">

    <!-- Header Action -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Target Finansial Masa Depan</h3>
            <p class="text-xs text-slate-400">
                Terkumpul: <strong class="text-emerald-600">@rupiah($totalCollected)</strong> dari target <strong class="text-slate-800">@rupiah($totalTarget)</strong>
            </p>
        </div>
        <button onclick="openAddGoalModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors">
            + Buat Target Tabungan Baru
        </button>
    </div>

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($goals as $goal)
            @php
                $pct = $goal->percentage;
                $remaining = max(0, (float) $goal->target_amount - (float) $goal->current_amount);
                $isDone = $goal->is_achieved || $pct >= 100;
            @endphp
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: {{ $goal->color }}"></div>

                <div>
                    <!-- Top header -->
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center space-x-2">
                                <h4 class="font-bold text-slate-900 text-base leading-tight">{{ $goal->name }}</h4>
                                @if($isDone)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                        ✓ Tercapai
                                    </span>
                                @endif
                            </div>
                            @if($goal->target_date)
                                <p class="text-[11px] text-slate-400 mt-1 flex items-center space-x-1">
                                    <span>🎯 Target: {{ $goal->target_date->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                                </p>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-1">
                            <button onclick="editGoal('{{ $goal->id }}', '{{ $goal->name }}', '{{ $goal->target_amount }}', '{{ $goal->current_amount }}', '{{ $goal->target_date ? $goal->target_date->format('Y-m-d') : '' }}', '{{ $goal->color }}', '{{ $goal->notes }}')" class="p-1 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Ubah">
                                ✏️
                            </button>
                            <form method="POST" action="{{ route('savings-goals.destroy', $goal) }}" onsubmit="return confirm('Hapus rencana tabungan ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($goal->notes)
                        <p class="text-xs text-slate-500 mb-4 bg-slate-50 p-2.5 rounded-xl border border-slate-100">{{ $goal->notes }}</p>
                    @endif

                    <!-- Progress Bar -->
                    <div class="my-4">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-blue-600">{{ $pct }}% Terkumpul</span>
                            <span class="text-slate-400">Sisa: <strong class="text-slate-700">@rupiah($remaining)</strong></span>
                        </div>
                        <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700" style="background-color: {{ $goal->color }}; width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs py-2 border-t border-slate-100">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Terkumpul</span>
                            <span class="font-black text-slate-900 text-sm">@rupiah($goal->current_amount)</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Target Akhir</span>
                            <span class="font-black text-slate-700 text-sm">@rupiah($goal->target_amount)</span>
                        </div>
                    </div>
                </div>

                <!-- Setor Tabungan Action Button -->
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <button onclick="openDepositModal('{{ $goal->id }}', '{{ $goal->name }}')" class="w-full py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs rounded-xl transition-colors flex items-center justify-center space-x-1.5">
                        <span>💰</span>
                        <span>+ Tambah Setoran Tabungan</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Modal Quick Deposit -->
<div id="modalDeposit" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="font-bold text-slate-900 text-base">Setor Tabungan</h4>
                <p id="depositGoalName" class="text-xs text-slate-400 mt-0.5"></p>
            </div>
            <button onclick="document.getElementById('modalDeposit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form id="depositForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="deposit_amount" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal Tambahan (Rupiah)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" step="any" min="1" name="deposit_amount" id="deposit_amount" required placeholder="0" class="w-full pl-9 pr-3 py-2.5 text-base font-black rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('modalDeposit').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                    Simpan Setoran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add/Edit Goal -->
<div id="modalGoal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-slate-900 text-base" id="modalGoalTitle">Buat Target Tabungan</h4>
            <button onclick="document.getElementById('modalGoal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form id="goalForm" method="POST" action="{{ route('savings-goals.store') }}" class="space-y-4">
            @csrf
            <div id="goalMethodField"></div>

            <div>
                <label for="goal_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Target / Tujuan <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="goal_name" required placeholder="Contoh: Dana Darurat 6 Bulan" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="goal_target_amount" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Nominal (Rp)</label>
                    <input type="number" step="any" min="1" name="target_amount" id="goal_target_amount" required placeholder="0" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label for="goal_current_amount" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sudah Terkumpul (Rp)</label>
                    <input type="number" step="any" min="0" name="current_amount" id="goal_current_amount" value="0" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="goal_target_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Tercapai</label>
                    <input type="date" name="target_date" id="goal_target_date" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label for="goal_color" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Warna Label</label>
                    <input type="color" name="color" id="goal_color" value="#10B981" class="w-full h-9 rounded-xl border border-slate-200 bg-slate-50 p-1 cursor-pointer">
                </div>
            </div>

            <div>
                <label for="goal_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan / Strategi</label>
                <textarea name="notes" id="goal_notes" rows="2" placeholder="Contoh: Disimpan di deposito / reksadana pasar uang" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3">
                <button type="button" onclick="document.getElementById('modalGoal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                    Simpan Rencana
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddGoalModal() {
        document.getElementById('modalGoalTitle').textContent = 'Buat Target Tabungan';
        const form = document.getElementById('goalForm');
        form.action = "{{ route('savings-goals.store') }}";
        document.getElementById('goalMethodField').innerHTML = '';
        document.getElementById('goal_name').value = '';
        document.getElementById('goal_target_amount').value = '';
        document.getElementById('goal_current_amount').value = '0';
        document.getElementById('goal_target_date').value = '';
        document.getElementById('goal_color').value = '#10B981';
        document.getElementById('goal_notes').value = '';
        document.getElementById('modalGoal').classList.remove('hidden');
    }

    function editGoal(id, name, target, current, targetDate, color, notes) {
        document.getElementById('modalGoalTitle').textContent = 'Ubah Target Tabungan';
        const form = document.getElementById('goalForm');
        form.action = "/savings-goals/" + id;
        document.getElementById('goalMethodField').innerHTML = '@method("PUT")';
        document.getElementById('goal_name').value = name;
        document.getElementById('goal_target_amount').value = target;
        document.getElementById('goal_current_amount').value = current;
        document.getElementById('goal_target_date').value = targetDate;
        document.getElementById('goal_color').value = color;
        document.getElementById('goal_notes').value = notes || '';
        document.getElementById('modalGoal').classList.remove('hidden');
    }

    function openDepositModal(id, name) {
        document.getElementById('depositGoalName').textContent = name;
        document.getElementById('depositForm').action = "/savings-goals/" + id + "/deposit";
        document.getElementById('deposit_amount').value = '';
        document.getElementById('modalDeposit').classList.remove('hidden');
    }
</script>
@endsection
