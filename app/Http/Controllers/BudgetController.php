<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = (int) $request->get('month', Carbon::now()->month);
        $selectedYear = (int) $request->get('year', Carbon::now()->year);

        $currentDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $monthName = $currentDate->locale('id')->isoFormat('MMMM YYYY');

        $categories = Category::where('type', 'expense')->orderBy('name')->get();

        $budgets = Budget::where('month', $selectedMonth)
            ->where('year', $selectedYear)
            ->get()
            ->keyBy('category_id');

        $totalBudget = $budgets->sum('amount');
        $totalSpent = $budgets->sum(function ($b) {
            return $b->spent_amount;
        });

        return view('budgets.index', compact(
            'categories',
            'budgets',
            'selectedMonth',
            'selectedYear',
            'monthName',
            'totalBudget',
            'totalSpent'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2040',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        Budget::updateOrCreate(
            [
                'category_id' => $validated['category_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            [
                'amount' => $validated['amount'],
                'notes' => $validated['notes'],
            ]
        );

        return redirect()->back()->with('success', 'Batas anggaran berhasil disimpan!');
    }

    public function copyPreviousMonth(Request $request)
    {
        $targetMonth = (int) $request->month;
        $targetYear = (int) $request->year;

        $prevDate = Carbon::createFromDate($targetYear, $targetMonth, 1)->subMonth();
        $prevMonth = $prevDate->month;
        $prevYear = $prevDate->year;

        $prevBudgets = Budget::where('month', $prevMonth)
            ->where('year', $prevYear)
            ->get();

        if ($prevBudgets->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data anggaran pada bulan sebelumnya untuk disalin.');
        }

        foreach ($prevBudgets as $pb) {
            Budget::updateOrCreate(
                [
                    'category_id' => $pb->category_id,
                    'month' => $targetMonth,
                    'year' => $targetYear,
                ],
                [
                    'amount' => $pb->amount,
                    'notes' => $pb->notes,
                ]
            );
        }

        return redirect()->back()->with('success', 'Anggaran bulan sebelumnya berhasil disalin ke bulan ini!');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return redirect()->back()->with('success', 'Batas anggaran berhasil dihapus.');
    }
}
