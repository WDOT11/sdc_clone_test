<template>
    <div class="container-fluid mt_12">
        <div v-if="filterMessage" class="alert warning_msg  text-center w-full d-flex align-items-center gap-1" role="alert">
            <svg id="fi_7186971" enable-background="new 0 0 512 512" height="30" viewBox="0 0 512 512" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m505.432 240.144-233.576-233.576c-8.757-8.757-22.955-8.757-31.712 0l-233.576 233.576c-8.757 8.757-8.757 22.955 0 31.712l233.576 233.576c8.757 8.757 22.955 8.757 31.712 0l233.576-233.576c8.757-8.757 8.757-22.955 0-31.712z" fill="#fdb441"></path><path d="m45.162 245.32 200.158-200.158c5.898-5.898 15.461-5.898 21.359 0l200.159 200.158c5.898 5.898 5.898 15.461 0 21.359l-200.158 200.159c-5.898 5.898-15.461 5.898-21.359 0l-200.159-200.158c-5.899-5.899-5.899-15.461 0-21.36z" fill="#ffe177"></path><path d="m79.444 266.676 193.696 193.696-6.458 6.469c-5.901 5.891-15.465 5.891-21.356 0l-200.164-200.165c-5.901-5.901-5.901-15.454 0-21.356l200.164-200.164c5.891-5.891 15.454-5.891 21.356 0l6.458 6.468-193.696 193.696c-5.891 5.902-5.891 15.455 0 21.356z" fill="#ffd15b"></path><g fill="#685e68"><path d="m256 294.366c-14.6 0-26.435-11.835-26.435-26.435v-128.264c0-14.6 11.835-26.435 26.435-26.435 14.6 0 26.435 11.835 26.435 26.435v128.265c0 14.599-11.835 26.434-26.435 26.434z"></path><path d="m256 376.999c-14.6 0-26.435-11.835-26.435-26.435 0-14.6 11.835-26.435 26.435-26.435 14.6 0 26.435 11.835 26.435 26.435 0 14.6-11.835 26.435-26.435 26.435z"></path></g></g></svg>
            <span>{{ filterMessage }}</span>
        </div>

        <!-- **** Claim Request Device Filter Modal **** -->
        <div class="modal" id="TrackClaims">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content onewhitebg">
                    <!-- Modal Header -->
                    <div class="modal-header align-items-center">
                        <h4 class="modal-title def_18_size d-flex gap-10 mb-0 align-items-center">
                            <svg height="25" viewBox="-4 0 393 393.99003" width="25" xmlns="http://www.w3.org/2000/svg" id="fi_1159641"><path d="m368.3125 0h-351.261719c-6.195312-.0117188-11.875 3.449219-14.707031 8.960938-2.871094 5.585937-2.3671875 12.3125 1.300781 17.414062l128.6875 181.28125c.042969.0625.089844.121094.132813.183594 4.675781 6.3125 7.203125 13.957031 7.21875 21.816406v147.796875c-.027344 4.378906 1.691406 8.582031 4.777344 11.6875 3.085937 3.105469 7.28125 4.847656 11.65625 4.847656 2.226562 0 4.425781-.445312 6.480468-1.296875l72.3125-27.574218c6.480469-1.976563 10.78125-8.089844 10.78125-15.453126v-120.007812c.011719-7.855469 2.542969-15.503906 7.214844-21.816406.042969-.0625.089844-.121094.132812-.183594l128.683594-181.289062c3.667969-5.097657 4.171875-11.820313 1.300782-17.40625-2.832032-5.511719-8.511719-8.9726568-14.710938-8.960938zm-131.53125 195.992188c-7.1875 9.753906-11.074219 21.546874-11.097656 33.664062v117.578125l-66 25.164063v-142.742188c-.023438-12.117188-3.910156-23.910156-11.101563-33.664062l-124.933593-175.992188h338.070312zm0 0"></path></svg>
                            <span>Filters </span>
                        </h4>
                        <button type="button" class="btn-close" id="claimFilterModalClose" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body onewhitebg ">

                            <div class="container-fluid  ">
                                <h5 class="def_16_size mt-3  list-header"> Claim Status </h5>
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-6 mt-2">
                                        <label class="form-label" for="selectClaimStatus">Select Claim Status</label>
                                        <select v-model="selectedStatus" class="form-control">
                                            <option value="all" selected>All</option>
                                            <option v-for="(claimstatus, index) in adminClaimStatus" :value="claimstatus.id" :key="index"> {{ claimstatus.name }}</option>
                                        </select>
                                    </div>
                                    <!-- Claim Reasons -->
                                    <div class="col-12 col-md-6 col-lg-6 mt-2">
                                        <label class="form-label" for="selectClaimReason">Select Claim Reason</label>
                                        <select v-model="selectedClaimReasons" class="form-control">
                                            <option value="all" selected>All</option>
                                            <option v-for="(claimreason, index) in claimreasons" :value="claimreason.id" :key="index"> {{ claimreason.claim_reason_name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="container-fluid  mt-2 ">
                                <h5 class="def_16_size mt-4  list-header"> Claim Request Date Range</h5>
                            <div class="row ">
                                <div class="col-12 col-md-6 col-lg-6 mt-2">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" :class="['form-control', { 'is-invalid': validationErrors.startDate },]" id="start_date" v-model="startDate" />
                                    <small v-if="validationErrors.startDate" ><ErrorMessage :msg="validationErrors.startDate"></ErrorMessage></small>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6 mt-2">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" :class="['form-control', { 'is-invalid': validationErrors.endDate },]" id="end_date" v-model="endDate" />
                                    <small v-if="validationErrors.endDate" ><ErrorMessage :msg="validationErrors.endDate"></ErrorMessage></small>
                                </div>
                            </div>

                        <div class="row">
                            <div class="col">
                                <label for="searchid" class="form-label mt-3">Search</label>
                                <input id="searchid" type="text" class="form-control" placeholder="Search..." v-model="search" />
                                <p class="mt-1 def_14_size themetextcolor">Search By Zoho Ticket ID, Device Serial Number, Device Name</p>
                            </div>
                        </div>
                            </div>
                        </div>

                    <!-- Modal footer -->
                    <div class="modal-footer onewhitebg">
                        <button type="button" @click="resetFilter" class="btn btn-warning float-left">
                            Reset Filter
                        </button>
                        <button type="button" @click="applyFilter" class="btn bg_blue text-white">
                            Filter
                        </button>
                        <!-- <button data-bs-dismiss="modal" type="button" @click="closeFilterModal" class="btn btn-secondary">
                            Cancel
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
        <!-- **** Claim Request Device Filter Modal End **** -->

        <!-- **** Device Details Listing ****-->
        <div class="card rounded border-0 onewhitebg">
            <div class="card-body">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 border-bottom pb-2  ">
                    <h5 class="coman_main_heading mb-0">Track Claims <small style="font-size: 13px; color: #696969;"> ({{ formattedDigits(totalClaim) }}) </small></h5>
                    <div class="d-flex align-items-center gap-10 justify-content-end">
                        <div @click="showFilterModal" >
                            <div class="d-flex justify-content-end">
                                <a  data-bs-toggle="modal" data-bs-target="#TrackClaims" class="align-items-center text-decoration-none  justify-content-center  d-flex gap-10 border-0 def_14_size  btn  blogal_pbtn_padding wmax cursor bg_blue text-white">
                                    <img :src="$imagePath + 'filter.svg'" width="16" height="16" />
                                    <span class="text-uppercase">Filter</span>
                                </a>
                            </div>
                        </div>
                        <div>
                            <button type="button" @click="exportDevicesClaim" class="align-items-center text-decoration-none  justify-content-center  d-flex gap-10 border-0 def_14_size  btn blogal_pbtn_padding  wmax cursor bg_blue text-white">
                                Export
                            </button>
                        </div>
                        <div>
                            <button type="button" @click="resetFilter" class="btn btn-warning float-left def_14_size blogal_pbtn_padding">
                                Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size table_custom">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" scope="col"> # </th>
                                <th scope="col"> Zoho Ticket ID </th>
                                <th scope="col"> Zoho Claim ID </th>
                                <th scope="col"> Device Name </th>
                                <th scope="col"> Device Type </th>
                                <th scope="col"> Serial Number/Asset Tag </th>
                                <th scope="col"> Zoho Claim Status </th>
                                <th scope="col"> User Claim Status </th>
                                <th scope="col"> Claim Raised Date </th>
                                <th scope="col"> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="claimData.length > 0" v-for="(claim, index) in claimData" :key="claim.id">
                                <td class="ps-3">
                                     {{ (paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1}}
                                </td>
                                <td>{{ claim.zoho_ticket_number }}</td>
                                <td>{{ claim.zoho_claim_id }}</td>
                                <td class="text-nowrap">{{ claim.device_model_name }}</td>
                                <td>{{ claim.device_type == 1 ? 'Personal' : (claim.org_type == 1 ? 'Educational' : 'Organizational') }}</td>
                                <td>{{ claim.serial_number }}</td>
                                <td class="text-nowrap">{{ getClaimStatus(claim.claim_status) ?? '' }}</td>
                                <td >{{ getUserClaimStatus(claim.user_claim_status) ?? '' }}</td>
                                <td class="text-nowrap">{{ formatDate(claim.created_at) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-20">
                                        <button type="button" @click="viewClaim(claim.id)" class=" rounded-pill globalviewbtn extrabtns  d-flex align-items-center def_14_size gap-1 themetextcolor open_notifSlid  action_toSlid"><img :src="viewIc" width="35" height="35"><span class="viewtext_Gl">View</span></button>
                                        <a :href="`/smarttiusadmin/track-claims/edit/${claim.id}`" class=" rounded-pill themetextcolor globaleditbtn extrabtns d-flex def_14_size  align-items-center gap-1 text-decoration-none  rounded editBtn">
                                            <img :src="editIc" width="35" height="35"><span class="edittext_Gl">Edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="10">Claims Not Found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getclaimData"></pagination>
            </div>
        </div>
        <!-- **** Device Details Listing End ****-->

        <!--  Detail box sidebar -->
        <div :class="claimId && openPopup ? 'dataSlide_bar_wrap   px-0 onopen_slide' : 'dataSlide_bar_wrap   px-0'">
            <div class="dataSlide_bar_inner  ">
                <div class="card  border-0 dataSlide_bar_div ">
                    <div class="card-header d-flex justify-content-between align-items-center  dataSlideh ">
                        <div class="media gap-10 border-20 d-flex align-items-center">
                            <!-- <img  :src="$imagePath + 'bell.svg'" /> -->
                            <div class="media-body  themetextcolor ">
                                Claim Details
                            </div>
                             <a class="text-decoration-none" :href="`/smarttiusadmin/track-claims/edit/${this.viewClaimData.id}`"><img :src="$imagePath + 'edit.svg'" width="30" height="30" /></a>

                        </div>
                        <div class="close_btn">
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 487.619 487.619" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M243.878 0a243.81 243.81 0 1 0 243.741 243.739A244.016 244.016 0 0 0 243.878 0zm110.697 322.889a22.068 22.068 0 0 1 0 31.27c-4.203 4.12-9.852 6.427-15.738 6.427s-11.537-2.306-15.74-6.427l-79.219-79.148-79.147 79.148a22.21 22.21 0 0 1-31.272 0 22.054 22.054 0 0 1-6.494-15.635 22.07 22.07 0 0 1 6.494-15.635l79.148-79.357-79.148-79.079a22.137 22.137 0 0 1 .752-30.52 22.137 22.137 0 0 1 30.52-.752l79.147 79.079 79.08-79.079a22.348 22.348 0 0 1 31.48 0 22.072 22.072 0 0 1 0 31.271l-79.08 79.079z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                        </div>
                    </div>

                    <div class="card-body px-0   onewhitebg dataSlide_bar_list  loaded_list">

                        <div class="block_wraps">
                            <!-- Claim Data  -->
                            <transition name="fade">
                                <div v-if="!isShowViewAnimLoader && viewClaimData">
                                    <div class="card-body py-0 ">
                                        <h3 class="list-header  pb-1 def_16_size"> Claim Info </h3>
                                        <table class="table table-bordered def_14_size">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Zoho Ticket ID</td>
                                                    <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg width="22" height="22" enable-background="new 0 0 64 64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" id="fi_12822507"><g id="directional"></g><g id="camera"></g><g id="train_00000178892782225402504130000015690401494868509316_"></g><g id="train"></g><g id="do_not_disturb_00000063623877084305965540000014065032412018789053_"></g><g id="do_not_disturb_00000034776432680539199770000007984210727708532380_"></g><g id="do_not_disturb_00000132775779256058570160000007725190755847483553_"></g><g id="do_not_disturb"></g><g id="cruise_ship"></g><g id="airport_check_in"></g><g id="passport_00000054265535315127765580000007410325881619203231_"></g><g id="vip_ticket_00000041986048335454509630000005438820234996332962_"></g><g id="ticket_price"></g><g id="cable_car"></g><g id="swimming_pool"></g><g id="arrival"></g><g id="departure"></g><g id="discount_coupon"></g><g id="ticket_booking"></g><g id="vip_ticket"></g><g id="support_ticket"></g><g id="ticket_sold_out"></g><g id="checked_ticket"><path d="m28.9 19.5c-6.9 0-12.4 5.6-12.4 12.4s5.6 12.4 12.4 12.4 12.4-5.5 12.4-12.3-5.5-12.5-12.4-12.5zm0 22.9c-5.8 0-10.4-4.7-10.4-10.4 0-5.8 4.7-10.4 10.4-10.4s10.4 4.6 10.4 10.4c0 5.7-4.6 10.4-10.4 10.4z"></path><path d="m33.2 28.2-5.5 5.5-3.1-3.1c-.4-.4-1-.4-1.4 0s-.4 1 0 1.4l3.8 3.8c.2.2.5.3.7.3s.5-.1.7-.3l6.2-6.2c.4-.4.4-1 0-1.4s-1-.4-1.4 0z"></path><path d="m61 26.9c.6 0 1-.4 1-1v-10.5c0-1.8-1.5-3.3-3.2-3.3h-53.6c-1.8 0-3.2 1.5-3.2 3.3v10.5c0 .6.4 1 1 1 2.8 0 5 2.3 5 5.1s-2.3 5.1-5 5.1c-.6 0-1 .4-1 1v10.5c0 1.8 1.5 3.3 3.2 3.3h53.5c1.8 0 3.2-1.5 3.2-3.3v-10.5c0-.6-.4-1-1-1-2.8 0-5-2.3-5-5.1.1-2.8 2.3-5.1 5.1-5.1zm-7 5.1c0 3.6 2.6 6.5 6 7v9.6c0 .7-.6 1.3-1.2 1.3h-9v-1.5c0-.6-.4-1-1-1s-1 .4-1 1v1.5h-42.6c-.7 0-1.2-.6-1.2-1.3v-9.6c3.4-.5 6-3.5 6-7 0-3.6-2.6-6.5-6-7v-9.6c0-.7.6-1.3 1.2-1.3h42.5v1.5c0 .6.4 1 1 1s1-.4 1-1v-1.5h9c.7 0 1.2.6 1.2 1.3v9.6c-3.3.5-5.9 3.4-5.9 7z"></path><path d="m48.8 28.7c-.6 0-1 .4-1 1v4.7c0 .6.4 1 1 1s1-.4 1-1v-4.7c0-.6-.5-1-1-1z"></path><path d="m48.8 19.3c-.6 0-1 .4-1 1v4.7c0 .6.4 1 1 1s1-.4 1-1v-4.7c0-.6-.5-1-1-1z"></path><path d="m48.8 38c-.6 0-1 .4-1 1v4.7c0 .6.4 1 1 1s1-.4 1-1v-4.7c0-.5-.5-1-1-1z"></path></g><g id="online_ticket_00000018237942094179105910000007941614640990071223_"></g><g id="online_ticket"></g><g id="ticket_00000132787386559086070130000003581786009904772786_"></g><g id="ticket"></g><g id="plane_ticket"></g><g id="worldwide_shipping"></g><g id="aviation_security_00000040569115728037331830000013852783306513737093_"></g><g id="aviation_security"></g><g id="flight_dispatcher_00000180350717560613288360000018278251513545641606_"></g><g id="chartered_flight"></g><g id="cancel_flight"></g><g id="compass_00000024707301156403386460000017384547609786364069_"></g><g id="compass_app"></g><g id="compass"></g><g id="flight_dispatcher"></g><g id="flight_delay"></g><g id="flight_schedule_00000022535532625805573330000006856744830413468315_"></g><g id="travel_website_00000047774854199427775640000015028895650002931351_"></g><g id="travel_website"></g><g id="flight_schedule"></g><g id="immigration_document"></g><g id="airplane_mode"></g><g id="passport_00000059290082564803097050000005369525408468146859_"></g><g id="passport"></g><g id="traveling"></g><g id="plane_trip"></g><g id="plane"></g></svg>{{ this.viewClaimData.zoho_ticket_number ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Zoho Claim ID</td>
                                                    <td><span class=" result_detail_font diffrent_div d-flex align-items-center gap-1"><svg fill="none" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_10035041"><g fill="rgb(0,0,0)"><path d="m17 21.75h-10c-4.41 0-5.75-1.34-5.75-5.75v-8c0-4.41 1.34-5.75 5.75-5.75h10c4.41 0 5.75 1.34 5.75 5.75v8c0 4.41-1.34 5.75-5.75 5.75zm-10-18c-3.58 0-4.25.68-4.25 4.25v8c0 3.57.67 4.25 4.25 4.25h10c3.58 0 4.25-.68 4.25-4.25v-8c0-3.57-.67-4.25-4.25-4.25z"></path><path d="m19 8.75h-5c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h5c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m19 12.75h-4c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h4c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m19 16.75h-2c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h2c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m8.50043 12.0399c-1.41 0-2.56-1.15-2.56-2.55998 0-1.41 1.15-2.56 2.56-2.56s2.55997 1.15 2.55997 2.56c0 1.40998-1.14997 2.55998-2.55997 2.55998zm0-3.61998c-.58 0-1.06.48-1.06 1.06 0 .57998.48 1.05998 1.06 1.05998s1.06-.48 1.06-1.05998c0-.58-.48-1.06-1.06-1.06z"></path><path d="m11.9999 17.08c-.38 0-.71-.29-.75-.68-.11-1.08-.98-1.95-2.06999-2.05-.46-.04-.92-.04-1.38 0-1.09.1-1.96.96-2.07 2.05-.04.41-.41.72-.82.67-.41-.04-.71-.41-.67-.82.18-1.8 1.61-3.23 3.42-3.39.55-.05 1.11-.05 1.66 0 1.79999.17 3.23999 1.6 3.41999 3.39.04.41-.26.78-.67.82-.02.01-.05.01-.07.01z"></path></g></svg>{{ this.viewClaimData.zoho_claim_id ?? '' }}</span></td>
                                                </tr>
                                               <!--  <tr>
                                                    <td class="fw-bold first_td_clss">Device Name</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.device_name }}</span></td>
                                                </tr> -->
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Device Name</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.device_model_name }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Serial number / Asset tag</td>
                                                    <td><span class="fw-bold result_detail_font diffrent_div d-flex align-items-center gap-1 "><svg height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_12126082"><g fill="none" fill-rule="evenodd" stroke="#000" stroke-linecap="round" stroke-linejoin="round"><path d="m9 13.5h4l-.5 2 .5-2h2.5-2.5l1-4h1.5-1.5l.5-2-.5 2h-4l-1 4h-1.5 1.5l1-4h4l-1 4h-4l-.5 2zm1-4h-2.5 2.5l.5-2z"></path><path d="m7 2.5h9c.8284271 0 1.5.67157288 1.5 1.5v15c0 .8284271-.6715729 1.5-1.5 1.5h-9c-.82842712 0-1.5-.6715729-1.5-1.5v-15c0-.82842712.67157288-1.5 1.5-1.5zm3.5 16h2zm0-14h2z"></path></g></svg>{{ this.viewClaimData.serial_number ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Device Type</td>
                                                    <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"> <svg id="fi_12960027" height="22" width="22" enable-background="new 0 0 512 512" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><g><path d="m497 382h-35v-56c0-8.284-6.716-15-15-15h-176v-41.828c67.407-7.485 120-64.802 120-134.172 0-74.439-60.561-135-135-135s-135 60.561-135 135c0 69.37 52.593 126.687 120 134.172v41.828h-176c-8.284 0-15 6.716-15 15v56h-35c-8.284 0-15 6.716-15 15v100c0 8.284 6.716 15 15 15h100c8.284 0 15-6.716 15-15v-100c0-8.284-6.716-15-15-15h-35v-41h161v41h-35c-8.284 0-15 6.716-15 15v100c0 8.284 6.716 15 15 15h100c8.284 0 15-6.716 15-15v-100c0-8.284-6.716-15-15-15h-35v-41h161v41h-35c-8.284 0-15 6.716-15 15v100c0 8.284 6.716 15 15 15h100c8.284 0 15-6.716 15-15v-100c0-8.284-6.716-15-15-15zm-261-252v-20c0-11.028 8.972-20 20-20s20 8.972 20 20v20c0 11.028-8.972 20-20 20s-20-8.972-20-20zm20 50c27.025 0 49.408 19.188 54.093 44.966-15.808 9.54-34.321 15.034-54.093 15.034s-38.285-5.494-54.093-15.034c4.685-25.778 27.068-44.966 54.093-44.966zm-105-45c0-57.897 47.103-105 105-105s105 47.103 105 105c0 26.353-9.759 50.468-25.851 68.924-4.393-11.2-11.174-21.425-20.05-30.014-5.765-5.579-12.193-10.251-19.099-13.942 6.277-8.357 10-18.736 10-29.968v-20c0-27.57-22.43-50-50-50s-50 22.43-50 50v20c0 11.232 3.723 21.611 10 29.968-6.906 3.691-13.333 8.363-19.099 13.942-8.876 8.589-15.657 18.815-20.05 30.014-16.092-18.456-25.851-42.571-25.851-68.924zm-51 347h-70v-70h70zm191 0h-70v-70h70zm191 0h-70v-70h70z"></path></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>{{ this.viewClaimData.device_type == 1 ? 'Personal' : (this.viewClaimData.org_type == 1 ? 'Educational' : 'Organizational') }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Claim Reason</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.claim_reason_name ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Claim Details</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.claim_details ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Zoho Claim Status</td>
                                                    <td v-if="this.viewClaimData.claim_status"><span class="result_detail_font">{{ getClaimStatus(this.viewClaimData.claim_status) ?? '' }}</span></td>
                                                    <td v-else>-</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">User Claim Status</td>
                                                    <td v-if="this.viewClaimData.user_claim_status"><span class="result_detail_font">{{ getUserClaimStatus(this.viewClaimData.user_claim_status) ?? '' }}</span></td>
                                                    <td v-else>-</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Status Updated Date</td>
                                                    <td v-if="this.viewClaimData.status_updated_date"><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg id="fi_10208662" enable-background="new 0 0 32 32" height="22" viewBox="0 0 32 32" width="22" xmlns="http://www.w3.org/2000/svg"><path d="m20.2929688 16.0913086-5.6665039 5.6665039-2.9194336-2.9194336c-.390625-.390625-1.0234375-.390625-1.4140625 0s-.390625 1.0234375 0 1.4140625l3.6264648 3.6264648c.1953125.1953125.4511719.2929688.7070312.2929688s.5117188-.0976562.7070312-.2929688l6.3735352-6.3735352c.390625-.390625.390625-1.0234375 0-1.4140625s-1.0234374-.3906249-1.4140624.0000001z"></path><path d="m25 3.9599609h-1.5v-.9599609c0-.5522461-.4477539-1-1-1s-1 .4477539-1 1v.9599609h-11v-.9599609c0-.5522461-.4477539-1-1-1s-1 .4477539-1 1v.9599609h-1.5c-2.7568359 0-5 2.2431641-5 5v16.0400391c0 2.7568359 2.2431641 5 5 5h18c2.7568359 0 5-2.2431641 5-5v-16.0400391c0-2.7568359-2.2431641-5-5-5zm-21 5c0-1.6542969 1.3457031-3 3-3h1.5v.9594727c0 .5522461.4477539 1 1 1s1-.4477539 1-1v-.9594727h11v.9594727c0 .5522461.4477539 1 1 1s1-.4477539 1-1v-.9594727h1.5c1.6542969 0 3 1.3457031 3 3v1.0097656h-24zm21 19.0400391h-18c-1.6542969 0-3-1.3457031-3-3v-13.0302734h24v13.0302734c0 1.6542969-1.3457031 3-3 3z"></path></svg>{{ formatDate(this.viewClaimData.status_updated_date) ?? '' }}</span></td>
                                                    <td v-else>-</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Claim Opened Date</td>
                                                    <td v-if="this.viewClaimData.created_at"><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg height="22" id="fi_18895680" width="22" enable-background="new 0 0 32 32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m22.5917969 28.7714844 5.6523438-5.6523438c.5664062-.5668945.8789062-1.3198242.8789062-2.1210938s-.3125-1.5541992-.8789062-2.1201172c-1.1328125-1.1328125-3.1083984-1.1328125-4.2412109 0l-5.6523438 5.6523438c-.1230469.1230469-.2119141.2758789-.2578125.4438477l-.8369141 3.0693359c-.1396484.5126953.0068359 1.0649414.3828125 1.4404297.28125.2817383.6621094.4345703 1.0507812.4345703.1298828 0 .2607422-.0170898.3886719-.0522461l3.0693359-.8369141c.1679688-.0458984.3212891-.1347656.444336-.2578124zm-3.1689453-1.0722656.5292969-1.9423828 5.4648438-5.4643555c.3769531-.3779297 1.0361328-.3789062 1.4130859 0 .1894531.1884766.2929688.4389648.2929688.7055664s-.1035156.5175781-.2929688.706543l-5.4648438 5.4648438z"></path><path d="m9.8769531 30h4.3603516c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-4.3603516c-2.7568359 0-5-2.2431641-5-5v-12.5800781c0-2.4144287 1.7207031-4.4342651 4-4.8989258v.3154297c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-.4165039h4v.4165039c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-.4165039h4v.4165039c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-.3154297c2.2792969.4646606 4 2.4844971 4 4.8989258v4.8398438c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-4.8398438c0-3.5194702-2.6137695-6.432251-6-6.920105v-.4998169c0-.5522461-.4472656-1-1-1s-1 .4477539-1 1v.4199219h-4v-.4199219c0-.5522461-.4472656-1-1-1s-1 .4477539-1 1v.4199219h-4v-.4199219c0-.5522461-.4472656-1-1-1s-1 .4477539-1 1v.4998169c-3.3862305.487854-6 3.4006348-6 6.920105v12.5800781c0 3.8598633 3.140625 7 7 7z"></path><path d="m25.8769531 10.5600586c0-.5522461-.4472656-1-1-1h-18c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h18c.5527344 0 1-.4477539 1-1z"></path></svg>{{ formatDate(this.viewClaimData.created_at) ?? '' }}</span></td>
                                                    <td v-else>-</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Device Registreation Date</td>
                                                    <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg clip-rule="evenodd" fill-rule="evenodd" height="22" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_8740776"><g id="Icon"><path d="m10.5 20.25h-6.5c-.696 0-1.25-.594-1.25-1.312v-13.375c0-.719.554-1.313 1.25-1.313h16c.696 0 1.25.594 1.25 1.313 0-.001 0 6.437 0 6.437 0 .414.336.75.75.75s.75-.336.75-.75v-6.437c0-1.559-1.238-2.813-2.75-2.813h-16c-1.512 0-2.75 1.254-2.75 2.813v13.375c0 1.558 1.238 2.812 2.75 2.812h6.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"></path><path d="m4.75 2v3c0 .414.336.75.75.75s.75-.336.75-.75v-3c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path><path d="m17.75 2v3c0 .414.336.75.75.75s.75-.336.75-.75v-3c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path><path d="m11.25 2v3c0 .414.336.75.75.75s.75-.336.75-.75v-3c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path><path d="m2 9.75h20c.414 0 .75-.336.75-.75s-.336-.75-.75-.75h-20c-.414 0-.75.336-.75.75s.336.75.75.75z"></path><path d="m17.25 11.75c-3.036 0-5.5 2.464-5.5 5.5s2.464 5.5 5.5 5.5 5.5-2.464 5.5-5.5-2.464-5.5-5.5-5.5zm0 1.5c2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4 1.792-4 4-4z"></path><path d="m16.5 15v2c0 .175.062.345.174.48l1.25 1.5c.265.318.738.361 1.056.096s.361-.738.096-1.056l-1.076-1.292v-1.728c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path></g></svg>{{ formatDate(this.viewClaimData.device_created_at) ?? '' }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-body py-0">
                                        <h3 class="list-header  pb-1 def_16_size"> Shipping Address </h3>
                                        <table class="table table-bordered def_14_size">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">User Name</td>
                                                    <td><span class="result_detail_font"> {{ (viewClaimData.first_name && viewClaimData.last_name) ? (viewClaimData.first_name + ' ' + viewClaimData.last_name) : '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Email</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.email ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Phone</td>
                                                    <td><span class="result_detail_font">{{ formatPhoneNumber(this.viewClaimData.phone) ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Street Address</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.street_address ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Address line 2</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.address_line_2 ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">City</td>
                                                    <td v-if="this.viewClaimData.city"><span class="result_detail_font">{{ getCityName(this.viewClaimData.city) ?? '' }}</span></td>
                                                    <td v-else>-</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">State</td>
                                                    <td v-if="this.viewClaimData.state"><span class="result_detail_font">{{ getStateName(this.viewClaimData.state) ?? '' }}</span></td>
                                                    <td v-else>-</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">ZipCode</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.zip ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Country</td>
                                                    <td v-if="this.viewClaimData.country"><span class="result_detail_font">{{ getCountryName(this.viewClaimData.country) ?? '' }}</span></td>
                                                    <td v-else>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-body py-0">
                                        <h3 class="list-header  pb-1 def_16_size"> Owner Info </h3>
                                        <table class="table table-bordered def_14_size">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Name</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.user_name ?? '-' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Email</td>
                                                    <td>
                                                        <div class="d-flex align-items-center justify-content-between w-100">
                                                            <span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg id="fi_11502423" enable-background="new 0 0 512 512" height="22" viewBox="0 0 512 512" width="22" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m462.88 337.781c0 43.236-35.17 78.351-78.351 78.351h-257.057c-43.181 0-78.352-35.116-78.352-78.351v-163.562c0-14.43 3.951-27.983 10.809-39.615l125.428 125.428c18.765 18.82 43.894 29.19 70.67 29.19 26.721 0 51.85-10.37 70.615-29.19l125.428-125.428c6.859 11.632 10.809 25.184 10.809 39.615v163.562zm-78.352-241.913h-257.056c-17.832 0-34.293 6.035-47.461 16.076l126.69 126.745c13.114 13.058 30.616 20.301 49.326 20.301 18.655 0 36.158-7.243 49.271-20.301l126.69-126.745c-13.167-10.041-29.627-16.076-47.46-16.076zm0-30.232h-257.056c-59.861 0-108.584 48.723-108.584 108.584v163.562c0 59.916 48.723 108.584 108.584 108.584h257.056c59.861 0 108.584-48.668 108.584-108.584v-163.563c0-59.861-48.723-108.583-108.584-108.583z" fill-rule="evenodd"></path></svg>{{ this.viewClaimData.user_email ?? '-' }}
                                                                </span>
                                                                <button type="button" @click="switchUser(this.viewClaimData.user_id)" class="slidebarswitch rounded-pill p-1 pe-3 d-flex align-items-center  def_12_size gap-1  btn"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"  x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><circle r="256" cx="256" cy="256" fill="#dcdcdc" shape="circle"/><g transform="matrix(0.6000000000000003,0,0,0.6000000000000003,102.39999999999992,102.39999999999992)"><path d="M171.497 125.841C203.173 144.033 224 178.072 224 216.5V240H15v-23.5c0-39.106 21.491-73.197 53.3-91.116" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><circle cx="120" cy="80" r="65" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="M443.833 381.461a104.866 104.866 0 0 1 22.557 17.149C485.3 417.52 497 443.64 497 472.5V497H288v-24.5c0-38.393 20.712-71.955 51.575-90.124" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><circle cx="392" cy="336" r="64" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="M256 112h106c16.569 0 30 13.431 30 30v58" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="m360 184 32 32 32-32M256 400H150c-16.569 0-30-13.431-30-30v-66" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="m152 328-32-32-32 32" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/></g></svg> Switch User </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Phone</td>
                                                    <td><span class="result_detail_font">{{ formatPhoneNumber(this.viewClaimData.user_phone) ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Organization</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.org_name ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Sub Organization</td>
                                                    <td><span class="result_detail_font">{{ this.viewClaimData.sub_org_name ?? '' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">User Registration Date</td>
                                                    <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg clip-rule="evenodd" fill-rule="evenodd" height="22" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_8740776"><g id="Icon"><path d="m10.5 20.25h-6.5c-.696 0-1.25-.594-1.25-1.312v-13.375c0-.719.554-1.313 1.25-1.313h16c.696 0 1.25.594 1.25 1.313 0-.001 0 6.437 0 6.437 0 .414.336.75.75.75s.75-.336.75-.75v-6.437c0-1.559-1.238-2.813-2.75-2.813h-16c-1.512 0-2.75 1.254-2.75 2.813v13.375c0 1.558 1.238 2.812 2.75 2.812h6.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"></path><path d="m4.75 2v3c0 .414.336.75.75.75s.75-.336.75-.75v-3c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path><path d="m17.75 2v3c0 .414.336.75.75.75s.75-.336.75-.75v-3c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path><path d="m11.25 2v3c0 .414.336.75.75.75s.75-.336.75-.75v-3c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path><path d="m2 9.75h20c.414 0 .75-.336.75-.75s-.336-.75-.75-.75h-20c-.414 0-.75.336-.75.75s.336.75.75.75z"></path><path d="m17.25 11.75c-3.036 0-5.5 2.464-5.5 5.5s2.464 5.5 5.5 5.5 5.5-2.464 5.5-5.5-2.464-5.5-5.5-5.5zm0 1.5c2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4 1.792-4 4-4z"></path><path d="m16.5 15v2c0 .175.062.345.174.48l1.25 1.5c.265.318.738.361 1.056.096s.361-.738.096-1.056l-1.076-1.292v-1.728c0-.414-.336-.75-.75-.75s-.75.336-.75.75z"></path></g></svg>{{ formatDate(this.viewClaimData.user_created_at) ?? '' }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-body py-0">
                                        <h3 class="list-header  d-flex justify-content-between align-items-center  pb-1 def_16_size"> More Info  <button type="button" @click="viewClaimInfo(this.viewClaimData.zoho_claim_id)"><img :src="viewIc" width="30" height="30"></button></h3>

                                        <table v-if="isViewZohoData" class="table table-bordered def_14_size">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Repair Technician</td>
                                                    <td><span class="result_detail_font">{{ this.moreInfo.repairTechnician ?? '-' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Parts Used</td>
                                                    <td>
                                                        <!-- <span class="result_detail_font">{{ this.moreInfo.usedParts ?? '-' }}</span> -->
                                                    <span class="result_detail_font">
                                                        {{ moreInfo.usedParts?.filter(p => p).join(', ') || '-' }}
                                                    </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-body py-0">
                                        <h3 class="list-header d-flex justify-content-between align-items-center  pb-1 def_16_size"> Claim Notes <button type="button" v-if="!isEditingClaimNote" @click.stop="updateClaimNote()"><img :src="editIc" width="30" height="30"></button></h3>

                                        <table class="table table-bordered def_14_size mb-1">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold first_td_clss">Claim Note</td>
                                                    <td>
                                                        <span v-if="!isEditingClaimNote" class="result_detail_font">{{ this.viewClaimData.claim_notes ?? '-' }}</span>
                                                        <textarea v-else v-model="updatedClaimNote" class="form-control" rows="3"></textarea>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-if="isEditingClaimNote" class="text-end">
                                            <button v-if="isEditingClaimNote" type="button" class="btn bg_blue text-white def_16_size" @click="saveUpdatedClaimNote(this.viewClaimData.id)">Save</button>
                                        </div>
                                    </div>
                                    <!-- <div class="card-body py-0">
                                        <h3 class="list-header  pb-1 def_16_size"> Transaction Info </h3>
                                        <table class="table table-bordered def_14_size">
                                            <tbody>
                                                <tr>
                                                <td class="fw-bold first_td_clss">Transaction ID</td>
                                                <td><span class="result_detail_font">{{ this.viewClaimData.transaction_stripe_transaction_id ?? '-'}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Amount</td>
                                                <td><span class="result_detail_font">${{ this.viewClaimData.transaction_amount ?? '0'}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Status</td>
                                                <td><span class="result_detail_font">{{ this.viewClaimData.transaction_status ?? '-'}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Date</td>
                                                <td><span class="result_detail_font">{{formatDate(this.viewClaimData.transaction_created_at) ?? '-' }}</span></td>
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
        </div>
    </div>
</template>
<script>
    export default {

        props: {
            claimsdata:{
                type: Object,
            },
            claimreasons: {
                type: Object,
            },
            paginationdata: {
                type: Object,
            },
            totalclaimrequests: {
                type: Number,
            }
        },

        data() {
            return {
                /* Icons for table */
                deleteIc: this.$deleteIcon,
                editIc: this.$editIcIcon,
                viewIc: this.$viewIcIcon,
                searchIc: this.$searchIcIcon,
                profileIc: this.$profileIcIcon,

                /** to store the claim data */
                totalClaim: this.totalclaimrequests,
                claimData: this.claimsdata.data,
                paginationData: this.paginationdata,

                /** To store the claim detail data */
                viewClaimData: {},
                isShowViewAnimLoader: false,

                /**To manage the claim noer update */
                isEditingClaimNote : false,
                updatedClaimNote : '',

                /** To store the more details getting from the zoho */
                isViewZohoData : false,
                moreInfo: {},

                /* validation */
                validationErrors: {},

                /** Managing the claim status from custom.js */
                /*
                claimStatus: this.$claimStatus,
                adminClaimStatus: this.$claimRepairStatus,
                */
                adminClaimStatus: [],
                userClaimStatus: this.$userClaimRepairStatus,

                /* Filter */
                selectedStatus: 'all',
                selectedClaimReasons: 'all',
                selectedUserId: null,
                startDate: '',
                endDate: '',
                search: '',
                filterMessage:'',

                /** To manage the country, sate, and city*/
                countryData: this.$countries,
                statesData: this.$states,
                cityData: this.$cities,

                openPopup: false,
                claimId: null,
            };
        },

        mounted() {
            const urlParams = new URLSearchParams(window.location.search);
            this.openPopup = urlParams.get('openPopup');
            this.claimId = urlParams.get('claimId');
            const filterData = urlParams.get('filter');
            const querySelectedUser = urlParams.get('userId');

            if (this.openPopup == 'true' && this.claimId) {
                    this.viewClaim(this.claimId);
            }else {
                    this.openPopup = false;
                    this.claimId = null;
                    this.selectedUserId = null;
            }
            if (filterData == 'true' && querySelectedUser) {
                    this.selectedUserId = querySelectedUser;
                    this.getclaimData();
            }
        },

        created() {
            this.getClaimRepairStatus();
        },

        methods: {

            /** Formate Date with Time */
            formatDate(dateString) {
                return formatDateTimeAndTimeZone(dateString);
            },

            /* Close Filter Modal */
            closeFilterModal() {
                show_ajax_loader();
                hide_admin_popup('#claimFilterModalClose');
                this.selectedStatus = 'all';
                this.selectedClaimReasons = 'all';
                this.selectedUserId = null;
                this.startDate = '';
                this.endDate = '';
                this.search = '';
                this.filterMessage = '';
                this.validationErrors = {};
                this.getclaimData();
                hide_ajax_loader();
            },

            /* Reset Filter */
            resetFilter() {
                show_ajax_loader();
                this.closeFilterModal();
                hide_ajax_loader();
            },

            /* Filter Data */
            applyFilter() {
                /* validation */
                this.validationErrors = {};
                show_ajax_loader();
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

                let selectedStatusName = this.selectedStatus == 'all' ? 'all' : this.adminClaimStatus.find(cstatus => cstatus.id == this.selectedStatus)?.name;
                let startDate = this.filterFormatDate(this.startDate);
                let endDate = this.filterFormatDate(this.endDate);
                this.filterMessage = `Selected status: ${selectedStatusName}` + (this.startDate && this.endDate ? ` | Date range: ${startDate} to ${endDate}`: '');

                this.getclaimData();
                hide_admin_popup('#claimFilterModalClose');
                hide_ajax_loader();
            },

            /** To get the claim data by using paging */
            async getclaimData(page = 1) {
                show_ajax_loader();
                try {
                    let url = `${this.$userAppUrl}smarttiusadmin/track-claims?page=${page}`;
                    if (this.search) {
                        url += `&search=${this.search}`;
                    }
                    if (this.selectedStatus) {
                        url += `&selectedStatus=${this.selectedStatus}`;
                    }
                    if (this.selectedClaimReasons) {
                        url += `&selectedClaimReasons=${this.selectedClaimReasons}`;
                    }
                    if (this.startDate && this.endDate) {
                        url += `&startDate=${this.startDate}&endDate=${this.endDate}`;
                    }
                    if (this.selectedUserId) {
                        url += `&userId=${this.selectedUserId}`;
                    }
                    // Make a GET request to the API endpoint
                    const response = await axios.get(url);
                    if (response.data.success == true) {
                        if (response.data.claimsData && response.data.pagination) {
                            this.totalClaim = response.data.claimsData.total;
                            this.claimData = response.data.claimsData.data;
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
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                       /*  setTimeout(
                            () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                            3000
                        ); */
                    }
                }
            },

            /* on click of view button*/
            async viewClaim(id) {
                show_overlay();
                this.isShowViewAnimLoader = true;
                this.isEditingClaimNote = false;
                this.isViewZohoData = false;

                try {
                    let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/track-claims/view/${id}`);
                    if (response.data.success == true) {
                        this.viewClaimData = response.data.viewData;
                    }
                } catch (error) {
                    if (error && error.response && error.response.data && error.response.data.error) {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = error.response.data.error;
                        setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                    }
                } finally {
                    setTimeout(() => {
                        this.isShowViewAnimLoader = false;
                    }, 800); /* short delay for animation effect */
                }
            },

            /** Formate Date */
            filterFormatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            },

            /** To match the status from the array of status */
            getClaimStatus(claimStatusId) {
                /* Find the status object in the claimStatus array */
                const claimStatus = this.adminClaimStatus.find(status => status.id == claimStatusId)?.name;
                /* Return the status if found, otherwise return an empty string */
                return claimStatus ? claimStatus : '';
            },

            /** To match the status from the array of status(users) */
            getUserClaimStatus(claimStatusId) {
                /* Find the status object in the claimStatus array */
                const claimStatus = this.userClaimStatus.find(status => status.id == claimStatusId);
                /* Return the status if found, otherwise return an empty string */
                return claimStatus ? claimStatus.status : '';
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

            /** Export devices claim */
            async exportDevicesClaim() {
                show_ajax_loader();
                try{
                    /* let url = `${this.$userAppUrl}smarttiusadmin/track-claims/export`;
                    if (this.search && this.selectedStatus && this.selectedClaimReasons && this.startDate && this.endDate && this.selectedUserId) {
                        url += `?search=${this.search}&selectedStatus=${this.selectedStatus}&selectedClaimReasons=${this.selectedClaimReasons}&startDate=${this.startDate}&endDate=${this.endDate}&userId=${this.selectedUserId}`;
                    } else if (this.search && this.selectedStatus && this.selectedClaimReasons && this.selectedUserId) {
                        url += `?search=${this.search}&selectedStatus=${this.selectedStatus}&selectedClaimReasons=${this.selectedClaimReasons}&userId=${this.selectedUserId}`;
                    } else if (this.search && this.selectedStatus) {
                        url += `?search=${this.search}&selectedStatus=${this.selectedStatus}`;
                    } else if (this.search && this.selectedClaimReasons) {
                        url += `?search=${this.search}&selectedClaimReasons=${this.selectedClaimReasons}`;
                    } else if (this.selectedStatus && this.selectedClaimReasons) {
                        url += `?selectedStatus=${this.selectedStatus}&selectedClaimReasons=${this.selectedClaimReasons}`;
                    } else if (this.startDate && this.endDate) {
                        url += `?startDate=${this.startDate}&endDate=${this.endDate}`;
                    } else if (this.search) {
                        url += `?search=${this.search}`;
                    } else if (this.selectedStatus) {
                        url += `?selectedStatus=${this.selectedStatus}`;
                    } else if (this.selectedClaimReasons) {
                        url += `?selectedClaimReasons=${this.selectedClaimReasons}`;
                    } else if (this.startDate && this.endDate) {
                        url += `?startDate=${this.startDate}&endDate=${this.endDate}`;
                    } else if (this.selectedUserId) {
                        url += `?userId=${this.selectedUserId}`;
                    } else {
                        url += `?selectedStatus=all&selectedClaimReasons=all`;
                    } */

                    let baseUrl = `${this.$userAppUrl}smarttiusadmin/track-claims/export`;
                    const params = new URLSearchParams();

                    if (this.search) params.append('search', this.search);
                    if (this.selectedStatus) params.append('selectedStatus', this.selectedStatus);
                    if (this.selectedClaimReasons) params.append('selectedClaimReasons', this.selectedClaimReasons);
                    if (this.startDate) params.append('startDate', this.startDate);
                    if (this.endDate) params.append('endDate', this.endDate);
                    if (this.selectedUserId) params.append('userId', this.selectedUserId);

                    // Set defaults if no params provided
                    if ([...params].length == 0) {
                        params.append('selectedStatus', 'all');
                        params.append('selectedClaimReasons', 'all');
                    }

                    const url = `${baseUrl}?${params.toString()}`;


                    /* Make a GET request to the API endpoint
                    const response = await axios.get(url, { responseType: 'blob' }); */
                    const response = await axios.get(url);
                    if (response.data.success == false) {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = response.data.msg;
                    }else {
                        /* Create a blob from the response data */
                        const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.setAttribute('download', 'devices_claim_report.csv');
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

            /** To get the claim repair status */
            async getClaimRepairStatus(){
                let url = `${this.$userAppUrl}smarttiusadmin/get-claim-repair-status`;
                try {
                    const response = await axios.get(url);
                    if (response.data.success == true) {
                        if (response.data.claimRepairStatus) {
                            this.adminClaimStatus = response.data.claimRepairStatus;
                        } else {
                            /* Loader */
                        }
                    }
                }catch (error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            },

            /** Getting zoho details */
            async viewClaimInfo(zohoClaimID){
                if(!zohoClaimID){
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Zoho Claim ID not found.';
                    return;
                }
                show_ajax_loader();
                let url = `${this.$userAppUrl}smarttiusadmin/track-claims/claim-more-info/${zohoClaimID}`;
                try{
                    const response = await axios.get(url);
                    if (response.data.success == true) {
                        if (response.data.claimData) {
                            this.moreInfo = response.data.claimData;
                            this.isViewZohoData = true;
                            hide_ajax_loader();
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = 'Claim more info not found.';
                        }
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Claim more info not found.';
                    }
                } catch (error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = 'Something went wrong, please try again.';
                }
            },

            /** To update the claim note(Changing the HTML) */
            updateClaimNote(){
                this.updatedClaimNote = this.viewClaimData.claim_notes;
                this.isEditingClaimNote = true;
            },

            /** To save the updated claim note */
            async saveUpdatedClaimNote(claimID){
                show_ajax_loader();
                try{
                    let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/track-claims/update-claim-note/${claimID}`, {
                        claimNote: this.updatedClaimNote,
                    });

                    if (response.data.success == true) {
                        if (response.data) {
                            this.viewClaimData.claim_notes = this.updatedClaimNote;
                            this.isEditingClaimNote = false;
                            hide_ajax_loader();
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = 'Claim more info not found.';
                        }
                    }
                } catch (error) {
                    hide_ajax_loader();
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
        }
    }
</script>