<template>
    <div class="container-fluid ">
        <div class="card onewhitebg border-0 p-3 mt_12">
            <h4 class="coman_main_heading border-bottom pb-2 mb-4">Edit Profile</h4>
            <!-- Edit User Profile Form -->
            <div class="edit-user-form">
                <!-- User Name -->
                <div class="row ">
                    <div class="col-md-6">
                        <label class="form-label" for="">First Name <span class="text-danger">*</span></label>
                        <input type="text" placeholder="First Name" v-model="update_firstName" :class="['form-control', { 'is-invalid': editValidationErrors.firstName },]" required />
                        <small v-if="editValidationErrors.firstName" >
                            <ErrorMessage :msg="editValidationErrors.firstName[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="">Last Name <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Last Name" v-model="update_lastName" :class="['form-control', { 'is-invalid': editValidationErrors.lastName },]" required />
                        <small v-if="editValidationErrors.lastName" >
                            <ErrorMessage :msg="editValidationErrors.lastName[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- User Email or Phone  -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label" for="">Email <span class="text-danger">*</span></label>
                        <input type="email" placeholder="Email" v-model="update_email" :class="['form-control', { 'is-invalid': editValidationErrors.email },]" required />
                        <small v-if="editValidationErrors.email" > <ErrorMessage :msg="editValidationErrors.email[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="">Phone <span class="text-danger">*</span></label>
                        <input type="tel" placeholder="Phone" v-model="update_phone" :class="['form-control', { 'is-invalid': editValidationErrors.phone },]" @input="formatPhoneNumberInput" @paste.prevent="handlePaste"/>
                        <small v-if="editValidationErrors.phone" ><ErrorMessage :msg="editValidationErrors.phone[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- User Password or Confirm Password  -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label" for="">Password</label>
                        <input type="password" placeholder="Password" v-model="update_password" :class="['form-control', { 'is-invalid': editValidationErrors.password },]" autocomplete="new-password" required />
                        <small v-if="editValidationErrors.password" ><ErrorMessage :msg="editValidationErrors.password[0]"></ErrorMessage></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="">Confirm Password</label>
                        <input type="password" placeholder="Confirm Password" v-model="update_confirmPassword" :class="['form-control', { 'is-invalid': editValidationErrors.confirmPassword },]" required />
                        <small v-if="editValidationErrors.confirmPassword" ><ErrorMessage :msg="editValidationErrors.confirmPassword[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- User Profile Image and Role  -->
                <div class="row mt-3">
                    <!-- User Profile Image -->
                    <div class="col-md-6">
                        <label class="form-label" for="update_profile_img">Profile Image</label>
                        <div class="mb-2 edituser_img">
                            <img v-if="update_profileImagePreview" :src="update_profileImagePreview"
                                alt="Profile Image" class="img-fluid "  />
                            <img v-else-if="$authUser.profile_image" :src="$authUser.profile_image"
                                alt="Profile Image" class="img-fluid "  />
                            <img v-else :src="$imagePath + 'profile.png'" alt="Default Profile Image" class=""  />
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve"><circle r="12" cx="12" cy="12" fill="#ffffff" shape="circle"></circle><g transform="matrix(0.7,0,0,0.7,3.6000000000000014,3.5999999284744266)"><linearGradient id="a" x1="4.01" x2="19.99" y1="21.49" y2="5.51" gradientUnits="userSpaceOnUse"><stop stop-opacity="1" stop-color="#0342a8" offset="0"></stop><stop stop-opacity="1" stop-color="#60d0fd" offset="1"></stop></linearGradient><path fill="url(#a)" d="M20 6.25h-3.46l-.6-1.8a1.75 1.75 0 0 0-1.66-1.2H9.72a1.75 1.75 0 0 0-1.66 1.2l-.6 1.8H4A1.76 1.76 0 0 0 2.25 8v11A1.76 1.76 0 0 0 4 20.75h16A1.76 1.76 0 0 0 21.75 19V8A1.76 1.76 0 0 0 20 6.25zm-8 11.5A4.75 4.75 0 1 1 16.75 13 4.75 4.75 0 0 1 12 17.75zM15.25 13A3.25 3.25 0 1 1 12 9.75 3.26 3.26 0 0 1 15.25 13z" opacity="1" data-original="url(#a)"></path></g></svg>

                        </div>
                        <input type="file" id="update_profile_img" accept="image/*" class="form-control"
                            @change="uploadUpdateProfileImage" />
                        <small v-if="editValidationErrors.profileImage" ><ErrorMessage :msg="editValidationErrors.profileImage[0]"></ErrorMessage></small>
                    </div>
                </div>

                <!-- User Address  -->
                <div class="row mt-3">
                    <!-- Street Address -->
                    <div class="col">
                        <label  for="streetAddress" class="form-label">Street Address <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Enter street address" v-model="update_streetAddress"
                            :class="['form-control', { 'is-invalid': editValidationErrors.streetAddress },]">
                        <small v-if="editValidationErrors.streetAddress" ><ErrorMessage :msg="editValidationErrors.streetAddress[0]"></ErrorMessage></small>
                    </div>
                    <!-- Address Line 1 -->
                    <div class="col">
                        <label for="addressLine2" class="form-label">Address Line 2</label>
                        <input type="text" placeholder="Enter address line 2" v-model="update_addressLine2" :class="['form-control', { 'is-invalid': editValidationErrors.addressLine2 },]">
                        <small v-if="editValidationErrors.addressLine2" ><ErrorMessage :msg="editValidationErrors.addressLine2[0]"></ErrorMessage></small>
                    </div>
                </div>
                <div class="row mt-3">
                    <!-- Country -->
                    <div class="col">
                        <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                        <multiselect id="country" v-model="update_country" placeholder="Search or select the country" label="name" track-by="code" :options="countryData" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': editValidationErrors.country },]" required>
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
                    <div class="col">
                        <label for="state" class="form-label">State / Province / Region <span class="text-danger">*</span></label>
                        <multiselect id="state" v-model="update_state" placeholder="Search or select the state" label="name" track-by="abbreviation" :options="update_country ? statesData.filter((s) => s.country_code == update_country.code) : []" :disabled="!update_country" :multiple="false" :taggable="false" selectLabel="" deselectLabel="" :class="['', { 'is-invalid': editValidationErrors.state },]" required>
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
                <div class="row mt-3">
                    <!-- City -->
                    <div class="col">
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
                    <div class="col">
                        <label for="zipCode" class="form-label">ZIP / Postal Code <span class="text-danger">*</span></label>
                        <input type="number" min="0" placeholder="Enter ZIP code" v-model="update_zipCode" :class="['form-control', { 'is-invalid': editValidationErrors.zipCode },]">
                        <small v-if="editValidationErrors.zipCode" ><ErrorMessage :msg="editValidationErrors.zipCode[0]"></ErrorMessage></small>
                    </div>

                </div>
                <!-- Submit Or Cancel Button  -->
                <div class="flex justify-end mt-3">
                    <button @click="cancelUpdate" class="btn btnblack text-white  rounded mr-2">
                        Cancel
                    </button>
                    <button @click="updateUser" class="btn bg_blue  text-white  rounded">
                        Save
                    </button>
                </div>
            </div>
            <!-- Edit User Profile Form End -->
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            editUserId: "",
            update_firstName: "",
            update_lastName: "",
            update_email: "",
            update_phone: "",
            update_password: "",
            update_confirmPassword: "",
            update_profileImage: null,
            update_profileImagePreview : null,
            update_streetAddress: "",
            update_addressLine2: "",
            update_country: "",
            update_state: "",
            update_city: "",
            update_zipCode: "",
            countryData: this.$countries ?? [],
            statesData: this.$states ?? [],
            cityData: this.$cities ?? [],
            editValidationErrors: {},
        };
    },
    created() {
        this.getUserData();


    },
    methods: {
        /** On click of cancel button */
        cancelUpdate(){
            show_ajax_loader();
            location.href = `${this.$userAppUrl}smarttiusadmin/`;
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
        async getUserData() {
            show_ajax_loader();
            try {
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/profile/user-data`);
                if (response.data.success == true && response.data.user_Details) {
                    const userDetails = response.data.user_Details;
                    this.editUserId = userDetails.id ?? "";
                    this.update_firstName = userDetails.first_name ?? "";
                    this.update_lastName = userDetails.last_name ?? "";
                    this.update_email = userDetails.email ?? "";
                    this.update_phone = userDetails.phone ? this.formatPhoneNumber(userDetails.phone) : "";
                    this.update_streetAddress = userDetails.street_address ?? "";
                    this.update_addressLine2 = userDetails.address_line_2 ?? "";
                    this.update_country = this.countryData.find(c => c.code == userDetails.country) || null;
                    // Find the correct state object (only if country is set)
                    this.update_state = this.update_country ? this.statesData.find(s => s.abbreviation == userDetails.state && s.country_code == this.update_country.code) || null : null;
                    // Find the correct city object (only if state is set)
                    this.update_city = this.update_state ? this.cityData.find(c => c.state_code == this.update_state.abbreviation && c.province == userDetails.city) || null : null;
                    this.update_zipCode = userDetails.zip ?? "";
                    hide_ajax_loader();

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
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                        3000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },

        /** Reset User form */
        resetUserForm() {
            show_ajax_loader();
            /** Make field variables empty */
            this.update_firstName = '';
            this.update_lastName = '';
            this.update_email = '';
            this.update_phone = '';
            this.update_password = '';
            this.update_confirmPassword = '';
            this.update_profileImage = null;
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
        formatPhoneNumber(phone = this.update_phone) {
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
            this.update_phone = this.formatPhoneNumber(this.update_phone);
        },

        /**  Paste Time check */
        handlePaste(event) {
            let paste = (event.clipboardData || window.clipboardData).getData('text');
            let digits = paste.replace(/\D/g, '').slice(0, 10);
            this.update_phone = digits;
            this.$nextTick(() => this.formatPhoneNumber());
        },

        /** Update the user(on submit) */
        async updateUser(e) {
            e.preventDefault();
            show_ajax_loader();
            /** Clear previous errors */
            this.editValidationErrors = {};
            let validationPassed = true;
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
            }

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

                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/profile/update/${this.editUserId}`, userData, { headers: { "Content-Type": "multipart/form-data" }, });

                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    /* Fetch/update data */
                    /* setTimeout(() =>(location.href = `${this.$userAppUrl}smarttiusadmin`),700); */
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
                            (location.href = `${this.$userAppUrl}smarttiusadmin/profile`),
                        2000
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
