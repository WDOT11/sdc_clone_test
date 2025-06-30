<template>
    <div class="modal fade" id="create_modal">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content onewhitebg ">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title def_20_size d-flex gap-10 mb-0  align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                            width="30" height="30" x="0" y="0" viewBox="0 0 461.103 461.103"
                            style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                            <g>
                                <path
                                    d="M325.949 350.032a13.84 13.84 0 0 0-12.749-12.749c-7.622.581-13.329 7.23-12.749 14.851a13.84 13.84 0 0 0 12.749 12.749c7.622-.58 13.329-7.23 12.749-14.851zM359.709 338.192a13.84 13.84 0 0 0-12.749-12.749c-7.622.581-13.329 7.23-12.749 14.851a13.84 13.84 0 0 0 12.749 12.749c7.622-.58 13.329-7.23 12.749-14.851z"
                                    fill="#262626" opacity="1" data-original="#000000" class=""></path>
                                <path
                                    d="M461.04 127.683v-.64a8.05 8.05 0 0 0-.64-1.28l-.48-.64-.96-1.04-.56-.48-1.04-.48h-.4L283.2 52.083a8.004 8.004 0 0 0-6 0l-173.36 69.84h-.4l-1.04.48-.56.48-.96 1.04-.48.64a8.05 8.05 0 0 0-.64 1.28v.64a7.968 7.968 0 0 0 0 2v64H8a8 8 0 0 0 0 16v1.04h90.8v27.36H67.68a8 8 0 0 0 0 16H98.8v27.36H25.04a8 8 0 0 0 0 16H98.8v33.44a8 8 0 0 0 4.96 7.36l173.44 72a8.005 8.005 0 0 0 2.72.56 8.005 8.005 0 0 0 2.72-.56l173.44-72a8 8 0 0 0 4.96-7.36v-200a7.968 7.968 0 0 0 0-2zm-180.88-59.36 65.76 26.32-149.6 63.28-68.4-28.48 152.24-61.12zm-8.24 321.44-157.12-65.44v-28.16H148a8 8 0 0 0 0-16h-33.2v-27.36h76a8 8 0 0 0 0-16h-76v-27.28h16a8 8 0 0 0 0-16h-16v-52.16l61.12 25.28 96 40v183.12zm8.32-196.64-63.2-26.48 149.76-63.52 65.52 26.4-95.44 40-56.64 23.6zm7.92 196.64v-183.12l45.04-18.72 112-46.72.4 183.12-157.44 65.44z"
                                    fill="#262626" opacity="1" data-original="#000000" class=""></path>
                            </g>
                        </svg>
                        <span>Shipping Supplies Request</span>
                    </h4>
                    <button type="button" class="btn-close" @click="resetForm" id="addShippingSupplyModalClose"
                        data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body onewhitebg steps_wrap">
                    <div class="formobile onewhitebg">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div id="steps-container">
                                <li class="step" v-if="currentStep == 1"><span class="mr-1">Step 1:</span> User Details</li>
                                <li class="step" v-if="currentStep == 2"><span class="mr-1">Step 2:</span> Box Info</li>


                            </div>
                            <span class="steptext">Steps: {{ currentStep }}/{{ 2 }}</span>
                        </div>
                        <div id="step-indicator">
                            <div class="progress" :style="`width: ${(currentStep / 2) * 100}%`"></div>
                        </div>
                    </div>
                    <!--   <div class="mb-1 steps_progress_mainwrap">

                    <div class="steps_wrap">
                                    <div class="formobile bg-white">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div  id="steps-container">
                                            <li class="step" v-if="currentStep == 1"><span>Step 1:</span> User Details</li>
                                            <li class="step" v-if="currentStep == 2"><span>Step 2:</span> Box Info</li>


                    </div> -->
                    <div class="overflow-y-auto main_steps flex-1 pe-2 ">
                        <!-- Step 1: User Details -->
                        <div id="step1" v-if="currentStep == 1" class="space-y-6">
                            <div class="spets_outwrap">
                                <h3 class="text-lg font-semibold  mb-4 flex items-center">
                                    <span
                                        class="bg-dark text-white rounded-full w-6 h-6 flex items-center justify-center mr-2 text-sm">1</span>
                                    User Information
                                </h3>
                                <div class="spets_outwrap_inner">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- First Name -->
                                        <div>
                                            <label for="firstName" class="block text-sm font-medium  mb-1">
                                                First Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" v-model="firstName" id="firstName" class="form-control"
                                                readonly />
                                        </div>
                                        <!-- Last Name -->
                                        <div>
                                            <label for="lastName" class="block text-sm font-medium  mb-1">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" v-model="lastName" id="lastName" class="form-control" readonly />
                                        </div>
                                        <!-- Email -->
                                        <div>
                                            <label for="userEmail" class="block text-sm font-medium  mb-1">
                                                Email <span class="text-red-500">*</span>
                                            </label>
                                            <input type="email" v-model="userEmail" id="userEmail" class="form-control"
                                                readonly />
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <!-- Organization Name -->
                                        <div>
                                            <label for="organization"
                                                class="block text-sm font-medium  mb-1">
                                                Organization <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" v-model="organization" id="organization"
                                                class="form-control" readonly />
                                        </div>
                                        <!-- Sub-Organization Name -->
                                        <div>
                                            <label for="subOrganization" class="block text-sm font-medium  mb-1">Sub-Organization</label>
                                            <input type="text" v-model="subOrganization" id="subOrganization" class="form-control" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="spets_outwrap">
                                <div class="flex justify-between">
                                    <h3 class="text-lg font-semibold  mb-4 flex items-center">
                                        <span
                                            class="bg-dark text-white rounded-full w-6 h-6 flex items-center justify-center mr-2 text-sm">2</span>
                                        Shipping Address
                                    </h3>
                                    <div data-tooltip-type="primarycolor" @change="editShippingAddress"
                                        class="toggleWrapper  d-flex maingape-15 align-items-center">
                                        <span class="fw-bold">Edit</span>
                                        <input type="checkbox" name="toggle1" class="mobileToggle" id="toggle1">
                                        <label for="toggle1"></label>
                                    </div>
                                    <!-- <button type="button" @click="editShippingAddress" class="btn bg-transparent btn-sm">
                                        <img :src="editIc" width="28" height="28">
                                    </button> -->

                                </div>
                                <div class="spets_outwrap_inner">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Street Address -->
                                        <div>
                                            <label for="streetAddress"
                                                class="block text-sm font-medium  mb-1"> Street Address
                                                <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" v-model="streetAddress" placeholder="123 Main St"
                                                id="streetAddress"
                                                :class="['form-control', validationErrors.streetAddress ? 'border-red-300' : 'border-gray-300']"
                                                :readonly="!isAddressEdit" />
                                            <small v-if="validationErrors.streetAddress">
                                                <ErrorMessage :msg="validationErrors.streetAddress[0]"></ErrorMessage>
                                            </small>
                                        </div>
                                        <!-- Address Line 2 -->
                                        <div>
                                            <label for="addressLine2"
                                                class="block text-sm font-medium  mb-1">Address
                                                Line 2</label>
                                            <input type="text" v-model="addressLine2" id="addressLine2"
                                                placeholder="Apt, suite, etc."
                                                :class="['form-control', validationErrors.addressLine2 ? 'border-red-300' : 'border-gray-300']"
                                                :readonly="!isAddressEdit" />
                                            <small v-if="validationErrors.addressLine2">
                                                <ErrorMessage :msg="validationErrors.addressLine2[0]"></ErrorMessage>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <!-- Country -->
                                        <div>
                                            <label for="country" class="block text-sm font-medium  mb-1">
                                                Country <span class="text-red-500">*</span>
                                            </label>
                                            <multiselect id="country" v-model="country" placeholder="Select country"
                                                label="name" track-by="code" :options="countryData" :multiple="false"
                                                :taggable="false" selectLabel="" deselectLabel=""
                                                :class="[validationErrors.country ? 'multiselect--error' : '']"
                                                :disabled="!isAddressEdit">
                                                <template #noResult>
                                                    <span class="custom-message">No countries found. Try another
                                                        search.</span>
                                                </template>
                                                <template #noOptions>
                                                    <span class="custom-message">Start typing to search countries</span>
                                                </template>
                                            </multiselect>
                                            <small v-if="validationErrors.country">
                                                <ErrorMessage :msg="validationErrors.country[0]"></ErrorMessage>
                                            </small>
                                        </div>
                                        <!-- State -->
                                        <div>
                                            <label for="state" class="block text-sm font-medium  mb-1">
                                                State/Province/Region <span class="text-red-500">*</span>
                                            </label>
                                            <multiselect id="state" v-model="state" placeholder="Select state"
                                                label="name" track-by="abbreviation"
                                                :options="country ? statesData.filter((s) => s.country_code == country.code) : []"
                                                :disabled="!country || !isAddressEdit" :multiple="false"
                                                :taggable="false" selectLabel="" deselectLabel=""
                                                :class="[validationErrors.state ? 'multiselect--error' : '']">


                                                <template #noResult>
                                                    <span class="custom-message">No states found. Try another
                                                        search.</span>
                                                </template>
                                                <template #noOptions>
                                                    <span class="custom-message" v-if="!country">Select a country
                                                        first</span>
                                                    <span class="custom-message" v-else>No states available for this
                                                        country</span>
                                                </template>
                                            </multiselect>
                                            <small v-if="validationErrors.state">
                                                <ErrorMessage :msg="validationErrors.state[0]"></ErrorMessage>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <!-- City -->
                                        <div>
                                            <label for="city" class="block text-sm font-medium  mb-1">
                                                City <span class="text-red-500">*</span>
                                            </label>
                                            <multiselect id="city" v-model="city" placeholder="Select city" label="name"
                                                track-by="province"
                                                :options="state ? cityData.filter((c) => c.state_code == state.abbreviation) : []"
                                                :disabled="!state || !isAddressEdit" :multiple="false" :taggable="false"
                                                selectLabel="" deselectLabel=""
                                                :class="[validationErrors.city ? 'multiselect--error' : '']">
                                                <template #noResult>
                                                    <span class="custom-message">No cities found. Try another
                                                        search.</span>
                                                </template>
                                                <template #noOptions>
                                                    <span class="custom-message" v-if="!state">Select a state
                                                        first</span>
                                                    <span class="custom-message" v-else>No cities available for this
                                                        state</span>
                                                </template>
                                            </multiselect>
                                            <small v-if="validationErrors.city">
                                                <ErrorMessage :msg="validationErrors.city[0]"></ErrorMessage>
                                            </small>
                                        </div>
                                        <!-- ZIP Code -->
                                        <div>
                                            <label for="zipCode" class="block text-sm font-medium  mb-1">
                                                ZIP/Postal Code <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" pattern="\d*" placeholder="e.g. 90210" v-model="zipCode"
                                                :class="['form-control', validationErrors.zipCode ? 'border-red-300' : 'border-gray-300']"
                                                :readonly="!isAddressEdit">
                                            <small v-if="validationErrors.zipCode">
                                                <ErrorMessage :msg="validationErrors.zipCode[0]"></ErrorMessage>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Box Info -->
                        <div id="step2" v-if="currentStep == 2" class="spets_outwrap">
                            <div class="  rounded-lg">
                                <h3 class="def_20_size mb-4 flex items-center">

                                    <span
                                        class="bg-dark text-white rounded-full w-6 h-6 flex items-center justify-center mr-2 text-sm">3</span>
                                    Box Info
                                </h3>

                                <div v-for="(box, index) in boxes" :key="index" class=" spets_outwrap device-info">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="def_18_size flex items-center"> Box #{{ index + 1 }}</h4>
                                        <button v-if="boxes.length > 1" @click="removeBox(index)" type="button"
                                            class="btn border del_btns  p-1">
                                            <img :src="deleteIc" width="28" height="28">
                                        </button>
                                    </div>
                                    <div class="spets_outwrap_inner">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Box Type -->
                                            <div>
                                                <label :for="`boxType-${index}`"
                                                    class="block text-sm font-medium  mb-1">
                                                    Box Type <span class="text-red-500">*</span>
                                                </label>
                                                <select :id="`boxType-${index}`" v-model="box.boxType"
                                                    :class="['form-control', validationErrors[`boxData.${index}.boxType`] ? 'border-red-300' : 'border-gray-300']">
                                                    <option value="" disabled selected>Select a box type</option>
                                                    <option v-for="(boxtype, boxIndex) in boxTypes"
                                                        :value="boxtype.name">{{
                                                            boxtype.name }}
                                                    </option>
                                                </select>
                                                <small v-if="validationErrors[`boxData.${index}.boxType`]">
                                                    <ErrorMessage
                                                        :msg="validationErrors[`boxData.${index}.boxType`][0]">
                                                    </ErrorMessage>
                                                </small>
                                            </div>
                                            <!-- Quantity -->
                                            <div>
                                                <label :for="`boxQuantity-${index}`"
                                                    class="block text-sm font-medium  mb-1">Quantity
                                                    <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number" min="1" v-model="box.boxQuantity"
                                                    :id="`boxQuantity-${index}`" placeholder="Enter quantity"
                                                    :class="['form-control', validationErrors[`boxData.${index}.boxQuantity`] ? 'border-red-300' : 'border-gray-300']" />
                                                <small v-if="validationErrors[`boxData.${index}.boxQuantity`]">
                                                    <ErrorMessage
                                                        :msg="validationErrors[`boxData.${index}.boxQuantity`][0]">
                                                    </ErrorMessage>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" @click="addBoxList"
                                    class="mt-4 btn bg_blue  d-flex align-items-center gap-1 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Add More
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Navigation Buttons -->

                </div>

                <!-- Modal footer -->
                <div class="modal-footer onewhitebg justify-content-between">
                    <div>
                        <button v-if="currentStep == 1" type="button" @click="resetForm"
                            class="btn customm_reset_btn themetextcolor">
                            Reset Form
                        </button>
                        <button v-if="currentStep == 2" @click="prevStep"
                            class="btn customm_reset_btn themetextcolor">
                            Back
                        </button>
                    </div>
                    <button v-if="currentStep == 1" @click="nextStep"
                        class="ml-3 d-flex align-items-center gap-1   text-white btn  bg_blue ">
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button v-if="currentStep == 2" @click="submitForm"
                        class="ml-3 d-flex align-items-center gap-1 btn bg_blue text-white">
                        Submit
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- <div v-if="addModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl max-h-[90vh]  relative animate-fade-in">
            Header with progress indicator

        </div>
    </div> -->
</template>
<script>
import { readonly } from 'vue';

export default {
    props: ['addmodal', 'getshippingsupplydata', 'boxtypes', 'closemodal'],
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,
            currentStep: 1,
            /** validation */
            validationErrors: {},
            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,
            /** Step 1 */
            /** user info */
            firstName: "",
            lastName: "",
            userEmail: "",
            organization: "",
            subOrganization: "",
            userMetaKey: "",
            userMetaValue: "",
            /** Shipping address info */
            streetAddress: "",
            addressLine2: "",
            // country: "",
            country: {
                code: 'US',
                name: 'United States'
            },
            state: "",
            city: "",
            zipCode: "",
            manualAddressChange: false,
            isAddressEdit: 0,
            /** Step 2 */
            boxes: [{
                boxType: "",
                boxQuantity: "",
            }],
            boxTypes: this.boxtypes,
        };
    },

    watch: {

        country(newVal, oldVal) {
            if (this.manualAddressChange && newVal !== oldVal) {
                this.state = null;
                this.city = null;
            }
        },

        state(newVal, oldVal) {
            if (this.manualAddressChange && newVal !== oldVal) {
                this.city = null;
            }
        },

        isAddressEdit(newVal) {
            this.isAddressEdit = newVal;
        }
    },

    created() {
        this.userDetails();
    },

    methods: {

        /** close button */
        closeAddModal() {
            this.resetForm();
            this.closemodal();
        },

        /** Get UserData  */
        async userDetails() {
            show_ajax_loader();
            /** Call a route here to get the address and other data of user */
            try {
                const response = await axios.get(`${this.$userAppUrl}sdcsmuser/shipping-supplies/user-data`);
                if (response.data.success == true) {
                    if (response.data.userDetails) {
                        this.firstName = response.data.userDetails.first_name;
                        this.lastName = response.data.userDetails.last_name;
                        this.userEmail = response.data.userDetails.email;
                        this.organization = response.data.userDetails.orgName;
                        this.subOrganization = response.data.userDetails.subOrgName;
                        this.userMetaKey = response.data.userDetails.userMetaKey;
                        this.userMetaValue = response.data.userDetails.userMetaValue;
                        this.streetAddress = response.data.userDetails.streetAddress;
                        this.addressLine2 = response.data.userDetails.addressLine2;
                        this.country = this.countryData.find(country => country.code == response.data.userDetails.country);
                        this.state = this.statesData.find(s => s.abbreviation == response.data.userDetails.state);
                        this.city = this.cityData.find(c => c.province == response.data.userDetails.city);
                        this.zipCode = response.data.userDetails.zip;
                        this.$nextTick(() => {
                            this.manualAddressChange = true; /* Re-enable after assignment */
                        });
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    }
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
        },

        /** on click add more button (add box list) */
        addBoxList() {
            show_ajax_loader();
            if (this.boxTypes.length > 0 && this.boxes.length > 0) {
                const countBoxes = this.boxes.length;
                if (countBoxes < this.boxTypes.length) {
                    this.boxes.push({ boxType: '', boxQuantity: '' });
                }
                hide_ajax_loader();
            } else {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
            }
        },

        /** On click remove box */
        removeBox(index) {
            show_ajax_loader();
            this.boxes.splice(index, 1);
            hide_ajax_loader();
        },

        /** On click reset button */
        resetForm() {
            show_ajax_loader();
            this.currentStep = 1;
            /** validation */
            this.validationErrors = {};
            /** Step 2 */
            this.boxes = [{
                boxType: "",
                boxQuantity: "",
            }];
            hide_ajax_loader();
        },

        /** On click back button */
        prevStep() {
            show_ajax_loader();
            this.currentStep--;
            hide_ajax_loader();
        },

        /** On clcik Shipping Address Edit Button */
        editShippingAddress() {
            show_ajax_loader();
            // this.isAddressEdit = 1;
            this.isAddressEdit = this.isAddressEdit == 0 ? 1 : 0;
            hide_ajax_loader();
        },

        /** On click next button */
        nextStep() {
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;

            /** Check validations for step 1 */
            if (this.currentStep == 1) {
                const zipCodeRegex = /^[0-9]{5}$/;
                if (!this.streetAddress) {
                    this.validationErrors.streetAddress = ['Please enter the street address.'];
                    validationPassed = false;
                }
                if (!this.country) {
                    this.validationErrors.country = ['Please select country.'];
                    validationPassed = false;
                }
                if (!this.state) {
                    this.validationErrors.state = ['Please select state.'];
                    validationPassed = false;
                }
                if (!this.city) {
                    this.validationErrors.city = ['Please select city.'];
                    validationPassed = false;
                }
                if (!this.zipCode) {
                    this.validationErrors.zipCode = ['Please enter zip code.'];
                    validationPassed = false;
                } else if (!zipCodeRegex.test(this.zipCode)) {
                    this.validationErrors.zipCode = ['Zip code must be a 5-digit number.'];
                    validationPassed = false;
                }

                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
            }
            this.currentStep++;
            hide_ajax_loader();
        },

        /** Handle form submission */
        async submitForm() {
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;
            /** Check the validation for the final(second) step */
            this.boxes.forEach((box, index) => {
                if (!box.boxType) {
                    this.validationErrors[`boxData.${index}.boxType`] = [`Please select box type.`];
                    validationPassed = false;
                }
                if (!box.boxQuantity) {
                    this.validationErrors[`boxData.${index}.boxQuantity`] = [`Please enter box quantity.`];
                    validationPassed = false;
                }
            });

            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                const payload = {
                    /** First step data */
                    isAddressEdit: this.isAddressEdit,
                    streetAddress: this.streetAddress,
                    addressLine2: this.addressLine2,
                    city: this.city.province,
                    state: this.state.abbreviation,
                    zipCode: this.zipCode,
                    country: this.country.code,
                    /** Second step data */
                    boxData: this.boxes.map(box => ({
                        boxType: box.boxType,
                        boxQuantity: box.boxQuantity,
                    })),
                };
                let response = await axios.post(`${this.$userAppUrl}sdcsmuser/shipping-supplies/store`, payload);
                if (response.data.success == true) {
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    this.isCloseBtnClick = true;
                    hide_user_popup('#addShippingSupplyModalClose');
                    this.closeAddModal();
                    /** getShippingSupplyData Function from index */
                    this.getshippingsupplydata();
                    hide_ajax_loader();
                } else if (response.data.errors) {
                    this.validationErrors = response.data.errors;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    hide_user_popup('#addShippingSupplyModalClose');
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "something went wrong, Please try again later.";
                hide_user_popup('#addShippingSupplyModalClose');
            }
        },
    },
};
</script>