<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Primary SEO Meta Tags -->
        <title>{{ $title ?? config('app.name', 'E-Jurnal SMKN 1 Beringin') }} - Portal Jurnal PKL Digital</title>
        <meta name="title" content="{{ $title ?? config('app.name', 'E-Jurnal SMKN 1 Beringin') }} - Portal Jurnal PKL Digital">
        <meta name="description" content="Sistem Informasi E-Jurnal PKL SMKN 1 Beringin. Platform digital resmi untuk pencatatan kegiatan harian, absensi, monitoring guru pembimbing, dan penilaian Praktik Kerja Lapangan (PKL).">
        <meta name="keywords" content="e-jurnal, jurnal pkl, pkl smkn 1 beringin, smkn 1 beringin, absensi pkl, kegiatan pkl, monitoring pkl, prakerin, smk bisa">
        <meta name="author" content="SMKN 1 Beringin">
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
        <meta name="theme-color" content="#063024">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $title ?? config('app.name', 'E-Jurnal SMKN 1 Beringin') }} - Portal Jurnal PKL Digital">
        <meta property="og:description" content="Sistem Informasi E-Jurnal PKL SMKN 1 Beringin. Platform digital terpadu untuk siswa, guru pembimbing, dan instruktur industri.">
        <meta property="og:image" content="{{ asset('logo.png') }}">
        <meta property="og:site_name" content="E-Jurnal SMKN 1 Beringin">
        <meta property="og:locale" content="id_ID">

        <!-- Twitter Cards -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="{{ $title ?? config('app.name', 'E-Jurnal SMKN 1 Beringin') }} - Portal Jurnal PKL Digital">
        <meta name="twitter:description" content="Sistem Informasi E-Jurnal PKL SMKN 1 Beringin. Platform digital terpadu untuk siswa, guru pembimbing, dan instruktur industri.">
        <meta name="twitter:image" content="{{ asset('logo.png') }}">

        <!-- Structured Data (JSON-LD) -->
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'EducationalOrganization',
                    '@id' => 'https://jurnal.tiksmkn1beringin.my.id/#organization',
                    'name' => 'SMKN 1 Beringin',
                    'url' => 'https://jurnal.tiksmkn1beringin.my.id',
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('logo.png'),
                    ],
                    'description' => 'Sekolah Menengah Kejuruan Negeri 1 Beringin, Deli Serdang, Sumatera Utara.',
                ],
                [
                    '@type' => 'SoftwareApplication',
                    '@id' => 'https://jurnal.tiksmkn1beringin.my.id/#software',
                    'name' => 'E-Jurnal PKL SMKN 1 Beringin',
                    'applicationCategory' => 'EducationalApplication',
                    'operatingSystem' => 'All',
                    'url' => 'https://jurnal.tiksmkn1beringin.my.id',
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'IDR',
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>

        <!-- Preconnect & Fonts Optimization -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- FontAwesome (Asynchronous / Non-blocking) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" media="print" onload="this.media='all'" />
        <noscript>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        </noscript>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Poppins', sans-serif; }
            .bg-school {
                background-image: url('{{ asset("bg-school.jpg") }}');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }
            .glass-panel {
                background: rgba(255, 255, 255, 0.16);
                backdrop-filter: blur(18px);
                -webkit-backdrop-filter: blur(18px);
                border: 1px solid rgba(255, 255, 255, 0.28);
                box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            }
            .input-field {
                background: rgba(255, 255, 255, 0.12);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.28);
                color: #ffffff;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .input-field:focus {
                background: rgba(255, 255, 255, 0.22);
                border-color: #34d399;
                box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.35);
                outline: none;
            }
            .input-field::placeholder { color: rgba(255, 255, 255, 0.65); }
            /* Select options dropdown readable styling */
            select.input-field option { background-color: #063024; color: #ffffff; }
        </style>
    </head>
    <body class="text-white antialiased min-h-screen overflow-x-hidden relative bg-school selection:bg-emerald-500 selection:text-white">
        
        <!-- Backdrop Ambient Overlay -->
        <div class="absolute inset-0 bg-[#063024]/80 backdrop-blur-[5px] pointer-events-none" aria-hidden="true"></div>

        <main role="main" class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6 lg:px-8 z-10">
            <div class="w-full max-w-md">
                <div class="glass-panel rounded-3xl p-8 sm:p-10 transition-all duration-300">
                    {{ $slot }}
                </div>
            </div>
            
            <footer class="mt-8 text-center text-sm text-white/70 font-medium tracking-wide">
                &copy; {{ date('Y') }} SMKN 1 Beringin. All rights reserved.
            </footer>
        </main>

        <!-- Floating Customer Service Button (Glassmorphism) -->
        <a href="https://wa.me/6285188981707?text=Halo%20Admin%2C%20saya%20butuh%20bantuan%20terkait%20E-Jurnal%20PKL." 
           target="_blank"
           rel="noopener noreferrer"
           aria-label="Pusat Bantuan WhatsApp Admin E-Jurnal"
           class="fixed bottom-6 right-6 z-50 flex items-center glass-panel rounded-full hover:bg-white/25 transition-all duration-300 hover:-translate-y-1 group hover:pr-6 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            <div class="w-14 h-14 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-headset text-2xl text-white"></i>
            </div>
            <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-xs transition-all duration-300 font-medium text-sm text-white">
                Pusat Bantuan
            </span>
        </a>
    </body>
</html>