<template>
    <div class="container-fluid mt_12">

        <!-- **** Device Filter Message ****-->
        <!-- <div v-if="filterMessage" class="alert alert-warning text-center w-full" role="alert"> {{ filterMessage }} </div> -->
        <!-- **** Device Filter Message End ****-->

        <!-- **** Device Filter Modal **** -->
        <div class="modal" id="myfilter">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content onewhitebg">

                    <!-- Modal Header -->
                    <div class="modal-header align-items-center">
                        <h4 class="modal-title def_18_size d-flex gap-10 mb-0 align-items-center">
                            <svg height="25" viewBox="-4 0 393 393.99003" width="25" xmlns="http://www.w3.org/2000/svg" id="fi_1159641"><path d="m368.3125 0h-351.261719c-6.195312-.0117188-11.875 3.449219-14.707031 8.960938-2.871094 5.585937-2.3671875 12.3125 1.300781 17.414062l128.6875 181.28125c.042969.0625.089844.121094.132813.183594 4.675781 6.3125 7.203125 13.957031 7.21875 21.816406v147.796875c-.027344 4.378906 1.691406 8.582031 4.777344 11.6875 3.085937 3.105469 7.28125 4.847656 11.65625 4.847656 2.226562 0 4.425781-.445312 6.480468-1.296875l72.3125-27.574218c6.480469-1.976563 10.78125-8.089844 10.78125-15.453126v-120.007812c.011719-7.855469 2.542969-15.503906 7.214844-21.816406.042969-.0625.089844-.121094.132812-.183594l128.683594-181.289062c3.667969-5.097657 4.171875-11.820313 1.300782-17.40625-2.832032-5.511719-8.511719-8.9726568-14.710938-8.960938zm-131.53125 195.992188c-7.1875 9.753906-11.074219 21.546874-11.097656 33.664062v117.578125l-66 25.164063v-142.742188c-.023438-12.117188-3.910156-23.910156-11.101563-33.664062l-124.933593-175.992188h338.070312zm0 0"></path></svg>
                            <span>Filters</span>
                        </h4>
                        <button type="button" class="btn-close" id="deviceFilterModalClose" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body onewhitebg">
                        <div class="container-fluid">
                            <label class="form-label" for="">Type Of Devices</label>
                            <select v-model="selectedBrands" class="form-control ">
                                <option value="all" selected>All</option>
                                <option v-for="(devicebrand, index) in devicebrands" :value="devicebrand.id" :key="index">
                                    {{ `${devicebrand.name} (${devicebrand.device_family_name}) ` }}
                                </option>
                            </select>
                        </div>

                        <div class="container-fluid  onewhitebg  ">
                            <h4 class="def_16_size mt-4 mb-1 list-header "> Purchase Date Range </h4>
                            <div class="row ">
                                <div class="col-12 col-md-6 col-lg-6 mt-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" :class="['form-control', { 'is-invalid': validationErrors.startDate },]" id="start_date" v-model="startDate" />
                                    <small v-if="validationErrors.startDate" class="text-red-500">{{ validationErrors.startDate }}</small>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6  mt-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" :class="['form-control  ', { 'is-invalid': validationErrors.endDate },]" id="end_date" v-model="endDate" />
                                    <small v-if="validationErrors.endDate" ><ErrorMessage :msg="validationErrors.endDate"></ErrorMessage></small>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-12 col-md-6 col-lg-6 mt-3">
                                    <label  class="form-label" for="Coverage">Coverage</label>
                                    <select v-model="deviceCoverage" class="form-control  ">
                                            <option value="all" selected>All</option>
                                            <option value="covered">Covered</option>
                                            <option value="uncovered">Uncovered</option>
                                    </select>

                                </div>
                                <div class="col-12  col-md-6 col-lg-6 mt-3">
                                    <label class="form-label" for="Search">Search</label>
                                    <!---- Search By Device Name, Serial Number, Model -->
                                    <input type="search" v-model="search" placeholder="Search..." class="form-control  ">
                                    <p class="def_12_size themetextcolor mt-1">Search by Device Name, Serial Number, Model</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button data-bs-dismiss="modal" type="button" @click="closeFilterModal" class="btn  py-2 customm_reset_btn def_14_size"> Cancel </button>
                        <button type="button" @click="resetFilter" class="btn  bg-warning float-left def_14_size"> Reset Filter </button>
                        <button type="button" @click="applyFilter" class="btn  bg_blue text-white def_14_size"> Filter </button>

                    </div>
                </div>
            </div>
        </div>
        <!-- **** Device Filter Modal End **** -->

        <!-- **** Device Details Listing ****-->
        <div class="card border-0">
            <div class="card-body rounded   onewhitebg ">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3  border-bottom pb-2">
                    <h4 class="coman_main_heading mb-0">Devices List</h4>
                    <div class="d-flex justify-content-end gap-2">
                        <a v-if="$authUser.role_for == 'is_subscriber' || $authUser.role_for == 'is_org_subscriber'" :href="$userAppUrl + 'sdcsmuser/early-renewal-devices'" class="btn bg_blue text-white def_14_size"> Early Renewal </a>
                        <a v-if="$authUser.role_for == 'is_subscriber' || $authUser.role_for == 'is_org_subscriber'" :href="$userAppUrl + 'sdcsmuser/renewal-devices'" class="btn bg_blue text-white def_14_size"> Renew Device </a>
                        <a data-bs-toggle="modal" data-bs-target="#myfilter" @click="showFilterModal" class="align-items-center btn text-decoration-none  justify-content-center rounded d-flex gap-10  border-0   wmax cursor bg_blue text-white">
                            <img :src="$imagePath + 'filter.svg'" width="16" height="16" />
                            <span class="text-uppercase def_14_size">Filter</span>
                        </a>
                        <button type="button" @click="resetFilter" class="btn btn-warning float-left def_14_size"> Reset Filter </button>
                    </div>
                </div>
                <div class="row  mt-1">
                    <div class="col-12  col-md-4 col-lg-4  mt-3">
                        <div class="card onewhitebg border-20 shadow  h-100 border-0">
                            <div class="card-body border-20  bg3">
                                <div class="media d-flex align-items-center gap-20">
                                    <div class="icon_box svg3">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 512 512"
                                            xml:space="preserve" class=""><g><circle cx="279.273" cy="349.091" r="11.636" fill="#6FA0B0" opacity="1" data-original="#000000"></circle><circle cx="81.455" cy="418.909" r="11.636" fill="#6FA0B0" opacity="1" data-original="#000000"></circle><path d="M477.091 46.545H81.455c-19.782 0-34.909 15.127-34.909 34.909v116.364c0 6.982 4.655 11.636 11.636 11.636s11.636-4.655 11.636-11.636V81.455c0-6.982 4.655-11.636 11.636-11.636H477.09c6.982 0 11.636 4.655 11.636 11.636v279.273c0 6.982-4.655 11.636-11.636 11.636H197.818c-6.982 0-11.636 4.655-11.636 11.636s4.655 11.636 11.636 11.636h23.273v34.909c0 6.982-4.655 11.636-11.636 11.636h-11.636c-6.982 0-11.636 4.655-11.636 11.636s4.655 11.636 11.636 11.636h162.909c6.982 0 11.636-4.655 11.636-11.636s-4.655-11.636-11.636-11.636h-11.636c-6.982 0-11.636-4.655-11.636-11.636v-34.909h139.636c19.782 0 34.909-15.127 34.909-34.909V81.455c-.001-19.782-15.128-34.91-34.91-34.91zM242.036 442.182c1.164-3.491 2.327-6.982 2.327-11.636v-34.909h69.818v34.909c0 4.655 1.164 8.145 2.327 11.636h-74.472z" fill="#6FA0B0" opacity="1" data-original="#000000"></path><path d="M453.818 302.545h-256c-6.982 0-11.636 4.655-11.636 11.636s4.655 11.636 11.636 11.636h256c6.982 0 11.636-4.655 11.636-11.636s-4.654-11.636-11.636-11.636zM128 232.727H34.909C15.127 232.727 0 247.855 0 267.636v162.909c0 19.782 15.127 34.909 34.909 34.909H128c19.782 0 34.909-15.127 34.909-34.909V267.636c0-19.781-15.127-34.909-34.909-34.909zm11.636 197.818c0 6.982-4.655 11.636-11.636 11.636H34.909c-6.982 0-11.636-4.655-11.636-11.636V267.636c0-6.982 4.655-11.636 11.636-11.636H128c6.982 0 11.636 4.655 11.636 11.636v162.909z" fill="#6FA0B0" opacity="1" data-original="#000000"></path></g>
                                        </svg>
                                    </div>
                                    <div class="media-body  d-flex flex-column align-items-center">
                                        <h6 class="media-title def_16_size color1 w-100">Devices | List Devices</h6>
                                        <h4 class="media-number def_20_size color1 w-100   mb-0">{{ formattedDigits(totaldevices) ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4 col-lg-4  mt-3">
                        <div class="card onewhitebg border-20 shadow  h-100 border-0">
                            <div class="card-body border-20 bg2">
                                <div class="media d-flex align-items-center gap-20">
                                    <div class="icon_box svg2">
                                        <svg id="fi_2415097" enable-background="new 0 0 50 50" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg"><g><path d="m21.5 43.4h-3.2c-.6 0-1 .4-1 1s.4 1 1 1h3.2c.6 0 1-.4 1-1s-.5-1-1-1z"></path><path d="m44.1 17.6c-2.6 0-4.7-2.1-4.7-4.7 0-.6-.4-1-1-1h-3.5c0-.1 0-8.1 0-7.9 0-1.7-1.3-3-3-3h-24.1c-1.7 0-3 1.3-3 3v42c0 1.7 1.3 3 3 3h24.2c1.7 0 3-1.3 3-3 0-.3 0-9.7 0-9.3 7-3.2 11.1-10.6 10.1-18.2-.1-.5-.5-.9-1-.9zm-14.6-5.7c-.3 0-.5.1-.7.3s-.3.4-.3.7c0 2.6-2.1 4.7-4.7 4.7-.4 0-.9.3-1 1-.9 7.6 3.2 14.9 10.2 18.1v3.2h-26.2v-30.8h26.2v2.8zm-21.7-8.9h24.2c.6 0 1 .4 1 1v3.1h-26.2v-3.1c0-.6.4-1 1-1zm24.2 44h-24.2c-.6 0-1-.4-1-1v-4.1h26.2v4.1c0 .6-.5 1-1 1zm2-12c-6-2.7-9.7-8.9-9.3-15.5 2.9-.4 5.3-2.7 5.7-5.6h7.1c.4 2.9 2.8 5.2 5.7 5.6.5 6.6-3.2 12.8-9.2 15.5z"></path><path d="m39.1 21.6c-.4-.4-1-.4-1.4 0l-4.7 4.7-2.7-2.7c-.4-.4-1-.4-1.4 0s-.4 1 0 1.4l3.4 3.4c.2.2.5.3.7.3s.5-.1.7-.3l5.4-5.4c.4-.4.4-1 0-1.4z"></path></g></svg>
                                    </div>
                                    <div class="media-body  d-flex flex-column align-items-center">
                                        <h6 class="media-title def_16_size color1 w-100">Covered Devices</h6>
                                        <h4 class="media-number def_20_size color1 w-100   mb-0">{{ formattedDigits(totalcovereddevices) ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4 col-lg-4  mt-3">
                        <div class="card onewhitebg border-20 shadow  h-100 border-0">
                            <div class="card-body border-20 bg4">
                                <div class="media d-flex align-items-center gap-20">
                                    <div class="icon_box svg4">
                                        <svg id="fi_5503231" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m364.969 0h-217.938a26.8 26.8 0 0 0 -26.769 26.77v458.46a26.8 26.8 0 0 0 26.769 26.77h217.938a26.8 26.8 0 0 0 26.769-26.77v-458.46a26.8 26.8 0 0 0 -26.769-26.77zm-166.014 16h114.09v10.721a5.686 5.686 0 0 1 -5.68 5.679h-102.731a5.685 5.685 0 0 1 -5.679-5.679zm176.783 469.23a10.781 10.781 0 0 1 -10.769 10.77h-217.938a10.781 10.781 0 0 1 -10.769-10.77v-458.46a10.781 10.781 0 0 1 10.769-10.77h35.924v10.721a21.7 21.7 0 0 0 21.679 21.679h102.731a21.7 21.7 0 0 0 21.68-21.679v-10.721h35.924a10.781 10.781 0 0 1 10.769 10.77zm-117.946-55.88a27.2 27.2 0 1 0 27.2 27.2 27.231 27.231 0 0 0 -27.2-27.2zm0 38.4a11.2 11.2 0 1 1 11.2-11.2 11.212 11.212 0 0 1 -11.2 11.2zm-63.311-273.269a87 87 0 1 0 123.037 0 87.1 87.1 0 0 0 -123.037 0zm111.724 111.719a70.967 70.967 0 1 1 0-100.408 71.079 71.079 0 0 1 0 100.408zm-19.33-69.766-19.561 19.566 19.561 19.562a8 8 0 1 1 -11.313 11.314l-19.562-19.562-19.562 19.562a8 8 0 0 1 -11.314-11.314l19.562-19.562-19.562-19.562a8 8 0 0 1 11.314-11.314l19.562 19.562 19.562-19.562a8 8 0 0 1 11.313 11.314z"></path></svg>
                                    </div>
                                    <div class="media-body  d-flex flex-column align-items-center">
                                        <h6 class="media-title def_16_size color1 w-100">Uncovered Devices</h6>
                                        <h4 class="media-number def_20_size color1 w-100   mb-0">{{ formattedDigits(totaluncovereddevices) ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered def_14_size table-hover table_custom">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" scope="col">#</th>
                                <th scope="col">Coverage</th>
                                <!-- <th scope="col">Name</th> -->
                                <th scope="col">Device</th>
                                <th scope="col"> Serial Number/Asset Tag </th>
                                <th id="second" scope="col">Device Type</th>
                                <th scope="col">Coverage Start Date</th>
                                <th scope="col">Coverage Expiration Date</th>
                                <th id="four" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="deviceData.length > 0" v-for="(data, index) in deviceData" :key="data.id">
                                <td class="ps-3">
                                  {{ (paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page - 1) * paginationData.per_page + index + 1 }}
                                </td>
                                <!-- <td v-if="new Date() <= new Date(data.expiration_date)">
                                    <span class="badge badge_covered text-white green_color px-3  rounded-pill">{{ 'Covered' }}</span>
                                </td> -->
                                <td v-if="isDateValid(data.expiration_date)">
                                    <span class="badge badge_covered text-white green_color px-3 rounded-pill">{{ 'Covered' }}</span>
                                </td>
                                <td v-else class="">
                                    <span class="badge  badge_uncovered orange_color px-3  rounded-pill">{{ 'Uncovered' }}</span>
                                </td>
                                <!--  <td>
                                    {{ data.device_title }}
                                </td> -->
                                <td>
                                    {{ data.device_model_name ?? '' }}
                                </td>
                                <td>
                                    {{ data.serial_number ?? '' }}
                                </td>
                                <td>
                                    {{ data.device_type == 1 ? 'Personal' : (data.org_type == 1 ? 'Educational' : 'Organizational') }}
                                </td>
                                <td class="text-nowrap">
                                    {{ formatDate(data.payment_added_date) ?? '' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ formatDate(data.expiration_date) ?? '' }}
                                </td>
                                <td id="bfour">
                                    <button type="button" @click="viewDevice(data.id)" class=" rounded-pill globalviewbtn extrabtns text-decoration-none open_notifSlid d-flex align-items-center def_14_size gap-1  action_toSlid fw-semibold themetextcolor"><img :src="viewIc" width="35" height="35"><span class="viewtext_Gl">View</span></button>
                                </td>
                            </tr>
                            <tr v-else>
                                <td  colspan="9">Device Not Found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <user-pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getdeviceData"></user-pagination>
            </div>
        </div>
        <!-- **** Device Details Listing End ****-->

    </div>
    <!-- <button class="open_notifSlid action_toSlid">open</button> -->

    <div :class="deviceId && openListSideBar ? 'dataSlide_bar_wrap  px-0 onopen_slide' : 'dataSlide_bar_wrap   px-0'">
        <div class="dataSlide_bar_inner  ">
            <div class="card onewhitebg border-0 rounded-0 dataSlide_bar_div ">
                <div class="card-header dataSlideh">
                    <div class="media py-1 d-flex align-items-center gap-10">
                        <div class="flex-grow-1"><span class=" def_18_size themetextcolor">Device Details</span></div>
                        <div class="close_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 487.619 487.619" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M243.878 0a243.81 243.81 0 1 0 243.741 243.739A244.016 244.016 0 0 0 243.878 0zm110.697 322.889a22.068 22.068 0 0 1 0 31.27c-4.203 4.12-9.852 6.427-15.738 6.427s-11.537-2.306-15.74-6.427l-79.219-79.148-79.147 79.148a22.21 22.21 0 0 1-31.272 0 22.054 22.054 0 0 1-6.494-15.635 22.07 22.07 0 0 1 6.494-15.635l79.148-79.357-79.148-79.079a22.137 22.137 0 0 1 .752-30.52 22.137 22.137 0 0 1 30.52-.752l79.147 79.079 79.08-79.079a22.348 22.348 0 0 1 31.48 0 22.072 22.072 0 0 1 0 31.271l-79.08 79.079z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0  onewhitebg dataSlide_bar_list  loaded_list">
                    <!-- <div class="d-flex px-3 align-items-center justify-content-end ">

                        <div class="d-flex align-items-center gap-10">
                            <a class="text-decoration-none" href="#"><img :src="$imagePath + 'about.svg'" width="25"
                                    height="25" /></a>
                            <a class="text-decoration-none" href="#"><img :src="$imagePath + 'edit.svg'" width="25"
                                    height="25" />
                            </a>
                        </div>
                    </div> -->
                    <div class="block_wraps">
                        <!-- Device Data -->
                        <transition name="fade">
                            <div v-if="!isShowViewAnimLoader && viewDeviceData">
                                <div class="card-body py-0">
                                    <h3 class="list-header def_16_size "> Device Information </h3>
                                    <table class="table table-bordered def_14_size">
                                        <tbody>
                                            <!-- <tr>
                                                <td class="fw-bold first_td_clss">Name</td>
                                                <td><span class="result_detail_font">{{
                                                    this.viewDeviceData.device_title ?? '' }}</span></td>
                                            </tr> -->

                                            <tr>
                                                <td class="fw-bold first_td_clss">Device</td>
                                                <td><span class="result_detail_font">{{
                                                    this.viewDeviceData.device_model_name ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Serial number / Asset tag</td>
                                                <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1 "><svg height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_12126082"><g fill="none" fill-rule="evenodd" stroke="#000" stroke-linecap="round" stroke-linejoin="round"><path d="m9 13.5h4l-.5 2 .5-2h2.5-2.5l1-4h1.5-1.5l.5-2-.5 2h-4l-1 4h-1.5 1.5l1-4h4l-1 4h-4l-.5 2zm1-4h-2.5 2.5l.5-2z"></path><path d="m7 2.5h9c.8284271 0 1.5.67157288 1.5 1.5v15c0 .8284271-.6715729 1.5-1.5 1.5h-9c-.82842712 0-1.5-.6715729-1.5-1.5v-15c0-.82842712.67157288-1.5 1.5-1.5zm3.5 16h2zm0-14h2z"></path></g></svg>{{ this.viewDeviceData.serial_number ?? '' }}</span></td>
                                            </tr>
                                             <tr>
                                                <td class="fw-bold first_td_clss">Device Type</td>
                                                <td>
                                                    <span class="result_detail_font"> {{ this.viewDeviceData.device_type == 1 ? 'Personal' : (this.viewDeviceData.org_type == 1 ? 'Educational' :'Organizational') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device Status</td>
                                                <td>
                                                    <span v-if="new Date() <= new Date(this.viewDeviceData.expiration_date)" class="badge wmax badge_covered text-white green_color px-3  rounded-pill"> Covered </span>
                                                    <span v-else class="badge wmax badge_uncovered orange_color px-3  rounded-pill"> Uncovered </span>
                                                </td>
                                            </tr>
                                            <tr v-if="$authUser.role_for != 'is_org_it_hod' && $authUser.role_for != 'is_org_it_director'">
                                                <td class="fw-bold first_td_clss">IMEI</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.imei ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="$authUser.role_for != 'is_org_it_hod' && $authUser.role_for != 'is_org_it_director'">
                                                <td class="fw-bold first_td_clss">Device Cellular Service</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.cellular_service == 1 ? 'Yes' : 'No' }}</span></td>
                                            </tr>
                                            <tr v-if="$authUser.role_for != 'is_org_it_hod' && $authUser.role_for != 'is_org_it_director'">
                                                <td class="fw-bold first_td_clss">Device Capacity</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.capacity ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="$authUser.role_for != 'is_org_it_hod' && $authUser.role_for != 'is_org_it_director'">
                                                <td class="fw-bold first_td_clss">Device Carrier</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.carrier ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="$authUser.role_for != 'is_org_it_hod' && $authUser.role_for != 'is_org_it_director'">
                                                <td class="fw-bold first_td_clss">Device Color</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.color ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device Registration Date</td>
                                                <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg fill="none" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_9307921"><g fill="rgb(0,0,0)"><path d="m8 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m16 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m8.5 14.5001c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.15.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m12 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m15.5 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.04-.05-.08-.1-.12-.15-.04-.06-.07-.12-.09-.18-.03-.06-.05-.12-.06-.18-.01-.07-.02-.14-.02-.2 0-.26.11-.52.29-.71.1-.09.2-.16.33-.21.37-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m8.5 17.9999c-.13 0-.26-.0299-.38-.0799s-.23-.1201-.33-.2101c-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .18.19.29.4499.29.7099s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m12 17.9999c-.26 0-.52-.11-.71-.29-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .09.09.16.1999.21.3299.05.12.08.25.08.38 0 .26-.11.52-.29.71-.19.18-.45.29-.71.29z"></path><path d="m15.5 17.9999c-.26 0-.52-.11-.71-.29-.09-.09-.16-.2-.21-.33-.05-.12-.08-.25-.08-.38s.03-.26.08-.38c.05-.13.12-.24.21-.33.23-.23.58-.34.9-.27.07.01.13.03.19.06.06.02.12.05.18.09.05.03.1.08.15.12.18.19.29.45.29.71s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m20.5 9.83984h-17c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h17c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m16 22.75h-8c-3.65 0-5.75-2.1-5.75-5.75v-8.5c0-3.65 2.1-5.75 5.75-5.75h8c3.65 0 5.75 2.1 5.75 5.75v8.5c0 3.65-2.1 5.75-5.75 5.75zm-8-18.5c-2.86 0-4.25 1.39-4.25 4.25v8.5c0 2.86 1.39 4.25 4.25 4.25h8c2.86 0 4.25-1.39 4.25-4.25v-8.5c0-2.86-1.39-4.25-4.25-4.25z"></path></g></svg>{{ formatDateTime(this.viewDeviceData.created_at) ?? '' }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-body py-0">
                                    <h3 class="list-header def_16_size"> User Information </h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">User Name</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.user_full_name ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">User Email</td>
                                                <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg id="fi_11502423" enable-background="new 0 0 512 512" height="22" viewBox="0 0 512 512" width="22" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m462.88 337.781c0 43.236-35.17 78.351-78.351 78.351h-257.057c-43.181 0-78.352-35.116-78.352-78.351v-163.562c0-14.43 3.951-27.983 10.809-39.615l125.428 125.428c18.765 18.82 43.894 29.19 70.67 29.19 26.721 0 51.85-10.37 70.615-29.19l125.428-125.428c6.859 11.632 10.809 25.184 10.809 39.615v163.562zm-78.352-241.913h-257.056c-17.832 0-34.293 6.035-47.461 16.076l126.69 126.745c13.114 13.058 30.616 20.301 49.326 20.301 18.655 0 36.158-7.243 49.271-20.301l126.69-126.745c-13.167-10.041-29.627-16.076-47.46-16.076zm0-30.232h-257.056c-59.861 0-108.584 48.723-108.584 108.584v163.562c0 59.916 48.723 108.584 108.584 108.584h257.056c59.861 0 108.584-48.668 108.584-108.584v-163.563c0-59.861-48.723-108.583-108.584-108.583z" fill-rule="evenodd"></path></svg>{{ this.viewDeviceData.user_email ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 1">
                                                <td class="fw-bold first_td_clss">Owner's First Name</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_user_first_name ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 1">
                                                <td class="fw-bold first_td_clss">Owner's Last Name</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_user_last_name ?? '' }}</span></td>
                                            </tr>

                                            <tr>
                                                <td class="fw-bold first_td_clss">User Registration Date</td>
                                                <td><span class=" result_detail_font diffrent_div d-flex align-items-center gap-1"><svg fill="none" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_9307921"><g fill="rgb(0,0,0)"><path d="m8 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m16 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m8.5 14.5001c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.15.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m12 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m15.5 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.04-.05-.08-.1-.12-.15-.04-.06-.07-.12-.09-.18-.03-.06-.05-.12-.06-.18-.01-.07-.02-.14-.02-.2 0-.26.11-.52.29-.71.1-.09.2-.16.33-.21.37-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m8.5 17.9999c-.13 0-.26-.0299-.38-.0799s-.23-.1201-.33-.2101c-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .18.19.29.4499.29.7099s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m12 17.9999c-.26 0-.52-.11-.71-.29-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .09.09.16.1999.21.3299.05.12.08.25.08.38 0 .26-.11.52-.29.71-.19.18-.45.29-.71.29z"></path><path d="m15.5 17.9999c-.26 0-.52-.11-.71-.29-.09-.09-.16-.2-.21-.33-.05-.12-.08-.25-.08-.38s.03-.26.08-.38c.05-.13.12-.24.21-.33.23-.23.58-.34.9-.27.07.01.13.03.19.06.06.02.12.05.18.09.05.03.1.08.15.12.18.19.29.45.29.71s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m20.5 9.83984h-17c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h17c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m16 22.75h-8c-3.65 0-5.75-2.1-5.75-5.75v-8.5c0-3.65 2.1-5.75 5.75-5.75h8c3.65 0 5.75 2.1 5.75 5.75v8.5c0 3.65-2.1 5.75-5.75 5.75zm-8-18.5c-2.86 0-4.25 1.39-4.25 4.25v8.5c0 2.86 1.39 4.25 4.25 4.25h8c2.86 0 4.25-1.39 4.25-4.25v-8.5c0-2.86-1.39-4.25-4.25-4.25z"></path></g></svg> {{ formatDateTime(this.viewDeviceData.user_created_at) ?? '' }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-if="this.viewDeviceData.device_type == 2" class="card-body py-0">
                                    <h3 class="list-header  def_16_size"> Organization Information </h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size">
                                        <tbody>
                                            <tr v-if="this.viewDeviceData.device_type == 2">
                                                <td class="fw-bold first_td_clss">Organization Name</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_name ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 2">
                                                <td class="fw-bold first_td_clss">Sub-Organization Name</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.sub_org_name ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 2 && ($authUser.role_for == 'is_org_it_hod' || $authUser.role_for == 'is_org_it_director')">
                                                <td class="fw-bold first_td_clss">{{ this.viewDeviceData.org_type == 1 ? "Parent's Name" : "Owner's Full Name" }}</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.device_owner_name ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 2">
                                                <td class="fw-bold first_td_clss">{{ this.viewDeviceData.org_type == 1 ? "Student's Name" : "Organization User Full Name" }}</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_user_full_name ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 2">
                                                <td class="fw-bold first_td_clss">{{ this.viewDeviceData.org_type == 1 ? "Student's Grade" : "User Designation" }}</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_user_designation ?? '' }}</span>
                                                </td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 2">
                                                <td class="fw-bold first_td_clss">Asset Tag </td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.asset_tag ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 2">
                                                <td class="fw-bold first_td_clss">{{ this.viewDeviceData.org_type == 1 ? "Student ID" : "Organization User ID" }}</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_user_id ?? '' }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-body py-0">
                                    <h3 class="list-header  def_16_size"> Plan Information</h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size">
                                        <tbody>
                                            <tr v-if="$authUser.role_for != 'is_org_it_hod' && $authUser.role_for != 'is_org_it_director'">
                                                <td class="fw-bold first_td_clss">Plan</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.plan_name ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Coverage Start Date</td>
                                                <td><span class="result_detail_font">{{ formatDate(this.viewDeviceData.payment_added_date) ?? ''}}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Coverage Expiration Date</td>
                                                <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg height="22" width="22" id="fi_10458899" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m212.91 106a9 9 0 0 0 -9 9v85.51a28.84 28.84 0 1 0 35.81 38 8.57 8.57 0 0 0 1.69.18l61 .6h.09a9 9 0 0 0 .08-17.92l-61-.6c-.26 0-.52 0-.77 0a28.93 28.93 0 0 0 -19-20.23v-85.51a9 9 0 0 0 -8.9-9.03zm0 132.79a10.92 10.92 0 1 1 10.92-10.91 10.93 10.93 0 0 1 -10.93 10.92z"></path><path d="m235.42 421.85c-4.49.52-9 .86-13.58 1.06v-45.53a9 9 0 1 0 -17.93 0v45.53c-100.41-4.61-181.25-85.5-185.77-185.91h45.54a9 9 0 1 0 0-17.93h-45.54c4.61-100.43 85.51-181.27 185.94-185.79v45.54a9 9 0 1 0 17.93 0v-45.54c100.41 4.61 181.25 85.51 185.77 185.94h-45.55a9 9 0 0 0 0 17.93h45.55c-.15 3.27-.38 6.53-.68 9.76a9 9 0 1 0 17.84 1.7c.65-6.77 1-13.67 1-20.51a213 213 0 0 0 -363.57-150.59 213 213 0 0 0 150.63 363.55 215.05 215.05 0 0 0 24.5-1.4 9 9 0 1 0 -2-17.81z"></path><path d="m143.76 90.31-8.39-14.53a9 9 0 1 0 -15.53 9l8.39 14.52a9 9 0 0 0 15.53-9z"></path><path d="m84.13 143.37-14.52-8.37a9 9 0 0 0 -9 15.53l14.53 8.39a9 9 0 0 0 9-15.53z"></path><path d="m75.17 297.3-14.53 8.39a9 9 0 1 0 9 15.52l14.52-8.38a9 9 0 0 0 -9-15.53z"></path><path d="m368.56 138.27a9 9 0 0 0 -12.25-3.27l-14.53 8.39a9 9 0 0 0 9 15.53l14.52-8.39a9 9 0 0 0 3.26-12.26z"></path><path d="m140.47 353.64a9 9 0 0 0 -12.24 3.29l-8.39 14.52a9 9 0 0 0 15.53 9l8.39-14.53a9 9 0 0 0 -3.29-12.28z"></path><path d="m289.92 103.76a9 9 0 0 0 7.77-4.49l8.38-14.52a9 9 0 1 0 -15.52-9l-8.39 14.53a9 9 0 0 0 7.76 13.45z"></path><path d="m508 452.32-98.58-172.08a29.75 29.75 0 0 0 -51.11-.87l-105 169.75a29.75 29.75 0 0 0 25 45.4l203.62 2.34h.35a29.75 29.75 0 0 0 25.72-44.54zm-15.61 20.78a11.6 11.6 0 0 1 -10.33 5.83l-203.62-2.33a11.83 11.83 0 0 1 -9.92-18.05l105-169.74a11.58 11.58 0 0 1 10-5.61h.22a11.58 11.58 0 0 1 10.06 5.95l98.59 172.08a11.71 11.71 0 0 1 0 11.87z"></path><path d="m379.92 337.32a9 9 0 0 0 -9 9v58.45a9 9 0 1 0 17.93 0v-58.49a9 9 0 0 0 -8.93-8.96z"></path><path d="m379.92 433.35a9 9 0 0 0 -9 9v1.16a9 9 0 0 0 17.93 0v-1.16a9 9 0 0 0 -8.93-9z"></path></svg>{{ formatDate(this.viewDeviceData.expiration_date) ?? ''}}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-body py-0" v-if="this.viewDeviceData.occurence && this.viewDeviceData.occurence == 1 && ($authUser.role_for == 'is_subscriber' || $authUser.role_for == 'is_org_subscriber')">
                                    <h3 class="list-header  def_16_size"> Transactions Information</h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction ID</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.transaction_id ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Amount</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.transaction_amount ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction status</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.transaction_status ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Date</td>
                                                <td><span class="result_detail_font">{{ formatDateTime(this.viewDeviceData.transaction_created_at) ?? ''}}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-body py-0" v-if="this.viewDeviceData.subscription_details && (this.viewDeviceData.occurence != 1 || this.viewDeviceData.occurence != null)">
                                    <h3 class="list-header  def_16_size"> Subscriptions Information</h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Subscription ID</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.subscription_details.id ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Amount</td>
                                                <td><span class="result_detail_font">${{ this.viewDeviceData.subscription_details.amount ?? '0' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Status</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.subscription_details.status ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Created Date</td>
                                                <td><span class="result_detail_font">{{ formatDateTime(this.viewDeviceData.subscription_details.created_at) ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Updated Date</td>
                                                <td><span class="result_detail_font">{{ formatDateTime(this.viewDeviceData.subscription_details.updated_at) ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Next Payment Date</td>
                                                <td><span class="result_detail_font">{{ formatDateTime(this.viewDeviceData.subscription_details.next_payment_date) ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Cancel Date</td>
                                                <td><span class="result_detail_font">{{ formatDateTime(this.viewDeviceData.subscription_details.ended_at) ?? '' }}</span></td>
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
    </div>
</template>
<script>
    export default {

        props: {
            devicedata: {
                type: Object,
            },
            devicebrands: {
                type: Object,
            },
            pagination: {
                type: Object,
            },
            totaldevices: {
                type: Number,
            },
            totalcovereddevices: {
                type: Number,
            },
            totaluncovereddevices: {
                type: Number,
            }
        },
        data() {
            return {
                /** Using the Icons from the JS File */
                deleteIc: this.$deleteIcon,
                editIc: this.$editIcIcon,
                viewIc: this.$viewIcIcon,
                searchIc: this.$searchIcIcon,
                profileIc: this.$profileIcIcon,

                /** To store the data */
                deviceData: this.devicedata.data,
                paginationData: this.pagination,
                search: '',
                viewDeviceData: {},
                isShowViewAnimLoader: false,
                validationErrors: {},
                deviceId: '',
                selectedBrands: 'all',
                deviceCoverage: 'all',
                startDate: '',
                endDate: '',
                filterMessage: '',
                openListSideBar: false,
                deviceId: null,
            };
        },

        mounted() {
            const urlParams = new URLSearchParams(window.location.search);
            const openPopup = urlParams.get('filter');
            const queryModel = urlParams.get('coverage');
            this.openListSideBar = urlParams.get('openPopup');
            this.deviceId = urlParams.get('deviceId');
            if (openPopup == 'true' && queryModel) {
                this.deviceCoverage = queryModel;
                this.getdeviceData(1);
            }
            if (this.openListSideBar == 'true' && this.deviceId) {
                this.viewDevice(this.deviceId);
            }
        },

        methods: {

            /** To check the deive is covered and uncovered */
            isDateValid(expirationDate) {
                const today = new Date();
                const todayDateOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());

                const expDate = new Date(expirationDate);
                const expDateOnly = new Date(expDate.getFullYear(), expDate.getMonth(), expDate.getDate());

                return todayDateOnly <= expDateOnly;
            },

            /** To get the device data by using paging */
            async getdeviceData(page = 1) {
                try {
                    show_ajax_loader();
                    let url = `${this.$userAppUrl}sdcsmuser/device-list?page=${page}`;
                    if (this.search) {
                        url += `&search=${this.search}`;
                    }
                    if (this.selectedBrands) {
                        url += `&brand=${this.selectedBrands}`;
                    }
                    if (this.deviceCoverage) {
                        url += `&coverage=${this.deviceCoverage}`;
                    }
                    if (this.startDate && this.endDate) {
                        url += `&startDate=${this.startDate}&endDate=${this.endDate}`;
                    }
                    /**
                    if (this.serach && this.selectedBrands && this.deviceCoverage && this.startDate && this.endDate) {
                        url += `&brand=${this.selectedBrands}&coverage=${this.deviceCoverage}&startDate=${this.startDate}&endDate=${this.endDate}`;
                    }
                    */
                    const response = await axios.get(url);
                    if (response.data.success == true) {
                        if (response.data.deviceData && response.data.pagination) {
                            this.deviceData = response.data.deviceData.data;
                            this.paginationData = response.data.pagination;
                            hide_ajax_loader();
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = "Something went wrong";
                        }
                    }else{
                        hide_ajax_loader();
                    }
                } catch (error) {
                    if (error && error.response && error.response.data && error.response.data.error) {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = error.response.data.error;
                        setTimeout(() => (location.href = `${this.$homeUrl}`), 5000); /* Small delay for smooth effect */
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, please try again.";
                    }
                }
            },

            /** Formate Date with Time */
            formatDateTime(dateString) {
                return formatDateTimeAndTimeZone(dateString);
            },

            formatDate(dateString) {
                return formatDateAndTimeZone(dateString);
            },

            /* on click view */
            async viewDevice(id) {
                show_overlay();
                this.isShowViewAnimLoader = true;
                try {
                    let response = await axios.get(`${this.$userAppUrl}sdcsmuser/device-list/view/${id}`);
                    if (response.data.success == true) {
                        this.viewDeviceData = response.data.viewData;
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

            /* Close Filter Modal */
            closeFilterModal() {
                this.selectedBrands = 'all';
                this.search = '';
                this.deviceCoverage = 'all';
                this.startDate = '';
                this.endDate = '';
                this.filterMessage = '';
                this.validationErrors = {};
                hide_user_popup('#deviceFilterModalClose');
                this.getdeviceData();
            },

            /* Filter Data */
            applyFilter() {
                show_ajax_loader();
                /* validation */
                this.validationErrors = {};
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
                hide_user_popup('#deviceFilterModalClose');
                // this.filterMessage = `Selected Device Type: ${this.selectedBrands}` + (this.startDate && this.endDate ? ` | Date range: ${this.startDate} to ${this.endDate}`: '');
                this.getdeviceData(1);
                hide_ajax_loader();
            },

            /* Reset Filter */
            resetFilter() {
                this.closeFilterModal();
                /* Remove ?filter=true&coverage=... from URL without reloading */
                const cleanUrl = window.location.origin + window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            },
            formattedDigits(number)
            {
                return formattedNumber(number);
            },
        },
    };
</script>
