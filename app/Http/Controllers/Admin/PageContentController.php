<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageContentController extends Controller
{
    // ── Defaults ──────────────────────────────────────────────────────────────

    const HERO_DEFAULTS = [
        'tagline'           => 'Breathe & Scroll',
        'click_note'        => 'Tap the bloom for ambient sound',
        'heading_line1'     => 'Your journey',
        'heading_line2'     => 'starts',
        'heading_highlight' => 'now',
        'sub'               => 'Namrata Mohan, RP — a safe, warm space for your healing, wherever you are in the world.',
    ];

    const REVIEWS_DEFAULTS = [
        'label'           => 'Healing Stories',
        'title'           => 'What clients say about',
        'title_highlight' => 'Namrata',
    ];

    const SESSIONS_DEFAULTS = [
        // Intro
        'intro_label'           => 'Choose Your Path',
        'intro_title'           => 'Begin free. Go deeper.',
        'intro_title_highlight' => 'Heal.',

        // Free box
        'free_badge'            => 'Zero Cost · No Card Needed',
        'free_title_top'        => 'Free',
        'free_title_highlight'  => '30-Min',
        'free_title_bottom'     => 'Discovery Call',
        'free_desc'             => 'Not sure? Just talk. 30 minutes with Namrata — completely free, no pressure, no commitments.',
        'free_perk_1'           => 'No credit card',
        'free_perk_2'           => 'Video, phone or chat',
        'free_perk_3'           => 'Any time zone, globally',
        'free_perk_4'           => '100% confidential',
        'free_price_label'      => 'Session Fee',
        'free_price'            => '$0',
        'free_price_unit'       => '/ 30 min',
        'free_btn'              => 'Claim My Free Call →',

        // Paid box
        'paid_badge'            => '✦ Individual · Couples · Family · Coaching',
        'paid_title_top'        => 'Book a Full',
        'paid_title_highlight'  => 'Therapy',
        'paid_title_bottom'     => 'Session',
        'paid_desc'             => 'Evidence-based psychotherapy. Choose your session, schedule your time, begin your healing.',
        'paid_note'             => '🔒 Secure · 📋 Insurance Receipts · 🕊️ Confidential',

        // Session cards
        'card_1_icon'           => '💬',
        'card_1_name'           => 'Individual Therapy',
        'card_1_detail'         => 'Anxiety · Depression · Trauma · Grief · 50 min',
        'card_1_price'          => '$180',
        'card_1_currency'       => 'CAD',

        'card_2_icon'           => '💑',
        'card_2_name'           => 'Couples Therapy',
        'card_2_detail'         => 'Marriage · Communication · Conflict · 80 min',
        'card_2_price'          => '$220',
        'card_2_currency'       => 'CAD',

        'card_3_icon'           => '👨‍👩‍👧',
        'card_3_name'           => 'Family Therapy',
        'card_3_detail'         => 'Parenting · Dynamics · Conflict · 90 min',
        'card_3_price'          => '$250',
        'card_3_currency'       => 'CAD',

        'card_4_icon'           => '🌱',
        'card_4_name'           => 'Coaching',
        'card_4_detail'         => 'Pre-Marital · Parenting · Life · 60 min',
        'card_4_price'          => '$160',
        'card_4_currency'       => 'CAD',
    ];

    // ── Controller Actions ────────────────────────────────────────────────────

    public function index()
    {
        $hero     = PageContent::getSection('hero',     self::HERO_DEFAULTS);
        $sessions = PageContent::getSection('sessions', self::SESSIONS_DEFAULTS);
        $reviews  = PageContent::getSection('reviews',  self::REVIEWS_DEFAULTS);

        return Inertia::render('Admin/Content/Index', [
            'hero'     => $hero,
            'sessions' => $sessions,
            'reviews'  => $reviews,
        ]);
    }

    public function updateHero(Request $request)
    {
        $validated = $request->validate([
            'tagline'           => 'required|string|max:120',
            'click_note'        => 'required|string|max:200',
            'heading_line1'     => 'required|string|max:100',
            'heading_line2'     => 'required|string|max:100',
            'heading_highlight' => 'required|string|max:50',
            'sub'               => 'required|string|max:400',
        ]);

        PageContent::upsertSection('hero', $validated);

        return back()->with('success', 'Hero section updated successfully.');
    }

    public function updateReviews(Request $request)
    {
        $validated = $request->validate([
            'label'           => 'required|string|max:100',
            'title'           => 'required|string|max:200',
            'title_highlight' => 'required|string|max:80',
        ]);

        PageContent::upsertSection('reviews', $validated);

        return back()->with('success', 'Reviews section header updated successfully.');
    }

    public function updateSessions(Request $request)
    {
        $validated = $request->validate([
            'intro_label'           => 'required|string|max:100',
            'intro_title'           => 'required|string|max:150',
            'intro_title_highlight' => 'required|string|max:80',

            'free_badge'            => 'required|string|max:120',
            'free_title_top'        => 'required|string|max:80',
            'free_title_highlight'  => 'required|string|max:80',
            'free_title_bottom'     => 'required|string|max:80',
            'free_desc'             => 'required|string|max:300',
            'free_perk_1'           => 'required|string|max:100',
            'free_perk_2'           => 'required|string|max:100',
            'free_perk_3'           => 'required|string|max:100',
            'free_perk_4'           => 'required|string|max:100',
            'free_price_label'      => 'required|string|max:60',
            'free_price'            => 'required|string|max:20',
            'free_price_unit'       => 'required|string|max:30',
            'free_btn'              => 'required|string|max:80',

            'paid_badge'            => 'required|string|max:120',
            'paid_title_top'        => 'required|string|max:80',
            'paid_title_highlight'  => 'required|string|max:80',
            'paid_title_bottom'     => 'required|string|max:80',
            'paid_desc'             => 'required|string|max:300',
            'paid_note'             => 'required|string|max:200',

            'card_1_icon'           => 'required|string|max:10',
            'card_1_name'           => 'required|string|max:80',
            'card_1_detail'         => 'required|string|max:150',
            'card_1_price'          => 'required|string|max:20',
            'card_1_currency'       => 'required|string|max:10',

            'card_2_icon'           => 'required|string|max:10',
            'card_2_name'           => 'required|string|max:80',
            'card_2_detail'         => 'required|string|max:150',
            'card_2_price'          => 'required|string|max:20',
            'card_2_currency'       => 'required|string|max:10',

            'card_3_icon'           => 'required|string|max:10',
            'card_3_name'           => 'required|string|max:80',
            'card_3_detail'         => 'required|string|max:150',
            'card_3_price'          => 'required|string|max:20',
            'card_3_currency'       => 'required|string|max:10',

            'card_4_icon'           => 'required|string|max:10',
            'card_4_name'           => 'required|string|max:80',
            'card_4_detail'         => 'required|string|max:150',
            'card_4_price'          => 'required|string|max:20',
            'card_4_currency'       => 'required|string|max:10',
        ]);

        PageContent::upsertSection('sessions', $validated);

        return back()->with('success', 'Sessions section updated successfully.');
    }
}
