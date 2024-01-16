<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FullUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "profile_image" => $this->photo_url,
            "about" => $this->about,
            "available_to_hire" => $this->available_to_hire,
            "designs_count" => $this->designs_count,
            "contact_email" => $this->contact_email,
            "job_title" => $this->job_title,
            "website_url" => $this->website_url
        ];
    }
}
