<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DirektoratController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PosisiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\LearningLogController;
use App\Http\Controllers\TeamApprovalController;
use App\Http\Controllers\LearningPlatformController;
use App\Http\Controllers\LearningTargetController;
use App\Http\Controllers\LearningReportController;
use App\Http\Controllers\LearningPlanProposalController;
use App\Http\Controllers\LearningPlanReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Direktorat Routes (secured by permissions)
    Route::get('/direktorat/index', [DirektoratController::class, 'index'])->middleware('permission:view any direktorat')->name('direktorat.index');
    Route::get('/direktorat/create', [DirektoratController::class, 'create'])->middleware('permission:create direktorat')->name('direktorat.create');
    Route::post('/direktorat', [DirektoratController::class, 'store'])->middleware('permission:create direktorat')->name('direktorat.store');
    Route::get('/direktorat/{direktorat}', [DirektoratController::class, 'show'])->middleware('permission:view direktorat')->name('direktorat.show');
    Route::get('/direktorat/{direktorat}/edit', [DirektoratController::class, 'edit'])->middleware('permission:update direktorat')->name('direktorat.edit');
    Route::put('/direktorat/{direktorat}', [DirektoratController::class, 'update'])->middleware('permission:update direktorat')->name('direktorat.update');
    Route::delete('/direktorat/{direktorat}', [DirektoratController::class, 'destroy'])->middleware('permission:delete direktorat')->name('direktorat.destroy');

    // Divisi Routes (secured)
    Route::get('/divisi/index', [DivisiController::class, 'index'])->middleware('permission:view any divisi')->name('divisi.index');
    Route::get('/divisi/create', [DivisiController::class, 'create'])->middleware('permission:create divisi')->name('divisi.create');
    Route::post('/divisi', [DivisiController::class, 'store'])->middleware('permission:create divisi')->name('divisi.store');
    Route::get('/divisi/{divisi}', [DivisiController::class, 'show'])->middleware('permission:view divisi')->name('divisi.show');
    Route::get('/divisi/{divisi}/edit', [DivisiController::class, 'edit'])->middleware('permission:update divisi')->name('divisi.edit');
    Route::put('/divisi/{divisi}', [DivisiController::class, 'update'])->middleware('permission:update divisi')->name('divisi.update');
    Route::delete('/divisi/{divisi}', [DivisiController::class, 'destroy'])->middleware('permission:delete divisi')->name('divisi.destroy');

    // Unit Routes (secured)
    Route::get('/unit/index', [UnitController::class, 'index'])->middleware('permission:view any unit')->name('unit.index');
    Route::get('/unit/create', [UnitController::class, 'create'])->middleware('permission:create unit')->name('unit.create');
    Route::post('/unit', [UnitController::class, 'store'])->middleware('permission:create unit')->name('unit.store');
    Route::get('/unit/{unit}/edit', [UnitController::class, 'edit'])->middleware('permission:update unit')->name('unit.edit');
    Route::put('/unit/{unit}', [UnitController::class, 'update'])->middleware('permission:update unit')->name('unit.update');
    Route::delete('/unit/{unit}', [UnitController::class, 'destroy'])->middleware('permission:delete unit')->name('unit.destroy');

    // Karyawan Routes (secured)
    Route::get('/karyawan/index', [KaryawanController::class, 'index'])->middleware('permission:view any karyawan')->name('karyawan.index');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->middleware('permission:create karyawan')->name('karyawan.create');
    Route::post('/karyawan', [KaryawanController::class, 'store'])->middleware('permission:create karyawan')->name('karyawan.store');
    Route::get('/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->middleware('permission:update karyawan')->name('karyawan.edit');
    Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])->middleware('permission:update karyawan')->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->middleware('permission:delete karyawan')->name('karyawan.destroy');

    // Ajax endpoints for dependent selects (read-only permissions)
    Route::get('/ajax/divisi-by-direktorat/{direktorat}', [DivisiController::class, 'ajaxByDirektorat'])->middleware('permission:view any divisi')->name('ajax.divisi.by.direktorat');
    Route::get('/ajax/unit-by-divisi/{divisi}', [UnitController::class, 'ajaxByDivisi'])->middleware('permission:view any unit')->name('ajax.unit.by.divisi');

    // Jabatan Routes (secured)
    Route::get('/jabatan/index', [JabatanController::class, 'index'])->middleware('permission:view any jabatan')->name('jabatan.index');
    Route::get('/jabatan/create', [JabatanController::class, 'create'])->middleware('permission:create jabatan')->name('jabatan.create');
    Route::post('/jabatan', [JabatanController::class, 'store'])->middleware('permission:create jabatan')->name('jabatan.store');
    Route::get('/jabatan/{jabatan}/edit', [JabatanController::class, 'edit'])->middleware('permission:update jabatan')->name('jabatan.edit');
    Route::put('/jabatan/{jabatan}', [JabatanController::class, 'update'])->middleware('permission:update jabatan')->name('jabatan.update');
    Route::delete('/jabatan/{jabatan}', [JabatanController::class, 'destroy'])->middleware('permission:delete jabatan')->name('jabatan.destroy');

    // Posisi Routes (secured)
    Route::get('/posisi/index', [PosisiController::class, 'index'])->middleware('permission:view any posisi')->name('posisi.index');
    Route::get('/posisi/create', [PosisiController::class, 'create'])->middleware('permission:create posisi')->name('posisi.create');
    Route::post('/posisi', [PosisiController::class, 'store'])->middleware('permission:create posisi')->name('posisi.store');
    Route::get('/posisi/{posisi}/edit', [PosisiController::class, 'edit'])->middleware('permission:update posisi')->name('posisi.edit');
    Route::put('/posisi/{posisi}', [PosisiController::class, 'update'])->middleware('permission:update posisi')->name('posisi.update');
    Route::delete('/posisi/{posisi}', [PosisiController::class, 'destroy'])->middleware('permission:delete posisi')->name('posisi.destroy');

    // Roles & Permissions management (restricted)
    Route::middleware('permission:manage roles & permissions')->group(function(){
        // Roles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::put('/roles/{role}/users', [RoleController::class, 'syncUsers'])->name('roles.users.sync');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // Permissions
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // Users -> Roles management
        Route::get('/users/roles', [\App\Http\Controllers\UserRoleController::class, 'index'])->name('users.roles.index');
        Route::put('/users/{user}/roles', [\App\Http\Controllers\UserRoleController::class, 'update'])->name('users.roles.update');
    });

    // Learning Platforms
    Route::get('/learning/platforms', [LearningPlatformController::class, 'index'])->middleware('permission:view any learning platform')->name('learning.platforms.index');
    Route::post('/learning/platforms', [LearningPlatformController::class, 'store'])->middleware('permission:create learning platform')->name('learning.platforms.store');
    Route::put('/learning/platforms/{platform}', [LearningPlatformController::class, 'update'])->middleware('permission:update learning platform')->name('learning.platforms.update');
    Route::delete('/learning/platforms/{platform}', [LearningPlatformController::class, 'destroy'])->middleware('permission:delete learning platform')->name('learning.platforms.destroy');

    // Learning Targets
    Route::get('/learning/targets', [LearningTargetController::class, 'index'])->middleware('permission:view any learning target')->name('learning.targets.index');
    Route::post('/learning/targets', [LearningTargetController::class, 'store'])->middleware('permission:create learning target')->name('learning.targets.store');
    Route::put('/learning/targets/{target}', [LearningTargetController::class, 'update'])->middleware('permission:update learning target')->name('learning.targets.update');
    Route::delete('/learning/targets/{target}', [LearningTargetController::class, 'destroy'])->middleware('permission:delete learning target')->name('learning.targets.destroy');

    // Learning Plan Proposals (Manager/Senior Manager -> role Admin)
    Route::middleware('role:Admin|Super Admin')->group(function(){
        Route::get('/learning/plans', [LearningPlanProposalController::class, 'index'])->name('learning.plans.index');
        Route::get('/learning/plans/create', [LearningPlanProposalController::class, 'create'])->name('learning.plans.create');
        Route::post('/learning/plans', [LearningPlanProposalController::class, 'store'])->name('learning.plans.store');
        Route::get('/learning/plans/{proposal}/edit', [LearningPlanProposalController::class, 'edit'])->name('learning.plans.edit');
        Route::put('/learning/plans/{proposal}', [LearningPlanProposalController::class, 'update'])->name('learning.plans.update');
        Route::post('/learning/plans/{proposal}/submit', [LearningPlanProposalController::class, 'submit'])->name('learning.plans.submit');
        // Impact preview endpoint (read-only)
        Route::get('/learning/plans/impact', [LearningPlanProposalController::class, 'impactPreview'])->name('learning.plans.impact');
    });

    // Recommendation events (track clicks)
    Route::get('/learning/recommendations/{recommendation}/click', [\App\Http\Controllers\LearningRecommendationEventController::class, 'click'])->middleware('auth')->name('learning.recommendations.click');

    // Learning Plan Reviews (HR -> role Super Admin)
    Route::middleware('role:Super Admin')->group(function(){
        Route::get('/learning/reviews', [LearningPlanReviewController::class, 'index'])->name('learning.reviews.index');
        Route::get('/learning/reviews/{proposal}', [LearningPlanReviewController::class, 'show'])->name('learning.reviews.show');
        Route::post('/learning/reviews/{proposal}/approve', [LearningPlanReviewController::class, 'approve'])->name('learning.reviews.approve');
        Route::post('/learning/reviews/{proposal}/reject', [LearningPlanReviewController::class, 'reject'])->name('learning.reviews.reject');
    });

    // My Learning Logs
    Route::get('/learning/logs', [LearningLogController::class, 'index'])->middleware('permission:view learning log')->name('learning.logs.index');
    Route::get('/learning/logs/create', [LearningLogController::class, 'create'])->middleware('permission:create learning log')->name('learning.logs.create');
    Route::post('/learning/logs', [LearningLogController::class, 'store'])->middleware('permission:create learning log')->name('learning.logs.store');
    Route::put('/learning/logs/{log}', [LearningLogController::class, 'update'])->middleware('permission:update learning log')->name('learning.logs.update');
    Route::post('/learning/logs/{log}/submit', [LearningLogController::class, 'submit'])->middleware('permission:submit learning log')->name('learning.logs.submit');
    Route::get('/learning/logs/{log}', [LearningLogController::class, 'show'])->middleware('permission:view learning log')->name('learning.logs.show');

    // Team Approvals
    Route::get('/learning/approvals', [TeamApprovalController::class, 'index'])->middleware('permission:view any learning log')->name('learning.approvals.index');
    Route::post('/learning/logs/{log}/approve', [TeamApprovalController::class, 'approve'])->middleware('permission:approve learning log')->name('learning.logs.approve');
    Route::post('/learning/logs/{log}/reject', [TeamApprovalController::class, 'reject'])->middleware('permission:reject learning log')->name('learning.logs.reject');

    // Reports
    Route::get('/learning/reports', [LearningReportController::class, 'index'])->middleware('role:Super Admin|Admin')->name('learning.reports.index');
    Route::get('/learning/reports/export-pdf', [LearningReportController::class, 'exportPdf'])->middleware('role:Super Admin|Admin')->name('learning.reports.export.pdf');
    Route::get('/learning/reports/{karyawan}', [LearningReportController::class, 'show'])->middleware('role:Super Admin|Admin')->name('learning.reports.show');
});

require __DIR__.'/auth.php';
