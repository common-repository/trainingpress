<?php
/**
 * This is course syllabus page.
 * @author	Rudra Innnovative Software 
 * @package	training/templates/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;

$c_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : '';

$get_course = get_post($c_id);

if (!empty($get_course)) {
    ?>
    <div id="my-course-modules_page" class="wptr-rud-training-pg wptr-rud-course-syll">
        <ul class="rdtr-breadcrumb">
            <li><a href="<?php echo get_permalink(get_option("rdtr_training_allcourses_page")); ?>">Home</a></li>
            <li><a href="<?php echo get_permalink(get_option("rdtr_training_mycourse_page")); ?>">My Course</a></li>
            <li><?php echo ucfirst($get_course->post_title); ?> <i class="mdi mdi-chevron-double-right font-nomal_icon"></i> Syllabus</li>
        </ul>

        <section class="rt_course_syllabus">
            <!-- Side Nav Bar Start here  -->
            <div id="content" class="wptr_pbtm-5">
                <!-- inner profile section -->
                <div class="wptr_pad-0 wptr_categories-section" id="my-course-syllabus">
                    <div class="wptr_container-fld wptr_px-xy-0 ">
                        <?php
                        if (count($module_ids) > 0) {

                            $module_count = 1;

                            foreach ($module_ids as $inx => $module) {
                                $slick = 1;
                                $first_ch = 0;
                                $module_data = get_post($module->post_id);
                                
                                ?>

                                <div class="wptr_row-section wptr_mx-xy-0 wptr_px-xy-3 wptr_py-yx-4 bg-light-gray" id="rdtr-modules-area_<?php echo $module_count; ?>">
                                    <h4 class="wptr_montserrat-font wptr_width-100  wptr_mx-xy-1 wptr_my-yx-4 wptr_pbtm-2 wptr-module-count">
                                        <div class="wptr_badge wptr_badge-info wptr_bg-light-red"><?php echo $module_count; ?></div> 
                                        <?php echo ucfirst($module_data->post_title); ?>
                                    </h4>
                                    <!-- chapter slider -->
                                    <?php
                                    
                                    $chapters = $this->wpl_rd_get_chapters_by_module($module->post_id);
                                    if (count($chapters) > 0) {
                                        
                                        ?>
                                        <div class="wptr_description-slider wptr-slider-layout rd-sld">
                                            <?php
                                            $chapters_count = 1;
                                            foreach ($chapters as $inx => $stx) {
                                                $chapter_detail = get_post($stx->post_id);
                                                if ($chapters_count < 2) {
                                                    $first_ch = $stx->post_id;
                                                }
                                               
                                                ?>
                                                <!-- single chapter -->
                                                <a href="javascript:void(0)" rd-data='<?php echo $stx->post_id; ?>' data-slick="<?php echo $module->post_id; ?>" data-id="<?php echo $stx->post_id ?>" data-module-order="<?php echo $module_count ?>" data-chapter-order="<?php echo $chapters_count; ?>" class="slick_inner-containner wptr_a-card wptr_px-xy-3 wptr_py-yx-3 wptr_slick-down-icon rdtr-chapter-slick">
                                                    <h3 class="wptr_mtop-0  wptr_h3_3 wptr_montserrat-font wptr_sr-number wptr_font-wt-light-bold">
                                                        <?php
                                                        echo $module_count . "." . $chapters_count;
                                                        $chapters_count++;
                                                        ?>
                                                    </h3>
                                                    <h5 class="wptr_p wptr_pt-sans-font wptr_mbtm-0 wptr_txt-truncate chapter-title wptr_montserrat-font"><?php echo $chapter_detail->post_title; ?></h5>
                                                    <p class="silk slider_connt font-open-sans">
                                                        <?php echo substr($chapter_detail->post_content, 0, 50) . "..."; ?>
                                                    </p>

                                                </a>
                                                <!-- single chapter ends -->
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    <?php } else {
                                        ?>
                                        <div class="wptr_description-slider wptr-slider-layout">
                                            <p>No Chapters found</p>
                                        </div>
                                    <?php }
                                    ?>
                                </div>

                                <div class="exercise-area" id="module_exercise_area_<?php echo $module_count; ?>">
                                    <?php
                                    $ch_id = $chapter_id = $first_ch;
                                    $mod_count = $module_count;
                                    $ch_count = 1;
                                    $exercises = $this->wpl_rd_get_exercises_by_chapter($chapter_id);
                                    $slick_count = isset($slick) ? intval($slick) : 0;

                                    if (count($exercises) > 0) {
                                        ?>
                                        <!-- exercise slider -->
                                        <div class="wptr_row-section wptr_mx-xy-0 wptr_pad-3 exercise_slidr">
                                            <div class="rdtr_create_slick_<?php echo $slick_count; ?> wptr_description-slider-inner wptr-slider-layout wptr_pbtm-3 wptr_width-100">
                                                <?php
                                                $exercise_count = 1;
                                                foreach ($exercises as $inx => $stx) {

                                                    $exercise_detail = get_post($stx->post_id);
                                                    if (empty($exercise_detail->post_title)) {
                                                        continue;
                                                    }
                                                    ?>

                                                    <!-- single exercise section -->
                                                    <div class="rdtr-single-exercise">
                                                        <h5 class="wptr_montserrat-font wptr_h5_5 wptr_sr-number wptr_font-wt-light-bold">
                                                            <?php
                                                            $exercise_seq_id = $mod_count . "." . $ch_count . "." . $exercise_count;
                                                            echo $exercise_seq_id;
                                                            ?>
                                                        </h5>

                                                        <div class="wptr_a-card wptr_pad-3">
                                                            <div style="" class="wptr_margin-0 wptr_mbtm-0"> 
                                                                <div class="wptr_img-fluid rdtr-exe-area">
                                                                    <?php
                                                                    $full_url = $this->wpl_rd_get_full_url();
                                                                    $full_url_segments_array = explode("&", $full_url);
                                                                    $get_rpage_param = explode("?", $full_url_segments_array[0]);
                                                                    $ref_url = $get_rpage_param[0] . "?rpage=course_detail&" . $full_url_segments_array[1];
                                                                    $exercise_id = $stx->post_id;
                                                                    $chapter_id_found = get_post_meta($exercise_id, "dd_chapter_id_box_post_type", true);
                                                                    $module_id_found = get_post_meta($chapter_id_found, "dd_module_id_box_post_type", true);

                                                                    $ref_url .= '#type=module#modid=' . $module_id_found . '#ch=chapter#chid=' . $chapter_id_found . '#sub=exercise#exeid=' . $stx->post_id;
                                                                    ?>
                                                                    <h4 class="wptr_text-trunc  wptr_montserrat-font slider_heading"><a href="<?php echo $ref_url; ?>"><?php echo ucfirst($exercise_detail->post_title); ?></a></h4>
                                                                    <div class="total-sections font-open-sans">Total sections: <?php echo count($this->wpl_rd_get_exercise_total_sections($stx->post_id)); ?></div>
                                                                    <div class="total-hours font-open-sans">Total hours: <?php echo get_post_meta($stx->post_id, "txt_exercise_complete_hour", true); ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- single exercise ends -->
                                                    <?php
                                                    $exercise_count++;
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <!-- here inner slider close -->
                                        <?php
                                    } else {
                                        ?>
                                        <p class="exercise-not-found">No Exercise found.</p>
                                        <?php
                                    }
                                    ?>

                                </div>
                                <?php
                                $module_count++;
                            }
                        }
                        ?>

                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
}
?>
