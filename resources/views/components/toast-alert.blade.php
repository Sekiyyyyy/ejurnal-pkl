<div 
    x-data="toastManager()" 
    class="fixed top-5 right-5 z-[99999] flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0"
    role="region"
    aria-label="Notifikasi Sistem"
>
    <template x-for="item in toasts" :key="item.id">
        <div 
            x-show="item.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
            @mouseenter="pauseToast(item)"
            @mouseleave="resumeToast(item)"
            class="pointer-events-auto relative flex items-start gap-3.5 p-4 rounded-2xl bg-neutral-900/95 text-white border border-neutral-800 shadow-[0_12px_40px_rgba(0,0,0,0.45)] backdrop-blur-md overflow-hidden transition-all duration-200 hover:border-neutral-700 hover:shadow-[0_16px_50px_rgba(0,0,0,0.6)]"
            role="status"
        >
            <!-- Progress Bar -->
            <div 
                class="absolute bottom-0 left-0 h-[2px] bg-gradient-to-r"
                :class="{
                    'from-emerald-500 to-teal-400': item.type === 'success',
                    'from-rose-500 to-red-400': item.type === 'error',
                    'from-amber-500 to-yellow-400': item.type === 'warning',
                    'from-sky-500 to-blue-400': item.type === 'info'
                }"
                :style="`width: ${item.progress}%; transition: width 50ms linear;`"
            ></div>

            <!-- Icon -->
            <div class="shrink-0 mt-0.5">
                <template x-if="item.type === 'success'">
                    <div class="size-6 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                </template>
                <template x-if="item.type === 'error'">
                    <div class="size-6 rounded-full bg-rose-500/15 text-rose-400 flex items-center justify-center border border-rose-500/30">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </template>
                <template x-if="item.type === 'warning'">
                    <div class="size-6 rounded-full bg-amber-500/15 text-amber-400 flex items-center justify-center border border-amber-500/30">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                </template>
                <template x-if="item.type === 'info'">
                    <div class="size-6 rounded-full bg-sky-500/15 text-sky-400 flex items-center justify-center border border-sky-500/30">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                </template>
            </div>

            <!-- Content -->
            <div class="min-w-0 flex-1 pt-0.5">
                <p class="text-sm font-semibold text-white tracking-tight leading-snug" x-text="item.title"></p>
                <p class="text-xs text-neutral-300/90 mt-1 leading-relaxed break-words" x-text="item.message"></p>
            </div>

            <!-- Dismiss Button -->
            <button 
                type="button" 
                @click="dismiss(item)"
                class="shrink-0 -mr-1 -mt-1 p-1.5 text-neutral-400 hover:text-white rounded-lg hover:bg-neutral-800/80 transition-colors focus:outline-none focus:ring-2 focus:ring-neutral-600"
                aria-label="Tutup notifikasi"
            >
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            initialized: false,

            init() {
                if (this.initialized) return;
                this.initialized = true;

                // Register this instance
                window.__activeToastManager = this;

                // Global event listener for custom toast events (only attach once)
                if (!window.__toastListenerAttached) {
                    window.__toastListenerAttached = true;
                    window.addEventListener('toast', (e) => {
                        const detail = e.detail || {};
                        window.__activeToastManager?.pushToast(
                            detail.type || 'success', 
                            detail.title || 'Pemberitahuan', 
                            detail.message || '', 
                            detail.timeout || 6000
                        );
                    });
                }

                // Expose global helper functions
                window.showToast = (message, title = 'Notifikasi', type = 'success', timeout = 6000) => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type, title, message, timeout }
                    }));
                };

                window.toast = {
                    success: (message, title = 'Berhasil', timeout = 6000) => window.showToast(message, title, 'success', timeout),
                    error: (message, title = 'Terjadi Kesalahan', timeout = 7000) => window.showToast(message, title, 'error', timeout),
                    warning: (message, title = 'Peringatan', timeout = 6000) => window.showToast(message, title, 'warning', timeout),
                    info: (message, title = 'Informasi', timeout = 6000) => window.showToast(message, title, 'info', timeout),
                };

                // Catch Laravel Session Flashes automatically on initial render (only if not already loaded)
                if (!window.__laravelSessionToasted) {
                    window.__laravelSessionToasted = true;

                    @if(session('success'))
                        this.pushToast('success', 'Berhasil', {!! json_encode(session('success')) !!}, 6000);
                    @endif

                    @if(session('status') === 'profile-updated')
                        this.pushToast('success', 'Perubahan Disimpan', 'Informasi profil akun Anda berhasil diperbarui.', 6000);
                    @elseif(session('status') === 'password-updated')
                        this.pushToast('success', 'Kata Sandi Diperbarui', 'Kata sandi akun Anda berhasil diperbarui.', 6000);
                    @elseif(session('status') === 'verification-link-sent')
                        this.pushToast('info', 'Tautan Terkirim', 'Tautan verifikasi baru telah dikirimkan ke alamat email Anda.', 6000);
                    @elseif(session('status'))
                        this.pushToast('info', 'Pemberitahuan', {!! json_encode(session('status')) !!}, 6000);
                    @endif

                    @if(session('error'))
                        this.pushToast('error', 'Terjadi Kesalahan', {!! json_encode(session('error')) !!}, 7000);
                    @endif

                    @if(session('warning'))
                        this.pushToast('warning', 'Peringatan', {!! json_encode(session('warning')) !!}, 6500);
                    @endif

                    @if($errors->any())
                        @php
                            $firstError = $errors->first();
                            $count = $errors->count();
                            $errorMsg = $count > 1 ? $firstError . ' (dan ' . ($count - 1) . ' kendala lainnya)' : $firstError;
                        @endphp
                        this.pushToast('error', 'Validasi Gagal', {!! json_encode($errorMsg) !!}, 8000);
                    @endif
                }
            },

            pushToast(type, title, message, duration = 6000) {
                // Deduplication: prevent identical toast within 1.5 seconds
                const now = Date.now();
                const isDuplicate = this.toasts.some(t => 
                    t.title === title && 
                    t.message === message && 
                    (now - t.createdAt) < 1500
                );
                if (isDuplicate) return;

                const id = now + Math.random().toString(36).substr(2, 5);
                const toastItem = {
                    id,
                    type,
                    title,
                    message,
                    duration,
                    remaining: duration,
                    progress: 100,
                    visible: true,
                    timer: null,
                    interval: null,
                    startTime: null,
                    createdAt: now,
                    paused: false
                };

                this.toasts.push(toastItem);
                this.startTimer(toastItem);
            },

            startTimer(item) {
                item.startTime = Date.now();
                const step = 50;

                item.interval = setInterval(() => {
                    if (item.paused) return;
                    item.remaining -= step;
                    item.progress = Math.max(0, (item.remaining / item.duration) * 100);

                    if (item.remaining <= 0) {
                        this.dismiss(item);
                    }
                }, step);
            },

            pauseToast(item) {
                item.paused = true;
            },

            resumeToast(item) {
                item.paused = false;
            },

            dismiss(item) {
                if (item.interval) clearInterval(item.interval);
                item.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== item.id);
                }, 300);
            }
        };
    }
</script>
