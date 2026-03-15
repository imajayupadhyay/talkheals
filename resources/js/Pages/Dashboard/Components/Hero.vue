<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const DEFAULTS = {
    tagline:           'Breathe & Scroll',
    click_note:        'Tap the bloom for ambient sound',
    heading_line1:     'Your journey',
    heading_line2:     'starts',
    heading_highlight: 'now',
    sub:               'Namrata Mohan, RP — a safe, warm space for your healing, wherever you are in the world.',
};

const c = computed(() => ({ ...DEFAULTS, ...props.content }));

const isPlaying = ref(false);
const breatheLabel = ref('Tap to begin');
const breatheIcon = ref('🌸');

// Web Audio logic
let actx = null;
let loopTimer = null;
const pentatonic = [261.6, 293.7, 329.6, 392, 440, 523.3, 587.3, 659.3];

const chime = (f, d = 0, vol = 0.032) => {
    if (!actx || !isPlaying.value) return;
    const o = actx.createOscillator();
    const g = actx.createGain();
    o.connect(g);
    g.connect(actx.destination);
    o.type = 'sine';
    o.frequency.value = f;
    const t = actx.currentTime + d;
    g.gain.setValueAtTime(0, t);
    g.gain.linearRampToValueAtTime(vol, t + .06);
    g.gain.exponentialRampToValueAtTime(.0001, t + 3.5);
    o.start(t);
    o.stop(t + 3.8);
};

const ambientLoop = () => {
    if (!isPlaying.value) return;
    const notes = [...pentatonic].sort(() => Math.random() - .5).slice(0, 3);
    notes.forEach((f, i) => chime(f, i * .6, 0.028 + Math.random() * 0.015));
    loopTimer = setTimeout(ambientLoop, 2200 + Math.random() * 1800);
};

const toggleSound = () => {
    if (!actx) {
        actx = new (window.AudioContext || window.webkitAudioContext)();
    }
    isPlaying.value = !isPlaying.value;
    if (isPlaying.value) {
        breatheLabel.value = 'Sound on';
        breatheIcon.value = '🎵';
        chime(392, 0, .05);
        chime(523.3, .4, .04);
        chime(659.3, .8, .03);
        ambientLoop();
    } else {
        breatheLabel.value = 'Tap to begin';
        breatheIcon.value = '🌸';
        if (loopTimer) {
            clearTimeout(loopTimer);
            loopTimer = null;
        }
    }
};
</script>

<template>
    <section class="breathe-hero min-h-screen flex flex-col items-center justify-center text-center px-10 pt-25 pb-15 relative z-10">
        <!-- THE BREATHE BUTTON -->
        <button 
            @click="toggleSound"
            id="breatheBtn"
            :class="['breathe-btn relative w-[200px] h-[200px] rounded-full bg-transparent border-none cursor-pointer flex flex-col items-center justify-center mb-12 opacity-0 fill-mode-forwards', isPlaying ? 'active' : '']"
        >
            <div class="breathe-ring-3 absolute inset-[-40px] rounded-full border border-talkheals-rose/10 animate-breatheRing duration-[4s] delay-[1000ms]"></div>
            <div class="breathe-ring-2 absolute inset-[-20px] rounded-full border-[1.5px] border-talkheals-gold/22 animate-breatheRing duration-[4s] delay-[500ms]"></div>
            <div class="breathe-ring-1 absolute inset-0 rounded-full bg-talkheals-rose/18 animate-breatheRing duration-[4s]"></div>
            <div 
                class="breathe-core w-[110px] h-[110px] rounded-full flex flex-col items-center justify-center gap-1 shadow-[0_12px_40px_rgba(196,154,138,0.35)] relative z-10 transition-all duration-400"
                :class="isPlaying ? 'active-core' : 'bg-gradient-to-br from-talkheals-rose-l to-talkheals-gold-l'"
            >
                <div class="breathe-icon text-[1.8rem] leading-none">{{ breatheIcon }}</div>
                <div class="breathe-label-sm text-[0.6rem] tracking-[0.12em] uppercase text-talkheals-mid font-medium">{{ breatheLabel }}</div>
            </div>
        </button>

        <div class="breathe-tagline font-serif text-[clamp(0.7rem,0.9rem,1rem)] font-light text-talkheals-muted tracking-[0.15em] uppercase mb-1.5 opacity-0 fill-mode-forwards">
            {{ c.tagline }}
        </div>
        <div class="breathe-click-note text-[0.7rem] text-talkheals-muted/60 tracking-[0.08em] mt-[-4px] mb-12 opacity-0 fill-mode-forwards">
            {{ c.click_note }}
        </div>

        <h1 class="hero-heading font-serif text-[clamp(2.8rem,6vw,5.2rem)] font-extralight leading-[1.05] text-talkheals-deep mb-[18px] opacity-0 fill-mode-forwards">
            {{ c.heading_line1 }}<br>{{ c.heading_line2 }} <em class="italic text-talkheals-rose not-italic">{{ c.heading_highlight }}</em>
        </h1>

        <p class="hero-sub text-[0.95rem] text-talkheals-muted font-light max-w-[420px] leading-[1.8] opacity-0 fill-mode-forwards">
            {{ c.sub }}
        </p>

        <!-- Scroll Hint -->
        <div class="scroll-hint absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-0 fill-mode-forwards">
            <div class="scroll-txt text-[0.62rem] tracking-[0.16em] uppercase text-talkheals-muted">Scroll</div>
            <div class="scroll-line w-[1px] h-12 bg-gradient-to-b from-talkheals-gold to-transparent animate-scrollPulse"></div>
        </div>
    </section>
</template>

<style scoped>
@keyframes bAppear {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes breatheRing {
    0%, 100% { transform: scale(1); opacity: 0.6; }
    50% { transform: scale(1.1); opacity: 1; }
}

@keyframes scrollPulse {
    0%, 100% { opacity: 0.4; transform: scaleY(1); }
    50% { opacity: 1; transform: scaleY(1.15); }
}

.breathe-btn { animation: bAppear 1s .3s forwards; }
.breathe-tagline { animation: bAppear 1s .7s forwards; }
.breathe-click-note { animation: bAppear 1s .9s forwards; }
.hero-heading { animation: bAppear 1s .5s forwards; }
.hero-sub { animation: bAppear 1s .75s forwards; }
.scroll-hint { animation: bAppear 1s 1.4s forwards; }

.active-core {
    background: linear-gradient(145deg, var(--sage-l), var(--sky-p)) !important;
}

.breathe-btn.active .breathe-ring-1 {
    background: rgba(122, 158, 142, .18) !important;
}

.breathe-btn.active .breathe-ring-2 {
    border-color: rgba(122, 158, 142, .25) !important;
}

.breathe-btn:hover .breathe-core {
    transform: scale(1.08);
    box-shadow: 0 18px 55px rgba(196, 154, 138, 0.45);
}
</style>
