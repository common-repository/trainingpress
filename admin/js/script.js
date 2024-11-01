/**
 * Plugin's admin javascript file
 * @author	Rudra Innnovative Software 
 * @package	training/admin/js/ 
 * @version	1.0.0
 */

jQuery(function () {
// console.log("helelll")
    var mulitple_choice_label_id;

    if (jQuery('.not-sort-p').length > 0) {

        var addSectionPosition = jQuery('.not-sort-p').offset();

        jQuery(window).scroll(function () {
            if (jQuery(window).scrollTop() > addSectionPosition.top) {
                jQuery('.not-sort-p').addClass("fix-exercise-bar-admin");
            } else {
                jQuery('.not-sort-p').removeClass("fix-exercise-bar-admin");
            }
        });
    }

    if (jQuery(".rdtr-paragraph-tinymce").length > 0) {
        rdtr_tinymce_editor_init();
    }

    if (jQuery("#dd-exercise-sections").length > 0) {
        /*jQuery("#dd-exercise-sections .inside").sortable({
         start: function (e, ui) {
         // creates a temporary attribute on the element with the old index
         //console.log(jQuery(this).attr("data-position-index"));
         },
         update: function (e, ui) {
         // gets the new and old index then removes the temporary attribute
         // console.log(jQuery(this).attr("data-position-index"));
         }
         });*/
    }

    jQuery(document).on("click", ".wpl-ex-btn", function () {

        var exe_id = jQuery(this).attr("data-id");
        var user_id = jQuery(this).attr("uid");

        var postdata = "action=rd_wpl_training_library&param=wpl_get_exercise_resouces&user_id=" + user_id + "&exe_id=" + exe_id;
        jQuery("body").addClass("wptr-rud-wpl-processing");
        jQuery.post(rdtr_training.ajaxurl, postdata, function (response) {
            //add processing class
            jQuery("body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                var template = data.arr.template;
                // console.log(template);

                jQuery("#exercise-files-dialog").addClass("ui_dilogbox_anim");

                jQuery("#user-exercise-status").html(template);

                jQuery("#exercise-files-dialog").dialog({
                    title: "Exercise progress view",
                    width: 600,
                    modal: true,
                    dialogClass: "wptr-rud-cstm-dialog"
                });
            }
        });
    });

    jQuery(document).on("click", ".wp-rd-open-file-dialog", function () {
        var dataType = jQuery(this).attr("data-type");
        var dataCourseId = jQuery(this).attr("data-cid");
        var dataUserId = jQuery(this).attr("data-uid");
        var title = '';
        if (dataType == "file") {
            title = "Course files";
        }
        if (dataType == "link") {
            title = "Course links";
        }

        var postdata = "action=rd_wpl_training_library&param=wpl_get_course_resouces&type=" + dataType + "&cid=" + dataCourseId + "&uid=" + dataUserId;
        jQuery("body").addClass("wptr-rud-wpl-processing");
        jQuery.post(rdtr_training.ajaxurl, postdata, function (response) {
            //add processing class
            jQuery("body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);

            if (data.sts == 1) {
                if (data.arr.file.length > 0) {
                    var html = '<div class="rdtr-send-survey-topbar"><h3 class="rdtr-send-survey-head">Course files</h3><div class="rdtr_modal_clsoe_btn"><span class="ui-dialog-titlebar-close"></span></div></div><ul class="rdtr_dialog_listing">';
                    var count = 1;
                    jQuery.each(data.arr.file, function (index, item) {
                        if (dataType == "link") {
                            html += '<li><a href="' + item.file + '" target="_blank">' + (count++) + '. ' + item.file + '</a></li>';
                        } else if (dataType == "file") {
                            html += '<li class="file_li_st"><span class="file_seq_no">' + (count++) + '. </span><a href="' + item + '" target="_blank" download class="file_anc_st">' + item + '</a></li>';
                        }
                    });
                    html += '</ul>';

                    jQuery("#wpl-dialog-message").html(html);

                    jQuery("#course-files-dialog").addClass("ui_dilogbox_anim");

                    jQuery("#course-files-dialog").dialog({
                        title: title,
                        width: 650,
                        modal: true,
                        dialogClass: "wptr-rud-cstm-dialog"
                    });
                }
            } else {
                wpl_training_notification(data.msg, 2000, "error");
            }

        });
    });

    jQuery(".txt-training-shortcode").on("click", function () {
        jQuery(this).select();
    });

    jQuery("#add-more-features-btn").on("click", function () {

        jQuery("#add-more-features").append('<div class="single-feature-div"><input type="text" class="course-feature-txt" name="txt_course_feature[]" placeholder="Enter feature..." size="30"/> <span class="remove-features-list">&times;</span></div>');
    });

    jQuery(document).on("click", "span.remove-features-list", function () {

        jQuery(this).parent("div.single-feature-div").remove();
    });

    jQuery("#wpl-save-settings").on("click", function () {
        //adding processing class
        jQuery("body").addClass("wptr-rud-wpl-processing");
        var postdata = jQuery("#frm-settings").serialize() + "&action=rd_wpl_training_library&param=training_settings";
        jQuery.post(rdtr_training.ajaxurl, postdata, function (response) {
            //remove processing class
            jQuery("body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                wpl_training_notification(data.msg, 2000, "success");
            } else {
                wpl_training_notification(data.msg, 2000, "error");
            }
        });
    });

    jQuery(".wpl-upload-image").on("click", function () {
        var image = wp.media({
            title: "Choose course default image",
            multiple: false
        }).open().on("select", function () {
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            var ext = image_url.split('.').pop().toLowerCase();
            if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                wpl_training_notification("Invalid file uploading", 2000, "error");
            } else {
                jQuery("#show-img-prev").html('<img src="' + image_url + '" class="course-img-prev">');
                jQuery("#rpl_course_img").val(image_url);
            }

        });
    });

    // read post type and according to that, given function implements sorting
    if (rdtr_training.post_type == "modules" || rdtr_training.post_type == "chapters" || rdtr_training.post_type == "exercises" || rdtr_training.post_type == "training") {

        //wpl_datatables_sort_training_sections();

        if (rdtr_training.post_type !== "training") { // post type either modules, chapters, exercises
            // hiding view link for cpt lists
            jQuery("span.view a[rel='bookmark']").remove();
        }
    }

    //remove submenu pages for cpt of modules, chapters, exercises
    jQuery("#menu-posts-modules").remove();
    jQuery("#menu-posts-chapters").remove();
    jQuery("#menu-posts-exercises").remove();

    // hide menus of modules from admin panel
    var mod = jQuery("li#toplevel_page_training > ul > li > a[href='edit.php?post_type=modules']");
    if (mod.length > 0) {
        mod.parent().css("display", "none");
    }
    // menus of chapters from admin panel
    var chap = jQuery("li#toplevel_page_training > ul > li > a[href='edit.php?post_type=chapters']");
    if (chap.length > 0) {
        chap.parent().css("display", "none");
    }
    // menus of exercises from admin panel
    var exe = jQuery("li#toplevel_page_training > ul > li > a[href='edit.php?post_type=exercises']");
    if (exe.length > 0) {
        exe.parent().css("display", "none");
    }

    // menus of user progress from admin panel
    var usr = jQuery("li#toplevel_page_training > ul > li > a[href='admin.php?page=user-progress']");
    if (usr.length > 0) {
        usr.parent().css("display", "none");
    }

    // menus of course progress from admin panel
    var view = jQuery("li#toplevel_page_training > ul > li > a[href='admin.php?page=view-progress']");
    if (view.length > 0) {
        view.parent().css("display", "none");
    }

    if (rdtr_training.post_type == "user_progress") {
        jQuery(".page-title-action").remove();
    }

    var is_valid = rdtr_training.is_post_type_valid;
    if (is_valid == 1) { // only for post types: "chapters", "training", "modules", "exercises"

        jQuery("input#title").attr("required", "required");

        jQuery("#post").validate();

        //adding extra parameter to url
        if (rdtr_training.post_type == "modules") {
            jQuery(".page-title-action").attr("href", rdtr_training.admin_url + "/post-new.php?post_type=modules&course_id=" + rdtr_training.course_id);
        } else if (rdtr_training.post_type == "chapters") {
            jQuery(".page-title-action").attr("href", rdtr_training.admin_url + "/post-new.php?post_type=chapters&module_id=" + rdtr_training.module_id);
        } else if (rdtr_training.post_type == "exercises") {

            // for paragraphs
            var para_count = paraCount;
            var para_id = "ex_para";
            var para_name = "ex_para";
            // for image or video file
            var file_count = fileCount;
            var file_id = "ex_file";
            var file_name = "ex_file";
            //for mcq
            var mcq_count = mcqCount;
            var mcq_id = "ex_mcq";
            var mcq_name = "ex_mcq";
            //for mcq
            var single_count = singleCount;
            var single_id = "ex_single";
            var single_name = "ex_single";
            //for poll type
            var poll_count = pollCount;
            var poll_id = "ex_poll_type";
            var poll_name = "ex_poll_type";
            //for reflection
            var reflecation_count = reflectionCount;
            var reflecation_id = "ex_reflection";
            var reflecation_name = "ex_reflection";

            var element_count = 1;

            var sort_count = 1;

            jQuery(".page-title-action").attr("href", rdtr_training.admin_url + "/post-new.php?post_type=exercises&chapter_id=" + rdtr_training.exercise_id);
        }
    }

    jQuery("input[name=rdb_upload_video]").on("click", function () {
        var uploadType = jQuery(this).val();
    });

    jQuery("#dd-ex-section-type").on("click", function () {
        var sectionType = jQuery(this).val();
        var exercise_parent_div = jQuery("#dd-exercise-sections");

        jQuery("select#dd-ex-section-type").val("-1");

        if (sectionType == "paragraph") {

            para_count++;
            para_name = "ex_para";
            para_id = "ex_para";
            para_name = para_name + "_" + para_count;
            para_id = para_id + "_" + para_count;
            var returned_html = wpl_ex_generate_paragrph(para_id, para_name, element_count, sort_count);
            exercise_parent_div.append(returned_html);
            rdtr_tinymce_editor_init();
            sort_count++;
            element_count++;
        } else if (sectionType == "video_image") {

            file_count++;
            file_name = "ex_file";
            file_id = "ex_file";

            file_name = file_name + "_" + file_count;
            file_id = file_id + "_" + file_count;
            var returned_html = wpl_ex_generate_video_image_html_generator(file_id, file_name, element_count);
            exercise_parent_div.append(returned_html);
            sort_count++;
            element_count++;

        } else if (sectionType == "mulitple_choice") {
            rdtr_training.label_count = 0;
            mcq_count++;
            mcq_name = "ex_mcq";
            mcq_id = "ex_mcq";
            mulitple_choice_label_id = 0;
            mcq_name = mcq_name + "_" + mcq_count;
            mcq_id = mcq_id + "_" + mcq_count;

            var returned_html = wpl_ex_generate_mcq_html_generator(mcq_id, mcq_name, element_count, sort_count, mulitple_choice_label_id);
            exercise_parent_div.append(returned_html);
            sort_count++;
            element_count++;
        } else if (sectionType == "single_choice") {
            rdtr_training.label_count = 0;
            single_count++;
            single_name = "ex_single";
            single_id = "ex_single";

            single_name = single_name + "_" + single_count;
            single_id = single_id + "_" + single_count;
            var returned_html = wpl_ex_generate_single_choice(single_id, single_name, element_count, sort_count, rdtr_training.label_count);
            exercise_parent_div.append(returned_html);
            sort_count++;
            element_count++;
        } else if (sectionType == "poll_type") {
            rdtr_training.label_count = 1;
            poll_count++;
            poll_name = "ex_poll_type";
            poll_id = "ex_poll_type";

            poll_name = poll_name + "_" + poll_count;
            poll_id = poll_id + "_" + poll_count;
            var returned_html = wpl_ex_generate_poll_type(poll_id, poll_name, element_count, sort_count, rdtr_training.label_count);
            exercise_parent_div.append(returned_html);
            sort_count++;
            element_count++;
        } else if (sectionType == "reflection") {

            reflecation_count++;
            reflecation_name = "ex_reflection";
            reflecation_id = "ex_reflection";

            reflecation_name = reflecation_name + "_" + reflecation_count;
            reflecation_id = reflecation_id + "_" + reflecation_count;
            var returned_html = wpl_ex_generate_reflection(reflecation_id, reflecation_name, element_count);
            exercise_parent_div.append(returned_html);
            rdtr_tinymce_editor_init();
            sort_count++;
            element_count++;
        } else {
            wpl_training_notification("Invalid selection", 2000, "error");
        }

        jQuery("html, body").animate({ scrollTop: jQuery(document).height() }, "slow");
        return false;
    });

    jQuery(document).on("click", "input.rdb_upload_file_ex", function () {
        var file_type = jQuery(this).val();
        var returned_html;
        if (file_type == "gallery") {
            jQuery(this).parents(".ex-add").find(".para-youtube").css("display", "none");
            jQuery(this).parents(".ex-add").find(".para-gallery").css("display", "block");
            //file_name = file_name + "_" + file_count;
            returned_html = wp_ex_generate_file_gallery_html(file_id, file_name, element_count, file_count);
        } else if (file_type == "youtube") {
            jQuery(this).parents(".ex-add").find(".para-youtube").css("display", "block");
            jQuery(this).parents(".ex-add").find(".para-gallery").css("display", "none");
            //file_name = file_name + "_" + file_count;
            returned_html = wp_ex_generate_file_youtube_html(file_id, file_name, element_count, file_count);
        }
        jQuery(this).parents(".ex-add").find(".add-file-html").html(returned_html);
    });

    jQuery(document).on("click", ".btn-upload-gallery", function () {
        var thisElement = jQuery(this);
        var file = wp.media({
            title: 'Upload Image',
            multiple: false
        }).open()
                .on('select', function (e) {
                    var uploaded_file = file.state().get('selection').first();
                    var file_url = uploaded_file.toJSON().url;
                    var ext = file_url.split('.').pop().toLowerCase();

                    if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg', 'mp4', 'webm']) !== -1) {

                        if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                            jQuery(thisElement).parent().find(".rd-img-prev-fit").attr("src", '');
                        } else {
                            jQuery(thisElement).parent().find(".rd-img-prev-fit").attr("src", file_url);
                        }
                        jQuery(thisElement).parent().find(".txt_file_url").val(file_url);

                        jQuery("#dd-exercise-sections #gallery-upload-msg").html('<br/><label><i>File path: ' + file_url + '</i></label>');

                    } else {
                        wpl_training_notification("Invalid fil format uploaded", 2000, "error");
                    }

                });
    });

    jQuery(document).on("click", ".add-more-chk-option", function () {
        var mc_name = mcq_name;
        rdtr_training.label_count = parseInt(rdtr_training.label_count) + 1;
        var dataIdAttr = jQuery(this).attr('data-id');
        if (typeof dataIdAttr !== typeof undefined && dataIdAttr !== false) {
            jQuery(this).parents(".add-mcq-choices").find("ul.ul-add-option").append('<li>\n\
                <input type="checkbox" value="' + rdtr_training.label_count + '" name="' + mcq_name + '_correct[]"/> \n\
                <input type="text" data-id="' + rdtr_training.label_count + '" name="' + dataIdAttr + '_option[]"/>\n\
                <input type="hidden" value="' + rdtr_training.label_count + '" name="' + mcq_name + '_answer_index[]"/> \n\
                <button class="btn-remove-option"><i class="fa fa-trash-o" aria-hidden="true"></i></button>\n\
                </li>');
        } else {
            jQuery(this).parents(".add-mcq-choices").find("ul.ul-add-option").append('<li>\n\
                <input type="checkbox" value="' + rdtr_training.label_count + '" name="' + mcq_name + '_correct[]"/>\n\
                 <input type="text" data-id="' + rdtr_training.label_count + '" name="' + mc_name + '_option[]"/>\n\
                <input type="hidden" value="' + rdtr_training.label_count + '" name="' + mcq_name + '_answer_index[]"/> \n\
            <button class="btn-remove-option"><i class="fa fa-trash-o" aria-hidden="true"></i></button>\n\
                </li>');
        }
    });

    jQuery(document).on("click", ".add-more-radio-option", function () {
        var sg_name = single_name;
        rdtr_training.label_count = parseInt(rdtr_training.label_count) + 1;
        var dataIdAttr = jQuery(this).attr('data-id');
        if (typeof dataIdAttr !== typeof undefined && dataIdAttr !== false) {
            jQuery(this).parents(".add-single-choice").find("ul.ul-add-option").append('<li>\n\
                <input type="radio" value="' + rdtr_training.label_count + '" name="' + sg_name + '_rdb_correct"/> \n\
                <input type="text" data-id="' + rdtr_training.label_count + '" name="' + dataIdAttr + '_option[]" class="rdb_single_answer_text"/>\n\
                <input type="hidden" value="' + rdtr_training.label_count + '" name="' + sg_name + '_answer_index[]"/> \n\
                <button class="btn-remove-option"><i class="fa fa-trash-o" aria-hidden="true"></i></button>\n\
                </li>');
        } else {
            jQuery(this).parents(".add-single-choice").find("ul.ul-add-option").append('<li>\n\
                            <input type="radio" value="' + rdtr_training.label_count + '" name="' + sg_name + '_rdb_correct"/> \n\
                            <input type="text" data-id="' + rdtr_training.label_count + '" name="' + sg_name + '_option[]" class="rdb_single_answer_text"/>\n\
                            <input type="hidden" value="' + rdtr_training.label_count + '" name="' + sg_name + '_answer_index[]"/> \n\
                            <button class="btn-remove-option"><i class="fa fa-trash-o" aria-hidden="true"></i></button>\n\
                            </li>');
        }
    });

    jQuery(document).on("click", ".add-more-poll-option", function () {
        var sg_name = poll_name;
        var dataIdAttr = jQuery(this).attr('data-id');
        var dataNext = jQuery(this).attr('data-next');

        if (typeof dataNext !== typeof undefined && dataNext !== false) {
            rdtr_training.label_count = dataNext;
        }
        rdtr_training.label_count = parseInt(rdtr_training.label_count) + 1;
        if (typeof dataNext !== typeof undefined && dataNext !== false) {
            jQuery(this).attr('data-next', rdtr_training.label_count);
        }
        if (typeof dataIdAttr !== typeof undefined && dataIdAttr !== false) {
            jQuery(this).parents(".add-poll-choice").find("ul.ul-add-option").append('<li> ' + rdtr_training.label_count + '. <input type="text" name="' + dataIdAttr + '_option[]"/></li>');
        } else {
            jQuery(this).parents(".add-poll-choice").find("ul.ul-add-option").append('<li> ' + rdtr_training.label_count + '. <input type="text" name="' + sg_name + '_option[]"/></li>');
        }
    });

    jQuery(document).on("click", ".ex-move-section-to-trash", function () {
        var conf = confirm("Are you sure want to remove?");
        if (conf) {
            jQuery(this).parents(".ex-add").remove();
        }
    });

    if (rdtr_training.post_type == "chapters") {
        jQuery("input[name='rdb_assignment']").on("click", function () {

            var rdb_value = jQuery(this).val();
            if (rdb_value == "yes") {
                jQuery("div#chatpter-assignment-editor").css({
                    display: "block"
                });
            } else {
                jQuery("div#chatpter-assignment-editor").css({
                    display: "none"
                });
            }
        });
    }

    if (rdtr_training.post_type == "modules") {

        var post_search_form = jQuery("#posts-filter");
        if (post_search_form.length > 0) {
            post_search_form.find(".search-box").prepend('<input type="hidden" id="filter_by_course" name="filter_by_course" value="' + rdtr_training.course_id + '">');
        }

    }

    if (rdtr_training.post_type == "chapters") {

        var post_search_form = jQuery("#posts-filter");
        if (post_search_form.length > 0) {
            post_search_form.find(".search-box").prepend('<input type="hidden" id="filter_by_module" name="filter_by_module" value="' + rdtr_training.module_id + '">');
        }

    }

    if (rdtr_training.post_type == "exercises") {

        var post_search_form = jQuery("#posts-filter");
        if (post_search_form.length > 0) {
            post_search_form.find(".search-box").prepend('<input type="hidden" id="filter_by_chapter" name="filter_by_chapter" value="' + rdtr_training.exercise_id + '">');
        }

    }

    jQuery('#course-enrolled-users').DataTable();

    jQuery('.modules-list-course').DataTable();

    jQuery(document).on("click", ".show-exercise-progress-dialog", function () {

        var exercise_id = jQuery(this).attr("data-id");
        var user_id = jQuery(this).attr("data-user");

        var postdata = "action=rd_wpl_training_library&param=wpl_get_exercise_resouces&exe_id=" + exercise_id + "&user_id=" + user_id;
        jQuery("body").addClass("wptr-rud-wpl-processing");
        jQuery.post(rdtr_training.ajaxurl, postdata, function (response) {
            //add processing class
            jQuery("body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                var template = data.arr.template;
                // console.log(template);

                jQuery("#exercise-progress-view").addClass("ui_dilogbox_anim");

                jQuery("#rdtr-exercise-content").html(template);

                jQuery("#exercise-progress-view").dialog({
                    title: "Exercise progress view",
                    width: 600,
                    height: 500,
                    resizable: false,
                    modal: true,
                    dialogClass: "wptr-rud-cstm-dialog"
                });
            }
        });
    });

    jQuery(document).on("click", ".ch-view-uploads", function () {

        var data_chapter = jQuery(this).attr("data-chapter");
        var user_id = jQuery(this).attr("data-user");

        jQuery("body").addClass("wptr-rud-wpl-processing");
        var postdata = "chapter_id=" + data_chapter + "&action=rd_wpl_training_library&param=show_chapter_uploads&user_id=" + user_id;
        jQuery.post(rdtr_training.ajaxurl, postdata, function (response) {

            jQuery("body").removeClass("wptr-rud-wpl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {

                if (data.arr.files.length > 0) {

                    var html = '<div class="rdtr-send-survey-topbar"><h2 class="rdtr-send-survey-head ui_label_login wptr_h2_1 text-center wptr_font-wt-bold">Course files</h2></div><ul class="rdtr_dialog_listing">';
                    var count = 1;
                    jQuery.each(data.arr.files, function (index, item) {
                        html += '<li class="file_li_st"><span class="file_seq_no">' + (count++) + '. </span><a href="' + item + '" target="_blank" download class="file_anc_st">' + item + '</a></li>';
                    });
                    html += '</ul>';

                    jQuery("#user-progress-content").html(html);

                    jQuery("#uploads-view").addClass("ui_dilogbox_anim");

                    jQuery("#uploads-view").dialog({
                        title: "Chapter Uploads List",
                        width: 650,
                        modal: true,
                        dialogClass: "wptr-rud-cstm-dialog"
                    });

                }
            } else {

            }
        });

    });

    jQuery(document).on("click", "#anchor_banner_upload", function () {

        var image = wp.media({
            title: "Upload Banner of 1200 X 350",
            multiple: false
        }).open().on("select", function () {
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            var ext = image_url.split('.').pop().toLowerCase();
            if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) > 0) {
            // if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == 1) {

                var postdata = "action=rd_wpl_training_library&param=check_banner_image_resolution&image_url=" + image_url;
                jQuery.post(rdtr_training.ajaxurl, postdata, function (response) {

                    var data = jQuery.parseJSON(response);

                    if (data.sts == 1) {
                        jQuery(".banner-image-preview").attr("src", image_url);
                        jQuery("#course_banner_image").val(image_url);
                        jQuery(".banner-image-preview").addClass("banner-image-preview-image");
                    } else {
                        wpl_training_notification(data.msg, 2000, "error");
                    }
                });

            } else {
                wpl_training_notification("Invalid file uploading", 2000, "error");
            }
        });

    });

    jQuery(document).on("click", "#btn-install-sample-course", function () {

        var conf = confirm("Are you sure want to Import Sample data");

        if (conf) {

            var postdata = "action=rd_wpl_training_library&param=wpl_rdtr_import_sample_course_data";

            jQuery("body").addClass("wptr-rud-wpl-processing");
            jQuery.post(rdtr_training.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wptr-rud-wpl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpl_training_notification(data.msg, 2000, "success");
                } else {
                    wpl_training_notification(data.msg, 2000, "error");
                }
            });
        }
    });

    jQuery(document).on("click", ".btn-remove-option", function () {

        jQuery(this).parents("li").remove();
    });

});

function wpl_ex_generate_paragrph(paragraph_id, paragraph_name, count, sort_count) {

    var html_para = '<div class="ex-add ex-section-sort paragraph-section" data-position-index="' + sort_count + '"><label class="lbl-ex-para">Please put contents to read for users</label><button type="button" class="button ex-btn ex-move-section-to-trash">Remove section</button><p> <textarea rows="6" cols="80" name="' + paragraph_name + '" class="rdtr-paragraph-tinymce exercise-textarea"></textarea></p> <input type="hidden" value="' + paragraph_name + "_" + count + '" name="section_order[]"/></div>';
    return html_para;
}

function wpl_ex_generate_video_image_html_generator(file_id, file_name, count, sort_count) {

    var file_html = '<div class="ex-add ex-section-sort video-section" data-position-index="' + sort_count + '"><label class="lbl-ex-para">Image/Video section</label><button class="button ex-btn ex-move-section-to-trash" type="button">Remove section</button><p>Upload video <input type="radio" name="rdb_upload_file" class="rdb_upload_file_ex" value="gallery"/> Gallery <input type="radio" class="rdb_upload_file_ex" name="rdb_upload_file" value="youtube"/> Youtube</p><div class="add-file-html"></div></div>';
    return file_html;
}

function wp_ex_generate_file_gallery_html(gallery_id, gallery_name, count, file_count) {

    gallery_name = gallery_name + "_" + file_count;
    var gallery_html = '<div class="para-gallery"><button class="button button-primary button-large btn-upload-gallery" type="button">Click here to upload</button><span id="gallery-upload-msg"></span><img src="" class="rd-img-prev-fit"/><input type="hidden" value="" name="' + gallery_name + '" class="txt_file_url"/><input type="hidden" value="' + gallery_name + "_" + count + '" name="section_order[]"/><input type="hidden" name="' + gallery_name + '_upload_type" value="gallery"/></div>';
    return gallery_html;
}

function wp_ex_generate_file_youtube_html(youtube_id, youtube_name, count, file_count) {

    youtube_name = youtube_name + "_" + file_count;
    var youtube_html = '<p class="para-youtube"><label>Youtube URL</label><input type="url" name="' + youtube_name + '"/></p><input type="hidden" value="' + youtube_name + "_" + count + '" name="section_order[]"/> <input type="hidden" name="' + youtube_name + '_upload_type" value="youtube"/>';
    return youtube_html;
}

function wpl_ex_generate_mcq_html_generator(mcq_id, mcq_name, count, sort_count, mulitple_choice_label_id) {

    var mcq_html = '<div class="ex-add ex-section-sort mcq-section" data-position-index="' + sort_count + '">\n\
                        <label class="lbl-ex-para">Question with Multiple Choice</label>\n\
                            <button class="button ex-btn ex-move-section-to-trash" type="button">Remove section</button>\n\
                        <span class="fix-messgae-note"><i>Note*: Please Select options after making MCQ else user\'s response will not be tracked.</i></span>\n\
                         <p class="mcq-area"><label class="lbl-ex-bold">Question</label>\n\
                            <input type="text" name="' + mcq_name + '_question"/>\n\
                        </p><div class="add-mcq-choices">\n\
                        <label class="lbl-ex-bold">Add Options</label>\n\
                        <ul class="ul-add-option">\n\
                            <li>\n\
                              <input type="checkbox" value="' + mulitple_choice_label_id + '" name="' + mcq_name + '_correct[]"/>\n\
                              <input type="text" data-id="' + mulitple_choice_label_id + '" name="' + mcq_name + '_option[]"/>\n\
\n\                           <input type="hidden" value="' + mulitple_choice_label_id + '" name="' + mcq_name + '_answer_index[]"/> \n\
                            </li>\n\
                         </ul>\n\
                         <button class="button button-primary button-large add-more-chk-option" type="button"> + Add more</button>\n\
                        </div></p>\n\
                        <p>\n\
                            <label class="lbl-ex-bold">Answer Explanation</label>\n\
                            <textarea class="exercise-textarea" rows="4" cols="40" name="' + mcq_name + '_answer_explanation" placeholder="Explanation"></textarea>\n\
                        </p>\n\
                        <input type="hidden" value="' + mcq_name + "_" + count + '" name="section_order[]"/>\n\
                    </div>';

    return mcq_html;
}

function wpl_ex_generate_single_choice(single_id, single_name, count, sort_count, label_count) {

    var single_choice = '<div class="ex-add ex-section-sort single-choice-section" data-position-index="' + sort_count + '">\n\
                            <label class="lbl-ex-para">Question with Single Choice</label>\n\
                            <button class="button ex-btn ex-move-section-to-trash" type="button">Remove section</button>\n\
                            <span class="fix-messgae-note"><i>Note*: Please Select option after making Single Choice else user\'s response will not be tracked.</i></span><p class="mcq-area">\n\
                                <label class="lbl-ex-bold">Question</label>\n\
                                <input type="text" name="' + single_name + '_question"/>\n\
                            </p>\n\
                            <div class="add-single-choice">\n\
                            <label class="lbl-ex-bold">Add Options</label>\n\
                            <ul class="ul-add-option">\n\
                                <li>\n\
                                    <input type="radio" value="' + label_count + '" name="' + single_name + '_rdb_correct"/> \n\
                                    <input type="text" data-id="' + label_count + '" name="' + single_name + '_option[]" class="rdb_single_answer_text"/>\n\
                                    <input type="hidden" value="' + label_count + '" name="' + single_name + '_answer_index[]"/> \n\
                                </li>\n\
                            </ul>\n\
                            <button class="button button-primary button-large add-more-radio-option" type="button"> + Add more</button>\n\
                            <p><label class="lbl-ex-bold">Answer Explanation</label>\n\
                            <textarea class="exercise-textarea" rows="4" cols="40" name="' + single_name + '_answer_explanation" placeholder="Explanation"></textarea></p>\n\
                            <input type="hidden" value="' + single_name + "_" + count + '" name="section_order[]"/>\n\
                            </div>\n\
                         </div>';

    return single_choice;
}

function wpl_ex_generate_reflection(reflection_id, reflection_name, count, sort_count) {

    var html_ref = '<div class="ex-add ex-section-sort reflection-section" data-position-index="' + sort_count + '"><label class="lbl-ex-para">Please provide a Question for the user.</label><button type="button" class="button ex-btn ex-move-section-to-trash">Remove section</button><p> <textarea rows="6" cols="80" name="' + reflection_name + '" class="rdtr-paragraph-tinymce exercise-textarea" ></textarea></p> <input type="hidden" value="' + reflection_name + "_" + count + '" name="section_order[]"/></div>';
    return html_ref;
}

function wpl_ex_generate_poll_type(poll_id, poll_name, element_count, sort_count, lb_count) {

    var poll_type = '<div class="ex-add ex-section-sort poll-section" data-position-index="' + sort_count + '">\n\
                        <label class="lbl-ex-para">Poll type Question</label>\n\
                        <button class="button ex-btn ex-move-section-to-trash" type="button">Remove section</button>\n\
                        <p class="mcq-area"><label class="lbl-ex-bold">Question</label>\n\
                        <input type="text" name="' + poll_name + '_question"/>\n\
                        </p>\n\
                        <div class="add-poll-choice">\n\
                        <label class="lbl-ex-bold">Add Options</label>\n\
                        <ul class="ul-add-option">\n\
                        <li> ' + lb_count + '. <input type="text" name="' + poll_name + '_option[]"/></li>\n\
                        </ul><button class="button button-primary button-large add-more-poll-option" type="button"> + Add more</button>\n\
                        <input type="hidden" value="' + poll_name + "_" + element_count + '" name="section_order[]"/>\n\
                        </div>\n\
                    </div>';

    return poll_type;
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

// Common function to implement sorting
function wpl_datatables_sort_training_sections() {

    jQuery('table.posts #the-list,table.pages #the-list').css({
        cursor: "move"
    });

    jQuery('table.posts #the-list,table.pages #the-list').sortable({
        'items': 'tr',
        'axis': 'y',
        'update': function (e, ui) {

            var post_type = jQuery('input[name="post_type"]').val();
            var order = jQuery('#the-list').sortable('serialize');

            var paged = wpl_rd_getUrlParameter('paged');
            if (typeof paged === 'undefined')
                paged = 1;

            var queryString = {
                "action": "rd_wpl_training_library",
                "post_type": post_type,
                "order": order,
                "paged": paged,
                "param": "exercise_sort"
            };
            //send the data through ajax
            jQuery.ajax({
                type: 'POST',
                url: rdtr_training.ajaxurl,
                data: queryString,
                cache: false,
                dataType: "html",
                success: function (data) {

                },
                error: function (html) {

                }
            });

        }
    });
}

// function to return url parameters
function wpl_rd_getUrlParameter(sParam) {

    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

function rdtr_tinymce_editor_init() {

    /*tinymce.init({
     selector: '.rdtr-paragraph-tinymce',
     toolbar: [
     'undo redo | styleselect | bold italic | link image',
     'alignleft aligncenter alignright'
     ],
     menubar: false,
     convert_newlines_to_brs:true,
     });*/
}


//Code for the backend validation of Exercise sections.

jQuery(document).ready(function($) {

    // Function to clear error messages for the video/image section
    function clearErrorMessages($section) {
        $section.find('.error_message').remove();
    }
 
    $(document).on('change', 'input[name="rdb_upload_file"]', function() {
        var $section = $(this).closest('.video-section');
        clearErrorMessages($section); // Clear error messages on change
    });
 
    // Function to validate questions
    function validateQuestion($section) {
        var $question_input = $section.find('input[name$="_question"]');
        var $reflection_input = $section.find('textarea[name^="ex_reflection_"]'); // Adjusted selector
        var $paragraph_input = $section.find('textarea[name^="ex_para_"]');
        var question_text = $question_input.val()?.trim();
        var reflection_text = $reflection_input.val()?.trim();
        var paragraph_text = $paragraph_input.val()?.trim();
        
        if ($section.hasClass('reflection-section')) {
            if (reflection_text === '') {
                $section.append('<div class="error_message">Please enter a question for this reflection section.</div>');
                return false;
            }
        }
        else if($section.hasClass('paragraph-section')){
            if (paragraph_text === '') {
                $section.append('<div class="error_message">Please enter text for this pargraph section.</div>');
                return false;
            }
        } 
        else {
            if (question_text === '') {
                $section.append('<div class="error_message">Please enter a question for this section.</div>');
                return false;
            }
        }
        return true;
    }

    // Function to validate options
    function validateOptions($section) {
        var $options = $section.find('input[name$="_option[]"]');
        var all_options_filled = true;
        $options.each(function() {
            if ($(this).val().trim() === '') {
                all_options_filled = false;
                return false; // Break out of the loop
            }
        });

        if (!all_options_filled) {
            $section.append('<div class="error_message">Please fill in all options for this section.</div>');
            return false;
        }
        
        // Check for poll type sections to have at least two options
        if ($section.hasClass('poll-section') && $options.length < 2) {
            $section.append('<div class="error_message">Please provide at least two options for this poll question.</div>');
            return false;
        }

        return true;
    }

    // Function to validate correct answers
    function validateCorrectAnswers($section, is_single_choice) {
        var $correct_answers;
        if (is_single_choice) {
            $correct_answers = $section.find('input[name$="_rdb_correct"]:checked');
        } else {
            $correct_answers = $section.find('input[name$="_correct[]"]:checked');
        }

        if ($correct_answers.length === 0) {
            var error_message = is_single_choice ?
                'Please select one option as the correct answer for this single choice question.' :
                'Please select at least one option as the correct answer for this MCQ question.';
            $section.append('<div class="error_message">' + error_message + '</div>');
            return false;
        }
        return true;
    }

// Function to validate video/image section
function validateVideoImageSection($section) {
    var $upload_type = $section.find('input[name^="rdb_upload_"]:checked');
    var $gallery_hidden_input = $section.find('.para-gallery input[name^="ex_file_"].txt_file_url'); // Adjusted selector

   // Clear any existing error messages
   clearErrorMessages($section);

    if ($upload_type.length === 0) {
        $section.append('<div class="error_message">Please select an upload type (Gallery or YouTube).</div>');
        return false;
    }

    var upload_type_value = $upload_type.val();
       if (upload_type_value === 'gallery') {
        
        // Check if there is a non-empty hidden input for gallery uploads
        var gallery_file_provided = $gallery_hidden_input.filter(function() {
            return $(this).val()?.trim() !== '';
        }).length > 0;
        
        if (!gallery_file_provided) {
            $section.append('<div class="error_message">Please upload a file from the gallery.</div>');
            return false;
        }
    } else if (upload_type_value === 'youtube') {
        var youtube_url = $section.find('.add-file-html input[type="url"]').val()?.trim();
        if (youtube_url === '') {
            $section.append('<div class="error_message">Please provide a YouTube URL.</div>');
            return false;
        }
    }

    return true;
}
    // Main validation function
    function validateSections() {
        var is_valid = true;
        $('.ex-add.ex-section-sort').each(function() {
            var $section = $(this);
            var is_single_choice = $section.hasClass('single-choice-section');
            var is_mcq = $section.hasClass('mcq-section');
            var is_poll = $section.hasClass('poll-section');
            var is_reflection = $section.hasClass('reflection-section');
            var is_paragraph = $section.hasClass('paragraph-section');
            var is_video = $section.hasClass('video-section');
            if (!validateQuestion($section)) {
                is_valid = false;
                return false; // Break out of the loop
            }

            if (!validateOptions($section)) {
                is_valid = false;
                return false; // Break out of the loop
            }

            if (is_single_choice || is_mcq) {
                if (!validateCorrectAnswers($section, is_single_choice)) {
                    is_valid = false;
                    return false; // Break out of the loop
                }
            }
            if (is_video) {
                if (!validateVideoImageSection($section)) {
                    is_valid = false;
                    return false; // Break out of the loop
                }
            }

        });

        return is_valid;
    }

    $('#publish').on('click', function(event) {
        $('.error_message').remove(); // Clear previous errors
         if (!validateSections()) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    });
});



