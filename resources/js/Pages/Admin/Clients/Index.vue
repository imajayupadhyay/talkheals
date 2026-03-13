<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';
import Sidebar from '../Components/Sidebar.vue';
import Topbar  from '../Components/Topbar.vue';

const props = defineProps({
    clients: Object,   // paginated
    stats:   Object,
    filters: Object,
});

// ── Search ────────────────────────────────────────────────────────────────────
const search = ref(props.filters?.search ?? '');
let searchTimeout = null;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.clients'), { search: val || undefined }, {
            preserveScroll: true, replace: true,
        });
    }, 350);
});

// ── Drawer state ──────────────────────────────────────────────────────────────
// mode: null | 'view' | 'create' | 'edit'
const drawerMode     = ref(null);
const drawerClient   = ref(null);
const drawerTab      = ref('basic'); // 'basic' | 'profile'
const viewTab        = ref('details'); // 'details' | 'bookings'
const viewData       = ref(null);   // { client, bookings }
const loadingView    = ref(false);

const openView = async (client) => {
    drawerMode.value   = 'view';
    drawerClient.value = client;
    viewTab.value      = 'details';
    viewData.value     = null;
    loadingView.value  = true;
    try {
        const res = await axios.get(route('admin.clients.show', client.id));
        viewData.value = res.data;
    } finally {
        loadingView.value = false;
    }
};

const openCreate = () => {
    drawerClient.value = null;
    drawerMode.value   = 'create';
    drawerTab.value    = 'basic';
    createForm.reset();
};

const openEdit = (client) => {
    // can be called from view mode too
    const c = viewData.value?.client ?? client;
    drawerClient.value = c;
    drawerMode.value   = 'edit';
    drawerTab.value    = 'basic';
    populateEditForm(c);
};

const closeDrawer = () => {
    drawerMode.value   = null;
    drawerClient.value = null;
    viewData.value     = null;
};

// ── Create form ───────────────────────────────────────────────────────────────
const createForm = useForm({
    name:                  '',
    email:                 '',
    password:              '',
    password_confirmation: '',
});

const submitCreate = () => {
    createForm.post(route('admin.clients.store'), {
        preserveScroll: true,
        onSuccess: closeDrawer,
    });
};

// ── Edit form ─────────────────────────────────────────────────────────────────
const editForm = useForm({
    name:                            '',
    email:                           '',
    password:                        '',
    password_confirmation:           '',
    preferred_name:                  '',
    pronouns:                        '',
    date_of_birth:                   '',
    gender_identity:                 '',
    phone:                           '',
    timezone:                        '',
    occupation:                      '',
    emergency_contact_name:          '',
    emergency_contact_phone:         '',
    emergency_contact_relationship:  '',
    reason_for_therapy:              '',
    previous_therapy_experience:     false,
    current_medications:             '',
    mental_health_history:           '',
});

const populateEditForm = (client) => {
    const p = client.profile ?? {};
    editForm.name                           = client.name           ?? '';
    editForm.email                          = client.email          ?? '';
    editForm.password                       = '';
    editForm.password_confirmation          = '';
    editForm.preferred_name                 = p.preferred_name      ?? '';
    editForm.pronouns                       = p.pronouns            ?? '';
    editForm.date_of_birth                  = p.date_of_birth ? String(p.date_of_birth).substring(0, 10) : '';
    editForm.gender_identity                = p.gender_identity     ?? '';
    editForm.phone                          = p.phone               ?? '';
    editForm.timezone                       = p.timezone            ?? '';
    editForm.occupation                     = p.occupation          ?? '';
    editForm.emergency_contact_name         = p.emergency_contact_name         ?? '';
    editForm.emergency_contact_phone        = p.emergency_contact_phone        ?? '';
    editForm.emergency_contact_relationship = p.emergency_contact_relationship ?? '';
    editForm.reason_for_therapy             = p.reason_for_therapy  ?? '';
    editForm.previous_therapy_experience    = p.previous_therapy_experience ?? false;
    editForm.current_medications            = p.current_medications ?? '';
    editForm.mental_health_history          = p.mental_health_history ?? '';
};

const submitEdit = () => {
    editForm.patch(route('admin.clients.update', drawerClient.value.id), {
        preserveScroll: true,
        onSuccess: closeDrawer,
    });
};

// ── Delete ────────────────────────────────────────────────────────────────────
const deleteTarget = ref(null);
const deleting     = ref(false);

const confirmDelete = (client) => { deleteTarget.value = client; };
const closeDelete   = () => { deleteTarget.value = null; };

const submitDelete = () => {
    deleting.value = true;
    router.delete(route('admin.clients.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => { deleting.value = false; closeDelete(); },
        onError:   () => { deleting.value = false; },
    });
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatDate = (d) => {
    if (!d) return '—';
    return new Date(String(d).substring(0, 10) + 'T00:00:00').toLocaleDateString('en-CA', {
        month: 'short', day: 'numeric', year: 'numeric',
    });
};

const initials = (name) => name ? name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '?';

const avatarGrad = (name) => {
    const idx = (name?.charCodeAt(0) ?? 0) % 4;
    return [
        'from-talkheals-gold/40 to-talkheals-rose/40',
        'from-talkheals-sage/40 to-talkheals-sky/40',
        'from-talkheals-rose/40 to-talkheals-gold/30',
        'from-talkheals-sky/40 to-talkheals-sage/40',
    ][idx];
};

const formatTime = (t) => {
    if (!t) return '';
    const [h, m] = String(t).substring(0, 5).split(':');
    const dt = new Date(); dt.setHours(+h, +m);
    return dt.toLocaleTimeString('en-CA', { hour: 'numeric', minute: '2-digit' });
};

const timezones = [
    'America/Toronto','America/Vancouver','America/New_York',
    'America/Chicago','America/Denver','America/Los_Angeles',
    'Europe/London','Europe/Paris','Asia/Kolkata','UTC',
];
</script>

<template>
    <Head title="Clients — TalkHeals Admin" />

    <div class="min-h-screen bg-talkheals-cream flex text-talkheals-deep font-sans">
        <Sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <Topbar />

            <main class="flex-1 p-10 overflow-y-auto">
                <div class="max-w-7xl mx-auto">

                    <!-- ── Header ── -->
                    <div class="flex items-start justify-between mb-10">
                        <div>
                            <h1 class="font-serif text-4xl font-light tracking-tight text-talkheals-deep">
                                Clients <em class="text-talkheals-gold not-italic italic">Directory</em>
                            </h1>
                            <p class="text-talkheals-mid text-lg font-light mt-2">
                                Manage all registered clients and their profiles.
                            </p>
                        </div>
                        <button
                            @click="openCreate"
                            class="flex items-center gap-2 px-6 py-3 bg-talkheals-gold text-white font-bold text-sm rounded-2xl hover:bg-talkheals-deep transition-all duration-300 hover:-translate-y-0.5 shadow-lg shadow-talkheals-gold/20"
                        >
                            <span class="text-lg leading-none">+</span> Add Client
                        </button>
                    </div>

                    <!-- ── Stats ── -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <div class="stat-card">
                            <div class="w-10 h-10 rounded-xl bg-talkheals-gold/10 border border-talkheals-gold/20 flex items-center justify-center text-xl mb-3">👥</div>
                            <div class="text-2xl font-serif font-light">{{ stats.total }}</div>
                            <div class="stat-lbl">Total Clients</div>
                        </div>
                        <div class="stat-card">
                            <div class="w-10 h-10 rounded-xl bg-talkheals-sage/10 border border-talkheals-sage/20 flex items-center justify-center text-xl mb-3">🌱</div>
                            <div class="text-2xl font-serif font-light text-talkheals-sage">{{ stats.new_this_month }}</div>
                            <div class="stat-lbl">New This Month</div>
                        </div>
                        <div class="stat-card">
                            <div class="w-10 h-10 rounded-xl bg-talkheals-rose/10 border border-talkheals-rose/20 flex items-center justify-center text-xl mb-3">📅</div>
                            <div class="text-2xl font-serif font-light text-talkheals-rose">{{ stats.with_bookings }}</div>
                            <div class="stat-lbl">Have Booked</div>
                        </div>
                        <div class="stat-card">
                            <div class="w-10 h-10 rounded-xl bg-talkheals-sky/10 border border-talkheals-sky/20 flex items-center justify-center text-xl mb-3">✨</div>
                            <div class="text-2xl font-serif font-light text-talkheals-sky">{{ stats.active }}</div>
                            <div class="stat-lbl">Upcoming Sessions</div>
                        </div>
                    </div>

                    <!-- ── Search ── -->
                    <div class="relative mb-6">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-talkheals-muted text-sm">🔍</span>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search by name or email…"
                            class="w-full bg-white border border-talkheals-gold-p rounded-2xl py-3 pl-10 pr-5 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition-all placeholder:text-talkheals-muted/50 shadow-sm"
                        />
                    </div>

                    <!-- ── Clients Table ── -->
                    <div class="bg-white border border-talkheals-gold-p rounded-[28px] shadow-sm shadow-talkheals-gold/5 overflow-hidden">

                        <!-- Empty -->
                        <div v-if="!clients.data.length" class="flex flex-col items-center justify-center py-20 text-center">
                            <div class="text-5xl mb-4 opacity-30">👤</div>
                            <div class="font-serif text-xl text-talkheals-deep font-light mb-2">No clients found</div>
                            <div class="text-talkheals-muted text-sm">Try adjusting your search or add a new client.</div>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-talkheals-gold-p">
                                        <th class="th">Client</th>
                                        <th class="th">Phone</th>
                                        <th class="th">Timezone</th>
                                        <th class="th">Bookings</th>
                                        <th class="th">Joined</th>
                                        <th class="th text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-talkheals-gold-p/50">
                                    <tr
                                        v-for="client in clients.data"
                                        :key="client.id"
                                        class="hover:bg-talkheals-cream/40 transition-colors group"
                                    >
                                        <!-- Client avatar + name -->
                                        <td class="td">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-2xl bg-gradient-to-br flex items-center justify-center text-talkheals-deep text-sm font-bold flex-shrink-0 border border-talkheals-gold-p"
                                                    :class="avatarGrad(client.name)"
                                                >
                                                    {{ initials(client.name) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-talkheals-deep truncate">{{ client.name }}</div>
                                                    <div class="text-talkheals-muted text-xs truncate">{{ client.email }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="td text-talkheals-mid text-xs">
                                            {{ client.profile?.phone || '—' }}
                                        </td>

                                        <td class="td text-talkheals-mid text-xs">
                                            {{ client.profile?.timezone || '—' }}
                                        </td>

                                        <td class="td">
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold border"
                                                :class="client.bookings_count > 0
                                                    ? 'bg-talkheals-gold/8 border-talkheals-gold/20 text-talkheals-gold'
                                                    : 'bg-talkheals-gold-p border-talkheals-gold-p text-talkheals-muted'"
                                            >
                                                {{ client.bookings_count }}
                                            </span>
                                        </td>

                                        <td class="td text-talkheals-muted text-xs">
                                            {{ formatDate(client.created_at) }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="td text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button
                                                    @click="openView(client)"
                                                    class="px-3 py-1.5 bg-talkheals-sky/10 hover:bg-talkheals-sky border border-talkheals-sky/20 text-talkheals-sky hover:text-white rounded-xl text-xs font-bold transition-all duration-200"
                                                >
                                                    View
                                                </button>
                                                <button
                                                    @click="openEdit(client)"
                                                    class="px-3 py-1.5 bg-talkheals-cream border border-talkheals-gold-p hover:border-talkheals-gold text-talkheals-mid hover:text-talkheals-deep rounded-xl text-xs font-bold transition-all duration-200"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    @click="confirmDelete(client)"
                                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-500 border border-red-100 text-red-400 hover:text-white rounded-xl text-xs font-bold transition-all duration-200"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="clients.last_page > 1" class="flex items-center justify-between px-8 py-5 border-t border-talkheals-gold-p/60">
                            <div class="text-xs text-talkheals-muted">
                                Showing {{ clients.from }}–{{ clients.to }} of {{ clients.total }} clients
                            </div>
                            <div class="flex gap-2">
                                <a v-if="clients.prev_page_url" :href="clients.prev_page_url"
                                   class="px-4 py-2 bg-talkheals-cream border border-talkheals-gold-p rounded-xl text-xs font-bold text-talkheals-mid hover:border-talkheals-gold transition-all">
                                    ← Prev
                                </a>
                                <a v-if="clients.next_page_url" :href="clients.next_page_url"
                                   class="px-4 py-2 bg-talkheals-gold text-white rounded-xl text-xs font-bold hover:bg-talkheals-deep transition-all">
                                    Next →
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════
         SLIDE-OVER DRAWER  (Create / Edit)
    ════════════════════════════════════════════════════════ -->
    <Teleport to="body">
        <transition name="overlay-fade">
            <div
                v-if="drawerMode"
                class="fixed inset-0 z-50"
                style="background:rgba(42,36,32,.45);backdrop-filter:blur(4px)"
                @click.self="closeDrawer"
            ></div>
        </transition>

        <transition name="drawer-slide">
            <div
                v-if="drawerMode"
                class="fixed top-0 right-0 h-full w-full max-w-[520px] z-50 bg-white shadow-2xl flex flex-col border-l border-talkheals-gold-p"
            >
                <!-- Drawer header -->
                <div class="flex items-center justify-between px-8 py-6 border-b border-talkheals-gold-p flex-shrink-0">
                    <div>
                        <h2 class="font-serif text-2xl font-light text-talkheals-deep">
                            {{ drawerMode === 'create' ? 'Add New Client' : drawerClient?.name }}
                        </h2>
                        <p class="text-talkheals-muted text-xs mt-0.5">
                            {{ drawerMode === 'create' ? 'Create a new client account' : drawerClient?.email }}
                        </p>
                    </div>
                    <button
                        @click="closeDrawer"
                        class="w-9 h-9 rounded-xl bg-talkheals-cream hover:bg-talkheals-gold-p border border-talkheals-gold-p flex items-center justify-center text-talkheals-muted hover:text-talkheals-deep transition-all text-sm font-bold"
                    >✕</button>
                </div>

                <!-- Tabs (view mode) -->
                <div v-if="drawerMode === 'view'" class="flex border-b border-talkheals-gold-p flex-shrink-0">
                    <button
                        v-for="tab in [{ key:'details', label:'Profile Details' }, { key:'bookings', label:'Booking History' }]"
                        :key="tab.key"
                        @click="viewTab = tab.key"
                        class="flex-1 py-3.5 text-xs font-bold uppercase tracking-widest transition-all"
                        :class="viewTab === tab.key
                            ? 'text-talkheals-sky border-b-2 border-talkheals-sky'
                            : 'text-talkheals-muted hover:text-talkheals-deep'"
                    >{{ tab.label }}</button>
                </div>

                <!-- Tabs (edit only) -->
                <div v-if="drawerMode === 'edit'" class="flex border-b border-talkheals-gold-p flex-shrink-0">
                    <button
                        v-for="tab in [{ key:'basic', label:'Basic Info' }, { key:'profile', label:'Clinical Profile' }]"
                        :key="tab.key"
                        @click="drawerTab = tab.key"
                        class="flex-1 py-3.5 text-xs font-bold uppercase tracking-widest transition-all"
                        :class="drawerTab === tab.key
                            ? 'text-talkheals-gold border-b-2 border-talkheals-gold'
                            : 'text-talkheals-muted hover:text-talkheals-deep'"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Drawer body (scrollable) -->
                <div class="flex-1 overflow-y-auto px-8 py-6">

                    <!-- ══════════════════════════════
                         VIEW MODE
                    ══════════════════════════════ -->

                    <!-- Loading spinner -->
                    <div v-if="drawerMode === 'view' && loadingView" class="flex justify-center py-16">
                        <div class="w-8 h-8 border-2 border-talkheals-gold border-t-transparent rounded-full animate-spin"></div>
                    </div>

                    <!-- VIEW: Profile Details tab -->
                    <div v-if="drawerMode === 'view' && !loadingView && viewTab === 'details' && viewData" class="space-y-6">

                        <!-- Client hero card -->
                        <div class="flex items-center gap-4 p-5 bg-talkheals-cream rounded-2xl border border-talkheals-gold-p">
                            <div
                                class="w-14 h-14 rounded-2xl bg-gradient-to-br flex items-center justify-center text-talkheals-deep text-lg font-bold border border-talkheals-gold-p flex-shrink-0"
                                :class="avatarGrad(viewData.client.name)"
                            >
                                {{ initials(viewData.client.name) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-talkheals-deep text-base">{{ viewData.client.name }}</div>
                                <div class="text-talkheals-muted text-xs mt-0.5">{{ viewData.client.email }}</div>
                                <div class="text-talkheals-muted text-xs mt-0.5">Joined {{ formatDate(viewData.client.created_at) }}</div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="font-serif text-2xl font-light text-talkheals-deep">{{ viewData.bookings.length }}</div>
                                <div class="text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold">Bookings</div>
                            </div>
                        </div>

                        <!-- Personal Details -->
                        <div>
                            <div class="section-heading">Personal Details</div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="view-field">
                                    <div class="view-field-label">Preferred Name</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.preferred_name || '—' }}</div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Pronouns</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.pronouns || '—' }}</div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Date of Birth</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.date_of_birth ? formatDate(viewData.client.profile.date_of_birth) : '—' }}</div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Gender Identity</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.gender_identity || '—' }}</div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Phone</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.phone || '—' }}</div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Occupation</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.occupation || '—' }}</div>
                                </div>
                                <div class="view-field col-span-2">
                                    <div class="view-field-label">Timezone</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.timezone || '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div>
                            <div class="section-heading">Emergency Contact</div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="view-field col-span-2">
                                    <div class="view-field-label">Name</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.emergency_contact_name || '—' }}</div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Phone</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.emergency_contact_phone || '—' }}</div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Relationship</div>
                                    <div class="view-field-val">{{ viewData.client.profile?.emergency_contact_relationship || '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Clinical Context -->
                        <div>
                            <div class="section-heading">Clinical Context</div>
                            <div class="space-y-3">
                                <div class="view-field">
                                    <div class="view-field-label">Previous Therapy Experience</div>
                                    <div class="view-field-val">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[0.6rem] font-bold border"
                                            :class="viewData.client.profile?.previous_therapy_experience
                                                ? 'bg-talkheals-sage/10 border-talkheals-sage/20 text-talkheals-sage'
                                                : 'bg-talkheals-gold-p border-talkheals-gold-p text-talkheals-muted'"
                                        >
                                            {{ viewData.client.profile?.previous_therapy_experience ? '✓ Yes' : '✕ No' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Reason for Therapy</div>
                                    <div class="view-field-val whitespace-pre-wrap leading-relaxed">{{ viewData.client.profile?.reason_for_therapy || '—' }}</div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Current Medications</div>
                                    <div class="view-field-val whitespace-pre-wrap leading-relaxed">{{ viewData.client.profile?.current_medications || '—' }}</div>
                                </div>
                                <div class="view-field">
                                    <div class="view-field-label">Mental Health History</div>
                                    <div class="view-field-val whitespace-pre-wrap leading-relaxed">{{ viewData.client.profile?.mental_health_history || '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VIEW: Booking History tab -->
                    <div v-if="drawerMode === 'view' && !loadingView && viewTab === 'bookings' && viewData">
                        <div v-if="!viewData.bookings.length" class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="text-4xl mb-3 opacity-30">📅</div>
                            <div class="font-serif text-lg text-talkheals-deep font-light mb-1">No bookings yet</div>
                            <div class="text-talkheals-muted text-xs">This client hasn't made any bookings.</div>
                        </div>

                        <div v-else class="space-y-3">
                            <div
                                v-for="booking in viewData.bookings"
                                :key="booking.id"
                                class="p-4 rounded-2xl border transition-all"
                                :class="['pending','confirmed'].includes(booking.status)
                                    ? 'bg-white border-talkheals-gold-p'
                                    : 'bg-talkheals-cream/60 border-talkheals-gold-p/60'"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <!-- Session type + type badge -->
                                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                            <span class="font-bold text-sm text-talkheals-deep">{{ booking.session_type || 'Session' }}</span>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-lg text-[0.58rem] font-bold border uppercase tracking-widest"
                                                :class="booking.type === 'free'
                                                    ? 'bg-talkheals-sage/10 border-talkheals-sage/20 text-talkheals-sage'
                                                    : 'bg-talkheals-gold/10 border-talkheals-gold/20 text-talkheals-gold'"
                                            >{{ booking.type === 'free' ? '🎁 Free' : '💳 Paid' }}</span>
                                        </div>
                                        <!-- Date + time -->
                                        <div class="text-xs text-talkheals-mid">
                                            {{ formatDate(booking.booking_date) }} · {{ formatTime(booking.start_time) }} – {{ formatTime(booking.end_time) }}
                                        </div>
                                        <!-- Notes -->
                                        <div v-if="booking.client_notes" class="text-xs text-talkheals-muted italic mt-1 truncate">
                                            "{{ booking.client_notes }}"
                                        </div>
                                        <!-- Cancellation reason -->
                                        <div v-if="booking.status === 'cancelled' && booking.cancellation_reason" class="text-xs text-red-400 mt-1">
                                            Cancelled: {{ booking.cancellation_reason }}
                                        </div>
                                    </div>
                                    <!-- Status badge -->
                                    <span
                                        class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[0.58rem] font-bold border uppercase tracking-widest"
                                        :class="{
                                            'bg-talkheals-gold/10 border-talkheals-gold/20 text-talkheals-gold': booking.status === 'pending',
                                            'bg-talkheals-sage/10 border-talkheals-sage/20 text-talkheals-sage': booking.status === 'confirmed',
                                            'bg-talkheals-sky/10  border-talkheals-sky/20  text-talkheals-sky':  booking.status === 'completed',
                                            'bg-talkheals-muted/10 border-talkheals-muted/20 text-talkheals-muted': booking.status === 'cancelled',
                                        }"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full" :class="{
                                            'bg-talkheals-gold': booking.status === 'pending',
                                            'bg-talkheals-sage': booking.status === 'confirmed',
                                            'bg-talkheals-sky':  booking.status === 'completed',
                                            'bg-talkheals-muted': booking.status === 'cancelled',
                                        }"></span>
                                        {{ booking.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── CREATE FORM ── -->
                    <div v-if="drawerMode === 'create'" class="space-y-5">
                        <div>
                            <label class="field-label">Full Name</label>
                            <input v-model="createForm.name" type="text" class="field-input" placeholder="Jane Doe" />
                            <div v-if="createForm.errors.name" class="field-error">{{ createForm.errors.name }}</div>
                        </div>
                        <div>
                            <label class="field-label">Email Address</label>
                            <input v-model="createForm.email" type="email" class="field-input" placeholder="jane@example.com" />
                            <div v-if="createForm.errors.email" class="field-error">{{ createForm.errors.email }}</div>
                        </div>
                        <div>
                            <label class="field-label">Password</label>
                            <input v-model="createForm.password" type="password" class="field-input" placeholder="Min. 8 characters" />
                            <div v-if="createForm.errors.password" class="field-error">{{ createForm.errors.password }}</div>
                        </div>
                        <div>
                            <label class="field-label">Confirm Password</label>
                            <input v-model="createForm.password_confirmation" type="password" class="field-input" placeholder="Repeat password" />
                        </div>
                    </div>

                    <!-- ── EDIT: Basic Info Tab ── -->
                    <div v-if="drawerMode === 'edit' && drawerTab === 'basic'" class="space-y-5">
                        <div>
                            <label class="field-label">Full Name</label>
                            <input v-model="editForm.name" type="text" class="field-input" />
                            <div v-if="editForm.errors.name" class="field-error">{{ editForm.errors.name }}</div>
                        </div>
                        <div>
                            <label class="field-label">Email Address</label>
                            <input v-model="editForm.email" type="email" class="field-input" />
                            <div v-if="editForm.errors.email" class="field-error">{{ editForm.errors.email }}</div>
                        </div>

                        <div class="pt-2 pb-1 border-t border-talkheals-gold-p">
                            <p class="text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold mb-4">
                                Reset Password <span class="normal-case font-normal">(leave blank to keep current)</span>
                            </p>
                            <div class="space-y-4">
                                <div>
                                    <label class="field-label">New Password</label>
                                    <input v-model="editForm.password" type="password" class="field-input" placeholder="Min. 8 characters" />
                                    <div v-if="editForm.errors.password" class="field-error">{{ editForm.errors.password }}</div>
                                </div>
                                <div>
                                    <label class="field-label">Confirm New Password</label>
                                    <input v-model="editForm.password_confirmation" type="password" class="field-input" placeholder="Repeat new password" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── EDIT: Clinical Profile Tab ── -->
                    <div v-if="drawerMode === 'edit' && drawerTab === 'profile'" class="space-y-6">

                        <!-- Personal -->
                        <div>
                            <div class="section-heading">Personal Details</div>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="field-label">Preferred Name</label>
                                        <input v-model="editForm.preferred_name" type="text" class="field-input" />
                                    </div>
                                    <div>
                                        <label class="field-label">Pronouns</label>
                                        <input v-model="editForm.pronouns" type="text" class="field-input" placeholder="she/her" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="field-label">Date of Birth</label>
                                        <input v-model="editForm.date_of_birth" type="date" class="field-input" />
                                    </div>
                                    <div>
                                        <label class="field-label">Gender Identity</label>
                                        <input v-model="editForm.gender_identity" type="text" class="field-input" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="field-label">Phone</label>
                                        <input v-model="editForm.phone" type="text" class="field-input" />
                                    </div>
                                    <div>
                                        <label class="field-label">Occupation</label>
                                        <input v-model="editForm.occupation" type="text" class="field-input" />
                                    </div>
                                </div>
                                <div>
                                    <label class="field-label">Timezone</label>
                                    <select v-model="editForm.timezone" class="field-input">
                                        <option value="">— Select —</option>
                                        <option v-for="tz in timezones" :key="tz" :value="tz">{{ tz }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div>
                            <div class="section-heading">Emergency Contact</div>
                            <div class="space-y-4">
                                <div>
                                    <label class="field-label">Name</label>
                                    <input v-model="editForm.emergency_contact_name" type="text" class="field-input" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="field-label">Phone</label>
                                        <input v-model="editForm.emergency_contact_phone" type="text" class="field-input" />
                                    </div>
                                    <div>
                                        <label class="field-label">Relationship</label>
                                        <input v-model="editForm.emergency_contact_relationship" type="text" class="field-input" placeholder="Spouse, Parent…" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Clinical -->
                        <div>
                            <div class="section-heading">Clinical Context</div>
                            <div class="space-y-4">
                                <div>
                                    <label class="field-label">Reason for Therapy</label>
                                    <textarea v-model="editForm.reason_for_therapy" rows="3" class="field-input resize-none"></textarea>
                                </div>
                                <div class="flex items-center justify-between py-2 px-4 bg-talkheals-cream rounded-xl border border-talkheals-gold-p">
                                    <div>
                                        <div class="text-sm font-bold text-talkheals-deep">Previous Therapy Experience</div>
                                        <div class="text-xs text-talkheals-muted">Has attended therapy before</div>
                                    </div>
                                    <button
                                        type="button"
                                        @click="editForm.previous_therapy_experience = !editForm.previous_therapy_experience"
                                        class="relative w-10 h-5 rounded-full transition-all duration-300"
                                        :class="editForm.previous_therapy_experience ? 'bg-talkheals-gold' : 'bg-talkheals-gold-p'"
                                    >
                                        <span
                                            class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow-sm transition-transform duration-300"
                                            :class="editForm.previous_therapy_experience ? 'translate-x-5' : 'translate-x-0'"
                                        ></span>
                                    </button>
                                </div>
                                <div>
                                    <label class="field-label">Current Medications</label>
                                    <textarea v-model="editForm.current_medications" rows="2" class="field-input resize-none" placeholder="List any medications…"></textarea>
                                </div>
                                <div>
                                    <label class="field-label">Mental Health History</label>
                                    <textarea v-model="editForm.mental_health_history" rows="3" class="field-input resize-none" placeholder="Previous diagnoses, treatments…"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Drawer footer -->
                <div class="flex items-center gap-3 px-8 py-5 border-t border-talkheals-gold-p flex-shrink-0 bg-white">
                    <button
                        @click="closeDrawer"
                        class="flex-1 py-3 bg-talkheals-cream border border-talkheals-gold-p rounded-xl text-sm font-bold text-talkheals-mid hover:border-talkheals-gold transition-all"
                    >
                        {{ drawerMode === 'view' ? 'Close' : 'Cancel' }}
                    </button>

                    <!-- View mode: Edit button -->
                    <button
                        v-if="drawerMode === 'view'"
                        @click="openEdit(drawerClient)"
                        class="flex-[2] py-3 bg-talkheals-gold text-white rounded-xl text-sm font-bold hover:bg-talkheals-deep transition-all duration-300"
                    >
                        ✏️ Edit Client
                    </button>

                    <!-- Create / Edit submit -->
                    <button
                        v-if="drawerMode !== 'view'"
                        @click="drawerMode === 'create' ? submitCreate() : submitEdit()"
                        :disabled="drawerMode === 'create' ? createForm.processing : editForm.processing"
                        class="flex-[2] py-3 bg-talkheals-gold text-white rounded-xl text-sm font-bold hover:bg-talkheals-deep transition-all duration-300 disabled:opacity-50"
                    >
                        <span v-if="drawerMode === 'create' && createForm.processing">Creating…</span>
                        <span v-else-if="drawerMode === 'edit' && editForm.processing">Saving…</span>
                        <span v-else>{{ drawerMode === 'create' ? 'Create Client' : 'Save Changes' }}</span>
                    </button>
                </div>
            </div>
        </transition>
    </Teleport>

    <!-- ════════════════════════════════════════════════════════
         DELETE CONFIRMATION MODAL
    ════════════════════════════════════════════════════════ -->
    <Teleport to="body">
        <transition name="overlay-fade">
            <div
                v-if="deleteTarget"
                class="fixed inset-0 z-[60] flex items-center justify-center p-6"
                style="background:rgba(42,36,32,.55);backdrop-filter:blur(6px)"
                @click.self="closeDelete"
            >
                <div class="bg-white rounded-[28px] p-8 w-full max-w-md border border-talkheals-gold-p shadow-2xl">
                    <div class="w-14 h-14 rounded-2xl bg-red-50 border border-red-100 flex items-center justify-center text-2xl mb-5 mx-auto">🗑️</div>
                    <h3 class="font-serif text-2xl font-light text-talkheals-deep text-center mb-2">Delete Client</h3>
                    <p class="text-talkheals-muted text-sm text-center mb-1">
                        Are you sure you want to delete
                        <strong class="text-talkheals-deep">{{ deleteTarget?.name }}</strong>?
                    </p>
                    <p class="text-talkheals-muted text-xs text-center mb-8">
                        This will permanently remove their account, profile, and all booking history.
                    </p>
                    <div class="flex gap-3">
                        <button
                            @click="closeDelete"
                            class="flex-1 py-3 bg-talkheals-cream border border-talkheals-gold-p rounded-xl text-sm font-bold text-talkheals-mid hover:border-talkheals-gold transition-all"
                        >
                            Keep Client
                        </button>
                        <button
                            @click="submitDelete"
                            :disabled="deleting"
                            class="flex-1 py-3 bg-red-500 text-white rounded-xl text-sm font-bold hover:bg-red-600 transition-all disabled:opacity-50"
                        >
                            {{ deleting ? 'Deleting…' : 'Delete Permanently' }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
.stat-card {
    @apply bg-white border border-talkheals-gold-p rounded-[24px] p-6 shadow-sm shadow-talkheals-gold/5;
}
.stat-lbl {
    @apply text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold mt-1;
}
.th {
    @apply px-5 py-4 text-left text-[0.6rem] uppercase tracking-widest text-talkheals-muted font-bold;
}
.td {
    @apply px-5 py-4;
}
.field-label {
    @apply block text-[0.6rem] tracking-[0.15em] uppercase text-talkheals-muted font-bold mb-1.5;
}
.field-input {
    @apply w-full bg-talkheals-cream border border-talkheals-gold-p rounded-xl px-4 py-2.5 text-sm text-talkheals-deep
           focus:outline-none focus:border-talkheals-gold transition-all duration-200
           placeholder:text-talkheals-muted/50;
}
.field-error {
    @apply text-red-500 text-xs mt-1;
}
.section-heading {
    @apply text-[0.6rem] uppercase tracking-widest text-talkheals-gold font-bold mb-4 pb-2 border-b border-talkheals-gold-p;
}
.bg-talkheals-gold\/8 { background-color: rgb(201 169 110 / .08); }

.view-field {
    @apply bg-talkheals-cream/60 rounded-xl px-4 py-3 border border-talkheals-gold-p/60;
}
.view-field-label {
    @apply text-[0.58rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1;
}
.view-field-val {
    @apply text-sm text-talkheals-deep font-medium;
}

/* Overlay */
.overlay-fade-enter-active, .overlay-fade-leave-active { transition: opacity .25s ease; }
.overlay-fade-enter-from, .overlay-fade-leave-to       { opacity: 0; }

/* Drawer slide */
.drawer-slide-enter-active { transition: transform .35s cubic-bezier(.34,1.56,.64,1); }
.drawer-slide-leave-active { transition: transform .25s ease; }
.drawer-slide-enter-from,
.drawer-slide-leave-to     { transform: translateX(100%); }
</style>
