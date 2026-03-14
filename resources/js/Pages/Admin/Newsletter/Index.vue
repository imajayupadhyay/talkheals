<script setup>
import { ref, computed } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import Sidebar from '../Components/Sidebar.vue';

const props = defineProps({
    subscribers: { type: Array,  default: () => [] },
    filters:     { type: Object, default: () => ({}) },
    stats:       { type: Object, default: () => ({}) },
});

const flash  = computed(() => usePage().props.flash ?? {});

// ── Search / Filter ───────────────────────────────────────────────────────────
const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

const applyFilters = () => {
    router.get(route('admin.newsletter'), {
        search: search.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
};

const clearFilters = () => {
    search.value = '';
    status.value = '';
    applyFilters();
};

// ── Toggle / Delete ───────────────────────────────────────────────────────────
const toggleForm  = useForm({});
const deleteForm  = useForm({});
const deleteTarget = ref(null);

const toggleActive = (s) => {
    toggleForm.patch(route('admin.newsletter.toggle', s.id), { preserveScroll: true });
};

const confirmDelete = (s) => { deleteTarget.value = s; };
const cancelDelete  = () => { deleteTarget.value = null; };

const doDelete = () => {
    deleteForm.delete(route('admin.newsletter.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => { deleteTarget.value = null; },
    });
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatDate = (dt) => {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString('en-CA', { year: 'numeric', month: 'short', day: 'numeric' });
};

const initial = (email) => email ? email.charAt(0).toUpperCase() : '?';

// Avatar colours cycle
const avatarColors = [
    'linear-gradient(135deg,#c49a8a,#c9a96e)',
    'linear-gradient(135deg,#7a9e8e,#8bb0c4)',
    'linear-gradient(135deg,#b8a0b8,#c49a8a)',
    'linear-gradient(135deg,#c9a96e,#7a9e8e)',
    'linear-gradient(135deg,#8bb0c4,#b8a0b8)',
];
const avatarColor = (id) => avatarColors[id % avatarColors.length];
</script>

<template>
    <div class="flex min-h-screen bg-talkheals-cream font-sans">
        <Sidebar />

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="bg-white border-b border-talkheals-gold-p px-8 py-5 flex items-center justify-between sticky top-0 z-20">
                <div>
                    <h1 class="text-2xl font-bold text-talkheals-deep tracking-tight">Newsletter Subscribers</h1>
                    <p class="text-sm text-talkheals-muted mt-0.5">View and manage clients who subscribed from the dashboard.</p>
                </div>
            </header>

            <!-- Flash -->
            <transition name="fade">
                <div v-if="flash.success" class="mx-8 mt-6 px-5 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ flash.success }}
                </div>
            </transition>

            <main class="flex-1 overflow-y-auto p-8 space-y-6">

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl p-5 border border-talkheals-gold-p">
                        <div class="text-2xl font-bold text-talkheals-deep">{{ stats.total ?? 0 }}</div>
                        <div class="text-xs text-talkheals-muted mt-1 uppercase tracking-widest font-semibold">Total Subscribers</div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-talkheals-gold-p">
                        <div class="text-2xl font-bold text-green-600">{{ stats.active ?? 0 }}</div>
                        <div class="text-xs text-talkheals-muted mt-1 uppercase tracking-widest font-semibold">Active</div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-talkheals-gold-p">
                        <div class="text-2xl font-bold text-talkheals-muted">{{ stats.unsubscribed ?? 0 }}</div>
                        <div class="text-xs text-talkheals-muted mt-1 uppercase tracking-widest font-semibold">Unsubscribed</div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-talkheals-gold-p">
                        <div class="text-2xl font-bold text-talkheals-gold">{{ stats.this_month ?? 0 }}</div>
                        <div class="text-xs text-talkheals-muted mt-1 uppercase tracking-widest font-semibold">This Month</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-2xl border border-talkheals-gold-p p-4 flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2 flex-1 min-w-48">
                        <svg class="w-4 h-4 text-talkheals-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input v-model="search" @keyup.enter="applyFilters" type="text" placeholder="Search by email…"
                            class="flex-1 text-sm text-talkheals-deep bg-transparent outline-none placeholder:text-talkheals-muted/50">
                    </div>
                    <select v-model="status" @change="applyFilters"
                        class="text-sm text-talkheals-deep bg-talkheals-cream border border-talkheals-gold/20 rounded-xl px-3 py-2 outline-none">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Unsubscribed</option>
                    </select>
                    <button @click="applyFilters" class="px-4 py-2 rounded-xl bg-talkheals-gold text-white text-sm font-semibold hover:bg-talkheals-gold/90 transition">Search</button>
                    <button v-if="search || status" @click="clearFilters" class="px-4 py-2 rounded-xl border border-talkheals-gold/30 text-talkheals-muted text-sm hover:bg-talkheals-cream transition">Clear</button>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-3xl border border-talkheals-gold-p overflow-hidden">
                    <table v-if="subscribers.length" class="w-full">
                        <thead>
                            <tr class="border-b border-talkheals-gold-p bg-talkheals-cream/50">
                                <th class="text-left px-6 py-4 text-xs font-bold text-talkheals-muted uppercase tracking-widest">Subscriber</th>
                                <th class="text-left px-6 py-4 text-xs font-bold text-talkheals-muted uppercase tracking-widest">Subscribed</th>
                                <th class="text-left px-6 py-4 text-xs font-bold text-talkheals-muted uppercase tracking-widest">Status</th>
                                <th class="text-right px-6 py-4 text-xs font-bold text-talkheals-muted uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-talkheals-gold-p">
                            <tr v-for="s in subscribers" :key="s.id"
                                class="hover:bg-talkheals-cream/30 transition"
                                :class="{ 'opacity-50': !s.is_active }">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                                            :style="{ background: avatarColor(s.id) }">
                                            {{ initial(s.email) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-talkheals-deep">{{ s.email }}</div>
                                            <div v-if="s.name" class="text-xs text-talkheals-muted">{{ s.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-talkheals-muted">{{ formatDate(s.subscribed_at) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                                        :class="s.is_active
                                            ? 'bg-green-50 text-green-700'
                                            : 'bg-talkheals-cream text-talkheals-muted'">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                            :class="s.is_active ? 'bg-green-500' : 'bg-gray-400'"></span>
                                        {{ s.is_active ? 'Active' : 'Unsubscribed' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="toggleActive(s)"
                                            class="px-3 py-1.5 rounded-xl text-xs font-semibold transition"
                                            :class="s.is_active
                                                ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100'
                                                : 'bg-green-50 text-green-700 hover:bg-green-100'">
                                            {{ s.is_active ? 'Unsubscribe' : 'Reactivate' }}
                                        </button>
                                        <button @click="confirmDelete(s)"
                                            class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-red-50 text-red-500 hover:bg-red-100 transition">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Empty state -->
                    <div v-else class="py-20 text-center">
                        <div class="text-5xl mb-4">📧</div>
                        <p class="text-talkheals-muted text-sm font-medium">No subscribers yet.</p>
                        <p class="text-talkheals-muted/60 text-xs mt-1">They'll appear here once clients subscribe from the dashboard.</p>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <transition name="fade">
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="cancelDelete"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-3xl mx-auto mb-4">🗑️</div>
                    <h3 class="text-lg font-bold text-talkheals-deep">Delete Subscriber?</h3>
                    <p class="text-sm text-talkheals-muted mt-2">
                        <span class="font-medium text-talkheals-deep">{{ deleteTarget.email }}</span> will be permanently removed.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button @click="cancelDelete" class="flex-1 py-3 rounded-2xl border border-talkheals-gold/30 text-talkheals-muted font-semibold text-sm hover:bg-talkheals-cream transition">Cancel</button>
                    <button @click="doDelete" :disabled="deleteForm.processing"
                        class="flex-1 py-3 rounded-2xl bg-red-500 text-white font-bold text-sm hover:bg-red-600 disabled:opacity-60 transition">
                        {{ deleteForm.processing ? 'Deleting…' : 'Yes, Delete' }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .25s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
