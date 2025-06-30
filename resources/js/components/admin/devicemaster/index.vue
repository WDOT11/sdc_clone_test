<template>
    <!-- **** Device Filter Modal **** -->
    <div class="modal" id="myfilter">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content onewhitebg">

                <!-- Modal Header -->
                <div class="modal-header align-items-center">
                    <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                        <svg height="30" viewBox="-4 0 393 393.99003" width="30" xmlns="http://www.w3.org/2000/svg" id="fi_1159641"><path d="m368.3125 0h-351.261719c-6.195312-.0117188-11.875 3.449219-14.707031 8.960938-2.871094 5.585937-2.3671875 12.3125 1.300781 17.414062l128.6875 181.28125c.042969.0625.089844.121094.132813.183594 4.675781 6.3125 7.203125 13.957031 7.21875 21.816406v147.796875c-.027344 4.378906 1.691406 8.582031 4.777344 11.6875 3.085937 3.105469 7.28125 4.847656 11.65625 4.847656 2.226562 0 4.425781-.445312 6.480468-1.296875l72.3125-27.574218c6.480469-1.976563 10.78125-8.089844 10.78125-15.453126v-120.007812c.011719-7.855469 2.542969-15.503906 7.214844-21.816406.042969-.0625.089844-.121094.132812-.183594l128.683594-181.289062c3.667969-5.097657 4.171875-11.820313 1.300782-17.40625-2.832032-5.511719-8.511719-8.9726568-14.710938-8.960938zm-131.53125 195.992188c-7.1875 9.753906-11.074219 21.546874-11.097656 33.664062v117.578125l-66 25.164063v-142.742188c-.023438-12.117188-3.910156-23.910156-11.101563-33.664062l-124.933593-175.992188h338.070312zm0 0"></path></svg>
                        <span>Filters</span>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="deviceFilterModalClose"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body onewhitebg">
                    <div class="container-fluid   ">
                        <label class="form-label" for="">Type Of Devices</label>
                        <select v-model="selectedBrands" class="form-control ">
                            <option value="all" selected>All</option>
                            <!-- <option :value="repairStatus.map(status => status.id).join(',')" selected>All</option> -->
                            <option v-for="(devicebrand, index) in devicebrands" :value="devicebrand.id" :key="index">
                                {{ `${devicebrand.name} (${devicebrand.device_family_name}) ` }}</option>
                        </select>
                    </div>


                    <div class="container-fluid    py-3">
                        <h5 class="def_18_size my-3 list-header"> Purchase Date Range </h5>
                        <div class="row g-3 ">
                            <div class="col-12 col-md-6 col-lg-6 mt-2">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control  " id="start_date" v-model="startDate" />
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mt-2">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" :class="['form-control  ', { 'is-invalid': validationErrors.endDate },]" id="end_date" v-model="endDate" />
                                <small v-if="validationErrors.endDate" ><ErrorMessage :msg="validationErrors.endDate"></ErrorMessage></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-md-3 col-lg-3 mt-2">
                                <label  class="form-label" for="Coverage">Coverage</label>
                                <select v-model="deviceCoverage" class="form-control ">
                                    <option value="all" selected>All</option>
                                    <option value="covered">Covered</option>
                                    <option value="uncovered">Uncovered</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3 col-lg-3 mt-2">
                                <label  class="form-label" for="DeviceType">Device Type</label>
                                <select v-model="deviceType" class="form-control ">
                                    <option value="all" selected>All</option>
                                    <option value="1">Personal</option>
                                    <option value="2">Organization</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 mt-2">
                                <label class="form-label" for="Search">Search</label>
                                <!---- Search By Device Name, Serial Number, Model, User Email , Organization Name -->
                                <input type="search" v-model="search" placeholder="Search..." class="form-control  ">
                                <p class="def_12_size mt-1 themetextcolor">Search by Device Name, Serial Number, Model, User Email, Organization Name</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer onewhitebg">
                    <button type="button" @click="resetFilter" class="btn btn-warning float-left"> Reset Filter </button>
                    <button type="button" @click="applyFilter" class="btn btn-primary"> Filter </button>
                    <!-- <button data-bs-dismiss="modal" type="button" @click="closeFilterModal" class="btn btn-secondary"> Cancel </button> -->
                </div>
            </div>
        </div>
    </div>
    <!-- **** Device Filter Modal End **** -->

    <!-- Table to show the device listing -->
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0  ">
            <div class="card-body">
                <h4 class=" mb-3 border-bottom pb-2 coman_main_heading">Device <small style="font-size: 13px; color: #696969;"> ({{ formattedDigits(totalDevice) }}) </small></h4>

                <!-- Button and search section -->
                <div class="d-flex justify-content-between flex-wrap align-items-start gap-3">
                    <!-- Left Side Button -->
                    <a href="/smarttiusadmin/devices/create" class="btn blogal_pbtn_padding bg_blue def_14_size wmax text-white d-flex align-items-center gap-10  rounded">
                        <img :src="profileIc" width="22" height="22"> Add New
                    </a>
                    <div class="d-flex justify-content-end">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#myfilter"  @click="showFilterModal" class="align-items-center btn  blogal_pbtn_padding text-decoration-none  justify-content-center rounded d-flex gap-10  border-0   wmax cursor bg_blue text-white">
                            <img :src="$imagePath + 'filter.svg'" width="16" height="16" />
                            <span class="text-uppercase def_14_size">Filter</span>
                        </button>
                        <!-- Export Devices -->
                        <button class="btn btn-sm bg_blue text-white ml-2" @click="exportDevices">Export</button>
                        <button type="button" @click="resetFilter" class="btn blogal_pbtn_padding customm_reset_btn def_14_size mx-2"> Clear</button>
                    </div>
                </div>

                <!-- Table to show the Device records -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size table_custom">
                        <thead class="table-light">
                            <tr>
                                <th >#</th>
                                <th >Coverage</th>
                                <!-- <th>Title</th> -->
                                <th>Device</th>
                                <th>Serial Number/Asset Tag </th>
                                <th >Device Type</th>
                                <th>User Email</th>
                                <th>Coverage Start Date</th>
                                <th>Coverage Expiration Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="deviceData.length > 0" v-for="(data, index) in deviceData" :key="data.id">
                                <td>
                                    {{(paginationData.current_page < 1 ? paginationData.current_page : paginationData.current_page  - 1) * paginationData.per_page +  index + 1}}
                                </td>
                                <td v-if="isDateValid(data.expiration_date)">
                                    <span class="badge badge_covered text-white green_color px-3 rounded-pill">{{ 'Covered' }}</span>
                                </td>
                                <td v-else class="">
                                    <span class="badge  badge_uncovered orange_color  px-3  rounded-pill">{{ 'Uncovered' }}</span>
                                </td>
                                <!-- <td >
                                    {{ data.device_title }}
                                </td> -->
                                <td >
                                    {{ data.device_model_name ?? '' }}
                                </td>
                                <td >
                                    {{ data.serial_number ?? '' }}
                                </td>
                                <td>
                                    {{ data.device_type == 1 ? 'Personal' : (data.org_type == 1 ? 'Educational' : 'Organizational') }}
                                </td>
                                <td class="text-nowrap">
                                    {{ data.user_email ?? '' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ formatDate(data.payment_added_date) ?? '' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ formatDate(data.expiration_date) ?? '' }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-20">
                                    <!-- <a :href="`/smarttiusadmin/devices/view/${data.id}`" class=" bg-transparent text-white rounded">
                                        <img :src="viewIc" width="28" height="28">
                                    </a> -->
                                    <button type="button" @click="viewDevice(data.id)" class=" rounded-pill d-flex align-items-center globalviewbtn extrabtns  themetextcolor def_14_size gap-1 open_notifSlid action_toSlid">
                                        <img :src="viewIc" width="35" height="35"><span class="viewtext_Gl">View</span></button>
                                    <!-- <button  class="btn bg-transparent text-white rounded open_notifSlid action_toSlid"><img :src="viewIc" width="28" height="28"></button> -->
                                    <a :href="`/smarttiusadmin/devices/edit/${data.id}`" class=" rounded-pill themetextcolor globaleditbtn extrabtns d-flex def_14_size  align-items-center gap-1 text-decoration-none  rounded editBtn">
                                        <img :src="editIc" width="35" height="35">
                                        <span class="edittext_Gl">Edit</span>
                                    </a>
                                    <!-- <button @click="confirmDelete(data.id)" class=" bg-transparent text-white   rounded"><img :src="deleteIc" width="28" height="28"></button> -->
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="9">Device Not Found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="paginationData && paginationData.last_page > 1" :pagination="paginationData" :offset="5" :paginate="getdeviceData"></pagination>
            </div>
        </div>
    </div>

    <!-- Sidebar to show the device details -->
    <div :class="deviceId && openListSideBar ? 'dataSlide_bar_wrap   px-0 onopen_slide' : 'dataSlide_bar_wrap   px-0'">
        <div class="dataSlide_bar_inner  ">
            <div class="card rounded-0 border-0 dataSlide_bar_div ">
                <div class="card-header dataSlideh">
                    <div class="media py-1 d-flex align-items-center  gap-10">
                        <div class="flex-grow-1"><span class="d-flex gap-2"><span class=" def_18_size themetextcolor">Device Details</span>  <a v-if="!isShowViewAnimLoader && viewDeviceData" class="text-decoration-none" :href="`/smarttiusadmin/devices/edit/${this.viewDeviceData.id}`"><img :src="$imagePath + 'edit.svg'" width="25" height="25" /></a></span></div>
                        <div class="close_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 487.619 487.619" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M243.878 0a243.81 243.81 0 1 0 243.741 243.739A244.016 244.016 0 0 0 243.878 0zm110.697 322.889a22.068 22.068 0 0 1 0 31.27c-4.203 4.12-9.852 6.427-15.738 6.427s-11.537-2.306-15.74-6.427l-79.219-79.148-79.147 79.148a22.21 22.21 0 0 1-31.272 0 22.054 22.054 0 0 1-6.494-15.635 22.07 22.07 0 0 1 6.494-15.635l79.148-79.357-79.148-79.079a22.137 22.137 0 0 1 .752-30.52 22.137 22.137 0 0 1 30.52-.752l79.147 79.079 79.08-79.079a22.348 22.348 0 0 1 31.48 0 22.072 22.072 0 0 1 0 31.271l-79.08 79.079z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0  onewhitebg dataSlide_bar_list  loaded_list">
                    <!-- Device Data -->
                    <transition name="fade">
                        <div v-if="!isShowViewAnimLoader && viewDeviceData">

                            <div class="block_wraps">
                                <div class="card-body py-0">
                                    <h3 class="list-header  def_16_size"> Device Information </h3>
                                    <table class="table table-bordered def_14_size">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.device_model_name ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Serial number / Asset tag</td>
                                                <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1 "><svg height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_12126082"><g fill="none" fill-rule="evenodd" stroke="#000" stroke-linecap="round" stroke-linejoin="round"><path d="m9 13.5h4l-.5 2 .5-2h2.5-2.5l1-4h1.5-1.5l.5-2-.5 2h-4l-1 4h-1.5 1.5l1-4h4l-1 4h-4l-.5 2zm1-4h-2.5 2.5l.5-2z"></path><path d="m7 2.5h9c.8284271 0 1.5.67157288 1.5 1.5v15c0 .8284271-.6715729 1.5-1.5 1.5h-9c-.82842712 0-1.5-.6715729-1.5-1.5v-15c0-.82842712.67157288-1.5 1.5-1.5zm3.5 16h2zm0-14h2z"></path></g></svg>{{ this.viewDeviceData.serial_number ?? '' }}</span></td>
                                            </tr>
                                            <!--  <tr>
                                                <td class="fw-bold first_td_clss">Name</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.device_title ?? '' }}</span></td>
                                            </tr> -->
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device Type</td>
                                                <td>
                                                    <span class="fw-bold result_detail_font diffrent_div d-flex align-items-center gap-1"> <svg id="fi_12960027" height="22" width="22" enable-background="new 0 0 512 512" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><g><path d="m497 382h-35v-56c0-8.284-6.716-15-15-15h-176v-41.828c67.407-7.485 120-64.802 120-134.172 0-74.439-60.561-135-135-135s-135 60.561-135 135c0 69.37 52.593 126.687 120 134.172v41.828h-176c-8.284 0-15 6.716-15 15v56h-35c-8.284 0-15 6.716-15 15v100c0 8.284 6.716 15 15 15h100c8.284 0 15-6.716 15-15v-100c0-8.284-6.716-15-15-15h-35v-41h161v41h-35c-8.284 0-15 6.716-15 15v100c0 8.284 6.716 15 15 15h100c8.284 0 15-6.716 15-15v-100c0-8.284-6.716-15-15-15h-35v-41h161v41h-35c-8.284 0-15 6.716-15 15v100c0 8.284 6.716 15 15 15h100c8.284 0 15-6.716 15-15v-100c0-8.284-6.716-15-15-15zm-261-252v-20c0-11.028 8.972-20 20-20s20 8.972 20 20v20c0 11.028-8.972 20-20 20s-20-8.972-20-20zm20 50c27.025 0 49.408 19.188 54.093 44.966-15.808 9.54-34.321 15.034-54.093 15.034s-38.285-5.494-54.093-15.034c4.685-25.778 27.068-44.966 54.093-44.966zm-105-45c0-57.897 47.103-105 105-105s105 47.103 105 105c0 26.353-9.759 50.468-25.851 68.924-4.393-11.2-11.174-21.425-20.05-30.014-5.765-5.579-12.193-10.251-19.099-13.942 6.277-8.357 10-18.736 10-29.968v-20c0-27.57-22.43-50-50-50s-50 22.43-50 50v20c0 11.232 3.723 21.611 10 29.968-6.906 3.691-13.333 8.363-19.099 13.942-8.876 8.589-15.657 18.815-20.05 30.014-16.092-18.456-25.851-42.571-25.851-68.924zm-51 347h-70v-70h70zm191 0h-70v-70h70zm191 0h-70v-70h70z"></path></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>{{ this.viewDeviceData.device_type == 1 ? 'Personal' : (this.viewDeviceData.org_type == 1 ? 'Educational' : 'Organizational') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device Status</td>
                                                <td>
                                                    <span v-if="isDateValid(this.viewDeviceData.expiration_date)" class="fw-bold result_detail_font badge badge_covered text-white green_color px-3  rounded-pill"> Covered </span>
                                                    <span v-else class="fw-bold result_detail_font badge badge_uncovered orange_color px-3 rounded-pill"> Uncovered </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">IMEI</td>
                                                <td><span class="result_detail_font ">{{ this.viewDeviceData.imei ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device Cellular Service</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.cellular_service == 1 ? 'Yes' : 'No' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device Capacity</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.capacity ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Device Carrier</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.carrier ?? '' }}</span></td>
                                            </tr>
                                            <tr>
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
                                    <h3 class="list-header  def_16_size"> User Information </h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">User Name</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.user_full_name ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">User Email</td>
                                                <td>
                                                    <div class="d-flex justify-content-between w-100">
                                                    <span class="result_detail_font diffrent_div d-flex align-items-center gap-1 ">
                                                        <svg id="fi_11502423" enable-background="new 0 0 512 512" height="22" viewBox="0 0 512 512" width="22" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m462.88 337.781c0 43.236-35.17 78.351-78.351 78.351h-257.057c-43.181 0-78.352-35.116-78.352-78.351v-163.562c0-14.43 3.951-27.983 10.809-39.615l125.428 125.428c18.765 18.82 43.894 29.19 70.67 29.19 26.721 0 51.85-10.37 70.615-29.19l125.428-125.428c6.859 11.632 10.809 25.184 10.809 39.615v163.562zm-78.352-241.913h-257.056c-17.832 0-34.293 6.035-47.461 16.076l126.69 126.745c13.114 13.058 30.616 20.301 49.326 20.301 18.655 0 36.158-7.243 49.271-20.301l126.69-126.745c-13.167-10.041-29.627-16.076-47.46-16.076zm0-30.232h-257.056c-59.861 0-108.584 48.723-108.584 108.584v163.562c0 59.916 48.723 108.584 108.584 108.584h257.056c59.861 0 108.584-48.668 108.584-108.584v-163.563c0-59.861-48.723-108.583-108.584-108.583z" fill-rule="evenodd"></path>
                                                        </svg>
                                                        {{ this.viewDeviceData.user_email ?? '' }}

                                                    </span>
                                                    <button type="button" @click="switchUser(this.viewDeviceData.user_id)" class="slidebarswitch d-flex align-items-center rounded-pill p-1 pe-3  def_12_size gap-1  btn"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"  x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><circle r="256" cx="256" cy="256" fill="#dcdcdc" shape="circle"/><g transform="matrix(0.6000000000000003,0,0,0.6000000000000003,102.39999999999992,102.39999999999992)"><path d="M171.497 125.841C203.173 144.033 224 178.072 224 216.5V240H15v-23.5c0-39.106 21.491-73.197 53.3-91.116" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><circle cx="120" cy="80" r="65" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="M443.833 381.461a104.866 104.866 0 0 1 22.557 17.149C485.3 417.52 497 443.64 497 472.5V497H288v-24.5c0-38.393 20.712-71.955 51.575-90.124" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><circle cx="392" cy="336" r="64" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="M256 112h106c16.569 0 30 13.431 30 30v58" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="m360 184 32 32 32-32M256 400H150c-16.569 0-30-13.431-30-30v-66" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/><path d="m152 328-32-32-32 32" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#3d3d3d" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1"/></g></svg> Switch User </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 1">
                                                <td class="fw-bold first_td_clss">Owner's First Name</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_user_first_name ?? '' }}</span></td>
                                            </tr>
                                            <tr  v-if="this.viewDeviceData.device_type == 1">
                                                <td class="fw-bold first_td_clss">Owner's Last Name</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_user_last_name ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">User Registration Date</td>
                                                <td><span class=" result_detail_font diffrent_div d-flex align-items-center gap-1"><svg fill="none" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_9307921"><g fill="rgb(0,0,0)"><path d="m8 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m16 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m8.5 14.5001c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.15.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m12 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m15.5 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.04-.05-.08-.1-.12-.15-.04-.06-.07-.12-.09-.18-.03-.06-.05-.12-.06-.18-.01-.07-.02-.14-.02-.2 0-.26.11-.52.29-.71.1-.09.2-.16.33-.21.37-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m8.5 17.9999c-.13 0-.26-.0299-.38-.0799s-.23-.1201-.33-.2101c-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .18.19.29.4499.29.7099s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m12 17.9999c-.26 0-.52-.11-.71-.29-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .09.09.16.1999.21.3299.05.12.08.25.08.38 0 .26-.11.52-.29.71-.19.18-.45.29-.71.29z"></path><path d="m15.5 17.9999c-.26 0-.52-.11-.71-.29-.09-.09-.16-.2-.21-.33-.05-.12-.08-.25-.08-.38s.03-.26.08-.38c.05-.13.12-.24.21-.33.23-.23.58-.34.9-.27.07.01.13.03.19.06.06.02.12.05.18.09.05.03.1.08.15.12.18.19.29.45.29.71s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m20.5 9.83984h-17c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h17c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m16 22.75h-8c-3.65 0-5.75-2.1-5.75-5.75v-8.5c0-3.65 2.1-5.75 5.75-5.75h8c3.65 0 5.75 2.1 5.75 5.75v8.5c0 3.65-2.1 5.75-5.75 5.75zm-8-18.5c-2.86 0-4.25 1.39-4.25 4.25v8.5c0 2.86 1.39 4.25 4.25 4.25h8c2.86 0 4.25-1.39 4.25-4.25v-8.5c0-2.86-1.39-4.25-4.25-4.25z"></path></g></svg> {{ formatDateTime(this.viewDeviceData.user_registration_Date) ?? '' }}</span></td>
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
                                            <tr  v-if="this.viewDeviceData.device_type == 2">
                                                <td class="fw-bold first_td_clss">{{ this.viewDeviceData.org_type == 1 ? "Parent's Name" : "Owner's Full Name" }}</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.device_owner_name ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 2">
                                                <td class="fw-bold first_td_clss">{{ this.viewDeviceData.org_type == 1 ? "Student's Name" : "Organization User Full Name" }}</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_user_full_name ?? '' }}</span></td>
                                            </tr>
                                            <tr v-if="this.viewDeviceData.device_type == 2">
                                                <td class="fw-bold first_td_clss">{{ this.viewDeviceData.org_type == 1 ? "Student's Grade" : "User Designation" }}</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.org_user_designation ?? '' }}</span></td>
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
                                    <h3 class="list-header   def_16_size"> Plan Information</h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size ">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Plan</td>
                                                <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg fill="none" height="22" viewBox="0 0 97 96" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_9742126"><g fill="rgb(0,0,0)"><path d="m37.7218 83.7942c-5.8542 0-10.6-4.7458-10.6-10.6v-50.3877c0-5.8542 4.7458-10.6 10.6-10.6h23.6617c1.4359 0 2.6 1.1641 2.6 2.6s-1.1641 2.6-2.6 2.6h-23.6617c-2.9823 0-5.4 2.4177-5.4 5.4v50.3877c0 2.9823 2.4177 5.4 5.4 5.4h36.9284c2.9823 0 5.4-2.4177 5.4-5.4v-36.4629c0-1.436 1.164-2.6 2.6-2.6 1.4359 0 2.6 1.164 2.6 2.6v36.4629c0 5.8542-4.7458 10.6-10.6 10.6z"></path><path d="m15.8705 31.0069c-3.6451 0-6.59998 2.9549-6.59998 6.6l-.00001 32.0427c0 3.6451 2.95489 6.6 6.59999 6.6h3.678c1.4359 0 2.6-1.164 2.6-2.6 0-1.4359-1.1641-2.6-2.6-2.6h-3.678c-.7732 0-1.4-.6268-1.4-1.4v-32.0427c0-.7732.6268-1.4 1.4-1.4h3.678c1.4359 0 2.6-1.1641 2.6-2.6 0-1.436-1.1641-2.6-2.6-2.6z"></path><path d="m76.051 12.541c.4045-1.0001 1.8205-1.0001 2.2249 0l1.7015 4.2072c.1719.4251.571.715 1.0284.7471l4.527.3181c1.0762.0756 1.5138 1.4223.6876 2.1161l-3.4755 2.9182c-.3512.2949-.5036.764-.3928 1.2089l1.0964 4.4038c.2606 1.0469-.8849 1.8792-1.8 1.3078l-3.8495-2.4036c-.3889-.2428-.8822-.2428-1.2711 0l-3.8494 2.4036c-.9151.5714-2.0607-.2609-1.8-1.3078l1.0964-4.4038c.1108-.4449-.0417-.914-.3928-1.2089l-3.4755-2.9182c-.8262-.6938-.3886-2.0405.6875-2.1161l4.5271-.3181c.4574-.0321.8565-.322 1.0284-.7471z"></path><path d="m64.4021 34.1409c-1.6784-1.3702-3.5654-2.2448-5.4716-2.6644v-2.8191c0-1.4359-1.164-2.6-2.6-2.6-1.4359 0-2.6 1.1641-2.6 2.6v2.7942c-2.7655.6081-5.3169 2.262-6.6026 4.9866-1.3241 2.806-1.248 5.7033.4157 8.2727 1.5422 2.3819 4.2627 4.1936 7.6182 5.5856 3.0614 1.2701 4.7015 2.6207 5.4632 3.7915.6713 1.0319.801 2.1282.2842 3.5171-.4639 1.2471-1.8459 2.2103-4.1986 2.2938-2.3259.0825-4.8403-.7846-6.4135-2.2885-1.0379-.9923-2.6838-.9552-3.676.0827-.9923 1.038-.9552 2.6838.0827 3.6761 1.9221 1.8374 4.4556 3.0034 7.0267 3.4835v2.4905c0 1.4359 1.1641 2.6 2.6 2.6 1.436 0 2.6-1.1641 2.6-2.6v-2.4846c2.8771-.5775 5.6555-2.2234 6.8524-5.4403 1.0298-2.768.862-5.6125-.7991-8.1659-1.5707-2.4145-4.2978-4.2938-7.8294-5.7589-2.9347-1.2175-4.532-2.5061-5.2459-3.6087-.5924-.915-.7106-1.8866-.0779-3.2274.5123-1.0856 1.7537-1.9956 3.5774-2.2235 1.7915-.2239 3.9089.2683 5.7056 1.7351 1.1123.9081 2.7502.7426 3.6583-.3698.9081-1.1123.7425-2.7502-.3698-3.6583z"></path></g></svg>{{ this.viewDeviceData.plan_name ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Coverage Start Date</td>
                                                <td><span class="result_detail_font">{{ formatDate(this.viewDeviceData.payment_added_date) ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Coverage Expiration Date</td>
                                                <td><span class="result_detail_font diffrent_div d-flex align-items-center gap-1"><svg fill="none" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg" id="fi_9307921"><g fill="rgb(0,0,0)"><path d="m8 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m16 5.75c-.41 0-.75-.34-.75-.75v-3c0-.41.34-.75.75-.75s.75.34.75.75v3c0 .41-.34.75-.75.75z"></path><path d="m8.5 14.5001c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.15.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m12 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.18-.19-.29-.44-.29-.71 0-.13.03-.26.08-.38s.12-.23.21-.33c.1-.09.2-.16.33-.21.36-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m15.5 14.4999c-.13 0-.26-.03-.38-.08-.13-.05-.23-.12-.33-.21-.04-.05-.08-.1-.12-.15-.04-.06-.07-.12-.09-.18-.03-.06-.05-.12-.06-.18-.01-.07-.02-.14-.02-.2 0-.26.11-.52.29-.71.1-.09.2-.16.33-.21.37-.16.81-.07 1.09.21.18.19.29.45.29.71 0 .06-.01.13-.02.2-.01.06-.03.12-.06.18-.02.06-.05.12-.09.18-.03.05-.08.1-.12.15-.19.18-.45.29-.71.29z"></path><path d="m8.5 17.9999c-.13 0-.26-.0299-.38-.0799s-.23-.1201-.33-.2101c-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .18.19.29.4499.29.7099s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m12 17.9999c-.26 0-.52-.11-.71-.29-.18-.19-.29-.45-.29-.71 0-.13.03-.26.08-.38.05-.13.12-.2399.21-.3299.37-.37 1.05-.37 1.42 0 .09.09.16.1999.21.3299.05.12.08.25.08.38 0 .26-.11.52-.29.71-.19.18-.45.29-.71.29z"></path><path d="m15.5 17.9999c-.26 0-.52-.11-.71-.29-.09-.09-.16-.2-.21-.33-.05-.12-.08-.25-.08-.38s.03-.26.08-.38c.05-.13.12-.24.21-.33.23-.23.58-.34.9-.27.07.01.13.03.19.06.06.02.12.05.18.09.05.03.1.08.15.12.18.19.29.45.29.71s-.11.52-.29.71c-.19.18-.45.29-.71.29z"></path><path d="m20.5 9.83984h-17c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h17c.41 0 .75.34.75.75s-.34.75-.75.75z"></path><path d="m16 22.75h-8c-3.65 0-5.75-2.1-5.75-5.75v-8.5c0-3.65 2.1-5.75 5.75-5.75h8c3.65 0 5.75 2.1 5.75 5.75v8.5c0 3.65-2.1 5.75-5.75 5.75zm-8-18.5c-2.86 0-4.25 1.39-4.25 4.25v8.5c0 2.86 1.39 4.25 4.25 4.25h8c2.86 0 4.25-1.39 4.25-4.25v-8.5c0-2.86-1.39-4.25-4.25-4.25z"></path></g></svg>{{ formatDate(this.viewDeviceData.expiration_date) ?? '' }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div  class="card-body py-0" v-if="this.viewDeviceData.occurence && this.viewDeviceData.occurence == 1">
                                    <h3 class="list-header   def_16_size"> Transaction Information</h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size ">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Id</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.transaction_id ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Amount</td>
                                                <td><span class="result_detail_font">${{ this.viewDeviceData.transaction_amount ?? '0' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Status</td>
                                                <td><span class="result_detail_font">{{ this.viewDeviceData.transaction_status ?? '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Transaction Date</td>
                                                <td><span class="result_detail_font">{{ formatDateTime(this.viewDeviceData.transaction_created_at) ?? '' }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div  class="card-body py-0" v-if="this.viewDeviceData.subscription_details && (this.viewDeviceData.occurence != 1 || this.viewDeviceData.occurence != null)">
                                    <h3 class="list-header   def_16_size">Subscription Information</h3>
                                    <table class="table bg-white table-bordered mt-1 def_14_size ">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold first_td_clss">Subscription Id</td>
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
</template>
<script>
    export default {

        props: {
            devicedata:{
                type: Object,
            },
            pagination: {
                type: Object,
            },

            devicebrands: {
                type: Object,
            },
            totaldevices: {
                type: Number,
            }
        },

        data() {
            return {
                /* icons for table */
                deleteIc: this.$deleteIcon,
                editIc: this.$editIcIcon,
                viewIc: this.$viewIcIcon,
                searchIc: this.$searchIcIcon,
                profileIc: this.$profileIcIcon,

                /** To manage the count of the devices */
                totalDevice: this.totaldevices,

                /** To show or hide the add ans edit model */
                showAddModal: false,
                showEditModal: false,

                /** To store the device and pagination data */
                deviceData: this.devicedata.data,
                paginationData: this.pagination,

                /** To store the searched value */
                search: '',

                /** To store the filter data */
                selectedBrands: 'all',
                selectedUserId: null,
                startDate: '',
                endDate: '',
                deviceCoverage: 'all',
                deviceType: 'all',
                validationErrors: {
                    endDate: '',
                },

                /** To store the data of device details */
                viewDeviceData: [],
                isShowViewAnimLoader: false,

                openListSideBar: false,
                deviceId: null,
            };
        },

        mounted() {
            const urlParams = new URLSearchParams(window.location.search);
            const openPopup = urlParams.get('filter');
            const queryModel = urlParams.get('coverage');
            const querySelectedUser = urlParams.get('userId');
            this.openListSideBar = urlParams.get('openPopup');
            this.deviceId = urlParams.get('deviceId');
            if (openPopup == 'true' && queryModel) {
                this.deviceCoverage = queryModel;
                this.getdeviceData();
            }
            if (openPopup == 'true' && querySelectedUser) {
                this.selectedUserId = querySelectedUser;
                this.getdeviceData();
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
                show_ajax_loader();
                try {
                    let url = `${this.$userAppUrl}smarttiusadmin/devices?page=${page}`;
                    if (this.search) {
                        url += `&search=${this.search}`;
                    }
                    if (this.selectedBrands) {
                        url += `&brand=${this.selectedBrands}`;
                    }
                    if (this.deviceCoverage) {
                        url += `&coverage=${this.deviceCoverage}`;
                    }
                    if (this.deviceType) {
                        url += `&orgType=${this.deviceType}`;
                    }
                    if (this.startDate && this.endDate) {
                        url += `&startDate=${this.startDate}&endDate=${this.endDate}`;
                    }
                    if (this.selectedUserId) {
                        url += `&userId=${this.selectedUserId}`;
                    }
                    // const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/devices?page=${page}${this.search ? `&search=${this.search}` : ''}`);
                    const response = await axios.get(url);
                    if (response.data.success == true) {
                        if (response.data.deviceData && response.data.pagination )
                        {
                            this.deviceData = response.data.deviceData.data;
                            this.totalDevice = response.data.deviceData.total;
                            this.paginationData = response.data.pagination;
                            hide_ajax_loader();
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = "Something went wrong";
                        }
                    }
                } catch (error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    // setTimeout(
                    //     () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                    //     5000
                    // );
                }
            },

            /* on click of view button*/
            async viewDevice(id = '') {
                show_overlay();
                this.isShowViewAnimLoader = true;
                try {
                    let response = await axios.get(`${this.$userAppUrl}smarttiusadmin/devices/view/${id}`);
                    if (response.data.success == true) {
                        this.viewDeviceData = response.data.deviceData;
                    }
                } catch (error) {
                    if (error && error.response && error.response.data && error.response.data.error) {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = error.response.data.error;
                        setTimeout(() =>(location.href = `${this.$homeUrl}`),4000);
                    }
                } finally {
                    setTimeout(() => {
                        this.isShowViewAnimLoader = false;
                    }, 800); /* short delay for animation effect */
                }
            },

            /** Cancel the search */
            async cancelSearch(){
                show_ajax_loader();
                this.search = '';
                this.getdeviceData();
                hide_ajax_loader();
            },

            /* On click of delete button */
            confirmDelete(id) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "This is under development.";
                /**
                this.$deleteAlertMessage.data.isOpen = true
                this.$deleteAlertMessage.data.itemId = id
                this.$deleteAlertMessage.data.message = 'Are you sure you want to delete this Device?'
                this.$deleteAlertMessage.data.callback = this.deleteDevice
                */
            },

            /** Delete existing device */
            async deleteDevice(id) {
                show_ajax_loader();
                try {
                    const response = await axios.delete(
                        `${this.$userAppUrl}smarttiusadmin/devices/delete/${id}`
                    );
                    if (response.data.success) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true
                        this.$alertMessage.message = response.data.msg
                        setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin/devices`),4000);
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false
                        this.$alertMessage.message = response.data.message
                    }
                } catch (error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        3000
                    );
                }
            },

            /** Formate Date */
            filterFormatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            },

            /** Formate Date with Time */
            formatDateTime(dateString) {
                return formatDateTimeAndTimeZone(dateString);
            },

            /** Formate Date */
            formatDate(dateString) {
                return formatDateAndTimeZone(dateString);
            },

            /** Reset filter */
            resetFilter() {
                this.selectedBrands = 'all';
                this.startDate = '';
                this.endDate = '';
                this.deviceCoverage = 'all';
                this.deviceType = 'all';
                this.validationErrors.endDate = '';
                this.validationErrors.startDate = '';
                this.search = '';
                this.selectedUserId = null;
                this.getdeviceData(1);
                hide_admin_popup('#deviceFilterModalClose');
                /* Remove ?filter=true&coverage=... from URL without reloading */
                const cleanUrl = window.location.origin + window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            },

            /** Apply filter */
            applyFilter() {
                /* validation */
                if (this.startDate && this.endDate) {
                    if (new Date(this.startDate) > new Date(this.endDate)) {
                        this.validationErrors.endDate = "End date should be greater than start date";
                        return;
                    }
                }
                this.getdeviceData(1);
                hide_admin_popup('#deviceFilterModalClose');
            },

            /** Export devices */
            async exportDevices() {
                show_ajax_loader();
                try {
                   /*  let url = `${this.$userAppUrl}smarttiusadmin/devices/export`;
                    if (this.search && this.selectedBrands && this.deviceCoverage && this.startDate && this.endDate && this.deviceType) {
                        url += `?search=${this.search}&brand=${this.selectedBrands}&coverage=${this.deviceCoverage}&startDate=${this.startDate}&endDate=${this.endDate}&orgType=${this.deviceType}`;
                    } else if (this.search && this.selectedBrands && this.deviceCoverage) {
                        url += `?search=${this.search}&brand=${this.selectedBrands}&coverage=${this.deviceCoverage}`;
                    } else if (this.search && this.selectedBrands) {
                        url += `?search=${this.search}&brand=${this.selectedBrands}`;
                    } else if (this.search && this.deviceCoverage) {
                        url += `?search=${this.search}&coverage=${this.deviceCoverage}`;
                    } else if (this.selectedBrands && this.deviceCoverage ) {
                        url += `?brand=${this.selectedBrands}&coverage=${this.deviceCoverage}`;
                    } else if (this.startDate && this.endDate) {
                        url += `?startDate=${this.startDate}&endDate=${this.endDate}`;
                    } else if (this.search) {
                        url += `?search=${this.search}`;
                    } else if (this.selectedBrands) {
                        url += `?brand=${this.selectedBrands}`;
                    } else if (this.deviceCoverage) {
                        url += `?coverage=${this.deviceCoverage}`;
                    } else if (this.deviceType) {
                        url += `?orgType=${this.deviceType}`;
                    } else if (this.startDate && this.endDate) {
                        url += `?startDate=${this.startDate}&endDate=${this.endDate}`;
                    } else {
                        url += `?brand=all&coverage=all&orgType=all`;
                    } */
                   let url = `${this.$userAppUrl}smarttiusadmin/devices/export`;

                    // Build query params dynamically
                    let params = new URLSearchParams();

                    if (this.search) params.append('search', this.search);
                    if (this.selectedBrands) params.append('brand', this.selectedBrands);
                    if (this.deviceCoverage) params.append('coverage', this.deviceCoverage);
                    if (this.startDate) params.append('startDate', this.startDate);
                    if (this.endDate) params.append('endDate', this.endDate);
                    if (this.deviceType) params.append('orgType', this.deviceType);
                    if (this.selectedUserId) params.append('userId', this.selectedUserId);

                    // Default fallback when no filters are set
                    if ([...params].length === 0) {
                        params.append('brand', 'all');
                        params.append('coverage', 'all');
                        params.append('orgType', 'all');
                    }

                    url += `?${params.toString()}`;

                    const response = await axios.get(url);
                    /* const response = await axios.get(url, { responseType: 'blob' }); */
                    if (response.data.success == false) {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = response.data.msg;
                        return;
                    }
                    // Create a blob from the response data
                    const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.setAttribute('download', 'devices.csv');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } catch (error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Error exporting devices. Please try again.";
                } finally {
                    hide_ajax_loader();
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
        },
    };
</script>
