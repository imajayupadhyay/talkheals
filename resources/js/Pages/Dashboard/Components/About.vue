<script setup>
import { onMounted } from 'vue';

onMounted(() => {
    const obs = new IntersectionObserver(e => e.forEach((en, i) => {
        if (en.isIntersecting) setTimeout(() => en.target.classList.add('in'), i * 70)
    }), { threshold: .09 });
    document.querySelectorAll('.rv, .rv-l, .rv-r').forEach(el => obs.observe(el));
});
</script>

<template>
    <section class="bio-sec" id="about">
        <div class="bio-inner">
            <!-- PHOTO LEFT -->
            <div class="bio-photo-wrap rv-l">
                <div class="bio-image-card">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=1000&auto=format&fit=crop" alt="Namrata Mohan" class="bio-img">
                    <div class="bio-img-overlay"></div>
                    <!-- Credentials Badge -->
                    <div class="bio-creds-badge">
                        <div class="bio-ico">🌸</div>
                        <div>
                            <div class="bio-badge-name">Namrata Mohan</div>
                            <div class="bio-badge-role">Registered Psychotherapist</div>
                        </div>
                    </div>
                </div>
                <!-- Decorative Elements -->
                <div class="bio-decor-1"></div>
                <div class="bio-decor-2"></div>
            </div>

            <!-- CONTENT RIGHT -->
            <div class="bio-content rv-r">
                <div class="slabel">About the practice</div>
                <h2 class="stitle">A therapist who <em>truly</em> gets it</h2>
                
                <div class="bio-text-group">
                    <p class="bio-p">I'm Namrata. I believe healing is possible for everyone — and this is your space to begin. My practice is grounded in cultural sensitivity, anti-racism, and a deep understanding of the unique challenges many face today.</p>
                    <p class="bio-p">Whether navigating immigration, trauma, or the stigma so many carry silently, I am here to provide a safe, affirming space where your voice is heard and your journey is honored with professional guidance.</p>
                </div>

                <div class="bio-stats">
                    <div class="bs">
                        <div class="bs-num">1,400+</div>
                        <div class="bs-lbl">Sessions</div>
                    </div>
                    <div class="bs">
                        <div class="bs-num">4.97★</div>
                        <div class="bs-lbl">Client Rating</div>
                    </div>
                    <div class="bs">
                        <div class="bs-num">8+</div>
                        <div class="bs-lbl">Specialties</div>
                    </div>
                    <div class="bs">
                        <div class="bs-num">Global</div>
                        <div class="bs-lbl">Online Access</div>
                    </div>
                </div>

                <div class="bio-btns">
                    <button class="btn-rose" @click="window.location='https://talkheals.ca/appointment-request'">Book with Namrata</button>
                    <button class="btn-out" @click="window.open('https://talkheals.ca/about','_blank')">Full Profile →</button>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* ══════════════════════════════════════
   ABOUT SECTION REDESIGN (PHOTO LEFT)
══════════════════════════════════════ */
.bio-sec {
    padding: 100px 48px;
    position: relative;
    z-index: 1;
    background: var(--cream);
}

.bio-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 80px;
    align-items: center;
}

/* Photo Wrap Styling */
.bio-photo-wrap {
    position: relative;
}

.bio-image-card {
    position: relative;
    z-index: 2;
    border-radius: 32px;
    overflow: hidden;
    aspect-ratio: 4/5;
    box-shadow: 0 32px 80px rgba(42,36,32,0.12);
    border: 12px solid white;
}

.bio-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.bio-image-card:hover .bio-img {
    transform: scale(1.05);
}

.bio-img-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent 60%, rgba(42,36,32,0.4));
    pointer-events: none;
}

/* Badge on Image */
.bio-creds-badge {
    position: absolute;
    bottom: 24px;
    left: 24px;
    right: 24px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(12px);
    padding: 16px 20px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 12px 32px rgba(0,0,0,0.08);
}

.bio-ico {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--rose-p);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.bio-badge-name {
    font-family: 'Fraunces', serif;
    font-size: 1.1rem;
    color: var(--deep);
    font-weight: 300;
}

.bio-badge-role {
    font-size: 0.65rem;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-top: 2px;
}

/* Decor Elements */
.bio-decor-1 {
    position: absolute;
    top: -30px;
    left: -30px;
    width: 140px;
    height: 140px;
    background: var(--gold-p);
    border-radius: 50%;
    z-index: 1;
    opacity: 0.6;
}

.bio-decor-2 {
    position: absolute;
    bottom: -40px;
    right: -40px;
    width: 180px;
    height: 180px;
    border: 2px solid var(--rose-l);
    border-radius: 50%;
    z-index: 1;
    opacity: 0.4;
}

/* Content Styling */
.slabel { font-size: .66rem; letter-spacing: .18em; text-transform: uppercase; color: var(--gold); margin-bottom: 12px; }
.stitle { font-family: 'Fraunces', serif; font-size: clamp(2rem, 4vw, 3rem); font-weight: 300; line-height: 1.15; color: var(--deep); margin-bottom: 24px; }
.stitle em { font-style: italic; color: var(--rose); }

.bio-p {
    font-size: 0.95rem;
    color: var(--mid);
    line-height: 1.85;
    font-weight: 300;
    margin-bottom: 20px;
}

.bio-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin: 32px 0;
}

.bs {
    padding: 18px 20px;
    background: white;
    border-radius: 16px;
    border: 1.5px solid var(--gold-p);
    transition: all 0.3s ease;
}

.bs:hover {
    border-color: var(--gold);
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(201,169,110,0.08);
}

.bs-num { font-family: 'Fraunces', serif; font-size: 1.6rem; font-weight: 300; color: var(--gold); }
.bs-lbl { font-size: 0.7rem; color: var(--muted); margin-top: 2px; text-transform: uppercase; letter-spacing: 0.05em; }

.bio-btns {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.btn-rose {
    padding: 14px 32px;
    background: var(--rose);
    color: white;
    border: none;
    border-radius: 40px;
    font-family: 'Jost', sans-serif;
    font-size: 0.88rem;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.btn-rose:hover {
    background: var(--deep);
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(196,154,138,0.3);
}

.btn-out {
    padding: 14px 32px;
    background: transparent;
    border: 1.5px solid var(--rose-l);
    color: var(--rose);
    border-radius: 40px;
    font-family: 'Jost', sans-serif;
    font-size: 0.88rem;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-out:hover {
    background: var(--rose-p);
    border-color: var(--rose);
}

@media(max-width: 900px) {
    .bio-inner {
        grid-template-columns: 1fr;
        gap: 50px;
    }
    .bio-photo-wrap {
        max-width: 450px;
        margin: 0 auto;
    }
    .bio-sec {
        padding: 80px 24px;
    }
}
</style>
