<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import Header from './Components/Header.vue';
import Footer from './Components/Footer.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

const props = defineProps({
    profile: Object,
    status: String,
});

const user = usePage().props.auth.user;

const form = useForm({
    preferred_name: props.profile.preferred_name || '',
    pronouns: props.profile.pronouns || '',
    date_of_birth: props.profile.date_of_birth || '',
    gender_identity: props.profile.gender_identity || '',
    phone: props.profile.phone || '',
    timezone: props.profile.timezone || 'UTC',
    occupation: props.profile.occupation || '',
    emergency_contact_name: props.profile.emergency_contact_name || '',
    emergency_contact_phone: props.profile.emergency_contact_phone || '',
    emergency_contact_relationship: props.profile.emergency_contact_relationship || '',
    reason_for_therapy: props.profile.reason_for_therapy || '',
    previous_therapy_experience: props.profile.previous_therapy_experience ? true : false,
    current_medications: props.profile.current_medications || '',
    mental_health_history: props.profile.mental_health_history || '',
});

const submit = () => {
    form.patch(route('client.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Success animation or toast
        },
    });
};
</script>

<template>
    <Head title="My Clinical Profile - TalkHeals" />

    <div class="relative min-h-screen bg-talkheals-cream overflow-x-hidden">
        <Header />

        <div class="orbs">
            <div class="orb o1"></div>
            <div class="orb o2"></div>
        </div>

        <main class="relative z-10 pt-32 pb-24 px-6 lg:px-12">
            <div class="max-w-[900px] mx-auto">
                <div class="mb-12">
                    <div class="slabel">Secure Portal</div>
                    <h1 class="stitle">Your <em>Clinical</em> Profile</h1>
                    <p class="ssub mt-2">Please provide these details to help Namrata understand your needs and ensure your safety. All data is strictly confidential.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- Section 1: Personal Details -->
                    <div class="bg-white/70 backdrop-blur-md rounded-[28px] border border-talkheals-gold-p p-8 lg:p-10 shadow-sm">
                        <h2 class="font-serif text-2xl text-talkheals-deep mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-talkheals-gold-p flex items-center justify-center text-sm">01</span>
                            Personal Information
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold font-bold mb-2 ml-1">Preferred Name</label>
                                <input v-model="form.preferred_name" type="text" class="custom-input" placeholder="What should we call you?">
                            </div>
                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold font-bold mb-2 ml-1">Pronouns</label>
                                <input v-model="form.pronouns" type="text" class="custom-input" placeholder="e.g. they/them, she/her">
                            </div>
                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold font-bold mb-2 ml-1">Date of Birth</label>
                                <input v-model="form.date_of_birth" type="date" class="custom-input">
                            </div>
                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold font-bold mb-2 ml-1">Phone Number</label>
                                <input v-model="form.phone" type="tel" class="custom-input" placeholder="+1 (___) ___ - ____">
                            </div>
                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold font-bold mb-2 ml-1">Gender Identity</label>
                                <input v-model="form.gender_identity" type="text" class="custom-input" placeholder="How do you identify?">
                            </div>
                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold font-bold mb-2 ml-1">Occupation</label>
                                <input v-model="form.occupation" type="text" class="custom-input" placeholder="Your profession">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Emergency Contact (Crucial for Therapy) -->
                    <div class="bg-white/70 backdrop-blur-md rounded-[28px] border border-talkheals-gold-p p-8 lg:p-10 shadow-sm">
                        <h2 class="font-serif text-2xl text-talkheals-deep mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-talkheals-rose-p text-talkheals-rose flex items-center justify-center text-sm font-bold">02</span>
                            Emergency Contact
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 p-4 bg-talkheals-rose-p/30 rounded-xl border border-talkheals-rose-l/20 mb-2">
                                <p class="text-[0.78rem] text-talkheals-mid font-light leading-relaxed">
                                    <strong>Why is this needed?</strong> As a Registered Psychotherapist, I am required by law to have an emergency contact on file for your safety in case of a crisis during our sessions.
                                </p>
                            </div>
                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold font-bold mb-2 ml-1">Contact Name</label>
                                <input v-model="form.emergency_contact_name" type="text" class="custom-input" placeholder="Full name">
                            </div>
                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold font-bold mb-2 ml-1">Relationship</label>
                                <input v-model="form.emergency_contact_relationship" type="text" class="custom-input" placeholder="e.g. Partner, Parent, Friend">
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold font-bold mb-2 ml-1">Contact Phone</label>
                                <input v-model="form.emergency_contact_phone" type="tel" class="custom-input" placeholder="Phone number">
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Clinical Context -->
                    <div class="bg-[#2a2420] rounded-[28px] p-8 lg:p-10 shadow-xl relative overflow-hidden">
                        <!-- Background orb inside dark card -->
                        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-talkheals-gold/5 blur-[60px]"></div>
                        
                        <h2 class="font-serif text-2xl text-white mb-8 flex items-center gap-3 relative z-10">
                            <span class="w-8 h-8 rounded-full bg-talkheals-gold text-white flex items-center justify-center text-sm font-bold">03</span>
                            Clinical Context
                        </h2>
                        
                        <div class="space-y-6 relative z-10">
                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold-l font-bold mb-2 ml-1">Primary Reason for seeking therapy</label>
                                <textarea v-model="form.reason_for_therapy" rows="4" class="custom-input dark" placeholder="What brings you here? How can I best support you?"></textarea>
                            </div>

                            <div class="form-group flex items-center gap-3 p-4 bg-white/5 rounded-xl border border-white/10">
                                <input v-model="form.previous_therapy_experience" type="checkbox" id="prev_therapy" class="w-5 h-5 rounded border-white/20 bg-transparent text-talkheals-gold focus:ring-talkheals-gold">
                                <label for="prev_therapy" class="text-[0.85rem] text-white/70 font-light cursor-pointer">I have had psychotherapy experience before.</label>
                            </div>

                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold-l font-bold mb-2 ml-1">Current Medications (if any)</label>
                                <input v-model="form.current_medications" type="text" class="custom-input dark" placeholder="List any mental health related medications">
                            </div>

                            <div class="form-group">
                                <label class="block text-[0.68rem] tracking-[0.15em] uppercase text-talkheals-gold-l font-bold mb-2 ml-1">Additional Mental Health History</label>
                                <textarea v-model="form.mental_health_history" rows="3" class="custom-input dark" placeholder="Previous diagnoses or significant life events..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="flex flex-col items-center gap-4 pt-6">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="px-12 py-4 bg-talkheals-rose text-white border-none rounded-[40px] font-sans text-[0.9rem] cursor-pointer transition-all duration-320 font-medium hover:bg-talkheals-deep hover:-translate-y-1 hover:shadow-[0_12px_32px_rgba(196,154,138,0.32)] disabled:opacity-50"
                        >
                            <span v-if="form.processing">Saving Details...</span>
                            <span v-else>Update My Profile</span>
                        </button>
                        
                        <p v-if="status === 'clinical-profile-updated'" class="text-talkheals-sage text-sm font-medium animate-pulse">
                            ✓ Your profile has been saved securely.
                        </p>
                    </div>
                </form>
            </div>
        </main>

        <Footer />
    </div>
</template>

<style scoped>
.custom-input {
    @apply w-full bg-talkheals-cream/50 border-talkheals-gold-p rounded-xl px-4 py-3.5 text-talkheals-deep placeholder:text-talkheals-muted/40 focus:ring-talkheals-gold focus:border-talkheals-gold transition-all duration-300;
}

.custom-input.dark {
    @apply bg-white/5 border-white/10 text-white placeholder:text-white/20 focus:border-talkheals-gold focus:ring-talkheals-gold;
}

.slabel { font-size: .66rem; letter-spacing: .18em; text-transform: uppercase; color: var(--gold); margin-bottom: 10px; }
.stitle { font-family: 'Playfair Display', serif; font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 400; line-height: 1.15; color: var(--deep); margin-bottom: 10px; }
.stitle em { font-style: italic; color: var(--rose); }
.ssub { font-size: .9rem; color: var(--muted); max-width: 520px; line-height: 1.78; font-weight: 300; }

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

.o1 { width: 600px; height: 600px; background: rgba(201, 169, 110, .08); top: -100px; left: -100px; animation-duration: 26s; }
.o2 { width: 500px; height: 500px; background: rgba(196, 154, 138, .08); bottom: -100px; right: -100px; animation-duration: 21s; animation-delay: -8s; }

@keyframes orbDrift {
    0%, 100% { transform: translate(0, 0) }
    34% { transform: translate(30px, -40px) }
    67% { transform: translate(-18px, 22px) }
}
</style>
