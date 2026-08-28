<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DirectorDashboardController;
use App\Http\Controllers\Admin\ContractManagementController;
use App\Http\Controllers\Direksi\DashboardController as DireksiDashboardController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\DoubleRole\DashboardController as DoubleRoleDashboardController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/notifications', 'App\Http\Controllers\NotificationController@index')->middleware('auth')->name('notifications.index');
Route::post('/impersonation/stop', 'App\Http\Controllers\Admin\UserController@stopImpersonating')->middleware('auth')->name('impersonation.stop');

// Super Admin Routes
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/dashboard', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/admin/profile', 'App\\Http\\Controllers\\Admin\\ProfileController@edit')->name('admin.profile.edit');
    Route::put('/admin/profile', 'App\\Http\\Controllers\\Admin\\ProfileController@update')->name('admin.profile.update');
    Route::get('/admin/activity-logs', 'App\\Http\\Controllers\\Admin\\ActivityLogController@index')->name('activity.logs');
    Route::get('/admin/backup', 'App\\Http\\Controllers\\Admin\\BackupController@download')->name('admin.backup');
    
    Route::get('/admin/users', 'App\Http\Controllers\Admin\UserController@index')->name('admin.users.index');
    Route::get('/admin/users/create', 'App\Http\Controllers\Admin\UserController@create')->name('admin.users.create');
    Route::post('/admin/users', 'App\Http\Controllers\Admin\UserController@store')->name('admin.users.store');
    Route::patch('/admin/users/{user}', 'App\Http\Controllers\Admin\UserController@update')->name('admin.users.update');
    Route::patch('/admin/users/{user}/role', 'App\Http\Controllers\Admin\UserController@updateRole')->name('admin.users.role');
    Route::patch('/admin/users/{user}/password', 'App\Http\Controllers\Admin\UserController@resetPassword')->name('admin.users.password');
    Route::patch('/admin/users/{user}/suspend', 'App\Http\Controllers\Admin\UserController@toggleSuspend')->name('admin.users.suspend');
    Route::post('/admin/users/{user}/impersonate', 'App\Http\Controllers\Admin\UserController@impersonate')->name('admin.users.impersonate');
    Route::delete('/admin/users/{user}', 'App\Http\Controllers\Admin\UserController@destroy')->name('admin.users.destroy');
    
    Route::get('/admin/employees', 'App\Http\Controllers\Admin\EmployeeController@index')->name('admin.employees.index');
    Route::get('/admin/employees/create', 'App\Http\Controllers\Admin\EmployeeController@create')->name('admin.employees.create');
    Route::post('/admin/employees', 'App\Http\Controllers\Admin\EmployeeController@store')->name('admin.employees.store');
    Route::delete('/admin/employees/{employee}', 'App\Http\Controllers\Admin\EmployeeController@destroy')->name('admin.employees.destroy');
    Route::get('/admin/employees/template', 'App\Http\Controllers\Admin\EmployeeDataExchangeController@template')->defaults('role', 'karyawan')->name('admin.employees.template');
    Route::get('/admin/employees/export', 'App\Http\Controllers\Admin\EmployeeDataExchangeController@export')->defaults('role', 'karyawan')->name('admin.employees.export');
    Route::get('/admin/employees/import', fn () => redirect()->route('admin.employees.index'));
    Route::post('/admin/employees/import', 'App\Http\Controllers\Admin\EmployeeDataExchangeController@import')->defaults('role', 'karyawan')->name('admin.employees.import');
    
    Route::get('/admin/teachers', 'App\Http\Controllers\Admin\TeacherController@index')->name('admin.teachers.index');
    Route::get('/admin/teachers/create', 'App\Http\Controllers\Admin\TeacherController@create')->name('admin.teachers.create');
    Route::post('/admin/teachers', 'App\Http\Controllers\Admin\TeacherController@store')->name('admin.teachers.store');
    Route::delete('/admin/teachers/{teacher}', 'App\Http\Controllers\Admin\TeacherController@destroy')->name('admin.teachers.destroy');
    Route::get('/admin/teachers/template', 'App\Http\Controllers\Admin\EmployeeDataExchangeController@template')->defaults('role', 'pengajar')->name('admin.teachers.template');
    Route::get('/admin/teachers/export', 'App\Http\Controllers\Admin\EmployeeDataExchangeController@export')->defaults('role', 'pengajar')->name('admin.teachers.export');
    Route::get('/admin/teachers/import', fn () => redirect()->route('admin.teachers.index'));
    Route::post('/admin/teachers/import', 'App\Http\Controllers\Admin\EmployeeDataExchangeController@import')->defaults('role', 'pengajar')->name('admin.teachers.import');
    Route::get('/admin/documents/{document}/file', 'App\Http\Controllers\Admin\DocumentController@show')->name('admin.documents.show');
    
    Route::get('/admin/specializations', 'App\Http\Controllers\Admin\SpecializationController@index')->name('admin.specializations.index');
    Route::post('/admin/specializations', 'App\Http\Controllers\Admin\SpecializationController@store')->name('admin.specializations.store');
    Route::delete('/admin/specializations/{specialization}', 'App\Http\Controllers\Admin\SpecializationController@destroy')->name('admin.specializations.destroy');
    Route::get('/admin/announcements', 'App\Http\Controllers\Admin\AnnouncementController@index')->name('admin.announcements.index');
    Route::post('/admin/announcements', 'App\Http\Controllers\Admin\AnnouncementController@store')->name('admin.announcements.store');
    Route::delete('/admin/announcements/{announcement}', 'App\Http\Controllers\Admin\AnnouncementController@destroy')->name('admin.announcements.destroy');
    
    // Director Dashboard Routes (For Executive Monitoring)
    Route::get('/admin/director/dashboard', [DirectorDashboardController::class, 'index'])->name('admin.director.dashboard');
    Route::get('/admin/director/report-request', [DirectorDashboardController::class, 'reportRequest'])->name('admin.director.report-request');

    Route::get('/admin/contracts', [ContractManagementController::class, 'index'])->name('admin.contracts.index');
    Route::get('/admin/contracts/data', [ContractManagementController::class, 'data'])->name('admin.contracts.data');
    Route::get('/admin/contracts/monitoring', [ContractManagementController::class, 'monitoring'])->name('admin.contracts.monitoring');
    Route::get('/admin/contracts/expiring', [ContractManagementController::class, 'expiring'])->name('admin.contracts.expiring');
    Route::get('/admin/contracts/history', [ContractManagementController::class, 'history'])->name('admin.contracts.history');
    Route::get('/admin/contracts/export', [ContractManagementController::class, 'exportAll'])->name('admin.contracts.export');
    Route::get('/admin/contracts/{nip}', [ContractManagementController::class, 'show'])->name('admin.contracts.show');
    Route::post('/admin/contracts/{nip}/extend', [ContractManagementController::class, 'extend'])->name('admin.contracts.extend');
    Route::put('/admin/contracts/history/{contract}', [ContractManagementController::class, 'updateExtension'])->name('admin.contracts.extension.update');
    Route::delete('/admin/contracts/history/{contract}', [ContractManagementController::class, 'cancelExtension'])->name('admin.contracts.extension.cancel');
    Route::get('/admin/contracts/{nip}/export', [ContractManagementController::class, 'exportEmployee'])->name('admin.contracts.export.employee');
    // Role-specific API endpoints
    Route::get('/admin/contracts/api/admin', [ContractManagementController::class, 'apiAdmin'])->name('admin.contracts.api.admin');
    Route::get('/admin/contracts/api/direksi', [ContractManagementController::class, 'apiDireksi'])->name('admin.contracts.api.direksi');
    Route::get('/admin/contracts/api/me', [ContractManagementController::class, 'apiMe'])->name('admin.contracts.api.me');
    Route::post('/admin/director/report-request', [DirectorDashboardController::class, 'storeReportRequest'])->name('admin.director.store-report-request');
    Route::get('/admin/director/report-history', [DirectorDashboardController::class, 'reportHistory'])->name('admin.director.report-history');
    Route::get('/admin/director/report/{report}/download', [DirectorDashboardController::class, 'downloadReport'])->name('admin.director.download-report');
    Route::delete('/admin/director/report/{report}', [DirectorDashboardController::class, 'deleteReport'])->name('admin.director.delete-report');
    
    // Legacy employee routes
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::get('/employees/print', [EmployeeController::class, 'print'])->name('employees.print');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::patch('/employees/{employee}/status', [EmployeeController::class, 'updateStatus'])->name('employees.status');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/admin/employees/{employee}/documents', 'App\Http\Controllers\Admin\DocumentController@store')->name('admin.documents.store');
    Route::patch('/admin/employees/{employee}/status', 'App\Http\Controllers\Admin\EmployeeController@updateStatus')->name('admin.employees.status');
});

// Direksi Routes (READ-ONLY)
Route::middleware(['auth', 'role:direksi'])->group(function () {
    Route::get('/direksi/dashboard', DireksiDashboardController::class)->name('direksi.dashboard');
    Route::get('/direksi/profile', 'App\Http\Controllers\Direksi\ProfileController@edit')->name('direksi.profile.edit');
    Route::get('/direksi/profile/view', 'App\Http\Controllers\Direksi\ProfileController@show')->name('direksi.profile');
    Route::put('/direksi/profile', 'App\Http\Controllers\Direksi\ProfileController@update')->name('direksi.profile.update');
    Route::get('/direksi/contracts', [ContractManagementController::class, 'direksiContracts'])->name('direksi.contracts.index');
    Route::get('/direksi/employees', 'App\Http\Controllers\Direksi\EmployeeController@index')->name('direksi.employees');
    Route::get('/direksi/teachers', 'App\Http\Controllers\Direksi\TeacherController@index')->name('direksi.teachers');
});

// Employee Routes (KARYAWAN)
Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/employee/dashboard', EmployeeDashboardController::class)->name('employee.dashboard');
    Route::get('/employee/profile/edit', 'App\Http\Controllers\Employee\ProfileController@edit')->name('employee.profile.edit');
    Route::get('/employee/profile', 'App\Http\Controllers\Employee\ProfileController@show')->name('employee.profile');
    Route::put('/employee/profile', 'App\Http\Controllers\Employee\ProfileController@update')->name('employee.profile.update');
    Route::get('/employee/contracts', [ContractManagementController::class, 'employeeContracts'])->name('employee.contracts.index');
    Route::get('/employee/documents', 'App\Http\Controllers\Employee\DocumentController@index')->name('employee.documents.index');
    Route::post('/employee/documents', 'App\Http\Controllers\Employee\DocumentController@store')->name('employee.documents.store');
    Route::get('/employee/documents/{document}/file', 'App\Http\Controllers\Employee\DocumentController@show')->name('employee.documents.show');
});

Route::middleware('auth')->get('/calendar', 'App\Http\Controllers\CalendarController@index')->name('calendar');

// Teacher Routes (PENGAJAR)
Route::middleware(['auth', 'role:pengajar'])->group(function () {
    Route::get('/teacher/dashboard', TeacherDashboardController::class)->name('teacher.dashboard');
    Route::get('/teacher/profile/edit', 'App\Http\Controllers\Teacher\ProfileController@edit')->name('teacher.profile.edit');
    Route::get('/teacher/profile', 'App\Http\Controllers\Teacher\ProfileController@show')->name('teacher.profile');
    Route::put('/teacher/profile', 'App\Http\Controllers\Teacher\ProfileController@update')->name('teacher.profile.update');
    Route::get('/teacher/profile/academic', 'App\Http\Controllers\Teacher\ProfileController@editAcademic')->name('teacher.profile.academic');
    Route::put('/teacher/profile/academic', 'App\Http\Controllers\Teacher\ProfileController@updateAcademic')->name('teacher.profile.academic.update');
    Route::get('/teacher/contracts', [ContractManagementController::class, 'teacherContracts'])->name('teacher.contracts.index');
    Route::get('/teacher/documents', 'App\Http\Controllers\Employee\DocumentController@index')->name('teacher.documents.index');
    Route::post('/teacher/documents', 'App\Http\Controllers\Employee\DocumentController@store')->name('teacher.documents.store');
    Route::get('/teacher/documents/{document}/file', 'App\Http\Controllers\Employee\DocumentController@show')->name('teacher.documents.show');
    Route::get('/teacher/competencies', 'App\Http\Controllers\Teacher\CompetencyController@index')->name('teacher.competencies');
    Route::post('/teacher/competencies', 'App\Http\Controllers\Teacher\CompetencyController@store')->name('teacher.competencies.store');
    Route::delete('/teacher/competencies/{competency}', 'App\Http\Controllers\Teacher\CompetencyController@destroy')->name('teacher.competencies.destroy');
    Route::post('/teacher/portfolios', 'App\Http\Controllers\Teacher\CompetencyController@storePortfolio')->name('teacher.portfolios.store');
    Route::delete('/teacher/portfolios/{portfolio}', 'App\Http\Controllers\Teacher\CompetencyController@destroyPortfolio')->name('teacher.portfolios.destroy');
    Route::get('/teacher/portfolios/{portfolio}/file', 'App\Http\Controllers\Teacher\CompetencyController@showPortfolio')->name('teacher.portfolios.show');
});

// Double Role Routes (KARYAWAN & PENGAJAR)
Route::middleware(['auth', 'role:karyawan_pengajar'])->group(function () {
    Route::get('/double-role/dashboard', DoubleRoleDashboardController::class)->name('double-role.dashboard');
    Route::get('/double-role/profile', 'App\Http\Controllers\DoubleRole\ProfileController@show')->name('double-role.profile');
    Route::get('/double-role/profile/admin', 'App\Http\Controllers\DoubleRole\ProfileController@editAdmin')->name('double-role.profile.admin');
    Route::get('/double-role/profile/academic', 'App\Http\Controllers\DoubleRole\ProfileController@editAcademic')->name('double-role.profile.academic');
    Route::put('/double-role/profile/admin', 'App\Http\Controllers\DoubleRole\ProfileController@updateAdmin')->name('double-role.profile.admin.update');
    Route::put('/double-role/profile/academic', 'App\Http\Controllers\DoubleRole\ProfileController@updateAcademic')->name('double-role.profile.academic.update');
    Route::get('/double-role/contracts', [ContractManagementController::class, 'doubleRoleContracts'])->name('double-role.contracts.index');
    Route::get('/double-role/competencies', 'App\Http\Controllers\DoubleRole\CompetencyController@index')->name('double-role.competencies');
    Route::post('/double-role/competencies', 'App\Http\Controllers\DoubleRole\CompetencyController@store')->name('double-role.competencies.store');
    Route::post('/double-role/portfolios', 'App\Http\Controllers\DoubleRole\CompetencyController@storePortfolio')->name('double-role.portfolios.store');
});

// Dashboard utama selalu diarahkan sesuai hak akses agar data pegawai lain tidak terbuka.
Route::middleware(['auth', 'role:super_admin,karyawan,pengajar,direksi,karyawan_pengajar'])->group(function () {
    Route::get('/', fn () => redirect()->route(request()->user()->homeRoute()))->name('dashboard');
});
