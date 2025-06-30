<template>
    <div class="container-fluid  mt_12">
        <div class="card onewhitebg rounded border-0 ">
           <div class="card-body">
                <h4 class="coman_main_heading border-bottom pb-2 mb-3">Update Claim</h4>

                <div class="step">
                    <div class="mb-3 p-3 rounded border">
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

                <div class="step device-info border onewhitebg mt-2 card-body rounded">
                    <div class="row">
                        <div class="col-md-11">
                            <h4 class=" def_18_size themetextcolor">Device Details</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-6 mt-3">
                            <label for="deviceModel" class="form-label">Device <span class="text-danger">*</span></label>
                             <input type="text" v-model="deviceModel" :readonly="inputDisable" :class="['form-control', { 'is-invalid': validationErrors.deviceModel }, { 'readonly-field': inputDisable }]">
                            <small v-if="validationErrors.deviceModel"><ErrorMessage :msg="validationErrors.deviceModel[0]"></ErrorMessage></small>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6 mt-3">
                            <label for="deviceModel" class="form-label">Serial Number <span class="text-danger">*</span></label>
                            <input type="text" v-model="serialNumber" :readonly="inputDisable" :class="['form-control', { 'is-invalid': validationErrors.serialNumber }, { 'readonly-field': inputDisable }]">
                            <small v-if="validationErrors.serialNumber" ><ErrorMessage :msg="validationErrors.serialNumber[0]"></ErrorMessage></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-6 mt-3">
                            <label for="claimReason" class="form-label">Claim Reason <span class="text-danger">*</span></label>
                            <select v-model="claimReason" :class="['form-control  ', { 'is-invalid': validationErrors.claimReason }]">
                                <option disabled value="">Select</option>
                                <option v-for="reason in claimReasons" :value="reason.id"> {{ reason.claim_reason_name }} </option>
                            </select>
                            <small v-if="validationErrors.claimReason" ><ErrorMessage :msg="validationErrors.claimReason[0]"></ErrorMessage> </small>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6 mt-3">
                            <label for="deviceModel" class="form-label">Claim Details <span class="text-danger">*</span></label>
                             <input type="text" v-model="claimDetails" :class="['form-control', { 'is-invalid': validationErrors.claimDetails },]">
                            <small v-if="validationErrors.claimDetails" ><ErrorMessage :msg="validationErrors.claimDetails[0]"></ErrorMessage></small>
                        </div>
                    </div>
                </div>

                <div class="step">
                    <!-- Shipping Address -->
                    <div class="mt-3 p-3 rounded border">
                        <div class="toggleWrapper d-flex justify-content-between maingape-15 align-items-center mb-2 border-bottom pb-2">
                            <label class="fw-bold def_20_size"> Shipping Address </label>
                            <div class="d-flex justify-content-between align-items-center" @change="editAddressEnable">
                                <span class="mr-3 fw-bold">Edit Address</span>
                                <input type="checkbox" name="toggle1" class="mobileToggle" id="toggle1" >
                                <label for="toggle1"></label>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="streetAddress" class="form-label">Street Address <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter street address" v-model="streetAddress"
                                    :class="['form-control', { 'is-invalid': validationErrors.streetAddress }, { 'readonly-field': !isAddressEdit }]"
                                    :readonly="!isAddressEdit">
                                <small v-if="validationErrors.streetAddress" ><ErrorMessage :msg="validationErrors.streetAddress[0]"></ErrorMessage></small>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="addressLine2" class="form-label">Address Line 2</label>
                                <input type="text" placeholder="Enter address line 2" v-model="addressLine2"
                                    :class="['form-control', { 'is-invalid': validationErrors.addressLine2 }, { 'readonly-field': !isAddressEdit }]"
                                    :readonly="!isAddressEdit">
                                <small v-if="validationErrors.addressLine2"><ErrorMessage :msg="validationErrors.addressLine2[0]"></ErrorMessage></small>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <multiselect id="country" v-model="country" placeholder="Search or select the country" label="name" track-by="code" :options="countryData" :multiple="false" :taggable="false" @select="state = null" selectLabel="" deselectLabel="" :class="[ { 'is-invalid': validationErrors.country }, { 'disabled-field': !isAddressEdit } ]" required :disabled="!isAddressEdit">
                                    <template #noResult>
                                        <span class="custom-message">No Country Found.</span>
                                    </template>
                                    <template #noOptions>
                                        <span class="custom-message">No Country Found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="validationErrors.country" ><ErrorMessage :msg="validationErrors.country[0]"></ErrorMessage></small>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="state" class="form-label">State / Province / Region <span class="text-danger">*</span></label>
                                <multiselect id="state" v-model="state" placeholder="Search or select the state" label="name" track-by="abbreviation" :options="country ? statesData.filter((s) => s.country_code == country.code) : []" :multiple="false" :taggable="false" @select="city = null" selectLabel="" deselectLabel="" :class="[ { 'is-invalid': validationErrors.state }, { 'disabled-field': !isAddressEdit } ]" required :disabled="!country || !isAddressEdit">
                                    <template #noResult>
                                        <span class="custom-message">No State/Province Found.</span>
                                    </template>
                                    <template #noOptions>
                                        <span class="custom-message">No State/Province Found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="validationErrors.state" ><ErrorMessage :msg="validationErrors.state[0]"></ErrorMessage></small>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <multiselect id="city" v-model="city" placeholder="Search or select the city" label="name" track-by="province" :options="state ? cityData.filter((c) => c.state_code == state.abbreviation) : []" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="[ { 'is-invalid': validationErrors.city }, { 'disabled-field': !isAddressEdit } ]" required :disabled="!state || !isAddressEdit">
                                    <template #noResult>
                                        <span class="custom-message">No City Found.</span>
                                    </template>
                                    <template #noOptions>
                                        <span class="custom-message">No City Found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="validationErrors.city" ><ErrorMessage :msg="validationErrors.city[0]"></ErrorMessage></small>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="zipCode" class="form-label">ZIP / Postal Code <span class="text-danger">*</span></label>
                                <input type="number" min="0" placeholder="Enter ZIP code" v-model="zipCode" :class="['form-control', { 'is-invalid': validationErrors.zipCode }, { 'readonly-field': !isAddressEdit }]" :readonly="!isAddressEdit">
                                <small v-if="validationErrors.zipCode" class="text-red-500"><ErrorMessage :msg="validationErrors.zipCode[0]"></ErrorMessage></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a :href="$userAppUrl + 'smarttiusadmin/track-claims'" class="btn btn-secondary me-2">Cancel</a>
                    <button class="btn bg_blue text-white" @click="submitForm">Submit</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        claimdata: {
            type: Object,
        }
    },
    data() {
        return {
            /** To manage the address edit(enabled or disabled) */
            isAddressEdit: 0,
            inputDisable: 1,

            claim_id : this.claimdata.id,
            device_id: this.claimdata.device_id,
            claimReasons : [],
            firstName: this.claimdata.first_name,
            lastName: this.claimdata.last_name,
            phone: this.claimdata.phone,
            email: this.claimdata.email,

            deviceModel: this.claimdata.device_model_name,
            serialNumber: this.claimdata.serial_number,
            claimReason: this.claimdata.claim_reason_id,
            claimDetails: this.claimdata.claim_details,

            streetAddress: this.claimdata.street_address,
            addressLine2: this.claimdata.address_line_2,
            country: this.claimdata.country,
            state: this.claimdata.state,
            city: this.claimdata.city,
            zipCode: this.claimdata.zip,

            isHodOrDirector: false,

            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,

            /** To contain the validation errors */
            validationErrors : {},
        }
    },
    created() {
        this.city = this.cityData.find(city => city.province === this.claimdata.city);
        this.state = this.statesData.find(state => state.abbreviation == this.claimdata.state);
        this.country = this.countryData.find(country => country.code == this.claimdata.country);

        this.getClaimReasonsbyDeviceID(this.device_id);
        this.formatPhoneNumber();
    },
    methods: {

        /** To enable the edit for the address on click of edit icon */
        editAddressEnable(){
            this.isAddressEdit = this.isAddressEdit == 0 ? 1 : 0;
        },

        /** Get claim reason by device ids */
        async getClaimReasonsbyDeviceID(device_id){
            show_ajax_loader();
            if (device_id == null) {

                this.claimReasons = [
                    {
                        id: null,
                        reason: "No claim reason found",
                    },
                ];
                hide_ajax_loader();
                return;
            }
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/file-claim/claim-reasons?deviceId=${device_id}`);
                if (response.data.success == true) {
                    if (response.data.claim_reasons) {
                        this.claimReasons = response.data.claim_reasons;
                    } else {
                        this.claimReasons = [
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

        /**  Paste Time check */
        handlePaste(event) {
            let paste = (event.clipboardData || window.clipboardData).getData('text');
            let digits = paste.replace(/\D/g, '').slice(0, 10);
            this.phone = digits;
            this.$nextTick(() => this.formatPhoneNumber());
        },

        /** Format phone number */
        formatPhoneNumber(){
            if(this.phone !== '' && this.phone !== null ){
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

        /** On click of the submit button */
        async submitForm(){
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;
            const zipCodeRegex = /^[0-9]{5}$/;
            if(!this.firstName){
                this.validationErrors.firstName = ['Please enter the first name'];
                validationPassed = false;
            }
            if(!this.lastName){
                this.validationErrors.lastName = ['Please enter the last name'];
                validationPassed = false;
            }
            if(!this.phone){
                this.validationErrors.phone = ['Please enter the phone number'];
                validationPassed = false;
            }
            if(!this.email){
                this.validationErrors.email = ['Please enter the email'];
                validationPassed = false;
            }
            if(!this.claimReason){
                this.validationErrors.claimReason = ['Please select the claim reason'];
                validationPassed = false;
            }
            if(!this.claimDetails){
                this.validationErrors.claimDetails = ['Please enter the claim details'];
                validationPassed = false;
            }
            if(!this.streetAddress){
                this.validationErrors.streetAddress = ['Please enter the street address.'];
                validationPassed = false;
            }
            if(!this.country){
                this.validationErrors.country = ['Please select the country.'];
                validationPassed = false;
            }
            if(!this.state){
                this.validationErrors.state = ['Please select the state.'];
                validationPassed = false;
            }
            if(!this.city){
                this.validationErrors.city = ['Please select the city.'];
                validationPassed = false;
            }
            if(!this.zipCode){
                this.validationErrors.zipCode = ['Please select the zipcode.'];
                validationPassed = false;
            } else if (!zipCodeRegex.test(this.zipCode)) {
                this.validationErrors.zipCode = ['Zip code must be a 5-digit number.'];
                validationPassed = false;
            }
            if(!validationPassed){
                hide_ajax_loader();
                return;
            }

            try {
                this.disabledButton = true;
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/track-claims/update`, {

                    claimID: this.claim_id,
                    firstName: this.firstName,
                    lastName: this.lastName,
                    phone: this.phone,
                    email: this.email,

                    claimReason: this.claimReason,
                    claimDetails: this.claimDetails,

                    streetAddress: this.streetAddress,
                    addressLine2: this.addressLine2,
                    country: this.country,
                    state: this.state,
                    city: this.city,
                    zipCode: this.zipCode,
                });

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin/track-claims`),4000);
                } else if (response.data.errors) {
                    this.disabledButton = false;
                    this.validationErrors = response.data.errors;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error){
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}`),
                        5000
                    );
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