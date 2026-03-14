<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import Sidebar from '../Components/Sidebar.vue';
import Topbar  from '../Components/Topbar.vue';

const props = defineProps({
    workshops: Object,
    stats:     Object,
    filters:   Object,
});

// ── Search / Filter ───────────────────────────────────────────────────────────
const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');

let searchTimeout = null;
watch([search, status], ([s, st]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.workshops'), {
            search: s || undefined,
            status: st || undefined,
        }, { preserveScroll: true, replace: true });
    }, 350);
});

// ── Drawer ────────────────────────────────────────────────────────────────────
const drawerMode     = ref(null); // null | 'create' | 'edit'
const drawerWorkshop = ref(null);

const blankForm = () => ({
    title:            '',
    description:      '',
    workshop_date:    '',
    workshop_time:    '',
    duration_minutes: 60,
    image_file:       null,
    image_url:        '',
    status:           'upcoming',
    zoom_link:        '',
    is_free:          true,
    is_active:        true,
});

const createForm = useForm(blankForm());
const editForm   = useForm(blankForm());

// image mode per drawer: 'upload' | 'url'
const createImageMode = ref('upload');
const editImageMode   = ref('upload');
const activeImageMode = computed(() =>
    drawerMode.value === 'create' ? createImageMode : editImageMode
);

// image preview
const imagePreview = ref(null);

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    activeForm.value.image_file = file;
    imagePreview.value = URL.createObjectURL(file);
};

const clearImage = () => {
    activeForm.value.image_file = null;
    activeForm.value.image_url  = '';
    imagePreview.value          = null;
};

const openCreate = () => {
    createForm.reset();
    Object.assign(createForm, blankForm());
    createImageMode.value = 'upload';
    imagePreview.value    = null;
    drawerMode.value      = 'create';
};

const openEdit = (w) => {
    drawerWorkshop.value = w;
    editForm.title            = w.title;
    editForm.description      = w.description ?? '';
    editForm.workshop_date    = w.workshop_date?.split('T')[0] ?? w.workshop_date;
    editForm.workshop_time    = w.workshop_time;
    editForm.duration_minutes = w.duration_minutes;
    editForm.image_file       = null;
    editForm.image_url        = w.image_url ?? '';
    editForm.status           = w.status;
    editForm.zoom_link        = w.zoom_link ?? '';
    editForm.is_free          = w.is_free;
    editForm.is_active        = w.is_active;

    // determine initial mode from existing image
    editImageMode.value = (w.image_url && !w.image_url.includes('/storage/')) ? 'url' : 'upload';
    imagePreview.value  = w.image_url || null;
    drawerMode.value    = 'edit';
};

const closeDrawer = () => {
    drawerMode.value     = null;
    drawerWorkshop.value = null;
    imagePreview.value   = null;
};

const submitCreate = () => {
    createForm.post(route('admin.workshops.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => closeDrawer(),
    });
};

const submitEdit = () => {
    editForm.post(route('admin.workshops.update', drawerWorkshop.value.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => closeDrawer(),
        data: { _method: 'PATCH' },
    });
};

// ── Delete ────────────────────────────────────────────────────────────────────
const deleteTarget    = ref(null);
const showDeleteModal = ref(false);
const deleteForm      = useForm({});

const confirmDelete = (w) => {
    deleteTarget.value    = w;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    deleteForm.delete(route('admin.workshops.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deleteTarget.value    = null;
        },
    });
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const statusBadge = (s) => ({
    upcoming:  { label: 'Upcoming',  cls: 'bg-emerald-50 text-emerald-700', dot: 'bg-emerald-500' },
    waitlist:  { label: 'Waitlist',  cls: 'bg-amber-50 text-amber-700',     dot: 'bg-amber-500'   },
    cancelled: { label: 'Cancelled', cls: 'bg-red-50 text-red-500',         dot: 'bg-red-400'     },
}[s] ?? { label: s, cls: 'bg-gray-50 text-gray-500', dot: 'bg-gray-400' });

// Single computed form reference so v-model works
const activeForm = computed(() => drawerMode.value === 'create' ? createForm : editForm);

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    : '—';
</script>

<template>
    <Head title="Admin — Workshops" />

    <div class="flex h-screen bg-talkheals-cream overflow-hidden">
        <Sidebar />

        <div class="flex-1 flex flex-col overflow-hidden">
            <Topbar />

            <main class="flex-1 overflow-y-auto p-8">

                <!-- Flash -->
                <div v-if="$page.props.flash?.success" class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium px-5 py-3.5 rounded-2xl">
                    <span>✓</span> {{ $page.props.flash.success }}
                </div>

                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="font-serif text-3xl font-light text-talkheals-deep">Workshops</h1>
                        <p class="text-sm text-talkheals-muted mt-1 font-light">Manage upcoming workshops shown on the client portal</p>
                    </div>
                    <button
                        @click="openCreate"
                        class="flex items-center gap-2 bg-talkheals-gold hover:bg-talkheals-deep text-white text-sm font-medium px-6 py-3 rounded-2xl transition-all duration-300 shadow-lg shadow-talkheals-gold/20 hover:shadow-none hover:-translate-y-0.5"
                    >
                        <span class="text-lg leading-none">+</span> Add Workshop
                    </button>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-4 gap-5 mb-8">
                    <div class="bg-white rounded-3xl p-6 border border-talkheals-gold-p shadow-sm">
                        <div class="text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1">Total</div>
                        <div class="text-4xl font-serif font-light text-talkheals-deep">{{ stats.total }}</div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 border border-talkheals-gold-p shadow-sm">
                        <div class="text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1">Upcoming</div>
                        <div class="text-4xl font-serif font-light text-emerald-600">{{ stats.upcoming }}</div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 border border-talkheals-gold-p shadow-sm">
                        <div class="text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1">Waitlist</div>
                        <div class="text-4xl font-serif font-light text-amber-500">{{ stats.waitlist }}</div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 border border-talkheals-gold-p shadow-sm">
                        <div class="text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold mb-1">Inactive</div>
                        <div class="text-4xl font-serif font-light text-talkheals-muted">{{ stats.inactive }}</div>
                    </div>
                </div>

                <!-- Search + Filter -->
                <div class="flex gap-4 mb-6">
                    <div class="relative flex-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-talkheals-muted">🔍</span>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search workshops…"
                            class="w-full bg-white border border-talkheals-gold-p rounded-2xl pl-11 pr-5 py-3.5 text-sm text-talkheals-deep placeholder:text-talkheals-muted/50 focus:outline-none focus:border-talkheals-gold transition"
                        />
                    </div>
                    <select
                        v-model="status"
                        class="bg-white border border-talkheals-gold-p rounded-2xl px-5 py-3.5 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                    >
                        <option value="">All statuses</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="waitlist">Waitlist</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-3xl border border-talkheals-gold-p shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-talkheals-gold-p bg-talkheals-cream/60">
                                <th class="text-left px-6 py-4 text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold">Workshop</th>
                                <th class="text-left px-6 py-4 text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold">Date & Time</th>
                                <th class="text-left px-6 py-4 text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold">Duration</th>
                                <th class="text-left px-6 py-4 text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold">Status</th>
                                <th class="text-left px-6 py-4 text-[0.65rem] uppercase tracking-widest text-talkheals-muted font-bold">Visible</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="workshops.data.length === 0">
                                <td colspan="6" class="text-center py-16 text-talkheals-muted font-light">No workshops found. Create your first one!</td>
                            </tr>
                            <tr
                                v-for="w in workshops.data"
                                :key="w.id"
                                class="border-b border-talkheals-gold-p/40 hover:bg-talkheals-cream/40 transition-colors"
                            >
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-talkheals-deep">{{ w.title }}</div>
                                    <div v-if="w.description" class="text-xs text-talkheals-muted font-light mt-0.5 truncate max-w-xs">{{ w.description }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-talkheals-mid">{{ formatDate(w.workshop_date) }}</div>
                                    <div class="text-xs text-talkheals-muted mt-0.5">{{ w.workshop_time }}</div>
                                </td>
                                <td class="px-6 py-4 text-talkheals-mid">{{ w.duration_minutes }} min</td>
                                <td class="px-6 py-4">
                                    <span :class="['inline-flex items-center gap-1.5 text-[0.7rem] font-bold uppercase tracking-wider px-3 py-1 rounded-full', statusBadge(w.status).cls]">
                                        <span :class="['w-1.5 h-1.5 rounded-full', statusBadge(w.status).dot]"></span>
                                        {{ statusBadge(w.status).label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="w.is_active" class="text-xs font-semibold text-emerald-600">Yes</span>
                                    <span v-else class="text-xs font-semibold text-talkheals-muted">Hidden</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEdit(w)" class="px-4 py-2 text-xs font-semibold text-talkheals-gold border border-talkheals-gold-p rounded-xl hover:bg-talkheals-cream transition">Edit</button>
                                        <button @click="confirmDelete(w)" class="px-4 py-2 text-xs font-semibold text-red-400 border border-red-100 rounded-xl hover:bg-red-50 transition">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="workshops.last_page > 1" class="flex items-center justify-between px-6 py-4 border-t border-talkheals-gold-p/40">
                        <div class="text-xs text-talkheals-muted font-light">Showing {{ workshops.from }}–{{ workshops.to }} of {{ workshops.total }}</div>
                        <div class="flex gap-1">
                            <component
                                :is="link.url ? 'a' : 'span'"
                                v-for="link in workshops.links"
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                class="px-3 py-1.5 rounded-lg text-xs transition"
                                :class="link.active ? 'bg-talkheals-gold text-white font-bold' : link.url ? 'text-talkheals-mid hover:bg-talkheals-cream cursor-pointer' : 'text-talkheals-muted/30 cursor-default'"
                                @click.prevent="link.url && router.get(link.url)"
                            />
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- ── Create / Edit Drawer (shared template) ─────────────────────────── -->
    <Transition name="drawer">
        <div v-if="drawerMode" class="fixed inset-0 z-50 flex">
            <div class="flex-1 bg-black/30 backdrop-blur-sm" @click="closeDrawer"></div>
            <div class="w-full max-w-lg bg-white h-full shadow-2xl flex flex-col overflow-y-auto">

                <!-- Header -->
                <div class="flex items-center justify-between px-8 py-6 border-b border-talkheals-gold-p">
                    <div>
                        <h2 class="font-serif text-2xl font-light text-talkheals-deep">
                            {{ drawerMode === 'create' ? 'New Workshop' : 'Edit Workshop' }}
                        </h2>
                        <p class="text-xs text-talkheals-muted mt-0.5">
                            {{ drawerMode === 'create' ? 'Fill in the details below' : drawerWorkshop?.title }}
                        </p>
                    </div>
                    <button @click="closeDrawer" class="text-talkheals-muted hover:text-talkheals-deep transition text-2xl leading-none">&times;</button>
                </div>

                <!-- Form -->
                <form @submit.prevent="drawerMode === 'create' ? submitCreate() : submitEdit()" class="flex-1 px-8 py-6 space-y-5">

                    <!-- Title -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Title *</label>
                        <input
                            v-model="activeForm.title"
                            type="text"
                            placeholder="e.g. Anxiety & the Immigrant Mind"
                            class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                            required
                        />
                        <p v-if="activeForm.errors.title" class="text-red-500 text-xs mt-1">{{ activeForm.errors.title }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Description</label>
                        <textarea
                            v-model="activeForm.description"
                            rows="3"
                            placeholder="Brief description shown on the client portal…"
                            class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition resize-none"
                        ></textarea>
                    </div>

                    <!-- Date + Time -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Date *</label>
                            <input
                                v-model="activeForm.workshop_date"
                                type="date"
                                class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                                required
                            />
                            <p v-if="activeForm.errors.workshop_date" class="text-red-500 text-xs mt-1">{{ activeForm.errors.workshop_date }}</p>
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Time *</label>
                            <input
                                v-model="activeForm.workshop_time"
                                type="text"
                                placeholder="7:00 PM EST"
                                class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                                required
                            />
                        </div>
                    </div>

                    <!-- Duration + Status -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Duration (min) *</label>
                            <input
                                v-model="activeForm.duration_minutes"
                                type="number"
                                min="5"
                                max="480"
                                class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Status *</label>
                            <select
                                v-model="activeForm.status"
                                class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                            >
                                <option value="upcoming">Upcoming</option>
                                <option value="waitlist">Waitlist</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Image — Upload or URL -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-2">Workshop Image</label>

                        <!-- Mode toggle -->
                        <div class="flex bg-talkheals-cream p-1 rounded-xl mb-3 border border-talkheals-gold-p w-fit">
                            <button
                                type="button"
                                @click="activeImageMode.value = 'upload'; clearImage()"
                                :class="['px-4 py-1.5 rounded-lg text-xs font-medium transition-all', activeImageMode.value === 'upload' ? 'bg-white text-talkheals-deep shadow-sm' : 'text-talkheals-muted hover:text-talkheals-mid']"
                            >Upload File</button>
                            <button
                                type="button"
                                @click="activeImageMode.value = 'url'; clearImage()"
                                :class="['px-4 py-1.5 rounded-lg text-xs font-medium transition-all', activeImageMode.value === 'url' ? 'bg-white text-talkheals-deep shadow-sm' : 'text-talkheals-muted hover:text-talkheals-mid']"
                            >Image URL</button>
                        </div>

                        <!-- Upload -->
                        <div v-if="activeImageMode.value === 'upload'">
                            <div
                                class="relative border-2 border-dashed border-talkheals-gold-p rounded-xl overflow-hidden transition hover:border-talkheals-gold"
                                :class="imagePreview ? 'border-talkheals-gold' : ''"
                            >
                                <!-- Preview -->
                                <div v-if="imagePreview" class="relative">
                                    <img :src="imagePreview" alt="Preview" class="w-full h-40 object-cover" />
                                    <button
                                        type="button"
                                        @click="clearImage(); $refs.fileInput.value = ''"
                                        class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hover:bg-red-600 transition"
                                    >✕</button>
                                </div>
                                <!-- Drop zone -->
                                <label v-else class="flex flex-col items-center justify-center py-8 cursor-pointer gap-2">
                                    <span class="text-2xl">🖼️</span>
                                    <span class="text-sm text-talkheals-muted font-light">Click to upload or drag image here</span>
                                    <span class="text-xs text-talkheals-muted/60">JPG, PNG or WebP · Max 3 MB</span>
                                    <input
                                        ref="fileInput"
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        @change="onFileChange"
                                    />
                                </label>
                            </div>
                            <p v-if="activeForm.errors.image_file" class="text-red-500 text-xs mt-1">{{ activeForm.errors.image_file }}</p>
                        </div>

                        <!-- URL -->
                        <div v-else>
                            <input
                                v-model="activeForm.image_url"
                                type="url"
                                placeholder="https://images.unsplash.com/…"
                                class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                            />
                            <div v-if="activeForm.image_url" class="mt-2 rounded-xl overflow-hidden border border-talkheals-gold-p h-32">
                                <img :src="activeForm.image_url" class="w-full h-full object-cover" @error="$event.target.style.display='none'" />
                            </div>
                            <p v-if="activeForm.errors.image_url" class="text-red-500 text-xs mt-1">{{ activeForm.errors.image_url }}</p>
                        </div>
                    </div>

                    <!-- Zoom Link -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-talkheals-muted font-bold mb-1.5">Zoom / Meet Link</label>
                        <input
                            v-model="activeForm.zoom_link"
                            type="url"
                            placeholder="https://zoom.us/j/…"
                            class="w-full bg-talkheals-cream/50 border border-talkheals-gold-p rounded-xl px-4 py-3 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition"
                        />
                    </div>

                    <!-- Toggles -->
                    <div class="flex items-center gap-8 pt-1">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div
                                @click="activeForm.is_free = !activeForm.is_free"
                                :class="['w-11 h-6 rounded-full transition-colors duration-300 relative', activeForm.is_free ? 'bg-talkheals-gold' : 'bg-gray-200']"
                            >
                                <span :class="['absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-300', activeForm.is_free ? 'translate-x-5' : '']"></span>
                            </div>
                            <span class="text-sm text-talkheals-mid font-medium">Free Event</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <div
                                @click="activeForm.is_active = !activeForm.is_active"
                                :class="['w-11 h-6 rounded-full transition-colors duration-300 relative', activeForm.is_active ? 'bg-emerald-500' : 'bg-gray-200']"
                            >
                                <span :class="['absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-300', activeForm.is_active ? 'translate-x-5' : '']"></span>
                            </div>
                            <span class="text-sm text-talkheals-mid font-medium">Visible to Clients</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="closeDrawer" class="flex-1 py-3 rounded-xl border border-talkheals-gold-p text-talkheals-mid text-sm font-medium hover:bg-talkheals-cream transition">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="activeForm.processing"
                            class="flex-1 py-3 rounded-xl bg-talkheals-gold hover:bg-talkheals-deep text-white text-sm font-medium transition disabled:opacity-50"
                        >
                            {{ activeForm.processing ? 'Saving…' : drawerMode === 'create' ? 'Create Workshop' : 'Save Changes' }}
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
                <h3 class="font-serif text-xl font-light text-talkheals-deep text-center mb-2">Delete Workshop?</h3>
                <p class="text-sm text-talkheals-muted text-center font-light mb-6">
                    <strong class="text-talkheals-deep">{{ deleteTarget?.title }}</strong> will be permanently removed and will no longer appear on the client portal.
                </p>
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 py-3 rounded-xl border border-talkheals-gold-p text-talkheals-mid text-sm font-medium hover:bg-talkheals-cream transition">Cancel</button>
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
.drawer-enter-from { opacity: 0; }
.drawer-leave-to { opacity: 0; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
