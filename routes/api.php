<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentCallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route untuk menerima notifikasi dari Midtrans
Route::post('/midtrans/callback', [PaymentCallbackController::class, 'handle']);

// Route untuk menerima webhook dari Meta WhatsApp Cloud API
// Alamat: https://domain-anda.com/api/whatsapp/webhook
Route::match(['get', 'post'], '/whatsapp/webhook', [\App\Http\Controllers\Api\WhatsAppWebhookController::class, 'handle']);
