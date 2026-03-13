<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const isScrolled = ref(false);
const isProfileOpen = ref(false);
const user = usePage().props.auth.user;

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const toggleProfile = () => {
    isProfileOpen.value = !isProfileOpen.value;
};

const closeProfile = (e) => {
    if (!e.target.closest('.profile-wrapper')) {
        isProfileOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('click', closeProfile);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('click', closeProfile);
});
</script>

<template>
    <nav 
        :class="[
            'fixed top-0 left-0 right-0 z-[100] flex items-center justify-between transition-all duration-400',
            isScrolled ? 'py-[11px] px-6 lg:px-12 bg-talkheals-cream/95 shadow-sm' : 'py-[15px] px-6 lg:px-12 bg-talkheals-cream/85 backdrop-blur-[22px] border-b border-talkheals-gold/10'
        ]"
    >
        <!-- Logo -->
        <Link :href="route('dashboard')" class="flex flex-col no-underline group">
            <div class="font-serif text-[1.5rem] font-light text-talkheals-deep leading-none">
                Talk<span class="text-talkheals-gold italic group-hover:text-talkheals-rose transition-colors duration-300">Heals</span>
            </div>
            <div class="text-[0.58rem] tracking-[0.15em] text-talkheals-muted uppercase mt-0.5">
                Psychotherapy · Globally Accessible
            </div>
        </Link>

        <!-- Nav Links -->
        <ul class="hidden md:flex gap-6 list-none">
            <li><a href="#upcoming" class="text-talkheals-mid text-[0.79rem] tracking-[0.05em] hover:text-talkheals-deep relative after:content-[''] after:absolute after:bottom-[-3px] after:left-0 after:right-0 after:h-[1px] after:bg-talkheals-gold after:scale-x-0 after:transition-transform after:duration-300 after:origin-left hover:after:scale-x-100 transition-colors duration-300">Workshops</a></li>
            <li><a href="#reviews" class="text-talkheals-mid text-[0.79rem] tracking-[0.05em] hover:text-talkheals-deep relative after:content-[''] after:absolute after:bottom-[-3px] after:left-0 after:right-0 after:h-[1px] after:bg-talkheals-gold after:scale-x-0 after:transition-transform after:duration-300 after:origin-left hover:after:scale-x-100 transition-colors duration-300">Stories</a></li>
            <li><a href="#content" class="text-talkheals-mid text-[0.79rem] tracking-[0.05em] hover:text-talkheals-deep relative after:content-[''] after:absolute after:bottom-[-3px] after:left-0 after:right-0 after:h-[1px] after:bg-talkheals-gold after:scale-x-0 after:transition-transform after:duration-300 after:origin-left hover:after:scale-x-100 transition-colors duration-300">Articles</a></li>
            <li><a href="#about" class="text-talkheals-mid text-[0.79rem] tracking-[0.05em] hover:text-talkheals-deep relative after:content-[''] after:absolute after:bottom-[-3px] after:left-0 after:right-0 after:h-[1px] after:bg-talkheals-gold after:scale-x-0 after:transition-transform after:duration-300 after:origin-left hover:after:scale-x-100 transition-colors duration-300">Namrata</a></li>
            <li><a href="#" class="text-talkheals-mid text-[0.79rem] tracking-[0.05em] hover:text-talkheals-deep relative after:content-[''] after:absolute after:bottom-[-3px] after:left-0 after:right-0 after:h-[1px] after:bg-talkheals-gold after:scale-x-0 after:transition-transform after:duration-300 after:origin-left hover:after:scale-x-100 transition-colors duration-300">Contact</a></li>
        </ul>

        <!-- Right Section -->
        <div class="flex items-center gap-3 lg:gap-5">
            <button class="px-5 py-2.5 bg-talkheals-rose text-white border-none rounded-[40px] font-sans text-[0.79rem] cursor-pointer hover:bg-talkheals-deep hover:translate-y-[-2px] transition-all duration-300 hidden sm:block">
                Book Now
            </button>

            <!-- Profile Dropdown -->
            <div class="relative profile-wrapper">
                <div 
                    @click="toggleProfile"
                    class="flex items-center gap-2 p-1.5 pr-3 bg-talkheals-gold-p/70 border border-talkheals-gold-l rounded-[40px] cursor-pointer hover:bg-talkheals-gold-p transition-all duration-300"
                >
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-talkheals-rose to-talkheals-gold flex items-center justify-center text-white text-[0.7rem] uppercase">
                        {{ user.name.charAt(0) }}
                    </div>
                    <span class="text-[0.76rem] text-talkheals-deep font-medium">Account</span>
                    <span class="text-talkheals-muted text-[0.65rem] transform transition-transform duration-300" :class="isProfileOpen ? 'rotate-180' : ''">▾</span>
                </div>

                <!-- Dropdown Menu -->
                <div 
                    v-if="isProfileOpen"
                    class="absolute top-[calc(100%+9px)] right-0 w-[232px] bg-white rounded-[15px] shadow-[0_18px_52px_rgba(42,36,32,0.13)] border border-talkheals-gold-p p-[14px] z-[200] transition-all duration-300 transform origin-top-right animate-in fade-in zoom-in-95 duration-200"
                >
                    <div class="flex gap-2.5 items-center pb-3 border-b border-talkheals-gold-p mb-2.5">
                        <div class="w-[38px] h-[38px] rounded-full bg-gradient-to-br from-talkheals-rose to-talkheals-gold flex items-center justify-center text-white text-[0.85rem] uppercase font-medium">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-medium text-[0.86rem] text-talkheals-deep truncate">{{ user.name }}</div>
                            <div class="text-[0.68rem] text-talkheals-muted uppercase tracking-wider">Member</div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-0.5">
                        <a href="#" class="flex items-center gap-2.5 p-2 rounded-lg text-[0.8rem] text-talkheals-deep hover:bg-talkheals-gold-p transition-colors duration-200">
                            <span>📅</span> My Appointments
                        </a>
                        <a href="#" class="flex items-center gap-2.5 p-2 rounded-lg text-[0.8rem] text-talkheals-deep hover:bg-talkheals-gold-p transition-colors duration-200">
                            <span>📒</span> Session Notes
                        </a>
                        <a href="#" class="flex items-center gap-2.5 p-2 rounded-lg text-[0.8rem] text-talkheals-deep hover:bg-talkheals-gold-p transition-colors duration-200">
                            <span>🎓</span> My Workshops
                        </a>
                        <a href="#" class="flex items-center gap-2.5 p-2 rounded-lg text-[0.8rem] text-talkheals-deep hover:bg-talkheals-gold-p transition-colors duration-200">
                            <span>💳</span> Billing
                        </a>
                        <Link 
                            :href="route('logout')" 
                            method="post" 
                            as="button"
                            class="w-full flex items-center gap-2.5 p-2 rounded-lg text-[0.8rem] text-[#b04040] hover:bg-red-50 transition-colors duration-200 text-left"
                        >
                            <span>🚪</span> Sign Out
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
