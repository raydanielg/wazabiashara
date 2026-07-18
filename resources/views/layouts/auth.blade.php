<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Wazabiashara'))</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <meta name="theme-color" content="#001816">
    <meta property="og:title" content="{{ config('app.name', 'Wazabiashara') }}">
    <meta property="og:description" content="Biashara yako, Mkononi mwako">
    <meta property="og:image" content="{{ asset('favicon.png') }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ config('app.name', 'Wazabiashara') }}">
    <meta name="twitter:description" content="Biashara yako, Mkononi mwako">
    <meta name="twitter:image" content="{{ asset('favicon.png') }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Toastify-JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <style>
        @keyframes simpleFadeIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes floatText { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-10px); } }
        @keyframes slideUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        @keyframes slideUpDelay { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        @keyframes pulseGlow { 0%,100% { opacity:0.4; } 50% { opacity:0.7; } }
        .ajax-loader { position:fixed; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #024938, #f9ac00, #024938); background-size: 200% 100%; animation: ajaxProgress 1s linear infinite; z-index:9999; display:none; }
        @keyframes ajaxProgress { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        .page-transition { animation: simpleFadeIn 0.35s ease-out both; }
        .float-text { animation: floatText 4s ease-in-out infinite; }
        .slide-up { animation: slideUp 0.6s ease-out both; }
        .slide-up-delay-1 { animation: slideUp 0.6s ease-out 0.15s both; }
        .slide-up-delay-2 { animation: slideUp 0.6s ease-out 0.3s both; }
        .slide-up-delay-3 { animation: slideUp 0.6s ease-out 0.45s both; }
        .pulse-glow { animation: pulseGlow 3s ease-in-out infinite; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-['Nunito',sans-serif] antialiased text-slate-800 min-h-screen">

    {{-- Auth Background --}}
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('flat-abstract-background-pattern-vector_822782-866.jpg') }}" alt="" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/90 via-emerald-800/85 to-emerald-700/80"></div>
        <div class="absolute top-0 left-0 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-400/10 rounded-full blur-3xl"></div>
    </div>

    {{-- AJAX Progress Bar --}}
    <div id="ajaxLoader" class="ajax-loader"></div>

    <main id="authMain" class="relative z-10 min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-5xl grid lg:grid-cols-2 gap-0 lg:gap-8 items-center">

            {{-- Left Column: Image + Animated Business Text (hidden on mobile) --}}
            <div class="hidden lg:flex flex-col justify-center relative overflow-hidden rounded-3xl h-[600px] shadow-2xl">
                {{-- Background Slideshow --}}
                <div id="authSlideshow" class="absolute inset-0">
                    <img src="{{ asset('images/58.jpg') }}" alt="" class="auth-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-100">
                    <img src="{{ asset('images/35876.jpg') }}" alt="" class="auth-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-0">
                    <img src="{{ asset('images/76155.jpg') }}" alt="" class="auth-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-0">
                    <img src="{{ asset('images/76169.jpg') }}" alt="" class="auth-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-0">
                    <img src="{{ asset('images/2148761600.jpg') }}" alt="" class="auth-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-0">
                    <img src="{{ asset('images/2148777464.jpg') }}" alt="" class="auth-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-0">
                </div>
                {{-- Slide Indicators --}}
                <div id="slideDots" class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                    <span class="slide-dot w-2 h-2 rounded-full bg-white/80 transition-all"></span>
                    <span class="slide-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                    <span class="slide-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                    <span class="slide-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                    <span class="slide-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                    <span class="slide-dot w-2 h-2 rounded-full bg-white/30 transition-all"></span>
                </div>
                {{-- Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/85 via-emerald-800/75 to-emerald-700/65"></div>
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(rgba(255,255,255,0.2) 1px, transparent 1px); background-size: 24px 24px;"></div>
                <div class="absolute top-0 right-0 w-72 h-72 bg-gold-500/15 rounded-full blur-3xl pulse-glow"></div>
                <div class="absolute bottom-0 left-0 w-72 h-72 bg-emerald-400/10 rounded-full blur-3xl pulse-glow"></div>

                {{-- Content --}}
                <div class="relative z-10 p-12 flex flex-col justify-center h-full">
                    {{-- Logo --}}
                    <div class="flex items-center gap-3 mb-8 slide-up">
                        <img src="{{ asset('logo.png') }}" alt="Wazabiashara" class="w-12 h-12 object-contain">
                        <div>
                            <h1 class="text-2xl font-extrabold text-white tracking-tight">Wazabiashara</h1>
                            <p class="text-gold-300 text-sm font-medium">Biashara yako, Mkononi mwako</p>
                        </div>
                    </div>

                    {{-- Animated Headlines --}}
                    <div class="space-y-6 mt-4">
                        <div class="slide-up-delay-1">
                            <h2 class="text-3xl font-extrabold text-white leading-tight">
                                Manage Your Business<br>
                                <span class="text-gold-400">With Ease</span>
                            </h2>
                        </div>

                        <div class="slide-up-delay-2 space-y-4">
                            <div class="flex items-start gap-3 float-text" style="animation-delay: 0s;">
                                <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-white font-semibold text-base">Smart Analytics</p>
                                    <p class="text-emerald-100/80 text-sm">Track sales, expenses & profits in real-time</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 float-text" style="animation-delay: 1s;">
                                <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-white font-semibold text-base">Easy Payments</p>
                                    <p class="text-emerald-100/80 text-sm">Accept mobile money & card payments</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 float-text" style="animation-delay: 2s;">
                                <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <p class="text-white font-semibold text-base">Grow Faster</p>
                                    <p class="text-emerald-100/80 text-sm">Tools and insights to scale your business</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-auto pt-8 slide-up-delay-3">
                        <div class="flex items-center gap-2 text-emerald-100/60 text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Trusted by thousands of businesses across Tanzania</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Form --}}
            <div class="w-full max-w-md mx-auto lg:mx-0 lg:ml-auto" style="animation: simpleFadeIn 0.4s ease-out both;">
                @yield('content')
            </div>

        </div>
    </main>

    {{-- Sweet Alert Component --}}
    @include('components.sweet-alert')

    {{-- Toastify-JS Toast System --}}
    <style>
        .toastify {
            font-family: 'Nunito', sans-serif !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.2) !important;
            padding: 14px 18px !important;
            min-width: 300px !important;
            max-width: 380px !important;
        }
        .toastify-colse-btn {
            opacity: 0.6 !important;
            transition: opacity 0.2s !important;
        }
        .toastify-colse-btn:hover { opacity: 1 !important; }
        .toastify-title {
            font-weight: 800 !important;
            font-size: 14px !important;
            margin-bottom: 2px !important;
        }
        .toastify-message {
            font-weight: 500 !important;
            font-size: 13px !important;
            opacity: 0.9 !important;
        }
        .toastify-icon {
            width: 22px !important;
            height: 22px !important;
            flex-shrink: 0 !important;
        }
        .toastify-progress {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            height: 3px !important;
            border-radius: 0 0 0 12px !important;
            opacity: 0.7 !important;
        }
        @keyframes toastProgress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
    <script>
    (function() {
        const icons = {
            success: '<svg class="toastify-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error: '<svg class="toastify-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            warning: '<svg class="toastify-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
            info: '<svg class="toastify-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };

        const styles = {
            success: { bg: 'linear-gradient(135deg, #024938, #013028)', accent: '#f9ac00' },
            error:   { bg: 'linear-gradient(135deg, #dc2626, #991b1b)', accent: '#fca5a5' },
            warning: { bg: 'linear-gradient(135deg, #d97706, #92400e)', accent: '#fde68a' },
            info:    { bg: 'linear-gradient(135deg, #2563eb, #1e3a8a)', accent: '#93c5fd' }
        };

        function showToast(type, title, message) {
            const s = styles[type] || styles.info;
            const icon = icons[type] || icons.info;
            const html = '<div style="display:flex;align-items:flex-start;gap:10px;">' +
                '<div style="color:' + s.accent + ';">' + icon + '</div>' +
                '<div style="flex:1;min-width:0;">' +
                    '<div class="toastify-title">' + title + '</div>' +
                    (message ? '<div class="toastify-message">' + message + '</div>' : '') +
                '</div>' +
                '</div>';

            const toast = Toastify({
                node: undefined,
                text: html,
                escapeMarkup: false,
                duration: 5000,
                close: true,
                gravity: 'top',
                position: 'right',
                stopOnFocus: true,
                style: {
                    background: s.bg,
                    color: '#fff',
                },
                onClick: function() {},
                offset: { x: 20, y: 20 },
            });

            toast.showToast();

            // Add progress bar to the toast element
            setTimeout(function() {
                const el = document.querySelector('.toastify:last-child');
                if (el) {
                    const bar = document.createElement('div');
                    bar.className = 'toastify-progress';
                    bar.style.background = s.accent;
                    bar.style.animation = 'toastProgress 5s linear forwards';
                    el.style.position = 'relative';
                    el.style.overflow = 'hidden';
                    el.appendChild(bar);
                }
            }, 50);
        }

        window.showToast = showToast;

        @if(session('status'))
            showToast('success', 'Success', {!! json_encode(session('status')) !!});
        @endif
        @if(session('error'))
            showToast('error', 'Error', {!! json_encode(session('error')) !!});
        @endif
        @if(session('warning'))
            showToast('warning', 'Warning', {!! json_encode(session('warning')) !!});
        @endif
        @if(session('info'))
            showToast('info', 'Info', {!! json_encode(session('info')) !!});
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast('error', 'Validation Error', {!! json_encode($error) !!});
            @endforeach
        @endif
    })();
    </script>

    {{-- Slideshow Script --}}
    <script>
    (function() {
        const slides = document.querySelectorAll('.auth-slide');
        const dots   = document.querySelectorAll('.slide-dot');
        if (slides.length === 0) return;
        let current = 0;

        function showSlide(idx) {
            slides.forEach((s, i) => {
                s.classList.toggle('opacity-100', i === idx);
                s.classList.toggle('opacity-0', i !== idx);
            });
            dots.forEach((d, i) => {
                d.className = d.className.replace(/bg-white\/(80|30)/, '').trim();
                d.classList.add(i === idx ? 'bg-white/80' : 'bg-white/30');
                if (i === idx) { d.classList.add('w-5'); d.classList.remove('w-2'); }
                else { d.classList.add('w-2'); d.classList.remove('w-5'); }
            });
            current = idx;
        }

        setInterval(() => {
            showSlide((current + 1) % slides.length);
        }, 4000);
    })();
    </script>

</body>
</html>
