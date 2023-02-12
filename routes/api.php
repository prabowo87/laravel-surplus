<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */
Route::get('/category',[CategoryController::class, 'get'])->name('category');
Route::put('/category/update',[CategoryController::class, 'update'])->name('category-update');
Route::post('/category/add',[CategoryController::class, 'add'])->name('category-add');
Route::delete('/category/delete',[CategoryController::class, 'delete'])->name('category-delete');

Route::get('/products',[ProductController::class, 'get'])->name('products');
Route::put('/product/update',[ProductController::class, 'update'])->name('product-update');
Route::post('/product/add',[ProductController::class, 'add'])->name('product-add');
Route::delete('/product/delete',[ProductController::class, 'delete'])->name('product-delete');
