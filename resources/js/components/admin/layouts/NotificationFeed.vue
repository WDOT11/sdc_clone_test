<template>
    <div :class="notificationsvisible ? 'notifoveropen' : ''"></div>
    <div class="notificaton_bar_wrap onewhitebg slideInRight onopen_notification">
    <!-- <div :class="notificationsvisible ? 'notificaton_bar_wrap slideInRight onopen_notification' : 'notificaton_bar_wrap slideInRight'"> -->
        <div class="notificaton_bar_inner h-100">
            <div class="card h-100  bg-transparent border-0 notification_div">
                <div class="notification_listdataconent">
                    <div class="card-header  py-3 main_notif_title d-flex justify-content-between align-items-center ">
                        <div class="media gap-10 border-20 d-flex align-items-center notification_himg">
                            <img :src="$imagePath + 'bell.svg'" />
                            <div class="media-body  notification_title themetextcolor">
                                Notifications Feeds
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                            width="22" height="22" x="0" y="0" viewBox="0 0 511.76 511.76"
                            style="enable-background: new 0 0 512 512" xml:space="preserve"
                            class="notification_closemaster" @click="hideNotification">
                            <g>
                                <path
                                    d="M436.896 74.869c-99.84-99.819-262.208-99.819-362.048 0-99.797 99.819-99.797 262.229 0 362.048 49.92 49.899 115.477 74.837 181.035 74.837s131.093-24.939 181.013-74.837c99.819-99.818 99.819-262.229 0-362.048zm-75.435 256.448c8.341 8.341 8.341 21.824 0 30.165a21.275 21.275 0 0 1-15.083 6.251 21.277 21.277 0 0 1-15.083-6.251l-75.413-75.435-75.392 75.413a21.348 21.348 0 0 1-15.083 6.251 21.277 21.277 0 0 1-15.083-6.251c-8.341-8.341-8.341-21.845 0-30.165l75.392-75.413-75.413-75.413c-8.341-8.341-8.341-21.845 0-30.165 8.32-8.341 21.824-8.341 30.165 0l75.413 75.413 75.413-75.413c8.341-8.341 21.824-8.341 30.165 0 8.341 8.32 8.341 21.824 0 30.165l-75.413 75.413 75.415 75.435z"
                                    fill="#999999" opacity="1" data-original="#000000"></path>
                            </g>
                        </svg>
                    </div>


                <div class="card-body py-0 notification_list px-0 m-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- <ul class="nav nav-pills mb-2 mt-2" id="pills-tab" role="tablist">
                            <li class="nav-item pl-2" role="presentation">
                                <button class="nav-link py-1 def_14_size active rounded-pill d-flex gap-1 align-items-center" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">All <span class="badge bg-danger line_hight1 rounded-pill">50+</span></button>
                            </li>
                        </ul> -->
                        <!-- <a href="#" class="unreadbtn def_12_size text-decoration-underline">Mark all as read</a> -->
                    </div>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <ul v-if="notificationsdata.length > 0" class="notification_listIn">
                        <li v-for="(notification, index) in notificationsdata" :key="index" :class="{ notificationSeen: notification.is_seen == 1 }">
                            <a href="javascript:void(0);" @click="handleNotificationClick(notification)">
                            <div class="media m-0 list_notification_shadow px-2  notification_list_main if_new gap-10 py-2 d-flex align-items-center ">
                                <span class="avatar avatar-md  avatar-rounded avatar_bg1" v-if="notification.notification_for == 'device_added'">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M20 21.75h-4A2.753 2.753 0 0 1 13.25 19v-8A2.753 2.753 0 0 1 16 8.25h4A2.753 2.753 0 0 1 22.75 11v8A2.753 2.753 0 0 1 20 21.75zm-4-12c-.69 0-1.25.56-1.25 1.25v8c0 .69.56 1.25 1.25 1.25h4c.69 0 1.25-.56 1.25-1.25v-8c0-.69-.56-1.25-1.25-1.25zM4.75 5c0-.69.56-1.25 1.25-1.25h12c.69 0 1.25.56 1.25 1.25v2H20c.257 0 .506.03.75.076V5A2.753 2.753 0 0 0 18 2.25H6A2.753 2.753 0 0 0 3.25 5v7h1.5zM12 16.25H5a2.254 2.254 0 0 1-2.122-1.5H12v-1.5H2a.75.75 0 0 0-.75.75A3.754 3.754 0 0 0 5 17.75h7z" fill="#3b6fb0" opacity="1" data-original="#000000" class=""></path></g></svg>
                                </span>
                                <span class="avatar avatar-md  avatar-rounded avatar_bg2" v-if="notification.notification_for == 'claim_raised'">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M352.459 220c0-11.046-8.954-20-20-20h-206c-11.046 0-20 8.954-20 20s8.954 20 20 20h206c11.046 0 20-8.954 20-20zM126.459 280c-11.046 0-20 8.954-20 20s8.954 20 20 20H251.57c11.046 0 20-8.954 20-20s-8.954-20-20-20H126.459z" fill="#ffa500" opacity="1" data-original="#000000" class=""></path><path d="M173.459 472H106.57c-22.056 0-40-17.944-40-40V80c0-22.056 17.944-40 40-40h245.889c22.056 0 40 17.944 40 40v123c0 11.046 8.954 20 20 20s20-8.954 20-20V80c0-44.112-35.888-80-80-80H106.57c-44.112 0-80 35.888-80 80v352c0 44.112 35.888 80 80 80h66.889c11.046 0 20-8.954 20-20s-8.954-20-20-20z" fill="#ffa500" opacity="1" data-original="#000000" class=""></path><path d="M467.884 289.572c-23.394-23.394-61.458-23.395-84.837-.016l-109.803 109.56a20.005 20.005 0 0 0-5.01 8.345l-23.913 78.725a20 20 0 0 0 24.476 25.087l80.725-22.361a19.993 19.993 0 0 0 8.79-5.119l109.573-109.367c23.394-23.394 23.394-61.458-.001-84.854zM333.776 451.768l-40.612 11.25 11.885-39.129 74.089-73.925 28.29 28.29-73.652 73.514zM439.615 346.13l-3.875 3.867-28.285-28.285 3.862-3.854c7.798-7.798 20.486-7.798 28.284 0 7.798 7.798 7.798 20.486.014 28.272zM332.459 120h-206c-11.046 0-20 8.954-20 20s8.954 20 20 20h206c11.046 0 20-8.954 20-20s-8.954-20-20-20z" fill="#ffa500" opacity="1" data-original="#000000" class=""></path></g></svg>
                                </span>
                                <span class="avatar avatar-md  avatar-rounded avatar_bg3" v-if="notification.notification_for == 'repair_request'">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M20 25.985c-.508.204-1.033.375-1.576.5l-.22 2.866a.703.703 0 0 1-.701.649h-3.007a.702.702 0 0 1-.7-.648l-.22-2.866a10.67 10.67 0 0 1-3.274-1.359L8.117 27a.702.702 0 0 1-.954-.037l-2.126-2.126A.703.703 0 0 1 5 23.883l1.873-2.185a10.72 10.72 0 0 1-1.359-3.274l-2.866-.22A.703.703 0 0 1 2 17.503v-3.007c0-.367.283-.672.648-.7l2.866-.22a10.67 10.67 0 0 1 1.359-3.274L5.001 8.117a.702.702 0 0 1 .036-.953l2.126-2.126a.704.704 0 0 1 .954-.037l2.185 1.873a10.72 10.72 0 0 1 3.274-1.359l.22-2.866A.703.703 0 0 1 14.497 2h3.007c.367 0 .672.283.7.648l.013.167a7.49 7.49 0 0 0-.414 7.462A6 6 0 1 0 16 22a5.968 5.968 0 0 0 4-1.537z" fill="#00b6aa" opacity="1" data-original="#000000" class=""></path><path d="M27 11.789V27.5a2.5 2.5 0 1 1-5 0V11.789a5.494 5.494 0 0 1-.176-9.696c.077-.042.176.018.176.106V8l2.5 1L27 8V2.199c0-.088.099-.148.176-.105A5.492 5.492 0 0 1 27 11.789z" fill="#00b6aa" opacity="1" data-original="#000000" class=""></path></g></svg>
                                </span>

                                <span class="avatar avatar-md  avatar-rounded avatar_bg4" v-if="notification.notification_for == 'user_added'">
                                    <svg  viewBox="0 0 32 32"  xmlns="http://www.w3.org/2000/svg" id="fi_2354573"><g id="user_account_people_man" data-name="user, account, people, man"><path d="m23.7373 16.1812a1 1 0 1 0 -1.4062 1.4218 8.9378 8.9378 0 0 1 2.6689 6.397c0 1.2231-3.5059 3-9 3s-9-1.7778-9-3.002a8.9385 8.9385 0 0 1 2.6348-6.3627 1 1 0 1 0 -1.4141-1.4141 10.9267 10.9267 0 0 0 -3.2207 7.7788c0 3.2476 5.667 5 11 5s11-1.7524 11-5a10.92 10.92 0 0 0 -3.2627-7.8188z"></path><path d="m16 17a7 7 0 1 0 -7-7 7.0081 7.0081 0 0 0 7 7zm0-12a5 5 0 1 1 -5 5 5.0059 5.0059 0 0 1 5-5z"></path></g></svg>
                                </span>
                                <span class="avatar avatar-md  avatar-rounded avatar_bg5" v-if="notification.notification_for == 'invoice_paid'">
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 512.001 512.001" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M266.712 176.733h-21.424c-9.447 0-17.133-7.686-17.133-17.134 0-9.447 7.686-17.133 17.133-17.133h42.844c8.284 0 15-6.716 15-15s-6.716-15-15-15h-17.134V95.334c0-8.284-6.716-15-15-15s-15 6.716-15 15v17.337c-23.985 2.175-42.843 22.387-42.843 46.929 0 25.99 21.144 47.134 47.133 47.134h21.424c9.447 0 17.133 7.686 17.133 17.133S276.16 241 266.712 241h-42.847c-8.284 0-15 6.716-15 15s6.716 15 15 15h17.133v17.133c0 8.284 6.716 15 15 15 8.284 0 15-6.716 15-15v-17.337c23.987-2.174 42.847-22.386 42.847-46.93 0-25.989-21.143-47.133-47.133-47.133zM336.333 353.467H175.667c-8.284 0-15 6.716-15 15s6.716 15 15 15h160.666c8.284 0 15-6.716 15-15s-6.716-15-15-15z" fill="#ff5c5c" opacity="1" data-original="#000000" class=""></path><path d="M432.733 0H79.267c-8.284 0-15 6.716-15 15v437.816c0 3.978 1.581 7.793 4.394 10.607l44.184 44.184c5.857 5.858 15.355 5.858 21.213 0l33.577-33.577 33.576 33.577a15 15 0 0 0 21.214 0L256 474.03l33.577 33.577a15 15 0 0 0 21.213 0l33.576-33.577 33.577 33.577c2.929 2.929 6.767 4.394 10.606 4.394s7.678-1.464 10.607-4.394l44.184-44.184a15.002 15.002 0 0 0 4.394-10.607V15c-.001-8.284-6.716-15-15.001-15zm-44.184 475.787-33.577-33.577a15.002 15.002 0 0 0-21.214 0l-33.576 33.577-33.577-33.577c-5.857-5.858-15.355-5.858-21.213 0l-33.577 33.577-33.576-33.577a15 15 0 0 0-21.213 0l-33.577 33.577-29.184-29.184V30h323.467v416.603h.001l-29.184 29.184z" fill="#ff5c5c" opacity="1" data-original="#000000" class=""></path></g></svg>
                                </span>
                                <span class="avatar avatar-md  avatar-rounded avatar_bg6" v-if="notification.notification_for == 'subscription'">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 468 468" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path fill="#8b00e9" d="M89.801 278.87H74.706c-6.627 0-12-5.373-12-12s5.373-12 12-12h15.095c6.627 0 12 5.373 12 12s-5.373 12-12 12zM248.303 218.187h-15.095c-6.627 0-12-5.373-12-12s5.373-12 12-12h15.095c6.627 0 12 5.373 12 12s-5.373 12-12 12zM327.554 218.187h-15.095c-6.627 0-12-5.373-12-12s5.373-12 12-12h15.095c6.627 0 12 5.373 12 12s-5.373 12-12 12zM169.052 218.187h-15.095c-6.627 0-12-5.373-12-12s5.373-12 12-12h15.095c6.627 0 12 5.373 12 12s-5.373 12-12 12zM169.052 339.554h-15.095c-6.627 0-12-5.373-12-12s5.373-12 12-12h15.095c6.627 0 12 5.373 12 12s-5.373 12-12 12zM89.801 339.554H74.706c-6.627 0-12-5.373-12-12s5.373-12 12-12h15.095c6.627 0 12 5.373 12 12s-5.373 12-12 12z" opacity="1" data-original="#333333" class=""></path><circle cx="161.504" cy="265.859" r="20.228" fill="#8b00e9" opacity="1" data-original="#333333" class=""></circle><g fill="#333"><path d="M343.736 219.472c-34.75 0-66.207 14.344-88.78 37.414a11.942 11.942 0 0 0-6.653-2.015h-15.095c-6.627 0-12 5.373-12 12s5.373 12 12 12h4.578c-11.61 18.894-18.315 41.11-18.315 64.865 0 68.52 55.745 124.265 124.264 124.265S468 412.255 468 343.735s-55.745-124.263-124.264-124.263zm0 224.528c-55.286 0-100.264-44.979-100.264-100.265 0-55.285 44.979-100.264 100.264-100.264S444 288.45 444 343.735C444 399.021 399.021 444 343.736 444z" fill="#8b00e9" opacity="1" data-original="#333333" class=""></path><path d="M376.26 32.403h-31.65V12c0-6.627-5.373-12-12-12s-12 5.373-12 12v20.403h-63.654V12c0-6.627-5.373-12-12-12s-12 5.373-12 12v20.403h-63.654V12c0-6.627-5.373-12-12-12s-12 5.373-12 12v20.403H81.649V12c0-6.627-5.373-12-12-12s-12 5.373-12 12v20.403H26c-14.336 0-26 11.663-26 26V376.26c0 14.337 11.664 26 26 26h155.54c6.627 0 12-5.373 12-12s-5.373-12-12-12H26c-1.103 0-2-.897-2-2V143.186h354.26v28.354c0 6.627 5.373 12 12 12s12-5.373 12-12V58.403c0-14.337-11.664-26-26-26z" fill="#8b00e9" opacity="1" data-original="#333333" class=""></path><circle cx="400.451" cy="356.891" r="14" fill="#8b00e9" opacity="1" data-original="#333333" class=""></circle><path d="m385.827 290.789-11.397-24.307c-2.814-6.001-9.961-8.582-15.959-5.771-5.436 2.55-8.053 8.652-6.407 14.24a66.717 66.717 0 0 0-7.884-.483c-38.134 0-69.158 32.238-69.158 71.864 0 6.627 5.373 12 12 12s12-5.373 12-12c0-26.37 20.224-47.826 45.095-47.862-5.468 9.698 4.689 21.282 15.046 16.977l20.407-8.482c6.101-2.538 9.09-10.133 6.257-16.176zM387.333 387.479c-4.02-5.27-11.55-6.281-16.819-2.263-7.703 5.876-16.81 8.982-26.335 8.982-4.66 0-9.244-.775-13.606-2.241 3.705-2.582 5.798-7.146 4.977-11.889-1.131-6.53-7.338-10.911-13.871-9.777l-22.364 3.871c-6.836 1.185-11.381 8.345-9.512 15.048l6.454 23.14c3.068 10.999 18.875 11.587 22.811.915 7.919 3.217 16.42 4.934 25.111 4.934 14.831 0 28.971-4.807 40.892-13.9 5.269-4.021 6.282-11.551 2.262-16.82z" fill="#8b00e9" opacity="1" data-original="#333333" class=""></path></g></g></svg>
                                </span>
                                <span class="avatar avatar-md  avatar-rounded avatar_bg7" v-if="notification.notification_for == 'shipping_supply'">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 461.103 461.103" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M325.949 350.032a13.84 13.84 0 0 0-12.749-12.749c-7.622.581-13.329 7.23-12.749 14.851a13.84 13.84 0 0 0 12.749 12.749c7.622-.58 13.329-7.23 12.749-14.851zM359.709 338.192a13.84 13.84 0 0 0-12.749-12.749c-7.622.581-13.329 7.23-12.749 14.851a13.84 13.84 0 0 0 12.749 12.749c7.622-.58 13.329-7.23 12.749-14.851z" fill="#00ab03" opacity="1" data-original="#000000" class=""></path><path d="M461.04 127.683v-.64a8.05 8.05 0 0 0-.64-1.28l-.48-.64-.96-1.04-.56-.48-1.04-.48h-.4L283.2 52.083a8.004 8.004 0 0 0-6 0l-173.36 69.84h-.4l-1.04.48-.56.48-.96 1.04-.48.64a8.05 8.05 0 0 0-.64 1.28v.64a7.968 7.968 0 0 0 0 2v64H8a8 8 0 0 0 0 16v1.04h90.8v27.36H67.68a8 8 0 0 0 0 16H98.8v27.36H25.04a8 8 0 0 0 0 16H98.8v33.44a8 8 0 0 0 4.96 7.36l173.44 72a8.005 8.005 0 0 0 2.72.56 8.005 8.005 0 0 0 2.72-.56l173.44-72a8 8 0 0 0 4.96-7.36v-200a7.968 7.968 0 0 0 0-2zm-180.88-59.36 65.76 26.32-149.6 63.28-68.4-28.48 152.24-61.12zm-8.24 321.44-157.12-65.44v-28.16H148a8 8 0 0 0 0-16h-33.2v-27.36h76a8 8 0 0 0 0-16h-76v-27.28h16a8 8 0 0 0 0-16h-16v-52.16l61.12 25.28 96 40v183.12zm8.32-196.64-63.2-26.48 149.76-63.52 65.52 26.4-95.44 40-56.64 23.6zm7.92 196.64v-183.12l45.04-18.72 112-46.72.4 183.12-157.44 65.44z" fill="#00ab03" opacity="1" data-original="#000000" class=""></path></g></svg>
                                </span>
                                <div class="media-body flex-grow-1">
                                    <p class="mb-1">
                                       {{ notification.message }}
                                    </p>
                                    <div class="media calendar_div gap-10 border-20 mt-1 d-flex align-items-center">

                                        <div class="media-body date_text" v-if="notification.notification_for == 'device_added'">
                                            <span class="notification_label">Devices</span> | {{ formatDate(notification.created_at) }}
                                        </div>
                                        <div class="media-body date_text" v-if="notification.notification_for == 'claim_raised' && notification.is_claim_status_changed == 1">
                                            <span class="notification_label">Claim Raised</span> | {{ formatDate(notification.created_at) }}
                                        </div>
                                        <div class="media-body date_text" v-if="notification.notification_for == 'repair_request' && notification.is_repair_status_changed == 1">
                                            <span class="notification_label">Repair Request</span> | {{ formatDate(notification.created_at) }}
                                        </div>
                                        <div class="media-body date_text" v-if="notification.notification_for == 'claim_raised' && notification.is_claim_status_changed == 2">
                                            <span class="notification_label">Claim Status</span> | {{ formatDate(notification.created_at) }}
                                        </div>
                                        <div class="media-body date_text" v-if="notification.notification_for == 'repair_request' && notification.is_repair_status_changed == 2">
                                            <span class="notification_label">Repair Status</span> | {{ formatDate(notification.created_at) }}
                                        </div>
                                        <div class="media-body date_text" v-if="notification.notification_for == 'user_added'">
                                            <span class="notification_label">Users</span> | {{ formatDate(notification.created_at) }}
                                        </div>
                                        <div class="media-body date_text" v-if="notification.notification_for == 'invoice_paid'">
                                            <span class="notification_label">Invoice</span> | {{ formatDate(notification.created_at) }}
                                        </div>
                                        <div class="media-body date_text" v-if="notification.notification_for == 'subscription'">
                                            <span class="notification_label">Subscriptions</span> | {{ formatDate(notification.created_at) }}
                                        </div>
                                        <div class="media-body date_text" v-if="notification.notification_for == 'shipping_supply'">
                                            <span class="notification_label">Shipping Supply Boxes</span> | {{ formatDate(notification.created_at) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="h-100 d-flex align-items-center arrow_svg">
                                    <svg id="fi_2985179" enable-background="new 0 0 128 128" height="18" viewBox="0 0 128 128" width="18" xmlns="http://www.w3.org/2000/svg"><path id="Right_Arrow_4_" d="m44 108c-1.023 0-2.047-.391-2.828-1.172-1.563-1.563-1.563-4.094 0-5.656l37.172-37.172-37.172-37.172c-1.563-1.563-1.563-4.094 0-5.656s4.094-1.563 5.656 0l40 40c1.563 1.563 1.563 4.094 0 5.656l-40 40c-.781.781-1.805 1.172-2.828 1.172z"></path></svg>
                                </div>
                            </div>
                            </a>
                        </li>
                    </ul>
                    <p class="text-center mt-2 text-muted  pt-2 def_14_size" v-else>No New Notifications Found.</p>
                    </div>
                    </div>
                </div>
            </div>
                <div class="card-footer border-top-0 d-flex justify-content-center py-2 modal-header">
                    <a href="#" class="view_all rounded-pill text-decoration-none def_14_size" @click="markAsSeen">View All</a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        notificationsdata: {
            type: Array,
            required: false,
            default: () => []
        },
        notificationsvisible: {
            type: Boolean,
            required: false,
            default: false
        }
    },
    emits: ['close-notifications'],
    methods: {

        /** Hide Notification Pop-up */
        hideNotification() {
            this.$emit('close-notifications');
        },
        /** Mark Seen to all */
        markAsSeen() {
            axios.get(`${this.$userAppUrl}smarttiusadmin/notifications/markSeen`)
            .then(() => {
                // optionally update UI or refetch notifications
                window.location.href = `${this.$userAppUrl}smarttiusadmin/notifications`;
            });
        },

        /** Mark seen to particular notification only */
        handleNotificationClick(notification) {
            // Construct the full target URL
            let targetUrl = `${this.$userAppUrl}${notification.admin_target_link}?openPopup=true`;

            if (notification.user_id && notification.notification_for == 'user_added') {
            targetUrl += `&userId=${notification.user_id}`;
            }
            if (notification.device_id) {
            targetUrl += `&deviceId=${notification.device_id}`;
            }
            if (notification.device_claim_id) {
            targetUrl += `&claimId=${notification.device_claim_id}`;
            }
            if (notification.device_repair_id) {
            targetUrl += `&repairId=${notification.device_repair_id}`;
            }
            if (notification.shipping_supply_id) {
            targetUrl += `&supplyId=${notification.shipping_supply_id}`;
            }

            // API call to mark as seen
            axios.post(`${this.$userAppUrl}smarttiusadmin/notification/single-markSeen`, {
              notificationId: notification.id
            }).then(() => {
            // Redirect after marking as seen
            window.location.href = targetUrl;
            }).catch(() => {
            // Optional fallback: redirect anyway if request fails
            window.location.href = targetUrl;
            });
        },

        formatDate(dateString){
            if (!dateString) return '';
            const rawString = String(dateString);
            const cleanedDateString = rawString
                .replace(' ', 'T')                /** Ensure ISO T separator  */
                .replace(/\.\d+Z$/, '')           /** Strip microseconds + Z */
                .replace(/Z$/, '');               /** Remove Z if not matched above */

            return formatDateTimeAndTimeZone(cleanedDateString);
        }
    }
}
</script>
