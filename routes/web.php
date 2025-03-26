<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentDislikeController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\DislikesController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Dashboard routes.
Route::redirect('/', '/board')->name('dashboard');

Route::get('/board/all', [BoardController::class, 'allBoards'])->name('board.all');
Route::get('/board/search', [BoardController::class, 'search'])->name('board.search');
Route::get('/board/searchown', [BoardController::class, 'searchOwn'])->name('board.searchOwn');

// Board routes.
Route::middleware(['auth', 'verified'])->group(function() {
    // Board routes.
    Route::resource('board', BoardController::class);
    Route::get('/board/all', [BoardController::class, 'allBoards'])->name('board.all');
    Route::get('/board/search', [BoardController::class, 'search'])->name('board.search');
    Route::post('/boards/{board}/join', [BoardController::class, 'join'])->name('board.join');
    Route::post('/boards/{board}/leave', [BoardController::class, 'leave'])->name('board.leave');

    // Discussion Routes
    Route::resource('board.discussion', DiscussionController::class);

    // Comment routes.
    Route::resource('comment', CommentController::class);
    Route::post('/discussion/{discussion}/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::post('/comment/{comment}/reply', [CommentController::class, 'reply'])->name('comment.reply');

    // Discussion likes & dislikes routes
    Route::post('/discussions/{discussion}/like', [LikesController::class, 'toggleLike'])->name('discussion.like');
    Route::post('/discussions/{discussion}/dislike', [DislikesController::class, 'toggleDislike'])->name('discussion.dislike');

    // Comment likes & dislikes routes
    Route::post('/comment/{comment}/like', [CommentLikeController::class, 'toggleLike'])->name('comment.like');
    Route::post('/comment/{comment}/dislike', [CommentDislikeController::class, 'toggleDislike'])->name('comment.dislike');
    
    // Block Routes
    Route::resource('blocked', BlockController::class);
    Route::post('/user/{user}/block', [BlockController::class, 'toggleBlock'])->name('user.block');
    Route::post('/unblock-all', [BlockController::class, 'unblockAll'])->name('blocks.unblockAll');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
