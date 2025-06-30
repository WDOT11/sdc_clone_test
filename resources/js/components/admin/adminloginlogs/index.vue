<template>
    <div class="container-fluid mt_12">

        <!-- **** Login Logs Filter **** -->
        <div class="modal fade" id="LoginLogs">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content onewhitebg">
                    <!-- Modal Header -->
                    <div class="modal-header align-items-center">
                        <h4 class="modal-title def_18_size d-flex gap-10 mb-0 align-items-center">
                            <svg height="25" viewBox="-4 0 393 393.99003" width="25" xmlns="http://www.w3.org/2000/svg"
                                id="fi_1159641">
                                <path
                                    d="m368.3125 0h-351.261719c-6.195312-.0117188-11.875 3.449219-14.707031 8.960938-2.871094 5.585937-2.3671875 12.3125 1.300781 17.414062l128.6875 181.28125c.042969.0625.089844.121094.132813.183594 4.675781 6.3125 7.203125 13.957031 7.21875 21.816406v147.796875c-.027344 4.378906 1.691406 8.582031 4.777344 11.6875 3.085937 3.105469 7.28125 4.847656 11.65625 4.847656 2.226562 0 4.425781-.445312 6.480468-1.296875l72.3125-27.574218c6.480469-1.976563 10.78125-8.089844 10.78125-15.453126v-120.007812c.011719-7.855469 2.542969-15.503906 7.214844-21.816406.042969-.0625.089844-.121094.132812-.183594l128.683594-181.289062c3.667969-5.097657 4.171875-11.820313 1.300782-17.40625-2.832032-5.511719-8.511719-8.9726568-14.710938-8.960938zm-131.53125 195.992188c-7.1875 9.753906-11.074219 21.546874-11.097656 33.664062v117.578125l-66 25.164063v-142.742188c-.023438-12.117188-3.910156-23.910156-11.101563-33.664062l-124.933593-175.992188h338.070312zm0 0">
                                </path>
                            </svg>
                            <span>Filters </span>
                        </h4>
                        <button type="button" class="btn-close" id="loginLogsFilterModalClose" data-bs-dismiss="modal" @click="closeFilter"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body onewhitebg ">
                        <div class="container-fluid  mt-2 ">
                            <div class="row g-3">
                                <div class="col-12 col-md-4 col-lg-4">
                                    <label for="ipAddress" class="form-label mt-3">Ip Address</label>
                                    <input type="text" v-model="search_ip" class="form-control def_14_size w-100" placeholder="Filter by IP Address">
                                </div>
                                <div class="col-12 col-md-4 col-lg-4">
                                    <label for="date" class="form-label mt-3">Email</label>
                                    <input type="text" v-model="search_email" class="form-control def_14_size w-100" placeholder="Filter by Email">
                                </div>
                                <div class="col-12 col-md-4 col-lg-4">
                                    <label for="date" class="form-label mt-3">Date</label>
                                    <input type="date" v-model="search_by_date" class="form-control def_14_size w-100" placeholder="Filter by Date">
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid  mt-2 ">
                            <div class="row g-2">
                                <div class="col-12 col-md-6 col-lg-6">
                                    <label for="status" class="form-label mt-3">Status</label>
                                     <select v-model="search_by_success" class="form-control def_14_size w-100">
                                        <option value="" selected>Select Success Type</option>
                                        <option value="1">Login Success</option>
                                        <option value="0">Login Failed</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <label for="panel" class="form-label mt-3">Panel</label>
                                     <select v-model="search_by_panel" class="form-control def_14_size w-100">
                                        <option value="" selected>Select Panel</option>
                                        <option value="Admin">Admin</option>
                                        <option value="User">User</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer onewhitebg">
                        <button type="button" @click="filterRecords" class="btn bg_blue text-white">
                            Filter
                        </button>
                         <button type="button" @click="clearFilter" class="btn btn-warning float-left">
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- ****Login Logs Filter End **** -->
        <!-- **** Login Logs Listing ****-->
        <div class="card onewhitebg rounded border-0 ">
            <div class="card-body    ">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 border-bottom pb-2  ">
                    <h5 class="coman_main_heading mb-0">Login Logs</h5>
                    <div class="d-flex align-items-center gap-10 justify-content-start justify-content-md-start justify-content-lg-end flex-wrap">
                        <div class="d-flex justify-content-end">
                            <a data-bs-toggle="modal" data-bs-target="#LoginLogs" class="align-items-center text-decoration-none  justify-content-center  d-flex gap-10 border-0 def_14_size  btn  blogal_pbtn_padding wmax cursor bg_blue text-white">
                                    <img :src="$imagePath + 'filter.svg'" width="16" height="16" />
                                    <span class="text-uppercase">Filter</span>
                            </a>
                            <button type="button" @click="clearFilter" class="btn customm_reset_btn def_14_size ml-1">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size table_custom">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" scope="col"> # </th>
                                <th scope="col"> IP Address </th>
                                <th scope="col"> Browser </th>
                                <th scope="col"> Email </th>
                                <th scope="col"> Password </th>
                                <th scope="col"> Panel </th>
                                <th scope="col"> Success </th>
                                <th scope="col"> Date & Time </th>
                                <!-- <th scope="col"> Action </th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="logData.length > 0" v-for="(log, index) in logData" :key="log.id">
                                <td class="ps-3">
                                    {{ (paginationData.current_page < 1 ? paginationData.current_page :
                                        paginationData.current_page - 1) * paginationData.per_page + index + 1}} </td>
                                <td>{{ log.ip_address }}</td>
                                <td>{{ log.browser }}</td>
                                <td class="text-nowrap">{{ log.user_email }}</td>
                                <td>{{ log.password_attempt }}</td>
                                <td>{{ log.panel }}</td>
                                <td>
                                    <span v-if="log.success == 1" class="badge badge_covered text-white green_color px-3 rounded-pill">
                                        Login Success
                                    </span>
                                    <span v-else class="badge badge_uncovered orange_color px-3 rounded-pill">
                                        Login Failed
                                    </span>
                                </td>
                                <td class="text-nowrap">{{ formatDate(log.date) }}</td>
                                <!-- <td>
                                    <button type="button" @click="viewClaim(log.id)" class="btn  bg-transparent open_notifSlid action_toSlid"><img :src="viewIc" width="35" height="35"></button>
                                </td> -->
                            </tr>
                            <tr v-else>
                                <td colspan="9">No Log Found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <user-pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getLogData"></user-pagination>
            </div>
        </div>
        <!-- **** Login Logs Listing End ****-->
    </div>
</template>
<script>
export default {
    props: {
        logdata: {
            type: Object,
        },
        paginationdata: {
            type: Object,
        }
    },
    data() {
        return {
            searchIc: this.$searchIcIcon,
            logData: this.logdata.data,
            paginationData: this.paginationdata,
            search_email: '',
            search_ip: '',
            search_by_success: '',
            search_by_panel: '',
            search_by_date: '',
            validationError: {},
        }
    },

    methods: {
        /** Get the data on page change and search */
        async getLogData(page = 1, search_email = this.search_email, search_ip = this.search_ip, search_by_success = this.search_by_success, search_by_date = this.search_by_date, search_by_panel = this.search_by_panel) {
            show_ajax_loader();
            try {
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/login-logs?page=${page}${search_email && search_email !== "" ? `&email=${search_email}` : ""}${search_ip && search_ip !== "" ? `&search_ip=${search_ip}` : ""}${search_by_success && search_by_success !== "" ? `&success=${search_by_success}` : ""}${search_by_date && search_by_date !== "" ? `&date=${search_by_date}` : ""}${search_by_panel && search_by_panel !== "" ? `&panel=${search_by_panel}` : ""}`);
                if (response.data.success == true) {
                    if (response.data.logData && response.data.pagination) {
                        this.logData = response.data.logData.data;
                        this.paginationData = response.data.pagination;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, please try again.";
                    }
                }
            } catch (error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
                setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 4000);
            }
        },

        /** Filter records */
        filterRecords() {
            this.validationError = {};
            if (this.search_ip || this.search_email || this.search_by_success || this.search_by_date || this.search_by_panel) {
                this.getLogData();
                hide_admin_popup('#loginLogsFilterModalClose');
            } else {
                this.validationError.filterError = 'Please select one of the filter.';
            }
        },

        /** Clear Filter */
        clearFilter(event) {
            if (event && event.target) {
                event.target.blur();
            }
            this.search_email = '';
            this.search_ip = '';
            this.search_by_success = '';
            this.search_by_date = '';
            this.search_by_panel = '';
            hide_admin_popup('#loginLogsFilterModalClose');
            this.getLogData();
        },

        /** Close Filter Modal */
        closeFilter(event) {
            /* Remove focus from the close button to avoid focus staying on a hidden element */
            if (event && event.target) {
                event.target.blur();
            }
            this.search_email = '';
            this.search_ip = '';
            this.search_by_success = '';
            this.search_by_date = '';
            this.search_by_panel = '';
        },

        /** Format Date */
        formatDate(dateString) {
            return formatDateTimeAndTimeZone(dateString);
        },

    },
}
</script>