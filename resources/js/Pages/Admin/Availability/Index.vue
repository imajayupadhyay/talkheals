<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Sidebar from '../Components/Sidebar.vue';
import Topbar from '../Components/Topbar.vue';

const props = defineProps({
    auth: Object,
    availability: Array,
    settings: Object,
    exceptions: Array,
});

// ── Day definitions (Mon → Sun order) ────────────────────────────────────────
const days = [
    { key: 1, label: 'Monday',    short: 'Mon' },
    { key: 2, label: 'Tuesday',   short: 'Tue' },
    { key: 3, label: 'Wednesday', short: 'Wed' },
    { key: 4, label: 'Thursday',  short: 'Thu' },
    { key: 5, label: 'Friday',    short: 'Fri' },
    { key: 6, label: 'Saturday',  short: 'Sat' },
    { key: 0, label: 'Sunday',    short: 'Sun' },
];

// ── Build reactive schedule state from props ──────────────────────────────────
const buildSchedule = () => {
    const s = {};
    days.forEach(d => {
        const daySlots = (props.availability || []).filter(a => a.day_of_week === d.key);
        s[d.key] = {
            active: daySlots.length > 0,
            slots: daySlots.length > 0
                ? daySlots.map(slot => ({ start: slot.start_time.substring(0, 5), end: slot.end_time.substring(0, 5) }))
                : [{ start: '09:00', end: '17:00' }],
        };
    });
    return s;
};

const schedule = ref(buildSchedule());

// ── Forms ─────────────────────────────────────────────────────────────────────
const scheduleForm = useForm({ schedule: {} });

const settingsForm = useForm({
    session_duration:     props.settings?.session_duration    ?? 50,
    buffer_time:          props.settings?.buffer_time         ?? 10,
    advance_booking_days: props.settings?.advance_booking_days ?? 30,
    timezone:             props.settings?.timezone            ?? 'America/Toronto',
    is_booking_enabled:   props.settings?.is_booking_enabled  ?? true,
});

const exceptionForm = useForm({
    date:       '',
    is_blocked: true,
    start_time: '',
    end_time:   '',
    reason:     '',
});

// ── Schedule helpers ──────────────────────────────────────────────────────────
const toggleDay = (dayKey) => {
    schedule.value[dayKey].active = !schedule.value[dayKey].active;
};

const addSlot = (dayKey) => {
    schedule.value[dayKey].slots.push({ start: '09:00', end: '17:00' });
};

const removeSlot = (dayKey, idx) => {
    schedule.value[dayKey].slots.splice(idx, 1);
    if (schedule.value[dayKey].slots.length === 0) {
        schedule.value[dayKey].active = false;
        schedule.value[dayKey].slots  = [{ start: '09:00', end: '17:00' }];
    }
};

const saveSchedule = () => {
    scheduleForm.schedule = schedule.value;
    scheduleForm.post(route('admin.availability.schedule'));
};

const saveSettings = () => {
    settingsForm.post(route('admin.availability.settings'));
};

const addException = () => {
    exceptionForm.post(route('admin.availability.exceptions.store'), {
        onSuccess: () => exceptionForm.reset(),
    });
};

const removeException = (id) => {
    router.delete(route('admin.availability.exceptions.destroy', id), {
        preserveScroll: true,
    });
};

// ── Computed ──────────────────────────────────────────────────────────────────
const activeDaysCount = computed(() => days.filter(d => schedule.value[d.key].active).length);
const todayDate       = computed(() => new Date().toISOString().split('T')[0]);

const formatDate = (dateStr) => {
    const d = String(dateStr).substring(0, 10);
    return new Date(d + 'T00:00:00').toLocaleDateString('en-CA', {
        weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
    });
};

// ── Options ───────────────────────────────────────────────────────────────────
const sessionDurations = [
    { value: 30, label: '30 minutes' },
    { value: 45, label: '45 minutes' },
    { value: 50, label: '50 minutes (standard)' },
    { value: 60, label: '60 minutes' },
    { value: 90, label: '90 minutes' },
];

const bufferTimes = [
    { value: 0,  label: 'No buffer' },
    { value: 5,  label: '5 minutes' },
    { value: 10, label: '10 minutes' },
    { value: 15, label: '15 minutes' },
    { value: 30, label: '30 minutes' },
];

const advanceBookingOptions = [
    { value: 7,  label: '1 week ahead' },
    { value: 14, label: '2 weeks ahead' },
    { value: 30, label: '1 month ahead' },
    { value: 60, label: '2 months ahead' },
    { value: 90, label: '3 months ahead' },
];

const timezones = [
    'America/Toronto',
    'America/Vancouver',
    'America/New_York',
    'America/Chicago',
    'America/Denver',
    'America/Los_Angeles',
    'Europe/London',
    'Europe/Paris',
    'Asia/Kolkata',
    'UTC',
];
</script>

<template>
    <Head title="Availability — TalkHeals Admin" />

    <div class="min-h-screen bg-talkheals-cream flex text-talkheals-deep font-sans">
        <Sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <Topbar />

            <main class="flex-1 p-10 overflow-y-auto">
                <div class="max-w-7xl mx-auto">

                    <!-- ── Page Header ── -->
                    <div class="mb-10 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <h1 class="font-serif text-4xl font-light tracking-tight text-talkheals-deep">
                                Availability <em class="text-talkheals-gold not-italic italic">Settings</em>
                            </h1>
                            <p class="text-talkheals-mid text-lg font-light mt-2">
                                Configure your weekly schedule and booking preferences.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <!-- Booking status badge -->
                            <div
                                class="flex items-center gap-2 px-4 py-2 rounded-full border transition-all duration-300"
                                :class="settingsForm.is_booking_enabled
                                    ? 'bg-talkheals-sage/10 border-talkheals-sage/30 text-talkheals-sage'
                                    : 'bg-talkheals-gold-p border-talkheals-gold-p text-talkheals-muted'"
                            >
                                <span
                                    class="w-2 h-2 rounded-full"
                                    :class="settingsForm.is_booking_enabled ? 'bg-talkheals-sage animate-pulse' : 'bg-talkheals-muted'"
                                ></span>
                                <span class="text-xs font-bold uppercase tracking-widest">
                                    {{ settingsForm.is_booking_enabled ? 'Booking Active' : 'Booking Paused' }}
                                </span>
                            </div>

                            <!-- Days active pill -->
                            <div class="text-xs text-talkheals-mid font-bold px-4 py-2 bg-white border border-talkheals-gold-p rounded-full">
                                {{ activeDaysCount }} day{{ activeDaysCount !== 1 ? 's' : '' }} available
                            </div>
                        </div>
                    </div>

                    <!-- ── 3-Column Layout ── -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">

                        <!-- ════════════════════════════════════════
                             LEFT: Weekly Schedule  (2 cols)
                        ════════════════════════════════════════ -->
                        <div class="xl:col-span-2">
                            <div class="bg-white border border-talkheals-gold-p rounded-[32px] p-8 shadow-sm shadow-talkheals-gold/5">

                                <!-- Card header -->
                                <div class="flex items-start justify-between mb-8">
                                    <div>
                                        <div class="flex items-center gap-3 mb-1">
                                            <div class="w-9 h-9 rounded-xl bg-talkheals-gold/10 flex items-center justify-center text-lg border border-talkheals-gold/20">
                                                📅
                                            </div>
                                            <h2 class="font-serif text-2xl font-light text-talkheals-deep">Weekly Schedule</h2>
                                        </div>
                                        <p class="text-talkheals-muted text-sm pl-12">
                                            Toggle each day and set one or more time windows.
                                        </p>
                                    </div>

                                    <button
                                        @click="saveSchedule"
                                        :disabled="scheduleForm.processing"
                                        class="flex-shrink-0 px-6 py-2.5 bg-talkheals-gold text-white text-sm font-bold rounded-xl hover:bg-talkheals-deep transition-all duration-300 hover:-translate-y-0.5 shadow-lg shadow-talkheals-gold/20 disabled:opacity-50"
                                    >
                                        <span v-if="scheduleForm.processing">Saving…</span>
                                        <span v-else>Save Schedule</span>
                                    </button>
                                </div>

                                <!-- Day rows -->
                                <div class="space-y-2">
                                    <div
                                        v-for="day in days"
                                        :key="day.key"
                                        class="flex items-start gap-5 p-4 rounded-2xl border transition-all duration-300"
                                        :class="schedule[day.key].active
                                            ? 'bg-talkheals-cream border-talkheals-gold-p'
                                            : 'border-transparent hover:border-talkheals-gold-p/60'"
                                    >
                                        <!-- Toggle + day label -->
                                        <div class="flex items-center gap-3 min-w-[160px] pt-0.5">
                                            <button
                                                @click="toggleDay(day.key)"
                                                class="relative w-11 h-6 rounded-full transition-all duration-300 flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40"
                                                :class="schedule[day.key].active ? 'bg-talkheals-gold' : 'bg-talkheals-gold-p'"
                                                :aria-label="`Toggle ${day.label}`"
                                            >
                                                <span
                                                    class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-300"
                                                    :class="schedule[day.key].active ? 'translate-x-5' : 'translate-x-0'"
                                                ></span>
                                            </button>
                                            <span
                                                class="text-sm font-bold w-24 transition-colors"
                                                :class="schedule[day.key].active ? 'text-talkheals-deep' : 'text-talkheals-muted'"
                                            >
                                                {{ day.label }}
                                            </span>
                                        </div>

                                        <!-- Time slots -->
                                        <div class="flex-1 min-w-0">
                                            <div v-if="!schedule[day.key].active" class="text-talkheals-muted text-sm italic pt-1">
                                                Unavailable
                                            </div>

                                            <div v-else class="space-y-2">
                                                <div
                                                    v-for="(slot, idx) in schedule[day.key].slots"
                                                    :key="idx"
                                                    class="flex items-center gap-3"
                                                >
                                                    <!-- Time range input -->
                                                    <div class="flex items-center gap-2 bg-white rounded-xl px-4 py-2 border border-talkheals-gold-p flex-1 max-w-xs">
                                                        <input
                                                            type="time"
                                                            v-model="slot.start"
                                                            class="bg-transparent border-none outline-none text-sm font-medium text-talkheals-deep w-24 cursor-pointer"
                                                        />
                                                        <span class="text-talkheals-muted/60 text-xs select-none">→</span>
                                                        <input
                                                            type="time"
                                                            v-model="slot.end"
                                                            class="bg-transparent border-none outline-none text-sm font-medium text-talkheals-deep w-24 cursor-pointer"
                                                        />
                                                    </div>

                                                    <!-- Remove slot -->
                                                    <button
                                                        v-if="schedule[day.key].slots.length > 1"
                                                        @click="removeSlot(day.key, idx)"
                                                        class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 transition-all flex items-center justify-center text-xs font-bold flex-shrink-0"
                                                        aria-label="Remove slot"
                                                    >✕</button>
                                                </div>

                                                <!-- Add slot -->
                                                <button
                                                    @click="addSlot(day.key)"
                                                    class="flex items-center gap-1.5 text-xs text-talkheals-gold hover:text-talkheals-deep font-bold transition-colors mt-1"
                                                >
                                                    <span class="w-4 h-4 rounded-full bg-talkheals-gold/10 border border-talkheals-gold/20 flex items-center justify-center leading-none">+</span>
                                                    Add time slot
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Schedule save feedback -->
                                <transition name="fade">
                                    <div
                                        v-if="scheduleForm.wasSuccessful"
                                        class="mt-6 flex items-center gap-3 p-4 bg-talkheals-sage/10 border border-talkheals-sage/20 rounded-2xl text-talkheals-sage text-sm font-medium"
                                    >
                                        <span class="text-lg">✓</span>
                                        Weekly schedule saved successfully.
                                    </div>
                                </transition>
                            </div>
                        </div>

                        <!-- ════════════════════════════════════════
                             RIGHT: Settings + Date Overrides
                        ════════════════════════════════════════ -->
                        <div class="space-y-6">

                            <!-- ── Session Settings Card ── -->
                            <div class="bg-white border border-talkheals-gold-p rounded-[32px] p-7 shadow-sm shadow-talkheals-gold/5">
                                <div class="flex items-center gap-3 mb-1">
                                    <div class="w-9 h-9 rounded-xl bg-talkheals-rose/10 flex items-center justify-center text-lg border border-talkheals-rose/20">⚙️</div>
                                    <h2 class="font-serif text-xl font-light text-talkheals-deep">Session Settings</h2>
                                </div>
                                <p class="text-talkheals-muted text-xs mb-6 pl-12">Configure how sessions are booked by clients.</p>

                                <div class="space-y-4">
                                    <!-- Session Duration -->
                                    <div>
                                        <label class="settings-label">Session Duration</label>
                                        <select v-model="settingsForm.session_duration" class="settings-select">
                                            <option v-for="d in sessionDurations" :key="d.value" :value="d.value">
                                                {{ d.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Buffer Time -->
                                    <div>
                                        <label class="settings-label">Buffer Between Sessions</label>
                                        <select v-model="settingsForm.buffer_time" class="settings-select">
                                            <option v-for="b in bufferTimes" :key="b.value" :value="b.value">
                                                {{ b.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Advance Booking -->
                                    <div>
                                        <label class="settings-label">Booking Window</label>
                                        <select v-model="settingsForm.advance_booking_days" class="settings-select">
                                            <option v-for="a in advanceBookingOptions" :key="a.value" :value="a.value">
                                                {{ a.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Timezone -->
                                    <div>
                                        <label class="settings-label">Timezone</label>
                                        <select v-model="settingsForm.timezone" class="settings-select">
                                            <option v-for="tz in timezones" :key="tz" :value="tz">{{ tz }}</option>
                                        </select>
                                    </div>

                                    <!-- Enable Booking Toggle -->
                                    <div class="flex items-center justify-between pt-3 mt-1 border-t border-talkheals-gold-p">
                                        <div>
                                            <div class="text-sm font-bold text-talkheals-deep">Enable Online Booking</div>
                                            <div class="text-xs text-talkheals-muted">Allow clients to book sessions</div>
                                        </div>
                                        <button
                                            @click="settingsForm.is_booking_enabled = !settingsForm.is_booking_enabled"
                                            class="relative w-11 h-6 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40"
                                            :class="settingsForm.is_booking_enabled ? 'bg-talkheals-gold' : 'bg-talkheals-gold-p'"
                                            aria-label="Toggle booking"
                                        >
                                            <span
                                                class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-300"
                                                :class="settingsForm.is_booking_enabled ? 'translate-x-5' : 'translate-x-0'"
                                            ></span>
                                        </button>
                                    </div>
                                </div>

                                <button
                                    @click="saveSettings"
                                    :disabled="settingsForm.processing"
                                    class="w-full mt-6 py-3 bg-talkheals-gold text-white text-sm font-bold rounded-xl hover:bg-talkheals-deep transition-all duration-300 disabled:opacity-50"
                                >
                                    {{ settingsForm.processing ? 'Saving…' : 'Save Settings' }}
                                </button>

                                <transition name="fade">
                                    <div
                                        v-if="settingsForm.wasSuccessful"
                                        class="mt-4 p-3 bg-talkheals-sage/10 border border-talkheals-sage/20 rounded-xl text-talkheals-sage text-xs font-medium text-center"
                                    >
                                        ✓ Settings updated.
                                    </div>
                                </transition>
                            </div>

                            <!-- ── Date Overrides Card ── -->
                            <div class="bg-white border border-talkheals-gold-p rounded-[32px] p-7 shadow-sm shadow-talkheals-gold/5">
                                <div class="flex items-center gap-3 mb-1">
                                    <div class="w-9 h-9 rounded-xl bg-talkheals-sky/10 flex items-center justify-center text-lg border border-talkheals-sky/20">🗓️</div>
                                    <h2 class="font-serif text-xl font-light text-talkheals-deep">Date Overrides</h2>
                                </div>
                                <p class="text-talkheals-muted text-xs mb-6 pl-12">Block specific dates or set custom hours.</p>

                                <!-- Add Override Form -->
                                <div class="space-y-3">
                                    <div>
                                        <label class="settings-label">Select Date</label>
                                        <input
                                            type="date"
                                            v-model="exceptionForm.date"
                                            :min="todayDate"
                                            class="settings-input"
                                        />
                                        <div v-if="exceptionForm.errors.date" class="text-red-500 text-xs mt-1">
                                            {{ exceptionForm.errors.date }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="settings-label">Reason <span class="normal-case font-normal">(optional)</span></label>
                                        <input
                                            type="text"
                                            v-model="exceptionForm.reason"
                                            placeholder="Holiday, personal day, training…"
                                            class="settings-input"
                                        />
                                    </div>

                                    <!-- Block type selector -->
                                    <div class="flex gap-2">
                                        <button
                                            type="button"
                                            @click="exceptionForm.is_blocked = true"
                                            class="flex-1 py-2.5 text-xs font-bold rounded-xl border transition-all duration-200"
                                            :class="exceptionForm.is_blocked
                                                ? 'bg-red-50 border-red-200 text-red-600'
                                                : 'bg-talkheals-cream border-talkheals-gold-p text-talkheals-muted hover:border-talkheals-gold/40'"
                                        >
                                            🚫 Block Day
                                        </button>
                                        <button
                                            type="button"
                                            @click="exceptionForm.is_blocked = false"
                                            class="flex-1 py-2.5 text-xs font-bold rounded-xl border transition-all duration-200"
                                            :class="!exceptionForm.is_blocked
                                                ? 'bg-talkheals-sage/10 border-talkheals-sage/30 text-talkheals-sage'
                                                : 'bg-talkheals-cream border-talkheals-gold-p text-talkheals-muted hover:border-talkheals-gold/40'"
                                        >
                                            ✏️ Custom Hours
                                        </button>
                                    </div>

                                    <!-- Custom hours input -->
                                    <transition name="slide">
                                        <div
                                            v-if="!exceptionForm.is_blocked"
                                            class="flex items-center gap-2 bg-talkheals-cream rounded-xl px-4 py-2.5 border border-talkheals-gold-p"
                                        >
                                            <input
                                                type="time"
                                                v-model="exceptionForm.start_time"
                                                class="bg-transparent border-none outline-none text-sm text-talkheals-deep flex-1 cursor-pointer"
                                            />
                                            <span class="text-talkheals-muted/60 text-xs select-none">→</span>
                                            <input
                                                type="time"
                                                v-model="exceptionForm.end_time"
                                                class="bg-transparent border-none outline-none text-sm text-talkheals-deep flex-1 cursor-pointer"
                                            />
                                        </div>
                                    </transition>

                                    <div v-if="exceptionForm.errors.start_time || exceptionForm.errors.end_time" class="text-red-500 text-xs">
                                        {{ exceptionForm.errors.start_time || exceptionForm.errors.end_time }}
                                    </div>

                                    <button
                                        @click="addException"
                                        :disabled="!exceptionForm.date || exceptionForm.processing"
                                        class="w-full py-2.5 bg-talkheals-deep text-white text-sm font-bold rounded-xl hover:bg-talkheals-gold transition-all duration-300 disabled:opacity-40"
                                    >
                                        {{ exceptionForm.processing ? 'Adding…' : '+ Add Override' }}
                                    </button>
                                </div>

                                <!-- Existing overrides list -->
                                <div class="mt-6">
                                    <div v-if="exceptions.length > 0" class="space-y-2">
                                        <div class="text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mb-3">
                                            Upcoming Overrides ({{ exceptions.length }})
                                        </div>

                                        <div
                                            v-for="ex in exceptions"
                                            :key="ex.id"
                                            class="flex items-start justify-between gap-3 p-3.5 rounded-2xl border transition-all duration-200"
                                            :class="ex.is_blocked
                                                ? 'bg-red-50/60 border-red-100 hover:border-red-200'
                                                : 'bg-talkheals-sage/5 border-talkheals-sage/20 hover:border-talkheals-sage/40'"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 mb-0.5">
                                                    <span class="text-[0.6rem] font-bold uppercase tracking-widest" :class="ex.is_blocked ? 'text-red-500' : 'text-talkheals-sage'">
                                                        {{ ex.is_blocked ? '🚫 Blocked' : '⏰ Custom Hours' }}
                                                    </span>
                                                </div>
                                                <div class="text-xs font-bold text-talkheals-deep truncate">
                                                    {{ formatDate(ex.date) }}
                                                </div>
                                                <div v-if="!ex.is_blocked && ex.start_time" class="text-[0.65rem] text-talkheals-sage font-medium mt-0.5">
                                                    {{ ex.start_time.substring(0,5) }} – {{ ex.end_time.substring(0,5) }}
                                                </div>
                                                <div v-if="ex.reason" class="text-[0.65rem] text-talkheals-muted truncate mt-0.5 italic">
                                                    {{ ex.reason }}
                                                </div>
                                            </div>

                                            <button
                                                @click="removeException(ex.id)"
                                                class="w-7 h-7 flex-shrink-0 rounded-lg bg-white hover:bg-red-50 text-talkheals-muted hover:text-red-500 transition-all flex items-center justify-center text-xs font-bold border border-talkheals-gold-p"
                                                aria-label="Remove override"
                                            >✕</button>
                                        </div>
                                    </div>

                                    <div v-else class="text-center py-6">
                                        <div class="text-2xl mb-2 opacity-40">📭</div>
                                        <div class="text-talkheals-muted text-xs">No date overrides set.</div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /right col -->
                    </div><!-- /grid -->

                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.settings-label {
    @apply block text-[0.6rem] tracking-[0.15em] uppercase text-talkheals-muted font-bold mb-1.5;
}

.settings-select {
    @apply w-full bg-talkheals-cream border border-talkheals-gold-p rounded-xl px-3 py-2.5 text-sm text-talkheals-deep
           focus:outline-none focus:border-talkheals-gold transition-all duration-200 cursor-pointer;
}

.settings-input {
    @apply w-full bg-talkheals-cream border border-talkheals-gold-p rounded-xl px-3 py-2.5 text-sm text-talkheals-deep
           focus:outline-none focus:border-talkheals-gold transition-all duration-200
           placeholder:text-talkheals-muted/50;
}

/* time input chrome/safari tick color */
input[type="time"]::-webkit-calendar-picker-indicator {
    opacity: 0.3;
    cursor: pointer;
}

/* Transition: fade */
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }

/* Transition: slide-down */
.slide-enter-active, .slide-leave-active { transition: all 0.25s ease; overflow: hidden; }
.slide-enter-from, .slide-leave-to       { opacity: 0; max-height: 0; }
.slide-enter-to, .slide-leave-from      { opacity: 1; max-height: 80px; }
</style>
