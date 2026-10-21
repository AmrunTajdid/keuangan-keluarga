<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\FamilyMember;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = (int) $request->get('month', Carbon::now()->month);
        $selectedYear = (int) $request->get('year', Carbon::now()->year);

        $currentDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $monthName = $currentDate->locale('id')->isoFormat('MMMM YYYY');

        // Wallets
        $wallets = Wallet::all();
        $totalBalance = $wallets->sum(function ($wallet) {
            return $wallet->current_balance;
        });

        // Income & Expense for selected month
        $monthlyIncome = (float) Transaction::where('type', 'income')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('amount');

        $monthlyExpense = (float) Transaction::where('type', 'expense')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('amount');

        $monthlyNet = $monthlyIncome - $monthlyExpense;
        $savingsRate = $monthlyIncome > 0 ? round(($monthlyNet / $monthlyIncome) * 100, 1) : 0;

        // Budgets for selected month
        $budgets = Budget::with('category')
            ->where('month', $selectedMonth)
            ->where('year', $selectedYear)
            ->get();

        $totalBudgetLimit = $budgets->sum('amount');
        $totalBudgetSpent = $budgets->sum(function ($b) {
            return $b->spent_amount;
        });

        // Recent 7 transactions
        $recentTransactions = Transaction::with(['familyMember', 'category', 'wallet', 'toWallet'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(7)
            ->get();

        // Savings Goals
        $savingsGoals = SavingsGoal::orderBy('is_achieved', 'asc')
            ->orderBy('target_date', 'asc')
            ->take(4)
            ->get();

        // Chart 1: Cashflow 6 months history
        $cashflowLabels = [];
        $cashflowIncomeData = [];
        $cashflowExpenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $m = $dt->month;
            $y = $dt->year;
            $cashflowLabels[] = $dt->locale('id')->isoFormat('MMM YY');

            $cashflowIncomeData[] = (float) Transaction::where('type', 'income')
                ->whereMonth('transaction_date', $m)
                ->whereYear('transaction_date', $y)
                ->sum('amount');

            $cashflowExpenseData[] = (float) Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', $m)
                ->whereYear('transaction_date', $y)
                ->sum('amount');
        }

        // Chart 2: Expense by Category for selected month
        $categoriesExpense = Category::where('type', 'expense')
            ->with(['transactions' => function ($q) use ($selectedMonth, $selectedYear) {
                $q->whereMonth('transaction_date', $selectedMonth)
                  ->whereYear('transaction_date', $selectedYear);
            }])
            ->get()
            ->map(function ($cat) {
                return [
                    'name' => $cat->name,
                    'color' => $cat->color,
                    'total' => (float) $cat->transactions->sum('amount'),
                ];
            })
            ->filter(fn($c) => $c['total'] > 0)
            ->values();

        // Chart 3: Expense by Family Member for selected month
        $familyExpense = FamilyMember::with(['transactions' => function ($q) use ($selectedMonth, $selectedYear) {
                $q->where('type', 'expense')
                  ->whereMonth('transaction_date', $selectedMonth)
                  ->whereYear('transaction_date', $selectedYear);
            }])
            ->get()
            ->map(function ($member) {
                return [
                    'name' => $member->name,
                    'color' => $member->avatar_color,
                    'total' => (float) $member->transactions->sum('amount'),
                ];
            })
            ->filter(fn($m) => $m['total'] > 0)
            ->values();

        return view('dashboard', compact(
            'wallets',
            'totalBalance',
            'monthlyIncome',
            'monthlyExpense',
            'monthlyNet',
            'savingsRate',
            'budgets',
            'totalBudgetLimit',
            'totalBudgetSpent',
            'recentTransactions',
            'savingsGoals',
            'selectedMonth',
            'selectedYear',
            'monthName',
            'cashflowLabels',
            'cashflowIncomeData',
            'cashflowExpenseData',
            'categoriesExpense',
            'familyExpense'
        ));
    }
}
