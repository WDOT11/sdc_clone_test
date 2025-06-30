<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0 ">
            <div class="card-body">
                <h4 class=" border-bottom pb-2 coman_main_heading">Manage SMTP Setting</h4>
                <!-- Email Field -->
                <div class="mb-3 row mt-3">
                    <div class="form-group">
                        <label for="senderEmail" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" v-model="senderEmail" :class="[ 'form-control', { 'is-invalid': validationErrors.senderEmail }, ]" id="senderEmail" placeholder="Enter Sender Email" required />
                        <small v-if="validationErrors.senderEmail"><ErrorMessage :msg="validationErrors.senderEmail[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- API Key Field -->
                <div class="mb-3 row mt-3">
                    <div class="form-group">
                        <label for="apiKey" class="form-label">API Key <span class="text-danger">*</span></label>
                        <input type="text" v-model="apiKey" :class="[ 'form-control', { 'is-invalid': validationErrors.apiKey }, ]" id="apiKey" placeholder="Enter API Key" required />
                        <small v-if="validationErrors.apiKey"><ErrorMessage :msg="validationErrors.apiKey[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Buttons -->
                <button type="submit" class="btn bg_blue text-white" @click="submitForm">Submit</button>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        props: {
            smtpdata: {
                type: Object,
                required: true
            },
        },
        data(){
            return {
                apiKey: this.smtpdata.apiKey || '',
                senderEmail: this.smtpdata.senderEmail || '',
                /** Validation */
                validationErrors: {},
            }
        },
        methods: {
            /** Submit Form */
            async submitForm() {
                show_ajax_loader();
                /** Clear previous errors */
                this.validationErrors = {};

                let validationPassed = true;

                /* Validate API Key */
                if (!this.apiKey) {
                    this.validationErrors.apiKey = [`API Key can't be empty.`];
                    validationPassed = false;
                }

                /* Validate Sender Email */
                if (!this.senderEmail) {
                    this.validationErrors.senderEmail = [`Please enter sender email.`];
                    validationPassed = false;
                }
                /* Stop submission if validation fails */
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
                try {
                    /** Storing smtp data in an object */
                    const smtpData = {
                        apiKey: this.apiKey,
                        senderEmail: this.senderEmail,
                    };
                    let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/smtp-setting/store`, smtpData);

                    if (response.data.success == true) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        /** Make input field empty */
                        this.apiKey = response.data.apiKey || '';
                        this.senderEmail = response.data.senderEmail || '';
                        this.validationErrors = {};
                    } else if (response.data.errors ) {
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
                        this.$alertMessage.message = error.response.data.error;
                        setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    }
                }
            }
        }
    }
</script>