@extends('layouts.app')

@section('title', 'Daftar Transaksi')
@section('page_title', 'Catatan Transaksi Keuangan')
@section('page_subtitle', 'Pantau dan kelola')

@section('content')
<div class="space-y-6">
    <!-- Action & Filter Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <h3 class="text-base font-bold text-slate-900">Filter Transaksi</h3>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center justify-center space-x-2 py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                <span>+ Catat Transaksi Baru</span>
            </a>
        </div>

        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari Keterangan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Belanja beras..." class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Type -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Jenis</label>
                <select name="type" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Jenis</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan (+)</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran (-)</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer Antar Kas</option>
                </select>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Kategori</label>
                <select name="category_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->type == 'income' ? 'Masuk' : 'Keluar' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Family Member -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Anggota Keluarga</label>
                <select name="family_member_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Anggota</option>
                    @foreach($familyMembers as $member)
                        <option value="{{ $member->id }}" {{ request('family_member_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Wallet -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Dompet / Rekening</label>
                <select name="wallet_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Dompet</option>
                    @foreach($wallets as $wallet)
                        <option value="{{ $wallet->id }}" {{ request('wallet_id') == $wallet->id ? 'selected' : '' }}>
                            {{ $wallet->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition-colors">
                    Filter
                </button>
                <a href="{{ route('transactions.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Filter Summary Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase text-emerald-700 tracking-wider">Total Pemasukan</p>
                <p class="text-lg font-black text-emerald-900 mt-0.5">@rupiah($totalIncome)</p>
            </div>
            <span class="text-2xl">📥</span>
        </div>
        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase text-rose-700 tracking-wider">Total Pengeluaran</p>
                <p class="text-lg font-black text-rose-900 mt-0.5">@rupiah($totalExpense)</p>
            </div>
            <span class="text-2xl">📤</span>
        </div>
        <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase text-blue-700 tracking-wider">Arus Bersih</p>
                <p class="text-lg font-black text-blue-900 mt-0.5">@rupiah($totalIncome - $totalExpense)</p>
            </div>
            <span class="text-2xl">⚖️</span>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        @if($transactions->isEmpty())
            <div class="p-12 text-center text-slate-400">
                <span class="text-4xl block mb-2">🧾</span>
                <p class="text-sm font-medium text-slate-600">Tidak ada transaksi yang cocok dengan filter.</p>
                <p class="text-xs text-slate-400 mt-1">Coba ubah filter atau catat transaksi baru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Jenis & Kategori</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4">Anggota</th>
                            <th class="py-3 px-4">Dompet / Kas</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($transactions as $tx)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-slate-500">
                                    {{ $tx->transaction_date->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($tx->type === 'income')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            + Pemasukan
                                        </span>
                                    @elseif($tx->type === 'expense')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            - Pengeluaran
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">
                                            ⇄ Transfer
                                        </span>
                                    @endif

                                    @if($tx->category)
                                        <div class="mt-1 flex items-center space-x-1.5 text-slate-600 font-medium">
                                            <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $tx->category->color }}"></span>
                                            <span>{{ $tx->category->name }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $tx->description }}</div>
                                    @if($tx->notes)
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $tx->notes }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($tx->familyMember)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background-color: {{ $tx->familyMember->avatar_color }}15; color: {{ $tx->familyMember->avatar_color }}">
                                            {{ $tx->familyMember->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-slate-600">
                                    @if($tx->type === 'transfer')
                                        <span>{{ $tx->wallet->name }} &rarr; <span class="font-bold text-slate-800">{{ $tx->toWallet->name ?? 'Tujuan' }}</span></span>
                                    @else
                                        <span>{{ $tx->wallet->name }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap font-black text-sm {{ $tx->type === 'income' ? 'text-emerald-600' : ($tx->type === 'expense' ? 'text-rose-600' : 'text-indigo-600') }}">
                                    {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '-' : '') }}@rupiah($tx->amount)
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('transactions.edit', $tx) }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Ubah">
                                            ✏️
                                        </a>
                                        <form method="POST" action="{{ route('transactions.destroy', $tx) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
