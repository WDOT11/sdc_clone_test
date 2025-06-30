<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0  ">
            <div class="card-body">
                <h4 class="mb-3 border-bottom pb-2 coman_main_heading">Organizations <small style="font-size: 13px; color: #696969;"> ({{ formattedDigits(totalOrg) }}) </small></h4>

                <!-- Search filter -->
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                    <a href="/smarttiusadmin/organizations/create" class="btn blogal_pbtn_padding def_14_size bg_blue wmax text-white d-flex align-items-center gap-10"><img :src="profileIc" width="22" height="22"> Organization</a>
                    <!-- Search by Organization name -->
                    <div class="d-flex justify-content-between flex-wrap search_options align-items-start gap-3">
                        <input type="text" v-model="search_name" class="form-control def_14_size w-auto" placeholder="Filter by Organization Name">

                        <!-- buttons -->
                        <div class="d-flex gap-2">

                            <button class="btn bg_blue blogal_pbtn_padding def_14_size d-flex align-items-center gap-10  text-white" @click="getOrganizations"><img :src="searchIc" width="20" height="20"> Search</button>
                            <button class="btn customm_reset_btn def_14_size blogal_pbtn_padding" @click="cancelSearch">Clear</button>
                        </div>
                    </div>
                </div>
                <!-- Search filter End -->

                <!-- Organization Details Modal -->
                <div class="modal org_modal upd_orgination fade" id="organizationDetailsModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  modal-xl" >
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
                                    <h5 class="modal-title def_18_size" id="exampleModalLabel">Organization Details</h5>
                                </div>
                                <button  type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body onewhitebg">
                                <div class="row">
                                    <div class="col-12 ">
                                        <div class=" card org_innercard">

                                    <div class="card-body">
                                        <div class="list-header flex justify-between align-items-center mb-2">
                                             <h4 class="fw-bold def_18_size  ">Organization</h4>
                                        </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="table-responsive">
                                                <table class="table  table-bordered def_14_size">
                                                    <tbody>
                                                        <tr>
                                                            <td><b>Organization Name:</b></td>
                                                            <td>{{ viewOrgData.org_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Logo:</b></td>
                                                            <td><img v-if="viewOrgData.logo_file_path && viewOrgData.logo_file_name" :src="viewOrgData.logo_file_path + viewOrgData.logo_file_name" alt="Logo" class="" width="50px" height="50px">
                                                                <p class="mb-0" v-else>N/A</p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="table-responsive">
                                                <table class="table  table-bordered def_14_size">
                                                    <tbody>
                                                        <tr>
                                                            <td><b>Cover Image:</b></td>
                                                            <td><p class="mb-0 mt-0"></p><img v-if="viewOrgData.cover_file_path && viewOrgData.cover_file_name" :src="viewOrgData.cover_file_path + viewOrgData.cover_file_name" alt="cover image" width="70" height="50" ><p class="mb-0 mt-0" v-else>N/A</p></td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Service Agreement:</b></td>
                                                            <td><p class="mb-0 mt-0" v-if="viewOrgData.agreement_link">{{ viewOrgData.agreement_link ?? 'N/A' }}</p> <button type="button" @click="downlaodPdf(viewOrgData.pdf_file_path, viewOrgData.pdf_file_name)" v-if="viewOrgData.pdf_file_path && viewOrgData.pdf_file_name" class="btn btn-sm btn-warning"> Download PDF </button></td>
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
                                                     <h4 class="fw-bold  def_18_size">Sub-Organization</h4>
                                                 </div>
                                                <div class="table-responsive">
                                                    <table class="table  table-bordered def_14_size">
                                                        <tbody>
                                                            <tr>

                                                                <td>
                                                                    <div class="glob_scroll">
                                                                        <ol v-if="viewOrgData.subOrg && viewOrgData.subOrg.length > 0" class="suborg_list p-0 m-0">
                                                                            <li v-for="(subOrg, index) in viewOrgData.subOrg" :key="index">{{ index + 1 }}. {{ subOrg }}</li>
                                                                        </ol>
                                                                        <p  v-else class="my-0">N/A</p>
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
                    <div class="row mt-3">
                        <div class="col-12 ">
                            <div class=" p-3 card org_innercard">

                                    <h4 class="fw-bold def_18_size border-bottom pb-2 themetextcolor">Device Details</h4>



                                    <div class="card-body px-0">
                                         <div class="list-header flex justify-between align-items-center mb-2">
                                             <h5 class="def_18_size ">Allowed Devices</h5>
                                         </div>
                                        <div class="glob_scroll">
                                            <div class="table-responsive  ">
                                                <table class="table  table-bordered table-hover def_14_size">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Device Model</th>
                                                            <th>Plan Name</th>
                                                            <th>Coverage Price</th>
                                                            <th>Deductible</th>
                                                            <th>Expiration Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Device Details -->
                                                        <tr v-if="viewOrgData.allowed_models && viewOrgData.allowed_models.length > 0" v-for="(allowedModel, index) in viewOrgData.allowed_models" :key="allowedModel.id">
                                                            <td>{{ index + 1 }}</td>
                                                            <td>{{ allowedModel.allowed_model_name ?? '' }}</td>
                                                            <td>{{ allowedModel.allowed_plan_name ?? '' }}</td>
                                                            <td>${{ allowedModel.allowed_coverage_price ?? '' }}</td>
                                                            <td>${{ allowedModel.allowed_deductible_price ?? '' }}</td>
                                                            <td>{{ filterFormatDate(allowedModel.allowed_expiration_date) }}</td>
                                                        </tr>
                                                        <tr v-else>
                                                            <td colspan="6" class="text-center">No device details found.</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="card-body px-0 ">
                                        <div class="list-header flex justify-between align-items-center mb-2">
                                            <h5 class=" def_18_size ">Renewal Allowed Devices</h5>
                                        </div>
                                        <div class="glob_scroll">
                                            <div class="table-responsive ">
                                                <table class="table table-bordered table-hover def_14_size">
                                                    <thead class="table-light">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Device Model</th>
                                                                    <th>Plan Name</th>
                                                                    <th>Coverage Price</th>
                                                                    <th>Deductible</th>
                                                                    <th>Expiration Date</th>
                                                                </tr>
                                                    </thead>
                                                    <tbody>
                                                            <!-- Device Details -->
                                                            <tr v-if="viewOrgData.allowed_renewal_models && viewOrgData.allowed_renewal_models.length > 0" v-for="(allowedRenewalModel, index) in viewOrgData.allowed_renewal_models" :key="allowedRenewalModel.id">
                                                                <td>{{ index + 1 }}</td>
                                                                <td>{{ allowedRenewalModel.allowed_renewal_model_name ?? '' }}</td>
                                                                <td>{{ allowedRenewalModel.allowed_renewal_plan_name ?? '' }}</td>
                                                                <td>${{ allowedRenewalModel.allowed_renewal_coverage_price ?? '' }}</td>
                                                                <td>${{ allowedRenewalModel.allowed_renewal_deductible_price ?? '' }}</td>
                                                                <td>{{ filterFormatDate(allowedRenewalModel.allowed_renewal_expiration_date) }}</td>
                                                            </tr>
                                                            <tr v-else>
                                                                <td colspan="6" class="text-center">No Renewal Allowed Devices Found</td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class=" p-3 card org_innercard">
                              <h4 class="fw-bold def_18_size border-bottom pb-2 themetextcolor">Claim & Additional Details</h4>
                                <div class="card-body px-0">
                                        <div class="list-header flex justify-between align-items-center mb-2">
                                            <h5 class=" def_18_size">Claim Status</h5>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover def_14_size">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Claim Reasons:</b></td>
                                                        <td v-if="viewOrgData.claim_reasons">{{ viewOrgData.claim_reasons ?? '' }}</td>
                                                        <td v-else>N/A</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Organization Type:</b></td>
                                                        <td v-if="viewOrgData.org_type">{{ viewOrgData.org_type == 1 ? 'School' : 'Non School' }}</td>
                                                        <td v-else>N/A</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Portal Status:</b></td>
                                                        <td  v-if="viewOrgData.portal_status">{{ viewOrgData.portal_status == 1 ? 'Open' : 'Close' }}</td>
                                                        <td v-else>N/A</td>
                                                    </tr>
                                                    <tr v-if="viewOrgData.portal_status == 0">
                                                        <td><b>Close Portal Message:</b></td>
                                                        <td  v-if="viewOrgData.close_portal_message">{{ viewOrgData.close_portal_message ?? '' }}</td>
                                                        <td v-else>N/A</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                    <div class="card-body px-0">
                                        <div class="list-header flex justify-between align-items-center mb-2">
                                            <h5 class="def_18_size ">Additional Details</h5>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover def_14_size">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Repair Enabled:</b></td>
                                                        <td v-if="viewOrgData.repair_enabled">{{ viewOrgData.repair_enabled == 1 ? 'Enable ' : 'Disable' }}</td>
                                                        <td v-else>N/A</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Can Edit Devices?:</b></td>
                                                        <td v-if="viewOrgData.can_edit_device">{{ viewOrgData.can_edit_device == 1 ? 'Yes' : 'No' }}</td>
                                                        <td v-else>N/A</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Allow Parents Claim:</b></td>
                                                        <td v-if="viewOrgData.allow_parents_claim">{{ viewOrgData.allow_parents_claim == 1 ? 'Allowed' : 'Not Allowed' }}</td>
                                                        <td v-else>N/A</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Additional Instructions:</b></td>
                                                        <td v-if="viewOrgData.additional_instructions">{{ viewOrgData.additional_instructions ?? '' }}</td>
                                                        <td v-else>N/A</td>
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

                <!-- Organization Details Modal End -->

                <!-- Organization Edit Modal  -->
                 <organization-update :editorgdata="editOrgData" :claimreasonsdata="claimreasons"></organization-update>
                <!-- Organization Edit Modal End  -->
                <!-- Organization Listing -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover def_14_size table_custom2 ">
                        <thead class="table-light">
                            <tr>
                                <th  width="60" >#</th>
                                <th >Organization Name</th>
                                <th >Organization Link</th>
                                <th >Created Date</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="organizations.length > 0" v-for="(organization, index) in organizations" :key="organization.id">
                                <td>
                                    {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}
                                </td>
                                <td>
                                    {{ organization.name }}
                                </td>
                                <td >
                                    <div v-if="editOrgLinkIndex == index" class="d-flex  p-2 rounded editor_live flex-column gap-1 align-items-start">
                                    <input v-if="editOrgLinkIndex == index" type="text" v-model="org_link" :class="['form-control', { 'is-invalid': validationErrors.orgLink, },]" />
                                    <small v-if="validationErrors.orgLink && editOrgLinkIndex == index" ><ErrorMessage :msg="validationErrors.orgLink"></ErrorMessage></small>
                                    <div class="d-flex  p-2 rounded editor_live  gap-1 align-items-start">
                                        <button type="submit" v-if="editOrgLinkIndex == index" @click="saveOrgLink(index)" class="btn d-flex align-items-center  gap-1 p-1 px-2  border savebtn_blobal  "> <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 28 28" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M7.8 26h12.5c2.8 0 5-2.2 5-5V9.2c0-1.1-.4-2.1-1.2-2.8l-3.3-3.2c-.4-.3-.8-.6-1.2-.8v4.2c0 1.7-1.3 3-3 3h-5.3c-1.6 0-3-1.3-3-3v-4c.1-.3-.2-.6-.5-.6-2.8 0-5 2.2-5 5v14c0 2.8 2.2 5 5 5zm1.5-12.5h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1zm0 4.7h9.3c.6 0 1 .4 1 1s-.4 1-1 1H9.3c-.6 0-1-.4-1-1s.5-1 1-1z" fill="#14b009" opacity="1" data-original="#000000" class=""></path><path d="M11.4 7.5h5.3c.6 0 1-.4 1-1V2h-7.3v4.5c0 .6.4 1 1 1z" fill="#14b009" opacity="1" data-original="#000000" class=""></path></g></svg>
                                                        Save</button>
                                        <button type="button" v-if="editOrgLinkIndex == index" @click="cancelOrgLink(index)" class="btn d-flex align-items-center gap-1 def_14_size  border candel_blobal p-1 px-2 "><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="fi_12864365"><linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse" x1="376.27" x2="135.74" y1="376.27" y2="135.74"><stop offset="0" stop-color="#ef3739"></stop><stop offset=".54" stop-color="#ef3739"></stop><stop offset="1" stop-color="#ff8c8b"></stop></linearGradient><linearGradient id="linear-gradient-2" gradientUnits="userSpaceOnUse" x1="326.21" x2="185.8" y1="326.2" y2="185.8"><stop offset="0" stop-color="#ffd2d2"></stop><stop offset=".57" stop-color="#fff"></stop><stop offset="1" stop-color="#fff"></stop></linearGradient><g id="Layer_14" data-name="Layer 14"><path d="m496.53 129.86c-13.44-49.47-64.89-100.93-114.37-114.37-30.34-7.56-70.98-15.35-126.16-15.49-55.16.14-95.8 7.93-126.14 15.49-49.47 13.44-100.93 64.89-114.37 114.37-7.56 30.33-15.35 70.98-15.49 126.14.14 55.17 7.93 95.81 15.49 126.15 13.44 49.48 64.9 100.93 114.37 114.37 30.34 7.56 71 15.35 126.15 15.48 55.16-.13 95.81-7.92 126.15-15.48 49.48-13.44 100.93-64.89 114.37-114.37 7.56-30.34 15.35-71 15.48-126.15-.13-55.16-7.92-95.8-15.48-126.14z" fill="#ffe5e5"></path><path d="m256 85.93a170.08 170.08 0 1 0 170.08 170.07 170.08 170.08 0 0 0 -170.08-170.07z" fill="url(#linear-gradient)"></path><path d="m282.65 256 43.56-43.55a18.86 18.86 0 0 0 0-26.65 18.86 18.86 0 0 0 -26.65 0l-43.56 43.56-43.55-43.56a18.84 18.84 0 0 0 -26.65 26.65l43.56 43.55-43.56 43.56a18.85 18.85 0 0 0 0 26.64 18.84 18.84 0 0 0 26.65 0l43.55-43.55 43.55 43.55a18.84 18.84 0 1 0 26.65-26.64z" fill="url(#linear-gradient-2)"></path></g></svg>
                                        Cancel
                                        </button>
                                    </div>
                                    </div>
                                    <div class="d-flex  justify-content-between  align-items-center gap-2 position-relative">
                                        <span v-if="editOrgLinkIndex !== index">{{ organization.org_link }}</span>
                                        <div v-if="editOrgLinkIndex !== index" class="d-flex  gap-3 align-items-center">
                                            <a  @click.prevent="copyOrgLink(`${$userAppUrl}org/${organization.org_link}`, index)" href="#" class="btn  bg-transparent p-0 ">
                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0" viewBox="0 0 24 24" xml:space="preserve" class=""><g><path fill="#e2e3e5" d="M7 7V5h9a3 3 0 0 1 3 3v9h-2v-7a3 3 0 0 0-3-3z" opacity="1" data-original="#c4e6ff" class=""></path><g fill-rule="evenodd" clip-rule="evenodd"><path fill="#024493" d="M7 4a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-3v-2h3a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H10a1 1 0 0 0-1 1v3H7z" opacity="1" data-original="#024493" class=""></path><path fill="#989fa7" d="M1 10a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H4a3 3 0 0 1-3-3zm3-1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V10a1 1 0 0 0-1-1z" opacity="1" data-original="#1e93ff" class=""></path></g></g></svg></a>

                                                <div v-if="showTooltipIndex == index" class="copidbadge">
                                                Copied!
                                            </div>
                                            <button ata-toggle="tooltip" data-placement="top" data-custom-class="tooltip-primary" title="Tooltip primary example" type="button" @click="editOrgLink(index)"class=" rounded-pill">
                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path fill="#989fa7" d="M21.41 6.12c.78-.78.78-2.05 0-2.83l-.71-.71c-.78-.78-2.05-.78-2.83 0L7.56 12.91c-.17.17-.27.38-.29.62L7 16.46c-.03.31.23.57.54.54l2.93-.27c.23-.02.45-.12.62-.29z" opacity="1" data-original="#fcbf49" class=""></path><path fill="#398ce6" d="M21.41 6.12 20 7.53 16.47 4l1.41-1.41c.78-.79 2.05-.79 2.83 0l.7.7c.79.78.79 2.05 0 2.83z" opacity="1" data-original="#e63946" class=""></path><path fill="#989fa7" d="M17 22.75H5c-2.07 0-3.75-1.68-3.75-3.75V7c0-2.07 1.68-3.75 3.75-3.75h5.81c.41 0 .75.34.75.75s-.34.75-.75.75H5C3.76 4.75 2.75 5.76 2.75 7v12c0 1.24 1.01 2.25 2.25 2.25h12c1.24 0 2.25-1.01 2.25-2.25v-5.81c0-.41.34-.75.75-.75s.75.34.75.75V19c0 2.07-1.68 3.75-3.75 3.75z" opacity="1" data-original="#0b4f6c" class=""></path></g></svg>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    {{ formatDate(organization.created_at) }}
                                </td>

                                <td>
                                    <div class="d-flex  align-items-center">

                                    <button   @click="showOrg(organization.id)" class=" rounded-pill globalviewbtn extrabtns themetextcolor  d-flex align-items-center def_14_size gap-1" data-bs-toggle="modal" data-bs-target="#organizationDetailsModal">
                                        <img :src="viewIc" width="35" height="35">
                                        <span class="viewtext_Gl">View</span>
                                    </button>

                                    <button  @click="editOrg(organization.id)" class=" rounded-pill themetextcolor globaleditbtn extrabtns d-flex align-items-center def_14_size gap-1" data-bs-toggle="modal" data-bs-target="#organizationEditModal">
                                        <img :src="editIc" width="35" height="35">
                                        <span class="edittext_Gl">Edit</span>
                                    </button>
                                    <!-- <button @click="confirmDelete" class=" btn bg-transparent   rounded">
                                        <img :src="deleteIc" width="28" height="28">
                                    </button> -->
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="5">
                                    Organizations Not Found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Organization Listing End -->

                <!-- Pagination -->
                <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5" :paginate="getOrganizations"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        organizationsdata:{
            type: Object
        },
        paginationdata:{
            type: Object
        },
        totalorgs: {
            type: Number
        }
    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,
            aboutIc: this.$aboutIcIcon,

            totalOrg: this.totalorgs,
            organizations: this.organizationsdata.data,
            pagination: this.paginationdata,
            message: "",
            success: true,
            changePagination_count: 2,
             /** Searching parameters */
             search_name: '',
             /** View Organization */
             showViewModal: false,
             viewOrgData: {},
             /** edit organization */
             editOrgData: {},
             claimreasons:{},

             /** Edit Organization Link */
             editOrgLinkIndex: null,
             org_link: '',
             validationErrors: {},
             showTooltip: false,
             showTooltipIndex: null,
        };
    },
    methods: {

        /** To format the date */
        formatDate(dateString){
            if (!dateString) return '';
            const rawString = String(dateString);
            const cleanedDateString = rawString
                .replace(' ', 'T')                /** Ensure ISO T separator  */
                .replace(/\.\d+Z$/, '')           /** Strip microseconds + Z */
                .replace(/Z$/, '');               /** Remove Z if not matched above */

                return formatDateAndTimeZone(cleanedDateString);
        },

        /** To get the organizations by using paging */
        async getOrganizations(page = 1, search_name = this.search_name) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/organizations/?page=${page}${search_name && search_name !== "" ? `&name=${search_name}` : ""}`
                );
                if (response.data.success == true) {
                    if (response.data.organizations.data && response.data.pagination )
                    {
                        this.totalOrg = response.data.pagination.total;
                        this.organizations = response.data.organizations.data;
                        this.pagination = response.data.pagination;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, please try again.";
                    }
                }
            }
            catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = error.response.data.error;
                setTimeout(
                    () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                    3000
                );
            }
        },

        async cancelSearch(){
            show_ajax_loader();
            this.search_name = '';
            this.getOrganizations();
            hide_ajax_loader();
        },

        /** On click details button */
        async showOrg(id){
            show_ajax_loader();
            this.showViewModal = true;
            this.viewOrgData = {};
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/show/${id}`);
                if (response.data.success == true) {
                    this.viewOrgData = response.data.viewData;
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** On click of edit button */
        async editOrg(id){
            show_ajax_loader();
            this.editOrgData = {};
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/organizations/edit/${id}`);
                if (response.data.success == true) {
                    this.editOrgData = response.data.viewData;
                    this.claimreasons = response.data.claimReasonsData;
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** On click of delete button */
        confirmDelete(){
            this.$alertMessage.success = false;
            this.$alertMessage.message = "This is under development.";
        },

        /** Download PDF File */
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

        editOrgLink(index){
           this.validationErrors = {};
            this.org_link = this.organizations[index].org_link;
            this.editOrgLinkIndex = index; // Set index being edited
        },

        /** Cancel Organization Link Edit */
        cancelOrgLink(index){
            this.editOrgLinkIndex = null; // Reset index
            this.org_link = '';
            /** Clear previous errors */
            this.validationErrors = {};
        },

        /** Save Organization Link */
        async saveOrgLink(index){
            show_ajax_loader();
            /** Clear previous errors */
            this.validationErrors = {};

            let validationPassed = true;

            /* Validate Organization Link */
            if (!this.org_link) {
                this.validationErrors.orgLink = `Organization link can't be empty.`;
                validationPassed = false;
            }

            if(this.org_link.length > 255){
                this.validationErrors.orgLink = `Organization link must not exceed 255 characters.`;
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/organizations/update-org-link/${this.organizations[index].id}`, {
                    org_link: this.org_link,
                });
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.editOrgLinkIndex = null; /* Reset index after saving */
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    this.getOrganizations();
                } else if(response.data.errors && response.data.success == false) {
                    if(response.data.errors.org_link){
                        this.validationErrors.orgLink = response.data.errors.org_link[0];
                    }
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        copyOrgLink(copyUrl, index) {
            const url = copyUrl;
            navigator.clipboard.writeText(url)
            .then(() => {
                this.showTooltipIndex = index;
                setTimeout(() => {
                    this.showTooltipIndex = null;
                }, 1500); // Hide after 1.5 seconds
            })
            .catch(err => {
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, please try again.";
            });
        },
        formattedDigits(number)
        {
            return formattedNumber(number);
        },
        /** Formate Date */
        filterFormatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        },
    },
};
</script>
