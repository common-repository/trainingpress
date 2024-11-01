<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Training_View_Progress_Table {

    public $table_activator;

    public function __construct() {
        require_once RDTR_TRAINING_DIR_PATH . 'includes/class-rdtr-activator.php';
        $table_activator = new Rdtr_Activator();

        $this->table_activator = $table_activator;
    }

    public function wpl_view_course_progress() {

        $generateUserProgressWpTable = new Generate_View_Progress_WP_Table();
        $cid = isset($_GET['c']) ? intval($_GET['c']) : '';
        if (isset($_POST['s'])) {
            $generateUserProgressWpTable->prepare_items($_POST['s']);
        } else {
            $generateUserProgressWpTable->prepare_items();
        }
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <?php
            $uid = isset($_GET['u']) ? intval($_GET['u']) : "";
            $user = get_user_by('ID', $uid);
            ?>
            <h2>User '<?php echo ucfirst($user->display_name); ?>' course progress</h2>

            <div class="notice notice-warning">
                <p>
                    User <b>'<?php echo ucfirst($user->display_name); ?>'</b> course progress.
                    &nbsp;&nbsp;<a href="admin.php?page=user-progress&c=<?php echo $cid ?>" class="button button-primary wpl-user-progress"> Back to User's list</a>
                </p>
            </div>

            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=user-progress">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

                <?php $generateUserProgressWpTable->display(); ?>
            </form>
            <div id="course-files-dialog" title="">
                <p id="wpl-dialog-message"></p>
            </div>
            <?php
            if (class_exists('Rdtr_Admin')) {
                require_once RDTR_TRAINING_DIR_PATH . 'admin/class-rdtr-admin.php';
            }

            $admin_page = new Rdtr_Admin(RDTR_TRAINING_NAME_VERSION, 'rdtr');
            global $wpdb;
            $all_exercises = $wpdb->get_results(
                    $wpdb->prepare(
                            "SELECT * from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE user_id  = %d AND course_post_id = %d", $uid, $cid
                    )
            );
            $total_exercises_found = $admin_page->wpl_rd_get_total_exercises_by_course($cid);
            if (count($all_exercises) > 0) {
                $total_done = 0;
                foreach ($all_exercises as $inx => $exe) {
                    $exercise_data = get_post($exe->exercise_id);
                    if (empty($exercise_data->post_title)) {
                        continue;
                    }
                    $total_done++;
                }
                ?>
                <div class='user-progress-exercisewise'>
                    <h3>Exercise Progress (<?php echo $total_done ?> / <?php echo count($total_exercises_found); ?>)</h3>
                </div>
                <?php
                foreach ($all_exercises as $inx => $exe) {
                    $exercise = new Generate_Exercise_Progress_WP_Table();
                    $exercise->prepare_items($exe->exercise_id);

                    $exercise_data = get_post($exe->exercise_id);

                    if (empty($exercise_data->post_title)) {
                        continue;
                    }
                    ?>
                    <div class='wpl-exe-notice'>
                        <?php
                        $has_started = $admin_page->wpl_rd_has_users_started_exercise($uid, $exe->exercise_id);
                        $exercise_user_status = '';
                        $success = 0;
                        if (!empty($has_started)) {
                            if ($has_started->complete_status == 1) {
                                $exercise_user_status = "Completed";
                                $success = 1;
                            } else {
                                $exercise_user_status = "In Progress";
                            }
                        } else {
                            $exercise_user_status = "Not yet started";
                        }
                        ?>
                        <h4><?php echo $exercise_data->post_title; ?> <span class='u-ex-status <?php
                            if ($success) {
                                echo 'st-success';
                            }
                            ?>'>(<?php echo $exercise_user_status; ?>)</span>
                        </h4>
                    </div>
                    <?php
                    $exercise->display();
                }
                ?>
                <div id="exercise-files-dialog" title="">
                    <p id="user-exercise-status">

                    </p>
                </div>
                <?php
            }
            ?>

        </div>
        <?php
    }

}

// generate Exercise progress section
class Generate_Exercise_Progress_WP_Table extends WP_List_Table {

    public function prepare_items($exercise_id = '') {

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data($exercise_id);
        usort($data, array(&$this, 'sort_data'));

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'id' => 'ID',
            "module_name" => "Module",
            "chapter_name" => "Chapter",
            'exercise_name' => 'Exercise',
            'total_hours' => 'Total Hours',
            'view_exercise_progress' => 'Exercise Status'
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        
    }

    private function table_data($exercise_id) {
        $data = array();

        if (class_exists('Rdtr_Admin')) {
            require_once RDTR_TRAINING_DIR_PATH . 'admin/class-rdtr-admin.php';
        }

        $admin_page = new Rdtr_Admin(RDTR_TRAINING_NAME_VERSION, 'rdtr');

        global $wpdb;
        if (class_exists('Rdtr_Activator')) {
            require_once RDTR_TRAINING_DIR_PATH . 'includes/class-rdtr-activator.php';
        }
        $table_activator = new Rdtr_Activator();
        $activator = $table_activator;

        $total_hours = get_post_meta($exercise_id, "txt_exercise_complete_hour", true);
        $chapter_id = get_post_meta($exercise_id, "dd_chapter_id_box_post_type", true);
        $module_id = get_post_meta($chapter_id, "dd_module_id_box_post_type", true);

        $exercise_data = get_post($exercise_id);
        $chapter_data = get_post($chapter_id);
        $module_data = get_post($module_id);

        $uid = isset($_GET['u']) ? intval($_GET['u']) : "";

        $data[] = array(
            'id' => 1,
            'module_name' => $module_data->post_title,
            'chapter_name' => $chapter_data->post_title,
            'exercise_name' => $exercise_data->post_title,
            'total_hours' => $total_hours,
            'view_exercise_progress' => '<button class="button wpl-ex-btn" data-id="' . $exercise_id . '" uid="' . $uid . '">Exercise Progress</button>',
        );
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
            case 'module_name':
            case 'chapter_name':
            case 'exercise_name':
            case 'total_hours':
            case 'view_exercise_progress':

                return $item[$column_name];

            default:
                return print_r($item, true);
        }
    }

}

class Generate_View_Progress_WP_Table extends WP_List_Table {

    public function prepare_items($search = '') {

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();
        usort($data, array(&$this, 'sort_data'));

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'id' => 'ID',
            'course_name' => 'Course',
            'total_exercises' => 'Total Exercise',
            'exercise_completed' => 'Exercise Completed',
            'submitted_files' => 'Submitted Files',
            //'submitted_links' => 'Submitted Links',
            'total_ratio' => 'Completed %'
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        
    }

    private function table_data() {
        $data = array();

        if (class_exists('Rdtr_Admin')) {
            require_once RDTR_TRAINING_DIR_PATH . 'admin/class-rdtr-admin.php';
        }

        $admin_page = new Rdtr_Admin(RDTR_TRAINING_NAME_VERSION, 'rdtr');

        global $wpdb;
        if (class_exists('Rdtr_Activator')) {
            require_once RDTR_TRAINING_DIR_PATH . 'includes/class-rdtr-activator.php';
        }
        $table_activator = new Rdtr_Activator();
        $activator = $table_activator;

        $user_id = isset($_GET['u']) ? intval($_GET['u']) : 0;
        $course_id = isset($_GET['c']) ? intval($_GET['c']) : 0;

        $resource_detail = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT group_concat(file) as files,chapter_id from " . $activator->wpl_rd_user_uploaded_files_tbl() . " WHERE user_id = %d GROUP BY chapter_id", $user_id
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

        $get_all_exercises = count($admin_page->wpl_rd_get_total_exercises_by_course($course_id));
        $completedExercise = $wpdb->get_var(
                $wpdb->prepare(
                        'SELECT COUNT( id ) 
                        FROM ' . $activator->wpl_rd_course_progress_tbl() . '
                        WHERE course_post_id = %d
                        AND user_id = %d
                        AND exercise_status = 1', $course_id, $user_id
                )
        );

        if ($completedExercise > 0) {
            $completedPercentage = ($completedExercise / $get_all_exercises) * 100;
        } else {
            $completedPercentage = 0;
        }

        $course_details = get_post($course_id, ARRAY_A);
        $user_course_files_detail = array();
        $links = 0;
        $files = 0;
        $user_course_links_detail = array();

        if (count($u_files) > 0) {

            foreach ($u_files as $inx => $file) {
                $user_course_files_detail[] = $file;
                $files++;
            }
            $data[] = array(
                'id' => 1,
                'course_name' => $course_details['post_title'],
                'total_exercises' => $get_all_exercises,
                'exercise_completed' => $completedExercise,
                'submitted_files' => "Total files: " . $files . "<br/><a href='javascript:void(0)' class='wp-rd-open-file-dialog' data-type='file' data-uid='" . $user_id . "' data-cid='" . $course_id . "'> View files</a>",
                //'submitted_links' => "Total links: " . $links . "<br/><a href='javascript:void(0)' class='wp-rd-open-file-dialog' data-type='link' data-uid='" . $user_id . "' data-cid='" . $course_id . "'> View links</a>",
                'total_ratio' => round($completedPercentage, 2) . "%",
            );
        } else {
            $data[] = array(
                'id' => 1,
                'course_name' => $course_details['post_title'],
                'total_exercises' => $get_all_exercises,
                'exercise_completed' => $completedExercise,
                'submitted_files' => "Total files: " . $files . "<br/><a href='javascript:void(0)' class='wp-rd-open-file-dialog' data-type='file' data-uid='" . $user_id . "' data-cid='" . $course_id . "'> View files</a>",
                //'submitted_links' => "Total links: " . $links . "<br/><a href='javascript:void(0)' class='wp-rd-open-file-dialog' data-type='link' data-uid='" . $user_id . "' data-cid='" . $course_id . "'> View links</a>",
                'total_ratio' => $completedPercentage . "%",
            );
        }
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
            case 'course_name':
            case 'total_exercises':
            case 'exercise_completed':
            case 'submitted_files':
            //case 'submitted_links':
            case 'total_ratio':

                return $item[$column_name];

            default:
                return print_r($item, true);
        }
    }

}
