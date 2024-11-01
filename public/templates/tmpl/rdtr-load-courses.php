<?php
/**
 * This File for load courses.
 * @author	Rudra Innnovative Software 
 * @package	training/templates/tmpl/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;

$search_keyword = isset($search_term) ? $search_term : "";
$offset = isset($offset) ? $offset : 0;
$allcourses = $this->wpl_rd_get_all_courses($search_keyword, $offset);

if (count($allcourses) > 0) {

    if (!empty($search_keyword)) {
        ?>
        <div class="training-course-found">
            <h3>Course found with the search keyword <b>'<?php echo $search_keyword; ?>'</b></h3>
        </div>
        <?php
    }

    foreach ($allcourses as $inx => $course) {
        // for the course image thumnail url
        $featured_img_url = get_the_post_thumbnail_url($course->ID, 'full');
        if (empty($featured_img_url)) {
            $featured_img_url = get_option("wpl_training_course_image");
        }
        // for course author
        $author_id = get_post_meta($course->ID, "dd_course_author_box_post_type", true);
        $author_avtaar = get_avatar_url($author_id);
        ?>
        <!-- single course region -->
        <div class="wptr_col-medium-3 wptr_col-small-12 wptr_categories-col wptr_py-yx-3 wptr_my-course course_traning_page">
            <div class="wptr_card-box">
                <a class="wptr_a wptr_shadow_none" href="<?php echo get_the_permalink($course->ID) ?>">
                    <figure class="wptr_mb-0 wptr_img_flex">
                        <?php
                        $setting_image = RDTR_TRAINING_PLUGIN_URL . "assets/images/no-image.png";
                        if (@getimagesize($featured_img_url)) {
                            $setting_image = $featured_img_url;
                        }
                        ?>
                        <img class="card-box-img-top wptr_shadow_none" src="<?php echo $setting_image; ?>" alt="<?php echo $course->post_title; ?>"> 
                    </figure>
                </a>
                <div class="wptr_card-box-body">
                    <div class="wptr_card-box-title wptr_h6_6 wptr_f-weight-bold wptr_pt-sans wptr_line-overflow">
                        <a class="wptr_a wptr_shadow_none" href="<?php echo get_the_permalink($course->ID) ?>"> <?php echo ucwords($course->post_title); ?></a>
                    </div>
                    <div class="wptr_userimg-and-price wptr_width-100 wptr_d-flex">
                        <img src="<?php echo $author_avtaar; ?>" class="wptr_img-fluid wptr_rounded-circle wptr_align-self-center" alt="<?php the_author_meta('display_name', $author_id); ?>">
                        <div class="wptr_user_name wptr_text-secondary wptr_f-weight-semi-bold  wptr_text-capitalize wptr_align-self-center wptr_montserrat"><?php
                            $authorName = get_the_author_meta('display_name', $author_id);
                            echo ucfirst($authorName);
                            ?>
                        </div>
                        <?php
                        if (has_action('training_py_adon')) { // checking for action hook definition
                            do_action("training_py_adon", $course->ID);
                        } else {
                            ?>
                            <div class="wptr_f-weight-semi-bold wptr_margin-left-auto wptr_d-5">Free</div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} else {

    if (!empty($search_keyword)) {
        ?>
        <div class="training-course-found">
            <h3>Course not found with the search keyword <b>'<?php echo $search_keyword; ?>'</b></h3>
        </div>
        <?php
    } else {
        ?>
        <div class="training-no-course">
            <h3>No courses added yet.</h3>
        </div>
        <?php
    }
}
?>