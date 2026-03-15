<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer>
        <div class="fg">
            <div>
                <div class="fl">Talk<span>Heals</span></div>
                <div class="fl-s">Psychotherapy · Accessible Globally</div>
                <div class="fl-d">A safe, affirming, culturally sensitive space for your mental wellness. Founded by Namrata Mohan, RP.</div>
                <div class="fl-c">📞 <a href="tel:+16477053013">+1 647-705-3013</a></div>
                <div class="fl-c">✉️ <a href="mailto:talkhealspsychotherapy@gmail.com">talkhealspsychotherapy@gmail.com</a></div>
            </div>
            <div class="fc">
                <h5>Services</h5>
                <a href="https://talkheals.ca/counseling-for-anxiety">Anxiety</a>
                <a href="https://talkheals.ca/therapy-for-depression">Depression</a>
                <a href="https://talkheals.ca/couples-therapy">Couples</a>
                <a href="https://talkheals.ca/family-therapy">Family</a>
                <a href="https://talkheals.ca/counseling-for-new-mothers-post-partum">Post-Partum</a>
                <a href="https://talkheals.ca/pre-marital-counseling">Pre-Marital</a>
                <a href="https://talkheals.ca/clinical-supervision">Supervision</a>
            </div>
            <div class="fc">
                <h5>Explore</h5>
                <a href="https://talkheals.ca/blog">Blog</a>
                <a href="https://talkheals.ca/articles">Articles</a>
                <a href="https://talkheals.ca/events">Events</a>
                <a href="https://talkheals.ca/faqs">FAQs</a>
                <a href="https://talkheals.ca/fees-insurance">Fees &amp; Insurance</a>
                <a href="https://talkheals.ca/contact">Contact</a>
            </div>
            <div class="fc">
                <h5>Connect</h5>
                <a href="https://www.psychologytoday.com/ca/therapists/namrta-mohan-brampton-on/767045">Psychology Today</a>
                <a href="https://www.goodtherapy.org/therapists/profile/namrta-mohan-20201010">GoodTherapy</a>
                <a href="https://www.therapytribe.com/listing/namrta-mohan-registered-psychotherapist-brampton-ontario-canada-l6p-0b7/">TherapyTribe</a>
            </div>
        </div>
        <div class="fb">
            <span>© 2026 Talk Heals Psychotherapy · Namrata Mohan, RP · CRPO</span>
            <span>⚠️ Not a crisis line. Crisis? Call 911 or Helpline 988</span>
        </div>
    </footer>
</template>

<style scoped>
/* ══════════════════════════════════════
   FOOTER (EXACT CSS)
══════════════════════════════════════ */
footer{background:var(--deep);padding:60px 48px 26px;position:relative;z-index:1}
.fg{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:34px;max-width:1100px;margin:0 auto;padding-bottom:40px;border-bottom:1px solid rgba(255,255,255,.07)}
.fl{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:400;color:white}
.fl span{color:var(--gold);font-style:italic}
.fl-s{font-size:.58rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.22);margin-bottom:12px;margin-top:3px}
.fl-d{font-size:.79rem;color:rgba(255,255,255,.36);line-height:1.72;max-width:250px;font-weight:300;margin-bottom:16px}
.fl-c{font-size:.76rem;color:rgba(255,255,255,.4);margin-bottom:7px;display:flex;align-items:center;gap:6px}
.fl-c a{color:inherit;text-decoration:none}
.fc h5{font-size:.65rem;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.28);margin-bottom:14px}
.fc a{display:block;text-decoration:none;color:rgba(255,255,255,.36);font-size:.79rem;margin-bottom:8px;transition:color .2s}
.fc a:hover{color:var(--gold-l)}
.fb{max-width:1100px;margin:20px auto 0;display:flex;justify-content:space-between;font-size:.7rem;color:rgba(255,255,255,.17)}

@media(max-width:900px){
  footer{padding:48px 20px 22px}.fg{grid-template-columns:1fr 1fr}
}
</style>
