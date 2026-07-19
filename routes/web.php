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
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\BillGalleryController;
use App\Http\Controllers\ImportController;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Auth::routes();

Route::post('/ajax/login', [App\Http\Controllers\Auth\LoginController::class, 'ajaxLogin'])->name('ajax.login');
Route::post('/ajax/register', [App\Http\Controllers\Auth\RegisterController::class, 'ajaxRegister'])->name('ajax.register');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Business Registration
    Route::get('/business/register', [App\Http\Controllers\BusinessController::class, 'create'])->name('business.register');
    Route::post('/business/register', [App\Http\Controllers\BusinessController::class, 'store'])->name('business.store');

    // Admin — Business Types
    Route::prefix('admin/business-types')->name('admin.business-types.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BusinessTypeController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\BusinessTypeController::class, 'store'])->name('store');
        Route::put('/{businessType}', [App\Http\Controllers\Admin\BusinessTypeController::class, 'update'])->name('update');
        Route::delete('/{businessType}', [App\Http\Controllers\Admin\BusinessTypeController::class, 'destroy'])->name('destroy');
        Route::patch('/{businessType}/toggle', [App\Http\Controllers\Admin\BusinessTypeController::class, 'toggle'])->name('toggle');
    });

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
    Route::get('/categories/party', [CategoryController::class, 'party'])->name('categories.party');
    Route::get('/categories/expense', [CategoryController::class, 'expense'])->name('categories.expense');
    Route::get('/categories/income', [CategoryController::class, 'income'])->name('categories.income');
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
    Route::resource('expenses', ExpenseController::class)->except(['create', 'show']);

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
    Route::get('/reports/chart-data', [ReportController::class, 'chartData'])->name('reports.chart-data');

    // Incomes
    Route::resource('incomes', IncomeController::class)->except(['create', 'show']);

    // Payments
    Route::resource('payments', PaymentController::class)->except(['create', 'show', 'edit', 'update']);

    // Quotations
    Route::resource('quotations', QuotationController::class)->except(['create', 'show', 'edit', 'update']);
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');

    // Returns
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::post('/returns/sale', [ReturnController::class, 'storeSaleReturn'])->name('returns.sale.store');
    Route::post('/returns/purchase', [ReturnController::class, 'storePurchaseReturn'])->name('returns.purchase.store');

    // Cash Flow & Accounts
    Route::get('/cash-flow', [CashFlowController::class, 'index'])->name('cash-flow.index');
    Route::post('/cash-flow/accounts', [CashFlowController::class, 'storeAccount'])->name('cash-flow.accounts.store');
    Route::delete('/cash-flow/accounts/{account}', [CashFlowController::class, 'destroyAccount'])->name('cash-flow.accounts.destroy');

    // Business Profile
    Route::get('/business/profile', [BusinessProfileController::class, 'show'])->name('business.profile');
    Route::put('/business/profile', [BusinessProfileController::class, 'update'])->name('business.profile.update');
    Route::put('/business/printer-settings', [BusinessProfileController::class, 'updatePrinter'])->name('business.printer.update');
    Route::post('/business/dark-mode', function () {
        auth()->user()->business->update(['dark_mode' => request('dark_mode')]);
        return response()->json(['success' => true]);
    })->name('business.dark-mode');

    // Reminders
    Route::resource('reminders', ReminderController::class)->except(['create', 'show', 'edit', 'update']);

    // Cards
    Route::get('/cards/greeting', [CardController::class, 'greetingIndex'])->name('cards.greeting');
    Route::post('/cards/greeting', [CardController::class, 'greetingStore'])->name('cards.greeting.store');
    Route::delete('/cards/greeting/{card}', [CardController::class, 'greetingDestroy'])->name('cards.greeting.destroy');
    Route::get('/cards/greeting/share/{token}', [CardController::class, 'greetingShare'])->name('cards.greeting.share');
    Route::get('/cards/business', [CardController::class, 'businessIndex'])->name('cards.business');
    Route::post('/cards/business', [CardController::class, 'businessStore'])->name('cards.business.store');
    Route::delete('/cards/business/{card}', [CardController::class, 'businessDestroy'])->name('cards.business.destroy');
    Route::get('/cards/business/share/{token}', [CardController::class, 'businessShare'])->name('cards.business.share');

    // Settings
    Route::get('/settings/app', [SettingsController::class, 'app'])->name('settings.app');
    Route::put('/settings/app', [SettingsController::class, 'updateApp'])->name('settings.app.update');
    Route::get('/settings/invoice', [SettingsController::class, 'invoice'])->name('settings.invoice');
    Route::put('/settings/invoice', [SettingsController::class, 'updateInvoice'])->name('settings.invoice.update');
    Route::get('/settings/transaction', [SettingsController::class, 'transaction'])->name('settings.transaction');
    Route::put('/settings/transaction', [SettingsController::class, 'updateTransaction'])->name('settings.transaction.update');

    // Calculators
    Route::get('/calculators/emi', function () { return view('calculators.emi'); })->name('calculators.emi');
    Route::get('/calculators/interest', function () { return view('calculators.interest'); })->name('calculators.interest');
    Route::get('/calculators/tax', function () { return view('calculators.tax'); })->name('calculators.tax');

    // Notebook
    Route::get('/notebook', [NoteController::class, 'index'])->name('notebook.index');
    Route::post('/notebook', [NoteController::class, 'store'])->name('notebook.store');
    Route::put('/notebook/{note}', [NoteController::class, 'update'])->name('notebook.update');
    Route::delete('/notebook/{note}', [NoteController::class, 'destroy'])->name('notebook.destroy');

    // Bill Gallery
    Route::get('/bill-gallery', [BillGalleryController::class, 'index'])->name('bill-gallery.index');
    Route::post('/bill-gallery', [BillGalleryController::class, 'store'])->name('bill-gallery.store');
    Route::delete('/bill-gallery/{bill}', [BillGalleryController::class, 'destroy'])->name('bill-gallery.destroy');

    // Import Data
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/parties', [ImportController::class, 'importParties'])->name('import.parties');
    Route::post('/import/items', [ImportController::class, 'importItems'])->name('import.items');
});
