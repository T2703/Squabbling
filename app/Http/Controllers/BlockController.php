<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function toggleBlock(User $user)
    {
        // Prevent users from blocking themselves
        if (auth()->id() === $user->id) {
            return back()->with('error', "You can't block yourself.");
        }

        // Unblock if the user is already blocked
        if (auth()->user()->isBlocking($user)) {
            auth()->user()->unblock($user);
        }
        else {
            auth()->user()->block($user);
        }

    }
}
