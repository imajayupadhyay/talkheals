<script setup>
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import Sidebar from '../Components/Sidebar.vue';
import Topbar  from '../Components/Topbar.vue';

const props = defineProps({
    admins:  Object,
    stats:   Object,
    filters: Object,
});

const currentAdmin = usePage().props.auth.user;

// ── Search ────────────────────────────────────────────────────────────────────
const search = ref(props.filters?.search ?? '');
let searchTimeout = null;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.admins'), { search: val || undefined }, {
            preserveScroll: true, replace: true,
        });
    }, 350);
});

// ── Drawer ────────────────────────────────────────────────────────────────────
const drawerMode   = ref(null); // null | 'create' | 'edit'
const drawerAdmin  = ref(null);

const openCreate = () => {
    drawerAdmin.value = null;
    drawerMode.value  = 'create';
    createForm.reset();
};

const openEdit = (admin) => {
    drawerAdmin.value = admin;
    drawerMode.value  = 'edit';
    editForm.name     = admin.name;
    editForm.email    = admin.email;
    editForm.password = '';
    editForm.password_confirmation = '';
};

const closeDrawer = () => {
    drawerMode.value  = null;
    drawerAdmin.value = null;
};

// ── Forms ─────────────────────────────────────────────────────────────────────
const createForm = useForm({
    name:                  '',
    email:                 '',
    password:              '',
    password_confirmation: '',
});

const editForm = useForm({
    name:                  '',
    email:                 '',
    password:              '',
    password_confirmation: '',
});

const submitCreate = () => {
    createForm.post(route('admin.admins.store'), {
        preserveScroll: true,
        onSuccess: () => closeDrawer(),
    });
};

const submitEdit = () => {
    editForm.patch(route('admin.admins.update', drawerAdmin.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDrawer(),
    });
};

// ── Delete ────────────────────────────────────────────────────────────────────
const deleteTarget  = ref(null);
const showDeleteModal = ref(false);

const confirmDelete = (admin) => {
    deleteTarget.value  = admin;
    showDeleteModal.value = true;
};

const deleteForm = useForm({});

const executeDelete = () => {
    deleteForm.delete(route('admin.admins.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deleteTarget.value    = null;
        },
    });
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const showCreatePassword = ref(false);
const showCreateConfirm  = ref(false);
const showEditPassword   = ref(false);
const showEditConfirm    = ref(false);

const initials = (name) => name?.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) ?? '?';
const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
</script>

<template>
    <Head title="Admin — Admins" />

    <div class="flex h-screen bg-talkheals-cream overflow-hidden">
        <Sidebar />

        <div class="flex-1 flex flex-col overflow-hidden">
            <Topbar />

            <main class="flex-1 overflow-y-auto p-8">

                <!-- Flash messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium px-5 py-3.5 rounded-2xl">
                    <span>✓</span> {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium px-5 py-3.5 rounded-2xl">
                    <span>✕</span> {{ $page.props.flash.error }}
                </div>

                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="font-serif text-3xl font-light text-talkheals-deep">Admins</h1>
                        <p class="text-sm text-talkheals-muted mt-1 font-light">Manage administrator accounts</p>
                    </div>
                    <button
                        @click="openCreate"
                        class="flex items-center gap-2 bg-talkheals-gold hover:bg-talkheals-deep text-white text-sm font-medium px-6 py-3 rounded-2xl transition-all duration-300 shadow-lg shadow-talkheals-gold/20 hover:shadow-none hover:-translate-y-0.5"
                    >
                        <span class="text-lg leading-none">+</span> Add Admin
                    </button>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-5 mb-8">
                    <div class="bg-white rounded-3xl p-6 border border-talkheals-gold-p shadow-sm">
                        <div class="text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1">Total Admins</div>
                        <div class="text-4xl font-serif font-light text-talkheals-deep">{{ stats.total }}</div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 border border-talkheals-gold-p shadow-sm">
                        <div class="text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1">Added This Month</div>
                        <div class="text-4xl font-serif font-light text-talkheals-rose">{{ stats.new_this_month }}</div>
                    </div>
                </div>

                <!-- Search -->
                <div class="relative mb-6">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-talkheals-muted text-base">🔍</span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search by name or email…"
                        class="w-full bg-white border border-talkheals-gold-p rounded-2xl pl-11 pr-5 py-3.5 text-sm text-talkheals-deep placeholder:text-talkheals-muted/50 focus:outline-none focus:border-talkheals-gold transition"
                    />
                </div>

                <!-- Table -->
                <div class="bg-white rounded-3xl border border-talkheals-gold-p shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-talkheals-gold-p bg-talkheals-cream/60">
                                <th class="text-left px-6 py-4 text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold">Admin</th>
                                <th class="text-left px-6 py-4 text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold">Email</th>
                                <th class="text-left px-6 py-4 text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold">Joined</th>
                                <th class="text-left px-6 py-4 text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold">Status</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="admins.data.length === 0">
                                <td colspan="5" class="text-center py-16 text-talkheals-muted font-light">No admins found.</td>
                            </tr>
                            <tr
                                v-for="admin in admins.data"
                                :key="admin.id"
                                class="border-b border-talkheals-gold-p/40 hover:bg-talkheals-cream/40 transition-colors"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-talkheals-gold to-talkheals-rose flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ initials(admin.name) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-talkheals-deep">{{ admin.name }}</div>
                                            <div v-if="admin.id === currentAdmin.id" class="text-[0.65rem] text-talkheals-gold font-bold uppercase tracking-wider">You</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-talkheals-mid">{{ admin.email }}</td>
                                <td class="px-6 py-4 text-talkheals-muted font-light">{{ formatDate(admin.created_at) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-[0.7rem] font-bold uppercase tracking-wider px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            @click="openEdit(admin)"
                                            class="px-4 py-2 text-xs font-semibold text-talkheals-gold border border-talkheals-gold-p rounded-xl hover:bg-talkheals-cream transition"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            v-if="admin.id !== currentAdmin.id"
                                            @click="confirmDelete(admin)"
                                            class="px-4 py-2 text-xs font-semibold text-red-400 border border-red-100 rounded-xl hover:bg-red-50 transition"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="admins.last_page > 1" class="flex items-center justify-between px-6 py-4 border-t border-talkheals-gold-p/40">
                        <div class="text-xs text-talkheals-muted font-light">
                            Showing {{ admins.from }}–{{ admins.to }} of {{ admins.total }}
                        </div>
                        <div class="flex gap-1">
                            <component
                                :is="link.url ? 'a' : 'span'"
                                v-for="link in admins.links"
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                class="px-3 py-1.5 rounded-lg text-xs transition"
                                :class="link.active
                                    ? 'bg-talkheals-gold text-white font-bold'
                                    : link.url
                                        ? 'text-talkheals-mid hover:bg-talkheals-cream cursor-pointer'
                                        : 'text-talkheals-muted/30 cursor-default'"
                                @click.prevent="link.url && router.get(link.url)"
                            />
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- ── Create Drawer ──────────────────────────────────────────────────── -->
    <Transition name="drawer">
        <div v-if="drawerMode === 'create'" class="fixed inset-0 z-50 flex">
            <div class="flex-1 bg-black/30 backdrop-blur-sm" @click="closeDrawer"></div>
            <div class="w-full max-w-md bg-white h-full shadow-2xl flex flex-col overflow-y-auto">
                <div class="flex items-center justify-between px-8 py-6 border-b border-talkheals-gold-p">
                    <div>
                        <h2 class="font-serif text-2xl font-light text-talkheals-deep">New Admin</h2>
                        <p class="text-xs text-talkheals-muted mt-0.5">Create an administrator account</p>
                    </div>
                    <button @click="closeDrawer" class="text-talkheals-muted hover:text-talkheals-deep transition text-2xl leading-none">&times;</button>
                </div>

                <form @submit.prevent="submitCreate" class="flex-1 px-8 py-6 space-y-5">
                    <!-- Name -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Full Name</label>
                        <input
                            v-model="createForm.name"
                            type="text"
                            placeholder="Enter full name"
                            class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                            required
                        />
                        <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Email Address</label>
                        <input
                            v-model="createForm.email"
                            type="email"
                            placeholder="admin@example.com"
                            class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                            required
                        />
                        <p v-if="createForm.errors.email" class="text-red-500 text-xs mt-1">{{ createForm.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Password</label>
                        <div class="relative">
                            <input
                                v-model="createForm.password"
                                :type="showCreatePassword ? 'text' : 'password'"
                                placeholder="Min. 8 characters"
                                class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 pr-11 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                                required
                            />
                            <button type="button" @click="showCreatePassword = !showCreatePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-talkheals-muted hover:text-talkheals-gold transition">
                                <svg v-if="!showCreatePassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.399 8.049 7.21 5 12 5c3.79 0 7.113 2.046 9.036 5.322a1.012 1.012 0 0 1 0 .644C19.601 15.951 15.79 19 12 19c-3.79 0-7.113-2.046-9.036-5.322Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                        <p v-if="createForm.errors.password" class="text-red-500 text-xs mt-1">{{ createForm.errors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input
                                v-model="createForm.password_confirmation"
                                :type="showCreateConfirm ? 'text' : 'password'"
                                placeholder="Repeat password"
                                class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 pr-11 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                                required
                            />
                            <button type="button" @click="showCreateConfirm = !showCreateConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-talkheals-muted hover:text-talkheals-gold transition">
                                <svg v-if="!showCreateConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.399 8.049 7.21 5 12 5c3.79 0 7.113 2.046 9.036 5.322a1.012 1.012 0 0 1 0 .644C19.601 15.951 15.79 19 12 19c-3.79 0-7.113-2.046-9.036-5.322Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="closeDrawer" class="flex-1 py-3 rounded-xl border border-talkheals-gold-p text-talkheals-mid text-sm font-medium hover:bg-talkheals-cream transition">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="createForm.processing"
                            class="flex-1 py-3 rounded-xl bg-talkheals-gold hover:bg-talkheals-deep text-white text-sm font-medium transition disabled:opacity-50"
                        >
                            {{ createForm.processing ? 'Creating…' : 'Create Admin' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>

    <!-- ── Edit Drawer ────────────────────────────────────────────────────── -->
    <Transition name="drawer">
        <div v-if="drawerMode === 'edit'" class="fixed inset-0 z-50 flex">
            <div class="flex-1 bg-black/30 backdrop-blur-sm" @click="closeDrawer"></div>
            <div class="w-full max-w-md bg-white h-full shadow-2xl flex flex-col overflow-y-auto">
                <div class="flex items-center justify-between px-8 py-6 border-b border-talkheals-gold-p">
                    <div>
                        <h2 class="font-serif text-2xl font-light text-talkheals-deep">Edit Admin</h2>
                        <p class="text-xs text-talkheals-muted mt-0.5">{{ drawerAdmin?.name }}</p>
                    </div>
                    <button @click="closeDrawer" class="text-talkheals-muted hover:text-talkheals-deep transition text-2xl leading-none">&times;</button>
                </div>

                <form @submit.prevent="submitEdit" class="flex-1 px-8 py-6 space-y-5">
                    <!-- Name -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Full Name</label>
                        <input
                            v-model="editForm.name"
                            type="text"
                            class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                            required
                        />
                        <p v-if="editForm.errors.name" class="text-red-500 text-xs mt-1">{{ editForm.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Email Address</label>
                        <input
                            v-model="editForm.email"
                            type="email"
                            class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                            required
                        />
                        <p v-if="editForm.errors.email" class="text-red-500 text-xs mt-1">{{ editForm.errors.email }}</p>
                    </div>

                    <div class="border-t border-talkheals-gold-p/40 pt-4">
                        <p class="text-xs text-talkheals-muted mb-4 font-light">Leave password fields blank to keep the current password.</p>

                        <!-- New Password -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">New Password</label>
                                <div class="relative">
                                    <input
                                        v-model="editForm.password"
                                        :type="showEditPassword ? 'text' : 'password'"
                                        placeholder="Leave blank to keep current"
                                        class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 pr-11 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                                    />
                                    <button type="button" @click="showEditPassword = !showEditPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-talkheals-muted hover:text-talkheals-gold transition">
                                        <svg v-if="!showEditPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.399 8.049 7.21 5 12 5c3.79 0 7.113 2.046 9.036 5.322a1.012 1.012 0 0 1 0 .644C19.601 15.951 15.79 19 12 19c-3.79 0-7.113-2.046-9.036-5.322Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                    </button>
                                </div>
                                <p v-if="editForm.errors.password" class="text-red-500 text-xs mt-1">{{ editForm.errors.password }}</p>
                            </div>

                            <div>
                                <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Confirm New Password</label>
                                <div class="relative">
                                    <input
                                        v-model="editForm.password_confirmation"
                                        :type="showEditConfirm ? 'text' : 'password'"
                                        placeholder="Repeat new password"
                                        class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 pr-11 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                                    />
                                    <button type="button" @click="showEditConfirm = !showEditConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-talkheals-muted hover:text-talkheals-gold transition">
                                        <svg v-if="!showEditConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.399 8.049 7.21 5 12 5c3.79 0 7.113 2.046 9.036 5.322a1.012 1.012 0 0 1 0 .644C19.601 15.951 15.79 19 12 19c-3.79 0-7.113-2.046-9.036-5.322Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="closeDrawer" class="flex-1 py-3 rounded-xl border border-talkheals-gold-p text-talkheals-mid text-sm font-medium hover:bg-talkheals-cream transition">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="editForm.processing"
                            class="flex-1 py-3 rounded-xl bg-talkheals-gold hover:bg-talkheals-deep text-white text-sm font-medium transition disabled:opacity-50"
                        >
                            {{ editForm.processing ? 'Saving…' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>

    <!-- ── Delete Modal ───────────────────────────────────────────────────── -->
    <Transition name="fade">
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl">
                <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center text-2xl mb-5 mx-auto">🗑️</div>
                <h3 class="font-serif text-xl font-light text-talkheals-deep text-center mb-2">Delete Admin?</h3>
                <p class="text-sm text-talkheals-muted text-center font-light mb-6">
                    <strong class="text-talkheals-deep">{{ deleteTarget?.name }}</strong> will be permanently removed and will lose access to the admin panel.
                </p>
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 py-3 rounded-xl border border-talkheals-gold-p text-talkheals-mid text-sm font-medium hover:bg-talkheals-cream transition">
                        Cancel
                    </button>
                    <button
                        @click="executeDelete"
                        :disabled="deleteForm.processing"
                        class="flex-1 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-medium transition disabled:opacity-50"
                    >
                        {{ deleteForm.processing ? 'Deleting…' : 'Yes, Delete' }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.drawer-enter-active, .drawer-leave-active { transition: opacity 0.25s ease; }
.drawer-enter-active .max-w-md, .drawer-leave-active .max-w-md { transition: transform 0.3s ease; }
.drawer-enter-from { opacity: 0; }
.drawer-enter-from .max-w-md { transform: translateX(100%); }
.drawer-leave-to { opacity: 0; }
.drawer-leave-to .max-w-md { transform: translateX(100%); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
