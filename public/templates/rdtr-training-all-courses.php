<?php
/**
 * This is training all course page.
 * @author	Rudra Innnovative Software 
 * @package	training/templates/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;

global $user_ID;
?>

<div class="wptr-rud-training-pg wptr_main_body wptr-rud-all-section" id='all-courses_top'>

    <div class='my_rdtr_topbar'>
        <ul class="rdtr-breadcrumb my-course_page">
            <li><a href="<?php echo get_permalink(get_option("rdtr_training_allcourses_page")); ?>">Home</a></li>
        </ul>
    </div>

    <input type="hidden" name="training_page_type" id="training_page_type" value="all"/>
    <section class="wptr_categories-section wptr_bg-gray">

        <form action="javascript:void(0)" id="frmSearchCourse" method="post" class="wptr_input-group">
            <input type="text" placeholder="Search by course name" name="txt_search_course" id="txt_search_course"/>
            <button class="wptr_p-0" id="btn-search">
                <div class="border-0 bg-light pl-0 py-md-2 py-0">
                    <i class="material-icons mt-1 display-6 font-weight-bold text-black">search</i>
                </div>
            </button>
        </form>

        <div class="wptr_p-top-3 wptr_p-btm-3">
            <div class="wptr_row-section" id="training-all-courses">
                <?php
                ob_start();
                include_once RDTR_TRAINING_DIR_PATH . 'public/templates/tmpl/rdtr-load-courses.php';
                $template = ob_get_contents();
                ob_end_clean();
                echo $template;
                ?>
            </div>
        </div>
    </section>
</div>
