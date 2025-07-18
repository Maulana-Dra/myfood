<?php

use Livewire\Livewire;
use App\Models\Transaction;
use App\Livewire\Pages\CartPage;
use App\Livewire\Pages\HomePage;
use App\Livewire\Pages\ScanPage;
use App\Livewire\Pages\PromoPage;
use App\Livewire\Pages\DetailPage;
use App\Livewire\Pages\AllFoodPage;
use App\Livewire\Pages\CheckoutPage;
use App\Livewire\Pages\FavoritePage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRController;
use App\Http\Middleware\CheckTableNumber;
use App\Livewire\Pages\PaymentFailurePage;
use App\Livewire\Pages\PaymentSuccessPage;
use App\Http\Controllers\TransactionController;

Route::middleware(CheckTableNumber::class)->group(function(){
    // Beranda | Home
    Route::get('/', HomePage::class)->name('home');
    // Semua Makanan | All Food
    Route::get('/food', AllFoodPage::class)->name('product.index');
    // Makanan Favorit | Favorite Food
    Route::get('/food/favorite', FavoritePage::class)->name('product.favorite');
    // Makanan Promo | Today's Promo
    Route::get('/food/promo', PromoPage::class)->name('product.promo');
     // Detail Makanan | Food Details
     Route::get('/food/{id}', DetailPage::class)->name('product.detail');
     Route::get('/pesanan', [TransactionController::class, 'pesananSaya'])->name('user.pesanan');
});



Route::middleware(CheckTableNumber::class)->controller(TransactionController::class)->group(function(){
    Route::get('/cart', CartPage::class)->name('payment.cart');
    Route::get('/checkout', CheckoutPage::class)->name('payment.checkout');

    // Proses pembayaran
    Route::middleware('throttle:10,1')->post('/payment/{token}', 'handlePayment')->name('payment');
    Route::get('/payment', function () {
    abort(404);
    })->name('payment.dummy'); // Atau tanpa name sama sekali


    // Status pembayaran
    Route::get('/payment/status/{id}', 'paymentStatus')->name('payment.status');
    Route::get('/payment/success', PaymentSuccessPage::class)->name('payment.success');
    Route::get('/payment/failure', PaymentFailurePage::class)->name('payment.failure');

    
    Route::get('/cek-status-pesanan', [TransactionController::class, 'cekStatusPesanan']);

});

// Webhook update pembayaran
Route::post('/payment/webhook', [TransactionController::class, 'handleWebhook'])->name('payment.webhook')->withoutMiddleware(['auth']);


Route::controller(QRController::class)->group(function (){
    Route::post('/store-qr-result', 'storeResult')->name('product.scan.store');
    // Scanner
    Route::get('/scan', ScanPage::class)->name('product.scan');
    Route::get('/{tableNumber}', 'checkCode')->name('product.scan.table');
});