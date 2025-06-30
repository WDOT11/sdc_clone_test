<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0  ">
            <div class="card-body">
                <h4 class="mb-3 border-bottom pb-2 coman_main_heading">Device Model</h4>

                <!-- Filter section to search the records -->
                <div class="d-flex justify-content-between flex-wrap align-items-start gap-3 ">
                    <button data-bs-toggle="modal" data-bs-target="#NewDeviceModel" @click="addDeviceModel" class="blogal_pbtn_padding btn def_14_size bg_blue wmax text-white d-flex align-items-center gap-10  rounded">
                        <img :src="profileIc" width="22" height="22"> Add New
                    </button>
                    <!-- Search by name -->
                    <div class="d-flex justify-content-between flex-wrap search_options align-items-start gap-3">
                        <input type="text" v-model="search_name" class="form-control w-auto def_14_size" placeholder="Filter by Title">

                        <!-- buttons -->
                        <div class="d-flex gap-2">
                            <button class="btn blogal_pbtn_padding bg_blue d-flex align-items-center gap-10 def_14_size text-white" @click="getDeviceModelData"><img :src="searchIc" width="20" height="20"> Search</button>
                            <button class="btn blogal_pbtn_padding customm_reset_btn def_14_size" @click="cancelSearch">Clear</button>
                        </div>
                    </div>
                </div>

                <!-- Add Device Model Modal -->
                <div class="modal" id="NewDeviceModel">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                    <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                                    <span>Add New Device Model</span>
                                </h4>
                                <button type="button" class="btn-close" id="addDeviceModelModalClose" @click="resetHideForm" data-bs-dismiss="modal"></button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">

                                <!-- Device Model Title -->
                                <div>
                                    <label  class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter Device Model Title" v-model="deviceModelTitle" :class="['form-control', { 'is-invalid': insertValidations.title },]" required />
                                    <small v-if="insertValidations.title"><ErrorMessage :msg="insertValidations.title"></ErrorMessage></small>
                                </div>

                                <!-- Device Family -->
                                <div>
                                    <label class="form-label mt-3" >Device Family <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="deviceFamily" placeholder="Search or add device family" label="name" track-by="id" :options="familydata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': insertValidations.deviceFamily }]" required>
                                        <template #noResult>
                                            <span class="custom-message">No device family found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No device family found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="insertValidations.deviceFamily"><ErrorMessage :msg="insertValidations.deviceFamily"></ErrorMessage></small>
                                </div>

                                <div>
                                    <!-- Device Brand -->
                                    <label class="form-label mt-3" >Device Brand <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="deviceBrand" placeholder="Search or add device brand" label="name" track-by="id" :options="branddata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': insertValidations.deviceBrand },]" required>
                                        <template #noResult>
                                            <span class="custom-message">No device brand found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No device brand found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="insertValidations.deviceBrand" ><ErrorMessage :msg="insertValidations.deviceBrand"></ErrorMessage></small>
                                </div>

                                <div>
                                    <!-- select device presence -->
                                    <label class="form-label mt-3" >Select device presence <span class="text-danger">*</span></label>
                                    <select v-model="showDevice" :class="['form-control', {'is-invalid': insertValidations.showDeviceModel}]">
                                        <option value="1">Insured Devices Only</option>
                                        <option value="2">Uninsured Devices Only</option>
                                        <option value="3">Show everywhere</option>
                                    </select>
                                    <small v-if="insertValidations.showDeviceModel"><ErrorMessage :msg="insertValidations.showDeviceModel"></ErrorMessage>  </small>
                                </div>

                                <div>
                                    <!-- Select this for the public coverages -->
                                    <label class="form-label mt-3" >Available for Public Coverages also? <span class="text-danger">*</span></label>
                                    <select v-model="showDevicePublicly" :class="['form-control', {'is-invalid': insertValidations.showDevicePublicly}]">
                                        <option value="1">Yes</option>
                                        <option value="2">No</option>
                                    </select>
                                    <small v-if="insertValidations.showDevicePublicly"><ErrorMessage :msg="insertValidations.showDevicePublicly"></ErrorMessage></small>
                                </div>

                                <!-- Note to show with the form -->
                                <div class="mt-1">
                                    <p class="themetextcolor"> <b>Note:-</b> Don't forget to create payment plans for the model.</p>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg">
                                <!-- <button data-bs-dismiss="modal" type="button" @click="cancelForm" class="btn btnblack text-white  rounded mr-2">
                                    Cancel
                                </button> -->
                                <button type="button" @click="saveDeviceModel" class="btn bg_blue text-white  rounded">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add Device Model Modal End -->

                <!-- Edit Device model Modal -->
                <div class="modal" id="editDeviceModel">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                        <!-- Modal Header -->
                        <div class="modal-header align-items-center">
                            <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                                <span>Edit Device Model</span>
                            </h4>
                            <button type="button" class="btn-close" id="editDeviceModelModalClose" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body onewhitebg">
                            <div>
                                <!-- Device Model Title -->
                                <label class="form-label">Device Model Title <span class="text-danger">*</span></label>
                                <input type="text" v-model="editdeviceTitle" :class="[ 'form-control', { 'is-invalid': editValidations.title }, ]" placeholder="Enter Device Model Title" required />
                                <small v-if="editValidations.title" ><ErrorMessage :msg="editValidations.title"></ErrorMessage></small>
                            </div>
                            <div>
                                <!-- Device Family -->
                                <label class="form-label mt-3" >Device Family <span class="text-danger">*</span></label>
                                <multiselect id="tagging" v-model="editdeviceFamily" placeholder="Search or change device family" label="name" track-by="id" :options="familydata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="[ '', { 'is-invalid': editValidations.deviceFamily }, ]" required>
                                    <template #noResult>
                                        <span class="custom-message">No device family found.</span>
                                    </template>
                                    <template #noOptions>
                                        <span class="custom-message">No device family found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="editValidations.deviceFamily" ><ErrorMessage :msg="editValidations.deviceFamily"></ErrorMessage></small>
                            </div>
                            <div>
                                <!-- Device Brand -->
                                <label class="form-label mt-3" >Device Brand <span class="text-danger">*</span></label>
                                <multiselect id="tagging" v-model="editdeviceBrand" placeholder="Search or change device brand" label="name" track-by="id" :options="branddata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="[ '', { 'is-invalid': editValidations.deviceBrand }, ]" required>
                                    <template #noResult>
                                        <span class="custom-message">No device brand found.</span>
                                    </template>
                                    <template #noOptions>
                                        <span class="custom-message">No device brand found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="editValidations.deviceBrand" ><ErrorMessage :msg="editValidations.deviceBrand"></ErrorMessage></small>
                            </div>
                            <div>
                                <!-- Model show type -->
                                <label class="form-label mt-3" >Show Device Model <span class="text-danger">*</span></label>
                                <select v-model="ediDeviceShowType" :class="['form-control', {'is-invalid': editValidations.showDeviceModel}]">
                                    <option value="1">Uninsured Devices Only</option>
                                    <option value="2">Insured Devices Only</option>
                                    <option value="3">Show everywhere</option>
                                </select>
                                <small v-if="editValidations.showDeviceModel" ><ErrorMessage :msg="editValidations.showDeviceModel"></ErrorMessage></small>
                            </div>
                            <div>
                                <!--  Select Public Presence -->
                                <label class="form-label mt-3" >Available for Public Coverages also? <span class="text-danger">*</span></label>
                                <select v-model="showDevicePublicly" :class="['form-control', {'is-invalid': editValidations.showDevicePublicly}]">
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                                <small v-if="editValidations.showDevicePublicly"> <ErrorMessage :msg="editValidations.showDevicePublicly"></ErrorMessage></small>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer onewhitebg">
                            <!-- <button data-bs-dismiss="modal" @click="showEditModal = false" class="btn btnblack text-white rounded mr-2">
                                Cancel
                            </button> -->
                            <button @click="updateDeviceModel" class="btn bg_blue text-white  rounded">
                                Save
                            </button>

                        </div>

                        </div>
                    </div>
                </div>
                <!-- Edit Device Model Modal End -->

                <!-- Table to show the Device model records -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size table_custom2">
                        <thead class="table-light">
                            <tr>
                                <th  width="60" class="">#</th>
                                <th class="">Model</th>
                                <th class="">Device Family</th>
                                <th class="">Device Brand</th>
                                <th class="">Action</th>
                                 <th class="">New Plan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="devicemodelData.length > 0" v-for="(data, index) in devicemodelData" :key="data.id">
                                <td class="">
                                    {{ (paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1 }}
                                </td>
                                <td class="">
                                    {{ data.title }}
                                </td>
                                <td class=" ">
                                    {{ data.device_family_name ?? '-' }}
                                </td>
                                <td class="">
                                    {{ data.device_brand_name ?? '-' }}
                                </td>
                                <td class="">

                                    <button data-bs-toggle="modal" data-bs-target="#editDeviceModel" @click="editDeviceModel(data.id)" class="  themetextcolor globaleditbtn extrabtns rounded-pill text-decoration-none d-flex align-items-center def_14_size gap-1  editBtn">
                                        <img :src="editIc" class="blobal_img_w">
                                        <span class="edittext_Gl">Edit</span>
                                    </button>


                                </td>
                                <td><a class=" text-decoration-none   d-flex align-items-center def_14_size gap-1 themetextcolor devicemodel" :href="`${this.$userAppUrl}smarttiusadmin/devices-plan?openPopup=true&model=${data.id}`"><img :src="createIc" class="blobal_img_w create_pay_w"> Create Payment </a>
                                    <!-- <button @click="confirmDelete(data.id)" class=" bg-transparent text-white rounded">
                                        <img :src="deleteIc" width="28" height="28">
                                    </button> --></td>
                            </tr>
                            <tr v-else>
                                <td class="" colspan="6">
                                    Device Model Not Found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getDeviceModelData"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        familydata: {
            type: Array,
        },
        devicemodeldata: {
            type: Object,
        },
        pagination: {
            type: Object,
        },
    },
    data() {
        return {
            createIc: this.$createImg,
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,
            /** Used to store the model data and paginations */
            devicemodelData: this.devicemodeldata.data,
            paginationData: this.pagination,

            insertValidations: {}, /* Store add validation errors here */
            editValidations: {}, /* Store edit validation errors here */

            /** Used to manage the data while creating the model */
            deviceModelTitle: "",
            deviceFamily: "",
            deviceBrand: "",
            showDevice: 3,
            showDevicePublicly: 1,
            branddata: [],

            /** Used to manage the data while updating the data */
            editDeviceModelId: null,
            editdeviceTitle: "",
            editdeviceFamily: "",
            editdeviceBrand: "",
            editdevicePlan: "",
            ediDeviceShowType: "",
            showDevicePublicly: 1,

            /** used while searching */
            search_name: "",
        };
    },
    watch: {
        deviceFamily(newVal) {
            if (newVal && newVal.id) {
                this.getBrands(newVal.id);
                /* Reset brand selection when family changes */
                this.deviceBrand = "";
            } else {
                /* Clear brand options if no family is selected */
                this.branddata = [];
                this.deviceBrand = "";
            }
        },

        editdeviceFamily(newVal) {
            if (newVal && newVal.id) {
                this.getBrands(newVal.id);
                /* Reset brand selection when family changes */
                this.editdeviceBrand = "";
            } else {
                /* Clear brand options if no family is selected */
                this.branddata = [];
                this.editdeviceBrand = "";
            }
        },
    },
    methods: {
        /** get brands */
        async getBrands(familyID) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/device-brand/fetch-brands?familyId=${familyID}`
                );
                if (response.data.success == true) {
                    this.branddata = response.data.brandData;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
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
                }
            }
        },

        /** To get the device model data by using paging */
        async getDeviceModelData(page = 1, searched_name = this.search_name, searched_family = this.search_device_family, searched_device_coverage = this.search_device_coverage) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/device-model?page=${page}${searched_name && searched_name !== "" ? `&title=${searched_name}` : ""}${searched_family && searched_family !== "" ? `&familyId=${searched_family}` : ""}${searched_device_coverage && searched_device_coverage !== "" ? `&showModel=${searched_device_coverage}` : ""}`
                );
                if (response && response.data) {
                    if (response.data.success == true) {
                        if (response.data.modelData && response.data.pagination) {
                            this.devicemodelData = response.data.modelData.data;
                            this.paginationData = response.data.pagination;
                            hide_ajax_loader();
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = 'Something went wrong, please try again.';
                        }
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
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
                }
            }
        },

        /* On click of Add new device model */
        // async addDeviceModel() {
        //     this.insertValidations = {};
        //     this.editValidations = {};
        //     this.showAddModal = true;
        // },

        /** Creating new device model */
        async saveDeviceModel(e) {
            e.preventDefault();
            show_ajax_loader();
            this.insertValidations = {};
            /* Client-side validation */
            let validationPassed = true;

            /* Validate Title */
            if (!this.deviceModelTitle) {
                this.insertValidations.title = 'Device model title is required.';
                validationPassed = false;
            } else if (this.deviceModelTitle.length < 2) {
                this.insertValidations.title = 'Title must be at least 2 characters long.';
                validationPassed = false;
            }

            /* Validate Device Family */
            if (!this.deviceFamily || !this.deviceFamily.id) {
                this.insertValidations.deviceFamily = 'Device family is required.';
                validationPassed = false;
            }

            /* Validate Device Brand */
            if (!this.deviceBrand || !this.deviceBrand.id) {
                this.insertValidations.deviceBrand = 'Device brand is required.';
                validationPassed = false;
            }

            /* Validate Device by for their presence */
            if (!this.showDevice) {
                this.insertValidations.showDevice = 'Please select the device presence.';
                validationPassed = false;
            }

            /** Validate the deice model for public presence also */
            if(!this.showDevicePublicly){
                this.insertValidations.showDevicePublicly = 'Please select the public appearence.';
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                /* Create a single object */
                const modelData = {
                    title: this.deviceModelTitle,
                    deviceFamily: this.deviceFamily.id,
                    deviceBrand: this.deviceBrand.id,
                    showDeviceModel: this.showDevice,
                    showDevicePublicly: this.showDevicePublicly
                };

                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/device-model/store`, modelData);

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* hide the model and reset the form */
                    this.resetHideForm();
                    /* get device model data */
                    this.getDeviceModelData();
                } else if (response.data.errors ) {
                    if(response.data.errors.title){
                        this.insertValidations.title = response.data.errors.title[0];
                    }
                    if(response.data.errors.deviceFamily){
                        this.insertValidations.deviceFamily = response.data.errors.deviceFamily[0];
                    }
                    if(response.data.errors.deviceBrand){
                        this.insertValidations.deviceBrand = response.data.errors.deviceBrand[0];
                    }
                    if(response.data.errors.showDeviceModel){
                        this.insertValidations.showDeviceModel = response.data.errors.showDeviceModel[0];
                    }
                    if(response.data.errors.showDevicePublicly){
                        this.insertValidations.showDevicePublicly = response.data.errors.showDevicePublicly[0];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
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
                }
            }
        },

        /** add modal form cancle form */
        async cancelForm() {
            /* hide the model and reset the form */
            this.resetHideForm();
        },

        /** Reset form fields and hide model (add device model) */
        async resetHideForm() {
            /* Close the modal */
            hide_admin_popup('#addDeviceModelModalClose');
            /* Reset form fields */
            this.deviceModelTitle = '';
            this.deviceFamily = '';
            this.deviceBrand = '';
            /* Clear validation errors */
            this.insertValidations = {};
        },

        /** On click of edit button */
        async editDeviceModel(id) {
            show_ajax_loader();
            this.editValidations = {};
            try {
                let response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/device-model/edit/${id}`
                );
                if (response.data.success == true) {
                    this.editDeviceModelId = response.data.editData.id;
                    this.editdeviceTitle = response.data.editData.title;
                    this.ediDeviceShowType = response.data.editData.show_device_model;

                    /* Find and set the selected family */
                    const familyID = response.data.editData.device_family_id;
                    this.editdeviceFamily = this.familydata.find(family => family.id == familyID) || null;
                    /* Fetch brands for selected family */
                    await this.getBrands(familyID);
                    /* Find and set the selected brand after fetching brand data */
                    const brandID = response.data.editData.device_brand_id;
                    this.editdeviceBrand = this.branddata.find(brand => brand.id == brandID) || null;
                    hide_ajax_loader();
                } else {
                    hide_admin_popup('#editDeviceModelModalClose');
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
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
                }
            }
        },

        /** Update the device model */
        async updateDeviceModel(e) {
            e.preventDefault();
            show_ajax_loader();
            this.editValidations = {}; /* Clear previous errors */
            let validationPassed = true;
            /* Validate Title */
            if (!this.editdeviceTitle) {
                this.editValidations.title = 'Device model title is required.';
                validationPassed = false;
            } else if (this.editdeviceTitle.length < 2) {
                this.editValidations.title = 'Title must be at least 2 characters long.';
                validationPassed = false;
            }

            /* Validate Device Family */
            if (!this.editdeviceFamily || !this.editdeviceFamily.id) {
                this.editValidations.deviceFamily = 'Device family is required.';
                validationPassed = false;
            }

            /* Validate Device Brand */
            if (!this.editdeviceBrand || !this.editdeviceBrand.id) {
                this.editValidations.deviceBrand = 'Device brand is required.';
                validationPassed = false;
            }

            /** Validate the deice model for public presence also */
            if(!this.showDevicePublicly){
                this.editValidations.showDevicePublicly = 'Please select the public appearence.';
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                /** Create FormData object */
                const editModelData = {
                    id: this.editDeviceModelId,
                    title: this.editdeviceTitle,
                    deviceFamily: this.editdeviceFamily.id,
                    deviceBrand: this.editdeviceBrand.id,
                    showDeviceModel: this.ediDeviceShowType,
                    showDevicePublicly: this.showDevicePublicly,
                };
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/device-model/update`, editModelData);

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* hide the model and reset the form */
                    this.editResetHideForm();
                    /* get device model data */
                    this.getDeviceModelData();
                } else if (response.data.errors ) {
                    if(response.data.errors.title){
                        this.editValidations.title = response.data.errors.title[0];
                    }
                    if(response.data.errors.deviceFamily){
                        this.editValidations.deviceFamily = response.data.errors.deviceFamily[0];
                    }
                    if(response.data.errors.deviceBrand){
                        this.editValidations.deviceBrand = response.data.errors.deviceBrand[0];
                    }
                    if(response.data.errors.showDeviceModel){
                        this.editValidations.showDeviceModel = response.data.errors.showDeviceModel[0];
                    }
                    if(response.data.errors.showDevicePublicly){
                        this.editValidations.showDevicePublicly = response.data.errors.showDevicePublicly[0];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
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
                }
            }
        },

        /** edit modal form cancle form */
        async editCancelForm() {
            /* hide the model and reset the form */
            this.editResetHideForm();
        },

        /** Reset form fields and hide model (edit device modal) */
        async editResetHideForm() {
            /* Close the modal */
            hide_admin_popup('#editDeviceModelModalClose');

            /* Reset form fields */
            this.editDeviceModelId = '';
            this.editdeviceTitle = '';
            this.editdeviceFamily = '';
            this.editdeviceBrand = '';
            /* Clear validation errors */
            this.editValidations = {};
        },

        /**
        confirmDelete(id) {
            this.$deleteAlertMessage.data.isOpen = true;
            this.$deleteAlertMessage.data.itemId = id;
            this.$deleteAlertMessage.data.message =
                "Are you sure you want to delete this Device Model?";
            this.$deleteAlertMessage.data.callback = this.deleteDeviceModel;
        },
        */

        /** Delete existing device model */
        /**
        async deleteDeviceModel(id) {
            try {
                const response = await axios.delete(
                    `${this.$userAppUrl}smarttiusadmin/device-model/delete/${id}`
                );
                if (response && response.data) {
                    if (response.data.success == true) {
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        setTimeout(() => {
                            // get device model data
                            this.getDeviceModelData();
                        }, 1000
                        );
                    } else {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = response.data.message;
                    }
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                        6000
                    );
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },
         */
         async confirmDelete(id) {
            this.$alertMessage.success = false;
            this.$alertMessage.message = 'This is under development.';
         },

        /** cancel search */
        async cancelSearch(){
            show_ajax_loader();
            this.search_name = '',
            this.search_device_family = '',
            this.search_device_coverage = '',
            this.getDeviceModelData();
            hide_ajax_loader();
        }
    },
};
</script>
