<template>
    <div class="container-fluid">
        <div class="card p-3 mt_12 onewhitebg shadow-md rounded-md">
            <h4 class="coman_main_heading mb-3 border-bottom pb-2">Add New Device</h4>
            <form class="form">
                <!-- Selecting the device type -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Device Type <span class="text-danger">*</span></label>
                        <div class="toggleWrapper">
                            <div class="toggleinner">
                                <label class="form-label">For Personal devices</label>
                                <input class="mobileToggle" type="radio" v-model="deviceType" id="deviceType1" :class="['form-check-input',{'is-invalid': insertValidations.deviceType,},]" value="1" checked />
                                <label class="form-check-label" for="deviceType1">

                                </label>
                            </div>
                            <div class="toggleinner">
                                <label class="form-label">For Organization devices</label>
                                <input class="mobileToggle"  type="radio" v-model="deviceType" id="deviceType2" :class="['form-check-input',{'is-invalid':insertValidations.deviceType,},]" value="2" />
                                <label class="form-check-label" for="deviceType2">

                                </label>
                            </div>
                        </div>
                        <small v-if="insertValidations.deviceType"><ErrorMessage :msg="insertValidations.deviceType[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Select organization if it's a organizational device -->
                <div v-if="deviceType == '2'" class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Organization Name <span class="text-danger">*</span></label>
                        <multiselect id="org" v-model="deviceOrg" placeholder="Search or choose organization" label="name" track-by="id" :options="deviceorgdata" @select="onOrgSelected" @remove="onOrgRemoved" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['',{ 'is-invalid': insertValidations.deviceOrg },]" required>
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
                        <multiselect id="sub-org" v-model="deviceSubOrg" placeholder="Search or choose sub organization" label="name" track-by="id" :options="deviceSubOrgData" @remove="onSubOrgRemoved" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', {'is-invalid':insertValidations.deviceSubOrg,},]" required>
                            <template #noResult>
                                <span class="custom-message">No sub organization found.</span>
                            </template>
                            <template #noOptions>
                                <span class="custom-message">No sub organization found.</span>
                            </template>
                        </multiselect>
                        <small v-if="insertValidations.deviceSubOrg" ><ErrorMessage :msg="insertValidations.deviceSubOrg[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Select the device model and Asset tag -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Device Model <span class="text-danger">*</span></label>
                        <multiselect id="device-model" v-model="deviceModel" :searchable="true" @search-change="updateSearchModelName" @select="handleSelectModel" @remove="removeModel" placeholder="Search or choose device model" label="title" track-by="id" :options="devicemodeldata" :multiple="false" :taggable="false" :class="['', {'is-invalid': insertValidations.deviceModel,},]" :allow-empty="true" :internal-search="true" :preserve-search="true" selectLabel="" deselectLabel="" required >
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
                        <input type="text" v-model="deviceSerialNumber" :class="['form-control',{'is-invalid': insertValidations.deviceSerialNumber,},]" placeholder="Enter Serial Number/ Asset Tag" />
                        <small v-if="insertValidations.deviceSerialNumber" ><ErrorMessage :msg="insertValidations.deviceSerialNumber[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!--  Select IMEI and Cellular service -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">IMEI</label>
                        <input type="text" v-model="deviceImei" :class="['form-control', {'is-invalid': insertValidations.deviceImei },]" placeholder="IMEI" />
                        <small v-if="insertValidations.deviceImei" ><ErrorMessage :msg="insertValidations.deviceImei[0]"></ErrorMessage></small>
                    </div>
                    <div :class="['mb-3', deviceCellService == 1 ? 'col-md-3' : 'col-md-6']">
                        <label class="form-label">Device Cellular Service</label>
                        <select v-model="deviceCellService" :class="['form-control', {'is-invalid': insertValidations.deviceCellService,},]">
                            <option value="" selected="selected">SELECT</option>
                            <option value="1">Yes</option>
                            <option value="2">No</option>
                        </select>
                        <small v-if="insertValidations.deviceCellService"><ErrorMessage :msg="insertValidations.deviceCellService[0]"></ErrorMessage></small>
                    </div>
                    <div v-if="deviceCellService == '1'" class="col-md-3 mb-3">
                        <label class="form-label">Device Carrier</label>
                        <select v-model="deviceCarrier" :class="['form-control',{'is-invalid': insertValidations.deviceCarrier,},]">
                            <option value="" selected="selected">SELECT</option>
                            <option v-for="(carrier, index) in $deviceCarrier" :value="carrier">
                                {{ carrier }}
                            </option>
                        </select>
                        <small v-if="insertValidations.deviceCarrier" ><ErrorMessage :msg="insertValidations.deviceCarrier[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Select Device capacity, Colour, and User -->
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Device Capacity</label>
                        <select v-model="deviceCapacity" :class="['form-control', {'is-invalid': insertValidations.deviceCapacity,},]">
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
                        <multiselect id="user" v-model="deviceUser" :searchable="true" @search-change="updateSearchText" @keyup.enter="updateSearchText" :custom-label="nameWithEmail" placeholder="Search or choose user" label="full_name" track-by="id" :options="deviceuserdata" :multiple="false" :taggable="false" :class="{ 'is-invalid': insertValidations.deviceUser }" :allow-empty="true" :internal-search="false" :preserve-search="true" selectLabel="" deselectLabel="" required>

                        </multiselect>
                        <small v-if="insertValidations.deviceUser" ><ErrorMessage :msg="insertValidations.deviceUser[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Enter owner details  -->
                <div v-if="deviceType == '1'" class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Owner's First Name <span class="text-danger">*</span></label>
                        <input type="text" v-model="deviceOwnerFirstName" :class="['form-control', {'is-invalid': insertValidations.deviceOwnerFirstName,},]" placeholder="Enter Owner First Name" />
                        <small v-if="insertValidations.deviceOwnerFirstName" ><ErrorMessage :msg="insertValidations.deviceOwnerFirstName[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Owner's Last Name</label>
                        <input type="text" v-model="deviceOwnerLastName" :class="[ 'form-control', {'is-invalid': insertValidations.deviceOwnerLastName,},]" placeholder="Enter Owner Last Name" />
                        <small v-if="insertValidations.deviceOwnerLastName" ><ErrorMessage :msg="insertValidations.deviceOwnerLastName[0]"></ErrorMessage></small>
                    </div>
                </div>

                <div v-if="deviceType == '2'" class=" mt-4">
                    <h5 class="def_20_size border-bottom pb-2 fw-semibold mb-3 mt-2">{{ deviceOrg && deviceOrg.org_type == 1 ? 'Educational Device Information' : 'Organization Device Information' }}</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ deviceOrg && deviceOrg.org_type == 1 ? "Parent's Name" : "Owner's Full Name" }} <span class="text-danger">*</span></label>
                            <input type="text" v-model="deviceOwnerName" :class="['form-control',{'is-invalid': insertValidations.deviceOwnerName,},]" :placeholder="deviceOrg && deviceOrg.org_type == 1 ? 'Enter Parent Full Name' : 'Enter Owner Full Name'"/>
                            <small v-if="insertValidations.deviceOwnerName" ><ErrorMessage :msg="insertValidations.deviceOwnerName[0]"></ErrorMessage></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ deviceOrg && deviceOrg.org_type == 1 ? "Student's Name" : "Organization User Full Name" }} <span class="text-danger">*</span></label>
                            <input type="text" v-model="deviceOrgUserName" :class="['form-control', {'is-invalid': insertValidations.deviceOrgUserName,},]" :placeholder="deviceOrg && deviceOrg.org_type == 1 ? 'Enter Student Full Name' : 'Enter Organization User Full Name'" />
                            <small v-if="insertValidations.deviceOrgUserName" ><ErrorMessage :msg="insertValidations.deviceOrgUserName[0]"></ErrorMessage></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ deviceOrg && deviceOrg.org_type == 1 ? "Student's Grade" : "User Designation" }} <span class="text-danger">*</span></label>
                            <input type="text" v-model="deviceOrgUserDesig" :class="['form-control', {'is-invalid': insertValidations.deviceOrgUserDesig,},]" :placeholder="deviceOrg && deviceOrg.org_type == 1 ? 'Enter Student Grade' : 'Enter Organization User Designation'" />
                            <small v-if="insertValidations.deviceOrgUserDesig"><ErrorMessage :msg="insertValidations.deviceOrgUserDesig[0]"></ErrorMessage></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asset Tag</label>
                            <input type="text" v-model="deviceOrgAssetTag" :class="['form-control',{ 'is-invalid': insertValidations.deviceOrgAssetTag,},]" placeholder="Asset tag"/>
                            <small v-if="insertValidations.deviceOrgAssetTag" ><ErrorMessage :msg="insertValidations.deviceOrgAssetTag[0]"></ErrorMessage></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ deviceOrg && deviceOrg.org_type == 1 ? "Student ID" : "Organization User ID" }} <span class="text-danger">*</span></label>
                            <input type="text" v-model="deviceOrgUserId" :class="['form-control',{'is-invalid': insertValidations.deviceOrgUserId,},]" :placeholder="deviceOrg && deviceOrg.org_type == 1 ? 'Enter Student ID' : 'Enter Organization User ID'" />
                            <small v-if="insertValidations.deviceOrgUserId" ><ErrorMessage :msg="insertValidations.deviceOrgUserId[0]"></ErrorMessage></small>
                        </div>
                    </div>
                </div>

                <!-- Entering coverage informations -->
                <h5 class="def_18_size border-bottom pb-2 fw-semibold mb-1 mt-2 themetextcolor"> Coverage Information</h5>
                <div class=" mt-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <select v-model="deviceCoveragePlan" @change="onDevicePlanSelected" :class="['form-control', { 'is-invalid': insertValidations.deviceCoveragePlan }]">
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

                <!-- Buttons -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" @click="saveDevice" class="btn bg_blue text-white">
                        Submit
                    </button>
                    <a href="/smarttiusadmin/devices" class="btn customm_reset_btn">
                        Go Back
                    </a>
                </div>

            </form>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        deviceorgdata: Array,
    },
    data() {
        return {
            deviceOrg: "",
            deviceUser: "",
            searchUserText: "",
            searchModelText: "",
            deviceSerialNumber: "",
            deviceImei: "",
            orgID: "",
            deviceType: "1",
            deviceCarrier: "",
            deviceCapacity: "",
            deviceColor: "",
            deviceCellService: "",
            deviceOwnerName: "",
            deviceOwnerFirstName: "",
            deviceOwnerLastName: "",
            deviceOrgUserName: "",
            deviceModel: "",
            devicemodeldata: [],
            deviceuserdata: [],
            deviceOrgUserDesig: "",
            deviceOrgAssetTag: "",
            deviceOrgUserId: "",
            deviceCoverageExDate: "",
            deviceCoveragePlan: '0',
            deviceBillingCycle: "1",
            /*  deviceSubscriptionId: "", */
            deviceSubOrg: "",
            deviceSubOrgData: [],
            insertValidations: {},

            /** To contain the plan data of the selected model */
            ModelPlanData: [],
            deviceModelCoverageExDays: '',
        };
    },
    watch: {
        /* deviceOrg(newVal) {
            if (newVal && newVal.id) {
                this.getDeviceSubOrg(newVal.id);
                this.getDeviceModels(this.searchModelText, newVal.id);
                this.getUsers(this.searchUserText, newVal.id, this.deviceSubOrg ? this.deviceSubOrg.id : null);

            } else {
                this.deviceSubOrgData = [];
                this.deviceuserdata = [];
                this.devicemodeldata = [];
                this.deviceSubOrg = "";
                this.orgID = "";
            }
        }, */
        deviceType(newVal) {
            if (newVal == "1") {
                this.resetFields();
                this.deviceType = "1";
                this.getDeviceModels(this.searchModelText);
            }
            if (newVal == "2") {
                this.resetFields();
                this.deviceType = "2";
            }
        },
    },
    created() {
        this.getDeviceModels(this.searchModelText);
        this.getUsers(this.searchUserText);
    },
    methods: {

        /** On selecting the device model to get the plan data*/
        async handleSelectModel(){
            show_ajax_loader();

            this.deviceCoverageExDate = '';
            let modelId = this.deviceModel.id;
            this.deviceCoveragePlan = '0';
            this.ModelPlanData = [];

            if(modelId){
                const payload = {
                modelId: modelId,
                planType: 1,
                orgId: this.deviceOrg.id ?? '',
                };

                try{
                    let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/devices-plan/get-device-plan`, payload);
                    if(response.data.success == true){
                        if(response.data.deviceModelPlans){
                            this.ModelPlanData = response.data.deviceModelPlans;
                        }
                        hide_ajax_loader();
                    } else {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                        hide_ajax_loader();
                    }
                } catch (error) {
                    if (error && error.response && error.response.data && error.response.data.error) {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = error.response.data.error;
                        setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 5000);
                    } else {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                        hide_ajax_loader();
                    }
                }
            }
        },

        /** When the device is dis-selected*/
        removeModel(){
            this.ModelPlanData = [];
            this.deviceCoveragePlan = '0';
        },

        /** On selecting organization */
        onOrgSelected() {
            show_ajax_loader();
            this.deviceSubOrg = "";
            this.orgID = this.deviceOrg.id;
            if (this.orgID) {
                this.getDeviceSubOrg(this.orgID);
                this.getDeviceModels(this.searchModelText, this.orgID);
                this.getUsers(this.searchUserText, this.orgID, this.deviceSubOrg ? this.deviceSubOrg.id : null);
                this.deviceSubOrg = "";
                this.insertValidations.deviceOrg = '';
            } else {
                this.deviceSubOrgData = [];
                this.deviceuserdata = [];
                this.devicemodeldata = [];
                this.deviceSubOrg = "";
                this.orgID = "";
                this.searchUserText = "";

            }
            hide_ajax_loader();
        },
        /** When the organization is removed */
        onOrgRemoved() {
            this.deviceSubOrgData = [];
            // this.deviceuserdata = [];
            this.getUsers(this.searchUserText, null, null);
            this.devicemodeldata = [];
            this.deviceSubOrg = "";
            this.orgID = "";
            this.deviceCoverageExDate = "";
            this.deviceCoveragePlan = '0';
            this.ModelPlanData = [];
            this.deviceModel = "";
            this.searchUserText = "";
            this.deviceUser = "";
        },

        /** When the sub organization is removed */
        onSubOrgRemoved() {
            this.searchUserText = "";
            this.getUsers(this.searchUserText, this.orgID, null);
            this.deviceUser = "";

        },

       /*  onDevicePlanSelected() {
            let planValidity = this.ModelPlanData[0].expiration_days;
            if (planValidity && !isNaN(planValidity)) {
                const today = new Date();
                today.setDate(today.getDate() + planValidity);

                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');

                this.deviceCoverageExDate = `${year}-${month}-${day}`; // correct format for input[type="date"]
            } else {
                this.deviceCoverageExDate = '';
            }
        }, */


        onDevicePlanSelected() {
            const selectedPlan = this.ModelPlanData.find(
                plan => plan.id == this.deviceCoveragePlan
            );

            if (!selectedPlan) {
                this.deviceCoverageExDate = '';
                return;
            }

            // If expiration_date exists, use it
            if (selectedPlan.expiration_date) {
                this.deviceCoverageExDate = selectedPlan.expiration_date;
                return;
            }

            // Otherwise, calculate from expiration_days
            const planValidity = selectedPlan.expiration_days;

            if (planValidity && !isNaN(planValidity)) {
                const today = new Date();
                today.setDate(today.getDate() + Number(planValidity));

                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');

                this.deviceCoverageExDate = `${year}-${month}-${day}`;
            } else {
                this.deviceCoverageExDate = '';
            }
        },



        /** Reset Fields */
        async resetFields(){
            show_ajax_loader();
            this.deviceTitle = "";
            this.deviceOrg = "";
            this.deviceUser = "";
            this.searchUserText = "";
            this.searchModelText = "";
            this.deviceSerialNumber = "";
            this.deviceImei = "";
            this.orgID = "";
            this.ModelPlanData = [];
            this.deviceSubOrgData = [];
            this.devicemodeldata = [];

            this.deviceCarrier = "";
            this.deviceCapacity = "";
            this.deviceColor = "";
            this.deviceCellService = "";
            this.deviceOwnerName = "";
            this.deviceOwnerFirstName = "";
            this.deviceOwnerLastName = "";
            this.deviceOrgUserName = "";
            this.deviceOrgUserDesig = "";
            this.deviceOrgAssetTag = "";
            this.deviceOrgUserId = "";
            this.deviceCoverageExDate = "";
            this.deviceCoveragePlan = "0";
            this.deviceBillingCycle = "1";
            this.deviceSubOrg = "";
            this.deviceModel = "";
            hide_ajax_loader();
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
                    hide_ajax_loader();
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again';
                    hide_ajax_loader();
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
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    hide_ajax_loader();
                }
            }
        },

        /** get device models */
        async getDeviceModels(name=this.searchModelText, orgID = null) {
            show_ajax_loader();
             /* Enter at list 5 characters */
            if (name && name.length < 5) {
                this.devicemodeldata = [{
                    id: null,
                    title: "Please enter at least five characters",
                }];
                hide_ajax_loader();
                return;
            }
            try {
                let url = null;
                if (this.deviceType == 2) {
                    url = `${this.$userAppUrl}smarttiusadmin/device-model/fetch-models?orgId=${orgID}${orgID ? `&name=${name}` : ''}`;
                } else {
                    url = `${this.$userAppUrl}smarttiusadmin/device-model/fetch-models?name=${name}`;
                }
                let response = await axios.get(url);
                if (name !== this.searchModelText) {
                    this.devicemodeldata = [{
                        id: null,
                        title: "Please enter at least five characters",
                    }];
                    hide_ajax_loader();
                    return;
                }else if (response.data.success && response.data.deviceModels.length > 0) {
                    this.devicemodeldata = response.data.deviceModels;
                    hide_ajax_loader();
                } else {
                    /* This will show up in the dropdown regardless of success status */
                    this.devicemodeldata = [{
                        id: null,
                        title: response.data.msg ?? 'No device found',
                    }];
                    hide_ajax_loader();
                }
            } catch (error) {
                this.devicemodeldata = [{
                    id: null,
                    title: "Something went wrong, please try again later",
                }];
                hide_ajax_loader();
            }
        },

        /** device model name text */
        updateSearchModelName(value) {
            show_ajax_loader();
            if (value.length > 0) {
                this.searchModelText = value;
                this.getDeviceModels(value, this.orgID);
                hide_ajax_loader();
            }else {
                this.searchModelText = "";
                this.devicemodeldata = [{
                        id: null,
                        title: "Please enter at least five characters",
                }];
                hide_ajax_loader();
            }
        },

        /** user text */
        updateSearchText(value) {
            show_ajax_loader();
            if (value.length > 0) {
                this.searchUserText = value;
                /* this.getUsers(value, this.orgID, this.deviceSubOrg.id); */
                /* If subOrg is not selected, pass null */
                if (this.deviceSubOrg) {
                    this.getUsers(value, this.orgID, this.deviceSubOrg.id);

                } else if (this.orgID) {
                    this.getUsers(value, this.orgID, null);
                } else {
                    this.getUsers(value);
                }
                hide_ajax_loader();
            } else {
                this.searchUserText = "";
                this.deviceuserdata = [{
                        id: null,
                        full_name: "Please enter at least two characters",
                        first_name: "",
                        last_name: "",
                        email: ""
                }];
                hide_ajax_loader();
            }
        },

        /** user name with email */
        nameWithEmail(option) {
            const email = option.email ? ` (${option.email})` : '';
            return `${option.full_name}${email}`;
        },

        /** get users */
        async getUsers(name = this.searchUserText, orgID = null, subOrgID = null) {
            show_ajax_loader();

            if (this.deviceType == 2 && !orgID) {
                this.deviceuserdata = [{
                    id: null,
                    full_name: "Please select an organization",
                    first_name: "",
                    last_name: "",
                    email: ""
                }];
                hide_ajax_loader();
                return;

            }
            /* Enter atleast 2 characters */
            if (name.length < 2) {
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
                /* Clear previous search results */
                this.deviceuserdata = [];
                const url = `${this.$userAppUrl}smarttiusadmin/devices/get-users?name=${name}${orgID ? `&orgId=${orgID}` : ''}${subOrgID ? `&subOrgId=${subOrgID}` : ''}`;
                /* const url = `${this.$userAppUrl}smarttiusadmin/devices/get-users?name=${name}${orgID ? `&orgId=${orgID}` : ''}`; */
                if (name !== this.searchUserText) {
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
                this.deviceuserdata = [{
                    id: null,
                    full_name: "Please enter at least two characters",
                    first_name: "",
                    last_name: "",
                    email: ""
                }];
                hide_ajax_loader();
            }
        },

        /* Submit Device */
        async saveDevice(e) {
            show_ajax_loader();

            e.preventDefault();
            this.insertValidations = {};
            let validatePassed = true;

            /** Client side validations */
            if(!this.deviceCoveragePlan || this.deviceCoveragePlan == '0'){
                this.insertValidations.deviceCoveragePlan = ["Please Select the Plan."];
                validatePassed = false;
            }

            if(!this.deviceCoverageExDate){
                this.insertValidations.deviceCoverageExDate = ["The Expiration Date is required."];
                validatePassed = false;
            }

            if(!this.deviceModel.id){
                this.insertValidations.deviceModel = ["The Device Model is required."];
                validatePassed = false;
            }

            if(!this.deviceOwnerFirstName && this.deviceType == 1){
                this.insertValidations.deviceOwnerFirstName = ["The Owner's First Name is required."];
                validatePassed = false;
            }

            if(!this.deviceOwnerLastName && this.deviceType == 1){
                this.insertValidations.deviceOwnerLastName = ["The Owner's Last Name is required."];
                validatePassed = false;
            }
            if(!this.deviceOrg && this.deviceType == 2){
                this.insertValidations.deviceOrg = ["Please Select the Organization."];
                validatePassed = false;
            }

            if(this.deviceOrg && this.deviceOrg.org_type == 2){
                if(!this.deviceOwnerName){
                    this.insertValidations.deviceOwnerName = ["The Owner's Full Name is required."];
                    validatePassed = false;
                }

                if(!this.deviceOrgUserName){
                    this.insertValidations.deviceOrgUserName = ["The Organization User Full Name is required."];
                    validatePassed = false;
                }

                if(!this.deviceOrgUserDesig){
                    this.insertValidations.deviceOrgUserDesig = ["The Organization User Designation is required."];
                    validatePassed = false;
                }

                if(!this.deviceOrgUserId){
                    this.insertValidations.deviceOrgUserId = ["The Organization User Id is required."];
                    validatePassed = false;
                }
            }

            if(this.deviceOrg && this.deviceOrg.org_type == 1){
                if(!this.deviceOwnerName){
                    this.insertValidations.deviceOwnerName = ["The Parent's Full Name is required."];
                    validatePassed = false;
                }

                if(!this.deviceOrgUserName){
                    this.insertValidations.deviceOrgUserName = ["The Student's Full Name is required."];
                    validatePassed = false;
                }

                if(!this.deviceOrgUserDesig){
                    this.insertValidations.deviceOrgUserDesig = ["The Student's Grade is required."];
                    validatePassed = false;
                }

                if(!this.deviceOrgUserId){
                    this.insertValidations.deviceOrgUserId = ["The Student Id is required."];
                    validatePassed = false;
                }
            }

            if(!this.deviceSerialNumber){
                this.insertValidations.deviceSerialNumber = ["The Serial Number/Asset Tag is required."];
                validatePassed = false;
            }

            if(!this.deviceUser){
                this.insertValidations.deviceUser = ["The User is required."];
                validatePassed = false;
            }

            /** Return if any validation failed */
            if(!validatePassed){
                hide_ajax_loader();
                return;
            }

            try {
                /* Create a single object instead of an array */
                const deviceData = {
                    deviceOrg: this.deviceOrg.id,
                    deviceUser: this.deviceUser.id,
                    deviceSerialNumber: this.deviceSerialNumber,
                    deviceImei: this.deviceImei,
                    deviceType: this.deviceType,
                    deviceOwnerFirstName: this.deviceOwnerFirstName,
                    deviceOwnerLastName: this.deviceOwnerLastName,
                    deviceOwnerName: this.deviceOwnerName,
                    deviceOrgUserName: this.deviceOrgUserName,
                    deviceModelTitle: this.deviceModel.title,
                    deviceModel: this.deviceModel.id,
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
                    deviceSubOrg: this.deviceSubOrg.id,
                    /* deviceSubscriptionId: this.deviceSubscriptionId, */
                    deviceSubscriptionId: null,
                };

                let response = await axios.post(
                    `${this.$userAppUrl}smarttiusadmin/devices/store`,
                    deviceData
                );

                if(response.data.success == true) {
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin/devices`), 700);
                } else if (response.data.errors ) {
                    this.insertValidations = response.data.errors;
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
                        3000
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
