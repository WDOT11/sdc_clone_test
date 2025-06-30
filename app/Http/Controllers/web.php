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

/** User Controller */
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\RoutePermissionController;
use App\Http\Controllers\Admin\StripeController;
use App\Http\Controllers\Admin\ZohoController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\HomeController;

/** Public controllers */
use App\Http\Controllers\Public\PublicGetCoverageController;
use App\Http\Controllers\Public\PublicOrgController;
use App\Http\Controllers\User\InsuredClaimsController;
use App\Http\Controllers\User\UninsuredClaimsController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserDeviceController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserShippingSupplyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/** Default Auth routes */
Auth::routes();

/** Admin Or user Login Routes */
Route::get('/smarttiusadmin/login', [AdminLoginController::class, 'adminLoginIndex'])->name('smarttiusadmin.login.index');
Route::post('/smarttiusadmin/login/store', [AdminLoginController::class, 'adminLogin'])->name('smarttiusadmin.login');
Route::post('/smarttiusadmin/logout', [AdminLoginController::class, 'adminLogout'])->middleware('auth');

Route::get('/sdcsmuser/login', [UserLoginController::class, 'userLoginIndex'])->name('sdcsmuser.login.index');
Route::post('/sdcsmuser/login/store', [UserLoginController::class, 'userLogin'])->name('sdcsmuser.login');
Route::post('/sdcsmuser/logout', [UserLoginController::class, 'userLogout'])->middleware('auth');


/** Index page (Selecting Admin or User dashboard option here.) */
    Route::get('/', function () {
        return view('index');
    });

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
        Route::get('/dash-chart/insured-claims', [AdminDashboardController::class, 'chartInsuredClaims']);
        Route::get('/dash-chart/users', [AdminDashboardController::class, 'chartUsers']);
        /** Route to verfy the device coverage in the admin dashboard */
        Route::post('/verify-coverage', [AdminDashboardController::class, 'verifyCoverage']);

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
            Route::post('/manage-device-plan', [DevicePlanController::class, 'getDevicePlanData']);
        });
    /** Import Devices Module Routes */
        Route::prefix('smarttiusadmin/import-devices')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminImportDevicesController::class, 'index'])->name('adminimportdevice.index');
            Route::post('/store', [AdminImportDevicesController::class, 'store']);
            Route::post('/progress/{importId}', [AdminImportDevicesController::class, 'checkCsvImportProgress']);
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
        Route::prefix('smarttiusadmin/track-claims')->middleware('CheckUserPermission')->group(function () {
            Route::get('/', [AdminClaimsController::class, 'index'])->name('admintrackclaims.index');
            Route::get('/view/{id}', [AdminClaimsController::class, 'getClaimDataById']);
            Route::get('/export', [AdminClaimsController::class,'exportDeviceClaims']);
        });

    /** Repairs Module Routes */
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

    /** Shipping Supplies Module */
    Route::prefix('smarttiusadmin/shipping-supplies')->middleware('CheckUserPermission')->group(function () {
        Route::get('/', [AdminShippingSuppliesController::class, 'index']);
        Route::get('/view', [AdminShippingSuppliesController::class, 'getShippingSupplyDetailById']);
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
             /** Mark seen */
             Route::get('/markSeen', [AdminNotificationController::class,'markSeen']);

             /** Latest Unseen messages count */
             Route::get('/get-notifications/count',[AdminNotificationController::class, 'getNotificationsCount']);
        });



/**
* USER ROUTES
*/
    Route::group(['as' => 'sdcsmuser.','prefix'=> 'sdcsmuser', 'middleware' => ['CheckUserPermission']], function () {


        Route::controller(UserDashboardController::class)->group(function () {
            Route::get('/home', 'index')->name('dashboard');
            Route::get('/dash-chart/claims-repair', 'chartClaimsRepair');
            Route::get('/dash-recent-claims', 'recentClaims');
            Route::get('/dash-recent/status-change-claims', 'recentClaimStatusChangedDevices');
            Route::get('/dash-recent/uninsured-claims', 'recentUninsuredClaims');
            Route::get('/dash-recent/devices', 'recentDevices');

        });
        /**
         * Profile Page Routes
         */
        Route::prefix('/profile')->controller(UserProfileController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/user-data', 'authUserData');
            Route::post('/update/{id}', 'update');
        });
        /** Devices*/
        Route::prefix('/device-list')->controller(UserDeviceController::class)->group(function () {
            Route::get('/', 'index')->name('device.list');
            Route::get('/view/{id}', 'getDeviceById');
            Route::post('/coverage', 'checkDeviceCoverage');

        });

        /** Insured Claims */
            /** User routes for insured claim functionalities */
            Route::controller(InsuredClaimsController::class)->group(function () {
                Route::get('/user-file-claim', 'fileClaim')->name('insuredclaim.fileclaim'); /** To open the form of the file claim */
                Route::post('/fileclaims/store', 'createClaim')->name('insuredclaim.fileclaim'); /** Submit of a file claim */

                Route::get('/claim-list/view/{id}', 'getClaimById'); /** To get the claim data to show in the sidebar */

                Route::post('/fileclaims/getdevices', 'fetchDeviceBySerialNumbers'); /** To fetch the data from serial numbers */
                Route::get('/fileclaims/userdata', 'getUserdataById'); /** To fetch the data to show in the form(address and other)*/

                Route::get('/user-track-claims', 'trackClaim')->name('insuredclaim.trackclaim'); /** To open the track claims page */
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
            /** Mark seen */
             Route::get('/markSeen', 'markSeen');
             /** Latest Unseen messages count */
             Route::get('/get-notifications/count','getNotificationsCount');
        });
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
    });

    /**
     * Organization view and registering the user and device by using the organization link.
     */
    // Route::group(['as' => 'public.','prefix'=> 'public/organization', 'middleware' => ['CheckUserPermission']], function () {
    Route::group(['as' => 'public.','prefix'=> 'public/organization'], function () {
        /* For organization */
        Route::get('/{slug}', [PublicOrgController::class, 'viewOrganization'])->name('org.view');
        Route::post('/store', [PublicOrgController::class, 'storePublicOrgData']);
        Route::post('/get-devices', [PublicOrgController::class, 'getOrgDeviceModels']);
        Route::post('/get-plans', [PublicOrgController::class,'getDevicePlan']);
        Route::post('/checkEmail', [PublicOrgController::class, 'checkUserEmail']);
    });