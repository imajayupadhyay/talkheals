<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order')->orderBy('id')->get();

        return Inertia::render('Admin/Testimonials/Index', [
            'testimonials' => $testimonials,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stars'           => 'required|integer|min:1|max:5',
            'quote'           => 'required|string|max:600',
            'name'            => 'required|string|max:100',
            'location'        => 'nullable|string|max:100',
            'tag'             => 'nullable|string|max:80',
            'avatar_gradient' => 'nullable|string|max:200',
            'sort_order'      => 'nullable|integer|min:0',
        ]);

        Testimonial::create($validated);

        return back()->with('success', 'Testimonial added successfully.');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'stars'           => 'required|integer|min:1|max:5',
            'quote'           => 'required|string|max:600',
            'name'            => 'required|string|max:100',
            'location'        => 'nullable|string|max:100',
            'tag'             => 'nullable|string|max:80',
            'avatar_gradient' => 'nullable|string|max:200',
            'sort_order'      => 'nullable|integer|min:0',
        ]);

        $testimonial->update($validated);

        return back()->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted.');
    }

    public function toggleActive(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => ! $testimonial->is_active]);

        return back()->with('success', $testimonial->is_active ? 'Testimonial activated.' : 'Testimonial hidden.');
    }
}
