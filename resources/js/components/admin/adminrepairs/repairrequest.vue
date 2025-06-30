<template>
    <div class="container-fluid mt_12">
        <div class="card border-0 onewhitebg">
            <div class="card-body  ">
                <h4 class="coman_main_heading border-bottom pb-2 mb-3">Repair Device</h4>
                <!-- Step Indicators -->
                <div class="steps_wrap">
                    <div class="formobile onewhitebg">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div id="steps-container">
                                <li class="step themetextcolor" v-if="currentStep == 1"><span class="mr-1">Step 1</span> Device
                                    Details</li>
                                <li class="step themetextcolor" v-if="currentStep == 2"><span class="mr-1">Step 2</span>Selected
                                    Devices</li>


                            </div>
                            <span class="steptext">Steps: {{ currentStep }}/{{ 2 }}</span>
                        </div>
                        <div id="step-indicator">
                            <div class="progress" :style="`width: ${(currentStep / 2) * 100}%`"></div>
                        </div>
                    </div>
                </div>
                <div class=" ">
                    <!-- Step 1: Device Details -->
                    <div v-if="currentStep == 1" class="step">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6 mb-3">
                                <label for="selectedUser" class="form-label">Select User <span class="text-danger">*</span></label>
                                <multiselect id="tagging" v-model="selectedUser" :searchable="true" @search-change="updateSearchUserName" placeholder="Search or choose user" :custom-label="nameWithEmail" label="full_name" track-by="id" :options="userData" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['w-full', { 'border-red-500': validationErrors.selectedUser }]" :allow-empty="true" :internal-search="true" :preserve-search="true" required>
                                    <template #noOptions>
                                        <span class="custom-message">Please enter at least five characters</span>
                                    </template>
                                    <template #noResult>
                                        <span class="custom-message">{{ isSearchUserTooShort ? 'Please enter at least five characters' : 'No user found.'}}</span>
                                    </template>
                                </multiselect>
                                <small v-if="validationErrors.selectedUser">
                                    <ErrorMessage :msg="validationErrors.selectedUser[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mb-3">
                                <label for="numberOfDevices" class="form-label">Number of Devices <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="50" placeholder="Enter the number of devices" v-model="numberOfDevices" :class="['form-control', { 'is-invalid': validationErrors.numberOfDevices }]" @input="onDeviceInput">
                                <small v-if="numberOfDevices > 50 && !validationErrors.numberOfDevices">
                                    <ErrorMessage :msg="`You can only add up to 50 devices.`"></ErrorMessage>
                                </small>
                                <small v-if="validationErrors.numberOfDevices">
                                    <ErrorMessage :msg="validationErrors.numberOfDevices[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        <div v-for="(device, index) in devices" :key="index"
                            class="device-info onewhitebg mb-3 card">
                            <div class="d-flex align-items-center card-header ">
                                <div class="col-md-11">
                                    <h4 class="fw-bold def_18_size my-0 themetextcolor">Device {{ index + 1 }}</h4>
                                </div>
                                <div class="col-md-1 ">
                                    <div class="d-flex justify-content-end">
                                       <img class="cursor" @click="removeDeviceList(index)" v-if="devices.length > 1" :src="deleteIc" width="28" height="28">
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid p-3">
                            <div class="row">
                                <!-- Select Device Model -->
                                <div class="col-md-12 mb-3">
                                    <label for="deviceModel" class="form-label">Model <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="device.model" :searchable="true" placeholder="Search or choose device model" @search-change="updateSearchDeviceModelName" :custom-label="nameWithfamily" label="title" track-by="id" :options="devicemodeldata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['w-full', { 'border-red-500': validationErrors[`devices.${index}.modelId`] }]" :allow-empty="true" :internal-search="true" :preserve-search="true" required>
                                         <template #noOptions>
                                            <span class="custom-message">Please enter at least five characters</span>
                                        </template>
                                        <template #noResult>
                                            <span class="custom-message">
                                                {{ isSearchModelTooShort ? "Please enter at least five characters" : "No Device Model Found." }}
                                            </span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors[`devices.${index}.modelId`]">
                                        <ErrorMessage :msg="validationErrors[`devices.${index}.modelId`][0]">
                                        </ErrorMessage>
                                    </small>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Device Serial number/Asset tag -->
                                <div class="col-12 col-md-6 col-lg-6 mb-3">
                                    <label class="form-label">Serial number/Asset tag <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Serial number/Asset tag"
                                        v-model="device.serialNumber"
                                        :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.serialNumber`] }]">
                                    <small v-if="validationErrors[`devices.${index}.serialNumber`]">
                                        <ErrorMessage :msg="validationErrors[`devices.${index}.serialNumber`][0]">
                                        </ErrorMessage>
                                    </small>
                                </div>
                                <!-- Claim Reason -->
                                <div class="col-12 col-md-6 col-lg-6 mb-3">
                                    <label for="repairReason" class="form-label">Repair Reason <span class="text-danger">*</span></label>
                                    <select v-model="device.repairReason"
                                        :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.repairReason`] }]">
                                        <option disabled value="">Select</option>
                                        <option v-for="(reason, i) in repairreasondata" :key="i" :value="reason.id">
                                            {{ reason.repair_reason_name }}
                                        </option>
                                    </select>
                                    <small v-if="validationErrors[`devices.${index}.repairReason`]">
                                        <ErrorMessage :msg="validationErrors[`devices.${index}.repairReason`][0]">
                                        </ErrorMessage>
                                    </small>
                                </div>
                            </div>
                            <!-- Claim Details -->
                            <div class="">
                                <label class="form-label">Repair Details <span class="text-danger">*</span></label>
                                <textarea rows="5" v-model="device.repairDetails"
                                    :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.repairDetails`] }]"
                                    placeholder="Enter your repair details"></textarea>
                                <small v-if="validationErrors[`devices.${index}.repairDetails`]">
                                    <ErrorMessage :msg="validationErrors[`devices.${index}.repairDetails`][0]">
                                    </ErrorMessage>
                                </small>
                            </div>
                        </div>
                        </div>
                       <!--  <button type="button" class="btn bg_blue text-white mt-4 d-flex align-items-center gap-10"
                            @click="addDeviceList">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0"
                                viewBox="0 0 426.667 426.667" style="enable-background:new 0 0 512 512"
                                xml:space="preserve">
                                <g>
                                    <path
                                        d="M405.332 192H234.668V21.332C234.668 9.559 225.109 0 213.332 0 201.559 0 192 9.559 192 21.332V192H21.332C9.559 192 0 201.559 0 213.332c0 11.777 9.559 21.336 21.332 21.336H192v170.664c0 11.777 9.559 21.336 21.332 21.336 11.777 0 21.336-9.559 21.336-21.336V234.668h170.664c11.777 0 21.336-9.559 21.336-21.336 0-11.773-9.559-21.332-21.336-21.332zm0 0"
                                        fill="#ffffff" opacity="1" data-original="#000000"></path>
                                </g>
                            </svg>
                            Add More
                        </button> -->
                    </div>

                    <!-- Step 2: Shipping Details -->
                    <div v-if="currentStep == 2" class="step ">
                        <div class="mb-3">
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(device, index) in devices.filter((d, i, arr) => arr.findIndex(x => x.model?.id == d.model?.id && x.serialNumber == d.serialNumber) == i)"
                                        :key="index">
                                        <td>{{ device.model.title }} ({{ device.model.device_family_name }}) </td>
                                        <td>{{ device.serialNumber }}</td>
                                        <td>{{repairreasondata.find(reason => reason?.id ==
                                            device.repairReason)?.repair_reason_name}}
                                        </td>
                                        <td>{{ device.repairDetails }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </div></div>
                        </div>
                        <div class="mb-3 bg-white p-3 rounded border">
                            <!-- Shipping Options -->
                            <div v-if="!isHodOrDirector" class="mb-3">
                                <label class="form-label fw-bold">Shipping Options (Required) <span class="text-danger">*</span></label>
                                <select class="form-control" v-model="shippingOption"
                                    :class="['form-control', { 'is-invalid': validationErrors.shippingOption },]">
                                    <option disabled value="">Select</option> shippingOptions
                                    <option v-for="(shippingOption, index) in shippingOptions" :key="index"
                                        :value="shippingOption.id"> {{
                                            shippingOption.name }} (${{ shippingOption.price }}) </option>
                                </select>
                                <small v-if="validationErrors.shippingOption">
                                    <ErrorMessage :msg="validationErrors.shippingOption[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div class="toggleWrapper d-flex justify-content-between maingape-15 align-items-center mb-2 border-bottom pb-2"
                                @change="editAddressEnable">
                                <label class="fw-bold def_20_size"> Shipping Address </label>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="mr-3 fw-bold">Edit Address</span>
                                    <input type="checkbox" name="toggle1" class="mobileToggle" id="toggle1">
                                    <label for="toggle1"></label>
                                </div>

                            </div>
                            <div class="row mb-2">
                                <!-- Shipping Address -->
                                <div class="col">
                                    <label for="streetAddress" class="form-label">Street Address <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter street address" v-model="streetAddress"
                                        :class="['form-control', { 'is-invalid': validationErrors.streetAddress }, { 'readonly-field': !isAddressEdit }]"
                                        :readonly="!isAddressEdit">
                                    <small v-if="validationErrors.streetAddress">
                                        <ErrorMessage :msg="validationErrors.streetAddress[0]"></ErrorMessage>
                                    </small>
                                </div>
                                <!-- Address Line 1 -->
                                <div class="col">
                                    <label for="addressLine1" class="form-label">Address Line 1</label>
                                    <input type="text" placeholder="Enter address line 1" v-model="addressLine1"
                                        :class="['form-control', { 'is-invalid': validationErrors.addressLine1 }, { 'readonly-field': !isAddressEdit }]"
                                        :readonly="!isAddressEdit">
                                    <small v-if="validationErrors.addressLine1">
                                        <ErrorMessage :msg="validationErrors.addressLine1[0]"></ErrorMessage>
                                    </small>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <!-- Country -->
                                <div class="col">
                                    <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="country" placeholder="Search or select the country" label="name" track-by="code" :options="countryData" :multiple="false" :taggable="false" @select="state = null" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.country }, { 'disabled-field': !isAddressEdit }]" :disabled="!isAddressEdit" required>
                                        <template #noResult>
                                            <span class="custom-message">No Country Found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No Country Found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors.country">
                                        <ErrorMessage :msg="validationErrors.country[0]"></ErrorMessage>
                                    </small>
                                </div>
                                <!-- State -->
                                <div class="col">
                                    <label for="state" class="form-label">State / Province / Region <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="state" placeholder="Search or select the state" label="name" track-by="abbreviation" :options="country ? statesData.filter((s) => s.country_code == country.code) : []" :disabled="!country || !isAddressEdit" :multiple="false" :taggable="false" @select="city = null" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.state }, { 'disabled-field': !isAddressEdit }]" required>
                                        <template #noResult>
                                            <span class="custom-message">No State/Region Found.</span>
                                        </template>
                                        <template #noOptions>
                                            <span class="custom-message">No State/Region Found.</span>
                                        </template>
                                    </multiselect>
                                    <small v-if="validationErrors.state">
                                        <ErrorMessage :msg="validationErrors.state[0]"></ErrorMessage>
                                    </small>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <!-- City -->
                                <div class="col">
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <multiselect id="tagging" v-model="city" placeholder="Search or select the city" label="name" track-by="province" :options="state ? cityData.filter((c) => c.state_code == state.abbreviation) : []" :disabled="!state || !isAddressEdit" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.city }, { 'disabled-field': !isAddressEdit }]" required>
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
                                <!-- ZIP Code -->
                                <div class="col">
                                    <label for="zipCode" class="form-label">ZIP / Postal Code <span class="text-danger">*</span></label>
                                    <input type="number" min="0" placeholder="Enter ZIP code" v-model="zipCode" :class="['form-control', { 'is-invalid': validationErrors.zipCode }, { 'readonly-field': !isAddressEdit }]" :readonly="!isAddressEdit">
                                    <small v-if="validationErrors.zipCode">
                                        <ErrorMessage :msg="validationErrors.zipCode[0]"></ErrorMessage>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a v-if="currentStep == 1" :href="$userAppUrl + 'smarttiusadmin/track-repairs'" class="btn btn-secondary me-2">Cancel</a>
                        <button v-if="currentStep == 1" class="btn bg_blue text-white" @click="nextStep">Next</button>
                        <button v-if="currentStep == 2" class="btn btn-dark" @click="prevStep":disabled="disabledButton && currentStep == 2">Back</button>
                        <button v-if="currentStep == 2" class="btn bg_blue text-white" @click="submitForm"
                            :disabled="disabledButton && currentStep == 2">Submit</button>
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
        shippingoptionsdata: {
            type: Object
        },
    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,

            steps: [
                { label: "Device Details" },
                { label: "Shipping Details" },
            ],

            /** To manage the steps of the form */
            currentStep: 1,
            /** To manage the form data */

            /** To contain the first step data */
            selectedUser: null,
            userData: [],
            debounceTimer: null,
            searchUserText: "", /** Used in searching of users */
            isSearchUserTooShort: false,
            numberOfDevices: 1,
            devices: [{
                model: null,
                serialNumber: '',
                repairReason: '',
                repairDetails: '',
            }],
            devicemodeldata: [], /** To store the device model data by searching */
            searchModelText: "", /** Used in searching of device models */
            isSearchModelTooShort: false,
            /** To contain the third step data */
            isHodOrDirector: false,
            shippingOption: '',
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
            metaKey: '',

            isAddressEdit: 0, /** To manage the address edit(enabled or disabled) */

            shippingOptions: this.shippingoptionsdata,  /** To manage the shipping options *
            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,


            validationErrors: {}, /** To contain the validation errors */
            disabledButton: false,
        };
    },
    created() {
        /** To get the device models */
        this.getDeviceModels(this.searchModelText);
    },
    watch: {
        isAddressEdit(newVal) {
            this.isAddressEdit = newVal;
        }
    },
    methods: {
        /** To enable the edit for the address on click of edit icon */
        editAddressEnable() {
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
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/repair-request/fetch-devices${name ? `?name=${name}` : ''}`);
                if (response.data.success == true && response.data.deviceModels.length > 0) {
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
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
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

        /** To search the user */
        updateSearchUserName(name) {
            this.searchUserText = name;

            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }

            this.debounceTimer = setTimeout(() => {
                this.getUser(name);
            }, 400); // Delay in ms
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
                this.isSearchUserTooShort = true;
                this.userData = [];
                hide_ajax_loader();
                return;
            }

            try {
                this.isSearchUserTooShort = false;
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/file-claim/user?search=${name}`);

                if (response.data.success == true && response.data.user_Details.length > 0) {
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
                    this.devices.push({ model: null, serialNumber: '', repairReason: '', repairDetails: '' });
                }
                hide_ajax_loader();
            } else {
                this.devices.splice(count);
                this.numberOfDevices = count;
                hide_ajax_loader();
            }
        },

        /** To manage the fields for add the multiple device (On Click Add more) */
        /* addDeviceList() {
            show_ajax_loader();
            this.devices.push({ model: null, serialNumber: '', repairReason: '', repairDetails: '' });
            hide_ajax_loader();
        }, */

        /** To remove the device fields (Created from the add more) */
        removeDeviceList(index) {
            show_ajax_loader();
            this.numberOfDevices -= 1;
            this.devices.splice(index, 1);
            hide_ajax_loader();
        },

        /** On click of next button */
        async nextStep() {
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;
            /** Form validations */
            if (this.currentStep == 1) {
                if (!this.selectedUser) {
                    this.validationErrors.selectedUser = ['Please select the user.'];
                    validationPassed = false;
                }
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

            }

            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            /** Updating the step number */
            this.currentStep += 1;
            if (this.currentStep == 2) {
                /** Call a route here to get the address and other data of user */
                try {
                    const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/repair-request/userdata?userId=${this.selectedUser.id}`);
                    if (response.data.success == true) {
                        if (response.data.user_Details) {
                            this.metaKey = response.data.user_Details.meta_key;
                            this.streetAddress = response.data.user_Details.street_address;
                            this.addressLine1 = response.data.user_Details.address_line_2;
                            this.city = this.cityData.find(city => city.province == response.data.user_Details.city);
                            this.state = this.statesData.find(state => state.abbreviation == response.data.user_Details.state);
                            this.zipCode = response.data.user_Details.zip;
                            this.country = this.countryData.find(country => country.code == response.data.user_Details.country);
                        } else {
                            this.isAddressEdit = 1;
                        }
                        hide_ajax_loader();
                    } else {
                        this.isAddressEdit = 1;
                    }
                } catch (error) {
                    if (error && error.response && error.response.data && error.response.data.error) {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    }
                }
            }
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

            if (this.currentStep == 2) {
                const zipCodeRegex = /^[0-9]{5}$/;
                if (!this.shippingOption && !this.isHodOrDirector) {
                    this.validationErrors.shippingOption = ['Please select a shipping option.'];
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
            try {
                this.disabledButton = true;
                let payload = {
                    selectedUser: this.selectedUser.id,
                    metaValue: this.metaKey,
                    firstName: this.selectedUser.first_name,
                    lastName: this.selectedUser.last_name,
                    phone: this.selectedUser.phone.replace(/\D/g, ''),
                    email: this.selectedUser.email,
                    devices: this.devices.map(device => ({
                        modelId: device.model ? device.model.id : null,
                        deviceModelName: device.model ? device.model.title + ' (' + device.model.device_family_name + ')' : null,
                        serialNumber: device.serialNumber,
                        repairReason: device.repairReason,
                        repairDetails: device.repairDetails,
                    })).filter((d, i, arr) => arr.findIndex(x => x.modelId == d.modelId && x.serialNumber == d.serialNumber) == i),
                    isAddressEdit: this.isAddressEdit,
                    shippingOption: this.shippingOption ?? null,
                    streetAddress: this.streetAddress,
                    addressLine1: this.addressLine1,
                    country: this.country.code,
                    state: this.state.abbreviation,
                    city: this.city.province,
                    zipCode: this.zipCode,
                };
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/repair-request/store`, payload);

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Redirect to the track step */
                    setTimeout(() => {
                        window.location.href = `${this.$userAppUrl}smarttiusadmin/track-repairs`;
                    }, 3000);
                } else if (response.data.errors) {
                    this.disabledButton = false;
                    this.validationErrors = response.data.errors;
                    /* Update the step number based on the error response */
                    if (response.data.errors.streetAddress || response.data.errors.addressLine1 || response.data.errors.country || response.data.errors.state || response.data.errors.city || response.data.errors.zipCode) {
                        this.currentStep = 2;
                    } else {
                        /* Handle other server-side errors */
                        this.currentStep = 1;
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
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },
    }
};
</script>