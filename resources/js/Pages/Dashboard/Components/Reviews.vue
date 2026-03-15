<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    testimonials: { type: Array, default: () => [] },
    content:      { type: Object, default: () => ({}) },
});

const DEFAULTS = {
    label:           'Healing Stories',
    title:           'What clients say about',
    title_highlight: 'Namrata',
};

const c = computed(() => ({ ...DEFAULTS, ...props.content }));

const rvP = ref(0);
const rvT = ref(null);

const rvSlide = (d) => {
    if (!rvT.value) return;
    const mx = rvT.value.children.length - 3;
    rvP.value = Math.max(0, Math.min(rvP.value + d, mx));
    rvT.value.style.transform = `translateX(-${rvP.value * 340}px)`;
};

// Derive avatar initial from name
const initial = (name) => name ? name.charAt(0).toUpperCase() : '?';

// Repeat stars string
const stars = (count) => '★'.repeat(Math.max(1, Math.min(5, count ?? 5)));

onMounted(() => {
    const obs = new IntersectionObserver(e => e.forEach((en, i) => {
        if (en.isIntersecting) setTimeout(() => en.target.classList.add('in'), i * 70)
    }), { threshold: .09 });
    document.querySelectorAll('.rv, .rv-l, .rv-r').forEach(el => obs.observe(el));
});
</script>

<template>
    <div class="rv-sec" id="reviews">
        <div class="rv-in">
            <div class="rv-hd">
                <div><div class="rv-label">{{ c.label }}</div><h2 class="rv-title">{{ c.title }} <em>{{ c.title_highlight }}</em></h2></div>
                <div class="cbtns">
                    <button class="cbtn" style="border-color:rgba(201,169,110,.22);color:rgba(255,255,255,.45)" @click="rvSlide(-1)">‹</button>
                    <button class="cbtn" style="border-color:rgba(201,169,110,.22);color:rgba(255,255,255,.45)" @click="rvSlide(1)">›</button>
                </div>
            </div>
        </div>
        <div class="rv-wrap">
            <div class="rv-track" ref="rvT">
                <div v-for="t in testimonials" :key="t.id" class="rv-card">
                    <div class="rv-stars">{{ stars(t.stars) }}</div>
                    <div class="rv-text">"{{ t.quote }}"</div>
                    <div class="rv-author">
                        <div class="rv-av" :style="{ background: t.avatar_gradient || 'linear-gradient(135deg,#c49a8a,#c9a96e)' }">{{ initial(t.name) }}</div>
                        <div>
                            <div class="rv-name">{{ t.name }}</div>
                            <div v-if="t.location" class="rv-loc">{{ t.location }}</div>
                            <div v-if="t.tag" class="rv-tag">{{ t.tag }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ══════════════════════════════════════
   REVIEWS CAROUSEL (EXACT CSS)
══════════════════════════════════════ */
.rv-sec{background:var(--deep);padding:80px 0;position:relative;z-index:1;overflow:hidden}
.rv-sec::before{content:'';position:absolute;width:480px;height:480px;border-radius:50%;background:rgba(201,169,110,.05);top:-140px;right:-60px}
.rv-in{max-width:1100px;margin:0 auto;padding:0 48px}
.rv-hd{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:42px}
.rv-label{font-size:.66rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold)}
.rv-title{font-family:'Playfair Display',serif;font-size:clamp(1.7rem,3.2vw,2.6rem);font-weight:400;color:white;line-height:1.12}
.rv-title em{font-style:italic;color:var(--rose-l)}
.rv-wrap{overflow:hidden;padding:10px 48px;margin:0 -48px}
.rv-track{display:flex;gap:20px;transition:transform .52s cubic-bezier(.77,0,.175,1)}
.rv-card{flex:0 0 320px;background:rgba(255,255,255,.04);border:1px solid rgba(201,169,110,.12);border-radius:20px;padding:28px 24px;backdrop-filter:blur(10px);transition:all .3s}
.rv-card:hover{background:rgba(255,255,255,.08);border-color:rgba(201,169,110,.28);transform:translateY(-5px)}
.rv-stars{color:var(--gold);font-size:.8rem;margin-bottom:12px;letter-spacing:2px}
.rv-text{font-size:.88rem;line-height:1.75;color:rgba(255,255,255,.6);font-weight:300;font-family:'Playfair Display',serif;font-style:italic;margin-bottom:20px}
.rv-author{display:flex;align-items:center;gap:10px}
.rv-av{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:500;color:white;flex-shrink:0}
.rv-name{font-size:.82rem;color:white;font-weight:500}
.rv-loc{font-size:.67rem;color:rgba(255,255,255,.32);margin-top:1px}
.rv-tag{display:inline-block;margin-top:4px;padding:2px 10px;border-radius:20px;background:rgba(201,169,110,.09);color:var(--gold-l);font-size:.64rem}
.cbtns{display:flex;gap:8px}
.cbtn{width:38px;height:38px;border-radius:50%;border:1.5px solid var(--gold-l);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.92rem;color:var(--deep);transition:all .22s}
.cbtn:hover{background:var(--rose);border-color:var(--rose);color:white}


@media(max-width:900px){
  .rv-wrap{padding:10px 20px;margin:0 -20px}
  .rv-in{padding:0 20px}
}
</style>
