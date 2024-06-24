<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\podUpdationController;
use App\Http\Controllers\QRcodeGenerateController;
use App\Http\Controllers\ImageController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('pod-updations/{lrnumber}', [podUpdationController::class, 'podUpdation']);
Route::get('/generate-qrcode/{lrnumber}', [QRcodeGenerateController::class,'qrcode']);
Route::get('image-upload', [ ImageController::class, 'upload' ])->name('image.upload');
Route::post('image-store', [ ImageController::class, 'store' ])->name('image.upload.post');

Route::get('/show', [QRcodeGenerateController::class, 'show']);
