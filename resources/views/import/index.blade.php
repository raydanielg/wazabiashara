@extends('layouts.dashboard')

@section('title', 'Import Data')
@section('page_title', 'Import Data')

@section('content')
<div class="max-w-4xl space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            Import Data
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Bulk import Parties (customers) or Items (products) from a CSV file</p>
    </div>

    @if(session('import_result'))
        @php $result = session('import_result'); @endphp
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3">
                Import Summary — {{ ucfirst($result['type']) }}
            </h3>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-lg font-extrabold text-gray-800">{{ $result['total'] }}</p>
                    <p class="text-[10px] font-semibold text-gray-500 uppercase">Rows Read</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-3 text-center">
                    <p class="text-lg font-extrabold text-emerald-600">{{ $result['imported'] }}</p>
                    <p class="text-[10px] font-semibold text-gray-500 uppercase">Imported</p>
                </div>
                <div class="bg-red-50 rounded-xl p-3 text-center">
                    <p class="text-lg font-extrabold text-red-500">{{ count($result['skipped']) }}</p>
                    <p class="text-[10px] font-semibold text-gray-500 uppercase">Skipped</p>
                </div>
            </div>
            @if(count($result['skipped']))
            <div class="bg-red-50/50 rounded-xl p-3 max-h-40 overflow-y-auto">
                <ul class="text-xs text-red-600 space-y-1">
                    @foreach($result['skipped'] as $reason)
                    <li>• {{ $reason }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {{-- Import Parties --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Import Parties</h3>
                <p class="text-xs text-gray-500 mt-1">CSV columns: <code class="bg-gray-100 px-1 py-0.5 rounded">name</code>, <code class="bg-gray-100 px-1 py-0.5 rounded">phone</code>, <code class="bg-gray-100 px-1 py-0.5 rounded">email</code></p>
            </div>
            <form action="{{ route('import.parties') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".csv,text/csv" required class="w-full text-sm">
                <button type="submit" class="w-full btn-gold font-bold py-2.5 rounded-xl text-sm">Upload & Import</button>
            </form>
        </div>

        {{-- Import Items --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Import Items</h3>
                <p class="text-xs text-gray-500 mt-1">CSV columns: <code class="bg-gray-100 px-1 py-0.5 rounded">name</code>, <code class="bg-gray-100 px-1 py-0.5 rounded">price</code>, <code class="bg-gray-100 px-1 py-0.5 rounded">category</code></p>
            </div>
            <form action="{{ route('import.items') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".csv,text/csv" required class="w-full text-sm">
                <button type="submit" class="w-full btn-gold font-bold py-2.5 rounded-xl text-sm">Upload & Import</button>
            </form>
        </div>
    </div>

    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-xs text-emerald-700">
        <p class="font-semibold mb-1">Tips</p>
        <ul class="list-disc list-inside space-y-0.5">
            <li>The first row of your CSV must contain column headers (name, phone, email, price, category).</li>
            <li>Rows missing a name will be skipped and listed in the summary.</li>
            <li>New item categories mentioned in the CSV are created automatically.</li>
        </ul>
    </div>
</div>
@endsection
