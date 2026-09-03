<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'name' => $this->name,
            'description' => $this->description,
            'image_urls' => $this->image_urls,
            'slug' => $this->slug,
            'public_path' => "/g/{$this->slug}",
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
