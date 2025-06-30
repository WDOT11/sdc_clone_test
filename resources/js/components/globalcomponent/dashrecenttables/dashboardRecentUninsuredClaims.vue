<template>
    <div class="card-body pt-0 px-0 overflow-hidden bottom_20border onewhitebg">
        <div class="table-responsive">
            <table class="table def_14_size table-hover table_custom2">
                <thead >
                    <tr>
                        <th class="ps-3" scope="col">#</th>
                        <th scope="col">User</th>
                        <th scope="col">Device</th>
                        <th scope="col">Serial Number</th>
                        <th scope="col">Type</th>
                        <th v-if="isAdmin" scope="col">Zoho Status</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="recentClaimDevices.length > 0" v-for="(repairDevice, index) in recentClaimDevices" :key="repairDevice.id" >
                        <td class="ps-3 text-nowrap" scope="row">
                            {{ index + 1 }}.
                        </td>
                        <td class="text-nowrap">
                            {{ repairDevice.first_name ?? '' }} {{ repairDevice.last_name ?? '' }}
                        </td>
                        <td class="text-nowrap">
                            {{ repairDevice.device_model_name ?? '-' }}
                        </td>
                        <td class="text-nowrap">
                           {{ repairDevice.device_serial_number ?? '-' }}
                        </td>
                        <td >
                            <span class="category-badge"> {{ repairDevice.device_family_name ?? '-' }} </span>
                        </td>
                        <td class="text-nowrap" v-if="isAdmin">
                            {{ getRepairStatus(repairDevice.repair_status) }}
                        </td>
                        <td class="text-nowrap">
                            {{ getUserRepairStatus(repairDevice.user_repair_status) }}
                        </td>
                        <td class="text-nowrap">
                            {{ formatDate(repairDevice.created_at) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-20">
                                <button type="button" class="rounded-pill globalviewbtn extrabtns d-flex align-items-center def_14_size gap-1 themetextcolor" @click="goToTrackRepair(repairDevice.id)"><img :src="viewIc" width="22" height="22">View</button>
                            </div>
                        </td>
                        <!-- track-repairs -->
                    </tr>
                    <tr v-else>
                        <td colspan="8" class="text-center">No repair requests found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
export default {
    props: ["isadmin"],
    data() {
        return {
            viewIc: this.$viewIcIcon,
            isAdmin: this.isadmin,
            recentClaimDevices: [],
             /** Managing the repair status from custom.js */
            repairStatus: [],
            // repairStatus: this.$claimRepairStatus,
            userRepairStatus: this.$userClaimRepairStatus,
        };
    },
    created() {
        this.getUninsuredClaimDevices();
        this.getClaimRepairStatus();
    },
    methods: {

        async getUninsuredClaimDevices() {
            let url = "";
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/dash-recent/uninsured-claims`;
            } else {
                url = `${this.$userAppUrl}sdcsmuser/dash-recent/uninsured-claims`;
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
                this.$alertMessage.message = "Something went wrong, please try again.";
            }
        },

        /** Formate Date */
        formatDate(dateString) {
            return formatDateTimeAndTimeZone(dateString);
        },

        /** To match the status from the array of status */
        getRepairStatus(repairStatusId) {
            /* Find the status object in the repairStatus array */
            //const repairStatus = this.repairStatus.find(status => status.id == repairStatusId);
            const repairStatusName = this.repairStatus.find(status => status.id == repairStatusId)?.name || 'Unknown Status';
            /* Return the status if found, otherwise return an empty string */
            return repairStatusName ? repairStatusName : '';
        },

        /** To match the status from the array of status(users) */
        getUserRepairStatus(repairStatusId) {
           /* Find the status object in the repairStatus array */
           const repairStatus = this.userRepairStatus.find(status => status.id == repairStatusId);
           /* Return the status if found, otherwise return an empty string */
           return repairStatus ? repairStatus.status : '';
        },

         /** To get the claim repair status */
        async getClaimRepairStatus(){
            let url = '';
            url = `${this.$userAppUrl}smarttiusadmin/get-claim-repair-status`;
           try {
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.claimRepairStatus) {
                        this.repairStatus = response.data.claimRepairStatus;
                    } else {
                        /* Loader */
                    }
                }
            }catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
            }
        },

        /** go to track repair  */
        goToTrackRepair(repairId) {
            let url = '';
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/track-repairs?openPopup=true${repairId ? `&repairId=${repairId}` : ''}`;
            } else {
                url = `${this.$userAppUrl}sdcsmuser/user-track-repairs?openPopup=true${repairId ? `&repairId=${repairId}` : ''}`;
            }
            window.location.href = url;
        }
    },
};
</script>
