<template>
    <div class="container-fluid mt_12">
        <!-- **** Repair Request Device Filter Modal **** -->
        <div class="modal" id="myFilters">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content onewhitebg">

                    <!-- Modal Header -->
                    <div class="modal-header align-items-center">
                        <h4 class="modal-title def_18_size d-flex gap-10 mb-0 align-items-center">
                            <svg height="25" viewBox="-4 0 393 393.99003" width="25" xmlns="http://www.w3.org/2000/svg" id="fi_1159641"><path d="m368.3125 0h-351.261719c-6.195312-.0117188-11.875 3.449219-14.707031 8.960938-2.871094 5.585937-2.3671875 12.3125 1.300781 17.414062l128.6875 181.28125c.042969.0625.089844.121094.132813.183594 4.675781 6.3125 7.203125 13.957031 7.21875 21.816406v147.796875c-.027344 4.378906 1.691406 8.582031 4.777344 11.6875 3.085937 3.105469 7.28125 4.847656 11.65625 4.847656 2.226562 0 4.425781-.445312 6.480468-1.296875l72.3125-27.574218c6.480469-1.976563 10.78125-8.089844 10.78125-15.453126v-120.007812c.011719-7.855469 2.542969-15.503906 7.214844-21.816406.042969-.0625.089844-.121094.132812-.183594l128.683594-181.289062c3.667969-5.097657 4.171875-11.820313 1.300782-17.40625-2.832032-5.511719-8.511719-8.9726568-14.710938-8.960938zm-131.53125 195.992188c-7.1875 9.753906-11.074219 21.546874-11.097656 33.664062v117.578125l-66 25.164063v-142.742188c-.023438-12.117188-3.910156-23.910156-11.101563-33.664062l-124.933593-175.992188h338.070312zm0 0"></path></svg>
                            <span>Filters</span>
                        </h4>
                        <button type="button" class="btn-close" id="repairFilterModalClose" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body onewhitebg">
                        <div class="container-fluid  ">
                            <div class="row">
                                <div class="col">
                                    <label  for="statuss" class="form-label">Repair Status</label>
                                    <select id="statuss" v-model="selectedStatus" class="form-control">
                                        <option value="all" selected>All</option>
                                        <option v-for="(repairstatus, index) in repairStatus" :value="repairstatus.id" :key="index"> {{ repairstatus.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <h5 class="def_16_size mt-3 mb-0  list-header"> Repair Request Date Range </h5>
                        <div class="container-fluid    py-3">
                            <div class="row g-3">
                                <div class="col-12 col-md-6 col-lg-6">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" :class="['form-control', { 'is-invalid': validationErrors.startDate },]" id="start_date" v-model="startDate" />
                                    <small v-if="validationErrors.startDate" ><ErrorMessage :msg="validationErrors.startDate"></ErrorMessage></small>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" :class="['form-control', { 'is-invalid': validationErrors.endDate },]" id="end_date" v-model="endDate" />
                                    <small v-if="validationErrors.endDate" ><ErrorMessage :msg="validationErrors.endDate"></ErrorMessage></small>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid bg-white  ">
                            <div class="row">
                                <div class="col">
                                    <label for="end_date" class="form-label">Search</label>
                                    <input type="text" class="form-control" placeholder="Search..." v-model="search" />
                                    <p><small>Search By Ticket ID, Device Serial Number</small></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer onewhitebg">
                        <button type="button" @click="resetFilter" class="btn btn-warning float-left def_14_size">
                            Reset Filter
                        </button>
                        <button type="button" @click="applyFilter" class="btn bg_blue text-white def_14_size">
                            Filter
                        </button>
                        <!-- <button data-bs-dismiss="modal" type="button" @click="closeFilterModal" class="btn btn-secondary">
                            Cancel
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
        <!-- </div> -->
        <!-- **** Repair Request Device Filter Modal End **** -->

        <!-- **** Repair Request Filter Message ****-->
        <div v-if="filterMessage" class="alert warning_msg text-center d-flex align-items-center gap-2 w-full" role="alert">
            <svg id="fi_7186971" enable-background="new 0 0 512 512" height="30" viewBox="0 0 512 512" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m505.432 240.144-233.576-233.576c-8.757-8.757-22.955-8.757-31.712 0l-233.576 233.576c-8.757 8.757-8.757 22.955 0 31.712l233.576 233.576c8.757 8.757 22.955 8.757 31.712 0l233.576-233.576c8.757-8.757 8.757-22.955 0-31.712z" fill="#fdb441"></path><path d="m45.162 245.32 200.158-200.158c5.898-5.898 15.461-5.898 21.359 0l200.159 200.158c5.898 5.898 5.898 15.461 0 21.359l-200.158 200.159c-5.898 5.898-15.461 5.898-21.359 0l-200.159-200.158c-5.899-5.899-5.899-15.461 0-21.36z" fill="#ffe177"></path><path d="m79.444 266.676 193.696 193.696-6.458 6.469c-5.901 5.891-15.465 5.891-21.356 0l-200.164-200.165c-5.901-5.901-5.901-15.454 0-21.356l200.164-200.164c5.891-5.891 15.454-5.891 21.356 0l6.458 6.468-193.696 193.696c-5.891 5.902-5.891 15.455 0 21.356z" fill="#ffd15b"></path><g fill="#685e68"><path d="m256 294.366c-14.6 0-26.435-11.835-26.435-26.435v-128.264c0-14.6 11.835-26.435 26.435-26.435 14.6 0 26.435 11.835 26.435 26.435v128.265c0 14.599-11.835 26.434-26.435 26.434z"></path><path d="m256 376.999c-14.6 0-26.435-11.835-26.435-26.435 0-14.6 11.835-26.435 26.435-26.435 14.6 0 26.435 11.835 26.435 26.435 0 14.6-11.835 26.435-26.435 26.435z"></path></g></g></svg>
            <span>{{ filterMessage }}</span>
        </div>
        <!-- **** Repair Request Filter Message End ****-->

        <!-- **** Repair Request Device Details Listing ****-->
        <div class="card  border-0 onewhitebg">
            <div class="card-body   ">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 border-bottom pb-2  ">
                    <h4 class="coman_main_heading mb-0"> Track Repairs <small style="font-size: 13px; color: #696969;"> ({{ formattedDigits(totalRepair) }}) </small></h4>
                    <div class="d-flex justify-content-end gap-10 align-items-center">
                        <a data-bs-toggle="modal" data-bs-target="#myFilters" @click="showFilterModal" class="align-items-center text-decoration-none blogal_pbtn_padding justify-content-center d-flex gap-10 border-0 def_14_size btn wmax cursor bg_blue text-white">
                            <img :src="$imagePath + 'filter.svg'" width="16" height="16" />
                            <span class="text-uppercase def_14_size">Filter</span>
                        </a>
                        <button type="button" @click="exportDevicesRepairRequests" class="blogal_pbtn_padding align-items-center text-decoration-none justify-content-center d-flex gap-10 border-0 def_14_size btn wmax cursor bg_blue text-white"> Export </button>
                        <button type="button" @click="resetFilter" class="btn btn-warning float-left def_14_size blogal_pbtn_padding"> Reset Filter </button>
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size table_custom">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" scope="col"> # </th>
                                <th scope="col"> Zoho Ticket ID </th>
                                <th scope="col"> Zoho Repair ID </th>
                                <th scope="col"> Device </th>
                                <th scope="col"> Serial Number/Asset Tag </th>
                                <th scope="col"> Email </th>
                                <!-- <th scope="col"> Organization </th> -->
                                <th scope="col"> Zoho Repair Status </th>
                                <th scope="col"> User Repair Status </th>
                                <th scope="col"> Repair Reason </th>
                                <th scope="col"> Date </th>
                                <th scope="col"> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="repairData.length > 0" v-for="(repair, index) in repairData" :key="repair.id">
                                <td class="ps-3">
                                    {{ (paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1}}
                                </td>
                                <td>{{ repair.zoho_ticket_number ?? '' }}</td>
                                <td class="text-nowrap">{{ repair.zoho_repair_id ?? '' }}</td>
                                <td class="text-nowrap">{{ (repair?.device_name ?? '') + '(' + (repair?.device_family_name ?? '') + ')' }}</td>
                                <td class="text-nowrap">{{ repair.device_serial_number ?? '' }}</td>
                                <td class="text-nowrap">{{ repair.email ?? '' }}</td>
                                <!-- <td class="text-nowrap">{{ repair.org_name ?? '' }}</td> -->
                                <td class="text-nowrap">{{ getRepairStatus(repair.repair_status)}}</td>
                                <td class="text-nowrap">{{ getUserRepairStatus(repair.user_repair_status)}}</td>
                                <td class="text-nowrap">{{ repair.repair_reason ?? '' }}</td>
                                <td class="text-nowrap">{{ formatDate(repair.created_at) ?? '' }}</td>
                                <td>
                                    <button type="button" @click="viewRepair(repair.id)" class=" rounded-pill themetextcolor globalviewbtn extrabtns d-flex align-items-center def_14_size gap-1  open_notifSlid action_toSlid"><img :src="viewIc" width="35" height="35"><span class="viewtext_Gl">View</span></button>
                                </td>
                            </tr>
                            <tr v-else>
                                <td  colspan="12">Repairs Not Found. </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getrepairData"></pagination>
            </div>
        </div>
        <!-- **** Repair Request Device Details Listing End ****-->
    </div>
    <!-- **** Repair Request Device Details View ****-->
    <div :class="repairId && openPopup ? 'dataSlide_bar_wrap   px-0 onopen_slide' : 'dataSlide_bar_wrap  px-0'">
        <div class="dataSlide_bar_inner h-100  ">
            <div class="card  rounded-0 border-0 dataSlide_bar_div ">
                <div class="card-header dataSlideh">
                    <div class="media d-flex justify-content-between align-items-center gap-10">
                        <h5 class="mb-0 def_18_size themetextcolor">Repair Details</h5>
                        <div class="close_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 487.619 487.619" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M243.878 0a243.81 243.81 0 1 0 243.741 243.739A244.016 244.016 0 0 0 243.878 0zm110.697 322.889a22.068 22.068 0 0 1 0 31.27c-4.203 4.12-9.852 6.427-15.738 6.427s-11.537-2.306-15.74-6.427l-79.219-79.148-79.147 79.148a22.21 22.21 0 0 1-31.272 0 22.054 22.054 0 0 1-6.494-15.635 22.07 22.07 0 0 1 6.494-15.635l79.148-79.357-79.148-79.079a22.137 22.137 0 0 1 .752-30.52 22.137 22.137 0 0 1 30.52-.752l79.147 79.079 79.08-79.079a22.348 22.348 0 0 1 31.48 0 22.072 22.072 0 0 1 0 31.271l-79.08 79.079z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0  onewhitebg dataSlide_bar_list  loaded_list">
                    <!-- <div class="d-flex  px-3 align-items-center justify-content-end">
                        <div class="d-flex align-items-center gap-10">
                            <a class="text-decoration-none" href="#"><img :src="$imagePath + 'about.svg'" width="25" height="25" /></a>
                            <a class="text-decoration-none" href="#"><img :src="$imagePath + 'edit.svg'" width="25" height="25" /></a>
                        </div>
                    </div> -->
                    <div class="block_wraps">
                        <!-- Repair Data  -->
                        <transition name="fade">
                            <div v-if="!isShowViewAnimLoader && viewRepairData">
                                <div class="card-body py-0">
                                    <h3 class="list-header  pb-1 def_16_size"> Info </h3>
                                    <table class="table table-bordered def_14_size">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Zoho Ticket ID</td>
                                                <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg width="22" height="22" enable-background="new 0 0 64 64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" id="fi_12822507"><g id="directional"></g><g id="camera"></g><g id="train_00000178892782225402504130000015690401494868509316_"></g><g id="train"></g><g id="do_not_disturb_00000063623877084305965540000014065032412018789053_"></g><g id="do_not_disturb_00000034776432680539199770000007984210727708532380_"></g><g id="do_not_disturb_00000132775779256058570160000007725190755847483553_"></g><g id="do_not_disturb"></g><g id="cruise_ship"></g><g id="airport_check_in"></g><g id="passport_00000054265535315127765580000007410325881619203231_"></g><g id="vip_ticket_00000041986048335454509630000005438820234996332962_"></g><g id="ticket_price"></g><g id="cable_car"></g><g id="swimming_pool"></g><g id="arrival"></g><g id="departure"></g><g id="discount_coupon"></g><g id="ticket_booking"></g><g id="vip_ticket"></g><g id="support_ticket"></g><g id="ticket_sold_out"></g><g id="checked_ticket"><path d="m28.9 19.5c-6.9 0-12.4 5.6-12.4 12.4s5.6 12.4 12.4 12.4 12.4-5.5 12.4-12.3-5.5-12.5-12.4-12.5zm0 22.9c-5.8 0-10.4-4.7-10.4-10.4 0-5.8 4.7-10.4 10.4-10.4s10.4 4.6 10.4 10.4c0 5.7-4.6 10.4-10.4 10.4z"></path><path d="m33.2 28.2-5.5 5.5-3.1-3.1c-.4-.4-1-.4-1.4 0s-.4 1 0 1.4l3.8 3.8c.2.2.5.3.7.3s.5-.1.7-.3l6.2-6.2c.4-.4.4-1 0-1.4s-1-.4-1.4 0z"></path><path d="m61 26.9c.6 0 1-.4 1-1v-10.5c0-1.8-1.5-3.3-3.2-3.3h-53.6c-1.8 0-3.2 1.5-3.2 3.3v10.5c0 .6.4 1 1 1 2.8 0 5 2.3 5 5.1s-2.3 5.1-5 5.1c-.6 0-1 .4-1 1v10.5c0 1.8 1.5 3.3 3.2 3.3h53.5c1.8 0 3.2-1.5 3.2-3.3v-10.5c0-.6-.4-1-1-1-2.8 0-5-2.3-5-5.1.1-2.8 2.3-5.1 5.1-5.1zm-7 5.1c0 3.6 2.6 6.5 6 7v9.6c0 .7-.6 1.3-1.2 1.3h-9v-1.5c0-.6-.4-1-1-1s-1 .4-1 1v1.5h-42.6c-.7 0-1.2-.6-1.2-1.3v-9.6c3.4-.5 6-3.5 6-7 0-3.6-2.6-6.5-6-7v-9.6c0-.7.6-1.3 1.2-1.3h42.5v1.5c0 .6.4 1 1 1s1-.4 1-1v-1.5h9c.7 0 1.2.6 1.2 1.3v9.6c-3.3.5-5.9 3.4-5.9 7z"></path><path d="m48.8 28.7c-.6 0-1 .4-1 1v4.7c0 .6.4 1 1 1s1-.4 1-1v-4.7c0-.6-.5-1-1-1z"></path><path d="m48.8 19.3c-.6 0-1 .4-1 1v4.7c0 .6.4 1 1 1s1-.4 1-1v-4.7c0-.6-.5-1-1-1z"></path><path d="m48.8 38c-.6 0-1 .4-1 1v4.7c0 .6.4 1 1 1s1-.4 1-1v-4.7c0-.5-.5-1-1-1z"></path></g><g id="online_ticket_00000018237942094179105910000007941614640990071223_"></g><g id="online_ticket"></g><g id="ticket_00000132787386559086070130000003581786009904772786_"></g><g id="ticket"></g><g id="plane_ticket"></g><g id="worldwide_shipping"></g><g id="aviation_security_00000040569115728037331830000013852783306513737093_"></g><g id="aviation_security"></g><g id="flight_dispatcher_00000180350717560613288360000018278251513545641606_"></g><g id="chartered_flight"></g><g id="cancel_flight"></g><g id="compass_00000024707301156403386460000017384547609786364069_"></g><g id="compass_app"></g><g id="compass"></g><g id="flight_dispatcher"></g><g id="flight_delay"></g><g id="flight_schedule_00000022535532625805573330000006856744830413468315_"></g><g id="travel_website_00000047774854199427775640000015028895650002931351_"></g><g id="travel_website"></g><g id="flight_schedule"></g><g id="immigration_document"></g><g id="airplane_mode"></g><g id="passport_00000059290082564803097050000005369525408468146859_"></g><g id="passport"></g><g id="traveling"></g><g id="plane_trip"></g><g id="plane"></g></svg>{{ this.viewRepairData.zoho_ticket_number ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Zoho Repair ID</td>
                                                <td><span class=" result_detail_font diffrent_div d-flex align-items-center gap-1"><svg fill="none" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_10035041"><g fill="rgb(0,0,0)"><path d="m17 21.75h-10c-4.41 0-5.75-1.34-5.75-5.75v-8c0-4.41 1.34-5.75 5.75-5.75h10c4.41 0 5.75 1.34 5.75 5.75v8c0 4.41-1.34 5.75-5.75 5.75zm-10-18c-3.58 0-4.25.68-4.25 4.25v8c0 3.57.67 4.25 4.25 4.25h10c3.58 0 4.25-.68 4.25-4.25v-8c0-3.57-.67-4.25-4.25-4.25z"></path><path d="m19 8.75h-5c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h5c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m19 12.75h-4c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h4c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m19 16.75h-2c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h2c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m8.50043 12.0399c-1.41 0-2.56-1.15-2.56-2.55998 0-1.41 1.15-2.56 2.56-2.56s2.55997 1.15 2.55997 2.56c0 1.40998-1.14997 2.55998-2.55997 2.55998zm0-3.61998c-.58 0-1.06.48-1.06 1.06 0 .57998.48 1.05998 1.06 1.05998s1.06-.48 1.06-1.05998c0-.58-.48-1.06-1.06-1.06z"></path><path d="m11.9999 17.08c-.38 0-.71-.29-.75-.68-.11-1.08-.98-1.95-2.06999-2.05-.46-.04-.92-.04-1.38 0-1.09.1-1.96.96-2.07 2.05-.04.41-.41.72-.82.67-.41-.04-.71-.41-.67-.82.18-1.8 1.61-3.23 3.42-3.39.55-.05 1.11-.05 1.66 0 1.79999.17 3.23999 1.6 3.41999 3.39.04.41-.26.78-.67.82-.02.01-.05.01-.07.01z"></path></g></svg>{{ this.viewRepairData.zoho_repair_id ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewRepairData.org_name">
                                                <td class="fw-bold first_td_clss">Organization Name</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.org_name ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewRepairData.sub_org_name">
                                                <td class="fw-bold first_td_clss">Sub-Organization Name</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.sub_org_name ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Repair Reason</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.repair_reason ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Repair Details</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.repair_details ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Zoho Repair Status</td>
                                                <td><span class="fw-bold result_detail_font">{{ getRepairStatus(this.viewRepairData.repair_status) ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">User Repair Status</td>
                                                <td><span class="fw-bold result_detail_font">{{ getUserRepairStatus(this.viewRepairData.user_repair_status) ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Status Updated Date</td>
                                                <td><span class="fw-bold result_detail_font">{{ formatDate(this.viewRepairData.status_updated_date) ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device owners name</td>
                                                <td><span class="result_detail_font">{{ (this.viewRepairData?.first_name ?? '') + (this.viewRepairData?.last_name ?? '')}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Owners email</td>
                                                <td>

                                                    <div class="d-flex align-items-center justify-content-between w-100">
                                                    <span class="fw-bold result_detail_font">
                                                        {{ this.viewRepairData.email ?? '' }}

                                                    </span>
                                                    <button type="button" @click="switchUser(this.viewRepairData.user_id)" class="slidebarswitch rounded-pill p-1 pe-3 d-flex align-items-center  def_12_size gap-1  btn"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"  x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><circle r="256" cx="256" cy="256" fill="#dcdcdc" shape="circle"/><g transform="matrix(0.6000000000000003,0,0,0.6000000000000003,102.39999999999992,102.39999999999992)"><path d="M171.497 125.841C203.173 144.033 224 178.072 224 216.5V240H15v-23.5c0-39.106 21.491-73.197 53.3-91.116" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><circle cx="120" cy="80" r="65" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="M443.833 381.461a104.866 104.866 0 0 1 22.557 17.149C485.3 417.52 497 443.64 497 472.5V497H288v-24.5c0-38.393 20.712-71.955 51.575-90.124" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><circle cx="392" cy="336" r="64" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="M256 112h106c16.569 0 30 13.431 30 30v58" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="m360 184 32 32 32-32M256 400H150c-16.569 0-30-13.431-30-30v-66" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="m152 328-32-32-32 32" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/></g></svg> Switch User </button>
                                                </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Owners phone</td>
                                                <td><span class="result_detail_font">{{ formatPhoneNumber(this.viewRepairData.phone) ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device</td>
                                                <td v-if="this.viewRepairData.device_name"><span class="result_detail_font">{{ (this.viewRepairData?.device_name ?? '') + '(' + (this.viewRepairData?.device_family_name ?? '') + ')' }}</span></td>
                                                <td v-else></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device serial number/Asset Tag</td>
                                                <td><span class="fw-bold result_detail_font">{{ this.viewRepairData.device_serial_number ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Street Address</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.street_address ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Address Line 2</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.address_line_2 ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">City</td>
                                                <td v-if="this.viewRepairData.city"><span class="result_detail_font">{{ getCityName(this.viewRepairData.city) }}</span></td>
                                                <td v-else></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">State</td>
                                                <td v-if="this.viewRepairData.state"><span class="result_detail_font">{{ getStateName(this.viewRepairData.state) }}</span></td>
                                                <td v-else></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">ZIP Code</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.zip ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Country</td>
                                                <td v-if="this.viewRepairData.country"><span class="result_detail_font">{{ getCountryName(this.viewRepairData.country) }}</span></td>
                                                <td v-else></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">User Registration Date</td>
                                                <td><span class="fw-bold result_detail_font">{{ formatDate(this.viewRepairData.user_registration_date) ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Repair Request Date</td>
                                                <td><span class="fw-bold result_detail_font">{{ formatDate(this.viewRepairData.created_at) ?? '' }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- <div class="card-body py-0">
                                    <h3 class="list-header  pb-1 def_16_size"> Transaction Info </h3>
                                    <table class="table table-bordered def_14_size">
                                        <tbody>
                                             <tr>
                                                <td class="fw-bold first_td_clss">Transaction ID</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.transaction_id ?? '-'}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Amount</td>
                                                <td><span class="result_detail_font">${{ this.viewRepairData.transaction_amount ?? '0'}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Status</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.transaction_status ?? '-'}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Date</td>
                                                <td><span class="result_detail_font">{{formatDate(this.viewRepairData.transaction_created_at) ?? '-' }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div> -->
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
    <!-- **** Repair Request Device Details View End ****-->
    </div>
</template>
<script>
export default {
    props: {
        repairdata: {
            type: Object,
        },
        paginationdata: {
            type: Object,
        },
        totalrepairrequests: {
            type: Number,
        }
    },
    data() {
        return {
            deleteIc: this.$deleteIcon,
            editIc: this.$editIcIcon,
            viewIc: this.$viewIcIcon,
            searchIc: this.$searchIcIcon,
            profileIc: this.$profileIcIcon,

            totalRepair: this.totalrepairrequests,
            repairData: this.repairdata.data,
            paginationData: this.paginationdata,
            viewRepairData: {},
            isShowViewAnimLoader: false,
            /** Managing the repair status from custom.js */
            // repairStatus: this.$claimRepairStatus,
            repairStatus: [],
            userRepairStatus: this.$userClaimRepairStatus,
            /* Filter */
            selectedStatus: 'all',
            selectedUserId: null,
            search: '',
            startDate: '',
            endDate: '',
            filterMessage:'',
            /* validation */
            validationErrors: {},
            /** To manage the country, sate, and city */
            countryData: this.$countries,
            statesData: this.$states,
            cityData: this.$cities,


            openPopup: false,
            repairId: null,
        };
    },
    mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        this.openPopup = urlParams.get('openPopup');
        this.repairId = urlParams.get('repairId');
        const filterData = urlParams.get('filter');
        const querySelectedUser = urlParams.get('userId');
        if (this.openPopup == "true" && this.repairId) {
            this.viewRepair(this.repairId);

        } else {
            this.openPopup = false;
            this.repairId = null;
            this.selectedUserId = null;
        }
        if (filterData == 'true' && querySelectedUser) {
            this.selectedUserId = querySelectedUser;
            this.getrepairData();
        }
    },
    created() {
        this.getClaimRepairStatus();
    },
    methods: {

        /** To get the repair data by using paging */
        async getrepairData(page = 1) {
            show_ajax_loader();
            try {
             /*    let response = null;
                if (status && startDate && endDate) {
                    response = await axios.get(`${this.$userAppUrl}smarttiusadmin/track-repairs?page=${page}&selectedStatus=${status}&startDate=${startDate}&endDate=${endDate}`);
                } else if (status) {
                    response = await axios.get(`${this.$userAppUrl}smarttiusadmin/track-repairs?page=${page}&selectedStatus=${status}`);
                } else if(startDate && endDate) {
                    response = await axios.get(`${this.$userAppUrl}smarttiusadmin/track-repairs?page=${page}&startDate=${startDate}&endDate=${endDate}`);
                } else {
                    response = await axios.get(`${this.$userAppUrl}smarttiusadmin/track-repairs?page=${page}`);
                } */
                let url = `${this.$userAppUrl}smarttiusadmin/track-repairs?page=${page}`;
                if (this.search) {
                    url += `&search=${this.search}`;
                }
                if (this.selectedStatus) {
                    url += `&selectedStatus=${this.selectedStatus}`;
                }
                if (this.startDate && this.endDate) {
                    url += `&startDate=${this.startDate}&endDate=${this.endDate}`;
                }
                if (this.selectedUserId) {
                    url += `&userId=${this.selectedUserId}`;
                }
                /* Make the API call */
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.repairData && response.data.pagination) {
                        this.totalRepair = response.data.pagination.total;
                        this.repairData = response.data.repairData.data;
                        this.paginationData = response.data.pagination;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong";
                    }
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
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

        /* on click of view button*/
        async viewRepair(id) {
            show_overlay();
            this.isShowViewAnimLoader = true;
            try {
                let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/track-repairs/view/${id}`);
                if (response.data.success == true) {
                    this.viewRepairData = response.data.viewData;
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
                }
            } finally {
                setTimeout(() => { this.isShowViewAnimLoader = false; }, 800); /* short delay for animation effect */
            }
        },

        /** To match the status from the array of status */
        getRepairStatus(repairStatusId) {
            /* Find the status object in the repairStatus array */
            const repairStatus = this.repairStatus.find(status => status.id == repairStatusId)?.name;
            /* Return the status if found, otherwise return an empty string */
            return repairStatus ? repairStatus : '';
        },

        /** To match the status from the array of status */
        getUserRepairStatus(repairStatusId) {
            /* Find the status object in the repairStatus array */
            const repairStatus = this.userRepairStatus.find(status => status.id == repairStatusId);
            /* Return the status if found, otherwise return an empty string */
            return repairStatus ? repairStatus.status : '';
        },

        /* Close Filter Modal */
        closeFilterModal() {
            show_ajax_loader();
            hide_admin_popup('#repairFilterModalClose');
            this.selectedStatus = 'all';
            this.selectedUserId = null;
            this.search = '';
            this.startDate = '';
            this.endDate = '';
            this.filterMessage = '';
            this.validationErrors = {};
            this.getrepairData();
            hide_ajax_loader();
        },

        /* Filter Data */
        applyFilter() {
            show_ajax_loader();
            /* validation */
            this.validationErrors = {};
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
            let selectedStatusName = this.selectedStatus == 'all' ? 'all' : this.repairStatus.find((status) => status.id == this.selectedStatus)?.name;
            let startDate = this.filterFormatDate(this.startDate);
            let endDate = this.filterFormatDate(this.endDate);
            this.filterMessage = `Selected status: ${selectedStatusName}` + (this.startDate && this.endDate ? ` | Date range: ${startDate} to ${endDate}`: '');

            this.getrepairData();
            hide_admin_popup('#repairFilterModalClose');
            hide_ajax_loader();
        },

        /* Reset Filter */
        resetFilter() {
            this.closeFilterModal();
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
        /** Formate Date */

        filterFormatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        },

        /** Export devices repair requests */
        async exportDevicesRepairRequests() {
            show_ajax_loader();
            try{
                /* let url = `${this.$userAppUrl}smarttiusadmin/track-repairs/export`;
                if (this.search && this.selectedStatus && this.startDate && this.endDate && this.selectedUserId) {
                    url += `?search=${this.search}&selectedStatus=${this.selectedStatus}&startDate=${this.startDate}&endDate=${this.endDate}&userId=${this.selectedUserId}`;
                } else if (this.search) {
                    url += `?search=${this.search}`;
                }else if (this.selectedStatus) {
                    url += `?selectedStatus=${this.selectedStatus}`;
                } else if (this.startDate && this.endDate) {
                    url += `?startDate=${this.startDate}&endDate=${this.endDate}`;
                } else {
                    url += `?selectedStatus=all`;
                } */
                let baseUrl = `${this.$userAppUrl}smarttiusadmin/track-repairs/export`;
                const params = new URLSearchParams();

                if (this.search) params.append('search', this.search);
                if (this.selectedStatus) params.append('selectedStatus', this.selectedStatus);
                if (this.startDate) params.append('startDate', this.startDate);
                if (this.endDate) params.append('endDate', this.endDate);
                if (this.selectedUserId) params.append('userId', this.selectedUserId);

                // Set defaults if no params provided
                if ([...params].length === 0) {
                    params.append('selectedStatus', 'all');
                }

                const url = `${baseUrl}?${params.toString()}`;


                // Make a GET request to the API endpoint
                // const response = await axios.get(url, { responseType: 'blob' });
                const response = await axios.get(url);
                if (response.data.success == false) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = response.data.msg;
                    return;
                } else {
                    // Create a blob from the response data
                    const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.setAttribute('download', 'devices_repair_report.csv');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
            } finally {
                hide_ajax_loader();
            }
        },

        /** To get the claim repair status */
        async getClaimRepairStatus(){
           let url = `${this.$userAppUrl}smarttiusadmin/get-claim-repair-status`;
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

        formattedDigits(number)
        {
            return formattedNumber(number);
        },
        formatPhoneNumber(phoneNumber){
            if(phoneNumber !== '' && phoneNumber !== null ){
                if (typeof phoneNumber !== 'string') {
                    return '';
                }

                /* Keep only digits and allowed formatting chars */
                let cleaned = phoneNumber.replace(/[^\d\(\)\-\s]/g, '');

                /* Extract digits only */
                let digits = cleaned.replace(/\D/g, '');

                /* Limit to 10 digits for US/Canada */
                if (digits.length > 10) {
                    digits = digits.slice(0, 10);
                }

                /* Apply (XXX) XXX-XXXX format */
                if (digits.length > 6) {
                phoneNumber = `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
                } else if (digits.length > 3) {
                phoneNumber = `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                } else if (digits.length > 0) {
                phoneNumber = `(${digits}`;
                }
                return phoneNumber;
            }
            return '';
        },
    },
};
</script>