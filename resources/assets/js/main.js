"use strict";

function _typeof(obj) {
    "@babel/helpers - typeof";
    return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
        return typeof obj;
    } : function (obj) {
        return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    }, _typeof(obj);
}

function _defineProperty(obj, key, value) {
    key = _toPropertyKey(key);
    if (key in obj) {
        Object.defineProperty(obj, key, {value: value, enumerable: true, configurable: true, writable: true});
    } else {
        obj[key] = value;
    }
    return obj;
}

function _toPropertyKey(arg) {
    var key = _toPrimitive(arg, "string");
    return _typeof(key) === "symbol" ? key : String(key);
}

function _toPrimitive(input, hint) {
    if (_typeof(input) !== "object" || input === null) return input;
    var prim = input[Symbol.toPrimitive];
    if (prim !== undefined) {
        var res = prim.call(input, hint || "default");
        if (_typeof(res) !== "object") return res;
        throw new TypeError("@@toPrimitive must return a primitive value.");
    }
    return (hint === "string" ? String : Number)(input);
}

var App = {
    SetBackground: function SetBackground() {
        $("[setBackground]").each(function () {
            var background = $(this).attr("setBackground");
            $(this).css({
                "background-image": "url(" + background + ")", "background-size": "cover", "background-position": "center center"
            });
        });
        $("[setBackgroundRepeat]").each(function () {
            var background = $(this).attr("setBackgroundRepeat");
            $(this).css({
                "background-image": "url(" + background + ")", "background-repeat": "repeat"
            });
        });
    }, EqualHeightElement: function EqualHeightElement(el) {
        var height = 0;
        var thisHeight = 0;
        $(el).each(function () {
            thisHeight = $(this).height();
            if (thisHeight > height) {
                height = thisHeight;
            }
        });
        $(el).height(height);
    }, ScrollTo: function ScrollTo(y) {
        $("html, body").animate({
            scrollTop: y
        }, 1000);
    }, Init: function Init() {
        App.ScrollTo();
        App.SetBackground();
    }
};

function InitSlider() {
}

function Headers() {
    $(document).on("click", ".toggle-btn", function () {
        $(this).toggleClass("active");
        $("#sidebar").toggleClass("active");
        $("#overlay").toggleClass("active");
    });
    $(document).on("click", "#overlay", function () {
        $(this).removeClass("active");
        $("#sidebar").removeClass("active");
        $(".toggle-btn").removeClass("active");
    });
    $(".has-dropdown .nav-sub").slideUp(200);
    $(document).on("click", ".has-dropdown .menu-link", function () {
        if ($(this).hasClass("active")) {
            $(this).removeClass("active");
            $(this).next().slideUp(200);
        } else {
            $(this).addClass("active");
            $(this).next().slideDown(200);
        }
    });
}

//mapping
function mappings() {
}

function accordion() {
    if ($(".accordions").length) {
        $(".accordion-item.active").find(".accordion-content").show();
        $(".accordion-heading").on("click", function () {
            if (!$(this).parent().is(".active")) {
                $(this).parent().toggleClass("active").children(".accordion-content").slideToggle(500).parent().siblings(".active").removeClass("active").children(".accordion-content").slideToggle(500);
                $(this).find(".arrow .txt").text("Show less");
            } else {
                $(this).parent().toggleClass("active");
                $(this).next().slideToggle(500);
                $(this).find(".arrow .txt").text("Show more");
            }
        });
    }
}

function myTabs() {
    $(".my-tabs").each(function () {
        var $this = $(this);
        var visibleElements = $(".tab-content:visible");
        var visibleTab = visibleElements.attr("class");
        $this.find(".tabs-nav li:first").addClass("active");
        $this.find(".tab-container").find(".tab-content:first").show();
        $this.find(".tabs-nav li").click(function () {
            var corresponding = $(this).data("tab");
            $this.find(".tab-content").hide();
            $this.find(".tab-content." + corresponding).show();
            $this.find(".tabs-nav li").removeClass("active");
            $(this).addClass("active");
        });
    });
}

var uploadFile = function uploadFile() {
    $(".change-image-el").each(function () {
        var chooseBtn = $(this).find(".choose-file");
        var inputFile = $(this).find("input");
        var currentUrl = $(this).find("img").attr("src");
        chooseBtn.click(function () {
            inputFile.click();
        });
        inputFile.change(function () {
            var fileName = $(this).val().split("\\").pop();
            $(this).parents(".change-image-el").find("img").attr("src", fileName);
        });
        $(".reset-btn").click(function () {
            $(this).parents(".change-image-el").find("input").val("");
            $(this).parents(".change-image-el").find("img").attr("src", currentUrl);
        });
    });
};

function replaceSvg() {
    $("img.svg").each(function () {
        var $img = $(this);
        var imgID = $img.attr("id");
        var imgClass = $img.attr("class");
        var imgURL = $img.attr("src");
        $.get(imgURL, function (data) {
            var $svg = $(data).find("svg");
            if (typeof imgID !== "undefined") {
                $svg = $svg.attr("id", imgID);
            }
            if (typeof imgClass !== "undefined") {
                $svg = $svg.attr("class", imgClass + " replaced-svg");
            }
            $svg = $svg.removeAttr("xmlns:a");
            $img.replaceWith($svg);
        }, "xml");
    });
}

$(document).ready(function () {
    App.Init();
    InitSlider();
    Headers();
    mappings();
    accordion();
    myTabs();
    uploadFile();
    replaceSvg();
    $("[data-fancybox]").fancybox({
        closeExisting: true, loop: true, touch: false
    });
    $(".datepicker").datepicker();
    $(".cancel-popup").on("click", function () {
        $.fancybox.close();
    });
    $(document).on("click", ".search-toggle", function () {
        $(this).toggleClass("active");
        $(".form-search").toggleClass("active");
    });
    var sb = $(".form-search");
    $(document).mouseup(function (e) {
        if (!sb.is(e.target) && sb.has(e.target).length === 0) {
            sb.removeClass("active");
        }
    });
    $(document).on("click", ".search-close", function () {
        $(".search-toggle").removeClass("active");
        $(".form-search").removeClass("active");
    });
    if ($(".form-enter-code").length) {
        var handleInput = function handleInput(e) {
            var input = e.target;
            var nextInput = input.nextElementSibling;
            if (nextInput && input.value) {
                nextInput.focus();
                if (nextInput.value) {
                    nextInput.select();
                }
            }
        };
        var handlePaste = function handlePaste(e) {
            e.preventDefault();
            var paste = e.clipboardData.getData("text");
            inputs.forEach(function (input, i) {
                input.value = paste[i] || "";
            });
        };
        var handleBackspace = function handleBackspace(e) {
            var input = e.target;
            if (input.value) {
                input.value = "";
                return;
            }
            input.previousElementSibling.focus();
        };
        var handleArrowLeft = function handleArrowLeft(e) {
            var previousInput = e.target.previousElementSibling;
            if (!previousInput) return;
            previousInput.focus();
        };
        var handleArrowRight = function handleArrowRight(e) {
            var nextInput = e.target.nextElementSibling;
            if (!nextInput) return;
            nextInput.focus();
        };
        var form = document.querySelector(".form-enter-code");
        var inputs = form.querySelectorAll("input");
        var KEYBOARDS = {
            backspace: 8, arrowLeft: 37, arrowRight: 39
        };
        form.addEventListener("input", handleInput);
        inputs[0].addEventListener("paste", handlePaste);
        inputs.forEach(function (input) {
            input.addEventListener("focus", function (e) {
                setTimeout(function () {
                    e.target.select();
                }, 0);
            });
            input.addEventListener("keydown", function (e) {
                switch (e.keyCode) {
                    case KEYBOARDS.backspace:
                        handleBackspace(e);
                        break;
                    case KEYBOARDS.arrowLeft:
                        handleArrowLeft(e);
                        break;
                    case KEYBOARDS.arrowRight:
                        handleArrowRight(e);
                        break;
                    default:
                }
            });
        });
    }
    //add row
    $(".table-add-row .add-row").on("click", function () {
        var newRow = $(".table-add-row table tbody tr:last").clone();
        newRow.find('input[type="text"]').val("");
        $(".table-add-row table tbody").append(newRow);
    });

    function initializeDatepicker(input) {
        input.datepicker();
    }

    $(".add-new-box .btn-group a").click(function (e) {
        e.preventDefault();
        var formGroup = $(this).parents(".add-new-box").find(".form-group");
        var newInput = $("<input>").attr({
            type: "text", class: "mt-8 form-control" + (formGroup.hasClass("date") ? " datepicker" : ""), value: "", required: "", placeholder: "New Field"
        });
        formGroup.append(newInput);
    });
    $(".add-new-box").on("focus", ".date input", function () {
        initializeDatepicker($(this));
    });
    //upload file
    $(document).ready(function () {
        $(".fileInput").change(function () {
            var fileContainer = $(this).closest(".fileContainer");
            var files = this.files;
            var columns = fileContainer.find(".fileColumn");
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var column;
                if (i < columns.length) {
                    column = columns.eq(i);
                } else {
                    column = $('<div class="fileColumn"></div>');
                    fileContainer.find(".files").append(column);
                }
                if (file.type.startsWith("image/")) {
                    var img = $("<img>");
                    img.attr("src", URL.createObjectURL(file));
                    column.html(img);
                    var deleteButton = $('<i class="icon-remove fa-regular fa-xmark"></i>');
                    deleteButton.click(function () {
                        $(this).closest(".fileColumn").remove();
                    });
                    column.append(deleteButton);
                }
                var fileName = $("<p class='name'>");
                fileName.text(file.name);
                column.append(fileName);
            }
        });
    });
    //add new box
    $(".btn").click(function () {
        var newItemBox = $(this).parents(".has-add-box").find(".box:first").clone();
        newItemBox.find('input[type="number"]').val("");
        newItemBox.find('input[type="text"]').val("");
        newItemBox.find("textarea").val("");
        $(this).parents(".has-add-box").find(".inner").append(newItemBox);
    });
    //table selected
    $(".select-rows #all").click(function () {
        $(".select-rows input[type='checkbox']").prop("checked", $(this).prop("checked"));
    });
    $(".select-rows input[type='checkbox']").click(function () {
        if (!$(this).prop("checked")) {
            $("#all").prop("checked", false);
        }
    });
    $(".select-rows input[type='checkbox']").change(function () {
        var isChecked = $(this).prop("checked");
        $(this).closest("tr").toggleClass("selected", isChecked);
    });
    //
    $(".js-select2").select2();
});
document.addEventListener("DOMContentLoaded", function () {
    // var _FullCalendar$Calenda;
    // var calendarEl = document.getElementById("calendar");
    // var calendar = new FullCalendar.Calendar(calendarEl, (_FullCalendar$Calenda = {
    //     headerToolbar: {
    //         right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek", // center: "title",
    //         left: "prev,next,title"
    //     }, navLinks: true, buttonText: {
    //         dayGridMonth: "Month", timeGridWeek: "Week", timeGridDay: "Day", listWeek: "List week"
    //     }, height: 300, initialView: "timeGridWeek"
    // }, _defineProperty(_FullCalendar$Calenda, "height", "70%"), _defineProperty(_FullCalendar$Calenda, "dayHeaders", true), _defineProperty(_FullCalendar$Calenda, "initialDate", "2021-02-15"), _defineProperty(_FullCalendar$Calenda, "editable", true), _defineProperty(_FullCalendar$Calenda, "eventContent", function eventContent(arg) {
    //     return {
    //         html: arg.event.title
    //     };
    // }), _defineProperty(_FullCalendar$Calenda, "events", [{
    //     id: 1, title: "GUNTIGER INVESTMENT HOLDINGS PTE. LTD", start: "2021-02-15 07:00:00", end: "2021-02-15 12:00:00"
    // }, {
    //     id: 2, title: "Another <b>Entry with HTML</b>", start: "2021-02-16 07:00:00", end: "2021-02-16 08:00:00"
    // }, {
    //     id: 3, title: "<i>Third Entry with HTML</i>", start: "2021-02-16 09:00:00", end: "2021-02-16 10:00:00"
    // }]), _FullCalendar$Calenda));
    // calendar.render();
});
