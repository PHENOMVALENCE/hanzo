<?php

use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\EstimateDefaultController;
use App\Http\Controllers\Admin\FreightRateController;
use App\Http\Controllers\Admin\TransportDefaultController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\QuoteBuilderController;
use App\Http\Controllers\Admin\RfqController as AdminRfqController;
use App\Http\Controllers\Buyer\CatalogController;
use App\Http\Controllers\Buyer\DashboardController as BuyerDashboardController;
use App\Http\Controllers\Buyer\ProductController as BuyerProductController;
use App\Http\Controllers\Buyer\DocumentController as BuyerDocumentController;
use App\Http\Controllers\Buyer\MessageController;
use App\Http\Controllers\Buyer\SavedController;
use App\Http\Controllers\Buyer\SupplierController;
use App\Http\Controllers\Buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Buyer\PaymentController as BuyerPaymentController;
use App\Http\Controllers\Buyer\QuoteController;
use App\Http\Controllers\Buyer\RfqController as BuyerRfqController;
use App\Http\Controllers\Factory\DashboardController as FactoryDashboardController;
use App\Http\Controllers\Factory\OrderController as FactoryOrderController;
use App\Http\Controllers\Factory\ProductController as FactoryProductController;
use App\Http\Controllers\Factory\RfqController as FactoryRfqController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* Public (no auth) */
Route::get('/how-it-works', fn () => view('public.how-it-works'))->name('how-it-works');
Route::get('/about', fn () => view('public.about'))->name('about');
Route::get('/factory/invite/accept', [\App\Http\Controllers\Auth\FactoryInviteController::class, 'show'])->name('factory.invite.accept');
Route::post('/factory/invite/accept', [\App\Http\Controllers\Auth\FactoryInviteController::class, 'store'])->name('factory.invite.accept.store');
Route::get('/partner-with-hanzo', [\App\Http\Controllers\Public\PartnerController::class, 'show'])->name('partner-with-hanzo');
Route::post('/partner-with-hanzo', [\App\Http\Controllers\Public\PartnerController::class, 'store'])->name('partner-with-hanzo.store');
Route::get('/categories', fn () => view('public.categories.index', ['categories' => \App\Models\Category::where('active', true)->orderBy('name')->get()]))->name('categories.index');
Route::get('/categories/{category}', function ($category) {
    $cat = \App\Models\Category::where('slug', $category)->orWhere('id', $category)->firstOrFail();
    return view('public.categories.show', ['category' => $cat]);
})->name('categories.show');

Route::get('/', function () {
    if (auth()->guest()) {
        return view('landing');
    }

    if (auth()->user()->status !== 'approved' && ! auth()->user()->hasRole('admin')) {
        return redirect()->route('pending-approval');
    }

    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->hasRole('buyer')) {
        return redirect()->route('buyer.dashboard');
    }
    if (auth()->user()->hasRole('factory')) {
        return redirect()->route('factory.dashboard');
    }

    return redirect()->route('dashboard');
})->name('home');

Route::get('pending-approval', function () {
    return view('auth.pending-approval');
})->middleware(['auth', 'verified'])->name('pending-approval');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->hasRole('buyer')) {
        return redirect()->route('buyer.dashboard');
    }
    if ($user->hasRole('factory')) {
        return redirect()->route('factory.dashboard');
    }

    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'verified', 'approved', 'role:admin', 'locale'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/approvals/buyers', [ApprovalController::class, 'buyers'])->name('approvals.buyers');
    Route::get('/approvals/factories', [ApprovalController::class, 'factories'])->name('approvals.factories');
    Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    Route::post('/approvals/{id}/request-more-info', [ApprovalController::class, 'requestMoreInfo'])->name('approvals.requestMoreInfo');

    Route::get('/rfqs', [AdminRfqController::class, 'index'])->name('rfqs.index');
    Route::get('/rfqs/{rfq}', [AdminRfqController::class, 'show'])->name('rfqs.show');
    Route::post('/rfqs/{rfq}/assign', [AdminRfqController::class, 'assign'])->name('rfqs.assign');

    Route::get('/quote-builder/{rfq}', [QuoteBuilderController::class, 'edit'])->name('quote-builder.edit');
    Route::post('/quote-builder/{rfq}', [QuoteBuilderController::class, 'store'])->name('quote-builder.store');

    Route::resource('freight-rates', FreightRateController::class)->except(['show'])->names('freight-rates');
    Route::get('/transport-defaults', [TransportDefaultController::class, 'edit'])->name('transport-defaults.edit');
    Route::put('/transport-defaults', [TransportDefaultController::class, 'update'])->name('transport-defaults.update');
    Route::get('/estimate-defaults', [EstimateDefaultController::class, 'edit'])->name('estimate-defaults.edit');
    Route::put('/estimate-defaults', [EstimateDefaultController::class, 'update'])->name('estimate-defaults.update');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/milestone', [AdminOrderController::class, 'updateMilestone'])->name('orders.updateMilestone');

    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/proof', [AdminPaymentController::class, 'proof'])->name('payments.proof');
    Route::post('/payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

    Route::get('/documents', [AdminDocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents/upload', [AdminDocumentController::class, 'upload'])->name('documents.upload');
    Route::delete('/documents/{document}', [AdminDocumentController::class, 'destroy'])->name('documents.destroy');

    Route::resource('users', AdminUserController::class)->names('users')->except(['show']);
<<<<<<< HEAD
    Route::get('/invite-factory', [\App\Http\Controllers\Admin\InviteFactoryController::class, 'create'])->name('invite-factory.create');
    Route::post('/invite-factory', [\App\Http\Controllers\Admin\InviteFactoryController::class, 'store'])->name('invite-factory.store');
=======

    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/approve', [AdminProductController::class, 'approve'])->name('products.approve');
    Route::post('/products/{product}/disable', [AdminProductController::class, 'disable'])->name('products.disable');
>>>>>>> 3a34daee (Hanzo in b2b style)
});

Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch')->middleware(['web']);
Route::post('/currency', [CurrencyController::class, 'switch'])->name('currency.switch')->middleware(['web']);

Route::middleware(['auth', 'verified', 'approved', 'role:buyer', 'locale'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [BuyerDashboardController::class, 'index'])->name('dashboard');

<<<<<<< HEAD
    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/catalog/{category}', [CatalogController::class, 'show'])->name('catalog.show');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/{factory}', [SupplierController::class, 'show'])->name('suppliers.show');

    Route::get('/saved', [SavedController::class, 'index'])->name('saved.index');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');

    Route::get('/settings', fn () => redirect()->route('profile.edit'))->name('settings');
=======
    Route::get('/products', [BuyerProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [BuyerProductController::class, 'show'])->name('products.show');
>>>>>>> 3a34daee (Hanzo in b2b style)

    Route::get('/rfqs', [BuyerRfqController::class, 'index'])->name('rfqs.index');
    Route::get('/rfqs/create', [BuyerRfqController::class, 'create'])->name('rfqs.create');
    Route::post('/rfqs', [BuyerRfqController::class, 'store'])->name('rfqs.store');
    Route::get('/rfqs/{rfq}', [BuyerRfqController::class, 'show'])->name('rfqs.show');

    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quotation}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{quotation}/accept', [QuoteController::class, 'accept'])->name('quotes.accept');
    Route::post('/quotes/{quotation}/reject', [QuoteController::class, 'reject'])->name('quotes.reject');

    Route::get('/orders', [BuyerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [BuyerOrderController::class, 'show'])->name('orders.show');

    Route::get('/orders/{order}/payments/create', [BuyerPaymentController::class, 'create'])->name('payments.create');
    Route::post('/orders/{order}/payments', [BuyerPaymentController::class, 'store'])->name('payments.store');

    Route::get('/orders/{order}/documents', [BuyerDocumentController::class, 'index'])->name('orders.documents');
});

Route::middleware(['auth', 'verified', 'approved', 'role:factory', 'locale'])->prefix('factory')->name('factory.')->group(function () {
    Route::get('/dashboard', [FactoryDashboardController::class, 'index'])->name('dashboard');

<<<<<<< HEAD
    Route::resource('products', \App\Http\Controllers\Factory\ProductController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/messages', fn () => redirect()->route('factory.dashboard'))->name('messages.index');
    Route::get('/profile', fn () => redirect()->route('profile.edit'))->name('profile.edit');
    Route::get('/analytics', fn () => redirect()->route('factory.dashboard'))->name('analytics.index');
=======
    Route::resource('products', FactoryProductController::class)->except(['show'])->names('products');
>>>>>>> 3a34daee (Hanzo in b2b style)

    Route::get('/rfqs', [FactoryRfqController::class, 'index'])->name('rfqs.index');
    Route::get('/rfqs/{rfq}', [FactoryRfqController::class, 'show'])->name('rfqs.show');
    Route::post('/rfqs/{rfq}/submit-price', [FactoryRfqController::class, 'submitPrice'])->name('rfqs.submit-price');

    Route::get('/orders', [FactoryOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [FactoryOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/approve', [FactoryOrderController::class, 'approveOrder'])->name('orders.approve');
    Route::post('/orders/{order}/ready-to-ship', [FactoryOrderController::class, 'updateToReadyToShip'])->name('orders.ready-to-ship');
    Route::post('/orders/{order}/production-update', [FactoryOrderController::class, 'submitProductionUpdate'])->name('orders.production-update');
});

require __DIR__ . '/auth.php';
