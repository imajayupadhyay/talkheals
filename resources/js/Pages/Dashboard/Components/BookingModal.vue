<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { usePage }  from '@inertiajs/vue3';
import axios        from 'axios';

// ── Props / emits ─────────────────────────────────────────────────────────────
defineProps({ open: Boolean });
const emit = defineEmits(['close']);

// ── Auth user ─────────────────────────────────────────────────────────────────
const user = usePage().props.auth?.user;

// ── Step state ────────────────────────────────────────────────────────────────
// 1 = pick date | 2 = pick time | 3 = confirm | 4 = success
const step = ref(1);

// ── Calendar state ────────────────────────────────────────────────────────────
const today          = new Date();
const currentMonth   = ref(new Date(today.getFullYear(), today.getMonth(), 1));
const availableDates = ref([]);
const loadingCal     = ref(false);
const selectedDate   = ref(null);

// ── Slot state ────────────────────────────────────────────────────────────────
const slots        = ref([]);
const loadingSlots = ref(false);
const selectedSlot = ref(null);

// ── Confirm form state ────────────────────────────────────────────────────────
const clientNotes  = ref('');
const submitting   = ref(false);
const submitError  = ref('');
const successData  = ref(null);

// ── Calendar grid helpers ─────────────────────────────────────────────────────
const DAYS = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
const MONTHS = ['January','February','March','April','May','June',
                'July','August','September','October','November','December'];

const monthLabel = computed(() =>
    `${MONTHS[currentMonth.value.getMonth()]} ${currentMonth.value.getFullYear()}`
);

const calendarCells = computed(() => {
    const y      = currentMonth.value.getFullYear();
    const m      = currentMonth.value.getMonth();
    const first  = new Date(y, m, 1).getDay(); // 0=Sun
    const total  = new Date(y, m + 1, 0).getDate();
    const cells  = [];
    for (let i = 0; i < first; i++)  cells.push(null);
    for (let i = 1; i <= total; i++) cells.push(i);
    return cells;
});

const dateStr = (day) => {
    if (!day) return null;
    const y = currentMonth.value.getFullYear();
    const m = String(currentMonth.value.getMonth() + 1).padStart(2, '0');
    const d = String(day).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

const isAvailable = (day) => day && availableDates.value.includes(dateStr(day));
const isPast      = (day) => {
    if (!day) return true;
    const ds = dateStr(day);
    return ds < new Date().toISOString().split('T')[0];
};
const isSelected  = (day) => day && dateStr(day) === selectedDate.value;
const isToday     = (day) => day && dateStr(day) === new Date().toISOString().split('T')[0];

const canPrevMonth = computed(() => {
    const now = new Date();
    return currentMonth.value > new Date(now.getFullYear(), now.getMonth(), 1);
});

// ── Data fetching ─────────────────────────────────────────────────────────────
const fetchCalendar = async () => {
    loadingCal.value = true;
    try {
        const res = await axios.get('/booking/calendar');
        availableDates.value = res.data.available_dates || [];
    } catch {
        availableDates.value = [];
    } finally {
        loadingCal.value = false;
    }
};

const fetchSlots = async (date) => {
    loadingSlots.value = true;
    slots.value = [];
    try {
        const res = await axios.get('/booking/slots', { params: { date } });
        slots.value = res.data || [];
    } catch {
        slots.value = [];
    } finally {
        loadingSlots.value = false;
    }
};

// ── Actions ───────────────────────────────────────────────────────────────────
const prevMonth = () => {
    if (!canPrevMonth.value) return;
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 1);
};

const pickDate = async (day) => {
    if (!isAvailable(day) || isPast(day)) return;
    selectedDate.value = dateStr(day);
    await fetchSlots(selectedDate.value);
    step.value = 2;
};

const pickSlot = (slot) => {
    selectedSlot.value = slot;
    step.value = 3;
};

const submitBooking = async () => {
    submitting.value = true;
    submitError.value = '';
    try {
        const res = await axios.post('/booking', {
            date:         selectedDate.value,
            start_time:   selectedSlot.value.start,
            client_notes: clientNotes.value || null,
        });
        successData.value = res.data;
        step.value = 4;
    } catch (err) {
        submitError.value = err.response?.data?.message || 'Something went wrong. Please try again.';
    } finally {
        submitting.value = false;
    }
};

const closeModal = () => {
    emit('close');
    // Reset after transition
    setTimeout(() => {
        step.value        = 1;
        selectedDate.value = null;
        selectedSlot.value = null;
        clientNotes.value  = '';
        submitError.value  = '';
        successData.value  = null;
    }, 300);
};

// ── Formatted display strings ─────────────────────────────────────────────────
const formattedDate = computed(() => {
    if (!selectedDate.value) return '';
    return new Date(selectedDate.value + 'T00:00:00').toLocaleDateString('en-CA', {
        weekday: 'long', month: 'long', day: 'numeric', year: 'numeric',
    });
});

// ── Fetch on mount ────────────────────────────────────────────────────────────
onMounted(fetchCalendar);
</script>

<template>
    <Teleport to="body">
        <transition name="modal-fade">
            <div
                v-if="open"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                style="background:rgba(42,36,32,.6);backdrop-filter:blur(8px)"
                @click.self="closeModal"
            >
                <transition name="modal-slide">
                    <div
                        v-if="open"
                        class="relative w-full max-w-xl bg-white rounded-[32px] shadow-2xl border border-talkheals-gold-p overflow-hidden"
                    >
                        <!-- Close -->
                        <button
                            @click="closeModal"
                            class="absolute top-5 right-5 z-10 w-9 h-9 rounded-xl bg-talkheals-cream hover:bg-talkheals-gold-p border border-talkheals-gold-p flex items-center justify-center text-talkheals-muted hover:text-talkheals-deep transition-all text-sm font-bold"
                        >✕</button>

                        <!-- ────────────────────────────────────────
                             Step indicator (steps 1–3 only)
                        ──────────────────────────────────────── -->
                        <div v-if="step < 4" class="px-8 pt-8 pb-0">
                            <!-- Session badge -->
                            <div class="flex items-center gap-2 mb-5">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-talkheals-sage/10 border border-talkheals-sage/20 rounded-full">
                                    <span class="w-2 h-2 rounded-full bg-talkheals-sage animate-pulse"></span>
                                    <span class="text-[0.6rem] font-bold uppercase tracking-widest text-talkheals-sage">Free · 30-Min Discovery Call</span>
                                </div>
                            </div>

                            <!-- Step dots -->
                            <div class="flex items-center gap-2 mb-6">
                                <div
                                    v-for="s in 3"
                                    :key="s"
                                    class="h-1 rounded-full transition-all duration-400"
                                    :class="[
                                        s <= step ? 'bg-talkheals-gold' : 'bg-talkheals-gold-p',
                                        s === step ? 'flex-[2]' : 'flex-1'
                                    ]"
                                ></div>
                            </div>
                        </div>

                        <!-- ════════════════════════════════════════
                             STEP 1 — Pick a Date
                        ════════════════════════════════════════ -->
                        <div v-if="step === 1" class="px-8 pb-8">
                            <h2 class="font-serif text-2xl font-light text-talkheals-deep mb-1">Choose a date</h2>
                            <p class="text-talkheals-muted text-sm mb-6">Select a day that works for you.</p>

                            <!-- Loading -->
                            <div v-if="loadingCal" class="flex justify-center py-10">
                                <div class="w-8 h-8 border-2 border-talkheals-gold border-t-transparent rounded-full animate-spin"></div>
                            </div>

                            <div v-else>
                                <!-- Month nav -->
                                <div class="flex items-center justify-between mb-4">
                                    <button
                                        @click="prevMonth"
                                        :disabled="!canPrevMonth"
                                        class="w-9 h-9 rounded-xl flex items-center justify-center transition-all"
                                        :class="canPrevMonth
                                            ? 'hover:bg-talkheals-cream text-talkheals-mid border border-talkheals-gold-p'
                                            : 'text-talkheals-muted/30 cursor-not-allowed'"
                                    >‹</button>

                                    <span class="font-serif text-base font-light text-talkheals-deep">{{ monthLabel }}</span>

                                    <button
                                        @click="nextMonth"
                                        class="w-9 h-9 rounded-xl hover:bg-talkheals-cream text-talkheals-mid border border-talkheals-gold-p flex items-center justify-center transition-all"
                                    >›</button>
                                </div>

                                <!-- Day headers -->
                                <div class="grid grid-cols-7 mb-2">
                                    <div
                                        v-for="d in DAYS"
                                        :key="d"
                                        class="text-center text-[0.6rem] font-bold uppercase tracking-widest text-talkheals-muted py-1"
                                    >{{ d }}</div>
                                </div>

                                <!-- Calendar grid -->
                                <div class="grid grid-cols-7 gap-1">
                                    <div
                                        v-for="(cell, idx) in calendarCells"
                                        :key="idx"
                                        @click="cell && pickDate(cell)"
                                        class="aspect-square flex items-center justify-center rounded-xl text-sm font-medium transition-all duration-200 relative"
                                        :class="[
                                            !cell ? 'invisible' : '',
                                            isSelected(cell)
                                                ? 'bg-talkheals-gold text-white shadow-lg shadow-talkheals-gold/30 scale-110'
                                                : isAvailable(cell) && !isPast(cell)
                                                    ? 'cursor-pointer bg-talkheals-gold/8 text-talkheals-deep border border-talkheals-gold/20 hover:bg-talkheals-gold hover:text-white hover:scale-105'
                                                    : 'text-talkheals-muted/40 cursor-not-allowed',
                                            isToday(cell) && !isSelected(cell) ? 'ring-1 ring-talkheals-gold/40' : ''
                                        ]"
                                    >
                                        {{ cell }}
                                        <!-- Available dot -->
                                        <span
                                            v-if="isAvailable(cell) && !isPast(cell) && !isSelected(cell)"
                                            class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-talkheals-gold"
                                        ></span>
                                    </div>
                                </div>

                                <!-- Legend -->
                                <div class="flex items-center gap-4 mt-5 text-[0.65rem] text-talkheals-muted">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-talkheals-gold"></span>
                                        Available
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-talkheals-muted/20"></span>
                                        Unavailable
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ════════════════════════════════════════
                             STEP 2 — Pick a Time Slot
                        ════════════════════════════════════════ -->
                        <div v-if="step === 2" class="px-8 pb-8">
                            <button @click="step = 1" class="flex items-center gap-1.5 text-talkheals-muted hover:text-talkheals-gold text-xs font-bold mb-5 transition-colors">
                                ← Back
                            </button>
                            <h2 class="font-serif text-2xl font-light text-talkheals-deep mb-1">Choose a time</h2>
                            <p class="text-talkheals-muted text-sm mb-6">
                                {{ formattedDate }} · 30 minutes
                            </p>

                            <!-- Loading slots -->
                            <div v-if="loadingSlots" class="flex justify-center py-10">
                                <div class="w-8 h-8 border-2 border-talkheals-gold border-t-transparent rounded-full animate-spin"></div>
                            </div>

                            <!-- No slots -->
                            <div v-else-if="!slots.length" class="text-center py-10">
                                <div class="text-3xl mb-3 opacity-40">🕐</div>
                                <div class="text-talkheals-muted text-sm">No available slots on this date.</div>
                                <button @click="step = 1" class="mt-4 text-talkheals-gold text-xs font-bold hover:text-talkheals-deep transition-colors">
                                    Pick another date →
                                </button>
                            </div>

                            <!-- Slots grid -->
                            <div v-else class="grid grid-cols-3 gap-2">
                                <button
                                    v-for="slot in slots"
                                    :key="slot.start"
                                    @click="pickSlot(slot)"
                                    class="py-3 rounded-2xl text-sm font-bold border transition-all duration-200 hover:scale-105"
                                    :class="selectedSlot?.start === slot.start
                                        ? 'bg-talkheals-gold text-white border-talkheals-gold shadow-lg shadow-talkheals-gold/30'
                                        : 'bg-talkheals-cream border-talkheals-gold-p text-talkheals-deep hover:border-talkheals-gold hover:bg-talkheals-gold/5'"
                                >
                                    {{ slot.label }}
                                </button>
                            </div>
                        </div>

                        <!-- ════════════════════════════════════════
                             STEP 3 — Confirm Booking
                        ════════════════════════════════════════ -->
                        <div v-if="step === 3" class="px-8 pb-8">
                            <button @click="step = 2" class="flex items-center gap-1.5 text-talkheals-muted hover:text-talkheals-gold text-xs font-bold mb-5 transition-colors">
                                ← Back
                            </button>
                            <h2 class="font-serif text-2xl font-light text-talkheals-deep mb-1">Confirm your call</h2>
                            <p class="text-talkheals-muted text-sm mb-6">Review your details and book.</p>

                            <!-- Booking summary card -->
                            <div class="bg-talkheals-cream border border-talkheals-gold-p rounded-2xl p-5 mb-6">
                                <div class="flex items-start gap-4">
                                    <div class="w-11 h-11 rounded-2xl bg-talkheals-gold/10 border border-talkheals-gold/20 flex items-center justify-center text-xl flex-shrink-0">📞</div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-talkheals-deep text-sm">Free Discovery Call</div>
                                        <div class="text-talkheals-muted text-xs mt-1">{{ formattedDate }}</div>
                                        <div class="text-talkheals-gold text-xs font-bold mt-0.5">
                                            {{ selectedSlot?.label }} · 30 minutes
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="px-2.5 py-1 bg-talkheals-sage/10 border border-talkheals-sage/20 text-talkheals-sage text-[0.6rem] font-bold uppercase tracking-widest rounded-lg">
                                            Free
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Client details (read-only from auth) -->
                            <div class="space-y-3 mb-5">
                                <div>
                                    <label class="block text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1.5">Your Name</label>
                                    <div class="w-full bg-talkheals-gold-p border border-talkheals-gold-p rounded-xl px-4 py-2.5 text-sm text-talkheals-mid font-medium">
                                        {{ user?.name ?? '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1.5">Email</label>
                                    <div class="w-full bg-talkheals-gold-p border border-talkheals-gold-p rounded-xl px-4 py-2.5 text-sm text-talkheals-mid font-medium">
                                        {{ user?.email ?? '—' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1.5">
                                        Anything you'd like to share? <span class="normal-case font-normal">(optional)</span>
                                    </label>
                                    <textarea
                                        v-model="clientNotes"
                                        rows="3"
                                        placeholder="What brings you here? Any concerns or questions for Namrata…"
                                        class="w-full bg-talkheals-cream border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition-all resize-none placeholder:text-talkheals-muted/50"
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Error -->
                            <div v-if="submitError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-500 text-xs font-medium">
                                {{ submitError }}
                            </div>

                            <button
                                @click="submitBooking"
                                :disabled="submitting"
                                class="w-full py-4 bg-talkheals-gold text-white font-bold rounded-xl hover:bg-talkheals-deep transition-all duration-300 hover:-translate-y-0.5 shadow-lg shadow-talkheals-gold/20 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="submitting" class="flex items-center justify-center gap-2">
                                    <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                    Booking…
                                </span>
                                <span v-else>Confirm Free Discovery Call →</span>
                            </button>

                            <p class="text-center text-talkheals-muted text-[0.65rem] mt-4">
                                🔒 100% confidential · No payment required
                            </p>
                        </div>

                        <!-- ════════════════════════════════════════
                             STEP 4 — Success
                        ════════════════════════════════════════ -->
                        <div v-if="step === 4" class="px-8 py-10 text-center">
                            <!-- Animated checkmark -->
                            <div class="w-20 h-20 mx-auto rounded-full bg-talkheals-sage/10 border-2 border-talkheals-sage/30 flex items-center justify-center mb-6 text-4xl">
                                ✓
                            </div>

                            <h2 class="font-serif text-3xl font-light text-talkheals-deep mb-3">
                                You're <em class="italic text-talkheals-sage not-italic">booked!</em>
                            </h2>
                            <p class="text-talkheals-muted text-sm mb-8 max-w-xs mx-auto leading-relaxed">
                                Your free discovery call with Namrata has been submitted. You'll receive a confirmation shortly.
                            </p>

                            <!-- Booking details pill -->
                            <div class="inline-flex flex-col gap-2 bg-talkheals-cream border border-talkheals-gold-p rounded-2xl px-8 py-5 text-left mb-8">
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-talkheals-gold text-lg">📅</span>
                                    <span class="text-talkheals-deep font-medium">{{ successData?.booking_date }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-talkheals-gold text-lg">⏰</span>
                                    <span class="text-talkheals-deep font-medium">{{ successData?.start_time }} – {{ successData?.end_time }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-talkheals-gold text-lg">🎁</span>
                                    <span class="text-talkheals-sage font-bold text-xs uppercase tracking-widest">Free Discovery Call</span>
                                </div>
                            </div>

                            <button
                                @click="closeModal"
                                class="w-full py-3.5 bg-talkheals-gold text-white font-bold rounded-xl hover:bg-talkheals-deep transition-all duration-300"
                            >
                                Done
                            </button>
                        </div>

                    </div>
                </transition>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
/* Modal overlay fade */
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to       { opacity: 0; }

/* Modal card slide-up */
.modal-slide-enter-active { transition: all 0.35s cubic-bezier(.34,1.56,.64,1); }
.modal-slide-leave-active { transition: all 0.25s ease; }
.modal-slide-enter-from   { opacity: 0; transform: translateY(24px) scale(0.97); }
.modal-slide-leave-to     { opacity: 0; transform: translateY(12px) scale(0.98); }

/* Available cell soft bg */
.bg-talkheals-gold\/8 { background-color: rgb(201 169 110 / 0.08); }
</style>
