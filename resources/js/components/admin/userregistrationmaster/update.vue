<template>
    <div class="container-fluid ">
        <div class="card onewhitebg border-0 p-3 mt_12">
            <h4 class="coman_main_heading border-bottom pb-2 ">Edit User</h4>
            <!-- Edit User Form -->
            <div class="edit-user-form">
                <!-- User Name -->
                <div class="row ">
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label class="form-label" for="">First Name <span class="text-danger">*</span></label>
                        <input type="text" placeholder="First Name" v-model="update_firstName" :class="['form-control', { 'is-invalid': editValidationErrors.firstName },]" required />
                        <small v-if="editValidationErrors.firstName" >
                            <ErrorMessage :msg="editValidationErrors.firstName[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label class="form-label" for="">Last Name <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Last Name" v-model="update_lastName" :class="['form-control', { 'is-invalid': editValidationErrors.lastName },]" required />
                        <small v-if="editValidationErrors.lastName" >
                            <ErrorMessage :msg="editValidationErrors.lastName[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- User Email or Phone  -->
                <div class="row ">
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label class="form-label" for="">Email <span class="text-danger">*</span></label>
                        <input type="email" placeholder="Email" v-model="update_email" :class="['form-control', { 'is-invalid': editValidationErrors.email },]" required />
                        <small v-if="editValidationErrors.email" > <ErrorMessage :msg="editValidationErrors.email[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label class="form-label" for="">Phone <span class="text-danger">*</span></label>
                        <input type="tel" placeholder="Phone" v-model="update_phone" :class="['form-control', { 'is-invalid': editValidationErrors.phone },]" @input="formatPhoneNumber" @paste.prevent="handlePaste" />
                        <small v-if="editValidationErrors.phone" ><ErrorMessage :msg="editValidationErrors.phone[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- User Password or Confirm Password  -->
                <div class="row ">
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label class="form-label" for="">Password</label>
                        <input type="password" placeholder="Password" v-model="update_password" :class="['form-control', { 'is-invalid': editValidationErrors.password },]" required />
                        <small v-if="editValidationErrors.password" ><ErrorMessage :msg="editValidationErrors.password[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label class="form-label" for="">Confirm Password</label>
                        <input type="password" placeholder="Confirm Password" v-model="update_confirmPassword" :class="['form-control', { 'is-invalid': editValidationErrors.confirmPassword },]" required />
                        <small v-if="editValidationErrors.confirmPassword" ><ErrorMessage :msg="editValidationErrors.confirmPassword[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- User Profile Image and Role  -->
                <div class="row ">
                    <!-- User Profile Image -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label class="form-label" for="update_profile_img">Profile Image</label>
                        <div class="mb-2 edituser_img">
                            <img v-if="update_profileImagePreview" :src="update_profileImagePreview"
                                alt="Profile Image" class=" user_img" width="100" height="100" />
                            <img v-else-if="userData.profile_image_path && userData.profile_image_name" :src="userData.profile_image_path + userData.profile_image_name"
                                alt="Profile Image" class=" user_img" width="100" height="100" />
                            <img v-else :src="`${$userAppUrl}images/user.png`"
                                alt="Default Profile Image" class=" user_img" width="100"
                                height="100" />
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve"><circle r="12" cx="12" cy="12" fill="#ffffff" shape="circle"></circle><g transform="matrix(0.7,0,0,0.7,3.6000000000000014,3.5999999284744266)"><linearGradient id="a" x1="4.01" x2="19.99" y1="21.49" y2="5.51" gradientUnits="userSpaceOnUse"><stop stop-opacity="1" stop-color="#0342a8" offset="0"></stop><stop stop-opacity="1" stop-color="#60d0fd" offset="1"></stop></linearGradient><path fill="url(#a)" d="M20 6.25h-3.46l-.6-1.8a1.75 1.75 0 0 0-1.66-1.2H9.72a1.75 1.75 0 0 0-1.66 1.2l-.6 1.8H4A1.76 1.76 0 0 0 2.25 8v11A1.76 1.76 0 0 0 4 20.75h16A1.76 1.76 0 0 0 21.75 19V8A1.76 1.76 0 0 0 20 6.25zm-8 11.5A4.75 4.75 0 1 1 16.75 13 4.75 4.75 0 0 1 12 17.75zM15.25 13A3.25 3.25 0 1 1 12 9.75 3.26 3.26 0 0 1 15.25 13z" opacity="1" data-original="url(#a)"></path></g></svg>
                        </div>
                        <input type="file" id="update_profile_img" accept="image/*" class="form-control"
                            @change="uploadUpdateProfileImage" />
                        <small v-if="editValidationErrors.profileImage" ><ErrorMessage :msg="editValidationErrors.profileImage[0]"></ErrorMessage></small>
                    </div>
                    <!-- User Role  -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label class="form-label" for="form-lable">Role <span class="text-danger">*</span></label>
                        <select v-model="update_userRole"
                            :class="['form-control', { 'is-invalid': editValidationErrors.userRole },]"
                            @change="updateRoleFor" required>
                            <option value="">Select Role</option>
                            <option v-for="role in rolesData" :value="role.id">{{ role.name }}</option>
                        </select>
                        <small v-if="editValidationErrors.userRole" ><ErrorMessage :msg="editValidationErrors.userRole[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Organization Or sub-org  -->
                <div v-if="['is_org_it_hod', 'is_org_it_director', 'is_org_subscriber'].includes(selectedRoleFor)"
                    class="row ">
                    <!-- Organization -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label class="form-label">Organization <span class="text-danger">*</span></label>
                        <multiselect v-model="update_organization"
                            placeholder="Search or choose organization" label="name" track-by="id"
                            :options="organizations" :searchable="true" @search-change="updateSearchOrgNameEdit"
                            @select="getSubOrg($event.id)" @remove="handleUpdateOrgRemove" :allow-empty="true"
                            :internal-search="false" :preserve-search="true" :multiple="false" :taggable="false"
                            selectLabel="" deselectLabel=""
                            :class="['', { 'is-invalid': editValidationErrors.organization },]" required>
                            <template #noResult>
                                <span class="custom-message">No organization found.</span>
                            </template>
                            <template #noOptions>
                                <span class="custom-message">No organization found.</span>
                            </template>
                        </multiselect>
                        <small v-if="editValidationErrors.organization" ><ErrorMessage :msg="editValidationErrors.organization[0]"></ErrorMessage></small>
                    </div>
                    <!-- Sub-Organization -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label  for="" class="form-label">Sub-Organization</label>
                        <multiselect id="update-sub-org" v-model="update_subOrganization"
                            placeholder="Search or choose sub organization" label="name" track-by="id"
                            :options="subOrganizations" :multiple="false" :taggable="false" selectLabel=""
                            deselectLabel="" :class="['', { 'is-invalid': editValidationErrors.subOrganization, },]"
                            required>
                            <template #noResult>
                                <span class="custom-message">No sub organization found.</span>
                            </template>
                            <template #noOptions>
                                <span class="custom-message">No sub organization found.</span>
                            </template>
                        </multiselect>
                        <small v-if="editValidationErrors.subOrganization"><ErrorMessage :msg="editValidationErrors.subOrganization[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- User Address  -->
                <div class="row ">
                    <!-- Street Address -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label  for="streetAddress" class="form-label">Street Address <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Enter street address" v-model="update_streetAddress"
                            :class="['form-control', { 'is-invalid': editValidationErrors.streetAddress },]">
                        <small v-if="editValidationErrors.streetAddress" ><ErrorMessage :msg="editValidationErrors.streetAddress[0]"></ErrorMessage></small>
                    </div>
                    <!-- Address Line 1 -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label for="addressLine2" class="form-label">Address Line 2</label>
                        <input type="text" placeholder="Enter address line 2" v-model="update_addressLine2" :class="['form-control', { 'is-invalid': editValidationErrors.addressLine2 },]">
                        <small v-if="editValidationErrors.addressLine2" ><ErrorMessage :msg="editValidationErrors.addressLine2[0]"></ErrorMessage></small>
                    </div>
                </div>
                <div class="row ">
                    <!-- Country -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                        <multiselect id="country" v-model="update_country" placeholder="Search or select the country" label="name" track-by="code" :options="countryData" :multiple="false" :taggable="false" @select="update_state = null" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': editValidationErrors.country },]" required>
                            <template #noResult>
                                <span class="custom-message">No Country Found.</span>
                            </template>
                            <template #noOptions>
                                <span class="custom-message">No Country Found.</span>
                            </template>
                        </multiselect>
                        <small v-if="editValidationErrors.country"><ErrorMessage :msg="editValidationErrors.country[0]"></ErrorMessage></small>
                    </div>
                    <!-- State -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label for="state" class="form-label">State / Province / Region <span class="text-danger">*</span></label>
                        <multiselect id="state" v-model="update_state" placeholder="Search or select the state" label="name" track-by="abbreviation" :options="update_country ? statesData.filter((s) => s.country_code == update_country.code) : []" :disabled="!update_country" :multiple="false" :taggable="false" @select="update_city = null" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': editValidationErrors.state },]" required>
                            <template #noResult>
                                <span class="custom-message">No State/Region Found.</span>
                            </template>
                            <template #noOptions>
                                <span class="custom-message">No State/Region Found.</span>
                            </template>
                        </multiselect>
                        <small v-if="editValidationErrors.state" ><ErrorMessage :msg="editValidationErrors.state[0]"></ErrorMessage></small>
                    </div>
                </div>
                <div class="row ">
                    <!-- City -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                        <multiselect id="city" v-model="update_city" placeholder="Search or select the city" label="name" track-by="province"
                            :options="update_state ? cityData.filter((c) => c.state_code == update_state.abbreviation) : []" :disabled="!update_state" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': editValidationErrors.city },]" required>
                            <template #noResult>
                                <span class="custom-message">No City Found.</span>
                            </template>
                            <template #noOptions>
                                <span class="custom-message">No City Found.</span>
                            </template>
                        </multiselect>
                        <small v-if="editValidationErrors.city" ><ErrorMessage :msg="editValidationErrors.city[0]"></ErrorMessage></small>
                    </div>
                    <!-- ZIP Code -->
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <label for="zipCode" class="form-label">ZIP / Postal Code <span class="text-danger">*</span></label>
                        <input type="number" min="0" placeholder="Enter ZIP code" v-model="update_zipCode" :class="['form-control', { 'is-invalid': editValidationErrors.zipCode },]">
                        <small v-if="editValidationErrors.zipCode" ><ErrorMessage :msg="editValidationErrors.zipCode[0]"></ErrorMessage></small>
                    </div>

                </div>
                <!-- Submit Or Cancel Button  -->
                <div class="flex justify-end mt-3">
                    <button @click="cancelUpdate" class="btn blogal_pbtn_padding  customm_reset_btn def_14_size rounded mr-2">
                        Cancel
                    </button>
                    <button @click="updateUser" class="btn bg_blue blogal_pbtn_padding  text-white def_14_size  rounded">
                        Save
                    </button>
                </div>
            </div>
            <!-- Edit User Form End -->
        </div>
    </div>
</template>
<script>
export default {
    props: {
        users: {
            type: Object,
        },
        roles: {
            type: Object,
        },
    },
    data() {
        return {
            userData: {}, /* Empty initially */
            editUserId: "",
            update_firstName: "",
            update_lastName: "",
            update_email: "",
            update_phone: "",
            update_password: "",
            update_confirmPassword: "",
            update_userRole: "",
            selectedRoleFor: "",
            update_profileImage: null,
            update_profileImagePreview : null,
            update_organization: null,
            update_subOrganization: null,
            update_streetAddress: "",
            update_addressLine2: "",
            update_country: "",
            update_state: "",
            update_city: "",
            update_zipCode: "",
            countryData: this.$countries ?? [],
            statesData: this.$states ?? [],
            cityData: this.$cities ?? [],
            rolesData: this.roles ?? [],
            searchOrgText: '',
            organizations: [],
            subOrganizations: [],
            editValidationErrors: {},
            orgID: '',
            subOrgID: '',
        };
    },
    created() {
        if (this.users) {
            this.userData = this.users;
            this.editUserId = this.users.id ?? "";
            this.update_firstName = this.users.first_name ?? "";
            this.update_lastName = this.users.last_name ?? "";
            this.update_email = this.users.email ?? "";
            this.update_phone = this.users.phone ?? "";
            this.update_streetAddress = this.users.street_address ?? "";
            this.update_addressLine2 = this.users.address_line_2 ?? "";
            this.update_userRole = this.users.role_id ?? null;
            this.update_country = this.countryData.find(c => c.code == this.users.country) || null;
            // Find the correct state object (only if country is set)
            this.update_state = this.update_country ? this.statesData.find(s => s.abbreviation == this.users.state && s.country_code == this.update_country.code) || null : null;
            // Find the correct city object (only if state is set)
            this.update_city = this.update_state ? this.cityData.find(c => c.state_code == this.update_state.abbreviation && c.province == this.users.city) || null : null;
            this.update_zipCode = this.users.zip ?? "";
        }
        this.orgID = this.users.org_id && this.users.parent_org_id ? this.users.parent_org_id : this.users.org_id;
        this.subOrgID = this.users.org_id && this.users.parent_org_id ? this.users.org_id : null;

        this.formatPhoneNumber();
        this.getOrganizations(this.searchOrgText, this.orgID);
        this.getSubOrg(this.orgID);
        this.updateRoleFor();
    },
    methods: {

        updateRoleFor() {
            const selectedRole = this.rolesData.find(role => role.id == this.update_userRole);
            this.selectedRoleFor = selectedRole ? selectedRole.role_for : "";
        },

        /** On click of cancel button */
        cancelUpdate(){
            show_ajax_loader();
            location.href = `${this.$userAppUrl}smarttiusadmin/users`;
        },

        /** To get Organizations */
        async getOrganizations(name, orgID = null) {
            show_ajax_loader();
            /* Enter at list 2 characters */
           if (!orgID && name.length < 2) {
                this.organizations = [{
                    id: null,
                    name: "Please enter at least two characters",
                }];
                hide_ajax_loader();
                return;
            }
            try {
                let response = null;
                const url = `${this.$userAppUrl}smarttiusadmin/organizations/get-organization?name=${name}${orgID ? `&orgId=${orgID}` : ''}`;
                // let url = `${this.$userAppUrl}smarttiusadmin/organizations/get-organization`;
                // if (name) {
                //     url = `${this.$userAppUrl}smarttiusadmin/organizations/get-organization?name=${name}`;
                // }
                // if (orgID) {
                //     url = `${this.$userAppUrl}smarttiusadmin/organizations/get-organization?orgId=${orgID}`;
                // }

                response = await axios.get(url);
                if (name !== this.searchOrgText) {
                    this.organizations = [{
                        id: null,
                        name: "Please enter at least two characters",
                    }];
                    hide_ajax_loader();
                    return;
                } else if (response.data.success == true && response.data.orgData.length > 0) {
                    /* if (this.editUserId) {
                        this.update_organization = response.data.orgData;
                    } */



                    if (!this.update_organization && this.editUserId && orgID) {
                        // Set only during initial load based on ID
                        const matchedOrg = response.data.orgData.find(org => org.id == orgID);
                        if (matchedOrg) {
                            this.update_organization = matchedOrg;
                        }
                    }

                    this.organizations = response.data.orgData;
                    hide_ajax_loader();
                } else {
                    /* This will show up in the dropdown regardless of success status */
                    this.organizations = [{
                        id: null,
                        name: response.data.msg ?? 'Please enter at least two characters',
                    }];
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    /* setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                        3000
                    ); */
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** organization name text */
        updateSearchOrgNameEdit(value) {
            show_ajax_loader();
            if (value.length > 0) {
                this.searchOrgText = value;
                this.getOrganizations(value, this.orgID);
                hide_ajax_loader();
            } else {
                this.searchOrgText = "";
                this.organizations = [{
                    id: null,
                    name: "Please enter at least two characters",
                }];
                hide_ajax_loader();
            }
        },

        /** get sub organizations of organizations */
        async getSubOrg(orgID) {
            show_ajax_loader();
            if (!orgID) {
                this.subOrganizations = [];
                hide_ajax_loader();
                return;
            }
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/organizations/sub-organizations?orgId=${orgID}`
                );
                if (response.data.success == true) {
                    this.subOrganizations = response.data.subOrgData;

                    /* Wait for the data to populate before selecting */
                    this.$nextTick(() => {
                        const selectedSubOrg = this.subOrganizations.find(
                            (subOrg) => subOrg.id == this.subOrgID
                        );
                        this.update_subOrganization = selectedSubOrg ? selectedSubOrg : null;
                    });
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
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
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
        handleUpdateOrgRemove() {
            this.subOrganizations = [];
        },

        /** Profile Image */
        uploadUpdateProfileImage(event) {
            const file = event.target.files[0];
            if (file) {

                if (this.update_profileImage) {
                    // Revoke old object URL
                    window.URL.revokeObjectURL(this.update_profileImagePreview);
                }
                this.update_profileImage = file; /** Correctly set the image for add mode */
                this.update_profileImagePreview = window.URL.createObjectURL(file);
            }
        },

        /** To get the users by using paging */
        async getUserData(page = 1, search_name = this.search_name) {
            show_ajax_loader();
            try {
                const response = await axios.get(
                    `${this.$userAppUrl}smarttiusadmin/users?page=${page}${search_name && search_name !== "" ? `&name=${search_name}` : ""}`
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
                    /**
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                        3000
                    );
                    */
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** close Add model */
        closeAddModel() {
            /** Close add model */
            this.showAddModal = false;
            this.resetUserAddForm();
        },

        /** Reset User Add form */
        resetUserAddForm() {
            show_ajax_loader();
            /** Make field variables empty */
            this.update_firstName = '';
            this.update_lastName = '';
            this.update_email = '';
            this.update_phone = '';
            this.update_password = '';
            this.update_confirmPassword = '';
            this.update_userRole = '';
            this.update_profileImage = null;
            this.update_organization = '';
            this.update_subOrganization = '';
            this.update_streetAddress = '';
            this.update_addressLine2 = '';
            this.update_country = null;
            this.update_state = null;
            this.update_city = null;
            this.update_zipCode = '';
            /** Make validation error empty */
            this.validationErrors = '';
            hide_ajax_loader();
        },

        /** Format phone Number */
        formatPhoneNumber() {
            if(this.update_phone !== '' && this.update_phone !== null){
                /* Keep only digits and allowed formatting chars */
                let cleaned = this.update_phone.replace(/[^\d\(\)\-\s]/g, '');

                /* Extract digits only */
                let digits = cleaned.replace(/\D/g, '');

                /* Limit to 10 digits for US/Canada */
                if (digits.length > 10) {
                    digits = digits.slice(0, 10);
                }

                /* Apply (XXX) XXX-XXXX format */
                if (digits.length > 6) {
                    this.update_phone = `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
                } else if (digits.length > 3) {
                    this.update_phone = `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                } else if (digits.length > 0) {
                    this.update_phone = `(${digits}`;
                } else {
                    this.update_phone = '';
                }
            }
        },

        /**  Paste Time check */
        handlePaste(event) {
            let paste = (event.clipboardData || window.clipboardData).getData('text');
            let digits = paste.replace(/\D/g, '').slice(0, 10);
            this.update_phone = digits;
            this.$nextTick(() => this.formatPhoneNumber());
        },

        /** Check valid phone number */
        isValidPhone(phone) {
            const pattern = /^\(\d{3}\) \d{3}-\d{4}$/;
            return pattern.test(phone);
        },

        /** Update the user(on submit) */
        async updateUser(e) {
            e.preventDefault();
            show_ajax_loader();
            /** Clear previous errors */
            this.editValidationErrors = {};
            let validationPassed = true;
            const zipCodeRegex = /^[0-9]{5}$/;
            /* Validate User details */
            if (!this.update_firstName) {
                this.editValidationErrors.firstName = ['First name can\'t be empty.'];
                validationPassed = false;
            }
            if (!this.update_lastName) {
                this.editValidationErrors.lastName = ['Last name can\'t be empty.'];
                validationPassed = false;
            }
            if (!this.update_email) {
                this.editValidationErrors.email = ['Email address can\'t be empty.'];
                validationPassed = false;
            }
            if (!this.update_phone) {
                this.editValidationErrors.phone = ['Phone number can\'t be empty.'];
                validationPassed = false;
            }
            if (this.update_password !== this.update_confirmPassword) {
                this.editValidationErrors.confirmPassword = ['Password and Confirm password should match.'];
                validationPassed = false;
            }
            if (!this.update_userRole) {
                this.editValidationErrors.userRole = ['User role can\'t be empty.'];
                validationPassed = false;
            }
           /*  if (!this.update_organization && this.selectedRoleFor && (this.selectedRoleFor == 'is_org_it_hod' || this.selectedRoleFor == 'is_org_it_director' || this.selectedRoleFor == 'is_org_subscriber')) {
                this.editValidationErrors.organization = ['Organization can\'t be empty.'];
                validationPassed = false;
            } */

            if (this.selectedRoleFor && (this.selectedRoleFor == 'is_org_it_hod' || this.selectedRoleFor == 'is_org_it_director' || this.selectedRoleFor == 'is_org_subscriber')) {
                if (!this.update_organization?.id) {
                    this.editValidationErrors.organization = ["Organization can't be empty."];
                    validationPassed = false;
                }
                /* if (!this.update_organization?.[0]?.id) {
                    this.editValidationErrors.organization = ["Organization can't be empty."];
                    validationPassed = false;
                } */
            }

            if (!this.update_streetAddress) {
                this.editValidationErrors.streetAddress = ['Street address can\'t be empty.'];
                validationPassed = false;
            }
            if (!this.update_country) {
                this.editValidationErrors.country = ['Country can\'t be empty.'];
                validationPassed = false;
            }
            if (!this.update_state) {
                this.editValidationErrors.state = ['State can\'t be empty.'];
                validationPassed = false;
            }
            if (!this.update_city) {
                this.editValidationErrors.city = ['City can\'t be empty.'];
                validationPassed = false;
            }
            if (!this.update_zipCode) {
                this.editValidationErrors.zipCode = ['Zip code can\'t be empty.'];
                validationPassed = false;
            } else if (!zipCodeRegex.test(this.update_zipCode)) {
                this.editValidationErrors.zipCode = ['Zip code must be a 5-digit number.'];
                validationPassed = false;
            }

            // if (!this.update_profileImage) {
            //     this.editValidationErrors.profileImage = ['Profile image can\'t be empty.'];
            //     validationPassed = false;
            // }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                /** Storing user data in an object */
                const userData = {
                    firstName: this.update_firstName,
                    lastName: this.update_lastName,
                    email: this.update_email,
                    phone: this.update_phone.replace(/\D/g, ''),
                    password: this.update_password,
                    confirmPassword: this.update_confirmPassword,
                    userRole: this.update_userRole,
                    userRoleFor: this.selectedRoleFor,
                    organization: this.update_organization?.id ? this.update_organization.id : null,
                    // organization: this.update_organization?.[0]?.id ? this.update_organization[0].id : null,
                    subOrganization: this.update_subOrganization?.id ? this.update_subOrganization.id : null,
                    streetAddress: this.update_streetAddress,
                    addressLine2: this.update_addressLine2,
                    country: this.update_country['code'],
                    state: this.update_state['abbreviation'],
                    city: this.update_city['province'],
                    zipCode: this.update_zipCode,
                };

                /** Append the image file if available */
                if (this.update_profileImage instanceof File) {
                    userData.profileImage = this.update_profileImage;
                }

                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/users/update/${this.editUserId}`, userData, { headers: { "Content-Type": "multipart/form-data" }, });

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;

                    /* Fetch/update data */
                    setTimeout(() =>(location.href = `${this.$userAppUrl}smarttiusadmin/users`),3000);
                } else if (response.data.errors) {
                    this.editValidationErrors = response.data.errors;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";

                    /* Fetch/update data */
                    setTimeout(
                        () =>
                            (location.href = `${this.$userAppUrl}smarttiusadmin/users`),
                        3000
                    );
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        3000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    /* Fetch/update data */
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        3000
                    );
                }
            }
        },
    },
};
</script>
