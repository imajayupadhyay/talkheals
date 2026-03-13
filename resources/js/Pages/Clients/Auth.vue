<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    status: String,
    canResetPassword: {
        type: Boolean,
        default: true
    }
});

const mode = ref('login'); // 'login' or 'signup'

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

const registerForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submitLogin = () => {
    loginForm.post(route('login'), {
        onFinish: () => loginForm.reset('password'),
    });
};

const submitRegister = () => {
    registerForm.post(route('register'), {
        onFinish: () => registerForm.reset('password', 'password_confirmation'),
    });
};

const toggleMode = () => {
    mode.value = mode.value === 'login' ? 'signup' : 'login';
};
</script>

<template>
    <Head title="TalkHeals - Welcome" />

    <div class="relative min-h-screen flex items-center justify-center overflow-hidden p-6 lg:p-12">
        <!-- Background Elements -->
        <div class="orbs">
            <div class="orb o1"></div>
            <div class="orb o2"></div>
            <div class="orb o3"></div>
        </div>

        <div class="relative z-10 w-full max-w-6xl grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Side: Hero Branding -->
            <div class="text-left">
                <div class="mb-8">
                    <div class="font-serif text-4xl lg:text-5xl font-light text-talkheals-deep leading-tight">
                        Talk<span class="text-talkheals-gold italic">Heals</span>
                    </div>
                    <div class="text-xs tracking-[0.2em] uppercase text-talkheals-muted mt-2 font-medium">
                        Psychotherapy · Globally Accessible
                    </div>
                </div>

                <h1 class="font-serif text-5xl lg:text-7xl font-extralight text-talkheals-deep mb-6 leading-[1.1]">
                    Your journey<br>starts <em class="text-talkheals-rose italic">now</em>
                </h1>
                
                <p class="text-lg text-talkheals-mid font-light max-w-md leading-relaxed mb-8">
                    TalkHeals is a safe, warm space for your healing, wherever you are in the world. Connect with professional support tailored to your unique story.
                </p>

                <div class="flex items-center gap-4 text-talkheals-muted text-sm font-light">
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-talkheals-gold"></span>
                        Confidential
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-talkheals-rose"></span>
                        Accessible
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-talkheals-sage"></span>
                        Compassionate
                    </span>
                </div>
            </div>

            <!-- Right Side: Auth Card -->
            <div class="relative flex justify-center lg:justify-end">
                <div class="w-full max-w-md bg-white/80 backdrop-blur-xl border border-talkheals-gold-l/30 rounded-[28px] shadow-[0_18px_52px_rgba(42,36,32,0.08)] p-8 lg:p-10">
                    
                    <!-- Switcher -->
                    <div class="flex bg-talkheals-cream p-1.5 rounded-2xl mb-10 border border-talkheals-gold-p">
                        <button 
                            @click="mode = 'login'"
                            class="flex-1 py-2.5 rounded-xl text-sm font-medium transition-all duration-300"
                            :class="mode === 'login' ? 'bg-white text-talkheals-deep shadow-sm' : 'text-talkheals-muted hover:text-talkheals-mid'"
                        >
                            Log In
                        </button>
                        <button 
                            @click="mode = 'signup'"
                            class="flex-1 py-2.5 rounded-xl text-sm font-medium transition-all duration-300"
                            :class="mode === 'signup' ? 'bg-white text-talkheals-deep shadow-sm' : 'text-talkheals-muted hover:text-talkheals-mid'"
                        >
                            Sign Up
                        </button>
                    </div>

                    <!-- Status Messages -->
                    <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-100 rounded-xl text-sm text-green-700 font-medium">
                        {{ status }}
                    </div>

                    <!-- Login Form -->
                    <div v-if="mode === 'login'">
                        <h2 class="font-serif text-3xl font-light text-talkheals-deep mb-2">Welcome Back</h2>
                        <p class="text-sm text-talkheals-muted font-light mb-8">Please enter your details to continue your journey.</p>

                        <form @submit.prevent="submitLogin" class="space-y-5">
                            <div>
                                <label for="login-email" class="block text-xs uppercase tracking-wider text-talkheals-muted font-medium mb-1.5 ml-1">Email Address</label>
                                <input 
                                    id="login-email"
                                    type="email" 
                                    v-model="loginForm.email"
                                    class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition"
                                    placeholder="your@email.com"
                                    required
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="loginForm.errors.email" />
                            </div>

                            <div>
                                <label for="login-password" class="block text-xs uppercase tracking-wider text-talkheals-muted font-medium mb-1.5 ml-1">Password</label>
                                <input 
                                    id="login-password"
                                    type="password" 
                                    v-model="loginForm.password"
                                    class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                />
                                <InputError class="mt-2" :message="loginForm.errors.password" />
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center group cursor-pointer">
                                    <Checkbox name="remember" v-model:checked="loginForm.remember" class="text-talkheals-gold focus:ring-talkheals-gold border-talkheals-gold-p" />
                                    <span class="ms-2 text-xs text-talkheals-muted group-hover:text-talkheals-mid transition">Remember me</span>
                                </label>

                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs text-talkheals-gold hover:text-talkheals-rose font-medium transition"
                                >
                                    Forgot Password?
                                </Link>
                            </div>

                            <button 
                                type="submit"
                                :disabled="loginForm.processing"
                                class="w-full bg-talkheals-rose hover:bg-talkheals-deep text-white font-medium py-3.5 rounded-xl shadow-[0_10px_28px_rgba(196,154,138,0.28)] hover:shadow-none transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50"
                            >
                                Continue to Portal
                            </button>
                        </form>
                    </div>

                    <!-- Signup Form -->
                    <div v-else>
                        <h2 class="font-serif text-3xl font-light text-talkheals-deep mb-2">Join TalkHeals</h2>
                        <p class="text-sm text-talkheals-muted font-light mb-8">Begin your path to healing with our supportive community.</p>

                        <form @submit.prevent="submitRegister" class="space-y-5">
                            <div>
                                <label for="reg-name" class="block text-xs uppercase tracking-wider text-talkheals-muted font-medium mb-1.5 ml-1">Full Name</label>
                                <input 
                                    id="reg-name"
                                    type="text" 
                                    v-model="registerForm.name"
                                    class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition"
                                    placeholder="Enter your name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                                <InputError class="mt-2" :message="registerForm.errors.name" />
                            </div>

                            <div>
                                <label for="reg-email" class="block text-xs uppercase tracking-wider text-talkheals-muted font-medium mb-1.5 ml-1">Email Address</label>
                                <input 
                                    id="reg-email"
                                    type="email" 
                                    v-model="registerForm.email"
                                    class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition"
                                    placeholder="your@email.com"
                                    required
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="registerForm.errors.email" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="reg-password" class="block text-xs uppercase tracking-wider text-talkheals-muted font-medium mb-1.5 ml-1">Password</label>
                                    <input 
                                        id="reg-password"
                                        type="password" 
                                        v-model="registerForm.password"
                                        class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition"
                                        placeholder="••••••••"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <InputError class="mt-2" :message="registerForm.errors.password" />
                                </div>
                                <div>
                                    <label for="reg-password-confirm" class="block text-xs uppercase tracking-wider text-talkheals-muted font-medium mb-1.5 ml-1">Confirm</label>
                                    <input 
                                        id="reg-password-confirm"
                                        type="password" 
                                        v-model="registerForm.password_confirmation"
                                        class="w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3 text-talkheals-deep placeholder:text-talkheals-muted/50 focus:ring-talkheals-gold focus:border-talkheals-gold transition"
                                        placeholder="••••••••"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <InputError class="mt-2" :message="registerForm.errors.password_confirmation" />
                                </div>
                            </div>

                            <p class="text-[10px] text-talkheals-muted leading-relaxed px-1">
                                By joining, you agree to our <a href="#" class="underline">Terms of Service</a> and <a href="#" class="underline">Privacy Policy</a>. We value your confidentiality.
                            </p>

                            <button 
                                type="submit"
                                :disabled="registerForm.processing"
                                class="w-full bg-talkheals-gold hover:bg-talkheals-deep text-white font-medium py-3.5 rounded-xl shadow-[0_10px_28px_rgba(201,169,110,0.22)] hover:shadow-none transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50"
                            >
                                Create My Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.orbs {
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    overflow: hidden;
}

.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(85px);
    animation: orbDrift ease-in-out infinite;
}

.o1 {
    width: 650px;
    height: 650px;
    background: rgba(201, 169, 110, 0.08);
    top: -180px;
    left: -180px;
    animation-duration: 26s;
}

.o2 {
    width: 480px;
    height: 480px;
    background: rgba(196, 154, 138, 0.09);
    top: 38%;
    right: -80px;
    animation-duration: 21s;
    animation-delay: -8s;
}

.o3 {
    width: 560px;
    height: 560px;
    background: rgba(122, 158, 142, 0.07);
    bottom: -80px;
    left: 18%;
    animation-duration: 30s;
    animation-delay: -14s;
}

@keyframes orbDrift {
    0%, 100% { transform: translate(0, 0); }
    34% { transform: translate(30px, -40px); }
    67% { transform: translate(-18px, 22px); }
}
</style>
