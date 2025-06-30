<template>
    <div class="modal fade" id="coverage_check" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
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

                        <h5 class="modal-title" id="exampleModalLabel">Verify Coverage</h5>
                    </div>
                    <button type="button" class="btn-close" id="verifyCoverageModalClose" @click="resetForm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body onewhitebg">
                    <div class="form-group">
                        <label class="mb-1 themetextcolor">Name</label>
                        <input v-model="name" placeholder="Enter name..." type="text" class="form-control py-2" name="">
                        <small v-if="validationErrors.name" ><ErrorMessage :msg="validationErrors.name"></ErrorMessage></small>
                    </div>
                    <div class="d-flex justify-content-center mt-3 mb-3 position-relative">
                        <h2 class="line-divider "><span class="span-line-divider ">OR</span></h2>
                    </div>
                    <div class="form-group">
                        <label class="mb-1 themetextcolor">Serial Number/Asset Tag</label>
                        <input v-model="serialNumber" placeholder="Enter serial number or tag" type="text"
                            class="form-control py-2" name="">
                        <small v-if="validationErrors.serialNumber" ><ErrorMessage :msg="validationErrors.serialNumber"></ErrorMessage></small>
                    </div>
                </div>
                <div class="modal-footer onewhitebg justify-content-start">
                    <button type="button" @click="getDeviceData" class="btn btn-primary">Check</button>
                    <button type="button" @click="resetForm" class="btn customm_reset_btn ">Reset</button>
                </div>
                <!-- Coverage Listing -->
                <div v-if="isDeviceData" class="row mt-2">
                    <div class="col-md-12">
                        <div class="card-body py-2 px-0 bg-white mt-3">
                            <div class="table-responsive popup_scroll_table px-3">
                            <table class="table table-hover table_custom">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Coverage</th>
                                        <th scope="col">Name</th>
                                        <th scope="col"> Serial Number </th>
                                        <th scope="col">Start Date</th>
                                        <th scope="col">Expiration Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="deviceData.length > 0" v-for="(data, index) in deviceData" :key="data.id">
                                       <td v-if="new Date() <= new Date(data.expiration_date)"
                                                class="">
                                               <span class="badge badge_covered text-white green_color px-3 py-2 rounded-pill">{{ 'Covered' }}</span>
                                        </td>
                                        <td v-else class="">
                                            <span class="badge  badge_uncovered orange_color px-3 py-2 rounded-pill"> {{ 'Uncovered' }}</span>
                                        </td>
                                        <td>
                                            {{ data.device_model_name }}
                                        </td>
                                        <td>
                                            {{ data.serial_number ?? '' }}
                                        </td>
                                        <td class="text-nowrap">
                                            {{ formatDate(data.payment_added_date) ?? '' }}
                                        </td>
                                        <td class="text-nowrap">
                                            {{ formatDate(data.expiration_date) ?? '' }}
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td class="border border-gray-300 px-4 py-2" colspan="5">Device Not Found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Coverage Listing End -->
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            deviceData: {},
            name: '',
            serialNumber: '',
            validationErrors: {},
            isDeviceData: false,
        };
    },
    methods: {
        /** Formate Date */
        /*  formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        }, */

        /** Formate Date with Time */
        formatDate(dateString) {
            return formatDateAndTimeZone(dateString);
        },

        /* on click check */
        async getDeviceData() {
            try {
                show_ajax_loader();
                this.validationErrors = {};
                let validationPassed = true;
                const serialNumber = this.serialNumber;
                const name = this.name;
                // let response = null;
                if (serialNumber || name) {
                    if (serialNumber && name) {
                        this.validationErrors.serialNumber = "Please enter either serial number or name.";
                        validationPassed = false;
                        hide_ajax_loader();
                        return;
                    }
                    if (serialNumber) {
                        if (serialNumber.length < 3) {
                            this.validationErrors.serialNumber = "Serial number should be at least 3 characters.";
                            validationPassed = false;
                            hide_ajax_loader();
                            return;
                        }
                    }
                    if (name) {
                        if (name.length < 3) {
                            this.validationErrors.name = "Name should be at least 3 characters.";
                            validationPassed = false;
                            hide_ajax_loader();
                            return;
                        }
                    }

                } else {
                    this.validationErrors.serialNumber = "Please enter serial number or name.";
                    validationPassed = false;
                }
                /* Stop submission if validation fails */
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
                let response = await axios.post(`${this.$userAppUrl}sdcsmuser/device-list/coverage`,{
                    name: this.name,
                    serialNumber: this.serialNumber,
                });
                if (response.data.success == true) {
                    this.deviceData = response.data.deviceData;
                    this.isDeviceData = true;
                    hide_ajax_loader();
                }
                else if (response.data.errors) {
                    if (response.data.errors.name) {
                        this.validationErrors.name = response.data.errors.name[0];
                    }
                    if (response.data.errors.serialNumber) {
                        this.validationErrors.serialNumber = response.data.errors.serialNumber[0];
                    }
                    hide_ajax_loader();
                }
                else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () =>
                            (location.href = `${this.$homeUrl}`),
                        5000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /* Reset Button */
        resetForm() {
            this.name = '';
            this.serialNumber = '';
            this.validationErrors = {};
            this.deviceData = {};
            this.isDeviceData = false;
        },

    },
}
</script>
