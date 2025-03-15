<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Dashboard routes.
Route::redirect('/', '/board')->name('dashboard');

// Board routes.
Route::middleware(['auth', 'verified'])->group(function() {
    Route::resource('board', BoardController::class);
    Route::post('/boards/{board}/join', [BoardController::class, 'join'])->name('board.join');
    Route::post('/boards/{board}/leave', [BoardController::class, 'leave'])->name('board.leave');
});

// Discussion Routes
Route::middleware(['auth', 'verified'])->group(function() {
    Route::resource('board.discussion', DiscussionController::class);
});

// Comment routes.
Route::middleware(['auth', 'verified'])->group(function() {
    Route::resource('comment', CommentController::class);
    Route::post('/discussion/{discussion}/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::post('/comment/{comment}/reply', [CommentController::class, 'reply'])->name('comment.reply');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
