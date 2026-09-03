<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_project_for_a_client(): void
    {
        $client = Client::factory()->create();

        $payload = [
            'client_id' => $client->id,
            'title' => 'Fall Family Session',
            'description' => 'Outdoor session at the park.',
            'project_date' => '2026-10-05',
            'status' => 'pending',
        ];

        $this->postJson('/api/projects', $payload)
            ->assertCreated()
            ->assertJsonPath('data.title', 'Fall Family Session')
            ->assertJsonPath('data.client_id', $client->id);

        $this->assertDatabaseHas('projects', ['title' => 'Fall Family Session']);
    }

    public function test_it_rejects_a_project_for_a_nonexistent_client(): void
    {
        $this->postJson('/api/projects', [
            'client_id' => 999,
            'title' => 'Orphan Project',
            'project_date' => '2026-01-01',
        ])->assertStatus(422)->assertJsonValidationErrors('client_id');
    }

    public function test_it_lists_projects_with_their_client(): void
    {
        $client = Client::factory()->create(['name' => 'Alex Rivera']);
        Project::factory()->create(['client_id' => $client->id]);

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonPath('data.0.client_name', 'Alex Rivera');
    }

    public function test_it_deletes_a_project(): void
    {
        $project = Project::factory()->create();

        $this->deleteJson("/api/projects/{$project->id}")->assertNoContent();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
