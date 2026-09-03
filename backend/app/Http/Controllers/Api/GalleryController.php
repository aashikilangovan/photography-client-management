<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryRequest;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\PublicGalleryResource;
use App\Models\Gallery;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class GalleryController extends Controller
{
    /**
     * List every gallery that belongs to a given project.
     * Route: GET /api/projects/{project}/galleries
     */
    public function index(Project $project): AnonymousResourceCollection
    {
        return GalleryResource::collection($project->galleries()->latest()->get());
    }

    /**
     * Create a gallery under a given project.
     * Route: POST /api/projects/{project}/galleries
     */
    public function store(StoreGalleryRequest $request, Project $project): JsonResponse
    {
        $gallery = $project->galleries()->create($request->validated());

        return (new GalleryResource($gallery))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Publicly view a single gallery by its share slug — no auth, and
     * intentionally returns fewer fields than the admin GalleryResource.
     * Route: GET /api/public/galleries/{slug}
     */
    public function showPublic(string $slug): PublicGalleryResource
    {
        $gallery = Gallery::with('project')->where('slug', $slug)->firstOrFail();

        return new PublicGalleryResource($gallery);
    }
}
