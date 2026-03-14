<script setup>
import { ref, computed, onMounted } from 'vue';
import BookingModal from './BookingModal.vue';

const props = defineProps({
    content: { type: Object, default: () => ({}) },
});

const DEFAULTS = {
    intro_label:           'Choose Your Path',
    intro_title:           'Begin free. Go deeper.',
    intro_title_highlight: 'Heal.',
    free_badge:            'Zero Cost · No Card Needed',
    free_title_top:        'Free',
    free_title_highlight:  '30-Min',
    free_title_bottom:     'Discovery Call',
    free_desc:             'Not sure? Just talk. 30 minutes with Namrata — completely free, no pressure, no commitments.',
    free_perk_1:           'No credit card',
    free_perk_2:           'Video, phone or chat',
    free_perk_3:           'Any time zone, globally',
    free_perk_4:           '100% confidential',
    free_price_label:      'Session Fee',
    free_price:            '$0',
    free_price_unit:       '/ 30 min',
    free_btn:              'Claim My Free Call →',
    paid_badge:            '✦ Individual · Couples · Family · Coaching',
    paid_title_top:        'Book a Full',
    paid_title_highlight:  'Therapy',
    paid_title_bottom:     'Session',
    paid_desc:             'Evidence-based psychotherapy. Choose your session, schedule your time, begin your healing.',
    paid_note:             '🔒 Secure · 📋 Insurance Receipts · 🕊️ Confidential',
    card_1_icon:           '💬',
    card_1_name:           'Individual Therapy',
    card_1_detail:         'Anxiety · Depression · Trauma · Grief · 50 min',
    card_1_price:          '$180',
    card_1_currency:       'CAD',
    card_2_icon:           '💑',
    card_2_name:           'Couples Therapy',
    card_2_detail:         'Marriage · Communication · Conflict · 80 min',
    card_2_price:          '$220',
    card_2_currency:       'CAD',
    card_3_icon:           '👨‍👩‍👧',
    card_3_name:           'Family Therapy',
    card_3_detail:         'Parenting · Dynamics · Conflict · 90 min',
    card_3_price:          '$250',
    card_3_currency:       'CAD',
    card_4_icon:           '🌱',
    card_4_name:           'Coaching',
    card_4_detail:         'Pre-Marital · Parenting · Life · 60 min',
    card_4_price:          '$160',
    card_4_currency:       'CAD',
};

const c = computed(() => ({ ...DEFAULTS, ...props.content }));

const showBookingModal = ref(false);

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('in');
                }, i * 70);
            }
        });
    }, { threshold: 0.09 });

    document.querySelectorAll('.rv, .rv-l, .rv-r').forEach(el => observer.observe(el));
});
</script>

<template>
    <div class="sessions" id="sessions">
        <div class="sess-inner">
            <div class="sess-intro rv">
                <div class="sess-intro-label">{{ c.intro_label }}</div>
                <div class="sess-intro-title">{{ c.intro_title }} <em>{{ c.intro_title_highlight }}</em></div>
            </div>
            <div class="sess-boxes">

                <!-- FREE -->
                <div class="box-free rv rv-l d1">
                    <div class="box-badge-free"><div class="bdot"></div>{{ c.free_badge }}</div>
                    <h2 class="box-title">{{ c.free_title_top }}<br><em>{{ c.free_title_highlight }}</em><br>{{ c.free_title_bottom }}</h2>
                    <p class="box-desc">{{ c.free_desc }}</p>
                    <ul class="free-perks">
                        <li><div class="ck">✓</div> {{ c.free_perk_1 }}</li>
                        <li><div class="ck">✓</div> {{ c.free_perk_2 }}</li>
                        <li><div class="ck">✓</div> {{ c.free_perk_3 }}</li>
                        <li><div class="ck">✓</div> {{ c.free_perk_4 }}</li>
                    </ul>
                    <div class="free-price-block">
                        <div class="price-row">
                            <span class="price-label">{{ c.free_price_label }}</span>
                            <div class="price-big">{{ c.free_price }} <span>{{ c.free_price_unit }}</span></div>
                        </div>
                        <button class="btn-free" @click="showBookingModal = true">{{ c.free_btn }}</button>
                    </div>
                </div>

                <!-- PAID -->
                <div class="box-paid rv rv-r d2">
                    <div class="box-badge-paid">{{ c.paid_badge }}</div>
                    <h2 class="box-paid-title">{{ c.paid_title_top }}<br><em>{{ c.paid_title_highlight }}</em><br>{{ c.paid_title_bottom }}</h2>
                    <p class="box-paid-desc">{{ c.paid_desc }}</p>
                    <div class="sess-cards">
                        <div class="sc" @click="showBookingModal = true">
                            <div class="sc-ico">{{ c.card_1_icon }}</div>
                            <div class="sc-inf"><div class="sc-name">{{ c.card_1_name }}</div><div class="sc-detail">{{ c.card_1_detail }}</div></div>
                            <div class="sc-price"><div class="sc-amt">{{ c.card_1_price }}</div><span class="sc-unit">{{ c.card_1_currency }}</span></div>
                        </div>
                        <div class="sc" @click="showBookingModal = true">
                            <div class="sc-ico">{{ c.card_2_icon }}</div>
                            <div class="sc-inf"><div class="sc-name">{{ c.card_2_name }}</div><div class="sc-detail">{{ c.card_2_detail }}</div></div>
                            <div class="sc-price"><div class="sc-amt">{{ c.card_2_price }}</div><span class="sc-unit">{{ c.card_2_currency }}</span></div>
                        </div>
                        <div class="sc" @click="showBookingModal = true">
                            <div class="sc-ico">{{ c.card_3_icon }}</div>
                            <div class="sc-inf"><div class="sc-name">{{ c.card_3_name }}</div><div class="sc-detail">{{ c.card_3_detail }}</div></div>
                            <div class="sc-price"><div class="sc-amt">{{ c.card_3_price }}</div><span class="sc-unit">{{ c.card_3_currency }}</span></div>
                        </div>
                        <div class="sc" @click="showBookingModal = true">
                            <div class="sc-ico">{{ c.card_4_icon }}</div>
                            <div class="sc-inf"><div class="sc-name">{{ c.card_4_name }}</div><div class="sc-detail">{{ c.card_4_detail }}</div></div>
                            <div class="sc-price"><div class="sc-amt">{{ c.card_4_price }}</div><span class="sc-unit">{{ c.card_4_currency }}</span></div>
                        </div>
                    </div>
                    <button class="btn-paid" @click="showBookingModal = true">Schedule My Session →</button>
                    <div class="paid-note">{{ c.paid_note }}</div>
                </div>

            </div>
        </div>
    </div>

    <div class="wave"><svg viewBox="0 0 1440 52" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,26 C360,52 720,0 1080,26 C1260,40 1380,12 1440,26 L1440,52 L0,52 Z" fill="rgba(201,169,110,.07)"/></svg></div>

    <!-- Free Booking Modal -->
    <BookingModal :open="showBookingModal" @close="showBookingModal = false" />
</template>

<style scoped>
/* ══════════════════════════════════════
   SESSION BOXES (EXACT CSS)
══════════════════════════════════════ */
.sessions{padding:0 48px 100px;position:relative;z-index:1}
.sess-inner{max-width:1100px;margin:0 auto}
.sess-intro{text-align:center;margin-bottom:44px}
.sess-intro-label{font-size:.68rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);margin-bottom:10px}
.sess-intro-title{font-family:'Fraunces',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:300;color:var(--deep)}
.sess-intro-title em{font-style:italic;color:var(--rose)}

.sess-boxes{display:grid;grid-template-columns:1fr 1fr;gap:22px}

/* ── FREE BOX ── */
.box-free{
  background:linear-gradient(158deg,#2a2420 0%,#3a2d28 52%,#283428 100%);
  border-radius:28px;padding:48px 42px;
  position:relative;overflow:hidden;
  text-align: left;
}
.box-free::before{content:'';position:absolute;width:280px;height:280px;border-radius:50%;background:rgba(201,169,110,.09);top:-90px;right:-70px;animation:orbDrift 20s ease-in-out infinite}
.box-free::after{content:'';position:absolute;width:160px;height:160px;border-radius:50%;background:rgba(122,158,142,.06);bottom:-40px;left:20%}

.box-badge-free{display:inline-flex;align-items:center;gap:7px;padding:5px 14px;border-radius:40px;font-size:.67rem;letter-spacing:.12em;text-transform:uppercase;border:1px solid rgba(201,169,110,.3);color:var(--gold-l);background:rgba(201,169,110,.08);margin-bottom:20px;position:relative;z-index:1}
.bdot{width:5px;height:5px;border-radius:50%;background:var(--gold-l);animation:pulse 2.2s infinite}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(1.6)}}

.box-title{font-family:'Fraunces',serif;font-size:2.4rem;font-weight:200;color:white;line-height:1.08;margin-bottom:14px;position:relative;z-index:1}
.box-title em{font-style:italic;color:var(--gold-l)}
.box-desc{font-size:.85rem;color:rgba(255,255,255,.48);line-height:1.8;font-weight:300;margin-bottom:30px;position:relative;z-index:1}

.free-perks{list-style:none;margin-bottom:32px;position:relative;z-index:1;display:flex;flex-direction:column;gap:7px; padding: 0;}
.free-perks li{display:flex;align-items:center;gap:9px;color:rgba(255,255,255,.65);font-size:.82rem}
.ck{width:16px;height:16px;border-radius:50%;background:rgba(201,169,110,.18);border:1px solid rgba(201,169,110,.32);display:flex;align-items:center;justify-content:center;font-size:.5rem;color:var(--gold-l);flex-shrink:0}

.free-price-block{background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.09);border-radius:16px;padding:24px 26px;position:relative;z-index:1;display:flex;flex-direction:column;gap:16px}
.price-row{display:flex;align-items:baseline;justify-content:space-between}
.price-label{font-size:.72rem;color:rgba(255,255,255,.38);letter-spacing:.06em}
.price-big{font-family:'Fraunces',serif;font-size:2.2rem;font-weight:200;color:white}
.price-big span{font-size:.8rem;font-family:'Jost',sans-serif;color:var(--gold-l);margin-left:5px}
.btn-free{width:100%;padding:15px;background:var(--gold);color:white;border:none;border-radius:11px;font-family:'Jost',sans-serif;font-size:.88rem;cursor:pointer;font-weight:500;letter-spacing:.04em;transition:all .32s cubic-bezier(.34,1.56,.64,1)}
.btn-free:hover{background:white;color:var(--deep);transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,0,0,.18)}

/* ── PAID BOX ── */
.box-paid{
  background:white;border-radius:28px;
  padding:48px 42px;border:1.5px solid var(--gold-l);
  position:relative;overflow:hidden;
  text-align: left;
}
.box-paid::before{content:'';position:absolute;width:260px;height:260px;border-radius:50%;background:rgba(201,169,110,.045);top:-80px;left:-80px}
.box-badge-paid{display:inline-flex;align-items:center;gap:7px;padding:5px 14px;border-radius:40px;font-size:.67rem;letter-spacing:.12em;text-transform:uppercase;border:1px solid var(--rose-l);color:var(--rose);background:var(--rose-p);margin-bottom:20px;position:relative;z-index:1}

.box-paid-title{font-family:'Fraunces',serif;font-size:2.4rem;font-weight:300;color:var(--deep);line-height:1.08;margin-bottom:14px;position:relative;z-index:1}
.box-paid-title em{font-style:italic;color:var(--rose)}
.box-paid-desc{font-size:.85rem;color:var(--muted);line-height:1.8;font-weight:300;margin-bottom:28px;position:relative;z-index:1}

.sess-cards{display:flex;flex-direction:column;gap:10px;position:relative;z-index:1}
.sc{
  display:flex;align-items:center;gap:14px;
  padding:15px 18px;
  border:1.5px solid var(--gold-p);border-radius:14px;
  background:var(--cream);cursor:pointer;
  transition:all .32s cubic-bezier(.34,1.56,.64,1);
}
.sc:hover{border-color:var(--rose);background:var(--rose-p);transform:translateX(5px)}
.sc-ico{font-size:1.45rem;flex-shrink:0}
.sc-inf{flex:1}
.sc-name{font-size:.9rem;font-weight:500;color:var(--deep);margin-bottom:2px}
.sc-detail{font-size:.72rem;color:var(--muted)}
.sc-price{text-align:right;flex-shrink:0}
.sc-amt{font-family:'Fraunces',serif;font-size:1.28rem;font-weight:300;color:var(--deep)}
.sc-unit{font-size:.66rem;color:var(--muted);display:block;margin-top:1px}

.btn-paid{width:100%;margin-top:18px;padding:15px;background:var(--rose);color:white;border:none;border-radius:11px;font-family:'Jost',sans-serif;font-size:.88rem;cursor:pointer;font-weight:500;letter-spacing:.04em;transition:all .32s cubic-bezier(.34,1.56,.64,1);position:relative;z-index:1}
.btn-paid:hover{background:var(--deep);transform:translateY(-2px);box-shadow:0 12px 30px rgba(196,154,138,.32)}
.paid-note{font-size:.7rem;color:var(--muted);text-align:center;margin-top:10px;position:relative;z-index:1}

/* wave */
.wave{width:100%;height:52px;overflow:hidden;position:relative;z-index:1}
.wave svg{display:block;width:100%;height:52px}

@keyframes orbDrift {
    0%, 100% { transform: translate(0, 0) }
    34% { transform: translate(30px, -40px) }
    67% { transform: translate(-18px, 22px) }
}

@media(max-width:900px){
  .sessions{padding:0 20px 80px}
  .sess-boxes{grid-template-columns:1fr}
}
</style>
