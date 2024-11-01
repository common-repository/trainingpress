<?php
/**
 * This is course detail page.
 * @author	Rudra Innnovative Software 
 * @package	training/templates/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;

global $user_ID;
$c_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : '';
$chid = isset($_REQUEST['chid']) ? intval($_REQUEST['chid']) : '';
$modid = isset($_REQUEST['modid']) ? intval($_REQUEST['modid']) : '';
$st = isset($_REQUEST['st']) ? trim($_REQUEST['st']) : '';
$get_course = get_post($c_id);
$device = 0;
if ($this->rdtr_wp_is_mobile()) {
    $style = 'display:none;';
    $device = 1;
}
?>
<script>
    var ismobile = <?php echo $device; ?>
</script>
<?php
if (!empty($get_course) && $user_ID > 0) {
    ?>
    <div id="my-course_module_detail" class="wptr-rud-training-pg wptr-rud-sin-course-detail">
        <div class="rdtr-pl my_rdtr_topbar">
            <ul class="rdtr-breadcrumb my-course_page">
                <li><a href="<?php echo get_permalink(get_option("rdtr_training_allcourses_page")); ?>">Home</a></li>
                <li><a href="<?php echo get_permalink(get_option("rdtr_training_mycourse_page")); ?>">My Course</a></li>
                <li><?php echo ucfirst($get_course->post_title); ?> <i class="mdi mdi-chevron-double-right font-nomal_icon"></i> Detail</li>
            </ul>

            <div class="wptr_row-section my-course-t_bar wptr_mx-0  wptr_px-3 wptr_py-3 wptr_mx-xy-0 ">
                <div class="wptr_container-fld">
                    <div class="wptr_column-flex wptr_items-center">
                        <span class="wptr_btn-success wptr_rounded  wptr_middle-algn wptr_round-tag text-center">
                            <i class="mdi mdi-book-open-variant wptr_middle-algn font-nomal_icons"></i>
                        </span>
                        <h6 class="wptr_txt-truncate wptr_middle-algn wptr_mb-0 wptr_pl-3 wptr_h6_6 wptr_mbtm-0"><?php echo ucfirst($get_course->post_title); ?></h6>
                        <div id="wptr-locate-mob-view">
                            <a href="javascript:void(0)">
                                <div id="wptr-collapse-burger-menu">
                                    <div class="menu-bar"></div>
                                    <div class="menu-bar"></div>
                                    <div class="menu-bar"></div>
                                    <div class="menu-bar"></div>
                                </div>
                            </a>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Top Nav Bar Start here  -->
        <section class="wptr_dashboard-warpper">
            <div id="wptr_content">
                <div class="wptr_dasboard-content-area my-course-dash">
                    <div class="wptr_container-fld wptr_px-0 wptr_height-100">
                        <div class="wptr_module-section wptr_width-100 wptr_column-lg-flex">

                            <div id="wptr_module" class="load-my-training-sidebar wptr_order-1 wptr_width-100 wptr_column-flex my-course_sidebar " >
                                <?php
                                ob_start();
                                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-course-detail-left-sidebar.php';
                                $template = ob_get_contents();
                                ob_end_clean();
                                echo $template;
                                ?>
                            </div>

                            <div class="wpl_content wptr_order-2 wptr_module-container my-course_container_area  wptr_p-4" id="rdtr-load-course-body">
                                <?php
                                ob_start();
                                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-course-body-section.php';
                                $template = ob_get_contents();
                                ob_end_clean();
                                echo $template;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
} else {
    echo '<h4>You have no permission to access.</h4>';
}
?>

<script>
    var is_course_locked = <?php echo $this->wpl_rd_has_course_locked($c_id); ?>
</script>