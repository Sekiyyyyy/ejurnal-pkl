<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Dalam Pemeliharaan</title>
    <!-- CDN Tailwind & Font Inter -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4 selection:bg-blue-500 selection:text-white">
    
    <!-- Card Utama -->
    <div class="max-w-lg w-full bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-12 text-center border border-slate-100 relative overflow-hidden">
        
        <!-- Efek Glow Halus di Background Card -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-50 rounded-full blur-3xl opacity-60"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-slate-50 rounded-full blur-3xl opacity-60"></div>

        <div class="relative z-10">
            <!-- Badge Status -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 text-amber-600 text-xs font-bold tracking-wider uppercase mb-8 border border-amber-100">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Maintenance Mode
            </div>

            <!-- Icon Modern -->
            <div class="mx-auto w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mb-8 shadow-sm border border-slate-100 transform rotate-3 hover:rotate-0 transition-transform duration-300">
                <svg class="w-10 h-10 text-slate-700 animate-[spin_8s_linear_infinite]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>

            <!-- Teks Konten -->
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-4 tracking-tight">
                Sistem Sedang Diperbarui
            </h1>
            
            <p class="text-slate-500 leading-relaxed mb-8">
                Kami sedang melakukan peningkatan performa dan penambahan fitur pada <strong class="text-slate-700 font-semibold">E-Jurnal PKL</strong>. Mohon maaf atas ketidaknyamanannya, silakan kembali beberapa saat lagi.
            </p>

            <!-- Divider -->
            <hr class="border-slate-100 mb-6 w-1/2 mx-auto">

            <!-- Footer Teks -->
            <p class="text-xs text-slate-400 font-medium tracking-widest uppercase">
                Terima kasih atas kesabarannya
            </p>
        </div>
    </div>

</body>
</html>