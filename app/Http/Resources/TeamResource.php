<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class TeamResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'owner' => new UserResource($this->whenLoaded("owner")),
            'designs' => DesignResource::collection($this->whenLoaded("designs")),
            'total_members' => $this->relationLoaded("members") ? $this->members->count() : new MissingValue(),
            'members' => UserResource::collection($this->whenLoaded("members")),
        ];
    }
}
