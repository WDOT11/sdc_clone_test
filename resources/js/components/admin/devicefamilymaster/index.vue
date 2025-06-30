<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0  ">
            <div class="card-body">
                <h4 class=" mb-3 border-bottom pb-2 coman_main_heading">Device Family</h4>

                <!-- Search filter -->
                <div class="d-flex justify-content-between flex-wrap  align-items-center gap-3 mb-3">
                    <button data-bs-toggle="modal" data-bs-target="#AddNewDeviceFamily" @click="addFamily" class="btn blogal_pbtn_padding bg_blue wmax text-white d-flex align-items-center gap-10 def_14_size rounded "><img :src="profileIc" width="22" height="22"> Add New</button>
                    <!-- Search by family name -->
                    <div class="d-flex justify-content-between flex-wrap search_options align-items-start gap-3">
                        <input type="text" v-model="search_name" class="form-control w-auto def_14_size" placeholder="Filter by Name">

                        <!-- buttons -->
                        <div class="d-flex gap-2">

                            <button class="btn blogal_pbtn_padding bg_blue d-flex def_14_size align-items-center gap-10  text-white" @click="getfamilyData"><img :src="searchIc" width="20" height="20"> Search</button>
                            <button class="btn blogal_pbtn_padding customm_reset_btn def_14_size" @click="cancelSearch">Clear</button>
                        </div>
                    </div>
                </div>

                <!-- Add Device Family Modal -->
                <div class="modal" id="AddNewDeviceFamily">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                    <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                                    <span>Add New Device Family</span>
                                </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" id="addDeviceFamilyModalClose" @click="closeAddModel"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">

                                <!-- Device Family Name -->
                                <div>
                                    <label  class="form-label" >Name <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter Device Family Name" v-model="deviceFamilyName" :class="[ 'form-control', { 'is-invalid': validationErrors.familyName }, ]" required />
                                    <small v-if="validationErrors.familyName" ><ErrorMessage :msg="validationErrors.familyName"></ErrorMessage></small>
                                </div>

                                <!-- Device Family Image Preview -->
                                <div v-if="previewImage" class="row mt-3">
                                    <div class="col-md-6">
                                        <img v-if="previewImage" :src="previewImage" class="mb-3" width="50%" height="50%" />
                                        <div class="col-md-6">
                                            <button v-if="previewImage" class="btn btn-sm btn-danger mx-3 justify-content-center align-content-center" type="button" @click="addRemoveImage"> Remove Image </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Device Family Image -->
                                <div>
                                    <label class="form-label mt-3">Image</label>
                                    <input ref="fileInput" type="file" accept=".jpg, .jpeg, .png, .svg" @change="handleFileUpload" :class="[ 'form-control', { 'is-invalid': validationErrors.familyImage }, ]" required />
                                    <small v-if="validationErrors.familyImage" ><ErrorMessage :msg="validationErrors.familyImage"></ErrorMessage></small>
                                </div>

                                <!-- Device Family Description -->
                                <div>
                                    <label class="form-label mt-3">Description</label>
                                    <textarea rows="4" v-model="deviceFamilyDescription" :class="[ 'form-control ', { 'is-invalid': validationErrors.familyDescription }, ]" required></textarea>
                                    <small v-if="validationErrors.familyDescription" ><ErrorMessage :msg="validationErrors.familyDescription"></ErrorMessage></small>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg">
                                <button data-bs-dismiss="modal" @click="closeAddModel" class="btn customm_reset_btn   mr-2">Cancel</button>
                                <button @click="saveFamily" class="btn bg_blue  text-white  ">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add Device Family Modal End -->

                <!-- Edit Device Family Modal -->
                <div class="modal" id="EditDeviceFamily">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                    <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                                    <span>Edit Device Family</span>
                                </h4>
                                <button type="button" class="btn-close" id="editDeviceFamilyModalClose" data-bs-dismiss="modal" @click="closeAddModel"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">
                                <!-- Device Family Name -->
                                <label class="form-label " >Name <span class="text-danger">*</span></label>
                                <input type="text" v-model="editdeviceFamilyName" :class="[ 'form-control', { 'is-invalid': validationErrors.familyName }, ]" placeholder="Enter Device Family Name" required />
                                <small v-if="validationErrors.familyName" ><ErrorMessage :msg="validationErrors.familyName"></ErrorMessage></small>

                                <!-- Device Family Image Preview -->
                                <div v-if="previewImage" class="row mt-3">
                                    <div class="col-md-4">
                                        <img v-if="previewImage" :src="previewImage" class="mb-3 choosedimg" width="100%"  />
                                    </div>
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center h-100">
                                            <button v-if="previewImage" class="btn btn-sm btn-danger mx-3 justify-content-center align-content-center" type="button" @click="removeImage">
                                                Remove Image
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <label class="form-label mt-3" >Image</label>

                                <!-- Device Family Image-->
                                <input type="file" ref="updateFileInput" accept=".jpg, .jpeg, .png, .svg" @change="handleEditFileUpload" :class="[ 'form-control', { 'is-invalid': validationErrors.familyImage }, ]" placeholder="Choose image" />
                                <small v-if="validationErrors.familyImage" ><ErrorMessage :msg="validationErrors.familyImage"></ErrorMessage></small>

                                <!-- Device Family Description -->
                                <label class="form-label mt-3" >Description</label>
                                <textarea rows="4" v-model="editdeviceFamilyDescription" :class="[ 'form-control', { 'is-invalid': validationErrors.familyDescription }, ]"></textarea>
                                <small v-if="validationErrors.familyDescription" ><ErrorMessage :msg="validationErrors.familyDescription"></ErrorMessage></small>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg">
                                <!-- <button data-bs-dismiss="modal" @click="showEditModal = false" class="btn btnblack text-white rounded mr-2">
                                    Cancel
                                </button> -->
                                <button @click="updateFamily" class="btn bg_blue text-white  rounded">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Edit Device Family Modal End -->

                <div class="table-responsive">
                    <!-- Table to show the Device Family records -->
                    <table class="table table-bordered table-hover def_14_size">
                        <thead class="table-light">
                            <tr>
                                <th  width="60" >#</th>
                                <th >Name</th>
                                <th >Image</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="familyData.length > 0" v-for="(data, index) in familyData" :key="data.id">
                                <td>
                                    {{ (paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1 }}
                                </td>
                                <td>
                                    {{ data.name }}
                                </td>
                                <td>
                                    <img v-if="data.file_path && data.file_name" :src="data.file_path + data.file_name" width="10%"
                                    height="10%" />
                                    <p class="mb-0" v-else>No Image</p>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-20">
                                    <button data-bs-toggle="modal" data-bs-target="#EditDeviceFamily" @click="editDeviceFamily(data.id)" class="themetextcolor globaleditbtn extrabtns  rounded-pill d-flex align-items-center def_14_size gap-1  editBtn">
                                        <img :src="editIc" width="35" height="35">
                                        <span class="edittext_Gl">Edit</span>
                                    </button>
                                    <!-- <button @click="confirmDelete(data.id)" class=" bg-transparent ">
                                        <img :src="deleteIc" width="28" height="28">
                                    </button> -->
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="5">
                                    Device Family Not Found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getfamilyData"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        familydata: {
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

            /** Used to store the family data and pagination */
            familyData: this.familydata.data,
            paginationData: this.pagination,

            /* add modal */
            addDeviceFamilyImg: null, /* This will hold the add family uploaded file */
            deviceFamilyName: "",
            deviceFamilyDescription: "",
            removeAddImageFlag: false, /* Initialize the remove image flag */

            /* edit modal */
            editFamilyId: null,
            editdeviceFamilyName: "",
            editdeviceFamilyDescription: "",
            editDeviceFamilyImg: null, /* This will hold the edit family uploaded file */
            editDeviceFamilySelectIMgURL: "",
            removeImageFlag: false, /* Initialize the remove image flag */

            /* Store edit validation */
            validationErrors: {},

            /** Searching parameters */
            search_name: '',
        };
    },
    computed: {
        previewImage() {
            /**
             * Priority:
             * 1. New image selected in add mode
             * 2. Existing image for edit mode
             */

            /* Preview image for add mode */
            if (this.addDeviceFamilyImg instanceof File) {
                return URL.createObjectURL(this.addDeviceFamilyImg);
            }

            /* Preview image for edit mode */
            if (this.editDeviceFamilyImg instanceof File) {
                return URL.createObjectURL(this.editDeviceFamilyImg);
            }

            /* Fallback to existing image path for edit mode */
            if (this.editDeviceFamilyImg) {
                return this.editDeviceFamilyImg;
            }

            return null; /* No image to preview */
        },
    },
    methods: {
        /** To get the device family by using paging */
        async getfamilyData(page = 1, search_name = this.search_name) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/device-family?page=${page}${search_name && search_name !== "" ? `&name=${search_name}` : ""}`
                );
                if (response.data.success == true) {
                    if (response.data.familyData && response.data.pagination) {
                        this.familyData = response.data.familyData.data;
                        this.paginationData = response.data.pagination;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, Please try again.';
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, Please try again.';
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
                }
            }
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.addDeviceFamilyImg = file; /** Correctly set the image for add mode */
                this.removeAddImageFlag = false; /** Reset the remove flag */
            }
        },

        handleEditFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.editDeviceFamilyImg = file; /* Store the file */
                this.removeImageFlag = false;
            }
        },

        /** close Add model */
        closeAddModel(){
            /** Make field variables empty */
            this.deviceFamilyName = '';
            this.deviceFamilyDescription = '';
            this.addDeviceFamilyImg = null /* Reset the selected image*/;
            this.removeAddImageFlag = false; /* Reset the remove image flag */
            this.editDeviceFamilyImg = null; /* Store the file */
            this.removeImageFlag = false;
            /** Manually reset file input */
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
            if (this.$refs.updateFileInput) {
                this.$refs.updateFileInput.value = '';
            }
            /** Make validation error empty */
            this.validationErrors = {};
        },

        /** On click of Add new family */
        async addFamily() {
            /** Clearing the validations errors if stored any here */
            this.validationErrors = {};
        },

        /** Creating new family */
        async saveFamily(e) {
            show_ajax_loader();
            e.preventDefault();
            /** Clear previous errors */
            this.validationErrors = {};

            let validationPassed = true;

            /* Validate Device Family */
            if (!this.deviceFamilyName) {
                this.validationErrors.familyName = `Device Family name can't be empty.`;
                validationPassed = false;
            }

            if(this.deviceFamilyDescription.length > 150){
                this.validationErrors.familyDescription = `Description must not exceed 150 characters.`;
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                /** Storing family data in an object */
                const familyData = {
                    name: this.deviceFamilyName,
                    description: this.deviceFamilyDescription,
                };
                /** Append the image file if available */
                if (this.addDeviceFamilyImg instanceof File) {
                    familyData.image = this.addDeviceFamilyImg;
                }

                /** Append remove_image flag if the image is removed */
                familyData.remove_image = this.removeAddImageFlag ? "1" : "0";

                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/device-family/store`, familyData, {headers: { "Content-Type": "multipart/form-data" },});

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    hide_admin_popup('#addDeviceFamilyModalClose');

                    /** Make input field empty */
                    this.deviceFamilyName = '';
                    this.deviceFamilyDescription = '';
                    this.addDeviceFamilyImg = null /* Reset the selected image*/;
                    this.removeAddImageFlag = false; /* Reset the remove image flag */

                    /** Manually reset file input */
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = ''; // Clears the filename
                    }

                    /* Fetch/update data */
                    this.getfamilyData();
                } else if (response.data.errors ) {
                    if(response.data.errors.name){
                        this.validationErrors.familyName = response.data.errors.name[0];
                    }
                    if(response.data.errors.description){
                        this.validationErrors.familyDescription = response.data.errors.description[0];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    /* Hide the modal */
                    hide_admin_popup('#addDeviceFamilyModalClose');

                    /* Fetch/update data */
                    this.getfamilyData();
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
                    hide_admin_popup('#addDeviceFamilyModalClose');

                    /* Fetch/update data */
                    this.getfamilyData();
                }
            }
        },

        /** On click of edit button */
        async editDeviceFamily(id) {
            show_ajax_loader();
            this.validationErrors = '';
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/device-family/edit/${id}`);
                if (response.data.editData && response.data.success == true) {
                    if(response.data.editData.id){
                        this.editFamilyId = response.data.editData.id;
                    }
                    if(response.data.editData.name){
                        this.editdeviceFamilyName = response.data.editData.name;
                    }
                    this.editdeviceFamilyDescription = response.data.editData.description ?? "";
                    this.editDeviceFamilyImg = response.data.editData.file_name && response.data.editData.file_path ? response.data.editData.file_path + response.data.editData.file_name : null;
                    this.removeImageFlag = false; /* Initialize the remove image flag */
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    /* Hide the modal */
                    hide_admin_popup('#editDeviceFamilyModalClose');

                    /* Fetch/update data */
                    this.getfamilyData();
                }
            } catch (error) {
                hide_admin_popup('#editDeviceFamilyModalClose');
                if (error && error.response && error.response.data && error.response.data.msg) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.msg;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        3000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    /* Hide the modal */
                    hide_admin_popup('#editDeviceFamilyModalClose');

                    /* Fetch/update data */
                    this.getfamilyData();
                }
            }
        },

        /* edit Remove Image */
        removeImage() {
            this.editDeviceFamilyImg = null;
            this.removeImageFlag = true; /* Set the flag to true when image is removed */
        },

        /* add remove image */
        addRemoveImage() {
            this.addDeviceFamilyImg = null /* Reset the image when a new image is selected */;
            this.removeAddImageFlag = false; /* Reset the remove image flag */
        },

        /** Update the family */
        async updateFamily(e) {
            show_ajax_loader();
            e.preventDefault();
            /** Clear previous errors */
            this.validationErrors = {};

            let validationPassed = true;

            /* Validate Device Brand */
            if (!this.editdeviceFamilyName) {
                this.validationErrors.familyName = `Device Family name can't be empty.`;
                validationPassed = false;
            }

            if(this.editdeviceFamilyDescription.length > 150){
                this.validationErrors.familyDescription = `Description must not exceed 150 characters.`;
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                /** Create FormData object */
                let editFamilyData = new FormData();
                editFamilyData.append("id", this.editFamilyId);
                editFamilyData.append("name", this.editdeviceFamilyName);
                editFamilyData.append("description", this.editdeviceFamilyDescription);

                /** Append image only if a new file is selected */
                if (this.editDeviceFamilyImg instanceof File) {
                    editFamilyData.append("image", this.editDeviceFamilyImg);
                }

                /** Append remove_image flag if the image is removed */
                editFamilyData.append("remove_image", this.removeImageFlag ? "1" : "0");

                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/device-family/update`,
                    editFamilyData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                    }
                );

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    hide_admin_popup('#editDeviceFamilyModalClose');

                    this.editDeviceFamilyImg = null; /* remove the file */
                    this.removeImageFlag = false;
                    /** Manually reset file input */
                    if (this.$refs.updateFileInput) {
                        this.$refs.updateFileInput.value = '';
                    }

                    /* Fetch/update data */
                    this.getfamilyData();
                } else if(response.data.errors && response.data.success == false) {
                    if(response.data.errors.name){
                        this.validationErrors.familyName = response.data.errors.name[0];
                    }

                    if(response.data.errors.image){
                        this.validationErrors.familyImage = response.data.errors.image[0];
                    }

                    if(response.data.errors.description){
                        this.validationErrors.familyDescription = response.data.errors.description[0];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    /* Hide the modal */
                    hide_admin_popup('#editDeviceFamilyModalClose');

                    /* Fetch/update data */
                    this.getfamilyData();
                }
            } catch(error) {
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
                    hide_admin_popup('#editDeviceFamilyModalClose');

                    /* Fetch/update data */
                    this.getfamilyData();
                }
            }
        },

        /**
        confirmDelete(id) {
            this.$deleteAlertMessage.data.isDelete = false
            this.$deleteAlertMessage.data.isChangeStatus = true
            this.$deleteAlertMessage.data.itemId = id
            this.$deleteAlertMessage.data.message = 'Are you sure you want to change the status?'
            this.$deleteAlertMessage.data.callback = this.changeStatus
        },
        */

        /** To show an error on click of delete button */
        async confirmDelete(){
            this.$alertMessage.success = false;
            this.$alertMessage.message = 'This is under development.';
        },

        /** Delete existing device family */
        /**
        async changeStatus(id) {

            // Client-side validation
            let validationPassed = true;

            // Validate Claim reason
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
                    `${this.$userAppUrl}smarttiusadmin/device-family/delete/${id}`
                );
                if(response && response.data){
                    if (response.data.success == true) {
                        this.$alertMessage.success = true
                        this.$alertMessage.message = response.data.msg
                        setTimeout(
                            () => this.getfamilyData(),
                            5000
                        );
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
                    setTimeout(
                        () => this.getfamilyData(),
                        5000
                    );
                }
            }
        },
        */

        /** On click of the cancel from the search */
        async cancelSearch(){
            show_ajax_loader();
            this.search_name = '';
            this.getfamilyData();
            hide_ajax_loader();
        },
    },
};
</script>
