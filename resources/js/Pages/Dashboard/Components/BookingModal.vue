<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { usePage, router }  from '@inertiajs/vue3';
import axios        from 'axios';

// ── Props / emits ─────────────────────────────────────────────────────────────
defineProps({ open: Boolean });
const emit = defineEmits(['close', 'restore']);

// ── Auth user (reactive so it updates after in-modal login) ──────────────────
const user = ref(usePage().props.auth?.user ?? null);

// ── Step state ────────────────────────────────────────────────────────────────
// 1 = pick date | 2 = pick time | 3 = auth (guests only) / confirm | 4 = success
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

// ── Auth form state (for in-modal login/register) ────────────────────────────
const authMode = ref('login'); // 'login' or 'signup'
const authLoading = ref(false);
const authErrors = ref({});
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const loginData = ref({ email: '', password: '', remember: false });
const registerData = ref({ name: '', email: '', password: '', password_confirmation: '' });

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
    const first  = new Date(y, m, 1).getDay();
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
    // If logged in → go to confirm, otherwise → auth step
    step.value = user.value ? 3 : 3; // same step number, different content shown
};

// ── In-modal authentication ──────────────────────────────────────────────────
const needsAuth = computed(() => !user.value && step.value === 3);

const saveBookingState = () => {
    sessionStorage.setItem('pendingBooking', JSON.stringify({
        date: selectedDate.value,
        slot: selectedSlot.value,
        notes: clientNotes.value,
    }));
};

const handleLogin = async () => {
    authLoading.value = true;
    authErrors.value = {};
    try {
        saveBookingState();
        await axios.post('/login', loginData.value);
        // Redirect will happen — sessionStorage preserves booking state
        window.location.reload();
    } catch (err) {
        if (err.response?.status === 422) {
            authErrors.value = err.response.data.errors || {};
        } else {
            authErrors.value = { email: ['Login failed. Please try again.'] };
        }
        authLoading.value = false;
    }
};

const handleRegister = async () => {
    authLoading.value = true;
    authErrors.value = {};
    try {
        saveBookingState();
        await axios.post('/register', registerData.value);
        window.location.reload();
    } catch (err) {
        if (err.response?.status === 422) {
            authErrors.value = err.response.data.errors || {};
        } else {
            authErrors.value = { email: ['Registration failed. Please try again.'] };
        }
        authLoading.value = false;
    }
};

// ── Submit booking ───────────────────────────────────────────────────────────
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
    setTimeout(() => {
        step.value        = 1;
        selectedDate.value = null;
        selectedSlot.value = null;
        clientNotes.value  = '';
        submitError.value  = '';
        successData.value  = null;
        authErrors.value   = {};
        loginData.value    = { email: '', password: '', remember: false };
        registerData.value = { name: '', email: '', password: '', password_confirmation: '' };
        authMode.value     = 'login';
    }, 300);
};

// ── Formatted display strings ─────────────────────────────────────────────────
const formattedDate = computed(() => {
    if (!selectedDate.value) return '';
    return new Date(selectedDate.value + 'T00:00:00').toLocaleDateString('en-CA', {
        weekday: 'long', month: 'long', day: 'numeric', year: 'numeric',
    });
});

// Total steps for progress dots (3 if logged in, 4 if guest needing auth)
const totalSteps = computed(() => user.value ? 3 : 4);
const displayStep = computed(() => {
    if (step.value <= 2) return step.value;
    if (step.value === 3 && needsAuth.value) return 3; // auth step
    if (step.value === 3 && !needsAuth.value) return user.value ? 3 : 4; // confirm
    return step.value; // success
});

// ── Restore booking state after auth redirect ────────────────────────────────
const restoreBookingState = () => {
    const saved = sessionStorage.getItem('pendingBooking');
    if (!saved || !user.value) return;

    try {
        const { date, slot, notes } = JSON.parse(saved);
        sessionStorage.removeItem('pendingBooking');

        if (date && slot) {
            selectedDate.value = date;
            selectedSlot.value = slot;
            clientNotes.value = notes || '';
            step.value = 3; // go straight to confirm
            // Tell parent to open the modal
            emit('restore');
        }
    } catch {
        sessionStorage.removeItem('pendingBooking');
    }
};

// ── Fetch on mount ────────────────────────────────────────────────────────────
onMounted(() => {
    fetchCalendar();
    restoreBookingState();
});
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
                        class="relative w-full max-w-xl bg-white rounded-[32px] shadow-2xl border border-talkheals-gold-p overflow-hidden max-h-[90vh] overflow-y-auto"
                    >
                        <!-- Close -->
                        <button
                            @click="closeModal"
                            class="absolute top-5 right-5 z-10 w-9 h-9 rounded-xl bg-talkheals-cream hover:bg-talkheals-gold-p border border-talkheals-gold-p flex items-center justify-center text-talkheals-muted hover:text-talkheals-deep transition-all text-sm font-bold"
                        >✕</button>

                        <!-- Step indicator (not on success) -->
                        <div v-if="step < 4" class="px-8 pt-8 pb-0">
                            <div class="flex items-center gap-2 mb-5">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-talkheals-sage/10 border border-talkheals-sage/20 rounded-full">
                                    <span class="w-2 h-2 rounded-full bg-talkheals-sage animate-pulse"></span>
                                    <span class="text-[0.6rem] font-bold uppercase tracking-widest text-talkheals-sage">Free · 30-Min Discovery Call</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mb-6">
                                <div
                                    v-for="s in totalSteps"
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

                            <div v-if="loadingCal" class="flex justify-center py-10">
                                <div class="w-8 h-8 border-2 border-talkheals-gold border-t-transparent rounded-full animate-spin"></div>
                            </div>

                            <div v-else>
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

                                <div class="grid grid-cols-7 mb-2">
                                    <div v-for="d in DAYS" :key="d" class="text-center text-[0.6rem] font-bold uppercase tracking-widest text-talkheals-muted py-1">{{ d }}</div>
                                </div>

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
                                        <span v-if="isAvailable(cell) && !isPast(cell) && !isSelected(cell)" class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-talkheals-gold"></span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 mt-5 text-[0.65rem] text-talkheals-muted">
                                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-talkheals-gold"></span> Available</div>
                                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-talkheals-muted/20"></span> Unavailable</div>
                                </div>
                            </div>
                        </div>

                        <!-- ════════════════════════════════════════
                             STEP 2 — Pick a Time Slot
                        ════════════════════════════════════════ -->
                        <div v-if="step === 2" class="px-8 pb-8">
                            <button @click="step = 1" class="flex items-center gap-1.5 text-talkheals-muted hover:text-talkheals-gold text-xs font-bold mb-5 transition-colors">← Back</button>
                            <h2 class="font-serif text-2xl font-light text-talkheals-deep mb-1">Choose a time</h2>
                            <p class="text-talkheals-muted text-sm mb-6">{{ formattedDate }} · 30 minutes</p>

                            <div v-if="loadingSlots" class="flex justify-center py-10">
                                <div class="w-8 h-8 border-2 border-talkheals-gold border-t-transparent rounded-full animate-spin"></div>
                            </div>

                            <div v-else-if="!slots.length" class="text-center py-10">
                                <div class="text-3xl mb-3 opacity-40">🕐</div>
                                <div class="text-talkheals-muted text-sm">No available slots on this date.</div>
                                <button @click="step = 1" class="mt-4 text-talkheals-gold text-xs font-bold hover:text-talkheals-deep transition-colors">Pick another date →</button>
                            </div>

                            <div v-else class="grid grid-cols-3 gap-2">
                                <button
                                    v-for="slot in slots"
                                    :key="slot.start"
                                    @click="pickSlot(slot)"
                                    class="py-3 rounded-2xl text-sm font-bold border transition-all duration-200 hover:scale-105"
                                    :class="selectedSlot?.start === slot.start
                                        ? 'bg-talkheals-gold text-white border-talkheals-gold shadow-lg shadow-talkheals-gold/30'
                                        : 'bg-talkheals-cream border-talkheals-gold-p text-talkheals-deep hover:border-talkheals-gold hover:bg-talkheals-gold/5'"
                                >{{ slot.label }}</button>
                            </div>
                        </div>

                        <!-- ════════════════════════════════════════
                             STEP 3 — Auth (guests) OR Confirm (logged in)
                        ════════════════════════════════════════ -->
                        <div v-if="step === 3" class="px-8 pb-8">
                            <button @click="step = 2" class="flex items-center gap-1.5 text-talkheals-muted hover:text-talkheals-gold text-xs font-bold mb-5 transition-colors">← Back</button>

                            <!-- ─── GUEST: Login / Register Form ─── -->
                            <template v-if="needsAuth">
                                <h2 class="font-serif text-2xl font-light text-talkheals-deep mb-1">Almost there!</h2>
                                <p class="text-talkheals-muted text-sm mb-5">Sign in or create an account to complete your booking.</p>

                                <!-- Booking summary reminder -->
                                <div class="bg-talkheals-cream border border-talkheals-gold-p rounded-2xl p-4 mb-5 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-talkheals-gold/10 border border-talkheals-gold/20 flex items-center justify-center text-lg shrink-0">📞</div>
                                    <div class="text-xs">
                                        <div class="font-bold text-talkheals-deep">Free Discovery Call</div>
                                        <div class="text-talkheals-muted">{{ formattedDate }} · {{ selectedSlot?.label }}</div>
                                    </div>
                                </div>

                                <!-- Google Sign-In -->
                                <a
                                    :href="route('auth.google')"
                                    @click="saveBookingState()"
                                    class="w-full flex items-center justify-center gap-3 bg-white border border-talkheals-gold-p hover:border-talkheals-gold rounded-xl px-4 py-3 text-talkheals-deep font-medium transition-all duration-300 hover:shadow-md mb-4"
                                >
                                    <svg width="18" height="18" viewBox="0 0 48 48">
                                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                    </svg>
                                    Continue with Google
                                </a>

                                <div class="flex items-center gap-4 mb-4">
                                    <div class="flex-1 h-px bg-talkheals-gold-p"></div>
                                    <span class="text-xs text-talkheals-muted font-light">or</span>
                                    <div class="flex-1 h-px bg-talkheals-gold-p"></div>
                                </div>

                                <!-- Login / Signup Toggle -->
                                <div class="flex bg-talkheals-cream p-1 rounded-xl mb-5 border border-talkheals-gold-p">
                                    <button
                                        @click="authMode = 'login'; authErrors = {}"
                                        class="flex-1 py-2 rounded-lg text-xs font-semibold transition-all duration-300"
                                        :class="authMode === 'login' ? 'bg-white text-talkheals-deep shadow-sm' : 'text-talkheals-muted'"
                                    >Log In</button>
                                    <button
                                        @click="authMode = 'signup'; authErrors = {}"
                                        class="flex-1 py-2 rounded-lg text-xs font-semibold transition-all duration-300"
                                        :class="authMode === 'signup' ? 'bg-white text-talkheals-deep shadow-sm' : 'text-talkheals-muted'"
                                    >Sign Up</button>
                                </div>

                                <!-- Login Form -->
                                <form v-if="authMode === 'login'" @submit.prevent="handleLogin" class="space-y-3">
                                    <div>
                                        <input v-model="loginData.email" type="email" placeholder="Email address" required
                                            class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition">
                                        <p v-if="authErrors.email" class="text-red-500 text-xs mt-1">{{ authErrors.email[0] }}</p>
                                    </div>
                                    <div class="relative">
                                        <input v-model="loginData.password" :type="showPassword ? 'text' : 'password'" placeholder="Password" required
                                            class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 pr-10 text-sm text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition">
                                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-talkheals-muted hover:text-talkheals-gold transition-colors">
                                            <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.644C3.399 8.049 7.21 5 12 5c3.79 0 7.113 2.046 9.036 5.322a1.012 1.012 0 010 .644C19.601 15.951 15.79 19 12 19c-3.79 0-7.113-2.046-9.036-5.322z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                        </button>
                                        <p v-if="authErrors.password" class="text-red-500 text-xs mt-1">{{ authErrors.password[0] }}</p>
                                    </div>
                                    <button type="submit" :disabled="authLoading"
                                        class="w-full py-3 bg-talkheals-rose text-white font-bold rounded-xl hover:bg-talkheals-deep transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-2">
                                        <span v-if="authLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                        {{ authLoading ? 'Signing in…' : 'Sign in & Continue' }}
                                    </button>
                                </form>

                                <!-- Register Form -->
                                <form v-else @submit.prevent="handleRegister" class="space-y-3">
                                    <div>
                                        <input v-model="registerData.name" type="text" placeholder="Full name" required
                                            class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition">
                                        <p v-if="authErrors.name" class="text-red-500 text-xs mt-1">{{ authErrors.name[0] }}</p>
                                    </div>
                                    <div>
                                        <input v-model="registerData.email" type="email" placeholder="Email address" required
                                            class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition">
                                        <p v-if="authErrors.email" class="text-red-500 text-xs mt-1">{{ authErrors.email[0] }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <div class="relative">
                                                <input v-model="registerData.password" :type="showPassword ? 'text' : 'password'" placeholder="Password" required
                                                    class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 pr-9 text-sm text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition">
                                                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-talkheals-muted hover:text-talkheals-gold">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.644C3.399 8.049 7.21 5 12 5c3.79 0 7.113 2.046 9.036 5.322a1.012 1.012 0 010 .644C19.601 15.951 15.79 19 12 19c-3.79 0-7.113-2.046-9.036-5.322z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                </button>
                                            </div>
                                            <p v-if="authErrors.password" class="text-red-500 text-xs mt-1">{{ authErrors.password[0] }}</p>
                                        </div>
                                        <div>
                                            <div class="relative">
                                                <input v-model="registerData.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" placeholder="Confirm" required
                                                    class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 pr-9 text-sm text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition">
                                                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-talkheals-muted hover:text-talkheals-gold">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.644C3.399 8.049 7.21 5 12 5c3.79 0 7.113 2.046 9.036 5.322a1.012 1.012 0 010 .644C19.601 15.951 15.79 19 12 19c-3.79 0-7.113-2.046-9.036-5.322z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" :disabled="authLoading"
                                        class="w-full py-3 bg-talkheals-gold text-white font-bold rounded-xl hover:bg-talkheals-deep transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-2">
                                        <span v-if="authLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                        {{ authLoading ? 'Creating account…' : 'Create Account & Continue' }}
                                    </button>
                                </form>

                                <p class="text-center text-talkheals-muted text-[0.6rem] mt-4">
                                    🔒 Your information is 100% confidential
                                </p>
                            </template>

                            <!-- ─── AUTHENTICATED: Confirm Booking ─── -->
                            <template v-else>
                                <h2 class="font-serif text-2xl font-light text-talkheals-deep mb-1">Confirm your call</h2>
                                <p class="text-talkheals-muted text-sm mb-6">Review your details and book.</p>

                                <div class="bg-talkheals-cream border border-talkheals-gold-p rounded-2xl p-5 mb-6">
                                    <div class="flex items-start gap-4">
                                        <div class="w-11 h-11 rounded-2xl bg-talkheals-gold/10 border border-talkheals-gold/20 flex items-center justify-center text-xl flex-shrink-0">📞</div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-talkheals-deep text-sm">Free Discovery Call</div>
                                            <div class="text-talkheals-muted text-xs mt-1">{{ formattedDate }}</div>
                                            <div class="text-talkheals-gold text-xs font-bold mt-0.5">{{ selectedSlot?.label }} · 30 minutes</div>
                                        </div>
                                        <span class="px-2.5 py-1 bg-talkheals-sage/10 border border-talkheals-sage/20 text-talkheals-sage text-[0.6rem] font-bold uppercase tracking-widest rounded-lg shrink-0">Free</span>
                                    </div>
                                </div>

                                <div class="space-y-3 mb-5">
                                    <div>
                                        <label class="block text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1.5">Your Name</label>
                                        <div class="w-full bg-talkheals-gold-p border border-talkheals-gold-p rounded-xl px-4 py-2.5 text-sm text-talkheals-mid font-medium">{{ user?.name ?? '—' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1.5">Email</label>
                                        <div class="w-full bg-talkheals-gold-p border border-talkheals-gold-p rounded-xl px-4 py-2.5 text-sm text-talkheals-mid font-medium">{{ user?.email ?? '—' }}</div>
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

                                <div v-if="submitError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-500 text-xs font-medium">{{ submitError }}</div>

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

                                <p class="text-center text-talkheals-muted text-[0.65rem] mt-4">🔒 100% confidential · No payment required</p>
                            </template>
                        </div>

                        <!-- ════════════════════════════════════════
                             STEP 4 — Success
                        ════════════════════════════════════════ -->
                        <div v-if="step === 4" class="px-8 py-10 text-center">
                            <div class="w-20 h-20 mx-auto rounded-full bg-talkheals-sage/10 border-2 border-talkheals-sage/30 flex items-center justify-center mb-6 text-4xl">✓</div>

                            <h2 class="font-serif text-3xl font-light text-talkheals-deep mb-3">
                                You're <em class="italic text-talkheals-sage not-italic">booked!</em>
                            </h2>
                            <p class="text-talkheals-muted text-sm mb-8 max-w-xs mx-auto leading-relaxed">
                                Your free discovery call with Namrata has been submitted. You'll receive a confirmation shortly.
                            </p>

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

                            <button @click="closeModal" class="w-full py-3.5 bg-talkheals-gold text-white font-bold rounded-xl hover:bg-talkheals-deep transition-all duration-300">Done</button>
                        </div>

                    </div>
                </transition>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to       { opacity: 0; }

.modal-slide-enter-active { transition: all 0.35s cubic-bezier(.34,1.56,.64,1); }
.modal-slide-leave-active { transition: all 0.25s ease; }
.modal-slide-enter-from   { opacity: 0; transform: translateY(24px) scale(0.97); }
.modal-slide-leave-to     { opacity: 0; transform: translateY(12px) scale(0.98); }

.bg-talkheals-gold\/8 { background-color: rgb(201 169 110 / 0.08); }
</style>
