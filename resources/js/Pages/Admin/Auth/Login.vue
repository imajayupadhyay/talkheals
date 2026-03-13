<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('admin.login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Admin Login - TalkHeals" />

    <div class="relative min-h-screen flex items-center justify-center overflow-hidden p-6 bg-talkheals-deep">
        <!-- Subtle Admin Background -->
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] rounded-full bg-talkheals-gold/20 blur-[100px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-talkheals-rose/20 blur-[100px]"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            <div class="text-center mb-10">
                <img src="/talkhealslogo.webp" alt="TalkHeals" class="h-16 w-auto mx-auto mb-6 brightness-0 invert opacity-80">
                <div class="text-white/40 text-[0.65rem] tracking-[0.2em] uppercase font-bold">Secure Administrative Access</div>
            </div>

            <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-[32px] p-8 lg:p-10 shadow-2xl">
                <h1 class="font-serif text-3xl text-white mb-2 font-light text-center">Admin Portal</h1>
                <p class="text-white/40 text-sm font-light text-center mb-8">Please authenticate to continue.</p>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-[0.65rem] tracking-[0.15em] uppercase text-talkheals-gold-l font-bold mb-2 ml-1">Admin Email</label>
                        <input 
                            v-model="form.email" 
                            type="email" 
                            class="admin-input" 
                            placeholder="admin@talkheals.ca"
                            required
                            autocomplete="username"
                        >
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <label class="block text-[0.65rem] tracking-[0.15em] uppercase text-talkheals-gold-l font-bold mb-2 ml-1">Security Key</label>
                        <div class="relative">
                            <input 
                                v-model="form.password" 
                                :type="showPassword ? 'text' : 'password'" 
                                class="admin-input pr-12" 
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 hover:text-talkheals-gold-l transition-colors"
                            >
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.399 8.049 7.21 5 12 5c3.79 0 7.113 2.046 9.036 5.322a1.012 1.012 0 0 1 0 .644C19.601 15.951 15.79 19 12 19c-3.79 0-7.113-2.046-9.036-5.322Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center">
                        <input v-model="form.remember" type="checkbox" id="remember" class="w-4 h-4 rounded border-white/10 bg-white/5 text-talkheals-gold focus:ring-talkheals-gold">
                        <label for="remember" class="ml-2 text-xs text-white/40 font-light cursor-pointer">Stay authenticated</label>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full py-4 bg-talkheals-gold text-talkheals-deep font-bold rounded-xl hover:bg-white transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg shadow-black/20 disabled:opacity-50"
                    >
                        Sign In to Dashboard
                    </button>
                </form>
            </div>

            <p class="text-center mt-10 text-white/20 text-[0.6rem] tracking-[0.3em] uppercase">
                &copy; 2026 TalkHeals Psychotherapy
            </p>
        </div>
    </div>
</template>

<style scoped>
.admin-input {
    @apply w-full bg-white/5 border-white/10 rounded-xl px-4 py-3.5 text-white placeholder:text-white/10 focus:border-talkheals-gold-l focus:bg-white/10 transition-all duration-300 outline-none ring-0;
}
</style>
