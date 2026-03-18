<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue';

const user = usePage().props.auth.user;

// Search state
const isSearchOpen = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const isLoading = ref(false);
const selectedIndex = ref(-1);
const searchInputRef = ref(null);
let debounceTimer = null;

function openSearch() {
    isSearchOpen.value = true;
    searchQuery.value = '';
    searchResults.value = [];
    selectedIndex.value = -1;
    nextTick(() => searchInputRef.value?.focus());
}

function closeSearch() {
    isSearchOpen.value = false;
    searchQuery.value = '';
    searchResults.value = [];
    selectedIndex.value = -1;
}

// Keyboard shortcut: Cmd/Ctrl + K
function handleGlobalKeydown(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        if (isSearchOpen.value) {
            closeSearch();
        } else {
            openSearch();
        }
    }
    if (e.key === 'Escape' && isSearchOpen.value) {
        closeSearch();
    }
}

onMounted(() => document.addEventListener('keydown', handleGlobalKeydown));
onUnmounted(() => document.removeEventListener('keydown', handleGlobalKeydown));

// Debounced search
watch(searchQuery, (val) => {
    clearTimeout(debounceTimer);
    selectedIndex.value = -1;

    if (!val || val.trim().length < 1) {
        searchResults.value = [];
        isLoading.value = false;
        return;
    }

    isLoading.value = true;
    debounceTimer = setTimeout(async () => {
        try {
            const response = await fetch(route('admin.search') + '?q=' + encodeURIComponent(val.trim()));
            const data = await response.json();
            searchResults.value = data.results || [];
        } catch {
            searchResults.value = [];
        } finally {
            isLoading.value = false;
        }
    }, 250);
});

function handleSearchKeydown(e) {
    const total = searchResults.value.length;
    if (!total) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex.value = (selectedIndex.value + 1) % total;
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex.value = (selectedIndex.value - 1 + total) % total;
    } else if (e.key === 'Enter' && selectedIndex.value >= 0) {
        e.preventDefault();
        navigateTo(searchResults.value[selectedIndex.value]);
    }
}

function navigateTo(result) {
    closeSearch();
    router.visit(result.url);
}

function getTypeLabel(type) {
    switch (type) {
        case 'client': return 'Client';
        case 'booking': return 'Booking';
        case 'workshop': return 'Workshop';
        default: return 'Result';
    }
}

function getStatusColor(status) {
    switch (status) {
        case 'confirmed': return 'bg-talkheals-sage/10 text-talkheals-sage border-talkheals-sage/20';
        case 'pending': return 'bg-talkheals-gold/10 text-talkheals-gold border-talkheals-gold/20';
        case 'completed': return 'bg-talkheals-sky/10 text-talkheals-sky border-talkheals-sky/20';
        case 'cancelled': return 'bg-talkheals-rose/10 text-talkheals-rose border-talkheals-rose/20';
        default: return '';
    }
}

// Current breadcrumb based on route
function getBreadcrumb() {
    const currentRoute = route().current();
    const map = {
        'admin.dashboard': 'Dashboard',
        'admin.clients': 'Clients',
        'admin.bookings': 'Bookings',
        'admin.availability': 'Availability',
        'admin.workshops': 'Workshops',
        'admin.admins': 'Admin Users',
        'admin.testimonials': 'Testimonials',
        'admin.newsletter': 'Newsletter',
        'admin.broadcasting': 'Broadcasting',
        'admin.content': 'Content',
    };
    return map[currentRoute] || 'Dashboard';
}
</script>

<template>
    <header class="h-20 bg-white border-b border-talkheals-gold-p/50 flex items-center justify-between px-10 sticky top-0 z-40">
        <div class="flex items-center gap-4">
            <div class="h-10 w-[1px] bg-talkheals-gold-p hidden md:block"></div>
            <div class="text-talkheals-muted text-xs uppercase tracking-widest font-bold">Overview / {{ getBreadcrumb() }}</div>
        </div>

        <div class="flex items-center gap-8">
            <!-- Search Trigger -->
            <button
                @click="openSearch"
                class="relative hidden lg:flex items-center gap-3 bg-talkheals-cream border border-talkheals-gold-p rounded-2xl py-2.5 pl-4 pr-3 text-sm text-talkheals-muted/60 hover:border-talkheals-gold/40 hover:text-talkheals-muted transition-all w-64 group"
            >
                <svg class="w-4 h-4 text-talkheals-muted/40 group-hover:text-talkheals-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span class="flex-1 text-left">Search...</span>
                <kbd class="text-[0.6rem] font-bold text-talkheals-muted/40 bg-white border border-talkheals-gold-p rounded-lg px-1.5 py-0.5 tracking-wide">
                    <span class="mr-0.5">&#8984;</span>K
                </kbd>
            </button>

            <!-- Mobile search button -->
            <button @click="openSearch" class="lg:hidden w-10 h-10 rounded-xl flex items-center justify-center hover:bg-talkheals-cream transition-colors text-talkheals-muted">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>

            <div class="flex items-center gap-5">
                <div class="flex flex-col text-right">
                    <div class="text-[0.85rem] font-bold text-talkheals-deep tracking-wide">{{ user.name }}</div>
                    <div class="text-[0.6rem] uppercase tracking-[0.15em] text-talkheals-gold font-bold">Administrator</div>
                </div>

                <div class="relative group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-talkheals-gold to-talkheals-rose flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-talkheals-gold/10">
                        {{ user.name.charAt(0) }}
                    </div>

                    <div class="absolute right-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                        <div class="bg-white border border-talkheals-gold-p rounded-2xl p-2 w-48 shadow-2xl">
                            <Link
                                :href="route('admin.logout')"
                                method="post"
                                as="button"
                                class="w-full flex items-center gap-3 px-4 py-3 text-[#b04040] hover:bg-red-50 rounded-xl transition-all text-sm font-bold text-left"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Search Modal / Command Palette -->
    <teleport to="body">
        <transition
            enter-active-class="transition-opacity duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isSearchOpen" class="fixed inset-0 z-[100]">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-talkheals-deep/40 backdrop-blur-sm" @click="closeSearch"></div>

                <!-- Search Panel -->
                <div class="relative flex justify-center pt-[12vh] px-4">
                    <transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95 -translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 -translate-y-4"
                        appear
                    >
                        <div v-if="isSearchOpen" class="w-full max-w-2xl bg-white rounded-[24px] shadow-2xl shadow-talkheals-deep/20 border border-talkheals-gold-p overflow-hidden">

                            <!-- Search Input -->
                            <div class="flex items-center gap-3 px-6 py-5 border-b border-talkheals-gold-p/60">
                                <svg class="w-5 h-5 text-talkheals-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input
                                    ref="searchInputRef"
                                    v-model="searchQuery"
                                    @keydown="handleSearchKeydown"
                                    type="text"
                                    placeholder="Search clients, bookings, workshops..."
                                    class="flex-1 text-base text-talkheals-deep placeholder:text-talkheals-muted/40 focus:outline-none bg-transparent font-light"
                                >
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <div v-if="isLoading" class="w-4 h-4 border-2 border-talkheals-gold/30 border-t-talkheals-gold rounded-full animate-spin"></div>
                                    <button @click="closeSearch" class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-talkheals-cream border border-talkheals-gold-p/60 transition-colors text-talkheals-muted hover:text-talkheals-deep">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Results -->
                            <div class="max-h-[400px] overflow-y-auto">

                                <!-- Empty state: no query -->
                                <div v-if="!searchQuery.trim()" class="px-6 py-10 text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-talkheals-cream flex items-center justify-center mx-auto mb-4 border border-talkheals-gold-p">
                                        <svg class="w-6 h-6 text-talkheals-muted/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <p class="text-sm text-talkheals-muted font-light">Search across your clients, bookings, and workshops</p>
                                    <div class="flex items-center justify-center gap-4 mt-4">
                                        <span class="text-[0.6rem] uppercase tracking-wider text-talkheals-muted/50 font-bold">Try:</span>
                                        <button @click="searchQuery = 'pending'" class="text-xs text-talkheals-gold hover:underline">"pending"</button>
                                        <button @click="searchQuery = 'Individual'" class="text-xs text-talkheals-gold hover:underline">"Individual"</button>
                                    </div>
                                </div>

                                <!-- No results -->
                                <div v-else-if="searchQuery.trim() && !isLoading && searchResults.length === 0" class="px-6 py-10 text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-talkheals-cream flex items-center justify-center mx-auto mb-4 border border-talkheals-gold-p">
                                        <svg class="w-6 h-6 text-talkheals-muted/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
                                    </div>
                                    <p class="text-sm text-talkheals-muted font-light">No results found for "<span class="font-medium text-talkheals-deep">{{ searchQuery }}</span>"</p>
                                    <p class="text-xs text-talkheals-muted/50 mt-1">Try a different search term</p>
                                </div>

                                <!-- Results list -->
                                <div v-else-if="searchResults.length > 0" class="py-2">
                                    <!-- Group by type -->
                                    <template v-for="type in ['client', 'booking', 'workshop']" :key="type">
                                        <div v-if="searchResults.filter(r => r.type === type).length > 0">
                                            <div class="px-6 pt-3 pb-1.5">
                                                <span class="text-[0.6rem] uppercase tracking-[0.2em] font-bold text-talkheals-muted/60">{{ getTypeLabel(type) }}s</span>
                                            </div>
                                            <div
                                                v-for="result in searchResults"
                                                :key="result.type + '-' + result.id"
                                            >
                                                <button
                                                    v-if="result.type === type"
                                                    @click="navigateTo(result)"
                                                    @mouseenter="selectedIndex = searchResults.indexOf(result)"
                                                    class="w-full flex items-center gap-4 px-6 py-3 text-left transition-all duration-100"
                                                    :class="selectedIndex === searchResults.indexOf(result) ? 'bg-talkheals-gold/5' : 'hover:bg-talkheals-cream/50'"
                                                >
                                                    <!-- Type Icon -->
                                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 border"
                                                        :class="{
                                                            'bg-talkheals-sky/10 border-talkheals-sky/20': result.type === 'client',
                                                            'bg-talkheals-gold/10 border-talkheals-gold/20': result.type === 'booking',
                                                            'bg-talkheals-sage/10 border-talkheals-sage/20': result.type === 'workshop',
                                                        }"
                                                    >
                                                        <!-- Client icon -->
                                                        <svg v-if="result.type === 'client'" class="w-4 h-4 text-talkheals-sky" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                        <!-- Booking icon -->
                                                        <svg v-if="result.type === 'booking'" class="w-4 h-4 text-talkheals-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        <!-- Workshop icon -->
                                                        <svg v-if="result.type === 'workshop'" class="w-4 h-4 text-talkheals-sage" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                                    </div>

                                                    <!-- Content -->
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-sm font-medium text-talkheals-deep truncate">{{ result.title }}</span>
                                                            <span v-if="result.status" class="text-[0.55rem] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-full border" :class="getStatusColor(result.status)">{{ result.status }}</span>
                                                        </div>
                                                        <div class="text-xs text-talkheals-muted truncate mt-0.5">{{ result.subtitle }}</div>
                                                    </div>

                                                    <!-- Meta -->
                                                    <div class="flex-shrink-0 text-right">
                                                        <span class="text-[0.65rem] text-talkheals-muted/60">{{ result.meta }}</span>
                                                    </div>

                                                    <!-- Arrow -->
                                                    <svg class="w-4 h-4 text-talkheals-muted/30 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-6 py-3 border-t border-talkheals-gold-p/40 flex items-center justify-between bg-talkheals-cream/30">
                                <div class="flex items-center gap-4 text-[0.6rem] text-talkheals-muted/50 font-medium">
                                    <span class="flex items-center gap-1">
                                        <kbd class="bg-white border border-talkheals-gold-p rounded px-1 py-0.5 font-bold text-talkheals-muted/60">&#8593;</kbd>
                                        <kbd class="bg-white border border-talkheals-gold-p rounded px-1 py-0.5 font-bold text-talkheals-muted/60">&#8595;</kbd>
                                        navigate
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <kbd class="bg-white border border-talkheals-gold-p rounded px-1.5 py-0.5 font-bold text-talkheals-muted/60">&#8629;</kbd>
                                        open
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <kbd class="bg-white border border-talkheals-gold-p rounded px-1.5 py-0.5 font-bold text-talkheals-muted/60">esc</kbd>
                                        close
                                    </span>
                                </div>
                                <span v-if="searchResults.length > 0" class="text-[0.6rem] text-talkheals-muted/50 font-medium">{{ searchResults.length }} result{{ searchResults.length !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </transition>
    </teleport>
</template>
