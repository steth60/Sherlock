"use strict";

// Function declarations
let slideUp = (target, duration = 0) => {
    if (target) {
        target.style.transitionProperty = 'height, margin, padding';
        target.style.transitionDuration = duration + 'ms';
        target.style.boxSizing = 'border-box';
        target.style.height = target.offsetHeight + 'px';
        target.offsetHeight;
        target.style.overflow = 'hidden';
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
    }
}

let slideDown = (target, duration = 0) => {
    if (target) {
        target.style.removeProperty('display');
        let display = window.getComputedStyle(target).display;

        if (display === 'none')
            display = 'block';

        target.style.display = display;
        let height = target.offsetHeight;
        target.style.overflow = 'hidden';
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
        target.offsetHeight;
        target.style.boxSizing = 'border-box';
        target.style.transitionProperty = "height, margin, padding";
        target.style.transitionDuration = duration + 'ms';
        target.style.height = height + 'px';
        target.style.removeProperty('padding-top');
        target.style.removeProperty('padding-bottom');
        target.style.removeProperty('margin-top');
        target.style.removeProperty('margin-bottom');
        window.setTimeout(() => {
            target.style.removeProperty('height');
            target.style.removeProperty('overflow');
            target.style.removeProperty('transition-duration');
            target.style.removeProperty('transition-property');
        }, duration);
    }
}

let slideToggle = (target, duration = 0) => {
    if (target) {
        if (window.getComputedStyle(target).display === 'none') {
            return slideDown(target, duration);
        } else {
            return slideUp(target, duration);
        }
    }
}

// Helper function to safely query DOM elements
function $(selector) {
    return document.querySelector(selector);
}

// Helper function to safely add event listeners
function addEventListenerSafe(element, event, handler) {
    if (element) {
        element.addEventListener(event, handler);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    if (typeof togglemenu === 'function') {
        togglemenu();
    }
    if (typeof menuclick === 'function') {
        menuclick();
    }
    if (typeof menuhrres === 'function') {
        menuhrres();
    }
    var vw = window.innerWidth;

    // Initialize Bootstrap components if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
    }

    // Mobile menu toggle
    addEventListenerSafe($(".mobile-menu"), 'click', function () {
        this.classList.toggle('on');
    });

    // Mobile collapse
    addEventListenerSafe($("#mobile-collapse"), 'click', function () {
        $(".pcoded-navbar").classList.toggle('navbar-collapsed');
    });

    // Search functionality
    addEventListenerSafe($(".search-btn"), 'click', function () {
        $(".main-search").classList.add('open');
        let formControl = $(".main-search .form-control");
        if (formControl) {
            formControl.style.width = '90px';
        }
    });

    addEventListenerSafe($(".search-close"), 'click', function () {
        $(".main-search").classList.remove('open');
        let formControl = $(".main-search .form-control");
        if (formControl) {
            formControl.style.width = '0px';
        }
    });

    // Chat functionality
    addEventListenerSafe($('.displayChatbox'), 'click', function () {
        $(".header-user-list").classList.toggle('open');
    });

    var userlistBoxes = document.querySelectorAll(".header-user-list .userlist-box");
    userlistBoxes.forEach(function(elem) {
        elem.addEventListener('click', function () {
            $(".header-chat").classList.add('open');
            $(".header-user-list").classList.toggle('msg-open');
        });
    });

    addEventListenerSafe($('.h-back-user-list'), 'click', function () {
        $(".header-chat").classList.remove('open');
        $(".header-user-list").classList.remove('msg-open');
    });

    addEventListenerSafe($('.h-close-text'), 'click', function () {
        $(".header-chat").classList.remove('open');
        $(".header-user-list").classList.remove('open');
        $(".header-user-list").classList.remove('msg-open');
    });

    // Responsive adjustments
    if (vw <= 991) {
        let mainSearch = $(".main-search");
        if (mainSearch) {
            mainSearch.classList.add('open');
            let formControl = mainSearch.querySelector('.form-control');
            if (formControl) {
                formControl.style.width = '90px';
            }
        }
    }

    // Initialize PerfectScrollbar if available
    if (typeof PerfectScrollbar !== 'undefined') {
        if (vw >= 1024) {
            if ($('.main-friend-cont')) {
                new PerfectScrollbar('.main-friend-cont', {
                    wheelSpeed: .5,
                    swipeEasing: 0,
                    suppressScrollX: true,
                    wheelPropagation: 1,
                    minScrollbarLength: 40,
                });
            }
            if ($('.main-chat-cont')) {
                new PerfectScrollbar('.main-chat-cont', {
                    wheelSpeed: .5,
                    swipeEasing: 0,
                    suppressScrollX: true,
                    wheelPropagation: 1,
                    minScrollbarLength: 40,
                });
            }
        }
        if ($('.noti-body')) {
            new PerfectScrollbar('.notification .noti-body', {
                wheelSpeed: .5,
                swipeEasing: 0,
                suppressScrollX: true,
                wheelPropagation: 1,
                minScrollbarLength: 40,
            });
        }
    }

    // Menu scroll
    if ($('.pcoded-navbar')) {
        if (!$('.pcoded-navbar').classList.contains('theme-horizontal')) {
            if (vw < 992 || $('.pcoded-navbar').classList.contains('menupos-static')) {
                new PerfectScrollbar('.navbar-content', {
                    wheelSpeed: .5,
                    swipeEasing: 0,
                    suppressScrollX: true,
                    wheelPropagation: 1,
                    minScrollbarLength: 40,
                });
            }
        }
    }

    // Horizontal menu
    if ($('.pcoded-navbar') && $('.pcoded-navbar').classList.contains('theme-horizontal')) {
        rmactive();
        horizontalmenu();
    }

    // Remove pre-loader
    setTimeout(function () {
        let loaderBg = $('.loader-bg');
        if (loaderBg) {
            loaderBg.remove();
        }
    }, 400);
});

// Functions
function horizontalmenu() {
    var vw = window.innerWidth;
    let pcodedNavbar = document.querySelector(".pcoded-navbar");
    if (pcodedNavbar && pcodedNavbar.classList.contains('theme-horizontal')) {
        if (vw < 992) {
            pcodedNavbar.classList.remove("theme-horizontal");
        }
    }
}

window.addEventListener("resize", function () {
    if (typeof togglemenu === 'function') {
        togglemenu();
    }
    if (typeof menuhrres === 'function') {
        menuhrres();
    }
    let pcodedNavbar = document.querySelector('.pcoded-navbar');
    if (pcodedNavbar && pcodedNavbar.classList.contains('theme-horizontal')) {
        rmactive();
        horizontalmenu();
    }
    let body = document.querySelector('body');
    if (body && (body.classList.contains('layout-6') || body.classList.contains('layout-7'))) {
        // Define the togglemenulayout function or remove this call if not necessary
    }
});

function rmactive() {
    var elem = document.querySelectorAll(".pcoded-navbar li.pcoded-hasmenu");
    for (var j = 0; j < elem.length; j++) {
        elem[j].classList.remove("active");
        elem[j].classList.remove("pcoded-trigger");
        if (elem[j].children[1]) {
            elem[j].children[1].removeAttribute("style");
        }
    }
}

function menuhrres() {
    let body = document.querySelector('body');
    if (body && body.classList.contains('theme-horizontal')) {
        var vw = window.innerWidth;
        if (vw < 992) {
            setTimeout(function () {
                let sidenavHorizontalWrapper = document.querySelector(".sidenav-horizontal-wrapper");
                if (sidenavHorizontalWrapper) {
                    sidenavHorizontalWrapper.classList.add("sidenav-horizontal-wrapper-dis");
                    sidenavHorizontalWrapper.classList.remove("sidenav-horizontal-wrapper");
                }
                let themeHorizontal = document.querySelector(".theme-horizontal");
                if (themeHorizontal) {
                    themeHorizontal.classList.add("theme-horizontal-dis");
                    themeHorizontal.classList.remove("theme-horizontal");
                }
            }, 400);
        } else {
            setTimeout(function () {
                let sidenavHorizontalWrapperDis = document.querySelector(".sidenav-horizontal-wrapper-dis");
                if (sidenavHorizontalWrapperDis) {
                    sidenavHorizontalWrapperDis.classList.add("sidenav-horizontal-wrapper");
                    sidenavHorizontalWrapperDis.classList.remove("sidenav-horizontal-wrapper-dis");
                }
                let themeHorizontalDis = document.querySelector(".theme-horizontal-dis");
                if (themeHorizontalDis) {
                    themeHorizontalDis.classList.add("theme-horizontal");
                    themeHorizontalDis.classList.remove("theme-horizontal-dis");
                }
            }, 400);
        }
        // Menu scroll
        setTimeout(function () {
            let pcodedNavbar = document.querySelector('.pcoded-navbar');
            if (pcodedNavbar && pcodedNavbar.classList.contains('theme-horizontal-dis')) {
                let sidenavHorizontalWrapperDis = document.querySelector(".sidenav-horizontal-wrapper-dis");
                if (sidenavHorizontalWrapperDis) {
                    sidenavHorizontalWrapperDis.style.height = '100%';
                    sidenavHorizontalWrapperDis.style.position = 'relative';
                    if (sidenavHorizontalWrapperDis) {
                        new PerfectScrollbar('.sidenav-horizontal-wrapper-dis', {
                            wheelSpeed: .5,
                            swipeEasing: 0,
                            suppressScrollX: true,
                            wheelPropagation: 1,
                            minScrollbarLength: 40,
                        });
                    }
                }
            }
        }, 1000);
    }
}

function togglemenu() {
    var vw = window.innerWidth;
    let pcodedNavbar = document.querySelector(".pcoded-navbar");
    if (pcodedNavbar && !pcodedNavbar.classList.contains('theme-horizontal')) {
        if (vw <= 1200 && vw >= 992) {
            pcodedNavbar.classList.add("navbar-collapsed");
        }
        if (vw < 992) {
            pcodedNavbar.classList.remove("navbar-collapsed");
        }
    }
}

// Menu click for tab Layout start
var tablayclick = document.querySelector('.layout1-nav > ul > li');
if (tablayclick) {
    console.log("condition");
    var tc = document.querySelectorAll('.layout1-nav > ul > li');
    for (var t = 0; t < tc.length; t++) {
        var c = tc[t];
        c.addEventListener('click', function (event) {
            var targetElement = event.target;
            if (targetElement.tagName == "A") {
                targetElement = targetElement.parentNode;
            }
            if (targetElement.tagName == "I") {
                targetElement = targetElement.parentNode.parentNode;
            }
            var tempcont = targetElement.children[0].getAttribute('data-cont');
            let activeSidelink = document.querySelector('.navbar-content .sidelink.active');
            if (activeSidelink) {
                activeSidelink.classList.remove('active');
            }
            let activeLayout1Nav = document.querySelector('.layout1-nav > ul > li.active');
            if (activeLayout1Nav) {
                activeLayout1Nav.classList.remove('active');
            }
            targetElement.classList.add('active');
            console.log(tempcont);
            let newActiveSidelink = document.querySelector('.navbar-content .sidelink.' + tempcont);
            if (newActiveSidelink) {
                newActiveSidelink.classList.add('active');
            }
        });
    }
}
// Menu click for tab Layout end

var tablaymenuclick = document.querySelector('.layout-1 .toggle-sidemenu');
if (tablaymenuclick) {
    tablaymenuclick.addEventListener('click', function () {
        let pcodedNavbar = document.querySelector(".pcoded-navbar");
        if (pcodedNavbar) {
            if (pcodedNavbar.classList.contains('hide-sidemenu')) {
                pcodedNavbar.classList.remove('hide-sidemenu');
            } else {
                pcodedNavbar.classList.add('hide-sidemenu');
            }
        }
    });
}

// toggle full screen
function toggleFullScreen() {
    var a = window.innerHeight - 10;

    if (!document.fullscreenElement && // alternative standard method
        !document.mozFullScreenElement && !document.webkitFullscreenElement) { // current working methods
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
    let fullScreenIcon = document.querySelector('.full-screen > i');
    if (fullScreenIcon) {
        fullScreenIcon.classList.toggle('icon-maximize');
        fullScreenIcon.classList.toggle('icon-minimize');
    }
}

// Menu click for main layout
function menuclick() {
    var vw = window.innerWidth;
    var elem = document.querySelectorAll(".pcoded-navbar li");
    for (var j = 0; j < elem.length; j++) {
        elem[j].removeEventListener("click", function () {});
    }
    if (!document.querySelector("body").classList.contains("minimenu")) {
        var elem = document.querySelectorAll(".pcoded-navbar li:not(.pcoded-trigger) .pcoded-submenu");
        for (var j = 0; j < elem.length; j++) {
            elem[j].style.display = "none";
        }
        var pclinkclick = document.querySelectorAll(".pcoded-inner-navbar > li:not(.pcoded-menu-caption)");
        for (var i = 0; i < pclinkclick.length; i++) {
            pclinkclick[i].addEventListener("click", function (event) {
                event.stopPropagation();
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                if (targetElement.parentNode.classList.contains("pcoded-trigger")) {
                    targetElement.parentNode.classList.remove("pcoded-trigger");
                    slideUp(targetElement.parentNode.children[1], 200);
                } else {
                    var tc = document.querySelectorAll("li.pcoded-trigger");
                    for (var t = 0; t < tc.length; t++) {
                        var c = tc[t];
                        c.classList.remove("pcoded-trigger");
                        slideUp(c.children[1], 200);
                    }
                    targetElement.parentNode.classList.add("pcoded-trigger");
                    var tmp = targetElement.children[1];
                    if (tmp) {
                        slideDown(targetElement.parentNode.children[1], 200);
                    }
                }
            });
        }
        var pcsublinkclick = document.querySelectorAll(".pcoded-inner-navbar > li:not(.pcoded-menu-caption) li");
        for (var i = 0; i < pcsublinkclick.length; i++) {
            pcsublinkclick[i].addEventListener("click", function (event) {
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                event.stopPropagation();
                if (targetElement.parentNode.classList.contains("pcoded-trigger")) {
                    targetElement.parentNode.classList.remove("pcoded-trigger");
                    slideUp(targetElement.parentNode.children[1], 200);
                } else {
                    var tc = targetElement.parentNode.parentNode.children;
                    for (var t = 0; t < tc.length; t++) {
                        var c = tc[t];
                        c.classList.remove("pcoded-trigger");
                        if (c.tagName == "LI") {
                            c = c.children[0];
                        }
                        if (c.parentNode.classList.contains("pcoded-hasmenu")) {
                            slideUp(c.parentNode.children[1], 200);
                        }
                    }
                    targetElement.parentNode.classList.add("pcoded-trigger");
                    var tmp = targetElement.parentNode.children[1];
                    if (tmp) {
                        tmp.removeAttribute('style');
                        slideDown(tmp, 200);
                    }
                }
            });
        }
    }
}

// Menu click for mobile layout
if (!!document.querySelector('#mobile-collapse1')) {
    document.querySelector("#mobile-collapse1").addEventListener("click", function (e) {
        var vw = window.innerWidth;
        if (vw < 992) {
            let pcodedNavbar = document.querySelector(".pcoded-navbar");
            if (pcodedNavbar) {
                if (pcodedNavbar.classList.contains('mob-open')) {
                    pcodedNavbar.classList.remove('mob-open');
                } else {
                    pcodedNavbar.classList.add('mob-open');
                }
                e.stopPropagation();
            }
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    var vw = window.innerWidth;
    let pcodedNavbar = document.querySelector(".pcoded-navbar");
    if (pcodedNavbar) {
        pcodedNavbar.addEventListener('click tap', function (e) {
            e.stopPropagation();
        });
    }
    let pcodedMainContainer = document.querySelector('.pcoded-main-container');
    if (pcodedMainContainer) {
        pcodedMainContainer.addEventListener("click", function () {
            if (vw < 992) {
                let pcodedNavbar = document.querySelector(".pcoded-navbar");
                if (pcodedNavbar && pcodedNavbar.classList.contains("mob-open")) {
                    pcodedNavbar.classList.remove('mob-open');
                    let mobileCollapse = document.querySelector("#mobile-collapse");
                    if (mobileCollapse) {
                        mobileCollapse.classList.remove('on');
                    }
                    let mobileCollapse1 = document.querySelector("#mobile-collapse1");
                    if (mobileCollapse1) {
                        mobileCollapse1.classList.remove('on');
                    }
                }
            }
        });
    }
});

// Active menu item list start
var elem = document.querySelectorAll('.pcoded-navbar .pcoded-inner-navbar a');
for (var l = 0; l < elem.length; l++) {
    var pageUrl = window.location.href.split(/[?#]/)[0];
    if (elem[l].href == pageUrl && elem[l].getAttribute('href') != "") {
        elem[l].parentNode.classList.add("active");
        elem[l].parentNode.parentNode.parentNode.classList.add("active");
        elem[l].parentNode.parentNode.parentNode.classList.add("pcoded-trigger");
        elem[l].parentNode.parentNode.style.display = 'block';

        elem[l].parentNode.parentNode.parentNode.parentNode.parentNode.classList.add("active");
        elem[l].parentNode.parentNode.parentNode.parentNode.parentNode.classList.add("pcoded-trigger");
        elem[l].parentNode.parentNode.parentNode.parentNode.style.display = 'block';

        if (document.body.classList.contains('tab-layout')) {
            var temp = document.querySelector('.sidelink.active').getAttribute('data-value');
            let layout1NavActive = document.querySelector('.layout1-nav > ul > li.active');
            if (layout1NavActive) {
                layout1NavActive.classList.remove('active');
            }
            let newLayout1NavActive = document.querySelector('.layout1-nav > ul > li > a[data-cont="' + temp + '"]').parentNode;
            if (newLayout1NavActive) {
                newLayout1NavActive.classList.add('active');
            }
        }
    }
}
