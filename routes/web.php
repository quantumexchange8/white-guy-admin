<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleOrderController;

Route::get('locale/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put("locale", $locale);

    return redirect()->back();
});

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('/data/members', [MemberController::class, 'getMembers']);

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
            Route::get('/getMembers', [MemberController::class, 'getMembers'])->name('crm.member.getMembers');
    
            Route::get('/detail/{id}', [MemberController::class, 'detail'])->name('crm.member.detail');
            Route::get('/getMemberData', [MemberController::class, 'getMemberData'])->name('crm.member.getMemberData');
            Route::get('/getMemberOrders', [MemberController::class, 'getMemberOrders'])->name('crm.member.getMemberOrders');
            Route::get('/getMemberLogEntries', [MemberController::class, 'getMemberLogEntries'])->name('crm.member.getMemberLogEntries');

        });

        Route::prefix('lead')->group(callback: function () {
            Route::get('/', [LeadController::class, 'index'])->name('crm.lead.index');
            Route::get('/getLeads', [LeadController::class, 'getLeads'])->name('crm.lead.getLeads');
    
            Route::get('/detail/{id}', [LeadController::class, 'detail'])->name('crm.lead.detail');
            Route::get('/getLeadData', [LeadController::class, 'getLeadData'])->name('crm.lead.getLeadData');
            Route::get('/getLeadNotes', [LeadController::class, 'getLeadNotes'])->name('crm.lead.getLeadNotes');

        });
    
        Route::prefix('order')->group(callback: function () {
            Route::get('/', [OrderController::class, 'index'])->name('crm.order.index');
            Route::get('/getOrders', [OrderController::class, 'getOrders'])->name('crm.order.getOrders');
    
            Route::get('/detail/{id}', [OrderController::class, 'detail'])->name('crm.order.detail');
            Route::get('/getOrderData', [OrderController::class, 'getOrderData'])->name('crm.order.getOrderData');
            Route::get('/getOrderNotes', [OrderController::class, 'getOrderNotes'])->name('crm.order.getOrderNotes');
            Route::get('/getOrderLogEntries', [OrderController::class, 'getOrderLogEntries'])->name('crm.order.getOrderLogEntries');

        });

        Route::prefix('saleOrder')->group(callback: function () {
            Route::get('/', [SaleOrderController::class, 'index'])->name('crm.saleOrder.index');
            Route::get('/getSaleOrders', [SaleOrderController::class, 'getSaleOrders'])->name('crm.saleOrder.getSaleOrders');
    
            Route::get('/detail/{id}', [SaleOrderController::class, 'detail'])->name('crm.saleOrder.detail');
            Route::get('/getSaleOrderData', [SaleOrderController::class, 'getSaleOrderData'])->name('crm.saleOrder.getSaleOrderData');
            Route::get('/getSaleOrderItems', [SaleOrderController::class, 'getSaleOrderItems'])->name('crm.saleOrder.getSaleOrderItems');
            Route::get('/getSaleOrderLogEntries', [SaleOrderController::class, 'getSaleOrderLogEntries'])->name('crm.saleOrder.getSaleOrderLogEntries');

        });

    });

require __DIR__.'/auth.php';
