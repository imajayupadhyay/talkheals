<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Sidebar from '../Components/Sidebar.vue';
import Topbar from '../Components/Topbar.vue';

const props = defineProps({
    auth:     Object,
    bookings: Object,   // paginated
    stats:    Object,
    filters:  Object,
});

// ── Status update ─────────────────────────────────────────────────────────────
const statusForm  = useForm({ status: '', cancellation_reason: '' });
const cancelModal = ref({ open: false, booking: null });

const updateStatus = (booking, status) => {
    if (status === 'cancelled') {
        cancelModal.value = { open: true, booking };
        return;
    }
    statusForm.status = status;
    statusForm.patch(route('admin.bookings.status', booking.id), {
        preserveScroll: true,
        onSuccess: () => statusForm.reset(),
    });
};

const confirmCancel = () => {
    statusForm.status = 'cancelled';
    statusForm.patch(route('admin.bookings.status', cancelModal.value.booking.id), {
        preserveScroll: true,
        onSuccess: () => {
            statusForm.reset();
            cancelModal.value = { open: false, booking: null };
        },
    });
};

// ── Filters ───────────────────────────────────────────────────────────────────
const activeStatus = ref(props.filters?.status || 'all');
const activeType   = ref(props.filters?.type   || 'all');

const applyFilter = () => {
    router.get(route('admin.bookings'), {
        status: activeStatus.value !== 'all' ? activeStatus.value : undefined,
        type:   activeType.value   !== 'all' ? activeType.value   : undefined,
    }, { preserveScroll: true, replace: true });
};

// ── Formatting ────────────────────────────────────────────────────────────────
const formatDate = (d) => new Date(String(d).substring(0, 10) + 'T00:00:00').toLocaleDateString('en-CA', {
    weekday: 'short', month: 'short', day: 'numeric', year: 'numeric',
});

const formatTime = (t) => {
    if (!t) return '';
    const [h, m] = t.split(':');
    const date = new Date();
    date.setHours(+h, +m);
    return date.toLocaleTimeString('en-CA', { hour: 'numeric', minute: '2-digit' });
};

const statusConfig = {
    pending:   { label: 'Pending',   cls: 'bg-talkheals-gold/10 text-talkheals-gold border-talkheals-gold/20' },
    confirmed: { label: 'Confirmed', cls: 'bg-talkheals-sage/10 text-talkheals-sage border-talkheals-sage/20' },
    completed: { label: 'Completed', cls: 'bg-talkheals-sky/10  text-talkheals-sky  border-talkheals-sky/20'  },
    cancelled: { label: 'Cancelled', cls: 'bg-red-50 text-red-400 border-red-100'                              },
};

const typeConfig = {
    free: { label: 'Free',  cls: 'bg-talkheals-sage/10 text-talkheals-sage border-talkheals-sage/20' },
    paid: { label: 'Paid',  cls: 'bg-talkheals-gold/10 text-talkheals-gold border-talkheals-gold/20' },
};

const statusTabs   = ['all', 'pending', 'confirmed', 'completed', 'cancelled'];
const statusCounts = computed(() => ({
    all:       props.stats.total,
    pending:   props.stats.pending,
    confirmed: props.stats.confirmed,
    completed: 0,
    cancelled: 0,
}));
</script>

<template>
    <Head title="Bookings — TalkHeals Admin" />

    <div class="min-h-screen bg-talkheals-cream flex text-talkheals-deep font-sans">
        <Sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <Topbar />

            <main class="flex-1 p-10 overflow-y-auto">
                <div class="max-w-7xl mx-auto">

                    <!-- ── Header ── -->
                    <div class="mb-10">
                        <h1 class="font-serif text-4xl font-light tracking-tight text-talkheals-deep">
                            Bookings <em class="text-talkheals-gold not-italic italic">Overview</em>
                        </h1>
                        <p class="text-talkheals-mid text-lg font-light mt-2">
                            Manage all client session bookings.
                        </p>
                    </div>

                    <!-- ── Stats ── -->
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
                        <div class="stat-mini">
                            <div class="stat-mini-val">{{ stats.total }}</div>
                            <div class="stat-mini-lbl">Total</div>
                        </div>
                        <div class="stat-mini">
                            <div class="stat-mini-val text-talkheals-gold">{{ stats.pending }}</div>
                            <div class="stat-mini-lbl">Pending</div>
                        </div>
                        <div class="stat-mini">
                            <div class="stat-mini-val text-talkheals-sage">{{ stats.confirmed }}</div>
                            <div class="stat-mini-lbl">Confirmed</div>
                        </div>
                        <div class="stat-mini">
                            <div class="stat-mini-val text-talkheals-sky">{{ stats.today }}</div>
                            <div class="stat-mini-lbl">Today</div>
                        </div>
                        <div class="stat-mini">
                            <div class="stat-mini-val text-talkheals-sage">{{ stats.free }}</div>
                            <div class="stat-mini-lbl">Free Calls</div>
                        </div>
                        <div class="stat-mini">
                            <div class="stat-mini-val text-talkheals-gold">{{ stats.paid }}</div>
                            <div class="stat-mini-lbl">Paid Sessions</div>
                        </div>
                    </div>

                    <!-- ── Filters ── -->
                    <div class="bg-white border border-talkheals-gold-p rounded-[28px] p-6 mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-4 shadow-sm shadow-talkheals-gold/5">
                        <!-- Status tabs -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mr-1">Status</span>
                            <button
                                v-for="tab in statusTabs"
                                :key="tab"
                                @click="activeStatus = tab; applyFilter()"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 capitalize"
                                :class="activeStatus === tab
                                    ? 'bg-talkheals-gold text-white shadow-md shadow-talkheals-gold/20'
                                    : 'bg-talkheals-cream text-talkheals-muted hover:text-talkheals-deep border border-talkheals-gold-p'"
                            >
                                {{ tab }}
                                <span v-if="statusCounts[tab]" class="ml-1 opacity-70">{{ statusCounts[tab] }}</span>
                            </button>
                        </div>

                        <div class="h-5 w-px bg-talkheals-gold-p hidden sm:block"></div>

                        <!-- Type filter -->
                        <div class="flex items-center gap-2">
                            <span class="text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mr-1">Type</span>
                            <button
                                v-for="t in ['all', 'free', 'paid']"
                                :key="t"
                                @click="activeType = t; applyFilter()"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 capitalize"
                                :class="activeType === t
                                    ? 'bg-talkheals-deep text-white'
                                    : 'bg-talkheals-cream text-talkheals-muted hover:text-talkheals-deep border border-talkheals-gold-p'"
                            >
                                {{ t === 'all' ? 'All types' : t }}
                            </button>
                        </div>
                    </div>

                    <!-- ── Bookings Table ── -->
                    <div class="bg-white border border-talkheals-gold-p rounded-[28px] shadow-sm shadow-talkheals-gold/5 overflow-hidden">

                        <!-- Empty state -->
                        <div v-if="!bookings.data.length" class="flex flex-col items-center justify-center py-20 text-center">
                            <div class="text-5xl mb-4 opacity-30">📋</div>
                            <div class="font-serif text-xl text-talkheals-deep font-light mb-2">No bookings found</div>
                            <div class="text-talkheals-muted text-sm">Try adjusting your filters above.</div>
                        </div>

                        <!-- Table -->
                        <div v-else class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-talkheals-gold-p">
                                        <th class="th-cell text-left">Client</th>
                                        <th class="th-cell text-left">Type</th>
                                        <th class="th-cell text-left">Session</th>
                                        <th class="th-cell text-left">Date & Time</th>
                                        <th class="th-cell text-left">Status</th>
                                        <th class="th-cell text-left">Notes</th>
                                        <th class="th-cell text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-talkheals-gold-p/60">
                                    <tr
                                        v-for="booking in bookings.data"
                                        :key="booking.id"
                                        class="hover:bg-talkheals-cream/40 transition-colors"
                                    >
                                        <!-- Client -->
                                        <td class="td-cell">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-talkheals-gold/20 to-talkheals-rose/20 flex items-center justify-center text-sm font-bold text-talkheals-deep border border-talkheals-gold-p flex-shrink-0">
                                                    {{ booking.client?.name?.charAt(0) ?? '?' }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-talkheals-deep text-sm">{{ booking.client?.name ?? '—' }}</div>
                                                    <div class="text-talkheals-muted text-xs">{{ booking.client?.email ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Type badge -->
                                        <td class="td-cell">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-[0.65rem] font-bold uppercase tracking-widest border"
                                                :class="typeConfig[booking.type]?.cls ?? ''"
                                            >
                                                {{ booking.type === 'free' ? '🎁 Free' : '💳 Paid' }}
                                            </span>
                                        </td>

                                        <!-- Session type -->
                                        <td class="td-cell">
                                            <div class="text-talkheals-deep text-xs font-medium">{{ booking.session_type ?? '—' }}</div>
                                        </td>

                                        <!-- Date & Time -->
                                        <td class="td-cell">
                                            <div class="font-medium text-talkheals-deep">{{ formatDate(booking.booking_date) }}</div>
                                            <div class="text-talkheals-muted text-xs mt-0.5">
                                                {{ formatTime(booking.start_time) }} – {{ formatTime(booking.end_time) }}
                                            </div>
                                        </td>

                                        <!-- Status badge -->
                                        <td class="td-cell">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-[0.65rem] font-bold uppercase tracking-widest border"
                                                :class="statusConfig[booking.status]?.cls ?? ''"
                                            >
                                                {{ statusConfig[booking.status]?.label ?? booking.status }}
                                            </span>
                                        </td>

                                        <!-- Notes -->
                                        <td class="td-cell max-w-[180px]">
                                            <p v-if="booking.client_notes" class="text-talkheals-muted text-xs truncate italic">
                                                "{{ booking.client_notes }}"
                                            </p>
                                            <span v-else class="text-talkheals-muted/40 text-xs">—</span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="td-cell text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button
                                                    v-if="booking.status === 'pending'"
                                                    @click="updateStatus(booking, 'confirmed')"
                                                    class="px-3 py-1.5 bg-talkheals-sage/10 hover:bg-talkheals-sage text-talkheals-sage hover:text-white rounded-xl text-xs font-bold transition-all duration-200 border border-talkheals-sage/20"
                                                >
                                                    Confirm
                                                </button>
                                                <button
                                                    v-if="booking.status === 'confirmed'"
                                                    @click="updateStatus(booking, 'completed')"
                                                    class="px-3 py-1.5 bg-talkheals-sky/10 hover:bg-talkheals-sky text-talkheals-sky hover:text-white rounded-xl text-xs font-bold transition-all duration-200 border border-talkheals-sky/20"
                                                >
                                                    Complete
                                                </button>
                                                <button
                                                    v-if="['pending','confirmed'].includes(booking.status)"
                                                    @click="updateStatus(booking, 'cancelled')"
                                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-500 text-red-400 hover:text-white rounded-xl text-xs font-bold transition-all duration-200 border border-red-100"
                                                >
                                                    Cancel
                                                </button>
                                                <span v-if="!['pending','confirmed'].includes(booking.status)" class="text-talkheals-muted/40 text-xs">—</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="bookings.last_page > 1" class="flex items-center justify-between px-8 py-5 border-t border-talkheals-gold-p/60">
                            <div class="text-xs text-talkheals-muted">
                                Showing {{ bookings.from }}–{{ bookings.to }} of {{ bookings.total }}
                            </div>
                            <div class="flex gap-2">
                                <a
                                    v-if="bookings.prev_page_url"
                                    :href="bookings.prev_page_url"
                                    class="px-4 py-2 bg-talkheals-cream border border-talkheals-gold-p rounded-xl text-xs font-bold text-talkheals-mid hover:border-talkheals-gold transition-all"
                                >← Prev</a>
                                <a
                                    v-if="bookings.next_page_url"
                                    :href="bookings.next_page_url"
                                    class="px-4 py-2 bg-talkheals-gold text-white rounded-xl text-xs font-bold hover:bg-talkheals-deep transition-all"
                                >Next →</a>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- ── Cancel confirmation modal ── -->
    <Teleport to="body">
        <transition name="fade">
            <div
                v-if="cancelModal.open"
                class="fixed inset-0 z-50 flex items-center justify-center p-6"
                style="background:rgba(42,36,32,.55);backdrop-filter:blur(6px)"
                @click.self="cancelModal = { open: false, booking: null }"
            >
                <div class="bg-white rounded-[28px] p-8 w-full max-w-md shadow-2xl border border-talkheals-gold-p">
                    <h3 class="font-serif text-2xl font-light text-talkheals-deep mb-2">Cancel Booking</h3>
                    <p class="text-talkheals-muted text-sm mb-6">
                        Provide a reason for cancelling
                        <strong class="text-talkheals-deep">{{ cancelModal.booking?.client?.name }}</strong>'s session.
                    </p>
                    <textarea
                        v-model="statusForm.cancellation_reason"
                        placeholder="Reason for cancellation (optional)…"
                        rows="3"
                        class="w-full bg-talkheals-cream border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition-all resize-none placeholder:text-talkheals-muted/50"
                    ></textarea>
                    <div class="flex gap-3 mt-6">
                        <button
                            @click="cancelModal = { open: false, booking: null }"
                            class="flex-1 py-3 bg-talkheals-cream border border-talkheals-gold-p rounded-xl text-sm font-bold text-talkheals-mid hover:border-talkheals-gold transition-all"
                        >
                            Keep Booking
                        </button>
                        <button
                            @click="confirmCancel"
                            :disabled="statusForm.processing"
                            class="flex-1 py-3 bg-red-500 text-white rounded-xl text-sm font-bold hover:bg-red-600 transition-all disabled:opacity-50"
                        >
                            {{ statusForm.processing ? 'Cancelling…' : 'Cancel Booking' }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
.stat-mini {
    @apply bg-white border border-talkheals-gold-p rounded-[20px] p-5 text-center shadow-sm shadow-talkheals-gold/5;
}
.stat-mini-val {
    @apply font-serif text-2xl font-light text-talkheals-deep;
}
.stat-mini-lbl {
    @apply text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mt-1;
}
.th-cell {
    @apply px-5 py-4 text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold;
}
.td-cell {
    @apply px-5 py-4;
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
