<?php

/** Admin Controllers */
use App\Http\Controllers\Admin\AdminClaimsController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDeviceController;
use App\Http\Controllers\Admin\AdminImportDevicesController;
use App\Http\Controllers\Admin\AdminLoginLogController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminRepairsController;
use App\Http\Controllers\Admin\AdminRepairsShippingOptionController;
use App\Http\Controllers\Admin\AdminReportsController;
use App\Http\Controllers\Admin\AdminShippingOptionController;
use App\Http\Controllers\Admin\AdminShippingSuppliesController;
use App\Http\Controllers\Admin\AdminUserRegistrationController;
use App\Http\Controllers\Admin\ClaimReasonController;
use App\Http\Controllers\Admin\DefaultPlanController;
use App\Http\Controllers\Admin\DeviceBrandController;
use App\Http\Controllers\Admin\DeviceFamilyController;
use App\Http\Controllers\Admin\DeviceModelController;
use App\Http\Controllers\Admin\DevicePlanController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\RepairReasonController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoleSettingController;
use App\Http\Controllers\Admin\DeviceRepairPlanController;

/** User Controller */
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\RoutePermissionController;
use App\Http\Controllers\Admin\Smtp2GoController;
use App\Http\Controllers\Admin\StripeController;
use App\Http\Controllers\Admin\ZohoController;
use App\Http\Controllers\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\AdminResetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\UserForgotPasswordController;

/** Public controllers */
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\Auth\UserResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Public\PublicGetCoverageController;
use App\Http\Controllers\Public\PublicOrgController;
use App\Http\Controllers\StripeWebHookController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\User\InsuredClaimsController;
use App\Http\Controllers\User\RenewDevicesController;
use App\Http\Controllers\User\UninsuredClaimsController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserDeviceController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserRegistrationController;
use App\Http\Controllers\User\UserShippingSupplyController;
use App\Http\Controllers\User\SupportTicketController;
use App\Models\Admin\Device;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/** Default Auth routes */
// Auth::routes();

/** Route to upload the service agreement for the organizations */
Route::post('/sdcsmadmin/upload-service-agreement', [AdminDashboardController::class, 'uploadServiceAgreement'])->withoutMiddleware(['web']);

/** Admin Login Routes */
// Route::middleware('guest')->group(function () {
    Route::get('/smarttiusadmin/login', [AdminLoginController::class, 'adminLoginIndex'])->name('smarttiusadmin.login.index');
    Route::post('/smarttiusadmin/login', [AdminLoginController::class, 'adminLogin'])->name('smarttiusadmin.login');

// });
/** Admin Logout Route */
Route::post('/smarttiusadmin/logout', [AdminLoginController::class, 'adminLogout'])->name('smarttiusadmin.logout')->middleware('auth');

/** Admin Forgot Password Routes */
Route::get('/smarttiusadmin/forgot-password', [AdminForgotPasswordController::class, 'showLinkRequestForm'])->name('smarttiusadmin.password.request');
Route::post('/smarttiusadmin/forgot-password', [AdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('smarttiusadmin.password.email');


/** Admin Reset Password Routes */
Route::get('/smarttiusadmin/reset-password/{token}', [AdminResetPasswordController::class, 'showResetForm'])->name('smarttiusadmin.password.reset');
Route::post('/smarttiusadmin/reset-password', [AdminResetPasswordController::class, 'reset'])->name('smarttiusadmin.password.update');




/** User Login Routes */
Route::middleware('guest')->group(function () {
    Route::get('/sdcsmuser/login', [UserLoginController::class, 'userLoginIndex'])->name('sdcsmuser.login.index');
    Route::post('/sdcsmuser/login', [UserLoginController::class, 'userLogin'])->name('sdcsmuser.login');

});
/** User Logout Route */
Route::post('/sdcsmuser/logout', [UserLoginController::class, 'userLogout'])->name('sdcsmuser.logout')->middleware('auth');

/** User Forgot Password Routes */
Route::get('/sdcsmuser/forgot-password', [UserForgotPasswordController::class, 'showLinkRequestForm'])->name('sdcsmuser.password.request');
Route::post('/sdcsmuser/forgot-password', [UserForgotPasswordController::class, 'sendResetLinkEmail'])->name('sdcsmuser.password.email');
/** User Reset Password Routes */
Route::get('/sdcsmuser/reset-password/{token}', [UserResetPasswordController::class, 'showResetForm'])->name('sdcsmuser.password.reset');
Route::get('/smarttiusadmin/generate-reset-password/{user}', [UserResetPasswordController::class, 'generateResetForm'])->withoutMiddleware(['web']);
Route::post('/sdcsmuser/reset-password', [UserResetPasswordController::class, 'reset'])->name('sdcsmuser.password.update');

/** SignUp Form */
Route::get('/sdcsmuser/register', [SignUpController::class, 'index'])->name('register');
Route::post('/sdcsmuser/register', [SignUpController::class, 'register'])->name('sdcsmuser.register');

/** Index page (Selecting Admin or User dashboard option here.) */
    Route::get('/', [HomeController::class, 'index'])->name('index');

/** Home page with site URL */
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/** Common page to show errors */
    Route::get('/error', function(){
        return view('errors.error');
    })->name('error');

/**
 * ADMIN ROUTES
 */
    /**
     * Route for Admin Dashbaord
     */
    /* Route::get('/smarttiusadmin', function () { return view('admin.dashboard'); })->middleware('CheckUserPermission')->name('admin.dashboard'); */
    Route::prefix('smarttiusadmin')->middleware('CheckUserPermission')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/dash-recent-claims', [AdminDashboardController::class, 'recentClaims']);
        Route::get('/dash-recent/status-change-claims', [AdminDashboardController::class, 'recentClaimStatusChangedDevices']);
        Route::get('/dash-recent/uninsured-claims', [AdminDashboardController::class, 'recentUninsuredClaims']);
        Route::get('/dash-recent/devices', [AdminDashboardController::class, 'recentDevices']);

        Route::get('/dash-recent-support-tickets', [AdminDashboardController::class, 'recentSupportTickets']);

        Route::get('/dash-chart/insured-claims', [AdminDashboardController::class, 'chartInsuredClaims']);
        Route::get('/dash-chart/users', [AdminDashboardController::class, 'chartUsers']);
        /** Route to verfy the device coverage in the admin dashboard */
        Route::post('/verify-coverage', [AdminDashboardController::class, 'verifyCoverage']);
    });

    /** Route to get the claim and repair status */
    Route::prefix('smarttiusadmin')->middleware('CheckUserPermission')->group(function () {
        Route::get('/get-claim-repair-status', [AdminClaimsController::class, 'getClaimRepairStatus']);
    });

    /**
     * Admin Auth User Profile Page Routes
     */
    Route::prefix('smarttiusadmin/profile')->middleware('CheckUserPermission')->group(function () {
        Route::get('/', [AdminProfileController::class, 'index']);
        Route::get('/user-data', [AdminProfileController::class, 'authUserData']);
        Route::post('/update/{id}', [AdminProfileController::class, 'update']);
    });
    /**
     * Role Module Routes
    */
        Route::prefix('smarttiusadmin/roles')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('adminuserrole.index');
            Route::post('/store', [RoleController::class, 'store']);
            Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('adminuserrole.update');
            Route::put('/update/{id}', [RoleController::class, 'update']);
            Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('adminuserrole.delete');
        });

    /** Role Setting Routes */
        Route::prefix('smarttiusadmin/role-setting')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [RoleSettingController::class, 'index']);
            Route::post('/store', [RoleSettingController::class, 'store']);
        });

    /** Route Module Routes */
        Route::prefix('smarttiusadmin/routes')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [RouteController::class, 'index'])->name('adminuserroute.index');
            Route::post('/store', [RouteController::class, 'store']);
            Route::get('/edit/{id}', [RouteController::class, 'edit'])->name('adminuserroute.update');
            Route::put('/update/{id}', [RouteController::class, 'update']);
            Route::delete('/delete/{id}', [RouteController::class, 'destroy'])->name('adminuserroute.delete');
        });

    /** Route Permission Module Routes */
        Route::prefix('smarttiusadmin/route-permission')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [RoutePermissionController::class, 'index'])->name('adminuserroutepermission.index');
            Route::post('/store', [RoutePermissionController::class, 'store']);
            Route::get('/edit/{id}', [RoutePermissionController::class, 'edit']);
            Route::post('/update', [RoutePermissionController::class, 'update']);
        });

    /** Organization Module Routes */
        Route::prefix('smarttiusadmin/organizations')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [OrganizationController::class, 'index'])->name('adminorganization.index');
            Route::get('/create', [OrganizationController::class, 'createOrganization'])->name('adminorganization.create');
            Route::get('/show/{id}', [OrganizationController::class, 'showOrganization']);

            Route::post('/store', [OrganizationController::class, 'storeOrgData']);
            /** Update Organization Link */
            Route::post('/update-org-link/{id}', [OrganizationController::class, 'updateOrgLink']);
            Route::get('/edit/{id}', [OrganizationController::class, 'editOrg'])->name('adminorganization.edit');
            /** Update Organization Details */
            Route::post('/update-org-details/{id}', [OrganizationController::class, 'updateOrgDetails']);
            /** Update Sub-Organization Details */
            Route::post('/update-sub-org-details/{id}', [OrganizationController::class, 'updateSubOrg']);
            /** Delete Sub-org */
            Route::delete('/delete-sub-org/{id}', [OrganizationController::class, 'subOrgDelete']);
            /** Update Organization Allowed Devices Details */
            Route::post('/update-allowed-devices/{id}', [OrganizationController::class, 'updateAllowedDevices']);
            /** Org Allowed Device Delete */
            Route::delete('/delete-allowed-device/{id}', [OrganizationController::class, 'orgAllowedDevicesDelete']);
            /** Update Organization Renewal Allowed Devices Details */
            Route::post('/update-renewal-allowed-devices/{id}', [OrganizationController::class, 'updateRenewalDevices']);
             /** Org Allowed Device Delete */
             Route::delete('/delete-renewal-device/{id}', [OrganizationController::class, 'orgRenewalDevicesDelete']);
            /** Update Organization Claim & Portal Message Details */
            Route::post('/update-claim-reasons/{id}', [OrganizationController::class, 'updateClaimReasonsPortalMsg']);
            /** Org Claim Reason Delete */
            Route::delete('/delete-claim-reason/{id}', [OrganizationController::class, 'orgClaimReasonDelete']);
            /** Update Organization Additional Details */
            Route::post('/update-additional-details/{id}', [OrganizationController::class, 'updateAdditionalDetails']);
            /* Sub Organizations Routes*/
            Route::get('/sub-organizations', [OrganizationController::class, 'getSubOrganizations']);
            /* Get Organization Route */
            Route::get('/get-organization', [OrganizationController::class, 'getOrgByName']);


            /** Cancel Routes */
            Route::get('/cancel/sub-org/{id}', [OrganizationController::class, 'cancelSubOrg']);
            Route::get('/cancel/allowed-devices/{id}', [OrganizationController::class, 'cancelAllowedDevices']);
            Route::get('/cancel/renewal-devices/{id}', [OrganizationController::class, 'cancelRenewalDevices']);
            Route::get('/cancel/claim-reasons-msg/{id}', [OrganizationController::class, 'cancelClaimReasonsPortalMsg']);
            Route::get('/cancel/additional-details/{id}', [OrganizationController::class, 'cancelAdditionalDetails']);


            /*
            Route::put('/update/{id}', [OrganizationController::class, 'update']);
            Route::delete('/delete/{id}', [OrganizationController::class, 'destroy'])->name('adminorganization.delete');
            */
        });

    /* User Registraion Module  Routes*/
        Route::prefix('smarttiusadmin/users')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminUserRegistrationController::class, 'index'])->name('admin.user.index');
            Route::post('/checkEmail', [AdminUserRegistrationController::class, 'checkUserEmail']);
            Route::post('/store', [AdminUserRegistrationController::class, 'store']);
            Route::get('/view', [AdminUserRegistrationController::class, 'getUserDataById']);
            Route::get('/edit/{id}', [AdminUserRegistrationController::class, 'edit']);
            Route::post('/update/{id}', [AdminUserRegistrationController::class, 'update']);
            Route::post('/switch-user/{id}', [AdminUserRegistrationController::class, 'switchUser']);
            Route::post('/switch-back-to-admin', [UserDashboardController::class, 'switchBackToAdmin']);
        });

    /* Device Family Module Routes */
        Route::prefix('smarttiusadmin/device-family')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [DeviceFamilyController::class, 'index'])->name('admin.devicefamily.index');
            Route::post('/store', [DeviceFamilyController::class, 'store']);
            Route::get('/edit/{id}', [DeviceFamilyController::class, 'edit']);
            Route::post('/update', [DeviceFamilyController::class, 'update']);
            Route::delete('/delete/{id}', [DeviceFamilyController::class, 'destroy']);
        });

    /** Device Brand Module Routes */
        Route::prefix('smarttiusadmin/device-brand')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [DeviceBrandController::class, 'index'])->name('admin.devicebrand.index');
            Route::post('/store', [DeviceBrandController::class, 'store']);
            Route::get('/edit/{id}', [DeviceBrandController::class, 'edit']);
            Route::post('/update', [DeviceBrandController::class, 'update']);
            Route::delete('/delete/{id}', [DeviceBrandController::class, 'destroy']);
            Route::get('/fetch-brands', [DeviceBrandController::class, 'getDeviceBrandsByFamily']);
        });

    /** Device Model Module Routes */
        Route::prefix('smarttiusadmin/device-model')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [DeviceModelController::class, 'index'])->name('admin.devicemodel.index');
            Route::post('/store', [DeviceModelController::class, 'store']);
            Route::get('/edit/{id}', [DeviceModelController::class, 'edit']);
            Route::post('/update', [DeviceModelController::class, 'update']);
            Route::delete('/delete/{id}', [DeviceModelController::class, 'destroy']);
            Route::get('/fetch-models', [DeviceModelController::class, 'fetchDeviceModels']);
        });

    /** Device Module Routes */
        Route::prefix('smarttiusadmin/devices')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminDeviceController::class, 'index'])->name('admin.device.index');
            Route::get('/create', [AdminDeviceController::class, 'create']);
            Route::post('/store', [AdminDeviceController::class, 'store']);
            Route::get('/view/{id}', [AdminDeviceController::class, 'show']);
            Route::get('/edit/{id}', [AdminDeviceController::class, 'edit']);
            Route::post('/update/{id}', [AdminDeviceController::class, 'update']);
            Route::delete('/delete/{id}', [AdminDeviceController::class, 'destroy']);
            /** Routes to get user and model data by using search */
            Route::get('/org-device-models', [AdminDeviceController::class, 'getOrgDeviceModels']);
            Route::get('/get-users', [AdminDeviceController::class, 'getUsersList']);

            /** Export Device List */
            Route::get('/export', [AdminDeviceController::class, 'exportDevice'])->name('admin.device.export');
        });

    /** Devices Plan Module routes */
        Route::prefix('smarttiusadmin/devices-plan')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [DevicePlanController::class, 'index'])->name('admindeviceplan.index');
            Route::post('/store',[DevicePlanController::class, 'store']);
            Route::get('/view/{id}', [DevicePlanController::class, 'show']);
            Route::get('/edit/{id}', [DevicePlanController::class, 'edit']);
            Route::post('/get-device-plan', [DevicePlanController::class, 'getDevicePlan']);
            Route::post('/one-time', [DevicePlanController::class, 'getOneTimeDevicePlan']);
            Route::post('/manage-device-plan', [DevicePlanController::class, 'getDevicePlanData']);
        });

    /** Import Devices Module Routes */
        Route::prefix('smarttiusadmin/import-devices')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminImportDevicesController::class, 'index'])->name('adminimportdevice.index');
            Route::post('/store', [AdminImportDevicesController::class, 'store']);
            Route::post('/progress/{importId}', [AdminImportDevicesController::class, 'checkCsvImportProgress']);
            Route::get('/users', [AdminImportDevicesController::class, 'getUsers']);
            Route::get('/csv/{id}', [AdminImportDevicesController::class, 'downloadImportDevicescsv']);
        });

    /** Device repair plans module routes */
        Route::prefix('smarttiusadmin/repair-plan')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [DeviceRepairPlanController::class, 'index']);
            Route::get('/view/{id}', [DeviceRepairPlanController::class, 'show']);
            Route::post('/manage-repair-plan', [DeviceRepairPlanController::class, 'getDeviceRepairPlanData']);
            Route::post('/store',[DeviceRepairPlanController::class, 'store']);
        });

    /** ClaimReason Module Routes */
        Route::prefix('smarttiusadmin/claim-reasons')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [ClaimReasonController::class, 'index'])->name('adminclaimreason.index');
            Route::post('/create', [ClaimReasonController::class, 'addClaimReason']);
            Route::post('/update/{id}', [ClaimReasonController::class, 'updateClaimReason']);
            Route::get('/edit/{id}', [ClaimReasonController::class, 'getEditData']);
            Route::delete('/delete/{id}', [ClaimReasonController::class, 'destroy']);
        });

    /** repairReason Module Routes */
        Route::prefix('smarttiusadmin/repair-reasons')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [RepairReasonController::class, 'index'])->name('adminrepairreason.index');
            Route::post('/create', [RepairReasonController::class, 'addRepairReason']);
            Route::post('/update/{id}', [RepairReasonController::class, 'updateRepairReason']);
            Route::get('/edit/{id}', [RepairReasonController::class, 'getEditData']);
            Route::delete('/delete/{id}', [RepairReasonController::class, 'destroy']);
        });

    /** Device Default Plan Master Routes */
        Route::prefix('smarttiusadmin/device-default-plan')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [DefaultPlanController::class, 'index'])->name('devicedefaultplanmaster.index');
            Route::post('/store',[DefaultPlanController::class, 'store']);
            Route::get('/edit/{id}', [DefaultPlanController::class, 'edit']);
            Route::post('/update/{id}', [DefaultPlanController::class, 'update']);
        });

    /** Claims Module Routes */
        Route::prefix('smarttiusadmin/file-claim')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminClaimsController::class, 'fileClaim']); /** To open the form of the file claim */
            Route::post('/store', [AdminClaimsController::class, 'createClaim']); /** Submit of a file claim */
            Route::get('/user', [AdminClaimsController::class, 'getUserdataByName']);
            Route::post('/fetch-devices', [AdminClaimsController::class, 'fetchDeviceByName']);
            Route::get('/claim-reasons', [AdminClaimsController::class, 'getAllClaimReasons']);
            Route::get('/userdata', [AdminClaimsController::class, 'getUserData']);
        });
        Route::prefix('smarttiusadmin/track-claims')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminClaimsController::class, 'index'])->name('admintrackclaims.index');
            Route::get('/view/{id}', [AdminClaimsController::class, 'getClaimDataById']);
            Route::get('/edit/{id}', [AdminClaimsController::class,'editDeviceClaims']);
            Route::post('/update', [AdminClaimsController::class,'updateDeviceClaims']);
            Route::get('/export', [AdminClaimsController::class,'exportDeviceClaims']);
            Route::get('/claim-more-info/{id}', [AdminClaimsController::class,'getClaimMoreInfo']);
            Route::post('/update-claim-note/{id}', [AdminClaimsController::class,'updateClaimNote']);
        });


    /** Repairs Module Routes */
        Route::prefix('smarttiusadmin/repair-request')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminRepairsController::class, 'repairRequest']); /** To open the form of the file claim */
            Route::post('/store', [AdminRepairsController::class, 'store']); /** Submit of a file claim */
            Route::get('/user', [AdminClaimsController::class, 'getUserdataByName']);
            Route::get('/fetch-devices', [AdminRepairsController::class, 'fetchDeviceModelsByName']);
            Route::get('/userdata', [AdminClaimsController::class, 'getUserData']);
        });

        Route::prefix('smarttiusadmin/track-repairs')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminRepairsController::class, 'index'])->name('admintrackrepairs.index');
            Route::get('/view/{id}', [AdminRepairsController::class, 'getRepairDataById']);
            Route::get('/export', [AdminRepairsController::class,'exportRepairRequests']);
        });

    /** Shipping Options Module (Claims) */
    Route::prefix('smarttiusadmin/shipping-options')->middleware('CheckUserPermission')->group(function () {
        Route::get('/', [AdminShippingOptionController::class, 'index']);
        Route::post('/store', [AdminShippingOptionController::class, 'store']);
        Route::get('/edit/{id}', [AdminShippingOptionController::class, 'edit']);
        Route::post('/update/{id}', [AdminShippingOptionController::class, 'update']);
    });

    /** Shipping Options Module (Repairs) */
    Route::prefix('smarttiusadmin/repair-shipping-options')->middleware('CheckUserPermission')->group(function () {
        Route::get('/', [AdminRepairsShippingOptionController::class, 'index']);
        Route::post('/store', [AdminRepairsShippingOptionController::class, 'store']);
        Route::get('/edit/{id}', [AdminRepairsShippingOptionController::class, 'edit']);
        Route::post('/update/{id}', [AdminRepairsShippingOptionController::class, 'update']);
    });

    /** Shipping Supply boxes */
     Route::prefix('smarttiusadmin/shipping-supply-boxes')->middleware('CheckUserPermission')->group(function () {
        Route::get('/', [AdminShippingSuppliesController::class, 'boxList']);
        Route::post('/create', [AdminShippingSuppliesController::class, 'storeBox']);
        Route::get('/edit/{id}', [AdminShippingSuppliesController::class, 'editBox']);
        Route::post('/update/{id}', [AdminShippingSuppliesController::class, 'updateBox']);
        // Route::get('/view', [AdminShippingSuppliesController::class, 'getShippingSupplyDetailById']);
    });

    /** Shipping Supplies Module */
    Route::prefix('smarttiusadmin/shipping-supplies')->middleware('CheckUserPermission')->group(function () {
        Route::get('/', [AdminShippingSuppliesController::class, 'index']);
        Route::get('/view', [AdminShippingSuppliesController::class, 'getShippingSupplyDetailById']);
        Route::post('/update-status', [AdminShippingSuppliesController::class, 'updateShippingSupplyStatus']);
    });

    /** Support Ticket Module */
    Route::prefix('smarttiusadmin/support-ticket')->middleware('CheckUserPermission')->group(function () {
        Route::get('/', [SupportTicketController::class, 'adminSupportTicketIndex']);
        Route::get('/view', [SupportTicketController::class, 'getTicketDataById']);
    });

    /** Login Logs Module */
    Route::prefix('smarttiusadmin/login-logs')->middleware('CheckUserPermission')->group(function () {
        Route::get('/', [AdminLoginLogController::class, 'index']);
    });

    /** INTEGRATIONS */
        /** ZOHO */
        /** Routes to manage the settings of ZOHO */
        Route::prefix('smarttiusadmin/zoho-setting')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [ZohoController::class, 'index'])->name('zohosetting.index'); /** To call the zoho setting form */
            Route::post('/verifydata', [ZohoController::class, 'verifydata']); /** To call the generate key function */
            Route::get('/refreshtoken', [ZohoController::class, 'refreshToken']); /** To call the refresh token function */
        });

        /** Route to get code, access token and refresh token first time */
        Route::prefix('zoho/first-handshake')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [ZohoController::class, 'sdcsm_generateAccessToken_func']);
        });

        /** STRIPE */
        /** Routes to manage the settings of Stripe */
        Route::prefix('smarttiusadmin/stripe-setting')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [StripeController::class, 'index'])->name('stripesetting.index'); /** To call the stripe setting form */
            Route::post('/store', [StripeController::class,'store'])->name('stripesetting.store'); /** To store stripe settings */
        });

        /** SMTP */
        /** Routes to manage the settings of SMTP */
        Route::prefix('smarttiusadmin/smtp-setting')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [Smtp2GoController::class, 'index'])->name('smtp2Gosetting.index'); /** To call the smtp setting form */
            Route::post('/store', [Smtp2GoController::class,'store'])->name('smtp2Gosetting.store'); /** To store smtp settings */
        });

        /** Admin Reports */
        Route::prefix('smarttiusadmin/reports')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminReportsController::class, 'index'])->name('adminreports.index');
            Route::get('/device-month', [AdminReportsController::class, 'deviceMonthReport']);
            Route::get('/total-devices', [AdminReportsController::class, 'totalDevicesReport']);
            Route::get('/device-repair-month', [AdminReportsController::class, 'deviceRepairRequestMonthReport']);
            Route::get('/total-device/repair-request', [AdminReportsController::class, 'totalRepairRequestsReport']);
            Route::get('/device-claim-month', [AdminReportsController::class, 'deviceClaimRequestMonthReport']);
            Route::get('/total-device/claim-request', [AdminReportsController::class, 'totalClaimRequestsReport']);
            Route::get('/users-month', [AdminReportsController::class, 'userMonthReport']);
            Route::get('/total-users', [AdminReportsController::class, 'totalUsersReport']);
        });

        /** Admin Notifications */
        Route::prefix('smarttiusadmin')->middleware('CheckUserPermission')->group(function () {
            /** Notifications */
            Route::get('/get-notifications', [AdminNotificationController::class,'getNotifications']);
            /** With Pagination */
            Route::get('/notifications', [AdminNotificationController::class,'index']);
             /** Mark all seen */
             Route::get('/notifications/markSeen', [AdminNotificationController::class,'markSeen']);
             /** Mark single seen */
             Route::post('/notification/single-markSeen', [AdminNotificationController::class,'markSingleSeen']);
             /** Latest Unseen messages count */
             Route::get('/get-notifications/count',[AdminNotificationController::class, 'getNotificationsCount']);
        });

        /** Admin Transactions */
        Route::prefix('smarttiusadmin')->middleware('CheckUserPermission')->group(function () {
            /** Transactions */
            Route::get('/transactions', [TransactionController::class,'adminindex']);
        });

/**
* USER ROUTES
*/
    Route::group(['as' => 'sdcsmuser.','prefix'=> 'sdcsmuser', 'middleware' => ['CheckUserPermission']], function () {

        Route::controller(UserDashboardController::class)->group(function () {
            Route::get('/home', 'index')->name('dashboard');
            Route::get('/dash-chart/claims-repair', 'chartClaimsRepair');
            Route::get('/dash-recent-claims', 'recentClaims');
            Route::get('/dash-recent-support-tickets', 'recentSupportTickets');
            Route::get('/dash-recent/status-change-claims', 'recentClaimStatusChangedDevices');
            Route::get('/dash-recent/uninsured-claims', 'recentUninsuredClaims');
            Route::get('/dash-recent/devices', 'recentDevices');
            Route::post('/switch-back-to-admin', 'switchBackToAdmin');
        });

        /**
         * Profile Page Routes
         */
        Route::prefix('/profile')->controller(UserProfileController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/user-data', 'authUserData');
            Route::post('/update/{id}', 'update');
        });

        /** Device Routes */
        Route::controller(UserDeviceController::class)->group(function () {
            /** Devices */
            Route::prefix('device-list')->group(function () {
                Route::get('/', 'index')->name('device.list');
                Route::get('/view/{id}', 'getDeviceById');
                Route::post('/coverage', 'checkDeviceCoverage');
            });


        });

        /** Renewal Devices */
        Route::controller(RenewDevicesController::class)->group(function () {
            /** Renewal Devices */
            Route::prefix('renewal-devices')->group(function () {
                Route::get('/', 'renewalDevicesIndex');
                Route::post('/store', 'renewNow'); /** Renew Device */
                Route::post('/plan', 'getRenewalPlansByModelId');
            });

            /** Early Renewal Devices  */
            Route::prefix('early-renewal-devices')->group(function () {
                Route::get('/', 'earlyRenewalIndex');
                Route::post('/store', 'earlyRenewNow');
                Route::post('/plan', 'getEarlyRenewalPlansByModelId');
            });
        });

        /** Insured Claims */
            /** User routes for insured claim functionalities */
            Route::controller(InsuredClaimsController::class)->group(function () {
                Route::get('/user-file-claim', 'fileClaim')->name('insuredclaim.fileclaim'); /** To open the form of the file claim */
                Route::post('/fileclaims/store', 'createClaim')->name('insuredclaim.fileclaim'); /** Submit of a file claim */

                Route::get('/claim-list/view/{id}', 'getClaimById'); /** To get the claim data to show in the sidebar */
                Route::get('/claim-reasons', 'getAllClaimReasons'); /** To get the claim reasons */

                Route::post('/fileclaims/getdevices', 'fetchDeviceBySerialNumbers'); /** To fetch the data from serial numbers */
                Route::get('/fileclaims/userdata', 'getUserdataById'); /** To fetch the data to show in the form(address and other)*/

                Route::get('/user-track-claims', 'trackClaim')->name('insuredclaim.trackclaim'); /** To open the track claims page */

                Route::get('/update-claims/{id}', 'updateClaim'); /** To open the update claims page */
                Route::post('/update-claims-data', 'updateClaimData'); /** To update claims data */

                Route::get('/fileclaims/device-plan', 'getDevicePlans');
            });

        /** Uninsured Repairs */
            /** To call the repair request */
            Route::controller(UninsuredClaimsController::class)->group(function () {
                Route::get('/user-repair-request', 'requestRepair')->name('uninsuredclaim.repairrequest');
                Route::post('/uninsured-devices', 'uninsuredDevices'); /** To get uninsuredDevices */
                Route::post('/user-repair-request/store', 'store'); /** To store the repair request */
                Route::get('/user-repair-request/view/{id}', 'getRepairById'); /** To view the repair request */
                Route::get('/user-track-repairs', 'trackRepairs')->name('uninsuredclaim.trackrepairs');
            });
            Route::post('/repair-plan', [DeviceRepairPlanController::class, 'getDeviceRepairPlanData']);

        /** Shipping Supply */
          Route::controller(UserShippingSupplyController::class)->group(function () {
             Route::get('/shipping-supplies', 'index')->name('shippingsupplies.index');
             Route::post('/shipping-supplies/store', 'store');
             Route::get('/shipping-supplies/user-data', 'getUserdataById');
             Route::get('/shipping-supplies-request/view', 'getShippingSupplyRequestById'); /** To view the request */
          });

        /** Notifications */
        Route::controller(UserNotificationController::class)->group(function () {
             /** Notifications */
             Route::get('/get-notifications', 'getNotifications');
             /** With Pagination */
             Route::get('/notifications', 'index');
            /** Mark seen to all */
             Route::get('/notifications/markSeen', 'markSeen');
            /** Mark seen to particular only */
             Route::post('/notification/single-markSeen', 'markSingleSeen');
             /** Latest Unseen messages count */
             Route::get('/get-notifications/count','getNotificationsCount');
        });

        /** Transactions */
        Route::controller(TransactionController::class)->group(function () {
             /** Transactions */
             Route::get('/transactions', 'userindex');
        });

        /** User Registration */
        Route::prefix('/users')->controller(UserRegistrationController::class)->group(function () {
            Route::get('/', 'index')->name('registration.index');
            Route::post('/store', 'store')->name('registration.store');
            Route::post('/checkEmail', 'checkUserEmail');
        });

        /** Support Ticket */
        Route::prefix('/support-ticket')->controller(SupportTicketController::class)->group(function () {
            Route::get('/', 'createForm');
            Route::post('/create', 'store');
        });

        Route::get('sub-org',[OrganizationController::class, 'getSubOrganizations']);
    });

/**
 * PUBLIC ROUTES
*/

    /**Route to get the data from Zoho and update the records */
    // Route::prefix('smarttiusadmin/zoho/')->middleware('CheckUserPermission')->group(function () {
    Route::prefix('zoho')->group(function () {
        Route::get('/crm/sdcsm/webhook/claimsrepairs', [ZohoController::class, 'sdcsm_handle_webhook_response_func']);
    });

    /**
     * Routes for the get coverage form
    */
    Route::prefix('get-coverage')->group(function () {
        Route::get('/', [PublicGetCoverageController::class, 'getCoverageForm'])->name('public.get-coverage.index');
        Route::post('/addcoverage', [PublicGetCoverageController::class, 'createCoverage']);
        Route::post('/getdevices', [PublicGetCoverageController::class, 'getDeviceModels']);
        Route::post('/checkuser', [PublicGetCoverageController::class, 'userCheck']);
        Route::post('/getplans', [PublicGetCoverageController::class, 'getPlans']);
        Route::post('/checkserialnumber', [PublicGetCoverageController::class, 'checkSerialNumber']);
    });

    /**
     * Organization view and registering the user and device by using the organization link.
    */
    // Route::group(['as' => 'public.','prefix'=> 'public/organization', 'middleware' => ['CheckUserPermission']], function () {
    // Route::group(['as' => 'public.','prefix'=> 'public/organization'], function () {
    Route::group(['as' => 'public.','prefix'=> 'org'], function () {
        /* For organization */
        Route::get('/{slug}', [PublicOrgController::class, 'viewOrganization'])->name('org.view');
        Route::post('/store', [PublicOrgController::class, 'storePublicOrgData']);
        Route::post('/get-devices', [PublicOrgController::class, 'getOrgDeviceModels']);
        Route::post('/get-plans', [PublicOrgController::class,'getDevicePlan']);
        Route::post('/checkEmail', [PublicOrgController::class, 'checkUserEmail']);
    });

    Route::post('/stripe/webhook', [StripeWebHookController::class, 'handleWebhook'])->withoutMiddleware(['web']);