<template>
    <div class="container-fluid mt_12">
        <!-- **** Repair Request Device Filter Modal **** -->

        <!-- **** Repair Request Device Filter Modal End **** -->
        <div class="modal" id="myfilter">
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
                        <div class="row">
                                <div class="col">
                                    <label for="Repair_Status" class="form-label">Repair Status</label>
                                    <select id="Repair_Status" v-model="selectedStatus" class="form-control">
                                        <option value="all" selected>All</option>
                                        <option v-for="(repairstatus, index) in repairStatus" :value="repairstatus.id" :key="index"> {{ repairstatus.status }}</option>
                                    </select>
                                </div>
                            </div>

                    <h3 class="def_16_size  mt-4  list-header"> Repair Request Date Range </h3>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" :class="['form-control', { 'is-invalid': validationErrors.startDate },]" id="start_date" v-model="startDate" />
                                <small v-if="validationErrors.startDate" class="text-red-500">{{ validationErrors.startDate }}</small>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mt-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" :class="['form-control', { 'is-invalid': validationErrors.endDate },]" id="end_date" v-model="endDate" />
                                <small v-if="validationErrors.endDate" class="text-red-500">{{ validationErrors.endDate }}</small>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col mt-2">
                                <label for="end_date" class="form-label">Search</label>
                                <input type="text" class="form-control" placeholder="Search..." v-model="search" />
                                <p><small>Search By Ticket ID, Device Serial Number</small></p>
                            </div>
                        </div>

                </div>
                    <!-- Modal footer -->
                    <div class="modal-footer onewhitebg">
                        <button data-bs-dismiss="modal" type="button" @click="closeFilterModal" class="btn customm_reset_btn def_14_size">
                            Cancel
                        </button>
                        <button type="button" @click="resetFilter" class="btn btn-warning float-left def_14_size">
                            Reset Filter
                        </button>
                        <button type="button" @click="applyFilter" class="btn bg_blue text-white def_14_size">
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- </div> -->
        <!-- **** Repair Request Filter Message ****-->
        <div v-if="filterMessage" class="alert warning_msg text-center w-full d-flex align-items-center gap-1" role="alert"> {{ filterMessage }} </div>
        <!-- **** Repair Request Filter Message End ****-->

        <!-- **** Repair Request Device Details Listing ****-->
        <div class="card onewhitebg border-0">
            <div class="card-body  overflow-hidden rounded onewhitebg ">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 border-bottom pb-2 mb-3">
                    <h4 class="coman_main_heading mb-0">Repairs <small style="font-size: 13px; color: #696969;"> ({{ formattedDigits(totalRepairs) }}) </small></h4>
                    <div class="d-flex justify-content-end gap-10 align-items-center">
                        <a data-bs-toggle="modal" data-bs-target="#myfilter" class="align-items-center text-decoration-none  justify-content-center rounded d-flex gap-10 border-0  btn   wmax cursor bg_blue text-white">
                            <img :src="$imagePath + 'filter.svg'" width="16" height="16" />
                            <span class="text-uppercase def_14_size">Filters</span>
                        </a>
                        <button type="button" @click="resetFilter" class="btn btn-warning float-left def_14_size"> Reset Filter </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered def_14_size table-hover table_custom">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" scope="col"> # </th>
                                <th scope="col"> Ticket ID </th>
                                <th scope="col"> Device </th>
                                <th scope="col"> Serial Number/Asset Tag </th>
                                <th scope="col"> Email </th>
                                <th scope="col" v-if="$authUser.role_for == 'is_org_it_hod'"> Organization </th>
                                <th scope="col"> Status </th>
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
                                <td class="text-nowrap">{{ repair.zoho_ticket_number ?? '' }}</td>
                                <td class="text-nowrap">{{ (repair?.device_name ?? '') + '(' + (repair?.device_family_name ?? '') + ')' }}</td>
                                <td>{{ repair.device_serial_number ?? '' }}</td>
                                <td class="text-nowrap">{{ repair.email ?? '' }}</td>
                                <td class="text-nowrap" v-if="$authUser.role_for == 'is_org_it_hod'">{{ repair.org_name ?? '' }}</td>
                                <td >{{ getRepairStatus(repair.user_repair_status)}}</td>
                                <td class="text-nowrap">{{ repair.repair_reason ?? '' }}</td>
                                <td class="text-nowrap">{{ formatDate(repair.created_at) ?? '' }}</td>
                                <td>
                                    <button type="button" @click="viewRepair(repair.id)" class=" rounded-pill globalviewbtn extrabtns text-decoration-none fw-semibold d-flex align-items-center themetextcolor def_14_size gap-1  open_notifSlid action_toSlid"><img :src="viewIc" width="35" height="35"> <span class="viewtext_Gl">View</span></button>
                                </td>
                            </tr>
                            <tr v-else>
                                <td  colspan="11">Repairs Not Found. </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <user-pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getrepairData"></user-pagination>
            </div>
        </div>
        <!-- **** Repair Request Device Details Listing End ****-->
    </div>

    <!-- **** Repair Request Device Details View ****-->
    <div :class="repairId && openPopup ? 'dataSlide_bar_wrap  px-0 onopen_slide' : 'dataSlide_bar_wrap   px-0'">
        <div class="dataSlide_bar_inner  ">
            <div class="card rounded-0 border-0 dataSlide_bar_div ">
                <div class="card-header dataSlideh">
                    <div class="media py-1 d-flex align-items-center gap-10">
                        <div class="flex-grow-1">
                            <span class="fontw600 def_18_size themetextcolor">
                                Repair Details
                            </span>
                        </div>
                        <div class="close_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 487.619 487.619" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M243.878 0a243.81 243.81 0 1 0 243.741 243.739A244.016 244.016 0 0 0 243.878 0zm110.697 322.889a22.068 22.068 0 0 1 0 31.27c-4.203 4.12-9.852 6.427-15.738 6.427s-11.537-2.306-15.74-6.427l-79.219-79.148-79.147 79.148a22.21 22.21 0 0 1-31.272 0 22.054 22.054 0 0 1-6.494-15.635 22.07 22.07 0 0 1 6.494-15.635l79.148-79.357-79.148-79.079a22.137 22.137 0 0 1 .752-30.52 22.137 22.137 0 0 1 30.52-.752l79.147 79.079 79.08-79.079a22.348 22.348 0 0 1 31.48 0 22.072 22.072 0 0 1 0 31.271l-79.08 79.079z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 onewhitebg dataSlide_bar_list  loaded_list">
                    <!-- <div class="d-flex  px-3 align-items-center justify-content-end">
                        <div class="d-flex align-items-center gap-10">
                            <a class="text-decoration-none" href="#"><img :src="$imagePath + 'about.svg'" width="25" height="25" /></a>
                            <a class="text-decoration-none" href="#"><img :src="$imagePath + 'edit.svg'" width="25" height="25" /></a>
                        </div>
                    </div> -->
                    <div class="block_wraps">
                        <!-- Repair Data -->
                        <transition name="fade">
                            <div class="card-body py-0" v-if="!isShowViewAnimLoader && viewRepairData">
                                <h3 class="list-header  def_20_size"> Info </h3>
                                <table class="table table-bordered def_14_size">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold first_td_clss">Ticket ID</td>
                                            <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg width="22" height="22" enable-background="new 0 0 64 64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" id="fi_12822507"><g id="directional"></g><g id="camera"></g><g id="train_00000178892782225402504130000015690401494868509316_"></g><g id="train"></g><g id="do_not_disturb_00000063623877084305965540000014065032412018789053_"></g><g id="do_not_disturb_00000034776432680539199770000007984210727708532380_"></g><g id="do_not_disturb_00000132775779256058570160000007725190755847483553_"></g><g id="do_not_disturb"></g><g id="cruise_ship"></g><g id="airport_check_in"></g><g id="passport_00000054265535315127765580000007410325881619203231_"></g><g id="vip_ticket_00000041986048335454509630000005438820234996332962_"></g><g id="ticket_price"></g><g id="cable_car"></g><g id="swimming_pool"></g><g id="arrival"></g><g id="departure"></g><g id="discount_coupon"></g><g id="ticket_booking"></g><g id="vip_ticket"></g><g id="support_ticket"></g><g id="ticket_sold_out"></g><g id="checked_ticket"><path d="m28.9 19.5c-6.9 0-12.4 5.6-12.4 12.4s5.6 12.4 12.4 12.4 12.4-5.5 12.4-12.3-5.5-12.5-12.4-12.5zm0 22.9c-5.8 0-10.4-4.7-10.4-10.4 0-5.8 4.7-10.4 10.4-10.4s10.4 4.6 10.4 10.4c0 5.7-4.6 10.4-10.4 10.4z"></path><path d="m33.2 28.2-5.5 5.5-3.1-3.1c-.4-.4-1-.4-1.4 0s-.4 1 0 1.4l3.8 3.8c.2.2.5.3.7.3s.5-.1.7-.3l6.2-6.2c.4-.4.4-1 0-1.4s-1-.4-1.4 0z"></path><path d="m61 26.9c.6 0 1-.4 1-1v-10.5c0-1.8-1.5-3.3-3.2-3.3h-53.6c-1.8 0-3.2 1.5-3.2 3.3v10.5c0 .6.4 1 1 1 2.8 0 5 2.3 5 5.1s-2.3 5.1-5 5.1c-.6 0-1 .4-1 1v10.5c0 1.8 1.5 3.3 3.2 3.3h53.5c1.8 0 3.2-1.5 3.2-3.3v-10.5c0-.6-.4-1-1-1-2.8 0-5-2.3-5-5.1.1-2.8 2.3-5.1 5.1-5.1zm-7 5.1c0 3.6 2.6 6.5 6 7v9.6c0 .7-.6 1.3-1.2 1.3h-9v-1.5c0-.6-.4-1-1-1s-1 .4-1 1v1.5h-42.6c-.7 0-1.2-.6-1.2-1.3v-9.6c3.4-.5 6-3.5 6-7 0-3.6-2.6-6.5-6-7v-9.6c0-.7.6-1.3 1.2-1.3h42.5v1.5c0 .6.4 1 1 1s1-.4 1-1v-1.5h9c.7 0 1.2.6 1.2 1.3v9.6c-3.3.5-5.9 3.4-5.9 7z"></path><path d="m48.8 28.7c-.6 0-1 .4-1 1v4.7c0 .6.4 1 1 1s1-.4 1-1v-4.7c0-.6-.5-1-1-1z"></path><path d="m48.8 19.3c-.6 0-1 .4-1 1v4.7c0 .6.4 1 1 1s1-.4 1-1v-4.7c0-.6-.5-1-1-1z"></path><path d="m48.8 38c-.6 0-1 .4-1 1v4.7c0 .6.4 1 1 1s1-.4 1-1v-4.7c0-.5-.5-1-1-1z"></path></g><g id="online_ticket_00000018237942094179105910000007941614640990071223_"></g><g id="online_ticket"></g><g id="ticket_00000132787386559086070130000003581786009904772786_"></g><g id="ticket"></g><g id="plane_ticket"></g><g id="worldwide_shipping"></g><g id="aviation_security_00000040569115728037331830000013852783306513737093_"></g><g id="aviation_security"></g><g id="flight_dispatcher_00000180350717560613288360000018278251513545641606_"></g><g id="chartered_flight"></g><g id="cancel_flight"></g><g id="compass_00000024707301156403386460000017384547609786364069_"></g><g id="compass_app"></g><g id="compass"></g><g id="flight_dispatcher"></g><g id="flight_delay"></g><g id="flight_schedule_00000022535532625805573330000006856744830413468315_"></g><g id="travel_website_00000047774854199427775640000015028895650002931351_"></g><g id="travel_website"></g><g id="flight_schedule"></g><g id="immigration_document"></g><g id="airplane_mode"></g><g id="passport_00000059290082564803097050000005369525408468146859_"></g><g id="passport"></g><g id="traveling"></g><g id="plane_trip"></g><g id="plane"></g></svg>{{ this.viewRepairData.zoho_ticket_number ?? '' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold first_td_clss">Repair Status</td>
                                            <td><span class="result_detail_font">{{ getRepairStatus(this.viewRepairData.user_repair_status) }}</span></td>
                                        </tr>
                                        <tr v-if="this.viewRepairData.org_name && $authUser.role_for == 'is_org_it_hod'">
                                            <td class="fw-bold first_td_clss">Organization Name</td>
                                            <td><span class="result_detail_font">{{ this.viewRepairData.org_name ?? '' }}</span></td>
                                        </tr>
                                        <tr v-if="this.viewRepairData.sub_org_name && $authUser.role_for == 'is_org_it_hod'">
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
                                            <td class="fw-bold first_td_clss">Device owner's name</td>
                                            <td><span class="result_detail_font">{{ (this.viewRepairData?.first_name ?? '') + ' ' +(this.viewRepairData?.last_name ?? '') }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold first_td_clss">Owners email</td>
                                            <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg id="fi_11502423" enable-background="new 0 0 512 512" height="22" viewBox="0 0 512 512" width="22" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m462.88 337.781c0 43.236-35.17 78.351-78.351 78.351h-257.057c-43.181 0-78.352-35.116-78.352-78.351v-163.562c0-14.43 3.951-27.983 10.809-39.615l125.428 125.428c18.765 18.82 43.894 29.19 70.67 29.19 26.721 0 51.85-10.37 70.615-29.19l125.428-125.428c6.859 11.632 10.809 25.184 10.809 39.615v163.562zm-78.352-241.913h-257.056c-17.832 0-34.293 6.035-47.461 16.076l126.69 126.745c13.114 13.058 30.616 20.301 49.326 20.301 18.655 0 36.158-7.243 49.271-20.301l126.69-126.745c-13.167-10.041-29.627-16.076-47.46-16.076zm0-30.232h-257.056c-59.861 0-108.584 48.723-108.584 108.584v163.562c0 59.916 48.723 108.584 108.584 108.584h257.056c59.861 0 108.584-48.668 108.584-108.584v-163.563c0-59.861-48.723-108.583-108.584-108.583z" fill-rule="evenodd"></path></svg>{{ this.viewRepairData.email ?? '' }}</span></td>
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
                                            <td><span class="result_detail_font">{{ this.viewRepairData.device_serial_number ?? '' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold first_td_clss">Street Address</td>
                                            <td><span class="result_detail_font">{{ this.viewRepairData.street_address ?? '' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold first_td_clss">Address line 2</td>
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
                                            <td class="fw-bold first_td_clss">Repair Request Date</td>
                                            <td><span class=" result_detail_font diffrent_div d-flex align-items-center gap-1"><svg fill="none" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_9307921"><g fill="rgb(0,0,0)"><path d="m8 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m16 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m8.5 14.5001c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.15.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m12 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m15.5 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.04-.05-.08-.1-.12-.15-.04-.06-.07-.12-.09-.18-.03-.06-.05-.12-.06-.18-.01-.07-.02-.14-.02-.2 0-.26.11-.52.29-.71.1-.09.2-.16.33-.21.37-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m8.5 17.9999c-.13 0-.26-.0299-.38-.0799s-.23-.1201-.33-.2101c-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .18.19.29.4499.29.7099s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m12 17.9999c-.26 0-.52-.11-.71-.29-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .09.09.16.1999.21.3299.05.12.08.25.08.38 0 .26-.11.52-.29.71-.19.18-.45.29-.71.29z"></path><path d="m15.5 17.9999c-.26 0-.52-.11-.71-.29-.09-.09-.16-.2-.21-.33-.05-.12-.08-.25-.08-.38s.03-.26.08-.38c.05-.13.12-.24.21-.33.23-.23.58-.34.9-.27.07.01.13.03.19.06.06.02.12.05.18.09.05.03.1.08.15.12.18.19.29.45.29.71s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m20.5 9.83984h-17c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h17c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m16 22.75h-8c-3.65 0-5.75-2.1-5.75-5.75v-8.5c0-3.65 2.1-5.75 5.75-5.75h8c3.65 0 5.75 2.1 5.75 5.75v8.5c0 3.65-2.1 5.75-5.75 5.75zm-8-18.5c-2.86 0-4.25 1.39-4.25 4.25v8.5c0 2.86 1.39 4.25 4.25 4.25h8c2.86 0 4.25-1.39 4.25-4.25v-8.5c0-2.86-1.39-4.25-4.25-4.25z"></path></g></svg>{{ formatDateTime(this.viewRepairData.created_at) ?? '-'}}</span></td>
                                        </tr>
                                            </tbody>
                                    </table>
                                    <h3 class="list-header border-bottom pb-1 def_20_size" v-if="$authUser.role_for == 'is_subscriber' || $authUser.role_for == 'is_org_subscriber'"> Transaction Info </h3>
                                    <table class="table table-bordered def_14_size" v-if="$authUser.role_for == 'is_subscriber' || $authUser.role_for == 'is_org_subscriber'">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Repair Request Fee</td>
                                                <td><span class="result_detail_font">${{ this.viewRepairData.repair_amount ?? '0'}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction ID</td>
                                                <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg id="fi_15575258" enable-background="new 0 0 512 512"  height="22" width="22" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="m79.79 292.38c0-2.76 2.24-5 5-5h224.39c2.76 0 5 2.24 5 5s-2.24 5-5 5h-224.39c-2.76 0-5-2.24-5-5zm153.32 85.3h-148.32c-2.76 0-5 2.24-5 5s2.24 5 5 5h148.32c2.76 0 5-2.24 5-5s-2.24-5-5-5zm-148.32-35.15h224.39c2.76 0 5-2.24 5-5s-2.24-5-5-5h-224.39c-2.76 0-5 2.24-5 5s2.24 5 5 5zm32.2-184.77c0-44.11 35.89-80 80-80s80 35.89 80 80-35.89 80-80 80-80-35.89-80-80zm10 0c0 38.6 31.4 70 70 70s70-31.4 70-70-31.4-70-70-70-70 31.4-70 70zm70 5c7.52 0 13.63 6.12 13.63 13.63s-6.12 13.63-13.63 13.63c-4.61 0-8.88-2.31-11.41-6.18-1.51-2.31-4.61-2.96-6.92-1.44-2.31 1.51-2.96 4.61-1.44 6.92 3.45 5.27 8.77 8.87 14.78 10.16v5.07c0 2.76 2.24 5 5 5s5-2.24 5-5v-5.07c10.64-2.3 18.63-11.79 18.63-23.1 0-13.03-10.6-23.63-23.63-23.63-7.52 0-13.63-6.12-13.63-13.63s6.12-13.63 13.63-13.63c4.61 0 8.88 2.31 11.41 6.18 1.51 2.31 4.61 2.96 6.92 1.44 2.31-1.51 2.96-4.61 1.44-6.92-3.46-5.27-8.77-8.87-14.78-10.16v-5.07c0-2.76-2.24-5-5-5s-5 2.24-5 5v5.07c-10.64 2.3-18.63 11.79-18.63 23.1-.01 13.03 10.6 23.63 23.63 23.63zm286.46 248.48c0 49.5-40.27 89.76-89.76 89.76-33.84 0-63.36-18.83-78.66-46.55h-259.53c-14.86 0-26.95-12.09-26.95-26.95v-336.65c0-1.33.53-2.6 1.46-3.54l74.85-74.85c.94-.94 2.21-1.46 3.54-1.46h230.07c14.86 0 26.95 12.09 26.95 26.95v288.09c8.89-2.96 18.39-4.56 28.26-4.56 49.5 0 89.77 40.26 89.77 89.76zm-437.83-325.39h40.84c9.34 0 16.95-7.6 16.95-16.95v-40.83zm264.67 358.6c-4.11-10.28-6.37-21.48-6.37-33.21 0-35.82 21.09-66.8 51.5-81.19v-292.1c0-9.34-7.6-16.95-16.95-16.95h-225.06v47.91c0 14.86-12.09 26.95-26.95 26.95h-47.91v331.64c0 9.34 7.6 16.95 16.95 16.95zm163.16-33.21c0-43.98-35.78-79.76-79.76-79.76s-79.76 35.78-79.76 79.76 35.78 79.76 79.76 79.76 79.76-35.78 79.76-79.76zm-26.6-31.9c5.38 7.34 3.79 17.69-3.55 23.07l-64.76 47.33c-2.88 2.08-6.32 3.17-9.8 3.17-1.09 0-2.17-.11-3.25-.32-4.54-.92-8.38-3.6-10.8-7.57l-19.27-31.4s0-.01-.01-.01c-4.74-7.77-2.27-17.95 5.49-22.69 3.76-2.29 8.2-2.99 12.48-1.95s7.91 3.68 10.2 7.44l9.89 16.12 50.3-36.76c7.34-5.36 17.7-3.77 23.08 3.57zm-8.07 5.91c-2.12-2.89-6.2-3.52-9.09-1.4l-54.68 39.96c-1.13.82-2.55 1.14-3.92.87s-2.56-1.1-3.29-2.29l-12.73-20.75c-.91-1.49-2.34-2.54-4.03-2.94-1.69-.41-3.43-.14-4.92.77-3.06 1.87-4.03 5.87-2.17 8.93l19.27 31.41c.96 1.57 2.46 2.63 4.24 2.99 1.82.37 3.67-.04 5.19-1.14l64.73-47.3c2.89-2.13 3.52-6.21 1.4-9.11z"></path></svg>{{ this.viewRepairData.transaction_id ?? '-'}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Status</td>
                                                <td><span class="result_detail_font">{{ this.viewRepairData.transaction_status ?? '-'}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Date</td>
                                                <td><span class="result_detail_font">{{formatDateTime(this.viewRepairData.transaction_created_at) ?? '-' }}</span></td>
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
    <!-- **** Repair Request Device Details View End ****-->
</template>
<script>
export default {
    props: {
        repairdata: {
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

            repairData: this.repairdata.data,
            paginationData: this.pagination,
            totalRepairs: this.pagination.total,
            viewRepairData: {},
            isShowViewAnimLoader: false,

            /** Managing the repair status from custom.js */
            // repairStatus: this.$repairStatus,
            repairStatus: this.$userClaimRepairStatus,
            /* Filter */
            selectedStatus: 'all',
            startDate: '',
            endDate: '',
            filterMessage:'',
            search:'',
            /* validation */
            validationErrors: {},
            /** To manage the country, sate, and city dropdown */
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
        if (this.openPopup == "true" && this.repairId) {
            this.viewRepair(this.repairId);

        }else {
            this.openPopup = false;
            this.repairId = null;
        }
    },
    methods: {

        /** To get the repair data by using paging */
        async getrepairData(page = 1) {
            try {
                    show_ajax_loader();
                   /*  let response = null;
                    if (status && startDate && endDate) {
                        response = await axios.get(`${this.$userAppUrl}sdcsmuser/user-track-repairs?page=${page}&selectedStatus=${status}&startDate=${startDate}&endDate=${endDate}`);
                    } else if (status) {
                        response = await axios.get(`${this.$userAppUrl}sdcsmuser/user-track-repairs?page=${page}&selectedStatus=${status}`);
                    } else if(startDate && endDate) {
                        response = await axios.get(`${this.$userAppUrl}sdcsmuser/user-track-repairs?page=${page}&startDate=${startDate}&endDate=${endDate}`);
                    } else {
                        response = await axios.get(`${this.$userAppUrl}sdcsmuser/user-track-repairs?page=${page}`);
                    } */

                    let url = `${this.$userAppUrl}sdcsmuser/user-track-repairs?page=${page}`;
                    if (this.search) {
                        url += `&search=${this.search}`;
                    }
                    if (this.selectedStatus) {
                        url += `&selectedStatus=${this.selectedStatus}`;
                    }
                    if (this.startDate && this.endDate) {
                        url += `&startDate=${this.startDate}&endDate=${this.endDate}`;
                    }
                    const response = await axios.get(url);
                    if (response.data.success == true) {
                        if (response.data.repairData && response.data.pagination) {
                            this.repairData = response.data.repairData.data;
                            this.paginationData = response.data.pagination;
                            this.totalRepairs = response.data.pagination.total;
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
                    setTimeout(() => (location.href = `${this.$homeUrl}`),5000); /* Small delay for smooth effect */
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
        /** Formate Date with Time */
        formatDateTime(dateString) {
            return formatDateTimeAndTimeZone(dateString);
        },

        /* on click of view button*/
        async viewRepair(id) {
            show_overlay();
            this.isShowViewAnimLoader = true;
            try {
                let response = await axios.get(`${this.$userAppUrl}sdcsmuser/user-repair-request/view/${id}`);
                if (response.data.success == true) {
                    this.viewRepairData = response.data.viewData;
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, please try again.";
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

        /** To match the status from the array of status */
        getRepairStatus(repairStatusId) {
            /* Find the status object in the repairStatus array */
            const repairStatus = this.repairStatus.find(status => status.id == repairStatusId);
            /* Return the status if found, otherwise return an empty string */
            return repairStatus ? repairStatus.status : '';
        },

        /* Close Filter Modal */
        closeFilterModal() {
            hide_user_popup('#repairFilterModalClose');
            this.selectedStatus = 'all';
            this.search = '';
            this.startDate = '';
            this.endDate = '';
            this.filterMessage = '';
            this.validationErrors = {};
            this.getrepairData();
        },

        /* Filter Data */
        applyFilter() {
            show_ajax_loader();
            this.validationErrors = {};
            /* validation */
            if (this.startDate && this.endDate) {
                if (new Date(this.startDate) > new Date(this.endDate)) {
                    this.validationErrors.endDate = "End date should be greater than start date.";
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
            let selectedStatusName = this.selectedStatus == 'all' ? 'all' : this.repairStatus.find((status) => status.id == this.selectedStatus)?.status;
            let startDate = this.formatDate(this.startDate);
            let endDate = this.formatDate(this.endDate);

            this.filterMessage = `Selected status: ${selectedStatusName}` + (this.startDate && this.endDate ? ` | Date range: ${startDate} to ${endDate}`: '');
            this.getrepairData();
            hide_user_popup('#repairFilterModalClose');
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
