<?php

namespace Database\Seeders;

use App\Models\Workshop;
use Illuminate\Database\Seeder;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        $workshops = [
            [
                'title'            => 'Anxiety & the Immigrant Mind',
                'description'      => 'Exploring cultural nuances in anxiety management and identity preservation for those navigating life between two worlds.',
                'workshop_date'    => now()->addDays(6)->toDateString(),
                'workshop_time'    => '7:00 PM EST',
                'duration_minutes' => 75,
                'image_url'        => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?q=80&w=800&auto=format&fit=crop',
                'status'           => 'upcoming',
                'zoom_link'        => null,
                'is_free'          => true,
                'is_active'        => true,
            ],
            [
                'title'            => 'Post-Partum: The Silent Side',
                'description'      => 'A safe space for new mothers to share their experience and find professional support through one of life\'s most vulnerable transitions.',
                'workshop_date'    => now()->addDays(13)->toDateString(),
                'workshop_time'    => '6:30 PM EST',
                'duration_minutes' => 60,
                'image_url'        => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=800&auto=format&fit=crop',
                'status'           => 'waitlist',
                'zoom_link'        => null,
                'is_free'          => true,
                'is_active'        => true,
            ],
            [
                'title'            => 'Understanding Depression',
                'description'      => 'Breaking down symptoms, cycles, and evidence-based recovery paths. Honest, jargon-free conversation about living with and healing from depression.',
                'workshop_date'    => now()->addDays(20)->toDateString(),
                'workshop_time'    => '5:00 PM EST',
                'duration_minutes' => 60,
                'image_url'        => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?q=80&w=800&auto=format&fit=crop',
                'status'           => 'upcoming',
                'zoom_link'        => null,
                'is_free'          => true,
                'is_active'        => true,
            ],
            [
                'title'            => 'Relationships After Trauma',
                'description'      => 'How trauma reshapes our ability to connect — and how we can rebuild intimacy, trust, and healthy attachment with the right tools.',
                'workshop_date'    => now()->addDays(27)->toDateString(),
                'workshop_time'    => '6:00 PM EST',
                'duration_minutes' => 90,
                'image_url'        => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=800&auto=format&fit=crop',
                'status'           => 'upcoming',
                'zoom_link'        => null,
                'is_free'          => true,
                'is_active'        => true,
            ],
            [
                'title'            => 'Mindfulness for a Busy Mind',
                'description'      => 'Practical, no-fluff mindfulness techniques for people who feel they\'re too anxious or distracted to meditate. No prior experience needed.',
                'workshop_date'    => now()->addDays(34)->toDateString(),
                'workshop_time'    => '7:30 PM EST',
                'duration_minutes' => 60,
                'image_url'        => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?q=80&w=800&auto=format&fit=crop',
                'status'           => 'upcoming',
                'zoom_link'        => null,
                'is_free'          => true,
                'is_active'        => true,
            ],
            [
                'title'            => 'Grief Without a Deadline',
                'description'      => 'Society tells us to "move on." This workshop honours grief in all its forms — loss of a person, a relationship, a homeland, a version of yourself.',
                'workshop_date'    => now()->addDays(41)->toDateString(),
                'workshop_time'    => '5:30 PM EST',
                'duration_minutes' => 75,
                'image_url'        => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?q=80&w=800&auto=format&fit=crop',
                'status'           => 'upcoming',
                'zoom_link'        => null,
                'is_free'          => true,
                'is_active'        => true,
            ],
        ];

        foreach ($workshops as $workshop) {
            Workshop::firstOrCreate(
                ['title' => $workshop['title']],
                $workshop
            );
        }
    }
}
