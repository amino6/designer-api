<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Storage;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'title' => $this->title,
            'slug' => $this->slug,
            'title' => $this->title,
            'likes' => $this->relationLoaded('likes') ? $this->likes->count() : new MissingValue(),
            'liked' => auth()->check() ? $this->alreadyLikedByUser() : new MissingValue(),
            'images' => $this->images,
            'tmp_image' => Storage::disk($this->disk)->exists('uploads/designs/original/' . $this->image) ? 
                Storage::disk($this->disk)->url('uploads/designs/original/' . $this->image) :  new MissingValue(),
            'tags_list' => $this->relationLoaded('tags') ? [
                'tag' => $this->tagArray,
                'tag_normalized' => $this->tagArrayNormalized,
            ] : new MissingValue(),
            'is_live' => $this->is_live,
            'description' => $this->description,
            'team' => $this->relationLoaded('team') ? new TeamResource($this->team) : new MissingValue(),
            'comments' => $this->relationLoaded('comments') && $this->comments->count() > 0 ?
                CommentResource::collection($this->comments)
                : [],
            'created_at' => date('Y-m-d H:i', strtotime($this->created_at)),
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}
