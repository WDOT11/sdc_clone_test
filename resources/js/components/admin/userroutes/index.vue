<template>
    <div class="container-fluid ">
        <div class="card onewhitebg border-0  mt_12">
            <div class="card-body">
            <h4 class=" mb-3 border-bottom pb-2 def_22_size">Routes</h4>


            <!-- Search filter -->


            <div class="d-flex justify-content-between flex-wrap align-items-start gap-3 ">
                <button @click="showAddModal = true" class="btn bg_blue text-white d-flex align-items-center gap-10 rounded  wmax">
                    <img :src="profileIc" width="24" height="24">
                    Add
            </button>
                <!-- Search by route type -->
                <div class="d-flex justify-content-between search_options flex-wrap align-items-start gap-3">
                <select class="form-control form-control w-auto" v-model="search_route_type">
                    <option value="" selected>Select by Route Type</option>
                    <option value="1">Admin</option>
                    <option value="2">User</option>
                    <option value="3">Public</option>
                </select>

                <!-- search by route status -->
                <select class="form-control form-control w-auto" v-model="search_access_type">
                    <option value="" selected>Select by Access Type</option>
                    <option value="1">View</option>
                    <option value="2">All</option>
                </select>

                <!-- search by group names -->
                <select class="form-control form-control w-auto" v-model="search_group_name">
                    <option value="" selected>Select the Group Name</option>
                    <option v-for="(group, index) in groupNames" :key="index" :value="group.groupName">
                        {{ group.groupName }}
                    </option>
                </select>

                <!-- buttons -->
                <div class="d-flex gap-2">
                    <button class="btn bg_blue d-flex align-items-center gap-10  text-white" @click="getRoutes"><img :src="searchIc" width="20" height="20"> Search</button>
                    <button class="btn btnblack" @click="cancelSearch">Clear</button>
                </div>
                </div>
            </div>

            <!-- Add Route Modal -->
            <div v-if="showAddModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                    <h3 class="text-lg font-semibold mb-4">Add New Route</h3>

                    <label class="block font-medium text-gray-700 mt-3 mb-1">Route Name</label>
                    <input v-model="routeName" type="text" class="w-full p-2 rounded mb-1" placeholder="Enter Route name" :class="['form-control',{ 'is-invalid': validationErrors.routeName },]" />
                    <small v-if="validationErrors.routeName"><ErrorMessage :msg="validationErrors.routeName"></ErrorMessage></small>

                    <label class="block font-medium text-gray-700 mt-3 mb-1">Route Type</label>
                    <select v-model="routeType" :class="['form-control', {'is-invalid': validationErrors.routeType}]">
                        <option value="1">Admin</option>
                        <option value="2">User</option>
                        <option value="3">Public</option>
                    </select>
                    <small v-if="validationErrors.routeType"><ErrorMessage :msg="validationErrors.routeType"></ErrorMessage></small>

                    <label class="block font-medium text-gray-700 mt-3 mb-1">Access Type</label>
                    <select v-model="accessType" :class="['form-control', {'is-invalid': validationErrors.accessType}]">
                        <option value="1">View</option>
                        <option value="2">All</option>
                    </select>
                    <small v-if="validationErrors.accessType"><ErrorMessage :msg="validationErrors.accessType"></ErrorMessage></small>

                    <label class="block font-medium text-gray-700 mt-3 mb-1">Group Name</label>
                    <select v-model="groupName" :class="[ 'form-control', { 'is-invalid': validationErrors.groupName }]">
                        <option value="" selected>Select</option>
                        <option v-for="(group, index) in groupNames" :key="index" :value="group">
                            {{ group.groupName }}
                        </option>
                    </select>
                    <small v-if="validationErrors.groupName" ><ErrorMessage :msg="validationErrors.groupName"></ErrorMessage></small>

                    <div class="flex justify-end mt-3">
                        <button @click="closeAddModal" class="btn btnblack text-white  rounded mr-2">
                            Cancel
                        </button>
                        <button @click="saveRouts" class="btn bg_blue text-white  rounded">
                            Save
                        </button>
                    </div>
                </div>
            </div>
            <!-- Add Route Modal End -->

            <!-- Edit Route Modal -->
            <div v-if="showEditModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                    <h3 class="text-lg font-semibold mb-4">Edit Route</h3>

                    <label class="block mt-2 mb-1">Route Name</label>
                    <input v-model="updated_route_name" type="text" class="w-full p-2 rounded mb-1" placeholder="Enter Route name" :class="['form-control',{ 'is-invalid': validationErrors.roleName },]" />
                    <small v-if="validationErrors.routeName" ><ErrorMessage :msg="validationErrors.routeName"></ErrorMessage></small>

                    <label class="block font-medium text-gray-700 mt-3 mb-1">Route Type</label>
                    <select v-model="updated_route_type" :class="['form-control', {'is-invalid': validationErrors.routeType}]">
                        <option value="1">Admin</option>
                        <option value="2">User</option>
                        <option value="3">Public</option>
                    </select>
                    <small v-if="validationErrors.routeType" ><ErrorMessage :msg="validationErrors.routeType"></ErrorMessage></small>

                    <label class="block font-medium text-gray-700 mt-3 mb-1">Access Type</label>
                    <select v-model="updated_access_type" :class="['form-control', {'is-invalid': validationErrors.accessType}]">
                        <option value="1">View</option>
                        <option value="2">All</option>
                    </select>
                    <small v-if="validationErrors.accessType" ><ErrorMessage :msg="validationErrors.accessType"></ErrorMessage></small>

                    <label class="block font-medium text-gray-700 mt-3 mb-1">Group Name</label>
                    <select v-model="updated_group_name" :class="[ 'form-control', { 'is-invalid': validationErrors.groupName }]">
                        <option value="" selected>Select</option>
                        <option v-for="(group, index) in groupNames" :key="index" :value="group.groupName">
                            {{ group.groupName }}
                        </option>
                    </select>
                    <small v-if="validationErrors.groupName" ><ErrorMessage :msg="validationErrors.groupName"></ErrorMessage></small>

                    <!-- Buttons -->
                    <div class="flex justify-end mt-4">
                        <button @click="closeEditModal" class="btn btnblack   rounded mr-2">
                            Cancel
                        </button>
                        <button @click="updateRoute" class="btn bg_blue text-white  rounded">
                            Save
                        </button>
                    </div>
                </div>
            </div>
            <!-- Edit Route Modal End -->

            <!-- Table to list the routes -->
            <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th  width="60" class="px-4">#</th>
                        <th class="px-4">Route Name</th>
                        <th class="px-4">Route Type</th>
                        <th class="px-4">Access Type</th>
                        <th class="px-4">Group Name</th>
                        <th class="px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="routes.length > 0" v-for="(route, index) in routes" :key="route.id">
                        <td class="border border-gray-300 px-4 ">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                        <td class="border border-gray-300 px-4 ">{{ route.route_name }}</td>
                        <td class="border border-gray-300 px-4 ">
                            {{ route.route_type == 1 ? 'Admin' : (route.route_type == 2 ? 'User' : 'Public') }}

                        </td>
                        <td class="border border-gray-300 px-4 ">
                            <span v-if="route.access_type == 1">View</span>
                            <span v-else>All</span>
                        </td>
                        <td class="border border-gray-300 px-4 ">
                            <span>{{ route.group_name }}</span>
                        </td>
                        <td class="border border-gray-300 px-4 ">
                            <div class="d-flex align-items-center gap-20">
                                <button @click="editRoute(route.id)" class="globaleditbtn extrabtns bg-transparent text-white  rounded editBtn"> <img :src="editIc" width="35" height="35"> </button>
                                <button @click="confirmDelete(route.id)" class=" bg-transparent text-white rounded"><img :src="deleteIc" width="35" height="35"> </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="7">
                            No Route Found!
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="3" :paginate="getRoutes"></pagination>
    </div></div>
</div>
</template>
<script>
export default {
    props: {
        routesdata:{
            type:Object
        },
        paginationdata:{
            type: Object
        },
    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,

            showAddModal: false,
            showEditModal: false,

            groupNames: this.$routeGroups,

            /** To store the validations */
            validationErrors: {},

            /** Used to manage the search paramters */
            search_route_type: '',
            search_access_type: '',
            search_group_name:'',

             /** Used to manage the role creation */
             route_id: '',
             routeName: '',
             routeType: '1',
             accessType: '1',
             groupName: '',

            /** Used to manage the edit role functionality */
            updated_route_name: '',
            updated_route_type: '',
            updated_access_type: '',
            updated_group_name: '',

            pagination: this.paginationdata,
            routes:this.routesdata.data,
        }
    },
    methods: {

        /** To get the paginated records */
        async getRoutes( page = 1, search_route_type = this.search_route_type, search_access_type = this.search_access_type, search_group_name = this.search_group_name) {
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/routes?page=${page}${search_route_type && search_route_type !== "" ? `&route_type=${search_route_type}` : ""}${search_group_name && search_group_name !== "" ? `&group_name=${search_group_name}` : ""}${search_access_type && search_access_type !== "" ? `&access_type=${search_access_type}` : ""}`
                );
                if (response && response.data) {
                    if (response.data.success == true) {
                        if ( response.data.routedata && response.data.routedata.data && response.data.pagination ) {
                            this.routes = response.data.routedata.data;
                            this.pagination = response.data.pagination;
                        } else {
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = "Something went wrong, Please try again later.";
                        }
                    } else {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        5000
                    );
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

         /** To save the routes */
         async saveRouts(){
            let validationPassed = true;
            this.validationErrors = {};

            /* Client side validations */
            if (!this.routeName) {
                this.validationErrors.routeName = `Route name can't be empty.`;
                validationPassed = false;
            }

            if (!this.routeType) {
                this.validationErrors.routeType = `Please select route type.`;
                validationPassed = false;
            }

            if (!this.accessType) {
                this.validationErrors.accessType = `Please select access type.`;
                validationPassed = false;
            }

            if (!this.groupName) {
                this.validationErrors.groupName = `Please select group name.`;
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                return;
            }

            try {
                /* Clear previous validation errors*/
                this.validationErrors = {};

                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/routes/store`, {
                    routeName: this.routeName,
                    routeType: this.routeType,
                    accessType: this.accessType,
                    groupName: this.groupName.groupName,

                });

                if (response.data.success == true) {
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    this.showAddModal = false;

                    /** Make input field empty */
                    this.routeName = '';
                    this.groupName = '';

                    /* Fetch/update data */
                    this.getRoutes();

                } else if (response.data.errors ) {
                    if(response.data.errors.routeName){
                        this.validationErrors.routeName = response.data.errors.routeName[0];
                    }
                    if(response.data.errors.accessType){
                        this.validationErrors.accessType = response.data.errors.accessType[0];
                    }
                    if(response.data.errors.routeType){
                        this.validationErrors.routeType = response.data.errors.routeType[0];
                    }
                    if(response.data.errors.groupName){
                        this.validationErrors.groupName = response.data.errors.groupName[0];
                    }
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        5000
                    ); /* Small delay for smooth effect */

                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** To close the Add route model */
        async closeAddModal(){
            this.showAddModal = false;
            this.validationErrors = {};
            this.routeName = '';
            this.routeType = '1';
            this.accessType = '1';
            this.groupName = '';
        },

         /** On click of edit button */
         async editRoute(route_id) {
            try{
                this.showEditModal = true;
                this.route_id = route_id;
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/routes/edit/${this.route_id}`);
                if(response.data.success == true){
                    this.updated_route_name = response.data.editData.route_name;
                    this.updated_route_type = response.data.editData.route_type;
                    this.updated_access_type = response.data.editData.access_type;
                    this.updated_group_name = response.data.editData.group_name;
                } else {
                    this.showEditModal = false;
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        5000
                    );
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** To update the routes */
        async updateRoute(){
            let validationPassed = true;

            this.validationErrors = {};
            /* Client side validations */
            if (!this.updated_route_name) {
                this.validationErrors.routeName = `Route name can't be empty.`;
                validationPassed = false;
            }

            if (!this.updated_route_type) {
                this.validationErrors.routeType = `Please select route type.`;
                validationPassed = false;
            }

            if (!this.updated_access_type) {
                this.validationErrors.accessType = `Please select access type.`;
                validationPassed = false;
            }

            if (!this.updated_group_name) {
                this.validationErrors.groupName = `Please select group name.`;
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                return;
            }

            try {
                /* Clear previous validation errors */
                this.validationErrors = {};
                let response = await axios.put(`${this.$userAppUrl}smarttiusadmin/routes/update/${this.route_id}`, {
                    routeName: this.updated_route_name,
                    routeType: this.updated_route_type,
                    accessType: this.updated_access_type,
                    groupName: this.updated_group_name,
                });

                if (response.data.success == true) {
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    this.showEditModal = false;

                    /* Fetch/update data */
                    this.getRoutes();

                } else if(response.data.errors && response.data.success == false) {
                    if(response.data.errors.routeName){
                        this.validationErrors.routeName = response.data.errors.routeName[0];
                    }
                    if(response.data.errors.routeType){
                        this.validationErrors.routeType = response.data.errors.routeType[0];
                    }
                    if(response.data.errors.accessType){
                        this.validationErrors.accessType = response.data.errors.accessType[0];
                    }
                    if(response.data.errors.groupName){
                        this.validationErrors.groupName = response.data.errors.groupName[0];
                    }
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        5000
                    );
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** To close the edit route popup and make error field empty */
        async closeEditModal(){
            this.showEditModal = false;
            this.validationErrors = {};
        },

        /** Onclick of the change status */
        /**
        confirmDelete(id) {
            this.$deleteAlertMessage.data.isDelete = false
            this.$deleteAlertMessage.data.isChangeStatus = true
            this.$deleteAlertMessage.data.itemId = id
            this.$deleteAlertMessage.data.message = 'Are you sure you want to change the status?'
            this.$deleteAlertMessage.data.callback = this.deleteRoute
        },
        */
        confirmDelete(id) {
            this.$alertMessage.success = false;
            this.$alertMessage.message = 'This is under development.';
        },

        /** To delete route */
        /**
        async deleteRoute(id) {

            // Client-side validation
            let validationPassed = true;

            // Validate route
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
                const response = await axios.delete(`${this.$userAppUrl}smarttiusadmin/routes/delete/${id}`);

                if (response.data.success == true) {
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg
                    setTimeout(() => this.getRoutes(), 5000);
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
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
        },
        */
        async cancelSearch(){
            this.search_status = '';
            this.search_route_type = '';
            this.search_access_type = '';
            this.search_group_name = '';
            this.getRoutes();
        },
    }
}
</script>