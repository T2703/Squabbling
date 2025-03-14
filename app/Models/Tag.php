<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = ['name'];

    public function boards()
    {
        return $this->belongsToMany(BoardModel::class, 'board_tag');
    }
}
