<?php

use Inertia\Inertia;
use App\Models\Announcement;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\InternalNewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PrivateMessageController;
use App\Http\Controllers\PaymentSubmissionController;
use App\Http\Controllers\AccountManagerProfileController;
use App\Http\Controllers\ApplicationController;

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
        Route::prefix('announcement')->group(callback: function () {
            Route::get('/', [AnnouncementController::class, 'index'])->name('crm.announcement.index');
            Route::get('/getAnnouncements', [AnnouncementController::class, 'getAnnouncements'])->name('crm.announcement.getAnnouncements');
            Route::get('/getAnnouncementLogEntries', [AnnouncementController::class, 'getAnnouncementLogEntries'])->name('crm.announcement.getAnnouncementLogEntries');

        });

        Route::prefix('notification')->group(callback: function () {
            Route::get('/', [NotificationController::class, 'index'])->name('crm.notification.index');
            Route::get('/getNotifications', [NotificationController::class, 'getNotifications'])->name('crm.notification.getNotifications');
    
            Route::get('/detail/{id}', [NotificationController::class, 'detail'])->name('crm.notification.detail');
            Route::get('/getNotificationData', [NotificationController::class, 'getNotificationData'])->name('crm.notification.getNotificationData');
            Route::get('/getNotificationLogEntries', [NotificationController::class, 'getNotificationLogEntries'])->name('crm.notification.getNotificationLogEntries');

        });

        Route::prefix('document')->group(callback: function () {
            Route::get('/', [DocumentController::class, 'index'])->name('crm.document.index');
            Route::get('/getDocuments', [DocumentController::class, 'getDocuments'])->name('crm.document.getDocuments');
            Route::get('/getDocumentLogEntries', [DocumentController::class, 'getDocumentLogEntries'])->name('crm.document.getDocumentLogEntries');

        });

        Route::prefix('internalNews')->group(callback: function () {
            Route::get('/', [InternalNewsController::class, 'index'])->name('crm.internalNews.index');
            Route::get('/getInternalNews', [InternalNewsController::class, 'getInternalNews'])->name('crm.internalNews.getInternalNews');
            Route::get('/getInternalNewsLogEntries', [InternalNewsController::class, 'getInternalNewsLogEntries'])->name('crm.internalNews.getInternalNewsLogEntries');

        });

        Route::prefix('privateMessage')->group(callback: function () {
            Route::get('/', [PrivateMessageController::class, 'index'])->name('crm.privateMessage.index');
            Route::get('/getPrivateMessages', [PrivateMessageController::class, 'getPrivateMessages'])->name('crm.privateMessage.getPrivateMessages');
            Route::get('/getPrivateMessageLogEntries', [PrivateMessageController::class, 'getPrivateMessageLogEntries'])->name('crm.privateMessage.getPrivateMessageLogEntries');

        });

        Route::prefix('accountManager')->group(callback: function () {
            Route::get('/', [AccountManagerProfileController::class, 'index'])->name('crm.accountManager.index');
            Route::get('/getAccountManagerProfiles', [AccountManagerProfileController::class, 'getAccountManagerProfiles'])->name('crm.accountManager.getAccountManagerProfiles');
            Route::get('/getAccontManagerProfileLogEntries', [AccountManagerProfileController::class, 'getAccontManagerProfileLogEntries'])->name('crm.accountManager.getAccontManagerProfileLogEntries');

        });

        Route::prefix('application')->group(callback: function () {
            Route::get('/', [ApplicationController::class, 'index'])->name('crm.application.index');
            Route::get('/getApplications', [ApplicationController::class, 'getApplications'])->name('crm.application.getApplications');
            Route::get('/getApplicationLogEntries', [ApplicationController::class, 'getApplicationLogEntries'])->name('crm.application.getApplicationLogEntries');

        });

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
            Route::get('/getLeadLogEntries', [LeadController::class, 'getLeadLogEntries'])->name('crm.lead.getLeadLogEntries');

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

        Route::prefix('paymentMethod')->group(callback: function () {
            Route::get('/', [PaymentMethodController::class, 'index'])->name('crm.paymentMethod.index');
            Route::get('/getPaymentMethods', [PaymentMethodController::class, 'getPaymentMethods'])->name('crm.paymentMethod.getPaymentMethods');
            Route::get('/getPaymentMethodLogEntries', [PaymentMethodController::class, 'getPaymentMethodLogEntries'])->name('crm.paymentMethod.getPaymentMethodLogEntries');
        });

        Route::prefix('paymentSubmission')->group(callback: function () {
            Route::get('/', [PaymentSubmissionController::class, 'index'])->name('crm.paymentSubmission.index');
            Route::get('/getPaymentSubmissions', [PaymentSubmissionController::class, 'getPaymentSubmissions'])->name('crm.paymentSubmission.getPaymentSubmissions');
            Route::get('/getPaymentSubmissionLogEntries', [PaymentSubmissionController::class, 'getPaymentSubmissionLogEntries'])->name('crm.paymentSubmission.getPaymentSubmissionLogEntries');
        });

    });

require __DIR__.'/auth.php';
