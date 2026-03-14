<script setup>
import { Head } from '@inertiajs/vue3';
import Header from './Components/Header.vue';
import Hero from './Components/Hero.vue';
import Sessions from './Components/Sessions.vue';
import Workshops from './Components/Workshops.vue';
import Library from './Components/Library.vue';
import Reviews from './Components/Reviews.vue';
import Articles from './Components/Articles.vue';
import About from './Components/About.vue';
import Footer from './Components/Footer.vue';

const props = defineProps({
    workshops: {
        type: Array,
        default: () => [],
    },
    hero: {
        type: Object,
        default: () => ({}),
    },
    sessions: {
        type: Object,
        default: () => ({}),
    },
    testimonials: {
        type: Array,
        default: () => [],
    },
    reviews: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <Head title="Dashboard - TalkHeals" />

    <div class="relative min-h-screen bg-talkheals-cream overflow-x-hidden">
        <!-- Background Orbs -->
        <div class="orbs">
            <div class="orb o1"></div>
            <div class="orb o2"></div>
            <div class="orb o3"></div>
        </div>

        <!-- Navigation -->
        <Header />

        <!-- Main Content -->
        <main>
            <Hero :content="hero" />
            <Sessions :content="sessions" />
            <Workshops :workshops="workshops" />
            <Library />
            <Reviews :testimonials="testimonials" :content="reviews" />
            <Articles />
            <About />
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<style scoped>
.orbs {
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    overflow: hidden;
}

.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(85px);
    animation: orbDrift ease-in-out infinite;
}

.o1 { width: 650px; height: 650px; background: rgba(201, 169, 110, .1); top: -180px; left: -180px; animation-duration: 26s; }
.o2 { width: 480px; height: 480px; background: rgba(196, 154, 138, .11); top: 38%; right: -80px; animation-duration: 21s; animation-delay: -8s; }
.o3 { width: 560px; height: 560px; background: rgba(122, 158, 142, .08); bottom: -80px; left: 18%; animation-duration: 30s; animation-delay: -14s; }

@keyframes orbDrift {
    0%, 100% { transform: translate(0, 0) }
    34% { transform: translate(30px, -40px) }
    67% { transform: translate(-18px, 22px) }
}
</style>
