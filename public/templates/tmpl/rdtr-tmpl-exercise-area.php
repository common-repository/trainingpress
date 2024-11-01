<?php
/**
 * This File is for to load exercise section.
 * @author	Rudra Innnovative Software 
 * @package	training/templates/tmpl/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;

$chapter_id = isset($ch_id) ? intval($ch_id) : "";
$mod_count = isset($mod_count) ? intval($mod_count) : "";
$ch_count = isset($ch_count) ? intval($ch_count) : "";
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
                        <figure style="" class="wptr_margin-0 wptr_mbtm-0"> 
                            <div class="wptr_img-fluid rdtr-exe-area">
                                <?php
                                $full_url_segments_array = explode("&", $full_url);
                                $get_rpage_param = explode("?", $full_url_segments_array[0]);
                                $ref_url = $get_rpage_param[0] . "?rpage=course_detail&" . $full_url_segments_array[1];
                                $exercise_id = $stx->post_id;
                                $chapter_id_found = get_post_meta($exercise_id, "dd_chapter_id_box_post_type", true);
                                $module_id_found = get_post_meta($chapter_id_found, "dd_module_id_box_post_type", true);

                                $ref_url .= '#type=module#modid=' . $module_id_found . '#ch=chapter#chid=' . $chapter_id_found . '#sub=exercise#exeid=' . $stx->post_id;
                                ?>
                                <h4 class="wptr_text-trunc  wptr_montserrat-font slider_heading"><a href="<?php echo $ref_url; ?>"><?php echo ucfirst($exercise_detail->post_title); ?></a></h4>
                                <span class="total-sections">Total sections: <?php echo count($this->wpl_rd_get_exercise_total_sections($stx->post_id)); ?></span>
                                <span class="total-hours">Total hours: <?php echo get_post_meta($stx->post_id, "txt_exercise_complete_hour", true); ?></span>
                            </div>
                        </figure>
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
