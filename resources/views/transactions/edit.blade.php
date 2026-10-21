@extends('layouts.app')

@section('title', 'Ubah Transaksi')
@section('page_title', 'Ubah Data Transaksi')
@section('page_subtitle', 'Perbarui detail transaksi yang telah dicatat')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs">
        
        <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Transaction Type Picker -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Transaksi</label>
                <div class="grid grid-cols-3 gap-2 p-1.5 bg-slate-100 rounded-2xl">
                    <label class="relative flex items-center justify-center p-3 rounded-xl cursor-pointer text-xs font-bold transition-all text-center has-[:checked]:bg-white has-[:checked]:text-rose-600 has-[:checked]:shadow-xs text-slate-600">
                        <input type="radio" name="type" value="expense" class="sr-only" {{ $transaction->type === 'expense' ? 'checked' : '' }} onchange="handleTypeChange('expense')">
                        <span>📤 Pengeluaran</span>
                    </label>

                    <label class="relative flex items-center justify-center p-3 rounded-xl cursor-pointer text-xs font-bold transition-all text-center has-[:checked]:bg-white has-[:checked]:text-emerald-600 has-[:checked]:shadow-xs text-slate-600">
                        <input type="radio" name="type" value="income" class="sr-only" {{ $transaction->type === 'income' ? 'checked' : '' }} onchange="handleTypeChange('income')">
                        <span>📥 Pemasukan</span>
                    </label>

                    <label class="relative flex items-center justify-center p-3 rounded-xl cursor-pointer text-xs font-bold transition-all text-center has-[:checked]:bg-white has-[:checked]:text-indigo-600 has-[:checked]:shadow-xs text-slate-600">
                        <input type="radio" name="type" value="transfer" class="sr-only" {{ $transaction->type === 'transfer' ? 'checked' : '' }} onchange="handleTypeChange('transfer')">
                        <span>⇄ Transfer Kas</span>
                    </label>
                </div>
            </div>

            <!-- Amount -->
            <div>
                <label for="amount" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominal (Rupiah) <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold text-base">Rp</span>
                    <input type="number" step="any" min="1" name="amount" id="amount" value="{{ old('amount', (float) $transaction->amount) }}" required class="w-full pl-12 pr-4 py-3 text-lg font-black rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Transaction Date -->
                <div>
                    <label for="transaction_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- Family Member -->
                <div>
                    <label for="family_member_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Oleh Anggota Keluarga</label>
                    <select name="family_member_id" id="family_member_id" class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Tanpa Anggota Spesifik --</option>
                        @foreach($familyMembers as $m)
                            <option value="{{ $m->id }}" {{ old('family_member_id', $transaction->family_member_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->name }} ({{ $m->role }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Category Section -->
            <div id="categoryContainer" class="{{ $transaction->type === 'transfer' ? 'hidden' : '' }}">
                <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori</label>
                
                <select name="category_id" id="categoryExpenseSelect" class="{{ $transaction->type === 'income' ? 'hidden' : '' }} w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Pilih Kategori Pengeluaran --</option>
                    @foreach($categoriesExpense as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select id="categoryIncomeSelect" class="{{ $transaction->type === 'income' ? '' : 'hidden' }} w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Pilih Kategori Pemasukan --</option>
                    @foreach($categoriesIncome as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Wallets Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Source Wallet -->
                <div>
                    <label for="wallet_id" id="sourceWalletLabel" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sumber Dompet / Kas <span class="text-rose-500">*</span></label>
                    <select name="wallet_id" id="wallet_id" required class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}" {{ old('wallet_id', $transaction->wallet_id) == $w->id ? 'selected' : '' }}>
                                {{ $w->name }} (Saldo: @rupiah($w->current_balance))
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Destination Wallet (Transfer only) -->
                <div id="toWalletContainer" class="{{ $transaction->type === 'transfer' ? '' : 'hidden' }}">
                    <label for="to_wallet_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tujuan Transfer <span class="text-rose-500">*</span></label>
                    <select name="to_wallet_id" id="to_wallet_id" class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Pilih Rekening Tujuan --</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}" {{ old('to_wallet_id', $transaction->to_wallet_id) == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keterangan Transaksi <span class="text-rose-500">*</span></label>
                <input type="text" name="description" id="description" value="{{ old('description', $transaction->description) }}" required class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('notes', $transaction->notes) }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition-colors">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>
</div>

<script>
    function handleTypeChange(type) {
        const catContainer = document.getElementById('categoryContainer');
        const toWalletContainer = document.getElementById('toWalletContainer');
        const sourceLabel = document.getElementById('sourceWalletLabel');
        const expSelect = document.getElementById('categoryExpenseSelect');
        const incSelect = document.getElementById('categoryIncomeSelect');

        if (type === 'transfer') {
            catContainer.classList.add('hidden');
            toWalletContainer.classList.remove('hidden');
            sourceLabel.textContent = 'Dari Dompet (Asal) *';
            expSelect.removeAttribute('name');
            incSelect.removeAttribute('name');
        } else if (type === 'income') {
            catContainer.classList.remove('hidden');
            toWalletContainer.classList.add('hidden');
            sourceLabel.textContent = 'Masuk ke Dompet / Kas *';
            
            expSelect.classList.add('hidden');
            expSelect.removeAttribute('name');

            incSelect.classList.remove('hidden');
            incSelect.setAttribute('name', 'category_id');
        } else {
            catContainer.classList.remove('hidden');
            toWalletContainer.classList.add('hidden');
            sourceLabel.textContent = 'Keluar dari Dompet / Kas *';

            incSelect.classList.add('hidden');
            incSelect.removeAttribute('name');

            expSelect.classList.remove('hidden');
            expSelect.setAttribute('name', 'category_id');
        }
    }
</script>
@endsection
