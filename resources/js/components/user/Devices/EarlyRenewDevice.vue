<template>
    <div class="container-fluid mt_12">
        <!-- **** Device Details Listing ****-->
        <div class="card border-0">
            <div class="card-body rounded onewhitebg">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 border-bottom pb-2">
                    <h4 class="coman_main_heading mb-0">
                       Early Renewal Devices List
                    </h4>
                    <div class="d-flex justify-content-end">
                        <button type="button" @click="renewNow" class="btn bg_blue text-white def_14_size">
                            Renew Now
                        </button>
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered def_14_size table-hover table_custom">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 40px">
                                    <input type="checkbox" class="form-check-input" @change="toggleSelectAll($event)" :checked="allSelectedOnCurrentPage" :indeterminate="isIndeterminate" />
                                </th>
                                <th class="ps-3" scope="col">#</th>
                                <th scope="col">Coverage</th>
                                <th scope="col">Device</th>
                                <th scope="col">Serial Number/Asset Tag</th>
                                <th scope="col">Plan</th>
                                <th id="second" scope="col">Device Type</th>
                                <th scope="col">Coverage Start Date</th>
                                <th scope="col">Coverage Expiration Date</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="deviceData.length > 0" v-for="(data, index) in deviceData" :key="data.id"
                                :class="{ 'table-row-selected': selectedDevices.includes(data.id), }">
                                <td>
                                    <input type="checkbox" class="form-check-input"
                                        @change="(e) => { if (!toggleDeviceSelection(data.id, e.target.checked)) { e.target.checked = false; } }"
                                        :checked="selectedDevices.includes(data.id)" />
                                </td>
                                <td class="ps-3"> {{ index + 1 }} </td>
                                <td>
                                    <span class="badge badge_covered text-white green_color px-3 rounded-pill">Covered</span>
                                </td>
                                <td>
                                    {{ data.device_model_name ?? "" }}
                                </td>
                                <td>
                                    {{ data.serial_number ?? "" }}
                                </td>
                                <td>
                                    <select v-model="deviceCoveragePlans[data.id]" class="form-control">
                                        <!-- @focus="handleSelectModel(data.device_model_id)"> -->
                                        <option value="">Select Plan</option>
                                        <option v-for="plan in ModelPlanData[data.device_model_id] || []" :key="plan.id"
                                            :value="plan.id">{{ plan.plan_name }} </option>
                                    </select>
                                </td>
                                <td>Personal</td>
                                <td class="text-nowrap">
                                    {{ formatDate(data.payment_added_date) ?? "" }}
                                </td>
                                <td class="text-nowrap">
                                    {{ formatDate(data.expiration_date) ?? "" }}
                                </td>
                                <td>
                                    {{ deviceRenewalAllowed[data.device_model_id] ? 'Renewal Allowed' : 'Renewal Not Allowed' }}
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="10">Device Not Found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Cancel Button -->
                <div>
                    <a :href="$userAppUrl + 'sdcsmuser/device-list'" class="btn bg_blue text-white def_14_size">Cancel</a>
                </div>
            </div>
        </div>
        <!-- Renewal Device Payment Form  -->
        <div class="spets_outwrap_inner renewcard z-3" v-if="paymentForm">

               <div class="card onewhitebg renew_card_width mt-5 ms-auto me-auto">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="themetextcolor">Renew</h5>
                    <button class="btn wmax p-0 renowclose" @click="renewClose">
                        <svg id="fi_9759080" enable-background="new 0 0 32 32" height="18" viewBox="0 0 32 32" width="18" xmlns="http://www.w3.org/2000/svg"><g><path d="m16 2c-18.5.6-18.5 27.4 0 28 18.5-.6 18.5-27.4 0-28z" fill="#f44336"></path><path d="m17.4 16 4.2-4.2c.9-.9-.5-2.3-1.4-1.4l-4.2 4.2-4.2-4.2c-.9-.9-2.3.5-1.4 1.4l4.2 4.2-4.2 4.2c-.9.9.5 2.3 1.4 1.4l4.2-4.2 4.2 4.2c.9.9 2.3-.5 1.4-1.4z" fill="#eee"></path></g></svg>
                    </button>
                </div>
                    <div class="card-body">


                    <!-- Total Amount -->
                    <div class="form-gropup">
                        <label for="totalAmount" class="block text-sm font-medium  mb-1">
                            Total
                        </label>
                        <input type="text" v-model="totalAmount" id="totalAmount" class="form-control" readonly />
                    </div>

                <!-- Credit Card Details-->

                <h3 class="list-header  def_20_size mt-3"> Card Details </h3>
                    <!-- Card Number -->

                        <div class="form-gropup mb-3">
                        <label for="creditCardNumber" class="block text-sm font-medium  mb-1"> Credit Card <span class="text-red-500">*</span></label>
                        <div id="card-element" class="form-control"></div>
                    </div>
                    <div class="form-gropup">
                        <label for="cardHolderName" class="block text-sm font-medium  mb-1">Cardholder Name <span class="text-red-500">*</span></label>
                        <input type="text" placeholder="Cardholder Name" v-model="cardHolderName"
                            :class="['form-control', { 'is-invalid': validationErrors.cardHolderName },]">
                    </div>
                        <small v-if="validationErrors.cardHolderName">
                            <ErrorMessage :msg="validationErrors.cardHolderName[0]"></ErrorMessage>
                        </small>

                </div>
                <div class="card-footer">
                    <button class="btn bg_blue text-white" @click="submitForm">Submit</button>
                </div>

               </div>
        </div>
        <!-- Renewal Device Payment Form End  -->
      <!--   <div class="container d-flex justify-content-between align-items-center gap-2" v-else>
                <div class="message">
                    <p>No renewal coverage plan was found. Please get in touch with adminstartor.</p>
                </div>
        </div> -->
        <!-- **** Device Details Listing End ****-->
        <!-- <div v-if="selectedDevices.length > 0" class="fixed bottom_del_box bg-light p-1 shadow-sm">
            <div class="container d-flex justify-content-between align-items-center gap-2">
                <div>
                    <strong>{{ selectedDevices.length }}</strong> device(s)
                    selected
                </div>
                <div>
                    <button class="btn btn-outline-danger me-2" @click="deselectAll">
                        <i class="fas fa-times me-1"></i> Clear
                    </button>
                    <button class="btn btn-primary" @click="renewNow">
                        <i class="fas fa-tasks me-1"></i> Renew Now
                    </button>
                </div>
            </div>
        </div> -->
    </div>
</template>
<script>
export default {
    props: {
        devicedata: {
            type: Object,
        },
        stripekey: {
            type: String
        }

    },

    data() {
        return {
            /** Using the Icons from the JS File */
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,

            /** To store the data */
            deviceData: this.devicedata,
            isShowViewAnimLoader: false,
            validationErrors: {},

            /** Stripe Details */
            stripe: null,
            elements: null,
            card: null,
            paymentMethodID: null,
            StripeToken: null,
            cardHolderName: '',

            selectedDevices: [],
            allSelected: false,
            isIndeterminate: false,
            maxSelectionLimit: 50,
            allSelectedOnCurrentPage: null,

            ModelPlanData: {},
            deviceCoveragePlans: {},

            paymentForm: false,
            totalAmount: 0,
            isRenewalAllowed: false,

            deviceRenewalAllowed: {}, // key: device_id, value: true/false
        };
    },
    computed: {
        currentPageDeviceIds() {
            return this.deviceData.map((device) => device.id);
        },
    },
    mounted() {
        this.deviceData.forEach(device => {
            /* Assume `device.plan_id` contains the pre-selected plan ID for each device */
            this.deviceCoveragePlans[device.id] = device.plan_id || '';
            this.handleSelectModel(device.device_model_id);
        });
    },
    watch: {
        deviceCoveragePlans: {
            handler(newPlans) {
                this.calculateTotalAmount();
            },
            deep: true
        }
    },
    methods: {
        /** Stripe Card Creation */
        initializeCardElement() {
            this.$nextTick(() => {
                if (!this.isHodOrDirector) {

                    if (!this.stripe) {
                        this.stripe = Stripe(this.stripekey);
                        this.elements = this.stripe.elements();
                    }

                    /* Check if card element already exists */
                    /* If card exists, just remount it if needed */
                    if (this.card) {
                        /* Instead of unmounting and creating new, just remount */
                        this.card.mount("#card-element");
                    } else {
                        /* Create new card only if it doesn't exist */
                        this.card = this.elements.create("card", {
                            hidePostalCode: true,
                        });
                        this.card.mount("#card-element");
                    }
                }

            });
        },

        /* Helper method to check if device is selected */
        isDeviceSelected(deviceId) {
            return this.selectedDevices.includes(deviceId);
        },
        toggleSelectAll(event) {
            const currentPageIds = this.currentPageDeviceIds;

            if (event.target.checked) {
                /* Add devices silently without showing errors */
                currentPageIds.forEach(id => {
                    this.toggleDeviceSelection(id, true, false); /* false prevents error message */
                });
            } else {
                /* Remove all current page devices */
                this.selectedDevices = this.selectedDevices.filter(id => !currentPageIds.includes(id));
            }
            this.updateSelectionStates();
        },
        toggleDeviceSelection(deviceId, isSelected, showError = true) {
            if (isSelected) {
                if (!this.selectedDevices.includes(deviceId)) {
                    if (this.selectedDevices.length >= this.maxSelectionLimit) {
                        if (showError) {  /* Only show error for individual selections */
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = `You can only select up to ${this.maxSelectionLimit} devices at a time.`;
                        }
                        return false; /* Prevent selection */
                    }
                    this.selectedDevices = [...this.selectedDevices, deviceId];
                }
            } else {
                this.selectedDevices = this.selectedDevices.filter(id => id != deviceId);
            }
            this.updateSelectionStates();
            return true;
        },

        /* Update the select-all checkbox state */
        updateSelectionStates() {
            const currentPageIds = this.currentPageDeviceIds;
            const selectedCount = currentPageIds.filter((id) => this.selectedDevices.includes(id)).length;
            this.allSelectedOnCurrentPage = selectedCount == currentPageIds.length;
            this.isIndeterminate = selectedCount > 0 && selectedCount < currentPageIds.length;
        },

        deselectAll() {
            this.selectedDevices = [];
            this.updateSelectionStates();
        },
        /** Formate Date with Time */
        formatDate(dateString) {
            if (!dateString) return "";
            const date = new Date(dateString);
            return date.toLocaleDateString("en-US", {
                month: "long",
                day: "numeric",
                year: "numeric",
                hour: "numeric",
                minute: "numeric",
                hour12: true,
            });
        },

        /**
         * Plan Data
         */
        async handleSelectModel(modelId) {
            if (!modelId || this.ModelPlanData[modelId]) return; // Avoid redundant fetches
            show_ajax_loader();
            try {
                const payload = {
                    modelId: modelId
                };
                const response = await axios.post(`${this.$userAppUrl}sdcsmuser/early-renewal-devices/plan`, payload);
                if (response.data.success == true && response.data.deviceModelPlans) {
                    this.ModelPlanData[modelId] = response.data.deviceModelPlans;
                    /* Store renewal flag per device ID */
                    this.deviceRenewalAllowed[modelId] = this.ModelPlanData[modelId].length > 0;
                } else {
                    this.deviceRenewalAllowed[modelId] = false;
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = error?.response?.data?.error || "Something went wrong. Please try again.";
            } finally {
                hide_ajax_loader();
            }
        },

        /** Calculate amount */
        calculateTotalAmount() {
            let total = 0;
            for (const [deviceId, planId] of Object.entries(this.deviceCoveragePlans)) {
                const modelId = this.deviceData.find(d => d.id == deviceId)?.device_model_id;
                const plan = this.ModelPlanData[modelId]?.find(p => p.id == planId);
                if (plan) {
                    total += parseFloat(plan.price || 0); // assuming price is a field
                }
            }
            this.totalAmount = '$' + total.toFixed(2);
        },


        /** Click on renew now button */
        renewNow() {
            if (this.selectedDevices.length > 0) {
                this.paymentForm = true;
                this.initializeCardElement();
            }
        },

        /** To close the payament pop-up */
        renewClose() {
            this.paymentForm = false;
            this.cardHolderName = '';
        },
        /** Submit form */
        async submitForm() {
            show_ajax_loader();
            this.validationErrors = {};
            let validationPassed = true;
            if (!this.cardHolderName) {
                this.validationErrors.cardHolderName = ['Please enter cardholder name.'];
                validationPassed = false;
            }
            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }
            try {
                /** Step 1: Create a Payment Method using Stripe **/
                const { paymentMethod, error } = await this.stripe.createPaymentMethod({
                    type: "card",
                    card: this.card,
                    billing_details: {
                        name: this.cardHolderName,
                        email: this.email,
                    },
                });
                if (error) {
                    hide_ajax_loader();
                    this.validationErrors.payment = ['Payment processing failed. Please check your card details.'];
                    return;
                }
                /** Step 2: Payment amount */
                const { stripeToken, paymentError } = await this.stripe.createToken(this.card);
                if (paymentError) {
                    hide_ajax_loader();
                    this.validationErrors.payment = ['Payment processing failed. Please check your card details.'];
                    return;
                }
                /** Step 3: Build Devices Payload **/
                const selectedDeviceDetails = this.selectedDevices.map(deviceId => {
                    const device = this.deviceData.find(d => d.id == deviceId);
                    return {
                        deviceId: device.id,
                        modelId: device.device_model_id,
                        selectedPlan: this.deviceCoveragePlans[deviceId] || null,
                    };
                }).filter(d => d.selectedPlan);

                /** Final Payload **/
                const payload = {
                    devices: selectedDeviceDetails,
                    stripeToken: stripeToken,
                    paymentMethodId: paymentMethod.id,
                    cardHolderName: this.cardHolderName,
                };
                let response = await axios.post(`${this.$userAppUrl}sdcsmuser/early-renewal-devices/store`, payload);
                if (response.data.success == true) {
                    this.renewClose();
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    setTimeout(() => (location.href = `${this.$userAppUrl}sdcsmuser/device-list`),2000);
                } else if (response.data.errors) {
                    if (response.data.errors.devices) {
                        this.renewClose();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Please select the device plan's also";
                    }
                    this.disabledButton = false;
                    this.validationErrors = response.data.errors;
                    hide_ajax_loader();
                } else {
                    this.renewClose();
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error){
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}`),
                        5000
                    );
                } else {
                    this.renewClose();
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },
    },
};
</script>
