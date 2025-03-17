<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'board_model_id', 'user_id'];
    
    /**
     * Get the user who created the discussion.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the board that this discussion belongs to.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(BoardModel::class);
    }

    /**
     * Get the comments that this discussion belongs to.
     */
    public function comments()
    {
        return $this->hasMany(CommentModel::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    public function dislikes()
    {
        return $this->belongsToMany(User::class, 'dislikes')->withTimestamps();
    }
}
