<template>
    <div class="container-fluid  mt_12">
        <div v-if="filterMessage" class="alert warning_msg text-center w-full d-flex align-items-center gap-1" role="alert">
             <svg id="fi_7186971" enable-background="new 0 0 512 512" height="30" viewBox="0 0 512 512" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m505.432 240.144-233.576-233.576c-8.757-8.757-22.955-8.757-31.712 0l-233.576 233.576c-8.757 8.757-8.757 22.955 0 31.712l233.576 233.576c8.757 8.757 22.955 8.757 31.712 0l233.576-233.576c8.757-8.757 8.757-22.955 0-31.712z" fill="#fdb441"></path><path d="m45.162 245.32 200.158-200.158c5.898-5.898 15.461-5.898 21.359 0l200.159 200.158c5.898 5.898 5.898 15.461 0 21.359l-200.158 200.159c-5.898 5.898-15.461 5.898-21.359 0l-200.159-200.158c-5.899-5.899-5.899-15.461 0-21.36z" fill="#ffe177"></path><path d="m79.444 266.676 193.696 193.696-6.458 6.469c-5.901 5.891-15.465 5.891-21.356 0l-200.164-200.165c-5.901-5.901-5.901-15.454 0-21.356l200.164-200.164c5.891-5.891 15.454-5.891 21.356 0l6.458 6.468-193.696 193.696c-5.891 5.902-5.891 15.455 0 21.356z" fill="#ffd15b"></path><g fill="#685e68"><path d="m256 294.366c-14.6 0-26.435-11.835-26.435-26.435v-128.264c0-14.6 11.835-26.435 26.435-26.435 14.6 0 26.435 11.835 26.435 26.435v128.265c0 14.599-11.835 26.434-26.435 26.434z"></path><path d="m256 376.999c-14.6 0-26.435-11.835-26.435-26.435 0-14.6 11.835-26.435 26.435-26.435 14.6 0 26.435 11.835 26.435 26.435 0 14.6-11.835 26.435-26.435 26.435z"></path></g></g></svg>
            <span>{{ filterMessage }}</span>
        </div>
        <!-- **** Shipping Supply Request Filter Modal **** -->
        <div class="modal fade" id="filterModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content onewhitebg">

                    <!-- Modal Header -->
                    <div class="modal-header align-items-center">
                        <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                            <svg height="30" viewBox="-4 0 393 393.99003" width="30" xmlns="http://www.w3.org/2000/svg" id="fi_1159641"><path d="m368.3125 0h-351.261719c-6.195312-.0117188-11.875 3.449219-14.707031 8.960938-2.871094 5.585937-2.3671875 12.3125 1.300781 17.414062l128.6875 181.28125c.042969.0625.089844.121094.132813.183594 4.675781 6.3125 7.203125 13.957031 7.21875 21.816406v147.796875c-.027344 4.378906 1.691406 8.582031 4.777344 11.6875 3.085937 3.105469 7.28125 4.847656 11.65625 4.847656 2.226562 0 4.425781-.445312 6.480468-1.296875l72.3125-27.574218c6.480469-1.976563 10.78125-8.089844 10.78125-15.453126v-120.007812c.011719-7.855469 2.542969-15.503906 7.214844-21.816406.042969-.0625.089844-.121094.132812-.183594l128.683594-181.289062c3.667969-5.097657 4.171875-11.820313 1.300782-17.40625-2.832032-5.511719-8.511719-8.9726568-14.710938-8.960938zm-131.53125 195.992188c-7.1875 9.753906-11.074219 21.546874-11.097656 33.664062v117.578125l-66 25.164063v-142.742188c-.023438-12.117188-3.910156-23.910156-11.101563-33.664062l-124.933593-175.992188h338.070312zm0 0"></path></svg>
                            <span>Filters</span>
                        </h4>
                        <button type="button" class="btn-close" id="supplyFilterModalClose" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body onewhitebg">
                        <h5 class="def_18_size mt-1 themetextcolor"> Shipping Supplies </h5>

                            <div class="row">
                                <!-- Box Types -->
                                <div class="col-12 col-md-6 col-lg-6 mt-2">
                                    <label class="form-label" for="selectClaimReason">Select Box Type</label>
                                    <select v-model="selectedBoxType" class="form-control">
                                        <option value="all" selected>All</option>
                                        <option v-for="(boxType, index) in boxTypes" :value="boxType.name" :key="index"> {{ boxType.name }}</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6 mt-2">
                                    <label for="request_date" class="form-label">Request Date</label>
                                    <input type="date" class="form-control" id="request_date" v-model="requestDate" />
                                </div>
                            </div>

                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer onewhitebg">
                        <button @click="resetFilter" type="button" class="btn btn-warning float-left def_14_size"> Reset Filter </button>
                        <button @click="applyFilter" type="button" class="btn bg_blue text-white def_14_size"> Filter </button>
                    </div>
                </div>
            </div>
        </div>




        <div class="card themetextcolor border-0">
            <div class="card-body rounded onewhitebg ">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3  border-bottom pb-2">
                    <h4 class="coman_main_heading mb-0">Shipping Supplies</h4>
                    <div class="d-flex justify-content-end">
                        <button @click="addSupplyModal" data-bs-toggle="modal" data-bs-target="#create_modal"  class="btn def_14_size bg_blue wmax text-white d-flex align-items-center gap-10 mx-2  rounded">
                            <img :src="profileIc" width="22" height="22">
                            Add New
                        </button>
                        <a data-bs-toggle="modal" data-bs-target="#filterModal" @click="showFilterModal"
                            class="align-items-center btn text-decoration-none  justify-content-center rounded d-flex gap-10  border-0   wmax cursor bg_blue text-white">
                            <img :src="$imagePath + 'filter.svg'" width="16" height="16" />
                            <span class="text-uppercase def_14_size">Filter</span>
                        </a>
                        <button @click="resetFilter" type="button" class="btn btn-warning float-left mx-2 def_14_size"> Reset Filter </button>
                    </div>
                    <!-- Shipping Supply Add Modal -->
                    <user-shipping-supplies-create :addmodal="shippingSupplyAddModal" :closemodal="closeSupplyModal" :boxtypes="boxTypes" :getshippingsupplydata="getShippingSupplyData"></user-shipping-supplies-create>
                </div>
             <!-- Select Status -->
            <div class="mb-2  mt-2">
                <div class="radio-inputs rounded-pill">
                    <label class="radio">
                        <input id="switch_left" name="thisr" type="radio" v-model="shippingSupplyStatus" value="1"  @change="change_status" />
                        <span class="name">Pending</span>
                    </label>
                    <label class="radio">
                        <input id="switch_right" name="thisr" type="radio" v-model="shippingSupplyStatus" value="2" @change="change_status" />
                        <span class="name">Complete</span>
                    </label>
                </div>
            </div>

            <!-- Shipping Supply Listing -->
            <div class="table-responsive mt-3">
                <table class="table table-bordered def_14_size table-hover table_custom">
                    <thead >
                        <tr>
                            <th class="ps-3" scope="col"> # </th>
                            <th scope="col"> Requested By </th>
                            <th scope="col"> Box Type </th>
                            <th scope="col"> Box Quantity </th>
                            <th scope="col"> Request Date </th>
                            <th scope="col"> Status </th>
                            <th scope="col"> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="shippingSupplyData.length > 0"
                            v-for="(shippingSupply, index) in shippingSupplyData" :key="shippingSupply.box_id">
                            <td class="ps-3">{{(paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1}}</td>
                            <td>{{ shippingSupply.full_name ?? '' }}</td>
                            <td>{{ shippingSupply.box_type }}</td>
                            <td>{{ shippingSupply.box_quantity }}</td>
                            <td class="text-nowrap">{{ formatDate(shippingSupply.created_at) }}</td>
                            <td>{{ shippingSupply.status == 2 ? 'Complete' : 'Pending' }}</td>
                            <td>
                                <button type="button" @click="viewRequest(shippingSupply.box_id)" class="globalviewbtn extrabtns rounded-pill text-decoration-none fw-semibold d-flex themetextcolor align-items-center def_14_size gap-1  open_notifSlid action_toSlid"><img :src="viewIc" width="35" height="35"><span class="viewtext_Gl">View</span></button>
                            </td>
                        </tr>
                        <tr v-else>
                            <td  colspan="7">No Request Found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <user-pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getShippingSupplyData"></user-pagination>
            <!-- Shipping Supply Listing End -->

                <!-- Shipping Supply View SideBar -->
                <div class="dataSlide_bar_wrap   px-0">
                    <div class="dataSlide_bar_inner  ">
                        <div class="card border-0 dataSlide_bar_div ">
                            <div class="card-header  dataSlideh">
                                <div class="media py-1 d-flex align-items-center gap-10">
                                    <div class="flex-grow-1"><span class=" def_18_size themetextcolor">Shipping Supply Request Details</span></div>
                                    <div class="close_btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0"
                                            viewBox="0 0 487.619 487.619" style="enable-background:new 0 0 512 512"
                                            xml:space="preserve" class="">
                                            <g>
                                                <path
                                                    d="M243.878 0a243.81 243.81 0 1 0 243.741 243.739A244.016 244.016 0 0 0 243.878 0zm110.697 322.889a22.068 22.068 0 0 1 0 31.27c-4.203 4.12-9.852 6.427-15.738 6.427s-11.537-2.306-15.74-6.427l-79.219-79.148-79.147 79.148a22.21 22.21 0 0 1-31.272 0 22.054 22.054 0 0 1-6.494-15.635 22.07 22.07 0 0 1 6.494-15.635l79.148-79.357-79.148-79.079a22.137 22.137 0 0 1 .752-30.52 22.137 22.137 0 0 1 30.52-.752l79.147 79.079 79.08-79.079a22.348 22.348 0 0 1 31.48 0 22.072 22.072 0 0 1 0 31.271l-79.08 79.079z"
                                                    fill="#ffffff" opacity="1" data-original="#000000" class=""></path>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body px-0 pt-0  onewhitebg dataSlide_bar_list  loaded_list">
                                <!-- <div class="d-flex pb-3 px-3 align-items-center flex-wrap justify-content-end">
                                    <div class="me-2">
                                        <span class="fontw600">Shipping Supply Request Details</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-10">
                                        <a class="text-decoration-none" href="#"><img :src="$imagePath + 'about.svg'"
                                                width="25" height="25" /></a>
                                        <a class="text-decoration-none" href="#"><img :src="$imagePath + 'edit.svg'"
                                                width="25" height="25" />
                                        </a>
                                    </div>
                                </div> -->
                                <div class="block_wraps">
                                    <!-- Supplies Data -->
                                    <transition name="fade">
                                        <div class="card-body py-0" v-if="!isShowViewAnimLoader && viewRequestData">
                                            <h3 class="list-header border-bottom pb-1 def_20_size"> Info </h3>
                                            <table class="table def_14_size table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Request Date</td>
                                                        <td><span class="result_detail_font">{{ formatDate(this.viewRequestData.created_at) ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Status</td>
                                                        <td><span class="result_detail_font">{{ this.viewRequestData.status == 2 ? 'Complete' : 'Pending' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Requested By</td>
                                                        <td><span class="result_detail_font">{{ this.viewRequestData.full_name ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Box Type</td>
                                                        <td><span class="result_detail_font">{{ this.viewRequestData.box_type ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Box Quantity</td>
                                                        <td><span class="result_detail_font">{{
                                                                this.viewRequestData.box_quantity ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Street Address</td>
                                                        <td><span class="result_detail_font">{{ this.viewRequestData.street_address ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Address line 2</td>
                                                        <td><span class="result_detail_font">{{ this.viewRequestData.address_line_2 ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">City</td>
                                                        <td v-if="this.viewRequestData.city"><span class="result_detail_font">{{ getCityName(this.viewRequestData.city) }}</span></td>
                                                        <td v-else></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">State</td>
                                                        <td v-if="this.viewRequestData.state"><span class="result_detail_font">{{ getStateName(this.viewRequestData.state) }}</span></td>
                                                        <td v-else></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">ZIP Code</td>
                                                        <td><span class="result_detail_font">{{ this.viewRequestData.zipcode ?? '' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold first_td_clss">Country</td>
                                                        <td v-if="this.viewRequestData.country"><span class="result_detail_font">{{ getCountryName(this.viewRequestData.country) }}</span></td>
                                                        <td v-else></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </transition>
                                    <!-- Loader animation (only visible while loading) -->
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
    props:{
        boxtypes: {
            type: Object
        },
        shippingsupplydata: {
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

            /** Shipping Supply Add Modal */
            shippingSupplyAddModal: false,
            /** for listing */
            shippingSupplyData: this.shippingsupplydata.data,
            paginationData: this.pagination,
            viewRequestData: {},
             isShowViewAnimLoader: false,

            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,

            /** Filters */
            selectedBoxType: 'all',
            requestDate: '',
            filterMessage: '',
            boxTypes:  this.boxtypes,
            shippingSupplyStatus : 1,
            selectedShippingSupplyStatus: this.shippingSupplyStatus,
        };
    },

    methods: {

        /** add supply modal */
        addSupplyModal() {
            this.shippingSupplyAddModal = true;
        },

        /** add supply modal */
        closeSupplyModal() {
            this.shippingSupplyAddModal = false;
        },

        /** Get Shipping Supply Data */
        async getShippingSupplyData(page = 1, boxtype=this.selectedBoxType, requestdate=this.requestDate) {
            show_ajax_loader();
            try {
                let response = null;
                if (boxtype && requestdate) {
                    response = await axios.get(`${this.$userAppUrl}sdcsmuser/shipping-supplies?page=${page}&status=${this.selectedShippingSupplyStatus ? this.selectedShippingSupplyStatus : this.shippingSupplyStatus}&selectedBoxType=${boxtype}&requestDate=${requestdate}`);
                } else if (boxtype) {
                    response = await axios.get(`${this.$userAppUrl}sdcsmuser/shipping-supplies?page=${page}&status=${this.selectedShippingSupplyStatus ? this.selectedShippingSupplyStatus : this.shippingSupplyStatus}&selectedBoxType=${boxtype}`);
                } else if(requestdate) {
                    response = await axios.get(`${this.$userAppUrl}sdcsmuser/shipping-supplies?page=${page}&status=${this.selectedShippingSupplyStatus ? this.selectedShippingSupplyStatus : this.shippingSupplyStatus}&requestDate=${requestdate}`);
                } else {
                    response = await axios.get(`${this.$userAppUrl}sdcsmuser/shipping-supplies?page=${page}&status=${this.selectedShippingSupplyStatus ? this.selectedShippingSupplyStatus : this.shippingSupplyStatus}`);
                }
                if (response.data.success == true) {
                    if (response.data.shippingSupplyData && response.data.pagination) {
                        this.shippingSupplyData = response.data.shippingSupplyData.data;
                        this.paginationData = response.data.pagination;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, please try again.";
                    }
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$homeUrl}`),4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /** Formate Date */
        formatDate(dateString) {
            return formatDateAndTimeZone(dateString);
        },

        /* on click of view button*/
        async viewRequest(id = '') {
            this.isShowViewAnimLoader = true;
            try {
                let response = await axios.get(`${this.$userAppUrl}sdcsmuser/shipping-supplies-request/view${id !== "" || id !== null ? `?requestId=${id}` : ""}`);
                if (response.data.success == true) {
                    this.viewRequestData = response.data.viewData;
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } finally {
                setTimeout(() => {
                    this.isShowViewAnimLoader = false;
                }, 800); /* short delay for animation effect */
            }
        },

        /* Filter Data */
        applyFilter() {
            let selectedBoxTypeName = this.selectedBoxType == 'all' ? 'all' : this.boxTypes.find((box) => box.name == this.selectedBoxType)?.name;
            let requestedDate = this.formatDate(this.requestDate);

            this.filterMessage = `Selected box type: ${selectedBoxTypeName}` + (this.requestDate ? ` | from  ${requestedDate}`: '');

            this.getShippingSupplyData(1, this.selectedBoxType, this.requestDate);
            hide_user_popup('#supplyFilterModalClose');
        },

        /* Reset Filter */
        resetFilter() {
            this.selectedBoxType = 'all';
            this.requestDate = '';
            this.filterMessage ='';
            this.getShippingSupplyData();
            hide_user_popup('#supplyFilterModalClose');
        },

        /** get country */
        getCountryName(countryCode) {
            if (countryCode) {
                const country = this.countryData.find((c) => c.code == countryCode);
                return country ? country.name : '';
            }
            return '';
        },

        /* get state */
        getStateName(stateCode) {
            if (stateCode) {
                const state = this.statesData.find((s) => s.abbreviation == stateCode);
                return state ? state.name : '';
            }
            return '';
        },

        /* get city */
        getCityName(cityCode) {
            if (cityCode) {
                const city = this.cityData.find((c) => c.province == cityCode);
                return city ? city.name : '';
            }
            return '';
        },

        change_status(){
            this.selectedBoxType = 'all';
            this.requestDate = '';
            this.filterMessage = '';
            this.selectedShippingSupplyStatus = this.shippingSupplyStatus;
            this.getShippingSupplyData();
        },
    },
};
</script>