<?php

namespace App\Http\Controllers;

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

        return Inertia::render('Dashboard/Index', [
            'workshops' => $workshops,
        ]);
    }
}
