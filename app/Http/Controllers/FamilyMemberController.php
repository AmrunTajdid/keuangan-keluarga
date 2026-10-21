<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use Illuminate\Http\Request;

class FamilyMemberController extends Controller
{
    public function index()
    {
        $members = FamilyMember::withCount('transactions')->get();
        return view('family-members.index', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:50',
            'avatar_color' => 'required|string|max:20',
        ]);

        FamilyMember::create($validated);

        return redirect()->route('family-members.index')->with('success', 'Anggota keluarga berhasil didaftarkan!');
    }

    public function update(Request $request, FamilyMember $familyMember)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:50',
            'avatar_color' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        $familyMember->update($validated);

        return redirect()->route('family-members.index')->with('success', 'Data anggota keluarga berhasil diperbarui!');
    }

    public function destroy(FamilyMember $familyMember)
    {
        if ($familyMember->transactions()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus anggota keluarga yang memiliki riwayat transaksi.');
        }

        $familyMember->delete();
        return redirect()->route('family-members.index')->with('success', 'Anggota keluarga berhasil dihapus.');
    }
}
