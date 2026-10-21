@extends('layouts.app')

@section('title', 'Anggota Keluarga')
@section('page_title', 'Anggota Keluarga')
@section('page_subtitle', 'Anggota Keluarga')

@section('content')
<div class="space-y-6">

    <!-- Header Action -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Anggota Keluarga</h3>
            <p class="text-xs text-slate-400">Total {{ $members->count() }} orang terdaftar</p>
        </div>
        <button onclick="openAddMemberModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors">
            + Tambah Anggota Baru
        </button>
    </div>

    <!-- Family Member Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($members as $m)
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <!-- Top Action & Avatar -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-sm" style="background-color: {{ $m->avatar_color }}">
                            {{ mb_substr($m->name, 0, 1) }}
                        </div>
                        <div class="flex items-center space-x-1">
                            <button onclick="editMember('{{ $m->id }}', '{{ $m->name }}', '{{ $m->role }}', '{{ $m->avatar_color }}')" class="p-1 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Ubah">
                                ✏️
                            </button>
                            <form method="POST" action="{{ route('family-members.destroy', $m) }}" onsubmit="return confirm('Hapus anggota keluarga ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Details -->
                    <h4 class="font-bold text-slate-900 text-base leading-tight">{{ $m->name }}</h4>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                        {{ $m->role }}
                    </span>
                </div>

                <!-- Footer Stats -->
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                    <span>Aktivitas Keuangan</span>
                    <span class="font-bold text-slate-700">{{ $m->transactions_count }} Transaksi</span>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Modal Add/Edit Member -->
<div id="modalMember" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-slate-900 text-base" id="modalMemberTitle">Tambah Anggota Keluarga</h4>
            <button onclick="document.getElementById('modalMember').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form id="memberForm" method="POST" action="{{ route('family-members.store') }}" class="space-y-4">
            @csrf
            <div id="memberMethodField"></div>

            <div>
                <label for="member_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap / Panggilan <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="member_name" required placeholder="Contoh: Ayah (Budi)" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="member_role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Peran di Keluarga</label>
                    <select name="role" id="member_role" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="Kepala Keluarga">Kepala Keluarga</option>
                        <option value="Istri / Bendahara">Istri / Bendahara</option>
                        <option value="Anak Pertama">Anak Pertama</option>
                        <option value="Anak Kedua">Anak Kedua</option>
                        <option value="Anak Ketiga">Anak Ketiga</option>
                        <option value="Kakek / Nenek">Kakek / Nenek</option>
                        <option value="Anggota Lainnya">Anggota Lainnya</option>
                    </select>
                </div>
                <div>
                    <label for="member_avatar_color" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Warna Profil</label>
                    <input type="color" name="avatar_color" id="member_avatar_color" value="#2563EB" class="w-full h-9 rounded-xl border border-slate-200 bg-slate-50 p-1 cursor-pointer">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3">
                <button type="button" onclick="document.getElementById('modalMember').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddMemberModal() {
        document.getElementById('modalMemberTitle').textContent = 'Tambah Anggota Keluarga';
        const form = document.getElementById('memberForm');
        form.action = "{{ route('family-members.store') }}";
        document.getElementById('memberMethodField').innerHTML = '';
        document.getElementById('member_name').value = '';
        document.getElementById('member_role').value = 'Kepala Keluarga';
        document.getElementById('member_avatar_color').value = '#2563EB';
        document.getElementById('modalMember').classList.remove('hidden');
    }

    function editMember(id, name, role, color) {
        document.getElementById('modalMemberTitle').textContent = 'Ubah Anggota Keluarga';
        const form = document.getElementById('memberForm');
        form.action = "/family-members/" + id;
        document.getElementById('memberMethodField').innerHTML = '@method("PUT")';
        document.getElementById('member_name').value = name;
        document.getElementById('member_role').value = role;
        document.getElementById('member_avatar_color').value = color;
        document.getElementById('modalMember').classList.remove('hidden');
    }
</script>
@endsection
