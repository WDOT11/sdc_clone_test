<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0  ">
            <div class="card-body">
                <h4 class=" mb-3 border-bottom pb-2 coman_main_heading">Device Brand</h4>

                <!-- Search filters -->
                <div class="d-flex justify-content-between flex-wrap align-items-start  gap-3 mb-3">
                    <button data-bs-toggle="modal" data-bs-target="#DeviceBrand" class="btn blogal_pbtn_padding bg_blue wmax text-white d-flex align-items-center gap-10 def_14_size  ">
                        <img :src="profileIc" width="22" height="22"> Add New
                    </button>
                    <!-- Search by name -->
                    <div class="d-flex justify-content-between flex-wrap search_options align-items-start gap-3">
                    <input type="text" v-model="search_name" class="form-control w-auto def_14_size" placeholder="Filter by Name">

                    <!-- Search by family -->
                    <select v-model="search_device_family" class="form-control w-auto def_14_size">
                        <option value="" selected>Select Family</option>
                        <option v-for="family in familydata" :value="family.id">{{ family.name }}</option>
                    </select>

                    <!-- buttons -->
                    <div class="d-flex gap-2">
                        <button class="btn blogal_pbtn_padding bg_blue d-flex align-items-center def_14_size gap-10 text-white" @click="getbrandData"><img :src="searchIc" width="20" height="20"> Search</button>
                        <button class="btn customm_reset_btn def_14_size blogal_pbtn_padding" @click="cancelSearch">Clear</button>
                    </div>
                </div>
                </div>

                <!-- Add Device Brand Modal -->
                <div class="modal" id="DeviceBrand">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                        <!-- Modal Header -->
                        <div class="modal-header align-items-center">
                            <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                                <span>Add New Device Brand</span>
                            </h4>
                            <button type="button" class="btn-close" id="addDeviceBrandModalClose" @click="closeAddModal" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body onewhitebg">
                            <!-- <div v-if="showAddModal"> -->
                                <div>
                                    <label class="form-label " >Name <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter Brand Name" v-model="deviceBrandName" :class="['form-control', { 'is-invalid': validationErrors.brandName }]" required>
                                    <small v-if="validationErrors.brandName" ><ErrorMessage :msg="validationErrors.brandName"></ErrorMessage> </small>
                                </div>
                                <div>
                                    <!-- Device Brand Family -->
                                    <label class="form-label mt-3" >Device Family <span class="text-danger">*</span></label>
                                    <select v-model="deviceFamily" id="" :class="['form-control', { 'is-invalid': validationErrors.deviceFamily }]" required>
                                        <option value="">Select Family</option>
                                        <option v-for="family in familydata" :value="family.id">{{ family.name }}</option>
                                    </select>
                                    <small v-if="validationErrors.deviceFamily" ><ErrorMessage :msg="validationErrors.deviceFamily"></ErrorMessage></small>
                                </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer onewhitebg">
                            <!-- <button data-bs-dismiss="modal" @click="closeAddModal" class="btn btnblack text-white  rounded mr-2"> Cancel </button> -->
                            <button @click="saveBrand" class="btn bg_blue text-white  "> Save </button>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Add Device Brand Modal End -->

                <!-- Edit Device Brand Modal -->
                <div class="modal" id="EditDeviceBrand">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content onewhitebg">

                        <!-- Modal Header -->
                        <div class="modal-header align-items-center">
                            <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                                <span>Edit Device Brand</span>
                            </h4>
                            <button type="button" class="btn-close" id="editDeviceBrandModalClose" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body onewhitebg">
                           <!-- <div v-if="showEditModal"> -->
                                 <!-- Device Brand Name -->
                                <label class="form-label" >Name <span class="text-danger">*</span></label>
                                <input type="text" v-model="editdeviceBrandName" :class="['form-control', { 'is-invalid': validationErrors.brandName }]" placeholder="Enter Device Brand Name" required>
                                <small v-if="validationErrors.brandName" ><ErrorMessage :msg="validationErrors.brandName"></ErrorMessage></small>

                                <!-- Device Brand Family -->
                                <label class="form-label mt-3" >Device Family <span class="text-danger">*</span></label>
                                <select v-model="editdeviceFamily" id="" :class="['form-control', { 'is-invalid': validationErrors.deviceFamily }]" required>
                                    <option value="">Select Family</option>
                                    <option v-for="family in familydata" :value="family.id" :selected="editdeviceFamily == family.id ? true : false">{{ family.name }}</option>
                                </select>
                                <small v-if="validationErrors.deviceFamily" ><ErrorMessage :msg="validationErrors.deviceFamily"></ErrorMessage></small>
                           </div>
                        <!-- </div> -->

                        <!-- Modal footer -->
                        <div class="modal-footer onewhitebg">
                            <!-- <button data-bs-dismiss="modal" @click="closeEditModal" class="btn btnblack text-white  rounded mr-2"> Cancel </button> -->
                            <button @click="updateBrand" class="btn bg_blue text-white "> Save </button>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Edit Device Brand Modal End -->

                <!-- Table to show the Device Brand records -->
                <div class="table-responsive">
                <table class="table table-bordered table-hover def_14_size">
                    <thead class="table-light">
                        <tr>
                            <th  width="60" >#</th>
                            <th >Brand Name</th>
                            <th >Device Family</th>
                            <th >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="brandData.length > 0" v-for="(data, index) in brandData" :key="data.id">
                            <td> {{(paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page  - 1) * paginationData.per_page +  index + 1}} </td>
                            <td> {{ data.name }} </td>
                            <td> {{ data.device_family_name ?? '-' }} </td>
                            <td>
                                <div class="d-flex align-items-center gap-20">
                                <button data-bs-toggle="modal" data-bs-target="#EditDeviceBrand" @click="editDeviceBrand(data.id)" class="themetextcolor globaleditbtn extrabtns  rounded-pill d-flex align-items-center def_14_size gap-1  editBtn">
                                    <img :src="editIc" width="35" height="35">
                                    <span class="edittext_Gl">Edit</span>
                                </button>
                                <!-- <button @click="changeStatus(data.id)" class=" bg-transparent text-white   rounded">
                                    <img :src="deleteIc" width="28" height="28">
                                </button> -->
                                </div>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="5">Device Brand Not Found.</td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getbrandData"></pagination>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    props: {
        branddata:{
            type: Object,
        },
        familydata:{
            type: Object,
        },
        pagination: {
            type: Object,
        },
    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,
            /** USed to store the brand data and pagination */
            brandData: this.branddata.data,
            paginationData: this.pagination,

            /** Used to add a new family */
            deviceBrandName: '',
            deviceFamily: '',

            /** Used to edit the family */
            editBrandId: null,
            editdeviceBrandName: '',
            editdeviceFamily: '',

            /** Used for search parameters */
            search_name: '',
            search_device_family: '',

            validationErrors: {}, /* Store validation errors here */
        };
    },
    methods: {

        /** To close the add device brand popup */
        async closeAddModal(){
            this.validationErrors = {};
            this.deviceBrandName = '';
            this.deviceFamily = '';
        },

        /** To get the device brand by using paging */
        async getbrandData(page = 1, search_name = this.search_name, searched_device_family = this.search_device_family) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/device-brand?page=${page}${search_name && search_name !== "" ? `&name=${search_name}` : ""}${searched_device_family && searched_device_family !== "" ? `&familyId=${searched_device_family}` : ""}`
                );
                if (response.data.success == true) {
                    if (response.data.brandData && response.data.pagination )
                    {
                        this.brandData = response.data.brandData.data;
                        this.paginationData = response.data.pagination;
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
            } catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = error.response.data.error;
                setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
            }
        },

        /**  Creating new brand */
        async saveBrand(e) {
            e.preventDefault();
            show_ajax_loader();
            this.validationErrors = {};

            let validationPassed = true;

            /** Client side validations */
            if (!this.deviceBrandName) {
                this.validationErrors.brandName = `Brand name can't be empty.`;
                validationPassed = false;
            }

            if (!this.deviceFamily) {
                this.validationErrors.deviceFamily = `Please select the brand family.`;
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                /** Create a single object instead of an array */
                const brandData = {
                    name: this.deviceBrandName,
                    deviceFamily: this.deviceFamily,
                };

                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/device-brand/store`, brandData);
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    hide_admin_popup('#addDeviceBrandModalClose');

                    /** Make input field empty */
                    this.validationErrors = {};
                    this.deviceBrandName = '';
                    this.deviceFamily = '';

                    /* Fetch/update data */
                    this.getbrandData();
                } else if (response.data.errors ) {
                    if(response.data.errors.name){
                        this.validationErrors.brandName = response.data.errors.name[0];
                    }
                    if(response.data.errors.deviceFamily){
                        this.validationErrors.deviceFamily = response.data.errors.deviceFamily[0];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    /* Hide the modal */
                    hide_admin_popup('#addDeviceBrandModalClose');

                    /** Make input field empty */
                    this.validationErrors = {};
                    this.deviceBrandName = '';
                    this.deviceFamily = '';

                    /* Fetch/update data */
                    this.getbrandData();
                }
            }
            catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    /* Hide the modal */
                    hide_admin_popup('#addDeviceBrandModalClose');

                    /** Make input field empty */
                    this.validationErrors = {};
                    this.deviceBrandName = '';
                    this.deviceFamily = '';

                    /* Fetch/update data */
                    this.getbrandData();
                }
            }
        },

        /** On click of edit button */
        async editDeviceBrand(id) {
            show_ajax_loader();
            this.validationErrors = {};
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/device-brand/edit/${id}`);
                if (response.data.success == true) {
                    this.editBrandId = response.data.editData.id;
                    if(response.data.editData.name){
                        this.editdeviceBrandName = response.data.editData.name;
                    }
                    if(response.data.editData.device_family_id){
                        this.editdeviceFamily = response.data.editData.device_family_id;
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    /* Hide the modal */
                    hide_admin_popup('#editDeviceBrandModalClose');
                    this.editdeviceBrandName = '';
                    this.editdeviceFamily = '';

                    /* Fetch/update data */
                    this.getbrandData();
                }
            }
            catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    /* Hide the modal */
                    hide_admin_popup('#editDeviceBrandModalClose');
                    this.editdeviceBrandName = '';
                    this.editdeviceFamily = '';

                    /* Fetch/update data */
                    this.getbrandData();
                }
            }
        },

        /** Update the brand */
        async updateBrand(e) {
            show_ajax_loader();
            e.preventDefault();
            this.validationErrors = {}; /** Clear previous errors  */

            let validationPassed = true;

            /** Client side validations */
            if (!this.editdeviceBrandName) {
                this.validationErrors.brandName = `Brand name can't be empty.`;
                validationPassed = false;
            }

            if (!this.editdeviceFamily) {
                this.validationErrors.deviceFamily = `Please select the brand family.`;
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                /* Create FormData object */
                const editBrandData = {
                    id: this.editBrandId,
                    name: this.editdeviceBrandName,
                    deviceFamily: this.editdeviceFamily,
                };
                let response = await axios.post(
                    `${this.$userAppUrl}smarttiusadmin/device-brand/update`,
                    editBrandData
                );

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    hide_admin_popup('#editDeviceBrandModalClose');

                    /* Fetch/update data */
                    this.getbrandData();
                } else if(response.data.errors && response.data.success == false) {
                    if(response.data.errors.name){
                        this.validationErrors.brandName = response.data.errors.name[0];
                    }
                    if(response.data.errors.deviceFamily){
                        this.validationErrors.brandName = response.data.errors.deviceFamily[0];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    hide_admin_popup('#editDeviceBrandModalClose');
                    /* Fetch/update data */
                    this.getbrandData();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        3000
                    );

                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    /* Hide the modal */
                    hide_admin_popup('#editDeviceBrandModalClose');

                    /* Fetch/update data */
                    this.getbrandData();
                }
            }
        },

        /** On click of change status */
        /** async changeStatus(id) {
            this.$deleteAlertMessage.data.isDelete = false
            this.$deleteAlertMessage.data.isChangeStatus = true
            this.$deleteAlertMessage.data.itemId = id
            this.$deleteAlertMessage.data.message = 'Are you sure you want to change the status?'
            this.$deleteAlertMessage.data.callback = this.deleteDeviceBrand
        }, */
        async changeStatus(){
            this.$alertMessage.success = false;
            this.$alertMessage.message = 'This is under development.';
        },

        /** Delete existing device brand */
        /*
        async deleteDeviceBrand(id) {
            try {
                const response = await axios.delete(
                    `${this.$userAppUrl}smarttiusadmin/device-brand/delete/${id}`
                );
                if (response.data.success) {
                    this.$alertMessage.success = true
                    this.$alertMessage.message = response.data.msg
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin/device-brand/`),5000);
                } else {
                    this.$alertMessage.success = false
                    this.$alertMessage.message = response.data.message
                }
            } catch (error) {
                console.error(error);
                this.$alertMessage.success = false;
                this.$alertMessage.message = error.response.data.error;
                setTimeout(
                    () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                    5000
                );
            }
        },
        */
        /** On click of cancel search */
        async cancelSearch(){
            show_ajax_loader();
            this.search_name = '',
            this.search_device_family = '',
            this.search_status_filter = '',
            this.getbrandData();
            hide_ajax_loader();
        },
    },
};
</script>
