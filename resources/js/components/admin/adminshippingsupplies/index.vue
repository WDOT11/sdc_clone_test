<template>
    <div class="container-fluid mt_12">

        <!-- **** Shipping Supplies Listing ****-->
        <div class="card rounded border-0 onewhitebg">
            <div class="card-body    ">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 border-bottom pb-2  ">
                    <h5 class="coman_main_heading mb-0">Shipping Supplies</h5>
                    <div class="d-flex align-items-center gap-10 justify-content-start justify-content-md-start justify-content-lg-end flex-wrap">
                        <input type="text" v-model="searchText" class="form-control def_14_size w-auto" placeholder="Filter by Email or Organization Name">
                        <small v-if="validationError.filterError"><ErrorMessage :msg="validationError.filterError"></ErrorMessage></small>
                        <div class="d-flex justify-content-end">
                            <button type="button" @click="filterRecords" class="align-items-center text-decoration-none  justify-content-center  d-flex gap-10 border-0 def_14_size  btn  blogal_pbtn_padding wmax cursor bg_blue text-white">
                                <img :src="searchIc" width="20" height="20">
                                Search
                            </button>
                            <button type="button" @click="clearFilter" class="btn customm_reset_btn def_14_size ml-1">
                                Clear
                            </button>
                        </div>

                    </div>
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
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size table_custom">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" scope="col"> # </th>
                                <th scope="col"> Name </th>
                                <th scope="col"> Email </th>
                                <th scope="col"> Organization </th>
                                <th scope="col"> Sub Organization </th>
                                <th scope="col"> Status</th>
                                <th scope="col"> Date </th>
                                <th scope="col"> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="shippingSupplyData.length > 0" v-for="(shippingSupply, index) in shippingSupplyData" :key="shippingSupply.id">
                                <td class="ps-3">
                                     {{ (paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1}}
                                </td>
                                <td class="text-nowrap">{{ shippingSupply.full_name ?? '' }}</td>
                                <td class="text-nowrap">{{ shippingSupply.email ?? '' }}</td>
                                <td>{{ shippingSupply.organization_name ?? '' }}</td>
                                <td class="text-nowrap">{{ shippingSupply.sub_organization_name ?? '-' }}</td>
                                <td class="text-nowrap">
                                    <template v-if="editingRowId !== shippingSupply.id">
                                        <span class="d-flex align-items-center gap-2">
                                            {{ shippingSupply.status == 2 ? 'Complete' : 'Pending' }}
                                            <button type="button" @click.stop="toggleEditShippingStatus(true, shippingSupply)">
                                                <img :src="editIc" width="30" height="30" />
                                            </button>

                                        </span>
                                    </template>

                                    <template v-else>
                                        <select v-model="editStatusMap[shippingSupply.id]" class="form-control d-inline w-auto">
                                            <option :value="1">Pending</option>
                                            <option :value="2">Complete</option>
                                        </select>
                                        <button @click="saveStatusUpdate(shippingSupply.id, 'list')" class="btn bg_blue text-white ms-2 def_14_size">Save</button>
                                        <button @click.stop.prevent="toggleEditShippingStatus(false, shippingSupply)" type="button" class="btn customm_reset_btn def_14_size ms-1">Cancel</button>
                                    </template>
                                </td>

                                <td class="text-nowrap">{{ formatDate(shippingSupply.created_at) ?? '' }}</td>
                                <td>
                                    <button type="button" @click="viewSupplyDetails(shippingSupply.id)" class=" rounded-pill globalviewbtn extrabtns d-flex themetextcolor align-items-center def_14_size gap-1 open_notifSlid  action_toSlid"><img :src="viewIc" width="35" height="35"><span class="viewtext_Gl">View</span></button>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="8">No Shipping Supplies Found!</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- **** Shipping Supplies Listing End ****-->

        <!-- **** Shipping Supplies Details View ****-->
        <div :class="supplyId && openPopup ? 'dataSlide_bar_wrap   px-0 onopen_slide' : 'dataSlide_bar_wrap  px-0'">
            <div class="dataSlide_bar_inner  ">
                <div class="card onewhitebg rounded-0 border-0 dataSlide_bar_div ">
                    <div class="card-header dataSlideh">
                        <div class="media d-flex justify-content-between align-items-center gap-10">
                            <h5 class="mb-0 def_18_size themetextcolor">Supply Details</h5>
                            <div class="close_btn">
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 487.619 487.619" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M243.878 0a243.81 243.81 0 1 0 243.741 243.739A244.016 244.016 0 0 0 243.878 0zm110.697 322.889a22.068 22.068 0 0 1 0 31.27c-4.203 4.12-9.852 6.427-15.738 6.427s-11.537-2.306-15.74-6.427l-79.219-79.148-79.147 79.148a22.21 22.21 0 0 1-31.272 0 22.054 22.054 0 0 1-6.494-15.635 22.07 22.07 0 0 1 6.494-15.635l79.148-79.357-79.148-79.079a22.137 22.137 0 0 1 .752-30.52 22.137 22.137 0 0 1 30.52-.752l79.147 79.079 79.08-79.079a22.348 22.348 0 0 1 31.48 0 22.072 22.072 0 0 1 0 31.271l-79.08 79.079z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0  onewhitebg dataSlide_bar_list  loaded_list">
                        <div class="block_wraps">
                            <!-- Shipping Supply Data -->
                            <transition name="fade">
                                <div v-if="!isShowViewAnimLoader && viewSupplyData">
                                    <div class="card-body py-0">
                                        <h3 class="list-header  pb-1 def_16_size"> Info </h3>
                                        <table class="table table-bordered def_14_size">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Name</td>
                                                    <td><span class="result_detail_font">
                                                        {{ this.viewSupplyData[0]?.full_name ?? '-'}}
                                                    </span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss"> Email</td>
                                                    <td>
                                                    <div class="d-flex align-items-center justify-content-between w-100">
                                                        <span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg id="fi_11502423" enable-background="new 0 0 512 512" height="22" viewBox="0 0 512 512" width="22" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m462.88 337.781c0 43.236-35.17 78.351-78.351 78.351h-257.057c-43.181 0-78.352-35.116-78.352-78.351v-163.562c0-14.43 3.951-27.983 10.809-39.615l125.428 125.428c18.765 18.82 43.894 29.19 70.67 29.19 26.721 0 51.85-10.37 70.615-29.19l125.428-125.428c6.859 11.632 10.809 25.184 10.809 39.615v163.562zm-78.352-241.913h-257.056c-17.832 0-34.293 6.035-47.461 16.076l126.69 126.745c13.114 13.058 30.616 20.301 49.326 20.301 18.655 0 36.158-7.243 49.271-20.301l126.69-126.745c-13.167-10.041-29.627-16.076-47.46-16.076zm0-30.232h-257.056c-59.861 0-108.584 48.723-108.584 108.584v163.562c0 59.916 48.723 108.584 108.584 108.584h257.056c59.861 0 108.584-48.668 108.584-108.584v-163.563c0-59.861-48.723-108.583-108.584-108.583z" fill-rule="evenodd"></path></svg>
                                                        {{ this.viewSupplyData[0]?.email ?? '-' }} </span>
                                                        <button type="button" @click="switchUser(this.viewSupplyData[0]?.user_id)" class="slidebarswitch rounded-pill p-1 pe-3 d-flex  align-items-center def_12_size gap-1  btn"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><circle r="256" cx="256" cy="256" fill="#dcdcdc" shape="circle"/><g transform="matrix(0.6000000000000003,0,0,0.6000000000000003,102.39999999999992,102.39999999999992)"><path d="M171.497 125.841C203.173 144.033 224 178.072 224 216.5V240H15v-23.5c0-39.106 21.491-73.197 53.3-91.116" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><circle cx="120" cy="80" r="65" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="M443.833 381.461a104.866 104.866 0 0 1 22.557 17.149C485.3 417.52 497 443.64 497 472.5V497H288v-24.5c0-38.393 20.712-71.955 51.575-90.124" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><circle cx="392" cy="336" r="64" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="M256 112h106c16.569 0 30 13.431 30 30v58" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="m360 184 32 32 32-32M256 400H150c-16.569 0-30-13.431-30-30v-66" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="m152 328-32-32-32 32" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/></g></svg> Switch User </button>
                                                    </div>
                                            </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Phone</td>
                                                    <td><span class="result_detail_font">
                                                        {{ this.viewSupplyData[0]?.phone ?? '-' }}
                                                    </span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Organization Name</td>
                                                    <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1">
                                                        <svg height="22" viewBox="0 0 28 28" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_4653923"><g id="Layer_2" data-name="Layer 2"><path d="m25.467 19.5h-1.967v-2.637a2.366 2.366 0 0 0 -2.363-2.363h-6.637v-4h2.79a1.212 1.212 0 0 0 1.21-1.21v-6.58a1.212 1.212 0 0 0 -1.21-1.21h-6.58a1.212 1.212 0 0 0 -1.21 1.21v6.58a1.212 1.212 0 0 0 1.21 1.21h2.79v4h-6.637a2.366 2.366 0 0 0 -2.363 2.363v2.637h-1.967a1.033 1.033 0 0 0 -1.033 1.032v4.936a1.033 1.033 0 0 0 1.033 1.032h4.934a1.033 1.033 0 0 0 1.033-1.032v-4.936a1.033 1.033 0 0 0 -1.033-1.032h-1.967v-2.637a1.364 1.364 0 0 1 1.363-1.363h6.637v4h-1.967a1.033 1.033 0 0 0 -1.033 1.032v4.936a1.033 1.033 0 0 0 1.033 1.032h4.934a1.033 1.033 0 0 0 1.033-1.032v-4.936a1.033 1.033 0 0 0 -1.033-1.032h-1.967v-4h6.637a1.364 1.364 0 0 1 1.363 1.363v2.637h-1.967a1.033 1.033 0 0 0 -1.033 1.032v4.936a1.033 1.033 0 0 0 1.033 1.032h4.934a1.033 1.033 0 0 0 1.033-1.032v-4.936a1.033 1.033 0 0 0 -1.033-1.032zm-14.967-10.21v-6.58a.21.21 0 0 1 .21-.21h6.58a.21.21 0 0 1 .21.21v6.58a.21.21 0 0 1 -.21.21h-6.58a.21.21 0 0 1 -.21-.21zm-3 11.242v4.936a.033.033 0 0 1 -.033.032l-4.967-.032.033-4.968zm9 0v4.936a.033.033 0 0 1 -.033.032l-4.967-.032.033-4.968zm9 4.936a.033.033 0 0 1 -.033.032l-4.967-.032.033-4.968 4.967.032z"></path></g></svg>
                                                        {{ this.viewSupplyData[0]?.organization_name ?? '-' }}
                                                    </span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Sub-Organization Name</td>
                                                    <td><span class="result_detail_font">
                                                        {{ this.viewSupplyData[0]?.sub_organization_name ?? '-' }}
                                                    </span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Street Address</td>
                                                    <td><span class="result_detail_font">
                                                        {{ this.viewSupplyData[0]?.street_address ?? '-' }}
                                                    </span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Address Line 2</td>
                                                    <td><span class="result_detail_font">
                                                        {{ this.viewSupplyData[0]?.address_line_2 ?? '-' }}
                                                    </span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">City</td>
                                                    <td><span class="result_detail_font">
                                                        {{ getCityName(this.viewSupplyData[0]?.city) ?? '-' }}
                                                    </span></td>
                                                    <!-- <td v-else></td> -->
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">State</td>
                                                    <td><span class="result_detail_font">
                                                        {{ getStateName(this.viewSupplyData[0]?.state) ?? '-' }}
                                                    </span></td>
                                                    <!-- <td v-else></td> -->
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">ZIP Code</td>
                                                    <td><span class="result_detail_font">
                                                        {{ this.viewSupplyData[0]?.zipcode ?? '-' }}
                                                    </span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Country</td>
                                                    <!-- <td v-if="this.viewSupplyData.country"><span class="result_detail_font"> -->
                                                    <td><span class="result_detail_font">
                                                    {{ getCountryName(this.viewSupplyData[0]?.country) ?? '-' }}
                                                    </span></td>
                                                    <!-- <td v-else></td> -->
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Date</td>
                                                    <td>
                                                        <span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg clip-rule="evenodd" fill-rule="evenodd" height="22" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_8740776"><g id="Icon"><path d="m10.5 20.25h-6.5c-.696 0-1.25-.594-1.25-1.312v-13.375c0-.719.554-1.313 1.25-1.313h16c.696 0 1.25.594 1.25 1.313 0-.001 0 6.437 0 6.437 0 .414.336.75.75.75s.75-.336.75-.75v-6.437c0-1.559-1.238-2.813-2.75-2.813h-16c-1.512 0-2.75 1.254-2.75 2.813v13.375c0 1.558 1.238 2.812 2.75 2.812h6.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"></path><path d="m4.75 2v3c0 .414.336.75.75.75s.75-.336.75-.75v-3c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path><path d="m17.75 2v3c0 .414.336.75.75.75s.75-.336.75-.75v-3c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path><path d="m11.25 2v3c0 .414.336.75.75.75s.75-.336.75-.75v-3c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path><path d="m2 9.75h20c.414 0 .75-.336.75-.75s-.336-.75-.75-.75h-20c-.414 0-.75.336-.75.75s.336.75.75.75z"></path><path d="m17.25 11.75c-3.036 0-5.5 2.464-5.5 5.5s2.464 5.5 5.5 5.5 5.5-2.464 5.5-5.5-2.464-5.5-5.5-5.5zm0 1.5c2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4 1.792-4 4-4z"></path><path d="m16.5 15v2c0 .175.062.345.174.48l1.25 1.5c.265.318.738.361 1.056.096s.361-.738.096-1.056l-1.076-1.292v-1.728c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path></g></svg>{{ formatDate(this.viewSupplyData[0]?.created_at) ?? '' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_class">Status</td>
                                                    <td>
                                                         <span class="result_detail_font">
                                                            <template v-if="!isEditingShippingStatus">
                                                                <span class="d-flex align-items-center gap-2">
                                                                    {{ this.viewSupplyData[0]?.status == 2 ? 'Complete' : 'Pending' }}
                                                                    <button type="button" @click.stop="updateShippingStatus()">
                                                                        <img :src="editIc" width="30" height="30">
                                                                    </button>
                                                                </span>
                                                            </template>
                                                            <template v-else>
                                                                <select v-model="editStatus" class="form-control d-inline w-auto">
                                                                    <option :value="1">Pending</option>
                                                                    <option :value="2">Complete</option>
                                                                </select>
                                                                <button @click="saveStatusUpdate(this.viewSupplyData[0]?.id, 'detail')" class="btn bg_blue text-white def_14_size ms-2">Save</button>
                                                                <button @click.stop.prevent="cancelStatusEdit" type="button" class="btn customm_reset_btn def_14_size ms-1">Cancel</button>
                                                            </template>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-body py-0">
                                    <h3 class="list-header def_16_size"> Box Information </h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size">
                                        <thead>
                                            <tr>
                                                <th class="fw-bold first_td_clss">Box Type</th>
                                                <th class="fw-bold">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in viewSupplyBoxDetails" :key="index">
                                                <td><span class="fw-bold result_detail_font">{{ item.box_type ?? '' }}</span></td>
                                                <td><span class="fw-bold result_detail_font">{{ item.box_quantity ?? '' }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
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
        <!-- **** Shipping Supplies Details View End ****-->
        </div>
    </div>
</template>
<script>
export default {
    props: {
        shippingsupplydata: {
            type: Object,
        },
        pagination: {
            type: Object,
        },
    },
    data() {
        return {
            /** To show the view icon */
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            editIc: this.$editIcIcon,

            /** Used to search the records */
            searchText : '',
            shippingSupplyStatus : 1,
            selectedShippingSupplyStatus: this.shippingSupplyStatus,

            /** To store the data of supplies */
            shippingSupplyData: this.shippingsupplydata.data,
            paginationData: this.pagination,

            /** To store the data on view supply */
            viewSupplyData: {},
            viewSupplyBoxDetails: {},
            isShowViewAnimLoader: false,
            /**To manage the shipping supply status update */
            isEditingShippingStatus : false,
            editStatus: null,
            listEditStatus: null,
            editingRowId: null, // which row is currently being edited
            editStatusMap: {},
            originalStatusMap: {}, // to store original values before edit

            /** To manage the country, sate, and city dropdown */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,

            /** To manage the validation error */
            validationError: {},
            openPopup: false,
            supplyId: null,
        };
    },
    mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        this.openPopup = urlParams.get('openPopup');
        this.supplyId = urlParams.get('supplyId');
        if (this.openPopup == "true" && this.supplyId) {
            this.viewSupplyDetails(this.supplyId);

        } else {
            this.openPopup = false;
            this.supplyId = null;
        }
    },
    methods: {

        /** Get Shipping Supply Data */
        async getShippingSupplyData(page = 1) {
            show_ajax_loader();
            try {
                let response = null;
                if(this.searchText) {
                    response = await axios.get(`${this.$userAppUrl}smarttiusadmin/shipping-supplies?page=${page}&status=${this.selectedShippingSupplyStatus}&search=${this.searchText}`);
                } else {
                    response = await axios.get(`${this.$userAppUrl}smarttiusadmin/shipping-supplies?page=${page}&status=${this.selectedShippingSupplyStatus}`);
                }
                if (response.data.success == true) {
                    if (response.data.shippingSupplyData && response.data.pagination) {
                        this.shippingSupplyData = response.data.shippingSupplyData.data;
                        this.paginationData = response.data.pagination;
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, please try again.";
                    }
                    hide_ajax_loader();
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`),4000);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            }
        },

        /* on click of view button*/
        async viewSupplyDetails(id = '') {
            this.isShowViewAnimLoader = true;
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/shipping-supplies/view${id !== "" || id !== null ? `?requestId=${id}` : ""}`);
                if (response.data.success == true) {
                    this.viewSupplyData = response.data.viewSupplyData;
                    this.editStatus = this.viewSupplyData[0]?.status;
                    this.isEditingShippingStatus = false;
                    this.viewSupplyBoxDetails = response.data.viewBoxData;
                }else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`),4000);
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

        /** To filter the records by email and organization name */
        filterRecords(){
            this.validationError = {};
            if(this.searchText){
                this.getShippingSupplyData();
            } else {
                this.validationError.filterError = 'Pleas enter email or organization name.';
            }
        },

        /** Clear Filter */
        clearFilter() {
            this.searchText = '';
            this.validationError = {};
            this.getShippingSupplyData();
        },

        /** Formate Date with Time */
        formatDate(dateString) {
            return formatDateTimeAndTimeZone(dateString);
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

        /** To swith the user */
        async switchUser(userId) {
            show_ajax_loader();
            try {
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/users/switch-user/${userId}`);
                if (response.data.success == true) {
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                    setTimeout(() => {
                        location.href = response.data.redirect_url;
                    }, 1500);
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            }
        },


        /** To update the status(Changing the HTML) */
        updateShippingStatus(){
            this.editStatus = this.viewSupplyData[0]?.status;
            this.listEditStatus = this.viewSupplyData[0]?.status;
            this.isEditingShippingStatus = true;
        },
         /** To save the updated claim note */
        async saveStatusUpdate(shippingSupplyID, source){
            show_ajax_loader();
            try{
                const statusToSend = source == 'list' ? this.editStatusMap?.[shippingSupplyID] : this.editStatus;
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/shipping-supplies/update-status`, {
                    id: shippingSupplyID,
                    status: statusToSend,
                });
                if (response.data.success == true) {
                    if (response.data) {
                        if (this.viewSupplyData[0]?.status) {
                            this.viewSupplyData[0].status = response.data.status;
                            this.editStatus = response.data.status;
                            this.editingRowId = null;
                        } else {
                            this.editStatusMap[shippingSupplyID] = response.data.status;
                            this.editingRowId = null;
                            delete this.originalStatusMap[shippingSupplyID];
                        }
                        this.getShippingSupplyData();
                        this.isEditingShippingStatus = false;
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                    }
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
            }
        },
        cancelStatusEdit() {
            this.editStatus = this.viewSupplyData[0]?.status;
            this.isEditingShippingStatus = false;
        },
        change_status(){
            this.searchText = '';
            this.selectedShippingSupplyStatus = this.shippingSupplyStatus;
            this.getShippingSupplyData();
        },
       /*  toggleEditShippingStatus(enable, supply) {
            if (enable) {
                this.editingRowId = supply.id;
                if (!(supply.id in this.editStatusMap)) {
                    this.editStatusMap[supply.id] = supply.status;
                }
            } else {
                this.editStatusMap[supply.id] = supply.status;
                this.editingRowId = null;
            }
        }, */


        toggleEditShippingStatus(enable, supply) {
            if (enable) {
                this.editingRowId = supply.id;

                // Store original before any changes
                this.editStatusMap[supply.id] = supply.status;
                this.originalStatusMap[supply.id] = supply.status;

            } else {
                // Revert to original if cancelled
                this.editStatusMap[supply.id] = this.originalStatusMap[supply.id];
                this.editingRowId = null;
            }
        }


    },
};
</script>