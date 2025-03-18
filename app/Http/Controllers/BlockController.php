<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function toggleBlock(User $user)
    {
        // Unblock if the user is already blocked
        if (auth()->user()->isBlocking($user)) {
            auth()->user()->unblock($user);
        }
        else {
            auth()->user()->block($user);
        }

    }
}
