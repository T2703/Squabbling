<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index()
    {
        print(auth()->user()->getBlocking());
        return view('block.index');
    }

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

        print($user);

    }

    public function unblockAll()
    {
        $blockers = auth()->user()->getBlocking();
        
        foreach ($blockers as $blocker) {
            // Access the actual user from the 'blocking' relationship
            $userToUnblock = $blocker->blocking;
    
            if ($userToUnblock) {
                // Unblock the user
                auth()->user()->unblock($userToUnblock);
                print("Unblocked: " . $userToUnblock->email);  // Debugging output
            }
        }

        //return redirect()->route('blocked.index')->with('message', 'All users have been unblocked successfully.'); 
    }

    //{"id":1,"email":"test@mail.com","email_verified_at":null,"created_at":"2025-03-19T16:35:26.000000Z","updated_at":"2025-03-19T16:35:26.000000Z"}
}
