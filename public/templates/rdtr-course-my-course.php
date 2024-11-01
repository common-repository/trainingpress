<?php
/**
 * This is my course page.
 * @author	Rudra Innnovative Software 
 * @package	training/templates/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;
?>

<div class="wptr-rud-training-pg wptr_content-section padd-0 wptr-rud-mycourse-section" id='mycourse-top'>

    <?php
    global $wpdb;
    global $user_ID;
    // if user logged in

    $query_param = isset($_REQUEST['req']) ? esc_attr($_REQUEST['req']) : '';
    ?>

    <div class='my_rdtr_topbar'>
        <ul class="rdtr-breadcrumb my-course_page wptr_items-center wptr_display-flex">
            <li><a href="<?php echo get_permalink(get_option("rdtr_training_allcourses_page")); ?>">Home</a></li>
            <li>Section <i class="mdi mdi-chevron-double-right font-nomal_icon"></i> My Course</li>
            <?php
            if (isset($query_param) && $query_param == "transactions") {
                ?>
                <a href="<?php echo get_permalink(get_option("rdtr_training_mycourse_page")); ?>" class="wptr_course-btn wptr_mleft-auto">Go to Course</a>
                <?php
            } else {

                if (has_action("payment_transaction_btn")) {
                    do_action("payment_transaction_btn");
                }
            }
            ?>

        </ul>
    </div>

    <?php
    if (is_user_logged_in()) {

        if (!empty($query_param) && $query_param == "transactions") {
            if (has_action("training_payment_transactions")) {
                do_action("training_payment_transactions", $user_ID);
            }
        } else {
            ?>
            <section class="wptr_dashboard-warpper wptr_ht-100 wptr_width-100" id="my-course-progress">

                <div id="content" class="wptr_width-100 wptr_ht-100 flt-right">

                    <div class="wptr_dasboard-content-area wptr_ptop-0 wptr_categories-section wptr_course-categorie">

                        <div class="wptr_container-fld">

                            <div class="wptr_row-section">

                                <?php
                                if (count($my_courses) > 0) {

                                    foreach ($my_courses as $inx => $course) {

                                        $currentUserProgress = $this->wpl_rd_get_course_progres_by_course_id($course['course_post_id'], $user_ID);
                                        $course_data = get_post($course['course_post_id']);
                                        $overallUserProgress = $this->wpl_rd_get_user_overall_course_progress($course['course_post_id']);
                                        $featured_img_url = get_the_post_thumbnail_url($course['course_post_id'], 'full');
                                        if (empty($featured_img_url)) {
                                            $featured_img_url = get_option("wpl_training_course_image");
                                        }
                                        ?>
                                        <div class="wptr_col-medium-3 wptr_col-tab-half wptr_col-small-12 wptr_categories-col wptr_py-yx-3 wptr_my-course">
                                            <div class="wptr_a-card wptr_overflow-hidden wptr_px-xy-0">
                                                <figure class="wptr_mbtm-0 wptr_img_flex">
                                                    <img class="card-img-top" src="<?php echo $featured_img_url; ?>" alt="<?php echo $course_data->post_title; ?>">
                                                </figure>
                                                <div class="wptr_a-card-body">
                                                    <h6 class="wptr_h6_6 wptr_a-card-title wptr_txt-truncate wptr_line-clp-2 wptr_txt-gray wptr_font-wt-bold wptr_pt-sans-font"><?php echo $course_data->post_title ?></h6>
                                                    <div class="wptr_col-small-12 wptr_mbtm-3">
                                                        <div class="wptr_row-section wptr_items-align-center">
                                                            <div class="wptr_px-xy-0"><p class="wptr_mbottom-1">Your Progress</p></div>
                                                            <div class="wptr_ml-auto wptr_txt-right wptr_px-xy-0"><p class="wptr_mbottom-1"><?php echo $currentUserProgress . "%"; ?></p></div>
                                                            <div class="wptr_process wptr_col-small-12 wptr_px-xy-0">
                                                                <div class="wptr_process-bar wptr_big-success" role="progressbar" style="width: <?php echo $currentUserProgress; ?>%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="wptr_col-small-12 wptr_mbtm-3">
                                                        <div class="wptr_row-section wptr_items-align-center">
                                                            <div class="wptr_px-xy-0"><p class="wptr_mbottom-1">Cohort Progress</p></div>
                                                            <div class="wptr_txt-right wptr_px-xy-0 wptr_ml-auto"><p class="wptr_mbottom-1"><?php echo $overallUserProgress . "%"; ?></p></div>
                                                            <div class="wptr_process wptr_col-small-12 wptr_px-xy-0">
                                                                <div class="wptr_process-bar wptr_big-success" role="progressbar" style="width: <?php echo $overallUserProgress; ?>%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr class="wptr_hr_1">
                                                    <?php
                                                    $current_page_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                                    $resume_url = $this->wpl_rd_get_resume_course_url($course['course_post_id'], $user_ID);
                                                    ?>
                                                    <a href="<?php echo $current_page_url; ?>?rpage=syllabus&course_id=<?php echo $course['course_post_id']; ?>" class="wptr_a wptr_btn-button wptr_btn-red wptr_button-small wptr_flt-left wptr_txt-none wptr_montserrat-font wptr_my-yx-0 wptr_line-ht_1  wptr_px-xy-md-3 wptr_Syllabus wptr_shadow-none-all wptr_txt-white wptr_rounded-corner-all ">Syllabus</a>
                                                    <?php
                                                    $course_status_detail = $wpdb->get_row(
                                                            $wpdb->prepare(
                                                                    "SELECT course_status from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE user_id = %d AND course_post_id = %d", $user_ID, $course['course_post_id']
                                                            )
                                                    );

                                                    if (!empty($course_status_detail)) {

                                                        $course_status = $course_status_detail->course_status;

                                                        if ($course_status) {
                                                            ?>
                                                            <a href="<?php echo $resume_url; ?>" class="wptr_a wptr_btn-button wptr_btn-red wptr_button-small wptr_flt-right  wptr_txt-none  wptr_montserrat-font wptr_my-yx-0 wptr_txt-white wptr_line-ht_1  wptr_px-xy-md-3 wptr_resume wptr_shadow-none-all wptr_rounded-corner-all ">Completed</a>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <a href="<?php echo $resume_url; ?>" class="wptr_a wptr_btn-button wptr_btn-red wptr_button-small wptr_flt-right  wptr_txt-none  wptr_montserrat-font wptr_my-yx-0 wptr_txt-white wptr_line-ht_1  wptr_px-xy-md-3 wptr_resume wptr_shadow-none-all wptr_rounded-corner-all ">Resume</a>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <a href="<?php echo $resume_url; ?>" class="wptr_a wptr_btn-button wptr_btn-red wptr_button-small wptr_flt-right  wptr_txt-none  wptr_montserrat-font wptr_my-yx-0 wptr_txt-white wptr_line-ht_1  wptr_px-xy-md-3 wptr_resume wptr_shadow-none-all wptr_rounded-corner-all ">Resume</a>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php
        }
    } else {
        ?>
        <p>You have no permission to access.</p>
        <?php
    }
    ?>
</div>

