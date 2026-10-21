<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FamilyMember;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = (int) $request->get('month', Carbon::now()->month);
        $selectedYear = (int) $request->get('year', Carbon::now()->year);

        $date = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $monthName = $date->locale('id')->isoFormat('MMMM YYYY');

        $incomeTransactions = Transaction::with(['category', 'familyMember', 'wallet'])
            ->where('type', 'income')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->orderBy('transaction_date', 'asc')
            ->get();

        $expenseTransactions = Transaction::with(['category', 'familyMember', 'wallet'])
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->orderBy('transaction_date', 'asc')
            ->get();

        $totalIncome = $incomeTransactions->sum('amount');
        $totalExpense = $expenseTransactions->sum('amount');
        $netCashflow = $totalIncome - $totalExpense;

        // Group by category
        $expensesByCategory = $expenseTransactions->groupBy('category_id')->map(function ($items) {
            return [
                'name' => $items->first()->category ? $items->first()->category->name : 'Tanpa Kategori',
                'color' => $items->first()->category ? $items->first()->category->color : '#94A3B8',
                'count' => $items->count(),
                'total' => $items->sum('amount'),
            ];
        })->sortByDesc('total');

        // Group by family member
        $expensesByMember = $expenseTransactions->groupBy('family_member_id')->map(function ($items) {
            return [
                'name' => $items->first()->familyMember ? $items->first()->familyMember->name : 'Bersama / Umum',
                'color' => $items->first()->familyMember ? $items->first()->familyMember->avatar_color : '#64748B',
                'count' => $items->count(),
                'total' => $items->sum('amount'),
            ];
        })->sortByDesc('total');

        return view('reports.index', compact(
            'selectedMonth',
            'selectedYear',
            'monthName',
            'incomeTransactions',
            'expenseTransactions',
            'totalIncome',
            'totalExpense',
            'netCashflow',
            'expensesByCategory',
            'expensesByMember'
        ));
    }
}
