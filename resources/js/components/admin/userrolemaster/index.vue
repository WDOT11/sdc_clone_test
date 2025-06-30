<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0  ">
            <div class="card-body">
                <h4 class="mb-3 border-bottom pb-2  coman_main_heading">Roles</h4>

                <!-- Filter and show add model button -->
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3  ">
                    <button data-bs-toggle="modal" data-bs-target="#rollsModal" class="btn blogal_pbtn_padding bg_blue text-white  d-flex align-items-center gap-10  def_14_size  wmax">
                        <img :src="profileIc" width="22" height="22"> Add
                    </button>

                    <!--  Filter section -->
                    <div class="d-flex justify-content-between flex-wrap search_options align-items-start gap-3">
                        <div>
                            <!-- Search by role name -->
                            <input type="text" v-model="search_name" :class="['form-control def_14_size w-auto', {'is-invalid': validationErrors.serachRoleName}]" placeholder="Filter by Role Name">
                            <small v-if="validationErrors.serachRoleName" ><ErrorMessage :msg="validationErrors.serachRoleName"></ErrorMessage></small>

                        </div>
                        <!-- buttons -->
                        <div class="d-flex gap-2 ">
                            <button class="btn bg_blue def_14_size d-flex align-items-center gap-10 text-white blogal_pbtn_padding" @click="handleSearch"><img :src="searchIc" width="20" height="20"> Search</button>
                            <button class="btn customm_reset_btn def_14_size blogal_pbtn_padding" @click="cancelSearch">Clear</button>
                        </div>
                    </div>
                </div>

                <!-- Add Role Modal -->
                <div class="modal fade" id="rollsModal">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <div class="d-flex align-items-center gap-10 ">
                                    <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                                    <h4 class="def_22_size modal-title mb-0">Add New Role</h4>
                                </div>
                                <button type="button" class="btn-close" id="addRoleClose" @click="closeAddModal" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">
                                <div class="addModalRoleName">
                                    <!-- Role name input -->
                                    <label class="form-label ">Role Name <span class="text-danger">*</span></label>
                                    <input v-model="roleName" type="text"  placeholder="Enter Role name" :class="['form-control def_14_size',{ 'is-invalid': validationErrors.roleName },]" />
                                    <small v-if="validationErrors.roleName" ><ErrorMessage :msg="validationErrors.roleName"></ErrorMessage></small>
                                </div>

                                <div class="addModalRoleType">
                                    <!-- Role type -->
                                    <label class="form-label mt-3">Role Type</label>
                                    <select v-model="roleType" @change="handleRoleType" :class="['form-control def_14_size', {'is-invalid': validationErrors.roleType}]">
                                        <option value="1">Admin</option>
                                        <option value="2">User</option>
                                    </select>
                                    <small v-if="validationErrors.roleType"><ErrorMessage :msg="validationErrors.roleType"></ErrorMessage></small>
                                </div>

                                <div class="addModalRoleFor">
                                    <!-- Role For -->
                                    <label class="form-label mt-3">Role For <span class="text-danger">*</span></label>
                                    <select v-model="roleFor" :class="['form-control def_14_size', {'is-invalid': validationErrors.roleFor}]">
                                        <option value="">Select</option>
                                        <option v-for="(role,key) in roleForByType" :value="role.value">{{ role.name }}</option>
                                    </select>
                                    <small v-if="validationErrors.roleFor" ><ErrorMessage :msg="validationErrors.roleFor"></ErrorMessage></small>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg">
                                <button @click="saveRoles" class="btn bg_blue text-white  "> Save </button>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Add Role Modal End -->

                <!-- Edit Role Modal -->
                <div  class="modal fade" id="editModal">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <div class="d-flex align-items-center gap-10 ">
                                    <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                        <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                                        <span>Edit Role</span></h4>
                                </div>
                                <button type="button" class="btn-close" id="roleEditClose" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">

                                <!-- Role Name -->
                                <div class="editRoleName">
                                    <label class="form-label ">Role Name <span class="text-danger">*</span></label>
                                    <input v-model="updated_role_name" type="text" class="" placeholder="Enter Role name" :class="['form-control def_14_size',{ 'is-invalid': validationErrors.roleName },]" />
                                    <small v-if="validationErrors.roleName" ><ErrorMessage :msg="validationErrors.roleName"></ErrorMessage></small>
                                </div>

                                <div class="editRoleType">
                                    <!-- Role type -->
                                    <label class="form-label mt-3">Role Type </label>
                                    <select v-model="updated_role_type" @change="handleRoleTypeOnUpdate" :class="['form-control def_14_size', {'is-invalid': validationErrors.role_type}]">
                                        <option value="1">Admin</option>
                                        <option value="2">User</option>
                                    </select>
                                    <!-- Show validation error for roleType -->
                                    <small v-if="validationErrors.role_type" class="invalid-feedback"> <ErrorMessage :msg="validationErrors.role_type"></ErrorMessage></small>
                                </div>

                                <div class="editRoleFor">
                                    <!-- Role For -->
                                    <label class="form-label mt-3">Role For <span class="text-danger">*</span></label>
                                    <select v-model="updated_role_for" :class="['form-control def_14_size', {'is-invalid': validationErrors.role_for}]">
                                        <option value="">Select</option>
                                        <option v-for="(role,key) in updatedRoleForByType" :value="role.value">{{ role.name }}</option>
                                    </select>
                                    <small v-if="validationErrors.role_for" ><ErrorMessage :msg="validationErrors.role_for"></ErrorMessage></small>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg justify-content-between">
                                <button @click="updateRole" class="btn bg_blue text-white  ">
                                    Update
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Edit Role Modal End -->

                <!-- Table to show the Role data -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size">
                        <thead class="table-light">
                            <tr>
                                <th width="60" >#</th>
                                <th >Name</th>
                                <th >Role Type</th>
                                <th >Role For</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="roles.length > 0" v-for="(role, index) in roles" :key="role.id">
                                <td> {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }} </td>
                                <td> {{ role.name }} </td>
                                <td>
                                    <span v-if="role.role_type == 1">Admin</span>
                                    <span v-else >User</span>
                                </td>
                                <td> {{ $roleFor.find(r => r.value == role.role_for)?.name || 'Unknown' }} </td>
                                <td>
                                    <div class="d-flex align-items-center gap-20">
                                        <button  data-bs-toggle="modal" data-bs-target="#editModal" @click="editRole(role.id)" class=" globaleditbtn extrabtns fw-semibold themetextcolor   rounded-pill text-decoration-none d-flex align-items-center def_14_size gap-1   editBtn">
                                            <img  @click="editRole(role.id)" data-bs-toggle="modal" data-bs-target="#editModal" :src="editIc" class="blobal_img_w">
                                            <span class="edittext_Gl">Edit</span>
                                        </button>
                                        <!-- <button @click="confirmDelete(role.id)" class="bg-transparent text-white rounded">
                                            <img :src="deleteIc" width="28" height="28">
                                        </button> -->
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="5"> No role found! </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5" :paginate="getRoles"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
export default {

    props: {
        rolesdata:{
            type:Object
        },
        paginationdata:{
            type: Object
        },
    },

    data() {
        return {
            /** Icons */
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,

            pagination: this.paginationdata,
            roles: this.rolesdata.data,

            /** Used to manage the searching */
            search_name : '',
            search_role_type: '',

            /** Used to manage the role creation */
            roleName: '',
            roleType: '1',
            roleFor: '',
            roleForByType: [],

            /** Used to manage the edit role functionality */
            updated_role_name: '',
            updated_role_type: '',
            updated_role_for: '',
            role_id: '',
            updatedRoleForByType: [],

            /* Store validation errors here */
            validationErrors: {}
        };
    },

    created() {
        this.handleRoleType();
    },

    methods: {

        /** TO close the add role model and make the form empty */
        closeAddModal(event){
            /* Remove focus from the close button to avoid focus staying on a hidden element */
            if (event && event.target) {
                event.target.blur();
            }
            this.roleName = '';
            this.roleType = '1';
            this.roleFor = '';
            this.roleForByType = [];
            this.validationErrors = {};
        },

        /** To handel the role type (add model) */
        handleRoleType(){
            show_ajax_loader();
            this.roleFor = '';
            let roleType = this.roleType;
            let roleFor =  this.$roleFor;
            this.roleForByType = roleFor.filter(role => role.role_type == roleType) || null;
            hide_ajax_loader();
        },

        /** To handel the role type (update model) */
        handleRoleTypeOnUpdate(){
            show_ajax_loader();
            this.updated_role_for = '';
            let updated_role_type = this.updated_role_type;
            let roleFor =  this.$roleFor;
            this.updatedRoleForByType = roleFor.filter(role => role.role_type == updated_role_type) || null;
            hide_ajax_loader();
        },

        /** Handle Serach */
        handleSearch() {
            const trimmedName = this.search_name?.trim();
            /* Only call getRoles if search_name is not empty */
            if (trimmedName) {
                if (trimmedName.length < 2) {
                    this.validationErrors.serachRoleName = `Please enter at least 2 characters long.`;
                } else {
                    this.validationErrors = {};
                    this.getRoles(1, trimmedName);
                }
            } else {
                this.validationErrors.serachRoleName = `Role name can't be empty.`;
            }
        },

        /** To cancel the search */
        async cancelSearch(){
            show_ajax_loader();
            this.search_name = '';
            this.search_role_type = '';
            this.validationErrors = {};
            this.getRoles();
            hide_ajax_loader();
        },

        /** On click of edit button */
        async editRole(role_id) {
            try{
                show_ajax_loader();
                this.validationErrors = {};
                this.role_id = role_id;

                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/roles/edit/${this.role_id}`);
                if(response.data.success == true){
                    let roleFor =  this.$roleFor;
                    this.updated_role_name = response.data.editData.name;
                    this.updated_role_type = response.data.editData.role_type;
                    this.updated_role_for = response.data.editData.role_for;
                    this.updatedRoleForByType = roleFor.filter(role => role.role_type == this.updated_role_type) || null;
                    hide_ajax_loader();
                } else {
                    hide_admin_popup('#roleEditClose');
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
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
                    hide_admin_popup('#roleEditClose');
                }
            }
        },

        /** To save the role */
        async saveRoles(){
            let validationPassed = true;
            show_ajax_loader();

            this.validationErrors = {};
            /* Validate Roles */
            if (!this.roleName) {
                this.validationErrors.roleName = `Role name can't be empty.`;
                validationPassed = false;
            }
            if (this.roleName && this.roleName.length < 2) {
                this.validationErrors.roleName = `Role name must be at least 2 characters long.`;
                validationPassed = false;
            }

            if(!this.roleType){
                this.validationErrors.roleType = 'Please select Role type for the role.';
                validationPassed = false;
            }

            if(!this.roleFor){
                this.validationErrors.roleFor = 'Please select it.';
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                /* Clear previous validation errors*/
                this.validationErrors = {};

                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/roles/store`, {
                    name: this.roleName,
                    roleType: this.roleType,
                    roleFor: this.roleFor,
                });

                if (response.data.success == true) {

                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /** Make input field empty */
                    this.roleName = '';
                    this.roleFor = '';
                    /* Hide the modal */
                    hide_admin_popup('#addRoleClose');
                    /* Fetch/update data */
                    this.getRoles();

                } else if (response.data.errors ) {
                    if(response.data.errors.name){
                        this.validationErrors.roleName = response.data.errors.name[0];
                    }
                    if(response.data.errors.roleType){
                        this.validationErrors.roleType = response.data.errors.roleType[0];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    hide_admin_popup('#addRoleClose');
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
                    hide_admin_popup('#addRoleClose');
                }
            }
        },

        /** To update the role */
        async updateRole(){
            let validationPassed = true;
            show_ajax_loader();

            this.validationErrors = {};

            /* Validate Device Brand */
            if (!this.updated_role_name) {
                this.validationErrors.roleName = 'Please enter Role name.';
                validationPassed = false;
            }
            if (this.updated_role_name && this.updated_role_name.length < 2) {
                this.validationErrors.roleName = `Role name must be at least 2 characters long.`;
                validationPassed = false;
            }

            if(!this.updated_role_type){
                this.validationErrors.role_type = 'Please select Role type for the role.';
                validationPassed = false;
            }
            if(!this.updated_role_for){
                this.validationErrors.role_for = 'Please select it.';
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                /* Clear previous validation errors */
                this.validationErrors = {};
                let response = await axios.put(`${this.$userAppUrl}smarttiusadmin/roles/update/${this.role_id}`, {
                    name: this.updated_role_name,
                    roleType: this.updated_role_type,
                    roleFor: this.updated_role_for,
                });

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    hide_admin_popup('#roleEditClose');
                    /* Fetch/update data */
                    this.getRoles();

                } else if(response.data.errors && response.data.success == false) {
                    if(response.data.errors.name){
                        this.validationErrors.roleName = response.data.errors.name[0];
                    }
                    if(response.data.errors.roleType){
                        this.validationErrors.role_type = response.data.errors.roleType[0];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    hide_admin_popup('#roleEditClose');
                }
            } catch (error) {
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
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    hide_admin_popup('#roleEditClose');
                }
            }
        },


        /** To get the roles on page change */
        async getRoles(page = 1, searched_name = '') {
            try {
                show_ajax_loader();
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/roles?page=${page}${searched_name && searched_name !== "" ? `&name=${searched_name}` : ""}`);
                if (response && response.data) {
                    if (response.data.success == true) {
                        if ( response.data.roledata && response.data.roledata.data && response.data.pagination ) {
                            this.roles = response.data.roledata.data;
                            this.pagination = response.data.pagination;
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
            } catch (error) {
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

        /**
        confirmDelete(id) {
            this.$deleteAlertMessage.data.isDelete = false
            this.$deleteAlertMessage.data.isChangeStatus = true
            this.$deleteAlertMessage.data.itemId = id
            this.$deleteAlertMessage.data.message = 'Are you sure you want to change the status?'
            this.$deleteAlertMessage.data.callback = this.deleteRole
        },
        */

        /** On click of the delete button */
        async confirmDelete(id){
            this.$alertMessage.success = false;
            this.$alertMessage.message = 'This is under development.';
        },

        /**
        async deleteRole(id) {

            // Client-side validation
            let validationPassed = true;

            // Validate Device Brand
            if (!id) {
                validationPassed = false;
            }

            // Stop submission if validation fails
            if (!validationPassed) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
                return;
            }

            try {
                const response = await axios.delete(
                    `${this.$userAppUrl}smarttiusadmin/roles/delete/${id}`
                )
                if(response && response.data){
                    if (response.data.success == true) {
                        this.$alertMessage.success = true
                        this.$alertMessage.message = response.data.msg
                        setTimeout(
                            () => this.getRoles(),
                            5000
                        )
                    } else {
                        this.$alertMessage.success = false
                        this.$alertMessage.message = response.data.message
                    }
                } else {
                    this.$alertMessage.success = false
                    this.$alertMessage.message = response.data.message
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                        5000
                    );
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        }
        */
    },
};
</script>
