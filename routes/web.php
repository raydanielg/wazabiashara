<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsletterController;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
