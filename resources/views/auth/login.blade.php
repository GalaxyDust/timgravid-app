<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staff - TIMGRAVID</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Panggil CSS & JS dari Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js untuk show/hide alert -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col items-center justify-center p-4">

    <!-- CONTAINER KONTEN -->
    <div class="w-full max-w-md">
        
        <!-- CARD PUTIH -->
        <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10 border border-gray-100">
            
            <!-- 1. HEADER CARD (Logo & Judul) -->
            <div class="text-center mb-8 animate-fade-in-down">
                <a href="{{ route('landing') }}" class="inline-block transition-transform hover:scale-105">
                    <img src="{{ asset('images/logo-green.png') }}" 
                         alt="TIMGRAVID" 
                         class="h-16 w-auto mx-auto mb-4">
                </a>
                <h1 class="text-3xl font-serif font-bold text-primary">Selamat Datang</h1>
                <p class="text-text-muted text-sm mt-2">Silakan masuk untuk melanjutkan.</p>
            </div>

            <!-- 2. NOTIFIKASI ERROR (BARU) -->
            @if($errors->any())
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
                 class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-6 flex justify-between items-start" 
                 role="alert">
                <div>
                    <p class="font-bold">Login Gagal</p>
                    <p class="text-sm">{{ $errors->first() }}</p>
                </div>
                <button @click="show = false" class="text-red-500 hover:text-red-700">&times;</button>
            </div>
            @endif

            <!-- 3. FORM LOGIN -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Input Email -->
                <div class="animate-fade-in-up delay-100">
                    <label for="email" class="block text-sm font-semibold text-primary mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="email" name="email" id="email" required autofocus
                            class="block w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg text-text-dark placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent sm:text-sm transition bg-cream/60 focus:bg-white"
                            placeholder="Masukkan alamat email Anda" value="{{ old('email') }}">
                    </div>
                </div>

                <!-- Input Password -->
                <div class="animate-fade-in-up delay-200">
                    <label for="password" class="block text-sm font-semibold text-primary mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        
                        <input type="password" name="password" id="password" required
                            class="block w-full pl-11 pr-10 py-3 border border-gray-300 rounded-lg text-text-dark placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent sm:text-sm transition bg-cream/60 focus:bg-white"
                            placeholder="Masukkan password Anda">
                        
                        <!-- Tombol Mata -->
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary focus:outline-none cursor-pointer">
                            <svg id="eye-open" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg id="eye-closed" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.575-2.964m4.332-4.075c.983-.497 2.096-.793 3.279-.793 4.478 0 8.268 2.943 9.542 7 0 0-.276.875-.725 1.625m-3.525 2.875a4.97 4.97 0 00.999.125 5 5 0 10-5-5 4.97 4.97 0 00.125.999M9 13.5l3.5-3.5m-3 7l6-6" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Tombol Masuk -->
                <div class="pt-4 animate-fade-in-up delay-300">
                    <button type="submit" 
                        class="w-full flex items-center justify-center py-3.5 px-4 rounded-lg shadow-lg text-sm font-bold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition duration-300 transform hover:-translate-y-1 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        MASUK
                    </button>
                </div>
            </form>
            
            <!-- Link Kembali -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <a href="{{ route('landing') }}" class="text-xs text-text-muted hover:text-accent font-medium flex items-center justify-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Halaman Utama
                </a>
            </div>
        </div>

        <!-- Footer Kecil -->
        <p class="text-center text-gray-400 text-xs mt-8">
            &copy; {{ date('Y') }} Yayasan Sajarah Timbanganten.
        </p>
    </div>

    <!-- SCRIPT JS -->
    <script>
        // Fitur Mata
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            }
        }

        // Fitur Loading Button
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function() {
            // Ganti teks & non-aktifkan tombol
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `;
        });
    </script>
</body>
</html>