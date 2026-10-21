<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::withCount('transactions')->get();
        return view('wallets.index', compact('wallets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank,ewallet,savings',
            'account_number' => 'nullable|string|max:50',
            'initial_balance' => 'required|numeric|min:0',
            'color' => 'required|string|max:20',
        ]);

        Wallet::create($validated);

        return redirect()->route('wallets.index')->with('success', 'Dompet / Rekening berhasil ditambahkan!');
    }

    public function update(Request $request, Wallet $wallet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank,ewallet,savings',
            'account_number' => 'nullable|string|max:50',
            'initial_balance' => 'required|numeric|min:0',
            'color' => 'required|string|max:20',
        ]);

        $wallet->update($validated);

        return redirect()->route('wallets.index')->with('success', 'Dompet / Rekening berhasil diperbarui!');
    }

    public function destroy(Wallet $wallet)
    {
        if ($wallet->transactions()->exists() || $wallet->incomingTransfers()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus rekening/dompet yang sudah memiliki riwayat transaksi.');
        }

        $wallet->delete();
        return redirect()->route('wallets.index')->with('success', 'Dompet / Rekening berhasil dihapus.');
    }
}
