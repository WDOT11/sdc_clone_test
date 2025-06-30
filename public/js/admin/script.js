document.addEventListener("DOMContentLoaded", function () {
    let arrow = document.querySelectorAll(".main_menu_item");
    let sidebar = document.querySelector(".sidebar");
    let sidebarBtn = document.querySelector(".bx-menu");
    let slideclose = document.querySelector(".close_slidebtn");

    if (!sidebar || !sidebarBtn) return;

    /** ✅ Get screen width */
    let screenWidth = window.innerWidth;

    /** ✅ Only apply sidebarState from localStorage on DESKTOP */
    if (screenWidth >= 1350) {
        if (localStorage.getItem("sidebarState") === "closed") {
            sidebar.classList.add("close");
        } else {
            sidebar.classList.remove("close");
        }
    } else {
        // Tablet & Mobile: Always keep open and remove saved state
        sidebar.classList.remove("close");
        localStorage.removeItem("sidebarState");
    }

    /** ⬇️ Dropdown toggle for menu items */
    arrow.forEach((arrowElement) => {
        arrowElement.addEventListener("click", (e) => {
            let arrowParent = e.target.closest(".main_menu_item");
            if (arrowParent) {
                arrowParent.classList.toggle("downmenu");
            }
        });
    });

    /** Sidebar toggle button */
    sidebarBtn.addEventListener("click", (e) => {
        sidebar.classList.toggle("close");

        let screenWidth = window.innerWidth;
        if (screenWidth >= 1350) {
            if (sidebar.classList.contains("close")) {
                localStorage.setItem("sidebarState", "closed");
            } else {
                localStorage.setItem("sidebarState", "open");
            }
        }
    });

    /** Slide close button */
    slideclose?.addEventListener("click", (e) => {
        sidebar.classList.toggle("close");

        let screenWidth = window.innerWidth;
        if (screenWidth >= 1350) {
            if (sidebar.classList.contains("close")) {
                localStorage.setItem("sidebarState", "closed");
            } else {
                localStorage.setItem("sidebarState", "open");
            }
        }
    });

    /** Remove extra modal backdrops */
    document.addEventListener("shown.bs.modal", function () {
        const backdrops = document.querySelectorAll(".modal-backdrop");
        backdrops.forEach((el, i) => {
            if (i > 0 && el?.parentNode) el.remove();
        });
    });

    document.addEventListener("hidden.bs.modal", function () {
        const backdrops = document.querySelectorAll(".modal-backdrop");
        backdrops.forEach((el, i) => {
            if (i > 0 && el?.parentNode) el.remove();
        });
    });

    /** ✅ Tablet width logic (1025–1349px): just remove saved state */
    function checkScreenSize() {
        let screenWidth = jQuery(window).width();
        if (screenWidth >= 1025 && screenWidth < 1350) {
            // sidebar.classList.remove("close");
            localStorage.removeItem("sidebarState");
        }
    }

    /* Initial check */
    checkScreenSize();

    /* Check on window resize */
    jQuery(window).resize(function () {
        checkScreenSize();
    });

    /** 📱 Optional: Close sidebar on outside click in mobile view */
    if (screenWidth < 1024) {
        document.body.addEventListener("click", (e) => {
            if (!sidebar.contains(e.target) && !sidebarBtn.contains(e.target)) {
                sidebar.classList.remove("close");
                localStorage.removeItem("sidebarState");
            }
        });
    }
});


jQuery(document).on("click", ".action_toSlid", function (event) {
    jQuery(".dataSlide_bar_wrap").addClass("onopen_slide");
    jQuery(".open_notifSlid")
        .addClass("onopen_closeslide")
        .removeClass("action_toSlid");
    jQuery(".notifoveropen2").show();
    jQuery("body").addClass("body_sidebar");
    /* jQuery("body").removeClass("body_sidebar"); */
    event.stopPropagation(); /** Prevent event from bubbling up */
});

jQuery(document).on("click", ".close_btn", function (event) {
    jQuery(".dataSlide_bar_wrap").removeClass("onopen_slide");
    jQuery(".notifoveropen2").hide();
    jQuery(".open_notifSlid")
        .addClass("action_toSlid")
        .removeClass("onopen_closeslide");
    jQuery("body").removeClass("body_sidebar");
    const cleanUrl = window.location.origin + window.location.pathname;
    window.history.replaceState({}, document.title, cleanUrl);
    event.stopPropagation(); /* Prevent triggering document click event */
});

jQuery(document).on("click", function (event) {
    if (
        !jQuery(event.target).closest(".dataSlide_bar_wrap, .open_notifSlid")
            .length
    ) {
        jQuery(".dataSlide_bar_wrap").removeClass("onopen_slide");
        jQuery(".notifoveropen2").hide();
        jQuery(".open_notifSlid")
            .addClass("action_toSlid")
            .removeClass("onopen_closeslide");
        jQuery("body").removeClass("body_sidebar");
        const cleanUrl = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }
});

/**
jQuery(document).on("click", ".action_to, .bubble_badge", function (event) {
    jQuery(".notificaton_bar_wrap").addClass("onopen_notification");
    jQuery(".open_notif").addClass("onopen_close").removeClass("action_to");
    event.stopPropagation(); // Prevent event from bubbling up
});
*/

/** Click on .onopen_close to close the notification */
/** jQuery(document).on("click", ".onopen_close", function (event) {
    jQuery(".notificaton_bar_wrap").removeClass("onopen_notification");
    jQuery(".open_notif").addClass("action_to").removeClass("onopen_close");
    event.stopPropagation(); // Prevent triggering document click event
});

jQuery(document).on("click", ".notification_closemaster", function (event) {
    jQuery(".notificaton_bar_wrap").removeClass("onopen_notification");
    jQuery(".open_notif").addClass("action_to").removeClass("onopen_close");
    event.stopPropagation();
});
*/

/** Click anywhere outside to close the notification */
jQuery(document).on("click", function (event) {
    if (
        !jQuery(event.target).closest(".notificaton_bar_wrap, .open_notif")
            .length
    ) {
        jQuery(".notificaton_bar_wrap").removeClass("onopen_notification");
        jQuery(".open_notif").addClass("action_to").removeClass("onopen_close");
        jQuery(".notifoveropen").hide();
        /* jQuery("body").addClass("body_sidebar"); */
        jQuery("body").removeClass("body_sidebar");
    }
});

document.addEventListener("DOMContentLoaded", function () {
    /* wawe chart */
    /*
    let messageCounts = [3, 5, 7, 6, 8, 10, 12, 14, 9, 11, 13, 15]; // Predefined data

    // const ctxsc = document.getElementById('usersChart').getContext('2d');
    const usersChartCanvas = document.getElementById('usersChart');
    // Check if usersChart exists before proceeding
    if (usersChartCanvas) {
        const ctxsc = usersChartCanvas.getContext('2d');
        const usersChart = new Chart(ctxsc, {
            type: 'line',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    data: messageCounts,
                    borderColor: '#2CA058',
                    backgroundColor: 'rgba(44, 160, 88, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 20
                    }
                }
            }
        });
    }
    */

    /* line chart start */
    /* const ctxm = document.getElementById('revenueChart').getContext('2d'); */
    const revenueChartCanvas = document.getElementById("revenueChart");
    /* Check if revenueChartCanvas exists before proceeding */
    if (revenueChartCanvas) {
        const ctxm = revenueChartCanvas.getContext("2d");
        new Chart(ctxm, {
            type: "line",
            data: {
                labels: [
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                ],
                datasets: [
                    {
                        label: "Total Revenue",
                        data: [10, 9, 5, 4, 1, 6, 9, 8, 7, 6, 3, 4],
                        borderColor: "red",
                        backgroundColor: "rgba(255, 0, 0, 0.1)",
                        borderWidth: 2,
                        pointBackgroundColor: "red",
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 10,
                    },
                },
            },
        });
    }
});

/** Format date with time and timezone */
function formatDateTimeAndTimeZone(dateString) {
    if (!dateString) return '';
    /* const date = new Date(dateString); */
    const date = new Date(dateString.replace(' ', 'T') + 'Z');
    const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    return date.toLocaleDateString('en-US', { timeZone: userTimeZone, month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit',});
}
/** Format only date without time and timezone */
function formatDateAndTimeZone(dateString) {
    if (!dateString) return '';
    /* const date = new Date(dateString); */
    const date = new Date(dateString.replace(' ', 'T') + 'Z');
    const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    return date.toLocaleDateString('en-US', { timeZone: userTimeZone, month: 'long', day: 'numeric', year: 'numeric'});
}

/** Format Digits */
function formattedNumber(number){
  if (!number) return 0;
  const userLocale = navigator.language || 'en-US';
  return Number(number).toLocaleString(userLocale);
}

/** Function to show overlay */
function show_overlay() {
    jQuery(".notifoveropen2").show();
}

/** Function to add class */
function to_add_class() {
    jQuery("body").addClass("body_sidebar");
}
/** Function to remove class */
function to_remove_class() {
    jQuery("body").removeClass("body_sidebar");
}

/** Functions to show and hide the loader */
function show_ajax_loader() {
    jQuery("#admin_ajax_loader").show();
}

function hide_ajax_loader() {
    jQuery("#admin_ajax_loader").hide();
}

/** Admin Popup  show */
function show_admin_popup(modalId) {
    jQuery(modalId).modal("show");
}

/** Function to hide the popups */
function hide_admin_popup(modalId) {
    jQuery(modalId).click();
}

/** Functions to show and hide (view section) loader */
function show_ajax_view_animation_loader() {
    jQuery("#skeleton-wrapper").show();
}

function hide_ajax_view_animation_loader() {
    jQuery("#skeleton-wrapper").hide();
}
jQuery(document).ready(function () {
    const container = $("#sidebar_menu");
    const activeItem = $(".active");
    if (container.length && activeItem.length) {
        /* Calculate the scroll position to center the active item */
        const scrollPosition =
            activeItem.position().top +
            container.scrollTop() -
            container.height() / 2;
        container.scrollTop(scrollPosition);
    }


    /*
    jQuery(".thistheme").click(function () {
        jQuery("body").toggleClass("thememasterclass");
    });

    // On page load, apply previously selected theme
    var activeIndex = localStorage.getItem("activeLiIndex");

    if (activeIndex !== null) {
        activeIndex = parseInt(activeIndex);
        jQuery(".theme_box li").removeClass("active");
        jQuery(".theme_box li").eq(activeIndex).addClass("active");

        if (activeIndex === 1) {
            jQuery("body").addClass("thememasterclass");
        } else {
            jQuery("body").removeClass("thememasterclass");
        }
    }

    // Click on li elements
    jQuery(".theme_box li").click(function () {
        var index = jQuery(this).index();

        jQuery(".theme_box li").removeClass("active");
        jQuery(this).addClass("active");
        localStorage.setItem("activeLiIndex", index);

        if (index === 1) {
            jQuery("body").addClass("thememasterclass");
        } else {
            jQuery("body").removeClass("thememasterclass");
        }
    });

    // Toggle theme when .thistheme button is clicked
    jQuery(".thistheme").click(function () {
        var $secondLi = jQuery(".theme_box li").eq(1);

        if ($secondLi.hasClass("active")) {
            // Already active, remove it
            $secondLi.removeClass("active");
            jQuery("body").removeClass("thememasterclass");
            localStorage.removeItem("activeLiIndex");
        } else {
            // Not active, activate it
            jQuery(".theme_box li").removeClass("active");
            $secondLi.addClass("active");
            jQuery("body").addClass("thememasterclass");
            localStorage.setItem("activeLiIndex", 1);
        }
    });
    */
});
jQuery(document).on('mouseenter', '.sidebar.close', function() {
    jQuery(this).addClass("hoverslide");
}).on('mouseleave', '.sidebar.close', function() {
    jQuery(this).removeClass("hoverslide");
});
document.addEventListener("DOMContentLoaded", function () {
 jQuery(function () {
	   var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
	    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		    return new bootstrap.Tooltip(tooltipTriggerEl);
	    });


	   jQuery('[data-toggle="tooltip"]').tooltip();
    })
});
jQuery(document).ready(function () {
  jQuery('.edituser_img').click(function () {
   jQuery('#update_profile_img').click();
  });
});