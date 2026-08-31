<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'E-Jurnal SMKN 1 Beringin') }}</title>

        <!-- Font Poppins agar persis seperti gambar -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Poppins', sans-serif; }
        </style>
    </head>
    <body class="text-white antialiased">
        <!-- Background foto sekolah dengan overlay warna teal pekat khas referensimu -->
        <div class="relative flex min-h-screen flex-col items-center justify-center bg-cover bg-center px-4 py-10" 
             style="background-image: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&q=80&w=1920');">
            
            <!-- Overlay Gelap (Teal/Cyan gelap) -->
            <div class="absolute inset-0 bg-[#0f545a]/80"></div>

            <!-- Container dibikin lebih ramping (max-w-[420px]) mengikuti proporsi gambar -->
            <div class="relative z-10 w-full max-w-[420px]">
                
                <!-- Panel Kaca transparan persis seperti gambar -->
                <div class="w-full rounded-2xl border border-white/20 bg-white/20 p-8 shadow-2xl backdrop-blur-md">
                    {{ $slot }}
                </div>

            </div>
        </div>
    </body>
</html>