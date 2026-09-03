<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_gallery_under_a_project_with_an_auto_generated_slug(): void
    {
        $project = Project::factory()->create();

        $payload = [
            'name' => 'Ceremony Highlights',
            'description' => 'Best shots from the ceremony.',
            'image_urls' => ['https://placehold.co/800x600?text=1'],
        ];

        $response = $this->postJson("/api/projects/{$project->id}/galleries", $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Ceremony Highlights');

        $this->assertNotEmpty($response->json('data.slug'));
        $this->assertDatabaseHas('galleries', ['project_id' => $project->id, 'name' => 'Ceremony Highlights']);
    }

    public function test_it_lists_galleries_for_a_project(): void
    {
        $project = Project::factory()->create();
        Gallery::factory()->count(2)->create(['project_id' => $project->id]);

        $this->getJson("/api/projects/{$project->id}/galleries")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_the_public_link_returns_a_gallery_with_no_client_or_admin_fields(): void
    {
        $gallery = Gallery::factory()->create(['name' => 'Public Preview']);

        $response = $this->getJson("/api/public/galleries/{$gallery->slug}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Public Preview')
            ->assertJsonMissingPath('data.id')
            ->assertJsonMissingPath('data.project_id');

        $response->assertJsonStructure(['data' => ['name', 'description', 'image_urls', 'project_title']]);
    }

    public function test_an_unknown_slug_returns_404(): void
    {
        $this->getJson('/api/public/galleries/does-not-exist')->assertNotFound();
    }
}
