<script setup>
import { onMounted } from 'vue';

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
    <section class="bio-sec" id="about">
        <div class="bio-inner">
            <div class="bio-card rv-l opacity-0 -translate-x-7 transition-all duration-750">
                <div class="bio-ico w-[88px] h-[88px] rounded-full bg-gradient-to-br from-talkheals-rose-l to-talkheals-gold-l flex items-center justify-center text-[2rem] mb-4 border-[3px] border-white shadow-[0_6px_22px_rgba(42,36,32,.1)] mx-auto md:mx-0">🌸</div>
                <div class="bio-name font-serif text-[1.4rem] font-light text-talkheals-deep mb-1 text-center md:text-left">Namrata Mohan</div>
                <div class="bio-role text-[0.68rem] text-talkheals-muted tracking-[0.08em] mb-[18px] text-center md:text-left">RP · Founding Director, Talk Heals Psychotherapy</div>
                <div class="bio-creds flex flex-col gap-2">
                    <div class="bio-cred flex items-center gap-2 text-[0.78rem] text-talkheals-mid px-3 py-2 bg-white/60 rounded-lg">🎓 Registered Psychotherapist (RP)</div>
                    <div class="bio-cred flex items-center gap-2 text-[0.78rem] text-talkheals-mid px-3 py-2 bg-white/60 rounded-lg">🏛️ CRPO — Registered Psychotherapists of Ontario</div>
                    <div class="bio-cred flex items-center gap-2 text-[0.78rem] text-talkheals-mid px-3 py-2 bg-white/60 rounded-lg">🤝 OAMHP Member in Good Standing</div>
                    <div class="bio-cred flex items-center gap-2 text-[0.78rem] text-talkheals-mid px-3 py-2 bg-white/60 rounded-lg">✅ Verified — Psychology Today &amp; GoodTherapy</div>
                    <div class="bio-cred flex items-center gap-2 text-[0.78rem] text-talkheals-mid px-3 py-2 bg-white/60 rounded-lg">🌍 Anti-Racism &amp; Anti-Oppression Framework</div>
                    <div class="bio-cred flex items-center gap-2 text-[0.78rem] text-talkheals-mid px-3 py-2 bg-white/60 rounded-lg">💻 Telehealth · Online · Globally</div>
                </div>
            </div>
            <div class="rv-r opacity-0 translate-x-7 transition-all duration-750">
                <div class="slabel text-[0.66rem] tracking-[0.18em] uppercase text-talkheals-gold mb-2.5">About</div>
                <h2 class="stitle font-serif text-[clamp(1.7rem,3.2vw,2.6rem)] font-light text-talkheals-deep leading-[1.15] mb-[18px]">A therapist who <em>truly</em> gets it</h2>
                <p class="bio-p text-[0.9rem] text-talkheals-mid font-light leading-[1.88] mb-4">I'm Namrata. I believe healing is possible for everyone — and this is your space to begin.</p>
                <p class="bio-p text-[0.9rem] text-talkheals-mid font-light leading-[1.88] mb-4">My practice is grounded in cultural sensitivity, anti-racism, and a deep understanding of immigration, trauma, and the stigma so many carry silently. Whatever brings you here, I'm here to listen.</p>
                <div class="bio-stats grid grid-cols-2 gap-2.5 mb-6">
                    <div class="bs p-[14px_16px] bg-white rounded-xl border border-talkheals-gold-p transition-all duration-300 hover:border-talkheals-gold hover:-translate-y-0.5">
                        <div class="bs-num font-serif text-[1.45rem] font-light text-talkheals-gold">1,400+</div>
                        <div class="bs-lbl text-[0.7rem] text-talkheals-muted mt-0.25">Sessions</div>
                    </div>
                    <div class="bs p-[14px_16px] bg-white rounded-xl border border-talkheals-gold-p transition-all duration-300 hover:border-talkheals-gold hover:-translate-y-0.5">
                        <div class="bs-num font-serif text-[1.45rem] font-light text-talkheals-gold">4.97★</div>
                        <div class="bs-lbl text-[0.7rem] text-talkheals-muted mt-0.25">Client Rating</div>
                    </div>
                    <div class="bs p-[14px_16px] bg-white rounded-xl border border-talkheals-gold-p transition-all duration-300 hover:border-talkheals-gold hover:-translate-y-0.5">
                        <div class="bs-num font-serif text-[1.45rem] font-light text-talkheals-gold">8+</div>
                        <div class="bs-lbl text-[0.7rem] text-talkheals-muted mt-0.25">Specialties</div>
                    </div>
                    <div class="bs p-[14px_16px] bg-white rounded-xl border border-talkheals-gold-p transition-all duration-300 hover:border-talkheals-gold hover:-translate-y-0.5">
                        <div class="bs-num font-serif text-[1.45rem] font-light text-talkheals-gold">Global</div>
                        <div class="bs-lbl text-[0.7rem] text-talkheals-muted mt-0.25">Online Access</div>
                    </div>
                </div>
                <div class="bio-btns flex gap-[11px] flex-wrap">
                    <button class="btn-rose px-[28px] py-3 bg-talkheals-rose text-white border-none rounded-[40px] font-sans text-[0.84rem] cursor-pointer transition-all duration-320 font-medium hover:bg-talkheals-deep hover:-translate-y-0.75 hover:shadow-[0_10px_28px_rgba(196,154,138,0.28)]" @click="window.location='https://talkheals.ca/appointment-request'">Book with Namrata</button>
                    <button class="btn-out px-[28px] py-3 bg-transparent border-[1.5px] border-talkheals-rose-l text-talkheals-rose rounded-[40px] font-sans text-[0.84rem] cursor-pointer transition-all duration-300 hover:bg-talkheals-rose-p hover:border-talkheals-rose" @click="window.open('https://talkheals.ca/about','_blank')">Full Profile →</button>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.bio-sec {
    padding: 80px 48px;
    position: relative;
    z-index: 1;
}

.bio-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1.3fr;
    gap: 60px;
    align-items: center;
}

.bio-card {
    background: linear-gradient(145deg, var(--gold-p), var(--rose-p));
    border-radius: 24px;
    padding: 40px 36px;
    position: relative;
    overflow: hidden;
}

.bio-card::before {
    content: '';
    position: absolute;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .4);
    top: -45px;
    right: -45px;
}

.rv-l.in, .rv-r.in {
    opacity: 1 !important;
    transform: translate(0) !important;
}

@media(max-width: 900px) {
    .bio-inner { grid-template-columns: 1fr; }
}
</style>
