<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Sidebar from '../Components/Sidebar.vue';

const props = defineProps({
    recipients: { type: Array, default: () => [] },
    history:    { type: Array, default: () => [] },
});

const flash = computed(() => usePage().props.flash ?? {});

// ── Recipient Selection ──────────────────────────────────────────────────────
const selectedEmails = ref([]);
const recipientSearch = ref('');
const showRecipientList = ref(true);

const filteredRecipients = computed(() => {
    const q = recipientSearch.value.toLowerCase();
    if (!q) return props.recipients;
    return props.recipients.filter(r =>
        r.email.toLowerCase().includes(q) ||
        (r.name && r.name.toLowerCase().includes(q))
    );
});

const isAllSelected = computed(() =>
    props.recipients.length > 0 && selectedEmails.value.length === props.recipients.length
);

const toggleAll = () => {
    if (isAllSelected.value) {
        selectedEmails.value = [];
    } else {
        selectedEmails.value = props.recipients.map(r => r.email);
    }
};

const toggleRecipient = (email) => {
    const idx = selectedEmails.value.indexOf(email);
    if (idx > -1) {
        selectedEmails.value.splice(idx, 1);
    } else {
        selectedEmails.value.push(email);
    }
};

const isSelected = (email) => selectedEmails.value.includes(email);

const sourceLabel = (source) => {
    const labels = { newsletter: 'Newsletter', registered: 'Registered', both: 'Both' };
    return labels[source] || source;
};

const sourceColor = (source) => {
    const colors = {
        newsletter: 'bg-blue-50 text-blue-700',
        registered: 'bg-purple-50 text-purple-700',
        both: 'bg-green-50 text-green-700',
    };
    return colors[source] || 'bg-gray-50 text-gray-700';
};

// ── Rich Text Editor ─────────────────────────────────────────────────────────
const editor = ref(null);
const activeFormats = ref([]);

const execCmd = (command, value = null) => {
    document.execCommand(command, false, value);
    editor.value?.focus();
    updateActiveFormats();
};

const updateActiveFormats = () => {
    const formats = ['bold', 'italic', 'underline', 'strikeThrough',
                     'insertOrderedList', 'insertUnorderedList',
                     'justifyLeft', 'justifyCenter', 'justifyRight'];
    activeFormats.value = formats.filter(f => document.queryCommandState(f));
};

const isActive = (cmd) => activeFormats.value.includes(cmd);

const insertLink = () => {
    const url = prompt('Enter URL:');
    if (url) execCmd('createLink', url);
};

const imageInput = ref(null);
const uploadingImage = ref(false);

const triggerImageUpload = () => {
    imageInput.value?.click();
};

const handleImageUpload = async (event) => {
    const file = event.target.files?.[0];
    if (!file) return;

    uploadingImage.value = true;
    const formData = new FormData();
    formData.append('image', file);

    try {
        const { data } = await axios.post(route('admin.broadcasting.upload-image'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        editor.value?.focus();
        execCmd('insertImage', data.url);
        trackEditorContent();
    } catch (err) {
        alert('Image upload failed. Max size is 5MB. Allowed: jpg, png, gif, webp.');
    } finally {
        uploadingImage.value = false;
        event.target.value = '';
    }
};

const setFontSize = (size) => {
    execCmd('fontSize', size);
};

const setTextColor = (color) => {
    execCmd('foreColor', color);
};

const setBgColor = (color) => {
    execCmd('hiliteColor', color);
};

// ── Form / Send ──────────────────────────────────────────────────────────────
const subject = ref('');
const editorHasContent = ref(false);
const sending = ref(false);

const form = useForm({
    subject: '',
    body: '',
    recipients: [],
});

const trackEditorContent = () => {
    editorHasContent.value = !!editor.value?.innerText?.trim();
};

const canSend = computed(() =>
    subject.value.trim() &&
    editorHasContent.value &&
    selectedEmails.value.length > 0
);

const showSuccessModal = ref(false);
const sentToCount = ref(0);
const sentSubject = ref('');

const sendBroadcast = () => {
    if (!canSend.value) return;

    form.subject = subject.value;
    form.body = editor.value.innerHTML;
    form.recipients = [...selectedEmails.value];

    sentToCount.value = selectedEmails.value.length;
    sentSubject.value = subject.value;

    form.post(route('admin.broadcasting.send'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessModal.value = true;
            subject.value = '';
            if (editor.value) editor.value.innerHTML = '';
            editorHasContent.value = false;
            selectedEmails.value = [];
        },
    });
};

const closeSuccessModal = () => {
    showSuccessModal.value = false;
};

// ── History tab ──────────────────────────────────────────────────────────────
const activeTab = ref('compose'); // compose | history

const statusBadge = (status) => {
    const map = {
        queued:    'bg-yellow-50 text-yellow-700',
        sending:   'bg-blue-50 text-blue-700',
        completed: 'bg-green-50 text-green-700',
        failed:    'bg-red-50 text-red-700',
    };
    return map[status] || 'bg-gray-50 text-gray-700';
};

const formatDate = (dt) => {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString('en-CA', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
};

// ── Colors for picker ────────────────────────────────────────────────────────
const textColors = ['#000000', '#e53e3e', '#dd6b20', '#d69e2e', '#38a169', '#3182ce', '#805ad5', '#d53f8c', '#718096', '#ffffff'];
const showColorPicker = ref(false);
const showBgColorPicker = ref(false);

onMounted(() => {
    if (editor.value) {
        editor.value.addEventListener('keyup', updateActiveFormats);
        editor.value.addEventListener('mouseup', updateActiveFormats);
    }
});
</script>

<template>
    <div class="flex min-h-screen bg-talkheals-cream font-sans">
        <Sidebar />

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="bg-white border-b border-talkheals-gold-p px-8 py-5 flex items-center justify-between sticky top-0 z-20">
                <div>
                    <h1 class="text-2xl font-bold text-talkheals-deep tracking-tight">Broadcasting</h1>
                    <p class="text-sm text-talkheals-muted mt-0.5">Send emails to your subscribers and registered clients.</p>
                </div>
                <div class="flex gap-2">
                    <button @click="activeTab = 'compose'"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition"
                        :class="activeTab === 'compose' ? 'bg-talkheals-gold text-white' : 'bg-white border border-talkheals-gold/30 text-talkheals-muted hover:bg-talkheals-cream'">
                        Compose
                    </button>
                    <button @click="activeTab = 'history'"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition"
                        :class="activeTab === 'history' ? 'bg-talkheals-gold text-white' : 'bg-white border border-talkheals-gold/30 text-talkheals-muted hover:bg-talkheals-cream'">
                        History
                    </button>
                </div>
            </header>

            <!-- Flash -->
            <transition name="fade">
                <div v-if="flash.success" class="mx-8 mt-6 px-5 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ flash.success }}
                </div>
            </transition>

            <main class="flex-1 overflow-y-auto p-8">

                <!-- ═══ COMPOSE TAB ═══ -->
                <div v-if="activeTab === 'compose'" class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    <!-- Left: Compose Area -->
                    <div class="xl:col-span-2 space-y-4">

                        <!-- Subject -->
                        <div class="bg-white rounded-2xl border border-talkheals-gold-p p-4">
                            <label class="text-xs font-bold text-talkheals-muted uppercase tracking-widest mb-2 block">Subject</label>
                            <input v-model="subject" type="text" placeholder="Enter email subject…"
                                class="w-full text-talkheals-deep text-lg font-medium bg-transparent outline-none placeholder:text-talkheals-muted/40 border-none focus:ring-0 p-0">
                        </div>

                        <!-- Rich Text Editor -->
                        <div class="bg-white rounded-2xl border border-talkheals-gold-p overflow-hidden">
                            <!-- Toolbar -->
                            <div class="border-b border-talkheals-gold-p px-4 py-2 flex flex-wrap items-center gap-1">
                                <!-- Text formatting -->
                                <button @click="execCmd('bold')" :class="isActive('bold') ? 'bg-talkheals-cream text-talkheals-deep' : 'text-talkheals-muted hover:bg-talkheals-cream/50'"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm transition" title="Bold">B</button>
                                <button @click="execCmd('italic')" :class="isActive('italic') ? 'bg-talkheals-cream text-talkheals-deep' : 'text-talkheals-muted hover:bg-talkheals-cream/50'"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center italic text-sm transition" title="Italic">I</button>
                                <button @click="execCmd('underline')" :class="isActive('underline') ? 'bg-talkheals-cream text-talkheals-deep' : 'text-talkheals-muted hover:bg-talkheals-cream/50'"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center underline text-sm transition" title="Underline">U</button>
                                <button @click="execCmd('strikeThrough')" :class="isActive('strikeThrough') ? 'bg-talkheals-cream text-talkheals-deep' : 'text-talkheals-muted hover:bg-talkheals-cream/50'"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center line-through text-sm transition" title="Strikethrough">S</button>

                                <div class="w-px h-5 bg-talkheals-gold-p mx-1"></div>

                                <!-- Font size -->
                                <select @change="setFontSize($event.target.value); $event.target.value = ''"
                                    class="text-xs text-talkheals-muted bg-transparent border border-talkheals-gold/20 rounded-lg px-2 py-1.5 outline-none cursor-pointer">
                                    <option value="">Size</option>
                                    <option value="1">Small</option>
                                    <option value="3">Normal</option>
                                    <option value="5">Large</option>
                                    <option value="7">Huge</option>
                                </select>

                                <div class="w-px h-5 bg-talkheals-gold-p mx-1"></div>

                                <!-- Text color -->
                                <div class="relative">
                                    <button @click="showColorPicker = !showColorPicker; showBgColorPicker = false"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition text-sm" title="Text Color">
                                        A<span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-4 h-1 bg-red-500 rounded-full"></span>
                                    </button>
                                    <div v-if="showColorPicker" class="absolute top-full left-0 mt-1 bg-white border border-talkheals-gold-p rounded-xl shadow-lg p-2 flex flex-wrap gap-1 w-32 z-30">
                                        <button v-for="c in textColors" :key="'tc-'+c" @click="setTextColor(c); showColorPicker = false"
                                            class="w-5 h-5 rounded-full border border-gray-200 hover:scale-110 transition" :style="{ backgroundColor: c }"></button>
                                    </div>
                                </div>

                                <!-- Background color -->
                                <div class="relative">
                                    <button @click="showBgColorPicker = !showBgColorPicker; showColorPicker = false"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition" title="Highlight Color">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                        </button>
                                    <div v-if="showBgColorPicker" class="absolute top-full left-0 mt-1 bg-white border border-talkheals-gold-p rounded-xl shadow-lg p-2 flex flex-wrap gap-1 w-32 z-30">
                                        <button v-for="c in textColors" :key="'bg-'+c" @click="setBgColor(c); showBgColorPicker = false"
                                            class="w-5 h-5 rounded-full border border-gray-200 hover:scale-110 transition" :style="{ backgroundColor: c }"></button>
                                    </div>
                                </div>

                                <div class="w-px h-5 bg-talkheals-gold-p mx-1"></div>

                                <!-- Alignment -->
                                <button @click="execCmd('justifyLeft')" :class="isActive('justifyLeft') ? 'bg-talkheals-cream' : ''"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition" title="Align Left">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M3 6h18M3 12h12M3 18h18"/></svg>
                                </button>
                                <button @click="execCmd('justifyCenter')" :class="isActive('justifyCenter') ? 'bg-talkheals-cream' : ''"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition" title="Align Center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M3 6h18M6 12h12M3 18h18"/></svg>
                                </button>
                                <button @click="execCmd('justifyRight')" :class="isActive('justifyRight') ? 'bg-talkheals-cream' : ''"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition" title="Align Right">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M3 6h18M9 12h12M3 18h18"/></svg>
                                </button>

                                <div class="w-px h-5 bg-talkheals-gold-p mx-1"></div>

                                <!-- Lists -->
                                <button @click="execCmd('insertUnorderedList')" :class="isActive('insertUnorderedList') ? 'bg-talkheals-cream' : ''"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition" title="Bullet List">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                                </button>
                                <button @click="execCmd('insertOrderedList')" :class="isActive('insertOrderedList') ? 'bg-talkheals-cream' : ''"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition" title="Numbered List">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13"/><text x="2" y="8" font-size="7" fill="currentColor">1</text><text x="2" y="14" font-size="7" fill="currentColor">2</text><text x="2" y="20" font-size="7" fill="currentColor">3</text></svg>
                                </button>

                                <div class="w-px h-5 bg-talkheals-gold-p mx-1"></div>

                                <!-- Link / Image -->
                                <button @click="insertLink"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition" title="Insert Link">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </button>
                                <button @click="triggerImageUpload" :disabled="uploadingImage"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition disabled:opacity-50" title="Upload Image">
                                    <svg v-if="uploadingImage" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </button>
                                <input ref="imageInput" type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" @change="handleImageUpload">

                                <div class="w-px h-5 bg-talkheals-gold-p mx-1"></div>

                                <!-- Heading -->
                                <select @change="execCmd('formatBlock', $event.target.value); $event.target.value = ''"
                                    class="text-xs text-talkheals-muted bg-transparent border border-talkheals-gold/20 rounded-lg px-2 py-1.5 outline-none cursor-pointer">
                                    <option value="">Heading</option>
                                    <option value="h1">Heading 1</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                    <option value="p">Paragraph</option>
                                </select>

                                <!-- Undo / Redo -->
                                <div class="ml-auto flex gap-1">
                                    <button @click="execCmd('undo')"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition" title="Undo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a4 4 0 014 4v2M3 10l4-4m-4 4l4 4"/></svg>
                                    </button>
                                    <button @click="execCmd('redo')"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-talkheals-muted hover:bg-talkheals-cream/50 transition" title="Redo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a4 4 0 00-4 4v2m14-6l-4-4m4 4l-4 4"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Editable Area -->
                            <div ref="editor"
                                contenteditable="true"
                                @input="trackEditorContent"
                                @click="updateActiveFormats"
                                @keyup="updateActiveFormats"
                                class="min-h-[350px] max-h-[500px] overflow-y-auto p-6 text-talkheals-deep text-[15px] leading-relaxed outline-none prose prose-sm max-w-none"
                                data-placeholder="Compose your email here…"
                            ></div>
                        </div>

                    </div>

                    <!-- Right: Recipients Panel -->
                    <div class="space-y-4">
                        <div class="bg-white rounded-2xl border border-talkheals-gold-p overflow-hidden">
                            <!-- Header -->
                            <div class="p-4 border-b border-talkheals-gold-p">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-bold text-talkheals-deep uppercase tracking-widest">Recipients</h3>
                                    <span class="text-xs text-talkheals-muted">{{ recipients.length }} total</span>
                                </div>

                                <!-- Search -->
                                <div class="flex items-center gap-2 bg-talkheals-cream rounded-xl px-3 py-2">
                                    <svg class="w-4 h-4 text-talkheals-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <input v-model="recipientSearch" type="text" placeholder="Search recipients…"
                                        class="flex-1 text-sm bg-transparent outline-none text-talkheals-deep placeholder:text-talkheals-muted/50 border-none focus:ring-0 p-0">
                                </div>
                            </div>

                            <!-- Select All -->
                            <div class="px-4 py-3 border-b border-talkheals-gold-p bg-talkheals-cream/30">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" :checked="isAllSelected" @change="toggleAll"
                                        class="w-4 h-4 rounded border-talkheals-gold/40 text-talkheals-gold focus:ring-talkheals-gold/30">
                                    <span class="text-sm font-semibold text-talkheals-deep group-hover:text-talkheals-gold transition">
                                        Select All ({{ recipients.length }})
                                    </span>
                                </label>
                            </div>

                            <!-- Recipient List -->
                            <div class="max-h-[480px] overflow-y-auto divide-y divide-talkheals-gold-p/50">
                                <label v-for="r in filteredRecipients" :key="r.email"
                                    class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-talkheals-cream/30 transition group">
                                    <input type="checkbox" :checked="isSelected(r.email)" @change="toggleRecipient(r.email)"
                                        class="w-4 h-4 rounded border-talkheals-gold/40 text-talkheals-gold focus:ring-talkheals-gold/30 shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm text-talkheals-deep truncate font-medium">{{ r.email }}</div>
                                        <div v-if="r.name" class="text-xs text-talkheals-muted truncate">{{ r.name }}</div>
                                    </div>
                                    <span class="text-[0.6rem] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider shrink-0"
                                        :class="sourceColor(r.source)">
                                        {{ sourceLabel(r.source) }}
                                    </span>
                                </label>

                                <!-- Empty -->
                                <div v-if="filteredRecipients.length === 0" class="py-10 text-center">
                                    <p class="text-talkheals-muted text-sm">No recipients found.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ═══ HISTORY TAB ═══ -->
                <div v-if="activeTab === 'history'">
                    <div class="bg-white rounded-3xl border border-talkheals-gold-p overflow-hidden">
                        <table v-if="history.length" class="w-full">
                            <thead>
                                <tr class="border-b border-talkheals-gold-p bg-talkheals-cream/50">
                                    <th class="text-left px-6 py-4 text-xs font-bold text-talkheals-muted uppercase tracking-widest">Subject</th>
                                    <th class="text-left px-6 py-4 text-xs font-bold text-talkheals-muted uppercase tracking-widest">Sent By</th>
                                    <th class="text-left px-6 py-4 text-xs font-bold text-talkheals-muted uppercase tracking-widest">Recipients</th>
                                    <th class="text-left px-6 py-4 text-xs font-bold text-talkheals-muted uppercase tracking-widest">Status</th>
                                    <th class="text-left px-6 py-4 text-xs font-bold text-talkheals-muted uppercase tracking-widest">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-talkheals-gold-p">
                                <tr v-for="b in history" :key="b.id" class="hover:bg-talkheals-cream/30 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-talkheals-deep max-w-xs truncate">{{ b.subject }}</td>
                                    <td class="px-6 py-4 text-sm text-talkheals-muted">{{ b.admin_name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-talkheals-deep font-medium">{{ b.sent_count }}/{{ b.total_recipients }}</div>
                                        <div v-if="b.failed_count > 0" class="text-xs text-red-500">{{ b.failed_count }} failed</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                            :class="statusBadge(b.status)">
                                            {{ b.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-talkheals-muted">{{ formatDate(b.sent_at || b.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-else class="py-20 text-center">
                            <div class="text-5xl mb-4">📡</div>
                            <p class="text-talkheals-muted text-sm font-medium">No broadcasts sent yet.</p>
                            <p class="text-talkheals-muted/60 text-xs mt-1">Compose and send your first broadcast!</p>
                        </div>
                    </div>
                </div>

            </main>

            <!-- Sticky Send Bar (always visible at bottom) -->
            <div v-if="activeTab === 'compose'" class="bg-white border-t border-talkheals-gold-p px-8 py-4 flex items-center justify-between">
                <div class="text-sm text-talkheals-muted">
                    <span class="font-semibold text-talkheals-deep">{{ selectedEmails.length }}</span> recipient{{ selectedEmails.length !== 1 ? 's' : '' }} selected
                </div>
                <button @click="sendBroadcast" :disabled="!canSend || form.processing"
                    class="px-8 py-3 rounded-2xl bg-talkheals-gold text-white font-bold text-sm shadow-lg shadow-talkheals-gold/20 hover:bg-talkheals-gold/90 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center gap-2">
                    <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    {{ form.processing ? 'Sending…' : 'Send Broadcast' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <transition name="fade">
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeSuccessModal"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl p-10 w-full max-w-md text-center">
                <!-- Animated checkmark -->
                <div class="w-20 h-20 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500 animate-bounce-once" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-talkheals-deep mb-2">Broadcast Sent!</h3>
                <p class="text-sm text-talkheals-muted mb-1">
                    Your email <span class="font-semibold text-talkheals-deep">"{{ sentSubject }}"</span>
                </p>
                <p class="text-sm text-talkheals-muted mb-6">
                    has been queued for delivery to
                    <span class="font-bold text-talkheals-gold">{{ sentToCount }}</span>
                    recipient{{ sentToCount !== 1 ? 's' : '' }}.
                </p>

                <div class="flex gap-3">
                    <button @click="closeSuccessModal"
                        class="flex-1 py-3 rounded-2xl border border-talkheals-gold/30 text-talkheals-muted font-semibold text-sm hover:bg-talkheals-cream transition">
                        Compose Another
                    </button>
                    <button @click="closeSuccessModal(); activeTab = 'history'"
                        class="flex-1 py-3 rounded-2xl bg-talkheals-gold text-white font-bold text-sm hover:bg-talkheals-gold/90 transition">
                        View History
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .25s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

[contenteditable]:empty::before {
    content: attr(data-placeholder);
    color: #b5a99a;
    pointer-events: none;
}

[contenteditable] {
    word-wrap: break-word;
    white-space: pre-wrap;
}

@keyframes bounce-once {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.2); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}
.animate-bounce-once {
    animation: bounce-once 0.5s ease-out forwards;
}
</style>
