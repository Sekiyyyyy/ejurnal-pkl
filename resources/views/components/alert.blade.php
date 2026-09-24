@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
    'dismissible' => true,
    'icon' => null,
])

@php
    $typeClasses = [
        'success' => [
            'wrapper' => 'bg-emerald-50/80 border-emerald-200/80 text-emerald-950 dark:bg-emerald-950/20 dark:border-emerald-800/40 dark:text-emerald-200',
            'icon_bg' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20',
            'title' => 'text-emerald-900 dark:text-emerald-100',
            'message' => 'text-emerald-700/90 dark:text-emerald-300/80',
            'close' => 'text-emerald-500 hover:text-emerald-800 hover:bg-emerald-100/50 dark:hover:bg-emerald-900/50',
            'default_title' => 'Berhasil'
        ],
        'error' => [
            'wrapper' => 'bg-rose-50/80 border-rose-200/80 text-rose-950 dark:bg-rose-950/20 dark:border-rose-800/40 dark:text-rose-200',
            'icon_bg' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20',
            'title' => 'text-rose-900 dark:text-rose-100',
            'message' => 'text-rose-700/90 dark:text-rose-300/80',
            'close' => 'text-rose-500 hover:text-rose-800 hover:bg-rose-100/50 dark:hover:bg-rose-900/50',
            'default_title' => 'Terjadi Kesalahan'
        ],
        'warning' => [
            'wrapper' => 'bg-amber-50/80 border-amber-200/80 text-amber-950 dark:bg-amber-950/20 dark:border-amber-800/40 dark:text-amber-200',
            'icon_bg' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20',
            'title' => 'text-amber-900 dark:text-amber-100',
            'message' => 'text-amber-700/90 dark:text-amber-300/80',
            'close' => 'text-amber-500 hover:text-amber-800 hover:bg-amber-100/50 dark:hover:bg-amber-900/50',
            'default_title' => 'Peringatan'
        ],
        'info' => [
            'wrapper' => 'bg-sky-50/80 border-sky-200/80 text-sky-950 dark:bg-sky-950/20 dark:border-sky-800/40 dark:text-sky-200',
            'icon_bg' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20',
            'title' => 'text-sky-900 dark:text-sky-100',
            'message' => 'text-sky-700/90 dark:text-sky-300/80',
            'close' => 'text-sky-500 hover:text-sky-800 hover:bg-sky-100/50 dark:hover:bg-sky-900/50',
            'default_title' => 'Informasi'
        ],
        'dark' => [
            'wrapper' => 'bg-neutral-900 text-white border-neutral-800 shadow-md',
            'icon_bg' => 'bg-white/10 text-white border border-white/10',
            'title' => 'text-white',
            'message' => 'text-neutral-300',
            'close' => 'text-neutral-400 hover:text-white hover:bg-neutral-800',
            'default_title' => 'Pemberitahuan'
        ]
    ];

    $cfg = $typeClasses[$type] ?? $typeClasses['info'];
    $finalTitle = $title ?? $cfg['default_title'];
@endphp

<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    {{ $attributes->merge(['class' => 'relative flex items-start gap-3.5 p-4 rounded-xl border backdrop-blur-sm shadow-sm transition-all ' . $cfg['wrapper']]) }}
    role="alert"
>
    <!-- Icon -->
    <div class="shrink-0 mt-0.5">
        <div class="size-7 rounded-lg {{ $cfg['icon_bg'] }} flex items-center justify-center">
            @if($icon)
                {{ $icon }}
            @elseif($type === 'success')
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            @elseif($type === 'error')
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            @elseif($type === 'warning')
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            @else
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="min-w-0 flex-1 pt-0.5">
        @if($finalTitle)
            <h4 class="text-sm font-semibold {{ $cfg['title'] }} leading-tight mb-1">{{ $finalTitle }}</h4>
        @endif
        
        <div class="text-xs leading-relaxed {{ $cfg['message'] }}">
            {{ $message ?? $slot }}
        </div>
    </div>

    <!-- Close button -->
    @if($dismissible)
        <button 
            type="button" 
            @click="show = false"
            class="shrink-0 -mr-1 -mt-1 p-1.5 rounded-lg {{ $cfg['close'] }} transition-colors focus:outline-none"
            aria-label="Tutup notifikasi"
        >
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
