<?php

use App\Http\Controllers\academic_year_creation;
use App\Http\Controllers\AccessGateController;
use App\Http\Controllers\accountactivateController;
use App\Http\Controllers\AdharGetControler;
use App\Http\Controllers\afternoon_report;
use App\Http\Controllers\AssignCargo;
use App\Http\Controllers\attendance_report;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\GateUserLoginController;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\PassUserLoginController;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\biometricAttendanceController;
use App\Http\Controllers\biometric_creation;
use App\Http\Controllers\biometric_employee;
use App\Http\Controllers\breakfast_report;
use App\Http\Controllers\CardRequestForPreOwnedController;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\CargoAuthenticationController;
use App\Http\Controllers\cargoController;
use App\Http\Controllers\CargoCreationController;
use App\Http\Controllers\cargoDetailsController;
use App\Http\Controllers\cargo_admissionController;
use App\Http\Controllers\cargo_handlingController;
use App\Http\Controllers\category_controller_user;
use App\Http\Controllers\category_creation;
use App\Http\Controllers\category_user_assign;
use App\Http\Controllers\center_name_creation;
use App\Http\Controllers\CompanyCreationController;
use App\Http\Controllers\ComplaintTypeController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\customs_permission_entryController;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\CenterAnalytics;
use App\Http\Controllers\dashboard\GateAnalytics;
use App\Http\Controllers\departmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\district_creation;
use App\Http\Controllers\DocumentMasterController;
use App\Http\Controllers\EmployeeAuthenticationController;
use App\Http\Controllers\EmployeeDesignationController;
use App\Http\Controllers\EmployeeRegistrationController;
use App\Http\Controllers\Employee_renewal;
use App\Http\Controllers\EquipmentComplaintApproval;
use App\Http\Controllers\EquipmentComplaintRegistration;

// customize UI
// use App\Http\Controllers\ui\district;
// use App\Http\Controllers\ZoneController;
use App\Http\Controllers\EquipmentComplaintRelease;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\Firmauthentication;
// use App\Http\Controllers\biometric_device;
use App\Http\Controllers\firmsGetControler;
use App\Http\Controllers\FirmsRegistrationController;
use App\Http\Controllers\FirmUserComplaintApproval;
use App\Http\Controllers\FirmUserComplaintRegistration;
use App\Http\Controllers\FirmUserComplaintRelease;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\HarbourPermitCategoryController;
use App\Http\Controllers\icons\Boxicons;
use App\Http\Controllers\importExportApplicationIssueController;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\LicenseGetControler;
use App\Http\Controllers\MainUserScreenController;
use App\Http\Controllers\MaterialComplaintApproval;
use App\Http\Controllers\MaterialComplaintRegistration;
use App\Http\Controllers\MaterialComplaintRelease;
use App\Http\Controllers\meal_report;
use App\Http\Controllers\monthly_af_report;
use App\Http\Controllers\monthly_bf_report;
use App\Http\Controllers\monthly_fn_report;
use App\Http\Controllers\monthly_meal_report;
use App\Http\Controllers\OfficeAuthorization;
use App\Http\Controllers\OnlineVehicleAuthentication;
use App\Http\Controllers\OnlineVehicleRegistrationController;
use App\Http\Controllers\OnlineVehicleRenewalController;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
// use App\Http\Controllers\CargoCreationController;
use App\Http\Controllers\passEntryController;
use App\Http\Controllers\PassExtendController;
use App\Http\Controllers\PassRequestFromAgentController;
// use App\Http\Controllers\CardRequestForPreOwnedController;

use App\Http\Controllers\paymentwalletController;
use App\Http\Controllers\PersonAuthenticationController;
use App\Http\Controllers\PersonComplaintApproval;
use App\Http\Controllers\PersonComplaintRegistration;
use App\Http\Controllers\PersonComplaintRelease;
use App\Http\Controllers\PersonPenalityController;
use App\Http\Controllers\PersonRegistration;
use App\Http\Controllers\PersonTypeController;
use App\Http\Controllers\person_accessController;
use App\Http\Controllers\PortAreaAccessController;
use App\Http\Controllers\PortVehicleAuthentication;
use App\Http\Controllers\PortVehicleRegistration;
use App\Http\Controllers\PortVehicleRegistrationController;
use App\Http\Controllers\port_permit_validation_with_fee_controller;
use App\Http\Controllers\RateMasterApproval;
use App\Http\Controllers\RateMasterController;
use App\Http\Controllers\rfidissueController;
use App\Http\Controllers\rfid_id_issueController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\school_report;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\staff_report;
use App\Http\Controllers\standard_creation;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\student_report;
use App\Http\Controllers\TalukCreationController;
use App\Http\Controllers\UserAuthentication;
use App\Http\Controllers\UserCreationController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\UserRegistration;
use App\Http\Controllers\UserRenewal;
use App\Http\COntrollers\UserScreenController;
use App\Http\Controllers\UserTypeController;
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
use App\Http\Controllers\VehGetControler;
use App\Http\Controllers\VehicalAuthenticationController;
use App\Http\Controllers\VehicleComplaintApproval;
use App\Http\Controllers\VehicleComplaintRegistration;
use App\Http\Controllers\VehicleComplaintRegistrationController;
use App\Http\Controllers\VehicleComplaintRelease;
use App\Http\Controllers\VehicleCreationController;
use App\Http\Controllers\VehiclePenalityController;
use App\Http\Controllers\VehicleRegistrationController;
use App\Http\Controllers\VehicleRenewalController;
use App\Http\Controllers\vehicle_accessController;
use App\Http\Controllers\ViewPassExtendController;
use App\Http\Controllers\weighment_entryController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\vehicleTypeGetControler;
use App\Http\Controllers\GateAccessInController;



use Illuminate\Support\Facades\Route;

// Main Page Route
// Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::get('/', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('home', [LoginBasic::class, 'index'])->name('home.index');

// Route::resource('login', LoginBasic::class);
Route::get('/login', [LoginBasic::class, 'store'])->name('login');

Route::get('/userlogin', [PassUserLoginController::class, 'store'])->name('userlogin');
Route::get('PassUser', [PassUserLoginController::class, 'index'])->name('PassUser.index');

Route::get('GateUser', [GateUserLoginController::class, 'index'])->name('GateUser.index');
Route::get('/gatelogin', [GateUserLoginController::class, 'store'])->name('gatelogin');

Route::resource('dashboard', Analytics::class);
Route::resource('Centerdashboard', CenterAnalytics::class);
Route::resource('Gatedashboard', GateAnalytics::class);

Route::get('biometric_attandance_list', [biometricAttendanceController::class, 'index'])->name('biometric_attendance.index');
Route::post('biometric_attandance_list', [biometricAttendanceController::class, 'punch'])->name('biometric_attendance.punch');

// Route::get('/dashboard', [Analytics::class, 'index'])->name('dashboard-analytics');

Route::get('/district', [district_creation::class, 'index'])->name('district');
Route::get('/district_add', [district_creation::class, 'add'])->name('add_form');
Route::post('/district/store', [district_creation::class, 'store'])->name('district.store');
// Route::get('/get-District', [district_creation::class, 'getDistrict'])->name('district.getDistrict');
Route::get('/district/edit{id}', [district_creation::class, 'edit'])->name('district.edit');
Route::post('/district/update{id}', [district_creation::class, 'update'])->name('district.update');
Route::delete('/district/delete/{id}', [district_creation::class, 'destroy'])->name('district.delete');
Route::get('/zones', [district_creation::class, 'getzone'])->name('zones');
// Route::get('/zone_add', [ZoneController::class, 'create'])->name('add_form');

Route::get('/zone', [ZoneController::class, 'index'])->name('zone.index');
Route::get('/zones/data', [ZoneController::class, 'getData'])->name('zone.data');
Route::get('/zone_add', [ZoneController::class, 'create'])->name('zone.create');
Route::post('/zone', [ZoneController::class, 'store'])->name('zone.store');
Route::get('/zone/{id}/edit', [ZoneController::class, 'edit'])->name('zone.edit');
Route::put('/zone/{id}', [ZoneController::class, 'update'])->name('zone.update');
Route::delete('/zone/{id}', [ZoneController::class, 'destroy'])->name('zone.destroy');

Route::resource('student', StudentController::class);
Route::get('/student/{student}', [StudentController::class, 'show'])->name('student.show');

// Route::get('school', [SchoolController::class, 'index'])->name('school.index');
// Route::get('school/create', [SchoolController::class, 'create'])->name('school.create');
// Route::post('school', [SchoolController::class, 'store'])->name('school.store'); // Optional
// Route::get('school/{school}/edit', [SchoolController::class, 'edit'])->name('school.edit');
// Route::put('school/{school}', [SchoolController::class, 'update'])->name('school.update');
// Route::delete('school/{school}', [SchoolController::class, 'destroy'])->name('school.destroy');
// Route::get('school/details', [SchoolController::class, 'getdetails'])->name('school.details');

Route::get('school/index', [SchoolController::class, 'index'])->name('school.index');
Route::get('school/create', [SchoolController::class, 'create'])->name('school.create');
Route::post('school/store', [SchoolController::class, 'store'])->name('school.store');
Route::get('school/{school}/edit', [SchoolController::class, 'edit'])->name('school.edit');
Route::post('school/update/{id}', [SchoolController::class, 'update'])->name('school.update');
Route::delete('school/{school}', [SchoolController::class, 'destroy'])->name('school.destroy');
Route::get('school/details', [SchoolController::class, 'getdetails'])->name('school.details');
Route::get('/get_district', [SchoolController::class, 'get_district'])->name('get_district');
Route::get('/get_taluk', [SchoolController::class, 'get_taluk'])->name('get_taluk');

// Route::get('school', [SchoolController::class, 'index'])->name('school.index');
// Route::get('school/create', [SchoolController::class, 'create'])->name('school.create');
// Route::post('school/store', [SchoolController::class, 'store'])->name('school.store'); // Optional
// Route::get('school/{school}/edit', [SchoolController::class, 'edit'])->name('school.edit');
// Route::put('school/{school}', [SchoolController::class, 'update'])->name('school.update');
// Route::delete('school/{school}', [SchoolController::class, 'destroy'])->name('school.destroy');
// Route::get('school/details', [SchoolController::class, 'getdetails'])->name('school.details');

Route::get('/designation', [DesignationController::class, 'index'])->name('designation.index');
Route::get('/designation_add', [DesignationController::class, 'create'])->name('designation.add'); // Change POST to GET
Route::post('/designation.store', [DesignationController::class, 'store'])->name('designation.store');
Route::get('/designation.edit/{id}', [DesignationController::class, 'edit'])->name('designation.edit');
Route::put('/designation.update/{id}', [DesignationController::class, 'update'])->name('designation.update');
Route::delete('/designation.destroy/{id}', [DesignationController::class, 'destroy'])->name('designation.destroy');
Route::get('/designation/data', [DesignationController::class, 'getData'])->name('designation.data');

Route::resource('staff', StaffController::class);

// Alternatively, you can use these explicit routes
Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
Route::post('/staff/store', [StaffController::class, 'store'])->name('staff.store');
Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
Route::post('/staff/update/{id}', [StaffController::class, 'update'])->name('staff.update');
Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
Route::get('/staff/details', [StaffController::class, 'getdetail'])->name('staff.details');
Route::get('/staff/data', [StaffController::class, 'getData'])->name('staff.data');
Route::get('/get_districts', [StaffController::class, 'get_districts'])->name('get_districts');
Route::get('/get_taluk', [StaffController::class, 'get_taluk'])->name('get_taluk');
Route::get('/get_schools', [StaffController::class, 'get_schools'])->name('get_schools');

Route::get('/usertype', [UserTypeController::class, 'index'])->name('usertype.index');
Route::get('/usertypes/data', [UserTypeController::class, 'getData'])->name('usertype.data');
Route::get('/usertype_add', [UserTypeController::class, 'create'])->name('usertype.create');
Route::post('/usertype', [UserTypeController::class, 'store'])->name('usertype.store');
Route::get('/usertype/{id}/edit', [UserTypeController::class, 'edit'])->name('usertype.edit');
Route::put('/usertype/{id}', [UserTypeController::class, 'update'])->name('usertype.update');
Route::delete('/usertype/{id}', [UserTypeController::class, 'destroy'])->name('usertype.destroy');

Route::get('/mainuserscreen', [MainUserScreenController::class, 'index'])->name('mainuserscreen.index');
Route::get('/mainuserscreens/data', [MainUserScreenController::class, 'getData'])->name('mainuserscreen.data');
Route::get('/mainuserscreen_add', [MainUserScreenController::class, 'create'])->name('mainuserscreen.create');
Route::post('/mainuserscreen', [MainUserScreenController::class, 'store'])->name('mainuserscreen.store');
Route::get('/mainuserscreen/{id}/edit', [MainUserScreenController::class, 'edit'])->name('mainuserscreen.edit');
Route::put('/mainuserscreen/{id}', [MainUserScreenController::class, 'update'])->name('mainuserscreen.update');
Route::delete('/mainuserscreen/{id}', [MainUserScreenController::class, 'destroy'])->name('mainuserscreen.destroy');
// web.php
Route::get('/get-screen-types', [MainUserScreenController::class, 'getScreenTypes'])->name('get.screen.types');

Route::get('/userscreen', [UserScreenController::class, 'index'])->name('userscreen.index');
Route::get('/userscreens/data', [UserScreenController::class, 'getData'])->name('userscreen.data');
Route::get('/userscreen_add', [UserScreenController::class, 'create'])->name('userscreen.create');
Route::post('/userscreen', [UserScreenController::class, 'store'])->name('userscreen.store');
Route::get('/userscreen/{id}/edit', [UserScreenController::class, 'edit'])->name('userscreen.edit');
Route::put('/userscreen/{id}', [UserScreenController::class, 'update'])->name('userscreen.update');
Route::delete('/userscreen/{id}', [UserScreenController::class, 'destroy'])->name('userscreen.destroy');

Route::get('/usercreation', [UserCreationController::class, 'index'])->name('usercreation.index');
Route::get('/usercreations/data', [UserCreationController::class, 'getData'])->name('usercreation.data');
Route::get('/usercreation_add', [UserCreationController::class, 'create'])->name('usercreation.create');
Route::post('/usercreation', [UserCreationController::class, 'store'])->name('usercreation.store');
Route::get('/usercreation/{id}/edit', [UserCreationController::class, 'edit'])->name('usercreation.edit');
Route::put('/usercreation/{id}', [UserCreationController::class, 'update'])->name('usercreation.update');
Route::delete('/usercreation/{id}', [UserCreationController::class, 'destroy'])->name('usercreation.destroy');

Route::get('/userpermission', [UserPermissionController::class, 'index'])->name('user_permission.index');
// ROute::get('/getuidata',[UserPermissionController::class,'getuidata'])->name('userpermission.data');
Route::get('/getdata', [UserPermissionController::class, 'getdata'])->name('userpermission.getdata');
Route::get('/userpermission_add', [UserPermissionController::class, 'create'])->name('userpermission.create');
Route::post('/userpermission', [UserPermissionController::class, 'store'])->name('userpermission.store');
// Route::get('/userpermission/{id}/edit', [UserPermissionController::class, 'edit'])->name('userpermission.edit');
Route::get('userpermission/edit/{uniqueIds}', [UserPermissionController::class, 'edit'])->name('userpermission.edit');

Route::put('/userpermission/{id}', [UserPermissionController::class, 'update'])->name('userpermission.update');
Route::delete('/userpermission/{id}', [UserPermissionController::class, 'destroy'])->name('userpermission.destroy');

// complaints
Route::get('/complaint_type_list', [ComplaintTypeController::class, 'index'])->name('complaint_type.index');
// Route::get( '/complaint_types/data', [ ComplaintTypeController::class, 'getData' ] )->name( 'complaint_type.data' );
Route::post('/complaint_types/data', [ComplaintTypeController::class, 'getData'])->name('complaint_type.data');

Route::get('/complaint_type_add', [ComplaintTypeController::class, 'create'])->name('complaint_type.create');
Route::post('/complaint_type', [ComplaintTypeController::class, 'store'])->name('complaint_type.store');
Route::get('/complaint_type/{id}/edit', [ComplaintTypeController::class, 'edit'])->name('complaint_type.edit');
Route::put('/complaint_type/{id}', [ComplaintTypeController::class, 'update'])->name('complaint_type.update');
Route::delete('/complaint_type/{id}', [ComplaintTypeController::class, 'destroy'])->name('complaint_type.destroy');

// vehicle_complaint_registration
Route::get('/vehicle_complaint_registration_list', [VehicleComplaintRegistrationController::class, 'index'])->name('vehicle_complaint_registration_list');
Route::get('/vehicle_complaint_registration_add', [VehicleComplaintRegistrationController::class, 'add'])->name('vehicle_complaint_registration_add');
Route::post('/vehicle_complaint_registration_store', [VehicleComplaintRegistrationController::class, 'store'])->name('vehicle_complaint_registration.store');
Route::get('/vehicle_complaint_registration/edit/{id}', [VehicleComplaintRegistrationController::class, 'edit'])->name('vehicle_complaint_registration.edit');
Route::put('/vehicle_complaint_registration/update/{id}', [VehicleComplaintRegistrationController::class, 'update'])->name('vehicle_complaint_registration.update');
Route::delete('/vehicle_complaint_registration/delete/{id}', [VehicleComplaintRegistrationController::class, 'destroy'])->name('vehicle_complaint_registration.delete');




Route::get('/access_gate_in', [GateAccessInController::class, 'index'])->name('access_gate_in.index');
Route::get('/access_gate_out', [GateAccessInController::class, 'create'])->name('access_gate_out.create');

// Route::get('getuidata', [permissionUiController::class, 'index'])->name('getuidata');
// Route::get('updategetuidata', [userPermissionController::class, 'index'])->name('updategetuidata');

// Route::resource('staff', StaffController::class);

// Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
// Route::post('/staff/store', [StaffController::class, 'store'])->name('staff.store');
// Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
// Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
// // Route::post('/staff/update', [StaffController::class, 'update'])->name('staff.update');

// Route::put('staff/{staff}/update', [StaffController::class, 'update'])->name('staff.update');
// Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
// Route::get('/staff/details', [StaffController::class, 'getdetail'])->name('staff.details');

// // Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
// Route::middleware(['web'])->group(function () {
//     Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
// });

// Route::get('/staff/data', [StaffController::class, 'getData'])->name('staff.data');

Route::get('/biometric_list', [biometric_creation::class, 'index'])->name('biometric_list');
Route::get('/biometric_add', [biometric_creation::class, 'add'])->name('biometric_add');
Route::post('/biometric/store', [biometric_creation::class, 'store'])->name('biometric.store');
Route::get('/biometric/edit/{id}', [biometric_creation::class, 'edit'])->name('biometric.edit');
Route::post('/biometric/update/{id}', [biometric_creation::class, 'update'])->name('biometric.update');
Route::delete('/biometric/delete/{id}', [biometric_creation::class, 'destroy'])->name('biometric.delete');
Route::get('/districts', [biometric_creation::class, 'getDistricts'])->name('districts');
Route::get('/biometric_emp_list', [biometric_employee::class, 'index'])->name('biometric_emp_list');
Route::get('/zone_district', [biometric_creation::class, 'zone_district'])->name('zone_district');
Route::get('/taluk', [biometric_creation::class, 'taluk'])->name('taluk');
Route::get('/school', [biometric_creation::class, 'school'])->name('school');

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
Route::get('/fetch-districts', [TalukCreationController::class, 'fetchDistricts'])->name('fetch_districts');

Route::get('/category_list', [category_creation::class, 'index'])->name('category_list');
Route::get('/category_add', [category_creation::class, 'add'])->name('category_add');
Route::post('/category/store', [category_creation::class, 'store'])->name('category.store');
Route::get('/category/edit/{id}', [category_creation::class, 'edit'])->name('category.edit');
Route::post('/category/update/{id}', [category_creation::class, 'update'])->name('category.update');
Route::delete('/category/delete/{id}', [category_creation::class, 'destroy'])->name('category.delete');

Route::get('/center_name_list', [center_name_creation::class, 'index'])->name('center_name_list');
Route::get('/center_name_add', [center_name_creation::class, 'add'])->name('center_name_add');
Route::post('/center_name_store', [center_name_creation::class, 'store'])->name('center_name.store');
Route::get('/center_name/edit/{id}', [center_name_creation::class, 'edit'])->name('center_name.edit');
Route::put('/center_name/update/{id}', [center_name_creation::class, 'update'])->name('center_name.update');
Route::delete('/center_name/delete/{id}', [center_name_creation::class, 'destroy'])->name('center_name.delete');

Route::get('/firms_registration_list', [FirmsRegistrationController::class, 'index'])->name('firms_registration_list');
Route::get('/firms_registration_add', [FirmsRegistrationController::class, 'add'])->name('firms_registration_add');
Route::get('/firms_registration', [FirmsRegistrationController::class, 'index'])->name('firms_registration');
Route::get('/get-last-firm', [FirmsRegistrationController::class, 'getLastFirmNumber']);
Route::post('/firms_sublist', [FirmsRegistrationController::class, 'sublist'])->name('firms_sublist.create');
// Route::post('/firms_registration_add', [FirmsRegistrationController::class, 'store'])->name('firms_registration.store');
Route::post('/firms_registration', [FirmsRegistrationController::class, 'store'])->name('firms_registration.store');
Route::get('/firms_registration/{unique_id}/edit', [FirmsRegistrationController::class, 'edit'])->name('firms_registration.edit');
Route::delete('/firms_registration_destroy/{id}', [FirmsRegistrationController::class, 'destroy'])->name('firms_registration.destroy');
Route::put('/firms_registration/{id}', [FirmsRegistrationController::class, 'update'])->name('firms_registration.update');
Route::post('/firms_authentication', [FirmsRegistrationController::class, 'authentication'])->name('firms_authentication.store');
Route::get('/sublist/{form_unique_id}/edit', [FirmsRegistrationController::class, 'sublist_edit'])->name('sublist.edit');
Route::post('/sublist/delete/{form_unique_id}', [FirmsRegistrationController::class, 'deleteSublistItem'])->name('sublist.delete');
Route::put('/firms/{id}', [FirmController::class, 'update'])->name('firms.update');
Route::put('/firms/update-authentication/{id}', [FirmsController::class, 'updateAuthentication'])->name('firms.update_authentication');
Route::post('/firms_authentication/{id}', [FirmsRegistrationController::class, 'authentication_update'])->name('firms_authentication.update');

Route::get('/academic_year_creation_list', [academic_year_creation::class, 'index'])->name('academic_year_creation_list');
Route::get('/academic_year_creation_add', [academic_year_creation::class, 'add'])->name('academic_year_creation_add');
Route::post('/academic_year_creation_store', [academic_year_creation::class, 'store'])->name('academic_year_creation.store');
Route::get('/academic_year_creation/{id}/edit', [academic_year_creation::class, 'edit'])->name('academic_year_creation.edit');
Route::delete('/academic_year_creation/{id}', [academic_year_creation::class, 'destroy'])->name('academic_year_creation.destroy');
Route::put('/academic_year_creation/{id}', [academic_year_creation::class, 'update'])->name('academic_year_creation.update');

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
// Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');

Route::get('/employee_designation', [EmployeeDesignationController::class, 'index'])->name('employee_designation');
Route::get('/employee_designation_add', [EmployeeDesignationController::class, 'add'])->name('employee_designation_add');
Route::post('/employee_designation_store', [EmployeeDesignationController::class, 'store'])->name('employee_designation_store');
Route::put('/employee_designation/{id}', [EmployeeDesignationController::class, 'update'])->name('employee_designation_update');
Route::delete('/employee_designation_destroy/{id}', [EmployeeDesignationController::class, 'destroy'])->name('employee_designation_destroy');
Route::get('/employee_designation/{id}/edit', [EmployeeDesignationController::class, 'edit'])->name('employee_designation_edit');

Route::get('/cargo_creation', [CargoCreationController::class, 'index'])->name('cargo_creation');
Route::get('/cargo_creation_add', [CargoCreationController::class, 'add'])->name('cargo_creation_add');
Route::post('/cargo_creation_store', [CargoCreationController::class, 'store'])->name('cargo_creation_store');
Route::put('/cargo_creation/{id}', [CargoCreationController::class, 'update'])->name('cargo_creation_update');
Route::delete('/cargo_creation_destroy/{id}', [CargoCreationController::class, 'destroy'])->name('cargo_creation_destroy');
Route::get('/cargo_creation/{id}/edit', [CargoCreationController::class, 'edit'])->name('cargo_creation_edit');

Route::get('/person_type', [PersonTypeController::class, 'index'])->name('person_type');
Route::get('/person_type_add', [PersonTypeController::class, 'add'])->name('person_type_add');
Route::post('/person_type_store', [PersonTypeController::class, 'store'])->name('person_type_store');
Route::put('/person_type/{id}', [PersonTypeController::class, 'update'])->name('person_type_update');
Route::delete('/person_type_destroy/{id}', [PersonTypeController::class, 'destroy'])->name('person_type_destroy');
Route::get('/person_type/{id}/edit', [PersonTypeController::class, 'edit'])->name('person_type_edit');

Route::get('/document_master', [DocumentMasterController::class, 'index'])->name('document_master');
Route::get('/document_master_add', [DocumentMasterController::class, 'add'])->name('document_master_add');
Route::post('/document_master_store', [DocumentMasterController::class, 'store'])->name('document_master_store');
Route::put('/document_master/{id}', [DocumentMasterController::class, 'update'])->name('document_master_update');
Route::delete('/document_master_destroy/{id}', [DocumentMasterController::class, 'destroy'])->name('document_master_destroy');
Route::get('/document_master/{id}/edit', [DocumentMasterController::class, 'edit'])->name('document_master_edit');

Route::get('/user_renewal', [UserRenewal::class, 'index'])->name('user_renewal');

Route::get('/employee_renewal', [Employee_renewal::class, 'index'])->name('employee_renewal');

Route::get('/person_registration', [PersonRegistration::class, 'index'])->name('person_registration');
Route::get('/person_registration_add', [PersonRegistration::class, 'add'])->name('person_registration_add');
Route::get('/person_registration_data', [PersonRegistration::class, 'getdata'])->name('person_registration_data');
Route::post('/person_registration_store', [PersonRegistration::class, 'store'])->name('person_registration.store');
Route::get('/person_registration/{id}/edit', [PersonRegistration::class, 'edit'])->name('person_registration.edit');
Route::post('/person_registration/{id}', [PersonRegistration::class, 'update'])->name('person_registration.update');
Route::post('/person_authentication/{id}', [PersonRegistration::class, 'authentication_update'])->name('person_authentication.update');

Route::delete('/person_registration_destroy/{id}', [PersonRegistration::class, 'destroy'])->name('person_registration.destroy');

Route::get('/get-last-person-number', [PersonRegistration::class, 'getLastregpersonNumber']);

Route::post('/person_sublist', [PersonRegistration::class, 'sublist'])->name('person_sublist.create');
Route::post('/person_authentication', [PersonRegistration::class, 'authentication'])->name('person_authentication.store');

// Route for editing a sublist item
Route::get('/sublist/{form_unique_id}/edit', [PersonRegistration::class, 'sublist_edit'])->name('sublist.edit');

// Route for deleting a sublist item
Route::post('/sublist/delete/{form_unique_id}', [PersonRegistration::class, 'deleteSublistItem'])->name('sublist.delete');

Route::get('/get-person-sublist-data', [PersonRegistration::class, 'getPersonSublistData'])->name('get_person_sublist_data');

Route::get('/user_registration', [UserRegistration::class, 'index'])->name('user_registration');
Route::get('/user_registration_add', [UserRegistration::class, 'add'])->name('user_registration_add');
Route::get('/user_registration_data', [UserRegistration::class, 'getdata'])->name('user_registration_data');

// cargo
Route::get('/new_cargo', [cargoController::class, 'index'])->name('new_cargo');
Route::get('/new_cargo_add', [cargoController::class, 'create'])->name('new_cargo_add');
Route::post('/new_cargo_store', [cargoController::class, 'store'])->name('cargo_ei.store');
Route::get('/new_cargo_data', [cargoController::class, 'getdata'])->name('cargo_ei.data');
Route::get('/new_cargo/edit/{unique_id}', [cargoController::class, 'edit'])->name('cargo_ei.edit');
Route::put('/new_cargo/{id}', [cargoController::class, 'update'])->name('cargo_ei.update');

// cargo creation

//vehical creation
Route::get('/vehicle_creation', [VehicleCreationController::class, 'index'])->name('vehicle_creation');
Route::get('/vehicle_creation_add', [VehicleCreationController::class, 'create'])->name('vehicle_creation_add');
Route::post('/vehicle_creation_store', [VehicleCreationController::class, 'store'])->name('vehicle_creation.store');
Route::get('/vehicle_creation/edit/{id}', [VehicleCreationController::class, 'edit'])->name('vehicle_creation.edit');
Route::post('/vehicle_creation/{id}', [VehicleCreationController::class, 'update'])->name('vehicle_creation.update');
Route::post('/vehicle_creation/delete/{id}', [VehicleCreationController::class, 'destroy'])->name('vehicle_creation.delete');

// container
Route::get('/container_list', [ContainerController::class, 'index'])->name('container_list');
Route::get('/new_container_add', [ContainerController::class, 'create'])->name('new_container_add');

// department
Route::get('/department', [departmentController::class, 'index'])->name('department');
Route::get('/department_add', [departmentController::class, 'create'])->name('department_add');
Route::post('/department/store', [departmentController::class, 'store'])->name('department.store');
Route::get('/department/edit/{id}', [departmentController::class, 'edit'])->name('department.edit');
Route::post('/department/{id}', [departmentController::class, 'update'])->name('department.update');
Route::post('/department/delete/{id}', [departmentController::class, 'destroy'])->name('department.delete');

// vechicle Registartion
Route::get('/vehicle_registration', [VehicleRegistrationController::class, 'index'])->name('vehicle_registration.index');
Route::get('/vehicle_registration_add', [VehicleRegistrationController::class, 'create'])->name('vehicle_registration_add');
Route::post('/vehicle_registration/store_1', [VehicleRegistrationController::class, 'stores'])->name('vehicle_registration.store_1');
Route::post('/vehicle_registration/store', [VehicleRegistrationController::class, 'store'])->name('vehicle_registration.store');
Route::get('/vehicle_registration/{id}', [VehicleRegistrationController::class, 'edit'])->name('vehicle_registration.edit');
Route::post('/vehicle_registration/{id}', [VehicleRegistrationController::class, 'update'])->name('vehicle_registration.update');
Route::delete('vehicle_registration/delete/{id}', [VehicleRegistrationController::class, 'destroy'])->name('vehicle_registration.destroy');
// Route::get('/vehicles', [VehicleRegistrationController::class, 'listVehicleDetails']);
Route::post('/vehicle_registration_sub_store', [VehicleRegistrationController::class, 'sub_store'])->name('vehicle_registration_sub_store');
Route::put('/vehicle_registration_document/{id}', [VehicleRegistrationController::class, 'markAsDeleted'])->name('vehicle_registration.markAsDeleted');
Route::get('/get-vehicle-category/{vehicleTypeId}', [VehicleRegistrationController::class, 'getVehicleCategory'])->name('get-vehicle-category');
Route::get('/VehicleReistrationSublist', [VehicleRegistrationController::class, 'sublistindex'])->name('VehicleReistrationSublist.index');

// port_permit_validation_with_fee_add
Route::get('/port_permit_validation_with_fee', [port_permit_validation_with_fee_controller::class, 'index'])->name('port_permit_validation_with_fee');
Route::get('/port_permit_validation_with_fee_add', [port_permit_validation_with_fee_controller::class, 'add'])->name('port_permit_validation_with_fee_add');

// port_permit_validation_with_fee_add
Route::get('/company_creation', [CompanyCreationController::class, 'index'])->name('company_creation.index');
Route::get('/company_creation_add', [CompanyCreationController::class, 'add'])->name('company_creation_add');
Route::post('/company_creation', [CompanyCreationController::class, 'store'])->name('company_creation.store');
Route::get('/company_creation/{id}/edit', [CompanyCreationController::class, 'edit'])->name('company_creation.edit');
// Route::delete('/company_creation/{id}', [CompanyCreationController::class, 'destroy'])->name('company_creation.destroy');
// Route::put('/company_creation/{id}', [CompanyCreationController::class, 'update'])->name('company_creation.update');
Route::get('/company_creation/getData', [CompanyCreationController::class, 'getData'])->name('company_creation.getData');
Route::put('/company_creation/{id}', [CompanyCreationController::class, 'update'])->name('company_creation.update');
// Route::delete('/company_creation/{id}', [CompanyCreationController::class, 'destroy'])->name('company_creation.destroy');
Route::delete('/company_creation/{id}', [CompanyCreationController::class, 'destroy'])->name('company_creation.destroy');

//Harbour Permit Category
Route::get('/harbour_permit_category', [HarbourPermitCategoryController::class, 'index'])->name('harbour_permit_category');
Route::get('/harbour_permit_category_add', [HarbourPermitCategoryController::class, 'create'])->name('harbour_permit_category_add');
Route::post('/harbour_permit_category/store', [HarbourPermitCategoryController::class, 'store'])->name('harbour_permit_category.store');
Route::get('/harbour_permit_category/edit/{id}', [HarbourPermitCategoryController::class, 'edit'])->name('harbour_permit_category.edit');
Route::post('/harbour_permit_category/{id}', [HarbourPermitCategoryController::class, 'update'])->name('harbour_permit_category.update');
Route::post('/harbour_permit_category/delete/{id}', [HarbourPermitCategoryController::class, 'destroy'])->name('harbour_permit_category.delete');

//Harbour Permit Category
// Route::get('/person_authentication', [PersonAuthenticationController::class, 'index'])->name('person_authentication');
// Route::get('/person_authentication_add', [PersonAuthenticationController::class, 'create'])->name('person_authentication_add');

Route::get('/person_authentication', [PersonAuthenticationController::class, 'index'])->name('person_authentication');
Route::get('/person_authentication_add/{id}', [PersonAuthenticationController::class, 'create'])->name('person_authentication_add');
Route::post('/person_authentication_status', [PersonAuthenticationController::class, 'status_upload'])->name('person_authentication.status');

//Harbour Permit Category
Route::get('/vehicle_authentication', [VehicalAuthenticationController::class, 'index'])->name('vehicle_authentication');
Route::get('/vehical_authentication_add/{screenUniqueId}', [VehicalAuthenticationController::class, 'create'])->name('vehical_authentication_add');
Route::post('/vehical_authentication.approval_status', [VehicalAuthenticationController::class, 'status_approval'])->name('vehical_authentication.approval_status');

//employee authentication
Route::get('/employee_authentication', [EmployeeAuthenticationController::class, 'index'])->name('employee_authentication');
Route::get('/employee_authentication_add/{id}', [EmployeeAuthenticationController::class, 'create'])->name('employee_authentication_add');
Route::post('/employee_authentication_status', [EmployeeAuthenticationController::class, 'status_upload'])->name('employee_authentication.status');

// // employee Registartion
// Route::get('/employee_registration', [EmployeeRegistrationController::class, 'index'])->name('employee_registration');
// Route::get('/employee_registration_add', [EmployeeRegistrationController::class, 'create'])->name('employee_registration_add');

// vechicle Registartion
Route::get('/online_vehicle_registration', [OnlineVehicleRegistrationController::class, 'index'])->name('online_vehicle_registration');
Route::get('/online_vehicle_registration_add', [OnlineVehicleRegistrationController::class, 'create'])->name('online_vehicle_registration_add');

// vechicle Registartion
Route::get('/online_vehicle_renewal', [OnlineVehicleRenewalController::class, 'index'])->name('online_vehicle_renewal');
Route::get('/online_vehicle_renewal_add', [OnlineVehicleRenewalController::class, 'add'])->name('online_vehicle_renewal_add');

Route::get('/port_area_access', [PortAreaAccessController::class, 'index'])->name('port_area_access');
Route::get('/port_area_access_add', [PortAreaAccessController::class, 'add'])->name('port_area_access_add');
Route::post('/port_area_access_store', [PortAreaAccessController::class, 'store'])->name('port_area_access.store');
Route::get('/port_area_access/{id}/edit', [PortAreaAccessController::class, 'edit'])->name('port_area_access.edit');
Route::delete('/port_area_access/{id}', [PortAreaAccessController::class, 'destroy'])->name('port_area_access.destroy');
Route::put('/port_area_access/{id}', [PortAreaAccessController::class, 'update'])->name('port_area_access.update');

Route::get('/person_access_center', [person_accessController::class, 'index'])->name('person_access_center');
//Route::get('/online_vehicle_renewal_add', [OnlineVehicleRenewalController::class, 'add'])->name('online_vehicle_renewal_add');

Route::get('/vehicle_access_center', [vehicle_accessController::class, 'index'])->name('vehicle_access_center');
//Route::get('/online_vehicle_renewal_add', [OnlineVehicleRenewalController::class, 'add'])->name('online_vehicle_renewal_add');

// VehicleRenewalController
Route::get('/vehicle_renewal', [VehicleRenewalController::class, 'index'])->name('vehicle_renewal');
Route::get('/vehicle_renewal_add', [VehicleRenewalController::class, 'add'])->name('vehicle_renewal_add');

Route::get('/port_vehicle_registration', [PortVehicleRegistration::class, 'index'])->name('port_vehicle_registration');
Route::get('/port_vehicle_registration_add', [PortVehicleRegistration::class, 'create'])->name('port_vehicle_registration_add');
Route::get('/port_vehicle_renewal', [PortVehicleRegistration::class, 'renewal'])->name('port_vehicle_renewal');

// paymentwalletController
Route::get('/payment_wallet', [paymentwalletController::class, 'index'])->name('payment_wallet');
Route::get('/payment_option', [paymentwalletController::class, 'add'])->name('payment_option');

// rfid_id_issues

Route::get('/rfid_id_issues', [rfid_id_issueController::class, 'index'])->name('rfid_id_issues');
Route::get('/verified_approved', [accountactivateController::class, 'index'])->name('verified_approved');
Route::get('/rfid_issues', [rfidissueController::class, 'index'])->name('rfid_issues');

Route::get('/person_penality_check', [PersonPenalityController::class, 'index'])->name('person_penality_check');
Route::get('/vehicle_penality_check', [VehiclePenalityController::class, 'index'])->name('vehicle_penality_check');
Route::get('/office_authorization', [OfficeAuthorization::class, 'index'])->name('office_authorization');

Route::get('/import_export_application', [importExportApplicationIssueController::class, 'index'])->name('import_export_application');
Route::get('/cargo_admission', [cargo_admissionController::class, 'index'])->name('cargo_admission');
Route::get('/cargo_handling', [cargo_handlingController::class, 'index'])->name('cargo_handling');

Route::get('/weighment_entry', [weighment_entryController::class, 'index'])->name('weighment_entry');
Route::get('/customs_permission_entry', [customs_permission_entryController::class, 'index'])->name('person_penality_check');
Route::get('/cargo_details', [cargoDetailsController::class, 'index'])->name('cargo_details');

//cargo
Route::get('/assign_cargo', [AssignCargo::class, 'index'])->name('assign_casrgo');

//access Gate
Route::get('/access_gate', [AccessGateController::class, 'index'])->name('access_gate.list');
Route::get('/access_gate_add', [AccessGateController::class, 'add'])->name('access_gate.add');
Route::post('access_gate', [AccessGateController::class, 'store'])->name('access_gate.store'); // Store new Access Gate
Route::get('access_gate/{id}/edit', [AccessGateController::class, 'edit'])->name('access_gate.edit'); // Show edit form
Route::put('access_gate/{id}', [AccessGateController::class, 'update'])->name('access_gate.update'); // Update existing Access Gate
Route::delete('access_gate/{id}', [AccessGateController::class, 'destroy'])->name('access_gate.destroy'); // Delete Access Gate

//RateMasterController
Route::get('/rate_master', [RateMasterController::class, 'index'])->name('rate_master');
Route::get('/rate_master_add', [RateMasterController::class, 'add'])->name('rate_master_add');
Route::post('/rate_master', [RateMasterController::class, 'store'])->name('rate_master.store');
Route::get('/rate_master/edit/{id}', [RateMasterController::class, 'edit'])->name('rate_master.edit');
Route::post('/rate_master/{id}', [RateMasterController::class, 'update'])->name('rate_master.update');
Route::post('/rate_master/delete/{id}', [RateMasterController::class, 'destroy'])->name('rate_master.delete');

// PersonType
// Route::get('/person_type', [PersonType::class, 'index'])->name('person_type');
// Route::get('/person_type_add', [PersonType::class, 'add'])->name('person_type_add');

//Document Master

//RateMsterApproval\

Route::get('/rate_master_approval', [RateMasterApproval::class, 'index'])->name('rate_master_approval');
Route::get('/rate_master_approval_add/{id}', [RateMasterApproval::class, 'add'])->name('rate_master_approval_add');
Route::post('/rate_master_approval_status', [RateMasterApproval::class, 'status'])->name('rate_master_approval.status');

//firm_authentication

Route::get('/firm_authentication', [Firmauthentication::class, 'index'])->name('firm_authentication');
Route::get('/firmauth/data', [Firmauthentication::class, 'getData'])->name('firmauth.data');
Route::get('/firmauth_view/{id}/{unique_id}', [Firmauthentication::class, 'view'])->name('firmauth.view');
Route::put('/firmauth_update/{id}', [Firmauthentication::class, 'update'])->name('firmauth.update');

// Route::get('/firm_authentication_add', [Firmauthentication::class, 'add'])->name('firm_authentication_add');

//UserAuthentication

Route::get('/user_authentication', [UserAuthentication::class, 'index'])->name('user_authentication');
Route::get('/user_authentication_add', [UserAuthentication::class, 'add'])->name('user_authentication_add');

//OnlineVehicleAuthentication

Route::get('/online_vehicle_authentication', [OnlineVehicleAuthentication::class, 'index'])->name('online_vehicle_authentication');
Route::get('/online_vehicle_authentication_add', [OnlineVehicleAuthentication::class, 'add'])->name('online_vehicle_authentication_add');

//PortVehicleAuthentication

Route::get('/port_vehicle_authentication', [PortVehicleAuthentication::class, 'index'])->name('port_vehicle_authentication');
Route::get('/port_vehicle_authentication/data', [PortVehicleAuthentication::class, 'getData'])->name('port_vehicle_authentication.data');
// Route::get('/port_vehicle_authentication_add', [PortVehicleAuthentication::class, 'add'])->name('port_vehicle_authentication_add');
Route::get('/port_vehicle_authentication_add/{id}/{screenUniqueId}', [PortVehicleAuthentication::class, 'view'])->name('port_vehicle_authentication_add');

// Route::get('/port_vehicle_authentication/{id}', [PortVehicleAuthentication::class, 'view'])->name('port_vehicle_authentication.view');
Route::get('/port_vehicle_authentication/{id}/{screenUniqueId}', [PortVehicleAuthentication::class, 'view'])->name('port_vehicle_authentication.view');
Route::put('/port_vehicle_authentication/{id}', [PortVehicleAuthentication::class, 'update'])->name('portVehicle.update');
// Route::put('/port_vehicle_authentication/{id}/{screenUniqueId}', [PortVehicleAuthentication::class, 'update'])->name('portVehicle.update');

Route::get('/pass_request_from_agent', [PassRequestFromAgentController::class, 'index'])->name('pass_request_from_agent');
Route::get('/pass_request_from', [PassRequestFromAgentController::class, 'create'])->name('pass_request_from');

Route::get('/pass_extend', [PassExtendController::class, 'index'])->name('pass_extend');
Route::get('/view_pass_extend', [ViewPassExtendController::class, 'index'])->name('view_pass_extend');
Route::get('/card_request', [CardRequestForPreOwnedController::class, 'index'])->name('card_request');
Route::get('/create', [CardRequestForPreOwnedController::class, 'store'])->name('create');

// Route::get('/pass_entry', [passEntryController::class, 'index'])->name('pass_entry');
// Route::get('/pass_entry_from', [passEntryController::class, 'create'])->name('pass_entry_from');

//personComplaintRegistration

Route::get('/person_complaint_registration', [PersonComplaintRegistration::class, 'index'])->name('person_complaint_registration');
Route::get('/person_complaint_registration_add', [PersonComplaintRegistration::class, 'add'])->name('person_complaint_registration_add');

//PersonCompplaintApproval

Route::get('/person_complaint_approval', [PersonComplaintApproval::class, 'index'])->name('person_complaint_approval');
Route::get('/person_complaint_approval_add', [PersonComplaintApproval::class, 'add'])->name('person_complaint_approval_add');

//PersonComplaintRelease
Route::get('/person_complaint_release', [PersonComplaintRelease::class, 'index'])->name('person_complaint_release');
Route::get('/person_complaint_release_add', [PersonComplaintRelease::class, 'add'])->name('person_complaint_release_add');

//VehicleComplaintRegistration

// Route::get('/vehicle_complaint_registration', [VehicleComplaintRegistration::class, 'index'])->name('vehicle_complaint_registration');
// Route::get('/vehicle_complaint_registration_add', [VehicleComplaintRegistration::class, 'add'])->name('vehicle_complaint_registrationon_complaint_release_add');

//VehicleComplaintRelease

Route::get('/vehicle_complaint_release', [VehicleComplaintRelease::class, 'index'])->name('vehicle_complaint_release');
Route::get('/vehicle_complaint_release_add', [VehicleComplaintRelease::class, 'add'])->name('vehicle_complaint_release_add');

//VehicleComplaintApproval

Route::get('/vehicle_complaint_approval', [VehicleComplaintApproval::class, 'index'])->name('vehicle_complaint_approval');
Route::get('/vehicle_complaint_approval_add', [VehicleComplaintApproval::class, 'add'])->name('vehicle_complaint_approval_add');

//FirmUserComplaintRegistration
Route::get('/firm_user_complaint_registration', [FirmUserComplaintRegistration::class, 'index'])->name('firm_user_complaint_registration');
Route::get('/firm_user_complaint_registration_add', [FirmUserComplaintRegistration::class, 'add'])->name('firm_user_complaint_registration_add');

//FirmUserComplaintApproval
Route::get('/firm_user_complaint_approval', [FirmUserComplaintApproval::class, 'index'])->name('firm_user_complaint_approval');
Route::get('/firm_user_complaint_approval_add', [FirmUserComplaintApproval::class, 'add'])->name('firm_user_complaint_approval_add');

//FirmUserComplaintRelease
Route::get('/firm_user_complaint_release', [FirmUserComplaintRelease::class, 'index'])->name('firm_user_complaint_release');
Route::get('/firm_user_complaint_release_add', [FirmUserComplaintRelease::class, 'add'])->name('firm_user_complaint_release_add');

//EquipmentComplaintRegistration

Route::get('/equipment_complaint_registration', [EquipmentComplaintRegistration::class, 'index'])->name('equipment_complaint_registration');
Route::get('/equipment_complaint_registration_add', [EquipmentComplaintRegistration::class, 'add'])->name('equipment_complaint_registration_add');

//EquipmentComplaintApproval

Route::get('/equipment_complaint_approval', [EquipmentComplaintApproval::class, 'index'])->name('equipment_complaint_approval');
Route::get('/equipment_complaint_approval_add', [EquipmentComplaintApproval::class, 'add'])->name('equipment_complaint_approval_add');

//EquipmentComplaintRelease

Route::get('/equipment_complaint_release', [EquipmentComplaintRelease::class, 'index'])->name('equipment_complaint_release');
Route::get('/equipment_complaint_release_add', [EquipmentComplaintRelease::class, 'add'])->name('equipment_complaint_release_add');

//MaterialComplaintApproval
Route::get('/material_complaint_approval', [MaterialComplaintApproval::class, 'index'])->name('material_complaint_approval');
Route::get('/material_complaint_approval_add', [MaterialComplaintApproval::class, 'add'])->name('material_complaint_approval_add');

//MaterialComplaintRegistration
Route::get('/material_complaint_registration', [MaterialComplaintRegistration::class, 'index'])->name('material_complaint_registration');
Route::get('/material_complaint_registration_add', [MaterialComplaintRegistration::class, 'add'])->name('material_complaint_registration_add');

//MaterialComplaintRelease
Route::get('/material_complaint_release', [MaterialComplaintRelease::class, 'index'])->name('material_complaint_release');
Route::get('/material_complaint_release_add', [MaterialComplaintRelease::class, 'add'])->name('material_complaint_release_add');

//Employee Registration
Route::get('/employee_registration', [EmployeeRegistrationController::class, 'index'])->name('employee_registration');
Route::get('/employee_registration_add', [EmployeeRegistrationController::class, 'add'])->name('employee_registration_add');
Route::post('/employee_registration_store', [EmployeeRegistrationController::class, 'store'])->name('employee_registration_store');
Route::post('/employee_registration_sub_store', [EmployeeRegistrationController::class, 'sub_store'])->name('employee_registration_sub_store');
Route::put('/employee_registration/{id}', [EmployeeRegistrationController::class, 'update'])->name('employee_registration_update');
Route::get('/employee_registration/{id}', [EmployeeRegistrationController::class, 'edit'])->name('employee_registration.edit');
Route::post('/employee_registration/store_1', [EmployeeRegistrationController::class, 'stores'])->name('employee_registration.store_1');
Route::put('/employee_registration_documnet/{id}', [EmployeeRegistrationController::class, 'markAsDeleted'])->name('employee_registration.markAsDeleted');
Route::delete('employee_registration/delete/{id}', [EmployeeRegistrationController::class, 'destroy'])->name('employee_registration.destroy');

// port vechicle Registartion
Route::get('/port_vehicle_registration', [PortVehicleRegistrationController::class, 'index'])->name('port_vehicle_registration.index');
Route::get('/port_vehicle_registration_add', [PortVehicleRegistrationController::class, 'create'])->name('port_vehicle_registration_add');
Route::post('/port_vehicle_registration/store_1', [PortVehicleRegistrationController::class, 'stores'])->name('port_vehicle_registration.store_1');
Route::post('/port_vehicle_registration/store', [PortVehicleRegistrationController::class, 'store'])->name('port_vehicle_registration.store');
Route::get('/port_vehicle_registration/{id}', [PortVehicleRegistrationController::class, 'edit'])->name('port_vehicle_registration.edit');
Route::post('/port_vehicle_registration/{id}', [PortVehicleRegistrationController::class, 'update'])->name('port_vehicle_registration.update');
Route::post('/port_vehicle_registration_sub_store', [PortVehicleRegistrationController::class, 'sub_store'])->name('port_vehicle_registration_sub_store');
Route::put('/port_vehicle_registration_document/{id}', [PortVehicleRegistrationController::class, 'markAsDeleted'])->name('port_vehicle_registration.markAsDeleted');
Route::delete('port_vehicle_registration/delete/{id}', [PortVehicleRegistrationController::class, 'destroy'])->name('port_vehicle_registration.destroy');

// new changes================================
Route::get('/pass_entry', [passEntryController::class, 'index'])->name('pass_entry');
Route::get('/pass_entry_from', [passEntryController::class, 'create'])->name('pass_entry_from');
Route::get('/PassEntry', [passEntryController::class, 'show'])->name('PassEntry');
Route::post('/pass_entry_store', [passEntryController::class, 'store'])->name('pass_entry.store');
Route::delete('/pass_entry/delete/{id}', [passEntryController::class, 'destroy'])->name('pass_entry.delete');
Route::get('/pass_entry/{id}/edit', [passEntryController::class, 'edit'])->name('pass_entry.edit');
Route::put('/pass_entry/update/{id}', [passEntryController::class, 'update'])->name('pass_entry.update');
Route::get('/get-last-firm-number', [passEntryController::class, 'getLastFirmNumber']);
// sublist
Route::post('/pass_sublist', [passEntryController::class, 'sublist'])->name('pass_sublist.create');
Route::post('/pass_authentication', [passEntryController::class, 'authentication'])->name('pass_authentication.store');
// Route::delete('/pass_sublist/delete/{id}', [passEntryController::class, 'subdestroy'])->name('pass_sublist.delete');
Route::delete('/pass_sublist1/delete/{unique_id}', [passEntryController::class, 'subdestroy1'])->name('pass_sublist1.delete');
Route::delete('/pass-sublist/{unique_id}', [passEntryController::class, 'delete'])->name('pass_sublist.delete');

// new changes
Route::resource('firms_list', controller: firmsGetControler::class);
Route::resource('vehicleType', controller: vehicleTypeGetControler::class);


// Route::post('/vehicleCargory', [firmsGetControler::class, 'store'])->name('vehicleCargory.store');


Route::resource('vech_register_no', controller: VehGetControler::class);
Route::resource('person_adhar_no', controller: AdharGetControler::class);
Route::resource('person_license_no', controller: LicenseGetControler::class);

Route::get('/cargo_authentication', [CargoAuthenticationController::class, 'index'])->name('cargo_authentication');
Route::get('/cargo_authentication_add/{id}', [CargoAuthenticationController::class, 'create'])->name('cargo_authentication_add');
Route::post('/cargo_authentication_status', [CargoAuthenticationController::class, 'status_upload'])->name('cargo_authentication.status');
