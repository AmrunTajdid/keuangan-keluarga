@extends('layouts.app')

@section('title', 'Kategori Keuangan')
@section('page_title', 'Kategori Keuangan Keluarga')
@section('page_subtitle', 'Kelompokkan pos-pos pemasukan dan pengeluaran agar analisis anggaran lebih rapi')

@section('content')
<div class="space-y-6">

    <!-- Header Action -->
    <div class="flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-900">Semua Pos Kategori</h3>
        <button onclick="openAddCategoryModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors">
            + Tambah Kategori Baru
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Income Categories -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 text-sm font-bold">📥</span>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Kategori Pemasukan</h4>
                        <p class="text-[11px] text-slate-400">Sumber arus kas masuk</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                    {{ $incomeCategories->count() }} Kategori
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($incomeCategories as $cat)
                    <div class="py-3 flex items-center justify-between hover:bg-slate-50/60 px-2 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-xs" style="background-color: {{ $cat->color }}"></span>
                            <div>
                                <p class="font-bold text-slate-800 text-xs">{{ $cat->name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $cat->transactions_count }} transaksi</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button onclick="editCategory('{{ $cat->id }}', '{{ $cat->name }}', '{{ $cat->type }}', '{{ $cat->color }}')" class="p-1 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Ubah">
                                ✏️
                            </button>
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini? Pastikan tidak ada transaksi terkait.');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Expense Categories -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <span class="p-2 rounded-xl bg-rose-50 text-rose-600 text-sm font-bold">📤</span>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Kategori Pengeluaran</h4>
                        <p class="text-[11px] text-slate-400">Pos alokasi belanja & biaya</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-100">
                    {{ $expenseCategories->count() }} Kategori
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($expenseCategories as $cat)
                    <div class="py-3 flex items-center justify-between hover:bg-slate-50/60 px-2 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-xs" style="background-color: {{ $cat->color }}"></span>
                            <div>
                                <p class="font-bold text-slate-800 text-xs">{{ $cat->name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $cat->transactions_count }} transaksi</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button onclick="editCategory('{{ $cat->id }}', '{{ $cat->name }}', '{{ $cat->type }}', '{{ $cat->color }}')" class="p-1 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Ubah">
                                ✏️
                            </button>
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini? Pastikan tidak ada transaksi terkait.');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

<!-- Modal Add/Edit Category -->
<div id="modalCategory" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-slate-900 text-base" id="modalCategoryTitle">Tambah Kategori</h4>
            <button onclick="document.getElementById('modalCategory').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form id="categoryForm" method="POST" action="{{ route('categories.store') }}" class="space-y-4">
            @csrf
            <div id="categoryMethodField"></div>

            <div>
                <label for="cat_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="cat_name" required placeholder="Contoh: Belanja Bahan Dapur" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="cat_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kategori</label>
                    <select name="type" id="cat_type" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="expense">Pengeluaran (-)</option>
                        <option value="income">Pemasukan (+)</option>
                    </select>
                </div>
                <div>
                    <label for="cat_color" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Warna Label</label>
                    <input type="color" name="color" id="cat_color" value="#10B981" class="w-full h-9 rounded-xl border border-slate-200 bg-slate-50 p-1 cursor-pointer">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3">
                <button type="button" onclick="document.getElementById('modalCategory').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddCategoryModal() {
        document.getElementById('modalCategoryTitle').textContent = 'Tambah Kategori';
        const form = document.getElementById('categoryForm');
        form.action = "{{ route('categories.store') }}";
        document.getElementById('categoryMethodField').innerHTML = '';
        document.getElementById('cat_name').value = '';
        document.getElementById('cat_type').value = 'expense';
        document.getElementById('cat_color').value = '#10B981';
        document.getElementById('modalCategory').classList.remove('hidden');
    }

    function editCategory(id, name, type, color) {
        document.getElementById('modalCategoryTitle').textContent = 'Ubah Kategori';
        const form = document.getElementById('categoryForm');
        form.action = "/categories/" + id;
        document.getElementById('categoryMethodField').innerHTML = '@method("PUT")';
        document.getElementById('cat_name').value = name;
        document.getElementById('cat_type').value = type;
        document.getElementById('cat_color').value = color;
        document.getElementById('modalCategory').classList.remove('hidden');
    }
</script>
@endsection
