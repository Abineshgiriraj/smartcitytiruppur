<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\icons\Boxicons;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\tables\Basic as TablesBasic;


// customize UI
// use App\Http\Controllers\ui\district;

use App\Http\Controllers\ui\school;

use App\Http\Controllers\student_creation;
use App\Http\Controllers\employee_creation;
// use App\Http\Controllers\biometric_creation;
use App\Http\Controllers\biometric_employee;
use App\Http\Controllers\student_report;
use App\Http\Controllers\staff_report;
use App\Http\Controllers\school_report;
use App\Http\Controllers\SchoolController;

use App\Http\Controllers\breakfast_report;
use App\Http\Controllers\attendance_report;
use App\Http\Controllers\afternoon_report;
use App\Http\Controllers\meal_report;
use App\Http\Controllers\TalukCreationController;
use App\Http\Controllers\category_creation;
use App\Http\Controllers\district_creation;
use App\Http\Controllers\standard_creation;
use App\Http\Controllers\designation;
use App\Http\Controllers\academic_year_creation;
use App\Http\Controllers\biometric_creation;
use App\Http\Controllers\category_controller_user;
use App\Http\Controllers\category_user_assign;
// use App\Http\Controllers\zone;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\StaffController;


use App\Http\Controllers\monthly_bf_report;
use App\Http\Controllers\monthly_fn_report;
use App\Http\Controllers\monthly_meal_report;
use App\Http\Controllers\monthly_af_report;

// Main Page Route
// Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::get('/', [LoginBasic::class, 'index'])->name('auth-login-basic');

Route::get('/dashboard', [Analytics::class, 'index'])->name('dashboard-analytics');

Route::get('/district', [district_creation::class, 'index'])->name('district');
Route::get('/district_add', [district_creation::class, 'add'])->name('add_form');
Route::post('/district/store', [district_creation::class, 'store'])->name('district.store');
// Route::get('/get-District', [district_creation::class, 'getDistrict'])->name('district.getDistrict');
Route::get('/district/edit{id}', [district_creation::class, 'edit'])->name('district.edit');
Route::post('/district/update{id}', [district_creation::class, 'update'])->name('district.update');
Route::delete('/district/delete/{id}', [district_creation::class, 'destroy'])->name('district.delete');
Route::get('/zones', [district_creation::class, 'getzone'])->name('zones');



// Route::get('/zone', [zone::class, 'index'])->name('zone');
// Route::get('/zone_add', [zone::class, 'add'])->name('add_form');
// Route::post('/zone/store', [zone::class, 'store'])->name('Zone.store');
// Route::get('/zone/edit/{id}', [zone::class, 'edit'])->name('zone.edit');
// Route::post('/zone/update/{id}', [zone::class, 'update'])->name('zone.update');
// Route::delete('/zone/delete/{id}', [zone::class, 'destroy'])->name('zone.delete');

Route::get('/zone', [ZoneController::class, 'index'])->name('zone.index');
Route::get('/zones/data', [ZoneController::class, 'getData'])->name('zone.data');
Route::get('/zone_add', [ZoneController::class, 'create'])->name('zone.create');
Route::post('/zone', [ZoneController::class, 'store'])->name('zone.store');
Route::get('/zone/{id}/edit', [ZoneController::class, 'edit'])->name('zone.edit');
Route::put('/zone/{id}', [ZoneController::class, 'update'])->name('zone.update');
Route::delete('/zone/{id}', [ZoneController::class, 'destroy'])->name('zone.destroy');


// Route::get('/school', [school::class, 'index'])->name('list');
// Route::get('/school_add', [school::class, 'add'])->name('add_form');

Route::get('school', [SchoolController::class, 'index'])->name('school.index');
Route::get('school/create', [SchoolController::class, 'create'])->name('school.create');
Route::post('school', [SchoolController::class, 'store'])->name('school.store'); // Optional
Route::get('school/{school}/edit', [SchoolController::class, 'edit'])->name('school.edit');
Route::put('school/{school}', [SchoolController::class, 'update'])->name('school.update');
Route::delete('school/{school}', [SchoolController::class, 'destroy'])->name('school.destroy');
Route::get('school/details', [SchoolController::class, 'getdetails'])->name('school.details');




Route::get('/student_list', [student_creation::class, 'index'])->name('list');
Route::get('/student_add', [student_creation::class, 'add'])->name('student_add');

Route::get('/designation_list', [designation::class, 'index'])->name('list');
Route::get('/designation_add', [designation::class, 'add'])->name('designation_add');

Route::resource('staff', StaffController::class);

Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
Route::post('/staff/store', [StaffController::class, 'store'])->name('staff.store');
Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
// Route::post('/staff/update', [StaffController::class, 'update'])->name('staff.update');
Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
Route::get('/staff/details', [StaffController::class, 'getdetail'])->name('staff.details');


Route::get('/biometric_list', [biometric_creation::class, 'index'])->name('biometric_list');
Route::get('/biometric_add', [biometric_creation::class, 'add'])->name('biometric_add');
Route::post('/biometric/store', [biometric_creation::class, 'store'])->name('biometric.store');
Route::get('/biometric/edit/{id}', [biometric_creation::class, 'edit'])->name('biometric.edit');
Route::post('/biometric/update/{id}', [biometric_creation::class, 'update'])->name('biometric.update');
Route::delete('/biometric/delete/{id}', [biometric_creation::class, 'destroy'])->name('biometric.delete');
Route::get('/districts', [biometric_creation::class, 'getDistricts'])->name('districts');




Route::get('/biometric_emp_list', [biometric_employee::class, 'index'])->name('biometric_emp_list');

Route::get('/student_report', [student_report::class, 'index'])->name('student_report');

Route::get('/staff_report', [staff_report::class, 'index'])->name('staff_report');
Route::get('/school_report', [school_report::class, 'index'])->name('school_report');
Route::get('/breakfast_report', [breakfast_report::class, 'index'])->name('breakfast_report');
Route::get('/attendance_report', [attendance_report::class, 'index'])->name('attendance_report');
Route::get('/afternoon_report', [afternoon_report::class, 'index'])->name('afternoon_report');
Route::get('/meal_report', [meal_report::class, 'index'])->name('meal_report');
Route::get('/monthly_bf_report', [monthly_bf_report::class, 'index'])->name('monthly_bf_report');
Route::get('/monthly_fn_report', [monthly_fn_report::class, 'index'])->name('monthly_fn_report');
Route::get('/monthly_meal_report', [monthly_meal_report::class, 'index'])->name('monthly_meal_report');
Route::get('/monthly_af_report', [monthly_af_report::class, 'index'])->name('monthly_af_report');


Route::get('/taluk_creation', [TalukCreationController::class, 'index'])->name('taluk_creation.index');
Route::get('/taluk_creation/create', [TalukCreationController::class, 'create'])->name('taluk_creation.create'); // Changed to 'create' instead of 'add'
Route::post('/taluk_creation/store', [TalukCreationController::class, 'store'])->name('taluk_creation.store');
Route::get('/taluk_creation/{talukName}/edit', [TalukCreationController::class, 'edit'])->name('taluk_creation.edit');
Route::get('/taluk_creation/data', [TalukCreationController::class, 'getData'])->name('taluk_creation.data');
Route::put('/taluk_creation/{id}', [TalukCreationController::class, 'update'])->name('taluk_creation.update');
Route::delete('/taluk_creation/{id}', [TalukCreationController::class, 'destroy'])->name('taluk_creation.destroy');

// Route::get('/taluk_creation_list', [taluk_creation::class, 'index'])->name('taluk_creation.index');
// Route::get('/taluk_creation_add', [taluk_creation::class, 'add'])->name('taluk_creation_add');
// Route::post('/taluk_cretion/edit/{id}',[taluk_creation::class,'edit'])->name('taluk_creation.edit');
// Route::post('/taluk_creation/store/{id}',[taluk_creation::class,'store'])->name('taluk_creation.store');
// Route::post('/taluk_creation/update/{id}',[taluk_creation::class,'update'])->name('taluk_creation.update');
// Route::post('/taluk_creation/getData/{id}',[taluk_creation::class,'getData'])->name('taluk_creation.getData');
// Route::post('/taluk_creation/delete/{id}',[taluk_creation::class,'destroy'])->name('taluk_creation.delete');




Route::get('/category_list', [category_creation::class, 'index'])->name('category_list');
Route::get('/category_add', [category_creation::class, 'add'])->name('category_add');
Route::post('/category/store', [category_creation::class, 'store'])->name('category.store');
Route::get('/category/edit/{id}', [category_creation::class, 'edit'])->name('category.edit');
Route::post('/category/update/{id}', [category_creation::class, 'update'])->name('category.update');
Route::delete('/category/delete/{id}', [category_creation::class, 'destroy'])->name('category.delete');


Route::get('/academic_year_creation_list', [academic_year_creation::class, 'index'])->name('academic_year_creation_list');
Route::get('/academic_year_creation_add', [academic_year_creation::class, 'add'])->name('academic_year_creation_add');


Route::get('/standard_list', [standard_creation::class, 'index'])->name('standard_list');
Route::get('/standard_creation_add', [standard_creation::class, 'add'])->name('standard_creation_add');
Route::post('/standard_creation/store', [standard_creation::class, 'store'])->name('standard_creation.store');
Route::get('/standard_creation/edit/{id}', [standard_creation::class, 'edit'])->name('standard_creation.edit');
Route::post('/standard_creation/update/{id}', [standard_creation::class, 'update'])->name('standard_creation.update');
Route::delete('/standard_creation/delete/{id}', [standard_creation::class, 'destroy'])->name('standard_creation.delete');



Route::get('/category_controller_user_list', [category_controller_user::class, 'index'])->name('category_controller_user_list');
Route::get('/category_controller_user_add', [category_controller_user::class, 'add'])->name('category_controller_user_add');


Route::get('/category_user_assign_list', [category_user_assign::class, 'index'])->name('category_user_assign_list');
Route::get('/category_user_assign_add', [category_user_assign::class, 'add'])->name('category_user_assign_add');


Route::post('/logout', [LoginBasic::class, 'logout'])->name('logout');


// layout
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

// pages
Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

// cards
Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');

// User Interface
Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

// extended ui
Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');

// icons
Route::get('/icons/boxicons', [Boxicons::class, 'index'])->name('icons-boxicons');

// form elements
Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');

// form layouts
Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');

// tables
Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');