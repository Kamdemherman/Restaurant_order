<?php
// FILE: routes/web.php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\PrinterConfigController;
use Illuminate\Support\Facades\Route;

// ── Auth ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class,    'showLoginForm'])->name('login');
    Route::post('/login',   [LoginController::class,    'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',[RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Customer ────────────────────────────────────────────────
Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/',           [MenuController::class,  'index'])->name('menu.index');
    Route::get('/menu/{item}',[MenuController::class,  'item'])->name('menu.item');

    Route::get('/checkout',           [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders',            [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders',             [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',     [OrderController::class, 'show'])->name('orders.show');
    Route::post('/coupon/apply',      [OrderController::class, 'applyCoupon'])->name('coupon.apply');

    Route::get('/profile',                       [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',                       [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',              [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/newsletter',            [ProfileController::class, 'updateNewsletter'])->name('profile.newsletter');
    Route::get('/profile/gdpr/export',           [ProfileController::class, 'gdprExport'])->name('profile.gdpr.export');
    Route::delete('/profile/gdpr/delete',        [ProfileController::class, 'gdprDelete'])->name('profile.gdpr.delete');
});

// ── Admin ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users',                [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}',         [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/approve',[UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/suspend',[UserController::class, 'suspend'])->name('users.suspend');
    Route::delete('/users/{user}',      [UserController::class, 'destroy'])->name('users.destroy');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Menu items
    Route::get('/menus',                              [MenuItemController::class, 'index'])->name('menus.index');
    Route::get('/menus/create',                       [MenuItemController::class, 'create'])->name('menus.create');
    Route::post('/menus',                             [MenuItemController::class, 'store'])->name('menus.store');
    Route::get('/menus/{menu}/edit',                  [MenuItemController::class, 'edit'])->name('menus.edit');
    Route::put('/menus/{menu}',                       [MenuItemController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}',                    [MenuItemController::class, 'destroy'])->name('menus.destroy');
    Route::post('/menus/{menu}/toggle',               [MenuItemController::class, 'toggleAvailability'])->name('menus.toggle');

    // Orders
    Route::get('/orders',                             [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',                     [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status',              [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/print',              [AdminOrderController::class, 'print'])->name('orders.print');
    Route::post('/orders/bulk-print',                 [AdminOrderController::class, 'bulkPrint'])->name('orders.bulk-print');

    // Coupons
    Route::get('/coupons',                            [CouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create',                     [CouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons',                           [CouponController::class, 'store'])->name('coupons.store');
    Route::post('/coupons/bulk',                      [CouponController::class, 'generateBulk'])->name('coupons.bulk');
    Route::post('/coupons/{coupon}/toggle',           [CouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('/coupons/{coupon}',                [CouponController::class, 'destroy'])->name('coupons.destroy');

    // Newsletter
    Route::get('/newsletter',                         [NewsletterController::class, 'index'])->name('newsletter.index');
    Route::get('/newsletter/export',                  [NewsletterController::class, 'export'])->name('newsletter.export');
    Route::delete('/newsletter/{subscription}',       [NewsletterController::class, 'destroy'])->name('newsletter.destroy');

    // Reports
    Route::get('/reports/sales',                      [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/export',                     [ReportController::class, 'exportCsv'])->name('reports.export');

    // Printer
    Route::get('/printer',                            [PrinterConfigController::class, 'index'])->name('printer.index');
    Route::post('/printer',                           [PrinterConfigController::class, 'store'])->name('printer.store');
    Route::put('/printer/{printerConfig}',            [PrinterConfigController::class, 'update'])->name('printer.update');
    Route::post('/printer/{printerConfig}/test',      [PrinterConfigController::class, 'testPrint'])->name('printer.test');
    Route::delete('/printer/{printerConfig}',         [PrinterConfigController::class, 'destroy'])->name('printer.destroy');
});
