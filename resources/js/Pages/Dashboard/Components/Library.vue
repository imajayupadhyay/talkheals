<script setup>
import { ref, onMounted } from 'vue';

const pwP = ref(0);
const pwT = ref(null);

const pwSlide = (d) => {
    if (!pwT.value) return;
    const mx = pwT.value.children.length - 3;
    pwP.value = Math.max(0, Math.min(pwP.value + d, mx));
    pwT.value.style.transform = `translateX(-${pwP.value * 308}px)`;
};

const showToast = (m) => {
    alert(m);
};

onMounted(() => {
    const obs = new IntersectionObserver(e => e.forEach((en, i) => {
        if (en.isIntersecting) setTimeout(() => en.target.classList.add('in'), i * 70)
    }), { threshold: .09 });
    document.querySelectorAll('.rv, .rv-l, .rv-r').forEach(el => obs.observe(el));
});
</script>

<template>
    <div class="pw-sec" id="past">
        <div class="pw-in">
            <div class="pw-hd rv">
                <div>
                    <div class="slabel">Free Recordings</div>
                    <h2 class="stitle">Past Workshop <em>Library</em></h2>
                </div>
                <div class="cbtns">
                    <button class="cbtn" @click="pwSlide(-1)">‹</button>
                    <button class="cbtn" @click="pwSlide(1)">›</button>
                </div>
            </div>
        </div>
        <div class="pw-wrap">
            <div class="pw-track" ref="pwT">
                <div class="pw-card">
                    <div class="pw-th pt1"><button class="play-btn" @click="showToast('▶ Opening...')">▶</button><span class="pw-dur">47:22</span></div>
                    <div class="pw-body"><div class="pw-cat">Anxiety</div><div class="pw-title">Breathwork for Anxiety: 5 Techniques That Actually Work</div><div class="pw-meta"><div class="pw-views">👁 2,341</div><button class="pw-watch" @click="showToast('▶ Opening...')">Watch</button></div></div>
                </div>
                <div class="pw-card">
                    <div class="pw-th pt2"><button class="play-btn" @click="showToast('▶ Opening...')">▶</button><span class="pw-dur">1:02:10</span></div>
                    <div class="pw-body"><div class="pw-cat">Relationships</div><div class="pw-title">Communicating Without Conflict: Tools for Couples</div><div class="pw-meta"><div class="pw-views">👁 1,876</div><button class="pw-watch" @click="showToast('▶ Opening...')">Watch</button></div></div>
                </div>
                <div class="pw-card">
                    <div class="pw-th pt3"><button class="play-btn" @click="showToast('▶ Opening...')">▶</button><span class="pw-dur">38:55</span></div>
                    <div class="pw-body"><div class="pw-cat">Mindfulness</div><div class="pw-title">Body Scan Meditation: Reconnect With Yourself</div><div class="pw-meta"><div class="pw-views">👁 3,108</div><button class="pw-watch" @click="showToast('▶ Opening...')">Watch</button></div></div>
                </div>
                <div class="pw-card">
                    <div class="pw-th pt4"><button class="play-btn" @click="showToast('▶ Opening...')">▶</button><span class="pw-dur">55:04</span></div>
                    <div class="pw-body"><div class="pw-cat">Self-Esteem</div><div class="pw-title">Silencing the Inner Critic</div><div class="pw-meta"><div class="pw-views">👁 1,540</div><button class="pw-watch" @click="showToast('▶ Opening...')">Watch</button></div></div>
                </div>
                <div class="pw-card">
                    <div class="pw-th pt5"><button class="play-btn" @click="showToast('▶ Opening...')">▶</button><span class="pw-dur">43:28</span></div>
                    <div class="pw-body"><div class="pw-cat">Grief</div><div class="pw-title">Holding Grief: The Many Faces of Loss</div><div class="pw-meta"><div class="pw-views">👁 992</div><button class="pw-watch" @click="showToast('▶ Opening...')">Watch</button></div></div>
                </div>
                <div class="pw-card">
                    <div class="pw-th pt6"><button class="play-btn" @click="showToast('▶ Opening...')">▶</button><span class="pw-dur">50:15</span></div>
                    <div class="pw-body"><div class="pw-cat">Post-Partum</div><div class="pw-title">You Are Not Alone: Post-Partum Healing</div><div class="pw-meta"><div class="pw-views">👁 2,215</div><button class="pw-watch" @click="showToast('▶ Opening...')">Watch</button></div></div>
                </div>
            </div>
        </div>
        <div style="text-align:center;margin-top:28px;padding:0 48px">
            <button class="btn-rose" @click="window.open('https://talkheals.ca/events','_blank')">View All Workshops →</button>
        </div>
    </div>
</template>

<style scoped>
/* ══════════════════════════════════════
   PAST WORKSHOPS CAROUSEL (EXACT CSS)
══════════════════════════════════════ */
.pw-sec{padding:80px 0;position:relative;z-index:1}
.pw-in{max-width:1100px;margin:0 auto;padding:0 48px}
.pw-hd{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:38px}
.cbtns{display:flex;gap:8px}
.cbtn{width:38px;height:38px;border-radius:50%;border:1.5px solid var(--gold-l);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.92rem;color:var(--deep);transition:all .22s}
.cbtn:hover{background:var(--rose);border-color:var(--rose);color:white}
.pw-wrap{overflow:hidden;padding:10px 48px;margin:0 -48px}
.pw-track{display:flex;gap:20px;transition:transform .52s cubic-bezier(.77,0,.175,1)}
.pw-card{flex:0 0 288px;background:white;border-radius:18px;overflow:hidden;border:1px solid var(--gold-p);transition:all .32s;cursor:pointer}
.pw-card:hover{transform:translateY(-7px);box-shadow:0 20px 48px rgba(42,36,32,.09)}
.pw-th{height:165px;display:flex;align-items:center;justify-content:center;position:relative}
.pt1{background:linear-gradient(135deg,#e8d5b0,#c9a96e)}.pt2{background:linear-gradient(135deg,#e8cfc6,#c49a8a)}.pt3{background:linear-gradient(135deg,#b4d0c4,#7a9e8e)}.pt4{background:linear-gradient(135deg,#dceaf3,#8bb0c4)}.pt5{background:linear-gradient(135deg,#f0eaf4,#b8a0b8)}.pt6{background:linear-gradient(135deg,#e8f3ef,#7a9e8e)}
.play-btn{width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,.92);border:none;display:flex;align-items:center;justify-content:center;font-size:.95rem;cursor:pointer;transition:all .3s;box-shadow:0 4px 16px rgba(42,36,32,.14)}
.play-btn:hover{transform:scale(1.14);background:white}
.pw-dur{position:absolute;bottom:9px;right:11px;background:rgba(42,36,32,.42);color:white;font-size:.65rem;padding:2px 9px;border-radius:20px}
.pw-body{padding:16px 18px}
.pw-cat{font-size:.62rem;letter-spacing:.1em;text-transform:uppercase;color:var(--gold);margin-bottom:5px}
.pw-title{font-size:.9rem;font-weight:500;color:var(--deep);line-height:1.32;margin-bottom:10px}
.pw-meta{display:flex;justify-content:space-between;align-items:center}
.pw-views{font-size:.68rem;color:var(--muted)}
.pw-watch{padding:4px 12px;border:1px solid var(--gold-l);border-radius:20px;font-size:.65rem;font-family:'DM Sans',sans-serif;color:var(--gold);cursor:pointer;background:none;transition:all .2s}
.pw-watch:hover{background:var(--gold);color:white}

.btn-rose{padding:12px 28px;background:var(--rose);color:white;border:none;border-radius:40px;font-family:'DM Sans',sans-serif;font-size:.84rem;cursor:pointer;transition:all .32s cubic-bezier(.34,1.56,.64,1);font-weight:500}
.btn-rose:hover{background:var(--deep);transform:translateY(-3px);box-shadow:0 10px 28px rgba(196,154,138,.28)}

.slabel { font-size: .66rem; letter-spacing: .18em; text-transform: uppercase; color: var(--gold); margin-bottom: 10px; }
.stitle { font-family: 'Playfair Display', serif; font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 400; line-height: 1.15; color: var(--deep); margin-bottom: 10px; }
.stitle em { font-style: italic; color: var(--rose); }

@media(max-width:900px){
  .pw-wrap{padding:10px 20px;margin:0 -20px}
  .pw-in{padding:0 20px}
}
</style>
