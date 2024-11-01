<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.rudrainnovative.com
 * @since      1.0.0
 *
 * @package    Rdtr
 * @subpackage Rdtr/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rdtr
 * @subpackage Rdtr/includes
 * @author     Rudra Innovative Software Pvt Ltd <info@rudrainnovatives.com>
 */
class Rdtr {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Rdtr_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('RDTR_TRAINING_NAME_VERSION')) {
            $this->version = RDTR_TRAINING_NAME_VERSION;
        } else {
            $this->version = '2.0.0';
        }
        $this->plugin_name = 'rdtr';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Rdtr_Loader. Orchestrates the hooks of the plugin.
     * - Rdtr_i18n. Defines internationalization functionality.
     * - Rdtr_Admin. Defines all hooks for the admin area.
     * - Rdtr_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-rdtr-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-rdtr-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-rdtr-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-rdtr-public.php';

        $this->loader = new Rdtr_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Rdtr_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Rdtr_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Rdtr_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('pre_get_posts', $plugin_admin, 'wpl_rd_allow_subscribers_upload_media');
        //action hooks to load css and js
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        // action hook to make menus
        $this->loader->add_action('admin_menu', $plugin_admin, 'wpl_rd_add_menus');

        //call to register custom post type and user role
        $this->loader->add_action('init', $plugin_admin, 'wpl_register_training_cpt');

        $this->loader->add_action("wp_ajax_rd_wpl_training_library", $plugin_admin, "wpl_ajax_handler");

        // add filter hook to modify search query for esxercises
        $this->loader->add_filter('parse_query', $plugin_admin, "wpl_do_exercise_filter_query");

        // add filter hook to modify search query for course dropdown filter on modules
        $this->loader->add_filter('parse_query', $plugin_admin, "wpl_modify_module_filter_query_for_chapters");

        // add filter hook to modify search query for course dropdown filter on modules
        $this->loader->add_filter('parse_query', $plugin_admin, "wpl_modify_course_filter_query");

        // add filter hook to modify search query for course author dropdown filter on courses
        $this->loader->add_filter('parse_query', $plugin_admin, 'wpl_convert_course_author_id_filter_query');

        // add action hook of make categories and author search filter box
        $this->loader->add_action('restrict_manage_posts', $plugin_admin, 'wpl_filter_course_by_author');

        //disply custom buttons to cpt
        //$this->loader->add_filter('views_edit-training', $plugin_admin, 'wpl_add_download_pdf_csv_buttons');

        $this->loader->add_filter('views_edit-modules', $plugin_admin, 'wpl_back_to_courses');

        $this->loader->add_filter('views_edit-chapters', $plugin_admin, 'wpl_back_to_module');

        $this->loader->add_filter('views_edit-exercises', $plugin_admin, 'wpl_back_to_chapter');

        // action hook to save chapter dd value for exercise
        $this->loader->add_action("save_post", $plugin_admin, "wpl_dd_chapter_meta_box_save_meta", 10, 3);
        
        // action hook to save chapter dd value for exercise
        $this->loader->add_action("save_post", $plugin_admin, "wpl_save_course_banner_image", 10, 2);

        // action hook to save module dd value for chapters
        $this->loader->add_action("save_post", $plugin_admin, "wpl_dd_module_meta_box_save_meta", 10, 3);

        // action hook to save exercises sections to db
        $this->loader->add_action("save_post", $plugin_admin, "wpl_dd_save_exercises_sections", 10, 3);

        // action hook to save total hours for exercise on exercise CPT
        $this->loader->add_action("save_post", $plugin_admin, "wpl_txt_hours_meta_box_save_meta", 10, 3);

        // action hook to save course meta box on module
        $this->loader->add_action("save_post", $plugin_admin, "wpl_dd_course_meta_box_save_meta", 10, 3);

        // action hook to save course author meta box on course
        $this->loader->add_action("save_post", $plugin_admin, "wpl_dd_course_author_meta_box_save_meta", 10, 3);

        // action hook to save course features
        $this->loader->add_action("save_post", $plugin_admin, "wpl_dd_course_feature_meta_box", 10, 2);

        // action hook to save course type meta box on course
        $this->loader->add_action("save_post", $plugin_admin, "wpl_dd_course_type_meta_box_save_meta", 10, 3);

        // action hook to save chapter assignment
        $this->loader->add_action("save_post", $plugin_admin, "wpl_chapter_assignment_meta_box_save_meta", 10, 2);

        // action hook to add metaboxes
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'wpl_tr_meta_box_add');

        // action hook to do open plugin menus on selecting category taxonomy
        $this->loader->add_action('parent_file', $plugin_admin, 'wpl_custom_taxonomy_menu_highlight');

        // action hook to create course category taxonomy
        //$this->loader->add_action('init', $plugin_admin, 'wpl_create_course_category_taxonomies');
        // action hook to add scripts to head
        $this->loader->add_action('wp_print_scripts', $plugin_admin, 'wpl_scripts_at_head');

        //filter hook to do sortable columns for modules CPT table
        $this->loader->add_filter('manage_edit-modules_sortable_columns', $plugin_admin, 'wpl_cpt_modules_sortable_columns');

        //filter hook to add custom column on exercise CPT
        $this->loader->add_filter('manage_exercises_posts_columns', $plugin_admin, 'wpl_set_cpt_exercise_columns');

        //filter hook to add custom column on chapters CPT
        $this->loader->add_filter('manage_chapters_posts_columns', $plugin_admin, 'wpl_set_cpt_chapters_columns');

        //filter hook to add custom column on modules CPT
        $this->loader->add_filter('manage_training_posts_columns', $plugin_admin, 'wpl_set_cpt_course_columns');

        //filter hook to add custom column on modules CPT
        $this->loader->add_filter('manage_modules_posts_columns', $plugin_admin, 'wpl_set_cpt_module_columns');

        // action hook to add data for custom column data for course CPT
        $this->loader->add_action('manage_training_posts_custom_column', $plugin_admin, 'wpl_cpt_course_column_data', 10, 2);

        // action hook to add data for custom column data for modules CPT
        $this->loader->add_action('manage_modules_posts_custom_column', $plugin_admin, 'wpl_cpt_module_column_data', 10, 2);

        // action hook to add data for custom column data for chapters CPT
        $this->loader->add_action('manage_chapters_posts_custom_column', $plugin_admin, 'wpl_cpt_chapter_column_data', 10, 2);

        // action hook to add data for custom column data for exercise CPT
        $this->loader->add_action('manage_exercises_posts_custom_column', $plugin_admin, 'wpl_cpt_exercises_column_data', 10, 2);

        //$this->loader->add_action('current_screen', $plugin_admin, "wpl_catch_page_screen_request");

        /*         * ****************************************************************************** */
        // filter hook for adding menu links to plugin installation list
        $this->loader->add_filter("plugin_action_links_" . RDTR_TRAINING_BASEPATH, $plugin_admin, 'add_settings_link', 10, 3);

        $this->loader->add_filter('posts_orderby', $plugin_admin, 'wpl_post_orderby_menu', 10, 2);

        //Create the rating interface.
        $this->loader->add_action('comment_form_logged_in_after', $plugin_admin, 'wpl_rd_rating_rating_field');
        $this->loader->add_action('comment_form_after_fields', $plugin_admin, 'wpl_rd_rating_rating_field');

        //Save the rating submitted by the user.
        $this->loader->add_action('comment_post', $plugin_admin, 'wpl_rd_comment_rating_save_comment_rating');

        //Make the rating required.
        $this->loader->add_filter('preprocess_comment', $plugin_admin, 'wpl_rd_comment_rating_require_rating');

        //Display the rating on a submitted comment.
        $this->loader->add_filter('comment_text', $plugin_admin, 'wpl_rd_comment_rating_display_rating');

        //Display the average rating above the content.
        $this->loader->add_filter('the_content', $plugin_admin, 'wpl_rd_comment_rating_display_average_rating');

        $this->loader->add_action('admin_bar_menu', $plugin_admin, 'wpl_add_training_to_admin_bar', 999);

        $this->loader->add_action('admin_notices', $plugin_admin, 'wpl_top_show_breadcrumbs');

        //remove comments from pages
        $this->loader->add_action('init', $plugin_admin, 'wpl_remove_comment_support_from', 100);

        $this->loader->add_action('pre_get_posts', $plugin_admin, 'wpl_rd_custom_search_filter_courses');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Rdtr_Public($this->get_plugin_name(), $this->get_version());

        // Action Hooks
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        //Filter Hooks
        $this->loader->add_filter("template_include", $plugin_public, 'training_page_template', 10, 3);

        $this->loader->add_action("wp_ajax_wpl_training_public_handler", $plugin_public, "wpl_public_ajax_handler");
        $this->loader->add_action("wp_ajax_nopriv_wpl_training_public_handler", $plugin_public, "wpl_public_ajax_handler");

        add_shortcode("training-courses", array($plugin_public, "wpl_rd_front_end_training_courses"));

        add_shortcode("training-my-course", array($plugin_public, "wpl_rd_front_end_training_my_course"));
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {

        self::wpl_training_more_plugin_constants_import();
        self::wpl_training_default_settings_import();

        $this->loader->run();
    }

    private function wpl_training_default_settings_import() {
        global $wpdb;

        //checking for default course image
        $course_default_image = get_option("wpl_training_course_image");
        if (empty($course_default_image)) {
            // if no image then add no-image 
            add_option("wpl_training_course_image", RDTR_TRAINING_PLUGIN_URL . "assets/images/no-image.png");
        }
    }

    private function wpl_training_more_plugin_constants_import() {

        define('RDTR_COURSE_DEFAULT_IMAGE', get_option("wpl_training_course_image"));
        define('RDTR_COURSE_LOAD_MORE', 12);
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Rdtr_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
