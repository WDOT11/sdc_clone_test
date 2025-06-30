<template>
    <div class="container-fluid  mt_12">
        <div class="card onewhitebg rounded border-0 ">
            <div class="card-body ">
                <h4 class="coman_main_heading border-bottom pb-2 ">File Claim</h4>
                <div class="steps_wrap">
                    <div class="formobile ">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div id="steps-container">
                                <li class="step themetextcolor" v-if="currentStep == 1"><span class="mr-1">Step 1:</span> Device Details</li>
                                <li class="step themetextcolor" v-if="currentStep == 2"><span class="mr-1">Step 2:</span> Selected Devices</li>
                            </div>
                            <span class="steptext">Steps: {{ currentStep }}/{{ 2 }}</span>
                        </div>
                        <div id="step-indicator">
                            <div class="progress" :style="`width: ${(currentStep / 2) * 100}%`"></div>
                        </div>
                    </div>
                </div>

                <!-- Step 1: All Fields -->
                <div v-if="currentStep == 1" class="step">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-6 mt-3">
                            <label for="deviceModel" class="form-label">Select User <span class="text-danger">*</span></label>
                            <multiselect id="tagging" v-model="selectedUser" :searchable="true" @search-change="updateSearchUserName" @select="handleUserSelect" placeholder="Search or choose user" @remove="clearUserSelection" :custom-label="nameWithEmail" label="full_name" track-by="id" :options="userData" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['w-full', { 'border-red-500': validationErrors.selectedUser }]" required :allow-empty="true" :internal-search="true" :preserve-search="true">
                                <template #noOptions>
                                    <span class="custom-message">Please enter at least five characters</span>
                                </template>
                                <template #noResult>
                                    <span class="custom-message">No user found, please try another search.</span>
                                </template>
                            </multiselect>
                            <small v-if="validationErrors.selectedUser">
                                <ErrorMessage :msg="validationErrors.selectedUser[0]"></ErrorMessage>
                            </small>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6 mt-3">
                            <label for="numberOfDevices" class="form-label">Number of Devices <span class="text-danger">*</span></label>
                            <input type="number" min="1" max="50" placeholder="Enter the number of devices" v-model="numberOfDevices" :class="['form-control', { 'is-invalid': validationErrors.numberOfDevices }]" @input="onDeviceInput">
                            <small v-if="numberOfDevices > 50 && !validationErrors.numberOfDevices">
                                <ErrorMessage :msg="`You can only add up to 50 devices.`"></ErrorMessage>
                            </small>
                            <!-- <small v-if="numberOfDevices < 1"><ErrorMessage :msg="`Please enter a valid number of devices`"></ErrorMessage></small> -->
                            <small v-if="validationErrors.numberOfDevices">
                                <ErrorMessage :msg="validationErrors.numberOfDevices[0]"></ErrorMessage>
                            </small>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-12">

                        </div>
                    </div> -->
                    <div v-for="(device, index) in devices" :key="index"
                        class="device-info   mt-3 card">

                         <div class="d-flex flex-wrap card-header align-items-center ">
                            <div class="col-md-11">
                                <h4 class=" def_18_size themetextcolor  my-0 py-0">Device {{ index + 1 }}</h4>
                            </div>
                            <div class="col-md-1 d-flex justify-content-end">
                                 <img class="cursor" @click="removeDeviceList(index)" v-if="devices.length > 1" :src="deleteIc" width="30" height="30">
                            </div>
                        </div>

                       <div class="container-fluid">

                        <div class="row mt-2">
                            <!-- Select Device -->
                            <div class="col-12 col-md-6 col-lg-6 mb-3">
                                <label for="deviceModel" class="form-label">Select Device <span class="text-danger">*</span></label>
                                <multiselect id="tagging" v-model="device.selectedDevice" :searchable="true" @search-change="updateSearchDeviceName" placeholder="Search or choose device" @select="handleDeviceSelect(index)" label="device_title" track-by="id" :options="deviceClaimData" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['w-full', { 'border-red-500': validationErrors[`devices.${index}.selectedDevice`] }]" required :allow-empty="true" :internal-search="true" :preserve-search="false">
                                    <template #noOptions>
                                        <span class="custom-message">Please enter at least five characters</span>
                                    </template>
                                    <template #noResult>
                                        <span class="custom-message">No device found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="validationErrors[`devices.${index}.selectedDevice`]">
                                    <ErrorMessage :msg="validationErrors[`devices.${index}.selectedDevice`][0]">
                                    </ErrorMessage>
                                </small>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mb-3">
                                <label for="claimReason" class="form-label">Claim Reason <span class="text-danger">*</span></label>
                                <select v-model="device.claimReason" :class="['form-control  ', { 'is-invalid': validationErrors[`devices.${index}.claimReason`] }]">
                                    <option disabled value="">Select</option>
                                    <option v-for="(reason, i) in device.claimreasonsdata" :key="i" :value="reason.id">{{ reason.claim_reason_name }} </option>
                                </select>
                                <small v-if="validationErrors[`devices.${index}.claimReason`]">
                                    <ErrorMessage :msg="validationErrors[`devices.${index}.claimReason`][0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Claim Details <span class="text-danger">*</span></label>
                                <textarea rows="5" v-model="device.claimDetails" :class="['form-control ', { 'is-invalid': validationErrors[`devices.${index}.claimDetails`] }]" placeholder="Enter your claim details"></textarea>
                                <small v-if="validationErrors[`devices.${index}.claimDetails`]">
                                    <ErrorMessage :msg="validationErrors[`devices.${index}.claimDetails`][0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        </div>
                    </div>
                    <!-- <button type="button" class="btn bg-dark text-white mt-4 d-flex align-items-center gap-10"
                        @click="addDeviceList">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                            width="20" height="20" x="0" y="0" viewBox="0 0 426.667 426.667"
                            style="enable-background:new 0 0 512 512" xml:space="preserve">
                            <g>
                                <path
                                    d="M405.332 192H234.668V21.332C234.668 9.559 225.109 0 213.332 0 201.559 0 192 9.559 192 21.332V192H21.332C9.559 192 0 201.559 0 213.332c0 11.777 9.559 21.336 21.332 21.336H192v170.664c0 11.777 9.559 21.336 21.332 21.336 11.777 0 21.336-9.559 21.336-21.336V234.668h170.664c11.777 0 21.336-9.559 21.336-21.336 0-11.773-9.559-21.332-21.336-21.332zm0 0"
                                    fill="#ffffff" opacity="1" data-original="#000000"></path>
                            </g>
                        </svg>
                        Add More
                    </button> -->
                </div>

                <!-- Step 2: Shipping and Payment Details -->
                <div v-if="currentStep == 2" class="step onewhitebg p-4 border">
                    <!-- Selected Devices List from First Step as (not duplicate allowed) table -->
                    <div class="mb-3">
                        <label class="form-label fw-bold def_18_size">Selected Devices</label>
                         <div class="glob_scroll">
                            <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>Serial Number</th>
                                    <th>Claim Reason</th>
                                    <th>Claim Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(device, index) in devices.filter((d, i, arr) => arr.findIndex(x => x.selectedDevice?.id == d.selectedDevice?.id) == i)" :key="index">
                                    <td>{{ device.selectedDevice.device_model_name }}</td>
                                    <td>{{ device.selectedDevice.serial_number }}</td>
                                    <td>{{Object.values(device.claimreasonsdata).find(reason => reason?.id == device.claimReason)?.claim_reason_name || 'N/A'}}</td>
                                    <td>{{ device.claimDetails }}</td>
                                </tr>
                            </tbody>
                        </table></div></div>
                    </div>

                    <!-- Shipping Options -->
                    <div v-if="!isHodOrDirector" class="mb-3">
                        <label class="form-label fw-bold">Shipping Options (Required) <span class="text-danger">*</span></label>
                        <select class="form-control" v-model="shippingOption" :class="['form-control', { 'is-invalid': validationErrors.shippingOption },]">
                            <option disabled value="">Select</option> shippingOptions
                            <option v-for="(shippingOption, index) in shippingOptions" :key="index" :value="shippingOption.id">
                                {{ shippingOption.name }} (${{ shippingOption.price }}) </option>
                        </select>
                        <small v-if="validationErrors.shippingOption">
                            <ErrorMessage :msg="validationErrors.shippingOption[0]"></ErrorMessage>
                        </small>
                    </div>

                    <!-- Shipping Address -->
                    <div class="mb-3">
                        <div
                            class="toggleWrapper d-flex justify-content-between maingape-15 align-items-center mb-2 border-bottom pb-2">
                            <label class="fw-bold def_20_size"> Shipping Address</label>
                            <div class="d-flex justify-content-between align-items-center" @change="editAddressEnable">
                                <span class="mr-3 fw-bold">Edit Address</span>
                                <input type="checkbox" name="toggle1" class="mobileToggle" id="toggle1">
                                <label for="toggle1"></label>
                            </div>
                            <!-- <button type="button" @click="editAddressEnable()" class="btn bg-transparent btn-sm">
                            <img :src="editIc" width="28" height="28">
                        </button> -->
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="streetAddress" class="form-label">Street Address <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter street address" v-model="streetAddress" :class="['form-control', { 'is-invalid': validationErrors.streetAddress }, { 'readonly-field': !isAddressEdit }]" :readonly="!isAddressEdit">
                                <small v-if="validationErrors.streetAddress">
                                    <ErrorMessage :msg="validationErrors.streetAddress[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div class="col">
                                <label for="addressLine2" class="form-label">Address Line 2</label>
                                <input type="text" placeholder="Enter address line 2" v-model="addressLine2" :class="['form-control', { 'is-invalid': validationErrors.addressLine2 }, { 'readonly-field': !isAddressEdit }]" :readonly="!isAddressEdit">
                                <small v-if="validationErrors.addressLine2">
                                    <ErrorMessage :msg="validationErrors.addressLine2[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <multiselect id="country" v-model="country" placeholder="Search or select the country" label="name" track-by="code" :options="countryData" :multiple="false" :taggable="false" @select="state = null" selectLabel="" deselectLabel="" :class="[{ 'is-invalid': validationErrors.country }, { 'disabled-field': !isAddressEdit }]" required :disabled="!isAddressEdit">
                                    <template #noResult>
                                        <span class="custom-message">No Country Found.</span>
                                    </template>
                                    <template #noOptions>
                                        <span class="custom-message">No Country Found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="validationErrors.country"><ErrorMessage :msg="validationErrors.country[0]"></ErrorMessage></small>
                            </div>
                            <div class="col">
                                <label for="state" class="form-label">State / Province / Region <span class="text-danger">*</span></label>
                                <multiselect id="state" v-model="state" placeholder="Search or select the state" label="name" track-by="abbreviation" :options="country ? statesData.filter((s) => s.country_code == country.code) : []" :multiple="false" :taggable="false" @select="city = null" selectLabel="" deselectLabel="" :class="[{ 'is-invalid': validationErrors.state }, { 'disabled-field': !isAddressEdit }]" required :disabled="!country || !isAddressEdit">
                                    <template #noResult>
                                        <span class="custom-message">No State/Province Found.</span>
                                    </template>
                                    <template #noOptions>
                                        <span class="custom-message">No State/Province Found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="validationErrors.state">
                                    <ErrorMessage :msg="validationErrors.state[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <multiselect id="city" v-model="city" placeholder="Search or select the city"
                                    label="name" track-by="province"
                                    :options="state ? cityData.filter((c) => c.state_code == state.abbreviation) : []"
                                    :multiple="false" :taggable="false" selectLabel="" deselectLabel=""
                                    :class="[{ 'is-invalid': validationErrors.city }, { 'disabled-field': !isAddressEdit }]"
                                    required :disabled="!state || !isAddressEdit">
                                    <template #noResult>
                                        <span class="custom-message">No City Found.</span>
                                    </template>
                                    <template #noOptions>
                                        <span class="custom-message">No City Found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="validationErrors.city">
                                    <ErrorMessage :msg="validationErrors.city[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div class="col">
                                <label for="zipCode" class="form-label">ZIP / Postal Code <span class="text-danger">*</span></label>
                                <input type="number" min="0" placeholder="Enter ZIP code" v-model="zipCode"
                                    :class="['form-control', { 'is-invalid': validationErrors.zipCode }, { 'readonly-field': !isAddressEdit }]"
                                    :readonly="!isAddressEdit">
                                <small v-if="validationErrors.zipCode" class="text-red-500">
                                    <ErrorMessage :msg="validationErrors.zipCode[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        <!-- Phone Number -->
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="phoneNumber" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" placeholder="Enter the phone number" v-model="phoneNumber" @input="formatPhoneNumber" @paste.prevent="handlePaste" :class="['form-control',{ 'is-invalid': validationErrors.phoneNumber },]">
                                <small v-if="validationErrors.phoneNumber" ><ErrorMessage :msg="validationErrors.phoneNumber"></ErrorMessage></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <!-- Step 1 Buttons -->
                    <a v-if="currentStep == 1" :href="$userAppUrl + 'smarttiusadmin/track-claims'" class="btn btn-secondary me-2">Cancel</a>
                    <button v-if="currentStep == 2" class="btn customm_reset_btn  me-2" @click="prevStep" :disabled="disabledButton && currentStep == 2">Back</button>
                    <button v-if="currentStep == 1" class="btn bg_blue text-white " @click="nextStep">Next</button>

                    <!-- Step 3 Buttons -->
                    <button v-if="currentStep == 2" class="btn bg_blue text-white" @click="submitForm" :disabled="disabledButton && currentStep == 2">Submit</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        // claimreasonsdata: {
        //     type: Object
        // },
        shippingoptionsdata: {
            type: Object
        },
    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,

            /** To manage the steps of the form */
            currentStep: 1,
            selectedUser: null,
            userData: [],
            debounceTimer: null,
            /** To manage the form data */
            numberOfDevices: 1,
            /** To manage the device and claim details for the first step */
            devices: [{
                selectedDevice: null,
                serialNumber: '',
                claimReason: '',
                claimDetails: '',
                claimreasonsdata: [],
            }],

            /** To manage the address edit(enabled or disabled) */
            isAddressEdit: 0,

            /** To manage the form data (second step) */
            isHodOrDirector: false,
            shippingOption: '',
            cardNumber: '',
            cardHolderName: '',
            phoneNumber: '',
            streetAddress: '',
            addressLine2: '',
            city: '',
            state: '',
            zipCode: '',
            // country: '',
            country: {
                code: 'US',
                name: 'United States'
            },
            metaKey: '',

            /** Used in searching of devices */
            searchDeviceText: "",
            /** Used in searching of users */
            searchUserText: "",

            /** To manage the shipping options */
            shippingOptions: this.shippingoptionsdata,

            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,

            /** To store the device data by searching */
            deviceClaimData: [],

            /** To contain the validation errors */
            validationErrors: {},
            disabledButton: false,
            totalDevices: 0,
        }
    },
    watch: {
        isAddressEdit(newVal) {
            this.isAddressEdit = newVal;
        },
    },
    methods: {
        /** To enable the edit for the address on click of edit icon */
        editAddressEnable() {
            this.isAddressEdit = this.isAddressEdit == 0 ? 1 : 0;
        },

        /** Check valid phone number */
        isValidPhone(phone) {
            const pattern = /^\(\d{3}\) \d{3}-\d{4}$/;
            return pattern.test(phone);
        },

        /** On click of the next on first step */
        async nextStep() {
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;

            if (!this.selectedUser) {
                this.validationErrors.selectedUser = ['Please select the user.'];
                validationPassed = false;
            }
            if(!this.numberOfDevices){
                this.validationErrors.numberOfDevices = ['Please enter the number of devices.'];
                validationPassed = false;
            }
            if (this.numberOfDevices < 1 || this.numberOfDevices > 50) {
                this.validationErrors.numberOfDevices = ['Please enter a valid number of devices.'];
                validationPassed = false;
            }

            this.devices.forEach((device, index) => {
                if (!device.selectedDevice) {
                    this.validationErrors[`devices.${index}.selectedDevice`] = [`Please select the device.`];
                    validationPassed = false;
                }
                if (!device.claimReason) {
                    this.validationErrors[`devices.${index}.claimReason`] = [`Please select the claim reason.`];
                    validationPassed = false;
                }
                if (!device.claimDetails) {
                    this.validationErrors[`devices.${index}.claimDetails`] = [`Please enter claim details.`];
                    validationPassed = false;
                }
            });

            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            /** Updating the step number */
            this.currentStep = 2;
            /** Call a route here to get the address and other data of user */
            try {
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/file-claim/userdata?userId=${this.selectedUser.id}`);
                if (response.data.success == true) {
                    if (response.data.user_Details) {
                        this.metaKey = response.data.user_Details.meta_key;
                        this.isHodOrDirector = response.data.user_Details.meta_key == 'org_it_hod' || response.data.user_Details.meta_key == 'org_it_director' ? true : false,
                        this.phoneNumber = response.data.user_Details.phone;
                        this.streetAddress = response.data.user_Details.street_address;
                        this.addressLine2 = response.data.user_Details.address_line_2;
                        this.city = this.cityData.find(city => city.province == response.data.user_Details.city);
                        this.state = this.statesData.find(state => state.abbreviation == response.data.user_Details.state);
                        this.zipCode = response.data.user_Details.zip;
                        this.country = this.countryData.find(country => country.code == response.data.user_Details.country);

                        /** To format the phone number */
                        this.formatPhoneNumber();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    }
                    hide_ajax_loader();
                }
                else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** On click of the back button on the second step */
        prevStep() {
            this.currentStep = 1;
        },

        /** Submit form */
        async submitForm() {
            show_ajax_loader();
            /** Check validations for the second step */
            this.validationErrors = {};
            let validationPassed = true;
            const zipCodeRegex = /^[0-9]{5}$/;

            if (!this.shippingOption && !this.isHodOrDirector) {
                this.validationErrors.shippingOption = ['Please select a shipping option.'];
                validationPassed = false;
            }
            if (this.phoneNumber && !this.isValidPhone(this.phoneNumber)) {
                this.validationErrors.phoneNumber = ['Please enter valid phone number.'];
                validationPassed = false;
            }
            if (!this.streetAddress) {
                this.validationErrors.streetAddress = ['Please enter the street address.'];
                validationPassed = false;
            }
            if (!this.city) {
                this.validationErrors.city = ['Please enter city name.'];
                validationPassed = false;
            }
            if (!this.state) {
                this.validationErrors.state = ['Please enter state name.'];
                validationPassed = false;
            }
            if (!this.zipCode) {
                this.validationErrors.zipCode = ['Please enter zip code.'];
                validationPassed = false;
            } else if (!zipCodeRegex.test(this.zipCode)) {
                this.validationErrors.zipCode = ['Zip code must be a 5-digit number.'];
                validationPassed = false;
            }
            if (!this.country) {
                this.validationErrors.country = ['Please enter country name.'];
                validationPassed = false;
            }

            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                this.disabledButton = true;
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/file-claim/store`, {
                    selectedUser: this.selectedUser.id,
                    full_name: this.selectedUser.full_name,
                    deviceData: this.devices.map(device => ({
                        selectedDevice: device.selectedDevice ? device.selectedDevice.id : null,
                        selectedDeviceTitle: device.selectedDevice ? device.selectedDevice.device_title : null,
                        claimReason: device.claimReason,
                        claimDetails: device.claimDetails,
                    })).filter((d, i, arr) => arr.findIndex(x => x.selectedDevice == d.selectedDevice) == i),
                    /** Second step data */
                    isAddressEdit: this.isAddressEdit,
                    metaValue: this.metaKey,
                    shippingOption: this.shippingOption ?? null,
                    phone : this.phoneNumber.replace(/\D/g, ''),
                    streetAddress: this.streetAddress,
                    addressLine2: this.addressLine2,
                    city: this.city.province,
                    state: this.state.abbreviation,
                    zipCode: this.zipCode,
                    country: this.country.code
                });

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;

                    /** To make fields empty */
                    this.devices = [
                        {
                            serialNumber: '',
                            selectedDevice: '',
                            claimReason: '',
                            claimDetails: '',
                            claimreasonsdata: [],
                        }
                    ];
                    this.shippingOption = 1;
                    this.deductibleCost = '';
                    /*  this.phoneNumber = ''; */
                    this.cardNumber = '';
                    this.cardHolderName = '';
                    this.streetAddress = '';
                    this.addressLine2 = '';
                    this.city = '';
                    this.state = '';
                    this.zipCode = '';
                    this.country = '';
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin/track-claims`), 2000);
                } else if (response.data.errors) {
                    hide_ajax_loader();
                    this.disabledButton = false;
                    this.validationErrors = response.data.errors;
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
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
                // Clear errors if input is valid and in range
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
                    this.devices.push({
                        selectedDevice: null,
                        serialNumber: '',
                        claimReason: '',
                        claimDetails: '',
                        claimreasonsdata: [],
                    });
                }
                hide_ajax_loader();
            } else {
                this.devices.splice(count);
                hide_ajax_loader();
            }
        },

        /** To manage the fields for add the multiple device (On Click Add more) */
        // addDeviceList() {
        //     show_ajax_loader();
        //     this.devices.push({ model: null, serialNumber: '', claimReason: '', claimDetails: '' });
        //     hide_ajax_loader();
        // },

        /** To remove the device fields (Created from the add more) */
        removeDeviceList(index) {
            show_ajax_loader();
            this.numberOfDevices -= 1;
            this.devices.splice(index, 1);
            hide_ajax_loader();
        },

        updateSearchUserName(name) {
            this.searchUserText = name;

            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }

            this.debounceTimer = setTimeout(() => {
                this.getUser(name);
            }, 400); /* Delay in ms */
        },

        /** user name with email */
        nameWithEmail(option) {
            const email = option.email ? ` (${option.email})` : '';
            return `${option.full_name}${email}`;
        },

        /** To get user data */
        async getUser(name) {
            show_ajax_loader();

            if (name.length < 5) {
                this.userData = [{
                    id: null,
                    full_name: "Please enter at least five characters",
                }];
                hide_ajax_loader();
                return;
            }

            try {
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/file-claim/user?search=${name}`);

                if (response.data.success && response.data.user_Details.length > 0) {
                    this.userData = response.data.user_Details;
                } else {
                    this.userData = [{
                        id: null,
                        full_name: "No user found",
                    }];
                }
                hide_ajax_loader();
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong. Please try again.";
            }
        },

        /** clear User Selection */
        clearUserSelection() {
            this.searchDeviceText = ''; /* clear any previous search */
            this.deviceClaimData = [];
            this.devices = [{
                selectedDevice: null,
                serialNumber: '',
                claimReason: '',
                claimDetails: '',
                claimreasonsdata: [],
            }];
        },

        handleUserSelect() {
            if (this.selectedUser && this.selectedUser?.id) {
                this.searchDeviceText = ''; /* clear any previous search */
                this.deviceClaimData = [];
                this.devices = [{
                    selectedDevice: null,
                    serialNumber: '',
                    claimReason: '',
                    claimDetails: '',
                    claimreasonsdata: [],
                }];
                this.getDevices();/* call your fetch method */
            } else {
                this.deviceClaimData = [];  /* clear device list if no user */
                this.searchDeviceText = ''; /* clear any previous search */
            }
        },

        /** To search the device and get the data */
        updateSearchDeviceName(value) {
            if (value) {
                this.searchDeviceText = value;
                this.getDevices(value);
            } else {
                this.searchDeviceText = '';
                this.getDevices();
            }
        },

        /** Handle Device Select */
        handleDeviceSelect(index) {
            const device = this.devices[index];
            this.getClaimReasons(device.selectedDevice.id, index);
        },

        /** To get the devices data */
        async getDevices(name = this.searchDeviceText) {
            show_ajax_loader();
            /* Enter at list 5 characters */
            if (name && name.length < 5) {
                this.deviceClaimData = [
                    {
                        id: null,
                        device_title: "Please enter at least five characters",
                    },
                ];
                hide_ajax_loader();
                return;
            }
            if (!this.selectedUser) {
                 this.deviceClaimData = [
                    {
                        id: null,
                        device_title: "No device found.",
                    },
                ];
                hide_ajax_loader();
                return;
            }
            try {
                const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/file-claim/fetch-devices`, {
                    name: name,
                    user_id: this.selectedUser ? this.selectedUser.id : null,
                });
                if (response.data.success == true && response.data.deviceData.length > 0) {
                    this.totalDevices = response.data.totalDevices;
                    this.deviceClaimData = response.data.deviceData;
                    hide_ajax_loader();
                } else {
                    /* This will show up in the dropdown regardless of success status */
                    this.deviceClaimData = [
                        {
                            id: null,
                            device_title: "No device found.",
                        },
                    ];
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$homeUrl}`), 5000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        /** To get Claim reasons according to the device */
        async getClaimReasons(deviceId, index) {
            show_ajax_loader();

            if (deviceId == null) {

                this.devices[index].claimreasonsdata = [
                    {
                        id: null,
                        reason: "No claim reason found",
                    },
                ];
                hide_ajax_loader();
                return;

            }
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/file-claim/claim-reasons?deviceId=${deviceId}`);
                if (response.data.success == true) {
                    if (response.data.claim_reasons) {
                        this.devices[index].claimreasonsdata = response.data.claim_reasons;
                    } else {
                        this.devices[index].claimreasonsdata = [
                            {
                                id: null,
                                reason: "No claim reason found",
                            },
                        ];

                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
                hide_ajax_loader();
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        /** Format phone Number */
        formatPhoneNumber() {
            if(this.phoneNumber !== '' && this.phoneNumber !== null ){
                /* Keep only digits and allowed formatting chars */
                let cleaned = this.phoneNumber.replace(/[^\d\(\)\-\s]/g, '');

                /* Extract digits only */
                let digits = cleaned.replace(/\D/g, '');

                /* Limit to 10 digits for US/Canada */
                if (digits.length > 10) {
                    digits = digits.slice(0, 10);
                }

                /* Apply (XXX) XXX-XXXX format */
                if (digits.length > 6) {
                this.phoneNumber = `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
                } else if (digits.length > 3) {
                this.phoneNumber = `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                } else if (digits.length > 0) {
                this.phoneNumber = `(${digits}`;
                } else {
                this.phoneNumber = '';
                }
            }
        },

        /**  Paste Time check */
        handlePaste(event) {
            let paste = (event.clipboardData || window.clipboardData).getData('text');
            let digits = paste.replace(/\D/g, '').slice(0, 10);
            this.phoneNumber = digits;
            this.$nextTick(() => this.formatPhoneNumber());
        },
    }
}
</script>