<script setup>
import { ref, onMounted } from 'vue';

const rvP = ref(0);
const rvT = ref(null);

const rvSlide = (d) => {
    if (!rvT.value) return;
    const mx = rvT.value.children.length - 3;
    rvP.value = Math.max(0, Math.min(rvP.value + d, mx));
    rvT.value.style.transform = `translateX(-${rvP.value * 340}px)`;
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
    <div class="rv-sec" id="reviews">
        <div class="rv-in">
            <div class="rv-hd">
                <div><div class="rv-label">Healing Stories</div><h2 class="rv-title">What clients say about <em>Namrata</em></h2></div>
                <div class="cbtns">
                    <button class="cbtn" style="border-color:rgba(201,169,110,.22);color:rgba(255,255,255,.45)" @click="rvSlide(-1)">‹</button>
                    <button class="cbtn" style="border-color:rgba(201,169,110,.22);color:rgba(255,255,255,.45)" @click="rvSlide(1)">›</button>
                </div>
            </div>
        </div>
        <div class="rv-wrap">
            <div class="rv-track" ref="rvT">
                <div class="rv-card">
                    <div class="rv-stars">★★★★★</div>
                    <div class="rv-text">"Namrata has this rare gift of making you feel seen without judgment. After three sessions I understood my anxiety in a way years of suppression never allowed."</div>
                    <div class="rv-author">
                        <div class="rv-av" style="background:linear-gradient(135deg,#c49a8a,#c9a96e)">P</div>
                        <div><div class="rv-name">Priya R.</div><div class="rv-loc">Toronto, ON</div><div class="rv-tag">Anxiety</div></div>
                    </div>
                </div>
                <div class="rv-card">
                    <div class="rv-stars">★★★★★</div>
                    <div class="rv-text">"As a new immigrant I felt completely lost. Namrata understood without me having to explain everything. She helped me rebuild my identity with such grace."</div>
                    <div class="rv-author">
                        <div class="rv-av" style="background:linear-gradient(135deg,#7a9e8e,#8bb0c4)">S</div>
                        <div><div class="rv-name">Supriya K.</div><div class="rv-loc">Vancouver, BC</div><div class="rv-tag">Immigration & Identity</div></div>
                    </div>
                </div>
                <div class="rv-card">
                    <div class="rv-stars">★★★★★</div>
                    <div class="rv-text">"Post-partum depression nearly broke me. Namrata created a space where I could finally say that out loud. Her compassion guided me back to my baby."</div>
                    <div class="rv-author">
                        <div class="rv-av" style="background:linear-gradient(135deg,#b8a0b8,#c49a8a)">M</div>
                        <div><div class="rv-name">Meera T.</div><div class="rv-loc">Brampton, ON</div><div class="rv-tag">Post-Partum</div></div>
                    </div>
                </div>
                <div class="rv-card">
                    <div class="rv-stars">★★★★★</div>
                    <div class="rv-text">"My husband and I were at a breaking point. Namrata's couples therapy gave us a language for each other again — a truly rare skill."</div>
                    <div class="rv-author">
                        <div class="rv-av" style="background:linear-gradient(135deg,#c9a96e,#7a9e8e)">A</div>
                        <div><div class="rv-name">Anjali & Raj V.</div><div class="rv-loc">Mississauga, ON</div><div class="rv-tag">Couples Therapy</div></div>
                    </div>
                </div>
                <div class="rv-card">
                    <div class="rv-stars">★★★★★</div>
                    <div class="rv-text">"Within two sessions I was having breakthroughs I hadn't thought possible. The free first call convinced me — best decision I ever made."</div>
                    <div class="rv-author">
                        <div class="rv-av" style="background:linear-gradient(135deg,#8bb0c4,#b8a0b8)">D</div>
                        <div><div class="rv-name">David O.</div><div class="rv-loc">Ottawa, ON</div><div class="rv-tag">Depression</div></div>
                    </div>
                </div>
                <div class="rv-card">
                    <div class="rv-stars">★★★★★</div>
                    <div class="rv-text">"Going through divorce felt like the end. Namrata helped me see it as a beginning. Her empathy transformed the most painful chapter of my life."</div>
                    <div class="rv-author">
                        <div class="rv-av" style="background:linear-gradient(135deg,#e8cfc6,#c49a8a)">R</div>
                        <div><div class="rv-name">Rekha N.</div><div class="rv-loc">Calgary, AB</div><div class="rv-tag">Divorce Recovery</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rv-bot">
            <button class="btn-rose" @click="window.location='https://talkheals.ca/appointment-request'">Begin Your Story</button>
            <button class="btn-dark-out" @click="showToast('📖 Loading reviews...')">Read All →</button>
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
.rv-title{font-family:'Fraunces',serif;font-size:clamp(1.7rem,3.2vw,2.6rem);font-weight:200;color:white;line-height:1.12}
.rv-title em{font-style:italic;color:var(--rose-l)}
.rv-wrap{overflow:hidden;padding:10px 48px;margin:0 -48px}
.rv-track{display:flex;gap:20px;transition:transform .52s cubic-bezier(.77,0,.175,1)}
.rv-card{flex:0 0 320px;background:rgba(255,255,255,.04);border:1px solid rgba(201,169,110,.12);border-radius:20px;padding:28px 24px;backdrop-filter:blur(10px);transition:all .3s}
.rv-card:hover{background:rgba(255,255,255,.08);border-color:rgba(201,169,110,.28);transform:translateY(-5px)}
.rv-stars{color:var(--gold);font-size:.8rem;margin-bottom:12px;letter-spacing:2px}
.rv-text{font-size:.88rem;line-height:1.75;color:rgba(255,255,255,.6);font-weight:300;font-family:'Fraunces',serif;font-style:italic;margin-bottom:20px}
.rv-author{display:flex;align-items:center;gap:10px}
.rv-av{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:500;color:white;flex-shrink:0}
.rv-name{font-size:.82rem;color:white;font-weight:500}
.rv-loc{font-size:.67rem;color:rgba(255,255,255,.32);margin-top:1px}
.rv-tag{display:inline-block;margin-top:4px;padding:2px 10px;border-radius:20px;background:rgba(201,169,110,.09);color:var(--gold-l);font-size:.64rem}
.rv-bot{text-align:center;margin-top:42px;position:relative;z-index:1;display:flex;gap:12px;justify-content:center}

.cbtns{display:flex;gap:8px}
.cbtn{width:38px;height:38px;border-radius:50%;border:1.5px solid var(--gold-l);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.92rem;color:var(--deep);transition:all .22s}
.cbtn:hover{background:var(--rose);border-color:var(--rose);color:white}

.btn-rose{padding:12px 28px;background:var(--rose);color:white;border:none;border-radius:40px;font-family:'Jost',sans-serif;font-size:.84rem;cursor:pointer;transition:all .32s cubic-bezier(.34,1.56,.64,1);font-weight:500}
.btn-rose:hover{background:var(--deep);transform:translateY(-3px);box-shadow:0 10px 28px rgba(196,154,138,.28)}

.btn-dark-out{padding:12px 28px;background:transparent;border:1.5px solid rgba(255,255,255,.2);color:rgba(255,255,255,.8);border-radius:40px;font-family:'Jost',sans-serif;font-size:.84rem;cursor:pointer;transition:all .3s}
.btn-dark-out:hover{border-color:white;color:white}

@media(max-width:900px){
  .rv-wrap{padding:10px 20px;margin:0 -20px}
  .rv-in{padding:0 20px}
}
</style>
