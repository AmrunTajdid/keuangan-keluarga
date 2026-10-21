<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use Illuminate\Http\Request;

class SavingsGoalController extends Controller
{
    public function index()
    {
        $goals = SavingsGoal::orderBy('is_achieved', 'asc')
            ->orderBy('target_date', 'asc')
            ->get();

        $totalTarget = $goals->sum('target_amount');
        $totalCollected = $goals->sum('current_amount');

        return view('savings-goals.index', compact('goals', 'totalTarget', 'totalCollected'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'target_amount' => 'required|numeric|min:1',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'nullable|date',
            'color' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $validated['current_amount'] = $validated['current_amount'] ?? 0;
        $validated['is_achieved'] = $validated['current_amount'] >= $validated['target_amount'];

        SavingsGoal::create($validated);

        return redirect()->route('savings-goals.index')->with('success', 'Rencana tabungan impian berhasil dibuat!');
    }

    public function update(Request $request, SavingsGoal $savingsGoal)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'target_amount' => 'required|numeric|min:1',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'nullable|date',
            'color' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'is_achieved' => 'boolean',
        ]);

        $validated['is_achieved'] = $request->has('is_achieved') ? true : ($validated['current_amount'] >= $validated['target_amount']);

        $savingsGoal->update($validated);

        return redirect()->route('savings-goals.index')->with('success', 'Rencana tabungan berhasil diperbarui!');
    }

    public function addDeposit(Request $request, SavingsGoal $savingsGoal)
    {
        $request->validate([
            'deposit_amount' => 'required|numeric|min:1',
        ]);

        $newAmount = (float) $savingsGoal->current_amount + (float) $request->deposit_amount;
        $savingsGoal->update([
            'current_amount' => $newAmount,
            'is_achieved' => $newAmount >= (float) $savingsGoal->target_amount,
        ]);

        return redirect()->back()->with('success', 'Setoran tabungan sebesar Rp ' . number_format($request->deposit_amount, 0, ',', '.') . ' berhasil ditambahkan!');
    }

    public function destroy(SavingsGoal $savingsGoal)
    {
        $savingsGoal->delete();
        return redirect()->route('savings-goals.index')->with('success', 'Rencana tabungan berhasil dihapus.');
    }
}
