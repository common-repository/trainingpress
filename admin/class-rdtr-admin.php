<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.rudrainnovative.com
 * @since      1.0.0
 *
 * @package    Rdtr
 * @subpackage Rdtr/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rdtr
 * @subpackage Rdtr/admin
 * @author     Rudra Innovative Software Pvt Ltd <info@rudrainnovatives.com>
 */
class Rdtr_Admin {

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
    private $userProgressListTable;
    private $table_activator;
    private $courseProgressListTable;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        if (!class_exists('Training_User_Progress_Table')) {
            require_once(RDTR_TRAINING_DIR_PATH . "admin/class-rdtr-user-progress.php" );
        }

        $myListTable = new Training_User_Progress_Table();

        $this->userProgressListTable = $myListTable;

        if (!class_exists('Training_View_Progress_Table')) {
            require_once(RDTR_TRAINING_DIR_PATH . "admin/class-rdtr-view-progress.php" );
        }

        $viewCourseProgress = new Training_View_Progress_Table();

        $this->courseProgressListTable = $viewCourseProgress;

        require_once RDTR_TRAINING_DIR_PATH . 'includes/class-rdtr-activator.php';
        $table_activator = new Rdtr_Activator();

        $this->table_activator = $table_activator;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style('dashicons');
        // css file for jquery ui dialog
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_style("notification", plugin_dir_url(__FILE__) . 'css/jquery.notifyBar.css', array(), $this->version, 'all');
        wp_enqueue_style("datatable", RDTR_TRAINING_PLUGIN_URL . 'assets/css/jquery.dataTables.min.css', array(), $this->version, 'all');
        wp_enqueue_style("style", RDTR_TRAINING_PLUGIN_URL . 'admin/css/style.css', array(), $this->version, 'all');
        wp_enqueue_style("style-rate", RDTR_TRAINING_PLUGIN_URL . 'assets/css/star-rate.css', array(), $this->version, 'all');
        wp_enqueue_style("fontawsome", RDTR_TRAINING_PLUGIN_URL . 'assets/css/fontawsome/css/font-awesome.min.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        global $post;

        wp_enqueue_script('jquery');
        // jquery ui js file for sortable
        wp_enqueue_script('jquery-ui-sortable');
        // jquery ui js file for dialog
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('validate-js', RDTR_TRAINING_PLUGIN_URL . '/assets/js/jquery.validate.js', array('jquery'), $this->version, true);
        wp_enqueue_script('notification-js', plugin_dir_url(__FILE__) . 'js/jquery.notifyBar.js', array('jquery'), $this->version, true);
        wp_enqueue_script('datatable-js', RDTR_TRAINING_PLUGIN_URL . 'assets/js/jquery.dataTables.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('script-js', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), $this->version, true);

        //for use of tinymce
        $js_src = includes_url('js/tinymce/') . 'tinymce.min.js';
        $css_src = includes_url('css/') . 'editor.css';
        echo '<script src="' . $js_src . '" type="text/javascript"></script>';
        wp_register_style('tinymce_css', $css_src);
        wp_enqueue_style('tinymce_css');

        $valid_post_types = array("chapters", "training", "modules", "exercises");
        $editPostId = isset($_GET['post']) ? intval($_GET['post']) : "";
        $editPostType = get_post_type($editPostId);
        $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : $editPostType;
        $valid = 0;
        if (in_array($post_type, $valid_post_types)) {
            $valid = 1;
        }

        //setting parameters for url 
        if ($post_type == "modules") {
            $couse_id = '';
            if (!empty($editPostId)) {
                $couse_id = get_post_meta($editPostId, "dd_course_box_post_type", true);
            } else {
                $cid = isset($_GET['filter_by_course']) ? intval($_GET['filter_by_course']) : "";
                $couse_id = sanitize_text_field($cid);
            }
            wp_localize_script("script-js", "rdtr_training", array(
                "ajaxurl" => admin_url('admin-ajax.php'),
                "post_type" => $post_type,
                "is_post_type_valid" => $valid,
                "site_url" => site_url(),
                "admin_url" => site_url() . "/wp-admin",
                "course_id" => $couse_id,
                "label_count" => 0
            ));
        } elseif ($post_type == "chapters") {
            $module_id = '';
            if (!empty($editPostId)) {
                $module_id = get_post_meta($editPostId, "dd_module_id_box_post_type", true);
            } else {
                $cid = isset($_GET['filter_by_module']) ? intval($_GET['filter_by_module']) : "";
                $module_id = sanitize_text_field($cid);
            }
            wp_localize_script("script-js", "rdtr_training", array(
                "ajaxurl" => admin_url('admin-ajax.php'),
                "post_type" => $post_type,
                "is_post_type_valid" => $valid,
                "site_url" => site_url(),
                "admin_url" => site_url() . "/wp-admin",
                "module_id" => $module_id,
                "label_count" => 0
            ));
        } elseif ($post_type == "exercises") {
            $exercise_id = '';
            if (!empty($editPostId)) {
                $exercise_id = get_post_meta($editPostId, "dd_chapter_id_box_post_type", true);
            } else {
                $cid = isset($_GET['filter_by_chapter']) ? intval($_GET['filter_by_chapter']) : "";
                $exercise_id = sanitize_text_field($cid);
            }
            wp_localize_script("script-js", "rdtr_training", array(
                "ajaxurl" => admin_url('admin-ajax.php'),
                "post_type" => $post_type,
                "is_post_type_valid" => $valid,
                "site_url" => site_url(),
                "admin_url" => site_url() . "/wp-admin",
                "exercise_id" => $exercise_id,
                "label_count" => 0
            ));
        } else {
            wp_localize_script("script-js", "rdtr_training", array(
                "ajaxurl" => admin_url('admin-ajax.php'),
                "post_type" => $post_type,
                "is_post_type_valid" => $valid,
                "label_count" => 0
            ));
        }
    }

    /* =========================================================== */
    /* function for plugin options for installed plugin list */
    /* =========================================================== */

    public function add_settings_link($links) {
        //$link = "#"; // Need to change later
        $link = admin_url('admin.php?page=training-settings');
        $settings_link = '<a href="' . $link . '">' . __('Settings') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }

    /* =========================================================== */
    /* function to add menus for training plugin */
    /* =========================================================== */

    public function wpl_rd_add_menus() {

        add_menu_page('Training', 'Training', 'administrator', 'training', false, 'dashicons-welcome-learn-more', 66);
        if (has_action('training_adon_menus')) {
            do_action("training_adon_menus"); // adding email templates as menu
        }
        add_submenu_page('training', 'Training - Settings', 'Settings', 'administrator', 'training-settings', array($this, "wpl_plugin_settings_callback"));        
        add_submenu_page('training', 'User Progress', 'User Progress', 'administrator', 'user-progress', array($this, "wpl_user_course_progress"));
        add_submenu_page('training', 'View Progress', 'View Progress', 'administrator', 'view-progress', array($this, "wpl_view_course_progress"));
    }

    public function wpl_user_course_progress() {
        global $wpdb;
        //$this->userProgressListTable->wpl_user_course_progress();
        $course_id = isset($_REQUEST['c']) ? intval($_REQUEST['c']) : "";
        $user_id = isset($_REQUEST['u']) ? intval($_REQUEST['u']) : 0;
        $chapter_id = isset($_REQUEST['ch']) ? intval($_REQUEST['ch']) : 0;

        ob_start();

        include_once RDTR_TRAINING_DIR_PATH . 'admin/partials/rdtr-course-progress.php';
        $template = ob_get_contents();
        ob_end_clean();

        echo $template;
    }

    public function wpl_view_course_progress() {

        $this->courseProgressListTable->wpl_view_course_progress();
    }

    /* =========================================================== */
    /* function to add metaboxes */
    /* =========================================================== */

    public function wpl_tr_meta_box_add() {

        //add back button to course
        add_meta_box('btn-back-exercises', 'Click here to back', array($this, "wpl_back_to_exercises_list"), "exercises", 'side', 'high');

        //add back button to course
        add_meta_box('btn-back-chapter', 'Click here to back', array($this, "wpl_back_to_chapters_list"), "chapters", 'side', 'high');

        //add back button to course
        add_meta_box('btn-back-module', 'Click here to back', array($this, "wpl_back_to_module_list"), "modules", 'side', 'high');

        //add course metabox for module section
        add_meta_box('dd-courses', 'Course', array($this, "wpl_course_meta_box_layout"), 'modules', 'side', 'high');

        //add back button to course
        add_meta_box('btn-back-course', 'Click here to back', array($this, "wpl_back_to_course_list"), RDTR_TRAINING_POST_TYPE, 'side', 'high');

        //add course author metabox for course section
        add_meta_box('dd-course-author', 'Course Author', array($this, "wpl_course_author_meta_box_layout"), RDTR_TRAINING_POST_TYPE, 'side', 'high');

        //add course type metabox for course section
        add_meta_box('dd-course-type', 'Course Type', array($this, "wpl_course_type_filter_meta_box_layout"), RDTR_TRAINING_POST_TYPE, 'side', 'high');

        //add module metabox for chapters section
        add_meta_box('dd-module', 'Modules', array($this, "wpl_module_meta_box_layout"), 'chapters', 'side', 'high');

        //add chapters metabox for excercises section
        add_meta_box('dd-chapter', 'Chapters', array($this, "wpl_chapter_meta_box_layout"), 'exercises', 'side', 'high');

        //add chapters metabox for excercises section
        add_meta_box('dd-chapter-assignment', 'Chapter Assignment', array($this, "wpl_chapter_assignment_meta_box_layout"), 'chapters', 'normal', 'high');

        // metabox to take hour to complete
        add_meta_box('dd-exercise-hour', 'Estimated Hour(s)', array($this, "wpl_exercise_hour_meta_box_layout"), 'exercises', 'side', 'high');

        // metabox for question type
        add_meta_box('dd-exercise-sections', 'Add Sections', array($this, "wpl_add_sections_to_exercise"), 'exercises', 'normal', 'high');

        // metabox for course features list
        add_meta_box('dd-course-features-sections', 'Course Features', array($this, "wpl_add_features_section_to_course"), 'training', 'normal', 'high');

        add_meta_box('course-banner-image', 'Banner Image', array($this, "wpl_add_banner_image_section_to_course"), 'training', 'side', 'high');
    }

    public function wpl_add_banner_image_section_to_course($post) {
        $image = '';
        $saved_banner_image = get_post_meta($post->ID, "course_banner_image", true);
        if (!empty($saved_banner_image)) {
            $image = $saved_banner_image;
        }
        ?>
        <style>
            .banner-image-preview-image{
                height: 100px;
                width:200px;
            }
        </style>
        <div id="banner-upload">
            Upload Image: <a href="javascript:void(0)" id="anchor_banner_upload">Click here to Upload</a>
            <br/><label><i>Note*: Banner Image size should be 1200px X 350px (width x height)</i></label>
            <img class="banner-image-preview <?php
            if (!empty($image)) {
                echo 'banner-image-preview-image';
            }
            ?>" src="<?php
                 if (!empty($image)) {
                     echo $image;
                 }
                 ?>"/>
            <input type="hidden" name="course_banner_image" id="course_banner_image"/>
        </div>
        <?php
    }

    public function wpl_save_course_banner_image($post_id, $post) {

        $banner_image = isset($_REQUEST['course_banner_image']) ? esc_attr($_REQUEST['course_banner_image']) : "";

        if (!empty($banner_image)) {

            update_post_meta($post_id, "course_banner_image", $banner_image);
        }
    }

    // button to go to courses list
    public function wpl_back_to_course_list() {
        ?>
        <div>
            <a href="edit.php?post_type=training" class="button button-primary button-large">Back to course list</a>
        </div>
        <?php
    }

    // button to go to modules list
    public function wpl_back_to_module_list($post) {
        $course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : '';
        if (empty($course_id)) {
            $course_id = get_post_meta($post->ID, "dd_course_box_post_type", true);
        }
        ?>
        <div>
            <a href="edit.php?post_type=modules&filter_by_course=<?php echo $course_id; ?>" class="wpl-anchor wpl-rd-btn button button-primary button-large">Back to modules list</a>
        </div>
        <?php
    }

    // button to go to chapters list
    public function wpl_back_to_chapters_list($post) {
        $module_id = isset($_GET['module_id']) ? intval($_GET['module_id']) : '';
        if (empty($module_id)) {
            $module_id = get_post_meta($post->ID, "dd_module_id_box_post_type", true);
        }
        ?>
        <div>
            <a href="edit.php?post_type=chapters&filter_by_module=<?php echo $module_id; ?>" class="wpl-anchor wpl-rd-btn button button-primary button-large">Back to chapters list</a>
        </div>
        <?php
    }

    // button to go exercises list
    public function wpl_back_to_exercises_list($post) {
        $exercise_id = isset($_GET['chapter_id']) ? intval($_GET['chapter_id']) : '';
        if (empty($exercise_id)) {
            $exercise_id = get_post_meta($post->ID, "dd_chapter_id_box_post_type", true);
        }
        ?>
        <div>
            <a href="edit.php?post_type=exercises&filter_by_chapter=<?php echo $exercise_id; ?>" class="wpl-anchor wpl-rd-btn button button-primary button-large">Back to exercises list</a>
        </div>
        <?php
    }

    public function wpl_add_features_section_to_course($post) {

        global $wpdb;
        wp_nonce_field(basename(__FILE__), 'dd-course-features-section');
        ?>
        <div>
            <div id="add-more-features" class="wptr-rud-fea">
                <?php
                $features_post_meta = get_post_meta($post->ID, "course_features_list", true);
                $course_features_list = json_decode($features_post_meta);

                if (!empty($course_features_list)) {
                    if (is_array($course_features_list)) {
                        $count = 1;
                        foreach ($course_features_list as $stx) {
                            if ($count < 2) {
                                ?>
                                <div class="single-feature-div">
                                    <input type="text" value="<?php echo $stx; ?>" class="course-feature-txt" name="txt_course_feature[]" placeholder="Enter feature..." size="30"/> 
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="single-feature-div">
                                    <input type="text" value="<?php echo $stx; ?>" class="course-feature-txt" name="txt_course_feature" placeholder="Enter feature..." size="30"/> <span class="remove-features-list">&times;</span>
                                </div>
                                <?php
                            }

                            $count++;
                        }
                    }
                } else {
                    ?>
                    <div class="single-feature-div">
                        <input type="text" class="course-feature-txt" name="txt_course_feature[]" placeholder="Enter feature..." size="30"/> 
                    </div>
                    <?php
                }
                ?>
            </div>
            <button type="button" class="button button-primary button-large course-btn-add-more" id="add-more-features-btn">+ Add More</button>
        </div>
        <?php
    }

    // function to add features of course
    public function wpl_dd_course_feature_meta_box($post_id, $post) {

        global $wpdb;

        if (!isset($_POST["dd-course-features-section"]) || !wp_verify_nonce($_POST["dd-course-features-section"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        $slug = "training";
        if ($slug != $post->post_type)
            return;

        /* Store the user enter value in a variable */
        $course_features_list = "";
        if (isset($_POST["txt_course_feature"])) {

            $course_features_list = json_encode($_POST["txt_course_feature"]);
        } else {
            $course_features_list = "";
        }
        update_post_meta($post_id, "course_features_list", $course_features_list);
    }

    // function to make dropdown of sections on exercise
    public function wpl_add_sections_to_exercise($post) {

        global $wpdb;
        wp_enqueue_media();
        wp_nonce_field(basename(__FILE__), 'dd-add-exercise-sections');
        ?>
        <p class="not-sort-p">
            <label>Add Sections</label>
            <select id="dd-ex-section-type">
                <option value="-1">Select section</option>
                <option value="paragraph">Paragraph</option>
                <option value="video_image">Add Video/Image</option>
                <option value="mulitple_choice">Question with Multiple Choice</option>
                <option value="single_choice">Question with Single Choice</option>
                <option value="poll_type">Poll Type</option>
                <option value="reflection">Reflection</option>
            </select>
        </p>
        <?php
        $get_saved_sections = get_post_meta($post->ID, "rd_wpl_exercise_section", true);

        if (!empty($get_saved_sections)) {

            $get_saved_sections = str_replace("\\", "", $get_saved_sections);
            $saved_value_array = json_decode($get_saved_sections, true);

            $saved_order = $saved_value_array['order'];
            $saved_sections = $saved_value_array['section'];
            $totalPara = isset($saved_sections['paragraph']) ? count($saved_sections['paragraph']) : 0;
            $totalFile = isset($saved_sections['file']) ? count($saved_sections['file']) : 0;
            $totalMcq = isset($saved_sections['mcq']) ? count($saved_sections['mcq']) : 0;
            $totalSingle = isset($saved_sections['single']) ? count($saved_sections['single']) : 0;
            $totalPoll = isset($saved_sections['poll']) ? count($saved_sections['poll']) : 0;
            $totalReflection = isset($saved_sections['reflection']) ? count($saved_sections['reflection']) : 0;
            ?>
            <script>
                var paraCount = <?php echo $totalPara ?>;
                var fileCount = <?php echo $totalFile ?>;
                var mcqCount = <?php echo $totalMcq; ?>;
                var singleCount = <?php echo $totalSingle; ?>;
                var pollCount = <?php echo $totalPoll; ?>;
                var reflectionCount = <?php echo $totalReflection; ?>;
            </script>
            <?php
            if (!empty($saved_order) && !empty($saved_sections)) {

                $file_count = 1;

                foreach ($saved_order as $inx => $stx) {
                    $sec = $stx;
                    $sec = explode("_", $sec);
                    array_pop($sec);
                    $sec = implode("_", $sec);

                    if (strpos($sec, '_para_') !== false) {
                        ?>
                        <div class="ex-add ex-section-sort paragraph-section">
                            <label class="lbl-ex-para textarea-label">Please put contents to read for users</label>
                            <button type="button" class="button ex-btn ex-move-section-to-trash">Remove section</button>
                            <p> 
                                <textarea rows="6" cols="80" class="rdtr-paragraph-tinymce exercise-textarea" name="<?php echo isset($saved_sections['paragraph'][$stx]['name']) ? $saved_sections['paragraph'][$stx]['name'] : ''; ?>"><?php echo isset($saved_sections['paragraph'][$stx]['value']) ? $saved_sections['paragraph'][$stx]['value'] : ''; ?></textarea>
                            </p> 
                            <input type="hidden" value="<?php echo isset($saved_sections['paragraph'][$stx]['order']) ? $saved_sections['paragraph'][$stx]['order'] : ''; ?>" name="section_order[]"/>
                        </div>
                        <?php
                    } elseif (strpos($sec, '_file_') !== false) {

                        $file_type = isset($saved_sections['file'][$stx]['type']) ? $saved_sections['file'][$stx]['type'] : '';
                        ?>
                        <div class="ex-add ex-section-sort video-section">
                            <label class="lbl-ex-para">Image/Video section</label>
                            <button class="button ex-btn ex-move-section-to-trash" type="button">Remove section</button>
                            <p>
                                Upload video <input type="radio" name="rdb_upload_file_<?php echo $file_count; ?>" class='rdb_upload_file_ex' value="gallery" <?php
                                if ($file_type == "gallery") {
                                    echo "checked='checked'";
                                }
                                ?>/> Gallery 
                                <input type="radio" name="rdb_upload_file_<?php echo $file_count++; ?>" class='rdb_upload_file_ex' value="youtube" <?php
                                if ($file_type == "youtube") {
                                    echo "checked='checked'";
                                }
                                ?>/> Youtube
                            </p>
                            <div class="add-file-html">
                                <?php
                                if ($file_type == "gallery") {
                                    ?>
                                    <div class="para-gallery">
                                        <button class="button button-primary button-large btn-upload-gallery" type="button">Click here to upload</button>
                                        <span id="gallery-upload-msg">
                                            <br/><label><i>File path: <?php echo isset($saved_sections['file'][$stx]['value']) ? $saved_sections['file'][$stx]['value'] : ''; ?></i></label>
                                        </span>
                                        <?php
                                        $prev_file = $saved_sections['file'][$stx]['value'];
                                        $ext = pathinfo($prev_file, PATHINFO_EXTENSION);
                                        if (in_array($ext, array("png", "gif", "jpeg", "jpg"))) {
                                            ?>
                                            <img src='<?php echo $prev_file ?>' class='rd-img-prev-fit'/>
                                            <?php
                                        } elseif (in_array($ext, array("mp3"))) {
                                            echo do_shortcode('[audio src="' . $prev_file . '"]');
                                        } elseif (in_array($ext, array("mp4", "webm"))) {
                                            echo do_shortcode('[video mp4="' . $prev_file . '" ogv="' . $prev_file . '" webm="' . $prev_file . '"]');
                                        }
                                        ?>
                                        <input type="hidden" value="<?php echo isset($saved_sections['file'][$stx]['value']) ? $saved_sections['file'][$stx]['value'] : ''; ?>" name="<?php echo isset($saved_sections['file'][$stx]['name']) ? $saved_sections['file'][$stx]['name'] : ''; ?>" class="txt_file_url"/>
                                    </div>
                                    <input type="hidden" value="<?php echo isset($saved_sections['file'][$stx]['order']) ? $saved_sections['file'][$stx]['order'] : ''; ?>" name="section_order[]"/>
                                    <input type="hidden" name="<?php echo isset($saved_sections['file'][$stx]['name']) ? $saved_sections['file'][$stx]['name'] : ''; ?>_upload_type" value="gallery"/>

                                    <?php
                                } elseif ($file_type == "youtube") {
                                    ?>
                                    <p class="para-youtube">
                                        <label>Youtube URL</label>
                                        <input type="url" value="<?php echo isset($saved_sections['file'][$stx]['value']) ? $saved_sections['file'][$stx]['value'] : ''; ?>" name="<?php echo isset($saved_sections['file'][$stx]['name']) ? $saved_sections['file'][$stx]['name'] : ''; ?>"/>
                                    </p>
                                    <input type="hidden" value="<?php echo isset($saved_sections['file'][$stx]['order']) ? $saved_sections['file'][$stx]['order'] : ''; ?>" name="section_order[]"/> 
                                    <input type="hidden" name="<?php echo isset($saved_sections['file'][$stx]['name']) ? $saved_sections['file'][$stx]['name'] : ''; ?>_upload_type" value="youtube"/>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    } elseif (strpos($sec, '_mcq_') !== false) {

                        $chk_order = $saved_sections['mcq'][$stx]['order'];
                        $chk_name = isset($saved_sections['mcq'][$stx]['id']) ? $saved_sections['mcq'][$stx]['id'] : '';
                        $label_index = 0;
                        $lbl_index = $stx;
                        ?>
                        <div class="ex-add ex-section-sort mcq-section">
                            <label class="lbl-ex-para">Question with Multiple Choice</label>
                            <button class="button ex-btn ex-move-section-to-trash" type="button">Remove section</button>
                            <span class="fix-messgae-note"><i>Note*: Please Select options after making MCQ else user's response will not be tracked.</i></span>
                            <p class="mcq-area question-choice">
                                <label class="lbl-ex-bold">Question</label>
                                <input type="text" value="<?php echo isset($saved_sections['mcq'][$stx]['question']) ? $saved_sections['mcq'][$stx]['question'] : ''; ?>" name="<?php echo isset($saved_sections['mcq'][$stx]['id']) ? $saved_sections['mcq'][$stx]['id'] : ''; ?>_question"/>
                            </p>
                            <div class="add-mcq-choices">
                                <label class="lbl-ex-bold">Add Options</label>
                                <ul class="ul-add-option">
                                    <?php
                                    $chk_options = isset($saved_sections['mcq'][$stx]['options']) ? $saved_sections['mcq'][$stx]['options'] : array();

                                    if (!empty($chk_options)) {

                                        $options_index = isset($saved_sections['mcq'][$lbl_index]['option_index']) ? $saved_sections['mcq'][$lbl_index]['option_index'] : array();
                                        $correct_answers_index = !empty($saved_sections['mcq'][$lbl_index]['correct_answers']) ? $saved_sections['mcq'][$lbl_index]['correct_answers'] : array();

                                        $opt_index = 0;
                                        foreach ($chk_options as $inx => $stx) {
                                            if (empty($stx)) {
                                                continue;
                                            }
                                            ?>
                                            <li>
                                                <?php
                                                $checked = '';

                                                if (in_array($options_index[$opt_index], $correct_answers_index)) {
                                                    $checked = 'checked';
                                                }
                                                ?>
                                                <input type="checkbox" <?php echo $checked; ?> value="<?php echo $options_index[$opt_index]; ?>" name="<?php echo $chk_name; ?>_correct[]"/> 
                                                <input type="text" data-id="<?php echo $options_index[$opt_index]; ?>" value="<?php echo isset($stx) ? $stx : ''; ?>" name="<?php echo $chk_name; ?>_option[]"/>
                                                <input type="hidden" value="<?php echo $options_index[$opt_index]; ?>" name="<?php echo $chk_name; ?>_answer_index[]"/>
                                                <button class="btn-remove-option"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                            </li>
                                            <?php
                                            $opt_index++;
                                            $label_index++;
                                        }
                                    }
                                    ?>
                                </ul>
                                <button class="button button-primary button-large add-more-chk-option" type="button" data-id="<?php echo $chk_name ?>"> + Add more</button>
                            </div>
                            <p>
                                <label class="lbl-ex-bold">Answer Explanation</label>
                                <textarea rows="4" cols="40" name="<?php echo $chk_name; ?>_answer_explanation" placeholder="Explanation" class="exercise-textarea"><?php echo $saved_sections['mcq'][$lbl_index]['answer_explanation']; ?></textarea>
                            </p>
                            <input type="hidden" value="<?php echo $chk_order; ?>" name="section_order[]"/>
                        </div>
                        <?php
                    } elseif (strpos($sec, '_single_') !== false) {

                        // to get the order id of Single Radio checked options
                        $rdb_order = $saved_sections['single'][$stx]['order'];
                        $lbl_index = $stx;
                        //print_r($saved_sections['single'][$stx]);
                        $label_index = 0;
                        $single_id = isset($saved_sections['single'][$stx]['id']) ? $saved_sections['single'][$stx]['id'] : '';
                        ?>
                        <div class="ex-add ex-section-sort single-choice-section">
                            <label class="lbl-ex-para">Question with Single Choice</label>
                            <button class="button ex-btn ex-move-section-to-trash" type="button">Remove section</button>
                            <span class="fix-messgae-note"><i>Note*: Please Select option after making Single Choice else user's response will not be tracked.</i></span>
                            <p class="mcq-area">
                                <label class="lbl-ex-bold">Question</label>
                                <input type="text" value="<?php echo isset($saved_sections['single'][$stx]['question']) ? $saved_sections['single'][$stx]['question'] : ''; ?>" name="<?php echo isset($saved_sections['single'][$stx]['id']) ? $saved_sections['single'][$stx]['id'] : ''; ?>_question"/>
                            </p>
                            <div class="add-single-choice">
                                <label class="lbl-ex-bold">Add Options</label>
                                <ul class="ul-add-option">
                                    <?php
                                    $rdb_options = isset($saved_sections['single'][$stx]['options']) ? $saved_sections['single'][$stx]['options'] : '';
                                    $options_index = isset($saved_sections['single'][$lbl_index]['option_index']) ? $saved_sections['single'][$lbl_index]['option_index'] : array();
                                    $correct_answers_index = isset($saved_sections['single'][$lbl_index]['correct_answer']) ? $saved_sections['single'][$lbl_index]['correct_answer'] : '';

                                    if (!empty($rdb_options)) {

                                        $opt_index = 0;

                                        foreach ($rdb_options as $inx => $stx) {
                                            if (empty($stx)) {
                                                continue;
                                            }

                                            $checked = '';
                                            if ($options_index[$opt_index] == $correct_answers_index) {
                                                $checked = 'checked';
                                            }
                                            ?>
                                            <li>
                                                <input type="radio" <?php echo $checked; ?> value="<?php echo $label_index; ?>" name="<?php echo $single_id; ?>_rdb_correct"/> 
                                                <input value="<?php echo isset($stx) ? $stx : ''; ?>" data-id="<?php echo $label_index; ?>" type="text" name="<?php echo $single_id; ?>_option[]"/>
                                                <input type="hidden" value="<?php echo $label_index; ?>" name="<?php echo $single_id; ?>_answer_index[]"/>
                                                <button class="btn-remove-option"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                            </li>
                                            <?php
                                            $opt_index++;
                                            $label_index++;
                                        }
                                    }
                                    ?>
                                </ul>
                                <button class="button button-primary button-large add-more-radio-option" type="button" data-id="<?php echo $single_id ?>"> + Add more</button>
                                <p>
                                    <label class="lbl-ex-bold">Answer Explanation</label>
                                    <textarea rows="4" cols="40" name="<?php echo $single_id; ?>_answer_explanation" placeholder="Explanation" class="exercise-textarea"><?php echo $saved_sections['single'][$lbl_index]['answer_explanation']; ?></textarea>
                                </p>
                                <input type="hidden" value="<?php echo $rdb_order; ?>" name="section_order[]"/>
                            </div>
                        </div>
                        <?php
                    } elseif (strpos($sec, '_poll_') !== false) {
                        // to get poll order
                        $poll_order = $saved_sections['poll'][$stx]['order'];
                        $label_index = 1;
                        $poll_id = isset($saved_sections['poll'][$stx]['id']) ? $saved_sections['poll'][$stx]['id'] : '';
                        ?>
                        <div class="ex-add ex-section-sort">
                            <label class="lbl-ex-para">Poll type Question</label>
                            <button class="button ex-btn ex-move-section-to-trash" type="button">Remove section</button>
                            <p class="mcq-area">
                                <label class="lbl-ex-bold">Question</label>
                                <input type="text" value="<?php echo isset($saved_sections['poll'][$stx]['question']) ? $saved_sections['poll'][$stx]['question'] : ''; ?>" name="<?php echo isset($saved_sections['poll'][$stx]['id']) ? $saved_sections['poll'][$stx]['id'] : ''; ?>_question"/>
                            </p>
                            <div class="add-poll-choice">
                                <div class="add-poll-choice-cont">
                                    <label class="lbl-ex-bold">Add Options</label>
                                    <ul class="ul-add-option">
                                        <?php
                                        $ct = 0;
                                        $rdb_options = isset($saved_sections['poll'][$stx]['options']) ? $saved_sections['poll'][$stx]['options'] : '';
                                        if (!empty($rdb_options)) {
                                            foreach ($rdb_options as $inx => $stx) {
                                                if (empty($stx)) {
                                                    continue;
                                                }
                                                $ct++;
                                                ?>
                                                <li>
                                                    <?php echo $label_index++ ?>. <input value="<?php echo isset($stx) ? $stx : ''; ?>" type="text" name="<?php echo $poll_id; ?>_option[]"/>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <button class="button button-primary button-large add-more-poll-option" data-next="<?php echo $ct; ?>" type="button" data-id="<?php echo $poll_id ?>"> + Add more</button>
                                <input type="hidden" value="<?php echo $poll_order; ?>" name="section_order[]"/>
                            </div>
                        </div>
                        <?php
                    } elseif (strpos($sec, '_reflection_') !== false) {
                        // to get the order of reflection
                        $reflection_order = $saved_sections['reflection'][$stx]['order'];
                        ?>
                        <div class="ex-add ex-section-sort reflection-section">
                            <label class="lbl-ex-para textarea-label">Please provide a Question for the user.</label>
                            <button type="button" class="button ex-btn ex-move-section-to-trash">Remove section</button>
                            <p> 
                                <textarea rows="6" cols="80" class="rdtr-paragraph-tinymce exercise-textarea" name="<?php echo isset($saved_sections['reflection'][$stx]['name']) ? $saved_sections['reflection'][$stx]['name'] : ''; ?>"><?php echo isset($saved_sections['reflection'][$stx]['value']) ? $saved_sections['reflection'][$stx]['value'] : ''; ?></textarea>
                            </p> 
                            <input type="hidden" value="<?php echo $reflection_order; ?>" name="section_order[]"/>
                        </div>
                        <?php
                    }
                }
            }
        } else {
            ?>
            <script>
                var paraCount = 0;
                var fileCount = 0;
                var mcqCount = 0;
                var singleCount = 0;
                var pollCount = 0;
                var reflectionCount = 0;
            </script>
            <?php
        }
    }

    public function wpl_dd_save_exercises_sections($post_id, $post) {
        global $wpdb;

        if (!isset($_POST["dd-add-exercise-sections"]) || !wp_verify_nonce($_POST["dd-add-exercise-sections"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        $slug = "exercises";
        if ($slug != $post->post_type)
            return;

       $section_order = isset($_POST['section_order']) ?  array_map('sanitize_text_field', wp_unslash($_POST['section_order'])) : '';

        $item = array();

        if (!empty($section_order)) {

            $item["order"] = $section_order;

            $chapter_id = get_post_meta($post_id, "dd_chapter_id_box_post_type", true);

            $module_id = get_post_meta($chapter_id, "dd_module_id_box_post_type", true);

            $course_id = get_post_meta($module_id, "dd_course_box_post_type", true);

            $has_course_exercise = $wpdb->get_row(
                    $wpdb->prepare(
                            "SELECT * from " . $this->table_activator->wpl_rd_course_exercise_sequence() . " WHERE course_id = %d AND exercise_id = %d AND sequence_status = 1", $course_id, $post_id
                    )
            );

            if (!empty($has_course_exercise)) {

                // insert into exercise sequence
                $wpdb->update($this->table_activator->wpl_rd_course_exercise_sequence(), array(
                    "sequence_status" => 0
                        ), array(
                    "course_id" => $course_id,
                    "exercise_id" => $post_id
                ));
            }

            foreach ($section_order as $order) {

                // insert into exercise sequence
                $wpdb->insert($this->table_activator->wpl_rd_course_exercise_sequence(), array(
                    "course_id" => $course_id,
                    "exercise_id" => $post_id,
                    "sequence_number" => $order,
                ));

                //finding element indexes
                $sec = $order;
                $sec = explode("_", $sec);
                array_pop($sec);
                $sec = implode("_", $sec);
                if (strpos($sec, '_para_') !== false) {

                    $value = isset($_POST[$sec]) ? htmlentities($_POST[$sec]) : "";

                    //$value = str_replace('"', "'", $value);

                    $item["section"]["paragraph"][$order] = array(
                        "name" => $sec,
                        "order" => $order,
                        "value" => $value
                    );
                } elseif (strpos($sec, '_file_') !== false) {

                    $value = isset($_POST[$sec]) ? esc_attr($_POST[$sec]) : "";
                    $item["section"]["file"][$order] = array(
                        "name" => $sec,
                        "order" => $order,
                        "value" => $value,
                        "type" => $_POST[$sec . "_upload_type"]
                    );
                } elseif (strpos($sec, '_mcq_') !== false) {

                    //$correct_answer = isset($_POST[$sec . "_correct"]) ? htmlentities($_POST[$sec . "_correct"]) : "";
                    $questions = isset($_POST[$sec . "_question"]) ? htmlentities($_POST[$sec . "_question"]) : "";

                    //$options = isset($_POST[$sec . "_option"]) ? $_POST[$sec . "_option"] : "";
                    $options = array_map('sanitize_text_field', wp_unslash($_POST[$sec . "_option"]));

                    //$option_index = isset($_POST[$sec . "_answer_index"]) ? $_POST[$sec . "_answer_index"] : "";
                    $option_index = array_map('sanitize_text_field', wp_unslash($_POST[$sec . "_answer_index"]));

                    //$correct_answers_index = isset($_POST[$sec . "_correct"]) ? $_POST[$sec . "_correct"] : array();
                    $correct_answers_index = array_map('sanitize_text_field', wp_unslash($_POST[$sec . "_correct"]));

                    $correct_answers_explanation = isset($_POST[$sec . "_answer_explanation"]) ? esc_attr($_POST[$sec . "_answer_explanation"]) : "";

                    $item["section"]["mcq"][$order] = array(
                        "id" => $sec,
                        "order" => $order,
                        "answer" => implode(",", $correct_answers_index),
                        "question" => $questions,
                        "options" => $options,
                        "option_index" => $option_index,
                        "correct_answers" => $correct_answers_index,
                        "answer_explanation" => $correct_answers_explanation
                    );
                } elseif (strpos($sec, '_single_') !== false) {

                    //$correct_answer = isset($_POST[$sec . "_correct"]) ? htmlentities($_POST[$sec . "_correct"]) : "";
                    $questions = isset($_POST[$sec . "_question"]) ? htmlentities($_POST[$sec . "_question"]) : "";

                    //$options = isset($_POST[$sec . "_option"]) ? $_POST[$sec . "_option"] : "";
                    $options = array_map('sanitize_text_field', wp_unslash($_POST[$sec . "_option"]));

                    //$option_index = isset($_POST[$sec . "_answer_index"]) ? $_POST[$sec . "_answer_index"] : "";
                    $option_index = array_map('sanitize_text_field', wp_unslash($_POST[$sec . "_answer_index"]));

                    $correct_answers_index = isset($_POST[$sec . "_rdb_correct"]) ? esc_attr($_POST[$sec . "_rdb_correct"]) : "";

                    $correct_answers_explanation = isset($_POST[$sec . "_answer_explanation"]) ? esc_attr($_POST[$sec . "_answer_explanation"]) : "";

                    $item["section"]["single"][$order] = array(
                        "id" => $sec,
                        "order" => $order,
                        "answer" => $correct_answers_index,
                        "question" => $questions,
                        "options" => $options,
                        "option_index" => $option_index,
                        "correct_answer" => $correct_answers_index,
                        "answer_explanation" => $correct_answers_explanation
                    );
                } elseif (strpos($sec, '_poll_') !== false) {

                    $questions = isset($_POST[$sec . "_question"]) ? htmlentities($_POST[$sec . "_question"]) : "";
                    //$options = isset($_POST[$sec . "_option"]) ? $_POST[$sec . "_option"] : "";

                    $options = array_map('sanitize_text_field', wp_unslash($_POST[$sec . "_option"]));

                    $item["section"]["poll"][$order] = array(
                        "id" => $sec,
                        "order" => $order,
                        "question" => $questions,
                        "options" => $options
                    );
                } elseif (strpos($sec, '_reflection_') !== false) {

                    $value = isset($_POST[$sec]) ? htmlentities($_POST[$sec]) : "";
                    $item["section"]["reflection"][$order] = array(
                        "name" => $sec,
                        "order" => $order,
                        "value" => $value
                    );
                }
            }

            $item = json_encode($item);

            // check for user enrol

            $users_enrolled = $wpdb->get_results(
                    $wpdb->prepare(
                            "SELECT * from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE course_post_id = %d", $course_id
                    )
            );

            if (count($users_enrolled) > 0) {

                foreach ($users_enrolled as $inx => $stx) {

                    $user_id = $stx->user_id;

                    $wpdb->insert($this->table_activator->wpl_rd_course_progress_tbl(), array(
                        "course_post_id" => $course_id,
                        "user_id" => $user_id,
                        "exercise_id" => $post_id,
                        "exercise_status" => 0
                    ));
                }
            }
        }

        update_post_meta($post_id, "rd_wpl_exercise_section", $item);
    }

    public function wpl_chapter_assignment_meta_box_layout($post) {
        wp_nonce_field(basename(__FILE__), 'txt-rdb-assignment-nonce');
        ?>
        <p>
            <label for="rdb_assignment"><?php echo __('Has chapter assignment'); ?></label>
            <?php
            $has_chapter_assignmment = get_post_meta($post->ID, "rdb_has_chapter_assignmment", true);
            if ($has_chapter_assignmment == "yes") {
                ?>
                <input type="radio" value="yes" name="rdb_assignment" checked/> Yes
                <input type="radio" value="no" name="rdb_assignment"/> No 
                <?php
            } elseif (empty($has_chapter_assignmment) || $has_chapter_assignmment == "no") {
                ?>
                <input type="radio" value="yes" name="rdb_assignment"/> Yes
                <input type="radio" value="no" name="rdb_assignment" checked/> No 
                <?php
            }
            ?>
        </p>
        <div id='chatpter-assignment-editor' style='<?php
        if ($has_chapter_assignmment == "yes") {
            echo "display:block;";
        } else {
            echo "display:none;";
        }
        ?>'>
            <h4>Please do list for assignment sections:</h4>
            <?php
            $editor_content = get_post_meta($post->ID, "chapter_editor_has_assignment", true);
            ?>
            <?php wp_editor($editor_content, "editor_has_assignment"); ?>
        </div>
        <?php
    }

    public function wpl_chapter_assignment_meta_box_save_meta($post_id, $post) {

        global $wpdb;

        if (!isset($_POST["txt-rdb-assignment-nonce"]) || !wp_verify_nonce($_POST["txt-rdb-assignment-nonce"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        $slug = "chapters";
        if ($slug != $post->post_type)
            return;

        /* Store the user enter value in a variable */
        $chapter_assignment = "";
        if (isset($_POST["rdb_assignment"])) {

            $chapter_assignment = sanitize_text_field($_POST["rdb_assignment"]);
        } else {
            $chapter_assignment = "";
        }
        update_post_meta($post_id, "rdb_has_chapter_assignmment", $chapter_assignment);

        if (isset($_POST['editor_has_assignment'])) {
            update_post_meta($post_id, 'chapter_editor_has_assignment', wp_kses_post($_POST['editor_has_assignment']));
        }
    }

    public function wpl_exercise_hour_meta_box_layout($post) {
        wp_nonce_field(basename(__FILE__), 'txt-total-exercise-hours-nonce');
        ?>
        <p>
            <?php
            $saved_hour = get_post_meta($post->ID, "txt_exercise_complete_hour", true);
            ?>
            <label for="txt_save_exercise_hour"><?php echo __('Estimated hour(s) to complete exercise'); ?></label>
            <input type="number" name="txt_save_exercise_hour" min="0" value="<?php echo!empty($saved_hour) ? $saved_hour : 1; ?>" id="txt_save_exercise_hour"/>
        </p>
        <?php
    }

    public function wpl_txt_hours_meta_box_save_meta($post_id, $post) {
        global $wpdb;

        if (!isset($_POST["txt-total-exercise-hours-nonce"]) || !wp_verify_nonce($_POST["txt-total-exercise-hours-nonce"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        $slug = "exercises";
        if ($slug != $post->post_type)
            return;

        /* Store the user enter value in a variable */
        $exercise_total_hour = "";
        if (isset($_POST["txt_save_exercise_hour"])) {

            $exercise_total_hour = intval($_POST["txt_save_exercise_hour"]);
        } else {
            $exercise_total_hour = "";
        }
        update_post_meta($post_id, "txt_exercise_complete_hour", $exercise_total_hour);
    }

    /* =========================================================== */
    /* function to add metabox for course type on course */
    /* =========================================================== */

    public function wpl_course_type_filter_meta_box_layout($post) {

        wp_nonce_field(basename(__FILE__), 'dropdown-course-type-nonce');
        ?>
        <p>
            <label for="dd_course_type_box_post"><?php echo __('Choose Type:'); ?></label>
            <select name='dd_course_type_box_post' id='dd_course_type_box_post' required="">
                <option value=""><?php echo __('Select type'); ?></option>
                <?php
                $saved_dd_value = get_post_meta($post->ID, "dd_course_type_box_post", true);
                $valid_course_type = array("locked", "unlocked");
                foreach ($valid_course_type as $type) {
                    $selected = '';
                    $default_selection = 'unlocked';
                    if ($saved_dd_value == $type) {
                        $selected = 'selected="selected"';
                    }
                    if (empty($saved_dd_value)) {
                        if ($default_selection == "unlocked") {
                            $selected = 'selected="selected"';
                        }
                    }
                    ?>
                    <option value="<?php echo $type; ?>" <?php echo $selected; ?>><?php echo __(ucfirst($type)); ?></option>
                    <?php
                }
                wp_reset_query();
                ?>
            </select>
        </p>
        <?php
    }

    /* =========================================================== */
    /* function for the layout of modules meta box at chapters */
    /* =========================================================== */

    public function wpl_chapter_meta_box_layout($post) {

        wp_nonce_field(basename(__FILE__), 'dropdown-chapter-nonce');
        ?>
        <p>
            <label for="dd_chapter_box_post_type"><?php echo __('Selected Chapter:'); ?> </label>
            <?php
            $postId = isset($_GET['chapter_id']) ? intval($_GET['chapter_id']) : "";
            if (empty($postId)) {
                $postId = get_post_meta($post->ID, "dd_chapter_id_box_post_type", true);
            }

            $post_data = get_post($postId);
            if (!empty($post_data)) {
                ?>
                <input type="text" readonly="" value="<?php echo $post_data->post_title; ?>"/>
                <input type='hidden' value='<?php echo $postId; ?>' name='dd_chapter_box_post_type' id='dd_chapter_box_post_type'/>
                <?php
            }
            ?>

        </p>
        <?php
    }

    /* =========================================================== */
    /* function for the layout of modules meta box at chapters */
    /* =========================================================== */

    public function wpl_module_meta_box_layout($post) {
        wp_nonce_field(basename(__FILE__), 'dropdown-module-nonce');
        ?>
        <p>
            <label for="dd_module_box_post_type"><?php echo __('Selected module: '); ?></label>
            <?php
            $postId = isset($_GET['module_id']) ? intval($_GET['module_id']) : "";
            if (empty($postId)) {
                $postId = get_post_meta($post->ID, "dd_module_id_box_post_type", true);
            }

            $post_data = get_post($postId);
            if (!empty($post_data)) {
                ?>
                <input type="text" readonly="" value="<?php echo $post_data->post_title; ?>"/>
                <input type='hidden' value='<?php echo $postId; ?>' name='dd_module_box_post_type' id='dd_module_box_post_type'/>
                <?php
            }
            ?>

        </p>
        <?php
    }

    /* =========================================================== */
    /* function for the layout of course author meta box at courses */
    /* =========================================================== */

    public function wpl_course_author_meta_box_layout($post) {
        wp_nonce_field(basename(__FILE__), 'dropdown-course-author-nonce');
        ?>
        <p>
            <label for="dd_course_author_box_post_type">Choose author: </label>
            <select name='dd_course_author_box_post_type' id='dd_course_author_box_post_type' required="">

                <option value="">Select author</option> 
                <?php
                // get author for custom user role
                $args = array(
                    'role' => 'author',
                    'orderby' => 'user_nicename',
                    'order' => 'ASC'
                );
                $training_authors = get_users($args);
                if (count($training_authors) > 0) {

                    $saved_dd_value = get_post_meta($post->ID, "dd_course_author_box_post_type", true);

                    foreach ($training_authors as $index => $author) {
                        $selected = '';
                        $author_data = $author->data;
                        if ($author_data->ID == $saved_dd_value) {
                            $selected = 'selected="selected"';
                        }
                        ?>
                        <option value="<?php echo esc_attr($author_data->ID); ?>" <?php echo $selected; ?>><?php echo esc_html($author_data->display_name); ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </p>
        <?php
    }

    /* =========================================================== */
    /* function for the layout of course meta box at modules */
    /* =========================================================== */

    public function wpl_course_meta_box_layout($post) {
        wp_nonce_field(basename(__FILE__), 'dropdown-course-nonce');
        ?>
        <p>
            <label for="dd_course_box_post_type">Selected Course: </label>
            <?php
            $postId = isset($_GET['course_id']) ? intval($_GET['course_id']) : "";
            if (empty($postId)) {
                $postId = get_post_meta($post->ID, "dd_course_box_post_type", true);
            }
            $post_data = get_post($postId);
            if (!empty($post_data)) {
                ?>
                <input type="text" readonly="" value="<?php echo $post_data->post_title; ?>"/>
                <input type='hidden' value='<?php echo $postId; ?>' name='dd_course_box_post_type' id='dd_course_box_post_type'/>
                <?php
            }
            ?>

        </p>
        <?php
    }

    /* =========================================================== */
    /* function to save course author dropdown meta value */
    /* =========================================================== */

    function wpl_dd_course_author_meta_box_save_meta($post_id, $post, $update) {

        if (!isset($_POST["dropdown-course-author-nonce"]) || !wp_verify_nonce($_POST["dropdown-course-author-nonce"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        $slug = RDTR_TRAINING_POST_TYPE;
        if ($slug != $post->post_type)
            return;

        /* Store the user enter value in a variable */
        $user_dd_course_author_value = "";
        if (isset($_POST["dd_course_author_box_post_type"])) {
            $user_dd_course_author_value = sanitize_text_field($_POST["dd_course_author_box_post_type"]);
        } else {
            $user_dd_course_author_value = "";
        }
        update_post_meta($post_id, "dd_course_author_box_post_type", $user_dd_course_author_value);
    }

    /* =========================================================== */
    /* function to save course type dropdown meta value */
    /* =========================================================== */

    function wpl_dd_course_type_meta_box_save_meta($post_id, $post, $update) {

        if (!isset($_POST["dropdown-course-type-nonce"]) || !wp_verify_nonce($_POST["dropdown-course-type-nonce"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        $slug = RDTR_TRAINING_POST_TYPE;
        if ($slug != $post->post_type)
            return;

        $user_dd_course_type_value = "";
        if (isset($_POST["dd_course_type_box_post"])) {
            $user_dd_course_type_value = sanitize_text_field($_POST["dd_course_type_box_post"]);
        } else {
            $user_dd_course_type_value = "";
        }
        update_post_meta($post_id, "dd_course_type_box_post", $user_dd_course_type_value);
    }

    /* =========================================================== */
    /* function to save course dropdown meta value for modules */
    /* =========================================================== */

    function wpl_dd_course_meta_box_save_meta($post_id, $post, $update) {

        if (!isset($_POST["dropdown-course-nonce"]) || !wp_verify_nonce($_POST["dropdown-course-nonce"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        $slug = "modules";
        if ($slug != $post->post_type)
            return;

        /* Store the user enter value in a variable */
        $user_dd_course_value = "";
        if (isset($_POST["dd_course_box_post_type"])) {
            $user_dd_course_value = sanitize_text_field($_POST["dd_course_box_post_type"]);
        } else {
            $user_dd_course_value = "";
        }
        update_post_meta($post_id, "dd_course_box_post_type", $user_dd_course_value);
    }

    /* =========================================================== */
    /* function to save dd value of module for chapters */
    /* =========================================================== */

    function wpl_dd_module_meta_box_save_meta($post_id, $post, $update) {

        if (!isset($_POST["dropdown-module-nonce"]) || !wp_verify_nonce($_POST["dropdown-module-nonce"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        $slug = "chapters";
        if ($slug != $post->post_type)
            return;

        /* Store the user enter value in a variable */
        $user_dd_module_value = "";
        if (isset($_POST["dd_module_box_post_type"])) {
            $user_dd_module_value = sanitize_text_field($_POST["dd_module_box_post_type"]);
        } else {
            $user_dd_module_value = "";
        }
        update_post_meta($post_id, "dd_module_id_box_post_type", $user_dd_module_value);
    }

    /* =========================================================== */
    /* function to save dd value of chapter for exercise */
    /* =========================================================== */

    function wpl_dd_chapter_meta_box_save_meta($post_id, $post, $update) {

        if (!isset($_POST["dropdown-chapter-nonce"]) || !wp_verify_nonce($_POST["dropdown-chapter-nonce"], basename(__FILE__)))
            return $post_id;

        if (!current_user_can("edit_post", $post_id))
            return $post_id;

        $slug = "exercises";
        if ($slug != $post->post_type)
            return;

        /* Store the user enter value in a variable */
        $user_dd_chapter_value = "";
        if (isset($_POST["dd_chapter_box_post_type"])) {
            $user_dd_chapter_value = sanitize_text_field($_POST["dd_chapter_box_post_type"]);
        } else {
            $user_dd_chapter_value = "";
        }
        update_post_meta($post_id, "dd_chapter_id_box_post_type", $user_dd_chapter_value);
    }

    function wpl_plugin_settings_callback() {
        require_once plugin_dir_path(__FILE__) . 'partials/rdtr-settings.php';
    }  

    /* =========================================================== */
    /* function to create & set custom columns order for course */
    /* =========================================================== */

    public function wpl_set_cpt_course_columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title'),
            'course_image' => __('Course Image'),
            //'course_author' => __('Author'),
            'course_type' => __('Type'),
            'total_modules' => __('Modules'),
            'manage_modules' => __('Manage Modules'),
            'view_progress' => __('Course Enrolled'),
            'comments' => '<span class="vers comment-grey-bubble" title="' . esc_attr__('Comments') . '"><span class="screen-reader-text">' . __('Comments') . '</span></span>',
            'date' => __('Date')
        );

        return $columns;
    }

    /* =========================================================== */
    /* function to create & set custom columns order for chapters */
    /* =========================================================== */

    public function wpl_set_cpt_chapters_columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title'),
            'total_exercises' => __('Total Exercise'),
            'total_hours' => __('Total Hours'),
            'manage_exercises' => 'Manage Exercises',
            'date' => __('Date')
        );

        return $columns;
    }

    /* =========================================================== */
    /* function to create & set custom columns order for exercises */
    /* =========================================================== */

    public function wpl_set_cpt_exercise_columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title'),
            'total_hours' => 'Total Hours',
            'date' => __('Date')
        );

        return $columns;
    }

    /* =========================================================== */
    /* function to create & set custom columns order for module */
    /* =========================================================== */

    public function wpl_set_cpt_module_columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title'),
            'total_chapters' => 'Total Chapters',
            'manage_chapters' => 'Manage Chapters',
            'date' => __('Date')
        );

        return $columns;
    }

    /* =========================================================== */
    /* function to pass data to custom columns of module */
    /* =========================================================== */

    public function wpl_cpt_module_column_data($column, $post_id) {
        global $wpdb;
        switch ($column) {

            case 'total_chapters' :
                $count_all_chapters = $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT count(post.ID) from " . $wpdb->posts . " post INNER JOIN $wpdb->postmeta pmeta ON pmeta.post_id = post.ID WHERE pmeta.meta_key = %s AND pmeta.meta_value = %d AND post.post_status = %s", "dd_module_id_box_post_type", $post_id, 'publish'
                        )
                );

                echo $count_all_chapters;
                break;
            case 'manage_chapters' :
                echo __('<a href="edit.php?post_type=chapters&filter_by_module=' . $post_id . '" class="button button-primary button-large">View Chapters</a>');
                break;
        }
    }

    /* =========================================================== */
    /* function to pass data to custom columns of chapters */
    /* =========================================================== */

    public function wpl_cpt_chapter_column_data($column, $post_id) {
        global $wpdb;
        switch ($column) {
            case 'manage_exercises' :
                echo __('<a href="edit.php?post_type=exercises&filter_by_chapter=' . $post_id . '" class="button button-primary button-large">View Exercises</a>');
                break;
            case 'total_hours' :
                $total_hours = $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT SUM( meta_value ) FROM $wpdb->postmeta WHERE post_id IN ( SELECT post_id FROM $wpdb->postmeta WHERE meta_key =  'dd_chapter_id_box_post_type' AND meta_value = %d ) AND meta_key =  'txt_exercise_complete_hour'", $post_id
                        )
                );
                if (isset($total_hours)) {
                    echo $total_hours;
                } else {
                    echo 0;
                }
                break;
            case 'total_exercises' :
                $count_all_exercises = $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT count(pmeta.post_id) from " . $wpdb->postmeta . " pmeta INNER JOIN $wpdb->posts post ON post.ID = pmeta.post_id WHERE pmeta.meta_key = %s AND pmeta.meta_value = %d AND post.post_status = %s", "dd_chapter_id_box_post_type", $post_id, 'publish'
                        )
                );
                echo $count_all_exercises;
                break;
        }
    }

    /* =========================================================== */
    /* function to pass data to custom columns of exercises */
    /* =========================================================== */

    public function wpl_cpt_exercises_column_data($column, $post_id) {
        switch ($column) {
            case 'total_hours' :
                $hours = get_post_meta($post_id, "txt_exercise_complete_hour", true);
                echo!empty($hours) ? $hours : "--";
                break;
        }
    }

    /* =========================================================== */
    /* function to pass data to custom columns of course */
    /* =========================================================== */

    public function wpl_cpt_course_column_data($column, $post_id) {
        global $wpdb;
        switch ($column) {

            case 'manage_modules' :
                echo __('<a href="edit.php?post_type=modules&filter_by_course=' . $post_id . '" class="button button-primary button-large">View Modules</a>');
                break;

            case 'course_author':
                $author_id = get_post_meta($post_id, "dd_course_author_box_post_type", true);
                if (empty($author_id)) {
                    echo "--";
                } else {
                    $author_obj = get_user_by('id', $author_id);
                    echo $author_obj->display_name;
                }
                break;
            case 'course_image':
                $url = wp_get_attachment_url(get_post_thumbnail_id($post_id));

                $setting_image = RDTR_TRAINING_PLUGIN_URL . "assets/images/no-image.png";
                $image = $setting_image;
                if (@getimagesize(!empty($url) ? $url : $image)) {
                    $setting_image = $url;
                }
                if (empty($setting_image)) {
                    //course default image
                    // $default_image = get_option("wpl_training_course_image");

                    echo '<img src="' . $image . '" class="wptr-rud-img course-image-size"/>';
                } else {
                    echo '<img src="' . $setting_image . '" class="wptr-rud-img course-image-size"/>';
                }
                break;
            case 'total_modules':
                $count_all_courses = $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT count(pmeta.post_id) from " . $wpdb->postmeta . " pmeta INNER JOIN $wpdb->posts post ON post.ID = pmeta.post_id WHERE post.post_status = 'publish' AND pmeta.meta_key = %s AND pmeta.meta_value = %d", "dd_course_box_post_type", $post_id
                        )
                );
                echo $count_all_courses;
                break;
            case 'view_progress':
                $total_course_enrolled = $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT COUNT( user_id ) FROM wp_rd_user_course_enroll WHERE course_post_id = %d", $post_id
                        )
                );
                echo __('<a href="admin.php?page=user-progress&c=' . $post_id . '" class="button button-default button-large">View Enrolled (' . $total_course_enrolled . ')</a>');
                break;
            case 'course_type':

                $get_course_type = ucfirst(get_post_meta($post_id, "dd_course_type_box_post", true));
                if (empty($get_course_type)) {
                    $get_course_type = "--";
                }
                echo $get_course_type;
                break;
        }
    }

    /* =========================================================== */
    /* function to make modules custom columns sortable */
    /* =========================================================== */

    public function wpl_cpt_modules_sortable_columns($columns) {

        $columns['course'] = 'course';

        return $columns;
    }

    /* =========================================================== */
    /* function to register custom post types */
    /* =========================================================== */

    public function wpl_register_training_cpt() {

        /* =========================================================== */
        /* REGISTER CPT FORM COURSES */
        /* =========================================================== */

        $course_labels = array(
            'name' => __('Courses'),
            'singular_name' => __('Course'),
            'menu_name' => __('Courses'),
            'parent_item_colon' => __('Parent Course'),
            'all_items' => __('Courses'),
            'view_item' => __('View Course'),
            'add_new_item' => __('Add New Course'),
            'add_new' => __('Add New'),
            'edit_item' => __('Edit Course'),
            'update_item' => __('Update Course'),
            'search_items' => __('Search Course'),
            'not_found' => __('Not Found'),
            'not_found_in_trash' => __('Not found in Trash'),
        );

        register_post_type(RDTR_TRAINING_POST_TYPE, array(
            'labels' => $course_labels,
            'supports' => array('title', 'thumbnail', 'editor', 'comments'),
            'taxonomies' => array('training_course_category'),
            'hierarchical' => true,
            'show_ui' => true,
            'can_export' => true,
            'has_archive' => true,
            'public' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_menu' => RDTR_TRAINING_POST_TYPE
                )
        );

        /* =========================================================== */
        /* REGISTER CPT FORM MODULES */
        /* =========================================================== */

        $labels = array(
            'name' => __('Modules'),
            'singular_name' => __('Module'),
            'menu_name' => __('Modules'),
            'parent_item_colon' => __('Parent Module'),
            'all_items' => __('Modules'),
            'view_item' => __('View Module'),
            'add_new_item' => __('Add New Module'),
            'add_new' => __('Add New'),
            'edit_item' => __('Edit Module'),
            'update_item' => __('Update Module'),
            'search_items' => __('Search Module'),
            'not_found' => __('Not Found'),
            'not_found_in_trash' => __('Not found in Trash'),
        );

        $args = array(
            'labels' => $labels,
            'supports' => array('title', 'editor'),
            'taxonomies' => array('training_course_category'),
            'hierarchical' => true,
            'show_ui' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_menu' => RDTR_TRAINING_POST_TYPE
        );

        register_post_type('modules', $args);

        /* =========================================================== */
        /* REGISTER CPT FORM CHAPTERS */
        /* =========================================================== */

        $labels = array(
            'name' => __('Chapters'),
            'singular_name' => __('Chapter'),
            'menu_name' => __('Chapters'),
            'parent_item_colon' => __('Parent Chapter'),
            'all_items' => __('Chapters'),
            'view_item' => __('View Chapter'),
            'add_new_item' => __('Add New Chapter'),
            'add_new' => __('Add New'),
            'edit_item' => __('Edit Chapter'),
            'update_item' => __('Update Chapter'),
            'search_items' => __('Search Chapter'),
            'not_found' => __('Not Found'),
            'not_found_in_trash' => __('Not found in Trash'),
        );

        $args = array(
            'labels' => $labels,
            'supports' => array('title', 'editor'),
            'taxonomies' => array('training_course_category'),
            'hierarchical' => true,
            'show_ui' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_menu' => RDTR_TRAINING_POST_TYPE
        );

        register_post_type('chapters', $args);

        /* =========================================================== */
        /* REGISTER CPT FORM EXERCISES */
        /* =========================================================== */

        $labels = array(
            'name' => __('Exercises'),
            'singular_name' => __('Exercise'),
            'menu_name' => __('Exercises'),
            'parent_item_colon' => __('Parent Exercise'),
            'all_items' => __('Exercises'),
            'view_item' => __('View Exercise'),
            'add_new_item' => __('Add New Exercise'),
            'add_new' => __('Add New'),
            'edit_item' => __('Edit Exercise'),
            'update_item' => __('Update Exercise'),
            'search_items' => __('Search Exercise'),
            'not_found' => __('Not Found'),
            'not_found_in_trash' => __('Not found in Trash'),
        );

        $args = array(
            'labels' => $labels,
            'supports' => array('title'),
            'taxonomies' => array('training_course_category'),
            'hierarchical' => false,
            'show_ui' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_menu' => RDTR_TRAINING_POST_TYPE
        );

        register_post_type('exercises', $args);
    }

    // show breadcrumb
    public function wpl_top_show_breadcrumbs() {

        $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : "";
        $post = isset($_GET['post']) ? sanitize_text_field($_GET['post']) : "";
        $space = '&nbsp;&nbsp;';
        $marker = '&#187;';
        $action = '';
        $status = 0;
        if (!empty($post)) {
            $status = 1;
            $action = "edit";
            $post_detail = get_post($post);
            $post_type = $post_detail->post_type;
        }
        if (!empty($post_type)) {
            if ($post_type == "training") {
                
            } elseif ($post_type == "modules") { // for the section module
                $course_name = '';
                $module_name = '';

                $url_course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : "";
                if (!empty($url_course_id)) {
                    $action = "add";
                    $status = 1;
                    $course_data = get_post($url_course_id);
                    $course_name = ucfirst($course_data->post_title);
                }

                if ($action == "edit") {
                    $module_data = get_post($post);
                    $module_name = ucfirst($module_data->post_title);
                    $course_id = get_post_meta($post, "dd_course_box_post_type", true);
                    $course_data = get_post($course_id);
                    $course_name = ucfirst($course_data->post_title);
                }

                if ($status) {
                    ?>
                    <div class="wptr-rud-notice notice notice-success wpl-crumb">
                        <p>
                            <a href='edit.php?post_type=training'><?php echo $course_name; ?></a><?php echo $space . $marker . $space; ?>
                            <?php
                            if ($action == "add") {
                                ?>
                                <a href='edit.php?post_type=modules&filter_by_course=<?php echo $url_course_id; ?>'>Modules</a><?php echo $space . $marker . $space; ?>
                                <?php
                            } elseif ($action == "edit") {
                                $course_id = get_post_meta($post, "dd_course_box_post_type", true);
                                ?>
                                <a href='edit.php?post_type=modules&filter_by_course=<?php echo $course_id; ?>'><?php echo $module_name; ?></a> <?php echo $space . $marker . $space; ?>
                                <?php
                            }
                            ?>

                            <?php echo ucfirst($action); ?> Module
                        </p>
                    </div>
                    <?php
                } else {
                    $course_id = isset($_REQUEST['filter_by_course']) ? intval(esc_attr($_REQUEST['filter_by_course'])) : "";
                    $course_data = get_post($course_id);
                    $course_name = ucfirst($course_data->post_title);
                    ?>
                    <div class="wptr-rud-notice notice notice-success wpl-crumb">
                        <p>
                            <a href='edit.php?post_type=training'><?php echo $course_name; ?></a><?php echo $space . $marker . $space; ?>
                            Modules
                        </p>
                    </div>
                    <?php
                }
            } elseif ($post_type == "chapters") { // for the section of chapters
                $filter_by_module = isset($_GET['filter_by_module']) ? intval($_GET['filter_by_module']) : "";
                $course_id = get_post_meta($filter_by_module, "dd_course_box_post_type", true);
                $course_name = '';
                $module_name = '';

                $url_module_id = isset($_GET['module_id']) ? intval($_GET['module_id']) : "";
                if (!empty($url_module_id)) {
                    $action = "add";
                    $status = 1;
                    $course_id = get_post_meta($url_module_id, "dd_course_box_post_type", true);
                    $course_data = get_post($course_id);
                    $course_name = ucfirst($course_data->post_title);

                    $module_data = get_post($url_module_id);
                    $module_name = ucfirst($module_data->post_title);
                }

                if ($action == "edit") {

                    $chapter_data = get_post($post);
                    $chapter_name = ucfirst($chapter_data->post_title);
                    $module_id = get_post_meta($post, "dd_module_id_box_post_type", true);
                    $course_id = get_post_meta($module_id, "dd_course_box_post_type", true);
                    $course_data = get_post($course_id);
                    $course_name = ucfirst($course_data->post_title);

                    $module_data = get_post($module_id);
                    $module_name = ucfirst($module_data->post_title);
                }

                if ($status) {
                    ?>
                    <div class="wptr-rud-notice notice notice-success wpl-crumb">
                        <p>
                            <a href='edit.php?post_type=training'><?php echo $course_name; ?></a><?php echo $space . $marker . $space; ?>
                            <a href='edit.php?post_type=modules&filter_by_course=<?php echo $course_id; ?>'><?php echo $module_name; ?></a><?php echo $space . $marker . $space; ?>
                            <?php
                            if ($action == "add") {
                                ?>
                                <a href='edit.php?post_type=chapters&filter_by_module=<?php echo $url_module_id; ?>'>Chapters</a><?php echo $space . $marker . $space; ?>
                                <?php
                            } elseif ($action == "edit") {

                                $module_id = get_post_meta($post, "dd_module_id_box_post_type", true);
                                $course_id = get_post_meta($module_id, "dd_course_box_post_type", true);
                                $course_data = get_post($course_id);
                                $course_name = ucfirst($course_data->post_title);

                                $module_data = get_post($module_id);
                                $module_name = ucfirst($module_data->post_title);
                                ?>
                                <a href='edit.php?post_type=chapters&filter_by_module=<?php echo $module_id; ?>'><?php echo $chapter_name; ?></a><?php echo $space . $marker . $space; ?>
                                <?php
                            }
                            ?>

                            <?php echo ucfirst($action); ?> Chapter
                        </p>
                    </div>
                    <?php
                } else {
                    $course_data = get_post($course_id);
                    $course_name = ucfirst($course_data->post_title);

                    $module_data = get_post($filter_by_module);
                    $module_name = ucfirst($module_data->post_title);
                    ?>
                    <div class="wptr-rud-notice notice notice-success wpl-crumb">
                        <p>
                            <a href='edit.php?post_type=training'><?php echo $course_name; ?></a><?php echo $space . $marker . $space; ?>
                            <a href='edit.php?post_type=modules&filter_by_course=<?php echo $course_id; ?>'><?php echo $module_name; ?></a><?php echo $space . $marker . $space; ?>
                            Chapters
                        </p>
                    </div>
                    <?php
                }
            } elseif ($post_type == "exercises") { // for the section of exercise
                $filter_by_chapter = isset($_GET['filter_by_chapter']) ? intval($_GET['filter_by_chapter']) : "";
                $module_id = get_post_meta($filter_by_chapter, "dd_module_id_box_post_type", true);
                $course_id = get_post_meta($module_id, "dd_course_box_post_type", true);
                $course_name = '';
                $module_name = '';
                $exercise_name = '';
                $course_name = '';
                $url_chapter_id = isset($_GET['chapter_id']) ? intval($_GET['chapter_id']) : "";
                if (!empty($url_chapter_id)) {
                    $action = "add";
                    $status = 1;

                    $chapter_data = get_post($url_chapter_id);
                    $chapter_name = ucfirst($chapter_data->post_title);

                    $module_id = get_post_meta($url_chapter_id, "dd_module_id_box_post_type", true);
                    $course_id = get_post_meta($module_id, "dd_course_box_post_type", true);
                    $course_data = get_post($course_id);
                    $course_name = ucfirst($course_data->post_title);

                    $module_data = get_post($module_id);
                    $module_name = ucfirst($module_data->post_title);
                }

                if ($action == "edit") {

                    $exercise_data = get_post($post);
                    $exercise_name = ucfirst($exercise_data->post_title);

                    $chapter_id = get_post_meta($post, "dd_chapter_id_box_post_type", true);
                    $chapter_data = get_post($chapter_id);
                    $chapter_name = ucfirst($chapter_data->post_title);

                    $module_id = get_post_meta($chapter_id, "dd_module_id_box_post_type", true);
                    $course_id = get_post_meta($module_id, "dd_course_box_post_type", true);
                    $course_data = get_post($course_id);
                    $course_name = ucfirst($course_data->post_title);

                    $module_data = get_post($module_id);
                    $module_name = ucfirst($module_data->post_title);
                }

                if ($status) {
                    ?>
                    <div class="wptr-rud-notice notice notice-success wpl-crumb">
                        <p>
                            <a href='edit.php?post_type=training'><?php echo $course_name; ?></a><?php echo $space . $marker . $space; ?>
                            <a href='edit.php?post_type=modules&filter_by_course=<?php echo $course_id; ?>'><?php echo $module_name; ?></a><?php echo $space . $marker . $space; ?>
                            <a href='edit.php?post_type=chapters&filter_by_module=<?php echo $module_id; ?>'><?php echo $chapter_name; ?></a><?php echo $space . $marker . $space; ?>
                            <?php
                            if ($action == "add") {
                                ?>
                                <a href='edit.php?post_type=exercises&filter_by_chapter=<?php echo $url_chapter_id; ?>'>Exercise</a><?php echo $space . $marker . $space; ?>
                                <?php
                            } elseif ($action == "edit") {
                                $chapter_id = get_post_meta($post, 'dd_chapter_id_box_post_type', true);
                                ?>
                                <a href='edit.php?post_type=exercises&filter_by_chapter=<?php echo $chapter_id; ?>'><?php echo $exercise_name; ?></a><?php echo $space . $marker . $space; ?>
                                <?php
                            }
                            ?>
                            <?php echo ucfirst($action); ?> Exercise
                        </p>
                    </div>
                    <?php
                } else {
                    $course_data = get_post($course_id);
                    $course_name = ucfirst($course_data->post_title);

                    $module_data = get_post($module_id);
                    $module_name = ucfirst($module_data->post_title);

                    $chapter_data = get_post($filter_by_chapter);
                    $chapter_name = ucfirst($chapter_data->post_title);
                    ?>
                    <div class="wptr-rud-notice notice notice-success wpl-crumb">
                        <p>
                            <a href='edit.php?post_type=training'><?php echo $course_name; ?></a><?php echo $space . $marker . $space; ?>
                            <a href='edit.php?post_type=modules&filter_by_course=<?php echo $course_id; ?>'><?php echo $module_name; ?></a><?php echo $space . $marker . $space; ?>
                            <a href='edit.php?post_type=chapters&filter_by_module=<?php echo $module_id; ?>'><?php echo $chapter_name; ?></a><?php echo $space . $marker . $space; ?>
                            Exercise
                        </p>
                    </div>
                    <?php
                }
            }
        }
    }

    /* =========================================================== */
    /* function to open training menu for custom taxonomy */
    /* =========================================================== */

    public function wpl_custom_taxonomy_menu_highlight($parent_file) {
        global $current_screen;

        $module = isset($current_screen->post_type) ? $current_screen->post_type : "";

        if ($module == "modules" || $module == "exercises") {
            $parent_file = "training";
        }

        return $parent_file;
    }

    /* =========================================================== */
    /* function to create custom taxonomy i.e course category */
    /* =========================================================== */

    public function wpl_create_course_category_taxonomies() {

        $labels = array(
            'name' => __('Course Category'),
            'singular_name' => __('Category'),
            'search_items' => __('Search Course Category'),
            'all_items' => __('All Course Category'),
            'parent_item' => __('Parent Category'),
            'parent_item_colon' => __('Parent Category:'),
            'edit_item' => __('Edit Category'),
            'update_item' => __('Update Category'),
            'add_new_item' => __('Add New Category'),
            'new_item_name' => __('New Category Name'),
            'menu_name' => __('Category'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'training_course_category'),
        );

        //register_taxonomy('training_course_category', array(RDTR_TRAINING_POST_TYPE), $args);
    }

    // adding script variables at head tag
    public function wpl_scripts_at_head() {
        ?>
        <script>
            var paraCount = 0;
            var fileCount = 0;
            var mcqCount = 0;
            var singleCount = 0;
            var pollCount = 0;
            var reflectionCount = 0;
        </script>
        <?php
    }

    /* =========================================================== */
    /* function to create course filter by category taxonomy */
    /* =========================================================== */

    public function wpl_filter_course_by_author() {
        global $typenow;
        $post_type = RDTR_TRAINING_POST_TYPE;
        $taxonomy = 'training_course_category';
        if ($typenow == $post_type) {
            //author
            $course_author_url_slug = "course_author_user";
            $selected_author = isset($_GET[$course_author_url_slug]) ? esc_attr($_GET[$course_author_url_slug]) : '';
            wp_dropdown_users(array(
                'show_option_all' => __("Show All Authors"),
                'show' => 'display_name',
                'name' => $course_author_url_slug, // string
                'show_option_none' => null, // string
                'selected' => $selected_author,
                'role' => 'authors'
            ));
        };
    }

    /* =========================================================== */
    /* function to make custom query for course filter, helper of wpl_filter_course_by_author() */
    /* =========================================================== */

    public function wpl_convert_course_id_to_term_in_query($query) {
        global $pagenow;
        $post_type = RDTR_TRAINING_POST_TYPE;
        $taxonomy = 'training_course_category';
        $q_vars = &$query->query_vars;
        if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    }

    /* =========================================================== */
    /* function to make custom query for course author filter */
    /* =========================================================== */

    public function wpl_convert_course_author_id_filter_query($query) {
        global $typenow;
        global $pagenow;
        $post_type = RDTR_TRAINING_POST_TYPE;
        $author_slug_name = 'course_author_user';
        $by_author = isset($_GET[$author_slug_name]) ? esc_attr($_GET[$author_slug_name]) : "";
        if ($pagenow == 'edit.php' && $typenow == RDTR_TRAINING_POST_TYPE && !empty($by_author)) {
            $query->query_vars['meta_key'] = 'dd_course_author_box_post_type';
            $query->query_vars['meta_value'] = (int) $by_author;
        }
    }

    /* =========================================================== */
    /* function to create course filter to module CPT */
    /* =========================================================== */

    public function wpl_add_course_filter_to_module() {
        global $typenow;
        if ($typenow == "modules") {
            $selected = isset($_GET['filter_by_course']) ? esc_attr($_GET['filter_by_course']) : '';
            wp_dropdown_pages(array(
                'id' => 'course-dropdown',
                "selected" => $selected,
                "name" => "filter_by_course",
                "show_option_none" => __("Show All Courses"),
                'post_type' => RDTR_TRAINING_POST_TYPE
            ));
        }
    }

    /* =========================================================== */
    /* function to create query for module, chapters section by course filter */
    /* =========================================================== */

    public function wpl_modify_course_filter_query($query) {
        global $typenow;
        global $pagenow;
        global $wpdb;
        $by_course = isset($_GET['filter_by_course']) ? esc_attr($_GET['filter_by_course']) : "";
        if ($pagenow == 'edit.php' && $typenow == "modules" && !empty($by_course)) {
            $query->query_vars['meta_key'] = 'dd_course_box_post_type';
            $query->query_vars['meta_value'] = (int) $by_course;
        } elseif ($pagenow == 'edit.php' && $typenow == "chapters" && !empty($by_course)) {

            $course_id = $by_course;
            $get_all_chapters = $wpdb->get_row(
                    $wpdb->prepare(
                            "SELECT group_concat(post_id ) AS chapter_ids FROM $wpdb->postmeta WHERE meta_key =  'dd_module_id_box_post_type' AND meta_value IN ( SELECT post_id FROM $wpdb->postmeta WHERE meta_key =  'dd_course_box_post_type' AND meta_value = %d )", $course_id
                    )
            );
            $ids_array = array();
            if (isset($get_all_chapters->chapter_ids)) {
                $ids_array = explode(",", $get_all_chapters->chapter_ids);
            }

            if (!empty($ids_array)) {
                $query->query_vars['post__in'] = $ids_array;
            } else {
                $query->query_vars['post__in'] = array('');
            }
        }
    }

    /* =========================================================== */
    /* function to create query for search chapter by module */
    /* =========================================================== */

    public function wpl_modify_module_filter_query_for_chapters($query) {
        global $typenow;
        global $pagenow;
        $bymodule = isset($_GET['filter_by_module']) ? esc_attr($_GET['filter_by_module']) : "";
        if ($pagenow == 'edit.php' && $typenow == "chapters" && !empty($bymodule)) {

            $query->query_vars['meta_key'] = 'dd_module_id_box_post_type';
            $query->query_vars['meta_value'] = (int) $bymodule;
        }
    }

    /* =========================================================== */
    /* function to create query for module section by course,module,exercise filter */
    /* =========================================================== */

    public function wpl_do_exercise_filter_query($query) {
        global $typenow;
        global $pagenow;
        global $wpdb;
        $by_chapter = isset($_GET['filter_by_chapter']) ? esc_attr($_GET['filter_by_chapter']) : "";
        if ($pagenow == 'edit.php' && $typenow == "exercises" && !empty($by_chapter)) {
            $query->query_vars['meta_key'] = 'dd_chapter_id_box_post_type';
            $query->query_vars['meta_value'] = (int) $by_chapter;
        }
    }

    /* =========================================================== */
    /* function to create back button for Module CPT */
    /* =========================================================== */

    public function wpl_back_to_courses($views) {
        $views['back-button'] = '<a href="edit.php?post_type=training"><span class="wptr-rud-back-btn arrow-back-admin-icon">»</span>Back to Courses</a>';
        return $views;
    }

    /* =========================================================== */
    /* function to create back button for Chapter CPT */
    /* =========================================================== */

    public function wpl_back_to_module($views) {
        $module_id = isset($_GET['filter_by_module']) ? intval($_GET['filter_by_module']) : "";

        if (!empty($module_id)) {
            $course_id = get_post_meta($module_id, "dd_course_box_post_type", true);
            $views['back-button'] = '<a href="edit.php?post_type=modules&filter_by_course=' . $course_id . '"><span class="wptr-rud-back-btn arrow-back-admin-icon">»</span>Back to Modules</a>';
            return $views;
        }
    }

    /* =========================================================== */
    /* function to create back button for Exercise CPT */
    /* =========================================================== */

    public function wpl_back_to_chapter($views) {
        $chapter_id = isset($_GET['filter_by_chapter']) ? intval($_GET['filter_by_chapter']) : "";

        if (!empty($chapter_id)) {
            $module_id = get_post_meta($chapter_id, "dd_module_id_box_post_type", true);
            $views['back-button'] = '<a href="edit.php?post_type=chapters&filter_by_module=' . $module_id . '"><span class="wptr-rud-back-btn arrow-back-admin-icon">»</span>Back to Chapters</a>';
            return $views;
        }
    }

    /* =========================================================== */
    /* function to handle ajax request */
    /* =========================================================== */

    public function wpl_ajax_handler() {
        global $wpdb;
        $param = isset($_REQUEST['param']) ? esc_attr($_REQUEST['param']) : "";
        if (!empty($param)) {

            if ($param == "exercise_sort") {

                $this->wpl_sort_cpt_table_list();
            } elseif ($param == "training_settings") {

                // code to make training settings
                $course_image = isset($_REQUEST['course_img']) ? esc_attr(trim($_REQUEST['course_img'])) : "";
                $my_course_page = isset($_REQUEST['dd-my-course-page']) ? intval($_REQUEST['dd-my-course-page']) : "";
                $all_courses_page = isset($_REQUEST['dd-all-courses-page']) ? intval($_REQUEST['dd-all-courses-page']) : "";
                $disable_all_courses_rating = isset($_REQUEST['want_chk_rating']) ? intval($_REQUEST['want_chk_rating']) : 0;

                update_option("training_disable_comments", $disable_all_courses_rating);

                if ($my_course_page < 1) {
                    $this->json(0, "Please select my course page");
                }

                update_option("rdtr_training_mycourse_page", $my_course_page);
                update_option("rdtr_training_allcourses_page", $all_courses_page);

                if (!empty($course_image)) {
                    if (preg_match('/\.(jpeg|jpg|png|gif)$/i', $course_image)) {

                        update_option("wpl_training_course_image", $course_image);
                        $this->json(1, "Default image updated successfully");
                    } else {
                        $this->json(0, "Invalid file (allowed files:  '.jpeg', '.jpg', '.png', or '.gif')", array("image" => RDTR_COURSE_DEFAULT_IMAGE));
                    }
                } else {
                    $this->json(1, "Settings updated");
                }
            } elseif ($param == "wpl_get_course_resouces") {

                $res_type = isset($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : "";
                $course_id = isset($_REQUEST['cid']) ? sanitize_text_field($_REQUEST['cid']) : "";
                $user_id = isset($_REQUEST['uid']) ? sanitize_text_field($_REQUEST['uid']) : "";

                $resource_detail = $wpdb->get_results(
                        $wpdb->prepare(
                                "SELECT group_concat(file) as files,chapter_id from " . $this->table_activator->wpl_rd_user_uploaded_files_tbl() . " WHERE user_id = %d GROUP BY chapter_id", $user_id
                        ), ARRAY_A
                );

                $cr_id = 0;
                $u_files = array();
                if (!empty($resource_detail)) {
                    foreach ($resource_detail as $inx => $stx) {
                        $course_id_by_ch = $wpdb->get_var(
                                "SELECT meta_value from $wpdb->postmeta WHERE post_id = (SELECT meta_value from $wpdb->postmeta WHERE post_id = " . $stx['chapter_id'] . " AND meta_key = 'dd_module_id_box_post_type' ) AND meta_key = 'dd_course_box_post_type'"
                        );

                        if ($course_id_by_ch == $course_id) {
                            $cr_id = 1;
                            $fls = isset($stx['files']) ? $stx['files'] : '';
                            $u_files = explode(",", $fls);
                        }
                    }
                }

                if (count($u_files) > 0) {

                    $filesArray = array();
                    $countFiles = 0;
                    foreach ($u_files as $inx => $file) {
                        if (!empty($file)) {
                            $filesArray[] = $file;
                            $countFiles++;
                        }
                    }

                    if ($countFiles > 0) {
                        $this->json(1, 'files found', array("file" => $filesArray));
                    } else {
                        $this->json(0, "No files found");
                    }
                } else {

                    $this->json(0, "No files found");
                }
            } elseif ($param == "wpl_get_exercise_resouces") {

                $exercise_id = isset($_REQUEST['exe_id']) ? intval($_REQUEST['exe_id']) : "";
                $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : "";

                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . '/admin/partials/rdtr-render-exercise-status.php';
                $template = ob_get_contents();
                ob_end_clean();

                $this->json(1, "exercise status", array("template" => $template));
            } elseif ($param == "show_chapter_uploads") {

                $chapter_id = isset($_REQUEST['chapter_id']) ? intval($_REQUEST['chapter_id']) : "";
                $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : "";
                global $wpdb;

                $total_uploads = $wpdb->get_results(
                        $wpdb->prepare(
                                "SELECT file from " . $this->table_activator->wpl_rd_user_uploaded_files_tbl() . " WHERE user_id = %d AND chapter_id = %d AND status = %d", $user_id, $chapter_id, 1
                        )
                );
                $uploads = array();
                if (!empty($total_uploads) && count($total_uploads) > 0) {

                    foreach ($total_uploads as $index => $file) {
                        $uploads[] = $file->file;
                    }

                    $this->json(1, "Upload found", array("files" => $uploads));
                }
                $this->json(0, "No upload found");
            } elseif ($param == "check_banner_image_resolution") {

                $image_url = isset($_REQUEST['image_url']) ? esc_attr($_REQUEST['image_url']) : "";

                list($width, $height) = getimagesize($image_url);
                    
                // if ($width == "1300" && $height == "350") {
                if ($width == "1200" && $height == "350") {
                    $this->json(1, "Valid image");
                } else {
                    $this->json(0, "Invalid Banner Size. It should be of (height x width): 1300px x 350px");
                }
            } elseif ($param == "wpl_rdtr_import_sample_course_data") {

                global $wpdb;

                //create test author
                $userdata = array(
                    'user_login' => 'sample_test_user',
                    'display_name' => 'Sample Test User',
                    "user_email" => "sample_test@test.com"
                );

                $email = 'sample_test@test.com';
                $exists = email_exists($email);
                if ($exists) {  // sample user already created
                    $user_data = get_user_by("email", $email);
                    $test_author_user_id = $user_data->ID;
                } else {
                    // creating sample user
                    $test_author_user_id = wp_insert_user($userdata);
                    $sample_author = new WP_User($test_author_user_id);
                    $sample_author->add_role('author');
                }

                //create course: 1
                $get_lorrem_ipsum_dummy_content = "The hooklift body manufactured the lifted tailboard! Once the cutaway van decelerated the 4WD! The upfitted railcar was trucked by the wrecker body. The stripped chassis trucked the bio-fuel upfitted cargo van. The shock-resistant ignition was deconstructed by the van but the 4WD AWD was constructed by the crane!

                                                    Once the chassis constructed the Nissan? The 4x4, lifted Ram 2500 accelerated when the platform body demolished the galvanized wheel. The lorry throttled the four wheel drive driver when once the dump body totaled the trailer. The flex fuel manufactured the lifted NRR. The mechanical vehicle was trucked by the rollback body and the body length manufactured the lifted garbage truck.

                                                    The mechanical dry freight was fixed by the NPR-XD. The heavy duty axle was totaled by the biodiesel. Once the upfitted cargo van decelerated the E-450. The hauler body fixed the vocational hand truck. The galvanized, 4WD cutaway dumped and once the dry freight trucked the hauler body.

                                                    Once the chipper body braked the Ford? The stabilizer bar decelerated the aluminum crane. Once the tow hook deconstructed the service utility van when the steel welder body was upfitted by the wrecker body.

                                                    Once the axle dumped the lorry! Once the cargo crashed the cylinder. The tow truck constructed the bio-fuel Transit 350. Once the wrecker body manufactured the service utility van. The Canyon manufactured the bio-fuel cab chassis.";

                $sample_post = array(
                    'post_title' => "Training Sample Course",
                    'post_content' => $get_lorrem_ipsum_dummy_content,
                    'post_status' => 'publish',
                    "post_type" => "training",
                    'post_author' => 1,
                );

                // Insert the post into the database        
                wp_insert_post($sample_post);

                $created_course_id = $wpdb->insert_id;

                // $filename should be the path to a file in the upload directory.
                $filename = RDTR_TRAINING_PLUGIN_URL . "assets/images/sample-image.jpeg";

                // Add Featured Image to Post
                $image_url = $filename;
                $image_name = 'sample-image.jpeg';
                $upload_dir = wp_upload_dir(); // Set upload folder
                $image_data = file_get_contents($image_url); // Get image data
                $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name); // Generate unique name
                $filename = basename($unique_file_name); // Create image file name
                // Check folder permission and define file location
                if (wp_mkdir_p($upload_dir['path'])) {
                    $file = $upload_dir['path'] . '/' . $filename;
                } else {
                    $file = $upload_dir['basedir'] . '/' . $filename;
                }

                // Create the image  file on the server
                file_put_contents($file, $image_data);

                // Check image file type
                $wp_filetype = wp_check_filetype($filename, null);

                // Set attachment data
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name($filename),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                // Create the attachment
                $attach_id = wp_insert_attachment($attachment, $file, $created_course_id);

                // Include image.php
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                // Define attachment metadata
                $attach_data = wp_generate_attachment_metadata($attach_id, $file);

                // Assign metadata to attachment
                wp_update_attachment_metadata($attach_id, $attach_data);

                // And finally assign featured image to post
                set_post_thumbnail($created_course_id, $attach_id);

                add_post_meta($created_course_id, "dd_course_author_box_post_type", $test_author_user_id);
                add_post_meta($created_course_id, "dd_course_type_box_post", "unlocked");

                //create module
                $created_modules = array();

                for ($module = 1; $module <= 5; $module++) {

                    $sample_post = array(
                        'post_title' => "Sample Module " . $module,
                        'post_content' => $get_lorrem_ipsum_dummy_content,
                        'post_status' => 'publish',
                        "post_type" => "modules",
                        'post_author' => 1,
                    );

                    // Insert the post into the database        
                    wp_insert_post($sample_post);

                    $created_module_id = $wpdb->insert_id;

                    add_post_meta($created_module_id, "dd_course_box_post_type", $created_course_id);

                    $created_modules[] = $created_module_id;
                }

                if (!empty($created_modules)) {

                    // create chapters
                    foreach ($created_modules as $module_id) {

                        $created_chapters = array();

                        for ($chapters = 1; $chapters <= 5; $chapters++) {

                            $sample_post = array(
                                'post_title' => "Sample Chapter " . $chapters,
                                'post_content' => $get_lorrem_ipsum_dummy_content,
                                'post_status' => 'publish',
                                "post_type" => "chapters",
                                'post_author' => 1,
                            );

                            // Insert the post into the database        
                            wp_insert_post($sample_post);

                            $created_chapter_id = $wpdb->insert_id;

                            add_post_meta($created_chapter_id, "dd_module_id_box_post_type", $module_id);

                            $created_chapters[] = $created_chapter_id;
                        }

                        //create exercise

                        if (count($created_chapters) > 0) {

                            foreach ($created_chapters as $chapter_id) {

                                $created_exercises = array();

                                for ($exercise = 1; $exercise <= 5; $exercise++) {

                                    $sample_post = array(
                                        'post_title' => "Sample Exercise " . $exercise,
                                        'post_content' => $get_lorrem_ipsum_dummy_content,
                                        'post_status' => 'publish',
                                        "post_type" => "exercises",
                                        'post_author' => 1,
                                    );

                                    // Insert the post into the database        
                                    wp_insert_post($sample_post);

                                    $created_exercise_id = $wpdb->insert_id;

                                    add_post_meta($created_exercise_id, "dd_chapter_id_box_post_type", $chapter_id);

                                    // exercise sequence
                                    $execise_order = array(
                                        'ex_para_1_1',
                                        'ex_file_1_1_3',
                                        'ex_mcq_1_3',
                                        'ex_single_1_5',
                                        //'ex_poll_type_1_6',
                                        'ex_reflection_1_7',
                                    );

                                    shuffle($execise_order);

                                    foreach ($execise_order as $seq_order) {

                                        $wpdb->insert($this->table_activator->wpl_rd_course_exercise_sequence(), array(
                                            "course_id" => $created_course_id,
                                            "exercise_id" => $created_exercise_id,
                                            "sequence_number" => $seq_order,
                                        ));
                                    }

                                    $paragraph = str_shuffle("The hooklift body manufactured the lifted tailboard! Once the cutaway van decelerated the 4WD! The upfitted railcar was trucked by the wrecker body. The stripped chassis trucked the bio-fuel upfitted cargo van. The shock-resistant ignition was deconstructed by the van but the 4WD AWD was constructed by the crane!");

                                    $exercise_json_data = array(
                                        'order' => $execise_order,
                                        'section' => array(
                                            'paragraph' => array(
                                                'ex_para_1_1' => array(
                                                    'name' => 'ex_para_1',
                                                    'order' => 'ex_para_1_1',
                                                    'value' => $paragraph,
                                                ),
                                            ),
                                            'file' => array(
                                                'ex_file_1_1_3' => array(
                                                    'name' => 'ex_file_1_1',
                                                    'order' => 'ex_file_1_1_3',
                                                    'value' => RDTR_TRAINING_PLUGIN_URL . 'assets/images/sample-image.jpeg',
                                                    'type' => 'gallery',
                                                ),
                                            ),
                                            'mcq' => array(
                                                'ex_mcq_1_3' => array(
                                                    'id' => 'ex_mcq_1',
                                                    'order' => 'ex_mcq_1_3',
                                                    'answer' => '1',
                                                    'question' => 'Which of the following gases can be used for storage of fresh sample of an oil for a long time?',
                                                    'options' => array(
                                                        'Carbon dioxide or oxygen',
                                                        'Nitrogen or helium',
                                                        'Helium or oxygen',
                                                        'Nitrogen or oxygen',
                                                    ),
                                                    'option_index' => array(
                                                        '0',
                                                        '1',
                                                        '2',
                                                        '3',
                                                    ),
                                                    'correct_answers' => array(
                                                        '1'
                                                    ),
                                                    'answer_explanation' => 'Helium and nitrogen both the gases provide inert atmosphere. When the packed food is surrounded by unreactive gas (nitrogen or helium), there is no oxygen (or air) to cause its oxidation and make it rancid. ',
                                                ),
                                            ),
                                            'single' => array(
                                                'ex_single_1_5' => array(
                                                    'id' => 'ex_single_1',
                                                    'order' => 'ex_single_1_5',
                                                    'answer' => '1',
                                                    'question' => 'When a magnesium ribbon is burnt in air, the ash formed is',
                                                    'options' => array(
                                                        'Black',
                                                        'White',
                                                        'Yellow',
                                                        'Pink',
                                                    ),
                                                    'option_index' => array(
                                                        '0',
                                                        '1',
                                                        '2',
                                                        '3',
                                                    ),
                                                    'correct_answer' => '1',
                                                    'answer_explanation' => 'When magnesium ribbon is burnt in air it forms white colored ash with dazzling light. This white colored substance is MgO ie magnesium oxide.',
                                                ),
                                            ),
                                            /* 'poll' => array(
                                              'ex_poll_type_1_6' => array(
                                              'id' => 'ex_poll_type_1',
                                              'order' => 'ex_poll_type_1_6',
                                              'question' => 'Which one is wild animal?',
                                              'options' => array(
                                              'Cow',
                                              'Tiger',
                                              'Goat',
                                              ),
                                              ),
                                              ), */
                                            'reflection' => array(
                                                'ex_reflection_1_7' => array(
                                                    'name' => 'ex_reflection_1',
                                                    'order' => 'ex_reflection_1_7',
                                                    'value' => 'Tell us about your self?',
                                                ),
                                            ),
                                        ),
                                    );

                                    add_post_meta($created_exercise_id, "rd_wpl_exercise_section", json_encode($exercise_json_data));
                                    add_post_meta($created_exercise_id, "txt_exercise_complete_hour", 1);
                                }
                            }
                        }
                    }
                }

                $this->json(1, "Sample course imported successfully");
            }
        }
        wp_die();
    }

    // function to sort data order by menu order
    public function wpl_post_orderby_menu($orderby_statement, $wp_query) {

        global $wpdb;

        if ($wp_query->get("post_type") === "exercises") {

            return "$wpdb->posts.menu_order ASC";
        } elseif ($wp_query->get("post_type") === "modules") {

            return "$wpdb->posts.menu_order ASC";
        } else {
            return $orderby_statement;
        }
    }

    // common function updated for courses, modules, chapters, exercises | function is to make sort by menu_order
    public function wpl_sort_cpt_table_list() {

        set_time_limit(600);

        global $wpdb, $userdata;

        $post_type = filter_var($_POST['post_type'], FILTER_SANITIZE_STRING);
        $paged = filter_var($_POST['paged'], FILTER_SANITIZE_NUMBER_INT);

        parse_str($_POST['order'], $data);

        if (!is_array($data) || count($data) < 1)
            die();

        //retrieve a list of all objects
        $mysql_query = $wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " 
                                                            WHERE post_type = %s AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')
                                                            ORDER BY menu_order, post_date DESC", $post_type);
        $results = $wpdb->get_results($mysql_query);

        if (!is_array($results) || count($results) < 1)
            die();

        //create the list of ID's
        $objects_ids = array();
        foreach ($results as $result) {
            $objects_ids[] = (int) $result->ID;
        }

        global $userdata;
        $objects_per_page = get_user_meta($userdata->ID, 'edit_' . $post_type . '_per_page', TRUE);
        if (empty($objects_per_page))
            $objects_per_page = 20;

        $edit_start_at = $paged * $objects_per_page - $objects_per_page;
        $index = 0;
        for ($i = $edit_start_at; $i < ($edit_start_at + $objects_per_page); $i++) {
            if (!isset($objects_ids[$i]))
                break;

            $objects_ids[$i] = (int) $data['post'][$index];
            $index++;
        }

        //update the menu_order within database
        foreach ($objects_ids as $menu_order => $id) {
            $data = array(
                'menu_order' => $menu_order
            );

            $wpdb->update($wpdb->posts, $data, array('ID' => $id));
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

    /* =========================================================== */
    /* function to show star rating on frontend */
    /* =========================================================== */

    public function wpl_rd_rating_rating_field() {
        global $post;
        $post_type = isset($post->post_type) ? $post->post_type : "";
        if ($post_type == "training") {
            ?>
            <label for="rating">Rating<span class="required">*</span></label>
            <fieldset class="comments-rating">
                <span class="rating-container">
                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                        <input type="radio" id="rating-<?php echo esc_attr($i); ?>" name="rating" value="<?php echo esc_attr($i); ?>" /><label for="rating-<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></label>
                    <?php endfor; ?>
                    <input type="radio" id="rating-0" class="star-cb-clear" name="rating" value="0" /><label for="rating-0">0</label>
                </span>
            </fieldset>
            <?php
        }
    }

    /* =========================================================== */
    /* function to save rating with comment */
    /* =========================================================== */

    public function wpl_rd_comment_rating_save_comment_rating($comment_id) {
        if (( isset($_POST['rating']) ) && ( '' !== $_POST['rating'] ))
            $rating = intval($_POST['rating']);
        add_comment_meta($comment_id, 'rating', $rating);

        global $wpdb;
        $comment_details = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT *  from " . $wpdb->prefix . "comments WHERE comment_ID = %d", $comment_id
                )
        );

        $post_id = $comment_details->comment_post_ID;
        $redirect = $post_link = get_the_permalink($post_id);
        $redirect = add_query_arg('comment_submit', 'success', $redirect);
        $redirect = add_query_arg('redirect_to', $post_link, $redirect);
        wp_redirect($redirect);
        // exit;
    }

    /* =========================================================== */
    /* function to add star rating as required field */
    /* =========================================================== */

    public function wpl_rd_comment_rating_require_rating($commentdata) {
        if (!is_admin()) {
            if (!isset($_POST['rating']) || 0 === intval($_POST['rating']))
                wp_die(__('Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.'));
        }
        return $commentdata;
    }

    /* =========================================================== */
    /* function to show/display star rating */
    /* =========================================================== */

    public function wpl_rd_comment_rating_display_rating($comment_text) {

        if ($rating = get_comment_meta(get_comment_ID(), 'rating', true)) {
            $stars = '<p class="stars">';
            $blank_star = (5 - ceil($rating));
            for ($i = 1; $i <= $rating; $i++) {
                $stars .= '<span class="dashicons dashicons-star-filled"></span>';
            }
            if ($blank_star > 0) {
                for ($i = 1; $i <= $blank_star; $i++) {
                    $stars .= '<i class="dashicons dashicons-star-empty"></i>';
                }
            }
            $stars .= '</p>';
            $comment_text = $comment_text . $stars;
            return $comment_text;
        } else {
            return $comment_text;
        }
    }

    /* =========================================================== */
    /* function to get/calculate average rating */
    /* =========================================================== */

    public function wpl_rd_comment_rating_get_average_ratings($id) {
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

    /* =========================================================== */
    /* function to display average star rating */
    /* =========================================================== */

    public function wpl_rd_comment_rating_display_average_rating($content) {

        global $post;

        if (false === $this->wpl_rd_comment_rating_get_average_ratings($post->ID)) {
            return $content;
        }

        $stars = '';
        $average = $this->wpl_rd_comment_rating_get_average_ratings($post->ID);

        for ($i = 1; $i <= $average + 1; $i++) {

            $width = intval($i - $average > 0 ? 20 - ( ( $i - $average ) * 20 ) : 20);

            if (0 === $width) {
                continue;
            }

            $stars .= '<span style="overflow:hidden; width:' . $width . 'px" class="dashicons dashicons-star-filled"></span>';

            if ($i - $average > 0) {
                $stars .= '<span style="overflow:hidden; position:relative; left:-' . $width . 'px;" class="dashicons dashicons-star-empty"></span>';
            }
        }

        $custom_content = '<p class="average-rating">This post\'s average rating is: ' . $average . ' ' . $stars . '</p>';
        $custom_content .= $content;
        return $custom_content;
    }

    /* =========================================================== */
    /* function to get total exercise by course ID */
    /* =========================================================== */

    public function wpl_rd_get_total_exercises_by_course($course_id = '') {

        global $wpdb;

        $total_exercises = $wpdb->get_results(
                "SELECT pmeta.post_id
                    FROM $wpdb->postmeta pmeta INNER JOIN $wpdb->posts post ON post.ID = pmeta.post_id
                    WHERE pmeta.meta_value
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
                    AND pmeta.meta_key =  'dd_chapter_id_box_post_type' AND post.post_status = 'publish'"
        );

        return $exercise_ids = array_column(json_decode(json_encode($total_exercises), true), "post_id");
    }

    /* =========================================================== */
    /* function to make menus at admin bar */
    /* =========================================================== */

    public function wpl_add_training_to_admin_bar($wp_admin_bar) {
        $args = array(
            'id' => 'rdtr-training',
            'title' => 'Training',
            'href' => "javascript:;",
            'meta' => array(
                'class' => 'rdtr-training'
            )
        );
        $wp_admin_bar->add_node($args);

        // Add the first child link 

        $args = array(
            'id' => 'rdtr-course',
            'title' => 'Courses',
            'href' => site_url() . '/wp-admin/edit.php?post_type=training',
            'parent' => 'rdtr-training',
            'meta' => array(
                'class' => 'rdtr-course'
            )
        );
        $wp_admin_bar->add_node($args);

        $args = array(
            'id' => 'rdtr-setting',
            'title' => 'Settings',
            'href' => site_url() . '/wp-admin/admin.php?page=training-settings',
            'parent' => 'rdtr-training',
            'meta' => array(
                'class' => 'rdtr-setting'
            )
        );
        $wp_admin_bar->add_node($args);
    }

    public function wpl_remove_comment_support_from() {
        $disable = get_option("training_disable_comments");
    }

    public function wpl_rd_allow_subscribers_upload_media($wp_query_obj) {

        global $current_user, $pagenow;

        $is_attachment_request = ($wp_query_obj->get('post_type') == 'attachment');

        if (!$is_attachment_request)
            return;

        if (!is_a($current_user, 'WP_User'))
            return;

        if (!in_array($pagenow, array('upload.php', 'admin-ajax.php')))
            return;

        if (!current_user_can('delete_pages'))
            $wp_query_obj->set('author', $current_user->ID);

        return;
    }

    public function wpl_rd_get_users_answer($user_id, $seq_no, $exercise_id) {

        global $wpdb;

        $data = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->wpl_rd_user_exercise_progress() . " WHERE user_id = %d AND exercise_id = %d AND seq_number = %s", $user_id, $exercise_id, $seq_no
                )
        );

        return $data;
    }

    public function wpl_rd_has_users_started_exercise($user_id, $exercise_id) {

        global $wpdb;

        $data = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->wpl_rd_user_exercise_progress() . " WHERE user_id = %d AND exercise_id = %d", $user_id, $exercise_id
                )
        );

        return $data;
    }

    public function wpl_rd_get_wp_pages() {

        $wp_pages = get_pages(array(
            'post_type' => 'page',
            'post_status' => 'publish'
        ));

        return $wp_pages;
    }

    public function wpl_rd_custom_search_filter_courses($query) {

        global $wpdb;

        if (is_admin() && $query->is_main_query()) {

            if ($query->is_search) {

                $valid_posts = array("training", "modules", "chapters", "exercises");

                if (in_array($query->query['post_type'], $valid_posts)) {

                    $search_term = $query->query_vars['s'];

                    $post_type = $query->query['post_type'];

                    if (!empty($search_term)) {

                        if ($post_type == "training") {
                            $get_all_post = $wpdb->get_row(
                                    "SELECT group_concat(ID) AS post_ids FROM $wpdb->posts WHERE post_title like  '%$search_term%' AND post_status = 'publish' AND post_type = '$post_type' "
                            );
                        } elseif ($post_type == "modules") {

                            $course_id = isset($_REQUEST['filter_by_course']) ? intval($_REQUEST['filter_by_course']) : "";

                            $get_all_post = $wpdb->get_row(
                                    "SELECT group_concat(ID) AS post_ids FROM $wpdb->posts post WHERE post_title like  '%$search_term%' AND post_status = 'publish' AND post_type = '$post_type' AND ID IN ( SELECT post_id from $wpdb->postmeta WHERE meta_value = $course_id AND meta_key = 'dd_course_box_post_type')"
                            );
                        } elseif ($post_type == "chapters") {

                            $module_id = isset($_REQUEST['filter_by_module']) ? intval($_REQUEST['filter_by_module']) : "";

                            $get_all_post = $wpdb->get_row(
                                    "SELECT group_concat(ID) AS post_ids FROM $wpdb->posts post WHERE post_title like  '%$search_term%' AND post_status = 'publish' AND post_type = '$post_type' AND ID IN ( SELECT post_id from $wpdb->postmeta WHERE meta_value = $module_id)"
                            );
                        } elseif ($post_type == "exercises") {

                            $chapter_id = isset($_REQUEST['filter_by_chapter']) ? intval($_REQUEST['filter_by_chapter']) : "";

                            $get_all_post = $wpdb->get_row(
                                    "SELECT group_concat(ID) AS post_ids FROM $wpdb->posts post WHERE post_title like  '%$search_term%' AND post_status = 'publish' AND post_type = '$post_type' AND ID IN ( SELECT post_id from $wpdb->postmeta WHERE meta_value = $chapter_id)"
                            );
                        }

                        $ids_array = array();
                        if (isset($get_all_post->post_ids)) {
                            $ids_array = explode(",", $get_all_post->post_ids);
                        }

                        if (!empty($ids_array)) {
                            $query->query_vars['post__in'] = $ids_array;
                        } else {
                            $query->query_vars['post__in'] = array('');
                        }
                    }
                }
            }
        }
        return $query;
    }

    public function wpl_rd_calculate_user_course_score($user_id, $course_id) {

        global $wpdb;

        //get total exercise of course
        $total_course_exercise = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(id) from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE user_id = %d AND course_post_id = %d", $user_id, $course_id
                )
        );

        // get completed exercise of user
        $total_completed_exercise = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(id) from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE user_id = %d AND course_post_id = %d AND exercise_status = %d", $user_id, $course_id, 1
                )
        );

        if ($total_course_exercise == 0) {
            return 100;
        }

        if ($total_completed_exercise == 0) {
            return 0;
        }

        $completed_percentage = round(($total_completed_exercise / $total_course_exercise), 2);

        return $completed_percentage * 100;
    }

    public function rdtr_check_exercise_status($exercise_id, $user_id) {

        global $wpdb;

        $exercise_status = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE exercise_id = %d and user_id = %d", $exercise_id, $user_id
                )
        );

        if (!empty($exercise_status)) {

            return $exercise_status;
        } else {
            return array();
        }
    }

}
