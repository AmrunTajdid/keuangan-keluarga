<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\PinAuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// PIN Authentication Routes (Public)
Route::get('/pin-login', [PinAuthController::class, 'showLogin'])->name('pin.login');
Route::post('/pin-login', [PinAuthController::class, 'verify'])->name('pin.verify');
Route::post('/pin-logout', [PinAuthController::class, 'logout'])->name('pin.logout');

// Protected Routes (Must authenticate with PIN: 030923)
Route::middleware('pin.auth')->group(function () {
    // Dashboard as homepage
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Budgets
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::post('/budgets/copy-previous', [BudgetController::class, 'copyPreviousMonth'])->name('budgets.copy');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Wallets & Accounts
    Route::resource('wallets', WalletController::class)->except(['create', 'show', 'edit']);

    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // Family Members
    Route::resource('family-members', FamilyMemberController::class)->except(['create', 'show', 'edit']);

    // Savings Goals
    Route::resource('savings-goals', SavingsGoalController::class)->except(['create', 'show', 'edit']);
    Route::post('/savings-goals/{savingsGoal}/deposit', [SavingsGoalController::class, 'addDeposit'])->name('savings-goals.deposit');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
