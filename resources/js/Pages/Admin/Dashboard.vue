<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import Sidebar from './Components/Sidebar.vue';
import Topbar from './Components/Topbar.vue';

const flash = computed(() => usePage().props.flash ?? {});
const showFlash = ref(false);

watch(flash, (val) => {
    if (val.success) {
        showFlash.value = true;
        setTimeout(() => { showFlash.value = false; }, 5000);
    }
}, { immediate: true });

const props = defineProps({
    auth: Object,
    todayBookings: Array,
    weekBookings: Array,
    monthBookings: Array,
    selectedDateBookings: Array,
    selectedDate: String,
    pendingBookings: Array,
    stats: Object,
    upcomingWorkshops: Array,
    calendarDates: Object,
});

// Schedule view tabs
const activeTab = ref('today');

const displayBookings = computed(() => {
    if (props.selectedDate && props.selectedDateBookings) {
        return props.selectedDateBookings;
    }
    switch (activeTab.value) {
        case 'today': return props.todayBookings || [];
        case 'week': return props.weekBookings || [];
        case 'month': return props.monthBookings || [];
        default: return [];
    }
});

// Mini calendar state
const now = new Date();
const calendarMonth = ref(now.getMonth());
const calendarYear = ref(now.getFullYear());

const calendarMonthName = computed(() => {
    return new Date(calendarYear.value, calendarMonth.value).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const calendarDays = computed(() => {
    const firstDay = new Date(calendarYear.value, calendarMonth.value, 1);
    const lastDay = new Date(calendarYear.value, calendarMonth.value + 1, 0);
    const startPad = (firstDay.getDay() + 6) % 7; // Monday start
    const days = [];

    // Padding days from previous month
    for (let i = 0; i < startPad; i++) {
        days.push({ day: null, date: null });
    }

    for (let d = 1; d <= lastDay.getDate(); d++) {
        const dateStr = `${calendarYear.value}-${String(calendarMonth.value + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
        days.push({
            day: d,
            date: dateStr,
            isToday: dateStr === todayStr,
            hasBookings: props.calendarDates && props.calendarDates[dateStr] > 0,
            bookingCount: props.calendarDates?.[dateStr] || 0,
            isSelected: props.selectedDate === dateStr,
        });
    }
    return days;
});

function prevMonth() {
    if (calendarMonth.value === 0) {
        calendarMonth.value = 11;
        calendarYear.value--;
    } else {
        calendarMonth.value--;
    }
}

function nextMonth() {
    if (calendarMonth.value === 11) {
        calendarMonth.value = 0;
        calendarYear.value++;
    } else {
        calendarMonth.value++;
    }
}

function selectDate(date) {
    if (!date) return;
    router.get(route('admin.dashboard'), { date }, { preserveState: true, preserveScroll: true });
}

function clearDateFilter() {
    router.get(route('admin.dashboard'), {}, { preserveState: true, preserveScroll: true });
}

function switchTab(tab) {
    activeTab.value = tab;
    if (props.selectedDate) {
        clearDateFilter();
    }
}

function formatTime(time) {
    if (!time) return '';
    const [h, m] = time.split(':');
    const hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const h12 = hour % 12 || 12;
    return `${h12}:${m} ${ampm}`;
}

function formatDateLabel(dateStr) {
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
}

function formatBookingDate(dateStr) {
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
}

function getStatusColor(status) {
    switch (status) {
        case 'confirmed': return 'bg-talkheals-sage/10 text-talkheals-sage border-talkheals-sage/20';
        case 'pending': return 'bg-talkheals-gold/10 text-talkheals-gold border-talkheals-gold/20';
        case 'completed': return 'bg-talkheals-sky/10 text-talkheals-sky border-talkheals-sky/20';
        case 'cancelled': return 'bg-talkheals-rose/10 text-talkheals-rose border-talkheals-rose/20';
        default: return 'bg-gray-100 text-gray-500';
    }
}

function getGreeting() {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 17) return 'Good afternoon';
    return 'Good evening';
}

function confirmBooking(bookingId) {
    router.patch(route('admin.bookings.status', bookingId), { status: 'confirmed' }, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Admin Dashboard - TalkHeals" />

    <div class="min-h-screen bg-talkheals-cream flex text-talkheals-deep font-sans">
        <Sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <Topbar />

            <main class="flex-1 p-8 overflow-y-auto relative">
                <!-- Flash Success Message -->
                <transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 -translate-y-3"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-3"
                >
                    <div v-if="showFlash && flash.success" class="fixed top-6 right-6 z-50 max-w-md px-5 py-3.5 bg-white border border-talkheals-sage/30 rounded-2xl shadow-lg shadow-talkheals-sage/10 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-talkheals-sage/10 flex items-center justify-center flex-shrink-0 border border-talkheals-sage/20">
                            <svg class="w-4 h-4 text-talkheals-sage" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm font-medium text-talkheals-deep">{{ flash.success }}</span>
                        <button @click="showFlash = false" class="ml-2 text-talkheals-muted hover:text-talkheals-deep flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </transition>

                <div class="max-w-7xl mx-auto">

                    <!-- Welcome Header -->
                    <div class="mb-8">
                        <h1 class="font-serif text-3xl font-light tracking-tight text-talkheals-deep">
                            {{ getGreeting() }}, <span class="text-talkheals-gold">{{ auth.user.name }}</span>
                        </h1>
                        <p class="text-talkheals-mid text-sm font-light mt-1">Here's what's on your plate today.</p>
                    </div>

                    <!-- Stats Row -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                        <div class="stat-card">
                            <div class="flex justify-between items-start mb-3">
                                <div class="w-11 h-11 rounded-2xl bg-talkheals-gold/10 flex items-center justify-center text-xl border border-talkheals-gold/20">
                                    <svg class="w-5 h-5 text-talkheals-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-talkheals-gold text-[0.65rem] font-bold bg-talkheals-gold/10 px-2 py-0.5 rounded-full border border-talkheals-gold/20">Today</span>
                            </div>
                            <div class="text-talkheals-muted text-[0.6rem] uppercase tracking-[0.2em] font-bold mb-0.5">Today's Sessions</div>
                            <div class="text-2xl font-serif font-light text-talkheals-deep">{{ stats.today_sessions }}</div>
                        </div>

                        <div class="stat-card">
                            <div class="flex justify-between items-start mb-3">
                                <div class="w-11 h-11 rounded-2xl bg-talkheals-rose/10 flex items-center justify-center text-xl border border-talkheals-rose/20">
                                    <svg class="w-5 h-5 text-talkheals-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-talkheals-rose text-[0.65rem] font-bold bg-talkheals-rose/10 px-2 py-0.5 rounded-full border border-talkheals-rose/20" v-if="stats.pending_count > 0">Action</span>
                            </div>
                            <div class="text-talkheals-muted text-[0.6rem] uppercase tracking-[0.2em] font-bold mb-0.5">Pending Approval</div>
                            <div class="text-2xl font-serif font-light text-talkheals-deep">{{ stats.pending_count }}</div>
                        </div>

                        <div class="stat-card">
                            <div class="flex justify-between items-start mb-3">
                                <div class="w-11 h-11 rounded-2xl bg-talkheals-sage/10 flex items-center justify-center text-xl border border-talkheals-sage/20">
                                    <svg class="w-5 h-5 text-talkheals-sage" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <div class="text-talkheals-muted text-[0.6rem] uppercase tracking-[0.2em] font-bold mb-0.5">Completed (Month)</div>
                            <div class="text-2xl font-serif font-light text-talkheals-deep">{{ stats.completed_this_month }}</div>
                        </div>

                        <div class="stat-card">
                            <div class="flex justify-between items-start mb-3">
                                <div class="w-11 h-11 rounded-2xl bg-talkheals-sky/10 flex items-center justify-center text-xl border border-talkheals-sky/20">
                                    <svg class="w-5 h-5 text-talkheals-sky" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <span class="text-talkheals-sky text-[0.65rem] font-bold bg-talkheals-sky/10 px-2 py-0.5 rounded-full border border-talkheals-sky/20">{{ stats.total_clients }}</span>
                            </div>
                            <div class="text-talkheals-muted text-[0.6rem] uppercase tracking-[0.2em] font-bold mb-0.5">New Clients (Month)</div>
                            <div class="text-2xl font-serif font-light text-talkheals-deep">{{ stats.new_clients_month }}</div>
                        </div>
                    </div>

                    <!-- Main Grid: Schedule + Calendar/Actions -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        <!-- LEFT: Schedule Section (2 cols) -->
                        <div class="lg:col-span-2 space-y-6">

                            <!-- Schedule Card -->
                            <div class="bg-white border border-talkheals-gold-p rounded-[28px] overflow-hidden shadow-sm shadow-talkheals-gold/5">
                                <!-- Tabs -->
                                <div class="flex items-center justify-between border-b border-talkheals-gold-p px-7 pt-6 pb-0">
                                    <div class="flex gap-1">
                                        <button
                                            v-for="tab in [
                                                { key: 'today', label: 'Today', count: stats.today_sessions },
                                                { key: 'week', label: 'This Week', count: stats.week_sessions },
                                                { key: 'month', label: 'This Month', count: stats.month_sessions },
                                            ]"
                                            :key="tab.key"
                                            @click="switchTab(tab.key)"
                                            class="px-4 py-3 text-sm font-medium rounded-t-xl transition-all duration-200 border-b-2 -mb-px"
                                            :class="activeTab === tab.key && !selectedDate
                                                ? 'border-talkheals-gold text-talkheals-gold bg-talkheals-gold/5'
                                                : 'border-transparent text-talkheals-muted hover:text-talkheals-mid'"
                                        >
                                            {{ tab.label }}
                                            <span class="ml-1.5 text-[0.65rem] font-bold bg-talkheals-cream px-1.5 py-0.5 rounded-full">{{ tab.count }}</span>
                                        </button>
                                    </div>
                                    <div v-if="selectedDate" class="flex items-center gap-2 pb-3">
                                        <span class="text-xs text-talkheals-gold font-medium">Viewing: {{ formatDateLabel(selectedDate) }}</span>
                                        <button @click="clearDateFilter" class="text-talkheals-muted hover:text-talkheals-deep text-xs underline">Clear</button>
                                    </div>
                                </div>

                                <!-- Booking List -->
                                <div class="p-7">
                                    <div v-if="displayBookings.length === 0" class="text-center py-12">
                                        <div class="w-16 h-16 rounded-3xl bg-talkheals-cream flex items-center justify-center mx-auto mb-4 border border-talkheals-gold-p">
                                            <svg class="w-7 h-7 text-talkheals-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <p class="text-talkheals-muted text-sm font-light">No sessions scheduled for this period.</p>
                                        <p class="text-talkheals-muted/60 text-xs mt-1">Enjoy the free time!</p>
                                    </div>

                                    <div v-else class="space-y-3">
                                        <!-- Group header for week/month view -->
                                        <template v-for="(booking, index) in displayBookings" :key="booking.id">
                                            <!-- Date separator for week/month views -->
                                            <div
                                                v-if="(activeTab !== 'today' || selectedDate) && (index === 0 || booking.booking_date !== displayBookings[index-1].booking_date)"
                                                class="flex items-center gap-3 pt-2"
                                                :class="index > 0 ? 'mt-4' : ''"
                                            >
                                                <span class="text-[0.65rem] uppercase tracking-[0.15em] font-bold text-talkheals-gold">{{ formatBookingDate(booking.booking_date) }}</span>
                                                <div class="flex-1 h-px bg-talkheals-gold-p"></div>
                                            </div>

                                            <!-- Booking Card -->
                                            <div class="flex items-center gap-4 p-4 rounded-2xl border border-talkheals-gold-p/60 hover:border-talkheals-gold/30 transition-all duration-200 hover:shadow-sm group">
                                                <!-- Time -->
                                                <div class="w-20 text-center flex-shrink-0">
                                                    <div class="text-sm font-bold text-talkheals-deep">{{ formatTime(booking.start_time) }}</div>
                                                    <div class="text-[0.6rem] text-talkheals-muted">{{ formatTime(booking.end_time) }}</div>
                                                </div>

                                                <div class="w-px h-10 bg-talkheals-gold-p flex-shrink-0"></div>

                                                <!-- Client Info -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-medium text-talkheals-deep truncate">{{ booking.client?.name || 'Unknown Client' }}</span>
                                                        <span class="text-[0.6rem] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border" :class="getStatusColor(booking.status)">{{ booking.status }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3 mt-0.5">
                                                        <span class="text-xs text-talkheals-muted">{{ booking.session_type || 'Individual' }}</span>
                                                        <span class="text-xs text-talkheals-muted/50">|</span>
                                                        <span class="text-xs capitalize" :class="booking.type === 'paid' ? 'text-talkheals-sage' : 'text-talkheals-sky'">{{ booking.type }}</span>
                                                    </div>
                                                </div>

                                                <!-- Google Meet Link -->
                                                <a
                                                    v-if="booking.google_meet_link"
                                                    :href="booking.google_meet_link"
                                                    target="_blank"
                                                    class="flex-shrink-0 px-3 py-1.5 text-xs font-medium bg-talkheals-sage/10 text-talkheals-sage rounded-xl border border-talkheals-sage/20 hover:bg-talkheals-sage/20 transition-colors"
                                                >
                                                    Join Meet
                                                </a>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Actions -->
                            <div v-if="pendingBookings.length > 0" class="bg-white border border-talkheals-rose/20 rounded-[28px] overflow-hidden shadow-sm shadow-talkheals-rose/5">
                                <div class="px-7 pt-6 pb-4 border-b border-talkheals-rose/10">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-talkheals-rose/10 flex items-center justify-center border border-talkheals-rose/20">
                                            <svg class="w-4 h-4 text-talkheals-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.27 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        </div>
                                        <h3 class="font-serif text-lg font-light text-talkheals-deep">Needs Your Attention</h3>
                                        <span class="text-[0.65rem] font-bold bg-talkheals-rose/10 text-talkheals-rose px-2 py-0.5 rounded-full border border-talkheals-rose/20">{{ stats.pending_count }} pending</span>
                                    </div>
                                </div>

                                <div class="p-7 space-y-3">
                                    <div v-for="booking in pendingBookings" :key="booking.id" class="flex items-center gap-4 p-4 rounded-2xl bg-talkheals-cream/50 border border-talkheals-gold-p/60">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-talkheals-deep">{{ booking.client?.name || 'Unknown' }}</div>
                                            <div class="text-xs text-talkheals-muted mt-0.5">
                                                {{ formatBookingDate(booking.booking_date) }} at {{ formatTime(booking.start_time) }}
                                                <span class="text-talkheals-muted/50 mx-1">|</span>
                                                {{ booking.session_type || 'Individual' }}
                                                <span class="text-talkheals-muted/50 mx-1">|</span>
                                                <span class="capitalize">{{ booking.type }}</span>
                                            </div>
                                            <div v-if="booking.client_notes" class="text-xs text-talkheals-mid mt-1 italic truncate">"{{ booking.client_notes }}"</div>
                                        </div>
                                        <div class="flex gap-2 flex-shrink-0">
                                            <button
                                                @click="confirmBooking(booking.id)"
                                                class="px-3.5 py-1.5 text-xs font-bold bg-talkheals-sage text-white rounded-xl hover:bg-talkheals-sage/90 transition-colors"
                                            >
                                                Confirm
                                            </button>
                                            <Link
                                                :href="route('admin.bookings')"
                                                class="px-3.5 py-1.5 text-xs font-medium text-talkheals-muted border border-talkheals-gold-p rounded-xl hover:bg-talkheals-cream transition-colors"
                                            >
                                                Review
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Calendar + Quick Links (1 col) -->
                        <div class="space-y-6">

                            <!-- Mini Calendar -->
                            <div class="bg-white border border-talkheals-gold-p rounded-[28px] p-6 shadow-sm shadow-talkheals-gold/5">
                                <!-- Month Nav -->
                                <div class="flex items-center justify-between mb-5">
                                    <button @click="prevMonth" class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-talkheals-cream transition-colors text-talkheals-muted hover:text-talkheals-deep">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <span class="text-sm font-bold text-talkheals-deep tracking-wide">{{ calendarMonthName }}</span>
                                    <button @click="nextMonth" class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-talkheals-cream transition-colors text-talkheals-muted hover:text-talkheals-deep">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>

                                <!-- Day Headers -->
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    <div v-for="d in ['M','T','W','T','F','S','S']" :key="d" class="text-center text-[0.6rem] font-bold uppercase tracking-wider text-talkheals-muted py-1">{{ d }}</div>
                                </div>

                                <!-- Calendar Grid -->
                                <div class="grid grid-cols-7 gap-1">
                                    <button
                                        v-for="(cell, idx) in calendarDays"
                                        :key="idx"
                                        @click="cell.date && selectDate(cell.date)"
                                        class="relative w-full aspect-square rounded-xl flex flex-col items-center justify-center text-xs transition-all duration-200"
                                        :class="[
                                            !cell.day ? '' : 'hover:bg-talkheals-cream cursor-pointer',
                                            cell.isToday ? 'bg-talkheals-gold text-white font-bold hover:bg-talkheals-gold/90' : '',
                                            cell.isSelected && !cell.isToday ? 'bg-talkheals-gold/15 border border-talkheals-gold font-bold text-talkheals-gold' : '',
                                            !cell.isToday && !cell.isSelected ? 'text-talkheals-mid' : '',
                                        ]"
                                        :disabled="!cell.day"
                                    >
                                        <span>{{ cell.day }}</span>
                                        <span v-if="cell.hasBookings && !cell.isToday" class="absolute bottom-1 w-1 h-1 rounded-full bg-talkheals-gold"></span>
                                        <span v-if="cell.hasBookings && cell.isToday" class="absolute bottom-1 w-1 h-1 rounded-full bg-white"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Upcoming Workshops -->
                            <div v-if="upcomingWorkshops.length > 0" class="bg-white border border-talkheals-gold-p rounded-[28px] p-6 shadow-sm shadow-talkheals-gold/5">
                                <h3 class="text-[0.65rem] uppercase tracking-[0.2em] font-bold text-talkheals-muted mb-4">Upcoming Workshops</h3>
                                <div class="space-y-3">
                                    <div v-for="ws in upcomingWorkshops" :key="ws.id" class="p-3 rounded-xl bg-talkheals-cream/50 border border-talkheals-gold-p/60">
                                        <div class="text-sm font-medium text-talkheals-deep truncate">{{ ws.title }}</div>
                                        <div class="text-xs text-talkheals-muted mt-0.5">
                                            {{ formatBookingDate(ws.workshop_date) }}
                                            <span v-if="ws.workshop_time"> at {{ formatTime(ws.workshop_time) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="text-[0.6rem] font-bold px-1.5 py-0.5 rounded-full" :class="ws.is_free ? 'bg-talkheals-sky/10 text-talkheals-sky border border-talkheals-sky/20' : 'bg-talkheals-gold/10 text-talkheals-gold border border-talkheals-gold/20'">
                                                {{ ws.is_free ? 'Free' : 'Paid' }}
                                            </span>
                                            <span class="text-[0.6rem] text-talkheals-muted">{{ ws.duration_minutes }}min</span>
                                        </div>
                                    </div>
                                </div>
                                <Link :href="route('admin.workshops')" class="block text-center text-xs text-talkheals-gold font-medium mt-4 hover:underline">
                                    View All Workshops
                                </Link>
                            </div>

                            <!-- Quick Actions -->
                            <div class="bg-white border border-talkheals-gold-p rounded-[28px] p-6 shadow-sm shadow-talkheals-gold/5">
                                <h3 class="text-[0.65rem] uppercase tracking-[0.2em] font-bold text-talkheals-muted mb-4">Quick Actions</h3>
                                <div class="space-y-2">
                                    <Link :href="route('admin.bookings')" class="quick-action-btn">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <span>Manage Bookings</span>
                                    </Link>
                                    <Link :href="route('admin.clients')" class="quick-action-btn">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>View Clients</span>
                                    </Link>
                                    <Link :href="route('admin.availability')" class="quick-action-btn">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Set Availability</span>
                                    </Link>
                                    <Link :href="route('admin.broadcasting')" class="quick-action-btn">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.858 15.355-5.858 21.213 0"/></svg>
                                        <span>Send Broadcast</span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.stat-card {
    @apply bg-white border border-talkheals-gold-p rounded-[24px] p-5 transition-all duration-300 hover:border-talkheals-gold hover:-translate-y-0.5 cursor-default shadow-sm shadow-talkheals-gold/5;
}

.quick-action-btn {
    @apply flex items-center gap-3 w-full px-4 py-3 text-sm font-medium text-talkheals-mid rounded-xl border border-talkheals-gold-p/60 hover:border-talkheals-gold/30 hover:bg-talkheals-cream hover:text-talkheals-deep transition-all duration-200;
}
</style>
