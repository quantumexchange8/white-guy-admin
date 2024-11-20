<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect(route('login'));
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
        Route::prefix('member')->group(callback: function () {
            Route::get('/', [MemberController::class, 'index'])->name('crm.member.index');
    
            Route::get('/detail/{id}', [LeadController::class, 'detail'])->name('crm.member.detail');
            Route::get('/getLeadData', [LeadController::class, 'getLeadData'])->name('crm.member.getLeadData');
            Route::get('/getLeadNotes', [LeadController::class, 'getLeadNotes'])->name('crm.member.getLeadNotes');

        });

        Route::prefix('lead')->group(callback: function () {
            Route::get('/', [LeadController::class, 'index'])->name('crm.lead.index');
    
            Route::get('/detail/{id}', [LeadController::class, 'detail'])->name('crm.lead.detail');
            Route::get('/getLeadData', [LeadController::class, 'getLeadData'])->name('crm.lead.getLeadData');
            Route::get('/getLeadNotes', [LeadController::class, 'getLeadNotes'])->name('crm.lead.getLeadNotes');

        });
    
    });

require __DIR__.'/auth.php';
