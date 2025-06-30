<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0  ">
            <div class="card-body">
                <h4 class="mb-3 border-bottom pb-2  coman_main_heading">Support Tickets</h4>

                <!-- Table to show the ticket data -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size">
                        <thead class="table-light">
                            <tr>
                                <th width="60" >#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="supportTickets.length > 0" v-for="(ticket, index) in supportTickets" :key="ticket.id">
                                <td > {{ (paginationData.current_page - 1) * paginationData.per_page + index + 1 }} </td>
                                <!-- <td> {{ ticket.first_name ?? '' + ' ' + ticket.last_name ?? ''}} </td> -->
                                <td  class="text-nowrap"> {{ (ticket.first_name ?? '') + ' ' + (ticket.last_name ?? '') ? (ticket.first_name ?? '') + ' ' + (ticket.last_name ?? '') : '' }} </td>
                                <td> {{ ticket.email }} </td>
                                <td  class="text-nowrap"> {{ ticket.subject }} </td>
                                <td class="text-nowrap"> {{ formatDate(ticket.created_at) }} </td>
                                <td>
                                    <div class="d-flex align-items-center gap-10">
                                        <button type="button" @click="viewTicketDetails(ticket.id)" class="globalviewbtn extrabtns  rounded-pill d-flex align-items-center themetextcolor def_14_size gap-1 open_notifSlid action_toSlid"><img :src="viewIc" width="35" height="35"><span class="viewtext_Gl">View</span></button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="6"> No Tickets found! </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getSupportTickets">
                </pagination>

                <!-- To show the details of the ticket -->
                <div :class="ticketId ? 'dataSlide_bar_wrap px-0 onopen_slide' : 'dataSlide_bar_wrap px-0'">
                    <div class="dataSlide_bar_inner  ">
                        <div class="card rounded-0 border-0 dataSlide_bar_div ">
                            <div class="card-header dataSlideh">
                                <div class="media d-flex justify-content-between align-items-center gap-10">
                                    <h5 class="mb-0 def_18_size themetextcolor">Ticket Details</h5>
                                    <div class="close_btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 487.619 487.619" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M243.878 0a243.81 243.81 0 1 0 243.741 243.739A244.016 244.016 0 0 0 243.878 0zm110.697 322.889a22.068 22.068 0 0 1 0 31.27c-4.203 4.12-9.852 6.427-15.738 6.427s-11.537-2.306-15.74-6.427l-79.219-79.148-79.147 79.148a22.21 22.21 0 0 1-31.272 0 22.054 22.054 0 0 1-6.494-15.635 22.07 22.07 0 0 1 6.494-15.635l79.148-79.357-79.148-79.079a22.137 22.137 0 0 1 .752-30.52 22.137 22.137 0 0 1 30.52-.752l79.147 79.079 79.08-79.079a22.348 22.348 0 0 1 31.48 0 22.072 22.072 0 0 1 0 31.271l-79.08 79.079z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body px-0  onewhitebg dataSlide_bar_list  loaded_list">
                                <div class="block_wraps">
                                    <!-- User Data -->
                                    <transition name="fade">
                                        <div v-if="!isShowViewAnimLoader && viewTicketData" class="card-body py-0">
                                            <h3 class="list-header  pb-1 def_16_size"> Ticket Info </h3>
                                            <table class="table table-bordered def_14_size">
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Name</td>
                                                        <td><span class="result_detail_font">{{ (viewTicketData.first_name ?? '') + ' ' + (viewTicketData.last_name ?? '') ? (viewTicketData.first_name ?? '') + ' ' + (viewTicketData.last_name ?? '') : '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Email</td>
                                                        <td><span class="result_detail_font">{{ viewTicketData.email ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Phone</td>
                                                        <td><span class="result_detail_font">{{ formatPhoneNumber(viewTicketData.phone) ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Date</td>
                                                        <td><span class="result_detail_font">{{ (formatDate(viewTicketData.created_at) ?? '') }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Subject</td>
                                                        <td><span class="result_detail_font">{{ (viewTicketData.subject ?? '') }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Message</td>
                                                        <td><span class="result_detail_font">{{ (viewTicketData.message ?? '') }}</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </transition>
                                    <transition name="fade">
                                        <div v-if="isShowViewAnimLoader" class="loader-wrap">
                                            <sidebar-animation-loader :isshow="true" />
                                        </div>
                                    </transition>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {

        props: {
            ticketdata:{
                type:Object
            },
            pagination:{
                type: Object
            },
        },

        data() {
            return {
                /** View icon */
                viewIc: this.$viewIcIcon,

                /** To store the data from props and api response */
                supportTickets: this.ticketdata.data,
                paginationData: this.pagination,

                /** To store the view data */
                ticketId: null,
                phone: '',
                viewTicketData: {},
                isShowViewAnimLoader: false
            };
        },

        mounted() {
            const urlParams = new URLSearchParams(window.location.search);
            this.openPopup = urlParams.get('openPopup');
            this.ticketId = urlParams.get('ticketId');

            if (this.openPopup == 'true' && this.ticketId) {
                    this.viewTicketDetails(this.ticketId);
            }else {
                    this.openPopup = false;
                    this.ticketId = null;
            }
        },

        methods: {

            /** To get the ticket data on page change */
            async getSupportTickets(page = 1) {
                try{
                    show_ajax_loader();
                    const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/support-ticket?page=${page}`);
                    if (response && response.data) {
                        if (response.data.success == true) {
                            if ( response.data.ticketData && response.data.ticketData.data && response.data.pagination ) {
                                this.supportTickets = response.data.ticketData.data;
                                this.paginationData = response.data.pagination;
                                hide_ajax_loader();
                            } else {
                                hide_ajax_loader();
                                this.$alertMessage.success = false;
                                this.$alertMessage.message = "Something went wrong, Please try again later.";
                            }
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = "Something went wrong, Please try again later.";
                        }
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } catch(error) {
                    if (error && error.response && error.response.data && error.response.data.error) {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                        setTimeout(
                            () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                            4000
                        );
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                }
            },

            /** To format the phone number */
            formatPhoneNumber(phone = this.phone) {
                if (phone !== '' && phone !== null) {
                    let cleaned = phone.replace(/[^\d\(\)\-\s]/g, '');
                    let digits = cleaned.replace(/\D/g, '');

                    if (digits.length > 10) {
                        digits = digits.slice(0, 10);
                    }

                    if (digits.length > 6) {
                        return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
                    } else if (digits.length > 3) {
                        return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                    } else if (digits.length > 0) {
                        return `(${digits}`;
                    } else {
                        return '';
                    }
                }
                return '';
            },

            /** To format the date */
            formatDate(dateString) {
                if (!dateString) return '';
                const rawString = String(dateString);
                const cleanedDateString = rawString
                    .replace(' ', 'T')                /** Ensure ISO T separator  */
                    .replace(/\.\d+Z$/, '')           /** Strip microseconds + Z */
                    .replace(/Z$/, '');               /** Remove Z if not matched above */

                return formatDateTimeAndTimeZone(cleanedDateString);
            },

            /** To get the ticket data */
            async viewTicketDetails(ticketId) {
                this.isShowViewAnimLoader = true;
                this.viewTicketData = {}; /* reset view */
                this.ticketId = ticketId;
                show_overlay();

                try {
                    const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/support-ticket/view${ticketId ? `?ticketId=${ticketId}` : ""}`);
                    if (response.data.success == true) {
                        this.viewTicketData = response.data.viewData;
                    }
                } catch (error) {
                    if (error && error.response && error.response.data && error.response.data.error) {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, please try again.";
                        setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    }
                } finally {
                    setTimeout(() => {
                        this.isShowViewAnimLoader = false;
                    }, 1500); /* short delay for animation effect */
                }
            },
        },
    }
</script>