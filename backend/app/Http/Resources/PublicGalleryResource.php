<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Deliberately slimmer than GalleryResource: this is what an unauthenticated
// visitor sees via the public share link, so no ids, timestamps, or client
// contact info are exposed — just enough to view the gallery.
class PublicGalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'image_urls' => $this->image_urls,
            'project_title' => $this->project->title,
        ];
    }
}
