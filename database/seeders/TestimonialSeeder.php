<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'stars'           => 5,
                'quote'           => 'Namrata has this rare gift of making you feel seen without judgment. After three sessions I understood my anxiety in a way years of suppression never allowed.',
                'name'            => 'Priya R.',
                'location'        => 'Toronto, ON',
                'tag'             => 'Anxiety',
                'avatar_gradient' => 'linear-gradient(135deg,#c49a8a,#c9a96e)',
                'sort_order'      => 1,
            ],
            [
                'stars'           => 5,
                'quote'           => 'As a new immigrant I felt completely lost. Namrata understood without me having to explain everything. She helped me rebuild my identity with such grace.',
                'name'            => 'Supriya K.',
                'location'        => 'Vancouver, BC',
                'tag'             => 'Immigration & Identity',
                'avatar_gradient' => 'linear-gradient(135deg,#7a9e8e,#8bb0c4)',
                'sort_order'      => 2,
            ],
            [
                'stars'           => 5,
                'quote'           => 'Post-partum depression nearly broke me. Namrata created a space where I could finally say that out loud. Her compassion guided me back to my baby.',
                'name'            => 'Meera T.',
                'location'        => 'Brampton, ON',
                'tag'             => 'Post-Partum',
                'avatar_gradient' => 'linear-gradient(135deg,#b8a0b8,#c49a8a)',
                'sort_order'      => 3,
            ],
            [
                'stars'           => 5,
                'quote'           => 'My husband and I were at a breaking point. Namrata\'s couples therapy gave us a language for each other again — a truly rare skill.',
                'name'            => 'Anjali & Raj V.',
                'location'        => 'Mississauga, ON',
                'tag'             => 'Couples Therapy',
                'avatar_gradient' => 'linear-gradient(135deg,#c9a96e,#7a9e8e)',
                'sort_order'      => 4,
            ],
            [
                'stars'           => 5,
                'quote'           => 'Within two sessions I was having breakthroughs I hadn\'t thought possible. The free first call convinced me — best decision I ever made.',
                'name'            => 'David O.',
                'location'        => 'Ottawa, ON',
                'tag'             => 'Depression',
                'avatar_gradient' => 'linear-gradient(135deg,#8bb0c4,#b8a0b8)',
                'sort_order'      => 5,
            ],
            [
                'stars'           => 5,
                'quote'           => 'Going through divorce felt like the end. Namrata helped me see it as a beginning. Her empathy transformed the most painful chapter of my life.',
                'name'            => 'Rekha N.',
                'location'        => 'Calgary, AB',
                'tag'             => 'Divorce Recovery',
                'avatar_gradient' => 'linear-gradient(135deg,#e8cfc6,#c49a8a)',
                'sort_order'      => 6,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::firstOrCreate(
                ['name' => $data['name'], 'tag' => $data['tag']],
                $data
            );
        }

        $this->command->info('Testimonials seeded successfully.');
    }
}
