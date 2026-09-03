<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'client_name' => $this->whenLoaded('client', fn () => $this->client->name),
            'title' => $this->title,
            'description' => $this->description,
            'project_date' => $this->project_date?->toDateString(),
            'status' => $this->status,
            'galleries_count' => $this->whenCounted('galleries'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
