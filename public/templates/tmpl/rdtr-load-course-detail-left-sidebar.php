<?php
$total_course_exercise = array();
$completed_exercise_count = 0;
$style = '';
?>
<div class="side-course-layer wptr_MainMenu">
    <div class="wptr_list-group">
        <?php
        $modules = $this->wpl_rd_get_modules_by_course($c_id);
        $lock_exe = 1;
        $mod_count_tr = 1;
        if (count($modules) > 0) {
            foreach ($modules as $index => $module) {

                if (empty($module)) {
                    continue;
                }
                ?>
                <div class="mod-area-section">
                    <div class="rdtr-module-anchor-link">
                        <a href="javascript:void(0)" title='<?php echo $module->post_title; ?>' data-id="<?php echo $module->ID; ?>" data-type="module" class="<?php
                        if ($this->wpl_rd_has_course_locked($c_id) && $mod_count_tr > 1) {
                            // echo 'call-ajax-to-check';
                        }
                        ?> wptr_list-group-item wptr_Montserrat-font rdtr-read-section rdtr-mod-sec">

                            <div class="wptr_pt-2 wptr-mod-text wptr_txt-truncate wptr_px-right"><?php echo $module->post_title; ?></div> 
                            <?php
                            $chapters = $this->wpl_rd_get_chapters_by_module_id($module->ID);
                            if (count($chapters) > 0) {
                                ?>
                                <div class="wptr_ml-auto wptr-d-flex align_v_center wptr-mr-sapce">

                                    <?php
                                    $ch_query_module = $wpdb->get_results(
                                            $wpdb->prepare(
                                                    "SELECT post_id from $wpdb->postmeta pmeta INNER JOIN $wpdb->posts post ON post.ID = pmeta.post_id where pmeta.meta_value IN ( SELECT post_id from $wpdb->postmeta WHERE meta_key = %s AND meta_value = %d) AND pmeta.meta_key = %s AND post.post_status = %s", 'dd_module_id_box_post_type', $module->ID, 'dd_chapter_id_box_post_type', 'publish'
                                            ), ARRAY_A
                                    );
                                    $all_tick_module = 0;
                                    if (!empty($ch_query_module)) {
                                        $exids = array_column($ch_query_module, "post_id");
                                        $exids_data = implode(",", $exids);

                                        $completed_exercise_status = $wpdb->get_row(
                                                $wpdb->prepare(
                                                        "SELECT count(id) as total_exercises from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE user_id = %d AND exercise_status = %d AND exercise_id IN ($exids_data)", $user_ID, 1
                                                )
                                        );

                                        $total_completed = isset($completed_exercise_status->total_exercises) ? $completed_exercise_status->total_exercises : "";

                                        if ($total_completed == count($exids)) {
                                            $all_tick_module = 1;
                                        }
                                    }
                                    ?>

                                    <?php
                                    //lock symbol for module

                                    if ($this->wpl_rd_has_course_locked($c_id)) {
                                        if ($all_tick_module) {
                                            
                                        } else {
                                            echo '<i class="mdi mdi-lock pr-2 pron-lock-icon px-right"></i>';
                                        }
                                    }

                                    if ($all_tick_module) {
                                        echo '<i class="mdi mdi-check-all read-tick-icon  px-right-single"></i>';
                                    }
                                    ?>

                                </div>
                                <?php
                            }
                            ?>
                        </a>
                        <?php
                        $chapters = $this->wpl_rd_get_chapters_by_module_id($module->ID);

                        if (count($chapters) > 0) {
                            ?>
                            <span trigger-id="<?php echo $module->ID ?>" class="rdtr-down-icon rdtr-module-panel" data-target="#<?php echo $module->post_name ?>_<?php echo $module->ID ?>">
                                <i class="mdi mdi-chevron-down font-nomal_icon wptr-drop-rotate-close-icon wptr-drop-rotate-icons"></i>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                    <div id="<?php echo $module->post_name ?>_<?php echo $module->ID ?>" class="wptr-rud-collapse-menu cr-detail mod_<?php echo $module->ID; ?>">
                        <?php
                        if (count($chapters) > 0) {
                            ?>
                            <ul class="wptr_ul">
                                <?php
                                foreach ($chapters as $inx => $chapter) {
                                    if (empty($chapter)) {
                                        continue;
                                    }
                                    ?>
                                    <li class="rdtr-ch-sec">
                                        <div class="chapter-anchor-link">
                                            <a href="javascript:void(0)" title="<?php echo $chapter->post_title; ?>" data-type="chapter" data-id="<?php echo $chapter->ID; ?>" class="wptr_list-group-item wptr_bg-transparent wptr_pl-0 border-0 wptr_pb-0 rdtr-read-section">
                                                <i class="mdi mdi-file-document-box-outline icon-normalize"></i>
                                                <div class="wptr_height-100 wptr_ml-3 wptr_Montserrat-font wptr-chapter-text wptr_txt-truncate wptr_px-right">
                                                    <?php echo $chapter->post_title; ?>
                                                </div>
                                                <?php
                                                $exes = $this->wpl_rd_get_exercises_by_chapter_id($chapter->ID);

                                                $ch_query = $wpdb->get_results(
                                                        $wpdb->prepare(
                                                                "SELECT post_id from $wpdb->postmeta pmeta INNER JOIN $wpdb->posts post ON post.ID = pmeta.post_id where pmeta.meta_value = %d AND pmeta.meta_key = %s AND post.post_status = %s", $chapter->ID, 'dd_chapter_id_box_post_type', 'publish'
                                                        ), ARRAY_A
                                                );
                                                $all_tick = 0;
                                                if (!empty($ch_query)) {
                                                    $exids = array_column($ch_query, "post_id");
                                                    $exids_data = implode(",", $exids);

                                                    $completed_exercise_status = $wpdb->get_row(
                                                            $wpdb->prepare(
                                                                    "SELECT count(id) as total_exercises from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE user_id = %d AND exercise_status = %d AND exercise_id IN ($exids_data)", $user_ID, 1
                                                            )
                                                    );
                                                    $total_completed = isset($completed_exercise_status->total_exercises) ? $completed_exercise_status->total_exercises : "";

                                                    if ($total_completed == count($exids)) {
                                                        $all_tick = 1;
                                                    }
                                                }
                                                ?>
                                                <div class="wptr_ml-auto wptr-d-flex align_v_center wptr-mr-sapce">
                                                    <?php
                                                    //lock symbol for chapters
                                                    if ($this->wpl_rd_has_course_locked($c_id)) {
                                                        if ($all_tick) {
                                                            
                                                        } else {
                                                            echo '<i class="mdi mdi-lock pr-2 pron-lock-icon px-right"></i>';
                                                        }
                                                    }

                                                    if ($all_tick) {
                                                        echo '<i class="mdi mdi-check-all read-tick-icon  px-right-single"></i>';
                                                    }
                                                    ?>
                                                </div>

                                            </a> 
                                            <?php
                                            $exercises = $this->wpl_rd_get_exercises_by_chapter_id($chapter->ID);

                                            if (count($exercises) > 0) {
                                                ?>
                                                <span trigger-id="<?php echo $chapter->ID ?>" class="rdtr-chapter-panel wptr_px-left" data-target="#<?php echo $chapter->post_name; ?>_<?php echo $chapter->ID; ?>">
                                                    <i class="mdi mdi-chevron-down wptr_ml-auto font-nomal_icon wptr-drop-rotate-close-icon wptr-drop-rotate-icons"></i>
                                                </span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <?php
                                        if (count($exercises) > 0) {
                                            ?>
                                            <ul id="<?php echo $chapter->post_name; ?>_<?php echo $chapter->ID; ?>" data-chapter="<?php echo $chapter->ID; ?>" class="rdtr-ul-chapter-ex">
                                                <?php
                                                foreach ($exercises as $i => $ex) {
                                                    if (empty($ex->post_title)) {
                                                        continue;
                                                    }
                                                    ?>
                                                    <li>
                                                        <?php
                                                        $complete_status = $this->wpl_rd_get_user_exercise_marked_status($ex->ID);
                                                        $status = isset($complete_status->exercise_status) ? $complete_status->exercise_status : "";
                                                        ?>
                                                        <a href="javascript:void(0)" title="<?php echo $ex->post_title; ?>" data-id="<?php echo $ex->ID; ?>" class="<?php
                                                        if ($status == 1) {
                                                            $completed_exercise_count++;
                                                        } else {
                                                            if ($lock_exe == 1) {
                                                                
                                                            } else {
                                                                if ($this->wpl_rd_has_course_locked($c_id)) {
                                                                    //echo 'call-ajax-to-check';
                                                                }
                                                            }
                                                        }

                                                        $total_course_exercise[] = array("id" => $ex->ID, "status" => $status);
                                                        ?> next_click_ch_exe create-excercise wptr_list-group-item wptr_bg-transparent wptr_pl-0 border-0 wptr_pb-0 wptr_items-start rdtr-start-exercise">
                                                            <i class="mdi mdi-album mr-1"></i>
                                                            <div class="inner_li wptr-chapter-text wptr_txt-truncate wptr_px-right wptr-d-block"><?php echo $ex->post_title; ?></div>
                                                            <div class="wptr_ml-auto wptr-d-flex align_v_center wptr-mr-sapce">
                                                                <?php
                                                                if ($this->wpl_rd_has_course_locked($c_id)) {
                                                                    //lock symbol for exercise
                                                                    $complete_status = $this->wpl_rd_get_user_exercise_marked_status($ex->ID);
                                                                    $status = isset($complete_status->exercise_status) ? $complete_status->exercise_status : "";

                                                                    if ($status) {
                                                                        
                                                                    } else {
                                                                        echo '<i class="mdi mdi-lock pr-2 pron-lock-icon px-right"></i>';
                                                                    }
                                                                }

                                                                if ($status == 1) {
                                                                    // exercise complete status
                                                                    ?>
                                                                    <i class="mdi mdi-check-all read-tick-icon  px-right-single"></i>
                                                                    <?php
                                                                }
                                                                ?>

                                                            </div>
                                                        </a>
                                                    </li>
                                                    <?php
                                                    $lock_exe++;
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        $has_chapter_assignmment = get_post_meta($chapter->ID, "rdb_has_chapter_assignmment", true);
                                        if ($has_chapter_assignmment == "yes") {
                                            ?>
                                            <span class="ch-file-upd">
                                                <i class="mdi mdi-cloud-upload font-nomal_icon"></i><a href="javascript:void(0)" class="link-to-upload-files wptr_list-group-item" chapter-id="<?php echo $chapter->ID; ?>">Upload Chapter Assignment</a>
                                            </span>
                                            <?php
                                        }
                                        ?>
                                    </li>

                                    <?php
                                }
                                ?>
                            </ul>

                            <?php
                        }
                        ?>

                    </div>
                </div>
                <?php
                $mod_count_tr++;
            }
        }
        ?>
    </div>      
</div>
<script>
    var exercisesArray = <?php echo json_encode($total_course_exercise); ?>;
    var total_completed_exercise_length = <?php echo $completed_exercise_count; ?>
</script>