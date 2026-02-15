<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // コメント投稿
    public function store(CommentRequest $request, Item $item)
    {
        if (!Auth::check()) {
            return back()->with('comment_error', 'コメントを投稿するにはログインが必要です');
        }

        $item->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()
            ->route('items.show', $item->id)
            ->with('success', 'コメントを投稿しました');
    }
}
