@extends('layouts.app')

@section('title', 'Dompet & Rekening')
@section('page_title', 'Dompet & Rekening Keluarga')
@section('page_subtitle', 'Kelola semua tempat penyimpanan dana')

@section('content')
<div class="space-y-6">

    <!-- Header Action -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Rekening & Dompet</h3>
            <p class="text-xs text-slate-400">Total Saldo Terhitung: <strong class="text-slate-800">@rupiah($wallets->sum(fn($w) => $w->current_balance))</strong></p>
        </div>
        <button onclick="openAddWalletModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors">
            + Tambah Dompet / Rekening
        </button>
    </div>

    <!-- Wallets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($wallets as $wallet)
            @php
                $typeIcons = [
                    'bank' => '🏦',
                    'cash' => '💵',
                    'ewallet' => '📱',
                    'savings' => '🌱',
                ];
                $typeLabels = [
                    'bank' => 'Rekening Bank',
                    'cash' => 'Dompet Tunai',
                    'ewallet' => 'E-Wallet',
                    'savings' => 'Tabungan Khusus',
                ];
            @endphp
            <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: {{ $wallet->color }}"></div>

                <div>
                    <!-- Top Info -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-2.5">
                            <span class="p-2.5 rounded-2xl bg-slate-100 text-lg">
                                {{ $typeIcons[$wallet->type] ?? '💳' }}
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm leading-tight">{{ $wallet->name }}</h4>
                                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                                    {{ $typeLabels[$wallet->type] ?? 'Dompet' }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-1">
                            <button onclick="editWallet('{{ $wallet->id }}', '{{ $wallet->name }}', '{{ $wallet->type }}', '{{ $wallet->account_number }}', '{{ $wallet->initial_balance }}', '{{ $wallet->color }}')" class="p-1 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Ubah">
                                ✏️
                            </button>
                            <form method="POST" action="{{ route('wallets.destroy', $wallet) }}" onsubmit="return confirm('Hapus dompet ini? Transaksi terkait tidak boleh ada.');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($wallet->account_number)
                        <div class="bg-slate-50 px-2.5 py-1 rounded-xl text-[11px] font-mono text-slate-600 mb-3 inline-block">
                            No. {{ $wallet->account_number }}
                        </div>
                    @endif

                    <!-- Balance -->
                    <div class="mt-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Saldo Saat Ini</span>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">
                            @rupiah($wallet->current_balance)
                        </h3>
                    </div>
                </div>

                <!-- Footer info -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span>Saldo Awal: @rupiah($wallet->initial_balance)</span>
                    <span>{{ $wallet->transactions_count }} transaksi</span>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Modal Add/Edit Wallet -->
<div id="modalWallet" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-slate-900 text-base" id="modalWalletTitle">Tambah Dompet / Rekening</h4>
            <button onclick="document.getElementById('modalWallet').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form id="walletForm" method="POST" action="{{ route('wallets.store') }}" class="space-y-4">
            @csrf
            <div id="walletMethodField"></div>

            <div>
                <label for="wallet_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Rekening / Dompet <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="wallet_name" required placeholder="Contoh: BCA Tabungan Utama" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="wallet_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe</label>
                    <select name="type" id="wallet_type" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="bank">Rekening Bank</option>
                        <option value="cash">Dompet Tunai</option>
                        <option value="ewallet">E-Wallet</option>
                        <option value="savings">Tabungan Khusus</option>
                    </select>
                </div>
                <div>
                    <label for="wallet_color" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Warna Aksen</label>
                    <input type="color" name="color" id="wallet_color" value="#3B82F6" class="w-full h-9 rounded-xl border border-slate-200 bg-slate-50 p-1 cursor-pointer">
                </div>
            </div>

            <div>
                <label for="wallet_account_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Rekening / HP (Opsional)</label>
                <input type="text" name="account_number" id="wallet_account_number" placeholder="Contoh: 1234567890" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label for="wallet_initial_balance" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Saldo Awal (Rupiah) <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" step="any" min="0" name="initial_balance" id="wallet_initial_balance" required placeholder="0" class="w-full pl-9 pr-3 py-2.5 text-sm font-bold rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3">
                <button type="button" onclick="document.getElementById('modalWallet').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddWalletModal() {
        document.getElementById('modalWalletTitle').textContent = 'Tambah Dompet / Rekening';
        const form = document.getElementById('walletForm');
        form.action = "{{ route('wallets.store') }}";
        document.getElementById('walletMethodField').innerHTML = '';
        document.getElementById('wallet_name').value = '';
        document.getElementById('wallet_account_number').value = '';
        document.getElementById('wallet_initial_balance').value = '0';
        document.getElementById('wallet_color').value = '#3B82F6';
        document.getElementById('modalWallet').classList.remove('hidden');
    }

    function editWallet(id, name, type, accNumber, initialBalance, color) {
        document.getElementById('modalWalletTitle').textContent = 'Ubah Dompet / Rekening';
        const form = document.getElementById('walletForm');
        form.action = "/wallets/" + id;
        document.getElementById('walletMethodField').innerHTML = '@method("PUT")';
        document.getElementById('wallet_name').value = name;
        document.getElementById('wallet_type').value = type;
        document.getElementById('wallet_account_number').value = accNumber || '';
        document.getElementById('wallet_initial_balance').value = initialBalance;
        document.getElementById('wallet_color').value = color;
        document.getElementById('modalWallet').classList.remove('hidden');
    }
</script>
@endsection
