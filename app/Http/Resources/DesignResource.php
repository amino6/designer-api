<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'likes' => $this->likes->count(),
            'images' => $this->images,
            'tags_list' => [
                'tag' => $this->tagArray,
                'tag_normalized' => $this->tagArrayNormalized,
            ],
            'is_live' => $this->is_live,
            'description' => $this->description,
            'team' => $this->team ? new TeamResource($this->whenLoaded('team')) : null,
            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}
