<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import Header from './Components/Header.vue';
import Footer from './Components/Footer.vue';

const props = defineProps({
    bookings: Array,
    stats:    Object,
});

const user       = usePage().props.auth.user;
const activeTab  = ref('upcoming');
const cancelForm = useForm({ reason: '' });
const cancelTarget = ref(null);

// ── Filters ───────────────────────────────────────────────────────────────────
const today = new Date().toISOString().split('T')[0];

const filteredBookings = computed(() => {
    if (activeTab.value === 'upcoming') {
        return props.bookings.filter(b =>
            ['pending', 'confirmed'].includes(b.status) &&
            String(b.booking_date).substring(0, 10) >= today
        );
    }
    if (activeTab.value === 'past') {
        return props.bookings.filter(b =>
            b.status === 'completed' ||
            b.status === 'cancelled' ||
            String(b.booking_date).substring(0, 10) < today
        );
    }
    return props.bookings;
});

// ── Cancel ────────────────────────────────────────────────────────────────────
const openCancel = (booking) => { cancelTarget.value = booking; };
const closeCancel = () => { cancelTarget.value = null; cancelForm.reset(); };

const submitCancel = () => {
    cancelForm.patch(route('client.appointments.cancel', cancelTarget.value.id), {
        preserveScroll: true,
        onSuccess: closeCancel,
    });
};

// ── Formatting ────────────────────────────────────────────────────────────────
const formatDate = (d) => {
    const ds = String(d).substring(0, 10);
    return new Date(ds + 'T00:00:00').toLocaleDateString('en-CA', {
        weekday: 'long', month: 'long', day: 'numeric', year: 'numeric',
    });
};

const formatDateShort = (d) => {
    const ds = String(d).substring(0, 10);
    return new Date(ds + 'T00:00:00').toLocaleDateString('en-CA', {
        month: 'short', day: 'numeric',
    });
};

const formatDay = (d) => {
    const ds = String(d).substring(0, 10);
    return new Date(ds + 'T00:00:00').toLocaleDateString('en-CA', { weekday: 'short' });
};

const formatYear = (d) => String(d).substring(0, 4);

const formatTime = (t) => {
    if (!t) return '';
    const [h, m] = t.substring(0, 5).split(':');
    const dt = new Date(); dt.setHours(+h, +m);
    return dt.toLocaleTimeString('en-CA', { hour: 'numeric', minute: '2-digit' });
};

const isUpcoming = (b) =>
    ['pending','confirmed'].includes(b.status) &&
    String(b.booking_date).substring(0, 10) >= today;

// ── Config ────────────────────────────────────────────────────────────────────
const statusConfig = {
    pending:   { label: 'Pending Confirmation', dot: 'bg-talkheals-gold',  text: 'text-talkheals-gold',  bg: 'bg-talkheals-gold/8  border-talkheals-gold/20'  },
    confirmed: { label: 'Confirmed',            dot: 'bg-talkheals-sage',  text: 'text-talkheals-sage',  bg: 'bg-talkheals-sage/8  border-talkheals-sage/20'  },
    completed: { label: 'Completed',            dot: 'bg-talkheals-sky',   text: 'text-talkheals-sky',   bg: 'bg-talkheals-sky/8   border-talkheals-sky/20'   },
    cancelled: { label: 'Cancelled',            dot: 'bg-talkheals-muted', text: 'text-talkheals-muted', bg: 'bg-talkheals-muted/8 border-talkheals-muted/20' },
};

const typeConfig = {
    free: { icon: '🎁', label: 'Free',  cls: 'text-talkheals-sage  bg-talkheals-sage/8  border-talkheals-sage/20'  },
    paid: { icon: '💳', label: 'Paid',  cls: 'text-talkheals-gold  bg-talkheals-gold/8  border-talkheals-gold/20'  },
};

const tabs = [
    { key: 'upcoming', label: 'Upcoming', count: props.stats.upcoming   },
    { key: 'past',     label: 'Past',     count: null                   },
    { key: 'all',      label: 'All',      count: props.stats.total      },
];

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('in'), i * 60);
            }
        });
    }, { threshold: 0.05 });

    document.querySelectorAll('.rv, .rv-l, .rv-r').forEach(el => observer.observe(el));
});
</script>

<template>
    <Head title="My Appointments — TalkHeals" />

    <div class="relative min-h-screen bg-talkheals-cream overflow-x-hidden">
        <Header />

        <!-- Orbs -->
        <div class="orbs">
            <div class="orb o1"></div>
            <div class="orb o2"></div>
            <div class="orb o3"></div>
        </div>

        <main class="relative z-10 pt-32 pb-24 px-6 lg:px-12">
            <div class="max-w-[960px] mx-auto">

                <!-- ── Page header ── -->
                <div class="mb-12 rv">
                    <div class="slabel">My Account</div>
                    <h1 class="stitle">My <em>Appointments</em></h1>
                    <p class="ssub mt-2">View and manage your upcoming and past sessions with Namrata.</p>
                </div>

                <!-- ── Stats row ── -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10 rv">
                    <div class="appt-stat">
                        <div class="appt-stat-val">{{ stats.total }}</div>
                        <div class="appt-stat-lbl">Total Bookings</div>
                    </div>
                    <div class="appt-stat">
                        <div class="appt-stat-val" style="color:var(--gold)">{{ stats.upcoming }}</div>
                        <div class="appt-stat-lbl">Upcoming</div>
                    </div>
                    <div class="appt-stat">
                        <div class="appt-stat-val" style="color:var(--sage)">{{ stats.completed }}</div>
                        <div class="appt-stat-lbl">Completed</div>
                    </div>
                    <div class="appt-stat">
                        <div class="appt-stat-val" style="color:var(--muted)">{{ stats.cancelled }}</div>
                        <div class="appt-stat-lbl">Cancelled</div>
                    </div>
                </div>

                <!-- ── Tabs ── -->
                <div class="flex items-center gap-2 mb-8 rv">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-medium transition-all duration-200"
                        :class="activeTab === tab.key
                            ? 'bg-talkheals-deep text-white shadow-lg'
                            : 'bg-white border border-talkheals-gold-p text-talkheals-mid hover:border-talkheals-gold hover:text-talkheals-deep'"
                    >
                        {{ tab.label }}
                        <span
                            v-if="tab.count !== null"
                            class="text-[0.65rem] font-bold px-1.5 py-0.5 rounded-full"
                            :class="activeTab === tab.key ? 'bg-white/20' : 'bg-talkheals-gold-p text-talkheals-muted'"
                        >{{ tab.count }}</span>
                    </button>
                </div>

                <!-- ── Empty state ── -->
                <div v-if="!filteredBookings.length" class="text-center py-20 rv">
                    <div class="text-5xl mb-5 opacity-30">📅</div>
                    <div class="font-serif text-2xl font-light text-talkheals-deep mb-3">
                        {{ activeTab === 'upcoming' ? 'No upcoming sessions' : 'No bookings here yet' }}
                    </div>
                    <p class="text-talkheals-muted text-sm max-w-sm mx-auto leading-relaxed mb-8">
                        {{ activeTab === 'upcoming'
                            ? 'Ready to take the first step? Book your free 30-minute discovery call today.'
                            : 'Your booking history will appear here once you start scheduling sessions.' }}
                    </p>
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 px-7 py-3 bg-talkheals-gold text-white font-medium rounded-2xl hover:bg-talkheals-deep transition-all duration-300 hover:-translate-y-0.5 shadow-lg shadow-talkheals-gold/20 text-sm"
                    >
                        Book a Free Call →
                    </Link>
                </div>

                <!-- ── Bookings list ── -->
                <div v-else class="space-y-4">
                    <div
                        v-for="booking in filteredBookings"
                        :key="booking.id"
                        class="appt-card rv"
                        :class="isUpcoming(booking) ? 'appt-card--active' : 'appt-card--past'"
                    >
                        <!-- Date column -->
                        <div class="appt-date-col">
                            <div class="appt-date-day">{{ formatDay(booking.booking_date) }}</div>
                            <div class="appt-date-num">{{ formatDateShort(booking.booking_date) }}</div>
                            <div class="appt-date-year">{{ formatYear(booking.booking_date) }}</div>
                        </div>

                        <!-- Divider -->
                        <div class="w-px self-stretch mx-2" :class="isUpcoming(booking) ? 'bg-talkheals-gold/20' : 'bg-talkheals-gold-p'"></div>

                        <!-- Main content -->
                        <div class="flex-1 min-w-0 py-1">
                            <!-- Top row: session name + badges -->
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <div class="font-serif text-lg font-light text-talkheals-deep leading-tight">
                                    {{ booking.session_type || 'Session' }}
                                </div>
                                <!-- Type badge -->
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[0.6rem] font-bold uppercase tracking-widest border"
                                    :class="typeConfig[booking.type]?.cls"
                                >
                                    {{ typeConfig[booking.type]?.icon }}
                                    {{ typeConfig[booking.type]?.label }}
                                </span>
                            </div>

                            <!-- Time -->
                            <div class="flex items-center gap-1.5 text-sm text-talkheals-mid mb-2">
                                <span class="text-xs opacity-60">⏰</span>
                                {{ formatTime(booking.start_time) }} – {{ formatTime(booking.end_time) }}
                                <span class="text-talkheals-muted text-xs">·</span>
                                <span class="text-talkheals-muted text-xs">{{ formatDate(booking.booking_date) }}</span>
                            </div>

                            <!-- Client note -->
                            <div v-if="booking.client_notes" class="text-talkheals-muted text-xs italic mb-2 truncate">
                                "{{ booking.client_notes }}"
                            </div>

                            <!-- Cancellation reason -->
                            <div v-if="booking.status === 'cancelled' && booking.cancellation_reason" class="text-talkheals-muted text-xs mb-2">
                                Reason: {{ booking.cancellation_reason }}
                            </div>
                        </div>

                        <!-- Right column: status + actions -->
                        <div class="flex flex-col items-end justify-between gap-3 flex-shrink-0 pl-4">
                            <!-- Status badge -->
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[0.6rem] font-bold uppercase tracking-widest border"
                                :class="statusConfig[booking.status]?.bg"
                            >
                                <span class="w-1.5 h-1.5 rounded-full" :class="statusConfig[booking.status]?.dot"></span>
                                <span :class="statusConfig[booking.status]?.text">{{ statusConfig[booking.status]?.label }}</span>
                            </span>

                            <!-- Cancel button -->
                            <button
                                v-if="isUpcoming(booking)"
                                @click="openCancel(booking)"
                                class="text-[0.65rem] text-talkheals-muted hover:text-red-500 font-medium transition-colors underline underline-offset-2"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <Footer />
    </div>

    <!-- ── Cancel modal ── -->
    <Teleport to="body">
        <transition name="fade">
            <div
                v-if="cancelTarget"
                class="fixed inset-0 z-[200] flex items-center justify-center p-6"
                style="background:rgba(42,36,32,.55);backdrop-filter:blur(6px)"
                @click.self="closeCancel"
            >
                <div class="bg-white rounded-[28px] p-8 w-full max-w-md border border-talkheals-gold-p shadow-2xl">
                    <h3 class="font-serif text-2xl font-light text-talkheals-deep mb-2">Cancel Session</h3>
                    <p class="text-talkheals-muted text-sm mb-1">
                        Are you sure you want to cancel your
                        <strong class="text-talkheals-deep">{{ cancelTarget?.session_type }}</strong>
                        on {{ formatDate(cancelTarget?.booking_date) }}?
                    </p>
                    <p class="text-talkheals-muted text-xs mb-6">This cannot be undone.</p>

                    <textarea
                        v-model="cancelForm.reason"
                        placeholder="Reason for cancelling (optional)…"
                        rows="3"
                        class="w-full bg-talkheals-cream border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition-all resize-none placeholder:text-talkheals-muted/50 mb-5"
                    ></textarea>

                    <div class="flex gap-3">
                        <button
                            @click="closeCancel"
                            class="flex-1 py-3 bg-talkheals-cream border border-talkheals-gold-p rounded-xl text-sm font-bold text-talkheals-mid hover:border-talkheals-gold transition-all"
                        >Keep Session</button>
                        <button
                            @click="submitCancel"
                            :disabled="cancelForm.processing"
                            class="flex-1 py-3 bg-red-500 text-white rounded-xl text-sm font-bold hover:bg-red-600 transition-all disabled:opacity-50"
                        >
                            {{ cancelForm.processing ? 'Cancelling…' : 'Cancel Session' }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
/* ── Reuse profile page label/title pattern ── */
.slabel {
    font-size: .68rem;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 10px;
}
.stitle {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 400;
    color: var(--deep);
    line-height: 1.1;
}
.stitle em { font-style: italic; color: var(--rose); }
.ssub {
    font-size: .9rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.8;
}

/* ── Stat cards ── */
.appt-stat {
    background: white;
    border: 1px solid var(--gold-p);
    border-radius: 20px;
    padding: 20px;
    text-align: center;
}
.appt-stat-val {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 400;
    color: var(--deep);
    line-height: 1;
}
.appt-stat-lbl {
    font-size: .6rem;
    text-transform: uppercase;
    letter-spacing: .15em;
    color: var(--muted);
    font-weight: 700;
    margin-top: 6px;
}

/* ── Booking card ── */
.appt-card {
    display: flex;
    align-items: stretch;
    gap: 20px;
    padding: 24px 28px;
    border-radius: 24px;
    border: 1.5px solid;
    transition: all .3s ease;
}
.appt-card:hover { transform: translateY(-2px); }

.appt-card--active {
    background: white;
    border-color: var(--gold-l);
    box-shadow: 0 4px 20px rgba(201,169,110,.08);
}
.appt-card--active:hover {
    border-color: var(--gold);
    box-shadow: 0 8px 32px rgba(201,169,110,.14);
}

.appt-card--past {
    background: rgba(250,247,242,.6);
    border-color: var(--gold-p);
}
.appt-card--past:hover {
    border-color: var(--gold-l);
}

/* ── Date column ── */
.appt-date-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 56px;
    text-align: center;
}
.appt-date-day {
    font-size: .58rem;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--gold);
    font-weight: 700;
}
.appt-date-num {
    font-family: 'Playfair Display', serif;
    font-size: 1.1rem;
    font-weight: 400;
    color: var(--deep);
    line-height: 1.2;
    white-space: nowrap;
}
.appt-date-year {
    font-size: .6rem;
    color: var(--muted);
    margin-top: 2px;
}

/* ── Orbs (same as dashboard) ── */
.orbs { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
.orb  { position: absolute; border-radius: 50%; filter: blur(85px); animation: orbDrift ease-in-out infinite; }
.o1 { width: 650px; height: 650px; background: rgba(201,169,110,.08); top: -180px; left: -180px; animation-duration: 26s; }
.o2 { width: 480px; height: 480px; background: rgba(196,154,138,.09); top: 38%; right: -80px; animation-duration: 21s; animation-delay: -8s; }
.o3 { width: 560px; height: 560px; background: rgba(122,158,142,.06); bottom: -80px; left: 18%; animation-duration: 30s; animation-delay: -14s; }

@keyframes orbDrift {
    0%, 100% { transform: translate(0, 0) }
    34%       { transform: translate(30px, -40px) }
    67%       { transform: translate(-18px, 22px) }
}

/* soft bg utilities */
.bg-talkheals-gold\/8  { background-color: rgb(201 169 110 / .08); }
.bg-talkheals-sage\/8  { background-color: rgb(122 158 142 / .08); }
.bg-talkheals-sky\/8   { background-color: rgb(139 176 196 / .08); }
.bg-talkheals-muted\/8 { background-color: rgb(154 136 128 / .08); }

/* Fade transition */
.fade-enter-active, .fade-leave-active { transition: opacity .25s ease; }
.fade-enter-from,   .fade-leave-to     { opacity: 0; }
</style>
