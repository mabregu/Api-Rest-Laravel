<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;

Route::apiResource('articles', ArticleController::class)->names('api.v1.articles');

// Route::group(['as' => 'api.v1.'], function () {
//     Route::group(['prefix' => 'articles', 'as' => 'articles.'], function () {
//         Route::get('/', [ArticleController::class, 'index'])->name('index');
//         Route::get('/{article}', [ArticleController::class, 'show'])->name('show');
//         Route::post('/', [ArticleController::class, 'store'])->name('store');
//         Route::patch('/{article}', [ArticleController::class, 'update'])->name('update');
//         Route::delete('/{article}', [ArticleController::class, 'destroy'])->name('destroy');
//     });
// });
