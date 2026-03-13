<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    auth: Object
});
</script>

<template>
    <Head title="TalkHeals - Client Portal" />

    <div class="relative min-h-screen bg-talkheals-cream flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-talkheals-gold-p py-4 px-8 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="font-serif text-2xl font-light text-talkheals-deep">
                    Talk<span class="text-talkheals-gold italic">Heals</span>
                </div>
                
                <div class="flex items-center gap-6">
                    <span class="text-sm text-talkheals-muted">Welcome, {{ auth.user.name }}</span>
                    <Link 
                        :href="route('logout')" 
                        method="post" 
                        as="button"
                        class="text-xs uppercase tracking-widest text-talkheals-rose hover:text-talkheals-deep font-medium transition"
                    >
                        Sign Out
                    </Link>
                </div>
            </div>
        </nav>

        <main class="flex-1 max-w-7xl w-full mx-auto p-8 lg:p-12">
            <div class="font-serif text-4xl font-extralight text-talkheals-deep mb-8">
                Your <em class="text-talkheals-gold italic">Healing Space</em>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 border border-talkheals-gold-p shadow-sm">
                    <div class="text-3xl mb-4">📅</div>
                    <h3 class="font-serif text-xl mb-2">My Appointments</h3>
                    <p class="text-sm text-talkheals-muted font-light mb-6">View your scheduled sessions and upcoming requests.</p>
                    <button class="w-full py-3 bg-talkheals-cream text-talkheals-deep text-sm font-medium rounded-xl hover:bg-talkheals-gold-p transition">View All</button>
                </div>

                <div class="bg-white rounded-2xl p-8 border border-talkheals-gold-p shadow-sm">
                    <div class="text-3xl mb-4">📒</div>
                    <h3 class="font-serif text-xl mb-2">Session Notes</h3>
                    <p class="text-sm text-talkheals-muted font-light mb-6">Review your private notes and healing reflections.</p>
                    <button class="w-full py-3 bg-talkheals-cream text-talkheals-deep text-sm font-medium rounded-xl hover:bg-talkheals-gold-p transition">Open Notes</button>
                </div>

                <div class="bg-white rounded-2xl p-8 border border-talkheals-gold-p shadow-sm">
                    <div class="text-3xl mb-4">🎓</div>
                    <h3 class="font-serif text-xl mb-2">My Workshops</h3>
                    <p class="text-sm text-talkheals-muted font-light mb-6">Access recordings and materials from your workshops.</p>
                    <button class="w-full py-3 bg-talkheals-cream text-talkheals-deep text-sm font-medium rounded-xl hover:bg-talkheals-gold-p transition">Enter Library</button>
                </div>
            </div>
        </main>

        <footer class="py-12 border-t border-talkheals-gold-p/30 px-8">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-xs text-talkheals-muted tracking-widest uppercase">
                <div>© 2026 TalkHeals Psychotherapy</div>
                <div class="mt-4 md:mt-0">Compassionate Care · Globally Accessible</div>
            </div>
        </footer>
    </div>
</template>
