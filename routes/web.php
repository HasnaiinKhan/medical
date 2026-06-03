<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\PincodeController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminMedicineController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminRefundController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\MedicineImportExportController;
use Illuminate\Support\Facades\Route;

// ── Webhooks (no auth, no CSRF) ───────────────────────────────────────────────
Route::post('/webhooks/razorpay', [WebhookController::class, 'razorpay'])
    ->name('webhooks.razorpay')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ── Chatbot ───────────────────────────────────────────────────────────────────
Route::post('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot.chat');
Route::post('/chatbot/order-detail', [\App\Http\Controllers\ChatbotController::class, 'orderDetail'])->name('chatbot.orderDetail')->middleware('auth');

// ── Public ────────────────────────────────────────────────────────────────────
Route::get('/', HomeController::class)->name('home');

Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
Route::get('/medicines/suggestions', [MedicineController::class, 'suggestions'])->name('medicines.suggestions');
Route::get('/medicines/{medicine:slug}', [MedicineController::class, 'show'])->name('medicines.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{medicine}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{medicine}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/pincode/lookup', [PincodeController::class, 'lookup'])->name('pincode.lookup');
Route::post('/delivery-pin', [PincodeController::class, 'setDelivery'])->name('delivery_pin.set');

// ── Auth (guests only) ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [RazorpayController::class, 'create'])->name('checkout.create');
    Route::post('/checkout/order', [RazorpayController::class, 'createOrder'])->name('checkout.order');
    Route::post('/checkout/verify', [RazorpayController::class, 'verifyPayment'])->name('checkout.verify');
    Route::get('/orders/{order}/thank-you', [RazorpayController::class, 'thankyou'])->name('checkout.thankyou');

    Route::get('/my-orders', [OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{order}', [OrderHistoryController::class, 'show'])->name('orders.show');

    // Refunds (customer)
    Route::get('/my-orders/{order}/refund', [RefundController::class, 'create'])->name('refunds.create');
    Route::post('/my-orders/{order}/refund', [RefundController::class, 'store'])->name('refunds.store');
});

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Medicine CRUD
    Route::resource('medicines', AdminMedicineController::class)->except(['show']);

    // Categories
    Route::get('categories',              [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories',             [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::delete('categories/{category}',[AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Import / Export
    Route::get('medicines-import',          [MedicineImportExportController::class, 'importForm'])->name('medicines.import.form');
    Route::post('medicines-import',         [MedicineImportExportController::class, 'import'])->name('medicines.import');
    Route::get('medicines-export',          [MedicineImportExportController::class, 'export'])->name('medicines.export');
    Route::get('medicines-export-template', [MedicineImportExportController::class, 'template'])->name('medicines.template');

    // Orders
    Route::get('orders',                          [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}',                  [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status',         [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('orders/bulk-status',             [AdminOrderController::class, 'bulkStatus'])->name('orders.bulkStatus');
    Route::patch('orders/{order}/dispatched',     [AdminRefundController::class, 'toggleDispatched'])->name('orders.toggleDispatched');

    // Refunds (admin)
    Route::get('refunds',                         [AdminRefundController::class, 'index'])->name('refunds.index');
    Route::get('refunds/{refund}',                [AdminRefundController::class, 'show'])->name('refunds.show');
    Route::post('refunds/{refund}/approve',       [AdminRefundController::class, 'approve'])->name('refunds.approve');
    Route::post('refunds/{refund}/processed',     [AdminRefundController::class, 'markProcessed'])->name('refunds.markProcessed');
    Route::post('refunds/{refund}/reject',        [AdminRefundController::class, 'reject'])->name('refunds.reject');

    // Settings
    Route::get('settings/notifications',          [AdminSettingsController::class, 'notifications'])->name('settings.notifications');
    Route::post('settings/notifications',         [AdminSettingsController::class, 'saveNotifications'])->name('settings.notifications.save');
    Route::get('settings/notifications/test/{channel}', [AdminSettingsController::class, 'testNotification'])->name('settings.notifications.test');

    // AI Medicine Generator
    Route::post('ai/medicine-generate', [\App\Http\Controllers\Admin\AIMedicineController::class, 'generate'])->name('ai.medicine.generate');
    Route::post('ai/medicine-detail',   [\App\Http\Controllers\Admin\AIMedicineController::class, 'detail'])->name('ai.medicine.detail');
});
