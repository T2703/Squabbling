<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TimGavin\LaravelBlock\LaravelBlock;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LaravelBlock;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function boards()
    {
        return $this->belongsToMany(BoardModel::class, 'board_user', 'user_id', 'board_id');
    }

    public function likedDiscussions()
    {
        return $this->belongsToMany(Discussion::class, 'likes')->withTimestamps();
    }

    public function dislikedDiscussions()
    {
        return $this->belongsToMany(Discussion::class, 'dislikes')->withTimestamps();
    }

    public function likedComments()
    {
        return $this->belongsToMany(CommentModel::class, 'comment_like', 'user_id', 'comment_id')->withTimestamps();
    }

    public function dislikedComments()
    {
        return $this->belongsToMany(CommentModel::class, 'comment_dislike', 'user_id', 'comment_id')->withTimestamps();
    }
    
}
