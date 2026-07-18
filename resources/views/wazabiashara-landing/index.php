<?php
/*
|--------------------------------------------------------------------------
| WAZABIASHARA — Landing Page (index.php)
|--------------------------------------------------------------------------
| Stack : PHP + Tailwind (CDN) + React 18 (CDN) + SweetAlert2 + AJAX (fetch)
| Font  : Nunito (Bunny.net) — 400,500,600,700,800,900
| Rangi : Emerald (#024938 family) + Gold (#f9ac00 family)
| Icons : Flat + bold dark outline (kama sample uliyotoa)
| Logo  : Weka logo yako => assets/logo.png (fallback ipo tayari)
*/

$appName   = 'Wazabiashara';
$slogan    = 'Biashara Yako, Mkononi Mwako';
$whatsapp  = '255700000000'; // <-- BADILISHA: namba yako ya WhatsApp (bila +)
$year      = date('Y');
?>
<!DOCTYPE html>
<html lang="sw" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $appName ?> — <?= $slogan ?></title>
<meta name="description" content="Mfumo wa kisasa wa kusimamia biashara za Tanzania — POS, stoo, matawi, madeni na ripoti. Web na Mobile. Offline mode. M-Pesa, Tigo Pesa, Airtel Money.">

<!-- Font: Nunito (Bunny.net) -->
<link rel="dns-prefetch" href="//fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">

<!-- Tailwind CDN + custom palette -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: { sans: ['Nunito','sans-serif'] },
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

<!-- React 18 + Babel (kwa sehemu ya React) -->
<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  /* ============ Base ============ */
  :root{ --em:#024938; --em4:#1a9f8e; --gd:#f9ac00; --gd3:#ffc64d; --ink:#3b4652; }
  ::selection{ background:#ffd680; color:#01241f; }
  html{ -webkit-tap-highlight-color:transparent; }

  /* ============ Awning strip (signature — kama kibanda) ============ */
  .awning{ display:flex; height:26px; filter:drop-shadow(0 4px 6px rgba(1,36,31,.15)); }
  .awning span{ flex:1; border:3px solid #3b4652; border-top:none; border-radius:0 0 999px 999px; margin:0 -1.5px; }
  .awning span:nth-child(odd){ background:#ef5350; }
  .awning span:nth-child(even){ background:#ffc64d; }

  /* ============ Animations ============ */
  @keyframes floaty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-14px)} }
  @keyframes floaty2{ 0%,100%{transform:translateY(0) rotate(-2deg)} 50%{transform:translateY(-9px) rotate(2deg)} }
  @keyframes pulse-ring { 0%{transform:scale(1);opacity:.6} 100%{transform:scale(1.9);opacity:0} }
  @keyframes wiggle { 0%,86%,100%{transform:rotate(0)} 88%{transform:rotate(12deg)} 92%{transform:rotate(-10deg)} 96%{transform:rotate(6deg)} }
  @keyframes shimmer { 0%{background-position:-500px 0} 100%{background-position:500px 0} }
  @keyframes blob { 0%,100%{border-radius:58% 42% 55% 45%/50% 55% 45% 50%} 50%{border-radius:45% 55% 42% 58%/55% 45% 55% 45%} }
  @keyframes ticker { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
  .anim-float{ animation:floaty 6s ease-in-out infinite; }
  .anim-float2{ animation:floaty2 7s ease-in-out infinite; }
  .anim-blob{ animation:blob 12s ease-in-out infinite; }
  .anim-wiggle{ animation:wiggle 3.2s ease-in-out infinite; transform-origin:50% 90%; }
  .ticker-track{ animation:ticker 26s linear infinite; }
  .ticker-wrap:hover .ticker-track{ animation-play-state:paused; }

  /* Scroll reveal */
  .reveal{ opacity:0; transform:translateY(26px); transition:opacity .7s ease, transform .7s cubic-bezier(.22,.9,.3,1); }
  .reveal.in{ opacity:1; transform:none; }
  .reveal[data-d="1"]{ transition-delay:.08s } .reveal[data-d="2"]{ transition-delay:.16s }
  .reveal[data-d="3"]{ transition-delay:.24s } .reveal[data-d="4"]{ transition-delay:.32s }
  .reveal[data-d="5"]{ transition-delay:.40s } .reveal[data-d="6"]{ transition-delay:.48s }

  /* Feature card hover */
  .fcard{ transition:transform .35s cubic-bezier(.22,.9,.3,1), box-shadow .35s ease; }
  .fcard:hover{ transform:translateY(-8px); box-shadow:0 24px 60px -20px rgba(1,36,31,.30); }
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
  #siteHeader.scrolled{ background:rgba(255,255,255,.92); backdrop-filter:blur(12px);
    box-shadow:0 10px 30px -12px rgba(1,36,31,.18); }
  .nav-link{ position:relative; }
  .nav-link::after{ content:''; position:absolute; left:0; bottom:-4px; height:3px; width:0;
    border-radius:99px; background:linear-gradient(90deg,#f9ac00,#ffc64d); transition:width .3s; }
  .nav-link:hover::after{ width:100%; }

  /* WhatsApp float */
  .wa-btn{ animation:floaty 3.4s ease-in-out infinite; }
  .wa-ring{ animation:pulse-ring 1.8s cubic-bezier(.4,0,.6,1) infinite; }

  /* Counter shimmer strip */
  .stat-strip{ background:linear-gradient(110deg,#013028 40%,#024938 50%,#013028 60%);
    background-size:1000px 100%; animation:shimmer 6s linear infinite; }

  /* Phone mock scroll list */
  @keyframes salesroll { 0%{transform:translateY(0)} 100%{transform:translateY(-50%)} }
  .salesroll{ animation:salesroll 14s linear infinite; }

  @media (prefers-reduced-motion:reduce){
    *,*::before,*::after{ animation:none !important; transition:none !important; }
    .reveal{ opacity:1; transform:none; }
  }
</style>
</head>

<body class="font-['Nunito',sans-serif] antialiased text-slate-800 min-h-screen bg-white overflow-x-hidden">

<!-- ================= HEADER ================= -->
<header id="siteHeader" class="fixed top-0 inset-x-0 z-50">
  <!-- awning signature strip -->
  <div class="awning" aria-hidden="true">
    <span></span><span></span><span></span><span></span><span></span><span></span>
    <span></span><span></span><span></span><span></span><span></span><span></span>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-[76px]">

      <!-- Logo -->
      <a href="#home" class="flex items-center gap-3 group">
        <span class="relative">
          <!-- Weka logo yako: assets/logo.png -->
          <img src="assets/logo.png" alt="<?= $appName ?>"
               class="h-12 w-12 rounded-2xl object-contain"
               onerror="this.style.display='none';document.getElementById('logoFallback').style.display='grid';">
          <span id="logoFallback" style="display:none"
                class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-gold-400 font-black text-2xl place-items-center border-[3px] border-emerald-800 shadow-card group-hover:rotate-6 transition-transform">W</span>
        </span>
        <span class="leading-tight">
          <span class="block font-black text-xl text-emerald-500 tracking-tight"><?= $appName ?></span>
          <span class="block text-[11px] font-bold text-gold-600 -mt-0.5"><?= $slogan ?></span>
        </span>
      </a>

      <!-- Desktop nav -->
      <nav class="hidden lg:flex items-center gap-8 font-bold text-[15px] text-emerald-700">
        <a href="#home"      class="nav-link">Nyumbani</a>
        <a href="#huduma"    class="nav-link">Huduma</a>
        <a href="#jinsi"     class="nav-link">Jinsi Inavyofanya Kazi</a>
        <a href="#maoni"     class="nav-link">Maoni</a>
        <a href="#newsletter" class="nav-link">Jiunge</a>
      </nav>

      <div class="hidden lg:flex items-center gap-3">
        <a href="#" class="btn-ghost font-extrabold text-emerald-600 px-5 py-2.5 rounded-2xl border-2 border-emerald-200">Ingia</a>
        <a href="#" class="btn-gold font-extrabold px-6 py-3 rounded-2xl">Anza Bure</a>
      </div>

      <!-- Mobile burger -->
      <button id="burger" aria-label="Fungua menyu" aria-expanded="false"
              class="lg:hidden relative h-11 w-11 grid place-items-center rounded-xl border-2 border-emerald-200 text-emerald-600">
        <svg id="burgerOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24">
          <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
        <svg id="burgerClose" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24">
          <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>
  </div>

  <!-- Mobile menu -->
  <div id="mobileMenu" class="lg:hidden hidden bg-white/95 backdrop-blur-xl border-t border-gray-100 shadow-cardlg">
    <nav class="px-6 py-5 flex flex-col gap-1 font-extrabold text-emerald-700">
      <a href="#home"   class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50">Nyumbani</a>
      <a href="#huduma" class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50">Huduma</a>
      <a href="#jinsi"  class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50">Jinsi Inavyofanya Kazi</a>
      <a href="#maoni"  class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50">Maoni</a>
      <a href="#newsletter" class="mob-link px-4 py-3 rounded-xl hover:bg-emerald-50">Jiunge</a>
      <div class="flex gap-3 pt-3">
        <a href="#" class="flex-1 text-center btn-ghost font-extrabold text-emerald-600 px-5 py-3 rounded-2xl border-2 border-emerald-200">Ingia</a>
        <a href="#" class="flex-1 text-center btn-gold font-extrabold px-5 py-3 rounded-2xl">Anza Bure</a>
      </div>
    </nav>
  </div>
</header>

<!-- ================= HERO ================= -->
<section id="home" class="relative pt-[130px] lg:pt-[150px] pb-20 lg:pb-28 bg-gradient-to-b from-emerald-50 via-white to-white">
  <!-- ambient blobs -->
  <div class="pointer-events-none absolute -top-10 -left-24 w-[420px] h-[420px] bg-emerald-400/15 anim-blob blur-2xl"></div>
  <div class="pointer-events-none absolute top-40 -right-24 w-[380px] h-[380px] bg-gold-400/20 anim-blob blur-2xl" style="animation-delay:-4s"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-14 items-center relative">

    <!-- Copy -->
    <div>
      <span class="reveal inline-flex items-center gap-2 bg-white border-2 border-emerald-200 text-emerald-600 font-extrabold text-sm px-4 py-2 rounded-full shadow-card">
        <span class="h-2.5 w-2.5 rounded-full bg-gold-500 animate-pulse"></span>
        Imejengwa kwa ajili ya Tanzania 🇹🇿
      </span>

      <h1 class="reveal mt-6 font-black text-[42px] leading-[1.05] sm:text-6xl text-emerald-700" data-d="1">
        Endesha Duka Lako<br>
        <span class="relative inline-block text-emerald-500">
          Kama Bosi.
          <svg class="absolute -bottom-3 left-0 w-full" height="14" viewBox="0 0 220 14" fill="none" preserveAspectRatio="none">
            <path d="M4 10 C 60 2, 160 2, 216 9" stroke="#f9ac00" stroke-width="7" stroke-linecap="round"/>
          </svg>
        </span>
      </h1>

      <p class="reveal mt-7 text-lg text-gray-500 font-semibold max-w-xl" data-d="2">
        Mauzo, stoo, matawi, wafanyakazi na madeni — vyote kwenye mfumo mmoja.
        Web na simu. Inafanya kazi hata <span class="text-emerald-500 font-extrabold">bila intaneti</span>,
        na malipo ya <span class="text-emerald-500 font-extrabold">M-Pesa, Tigo Pesa na Airtel Money</span>.
      </p>

      <div class="reveal mt-9 flex flex-wrap items-center gap-4" data-d="3">
        <a href="#" class="btn-gold font-black text-lg px-8 py-4 rounded-2xl inline-flex items-center gap-2">
          Anza Bure Leo
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M4 12h16"/></svg>
        </a>
        <a href="#jinsi" class="btn-ghost font-extrabold text-emerald-600 px-7 py-4 rounded-2xl border-2 border-emerald-200 inline-flex items-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5.5v13l11-6.5z"/></svg>
          Ona Jinsi Inavyofanya
        </a>
      </div>

      <div class="reveal mt-9 flex items-center gap-5" data-d="4">
        <div class="flex -space-x-3">
          <span class="h-11 w-11 rounded-full border-[3px] border-white bg-emerald-400 grid place-items-center text-white font-black">J</span>
          <span class="h-11 w-11 rounded-full border-[3px] border-white bg-gold-400 grid place-items-center text-emerald-800 font-black">N</span>
          <span class="h-11 w-11 rounded-full border-[3px] border-white bg-emerald-600 grid place-items-center text-gold-300 font-black">B</span>
          <span class="h-11 w-11 rounded-full border-[3px] border-white bg-gold-200 grid place-items-center text-emerald-700 font-black">+</span>
        </div>
        <p class="text-sm font-bold text-gray-500">Wafanyabiashara <span class="text-emerald-500">1,000+</span> wanaamini Wazabiashara</p>
      </div>
    </div>

    <!-- Visual: duka + phone (flat outline style) -->
    <div class="relative reveal" data-d="2">
      <!-- Duka SVG -->
      <svg viewBox="0 0 520 430" class="w-full max-w-[560px] mx-auto drop-shadow-xl" role="img" aria-label="Duka la Wazabiashara">
        <defs>
          <style>.o{stroke:#3b4652;stroke-width:9;stroke-linejoin:round;stroke-linecap:round}</style>
        </defs>
        <!-- sign -->
        <rect x="120" y="18" width="280" height="52" rx="10" fill="#41546b" class="o"/>
        <rect x="150" y="36" width="220" height="14" rx="7" fill="#2f3d4f"/>
        <!-- awning -->
        <g>
          <path class="o" d="M60 78 L110 150 L60 150 Z" fill="#ef5350"/>
          <path class="o" d="M110 78 L170 150 L110 150 Z" fill="#ffc64d" transform="translate(0,0)"/>
        </g>
        <path class="o" d="M70 78 H450 L505 160 Q505 210 460 210 Q420 210 418 165 Q414 210 372 210 Q330 210 328 165 Q324 210 282 210 Q240 210 238 165 Q234 210 192 210 Q150 210 148 165 Q144 210 102 210 Q58 210 58 160 Z" fill="#ef5350"/>
        <path class="o" d="M140 80 L148 165 Q150 208 192 208 Q234 208 238 165 L222 80 Z" fill="#ffc64d"/>
        <path class="o" d="M300 80 L328 165 Q330 208 372 208 Q414 208 418 165 L382 80 Z" fill="#ffc64d"/>
        <!-- body -->
        <rect x="78" y="205" width="364" height="200" rx="8" fill="#f4f3f1" class="o"/>
        <!-- window -->
        <rect x="112" y="248" width="140" height="88" rx="10" fill="#54b8e0" class="o"/>
        <rect x="126" y="262" width="112" height="14" rx="7" fill="#7fd0ee"/>
        <!-- door -->
        <rect x="300" y="248" width="96" height="157" rx="8" fill="#ffc64d" class="o"/>
        <rect x="300" y="248" width="96" height="26" fill="#ffb71a" class="o"/>
        <circle cx="318" cy="340" r="7" fill="#3b4652"/>
        <!-- ground -->
        <line x1="60" y1="406" x2="460" y2="406" class="o"/>
      </svg>

      <!-- Floating card: Mauzo -->
      <div class="anim-float absolute -left-2 sm:left-0 top-24 bg-white rounded-2xl shadow-cardlg border-2 border-gray-100 px-5 py-4 flex items-center gap-3">
        <span class="h-11 w-11 grid place-items-center rounded-xl bg-emerald-50 border-2 border-emerald-200">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><path d="M4 17l5-6 4 3 6-8" stroke="#024938" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="19" cy="6" r="2.6" fill="#f9ac00" stroke="#3b4652" stroke-width="1.6"/></svg>
        </span>
        <div>
          <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Mauzo ya Leo</p>
          <p class="font-black text-emerald-600 text-lg">TZS <span data-count="1240000">0</span></p>
        </div>
      </div>

      <!-- Floating card: SMS deni -->
      <div class="anim-float2 absolute -right-1 sm:right-2 bottom-10 bg-white rounded-2xl shadow-cardlg border-2 border-gray-100 px-5 py-4 max-w-[240px]">
        <div class="flex items-center gap-2">
          <span class="h-9 w-9 grid place-items-center rounded-xl bg-gold-50 border-2 border-gold-200 anim-wiggle">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M4 5h16v11H8l-4 4z" fill="#ffc64d" stroke="#3b4652" stroke-width="2"/></svg>
          </span>
          <p class="font-extrabold text-sm text-emerald-700">SMS Imetumwa ✓</p>
        </div>
        <p class="mt-1.5 text-[12px] font-semibold text-gray-500">"Habari Juma, deni lako TZS 45,000 — lipa kabla ya 25/07."</p>
      </div>

      <!-- Floating chip: Offline -->
      <div class="anim-float absolute left-6 -bottom-3 bg-emerald-500 text-white rounded-full px-4 py-2 text-xs font-extrabold shadow-cardlg flex items-center gap-2" style="animation-delay:-2.5s">
        <span class="h-2 w-2 rounded-full bg-gold-400"></span> Offline Mode ✓
      </div>
    </div>
  </div>

  <!-- Ticker: njia za malipo -->
  <div class="ticker-wrap mt-16 border-y-2 border-emerald-100 bg-white/70 backdrop-blur overflow-hidden">
    <div class="ticker-track flex whitespace-nowrap py-4 font-extrabold text-emerald-600/80 text-sm">
      <?php
        $items = ['📱 M-Pesa','📲 Tigo Pesa','📶 Airtel Money','💳 Halopesa','🏦 Benki','🧾 Risiti za SMS','🛰️ Offline Mode','🏪 Matawi Mengi','📊 Ripoti Live','🔐 Usalama wa Data'];
        for ($r=0; $r<2; $r++) foreach ($items as $it) echo '<span class="mx-7">'.$it.'</span>';
      ?>
    </div>
  </div>
</section>

<!-- ================= STATS ================= -->
<section class="stat-strip text-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
    <div class="reveal"><p class="font-black text-4xl text-gold-400"><span data-count="1000">0</span>+</p><p class="mt-1 text-sm font-bold text-emerald-100">Biashara Zinazotumia</p></div>
    <div class="reveal" data-d="1"><p class="font-black text-4xl text-gold-400"><span data-count="26">0</span></p><p class="mt-1 text-sm font-bold text-emerald-100">Mikoa ya Tanzania</p></div>
    <div class="reveal" data-d="2"><p class="font-black text-4xl text-gold-400"><span data-count="99">0</span>.5%</p><p class="mt-1 text-sm font-bold text-emerald-100">Upatikanaji (Uptime)</p></div>
    <div class="reveal" data-d="3"><p class="font-black text-4xl text-gold-400"><span data-count="15">0</span>s</p><p class="mt-1 text-sm font-bold text-emerald-100">Kukamilisha Muuzo</p></div>
  </div>
</section>

<!-- ================= FEATURES ================= -->
<section id="huduma" class="py-24 bg-white relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto">
      <span class="reveal inline-block bg-gold-50 border-2 border-gold-200 text-gold-700 font-extrabold text-xs uppercase tracking-widest px-4 py-2 rounded-full">Huduma Zetu</span>
      <h2 class="reveal mt-5 font-black text-4xl sm:text-5xl text-emerald-700" data-d="1">Kila Kitu Biashara Yako Inahitaji</h2>
      <p class="reveal mt-4 text-gray-500 font-semibold" data-d="2">Kuanzia kaunta ya duka hadi ripoti za mmiliki — Wazabiashara imekubeba kote.</p>
    </div>

    <div class="mt-16 grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
      <?php
      // ---- Feature cards (icon = flat + bold outline style) ----
      $features = [
        ['title'=>'POS ya Kasi ya Umeme','desc'=>'Uza kwa sekunde — scan barcode kwa kamera ya simu, toa risiti kwa printer, SMS au WhatsApp.','icon'=>'pos','tint'=>'emerald'],
        ['title'=>'Stoo Inayojisimamia','desc'=>'Tahadhari za bidhaa zinazoisha na zinazo-expire. Hesabu stoo na uone tofauti papo hapo.','icon'=>'stock','tint'=>'gold'],
        ['title'=>'Matawi Sehemu Moja','desc'=>'Kila tawi na stoo yake na hesabu zake — wewe unaona yote live, popote ulipo.','icon'=>'branch','tint'=>'emerald'],
        ['title'=>'Kitabu cha Madeni','desc'=>'Madeni hayasahauliki tena — SMS za kukumbusha wateja zinatumwa zenyewe.','icon'=>'debt','tint'=>'gold'],
        ['title'=>'Ripoti za Bosi','desc'=>'Faida, hasara, mauzo kwa tawi na mfanyakazi — kwa siku, wiki, mwezi. PDF & Excel.','icon'=>'report','tint'=>'emerald'],
        ['title'=>'Inafanya Kazi Offline','desc'=>'Intaneti ikikatika mauzo yanaendelea — ikirudi, kila kitu kina-sync chenyewe.','icon'=>'offline','tint'=>'gold'],
      ];

      // Flat-outline SVG icons (style kama sample)
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

      foreach ($features as $i => $f):
        $tint = $f['tint']==='gold' ? 'bg-gold-50 border-gold-200' : 'bg-emerald-50 border-emerald-200';
      ?>
      <article class="reveal fcard bg-white rounded-3xl border-2 border-gray-100 shadow-card p-8" data-d="<?= ($i%3)+1 ?>">
        <span class="ficon inline-grid place-items-center h-20 w-20 rounded-2xl border-2 <?= $tint ?>">
          <?= wz_icon($f['icon']) ?>
        </span>
        <h3 class="mt-6 font-black text-xl text-emerald-700"><?= $f['title'] ?></h3>
        <p class="mt-2.5 text-gray-500 font-semibold text-[15px] leading-relaxed"><?= $f['desc'] ?></p>
        <a href="#" class="mt-5 inline-flex items-center gap-1.5 font-extrabold text-emerald-500 text-sm group">
          Jifunze zaidi
          <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M4 12h16"/></svg>
        </a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section id="jinsi" class="py-24 bg-gradient-to-b from-emerald-50/60 to-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto">
      <span class="reveal inline-block bg-emerald-50 border-2 border-emerald-200 text-emerald-600 font-extrabold text-xs uppercase tracking-widest px-4 py-2 rounded-full">Hatua 3 Tu</span>
      <h2 class="reveal mt-5 font-black text-4xl sm:text-5xl text-emerald-700" data-d="1">Jisajili Leo, Uza Leo</h2>
    </div>

    <div class="mt-16 grid md:grid-cols-3 gap-8 relative">
      <!-- dashed connector -->
      <svg class="hidden md:block absolute top-12 left-[16%] w-[68%]" height="4" aria-hidden="true">
        <line x1="0" y1="2" x2="100%" y2="2" stroke="#80cbc0" stroke-width="4" stroke-dasharray="2 12" stroke-linecap="round"/>
      </svg>
      <?php
      $steps = [
        ['n'=>'1','t'=>'Jisajili kwa Simu','d'=>'Weka jina, namba ya simu na jina la biashara yako. Thibitisha kwa OTP — dakika 2 tu.'],
        ['n'=>'2','t'=>'Ongeza Bidhaa Zako','d'=>'Weka bidhaa, bei na wafanyakazi wako. Una matawi? Yaongeze yote sehemu moja.'],
        ['n'=>'3','t'=>'Anza Kuuza & Kuona Faida','d'=>'POS ya kasi, risiti za SMS, na dashibodi ya mmiliki inayokuonyesha kila senti — live.'],
      ];
      foreach ($steps as $i => $s): ?>
      <div class="reveal text-center relative" data-d="<?= $i+1 ?>">
        <span class="relative inline-grid place-items-center h-24 w-24 rounded-3xl bg-white border-[3px] border-emerald-200 shadow-card font-black text-4xl text-emerald-500">
          <?= $s['n'] ?>
          <span class="absolute -top-2 -right-2 h-7 w-7 rounded-full bg-gold-400 border-[3px] border-white grid place-items-center text-[13px]">✓</span>
        </span>
        <h3 class="mt-6 font-black text-xl text-emerald-700"><?= $s['t'] ?></h3>
        <p class="mt-2.5 text-gray-500 font-semibold max-w-xs mx-auto"><?= $s['d'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="reveal mt-14 text-center" data-d="3">
      <a href="#" class="btn-gold font-black text-lg px-9 py-4 rounded-2xl inline-flex items-center gap-2">
        Fungua Akaunti Sasa
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M4 12h16"/></svg>
      </a>
    </div>
  </div>
</section>

<!-- ================= TESTIMONIALS (REACT) ================= -->
<section id="maoni" class="py-24 bg-emerald-800 relative overflow-hidden">
  <div class="pointer-events-none absolute -top-24 -right-24 w-[420px] h-[420px] bg-emerald-400/10 anim-blob"></div>
  <div class="pointer-events-none absolute -bottom-28 -left-24 w-[380px] h-[380px] bg-gold-400/10 anim-blob" style="animation-delay:-5s"></div>

  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative">
    <div class="text-center">
      <span class="reveal inline-block bg-white/10 border-2 border-emerald-400/40 text-gold-300 font-extrabold text-xs uppercase tracking-widest px-4 py-2 rounded-full">Maoni ya Wateja</span>
      <h2 class="reveal mt-5 font-black text-4xl sm:text-5xl text-white" data-d="1">Wafanyabiashara Wanasemaje?</h2>
    </div>
    <!-- React inamount hapa -->
    <div id="react-testimonials" class="mt-14"></div>
  </div>
</section>

<!-- ================= NEWSLETTER (AJAX + SweetAlert) ================= -->
<section id="newsletter" class="py-24 bg-white">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="reveal relative rounded-[32px] bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900 p-10 sm:p-14 overflow-hidden shadow-cardlg">
      <!-- deco -->
      <div class="absolute -top-14 -right-14 h-52 w-52 rounded-full bg-gold-400/20 blur-xl"></div>
      <div class="absolute -bottom-16 -left-10 h-44 w-44 rounded-full bg-emerald-400/20 blur-xl"></div>
      <div class="awning absolute top-0 inset-x-0" aria-hidden="true">
        <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
      </div>

      <div class="relative text-center">
        <h2 class="font-black text-3xl sm:text-4xl text-white">Usikose Habari za <span class="text-gold-400">Wazabiashara</span></h2>
        <p class="mt-3 text-emerald-100 font-semibold max-w-xl mx-auto">Jiunge na jarida letu — vidokezo vya biashara, huduma mpya na ofa maalum, moja kwa moja kwenye barua pepe yako.</p>

        <form id="newsletterForm" class="mt-8 flex flex-col sm:flex-row gap-3 max-w-xl mx-auto" novalidate>
          <div class="relative flex-1">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M4 6h16a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1z"/></svg>
            </span>
            <input id="newsEmail" type="email" required placeholder="Barua pepe yako…"
                   class="w-full rounded-2xl bg-white/95 pl-12 pr-4 py-4 font-bold text-emerald-800 placeholder:text-gray-400 outline-none border-2 border-transparent focus:border-gold-400 transition">
          </div>
          <button id="newsBtn" type="submit"
                  class="btn-gold font-black px-8 py-4 rounded-2xl inline-flex items-center justify-center gap-2 min-w-[150px]">
            <span class="btn-label">Jiunge Sasa</span>
            <svg class="btn-spin hidden w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
          </button>
        </form>
        <p class="mt-4 text-xs font-bold text-emerald-200">🔒 Hatutakutumia spam. Unaweza kujitoa wakati wowote.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="bg-emerald-900 text-emerald-100 pt-16 pb-8 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-4 gap-10">
    <div class="md:col-span-2">
      <a href="#home" class="flex items-center gap-3">
        <span class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-gold-400 font-black text-2xl grid place-items-center border-[3px] border-emerald-700">W</span>
        <span>
          <span class="block font-black text-xl text-white"><?= $appName ?></span>
          <span class="block text-[11px] font-bold text-gold-400"><?= $slogan ?></span>
        </span>
      </a>
      <p class="mt-5 text-sm font-semibold text-emerald-200/80 max-w-sm">Mfumo wa kisasa wa kusimamia biashara za Tanzania — POS, stoo, matawi, madeni na ripoti. Web na Mobile, hata bila intaneti.</p>
    </div>
    <div>
      <h4 class="font-black text-white uppercase text-sm tracking-wider">Viungo</h4>
      <ul class="mt-4 space-y-2.5 text-sm font-bold">
        <li><a class="hover:text-gold-300 transition" href="#huduma">Huduma</a></li>
        <li><a class="hover:text-gold-300 transition" href="#jinsi">Jinsi Inavyofanya Kazi</a></li>
        <li><a class="hover:text-gold-300 transition" href="#maoni">Maoni ya Wateja</a></li>
        <li><a class="hover:text-gold-300 transition" href="#newsletter">Jiunge na Jarida</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-black text-white uppercase text-sm tracking-wider">Mawasiliano</h4>
      <ul class="mt-4 space-y-2.5 text-sm font-bold">
        <li>📞 +<?= $whatsapp ?></li>
        <li>✉️ info@wazabiashara.co.tz</li>
        <li>📍 Tanzania 🇹🇿</li>
      </ul>
    </div>
  </div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-6 border-t border-emerald-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold text-emerald-300/80">
    <p>© <?= $year ?> <?= $appName ?>. Haki zote zimehifadhiwa.</p>
    <p>Imetengenezwa kwa ❤️ Tanzania</p>
  </div>
</footer>

<!-- ================= WHATSAPP FLOAT ================= -->
<a href="https://wa.me/<?= $whatsapp ?>?text=<?= rawurlencode('Habari! Nataka kujua zaidi kuhusu Wazabiashara.') ?>"
   target="_blank" rel="noopener" aria-label="Wasiliana nasi WhatsApp"
   class="wa-btn fixed bottom-6 right-6 z-50 group">
  <span class="wa-ring absolute inset-0 rounded-full bg-[#25D366]"></span>
  <span class="relative grid place-items-center h-16 w-16 rounded-full bg-[#25D366] shadow-cardlg border-[3px] border-white">
    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#fff"><path d="M17.5 14.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.5 0 1.47 1.07 2.9 1.22 3.1.15.2 2.1 3.2 5.1 4.49.71.3 1.27.49 1.7.63.72.23 1.37.2 1.88.12.58-.09 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35zM12.05 21.8h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.85 9.85 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.9-9.88a9.82 9.82 0 0 1 7 2.9 9.82 9.82 0 0 1 2.9 7c0 5.45-4.45 9.87-9.9 9.87zm8.42-18.3A11.8 11.8 0 0 0 12.04 0C5.46 0 .1 5.35.1 11.93c0 2.1.55 4.16 1.6 5.97L0 24l6.24-1.64a11.93 11.93 0 0 0 5.8 1.48h.01c6.58 0 11.93-5.35 11.93-11.93 0-3.19-1.24-6.19-3.5-8.42z"/></svg>
  </span>
  <span class="absolute right-[76px] top-1/2 -translate-y-1/2 whitespace-nowrap bg-white text-emerald-700 font-extrabold text-sm px-4 py-2 rounded-xl shadow-card border-2 border-gray-100 opacity-0 translate-x-2 pointer-events-none transition-all group-hover:opacity-100 group-hover:translate-x-0">
    Tuandikie WhatsApp 💬
  </span>
</a>

<!-- ================= SCRIPTS ================= -->
<script>
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

/* ---------- Scroll reveal ---------- */
const io = new IntersectionObserver(es => es.forEach(e => {
  if (e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); }
}), {threshold:.14});
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

/* ---------- Animated counters ---------- */
const fmt = n => n.toLocaleString('sw-TZ');
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
  const email = document.getElementById('newsEmail').value.trim();
  const btn = document.getElementById('newsBtn');
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)){
    return swalBrand.fire({icon:'warning', title:'Barua pepe si sahihi',
      text:'Tafadhali andika barua pepe sahihi, mfano: jina@mfano.co.tz'});
  }
  btn.disabled = true;
  btn.querySelector('.btn-label').textContent = 'Inatuma…';
  btn.querySelector('.btn-spin').classList.remove('hidden');
  try{
    const res = await fetch('api/subscribe.php', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
      body: JSON.stringify({email})
    });
    const data = await res.json();
    if (data.success){
      document.getElementById('newsletterForm').reset();
      swalBrand.fire({icon:'success', title:'Karibu Wazabiashara! 🎉',
        text:data.message || 'Umejiunga na jarida letu kikamilifu.',
        timer:3500, timerProgressBar:true});
    } else {
      swalBrand.fire({icon:'error', title:'Samahani!', text:data.message || 'Imeshindikana kujiunga. Jaribu tena.'});
    }
  } catch(err){
    swalBrand.fire({icon:'error', title:'Tatizo la Mtandao',
      text:'Imeshindikana kuwasiliana na seva. Tafadhali jaribu tena baadaye.'});
  } finally{
    btn.disabled = false;
    btn.querySelector('.btn-label').textContent = 'Jiunge Sasa';
    btn.querySelector('.btn-spin').classList.add('hidden');
  }
});
</script>

<!-- ---------- React: Testimonials carousel ---------- -->
<script type="text/babel">
const { useState, useEffect, useCallback } = React;

const DATA = [
  { name:'Mangi Josephat', biz:'Mangi Traders — Kariakoo, Dar', stars:5,
    text:'Nina matawi matatu — zamani nilikuwa napiga simu kila jioni kuuliza mauzo. Sasa nafungua app tu, naona kila tawi live. Wazabiashara imenirudishia usingizi!' },
  { name:'Neema Michael', biz:'Neema Pharmacy — Mwanza', stars:5,
    text:'Tahadhari za dawa zinazo-expire zimeniokolea mtaji mkubwa. Na wateja wanapenda risiti za SMS — duka linaonekana la kisasa kweli.' },
  { name:'Baraka John', biz:'Baraka Hardware — Arusha', stars:5,
    text:'Madeni yalikuwa yananimaliza. Sasa SMS za kukumbusha zinatumwa zenyewe na wateja wanalipa kwa wakati. Mauzo ya deni yamekuwa salama.' },
  { name:'Zainabu Ally', biz:'Zainabu Shop — Dodoma', stars:5,
    text:'Hata intaneti ikikatika duka linaendelea kuuza — ikirudi kila kitu kinajipanga chenyewe. Hii ndiyo teknolojia ya Tanzania halisi!' },
];

function Stars({n}){
  return <div className="flex justify-center gap-1 text-gold-400 text-xl" aria-label={`Nyota ${n} kati ya 5`}>
    {Array.from({length:n}).map((_,i)=><span key={i}>★</span>)}
  </div>;
}

function Testimonials(){
  const [i, setI] = useState(0);
  const [fade, setFade] = useState(true);
  const go = useCallback(next => {
    setFade(false);
    setTimeout(() => { setI(next); setFade(true); }, 220);
  }, []);
  useEffect(() => {
    const t = setInterval(() => go((i+1) % DATA.length), 6000);
    return () => clearInterval(t);
  }, [i, go]);
  const c = DATA[i];

  return (
    <div className="relative">
      <div className={"bg-white rounded-[28px] shadow-cardlg border-2 border-emerald-200/40 px-8 py-12 sm:px-14 text-center transition-all duration-300 " + (fade ? "opacity-100 translate-y-0" : "opacity-0 translate-y-3")}>
        <span className="inline-grid place-items-center h-16 w-16 rounded-full bg-emerald-50 border-2 border-emerald-200 text-3xl font-black text-emerald-500 mb-5">
          {c.name.charAt(0)}
        </span>
        <Stars n={c.stars}/>
        <p className="mt-5 text-lg sm:text-xl font-bold text-emerald-800 leading-relaxed max-w-2xl mx-auto">“{c.text}”</p>
        <p className="mt-6 font-black text-emerald-600">{c.name}</p>
        <p className="text-sm font-bold text-gray-400">{c.biz}</p>
      </div>

      {/* Controls */}
      <button onClick={() => go((i-1+DATA.length)%DATA.length)} aria-label="Iliyopita"
        className="absolute left-0 sm:-left-6 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-gold-400 border-[3px] border-white shadow-gold grid place-items-center text-emerald-900 font-black hover:scale-110 transition-transform">‹</button>
      <button onClick={() => go((i+1)%DATA.length)} aria-label="Inayofuata"
        className="absolute right-0 sm:-right-6 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-gold-400 border-[3px] border-white shadow-gold grid place-items-center text-emerald-900 font-black hover:scale-110 transition-transform">›</button>

      {/* Dots */}
      <div className="mt-8 flex justify-center gap-2.5">
        {DATA.map((_,d)=>(
          <button key={d} onClick={()=>go(d)} aria-label={"Maoni "+(d+1)}
            className={"h-3 rounded-full transition-all duration-300 " + (d===i ? "w-9 bg-gold-400" : "w-3 bg-emerald-300/50 hover:bg-emerald-300")}/>
        ))}
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('react-testimonials')).render(<Testimonials/>);
</script>

</body>
</html>
