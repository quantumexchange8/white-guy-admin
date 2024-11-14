<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('/data/leads', [LeadController::class, 'getLeads']);
Route::get('/data/leads/latest', [LeadController::class, 'getLatestLeads']);
Route::get('/data/leads/duplicates', [LeadController::class, 'getDuplicatedLeads']);
Route::get('/data/leads/categories', [LeadController::class, 'getCategories']);

    /**
     * ==============================
     *           CRM
     * ==============================
     */
    Route::prefix('crm')->group(callback: function () {
        Route::prefix('lead')->group(callback: function () {
            Route::get('/', [LeadController::class, 'index'])->name('crm.lead.index');
    
            Route::get('/detail/{id}', [LeadController::class, 'detail'])->name('crm.lead.detail');
            Route::get('/getLeadData', [LeadController::class, 'getLeadData'])->name('crm.lead.getLeadData');
            Route::get('/getLeadNotes', [LeadController::class, 'getLeadNotes'])->name('crm.lead.getLeadNotes');

        });
    
    });

require __DIR__.'/auth.php';
