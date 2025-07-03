<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\QuotesController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuoteLineController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes pour les profils utilisateur (accessible aux utilisateurs connectés)
    Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('user.update');
});

// Routes admin (gestion complète des utilisateurs)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

Route::resource('customers', CustomersController::class);
Route::resource('projects', ProjectController::class);
Route::resource('expenses', ExpensesController::class);

// Routes quotes sans contexte project
// On conserve la resource (qui inclut index, show, edit, update, destroy, etc.)
Route::resource('quotes', QuotesController::class);

// Routes pour gérer les lignes des devis
Route::prefix('quotes/{quote}')->group(function () {
    Route::get('add-lines', [QuotesController::class, 'showAddLines'])->name('quotes.addLines.show');
    Route::post('add-lines', [QuotesController::class, 'addLine'])->name('quotes.addLine');
    Route::delete('lines/{line}', [QuotesController::class, 'destroyLine'])->name('quotes.lines.destroy');
});

Route::resource('quotes.lines', QuoteLineController::class)->except(['index', 'show']);

Route::post('/quotes/{quote}/validate', [QuotesController::class, 'validateQuote'])->name('quotes.validate');
Route::post('/quotes/{quote}/send', [QuotesController::class, 'sendQuote'])->name('quotes.send');

// Routes quotes dans projet avec noms différents pour éviter collision
Route::prefix('projects/{project}')->group(function () {
    Route::get('quotes/create', [QuotesController::class, 'create'])->name('projects.quotes.create');
    Route::post('quotes', [QuotesController::class, 'store'])->name('projects.quotes.store');
    Route::get('quotes/{quote}/edit', [QuotesController::class, 'edit'])->name('projects.quotes.edit');
    Route::put('quotes/{quote}', [QuotesController::class, 'update'])->name('projects.quotes.update');
});

Route::get('/expenses/{id}/download', [ExpensesController::class, 'downloadInvoice'])->name('expenses.download');

require __DIR__.'/auth.php';