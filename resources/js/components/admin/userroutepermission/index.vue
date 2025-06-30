<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0   ">
            <div class="card-body">
                <h4 class=" border-bottom  pb-2 coman_main_heading">Roles and Permissions</h4>

                <!-- Button and search section -->
                <div class="d-flex justify-content-between flex-wrap align-items-center ">
                    <!-- Left Side Button -->
                    <div class="d-flex align-items-center gap-3 mt-2 ">
                        <button data-bs-toggle="modal" data-bs-target="#smanagePermission" @click="managePermissionClick" class="btn blogal_pbtn_padding bg_blue def_14_size text-white d-flex align-items-center gap-10 rounded">
                        <img :src="profileIc" width="22" height="22"> Manage Permissions </button>
                    </div>

                    <!-- Search filter -->
                    <div class="d-flex justify-content-end flex-wrap search_options mt-2 align-items-center gap-3">
                        <!-- Search by role -->
                        <select v-model="SelectRole" class="form-control w-auto def_14_size" @change="getRoutePermissions">
                            <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Manage Route Permissions Model -->
                <div  id="smanagePermission" class="modal fade" ref="managePermission">
                    <div class="modal-dialog modal-xl modal-dialog-centered">

                        <!-- Modal content-->
                        <div class="modal-content onewhitebg">
                        <div class="modal-header justify-content-between align-items-center">
                            <h4 class="def_20_size modal-title d-flex align-items-center gap-10 my-0 ">
                                <svg width="30" height="30" id="fi_13430133" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><g fill="#293a4c"><g><path d="m32 57.5h-21c-4.13574 0-7.5-3.36426-7.5-7.5v-22c0-4.13574 3.36426-7.5 7.5-7.5h30c4.13574 0 7.5 3.36426 7.5 7.5v7c0 .82812-.67139 1.5-1.5 1.5s-1.5-.67188-1.5-1.5v-7c0-2.48145-2.01855-4.5-4.5-4.5h-30c-2.48145 0-4.5 2.01855-4.5 4.5v22c0 2.48145 2.01855 4.5 4.5 4.5h21c.82861 0 1.5.67188 1.5 1.5s-.67139 1.5-1.5 1.5z"></path><path d="m19 23.5c-.82861 0-1.5-.67188-1.5-1.5v-4.5c0-4.41113 3.58887-8 8-8 3.16357 0 6.03613 1.86914 7.31787 4.7627.36035.81445 1.19629 1.25586 2.02783 1.06934.55469-.12207 1.00732-.49023 1.24268-1.00781.23291-.51367.2124-1.09082-.05713-1.58301-2.11035-3.84961-6.146-6.24121-10.53125-6.24121-6.6167 0-12 5.38281-12 12v3.5c0 .82812-.67139 1.5-1.5 1.5s-1.5-.67188-1.5-1.5v-3.5c0-8.27148 6.729-15 15-15 5.48145 0 10.5249 2.98828 13.16211 7.7998.72754 1.32812.78516 2.88184.1582 4.26367-.62891 1.38574-1.84131 2.36914-3.3252 2.69824-2.21045.48535-4.48926-.68164-5.42041-2.78418-.80127-1.80859-2.59717-2.97754-4.57471-2.97754-2.75684 0-5 2.24316-5 5v4.5c0 .82812-.67139 1.5-1.5 1.5z"></path><path d="m26 40.97363c-3.43896 0-6.23682-2.79785-6.23682-6.23633 0-3.43945 2.79785-6.2373 6.23682-6.2373s6.23682 2.79785 6.23682 6.2373c0 3.43848-2.79785 6.23633-6.23682 6.23633zm0-9.47363c-1.78467 0-3.23682 1.45215-3.23682 3.2373 0 1.78418 1.45215 3.23633 3.23682 3.23633s3.23682-1.45215 3.23682-3.23633c0-1.78516-1.45215-3.2373-3.23682-3.2373z"></path><path d="m26 49.5c-.82861 0-1.5-.67188-1.5-1.5v-8.52637c0-.82812.67139-1.5 1.5-1.5s1.5.67188 1.5 1.5v8.52637c0 .82812-.67139 1.5-1.5 1.5z"></path></g><g><path d="m47 60.5c-7.44385 0-13.5-6.05566-13.5-13.5s6.05615-13.5 13.5-13.5 13.5 6.05566 13.5 13.5-6.05615 13.5-13.5 13.5zm0-24c-5.78955 0-10.5 4.70996-10.5 10.5s4.71045 10.5 10.5 10.5 10.5-4.70996 10.5-10.5-4.71045-10.5-10.5-10.5z"></path><path d="m45.25 52.5c-.38379 0-.76758-.14648-1.06055-.43945l-4.5-4.49902c-.58594-.58594-.58594-1.53516 0-2.12109.58496-.58594 1.53516-.58594 2.12109 0l3.43945 3.43848 6.93945-6.93945c.58594-.58594 1.53516-.58594 2.12109 0s.58594 1.53516 0 2.12109l-8 8c-.29297.29297-.67676.43945-1.06055.43945z"></path></g></g></svg>
                                <span>Manage Role Permission</span>
                            </h4>
                            <button type="button" class="btn-close" id="managePermissionClose" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body onewhitebg">
                            <div class="" style="min-height: 250px;">
                            <!-- Role Selection -->
                            <div class="mb-6">
                                <label class="form-label def_18_size fw-bold mt-3"> Role Name <span class="text-danger">*</span> </label>
                                <multiselect id="tagging" v-model="selected_role" placeholder="Search or add role" label="name" track-by="id" @select="handleSelectRole()" @remove="handelRemoveRole()" :options="roles" :multiple="false" :taggable="false" selectLabel="" deselectLabel=""></multiselect>
                                <small v-if="insertValidations.role" ><ErrorMessage :msg="insertValidations.role"></ErrorMessage></small>
                            </div>

                            <!-- Permissions Selection -->
                            <div v-if="groupNames && groupNames.length > 0">
                                <label class="form-label def_18_size fw-bold">Select Permissions</label>
                                <small v-if="insertValidations.selected_routes" ><ErrorMessage :msg="insertValidations.selected_routes"></ErrorMessage></small>
                                <div class="options_grids_rout">
                                    <div v-for="group in groupNames" :key="group.id" class="mb-6 wrapper_r">
                                        <!-- Group Name -->
                                        <p class=" mb-3 def_16_size fw-bold border-bottom pb-2">{{ group.groupName }}</p>

                                        <!-- Radio Options (Including "None") -->
                                        <div class=" radio-wrapper noselect">
                                            <!-- "None" Option -->
                                            <div class="radio-option">
                                                <input :id="group.groupName + 'None'" type="radio" :name="'permission_' + group.id" value="None" v-model="updatePermission[group.groupName]" class="" />
                                                <label :for="group.groupName + 'None'" >None</label>
                                            </div>
                                            <!-- Other Access Types -->
                                            <div v-for="accessType in group.accessTypes" :key="accessType" class="radio-option">
                                                <input :id="group.groupName + accessType"  type="radio" :name="'permission_' + group.id" :value="accessType" v-model="updatePermission[group.groupName]" class="" />
                                                <label :for="group.groupName + accessType" >{{ accessType }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="modal-footer onewhitebg justify-content-between">

                            <button @click="updateRoutePermission" class="btn bg_blue text-white ">
                                Save
                            </button>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Manage Route Permissions Modal End -->

                <!-- Table to show the Routepermission records -->
                <div class="table-responsive mt-3  ">
                    <!-- Table to show the Routepermission records -->
                    <table class="table table-bordered table-hover def_14_size mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Role Name</th>
                                <th>Group Name</th>
                                <th width="140">Access Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="routePermissions.length !== 0" v-for="(role, roleName) in routePermissions" :key="roleName">
                                <td class="align-middle">{{ roleName }}</td>
                                <td class="p-0" colspan="2">
                                    <table class="table table-bordered table-hover inner_table">
                                        <tbody>
                                            <tr v-for="(groups, accessName, accessIndex) in role" :key="accessName">
                                                <td>{{ groups ? groups : "N/A" }}</td>
                                                <td class="last_td" width="140">{{ accessName }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="4">No Permission Found!</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        roles: {
            type: Object
        },
        routespermissions: {
            type: Object
        }
    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,

            /** To store the data for the manage permissions */
            selected_role: '',
            groupNames: [],
            updatePermission: {},
            updateRoleData: '',

            /** To store the data while creating a new permission */
            newPermission: {
                role_id: []
            },
            selectedPermission: [], /* Stores selected option */

            /** To store the validation message while creating new route permissions */
            insertValidations: {},

            SelectRole: 1,

            routePermissions: this.routespermissions,
        };
    },
    methods: {

        /** on click of manage permission */
        managePermissionClick() {
            this.insertValidations = {};
            this.selected_role = '';
            this.groupNames = [];
        },

        /** To get the route groups based on the role type */
        handleSelectRole(){
            show_ajax_loader();
            let role_type = this.selected_role.role_type;
            let groups = this.$routeGroups;
            this.groupNames = groups.filter(group => group.role_type == role_type) || null;

            /** called a function to get the permissions of the selected role */
            this.editRoutePermission(this.selected_role.id);
            hide_ajax_loader();
        },

        /** To remove the group when the role is removed or disselected */
        handelRemoveRole(){
            show_ajax_loader();
            this.groupNames = [];
            hide_ajax_loader();
        },

        /* To list the records by pagination */
        async getRoutePermissions(page = 1, role = this.SelectRole) {
            try{
                show_ajax_loader();
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/route-permission?roleId=${role}&page=${page}`);
                if (response && response.data) {
                    if (response.data.success == true) {
                        if ( response.data.routePermission) {
                            this.routePermissions = response.data.routePermission;
                            hide_ajax_loader();
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = "Something went wrong, Please try again later.";
                        }
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
            catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        4000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        /* On click of edit button */
        async editRoutePermission(id) {
            show_ajax_loader();
            try {
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/route-permission/edit/${id}`);
                if (response.data.success == true) {

                    let routePermissionData = response.data.editData;
                    this.updateRoleData = this.roles.find(role => role.id == this.SelectRole) || '';

                    /* Clear any existing selections */
                    this.updatePermission = {};

                    /* Set "View" permission for groups in the View array */
                    if (routePermissionData.View && routePermissionData.View.length > 0) {
                        routePermissionData.View.forEach(groupName => {
                            this.updatePermission[groupName] = "View";
                        });
                    }

                    /* Set "All" permission for groups in the All array */
                    if (routePermissionData.All && routePermissionData.All.length > 0) {
                        routePermissionData.All.forEach(groupName => {
                            this.updatePermission[groupName] = "All";
                        });
                    }
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        3000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    hide_admin_popup('#managePermissionClose');
                }
            }
        },

        /* Update the permissions */
        async updateRoutePermission(e) {
            e.preventDefault();
            show_ajax_loader();
            this.insertValidations = {};
            let validationPassed = true;
            if(!this.selected_role){
                this.insertValidations.role = 'Please select the role.';
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                let route_data = Object.entries(this.updatePermission).map(([group, access]) => {
                    return {
                        'groupName': group,
                        'accessType': access
                    };
                });

                let role_id = this.selected_role.id;

                /* Object to store the data for the permission */
                const permissionData = {
                    selected_routes: route_data,
                    role_id: role_id,
                };

                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/route-permission/update`, permissionData);
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    hide_admin_popup('#managePermissionClose');
                    /* Fetch/update data */
                    this.getRoutePermissions();
                } else if(response.data.errors){
                    if(response.data.errors.role_id){
                        this.insertValidations.role = 'Please select the role.';
                        hide_ajax_loader();
                        return;
                    }
                    if(response.data.errors.selected_routes){
                        this.insertValidations.selected_routes = 'Please select atleast one route group.';
                        hide_ajax_loader();
                        return;
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    /* Hide the modal */
                    hide_admin_popup('#managePermissionClose');
                }
            }
            catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    /* Hide the modal */
                    hide_admin_popup('#managePermissionClose');
                }
            }
        },
    },
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>