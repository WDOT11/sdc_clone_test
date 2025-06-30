<template>
    <div class="container-fluid  mt_12">
        <div class="card onewhitebg rounded border-0 ">
           <div class="card-body ">
                <h4 class="coman_main_heading border-bottom pb-2 mb-3">Support Ticket</h4>

                <!-- Step 1: User Details -->
                <div class="step">
                    <div class="mb-3 p-3 device-info border onewhitebg  rounded ">
                        <!-- <label class="form-label def_18_size">User Details</label> -->
                        <div class="row ">
                            <!-- First Name -->
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter First Name" v-model="firstName" :class="['form-control', { 'is-invalid': validationErrors.firstName },]">
                                <small v-if="validationErrors.firstName" ><ErrorMessage :msg="validationErrors.firstName[0]"></ErrorMessage></small>
                            </div>
                            <!-- Last Name -->
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Last Name" v-model="lastName" :class="['form-control', { 'is-invalid': validationErrors.lastName },]">
                                <small v-if="validationErrors.lastName" ><ErrorMessage :msg="validationErrors.lastName[0]"></ErrorMessage></small>
                            </div>
                        </div>
                        <div class="row ">
                            <!-- Phone Number -->
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" placeholder="Enter phone number" v-model="phone" @input="formatPhoneNumber" @paste.prevent="handlePaste" :class="['form-control', { 'is-invalid': validationErrors.phone },]">
                                <small v-if="validationErrors.phone" ><ErrorMessage :msg="validationErrors.phone[0]"></ErrorMessage></small>
                            </div>
                            <!-- Email -->
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" placeholder="Enter email" v-model="email" :class="['form-control', { 'is-invalid': validationErrors.email },]">
                                <small v-if="validationErrors.email" ><ErrorMessage :msg="validationErrors.email[0]"></ErrorMessage></small>
                            </div>
                        </div>
                        <div>
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Please enter the subject" v-model="subject" :class="['form-control', { 'is-invalid': validationErrors.subject },]">
                                <small v-if="validationErrors.subject" ><ErrorMessage :msg="validationErrors.subject[0]"></ErrorMessage></small>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea rows="5" v-model="message" :class="['form-control', { 'is-invalid': validationErrors.message },]" placeholder="Please enter the message."></textarea>
                                <small v-if="validationErrors.message" ><ErrorMessage :msg="validationErrors.message[0]"></ErrorMessage></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button class="btn bg_blue text-white" @click="submitForm">Raise Ticket</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {

        data() {
            return {
                firstName: '',
                lastName: '',
                phone: '',
                email: '',
                subject: '',
                message: '',
                validationErrors: {}
            };
        },

        created() {
            if (this.$authUser) {
                this.firstName = this.$authUser.first_name;
                this.lastName = this.$authUser.last_name;
                this.phone = this.$authUser.phone;
                this.email = this.$authUser.email;
            }
            this.formatPhoneNumber();
        },

        methods: {

            /** Format phone Number */
            formatPhoneNumber() {
                if(this.phone != '' && this.phone != null){
                    /* Keep only digits and allowed formatting chars */
                    let cleaned = this.phone.replace(/[^\d\(\)\-\s]/g, '');

                    /* Extract digits only */
                    let digits = cleaned.replace(/\D/g, '');

                    /* Limit to 10 digits for US/Canada */
                    if (digits.length > 10) {
                        digits = digits.slice(0, 10);
                    }

                    /* Apply (XXX) XXX-XXXX format */
                    if (digits.length > 6) {
                        this.phone = `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
                    } else if (digits.length > 3) {
                        this.phone = `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                    } else if (digits.length > 0) {
                        this.phone = `(${digits}`;
                    } else {
                        this.phone = '';
                    }
                }
            },

            /**  Paste Time check */
            handlePaste(event) {
                let paste = (event.clipboardData || window.clipboardData).getData('text');
                let digits = paste.replace(/\D/g, '').slice(0, 10);
                this.userData.phone = digits;
                this.$nextTick(() => this.formatPhoneNumber());
            },

             /** Check valid phone number */
            isValidPhone(phone) {
                const pattern = /^\(\d{3}\) \d{3}-\d{4}$/;
                return pattern.test(phone);
            },

            async submitForm() {
                show_ajax_loader();
                this.validationErrors = {};
                let validationPassed = true;

                if(!this.firstName) {
                    this.validationErrors.firstName = ['First Name is required.'];
                    validationPassed = false;
                }
                if(!this.lastName) {
                    this.validationErrors.lastName = ['Last Name is required.'];
                    validationPassed = false;
                }
                if(!this.phone) {
                    this.validationErrors.phone = ['Phone number is required.'];
                    validationPassed = false;
                }
                if (this.phone && !this.isValidPhone(this.phone)) {
                    this.validationErrors.phone = ['Please enter valid phone number.'];
                    validationPassed = false;
                }
                if(!this.email) {
                    this.validationErrors.email = ['Email is required.'];
                    validationPassed = false;
                }
                if(!this.subject) {
                    this.validationErrors.subject = ['Subject is required.'];
                    validationPassed = false;
                }
                if(!this.message) {
                    this.validationErrors.message = ['Message is required.'];
                    validationPassed = false;
                }
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }

                let formData = {
                    first_name: this.firstName,
                    last_name: this.lastName,
                    phone: this.phone.replace(/\D/g, ''),
                    email: this.email,
                    subject: this.subject,
                    message: this.message
                };

                try {
                    let response = await axios.post(`${this.$userAppUrl}sdcsmuser/support-ticket/create`, formData);
                    if (response.data.success == true) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = "Support ticket raised successfully.";

                        this.subject = '';
                        this.message = '';

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
            }
        }
    };
</script>