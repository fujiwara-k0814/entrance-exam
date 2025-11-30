<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DeliveryAddressController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//未認証操作
Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item_id}', [ItemController::class, 'show']);

//認証、初回登録済操作
Route::middleware(['auth', 'verified', 'first.login'])->group(function(){
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show']);
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store']);
    Route::get('/purchase/address/{item_id}', [DeliveryAddressController::class, 'edit']);
    Route::post('/purchase/address/{item_id}', [DeliveryAddressController::class, 'update']);
    Route::get('/sell', [SellController::class, 'create']);
    Route::post('/sell', [SellController::class, 'store']);
    Route::get('/mypage', [MypageController::class, 'show']);
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store']);
    Route::post('/item/{item_id}/like', [LikeController::class, 'update']);
    Route::get('/message/{item_id}', [MessageController::class, 'show'])
        ->name('user.message');//ヘッダー非表示判別の為'name'付与
    Route::post('/message/{item_id}', [MessageController::class, 'store']);
    Route::post('/message/edit/{message_id}', [MessageController::class, 'update']);
    Route::post('/transaction/evaluation/{item_id}/{targetor_id}/{user_id}', [
        TransactionController::class, 'upsert']);
});

//認証済操作
Route::middleware('auth', 'verified')->group(function(){
    Route::get('/mypage/profile', [MypageController::class, 'create']);
    Route::post('/mypage/profile', [MypageController::class, 'store']);
});

//メール認証操作(システム制約でname付与)
Route::get('/email/verify', [VerifyEmailController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');
Route::post('/email/verification-notification', [VerifyEmailController::class, 'send'])
    ->middleware(['auth', 'throttle:10,1'])
    ->name('verification.send');