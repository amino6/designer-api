<?php

namespace App\Http\Controllers\Comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Design;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Design $design)
    {
        $comment = $design->comments()->create([
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return new CommentResource($comment);
    }
}
