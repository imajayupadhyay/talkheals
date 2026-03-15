<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    const ABOUT_DEFAULTS = [
        'image_url'       => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=1000&auto=format&fit=crop',
        'badge_icon'      => '🌸',
        'badge_name'      => 'Namrata Mohan',
        'badge_role'      => 'Registered Psychotherapist',
        'label'           => 'About the practice',
        'title'           => 'A therapist who',
        'title_highlight' => 'truly',
        'title_suffix'    => 'gets it',
        'bio_p1'          => "I'm Namrata. I believe healing is possible for everyone — and this is your space to begin. My practice is grounded in cultural sensitivity, anti-racism, and a deep understanding of the unique challenges many face today.",
        'bio_p2'          => "Whether navigating immigration, trauma, or the stigma so many carry silently, I am here to provide a safe, affirming space where your voice is heard and your journey is honored with professional guidance.",
        'stat_1_value'    => '1,400+',
        'stat_1_label'    => 'Sessions',
        'stat_2_value'    => '4.97★',
        'stat_2_label'    => 'Client Rating',
        'stat_3_value'    => '8+',
        'stat_3_label'    => 'Specialties',
        'stat_4_value'    => 'Global',
        'stat_4_label'    => 'Online Access',
        'btn1_text'       => 'Book with Namrata',
        'btn1_link'       => '',
        'btn2_text'       => 'Full Profile →',
        'btn2_link'       => 'https://talkheals.ca/about',
    ];

    const ARTICLES_DEFAULTS = [
        // Section intro
        'intro_label'           => "From Namrata's Desk",
        'intro_title'           => 'Read. Learn.',
        'intro_title_highlight' => 'Feel less alone.',

        // Articles card
        'art_badge'             => '✦ Articles & Blog',
        'art_title'             => 'Words that',
        'art_title_highlight'   => 'heal',
        'art_desc'              => "Namrata writes openly about mental health — no jargon, just honesty. Topics you've wondered about, finally spoken aloud.",
        'art_p1_icon'           => '🌿',
        'art_p1_text'           => "Why Therapy Isn't Weakness — It's Courage",
        'art_p1_tag'            => 'Mental Health · 5 min read',
        'art_p2_icon'           => '🌸',
        'art_p2_text'           => 'The Invisible Weight of the Immigrant Experience',
        'art_p2_tag'            => 'Identity · 7 min read',
        'art_p3_icon'           => '💛',
        'art_p3_text'           => 'What No One Tells You About Post-Partum Anxiety',
        'art_p3_tag'            => 'Post-Partum · 6 min read',
        'art_btn'               => 'Read All Articles →',
        'art_btn_link'          => 'https://talkheals.ca/blog',

        // Newsletter card
        'nl_badge'              => '✦ Free · Weekly',
        'nl_title'              => 'Your weekly',
        'nl_title_highlight'    => 'moment of calm',
        'nl_desc'               => "Namrata's newsletter — one gentle insight, one breathing prompt, one reminder that you're doing better than you think. Every week, free.",
        'nl_perk_1'             => 'Mental health insights',
        'nl_perk_2'             => 'Workshop announcements first',
        'nl_perk_3'             => 'Early access to free calls',
        'nl_perk_4'             => 'No spam. Unsubscribe anytime.',
        'nl_placeholder'        => 'your@email.com',
        'nl_btn'                => 'Subscribe Free →',
        'nl_note'               => 'Joined by 2,400+ readers · 100% free forever',
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
        $articles = PageContent::getSection('articles', self::ARTICLES_DEFAULTS);
        $about    = PageContent::getSection('about',    self::ABOUT_DEFAULTS);

        return Inertia::render('Admin/Content/Index', [
            'hero'     => $hero,
            'sessions' => $sessions,
            'reviews'  => $reviews,
            'articles' => $articles,
            'about'    => $about,
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

    public function updateAbout(Request $request)
    {
        $request->validate([
            'image_file'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'image_url'       => 'nullable|string|max:500',
            'badge_icon'      => 'required|string|max:10',
            'badge_name'      => 'required|string|max:100',
            'badge_role'      => 'required|string|max:100',
            'label'           => 'required|string|max:100',
            'title'           => 'required|string|max:100',
            'title_highlight' => 'required|string|max:50',
            'title_suffix'    => 'required|string|max:100',
            'bio_p1'          => 'required|string|max:600',
            'bio_p2'          => 'required|string|max:600',
            'stat_1_value'    => 'required|string|max:30',
            'stat_1_label'    => 'required|string|max:50',
            'stat_2_value'    => 'required|string|max:30',
            'stat_2_label'    => 'required|string|max:50',
            'stat_3_value'    => 'required|string|max:30',
            'stat_3_label'    => 'required|string|max:50',
            'stat_4_value'    => 'required|string|max:30',
            'stat_4_label'    => 'required|string|max:50',
            'btn1_text'       => 'required|string|max:80',
            'btn1_link'       => 'nullable|url|max:300',
            'btn2_text'       => 'required|string|max:80',
            'btn2_link'       => 'required|url|max:300',
        ]);

        // Resolve image: uploaded file > URL input > keep existing
        $imageUrl = $this->resolveAboutImage($request);

        $data = $request->except(['image_file', 'image_url']);
        $data['image_url'] = $imageUrl;

        PageContent::upsertSection('about', $data);

        return back()->with('success', 'About section updated successfully.');
    }

    private function resolveAboutImage(Request $request): string
    {
        if ($request->hasFile('image_file')) {
            // Delete old uploaded file if it exists
            $existing = PageContent::where('section', 'about')->where('key', 'image_url')->value('value');
            if ($existing && str_contains($existing, '/storage/about/')) {
                $oldPath = str_replace('/storage/', '', parse_url($existing, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_file')->store('about', 'public');
            return Storage::url($path);
        }

        if ($request->filled('image_url')) {
            return $request->image_url;
        }

        // Keep existing
        return PageContent::where('section', 'about')->where('key', 'image_url')->value('value')
            ?? self::ABOUT_DEFAULTS['image_url'];
    }

    public function updateArticles(Request $request)
    {
        $validated = $request->validate([
            'intro_label'           => 'required|string|max:100',
            'intro_title'           => 'required|string|max:150',
            'intro_title_highlight' => 'required|string|max:100',
            'art_badge'             => 'required|string|max:120',
            'art_title'             => 'required|string|max:80',
            'art_title_highlight'   => 'required|string|max:50',
            'art_desc'              => 'required|string|max:400',
            'art_p1_icon'           => 'required|string|max:10',
            'art_p1_text'           => 'required|string|max:150',
            'art_p1_tag'            => 'required|string|max:80',
            'art_p2_icon'           => 'required|string|max:10',
            'art_p2_text'           => 'required|string|max:150',
            'art_p2_tag'            => 'required|string|max:80',
            'art_p3_icon'           => 'required|string|max:10',
            'art_p3_text'           => 'required|string|max:150',
            'art_p3_tag'            => 'required|string|max:80',
            'art_btn'               => 'required|string|max:80',
            'art_btn_link'          => 'required|url|max:300',
            'nl_badge'              => 'required|string|max:120',
            'nl_title'              => 'required|string|max:80',
            'nl_title_highlight'    => 'required|string|max:80',
            'nl_desc'               => 'required|string|max:400',
            'nl_perk_1'             => 'required|string|max:100',
            'nl_perk_2'             => 'required|string|max:100',
            'nl_perk_3'             => 'required|string|max:100',
            'nl_perk_4'             => 'required|string|max:100',
            'nl_placeholder'        => 'required|string|max:60',
            'nl_btn'                => 'required|string|max:80',
            'nl_note'               => 'required|string|max:150',
        ]);

        PageContent::upsertSection('articles', $validated);

        return back()->with('success', 'Articles & Newsletter section updated successfully.');
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
