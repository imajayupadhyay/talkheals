<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
</script>

<template>
    <header class="h-20 bg-white border-b border-talkheals-gold-p/50 flex items-center justify-between px-10 sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <div class="h-10 w-[1px] bg-talkheals-gold-p hidden md:block"></div>
            <div class="text-talkheals-muted text-xs uppercase tracking-widest font-bold">Overview / Dashboard</div>
        </div>

        <div class="flex items-center gap-8">
            <!-- Search Placeholder -->
            <div class="relative hidden lg:block">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 opacity-40">🔍</span>
                <input type="text" placeholder="Search records..." class="bg-talkheals-cream border border-talkheals-gold-p rounded-full py-2.5 pl-10 pr-6 text-sm text-talkheals-deep focus:outline-none focus:border-talkheals-gold transition-all w-64 placeholder:text-talkheals-muted/50">
            </div>

            <div class="flex items-center gap-5">
                <div class="flex flex-col text-right">
                    <div class="text-[0.85rem] font-bold text-talkheals-deep tracking-wide">{{ user.name }}</div>
                    <div class="text-[0.6rem] uppercase tracking-[0.15em] text-talkheals-gold font-bold">Administrator</div>
                </div>
                
                <div class="relative group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-talkheals-gold to-talkheals-rose flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-talkheals-gold/10">
                        {{ user.name.charAt(0) }}
                    </div>
                    
                    <!-- Dropdown on Hover -->
                    <div class="absolute right-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                        <div class="bg-white border border-talkheals-gold-p rounded-2xl p-2 w-48 shadow-2xl">
                            <Link 
                                :href="route('admin.logout')" 
                                method="post" 
                                as="button"
                                class="w-full flex items-center gap-3 px-4 py-3 text-[#b04040] hover:bg-red-50 rounded-xl transition-all text-sm font-bold text-left"
                            >
                                <span>🚪</span> Logout System
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
