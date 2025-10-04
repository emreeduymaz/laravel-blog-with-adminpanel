<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $commentData = [
            'content' => $validated['content'],
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => (string) ($request->header('User-Agent') ?? ''),
        ];

        $commentData['user_id'] = auth()->id();
        $commentData['name'] = auth()->user()->name;
        $commentData['email'] = auth()->user()->email;

        $post->comments()->create($commentData);

        return back()->with('status', 'Comment submitted and awaiting approval.');
    }
}


