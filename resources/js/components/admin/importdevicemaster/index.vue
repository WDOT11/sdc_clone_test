<template>
    <div class="container-fluid ">
        <div class="card onewhitebg border-0 p-3 mt_12">
            <h4 class="coman_main_heading mb-2 border-bottom pb-2">Import Devices</h4>
            <!-- Button and search section -->
            <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 mt-2 mb-3">
                <!-- Left Side Button -->

                <button data-bs-toggle="modal" data-bs-target="#NewImport" @click="importNewDevices" class="btn bg_blue text-white  def_14_size d-flex align-items-center gap-10  rounded">
                    <img :src="profileIc" width="22" height="22">
                    New Import
                </button>
                 <!-- Search by user name or email -->
                <div class="d-flex justify-content-between flex-wrap search_options align-items-start gap-3">
                    <input type="text" v-model="search_name" class="form-control w-auto def_14_size" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" placeholder="Filter by Name">

                    <!-- buttons -->
                    <div class="d-flex gap-2">

                        <button class="btn bg_blue d-flex align-items-center gap-10  blogal_pbtn_padding text-white def_14_size"
                            @click="getImportedDevices"><img :src="searchIc" width="20" height="20"> Search</button>
                        <button class="btn customm_reset_btn def_14_size blogal_pbtn_padding" @click="cancelSearch">Clear</button>
                    </div>
                </div>
            </div>

            <!-- New Import Modal -->
            <div class="modal" id="NewImport">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content onewhitebg">

                        <!-- Modal Header -->
                        <div class="modal-header align-items-center">
                            <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                <svg width="30" height="30" id="fi_17215689" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m55.03 4.06h-46.06a7.477 7.477 0 0 0 -7.47 7.47v40.94a7.477 7.477 0 0 0 7.47 7.47h46.06a7.477 7.477 0 0 0 7.47-7.47v-40.94a7.477 7.477 0 0 0 -7.47-7.47zm4.47 48.41a4.475 4.475 0 0 1 -4.47 4.47h-46.06a4.475 4.475 0 0 1 -4.47-4.47v-14.99h24.29v7.03a1.5 1.5 0 0 0 .9 1.37 1.411 1.411 0 0 0 .6.13 1.473 1.473 0 0 0 1.01-.39l13.79-12.51a1.5 1.5 0 0 0 0-2.22l-13.79-12.51a1.5 1.5 0 0 0 -2.51 1.11v7.03h-24.29v-14.99a4.475 4.475 0 0 1 4.47-4.47h46.06a4.475 4.475 0 0 1 4.47 4.47z"></path></svg>
                                <span> Import Devices</span>
                            </h4>
                            <button type="button" class="btn-close" id="importDeviceModalClose" @click="resetImportForm" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body onewhitebg">
                            <import-device-csv-progress v-if="importId" :importid="importId" :devicedata="deviceData" @completed="handleImportComplete"/>
                            <div class="row">
                                <!-- Organization -->
                                <div class="col-md-6 mt-2">
                                    <label class="form-label" for="org">Organization <span class="text-danger">*</span></label>
                                    <multiselect id="org" v-model="organization" placeholder="Search or choose organization" label="name" track-by="id" :options="organizations" :searchable="true" @search-change="updateSearchOrgName" @select="handleSelectOrg" @remove="handleOrgRemove" :allow-empty="true" :internal-search="true" :preserve-search="false" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['',{ 'is-invalid': validationErrors.organization },]" required>
                                        <template #noResult>
                                            <span class="custom-message">No organization found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No organization found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors.organization" ><ErrorMessage :msg="validationErrors.organization[0]"></ErrorMessage></small>
                                </div>
                                <!-- Sub-Organization -->
                                <div class="col-md-6 mt-2">
                                    <label for="" class="form-label">Sub-Organization</label>
                                    <multiselect id="sub-org" v-model="subOrganization" placeholder="Search or choose sub organization" label="name" track-by="id" :options="subOrganizations" :multiple="false" @select="handleSelectSubOrg" @remove="handleSubOrgRemove" :taggable="false" selectLabel="" deselectLabel="" :class="['', {'is-invalid':validationErrors.subOrganization,},]" required>
                                        <template #noResult>
                                            <span class="custom-message">No sub organization found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No sub organization found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors.subOrganization" ><ErrorMessage :msg="validationErrors.subOrganization[0]"></ErrorMessage></small>
                                </div>
                            </div>
                            <!-- User Selection -->
                            <div class="row">
                                <!-- Users -->
                                <div class="col-md-12 mt-2">
                                    <label class="form-label" for="selectedUser">Users <span class="text-danger">*</span></label>
                                    <multiselect id="selectedUser" v-model="selectedUser" placeholder="Search or choose user" label="full_name" track-by="id" :options="users" :allow-empty="true" :internal-search="true" :preserve-search="false" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['',{ 'is-invalid': validationErrors.users },]" required>
                                        <template #noResult>
                                            <span class="custom-message">No user found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No user found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors.users" ><ErrorMessage :msg="validationErrors.users[0]"></ErrorMessage></small>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Device Model Selection -->
                                <div class="col-md-6 mt-2">
                                    <label for="" class="form-label">Device Model <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="selectedDeviceModel" :searchable="true" @search-change="updateSearchModelName" placeholder="Search or choose device model" label="title" track-by="id" :options="devicemodeldata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['w-full',{'border-red-500': validationErrors.selectedDeviceModel,}, ]" required :allow-empty="true" :internal-search="true" :preserve-search="false">
                                        <template #noOptions>
                                            <span class="custom-message">Please select the organization first</span>
                                        </template>
                                        <template #noResult>
                                            <span class="custom-message">No device model found.</span>
                                        </template>
                                    </multiselect>
                                    <span v-if="validationErrors.selectedDeviceModel">
                                        <ErrorMessage :msg="validationErrors.selectedDeviceModel[0]"></ErrorMessage>
                                    </span>
                                </div>
                                <!-- Device Expiration Date -->
                                <div class="col-md-6 mt-2">
                                    <label for="deviceExp" class="form-label">Expiration Date<span class="text-danger">*</span></label>
                                    <input type="date" v-model="deviceExp" id="deviceExp" :class="['form-control w-full',{'border-red-500': validationErrors.deviceExp}, ]">
                                    <span v-if="validationErrors.deviceExp">
                                        <ErrorMessage :msg="validationErrors.deviceExp[0]"></ErrorMessage>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <!-- File Upload -->
                                <div class="my-3">
                                    <label for="file" class="form-label">Upload CSV <span class="text-danger">*</span></label>
                                    <input type="file" id="file" class="form-control" accept=".csv" @change="handleFileUpload" required>
                                    <small v-if="validationErrors.file" ><ErrorMessage :msg="validationErrors.file[0]"></ErrorMessage></small>
                                </div>
                                <!-- CSV Note -->
                                <div class="col-md-12 mb-6">
                                    <p class="text-sm themetextcolor">
                                    <b>Note:</b> Please follow the CSV structure as shown in the sample file.
                                        <button type="button" class="btn btn-sm btn-primary" @click="downlaodSampleCSV('/storage/device_csv/', 'sample_device.csv')">
                                            Download Sample CSV
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer onewhitebg">
                            <button type="submit" @click="importDevices" class="btn bg_blue text-white">
                                Save
                            </button>
                            <!-- <button data-bs-dismiss="modal" @click="closeImportModel" type="button" class="btn btnblack">
                                Cancel
                            </button> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- New Import Modal End -->

             <!-- Import Device CSV Listing -->
             <div class="table-responsive">
                <table class="table table-bordered table-hover def_14_size table_custom">
                    <thead class="table-light">
                        <tr>
                            <th  width="60" >#</th>
                            <th >CSV Name</th>
                            <th >CSV File Path</th>
                            <th >Created Date</th>
                            <th >User Name</th>
                            <th >Summary</th>
                            <th >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="importedDeviceCsvData.length > 0" v-for="(data, index) in importedDeviceCsvData" :key="data.id">
                            <td>
                                {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}
                            </td>
                            <td>
                                {{ data.file_name }}
                            </td>
                            <td>
                                {{ data.file_path }}
                            </td>
                            <td class="text-nowrap">
                                {{ formatDate(new Date(data.created_at).toISOString().split('T')[0]) }}
                            </td>
                            <td>{{ data.user_name ?? '' }}</td>
                            <td class="text-nowrap">
                                {{ data.inserted }} Devices created,
                                {{ data.updated }} updated, {{ data.skipped }} skipped, 0 deleted
                            </td>
                            <td>
                                <button type="button" @click="downloadImportDeviceCsv(data.id)" class="btn btn-sm downloadbtn"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M243.968 378.528c3.04 3.488 7.424 5.472 12.032 5.472s8.992-2.016 12.032-5.472l112-128c4.16-4.704 5.12-11.424 2.528-17.152S374.272 224 368 224h-64V16c0-8.832-7.168-16-16-16h-64c-8.832 0-16 7.168-16 16v208h-64a16.013 16.013 0 0 0-14.56 9.376c-2.624 5.728-1.6 12.416 2.528 17.152l112 128z" style="" fill="#0055fa" data-original="#2196f3" opacity="1" class=""></path><path d="M432 352v96H80v-96H16v128c0 17.696 14.336 32 32 32h416c17.696 0 32-14.304 32-32V352h-64z" style="" fill="#607d8b" data-original="#607d8b"></path></g></svg>Download CSV</button>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="7">
                                Import Device data not found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Import Device CSV Listing End -->

            <!-- Pagination -->
            <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5" :paginate="getImportedDevices"></pagination>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        devicecsvdata:{
            type: Object
        },
        paginationdata:{
            type: Object
        }
    },
    data() {
        return {
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,

            /** Used in searching of organization */
            searchOrgText: '',
            organization:'',
            subOrganization:'',
            organizations:[],
            subOrganizations: [],
            selectedDeviceModel: '',
            users: [],
            selectedUser: null,
            deviceExp: '',
            csvFile: null,
            /** Used in searching of models */
            searchModelText: "",
            devicemodeldata: [],

            /* Store add validation */
            validationErrors: {},

            importedDeviceCsvData: this.devicecsvdata.data,
            pagination: this.paginationdata,

            /** Progress Bar */
            importId: '',
            deviceData: null,
            search_name: '',
        };
    },
    methods: {

        formatDate(dateString){
            return formatDateAndTimeZone(dateString);
        },

        /** Get Imported Devices CSV Data */
        async getImportedDevices(page = 1, search_name = this.search_name) {
            show_ajax_loader();
            try {
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/import-devices/?page=${page}${search_name && search_name !== "" ? `&search=${search_name}` : ""}`);
                if (response.data.success == true) {
                    if (response.data.deviceCsvData.data && response.data.pagination )
                    {
                        this.importedDeviceCsvData = response.data.deviceCsvData.data;
                        this.pagination = response.data.pagination;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                }
            }
            catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = error.response.data.error;
                setTimeout(
                    () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                    3000
                );
            }
        },

        /** To get Organizations */
        async getOrganizations(name) {
            show_ajax_loader();
           /* Enter at list 2 characters */
           if (name.length < 2) {
                this.organizations = [{
                    id: null,
                    name: "Please enter at least two characters",
                }];
                hide_ajax_loader();
                return;
            }
            try {
                let response = null;
                const url = `${this.$userAppUrl}smarttiusadmin/organizations/get-organization?name=${name}`;
                response = await axios.get(url);
                if (name !== this.searchOrgText) {
                    this.organizations = [{
                        id: null,
                        name: "Please enter at least two characters",
                    }];
                    hide_ajax_loader();
                    return;
                }else if (response.data.success && response.data.orgData.length > 0) {
                    this.organizations = response.data.orgData;
                    hide_ajax_loader();
                } else {
                    /* This will show up in the dropdown regardless of success status */
                    this.organizations = [{
                        id: null,
                        name: response.data.msg ?? 'No organization found',
                    }];
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin/`), 3000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** organization name text */
        updateSearchOrgName(value) {
            show_ajax_loader();
            if (value.length > 0) {
                this.searchOrgText = value;
                this.getOrganizations(value);
                hide_ajax_loader();
            }else {
                this.searchOrgText = "";
                this.organizations = [{
                        id: null,
                        name: "Please enter at least two characters",
                }];
                hide_ajax_loader();
            }
        },

        /** Handle Organization Selection */
        handleSelectOrg() {
            show_ajax_loader();
            if (this.organization && this.organization?.id) {
                 /* Clear all dependent fields */
                this.selectedDeviceModel = '';
                this.searchModelText = '';
                this.devicemodeldata = [];

                this.subOrganization = '';
                this.subOrganizations = [];
                this.users = [];
                this.getDeviceSubOrg(this.organization.id);
                this.getDeviceModels(this.organization.id);
                this.getUsers(this.organization.id);
                hide_ajax_loader();
            } else {
                /* Clear all dependent fields */
                this.selectedDeviceModel = '';
                this.searchModelText = '';
                this.devicemodeldata = [];

                this.subOrganization = '';
                this.subOrganizations = [];

                this.users = [];
                hide_ajax_loader();
            }
        },

        /** Handle Remove Organization */
        handleOrgRemove()
        {
            show_ajax_loader();
            this.searchOrgText = '';
            this.subOrganization = '';
            this.subOrganizations = [];
            this.searchModelText = '';
            this.devicemodeldata = [];
            this.users = [];
            this.selectedDeviceModel = '';
            hide_ajax_loader();
        },
        /** Handle Sub-Organization Selection */
        handleSelectSubOrg() {
            show_ajax_loader();
            if (this.subOrganization && this.subOrganization?.id) {
                /* Clear all dependent fields */
                this.users = [];
                this.getUsers(this.organization.id,this.subOrganization.id);
                hide_ajax_loader();
            } else {
                /* Clear all dependent fields */
                this.users = [];
                this.getUsers(this.organization.id);
                hide_ajax_loader();
            }
        },

        /** Handle Remove Sub-Organization */
        handleSubOrgRemove()
        {
            show_ajax_loader();
            this.users = [];
            this.getUsers(this.organization.id);
            hide_ajax_loader();
        },

        /** get sub organizations of organizations */
        async getDeviceSubOrg(orgID) {
            show_ajax_loader();
            if (!orgID) {
                this.subOrganizations = [];
            }
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/organizations/sub-organizations?orgId=${orgID}`
                );
                if (response.data.success == true) {
                    this.subOrganizations = response.data.subOrgData;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again';
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin/`), 5000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /* get device models */
        async getDeviceModels(orgID, name = this.searchModelText) {
            show_ajax_loader();
            /* If no orgID, clear the device model data */
            if (!orgID) {
                this.devicemodeldata = [
                    {
                        id: null,
                        title: "Please select the organization first",
                    },
                ];
                hide_ajax_loader();
                return;
            }
            /* Enter at list 5 characters */
            if (name && name.length < 5) {
                this.devicemodeldata = [
                    {
                        id: null,
                        title: "Please enter at least five characters",
                    },
                ];
                hide_ajax_loader();
                return;
            }
            try {
                let response = null;
                const url = `${this.$userAppUrl}smarttiusadmin/device-model/fetch-models?orgId=${orgID}${name ? '&name=' + name : ''}`;
                /* If name is empty, we don't want to make the API call */
                response = await axios.get(url);
                if (response.data.success && response.data.deviceModels.length > 0) {
                    this.devicemodeldata = response.data.deviceModels;
                    hide_ajax_loader();
                } else {
                    /* This will show up in the dropdown regardless of success status */
                    this.devicemodeldata = [
                        {
                            id: null,
                            title: "No device found",
                        },
                    ];
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`),3000 );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        updateSearchModelName(value) {
            if (value) {
                this.searchDeviceText = value;
                this.getDeviceModels(this.organization.id, value);
            } else {
                this.searchDeviceText = '';
                this.getDeviceModels(this.organization.id);
            }
        },

        /** get users of organization or sub-org*/
        async getUsers(orgID, subOrgID) {
            show_ajax_loader();

            if (!orgID) {
                this.users = [
                    {
                        id: null,
                        full_name: "Please select the organization first",
                    },
                ];
                this.selectedUser = null; // Clear selected user if no org is selected
                hide_ajax_loader();
                return;
            }

            try {
                const url = `${this.$userAppUrl}smarttiusadmin/import-devices/users?orgId=${orgID}${subOrgID ? '&subOrgId=' + subOrgID : ''}`;
                const response = await axios.get(url);

                if (response.data.success && response.data.users.length > 0) {
                    this.users = response.data.users; // Update users list
                    this.selectedUser = null; // Reset selected user when new data is fetched
                    hide_ajax_loader();
                } else {
                    this.users = [
                        {
                            id: null,
                            full_name: "No user found",
                        },
                    ];
                    this.selectedUser = null; // Reset if no users are found
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        /** Handle File Upload */
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.csvFile = file;
            }
        },

        /** close Import model */
        closeImportModel() {
            show_ajax_loader();
            /** Close add model */
            hide_admin_popup('#importDeviceModalClose');
            this.resetImportForm();
            hide_ajax_loader();
        },

        /** On click of New Import */
        async importNewDevices() {
            show_ajax_loader();
            /** Clearing the validations errors if stored any here */
            this.validationErrors = {};
            /** To show the popup */
            this.getOrganizations(this.searchOrgText);
            hide_ajax_loader();
        },

        /** Reset Import form */
        resetImportForm() {
            show_ajax_loader();
            /** Make field variables empty */
            this.selectedDeviceModel = '';
            this.devicemodeldata = [];
            this.searchOrgText = '';
            this.organization = '';
            this.subOrganization = '';
            this.subOrganizations = [];
            this.csvFile = null;
            this.validationErrors = {};
            this.deviceData = {};
            this.selectedUser = null;
            this.users = [];
            this.deviceExp = '';
            this.searchModelText = '';
            this.importId = '';
            hide_ajax_loader();
        },

        /* Import the Devices Save in db */
        async importDevices() {
            show_ajax_loader();
            this.validationErrors = {};
            let validatePassed = true;
            /** Client side validations */
            if(!this.organization || !this.organization.id){
                this.validationErrors.organization = ["Please Select the Organization."];
                validatePassed = false;
            }
            if(!this.selectedDeviceModel || !this.selectedDeviceModel.id){
                this.validationErrors.selectedDeviceModel = ["Please Select the Device model."];
                validatePassed = false;
            }

            if(!this.selectedUser || !this.selectedUser.id){
                this.validationErrors.users = ["Please Select the User."];
                validatePassed = false;
            }
            if(!this.deviceExp){
                this.validationErrors.deviceExp = ["Please Select the expiration date."];
                validatePassed = false;
            }
            if(!this.csvFile){
                this.validationErrors.file = ["Please Select the CSV file."];
                validatePassed = false;
            }
            /** Return if any validation failed */
            if(!validatePassed){
                hide_ajax_loader();
                return;
            }

            try {
                /* Create a single object instead of an array */
                this.deviceData = {
                    organization: this.organization.id,
                    subOrganization: this.subOrganization.id ?? '',
                    selectedDeviceModel: this.selectedDeviceModel.id,
                    users: this.selectedUser.id,
                    deviceModelTitle: this.selectedDeviceModel.title,
                    deviceExp: this.deviceExp,
                    file: this.csvFile,
                };
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/import-devices/store`, this.deviceData, {headers: { "Content-Type": "multipart/form-data" }});
                if(response.data.success == true) {
                    this.importId = response.data.import_id;
                    hide_ajax_loader();
                } else if (response.data.errors ) {
                    this.validationErrors = response.data.errors;
                    hide_ajax_loader();
                } else if(response.data.rcode == 1){
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "It HOD or Director not found for the organization.";
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

        handleImportComplete() {
            // Do something when import is complete
            this.importId = null;
            this.$alertMessage.success = true;
            this.$alertMessage.message = 'CSV processed successfully.';
            this.getImportedDevices();
            /** Reset Form  */
            this.closeImportModel();
        },

        /** Download Sample CSV File */
        async downlaodSampleCSV(filepath, filename) {
            try {
                let fullPath = filepath.startsWith('/') ? filepath.substring(1) : filepath;
                const response = await axios.get(`${window.location.origin}/${fullPath}${filename}`, {
                    responseType: 'blob',
                });
                const blob = new Blob([response.data], { type: 'application/csv' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = filename;
                link.click();
            } catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Failed to download CSV.";
            }
        },

        /** Download CSV Of import devices */
        async downloadImportDeviceCsv(id) {
            show_ajax_loader();

            if (!id) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
                return;
            }

            try {
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/import-devices/csv/${id}`,{ responseType: 'blob' });

                /* Try to parse blob as text */
                const text = await new Response(response.data).text();

                try {
                    /* Try parse as JSON (error message) */
                    const json = JSON.parse(text);

                    /* If parsed JSON and has success == false, show error message */
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = json.msg || "Something went wrong, Please try again later.";
                } catch {
                    /* Not JSON → it's file data, trigger download */
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'imported_devices.csv');
                    document.body.appendChild(link);
                    link.click();
                    link.remove();

                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = 'CSV downloaded successfully.';
                }

            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Failed to download CSV.";
            }
        },
        /** On click of the cancel from the search */
        async cancelSearch() {
            show_ajax_loader();
            this.search_name = '';
            this.getImportedDevices(1);
            hide_ajax_loader();
        },


    },
};
</script>