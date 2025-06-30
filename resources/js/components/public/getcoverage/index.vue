<template>
    <!-- Success Message Modal  -->
    <div v-if="paymentDone" class="fixed success-msg z-3 inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center" tabindex="-1">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-lg font-semibold mb-4">{{ this.paymentMessage }}</h3>
            <!-- <div class="flex justify-end mt-3">
                <button @click="resetForm" class="btn btn-secondary text-white  btnblack rounded mr-2">
                    Close
                </button>
                <a :href="login" class="btn bg_blue text-white rounded">
                    Login
                </a>
            </div> -->
        </div>
    </div>
    <!-- Success Message Modal End -->
    <div class="container-fluid  py-5 public_coveragePg card_public_bg2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10">
                    <div class="text-center mt-4 mb-3">
                        <h1 class="text-center main_hedaing fw-bold text-white mb-1">Get Coverage</h1>
                        <p class="lead text-white text-center  mb-2 ">Protect your device with our comprehensive plans</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container top_pull2 steps_wrap ">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 ">
                <!-- Hero Header -->

                <!-- Multi-step form -->
                <div class="card bg-white border-0 mb-5 mt-2 shadow-lg">
                    <div class="card-header bg-white text-dark py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <ul class="list-unstyled mb-0 fw-bold"  id="steps-container">
                                <li class="step" v-if="currentStep == 1"> <span class="mr-1">Step 1:</span><span class="text-muted">Purchaser Details</span></li>
                                <li class="step" v-if="currentStep == 2"><span class="mr-1">Step 2:</span><span class="text-muted">Device Details</span></li>
                                <li class="step" v-if="currentStep == 3"><span class="mr-1">Step 3:</span><span class="text-muted">Plan Details</span></li>
                                <li class="step" v-if="currentStep == 4"><span class="mr-1">Step 4:</span><span class="text-muted">Billing Details</span></li>
                            </ul>
                            <div>
                                <span class="badge bg-black text-white steps_lineH">Step {{ currentStep }} /4</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body  ">
                        <!-- First step - User Details -->
                        <div class="bg-white rounded border p-3" v-if="currentStep == 1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="purchaseFirstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" :class="['form-control', { 'is-invalid': validationErrors.firstName }]" :disabled="isLogin" id="purchaseFirstName" v-model="userData.firstName" placeholder="Enter your first name" required>
                                    <small v-if="validationErrors.firstName" class="text-red-500">{{ validationErrors.firstName[0] }}</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="purchaserLastName" class="form-label"> Last Name <span class="text-danger">*</span></label>
                                    <input type="text" :class="['form-control', { 'is-invalid': validationErrors.lastName }]" id="purchaserLastName" v-model="userData.lastName" placeholder="Enter your last name" required>
                                    <small v-if="validationErrors.lastName" class="text-red-500">{{ validationErrors.lastName[0] }}</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" :class="['form-control', { 'is-invalid': validationErrors.phone }]" id="phone" v-model="userData.phone" @input="formatPhoneNumberInput" @paste.prevent="handlePaste" placeholder="Enter phone number" required>
                                    <small v-if="validationErrors.phone" class="text-red-500">{{ validationErrors.phone[0] }}</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" :class="['form-control', { 'is-invalid': validationErrors.email }]" @blur="handelEmailCheck" :disabled="isLogin" id="email" v-model="userData.email" placeholder="Enter email address" required>
                                    <small v-if="validationErrors.email" class="text-red-500 ">{{ validationErrors.email[0] }}</small><span v-if="showLoginButton"> <a class="btn bg-light border  text-dark rounded px-2 py-1 text-decoration-none font-weight-bold mt-1 ml-2" :href="`${$userAppUrl}sdcsmuser/login?public=true&coverage=get-coverage`">Login.</a></span>
                                </div>
                            </div>
                            <div class="row" v-if="isLogin == false">
                                <div class="col-md-6 mb-3">
                                    <label for="currentPassword" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" :class="['form-control', { 'is-invalid': validationErrors.password }]" id="currentPassword" v-model="userData.password" placeholder="Enter Password"  required>
                                    <small v-if="validationErrors.password" class="text-red-500">{{ validationErrors.password[0] }}</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" :class="['form-control', { 'is-invalid': validationErrors.confirmPassword }]" id="confirmPassword" v-model="userData.confirmPassword" placeholder="Enter Confirm Password" required>
                                    <small v-if="validationErrors.confirmPassword" class="text-red-500">{{ validationErrors.confirmPassword[0] }}</small>
                                    <small v-if="validationErrors.authError" class="text-red-500">{{ validationErrors.authError[0] }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Second step - Device Details -->
                        <div  v-if="currentStep == 2">
                            <div v-for="(device, index) in devices">
                                <div class="device-div device-info  mb-5">
                                    <div class="card">
                                        <div class="card-header d-flex align-items-center justify-content-between gap-1">
                                            <div class="col-md-11">
                                                <h5 class="fw-bold">Device {{ index + 1 }}</h5>
                                            </div>
                                            <div class="col-md-1 d-flex justify-content-end">
                                                <button type="button" class="btn del_btns border btn-sm" @click="removeDeviceList(index)" v-if="devices.length > 1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" fill-rule="evenodd" class=""><g><path fill="#1B65DE" d="M9 7H4a1 1 0 0 0 0 2h22a1 1 0 0 0 0-2h-5V5c0-.796-.316-1.559-.879-2.121A2.996 2.996 0 0 0 18 2h-6c-.796 0-1.559.316-2.121.879A2.996 2.996 0 0 0 9 5zm10 0h-8V5a.997.997 0 0 1 1-1h6a.997.997 0 0 1 1 1z" opacity="1" data-original="#ff730a" class=""></path><path fill="#989FA7" d="M24.719 11H5.281l1.551 16.284A3 3 0 0 0 9.819 30h10.362a3 3 0 0 0 2.987-2.716zm-14.602 5.11.889 8a1 1 0 0 0 1.988-.22l-.889-8a1 1 0 0 0-1.988.22zm7.778-.22-.889 8a1 1 0 0 0 1.988.22l.889-8a1 1 0 0 0-1.988-.22z" opacity="1" data-original="#ffa008" class=""></path></g></svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Device Model <span class="text-danger">*</span></label>
                                                    <multiselect id="tagging" v-model="device.deviceModelId" :searchable="true" @select="handleSelectModel($event, index)" @remove="removeModel(index)" placeholder="Search or choose device model" :custom-label="nameWithfamily" label="title" track-by="id" :options="devicemodeldata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['w-full', { 'border-red-500': validationErrors[`devices.${index}.deviceModelId`] }]" required :allow-empty="true" :preserve-search="true">
                                                        <template #noOptions>
                                                            <span class="custom-message">No device model found.</span>
                                                        </template>
                                                        <template #noResult>
                                                            <div class="custom-message">No device model found.</div>
                                                        </template>>
                                                    </multiselect>
                                                    <small v-if="validationErrors[`devices.${index}.deviceModelId`]" class="text-red-500">{{ validationErrors[`devices.${index}.deviceModelId`][0] }}</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="deviceSerialNumber" class="form-label">Serial Number <span class="text-danger">*</span></label>
                                                    <input type="text" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.serialNumber`] }]" id="deviceSerialNumber" v-model="device.serialNumber" placeholder="Enter Serial Number" required>
                                                    <small v-if="validationErrors[`devices.${index}.serialNumber`]" class="text-red-500">{{ validationErrors[`devices.${index}.serialNumber`] }}</small>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="deviceCellularService" class="form-label">Cellular Service <span class="text-danger">*</span></label>
                                                    <select name="cellularService" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.cellularService`] }]" v-model="device.cellularService">
                                                        <option value="0">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                    <small v-if="validationErrors[`devices.${index}.cellularService`]" class="text-red-500">{{ validationErrors[`devices.${index}.cellularService`][0] }}</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="deviceCarrier" class="form-label">Carrier</label>
                                                    <select name="carrier" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.carrier`] }]" v-model="device.carrier">
                                                        <option value="" selected="selected">SELECT</option>
                                                        <option v-for="(carrier, index) in deviceCarrier" :value="carrier">{{ carrier }}</option>
                                                    </select>
                                                    <small v-if="validationErrors[`devices.${index}.carrier`]" class="text-red-500">{{ validationErrors[`devices.${index}.carrier`][0] }}</small>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="deviceImei" class="form-label">IMEI</label>
                                                    <input type="text" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.imei`] }]" id="imei" v-model="device.imei" placeholder="Enter IMEI Number">
                                                    <small v-if="validationErrors[`devices.${index}.imei`]" class="text-red-500">{{ validationErrors[`devices.${index}.imei`][0] }}</small>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="deviceCapacity" class="form-label">Capacity</label>
                                                    <select name="capacity" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.capacity`] }]" v-model="device.capacity">
                                                        <option value="" selected="selected">SELECT</option>
                                                        <option v-for="(capacity, index) in deviceCapacity" :value="capacity">{{ capacity }}</option>
                                                    </select>
                                                    <small v-if="validationErrors[`devices.${index}.capacity`]" class="text-red-500">{{ validationErrors[`devices.${index}.capacity`][0] }}</small>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="deviceColor" class="form-label">Color</label>
                                                    <input type="text" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.color`] }]" id="deviceColor" v-model="device.color" placeholder="Enter device color">
                                                    <small v-if="validationErrors[`devices.${index}.color`]" class="text-red-500">{{ validationErrors[`devices.${index}.color`][0] }}</small>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="devicePlan" class="form-label">Select Plan <span class="text-danger">*</span></label>
                                                    <select name="plan" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.devicePlan`] }]" v-model="device.devicePlan">
                                                        <option value="">Select</option>
                                                        <option v-for="plan in device.plans" :value="plan.id">{{ plan.plan_name }}</option>
                                                    </select>
                                                    <small v-if="validationErrors[`devices.${index}.devicePlan`]" class="text-red-500">{{ validationErrors[`devices.${index}.devicePlan`][0] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                            </div>
                            <button type="button" class="btn btn-primary text-white mt-4 d-flex align-items-center gap-10" @click="addDeviceList">Add More</button>
                        </div>

                        <!-- Third step - Plan Details -->
                        <div v-if="currentStep == 3">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Device</th>
                                                    <th>Serial Number</th>
                                                    <th>Plan Name</th>
                                                    <th>Expires In</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Use selectedPlan (computed property) instead of devicePlan -->
                                                <tr v-if="deviceSummery.length > 0" v-for="(ds, index) in deviceSummery">
                                                    <td>{{ ds.deviceModelTitle ?? '' }}</td>
                                                    <td>{{ ds.serialNumber }}</td>
                                                    <td>{{ ds.devicePlanData.plan_name }}</td>
                                                    <td>{{ ds.devicePlanData.expiration_days }} days</td>
                                                    <td>${{ ds.devicePlanData.price }}</td>
                                                </tr>
                                                <!-- Show message if no plan selected -->
                                                <tr v-else>
                                                    <td colspan="6" class="text-center text-muted">
                                                        No device selected for coverage
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4">Total</td>
                                                    <td colspan="1">${{ paymentAmount }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fourth step - Billing Details -->
                        <div v-if="currentStep == 4">

                            <!-- Pricing Summary -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card def_16_size">
                                         <div class="card-header">
                                            <h6 class="  my-0 ">Order Summary</h6>
                                         </div>
                                        <div class="card-body">
                                            <!-- Need to manage the total from the plan data which is selected -->

                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Sub Total:</span>
                                                <span class="fw-bold">${{ paymentAmount }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Total:</span>
                                                <span class="fw-bold text-primary h5">${{ paymentAmount }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Billing Address -->
                            <div class="row mb-4">
                                <div class="col-md-12">

                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class=" my-0"><i class="bi bi-house-door me-2"></i>Billing Address</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row def_16_size">
                                                <div class="col-md-12 mb-3">
                                                    <label for="streetAddress" class="form-label ">Street Address <span class="text-danger">*</span></label>
                                                    <input type="text" v-model="BillingData.streetAddress" :class="['form-control', { 'is-invalid': validationErrors.streetAddress }]" id="streetAddress" placeholder="Enter street address">
                                                    <small v-if="validationErrors.streetAddress" class="text-red-500">{{ validationErrors.streetAddress[0] }}</small>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="addressLine2" class="form-label">Address Line 2 (Optional)</label>
                                                    <input type="text" v-model="BillingData.addressLine" class="form-control" id="addressLine2" placeholder="Apt, suite, unit, etc.">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                                    <multiselect id="tagging" v-model="BillingData.country" placeholder="Search or select the country" label="name" track-by="code" :options="countryData" :multiple="false" :taggable="false" :disabled="true" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.country },]" required>
                                                        <template #noResult>
                                                            <span class="custom-message">No Country Found.</span>
                                                        </template>
                                                        <template #noOptions>
                                                            <span class="custom-message">No Country Found.</span>
                                                        </template>
                                                    </multiselect>
                                                    <small v-if="validationErrors.country" class="text-red-500">{{ validationErrors.country[0] }}</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="state" class="form-label">State/Province <span class="text-danger">*</span></label>
                                                    <multiselect id="tagging" v-model="BillingData.state" placeholder="Search or select the state" label="name" track-by="abbreviation" :options="BillingData.country ? statesData.filter((s) => s.country_code == BillingData.country.code) : []" :disabled="!BillingData.country" :multiple="false" :taggable="false" @select="BillingData.city = null" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.state },]" required>
                                                        <template #noResult>
                                                            <span class="custom-message">No State/Region Found.</span>
                                                        </template>
                                                        <template #noOptions>
                                                            <span class="custom-message">No State/Region Found.</span>
                                                        </template>
                                                    </multiselect>
                                                    <small v-if="validationErrors.state" class="text-red-500">{{ validationErrors.state[0] }}</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                                    <multiselect id="tagging" v-model="BillingData.city" placeholder="Search or select the city" label="name" track-by="province"
                                                        :options="BillingData.state ? cityData.filter((c) => c.state_code == BillingData.state.abbreviation) : []" :disabled="!BillingData.state" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.city },]" required>
                                                        <template #noResult>
                                                            <span class="custom-message">No City Found.</span>
                                                        </template>
                                                        <template #noOptions>
                                                            <span class="custom-message">No City Found.</span>
                                                        </template>
                                                    </multiselect>
                                                    <small v-if="validationErrors.city" class="text-red-500">{{ validationErrors.city[0] }}</small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="zipCode" class="form-label">Zip/Postal Code <span class="text-danger">*</span></label>
                                                    <input type="text" v-model="BillingData.zipCode" :class="['form-control', { 'is-invalid': validationErrors.zipCode }]" id="zipCode" placeholder="Enter the zipcode">
                                                    <small v-if="validationErrors.zipCode" class="text-red-500">{{ validationErrors.zipCode[0] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="row mb-4">
                                <div class="col-md-12">

                                    <div class="card def_16_size">
                                        <div class="card-header">
                                             <h6 class=" my-0"><i class="bi bi-credit-card-2-front me-2"></i>Credit Card</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="creditCardNumber" class="form-label">Credit Card <span class="text-danger">*</span></label>
                                                    <div id="card-element" class="form-control"></div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="cardHolder" class="form-label">Cardholder Name <span class="text-danger">*</span></label>
                                                    <input type="text" v-model="BillingData.cardholderName" :class="['form-control', { 'is-invalid': validationErrors.cardholderName }]" id="cardHolder" placeholder="Enter the cardholder name">
                                                    <small v-if="validationErrors.cardholderName" class="text-red-500">{{ validationErrors.cardholderName[0] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Agreement -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="membershipAgreement" id="membershipAgreement" :true-value="1" :false-value="0">
                                        <label class="form-check-label" for="membershipAgreement"> I have read and agree to the SmartTech Membership Agreement. <span class="text-danger">*</span> </label>
                                    </div>
                                    <small v-if="validationErrors.membershipAgreement" class="text-red-500 d-block ml-5">{{ validationErrors.membershipAgreement[0] }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-dark" @click="prevStep" v-if="currentStep > 1" :disabled="disabledButton && currentStep == 4"> Previous </button>
                            <button class="btn btn-primary" @click="nextStep" v-if="currentStep < 4" :disabled="showLoginButton"> Next </button>
                            <button class="btn bg_blue text-white" @click="submitForm" v-if="currentStep == 4" :disabled="disabledButton && currentStep == 4"> Submit </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        stripekey: {
            type: String
        }
    },
    data() {
        return {
            /** To contain the steps value */
            currentStep: 1,

            /** To contain the user data(purchase data) */
            showLoginButton: false,
            userData: {
                firstName: '',
                lastName: '',
                phone: '',
                email: '',
                password: '',
                confirmPassword: '',
            },
            isLogin: this.$authUser ? true : false,

            /** To contain the details of device */
            deviceData: {
                deviceModel: '',
                cellularService: 0,
                serialNumber: '',
                imei: '',
                carrier: '',
                capacity: '',
                colour: '',
            },

            devices: [{
                deviceModelId: null,
                deviceModelTitle: '',
                cellularService: 0,
                serialNumber: '',
                imei: '',
                carrier: '',
                capacity: '',
                color: '',
                devicePlan: '',
                plans: [],
            }],

            /** Getting data from global variables */
            deviceCarrier: this.$deviceCarrier,
            deviceCapacity: this.$deviceCapacity,

            /** To contain the plan data */
            planData: {
                devicePlan: '',
            },

            stripe: null,
            elements: null,
            card: null,
            paymentAmount: 0,

            deviceSummery: [],

            /** To contain the other details(Billing and Address) */
            BillingData: {
                /** Card details */
                cardholderName: '',
                /** Billing address */
                streetAddress: '',
                addressLine: '',
                city: '',
                state: '',
                zipCode: '',
                // country: '',
                country: {
                    code: 'US',
                    name: 'United States'
                }
            },
            membershipAgreement: 0,

            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,

            /** To contain the search string */
            searchModelText: '',

            /** To store the devices after getting by search */
            devicemodeldata: [],

            /** To store the device plans by selecting the devices */
            devicePlans: [],

            /** To contain the validation errors */
            validationErrors: {},

            paymentDone: false,
            paymentMessage: '',
            login: null,
            disabledButton: false,
        }
    },
    created() {

        if (this.$authUser) {
            this.userData.isLogin = true;
            this.userData.firstName = this.$authUser.first_name;
            this.userData.lastName = this.$authUser.last_name;
            this.userData.phone = this.formatPhoneNumber(this.$authUser.phone);
            this.userData.email = this.$authUser.email;
        } else {
            this.isLogin = false;
        }

        /** Getting device models on pageload */
        this.getDeviceModels();
    },
    watch: {
        currentStep(newStep) {
            // if (newStep == 2) {
            //     this.getDeviceModels(this.searchModelText);
            // }
            if (newStep == 4) {
                this.initializeCardElement();
            }
        },
    },
    methods: {

        /** Stripe Card Creation */
        initializeCardElement() {
            this.$nextTick(() => {
                if (!this.stripe) {
                    this.stripe = Stripe(this.stripekey);
                    // this.stripe = Stripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY);
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
            });
        },

        /** To manage the fields for add the multiple device (On Click Add more) */
        addDeviceList() {
            show_ajax_loader();
            this.devices.push({
                deviceModelId: null,
                deviceModelTitle: '',
                cellularService: 0,
                serialNumber: '',
                imei: '',
                carrier: '',
                capacity: '',
                color: '',
                devicePlan: '',
                plans: [],
            });
            hide_ajax_loader();
        },

        /** To remove the device fields (Created from the add more) */
        removeDeviceList(index) {
            show_ajax_loader();
            this.devices.splice(index, 1);
            hide_ajax_loader();
        },

        /* device model name text */
        // updateSearchModelName(value) {
        //     this.searchModelText = value;
        //     this.getDeviceModels(value);
        // },

        /** device model name with family name */
        nameWithfamily(option) {
            const deviceFamilyName = option.device_family_name ? ` (${option.device_family_name})` : '';
            return `${option.title}${deviceFamilyName}`;
        },

        /* get device models */
        async getDeviceModels() {
            show_ajax_loader();
            try {
                let response = null;
                response = await axios.post(`${this.$userAppUrl}get-coverage/getdevices`);
                if (response.data.success && response.data.modelData.length > 0) {
                    if (response.data.success == true) {
                        this.devicemodeldata = response.data.modelData;
                        hide_ajax_loader();
                    }
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

        /** To get the device plans */
        async getDevicePlan(model, index) {
            show_ajax_loader();
            if (!model?.id) {
                /* this.clearPlans(index); */
                hide_ajax_loader();
                return;
            }
            try {
                const { data } = await axios.post(`${this.$userAppUrl}get-coverage/getplans`,
                    {
                        modelId: model.id,
                    }
                );
                if (data.success == true) {
                    /* Ensure the row exists before assigning `plans` */
                    if (!this.devices[index]) {
                        this.devices[index] = { plans: [] };
                    }
                    this.devices[index].plans = data.planData || [];
                    hide_ajax_loader();

                } else {
                    /* this.clearPlans(index);*/
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
            }
        },

        /** Handle device model selection */
        handleSelectModel(model, index) {
            show_ajax_loader();
            if (model?.id) {
                this.getDevicePlan(model, index);
                hide_ajax_loader();
            } else {
                hide_ajax_loader();
            }
        },

        /** When the device is dis-selected*/
        removeModel(index) {
            show_ajax_loader();
            if (this.devices[index] !== undefined) {
                this.devices[index].deviceModelId = null;
                this.devices[index].plans = [];
                this.devices[index].devicePlan = "";
            }
            hide_ajax_loader();
        },

        /** Format phone Number */
        formatPhoneNumber(phone = this.phone) {
            if (phone !== '' && phone !== null) {
                let cleaned = phone.replace(/[^\d\(\)\-\s]/g, '');
                let digits = cleaned.replace(/\D/g, '');

                if (digits.length > 10) {
                    digits = digits.slice(0, 10);
                }

                if (digits.length > 6) {
                    return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
                } else if (digits.length > 3) {
                    return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                } else if (digits.length > 0) {
                    return `(${digits}`;
                } else {
                    return '';
                }
            }
            return '';
        },

        formatPhoneNumberInput() {
            this.userData.phone = this.formatPhoneNumber(this.userData.phone);
        },

        /**  Paste Time check */
        handlePaste(event) {
            let paste = (event.clipboardData || window.clipboardData).getData('text');
            let digits = paste.replace(/\D/g, '').slice(0, 10);
            this.userData.phone = digits;
            this.$nextTick(() => this.formatPhoneNumber());
        },

        /** Onclick of the next step button */
        async nextStep() {
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;

            /** Check validations for step 1 */
            if (this.currentStep == 1) {

                if (!this.userData.firstName) {
                    this.validationErrors.firstName = ['Please enter the First Name.'];
                    validationPassed = false;
                }
                if (!this.userData.lastName) {
                    this.validationErrors.lastName = ['Please enter the Last Name.'];
                    validationPassed = false;
                }
                if (!this.userData.phone) {
                    this.validationErrors.phone = ['Please enter the Phone Number.'];
                    validationPassed = false;
                }

                if (!this.userData.email) {
                    this.validationErrors.email = ['Please enter the Email address.'];
                    validationPassed = false;
                }
                if (!this.isValidEmail(this.userData.email)) {
                    this.validationErrors.email = ['Please enter valid email address.'];
                    validationPassed = false;
                }

                if (!this.userData.password && this.isLogin == false) {
                    this.validationErrors.password = ['Please enter the Password.'];
                    validationPassed = false;
                }
                if (this.userData.password && !this.userData.confirmPassword && this.isLogin == false) {
                    this.validationErrors.confirmPassword = ['Please enter the Confirm Password.'];
                    validationPassed = false;
                }

                if (this.userData.password && this.userData.confirmPassword && this.isLogin == false) {
                    if (this.userData.password !== this.userData.confirmPassword && this.isLogin == false) {
                        this.validationErrors.authError = ['Password and Confirm Passwords does not matched.'];
                        validationPassed = false;
                    }
                }

                if (this.userData.password.length < 8 && this.isLogin == false) {
                    this.validationErrors.password = ['Please enter at least 8 characters.'];
                    validationPassed = false;
                }

                if (this.userData.confirmPassword.length < 8 && this.isLogin == false) {
                    this.validationErrors.confirmPassword = ['Please enter atleast 8 characters.'];
                    validationPassed = false;
                }


                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
                this.currentStep++;
                hide_ajax_loader();
            }

            /** Check validations for step 2 */
            else if (this.currentStep == 2) {
                const serialNumbers = new Set();
                for (let i = 0; i < this.devices.length; i++) {
                    const device = this.devices[i];

                   if (!device.deviceModelId) {
                        this.validationErrors[`devices.${i}.deviceModelId`] = [`Please select the device model.`];
                        validationPassed = false;
                    }

                    if (!device.serialNumber) {
                        this.validationErrors[`devices.${i}.serialNumber`] = `Please enter serial number.`;
                        validationPassed = false;
                    } else if (serialNumbers.has(device.serialNumber)) {
                        this.validationErrors[`devices.${i}.serialNumber`] = `Serial number must be unique.`;
                        validationPassed = false;
                    } else {
                        serialNumbers.add(device.serialNumber);

                        /** call to server to validate serial */
                        const serialErrors = await this.handelSerialNumberCheck(device, i);
                        if (!serialErrors) {
                            validationPassed = false;
                        }
                    }

                    /** Cellular Service */
                    if (!device.cellularService) {
                        this.validationErrors[`devices.${i}.cellularService`] = [`Please select the cellular service.`];
                        validationPassed = false;
                    }

                    /** Carrier */
                    if (device.cellularService == 1 && !device.carrier) {
                        this.validationErrors[`devices.${i}.carrier`] = [`Please select device carrier.`];
                        validationPassed = false;
                    }

                    /** Plan */
                    if (!device.devicePlan) {
                        this.validationErrors[`devices.${i}.devicePlan`] = [`Please select a plan.`];
                        validationPassed = false;
                    }
                }

                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }

                this.deviceSummery = this.devices.map(device => ({
                    deviceModelId: device.deviceModelId ? device.deviceModelId.id : null,
                    deviceModelTitle: device.deviceModelId ? device.deviceModelId.title : null,
                    serialNumber: device.serialNumber || null,
                    // devicePlanName: device.plans.find(device.devicePlan)  || null,
                    devicePlanData: device.plans.find(plan => plan.id == device.devicePlan) || null,
                }));
                let planTotal = 0;

                this.deviceSummery.forEach((device, i) => {
                    if (device.devicePlanData) {
                        planTotal += parseFloat(device.devicePlanData.price) || 0;

                    }
                });

                this.paymentAmount = planTotal.toFixed(2);

                this.currentStep++;
                hide_ajax_loader();
            }

            else if (this.currentStep == 3) {

                /** Getting the user address if user is logged in*/
                if(this.userData.email){
                    this.getUserAddress();
                }

                this.currentStep++;
                hide_ajax_loader();
            }
        },

        /**Getting the user address */
        async getUserAddress(){
            const response = await axios.get(`${this.$userAppUrl}sdcsmuser/fileclaims/userdata`);
            if(response.data.success == true){
                if(response.data.user_Details){
                    this.BillingData.streetAddress = response.data.user_Details.street_address;
                    this.BillingData.addressLine = response.data.user_Details.address_line_2;
                    this.BillingData.city = this.cityData.find(city => city.province == response.data.user_Details.city);
                    this.BillingData.state = this.statesData.find(state => state.abbreviation == response.data.user_Details.state);
                    this.BillingData.zipCode = response.data.user_Details.zip;
                    this.BillingData.country = this.countryData.find(country => country.code == response.data.user_Details.country);
                }
            }
        },

        /** check the user existance */
        isValidEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        /** To check the email (Existance)*/
        async handelEmailCheck() {
            show_ajax_loader();
            if (!this.isValidEmail(this.userData.email)) {
                this.validationErrors.email = ['Please enter email valid address.'];
                this.showLoginButton = false;
                hide_ajax_loader();
                return;
            }
            let email_id = this.userData.email;
            let emailData = {};
            emailData = {
                'email': email_id
            };
            const response = await axios.post(`${this.$userAppUrl}get-coverage/checkuser`, emailData);
            if (response.data.success == false && response.data.showLoginButton == true) {
                this.validationErrors.email = response.data.errors.email;
                this.showLoginButton = response.data.showLoginButton;
                hide_ajax_loader();
            } else {
                this.validationErrors = {};
                this.showLoginButton = response.data.showLoginButton;
                hide_ajax_loader();
            }
        },

        /** To check the existance of the device serial number */
        async handelSerialNumberCheck(device, index) {
            show_ajax_loader();
            let serialNumber = device.serialNumber;
            let serialData = {};
            serialData = {
                'serialNumber': serialNumber
            };
            const response = await axios.post(`${this.$userAppUrl}get-coverage/checkserialnumber`, serialData);

            if (response.data.success == false) {
                this.validationErrors[`devices.${index}.serialNumber`] = response.data.msg;
                hide_ajax_loader();
                return false;
            } else {
                this.validationErrors = {};
                hide_ajax_loader();
                return true;
            }
        },

        /** On click of previous step button */
        prevStep() {
            show_ajax_loader();
            if (this.currentStep > 1) {
                this.currentStep--;
            }
            hide_ajax_loader();
        },

        /** Handle form submission */
        async submitForm() {
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;

            /** Check the validation for the final(fourth) step */
            if (this.currentStep == 4) {
                const zipCodeRegex = /^[0-9]{5}$/;
                if (!this.BillingData.cardholderName) {
                    this.validationErrors.cardholderName = ['Please enter the cardholder name.'];
                    validationPassed = false;
                }
                if (!this.BillingData.streetAddress) {
                    this.validationErrors.streetAddress = ['Please enter the street address.'];
                    validationPassed = false;
                }

                if (!this.BillingData.city) {
                    this.validationErrors.city = ['Please select the city.'];
                    validationPassed = false;
                }

                if (!this.BillingData.state) {
                    this.validationErrors.state = ['Please select the state.'];
                    validationPassed = false;
                }

                if (!this.BillingData.zipCode) {
                    this.validationErrors.zipCode = ['Please enter the zipcode.'];
                    validationPassed = false;
                } else if (!zipCodeRegex.test(this.BillingData.zipCode)) {
                    this.validationErrors.zipCode = ['Zip code must be a 5-digit number.'];
                    validationPassed = false;
                }

                if (!this.BillingData.country) {
                    this.validationErrors.country = ['Please select the country.'];
                    validationPassed = false;
                }
                if (!this.membershipAgreement) {
                    this.validationErrors.membershipAgreement = ['Please accept terms and conditions.'];
                    validationPassed = false;
                }
            }
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            let formData = {};
            /** Step 1: Create a Payment Method using Stripe **/
            const { paymentMethod, error } = await this.stripe.createPaymentMethod({
                type: "card",
                card: this.card,
                billing_details: {
                    name: this.BillingData.cardholderName,
                    email: this.userData.email,
                    address: {
                        line1: this.streetAddress,
                        city: this.BillingData.city.name,
                        state: this.BillingData.state.abbreviation,
                        country: this.BillingData.country.code,
                        postal_code: this.BillingData.zipCode,
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
            formData = {
                /** Current Step */
                // currentStep: this.currentStep,
                isLoginUser: this.isLoginUser,
                /** First Step Form Data */
                firstName: this.userData.firstName,
                lastName: this.userData.lastName,
                phone: this.userData.phone.replace(/\D/g, ''),
                email: this.userData.email,
                password: this.userData.password,
                password_confirmation: this.userData.confirmPassword,

                /** Second Step Form Data*/
                devices: this.devices.map(device => ({
                    deviceModelId: device.deviceModelId ? device.deviceModelId.id : null,
                    deviceModelTitle: device.deviceModelId ? device.deviceModelId.title : null,
                    serialNumber: device.serialNumber || null,
                    devicePlan: device.devicePlan || null,
                    cellularService: device.cellularService || null,
                    carrier: device.carrier || null,
                    imei: device.imei || null,
                    capacity: device.capacity || null,
                    color: device.color || null,
                })),

                /** Third Step Form Data */
                // devicePlan: this.planData.devicePlan,

                /** Fourth(Final) Step Form Data */
                stripeToken: stripeToken,
                paymentMethodId: paymentMethod.id, // Stripe Payment Method ID
                cardholderName: this.BillingData.cardholderName,
                streetAddress: this.BillingData.streetAddress,
                addressLine2: this.BillingData.addressLine,
                country: this.BillingData.country.code,
                state: this.BillingData.state.abbreviation,
                city: this.BillingData.city.province,
                zipCode: this.BillingData.zipCode,
                membershipAgreement: this.membershipAgreement
            };

            try {
                this.disabledButton = true;
                let response = await axios.post(`${this.$userAppUrl}get-coverage/addcoverage`, formData);
                if (response.data.success == true) {
                    if (this.currentStep == 4) {
                        this.paymentDone = true;
                        this.paymentMessage = response.data.msg;
                        if (this.$authUser) {
                            /* Redirect to dashboard */
                            window.location.href = `${this.$userAppUrl}sdcsmuser/home`;
                        } else {
                            this.autoLogin();
                        }
                        // this.login = response.data.redirectUrl;
                    }
                    hide_ajax_loader();
                } else if (response.data.errors) {
                    this.validationErrors = response.data.errors;
                    this.disabledButton = false;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "something went wrong, Please try again later.";
            }
        },

        /** To reset the form */
        resetForm() {
            show_ajax_loader();
            this.currentStep = 1;

            /** Reset first step data */
           /*  this.firstName = '';
            this.lastName = '';
            this.phone = '';
            this.email = ''; */
            this.password = '';
            this.confirmed = '';

            /* Reset second step data */
            this.numberOfDevices = 1;
            this.selectedSubOrganization = '';
            this.devices = [
                {
                    model: null,
                    serialNumber: '',
                    selectedPlan: '',
                    stFirstName: '',
                    stLastName: '',
                    stGrade: '',
                    stId: '',
                }
            ];
            this.devicemodeldata = [];
            this.searchModelText = "";

            /* Reset third step data */
            this.subTotal = '';
            this.totalPrice = '';
            this.couponCode = '';
            this.cardHolderName = '';
            this.streetAddress = '';
            this.addressLine2 = '';
            this.country = '';
            this.state = '';
            this.city = '';
            this.zipCode = '';

            /* Reset validation errors */
            this.validationErrors = {};

            /* Reset payment-related data */
            this.stripe = null;
            this.elements = null;
            this.card = null;
            this.paymentAmount = 0;
            this.paymentDone = false;
            this.paymentMessage = '';
            hide_ajax_loader();
        },

        /** Auto Login After Successfully payment */
        async autoLogin() {
            try {
                const payload = {
                    email: this.userData.email,
                    password: this.userData.password
                };

                const response = await axios.post(`${this.$userAppUrl}sdcsmuser/login`, payload);

                if (response.data) {
                    /* Redirect to dashboard */
                    window.location.href = `${this.$userAppUrl}sdcsmuser/home`;
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Login failed. Please login manually.';
                }

            } catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Login error. Please try again.';
            }
        },

        /** Toggle Password Visibility */
        togglePassword(inputId, iconId) {
            togglePasswordVisibility(inputId, iconId);
        }
    }
}
</script>