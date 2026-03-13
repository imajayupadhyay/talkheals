<script setup>
import { Head } from '@inertiajs/vue3';
import Sidebar from './Components/Sidebar.vue';
import Topbar from './Components/Topbar.vue';

const props = defineProps({
    auth: Object
});
</script>

<template>
    <Head title="Admin Dashboard - TalkHeals" />

    <div class="min-h-screen bg-talkheals-cream flex text-talkheals-deep font-sans">
        <!-- Sidebar -->
        <Sidebar />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <Topbar />

            <!-- Dashboard Content -->
            <main class="flex-1 p-10 overflow-y-auto">
                <div class="max-w-7xl mx-auto">
                    <!-- Welcome Header -->
                    <div class="mb-12">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-talkheals-gold text-2xl">👋</span>
                            <h1 class="font-serif text-4xl font-light tracking-tight text-talkheals-deep">
                                Welcome back, <em class="text-talkheals-gold italic not-italic">{{ auth.user.name }}</em>
                            </h1>
                        </div>
                        <p class="text-talkheals-mid text-lg font-light pl-10">Administrative overview for TalkHeals Psychotherapy.</p>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                        <div class="stat-card">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-talkheals-gold/10 flex items-center justify-center text-2xl border border-talkheals-gold/20">👥</div>
                                <span class="text-talkheals-sage text-xs font-bold bg-talkheals-sage/10 px-2.5 py-1 rounded-full border border-talkheals-sage/20">+12%</span>
                            </div>
                            <div class="text-talkheals-muted text-[0.65rem] uppercase tracking-[0.2em] font-bold mb-1">Total Clients</div>
                            <div class="text-3xl font-serif font-light text-talkheals-deep">1,284</div>
                        </div>

                        <div class="stat-card">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-talkheals-rose/10 flex items-center justify-center text-2xl border border-talkheals-rose/20">📅</div>
                                <span class="text-talkheals-gold text-xs font-bold bg-talkheals-gold/10 px-2.5 py-1 rounded-full border border-talkheals-gold/20">Today</span>
                            </div>
                            <div class="text-talkheals-muted text-[0.65rem] uppercase tracking-[0.2em] font-bold mb-1">Active Sessions</div>
                            <div class="text-3xl font-serif font-light text-talkheals-deep">12</div>
                        </div>

                        <div class="stat-card">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-talkheals-sage/10 flex items-center justify-center text-2xl border border-talkheals-sage/20">🎓</div>
                                <span class="text-talkheals-sage text-xs font-bold bg-talkheals-sage/10 px-2.5 py-1 rounded-full border border-talkheals-sage/20">New</span>
                            </div>
                            <div class="text-talkheals-muted text-[0.65rem] uppercase tracking-[0.2em] font-bold mb-1">Workshop Seats</div>
                            <div class="text-3xl font-serif font-light text-talkheals-deep">42</div>
                        </div>

                        <div class="stat-card">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-talkheals-sky/10 flex items-center justify-center text-2xl border border-talkheals-sky/20">💎</div>
                                <span class="text-talkheals-mid text-xs font-bold bg-talkheals-mid/5 px-2.5 py-1 rounded-full border border-talkheals-mid/10">MTD</span>
                            </div>
                            <div class="text-talkheals-muted text-[0.65rem] uppercase tracking-[0.2em] font-bold mb-1">Platform Revenue</div>
                            <div class="text-3xl font-serif font-light text-talkheals-deep">$14,250</div>
                        </div>
                    </div>

                    <!-- Main Section Card -->
                    <div class="bg-white border border-talkheals-gold-p rounded-[40px] p-12 flex flex-col items-center justify-center text-center min-h-[400px] relative overflow-hidden shadow-sm shadow-talkheals-gold/5">
                        <!-- Background Accent -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-talkheals-gold/5 rounded-full blur-[100px] pointer-events-none"></div>
                        
                        <div class="relative z-10">
                            <div class="w-20 h-20 rounded-3xl bg-talkheals-cream flex items-center justify-center text-4xl mb-8 mx-auto border border-talkheals-gold-p">🏢</div>
                            <h2 class="text-3xl font-serif font-light text-talkheals-deep mb-4">System Initialized</h2>
                            <p class="text-talkheals-mid max-w-lg mx-auto text-lg font-light leading-relaxed">
                                The TalkHeals Admin Portal is now fully structured. Use the sidebar to navigate through client records, appointment management, and workshop libraries.
                            </p>
                            <button class="mt-10 px-8 py-3.5 bg-talkheals-gold text-white font-bold rounded-2xl hover:bg-talkheals-deep transition-all duration-300 transform hover:-translate-y-1 shadow-xl shadow-talkheals-gold/10">
                                View Recent Activity
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.stat-card {
    @apply bg-white border border-talkheals-gold-p rounded-[32px] p-7 transition-all duration-300 hover:border-talkheals-gold hover:-translate-y-1 cursor-default shadow-sm shadow-talkheals-gold/5;
}

:deep(body) {
    background-color: var(--cream) !important;
}
</style>
