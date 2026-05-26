<?php

use App\Http\Controllers\ReportAttachmentsController;
use App\Http\Controllers\ReportPdfController;
use App\Livewire\Admin\AdminIndex;
use App\Livewire\Dashboard;
use App\Livewire\Expenses\ExpenseList;
use App\Livewire\Profile\ProfileSettings;
use App\Livewire\Reports\CreateReport;
use App\Livewire\Reports\ReportDetail;
use App\Livewire\Reports\ReportList;
use Illuminate\Support\Facades\Route;

// Raiz → redireciona para dashboard ou login
Route::get('/', fn() => redirect()->route(
    auth()->check() ? 'dashboard' : 'login'
));

// Logout
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Rotas autenticadas
Route::middleware('auth')->group(function () {

    Route::get('/dashboard',  Dashboard::class)->name('dashboard');
    Route::get('/profile',    ProfileSettings::class)->name('profile');

    // Despesas
    Route::get('/expenses', ExpenseList::class)->name('expenses.index');

    // Relatórios
    Route::get('/reports',         ReportList::class)->name('reports.index');
    Route::get('/reports/create',  CreateReport::class)->name('reports.create');
    Route::get('/reports/{report}', ReportDetail::class)->name('reports.show');
    Route::get('/reports/{report}/pdf', [ReportPdfController::class, 'download'])->name('reports.pdf');
    Route::get('/reports/{report}/attachments', [ReportAttachmentsController::class, 'download'])->name('reports.attachments');

    // Admin (só admins)
    Route::middleware('admin')->group(function () {
        Route::get('/admin', AdminIndex::class)->name('admin.index');
    });
});

require __DIR__.'/auth.php';
