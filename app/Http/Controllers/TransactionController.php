<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FamilyMember;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['familyMember', 'category', 'wallet', 'toWallet']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('wallet_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('wallet_id', $request->wallet_id)
                  ->orWhere('to_wallet_id', $request->wallet_id);
            });
        }

        if ($request->filled('family_member_id')) {
            $query->where('family_member_id', $request->family_member_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $familyMembers = FamilyMember::where('is_active', true)->orderBy('name')->get();
        $wallets = Wallet::orderBy('name')->get();

        // Calculate summary for filtered results
        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'familyMembers',
            'wallets',
            'totalIncome',
            'totalExpense'
        ));
    }

    public function create()
    {
        $categoriesIncome = Category::where('type', 'income')->orderBy('name')->get();
        $categoriesExpense = Category::where('type', 'expense')->orderBy('name')->get();
        $familyMembers = FamilyMember::where('is_active', true)->orderBy('name')->get();
        $wallets = Wallet::orderBy('name')->get();

        return view('transactions.create', compact('categoriesIncome', 'categoriesExpense', 'familyMembers', 'wallets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id' => 'required_if:type,transfer|nullable|exists:wallets,id|different:wallet_id',
            'category_id' => 'nullable|exists:categories,id',
            'family_member_id' => 'nullable|exists:family_members,id',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'to_wallet_id.different' => 'Dompet tujuan transfer harus berbeda dari dompet asal.',
            'to_wallet_id.required_if' => 'Dompet tujuan wajib dipilih untuk tipe transfer.',
        ]);

        Transaction::create($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function edit(Transaction $transaction)
    {
        $categoriesIncome = Category::where('type', 'income')->orderBy('name')->get();
        $categoriesExpense = Category::where('type', 'expense')->orderBy('name')->get();
        $familyMembers = FamilyMember::where('is_active', true)->orderBy('name')->get();
        $wallets = Wallet::orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'categoriesIncome', 'categoriesExpense', 'familyMembers', 'wallets'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id' => 'required_if:type,transfer|nullable|exists:wallets,id|different:wallet_id',
            'category_id' => 'nullable|exists:categories,id',
            'family_member_id' => 'nullable|exists:family_members,id',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
