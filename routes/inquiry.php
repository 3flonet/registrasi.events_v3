<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Inquiry\Index as AdminInquiryIndex;
use App\Livewire\Admin\Inquiry\Builder as AdminInquiryBuilder;
use App\Livewire\Admin\Inquiry\Monitoring as AdminInquiryMonitoring;
use App\Livewire\Public\Inquiry\Landing as PublicInquiryLanding;
use App\Livewire\Public\Inquiry\Form as PublicInquiryForm;
use App\Livewire\Public\Inquiry\Success as PublicInquirySuccess;

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'permission:manage forms'])->prefix('admin/inquiries')->name('admin.inquiries.')->group(function () {
    // 1. Management Tipe Inquiry (Sponsorship, Speaker, dll)
    Route::get('/', AdminInquiryIndex::class)->name('index'); 
    
    // 2. Monitoring Submission (Hasil Input User)
    Route::get('/monitoring', AdminInquiryMonitoring::class)->name('monitoring');
    
    // 3. Builder Form & Kategori
    Route::get('/{form}/builder', AdminInquiryBuilder::class)->name('builder');
});


// --- PUBLIC ROUTES ---
Route::prefix('inquiry')->name('public.inquiry.')->group(function () {
    // 1. Landing Page (Pilih Tipe Inquiry: Sponsorship / Partnership)
    Route::get('/', PublicInquiryLanding::class)->name('landing');
    
    // 2. Halaman Form (Isi Data + Pilih Kategori Dynamic)
    Route::get('/{form:slug}', PublicInquiryForm::class)->name('show');
    
    // 3. Success Page (Download Proposal)
    Route::get('/{submission}/success', PublicInquirySuccess::class)->name('success');
});
