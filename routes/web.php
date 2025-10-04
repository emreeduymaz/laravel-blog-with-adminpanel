<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

// Root -> Blog
Route::get('/', function () {
    return redirect('/blog');
});

// Dashboard removed; redirect any access to blog
Route::get('/dashboard', function () {
    return redirect()->route('blog.index');
})->name('dashboard');

// Authenticated profile routes (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Blog routes
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/category/{category:slug}', [BlogController::class, 'category'])->name('category');
    Route::get('/tag/{tag:slug}', [BlogController::class, 'tag'])->name('tag');
    Route::get('/{post:slug}', [BlogController::class, 'show'])->name('show');
    Route::post('/{post:slug}/comments', [CommentController::class, 'store'])->middleware(['auth'])->name('comments.store');
});

// Breeze auth routes
require __DIR__.'/auth.php';
