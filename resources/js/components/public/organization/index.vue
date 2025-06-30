<template>
    <!-- Success Message Modal  -->
    <div v-if="paymentDone"
        class="fixed success-msg z-3 inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center" tabindex="-1">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-lg font-semibold mb-4">{{ this.paymentMessage }}</h3>
            <!-- <div class="flex justify-end mt-3">
                <button @click="resetForm" class="btn btnblack btn-secondary text-white rounded mr-2">
                    Close
                </button>
                <a :href="login" class="btn bg_blue text-white rounded">
                    Login
                </a>
            </div> -->
        </div>
    </div>
    <!-- Success Message Modal End -->
    <div :class="org.portal_status == 0 ? 'container' : 'container top_pull'">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 mt-3">
                <!-- Hero Header -->

                <div class="steps_wrap" v-if="org.portal_status == 1">
                    <div class="text-center mt-4 mb-3">
                        <h1 class="text-center text-white main_hedaing fw-bold  mb-1">{{ org.name }}</h1>
                        <p class="lead text-white mb-2 text-center">Protect your device with our comprehensive plans</p>
                    </div>

                    <div class="card mb-5 bg-white border-0 mt-4 shadow-lg ">
                        <!-- Step Indicators -->
                        <div class="card-header bg-white text-dark py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <ul class="list-unstyled fw-bold  mb-0" id="steps-container">
                                    <li class="step" v-if="currentStep == 1"><span class="mr-1">Step 1:</span><span
                                            class="text-muted">Purchaser Information</span> </li>
                                    <li class="step" v-if="currentStep == 2"><span class="mr-1">Step 2:</span> <span
                                            class="text-muted">Device Details</span></li>
                                    <li class="step" v-if="currentStep == 3"><span class="mr-1">Step 3:</span><span
                                            class="text-muted">Plan Details</span> </li>
                                    <li class="step" v-if="currentStep == 4"><span class="mr-1">Step 4:</span><span
                                            class="text-muted">Billing Information</span> </li>
                                </ul>
                                <span class="badge bg-black text-white steps_lineH">Step {{ currentStep }} / 4</span>
                            </div>
                        </div>

                        <div class="card-body  ">

                            <!-- Step 1: Purchaser Information -->
                            <div v-if="currentStep == 1" class="">
                                <div class="mb-3 bg-white rounded border  p-3">
                                    <div class="row mb-2">
                                        <!-- First Name -->
                                        <div class="col">
                                            <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input type="text" placeholder="Enter First Name" v-model="firstName" :disabled="isLogin" :class="['form-control', { 'is-invalid': validationErrors.firstName },]">
                                            <small v-if="validationErrors.firstName" class="text-red-500">{{ validationErrors.firstName[0] }}</small>
                                        </div>
                                        <!-- Last Name -->
                                        <div class="col">
                                            <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" placeholder="Enter Last Name" v-model="lastName" :class="['form-control', { 'is-invalid': validationErrors.lastName },]">
                                            <small v-if="validationErrors.lastName" class="text-red-500">{{ validationErrors.lastName[0] }}</small>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <!-- Phone Number -->
                                        <div class="col">
                                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                            <input type="tel" placeholder="Enter phone number" @input="formatPhoneNumberInput" @paste.prevent="handlePaste" v-model="phone" :class="['form-control', { 'is-invalid': validationErrors.phone },]">
                                            <small v-if="validationErrors.phone" class="text-red-500">{{ validationErrors.phone[0] }}</small>
                                        </div>
                                        <!-- Email -->
                                        <div class="col">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" placeholder="Enter email" v-model="email" @blur="handelEmailCheck" :disabled="isLogin" :class="['form-control', { 'is-invalid': validationErrors.email },]">
                                            <small v-if="validationErrors.email" class="text-red-500">{{ validationErrors.email[0] }}</small><span v-if="showLoginButton"> <a class="btn bg-light border  text-dark rounded px-2 py-1 text-decoration-none font-weight-bold mt-1 ml-2" :href="`${$userAppUrl}sdcsmuser/login?public=true&org=${org.org_link}`"> Login.</a></span>
                                        </div>
                                    </div>
                                    <div v-if="isLogin == false" class="row mb-2">
                                        <!-- Password -->
                                        <div class="col">
                                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                            <input type="password" placeholder="Enter Password" v-model="password" :class="['form-control', { 'is-invalid': validationErrors.password },]">
                                            <small v-if="validationErrors.password" class="text-red-500">{{ validationErrors.password[0] }}</small>
                                        </div>
                                        <!-- Confirm Password -->
                                        <div class="col">
                                            <label for="confirmed" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" placeholder="Enter Confirm Password" v-model="confirmed" :class="['form-control', { 'is-invalid': validationErrors.confirmed },]">
                                            <small v-if="validationErrors.confirmed" class="text-red-500">{{ validationErrors.confirmed[0] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Device Details -->
                            <div v-if="currentStep == 2" class="">
                                <div class="row">
                                    <!-- Sub-Organizations -->
                                    <div class="col-md-12 ">
                                        <label class="form-label fw-bold">Sub-Organizations</label>
                                        <select v-model="selectedSubOrganization" :class="['form-control', { 'is-invalid': validationErrors.subOrg }]">
                                            <option value="">Select Sub-Organization</option>
                                            <option v-for="subOrganization in suborgs" :value="subOrganization.id">{{ subOrganization.name }}</option>
                                        </select>
                                        <small v-if="validationErrors.subOrg" class="text-red-500">{{ validationErrors.subOrg[0] }}</small>
                                    </div>
                                </div>
                                <div v-for="(device, index) in devices" :key="index" class="device-info b3 mt-3 ">
                                    <div class="card">
                                        <div
                                            class="card-header d-flex align-items-center justify-content-between gap-1">
                                            <div class="col-md-11">
                                                <h5 class="fw-bold">Device {{ index + 1 }}</h5>
                                            </div>
                                            <div class="col-md-1 d-flex justify-content-end">
                                                <button type="button" class="btn del_btns border  btn-sm" @click="removeDeviceList(index)" v-if="devices.length > 1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" fill-rule="evenodd" class="">
                                                        <g>
                                                            <path fill="#1B65DE"
                                                                d="M9 7H4a1 1 0 0 0 0 2h22a1 1 0 0 0 0-2h-5V5c0-.796-.316-1.559-.879-2.121A2.996 2.996 0 0 0 18 2h-6c-.796 0-1.559.316-2.121.879A2.996 2.996 0 0 0 9 5zm10 0h-8V5a.997.997 0 0 1 1-1h6a.997.997 0 0 1 1 1z"
                                                                opacity="1" data-original="#ff730a" class=""></path>
                                                            <path fill="#989FA7"
                                                                d="M24.719 11H5.281l1.551 16.284A3 3 0 0 0 9.819 30h10.362a3 3 0 0 0 2.987-2.716zm-14.602 5.11.889 8a1 1 0 0 0 1.988-.22l-.889-8a1 1 0 0 0-1.988.22zm7.778-.22-.889 8a1 1 0 0 0 1.988.22l.889-8a1 1 0 0 0-1.988-.22z"
                                                                opacity="1" data-original="#ffa008" class=""></path>
                                                        </g>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Select Device Model -->
                                                <div class="col-md-12 mb-3">
                                                    <label for="deviceModel" class="form-label">Model <span class="text-danger">*</span></label>
                                                    <multiselect id="tagging" v-model="device.model" :searchable="true"
                                                        @search-change="updateSearchModelName"
                                                        @select="handleSelectModel($event, index)"
                                                        @remove="removeModel(index)"
                                                        placeholder="Search or choose device model"
                                                        :custom-label="nameWithfamily" label="title" track-by="id"
                                                        :options="devicemodeldata" :multiple="false" :taggable="false"
                                                        selectLabel="" deselectLabel=""
                                                        :class="['w-full', { 'border-red-500': validationErrors[`devices.${index}.modelId`] }]"
                                                        required :allow-empty="true" :internal-search="false"
                                                        :preserve-search="true">
                                                        <template #noResult>
                                                            <span class="custom-message">No Device Model Found.</span>
                                                        </template>
                                                        <template #noOptions>
                                                            <span class="custom-message">No Device Model Found.</span>
                                                        </template>
                                                    </multiselect>
                                                    <small v-if="validationErrors[`devices.${index}.modelId`]" class="text-red-500">
                                                        {{ validationErrors[`devices.${index}.modelId`][0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <!-- Device Serial number/Asset tag -->
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Serial number/Asset tag <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" placeholder="Serial number/Asset tag" v-model="device.serialNumber" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.serialNumber`] }]">
                                                    <small v-if="validationErrors[`devices.${index}.serialNumber`]" class="text-red-500">{{ validationErrors[`devices.${index}.serialNumber`] }}</small>
                                                </div>
                                                <!-- Device plans -->
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Select Plan <span class="text-danger">*</span></label>
                                                    <select v-model="device.selectedPlan" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.selectedPlan`] }]">
                                                        <option value="">Select Plan</option>
                                                        <option v-for="plan in device.plans" v-if="device.plans.length > 0" :key="plan.id" :value="plan.id">{{ plan.plan_name }}</option>
                                                    </select>
                                                    <small v-if="validationErrors[`devices.${index}.selectedPlan`]" class="text-red-500">{{ validationErrors[`devices.${index}.selectedPlan`][0] }}</small>
                                                </div>
                                            </div>
                                            <div v-if="org.org_type == 1">
                                                <div class="row">
                                                    <!-- Student First Name -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="stFirstName" class="form-label">Student First Name</label>
                                                        <input type="text" placeholder="Enter Student First Name" v-model="device.stFirstName" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.stFirstName`] },]">
                                                        <small v-if="validationErrors[`devices.${index}.stFirstName`]" class="text-red-500">{{ validationErrors[`devices.${index}.stFirstName`][0] }}</small>
                                                    </div>
                                                    <!-- Student Last Name -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="stLastName" class="form-label">Student Last Name</label>
                                                        <input type="text" placeholder="Enter Student Last Name" v-model="device.stLastName" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.stLastName`] },]">
                                                        <small v-if="validationErrors[`devices.${index}.stLastName`]" class="text-red-500">{{ validationErrors[`devices.${index}.stLastName`][0] }}</small>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <!-- Student Grade -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="stGrade" class="form-label">Student Grade</label>
                                                        <input type="text" placeholder="Enter Student Grade" v-model="device.stGrade" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.stGrade`] },]">
                                                        <small v-if="validationErrors[`devices.${index}.stGrade`]" class="text-red-500">{{ validationErrors[`devices.${index}.stGrade`][0] }}</small>
                                                    </div>
                                                    <!-- Student ID -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="stId" class="form-label">Student ID</label>
                                                        <input type="text" placeholder="Enter Student ID" v-model="device.stId" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.stId`] },]">
                                                        <small v-if="validationErrors[`devices.${index}.stId`]" class="text-red-500">{{ validationErrors[`devices.${index}.stId`][0] }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div class="row">
                                                    <!-- Student First Name -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="stFirstName" class="form-label">Owner First Name</label>
                                                        <input type="text" placeholder="Enter Owner First Name" v-model="device.stFirstName" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.stFirstName`] },]">
                                                        <small v-if="validationErrors[`devices.${index}.stFirstName`]" class="text-red-500">{{ validationErrors[`devices.${index}.stFirstName`][0] }}</small>
                                                    </div>
                                                    <!-- Student Last Name -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="stLastName" class="form-label">Owner Last Name</label>
                                                        <input type="text" placeholder="Enter Owner Last Name" v-model="device.stLastName" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.stLastName`] },]">
                                                        <small v-if="validationErrors[`devices.${index}.stLastName`]" class="text-red-500">{{ validationErrors[`devices.${index}.stLastName`][0] }}</small>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <!-- Student's Grade -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="stGrade" class="form-label">Owner's Designation</label>
                                                        <input type="text" placeholder="Enter Owner's Designation" v-model="device.stGrade" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.stGrade`] },]">
                                                        <small v-if="validationErrors[`devices.${index}.stGrade`]" class="text-red-500">{{ validationErrors[`devices.${index}.stGrade`][0] }}</small>
                                                    </div>
                                                    <!-- Student ID -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="stId" class="form-label">Owner's ID</label>
                                                        <input type="text" placeholder="Enter Owner's ID" v-model="device.stId" :class="['form-control', { 'is-invalid': validationErrors[`devices.${index}.stId`] },]">
                                                        <small v-if="validationErrors[`devices.${index}.stId`]" class="text-red-500">{{ validationErrors[`devices.${index}.stId`][0] }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary text-white mt-4 d-flex align-items-center gap-10" @click="addDeviceList">
                                    Add More
                                </button>
                            </div>

                            <!-- Step 3 - Plan Details -->
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
                                                        <th>Expires On</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Use selectedPlan (computed property) instead of devicePlan -->
                                                    <tr v-if="deviceSummery.length > 0"
                                                        v-for="(ds, index) in deviceSummery">
                                                        <td>{{ ds.deviceModelTitle ?? '' }}</td>
                                                        <td>{{ ds.serialNumber }}</td>
                                                        <td>{{ ds.devicePlanData.plan_name }}</td>
                                                        <td>{{ formatDate(ds.devicePlanData.expiration_date) }}</td>
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

                            <!-- Step 4: Billing Information -->
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
                                                    <span>Subtotal Price: </span>
                                                    <span class="fw-bold">{{ this.subTotal ?? 0 }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Total:</span>
                                                    <span class="fw-bold text-primary h5">{{ this.totalPrice ?? 0 }}</span>
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
                                                <div class="row mb-2">
                                                    <!-- Street Address -->
                                                    <div class="col">
                                                        <label for="streetAddress" class="form-label">Street Address <span class="text-danger">*</span></label>
                                                        <input type="text" placeholder="Street Address" v-model="streetAddress" :class="['form-control', { 'is-invalid': validationErrors.streetAddress },]">
                                                        <small v-if="validationErrors.streetAddress" class="text-red-500">{{ validationErrors.streetAddress[0] }}</small>
                                                    </div>
                                                    <!-- Address Line 2 -->
                                                    <div class="col">
                                                        <label for="addressLine2" class="form-label">Address Line 2</label>
                                                        <input type="text" placeholder="Address Line 2" v-model="addressLine2" :class="['form-control', { 'is-invalid': validationErrors.addressLine2 },]">
                                                        <small v-if="validationErrors.addressLine2" class="text-red-500">{{ validationErrors.addressLine2[0] }}</small>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <!-- Country -->
                                                    <div class="col">
                                                        <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                                        <multiselect id="tagging" v-model="country" placeholder="Search or select the country" label="name" track-by="code" :options="countryData" :multiple="false" :disabled="true" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.country },]" required>
                                                            <template #noResult>
                                                                <span class="custom-message">No Country Found.</span>
                                                            </template>
                                                            <template #noOptions>
                                                                <span class="custom-message">No Country Found.</span>
                                                            </template>
                                                        </multiselect>
                                                        <small v-if="validationErrors.country" class="text-red-500">{{ validationErrors.country[0] }}</small>
                                                    </div>
                                                    <!-- State -->
                                                    <div class="col">
                                                        <label for="state" class="form-label">State / Province / Region <span class="text-danger">*</span></label>
                                                        <multiselect id="tagging" v-model="state" placeholder="Search or select the state" label="name" track-by="abbreviation" :options="country ? statesData.filter((s) => s.country_code == country.code) : []" :disabled="!country" :multiple="false" :taggable="false" @select="city = null" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.state },]" required>
                                                            <template #noResult>
                                                                <span class="custom-message">No State/Region Found.</span>
                                                            </template>
                                                            <template #noOptions>
                                                                <span class="custom-message">No State/Region Found.</span>
                                                            </template>
                                                        </multiselect>
                                                        <small v-if="validationErrors.state" class="text-red-500">{{ validationErrors.state[0] }}</small>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <!-- City -->
                                                    <div class="col">
                                                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                                        <multiselect id="tagging" v-model="city" placeholder="Search or select the city" label="name" track-by="province" :options="state ? cityData.filter((c) => c.state_code == state.abbreviation) : []" :disabled="!state" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.city },]" required>
                                                            <template #noResult>
                                                                <span class="custom-message">No City Found.</span>
                                                            </template>
                                                            <template #noOptions>
                                                                <span class="custom-message">No City Found.</span>
                                                            </template>
                                                        </multiselect>
                                                        <small v-if="validationErrors.city" class="text-red-500">{{ validationErrors.city[0] }}</small>
                                                    </div>
                                                    <!-- ZIP Code -->
                                                    <div class="col">
                                                        <label for="zipCode" class="form-label">ZIP / Postal Code <span class="text-danger">*</span></label>
                                                        <input type="text" placeholder="Enter ZIP code" v-model="zipCode" :class="['form-control', { 'is-invalid': validationErrors.zipCode },]">
                                                        <small v-if="validationErrors.zipCode" class="text-red-500">{{ validationErrors.zipCode[0] }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Details -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card def_16_size">
                                            <div class="card-header">
                                             <h6 class=" my-0"><i class="bi bi-credit-card-2-front me-2"></i>Credit Card</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-2">
                                                    <!-- Card Details -->
                                                    <div class="col">
                                                        <label for="creditCardNumber" class="form-label">Credit Card <span class="text-danger">*</span></label>
                                                        <div id="card-element" class="form-control"></div>
                                                    </div>
                                                    <!-- Cardholder Name -->
                                                    <div class="col">
                                                        <label for="cardHolderName" class="form-label">Cardholder Name <span class="text-danger">*</span></label>
                                                        <input type="text" placeholder="Cardholder Name" v-model="cardHolderName" :class="['form-control', { 'is-invalid': validationErrors.cardHolderName },]">
                                                        <small v-if="validationErrors.cardHolderName" class="text-red-500">{{ validationErrors.cardHolderName[0] }}</small>
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
                                            <label class="form-check-label" for="membershipAgreement"> I have read and agree to the SmartTech Membership Agreement. <span class="text-danger">*</span></label>
                                        </div>
                                        <small v-if="validationErrors.membershipAgreement" class="text-red-500 d-block ml-5">{{ validationErrors.membershipAgreement[0] }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button v-if="currentStep == 2 || currentStep == 3 || currentStep == 4" class="btn btn-dark" @click="prevStep" :disabled="disabledButton && currentStep == 4">Previous</button>
                                <button v-if="currentStep == 1 || currentStep == 2 || currentStep == 3" class="btn btn-primary" @click="nextStep" :disabled="showLoginButton">Next</button>
                                <button v-if="currentStep == 4" class="btn bg_blue text-white" @click="nextStep" :disabled="disabledButton && currentStep == 4">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    {{ org.close_portal_message }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        org: {
            type: Object
        },
        suborgs: {
            type: Object
        },
        stripekey: {
            type: String
        }
    },
    data() {
        return {
            steps: [
                { label: "User Details" },
                { label: "Device Details" },
                { label: "Plan Details" },
                { label: "Shipping Details" },
            ],

            /** To manage the steps of the form */
            currentStep: 1,

            /** To manage the form data */
            /** To contain the first step data */
            showLoginButton: false,
            firstName: '',
            lastName: '',
            phone: '',
            email: '',
            password: '',
            confirmed: '',
            isLogin: this.$authUser ? true : false, /* Check if user is logged in */
            /** To contain the second step data */
            numberOfDevices: 1,
            selectedSubOrganization: '',
            devices: [{
                model: null,
                serialNumber: '',
                selectedPlan: '',
                plans: [],
                stFirstName: '',
                stLastName: '',
                stGrade: '',
                stId: '',
            }],

            /** To store the device model data by searching */
            devicemodeldata: [],
            /** Used in searching of models */
            searchModelText: "",

            /** To contain the third step data */
            paymentAmount: 0,
            deviceSummery: [],

            /** To contain the 4th step data */
            subTotal: '',
            totalPrice: '',
            cardHolderName: '',
            streetAddress: '',
            addressLine2: '',
            // country: '',
            country: {
                code: 'US',
                name: 'United States'
            },
            state: '',
            city: '',
            zipCode: '',
            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,
            stripe: null,
            elements: null,
            card: null,

            /** To contain the validation errors */
            validationErrors: {},
            paymentDone: false,
            paymentMessage: '',
            login: null,
            disabledButton: false,
            membershipAgreement: 0,

        };
    },
    created() {
        if (this.$authUser) {
            this.isLogin = true;
            this.firstName = this.$authUser.first_name;
            this.lastName = this.$authUser.last_name;
            this.phone = this.formatPhoneNumber(this.$authUser.phone);
            this.email = this.$authUser.email;
        } else {
            this.isLogin = false;
        }

    },
    watch: {
        currentStep(newStep) {
            if (newStep == 2) {
                this.getDeviceModels(this.searchModelText);
            }
            if (newStep == 4) {
                this.initializeCardElement();
            }
        }
    },
    methods: {

        /** Getting the user address */
        async getUserAddress() {
            const response = await axios.get(`${this.$userAppUrl}sdcsmuser/fileclaims/userdata`);
            if (response.data.success == true) {
                if (response.data.user_Details) {
                    this.streetAddress = response.data.user_Details.street_address;
                    this.addressLine2 = response.data.user_Details.address_line_2;
                    this.city = this.cityData.find(city => city.province == response.data.user_Details.city);
                    this.state = this.statesData.find(state => state.abbreviation == response.data.user_Details.state);
                    this.zipCode = response.data.user_Details.zip;
                    this.country = this.countryData.find(country => country.code == response.data.user_Details.country);
                }
            }
        },

        /** Stripe Card Creation */
        initializeCardElement() {
            this.$nextTick(() => {
                if (!this.stripe) {
                    this.stripe = Stripe(this.stripekey);
                    /* this.stripe = Stripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY); */
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

        /* get device models */
        async getDeviceModels(name = this.searchModelText) {
            show_ajax_loader();
            /* Enter at list 5 characters */
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
                let response = null;
                response = await axios.post(`${this.$userAppUrl}org/get-devices`, {
                    name: name,
                    orgId: this.org.id,
                    serviceProvider: this.org.service_provider_id,
                });
                if (response.data.success && response.data.deviceModels.length > 0) {
                    if (name !== this.searchModelText) {
                        this.devicemodeldata = [];
                        hide_ajax_loader();
                    } else {
                        this.devicemodeldata = response.data.deviceModels;
                        hide_ajax_loader();
                    }
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
                    show_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        /* device model name text */
        updateSearchModelName(value) {
            this.searchModelText = value;
            this.getDeviceModels(value);
        },

        /** device model name with family */
        nameWithfamily(option) {
            const deviceFamilyName = option.device_family_name ? ` (${option.device_family_name})` : '';
            return `${option.title}${deviceFamilyName}`;
        },

        /** On selecting the device model to get the plan data*/
        async getDevicePlan(model, index) {
            show_ajax_loader();
            if (!model?.id) {
                /* this.clearPlans(index); */
                hide_ajax_loader();
                return;
            }

            try {
                const { data } = await axios.post(
                    `${this.$userAppUrl}org/get-plans`,
                    {
                        modelId: model.id,
                        orgId: this.org.id,
                        serviceProvider: this.org.service_provider_id,
                    }
                );
                if (data.success == true) {
                    /* Ensure the row exists before assigning `plans` */
                    if (!this.devices[index]) {
                        this.devices[index] = { plans: [] };
                    }
                    this.devices[index].plans = data.deviceModelPlans || [];
                    hide_ajax_loader();

                } else {
                    /* this.clearPlans(index);*/
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                /* this.clearPlans(index); */
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
            if (this.devices[index] !== undefined) {
                this.devices[index].model = null;
                this.devices[index].plans = [];
                this.devices[index].selectedPlan = "";
            }
            /* this.deviceCoveragePlan = 0; */
        },

        /** To manage the fields for add the multiple device (On Click Add more) */
        addDeviceList() {
            this.devices.push({ model: null, serialNumber: '', selectedPlan: '', plans: [], stFirstName: '', stLastName: '', stGrade: '', stId: '' });
        },

        /** To remove the device fields (Created from the add more) */
        removeDeviceList(index) {
            this.devices.splice(index, 1);
        },

        /** On click of next button */
        async nextStep() {
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;
            let payload = {};

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
                if (!this.email) {
                    this.validationErrors.email = ['Please enter email address.'];
                    validationPassed = false;
                }
                if (!this.email) {
                    this.validationErrors.email = ['Please enter email address.'];
                    validationPassed = false;
                }
                if (!this.isValidEmail(this.email)) {
                    this.validationErrors.email = ['Please enter email valid address.'];
                    validationPassed = false;
                }
                if (!this.password && this.isLogin == false) {
                    this.validationErrors.password = ['Please enter valid password.'];
                    validationPassed = false;
                }
                if (this.password.length < 8 && this.isLogin == false) {
                    this.validationErrors.password = ['Password must be at least 8 characters long.'];
                    validationPassed = false;
                }
                if (this.password && !this.confirmed && this.isLogin == false) {
                    this.validationErrors.confirmed = ['Please confirm password.'];
                    validationPassed = false;
                }

                if (this.password && this.confirmed && this.password !== this.confirmed && this.isLogin == false) {
                    this.validationErrors.confirmed = ['Password and Confirm password should match.'];
                    validationPassed = false;
                }
                /* Stop submission if validation fails */
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
            }
            if (this.currentStep == 2) {
                const serialNumbers = new Set();
                hide_ajax_loader();

                // if (!this.devices.length) {
                //     this.validationErrors.devices = ['Please add at least one device.'];
                //     validationPassed = false;
                // }

                for (let i = 0; i < this.devices.length; i++) {

                    const device = this.devices[i];
                    if (!device.model) {
                        this.validationErrors[`devices.${i}.modelId`] = [`Please select the dvevice model.`];
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
                    if (!device.selectedPlan) {
                        this.validationErrors[`devices.${i}.selectedPlan`] = [`Please select a plan.`];
                        validationPassed = false;
                    }

                }
                if (this.suborgs.length > 0 && !this.selectedSubOrganization) {
                    this.validationErrors.subOrg = ['Please select a sub-organization.'];
                    validationPassed = false;
                }

                /* Stop submission if validation fails */
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }

                this.deviceSummery = this.devices.map(device => ({
                    deviceModelId: device.model ? device.model.id : null,
                    deviceModelTitle: device.model ? device.model.title : null,
                    serialNumber: device.serialNumber || null,
                    // devicePlanName: device.plans.find(device.devicePlan)  || null,
                    devicePlanData: device.plans.find(plan => plan.id == device.selectedPlan) || null,
                }));
                let planTotal = 0;
                this.deviceSummery.forEach((device, i) => {
                    if (device.devicePlanData) {
                        planTotal += parseFloat(device.devicePlanData.price) || 0;

                    }

                });
                this.subTotal = '$' + planTotal.toFixed(2);
                this.totalPrice = '$' + planTotal.toFixed(2);
                this.paymentAmount = planTotal.toFixed(2);

                /** Getting user address data */
                this.getUserAddress();
            }

            if (this.currentStep == 4) {
                hide_ajax_loader();
                const zipCodeRegex = /^[0-9]{5}$/;
                if (!this.cardHolderName) {
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
                /* if (this.zipCode && (this.zipCode.length < 0 || this.zipCode.length > 5)) {
                    this.validationErrors.zipCode = ['Please enter valid zip code.'];
                    validationPassed = false;
                } */
                if (!this.membershipAgreement) {
                    this.validationErrors.membershipAgreement = ['Please accept terms and conditions.'];
                    validationPassed = false;
                }
                /* Stop submission if validation fails */
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
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
                payload = {
                    isLoginUser: this.isLogin,
                    firstName: this.firstName,
                    lastName: this.lastName,
                    phone: this.phone.replace(/\D/g, ''), // Send only digits,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.confirmed,
                    orgId: this.org.id,
                    serviceProvider: this.org.service_provider_id,
                    subOrg: this.selectedSubOrganization,
                    devices: this.devices.map(device => ({
                        modelId: device.model ? device.model.id : null,
                        modelTitle: device.model ? device.model.title : null,
                        serialNumber: device.serialNumber || null,
                        selectedPlan: device.selectedPlan || null,
                        stFirstName: device.stFirstName || null,
                        stLastName: device.stLastName || null,
                        stGrade: device.stGrade || null,
                        stId: device.stId || null,
                    })),
                    amount: this.paymentAmount,
                    stripeToken: stripeToken,
                    paymentMethodId: paymentMethod.id, // Stripe Payment Method ID
                    cardHolderName: this.cardHolderName,
                    streetAddress: this.streetAddress,
                    addressLine2: this.addressLine2,
                    country: this.country.code,
                    state: this.state.abbreviation,
                    city: this.city.province,
                    zipCode: this.zipCode,
                    membershipAgreement: this.membershipAgreement
                };
                const response = await this.submitForm(payload);
                if (!response || !response.data || response.data.success == false) return; // Stop on error
            }
            /** Updating the step number */
            if (this.currentStep < 4) {
                hide_ajax_loader();
                this.currentStep++;
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

        /** For the previous step */
        prevStep() {
            this.currentStep--;
        },

        /** To submit the form */
        async submitForm(payload) {
            show_ajax_loader();
            /** Check validations for the second step */
            this.validationErrors = {};
            try {
                this.disabledButton = true;
                let response = await axios.post(`${this.$userAppUrl}org/store`, payload);
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
                    return response; /* Return the response to be used in nextStep() */
                } else if (response.data.errors) {
                    this.disabledButton = false;
                    this.validationErrors = response.data.errors;
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

        /** To reset the form */
        resetForm() {
            show_ajax_loader();
            this.currentStep = 1;

            /** Reset first step data */
            /* this.firstName = '';
            this.lastName = '';
            this.phone = '';
            this.email = ''; */
            this.password = '';
            this.confirmed = '';

            // Reset second step data
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

            // Reset third step data
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

            // Reset validation errors
            this.validationErrors = {};

            // Reset payment-related data
            this.stripe = null;
            this.elements = null;
            this.card = null;
            this.paymentAmount = 0;
            this.paymentDone = false;
            this.paymentMessage = '';
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
            this.phone = this.formatPhoneNumber(this.phone);
        },

        /**  Paste Time check */
        handlePaste(event) {
            let paste = (event.clipboardData || window.clipboardData).getData('text');
            let digits = paste.replace(/\D/g, '').slice(0, 10);
            this.phone = digits;
            this.$nextTick(() => this.formatPhoneNumber());
        },

        isValidEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        /** To check the email (Existance)*/
        async handelEmailCheck() {
            show_ajax_loader();
            if (!this.isValidEmail(this.email)) {
                this.validationErrors.email = ['Please enter email valid address.'];
                this.showLoginButton = false;
                hide_ajax_loader();
                return;
            }
            let email_id = this.email;
            let emailData = {};
            emailData = {
                'email': email_id
            };
            const response = await axios.post(`${this.$userAppUrl}org/checkEmail`, emailData);
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

        /** Auto Login After Successfully payment */
        async autoLogin() {
            try {
                const payload = {
                    email: this.email,
                    password: this.password
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
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric'});
        },
    }
};
</script>