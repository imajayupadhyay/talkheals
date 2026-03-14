<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class WorkshopController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Workshop::orderByDesc('workshop_date');

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $workshops = $query->paginate(12)->withQueryString();

        $stats = [
            'total'    => Workshop::count(),
            'upcoming' => Workshop::active()->upcoming()->count(),
            'waitlist' => Workshop::where('status', 'waitlist')->count(),
            'inactive' => Workshop::where('is_active', false)->count(),
        ];

        return Inertia::render('Admin/Workshops/Index', [
            'workshops' => $workshops,
            'stats'     => $stats,
            'filters'   => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'workshop_date'    => 'required|date',
            'workshop_time'    => 'required|string|max:100',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'image_file'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'image_url'        => 'nullable|url|max:500',
            'status'           => 'required|in:upcoming,waitlist,cancelled',
            'zoom_link'        => 'nullable|url|max:500',
            'is_free'          => 'boolean',
            'is_active'        => 'boolean',
        ]);

        $validated['image_url'] = $this->resolveImageUrl($request, null);

        unset($validated['image_file']);

        Workshop::create($validated);

        return back()->with('success', 'Workshop created successfully.');
    }

    public function update(Request $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'workshop_date'    => 'required|date',
            'workshop_time'    => 'required|string|max:100',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'image_file'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'image_url'        => 'nullable|url|max:500',
            'status'           => 'required|in:upcoming,waitlist,cancelled',
            'zoom_link'        => 'nullable|url|max:500',
            'is_free'          => 'boolean',
            'is_active'        => 'boolean',
        ]);

        $validated['image_url'] = $this->resolveImageUrl($request, $workshop);

        unset($validated['image_file']);

        $workshop->update($validated);

        return back()->with('success', 'Workshop updated successfully.');
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        // Delete uploaded image if it lives in our storage
        if ($workshop->image_url && str_contains($workshop->image_url, '/storage/workshops/')) {
            $path = str_replace('/storage/', 'public/', parse_url($workshop->image_url, PHP_URL_PATH));
            Storage::delete($path);
        }

        $workshop->delete();

        return back()->with('success', 'Workshop deleted.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * If an image file was uploaded, store it and return its public URL.
     * If only a URL was provided, use that.
     * If neither, keep the existing image (on update) or null (on create).
     */
    private function resolveImageUrl(Request $request, ?Workshop $existing): ?string
    {
        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            // Delete old uploaded image if it was one of ours
            if ($existing?->image_url && str_contains($existing->image_url, '/storage/workshops/')) {
                $old = str_replace('/storage/', 'public/', parse_url($existing->image_url, PHP_URL_PATH));
                Storage::delete($old);
            }

            $path = $request->file('image_file')->store('workshops', 'public');

            return Storage::url($path);
        }

        // Use URL if provided
        if ($request->filled('image_url')) {
            return $request->input('image_url');
        }

        // Fall back to existing image on update, null on create
        return $existing?->image_url;
    }
}
