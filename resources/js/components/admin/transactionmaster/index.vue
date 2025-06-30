<template>
    <!-- **** Transactions Filter Modal **** -->
    <div class="modal" id="myfilter">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header align-items-center">
                    <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                        <svg height="30" viewBox="-4 0 393 393.99003" width="30" xmlns="http://www.w3.org/2000/svg" id="fi_1159641"><path d="m368.3125 0h-351.261719c-6.195312-.0117188-11.875 3.449219-14.707031 8.960938-2.871094 5.585937-2.3671875 12.3125 1.300781 17.414062l128.6875 181.28125c.042969.0625.089844.121094.132813.183594 4.675781 6.3125 7.203125 13.957031 7.21875 21.816406v147.796875c-.027344 4.378906 1.691406 8.582031 4.777344 11.6875 3.085937 3.105469 7.28125 4.847656 11.65625 4.847656 2.226562 0 4.425781-.445312 6.480468-1.296875l72.3125-27.574218c6.480469-1.976563 10.78125-8.089844 10.78125-15.453126v-120.007812c.011719-7.855469 2.542969-15.503906 7.214844-21.816406.042969-.0625.089844-.121094.132812-.183594l128.683594-181.289062c3.667969-5.097657 4.171875-11.820313 1.300782-17.40625-2.832032-5.511719-8.511719-8.9726568-14.710938-8.960938zm-131.53125 195.992188c-7.1875 9.753906-11.074219 21.546874-11.097656 33.664062v117.578125l-66 25.164063v-142.742188c-.023438-12.117188-3.910156-23.910156-11.101563-33.664062l-124.933593-175.992188h338.070312zm0 0"></path></svg>
                        <span>Filters</span>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="transactionFilterModalClose"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body onewhitebg pt-0 ">
                    <div class="container-fluid    pt-3">
                        <div class="row  ">
                           <div class="col-12 col-md-6 col-lg-6 mt-2">
                               <label class="form-label" for="">Payment For</label>
                               <select v-model="paymentFor" class="form-control ">
                                   <option value="all" selected>All</option>
                                   <option value="1" selected>Device</option>
                                   <option value="2" selected>Claim Request</option>
                                   <option value="3" selected>Repair Request</option>
                                   <option value="4" selected>Invoice Paid</option>
                               </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mt-2">
                                <label class="form-label" for="Search">Search</label>
                                <!---- Search by User Email -->
                                <input type="search" v-model="search" placeholder="Search..." class="form-control  ">
                                <p class="def_14_size themetextcolor">Search by User Email</p>
                            </div>
                        </div>
                    </div>


                    <div class="container-fluid ">
                         <h5 class="def_18_size   list-header"> Transaction Date Range </h5>
                        <div class="row  ">
                            <div class="col-12 col-md-6 col-lg-6 mt-2">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control  " id="start_date" v-model="startDate" />
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mt-2">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" :class="['form-control  ', { 'is-invalid': validationErrors.endDate },]"
                                    id="end_date" v-model="endDate" />
                                <small v-if="validationErrors.endDate" ><ErrorMessage :msg="validationErrors.endDate"></ErrorMessage></small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer onewhitebg">
                    <button type="button" @click="resetFilter" class="btn  btn-warning float-left"> Reset Filter </button>
                    <button type="button" @click="applyFilter" class="btn btn-primary"> Filter </button>
                </div>
            </div>
        </div>
    </div>
    <!-- **** Transactions Filter Modal End **** -->
    <div class="container-fluid mt_12">

        <!-- **** Transactions Details Listing ****-->
        <div class="card onewhitebg border-0 bg-white">
            <div class="card-body   ">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 border-bottom pb-2  ">
                    <h4 class="coman_main_heading mb-0"> Transactions <small style="font-size: 13px; color: #696969;"> ({{ formattedDigits(totalTransactions) }}) </small></h4>
                    <!-- Button and search section -->
                    <div class="d-flex justify-content-between flex-wrap align-items-start gap-3">
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#myfilter"  @click="showFilterModal" class="blogal_pbtn_padding def_14_size  align-items-center btn text-decoration-none  justify-content-center rounded d-flex gap-10  border-0   wmax cursor bg_blue text-white">
                                <img :src="$imagePath + 'filter.svg'" width="16" height="16" />
                                <span class=" def_14_size">Filter</span>
                            </button>
                            <button type="button" @click="resetFilter" class="btn blogal_pbtn_padding  btn-warning text-black ml-2 def_14_size"> Reset Filter </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size table_custom">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" scope="col"> # </th>
                                <th scope="col"> Transaction Id </th>
                                <th scope="col"> Amount </th>
                                <th scope="col"> Status </th>
                                <th scope="col"> Payment method </th>
                                <th scope="col"> Description </th>
                                <th scope="col"> Customer </th>
                                <th scope="col"> Date </th>
                                <th scope="col"> View </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="transactionData.length > 0" v-for="(transaction, index) in transactionData" :key="transaction.id">
                                <td class="ps-3 text-nowrap">
                                    {{ (paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1}}
                                </td>
                                <td class="text-nowrap">{{ transaction.stripe_transaction_id }}</td>
                                <td class="text-nowrap">${{ transaction.amount ?? '0' }}</td>
                               <td class="text-nowrap">
                                    <span class="badge badge_covered text-white green_color px-3  rounded-pill" v-if="transaction.status == 'succeeded' || transaction.status == 'paid'">
                                     {{ transaction.status }}
                                    </span>
                                    <span class="badge  badge_uncovered orange_color px-3  rounded-pill" v-else>
                                     {{ transaction.status }}
                                    </span>
                                </td>
                                <td class="text-nowrap">{{ transaction.payment_method }}({{ transaction.card_number }})</td>
                                <td >{{ transaction.description }}</td>
                                <td class="text-nowrap">{{ transaction.user_email }}</td>
                                <td class="text-nowrap">{{ formatDate(transaction.created_at) }}</td>
                                <td >
                                    <a v-if="transaction.payment_for == 1" class="globalviewbtn extrabtns rounded-pill  text-decoration-none themetextcolor def_14_size d-flex align-items-center gap-1 fw-semibold justify-content-center" :href="`${$userAppUrl}smarttiusadmin/devices?openPopup=true${transaction.device_id ? `&deviceId=${transaction.device_id}` : ''}`">
                                        <img :src="viewIc" width="35" height="35">
                                        <span class="viewtext_Gl">View</span>
                                    </a>
                                    <a v-else-if="transaction.payment_for == 2" class="globalviewbtn extrabtns rounded-pill text-decoration-none themetextcolor def_14_size d-flex align-items-center gap-1 fw-semibold justify-content-center" :href="`${$userAppUrl}smarttiusadmin/track-claims?openPopup=true${transaction.device_claim_id ? `&claimId=${transaction.device_claim_id}` : ''}`">
                                       <img :src="viewIc" width="35" height="35">
                                       <span class="viewtext_Gl">View</span>
                                    </a>
                                    <a v-else-if="transaction.payment_for == 3" class="globalviewbtn extrabtns rounded-pill text-decoration-none themetextcolor def_14_size d-flex align-items-center gap-1 fw-semibold justify-content-center" :href="`${$userAppUrl}smarttiusadmin/track-repairs?openPopup=true${transaction.device_repair_id ? `&repairId=${transaction.device_repair_id}` : ''}`">
                                        <img :src="viewIc" width="35" height="35">
                                        <span class="viewtext_Gl">View</span>
                                    </a>
                                    <a v-else class="globalviewbtn extrabtns rounded-pill text-decoration-none d-flex align-items-center themetextcolor gap-1 def_14_size fw-semibold" href="#">
                                        <img :src="viewIc" width="35" height="35">
                                        <span class="viewtext_Gl">View</span>
                                    </a>
                                </td>
                            </tr>
                            <tr v-else>
                                <td  colspan="9">Transactions Not Found. </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getTransactionData"></pagination>
            </div>
        </div>
        <!-- **** Transactions Details Listing End ****-->
    </div>
</template>
<script>
export default {
    props: {
        transactiondata: {
            type: Object,
        },
        paginationdata: {
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

            transactionData: this.transactiondata.data,
            paginationData: this.paginationdata,
            totalTransactions: this.paginationdata.total,

            /** Filter */
            search: '',
            paymentFor: 'all',
            startDate: '',
            endDate: '',
            selectedUserId: null,
            validationErrors: {},
        };
    },
    mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        const filterData = urlParams.get('filter');
        const querySelectedUser = urlParams.get('userId');
        if (filterData == 'true' && querySelectedUser) {
            this.selectedUserId = querySelectedUser;
            this.getTransactionData();
        } else {
            this.selectedUserId = null;
        }
    },
    methods: {

        /** To get the transactions data by using paging */
        async getTransactionData(page = 1) {
            show_ajax_loader();
            try {
                let url = `${this.$userAppUrl}smarttiusadmin/transactions?page=${page}`;
                if (this.search) {
                    url += `&search=${this.search}`;
                }
                if (this.paymentFor) {
                    url += `&paymentFor=${this.paymentFor}`;
                }
                if (this.startDate && this.endDate) {
                    url += `&startDate=${this.startDate}&endDate=${this.endDate}`;
                }
                if (this.selectedUserId) {
                    url += `&userId=${this.selectedUserId}`;
                }
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.transactionData && response.data.pagination) {
                        this.transactionData = response.data.transactionData.data;
                        this.paginationData = response.data.pagination;
                        this.totalTransactions = response.data.pagination.total;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong";
                    }
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
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },
        /** Formate Date with Time */
        formatDate(dateString) {
            return formatDateTimeAndTimeZone(dateString);
        },
        /** Reset filter */
        resetFilter() {
            this.paymentFor = 'all';
            this.startDate = '';
            this.endDate = '';
            this.search = '';
            this.selectedUserId = null;
            this.getTransactionData(1);
            hide_admin_popup('#transactionFilterModalClose');
        },
        /** Apply filter */
        applyFilter() {
            show_ajax_loader();
            /* validation */
            if (this.startDate && this.endDate) {
                if (new Date(this.startDate) > new Date(this.endDate)) {
                    this.validationErrors.endDate = "End date should be greater than start date";
                    hide_ajax_loader();
                    return;
                }
            }
            if (this.endDate && !this.startDate) {
                this.validationErrors.startDate = "Please select start date.";
                hide_ajax_loader();
                return;
            }
            if (this.startDate && !this.endDate) {
                this.validationErrors.endDate = "Please select end date.";
                hide_ajax_loader();
                return;
            }
            this.getTransactionData(1);
            hide_admin_popup('#transactionFilterModalClose');
        },
        formattedDigits(number)
        {
            return formattedNumber(number);
        },

    },
};
</script>