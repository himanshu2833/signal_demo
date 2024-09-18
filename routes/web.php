<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignalController;
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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/',[SignalController::class,'index'])->name('signals.index');
Route::post('/signals/store',[SignalController::class,'store'])->name('signals.store');
Route::get('/signals/{id}/edit',[SignalController::class,'edit'])->name('signals.edit');
Route::post('/signals/update/{id}',[SignalController::class,'update'])->name('signals.update');
Route::delete('/signals/delete/{id}',[SignalController::class,'destroy'])->name('signals.destory');
