@extends('layouts.auth')

@section('content')
<div class="w-full lg:max-w-md lg:mx-0 lg:ml-auto">
    <div class="w-full">
        <div class="bg-white rounded-none sm:rounded-2xl shadow-none sm:shadow-xl border-0 sm:border border-gray-100 overflow-hidden min-h-screen sm:min-h-0">
            <div class="px-6 sm:px-8 py-8 text-center border-b border-gray-100">
                <img src="{{ asset('images/logo.png') }}" alt="Wazabiashara" class="w-16 h-16 mx-auto mb-3">
                <h2 class="text-2xl font-extrabold text-gray-900">Badilisha Nenosiri</h2>
                <p class="text-sm text-gray-400 mt-1">Weka nenosiri jipya la akaunti yako</p>
            </div>

            <div class="p-6 sm:p-8">
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.reset.submit') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nenosiri Jipya</label>
                        <input type="password" name="password" required minlength="8"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition"
                            placeholder="Angalau herufi 8">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Thibitisha Nenosiri</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition"
                            placeholder="Rudia nenosiri jipya">
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 rounded-xl bg-gradient-to-r from-teal-600 to-teal-700 text-white font-bold text-sm shadow-lg shadow-teal-600/30 hover:shadow-teal-600/50 transition">
                        Badilisha Nenosiri
                    </button>
                </form>

                <p class="mt-6 text-center text-xs text-gray-400 hidden sm:block">
                    &copy; {{ date('Y') }} Wazabiashara. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
