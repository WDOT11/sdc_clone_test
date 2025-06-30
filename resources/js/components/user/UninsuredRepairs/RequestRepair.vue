<template>
    <div class="container-fluid mt_12">
        <div class="card border-0 onewhitebg">
            <div class="card-body  ">
                <h4 class="coman_main_heading border-bottom pb-2 mb-3">Repair Device</h4>
                <div class="steps_wrap">
                    <div class="formobile onewhitebg">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div  id="steps-container">
                                <li class="step themetextcolor" v-if="currentStep == 1"><span class="mr-1">Step 1:</span> User Details</li>
                                <li class="step themetextcolor" v-if="currentStep == 2"><span class="mr-1">Step 2:</span> Device Details</li>
                                <li class="step themetextcolor" v-if="currentStep == 3"><span class="mr-1">Step 3:</span>Selected Devices</li>
                            </div>
                            <span class="steptext">Steps: {{ currentStep }}/{{ 3 }}</span>
                        </div>
                        <div id="step-indicator">
                            <div class="progress" :style="`width: ${(currentStep / 3) * 100}%`"></div>
                        </div>
                    </div>
                </div>
                <!-- Step Indicators -->

                <div class=" ">
                    <!-- Step 1: User Details -->
                    <div v-if="currentStep == 1" class="step">
                        <div class="mb-3 onewhitebg p-3 rounded border device-info">
                            <label class="form-label def_18_size">User Details</label>
                            <div class="row ">
                                <!-- First Name -->
                                <div class="col-12 col-md-6 col-lg-6 mt-3">
                                    <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter First Name" v-model="firstName" :class="['form-control', { 'is-invalid': validationErrors.firstName },]">
                                    <small v-if="validationErrors.firstName" ><ErrorMessage :msg="validationErrors.firstName[0]"></ErrorMessage></small>
                                </div>
                                <!-- Last Name -->
                                <div class="col-12 col-md-6 col-lg-6 mt-3">
                                    <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter Last Name" v-model="lastName" :class="['form-control', { 'is-invalid': validationErrors.lastName },]">
                                    <small v-if="validationErrors.lastName" ><ErrorMessage :msg="validationErrors.lastName[0]"></ErrorMessage></small>
                                </div>
                            </div>
                            <div class="row ">
                                <!-- Phone Number -->
                                <div class="col-12 col-md-6 col-lg-6 mt-3">
                                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" placeholder="Enter phone number" v-model="phone" @input="formatPhoneNumber" @paste.prevent="handlePaste" :class="['form-control', { 'is-invalid': validationErrors.phone },]">
                                    <small v-if="validationErrors.phone" ><ErrorMessage :msg="validationErrors.phone[0]"></ErrorMessage></small>
                                </div>
                                <!-- Email -->
                                <div class="col-12 col-md-6 col-lg-6 mt-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" placeholder="Enter email" v-model="email" :class="['form-control', { 'is-invalid': validationErrors.email },]">
                                    <small v-if="validationErrors.email" ><ErrorMessage :msg="validationErrors.email[0]"></ErrorMessage></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Device Details -->
                    <div v-if="currentStep == 2" class="step">
                        <div class="row">
                            <div class="col-12 col-lg-12 mb-3">
                                <label for="numberOfDevices" class="form-label">Number of Devices</label>
                                <input type="number" min="1" max="50" placeholder="Enter the number of devices"
                                    v-model="numberOfDevices"
                                    :class="['form-control', { 'is-invalid': validationErrors.numberOfDevices }]"
                                    @input="onDeviceInput">
                                <small v-if="numberOfDevices > 50 && !validationErrors.numberOfDevices">
                                    <ErrorMessage :msg="`You can only add up to 50 devices.`"></ErrorMessage>
                                </small>
                                <small v-if="validationErrors.numberOfDevices">
                                    <ErrorMessage :msg="validationErrors.numberOfDevices[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        <div v-for="(device, index) in devices" :key="index" class="border device-info onewhitebg mb-3  p-3 rounded">
                            <div class="row mb-3">
                                <div class="col-md-11">
                                    <h4 class="fw-bold def_18_size">Device {{ index + 1 }}</h4>
                                </div>
                                <div class="col-md-1 ">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn bg-transparent border del_btns btn-sm" @click="removeDeviceList(index)" v-if="devices.length > 1"> <img :src="deleteIc" width="28" height="28"> </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Select Device Model -->
                                <div class="col-md-12 mb-3">
                                    <label for="deviceModel" class="form-label">Model <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="device.model" :searchable="true" @select="selectDeviceModel(device.model.id)" @search-change="updateSearchDeviceModelName" placeholder="Search or choose device model" :custom-label="nameWithfamily" label="title" track-by="id" :options="devicemodeldata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['w-full', { 'border-red-500': validationErrors[`devices.${index}.modelId`] }]" required :allow-empty="true" :internal-search="true" :preserve-search="true">
                                        <template #noOptions>
                                            <span class="custom-message">Please enter at least five characters</span>
                                        </template>
                                        <template #noResult>
                                            <span class="custom-message">{{ isSearchModelTooShort ? "Please enter at least five characters" : "No Device Model Found." }}</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors[`devices.${index}.modelId`]" >
                                        <ErrorMessage :msg="validationErrors[`devices.${index}.modelId`][0]"></ErrorMessage>
                                    </small>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Device Serial number/Asset tag -->
                                <div class="col-12 col-md-6 col-lg-6 mt-3">
                                    <label class="form-label">Serial number/Asset tag <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Serial number/Asset tag" v-model="device.serialNumber" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.serialNumber`] }]">
                                    <small v-if="validationErrors[`devices.${index}.serialNumber`]" >
                                        <ErrorMessage :msg="validationErrors[`devices.${index}.serialNumber`][0]"></ErrorMessage>
                                    </small>
                                </div>
                                <!-- Claim Reason -->
                                <div class="col-12 col-md-6 col-lg-6 mt-3">
                                    <label for="repairReason" class="form-label">Repair Reason <span class="text-danger">*</span></label>
                                    <select v-model="device.repairReason" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.repairReason`] }]">
                                        <option disabled value="">Select</option>
                                        <option v-for="(reason, i) in repairreasondata" :key="i" :value="reason.id">
                                            {{ reason.repair_reason_name }}
                                        </option>
                                    </select>
                                    <small v-if="validationErrors[`devices.${index}.repairReason`]" >
                                        <ErrorMessage :msg="validationErrors[`devices.${index}.repairReason`][0]"></ErrorMessage>
                                    </small>
                                </div>
                            </div>
                            <!-- Claim Details -->
                            <div class="mt-3">
                                <label class="form-label">Repair Details <span class="text-danger">*</span></label>
                                <textarea rows="5" v-model="device.repairDetails" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.repairDetails`] }]" placeholder="Enter your repair details"></textarea>
                                <small v-if="validationErrors[`devices.${index}.repairDetails`]" >
                                    <ErrorMessage :msg="validationErrors[`devices.${index}.repairDetails`][0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                       <!--  <button type="button" class="btn bg_blue text-white mt-4 d-flex align-items-center gap-10" @click="addDeviceList">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0" viewBox="0 0 426.667 426.667" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M405.332 192H234.668V21.332C234.668 9.559 225.109 0 213.332 0 201.559 0 192 9.559 192 21.332V192H21.332C9.559 192 0 201.559 0 213.332c0 11.777 9.559 21.336 21.332 21.336H192v170.664c0 11.777 9.559 21.336 21.332 21.336 11.777 0 21.336-9.559 21.336-21.336V234.668h170.664c11.777 0 21.336-9.559 21.336-21.336 0-11.773-9.559-21.332-21.336-21.332zm0 0" fill="#ffffff" opacity="1" data-original="#000000"></path></g></svg>
                            Add More
                        </button> -->
                    </div>

                    <!-- Step 3: Shipping Details -->
                    <div v-if="currentStep == 3" class="step ">
                        <!-- Selected Devices List from second step as (not duplicate allowed(device model and serial number)) table -->

                        <div class="mb-3 p-3 rounded border">
                            <h4 class="form-label fw-bold def_18_size">Selected Devices</h4>
                             <div class="glob_scroll">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Device</th>
                                                <th>Serial Number</th>
                                                <th>Repair Reason</th>
                                                <th>Repair Details</th>
                                                <th v-if="!isHodOrDirector">Repair Fee</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(device, index) in devices.filter((d, i, arr) => arr.findIndex(x => x.model?.id == d.model?.id && x.serialNumber == d.serialNumber) == i)" :key="index">
                                                <td>{{ device.model.title }} ({{ device.model.device_family_name }}) </td>
                                                <td>{{ device.serialNumber }}</td>
                                                <td>{{ repairreasondata.find(reason => reason?.id == device.repairReason)?.repair_reason_name }}</td>
                                                <td>{{ device.repairDetails }}</td>
                                                <td v-if="!isHodOrDirector">${{ device.repairFeeAmount }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot v-if="!isHodOrDirector">
                                            <tr><td colspan="4" class="text-end"><strong>Total</strong></td>
                                                <td><strong>${{ totalRepairFee.toFixed(2) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 bg-white p-3 rounded border">
                            <!-- Shipping Options -->
                            <div v-if="!isHodOrDirector" class="mb-3">
                                <label class="form-label fw-bold">Shipping Options (Required) <span class="text-danger">*</span></label>
                                <select class="form-control" @change="handleShippingOption" v-model="shippingOption" :class="['form-control',{ 'is-invalid': validationErrors.shippingOption },]">
                                    <option disabled value="">Select</option> shippingOptions
                                    <option v-for="(shippingOption, index) in shippingOptions" :key="index" :value="shippingOption.id"> {{ shippingOption.name }} (${{ shippingOption.price }}) </option>
                                </select>
                                <small v-if="validationErrors.shippingOption">
                                    <ErrorMessage :msg="validationErrors.shippingOption[0]"></ErrorMessage></small>
                            </div>
                            <!-- Grand Total -->
                            <div v-if="!isHodOrDirector && shippingOption" class="row mb-3">
                                <!-- Grand Total -->
                                <div class="col-md-6">
                                    <label for="grandTotal" class="form-label">Grand Total</label>
                                    <input type="text" id="grandTotal" class="form-control" v-model="grandTotalValue" readonly>
                                </div>
                            </div>
                            <!-- Card Details -->
                            <div v-if="!isHodOrDirector && shippingOption && this.grandTotal > 0" class="row mb-3">
                                <!-- Card Number -->
                                <div class="col-md-6">
                                    <label for="creditCardNumber" class="form-label">Credit Card <span class="text-danger">*</span></label>
                                    <div id="card-element" class="form-control"></div>

                                </div>
                                <!-- Cardholder name -->
                                <div class="col-md-6">
                                    <label for="cardHolderName" class="form-label">Cardholder Name <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Cardholder Name" v-model="cardHolderName" :class="['form-control', { 'is-invalid': validationErrors.cardHolderName },]">
                                    <small v-if="validationErrors.cardHolderName" ><ErrorMessage :msg="validationErrors.cardHolderName[0]"></ErrorMessage></small>
                                </div>
                            </div>
                            <div class="toggleWrapper d-flex justify-content-between maingape-15 align-items-center mb-2 border-bottom pb-2" @change="editAddressEnable">
                                <label class="fw-bold def_20_size"> Shipping Address </label>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="mr-3 fw-bold">Edit Address</span>
                                    <input type="checkbox" name="toggle1" class="mobileToggle" id="toggle1" >
                                    <label for="toggle1"></label>
                                </div>

                            </div>
                            <!-- <div class="">
                                <label class="form-label def_18_size fw-bold">Shipping Address</label>
                                <button type="button" @click="editAddressEnable()" class="btn bg-transparent btn-sm">
                                    <img :src="editIc" width="28" height="28">
                                </button>
                            </div> -->
                            <div class="row mb-2">
                                <!-- Shipping Address -->
                                <div class="col">
                                    <label for="streetAddress" class="form-label">Street Address <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter street address" v-model="streetAddress" :class="['form-control', { 'is-invalid': validationErrors.streetAddress }, { 'readonly-field': !isAddressEdit }]" :readonly="!isAddressEdit">
                                    <small v-if="validationErrors.streetAddress"><ErrorMessage :msg="validationErrors.streetAddress[0]"></ErrorMessage></small>
                                </div>
                                <!-- Address Line 1 -->
                                <div class="col">
                                    <label for="addressLine1" class="form-label">Address Line 1</label>
                                    <input type="text" placeholder="Enter address line 1" v-model="addressLine1" :class="['form-control', { 'is-invalid': validationErrors.addressLine1 }, { 'readonly-field': !isAddressEdit }]" :readonly="!isAddressEdit">
                                    <small v-if="validationErrors.addressLine1" ><ErrorMessage :msg="validationErrors.addressLine1[0]"></ErrorMessage></small>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <!-- Country -->
                                <div class="col">
                                    <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="country" placeholder="Search or select the country" label="name" track-by="code" :options="countryData" :multiple="false" :taggable="false" @select="state = null" selectLabel="" deselectLabel="" :class="[ '', { 'is-invalid': validationErrors.country }, { 'disabled-field': !isAddressEdit }]" :disabled="!isAddressEdit" required>
                                        <template #noResult>
                                            <span class="custom-message">No Country Found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No Country Found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors.country" ><ErrorMessage :msg="validationErrors.country[0]"></ErrorMessage></small>
                                </div>
                                <!-- State -->
                                <div class="col">
                                    <label for="state" class="form-label">State / Province / Region <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="state" placeholder="Search or select the state" label="name" track-by="abbreviation" :options="country ? statesData.filter((s) => s.country_code == country.code) : []" :disabled="!country || !isAddressEdit" :multiple="false" @select="city = null" :taggable="false" selectLabel="" deselectLabel="" :class="[ '', { 'is-invalid': validationErrors.state }, { 'disabled-field': !isAddressEdit }]" required>
                                        <template #noResult>
                                            <span class="custom-message">No State/Region Found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No State/Region Found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors.state" ><ErrorMessage :msg="validationErrors.state[0]"></ErrorMessage></small>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <!-- City -->
                                <div class="col">
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="city" placeholder="Search or select the city" label="name" track-by="province" :options="state ? cityData.filter((c) => c.state_code == state.abbreviation) : []" :disabled="!state || !isAddressEdit" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="[ '', { 'is-invalid': validationErrors.city }, { 'disabled-field': !isAddressEdit } ]" required>
                                        <template #noResult>
                                            <span class="custom-message">No City Found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No City Found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors.city"><ErrorMessage :msg="validationErrors.city[0]"></ErrorMessage></small>
                                </div>
                                <!-- ZIP Code -->
                                <div class="col">
                                    <label for="zipCode" class="form-label">ZIP / Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter ZIP code" v-model="zipCode" :class="['form-control', { 'is-invalid': validationErrors.zipCode }, { 'readonly-field': !isAddressEdit }]" :readonly="!isAddressEdit">
                                    <small v-if="validationErrors.zipCode" ><ErrorMessage :msg="validationErrors.zipCode[0]"></ErrorMessage></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <!-- <button v-if="currentStep == 1" type="button" class="btn btn-secondary"
                            @click="resetForm">Reset</button> -->
                        <a v-if="currentStep == 1" :href="$userAppUrl + 'sdcsmuser/user-track-repairs'" class="btn btn-secondary me-2">Cancel</a>
                        <button v-if="currentStep == 2 || currentStep == 3" class="btn customm_reset_btn"
                            @click="prevStep" :disabled="disabledButton && currentStep == 3">Back</button>
                        <button v-if="currentStep == 1 || currentStep == 2" class="btn bg_blue text-white" @click="nextStep">Next</button>
                        <button v-if="currentStep == 3" class="btn bg_blue text-white" @click="submitForm" :disabled="disabledButton && currentStep == 3">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        repairreasondata: {
            type: Object
        },
        userdata: {
            type: Object
        },
        shippingoptionsdata: {
            type: Object
        },
        stripekey:{
            type:String
        }
    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,

            steps: [
                { label: "User Details" },
                { label: "Device Details" },
                { label: "Shipping Details" },
            ],

            /** To manage the steps of the form */
            currentStep: 1,
            /** To manage the form data */
            /** To contain the first step data */
            firstName: this.userdata.firstName,
            lastName: this.userdata.lastName,
            phone: this.userdata.phone,
            email: this.userdata.email,
            metaKey: this.userdata.meta_key,

            /** To manage the address edit(enabled or disabled) */
            isAddressEdit: 0,

            /** To contain the second step data */
            numberOfDevices: 1,
            devices: [{
                model: null,
                serialNumber: '',
                repairReason: '',
                repairDetails: '',
                repairFeeAmount: 0,
            }],

            /** To store the device model data by searching */
            devicemodeldata: [],
            /** Used in searching of models */
            searchModelText: "",
            isSearchModelTooShort: false,

            /** To contain the third step data */
            isHodOrDirector: this.userdata.meta_key == 'org_it_hod' || this.userdata.meta_key == 'org_it_director' ? true : false,
            shippingOption: '',
            cardHolderName:'',
            streetAddress: '',
            addressLine1: '',
            // country: '',
            country: {
                code: 'US',
                name: 'United States'
            },
            state: '',
            city: '',
            zipCode: '',
            /** To manage the shipping options */
            shippingOptions: this.shippingoptionsdata,
            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,
            stripe: null,
            elements: null,
            card: null,
            paymentMethodID:null,
            StripeToken:null,

            /** To contain the validation errors */
            validationErrors: {},
            disabledButton: false,
            shippingAmount: '',
            totalRepairFee: 0,
            grandTotal: 0,
            grandTotalValue: '$0',

        };
    },
    created() {
        /** To format the phone numbers on page load */
        this.formatPhoneNumber();
    },
    watch: {
        currentStep(newStep) {
            if (newStep == 3 && !this.isHodOrDirector && this.shippingOption) {
                this.grandTotal = Number(this.totalRepairFee) + Number(this.shippingAmount);
                this.grandTotalValue = '$' + this.grandTotal.toFixed(2);
                if (this.grandTotal > 0) {
                    this.initializeCardElement();
                }
            }
            if (newStep == 2) {
                /** To get the device models */
                this.getDeviceModels();
            }
        },

        isAddressEdit(newVal) {
            this.isAddressEdit = newVal;
        }
    },
    methods: {

        /** Stripe Card Creation */
        initializeCardElement() {
            this.$nextTick(() => {
                if (!this.isHodOrDirector && this.grandTotal > 0) {

                    if (!this.stripe) {
                        this.stripe = Stripe(this.stripekey);
                        this.elements = this.stripe.elements();
                    }

                    /* Check if card element already exists */
                    /* If card exists, just remount it if needed */
                    if (this.card) {
                        /* Instead of unmounting and creating new, just remount */
                        this.card.mount("#card-element");
                    } else {
                        /* Create new card only if it doesn't exist */
                        this.card = this.elements.create("card", {
                            hidePostalCode: true,
                        });
                        this.card.mount("#card-element");
                    }
                }

            });
        },

        /** To enable the edit for the address on click of edit icon */
        editAddressEnable(){
            // this.isAddressEdit = 1;
            this.isAddressEdit = this.isAddressEdit == 0 ? 1 : 0;
        },

        /* get device models */
        async getDeviceModels(name = this.searchModelText) {
            show_ajax_loader();
            if (name && name.length < 5) {
                this.devicemodeldata = [];
                this.isSearchModelTooShort = true;
                hide_ajax_loader();
                return;
            }
            try {
                this.isSearchModelTooShort = false;
                const response = await axios.post(`${this.$userAppUrl}sdcsmuser/uninsured-devices`, {
                    name: name
                });
                if (response.data.success == true && response.data.deviceModels.length > 0) {
                    this.devicemodeldata = response.data.deviceModels;
                    hide_ajax_loader();
                } else {
                    /* This will show up in the dropdown regardless of success status */
                    this.devicemodeldata = [
                        {
                            id: null,
                            title: response.data.msg ?? "No device found",
                        },
                    ];
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$homeUrl}`),
                        5000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        /** device model name with family */
        nameWithfamily(option) {
            const deviceFamilyName = option.device_family_name ? ` (${option.device_family_name})` : '';
            return `${option.title}${deviceFamilyName}`;
        },

        /** To search the device model and get the data */
        updateSearchDeviceModelName(value) {
            if (value) {
                this.searchModelText = value;
                this.getDeviceModels(this.searchModelText);
            } else {
                this.searchModelText = '';
                this.getDeviceModels();
            }
        },



        /** Common function for both selection and Enter key */
        async selectDeviceModel(model) {
            show_ajax_loader();
            this.validationErrors = {};
            const payload = {
                modelId: model,
            };
            try{
                let response = await axios.post(
                    `${this.$userAppUrl}sdcsmuser/repair-plan`,
                    payload
                );
                if (response.data.success == true) {
                    let fee = 0;
                    let planId = null;
                    if(response.data.deviceModelPlans.length > 0){
                        fee = response.data.deviceModelPlans[0].price;
                        planId = response.data.deviceModelPlans[0].id;
                        hide_ajax_loader();
                    } else {
                        fee = 0;
                        planId = null;
                        hide_ajax_loader();
                    }
                    /* Update each device with this model ID */
                    this.devices.forEach(device => {
                        if (device.model?.id == model) {
                            device.repairFeeAmount = fee;
                        }
                    });
                } else if(response.data.errors) {
                    this.validationErrors = response.data.errors;
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
                    setTimeout(() => (location.href = `${this.$homeUrl}`), 3000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        onDeviceInput(event) {
            let value = parseInt(event.target.value, 10);

            /* Clamp input to min/max range */
            if (value > 50) {
                value = 50;
                this.validationErrors.numberOfDevices = ['You can only add up to 50 devices.'];
                setTimeout(() => {
                    this.validationErrors.numberOfDevices = null;
                }, 3000);
            } else if (value < 1) {
                value = 1;
            } else {
                /* Clear errors if input is valid and in range */
                this.validationErrors.numberOfDevices = null;
            }

            this.numberOfDevices = value;
            this.updateDeviceList();
        },

        /** To manage the fields according to device numbers */
        updateDeviceList() {
            show_ajax_loader();
            /* Adjust the devices array size based on numberOfDevices */
            const count = this.numberOfDevices;
            if (count > this.devices.length) {
                while (this.devices.length < count) {
                    this.devices.push({ model: null, serialNumber: '', repairReason: '', repairDetails: '', repairFeeAmount: 0});
                }
                hide_ajax_loader();
            } else {
                this.devices.splice(count);
                this.numberOfDevices = count;
                hide_ajax_loader();
            }
        },

        handleShippingOption() {
            const selectedOption = this.shippingOptions.find((so) => so.id == this.shippingOption);
            this.shippingAmount = selectedOption ? Number(selectedOption.price) : 0;
            const totalFee = Number(this.totalRepairFee);

            if (!isNaN(this.shippingAmount) && !isNaN(totalFee)) {
                this.grandTotal = totalFee + this.shippingAmount;
                this.grandTotalValue = '$' + this.grandTotal.toFixed(2);
                if (this.grandTotal > 0) {
                    this.initializeCardElement();
                }
            }
        },

        /** To manage the fields for add the multiple device (On Click Add more) */
        /*  addDeviceList() {
            show_ajax_loader();
            this.devices.push({ model: null, serialNumber: '', repairReason: '', repairDetails: '' });
            hide_ajax_loader();
        }, */

        /** To remove the device fields (Created from the add more) */
        removeDeviceList(index) {
            show_ajax_loader();
            this.devices.splice(index, 1);
            this.numberOfDevices -= 1;
            hide_ajax_loader();
        },

        /** Format phone Number */
        formatPhoneNumber() {
            if(this.phone != '' && this.phone != null){
                /* Keep only digits and allowed formatting chars */
                let cleaned = this.phone.replace(/[^\d\(\)\-\s]/g, '');

                /* Extract digits only */
                let digits = cleaned.replace(/\D/g, '');

                /* Limit to 10 digits for US/Canada */
                if (digits.length > 10) {
                    digits = digits.slice(0, 10);
                }

                /* Apply (XXX) XXX-XXXX format */
                if (digits.length > 6) {
                    this.phone = `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
                } else if (digits.length > 3) {
                    this.phone = `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                } else if (digits.length > 0) {
                    this.phone = `(${digits}`;
                } else {
                    this.phone = '';
                }
            }
        },

        /**  Paste Time check */
        handlePaste(event) {
            let paste = (event.clipboardData || window.clipboardData).getData('text');
            let digits = paste.replace(/\D/g, '').slice(0, 10);
            this.phone = digits;
            this.$nextTick(() => this.formatPhoneNumber());
        },

        /** Check valid phone number */
        isValidPhone(phone) {
            const pattern = /^\(\d{3}\) \d{3}-\d{4}$/;
            return pattern.test(phone);
        },

        /** On click of next button */
        async nextStep() {
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;

            /** Form validations */
            if (this.currentStep == 1) {
                if (!this.firstName) {
                    this.validationErrors.firstName = ['Please enter first name.'];
                    validationPassed = false;
                }
                if (!this.lastName) {
                    this.validationErrors.lastName = ['Please enter last name.'];
                    validationPassed = false;
                }
                if (this.lastName && this.lastName.length < 2) {
                    this.validationErrors.lastName = ['Please enter at least 2 characters.'];
                    validationPassed = false;
                }
                if (!this.phone) {
                    this.validationErrors.phone = ['Please enter phone number.'];
                    validationPassed = false;
                }
                if (this.phone && !this.isValidPhone(this.phone)) {
                    this.validationErrors.phone = ['Please enter valid phone number.'];
                    validationPassed = false;
                }
                if (!this.email) {
                    this.validationErrors.email = ['Please enter email address.'];
                    validationPassed = false;
                }
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
                // /** Updating the step number */
                // this.currentStep = 2;
                // hide_ajax_loader();
                // return;

            }
            if (this.currentStep == 2) {

                /* if (!this.devices.length) {
                    this.validationErrors.devices = ['Please add at least one device.'];
                    validationPassed = false;
                } */
               if (!this.numberOfDevices) {
                    this.validationErrors.numberOfDevices = ['Please enter the number of devices.'];
                    validationPassed = false;
                }
                if (this.numberOfDevices < 1 || this.numberOfDevices > 50) {
                    this.validationErrors.numberOfDevices = ['Please enter a valid number of devices.'];
                    validationPassed = false;
                }

                this.devices.forEach((device, i) => {
                    if (!device.model) {
                        this.validationErrors[`devices.${i}.modelId`] = [`Please select a model for Device ${i + 1}.`];
                        validationPassed = false;
                    }
                    if (!device.serialNumber) {
                        this.validationErrors[`devices.${i}.serialNumber`] = [`Please enter a serial number for Device ${i + 1}.`];
                        validationPassed = false;
                    }
                    if (!device.repairReason) {
                        this.validationErrors[`devices.${i}.repairReason`] = [`Please select a repair reason for Device ${i + 1}.`];
                        validationPassed = false;
                    }
                    if (!device.repairDetails) {
                        this.validationErrors[`devices.${i}.repairDetails`] = [`Please enter repair details for Device ${i + 1}.`];
                        validationPassed = false;
                    }
                });

                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
                /** Call a route here to get the address and other data of user */
                try{
                    const response = await axios.get(`${this.$userAppUrl}sdcsmuser/fileclaims/userdata`);
                    if(response.data.success == true){
                        if(response.data.user_Details){
                            this.streetAddress = response.data.user_Details.street_address;
                            this.addressLine1 = response.data.user_Details.address_line_2;
                            this.city = this.cityData.find(city => city.province == response.data.user_Details.city);
                            this.state = this.statesData.find(state => state.abbreviation == response.data.user_Details.state);
                            this.zipCode = response.data.user_Details.zip;
                            this.country = this.countryData.find(country => country.code == response.data.user_Details.country);

                            /** To format the phone number */
                            this.formatPhoneNumber();
                        }else {
                            this.isAddressEdit = 1;
                        }
                        hide_ajax_loader();
                    }else {
                        this.isAddressEdit = 1;
                    }
                } catch(error){
                    if (error && error.response && error.response.data && error.response.data.error) {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                        setTimeout(
                            () => (location.href = `${this.$userAppUrl}`),
                            4000
                        );
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    }
                }
               /*  this.totalRepairFee = this.devices.reduce((sum, device) => {
                    const fee = parseFloat(device.repairFeeAmount || 0);
                    return sum + (isNaN(fee) ? 0 : fee);
                }, 0); */

                const uniqueRepairDevices = this.devices.filter(
                    (d, i, arr) => arr.findIndex(x => x.model?.id == d.model?.id && x.serialNumber == d.serialNumber) == i
                );

                this.totalRepairFee = uniqueRepairDevices.reduce((sum, device) => {
                    const fee = parseFloat(device.repairFeeAmount || 0);
                    return sum + (isNaN(fee) ? 0 : fee);
                }, 0);


                /** Updating the step number */
                // this.currentStep = 3;
                // hide_ajax_loader();
                // return;

            }
            /** Updating the step number */
            this.currentStep += 1;
            hide_ajax_loader();
        },

        /** For the previous step */
        prevStep() {
            show_ajax_loader();
            this.currentStep -= 1;
            hide_ajax_loader();
        },

        /** To submit the form */
        async submitForm() {
            show_ajax_loader();
            /** Check validations for the second step */
            this.validationErrors = {};
            let validationPassed = true;
            /* let amount = this.shippingOptions.find((so,index) => so.id == this.shippingOption)?.price; */
            if (this.currentStep == 3) {
                const zipCodeRegex = /^[0-9]{5}$/;
                if(!this.shippingOption && !this.isHodOrDirector){
                    this.validationErrors.shippingOption = ['Please select a shipping option.'];
                    validationPassed = false;
                }
                if (!this.cardHolderName && !this.isHodOrDirector && this.shippingOption && this.grandTotal > 0) {
                    this.validationErrors.cardHolderName = ['Please enter cardholder name.'];
                    validationPassed = false;
                }
                if (!this.streetAddress) {
                    this.validationErrors.streetAddress = ['Please enter the street address.'];
                    validationPassed = false;
                }
                if (!this.country) {
                    this.validationErrors.country = ['Please enter country name.'];
                    validationPassed = false;
                }
                if (!this.state) {
                    this.validationErrors.state = ['Please enter state name.'];
                    validationPassed = false;
                }
                if (!this.city) {
                    this.validationErrors.city = ['Please enter city name.'];
                    validationPassed = false;
                }
                if (!this.zipCode) {
                    this.validationErrors.zipCode = ['Please enter zip code.'];
                    validationPassed = false;
                } else if (!zipCodeRegex.test(this.zipCode)) {
                    this.validationErrors.zipCode = ['Zip code must be a 5-digit number.'];
                    validationPassed = false;
                }
            }

            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            if (!this.isHodOrDirector && this.grandTotal > 0) {
                /** Step 1: Create a Payment Method using Stripe **/
                const { paymentMethod, error } = await this.stripe.createPaymentMethod({
                    type: "card",
                    card: this.card,
                    billing_details: {
                        name: this.cardHolderName,
                        email: this.email,
                        address: {
                            line1: this.streetAddress,
                            city: this.city.name,
                            state: this.state.abbreviation,
                            country: this.country.code,
                            postal_code: this.zipCode,
                        },
                    },
                });
                if (error) {
                    hide_ajax_loader();
                    this.validationErrors.payment = ['Payment processing failed. Please check your card details.'];
                    return;
                }
                /** Step 2: Payment amount */
                const { stripeToken, paymentError } = await this.stripe.createToken(this.card);
                if (paymentError) {
                    hide_ajax_loader();
                    this.validationErrors.payment = ['Payment processing failed. Please check your card details.'];
                    return;
                }
                this.paymentMethodID = paymentMethod.id;
                this.StripeToken = stripeToken;

            }

            try {
                this.disabledButton = true;
                let payload = {
                    metaValue: this.metaKey,
                    firstName: this.firstName,
                    lastName: this.lastName,
                    phone: this.phone.replace(/\D/g, ''),
                    email: this.email,
                    devices: this.devices.map(device => ({
                        modelId: device.model ? device.model.id : null,
                        deviceModelName: device.model ? device.model.title +' ('+device.model.device_family_name+')' : null,
                        serialNumber: device.serialNumber,
                        repairReason: device.repairReason,
                        repairDetails: device.repairDetails,
                        repairAmount: device.repairFeeAmount,
                    })).filter((d, i, arr) => arr.findIndex(x => x.modelId == d.modelId && x.serialNumber == d.serialNumber) == i),
                    isAddressEdit: this.isAddressEdit,
                    shippingOption: this.shippingOption ?? null,
                    amount: this.grandTotal.toFixed(2),
                    shippingAmount: this.shippingAmount,
                    stripeToken: this.StripeToken,
                    paymentMethodId: this.paymentMethodID, // Stripe Payment Method ID
                    cardHolderName: this.cardHolderName,
                    streetAddress: this.streetAddress,
                    addressLine1: this.addressLine1,
                    country: this.country.code,
                    state: this.state.abbreviation,
                    city: this.city.province,
                    zipCode: this.zipCode,
                };
                let response = await axios.post(`${this.$userAppUrl}sdcsmuser/user-repair-request/store`, payload);

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Redirect to the track step */
                    setTimeout(() => {
                        window.location.href = `${this.$userAppUrl}sdcsmuser/user-track-repairs`;
                    }, 2000);
                } else if (response.data.errors) {
                    this.disabledButton = false;
                    this.validationErrors = response.data.errors;
                    /* Update the step number based on the error response */
                    if (response.data.errors.firstName || response.data.errors.lastName || response.data.errors.phone || response.data.errors.email ) {
                        this.currentStep = 1;
                    } else if (response.data.errors.devices || response.data.errors.streetAddress || response.data.errors.addressLine1 || response.data.errors.country || response.data.errors.state || response.data.errors.city || response.data.errors.zipCode) {
                        this.currentStep = 3;
                    } else {
                        /* Handle other server-side errors */
                        this.currentStep = 2;
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
                        () => (location.href = `${this.$homeUrl}`),
                        4000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** To reset the form */
       /*  resetForm() {
            this.firstName = '';
            this.lastName = '';
            this.phone = '';
            this.email = '';
            this.validationErrors = {};
        }, */

        // updateDeviceList() {
        //     /* Adjust the devices array size based on numberOfDevices */
        //     const count = this.numberOfDevices;
        //     if (count > this.devices.length) {
        //         while (this.devices.length < count) {
        //             this.devices.push({ model: null, serialNumber: '', claimReason: '', claimDetails: '' });
        //         }
        //     } else {
        //         this.devices.splice(count);
        //     }
        // },
    }
};
</script>