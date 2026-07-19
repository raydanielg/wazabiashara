<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CashFlowController;
use App\Http\Controllers\Api\BusinessProfileController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by bootstrap/app.php's withRouting(api: ...)
| and automatically prefixed with "api" (apiPrefix: 'api'), so paths
| below should NOT repeat the "api/" segment. Auth is handled by a
| custom Bearer-token middleware ("auth.token") since Sanctum/Passport
| are not installed in this app.
|
*/

// Public
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.forgot-password');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.reset-password');

// Authenticated (Bearer token)
Route::middleware('auth.token')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('api.products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('api.products.show');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('api.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('api.products.destroy');
    Route::post('/products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('api.products.adjust-stock');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('api.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('api.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('api.categories.destroy');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('api.customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('api.customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('api.customers.show');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('api.customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('api.customers.destroy');
    Route::get('/customers/{customer}/debts', [CustomerController::class, 'debts'])->name('api.customers.debts');
    Route::post('/debts/{debt}/pay', [CustomerController::class, 'payDebt'])->name('api.debts.pay');

    // Suppliers & Purchases
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('api.suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('api.suppliers.store');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('api.suppliers.show');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('api.suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('api.suppliers.destroy');
    Route::get('/purchases', [SupplierController::class, 'purchases'])->name('api.purchases.index');
    Route::post('/purchases', [SupplierController::class, 'storePurchase'])->name('api.purchases.store');

    // Sales / POS
    Route::get('/sales', [SaleController::class, 'index'])->name('api.sales.index');
    Route::get('/sales/{id}', [SaleController::class, 'show'])->name('api.sales.show');
    Route::post('/sales', [SaleController::class, 'store'])->name('api.sales.store');
    Route::post('/sales/checkout', [SaleController::class, 'store'])->name('api.sales.checkout');
    Route::post('/sales/{id}/void', [SaleController::class, 'void'])->name('api.sales.void');

    // Expenses
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('api.expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('api.expenses.store');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('api.expenses.show');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('api.expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('api.expenses.destroy');

    // Incomes
    Route::get('/incomes', [IncomeController::class, 'index'])->name('api.incomes.index');
    Route::post('/incomes', [IncomeController::class, 'store'])->name('api.incomes.store');
    Route::get('/incomes/{income}', [IncomeController::class, 'show'])->name('api.incomes.show');
    Route::put('/incomes/{income}', [IncomeController::class, 'update'])->name('api.incomes.update');
    Route::delete('/incomes/{income}', [IncomeController::class, 'destroy'])->name('api.incomes.destroy');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('api.payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('api.payments.store');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('api.payments.destroy');

    // Cash Flow & Accounts
    Route::get('/cash-flow', [CashFlowController::class, 'index'])->name('api.cash-flow.index');
    Route::post('/cash-flow/accounts', [CashFlowController::class, 'storeAccount'])->name('api.cash-flow.accounts.store');
    Route::delete('/cash-flow/accounts/{account}', [CashFlowController::class, 'destroyAccount'])->name('api.cash-flow.accounts.destroy');

    // Business Profile
    Route::get('/business/profile', [BusinessProfileController::class, 'show'])->name('api.business.profile');
    Route::put('/business/profile', [BusinessProfileController::class, 'update'])->name('api.business.profile.update');

    // Reminders
    Route::get('/reminders', [ReminderController::class, 'index'])->name('api.reminders.index');
    Route::post('/reminders', [ReminderController::class, 'store'])->name('api.reminders.store');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('api.reminders.destroy');

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('api.reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('api.reports.inventory');
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('api.reports.profit-loss');
    Route::get('/reports/chart-data', [ReportController::class, 'chartData'])->name('api.reports.chart-data');
});
