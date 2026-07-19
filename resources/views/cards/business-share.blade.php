<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $card->card_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Nunito', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-emerald-600 to-emerald-900 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-gold-500/10 rounded-full -ml-12 -mb-12"></div>
            <div class="relative z-10 flex items-center gap-4">
                @if($card->logo)<img src="{{ asset('storage/' . $card->logo) }}" class="w-16 h-16 rounded-xl object-cover">@endif
                <div>
                    <p class="text-lg font-bold">{{ $card->owner_name }}</p>
                    <p class="text-xs text-emerald-200">{{ $card->card_name }}</p>
                </div>
            </div>
        </div>
        <div class="p-6 space-y-3">
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div><div><p class="text-[10px] text-gray-400">Simu</p><p class="text-sm font-semibold text-gray-900">{{ $card->phone }}</p></div></div>
            @if($card->email)<div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center"><svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div><div><p class="text-[10px] text-gray-400">Email</p><p class="text-sm font-semibold text-gray-900">{{ $card->email }}</p></div></div>@endif
            @if($card->website)<div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center"><svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg></div><div><p class="text-[10px] text-gray-400">Website</p><p class="text-sm font-semibold text-gray-900">{{ $card->website }}</p></div></div>@endif
            @if($card->address)<div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center"><svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><div><p class="text-[10px] text-gray-400">Anwani</p><p class="text-sm font-semibold text-gray-900">{{ $card->address }}</p></div></div>@endif
            @if(!empty($card->social_media))
            <div class="flex gap-2 pt-3 border-t">
                @foreach($card->social_media as $platform => $link)
                <a href="{{ $link }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-gray-50 text-xs font-semibold text-gray-600 hover:bg-gray-100">{{ ucfirst($platform) }}</a>
                @endforeach
            </div>
            @endif
        </div>
        <div class="px-6 pb-6 text-center">
            <p class="text-xs text-gray-400">{{ $card->business->name }}</p>
        </div>
    </div>
</body>
</html>
