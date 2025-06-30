<template>
    <div class="card-body pt-0 px-0 overflow-hidden bottom_20border onewhitebg">
        <div class="table-responsive">
            <table class="table def_14_size table-hover table_custom2">
                <thead >
                    <tr>
                        <th class="ps-3" scope="col">#</th>
                        <th scope="col">Device</th>
                        <th scope="col">Serial Number</th>
                        <th scope="col">Type</th>
                        <th scope="col">Coverage Start Date</th>
                        <th scope="col">Coverage Expiration Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="recentDevices.length > 0" v-for="(device, index) in recentDevices" :key="device.id" >
                        <td class="ps-3 text-nowrap">
                            {{ index + 1 }}
                        </td>
                        <td class="text-nowrap">
                            {{ device.device_model_name ?? '' }}
                        </td>
                        <td class="text-nowrap">
                            {{ device.serial_number ?? '' }}
                        </td>
                        <td class="text-nowrap">
                           <span class="category-badge"> {{ device.device_family_name ?? '' }}</span>
                        </td>
                        <td class="text-nowrap">
                            {{ formatDate(device.payment_added_date) }}
                        </td>
                        <td class="text-nowrap">
                            {{ formatDate(device.expiration_date) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-20">
                                <button type="button" class="rounded-pill globalviewbtn extrabtns d-flex align-items-center def_14_size gap-1 themetextcolor open_notifSlid" @click="goToDevice(device.id)"><img :src="viewIc" width="22" height="22">View</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="7" class="text-center">No recent devices found.</td>
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
            recentDevices: [],
        }
    },
    created() {
        this.getDevices();
    },
    methods: {
        async getDevices() {
            let url = '';
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/dash-recent/devices`;
            } else {
                url = `${this.$userAppUrl}sdcsmuser/dash-recent/devices`;
            }
            try {
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.devicesData) {
                        this.recentDevices = response.data.devicesData;
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
        /* formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        }, */

        /** Formate Date with Time */
        formatDate(dateString) {
            return formatDateTimeAndTimeZone(dateString);
        },

        /* Go to device */
        goToDevice(deviceId) {
            let url = '';
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/devices?openPopup=true${deviceId ? `&deviceId=${deviceId}` : ''}`;
            } else {
                url = `${this.$userAppUrl}sdcsmuser/device-list?openPopup=true${deviceId ? `&deviceId=${deviceId}` : ''}`;
            }
            window.location.href = url;
        }
    }
};
</script>