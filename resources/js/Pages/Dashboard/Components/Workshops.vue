<script setup>
import { onMounted } from 'vue';

const showToast = (m) => {
    // Basic toast logic for the demo
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
    <div class="upcoming" id="upcoming">
        <div class="uw-in">
            <div class="slabel rv">Live &amp; Free</div>
            <h2 class="stitle rv">Upcoming <em>Workshops</em></h2>
            <p class="ssub rv">Join Namrata live — always free, always open.</p>
            <div class="uw-list">
                <div class="uw-card rv d1">
                    <div class="uw-date">
                        <div class="uw-mon">Mar</div>
                        <div class="uw-day">20</div>
                    </div>
                    <div class="uw-info">
                        <h4>Anxiety &amp; the Immigrant Mind</h4>
                        <p>🕔 7:00 PM EST · 75 min · Zoom</p>
                        <div class="uw-tags">
                            <span class="utag tf">Free</span>
                            <span class="utag tl">Live</span>
                            <span class="utag th">8 spots</span>
                        </div>
                    </div>
                    <button class="uw-btn" @click="showToast('🎉 Registered! Zoom link on its way.')">Register →</button>
                </div>
                <div class="uw-card rv d2">
                    <div class="uw-date">
                        <div class="uw-mon">Mar</div>
                        <div class="uw-day">27</div>
                    </div>
                    <div class="uw-info">
                        <h4>Post-Partum: The Side No One Talks About</h4>
                        <p>🕔 6:30 PM EST · 60 min · Zoom</p>
                        <div class="uw-tags">
                            <span class="utag tf">Free</span>
                            <span class="utag tl">Live</span>
                        </div>
                    </div>
                    <button class="uw-btn" @click="showToast('🎉 Registered! Zoom link on its way.')">Register →</button>
                </div>
                <div class="uw-card rv d3">
                    <div class="uw-date">
                        <div class="uw-mon">Apr</div>
                        <div class="uw-day">3</div>
                    </div>
                    <div class="uw-info">
                        <h4>Understanding Depression</h4>
                        <p>🕔 5:00 PM EST · 60 min · Zoom</p>
                        <div class="uw-tags">
                            <span class="utag tf">Free</span>
                            <span class="utag tl">Live</span>
                        </div>
                    </div>
                    <button class="uw-btn" @click="showToast('🔔 We\'ll notify you when it opens!')">Notify Me</button>
                </div>
                <div class="uw-card rv d4">
                    <div class="uw-date">
                        <div class="uw-mon">Apr</div>
                        <div class="uw-day">10</div>
                    </div>
                    <div class="uw-info">
                        <h4>Navigating Divorce with Dignity</h4>
                        <p>🕔 7:30 PM EST · 90 min · Zoom</p>
                        <div class="uw-tags">
                            <span class="utag tf">Free</span>
                            <span class="utag tl">Live</span>
                        </div>
                    </div>
                    <button class="uw-btn" @click="showToast('🔔 We\'ll notify you when it opens!')">Notify Me</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ══════════════════════════════════════
   UPCOMING WORKSHOPS (EXACT CSS)
══════════════════════════════════════ */
.upcoming { background: linear-gradient(155deg, var(--rose-p), var(--gold-p), var(--sage-p)); padding: 80px 0; position: relative; z-index: 1; }
.uw-in { max-width: 1100px; margin: 0 auto; padding: 0 48px; }
.uw-list { display: flex; flex-direction: column; gap: 12px; margin-top: 40px; }
.uw-card {
  display: flex; align-items: center; gap: 20px;
  background: rgba(255, 255, 255, .78); backdrop-filter: blur(12px);
  border: 1px solid rgba(201, 169, 110, .18); border-radius: 18px;
  padding: 20px 24px; transition: all .35s cubic-bezier(.34, 1.56, .64, 1); cursor: pointer;
}
.uw-card:hover { transform: translateX(7px) translateY(-2px); border-color: var(--rose-l); box-shadow: 0 14px 42px rgba(196, 154, 138, .14); }
.uw-date { flex-shrink: 0; width: 52px; text-align: center; background: var(--rose-p); border-radius: 11px; padding: 8px 5px; border: 1px solid var(--rose-l); }
.uw-mon { font-size: .58rem; text-transform: uppercase; letter-spacing: .1em; color: var(--rose); }
.uw-day { font-family: 'Fraunces', serif; font-size: 1.75rem; color: var(--deep); line-height: 1; }
.uw-info { flex: 1; }
.uw-info h4 { font-size: .92rem; font-weight: 500; margin-bottom: 4px; color: var(--deep); }
.uw-info p { font-size: .76rem; color: var(--muted); }
.uw-tags { display: flex; gap: 6px; margin-top: 6px; }
.utag { padding: 2px 10px; border-radius: 20px; font-size: .65rem; }
.tf { background: var(--sage-p); color: var(--sage); }
.tl { background: var(--sky-p); color: var(--sky); }
.th { background: var(--rose-p); color: var(--rose); }
.uw-btn { padding: 8px 18px; border: 1.5px solid var(--rose); border-radius: 40px; background: none; font-family: 'Jost', sans-serif; font-size: .76rem; color: var(--rose); cursor: pointer; transition: all .25s; font-weight: 500; flex-shrink: 0; }
.uw-btn:hover { background: var(--rose); color: white; }

.slabel { font-size: .66rem; letter-spacing: .18em; text-transform: uppercase; color: var(--gold); margin-bottom: 10px; }
.stitle { font-family: 'Fraunces', serif; font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 300; line-height: 1.15; color: var(--deep); margin-bottom: 10px; }
.stitle em { font-style: italic; color: var(--rose); }
.ssub { font-size: .9rem; color: var(--muted); max-width: 420px; line-height: 1.78; font-weight: 300; }

@media(max-width:900px){
  .upcoming { padding-left: 0; padding-right: 0; }
  .uw-in { padding: 0 20px; }
}
</style>
