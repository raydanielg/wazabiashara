@php
$appName = config('app.name', 'Wazabiashara');
$year    = date('Y');
@endphp
<!DOCTYPE html>
<html lang="sw" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Sera ya Faragha — {{ $appName }}</title>
<meta name="description" content="Sera ya faragha ya mfumo wa Wazabiashara.">
<link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
<link rel="dns-prefetch" href="//fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: { sans: ['Nunito','sans-serif'] },
      colors: {
        emerald: {50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816'},
        gold:    {50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600'}
      }
    }
  }
}
</script>
<style>
  body { font-family: 'Nunito', sans-serif; }
  ::selection { background: #ffd680; color: #01241f; }
</style>
</head>
<body class="bg-emerald-50/30 text-slate-800 antialiased min-h-screen">

<!-- Header -->
<header class="bg-emerald-900 text-white sticky top-0 z-50 shadow-lg">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
    <a href="{{ url('/') }}" class="flex items-center gap-3">
      <img src="{{ asset('logo.png') }}" alt="{{ $appName }}" class="h-9 w-9 rounded-lg object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='grid';">
      <span style="display:none" class="h-9 w-9 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 text-gold-400 font-black text-xl grid place-items-center border-2 border-emerald-700">W</span>
      <span class="font-black text-lg">{{ $appName }}</span>
    </a>
    <a href="{{ url('/') }}" class="text-sm font-bold text-emerald-300 hover:text-gold-300 transition">&larr; Rudi Nyumbani</a>
  </div>
</header>

<!-- Content -->
<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">

  <div class="bg-white rounded-2xl shadow-card border border-emerald-100 overflow-hidden">
    <!-- Title bar -->
    <div class="bg-gradient-to-br from-emerald-900 to-emerald-700 px-6 sm:px-10 py-8 text-center">
      <h1 class="text-2xl sm:text-3xl font-black text-white">Sera ya Faragha</h1>
      <p class="text-emerald-200 text-sm font-semibold mt-2">Ilisasishwa tarehe {{ $year }}</p>
    </div>

    <div class="px-6 sm:px-10 py-8 sm:py-10 space-y-8 text-sm sm:text-[15px] leading-relaxed text-slate-700">

      <div>
        <p class="text-slate-500 italic">{{ $appName }} inaheshimu faragha yako. Sera hii inaelezea jinsi tunavyokusanya, kutumia, na kulinda taarifa zako. Tafadhali soma kwa makini.</p>
      </div>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">1. Taarifa Tunazokusanya</h2>
        <p class="mb-3">Tunakusanya aina zifuatazo za taarifa:</p>
        <ul class="space-y-2 list-disc pl-5">
          <li><strong>Taarifa za kibinafsi:</strong> Jina, nambari ya simu, barua pepe, wakati wa usajili.</li>
          <li><strong>Taarifa za biashara:</strong> Jina la biashara, aina ya biashara, eneo, matawi, na taarifa zingine za biashara unazozingiza kwenye mfumo.</li>
          <li><strong>Taarifa za muamala:</strong> Mauzo, gharama, madeni, malipo, na taarifa zingine za kifedha unazozingiza.</li>
          <li><strong>Taarifa za kiufundi:</strong> Aina ya kifaa, kivinjari, anwani ya IP, na data ya matumizi ya mfumo.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">2. Jinsi Tunavyotumia Taarifa Zako</h2>
        <ul class="space-y-2 list-disc pl-5">
          <li>Kutoa huduma za usimamizi wa biashara kama ulivyotaka.</li>
          <li>Kutuma arifa kuhusu akaunti yako, mauzo, au vikumbusho.</li>
          <li>Kuboresha mfumo na huduma zetu.</li>
          <li>Kulinda mfumo dhidi ya udanganyifu na matumizi mabaya.</li>
          <li>Kutimiza sheria na kanuni za Tanzania.</li>
        </ul>
        <p class="mt-3">Hatupati taarifa zako kwa kampuni tatu kwa madhumuni ya matangazo.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">3. Uhifadhi wa Data</h2>
        <p class="mb-3">Taarifa zako zinahifadhiwa kwenye seva salama. Tunahifadhi taarifa zako kwa muda unaohitajika kutoa huduma. Baada ya kufuta akaunti yako, data yako inaweza kufutwa kabisa ndani ya siku 90.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">4. Usalama wa Data</h2>
        <p class="mb-3">Tunatumia hatua za kisasa za usalama ikiwa ni pamoja na:</p>
        <ul class="space-y-2 list-disc pl-5">
          <li>Usimbaji wa data (encryption) wakati wa usafirishaji na uhifadhi.</li>
          <li>Nenosiri zilizosimbwa kwa kutumia bcrypt.</li>
          <li>Ufikiaji uliopunguziwa wa seva na mfumo.</li>
          <li>Ufuatiliaji wa shughuli za kutiliwa shaka.</li>
        </ul>
        <p class="mt-3">Hata hivyo, hakuna mfumo unaoweza kuhakikisha usalama wa 100%. Tafadhali chukua hatua za usalama upande wako pia.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">5. Haki Za Mtumiaji</h2>
        <p class="mb-3">Una haki zifuatazo kuhusu taarifa zako:</p>
        <ul class="space-y-2 list-disc pl-5">
          <li><strong>Haki ya kuona:</strong> Unaweza kuomba taarifa zako zote tunazozihifadhi.</li>
          <li><strong>Haki ya kusahihisha:</strong> Unaweza kusahihisha taarifa zako zisizo sahihi.</li>
          <li><strong>Haki ya kufuta:</strong> Unaweza omba kufuta akaunti yako na taarifa zote.</li>
          <li><strong>Haki ya kuomba nakala:</strong> Unaweza kuomba nakala ya data yako kwa format inayosomwa kwa mashine.</li>
        </ul>
        <p class="mt-3">Kutumia haki hizi, wasiliana nasi kupitia <a href="mailto:info@wazabiashara.co.tz" class="text-emerald-600 font-bold underline hover:text-gold-600">info@wazabiashara.co.tz</a></p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">6. Cookies</h2>
        <p class="mb-3">Tunatumia cookies kwa madhumuni ya kutoa huduma bora zaidi, ikiwa ni pamoja na kukumbuka akaunti yako na kuboresha utendaji. Unaweza zima cookies kupitia mipangilio ya kivinjari chako.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">7. Viungo vya Tovuti za Tatu</h2>
        <p class="mb-3">Mfumo wetu unaweza kuwa na viungo vya tovuti za tatu. Hatuwajibikii kwa sera za faragha za tovuti hizo. Tafadhali soma sera zao kabla ya kutumia.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">8. Faragha ya Watoto</h2>
        <p class="mb-3">Mfumo wetu haukusudiwi kwa watoto chini ya miaka 18. Hatukusanyi taarifa za watoto kwa makusudi.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">9. Mabadiliko ya Sera Hii</h2>
        <p class="mb-3">Tunaweza kusasisha sera hii wakati wowote. Sera mpya itatumika mara moja baada ya kuchapishwa kwenye ukurasa huu. Ni jukumu lako kusoma ukurasa huu mara kwa mara.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">10. Mawasiliano</h2>
        <p>Kama una maswali kuhusu sera hii ya faragha, wasiliana nasi kupitia: <a href="mailto:info@wazabiashara.co.tz" class="text-emerald-600 font-bold underline hover:text-gold-600">info@wazabiashara.co.tz</a></p>
      </section>

    </div>
  </div>

</main>

<!-- Footer -->
<footer class="bg-emerald-900 text-emerald-100 py-8">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-bold text-emerald-300/80 text-center sm:text-left">
    <p>&copy; {{ $year }} {{ $appName }}. Haki zote zimehifadhiwa.</p>
    <div class="flex items-center gap-4">
      <a href="{{ route('terms') }}" class="hover:text-gold-300 transition">Sheria na Masharti</a>
      <a href="{{ route('privacy.policy') }}" class="hover:text-gold-300 transition">Sera ya Faragha</a>
    </div>
  </div>
</footer>

</body>
</html>
