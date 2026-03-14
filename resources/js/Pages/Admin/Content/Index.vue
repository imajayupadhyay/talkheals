<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Sidebar from '../Components/Sidebar.vue';

const props = defineProps({
    hero:     { type: Object, required: true },
    sessions: { type: Object, required: true },
    reviews:  { type: Object, required: true },
});

// Flash message
const flash = computed(() => usePage().props.flash ?? {});

// ── Hero Form ─────────────────────────────────────────────────────────────────
const heroForm = useForm({
    tagline:           props.hero.tagline           ?? '',
    click_note:        props.hero.click_note        ?? '',
    heading_line1:     props.hero.heading_line1     ?? '',
    heading_line2:     props.hero.heading_line2     ?? '',
    heading_highlight: props.hero.heading_highlight ?? '',
    sub:               props.hero.sub               ?? '',
});

const submitHero = () => {
    heroForm.post(route('admin.content.hero'), {
        preserveScroll: true,
    });
};

// ── Sessions Form ─────────────────────────────────────────────────────────────
const sessionsForm = useForm({
    intro_label:           props.sessions.intro_label           ?? '',
    intro_title:           props.sessions.intro_title           ?? '',
    intro_title_highlight: props.sessions.intro_title_highlight ?? '',

    free_badge:            props.sessions.free_badge            ?? '',
    free_title_top:        props.sessions.free_title_top        ?? '',
    free_title_highlight:  props.sessions.free_title_highlight  ?? '',
    free_title_bottom:     props.sessions.free_title_bottom     ?? '',
    free_desc:             props.sessions.free_desc             ?? '',
    free_perk_1:           props.sessions.free_perk_1           ?? '',
    free_perk_2:           props.sessions.free_perk_2           ?? '',
    free_perk_3:           props.sessions.free_perk_3           ?? '',
    free_perk_4:           props.sessions.free_perk_4           ?? '',
    free_price_label:      props.sessions.free_price_label      ?? '',
    free_price:            props.sessions.free_price            ?? '',
    free_price_unit:       props.sessions.free_price_unit       ?? '',
    free_btn:              props.sessions.free_btn              ?? '',

    paid_badge:            props.sessions.paid_badge            ?? '',
    paid_title_top:        props.sessions.paid_title_top        ?? '',
    paid_title_highlight:  props.sessions.paid_title_highlight  ?? '',
    paid_title_bottom:     props.sessions.paid_title_bottom     ?? '',
    paid_desc:             props.sessions.paid_desc             ?? '',
    paid_note:             props.sessions.paid_note             ?? '',

    card_1_icon:     props.sessions.card_1_icon     ?? '',
    card_1_name:     props.sessions.card_1_name     ?? '',
    card_1_detail:   props.sessions.card_1_detail   ?? '',
    card_1_price:    props.sessions.card_1_price    ?? '',
    card_1_currency: props.sessions.card_1_currency ?? '',

    card_2_icon:     props.sessions.card_2_icon     ?? '',
    card_2_name:     props.sessions.card_2_name     ?? '',
    card_2_detail:   props.sessions.card_2_detail   ?? '',
    card_2_price:    props.sessions.card_2_price    ?? '',
    card_2_currency: props.sessions.card_2_currency ?? '',

    card_3_icon:     props.sessions.card_3_icon     ?? '',
    card_3_name:     props.sessions.card_3_name     ?? '',
    card_3_detail:   props.sessions.card_3_detail   ?? '',
    card_3_price:    props.sessions.card_3_price    ?? '',
    card_3_currency: props.sessions.card_3_currency ?? '',

    card_4_icon:     props.sessions.card_4_icon     ?? '',
    card_4_name:     props.sessions.card_4_name     ?? '',
    card_4_detail:   props.sessions.card_4_detail   ?? '',
    card_4_price:    props.sessions.card_4_price    ?? '',
    card_4_currency: props.sessions.card_4_currency ?? '',
});

const submitSessions = () => {
    sessionsForm.post(route('admin.content.sessions'), { preserveScroll: true });
};

// ── Reviews Form ──────────────────────────────────────────────────────────────
const reviewsForm = useForm({
    label:           props.reviews.label           ?? '',
    title:           props.reviews.title           ?? '',
    title_highlight: props.reviews.title_highlight ?? '',
});

const submitReviews = () => {
    reviewsForm.post(route('admin.content.reviews'), { preserveScroll: true });
};

// ── Preview ───────────────────────────────────────────────────────────────────
const showPreview = ref(false);
</script>

<template>
    <div class="flex min-h-screen bg-talkheals-cream font-sans">
        <Sidebar />

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="bg-white border-b border-talkheals-gold-p px-8 py-5 flex items-center justify-between sticky top-0 z-20">
                <div>
                    <h1 class="text-2xl font-bold text-talkheals-deep tracking-tight">Page Content</h1>
                    <p class="text-sm text-talkheals-muted mt-0.5">Manage client-facing dashboard text, section by section.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="showPreview = !showPreview"
                        class="px-4 py-2 rounded-xl border border-talkheals-gold text-talkheals-gold text-sm font-semibold hover:bg-talkheals-gold/10 transition"
                    >
                        {{ showPreview ? 'Hide Preview' : 'Show Preview' }}
                    </button>
                </div>
            </header>

            <!-- Flash Message -->
            <transition name="fade">
                <div v-if="flash.success" class="mx-8 mt-6 px-5 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ flash.success }}
                </div>
            </transition>

            <!-- Main content -->
            <main class="flex-1 overflow-y-auto p-8 space-y-8">

                <!-- Section Navigation Pills -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-4 py-1.5 rounded-full bg-talkheals-gold text-white text-xs font-bold tracking-wide">Hero</span>
                    <span class="px-4 py-1.5 rounded-full bg-talkheals-gold text-white text-xs font-bold tracking-wide">Sessions</span>
                    <span class="px-4 py-1.5 rounded-full bg-talkheals-gold text-white text-xs font-bold tracking-wide">Reviews</span>
                    <span class="px-4 py-1.5 rounded-full bg-talkheals-cream border border-talkheals-gold/30 text-talkheals-muted text-xs font-medium tracking-wide cursor-not-allowed opacity-50">About (coming soon)</span>
                    <span class="px-4 py-1.5 rounded-full bg-talkheals-cream border border-talkheals-gold/30 text-talkheals-muted text-xs font-medium tracking-wide cursor-not-allowed opacity-50">Library (coming soon)</span>
                </div>

                <!-- ── Hero Section Card ─────────────────────────────────── -->
                <div class="bg-white rounded-3xl shadow-sm border border-talkheals-gold-p overflow-hidden">
                    <!-- Card Header -->
                    <div class="flex items-center gap-4 px-8 py-6 border-b border-talkheals-gold-p bg-gradient-to-r from-talkheals-cream to-white">
                        <div class="w-12 h-12 rounded-2xl bg-talkheals-rose/10 flex items-center justify-center text-2xl shrink-0">🌸</div>
                        <div>
                            <h2 class="text-lg font-bold text-talkheals-deep">Hero Section</h2>
                            <p class="text-sm text-talkheals-muted">The full-screen opening section clients see first on the dashboard.</p>
                        </div>
                        <div class="ml-auto">
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Active</span>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submitHero" class="p-8 space-y-8">

                        <!-- Live Preview (inside card) -->
                        <transition name="slide">
                            <div v-if="showPreview" class="rounded-2xl overflow-hidden border border-talkheals-gold/20 bg-[#fdf9f5]">
                                <div class="px-4 py-2 border-b border-talkheals-gold/10 bg-talkheals-gold/5 flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-red-400"></div>
                                    <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                                    <span class="text-xs text-talkheals-muted ml-2">Hero Preview</span>
                                </div>
                                <div class="py-12 px-8 flex flex-col items-center text-center">
                                    <div class="w-20 h-20 rounded-full bg-talkheals-rose/20 border-2 border-talkheals-rose/30 flex items-center justify-center text-3xl mb-5">🌸</div>
                                    <div class="text-xs tracking-[0.2em] uppercase text-talkheals-muted font-light mb-1">{{ heroForm.tagline }}</div>
                                    <div class="text-[0.65rem] text-talkheals-muted/60 tracking-wide mb-6">{{ heroForm.click_note }}</div>
                                    <h1 class="font-serif text-3xl font-extralight text-talkheals-deep leading-snug mb-3">
                                        {{ heroForm.heading_line1 }}<br>
                                        {{ heroForm.heading_line2 }} <em class="text-talkheals-rose not-italic">{{ heroForm.heading_highlight }}</em>
                                    </h1>
                                    <p class="text-sm text-talkheals-muted font-light max-w-sm leading-relaxed">{{ heroForm.sub }}</p>
                                </div>
                            </div>
                        </transition>

                        <!-- Row 1: Tagline + Click Note -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">
                                    Tagline
                                    <span class="ml-1 text-talkheals-muted normal-case tracking-normal font-normal">— above the heading, small caps</span>
                                </label>
                                <input
                                    v-model="heroForm.tagline"
                                    type="text"
                                    maxlength="120"
                                    placeholder="e.g. Breathe & Scroll"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition"
                                >
                                <div class="flex justify-between mt-1">
                                    <p v-if="heroForm.errors.tagline" class="text-red-500 text-xs">{{ heroForm.errors.tagline }}</p>
                                    <span class="text-xs text-talkheals-muted ml-auto">{{ heroForm.tagline.length }}/120</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">
                                    Click Note
                                    <span class="ml-1 text-talkheals-muted normal-case tracking-normal font-normal">— tiny hint below tagline</span>
                                </label>
                                <input
                                    v-model="heroForm.click_note"
                                    type="text"
                                    maxlength="200"
                                    placeholder="e.g. Tap the bloom for ambient sound"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition"
                                >
                                <div class="flex justify-between mt-1">
                                    <p v-if="heroForm.errors.click_note" class="text-red-500 text-xs">{{ heroForm.errors.click_note }}</p>
                                    <span class="text-xs text-talkheals-muted ml-auto">{{ heroForm.click_note.length }}/200</span>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                            <span class="text-xs text-talkheals-muted font-semibold uppercase tracking-widest">Main Heading</span>
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                        </div>

                        <!-- Heading explanation -->
                        <div class="bg-talkheals-gold/5 border border-talkheals-gold/20 rounded-2xl px-5 py-4 text-sm text-talkheals-muted">
                            The heading is displayed as two lines with a highlighted word at the end:
                            <span class="font-semibold text-talkheals-deep">Line 1</span> /
                            <span class="font-semibold text-talkheals-deep">Line 2</span> +
                            <span class="font-semibold text-talkheals-rose">Highlight</span>
                            <br>
                            <span class="text-xs mt-1 block">Example: "<em>Your journey</em>" / "<em>starts</em>" + "<em class='text-talkheals-rose'>now</em>"</span>
                        </div>

                        <!-- Row 2: Heading parts -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">
                                    Heading Line 1
                                </label>
                                <input
                                    v-model="heroForm.heading_line1"
                                    type="text"
                                    maxlength="100"
                                    placeholder="e.g. Your journey"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition"
                                >
                                <p v-if="heroForm.errors.heading_line1" class="text-red-500 text-xs mt-1">{{ heroForm.errors.heading_line1 }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">
                                    Heading Line 2
                                </label>
                                <input
                                    v-model="heroForm.heading_line2"
                                    type="text"
                                    maxlength="100"
                                    placeholder="e.g. starts"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition"
                                >
                                <p v-if="heroForm.errors.heading_line2" class="text-red-500 text-xs mt-1">{{ heroForm.errors.heading_line2 }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">
                                    Highlight Word
                                    <span class="ml-1 text-talkheals-rose normal-case tracking-normal font-normal">— rose colour</span>
                                </label>
                                <input
                                    v-model="heroForm.heading_highlight"
                                    type="text"
                                    maxlength="50"
                                    placeholder="e.g. now"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-rose/30 bg-talkheals-cream/50 text-talkheals-rose text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-talkheals-rose/30 transition"
                                >
                                <p v-if="heroForm.errors.heading_highlight" class="text-red-500 text-xs mt-1">{{ heroForm.errors.heading_highlight }}</p>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                            <span class="text-xs text-talkheals-muted font-semibold uppercase tracking-widest">Sub Text</span>
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                        </div>

                        <!-- Row 3: Sub text -->
                        <div>
                            <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">
                                Sub-heading Paragraph
                                <span class="ml-1 text-talkheals-muted normal-case tracking-normal font-normal">— shown below the main heading</span>
                            </label>
                            <textarea
                                v-model="heroForm.sub"
                                rows="3"
                                maxlength="400"
                                placeholder="e.g. Namrata Mohan, RP — a safe, warm space for your healing, wherever you are in the world."
                                class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition resize-none"
                            ></textarea>
                            <div class="flex justify-between mt-1">
                                <p v-if="heroForm.errors.sub" class="text-red-500 text-xs">{{ heroForm.errors.sub }}</p>
                                <span class="text-xs text-talkheals-muted ml-auto">{{ heroForm.sub.length }}/400</span>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-between pt-2 border-t border-talkheals-gold/10">
                            <p class="text-xs text-talkheals-muted">Changes go live immediately after saving.</p>
                            <button
                                type="submit"
                                :disabled="heroForm.processing"
                                class="px-8 py-3 rounded-2xl bg-talkheals-gold text-white font-bold text-sm shadow-lg shadow-talkheals-gold/20 hover:bg-talkheals-gold/90 disabled:opacity-60 transition-all duration-200 flex items-center gap-2"
                            >
                                <svg v-if="heroForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                {{ heroForm.processing ? 'Saving...' : 'Save Hero Section' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ── Reviews Section Card ───────────────────────────────── -->
                <div class="bg-white rounded-3xl shadow-sm border border-talkheals-gold-p overflow-hidden">
                    <div class="flex items-center gap-4 px-8 py-6 border-b border-talkheals-gold-p bg-gradient-to-r from-talkheals-cream to-white">
                        <div class="w-12 h-12 rounded-2xl bg-talkheals-deep/5 flex items-center justify-center text-2xl shrink-0">⭐</div>
                        <div>
                            <h2 class="text-lg font-bold text-talkheals-deep">Reviews Section</h2>
                            <p class="text-sm text-talkheals-muted">The heading above the testimonials carousel.</p>
                        </div>
                        <div class="ml-auto">
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Active</span>
                        </div>
                    </div>

                    <form @submit.prevent="submitReviews" class="p-8 space-y-6">

                        <!-- Info hint -->
                        <div class="bg-talkheals-gold/5 border border-talkheals-gold/20 rounded-2xl px-5 py-4 text-sm text-talkheals-muted">
                            The heading renders as:
                            <span class="font-semibold text-talkheals-deep">Label</span> (small gold caps) and
                            <span class="font-semibold text-talkheals-deep">Title</span> +
                            <span class="font-semibold text-talkheals-rose">Highlight</span> (italic rose).
                            <br>
                            <span class="text-xs mt-1 block">Example: "<em>Healing Stories</em>" / "<em>What clients say about</em> <em class='text-talkheals-rose'>Namrata</em>"</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Label -->
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">
                                    Label
                                    <span class="font-normal normal-case tracking-normal text-talkheals-muted ml-1">— gold small caps</span>
                                </label>
                                <input v-model="reviewsForm.label" type="text" maxlength="100"
                                    placeholder="e.g. Healing Stories"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                <p v-if="reviewsForm.errors.label" class="text-red-500 text-xs mt-1">{{ reviewsForm.errors.label }}</p>
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">
                                    Title
                                </label>
                                <input v-model="reviewsForm.title" type="text" maxlength="200"
                                    placeholder="e.g. What clients say about"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                <p v-if="reviewsForm.errors.title" class="text-red-500 text-xs mt-1">{{ reviewsForm.errors.title }}</p>
                            </div>

                            <!-- Highlight -->
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">
                                    Highlight
                                    <span class="ml-1 text-talkheals-rose font-normal normal-case tracking-normal">— italic rose</span>
                                </label>
                                <input v-model="reviewsForm.title_highlight" type="text" maxlength="80"
                                    placeholder="e.g. Namrata"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-rose/30 bg-talkheals-cream/50 text-talkheals-rose text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-talkheals-rose/30 transition">
                                <p v-if="reviewsForm.errors.title_highlight" class="text-red-500 text-xs mt-1">{{ reviewsForm.errors.title_highlight }}</p>
                            </div>
                        </div>

                        <!-- Live preview -->
                        <div class="bg-[#1e1a17] rounded-2xl px-8 py-6 flex items-center justify-between">
                            <div>
                                <div class="text-[0.65rem] tracking-[0.18em] uppercase text-[#c9a96e] font-semibold mb-1">{{ reviewsForm.label || 'Healing Stories' }}</div>
                                <h2 class="font-serif text-xl font-light text-white leading-snug">
                                    {{ reviewsForm.title || 'What clients say about' }}
                                    <em class="text-[#c49a8a] not-italic">{{ reviewsForm.title_highlight || 'Namrata' }}</em>
                                </h2>
                            </div>
                            <div class="flex gap-2">
                                <div class="w-8 h-8 rounded-full border border-[#c9a96e]/30 flex items-center justify-center text-[#c9a96e]/50 text-sm">‹</div>
                                <div class="w-8 h-8 rounded-full border border-[#c9a96e]/30 flex items-center justify-center text-[#c9a96e]/50 text-sm">›</div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-between pt-2 border-t border-talkheals-gold/10">
                            <p class="text-xs text-talkheals-muted">Changes go live immediately after saving.</p>
                            <button type="submit" :disabled="reviewsForm.processing"
                                class="px-8 py-3 rounded-2xl bg-talkheals-gold text-white font-bold text-sm shadow-lg shadow-talkheals-gold/20 hover:bg-talkheals-gold/90 disabled:opacity-60 transition-all duration-200 flex items-center gap-2">
                                <svg v-if="reviewsForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                {{ reviewsForm.processing ? 'Saving...' : 'Save Reviews Header' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ── Sessions Section Card ──────────────────────────────── -->
                <div class="bg-white rounded-3xl shadow-sm border border-talkheals-gold-p overflow-hidden">
                    <!-- Card Header -->
                    <div class="flex items-center gap-4 px-8 py-6 border-b border-talkheals-gold-p bg-gradient-to-r from-talkheals-cream to-white">
                        <div class="w-12 h-12 rounded-2xl bg-talkheals-gold/10 flex items-center justify-center text-2xl shrink-0">💆</div>
                        <div>
                            <h2 class="text-lg font-bold text-talkheals-deep">Sessions Section</h2>
                            <p class="text-sm text-talkheals-muted">The two booking cards — Free Discovery Call (dark) and Full Therapy Session (light).</p>
                        </div>
                        <div class="ml-auto">
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Active</span>
                        </div>
                    </div>

                    <form @submit.prevent="submitSessions" class="p-8 space-y-8">

                        <!-- ── Intro ── -->
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                            <span class="text-xs text-talkheals-muted font-semibold uppercase tracking-widest">Section Intro</span>
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Label <span class="font-normal normal-case tracking-normal text-talkheals-muted">— small caps above title</span></label>
                                <input v-model="sessionsForm.intro_label" type="text" maxlength="100"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                <p v-if="sessionsForm.errors.intro_label" class="text-red-500 text-xs mt-1">{{ sessionsForm.errors.intro_label }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Title</label>
                                <div class="flex gap-2">
                                    <input v-model="sessionsForm.intro_title" type="text" maxlength="150" placeholder="Begin free. Go deeper."
                                        class="flex-1 px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                    <input v-model="sessionsForm.intro_title_highlight" type="text" maxlength="80" placeholder="Heal."
                                        class="w-28 px-4 py-3 rounded-xl border border-talkheals-rose/30 bg-talkheals-cream/50 text-talkheals-rose text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-talkheals-rose/30 transition">
                                </div>
                                <p class="text-xs text-talkheals-muted mt-1">Main text + <span class="text-talkheals-rose">italic rose highlight</span></p>
                            </div>
                        </div>

                        <!-- ── Free Box ── -->
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                            <span class="text-xs text-talkheals-muted font-semibold uppercase tracking-widest px-3 py-1 bg-[#2a2420] text-[#c9a96e] rounded-full">Free Box (dark card)</span>
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Badge Text</label>
                                <input v-model="sessionsForm.free_badge" type="text" maxlength="120"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                <p v-if="sessionsForm.errors.free_badge" class="text-red-500 text-xs mt-1">{{ sessionsForm.errors.free_badge }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Title <span class="font-normal normal-case tracking-normal text-talkheals-muted">— 3 lines</span></label>
                                <div class="flex gap-2">
                                    <input v-model="sessionsForm.free_title_top" type="text" maxlength="80" placeholder="Free"
                                        class="flex-1 px-3 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                    <input v-model="sessionsForm.free_title_highlight" type="text" maxlength="80" placeholder="30-Min"
                                        class="flex-1 px-3 py-3 rounded-xl border border-talkheals-gold/40 bg-talkheals-gold/10 text-talkheals-gold text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-talkheals-gold/30 transition">
                                    <input v-model="sessionsForm.free_title_bottom" type="text" maxlength="80" placeholder="Discovery Call"
                                        class="flex-1 px-3 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                </div>
                                <p class="text-xs text-talkheals-muted mt-1">Line 1 / <span class="text-talkheals-gold font-medium">Italic gold highlight</span> / Line 3</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Description</label>
                            <textarea v-model="sessionsForm.free_desc" rows="2" maxlength="300"
                                class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition resize-none"></textarea>
                            <p v-if="sessionsForm.errors.free_desc" class="text-red-500 text-xs mt-1">{{ sessionsForm.errors.free_desc }}</p>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="n in 4" :key="n">
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Perk {{ n }}</label>
                                <input v-model="sessionsForm[`free_perk_${n}`]" type="text" maxlength="100"
                                    class="w-full px-3 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Price Label</label>
                                <input v-model="sessionsForm.free_price_label" type="text" maxlength="60"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Price</label>
                                <input v-model="sessionsForm.free_price" type="text" maxlength="20"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Price Unit</label>
                                <input v-model="sessionsForm.free_price_unit" type="text" maxlength="30"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Button Text</label>
                            <input v-model="sessionsForm.free_btn" type="text" maxlength="80"
                                class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                        </div>

                        <!-- ── Paid Box ── -->
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                            <span class="text-xs text-talkheals-muted font-semibold uppercase tracking-widest px-3 py-1 bg-white border border-talkheals-rose/30 text-talkheals-rose rounded-full">Paid Box (light card)</span>
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Badge Text</label>
                                <input v-model="sessionsForm.paid_badge" type="text" maxlength="120"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                <p v-if="sessionsForm.errors.paid_badge" class="text-red-500 text-xs mt-1">{{ sessionsForm.errors.paid_badge }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Title <span class="font-normal normal-case tracking-normal text-talkheals-muted">— 3 lines</span></label>
                                <div class="flex gap-2">
                                    <input v-model="sessionsForm.paid_title_top" type="text" maxlength="80" placeholder="Book a Full"
                                        class="flex-1 px-3 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                    <input v-model="sessionsForm.paid_title_highlight" type="text" maxlength="80" placeholder="Therapy"
                                        class="flex-1 px-3 py-3 rounded-xl border border-talkheals-rose/30 bg-talkheals-cream/50 text-talkheals-rose text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-talkheals-rose/30 transition">
                                    <input v-model="sessionsForm.paid_title_bottom" type="text" maxlength="80" placeholder="Session"
                                        class="flex-1 px-3 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                </div>
                                <p class="text-xs text-talkheals-muted mt-1">Line 1 / <span class="text-talkheals-rose font-medium">Italic rose highlight</span> / Line 3</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Description</label>
                                <textarea v-model="sessionsForm.paid_desc" rows="2" maxlength="300"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition resize-none"></textarea>
                                <p v-if="sessionsForm.errors.paid_desc" class="text-red-500 text-xs mt-1">{{ sessionsForm.errors.paid_desc }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Footer Note</label>
                                <input v-model="sessionsForm.paid_note" type="text" maxlength="200"
                                    class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                <p v-if="sessionsForm.errors.paid_note" class="text-red-500 text-xs mt-1">{{ sessionsForm.errors.paid_note }}</p>
                            </div>
                        </div>

                        <!-- Session Cards -->
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                            <span class="text-xs text-talkheals-muted font-semibold uppercase tracking-widest">Session Type Cards</span>
                            <div class="flex-1 h-px bg-talkheals-gold/10"></div>
                        </div>

                        <div class="space-y-4">
                            <div v-for="n in 4" :key="n" class="bg-talkheals-cream/50 border border-talkheals-gold/20 rounded-2xl p-5">
                                <div class="text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-4">Card {{ n }}</div>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                    <div>
                                        <label class="block text-xs text-talkheals-muted mb-1.5">Icon (emoji)</label>
                                        <input v-model="sessionsForm[`card_${n}_icon`]" type="text" maxlength="10"
                                            class="w-full px-3 py-2.5 rounded-xl border border-talkheals-gold/30 bg-white text-talkheals-deep text-lg text-center focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs text-talkheals-muted mb-1.5">Session Name</label>
                                        <input v-model="sessionsForm[`card_${n}_name`]" type="text" maxlength="80"
                                            class="w-full px-3 py-2.5 rounded-xl border border-talkheals-gold/30 bg-white text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-talkheals-muted mb-1.5">Price</label>
                                        <input v-model="sessionsForm[`card_${n}_price`]" type="text" maxlength="20"
                                            class="w-full px-3 py-2.5 rounded-xl border border-talkheals-gold/30 bg-white text-talkheals-deep text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-talkheals-muted mb-1.5">Currency</label>
                                        <input v-model="sessionsForm[`card_${n}_currency`]" type="text" maxlength="10"
                                            class="w-full px-3 py-2.5 rounded-xl border border-talkheals-gold/30 bg-white text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                    </div>
                                    <div class="col-span-2 md:col-span-5">
                                        <label class="block text-xs text-talkheals-muted mb-1.5">Detail line</label>
                                        <input v-model="sessionsForm[`card_${n}_detail`]" type="text" maxlength="150"
                                            class="w-full px-3 py-2.5 rounded-xl border border-talkheals-gold/30 bg-white text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-between pt-2 border-t border-talkheals-gold/10">
                            <p class="text-xs text-talkheals-muted">Changes go live immediately after saving.</p>
                            <button
                                type="submit"
                                :disabled="sessionsForm.processing"
                                class="px-8 py-3 rounded-2xl bg-talkheals-gold text-white font-bold text-sm shadow-lg shadow-talkheals-gold/20 hover:bg-talkheals-gold/90 disabled:opacity-60 transition-all duration-200 flex items-center gap-2"
                            >
                                <svg v-if="sessionsForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                {{ sessionsForm.processing ? 'Saving...' : 'Save Sessions Section' }}
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-enter-active, .slide-leave-active { transition: all .3s ease; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
