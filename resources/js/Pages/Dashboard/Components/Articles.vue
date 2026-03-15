<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    content: { type: Object, default: () => ({}) },
});

const DEFAULTS = {
    intro_label:           "From Namrata's Desk",
    intro_title:           'Read. Learn.',
    intro_title_highlight: 'Feel less alone.',
    art_badge:             '✦ Articles & Blog',
    art_title:             'Words that',
    art_title_highlight:   'heal',
    art_desc:              "Namrata writes openly about mental health — no jargon, just honesty. Topics you've wondered about, finally spoken aloud.",
    art_p1_icon:           '🌿',
    art_p1_text:           "Why Therapy Isn't Weakness — It's Courage",
    art_p1_tag:            'Mental Health · 5 min read',
    art_p2_icon:           '🌸',
    art_p2_text:           'The Invisible Weight of the Immigrant Experience',
    art_p2_tag:            'Identity · 7 min read',
    art_p3_icon:           '💛',
    art_p3_text:           'What No One Tells You About Post-Partum Anxiety',
    art_p3_tag:            'Post-Partum · 6 min read',
    art_btn:               'Read All Articles →',
    art_btn_link:          'https://talkheals.ca/blog',
    nl_badge:              '✦ Free · Weekly',
    nl_title:              'Your weekly',
    nl_title_highlight:    'moment of calm',
    nl_desc:               "Namrata's newsletter — one gentle insight, one breathing prompt, one reminder that you're doing better than you think. Every week, free.",
    nl_perk_1:             'Mental health insights',
    nl_perk_2:             'Workshop announcements first',
    nl_perk_3:             'Early access to free calls',
    nl_perk_4:             'No spam. Unsubscribe anytime.',
    nl_placeholder:        'your@email.com',
    nl_btn:                'Subscribe Free →',
    nl_note:               'Joined by 2,400+ readers · 100% free forever',
};

const c = computed(() => ({ ...DEFAULTS, ...props.content }));

// Newsletter subscription form
const nlForm = useForm({ email: '' });

const newsletterSuccess = computed(() => usePage().props.flash?.newsletter_success ?? null);

const openLink = (url) => {
    if (url) window.open(url, '_blank');
};

const subscribeNl = () => {
    if (!nlForm.email || !nlForm.email.includes('@')) {
        nlForm.setError('email', 'Please enter a valid email address.');
        return;
    }
    nlForm.post(route('newsletter.subscribe'), {
        preserveScroll: true,
        onSuccess: () => { nlForm.reset(); },
    });
};
</script>

<template>
    <div class="content-band" id="content">
        <div class="content-inner">
            <div class="sess-intro rv" style="margin-bottom:0">
                <div class="sess-intro-label">{{ c.intro_label }}</div>
                <div class="sess-intro-title">{{ c.intro_title }} <em>{{ c.intro_title_highlight }}</em></div>
            </div>
            <div class="content-grid">

                <!-- ARTICLES -->
                <div class="art-card rv rv-l d1">
                    <div class="art-badge">{{ c.art_badge }}</div>
                    <h3 class="art-title">{{ c.art_title }} <em>{{ c.art_title_highlight }}</em></h3>
                    <p class="art-desc">{{ c.art_desc }}</p>
                    <div class="art-previews">
                        <div class="art-prev" @click="openLink(c.art_btn_link)">
                            <div class="art-prev-icon">{{ c.art_p1_icon }}</div>
                            <div><div class="art-prev-text">{{ c.art_p1_text }}</div><div class="art-prev-tag">{{ c.art_p1_tag }}</div></div>
                            <div class="art-prev-arr">→</div>
                        </div>
                        <div class="art-prev" @click="openLink(c.art_btn_link)">
                            <div class="art-prev-icon">{{ c.art_p2_icon }}</div>
                            <div><div class="art-prev-text">{{ c.art_p2_text }}</div><div class="art-prev-tag">{{ c.art_p2_tag }}</div></div>
                            <div class="art-prev-arr">→</div>
                        </div>
                        <div class="art-prev" @click="openLink(c.art_btn_link)">
                            <div class="art-prev-icon">{{ c.art_p3_icon }}</div>
                            <div><div class="art-prev-text">{{ c.art_p3_text }}</div><div class="art-prev-tag">{{ c.art_p3_tag }}</div></div>
                            <div class="art-prev-arr">→</div>
                        </div>
                    </div>
                    <button class="btn-rose" @click="openLink(c.art_btn_link)">{{ c.art_btn }}</button>
                </div>

                <!-- NEWSLETTER -->
                <div class="nl-card rv rv-r d2">
                    <div class="nl-badge">{{ c.nl_badge }}</div>
                    <h3 class="nl-title">{{ c.nl_title }}<br><em>{{ c.nl_title_highlight }}</em></h3>
                    <p class="nl-desc">{{ c.nl_desc }}</p>
                    <ul class="nl-perks">
                        <li><div class="ck">✓</div> {{ c.nl_perk_1 }}</li>
                        <li><div class="ck">✓</div> {{ c.nl_perk_2 }}</li>
                        <li><div class="ck">✓</div> {{ c.nl_perk_3 }}</li>
                        <li><div class="ck">✓</div> {{ c.nl_perk_4 }}</li>
                    </ul>

                    <!-- Success state -->
                    <div v-if="newsletterSuccess" class="nl-success">
                        <div class="nl-success-icon">🌿</div>
                        <div class="nl-success-text">{{ newsletterSuccess }}</div>
                    </div>

                    <!-- Form state -->
                    <template v-else>
                        <div class="nl-input-wrap">
                            <input
                                v-model="nlForm.email"
                                type="email"
                                class="nl-input"
                                :placeholder="c.nl_placeholder"
                                @keyup.enter="subscribeNl"
                            />
                            <div v-if="nlForm.errors.email" class="nl-error">{{ nlForm.errors.email }}</div>
                        </div>
                        <button class="nl-btn" :disabled="nlForm.processing" @click="subscribeNl">
                            {{ nlForm.processing ? 'Subscribing...' : c.nl_btn }}
                        </button>
                    </template>

                    <div class="nl-note">{{ c.nl_note }}</div>
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
.sess-intro-title{ font-family:'Playfair Display',serif; font-size:clamp(1.6rem,3vw,2.4rem); font-weight:400; color:var(--deep) }
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
.art-title{ font-family:'Playfair Display',serif; font-size:1.9rem; font-weight:400; color:var(--deep); line-height:1.15; margin-bottom:12px }
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
.nl-title{ font-family:'Playfair Display',serif; font-size:1.9rem; font-weight:400; color:white; line-height:1.15; margin-bottom:12px; position:relative; z-index:1 }
.nl-title em{ font-style:italic; color:var(--gold-l) }
.nl-desc{ font-size:.84rem; color:rgba(255,255,255,.48); line-height:1.78; font-weight:300; margin-bottom:28px; position:relative; z-index:1 }
.nl-perks{ list-style:none; margin-bottom:28px; position:relative; z-index:1; display:flex; flex-direction:column; gap:6px; padding:0 }
.nl-perks li{ display:flex; align-items:center; gap:8px; color:rgba(255,255,255,.62); font-size:.8rem }
.ck{ width:16px; height:16px; border-radius:50%; background:rgba(201,169,110,.18); border:1px solid rgba(201,169,110,.32); display:flex; align-items:center; justify-content:center; font-size:.5rem; color:var(--gold-l); flex-shrink:0 }

.nl-input-wrap{ position:relative; z-index:1; margin-bottom:10px }
.nl-input{ width:100%; padding:13px 16px; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.12); border-radius:11px; color:white; font-family:'DM Sans',sans-serif; font-size:.86rem; outline:none; transition:border-color .3s,background .3s; box-sizing:border-box }
.nl-input::placeholder{ color:rgba(255,255,255,.28) }
.nl-input:focus{ border-color:var(--gold-l); background:rgba(255,255,255,.1) }
.nl-error{ font-size:.72rem; color:#f99; margin-top:5px; position:relative; z-index:1 }
.nl-btn{ width:100%; padding:14px; background:var(--gold); color:white; border:none; border-radius:11px; font-family:'DM Sans',sans-serif; font-size:.88rem; cursor:pointer; font-weight:500; letter-spacing:.04em; transition:all .3s; position:relative; z-index:1 }
.nl-btn:hover:not(:disabled){ background:white; color:var(--deep); transform:translateY(-2px) }
.nl-btn:disabled{ opacity:.6; cursor:not-allowed }
.nl-note{ font-size:.67rem; color:rgba(255,255,255,.3); text-align:center; margin-top:9px; position:relative; z-index:1 }

.nl-success{ position:relative; z-index:1; margin-bottom:12px; background:rgba(122,158,142,.15); border:1px solid rgba(122,158,142,.3); border-radius:11px; padding:18px; text-align:center }
.nl-success-icon{ font-size:1.5rem; margin-bottom:6px }
.nl-success-text{ font-size:.84rem; color:rgba(255,255,255,.8); line-height:1.5 }

.btn-rose{ padding:12px 28px; background:var(--rose); color:white; border:none; border-radius:40px; font-family:'DM Sans',sans-serif; font-size:.84rem; cursor:pointer; transition:all .32s cubic-bezier(.34,1.56,.64,1); font-weight:500; width: 100%; }
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
