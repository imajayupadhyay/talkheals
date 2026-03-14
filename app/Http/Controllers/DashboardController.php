<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\PageContentController;
use App\Models\PageContent;
use App\Models\Testimonial;
use App\Models\Workshop;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $workshops = Workshop::active()
            ->upcoming()
            ->limit(6)
            ->get([
                'id', 'title', 'description', 'workshop_date',
                'workshop_time', 'duration_minutes', 'image_url',
                'status', 'zoom_link', 'is_free',
            ]);

        $hero     = PageContent::getSection('hero',     PageContentController::HERO_DEFAULTS);
        $sessions = PageContent::getSection('sessions', PageContentController::SESSIONS_DEFAULTS);
        $reviews  = PageContent::getSection('reviews',  PageContentController::REVIEWS_DEFAULTS);

        $testimonials = Testimonial::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'stars', 'quote', 'name', 'location', 'tag', 'avatar_gradient']);

        return Inertia::render('Dashboard/Index', [
            'workshops'    => $workshops,
            'hero'         => $hero,
            'sessions'     => $sessions,
            'testimonials'  => $testimonials,
            'reviews'       => $reviews,
        ]);
    }
}
