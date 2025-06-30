<template>
    <ul class="nav nav-tabs px-3 top_nav_barmain ">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#home">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#menu1">AI Agent</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#menu3">Status</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#menu4">Devices</a>
        </li>
    </ul>
    <div class="tab-content mt_12">
        <div class="tab-pane  active" id="home">
            <div class="container-fluid  ">
                <div class="card     border-20 border-newT welcome_div">
                    <div class="card-body d-flex justify-content-between border-20">
                        <div>
                            <h2 class="mb-2 themetextcolor"><span class="text-primary">Hi,</span> {{ $authUser.full_name ?? '' }}!</h2>
                            <h5 class="mb-2 themetextcolor">What do you want to do today?</h5>
                        </div>
                    <div class="d-flex  align-items-center">
                        <div class="welcome_svg">
                            <svg  xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <defs>
                                        <path id="welcome-a" d="M64 0l64 128-64-20-64 20z"></path>
                                        <path id="welcome-e" d="M40 0l40 80-40-12.5L0 80z"></path>
                                        <path id="welcome-g" d="M40 0l40 80-40-12.5L0 80z"></path>
                                        <linearGradient x1="50%" y1="0%" x2="50%" y2="100%" id="welcome-b">
                                            <stop stop-color="#A5B4FC" offset="0%"></stop>
                                            <stop stop-color="#818CF8" offset="100%"></stop>
                                        </linearGradient>
                                        <linearGradient x1="50%" y1="24.537%" x2="50%" y2="100%" id="welcome-c">
                                            <stop stop-color="#4338CA" offset="0%"></stop>
                                            <stop stop-color="#6366F1" stop-opacity="0" offset="100%"></stop>
                                        </linearGradient>
                                    </defs>
                                    <g fill="none" fill-rule="evenodd">
                                        <g transform="rotate(64 36.592 105.604)">
                                            <mask id="welcome-d" fill="#fff">
                                                <use xlink:href="#welcome-a"></use>
                                            </mask>
                                            <use fill="url(#welcome-b)" xlink:href="#welcome-a"></use>
                                            <path fill="url(#welcome-c)" mask="url(#welcome-d)" d="M64-24h80v152H64z"></path>
                                        </g>
                                        <g transform="rotate(-51 91.324 -105.372)">
                                            <mask id="welcome-f" fill="#fff">
                                                <use xlink:href="#welcome-e"></use>
                                            </mask>
                                            <use fill="url(#welcome-b)" xlink:href="#welcome-e"></use>
                                            <path fill="url(#welcome-c)" mask="url(#welcome-f)" d="M40.333-15.147h50v95h-50z"></path>
                                        </g>
                                        <g transform="rotate(44 61.546 392.623)">
                                            <mask id="welcome-h" fill="#fff">
                                                <use xlink:href="#welcome-g"></use>
                                            </mask>
                                            <use fill="url(#welcome-b)" xlink:href="#welcome-g"></use>
                                            <path fill="url(#welcome-c)" mask="url(#welcome-h)" d="M40.333-15.147h50v95h-50z"></path>
                                        </g>
                                    </g>
                                </svg>
                        </div>
                         <!-- <button class="btn z-1 def_14_size border border-black signoutbtn text-black d-flex align-items-center gap-1">
                            <svg width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" id="fi_16027000"><g id="_109_log-out-02" data-name="109 log-out-02"><path d="m8.88 22.71-5.29-5.3a2 2 0 0 1 0-2.82l5.29-5.3 1.41 1.42-5.29 5.29 5.29 5.29z"></path><path d="m4.59 15h14v2h-14z"></path><path d="m26 29h-8v-2h8a1 1 0 0 0 1-1v-20a1 1 0 0 0 -1-1h-8v-2h8a3 3 0 0 1 3 3v20a3 3 0 0 1 -3 3z"></path></g></svg>
                            Sign Out
                        </button> -->
                    </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid top_four_card D_divice_list_blocks">
                <div class="row">
                    <div class="col-12  col-md-6 col-lg-3 mt-4">
                        <div class="card justify-content-between border-20 onewhitebg  shadow h-100 border-newT">
                            <div class="card-body front_page_card_height border_radioustop375 ">
                                <div class="media d-flex align-items-center gap-20">
                                    <div class="icon_box svg3">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 512 512"
                                            style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                            <g>
                                                <circle cx="279.273" cy="349.091" r="11.636" fill="#6FA0B0" opacity="1"
                                                    data-original="#000000"></circle>
                                                <circle cx="81.455" cy="418.909" r="11.636" fill="#6FA0B0" opacity="1"
                                                    data-original="#000000"></circle>
                                                <path
                                                    d="M477.091 46.545H81.455c-19.782 0-34.909 15.127-34.909 34.909v116.364c0 6.982 4.655 11.636 11.636 11.636s11.636-4.655 11.636-11.636V81.455c0-6.982 4.655-11.636 11.636-11.636H477.09c6.982 0 11.636 4.655 11.636 11.636v279.273c0 6.982-4.655 11.636-11.636 11.636H197.818c-6.982 0-11.636 4.655-11.636 11.636s4.655 11.636 11.636 11.636h23.273v34.909c0 6.982-4.655 11.636-11.636 11.636h-11.636c-6.982 0-11.636 4.655-11.636 11.636s4.655 11.636 11.636 11.636h162.909c6.982 0 11.636-4.655 11.636-11.636s-4.655-11.636-11.636-11.636h-11.636c-6.982 0-11.636-4.655-11.636-11.636v-34.909h139.636c19.782 0 34.909-15.127 34.909-34.909V81.455c-.001-19.782-15.128-34.91-34.91-34.91zM242.036 442.182c1.164-3.491 2.327-6.982 2.327-11.636v-34.909h69.818v34.909c0 4.655 1.164 8.145 2.327 11.636h-74.472z"
                                                    fill="#6FA0B0" opacity="1" data-original="#000000"></path>
                                                <path
                                                    d="M453.818 302.545h-256c-6.982 0-11.636 4.655-11.636 11.636s4.655 11.636 11.636 11.636h256c6.982 0 11.636-4.655 11.636-11.636s-4.654-11.636-11.636-11.636zM128 232.727H34.909C15.127 232.727 0 247.855 0 267.636v162.909c0 19.782 15.127 34.909 34.909 34.909H128c19.782 0 34.909-15.127 34.909-34.909V267.636c0-19.781-15.127-34.909-34.909-34.909zm11.636 197.818c0 6.982-4.655 11.636-11.636 11.636H34.909c-6.982 0-11.636-4.655-11.636-11.636V267.636c0-6.982 4.655-11.636 11.636-11.636H128c6.982 0 11.636 4.655 11.636 11.636v162.909z"
                                                    fill="#6FA0B0" opacity="1" data-original="#000000"></path>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="media-body  d-flex flex-column align-items-center">
                                        <h6 class="media-title def_16_size  w-100">Devices</h6>
                                        <h4 class="media-number def_20_size  w-100    mb-0">{{ formattedDigits(totalDevices) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer  border-top-0 bottom_20border  p-0">
                                <div class="bottom_links">
                                    <div class="first_link_div bottom_right_l15 bg3 bprimary">
                                        <a href="#">
                                        <div data-bs-toggle="modal" data-bs-target="#coverage_check" class="bg_foot_btn cursor justify-content-center d-flex align-items-center gap-10 onedarkGr">
                                            Verify Coverage
                                            <img :src="$imagePath + 'right-arrow.svg'">

                                        </div>
                                    </a>
                                    </div>
                                    <div class="first_link_div bottom_right_r15 bg3">
                                        <a :href="$userAppUrl + 'sdcsmuser/device-list'">
                                        <div class="bg_foot_btn cursor justify-content-center d-flex align-items-center gap-10 onedarkGr">

                                                List Devices
                                                <img :src="$imagePath + 'right-arrow.svg'">

                                        </div></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-6 col-lg-3  mt-4">
                        <div class="card justify-content-between border-20 onewhitebg  shadow  h-100 border-newT">
                            <div class="card-body front_page_card_height border_radioustop375 ">
                                <div class="media d-flex align-items-center gap-20">
                                    <div class="icon_box svg1">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 24 24"
                                            style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                            <g>
                                                <path
                                                    d="M17 15.63a4.375 4.375 0 0 0-3.309-4.241l-2.9-.718A2.371 2.371 0 0 1 11.37 6h1.26a2.372 2.372 0 0 1 2.356 2.112 1 1 0 0 0 1.988-.224 4.359 4.359 0 0 0-4.014-3.857V3a1 1 0 0 0-2 0v1.041a4.359 4.359 0 0 0-.651 8.57l2.9.718A2.372 2.372 0 0 1 12.63 18h-1.26a2.372 2.372 0 0 1-2.356-2.112 1 1 0 0 0-1.988.224 4.359 4.359 0 0 0 3.934 3.85V21a1 1 0 0 0 2 0v-1.032A4.344 4.344 0 0 0 17 15.63z"
                                                    fill="#bdccc4" opacity="1" data-original="#000000" class=""></path>
                                            </g>
                                        </svg>

                                    </div>
                                    <div class="media-body  d-flex flex-column align-items-center">
                                        <h6 class="media-title def_16_size  w-100">Insured Claims</h6>
                                        <h4 class="media-number def_20_size  w-100   mb-0">{{ formattedDigits(totalInsuredClaims) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer  border-top-0 bottom_20border  p-0">
                                <div class="bottom_links">
                                    <div class="first_link_div bottom_right_l15 bg1 bprimary">
                                        <a :href="$userAppUrl + 'sdcsmuser/user-file-claim'">
                                        <div class="bg_foot_btn cursor justify-content-center d-flex align-items-center gap-10 onedarkGr">

                                                File Claim
                                                <img :src="$imagePath + 'right-arrow.svg'">

                                        </div> </a>
                                    </div>
                                    <div class="first_link_div bottom_right_r15 bg1 "><a :href="$userAppUrl + 'sdcsmuser/user-track-claims'">
                                        <div class="bg_foot_btn cursor justify-content-center d-flex align-items-center gap-10 onedarkGr">

                                                Track Claim
                                                <img :src="$imagePath + 'right-arrow.svg'">

                                        </div></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3  mt-4">
                        <div class="card justify-content-between border-20 onewhitebg  shadow h-100 border-newT">
                            <div class="card-body front_page_card_height border_radioustop375 ">
                                <div class="media d-flex align-items-center gap-20">
                                    <div class="icon_box svg2">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 512 512"
                                            style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                            <g>
                                                <path
                                                    d="M448.731 160.729a10.002 10.002 0 0 0-7.278-6.053 9.994 9.994 0 0 0-9.064 2.729l-49.321 49.321-25.927-6.947-6.947-25.928 49.32-49.32a10 10 0 0 0-3.323-16.342 95.24 95.24 0 0 0-35.923-6.948V45.694C360.266 20.498 339.767 0 314.571 0H102.035C76.839 0 56.341 20.498 56.341 45.694v420.611c0 25.196 20.498 45.694 45.694 45.694h212.536c25.196 0 45.694-20.498 45.694-45.694v-174.74c24.891.014 49.378-9.69 67.518-27.83a95.06 95.06 0 0 0 20.948-103.006zM271.807 20l-5.25 16.157H150.049L144.8 20zm42.764 472H102.035c-14.167 0-25.694-11.526-25.694-25.694V45.694C76.341 31.526 87.868 20 102.035 20h21.735l9.503 29.247a10 10 0 0 0 9.511 6.91h131.039a10 10 0 0 0 9.511-6.91L292.836 20h21.735c14.168 0 25.694 11.526 25.694 25.694v57.715a95.067 95.067 0 0 0-47.083 25.725c-24.605 24.605-33.712 60.888-24.059 94.019l-62.905 62.904c-17.822 17.823-17.822 46.822 0 64.644 8.605 8.605 20.084 13.345 32.322 13.345s23.716-4.739 32.321-13.345l62.905-62.904a94.508 94.508 0 0 0 6.5 1.642v176.868c0 14.167-11.527 25.693-25.695 25.693zm19.92-225.008a9.996 9.996 0 0 0-10.521 2.314l-67.251 67.251c-4.828 4.828-11.284 7.487-18.179 7.487-6.896 0-13.352-2.659-18.18-7.487-10.023-10.023-10.023-26.334.001-36.358l67.251-67.251a10 10 0 0 0 2.315-10.521c-10.083-27.431-3.253-58.499 17.397-79.15 14.402-14.401 33.584-22.049 53.145-22.049 4.31 0 8.639.372 12.938 1.124l-41.439 41.439a10 10 0 0 0-2.588 9.659l9.936 37.081a10 10 0 0 0 7.071 7.071l37.081 9.936a9.996 9.996 0 0 0 9.659-2.588l41.44-41.439a75.082 75.082 0 0 1-20.925 66.082c-20.652 20.652-51.72 27.478-79.151 17.399zm-126.188 144.42c-16.818 0-30.5 13.683-30.5 30.5s13.682 30.5 30.5 30.5 30.5-13.683 30.5-30.5-13.682-30.5-30.5-30.5zm0 41c-5.79 0-10.5-4.71-10.5-10.5s4.71-10.5 10.5-10.5 10.5 4.71 10.5 10.5-4.71 10.5-10.5 10.5z"
                                                    fill="#c5998d" opacity="1" data-original="#000000" class=""></path>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="media-body  d-flex   flex-column align-items-center">
                                        <h6 class="media-title def_16_size  w-100">Uninsured Repairs</h6>
                                        <h4 class="media-number def_20_size  w-100   mb-0">{{ formattedDigits(totalUninsuredClaims) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer  border-top-0 bottom_20border  p-0">
                                <div class="bottom_links">
                                    <div class="first_link_div bottom_right_l15 bg2 bprimary ">
                                        <a :href="$userAppUrl + 'sdcsmuser/user-repair-request'">
                                        <div class="bg_foot_btn cursor justify-content-center d-flex align-items-center gap-10 onedarkGr">

                                                Repair Request
                                                <img :src="$imagePath + 'right-arrow.svg'">

                                        </div> </a>
                                    </div>
                                    <div class="first_link_div bottom_right_r15  bg2"><a :href="$userAppUrl + 'sdcsmuser/user-track-repairs'">
                                        <div class="bg_foot_btn cursor justify-content-center d-flex align-items-center gap-10 onedarkGr">
                                                Track Repair
                                                <img :src="$imagePath + 'right-arrow.svg'">
                                        </div></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-6 col-lg-3  mt-4">
                        <div class="card justify-content-between border-20 onewhitebg  shadow  h-100 border-newT">
                            <div class="card-body front_page_card_height border_radioustop375 ">
                                <div class="media d-flex align-items-center gap-20">
                                    <div class="icon_box svg4">
                                        <svg id="fi_2910751" enable-background="new 0 0 24 24" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="m19.5 24h-15c-1.378 0-2.5-1.122-2.5-2.5v-19c0-1.378 1.122-2.5 2.5-2.5h15c1.378 0 2.5 1.122 2.5 2.5v19c0 1.378-1.122 2.5-2.5 2.5zm-15-23c-.827 0-1.5.673-1.5 1.5v19c0 .827.673 1.5 1.5 1.5h15c.827 0 1.5-.673 1.5-1.5v-19c0-.827-.673-1.5-1.5-1.5z">
                                            </path>
                                            <path
                                                d="m18.5 12h-2c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h2c.276 0 .5.224.5.5s-.224.5-.5.5z">
                                            </path>
                                            <path
                                                d="m18.5 9h-2c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h2c.276 0 .5.224.5.5s-.224.5-.5.5z">
                                            </path>
                                            <path
                                                d="m18.5 15h-2c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h2c.276 0 .5.224.5.5s-.224.5-.5.5z">
                                            </path>
                                            <path
                                                d="m18.5 18h-2c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h2c.276 0 .5.224.5.5s-.224.5-.5.5z">
                                            </path>
                                            <path
                                                d="m18.5 21h-13c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h13c.276 0 .5.224.5.5s-.224.5-.5.5z">
                                            </path>
                                            <path
                                                d="m10 18c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5zm0-9c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4z">
                                            </path>
                                            <path
                                                d="m6.818 16.682c-.128 0-.256-.049-.354-.146-.195-.195-.195-.512 0-.707l3.036-3.036v-4.293c0-.276.224-.5.5-.5s.5.224.5.5v4.5c0 .133-.053.26-.146.354l-3.182 3.182c-.098.097-.226.146-.354.146z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="media-body  d-flex flex-column align-items-center">
                                        <h6 class="media-title def_16_size  w-100 mb-0">Uncovered Devices</h6>
                                        <h4 class="media-number def_20_size  w-100   mb-0">{{ formattedDigits(uncoverddevices) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer  bottom_20border  border-top-0   p-0">
                                <div class="last_card">
                                    <div class="first_link_div  bg4 bottom_20border">
                                        <a :href="$userAppUrl + 'sdcsmuser/device-list?filter=true&coverage=uncovered'">
                                            <div class="bg_foot_btn cursor d-flex align-items-center gap-10 onedarkGr">
                                                List Uncovered Devices
                                                <img :src="$imagePath + 'right-arrow.svg'">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid top_four_card">
                <div class="row">
                    <div class="col-12   col-md-6 col-lg-3 mt-4">
                        <div class="card onewhitebg border-20 border-newT shadow h-100">
                            <div class="card-header top_20border py-3  dashboard_itemhead  d-flex justify-content-between">
                                <h4 class=" mb-0 mt-0 themetextcolor  def_18_size">Covered & Uncovered Devices</h4>

                            </div>
                            <!-- Covered and Uncoverd Devices Chart -->
                            <dash-covered-uncoverd-devices-chart :totaldevice="totalDevices" :covereddevice="totalCoveredDevices" :uncovereddevice="totalUncoveredDevices"></dash-covered-uncoverd-devices-chart>

                        </div>
                    </div>
                    <div class="col-12   col-md-6 col-lg-3 mt-4">
                        <div class="card onewhitebg border-20 border-newT shadow h-100">
                            <div class="card-header top_20border py-3  dashboard_itemhead ">

                                <div class="row">
                                    <div class="col-6 col-md-6 col-lg-8 d-flex align-items-center">
                                        <h4 class="d-flex align-items-center themetextcolor  def_18_size gap-10 mb-0">
                                            Claims & Repairs
                                        </h4>
                                    </div>

                                </div>
                            </div>
                            <!-- Claim and Repair Chart -->
                            <dash-claim-repairs-chart :insuredclaims="totalInsuredClaims" :uninsuredclaims="totalUninsuredClaims"></dash-claim-repairs-chart>
                        </div>
                    </div>
                    <div class="col-12   col-md-12 col-lg-6 mt-4">
                        <div class="card onewhitebg border-20 border-newT shadow h-100">
                            <div class="card-header top_20border  py-3 dashboard_itemhead ">
                                <div class="row">
                                    <div class="col-6 col-md-6 col-lg-8 d-flex align-items-center">
                                        <h4 class="d-flex align-items-center def_18_size themetextcolor  gap-10 mb-0">

                                            Claims and Repairs By Month
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <dash-claim-repairs-chart-month :isadmin=false></dash-claim-repairs-chart-month>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid  mt-4 five_tables">
                <div class="row">
                    <div class="col-12">
                        <div class="card onewhitebg tabmain_wrap border-newT border-20 ">
                            <div class="card-body tabmain_wrap_inner">
                                <ul class="nav nav-pills  ">
                                     <li class="nav-item">
                                        <a class="nav-link text-center  py-3 h-100  active" data-bs-toggle="pill" href="#devic">Recent Devices</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link py-3 text-center h-100  " data-bs-toggle="pill" href="#rcl">Recent Claims</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-center py-3 h-100 " data-bs-toggle="pill" href="#rprrq">Recent Repair Requests</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link text-center py-3 h-100 " data-bs-toggle="pill" href="#mostrecent">Claim Status Updates<span class="d-none def_12_size d-block text-center">Claim status changed devices</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-center  py-3 h-100 " data-bs-toggle="pill" href="#stick">Recent Support Tickets</a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-3">
                                    <div class="tab-pane  " id="rcl">
                                        <div class="card onewhitebg table_gredient  border-newT  h-100">
                                            <div
                                                class="card-header  dashboard_itemhead  border-bottom-0  align-items-center d-flex justify-content-between">
                                                <h4 class=" mb-0 mt-0 themetextcolor  def_18_size text-capitalize">Recent Claims</h4>
                                                <a :href="`${this.$userAppUrl}sdcsmuser/user-track-claims`" class="btn viewallbtn def_14_size rounded-pill ">View All</a>
                                            </div>
                                            <!-- Recent Claims Components -->
                                            <dash-recent-claims :isadmin=false></dash-recent-claims>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="stick">
                                        <div class="card onewhitebg table_gredient  border-newT  h-100">
                                            <div
                                                class="card-header  dashboard_itemhead  border-bottom-0    align-items-center d-flex justify-content-between">
                                                <h4 class=" mb-0 mt-0 themetextcolor  def_18_size  text-capitalize">Recent support tickets</h4>
                                            </div>
                                            <!-- Recent Support Ticket Component -->
                                            <dash-recent-support-ticket :isadmin=false></dash-recent-support-ticket>
                                        </div>
                                    </div>
                                    <div class="tab-pane active " id="devic">
                                        <div class="card onewhitebg table_gredient border-newT  h-100">
                                            <div
                                                class="card-header   border-bottom-0    dashboard_itemhead  align-items-center d-flex justify-content-between">
                                                <h4 class="mb-0 mt-0 themetextcolor  def_18_size  text-capitalize">Devices</h4>
                                                <a :href="`${this.$userAppUrl}sdcsmuser/device-list`" class="btn viewallbtn def_14_size rounded-pill">View All</a>
                                            </div>
                                            <!-- Recent Devices -->
                                            <dash-recent-devices :isadmin=false></dash-recent-devices>
                                        </div>
                                    </div>
                                    <div class="tab-pane  fade" id="rprrq">
                                        <div class="card onewhitebg table_gredient  border-newT  h-100">
                                        <div
                                            class="card-header   border-bottom-0    dashboard_itemhead  align-items-center d-flex justify-content-between">
                                            <h4 class="mb-0 mt-0 themetextcolor  def_18_size  text-capitalize">Repair Requests</h4>
                                            <a :href="`${this.$userAppUrl}sdcsmuser/user-track-repairs`" class="btn viewallbtn def_14_size rounded-pill">View All</a>
                                        </div>
                                    <!-- Recent Repair Requests Component -->
                                    <dash-recent-repairs :isadmin=false></dash-recent-repairs>
                                    </div>
                                    </div>
                                    <div class="tab-pane fade" id="mostrecent">
                                        <div class="card onewhitebg table_gredient  border-newT  h-100">
                                            <div
                                                class="card-header   dashboard_itemhead   border-bottom-0   align-items-center d-flex justify-content-between">
                                                <h4 class="mb-0 mt-0 themetextcolor  def_18_size  text-capitalize">Most recent claim status changed devices</h4>
                                                <a :href="`${this.$userAppUrl}sdcsmuser/user-track-claims`" class="btn  viewallbtn def_14_size rounded-pill">View All</a>
                                            </div>
                                            <!-- Most recent claim status changed devices Components -->
                                            <dash-recent-status-change-claims :isadmin=false></dash-recent-status-change-claims>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane container-fluid fade" id="menu1">This is Under Development.</div>
        <div class="tab-pane container-fluid fade" id="menu3">This is Under Development.</div>
        <div class="tab-pane container-fluid fade" id="menu4">This is Under Development.</div>
    </div>
    <div class="chat_popup">
        <img :src="$imagePath + 'circle.svg'">
    </div>
    <!-- <user-layout-notification></user-layout-notification> -->
</template>
<script>
    export default {
        props: {
            totaldevices: {
                type: Number
            },
            coverddevices: {
                type: Number
            },
            uncoverddevices: {
                type: Number
            },
            totalinsuredclaims: {
                type: Number
            },
            totaluninsuredclaims: {
                type: Number
            },
        },
        data() {
            return {
                totalDevices: this.totaldevices,
                totalCoveredDevices: this.coverddevices,
                totalUncoveredDevices: this.uncoverddevices,
                totalInsuredClaims: this.totalinsuredclaims,
                totalUninsuredClaims: this.totaluninsuredclaims,
            }
        },
        methods: {
            formattedDigits(number)
            {
                return formattedNumber(number);
            },
        }
    };
</script>