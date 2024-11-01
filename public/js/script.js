/**
 * Plugin javascript file
 * @author	Rudra Innnovative Software 
 * @package	training/public/js/ 
 * @version	1.0.0
 */

jQuery(function () {

    if (jQuery('.rdtr-pl.my_rdtr_topbar').length > 0) {

        var my_rdtr_topbar = jQuery(".rdtr-pl.my_rdtr_topbar").offset().top;

        training_url_hashing_fn();

        jQuery(window).scroll(function () {

            var topbar_diff_header = my_rdtr_topbar - jQuery(window).scrollTop();
            var check_visibility = jQuery(window).scrollTop() >= jQuery('.wptr_dasboard-content-area.my-course-dash').offset().top + jQuery('.wptr_dasboard-content-area.my-course-dash').outerHeight() - window.innerHeight;
            if (topbar_diff_header <= 0 && !check_visibility) {
                jQuery('#wptr_module').addClass('rdtr-fixed-sidebar');
            } else {
                jQuery('#wptr_module').removeClass('rdtr-fixed-sidebar');
            }
        })

    }

    if (jQuery("#login-dialog").length > 0) {
        jQuery("#login-dialog").css("display", "none");
    }

    if (jQuery("#training_page_type").length > 0) {

        var page_val = jQuery("#training_page_type").val();
        if (page_val == "all") {
            var preventScroll = false;
            var offset = 1;
            jQuery(window).scroll(function () {
                var scrollHeight = jQuery(document).height();
                var scrollPosition = jQuery(window).height() + jQuery(window).scrollTop();
                //if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
                if (preventScroll == false) {
                    preventScroll = true;
                    if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
                        var srchKey = jQuery("#txt_search_course").val();
                        wpl_load_more_courses(offset, srchKey);
                        offset++;
                    }
                }
            });
            jQuery(window).scroll(function () {
                var scrollHeight = jQuery(document).height();
                var scrollPosition = jQuery(window).height() + jQuery(window).scrollTop();
                if ((scrollHeight - scrollPosition) / scrollHeight !== 0) {
                    preventScroll = false;
                }
            });
        }
    }

    if (jQuery('.my-course_contnet_area').length > 0) {

        // added for progress
        jQuery(window).scroll(function () {
            var divPosition = jQuery('.my-course_contnet_area').offset().top;
            var scrollTopOffsetPosition = jQuery(window).scrollTop();
            var topPosition = divPosition - scrollTopOffsetPosition;
            if (topPosition < 0) {
                // adding height when scrolls to top from 0, so topPosition will be -ve in that case so adding to 250px;
                jQuery(".wptr_course_processing").css("top", 250 - (topPosition));
            }
        });

    }

    jQuery(document).on("click", ".ch-upload-files-section", function () {
        var hash_values = location.hash;
        var hash_params = hash_values.split("#");
        if (hash_params.length > 0) {
            var chapter_id = hash_params[4].split("=")[1];
            var findNextChapterLi = jQuery("a[data-id=" + chapter_id + "]").closest("li").nextAll().length;
            if (findNextChapterLi) {

                jQuery("a[data-id=" + chapter_id + "]").closest("li").next().find("a[data-type='chapter']").removeClass("call-ajax-to-check");
                jQuery("a[data-id=" + chapter_id + "]").closest("li").next().find("a[data-type='chapter']").click();
            } else {
                var module_id = hash_params[2].split("=")[1];
                var isNextModule = jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").length;

                if (isNextModule > 0) {

                    var trigger_id = jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").attr("data-id");
                    var hasModuleDropdown = jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("span[trigger-id='" + trigger_id + "']").length;

                    if (hasModuleDropdown > 0) {
                        jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").click();
                        jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("span[trigger-id='" + trigger_id + "']").click();
                    } else {
                        jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").click();
                    }

                } else {
                    //course completed
                    var formdata = "action=wpl_training_public_handler&param=wpl_rd_check_complete_course&module_id=" + module_id;
                    jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
                    jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
                        jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
                        var data = jQuery.parseJSON(response);
                        if (data.sts == 1) {
                            jQuery("#rdtr-load-course-body").html(data.arr.template);
                        }
                    });
                }
            }
        }
    });

    // for all courses search bar
    jQuery("form#frmSearchCourse").on("submit", function () {
        jQuery(".wptr_main_body").addClass("wptr-rud-wpl-processing");
        var formdata = jQuery(this).serialize() + "&action=wpl_training_public_handler&param=wpl_course_search";
        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
            jQuery(".wptr_main_body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                jQuery("#training-all-courses").html(data.arr.template);
            }
        });
    });

    jQuery("a.wpl_module_head").on("click", function () {

        var data_value = jQuery(this).attr("data-value");
        jQuery("ul#" + data_value).slideToggle("slow", "linear");
        var data_status = jQuery(this).attr("data-status");
        data_status = data_status == 0 ? 1 : 0;
        jQuery(this).attr("data-status", data_status);
        if (jQuery(this).find(".wptr_plus").hasClass("wptr_display-none")) {
            jQuery(this).find(".wptr_minus").addClass("wptr_display-none");
            jQuery(this).find(".wptr_plus").removeClass("wptr_display-none");
            jQuery(this).find(".wptr_plus i").addClass("wptr_text-z-black");
            jQuery(this).find(".wptr_minus i").removeClass("wptr_text-z-black");
            return false;
        }
        if (jQuery(this).find(".wptr_minus").hasClass("wptr_display-none")) {
            jQuery(this).find(".wptr_minus").removeClass("wptr_display-none");
            jQuery(this).find(".wptr_plus").addClass("wptr_display-none");
            jQuery(this).find(".wptr_minus i").addClass("wptr_text-z-black");
            jQuery(this).find(".wptr_plus i").removeClass("wptr_text-z-black");
            return false;
        }

    });
    var loginDialog = jQuery("#login-dialog").dialog({
        autoOpen: false,
        show: {
            effect: "slide",
            duration: 700
        },
        hide: {
            effect: "fold",
            duration: 700
        },
        width: 480,
        modal: true,
        dialogClass: "wptr-rud-cstm-dialog",
        close: function () {
            jQuery("#login-dialog").css("display", "none");
            jQuery("body").css("overflow-y", "");
        }
    });
    var registerDialog = jQuery("#signup-dialog").dialog({
        autoOpen: false,
        show: {
            effect: "slide",
            duration: 700
        },
        hide: {
            effect: "fold",
            duration: 700
        },
        width: 480,
        modal: true,
        dialogClass: "wptr-rud-cstm-dialog",
        close: function () {
            jQuery("#signup-dialog").css("display", "none");
            jQuery("body").css("overflow-y", "");
        }
    });
    jQuery("#training-course-login").on("click", function () {
        jQuery("#login-dialog").css("display", "block");
        jQuery("body").css("overflow-y", "hidden");
        loginDialog.dialog("open");
        jQuery("#login-dialog").addClass("ui_dilogbox_anim");
    });
    jQuery("#rd-training-user-signup").on("click", function () {
        loginDialog.dialog("close");
        jQuery("body").css("overflow-y", "hidden");
        registerDialog.dialog("open");
        jQuery("#signup-dialog").addClass("ui_dilogbox_anim");
    });
    jQuery(document).on("click", "#rd-training-user-login", function () {
        loginDialog.dialog("open");
        jQuery("#login-dialog").addClass("ui_dilogbox_anim");
        registerDialog.dialog("close");
        jQuery("body").css("overflow-y", "hidden");
    });

    jQuery('#wptr-collapse-burger-menu').click(function () {
        jQuery(this).toggleClass('open');
    });

    jQuery("#frmtraininglogin").validate({
        submitHandler: function () {
            var formdata = jQuery("#frmtraininglogin").serialize() + "&action=wpl_training_public_handler&param=wpl_check_user_login";
            jQuery("body").addClass("wptr-rud-wpl-processing");
            jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
                jQuery("body").removeClass("wptr-rud-wpl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    location.reload();
                } else {
                    wpl_training_notification(data.msg, 3000, "error");
                }
            });
        }
    });
    jQuery("#frmtrainingsignup").validate({
        rules: {
            training_signup_user_pwd: "required",
            training_signup_user_conf_pwd: {
                equalTo: "#training_signup_user_pwd"
            }
        },
        submitHandler: function () {
            var formdata = jQuery("#frmtrainingsignup").serialize() + "&action=wpl_training_public_handler&param=wpl_user_registration";
            jQuery("body").addClass("wptr-rud-wpl-processing");
            jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
                jQuery("body").removeClass("wptr-rud-wpl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpl_training_notification(data.msg, 1500, "success");
                    setTimeout(function () {
                        registerDialog.dialog("close");
                        location.reload();
                    }, 1200);
                } else if (data.sts == 0) {
                    wpl_training_notification(data.msg, 3000, "error");
                }
            });
        }
    });
    jQuery("#training_course_enrol_now").on("click", function () {

        jQuery("form#frmenrolnow").submit();
    });
    jQuery("form#frmenrolnow").on("submit", function () {

        jQuery("body").addClass("wptr-rud-wpl-processing");
        var formdata = jQuery(this).serialize() + "&action=wpl_training_public_handler&param=wpl_user_course_enrol";
        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
            jQuery("body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                wpl_training_notification(data.msg, 2000, "success");
                //return false;
                setTimeout(function () {
                    window.location.href = data.arr.my_course;
                }, 2000);
            } else {

                window.location.href = data.arr.my_course;
            }
        });
    });

    jQuery(document).on("click", ".rdtr-chapter-slick", function () {

        var data_id = jQuery(this).attr("data-id");
        var data_module_order = jQuery(this).attr("data-module-order");
        var data_chapter_order = jQuery(this).attr("data-chapter-order");
        var slick_counter = jQuery(this).attr("data-slick");
        jQuery("#rdtr-modules-area_" + data_module_order).find(".slick-current").removeClass("slick-current");
        jQuery(this).parents("div.slick-slide").addClass("slick-current");
        var formdata = "url=" + location.href + "&slick=" + slick_counter + "&chapter_id=" + data_id + "&module_order=" + data_module_order + "&chapter_order=" + data_chapter_order + "&action=wpl_training_public_handler&param=wpl_training_course_syllabus";
        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
            jQuery(".wptr_main_body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                jQuery("#module_exercise_area_" + data_module_order).html(data.arr.template);

                //create slick slider dynamic
                jQuery(".rdtr_create_slick_" + data.arr.slick_order).slick({
                    dots: false,
                    infinite: false,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3,
                                infinite: true,
                                dots: true
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            }
        });
    });

    jQuery(document).on("click", ".rdtr-module-panel", function () {

        var target_id = jQuery(this).attr("data-target");

        jQuery(".cr-detail").slideUp("slow");

        if (jQuery(this).find("i").hasClass("wptr-drop-rotate-open-icon")) {

            jQuery(this).find("i").removeClass("wptr-drop-rotate-open-icon").addClass("wptr-drop-rotate-close-icon")

        } else {

            jQuery("#wptr_module.load-my-training-sidebar").find("span.rdtr-module-panel i").removeClass("wptr-drop-rotate-open-icon").addClass("wptr-drop-rotate-close-icon")

            if (jQuery(this).find("i").hasClass("wptr-drop-rotate-close-icon")) {

                jQuery(this).find("i").removeClass("wptr-drop-rotate-close-icon").addClass("wptr-drop-rotate-open-icon")

                jQuery(target_id).slideDown("slow");

            }
        }

        var data_id = jQuery(this).attr("trigger-id");

        window.location.hash = '#type=module#modid=' + data_id;
    });


    jQuery(document).on("click", ".rdtr-chapter-panel", function () {

        var target_id = jQuery(this).attr("data-target");

        var ch_id = jQuery(this).attr("trigger-id");

        jQuery(".rdtr-ul-chapter-ex").slideUp("slow");

        if (jQuery(this).find("i").hasClass("wptr-drop-rotate-open-icon")) {

            jQuery(this).find("i").removeClass("wptr-drop-rotate-open-icon").addClass("wptr-drop-rotate-close-icon")
        } else {

            jQuery("#wptr_module.load-my-training-sidebar").find("span.rdtr-chapter-panel i").removeClass("wptr-drop-rotate-open-icon").addClass("wptr-drop-rotate-close-icon")

            if (jQuery(this).find("i").hasClass("wptr-drop-rotate-close-icon")) {

                jQuery(this).find("i").removeClass("wptr-drop-rotate-close-icon").addClass("wptr-drop-rotate-open-icon")

                jQuery(target_id).slideDown("slow");
            }
        }

        var hash_values = location.hash;
        var hash_params = hash_values.split("#");
        if (hash_params.length > 0) {

            var module_id = hash_params[2].split("=")[1];

            window.location.hash = '#type=module#modid=' + module_id + '#ch=chapter#chid=' + ch_id;
        }
    });
    // right now it is not in used
    jQuery(document).on("click", ".dash-panel-exelink", function () {
        var exercise_id = jQuery(this).attr("data-id");
        jQuery("body").addClass("wptr-rud-wpl-processing");
        var formdata = "&action=wpl_training_public_handler&param=wpl_load_more_course";
        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
            jQuery("body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                jQuery("div.wptr_row-section").append(data.arr.template);
            }
        });
    });

    jQuery(document).on("click", ".rdtr-read-section", function () {
        var id = jQuery(this).attr("data-id");

        if (jQuery("#wptr-collapse-burger-menu").length > 0) {
            //jQuery("#wptr-collapse-burger-menu").click();
        }

        var ajax_class_exists = jQuery(this).hasClass("call-ajax-to-check");

        if (ajax_class_exists) {
            wpl_training_notification("Please complete previous sections", 2000, "error");
            return false;
        }

        var data_type = jQuery(this).attr("data-type");
        jQuery("body").find(".load-my-training-sidebar .rdtr_active").removeClass("rdtr_active");
        jQuery(this).addClass("rdtr_active");
        var has_values = location.hash.split("#");
        if (has_values.length > 0) {
            var type = has_values[1].split("=")[1];
            var mod_id = has_values[2].split("=")[1];
            var extra = {
                type: type,
                mod_id: mod_id
            };
            load_course_data_section(data_type, id, extra);
        } else {

        }

    });
    // section for exercise reading

    jQuery(document).on("click", ".rdtr-start-exercise", function () {

        var dataid = jQuery(this).attr("data-id");

        if (jQuery("#wptr-collapse-burger-menu").length > 0) {
            //jQuery("#wptr-collapse-burger-menu").click();
        }

        if (is_course_locked == 1) {

            if (exercisesArray.length > 0) {
                var ex_index = 0;
                jQuery.each(exercisesArray, function (index, exercise) {

                    if (exercise.id == dataid) {
                        ex_index = index;
                    } else {

                    }
                });

                if (ex_index > 0) {

                    ex_index = ex_index - 1;

                    jQuery.each(exercisesArray, function (index, exercise) {

                        if (ex_index == index) {

                            if (exercise.status == 1) {

                                jQuery(".rdtr-start-exercise").removeClass("rdtr_active");
                                jQuery(this).addClass("current-exercise-open");
                                jQuery(this).addClass("rdtr_active");
                                var has_params = window.location.hash.split("#");
                                if (has_params.length >= 5 && has_params.length <= 7) {
                                    var module_id = has_params[2].split("=")[1];
                                    var ch = has_params[3].split("=")[1];
                                    var chapter_id = has_params[4].split("=")[1];

                                    var extra_data = {
                                        type: "module",
                                        mod_id: module_id,
                                        ch: ch,
                                        chapter_id: chapter_id
                                    };

                                    load_course_data_section("exercise", dataid, extra_data);
                                }

                            } else {
                                var formdata = "exercise_id=" + dataid + "&action=wpl_training_public_handler&param=wpl_load_check_exercise_access";
                                jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
                                jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
                                    jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
                                    var data = jQuery.parseJSON(response);
                                    if (data.sts == 1) {
                                    } else {
                                        wpl_training_notification(data.msg, 2000, "error");
                                    }
                                });
                            }
                        }
                    });
                } else {
                    jQuery(".rdtr-start-exercise").removeClass("rdtr_active");
                    jQuery(this).addClass("current-exercise-open");
                    jQuery(this).addClass("rdtr_active");
                    var has_params = window.location.hash.split("#");
                    if (has_params.length >= 5 && has_params.length <= 7) {
                        var module_id = has_params[2].split("=")[1];
                        var ch = has_params[3].split("=")[1];
                        var chapter_id = has_params[4].split("=")[1];

                        var extra_data = {
                            type: "module",
                            mod_id: module_id,
                            ch: ch,
                            chapter_id: chapter_id
                        };

                        load_course_data_section("exercise", dataid, extra_data);
                    }
                }
            }
        } else if (is_course_locked == 0) {

            jQuery(".rdtr-start-exercise").removeClass("rdtr_active");
            jQuery(this).addClass("current-exercise-open");
            jQuery(this).addClass("rdtr_active");
            var has_params = window.location.hash.split("#");
            if (has_params.length >= 5 && has_params.length <= 7) {
                var module_id = has_params[2].split("=")[1];
                var ch = has_params[3].split("=")[1];
                var chapter_id = has_params[4].split("=")[1];

                var extra_data = {
                    type: "module",
                    mod_id: module_id,
                    ch: ch,
                    chapter_id: chapter_id
                };

                load_course_data_section("exercise", dataid, extra_data);
            }
        }
    });
    jQuery(document).on("click", ".btn-submit-exercise", function () {

        var section_name = jQuery(this).attr("section-name");
        var section_type = jQuery(this).attr("section-type");
        var exercise_id = jQuery(this).attr("exid");

        var formdata = jQuery("." + section_type + "_" + section_name).serialize() + "&exid=" + exercise_id + "&action=wpl_training_public_handler&param=wpl_save_exercise_data_section";
        jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
            jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                // URL Hash implementation
                //remove submit btn on click
                jQuery(this).remove();
                var crawl_url = location.href;
                var hash_values = location.hash;
                var hash_params = hash_values.split("#");
                id = 0;
                if (hash_params.length > 0) {
                    var type = hash_params[5].split("=")[1];
                    if (type == "exercise") {
                        var id = hash_params[6].split("=")[1];
                    }
                }
                jQuery("#rdtr-load-course-body").html(data.arr.template);
                jQuery("button.ch-next-mode").attr("data-id", id);
            } else {
                wpl_training_notification(data.msg, 2000, "error");
            }
        });
    });

    jQuery(document).on("click", ".link-to-upload-files", function () {

        var chapter_id = jQuery(this).attr("chapter-id");
        var hash_values = location.hash;
        var hash_params = hash_values.split("#");
        if (hash_params.length > 0) {

            var module_id = hash_params[2].split("=")[1];

            window.location.hash = "#type=module#modid=" + module_id + "#ch=chapter#chid=" + chapter_id + "#fn=upload";
            var formdata = "chapter_id=" + chapter_id + "&action=wpl_training_public_handler&param=wpl_load_chapter_upload_section";
            jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
            jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {

                jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {

                    jQuery("#rdtr-load-course-body").html(data.arr.template);
                }
            });

        }
    });

    jQuery(document).on("click", "#wptr-collapse-burger-menu", function () {
        jQuery("#wptr_module").toggleClass("wptr-open-sidebar")
        /*if (jQuery("#wptr_module").hasClass("wptr-open-sidebar")) {
         jQuery("#wptr_module").removeClass("wptr-open-sidebar")
         } else {
         jQuery("#wptr_module").addClass("wptr-open-sidebar")
         }*/
    });

    jQuery(document).on("click", ".btn-chapter-files-upload", function () {

        var has_files = jQuery("form#frm_ch_file_submit").find("input[name='ch_file_uploads[]']").length;

        if (has_files > 0) {
            jQuery("form#frm_ch_file_submit").submit();
        } else {
            wpl_training_notification("Please select files", 2000, "error");
        }

    });

    jQuery(document).on("submit", "form#frm_ch_file_submit", function () {
        var chapter_id = jQuery("#btn-open-users-media").attr("ch-id");
        var formdata = jQuery("form#frm_ch_file_submit").serialize() + "&ch_id=" + chapter_id + "&action=wpl_training_public_handler&param=wpl_upd_chapter_files";
        jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
            jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                wpl_training_notification(data.msg, 2000, "success");
                jQuery("#rdtr-load-course-body").html(data.arr.template);
            }
        });
    });
    jQuery(document).on("click", "#btn-open-users-media", function () {
        var files = wp.media({
            title: "Choose File",
            multiple: true
        }).open().on("select", function () {

            var selection = files.state().get('selection');
            var images_html = '<div class="wptr_upload_parent"><h4 class="wptr_montserrat uploaded_title">Total file(s) secleted: ' + selection.length + '</h4><ul class="rdtr_uploaded_files">';
            var hidden_form = '<form method="post" action="javascript:void(0)" id="frm_ch_file_submit">';
            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                var fileType = '';
                var ext = attachment.url.split('.').pop().toLowerCase();
                var icon_url = '';
                if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == 1) {
                    fileType = "Image";
                    icon_url = '<i class="mdi mdi-file-image uploaded-files-icon-font"></i>';
                } else if (jQuery.inArray(ext, ['mp4', 'webm', "3gp", "mkv"]) == 1) {
                    fileType = "Video";
                    icon_url = '<i class="mdi mdi-file-video uploaded-files-icon-font"></i>';
                } else if (jQuery.inArray(ext, ['mp3']) == 1) {
                    fileType = "Audio";
                    icon_url = '<i class="mdi mdi-file-audio uploaded-files-icon-font"></i>';
                } else {
                    fileType = "File";
                    icon_url = '<i class="mdi mdi-file uploaded-files-icon-font"></i>';
                }
                hidden_form += '<input type="hidden" value="' + attachment.url + '" name="ch_file_uploads[]"/>';
                images_html += '<li><a href="' + attachment.url + '" download>' + icon_url + '</a><label>' + fileType + '</label><div class="remove-uploaded-file-icon"><a href="javascript:void(0)" class="remove-uploaded-file"><i class="mdi mdi-close-circle rd-icon"></i></a></div></li>';
            });
            hidden_form += '</form>';
            if (jQuery("#rdtr-upload-files").length > 0) {
                images_html += '</ul><div class="wptr_file_sub_btn"><button class="wptr_course-btn btn-chapter-files-upload">Submit</button></div></div>';
                images_html += hidden_form;
                jQuery("#rdtr-upload-new").html(images_html);
            }
        });
    });

    jQuery(document).on("click", ".remove-uploaded-file", function () {

        var conf = confirm("Are you sure want to remove this file?");
        if (conf) {
            var fileurl = jQuery(this).parents("li").find("a").attr("href");
            jQuery(this).parents("li").remove();
            jQuery("body").find("#frm_ch_file_submit input[value='" + fileurl + "']").remove();
        }

    });

    jQuery(document).on("click", ".ch-next-mode", function () {
        var dataid = jQuery(this).attr("data-id");
        //save chapter complete status
        var formdata = "exercise_id=" + dataid + "&action=wpl_training_public_handler&param=wpl_save_user_exercise";
        jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
            jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
            jQuery("a[data-id=" + dataid + "]").addClass("ex-cmpl");
            var data = jQuery.parseJSON(response);
        });

        var findExerciseLi = jQuery("a[data-id=" + dataid + "]").closest("li").nextAll().length;

        if (findExerciseLi) {

            jQuery("a[data-id=" + dataid + "]").closest("li").next().find("a").removeClass("call-ajax-to-check");
            jQuery("a[data-id=" + dataid + "]").closest("li").next().find("a").click();
        } else {

            var chapter_id = jQuery("a[data-id=" + dataid + "]").parents("ul").attr("data-chapter");
            var findSpanTag = jQuery("a[data-id=" + chapter_id + "]").closest("li.rdtr-ch-sec").next().length;

            if (findSpanTag > 0) {

                var triggerId = jQuery("a[data-id=" + chapter_id + "]").closest("li.rdtr-ch-sec").next().find("a").attr("data-id");
                if (triggerId == undefined) {

                } else {

                    var findChapterLi = jQuery("a[data-id=" + chapter_id + "]").closest("li").nextAll().find(".chapter-anchor-link span[trigger-id=" + triggerId + "]").length;

                    if (findChapterLi) {

                        jQuery("a[data-id=" + chapter_id + "]").closest("li").nextAll().find(".chapter-anchor-link a[data-id=" + triggerId + "]").click();
                        jQuery("a[data-id=" + chapter_id + "]").closest("li").nextAll().find(".chapter-anchor-link span[trigger-id=" + triggerId + "]").click();
                        jQuery("ul[data-chapter=" + triggerId + "] li:first-child a").removeClass("call-ajax-to-check");

                    } else {

                        jQuery("ul li a[data-id=" + triggerId + "]").click();
                    }

                }

            } else {

                var hash_values = location.hash;
                var hash_params = hash_values.split("#");
                if (hash_params.length > 0) {

                    var module_id = hash_params[2].split("=")[1];
                    var isNextModule = jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").length;
                    // finding next module

                    if (isNextModule > 0) {

                        jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").click();
                    } else {

                        var formdata = "action=wpl_training_public_handler&param=wpl_rd_check_complete_course&module_id=" + module_id;
                        jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
                        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
                            jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
                            var data = jQuery.parseJSON(response);
                            if (data.sts == 1) {
                                jQuery("#rdtr-load-course-body").html(data.arr.template);
                            }
                        });
                    }
                } else {
                    wpl_training_notification("Invalid URL parameters", 2000, "error");
                }
            }
        }
    });

    // module next btn click
    jQuery(document).on("click", ".next-module-btn", function () {
        var hash_values = location.hash;
        var hash_params = hash_values.split("#");
        if (hash_params.length > 0) {
            var module_id = hash_params[2].split("=")[1];
            jQuery("div.mod_" + module_id + " ul li:first-child a.rdtr-read-section").addClass("rdtr_active").click();
            var trigger_id = jQuery("div.mod_" + module_id + " ul li:first-child a.rdtr-read-section").attr("data-id");
            var hasChaptersFound = jQuery("div.mod_" + module_id + " ul li:first-child a").length;
            if (hasChaptersFound > 0) {
                var hasExercisesFound = jQuery("div.mod_" + module_id + " ul li:first-child span[trigger-id='" + trigger_id + "']").length;

                if (hasExercisesFound > 0) {
                    jQuery("div.mod_" + module_id + " ul li:first-child span[trigger-id='" + trigger_id + "']").click();
                } else {
                    jQuery("div.mod_" + module_id + " ul li:first-child a").click();
                }
            } else {
                var module_id = hash_params[2].split("=")[1];
                var isNextModule = jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").length;

                if (isNextModule > 0) {

                    jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").click();
                } else {
                    //course completed
                    var formdata = "action=wpl_training_public_handler&param=wpl_rd_check_complete_course&module_id=" + module_id;
                    jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
                    jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
                        jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
                        var data = jQuery.parseJSON(response);
                        if (data.sts == 1) {
                            jQuery("#rdtr-load-course-body").html(data.arr.template);
                        }
                    });
                }
            }
        }
    });

    // chapter next btn click
    jQuery(document).on("click", ".next-chapter-btn", function () {
        var hash_values = location.hash;
        var hash_params = hash_values.split("#");
        if (hash_params.length > 0) {
            var chapter_id = hash_params[4].split("=")[1];
            var exercise_li = jQuery("ul[data-chapter='" + chapter_id + "'] li:first-child a.rdtr-start-exercise").length;

            if (exercise_li > 0) {

                jQuery("ul[data-chapter='" + chapter_id + "'] li:first-child a.rdtr-start-exercise").removeClass("call-ajax-to-check").click();
            } else {

                var findNextChapterLi = jQuery("a[data-id=" + chapter_id + "]").closest("li").nextAll().length;

                if (findNextChapterLi) {

                    jQuery("a[data-id=" + chapter_id + "]").closest("li").next().find("a[data-type='chapter']").removeClass("call-ajax-to-check");
                    jQuery("a[data-id=" + chapter_id + "]").closest("li").next().find("a[data-type='chapter']").click();
                } else {
                    var module_id = hash_params[2].split("=")[1];
                    var isNextModule = jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").length;

                    if (isNextModule > 0) {

                        var trigger_id = jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").attr("data-id");
                        var hasModuleDropdown = jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("span[trigger-id='" + trigger_id + "']").length;

                        if (hasModuleDropdown > 0) {
                            jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").click();
                            jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("span[trigger-id='" + trigger_id + "']").click();
                        } else {
                            jQuery("a[data-id=" + module_id + "]").parent().parent().next().find("a[data-type='module']").click();
                        }

                    } else {
                        //course completed
                        var formdata = "action=wpl_training_public_handler&param=wpl_rd_check_complete_course&module_id=" + module_id;
                        jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
                        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
                            jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
                            var data = jQuery.parseJSON(response);
                            if (data.sts == 1) {
                                jQuery("#rdtr-load-course-body").html(data.arr.template);
                            }
                        });
                    }
                }

            }

        }

    });

    jQuery(document).on("click", "#open-no-page-model", function () {
        return alert("Please set My Course page url to redirect by following Admin > Training > Setting");
    });

    jQuery(document).on("click", ".rdtr-see-explanation", function () {
        jQuery(this).parent().find(".toggle-pexplanation").toggle();
    });

    // complete exercise
    jQuery(document).on("click", ".rdtr-complete-exercise", function () {

        var hash_values = location.hash;
        var hash_params = hash_values.split("#");
        if (hash_params.length > 0) {
            var exercise_id = hash_params[6].split("=")[1];

            if (exercise_id !== undefined) {

                var formdata = "action=wpl_training_public_handler&param=wpl_rd_mark_exercise_complete&exercise_id=" + exercise_id;
                jQuery("#rdtr-load-course-body").addClass("wptr-rud-wpl-processing");
                jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
                    jQuery("#rdtr-load-course-body").removeClass("wptr-rud-wpl-processing");
                    var data = jQuery.parseJSON(response);

                    if (data.sts == 1) {
                        jQuery(".load-my-training-sidebar").html(data.arr.template);
                        wpl_training_notification(data.msg, 2000, "success");
                        training_url_hashing_fn();
                    } else {
                        wpl_training_notification(data.msg, 2000, "error");
                    }
                });
            }
        }

    });

});


function training_url_hashing_fn() {

    // URL Hash implementation
    var hash_values = location.hash;
    var hash_params = hash_values.split("#");
    if (hash_params.length > 0) {
        var type = hash_params[1].split("=")[1];
        // checking for model parameters
        if (type == "module") {

            if (hash_params.length <= 3) { // for module redirection

                var id = hash_params[2].split("=")[1];

                load_course_data_section(type, id);
                var module_id = hash_params[2].split("=")[1];
                if (module_id == "") {

                    wpl_rdtr_redirect_invalid_path();

                } else {
                    if (jQuery("a[data-id='" + module_id + "']").length > 0) {

                        setTimeout(function () {
                            jQuery("a[data-id='" + module_id + "']").click();
                            jQuery("a[data-id='" + module_id + "']").addClass("rdtr_active");
                            jQuery("a[data-id='" + module_id + "']").next("span[trigger-id=" + module_id + "]").click();

                        }, 100);
                    } else {

                        wpl_rdtr_redirect_invalid_path();
                    }

                }

            } else if (hash_params.length > 3 && hash_params.length <= 6) { // for chapter

                var module_id = hash_params[2].split("=")[1];
                var ch = hash_params[3].split("=")[1];
                var chapter_id = hash_params[4].split("=")[1];

                var extra_data = {
                    type: type,
                    mod_id: module_id
                };

                if (hash_params[5] !== "") {

                    load_course_data_section("chapter", chapter_id, extra_data);
                    setTimeout(function () {
                        jQuery("a.rdtr-mod-sec").next("span[trigger-id=" + module_id + "]").click();
                        jQuery("a[data-id='" + chapter_id + "']").click();
                        jQuery("a[data-id='" + chapter_id + "']").next("span[trigger-id=" + chapter_id + "]").click();
                        jQuery("a.link-to-upload-files[chapter-id='" + chapter_id + "']").addClass("rdtr_active").click();
                        jQuery("a[data-id='" + chapter_id + "']").addClass("rdtr_active");
                        jQuery("a.rdtr-mod-sec").next("span[trigger-id=" + module_id + "]");
                    }, 100);
                } else {

                    load_course_data_section("chapter", chapter_id, extra_data);
                    setTimeout(function () {
                        jQuery("a.rdtr-mod-sec").next("span[trigger-id=" + module_id + "]");
                        jQuery("a.rdtr-mod-sec").next("span[trigger-id=" + module_id + "]").click()
                        jQuery("a[data-id='" + chapter_id + "']").click();
                        jQuery("a[data-type='chapter']").next("span[trigger-id=" + chapter_id + "]").click()
                        jQuery("a[data-id='" + chapter_id + "']").addClass("rdtr_active");
                        jQuery("a.rdtr-mod-sec").next("span[trigger-id=" + module_id + "]");
                    }, 100);
                }

            } else { // for exercise

                var module_id = hash_params[2].split("=")[1];
                var ch = hash_params[3].split("=")[1];
                var chapter_id = hash_params[4].split("=")[1];
                var exercise_id = hash_params[6].split("=")[1];
                var extra_data = {
                    type: type,
                    mod_id: module_id,
                    ch: ch,
                    chapter_id: chapter_id
                };
                load_course_data_section("exercise", exercise_id, extra_data);

                setTimeout(function () {
                    jQuery("a.rdtr-mod-sec").next("span[trigger-id=" + module_id + "]").click()
                    jQuery("a.rdtr-read-section").next("span[trigger-id=" + chapter_id + "]").click()
                    jQuery("a.rdtr-start-exercise[data-id=" + exercise_id + "]").click()
                }, 200);
            }
        }
    }

}

function load_course_data_section(type, id, obj = {}) {

    if (type == "module") {
        window.location.hash = '#type=module#modid=' + id;
        var formdata = "module_id=" + id + "&action=wpl_training_public_handler&param=wpl_load_module_data_section";
        jQuery("#rdtr-load-course-body #wptr-rd-loader-area").addClass("wptr_course_processing");
        jQuery("#rdtr-load-course-body .my-course_contnet_area").addClass("rdtr-fade-container");
        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
            jQuery("#rdtr-load-course-body #wptr-rd-loader-area").removeClass("wptr_course_processing");
            jQuery("#rdtr-load-course-body .my-course_contnet_area").removeClass("rdtr-fade-container");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                jQuery("#rdtr-load-course-body").html(data.arr.template);
            }
        });
    } else if (type == "exercise") {
        window.location.hash = '#type=' + obj.type + '#modid=' + obj.mod_id + '#ch=' + obj.ch + '#chid=' + obj.chapter_id + '#sub=exercise#exeid=' + id;
        var formdata = "chapter_id=" + obj.chapter_id + "&exercise_id=" + id + "&action=wpl_training_public_handler&param=wpl_load_exercise_data_section";
        jQuery("#rdtr-load-course-body #wptr-rd-loader-area").addClass("wptr_course_processing");
        jQuery("#rdtr-load-course-body .my-course_contnet_area").addClass("rdtr-fade-container");
        jQuery("button.ch-next-mode").attr("data-id", id);
        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {

            jQuery("#rdtr-load-course-body #wptr-rd-loader-area").removeClass("wptr_course_processing");
            jQuery("#rdtr-load-course-body .my-course_contnet_area").removeClass("rdtr-fade-container");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {

                jQuery("#rdtr-load-course-body").html(data.arr.template);
            }
        });
    } else if (type == "chapter") {
        window.location.hash = '#type=' + obj.type + '#modid=' + obj.mod_id + '#ch=chapter#chid=' + id;
        var formdata = "chapter_id=" + id + "&action=wpl_training_public_handler&param=wpl_load_chapter_data_section";
        jQuery("#rdtr-load-course-body #wptr-rd-loader-area").addClass("wptr_course_processing");
        jQuery("#rdtr-load-course-body .my-course_contnet_area").addClass("rdtr-fade-container");

        jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
            jQuery("#rdtr-load-course-body #wptr-rd-loader-area").removeClass("wptr_course_processing");
            jQuery("#rdtr-load-course-body .my-course_contnet_area").removeClass("rdtr-fade-container");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                jQuery("#rdtr-load-course-body").html(data.arr.template);
            }
        });
}
}

function wpl_load_more_courses(offset = 0, search_key = '') {

    var formdata = "srch=" + search_key + "&offset=" + offset + "&action=wpl_training_public_handler&param=wpl_load_more_course";
    //jQuery("body").addClass("wptr-rud-wpl-processing");
    jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
        //jQuery("body").removeClass("wptr-rud-wpl-processing");
        var data = jQuery.parseJSON(response);
        if (data.sts == 1) {
            jQuery("#training-all-courses").append(data.arr.template);
        }
    });
}

// training notification function uses notifyBar
function wpl_training_notification(data, delay, cssClass) {

    jQuery.notifyBar({
        html: data,
        delay: delay,
        animationSpeed: "normal",
        cssClass: cssClass //error or success or warning
    });
}


function wpl_rdtr_redirect_invalid_path() {

    jQuery(".wptr_main_body").addClass("wptr-rud-wpl-processing");
    var formdata = "action=wpl_training_public_handler&param=wpl_redirect_course_not_found";
    jQuery.post(rdtr_training.ajaxurl, formdata, function (response) {
        jQuery(".wptr_main_body").removeClass("wptr-rud-wpl-processing");
        var data = jQuery.parseJSON(response);
        if (data.sts == 1) {
            jQuery("#rdtr-load-course-body").html(data.arr.template);
        }
    });
}