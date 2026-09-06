<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'E-Jurnal SMKN 1 Beringin') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }
            .input-field {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: #ffffff;
                transition: all 0.3s ease;
            }
            .input-field:focus {
                background: rgba(255, 255, 255, 0.2);
                border-color: #34d399;
                box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.3);
                outline: none;
            }
            .input-field::placeholder { color: rgba(255, 255, 255, 0.7); }
            /* Select options in dropdown need dark background to be legible */
            select.input-field option { background-color: #063024; color: white; }
        </style>
    </head>
    <body class="text-white antialiased min-h-screen overflow-x-hidden relative bg-school">
        
        <!-- Overlay persis seperti yang disukai user -->
        <div class="absolute inset-0 bg-[#063024]/75 backdrop-blur-[4px]"></div>

        <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6 lg:px-8 z-10">
            <div class="w-full max-w-md">
                <div class="glass-panel rounded-3xl p-8 sm:p-10">
                    {{ $slot }}
                </div>
            </div>
            
            <div class="mt-8 text-center text-sm text-white/70 font-medium tracking-wide">
                &copy; {{ date('Y') }} SMKN 1 Beringin. All rights reserved.
            </div>
        </div>

        <!-- Floating Customer Service Button (Glassmorphism) -->
        <a href="https://wa.me/6285188981707?text=Halo%20Admin%2C%20saya%20butuh%20bantuan%20terkait%20E-Jurnal%20PKL." 
           target="_blank"
           class="fixed bottom-6 right-6 z-50 flex items-center glass-panel rounded-full hover:bg-white/20 transition-all duration-300 hover:-translate-y-1 group hover:pr-6">
            <div class="w-14 h-14 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-headset text-2xl text-white"></i>
        </div>
            <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-xs transition-all duration-300 font-medium text-sm text-white">
                Pusat Bantuan
            </span>
        </a>
    </body>
</html>