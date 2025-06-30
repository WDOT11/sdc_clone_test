<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0  ">
            <div class="card-body">
                <h4 class="mb-3 border-bottom pb-2 coman_main_heading">Users</h4>

                <!-- Search filter -->
                <div class="d-flex justify-content-between flex-wrap align-items-items gap-3 mb-3">
                    <button data-bs-toggle="modal" data-bs-target="#AddNewUser" class="btn bg_blue wmax text-white d-flex align-items-center gap-10 def_14_size  rounded "><img :src="profileIc" width="22" height="22"> Add New</button>
                    <!-- Search by user name or email -->
                    <div class="d-flex justify-content-between flex-wrap search_options align-items-start gap-3">
                        <!-- <input type="text" v-model="search_name" class="form-control w-auto" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" placeholder="Filter by Name"> -->

                        <!-- buttons -->
                       <!--  <div class="d-flex gap-2">

                            <button class="btn bg_blue d-flex align-items-center gap-10  text-white"
                                @click="getUserData"><img :src="searchIc" width="20" height="20"> Search</button>
                            <button class="btn btnblack" @click="cancelSearch">Clear</button>
                        </div> -->
                    </div>
                </div>

                <!-- Add User Modal -->
                <div class="modal" id="AddNewUser">
                    <div class="modal-dialog modal-xl ">
                        <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                    <svg fill="none" height="30" viewBox="0 0 24 24" width="30" xmlns="http://www.w3.org/2000/svg" id="fi_8005826"><g fill="rgb(0,0,0)"><path d="m12.25 12.25c-1.1372 0-2.2489-.3372-3.19453-.969-.94558-.6319-1.68257-1.52989-2.11777-2.58057-.43521-1.05067-.54908-2.20681-.32721-3.3222.22186-1.11539.7695-2.13994 1.57365-2.94409s1.82866-1.35179 2.94406-1.573653c1.1154-.221865 2.2716-.107996 3.3222.327213 1.0507.4352 1.9487 1.17219 2.5806 2.11777.6318.94559.969 2.05729.969 3.19453-.0026 1.52419-.6093 2.98518-1.6871 4.0629-1.0777 1.0778-2.5387 1.6845-4.0629 1.6871zm0-10c-.8406 0-1.6623.24926-2.36117.71626-.69891.46699-1.24364 1.13075-1.56532 1.90734-.32167.77658-.40583 1.63112-.24185 2.45554.16399.82442.56877 1.58169 1.16314 2.17607.59437.59439 1.3516.99919 2.1761 1.16309.8244.164 1.6789.0799 2.4555-.2418s1.4404-.86641 1.9073-1.56532c.467-.69891.7163-1.52061.7163-2.36118-.0026-1.12636-.4513-2.20583-1.2477-3.00228-.7965-.79646-1.8759-1.24508-3.0023-1.24772z"></path><path d="m3.25 20.25c-.19811-.0026-.38737-.0824-.52747-.2225-.14009-.1401-.21994-.3294-.22253-.5275.00528-2.319.92885-4.5415 2.56865-6.1813s3.86233-2.5634 6.18135-2.5687h2c.1989 0 .3897.079.5303.2197.1407.1406.2197.3314.2197.5303s-.079.3897-.2197.5303c-.1406.1407-.3314.2197-.5303.2197h-2c-1.92201.0026-3.76454.7673-5.1236 2.1264-1.35907 1.3591-2.12375 3.2016-2.1264 5.1236-.00259.1981-.08244.3874-.22253.5275-.1401.1401-.32936.2199-.52747.2225z"></path><path d="m16.75 23.25c-.1981-.0026-.3874-.0824-.5275-.2225s-.2199-.3294-.2225-.5275v-8c0-.1989.079-.3897.2197-.5303.1406-.1407.3314-.2197.5303-.2197s.3897.079.5303.2197c.1407.1406.2197.3314.2197.5303v8c-.0026.1981-.0824.3874-.2225.5275s-.3294.2199-.5275.2225z"></path><path d="m20.75 19.25h-8c-.1989 0-.3897-.079-.5303-.2197-.1407-.1406-.2197-.3314-.2197-.5303s.079-.3897.2197-.5303c.1406-.1407.3314-.2197.5303-.2197h8c.1989 0 .3897.079.5303.2197.1407.1406.2197.3314.2197.5303s-.079.3897-.2197.5303c-.1406.1407-.3314.2197-.5303.2197z"></path></g></svg>
                                    <span>Add New User</span>
                                </h4>
                                <button type="button" class="btn-close" id="addUserModalClose" data-bs-dismiss="modal" @click="closeAddModal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body pt-0 ">
                                <div class="glob_scroll2 pe-2">

                                    <!-- Section 1: Role Selection (Top) -->
                                    <div class="container-fluid px-4 bg-white  py-3">
                                        <label class="form-label">Role</label>
                                        <select v-model="userRole" @change="updateRoleFor" :class="['form-control def_14_size', { 'is-invalid': validationErrors.userRole }]" required>
                                            <option value="">Select Role</option>
                                            <option v-for="role in rolesData" :value="role.id">{{ role.name }}</option>
                                        </select>
                                        <small v-if="validationErrors.userRole" ><ErrorMessage :msg="validationErrors.userRole[0]"></ErrorMessage></small>
                                    </div>

                                    <!-- Section 2: Basic Information -->
                                    <h5 class="list-header my-3 ">Basic Information</h5>
                                    <div class="container-fluid px-4 bg-white  py-3">
                                        <div class="row mb-2">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">First Name</label>
                                                <input type="text" v-model="firstName" placeholder="Enter first name" :class="['form-control def_14_size', { 'is-invalid': validationErrors.firstName }]" required />
                                                <small v-if="validationErrors.firstName"><ErrorMessage :msg="validationErrors.firstName[0]"></ErrorMessage></small>
                                            </div>
                                            <div class="col-md-6  mb-3  ">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" v-model="lastName" placeholder="Enter last name" :class="['form-control def_14_size', { 'is-invalid': validationErrors.lastName }]" required />
                                                <small v-if="validationErrors.lastName"><ErrorMessage :msg="validationErrors.lastName[0]"></ErrorMessage></small>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-6  mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" v-model="email" placeholder="Enter email address" @blur="handelEmailCheck" :class="['form-control def_14_size', { 'is-invalid': validationErrors.email }]" required />
                                                <small v-if="validationErrors.email" ><ErrorMessage :msg="validationErrors.email[0]"></ErrorMessage></small>
                                            </div>
                                            <div class="col-md-6  mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="tel" v-model="phone" placeholder="Enter phone number" @input="formatPhoneNumberInput" @paste.prevent="handlePaste" :class="['form-control def_14_size', { 'is-invalid': validationErrors.phone }]" />
                                                <small v-if="validationErrors.phone" ><ErrorMessage :msg="validationErrors.phone[0]"></ErrorMessage></small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section 3: Security -->
                                    <h5 class="list-header my-3 ">Security</h5>
                                    <div class="container-fluid px-4 bg-white  py-3">
                                        <div class="row  mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Password</label>
                                                <input type="password" v-model="password" :class="['form-control def_14_size', { 'is-invalid': validationErrors.password }]" required />
                                                <small v-if="validationErrors.password"><ErrorMessage :msg="validationErrors.password[0]"></ErrorMessage></small>
                                            </div>
                                            <div class="col-md-6   mb-3">
                                                <label class="form-label">Confirm Password</label>
                                                <input type="password" v-model="confirmPassword" :class="['form-control def_14_size', { 'is-invalid': validationErrors.confirmPassword }]" required />
                                                <small v-if="validationErrors.confirmPassword"><ErrorMessage :msg="validationErrors.confirmPassword[0]"></ErrorMessage></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="['is_org_it_hod', 'is_org_it_director'].includes(selectedRoleFor)">
                                        <!-- Section 4: Organization (Conditional) -->
                                        <h5 class="list-header my-3 ">Organization</h5>
                                        <div class="container-fluid px-4 bg-white py-3">
                                            <div class="mb-4">
                                            <div class="row mb-2">
                                                <!-- Organization -->
                                                <div :class="{ 'col-md-12': ['is_org_it_hod'].includes(selectedRoleFor), 'col-md-6': ['is_org_it_director'].includes(selectedRoleFor)}">
                                                    <label class="form-label" for="org">Organization</label>



                                                    <select v-model="organization" :class="['form-control def_14_size', { 'is-invalid': validationErrors.organization }]" @change="handelOrg" required>
                                                        <option value="">Select Organization</option>
                                                        <option v-if="orgdata" :value="orgdata.id">{{ orgdata.name }}</option>
                                                    </select>




                                                  <!--   <multiselect id="org" v-model="organization" placeholder="Search or choose organization" label="name" track-by="id" :options="orgdata" :searchable="true" @select="getDeviceSubOrg($event.id)" @remove="handleOrgRemove" :allow-empty="true" :internal-search="true" :preserve-search="true" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['',{ 'is-invalid': validationErrors.organization },]" required>
                                                        <template #noResult>
                                                            <span class="custom-message">No organization found.</span>
                                                        </template>
                                                        <template #noOptions>
                                                            <span class="custom-message">No organization found.</span>
                                                        </template>
                                                    </multiselect> -->
                                                    <small v-if="validationErrors.organization" ><ErrorMessage :msg="validationErrors.organization[0]"></ErrorMessage></small>
                                                </div>
                                                <!-- Sub-Organization -->
                                                <div class="col-md-6" v-if="['is_org_it_director'].includes(selectedRoleFor)">
                                                    <label  for="" class="form-label">Sub-Organization</label>
                                                    <multiselect id="sub-org" v-model="subOrganization" placeholder="Search or choose sub organization" label="name" track-by="id" :options="subOrganizations" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', {'is-invalid':validationErrors.subOrganization,},]" required>
                                                        <template #noResult>
                                                            <span class="custom-message">No sub organization found.</span>
                                                        </template>
                                                        <template #noOptions>
                                                            <span class="custom-message">No sub organization found.</span>
                                                        </template>
                                                    </multiselect>
                                                    <small v-if="validationErrors.subOrganization" ><ErrorMessage :msg="validationErrors.subOrganization[0]"></ErrorMessage></small>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section 5: Profile Image -->
                                    <h5 class="list-header my-3 ">Profile</h5>
                                    <div class="container-fluid px-4 bg-white  py-3">
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label class="form-label">Profile Image</label>
                                                <input type="file" ref="fileInput" accept="image/*" class="form-control def_14_size" @change="uploadProfileImage" />
                                                <small v-if="validationErrors.profileImage" ><ErrorMessage :msg="validationErrors.profileImage[0]"></ErrorMessage></small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section 6: Address -->
                                    <h5 class=" list-header my-3 ">Address</h5>
                                    <div class="container-fluid px-4 bg-white  py-3">
                                        <div class="row mb-2">
                                            <!-- Street Address -->
                                            <div class="col-md-6  mb-3">
                                                <label for="streetAddress" class="form-label">Street Address</label>
                                                <input type="text" placeholder="Enter street address" v-model="streetAddress" :class="['form-control def_14_size', { 'is-invalid': validationErrors.streetAddress },]">
                                                <small v-if="validationErrors.streetAddress"><ErrorMessage :msg="validationErrors.streetAddress[0]"></ErrorMessage></small>
                                            </div>
                                            <!-- Address Line 1 -->
                                            <div class="col-md-6  mb-3">
                                                <label for="addressLine2" class="form-label">Address Line 2</label>
                                                <input type="text" placeholder="Enter address line 2" v-model="addressLine2" :class="['form-control def_14_size', { 'is-invalid': validationErrors.addressLine2 },]">
                                                <small v-if="validationErrors.addressLine2" ><ErrorMessage :msg="validationErrors.addressLine2[0]"></ErrorMessage></small>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <!-- Country -->
                                            <div class="col-md-6  mb-3">
                                                <label for="country" class="form-label">Country</label>
                                                <multiselect id="country" v-model="country" placeholder="Search or select the country" label="name" track-by="code" :options="countryData" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.country },]" required>
                                                    <template #noResult>
                                                        <span class="custom-message">No Country Found.</span>
                                                    </template>
                                                    <template #noOptions>
                                                        <span class="custom-message">No Country Found.</span>
                                                    </template>
                                                </multiselect>
                                                <small v-if="validationErrors.country" ><ErrorMessage :msg="validationErrors.country[0]"></ErrorMessage></small>
                                            </div>
                                            <!-- State -->
                                            <div class="col-md-6   mb-3">
                                                <label for="state" class="form-label">State / Province / Region</label>
                                                <multiselect id="state" v-model="state" placeholder="Search or select the state" label="name" track-by="abbreviation" :options="country ? statesData.filter((s) => s.country_code == country.code) : []" :disabled="!country" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.state },]" required>
                                                    <template #noResult>
                                                        <span class="custom-message">No State/Region Found.</span>
                                                    </template>
                                                    <template #noOptions>
                                                        <span class="custom-message">No State/Region Found.</span>
                                                    </template>
                                                </multiselect>
                                                <small v-if="validationErrors.state" >
                                                    <ErrorMessage :msg="validationErrors.state[0]"></ErrorMessage></small>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <!-- City -->
                                            <div class="col-md-6  mb-3">
                                                <label for="city" class="form-label">City</label>
                                                <multiselect id="city" v-model="city" placeholder="Search or select the city" label="name" track-by="province" :options="state ? cityData.filter((c) => c.state_code == state.abbreviation) : []" :disabled="!state" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': validationErrors.city },]" required>
                                                    <template #noResult>
                                                        <span class="custom-message">No City Found.</span>
                                                    </template>
                                                    <template #noOptions>
                                                        <span class="custom-message">No City Found.</span>
                                                    </template>
                                                </multiselect>
                                                <small v-if="validationErrors.city" ><ErrorMessage :msg="validationErrors.city[0]"></ErrorMessage></small>
                                            </div>
                                            <!-- ZIP Code -->
                                            <div class="col-md-6   mb-3">
                                                <label for="zipCode" class="form-label">ZIP / Postal Code</label>
                                                <input type="number" min="0" placeholder="Enter ZIP code" v-model="zipCode" :class="['form-control def_14_size', { 'is-invalid': validationErrors.zipCode },]">
                                                <small v-if="validationErrors.zipCode" ><ErrorMessage :msg="validationErrors.zipCode[0]"></ErrorMessage></small>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <!-- <button data-bs-dismiss="modal" class="btn btnblack text-white rounded mr-2">Cancel</button> -->
                                <button @click="registerUser" class="btn bg_blue text-white ">Save</button>
                            </div>

                        </div>
                        <!-- Add User Modal End -->
                    </div>
                </div>

                <!-- List User -->
                <div class="table-responsive">
                    <!-- Table to show the user records -->
                    <table class="table table-bordered table-hover def_14_size table_custom">
                        <thead class="table-light">
                            <tr>
                                <th width="60" >#</th>
                                <th >First Name</th>
                                <th >Last Name</th>
                                <th >Full Name</th>
                                <th >Email</th>
                                <th >Phone</th>
                                <th >Role</th>
                                <!-- <th >Reset Password</th> -->
                                <!-- <th >Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Jon</td>
                                <td>Doy</td>
                                <td>Jon Doy</td>
                                <td>jonDoy@gmail.com</td>
                                <td>(234) 456-6789 </td>
                                <td>IT HOD </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Peter</td>
                                <td>Doy</td>
                                <td>Jon Doy</td>
                                <td>peterDoy@gmail.com</td>
                                <td>(789) 755-6789 </td>
                                <td>IT Director </td>
                            </tr>




                            <!-- <tr v-if="userData.length > 0" v-for="(data, index) in userData" :key="data.id">
                                <td> {{ (paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1 }} </td>
                                <td>{{ data.first_name }} </td>
                                <td>{{ data.last_name }} </td>
                                <td>{{ data.full_name }} </td>
                                <td>{{ data.email }} </td>
                                <td>{{ formatPhoneNumber(data.phone) }} </td>
                                <td>{{ data.user_role_name }} </td> -->

                                <!-- <td>
                                    <button type="button" class="d-flex align-items-center gap-1 text-dark text-decoration-none wmax text-center fw-bold hover_global" @click="confirmSendEmail(data.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 682.667 682.667" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#000000" opacity="1" data-original="#000000" class=""></path></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0c-42.189 42.19-43.346 109.439 9.732 162.518l-13.164 13.55-40.336-40.55C-96.846 82.439-95.689 15.19-53.5-27c39.037-39.036 99.523-42.937 150.391-1.261C61.201-36.315 25.885-25.884 0 0" style="fill-opacity:1;fill-rule:nonzero;stroke:none" transform="translate(104.432 77.932)" fill="#dcdcdc" data-original="#c6e1f2" class="" opacity="1"></path><path d="M0 0c8.231-4.688 15.949-10.575 22.973-17.598 42.19-42.19 43.345-109.439-9.733-162.517l-71.886-71.887c-53.079-53.078-120.328-51.923-162.518-9.732-42.19 42.19-43.346 109.439 9.732 162.517l52.336 52.551" style="stroke-width:40;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(272.096 312.666)" fill="none" stroke="#989fa7" stroke-width="40" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#0023c4" opacity="1"></path><path d="M0 0c-8.231 4.688-15.949 10.574-22.973 17.598-42.19 42.191-43.345 109.439 9.733 162.517l71.886 71.886c53.079 53.079 120.328 51.923 162.518 9.733 42.19-42.191 43.346-109.439-9.732-162.517l-52.336-52.551" style="stroke-width:40;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(239.904 199.334)" fill="none" stroke="#1b65de" stroke-width="40" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#ff5cf3" opacity="1" class=""></path></g></g>
                                    </svg>
                                        <span>Send  Link</span>
                                    </button>
                                </td> -->
                                <!-- <td>
                                    <div class="d-flex align-items-center gap-10">
                                        <button type="button" @click="viewUserDetails(data.id)" class="btn p-0 bg-transparent d-flex align-items-center def_14_size gap-1 open_notifSlid action_toSlid"><img :src="viewIc" width="22" height="22">View</button>
                                        <a :href="`/smarttiusadmin/users/edit/${data.id}`" class="bg-transparent d-flex text-dark text-decoration-none align-items-center def_14_size gap-1 editBtn">
                                            <img :src="editIc" width="22" height="22">
                                            Edit
                                        </a>

                                        <button type="button" @click="sendResetPasswordLink(data.id)" class="btn btn-warning p-0">Send Reset Password Link</button>

                                    </div>
                                </td> -->
                            <!-- </tr>
                            <tr v-else>
                                <td colspan="8">
                                    User Not Found.
                                </td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
                <!-- <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getUserData"></pagination> -->

                <!-- User detail sidebar -->
                <div :class="userId && openListSideBar ? 'dataSlide_bar_wrap px-0 onopen_slide' : 'dataSlide_bar_wrap px-0'">
                    <div class="dataSlide_bar_inner  ">
                        <div class="card rounded-0 border-0 dataSlide_bar_div ">
                            <div class="card-header dataSlideh">
                                <div class="media d-flex justify-content-between align-items-center gap-10">
                                    <h5 class="mb-0 def_18_size">User Details</h5>
                                    <div class="close_btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 487.619 487.619" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M243.878 0a243.81 243.81 0 1 0 243.741 243.739A244.016 244.016 0 0 0 243.878 0zm110.697 322.889a22.068 22.068 0 0 1 0 31.27c-4.203 4.12-9.852 6.427-15.738 6.427s-11.537-2.306-15.74-6.427l-79.219-79.148-79.147 79.148a22.21 22.21 0 0 1-31.272 0 22.054 22.054 0 0 1-6.494-15.635 22.07 22.07 0 0 1 6.494-15.635l79.148-79.357-79.148-79.079a22.137 22.137 0 0 1 .752-30.52 22.137 22.137 0 0 1 30.52-.752l79.147 79.079 79.08-79.079a22.348 22.348 0 0 1 31.48 0 22.072 22.072 0 0 1 0 31.271l-79.08 79.079z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body px-0  bg-white dataSlide_bar_list  loaded_list">
                                <div class="block_wraps">
                                    <!-- User Data -->
                                    <transition name="fade">
                                        <div v-if="!isShowViewAnimLoader && viewUserData" class="card-body py-0">
                                            <h3 class="list-header  pb-1 def_16_size"> User Info </h3>
                                            <table class="table table-bordered def_14_size">
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Name</td>
                                                        <td><span class="result_detail_font">{{ viewUserData.full_name ?? '-' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Email</td>
                                                        <td><span class="fw-bold result_detail_font diffrent_div d-flex align-items-center gap-1"><svg id="fi_11502423" enable-background="new 0 0 512 512" height="22" viewBox="0 0 512 512" width="22" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m462.88 337.781c0 43.236-35.17 78.351-78.351 78.351h-257.057c-43.181 0-78.352-35.116-78.352-78.351v-163.562c0-14.43 3.951-27.983 10.809-39.615l125.428 125.428c18.765 18.82 43.894 29.19 70.67 29.19 26.721 0 51.85-10.37 70.615-29.19l125.428-125.428c6.859 11.632 10.809 25.184 10.809 39.615v163.562zm-78.352-241.913h-257.056c-17.832 0-34.293 6.035-47.461 16.076l126.69 126.745c13.114 13.058 30.616 20.301 49.326 20.301 18.655 0 36.158-7.243 49.271-20.301l126.69-126.745c-13.167-10.041-29.627-16.076-47.46-16.076zm0-30.232h-257.056c-59.861 0-108.584 48.723-108.584 108.584v163.562c0 59.916 48.723 108.584 108.584 108.584h257.056c59.861 0 108.584-48.668 108.584-108.584v-163.563c0-59.861-48.723-108.583-108.584-108.583z" fill-rule="evenodd"></path></svg>{{ this.viewUserData.email ?? '-' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Phone</td>
                                                        <td><span class="result_detail_font">{{ formatPhoneNumber(viewUserData.phone) ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Role</td>
                                                        <td><span class="fw-bold result_detail_font diffrent_div d-flex align-items-center gap-1"><svg id="fi_6995776" height="22" viewBox="0 0 36 36" width="22" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m18 34a1.07 1.07 0 0 1 -.48-.11l-4.87-2.43a13.79 13.79 0 0 1 -7.65-12.41v-12.14a1.07 1.07 0 0 1 1.05-1.07h3.47a7.45 7.45 0 0 0 4-1.19l3.87-2.48a1.07 1.07 0 0 1 1.15 0l3.87 2.48a7.45 7.45 0 0 0 4 1.19h3.47a1.07 1.07 0 0 1 1.12 1.07v12.14a13.79 13.79 0 0 1 -7.67 12.4l-4.87 2.43a1.07 1.07 0 0 1 -.46.12zm-10.88-26v11.05a11.67 11.67 0 0 0 6.49 10.49l4.39 2.2 4.39-2.2a11.67 11.67 0 0 0 6.49-10.49v-11.05h-2.4a9.57 9.57 0 0 1 -5.19-1.53l-3.29-2.14-3.29 2.12a9.57 9.57 0 0 1 -5.19 1.55z"></path><path d="m18 18.8a4.8 4.8 0 1 1 4.8-4.8 4.81 4.81 0 0 1 -4.8 4.8zm0-7.47a2.67 2.67 0 1 0 2.67 2.67 2.67 2.67 0 0 0 -2.67-2.66z"></path><path d="m24.4 24.67h-2.13a2.14 2.14 0 0 0 -2.13-2.13h-4.28a2.13 2.13 0 0 0 -2.13 2.13h-2.13a4.26 4.26 0 0 1 4.26-4.26h4.27a4.27 4.27 0 0 1 4.27 4.26z"></path></svg>{{ this.viewUserData.user_role_name ?? '' }}</span></td>
                                                    </tr>
                                                    <tr v-if="viewUserData.meta_key != 'subscriber' && viewUserData.meta_key != 'admin'">
                                                        <td class="fw-bold first_td_clss">Organization</td>
                                                        <td>
                                                            <span class="result_detail_font">
                                                                {{ viewUserData.parent_org ? viewUserData.parent_org : (viewUserData.org ?? '-') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="viewUserData.meta_key != 'subscriber' && viewUserData.meta_key != 'admin'">
                                                        <td class="fw-bold first_td_clss">Sub Organization</td>
                                                        <td><span class="result_detail_font">{{ viewUserData.parent_org ? viewUserData.org : '-' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Street Address</td>
                                                        <td><span class="result_detail_font">{{ viewUserData.street_address ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Address line 2</td>
                                                        <td><span class="result_detail_font">{{ viewUserData.address_line_2 ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">City</td>
                                                        <td v-if="viewUserData.city"><span class="result_detail_font">{{ getCityName(viewUserData.city) ?? '' }}</span></td>
                                                        <td v-else>-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">State</td>
                                                        <td v-if="viewUserData.state"><span class="result_detail_font">{{ getStateName(viewUserData.state) ?? '' }}</span></td>
                                                        <td v-else>-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">ZipCode</td>
                                                        <td><span class="result_detail_font">{{ viewUserData.zip ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Country</td>
                                                        <td v-if="viewUserData.country"><span class="result_detail_font">{{ getCountryName(viewUserData.country) ?? '' }}</span></td>
                                                        <td v-else>-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">User Registration Date</td>
                                                        <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg fill="none" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_9307921"><g fill="rgb(0,0,0)"><path d="m8 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m16 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m8.5 14.5001c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.15.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m12 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m15.5 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.04-.05-.08-.1-.12-.15-.04-.06-.07-.12-.09-.18-.03-.06-.05-.12-.06-.18-.01-.07-.02-.14-.02-.2 0-.26.11-.52.29-.71.1-.09.2-.16.33-.21.37-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m8.5 17.9999c-.13 0-.26-.0299-.38-.0799s-.23-.1201-.33-.2101c-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .18.19.29.4499.29.7099s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m12 17.9999c-.26 0-.52-.11-.71-.29-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .09.09.16.1999.21.3299.05.12.08.25.08.38 0 .26-.11.52-.29.71-.19.18-.45.29-.71.29z"></path><path d="m15.5 17.9999c-.26 0-.52-.11-.71-.29-.09-.09-.16-.2-.21-.33-.05-.12-.08-.25-.08-.38s.03-.26.08-.38c.05-.13.12-.24.21-.33.23-.23.58-.34.9-.27.07.01.13.03.19.06.06.02.12.05.18.09.05.03.1.08.15.12.18.19.29.45.29.71s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m20.5 9.83984h-17c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h17c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m16 22.75h-8c-3.65 0-5.75-2.1-5.75-5.75v-8.5c0-3.65 2.1-5.75 5.75-5.75h8c3.65 0 5.75 2.1 5.75 5.75v8.5c0 3.65-2.1 5.75-5.75 5.75zm-8-18.5c-2.86 0-4.25 1.39-4.25 4.25v8.5c0 2.86 1.39 4.25 4.25 4.25h8c2.86 0 4.25-1.39 4.25-4.25v-8.5c0-2.86-1.39-4.25-4.25-4.25z"></path></g></svg> {{ formatDate(viewUserData.created_at) }}</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <h3 class="list-header  pb-1 def_16_size"> Other Info </h3>

                                            <table class="table table-bordered def_14_size">
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Device Info
                                                            <a :href="$userAppUrl + `smarttiusadmin/devices?filter=true&userId=${viewUserData.id}`" target="_blank" class="btn viewallbtn def_14_size rounded-pill">View All</a>
                                                        </td>
                                                        <!-- <td><span class="result_detail_font">{{ viewUserData.full_name ?? '-' }}</span></td> -->
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Device Claim Info
                                                            <a :href="$userAppUrl + `smarttiusadmin/track-claims?filter=true&userId=${viewUserData.id}`" target="_blank" class="btn viewallbtn def_14_size rounded-pill">View All</a>
                                                        </td>
                                                        <!-- <td><span class="result_detail_font">{{ viewUserData.full_name ?? '-' }}</span></td> -->
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Device Repair Info
                                                            <a :href="$userAppUrl + `smarttiusadmin/track-repairs?filter=true&userId=${viewUserData.id}`" target="_blank" class="btn viewallbtn def_14_size rounded-pill">View All</a>
                                                        </td>
                                                        <!-- <td><span class="result_detail_font">{{ viewUserData.full_name ?? '-' }}</span></td> -->
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Transactions Info
                                                            <a :href="$userAppUrl + `smarttiusadmin/transactions?filter=true&userId=${viewUserData.id}`" target="_blank" class="btn viewallbtn def_14_size rounded-pill">View All</a>
                                                        </td>
                                                        <!-- <td><span class="result_detail_font">{{ viewUserData.full_name ?? '-' }}</span></td> -->
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </transition>
                                    <!-- Loader animation (only visible while loading) -->
                                    <transition name="fade">
                                        <div v-if="isShowViewAnimLoader" class="loader-wrap">
                                            <sidebar-animation-loader :isshow="true" />
                                        </div>
                                    </transition>
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
export default {

    props: {
        userdata: {
            type: Object,
        },
        pagination: {
            type: Object,
        },
        roles: {
            type: Object,
        },
        orgdata: {
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

            /** Used to store the family data and pagination */
            userData: this.userdata.data,
            paginationData: this.pagination,
            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,
            rolesData: this.roles,
            searchOrgText: '',
            organizations:[],
            subOrganizations: [],

            /** Tio store the view data */
            viewUserData: {},
            isShowViewAnimLoader: false,

            /* add modal */
            firstName: "",
            lastName: "",
            email: "",
            phone: "",
            password: "",
            confirmPassword: "",
            userRole: "",
            selectedRoleFor: "", // Stores role_for value
            profileImage: null,
            organization: "",
            subOrganization: "",
            streetAddress: "",
            addressLine2: "",
            country: null,
            state: null,
            city: null,
            zipCode: null,
            /* Store add validation */
            validationErrors: {},

            /** Searching parameters */
            search_name: '',

            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,

            openListSideBar: false,
            userId: null,
        };
    },
    mounted() {
            const urlParams = new URLSearchParams(window.location.search);
            this.openListSideBar = urlParams.get('openPopup');
            this.userId = urlParams.get('userId');
            if (this.openListSideBar == 'true' && this.userId) {
                this.viewUserDetails(this.userId);
            }
    },
    methods: {

        /** Formate Date */
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        },

        /** To get the user details(View click) */
        async viewUserDetails(id) {
            show_overlay();
            this.isShowViewAnimLoader = true;
            this.viewUserData = {}; /* reset view */
            try {
                const response = await axios.get(`${this.$userAppUrl}sdcsmuser/users/view${id ? `?userId=${id}` : ""}`);
                if (response.data.success == true) {
                    this.viewUserData = response.data.viewData;
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$homeUrl}`), 3000);
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            } finally {
                setTimeout(() => {
                    this.isShowViewAnimLoader = false;
                }, 800); /* short delay for animation effect */
            }
        },

        /** To check the email (Existance)*/
        async handelEmailCheck(){
            let email_id = this.email;
            let emailData = {};
            emailData = {
                'email' : email_id
            };
            const response = await axios.post(`${this.$userAppUrl}sdcsmuser/users/checkEmail`, emailData);
            if(response.data.success == false){
                this.validationErrors.email = response.data.errors.email;
            } else {
                this.validationErrors = {};
            }
        },

        /** To close the add modal */
        closeAddModal(){
            this.firstName = "";
            this.lastName = "";
            this.email = "";
            this.phone = "";
            this.password = "";
            this.confirmPassword = "";
            this.userRole = "";
            this.selectedRoleFor = ""; // Stores role_for value
            this.profileImage = null;
            this.organization = "";
            this.subOrganization = "";
            this.streetAddress = "";
            this.addressLine2 = "";
            this.country = null;
            this.state = null;
            this.city = null;
            this.zipCode = null;

            /** Manually reset file input */
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }

            /* Store add validation */
            this.validationErrors = {};
        },

        updateRoleFor() {
            const selectedRole = this.rolesData.find(role => role.id == this.userRole);
            this.selectedRoleFor = selectedRole ? selectedRole.role_for : "";
        },
        handelOrg() {
            if (this.organization) {
                this.getDeviceSubOrg(this.organization);
            } else {
                this.organization = '';
                this.subOrganizations = [];
            }
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
            this.$nextTick(() => this.formatPhoneNumberInput());
        },

        /** get sub organizations of organizations */
        async getDeviceSubOrg(orgID) {
            show_ajax_loader();
            if (!orgID) {
                this.subOrganizations = [];
                hide_ajax_loader();
            }
            try {
                const response = await axios.get( `${this.$userAppUrl}sdcsmuser/sub-org?orgId=${orgID}`);
                if (response.data.success == true) {
                    this.subOrganizations = response.data.subOrgData;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again';
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    /**
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        5000
                    );
                    */
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** Handle Remove Organization */
        handleOrgRemove(){
            this.subOrganization = '';
            this.subOrganizations = [];
        },

        /** Profile Image */
        uploadProfileImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.profileImage = file; /** Correctly set the image for add mode */
            }
        },

        /** To get the users by using paging */
        async getUserData(page = 1, search_name = this.search_name) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}sdcsmuser/users?page=${page}${search_name && search_name !== "" ? `&search=${search_name}` : ""}`
                );
                if (response.data.success == true) {
                    if (response.data.userData && response.data.pagination) {
                        this.userData = response.data.userData.data;
                        this.paginationData = response.data.pagination;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, Please try again.';
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, Please try again.';
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** Register new user */
        async registerUser(e)
        {
            e.preventDefault();
            show_ajax_loader();
            /** Clear previous errors */
            this.validationErrors = {};
            let validationPassed = true;

            /* Validate User details */
            if (!this.firstName) {
                this.validationErrors.firstName = ['First name can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.email) {
                this.validationErrors.email = ['Email address can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.phone) {
                this.validationErrors.phone = ['Phone number can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.password) {
                this.validationErrors.password = ['Password can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.confirmPassword) {
                this.validationErrors.confirmPassword = ['Confirm password can\'t be empty.'];
                validationPassed = false;
            }

            if (this.password!== this.confirmPassword) {
                this.validationErrors.confirmPassword = ['Password and Confirm password should match.'];
                validationPassed = false;
            }

            if (!this.userRole) {
                this.validationErrors.userRole = ['User role can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.organization && this.selectedRoleFor && (this.selectedRoleFor == 'is_org_it_hod' || this.selectedRoleFor == 'is_org_it_director')) {
                this.validationErrors.organization = ['Organization can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.streetAddress) {
                this.validationErrors.streetAddress =[ 'Street address can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.country) {
                this.validationErrors.country = ['Country can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.state) {
                this.validationErrors.state = ['State can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.city) {
                this.validationErrors.city = ['City can\'t be empty.'];
                validationPassed = false;
            }

            if (!this.zipCode) {
                this.validationErrors.zipCode = ['Zip code can\'t be empty.'];
                validationPassed = false;
            }

            /** if (!this.profileImage) {
                this.validationErrors.profileImage = ['Profile image can\'t be empty.'];
                validationPassed = false;
            } */

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                /** Storing user data in an object */
                const userData = {
                    firstName: this.firstName,
                    lastName: this.lastName,
                    email: this.email,
                    phone: this.phone.replace(/\D/g, ''),
                    password: this.password,
                    confirmPassword: this.confirmPassword,
                    userRole: this.userRole,
                    userRoleFor: this.selectedRoleFor,
                    organization: this.organization,
                    subOrganization: this.subOrganization['id'],
                    streetAddress: this.streetAddress,
                    addressLine2: this.addressLine2,
                    country: this.country['code'],
                    state: this.state['abbreviation'],
                    city: this.city['province'],
                    zipCode: this.zipCode,
                };
                /** Append the image file if available */
                if (this.profileImage instanceof File) {
                    userData.profileImage = this.profileImage;
                }

                let response = await axios.post(`${this.$userAppUrl}sdcsmuser/users/store`, userData, { headers: { "Content-Type": "multipart/form-data" }, });

                if (response.data.success == true) {
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Fetch/update data */
                    this.getUserData(1);
                    /* Hide the modal */
                    hide_user_popup('#addUserModalClose');
                    /** To make the form empty */
                    this.closeAddModal();
                    /* Fetch/update data */
                    this.getUserData();
                    hide_ajax_loader();
                } else if (response.data.errors) {
                    this.validationErrors = response.data.errors;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    /* Hide the modal */
                    hide_user_popup('#addUserModalClose');
                    /* Fetch/update data */
                    this.getUserData(1);
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    /** setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        3000
                    ); */
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    /* Hide the modal */
                    hide_user_popup('#addUserModalClose');
                    /* Fetch/update data */
                    this.getUserData(1);
                }
            }
        },

        /** On click of the cancel from the search */
        async cancelSearch() {
            show_ajax_loader();
            this.search_name = '';
            this.getUserData(1);
            hide_ajax_loader();
        },

        /** Reset User Add form */
        resetUserAddForm() {
            show_ajax_loader();
            /** Make field variables empty */
            this.firstName = '';
            this.lastName = '';
            this.email = '';
            this.phone = '';
            this.password = '';
            this.confirmPassword = '';
            this.userRole = '';
            this.profileImage = null;
            this.organization = '';
            this.subOrganization = '';
            this.streetAddress = '';
            this.addressLine2 = '';
            this.country = null;
            this.state = null;
            this.city = null;
            this.zipCode = '';
            /** Make validation error empty */
            this.validationErrors = '';
            hide_ajax_loader();
        },

        /** get country */
        getCountryName(countryCode) {
            if (countryCode) {
                const country = this.countryData.find((c) => c.code == countryCode);
                return country ? country.name : '';
            }
            return '';
        },

        /* get state */
        getStateName(stateCode) {
            if (stateCode) {
                const state = this.statesData.find((s) => s.abbreviation == stateCode);
                return state ? state.name : '';
            }
            return '';
        },

        /* get city */
        getCityName(cityCode) {
            if (cityCode) {
                const city = this.cityData.find((c) => c.province == cityCode);
                return city ? city.name : '';
            }
            return '';
        },
        /** confirmSendEmail */
        confirmSendEmail(id) {
            this.$deleteAlertMessage.data.isDelete = false;
            this.$deleteAlertMessage.data.isChangeStatus = true;
            this.$deleteAlertMessage.data.itemId = id;
            this.$deleteAlertMessage.data.heading = 'Send Email';
            this.$deleteAlertMessage.data.message = 'Are you sure you want to send this email?';
            this.$deleteAlertMessage.data.callback = this.sendResetPasswordLink;
        },
        /** Send Reset Password Link */
        // async sendResetPasswordLink(userId) {
        //     show_ajax_loader();
        //     try {
        //         let response = await axios.get(`${this.$userAppUrl}sdcsmuser/generate-reset-password/${userId}`);
        //         if (response.data.success == true) {
        //             this.$alertMessage.success = true;
        //             this.$alertMessage.message = response.data.msg;
        //             hide_ajax_loader();
        //         } else {
        //             this.$alertMessage.success = false;
        //             this.$alertMessage.message = 'Something went wrong, please try again.';
        //             hide_ajax_loader();
        //         }
        //     } catch (error) {
        //         if (error && error.response && error.response.data && error.response.data.error) {
        //             this.$alertMessage.success = false;
        //             this.$alertMessage.message = "Something went wrong, please try again.";
        //             hide_ajax_loader();
        //         } else {
        //             this.$alertMessage.success = false;
        //             this.$alertMessage.message = 'Something went wrong, please try again.';
        //             hide_ajax_loader();
        //         }
        //     }
        // },
    },
};
</script>
