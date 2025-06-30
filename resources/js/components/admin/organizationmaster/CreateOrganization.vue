<template>
    <div class="container-fluid mt_12">
        <div class="card  onewhitebg border-0">
            <div class="card-body steps_wrap">
                <h4 class="mb-3 border-bottom pb-2 coman_main_heading">Create Organization</h4>

                <div class="formobile ">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div type="1" id="steps-container">
                            <li class="step themetextcolor" v-if="currentStep == 1"><span class="mr-1">Step 1:</span>
                                Organization Details</li>
                            <li class="step themetextcolor" v-if="currentStep == 2"><span class="mr-1">Step 2:
                                </span>Related Organizations</li>
                            <li class="step themetextcolor" v-if="currentStep == 3"><span class="mr-1">Step 3:</span>
                                Device Details</li>
                            <li class="step themetextcolor" v-if="currentStep == 4"><span class="mr-1">Step
                                    4:</span>Claim & Status</li>
                            <li class="step themetextcolor" v-if="currentStep == 5"><span class="mr-1">Step
                                    5:</span>Additional Details</li>
                        </div>
                        <span class="steptext">Steps: {{ currentStep }}/{{ steps.length }}</span>
                    </div>
                    <div id="step-indicator">
                        <div class="progress" :style="`width: ${(currentStep / 5) * 100}%`"></div>
                    </div>
                </div>

                <form class=" border px-3 pb-3 pt-0 ">
                    <!-- Step 1 -> Organization Details -->
                    <div v-if="currentStep == 1">
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label class="form-label mt-2">Name <span class="text-danger">*</span></label>
                                <input v-model="organization.name" type="text" placeholder="Enter organization name"
                                    :class="['form-control', { 'is-invalid': insertValidations.org_name, },]"
                                    required />
                                <small v-if="insertValidations.org_name">
                                    <ErrorMessage :msg="insertValidations.org_name[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label mt-2">Organization Logo</label>
                                <input type="file" accept=".jpg, .jpeg, .png, .svg" class="form-control"
                                    @change="handleLogoUpload" />
                                <small v-if="insertValidations.org_logo">
                                    <ErrorMessage :msg="insertValidations.org_logo[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label class="form-label mt-2 ">Cover Image</label>
                                <input type="file" class="form-control" @change="handleCoverImageUpload" />
                                <small v-if="insertValidations.org_cover_image">
                                    <ErrorMessage :msg="insertValidations.org_cover_image[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label mt-2 ">Service Agreement</label>
                                <div class="row mx-1 mt-2">
                                    <div class="form-check col-md-4">
                                        <input type="radio" v-model="organization.service_agreement" id="agreement1"
                                            class="form-check-input" value="pdf" />
                                        <label class="form-check-label themetextcolor" for="agreement1"> Upload PDF
                                        </label>
                                    </div>
                                    <div class="form-check col-md-4">
                                        <input type="radio" v-model="organization.service_agreement" id="agreement2"
                                            class="form-check-input" value="link" />
                                        <label class="form-check-label themetextcolor" for="agreement2"> Upload URL
                                        </label>
                                    </div>
                                    <div class="form-check col-md-4">
                                        <input type="radio" v-model="organization.service_agreement" id="agreement3"
                                            class="form-check-input" value="none" />
                                        <label class="form-check-label themetextcolor" for="agreement3"> None </label>
                                    </div>
                                    <small v-if="insertValidations.org_service_agreement">
                                        <ErrorMessage :msg="insertValidations.org_service_agreement[0]"></ErrorMessage>
                                    </small>
                                </div>
                            </div>
                            <div v-if="organization.service_agreement == 'pdf'" class="col-md-6">
                                <label class="form-label mt-3 mb-1">Service Agreement</label>
                                <input type="file" class="form-control" @change="handleServiceAgreementUpload" />
                                <small v-if="insertValidations.org_agreement_pdf">
                                    <ErrorMessage :msg="insertValidations.org_agreement_pdf[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div v-if="organization.service_agreement == 'link'" class="col-md-6">
                                <label class="form-label mt-3 mb-1">Service Agreement Link</label>
                                <input v-model="organization.agreement_link" type="text" class="form-control" />
                                <small v-if="insertValidations.org_agreement_link">
                                    <ErrorMessage :msg="insertValidations.org_agreement_link[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -> Related Organizations -->
                    <div v-if="currentStep == 2">
                        <div class="row">
                            <div class="col-md-12 mt-3">
                                <label class="form-label mt-3 mb-1">Enable Sub Organization <span
                                        class="text-danger">*</span></label>
                                <select v-model="organization.enable_multiple_sub_org" class="form-control">
                                    <option :value="1">Enable</option>
                                    <option :value="0">Disable</option>
                                </select>
                                <small v-if="insertValidations.enable_multiple_sub_org">
                                    <ErrorMessage :msg="insertValidations.enable_multiple_sub_org[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>

                        <div class="row" v-if="organization.enable_multiple_sub_org == 1">
                            <label class="form-label mt-3 mb-1">Add Sub Organization</label>
                            <div v-for="(sub_org, index) in sub_orgs" :key="index" class="grid grid-cols-2 gap-4 mb-4 ">
                                <div class="form-group">
                                    <input v-model="sub_orgs[index]" type="text" class="form-control" />
                                    <small v-if="insertValidations[`organiations_sub_org.${index}.subOrgName`]">
                                        <ErrorMessage
                                            :msg="insertValidations[`organiations_sub_org.${index}.subOrgName`][0]">
                                        </ErrorMessage>
                                    </small>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger" @click="removeSubOrg(index)"
                                        v-if="sub_orgs.length > 1">X</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn bg_blue text-white mt-2" @click="addSubOrg">
                                    Add More
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -> Device Details -->
                    <div v-if="currentStep == 3">
                        <small v-if="insertValidations.organiation_allowed_devices">
                            <ErrorMessage :msg="insertValidations.organiation_allowed_devices[0]"></ErrorMessage>
                        </small>
                        <div class="org_allowed_devices">
                            <label class="form-label mt-3 mb-1">Add Allowed Models</label>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Device Model Name</th>
                                        <th>Plan Name</th>
                                        <th class="w_def70">Coverage Price</th>
                                        <th class="w_def70">Deductible</th>
                                        <th class="w_def70">Expiration Date</th>
                                        <th style="width:24px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(allowedDevice, index) in allowedDevices" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            <multiselect v-model="allowedDevice.deviceModelId" :options="devicemodels"
                                                :searchable="true" label="title" track-by="id"
                                                placeholder="Search or choose device model" :taggable="false"
                                                selectLabel="" deselectLabel=""
                                                @select="handleSelect($event, false, index)"
                                                @keydown.enter.native="handleEnterKey(false)"
                                                @remove="clearDeviceSelection(false, index)"
                                                :class="[{ 'is-invalid': insertValidations[`organiation_allowed_devices.${index}.deviceModelId`] }]">
                                            </multiselect>
                                            <small
                                                v-if="insertValidations[`organiation_allowed_devices.${index}.deviceModelId`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_devices.${index}.deviceModelId`][0]">
                                                </ErrorMessage>
                                            </small>
                                        </td>
                                        <td>
                                            <multiselect v-model="allowedDevice.planId" :options="allowedDevice.plans"
                                                label="plan_name" track-by="id" placeholder="Select a Device Plan"
                                                :taggable="false" selectLabel="" deselectLabel=""
                                                @select="getDevicePlansData($event.id, 1, index)"
                                                @remove="clearPlans(false, index)"
                                                :class="['', { 'is-invalid': insertValidations[`organiation_allowed_devices${index}.planId`] }]">
                                                <template #noOptions>
                                                    <span class="custom-message">No device plans available.</span>
                                                </template>
                                            </multiselect>
                                            <small
                                                v-if="insertValidations[`organiation_allowed_devices.${index}.planId`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_devices.${index}.planId`][0]">
                                                </ErrorMessage>
                                            </small>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0"
                                                v-model="allowedDevice.coverage_price"
                                                :class="['form-control ', { 'is-invalid': insertValidations[`organiation_allowed_devices.${index}.coverage_price`] }]" />
                                            <small
                                                v-if="insertValidations[`organiation_allowed_devices.${index}.coverage_price`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_devices.${index}.coverage_price`][0]">
                                                </ErrorMessage>
                                            </small>
                                        </td>
                                        <td>
                                            <input v-model="allowedDevice.deductible" type="number" step="any" min="0"
                                                :class="['form-control ', { 'is-invalid': insertValidations[`organiation_allowed_devices.${index}.deductible`] }]" />
                                            <small
                                                v-if="insertValidations[`organiation_allowed_devices.${index}.deductible`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_devices.${index}.deductible`][0]">
                                                </ErrorMessage>
                                            </small>
                                        </td>
                                        <td>
                                            <input v-model="allowedDevice.expiration_date" type="date"
                                                :class="['form-control ', { 'is-invalid': insertValidations[`organiation_allowed_devices.${index}.expiration_date`] }]" />
                                            <small
                                                v-if="insertValidations[`organiation_allowed_devices.${index}.expiration_date`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_devices.${index}.expiration_date`][0]">
                                                </ErrorMessage>
                                            </small>

                                        </td>
                                        <td>
                                            <button type="button" class="btn  p-1 border"
                                                @click="removeAllowedDevice(index)"
                                                v-if="allowedDevices.length > 1"><svg xmlns="http://www.w3.org/2000/svg"
                                                    version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                                                    height="24" x="0" y="0" viewBox="0 0 32 32" xml:space="preserve"
                                                    fill-rule="evenodd" class="">
                                                    <g>
                                                        <path fill="#1B65DE"
                                                            d="M9 7H4a1 1 0 0 0 0 2h22a1 1 0 0 0 0-2h-5V5c0-.796-.316-1.559-.879-2.121A2.996 2.996 0 0 0 18 2h-6c-.796 0-1.559.316-2.121.879A2.996 2.996 0 0 0 9 5zm10 0h-8V5a.997.997 0 0 1 1-1h6a.997.997 0 0 1 1 1z"
                                                            opacity="1" data-original="#ff730a" class=""></path>
                                                        <path fill="#989FA7"
                                                            d="M24.719 11H5.281l1.551 16.284A3 3 0 0 0 9.819 30h10.362a3 3 0 0 0 2.987-2.716zm-14.602 5.11.889 8a1 1 0 0 0 1.988-.22l-.889-8a1 1 0 0 0-1.988.22zm7.778-.22-.889 8a1 1 0 0 0 1.988.22l.889-8a1 1 0 0 0-1.988-.22z"
                                                            opacity="1" data-original="#ffa008" class=""></path>
                                                    </g>
                                                </svg></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn bg_blue text-white" @click="addAllowedDevice">
                                Add Device
                            </button>
                        </div>
                        <div class="org_renewal_allowed_devices mt-3">
                            <label class="form-label">Add Renewal Allowed Devices</label>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Device Model Name</th>
                                        <th>Plan Name</th>
                                        <th class="w_def70">Coverage Price</th>
                                        <th class="w_def70">Deductible</th>
                                        <th class="w_def70">Expiration Date</th>
                                        <th style="width:24px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(allowedRenewalDevice, index) in allowedRenewalDevices" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            <multiselect v-model="allowedRenewalDevice.deviceModelId"
                                                :options="devicemodels" :searchable="true" label="title" track-by="id"
                                                placeholder="Search or choose device model" :taggable="false"
                                                selectLabel="" deselectLabel=""
                                                @select="handleSelect($event, true, index)"
                                                @keydown.enter.native="handleEnterKey(true, index)"
                                                @remove="clearDeviceSelection(true, index)"
                                                :class="['', { 'is-invalid': insertValidations[`organiation_allowed_renewal_devices.${index}.deviceModelId`] }]">
                                            </multiselect>
                                            <small
                                                v-if="insertValidations[`organiation_allowed_renewal_devices.${index}.deviceModelId`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_renewal_devices.${index}.deviceModelId`][0]">
                                                </ErrorMessage>
                                            </small>
                                        </td>
                                        <td>
                                            <multiselect v-model="allowedRenewalDevice.planId"
                                                :options="allowedRenewalDevice.plans" label="plan_name" track-by="id"
                                                placeholder="Select a Device Plan" :taggable="false" selectLabel=""
                                                deselectLabel="" @select="getDevicePlansData($event.id, 2, index)"
                                                @remove="clearPlans(true, index)"
                                                :class="['', { 'is-invalid': insertValidations[`organiation_allowed_renewal_devices.${index}.planId`] }]">
                                                <template #noOptions>
                                                    <span class="custom-message">No device plans available.</span>
                                                </template>
                                            </multiselect>
                                            <small
                                                v-if="insertValidations[`organiation_allowed_renewal_devices.${index}.planId`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_renewal_devices.${index}.planId`][0]">
                                                </ErrorMessage>
                                            </small>
                                        </td>
                                        <td>
                                            <input v-model="allowedRenewalDevice.coverage_price" type="number"
                                                step="any" min="0"
                                                :class="['form-control ', { 'is-invalid': insertValidations[`organiation_allowed_renewal_devices.${index}.coverage_price`] }]" />
                                            <small
                                                v-if="insertValidations[`organiation_allowed_renewal_devices.${index}.coverage_price`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_renewal_devices.${index}.coverage_price`][0]">
                                                </ErrorMessage>
                                            </small>
                                        </td>
                                        <td>
                                            <input v-model="allowedRenewalDevice.deductible" type="number" step="any"
                                                min="0"
                                                :class="['form-control ', { 'is-invalid': insertValidations[`organiation_allowed_renewal_devices.${index}.deductible`] }]" />
                                            <small
                                                v-if="insertValidations[`organiation_allowed_renewal_devices.${index}.deductible`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_renewal_devices.${index}.deductible`][0]">
                                                </ErrorMessage>
                                            </small>
                                        </td>
                                        <td>
                                            <input v-model="allowedRenewalDevice.expiration_date" type="date"
                                                :class="['form-control ', { 'is-invalid': insertValidations[`organiation_allowed_renewal_devices.${index}.expiration_date`] }]" />
                                            <small
                                                v-if="insertValidations[`organiation_allowed_renewal_devices.${index}.expiration_date`]">
                                                <ErrorMessage
                                                    :msg="insertValidations[`organiation_allowed_renewal_devices.${index}.expiration_date`][0]">
                                                </ErrorMessage>
                                            </small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn p-1 border"
                                                @click="removeAllowedRenewalDevice(index)"
                                                style="width: 32px; height: 32px"
                                                v-if="allowedRenewalDevices.length > 1">
                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24"
                                                    x="0" y="0" viewBox="0 0 32 32" xml:space="preserve"
                                                    fill-rule="evenodd" class="">
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
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn bg_blue text-white" @click="addAllowedRenewalDevice">
                                Add Device
                            </button>
                        </div>
                    </div>

                    <!-- Step 4 -> Claim Reasons and Portal Status -->
                    <div v-if="currentStep == 4">
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Claim reasons <span class="text-danger">*</span></label>
                                <multiselect id="tagging" v-model="claimReasons" placeholder="Search or add reason"
                                    label="claim_reason_name" track-by="id" :options="claimreasons" :multiple="true"
                                    :taggable="false" selectLabel="" deselectLabel=""
                                    :class="['', { 'is-invalid': insertValidations.org_claim_reasons }]">
                                    <template #noResult>
                                        <span class="custom-message">No Claim Reason Found.</span>
                                    </template>
                                    <template #noOptions>
                                        <span class="custom-message">No Claim Reason Found.</span>
                                    </template>
                                </multiselect>
                                <small v-if="insertValidations.org_claim_reasons">
                                    <ErrorMessage :msg="insertValidations.org_claim_reasons[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Organization Type <span class="text-danger">*</span></label>
                                <select v-model="organization.org_type"
                                    :class="['form-control', { 'is-invalid': insertValidations.org_type }]">
                                    <option :value="1">School</option>
                                    <option :value="2">Non School</option>
                                </select>
                                <small v-if="insertValidations.org_type">
                                    <ErrorMessage :msg="insertValidations.org_type[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Portal Status <span class="text-danger">*</span></label>
                                <select v-model="organization.portal_status"
                                    :class="['form-control', { 'is-invalid': insertValidations.org_portal_status }]">
                                    <option :value="1">Open</option>
                                    <option :value="0">Closed</option>
                                </select>
                                <small v-if="insertValidations.org_portal_status">
                                    <ErrorMessage :msg="insertValidations.org_portal_status[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div v-if="organization.portal_status == 0" class="col-md-6">
                                <label class="form-label">Close Portal Message <span
                                        class="text-danger">*</span></label>
                                <textarea v-model="organization.close_portal_message"
                                    :class="['form-control', { 'is-invalid': insertValidations.org_closed_portal_msg }]"></textarea>
                                <small v-if="insertValidations.org_closed_portal_msg">
                                    <ErrorMessage :msg="insertValidations.org_closed_portal_msg[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5 -> Additional Details -->
                    <div v-if="currentStep == 5">
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Repair Enabled <span class="text-danger">*</span></label>
                                <select v-model="organization.repair_enabled"
                                    :class="['form-control', { 'is-invalid': insertValidations.repair_enabled }]">
                                    <option :value="1">Enable</option>
                                    <option :value="0">Disable</option>
                                </select>
                                <small v-if="insertValidations.repair_enabled">
                                    <ErrorMessage :msg="insertValidations.repair_enabled[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Can Edit Devices? <span class="text-danger">*</span></label>
                                <select v-model="organization.can_edit_device"
                                    :class="['form-control', { 'is-invalid': insertValidations.can_edit_device }]">
                                    <option :value="1">Yes</option>
                                    <option :value="0">No</option>
                                </select>
                                <small v-if="insertValidations.can_edit_device">
                                    <ErrorMessage :msg="insertValidations.can_edit_device[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Allow Parents Claim <span class="text-danger">*</span></label>
                                <select v-model="organization.allow_parents_claim"
                                    :class="['form-control', { 'is-invalid': insertValidations.allow_parents_claim }]">
                                    <option :value="1">Allowed</option>
                                    <option :value="0">Not Allowed</option>
                                </select>
                                <small v-if="insertValidations.allow_parents_claim">
                                    <ErrorMessage :msg="insertValidations.allow_parents_claim[0]"></ErrorMessage>
                                </small>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Additional Instructions</label>
                                <textarea v-model="organization.additional_instructions"
                                    :class="['form-control', { 'is-invalid': insertValidations.additional_instructions }]"></textarea>
                                <small v-if="insertValidations.additional_instructions">
                                    <ErrorMessage :msg="insertValidations.additional_instructions[0]"></ErrorMessage>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex mt-3 justify-content-between">
                        <button type="button" class="btn customm_reset_btn me-2" @click="prevStep" :disabled="isDisabledButton && currentStep == steps.length" v-if="currentStep > 1">
                            Previous
                        </button>
                        <a href="/smarttiusadmin/organizations" class="btn customm_reset_btn me-2" v-if="currentStep == 1">
                            Cancel
                        </a>
                        <button type="button" class="btn bg_blue text-white" @click="nextStep" v-if="currentStep < steps.length">
                            Next
                        </button>
                        <button type="button" class="btn bg_blue text-white" v-if="currentStep == steps.length" @click="createOrganizationSubmit" :disabled="isDisabledButton && currentStep == steps.length">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {

    props: {
        claimreasons: {
            type: Object,
        },
    },

    data() {
        return {
            currentStep: 1,
            devicemodels: [],
            steps: [
                { label: "Organization Details" },
                { label: "Related Organizations" },
                { label: "Device Details" },
                { label: "Claim & Status" },
                { label: "Additional Details" },
            ],
            submittedSteps: new Set(), /* Track submitted steps */
            claimReasons: [],
            searchModelText: "",
            org_id: null,
            sub_orgs: [],
            organization: {
                name: "",
                cover_image: null,
                logo: null,
                repair_enabled: 1,
                can_edit_device: 1,
                service_agreement: 'none',
                agreement_pdf: null,
                agreement_link: "",
                allow_parents_claim: 1,
                org_type: 1,
                portal_status: 1,
                close_portal_message: "",
                enable_multiple_sub_org: 0,
                additional_instructions: "",
            },

            allowedDevices: [
                {
                    deviceModelId: "",
                    planId: "",
                    coverage_price: "",
                    deductible: "",
                    expiration_date: "",
                    plans: [], /* Add this to store row-specific plans */
                },
            ],

            allowedRenewalDevices: [
                {
                    deviceModelId: "",
                    planId: "",
                    coverage_price: "",
                    deductible: "",
                    expiration_date: "",
                    plans: [], /* Add this for renewal-specific plans */
                },
            ],

            insertValidations: {}, /* To store the add model validations */
            isSubmitted: false,
            /** Disabled Submit button */
            isDisabledButton: false,
        };
    },

    watch: {
        /* Watch for changes in the organization.enable_multiple_sub_org */
        "organization.enable_multiple_sub_org"(optionValue) {
            if (optionValue == 1 && this.sub_orgs.length == 0) {
                this.sub_orgs.push("");
            }
        },
        /* Watch for changes in the current step */
        currentStep(newStep) {
            if (newStep > 1) {
                this.updateProgress(this.currentStep, this.steps.length);
            }
        },

    },

    created() {
        this.getDeviceModels();
    },

    mounted() {
        this.updateProgress(this.currentStep, this.steps.length);
    },

    methods: {

        /* get device models */
        async getDeviceModels() {
            show_ajax_loader();
            try {
                let response = null;
                const url = `${this.$userAppUrl}smarttiusadmin/devices/org-device-models`;
                response = await axios.get(url);
                if (response.data.success && response.data.deviceModels.length > 0) {
                    if (response.data.success == true) {
                        this.devicemodels = response.data.deviceModels;
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
                this.devicemodels = [
                    {
                        id: null,
                        title: "Something went wrong, please try again later",
                    },
                ];
                hide_ajax_loader();
            }
        },

        /* Organization Logo image */
        handleLogoUpload(event) {
            this.organization.logo = event.target.files[0];
        },

        /* Organization Cover Image */
        handleCoverImageUpload(event) {
            this.organization.cover_image = event.target.files[0];
        },

        /* Service Agreement PDF */
        handleServiceAgreementUpload(event) {
            this.organization.agreement_pdf = event.target.files[0];
        },

        /* Add Sub Organizations  */
        addSubOrg() {
            this.sub_orgs.push("");
        },

        /* Remove Sub Organizations  */
        removeSubOrg(index) {
            this.sub_orgs.splice(index, 1);
        },

        /* Add Allowed Devices  */
        addAllowedDevice() {
            this.allowedDevices.push({
                deviceModelId: "",
                planId: "",
                coverage_price: "",
                deductible: "",
                expiration_date: "",
                plans: [], // Add this to store row-specific plans
            });
        },

        /* Remove Allowed Devices  */
        removeAllowedDevice(index) {
            this.allowedDevices.splice(index, 1);
        },

        /* Add Renewal Devices  */
        addAllowedRenewalDevice() {
            this.allowedRenewalDevices.push({
                deviceModelId: "",
                planId: "",
                coverage_price: "",
                deductible: "",
                expiration_date: "",
                plans: [], // Add this to store row-specific plans
            });
        },

        /* Remove Renewal Devices  */
        removeAllowedRenewalDevice(index) {
            this.allowedRenewalDevices.splice(index, 1);
        },

        /** Fetch device plan based on model and plan type */
        async getDevicePlan(model, planType, index) {
            show_ajax_loader();
            if (!model?.id || !planType) {
                this.clearPlans(planType, index);
                hide_ajax_loader();
                return;
            }

            try {
                const { data } = await axios.post(
                    `${this.$userAppUrl}smarttiusadmin/devices-plan/one-time`,
                    {
                        modelId: model.id,
                        planType,
                    }
                );
                if (data.success) {
                    if (planType == 1) {
                        /* Ensure the row exists before assigning `plans` */
                        if (!this.allowedDevices[index]) {
                            this.allowedDevices[index] = { plans: [] };
                        }
                        this.allowedDevices[index].plans = data.deviceModelPlans || [];
                        hide_ajax_loader();
                    } else if (planType == 2) {
                        if (!this.allowedRenewalDevices[index]) {
                            this.allowedRenewalDevices[index] = { plans: [] };
                        }
                        this.allowedRenewalDevices[index].plans = data.deviceModelPlans || [];
                        hide_ajax_loader();
                    }
                } else {
                    this.clearPlans(planType, index);
                    hide_ajax_loader();
                }
            } catch (error) {
                this.clearPlans(planType, index);
                hide_ajax_loader();
            }
        },

        /** Handle device model selection */
        handleSelect(model, isRenewal = false, index) {
            show_ajax_loader();
            if (model?.id) {
                if (isRenewal) {
                    this.getDevicePlan(model, 2, index);
                    hide_ajax_loader();
                } else {
                    this.getDevicePlan(model, 1, index);
                    hide_ajax_loader();
                }
            } else {
                this.clearDeviceSelection(isRenewal, index);
                hide_ajax_loader();
            }
        },

        /** Handle Enter key press */
        handleEnterKey(isRenewal = false, index) {
            show_ajax_loader();
            if (!this.deviceModelId) {
                this.clearDeviceSelection(isRenewal, index);
                hide_ajax_loader();
                return;
            }
            this.getDevicePlan(
                { id: this.deviceModelId },
                isRenewal ? 2 : 1,
                index
            );
            hide_ajax_loader();
        },

        /** Handle selection from dropdown (Renewal)*/
        handleRenewalSelect(model, index) {
            show_ajax_loader();
            this.deviceModelId = model.id;
            if (model.id) {
                this.getDevicePlan(model, 2, index);
            }
            hide_ajax_loader();
        },

        /** Handle Enter key press (Renewal) */
        handleRenewalEnterKey() {
            show_ajax_loader();
            this.deviceModelId = this.deviceModelId.id;
            if (this.deviceModelId.id) {
                this.getDevicePlan(this.deviceModelId, 2);
            }
            hide_ajax_loader();
        },

        /** Fetch selected plan details */
        async getDevicePlansData(planId, planType, index) {
            if (!planId) {
                return;
            }
            try {
                const { data } = await axios.post(`${this.$userAppUrl}smarttiusadmin/devices-plan/one-time`, { planId, planType });

                if (data.success) {
                    /*  const targetList = planType == 1 ? this.allowedDevices : this.allowedRenewalDevices;
                     targetList[index] = {
                         ...targetList[index],
                         coverage_price: data.deviceModelPlans?.[0]?.price || 0,
                         deductible: data.deviceModelPlans?.[0]?.deductible_price || 0,
                         expiration_days: data.deviceModelPlans?.[0]?.expiration_days || 0,
                     }; */



                    const devicePlan = data.deviceModelPlans?.[0] || {};
                    const targetList = planType == 1 ? this.allowedDevices : this.allowedRenewalDevices;

                    let expirationDate = null;
                    if (devicePlan.expiration_days) {
                        const now = new Date();
                        now.setDate(now.getDate() + devicePlan.expiration_days);
                        expirationDate = now.toISOString().split('T')[0]; // format: YYYY-MM-DD
                    }

                    targetList[index] = {
                        ...targetList[index],
                        coverage_price: devicePlan.price || 0,
                        deductible: devicePlan.deductible_price || 0,
                        expiration_date: expirationDate || 0,
                        // expiration_days: devicePlan.expiration_days || 0,
                    };
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            }
        },

        /** Handle selection from allowed plans dropdown */
        handleAllowedPlansSelect(plan, index) {
            if (plan.id) {
                this.getDevicePlansData(plan.id, 1, index);
            }
        },
        /** Handle selection from Renewal plans dropdown */
        handleRenewalPlansSelect(plan, index) {
            if (plan.id) {
                this.getDevicePlansData(plan.id, 2, index);
            }
        },
        /** Clear device selection when deselecting from dropdown */
        clearDeviceSelection(isRenewal = false, index) {
            show_ajax_loader();
            if (isRenewal) {
                if (this.allowedRenewalDevices[index] !== undefined) {
                    this.allowedRenewalDevices[index].deviceModelId = "";
                    this.allowedRenewalDevices[index].plans = [];
                    this.allowedRenewalDevices[index].planId = "";
                    this.allowedRenewalDevices[index].coverage_price = "";
                    this.allowedRenewalDevices[index].deductible = "";
                    this.allowedRenewalDevices[index].expiration_date = "";
                }
                hide_ajax_loader();
            } else {
                if (this.allowedDevices[index] !== undefined) {
                    this.allowedDevices[index].deviceModelId = "";
                    this.allowedDevices[index].plans = [];
                    this.allowedDevices[index].planId = "";
                    this.allowedDevices[index].coverage_price = "";
                    this.allowedDevices[index].deductible = "";
                    this.allowedDevices[index].expiration_date = "";
                }
                hide_ajax_loader();
            }
        },

        /** Clear plans when deselecting model */
        clearPlans(isRenewal = false, index) {
            show_ajax_loader();
            if (isRenewal) {
                if (this.allowedRenewalDevices[index] !== undefined) {
                    this.allowedRenewalDevices[index].coverage_price = "";
                    this.allowedRenewalDevices[index].deductible = "";
                    this.allowedRenewalDevices[index].expiration_date = "";
                }
                hide_ajax_loader();
            } else {
                if (this.allowedDevices[index] !== undefined) {
                    this.allowedDevices[index].coverage_price = "";
                    this.allowedDevices[index].deductible = "";
                    this.allowedDevices[index].expiration_date = "";
                }
                hide_ajax_loader();
            }
        },


        /* For Next Steps */
        async nextStep() {
            show_ajax_loader();
            if (this.isSubmitting) return; /* Prevents multiple clicks creating duplicates */
            this.isSubmitting = true;
            try {
                if (this.currentStep < this.steps.length) {
                    if (!this.submittedSteps.has(this.currentStep) || this.currentStep == 1) {
                        let form_data = {};
                        let validationPassed = true;
                        /* Step 1: Create (only once) or Update Organization */
                        if (this.currentStep == 1) {
                            /* Client Side Validation */
                            this.insertValidations = {};
                            if (!this.organization.name) {
                                this.insertValidations.org_name = ["Organization name can't be empty."];
                                validationPassed = false;
                            }
                            if (this.organization.name.length < 5) {
                                this.insertValidations.org_name = ['Organization name should be at least 5 characters long.'];
                                validationPassed = false;
                            }
                            if (this.organization.service_agreement == 'pdf') {
                                if (!this.organization.agreement_pdf) {
                                    this.insertValidations.org_agreement_pdf = ['Please select a PDF file for service agreement.'];
                                    validationPassed = false;
                                }
                            }
                            if (this.organization.service_agreement == 'link') {
                                if (!this.organization.agreement_link) {
                                    this.insertValidations.org_agreement_link = ['Please enter a valid URL for service agreement link.'];
                                    validationPassed = false;
                                }
                            }
                            /* Stop submission if validation fails */
                            if (!validationPassed) {
                                hide_ajax_loader();
                                return;
                            }

                            form_data = {
                                form_step: this.currentStep,
                                org_name: this.organization.name,
                                org_logo: this.organization.logo,
                                org_cover_image: this.organization.cover_image,
                                org_service_agreement: this.organization.service_agreement,
                                org_agreement_pdf: this.organization.agreement_pdf,
                                org_agreement_link: this.organization.agreement_link,
                            };
                            if (this.org_id) {
                                form_data.org_id = this.org_id;
                            }

                            const response = await this.createOrganization(form_data);

                            // Prevent duplicate creation by storing org_id immediately
                            if (!this.org_id && response?.data?.org_id) {
                                this.org_id = response.data.org_id;
                            }

                            if (!response || !response.data || response.data.success == false) return; // Stop on error
                        }

                        else if (this.currentStep == 2) {
                            this.insertValidations = {};
                            form_data = {
                                'form_step': this.currentStep,
                                'enable_multiple_sub_org': this.organization.enable_multiple_sub_org,
                                'org_id': this.org_id, /* Use stored organization ID */
                                'organiations_sub_org': []
                            };

                            /* Client-side validation */
                            if (form_data.enable_multiple_sub_org != 0 && form_data.enable_multiple_sub_org != 1) {
                                this.insertValidations.enable_multiple_sub_org = ['Please select whether to enable multiple sub organizations.'];
                                validationPassed = false;
                            }
                            if (form_data.enable_multiple_sub_org == 1) {
                                this.sub_orgs.forEach((subOrg, index) => {
                                    if (!subOrg) {
                                        this.insertValidations[`organiations_sub_org.${index}.subOrgName`] = ['Please enter sub organization name.'];
                                        validationPassed = false;
                                    }
                                    if (subOrg) {
                                        if (subOrg.length < 5) {
                                            this.insertValidations[`organiations_sub_org.${index}.subOrgName`] = ['Sub organization name should be at least 5 characters long.'];
                                            validationPassed = false;
                                        }
                                    }
                                });
                            }
                            /* Stop submission if validation fails */
                            if (!validationPassed) {
                                hide_ajax_loader();
                                return;
                            }
                            if (this.organization.enable_multiple_sub_org == 1 && this.sub_orgs.length > 0) {
                                this.sub_orgs.forEach(sub_org => {
                                    form_data.organiations_sub_org.push({
                                        'subOrgName': sub_org,
                                    });
                                });
                            }

                            const response = await this.createOrganization(form_data);
                            if (!response || !response.data || response.data.success == false) return;
                        }

                        else if (this.currentStep == 3) {
                            this.insertValidations = {};
                            /* Client-side validation */
                            if (this.allowedDevices.length > 0) {
                                this.allowedDevices.forEach((allowedDevice, index) => {
                                    if (allowedDevice.deviceModelId || allowedDevice.planId || allowedDevice.coverage_price || allowedDevice.expiration_date) {
                                        if (!allowedDevice.deviceModelId) {
                                            this.insertValidations[`organiation_allowed_devices.${index}.deviceModelId`] = ['Please select a device model.'];
                                            validationPassed = false;
                                        }
                                        if (!allowedDevice.planId) {
                                            this.insertValidations[`organiation_allowed_devices.${index}.planId`] = ['Please select a plan.'];
                                            validationPassed = false;
                                        }
                                        if (!allowedDevice.coverage_price) {
                                            this.insertValidations[`organiation_allowed_devices.${index}.coverage_price`] = ['Please enter coverage price.'];
                                            validationPassed = false;
                                        }
                                        if (!allowedDevice.expiration_date) {
                                            this.insertValidations[`organiation_allowed_devices.${index}.expiration_date`] = ['Please enter expiration date.'];
                                            validationPassed = false;
                                        }
                                    }
                                });
                            }
                            if (this.allowedRenewalDevices.length > 0) {
                                this.allowedRenewalDevices.forEach((renewalDevice, index) => {
                                    if (renewalDevice.deviceModelId || renewalDevice.planId || renewalDevice.coverage_price || renewalDevice.expiration_date) {
                                        if (!renewalDevice.deviceModelId) {
                                            this.insertValidations[`organiation_allowed_renewal_devices.${index}.deviceModelId`] = ['Please select a device model.'];
                                            validationPassed = false;
                                        }
                                        if (!renewalDevice.planId) {
                                            this.insertValidations[`organiation_allowed_renewal_devices.${index}.planId`] = ['Please select a plan.'];
                                            validationPassed = false;
                                        }
                                        if (!renewalDevice.coverage_price) {
                                            this.insertValidations[`organiation_allowed_renewal_devices.${index}.coverage_price`] = ['Please enter coverage price.'];
                                            validationPassed = false;
                                        }
                                        if (!renewalDevice.expiration_date) {
                                            this.insertValidations[`organiation_allowed_renewal_devices.${index}.expiration_date`] = ['Please enter expiration date.'];
                                            validationPassed = false;
                                        }
                                    }
                                });
                            }
                            /* Stop submission if validation fails */
                            if (!validationPassed) {
                                hide_ajax_loader();
                                return;
                            }
                            form_data = {
                                'form_step': this.currentStep,
                                'org_id': this.org_id, /* Use stored organization ID */
                                'organiation_allowed_devices': [],
                                'organiations_allowed_renewal_devices': []
                            };

                            /* Allowed devices */
                            if (this.allowedDevices.length > 0) {

                                this.allowedDevices.forEach(device => {
                                    if (device.deviceModelId.id && device.coverage_price || device.deductible && device.expiration_date) {
                                        form_data.organiation_allowed_devices.push({
                                            deviceModelId: device.deviceModelId.id,
                                            deviceModelName: device.deviceModelId.title,
                                            planId: device.planId.id,
                                            planName: device.planId.plan_name,
                                            freqOccurence: device.planId.freq_occurence,
                                            coverage_price: device.coverage_price,
                                            deductible: device.deductible,
                                            expiration_date: device.expiration_date
                                        });
                                    }
                                });
                                hide_ajax_loader();
                            }

                            /* Allowed renewal devices */
                            if (this.allowedRenewalDevices.length > 0) {
                                this.allowedRenewalDevices.forEach(device => {
                                    if (device.deviceModelId.id && device.coverage_price || device.deductible && device.expiration_date) {
                                        form_data.organiations_allowed_renewal_devices.push({
                                            deviceModelId: device.deviceModelId.id,
                                            deviceModelName: device.deviceModelId.title,
                                            planId: device.planId.id,
                                            planName: device.planId.plan_name,
                                            freqOccurence: device.planId.freq_occurence,
                                            coverage_price: device.coverage_price,
                                            deductible: device.deductible,
                                            expiration_date: device.expiration_date
                                        });
                                    }
                                });
                                hide_ajax_loader();
                            }

                            const response = await this.createOrganization(form_data);
                            if (!response || !response.data || response.data.success == false) return;
                        }

                        else if (this.currentStep == 4) {
                            /* Client-side Validation */
                            this.insertValidations = {};

                            if (this.organization.org_type !== 1 && this.organization.org_type !== 2) {
                                this.insertValidations.org_type = ['Please select organization type.'];
                                validationPassed = false;
                            }

                            if (this.organization.portal_status !== 0 && this.organization.portal_status !== 1) {
                                this.insertValidations.org_portal_status = ['Please select portal status.'];
                                validationPassed = false;
                            }
                            if (this.organization.portal_status == 0 && !this.organization.close_portal_message) {
                                this.insertValidations.org_closed_portal_msg = ['Please enter close portal message.'];
                                validationPassed = false;
                            }
                            if (this.claimReasons.length == 0) {
                                this.insertValidations.org_claim_reasons = ['Please select at least one claim reason.'];
                                validationPassed = false;
                            }
                            /* Stop submission if validation fails */
                            if (!validationPassed) {
                                hide_ajax_loader();
                                return;
                            }
                            form_data = {
                                'form_step': this.currentStep,
                                'org_id': this.org_id, /* Use stored organization ID */
                                'org_type': this.organization.org_type,
                                'org_portal_status': this.organization.portal_status,
                                'org_claim_reasons': [],
                            };

                            if (this.organization.portal_status == 0) {
                                form_data.org_close_portal_message = this.organization.close_portal_message;
                            }

                            if (this.claimReasons.length > 0) {
                                this.claimReasons.forEach(reason => {
                                    form_data.org_claim_reasons.push({
                                        reasonId: reason.id
                                    });
                                });
                            }

                            const response = await this.createOrganization(form_data);
                            if (!response || !response.data || response.data.success == false) return;
                        }
                        /* Mark the step as submitted */
                        this.submittedSteps.add(this.currentStep);
                    }
                    /* Move to the next step */
                    this.currentStep++;
                }
            } finally {
                this.isSubmitting = false; // Ensures flag is reset
            }
        },

        /* For Previous steps */
        prevStep() {
            show_ajax_loader();
            if (this.currentStep > 1) {
                this.currentStep--;
                /** update progress call */
                this.updateProgress(this.currentStep, this.steps.length);
                if (this.submittedSteps.has(this.currentStep)) {
                    this.submittedSteps.delete(this.currentStep);
                }
            }
            hide_ajax_loader();
        },


        /* For Next Steps submit */
        async createOrganization(form_data) {
            show_ajax_loader();
            this.insertValidations = {};
            const response = await axios.post("/smarttiusadmin/organizations/store", form_data, {
                headers: { "Content-type": "multipart/form-data" },
            }
            );
            if (response.data.success == true) {
                if (response.data.org_id) {
                    this.org_id = response.data.org_id;
                }
                hide_ajax_loader();
                return response; /* Return the response to be used in nextStep() */
            } else if (response.data.errors) {
                this.insertValidations = response.data.errors;
                hide_ajax_loader();
            } else {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "something went wrong, please try again.";
            }
        },

        /* Final Submit */
        async createOrganizationSubmit() {
            show_ajax_loader();
            this.isDisabledButton = true;
            const form_data = {
                form_step: this.currentStep,
                org_id: this.org_id,
                repair_enabled: this.organization.repair_enabled,
                can_edit_device: this.organization.can_edit_device,
                allow_parents_claim: this.organization.allow_parents_claim,
                additional_instructions: this.organization.additional_instructions,
            };
            const response = await this.createOrganization(form_data);
            if (response && response.data && response.data.success == true) {
                hide_ajax_loader();
                this.$alertMessage.success = true;
                this.$alertMessage.message = response.data.msg;
                this.isSubmitted = true;
                setTimeout(() => {
                    location.href = `${this.$userAppUrl}smarttiusadmin/organizations`;
                }, 5000);
            }
            else {
                this.isDisabledButton = false;
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            }
        },

        /** Update Progress bar (Mobile View) */
        updateProgress(currentStep, totalSteps) {
            const circle = $('svg circle:nth-of-type(2)');
            const text = $('svg text');

            const radius = 66;
            const circumference = 2 * Math.PI * radius;

            const percent = (currentStep / totalSteps) * 100;
            const offset = circumference - (percent / 100) * circumference;

            // Update circle stroke
            circle.css('stroke-dashoffset', offset + 'px');

            // Update step text
            text.text(currentStep + " Step");

            // Update step list
            $('.title_list li').each(function () {
                const stepNum = parseInt($(this).attr('list-data'), 10);
                if (stepNum <= currentStep) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
        }

    },
};
</script>
