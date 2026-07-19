@extends('layouts.auth')

@section('title', 'Verify Email - Wazabiashara')

@section('content')
<div class="w-full">
    <div class="bg-white rounded-none sm:rounded-2xl shadow-none sm:shadow-xl border-0 sm:border border-gray-100 overflow-hidden min-h-screen sm:min-h-0">
        {{-- Header --}}
        <div class="px-6 sm:px-8 py-8 text-center border-b border-gray-100">
            <img src="{{ asset('logo.png') }}" alt="Wazabiashara" class="w-16 h-16 mx-auto object-contain mb-3">
            <h2 class="text-2xl font-extrabold text-gray-800">Verify Email</h2>
            <p class="text-gray-400 text-sm mt-1">One more step to get started</p>
            <p class="text-emerald-600 text-xs font-semibold mt-2 tracking-wide">Biashara yako, Mkononi mwako</p>
        </div>

        {{-- Content --}}
        <div class="p-6 sm:p-8 text-center">
            @if (session('resent'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    A fresh verification link has been sent to your email address.
                </div>
            @endif

            <div class="w-20 h-20 mx-auto bg-gold-50 rounded-full flex items-center justify-center mb-5">
                <svg class="w-10 h-10 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </div>

            <p class="text-gray-600 mb-2">Before proceeding, please check your email for a verification link.</p>
            <p class="text-gray-500 text-sm mb-6">If you did not receive the email, click the button below to request another.</p>

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="w-full py-3 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Resend Verification Link
                </button>
            </form>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400 hidden sm:block">&copy; {{ date('Y') }} Wazabiashara. All rights reserved.</p>
</div>
@endsection
