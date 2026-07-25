@php
$appName = config('app.name', 'Wazabiashara');
$year    = date('Y');
@endphp
<!DOCTYPE html>
<html lang="sw" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Sheria na Masharti — {{ $appName }}</title>
<meta name="description" content="Sheria na masharti ya matumizi ya mfumo wa Wazabiashara.">
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
      <h1 class="text-2xl sm:text-3xl font-black text-white">Sheria na Masharti</h1>
      <p class="text-emerald-200 text-sm font-semibold mt-2">Ilisasishwa tarehe {{ $year }}</p>
    </div>

    <div class="px-6 sm:px-10 py-8 sm:py-10 space-y-8 text-sm sm:text-[15px] leading-relaxed text-slate-700">

      <div>
        <p class="text-slate-500 italic">Kwa kutumia mfumo wa {{ $appName }}, unakubali kufuata sheria na masharti yafuatayo. Tafadhali soma kwa makini kabla ya kutumia huduma hizi.</p>
      </div>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">1. Ukaribisho wa Masharti</h2>
        <p class="mb-3">Kwa kuunda akaunti na kutumia mfumo wa {{ $appName }}, unakubaliana na kufungwa na sheria na masharti yote yaliyoandikwa hapa. Kama hukubaliani na masharti yoyote, tafadhali usitumie mfumo huu.</p>
        <p>Tunaweza kusasisha masharti haya wakati wowote. Masharti mapya yatatumika mara moja baada ya kuchapishwa kwenye ukurasa huu. Ni jukumu lako kusoma ukurasa huu mara kwa mara.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">2. Akaunti ya Mtumiaji</h2>
        <ul class="space-y-2 list-disc pl-5">
          <li>Unahitaji kuwa na umri wa angalau miaka 18 kufungua akaunti.</li>
          <li>Unawajibikia kuhifadhi usiri wa nenosiri lako na kwa matumizi yote yatokayo akaunti yako.</li>
          <li>Taarifa ulizotoa wakati wa usajili zinapaswa kuwa za kweli na sahihi.</li>
          <li>Hauruhusiwi kushiraka akaunti yako na mtu mwingine.</li>
          <li>Tunaweza kusimamisha au kufuta akaunti yako kama utakiuka masharti haya.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">3. Matumizi ya Huduma</h2>
        <p class="mb-3">{{ $appName }} inatoa huduma za usimamizi wa biashara ikiwa ni pamoja na POS, usimamizi wa stoo, matawi, madeni, ripoti, na zaidi.</p>
        <ul class="space-y-2 list-disc pl-5">
          <li>Hauruhusiwi kutumia mfumo kwa madhumuni yasiyo halali au yanayokiuka sheria za Tanzania.</li>
          <li>Hauruhusiwi kujaribu kuvuruga, hack, au kufanya madhara kwenye seva au mfumo wetu.</li>
          <li>Hauruhusiwi kutumia mfumo kusambaza maudhui ya kutisha, yanayodharau, au yanayokiuka haki za wengine.</li>
          <li>Matumizi ya mfumo kwa biashara haramu hayaruhusiwi kabisa.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">4. Data na Usiri</h2>
        <p class="mb-3">Data yako ya biashara inahifadhiwa kwa usalama. Tunaheshimu usiri wa taarifa zako za kibiashara. Tafadhali soma <a href="{{ route('privacy.policy') }}" class="text-emerald-600 font-bold underline hover:text-gold-600">Sera ya Faragha</a> kwa maelezo zaidi.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">5. Malipo na Usajili</h2>
        <p class="mb-3">Baadhi ya huduma zinaweza hitaji malipo. Malipo yote hayarudishwi isipokuwa kama ilivyoelezwa wazi wakati wa kununua huduma. Tunaweza kubadilisha bei wakati wowote kwa taarifa ya awali.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">6. Kukomesha Huduma</h2>
        <p class="mb-3">Tunaweza kusitisha au kukomesha huduma zetu wakati wowote kwa sababu yoyote, ikiwa ni pamoja na kukiuka masharti haya. Unaweza kufuta akaunti yako wakati wowote kwa kuwasiliana nasi.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">7. Kikomo cha Dhima</h2>
        <p class="mb-3">{{ $appName }} inatolewa "kama ilivyo" bila dhamana yoyote. Hatutawajibika kwa hasara yoyote inayotokana na matumizi ya mfumo, ikiwa ni pamoja na hasara ya data, biashara, au faida.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">8. Hakimiliki</h2>
        <p class="mb-3">Mfumo wa {{ $appName }} na alama zote ni mali ya Wazabiashara. Hakuna sehemu ya mfumo huu inayoruhusiwa kunakiliwa bila ruhusa.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">9. Sheria Tawala</h2>
        <p>Masharti haya yanatawaliwa na sheria za Jamhuri ya Muungano wa Tanzania. Migogoro yote itatatuliwa kwenye mahakama za Tanzania.</p>
      </section>

      <section>
        <h2 class="text-lg font-black text-emerald-900 mb-3">10. Mawasiliano</h2>
        <p>Kama una maswali kuhusu masharti haya, wasiliana nasi kupitia: <a href="mailto:info@wazabiashara.co.tz" class="text-emerald-600 font-bold underline hover:text-gold-600">info@wazabiashara.co.tz</a></p>
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
