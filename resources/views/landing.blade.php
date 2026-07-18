<?php
/*
|--------------------------------------------------------------------------
| WAZABIASHARA — Landing Page (landing.blade.php)  v2 FINTECH
|--------------------------------------------------------------------------
| Stack : Laravel Blade + Tailwind (CDN) + React 18 (CDN) + Animate.css
|         + SweetAlert2 + AJAX (fetch)
| Font  : Nunito (Bunny.net) — 400,500,600,700,800,900
| Rangi : Emerald (#024938 family) + Gold (#f9ac00 family)
| Lugha : Kiswahili (default) + English — dropdown juu (topbar)
| Icons : SVG pekee — HAKUNA emoji
| Hero  : Picha halisi => weka public/images/hero.jpg (fallback ya mtandaoni ipo)
*/

$appName  = config('app.name', 'Wazabiashara');
$slogan   = 'Biashara Yako, Mkononi Mwako';
$whatsapp = '255716212896'; // <-- Namba ya WhatsApp (bila +)
$year     = date('Y');
?>
<!DOCTYPE html>
<html lang="sw" id="htmlRoot" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $appName }} — {{ $slogan }}</title>
<meta name="description" content="Mfumo wa kifedha na usimamizi wa biashara za Tanzania — POS, stoo, matawi, madeni na ripoti. Web na Mobile. Offline mode. M-Pesa, Tigo Pesa, Airtel Money.">

<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo.png') }}">
<link rel="shortcut icon" href="{{ asset('logo.png') }}">
<meta name="theme-color" content="#024938">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Wazabiashara">
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="Wazabiashara">
<meta name="msapplication-TileColor" content="#024938">
<meta name="msapplication-tap-highlight" content="no">
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta property="og:title" content="{{ $appName }}">
<meta property="og:description" content="{{ $slogan }}">
<meta property="og:image" content="{{ asset('logo.png') }}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $appName }}">
<meta name="twitter:description" content="{{ $slogan }}">
<meta name="twitter:image" content="{{ asset('logo.png') }}">
<meta property="og:type" content="website">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Font: Nunito (Bunny.net) -->
<link rel="dns-prefetch" href="//fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- Tailwind CDN + custom palette -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: { sans: ['Nunito','sans-serif'] },
      screens: { xs: '400px' },
      colors: {
        emerald: {50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816'},
        gold:    {50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600'}
      },
      boxShadow: {
        card: '0 10px 30px -12px rgba(1,36,31,.18)',
        cardlg: '0 24px 60px -20px rgba(1,36,31,.30)',
        gold: '0 12px 30px -10px rgba(249,172,0,.55)'
      }
    }
  }
}
</script>

<!-- React 18 + Babel -->
<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  :root{ --em:#024938; --em4:#1a9f8e; --gd:#f9ac00; --gd3:#ffc64d; --ink:#3b4652; }
  ::selection{ background:#ffd680; color:#01241f; }
  html{ -webkit-tap-highlight-color:transparent; }
  [x-cloak]{ display:none; }

  /* ============ Awning strip (brand signature) ============ */
  .awning{ display:flex; height:22px; filter:drop-shadow(0 4px 6px rgba(1,36,31,.15)); }
  .awning span{ flex:1; border:3px solid #3b4652; border-top:none; border-radius:0 0 999px 999px; margin:0 -1.5px; }
  .awning span:nth-child(odd){ background:#ef5350; }
  .awning span:nth-child(even){ background:#ffc64d; }

  /* ============ Custom keyframes (zinaongezea animate.css) ============ */
  @keyframes floaty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-14px)} }
  @keyframes floaty2{ 0%,100%{transform:translateY(0) rotate(-2deg)} 50%{transform:translateY(-9px) rotate(2deg)} }
  @keyframes pulse-ring { 0%{transform:scale(1);opacity:.6} 100%{transform:scale(1.9);opacity:0} }
  @keyframes wiggle { 0%,86%,100%{transform:rotate(0)} 88%{transform:rotate(12deg)} 92%{transform:rotate(-10deg)} 96%{transform:rotate(6deg)} }
  @keyframes shimmer { 0%{background-position:-500px 0} 100%{background-position:500px 0} }
  @keyframes blob { 0%,100%{border-radius:58% 42% 55% 45%/50% 55% 45% 50%} 50%{border-radius:45% 55% 42% 58%/55% 45% 55% 45%} }
  @keyframes ticker { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
  @keyframes spinslow { to{transform:rotate(360deg)} }
  .anim-float{ animation:floaty 6s ease-in-out infinite; }
  .anim-float2{ animation:floaty2 7s ease-in-out infinite; }
  .anim-blob{ animation:blob 12s ease-in-out infinite; }
  .anim-wiggle{ animation:wiggle 3.2s ease-in-out infinite; transform-origin:50% 90%; }
  .anim-spinslow{ animation:spinslow 22s linear infinite; }
  .ticker-track{ animation:ticker 28s linear infinite; }
  .ticker-wrap:hover .ticker-track{ animation-play-state:paused; }

  /* ============ Scroll reveal (inatumia Animate.css classes) ============ */
  [data-anim]{ opacity:0; }
  [data-anim].animate__animated{ opacity:1; }

  /* Feature card hover */
  .fcard{ transition:transform .35s cubic-bezier(.22,.9,.3,1), box-shadow .35s ease, border-color .35s; }
  .fcard:hover{ transform:translateY(-8px); box-shadow:0 24px 60px -20px rgba(1,36,31,.30); border-color:#80cbc0; }
  .fcard:hover .ficon{ transform:scale(1.08) rotate(-3deg); }
  .ficon{ transition:transform .35s cubic-bezier(.34,1.56,.64,1); }

  /* Buttons */
  .btn-gold{ background:linear-gradient(135deg,#ffc64d,#ffb71a); color:#01241f;
    box-shadow:0 12px 30px -10px rgba(249,172,0,.55); transition:transform .25s, box-shadow .25s, filter .25s; }
  .btn-gold:hover{ transform:translateY(-3px); filter:brightness(1.04); box-shadow:0 18px 40px -12px rgba(249,172,0,.65); }
  .btn-gold:active{ transform:translateY(-1px); }
  .btn-ghost{ transition:all .25s; }
  .btn-ghost:hover{ background:#e6f5f1; transform:translateY(-3px); }

  /* Header glass on scroll */
  #siteHeader{ transition:background .35s, box-shadow .35s, backdrop-filter .35s; }
  #siteHeader.scrolled{ background:rgba(255,255,255,.94); backdrop-filter:blur(12px);
    box-shadow:0 10px 30px -12px rgba(1,36,31,.18); }
  .nav-link{ position:relative; }
  .nav-link::after{ content:''; position:absolute; left:0; bottom:-4px; height:3px; width:0;
    border-radius:99px; background:linear-gradient(90deg,#f9ac00,#ffc64d); transition:width .3s; }
  .nav-link:hover::after{ width:100%; }

  /* Language dropdown */
  .lang-menu{ transform-origin:top right; transform:scale(.92); opacity:0; pointer-events:none;
    transition:transform .2s cubic-bezier(.22,.9,.3,1), opacity .2s; }
  .lang-menu.open{ transform:scale(1); opacity:1; pointer-events:auto; }

  /* Hero photo frame */
  .hero-frame{ position:relative; }
  .hero-frame::before{ content:''; position:absolute; inset:-14px -14px auto auto; width:60%; height:60%;
    border-radius:32px; background:linear-gradient(135deg,#ffc64d,#ffb71a); z-index:-1; transform:rotate(3deg); }
  .hero-frame::after{ content:''; position:absolute; inset:auto auto -14px -14px; width:55%; height:55%;
    border-radius:32px; background:linear-gradient(135deg,#4db5a8,#024938); z-index:-1; transform:rotate(-3deg); }
  .hero-img{ border-radius:28px; border:4px solid #fff; box-shadow:0 30px 70px -25px rgba(1,36,31,.45);
    width:100%; height:100%; object-fit:cover; aspect-ratio:4/4.4; object-position:center top; }
  @media (min-width:1024px){ .hero-img{ aspect-ratio:4/4; } }

  /* WhatsApp float */
  .wa-btn{ animation:floaty 3.4s ease-in-out infinite; }
  .wa-ring{ animation:pulse-ring 1.8s cubic-bezier(.4,0,.6,1) infinite; }

  .stat-strip{ background:linear-gradient(110deg,#013028 40%,#024938 50%,#013028 60%);
    background-size:1000px 100%; animation:shimmer 6s linear infinite; }

  @media (prefers-reduced-motion:reduce){
    *,*::before,*::after{ animation:none !important; transition:none !important; }
    [data-anim]{ opacity:1; }
  }
</style>
</head>

<body class="font-['Nunito',sans-serif] antialiased text-slate-800 min-h-screen bg-white overflow-x-hidden">

@php
/* ==================================================================
   SVG ICON HELPERS — flat + bold outline style (hakuna emoji popote)
   ================================================================== */
if (!function_exists('wz_icon')) {
  function wz_icon($name){
    $o = 'stroke="#3b4652" stroke-width="5" stroke-linejoin="round" stroke-linecap="round"';
    switch($name){
      case 'pos': return '<svg viewBox="0 0 96 96" class="w-14 h-14">
        <rect x="26" y="10" width="44" height="76" rx="10" fill="#f4f3f1" '.$o.'/>
        <rect x="34" y="22" width="28" height="30" rx="5" fill="#54b8e0" '.$o.'/>
        <rect x="34" y="60" width="12" height="10" rx="3" fill="#ffc64d" '.$o.'/>
        <rect x="50" y="60" width="12" height="10" rx="3" fill="#ef5350" '.$o.'/>
        <circle cx="48" cy="16" r="2.6" fill="#3b4652"/></svg>';
      case 'stock': return '<svg viewBox="0 0 96 96" class="w-14 h-14">
        <rect x="14" y="46" width="32" height="32" rx="6" fill="#ffc64d" '.$o.'/>
        <rect x="50" y="46" width="32" height="32" rx="6" fill="#ef5350" '.$o.'/>
        <rect x="32" y="14" width="32" height="32" rx="6" fill="#54b8e0" '.$o.'/>
        <path d="M48 14v10M30 46v10M66 46v10" '.$o.'/></svg>';
      case 'branch': return '<svg viewBox="0 0 96 96" class="w-14 h-14">
        <rect x="38" y="12" width="20" height="18" rx="5" fill="#ef5350" '.$o.'/>
        <rect x="12" y="62" width="20" height="18" rx="5" fill="#ffc64d" '.$o.'/>
        <rect x="38" y="62" width="20" height="18" rx="5" fill="#54b8e0" '.$o.'/>
        <rect x="64" y="62" width="20" height="18" rx="5" fill="#8fd9c9" '.$o.'/>
        <path d="M48 30v14M48 44H22v18M48 44v18M48 44h26v18" '.$o.' fill="none"/></svg>';
      case 'debt': return '<svg viewBox="0 0 96 96" class="w-14 h-14">
        <path d="M18 16h48l12 12v52H18z" fill="#f4f3f1" '.$o.'/>
        <path d="M66 16v12h12" fill="#ffc64d" '.$o.'/>
        <path d="M30 42h30M30 54h30M30 66h18" '.$o.'/>
        <circle cx="68" cy="70" r="14" fill="#ffc64d" '.$o.'/>
        <path d="M68 63v14M63.5 66.5h6.5a3.5 3.5 0 0 1 0 7H63" stroke="#3b4652" stroke-width="4" stroke-linecap="round" fill="none"/></svg>';
      case 'report': return '<svg viewBox="0 0 96 96" class="w-14 h-14">
        <rect x="14" y="14" width="68" height="68" rx="10" fill="#f4f3f1" '.$o.'/>
        <rect x="26" y="50" width="10" height="20" rx="3" fill="#54b8e0" '.$o.'/>
        <rect x="43" y="38" width="10" height="32" rx="3" fill="#ffc64d" '.$o.'/>
        <rect x="60" y="26" width="10" height="44" rx="3" fill="#ef5350" '.$o.'/></svg>';
      case 'offline': return '<svg viewBox="0 0 96 96" class="w-14 h-14">
        <path d="M16 42a44 44 0 0 1 64 0" fill="none" '.$o.'/>
        <path d="M28 56a27 27 0 0 1 40 0" fill="none" '.$o.'/>
        <circle cx="48" cy="72" r="8" fill="#ffc64d" '.$o.'/>
        <path d="M70 18 26 78" stroke="#ef5350" stroke-width="7" stroke-linecap="round"/></svg>';
    }
    return '';
  }
}
if (!function_exists('wz_mini')) {
  // Icons ndogo (16-20px) za ticker, topbar, footer n.k.
  function wz_mini($name, $cls='w-4 h-4'){
    $s = 'fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"';
    switch($name){
      case 'phone':  return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="M5 4h4l2 5-3 2a13 13 0 0 0 5 5l2-3 5 2v4a2 2 0 0 1-2 2A17 17 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg>';
      case 'mail':   return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 8 9 6 9-6"/></svg>';
      case 'pin':    return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/></svg>';
      case 'globe':  return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></svg>';
      case 'chev':   return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="m6 9 6 6 6-6"/></svg>';
      case 'check':  return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="m5 13 4 4L19 7"/></svg>';
      case 'card':   return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3 10h18M7 15h4"/></svg>';
      case 'wallet': return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="M4 7h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7z"/><path d="M4 7V6a2 2 0 0 1 2-2h10"/><circle cx="16.5" cy="13.5" r="1.2" fill="currentColor" stroke="none"/></svg>';
      case 'bank':   return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="m3 9 9-6 9 6M5 9v9m4-9v9m6-9v9m4-9v9M3 21h18"/></svg>';
      case 'receipt':return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/><path d="M9 8h6M9 12h6"/></svg>';
      case 'wifi':   return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="M2.5 9a15 15 0 0 1 19 0M6 12.5a10 10 0 0 1 12 0M9.5 16a5 5 0 0 1 5 0"/><circle cx="12" cy="19" r="1.4" fill="currentColor" stroke="none"/></svg>';
      case 'store':  return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="M4 9 5.5 4h13L20 9M4 9v11h16V9M4 9h16M10 20v-6h4v6"/></svg>';
      case 'chart':  return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="M4 20V10m6 10V4m6 16v-7"/><path d="M3 20h18"/></svg>';
      case 'shield': return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><path d="M12 3 5 6v5c0 4.5 3 8.2 7 10 4-1.8 7-5.5 7-10V6l-7-3z"/><path d="m9 12 2 2 4-4"/></svg>';
      case 'moneyphone': return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><rect x="7" y="2.5" width="10" height="19" rx="2.5"/><path d="M12 8v8M9.8 9.8h3.4a1.7 1.7 0 0 1 0 3.4H9.8"/></svg>';
      case 'arrow':  return '<svg class="'.$cls.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M13 5l7 7-7 7M4 12h16"/></svg>';
      case 'play':   return '<svg class="'.$cls.'" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.5v13l11-6.5z"/></svg>';
      case 'lock':   return '<svg class="'.$cls.'" viewBox="0 0 24 24" '.$s.'><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>';
      case 'flash':  return '<svg class="'.$cls.'" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/></svg>';
    }
    return '';
  }
}
@endphp

<!-- ================= HEADER (topbar + nav) ================= -->
<header id="siteHeader" class="fixed top-0 inset-x-0 z-50">

  <!-- ===== TOPBAR: mawasiliano + LANGUAGE DROPDOWN ===== -->
  <div class="bg-emerald-900 text-emerald-100 text-[11px] sm:text-[13px] font-bold relative z-[60]">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-9 sm:h-10 flex items-center justify-between gap-2">
      <div class="flex items-center gap-3 sm:gap-5 min-w-0">
        <a href="tel:+{{ $whatsapp }}" class="hidden sm:inline-flex items-center gap-1.5 hover:text-gold-300 transition shrink-0">
          {!! wz_mini('phone') !!} <span>+{{ $whatsapp }}</span>
        </a>
        <a href="mailto:info@wazabiashara.co.tz" class="hidden xs:inline-flex items-center gap-1.5 hover:text-gold-300 transition truncate min-w-0">
          {!! wz_mini('mail') !!} <span class="truncate">info@wazabiashara.co.tz</span>
        </a>
        <span class="hidden md:inline-flex items-center gap-1.5 text-emerald-300 shrink-0">
          {!! wz_mini('pin') !!} <span>Tanzania</span>
        </span>
      </div>

      <!-- Language dropdown -->
      <div class="relative shrink-0">
        <button id="langBtn" aria-haspopup="listbox" aria-expanded="false"
                class="inline-flex items-center gap-1.5 sm:gap-2 bg-emerald-800/80 hover:bg-emerald-700 border border-emerald-700 rounded-full pl-2.5 sm:pl-3 pr-2 sm:pr-2.5 py-1 sm:py-1.5 transition">
          <span class="text-gold-400">{!! wz_mini('globe') !!}</span>
          <span id="langLabel" class="text-[11px] sm:text-[13px]">Kiswahili</span>
          <span id="langChev" class="transition-transform duration-200">{!! wz_mini('chev','w-3.5 h-3.5') !!}</span>
        </button>
        <div id="langMenu" role="listbox"
             class="lang-menu absolute right-0 top-[calc(100%+8px)] w-44 bg-white text-emerald-800 rounded-2xl shadow-cardlg border-2 border-gray-100 overflow-hidden">
          <button data-lang="sw" class="lang-opt w-full flex items-center justify-between px-4 py-3 font-extrabold hover:bg-emerald-50 transition">
            <span class="flex items-center gap-2.5"><span class="h-6 w-6 grid place-items-center rounded-full bg-emerald-50 border border-emerald-200 text-[10px] font-black">SW</span> Kiswahili</span>
            <span class="lang-check text-emerald-500">{!! wz_mini('check') !!}</span>
          </button>
          <button data-lang="en" class="lang-opt w-full flex items-center justify-between px-4 py-3 font-extrabold hover:bg-emerald-50 transition border-t border-gray-100">
            <span class="flex items-center gap-2.5"><span class="h-6 w-6 grid place-items-center rounded-full bg-gold-50 border border-gold-200 text-[10px] font-black">EN</span> English</span>
            <span class="lang-check text-emerald-500 hidden">{!! wz_mini('check') !!}</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- awning signature strip -->
  <div class="awning" aria-hidden="true">
    <span></span><span></span><span></span><span></span><span></span><span></span>
    <span></span><span></span><span></span><span></span><span></span><span></span>
  </div>

  <!-- ===== NAVBAR ===== -->
  <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-[60px] sm:h-[72px] gap-2">

      <!-- Logo -->
      <a href="#home" class="flex items-center gap-2 sm:gap-3 group min-w-0">
        <span class="relative shrink-0">
          <img src="{{ asset('logo.png') }}" alt="{{ $appName }}"
               class="h-9 w-9 sm:h-11 sm:w-11 rounded-xl sm:rounded-2xl object-contain"
               onerror="this.style.display='none';document.getElementById('logoFallback').style.display='grid';">
          <span id="logoFallback" style="display:none"
                class="h-9 w-9 sm:h-11 sm:w-11 rounded-xl sm:rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-gold-400 font-black text-xl sm:text-2xl place-items-center border-[3px] border-emerald-800 shadow-card group-hover:rotate-6 transition-transform">W</span>
        </span>
        <span class="leading-tight min-w-0">
          <span class="block font-black text-base sm:text-xl text-emerald-500 tracking-tight truncate">{{ $appName }}</span>
          <span class="hidden xs:block text-[10px] sm:text-[11px] font-bold text-gold-600 -mt-0.5 truncate" data-i18n="slogan">{{ $slogan }}</span>
        </span>
      </a>

      <!-- Desktop nav -->
      <nav class="hidden lg:flex items-center gap-8 font-bold text-[15px] text-emerald-700">
        <a href="#home"       class="nav-link" data-i18n="nav_home">Nyumbani</a>
        <a href="#huduma"     class="nav-link" data-i18n="nav_features">Huduma</a>
        <a href="#jinsi"      class="nav-link" data-i18n="nav_how">Jinsi Inavyofanya Kazi</a>
        <a href="#maoni"      class="nav-link" data-i18n="nav_reviews">Maoni</a>
        <a href="#newsletter" class="nav-link" data-i18n="nav_join">Jiunge</a>
      </nav>

      <div class="hidden lg:flex items-center gap-3">
        @guest
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-7 py-3 rounded-xl shadow-md hover:shadow-lg transition-all inline-flex items-center gap-2" data-i18n="btn_getstarted">Anza Bure</a>
          @elseif (Route::has('login'))
            <a href="{{ route('login') }}" class="font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-7 py-3 rounded-xl shadow-md hover:shadow-lg transition-all inline-flex items-center gap-2" data-i18n="btn_getstarted">Ingia Sasa</a>
          @endif
        @else
          <a href="{{ url('/home') }}" class="font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-7 py-3 rounded-xl shadow-md hover:shadow-lg transition-all inline-flex items-center gap-2" data-i18n="btn_dash">Dashibodi</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        @endguest
      </div>

      <!-- Mobile burger -->
      <button id="burger" aria-label="Fungua menyu" aria-expanded="false"
              class="lg:hidden relative h-10 w-10 sm:h-11 sm:w-11 grid place-items-center rounded-xl border-2 border-emerald-200 text-emerald-600 active:scale-95 transition-transform shrink-0">
        <svg id="burgerOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24">
          <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
        <svg id="burgerClose" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24">
          <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>
  </div>

  <!-- Mobile menu -->
  <div id="mobileMenu" class="lg:hidden hidden bg-white/95 backdrop-blur-xl border-t border-gray-100 shadow-cardlg animate__animated animate__fadeIn animate__faster">
    <nav class="px-4 sm:px-6 py-4 sm:py-5 flex flex-col gap-1 font-extrabold text-emerald-700 max-h-[70vh] overflow-y-auto">
      <a href="#home"       class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50 active:bg-emerald-100" data-i18n="nav_home">Nyumbani</a>
      <a href="#huduma"     class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50 active:bg-emerald-100" data-i18n="nav_features">Huduma</a>
      <a href="#jinsi"      class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50 active:bg-emerald-100" data-i18n="nav_how">Jinsi Inavyofanya Kazi</a>
      <a href="#maoni"      class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50 active:bg-emerald-100" data-i18n="nav_reviews">Maoni</a>
      <a href="#newsletter" class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50 active:bg-emerald-100" data-i18n="nav_join">Jiunge</a>
      <div class="flex gap-3 pt-3">
        @guest
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="flex-1 text-center font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-5 py-3 rounded-xl shadow-md hover:shadow-lg transition-all" data-i18n="btn_getstarted">Anza Bure</a>
          @elseif (Route::has('login'))
            <a href="{{ route('login') }}" class="flex-1 text-center font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-5 py-3 rounded-xl shadow-md hover:shadow-lg transition-all" data-i18n="btn_getstarted">Ingia Sasa</a>
          @endif
        @else
          <a href="{{ url('/home') }}" class="flex-1 text-center font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-5 py-3 rounded-xl shadow-md hover:shadow-lg transition-all" data-i18n="btn_dash">Dashibodi</a>
          <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        @endguest
      </div>
    </nav>
  </div>
</header>

<!-- ================= HERO ================= -->
<section id="home" class="relative pt-[120px] sm:pt-[150px] lg:pt-[168px] pb-16 lg:pb-24 bg-gradient-to-b from-emerald-50 via-white to-white">
  <!-- ambient blobs -->
  <div class="pointer-events-none absolute -top-10 -left-24 w-[420px] h-[420px] bg-emerald-400/15 anim-blob blur-2xl"></div>
  <div class="pointer-events-none absolute top-40 -right-24 w-[380px] h-[380px] bg-gold-400/20 anim-blob blur-2xl" style="animation-delay:-4s"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 lg:gap-14 items-center relative">

    <!-- Copy -->
    <div class="text-center lg:text-left">
      <span class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 border-2 border-emerald-800 text-white font-black text-sm px-5 py-2.5 rounded-md shadow-md"
            data-anim="fadeInDown">
        <span class="text-gold-400">{!! wz_mini('shield','w-5 h-5') !!}</span>
        <span data-i18n="hero_badge">Jukwaa la kifedha la biashara za Tanzania</span>
      </span>

      <h1 class="mt-6 font-black text-[38px] leading-[1.06] sm:text-6xl text-emerald-700" data-anim="fadeInUp">
        <span data-i18n="hero_h1a">Fedha za Biashara Yako,</span><br>
        <span class="relative inline-block text-emerald-500">
          <span data-i18n="hero_h1b">Udhibiti Kamili.</span>
          <svg class="absolute -bottom-3 left-0 w-full" height="14" viewBox="0 0 220 14" fill="none" preserveAspectRatio="none">
            <path d="M4 10 C 60 2, 160 2, 216 9" stroke="#f9ac00" stroke-width="7" stroke-linecap="round"/>
          </svg>
        </span>
      </h1>

      <p class="mt-7 text-lg text-gray-500 font-semibold max-w-xl mx-auto lg:mx-0" data-anim="fadeInUp" data-delay="1"
         data-i18n-html="hero_p">
        Mauzo, stoo, matawi, wafanyakazi na madeni — kwenye mfumo mmoja salama.
        Inafanya kazi hata <span class="text-emerald-500 font-extrabold">bila intaneti</span>,
        na malipo ya <span class="text-emerald-500 font-extrabold">M-Pesa, Tigo Pesa na Airtel Money</span> moja kwa moja.
      </p>

      <div class="mt-9 flex flex-wrap items-center justify-center lg:justify-start gap-4" data-anim="fadeInUp" data-delay="2">
        @guest
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="font-bold text-base sm:text-lg text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-7 sm:px-8 py-4 rounded-xl shadow-md hover:shadow-lg transition-all inline-flex items-center gap-2">
              <span data-i18n="hero_cta1">Fungua Akaunti Bure</span> {!! wz_mini('arrow','w-5 h-5') !!}
            </a>
          @endif
        @else
          <a href="{{ url('/home') }}" class="font-bold text-base sm:text-lg text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-7 sm:px-8 py-4 rounded-xl shadow-md hover:shadow-lg transition-all inline-flex items-center gap-2">
            <span data-i18n="btn_godash">Nenda Dashibodi</span> {!! wz_mini('arrow','w-5 h-5') !!}
          </a>
        @endguest
        <a href="#jinsi" class="btn-ghost font-bold text-emerald-600 px-6 sm:px-7 py-4 rounded-xl border-2 border-emerald-200 inline-flex items-center gap-2">
          {!! wz_mini('play','w-5 h-5') !!} <span data-i18n="hero_cta2">Ona Jinsi Inavyofanya</span>
        </a>
      </div>

      <!-- trust row -->
      <div class="mt-9 flex items-center justify-center lg:justify-start gap-5" data-anim="fadeInUp" data-delay="3">
        <div class="flex -space-x-3">
          <span class="h-11 w-11 rounded-full border-[3px] border-white bg-emerald-400 grid place-items-center text-white font-black">J</span>
          <span class="h-11 w-11 rounded-full border-[3px] border-white bg-gold-400 grid place-items-center text-emerald-800 font-black">N</span>
          <span class="h-11 w-11 rounded-full border-[3px] border-white bg-emerald-600 grid place-items-center text-gold-300 font-black">B</span>
          <span class="h-11 w-11 rounded-full border-[3px] border-white bg-gold-200 grid place-items-center text-emerald-700 font-black">+</span>
        </div>
        <p class="text-sm font-bold text-gray-500 text-left" data-i18n-html="hero_trust">
          Wafanyabiashara <span class="text-emerald-500">1,000+</span> wanaamini Wazabiashara
        </p>
      </div>

      <!-- security mini-badges (fintech trust) -->
      <div class="mt-7 flex flex-wrap items-center justify-center lg:justify-start gap-2.5 text-[12px] font-black text-white" data-anim="fadeInUp" data-delay="4">
        <span class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 border-2 border-emerald-800 rounded-md px-3.5 py-2 shadow-md">
          <span class="text-gold-400">{!! wz_mini('lock','w-4 h-4') !!}</span> <span data-i18n="badge_ssl">Usimbaji wa SSL/TLS</span>
        </span>
        <span class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 border-2 border-emerald-800 rounded-md px-3.5 py-2 shadow-md">
          <span class="text-gold-400">{!! wz_mini('shield','w-4 h-4') !!}</span> <span data-i18n="badge_audit">Audit Trail Kamili</span>
        </span>
        <span class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 border-2 border-emerald-800 rounded-md px-3.5 py-2 shadow-md">
          <span class="text-gold-400">{!! wz_mini('flash','w-4 h-4') !!}</span> <span data-i18n="badge_rt">Ripoti za Papo Hapo</span>
        </span>
      </div>
    </div>

    <!-- ===== Visual: PICHA HALISI + floating fintech cards ===== -->
    <div class="relative hero-frame mx-2 sm:mx-6 lg:mx-0" data-anim="zoomIn" data-delay="1">
      {{-- Picha halisi: public/images/2148761600.jpg (mfanyabiashara/POS).
           Fallback: public/images/2148777464.jpg --}}
      <img src="{{ asset('images/2148761600.jpg') }}"
           onerror="this.onerror=null;this.src='{{ asset('images/2148777464.jpg') }}';"
           alt="Mfanyabiashara akitumia Wazabiashara POS"
           class="hero-img" loading="eager">

      <!-- Floating card: Mauzo (kushoto juu) -->
      <div class="anim-float absolute -left-3 sm:-left-8 top-8 sm:top-14 bg-white rounded-2xl shadow-cardlg border-2 border-gray-100 px-4 sm:px-5 py-3.5 flex items-center gap-3 scale-90 sm:scale-100 origin-left">
        <span class="h-11 w-11 grid place-items-center rounded-xl bg-emerald-50 border-2 border-emerald-200 text-emerald-500">
          {!! wz_mini('chart','w-6 h-6') !!}
        </span>
        <div>
          <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide" data-i18n="card_sales">Mauzo ya Leo</p>
          <p class="font-black text-emerald-600 text-lg">TZS <span data-count="1240000">0</span></p>
        </div>
      </div>

      <!-- Floating card: Malipo yamepokelewa (kulia kati) -->
      <div class="anim-float2 absolute -right-3 sm:-right-8 top-[42%] bg-white rounded-2xl shadow-cardlg border-2 border-gray-100 px-4 sm:px-5 py-3.5 flex items-center gap-3 scale-90 sm:scale-100 origin-right">
        <span class="h-11 w-11 grid place-items-center rounded-xl bg-gold-50 border-2 border-gold-200 text-gold-600 anim-wiggle">
          {!! wz_mini('moneyphone','w-6 h-6') !!}
        </span>
        <div>
          <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide" data-i18n="card_pay">Malipo Yamepokelewa</p>
          <p class="font-black text-emerald-600 text-sm">M-Pesa <span class="text-gray-400 font-bold">•</span> TZS 45,000</p>
        </div>
      </div>

      <!-- Floating chip: Offline (chini kushoto) -->
      <div class="anim-float absolute left-4 -bottom-4 bg-emerald-500 text-white rounded-full px-4 py-2 text-xs font-extrabold shadow-cardlg flex items-center gap-2" style="animation-delay:-2.5s">
        <span class="text-gold-400">{!! wz_mini('wifi','w-4 h-4') !!}</span> <span data-i18n="chip_offline">Offline Mode Imewashwa</span>
      </div>

      <!-- Rotating accent ring -->
      <svg class="anim-spinslow pointer-events-none absolute -top-8 -right-8 w-20 h-20 text-gold-400/70 hidden sm:block" viewBox="0 0 100 100" fill="none">
        <circle cx="50" cy="50" r="42" stroke="currentColor" stroke-width="6" stroke-dasharray="10 14" stroke-linecap="round"/>
      </svg>
    </div>
  </div>

  <!-- Ticker: njia za malipo (icons, si emoji) -->
  <div class="ticker-wrap mt-14 border-y-2 border-emerald-100 bg-white/70 backdrop-blur overflow-hidden">
    <div class="ticker-track flex whitespace-nowrap py-3.5 font-extrabold text-emerald-600/80 text-sm">
      @php
        $ticks = [
          ['moneyphone','tick_mpesa','M-Pesa'], ['moneyphone','tick_tigo','Tigo Pesa'],
          ['moneyphone','tick_airtel','Airtel Money'], ['wallet','tick_halo','Halopesa'],
          ['bank','tick_bank','Benki na Kadi'], ['receipt','tick_receipt','Risiti za SMS'],
          ['wifi','tick_offline','Offline Mode'], ['store','tick_branch','Matawi Mengi'],
          ['chart','tick_report','Ripoti Live'], ['shield','tick_secure','Usalama wa Data'],
        ];
      @endphp
      @for ($r=0; $r<2; $r++)
        @foreach ($ticks as $t)
          <span class="mx-6 inline-flex items-center gap-2">
            <span class="text-gold-500">{!! wz_mini($t[0],'w-4 h-4') !!}</span>
            <em class="not-italic" data-i18n="{{ $t[1] }}">{{ $t[2] }}</em>
          </span>
        @endforeach
      @endfor
    </div>
  </div>
</section>

<!-- ================= STATS ================= -->
<section class="relative overflow-hidden bg-emerald-800">
  {{-- Pattern background --}}
  <div class="absolute inset-0 opacity-15" style="background-image:url('{{ asset('flat-abstract-background-pattern-vector_822782-866.jpg') }}');background-size:cover;background-position:center;"></div>
  <div class="absolute inset-0 bg-gradient-to-r from-emerald-800/90 via-emerald-800/80 to-emerald-900/90"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16 relative z-10">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      <div class="bg-white/10 backdrop-blur-md border-2 border-emerald-400/20 rounded-2xl px-4 py-6 text-center" data-anim="fadeInUp">
        <p class="font-black text-3xl sm:text-4xl text-gold-400"><span data-count="1000">0</span>+</p>
        <p class="mt-2 text-[12px] sm:text-sm font-bold text-emerald-100" data-i18n="st1">Biashara Zinazotumia</p>
      </div>
      <div class="bg-white/10 backdrop-blur-md border-2 border-emerald-400/20 rounded-2xl px-4 py-6 text-center" data-anim="fadeInUp" data-delay="1">
        <p class="font-black text-3xl sm:text-4xl text-gold-400"><span data-count="26">0</span></p>
        <p class="mt-2 text-[12px] sm:text-sm font-bold text-emerald-100" data-i18n="st2">Mikoa ya Tanzania</p>
      </div>
      <div class="bg-white/10 backdrop-blur-md border-2 border-emerald-400/20 rounded-2xl px-4 py-6 text-center" data-anim="fadeInUp" data-delay="2">
        <p class="font-black text-3xl sm:text-4xl text-gold-400"><span data-count="99">0</span>.5%</p>
        <p class="mt-2 text-[12px] sm:text-sm font-bold text-emerald-100" data-i18n="st3">Upatikanaji (Uptime)</p>
      </div>
      <div class="bg-white/10 backdrop-blur-md border-2 border-emerald-400/20 rounded-2xl px-4 py-6 text-center" data-anim="fadeInUp" data-delay="3">
        <p class="font-black text-3xl sm:text-4xl text-gold-400"><span data-count="15">0</span>s</p>
        <p class="mt-2 text-[12px] sm:text-sm font-bold text-emerald-100" data-i18n="st4">Kukamilisha Muuzo</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= FEATURES ================= -->
<section id="huduma" class="py-20 sm:py-24 bg-white relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto">
      <span class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 border-2 border-emerald-800 text-white font-black text-xs uppercase tracking-widest px-5 py-2.5 rounded-md shadow-md" data-anim="fadeInDown">
        <span class="text-gold-400">{!! wz_mini('store','w-4 h-4') !!}</span>
        <span data-i18n="feat_eyebrow">Huduma Zetu</span>
      </span>
      <h2 class="mt-5 font-black text-3xl sm:text-5xl text-emerald-700" data-anim="fadeInUp" data-i18n="feat_h2">Miundombinu ya Kifedha ya Biashara Yako</h2>
      <p class="mt-4 text-gray-500 font-semibold" data-anim="fadeInUp" data-delay="1" data-i18n="feat_p">Kuanzia kaunta ya duka hadi ripoti za mmiliki — kila muamala uko salama na unaonekana.</p>
    </div>

    <div class="mt-14 sm:mt-16 grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
      @php
      $features = [
        ['k'=>'f1','img'=>'pos.png'],
        ['k'=>'f2','img'=>'invetory.png'],
        ['k'=>'f3','img'=>'branches.png'],
        ['k'=>'f4','img'=>'ledgers.png'],
        ['k'=>'f5','img'=>'pos.png'],
        ['k'=>'f6','img'=>'offline.png'],
      ];
      $ftxt = [
        'f1'=>['POS ya Kasi ya Umeme','Uza kwa sekunde — scan barcode kwa kamera ya simu, toa risiti kwa printer, SMS au WhatsApp.'],
        'f2'=>['Stoo Inayojisimamia','Tahadhari za bidhaa zinazoisha na zinazo-expire. Hesabu stoo na uone tofauti papo hapo.'],
        'f3'=>['Matawi Sehemu Moja','Kila tawi na stoo yake na hesabu zake — wewe unaona yote live, popote ulipo.'],
        'f4'=>['Kitabu cha Madeni','Madeni hayasahauliki tena — SMS za kukumbusha wateja zinatumwa zenyewe.'],
        'f5'=>['Ripoti za Bosi','Faida, hasara, mauzo kwa tawi na mfanyakazi — kwa siku, wiki, mwezi. PDF na Excel.'],
        'f6'=>['Inafanya Kazi Offline','Intaneti ikikatika mauzo yanaendelea — ikirudi, kila kitu kina-sync chenyewe.'],
      ];
      @endphp

      @foreach($features as $i => $f)
      <article class="fcard bg-white rounded-2xl border-2 border-gray-100 shadow-card p-5 sm:p-7" data-anim="fadeInUp" data-delay="{{ ($i%3)+1 }}">
        <img src="{{ asset('images/' . $f['img']) }}" alt="{{ $ftxt[$f['k']][0] }}" class="w-12 h-12 sm:w-14 sm:h-14 object-contain" loading="lazy">
        <h3 class="mt-4 font-black text-base sm:text-xl text-emerald-700" data-i18n="{{ $f['k'] }}_t">{{ $ftxt[$f['k']][0] }}</h3>
        <p class="mt-2 text-gray-500 font-semibold text-xs sm:text-[15px] leading-relaxed" data-i18n="{{ $f['k'] }}_d">{{ $ftxt[$f['k']][1] }}</p>
      </article>
      @endforeach
    </div>
  </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section id="jinsi" class="py-20 sm:py-24 bg-gradient-to-b from-emerald-50/60 to-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto">
      <span class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 border-2 border-emerald-800 text-white font-black text-xs uppercase tracking-widest px-5 py-2.5 rounded-md shadow-md" data-anim="fadeInDown">
        <span class="text-gold-400">{!! wz_mini('flash','w-4 h-4') !!}</span>
        <span data-i18n="how_eyebrow">Hatua 3 Tu</span>
      </span>
      <h2 class="mt-5 font-black text-3xl sm:text-5xl text-emerald-700" data-anim="fadeInUp" data-i18n="how_h2">Jisajili Leo, Uza Leo</h2>
    </div>

    {{-- Stepper --}}
    <div class="mt-14 sm:mt-16 max-w-5xl mx-auto" data-anim="fadeInUp" data-delay="1">
      @php
      $stxt = [
        ['1','Jisajili kwa Simu','Weka jina, namba ya simu na jina la biashara yako. Thibitisha kwa OTP — dakika 2 tu.'],
        ['2','Ongeza Bidhaa Zako','Weka bidhaa, bei na wafanyakazi wako. Una matawi? Yaongeze yote sehemu moja.'],
        ['3','Anza Kuuza na Kuona Faida','POS ya kasi, risiti za SMS, na dashibodi inayokuonyesha kila senti — live.'],
      ];
      @endphp

      {{-- Horizontal stepper bar --}}
      <ol class="flex items-center w-full p-4 sm:p-6 space-x-2 sm:space-x-4 text-sm font-bold text-center bg-emerald-50 border-2 border-emerald-200 rounded-2xl shadow-card">
        @foreach($stxt as $i => $s)
        <li class="flex items-center {{ $i === 0 ? 'text-emerald-700' : 'text-gray-400' }} {{ $i === count($stxt)-1 ? 'flex-1' : 'flex-1' }}">
          <span class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 me-2 text-sm sm:text-base font-black border-2 {{ $i === 0 ? 'border-emerald-600 text-emerald-700 bg-white' : 'border-gray-300 text-gray-400 bg-white' }} rounded-full shrink-0">
            {{ $s[0] }}
          </span>
          <span class="hidden sm:inline">{{ $s[1] }}</span>
          <span class="sm:hidden">{{ explode(' ', $s[1])[0] }}</span>
          @if($i < count($stxt)-1)
          <svg class="w-5 h-5 ms-2 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 16 4-4-4-4m6 8 4-4-4-4"/></svg>
          @endif
        </li>
        @endforeach
      </ol>

      {{-- Step details --}}
      <div class="mt-8 grid sm:grid-cols-3 gap-6">
        @foreach($stxt as $i => $s)
        <div class="bg-white rounded-2xl border-2 border-gray-100 shadow-card p-5 sm:p-6" data-anim="fadeInUp" data-delay="{{ $i+1 }}">
          <span class="flex items-center justify-center w-10 h-10 mb-3 font-black text-lg border-2 border-emerald-600 text-emerald-700 bg-emerald-50 rounded-full">
            {{ $s[0] }}
          </span>
          <h3 class="font-black text-base sm:text-lg text-emerald-700" data-i18n="s{{ $i+1 }}_t">{{ $s[1] }}</h3>
          <p class="mt-2 text-gray-500 font-semibold text-sm leading-relaxed" data-i18n="s{{ $i+1 }}_d">{{ $s[2] }}</p>
        </div>
        @endforeach
      </div>
    </div>

    <div class="mt-12 text-center" data-anim="fadeInUp" data-delay="3">
      @guest
        @if (Route::has('register'))
          <a href="{{ route('register') }}" class="font-bold text-base sm:text-lg text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-8 sm:px-9 py-4 rounded-xl shadow-md hover:shadow-lg transition-all inline-flex items-center gap-2">
            <span data-i18n="how_cta">Fungua Akaunti Sasa</span> {!! wz_mini('arrow','w-5 h-5') !!}
          </a>
        @endif
      @else
        <a href="{{ url('/home') }}" class="font-bold text-base sm:text-lg text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-8 sm:px-9 py-4 rounded-xl shadow-md hover:shadow-lg transition-all inline-flex items-center gap-2">
          <span data-i18n="btn_godash">Nenda Dashibodi</span> {!! wz_mini('arrow','w-5 h-5') !!}
        </a>
      @endguest
    </div>
  </div>
</section>

<!-- ================= TESTIMONIALS (REACT) ================= -->
<section id="maoni" class="py-20 sm:py-24 relative overflow-hidden bg-emerald-800">
  {{-- Background pattern image --}}
  <div class="absolute inset-0 opacity-20" style="background-image:url('{{ asset('flat-abstract-background-pattern-vector_822782-866.jpg') }}');background-size:cover;background-position:center;"></div>
  <div class="absolute inset-0 bg-gradient-to-b from-emerald-800/80 via-emerald-800/70 to-emerald-900/90"></div>

  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center">
      <span class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 border-2 border-emerald-800 text-white font-black text-xs uppercase tracking-widest px-5 py-2.5 rounded-md shadow-md" data-anim="fadeInDown">
        <span class="text-gold-400">{!! wz_mini('star','w-4 h-4') !!}</span>
        <span data-i18n="rev_eyebrow">Maoni ya Wateja</span>
      </span>
      <h2 class="mt-5 font-black text-3xl sm:text-5xl text-white" data-anim="fadeInUp" data-i18n="rev_h2">Wafanyabiashara Wanasemaje?</h2>
    </div>
    <div id="react-testimonials" class="mt-12 sm:mt-14"></div>
  </div>
</section>

<!-- ================= NEWSLETTER (AJAX + SweetAlert) ================= -->
<section id="newsletter" class="py-20 sm:py-24 bg-white">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="relative rounded-[32px] bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900 p-8 sm:p-14 overflow-hidden shadow-cardlg" data-anim="zoomIn">
      <div class="absolute -top-14 -right-14 h-52 w-52 rounded-full bg-gold-400/20 blur-xl"></div>
      <div class="absolute -bottom-16 -left-10 h-44 w-44 rounded-full bg-emerald-400/20 blur-xl"></div>
      <div class="awning absolute top-0 inset-x-0" aria-hidden="true">
        <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
      </div>

      <div class="relative text-center">
        <h2 class="font-black text-2xl sm:text-4xl text-white" data-i18n-html="news_h2">Usikose Habari za <span class="text-gold-400">Wazabiashara</span></h2>
        <p class="mt-3 text-emerald-100 font-semibold max-w-xl mx-auto text-sm sm:text-base" data-i18n="news_p">Jiunge na jarida letu — vidokezo vya fedha na biashara, huduma mpya na ofa maalum, moja kwa moja kwenye barua pepe yako.</p>

        <form id="newsletterForm" class="mt-8 flex flex-col sm:flex-row gap-3 max-w-xl mx-auto" novalidate>
          <div class="relative flex-1">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-300">{!! wz_mini('mail','w-5 h-5') !!}</span>
            <input id="newsEmail" type="email" required placeholder="Barua pepe yako…" data-i18n-ph="news_ph"
                   class="w-full rounded-2xl bg-white/95 pl-12 pr-4 py-4 font-bold text-emerald-800 placeholder:text-gray-400 outline-none border-2 border-transparent focus:border-gold-400 transition">
          </div>
          <button id="newsBtn" type="submit"
                  class="font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 px-8 py-4 rounded-xl shadow-md hover:shadow-lg transition-all inline-flex items-center justify-center gap-2 min-w-[150px]">
            <span class="btn-label" data-i18n="news_btn">Jiunge Sasa</span>
            <svg class="btn-spin hidden w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
          </button>
        </form>
        <p class="mt-4 text-xs font-bold text-emerald-200 inline-flex items-center gap-1.5">{!! wz_mini('lock','w-3.5 h-3.5') !!} <span data-i18n="news_note">Hatutakutumia spam. Unaweza kujitoa wakati wowote.</span></p>
      </div>
    </div>
  </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="bg-emerald-900 text-emerald-100 pt-14 sm:pt-16 pb-8 relative overflow-hidden">
  {{-- Subtle pattern overlay --}}
  <div class="absolute inset-0 opacity-5" style="background-image:url('{{ asset('flat-abstract-background-pattern-vector_822782-866.jpg') }}');background-size:cover;background-position:center;"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    {{-- Main footer grid --}}
    <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-8 sm:gap-10">
      {{-- Brand --}}
      <div class="sm:col-span-2">
        <a href="#home" class="flex items-center gap-3">
          <img src="{{ asset('logo.png') }}" alt="{{ $appName }}" class="h-12 w-12 rounded-xl object-contain"
               onerror="this.style.display='none';this.nextElementSibling.style.display='grid';">
          <span style="display:none"
                class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-gold-400 font-black text-2xl grid place-items-center border-2 border-emerald-700">W</span>
          <span>
            <span class="block font-black text-xl text-white">{{ $appName }}</span>
            <span class="block text-[11px] font-bold text-gold-400" data-i18n="slogan">{{ $slogan }}</span>
          </span>
        </a>
        <p class="mt-5 text-sm font-semibold text-emerald-200/80 max-w-sm" data-i18n="foot_about">Jukwaa la kifedha na usimamizi wa biashara za Tanzania — POS, stoo, matawi, madeni na ripoti. Web na Mobile, hata bila intaneti.</p>

        {{-- Social media icons --}}
        <div class="mt-6 flex items-center gap-3">
          <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="WhatsApp"
             class="grid place-items-center h-10 w-10 rounded-xl bg-emerald-800 border-2 border-emerald-700 text-emerald-300 hover:bg-gold-400 hover:text-emerald-900 hover:border-gold-500 transition-all">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.5 0 1.47 1.07 2.9 1.22 3.1.15.2 2.1 3.2 5.1 4.49.71.3 1.27.49 1.7.63.72.23 1.37.2 1.88.12.58-.09 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35zM12.05 21.8h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.85 9.85 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.9-9.88a9.82 9.82 0 0 1 7 2.9 9.82 9.82 0 0 1 2.9 7c0 5.45-4.45 9.87-9.9 9.87zm8.42-18.3A11.8 11.8 0 0 0 12.04 0C5.46 0 .1 5.35.1 11.93c0 2.1.55 4.16 1.6 5.97L0 24l6.24-1.64a11.93 11.93 0 0 0 5.8 1.48h.01c6.58 0 11.93-5.35 11.93-11.93 0-3.19-1.24-6.19-3.5-8.42z"/></svg>
          </a>
          <a href="#" aria-label="Facebook"
             class="grid place-items-center h-10 w-10 rounded-xl bg-emerald-800 border-2 border-emerald-700 text-emerald-300 hover:bg-gold-400 hover:text-emerald-900 hover:border-gold-500 transition-all">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.07C24 5.41 18.63 0 12 0S0 5.41 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.69.24 2.69.24v2.97h-1.52c-1.49 0-1.96.93-1.96 1.89v2.25h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07z"/></svg>
          </a>
          <a href="#" aria-label="Instagram"
             class="grid place-items-center h-10 w-10 rounded-xl bg-emerald-800 border-2 border-emerald-700 text-emerald-300 hover:bg-gold-400 hover:text-emerald-900 hover:border-gold-500 transition-all">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.43.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.43.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.48-1.38-.9-.42-.42-.68-.82-.9-1.38-.16-.43-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.43-.16 1.06-.36 2.23-.41 1.27-.06 1.65-.07 4.85-.07M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63c-.79.31-1.46.72-2.13 1.38C1.35 2.68.94 3.35.63 4.14.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.79.72 1.46 1.38 2.13.67.66 1.34 1.07 2.13 1.38.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56.79-.31 1.46-.72 2.13-1.38.66-.67 1.07-1.34 1.38-2.13.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91-.31-.79-.72-1.46-1.38-2.13C21.32 1.35 20.65.94 19.86.63c-.76-.3-1.64-.5-2.91-.56C15.67.01 15.26 0 12 0zm0 5.84a6.16 6.16 0 1 0 0 12.32 6.16 6.16 0 0 0 0-12.32zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.41-10.85a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88z"/></svg>
          </a>
          <a href="#" aria-label="X"
             class="grid place-items-center h-10 w-10 rounded-xl bg-emerald-800 border-2 border-emerald-700 text-emerald-300 hover:bg-gold-400 hover:text-emerald-900 hover:border-gold-500 transition-all">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <a href="#" aria-label="YouTube"
             class="grid place-items-center h-10 w-10 rounded-xl bg-emerald-800 border-2 border-emerald-700 text-emerald-300 hover:bg-gold-400 hover:text-emerald-900 hover:border-gold-500 transition-all">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.51A3.02 3.02 0 0 0 .5 6.2C0 8.08 0 12 0 12s0 3.92.5 5.8a3.02 3.02 0 0 0 2.12 2.14c1.88.51 9.38.51 9.38.51s7.5 0 9.38-.51a3.02 3.02 0 0 0 2.12-2.14C24 15.92 24 12 24 12s0-3.92-.5-5.8zM9.6 15.6V8.4l6.2 3.6z"/></svg>
          </a>
        </div>
      </div>

      {{-- Links --}}
      <div>
        <h4 class="flex items-center gap-2 font-black text-white uppercase text-sm tracking-wider">
          <span class="text-gold-400">&gt;</span>
          <span data-i18n="foot_links">Viungo</span>
        </h4>
        <ul class="mt-4 space-y-3 text-sm font-bold">
          <li><a class="inline-flex items-center gap-1.5 hover:text-gold-300 transition" href="#huduma" data-i18n="nav_features"><span class="text-gold-500/60">&rsaquo;</span>Huduma</a></li>
          <li><a class="inline-flex items-center gap-1.5 hover:text-gold-300 transition" href="#jinsi" data-i18n="nav_how"><span class="text-gold-500/60">&rsaquo;</span>Jinsi Inavyofanya Kazi</a></li>
          <li><a class="inline-flex items-center gap-1.5 hover:text-gold-300 transition" href="#maoni" data-i18n="nav_reviews"><span class="text-gold-500/60">&rsaquo;</span>Maoni</a></li>
          <li><a class="inline-flex items-center gap-1.5 hover:text-gold-300 transition" href="#newsletter" data-i18n="nav_join"><span class="text-gold-500/60">&rsaquo;</span>Jiunge</a></li>
        </ul>
      </div>

      {{-- Contact --}}
      <div>
        <h4 class="flex items-center gap-2 font-black text-white uppercase text-sm tracking-wider">
          <span class="text-gold-400">&gt;</span>
          <span data-i18n="foot_contact">Mawasiliano</span>
        </h4>
        <ul class="mt-4 space-y-3 text-sm font-bold">
          <li class="flex items-center gap-2"><span class="text-gold-400">{!! wz_mini('phone') !!}</span> +{{ $whatsapp }}</li>
          <li class="flex items-center gap-2"><span class="text-gold-400">{!! wz_mini('mail') !!}</span> info@wazabiashara.co.tz</li>
          <li class="flex items-center gap-2"><span class="text-gold-400">{!! wz_mini('pin') !!}</span> Tanzania</li>
        </ul>
      </div>
    </div>

    {{-- Bottom bar --}}
    <div class="mt-12 pt-6 border-t border-emerald-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-bold text-emerald-300/80">
      <p>&copy; {{ $year }} {{ $appName }}. <span data-i18n="foot_rights">Haki zote zimehifadhiwa.</span></p>
      <div class="flex items-center gap-2">
        <span data-i18n="foot_made">Imetengenezwa Tanzania</span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-800 border border-emerald-700 text-emerald-200">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Dar es Salaam
        </span>
      </div>
    </div>
  </div>
</footer>

<!-- ================= WHATSAPP FLOAT ================= -->
<a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Habari! Nataka kujua zaidi kuhusu Wazabiashara.') }}"
   target="_blank" rel="noopener" aria-label="WhatsApp"
   class="wa-btn fixed bottom-5 right-5 sm:bottom-6 sm:right-6 z-50 group">
  <span class="wa-ring absolute inset-0 rounded-full bg-[#25D366]"></span>
  <span class="relative grid place-items-center h-14 w-14 sm:h-16 sm:w-16 rounded-full bg-[#25D366] shadow-cardlg border-[3px] border-white">
    <svg class="w-7 h-7 sm:w-8 sm:h-8" viewBox="0 0 24 24" fill="#fff"><path d="M17.5 14.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.5 0 1.47 1.07 2.9 1.22 3.1.15.2 2.1 3.2 5.1 4.49.71.3 1.27.49 1.7.63.72.23 1.37.2 1.88.12.58-.09 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35zM12.05 21.8h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.85 9.85 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.9-9.88a9.82 9.82 0 0 1 7 2.9 9.82 9.82 0 0 1 2.9 7c0 5.45-4.45 9.87-9.9 9.87zm8.42-18.3A11.8 11.8 0 0 0 12.04 0C5.46 0 .1 5.35.1 11.93c0 2.1.55 4.16 1.6 5.97L0 24l6.24-1.64a11.93 11.93 0 0 0 5.8 1.48h.01c6.58 0 11.93-5.35 11.93-11.93 0-3.19-1.24-6.19-3.5-8.42z"/></svg>
  </span>
  <span class="absolute right-[68px] sm:right-[76px] top-1/2 -translate-y-1/2 whitespace-nowrap bg-white text-emerald-700 font-extrabold text-sm px-4 py-2 rounded-xl shadow-card border-2 border-gray-100 opacity-0 translate-x-2 pointer-events-none transition-all group-hover:opacity-100 group-hover:translate-x-0"
        data-i18n="wa_tip">Tuandikie WhatsApp</span>
</a>

<!-- ================= SCRIPTS ================= -->
<script>
/* =========================================================
   I18N — Kiswahili (default) + English
   ========================================================= */
const I18N = {
  sw: {
    slogan:'Biashara Yako, Mkononi Mwako',
    nav_home:'Nyumbani', nav_features:'Huduma', nav_how:'Jinsi Inavyofanya Kazi', nav_reviews:'Maoni', nav_join:'Jiunge',
    btn_login:'Ingia', btn_register:'Anza Bure', btn_dash:'Dashibodi', btn_logout:'Toka', btn_godash:'Nenda Dashibodi', btn_getstarted:'Anza Bure',
    hero_badge:'Jukwaa la kifedha la biashara za Tanzania',
    hero_h1a:'Fedha za Biashara Yako,', hero_h1b:'Udhibiti Kamili.',
    hero_p:'Mauzo, stoo, matawi, wafanyakazi na madeni — kwenye mfumo mmoja salama. Inafanya kazi hata <span class="text-emerald-500 font-extrabold">bila intaneti</span>, na malipo ya <span class="text-emerald-500 font-extrabold">M-Pesa, Tigo Pesa na Airtel Money</span> moja kwa moja.',
    hero_cta1:'Fungua Akaunti Bure', hero_cta2:'Ona Jinsi Inavyofanya',
    hero_trust:'Wafanyabiashara <span class="text-emerald-500">1,000+</span> wanaamini Wazabiashara',
    badge_ssl:'Usimbaji wa SSL/TLS', badge_audit:'Audit Trail Kamili', badge_rt:'Ripoti za Papo Hapo',
    card_sales:'Mauzo ya Leo', card_pay:'Malipo Yamepokelewa', chip_offline:'Offline Mode Imewashwa',
    tick_mpesa:'M-Pesa', tick_tigo:'Tigo Pesa', tick_airtel:'Airtel Money', tick_halo:'Halopesa',
    tick_bank:'Benki na Kadi', tick_receipt:'Risiti za SMS', tick_offline:'Offline Mode',
    tick_branch:'Matawi Mengi', tick_report:'Ripoti Live', tick_secure:'Usalama wa Data',
    st1:'Biashara Zinazotumia', st2:'Mikoa ya Tanzania', st3:'Upatikanaji (Uptime)', st4:'Kukamilisha Muuzo',
    feat_eyebrow:'Huduma Zetu', feat_h2:'Miundombinu ya Kifedha ya Biashara Yako',
    feat_p:'Kuanzia kaunta ya duka hadi ripoti za mmiliki — kila muamala uko salama na unaonekana.',
    feat_more:'Jifunze zaidi',
    f1_t:'POS ya Kasi ya Umeme', f1_d:'Uza kwa sekunde — scan barcode kwa kamera ya simu, toa risiti kwa printer, SMS au WhatsApp.',
    f2_t:'Stoo Inayojisimamia', f2_d:'Tahadhari za bidhaa zinazoisha na zinazo-expire. Hesabu stoo na uone tofauti papo hapo.',
    f3_t:'Matawi Sehemu Moja', f3_d:'Kila tawi na stoo yake na hesabu zake — wewe unaona yote live, popote ulipo.',
    f4_t:'Kitabu cha Madeni', f4_d:'Madeni hayasahauliki tena — SMS za kukumbusha wateja zinatumwa zenyewe.',
    f5_t:'Ripoti za Bosi', f5_d:'Faida, hasara, mauzo kwa tawi na mfanyakazi — kwa siku, wiki, mwezi. PDF na Excel.',
    f6_t:'Inafanya Kazi Offline', f6_d:'Intaneti ikikatika mauzo yanaendelea — ikirudi, kila kitu kina-sync chenyewe.',
    how_eyebrow:'Hatua 3 Tu', how_h2:'Jisajili Leo, Uza Leo', how_cta:'Fungua Akaunti Sasa',
    s1_t:'Jisajili kwa Simu', s1_d:'Weka jina, namba ya simu na jina la biashara yako. Thibitisha kwa OTP — dakika 2 tu.',
    s2_t:'Ongeza Bidhaa Zako', s2_d:'Weka bidhaa, bei na wafanyakazi wako. Una matawi? Yaongeze yote sehemu moja.',
    s3_t:'Anza Kuuza na Kuona Faida', s3_d:'POS ya kasi, risiti za SMS, na dashibodi inayokuonyesha kila senti — live.',
    rev_eyebrow:'Maoni ya Wateja', rev_h2:'Wafanyabiashara Wanasemaje?',
    news_h2:'Usikose Habari za <span class="text-gold-400">Wazabiashara</span>',
    news_p:'Jiunge na jarida letu — vidokezo vya fedha na biashara, huduma mpya na ofa maalum, moja kwa moja kwenye barua pepe yako.',
    news_ph:'Barua pepe yako…', news_btn:'Jiunge Sasa', news_sending:'Inatuma…',
    news_note:'Hatutakutumia spam. Unaweza kujitoa wakati wowote.',
    foot_about:'Jukwaa la kifedha na usimamizi wa biashara za Tanzania — POS, stoo, matawi, madeni na ripoti. Web na Mobile, hata bila intaneti.',
    foot_links:'Viungo', foot_contact:'Mawasiliano', foot_rights:'Haki zote zimehifadhiwa.', foot_made:'Imetengenezwa Tanzania',
    wa_tip:'Tuandikie WhatsApp',
    sw_ok_t:'Karibu Wazabiashara!', sw_ok_d:'Umejiunga na jarida letu kikamilifu.',
    sw_bad_t:'Barua pepe si sahihi', sw_bad_d:'Tafadhali andika barua pepe sahihi, mfano: jina@mfano.co.tz',
    sw_err_t:'Samahani!', sw_err_d:'Imeshindikana kujiunga. Jaribu tena.',
    sw_net_t:'Tatizo la Mtandao', sw_net_d:'Imeshindikana kuwasiliana na seva. Tafadhali jaribu tena baadaye.',
  },
  en: {
    slogan:'Your Business, In Your Hands',
    nav_home:'Home', nav_features:'Features', nav_how:'How It Works', nav_reviews:'Reviews', nav_join:'Subscribe',
    btn_login:'Sign In', btn_register:'Start Free', btn_dash:'Dashboard', btn_logout:'Log Out', btn_godash:'Go to Dashboard', btn_getstarted:'Get Started',
    hero_badge:'The financial platform for Tanzanian businesses',
    hero_h1a:'Your Business Finances,', hero_h1b:'Fully in Control.',
    hero_p:'Sales, inventory, branches, staff and credit — in one secure platform. Works even <span class="text-emerald-500 font-extrabold">without internet</span>, with direct <span class="text-emerald-500 font-extrabold">M-Pesa, Tigo Pesa and Airtel Money</span> payments.',
    hero_cta1:'Open a Free Account', hero_cta2:'See How It Works',
    hero_trust:'<span class="text-emerald-500">1,000+</span> business owners trust Wazabiashara',
    badge_ssl:'SSL/TLS Encryption', badge_audit:'Full Audit Trail', badge_rt:'Real-Time Reports',
    card_sales:'Today\u2019s Sales', card_pay:'Payment Received', chip_offline:'Offline Mode Active',
    tick_mpesa:'M-Pesa', tick_tigo:'Tigo Pesa', tick_airtel:'Airtel Money', tick_halo:'Halopesa',
    tick_bank:'Banks and Cards', tick_receipt:'SMS Receipts', tick_offline:'Offline Mode',
    tick_branch:'Multi-Branch', tick_report:'Live Reports', tick_secure:'Data Security',
    st1:'Businesses On Board', st2:'Regions of Tanzania', st3:'Platform Uptime', st4:'To Complete a Sale',
    feat_eyebrow:'What You Get', feat_h2:'The Financial Infrastructure for Your Business',
    feat_p:'From the shop counter to the owner\u2019s reports — every transaction is secure and visible.',
    feat_more:'Learn more',
    f1_t:'Lightning-Fast POS', f1_d:'Sell in seconds — scan barcodes with your phone camera, issue receipts via printer, SMS or WhatsApp.',
    f2_t:'Self-Managing Inventory', f2_d:'Low-stock and expiry alerts. Run stock counts and see variances instantly.',
    f3_t:'All Branches, One View', f3_d:'Each branch keeps its own stock and books — you see everything live, wherever you are.',
    f4_t:'Digital Credit Ledger', f4_d:'Debts are never forgotten — automatic SMS reminders go out to your customers.',
    f5_t:'Owner-Grade Reports', f5_d:'Profit, loss, sales by branch and staff — daily, weekly, monthly. PDF and Excel.',
    f6_t:'Works Offline', f6_d:'If the internet drops, sales continue — when it\u2019s back, everything syncs automatically.',
    how_eyebrow:'Just 3 Steps', how_h2:'Sign Up Today, Sell Today', how_cta:'Open an Account Now',
    s1_t:'Register by Phone', s1_d:'Enter your name, phone number and business name. Verify with OTP — 2 minutes.',
    s2_t:'Add Your Products', s2_d:'Add products, prices and your staff. Have branches? Bring them all in one place.',
    s3_t:'Start Selling and Track Profit', s3_d:'A fast POS, SMS receipts, and a dashboard that shows you every shilling — live.',
    rev_eyebrow:'Customer Stories', rev_h2:'What Business Owners Say',
    news_h2:'Never Miss an Update from <span class="text-gold-400">Wazabiashara</span>',
    news_p:'Join our newsletter — finance and business tips, new features and special offers, straight to your inbox.',
    news_ph:'Your email address…', news_btn:'Subscribe', news_sending:'Sending…',
    news_note:'No spam, ever. Unsubscribe anytime.',
    foot_about:'The financial and business management platform for Tanzania — POS, inventory, branches, credit and reports. Web and Mobile, even offline.',
    foot_links:'Links', foot_contact:'Contact', foot_rights:'All rights reserved.', foot_made:'Proudly built in Tanzania',
    wa_tip:'Chat with us on WhatsApp',
    sw_ok_t:'Welcome to Wazabiashara!', sw_ok_d:'You have successfully joined our newsletter.',
    sw_bad_t:'Invalid email address', sw_bad_d:'Please enter a valid email, e.g. name@example.co.tz',
    sw_err_t:'Sorry!', sw_err_d:'Subscription failed. Please try again.',
    sw_net_t:'Network Error', sw_net_d:'Could not reach the server. Please try again later.',
  }
};

let LANG = 'sw';
try { LANG = localStorage.getItem('wz_lang') || 'sw'; } catch(e) {}
window.WZ_LANG = LANG;

function applyLang(lang){
  LANG = I18N[lang] ? lang : 'sw';
  window.WZ_LANG = LANG;
  try { localStorage.setItem('wz_lang', LANG); } catch(e) {}
  const d = I18N[LANG];
  document.getElementById('htmlRoot').setAttribute('lang', LANG);
  document.querySelectorAll('[data-i18n]').forEach(el => { const k = el.dataset.i18n; if (d[k] != null) el.textContent = d[k]; });
  document.querySelectorAll('[data-i18n-html]').forEach(el => { const k = el.dataset.i18nHtml; if (d[k] != null) el.innerHTML = d[k]; });
  document.querySelectorAll('[data-i18n-ph]').forEach(el => { const k = el.dataset.i18nPh; if (d[k] != null) el.placeholder = d[k]; });
  document.getElementById('langLabel').textContent = LANG === 'sw' ? 'Kiswahili' : 'English';
  document.querySelectorAll('.lang-opt').forEach(btn => {
    btn.querySelector('.lang-check').classList.toggle('hidden', btn.dataset.lang !== LANG);
  });
  document.dispatchEvent(new CustomEvent('wz:lang', { detail: LANG }));
}

/* ---------- Language dropdown ---------- */
const langBtn = document.getElementById('langBtn'),
      langMenu = document.getElementById('langMenu'),
      langChev = document.getElementById('langChev');
function toggleLang(force){
  const open = force != null ? force : !langMenu.classList.contains('open');
  langMenu.classList.toggle('open', open);
  langBtn.setAttribute('aria-expanded', open);
  langChev.style.transform = open ? 'rotate(180deg)' : '';
}
langBtn.addEventListener('click', e => { e.stopPropagation(); toggleLang(); });
document.addEventListener('click', () => toggleLang(false));
langMenu.addEventListener('click', e => e.stopPropagation());
document.querySelectorAll('.lang-opt').forEach(btn =>
  btn.addEventListener('click', () => { applyLang(btn.dataset.lang); toggleLang(false); }));

/* ---------- Header scroll ---------- */
const header = document.getElementById('siteHeader');
const onScroll = () => header.classList.toggle('scrolled', scrollY > 24);
addEventListener('scroll', onScroll, {passive:true}); onScroll();

/* ---------- Mobile menu ---------- */
const burger = document.getElementById('burger'),
      menu   = document.getElementById('mobileMenu'),
      icoO   = document.getElementById('burgerOpen'),
      icoC   = document.getElementById('burgerClose');
const closeMenu = () => { menu.classList.add('hidden'); icoO.classList.remove('hidden'); icoC.classList.add('hidden'); burger.setAttribute('aria-expanded','false'); };
burger.addEventListener('click', () => {
  const open = menu.classList.toggle('hidden') === false;
  icoO.classList.toggle('hidden', open); icoC.classList.toggle('hidden', !open);
  burger.setAttribute('aria-expanded', open);
});
document.querySelectorAll('.mob-link').forEach(a => a.addEventListener('click', closeMenu));

/* ---------- Scroll reveal (Animate.css) ---------- */
const DELAYS = ['0s','.12s','.24s','.36s','.48s','.6s'];
const io = new IntersectionObserver(es => es.forEach(e => {
  if (!e.isIntersecting) return;
  const el = e.target;
  el.style.animationDelay = DELAYS[+(el.dataset.delay || 0)] || '0s';
  el.classList.add('animate__animated', 'animate__' + el.dataset.anim);
  io.unobserve(el);
}), {threshold:.14});
document.querySelectorAll('[data-anim]').forEach(el => io.observe(el));

/* ---------- Animated counters ---------- */
const fmt = n => n.toLocaleString(LANG === 'sw' ? 'sw-TZ' : 'en-US');
const cio = new IntersectionObserver(es => es.forEach(e => {
  if (!e.isIntersecting) return; cio.unobserve(e.target);
  const el = e.target, end = +el.dataset.count, t0 = performance.now(), dur = 1600;
  const tick = t => { const p = Math.min((t - t0)/dur, 1), ease = 1 - Math.pow(1-p, 3);
    el.textContent = fmt(Math.round(end * ease)); if (p < 1) requestAnimationFrame(tick); };
  requestAnimationFrame(tick);
}), {threshold:.6});
document.querySelectorAll('[data-count]').forEach(el => cio.observe(el));

/* ---------- Newsletter: AJAX + SweetAlert2 ---------- */
const swalBrand = Swal.mixin({
  confirmButtonColor:'#024938',
  customClass:{ popup:'rounded-3xl', title:'font-black', confirmButton:'font-extrabold rounded-xl px-6 py-3' }
});
document.getElementById('newsletterForm').addEventListener('submit', async ev => {
  ev.preventDefault();
  const d = I18N[LANG];
  const email = document.getElementById('newsEmail').value.trim();
  const btn = document.getElementById('newsBtn');
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)){
    return swalBrand.fire({icon:'warning', title:d.sw_bad_t, text:d.sw_bad_d});
  }
  btn.disabled = true;
  btn.querySelector('.btn-label').textContent = d.news_sending;
  btn.querySelector('.btn-spin').classList.remove('hidden');
  try{
    const res = await fetch('{{ route("newsletter.subscribe") }}', {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With':'XMLHttpRequest'
      },
      body: JSON.stringify({email})
    });
    const data = await res.json();
    if (data.success){
      document.getElementById('newsletterForm').reset();
      swalBrand.fire({icon:'success', title:d.sw_ok_t, text:data.message || d.sw_ok_d, timer:3500, timerProgressBar:true});
    } else {
      swalBrand.fire({icon:'error', title:d.sw_err_t, text:data.message || d.sw_err_d});
    }
  } catch(err){
    swalBrand.fire({icon:'error', title:d.sw_net_t, text:d.sw_net_d});
  } finally{
    btn.disabled = false;
    btn.querySelector('.btn-label').textContent = d.news_btn;
    btn.querySelector('.btn-spin').classList.add('hidden');
  }
});

/* Apply saved language on load */
applyLang(LANG);
</script>

<!-- ---------- React: Testimonials carousel (bilingual) ---------- -->
<script type="text/babel">
const { useState, useEffect, useCallback } = React;

const T_DATA = {
  sw: [
    { name:'Mangi Josephat', biz:'Mangi Traders — Kariakoo, Dar', stars:5,
      text:'Nina matawi matatu — zamani nilikuwa napiga simu kila jioni kuuliza mauzo. Sasa nafungua app tu, naona kila tawi live. Wazabiashara imenirudishia usingizi.' },
    { name:'Neema Michael', biz:'Neema Pharmacy — Mwanza', stars:5,
      text:'Tahadhari za dawa zinazo-expire zimeniokolea mtaji mkubwa. Na wateja wanapenda risiti za SMS — duka linaonekana la kisasa kweli.' },
    { name:'Baraka John', biz:'Baraka Hardware — Arusha', stars:5,
      text:'Madeni yalikuwa yananimaliza. Sasa SMS za kukumbusha zinatumwa zenyewe na wateja wanalipa kwa wakati. Mauzo ya deni yamekuwa salama.' },
    { name:'Zainabu Ally', biz:'Zainabu Shop — Dodoma', stars:5,
      text:'Hata intaneti ikikatika duka linaendelea kuuza — ikirudi kila kitu kinajipanga chenyewe. Hii ndiyo teknolojia ya Tanzania halisi.' },
  ],
  en: [
    { name:'Mangi Josephat', biz:'Mangi Traders — Kariakoo, Dar', stars:5,
      text:'I run three branches — I used to call every evening asking for the day\u2019s sales. Now I just open the app and see every branch live. Wazabiashara gave me my sleep back.' },
    { name:'Neema Michael', biz:'Neema Pharmacy — Mwanza', stars:5,
      text:'Expiry alerts have saved me serious capital. And customers love the SMS receipts — the shop truly feels modern now.' },
    { name:'Baraka John', biz:'Baraka Hardware — Arusha', stars:5,
      text:'Customer debts were killing me. Now reminder SMS go out automatically and people pay on time. Credit sales finally feel safe.' },
    { name:'Zainabu Ally', biz:'Zainabu Shop — Dodoma', stars:5,
      text:'Even when the internet drops, the shop keeps selling — when it\u2019s back, everything syncs itself. This is technology built for Tanzania.' },
  ]
};

function Stars({n}){
  return <div className="flex justify-center gap-1 text-gold-400 text-xl" aria-label={n + "/5"}>
    {Array.from({length:n}).map((_,i)=>(
      <svg key={i} className="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.5 15 9l7 .8-5.2 4.7 1.4 6.9L12 18l-6.2 3.4 1.4-6.9L2 9.8 9 9z"/></svg>
    ))}
  </div>;
}

function Testimonials(){
  const [lang, setLang] = useState(window.WZ_LANG || 'sw');
  const [i, setI] = useState(0);
  const [fade, setFade] = useState(true);
  const DATA = T_DATA[lang] || T_DATA.sw;

  useEffect(() => {
    const h = e => { setLang(e.detail); setI(0); };
    document.addEventListener('wz:lang', h);
    return () => document.removeEventListener('wz:lang', h);
  }, []);

  const go = useCallback(next => {
    setFade(false);
    setTimeout(() => { setI(next); setFade(true); }, 220);
  }, []);

  useEffect(() => {
    const t = setInterval(() => go((i+1) % DATA.length), 6000);
    return () => clearInterval(t);
  }, [i, go, DATA.length]);

  const c = DATA[i];

  return (
    <div className="relative">
      <div className={"bg-white rounded-2xl shadow-cardlg border-2 border-emerald-100 px-6 py-10 sm:px-14 sm:py-12 text-center transition-all duration-300 " + (fade ? "opacity-100 translate-y-0" : "opacity-0 translate-y-3")}>
        <span className="inline-grid place-items-center h-14 w-14 sm:h-16 sm:w-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 border-2 border-emerald-800 text-2xl sm:text-3xl font-black text-white mb-5 shadow-md">
          {c.name.charAt(0)}
        </span>
        <Stars n={c.stars}/>
        <p className="mt-5 text-base sm:text-xl font-bold text-emerald-800 leading-relaxed max-w-2xl mx-auto">&ldquo;{c.text}&rdquo;</p>
        <p className="mt-6 font-black text-emerald-600">{c.name}</p>
        <p className="text-sm font-bold text-gray-400">{c.biz}</p>
      </div>

      <button onClick={() => go((i-1+DATA.length)%DATA.length)} aria-label="Prev"
        className="absolute -left-1 sm:-left-6 top-1/2 -translate-y-1/2 h-11 w-11 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-r from-gold-300 to-gold-400 border-2 border-gold-500 shadow-md grid place-items-center text-emerald-900 hover:scale-110 transition-transform">
        <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      </button>
      <button onClick={() => go((i+1)%DATA.length)} aria-label="Next"
        className="absolute -right-1 sm:-right-6 top-1/2 -translate-y-1/2 h-11 w-11 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-r from-gold-300 to-gold-400 border-2 border-gold-500 shadow-md grid place-items-center text-emerald-900 hover:scale-110 transition-transform">
        <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="m9 6 6 6-6 6"/></svg>
      </button>

      <div className="mt-8 flex justify-center gap-2.5">
        {DATA.map((_,d)=>(
          <button key={d} onClick={()=>go(d)} aria-label={"#"+(d+1)}
            className={"h-3 rounded-md transition-all duration-300 " + (d===i ? "w-9 bg-gold-400" : "w-3 bg-emerald-300/50 hover:bg-emerald-300")}/>
        ))}
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('react-testimonials')).render(<Testimonials/>);
</script>

<!-- ===== PWA: Service Worker Registration + Install Prompt ===== -->
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('SW registered:', reg.scope))
      .catch(err => console.log('SW registration failed:', err));
  });
}

/* PWA Install Prompt — shows once, never again after dismiss/install */
let deferredPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  if (localStorage.getItem('wz_pwa_dismissed') === '1') return;
  deferredPrompt = e;
  showInstallBanner();
});

function showInstallBanner() {
  if (!deferredPrompt) return;
  const banner = document.createElement('div');
  banner.id = 'pwa-install-banner';
  banner.style.cssText = 'position:fixed;bottom:0;left:0;right:0;z-index:9999;background:linear-gradient(135deg,#024938,#013028);border-top:3px solid #f9ac00;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 -8px 30px rgba(0,0,0,.2);animation:slideUp .4s ease';
  banner.innerHTML = `
    <img src="/logo.png" alt="Wazabiashara" style="width:42px;height:42px;border-radius:10px;flex-shrink:0">
    <div style="flex:1;min-width:0">
      <p style="font-weight:900;color:#fff;font-size:14px;margin:0;font-family:Nunito,sans-serif">Sakinisha Wazabiashara</p>
      <p style="font-weight:600;color:#a7d3cc;font-size:12px;margin:2px 0 0;font-family:Nunito,sans-serif">Tumia kama app — haraka na offline</p>
    </div>
    <button id="pwa-install-btn" style="background:linear-gradient(to right,#f9ac00,#d49700);color:#01241f;font-weight:900;font-size:13px;padding:10px 18px;border:none;border-radius:10px;cursor:pointer;flex-shrink:0;font-family:Nunito,sans-serif;white-space:nowrap">Sakinisha</button>
    <button id="pwa-dismiss-btn" style="background:transparent;color:#a7d3cc;font-weight:700;font-size:18px;padding:4px 8px;border:none;cursor:pointer;flex-shrink:0;font-family:Nunito,sans-serif">×</button>
  `;
  document.body.appendChild(banner);

  document.getElementById('pwa-install-btn').addEventListener('click', async () => {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    localStorage.setItem('wz_pwa_dismissed', '1');
    deferredPrompt = null;
    banner.remove();
  });

  document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
    localStorage.setItem('wz_pwa_dismissed', '1');
    deferredPrompt = null;
    banner.remove();
  });
}

window.addEventListener('appinstalled', () => {
  localStorage.setItem('wz_pwa_dismissed', '1');
  const b = document.getElementById('pwa-install-banner');
  if (b) b.remove();
  deferredPrompt = null;
});
</script>

</body>
</html>