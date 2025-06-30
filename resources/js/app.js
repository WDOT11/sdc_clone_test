import './bootstrap';
import { createApp,reactive } from 'vue';
import axios from 'axios';

/**
 * Importing Dashboard component for designing
 */

import AdminDashboardDesign from './components/admin/adminDashboard.vue';

/**
 * GLOBAL COMPONENTS
*/
/**
 * Importing the pagination component
*/
import { registerGlobalProperties } from './custom.js'; /** Import as named function */

/** Import the global components */
import Pagination from './components/globalcomponent/pagination.vue';
import ErrorMessage from './components/globalcomponent/errorMessage.vue';

/**
 * Importing multiselect from vue-multiselect
*/
import Multiselect from 'vue-multiselect';
import Alert from './components/globalcomponent/alert.vue';
import App from './components/globalcomponent/app.vue';
import DeleteAlert from './components/globalcomponent/deleteAlert.vue';
import SideBarAnimationLoader from './components/globalcomponent/sidebaranimationloader.vue';
import DashRecentClaims from './components/globalcomponent/dashrecenttables/dashboardRecentClaims.vue';
import DashRecentStausChangeClaims from './components/globalcomponent/dashrecenttables/dashboardStausChangeClaims.vue';
import DashRecentRepairs from './components/globalcomponent/dashrecenttables/dashboardRecentUninsuredClaims.vue';
import DashRecentDevices from './components/globalcomponent/dashrecenttables/dashboardRecentDevices.vue';
import DashRecentSupportTickets from './components/globalcomponent/dashrecenttables/dashboardRecentSupportTickets.vue';
import DashDevicesChart from './components/globalcomponent/charts/dashboardDevicesChart.vue'
import DashClaimRepairChart from './components/globalcomponent/charts/dashboardClaimsRepairChart.vue';
import DashClaimRepairMonthChart from './components/globalcomponent/charts/dashboardClaimsRepairMonthChart.vue';

/**
 * ADMIN MODULE COMPONENTS
*/
    /**
     * Importing the admin dashboard components
    */
    import AdminDashHeader from './components/admin/layouts/Header.vue';
    import AdminDashFooter from './components/admin/layouts/Footer.vue';
    import AdminDashSideBar from './components/admin/layouts/Siderbar.vue';
    import AdminDashUserChart from './components/admin/charts/dashboardUsersChart.vue';
    import AdminNotificationFeed from './components/admin/layouts/NotificationFeed.vue';
    import AdminProfilePage from './components/admin/profile/index.vue';

    /**
    * Importing the components of role moduie
    */
    import UserRoleList from './components/admin/userrolemaster/index.vue';

    /**
    *  Importing the components of route moduie
    */
    import UserRouteList from './components/admin/userroutes/index.vue';

    /**
     * Importing the component of the role setting module
     */
    import AdminRoleSetting from './components/admin/rolesettings/index.vue';

    /**
    *  Importing the components of the route permission module
    */
    import UserRoutePermissionList from './components/admin/userroutepermission/index.vue';

    /** Importing the components of the device module(Family, Brands, Models, Plans) */
    import DeviceFamilyMaster from './components/admin/devicefamilymaster/index.vue';
    import DeviceBrandMaster from './components/admin/devicebrandmaster/index.vue';
    import DeviceModelMaster from './components/admin/devicemodelmaster/index.vue';
    import DeviceMasterIndex from './components/admin/devicemaster/index.vue';
    import DeviceMasterCreate from './components/admin/devicemaster/CreateDevice.vue';
    import DeviceMasterDetails from './components/admin/devicemaster/DetailsDevice.vue';
    import DeviceMasterUpdate from './components/admin/devicemaster/UpdateDevice.vue';
    import DevicePlanMasterIndex from './components/admin/deviceplanmaster/Index.vue';
    import DeviceImportMasterIndex from './components/admin/importdevicemaster/index.vue';
    import DeviceImportProgress from './components/admin/importdevicemaster/progress.vue';

    /** Importing the components of the organization module */
    import AdminOrganizarionList from './components/admin/organizationmaster/index.vue';
    import AdminOrganizarionCreate from './components/admin/organizationmaster/CreateOrganization.vue';
    import AdminOrganizarionUpdate from './components/admin/organizationmaster/UpdateOrganization.vue';

    /** Importing the components of the claim reason module */
    import AdminClaimReasonMaster from './components/admin/claimreasonmaster/index.vue';

    /** Importing the components of the repair reason module */
    import AdminRepairReasonMaster from './components/admin/repairreasonmaster/index.vue';

    /** Importing the components of the default device plan module */
    import DefaultDevicePlanMasterIndex from './components/admin/devicedefaultplanmaster/index.vue';

    /** Importing the components of the shipping options module (Claims) */
    import AdminShippingOptions from './components/admin/adminshippingoptions/index.vue';

    /** Importing the components of the shipping options module (Repairs) */
    import AdminRepairsShippingOptions from './components/admin/adminrepairsshippingoptions/index.vue';

    /** Importing the components of the Track claim and track repair module */
    import TrackClaim from './components/admin/adminclaims/index.vue';
    import AdminFileClaim from './components/admin/adminclaims/fileclaim.vue';
    import AdminUpdateClaim from './components/admin/adminclaims/updateclaim.vue';
    import AdminRepairRequest from './components/admin/adminrepairs/repairrequest.vue';
    import TrackRepair from './components/admin/adminrepairs/index.vue';

    /** Importing the component for the shipping supplies module */
    import AdminShippingSupplies from './components/admin/adminshippingsupplies/index.vue';

    /** Importing the components of role moduie */
    import AdminSupplyBoxs from './components/admin/shippingsupplyboxes/index.vue';

    /** Importing the component for the repair device plan module */
    import RepairPlanMaster from './components/admin/repairplanmaster/index.vue';

    /** Importing the component for the admin zoho setting */
    import AdminZohoSetting from './components/admin/zohosettings/index.vue';

    /** Importing the component for the admin stripe setting */
    import AdminStripeSetting from './components/admin/stripesettings/index.vue';

    /** Importing the component for the admin smtp2Go setting */
    import AdminSmtp2GoSetting from './components/admin/smtp2gosettings/index.vue';

    /** Importing the components of the user registration module */
    import AdminUserRegistrationIndex from './components/admin/userregistrationmaster/index.vue';
    import AdminUserRegistrationUpdate from './components/admin/userregistrationmaster/update.vue';

    /** Importing the component of the verify coverage module(popup) */
    import AdminVerifyCoverage from './components/admin/adminvarifycoverage/verifyCoverage.vue';

    /** Importing the component of the notifications module*/
    import AdminNotificationsIndex from './components/admin/notificationsmaster/index.vue';

    /** Importing the component of the transactions module*/
    import AdminTransactionIndex from './components/admin/transactionmaster/index.vue';

    /** Importing the component for login logs */
    import AdminLoginLogs from './components/admin/adminloginlogs/index.vue';

    /** Importing the component for admin reports */
    import AdminReports from './components/admin/adminreports/index.vue';
    import AdminDeviceMonthChartReports from './components/admin/adminreports/devicereport/monthchart.vue';
    import AdminDevicePieChartReports from './components/admin/adminreports/devicereport/piechart.vue';
    import AdminDeviceRepairMonthChartReports from './components/admin/adminreports/repairreport/repairmonthchart.vue';
    import AdminDeviceRepairPieChartReports from './components/admin/adminreports/repairreport/repairpiechart.vue';
    import AdminDeviceClaimMonthChartReports from './components/admin/adminreports/claimreport/claimmonthchart.vue';
    import AdminDeviceClaimPieChartReports from './components/admin/adminreports/claimreport/claimpiechart.vue';
    import AdminUsersMonthChartReports from './components/admin/adminreports/userreports/usermonthchart.vue';
    import AdminUsersPieChartReports from './components/admin/adminreports/userreports/userpiechart.vue';

    /** importing the component of the support tickets */
    import AdminSupportTicket from './components/admin/supportticket/index.vue';

/**
 * USER MODULE COMPONENTS
*/
    /**
     * Layout components
     */
    import UserHeader from './components/user/layouts/Header.vue';
    import UserFooter from './components/user/layouts/Footer.vue';
    import UserSideBar from './components/user/layouts/SideBar.vue';
    import UserPagination from './components/user/layouts/UserPagination.vue';
    import UserNotificationFeed from './components/user/Home/NotificationFeed.vue';
    import UserHome from './components/user/Home/Index.vue';
    import UserQuickLinks from './components/user/layouts/QuickLinks.vue';
    import UserProfilePage from './components/user/Profile/Index.vue';

    /**
     * Device componets
     */
    import UserDeviceList from './components/user/Devices/DeviceList.vue';
    import UserDeviceCoverage from './components/user/Devices/DeviceCoverage.vue';
    import UserRenewalDeviceList from './components/user/Devices/RenewDevice.vue';
    import UserEarlyRenewalDeviceList from './components/user/Devices/EarlyRenewDevice.vue';

    /** Insured claims components */
    import UserFileClaim from './components/user/InsuredClaims/FileClaim.vue';
    import UserTrackClaims from './components/user/InsuredClaims/TrackClaim.vue';
    import UserUpdateClaims from './components/user/InsuredClaims/UpdateClaim.vue';

    /**
     * Uninsured repair components
     */
    import UserRepairRequest from './components/user/UninsuredRepairs/RequestRepair.vue';
    import UserTrackRepairs from './components/user/UninsuredRepairs/TrackReapir.vue';

    /**
     * Shipping supplies form component
    */
    import UserShipingSuppliesIndex from './components/user/Supplies/index.vue';
    import UserShipingSuppliesCreate from './components/user/Supplies/create.vue';

    /**
     * Support Ticket Components
    */
    import UserSupportTicketCreate from './components//user/SupportTicket/createTicket.vue';

    /**
     * Notifications components
    */

    import UserNotificationsIndex from './components/user/NotificationsMaster/Index.vue';
    /**
     * Transactions components
    */

    import UserTransactionIndex from './components/user/Transactions/Index.vue';

    /** User Registration components */
    import UserRegistrationIndex from './components/user/UserRegistration/Index.vue';
    import UserRegistrationUpdate from './components/user/UserRegistration/RegisterUserUpdate.vue';

/**
 * PUBLIC MODULE COMPONENTS
 */

    /**
     * Public module common components
     */
    import PublicHeader from './components/public/layout/header.vue';
    import PublicFooter from './components/public/layout/footer.vue';

    /**
     * Get coverage form for frontend user
     */
    import GetCoveragePublic from './components/public/getcoverage/index.vue';

    /**
     * get coverage by using organization URL
     */
    import PublicOrganization from './components/public/organization/index.vue';

const app = createApp({});

/* Register Global Properties (Fix for `custom.js`) */
    registerGlobalProperties(app); // Pass app instance to custom.js

/**
 * Dashboards to add the HTML code
 */
    app.component('admin-dashboard-design', AdminDashboardDesign);

/**
 * GLOBAL COMPONENTS
*/
    /**
     * Multiselect component
    */
        app.component('multiselect', Multiselect);

    /**
     * Global Components
     */
        app.component('alert-component', Alert);
        app.component('delete-alert', DeleteAlert);
        app.component('sidebar-animation-loader', SideBarAnimationLoader);
        app.component('app-component', App);

        /* Recent Insured Claims */
        app.component('dash-recent-claims', DashRecentClaims);
        /* Most recent claim status changed devices */
        app.component('dash-recent-status-change-claims', DashRecentStausChangeClaims);
        /* Recent Repair Requests */
        app.component('dash-recent-repairs', DashRecentRepairs);
        /* Recent Devices */
        app.component('dash-recent-devices', DashRecentDevices);
        /** Recent Support Ticket */
        app.component('dash-recent-support-ticket', DashRecentSupportTickets);

        /** Covered & Uncovered Device Chart */
        app.component('dash-covered-uncoverd-devices-chart', DashDevicesChart);
        /** Claim & Repair Chart */
        app.component('dash-claim-repairs-chart', DashClaimRepairChart);
        /** Claim & Repair Chart according to the month */
        app.component('dash-claim-repairs-chart-month', DashClaimRepairMonthChart);
        /** To show the errors globaly */
        app.component('ErrorMessage', ErrorMessage);

/**
 * ADMIN MODULE COMPONENTS
 */
    /** components for the admin dashboard */
        app.component('pagination', Pagination);
        app.component('admin-dash-header', AdminDashHeader);
        app.component('admin-dash-footer', AdminDashFooter);
        app.component('admin-dash-sidebar', AdminDashSideBar);
        app.component('admin-dash-users-chart', AdminDashUserChart);
        app.component('admin-dash-notification', AdminNotificationFeed);

    /** components for admin profile page */
        app.component('admin-profile-page', AdminProfilePage);

    /** Components for user role pages */
        app.component('user-role-list', UserRoleList);

    /** Components for user route pages */
        app.component('user-route-list', UserRouteList);

    /** Component for the role setting page */
        app.component('admin-role-setting', AdminRoleSetting);

    /** Component for user route permission pages */
        app.component('user-route-permission-list', UserRoutePermissionList);

    /** Components of organization module */
        app.component('organization-list', AdminOrganizarionList);
        app.component('organization-create', AdminOrganizarionCreate);
        app.component('organization-update', AdminOrganizarionUpdate);

    /** Componets for device, family, brand , model, plan */
        app.component('device-family', DeviceFamilyMaster);
        app.component('device-brand', DeviceBrandMaster);
        app.component('device-model', DeviceModelMaster);
        app.component('device-master-index', DeviceMasterIndex);
        app.component('device-master-create', DeviceMasterCreate);
        app.component('device-master-details', DeviceMasterDetails);
        app.component('device-master-update', DeviceMasterUpdate);
        app.component('device-plan-index', DevicePlanMasterIndex);
        app.component('device-import-master-index', DeviceImportMasterIndex);
        /** Import Device CSV Progress */
        app.component('import-device-csv-progress', DeviceImportProgress);

    /** Components for the support ticket */
        app.component('admin-support-ticket', AdminSupportTicket);


    /** Component for device repair plans */
        app.component('repair-plan-index', RepairPlanMaster);

    /** Components of Claim Reason module */
        app.component('claimreason-master', AdminClaimReasonMaster);

        /** Components of Repair Reason module */
        app.component('repairreason-master', AdminRepairReasonMaster);

    /** Components of shipping options module (Claims) */
        app.component('admin-shipping-options', AdminShippingOptions);

    /** Components of shipping options module (Repairs) */
        app.component('admin-repairs-shipping-options', AdminRepairsShippingOptions);

    /** Component of device default plan master */
        app.component('default-device-plan-index', DefaultDevicePlanMasterIndex);

    /** Components for track claim, track repair, and shipping supplies */
        app.component('admin-track-claim', TrackClaim);
        app.component('admin-file-claim', AdminFileClaim);
        app.component('admin-update-claim', AdminUpdateClaim);
        app.component('admin-repair-request', AdminRepairRequest);
        app.component('admin-track-repair', TrackRepair);

        app.component('admin-shipping-supplies', AdminShippingSupplies);
        app.component('admin-supply-boxes', AdminSupplyBoxs);

    /** Component for zoho setting in Admin Dashboard */
        app.component('admin-zoho-setting', AdminZohoSetting);

    /** Component for stripe setting in Admin Dashboard */
        app.component('admin-stripe-setting', AdminStripeSetting);

    /** Component for smtp2Go setting in Admin Dashboard */
        app.component('admin-smtp2go-setting', AdminSmtp2GoSetting);

    /** Component for user registration in Admin Dashboard */
        app.component('admin-user-registartion-index', AdminUserRegistrationIndex);
        app.component('admin-user-registartion-update', AdminUserRegistrationUpdate);

    /** Component for verify coverage in Admin Dashboard */
        app.component('admin-verify-coverage', AdminVerifyCoverage);

    /** Components for the login logs */
        app.component('admin-login-logs', AdminLoginLogs);

    /** Components for notifications */
    app.component('admin-notifications', AdminNotificationsIndex);

    /** Components for transactions */
    app.component('admin-transactions', AdminTransactionIndex);

    /** Components for admin reports */
        app.component('admin-reports', AdminReports);
        app.component('admin-device-pie-chart-reports', AdminDevicePieChartReports);
        app.component('admin-device-month-chart-reports', AdminDeviceMonthChartReports);
        app.component('admin-device-repair-month-chart-reports', AdminDeviceRepairMonthChartReports);
        app.component('admin-device-repair-pie-chart-reports', AdminDeviceRepairPieChartReports);
        app.component('admin-device-claim-month-chart-reports', AdminDeviceClaimMonthChartReports);
        app.component('admin-device-claim-pie-chart-reports', AdminDeviceClaimPieChartReports);
        app.component('admin-users-month-chart-reports', AdminUsersMonthChartReports);
        app.component('admin-users-pie-chart-reports', AdminUsersPieChartReports);

/**
 * USER MODULE COMPONENTS
 */
    /* User Layout Component */
        app.component('user-layout-header', UserHeader);
        app.component('user-layout-footer', UserFooter);
        app.component('user-layout-sidebar', UserSideBar);
        app.component('user-layout-quick-link', UserQuickLinks);
        app.component('user-pagination', UserPagination);
    /** User Profile Component */
    app.component('user-profile-page', UserProfilePage);


    /** User Pages */
        app.component('user-home', UserHome);
        app.component('user-layout-notification', UserNotificationFeed);

        /** Device listing and verify coverage */
            app.component('user-device-list', UserDeviceList);
            app.component('user-device-coverage', UserDeviceCoverage);
            app.component('user-renewal-device-list', UserRenewalDeviceList);
            app.component('user-earlyrenewal-device-list', UserEarlyRenewalDeviceList);

        /** Components of Insured Claims */
            app.component('user-file-claim', UserFileClaim);
            app.component('user-track-claims', UserTrackClaims);
            app.component('user-update-claims', UserUpdateClaims);

        /** Components of Uninsured repairs */
            app.component('user-repair-request', UserRepairRequest);
            app.component('user-track-repair', UserTrackRepairs);

        /** Components for shipping supplies */
            app.component('user-shipping-supplies-index', UserShipingSuppliesIndex);
            app.component('user-shipping-supplies-create', UserShipingSuppliesCreate);

        /** Components for notifications */
         app.component('user-notifications', UserNotificationsIndex);

        /** Components for transactions */
        app.component('user-transactions', UserTransactionIndex);

        /** Components for user-registartions */
        app.component('user-hod-director-registration-index', UserRegistrationIndex);
        app.component('user-hod-director-registration-update', UserRegistrationUpdate);

        /** Components for support ticket */
        app.component('user-support-ticket-create', UserSupportTicketCreate);

/**
 * PUBLIC COMPONENTS
 */
    /** Public layout components */
        app.component('public-header', PublicHeader);
        app.component('public-footer', PublicFooter);

    /** Get Coverage form component for frontend users */
        app.component('get-coverage-public', GetCoveragePublic);

    /** get coverage by using the organization URL's */
        app.component('public-organization', PublicOrganization);

app.mount('#app');
