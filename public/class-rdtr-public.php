<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.rudrainnovative.com
 * @since      1.0.0
 *
 * @package    Rdtr
 * @subpackage Rdtr/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rdtr
 * @subpackage Rdtr/public
 * @author     Rudra Innovative Software Pvt Ltd <info@rudrainnovatives.com>
 */
class Rdtr_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    private $table_activator;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        require_once RDTR_TRAINING_DIR_PATH . 'includes/class-rdtr-activator.php';
        $table_activator = new Rdtr_Activator();
        $this->table_activator = $table_activator;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        global $post;
        global $wpdb;

        $training_page_ids = array(
            get_option("rdtr_training_allcourses_page"),
            get_option("rdtr_training_mycourse_page")
        );

        if (!empty($post)) {


            $page_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            // this is page link of wp

            if (in_array($post->ID, $training_page_ids) || preg_match("/" . RDTR_TRAINING_POST_TYPE . "/", $page_link)) {

                wp_enqueue_style('wp-jquery-ui-dialog');
                wp_enqueue_style("montserrat", plugin_dir_url(__FILE__) . 'css/montserrat-webfont.css', array(), $this->version, 'all');
                wp_enqueue_style("pt-sans", plugin_dir_url(__FILE__) . 'css/pt-sans.css', array(), $this->version, 'all');
                wp_enqueue_style("font-sans", '//fonts.googleapis.com/css?family=PT+Sans:400,700', array(), $this->version, 'all');
                wp_enqueue_style("material-icons", plugin_dir_url(__FILE__) . 'fonts/material-icons.css', array(), $this->version, 'all');
                wp_enqueue_style("material-designs", plugin_dir_url(__FILE__) . 'css/materialdesignicons.css', array(), $this->version, 'all');
                wp_enqueue_style("slick", plugin_dir_url(__FILE__) . 'slick/slick.css', array(), $this->version, 'all');
                wp_enqueue_style("slick-theme", plugin_dir_url(__FILE__) . 'slick/slick-theme.css', array(), $this->version, 'all');
                wp_enqueue_style("font-montserrat", '//fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800', array(), $this->version, 'all');
                wp_enqueue_style("style", plugin_dir_url(__FILE__) . 'css/style.css', array(), $this->version, 'all');
                wp_enqueue_style("style-star", RDTR_TRAINING_PLUGIN_URL . 'assets/css/star-rate.css', array(), $this->version, 'all');
                wp_enqueue_style("notification", plugin_dir_url(__FILE__) . 'css/jquery.notifyBar.css', array(), $this->version, 'all');
                wp_enqueue_style("plugin-style", RDTR_TRAINING_PLUGIN_URL . 'public/css/rdtr-public.css', array(), $this->version, 'all');
            }
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        global $post;
        global $wpdb;

        $check_page = isset($_REQUEST['survey_id']) ? intval($_REQUEST['survey_id']) : "";

        if (!empty($check_page)) {
            
        } else {

            $training_page_ids = array(
                get_option("rdtr_training_allcourses_page"),
                get_option("rdtr_training_mycourse_page")
            );

            if (!empty($post)) {

                $page_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                // this is page link of wp

                if (in_array($post->ID, $training_page_ids) || preg_match("/" . RDTR_TRAINING_POST_TYPE . "/", $page_link)) {

                    wp_enqueue_script('jquery');
                    wp_enqueue_script('jquery-ui-dialog');
                    wp_enqueue_script('jquery-effects-core');
                    wp_enqueue_script("validate", plugin_dir_url(__FILE__) . 'js/jquery.validate.min.js', array('jquery'), $this->version, false);
                    wp_enqueue_script('notification-js', plugin_dir_url(__FILE__) . 'js/jquery.notifyBar.js', array('jquery'), $this->version, true);
                    wp_enqueue_script("slick", plugin_dir_url(__FILE__) . 'js/slick.js', array('jquery'), $this->version, false);
                    wp_enqueue_script("main-js", plugin_dir_url(__FILE__) . 'js/main-js.js', array('jquery'), $this->version, false);
                    wp_enqueue_script("script", plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), $this->version, false);
                    wp_localize_script("script", "rdtr_training", array(
                        "ajaxurl" => admin_url('admin-ajax.php'),
                        "is_single" => is_single() == 1 ? 1 : 0,
                        "plpath" => RDTR_TRAINING_PLUGIN_URL,
                        "site_url" => site_url()
                    ));
                }
            }
        }
    }

    /* Template included */

    function training_page_template($page_template) {
        global $post;
        if (isset($post->post_type)) {
            if ($post->post_type == RDTR_TRAINING_POST_TYPE) {

                $page_template = plugin_dir_path(__FILE__) . '/templates/rdtr-course-desc-template.php';
            }
        }
        return $page_template;
    }

    /* =========================================================== */
    /* function to show all courses at front-end */
    /* =========================================================== */

    public function wpl_rd_front_end_training_courses() {

        $has_set_all_courses = get_option("rdtr_training_allcourses_page");
        $has_set_my_courses = get_option("rdtr_training_mycourse_page");

        if (!empty($has_set_all_courses) && !empty($has_set_my_courses)) {

            ob_start();
            include_once RDTR_TRAINING_DIR_PATH . '/public/templates/rdtr-training-all-courses.php';
            $template = ob_get_contents();
            ob_end_clean();
            return $template;
        } else {
            echo '<h3 style="color:red;">Training settings are not configured properly.</h3>';
        }
    }

    /*
     * function to get all courses
     */

    public function wpl_rd_get_all_courses($search_term = '', $offest = 0) {
        global $wpdb;
        $all_courses = array();
        $limit = RDTR_COURSE_LOAD_MORE;
        $start = $offest * RDTR_COURSE_LOAD_MORE;
        if (!empty($search_term)) {
            $search_term = strtolower($search_term);
            $all_courses = $wpdb->get_results(
                    "SELECT post . *  FROM " . $wpdb->posts . " post  WHERE LOWER(post.post_title) like '%$search_term%' AND post.post_type = 'training' AND post.post_status = 'publish' ORDER BY post.ID desc limit $start, $limit"
            );
        } else {

            $all_courses = $wpdb->get_results(
                    "SELECT * from " . $wpdb->posts . " WHERE post_type = 'training' AND post_status = 'publish' AND post_title != '' ORDER BY ID desc limit $start,$limit"
            );
        }

        return $all_courses;
    }

    /*
     * function to generate publich ajax handler, request to handle ajax of public
     */

    public function wpl_public_ajax_handler() {
        global $wpdb;
        $param = isset($_REQUEST['param']) ? sanitize_text_field($_REQUEST['param']) : "";
        if (!empty($param)) {
            if ($param == "wpl_course_search") {
                $search_term = isset($_REQUEST['txt_search_course']) ? sanitize_text_field($_REQUEST['txt_search_course']) : "";
                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-courses.php';
                $template = ob_get_contents();
                ob_end_clean();
                $this->json(1, "Course filtered", array("template" => $template));
            } elseif ($param == "wpl_load_more_course") {
                $offset = isset($_REQUEST['offset']) ? intval($_REQUEST['offset']) : 1;
                $search = isset($_REQUEST['srch']) ? sanitize_text_field($_REQUEST['srch']) : 0;
                $allcourses = $this->wpl_rd_get_all_courses($search, $offset);
                if (count($allcourses) > 0) {

                    ob_start();
                    include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-courses.php';
                    $template = ob_get_contents();
                    ob_end_clean();
                    $this->json(1, "Course found", array("template" => $template));
                } else {
                    $this->json(0, "Course not found");
                }
            } elseif ($param == "wpl_check_user_login") {

                $username = isset($_REQUEST['training_user_email']) ? sanitize_text_field($_REQUEST['training_user_email']) : "";
                $password = isset($_REQUEST['training_user_pwd']) ? sanitize_text_field($_REQUEST['training_user_pwd']) : "";

                $creds = array(
                    'user_login' => $username,
                    'user_password' => $password,
                    'remember' => true
                );

                $user = wp_signon($creds, false);

                if (is_wp_error($user)) {
                    $this->json(0, "Login failed: " . $user->get_error_message());
                } else {

                    $this->json(1, "Login success");
                }
            } elseif ($param == "wpl_user_registration") {

                $name = isset($_REQUEST['training_signup_name']) ? sanitize_text_field($_REQUEST['training_signup_name']) : "";
                $email = isset($_REQUEST['training_signup_user_email']) ? sanitize_text_field($_REQUEST['training_signup_user_email']) : "";
                $password = isset($_REQUEST['training_signup_user_pwd']) ? sanitize_text_field($_REQUEST['training_signup_user_pwd']) : "";

                if (email_exists($email) == false) {

                    $userdata = array(
                        'user_login' => $name,
                        'user_email' => $email,
                        "display_name" => $name,
                        "user_nicename" => $name,
                        'user_pass' => $password
                    );

                    $user_id = wp_insert_user($userdata);

                    if (!is_wp_error($user_id)) {

                        //sending mail via mail template to user
                        if (has_filter("rdtr_email_templates_mail")) {
                            $userdata['password'] = $password;
                            apply_filters("rdtr_email_templates_mail", $userdata, "register");
                        }

                        $creds = array(
                            'user_login' => $name,
                            'user_password' => $password,
                            'remember' => true
                        );

                        $user = wp_signon($creds, false);

                        $this->json(1, "Account registered successfully");
                    } else {
                        $this->json(0, "User already exists");
                    }
                } else {
                    $this->json(0, "User already exists with given email");
                }
            } elseif ($param == "wpl_user_course_enrol") {

                $enrol_course_id = isset($_REQUEST['enrol_course_id']) ? intval($_REQUEST['enrol_course_id']) : "";
                $logged_in_user_id = isset($_REQUEST['logged_in_user_id']) ? intval($_REQUEST['logged_in_user_id']) : "";

                $saved_my_course_page = get_option("rdtr_training_mycourse_page");
                $redirect_url = '';
                if ($saved_my_course_page > 0) {
                    $redirect_url = get_permalink($saved_my_course_page);
                }

                $get_first_module = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * from $wpdb->postmeta WHERE meta_value = %d AND meta_key = %s limit 1", $enrol_course_id, 'dd_course_box_post_type'
                        )
                );

                if (!$this->wpl_check_course_enrollment($enrol_course_id, $logged_in_user_id)) {

                    $get_all_exercise = $this->wpl_rd_get_total_exercises_by_course($enrol_course_id);

                    if (count($get_all_exercise) > 0) {

                        foreach ($get_all_exercise as $inx => $exercise) {

                            $wpdb->insert($this->table_activator->wpl_rd_course_progress_tbl(), array(
                                "course_post_id" => $enrol_course_id,
                                "user_id" => $logged_in_user_id,
                                "exercise_id" => $exercise,
                                "exercise_status" => 0
                            ));
                        }
                    }

                    $wpdb->insert($this->table_activator->wpl_rd_user_enroll_tbl(), array(
                        "course_post_id" => $enrol_course_id,
                        "user_id" => $logged_in_user_id
                    ));

                    if ($wpdb->insert_id > 0) {

                        //sending mail via mail template to user
                        if (has_filter("rdtr_email_templates_mail")) {
                            apply_filters("rdtr_email_templates_mail", array("user_id" => $logged_in_user_id, "course_id" => $enrol_course_id), "course_enroll");
                        }

                        $this->json(1, "Course enrolled", array("enrol_status" => 1, "my_course" => $redirect_url . "?rpage=course_detail&course_id=" . $enrol_course_id . "&st=inprogress#type=module#modid=" . $get_first_module->post_id));
                    } else {
                        $this->json(0, "Failed to enrol", array("enrol_status" => 0, "my_course" => $redirect_url . "?rpage=course_detail&course_id=" . $enrol_course_id . "&st=inprogress#type=module#modid=" . $get_first_module->post_id));
                    }
                } else {
                    $this->json(0, "Course already enrolled", array("enrol_status" => 1, "my_course" => $redirect_url . "?rpage=course_detail&course_id=" . $enrol_course_id . "&st=inprogress#type=module#modid=" . $get_first_module->post_id));
                }
            } elseif ($param == "wpl_training_course_syllabus") {
                // get request parameters
                $course_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : "";
                $module_order = isset($_REQUEST['module_order']) ? intval($_REQUEST['module_order']) : "";
                $chapter_order = isset($_REQUEST['chapter_order']) ? intval($_REQUEST['chapter_order']) : "";
                $chapter_id = isset($_REQUEST['chapter_id']) ? intval($_REQUEST['chapter_id']) : "";
                $slick_order = isset($_REQUEST['slick']) ? intval($_REQUEST['slick']) : "";
                $url = isset($_REQUEST['url']) ? esc_attr(trim($_REQUEST['url'])) : "";

$condition = is_countable($chapter_id) ? (count($chapter_id) > 0) : ($chapter_id >0);       
if ($condition) {

                    $ch_id = $chapter_id;
                    $mod_count = $module_order;
                    $ch_count = $chapter_order;
                    $slick = $slick_order + 1;
                    $full_url = $url . "&course_id=" . $course_id;
                    //loading buffer
                    ob_start();
                    include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-tmpl-exercise-area.php';
                    $template = ob_get_contents();
                    ob_end_clean();

                    $this->json(1, "exercises found", array("template" => $template, "slick_order" => $slick));
                } else {

                    $this->json(0, "no exercise found");
                }
            } elseif ($param == "wpl_load_module_section") {
                
            } elseif ($param == "wpl_load_module_data_section") {

                $module_id = isset($_REQUEST['module_id']) ? intval($_REQUEST['module_id']) : "";
                $module_data = get_post($module_id, ARRAY_A);
                $dataType = "module";
                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-course-body-section.php';
                $template = ob_get_contents();
                ob_end_clean();

                $this->json(1, "module data", array("template" => $template));
            } elseif ($param == 'wpl_load_chapter_data_section') {

                $chapter_id = isset($_REQUEST['chapter_id']) ? intval($_REQUEST['chapter_id']) : "";
                $chapter_data = get_post($chapter_id, ARRAY_A);

                $dataType = "chapter";
                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-course-body-section.php';
                $template = ob_get_contents();
                ob_end_clean();

                $this->json(1, "chapter data", array("template" => $template));
            } elseif ($param == "wpl_load_exercise_data_section") {

                $exercise_id = isset($_REQUEST['exercise_id']) ? intval($_REQUEST['exercise_id']) : "";
                $chapter_id = isset($_REQUEST['chapter_id']) ? intval($_REQUEST['chapter_id']) : "";
                global $wpdb;
                global $user_ID;
                $item = $this->wpl_rd_get_exercise_sections_json($exercise_id);

                $has_seq_queries = $wpdb->get_var(
                        "SELECT count(id) from " . $this->table_activator->wpl_rd_course_exercise_sequence() . " WHERE exercise_id = " . $exercise_id . " AND ( sequence_number like '%reflection%' OR sequence_number like '%single%' OR sequence_number like '%mcq%' OR sequence_number like '%poll_type%' ) AND sequence_status = 1"
                );

                $completed_queries = $wpdb->get_var(
                        "SELECT count(id) from " . $this->table_activator->wpl_rd_user_exercise_progress() . " WHERE user_id = $user_ID AND exercise_id = " . $exercise_id . " AND ( seq_number like '%reflection%' OR seq_number like '%single%' OR seq_number like '%mcq%' OR seq_number like '%poll_type%' ) AND complete_status = 1"
                );

                $show_next = 0;

                if ($has_seq_queries == $completed_queries) {
                    $show_next = 1;
                }

                if (!empty($item)) {
                    $exercise_detail = get_post($exercise_id, ARRAY_A);
                    ob_start();
                    include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-exercise-sections.php';
                    $template = ob_get_contents();
                    ob_end_clean();
                } else {
                    $template = "";
                }
                $this->json(1, "exercise sections", array("template" => $template));
            } elseif ($param == "wpl_save_exercise_data_section") {
                global $user_ID;
                global $wpdb;
                $section_name = isset($_REQUEST['section_name']) ? esc_attr(trim($_REQUEST['section_name'])) : "";
                $section_type = isset($_REQUEST['section_type']) ? esc_attr(trim($_REQUEST['section_type'])) : "";
                $exercise_id = isset($_REQUEST['exid']) ? intval($_REQUEST['exid']) : "";
                $user_id = $user_ID;
                $section_detail = $this->wpl_rd_get_sequence_id_by_seq_no($section_name);

                if (!empty($section_detail)) {

                    if ($section_type == "reflection") {

                        $answer = isset($_REQUEST['reflection_answer']) && !empty($_REQUEST['reflection_answer']) ? sanitize_text_field($_REQUEST['reflection_answer']) : "no_value";

                        if ($answer == "no_value") {
                            $this->json(0, "Please provide answer");
                        }
                    } elseif ($section_type == "mcq") {

                        $answer = isset($_REQUEST['rdb_mcq']) ? array_map('sanitize_text_field', wp_unslash($_REQUEST['rdb_mcq'])) : array();

                        if (empty($answer)) {
                            $this->json(0, "Please select an option");
                        } else {
                            $answer = implode(",", $answer);
                        }
                    } elseif ($section_type == "single") {

                        $answer = isset($_REQUEST['rdb_single']) ? esc_attr(trim($_REQUEST['rdb_single'])) : 'no_value';
                        if ($answer == "no_value") {
                            $this->json(0, "Please select an option");
                        }
                    } elseif ($section_type == "poll") {

                        $answer = isset($_REQUEST['rdb_poll']) ? esc_attr(trim($_REQUEST['rdb_poll'])) : "no_value";

                        if ($answer == "no_value") {
                            $this->json(0, "Please select an option");
                        }
                    }

                    $wpdb->insert($this->table_activator->wpl_rd_user_exercise_progress(), array(
                        "user_id" => $user_id,
                        "exe_type" => $section_type,
                        "exercise_id" => $exercise_id,
                        "seq_number" => $section_name,
                        "exe_answer" => $answer,
                        "complete_status" => 1
                    ));

                    $item = $this->wpl_rd_get_exercise_sections_json($exercise_id);

                    $has_seq_queries = $wpdb->get_var(
                            "SELECT count(id) from " . $this->table_activator->wpl_rd_course_exercise_sequence() . " WHERE exercise_id = " . $exercise_id . " AND ( sequence_number like '%reflection%' OR sequence_number like '%single%' OR sequence_number like '%mcq%' OR sequence_number like '%poll_type%' ) AND sequence_status = 1"
                    );

                    $completed_queries = $wpdb->get_var(
                            "SELECT count(id) from " . $this->table_activator->wpl_rd_user_exercise_progress() . " WHERE user_id = $user_ID AND exercise_id = " . $exercise_id . " AND ( seq_number like '%reflection%' OR seq_number like '%single%' OR seq_number like '%mcq%' OR seq_number like '%poll_type%' ) AND complete_status = 1"
                    );

                    $show_next = 0;

                    if ($has_seq_queries == $completed_queries) {
                        $show_next = 1;
                    }

                    if (!empty($item)) {
                        $exercise_detail = get_post($exercise_id, ARRAY_A);
                        ob_start();
                        include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-exercise-sections.php';
                        $template = ob_get_contents();
                        ob_end_clean();
                    } else {
                        $template = "";
                    }
                    $this->json(1, "exercise marked", array("template" => $template));
                } else {

                    $this->json(1, "exercise not found");
                }
            } elseif ($param == "wpl_load_chapter_upload_section") {

                $chapter_id = isset($_REQUEST['chapter_id']) ? intval($_REQUEST['chapter_id']) : "";
                $chapter_data = get_post($chapter_id, ARRAY_A);
                global $user_ID;

                $dataType = "chapter_upload";
                $has_any_uploaded_files = $wpdb->get_results(
                        $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->wpl_rd_user_uploaded_files_tbl() . " WHERE user_id = %d AND chapter_id = %d", $user_ID, $chapter_id
                        )
                );
                ob_start();
                $chapter_details = get_post($chapter_id);
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-course-body-section.php';
                $template = ob_get_contents();
                ob_end_clean();

                $this->json(1, "chapter data", array("template" => $template));
            } elseif ($param == "wpl_upd_chapter_files") {

                $chapter_id = isset($_REQUEST['ch_id']) ? intval($_REQUEST['ch_id']) : "";
                global $user_ID;
                $course_id = '';

                $files = isset($_REQUEST['ch_file_uploads']) ? $_REQUEST['ch_file_uploads'] : "";

                if (count($files) > 0) {
                    $valid_images = array("png", "gif", "jpeg", "jpg");
                    $valid_audio = array("mp3");
                    $valid_video = array("mp4", "webm");
                    foreach ($files as $inx => $stx) {
                        $extension = pathinfo($stx, PATHINFO_EXTENSION);
                        $file_type = '';

                        if (in_array($extension, $valid_images)) {
                            $file_type = "image";
                        } elseif (in_array($extension, $valid_audio)) {
                            $file_type = "audio";
                        } elseif (in_array($extension, $valid_video)) {
                            $file_type = "video";
                        }

                        $wpdb->insert($this->table_activator->wpl_rd_user_uploaded_files_tbl(), array(
                            "user_id" => $user_ID,
                            "chapter_id" => $chapter_id,
                            "type" => $file_type,
                            "file" => $stx,
                            "status" => 1
                        ));
                    }

                    $chapter_data = get_post($chapter_id, ARRAY_A);
                    global $user_ID;

                    //sending mail via mail template to user
                    if (has_filter("rdtr_email_templates_mail")) {
                        $userdata['files'] = $files;
                        $userdata['user_id'] = $user_ID;
                        $userdata['chapter_id'] = $chapter_id;
                        apply_filters("rdtr_email_templates_mail", $userdata, "chapter_assignment");
                    }

                    $dataType = "chapter_upload";
                    $has_any_uploaded_files = $wpdb->get_results(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->wpl_rd_user_uploaded_files_tbl() . " WHERE user_id = %d AND chapter_id = %d", $user_ID, $chapter_id
                            )
                    );
                    ob_start();
                    $chapter_details = get_post($chapter_id);
                    include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-course-body-section.php';
                    $template = ob_get_contents();
                    ob_end_clean();

                    $this->json(1, "Files submitted", array("template" => $template));
                } else {
                    $this->json(0, "No files found to upload");
                }
            } elseif ($param == "wpl_save_user_exercise") {

                $exercise_id = isset($_REQUEST['exercise_id']) ? intval($_REQUEST['exercise_id']) : 0;
                global $user_ID;
                $exercise_data = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE exercise_id = %d", $exercise_id
                        )
                );

                if (!empty($exercise_data)) {

                    $wpdb->update($this->table_activator->wpl_rd_course_progress_tbl(), array(
                        "exercise_status" => 1
                            ), array(
                        "exercise_id" => $exercise_id,
                        "user_id" => $user_ID
                    ));

                    // check for all exercises done at end
                    $find_course_id = $wpdb->get_var(
                            $wpdb->prepare(
                                    "SELECT course_post_id from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE exercise_id = %d", $exercise_id
                            )
                    );

                    $find_unfinished_ex = $wpdb->get_var(
                            $wpdb->prepare(
                                    "SELECT count(id) from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE course_post_id = %d AND user_id = %d AND exercise_status = %d", $course_id, $user_id, 0
                            )
                    );

                    if ($find_unfinished_ex > 0) {
                        //some exercises has to complete
                    }

                    $this->json(1, "Exercise marked");
                } else {

                    $this->json(0, "Invalid Exercise");
                }
            } elseif ($param == "wpl_load_check_exercise_access") {

                $exercise_id = isset($_REQUEST['exercise_id']) ? intval($_REQUEST['exercise_id']) : "";

                $exe_status = $this->wpl_rd_get_user_exercise_marked_status($exercise_id);

                if ($exe_status->exercise_status == 1) {
                    $this->json(1, "exercise marked");
                } else {
                    $this->json(0, "Please complete previous exercises");
                }
            } elseif ($param == "wpl_rd_check_complete_course") {

                $module_id = isset($_REQUEST['module_id']) ? intval($_REQUEST['module_id']) : "";
                $dataType = "course_completed";
                global $user_ID;

                $course_id = get_post_meta($module_id, "dd_course_box_post_type", true);

                if ($this->wpl_rd_get_course_status($course_id)) {
                    // course already in complete status
                    $message = "Course has been completed already";
                } else {

                    $wpdb->update($this->table_activator->wpl_rd_user_enroll_tbl(), array(
                        "course_status" => 1
                            ), array(
                        "course_post_id" => $course_id,
                        "user_id" => $user_ID
                    ));

                    //sending mail via mail template to user
                    if (has_filter("rdtr_email_templates_mail")) {
                        $userdata['password'] = $password;
                        $course_data['module_id'] = $module_id;
                        apply_filters("rdtr_email_templates_mail", $course_data, "course_completed");
                    }
                    $message = "Course has been completed successfully";
                }

                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-course-body-section.php';
                $template = ob_get_contents();
                ob_end_clean();

                $this->json(1, "course completed layout", array("template" => $template));
            } elseif ($param == "wpl_redirect_course_not_found") {
                $dataType = "";

                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-course-body-section.php';
                $template = ob_get_contents();
                ob_end_clean();

                $this->json(1, "course completed layout", array("template" => $template));
            } elseif ($param == "wpl_rd_mark_exercise_complete") {

                $exercise_id = isset($_REQUEST['exercise_id']) ? intval($_REQUEST['exercise_id']) : "";

                global $wpdb;
                global $user_ID;

                if ($exercise_id > 0) {

                    $exericse_row = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE exercise_id = %d AND user_id = %d", $exercise_id, $user_ID
                            ), ARRAY_A
                    );

                    if (!empty($exericse_row)) {

                        $wpdb->update($this->table_activator->wpl_rd_course_progress_tbl(), array(
                            "exercise_status" => 1
                                ), array(
                            "id" => $exericse_row['id']
                        ));

                        $c_id = $exericse_row['course_post_id'];

                        $get_all_exercises_by_course = $wpdb->get_results(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE course_post_id = %d AND user_id = %d", $c_id, $user_ID
                                )
                        );

                        $get_all_marked_exercises = $wpdb->get_results(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE course_post_id = %d AND user_id = %d AND exercise_status = %d", $c_id, $user_ID, 1
                                )
                        );

                        if (count($get_all_exercises_by_course) == count($get_all_marked_exercises)) {

                            $wpdb->update($this->table_activator->wpl_rd_user_enroll_tbl(), array(
                                "course_status" => 1
                                    ), array(
                                "course_post_id" => $c_id,
                                "user_id" => $user_ID
                            ));
                        }

                        ob_start();
                        include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-course-detail-left-sidebar.php';
                        $template = ob_get_contents();
                        ob_end_clean();

                        $this->json(1, "Exercise completed successfully", array("template" => $template));
                    } else {
                        $this->json(0, "Invalid Exercise to complete");
                    }
                } else {
                    $this->json(0, "Invalid Exercise ID");
                }
            }
        }

        wp_die();
    }

    public function rdtr_wp_is_mobile() {
        static $is_mobile;

        if (isset($is_mobile))
            return $is_mobile;

        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $is_mobile = false;
        } elseif (
                strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false) {
            $is_mobile = true;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false) {
            $is_mobile = true;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
            $is_mobile = false;
        } else {
            $is_mobile = false;
        }

        return $is_mobile;
    }

    public function wpl_rd_get_exercise_sections_json($exercise_id) {
        $item = array();
        $exercise_sections_meta = get_post_meta($exercise_id, "rd_wpl_exercise_section", true);

        $get_saved_sections = str_replace("\\", "", $exercise_sections_meta);
        $exercise_json = $get_saved_sections ? json_decode($get_saved_sections, true) : '';

        if (!empty($exercise_json)) {

            $exercise_order = $exercise_json['order'];
            $exercise_sections = $exercise_json['section'];

            if (!empty($exercise_sections)) {

                foreach ($exercise_order as $inx => $order) {
                    $sec = $order;
                    $sec = explode("_", $sec);
                    array_pop($sec);
                    $sec = implode("_", $sec);

                    if (strpos($sec, '_para_') !== false) {
                        $item["sections"][] = array(
                            "name" => $order,
                            "value" => $exercise_sections['paragraph'][$order]['value'],
                            "show" => "paragraph"
                        );
                    } elseif (strpos($sec, '_file_') !== false) {

                        if ($exercise_sections['file'][$order]['type'] == "youtube") {
                            $show = "youtube";
                        } else {
                            $show = "gallery";
                        }

                        $item["sections"][] = array(
                            "name" => $order,
                            "value" => $exercise_sections['file'][$order]['value'],
                            "type" => $exercise_sections['file'][$order]['type'],
                            "show" => $show
                        );
                    } elseif (strpos($sec, '_mcq_') !== false) {

                        $item["sections"][] = array(
                            "name" => $order,
                            "question" => $exercise_sections['mcq'][$order]['question'],
                            "options" => $exercise_sections['mcq'][$order]['options'],
                            "show" => "mcq",
                            "answer" => $exercise_sections['mcq'][$order]['answer'],
                            "option_index" => $exercise_sections['mcq'][$order]['option_index']
                        );
                    } elseif (strpos($sec, '_single_') !== false) {

                        $item["sections"][] = array(
                            "name" => $order,
                            "question" => $exercise_sections['single'][$order]['question'],
                            "options" => $exercise_sections['single'][$order]['options'],
                            "show" => "single",
                            "answer" => $exercise_sections['single'][$order]['answer'],
                            "option_index" => $exercise_sections['single'][$order]['option_index']
                        );
                    } elseif (strpos($sec, '_poll_') !== false) {

                        $item["sections"][] = array(
                            "name" => $order,
                            "question" => $exercise_sections['poll'][$order]['question'],
                            "options" => $exercise_sections['poll'][$order]['options'],
                            "show" => "poll"
                        );
                    } elseif (strpos($sec, '_reflection_') !== false) {

                        $item["sections"][] = array(
                            "name" => $order,
                            "value" => $exercise_sections['reflection'][$order]['value'],
                            "show" => "reflection"
                        );
                    }
                }
            }
        }

        return $item;
    }

    public function wpl_check_course_enrollment($course_id, $user_id) {
        global $wpdb;
        $enrol_find = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE course_post_id = %d AND user_id = %d", $course_id, $user_id
                )
        );
        if (!empty($enrol_find)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function wpl_rdtr_calculate_average_star_rating($id) {

        $comments = get_approved_comments($id);

        if ($comments) {
            $i = 0;
            $total = 0;
            foreach ($comments as $comment) {
                $rate = get_comment_meta($comment->comment_ID, 'rating', true);
                if (isset($rate) && '' !== $rate) {
                    $i++;
                    $total += $rate;
                }
            }

            if (0 === $i) {
                return false;
            } else {
                return round($total / $i, 1);
            }
        } else {
            return false;
        }
    }

    public function wpl_rdtr_has_total_exercise_sections_done($exercise_id) {
        global $wpdb;

        $total_exercises = $wpdb->get_var(
                "SELECT count(id) from " . $this->table_activator->wpl_rd_course_exercise_sequence() . " WHERE exercise_id = $exercise_id AND sequence_status = 1 AND (sequence_number LIKE '%mcq%' OR sequence_number LIKE '%single%' OR sequence_number LIKE '%poll%' OR sequence_number LIKE '%reflection%')"
        );

        $total_completed_exercises = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(uex.id) from " . $this->table_activator->wpl_rd_user_exercise_progress() . " uex  WHERE uex.exercise_id = %d", $exercise_id
                )
        );

        if ($total_completed_exercises == $total_exercises) {
            return 1;
        } else {
            return 0;
        }
    }

    /* =========================================================== */
    /* function to show star rating on frontend */
    /* =========================================================== */

    public function json($sts, $msg, $arr = array()) {
        $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
        print_r(json_encode($ar));
        die;
    }

    // load my course section on fron-end
    public function wpl_rd_front_end_training_my_course() {

        global $wpdb;
        global $user_ID;

        $is_page = isset($_REQUEST['rpage']) ? trim($_REQUEST['rpage']) : "";
        $course_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : 0;

        //checking for syllabus page if it is in query string navigate to syllabus for that course
        if ($is_page == "syllabus" && $course_id > 0) {

            $is_valid_course = get_post($course_id);

            if (!empty($is_valid_course)) {

                $module_ids = $wpdb->get_results(
                        $wpdb->prepare(
                                "SELECT pmeta.post_id from " . $wpdb->postmeta . " pmeta INNER JOIN $wpdb->posts post ON pmeta.post_id = post.ID WHERE post.post_status = %s and pmeta.meta_key = %s and pmeta.meta_value = %d", "publish", "dd_course_box_post_type", $course_id
                        )
                );

                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/rdtr-course-syllabus.php';
                $template = ob_get_contents();
                ob_end_clean();
                return $template;
                // here have to add syllabus page to use, need to make templates inside /template folder
            } else {

                // if suppose remove parameters and missued parameters

                $my_courses = $this->wpl_rd_training_my_courses($user_ID);
                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/rdtr-course-my-course.php';
                $template = ob_get_contents();
                ob_end_clean();
                return $template;
            }
        } elseif ($is_page == "course_detail" && $course_id > 0) {

            $is_valid_course = get_post($course_id);

            if (!empty($is_valid_course)) {

                // go to course detail page need to make templates inside /template folder
                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/rdtr-course-detail.php';
                $template = ob_get_contents();
                ob_end_clean();
                return $template;
            } else {

                // if suppose remove parameters and missued parameters
                $my_courses = $this->wpl_rd_training_my_courses($user_ID);
                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/rdtr-course-my-course.php';
                $template = ob_get_contents();
                ob_end_clean();
                return $template;
            }
        } else {

            // if page is course my course
            $my_courses = $this->wpl_rd_training_my_courses($user_ID);
            ob_start();
            include_once RDTR_TRAINING_DIR_PATH . 'public/templates/rdtr-course-my-course.php';
            $template = ob_get_contents();
            ob_end_clean();
            return $template;
        }
    }

    public function wpl_rd_training_my_courses($user_id) {

        global $wpdb;
        $my_courses = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT enrol.course_post_id from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " enrol INNER JOIN $wpdb->posts post ON post.ID = enrol.course_post_id WHERE enrol.user_id = %d AND post.post_status = %s ORDER BY enrol.id DESC", $user_id, 'publish'
                ), ARRAY_A
        );
        return $my_courses;
    }

    /* =========================================================== */
    /* function to get total exercise by course ID */
    /* =========================================================== */

    public function wpl_rd_get_total_exercises_by_course($course_id = '') {

        global $wpdb;

        $total_exercises = $wpdb->get_results(
                "SELECT post_id
                    FROM $wpdb->postmeta
                    WHERE meta_value
                    IN (

                    SELECT post_id
                    FROM $wpdb->postmeta
                    WHERE meta_value
                    IN (

                    SELECT post_id
                    FROM $wpdb->postmeta
                    WHERE meta_value =  '$course_id'
                    AND meta_key =  'dd_course_box_post_type'
                    )
                    AND meta_key =  'dd_module_id_box_post_type'
                    )
                    AND meta_key =  'dd_chapter_id_box_post_type'"
        );

        return $exercise_ids = array_column(json_decode(json_encode($total_exercises), true), "post_id");
    }

    /* =========================================================== */
    /* function to get course exercise progress by its ID */
    /* =========================================================== */

    public function wpl_rd_get_course_excercise_progress($exercise_id) {
        global $wpdb;
        $exercise_data = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE exercise_id = %d", $exercise_id
                )
        );
        return $exercise_data;
    }

    /* =========================================================== */
    /* function to get total completed exercise by course id */
    /* =========================================================== */

    public function wpl_rd_get_course_completed_excercise($course_id, $user_id) {
        global $wpdb;
        $total_exercises = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(id) from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE course_post_id = %d AND exercise_status = %d AND user_id = %d", $course_id, 1, $user_id
                )
        );
        return $total_exercises;
    }

    /* =========================================================== */
    /* function to get course progress by course id */
    /* =========================================================== */

    public function wpl_rd_get_course_progres_by_course_id($course_id, $user_id) {
        global $wpdb;
        global $user_ID;

        $total_course_exercise = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(id) from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE course_post_id = %d AND user_id = %d", $course_id, $user_ID
                )
        );

        $ratio = 0;
        if ($total_course_exercise > 0) {
            $completed_exercise = $this->wpl_rd_get_course_completed_excercise($course_id, $user_id);

            $completed_exercise_ration = $completed_exercise / $total_course_exercise;

            $ratio = round($completed_exercise_ration, 2);
        }

        if ($ratio > 1) {
            $ratio = 1;
        }

        return $ratio * 100;
    }

    /* =========================================================== */
    /* function to estimate course enrolled by users and completed by users */
    /* =========================================================== */

    public function wp_rd_get_course_enrolled_by_user($course_id) {

        global $wpdb;

        $total_enrolled_by = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(user_id) from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE  course_post_id = %d AND status = %d", $course_id, 1
                )
        );

        $total_completed_by = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(user_id) from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE  course_post_id = %d AND course_status = %d AND status = %d", $course_id, 1, 1
                )
        );

        return array("enrolled_by" => $total_enrolled_by, "completed_by" => $total_completed_by);
    }

    /* =========================================================== */
    /* function to get all users overall progress on any course */
    /* =========================================================== */

    public function wpl_rd_get_user_overall_course_progress($course_id) {

        global $wpdb;

        $user_data = $this->wp_rd_get_course_enrolled_by_user($course_id);
        $ratio = 0;
        if ($user_data['enrolled_by'] > 0) {
            if ($user_data['completed_by'] > 0) {
                $ratio = (round(($user_data['completed_by'] / $user_data['enrolled_by']), 2)) * 100;
            } else {
                $ratio = 0;
            }
        } else {
            $ratio = 0;
        }
        return $ratio;
    }

    /* =========================================================== */
    /* function to get all chapters by module ID */
    /* =========================================================== */

    public function wpl_rd_get_chapters_by_module($module_id) {

        global $wpdb;

        $chapter_ids = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT pmeta.post_id from " . $wpdb->postmeta . " pmeta INNER JOIN $wpdb->posts post ON pmeta.post_id = post.ID WHERE post.post_status = %s and pmeta.meta_key = %s and pmeta.meta_value = %d", "publish", "dd_module_id_box_post_type", $module_id
                )
        );

        return $chapter_ids;
    }

    /* =========================================================== */
    /* function to get all exercises by chapter ID */
    /* =========================================================== */

    public function wpl_rd_get_exercises_by_chapter($chapter_id) {

        global $wpdb;
        $exercise_ids = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT post_id from " . $wpdb->postmeta . " WHERE meta_key = %s and meta_value = %d", "dd_chapter_id_box_post_type", $chapter_id
                )
        );
        return $exercise_ids;
    }

    /* =========================================================== */
    /* function to get all exercise sections by exercise ID */
    /* =========================================================== */

    public function wpl_rd_get_exercise_total_sections($exercise_id) {
        global $wpdb;

        $exercises = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT seq.* from " . $this->table_activator->wpl_rd_course_exercise_sequence() . " seq INNER JOIN $wpdb->posts post ON seq.exercise_id = post.ID WHERE seq.exercise_id = %d AND seq.sequence_status = %d", $exercise_id, 1
                )
        );

        return $exercises;
    }

    /* =========================================================== */
    /* function to get full url */
    /* =========================================================== */

    public function wpl_rd_get_full_url() {
        $full_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $full_url;
    }

    /* =========================================================== */
    /* function to get my course url */
    /* =========================================================== */

    public function wpl_rd_get_my_course_url($url) {
        $url = explode("?", $url);
        return rtrim($url[0], "/");
    }

    /* =========================================================== */
    /* function to get resume course url */
    /* =========================================================== */

    public function wpl_rd_get_resume_course_url($course_id, $user_id) {

        global $wpdb;
        $url = "";
        $exercise_id = 0;

        // get course detail
        $find_course_detail = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE course_post_id = %d AND user_id = %d", $course_id, $user_id
                )
        );

        if (!empty($find_course_detail)) {

            $get_first_module = $wpdb->get_row(
                    $wpdb->prepare(
                            "SELECT * from $wpdb->postmeta WHERE meta_value = %d AND meta_key = %s limit 1", $course_id, 'dd_course_box_post_type'
                    )
            );

            if ($find_course_detail->course_status == 1) {

                $url = "?rpage=course_detail&course_id=" . $course_id . "&st=inprogress#type=module#modid=" . $get_first_module->post_id;
            } else {

                // get exercise detail
                $find_exercise_detail = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE course_post_id = %d AND user_id = %d AND exercise_status = %d", $course_id, $user_id, 0
                        )
                );

                if (!empty($find_exercise_detail)) {

                    $exercise_id = $find_exercise_detail->exercise_id;
                    $chapter_id = get_post_meta($exercise_id, "dd_chapter_id_box_post_type", true);
                    $module_id = get_post_meta($chapter_id, "dd_module_id_box_post_type", true);

                    $url = "?rpage=course_detail&course_id=" . $course_id . "&st=inprogress#type=module#modid=" . $module_id . "#ch=chapter#chid=" . $chapter_id . "#sub=exercise#exeid=" . $exercise_id;
                } else {

                    $url = "?rpage=course_detail&course_id=" . $course_id . "&st=inprogress#type=module#modid=" . $get_first_module->post_id;
                }
            }
        }

        return $url;
    }

    public function wpl_rd_get_resume_exercise_data($user_id) {

        global $wpdb;

        $get_first_incomplete_exe_id = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT exercise_id from " . $this->table_activator->wpl_rd_user_exercise_progress() . " WHERE user_id = %d AND complete_status = %d ORDER BY id DESC limit 1", $user_id, 1
                )
        );
        return $get_first_incomplete_exe_id;
    }

    /* =========================================================== */
    /* function to get all modules by courses */
    /* =========================================================== */

    public function wpl_rd_get_modules_by_course($course_id) {
        global $wpdb;

        $modules = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT pm.*,pm.post_id, p.* from $wpdb->postmeta pm INNER JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE meta_key = %s AND meta_value = %d AND p.post_status = 'publish'", "dd_course_box_post_type", $course_id
                )
        );

        return $modules;
    }

    /* =========================================================== */
    /* function to get all chapters by module */
    /* =========================================================== */

    public function wpl_rd_get_chapters_by_module_id($module_id) {
        global $wpdb;

        $chapters = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT pm.*, pm.post_id, p.* from $wpdb->postmeta pm INNER JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE meta_key = %s AND meta_value = %d AND p.post_status = 'publish'", "dd_module_id_box_post_type", $module_id
                )
        );

        return $chapters;
    }

    /* =========================================================== */
    /* function to get all chapters by module */
    /* =========================================================== */

    public function wpl_rd_get_exercises_by_chapter_id($chapter_id) {
        global $wpdb;

        $exercises = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT pm.*, pm.post_id, p.* from $wpdb->postmeta pm INNER JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE meta_key = %s AND meta_value = %d AND p.post_status = 'publish'", "dd_chapter_id_box_post_type", $chapter_id
                )
        );

        return $exercises;
    }

    public function wpl_rd_get_user_exercise_status($course_id, $user_id) {

        global $wpdb;
        $course_report = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE course_post_id = %d AND user_id = %d", $course_id, $user_id
                )
        );
        return $course_report;
    }

    public function wpl_rd_get_sequence_id_by_seq_no($seq) {

        global $wpdb;
        $seq_report = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->wpl_rd_course_exercise_sequence() . " WHERE sequence_number = %s", $seq
                )
        );
        return $seq_report;
    }

    public function wpl_rd_get_exercise_status_proccessed_by_user($exe_id, $seq_name, $type) {

        global $wpdb;
        global $user_ID;

        $seq_report = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->wpl_rd_user_exercise_progress() . " WHERE seq_number = %s AND exercise_id = %d AND user_id = %d AND exe_type = %s", $seq_name, $exe_id, $user_ID, $type
                )
        );
        return $seq_report;
    }

    public function wpl_rd_calculate_poll_review($exercise_id) {

        global $wpdb;
        $seq_report = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT count(distinct user_id) as total_user, uex.exe_answer from " . $this->table_activator->wpl_rd_course_exercise_sequence() . " ex INNER JOIN " . $this->table_activator->wpl_rd_user_exercise_progress() . " uex ON ex.exercise_id = uex.exercise_id WHERE ex.exercise_id = %d AND uex.exe_type = %s GROUP BY exe_answer", $exercise_id, "poll"
                )
        );
        $options_array = array();
        if (!empty($seq_report)) {
            $total_users = 0;
            foreach ($seq_report as $inx => $report) {
                $total_users += intval($report->total_user);
            }
            foreach ($seq_report as $inx => $report) {
                if ($total_users == 0) {
                    $percentage = 0;
                } else {
                    $percentage = round(($report->total_user / $total_users), 2);
                }

                $options_array[] = array(
                    "name" => $report->exe_answer,
                    "total_percentage" => $percentage * 100
                );
            }
        }
        return array("total_votes" => $total_users, "options" => $options_array);
    }

    public function wpl_rd_get_user_exercise_marked_status($exercise_id) {

        global $wpdb;
        global $user_ID;

        $exe_report = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT exercise_status from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE exercise_id = %d AND user_id = %d", $exercise_id, $user_ID
                )
        );
        return $exe_report;
    }

    public function wpl_rd_has_course_locked($course_id) {

        $has_course_locked = get_post_meta($course_id, "dd_course_type_box_post", true);
        if ($has_course_locked == "locked") {
            return 1;
        } elseif ($has_course_locked == "unlocked") {
            return 0;
        }
    }

    public function wpl_rd_get_saved_exercise_answer($user_id, $exercise_id, $key) {

        global $wpdb;

        $saved_json = get_post_meta($exercise_id, "rd_wpl_exercise_section", true);
        $saved_exercise = json_decode($saved_json, true);

        $section = $saved_exercise['section'];

        $get_answer = '';
        $explanation = '';
        if (!empty($section)) {

            if (strpos($key, '_mcq_') !== false) {
                $get_answer = $section['mcq'][$key]['answer'];
                $explanation = $section['mcq'][$key]['answer_explanation'];
            } elseif (strpos($key, '_single_') !== false) {
                $get_answer = $section['single'][$key]['answer'];
                $explanation = $section['single'][$key]['answer_explanation'];
            }
        }

        $user_answer = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT exe_answer from " . $this->table_activator->wpl_rd_user_exercise_progress() . " WHERE user_id = %d AND exercise_id = %d AND seq_number = %s", $user_id, $exercise_id, $key
                )
        );

        $explanation_div = '';

        if (!empty($explanation)) {
            $explanation_div = "<div class='rd-explanation-area'><span class='rdtr-see-explanation' data-key='" . $key . "' data-exercise='" . $exercise_id . "'>See Explanation</span><p class='toggle-pexplanation' style='display:none;'>" . $explanation . "</p></div>";
        }

        if (trim($user_answer->exe_answer) != trim($get_answer)) {
            return "<span class='rdtr-wrong-answer'>Your answer didn't match.</span><br/>" . $explanation_div;
        } else {
            return "<span class='rdtr-right-answer'>Success, your answer is right.</span><br/>" . $explanation_div;
        }
    }

    public function wpl_rd_get_course_status($course_id) {

        global $user_ID;
        global $wpdb;

        $course_status_detail = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT course_status from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE user_id = %d AND course_post_id = %d", $user_ID, $course_id
                )
        );

        if (!empty($course_status_detail)) {

            if ($course_status_detail->course_status) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

}
