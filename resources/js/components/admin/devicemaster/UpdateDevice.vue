<template>
    <div class="container-fluid">
        <div class="card p-3 mt_12 onewhitebg shadow-md rounded-md">
            <h4 class="coman_main_heading border-bottom pb-2 mb-3">Edit Device</h4>
            <!-- Edit device form -->
            <form class="form">
                <!-- Select the device type -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Device Type</label>
                        <div class="row mx-1">
                            <div class="form-check col-6 col-md-6">
                                <input type="radio" v-model="deviceType" id="deviceType1" :class="['form-check-input', { 'is-invalid': insertValidations.deviceType, },]" value="1" :disabled="deviceType == 2" />
                                <label class="form-check-label themetextcolor" for="deviceType1">
                                    For Personal devices
                                </label>
                            </div>
                            <div class="form-check col-6 col-md-6">
                                <input type="radio" v-model="deviceType" id="deviceType2"
                                    :class="['form-check-input', { 'is-invalid': insertValidations.deviceType, },]" value="2" :disabled="deviceType == 1" />
                                <label class="form-check-label themetextcolor" for="deviceType2">
                                    For Organization devices
                                </label>
                            </div>
                        </div>
                        <small v-if="insertValidations.deviceType" ><ErrorMessage :msg="insertValidations.deviceType[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Select the organization and sub-organization -->
                <div v-if="deviceType == 2" class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Organization Name <span class="text-danger">*</span></label>
                        <multiselect id="tagging" v-model="deviceOrg" placeholder="Search or choose organization" label="name" track-by="id" :options="deviceorgdata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': insertValidations.deviceOrg },]" required>
                            <template #noResult>
                                <span class="custom-message">No organization found.</span>
                            </template>
                            <template #noOptions>
                                <span class="custom-message">No organization found.</span>
                            </template>
                        </multiselect>
                        <small v-if="insertValidations.deviceOrg" ><ErrorMessage :msg="insertValidations.deviceOrg[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sub-Organization Name</label>
                        <multiselect id="tagging" v-model="deviceSubOrg" placeholder="Search or choose sub organization" @remove="onSubOrgRemoved" label="name" track-by="id" :options="deviceSubOrgData" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': insertValidations.deviceSubOrg, },]" required>
                            <template #noResult>
                                <span class="custom-message">No sub organization found.</span>
                            </template>
                            <template #noOptions>
                                <span class="custom-message">No sub organization found.</span>
                            </template>
                        </multiselect>
                        <small v-if="insertValidations.deviceSubOrg"><ErrorMessage :msg="insertValidations.deviceSubOrg[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Select device model -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Device Model <span class="text-danger">*</span></label>
                        <multiselect id="tagging" v-model="deviceModel" :searchable="true" @search-change="updateSearchModelName" @keyup.enter="updateSearchModelName(this.searchModelText)" @select="handleSelectModel" @remove="removeModel" placeholder="Search or choose device model" label="title" track-by="id" :options="devicemodeldata" :multiple="false" :taggable="false" :class="['', { 'is-invalid': insertValidations.deviceModel, },]" required :allow-empty="true" :internal-search="true" :preserve-search="true" selectLabel="" deselectLabel="" >
                             <template #noOptions>
                                <span class="custom-message">Please select the organization first</span>
                            </template>
                            <template #noResult>
                                <span class="custom-message">No device model found.</span>
                            </template>
                        </multiselect>
                        <small v-if="insertValidations.deviceModel" ><ErrorMessage :msg="insertValidations.deviceModel[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Serial Number/Asset Tag <span class="text-danger">*</span></label>
                        <input type="text" v-model="deviceSerialNumber" :class="['form-control', { 'is-invalid': insertValidations.deviceSerialNumber, },]" placeholder="Enter Serial Number/ Asset Tag" />
                        <small v-if="insertValidations.deviceSerialNumber" ><ErrorMessage :msg="insertValidations.deviceSerialNumber[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- IMEI and other details -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">IMEI</label>
                        <input type="text" v-model="deviceImei" :class="['form-control', { 'is-invalid': insertValidations.deviceImei },]" placeholder="IMEI" />
                        <small v-if="insertValidations.deviceImei"><ErrorMessage :msg="insertValidations.deviceImei[0]"></ErrorMessage></small>
                    </div>

                    <div :class="['mb-3', deviceCellService == 1 ? 'col-md-3' : 'col-md-6',]">
                        <label class="form-label">Device Cellular Service</label>
                        <select v-model="deviceCellService" :class="['form-control', { 'is-invalid': insertValidations.deviceCellService, },]">
                            <option value="" selected="selected">SELECT</option>
                            <option value="1">Yes</option>
                            <option value="2">No</option>
                        </select>
                        <small v-if="insertValidations.deviceCellService"><ErrorMessage :msg="insertValidations.deviceCellService[0]"></ErrorMessage></small>
                    </div>
                    <div v-if="deviceCellService == 1" class="col-md-3 mb-3">
                        <label class="form-label">Device Carrier</label>
                        <select v-model="deviceCarrier" :class="['form-control', { 'is-invalid': insertValidations.deviceCarrier, },]">
                            <option value="" selected="selected">SELECT</option>
                            <option v-for="(carrier, index) in $deviceCarrier" :value="carrier">
                                {{ carrier }}
                            </option>
                        </select>
                        <small v-if="insertValidations.deviceCarrier" ><ErrorMessage :msg="insertValidations.deviceCarrier[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Device capacity, color and user -->
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Device Capacity</label>
                        <select v-model="deviceCapacity" :class="['form-control', { 'is-invalid': insertValidations.deviceCapacity, },]">
                            <option value="" selected="selected">SELECT</option>
                            <option v-for="(capacity, index) in $deviceCapacity" :value="capacity">
                                {{ capacity }}
                            </option>
                        </select>
                        <small v-if="insertValidations.deviceCapacity" ><ErrorMessage :msg="insertValidations.deviceCapacity[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Device Color</label>
                        <input type="text" v-model="deviceColor" :class="['form-control', { 'is-invalid': insertValidations.deviceColor },]" placeholder="Enter Device Color" />
                        <small v-if="insertValidations.deviceColor" ><ErrorMessage :msg="insertValidations.deviceColor[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">User <span class="text-danger">*</span></label>
                        <multiselect id="tagging" v-model="deviceUser" :searchable="true" @search-change="updateSearchText" :custom-label="nameWithEmail" @keyup.enter="updateSearchText(this.searchUserText)" placeholder="Search or choose user" label="full_name" track-by="id" :options="deviceuserdata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="{'is-invalid': insertValidations.deviceUser, }" required :allow-empty="true" :internal-search="false" :preserve-search="true">
                        </multiselect>
                        <small v-if="insertValidations.deviceUser" ><ErrorMessage :msg="insertValidations.deviceUser[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Owner details -->
                <div v-if="deviceType == 1" class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Owner's First Name <span class="text-danger">*</span></label>
                        <input type="text" v-model="deviceOwnerFirstName" :class="['form-control', { 'is-invalid': insertValidations.deviceOwnerFirstName, },]" placeholder="Enter Owner First Name" />
                        <small v-if="insertValidations.deviceOwnerFirstName" ><ErrorMessage :msg="insertValidations.deviceOwnerFirstName[0]"></ErrorMessage></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Owner's Last Name</label>
                        <input type="text" v-model="deviceOwnerLastName" :class="['form-control', { 'is-invalid': insertValidations.deviceOwnerLastName,},]" placeholder="Enter Owner Last Name" />
                        <small v-if="insertValidations.deviceOwnerLastName"><ErrorMessage :msg="insertValidations.deviceOwnerLastName[0]"></ErrorMessage></small>
                    </div>
                </div>
                <div v-if="deviceType == 2" class=" mt-3">
                    <h5 class="def_20_size border-bottom pb-2 fw-semibold mb-0 mt-2 themetextcolor">
                        {{ orgType == 1 ? 'Education Device Information' : 'Organization Device Information' }}
                    </h5>

                    <div class="row">
                        <div class="col-md-6 my-3 ">
                            <label class="form-label"> {{ orgType == 1 ? "Parent's Name" : "Owner's Full Name" }} <span class="text-danger">*</span></label>
                            <input type="text" v-model="deviceOwnerName" :class="['form-control', {'is-invalid': insertValidations.deviceOwnerName,},]" :placeholder="orgType == 1 ? 'Enter Parent Full Name' : 'Enter Owner Full Name'"/>
                            <small v-if="insertValidations.deviceOwnerName" ><ErrorMessage :msg="insertValidations.deviceOwnerName[0]"></ErrorMessage></small>
                        </div>

                        <div class="col-md-6 my-3 ">
                            <label class="form-label">{{ orgType == 1 ? "   Stundet's Name" : "Organization User Full Name" }} <span class="text-danger">*</span></label>
                            <input type="text" v-model="deviceOrgUserName" :class="['form-control',{ 'is-invalid': insertValidations.deviceOrgUserName, },]" :placeholder="orgType == 1 ? 'Enter Student Full Name' : 'Enter Organization User Full Name'" />
                            <small v-if="insertValidations.deviceOrgUserName" ><ErrorMessage :msg="insertValidations.deviceOrgUserName[0]"></ErrorMessage></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ orgType == 1 ? "   Stundet's Grade" : "User Designation" }} <span class="text-danger">*</span></label>
                            <input type="text" v-model="deviceOrgUserDesig" :class="[ 'form-control', {'is-invalid': insertValidations.deviceOrgUserDesig,},]" :placeholder="orgType == 1 ? 'Enter Student Grade' : 'Enter Organization User Designation'" />
                            <small v-if="insertValidations.deviceOrgUserDesig" ><ErrorMessage :msg="insertValidations.deviceOrgUserDesig[0]"></ErrorMessage></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asset Tag</label>
                            <input type="text" v-model="deviceOrgAssetTag" :class="['form-control', {'is-invalid': insertValidations.deviceOrgAssetTag,},]" placeholder="Asset tag" />
                            <small v-if="insertValidations.deviceOrgAssetTag" ><ErrorMessage :msg="insertValidations.deviceOrgAssetTag[0]"></ErrorMessage></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ orgType == 1 ? "   Stundet's ID" : "Organization User ID" }} <span class="text-danger">*</span></label>
                            <input type="text" v-model="deviceOrgUserId" :class="['form-control',{'is-invalid': insertValidations.deviceOrgUserId,},]" :placeholder="orgType == 1 ? 'Enter Student ID' : 'Enter Organization User ID'" />
                            <small v-if="insertValidations.deviceOrgUserId" ><ErrorMessage :msg="insertValidations.deviceOrgUserId[0]"></ErrorMessage></small>
                        </div>
                    </div>
                </div>
                <!-- Coverage informations -->
                <h5 class="def_20_size border-bottom pb-2 fw-semibold mb-0 mt-2 themetextcolor">Coverage Information</h5>
                <div class="mt-3">


                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <select v-model="deviceCoveragePlan" :class="['form-control', { 'is-invalid': insertValidations.deviceCoveragePlan }]">
                                <option value="0" >Select Plan</option>
                                <option v-for="plan in ModelPlanData" v-if="ModelPlanData.length > 0" :key="plan.id" :value="plan.id">{{ plan.plan_name }}</option>
                            </select>
                            <small v-if="insertValidations.deviceCoveragePlan"><ErrorMessage :msg="insertValidations.deviceCoveragePlan[0]"></ErrorMessage></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Plan Expiration Date <span class="text-danger">*</span></label>
                            <input type="date"v-model="deviceCoverageExDate" :class="['form-control',{'is-invalid': insertValidations.deviceCoverageExDate,},]" placeholder="Enter the expiration days." />
                            <small v-if="insertValidations.deviceCoverageExDate" ><ErrorMessage :msg="insertValidations.deviceCoverageExDate[0]"></ErrorMessage></small>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" @click="updateDevice" class="btn bg_blue text-white">
                        Submit
                    </button>
                    <a href="/smarttiusadmin/devices" class="btn customm_reset_btn">Back</a>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        deviceorgdata: Array,
        devices: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            deviceId: this.devices.id,
            deviceOrg:
                this.deviceorgdata.find(
                    (org) => org.id == this.devices.org_id
                ) || null,
            deviceUser: null,
            searchUserText: '',
            searchModelText: "",
            deviceSerialNumber: this.devices.serial_number,
            deviceImei: this.devices.imei,
            orgID: this.devices.org_id,
            orgType: this.devices.org_type,
            deviceType: this.devices.device_type,
            deviceCarrier: this.devices.carrier ?? '',
            deviceCapacity: this.devices.capacity ?? '',
            deviceColor: this.devices.color ?? '',
            deviceCellService: this.devices.cellular_service ?? '',
            deviceOwnerName: this.devices.device_owner_name,
            deviceOwnerFirstName: this.devices.org_user_first_name,
            deviceOwnerLastName: this.devices.org_user_last_name,
            deviceOrgUserName: this.devices.org_user_full_name,
            deviceModel: null,
            devicemodeldata: [],
            deviceuserdata: [],
            deviceOrgUserDesig: this.devices.org_user_designation,
            deviceOrgAssetTag: this.devices.asset_tag,
            deviceOrgUserId: this.devices.org_user_id,
            deviceCoverageExDate: this.devices.expiration_date.split(' ')[0],
            deviceCoveragePlan: '0',
            // deviceCoveragePlan: this.devices.plan_id ?? '0',
            deviceBillingCycle: this.devices.billing_cycle_id,
            deviceSubscriptionId: this.devices.subscription_id,
            deviceSubOrg: this.devices.sub_org_id,
            deviceSubOrgData: [],
            insertValidations: {},
            ModelPlanData: [],
        };
    },
    watch: {
        deviceOrg(newVal) {
            if (newVal && newVal.id) {
                this.orgID = newVal.id;
                this.deviceSubOrgData = [];
                this.deviceuserdata = [];
                this.devicemodeldata = [];
                this.deviceModel = null;
                this.deviceUser = null;
                /* Load sub-organizations without clearing the selection initially */
                this.getDeviceSubOrg(newVal.id);
                this.getDeviceModels(null, this.searchModelText, newVal.id);
                this.getUsers(null, this.searchUserText, newVal.id, this.deviceSubOrg ? this.deviceSubOrg.id : null);
            } else {
                /* Clear only if there's no valid organization selected */
                this.deviceSubOrgData = [];
                this.deviceuserdata = [];
                this.devicemodeldata = [];
                this.deviceSubOrg = "";
                this.deviceModel = null; /* Clear device model when org is not selected */
                this.orgID = "";
                this.deviceUser = null
            }
        },
    },
    created() {
        /** Calling functions on page load */
        if (this.devices.org_id) {
            this.deviceOrg = this.deviceorgdata.find((org) => org.id == this.devices.org_id) || null;
            this.getDeviceSubOrg(this.devices.org_id);
            this.getDeviceModels(this.devices.device_model_id,this.searchModelText,this.devices.org_id);
            this.getUsers(this.devices.user_id, this.searchUserText, this.devices.org_id, this.devices.sub_org_id);
        } else {
            this.getDeviceModels(this.devices.device_model_id,this.searchModelText,this.devices.org_id);
            this.getUsers(this.devices.user_id, this.searchUserText, this.devices.org_id, this.devices.sub_org_id);
        }

        if(this.devices.device_model_id){
            this.DevicePlan(this.devices.device_model_id, this.devices.device_type, this.devices.org_id)
        }
    },

    methods: {
        /** When the sub organization is removed */
        onSubOrgRemoved() {
            this.searchUserText = "";
            this.getUsers(this.devices.user_id, this.searchUserText, this.devices.org_id, null);
            this.deviceUser = "";

        },
        /** getting user data */
        async getUsers(userID = null, name = this.searchUserText, orgID = null, subOrgID = null) {
            show_ajax_loader();
            /* Enter atleast 2 characters */
            if (name && name.length < 2) {
                this.deviceuserdata = [{
                    id: null,
                    full_name: "Please enter at least two characters",
                    first_name: "",
                    last_name: "",
                    email: ""
                }];
                hide_ajax_loader();
                return;
            }
            try {
                const url = `${this.$userAppUrl}smarttiusadmin/devices/get-users?${userID ? `deviceUserId=${userID}` : ""}&name=${name}${orgID ? `&orgId=${orgID}` : ''}${subOrgID ? `&subOrgId=${subOrgID}` : ''}`;
                if(name !== this.searchUserText){
                    this.deviceuserdata = [{
                            id: null,
                            full_name: "Please enter at least two characters",
                            first_name: "",
                            last_name: "",
                            email: ""
                    }];
                    hide_ajax_loader();
                    return;
                }
                const response = await axios.get(url);

                /* Always ensure deviceuserdata has at least one option */
                if (response.data.success && response.data.users.length > 0) {
                    if (userID) {
                        this.deviceUser = response.data.users;
                    }
                    this.deviceuserdata = response.data.users;
                    hide_ajax_loader();
                } else {
                    /* This will show up in the dropdown regardless of success status */
                    this.deviceuserdata = [{
                        id: null,
                        full_name: "No user found",
                        first_name: "",
                        last_name: "",
                        email: ""
                    }];
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 5000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** get sub organizations of device organizations */
        async getDeviceSubOrg(orgID) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/organizations/sub-organizations?orgId=${orgID}`
                );

                if (response.data.success == true) {
                    this.deviceSubOrgData = response.data.subOrgData;

                    /* Wait for the data to populate before selecting */
                    this.$nextTick(() => {
                        const selectedSubOrg = this.deviceSubOrgData.find(
                            (subOrg) => subOrg.id == this.devices.sub_org_id
                        );
                        this.deviceSubOrg = selectedSubOrg
                            ? selectedSubOrg
                            : null;
                    });
                    hide_ajax_loader();
                }
                else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        4000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** Get device models */
        async getDeviceModels(modelID = null, name = this.searchModelText, orgID = null) {
            show_ajax_loader();
            /* Enter at least 5 characters */
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
                let url = null;
                if (this.deviceType == 2) {
                    url = `${this.$userAppUrl}smarttiusadmin/device-model/fetch-models?orgId=${orgID}${modelID ? `&deviceModelId=${modelID}` : ""}${name ? `&name=${name}` : ''}`;
                }else if(modelID){
                    url = `${this.$userAppUrl}smarttiusadmin/device-model/fetch-models${modelID ? `?deviceModelId=${modelID}` : ""}`;
                }else {
                    url = `${this.$userAppUrl}smarttiusadmin/device-model/fetch-models?name=${name}`;
                }
                if (name !== this.searchModelText) {
                    this.devicemodeldata = [
                        {
                            id: null,
                            title: "Please enter at least five characters",
                        },
                    ];
                    hide_ajax_loader();
                    return;
                }
                const response = await axios.get(url);

                if (response.data.success == true && response.data.deviceModels.length > 0) {

                    if (modelID) {
                        this.deviceModel = response.data.deviceModels;
                        this.deviceModel = this.deviceModel.find((model) => model.id == modelID) || null;
                    }
                    this.devicemodeldata = response.data.deviceModels;
                    hide_ajax_loader();
                } else {
                    this.devicemodeldata = [
                        {
                            id: null,
                            title: "No device model found",
                        },
                    ];
                    this.deviceModel = null; /* Clear selection if no models found */
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** getting device plans */
        async DevicePlan(model_id = null, device_type, org_id){
            show_ajax_loader();
            this.ModelPlanData = [];
            if(model_id){
                const payload = {
                    modelId: model_id,
                    planType: device_type,
                    orgId: org_id ?? '',
                };

                try{
                    let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/devices-plan/get-device-plan`, payload);
                    if(response.data.success == true){
                        if(response.data.deviceModelPlans){
                            this.ModelPlanData = response.data.deviceModelPlans;
                        }
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
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
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    }
                }
            }
        },

        updateSearchText(searchText) {
            show_ajax_loader();
            if (searchText.length > 0) {
                this.searchUserText = searchText;
                this.getUsers(null, this.searchUserText, this.orgID, this.deviceSubOrg ? this.deviceSubOrg.id : null);
                hide_ajax_loader();
            } else {
                this.searchUserText = "";
                this.deviceuserdata = [{ id: null, full_name: "Please enter at least two characters" }];
                hide_ajax_loader();
            }
        },
        /** user name with email */
        nameWithEmail(option) {
            const email = option.email ? ` (${option.email})` : '';
            return `${option.full_name}${email}`;
        },

        updateSearchModelName(name) {
            show_ajax_loader();
            if (name.length > 0) {
                this.searchModelText = name;
                /* Fetch device models based on the search name and organization ID */
                this.getDeviceModels(null, this.searchModelText, this.orgID);
                hide_ajax_loader();
            } else {
                this.searchModelText = "";
                this.devicemodeldata = [{ id: null, title: "Please enter at least five characters" }];
                hide_ajax_loader();
            }
        },

        /** When the device is selected */
        async handleSelectModel(){
            show_ajax_loader();
            this.DevicePlan(
                this.deviceModel.id,
                this.deviceType,
                this.orgID
            );
            hide_ajax_loader();
        },

        /** When the device is dis-selected*/
        removeModel(){
            this.ModelPlanData = [];
            this.deviceCoveragePlan = '0';
        },

        /* Submit Device */
        async updateDevice(e) {
            e.preventDefault();
            show_ajax_loader();
            this.insertValidations = {};
            let validationPassed = true;

            /** Client side validations */
            if(this.deviceType == 2){
                if(!this.deviceOrg || this.deviceOrg == null){
                    this.insertValidations.deviceOrg = ['Please select organization.'];
                    validationPassed = false;
                }
            }

            if(!this.deviceUser){
                this.insertValidations.deviceUser = ['Please select user.'];
                validationPassed = false;
            }

            if(!this.deviceSerialNumber){
                this.insertValidations.deviceSerialNumber = ['Please enter serial number.'];
                validationPassed = false;
            }

            /*if(!this.deviceOwnerFirstName && this.deviceType == 1){
                this.insertValidations.deviceOwnerFirstName = ['Please enter owner first name.'];
                validationPassed = false;
            }

            if(!this.deviceOwnerLastName && this.deviceType == 1){
                this.insertValidations.deviceOwnerLastName = ['Please enter owner last name.'];
                validationPassed = false;
            }

            if(!this.deviceOwnerName && this.deviceType == 2){
                this.insertValidations.deviceOwnerName = ['Please enter owner full name.'];
                validationPassed = false;
            }

            if(!this.deviceOrgUserName && this.deviceType == 2){
                this.insertValidations.deviceOrgUserName = ['Please enter organization user full name.'];
                validationPassed = false;
            }*/

            if(!this.deviceModel){
                this.insertValidations.deviceModel = ['Please select device model.'];
                validationPassed = false;
            }

            if(!this.deviceCoverageExDate){
                this.insertValidations.deviceCoverageExDate = ['Please select expiration date.'];
                validationPassed = false;
            }

            if(!this.deviceCoveragePlan){
                this.insertValidations.deviceCoveragePlan = ['Please select plan.'];
                validationPassed = false;
            }

            /** Return if any validation failed */
            if(!validationPassed){
                hide_ajax_loader();
                return;
            }

            try {
                /* Create a single object to store the device data */
                const deviceData = {
                    deviceOrg: this.deviceOrg ? this.deviceOrg.id : null,
                    deviceUser: this.deviceUser[0] ? this.deviceUser[0].id : this.deviceUser.id,
                    deviceSerialNumber: this.deviceSerialNumber,
                    deviceImei: this.deviceImei,
                    deviceType: this.deviceType,
                    deviceOwnerFirstName: this.deviceOwnerFirstName,
                    deviceOwnerLastName: this.deviceOwnerLastName,
                    deviceOwnerName: this.deviceOwnerName,
                    deviceOrgUserName: this.deviceOrgUserName,
                    deviceModelTitle: this.deviceModel[0] ? this.deviceModel[0].title : this.deviceModel.title,
                    deviceModel: this.deviceModel[0] ? this.deviceModel[0].id : this.deviceModel.id,
                    deviceCarrier: this.deviceCarrier,
                    deviceCapacity: this.deviceCapacity,
                    deviceColor: this.deviceColor,
                    deviceCellService: this.deviceCellService,
                    deviceOrgUserDesig: this.deviceOrgUserDesig,
                    deviceOrgAssetTag: this.deviceOrgAssetTag,
                    deviceOrgUserId: this.deviceOrgUserId,
                    deviceCoverageExDate: this.deviceCoverageExDate,
                    deviceCoveragePlan: this.deviceCoveragePlan,
                    deviceBillingCycle: this.deviceBillingCycle,
                    deviceSubOrg: this.deviceSubOrg ? this.deviceSubOrg.id : null,
                    /* deviceSubscriptionId: this.deviceSubscriptionId, */
                    deviceSubscriptionId: null,
                };

                let response = await axios.post(
                    `${this.$userAppUrl}smarttiusadmin/devices/update/${this.deviceId}`,
                    deviceData
                );

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    setTimeout(
                        () =>
                            (location.href = `${this.$userAppUrl}smarttiusadmin/devices`),
                        3000
                    );
                } else if(response.data.errors) {
                    this.insertValidations = response.data.errors;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = response.data.msg;
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
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },
    },
};
</script>
