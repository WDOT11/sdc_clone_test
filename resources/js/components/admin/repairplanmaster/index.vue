<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0 ">
            <div class="card-body">
                <h4 class="mb-3 border-bottom pb-2 coman_main_heading">Device Repair Charges</h4>

                <!-- Button and search section -->
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 mt-2 mb-3">
                    <!-- Left Side Button -->
                    <button data-bs-toggle="modal" data-bs-target="#Managedeviceplans" class="btn blogal_pbtn_padding bg_blue text-white  d-flex align-items-center gap-10 def_14_size rounded">
                        <img :src="profileIc" width="22" height="22"> Manage Repair Charges
                    </button>

                    <!-- Right Side: Search Bar and Buttons -->
                    <div class="d-flex align-items-center flex-wrap search_options gap-3 ">
                        <!-- Search by name -->
                        <input type="text" v-model="search" class="form-control def_14_size w-auto" placeholder="Search by Device Or Plan Name">

                        <!-- Buttons -->
                        <button class="btn d-flex align-items-center gap-10 def_14_size bg_blue blogal_pbtn_padding  text-white" @click="getRepairPlanData"><img :src="searchIc" width="20" height="20"> Search </button>
                        <button class="btn customm_reset_btn def_14_size blogal_pbtn_padding" @click="cancelSearch"> Clear </button>
                    </div>
                </div>

                <!-- Manage Device Repair Plan -->
                <div class="modal" id="Managedeviceplans">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                    <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                                    <span>Manage Repair Charges</span>
                                </h4>
                                <button type="button" class="btn-close" id="managePlanModalClose" @click="closeAddPlanModel" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">
                                <div class="container-fluid mb-4">
                                    <label class="block mb-2 def_18_size  list-header font-semibold ">Device Model</label>
                                    <multiselect id="tagging" v-model="selectedDeviceModel" :searchable="true" @select="handleSelect" @keydown.enter.native="handleEnterKey" @remove="handelRemove" @search-change="updateSearchModelName" placeholder="Search or choose device model" label="title" track-by="id" :options="devicemodeldata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['w-full',{'border-red-500': insertValidations.selectedDeviceModel,}, ]" :allow-empty="true" :preserve-search="false" :internal-search="true" required>
                                        <template #noOptions>
                                            <span class="custom-message">No device model found.</span>
                                        </template>
                                        <template #noResult>
                                            <span class="custom-message">No device model found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="insertValidations.selectedDeviceModel" >
                                        <ErrorMessage :msg="insertValidations.selectedDeviceModel[0]"></ErrorMessage>
                                    </small>
                                </div>

                                <!-- Repair Plans -->
                                <div class="mb-6">

                                        <div class="container-fluid">
                                             <h5 class="def_18_size list-header fw-semibold mb-0 mt-2"> Repair Charges </h5>
                                            <div v-for="(repair_plan, index) in repair_plans" :key="index" class="row   mb-4   rounded onewhitebg">
                                                <div class="col-12 col-md-6 col-lg-6">
                                                    <div class="form-group mt-3 w-100">
                                                        <label for="Plan" class="form-label">Plan Name <span class="text-danger">*</span></label>
                                                        <input v-model="repair_plan.PlanName" type="text" placeholder="Enter Plan Name" class="form-input form-control w-full rounded-md border-gray-300" />
                                                        <small v-if="insertValidations[`repair_plans.${index}.PlanName`]" >
                                                            <ErrorMessage :msg="insertValidations[`repair_plans.${index}.PlanName`][0]"></ErrorMessage>

                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-6">
                                                    <div class="form-group  mt-3 w-100">
                                                        <label for="Price" class="form-label">Price <span class="text-danger">*</span></label>
                                                        <input v-model="repair_plan.PlanPrice" type="number" step="any" min="0" placeholder="Enter Price" class="form-input form-control w-full rounded-md border-gray-300" />
                                                        <span v-if="insertValidations[`repair_plans.${index}.PlanPrice`]" class="error_msg_text">
                                                            <ErrorMessage :msg="insertValidations[`repair_plans.${index}.PlanPrice`][0]"></ErrorMessage>

                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg">
                                <button type="submit" @click="saveDevicePlan" :disabled="disabledButton" class="btn bg_blue text-white">
                                    Save
                                </button>
                                <!-- <button data-bs-dismiss="modal" @click="closeAddPlanModel" type="button" class="btn btnblack">
                                    Cancel
                                </button> -->
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Manage Device Repair Plan End -->


                <!-- View Device Plan Modal -->
                <div class="modal" id="DevicePlanDetails">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                    <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                                    <span> Repair Plan Details </span>
                                </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">
                            <div v-if="showViewModal">
                                <div class="table-responsive">
                                <table class="table table-bordered table-hover ">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold">Plan Name:</td>
                                            <td class="text-muted fw-normal">{{ this.viewDevicePlanData.plan_name }}</td>

                                            <td class="fw-bold">Device Family:</td>
                                            <td class="text-muted fw-normal">{{ this.viewDevicePlanData.device_family_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Device Brand:</td>
                                            <td class="text-muted fw-normal">{{ this.viewDevicePlanData.device_brand_name }}</td>

                                            <td class="fw-bold">Device Model:</td>
                                            <td class="text-muted fw-normal">{{ this.viewDevicePlanData.device_model_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Price:</td>
                                            <td class="text-muted fw-normal" colspan="3">${{ this.viewDevicePlanData.price }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg">
                                <button  data-bs-dismiss="modal" @click="showViewModal = false" class="btn customm_reset_btn "> Close </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- View Device Plan Modal End -->

                <!-- Table to show the list of repair plans -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover def_14_size table_custom">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Device</th>
                                <th>Plan Name</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="devicePlanData.length > 0" v-for="(data, index) in devicePlanData" :key="data.id">
                                <td>
                                    {{ (paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1 }} </td>
                                <td>
                                    {{ data.device_model_name && data.device_family_name && data.device_brand_name ? data.device_family_name + ">>" + data.device_brand_name + ">>" + data.device_model_name : "-" }}
                                </td>
                                <td>
                                    {{ data.plan_name }}
                                </td>
                                <td>
                                    ${{ data.price ?? "-" }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-20">
                                    <button  data-bs-toggle="modal" data-bs-target="#DevicePlanDetails" @click="viewDevicPlan(data.id)" class="themetextcolor globalviewbtn extrabtns rounded-pill d-flex align-items-center def_14_size gap-1 "><img :src="viewIc" width="35" height="35"><span class="viewtext_Gl">View</span></button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="7">
                                    No records found!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getRepairPlanData"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        deviceplandata: {
            type: Object,
        },
        pagination: {
            type: Object,
        },
    },
    data() {
        return {
            /** Icons */
            deleteIc: this.$deleteIcon,
            profileIc: this.$profileIcIcon,
            searchIc: this.$searchIcIcon,
            viewIc: this.$viewIcIcon,

            /** To manage the search */
            search: "",
            searchModelText: "",

            /** To store the device model data by searching */
            devicemodeldata: [],

            /** To manage the data of the selected device */
            selectedDeviceModel: "",

            /** To manage the repair plan data of the selected device */
            repair_plans: [
                {
                    planId: null,
                    PlanName: "",
                    PlanPrice: "",
                },
            ],

            /** To manage the manage and view models */
            showViewModal: false,

            /* Plans List */
            devicePlanData: this.deviceplandata.data,
            paginationData: this.pagination,

            /** To manage the the data of plan details to show in detail popup */
            viewDevicePlanData: {},

            insertValidations: {}, /** To store the add model validations */

            disabledButton: false, /** Save Button */
        };
    },
    created() {
        this.getDeviceModels();
    },
    methods: {

        /* To get the device plan data by using paging */
        async getRepairPlanData(page = 1, search = this.search) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/repair-plan?page=${page}${search !== "" || search !== null ? `&search=${this.search}` : "" }`
                );

                if (response && response.data) {
                    if (response.data.success == true) {
                        if (response.data.planData && response.data.pagination) {
                            this.devicePlanData = response.data.planData.data;
                            this.paginationData = response.data.pagination;
                            hide_ajax_loader();
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = "Something went wrong, please try again.";
                        }
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, please try again.";
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () =>
                            (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        3000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /* To clear the search */
        async cancelSearch() {
            show_ajax_loader();
            this.search = "";
            await this.getRepairPlanData(1);
            hide_ajax_loader();
        },

        /* on click view */
        async viewDevicPlan(id) {
            show_ajax_loader();
            this.showViewModal = true;
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/repair-plan/view/${id}`);
                if (response.data.success == true) {
                    this.viewDevicePlanData = response.data.viewData;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                if ( error && error.response && error.response.data && error.response.data.error ) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() =>(location.href = `${this.$userAppUrl}smarttiusadmin`),3000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** Handle selection from dropdown */
        handleSelect(model) {
            show_ajax_loader();
            this.selectedDeviceId = model.id;
            if(model.id){
                this.selectDeviceModel(model.id);
            }
            hide_ajax_loader();
        },

        /** Handle Enter key press */
        handleEnterKey() {
            show_ajax_loader();
            this.selectedDeviceId = this.selectedDeviceModel.id;

            if(this.selectedDeviceModel.id){
                this.selectDeviceModel(this.selectedDeviceModel.id);
            }
            hide_ajax_loader();
        },

        /** When the device is dis-selected */
        handelRemove(){
            show_ajax_loader();
            /** removing the repair plans data */
            this.repair_plans = [
                {
                    planId: null,
                    PlanName: "",
                    PlanPrice: "",
                },
            ];
            hide_ajax_loader();
        },

        updateSearchModelName(value) {
            if (value) {
                this.searchModelText = value;
                this.getDeviceModels(value);
            } else {
                this.searchModelText = '';
                this.getDeviceModels();
            }
        },

        /* get device models */
        async getDeviceModels(name = this.searchModelText) {
            show_ajax_loader();
            try {
                let response = null;
                // const url = `${this.$userAppUrl}smarttiusadmin/device-model/fetch-models${name ? `?name=${name}` : ""}${modelID ? `?deviceModelId=${modelID}` : ""}`;
                // response = await axios.get(url);
                response = await axios.get(`${this.$userAppUrl}smarttiusadmin/repair-request/fetch-devices${name ? `?name=${name}` : ''}`);
                if (response.data.success && response.data.deviceModels.length > 0) {
                    /* if (modelID) {
                        this.devicemodeldata = response.data.deviceModels;
                        const matched = this.devicemodeldata.find(
                            model => model.id == modelID
                        );
                        if (matched) {
                            this.selectedDeviceModel = matched;
                        }
                    } */
                    this.devicemodeldata = response.data.deviceModels;
                    hide_ajax_loader();
                } else {
                    /* This will show up in the dropdown regardless of success status */
                    this.devicemodeldata = [
                        {
                            id: null,
                            title: "No device model found",
                        },
                    ];
                    hide_ajax_loader();
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
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        /** Common function for both selection and Enter key */
        async selectDeviceModel(model) {
            show_ajax_loader();
            this.insertValidations = {};

            const payload = {
                modelId: model,
            };

            try{
                let response = await axios.post(
                    `${this.$userAppUrl}smarttiusadmin/repair-plan/manage-repair-plan`,
                    payload
                );
                if (response.data.success == true) {
                    if(response.data.deviceModelPlans.length > 0){
                        this.repair_plans = response.data.deviceModelPlans.map((plan) => {
                            return {
                                planId: plan.id,
                                PlanName: plan.plan_name,
                                PlanPrice: plan.price,
                            };
                        });
                        hide_ajax_loader();
                    } else {
                        this.repair_plans = [
                                {
                                    planId: null,
                                    PlanName: "",
                                    PlanPrice: "",
                                },
                            ];
                        hide_ajax_loader();
                    }
                } else if(response.data.errors) {
                    this.insertValidations = response.data.errors;
                    hide_ajax_loader();
                }  else {
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

        /** To close the add plan model */
        async closeAddPlanModel(){
            show_ajax_loader();
            /* hide the model and remove the data from the fields */
            hide_admin_popup('#managePlanModalClose');
            this.selectedDeviceModel = "";
            this.searchModelText = "";
            this.repair_plans = [
                {
                    deviceModel: "",
                    PlanName: "",
                    PlanPrice: "",
                },
            ];
            this.insertValidations = {};
            this.isMultiselectDisabled = false;
            this.disabledButton = false;
            this.queryModel = null;
            hide_ajax_loader();
        },

        /* Add device Plan(save) */
        async saveDevicePlan(e) {
            e.preventDefault();
            show_ajax_loader();
            let validationPassed = true;
            this.insertValidations = {};

            /* Client side validations */
            /** validate device model selection */
            if (!this.selectedDeviceModel) {
                this.insertValidations.selectedDeviceModel = [`Please select device model.`];
                validationPassed = false;
            }

            /** Validate the coverage plan fields If the coverage plan is selected*/
            this.repair_plans.forEach((plan, index) => {
                if (!plan.PlanName) {
                    this.insertValidations[`repair_plans.${index}.PlanName`] = [`Please enter plan name.`];
                    validationPassed = false;
                }
                if (!plan.PlanPrice) {
                    this.insertValidations[`repair_plans.${index}.PlanPrice`] = [`Please enter plan price.`];
                    validationPassed = false;
                }
                if(isNaN(plan.PlanPrice)){
                    this.insertValidations[`repair_plans.${index}.PlanPrice`] = [`Please enter valid plan price.`];
                    validationPassed = false;
                }
            });

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            const payload = {
                selectedDeviceModel: this.selectedDeviceModel?.id,
                selectedDeviceModelName: this.selectedDeviceModel?.title,
                repair_plans:
                    this.repair_plans ? this.repair_plans : [],
            };

            try {
                this.disabledButton = true;
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/repair-plan/store`, payload);

                if (response.data.success) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    this.closeAddPlanModel();
                    /* get device plan data */
                    this.getRepairPlanData();
                } else if(response.data.errors) {
                    this.disabledButton = false;
                    this.insertValidations = response.data.errors;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
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
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },
    }
}
</script>