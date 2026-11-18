<?php

use App\Http\Controllers\academicYearController;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\GateUserLoginController;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\PassUserLoginController;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\center_name_creation;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\CenterAnalytics;
use App\Http\Controllers\dashboard\GateAnalytics;

use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\icons\Boxicons;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\MainUserScreenController;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;

use App\Http\Controllers\StaffController;
use App\Http\Controllers\student_report;
use App\Http\Controllers\UserAuthentication;
use App\Http\Controllers\UserCreationController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\UserScreenController;
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
use App\Http\Controllers\GateAccessInController;

use App\Http\Controllers\GateAccessOutController;
use App\Http\Controllers\PassDetailGetController;
use App\Http\Controllers\PassDetailOutGetController;

use App\Http\Controllers\ProductSpecificationController;


use App\Http\Controllers\verficationGetControler;
use App\Http\Controllers\ProductCreationController;
use App\Http\Controllers\SpareCreationController;
use App\Http\Controllers\group_creationController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\spare_categoryController;
use App\Http\Controllers\unitController;
use App\Http\Controllers\supplierController;
use App\Http\Controllers\invoiceController;
use App\Http\Controllers\grninwardController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\spare_groupController;
use App\Http\Controllers\TestingMasterController;
use App\Http\Controllers\ProductSpareController;
use App\Http\Controllers\physicaltestController;
use App\Http\Controllers\technicaltestController;
use App\Http\Controllers\SpareRequestController;
use App\Http\Controllers\RateFixingController;

use App\Http\Controllers\CleaningController;
use App\Http\Controllers\sparestockController;
use App\Http\Controllers\LotStockController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\paintingProductController;
use App\Http\Controllers\spareRequestStoreController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ScrapStockController;
use App\Http\Controllers\CustomercreationController;
use App\Http\Controllers\GetServiceController;
use App\Http\Controllers\companycreationcontroller;
use App\Http\Controllers\assignServiceController;
use App\Http\Controllers\technicalServiceController;
use App\Http\Controllers\SpareRequestServiceController;
use App\Http\Controllers\quotationController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\oemRegistrationController;
use App\Http\Controllers\grnVerificationController;
use App\Http\Controllers\productSpecificationType;
use App\Http\Controllers\issueCategoryController;
use App\Http\Controllers\issueCreationController;
use App\Http\Controllers\qcParticularsController;
use App\Http\Controllers\trackingQRController;
use App\Http\Controllers\qcTypeController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\wardController;
use App\Http\Controllers\departmentController;
use App\Http\Controllers\designationController;
use App\Http\Controllers\complaintcategoryController;
use App\Http\Controllers\departmentComplaintController;
use App\Http\Controllers\departmentcategoryController;
use App\Http\Controllers\Locationcontroller;
use App\Http\Controllers\newComplaintsController;
use App\Http\Controllers\sourcenameController;
use App\Http\Controllers\transferComplaintController;
use App\Http\Controllers\priorityController;
use App\Http\Controllers\stageController;
use App\Http\Controllers\userregistrationController;
use App\Http\Controllers\overallGrievanceController;
use App\Http\Controllers\RepeatedComplaintsController;
use App\Http\Controllers\zonecomplaintController;
use App\Http\Controllers\employeewisereportController;
use App\Http\Controllers\periodicalReportController;
use App\Http\Controllers\wardwisereportController;
use App\Http\Controllers\departmentAllocationController;
use App\Http\Controllers\activePersonController;
use App\Http\Controllers\escalationController;
use App\Http\Controllers\streetlightreportController;
use App\Http\Controllers\newstreetlightreportController;
use App\Http\Controllers\escalationreportController;
use Illuminate\Support\Facades\Route;

// Main Page Route
// Route::get( '/', [ Analytics::class, 'index' ] )->name( 'dashboard-analytics' );
Route::get('/', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('home', [LoginBasic::class, 'index'])->name('home.index');

Route::get('/authlogin', [LoginBasic::class, 'index'])->name('authlogin');

// Route::resource( 'login', LoginBasic::class );
Route::get('/login', [LoginBasic::class, 'store'])->name('login');

Route::get('/userlogin', [PassUserLoginController::class, 'store'])->name('userlogin');
Route::get('PassUser', [PassUserLoginController::class, 'index'])->name('PassUser.index');

Route::get('GateUser', [GateUserLoginController::class, 'index'])->name('GateUser.index');
Route::get('/gatelogin', [GateUserLoginController::class, 'store'])->name('gatelogin');

Route::resource('dashboard', Analytics::class);
Route::resource('Centerdashboard', CenterAnalytics::class);
Route::resource('Gatedashboard', GateAnalytics::class);

Route::resource('staff', StaffController::class);

// Alternatively, you can use these explicit routes
Route::get('/staff_creation', [StaffController::class, 'index'])->name('staff_creation');
Route::post('/staff/store', [StaffController::class, 'store'])->name('staff.store');
Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
Route::PUT('/staff/update/{id}', [StaffController::class, 'update'])->name('staff.update');
Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');

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

Route::get('/AccessIn', [GateAccessInController::class, 'index'])->name('AccessIn.index');
Route::get('/AccessOut', [GateAccessOutController::class, 'index'])->name('AccessOut.index');

Route::get('/getPassDetails', [PassDetailGetController::class, 'index'])->name('getPassDetails.index');
Route::get('/getPasstable', [PassDetailGetController::class, 'get_pass_data'])->name('getPasstable.create');

Route::get('/getoutPassDetails', [PassDetailOutGetController::class, 'index'])->name('getPassOutDetails.index');

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
// ROute::get( '/getuidata', [ UserPermissionController::class, 'getuidata' ] )->name( 'userpermission.data' );
Route::get('/getdata', [UserPermissionController::class, 'getdata'])->name('userpermission.getdata');
Route::get('/userpermission_add', [UserPermissionController::class, 'create'])->name('userpermission.create');
Route::post('/userpermission', [UserPermissionController::class, 'store'])->name('userpermission.store');
// Route::get( '/userpermission/{id}/edit', [ UserPermissionController::class, 'edit' ] )->name( 'userpermission.edit' );
Route::get('userpermission/edit/{uniqueIds}', [UserPermissionController::class, 'edit'])->name('userpermission.edit');

Route::put('/userpermission/{id}', [UserPermissionController::class, 'update'])->name('userpermission.update');
Route::delete('/userpermission/{id}', [UserPermissionController::class, 'destroy'])->name('userpermission.destroy');

Route::get('/student_report', [student_report::class, 'index'])->name('student_report');

//academic_year
Route::get('/academic_year', [academicYearController::class, 'index'])->name('academic_year');
Route::get('/academic_year_add', [academicYearController::class, 'add'])->name('academic_year_add');
Route::post('/academic_year_store', [academicYearController::class, 'store'])->name('academic_year.store');
Route::get('/academic_year/{id}/edit', [academicYearController::class, 'edit'])->name('academic_year.edit');
Route::delete('/academic_year/{id}', [academicYearController::class, 'destroy'])->name('academic_year.delete');
Route::put('/academic_year/{id}', [academicYearController::class, 'update'])->name('academic_year.update');

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

Route::get('/center_name_list', [center_name_creation::class, 'index'])->name('center_name_list');
Route::get('/center_name_add', [center_name_creation::class, 'add'])->name('center_name_add');
Route::post('/center_name_store', [center_name_creation::class, 'store'])->name('center_name.store');
Route::get('/center_name/edit/{id}', [center_name_creation::class, 'edit'])->name('center_name.edit');
Route::post('/center_name/{id}', [center_name_creation::class, 'update'])->name('center_name.update');
Route::delete('/center_name/delete/{id}', [center_name_creation::class, 'destroy'])->name('center_name.delete');

//UserAuthentication

Route::get('/user_authentication', [UserAuthentication::class, 'index'])->name('user_authentication');
Route::get('/user_authentication_add', [UserAuthentication::class, 'add'])->name('user_authentication_add');

Route::resource('verfication', controller: verficationGetControler::class);

Route::get('/group_creation', [group_creationController::class, 'index'])->name('group_creation');
Route::get('/group_creation_add', [group_creationController::class, 'add'])->name('group_creation_add');
Route::post('/group_creation_store', [group_creationController::class, 'store'])->name('group_creation.store');
// Route::get('/group_creation/edit/{id}', [group_creationController::class, 'edit'])->name('group_creation.edit');
Route::get('/group_creation/edit', [group_creationController::class, 'edit'])->name('group_creation.edit');

Route::PUT('/group_creation/{id}', [group_creationController::class, 'update'])->name('group_creation.update');
Route::delete('/group_creation/delete/{id}', [group_creationController::class, 'destroy'])->name('group_creation.delete');

Route::get('/category', [categoryController::class, 'index'])->name('category');
Route::get('/category_add', [categoryController::class, 'add'])->name('category_add');
Route::post('/category_store', [categoryController::class, 'store'])->name('category.store');
Route::get('/category/edit/{id}', [categoryController::class, 'edit'])->name('category.edit');
Route::PUT('/category/{id}', [categoryController::class, 'update'])->name('category.update');
Route::delete('/category/delete/{id}', [categoryController::class, 'destroy'])->name('category.delete');

Route::get('/spare_category', [spare_categoryController::class, 'index'])->name('spare_category');
Route::get('/spare_category_add', [spare_categoryController::class, 'add'])->name('spare_category_add');
Route::post('/spare_category_store', [spare_categoryController::class, 'store'])->name('spare_category.store');
Route::get('/spare_category/edit', [spare_categoryController::class, 'edit'])->name('spare_category.edit');



Route::get('/product_specification', [ProductSpecificationController::class, 'index'])->name('product_specification');
Route::get('/product_specification_add', [ProductSpecificationController::class, 'add'])->name('product_specification_add');
Route::post('/product_specification_store', [ProductSpecificationController::class, 'store'])->name('product_specification.store');
Route::get('/product_specification/edit', [ProductSpecificationController::class, 'edit'])->name('product_specification.edit');


Route::get('/subcategory', [SubcategoryController::class, 'index'])->name('subcategory.index');
Route::get('/subcategory_add', [SubcategoryController::class, 'add'])->name('subcategory_add');
Route::post('/subcategory_store', [SubcategoryController::class, 'store'])->name('subcategory.store');
Route::get('/subcategory/edit', [SubcategoryController::class, 'edit'])->name('subcategory.edit');
// Route::get('/spare_category/edit/{id}', [spare_categoryController::class, 'edit'])->name('spare_category.edit');
Route::PUT('/spare_category/{id}', [spare_categoryController::class, 'update'])->name('spare_category.update');
Route::delete('/spare_category/delete/{id}', [spare_categoryController::class, 'destroy'])->name('spare_category.delete');

Route::get('/unit', [unitController::class, 'index'])->name('unit');
Route::get('/unit_add', [unitController::class, 'add'])->name('unit_add');
Route::post('/unit_store', [unitController::class, 'store'])->name('unit.store');
Route::get('/unit/edit/{id}', [unitController::class, 'edit'])->name('unit.edit');
Route::PUT('/unit/{id}', [unitController::class, 'update'])->name('unit.update');
Route::delete('/unit/delete/{id}', [unitController::class, 'destroy'])->name('unit.delete');



Route::get('/supplier', [supplierController::class, 'index'])->name('supplier');
Route::get('/supplier_add', [supplierController::class, 'add'])->name('supplier_add');
Route::get('/supplier/edit/{id}', [supplierController::class, 'edit'])->name('supplier.edit');
Route::post('/supplier_store', [supplierController::class, 'store'])->name('supplier.store');
Route::PUT('/supplier/{id}', [supplierController::class, 'update'])->name('supplier.update');
Route::delete('/supplier/delete/{id}', [supplierController::class, 'destroy'])->name('supplier.delete');

Route::get('/product_creation', [ProductCreationController::class, 'index'])->name('product_creation');
Route::get('/product_creation_add', [ProductCreationController::class, 'add'])->name('product_creation_add');
Route::get('/product_creation/edit/{id}', [ProductCreationController::class, 'edit'])->name('product_creation.edit');
Route::post('/product_creation/store', [ProductCreationController::class, 'store'])->name('product_creation.store');
Route::put('/product_creation/update/{id}', [ProductCreationController::class, 'update'])->name('product_creation.update');
Route::delete('/product_creation/delete/{id}', [ProductCreationController::class, 'destroy'])->name('product_creation.delete');
Route::post('/get-categories-by-group', [ProductCreationController::class, 'getCategoriesByGroup']);

Route::get('/spare_creation', [SpareCreationController::class, 'index'])->name('spare_creation');
Route::get('/spare_creation_add', [SpareCreationController::class, 'add'])->name('spare_creation_add');
Route::post('/spare_creation', [SpareCreationController::class, 'store'])->name('spare_creation.store');
Route::get('/spare_creation/{id}/edit', [SpareCreationController::class, 'edit'])->name('spare_creation.edit');
Route::put('/spare_creation/{id}', [SpareCreationController::class, 'update'])->name('spare_creation.update');
Route::delete('/spare_creation/delete/{id}', [SpareCreationController::class, 'destroy'])->name('spare_creation.delete');
Route::post('/get-spare-categories-by-group', [SpareCreationController::class, 'getSpareCategoriesByGroup']);

Route::get('/purchase_item', [PurchaseItemController::class, 'index'])->name('purchase_item');
Route::get('/purchase_item_add', [PurchaseItemController::class, 'add'])->name('purchase_item_add');
Route::get('/purchase_item/edit/{id}', [PurchaseItemController::class, 'edit'])->name('purchase_item.edit');
Route::post('/purchase_item_store', [PurchaseItemController::class, 'store'])->name('purchase_item.store');
Route::PUT('/purchase_item/{id}', [PurchaseItemController::class, 'update'])->name('purchase_item.update');
Route::delete('/purchase_item/delete/{id}', [PurchaseItemController::class, 'destroy'])->name('purchase_item.delete');

Route::get('/quotation_creation', [quotationController::class, 'index'])->name('quotation_creation');
Route::get('/quotation_creation_add', [quotationController::class, 'add'])->name('quotation_creation_add');

Route::get('/invoice', [invoiceController::class, 'index'])->name('invoice');
Route::get('/invoice_add', [invoiceController::class, 'add'])->name('invoice_add');
Route::get('/invoice/q_view', [invoiceController::class, 'view'])->name('q_view.view');


Route::get('/grn_inward', [grninwardController::class, 'index'])->name('grn_inward');
Route::get('/grninward_add', [grninwardController::class, 'add'])->name('grninward_add');

Route::get('/spare_group', [spare_groupController::class, 'index'])->name('spare_group');
Route::get('/sparegroup_add', [spare_groupController::class, 'add'])->name('sparegroup_add');
Route::post('/sparegroup_store', [spare_groupController::class, 'store'])->name('sparegroup.store');
Route::get('/sparegroup/edit/{id}', [spare_groupController::class, 'edit'])->name('sparegroup.edit');
Route::PUT('/sparegroup/{id}', [spare_groupController::class, 'update'])->name('sparegroup.update');
Route::delete('/sparegroup/delete/{id}', [spare_groupController::class, 'destroy'])->name('sparegroup.delete');

Route::get('/testing_master', [TestingMasterController::class, 'index'])->name('testing_master');
Route::get('/testing_master_add', [TestingMasterController::class, 'add'])->name('testing_master_add');
Route::post('/testing_master_store', [TestingMasterController::class, 'store'])->name('testing_master.store');
Route::get('/testing_master/edit/{id}', [TestingMasterController::class, 'edit'])->name('testing_master.edit');
Route::PUT('/testing_master/{id}', [TestingMasterController::class, 'update'])->name('testing_master.update');
Route::delete('/testing_master/delete/{id}', [TestingMasterController::class, 'destroy'])->name('testing_master.delete');

Route::get('/designation', [designationController::class, 'index'])->name('designation');
Route::get('/designation_add', [designationController::class, 'add'])->name('designation_add');
Route::post('/designation_store', [designationController::class, 'store'])->name('designation.store');
Route::get('/designation/edit/{id}', [designationController::class, 'edit'])->name('designation.edit');
Route::PUT('/designation/{id}', [designationController::class, 'update'])->name('designation.update');
Route::delete('/designation/delete/{id}', [designationController::class, 'destroy'])->name('designation.delete');

Route::get('/product_spare', [ProductSpareController::class, 'index'])->name('product_spare');
Route::get('/product_spare_add', [ProductSpareController::class, 'add'])->name('product_spare_add');
Route::post('/product_spare_store', [ProductSpareController::class, 'store'])->name('product_spare.store');
Route::get('/product_spare/edit/{id}', [ProductSpareController::class, 'edit'])->name('product_spare.edit');
Route::PUT('/product_spare/{id}', [ProductSpareController::class, 'update'])->name('product_spare.update');
Route::delete('/product_spare/delete/{id}', [ProductSpareController::class, 'destroy'])->name('product_spare.delete');

Route::get('/physical_test', [physicaltestController::class, 'index'])->name('physical_test');
Route::get('/physical_add', [physicaltestController::class, 'add'])->name('physical_add');

Route::get('/technical_test', [technicaltestController::class, 'index'])->name('technical_test');
Route::get('/technical_add', [technicaltestController::class, 'add'])->name('technical_add');

Route::get('/spare_request', [SpareRequestController::class, 'index'])->name('spare_request');
Route::get('/spare_request_add', [SpareRequestController::class, 'add'])->name('spare_request_add');
// Route::post( '/spare_request_store', [ SpareRequestController::class, 'store' ] )->name( 'spare_request.store' );
Route::get('/spare_request/edit/{id}', [SpareRequestController::class, 'edit'])->name('spare_request.edit');
// Route::PUT( '/spare_request/{id}', [ SpareRequestController::class, 'update' ] )->name( 'spare_request.update' );
// Route::delete( '/spare_request/delete/{id}', [ SpareRequestController::class, 'destroy' ] )->name( 'spare_request.delete' );

Route::get('/rate_fixing', [RateFixingController::class, 'index'])->name('rate_fixing');
Route::get('/rate_fixing_add', [RateFixingController::class, 'add'])->name('rate_fixing_add');

Route::get('/cleaning', [CleaningController::class, 'index'])->name('cleaning');
Route::get('/cleaning/edit', [CleaningController::class, 'edit'])->name('cleaning.edit');
Route::get('/cleaning/view/', [CleaningController::class, 'view'])->name('cleaning.view');

Route::get('/painting_product', [paintingProductController::class, 'index'])->name('painting_product');
Route::get('/painting_product/edit', [paintingProductController::class, 'edit'])->name('painting_product.edit');
Route::get('/painting_product/view/', [paintingProductController::class, 'view'])->name('painting_product.view');

Route::get('/spare_stock', [sparestockController::class, 'index'])->name('spare_stock');
Route::get('/sparestockadd', [sparestockController::class, 'add'])->name('spare_stock_add');
Route::get('/sparestockname/update', [sparestockController::class, 'update'])->name('spare_stock_name_update');
Route::get('/sparestockname', [sparestockController::class, 'edit'])->name('spare_stock_edit');

Route::get('/lot_stock', [LotStockController::class, 'index'])->name('lot_stock');

Route::get('/product_stock', [ProductStockController::class, 'index'])->name('product_stock');
Route::get('/productstockadd', [ProductStockController::class, 'add'])->name('product_stock_add');
Route::get('/productstockname/update', [ProductStockController::class, 'update'])->name('product_stock_name');
Route::get('/productstockname', [ProductStockController::class, 'edit'])->name('product_stock_name_edit');

Route::get('/spare_request_store', [spareRequestStoreController::class, 'index'])->name('spare_request_store');

Route::get('/branch_division', [BranchController::class, 'index'])->name('branch_division');
Route::get('/branch_divisionadd', [BranchController::class, 'add'])->name('branch_divisionadd');
Route::post('/branch_divisionstore', [BranchController::class, 'store'])->name('branch_division.store');
Route::get('/branch_division/edit/{id}', [BranchController::class, 'edit'])->name('branch_division.edit');
Route::PUT('/branch_division/update/{id}', [BranchController::class, 'update'])->name('branch_division.update');
Route::delete('/branch_division/delete/{id}', [BranchController::class, 'destroy'])->name('branch_division.delete');

Route::get('/scrapstock', [ScrapStockController::class, 'index'])->name('scrapstock');
Route::get('/scrapstockadd', [ScrapStockController::class, 'add'])->name('scrapstock_add');
Route::get('/scrapstockname/update', [ScrapStockController::class, 'update'])->name('scrapstock_name_update');
Route::get('/scrapstockname', [ScrapStockController::class, 'edit'])->name('scrapstock_edit');

Route::get('/company_creation', [companycreationcontroller::class, 'index'])->name('company_creation');
Route::get('/companycreationadd', [companycreationcontroller::class, 'add'])->name('companycreation_add');
Route::post('/companycreationstore', [companycreationcontroller::class, 'store'])->name('company_creation.store');
Route::get('/companycreationedit/{id}', [companycreationcontroller::class, 'edit'])->name('company_creation.edit');
Route::PUT('/companycreationupdate/{id}', [companycreationcontroller::class, 'update'])->name('company_creation.update');

Route::get('/customer_creation', [CustomercreationController::class, 'index'])->name('customer_creation');
Route::get('/customer_creationadd', [CustomercreationController::class, 'add'])->name('customer_creation_add');
Route::post('/customer_creationstore', [CustomercreationController::class, 'store'])->name('customer_creation.store');
Route::get('/customer_creation/edit/{id}', [CustomercreationController::class, 'edit'])->name('customer_creation.edit');
Route::PUT('/customer_creation/update/{id}', [CustomercreationController::class, 'update'])->name('customer_creation.update');
Route::delete('/customer_creation/delete/{id}', [CustomercreationController::class, 'destroy'])->name('customer_creation.delete');

Route::get('/get_service', [GetServiceController::class, 'index'])->name('get_service');

//assign service
Route::get('/assign_service', [assignServiceController::class, 'index'])->name('assign_service');
Route::get('/assign_service/edit/{id}', [assignServiceController::class, 'edit'])->name('assign_service.edit');

//technical service
Route::get('/tech_service', [technicalServiceController::class, 'index'])->name('tech_service');
Route::get('/tech_service/edit/{id}', [technicalServiceController::class, 'edit'])->name('tech_service.edit');

//spare request Service
Route::get('/spare_request_service', [SpareRequestServiceController::class, 'index'])->name('spare_request_service');

//oem Registration
Route::get('/oem_registration', [oemRegistrationController::class, 'index'])->name('oem_registration');
Route::get('/oem_registrationadd', [oemRegistrationController::class, 'add'])->name('oem_registration_add');

//grn verification
Route::get('/grn_verification', [grnVerificationController::class, 'index'])->name('grn_verification');
Route::get('/grn_verification/edit', [grnVerificationController::class, 'edit'])->name('grn_verification.edit');
Route::get('/grn_verification/view', [grnVerificationController::class, 'view'])->name('grn_verification.view');
Route::get('/grn_verification/print', [grnVerificationController::class, 'print'])->name('grn_verification.print');

//product Specification type
Route::get('/product_specification_type', [productSpecificationType::class, 'index'])->name('product_specification_type');
Route::get('/product_specification_typeadd', [productSpecificationType::class, 'add'])->name('product_specification_type_add');

//issue category
Route::get('/issue_category', [issueCategoryController::class, 'index'])->name('issue_category');
Route::get('/issue_categoryadd', [issueCategoryController::class, 'add'])->name('issue_category_add');

//QC Type
Route::get('/qc_type', [qcTypeController::class, 'index'])->name('qc_type');
Route::get('/qc_typeadd', [qcTypeController::class, 'add'])->name('qc_type_add');

//issue creation
Route::get('/issue_creation', [issueCreationController::class, 'index'])->name('issue_creation');
Route::get('/issue_creationadd', [issueCreationController::class, 'add'])->name('issue_creation_add');

Route::get('/qc_particulars', [qcParticularsController::class, 'index'])->name('qc_particulars');
Route::get('/qc_particularsadd', [qcParticularsController::class, 'add'])->name('qc_particulars_add');

//tracking qr
Route::get('/tracking_qr', [trackingQRController::class, 'index'])->name('tracking_qr');
Route::get('/tracking_qr/view', [trackingQRController::class, 'view'])->name('tracking_qr.view');
Route::get('/tracking_qr/print', [trackingQRController::class, 'print'])->name('tracking_qr.print');

//zone
Route::get('/zone', [ZoneController::class, 'index'])->name('zone');
Route::get('/zone_add', [ZoneController::class, 'add'])->name('zone_add');
Route::post('/zonestore', [ZoneController::class, 'store'])->name('zone.store');
Route::get('/zoneedit/{id}', [ZoneController::class, 'edit'])->name('zone.edit');
Route::PUT('/zoneupdate/{id}', [ZoneController::class, 'update'])->name('zone.update');
Route::delete('/zone/delete/{id}', [ZoneController::class, 'destroy'])->name('zone.delete');

//source_name
Route::get('/source_name', [sourcenameController::class, 'index'])->name('source_name');
Route::get('/source_name_add', [sourcenameController::class, 'add'])->name('source_name_add');
Route::post('/source_namestore', [sourcenameController::class, 'store'])->name('source_name.store');
Route::get('/source_nameedit/{id}', [sourcenameController::class, 'edit'])->name('source_name.edit');
Route::PUT('/source_nameupdate/{id}', [sourcenameController::class, 'update'])->name('source_name.update');
Route::delete('/source_name/delete/{id}', [sourcenameController::class, 'destroy'])->name('source_name.delete');

//ward
Route::get('/ward', [wardController::class, 'index'])->name('ward');
Route::get('/ward_add', [wardController::class, 'add'])->name('ward_add');
Route::post('/wardstore', [wardController::class, 'store'])->name('ward.store');
Route::get('/wardedit/{id}', [wardController::class, 'edit'])->name('ward.edit');
Route::PUT('/wardupdate/{id}', [wardController::class, 'update'])->name('ward.update');
Route::delete('/ward/delete/{id}', [wardController::class, 'destroy'])->name('ward.delete');

//department
Route::get('/department', [departmentController::class, 'index'])->name('department');
Route::get('/department_add', [departmentController::class, 'add'])->name('department_add');
Route::post('/department_store', [departmentController::class, 'store'])->name('department.store');
Route::get('/department/edit/{id}', [departmentController::class, 'edit'])->name('department.edit');
Route::PUT('/department/{id}', [departmentController::class, 'update'])->name('department.update');
Route::delete('/department/delete/{id}', [departmentController::class, 'destroy'])->name('department.delete');

//designation
// Route::get('/designation', [designationController::class, 'index'])->name('designation');
// Route::get('/designation_add', [designationController::class, 'add'])->name('designation_add');

//complaintcategory
Route::get('/complaintcategory', [complaintcategoryController::class, 'index'])->name('complaintcategory');
Route::get('/complaintcategory_add', [complaintcategoryController::class, 'add'])->name('complaintcategory_add');

//departmentwisereport  departmentwiseReportController
// Department Complaint Wise
Route::get('/departmentcomplaint', [departmentComplaintController::class, 'index'])->name('departmentcomplaint');
Route::get('/departmentcomplaint/details', [departmentComplaintController::class, 'details'])->name('departmentcomplaint.details');
Route::get('/departmentcomplaint/export', [departmentComplaintController::class, 'export'])->name('departmentcomplaint.export');
Route::get('/departmentcomplaint/print', [departmentComplaintController::class, 'print'])->name('departmentcomplaint.print');



// Route::get('/departmentwisereport', [departmentwiseReportController::class, 'view'])->name('departmentwisereport.view');
// Route::get('/departmentwisereport/data', [departmentwiseReportController::class, 'data'])->name('departmentwisereport.data');
// Route::get('/departmentwisereport/export', [departmentwiseReportController::class, 'export'])->name('departmentwisereport.export');
// Route::get('/departmentwisereport/complaints', [departmentwiseReportController::class, 'complaints'])->name('departmentwisereport.complaints');



//departmentcategoryController
Route::get('/departmentcategory', [departmentcategoryController::class, 'index'])->name('departmentcategory');
Route::get('/departmentcategory_add', [departmentcategoryController::class, 'add'])->name('departmentcategory_add');
Route::post('/departmentcategory_store', [departmentcategoryController::class, 'store'])->name('departmentcategory.store');
Route::get('/departmentcategory/edit/{id}', [departmentcategoryController::class, 'edit'])->name('departmentcategory.edit');
Route::PUT('/departmentcategory/{id}', [departmentcategoryController::class, 'update'])->name('departmentcategory.update');
Route::delete('/departmentcategory/delete/{id}', [departmentcategoryController::class, 'destroy'])->name('departmentcategory.delete');

//Location
Route::get('/location', [Locationcontroller::class, 'index'])->name('location');
Route::get('/location_add', [Locationcontroller::class, 'add'])->name('location_add');
Route::post('/locationstore', [Locationcontroller::class, 'store'])->name('location.store');
Route::get('/locationedit/{id}', [Locationcontroller::class, 'edit'])->name('location.edit');
Route::PUT('/locationupdate/{id}', [Locationcontroller::class, 'update'])->name('location.update');
Route::delete('/location/delete/{id}', [Locationcontroller::class, 'destroy'])->name('location.delete');
Route::get('/get-wards-by-zone-loc/{zoneId}', [Locationcontroller::class, 'getWardsByZoneloc'])->name('getZoneWarlocdData');

//new Complaints
Route::get('/new_complaints', [newComplaintsController::class, 'index'])->name('new_complaints');
Route::get('/new_complaints_completed', [newComplaintsController::class, 'index1'])->name('new_complaints_com');
Route::get('/new_complaints_add', [newComplaintsController::class, 'add'])->name('new_complaints_add');
Route::get('/new_complaints-wards-by-zone/{zoneId}', [newComplaintsController::class, 'getWardsByZoneNewComplaints'])->name('getZoneWardComplaints');
Route::post('/new_complaints_store', [newComplaintsController::class, 'store'])->name('new_complaints.store');
Route::get('/new_complaints_edit/{id}', [newComplaintsController::class, 'edit'])->name('new_complaints.edit');
Route::PUT('/new_complaints_update/{id}', [newComplaintsController::class, 'update'])->name('new_complaints.update');
Route::delete('/new_complaints/delete/{id}', [newComplaintsController::class, 'destroy'])->name('new_complaints.delete');
Route::get('/grievance-id/next', [newComplaintsController::class, 'getNextGrievanceId'])->name('grievance.next');
Route::get('/get-ward-location-data', [newComplaintsController::class, 'getWardLocationData'])->name('getWardLocationData');



//transfer Complaint
Route::get('/transfer_complaint', [transferComplaintController::class, 'index'])->name('transfer_complaint');

//priority
Route::get('/priority', [priorityController::class, 'index'])->name('priority');
Route::get('/priority_add', [priorityController::class, 'add'])->name('priority_add');
Route::post('/prioritystore', [priorityController::class, 'store'])->name('priority.store');
Route::get('/priorityedit/{id}', [priorityController::class, 'edit'])->name('priority.edit');
Route::PUT('/priorityupdate/{id}', [priorityController::class, 'update'])->name('priority.update');
Route::delete('/priority/delete/{id}', [priorityController::class, 'destroy'])->name('priority.delete');

//stage
Route::get('/stage', [stagecontroller::class, 'index'])->name('stage');
Route::get('/stage_add', [stageController::class, 'add'])->name('stage_add');
Route::post('/stagestore', [stageController::class, 'store'])->name('stage.store');
Route::get('/stageedit/{id}', [stageController::class, 'edit'])->name('stage.edit');
Route::PUT('/stageupdate/{id}', [stageController::class, 'update'])->name('stage.update');
Route::delete('/stage/delete/{id}', [stageController::class, 'destroy'])->name('stage.delete');

//userregistration
Route::get('/user_registration', [userregistrationcontroller::class, 'index'])->name('user_registration');
Route::get('/user_registration_add', [userregistrationController::class, 'add'])->name('user_registration_add');
Route::post('/user_registrationstore', [userregistrationcontroller::class, 'store'])->name('user_registration.store');
Route::get('/user_registrationedit/{id}', [userregistrationcontroller::class, 'edit'])->name('user_registration.edit');
Route::PUT('/user_registrationupdate/{id}', [userregistrationcontroller::class, 'update'])->name('user_registration.update');
Route::delete('/user_registration/delete/{id}', [userregistrationcontroller::class, 'destroy'])->name('user_registration.delete');
Route::post('/user_registration_sub/store', [userregistrationcontroller::class, 'store1'])->name('user_registration_sub.store');
Route::post('/user_registration_sub/table_convert', [userregistrationcontroller::class, 'tableConvert'])->name('user.table.convert');


//overall Grievance
Route::get('/overall_grievance', [overallGrievanceController::class, 'index'])->name('overall_grievance');

//Repated complaints
Route::get('/repeated_complaints', [RepeatedComplaintsController::class, 'index'])->name('repeated_complaints');
//zonecomplaint
Route::get('/zonecomplaint', [zonecomplaintController::class, 'index'])->name('zonecomplaint'); // existing
Route::get('/zonecomplaint', [zonecomplaintController::class, 'index'])->name('zonecomplaint'); // existing duplicate in your file
Route::get('/zonewise-source/data', [zonecomplaintController::class, 'zoneWiseSourceData'])->name('zonewise-source.data'); // existing (unused here)
Route::get('/zonecomplaint/print', [zonecomplaintController::class, 'print'])->name('zonecomplaint.print');

/* ✅ NEW: CSV export route */
Route::get('/zonecomplaint/export', [zonecomplaintController::class, 'export'])->name('zonecomplaint.export');
Route::get('/zonewise-source/data', [zonecomplaintController::class, 'zoneWiseSourceData'])->name('zonewise-source.data');
Route::get('/zonecomplaint/details', [zonecomplaintController::class, 'details'])->name('zonecomplaint.details'); 
Route::get('/zonecomplaint/details', function () {return view('content.zonecomplaint.details');})->name('zonecomplaint.details');


// //zonalcategoryreport
// Route::get('/zonalcategoryreport', [zonalcategoryreportController::class, 'index'])->name('zonalcategoryreport');
// //periodicalreport
// Route::get('/periodicalreport', [periodicalreportController::class, 'index'])->name('periodicalreport');


//wardwiswreport
Route::get('/wardwisereport', [wardwisereportController::class, 'index'])->name('wardwisereport');
Route::get('/wardwisereport/details', [wardwisereportController::class, 'details'])->name('wardwisereport.details');
Route::get('/wardwisereport/export', [wardwisereportController::class, 'export'])->name('wardwisereport.export'); // ✅ Excel



//department Allocation 
Route::get('/department_allocation', [departmentAllocationController::class, 'index'])->name('department_allocation');
Route::get('/department_allocation_add', [departmentAllocationController::class, 'add'])->name('department_allocation_add');
Route::get('/department_allocationedit/{id}', [departmentAllocationController::class, 'edit'])->name('department_allocation.edit');
Route::PUT('/department_allocationupdate/{id}', [departmentAllocationController::class, 'update'])->name('department_allocation.update');
Route::delete('/department_allocation/delete/{id}', [departmentAllocationController::class, 'destroy'])->name('department_allocation.delete');
Route::get('/get_user_profile/{staffId}', [departmentAllocationController::class, 'getUserProfile'])->name('getUserProfile');
Route::get('/get-department-categories/{deptId}', [departmentAllocationController::class, 'getDepartmentCategories'])->name('getDepartmentCategories');
Route::get('/get-wards-by-zone/{zoneId}', [departmentAllocationController::class, 'getWardsByZone'])->name('getZoneWardData');
Route::post('/department_allocation_sub_store', [departmentAllocationController::class, 'store'])->name('department_allocation_sub.store');
Route::post('/convert-table-data', [departmentAllocationController::class, 'convertTableData'])->name('table.convert');
Route::post('/department_allocation_store', [departmentAllocationController::class, 'store1'])->name('department_allocation.store1');
Route::get('/staff_profile/{staffId}', [departmentAllocationController::class, 'getStaffData'])->name('getStaffData');


//active person
Route::get('/active_person', [activePersonController::class, 'index'])->name('active_person');
Route::get('/active_person/{status}', [activePersonController::class, 'statusDetails'])->name('status.details');

//escalation
Route::get('/escalation', [escalationController::class, 'index'])->name('escalation');
Route::get('/escalation_add', [escalationController::class, 'add'])->name('escalation_add');
Route::post('/escalationstore', [escalationController::class, 'store'])->name('escalation.store');
Route::get('/escalationedit/{id}', [escalationController::class, 'edit'])->name('escalation.edit');
Route::put('/escalation/{unique_id}', [escalationController::class, 'update'])->name('escalation.update');
Route::delete('/escalation/{id}', [escalationController::class, 'destroy'])->name('escalation.delete');

//employee wise report
Route::get('employeewisereport', [employeewisereportController::class, 'index'])->name('employeewisereport.index');
Route::get('employeewisereport/fetch', [employeewisereportController::class, 'fetchData'])->name('employeewisereport.fetch');
Route::get('employeewisereport/export', [employeewisereportController::class, 'export'])->name('employeewisereport.export');

//streetlightreportController
Route::get('/streetlightreport', [streetlightreportController::class, 'index'])->name('streetlightreport');
Route::get('/streetlightreport/export', [streetlightreportController::class, 'export'])->name('streetlightreport.export');
Route::get('/streetlightreport/print', [streetlightreportController::class, 'print'])->name('streetlightreport.print'); // 

//newstreetlightController
Route::get('/newstreetlightreport', [newstreetlightreportController::class, 'index'])->name('newstreetlightreport');
Route::get('/newstreetlightreport/export', [newstreetlightreportController::class, 'export'])->name('newstreetlightreport.export');
Route::get('/newstreetlightreport/print', [newstreetlightreportController::class, 'print'])->name('newstreetlightreport.print');

//escalationreportreportController
Route::get('/escalation_report', [escalationreportController::class, 'index'])->name('escalation_report');
Route::get('/escalation_report/export', [escalationreportController::class, 'export'])->name('escalation_report.export');
Route::get('/escalation_report/print', [escalationreportController::class, 'print'])->name('escalation_report.print');







