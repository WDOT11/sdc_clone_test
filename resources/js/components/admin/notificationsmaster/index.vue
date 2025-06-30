<template>
    <div class="container-fluid  mt_12">
        <!-- **** Notifications Listing ****-->
        <div class="card border-0 rounded onewhitebg">
            <div class="card-body rounded   ">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 mb-3 border-bottom pb-2">
                    <h4 class="mb-0 coman_main_heading"> Notifications</h4>


                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover table_custom">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" scope="col"> # </th>
                                <th scope="col"> Message </th>
                                <th scope="col"> Date & Time </th>
                                <th scope="col"> Action </th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-if="notificationData.length > 0" v-for="(notification, index) in notificationData" :key="notification.id">
                                <td class="ps-3">
                                     {{ (paginationData.current_page < 1 ?
                                        paginationData.current_page : paginationData.current_page - 1) *
                                            paginationData.per_page + index + 1}}
                                </td>
                                <td>{{ notification.message }}</td>
                                <td class="text-nowrap">{{ formatDate(notification.created_at) }}</td>
                                <td><a class="globalviewbtn   text-decoration-none  d-flex align-items-center themetextcolor gap-1 def_14_size fw-semibold" :href="`${$userAppUrl}${notification.admin_target_link}?openPopup=true${notification.user_id && notification.notification_for == 'user_added' ? `&userId=${notification.user_id}` : ''}${notification.device_id ? `&deviceId=${notification.device_id}` : ''}${notification.device_claim_id ? `&claimId=${notification.device_claim_id}` : ''}${notification.device_repair_id ? `&repairId=${notification.device_repair_id}` : ''}${notification.shipping_supply_id ? `&supplyId=${notification.shipping_supply_id}` : ''}`"><img :src="viewIc" width="35" height="35"> <span class="viewtext_Gl">View</span></a></td>
                            </tr>
                            <tr v-else>
                                <td  colspan="4">Notifications Not Found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getnotificationData"></pagination>
            </div>

        </div>
        <!-- **** Notifications Listing End ****-->
    </div>
</template>
<script>
export default {
    props: {
        notificationdata: {
            type: Object,
        },
        pagination: {
            type: Object,
        },

    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,

            notificationData: this.notificationdata,
            paginationData: this.pagination,
        };
    },
    methods: {

        /** To get the notifications list data by using paging */
        async getnotificationData(page = 1) {
            try {
                show_ajax_loader();
                let url = `${this.$userAppUrl}smarttiusadmin/notifications?page=${page}`;
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.notifications && response.data.pagination) {
                        this.notificationData = response.data.notifications;
                        this.paginationData = response.data.pagination;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong";
                    }
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = error.response.data.error;
            }
        },

        formatDate(dateString){
            if (!dateString) return '';
            const rawString = String(dateString);
            const cleanedDateString = rawString
                .replace(' ', 'T')                /** Ensure ISO T separator  */
                .replace(/\.\d+Z$/, '')           /** Strip microseconds + Z */
                .replace(/Z$/, '');               /** Remove Z if not matched above */

            return formatDateTimeAndTimeZone(cleanedDateString);
        }
    },
};
</script>
