<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_clients(): void
    {
        Client::factory()->count(3)->create();

        $this->getJson('/api/clients')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_it_creates_a_client(): void
    {
        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '555-0100',
            'notes' => 'Met at a bridal expo.',
        ];

        $this->postJson('/api/clients', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.email', 'jane@example.com');

        $this->assertDatabaseHas('clients', ['email' => 'jane@example.com']);
    }

    public function test_it_rejects_a_client_without_required_fields(): void
    {
        $this->postJson('/api/clients', ['name' => 'Missing Email'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_it_updates_a_client(): void
    {
        $client = Client::factory()->create(['name' => 'Old Name']);

        $this->putJson("/api/clients/{$client->id}", ['name' => 'New Name'])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name');
    }

    public function test_it_deletes_a_client(): void
    {
        $client = Client::factory()->create();

        $this->deleteJson("/api/clients/{$client->id}")->assertNoContent();

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }
}
