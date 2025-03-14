<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BoardModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'user_id'
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'board_tag');
    }

    public function discussion()
    {
        return $this->hasMany(Discussion::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'board_user', 'board_id', 'user_id');
    }

}
