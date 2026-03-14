<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Sidebar from '../Components/Sidebar.vue';

const props = defineProps({
    testimonials: { type: Array, default: () => [] },
});

const flash  = computed(() => usePage().props.flash ?? {});
const search = ref('');

const filtered = computed(() => {
    const q = search.value.toLowerCase();
    return props.testimonials.filter(t =>
        t.name.toLowerCase().includes(q) ||
        (t.tag ?? '').toLowerCase().includes(q) ||
        (t.location ?? '').toLowerCase().includes(q)
    );
});

// ── Avatar gradient presets ──────────────────────────────────────────────────
const gradientPresets = [
    { label: 'Rose Gold',    value: 'linear-gradient(135deg,#c49a8a,#c9a96e)' },
    { label: 'Sage Teal',    value: 'linear-gradient(135deg,#7a9e8e,#8bb0c4)' },
    { label: 'Mauve Rose',   value: 'linear-gradient(135deg,#b8a0b8,#c49a8a)' },
    { label: 'Gold Sage',    value: 'linear-gradient(135deg,#c9a96e,#7a9e8e)' },
    { label: 'Sky Mauve',    value: 'linear-gradient(135deg,#8bb0c4,#b8a0b8)' },
    { label: 'Blush Rose',   value: 'linear-gradient(135deg,#e8cfc6,#c49a8a)' },
    { label: 'Deep Rose',    value: 'linear-gradient(135deg,#c49a8a,#9a6e82)' },
    { label: 'Forest Gold',  value: 'linear-gradient(135deg,#6a8e7e,#c9a96e)' },
];

// ── Drawer state ──────────────────────────────────────────────────────────────
const drawerOpen = ref(false);
const drawerMode = ref('create'); // 'create' | 'edit'
const editing    = ref(null);

const blankForm = {
    stars:           5,
    quote:           '',
    name:            '',
    location:        '',
    tag:             '',
    avatar_gradient: gradientPresets[0].value,
    sort_order:      0,
};

const form = useForm({ ...blankForm });

const openCreate = () => {
    drawerMode.value = 'create';
    editing.value    = null;
    form.reset();
    Object.assign(form, { ...blankForm });
    drawerOpen.value = true;
};

const openEdit = (t) => {
    drawerMode.value = 'edit';
    editing.value    = t;
    form.stars           = t.stars;
    form.quote           = t.quote;
    form.name            = t.name;
    form.location        = t.location ?? '';
    form.tag             = t.tag ?? '';
    form.avatar_gradient = t.avatar_gradient ?? gradientPresets[0].value;
    form.sort_order      = t.sort_order ?? 0;
    drawerOpen.value = true;
};

const closeDrawer = () => { drawerOpen.value = false; };

const submitForm = () => {
    if (drawerMode.value === 'create') {
        form.post(route('admin.testimonials.store'), {
            preserveScroll: true,
            onSuccess: () => closeDrawer(),
        });
    } else {
        form.patch(route('admin.testimonials.update', editing.value.id), {
            preserveScroll: true,
            onSuccess: () => closeDrawer(),
        });
    }
};

// ── Delete ────────────────────────────────────────────────────────────────────
const deleteTarget = ref(null);
const deleteForm   = useForm({});

const confirmDelete = (t) => { deleteTarget.value = t; };
const cancelDelete  = () => { deleteTarget.value = null; };

const doDelete = () => {
    deleteForm.delete(route('admin.testimonials.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => { deleteTarget.value = null; },
    });
};

// ── Toggle active ─────────────────────────────────────────────────────────────
const toggleForm = useForm({});
const toggleActive = (t) => {
    toggleForm.patch(route('admin.testimonials.toggle', t.id), { preserveScroll: true });
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const initial   = (name) => name ? name.charAt(0).toUpperCase() : '?';
const starsStr  = (n)    => '★'.repeat(n) + '☆'.repeat(5 - n);
const totalActive   = computed(() => props.testimonials.filter(t => t.is_active).length);
</script>

<template>
    <div class="flex min-h-screen bg-talkheals-cream font-sans">
        <Sidebar />

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="bg-white border-b border-talkheals-gold-p px-8 py-5 flex items-center justify-between sticky top-0 z-20">
                <div>
                    <h1 class="text-2xl font-bold text-talkheals-deep tracking-tight">Testimonials</h1>
                    <p class="text-sm text-talkheals-muted mt-0.5">Manage client healing stories shown in the Reviews carousel.</p>
                </div>
                <button @click="openCreate"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-talkheals-gold text-white font-bold text-sm shadow-lg shadow-talkheals-gold/20 hover:bg-talkheals-gold/90 transition">
                    <span class="text-lg leading-none">+</span> Add Testimonial
                </button>
            </header>

            <!-- Flash -->
            <transition name="fade">
                <div v-if="flash.success" class="mx-8 mt-6 px-5 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ flash.success }}
                </div>
            </transition>

            <!-- Main -->
            <main class="flex-1 overflow-y-auto p-8 space-y-6">

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl p-5 border border-talkheals-gold-p">
                        <div class="text-2xl font-bold text-talkheals-deep">{{ testimonials.length }}</div>
                        <div class="text-xs text-talkheals-muted mt-1 uppercase tracking-widest font-semibold">Total</div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-talkheals-gold-p">
                        <div class="text-2xl font-bold text-green-600">{{ totalActive }}</div>
                        <div class="text-xs text-talkheals-muted mt-1 uppercase tracking-widest font-semibold">Visible</div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-talkheals-gold-p">
                        <div class="text-2xl font-bold text-talkheals-gold">{{ testimonials.length - totalActive }}</div>
                        <div class="text-xs text-talkheals-muted mt-1 uppercase tracking-widest font-semibold">Hidden</div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-talkheals-gold-p">
                        <div class="text-2xl font-bold text-talkheals-rose">★ 5.0</div>
                        <div class="text-xs text-talkheals-muted mt-1 uppercase tracking-widest font-semibold">Avg Rating</div>
                    </div>
                </div>

                <!-- Search -->
                <div class="bg-white rounded-2xl border border-talkheals-gold-p p-4 flex items-center gap-3">
                    <svg class="w-4 h-4 text-talkheals-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input v-model="search" type="text" placeholder="Search by name, tag or location…"
                        class="flex-1 text-sm text-talkheals-deep bg-transparent outline-none placeholder:text-talkheals-muted/50">
                </div>

                <!-- Cards grid -->
                <div v-if="filtered.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    <div v-for="t in filtered" :key="t.id"
                        class="bg-white rounded-2xl border border-talkheals-gold-p p-6 flex flex-col gap-4 relative"
                        :class="{ 'opacity-50': !t.is_active }">

                        <!-- Status badge -->
                        <div class="absolute top-4 right-4 flex items-center gap-2">
                            <span class="text-[0.6rem] uppercase tracking-widest font-bold"
                                :class="t.is_active ? 'text-green-500' : 'text-talkheals-muted'">
                                {{ t.is_active ? 'Visible' : 'Hidden' }}
                            </span>
                        </div>

                        <!-- Stars -->
                        <div class="text-talkheals-gold text-sm tracking-widest">{{ starsStr(t.stars) }}</div>

                        <!-- Quote -->
                        <p class="text-sm text-talkheals-muted font-light leading-relaxed line-clamp-4 font-serif italic">
                            "{{ t.quote }}"
                        </p>

                        <!-- Author -->
                        <div class="flex items-center gap-3 mt-auto">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm shrink-0"
                                :style="{ background: t.avatar_gradient || 'linear-gradient(135deg,#c49a8a,#c9a96e)' }">
                                {{ initial(t.name) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-talkheals-deep truncate">{{ t.name }}</div>
                                <div v-if="t.location" class="text-xs text-talkheals-muted">{{ t.location }}</div>
                                <div v-if="t.tag" class="inline-block mt-1 px-2 py-0.5 rounded-full bg-talkheals-gold/10 text-talkheals-gold text-[0.6rem] font-semibold uppercase tracking-wide">{{ t.tag }}</div>
                            </div>
                            <div class="text-xs text-talkheals-muted shrink-0">#{{ t.sort_order }}</div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pt-2 border-t border-talkheals-gold/10">
                            <button @click="toggleActive(t)"
                                class="flex-1 py-2 rounded-xl text-xs font-semibold transition"
                                :class="t.is_active
                                    ? 'bg-talkheals-cream text-talkheals-muted hover:bg-yellow-50 hover:text-yellow-600'
                                    : 'bg-green-50 text-green-600 hover:bg-green-100'">
                                {{ t.is_active ? 'Hide' : 'Show' }}
                            </button>
                            <button @click="openEdit(t)"
                                class="flex-1 py-2 rounded-xl bg-talkheals-cream text-talkheals-deep text-xs font-semibold hover:bg-talkheals-gold/10 transition">
                                Edit
                            </button>
                            <button @click="confirmDelete(t)"
                                class="flex-1 py-2 rounded-xl bg-red-50 text-red-500 text-xs font-semibold hover:bg-red-100 transition">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-else class="bg-white rounded-3xl border border-talkheals-gold-p p-16 text-center">
                    <div class="text-5xl mb-4">💬</div>
                    <p class="text-talkheals-muted text-sm">No testimonials found. Add your first healing story.</p>
                    <button @click="openCreate" class="mt-4 px-6 py-2.5 rounded-xl bg-talkheals-gold text-white font-bold text-sm hover:bg-talkheals-gold/90 transition">
                        Add Testimonial
                    </button>
                </div>

            </main>
        </div>
    </div>

    <!-- ── Drawer ── -->
    <transition name="drawer">
        <div v-if="drawerOpen" class="fixed inset-0 z-40 flex justify-end">
            <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="closeDrawer"></div>
            <div class="relative w-full max-w-lg bg-white h-full shadow-2xl flex flex-col overflow-hidden">
                <!-- Drawer header -->
                <div class="px-8 py-6 border-b border-talkheals-gold-p flex items-center justify-between bg-gradient-to-r from-talkheals-cream to-white">
                    <div>
                        <h2 class="text-lg font-bold text-talkheals-deep">{{ drawerMode === 'create' ? 'Add Testimonial' : 'Edit Testimonial' }}</h2>
                        <p class="text-xs text-talkheals-muted mt-0.5">{{ drawerMode === 'create' ? 'Add a new client healing story.' : 'Update this testimonial.' }}</p>
                    </div>
                    <button @click="closeDrawer" class="w-9 h-9 rounded-full bg-talkheals-cream flex items-center justify-center text-talkheals-muted hover:text-talkheals-deep transition text-xl leading-none">&times;</button>
                </div>

                <!-- Drawer body -->
                <form @submit.prevent="submitForm" class="flex-1 overflow-y-auto px-8 py-6 space-y-5">

                    <!-- Stars -->
                    <div>
                        <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Rating</label>
                        <div class="flex gap-2">
                            <button v-for="s in 5" :key="s" type="button"
                                @click="form.stars = s"
                                class="text-2xl transition"
                                :class="s <= form.stars ? 'text-talkheals-gold' : 'text-talkheals-gold/20'">★</button>
                        </div>
                        <p v-if="form.errors.stars" class="text-red-500 text-xs mt-1">{{ form.errors.stars }}</p>
                    </div>

                    <!-- Quote -->
                    <div>
                        <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Quote / Testimonial</label>
                        <textarea v-model="form.quote" rows="4" maxlength="600"
                            placeholder="Client's testimonial in their own words…"
                            class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition resize-none"></textarea>
                        <div class="flex justify-between mt-1">
                            <p v-if="form.errors.quote" class="text-red-500 text-xs">{{ form.errors.quote }}</p>
                            <span class="text-xs text-talkheals-muted ml-auto">{{ form.quote.length }}/600</span>
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Client Name</label>
                        <input v-model="form.name" type="text" maxlength="100" placeholder="e.g. Priya R."
                            class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Location + Tag -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Location</label>
                            <input v-model="form.location" type="text" maxlength="100" placeholder="e.g. Toronto, ON"
                                class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Tag</label>
                            <input v-model="form.tag" type="text" maxlength="80" placeholder="e.g. Anxiety"
                                class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                        </div>
                    </div>

                    <!-- Avatar colour -->
                    <div>
                        <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-3">Avatar Colour</label>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="p in gradientPresets" :key="p.value" type="button"
                                @click="form.avatar_gradient = p.value"
                                class="w-9 h-9 rounded-full border-2 transition"
                                :style="{ background: p.value }"
                                :class="form.avatar_gradient === p.value ? 'border-talkheals-deep scale-110' : 'border-transparent'"
                                :title="p.label">
                            </button>
                        </div>
                        <!-- Live preview -->
                        <div class="flex items-center gap-3 mt-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm"
                                :style="{ background: form.avatar_gradient }">
                                {{ initial(form.name || '?') }}
                            </div>
                            <span class="text-xs text-talkheals-muted">Preview</span>
                        </div>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-xs font-bold text-talkheals-deep uppercase tracking-widest mb-2">Sort Order <span class="font-normal normal-case tracking-normal text-talkheals-muted">— lower = shown first</span></label>
                        <input v-model.number="form.sort_order" type="number" min="0" max="999"
                            class="w-full px-4 py-3 rounded-xl border border-talkheals-gold/30 bg-talkheals-cream/50 text-talkheals-deep text-sm focus:outline-none focus:ring-2 focus:ring-talkheals-gold/40 transition">
                    </div>

                </form>

                <!-- Drawer footer -->
                <div class="px-8 py-5 border-t border-talkheals-gold/10 flex gap-3">
                    <button type="button" @click="closeDrawer"
                        class="flex-1 py-3 rounded-2xl border border-talkheals-gold/30 text-talkheals-muted font-semibold text-sm hover:bg-talkheals-cream transition">
                        Cancel
                    </button>
                    <button @click="submitForm" :disabled="form.processing"
                        class="flex-1 py-3 rounded-2xl bg-talkheals-gold text-white font-bold text-sm shadow-lg shadow-talkheals-gold/20 hover:bg-talkheals-gold/90 disabled:opacity-60 transition flex items-center justify-center gap-2">
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        {{ form.processing ? 'Saving…' : (drawerMode === 'create' ? 'Add Testimonial' : 'Save Changes') }}
                    </button>
                </div>
            </div>
        </div>
    </transition>

    <!-- ── Delete Confirm Modal ── -->
    <transition name="fade">
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="cancelDelete"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-3xl mx-auto mb-4">🗑️</div>
                    <h3 class="text-lg font-bold text-talkheals-deep">Delete Testimonial?</h3>
                    <p class="text-sm text-talkheals-muted mt-2">
                        "<span class="font-medium text-talkheals-deep">{{ deleteTarget.name }}</span>'s" testimonial will be permanently removed from the carousel.
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

.drawer-enter-active, .drawer-leave-active { transition: all .3s ease; }
.drawer-enter-from .relative, .drawer-leave-to .relative { transform: translateX(100%); }
</style>
