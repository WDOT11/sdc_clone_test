<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0 ">
            <div class="card-body">
                <h4 class="mb-3 border-bottom pb-2 coman_main_heading">Device Plan</h4>

                <!-- Button and search section -->
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 mt-2 mb-3">
                    <!-- Left Side Button -->
                    <button data-bs-toggle="modal" data-bs-target="#Managedeviceplans" @click="addDevicePlan" class="btn blogal_pbtn_padding bg_blue text-white  d-flex align-items-center gap-10 def_14_size rounded">
                        <img :src="profileIc" width="22" height="22"> Manage device plans
                    </button>

                    <!-- Right Side: Search Bar and Buttons -->
                    <div class="d-flex align-items-center flex-wrap search_options gap-3 ">
                        <!-- Search by name -->
                        <input type="text" v-model="search" class="form-control def_14_size w-auto" placeholder="Search by Device Or Plan Name">

                        <!-- Buttons -->
                        <button class="btn d-flex align-items-center gap-10 def_14_size bg_blue blogal_pbtn_padding  text-white" @click="getDevicePlanData"><img :src="searchIc" width="20" height="20"> Search </button>
                        <button class="btn customm_reset_btn def_14_size blogal_pbtn_padding" @click="cancelSearch"> Clear </button>
                    </div>
                </div>

                <!-- Manage Device Plan Modal -->
                <div class="modal" id="Managedeviceplans">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <h4 class="modal-title mt-2 def_20_size d-flex gap-10 mb-0 align-items-center">
                                    <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                                    <span>Manage Device Plans</span>
                                </h4>
                                <button type="button" class="btn-close" id="managePlanModalClose" @click="closeAddPlanModel" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">
                                <div class="mb-5">
                                    <label class="block mb-2 def_18_size  list-header font-semibold ">Device Model</label>
                                    <multiselect id="tagging" v-model="selectedDeviceModel" :searchable="true" @select="handleSelect" @keydown.enter.native="handleEnterKey" @remove="handelRemove" @search-change="updateSearchModelName" placeholder="Search or choose device model" label="title" track-by="id" :options="devicemodeldata" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['w-full',{'border-red-500': insertValidations.selectedDeviceModel,}, ]" required :allow-empty="true" :preserve-search="false" :internal-search="true" :disabled="isMultiselectDisabled">
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

                                <!-- Select Plan -->
                                <div class="mb-2  ">
                                    <label class="block def_18_size list-header fw-semibold mb-3 mt-2"> Select Plan </label>
                                    <div class="radio-inputs rounded-pill">
                                        <label class="radio">
                                            <input id="switch_left" name="thisr" type="radio" v-model="devicePlanType" value="1"  @change="change_plan" />
                                            <span class="name">Coverage Plan</span>
                                        </label>
                                        <label class="radio">
                                            <input id="switch_right" name="thisr" type="radio" v-model="devicePlanType" value="2" @change="change_plan" />
                                            <span class="name">Renewal Plan</span>
                                        </label>
                                    </div>
                                    <small v-if="insertValidations.devicePlanType" ><ErrorMessage :msg="insertValidations.devicePlanType[0]"></ErrorMessage></small>
                                </div>

                                <!-- Coverage Plans -->
                                <div v-if="devicePlanType == 1" class="mb-6">
                                    <label class="block def_18_size list-header fw-semibold mb-0 "> Coverage Plans </label>
                                    <div class="mt-4 glob_scroll">
                                        <div v-for="(coverage_plan, index) in coverage_plans" :key="index" class="options_grids_plan mb-4 px-2 py-3 rounded onewhitebg">
                                            <div class="form-group d-flex align-items-center">
                                                <button style="width: 35px;" type="button" class=" bg-transparent border-0" @click="removeCoveragePlan(index)" v-if="coverage_plans.length > 1"> <img :src="deleteIc" width="35" height="35"> </button>
                                            </div>
                                            <div class="form-group">
                                                <label for="Plan" class="form-label">Plan Name <span class="text-danger">*</span></label>
                                                <input v-model="coverage_plan.PlanName" type="text" placeholder="Enter Plan Name" class="form-input form-control w-full rounded-md border-gray-300" />
                                                <small v-if="insertValidations[`coverage_plans.${index}.PlanName`]" >
                                                    <ErrorMessage :msg="insertValidations[`coverage_plans.${index}.PlanName`][0]"></ErrorMessage>

                                                </small>
                                            </div>
                                            <div class="form-group">
                                                <label for="Price" class="form-label">Price <span class="text-danger">*</span></label>
                                                <input v-model="coverage_plan.PlanPrice" type="number" step="any" min="0" placeholder="Enter Price" class="form-input form-control w-full rounded-md border-gray-300" />
                                                <span v-if="insertValidations[`coverage_plans.${index}.PlanPrice`]" class="error_msg_text">
                                                    <ErrorMessage :msg="insertValidations[`coverage_plans.${index}.PlanPrice`][0]"></ErrorMessage>

                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <label for="Deductible" class="form-label">Deductible Price</label>
                                                <input v-model="coverage_plan.PlanDeductPrice" type="number" step="any" min="0" placeholder="Enter Deductible Price" class="form-input form-control w-full rounded-md border-gray-300" />
                                                <span v-if="insertValidations[`coverage_plans.${index}.PlanDeductPrice`]" class="error_msg_text">
                                                    <ErrorMessage :msg="insertValidations[`coverage_plans.${index}.PlanDeductPrice`][0]"></ErrorMessage>
                                                </span>
                                            </div>
                                            <div class="form-group">

                                                <label for="Occurrence" class="form-label">Occurrence <span class="text-danger">*</span></label>
                                                <select v-model="coverage_plan.freqOccurence" class="form-control form-control w-full rounded-md border-gray-300">
                                                    <option value="">SELECT</option>
                                                    <option v-for="(billingCycle, index) in $billingCycle" :value="index + 1"> {{ billingCycle }} </option>
                                                </select>
                                                <span v-if=" insertValidations[`coverage_plans.${index}.freqOccurence`]" class="error_msg_text">
                                                    <ErrorMessage :msg="insertValidations[`coverage_plans.${index}.freqOccurence`][0]"></ErrorMessage>
                                                </span>
                                            </div>
                                            <div class="form-group" v-if="coverage_plan.freqOccurence == 1">
                                                <label for="expiryDays" class="form-label">Expiration Days <span class="text-danger">*</span></label>
                                                <input id="expiryDays" type="number" min="0" v-model="coverage_plan.planExpireDays" placeholder="Expiration Days" class="form-input form-control w-full rounded-md border-gray-300" />
                                                <span v-if="insertValidations[`coverage_plans.${index}.planExpireDays`]" class="error_msg_text">
                                                    <ErrorMessage :msg="insertValidations[`coverage_plans.${index}.planExpireDays`][0]"></ErrorMessage>
                                                </span>
                                            </div>

                                        </div>
                                        <button type="button" class="btn def_14_size bg_blue text-white mt-4 d-flex align-items-center gap-10" @click="addCoveragePlan">
                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0" viewBox="0 0 426.667 426.667" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M405.332 192H234.668V21.332C234.668 9.559 225.109 0 213.332 0 201.559 0 192 9.559 192 21.332V192H21.332C9.559 192 0 201.559 0 213.332c0 11.777 9.559 21.336 21.332 21.336H192v170.664c0 11.777 9.559 21.336 21.332 21.336 11.777 0 21.336-9.559 21.336-21.336V234.668h170.664c11.777 0 21.336-9.559 21.336-21.336 0-11.773-9.559-21.332-21.336-21.332zm0 0" fill="#ffffff" opacity="1" data-original="#000000"></path></g></svg>
                                            Add More </button>
                                    </div>
                                </div>

                                <!-- Renewal Plans -->
                                <div v-if="devicePlanType == 2" class="mb-6">
                                    <label class="block def_18_size list-header fw-semibold mb-0 mt-2"> Renewal Plans </label>
                                    <div class=" mt-4">
                                        <div v-for="(renewal_plan, index) in renewal_plans" :key="index" class="options_grids_plan mb-4 px-2 py-3 rounded ">
                                            <div class="form-group d-flex align-items-center">
                                                <button style="width: 35px;" type="button" class="bg-transparent border-0" @click="removeRenewalPlan(index)" v-if="renewal_plans.length > 1">
                                                    <img :src="deleteIc" width="35" height="35">
                                                </button>
                                            </div>
                                            <div class="form-group">
                                                <label for="Plan" class="form-label">Plan Name <span class="text-danger">*</span></label>
                                                <input v-model="renewal_plan.PlanName" type="text" placeholder="Enter Plan Name" class="form-input form-control w-full rounded-md border-gray-300" />
                                                <span v-if="insertValidations[`renewal_plans.${index}.PlanName`] " class="error_msg_text">
                                                    <ErrorMessage :msg="insertValidations[`renewal_plans.${index}.PlanName`][0]"></ErrorMessage>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <label for="Price" class="form-label">Price <span class="text-danger">*</span></label>
                                                <input v-model="renewal_plan.PlanPrice" type="number" step="any" min="0" placeholder="Enter Price" class="form-input form-control w-full rounded-md border-gray-300" />
                                                <span v-if="insertValidations[`renewal_plans.${index}.PlanPrice`]" class="error_msg_text">
                                                    <ErrorMessage :msg="insertValidations[`renewal_plans.${index}.PlanPrice`][0]"></ErrorMessage>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <label for="Deductible" class="form-label">Deductible Price</label>
                                                <input v-model="renewal_plan.PlanDeductPrice" type="number" step="any" min="0" placeholder="Enter Deductible Price" class="form-input form-control w-full rounded-md border-gray-300" />
                                                <span v-if="insertValidations[`renewal_plans.${index}.PlanDeductPrice`]" class="error_msg_text">
                                                    <ErrorMessage :msg="insertValidations[`renewal_plans.${index}.PlanDeductPrice`][0]"></ErrorMessage>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <label for="Occurrence" class="form-label">Occurrence <span class="text-danger">*</span></label>
                                                <select v-model="renewal_plan.freqOccurence" class="form-control form-control w-full rounded-md border-gray-300">
                                                    <option value="">SELECT</option>
                                                    <option v-for="(billingCycle, index) in $billingCycle" :value="index + 1">
                                                        {{ billingCycle }}
                                                    </option>
                                                </select>
                                                <span v-if="insertValidations[`renewal_plans.${index}.freqOccurence`]" class="error_msg_text">
                                                    <ErrorMessage :msg="insertValidations[`renewal_plans.${index}.freqOccurence`][0]"></ErrorMessage>
                                                </span>
                                            </div>
                                            <div class="form-group" v-if="renewal_plan.freqOccurence == 1">
                                                <label for="expiryDays" class="form-label">Expiration Days <span class="text-danger">*</span></label>
                                                <input type="number" v-model="renewal_plan.planExpireDays" placeholder="Expiration Days" class="form-input form-control w-full rounded-md border-gray-300" />
                                                <span v-if="insertValidations[`renewal_plans.${index}.planExpireDays`]" class="error_msg_text">
                                                    <ErrorMessage :msg="insertValidations[`renewal_plans.${index}.planExpireDays`][0]"></ErrorMessage>
                                                </span>
                                            </div>

                                        </div>

                                        <button type="button" class="btn bg_blue def_14_size text-white mt-4 d-flex align-items-center gap-10" @click="addRenewalPlan">
                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0" viewBox="0 0 426.667 426.667" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M405.332 192H234.668V21.332C234.668 9.559 225.109 0 213.332 0 201.559 0 192 9.559 192 21.332V192H21.332C9.559 192 0 201.559 0 213.332c0 11.777 9.559 21.336 21.332 21.336H192v170.664c0 11.777 9.559 21.336 21.332 21.336 11.777 0 21.336-9.559 21.336-21.336V234.668h170.664c11.777 0 21.336-9.559 21.336-21.336 0-11.773-9.559-21.332-21.336-21.332zm0 0" fill="#ffffff" opacity="1" data-original="#000000"></path></g></svg>
                                            Add More
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg ">
                                <button type="submit" @click="saveDevicePlan" :disabled="disabledButton" class="btn def_14_size bg_blue text-white">
                                    Save
                                </button>
                                <!-- <button data-bs-dismiss="modal" @click="closeAddPlanModel" type="button" class="btn btnblack">
                                    Cancel
                                </button> -->
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Manage Device Plan Modal End -->

                <!-- View Device Plan Modal -->
                <div class="modal" id="DevicePlanDetails">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content onewhitebg">

                        <!-- Modal Header -->
                        <div class="modal-header align-items-center">
                            <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                                <span> Device Plan Details </span>
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


                                        <td class="fw-bold">Plan Type:</td>
                                        <td class="text-muted fw-normal">{{ this.viewDevicePlanData.plan_type == 1 ? 'Coverage' : 'Renewal' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Device Family:</td>
                                        <td class="text-muted fw-normal">{{ this.viewDevicePlanData.device_family_name }}</td>


                                        <td class="fw-bold">Device Brand:</td>
                                        <td class="text-muted fw-normal">{{ this.viewDevicePlanData.device_brand_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Device Model:</td>
                                        <td class="text-muted fw-normal">{{ this.viewDevicePlanData.device_model_name }}</td>


                                        <td class="fw-bold">Price:</td>
                                        <td class="text-muted fw-normal">${{ this.viewDevicePlanData.price }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Deductible Price:</td>
                                        <td class="text-muted fw-normal">${{ this.viewDevicePlanData.deductible_price ?? 0 }}</td>


                                        <td class="fw-bold">Occurrence:</td>
                                        <td class="text-muted fw-normal">{{$billingCycle.find((billingCycle, index) => index + 1 == this.viewDevicePlanData.freq_occurence)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Plan Expiration Days:</td>
                                        <td colspan="3" class="text-muted fw-normal">{{ this.viewDevicePlanData.expiration_days }} Days</td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                           </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer onewhitebg">
                            <button  data-bs-dismiss="modal" @click="showViewModal = false" class="btn customm_reset_btn ">
                                Close
                            </button>

                        </div>

                        </div>
                    </div>
                </div>
                <!-- View Device Plan Modal End -->

                <div class="table-responsive">
                    <table class="table table-bordered table-hover def_14_size table_custom">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Device</th>
                                <th>Plan Name</th>
                                <th>Price</th>
                                <th>Plan Type</th>
                                <th>Occurence</th>
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
                                    {{ data.plan_type && data.plan_type == 1 ? "Coverage" : data.plan_type && data.plan_type == 2 ? "Renewal" : "-" }}
                                </td>
                                <td>
                                    {{ data.freq_occurence ? $billingCycle.find((billingCycle, index) => index + 1 == data.freq_occurence) : "-" }}
                                </td>
                                <td>
                                    <!-- <button @click="editDevicPlan(data.id)" class="bg-blue-500 text-white px-2 py-1 rounded editBtn">
                                        Edit
                                    </button> -->
                                    <div class="d-flex align-items-center gap-20">
                                    <button  data-bs-toggle="modal" data-bs-target="#DevicePlanDetails" @click="viewDevicPlan(data.id)" class="themetextcolor globalviewbtn extrabtns rounded-pill d-flex align-items-center def_14_size gap-1 "><img :src="viewIc" width="35" height="35"><span class="viewtext_Gl">View</span></button>
                                    <!-- <button @click="confirmDelete(data.id)" class=" bg-transparent text-white rounded"><img :src="deleteIc" width="28" height="28"></button> -->
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
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getDevicePlanData"></pagination>
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
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,
            /** To manage the manage and view models */
            showViewModal: false,

            /** Used in searching of models */
            searchModelText: "",

            /** To store the device model data by searching */
            devicemodeldata: [],

            /* Plans List */
            devicePlanData: this.deviceplandata.data,
            paginationData: this.pagination,
            search: "",

            /* Manage Device Plans */
            selectedDeviceId: "",
            queryModel: null,
            selectedPlanType: "",
            selectedDeviceModel: "",
            coverage_plans: [
                {
                    planId: null,
                    PlanName: "",
                    PlanPrice: "",
                    PlanDeductPrice: "",
                    freqOccurence: "",
                    planExpireDays: null,
                },
            ],
            renewal_plans: [
                {
                    planId: null,
                    PlanName: "",
                    PlanPrice: "",
                    PlanDeductPrice: "",
                    freqOccurence: "",
                    planExpireDays: null,
                },
            ],

            devicePlanType: 1,
            insertValidations: {}, /** To store the add model validations */
            viewDevicePlanData: {},
            isMultiselectDisabled:false,
            disabledButton: false, /** Save Button */
        };
    },
    /* created() {
        this.getDeviceModels(this.queryModel);
    }, */
    mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        const openPopup = urlParams.get('openPopup');
        this.queryModel = urlParams.get('model');

        if (openPopup == 'true') {
            show_admin_popup('#Managedeviceplans');
            if (this.queryModel) {
                this.selectedDeviceId = this.queryModel;
                this.selectedDeviceModel = this.queryModel;
                this.getDeviceModels(this.queryModel);
                this.selectDeviceModel(this.queryModel, this.devicePlanType);
                this.isMultiselectDisabled = true;
            }
        }
    },
    methods: {

        /* get device models */
        async getDeviceModels(modelID = null, name = this.searchModelText) {
            show_ajax_loader();
            try {
                let response = null;
                const url = `${this.$userAppUrl}smarttiusadmin/device-model/fetch-models${name ? `?name=${name}` : ""}${modelID ? `?deviceModelId=${modelID}` : ""}`;
                response = await axios.get(url);
                if (response.data.success && response.data.deviceModels.length > 0) {
                    if (modelID) {
                        this.devicemodeldata = response.data.deviceModels;
                        const matched = this.devicemodeldata.find(
                            model => model.id == modelID
                        );
                        if (matched) {
                            this.selectedDeviceModel = matched;
                        }
                    }
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
        updateSearchModelName(value) {
            if (value) {
                this.searchModelText = value;
                this.getDeviceModels(null, value);
            } else {
                this.searchModelText = '';
                this.getDeviceModels(null);
            }
        },

        /* To get the device plan data by using paging */
        async getDevicePlanData(page = 1, search = this.search) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/devices-plan?page=${page}${search !== "" || search !== null ? `&search=${this.search}` : "" }`
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
            await this.getDevicePlanData(1);
            hide_ajax_loader();
        },

        /** On click of delete */
        async confirmDelete(plan_id){
            this.$alertMessage.success = false;
            this.$alertMessage.message = "This is under development.";
        },

        /* on click view */
        async viewDevicPlan(id) {
            show_ajax_loader();
            this.showViewModal = true;
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/devices-plan/view/${id}`);
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

        /** When the device is dis-selected */
        handelRemove(){
            show_ajax_loader();
            /** removing the plans data */
            this.coverage_plans = [
                {
                    planId: null,
                    PlanName: "",
                    PlanPrice: "",
                    PlanDeductPrice: "",
                    freqOccurence: "",
                    planExpireDays: null,
                },
            ];
            this.renewal_plans = [
                {
                    planId: null,
                    PlanName: "",
                    PlanPrice: "",
                    PlanDeductPrice: "",
                    freqOccurence: "",
                    planExpireDays: null,
                },
            ];
            hide_ajax_loader();
        },

        /** On clcik manage device plans */
        async addDevicePlan(){
            this.searchModelText = "";
            this.selectedDeviceModel = "";
            this.getDeviceModels(null);
        },

        /** To close the add plan model */
        async closeAddPlanModel(){
            show_ajax_loader();

            this.searchModelText = "";
            this.selectedDeviceModel = "";
            this.devicemodeldata = [];

            this.coverage_plans = [
                {
                    deviceModel: "",
                    PlanName: "",
                    PlanPrice: "",
                    PlanDeductPrice: "",
                    freqOccurence: "",
                    planExpireDays: null,
                    planType: "",
                },
            ];
            this.renewal_plans = [
                {
                    deviceModel: "",
                    PlanName: "",
                    PlanPrice: "",
                    PlanDeductPrice: "",
                    freqOccurence: "",
                    planExpireDays: "",
                    planType: "",
                },
            ];
            this.devicePlanType = 1;
            this.insertValidations = {};
            this.isMultiselectDisabled = false;
            this.disabledButton = false;
            this.queryModel = null;
             /* Remove ?openPopup=true&model=... from URL without reloading */
            const cleanUrl = window.location.origin + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
            /* hide the model and remove the data from the fields */
            hide_admin_popup('#managePlanModalClose');
            hide_ajax_loader();
        },

        /** Handle selection from dropdown */
        handleSelect(model) {
            show_ajax_loader();
            this.selectedPlanType = this.devicePlanType;
            this.selectedDeviceId = model.id;
            if(model.id){
                this.selectDeviceModel(model.id, this.selectedPlanType);
            }
            hide_ajax_loader();
        },

        /** Handle Enter key press */
        handleEnterKey() {
            show_ajax_loader();
            this.selectedPlanType = this.devicePlanType;
            this.selectedDeviceId = this.selectedDeviceModel.id;

            if(this.selectedDeviceModel.id){
                this.selectDeviceModel(this.selectedDeviceModel.id, this.selectedPlanType);
            }
            hide_ajax_loader();
        },

        /** On change of plan type */
        async change_plan(){
            show_ajax_loader();
            this.selectedPlanType = this.devicePlanType;
            let selectedModel = this.selectedDeviceModel;
            if(selectedModel){
                this.selectDeviceModel(selectedModel.id, this.selectedPlanType);
            }
            hide_ajax_loader();
        },

        /** Common function for both selection and Enter key */
        async selectDeviceModel(model, planType = 1) {
            show_ajax_loader();
            this.insertValidations = {};

            const payload = {
                modelId: model,
                planType: planType,
            };

            try{
                let response = await axios.post(
                    `${this.$userAppUrl}smarttiusadmin/devices-plan/manage-device-plan`,
                    payload
                );
                if (response.data.success == true) {
                    if (planType == 1) {
                        if(response.data.deviceModelPlans.length > 0){
                            this.coverage_plans = response.data.deviceModelPlans.map((plan) => {
                                return {
                                    planId: plan.id,
                                    PlanName: plan.plan_name,
                                    PlanPrice: plan.price,
                                    PlanDeductPrice: plan.deductible_price,
                                    freqOccurence: plan.freq_occurence,
                                    planExpireDays: plan.expiration_days,
                                };
                            });
                            hide_ajax_loader();
                        } else {
                            this.coverage_plans = [
                                {
                                    planId: null,
                                    PlanName: "",
                                    PlanPrice: "",
                                    PlanDeductPrice: "",
                                    freqOccurence: "",
                                    planExpireDays: null,
                                },
                            ];
                            hide_ajax_loader();
                        }
                    } else if (planType == 2) {
                        if(response.data.deviceModelPlans.length > 0){
                            this.renewal_plans = response.data.deviceModelPlans.map((plan) => {
                                return {
                                    planId: plan.id,
                                    PlanName: plan.plan_name,
                                    PlanPrice: plan.price,
                                    PlanDeductPrice: plan.deductible_price,
                                    freqOccurence: plan.freq_occurence,
                                    planExpireDays: plan.expiration_days,
                                };
                            });
                            hide_ajax_loader();
                        } else {
                            this.renewal_plans = [
                                {
                                    planId: null,
                                    PlanName: "",
                                    PlanPrice: "",
                                    PlanDeductPrice: "",
                                    freqOccurence: "",
                                    planExpireDays: null,
                                },
                            ];
                            hide_ajax_loader();
                        }
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "something went wrong, please try again.";
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

        /** To manage the fields for add the multiple plans for a device (While selected Add renewal plans) */
        addRenewalPlan() {
            this.renewal_plans.push({
                deviceModel: this.selectedDeviceModel.id,
                PlanName: "",
                PlanPrice: "",
                PlanDeductPrice: "",
                freqOccurence: "",
                planExpireDays: null,
                planType: this.devicePlanType,
            });
        },

        /** To remove the plan fields(Created from the add more for add renewal plans) */
        removeRenewalPlan(index) {
            show_ajax_loader();
            this.renewal_plans.splice(index, 1);
            hide_ajax_loader();
        },

        /** To manage the fields for add the multiple plans for a device (While selected Add coverage plans) */
        addCoveragePlan() {
            this.coverage_plans.push({
                deviceModel: this.selectedDeviceModel.id,
                PlanName: "",
                PlanPrice: "",
                PlanDeductPrice: "",
                freqOccurence: "",
                planExpireDays: null,
                planType: this.devicePlanType,
            });
        },

        /** To remove the plan fields (Created from the add more for add coverage plans) */
        removeCoveragePlan(index) {
            show_ajax_loader();
            this.coverage_plans.splice(index, 1);
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

            /** Validate the plan type selection*/
            if(!this.devicePlanType){
                this.insertValidations.devicePlanType = [`Please select the plan type.`];
                validationPassed = false;
            }

            /** Validate the coverage plan fields If the coverage plan is selected*/
            if(this.devicePlanType == 1){
                this.coverage_plans.forEach((plan, index) => {
                    if (!plan.PlanName) {
                        this.insertValidations[`coverage_plans.${index}.PlanName`] = [`Please enter plan name.`];
                        validationPassed = false;
                    }
                    if (!plan.PlanPrice) {
                        this.insertValidations[`coverage_plans.${index}.PlanPrice`] = [`Please enter plan price.`];
                        validationPassed = false;
                    }
                    if(isNaN(plan.PlanDeductPrice)){
                        this.insertValidations[`coverage_plans.${index}.PlanDeductPrice`] = [`Please enter valid Deductible price.`];
                        validationPassed = false;
                    }
                    if(isNaN(plan.PlanPrice)){
                        this.insertValidations[`coverage_plans.${index}.PlanPrice`] = [`Please enter valid plan price.`];
                        validationPassed = false;
                    }
                    if (!plan.freqOccurence) {
                        this.insertValidations[`coverage_plans.${index}.freqOccurence`] = [`Please select frequency occurrence.`];
                        validationPassed = false;
                    }
                    if (!plan.planExpireDays && plan.freqOccurence == 1) {
                        this.insertValidations[`coverage_plans.${index}.planExpireDays`] = [`Please enter plan expiration days.`];
                        validationPassed = false;
                    }
                });
            }

            /** Validate the renewal plan fields If the renewal plan is selected */
            if(this.devicePlanType == 2){
                this.renewal_plans.forEach((plan, index) => {
                    if (!plan.PlanName) {
                        this.insertValidations[`renewal_plans.${index}.PlanName`] = [`Please enter plan name.`];
                        validationPassed = false;
                    }
                    if (!plan.PlanPrice) {
                        this.insertValidations[`renewal_plans.${index}.PlanPrice`] = [`Please enter plan price.`];
                        validationPassed = false;
                    }
                    if (!plan.freqOccurence) {
                        this.insertValidations[`renewal_plans.${index}.freqOccurence`] = [`Please select frequency occurrence.`];
                        validationPassed = false;
                    }
                    if (!plan.planExpireDays && plan.freqOccurence == 1) {
                        this.insertValidations[`renewal_plans.${index}.planExpireDays`] = [`Please enter plan expiration days.`];
                        validationPassed = false;
                    }
                });
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            const payload = {
                devicePlanType: this.devicePlanType,
                selectedDeviceModel: this.selectedDeviceModel?.id,
                selectedDeviceModelName: this.selectedDeviceModel?.title,
                coverage_plans:
                    this.devicePlanType == 1 ? this.coverage_plans : [],
                renewal_plans:
                    this.devicePlanType == 2 ? this.renewal_plans : [],
            };

            try {
                this.disabledButton = true;
                let response = await axios.post(
                    `${this.$userAppUrl}smarttiusadmin/devices-plan/store`,
                    payload
                );

                if (response.data.success) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    this.closeAddPlanModel();
                    /* get device plan data */
                    this.getDevicePlanData();
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

        /* On click of edit button */
        /*
        async editDevicPlan(id) {
            console.log("edit clicked popup opened");
            this.showEditModal = true;
            try {
                let response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/devices-plan/edit/${id}`
                );
                if (response.data.success == true) {
                    this.editDevicePlanId = response.data.editData.id;
                    this.editdeviceTitle = response.data.editData.title;

                    // Find and set the selected family
                    const familyID = response.data.editData.device_family_id;
                    this.editdeviceFamily = this.familydata.find((family) => family.id == familyID) || null;
                    // Fetch brands for selected family
                    await this.getBrands(familyID);
                    // Find and set the selected brand after fetching brand data
                    const brandID = response.data.editData.device_brand_id;
                    this.editdeviceBrand = this.branddata.find((brand) => brand.id == brandID) || null;
                }
            } catch (error) {
                if ( error && error.response && error.response.data && error.response.data.error ) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () =>
                            (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                        5000
                    ); // Small delay for smooth effect
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },
        */
    },
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
