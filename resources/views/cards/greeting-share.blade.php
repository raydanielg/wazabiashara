<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $card->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Nunito', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-emerald-600 to-emerald-900 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        @if($card->image)
        <div class="relative h-64 overflow-hidden">
            <img src="{{ asset('storage/' . $card->image) }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        </div>
        @else
        <div class="h-48 bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
            <svg class="w-20 h-20 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v1a1 1 0 001 1h3a1 1 0 001-1V3m0 0V2a1 1 0 00-1-1H5a1 1 0 00-1 1v1m4 0h.01M7 3h.01M20 3v1a1 1 0 01-1 1h-3a1 1 0 01-1-1V3m0 0V2a1 1 0 011-1h5a1 1 0 011 1v1m-4 0h.01M17 3h.01M7 8h10M7 12h10M7 16h6"/></svg>
        </div>
        @endif
        <div class="p-6 text-center">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $card->type === 'birthday' ? 'bg-amber-50 text-amber-700' : ($card->type === 'holiday' ? 'bg-violet-50 text-violet-700' : 'bg-sky-50 text-sky-700') }}">{{ ucfirst($card->type) }}</span>
            <h1 class="text-2xl font-bold text-gray-900 mt-4">{{ $card->title }}</h1>
            @if($card->customer)<p class="text-sm text-gray-500 mt-1">Kwa: {{ $card->customer->name }}</p>@endif
            <p class="text-gray-600 mt-4 leading-relaxed">{{ $card->message }}</p>
            <div class="mt-6 pt-6 border-t">
                <p class="text-xs text-gray-400">Kutoka:</p>
                <p class="text-sm font-bold text-emerald-600">{{ $card->business->name }}</p>
                <p class="text-xs text-gray-400">{{ $card->business->phone }}</p>
            </div>
        </div>
    </div>
</body>
</html>
