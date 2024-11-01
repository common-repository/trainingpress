<?php
/**
 * This is course description template file.
 * @author	Rudra Innnovative Software 
 * @package	training/templates/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;

get_header();

global $post;
global $wpdb;
global $user_ID;

$query_param = isset($_REQUEST['query_param']) ? esc_attr(trim($_REQUEST['query_param'])) : "";
$rc_id = isset($_REQUEST['rc_id']) ? esc_attr(trim($_REQUEST['rc_id'])) : "";

//table
require_once RDTR_TRAINING_DIR_PATH . 'includes/class-rdtr-activator.php';
$table_activator = new Rdtr_Activator();

$user_login_status = is_user_logged_in(); // checking login status of user
$comments_rating = 0;
$comments = get_approved_comments($post->ID); // get comments
$rating = 0;
if ($comments) {
    $i = 0;
    $total = 0;
    $comments_rating = count($comments);
    foreach ($comments as $comment) {
        $rate = get_comment_meta($comment->comment_ID, 'rating', true);
        if (isset($rate) && '' !== $rate) {
            $i++;
            $total += $rate;
        }
    }

    if (0 === $i) {
        $rating = 0;
    } else {
        $rating = round($total / $i, 3);
    }
}

$total_users = $wpdb->get_var(
        $wpdb->prepare(
                "SELECT count(user_id) from " . $table_activator->wpl_rd_user_enroll_tbl() . " WHERE course_post_id = %d", $post->ID
        )
);


$modules = $wpdb->get_results(
        $wpdb->prepare(
                "SELECT post_id from " . $wpdb->postmeta . " WHERE meta_key = %s AND meta_value = %d", "dd_course_box_post_type", get_the_ID()
        ), ARRAY_A
);
$module_ids = array_column($modules, "post_id");
$ids_string = implode(",", $module_ids);


if (filter_input(INPUT_GET, 'comment_submit') === 'success') {
    $redirect_to = filter_input(INPUT_GET, 'redirect_to');
    ?>
    <script>
        jQuery.notifyBar({
            html: "Comment added successfully",
            delay: 2000,
            animationSpeed: "normal",
            cssClass: "success" //error or success or warning
        });

        setTimeout(function () {
            window.location.href = "<?php echo $redirect_to; ?>";
        }, 2000);
    </script>

<?php } ?>

<div class="wptr-rud-training-pg wptr_content-section wptr-rud-single-course-desc" id="training_course_name">

    <div class='my_rdtr_topbar'>
        <ul class="rdtr-breadcrumb my-course_page">
            <li><a href="<?php echo get_permalink(get_option("rdtr_training_allcourses_page")); ?>">Home</a></li>
            <li>
                <a href="<?php
                if ($user_ID > 0) {
                    echo get_permalink(get_option("rdtr_training_mycourse_page"));
                } else {
                    echo get_permalink(get_option("rdtr_training_allcourses_page"));
                }
                ?>">
                       <?php
                       if ($user_ID > 0) {
                           echo 'My Course';
                       } else {
                           echo 'Courses';
                       }
                       ?>
                </a>
            </li>
            <li>Course description <i class="mdi mdi-chevron-double-right font-nomal_icon"></i> <?php echo get_the_title(); ?></li>
        </ul>
    </div>

    <?php
    if ($query_param == "receipt") {
        if (has_action("training_receipt")) {
            do_action("training_receipt", array("course_id" => get_the_ID(), "rc_id" => $rc_id));
        }
    } else {
        ?>

        <div class="wptr_course-description">
            <?php
            $image = RDTR_TRAINING_PLUGIN_URL . 'public/img/dummy-image-dis.jpg';
            $banner_image = get_post_meta($post->ID, "course_banner_image", true);
            if (!empty($banner_image)) {
                $image = $banner_image;
            }
            ?>
            <section class="wptr_course-info" style="background-image: url(<?php echo $image; ?>);">

                <div class="wptr_left-section">
                    <div class="wptr-left-section-inner-top">
                        <h3 class="wptr_h2_1  wptr_course-title">
                            <?php echo get_the_title(); ?>
                        </h3>
                        <?php
                        if (has_action("rdtr_show_payment_amount")) {
                            do_action("rdtr_show_payment_amount", get_the_ID());
                        } else {
                            ?>
                            <div class="rdtr-published-price-sec">
                                <span class="rdtr-course-price wptr-course-free">FREE</span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <div class="wptr_r-section wptr_pt-sans rdtr-rating-sec">
                        <div class="wptr_badge-star badge-arrow-right z-index pt-sans py-1 px-3">Average Rating</div>
                        <div class="wptr_rating">
                            <?php
                            if (!empty($rating)) {
                                $half_rate = preg_match('/^\d+\.\d+$/', $rating);
                                $stars = '<div class="stars">';
                                $starsRt = '';
                                $blank_star = (5 - ceil($rating));

                                for ($i = 1; $i <= $rating; $i++) {
                                    $starsRt .= '<i class="dashicons dashicons-star-filled"></i>';
                                }
                                if ($half_rate) {
                                    $starsRt .= '<i class="dashicons dashicons-star-half"></i>';
                                }
                                if ($blank_star > 0) {
                                    for ($i = 1; $i <= $blank_star; $i++) {
                                        $starsRt .= '<i class="dashicons dashicons-star-empty"></i>';
                                    }
                                }
                                $stars .= '<div id="stars-view">' . $starsRt . '</div>';
                                $stars .= '<div id="avg-rting">' . $rating . '&nbsp;</div>';
                                $stars .= '<div id="total-rting">  (' . $comments_rating . 'ratings)</div>';
                                $stars .= '</div>';
                                echo $stars;
                                ?> 
                                <?php
                            } else {
                                echo '<p class="wptr_mbtm-0">No Rating</p>';
                            }
                            ?>
                        </div>
                        <div class="wptr_enrolled"><?php echo $total_users; ?> student(s) enrolled</div>
                    </div>

                    <div class="wptr_share-section">
                        <h6 class="wptr_h6_6">Share</h6>
                        <ul class="">
                            <li class="">
                                <!-- Facebook -->
                                <a onclick='window.open("http://www.facebook.com/sharer.php?u=<?php echo get_the_permalink(); ?>", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=300,width=600,height=400");' href="javascript:void(0)" class="wptr_icon-width" target="_blank">
                                    <img src="<?php echo RDTR_TRAINING_PLUGIN_URL ?>public/img/facebook.svg" alt="facebook-icon">
                                </a>

                            </li>
                            <li class="">
                                <a onclick='window.open("https://twitter.com/share?url=<?php echo get_the_permalink(); ?>&amp;hashtags=<?php echo get_the_title() ?>", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=300,width=600,height=400");' href="javascript:void(0)" class="wptr_icon-width" target="_blank">
                                    <img src="<?php echo RDTR_TRAINING_PLUGIN_URL ?>public/img/twitter.svg" alt="facebook-icon">
                                </a>

                            </li>
                            <li class="">
                                <a onclick='window.open("https://plus.google.com/share?url=<?php echo get_the_permalink(); ?>", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=300,width=600,height=400");' href="javascript:void(0)" class="wptr_icon-width rdtr-google-icon" target="_blank">
                                    <img src="<?php echo RDTR_TRAINING_PLUGIN_URL ?>public/img/google-plus.svg" alt="facebook-icon">
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php
                    $post_author_id = get_post_meta(get_the_ID(), "dd_course_author_box_post_type", true);
                    ?>
                    <div class="rdtr-published-autoher-price-sec">
                        <div class="rdtr-published-outer-sec">
                            <div class="rdtr-published-date">
                                <div class="posted-con">Posted On: &nbsp;</div>
                                <div class="rdtr-publisher-name">
                                    <span><?php echo get_the_date(); ?></span>
                                </div>
                            </div>
                            <div class="rdtr-published-sec">
                                <div class="rdtr-publisher-img auther-image">        
                                    <!--img src='http://192.168.1.38/wp/5/wp-content/plugins/training/public/img/dummy-auther.jpg' class='auther-image-img' /-->
                                    <img src='<?php echo esc_url(get_avatar_url($post_author_id)); ?>' class='auther-image-img' />
                                </div>
                                <div class="rdtr-publisher-name">
                                    <div class="autother-name">
                                        <?php echo ucfirst(get_the_author_meta("display_name", $post_author_id)); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wptr_right-section">         

                        <?php
                        if (count($module_ids) > 0) {

                            if (has_action('training_buynow_btn')) { // checking for action hook definition
                                do_action("training_buynow_btn", get_the_ID());
                            } else {
                                //activate on py adon
                                if (has_action("training_start_now")) {
                                    do_action("training_start_now", get_the_ID());
                                } else {

                                    if ($user_ID > 0) {

                                        $is_enrolled = $wpdb->get_row(
                                                $wpdb->prepare(
                                                        "SELECT id from " . $wpdb->prefix . "rd_user_course_enroll WHERE course_post_id = %d AND user_id = %d", get_the_ID(), $user_ID
                                                )
                                        );

                                        if (!empty($is_enrolled)) {

                                            $course_status_detail = $wpdb->get_row(
                                            $wpdb->prepare("SELECT exercise_status from " . $wpdb->prefix. "rd_user_course_progress WHERE user_id = %d AND course_post_id = %d AND exercise_status=%d", $user_ID, get_the_ID() ,'1'));

                                                $startcourse = 'Start Course';
                                                if (!empty($course_status_detail)) {
                                                       $course_status = $course_status_detail->exercise_status;
                                                        if ($course_status == 1) {
                                                             $startcourse = 'Resume Course';
                                                        }
                                                        else
                                                        {
                                                             $startcourse = 'Start Course';
                                                        }
                                                    }
                                            ?>
                                            <h3 class="wptr_h3_3 wptr_course-price">
                                                <a href="javascript:void(0)" id="training_course_enrol_now" class="wptr_course-btn"><?php echo $startcourse; ?> </a>
                                                <form id="frmenrolnow" name="frmenrolnow" method="post" action="javascript:void(0)">
                                                    <input type="hidden" value="<?php echo get_the_ID() ?>" name="enrol_course_id" id="enrol_course_id"/>
                                                    <input type="hidden" value="<?php echo $user_ID; ?>" name="logged_in_user_id" id="logged_in_user_id"/>
                                                </form>
                                            </h3>
                                            <?php
                                        } else {
                                            ?>
                                            <h3 class="wptr_h3_3 wptr_course-price">
                                                <a href="javascript:void(0)" id="training_course_enrol_now" class="wptr_course-btn">Enroll</a>
                                                <form id="frmenrolnow" name="frmenrolnow" method="post" action="javascript:void(0)">
                                                    <input type="hidden" value="<?php echo get_the_ID() ?>" name="enrol_course_id" id="enrol_course_id"/>
                                                    <input type="hidden" value="<?php echo $user_ID; ?>" name="logged_in_user_id" id="logged_in_user_id"/>
                                                </form>
                                            </h3>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <h3 class="wptr_h3_3 wptr_course-price">
                                            <a href="javascript:void(0)" id="training-course-login" class="wptr_course-btn">Enroll </a>
                                        </h3>
                                        <?php
                                    }
                                }
                            }
                        }
                        ?>

                    </div>

                </div>

                <div class="wptr-clear"></div>

            </section>


            <section class="wptr_course-detail">

                <div class="wptr_description">
                    <h3 class="wptr_h3_3">Description</h3>

                    <div class="lfet_title">

                        <p>
                            <?php
                            $post_data = get_post(get_the_ID());
                            echo do_shortcode(html_entity_decode($post_data->post_content));
                            ?>
                        </p>

                    </div>

                </div>		

                <div class="course_features">
                    <?php
                    $course_features = get_post_meta(get_the_ID(), "course_features_list", true);
                    if (!empty($course_features)) {
                        $course_features = json_decode($course_features);

                        if (!empty($course_features)) {
                            $featuresCount = 0;
                            if (is_array($course_features)) {
                                foreach ($course_features as $feature) {
                                    if (empty($feature)) {
                                        continue;
                                    }
                                    $featuresCount++;
                                }
                                if ($featuresCount > 0) {
                                    ?>
                                    <h3 class="course_features_heading wptr_h3_3">Course Features</h3>
                                    <ul class="course_features_list">
                                        <?php
                                        foreach ($course_features as $feature) {
                                            ?>
                                            <li class="">
                                                <i class="material-icons wptr_dp48">check</i>
                                                <h5 class="course_features_sub_h"><?php echo $feature; ?></h5>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </div>

                <div class="wptr_curriculum-section" id="curriculum-accordions">
                    <div class="curriculum_course_sec">
                        <div class="wptr_row-section curriculum_topbar">
                            <h3 class="wptr_h3_3">Curriculum For This Course</h3>
                            <div class="wptr_course-duration right_con">
                                <div class="right_con_one wptr_h5_5 wptr_mbtm-0">
                                    <?php echo count($module_ids) ?> Module(s)
                                </div>
                                <?php
                                if (count($module_ids) > 0) {

                                    $total_exercise_hours = $wpdb->get_var(
                                            "SELECT SUM( meta_value ) as total_hours FROM $wpdb->postmeta WHERE post_id IN ( SELECT post_id FROM $wpdb->postmeta WHERE meta_key =  'dd_chapter_id_box_post_type' AND meta_value IN ( SELECT post_id FROM $wpdb->postmeta WHERE meta_key =  'dd_module_id_box_post_type' AND meta_value IN ( " . $ids_string . " ) ) ) AND meta_key =  'txt_exercise_complete_hour'"
                                    );
                                    ?>
                                    <div class="wptr_curriculum-header-length right_con_two wptr_h5_5 wptr_mbtm-0">
                                        <?php echo $total_exercise_hours ?> hour(s)
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <?php
                        if (count($module_ids) > 0) {
                            ?>
                            <ul class="curriculum_accordion w-100">
                                <?php
                                foreach ($module_ids as $m_id) {
                                    $module_name = get_post($m_id);

                                    $total_lec = 0;
                                    $total_hrs = 0;
                                    $chapter_details = $wpdb->get_results(
                                            "SELECT post_id from $wpdb->postmeta WHERE meta_key = 'dd_module_id_box_post_type' AND meta_value = $m_id", ARRAY_A
                                    );
                                    $chapter_ids_array = $chapters_array = array_column($chapter_details, "post_id");

                                    if (!empty($chapter_ids_array)) {
                                        $chapter_ids_array = implode(",", $chapter_ids_array);
                                        $exercise_details = $wpdb->get_results(
                                                "SELECT post_id from $wpdb->postmeta WHERE meta_key = 'dd_chapter_id_box_post_type' AND meta_value IN ($chapter_ids_array)", ARRAY_A
                                        );
                                        $exercise_ids_array = array_column($exercise_details, "post_id");
                                        $total_lec = count($exercise_details);

                                        if (!empty($exercise_ids_array)) {
                                            $exercise_ids_array = implode(",", $exercise_ids_array);
                                            $total_hours = $wpdb->get_var(
                                                    "SELECT SUM(meta_value) from $wpdb->postmeta WHERE post_id IN ($exercise_ids_array) AND meta_key = 'txt_exercise_complete_hour'"
                                            );
                                            $total_hrs = $total_hours;
                                        }
                                    }
                                    ?>
                                    <li class=" curriculum_accordion_list">
                                        <a class="wpl_module_head d-block" data-status="0" data-value="class_<?php echo $m_id ?>_open" href="javascript:void(0);">
                                            <div class="wptr_row-section c_mx-0 align_v_center">
                                                <?php
                                                if (count($chapters_array) > 0) {
                                                    ?>
                                                    <div class="wptr_plus curriculum_accordion_icon"><i class="wptr_text-z-black material-icons wptr_dp48 wptr_middle-algn">add</i></div>
                                                    <div class="wptr_minus wptr_display-none curriculum_accordion_icon"><i class="material-icons wptr_dp48 wptr_middle-algn">remove</i></div>
                                                    <?php
                                                }
                                                ?>
                                                <div class="wptr_text-z-black wptr_h5_5 wptr_mbtm-0"><?php echo $module_name->post_title ?></div>
                                                <div class="right_inner_con">
                                                    <div class="wptr_text-z-black text_onne wptr_h5_5 wptr_mbtm-0">
                                                        <?php echo count($chapters_array); ?> Chapter(s)
                                                    </div>
                                                    <div class="wptr_text-z-black text_two wptr_h5_5 wptr_mbtm-0">
                                                        <?php echo $total_hrs; ?> hour(s)
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        <?php
                                        if (count($chapters_array) > 0) {
                                            ?>
                                            <ul class="w-100 inner_content_list differ_l" id="class_<?php echo $m_id ?>_open" style="display:none">

                                                <?php
                                                foreach ($chapters_array as $chapter) {
                                                    $chapter_detail = get_post($chapter);
                                                    $ch_exercise_details = $wpdb->get_results(
                                                            "SELECT post_id from $wpdb->postmeta WHERE meta_key = 'dd_chapter_id_box_post_type' AND meta_value = $chapter", ARRAY_A
                                                    );
                                                    $total_exe = count($ch_exercise_details);

                                                    if (!empty($ch_exercise_details)) {
                                                        $ch_exercise_details = array_column($ch_exercise_details, "post_id");
                                                        $exercise_ids_array = implode(",", $ch_exercise_details);
                                                        $total_hours = $wpdb->get_var(
                                                                "SELECT SUM(meta_value) from $wpdb->postmeta WHERE post_id IN ($exercise_ids_array) AND meta_key = 'txt_exercise_complete_hour'"
                                                        );
                                                        $total_hrs = $total_hours;
                                                    }
                                                    ?>
                                                    <li class="accord_innercon chapter_list">
                                                        <div class="accord_innercon_title_bar d-flex lfet_title wptr_h5_5 wptr_mbtm-0">
                                                            <div class="wptr_h5_5 wptr_mbtm-0"><?php echo $chapter_detail->post_title ?></div>
                                                            <div class="right_titles d-flex wptr_h5_5 wptr_mbtm-0">
                                                                <div class="wptr_h5_5 wptr_mbtm-0"><?php echo $total_hrs ?> hour(s)</div>
                                                                <a href="javascript:void(0)" class="total_exercise wptr_h5_5 wptr_mbtm-0">
                                                                    <div class="wptr_preview-text wptr_h5_5 wptr_mbtm-0"><?php echo $total_exe ?> Exercise(s)</div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <p class="accord_con_main">
                                                            <?php echo substr($chapter_detail->post_content, 0, 100); ?>
                                                        </p>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                        }
                                        ?>

                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>	
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <?php
                if (empty($user_login_status)) {
                    ?>
                    <div id="login-dialog" title="Login Window" class="">
                        <p class="validateTips">All form fields are required.</p>
                        <button type="button" class="ui-dialog-titlebar-close custome_btn"></button>
                        <h2 class="ui_label_login wptr_h2_1 text-center wptr_font-wt-bold">Training Login</h2>
                        <form id="frmtraininglogin" name="frmtraininglogin" action="javascript:void(0)" method="post" class="ui_login_form">
                            <div class="ui_user_name full-witdh">
                                <label for="training_user_email" class="ui_user_label">User Name</label>
                                <div class="pos_relative">
                                    <input type="text" name="training_user_email" id="training_user_email" required="" value="" class="text ui-widget-content ui-corner-all ui_user_input effect-2" placeholder="e.g: admin@gmail.com" autocomplete="off" onfocus="this.removeAttribute('readonly');">
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="ui_user_passs full-witdh">
                                <label for="training_user_pwd" class="ui_user_label">Password</label>
                                <div class="pos_relative">
                                    <input style="display:none" type="password" name="training_user_pwd"/>
                                    <input type="password" placeholder="e.g:********" name="training_user_pwd" id="training_user_pwd" required value="" class="text ui-widget-content ui-corner-all ui_user_input effect-2" autocomplete="off" onfocus="this.removeAttribute('readonly');">
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="ui_user_sub full-witdh text-center">
                                <button type='submit' class="ui_modal_submit">Login to your account</button>
                            </div>
                            <p class="already_account full-witdh text-center">Forgot password?<a href="<?php echo wp_lostpassword_url(); ?>"> Click here.</a></p>
                            <p class="already_account full-witdh text-center">Don't have an account?<a href="javascript:void(0)" id="rd-training-user-signup"> Create Now.</a></p>
                        </form>
                    </div>
                    <!-- Signup Window -->
                    <div id="signup-dialog" title="Sign Up Window" class="">
                        <p class="validateTips">All form fields are required.</p>
                        <button type="button" class="ui-dialog-titlebar-close custome_btn"></button>
                        <h2 class="ui_label_login wptr_h2_1 text-center wptr_font-wt-bold">Personal Information</h2>
                        <form id="frmtrainingsignup" name="frmtrainingsignup" action="javascript:void(0)" method="post" class="ui_signup_form">
                            <div class="ui_user_sname half_width">
                                <label for="training_signup_name" class="ui_user_label">User NAME</label>
                                <div class="pos_relative">
                                    <input type="text" name="training_signup_name" id="training_signup_name" required="" value="" class="text ui-widget-content ui-corner-all ui_user_input effect-2" placeholder="e.g: rudra_test" autocomplete="off" onfocus="this.removeAttribute('readonly');">
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="ui_user_semail half_width">
                                <label for="training_signup_user_email" class="ui_user_label">Email</label>
                                <div class="pos_relative">
                                    <input type="email" name="training_signup_user_email" id="training_signup_user_email" required="" value="" class="text ui-widget-content ui-corner-all ui_user_input effect-2" placeholder="e.g: admin@gmail.com" autocomplete="off" onfocus="this.removeAttribute('readonly');">
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="ui_user_spass full-witdh">
                                <label for="training_signup_user_pwd" class="ui_user_label">Password</label>
                                <div class="pos_relative">
                                    <input style="display:none" type="password" name="training_signup_user_pwd"/>
                                    <input type="password" placeholder="e.g:********" name="training_signup_user_pwd" id="training_signup_user_pwd" required value="" class="text ui-widget-content ui-corner-all ui_user_input effect-2" autocomplete="off" onfocus="this.removeAttribute('readonly');">
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="ui_user_con_sconpass full-witdh">
                                <label for="training_signup_user_conf_pwd" class="ui_user_label">Confirm password</label>
                                <div class="pos_relative">
                                    <input type="password" placeholder="e.g:********" name="training_signup_user_conf_pwd" id="training_signup_user_conf_pwd" required value="" class="text ui-widget-content ui-corner-all ui_user_input effect-2" autocomplete="off" onfocus="this.removeAttribute('readonly');">
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="ui_user_sub full-witdh text-center">
                                <button type='submit' class="ui_modal_submit">Submit</button>
                            </div>
                            <p class="already_account full-witdh text-center">Already have an account? <a href="javascript:void(0)" id="rd-training-user-login">LogIn</a></p>
                        </form>
                    </div>
                    <?php
                } else {
                    
                }
                ?>


                <?php
                $has_comments_enabled = get_option("training_disable_comments");
                if (!$has_comments_enabled) {
                    // If comments are open or we have at least one comment, load up the comment template.
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                }
                ?>

            </section>

        </div>

        <?php
    }
    ?>

</div>

<?php
get_footer();
