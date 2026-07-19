<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\Admin\DashboardController;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Page\Index as PageIndex;
use App\Livewire\Admin\Media\Index as MediaIndex;
use App\Models\Page;
use App\Livewire\Admin\Event\Index as EventIndex;
use App\Http\Controllers\EventController;
use App\Livewire\Admin\Event\Registrants as EventRegistrants;
use App\Http\Controllers\Admin\CertificateController;
use App\Livewire\Admin\Role\Index as RoleIndex;
use App\Http\Controllers\RegistrationController;
use App\Livewire\Admin\Checkin\CameraScanner;
use App\Livewire\Admin\Checkin\HandheldScanner;
use App\Livewire\Admin\User\Index as UserIndex;
use App\Livewire\Admin\User\Edit as UserEdit;
use App\Livewire\Admin\Form\Index as FormIndex;
use App\Livewire\Public\InquiryFormShow;
use App\Models\Event;
use App\Models\Registration;
use App\Livewire\Admin\News\Index as NewsIndex;
use App\Livewire\Admin\News\Categories as NewsCategories;
use App\Http\Controllers\NewsController;
use App\Livewire\UserDashboard;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\OnlineCheckinController;
use App\Http\Controllers\FeedbackResultController;
use App\Livewire\Public\SubmissionsResult;
use App\Http\Controllers\InquiryFormController;
use Livewire\Volt\Volt;
use App\Livewire\Exhibitor\Dashboard as ExhibitorDashboard;
use App\Http\Controllers\ScanController;
use App\Livewire\Public\ExhibitorList;
use App\Livewire\Exhibitor\EditProfile;
use App\Livewire\Public\ExhibitorProfile;
use App\Livewire\ScannerPage;
use App\Livewire\Admin\Analytics\InterestReport;
use App\Livewire\Admin\Register as AdminRegister;
use App\Livewire\Admin\Page\WelcomeBuilder;

use App\Models\WelcomeSection;
use App\Models\Post;

use App\Livewire\Admin\Banner\Index as BannerIndex;
use App\Models\Banner;
use App\Livewire\Admin\Ads\Index as AdsIndex;

use App\Http\Controllers\Public\GalleryController;
use App\Models\Album;

use App\Livewire\Admin\Event\Report as EventReport;
use App\Livewire\Admin\Checkin\ReturnByQr;
use App\Livewire\EventRegistrationForm;
use App\Livewire\Admin\Voucher\Index as VoucherIndex;

use App\Livewire\Admin\FileManager\Index as FileManagerIndex;
use App\Http\Controllers\Admin\FileManagerController;
use App\Livewire\Admin\Checkin\UnreturnedRfid;
use App\Livewire\Public\Event\LiveReport as PublicLiveReport;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/register/admin', AdminRegister::class)->name('admin.register');

Route::get('/media/public/stream', [\App\Http\Controllers\Public\PublicMediaController::class, 'stream'])
    ->name('media.stream.public');

// === ROUTE UNDANGAN (PUBLIC) ===
Route::get('/invitation/{invitation}/confirm', [\App\Http\Controllers\InvitationController::class, 'show'])->name('invitation.confirm');
Route::post('/invitation/{invitation}/submit', [\App\Http\Controllers\InvitationController::class, 'submit'])->name('invitation.submit');
Route::get('/invitation/{invitation}/letter', [\App\Http\Controllers\InvitationController::class, 'letter'])->name('invitation.letter');

Route::get('/invoice/{registration_uuid}', \App\Livewire\Public\Invoice::class)->name('invoice.show');
Route::get('/order-cancelled/{registration_uuid}', \App\Livewire\Public\OrderCancelled::class)->name('order.cancelled');

// Route Publik E-commerce
Route::get('/shop/checkout', \App\Livewire\Public\ProductCheckout::class)->name('shop.checkout'); // Checkout harus sebelum slug toko
Route::get('/shop/{slug}', \App\Livewire\Public\TenantShop::class)->name('tenant.shop');

Route::get('/event-agenda', \App\Livewire\Public\EventAgenda::class)->name('public.agenda');
Route::get('/event-programme', \App\Livewire\Public\EventProgramme::class)->name('public.programme');

Route::get('/collaborators', \App\Livewire\Public\Collaborators::class)->name('public.collaborators');

// === ROUTE PUBLIC LIVE REPORT ===
Route::get('/public/report/{event:slug}', PublicLiveReport::class)->name('public.report.show');

// === ROUTE PUBLIC CERTIFICATE ===
Route::get('/certificate/{registration_uuid}', [\App\Http\Controllers\PublicCertificateController::class, 'show'])->name('public.certificate.show');

Route::get('/', function () {
    // MODIFIKASI: Tambahkan 'customSection.template' ke dalam eager loading
    $sections = WelcomeSection::where('is_visible', true)
        ->with(['items.item', 'customSection.template']) // <-- Perubahan di sini
        ->orderBy('order')
        ->get();

    $data = $sections->keyBy('component')->map(function ($section) {
        if ($section->component) {
            $items = $section->items->pluck('item');
            return $items->filter();
        }
        return null;
    });

    if ($sections->firstWhere('component', 'banner')) {
        $data['banner'] = Banner::where('is_active', true)->orderBy('order')->get();
    }

    if ($sections->firstWhere('component', 'plans')) {
        $data['plans'] = \App\Models\SubscriptionPlan::where('is_active', true)->get();
    }

    return view('welcome', compact('sections', 'data'));
})->name('home');

Route::get('/dashboard', UserDashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/scanner', ScannerPage::class)
    ->middleware('auth')
    ->name('scanner.page');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/my-qrcode', function () {
    // Pastikan pengguna sudah login
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    // URL yang akan dijadikan QR code adalah URL 'connect' milik pengguna
    $connectUrl = route('scan.connect', auth()->user()->uuid);

    // Gunakan library untuk membuat QR code
    $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate($connectUrl);

    // Tampilkan di sebuah view
    return view('user.my-qrcode', ['qrCode' => $qrCode]);
})->middleware('auth')->name('user.qrcode');

Route::get('/events/{event:slug}/register/success/{registration}', function (Event $event, $registration, \App\Services\TenantService $tenantService) {
    $reg = Registration::withoutGlobalScope('organizer')->where('uuid', $registration)->firstOrFail();
    $tenantService->setOrganizer($event->organizer);
    return view('event.registration-success', [
        'event' => $event,
        'registration' => $reg
    ]);
})->name('events.register.success');

Route::get('/calendar/{event:slug}/ics', [\App\Http\Controllers\Public\CalendarController::class, 'ical'])->name('calendar.ics');



// 1. Route Form Pendaftaran (Landing Page -> Klik Daftar -> Kesini)
Route::get('/events/{event:slug}/register', EventRegistrationForm::class)
    ->name('event.register');

// 2. Route Halaman Sukses (Setelah bayar/daftar)
Route::get('/events/{event:slug}/registration-success', function ($slug) {
    $event = Event::where('slug', $slug)->firstOrFail();
    return view('event.registration-success', compact('event'));
})->name('event.registration.success');




Route::get('/gallery', [GalleryController::class, 'index']) // <-- TAMBAHKAN INI
    ->name('public.gallery.index');

Route::get('/gallery/{album:slug}', [GalleryController::class, 'showAlbum'])
    ->name('public.gallery.album');

// === PWA MANIFEST ===
Route::get('/manifest.json', function () {
    $tenantService = app(\App\Services\TenantService::class);
    $activeOrganizer = $tenantService->getOrganizer();

    $name = $activeOrganizer ? $activeOrganizer->name : config('settings.app_name', 'Registrasi Events');
    $shortName = $activeOrganizer ? Str::limit($activeOrganizer->name, 12, '') : 'Reg.Events';
    
    $favicon = $activeOrganizer && $activeOrganizer->favicon_path 
                ? asset('storage/' . $activeOrganizer->favicon_path) 
                : (config('settings.app_favicon') ? asset('storage/' . config('settings.app_favicon')) : null);

    // Fallback to a placeholder if no icon is found
    if (!$favicon) {
        $initials = $activeOrganizer ? substr($activeOrganizer->name, 0, 2) : 'RE';
        $favicon = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=322365&color=fff&size=512";
    }

    return response()->json([
        "name" => $name,
        "short_name" => $shortName,
        "start_url" => "/",
        "display" => "standalone",
        "background_color" => "#1a1235",
        "theme_color" => "#322365",
        "description" => $activeOrganizer->description ?? "Platform Registrasi Event Terbaik",
        "icons" => [
            [
                "src" => $favicon,
                "sizes" => "192x192",
                "type" => "image/png",
                "purpose" => "any maskable"
            ],
            [
                "src" => $favicon,
                "sizes" => "512x512",
                "type" => "image/png",
                "purpose" => "any maskable"
            ]
        ]
    ]);
});


require __DIR__ . '/auth.php';
require __DIR__ . '/inquiry.php';

Volt::route('/register/exhibitor', 'pages.auth.register-exhibitor')
    ->middleware('guest')
    ->name('register.exhibitor');

Volt::route('/register/tenant', 'pages.auth.register-tenant')
    ->middleware('guest')
    ->name('register.tenant');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/tickets/{uuid}/qrcode', [RegistrationController::class, 'showQrCode'])->name('tickets.qrcode');
Route::get('/check-in/{uuid}', [RegistrationController::class, 'scanCheckIn'])->name('checkin.scan');
Route::get('/forms/{form:slug}', InquiryFormShow::class)->name('forms.show');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{post:slug}', [NewsController::class, 'show'])->name('news.show');

// Rute untuk Check-in Online
Route::get('/online-checkin/{event}', [OnlineCheckinController::class, 'show'])->name('online.checkin.show');
Route::post('/online-checkin/{event}', [OnlineCheckinController::class, 'store'])->name('online.checkin.store');
Route::get('/online-checkin/success/{registration}', [OnlineCheckinController::class, 'success'])->name('online.checkin.success');


// Rute untuk menampilkan halaman pengisian feedback
Route::get('/feedback/{event}/submit/{registration}', [\App\Http\Controllers\FeedbackController::class, 'show'])->name('feedback.show');
Route::post('/feedback/{event}/submit/{registration}', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');

// RUTE BARU: Untuk menampilkan hasil feedback secara publik
Route::get('/events/{event:slug}/feedback/results', [FeedbackResultController::class, 'show'])->name('feedback.results.show');

Route::get('/forms/{form:slug}/results', SubmissionsResult::class)->name('forms.results.show');
Route::get('/forms/{form:slug}/results/export', [InquiryFormController::class, 'exportSubmissions'])->name('forms.results.export');


// EXHIBITOR DASHBOARD ROUTE
Route::get('/exhibitor/dashboard', ExhibitorDashboard::class)
    ->middleware(['auth', 'role:Exhibitor']) // <-- INI PENTING!
    ->name('exhibitor.dashboard');

Route::get('/exhibitor/profile', EditProfile::class)
    ->middleware(['auth', 'role:Exhibitor'])
    ->name('exhibitor.profile');

Route::get('/exhibitors', ExhibitorList::class)->name('exhibitors.index');

Route::get('/exhibitors/{user:uuid}', ExhibitorProfile::class)->name('exhibitors.show');

// Route::get('/scan/link/{exhibitor_uuid}/{attendee_uuid}', [ScanController::class, 'linkExhibitorAndAttendee'])
//     ->name('scan.link');

Route::get('/admin/files/stream', [\App\Http\Controllers\Admin\FileManagerController::class, 'stream'])
    ->middleware(['auth'])
    ->name('files.stream');


Route::get('/connect/{uuid}', [ScanController::class, 'connect'])
    ->middleware('auth') // PENTING: Memastikan pengguna sudah login saat scan
    ->name('scan.connect');

Route::get('/social-wall', \App\Livewire\Public\SocialWall::class)->name('social-wall');


// ==> LETAKKAN RUTE DINAMIS DI SINI HARUS TERAKHIR <==




// --- ROUTES KHUSUS TENANT ---
Route::middleware(['auth', 'tenant'])->prefix('tenant')->name('tenant.')->group(function () {

    // Dashboard Produk (Menjadi: tenant.products)
    Route::get('/products', \App\Livewire\Tenant\ProductManager::class)->name('products');

    // Manajemen Pesanan (Menjadi: tenant.orders)
    Route::get('/orders', \App\Livewire\Tenant\OrderManager::class)->name('orders');

    // Laporan Penjualan (Menjadi: tenant.report)
    Route::get('/report', \App\Livewire\Tenant\SalesReport::class)->name('report');

    // Route Download QR (A4 PDF) -> (Menjadi: tenant.qr-download)
    Route::get('/qr-code', [\App\Http\Controllers\Tenant\TenantQrController::class, 'downloadPdf'])
        ->name('qr-download'); // <--- Hapus 'tenant.' di sini
});



// ADMIN ROUTES
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
     Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
     Route::get('/wallet', \App\Livewire\Admin\Organizer\Wallet::class)->name('wallet.index');
     Route::get('/withdrawals', \App\Livewire\Admin\Financial\WithdrawalManager::class)->name('withdrawals.index')->middleware('role:Super Admin');
     Route::get('/reports', \App\Livewire\Admin\Reports::class)->name('reports.index')->middleware('role:Super Admin');
     Route::get('/notifications', \App\Livewire\Admin\Notifications\Index::class)->name('notifications.index');
 
     // Menu Management (Diprioritaskan & Dibentengi agar tidak salah baca)
     Route::get('/menus', \App\Livewire\Admin\Menu\Index::class)->name('menus.index');
     Route::get('/menus/create', \App\Livewire\Admin\Menu\MenuEditor::class)->name('menus.create');
     Route::get('/menus/{menu}/edit', \App\Livewire\Admin\Menu\MenuEditor::class)
        ->name('menus.edit')
        ->where('menu', '[0-9]+'); // Hanya izinkan angka untuk ID menu community
     
     Route::get('/files', FileManagerIndex::class)->name('files.index');
    Route::get('/analytics/interest', InterestReport::class)->name('analytics.interest');
    Route::get('/pages', PageIndex::class)->name('pages.index');
    Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
    Route::match(['get', 'post'], '/pages/{page}/builder', \App\Livewire\Admin\Page\PageBuilder::class)->name('pages.builder');
    Route::get('/events/{event}/broadcasts', \App\Livewire\Admin\Event\BroadcastManager::class)->name('events.broadcasts')->middleware('can:manage broadcasts');
    Route::get('/global-broadcast', \App\Livewire\Admin\Broadcast\GlobalManager::class)->name('global-broadcast')->middleware('can:send global broadcasts');
    Route::get('/global-broadcast/template/create', \App\Livewire\Admin\Broadcast\TemplateForm::class)->name('global-broadcast.template.create')->middleware('can:send global broadcasts');
    Route::get('/global-broadcast/template/{template}/edit', \App\Livewire\Admin\Broadcast\TemplateForm::class)->name('global-broadcast.template.edit')->middleware('can:send global broadcasts');
    // Message Templates — Organizer-scoped
    Route::get('/message-templates', \App\Livewire\Admin\MessageTemplate\Index::class)->name('message-templates.index')->middleware('can:manage broadcasts');
    Route::get('/message-templates/categories', \App\Livewire\Admin\MessageTemplate\CategoryManager::class)->name('message-templates.categories')->middleware('can:manage broadcasts');
    Route::get('/message-templates/create', \App\Livewire\Admin\MessageTemplate\Form::class)->name('message-templates.create')->middleware('can:manage broadcasts');
    Route::get('/message-templates/{template}/edit', \App\Livewire\Admin\MessageTemplate\Form::class)->name('message-templates.edit')->middleware('can:manage broadcasts');
    
    // Meta WhatsApp Templates — Super Admin scoped
    Route::get('/whatsapp-templates', \App\Livewire\Admin\WhatsAppTemplate\Index::class)->name('whatsapp-templates.index')->middleware('role:Super Admin');
    Route::get('/whatsapp-templates/create', \App\Livewire\Admin\WhatsAppTemplate\Form::class)->name('whatsapp-templates.create')->middleware('role:Super Admin');
    Route::get('/whatsapp-templates/{template}/edit', \App\Livewire\Admin\WhatsAppTemplate\Form::class)->name('whatsapp-templates.edit')->middleware('role:Super Admin');
     Route::get('/media', MediaIndex::class)->name('media.index');
    Route::get('/events', EventIndex::class)->name('events.index');
    Route::get('/events/create', \App\Livewire\Admin\Event\Form::class)->name('events.create')->middleware('subscription.guard:event_creation');
    Route::get('/events/{event}/edit', \App\Livewire\Admin\Event\Form::class)->name('events.edit');
    Route::get('/events/{event}/tickets', \App\Livewire\Admin\Event\TicketManager::class)->name('events.tickets');

    Route::get('/event-agenda', \App\Livewire\Admin\Agenda\Index::class)->name('agenda.index');
    Route::get('/event-programme', \App\Livewire\Admin\Programme\Index::class)->name('programme.index');
    Route::get('/vouchers', VoucherIndex::class)->name('vouchers.index');
    Route::get('/events/{event}/registrants', EventRegistrants::class)->name('events.registrants');
    Route::get('/events/{event}/certificate-config', \App\Livewire\Admin\Event\CertificateConfig::class)->name('events.certificate-config')->middleware('can:manage events');
    Route::get('/certificate', \App\Livewire\Admin\Event\CertificateList::class)->name('certificate.index')->middleware('can:manage events');
    Route::get('/roles', RoleIndex::class)->name('roles.index');
    Route::get('/check-in', \App\Livewire\Admin\Checkin\Index::class)->name('checkin.index');
    Route::get('/check-in/{event}/camera', CameraScanner::class)->name('checkin.camera');
    Route::get('/check-in/{event}/handheld', HandheldScanner::class)->name('checkin.handheld');
    Route::get('/check-in/{event}/register-rfid', \App\Livewire\Admin\Checkin\RegisterRfid::class)->name('checkin.register-rfid');
    Route::get('/check-in/{event}/rfid-tap', \App\Livewire\Admin\Checkin\RfidTap::class)->name('checkin.rfid-tap');
    Route::get('/check-in/return-rfid', \App\Livewire\Admin\Checkin\ReturnRfid::class)
        ->name('checkin.return-rfid')
        ->middleware('can:checkin attendees');
    Route::get('/check-in/{event}/return-by-qr', ReturnByQr::class)->name('checkin.return-by-qr');
    Route::get('/check-in/unreturned-rfid', UnreturnedRfid::class)->name('checkin.unreturned-rfid');
    
    Route::get('/files/stream', [FileManagerController::class, 'stream'])->name('files.stream');

    Route::get('/events/{event}/report', EventReport::class)->name('events.report');
    Route::get('/users', UserIndex::class)->name('users.index');
    Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');
    Route::get('/forms', FormIndex::class)->name('forms.index');
    Route::get('/news', NewsIndex::class)->name('news.index');
    Route::get('/ads', AdsIndex::class)->name('ads.index');
    Route::get('/news-categories', NewsCategories::class)->name('news.categories');

    Route::get('social-wall', \App\Livewire\Admin\SocialWall\Index::class)->middleware(['can:manage social wall'])->name('social-wall.index');

    Route::get('/settings', \App\Livewire\Admin\Settings\Index::class)->name('settings.index')->middleware('role:Super Admin');
    Route::get('/organizers', \App\Livewire\Admin\Organizer\Index::class)->name('organizers.index')->middleware('role:Super Admin');
    Route::get('/plans', \App\Livewire\Admin\SubscriptionPlan\Index::class)->name('plans.index')->middleware('role:Super Admin');
    
    // Subscription Voucher Management
    Route::group(['middleware' => 'role:Super Admin'], function() {
        Route::get('/subscription-vouchers', \App\Livewire\Admin\SubscriptionVoucher\Index::class)->name('subscription-vouchers.index');
        Route::get('/subscription-vouchers/create', \App\Livewire\Admin\SubscriptionVoucher\Form::class)->name('subscription-vouchers.create');
        Route::get('/subscription-vouchers/{id}/edit', \App\Livewire\Admin\SubscriptionVoucher\Form::class)->name('subscription-vouchers.edit');
    });

    Route::get('/billing', \App\Livewire\Admin\Organizer\Billing::class)->name('billing.index');
    Route::get('/billing/invoice/{id}', [\App\Http\Controllers\Admin\BillingInvoiceController::class, 'download'])->name('billing.invoice.download');
    Route::get('/branding', \App\Livewire\Admin\Organizer\Branding::class)->name('branding.index');
    Route::get('/settings/exhibitor-export', \App\Livewire\Admin\Settings\ExhibitorExport::class)
        ->name('settings.exhibitor-export')
        ->middleware('can:manage application settings');
    Route::get('/settings/sticky-bar', \App\Livewire\Admin\Settings\StickyBarManager::class)
        ->name('settings.sticky-bar')
        ->middleware('can:manage application settings');
    Route::get('/collaborators', \App\Livewire\Admin\Collaborator\Index::class)
        ->name('collaborators.index')
        ->middleware('can:manage application settings');
    Route::get('/section-templates', \App\Livewire\Admin\SectionTemplate\Index::class)
        ->name('section-templates.index')
        ->middleware('can:manage section templates');
    Route::get('/section-templates/{template}/preview', [\App\Http\Controllers\Admin\SectionTemplateController::class, 'preview'])
        ->name('section-templates.preview')
        ->middleware('can:manage section templates');
    Route::get('/section-templates/create', \App\Livewire\Admin\SectionTemplate\Form::class)
        ->name('section-templates.create')
        ->middleware('can:manage section templates');
    Route::get('/section-templates/{template}/edit', \App\Livewire\Admin\SectionTemplate\Form::class)
        ->name('section-templates.edit')
        ->middleware('can:manage section templates');



    Route::get('/pages/welcome-builder', WelcomeBuilder::class)
        ->name('pages.welcome-builder')
        ->middleware('can:manage welcome');

    Route::get('/banners', BannerIndex::class)
        ->name('banners.index')
        ->middleware('can:manage welcome');

    Route::get('/feedback-forms', \App\Livewire\Admin\FeedbackForm\Index::class)
        ->name('feedback-forms.index')
        ->middleware('can:manage forms');

    Route::get('/events/{event}/invitations', \App\Livewire\Admin\Event\InvitationManager::class)
        ->name('events.invitations')
        ->middleware('can:manage events');
    Route::get('/events/{event}/invitations/{invitation}/edit', \App\Livewire\Admin\Event\InvitationEdit::class)
        ->name('events.invitations.edit')
        ->middleware('can:manage events');
});

// Route for custom pages with prefix
Route::get('/pages/{page:slug}', function (App\Models\Page $page) {
    if ($page->status === 'published' || (auth()->check() && auth()->user()->can('manage pages'))) {
        return view('page.show', ['page' => $page]);
    }
    abort(404);
})->name('pages.public.show');

Route::get('/order-cancel-request/{registration}', function ($registration) {
    $reg = Registration::withoutGlobalScope('organizer')->where('uuid', $registration)->firstOrFail();
    if ($reg->payment_status === 'paid') {
        return redirect()->route('invoice.show', $reg->uuid);
    }
    
    $reg->update(['status' => 'cancelled']);
    if ($reg->transaction) {
        $reg->transaction->update(['status' => 'failed']);
    }
    
    return redirect()->route('order.cancelled', $reg->uuid);
})->name('order.cancel_request');

// Route for WhatsApp redirect redirection (bypassing Meta template URL policy restrictions)
Route::get('/chat-wa/{phone}', function ($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return redirect()->away('https://wa.me/' . $phone);
})->name('chat-wa.redirect');

// Catch-all route for custom pages (MUST be at the very bottom)
Route::get('/{page:slug}', function (App\Models\Page $page) {
    // Allow if published OR if user is admin (has 'manage pages' permission)
    if ($page->status === 'published' || (auth()->check() && auth()->user()->can('manage pages'))) {
        return view('page.show', ['page' => $page]);
    }
    
    abort(404);
})->name('page.show');
