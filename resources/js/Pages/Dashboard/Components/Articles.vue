<script setup>
import { onMounted } from 'vue';

const showToast = (m) => {
    alert(m);
};

const subscribeNl = () => {
    const email = document.getElementById('nlEmail').value;
    if(!email || !email.includes('@')) {
        showToast('📧 Please enter a valid email.');
        return;
    }
    showToast('🌿 Welcome! Your first newsletter is on its way.');
};

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) entry.target.classList.add('in');
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.rv, .rv-l, .rv-r').forEach(el => observer.observe(el));
});
</script>

<template>
    <div class="content-band" id="content">
        <div class="content-inner">
            <div class="sess-intro rv" style="margin-bottom:0">
                <div class="sess-intro-label">From Namrata's Desk</div>
                <div class="sess-intro-title">Read. Learn. <em>Feel less alone.</em></div>
            </div>
            <div class="content-grid">

                <!-- ARTICLES -->
                <div class="art-card rv rv-l d1">
                    <div class="art-badge">✦ Articles &amp; Blog</div>
                    <h3 class="art-title">Words that <em>heal</em></h3>
                    <p class="art-desc">Namrata writes openly about mental health — no jargon, just honesty. Topics you've wondered about, finally spoken aloud.</p>
                    <div class="art-previews">
                        <div class="art-prev" @click="window.open('https://talkheals.ca/blog','_blank')">
                            <div class="art-prev-icon">🌿</div>
                            <div><div class="art-prev-text">Why Therapy Isn't Weakness — It's Courage</div><div class="art-prev-tag">Mental Health · 5 min read</div></div>
                            <div class="art-prev-arr">→</div>
                        </div>
                        <div class="art-prev" @click="window.open('https://talkheals.ca/blog','_blank')">
                            <div class="art-prev-icon">🌸</div>
                            <div><div class="art-prev-text">The Invisible Weight of the Immigrant Experience</div><div class="art-prev-tag">Identity · 7 min read</div></div>
                            <div class="art-prev-arr">→</div>
                        </div>
                        <div class="art-prev" @click="window.open('https://talkheals.ca/articles','_blank')">
                            <div class="art-prev-icon">💛</div>
                            <div><div class="art-prev-text">What No One Tells You About Post-Part_um Anxiety</div><div class="art-prev-tag">Post-Part_um · 6 min read</div></div>
                            <div class="art-prev-arr">→</div>
                        </div>
                    </div>
                    <button class="btn-rose" @click="window.open('https://talkheals.ca/blog','_blank')">Read All Articles →</button>
                </div>

                <!-- NEWSLETTER -->
                <div class="nl-card rv rv-r d2">
                    <div class="nl-badge">✦ Free · Weekly</div>
                    <h3 class="nl-title">Your weekly<br><em>moment of calm</em></h3>
                    <p class="nl-desc">Namrata's newsletter — one gentle insight, one breathing prompt, one reminder that you're doing better than you think. Every week, free.</p>
                    <ul class="nl-perks">
                        <li><div class="ck">✓</div> Mental health insights</li>
                        <li><div class="ck">✓</div> Workshop announcements first</li>
                        <li><div class="ck">✓</div> Early access to free calls</li>
                        <li><div class="ck">✓</div> No spam. Unsubscribe anytime.</li>
                    </ul>
                    <div class="nl-input-wrap">
                        <input type="email" class="nl-input" placeholder="your@email.com" id="nlEmail" />
                    </div>
                    <button class="nl-btn" @click="subscribeNl()">Subscribe Free →</button>
                    <div class="nl-note">Joined by 2,400+ readers · 100% free forever</div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
/* ══════════════════════════════════════
   ARTICLES + NEWSLETTER (EXACT CSS)
══════════════════════════════════════ */
.content-band{ background:var(--cream); padding:80px 48px; position:relative; z-index:1; }
.content-inner{ max-width:1100px; margin:0 auto }
.content-grid{ display:grid; grid-template-columns:1fr 1fr; gap:22px; margin-top:44px }

.sess-intro{ text-align:center; margin-bottom:44px }
.sess-intro-label{ font-size:.68rem; letter-spacing:.18em; text-transform:uppercase; color:var(--gold); margin-bottom:10px }
.sess-intro-title{ font-family:'Fraunces',serif; font-size:clamp(1.6rem,3vw,2.4rem); font-weight:300; color:var(--deep) }
.sess-intro-title em{ font-style:italic; color:var(--rose) }

/* Articles card */
.art-card{
  background:white; border-radius:24px;
  padding:42px 38px; border:1.5px solid var(--gold-l);
  position:relative; overflow:hidden; transition:all .35s;
}
.art-card:hover{ transform:translateY(-4px); box-shadow:0 18px 52px rgba(42,36,32,.08) }
.art-card::before{ content:''; position:absolute; width:200px; height:200px; border-radius:50%; background:rgba(201,169,110,.06); top:-60px; right:-60px }
.art-badge{ display:inline-flex; align-items:center; gap:6px; padding:5px 13px; border-radius:40px; font-size:.66rem; letter-spacing:.1em; text-transform:uppercase; border:1px solid var(--gold-l); color:var(--gold); background:var(--gold-p); margin-bottom:18px }
.art-title{ font-family:'Fraunces',serif; font-size:1.9rem; font-weight:300; color:var(--deep); line-height:1.15; margin-bottom:12px }
.art-title em{ font-style:italic; color:var(--gold) }
.art-desc{ font-size:.84rem; color:var(--muted); line-height:1.78; font-weight:300; margin-bottom:28px }
.art-previews{ display:flex; flex-direction:column; gap:10px; margin-bottom:28px }
.art-prev{ display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:11px; background:var(--cream); border:1px solid var(--gold-p); cursor:pointer; transition:all .28s }
.art-prev:hover{ border-color:var(--gold); background:var(--gold-p); transform:translateX(4px) }
.art-prev-icon{ font-size:1.1rem; flex-shrink:0 }
.art-prev-text{ font-size:.8rem; color:var(--deep); font-weight:400; line-height:1.3; text-align:left; }
.art-prev-tag{ font-size:.65rem; color:var(--muted); margin-top:2px; text-align:left; }
.art-prev-arr{ margin-left:auto; font-size:.85rem; color:var(--gold); opacity:.6; flex-shrink:0 }

/* Newsletter card */
.nl-card{
  background:linear-gradient(155deg,#2a2420 0%,#3a2d28 55%,#283428 100%);
  border-radius:24px; padding:42px 38px; position:relative; overflow:hidden;
}
.nl-card::before{ content:''; position:absolute; width:250px; height:250px; border-radius:50%; background:rgba(201,169,110,.09); top:-70px; right:-60px; animation:orbDrift 18s ease-in-out infinite }
.nl-card::after{ content:''; position:absolute; width:150px; height:150px; border-radius:50%; background:rgba(122,158,142,.06); bottom:-30px; left:20% }
.nl-badge{ display:inline-flex; align-items:center; gap:6px; padding:5px 13px; border-radius:40px; font-size:.66rem; letter-spacing:.1em; text-transform:uppercase; border:1px solid rgba(201,169,110,.3); color:var(--gold-l); background:rgba(201,169,110,.08); margin-bottom:18px; position:relative; z-index:1 }
.nl-title{ font-family:'Fraunces',serif; font-size:1.9rem; font-weight:200; color:white; line-height:1.15; margin-bottom:12px; position:relative; z-index:1 }
.nl-title em{ font-style:italic; color:var(--gold-l) }
.nl-desc{ font-size:.84rem; color:rgba(255,255,255,.48); line-height:1.78; font-weight:300; margin-bottom:28px; position:relative; z-index:1 }
.nl-perks{ list-style:none; margin-bottom:28px; position:relative; z-index:1; display:flex; flex-direction:column; gap:6px }
.nl-perks li{ display:flex; align-items:center; gap:8px; color:rgba(255,255,255,.62); font-size:.8rem }
.ck{ width:16px; height:16px; border-radius:50%; background:rgba(201,169,110,.18); border:1px solid rgba(201,169,110,.32); display:flex; align-items:center; justify-content:center; font-size:.5rem; color:var(--gold-l); flex-shrink:0 }

.nl-input-wrap{ position:relative; z-index:1; margin-bottom:10px }
.nl-input{ width:100%; padding:13px 16px; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.12); border-radius:11px; color:white; font-family:'Jost',sans-serif; font-size:.86rem; outline:none; transition:border-color .3s,background .3s }
.nl-input::placeholder{ color:rgba(255,255,255,.28) }
.nl-input:focus{ border-color:var(--gold-l); background:rgba(255,255,255,.1) }
.nl-btn{ width:100%; padding:14px; background:var(--gold); color:white; border:none; border-radius:11px; font-family:'Jost',sans-serif; font-size:.88rem; cursor:pointer; font-weight:500; letter-spacing:.04em; transition:all .3s; position:relative; z-index:1 }
.nl-btn:hover{ background:white; color:var(--deep); transform:translateY(-2px) }
.nl-note{ font-size:.67rem; color:rgba(255,255,255,.3); text-align:center; margin-top:9px; position:relative; z-index:1 }

.btn-rose{ padding:12px 28px; background:var(--rose); color:white; border:none; border-radius:40px; font-family:'Jost',sans-serif; font-size:.84rem; cursor:pointer; transition:all .32s cubic-bezier(.34,1.56,.64,1); font-weight:500; width: 100%; }
.btn-rose:hover{ background:var(--deep); transform:translateY(-3px); box-shadow:0 10px 28px rgba(196,154,138,.28) }

@keyframes orbDrift {
    0%, 100% { transform: translate(0, 0) }
    34% { transform: translate(30px, -40px) }
    67% { transform: translate(-18px, 22px) }
}

@media(max-width:900px){
  .content-band{ padding-left:20px; padding-right:20px }
  .content-grid{ grid-template-columns:1fr }
}
</style>
