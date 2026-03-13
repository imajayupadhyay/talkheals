<script setup>
import { onMounted } from 'vue';

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
    <section class="upcoming-sec" id="upcoming">
        <div class="uw-inner">
            <!-- Header -->
            <div class="uw-header rv">
                <div class="header-left">
                    <div class="slabel">Live &amp; Free</div>
                    <h2 class="stitle">Upcoming <em>Workshops</em></h2>
                </div>
                <button class="view-all-btn" @click="window.open('https://talkheals.ca/events','_blank')">
                    View Calendar <span>→</span>
                </button>
            </div>

            <!-- Horizontal Grid -->
            <div class="uw-grid">
                <!-- Workshop 1 -->
                <div class="uw-card-modern rv d1">
                    <div class="card-thumb">
                        <img src="https://images.unsplash.com/photo-1518199266791-5375a83190b7?q=80&w=800&auto=format&fit=crop" alt="Meditation" class="thumb-img">
                        <div class="date-badge">
                            <span class="day">20</span>
                            <span class="mon">Mar</span>
                        </div>
                        <div class="card-tags">
                            <span class="tag free">Free</span>
                            <span class="tag live">Live</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-time">🕔 7:00 PM EST · 75 min</div>
                        <h3 class="card-title">Anxiety &amp; the Immigrant Mind</h3>
                        <p class="card-desc">Exploring cultural nuances in anxiety management and identity preservation.</p>
                        <button class="register-btn" @click="showToast('🎉 Registered! Zoom link on its way.')">
                            Register Now
                        </button>
                    </div>
                </div>

                <!-- Workshop 2 -->
                <div class="uw-card-modern rv d2">
                    <div class="card-thumb">
                        <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=800&auto=format&fit=crop" alt="Postpartum" class="thumb-img">
                        <div class="date-badge">
                            <span class="day">27</span>
                            <span class="mon">Mar</span>
                        </div>
                        <div class="card-tags">
                            <span class="tag free">Free</span>
                            <span class="tag spots">8 spots left</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-time">🕔 6:30 PM EST · 60 min</div>
                        <h3 class="card-title">Post-Partum: The Silent Side</h3>
                        <p class="card-desc">A safe space for new mothers to share and find professional support.</p>
                        <button class="register-btn" @click="showToast('🎉 Registered! Zoom link on its way.')">
                            Register Now
                        </button>
                    </div>
                </div>

                <!-- Workshop 3 -->
                <div class="uw-card-modern rv d3">
                    <div class="card-thumb">
                        <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?q=80&w=800&auto=format&fit=crop" alt="Depression" class="thumb-img">
                        <div class="date-badge" style="background: var(--gold-l);">
                            <span class="day">03</span>
                            <span class="mon">Apr</span>
                        </div>
                        <div class="card-tags">
                            <span class="tag free">Free</span>
                            <span class="tag upcoming-tag">Waitlist</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-time">🕔 5:00 PM EST · 60 min</div>
                        <h3 class="card-title">Understanding Depression</h3>
                        <p class="card-desc">Breaking down symptoms, cycles, and evidence-based recovery paths.</p>
                        <button class="register-btn waitlist" @click="showToast('🔔 We\'ll notify you when it opens!')">
                            Notify Me
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* ══════════════════════════════════════
   UPCOMING WORKSHOPS REDESIGN
══════════════════════════════════════ */
.upcoming-sec {
    padding: 100px 48px;
    background: linear-gradient(180deg, var(--cream) 0%, var(--gold-p) 100%);
    position: relative;
    z-index: 1;
}

.uw-inner {
    max-width: 1100px;
    margin: 0 auto;
}

/* Header Styling */
.uw-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 48px;
}

.slabel { font-size: .66rem; letter-spacing: .18em; text-transform: uppercase; color: var(--gold); margin-bottom: 10px; }
.stitle { font-family: 'Fraunces', serif; font-size: clamp(1.8rem, 3.5vw, 2.8rem); font-weight: 300; line-height: 1.1; color: var(--deep); }
.stitle em { font-style: italic; color: var(--rose); }

.view-all-btn {
    background: transparent;
    border: none;
    color: var(--mid);
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 4px;
    border-bottom: 1.5px solid var(--gold-l);
    transition: all 0.3s ease;
}

.view-all-btn:hover {
    color: var(--rose);
    border-color: var(--rose);
    transform: translateX(5px);
}

/* Grid Styling */
.uw-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

/* Modern Card Styling */
.uw-card-modern {
    background: white;
    border-radius: 28px;
    overflow: hidden;
    border: 1px solid var(--gold-p);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 12px 40px rgba(42,36,32,0.04);
}

.uw-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 32px 60px rgba(42,36,32,0.1);
    border-color: var(--rose-l);
}

.card-thumb {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.uw-card-modern:hover .thumb-img {
    transform: scale(1.1);
}

.date-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    background: var(--rose);
    color: white;
    padding: 10px 14px;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    line-height: 1;
    box-shadow: 0 8px 20px rgba(196,154,138,0.3);
}

.date-badge .day { font-family: 'Fraunces', serif; font-size: 1.4rem; font-weight: 300; }
.date-badge .mon { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 2px; }

.card-tags {
    position: absolute;
    bottom: 16px;
    left: 16px;
    display: flex;
    gap: 6px;
}

.tag {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    backdrop-filter: blur(8px);
}

.tag.free { background: rgba(255,255,255,0.9); color: var(--sage); }
.tag.live { background: var(--sky-p); color: var(--sky); }
.tag.spots { background: var(--rose-p); color: var(--rose); }
.tag.upcoming-tag { background: var(--gold-p); color: var(--gold); }

.card-body {
    padding: 24px;
}

.card-time {
    font-size: 0.72rem;
    color: var(--muted);
    margin-bottom: 8px;
    letter-spacing: 0.02em;
}

.card-title {
    font-family: 'Fraunces', serif;
    font-size: 1.25rem;
    color: var(--deep);
    font-weight: 300;
    margin-bottom: 12px;
    line-height: 1.3;
}

.card-desc {
    font-size: 0.85rem;
    color: var(--mid);
    line-height: 1.6;
    font-weight: 300;
    margin-bottom: 24px;
    height: 48px; /* For alignment */
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.register-btn {
    width: 100%;
    padding: 12px;
    background: var(--cream);
    color: var(--deep);
    border: 1.5px solid var(--gold-p);
    border-radius: 14px;
    font-family: 'Jost', sans-serif;
    font-size: 0.82rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.register-btn:hover {
    background: var(--rose);
    color: white;
    border-color: var(--rose);
    box-shadow: 0 8px 24px rgba(196,154,138,0.25);
}

.register-btn.waitlist:hover {
    background: var(--gold);
    border-color: var(--gold);
    box-shadow: 0 8px 24px rgba(201,169,110,0.25);
}

@media(max-width: 1024px) {
    .uw-grid { grid-template-columns: repeat(2, 1fr); }
}

@media(max-width: 768px) {
    .uw-grid { grid-template-columns: 1fr; }
    .upcoming-sec { padding: 80px 24px; }
}
</style>
