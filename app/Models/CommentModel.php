<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentModel extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'user_id', 'discussion_id', 'parent_id'];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Child comments
    public function replies()
    {
        return $this->hasMany(CommentModel::class, 'parent_id');
    }

    // Parent comment
    public function parent()
    {
        return $this->belongsTo(CommentModel::class, 'parent_id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'comment_like', 'comment_id', 'user_id')->withTimestamps();
    }
}
