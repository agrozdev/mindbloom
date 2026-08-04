<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{category}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{category}/{post}', [BlogController::class, 'show'])->name('blog.show')->scopeBindings();

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store')->middleware('throttle:5,1');

Route::get('/politika-za-poveritelnost', [PageController::class, 'privacyPolicy'])->name('legal.privacy');
Route::get('/obshti-usloviya-za-polzvane', [PageController::class, 'termsOfUse'])->name('legal.terms');
Route::get('/politika-za-biskvitkite', [PageController::class, 'cookiePolicy'])->name('legal.cookies');
