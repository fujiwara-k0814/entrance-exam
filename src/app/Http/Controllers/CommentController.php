<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $item_id)
    {
        $itemComment = [
            'item_id' => $item_id,
            'body' => $request->input('body')
        ];

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->comments()->create($itemComment);

        return redirect("/item/$item_id");
    }
}
