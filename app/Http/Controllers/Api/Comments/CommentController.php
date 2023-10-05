<?php

namespace App\Http\Controllers\Api\Comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Design;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function show(Design $design, Comment $comment)
    {
        return new CommentResource($comment);
    }

    public function store(StoreCommentRequest $request, Design $design)
    {
        $comment = $design->comments()->create([
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return new CommentResource($comment);
    }

    public function update(StoreCommentRequest $request, Design $design, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'body' => ['required']
        ]);

        $comment->update([
            "body" => $request->body,
        ]);

        return new CommentResource($comment);
    }

    public function destroy(Design $design, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(status: 200);
    }
}
