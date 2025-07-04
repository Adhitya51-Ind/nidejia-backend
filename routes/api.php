<?php

use App\Http\Controllers\API\ListingController;
use App\Http\Controllers\API\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use app\Models\Listing;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'message'=> 'Detail Logged in user',
        'data' => $request->user(),
    ]);
});

Route::resource('listing', ListingController::class)->only(['index','show']);

Route::post('transaction/is-available', [TransactionController::class, 'isAvailable'])->middleware(['auth:sanctum']);


route::resource('transaction', TransactionController::class)->only(['store','index','show'])->middleware(['auth:sanctum']);

require __DIR__.'/auth.php';