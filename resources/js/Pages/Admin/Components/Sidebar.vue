<script setup>
import { Link } from '@inertiajs/vue3';

const menuItems = [
    { name: 'Dashboard',    icon: '📊',  route: 'admin.dashboard'   },
    { name: 'Availability', icon: '🗓️',  route: 'admin.availability' },
];
</script>

<template>
    <aside class="w-72 bg-white border-r border-talkheals-gold-p flex flex-col h-screen sticky top-0">
        <div class="p-8">
            <img src="/talkhealslogo.webp" alt="TalkHeals Admin" class="h-12 w-auto opacity-90">
            <div class="mt-2 text-[0.6rem] tracking-[0.2em] uppercase text-talkheals-gold font-bold">Admin Control Center</div>
        </div>

        <nav class="flex-1 px-4 space-y-2">
            <template v-for="item in menuItems" :key="item.name">
                <Link 
                    :href="item.route !== '#' ? route(item.route) : '#'"
                    class="flex items-center gap-4 px-5 py-3.5 rounded-2xl transition-all duration-300 group"
                    :class="route().current(item.route) 
                        ? 'bg-talkheals-gold text-white font-bold shadow-lg shadow-talkheals-gold/20' 
                        : 'text-talkheals-mid hover:text-talkheals-deep hover:bg-talkheals-cream'"
                >
                    <span class="text-xl group-hover:scale-110 transition-transform">{{ item.icon }}</span>
                    <span class="text-[0.9rem] tracking-wide font-medium">{{ item.name }}</span>
                </Link>
            </template>
        </nav>

        <div class="p-6 border-t border-talkheals-gold-p/30">
            <div class="bg-talkheals-cream rounded-2xl p-4 border border-talkheals-gold-p">
                <div class="text-[0.65rem] uppercase tracking-widest text-talkheals-muted mb-1 font-bold">System Version</div>
                <div class="text-talkheals-deep text-xs font-bold">TalkHeals v1.0.4</div>
            </div>
        </div>
    </aside>
</template>
