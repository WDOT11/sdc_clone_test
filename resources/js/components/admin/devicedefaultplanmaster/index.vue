<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0 ">
            <div class="card-body">
                <h4 class=" mb-3 border-bottom pb-2 coman_main_heading">Default Device Plans</h4>

                <!-- Search filter -->
                <div class="d-flex justify-content-between flex-wrap align-items-start gap-3 mb-2 ">
                        <button data-bs-toggle="modal" data-bs-target="#AddNewPlan" class="blogal_pbtn_padding btn def_14_size bg_blue d-flex align-items-start gap-10 text-white wmax  ">
                            <img :src="profileIc" width="22" height="22">
                        Add New
                    </button>
                        <!-- Search by plan name -->
                        <div class="d-flex def_14_size flex-wrap search_options align-items-start gap-3">
                        <input type="text" v-model="search_name" class="form-control w-auto def_14_size" placeholder="Filter by Plan Name">

                        <!-- buttons -->
                        <div class="d-flex gap-2">
                            <button class="btn bg_blue blogal_pbtn_padding d-flex def_14_size align-items-center gap-10  text-white" @click="getDeviceDefaultPlans">
                                <img :src="searchIc" width="20" height="20">
                                Search</button>
                            <button class="btn customm_reset_btn def_14_size blogal_pbtn_padding" @click="cancelSearch">Clear</button>
                        </div>
                    </div>
                </div>

                <!-- Add plan Modal -->
                <div class="modal" id="AddNewPlan">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                        <!-- Modal Header -->
                        <div class="modal-header align-items-center">
                            <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                                <span>Add New Plan</span>
                            </h4>
                            <button type="button" class="btn-close" id="addDefaultPlanModalClose" @click="closeAddModal" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body onewhitebg">
                        <!-- Select Plan Type -->
                        <div class="mb-6">
                            <label class="form-label mt-1" >Select Plan</label>

                            <div class="radio-inputs rounded-pill">
                                <label class="radio">
                                    <input id="cp"  type="radio" v-model="devicePlanType" value="1" class="mobileToggle"/>
                                    <span for="cp" class="name">Coverage Plan</span>
                                </label>
                                <label class="radio">
                                    <input id="rp" type="radio" v-model="devicePlanType" value="2" class="mobileToggle"/>
                                    <span for="rp" class="name">Renewal Plan</span>
                                </label>
                            </div>
                            <span v-if="validationErrors.devicePlanType" ><ErrorMessage :msg="validationErrors.devicePlanType"></ErrorMessage></span>
                        </div>
                        <div>
                            <div>
                                <label class="form-label mt-1" >Plan Name</label>
                                <input type="text" v-model="planName" class="form-control" placeholder="Enter Plan Name">
                                <span v-if="validationErrors.planName" ><ErrorMessage :msg="validationErrors.planName"></ErrorMessage></span>
                            </div>
                            <div>
                                <label class="form-label mt-3" >Plan Price</label>
                                <input type="number" step="any" min="0" v-model="planPrice" class="form-control" placeholder="Enter Plan Price">
                                <span v-if="validationErrors.planPrice" ><ErrorMessage :msg="validationErrors.planPrice"></ErrorMessage></span>
                            </div>
                            <div>
                                <label  class="form-label mt-3" >Deductible</label>
                                <input type="number" step="any" min="0" v-model="planDeductible" class="form-control" placeholder="Enter Deductible">
                                <span v-if="validationErrors.planDeductible" ><ErrorMessage :msg="validationErrors.planDeductible"></ErrorMessage></span>
                            </div>
                            <div>
                                <label  class="form-label mt-3" >Occurence</label>
                                <select v-model="planOccurence" class="form-control form-control w-full rounded-md border-gray-300">
                                    <option value="">SELECT</option>
                                    <option v-for="(billingCycle, index) in $billingCycle" :value="index + 1"> {{ billingCycle }} </option>
                                </select>
                                <span v-if="validationErrors.planOccurence" ><ErrorMessage :msg="validationErrors.planOccurence"></ErrorMessage></span>
                            </div>
                            <div v-if="planOccurence == 1">
                                <label  class="form-label mt-3" >Expiration Days</label>
                                <input type="number" min="0" v-model="planExpirationDays" class="form-control" placeholder="Enter Expiration Days">
                                <span v-if="validationErrors.planExpirationDays" ><ErrorMessage :msg="validationErrors.planExpirationDays"></ErrorMessage></span>
                            </div>
                        </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer onewhitebg">
                            <!-- <button data-bs-dismiss="modal" @click="closeAddModal" class="btn btnblack text-white rounded mr-2">
                                Cancel
                            </button> -->
                            <button @click="savePlans" class="btn bg_blue text-white rounded">
                                Save
                            </button>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Add Plan Modal End -->

                <!-- Edit plan Modal -->
                <div class="modal" id="EditPlan">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content onewhitebg">

                        <!-- Modal Header -->
                        <div class="modal-header align-items-center">
                            <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                                <span>Edit Plan</span>
                            </h4>
                            <button type="button" class="btn-close" id="editDevicePlanModalClose" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body onewhitebg">
                        <!-- Select Plan Type -->
                        <div class="mb-6">
                            <label class="form-label mt-1" >Select Plan</label>

                             <div class="radio-inputs rounded-pill">
                                <label class="radio">
                                    <input id="editr" type="radio" v-model="editDevicePlanType" value="1" class="mobileToggle"/>
                                    <span for="editr" class="name">Coverage Plan</span>
                                </label>
                                <label class="radio">
                                    <input id="editr1" type="radio" v-model="editDevicePlanType" value="2" class="mobileToggle"/>
                                    <span for="editr1" class="name">Renewal Plan</span>
                                </label>
                            </div>
                            <span v-if="validationErrors.editDevicePlanType" ><ErrorMessage :msg="validationErrors.editDevicePlanType"></ErrorMessage></span>
                        </div>
                        <div>
                            <div>
                                <label class="form-label mt-1" >Plan Name</label>
                                <input type="text" v-model="editPlanName" class="form-control" placeholder="Enter Plan Name">
                                <span v-if="validationErrors.editPlanName" ><ErrorMessage :msg="validationErrors.editPlanName"></ErrorMessage></span>
                            </div>
                            <div>
                                <label class="form-label mt-3" >Plan Price</label>
                                <input type="number" step="any" min="0" v-model="editPlanPrice" class="form-control" placeholder="Enter Plan Price">
                                <span v-if="validationErrors.editPlanPrice" ><ErrorMessage :msg="validationErrors.editPlanPrice"></ErrorMessage></span>
                            </div>
                            <div>
                                <label class="form-label mt-3" >Deductible</label>
                                <input type="number" step="any" min="0" v-model="editPlanDeductible" class="form-control" placeholder="Enter Deductible">
                                <span v-if="validationErrors.editPlanDeductible" ><ErrorMessage :msg="validationErrors.editPlanDeductible"></ErrorMessage></span>
                            </div>
                            <div>
                                <label class="form-label mt-3" >Occurence</label>
                                <select v-model="editPlanOccurence" class="form-control form-control w-full rounded-md border-gray-300">
                                    <option v-for="(billingCycle, index) in $billingCycle" :value="index + 1"> {{ billingCycle }} </option>
                                </select>
                                <span v-if="validationErrors.editPlanOccurence" ><ErrorMessage :msg="validationErrors.editplanOccurence"></ErrorMessage></span>
                            </div>
                            <div v-if="editPlanOccurence == 1">
                                <label class="form-label mt-3" >Expiration Days</label>
                                <input type="number" min="0" v-model="editPlanExpirationDays" class="form-control" placeholder="Enter Expiration Days">
                                <span v-if="validationErrors.editPlanExpirationDays" ><ErrorMessage :msg="validationErrors.editPlanExpirationDays"></ErrorMessage></span>
                            </div>
                        </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer onewhitebg">
                            <!-- <button data-bs-dismiss="modal" @click="closeEditModal" class="btn btnblack text-white  rounded mr-2">
                                Cancel
                            </button> -->
                            <button @click="updatePlan" class="btn bg_blue text-white rounded">
                                Update
                            </button>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Edit Plan Modal End -->

                <!-- Table to contain the plan data -->
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover def_14_size table_custom2">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th class="text-nowrap">Plan Name</th>
                                <th class="text-nowrap">plan type</th>
                                <th  class="text-nowrap">Plan Price</th>
                                <th>Deductibe</th>
                                <th>Occurence</th>
                                <th  class="text-nowrap">Expiration days</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="plansdata.length > 0" v-for="(plandata, index) in plansdata" :key="plandata.id">
                                <td>{{ (paginationdata.current_page - 1) * paginationdata.per_page + index + 1 }}</td>
                                <td>{{ plandata.plan_name }}</td>
                                <td>{{ plandata.plan_type && plandata.plan_type == 1 ? "Coverage" : plandata.plan_type && plandata.plan_type == 2 ? "Renewal" : "-" }}</td>
                                <td>${{ plandata.price }}</td>
                                <td>${{ plandata.deductible_price ?? "0" }}</td>
                                <td>{{ plandata.freq_occurence ? $billingCycle.find((billingCycle, index) => index + 1 == plandata.freq_occurence) : "-" }}</td>
                                <td>{{ plandata.expiration_days }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-20">
                                        <button data-bs-toggle="modal" data-bs-target="#EditPlan" @click="editPlan(plandata.id)" class="themetextcolor globaleditbtn extrabtns rounded-pill d-flex align-items-center def_14_size gap-1  rounded editBtn"> <img :src="editIc" width="35" height="35"> <span class="edittext_Gl">Edit</span></button>
                                        <!-- <button @click="confirmDelete(plandata.id)" class=" bg-transparent  text-white  rounded"> <img :src="deleteIc" width="28" height="28"> </button> -->
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="8">
                                    No plans found!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationdata && paginationdata.last_page > 1" :pagination="paginationdata" :offset="5" :paginate="getDeviceDefaultPlans"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        plandata: {
            type : Object
        },
        pagination: {
            type : Object
        }
    },

    data() {
        return {
            // delet data icon for table
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,

            /** To contain the plans, and the pagination data */
            plansdata: this.plandata.data,
            paginationdata: this.pagination,

            /** To contain the search value */
            search_name: '',

            /** To contain the values while creating a new plan */
            planName: '',
            planPrice: '',
            planDeductible: '',
            planOccurence: '',
            planExpirationDays: '',
            devicePlanType: 1,

            /** To contain the values while updating the plans */
            editPlanId: '',
            editDevicePlanType: '',
            editPlanName: '',
            editPlanPrice: '',
            editPlanDeductible: '',
            editPlanOccurence: '',
            editPlanExpirationDays: '',

            /** To contain the validation errors */
            validationErrors: {},
        }
    },

    methods: {

        /** To Get the plans and pagination data */
        async getDeviceDefaultPlans(page = 1, search = this.search_name) {
            show_ajax_loader();
            try{
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/device-default-plan?page=${page}${search !== "" || search !== null ? `&search=${this.search_name}` : "" }`
                );

                if (response && response.data) {
                    if (response.data.success == true) {
                        if ( response.data.planData && response.data.planData.data && response.data.pagination ) {
                            this.plansdata = response.data.planData.data;
                            this.paginationdata = response.data.pagination;
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

        /** To save the plan data */
        async savePlans(){
            show_ajax_loader();
            let validationPassed = true;
            this.validationErrors = {};

            /** Client side validations */
            if(!this.devicePlanType){
                this.validationErrors.planType = 'Please select the plan type.';
                validationPassed = false;
            }

            if(!this.planName){
                this.validationErrors.planName = 'Please enter the plan name.';
                validationPassed = false;
            }

            if(!this.planPrice){
                this.validationErrors.planPrice = 'Please enter the plan price.';
                validationPassed = false;
            }

            if(!this.planOccurence){
                this.validationErrors.planOccurence = 'Please select the occurence of the plan.';
                validationPassed = false;
            }

            if(!this.planExpirationDays && this.planOccurence == 1){
                this.validationErrors.planExpirationDays = 'Please enter the expiration days.';
                validationPassed = false;
            }

            if(!validationPassed){
                hide_ajax_loader();
                return;
            }

            try{
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/device-default-plan/store`, {
                    plan_name: this.planName,
                    device_plan_type: this.devicePlanType,
                    plan_price: this.planPrice,
                    plan_deductibe: this.planDeductible,
                    plan_occurence: this.planOccurence,
                    plan_expiration_days: this.planExpirationDays
                });

                if(response.data.success == true){
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /** To close the popup and make the fields empty */
                    this.closeAddModal();
                    this.getDeviceDefaultPlans();
                } else if(response.data.errors){
                    if(response.data.errors.device_plan_type){
                        this.validationErrors.planType = response.data.errors.device_plan_type[0];
                    }
                    if(response.data.errors.plan_name){
                        this.validationErrors.planName = response.data.errors.plan_name[0];
                    }
                    if(response.data.errors.plan_price){
                        this.validationErrors.planPrice = response.data.errors.plan_price[0];
                    }
                    if(response.data.errors.plan_deductibe){
                        this.validationErrors.planDeductible = response.data.errors.plan_deductibe[0];
                    }
                    if(response.data.errors.plan_occurence){
                        this.validationErrors.planOccurence = response.data.errors.plan_occurence[0];
                    }
                    if(response.data.errors.plan_expiration_days){
                        this.validationErrors.planExpirationDays = response.data.errors.plan_expiration_days[0];
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
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** To manage the update fuinctionality */
        async editPlan(plan_id){
            show_ajax_loader();
            this.validationErrors = {};
            this.editPlanId = plan_id;
            try{
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/device-default-plan/edit/${plan_id}`);
                if(response.data.success == true){
                    this.editDevicePlanType = response.data.editData.plan_type;
                    this.editPlanName = response.data.editData.plan_name;
                    this.editPlanPrice = response.data.editData.price;
                    this.editPlanDeductible = response.data.editData.deductible_price;
                    this.editPlanOccurence = response.data.editData.freq_occurence;
                    this.editPlanExpirationDays = response.data.editData.expiration_days;
                    hide_ajax_loader();
                } else {
                    hide_admin_popup('#editDevicePlanModalClose');
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error){
                if (error && error.response && error.response.data && error.response.data.error) {
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

        /** To update the plan data */
        async updatePlan(){
            show_ajax_loader();
            let validationPassed = true;
            this.validationErrors = {};

            if(!this.editDevicePlanType){
                this.validationErrors.editDevicePlanType = 'Please select the plan type.';
                validationPassed = false;
            }

            if(!this.editPlanName){
                this.validationErrors.editPlanName = 'Please enter the plan name.';
                validationPassed = false;
            }

            if(!this.editPlanPrice){
                this.validationErrors.editPlanPrice = 'Please enter the plan price.';
                validationPassed = false;
            }

            if(!this.editPlanOccurence){
                this.validationErrors.editPlanOccurence = 'Please select the occurence of the plan.';
                validationPassed = false;
            }

            if(!this.editPlanExpirationDays && this.editPlanOccurence == 1){
                this.validationErrors.editPlanExpirationDays = 'Please enter the expiration days.';
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            let plan_id = this.editPlanId ?? '';
            try {
                /* Clear previous validation errors */
                this.validationErrors = {};
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/device-default-plan/update/${plan_id}`, {
                    device_plan_type: this.editDevicePlanType,
                    plan_name: this.editPlanName,
                    plan_price: this.editPlanPrice,
                    plan_deductible: this.editPlanDeductible,
                    plan_occurence: this.editPlanOccurence,
                    plan_expiration_days: this.editPlanExpirationDays,
                });

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Hide the modal */
                    hide_admin_popup('#editDevicePlanModalClose');

                    /* Fetch/update data */
                    this.getDeviceDefaultPlans();
                } else if (response.data.errors){
                    if(response.data.errors.device_plan_type){
                        this.validationErrors.editDevicePlanType = response.data.errors.device_plan_type[0];
                    }
                    if(response.data.errors.plan_name){
                        this.validationErrors.editPlanName = response.data.errors.plan_name[0];
                    }
                    if(response.data.errors.plan_price){
                        this.validationErrors.editPlanPrice = response.data.errors.plan_price[0];
                    }
                    if(response.data.errors.plan_deductibe){
                        this.validationErrors.editPlanDeductible = response.data.errors.plan_deductibe[0];
                    }
                    if(response.data.errors.plan_occurence){
                        this.validationErrors.editPlanOccurence = response.data.errors.plan_occurence[0];
                    }
                    if(response.data.errors.plan_expiration_days){
                        this.validationErrors.editPlanExpirationDays = response.data.errors.plan_expiration_days[0];
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
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** To manage the cancelation of the search */
        async cancelSearch() {
            show_ajax_loader();
            this.search_name = '';
            this.getDeviceDefaultPlans();
            hide_ajax_loader();
        },

        /** To close the add plan popup model */
        async closeAddModal() {
            hide_admin_popup('#addDefaultPlanModalClose');
            this.devicePlanType = 1;
            this.planName = '';
            this.planPrice = '';
            this.planDeductible = '';
            this.planOccurence = '';
            this.planExpirationDays = '';
            this.validationErrors = {};
        },

        /** To close the edit plan model */
        // async closeEditModal(){
        //     show_ajax_loader();
        //     hide_admin_popup('#editDevicePlanModalClose');
        //     this.validationErrors = {};
        //     hide_ajax_loader();
        // },

        /** To manage the delete functionality */
        async confirmDelete(){
            this.$alertMessage.success = false;
            this.$alertMessage.message = 'This is under development.';
        },
    }
}
</script>