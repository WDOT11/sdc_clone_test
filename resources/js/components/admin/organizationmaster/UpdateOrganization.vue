<template>
    <!-- Organization Details Modal -->
    <div class="modal org_modal upd_orgination" id="organizationEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  modal-xl">
        <!-- <div class="modal-dialog" style="max-width: 100%; width: 100%; margin: 0;"> -->
            <div class="modal-content onewhitebg">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-10">
                        <svg width="30" height="30" id="fi_15362521" viewBox="0 0 64 64"
                            xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                            <path
                                d="m32.6400146 2.2199707c-.3699951-.289978-.8900146-.289978-1.2600098 0-.0799561.0700073-8.539917 6.6799927-25.9700928 4.5800171-.2799072-.0300293-.5698242.0499878-.7799072.2399902-.2200928.1900024-.3399658.4700317-.3399658.75v20.5700074c0 9.9799805 4.1899414 19.2299805 11.5 25.3800049 4.3999023 3.6799927 9.7600098 6.4400024 15.9399414 8.2199707.0900879.0300293.1899414.0400391.2800293.0400391.0899658 0 .1799316-.0100098.2800293-.0400391 6.1599121-1.7799683 11.5200195-4.539978 15.9199219-8.2199707 7.3100586-6.1500244 11.5-15.4000244 11.5-25.3800049v-20.5700074c0-.2799683-.119873-.5599976-.3399658-.75-.210083-.1900024-.5-.2700195-.7799072-.2399902-17.3500977 2.0900269-25.8701172-4.5100098-25.9500732-4.5800171zm25.0699463 26.1400146c0 9.3900146-3.9299316 18.0800171-10.7900391 23.8400269-4.1098633 3.4499512-9.1298828 6.0599976-14.9099121 7.7599487-5.7900391-1.6999512-10.8200684-4.3099976-14.9299316-7.75-6.8601074-5.7699585-10.7900391-14.4599609-10.7900391-23.8499756v-19.4500122c15.0599365 1.5400391 23.4399414-3.1499634 25.7199707-4.6799927 2.2700195 1.5300293 10.6400146 6.2200317 25.6999512 4.6799927v19.4500123z">
                            </path>
                            <path
                                d="m33.4699707 29.9699707h-2.9399414c-5.1500244 0-9.3300781 4.1900024-9.3300781 9.3300171v2.5900269h21.6000977v-2.5900269c0-5.1400146-4.1800537-9.3300171-9.3300781-9.3300171z">
                            </path>
                            <path
                                d="m32 27.9699707c2.6899414 0 4.8800049-2.1799927 4.8800049-4.8699951s-2.1900635-4.8800049-4.8800049-4.8800049-4.8800049 2.1900024-4.8800049 4.8800049 2.1900635 4.8699951 4.8800049 4.8699951z">
                            </path>
                        </svg>
                        <h5 class="modal-title def_22_size" id="exampleModalLabel">Edit Organization Details</h5>
                    </div>
                    <button type="button" class="btn-close" @click="closeEditOrg" data-bs-dismiss="modal" id="organizationEditModal"></button>
                </div>
                <div class="modal-body onewhitebg">
                    <div class="row">
                        <div class="col-12 ">
                            <div class="card org_innercard">

                                <div class="card-body">
                                    <div class="list-header flex justify-between align-items-center mb-2">
                                        <h4 class="fw-bold def_18_size  mb-0">Organization</h4>
                                        <div class="onewhitebg d-flex align-items-center gap-1 rounded-pill px-3 py-1">

                                            <button v-if="!isEditOrgDetails" type="button" @click="editOrgDetails" class="btn text-dark def_14_size d-flex align-items-center gap-1 bg-transparent p-0">
                                                <img :src="editIc" width="25" height="25">
                                                Edit
                                            </button>

                                            <button v-else type="button" @click="saveOrgDetails(editorgdata.id)"
                                                class="btn d-flex align-items-center gap-1 p-1 bg-transparent border savebtn_blobal  me-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0"
                                                    viewBox="0 0 28 28" style="enable-background:new 0 0 512 512"
                                                    xml:space="preserve" class="">
                                                    <g>
                                                        <path
                                                            d="M7.8 26h12.5c2.8 0 5-2.2 5-5V9.2c0-1.1-.4-2.1-1.2-2.8l-3.3-3.2c-.4-.3-.8-.6-1.2-.8v4.2c0 1.7-1.3 3-3 3h-5.3c-1.6 0-3-1.3-3-3v-4c.1-.3-.2-.6-.5-.6-2.8 0-5 2.2-5 5v14c0 2.8 2.2 5 5 5zm1.5-12.5h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1zm0 4.7h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1z"
                                                            fill="#14b009" opacity="1" data-original="#000000" class="">
                                                        </path>
                                                        <path d="M11.4 7.5h5.3c.6 0 1-.4 1-1V2h-7.3v4.5c0 .6.4 1 1 1z"
                                                            fill="#14b009" opacity="1" data-original="#000000" class="">
                                                        </path>
                                                    </g>
                                                </svg>
                                                Save
                                            </button>
                                            <button v-if="isEditOrgDetails" type="button" @click="cancelEditOrgDetails" class="btn def_14_size d-flex align-items-center gap-1 bg-transparent border candel_blobal p-1">
                                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" id="fi_12864365">
                                                    <linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse"
                                                        x1="376.27" x2="135.74" y1="376.27" y2="135.74">
                                                        <stop offset="0" stop-color="#ef3739"></stop>
                                                        <stop offset=".54" stop-color="#ef3739"></stop>
                                                        <stop offset="1" stop-color="#ff8c8b"></stop>
                                                    </linearGradient>
                                                    <linearGradient id="linear-gradient-2"
                                                        gradientUnits="userSpaceOnUse" x1="326.21" x2="185.8" y1="326.2"
                                                        y2="185.8">
                                                        <stop offset="0" stop-color="#ffd2d2"></stop>
                                                        <stop offset=".57" stop-color="#fff"></stop>
                                                        <stop offset="1" stop-color="#fff"></stop>
                                                    </linearGradient>
                                                    <g id="Layer_14" data-name="Layer 14">
                                                        <path
                                                            d="m496.53 129.86c-13.44-49.47-64.89-100.93-114.37-114.37-30.34-7.56-70.98-15.35-126.16-15.49-55.16.14-95.8 7.93-126.14 15.49-49.47 13.44-100.93 64.89-114.37 114.37-7.56 30.33-15.35 70.98-15.49 126.14.14 55.17 7.93 95.81 15.49 126.15 13.44 49.48 64.9 100.93 114.37 114.37 30.34 7.56 71 15.35 126.15 15.48 55.16-.13 95.81-7.92 126.15-15.48 49.48-13.44 100.93-64.89 114.37-114.37 7.56-30.34 15.35-71 15.48-126.15-.13-55.16-7.92-95.8-15.48-126.14z"
                                                            fill="#ffe5e5"></path>
                                                        <path
                                                            d="m256 85.93a170.08 170.08 0 1 0 170.08 170.07 170.08 170.08 0 0 0 -170.08-170.07z"
                                                            fill="url(#linear-gradient)"></path>
                                                        <path
                                                            d="m282.65 256 43.56-43.55a18.86 18.86 0 0 0 0-26.65 18.86 18.86 0 0 0 -26.65 0l-43.56 43.56-43.55-43.56a18.84 18.84 0 0 0 -26.65 26.65l43.56 43.55-43.56 43.56a18.85 18.85 0 0 0 0 26.64 18.84 18.84 0 0 0 26.65 0l43.55-43.55 43.55 43.55a18.84 18.84 0 1 0 26.65-26.64z"
                                                            fill="url(#linear-gradient-2)"></path>
                                                    </g>
                                                </svg>
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="table-responsive">
                                                <table class="table table-bordered def_14_size">
                                                    <tbody>
                                                        <tr>
                                                            <td><b>Organization Name:</b></td>
                                                            <td>
                                                                <input v-if="isEditOrgDetails" type="text" v-model="editorgdata.org_name" :class="['form-control ', { 'is-invalid': validationErrors.org_name, },]" />
                                                                <small v-if="validationErrors.org_name && isEditOrgDetails">
                                                                    <ErrorMessage :msg="validationErrors.org_name[0]">
                                                                    </ErrorMessage>
                                                                </small>
                                                                <span v-if="!isEditOrgDetails">{{ editorgdata.org_name }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Logo:</b></td>
                                                            <td>
                                                                <div v-if="isEditOrgDetails"
                                                                    class="d-flex align-items-center gap-2">
                                                                    <img v-if="editorgdata.logo_file_path && editorgdata.logo_file_name"
                                                                        :src="editorgdata.logo_file_path + editorgdata.logo_file_name"
                                                                        alt="Logo" class="img-thumbnail" width="50px"
                                                                        height="50px">
                                                                    <input type="file" @change="handleLogoUpload"
                                                                        class="form-control "
                                                                        accept="image/*">
                                                                    <small
                                                                        v-if="validationErrors.logo_file && isEditOrgDetails">
                                                                        <ErrorMessage
                                                                            :msg="validationErrors.logo_file[0]">
                                                                        </ErrorMessage>
                                                                    </small>
                                                                </div>
                                                                <div v-else>
                                                                    <img v-if="editorgdata.logo_file_path && editorgdata.logo_file_name"
                                                                        :src="editorgdata.logo_file_path + editorgdata.logo_file_name"
                                                                        alt="Logo" class="img-thumbnail" width="50px"
                                                                        height="50px">
                                                                    <p class="mb-0 themetextcolor" v-else>N/A</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered def_14_size">
                                                    <tbody>
                                                        <tr>
                                                            <td><b>Cover Image:</b></td>
                                                            <td>
                                                                <div v-if="isEditOrgDetails"
                                                                    class="d-flex align-items-center gap-2">
                                                                    <img v-if="editorgdata.cover_file_path && editorgdata.cover_file_name"
                                                                        :src="editorgdata.cover_file_path + editorgdata.cover_file_name"
                                                                        alt="cover image" class="img-thumbnail"
                                                                        width="70" height="50">
                                                                    <input type="file" @change="handleCoverUpload"
                                                                        class="form-control "
                                                                        accept="image/*">
                                                                    <small
                                                                        v-if="validationErrors.cover_file && isEditOrgDetails">
                                                                        <ErrorMessage
                                                                            :msg="validationErrors.cover_file[0]">
                                                                        </ErrorMessage>
                                                                    </small>
                                                                </div>
                                                                <div v-else>
                                                                    <img v-if="editorgdata.cover_file_path && editorgdata.cover_file_name"
                                                                        :src="editorgdata.cover_file_path + editorgdata.cover_file_name"
                                                                        alt="cover image" class="img-thumbnail"
                                                                        width="70" height="50">
                                                                    <p class="mb-0 themetextcolor" v-else>N/A</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">

                                            <div class="table-responsive">
                                                <table class="table table-bordered def_14_size editorg">
                                                    <tbody>
                                                        <tr>
                                                            <td><b>Service Agreement:</b>

                                                                <div class="row mx-1 wrapper_r">
                                                                    <div class="radio-wrapper noselect">
                                                                        <div class="radio-option">
                                                                            <input type="radio"
                                                                                v-model="org_service_agreement"
                                                                                id="agreement1" class="form-check-input"
                                                                                value="pdf"
                                                                                :disabled="!isEditOrgDetails"
                                                                                @change="handleAgreementChange" />
                                                                            <label class="form-check-label"
                                                                                for="agreement1"> Upload PDF </label>
                                                                        </div>
                                                                        <div class="radio-option">
                                                                            <input type="radio"
                                                                                v-model="org_service_agreement"
                                                                                id="agreement2" class="form-check-input"
                                                                                value="link"
                                                                                :disabled="!isEditOrgDetails"
                                                                                @change="handleAgreementChange" />
                                                                            <label class="form-check-label"
                                                                                for="agreement2"> Upload URL </label>
                                                                        </div>
                                                                        <div class="radio-option">
                                                                            <input type="radio"
                                                                                v-model="org_service_agreement"
                                                                                id="agreement3" class="form-check-input"
                                                                                value="none"
                                                                                :disabled="!isEditOrgDetails"
                                                                                @change="handleAgreementChange" />
                                                                            <label class="form-check-label"
                                                                                for="agreement3"> None </label>
                                                                        </div>
                                                                    </div>
                                                                    <!-- <small v-if="insertValidations.org_service_agreement" class="text-red-500">{{ insertValidations.org_service_agreement[0] }}</small> -->
                                                                </div>

                                                                <div v-if="isEditOrgDetails">
                                                                    <input placeholder="Past URL..."
                                                                        v-if="org_service_agreement == 'link'"
                                                                        type="text" v-model="editorgdata.agreement_link"
                                                                        class="form-control mt-2  mb-2">
                                                                    <small
                                                                        v-if="validationErrors.agreement_link && isEditOrgDetails">
                                                                        <ErrorMessage
                                                                            :msg="validationErrors.agreement_link[0]">
                                                                        </ErrorMessage>
                                                                    </small>
                                                                    <input v-if="org_service_agreement == 'pdf'"
                                                                        type="file" @change="handlePdfUpload"
                                                                        class="form-control mt-2 "
                                                                        accept=".pdf">
                                                                    <small
                                                                        v-if="validationErrors.pdf_file && isEditOrgDetails">
                                                                        <ErrorMessage
                                                                            :msg="validationErrors.pdf_file[0]">
                                                                        </ErrorMessage>
                                                                    </small>
                                                                </div>
                                                                <div v-else>
                                                                    <p class="mb-0"
                                                                        v-if="org_service_agreement == 'link'">{{
                                                                            editorgdata.agreement_link ?? 'N/A' }}</p>
                                                                    <button type="button"
                                                                        @click="downlaodPdf(editorgdata.pdf_file_path, editorgdata.pdf_file_name)"
                                                                        v-if="org_service_agreement == 'pdf'"
                                                                        class="btn btn-sm btn-warning"> Download PDF
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="h-100 card org_innercard">

                                <div class="card-body">
                                    <div class="list-header flex justify-between align-items-center mb-2">
                                        <h4 class="fw-bold mb-0  def_18_size">Sub-Organization</h4>
                                        <div class="onewhitebg d-flex align-items-center gap-1 rounded-pill px-3 py-1">
                                            <button v-if="!isEditSubOrg" type="button" @click="editSubOrg(editorgdata.id)"
                                                class="btn text-dark def_14_size d-flex align-items-center gap-1 bg-transparent p-0">
                                                <img :src="editIc" width="25" height="25">
                                                Edit
                                            </button>
                                            <button v-else type="button" @click="saveSubOrg(editorgdata.id)"
                                                class="btn d-flex align-items-center gap-1 p-1 bg-transparent border savebtn_blobal  me-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0"
                                                    viewBox="0 0 28 28" style="enable-background:new 0 0 512 512"
                                                    xml:space="preserve" class="">
                                                    <g>
                                                        <path
                                                            d="M7.8 26h12.5c2.8 0 5-2.2 5-5V9.2c0-1.1-.4-2.1-1.2-2.8l-3.3-3.2c-.4-.3-.8-.6-1.2-.8v4.2c0 1.7-1.3 3-3 3h-5.3c-1.6 0-3-1.3-3-3v-4c.1-.3-.2-.6-.5-.6-2.8 0-5 2.2-5 5v14c0 2.8 2.2 5 5 5zm1.5-12.5h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1zm0 4.7h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1z"
                                                            fill="#14b009" opacity="1" data-original="#000000" class="">
                                                        </path>
                                                        <path d="M11.4 7.5h5.3c.6 0 1-.4 1-1V2h-7.3v4.5c0 .6.4 1 1 1z"
                                                            fill="#14b009" opacity="1" data-original="#000000" class="">
                                                        </path>
                                                    </g>
                                                </svg>
                                                Save
                                            </button>
                                            <button v-if="isEditSubOrg" type="button" @click="cancelEditSubOrg(editorgdata.id)" class="btn def_14_size d-flex align-items-center gap-1 bg-transparent border candel_blobal p-1">
                                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" id="fi_12864365">
                                                    <linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse"
                                                        x1="376.27" x2="135.74" y1="376.27" y2="135.74">
                                                        <stop offset="0" stop-color="#ef3739"></stop>
                                                        <stop offset=".54" stop-color="#ef3739"></stop>
                                                        <stop offset="1" stop-color="#ff8c8b"></stop>
                                                    </linearGradient>
                                                    <linearGradient id="linear-gradient-2"
                                                        gradientUnits="userSpaceOnUse" x1="326.21" x2="185.8" y1="326.2"
                                                        y2="185.8">
                                                        <stop offset="0" stop-color="#ffd2d2"></stop>
                                                        <stop offset=".57" stop-color="#fff"></stop>
                                                        <stop offset="1" stop-color="#fff"></stop>
                                                    </linearGradient>
                                                    <g id="Layer_14" data-name="Layer 14">
                                                        <path
                                                            d="m496.53 129.86c-13.44-49.47-64.89-100.93-114.37-114.37-30.34-7.56-70.98-15.35-126.16-15.49-55.16.14-95.8 7.93-126.14 15.49-49.47 13.44-100.93 64.89-114.37 114.37-7.56 30.33-15.35 70.98-15.49 126.14.14 55.17 7.93 95.81 15.49 126.15 13.44 49.48 64.9 100.93 114.37 114.37 30.34 7.56 71 15.35 126.15 15.48 55.16-.13 95.81-7.92 126.15-15.48 49.48-13.44 100.93-64.89 114.37-114.37 7.56-30.34 15.35-71 15.48-126.15-.13-55.16-7.92-95.8-15.48-126.14z"
                                                            fill="#ffe5e5"></path>
                                                        <path
                                                            d="m256 85.93a170.08 170.08 0 1 0 170.08 170.07 170.08 170.08 0 0 0 -170.08-170.07z"
                                                            fill="url(#linear-gradient)"></path>
                                                        <path
                                                            d="m282.65 256 43.56-43.55a18.86 18.86 0 0 0 0-26.65 18.86 18.86 0 0 0 -26.65 0l-43.56 43.56-43.55-43.56a18.84 18.84 0 0 0 -26.65 26.65l43.56 43.55-43.56 43.56a18.85 18.85 0 0 0 0 26.64 18.84 18.84 0 0 0 26.65 0l43.55-43.55 43.55 43.55a18.84 18.84 0 1 0 26.65-26.64z"
                                                            fill="url(#linear-gradient-2)"></path>
                                                    </g>
                                                </svg>
                                                Cancel
                                            </button>
                                        </div>
                                    </div>

                                    <!-- EDIT MODE -->
                                    <div class="onewhitebg p-3 rounded" v-if="isEditSubOrg">
                                        <div class="inner_respo">
                                        <div v-for="(item, index) in editorgdata.subOrg" :key="item.id || index"
                                            class="d-flex align-items-center mb-2 gap-2">
                                            <input type="text" v-model="editorgdata.subOrg[index].name"
                                                :class="['form-control ', { 'is-invalid': validationErrors[`sub_org_data.${index}.name`], },]"
                                                placeholder="Enter sub-organization name" />
                                            <small v-if="validationErrors[`sub_org_data.${index}.name`] && isEditSubOrg">
                                                <ErrorMessage :msg="validationErrors[`sub_org_data.${index}.name`][0]"></ErrorMessage>
                                            </small>
                                            <!-- <button type="button" :class="editorgdata.subOrg[index].active == 1 ? 'btn btn-sm btn-success' : 'btn btn-sm btn-secondary'" @click="toggleSubOrgStatus(index)">{{ editorgdata.subOrg[index].active == 1 ? 'Active' : 'Inactive' }}</button> -->
                                            <button type="button" :class="item.id ? (item.active == 1 || item.active == null ? 'customm_reset_btn btn rounded-pill ml-1 def_14_size' : 'btn  btn-success  rounded-pill ml-1 def_14_size') : 'btn  rounded-pill btn-danger ml-1 def_14_size'"
                                                @click="toggleSubOrgStatus(index)"> {{ item.id ? (item.active == 1 || item.active == null ? 'Inactive' : 'Active') : 'Remove' }}</button>
                                            <button type="button" class="btn border p-1 ml-1 global_b"
                                                v-if="editorgdata.subOrg[index].isDeletable"
                                                @click="confirmSubOrgDelete(editorgdata.subOrg[index].id)"><svg
                                                    xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24"
                                                    x="0" y="0" viewBox="0 0 32 32"
                                                    style="enable-background:new 0 0 512 512" xml:space="preserve"
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
                                        </div>
                                        </div>
                                        <!-- Add Sub-Org Button -->
                                        <button type="button" class="btn btn-sm btn-primary mt-2"
                                            @click="addNewSubOrg">Add Sub-Org</button>
                                    </div>

                                    <!-- VIEW MODE -->
                                    <span v-else>
                                        <!-- Active Sub-Orgs -->
                                        <div v-if="editorgdata.subOrg && editorgdata.subOrg.length"
                                            class="d-flex p-2 onewhitebg gap-4">
                                            <div class="flex-fill onewhitebg shadow rounded p-2">
                                                <h6 class="border-bottom pb-2 themetextcolor">Active</h6>
                                                <div v-if="editorgdata.subOrg.some(item => item.active == 1 || item.active == null)" class="mt-1 glob_scroll">
                                                    <ol class="suborg_list p-0 m-0">
                                                        <li v-for="(item, index) in editorgdata.subOrg.filter(item => item.active == 1 || item.active == null)"
                                                            :key="'active-' + index">
                                                            {{ item.name }}
                                                        </li>
                                                    </ol>
                                                </div>
                                                <p class="themetextcolor" v-else>N/A</p>

                                            </div>
                                            <div class="flex-fill onewhitebg shadow rounded p-2">
                                                <h6 class="border-bottom pb-2  themetextcolor">Inactive</h6>
                                                <!-- Inactive Sub-Orgs -->
                                                <div v-if="editorgdata.subOrg.some(item => item.active == 0)"
                                                    class="mt-1 glob_scroll">
                                                    <ol class="suborg_list p-0 m-0">
                                                        <li v-for="(item, index) in editorgdata.subOrg.filter(item => item.active == 0)"
                                                            :key="'inactive-' + index">
                                                            {{ item.name }}
                                                        </li>
                                                    </ol>
                                                </div>
                                                <p class="themetextcolor" v-else>N/A</p>

                                            </div>
                                        </div>
                                        <span class="themetextcolor" v-else>N/A</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="border-0 bg-transparent card">

                                <div class="card  org_innercard">
                                    <div class="card-body">
                                        <h5 class="def_18_size themetextcolor  mb-2  border-bottom pb-1">Device Details</h5>
                                        <div class="list-header flex justify-between align-items-center mb-2">
                                            <h4 class="fw-bold  mb-0 def_18_size">Allowed Devices</h4>
                                            <div class="onewhitebg d-flex align-items-center gap-1 rounded-pill px-3 py-1">

                                                <button v-if="!isEditAllowedDevices" type="button" @click="editAllowedDevices(editorgdata.id)" class="btn text-dark def_14_size d-flex align-items-center gap-1 bg-transparent p-0">
                                                    <img :src="editIc" width="25" height="25">
                                                    Edit
                                                </button>

                                                <button v-if="isEditAllowedDevices" type="button"
                                                    @click="cancelEditAllowedDevices(editorgdata.id)"
                                                    class="btn def_14_size d-flex align-items-center gap-1 bg-transparent border candel_blobal p-1">
                                                        <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="fi_12864365"><linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse" x1="376.27" x2="135.74" y1="376.27" y2="135.74"><stop offset="0" stop-color="#ef3739"></stop><stop offset=".54" stop-color="#ef3739"></stop><stop offset="1" stop-color="#ff8c8b"></stop></linearGradient><linearGradient id="linear-gradient-2" gradientUnits="userSpaceOnUse" x1="326.21" x2="185.8" y1="326.2" y2="185.8"><stop offset="0" stop-color="#ffd2d2"></stop><stop offset=".57" stop-color="#fff"></stop><stop offset="1" stop-color="#fff"></stop></linearGradient><g id="Layer_14" data-name="Layer 14"><path d="m496.53 129.86c-13.44-49.47-64.89-100.93-114.37-114.37-30.34-7.56-70.98-15.35-126.16-15.49-55.16.14-95.8 7.93-126.14 15.49-49.47 13.44-100.93 64.89-114.37 114.37-7.56 30.33-15.35 70.98-15.49 126.14.14 55.17 7.93 95.81 15.49 126.15 13.44 49.48 64.9 100.93 114.37 114.37 30.34 7.56 71 15.35 126.15 15.48 55.16-.13 95.81-7.92 126.15-15.48 49.48-13.44 100.93-64.89 114.37-114.37 7.56-30.34 15.35-71 15.48-126.15-.13-55.16-7.92-95.8-15.48-126.14z" fill="#ffe5e5"></path><path d="m256 85.93a170.08 170.08 0 1 0 170.08 170.07 170.08 170.08 0 0 0 -170.08-170.07z" fill="url(#linear-gradient)"></path><path d="m282.65 256 43.56-43.55a18.86 18.86 0 0 0 0-26.65 18.86 18.86 0 0 0 -26.65 0l-43.56 43.56-43.55-43.56a18.84 18.84 0 0 0 -26.65 26.65l43.56 43.55-43.56 43.56a18.85 18.85 0 0 0 0 26.64 18.84 18.84 0 0 0 26.65 0l43.55-43.55 43.55 43.55a18.84 18.84 0 1 0 26.65-26.64z" fill="url(#linear-gradient-2)"></path></g></svg>
                                                        Cancel
                                                </button>

                                            </div>
                                        </div>
                                        <!-- <div class="glob_scroll"></div> -->
                                        <div class="table-responsive">
                                            <div class="inner_respo">
                                                <table class="table table-bordered table-hover def_14_size">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Device Model</th>
                                                            <th>Plan Name</th>
                                                            <th>Coverage Price</th>
                                                            <th>Deductible</th>
                                                            <th>Expiration Date</th>
                                                            <th>Status</th>
                                                            <th v-if="isEditAllowedDevices">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-if="editorgdata.allowed_models && editorgdata.allowed_models.length > 0" v-for="(allowedModel, index) in editorgdata.allowed_models" :key="allowedModel.id">
                                                            <td>{{ index + 1 }}</td>
                                                            <td>
                                                                <multiselect data-order="false" class="def_14_size"
                                                                    v-if="isEditAllowedDevices"
                                                                    v-model="allowedModel.model_id"
                                                                    :options="devicemodeldata" :searchable="true"
                                                                    label="title" track-by="id"
                                                                    placeholder="Search or choose device model"
                                                                    @search-change="updateSearchModelName"
                                                                    @select="handleSelect($event, false, index)"
                                                                    @keydown.enter.native="handleEnterKey(false, index)"
                                                                    @remove="clearDeviceSelection(false, index)"
                                                                    selectLabel="" deselectLabel="" :disabled="!(editAllowedDeviceIndex == index || !allowedModel.id)">
                                                                    <template #noOptions>
                                                                        <span class="custom-message">Please enter at least five characters</span>
                                                                    </template>
                                                                    <template #noResult>
                                                                        <span class="custom-message">No device models found, please try another search.</span>
                                                                    </template>
                                                                </multiselect>
                                                                <small v-if="validationErrors[`allowed_devices_data.${index}.deviceModelId`] && isEditAllowedDevices">
                                                                    <ErrorMessage
                                                                        :msg="validationErrors[`allowed_devices_data.${index}.deviceModelId`][0]">
                                                                    </ErrorMessage>
                                                                </small>
                                                                <span v-if="!isEditAllowedDevices">{{ allowedModel.allowed_model_name || '' }}</span>
                                                            </td>
                                                            <td>
                                                                <multiselect class="def_14_size"
                                                                    v-if="isEditAllowedDevices"
                                                                    v-model="allowedModel.device_plan_id"
                                                                    :options="coveragePlans[index] || []"
                                                                    label="plan_name" track-by="id"
                                                                    placeholder="Select a Device Plan" :taggable="false"
                                                                    selectLabel="" deselectLabel=""
                                                                    @select="getDevicePlansData($event.id, 1, index)"
                                                                    @remove="clearPlans(false, index)" :disabled="!(editAllowedDeviceIndex == index || !allowedModel.id)">
                                                                    <template #noOptions>
                                                                        <span class="custom-message ">No device plans available.</span>
                                                                    </template>
                                                                    <template #noResult>
                                                                        <span class="custom-message">No device plans found, please try another search.</span>
                                                                    </template>
                                                                </multiselect>
                                                                <small v-if="validationErrors[`allowed_devices_data.${index}.planId`] && isEditAllowedDevices">
                                                                    <ErrorMessage :msg="validationErrors[`allowed_devices_data.${index}.planId`][0]">
                                                                    </ErrorMessage>
                                                                </small>
                                                                <span v-if="!isEditAllowedDevices">{{ allowedModel.allowed_plan_name || '' }}</span>
                                                            </td>
                                                            <td>
                                                                <input v-if="isEditAllowedDevices" type="number" v-model="allowedModel.allowed_coverage_price" min="0" :class="['form-control form_ctrl_w', { 'is-invalid': validationErrors[`allowed_devices_data.${index}.coveragePrice`], },]" :disabled="!(editAllowedDeviceIndex == index || !allowedModel.id)">
                                                                <small v-if="validationErrors[`allowed_devices_data.${index}.coveragePrice`] && isEditAllowedDevices">
                                                                    <ErrorMessage :msg="validationErrors[`allowed_devices_data.${index}.coveragePrice`][0]">
                                                                    </ErrorMessage>
                                                                </small>
                                                                <span v-if="!isEditAllowedDevices">${{ allowedModel.allowed_coverage_price || '0' }}</span>
                                                            </td>
                                                            <td>
                                                                <input v-if="isEditAllowedDevices" type="number" v-model="allowedModel.allowed_deductible_price":class="['form-control form_ctrl_w', { 'is-invalid': validationErrors[`allowed_devices_data.${index}.deductiblePrice`], },] "min="0" :disabled="!(editAllowedDeviceIndex == index || !allowedModel.id)">
                                                                <small v-if="validationErrors[`allowed_devices_data.${index}.deductiblePrice`] && isEditAllowedDevices">
                                                                    <ErrorMessage :msg="validationErrors[`allowed_devices_data.${index}.deductiblePrice`][0]">
                                                                    </ErrorMessage>
                                                                </small>
                                                                <span v-if="!isEditAllowedDevices">${{allowedModel.allowed_deductible_price || '0'}}</span>
                                                            </td>
                                                            <td>
                                                                <input v-if="isEditAllowedDevices" type="date" v-model="allowedModel.allowed_expiration_date" :class="['form-control form_ctrl_w', { 'is-invalid': validationErrors[`allowed_devices_data.${index}.expiration_date`], },]" :disabled="!(editAllowedDeviceIndex == index || !allowedModel.id)">
                                                                <small v-if="validationErrors[`allowed_devices_data.${index}.expiration_date`] && isEditAllowedDevices">
                                                                    <ErrorMessage :msg="validationErrors[`allowed_devices_data.${index}.expiration_date`][0]"></ErrorMessage>
                                                                </small>
                                                                <span v-if="!isEditAllowedDevices">{{ filterFormatDate(allowedModel.allowed_expiration_date) }}</span>
                                                            </td>
                                                            <td> {{ allowedModel.id ? (allowedModel.active == 1 ? 'Active' : 'Inactive') : '' }} </td>
                                                            <td v-if="isEditAllowedDevices">
                                                                <button type="button"  v-if="editAllowedDeviceIndex != index && allowedModel.id" @click="editAllowedDevice(index)" class="customm_reset_btn rounded-pill btn d-flex align-items-center gap-1 def_14_size"><img :src="editIc" width="20" height="20"> Edit</button>
                                                                <button type="button" v-if="editAllowedDeviceIndex == index" :class="allowedModel.id ? (allowedModel.active == 1 ? 'customm_reset_btn btn rounded-pill ml-1 def_14_size' : 'btn rounded-pill btn-success ml-1 def_14_size') : 'btn rounded-pill btn-danger ml-1 def_14_size'" @click="toggleAllowedDevicesStatus(index)"> {{ allowedModel.id ? (allowedModel.active == 1 ? 'Inactive' : 'Active') : 'Remove' }}</button>
                                                                <button type="button" class="btn border p-1 ml-1" v-if="allowedModel.isDeletable && editAllowedDeviceIndex == index" @click="confirmOrgAllowedDeviceDelete(allowedModel.id)">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" fill-rule="evenodd" class="">
                                                                        <g>
                                                                            <path fill="#1B65DE" d="M9 7H4a1 1 0 0 0 0 2h22a1 1 0 0 0 0-2h-5V5c0-.796-.316-1.559-.879-2.121A2.996 2.996 0 0 0 18 2h-6c-.796 0-1.559.316-2.121.879A2.996 2.996 0 0 0 9 5zm10 0h-8V5a.997.997 0 0 1 1-1h6a.997.997 0 0 1 1 1z"
                                                                                opacity="1" data-original="#ff730a" class=""></path>
                                                                            <path fill="#989FA7" d="M24.719 11H5.281l1.551 16.284A3 3 0 0 0 9.819 30h10.362a3 3 0 0 0 2.987-2.716zm-14.602 5.11.889 8a1 1 0 0 0 1.988-.22l-.889-8a1 1 0 0 0-1.988.22zm7.778-.22-.889 8a1 1 0 0 0 1.988.22l.889-8a1 1 0 0 0-1.988-.22z"
                                                                                opacity="1" data-original="#ffa008" class=""></path>
                                                                        </g>
                                                                    </svg>
                                                                </button>
                                                                <div v-if="editAllowedDeviceIndex == index || !allowedModel.id" class="d-flex align-items-center gap-1 mt-1 rounded-pill onewhitebg px-3 py-1">
                                                                    <!-- Save Button -->
                                                                    <button v-if="editAllowedDeviceIndex == index || !allowedModel.id" type="button" @click="saveAllowedDevices(editorgdata.id, index)" class="btn d-flex align-items-center gap-1 p-1 bg-transparent border savebtn_blobal me-2">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 28 28" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                                                            <g>
                                                                            <path d="M7.8 26h12.5c2.8 0 5-2.2 5-5V9.2c0-1.1-.4-2.1-1.2-2.8l-3.3-3.2c-.4-.3-.8-.6-1.2-.8v4.2c0 1.7-1.3 3-3 3h-5.3c-1.6 0-3-1.3-3-3v-4c.1-.3-.2-.6-.5-.6-2.8 0-5 2.2-5 5v14c0 2.8 2.2 5 5 5zm1.5-12.5h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1zm0 4.7h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1z" fill="#14b009" opacity="1"></path>
                                                                            <path d="M11.4 7.5h5.3c.6 0 1-.4 1-1V2h-7.3v4.5c0 .6.4 1 1 1z" fill="#14b009" opacity="1"></path>
                                                                            </g>
                                                                        </svg>
                                                                        Save
                                                                    </button>
                                                                    <!-- Cancel Button  -->
                                                                    <button v-if="editAllowedDeviceIndex == index || !allowedModel.id" type="button"
                                                                        @click="cancelSingleEditAllowedDevice(index)"
                                                                        class="btn def_14_size d-flex align-items-center gap-1 bg-transparent border candel_blobal p-1">
                                                                        <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" id="fi_12864365">
                                                                            <linearGradient id="linear-gradient"
                                                                                gradientUnits="userSpaceOnUse" x1="376.27" x2="135.74"
                                                                                y1="376.27" y2="135.74">
                                                                                <stop offset="0" stop-color="#ef3739"></stop>
                                                                                <stop offset=".54" stop-color="#ef3739"></stop>
                                                                                <stop offset="1" stop-color="#ff8c8b"></stop>
                                                                            </linearGradient>
                                                                            <linearGradient id="linear-gradient-2"
                                                                                gradientUnits="userSpaceOnUse" x1="326.21" x2="185.8"
                                                                                y1="326.2" y2="185.8">
                                                                                <stop offset="0" stop-color="#ffd2d2"></stop>
                                                                                <stop offset=".57" stop-color="#fff"></stop>
                                                                                <stop offset="1" stop-color="#fff"></stop>
                                                                            </linearGradient>
                                                                            <g id="Layer_14" data-name="Layer 14">
                                                                                <path
                                                                                    d="m496.53 129.86c-13.44-49.47-64.89-100.93-114.37-114.37-30.34-7.56-70.98-15.35-126.16-15.49-55.16.14-95.8 7.93-126.14 15.49-49.47 13.44-100.93 64.89-114.37 114.37-7.56 30.33-15.35 70.98-15.49 126.14.14 55.17 7.93 95.81 15.49 126.15 13.44 49.48 64.9 100.93 114.37 114.37 30.34 7.56 71 15.35 126.15 15.48 55.16-.13 95.81-7.92 126.15-15.48 49.48-13.44 100.93-64.89 114.37-114.37 7.56-30.34 15.35-71 15.48-126.15-.13-55.16-7.92-95.8-15.48-126.14z"
                                                                                    fill="#ffe5e5"></path>
                                                                                <path
                                                                                    d="m256 85.93a170.08 170.08 0 1 0 170.08 170.07 170.08 170.08 0 0 0 -170.08-170.07z"
                                                                                    fill="url(#linear-gradient)"></path>
                                                                                <path d="m282.65 256 43.56-43.55a18.86 18.86 0 0 0 0-26.65 18.86 18.86 0 0 0 -26.65 0l-43.56 43.56-43.55-43.56a18.84 18.84 0 0 0 -26.65 26.65l43.56 43.55-43.56 43.56a18.85 18.85 0 0 0 0 26.64 18.84 18.84 0 0 0 26.65 0l43.55-43.55 43.55 43.55a18.84 18.84 0 1 0 26.65-26.64z" fill="url(#linear-gradient-2)"></path>
                                                                            </g>
                                                                        </svg>
                                                                        Cancel
                                                                    </button>
                                                                </div>
                                                            </td>

                                                        </tr>
                                                        <tr v-else>
                                                            <td colspan="8" class="text-center themetextcolor">No Allowed Devices Found.</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="py-2 bg-light text-center" v-if="isEditAllowedDevices">
                                                <button @click="addNewAllowedDevice" class="btn btn-sm btn-primary">
                                                    + Add New Device
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body mt-1">
                                        <div class="list-header flex justify-between align-items-center mb-2">
                                            <h5 class="def_20_size  mb-0">Renewal Allowed Devices</h5>
                                            <div class="onewhitebg rounded-pill d-flex align-items-center gap-1 px-3 py-1">
                                                <button v-if="!isEditRenewalAllowedDevices" type="button" @click="editRenewalAllowedDevices(editorgdata.id)" class="btn text-dark def_14_size d-flex align-items-center gap-1 bg-transparent p-0">
                                                    <img :src="editIc" width="25" height="25">
                                                    Edit
                                                </button>

                                                <button v-if="isEditRenewalAllowedDevices" type="button" @click="cancelEditRenewalAllowedDevices(editorgdata.id)" class="btn def_14_size d-flex align-items-center gap-1 bg-transparent border candel_blobal p-1">
                                                    <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="fi_12864365"><linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse" x1="376.27" x2="135.74" y1="376.27" y2="135.74"><stop offset="0" stop-color="#ef3739"></stop><stop offset=".54" stop-color="#ef3739"></stop><stop offset="1" stop-color="#ff8c8b"></stop></linearGradient><linearGradient id="linear-gradient-2" gradientUnits="userSpaceOnUse" x1="326.21" x2="185.8" y1="326.2" y2="185.8"><stop offset="0" stop-color="#ffd2d2"></stop><stop offset=".57" stop-color="#fff"></stop><stop offset="1" stop-color="#fff"></stop></linearGradient><g id="Layer_14" data-name="Layer 14"><path d="m496.53 129.86c-13.44-49.47-64.89-100.93-114.37-114.37-30.34-7.56-70.98-15.35-126.16-15.49-55.16.14-95.8 7.93-126.14 15.49-49.47 13.44-100.93 64.89-114.37 114.37-7.56 30.33-15.35 70.98-15.49 126.14.14 55.17 7.93 95.81 15.49 126.15 13.44 49.48 64.9 100.93 114.37 114.37 30.34 7.56 71 15.35 126.15 15.48 55.16-.13 95.81-7.92 126.15-15.48 49.48-13.44 100.93-64.89 114.37-114.37 7.56-30.34 15.35-71 15.48-126.15-.13-55.16-7.92-95.8-15.48-126.14z" fill="#ffe5e5"></path><path d="m256 85.93a170.08 170.08 0 1 0 170.08 170.07 170.08 170.08 0 0 0 -170.08-170.07z" fill="url(#linear-gradient)"></path><path d="m282.65 256 43.56-43.55a18.86 18.86 0 0 0 0-26.65 18.86 18.86 0 0 0 -26.65 0l-43.56 43.56-43.55-43.56a18.84 18.84 0 0 0 -26.65 26.65l43.56 43.55-43.56 43.56a18.85 18.85 0 0 0 0 26.64 18.84 18.84 0 0 0 26.65 0l43.55-43.55 43.55 43.55a18.84 18.84 0 1 0 26.65-26.64z" fill="url(#linear-gradient-2)"></path></g></svg>
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                        <div class="glob_scroll">
                                            <div class="table-responsive">
                                                <div class="inner_respo">
                                                    <table class="table table-bordered table-hover def_14_size">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Device Model</th>
                                                                <th>Plan Name</th>
                                                                <th>Coverage Price</th>
                                                                <th>Deductible</th>
                                                                <th>Expiration Date</th>
                                                                <th>Status</th>
                                                                <th v-if="isEditRenewalAllowedDevices">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-if="editorgdata.allowed_renewal_models && editorgdata.allowed_renewal_models.length > 0" v-for="(allowedRenewalModel, index) in editorgdata.allowed_renewal_models" :key="allowedRenewalModel.id">
                                                                <td>{{ index + 1 }}</td>
                                                                <td>
                                                                    <multiselect v-if="isEditRenewalAllowedDevices" id="tagging" v-model="allowedRenewalModel.model_id" :options="devicemodeldata" :searchable="true" label="title" track-by="id" placeholder="Search or choose device model" @search-change="updateRenewalSearchModelName" @select="handleSelect($event, true, index)" @keydown.enter.native="handleEnterKey(true, index)" @remove="clearDeviceSelection(true, index)" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :disabled="!(editRenewalDeviceIndex == index || !allowedRenewalModel.id)">
                                                                        <template #noOptions>
                                                                            <span class="custom-message">Please enter at least five characters</span>
                                                                        </template>
                                                                        <template #noResult>
                                                                            <span class="custom-message">No device models found, please try another search.</span>
                                                                        </template>
                                                                    </multiselect>
                                                                    <small v-if="validationErrors[`allowed_renewal_devices_data.${index}.deviceModelId`] && isEditRenewalAllowedDevices">
                                                                        <ErrorMessage
                                                                            :msg="validationErrors[`allowed_renewal_devices_data.${index}.deviceModelId`][0]">
                                                                        </ErrorMessage>
                                                                    </small>
                                                                    <span v-if="!isEditRenewalAllowedDevices">{{ allowedRenewalModel.allowed_renewal_model_name || '' }}</span>
                                                                </td>
                                                                <td>
                                                                    <multiselect v-if="isEditRenewalAllowedDevices"
                                                                        v-model="allowedRenewalModel.device_plan_id"
                                                                        :options="renewalPlans[index] || []"
                                                                        label="plan_name" track-by="id"
                                                                        placeholder="Select a Device Plan"
                                                                        :taggable="false" selectLabel=""
                                                                        deselectLabel=""
                                                                        @select="getDevicePlansData($event.id, 2, index)"
                                                                        @remove="clearPlans(true, index)" :disabled="!(editRenewalDeviceIndex == index || !allowedRenewalModel.id)">
                                                                        <template #noOptions>
                                                                            <span class="custom-message">No device plans
                                                                                available.</span>
                                                                        </template>
                                                                        <template #noResult>
                                                                            <span class="custom-message">No device plans found, please try another search.</span>
                                                                        </template>
                                                                    </multiselect>
                                                                    <small v-if="validationErrors[`allowed_renewal_devices_data.${index}.planId`] && isEditRenewalAllowedDevices">
                                                                        <ErrorMessage :msg="validationErrors[`allowed_renewal_devices_data.${index}.planId`][0]">
                                                                        </ErrorMessage>
                                                                    </small>
                                                                    <span v-if="!isEditRenewalAllowedDevices">{{ allowedRenewalModel.allowed_renewal_plan_name || '' }}</span>
                                                                </td>
                                                                <td>
                                                                    <input v-if="isEditRenewalAllowedDevices" type="number" v-model="allowedRenewalModel.allowed_renewal_coverage_price" min="0"
                                                                        :class="['form-control form_ctrl_w', { 'is-invalid': validationErrors[`allowed_renewal_devices_data.${index}.coveragePrice`], },]" :disabled="!(editRenewalDeviceIndex == index || !allowedRenewalModel.id)">
                                                                    <small v-if="validationErrors[`allowed_renewal_devices_data.${index}.coveragePrice`] && isEditRenewalAllowedDevices">
                                                                        <ErrorMessage
                                                                            :msg="validationErrors[`allowed_renewal_devices_data.${index}.coveragePrice`][0]">
                                                                        </ErrorMessage>
                                                                    </small>
                                                                    <span v-if="!isEditRenewalAllowedDevices">${{ allowedRenewalModel.allowed_renewal_coverage_price || '0' }}</span>
                                                                </td>
                                                                <td>
                                                                    <input v-if="isEditRenewalAllowedDevices" type="number" v-model="allowedRenewalModel.allowed_renewal_deductible_price"
                                                                        :class="['form-control form_ctrl_w', { 'is-invalid': validationErrors[`allowed_renewal_devices_data.${index}.deductiblePrice`], },]"
                                                                        min="0" :disabled="!(editRenewalDeviceIndex == index || !allowedRenewalModel.id)">
                                                                    <small v-if="validationErrors[`allowed_renewal_devices_data.${index}.deductiblePrice`] && isEditRenewalAllowedDevices">
                                                                        <ErrorMessage :msg="validationErrors[`allowed_renewal_devices_data.${index}.deductiblePrice`][0]"></ErrorMessage>
                                                                    </small>
                                                                    <span v-if="!isEditRenewalAllowedDevices">${{ allowedRenewalModel.allowed_renewal_deductible_price || '0' }}</span>
                                                                </td>
                                                                <td>
                                                                    <input v-if="isEditRenewalAllowedDevices" type="date" v-model="allowedRenewalModel.allowed_renewal_expiration_date" :class="['form-control form_ctrl_w', { 'is-invalid': validationErrors[`allowed_renewal_devices_data.${index}.expiration_date`], },]" :disabled="!(editRenewalDeviceIndex == index || !allowedRenewalModel.id)">
                                                                    <small v-if="validationErrors[`allowed_renewal_devices_data.${index}.expiration_date`] && isEditRenewalAllowedDevices">
                                                                        <ErrorMessage :msg="validationErrors[`allowed_renewal_devices_data.${index}.expiration_date`][0]"></ErrorMessage>
                                                                    </small>
                                                                    <span v-if="!isEditRenewalAllowedDevices">{{ filterFormatDate(allowedRenewalModel.allowed_renewal_expiration_date) }}</span>
                                                                </td>
                                                                <td> {{ allowedRenewalModel.id ? (allowedRenewalModel.active == 1 ? 'Active' : 'Inactive') : '' }} </td>
                                                                <td v-if="isEditRenewalAllowedDevices">
                                                                    <button type="button" v-if="editRenewalDeviceIndex != index && allowedRenewalModel.id" @click="editRenewalDevice(index)" class="customm_reset_btn btn d-flex gap-1 def_14_size gap-1 rounded-pill"><img :src="editIc" width="20" height="20"> Edit</button>
                                                                    <button type="button" v-if="editRenewalDeviceIndex == index" :class="allowedRenewalModel.id ? (allowedRenewalModel.active == 1 ? 'btn rounded-pill btn-secondary ml-1 def_14_size' : 'btn rounded-pill btn-success ml-1 def_14_size') : 'btn rounded-pill btn-danger def_14_size'"
                                                                        @click="toggleRenewalAllowedDevicesStatus(index)"> {{ allowedRenewalModel.id ? (allowedRenewalModel.active == 1 ? 'Inactive' : 'Active') : 'Remove' }}</button>
                                                                    <button type="button" class="btn border p-1 ml-1" v-if="allowedRenewalModel.isDeletable && editRenewalDeviceIndex == index" @click="confirmOrgRenewalDeviceDelete(allowedRenewalModel.id)"><svg
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            version="1.1"
                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                            width="24" height="24" x="0" y="0"
                                                                            viewBox="0 0 32 32"
                                                                            style="enable-background:new 0 0 512 512"
                                                                            xml:space="preserve" fill-rule="evenodd"
                                                                            class="">
                                                                            <g>
                                                                                <path fill="#1B65DE"
                                                                                    d="M9 7H4a1 1 0 0 0 0 2h22a1 1 0 0 0 0-2h-5V5c0-.796-.316-1.559-.879-2.121A2.996 2.996 0 0 0 18 2h-6c-.796 0-1.559.316-2.121.879A2.996 2.996 0 0 0 9 5zm10 0h-8V5a.997.997 0 0 1 1-1h6a.997.997 0 0 1 1 1z"
                                                                                    opacity="1" data-original="#ff730a"
                                                                                    class=""></path>
                                                                                <path fill="#989FA7"
                                                                                    d="M24.719 11H5.281l1.551 16.284A3 3 0 0 0 9.819 30h10.362a3 3 0 0 0 2.987-2.716zm-14.602 5.11.889 8a1 1 0 0 0 1.988-.22l-.889-8a1 1 0 0 0-1.988.22zm7.778-.22-.889 8a1 1 0 0 0 1.988.22l.889-8a1 1 0 0 0-1.988-.22z"
                                                                                    opacity="1" data-original="#ffa008"
                                                                                    class=""></path>
                                                                            </g>
                                                                        </svg>
                                                                    </button>
                                                                    <!-- Save Button -->
                                                                    <div v-if="editRenewalDeviceIndex == index || !allowedRenewalModel.id" class="d-flex align-items-center gap-1 mt-1 rounded-pill onewhitebg px-3 py-1">
                                                                        <button v-if="editRenewalDeviceIndex == index || !allowedRenewalModel.id" type="button" @click="saveRenewalAllowedDevices(editorgdata.id, index)"
                                                                            class="btn d-flex align-items-center gap-1 p-1 bg-transparent border savebtn_blobal  me-2">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0"
                                                                                viewBox="0 0 28 28" style="enable-background:new 0 0 512 512"
                                                                                xml:space="preserve" class="">
                                                                                <g>
                                                                                    <path
                                                                                        d="M7.8 26h12.5c2.8 0 5-2.2 5-5V9.2c0-1.1-.4-2.1-1.2-2.8l-3.3-3.2c-.4-.3-.8-.6-1.2-.8v4.2c0 1.7-1.3 3-3 3h-5.3c-1.6 0-3-1.3-3-3v-4c.1-.3-.2-.6-.5-.6-2.8 0-5 2.2-5 5v14c0 2.8 2.2 5 5 5zm1.5-12.5h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1zm0 4.7h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1z"
                                                                                        fill="#14b009" opacity="1" data-original="#000000"
                                                                                        class=""></path>
                                                                                    <path d="M11.4 7.5h5.3c.6 0 1-.4 1-1V2h-7.3v4.5c0 .6.4 1 1 1z" fill="#14b009" opacity="1" data-original="#000000" class=""></path>
                                                                                </g>
                                                                            </svg>
                                                                            Save
                                                                        </button>

                                                                        <!-- Cancel Button -->
                                                                        <button v-if="editRenewalDeviceIndex == index || !allowedRenewalModel.id" type="button" @click="cancelSingleEditRenewalDevice(index)" class="btn def_14_size d-flex gap-1 align-items-center bg-transparent border candel_blobal p-1">
                                                                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="fi_12864365">
                                                                                <linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse" x1="376.27" x2="135.74" y1="376.27" y2="135.74">
                                                                                    <stop offset="0" stop-color="#ef3739"></stop>
                                                                                    <stop offset=".54" stop-color="#ef3739"></stop>
                                                                                    <stop offset="1" stop-color="#ff8c8b"></stop>
                                                                                </linearGradient>
                                                                                <linearGradient id="linear-gradient-2" gradientUnits="userSpaceOnUse" x1="326.21" x2="185.8" y1="326.2" y2="185.8">
                                                                                    <stop offset="0" stop-color="#ffd2d2"></stop>
                                                                                    <stop offset=".57" stop-color="#fff"></stop>
                                                                                    <stop offset="1" stop-color="#fff"></stop>
                                                                                </linearGradient>
                                                                                <g id="Layer_14" data-name="Layer 14">
                                                                                    <path
                                                                                        d="m496.53 129.86c-13.44-49.47-64.89-100.93-114.37-114.37-30.34-7.56-70.98-15.35-126.16-15.49-55.16.14-95.8 7.93-126.14 15.49-49.47 13.44-100.93 64.89-114.37 114.37-7.56 30.33-15.35 70.98-15.49 126.14.14 55.17 7.93 95.81 15.49 126.15 13.44 49.48 64.9 100.93 114.37 114.37 30.34 7.56 71 15.35 126.15 15.48 55.16-.13 95.81-7.92 126.15-15.48 49.48-13.44 100.93-64.89 114.37-114.37 7.56-30.34 15.35-71 15.48-126.15-.13-55.16-7.92-95.8-15.48-126.14z"
                                                                                        fill="#ffe5e5"></path>
                                                                                    <path d="m256 85.93a170.08 170.08 0 1 0 170.08 170.07 170.08 170.08 0 0 0 -170.08-170.07z" fill="url(#linear-gradient)"></path>
                                                                                    <path d="m282.65 256 43.56-43.55a18.86 18.86 0 0 0 0-26.65 18.86 18.86 0 0 0 -26.65 0l-43.56 43.56-43.55-43.56a18.84 18.84 0 0 0 -26.65 26.65l43.56 43.55-43.56 43.56a18.85 18.85 0 0 0 0 26.64 18.84 18.84 0 0 0 26.65 0l43.55-43.55 43.55 43.55a18.84 18.84 0 1 0 26.65-26.64z" fill="url(#linear-gradient-2)"></path>
                                                                                </g>
                                                                            </svg>
                                                                            Cancel
                                                                        </button>
                                                                    </div>
                                                                </td>

                                                            </tr>
                                                            <tr v-else>
                                                                <td colspan="8" class="text-center themetextcolor">
                                                                    No Renewal Allowed Devices Found.
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="py-2 bg-light text-center" v-if="isEditRenewalAllowedDevices">
                                                    <button @click="addNewRenewalAllowedDevice"
                                                        class="btn btn-sm btn-primary">+ Add New Device</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="card mt-3 org_innercard">
                                <div class="card-body">
                                    <h4 class="fw-bold def_20_size themetextcolor border-bottom pb-1 mb-2">Claim & Additional Details
                                    </h4>
                                    <div class="list-header flex justify-between align-items-center mb-2">
                                        <h5 class="def_18_size mb-0">Claim Status</h5>
                                        <div class="onewhitebg rounded-pill d-flex align-items-center gap-1 px-3 py-1">
                                            <button v-if="!isEditClaimReasons" type="button" @click="editClaimReasons(editorgdata.id)"class="btn text-dark text-dark def_14_size d-flex align-items-center gap-1 bg-transparent p-0">
                                                <img :src="editIc" width="25" height="25">
                                                Edit
                                            </button>
                                            <button v-else type="button" @click="saveClaimReasons(editorgdata.id)" class="btn d-flex align-items-center gap-1 px-2  p-1 bg-transparent border savebtn_blobal  me-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 28 28" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M7.8 26h12.5c2.8 0 5-2.2 5-5V9.2c0-1.1-.4-2.1-1.2-2.8l-3.3-3.2c-.4-.3-.8-.6-1.2-.8v4.2c0 1.7-1.3 3-3 3h-5.3c-1.6 0-3-1.3-3-3v-4c.1-.3-.2-.6-.5-.6-2.8 0-5 2.2-5 5v14c0 2.8 2.2 5 5 5zm1.5-12.5h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1zm0 4.7h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1z" fill="#14b009" opacity="1" data-original="#000000" class=""></path><path d="M11.4 7.5h5.3c.6 0 1-.4 1-1V2h-7.3v4.5c0 .6.4 1 1 1z" fill="#14b009" opacity="1" data-original="#000000" class=""></path></g></svg>
                                                Save
                                            </button>
                                            <button v-if="isEditClaimReasons" type="button" @click="cancelEditClaimReasons(editorgdata.id)" class="btn def_14_size d-flex align-items-center gap-1 bg-transparent border candel_blobal p-1 px-2 ">
                                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="fi_12864365"><linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse" x1="376.27" x2="135.74" y1="376.27" y2="135.74"><stop offset="0" stop-color="#ef3739"></stop><stop offset=".54" stop-color="#ef3739"></stop><stop offset="1" stop-color="#ff8c8b"></stop></linearGradient><linearGradient id="linear-gradient-2" gradientUnits="userSpaceOnUse" x1="326.21" x2="185.8" y1="326.2" y2="185.8"><stop offset="0" stop-color="#ffd2d2"></stop><stop offset=".57" stop-color="#fff"></stop><stop offset="1" stop-color="#fff"></stop></linearGradient><g id="Layer_14" data-name="Layer 14"><path d="m496.53 129.86c-13.44-49.47-64.89-100.93-114.37-114.37-30.34-7.56-70.98-15.35-126.16-15.49-55.16.14-95.8 7.93-126.14 15.49-49.47 13.44-100.93 64.89-114.37 114.37-7.56 30.33-15.35 70.98-15.49 126.14.14 55.17 7.93 95.81 15.49 126.15 13.44 49.48 64.9 100.93 114.37 114.37 30.34 7.56 71 15.35 126.15 15.48 55.16-.13 95.81-7.92 126.15-15.48 49.48-13.44 100.93-64.89 114.37-114.37 7.56-30.34 15.35-71 15.48-126.15-.13-55.16-7.92-95.8-15.48-126.14z" fill="#ffe5e5"></path><path d="m256 85.93a170.08 170.08 0 1 0 170.08 170.07 170.08 170.08 0 0 0 -170.08-170.07z" fill="url(#linear-gradient)"></path><path d="m282.65 256 43.56-43.55a18.86 18.86 0 0 0 0-26.65 18.86 18.86 0 0 0 -26.65 0l-43.56 43.56-43.55-43.56a18.84 18.84 0 0 0 -26.65 26.65l43.56 43.55-43.56 43.56a18.85 18.85 0 0 0 0 26.64 18.84 18.84 0 0 0 26.65 0l43.55-43.55 43.55 43.55a18.84 18.84 0 1 0 26.65-26.64z" fill="url(#linear-gradient-2)"></path></g></svg>
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover def_14_size">
                                            <tbody>
                                                <tr>
                                                    <td><b>Claim Reasons:</b></td>
                                                    <td>
                                                        <!-- EDIT MODE -->
                                                        <div  v-if="isEditClaimReasons">
                                                            <div class="d-flex gap-2 flex-wrap check_data">
                                                                <!-- Claim Reasons as checkboxes with selected also -->
                                                                 <ol class="suborg_list p-0 m-0">
                                                                    <div class="form-check" v-for="(reason, rIndex) in claimreasonsdata" :key="'claim-reason-' + rIndex">
                                                                        <li class="d-flex align-items-center gap-1 ">
                                                                            <input class="" type="checkbox" :id="'claim-reason-' + rIndex" :value="reason.id" :checked="selectedClaimReasons.includes(Number(reason.id))" @change="toggleClaimReason(reason)">
                                                                            <!-- <input class="form-check-input" type="checkbox" :id="'claim-reason-' + rIndex" :value="reason.id" v-model="editorgdata.claim_reason_id" :checked="isChecked(reason.id)" > -->
                                                                            <label class="form-check-label" :for="'claim-reason-' + rIndex">{{ reason.claim_reason_name }}</label>
                                                                        </li>
                                                                    </div>
                                                                </ol>
                                                                <!-- Error message if any -->
                                                                <small v-if="validationErrors.claim_reasons_data && isEditClaimReasons">
                                                                    <ErrorMessage
                                                                        :msg="validationErrors.claim_reasons_data[0]">
                                                                    </ErrorMessage>
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <!-- END EDIT MODE -->
                                                        <!-- VIEW MODE -->
                                                        <div v-if="!isEditClaimReasons">
                                                            <div class="flex-fill onewhitebg shadow rounded p-2">
                                                             <ol v-if="editorgdata.claim_reasons_id && editorgdata.claim_reasons_id.length" class="suborg_list p-0 m-0">
                                                                <li v-if="editorgdata.claim_reasons_id && editorgdata.claim_reasons_id.length" v-for="(item, index) in editorgdata.claim_reasons_id">
                                                                    {{ item.claim_reason_name }}
                                                                </li>
                                                             </ol>
                                                                <span class="themetextcolor" v-else>N/A</span>
                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Organization type:</b></td>
                                                    <td>
                                                        <select v-if="isEditClaimReasons" v-model="editorgdata.org_type"
                                                            :class="['form-control ', { 'is-invalid': validationErrors.org_type },]">
                                                            <option value="1">School</option>
                                                            <option value="2">Non School</option>
                                                        </select>
                                                        <small v-if="validationErrors.org_type && isEditClaimReasons">
                                                            <ErrorMessage :msg="validationErrors.org_type[0]">
                                                            </ErrorMessage>
                                                        </small>
                                                        <span v-if="!isEditClaimReasons">{{ editorgdata.org_type == 1 ? 'School' : 'Non School' }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Portal Status:</b></td>
                                                    <td>
                                                        <select v-if="isEditClaimReasons" v-model="editorgdata.portal_status" :class="['form-control ', { 'is-invalid': validationErrors.portalStatus },]">
                                                            <option value="1">Open</option>
                                                            <option value="0">Close</option>
                                                        </select>
                                                        <small v-if="validationErrors.portalStatus && isEditClaimReasons">
                                                            <ErrorMessage :msg="validationErrors.portalStatus[0]"></ErrorMessage>
                                                        </small>
                                                        <span v-if="!isEditClaimReasons">{{ editorgdata.portal_status == 1 ? 'Open' : 'Close' }}</span>
                                                    </td>
                                                </tr>
                                                <tr v-if="editorgdata.portal_status == 0 && isEditClaimReasons">
                                                    <td><b>Close Portal Message:</b></td>
                                                    <td>
                                                        <textarea v-if="isEditClaimReasons && editorgdata.portal_status == 0" v-model="editorgdata.close_portal_message" :class="['form-control ', { 'is-invalid': validationErrors.close_portal_message },]" rows="3"></textarea>
                                                        <small v-if="validationErrors.close_portal_message && isEditClaimReasons">
                                                            <ErrorMessage :msg="validationErrors.close_portal_message[0]"></ErrorMessage>
                                                        </small>
                                                        <span v-if="!isEditClaimReasons">{{ editorgdata.close_portal_message || 'N/A' }}</span>
                                                    </td>
                                                </tr>
                                                <tr v-if="editorgdata.portal_status == 0 && !isEditClaimReasons">
                                                    <td><b>Close Portal Message:</b></td>
                                                    <td>
                                                        <span v-if="!isEditClaimReasons">{{ editorgdata.close_portal_message || 'N/A' }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="card-body">
                                    <div class="list-header flex justify-between align-items-center mb-2">
                                        <h5 class="def_20_size  mb-0">Additional Details</h5>
                                        <div class="onewhitebg rounded-pill d-flex align-items-center gap-1 px-3 py-1">
                                            <button v-if="!isEditAdditionalDetails" type="button" @click="editAdditionalDetails(editorgdata.id)" class="btn text-dark def_14_size d-flex align-items-center gap-1 bg-transparent p-0">
                                                <img :src="editIc" width="25" height="25">
                                                Edit
                                            </button>
                                            <button v-else type="button" @click="saveAdditionalDetails(editorgdata.id)" class="btn d-flex align-items-center gap-1 p-1 px-2  bg-transparent border savebtn_blobal  me-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 28 28" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M7.8 26h12.5c2.8 0 5-2.2 5-5V9.2c0-1.1-.4-2.1-1.2-2.8l-3.3-3.2c-.4-.3-.8-.6-1.2-.8v4.2c0 1.7-1.3 3-3 3h-5.3c-1.6 0-3-1.3-3-3v-4c.1-.3-.2-.6-.5-.6-2.8 0-5 2.2-5 5v14c0 2.8 2.2 5 5 5zm1.5-12.5h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1zm0 4.7h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1z" fill="#14b009" opacity="1" data-original="#000000" class=""></path><path d="M11.4 7.5h5.3c.6 0 1-.4 1-1V2h-7.3v4.5c0 .6.4 1 1 1z" fill="#14b009" opacity="1" data-original="#000000" class=""></path></g></svg>
                                                Save
                                            </button>
                                            <button v-if="isEditAdditionalDetails" type="button" @click="cancelEditAdditionalDetails(editorgdata.id)" class="btn d-flex gap-1 def_14_size bg-transparent border candel_blobal p-1 px-2 ">
                                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="fi_12864365"><linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse" x1="376.27" x2="135.74" y1="376.27" y2="135.74"><stop offset="0" stop-color="#ef3739"></stop><stop offset=".54" stop-color="#ef3739"></stop><stop offset="1" stop-color="#ff8c8b"></stop></linearGradient><linearGradient id="linear-gradient-2" gradientUnits="userSpaceOnUse" x1="326.21" x2="185.8" y1="326.2" y2="185.8"><stop offset="0" stop-color="#ffd2d2"></stop><stop offset=".57" stop-color="#fff"></stop><stop offset="1" stop-color="#fff"></stop></linearGradient><g id="Layer_14" data-name="Layer 14"><path d="m496.53 129.86c-13.44-49.47-64.89-100.93-114.37-114.37-30.34-7.56-70.98-15.35-126.16-15.49-55.16.14-95.8 7.93-126.14 15.49-49.47 13.44-100.93 64.89-114.37 114.37-7.56 30.33-15.35 70.98-15.49 126.14.14 55.17 7.93 95.81 15.49 126.15 13.44 49.48 64.9 100.93 114.37 114.37 30.34 7.56 71 15.35 126.15 15.48 55.16-.13 95.81-7.92 126.15-15.48 49.48-13.44 100.93-64.89 114.37-114.37 7.56-30.34 15.35-71 15.48-126.15-.13-55.16-7.92-95.8-15.48-126.14z" fill="#ffe5e5"></path><path d="m256 85.93a170.08 170.08 0 1 0 170.08 170.07 170.08 170.08 0 0 0 -170.08-170.07z" fill="url(#linear-gradient)"></path><path d="m282.65 256 43.56-43.55a18.86 18.86 0 0 0 0-26.65 18.86 18.86 0 0 0 -26.65 0l-43.56 43.56-43.55-43.56a18.84 18.84 0 0 0 -26.65 26.65l43.56 43.55-43.56 43.56a18.85 18.85 0 0 0 0 26.64 18.84 18.84 0 0 0 26.65 0l43.55-43.55 43.55 43.55a18.84 18.84 0 1 0 26.65-26.64z" fill="url(#linear-gradient-2)"></path></g></svg>
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover def_14_size">
                                            <tbody>
                                                <tr>
                                                    <td width="25%"><b>Repair Enabled:</b></td>
                                                    <td>
                                                        <select v-if="isEditAdditionalDetails"
                                                            v-model="editorgdata.repair_enabled"
                                                            :class="['form-control ', { 'is-invalid': validationErrors.repair_enabled },]">
                                                            <option value="1">Enable</option>
                                                            <option value="0">Disable</option>
                                                        </select>
                                                        <small
                                                            v-if="validationErrors.repair_enabled && isEditAdditionalDetails">
                                                            <ErrorMessage :msg="validationErrors.repair_enabled[0]">
                                                            </ErrorMessage>
                                                        </small>
                                                        <span v-if="!isEditAdditionalDetails">{{
                                                            editorgdata.repair_enabled == 1 ? 'Enable' :
                                                                'Disable' }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Can Edit Devices?:</b></td>
                                                    <td>
                                                        <select v-if="isEditAdditionalDetails" v-model="editorgdata.can_edit_device" :class="['form-control ', { 'is-invalid': validationErrors.can_edit_device },]">
                                                            <option value="1">Yes</option>
                                                            <option value="0">No</option>
                                                        </select>
                                                        <small v-if="validationErrors.can_edit_device && isEditAdditionalDetails">
                                                            <ErrorMessage :msg="validationErrors.can_edit_device[0]">
                                                            </ErrorMessage>
                                                        </small>
                                                        <span v-if="!isEditAdditionalDetails">{{ editorgdata.can_edit_device == 1 ? 'Yes' : 'No' }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Allow Parents Claim:</b></td>
                                                    <td>
                                                        <select v-if="isEditAdditionalDetails" v-model="editorgdata.allow_parents_claim" :class="['form-control ', { 'is-invalid': validationErrors.allow_parents_claim },]">
                                                            <option value="1">Allowed</option>
                                                            <option value="0">Not Allowed</option>
                                                        </select>
                                                        <small v-if="validationErrors.allow_parents_claim && isEditAdditionalDetails">
                                                            <ErrorMessage :msg="validationErrors.allow_parents_claim[0]">
                                                            </ErrorMessage>
                                                        </small>
                                                        <span v-if="!isEditAdditionalDetails">{{ editorgdata.allow_parents_claim == 1 ? 'Allowed' : 'Not Allowed' }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Additional Instructions:</b></td>
                                                    <td>
                                                        <textarea v-if="isEditAdditionalDetails" v-model="editorgdata.additional_instructions" :class="['form-control', { 'is-invalid': validationErrors.additional_instructions },]" rows="3"></textarea>
                                                        <small v-if="validationErrors.additional_instructions && isEditAdditionalDetails">
                                                            <ErrorMessage :msg="validationErrors.additional_instructions[0]">
                                                            </ErrorMessage>
                                                        </small>
                                                        <span v-if="!isEditAdditionalDetails">{{ editorgdata.additional_instructions || 'N/A' }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import { hide } from '@popperjs/core';

export default {
    props: {
        editorgdata: {
            type: Object,
        },
        claimreasonsdata: {
            type: Object,
        },
    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,

            isEditOrgDetails: false,
            isEditSubOrg: false,
            isEditAllowedDevices: false,
            editAllowedDeviceIndex: null,
            isEditRenewalAllowedDevices: false,
            editRenewalDeviceIndex: null,
            isEditClaimReasons: false,
            isEditAdditionalDetails: false,

            originalData: {}, /* To store original data for cancel operations */

            devicemodeldata: [],
            coveragePlans: [],
            renewalPlans: [],
            searchModelText: '',
            org_service_agreement: this.editorgdata.pdf_file_name ? 'pdf' : (this.editorgdata.agreement_link ? 'link' : 'none'),


            localEditOrgData: {
                ...this.editorgdata,
                logo_file: null,
                cover_file: null,
                pdf_file: null,
                org_service_agreement: this.org_service_agreement,
            },
            /** validations */
            validationErrors: {},

            selectedClaimReasons: [], // For selected claim reasons in edit mode

        };
    },
    watch: {
        editorgdata: {
            handler(newVal) {
                const agreementType = newVal.pdf_file_name
                    ? 'pdf'
                    : newVal.agreement_link
                        ? 'link'
                        : 'none';

                this.org_service_agreement = agreementType;
                this.localEditOrgData = {
                    ...newVal,
                    logo_file: this.localEditOrgData.logo_file || null,
                    cover_file: this.localEditOrgData.cover_file || null,
                    pdf_file: this.localEditOrgData.pdf_file || null,
                    org_service_agreement: this.agreementType,
                };
            },
            deep: true,
            immediate: true,
        },
        // org_service_agreement(newVal) {
        //     this.org_service_agreement = newVal;
        // }
    },
    methods: {

        /** To remove the validation errors on change of agreement type */
        handleAgreementChange() {
            this.validationErrors.agreement_link = '';
            this.validationErrors.pdf_file = '';
        },

        /** Edit Methods */
        async editOrgDetails() {
            try {


                this.isEditOrgDetails = true;
                /** Other's cancel buttons call */
                await this.cancelEditSubOrg(this.editorgdata.id);
                await this.cancelEditAllowedDevices(this.editorgdata.id);
                await this.cancelEditRenewalAllowedDevices(this.editorgdata.id);
                await this.cancelEditClaimReasons(this.editorgdata.id);
                await this.cancelEditAdditionalDetails(this.editorgdata.id);
                this.originalData.orgDetails = JSON.parse(JSON.stringify(this.editorgdata));
                if (this.editorgdata.pdf_file_name && this.editorgdata.pdf_file_path) {
                    this.org_service_agreement = 'pdf';
                } else if (this.editorgdata.agreement_link) {
                    this.org_service_agreement = 'link';
                } else {
                    this.org_service_agreement = 'none';

                }
            } catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            }

        },
        async editSubOrg(id) {
            try {
                this.isEditSubOrg = true;
                 /** Other's cancel buttons call */
                this.cancelEditOrgDetails();
                await this.cancelEditAllowedDevices(this.editorgdata.id);
                await this.cancelEditRenewalAllowedDevices(this.editorgdata.id);
                await this.cancelEditClaimReasons(this.editorgdata.id);
                await this.cancelEditAdditionalDetails(this.editorgdata.id);
                // this.editorgdata.subOrg = [];  // Reset any changes before editing
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/sub-org/${id}`);
                if (response.data.success == true) {
                    /* Reset the subOrg data to its original state */
                    this.editorgdata.subOrg = response.data.sub_organizations.map(subOrg => ({
                        id: subOrg.id,
                        name: subOrg.name,
                        active: subOrg.active,
                        isDeletable: subOrg.isDeletable
                    }));
                    /* If no sub-organizations were returned, clear the array */
                    if (!this.editorgdata.subOrg.length) {
                        this.editorgdata.subOrg = [];
                    }
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            }
        },
        async editAllowedDevices(id) {
            show_ajax_loader();
            try {
                /** Other's cancel buttons call */
                this.cancelEditOrgDetails();
                await this.cancelEditSubOrg(this.editorgdata.id);
                await this.cancelEditRenewalAllowedDevices(this.editorgdata.id);
                await this.cancelEditClaimReasons(this.editorgdata.id);
                await this.cancelEditAdditionalDetails(this.editorgdata.id);

                this.originalData.allowedModels = JSON.parse(JSON.stringify(this.editorgdata.allowed_models));
                this.isEditAllowedDevices = true;
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/allowed-devices/${id}`);
                if (response.data.success == true) {
                    /* Reset the orgAllowedModelData data to its original state */
                    this.editorgdata.allowed_models = [];
                    /* Push all orgAllowedModelData from response */
                    this.editorgdata.allowed_models = response.data.orgAllowedModelData.map(allowed => ({
                            id: allowed.id,
                            model_id: allowed.model_id,
                            device_plan_id: allowed.device_plan_id,
                            allowed_model_name: allowed.allowed_model_name,
                            allowed_plan_name: allowed.allowed_plan_name,
                            allowed_coverage_price: allowed.allowed_coverage_price,
                            allowed_deductible_price: allowed.allowed_deductible_price,
                            allowed_expiration_date: allowed.allowed_expiration_date,
                            active: allowed.active,
                            isDeletable: allowed.isDeletable
                    }));
                    const modelIDs = this.editorgdata.allowed_models.map(d => d.model_id); // collect all model_ids
                    for (let i = 0; i < modelIDs.length; i++) {
                        const id = modelIDs[i];
                        this.getDeviceModels(false, id); // Wait for device model to load
                        this.coveragePlans[i] = [];
                        this.getDevicePlan(id, 1, i); // You can still run this without waiting

                    }
                    /* If no sub-organizations were returned, clear the array */
                    if (!this.editorgdata.allowed_models.length) {
                        this.editorgdata.allowed_models = [];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            }
        },
        /** Edit Single Allowed Device */
        editAllowedDevice(index) {
            this.editAllowedDeviceIndex = index;
        },
        async editRenewalAllowedDevices(id) {
            try {
                show_ajax_loader();

                /** Other's cancel buttons call */
                this.cancelEditOrgDetails();
                await this.cancelEditSubOrg(this.editorgdata.id);
                await this.cancelEditAllowedDevices(this.editorgdata.id);
                await this.cancelEditClaimReasons(this.editorgdata.id);
                await this.cancelEditAdditionalDetails(this.editorgdata.id);

                this.originalData.allowedRenewalModels = JSON.parse(JSON.stringify(this.editorgdata.allowed_renewal_models));

                this.isEditRenewalAllowedDevices = true;
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/renewal-devices/${id}`);
                if (response.data.success == true) {
                    /* Reset the orgRenewalModelData data to its original state */
                    this.editorgdata.allowed_renewal_models = [];
                    /* Push all orgRenewalModelData from response */
                    this.editorgdata.allowed_renewal_models = response.data.orgRenewalModelData.map(renewal => ({
                        id: renewal.id,
                        model_id: renewal.model_id,
                        device_plan_id: renewal.device_plan_id,
                        allowed_renewal_model_name: renewal.allowed_renewal_model_name,
                        allowed_renewal_plan_name: renewal.allowed_renewal_plan_name,
                        allowed_renewal_coverage_price: renewal.allowed_renewal_coverage_price,
                        allowed_renewal_deductible_price: renewal.allowed_renewal_deductible_price,
                        allowed_renewal_expiration_date: renewal.allowed_renewal_expiration_date,
                        active: renewal.active,
                        isDeletable: renewal.isDeletable
                    }));
                    const modelIDs = this.editorgdata.allowed_renewal_models.map(d => d.model_id); // collect all model_ids
                    for (let i = 0; i < modelIDs.length; i++) {
                        const id = modelIDs[i];
                        this.getDeviceModels(true, id); // Wait for device model to load
                        this.renewalPlans[i] = [];
                        this.getDevicePlan(id, 2, i); // You can still run this without waiting
                    }
                    /* If no sub-organizations were returned, clear the array */
                    if (!this.editorgdata.allowed_renewal_models.length) {
                        this.editorgdata.allowed_renewal_models = [];
                    }
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            }
        },
        /** Edit Single Renewal Device */
        editRenewalDevice(index) {
            this.editRenewalDeviceIndex = index;
        },
        async editClaimReasons(id) {
            try {

                show_ajax_loader();
                this.cancelEditOrgDetails();
                await this.cancelEditSubOrg(this.editorgdata.id);
                await this.cancelEditAllowedDevices(this.editorgdata.id);
                await this.cancelEditRenewalAllowedDevices(this.editorgdata.id);
                await this.cancelEditAdditionalDetails(this.editorgdata.id);

                this.isEditClaimReasons = true;
                this.selectedClaimReasons = this.editorgdata.claim_reasons_id.map(item => Number(item.claim_reason_id)); // Initialize selectedClaimReasons with IDs
                /** Other's cancel buttons call */

                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/claim-reasons-msg/${id}`);
                if (response.data.success == true) {
                    /* Reset fields */
                    /* Clear existing claim_reasons_id array first */
                    this.editorgdata.claim_reasons_id = [];
                    /* Push all claim reasons from response */
                    response.data.updatedClaimIds.forEach(claim => {
                        this.editorgdata.claim_reasons_id.push({
                            id: claim.id,
                            claim_reason_name: claim.claim_reason_name,
                            claim_reason_id: claim.claim_reason_id,
                            active: claim.active,
                            isDeletable: claim.isDeletable
                        });
                    });
                    this.editorgdata.org_type = response.data.organizationData.org_type;
                    this.editorgdata.portal_status = response.data.organizationData.portal_status;
                    this.editorgdata.close_portal_message = response.data.organizationData.close_portal_message;
                    hide_ajax_loader();
                }else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            }
        },

        async editAdditionalDetails(id) {
            try {
                show_ajax_loader();
                /** Other's cancel buttons call */
                this.cancelEditOrgDetails();
                await this.cancelEditSubOrg(this.editorgdata.id);
                await this.cancelEditAllowedDevices(this.editorgdata.id);
                await this.cancelEditRenewalAllowedDevices(this.editorgdata.id);
                await this.cancelEditClaimReasons(this.editorgdata.id);

                this.isEditAdditionalDetails = true;

                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/additional-details/${id}`);
                if (response.data.success == true) {
                    this.editorgdata.repair_enabled = response.data.orgData.repair_enabled;
                    this.editorgdata.can_edit_device = response.data.orgData.can_edit_device;
                    this.editorgdata.allow_parents_claim = response.data.orgData.allow_parents_claim;
                    this.editorgdata.additional_instructions = response.data.orgData.additional_instructions;
                    hide_ajax_loader();
                }else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            }
        },
        getSelectedReasonObject() {
            show_ajax_loader();

            // Create a new array of selected reason objects
            const selectedObjects = [];
            this.editorgdata.claim_reasons_id.forEach((claim, index) => {
                const reasonObj = this.claimreasonsdata.find(item => item.id == Number(claim.claim_reason_id));
                if (reasonObj) {
                    this.editorgdata.claim_reasons_id = reasonObj; // Replace ID with object
                }
            });
            hide_ajax_loader();
        },

        isChecked(id) {
            return this.selectedClaimReasons.includes(id);
        },
        toggleClaimReason(reason) {
            const reasonId = Number(reason.id);
            const index = this.editorgdata.claim_reasons_id.findIndex(item => Number(item.claim_reason_id) == reasonId);
            if (index == -1) {
               /*  this.editorgdata.claim_reasons_id.push({
                    'claim_reason_id': reasonId,
                    'claim_reason_name': reason.claim_reason_name,
                }); */
                this.selectedClaimReasons.push(reasonId);
            } else {
                // this.editorgdata.claim_reasons_id.splice(index, 1);
                this.selectedClaimReasons = this.selectedClaimReasons.filter(id => id != reasonId);
            }
        },
        /** Sub-Organization Methods */
        addNewSubOrg() {
            this.editorgdata.subOrg.push({ id: null, name: '', active: 1 });
        },
        toggleSubOrgStatus(index) {
            const subOrg = this.editorgdata.subOrg[index];
            if (!subOrg.id) {
                /* Remove new sub-orgs */
                this.editorgdata.subOrg.splice(index, 1);
            } else {
                /* Toggle between 1 (Active) and 0 (Inactive) */
                subOrg.active = subOrg.active == 1 ? 0 : 1;
            }
        },

        /** Device Management Methods */
        addNewAllowedDevice() {
            if (!this.editorgdata.allowed_models) {
                this.editorgdata.allowed_models = [];
            }
            /* Prevent adding if an unsaved device already exists */
            /* const hasUnsavedRow = this.editorgdata.allowed_models.some(model => !model.id);
            if (hasUnsavedRow) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Please save the current device before adding another one.";
                return;
            } */
            // this.devicemodeldata = [];
            this.editorgdata.allowed_models.push({
                model_id: '',
                device_plan_id: '',
                allowed_model_name: '',
                allowed_plan_name: '',
                allowed_coverage_price: 0,
                allowed_deductible_price: 0,
                allowed_expiration_date: '',
                active: 1,
            });
            /** Call get models */
            this.getDeviceModels(false);
        },
        toggleAllowedDevicesStatus(index) {
            const allowedDevice = this.editorgdata.allowed_models[index];
            if (!allowedDevice.id) {
                /* Remove new allowed devices */
                /* this.editorgdata.allowed_models.splice(index, 1); */
                this.editAllowedDeviceIndex = null;
            } else {
                /* Toggle active status for existing allowed devices */
                allowedDevice.active = allowedDevice.active == 1 ? 0 : 1;
            }
        },

        addNewRenewalAllowedDevice() {
            if (!this.editorgdata.allowed_renewal_models) {
                this.editorgdata.allowed_renewal_models = [];
            }
            // this.devicemodeldata = [];
            this.editorgdata.allowed_renewal_models.push({
                model_id: '',
                device_plan_id: '',
                allowed_renewal_model_name: '',
                allowed_renewal_plan_name: '',
                allowed_renewal_coverage_price: 0,
                allowed_renewal_deductible_price: 0,
                allowed_renewal_expiration_date: '',
                active: 1,
            });
            /** Call get models */
            this.getDeviceModels(true);
        },
        toggleRenewalAllowedDevicesStatus(index) {
            const allowedRenewalDevice = this.editorgdata.allowed_renewal_models[index];
            if (!allowedRenewalDevice.id) {
                /* Remove new allowed devices */
                this.editorgdata.allowed_renewal_models.splice(index, 1);
                this.editRenewalDeviceIndex = null;
            } else {
                /* Toggle active status for existing allowed devices */
                allowedRenewalDevice.active = allowedRenewalDevice.active == 1 ? 0 : 1;
            }
        },

        addNewClaimReason() {
            if (!this.editorgdata.claim_reasons_id) {
                this.editorgdata.claim_reasons_id = [];
            }

            // Get the unselected ones
            const selectedIds = this.editorgdata.claim_reasons_id.map(item => item.claim_reason_id);
            const remaining = this.claimreasonsdata.filter(cr => !selectedIds.includes(cr.id));

            if (remaining.length > 0) {
                // Push the first remaining option with active status
                this.editorgdata.claim_reasons_id.push({
                    id: null,
                    claim_reason_id: remaining[0].id,
                    claim_reason_name: remaining[0].claim_reason_name,
                    active: 1,
                });
                // this.editorgdata.claim_reasons_id.push({ ...remaining[0], active: 1 });
            }
        },
        toggleClaimReasonStatus(index) {
            const claimReason = this.editorgdata.claim_reasons_id[index];
            if (!claimReason.id) {
                /* Remove new claim reason */
                this.editorgdata.claim_reasons_id.splice(index, 1);
            } else {
                /* Toggle active status for existing claim reasons */
                claimReason.active = claimReason.active == 1 ? 0 : 1;
            }
        },

        /** File Upload Methods */
        handleLogoUpload(event) {
            // Handle logo file upload
            const file = event.target.files[0];
            if (file) {
                // Process the file (upload to server, preview, etc.)
                this.localEditOrgData.logo_file = file;
                /* For preview: */
                // this.editorgdata.logo_file_name = URL.createObjectURL(file);
            }
        },
        handleCoverUpload(event) {
            /* Handle cover image file upload */
            const file = event.target.files[0];
            if (file) {
                // this.editorgdata.cover_file_name = URL.createObjectURL(file);
                this.localEditOrgData.cover_file = file;
            }
        },
        handlePdfUpload(event) {
            /* Handle PDF file upload */
            const file = event.target.files[0];
            if (file) {
                this.localEditOrgData.pdf_file = file;
                // this.editorgdata.pdf_file_name = file.name;
            }
        },

        async downlaodPdf(filepath, filename) {
            try {
                let fullPath = filepath.startsWith('/') ? filepath.substring(1) : filepath;
                const response = await axios.get(`${window.location.origin}/${fullPath}${filename}`, {
                    responseType: 'blob',
                });
                const blob = new Blob([response.data], { type: 'application/pdf' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = filename;
                link.click();
            } catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Failed to download PDF.";
            }
        },

        /** Save Methods */
        async saveOrgDetails(id) {
            show_ajax_loader();
            /** Validations */
            this.validationErrors = {};
            let validationPassed = true;
            if (!this.localEditOrgData.org_name) {
                this.validationErrors.org_name = ["Organization name is required."];
            }

            if (!this.localEditOrgData.pdf_file && this.org_service_agreement == 'pdf') {
                this.validationErrors.pdf_file = ["PDF file is required."];

                validationPassed = false;
            }
            if (!this.localEditOrgData.agreement_link && this.org_service_agreement == 'link') {
                this.validationErrors.agreement_link = ["Agreement link is required."];
                validationPassed = false;
            }

            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                const orgDetails = {
                    'org_name': this.localEditOrgData.org_name,
                    'org_logo': this.localEditOrgData.logo_file,
                    'org_cover_image': this.localEditOrgData.cover_file,
                    'org_service_agreement': this.org_service_agreement,
                    'org_agreement_pdf': this.localEditOrgData.pdf_file,
                    'org_agreement_link': this.localEditOrgData.agreement_link,
                };
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/organizations/update-org-details/${id}`, orgDetails,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                    }
                );
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;

                    this.editorgdata.org_name = response.data.orgData.name;
                    this.editorgdata.logo_file_name = response.data.orgData.logo_file_name;
                    this.editorgdata.logo_file_path = response.data.orgData.logo_file_path;

                    this.editorgdata.cover_file_name = response.data.orgData.cover_file_name;
                    this.editorgdata.cover_file_path = response.data.orgData.cover_file_path;

                    this.editorgdata.org_service_agreement = response.data.orgData.pdf_file_name ? 'pdf' : (response.data.orgData.agreement_link ? 'link' : 'none');

                    this.editorgdata.pdf_file_name = response.data.orgData.pdf_file_name;
                    this.editorgdata.pdf_file_path = response.data.orgData.pdf_file_path;
                    this.editorgdata.agreement_link = response.data.orgData.agreement_link ?? '';
                    // Turn off all edit flags
                    this.isEditOrgDetails = false;
                    this.isEditSubOrg = false;
                    this.isEditAllowedDevices = false;
                    this.isEditRenewalAllowedDevices = false;
                    this.isEditClaimReasons = false;
                    this.isEditAdditionalDetails = false;
                } else if (response.data.errors && response.data.success == false) {
                    this.validationErrors = response.data.errors;
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** Save Sub-Organization */
        async saveSubOrg(id) {
            show_ajax_loader();
            /** Validations */
            this.validationErrors = {};
            let validationPassed = true;
            if (this.localEditOrgData.subOrg && this.localEditOrgData.subOrg.length > 0) {
                this.localEditOrgData.subOrg.forEach((subOrg, index) => {
                    if (!subOrg.name) {
                        this.validationErrors[`sub_org_data.${index}.name`] = ['Please enter sub organization name.'];
                        validationPassed = false;
                    }
                    if (subOrg.name.length > 50) {
                        this.validationErrors[`sub_org_data.${index}.name`] = ['Sub organization name should not exceed 50 characters.'];
                        validationPassed = false;
                    }
                });
            }
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                const subOrgDetails = {
                    'sub_org_data': this.localEditOrgData.subOrg,
                };
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/organizations/update-sub-org-details/${id}`, subOrgDetails);
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    // Clear existing subOrg array first if needed
                    this.editorgdata.subOrg = [];

                    // Push all sub-organizations from response
                    response.data.sub_organizations.forEach(subOrg => {
                        this.editorgdata.subOrg.push({
                            id: subOrg.id,
                            name: subOrg.name,
                            active: subOrg.active,
                            isDeletable: subOrg.isDeletable
                        });
                    });
                    /* Turn off all edit flags */
                    this.isEditOrgDetails = false;
                    this.isEditSubOrg = false;
                    this.isEditAllowedDevices = false;
                    this.isEditRenewalAllowedDevices = false;
                    this.isEditClaimReasons = false;
                    this.isEditAdditionalDetails = false;
                } else if (response.data.errors && response.data.success == false) {
                    this.validationErrors = response.data.errors;
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** Delete Sub-Org */
        confirmSubOrgDelete(id) {
            this.$deleteAlertMessage.data.isDelete = true
            this.$deleteAlertMessage.data.isChangeStatus = false
            this.$deleteAlertMessage.data.itemId = id
            this.$deleteAlertMessage.data.heading = 'Delete Sub-Organization'
            this.$deleteAlertMessage.data.message = 'Are you sure you want to delete this Sub-Organization?'
            this.$deleteAlertMessage.data.callback = this.subOrgDelete
        },

        async subOrgDelete(id) {
            show_ajax_loader();
            /* Client-side validation */
            let validationPassed = true;

            /* Validate Sub-org Id */
            if (!id) {
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
                return;
            }

            try {
                const response = await axios.delete(`${this.$userAppUrl}smarttiusadmin/organizations/delete-sub-org/${id}`, {
                    params: {
                        orgId: this.editorgdata.id
                    }
                });
                if (response && response.data) {
                    if (response.data.success == true) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        /* Clear existing subOrg array first if needed */
                        this.editorgdata.subOrg = [];
                        if (response.data.sub_organizations.length > 0) {
                            // Push all sub-organizations from response
                            response.data.sub_organizations.forEach(subOrg => {
                                this.editorgdata.subOrg.push({
                                    id: subOrg.id,
                                    name: subOrg.name,
                                    active: subOrg.active,
                                    isDeletable: subOrg.isDeletable
                                });
                            });

                        }else {
                            this.editorgdata.subOrg = [];
                        }
                        /* Turn off all edit flags */
                        this.isEditOrgDetails = false;
                        this.isEditSubOrg = false;
                        this.isEditAllowedDevices = false;
                        this.isEditRenewalAllowedDevices = false;
                        this.isEditClaimReasons = false;
                        this.isEditAdditionalDetails = false;
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin/`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        /** Save Allowed Devices */
        async saveAllowedDevices(id, index) {
            show_ajax_loader();
            /** Validations */
            this.validationErrors = {};
            let validationPassed = true;
           /*  if (this.localEditOrgData.allowed_models && this.localEditOrgData.allowed_models.length > 0) {
                this.localEditOrgData.allowed_models.forEach((allowedModel, index) => {
                    if (!allowedModel.model_id) {
                        this.validationErrors[`allowed_devices_data.${index}.deviceModelId`] = ['Please select device model.'];
                        validationPassed = false;
                    }
                    if (!allowedModel.device_plan_id) {
                        this.validationErrors[`allowed_devices_data.${index}.planId`] = ['Please select device plan.'];
                        validationPassed = false;
                    }
                    if (!allowedModel.allowed_coverage_price) {
                        this.validationErrors[`allowed_devices_data.${index}.coverage_price`] = ['Please enter coverage price.'];
                        validationPassed = false;
                    }
                    if (allowedModel.allowed_coverage_price < 0) {
                        this.validationErrors[`allowed_devices_data.${index}.coverage_price`] = ['Coverage price should be greater than or equal to 0.'];
                        validationPassed = false;
                    }
                    if (allowedModel.allowed_deductible_price < 0) {
                        this.validationErrors[`allowed_devices_data.${index}.deductible`] = ['Deductible price should be greater than or equal to 0.'];
                        validationPassed = false;
                    }
                    if (!allowedModel.allowed_expiration_days) {
                        this.validationErrors[`allowed_devices_data.${index}.expiration_days`] = ['Please enter expiration days.'];
                        validationPassed = false;
                    }
                    if (allowedModel.allowed_expiration_days < 0) {
                        this.validationErrors[`allowed_devices_data.${index}.expiration_days`] = ['Expiration days should be greater than or equal to 0.'];
                        validationPassed = false;
                    }
                });
            } */



            const allowedModel = this.localEditOrgData.allowed_models[index];

            if (!allowedModel.model_id) {
                this.validationErrors[`allowed_devices_data.${index}.deviceModelId`] = ['Please select device model.'];
                validationPassed = false;
            }
            if (!allowedModel.device_plan_id) {
                this.validationErrors[`allowed_devices_data.${index}.planId`] = ['Please select device plan.'];
                validationPassed = false;
            }
            if (allowedModel.allowed_coverage_price == '' || allowedModel.allowed_coverage_price == null || allowedModel.allowed_coverage_price == undefined) {
                this.validationErrors[`allowed_devices_data.${index}.coverage_price`] = ['Please enter coverage price.'];
                validationPassed = false;
            } else if (allowedModel.allowed_coverage_price < 0) {
                this.validationErrors[`allowed_devices_data.${index}.coverage_price`] = ['Coverage price should be greater than or equal to 0.'];
                validationPassed = false;
            }
            if (allowedModel.allowed_deductible_price < 0) {
                this.validationErrors[`allowed_devices_data.${index}.deductible`] = ['Deductible price should be greater than or equal to 0.'];
                validationPassed = false;
            }
            if (allowedModel.allowed_expiration_date == '' || allowedModel.allowed_expiration_date == null || allowedModel.allowed_expiration_date == undefined) {
                this.validationErrors[`allowed_devices_data.${index}.expiration_date`] = ['Please enter expiration date.'];
                validationPassed = false;
            }
          /*   else if (allowedModel.allowed_expiration_date < 0) {
                this.validationErrors[`allowed_devices_data.${index}.expiration_date`] = ['Expiration days should be greater than or equal to 0.'];
                validationPassed = false;
            } */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                const allowedDevicesData = {
                    /* Send only the required data */
                   /*  'allowed_devices_data': this.localEditOrgData.allowed_models[index].map(device => {
                        return {
                            deviceId: device.id ?? null,
                            deviceModelId: device.model_id.id,
                            planId: device.device_plan_id.id,
                            coverage_price: device.allowed_coverage_price,
                            deductible: device.allowed_deductible_price,
                            expiration_days: device.allowed_expiration_days,
                            active: device.active,
                        };
                    }), */
                    /* 'allowed_devices_data': [
                        { */
                            deviceId: this.localEditOrgData.allowed_models[index].id ?? null,
                            deviceModelId: this.localEditOrgData.allowed_models[index].model_id.id,
                            planId: this.localEditOrgData.allowed_models[index].device_plan_id.id,
                            coverage_price: this.localEditOrgData.allowed_models[index].allowed_coverage_price,
                            deductible: this.localEditOrgData.allowed_models[index].allowed_deductible_price,
                            expiration_date: this.localEditOrgData.allowed_models[index].allowed_expiration_date,
                            active: this.localEditOrgData.allowed_models[index].active,
                    /*  }
                    ], */

                };
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/organizations/update-allowed-devices/${id}`, allowedDevicesData);
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Clear existing allowed_models array first if needed */
                    this.editorgdata.allowed_models = [];
                    /* Push all orgAllowedModelData from response */
                    response.data.updatedAllowedDevicesData.forEach(allowed => {
                        this.editorgdata.allowed_models.push({
                            id: allowed.id,
                            model_id: allowed.model_id,
                            device_plan_id: allowed.device_plan_id,
                            allowed_model_name: allowed.allowed_model_name,
                            allowed_plan_name: allowed.allowed_plan_name,
                            allowed_coverage_price: allowed.allowed_coverage_price,
                            allowed_deductible_price: allowed.allowed_deductible_price,
                            allowed_expiration_date: allowed.allowed_expiration_date,
                            active: allowed.active,
                            isDeletable: allowed.isDeletable
                        });
                    });
                    // Turn off all edit flags
                    this.isEditOrgDetails = false;
                    this.isEditSubOrg = false;
                    this.isEditAllowedDevices = false;
                    this.isEditRenewalAllowedDevices = false;
                    this.isEditClaimReasons = false;
                    this.isEditAdditionalDetails = false;
                } else if (response.data.errors && response.data.success == false) {
                   /*  this.validationErrors = response.data.errors;
                    hide_ajax_loader(); */
                    const backendErrors = response.data.errors;
                    const indexedErrors = {};
                    const indexPrefix = `allowed_devices_data.${index}.`;

                    for (const [field, messages] of Object.entries(backendErrors)) {
                        indexedErrors[`${indexPrefix}${field}`] = messages;
                    }

                    this.validationErrors = indexedErrors;
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** Delete Org-Allowed Devices */
        confirmOrgAllowedDeviceDelete(id) {
            this.$deleteAlertMessage.data.isDelete = true
            this.$deleteAlertMessage.data.isChangeStatus = false
            this.$deleteAlertMessage.data.itemId = id
            this.$deleteAlertMessage.data.heading = 'Delete Allowed Device Model'
            this.$deleteAlertMessage.data.message = 'Are you sure you want to delete this device plan?'
            this.$deleteAlertMessage.data.callback = this.orgAllowedDeviceDelete
        },

        async orgAllowedDeviceDelete(id) {
            show_ajax_loader();
            /* Client-side validation */
            let validationPassed = true;

            /* Validate Sub-org Id */
            if (!id) {
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
                return;
            }

            try {
                const response = await axios.delete(`${this.$userAppUrl}smarttiusadmin/organizations/delete-allowed-device/${id}`, {
                    params: {
                        orgId: this.editorgdata.id
                    }
                });
                if (response && response.data) {
                    if (response.data.success == true) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        /* Clear existing allowed_models array first if needed */
                        this.editorgdata.allowed_models = [];
                        /* Push all orgAllowedModelData from response */
                        response.data.orgAllowedModelData.forEach(allowed => {
                            this.editorgdata.allowed_models.push({
                                id: allowed.id,
                                model_id: allowed.model_id,
                                device_plan_id: allowed.device_plan_id,
                                allowed_model_name: allowed.allowed_model_name,
                                allowed_plan_name: allowed.allowed_plan_name,
                                allowed_coverage_price: allowed.allowed_coverage_price,
                                allowed_deductible_price: allowed.allowed_deductible_price,
                                allowed_expiration_date: allowed.allowed_expiration_date,
                                active: allowed.active,
                                isDeletable: allowed.isDeletable
                            });
                        });
                        /* Turn off all edit flags */
                        this.isEditOrgDetails = false;
                        this.isEditSubOrg = false;
                        this.isEditAllowedDevices = false;
                        this.isEditRenewalAllowedDevices = false;
                        this.isEditClaimReasons = false;
                        this.isEditAdditionalDetails = false;
                        this.editAllowedDeviceIndex = null;
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                        4000
                    );

                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        async saveRenewalAllowedDevices(id, index) {
            show_ajax_loader();
            /** Validations */
            this.validationErrors = {};
            let validationPassed = true;

            /* if (this.localEditOrgData.allowed_renewal_models && this.localEditOrgData.allowed_renewal_models.length > 0) {
                this.localEditOrgData.allowed_renewal_models.forEach((allowedModel, index) => {
                    if (!allowedModel.model_id) {
                        this.validationErrors[`allowed_renewal_devices_data.${index}.deviceModelId`] = ['Please select device model.'];
                        validationPassed = false;
                    }
                    if (!allowedModel.device_plan_id) {
                        this.validationErrors[`allowed_renewal_devices_data.${index}.planId`] = ['Please select device plan.'];
                        validationPassed = false;
                    }
                    if (!allowedModel.allowed_renewal_coverage_price) {
                        this.validationErrors[`allowed_renewal_devices_data.${index}.coverage_price`] = ['Please enter coverage price.'];
                        validationPassed = false;
                    }
                    if (allowedModel.allowed_renewal_coverage_price < 0) {
                        this.validationErrors[`allowed_renewal_devices_data.${index}.coverage_price`] = ['Coverage price should be greater than or equal to 0.'];
                        validationPassed = false;
                    }
                    if (allowedModel.allowed_renewal_deductible_price < 0) {
                        this.validationErrors[`allowed_renewal_devices_data.${index}.deductible`] = ['Deductible price should be greater than or equal to 0.'];
                        validationPassed = false;
                    }
                    if (!allowedModel.allowed_renewal_expiration_days) {
                        this.validationErrors[`allowed_renewal_devices_data.${index}.expiration_days`] = ['Please enter expiration days.'];
                        validationPassed = false;
                    }
                    if (allowedModel.allowed_renewal_expiration_days < 0) {
                        this.validationErrors[`allowed_renewal_devices_data.${index}.expiration_days`] = ['Expiration days should be greater than or equal to 0.'];
                        validationPassed = false;
                    }
                });
            } */
            const allowedRenewalModel = this.localEditOrgData.allowed_renewal_models[index];
            if (!allowedRenewalModel.model_id) {
                this.validationErrors[`allowed_renewal_devices_data.${index}.deviceModelId`] = ['Please select device model.'];
                validationPassed = false;
            }
            if (!allowedRenewalModel.device_plan_id) {
                this.validationErrors[`allowed_renewal_devices_data.${index}.planId`] = ['Please select device plan.'];
                validationPassed = false;
            }
            if (allowedRenewalModel.allowed_renewal_coverage_price == '' || allowedRenewalModel.allowed_renewal_coverage_price == null || allowedRenewalModel.allowed_renewal_coverage_price == undefined) {
                this.validationErrors[`allowed_renewal_devices_data.${index}.coverage_price`] = ['Please enter coverage price.'];
                validationPassed = false;
            } else if (allowedRenewalModel.allowed_renewal_coverage_price < 0) {
                this.validationErrors[`allowed_renewal_devices_data.${index}.coverage_price`] = ['Coverage price should be greater than or equal to 0.'];
                validationPassed = false;
            }
            if (allowedRenewalModel.allowed_renewal_deductible_price < 0) {
                this.validationErrors[`allowed_renewal_devices_data.${index}.deductible`] = ['Deductible price should be greater than or equal to 0.'];
                validationPassed = false;
            }
            if (allowedRenewalModel.allowed_renewal_expiration_date == '' || allowedRenewalModel.allowed_renewal_expiration_date == null || allowedRenewalModel.allowed_renewal_expiration_date == undefined) {
                this.validationErrors[`allowed_renewal_devices_data.${index}.expiration_date`] = ['Please enter expiration date.'];
                validationPassed = false;
            }
            /* else if (allowedRenewalModel.allowed_renewal_expiration_date < 0) {
                this.validationErrors[`allowed_renewal_devices_data.${index}.expiration_date`] = ['Expiration days should be greater than or equal to 0.'];
                validationPassed = false;
            } */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                const allowedRenwalDevicesData = {
                    /* 'allowed_renewal_devices_data': this.localEditOrgData.allowed_renewal_models.map(device => {
                        return {
                            deviceId: device.id ?? null,
                            deviceModelId: device.model_id.id,
                            planId: device.device_plan_id.id,
                            coverage_price: device.allowed_renewal_coverage_price,
                            deductible: device.allowed_renewal_deductible_price,
                            expiration_date: device.allowed_renewal_expiration_days,
                            active: device.active,
                        };
                    }), */
                    /* 'allowed_renewal_devices_data': [
                        { */
                            deviceId: this.localEditOrgData.allowed_renewal_models[index].id ?? null,
                            deviceModelId: this.localEditOrgData.allowed_renewal_models[index].model_id.id,
                            planId: this.localEditOrgData.allowed_renewal_models[index].device_plan_id.id,
                            coverage_price: this.localEditOrgData.allowed_renewal_models[index].allowed_renewal_coverage_price,
                            deductible: this.localEditOrgData.allowed_renewal_models[index].allowed_renewal_deductible_price,
                            expiration_date: this.localEditOrgData.allowed_renewal_models[index].allowed_renewal_expiration_date,
                            active: this.localEditOrgData.allowed_renewal_models[index].active,
                     /*    }
                    ], */
                };
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/organizations/update-renewal-allowed-devices/${id}`, allowedRenwalDevicesData);
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Clear existing allowed_models array first if needed */
                    this.editorgdata.allowed_renewal_models = [];
                    /* Push all orgAllowedModelData from response */
                    response.data.updatedRenewalDevicesData.forEach(renewal => {
                        this.editorgdata.allowed_renewal_models.push({
                            id: renewal.id,
                            model_id: renewal.model_id,
                            device_plan_id: renewal.device_plan_id,
                            allowed_renewal_model_name: renewal.allowed_renewal_model_name,
                            allowed_renewal_plan_name: renewal.allowed_renewal_plan_name,
                            allowed_renewal_coverage_price: renewal.allowed_renewal_coverage_price,
                            allowed_renewal_deductible_price: renewal.allowed_renewal_deductible_price,
                            allowed_renewal_expiration_date: renewal.allowed_renewal_expiration_date,
                            active: renewal.active,
                            isDeletable: renewal.isDeletable
                        });
                    });
                    // Turn off all edit flags
                    this.isEditOrgDetails = false;
                    this.isEditSubOrg = false;
                    this.isEditAllowedDevices = false;
                    this.isEditRenewalAllowedDevices = false;
                    this.isEditClaimReasons = false;
                    this.isEditAdditionalDetails = false;
                } else if (response.data.errors && response.data.success == false) {
                    /* this.validationErrors = response.data.errors;
                    hide_ajax_loader(); */
                    const backendErrors = response.data.errors;
                    const indexedErrors = {};
                    const indexPrefix = `allowed_renewal_devices_data.${index}.`;
                    for (const [field, messages] of Object.entries(backendErrors)) {
                        indexedErrors[`${indexPrefix}${field}`] = messages;
                    }
                    this.validationErrors = indexedErrors;
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** Delete Org-Renewal Devices */
        confirmOrgRenewalDeviceDelete(id) {
            this.$deleteAlertMessage.data.isDelete = true
            this.$deleteAlertMessage.data.isChangeStatus = false
            this.$deleteAlertMessage.data.itemId = id
            this.$deleteAlertMessage.data.heading = 'Delete Renewal Device Model'
            this.$deleteAlertMessage.data.message = 'Are you sure you want to delete this device plan?'
            this.$deleteAlertMessage.data.callback = this.orgRenewalDeviceDelete
        },

        async orgRenewalDeviceDelete(id) {
            show_ajax_loader();
            /* Client-side validation */
            let validationPassed = true;

            /* Validate Sub-org Id */
            if (!id) {
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
                return;
            }

            try {
                const response = await axios.delete(`${this.$userAppUrl}smarttiusadmin/organizations/delete-renewal-device/${id}`, {
                    params: {
                        orgId: this.editorgdata.id
                    }
                });
                if (response && response.data) {
                    if (response.data.success == true) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        /* Clear existing allowed_models array first if needed */
                        this.editorgdata.allowed_renewal_models = [];
                        /* Push all orgAllowedModelData from response */
                        response.data.orgRenewalModelData.forEach(renewal => {
                            this.editorgdata.allowed_renewal_models.push({
                                id: renewal.id,
                                model_id: renewal.model_id,
                                device_plan_id: renewal.device_plan_id,
                                allowed_renewal_model_name: renewal.allowed_renewal_model_name,
                                allowed_renewal_plan_name: renewal.allowed_renewal_plan_name,
                                allowed_renewal_coverage_price: renewal.allowed_renewal_coverage_price,
                                allowed_renewal_deductible_price: renewal.allowed_renewal_deductible_price,
                                allowed_renewal_expiration_date: renewal.allowed_renewal_expiration_date,
                                active: renewal.active,
                                isDeletable: renewal.isDeletable
                            });
                        });
                        /* Turn off all edit flags */
                        this.isEditOrgDetails = false;
                        this.isEditSubOrg = false;
                        this.isEditAllowedDevices = false;
                        this.isEditRenewalAllowedDevices = false;
                        this.isEditClaimReasons = false;
                        this.isEditAdditionalDetails = false;
                        this.editRenewalDeviceIndex = null;
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                        4000
                    );

                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        async saveClaimReasons(id) {
            show_ajax_loader();
            /** Validations */
            this.validationErrors = {};
            let validationPassed = true;
            if (this.selectedClaimReasons.length < 0) {
                this.validationErrors.claim_reasons_data = ['Please select claim reason.'];
                validationPassed = false;
            }
           /*  if (this.localEditOrgData.claim_reasons_id && this.localEditOrgData.claim_reasons_id.length > 0) {
                this.localEditOrgData.claim_reasons_id.forEach((claimReason, index) => {
                    if (!claimReason.claim_reason_id) {
                        this.validationErrors[`claim_reasons_data.${index}.claim_reason_id`] = ['Please select claim reason.'];
                        validationPassed = false;
                    }
                });
            } */
            if (this.localEditOrgData.org_type != 1 && this.localEditOrgData.org_type != 2) {
                this.validationErrors.org_type = ['Please select organization type.'];
                validationPassed = false;
            }

            if (this.localEditOrgData.portal_status != 1 && this.localEditOrgData.portal_status != 0) {
                this.validationErrors.portal_status = ['Please select portal status.'];
                validationPassed = false;
            }
            if (this.localEditOrgData.portal_status == 0 && !this.localEditOrgData.close_portal_message) {
                this.validationErrors.close_portal_message = ['Please enter close portal message.'];
                validationPassed = false;
            }
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                const claimReasonPortalDetails = {
                    'claim_reasons_data': this.selectedClaimReasons,
                   /*  'claim_reasons_data': this.localEditOrgData.claim_reasons_id.map(claim => {
                        return {
                            id: claim.id ?? null,
                            // claim_reason_id: claim.claim_reason_id.id,
                            claim_reason_id: claim.claim_reason_id.id,
                            active: claim.active,
                        };
                    }), */
                    // 'claim_reasons_data': this.localEditOrgData.claim_reasons_id,
                    'org_type': this.localEditOrgData.org_type,
                    'portalStatus': this.localEditOrgData.portal_status,
                    'portalMsg': this.localEditOrgData.close_portal_message,
                };
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/organizations/update-claim-reasons/${id}`, claimReasonPortalDetails);
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    // Clear existing claim_reasons_id array first if needed
                    this.editorgdata.claim_reasons_id = [];

                    // Push all sub-organizations from response
                    response.data.updatedClaimIds.forEach(claim => {
                        this.editorgdata.claim_reasons_id.push({
                            id: claim.id,
                            claim_reason_name: claim.claim_reason_name,
                            claim_reason_id: claim.claim_reason_id,
                            active: claim.active,
                            isDeletable: claim.isDeletable
                        });
                    });
                    this.editorgdata.org_type = response.data.organizationData.org_type;
                    this.editorgdata.portal_status = response.data.organizationData.portal_status;
                    this.editorgdata.close_portal_message = response.data.organizationData.close_portal_message;

                    /* Turn off all edit flags */
                    this.isEditOrgDetails = false;
                    this.isEditSubOrg = false;
                    this.isEditAllowedDevices = false;
                    this.isEditRenewalAllowedDevices = false;
                    this.isEditClaimReasons = false;
                    this.isEditAdditionalDetails = false;
                } else if (response.data.errors && response.data.success == false) {
                    this.validationErrors = response.data.errors;
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** Delete Org-Claim Reasons */
        confirmOrgClaimReasonDelete(id) {
            this.$deleteAlertMessage.data.isDelete = true;
            this.$deleteAlertMessage.data.isChangeStatus = false;
            this.$deleteAlertMessage.data.itemId = id;
            this.$deleteAlertMessage.data.heading = 'Delete Claim Reason';
            this.$deleteAlertMessage.data.message = 'Are you sure you want to delete this Claim-Reason?';
            this.$deleteAlertMessage.data.callback = this.orgClaimReasonDelete;
        },

        async orgClaimReasonDelete(id) {
            show_ajax_loader();
            // Client-side validation
            let validationPassed = true;

            // Validate Sub-org Id
            if (!id) {
                validationPassed = false;
            }

            // Stop submission if validation fails
            if (!validationPassed) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
                return;
            }

            try {
                const response = await axios.delete(`${this.$userAppUrl}smarttiusadmin/organizations/delete-claim-reason/${id}`, {
                    params: {
                        orgId: this.editorgdata.id
                    }
                });
                if (response && response.data) {
                    if (response.data.success == true) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        // Clear existing subOrg array first if needed
                        this.editorgdata.claim_reasons_id = [];

                        // Push all sub-organizations from response
                        response.data.orgClaimReasonData.forEach(claim => {
                            this.editorgdata.claim_reasons_id.push({
                                id: claim.id,
                                claim_reason_name: claim.claim_reason_name,
                                claim_reason_id: claim.claim_reason_id,
                                active: claim.active,
                                isDeletable: claim.isDeletable
                            });
                        });
                        /* Turn off all edit flags */
                        this.isEditOrgDetails = false;
                        this.isEditSubOrg = false;
                        this.isEditAllowedDevices = false;
                        this.isEditRenewalAllowedDevices = false;
                        this.isEditClaimReasons = false;
                        this.isEditAdditionalDetails = false;
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                        4000
                    );

                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        async saveAdditionalDetails(id) {
            show_ajax_loader();
            /** Validations */
            this.validationErrors = {};
            let validationPassed = true;
            if (this.localEditOrgData.repair_enabled != 1 && this.localEditOrgData.repair_enabled != 0) {
                this.validationErrors.repair_enabled = ['Please select repair enabled status.'];
                validationPassed = false;
            }
            if (this.localEditOrgData.can_edit_device != 1 && this.localEditOrgData.can_edit_device != 0) {
                this.validationErrors.can_edit_device = ['Please select can edit device status.'];
                validationPassed = false;
            }
            if (this.localEditOrgData.allow_parents_claim != 1 && this.localEditOrgData.allow_parents_claim != 0) {
                this.validationErrors.allow_parents_claim = ['Please select allow parents claim status.'];
                validationPassed = false;
            }
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                const additionalDetails = {
                    'repair_enabled': this.localEditOrgData.repair_enabled,
                    'can_edit_device': this.localEditOrgData.can_edit_device,
                    'allow_parents_claim': this.localEditOrgData.allow_parents_claim,
                    'additional_instructions': this.localEditOrgData.additional_instructions,
                };
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/organizations/update-additional-details/${id}`, additionalDetails);
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    this.editorgdata.repair_enabled = response.data.orgData.repair_enabled;
                    this.editorgdata.can_edit_device = response.data.orgData.can_edit_device;
                    this.editorgdata.allow_parents_claim = response.data.orgData.allow_parents_claim;
                    this.editorgdata.additional_instructions = response.data.orgData.additional_instructions;
                    // Turn off all edit flags
                    this.isEditOrgDetails = false;
                    this.isEditSubOrg = false;
                    this.isEditAllowedDevices = false;
                    this.isEditRenewalAllowedDevices = false;
                    this.isEditClaimReasons = false;
                    this.isEditAdditionalDetails = false;
                } else if (response.data.errors && response.data.success == false) {
                    this.validationErrors = response.data.errors;
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** Cancel Methods */
        cancelEditOrgDetails() {
            this.validationErrors = {};
            Object.assign(this.editorgdata, this.originalData.orgDetails);
            this.isEditOrgDetails = false;
        },
        async cancelEditSubOrg(id) {
            this.validationErrors = {};
            // show_ajax_loader();
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/sub-org/${id}`);
                if (response.data.success == true) {
                    // hide_ajax_loader();

                    /* Clear existing subOrg array first */
                    this.editorgdata.subOrg = [];
                    // Reset the subOrg data to its original state
                    this.editorgdata.subOrg = response.data.sub_organizations.map(subOrg => ({
                        id: subOrg.id,
                        name: subOrg.name,
                        active: subOrg.active,
                        isDeletable: subOrg.isDeletable
                    }));
                    // If no sub-organizations were returned, clear the array
                    if (!this.editorgdata.subOrg.length) {
                        this.editorgdata.subOrg = [];
                    }
                } else {
                    // hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                // hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            }

            this.isEditSubOrg = false;
        },
        async cancelEditAllowedDevices(id) {
            this.validationErrors = {};
            // const original = this.originalData.allowedModels;

            // /* If original exists, it's an existing device — restore it */
            // if (original) {
            //     this.editorgdata.allowed_models = JSON.parse(JSON.stringify(original));

            //     /* Remove all newly added devices (i.e., ones without an `id`) */
            //     this.editorgdata.allowed_models = this.editorgdata.allowed_models.filter((device, i) => {
            //         return device.id; /* keep existing and the one we restored */
            //     });
            // }
            this.isEditAllowedDevices = false;
            this.editAllowedDeviceIndex = null;
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/allowed-devices/${id}`);
                if (response.data.success == true) {
                    /* Reset the orgAllowedModelData data to its original state */
                    this.editorgdata.allowed_models = [];
                    /* Push all orgAllowedModelData from response */
                    this.editorgdata.allowed_models = response.data.orgAllowedModelData.map(allowed => ({
                            id: allowed.id,
                            model_id: allowed.model_id,
                            device_plan_id: allowed.device_plan_id,
                            allowed_model_name: allowed.allowed_model_name,
                            allowed_plan_name: allowed.allowed_plan_name,
                            allowed_coverage_price: allowed.allowed_coverage_price,
                            allowed_deductible_price: allowed.allowed_deductible_price,
                            allowed_expiration_date: allowed.allowed_expiration_date,
                            active: allowed.active,
                            isDeletable: allowed.isDeletable
                    }));
                    /* If no sub-organizations were returned, clear the array */
                    if (!this.editorgdata.allowed_models.length) {
                        this.editorgdata.allowed_models = [];
                    }
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }

            } catch (error) {
               if (error && error.response && error.response.data && error.response.data.error) {
                   this.$alertMessage.success = false;
                   this.$alertMessage.message = "Something went wrong, please try again.";
               } else {
                   this.$alertMessage.success = false;
                   this.$alertMessage.message = "Something went wrong, please try again.";
               }
            }

        },
       /* Cancel Single Edit allowed Device */
        cancelSingleEditAllowedDevice(index) {
            this.validationErrors = {};
            const original = this.originalData.allowedModels[index];
            /* If original exists, it's an existing device — restore it */
            if (original) {
                /* Restore the original data */
                this.editorgdata.allowed_models[index] = JSON.parse(JSON.stringify(original));
                /* Re-populate dependent dropdowns */
                const modelId = original.model_id;
                const planId = original.device_plan_id;
                /* Trigger dropdown logic */
                this.getDeviceModels(false, modelId); /* reload model options */
                this.coveragePlans[index] = [];
                this.getDevicePlan(modelId, 1, index); /* reload plans for the selected model */
                this.editAllowedDeviceIndex = null;
            }
            else {
                /* If it's a newly added device being canceled, just remove it */
                this.editorgdata.allowed_models.splice(index, 1);
            }
        },
        async cancelEditRenewalAllowedDevices(id) {
            this.validationErrors = {};
            const original = this.originalData.allowedRenewalModels;
            // If original exists, it's an existing device — restore it
            // if (original) {
            //     this.editorgdata.allowed_renewal_models = JSON.parse(JSON.stringify(original));
            //     /* Remove all newly added devices (i.e., ones without an `id`) */
            //     this.editorgdata.allowed_renewal_models = this.editorgdata.allowed_renewal_models.filter((device, i) => {
            //         return device.id; // keep existing and the one we restored
            //     });
            //     this.isEditRenewalAllowedDevices = false;
            //     this.editRenewalDeviceIndex = null;
            // }
            this.isEditRenewalAllowedDevices = false;
            this.editRenewalDeviceIndex = null;
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/renewal-devices/${id}`);
                if (response.data.success == true) {
                    /* Reset the orgAllowedModelData data to its original state */
                    this.editorgdata.allowed_renewal_models = [];
                    /* Push all orgRenewalModelData from response */
                    this.editorgdata.allowed_renewal_models = response.data.orgRenewalModelData.map(renewal => ({
                        id: renewal.id,
                        model_id: renewal.model_id,
                        device_plan_id: renewal.device_plan_id,
                        allowed_renewal_model_name: renewal.allowed_renewal_model_name,
                        allowed_renewal_plan_name: renewal.allowed_renewal_plan_name,
                        allowed_renewal_coverage_price: renewal.allowed_renewal_coverage_price,
                        allowed_renewal_deductible_price: renewal.allowed_renewal_deductible_price,
                        allowed_renewal_expiration_date: renewal.allowed_renewal_expiration_date,
                        active: renewal.active,
                        isDeletable: renewal.isDeletable
                    }));
                    /* If no sub-organizations were returned, clear the array */
                    if (!this.editorgdata.allowed_renewal_models.length) {
                        this.editorgdata.allowed_renewal_models = [];
                    }
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }

            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },
        /** Cancel Single  Edit Renewal Device */
        cancelSingleEditRenewalDevice(index) {
            this.validationErrors = {};
            const original = this.originalData.allowedRenewalModels[index];
            /* If original exists, it's an existing device — restore it */
            if (original) {
                /* Restore the original data */
                this.editorgdata.allowed_renewal_models[index] = JSON.parse(JSON.stringify(original));
                /* Re-populate dependent dropdowns */
                const modelId = original.model_id;
                const planId = original.device_plan_id;
                /* Trigger dropdown logic */
                this.getDeviceModels(true, modelId); /* reload model options */
                this.renewalPlans[index] = [];
                this.getDevicePlan(modelId, 2, index); /* reload plans for the selected model */
                this.editRenewalDeviceIndex = null;
            }
            else {
                /* If it's a newly added device being canceled, just remove it */
                this.editorgdata.allowed_renewal_models.splice(index, 1);
            }
        },
        async cancelEditClaimReasons(id) {
            this.validationErrors = {};
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/claim-reasons-msg/${id}`);
                if (response.data.success == true) {
                    /* Reset fields */
                    /* Clear existing claim_reasons_id array first */
                    this.editorgdata.claim_reasons_id = [];
                    /* Push all claim reasons from response */
                    response.data.updatedClaimIds.forEach(claim => {
                        this.editorgdata.claim_reasons_id.push({
                            id: claim.id,
                            claim_reason_name: claim.claim_reason_name,
                            claim_reason_id: claim.claim_reason_id,
                            active: claim.active,
                            isDeletable: claim.isDeletable
                        });
                    });
                    this.editorgdata.org_type = response.data.organizationData.org_type;
                    this.editorgdata.portal_status = response.data.organizationData.portal_status;
                    this.editorgdata.close_portal_message = response.data.organizationData.close_portal_message;

                }else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
            this.isEditClaimReasons = false;
        },
        async cancelEditAdditionalDetails(id) {
            this.validationErrors = {};
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/cancel/additional-details/${id}`);
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.editorgdata.repair_enabled = response.data.orgData.repair_enabled;
                    this.editorgdata.can_edit_device = response.data.orgData.can_edit_device;
                    this.editorgdata.allow_parents_claim = response.data.orgData.allow_parents_claim;
                    this.editorgdata.additional_instructions = response.data.orgData.additional_instructions;

                }else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
            this.isEditAdditionalDetails = false;
        },

        /* get device models */
        async getDeviceModels(isRenewal = false, modelID = null, name = this.searchModelText) {
            /* Enter at least 5 characters */
            if (name && name.length < 5) {
                this.devicemodeldata = [
                    {
                        id: null,
                        title: "Please enter at least five characters",
                    },
                ];
                return;
            }
            try {
                let response = null;
                const url = `${this.$userAppUrl}smarttiusadmin/devices/org-device-models?name=${name}`;
                response = await axios.get(url);
                if (response.data.success && response.data.deviceModels.length > 0) {
                    if (name !== this.searchModelText) {
                        this.devicemodeldata = [];
                     } else {
                        if (isRenewal) {
                            this.devicemodeldata = response.data.deviceModels;
                            this.editorgdata.allowed_renewal_models.forEach((allowedRenewalModel, index) => {
                                const modelObj = this.devicemodeldata.find(m => m.id == allowedRenewalModel.model_id);
                                if (modelObj) {
                                    allowedRenewalModel.model_id = modelObj; // Replace ID with object
                                }

                            });
                        } else {
                            this.devicemodeldata = response.data.deviceModels;
                            this.editorgdata.allowed_models.forEach((allowedModel, index) => {
                                const modelObj = this.devicemodeldata.find(m => m.id == allowedModel.model_id);
                                if (modelObj) {
                                    allowedModel.model_id = modelObj; // Replace ID with object
                                }
                            });
                        }
                    }
                } else {
                    /* This will show up in the dropdown regardless of success status */
                    this.devicemodeldata = [
                        {
                            id: null,
                            title: "No device model found",
                        },
                    ];
                }
            } catch (error) {
                this.devicemodels = [
                    {
                        id: null,
                        title: "Something went wrong, please try again later",
                    },
                ];
            }
        },

        /* device model name text */
        updateSearchModelName(value) {
            show_ajax_loader();
            this.searchModelText = value;
            this.getDeviceModels(false, null, value);
            hide_ajax_loader();
        },

        /** Fetch device plan based on model and plan type */
        async getDevicePlan(model, planType, index) {
            show_ajax_loader();
            if (!model || !planType) {
                this.clearPlans(planType, index);
                hide_ajax_loader();
                return;
            }
            try {
                const { data } = await axios.post(
                    `${this.$userAppUrl}smarttiusadmin/devices-plan/one-time`,
                    {
                        modelId: model,
                        planType,
                    }
                );
                if (data.success) {
                    if (planType == 1) {
                        this.coveragePlans[index] = data.deviceModelPlans || [];
                        this.editorgdata.allowed_models.forEach((allowedModel, index) => {
                            const modelPlanObj = this.coveragePlans[index].find(mp => mp.id == allowedModel.device_plan_id);
                            if (modelPlanObj) {
                                allowedModel.device_plan_id = modelPlanObj; // Replace ID with object
                            }

                        });
                        hide_ajax_loader();
                    } else if (planType == 2) {
                        this.renewalPlans[index] = data.deviceModelPlans || [];
                        this.editorgdata.allowed_renewal_models.forEach((allowedRenewalModel, index) => {
                            const modelPlanObj = this.renewalPlans[index].find(mp => mp.id == allowedRenewalModel.device_plan_id);
                            if (modelPlanObj) {
                                allowedRenewalModel.device_plan_id = modelPlanObj; // Replace ID with object
                            }

                        });
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
                    this.getDevicePlan(model.id, 2, index);
                    hide_ajax_loader();
                } else {
                    this.getDevicePlan(model.id, 1, index);
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
            this.getDevicePlan({ id: this.deviceModelId },isRenewal ? 2 : 1,index );
            hide_ajax_loader();
        },

        /* device model name(Renewal) text */
        updateRenewalSearchModelName(value) {
            show_ajax_loader();
            this.searchModelText = value;
            this.getDeviceModels(true, null, value);
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
                    /* const targetList = planType == 1 ? this.editorgdata.allowed_models : this.editorgdata.allowed_renewal_models;
                    if (planType == 1) {
                        targetList[index] = {
                            ...targetList[index],
                            allowed_coverage_price: data.deviceModelPlans?.[0]?.price || 0,
                            allowed_deductible_price: data.deviceModelPlans?.[0]?.deductible_price || 0,
                            allowed_expiration_date: data.deviceModelPlans?.[0]?.expiration_date || 0,
                        };

                    } else if (planType == 2) {
                        targetList[index] = {
                            ...targetList[index],
                            allowed_renewal_coverage_price: data.deviceModelPlans?.[0]?.price || 0,
                            allowed_renewal_deductible_price: data.deviceModelPlans?.[0]?.deductible_price || 0,
                            allowed_renewal_expiration_date: data.deviceModelPlans?.[0]?.expiration_date || 0,
                        };
                    }  */

                    const devicePlan = data.deviceModelPlans?.[0] || {};
                    const expirationDays = devicePlan.expiration_days || 0;

                    let expirationDate = null;
                    if (expirationDays > 0) {
                        const date = new Date();
                        date.setDate(date.getDate() + expirationDays);
                        expirationDate = date.toISOString().split('T')[0]; // format: YYYY-MM-DD
                    }

                    const targetList = planType == 1 ? this.editorgdata.allowed_models : this.editorgdata.allowed_renewal_models;

                    if (planType == 1) {
                        targetList[index] = {
                            ...targetList[index],
                            allowed_coverage_price: devicePlan.price || 0,
                            allowed_deductible_price: devicePlan.deductible_price || 0,
                            allowed_expiration_date: expirationDate,
                        };
                    } else if (planType == 2) {
                        targetList[index] = {
                            ...targetList[index],
                            allowed_renewal_coverage_price: devicePlan.price || 0,
                            allowed_renewal_deductible_price: devicePlan.deductible_price || 0,
                            allowed_renewal_expiration_date: expirationDate,
                        };
                    } else {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, please try again.";
                    }
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
                if (this.editorgdata.allowed_renewal_models[index] !== undefined) {
                    this.editorgdata.allowed_renewal_models[index].model_id = "";
                    this.editorgdata.allowed_renewal_models[index].device_plan_id = "";
                    // this.editorgdata.allowed_renewal_models[index].renewalPlans = [];
                    this.renewalPlans[index] = [];
                    this.editorgdata.allowed_renewal_models[index].allowed_renewal_coverage_price = 0;
                    this.editorgdata.allowed_renewal_models[index].allowed_renewal_deductible_price = 0;
                    this.editorgdata.allowed_renewal_models[index].allowed_renewal_expiration_date = 0;
                }
                hide_ajax_loader();
            } else {
                if (this.editorgdata.allowed_models[index] !== undefined) {
                    this.editorgdata.allowed_models[index].model_id = "";
                    this.editorgdata.allowed_models[index].device_plan_id = "";
                    // this.editorgdata.allowed_models[index].coveragePlans = [];
                    this.coveragePlans[index] = [];
                    this.editorgdata.allowed_models[index].allowed_coverage_price = 0;
                    this.editorgdata.allowed_models[index].allowed_deductible_price = 0;
                    this.editorgdata.allowed_models[index].allowed_expiration_date = 0;
                }
                hide_ajax_loader();
            }
        },

        /** Clear plans when deselecting model */
        clearPlans(isRenewal = false, index) {
            show_ajax_loader();
            if (isRenewal) {
                if (this.editorgdata.allowed_renewal_models[index] !== undefined) {
                     this.editorgdata.allowed_renewal_models[index].allowed_renewal_coverage_price = 0;
                    this.editorgdata.allowed_renewal_models[index].allowed_renewal_deductible_price = 0;
                    this.editorgdata.allowed_renewal_models[index].allowed_renewal_expiration_date = 0;
                }
                hide_ajax_loader();
            } else {
                if (this.editorgdata.allowed_models[index] !== undefined) {
                    this.editorgdata.allowed_models[index].allowed_coverage_price = 0;
                    this.editorgdata.allowed_models[index].allowed_deductible_price = 0;
                    this.editorgdata.allowed_models[index].allowed_expiration_date = 0;
                }
                hide_ajax_loader();
            }
        },

        async updatedOrgData(id) {
            show_ajax_loader();
            this.editOrgData = {};
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/edit/${id}`);
                if (response.data.success == true) {
                    this.editorgdata = response.data.viewData;
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        closeEditOrg()
        {
            this.isEditOrgDetails = false;
            this.isEditSubOrg = false;
            this.isEditAllowedDevices = false;
            this.editAllowedDeviceIndex = null;
            this.isEditRenewalAllowedDevices = false;
            this.editRenewalDeviceIndex = null;
            this.isEditClaimReasons = false;
            this.isEditAdditionalDetails = false;
            this.validationErrors = {};
            this.selectedClaimReasons = [];
            this.originalData = {};
            this.devicemodeldata = [];
            this.coveragePlans = [];
            this.renewalPlans = [];
            this.searchModelText = '';
            hide_admin_popup('#organizationEditModal');
        },
        /** Formate Date */
        filterFormatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        },
    }
}
</script>