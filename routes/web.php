<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockTransferController;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // POS
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [POSController::class, 'search'])->name('pos.search');
    Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{id}', [POSController::class, 'receipt'])->name('pos.receipt');
    Route::post('/pos/hold', [POSController::class, 'holdSale'])->name('pos.hold');
    Route::get('/pos/held', [POSController::class, 'heldSales'])->name('pos.held');
    Route::post('/pos/void/{id}', [POSController::class, 'voidSale'])->name('pos.void');

    // Products
    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // Branches
    Route::resource('branches', BranchController::class)->except(['create', 'show', 'edit']);
    Route::post('/branches/{branch}/switch', [BranchController::class, 'switch'])->name('branches.switch');

    // Customers
    Route::resource('customers', CustomerController::class)->except(['create', 'show', 'edit']);
    Route::get('/customers/{customer}/debts', [CustomerController::class, 'debts'])->name('customers.debts');
    Route::post('/debts/{debt}/pay', [CustomerController::class, 'payDebt'])->name('debts.pay');

    // Suppliers & Purchases
    Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);
    Route::get('/purchases', [SupplierController::class, 'purchases'])->name('suppliers.purchases');
    Route::post('/purchases', [SupplierController::class, 'storePurchase'])->name('suppliers.purchases.store');

    // Expenses
    Route::resource('expenses', ExpenseController::class)->except(['create', 'show', 'edit']);

    // Shifts
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts/open', [ShiftController::class, 'open'])->name('shifts.open');
    Route::post('/shifts/{shift}/close', [ShiftController::class, 'close'])->name('shifts.close');

    // Stock Transfers
    Route::get('/stock-transfers', [StockTransferController::class, 'index'])->name('stock-transfers.index');
    Route::post('/stock-transfers', [StockTransferController::class, 'store'])->name('stock-transfers.store');
    Route::post('/stock-transfers/{transfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');
    Route::post('/stock-transfers/{transfer}/ship', [StockTransferController::class, 'ship'])->name('stock-transfers.ship');
    Route::post('/stock-transfers/{transfer}/receive', [StockTransferController::class, 'receive'])->name('stock-transfers.receive');
    Route::post('/stock-transfers/{transfer}/reject', [StockTransferController::class, 'reject'])->name('stock-transfers.reject');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
});
