<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Training_User_Progress_Table {

    public $activator;

    public function __construct() {
        
    }

    public function wpl_user_course_progress() {

        $generateUserProgressWpTable = new Generate_User_Progress_WP_Table();
        if (isset($_POST['s'])) {
            $generateUserProgressWpTable->prepare_items($_POST['s']);
        } else {
            $generateUserProgressWpTable->prepare_items();
        }
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <?php
            $course_id = isset($_GET['c']) ? intval($_GET['c']) : "";
            $course_data = get_post($course_id);
            ?>
            <h2>'<?php echo ucfirst($course_data->post_title) ?>' course enrolled</h2>
            <div class="notice notice-warning">
                <p>
                    This is the list of Users who enrolled course <b>'<?php echo ucfirst($course_data->post_title) ?>'</b>
                    &nbsp;&nbsp;<a href="edit.php?post_type=training" class="button button-primary wpl-user-progress"> Back to Course</a>
                </p>
            </div>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=user-progress&c=<?php echo $course_id ?>">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php $generateUserProgressWpTable->search_box('search user(s)', 'search_training_course'); ?>
                <?php $generateUserProgressWpTable->display(); ?>
            </form>
        </div>
        <?php
    }

}

class Generate_User_Progress_WP_Table extends WP_List_Table {

    public function prepare_items($search = '') {

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data($search);
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
            'username' => 'Username',
            'email' => 'Email',
            'course_progress' => 'Progress',
            'enrolled_at' => 'Created at',
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

    private function table_data($search = '') {
        $data = array();

        global $wpdb;

        require_once RDTR_TRAINING_DIR_PATH . 'includes/class-rdtr-activator.php';
        $table_activator = new Rdtr_Activator();

        $activator = $table_activator;

        $cid = isset($_GET['c']) ? intval($_GET['c']) : "";

        if (!empty($search)) {
            
            $user_course_enroll = $wpdb->get_results(
                    "SELECT user.ID,user.user_login, user.user_email, enrol.created_at FROM $wpdb->users user INNER JOIN " . $activator->wpl_rd_user_enroll_tbl() . " enrol ON user.ID = enrol.user_id WHERE enrol.course_post_id = $cid AND (user.user_login like '%$search%' OR user.user_email like '%$search%')"
            );
        } else {
            
            $user_course_enroll = $wpdb->get_results(
                    $wpdb->prepare(
                            "SELECT user.ID,user.user_login, user.user_email, enrol.created_at FROM $wpdb->users user INNER JOIN " . $activator->wpl_rd_user_enroll_tbl() . " enrol ON user.ID = enrol.user_id WHERE enrol.course_post_id = %d", $cid
                    )
            );
        }

        if (count($user_course_enroll) > 0) {
            $count = 1;
            foreach ($user_course_enroll as $inx => $subscriber) {
                $data[] = array(
                    'id' => $count++,
                    'username' => $subscriber->user_login,
                    'email' => $subscriber->user_email,
                    'course_progress' => '<a href="admin.php?page=view-progress&c=' . $cid . '&u=' . $subscriber->ID . '" class="button button-default button-large">View Progress</a>',
                    'enrolled_at' => $subscriber->created_at,
                );
            }
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
            case 'username':
            case 'email':
            case 'course_progress':
            case 'enrolled_at':

                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

}
