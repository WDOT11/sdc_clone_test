<template>
    <div class="card-body pt-0 px-0 overflow-hidden bottom_20border onewhitebg">
        <div class="table-responsive">
            <table class="table def_14_size table-hover table_custom2">

                <thead >
                    <tr>
                        <th class="ps-3" scope="col">#</th>
                        <th scope="col">Device</th>
                        <th scope="col">Serial Number</th>
                        <th scope="col" v-if="isAdmin">Zoho Current Status</th>
                        <th scope="col">Current Status</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="recentClaimDevices.length > 0" v-for="(claimDevice, index) in recentClaimDevices" :key="claimDevice.id" >
                        <td class="ps-3 text-nowrap" scope="row">
                            {{ index + 1 }}
                        </td>
                        <td class="text-nowrap">
                            {{ claimDevice.device_model_name }}
                        </td>
                        <td class="text-nowrap">
                           {{ claimDevice.serial_number }}
                        </td>
                        <td class="text-nowrap" v-if="isAdmin">
                            {{ getClaimStatus(claimDevice.claim_status) }}
                        </td>
                        <td class="text-nowrap">
                            {{ getUserClaimStatus(claimDevice.user_claim_status) }}
                        </td>
                        <td class="text-nowrap">
                            {{ formatDate(claimDevice.status_updated_date) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-20">
                                <button type="button" class="rounded-pill globalviewbtn extrabtns d-flex align-items-center def_14_size gap-1 themetextcolor" @click="goToTrackClaim(claimDevice.id)"><img :src="viewIc" width="22" height="22">View</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="6" class="text-center">No claims found.</td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
</template>
<script>
export default {
    props: ['isadmin'],
    data() {
        return {
            viewIc: this.$viewIcIcon,
            isAdmin: this.isadmin,
            recentClaimDevices: [],
            /** Managing the claim status from custom.js */
            // claimStatus: this.$claimRepairStatus,
            claimStatus: [],
            userClaimStatus: this.$userClaimRepairStatus,
        }
    },
    created() {
        this.getClaimDevices();
        this.getClaimRepairStatus();
    },
    methods: {

        async getClaimDevices() {
            let url = '';
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/dash-recent/status-change-claims`;
            } else {
                url = `${this.$userAppUrl}sdcsmuser/dash-recent/status-change-claims`;
            }
            try {
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.claimDevicesData) {
                        this.recentClaimDevices = response.data.claimDevicesData;
                    } else {
                        /* Loader */
                    }
                }
            } catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
            }
        },

        /** Formate Date */
        formatDate(dateString) {
            return formatDateTimeAndTimeZone(dateString);
        },

        /** To match the status from the array of status */
        getClaimStatus(claimStatusId) {
           /* Find the status object in the claimStatus array */
           const claimStatus = this.claimStatus.find(status => status.id == claimStatusId)?.name || 'Unknown Status';

           /* Return the status if found, otherwise return an empty string */
           return claimStatus ? claimStatus : '';
        },

        /** To match the status from the array of status(user) */
        getUserClaimStatus(claimStatusId) {
           /* Find the status object in the claimStatus array */
           const claimStatus = this.userClaimStatus.find(status => status.id == claimStatusId);
           /* Return the status if found, otherwise return an empty string */
           return claimStatus ? claimStatus.status : '';
        },

        /** go to track claim  */
        goToTrackClaim(claimId) {
            let url = '';
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/track-claims?openPopup=true${claimId ? `&claimId=${claimId}` : ''}`;
            } else {
                url = `${this.$userAppUrl}sdcsmuser/user-track-claims?openPopup=true${claimId ? `&claimId=${claimId}` : ''}`;
            }
            window.location.href = url;
        },

        /** To get the claim repair status */
        async getClaimRepairStatus(){
            let url = '';
            url = `${this.$userAppUrl}smarttiusadmin/get-claim-repair-status`;
           try {
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.claimRepairStatus) {
                        this.claimStatus = response.data.claimRepairStatus;
                    } else {
                        /* Loader */
                    }
                }
            }catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
            }
        }
    }
};
</script>