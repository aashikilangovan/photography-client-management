<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Gallery;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * A small, hand-written set of demo rows — just enough so the frontend
     * has something real to display right after `migrate --seed`, without
     * the extra machinery of model factories for a project this size.
     */
    public function run(): void
    {
        $sarah = Client::create([
            'name' => 'Sarah Chen',
            'email' => 'sarah.chen@example.com',
            'phone' => '604-555-0142',
            'notes' => 'Prefers golden-hour outdoor shoots. Referred by Maria.',
        ]);

        $daniel = Client::create([
            'name' => 'Daniel Okafor',
            'email' => 'daniel.okafor@example.com',
            'phone' => '778-555-0199',
            'notes' => 'Corporate headshots, repeat client.',
        ]);

        $wedding = Project::create([
            'client_id' => $sarah->id,
            'title' => 'Chen–Park Wedding',
            'description' => 'Full-day wedding coverage at Stanley Park.',
            'project_date' => '2026-06-14',
            'status' => 'in_progress',
        ]);

        $engagement = Project::create([
            'client_id' => $sarah->id,
            'title' => 'Engagement Session',
            'description' => 'Sunset engagement shoot, Kitsilano Beach.',
            'project_date' => '2026-03-02',
            'status' => 'completed',
        ]);

        $headshots = Project::create([
            'client_id' => $daniel->id,
            'title' => 'Team Headshots 2026',
            'description' => 'Annual headshot refresh for the leadership team.',
            'project_date' => '2026-04-20',
            'status' => 'pending',
        ]);

        Gallery::create([
            'project_id' => $engagement->id,
            'name' => 'Kitsilano Beach Highlights',
            'description' => 'Selects from the engagement shoot.',
            // Picsum (picsum.photos) serves real, freely-usable stock photos —
            // used here instead of a plain color+text placeholder purely so the
            // demo data looks like an actual gallery. A seed keeps each URL
            // pointing at the same photo every time (not truly random).
            'image_urls' => [
                'https://picsum.photos/seed/engagement-session-1/800/600',
                'https://picsum.photos/seed/engagement-session-2/800/600',
                'https://picsum.photos/seed/engagement-session-3/800/600',
            ],
        ]);

        Gallery::create([
            'project_id' => $wedding->id,
            'name' => 'Ceremony Preview',
            'description' => 'First look — full gallery coming soon.',
            'image_urls' => [
                'https://picsum.photos/seed/wedding-ceremony-1/800/600',
            ],
        ]);
    }
}
