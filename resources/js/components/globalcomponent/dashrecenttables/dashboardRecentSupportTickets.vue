<template>
    <div class="card-body px-0 pt-0  bottom_20border onewhitebg">
        <div class="table-responsive">
            <table class="table def_14_size table-hover table_custom2">
                <thead >
                    <tr>
                        <th class="ps-3" scope="col">#</th>
                        <th scope="col">Ticket ID</th>
                        <th scope="col">User</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Date</th>
                        <th v-if="isAdmin" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="recentSupportTicket.length > 0" v-for="(ticket, index) in recentSupportTicket" :key="ticket.id" >
                        <td class="ps-3 text-nowrap" scope="row">
                            {{ index + 1 }}.
                        </td>
                        <td><span class="category-badge">#{{ ticket.id }}</span></td>
                        <td><span>{{ (ticket.first_name ?? '') + ' ' + (ticket.last_name ?? '') ? (ticket.first_name ?? '') + ' ' + (ticket.last_name ?? '') : '' }}</span></td>
                        <td><span>{{ ticket.subject }}</span></td>
                        <td><span>{{ formatDate(ticket.created_at) }}</span></td>
                        <td v-if="isAdmin">
                            <div class="d-flex align-items-center gap-20">
                                <button type="button" class="rounded-pill globalviewbtn extrabtns d-flex align-items-center def_14_size gap-1 themetextcolor" @click="goToSupportTicket(ticket.id)"><img :src="viewIc" width="22" height="22">View</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="9" class="text-center">No support ticket found.</td>
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
            isAdmin: this.isadmin,
            recentSupportTicket:[],
            viewIc: this.$viewIcIcon,
        }
    },
    created() {
        this.getSupportTickets();
    },
    methods: {

        /** To get the recent support tickets */
        async getSupportTickets() {
            let url = '';
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/dash-recent-support-tickets`;
            } else {
                url = `${this.$userAppUrl}sdcsmuser/dash-recent-support-tickets`;
            }
            try {
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.ticketData) {
                        this.recentSupportTicket = response.data.ticketData;
                    } else {
                        /* Loader */
                    }
                }
            }catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
            }
        },

        /** Formate Date */
        formatDate(dateString) {
            if (!dateString) return '';
            const rawString = String(dateString);
            const cleanedDateString = rawString
                .replace(' ', 'T')
                .replace(/\.\d+Z$/, '')
                .replace(/Z$/, '');

            return formatDateTimeAndTimeZone(cleanedDateString);
        },

        /** go to track claim  */
         goToSupportTicket(ticketId) {
            let url = '';
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/support-ticket?openPopup=true${ticketId ? `&ticketId=${ticketId}` : ''}`;
            }
            window.location.href = url;
        },
    }
};
</script>