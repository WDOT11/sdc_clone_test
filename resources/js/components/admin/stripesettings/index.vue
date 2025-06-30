<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0 ">
            <div class="card-body">
                <h4 class=" border-bottom pb-2 coman_main_heading">Manage Stripe Setting</h4>
                <!-- Publishable Key Field -->
                <div class="mb-3 row mt-3">

                    <div class="form-group">
                        <label for="publishableKey" class="form-label">Publishable Key <span class="text-danger">*</span></label>
                        <input type="text" v-model="publishableKey" :class="[ 'form-control', { 'is-invalid': validationErrors.publishableKey }, ]" id="publishableKey" placeholder="Enter Publishable Key" required />
                        <small v-if="validationErrors.publishableKey"><ErrorMessage :msg="validationErrors.publishableKey[0]"></ErrorMessage></small>
                    </div>
                </div>

                <!-- Secret Key Field -->
                <div class="mb-3 row">
                    <div class="form-group">
                        <label for="secretKey" class="form-label">Secret Key <span class="text-danger">*</span></label>
                        <input type="text" v-model="secretKey" :class="['form-control', {'is-invalid': validationErrors.secretKey}]" id="secretKey" placeholder="Enter Secret Key" required />
                        <small v-if="validationErrors.secretKey" ><ErrorMessage :msg="validationErrors.secretKey[0]"></ErrorMessage></small>
                    </div>
                </div>

                <!-- Stripe Web Hook -->
                <div class="mb-3 row">
                    <div class="form-group">
                        <label for="webhookUrl" class="form-label">Webhook URL <span class="text-danger">*</span></label>
                        <input type="text" v-model="webhookUrl" :class="[ 'form-control', { 'is-invalid': validationErrors.webhookUrl }, ]" id="webhookUrl" placeholder="Enter Webhook URL" required />
                        <small v-if="validationErrors.webhookUrl" ><ErrorMessage :msg="validationErrors.webhookUrl[0]"></ErrorMessage></small>
                    </div>
                </div>
                <!-- Stripe Web Hook Secret -->
                <div class="mb-3 row">
                    <div class="form-group">
                        <label for="webhookSecret" class="form-label">Webhook Secret <span class="text-danger">*</span></label>
                        <input type="text" v-model="webhookSecret" :class="[ 'form-control', { 'is-invalid': validationErrors.webhookSecret }, ]" id="webhookSecret" placeholder="Enter Webhook Secret" required />
                        <small v-if="validationErrors.webhookSecret" ><ErrorMessage :msg="validationErrors.webhookSecret[0]"></ErrorMessage></small>
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
            stripedata: {
                type: Object,
                required: true
            },
        },
        data(){
            return {
                publishableKey: this.stripedata.publishableKey || '',
                secretKey: this.stripedata.secretKey || '',
                webhookUrl: this.stripedata.webhookUrl || '',
                webhookSecret: this.stripedata.webhookSecret || '',
                /** Validation */
                validationErrors: {},
            }
        },
        methods: {
            /** Submit Form */
            async submitForm(e) {
                show_ajax_loader();
                e.preventDefault();
                /** Clear previous errors */
                this.validationErrors = {};

                let validationPassed = true;

                /* Validate Publishable Key */
                if (!this.publishableKey) {
                    this.validationErrors.publishableKey = [`Publishable Key can't be empty.`];
                    validationPassed = false;
                }

                /* Validate Secret Key */
                if (!this.secretKey) {
                    this.validationErrors.secretKey = [`Secret Key can't be empty.`];
                    validationPassed = false;
                }
                /* Validate Webhook URL */
                if (!this.webhookUrl) {
                    this.validationErrors.webhookUrl = [`Webhook URL can't be empty.`];
                    validationPassed = false;
                }
                /* Validate Webhook Secret */
                if (!this.webhookSecret) {
                    this.validationErrors.webhookSecret = [`Webhook Secret can't be empty.`];
                    validationPassed = false;
                }
                /* Validate Webhook URL */
                if (!this.webhookUrl.match(/^(https?:\/\/)?([a-z0-9-]+\.)+[a-z]{2,}(:\d+)?(\/.*)?$/i)) {
                    this.validationErrors.webhookUrl = [`Webhook URL is not valid.`];
                    validationPassed = false;
                }
                /* Validate Webhook Secret */
                if (!this.webhookSecret.match(/^[a-zA-Z0-9_]+$/)) {
                    this.validationErrors.webhookSecret = [`Webhook Secret is not valid.`];
                    validationPassed = false;
                }
                /* Validate Publishable Key */
                if (!this.publishableKey.match(/^[a-zA-Z0-9_]+$/)) {
                    this.validationErrors.publishableKey = [`Publishable Key is not valid.`];
                    validationPassed = false;
                }
                /* Validate Secret Key */
                if (!this.secretKey.match(/^[a-zA-Z0-9_]+$/)) {
                    this.validationErrors.secretKey = [`Secret Key is not valid.`];
                    validationPassed = false;
                }

                /* Stop submission if validation fails */
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
                try {
                    /** Storing stripe data in an object */
                    const stripeData = {
                        publishableKey: this.publishableKey,
                        secretKey: this.secretKey,
                        webhookUrl: this.webhookUrl,
                        webhookSecret: this.webhookSecret
                    };
                    let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/stripe-setting/store`, stripeData);

                    if (response.data.success == true) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        /** Make input field empty */
                        this.publishableKey = response.data.publishableKey || '';
                        this.secretKey = response.data.secretKey || '';
                        this.webhookUrl = response.data.webhookUrl || '';
                        this.webhookSecret = response.data.webhookSecret || '';
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