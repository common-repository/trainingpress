<?php
/**
 * This File to load exercise sections.
 * @author	Rudra Innnovative Software 
 * @package	training/public/templates/tmpl/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;
?>

<div class="my-course_contnet_area" >
    <div id="wptr-rd-loader-area"></div>
    <h3>
        <?php echo ucwords($exercise_detail['post_title']); ?>
    </h3>
    <div id="exercise-parent-div">

        <?php
        global $user_ID;

        $exercise_percentage = $this->wpl_rdtr_has_total_exercise_sections_done($exercise_detail['ID']);

        $sections = $item['sections'];

        foreach ($sections as $section) {
            if ($section['show'] == "paragraph") {
                ?>
                <p class="excercise_paragraph" ><?php echo $section['value']; ?></p>
                <?php
            } elseif ($section['show'] == "gallery") {
                ?>
                <div class='rdtr-question-image-sec'>
                    <?php
                    $prev_file = $section['value'];
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
                </div>
                <?php 
            } elseif ($section['show'] == "youtube") {
                ?>
                <div class='rdtr-youtube-video-player'>
                    <?php global $wp_embed;?>
                    <?php /*echo do_shortcode('[embed width="300" height="250"]' . $section["value"] . '[/embed]');*/  ?>
                    <?php echo $wp_embed->run_shortcode('[embed width="300" height="250"]' . $section["value"] . '[/embed]'); ?>
               
                </div>
                <?php
            } elseif ($section['show'] == "mcq") {

                $user_data = $this->wpl_rd_get_exercise_status_proccessed_by_user($exercise_detail['ID'], $section['name'], 'mcq');

                $selected_options = array();
                if (!empty($user_data)) {
                    $selected_options = explode(",", $user_data->exe_answer);
                }
                ?>
                <div class="f_question question">
                    <form class="<?php echo $section['show']; ?>_<?php echo $section['name']; ?>" method="post" action="javascript:void(0)">
                        <label>Question: <?php echo $section['question']; ?></label>
                        <div class="ans_opetions <?php
                        if (!empty($user_data)) {
                            echo 'question-disable-zone';
                        }
                        ?>">
                                 <?php
                                 if (count($section['options']) > 0) {

                                     $options_index = $section['option_index'];
                                     $write_index = 0;

                                     foreach ($section['options'] as $option) {

                                         $selected = '';
                                         if (in_array($options_index[$write_index], $selected_options)) {
                                             $selected = 'checked';
                                         }
                                         ?>
                                    <div class="ans_r_options">
                                        <label class="rud_check"><?php echo $option ?>
                                            <input type="checkbox" <?php echo $selected; ?> name="rdb_mcq[]" value="<?php echo $options_index[$write_index] ?>">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <?php
                                    $write_index++;
                                }
                            }
                            ?>
                        </div>
                        <input type="hidden" value="<?php echo $section['show']; ?>" name="section_type"/>
                        <input type="hidden" value="<?php echo $section['name']; ?>" name="section_name"/>
                        <?php
                        if (empty($user_data)) {
                            if ($user_ID > 0) {
                                ?>
                                <button class="btn-submit-exercise wptr_course-btn" exid="<?php echo $exercise_detail['ID'] ?>" section-name="<?php echo $section['name']; ?>" section-type="<?php echo $section['show']; ?>">Submit</button>
                                <?php
                            }
                        } else {
                            $saved_ans_options = explode(",", $section['answer']);
                            $question_options = $section['options'];
                            $system_answer = '';

                            foreach ($saved_ans_options as $item_index) {
                                $system_answer .= $question_options[$item_index] . ", ";
                            }
                            ?>
                            <span class="ch-correct-answer">
                                Correct Answer: <?php echo rtrim($system_answer, ","); ?>
                            </span>
                            <br/>
                            <?php echo $this->wpl_rd_get_saved_exercise_answer($user_ID, $exercise_detail['ID'], $section['name']); ?>
                        <?php }
                        ?>
                    </form>
                </div>
                <?php
            } elseif ($section['show'] == "single") {
                $user_data = $this->wpl_rd_get_exercise_status_proccessed_by_user($exercise_detail['ID'], $section['name'], 'single');
                $selected_option = array();

                if (!empty($user_data)) {
                    $selected_option = $user_data->exe_answer;
                }
                ?>
                <div class="s_question question" >
                    <form class="<?php echo $section['show']; ?>_<?php echo $section['name']; ?>" method="post" action="javascript:void(0)">
                        <label>Question: <?php echo $section['question']; ?></label>
                        <div class="ans_options <?php
                        if (!empty($user_data)) {
                            echo 'question-disable-zone';
                        }
                        ?>">
                                 <?php
                                 if (count($section['options']) > 0) {

                                     $options_index = $section['option_index'];
                                     $write_index = 0;

                                     foreach ($section['options'] as $inx => $option) {

                                         $selected = '';

                                         if ($selected_option == $options_index[$write_index]) {
                                             $selected = 'checked';
                                         }
                                         ?>
                                    <div class="ans_r_options">
                                        <!-- <input type="radio" name="rdb_single" value="<?php echo $option ?>"/> <?php echo $option ?> -->

                                        <label class="rud_check radio"> <?php echo $option ?>
                                            <input type="radio" <?php echo $selected; ?> name="rdb_single" value="<?php echo $options_index[$write_index] ?>"/>
                                            <span class="checkmark"></span>
                                        </label>

                                    </div>
                                    <?php
                                    $write_index++;
                                }
                            }
                            ?>
                        </div>
                        <input type="hidden" value="<?php echo $section['show']; ?>" name="section_type"/>
                        <input type="hidden" value="<?php echo $section['name']; ?>" name="section_name"/>
                        <?php
                        if (empty($user_data)) {
                            if ($user_ID > 0) {
                                ?>

                                <button class="btn-submit-exercise wptr_course-btn" exid="<?php echo $exercise_detail['ID'] ?>" section-name="<?php echo $section['name']; ?>" section-type="<?php echo $section['show']; ?>">Submit</button>
                                <?php
                            }
                        } else {
                            $saved_ans_options = explode(",", $section['answer']);
                            $question_options = $section['options'];
                            $system_answer = '';
                           
                            foreach ($saved_ans_options as $item_index) {
                               
                                $system_answer = $question_options[$item_index];
                            }
                            ?>
                            <span class="ch-correct-answer">
                                Correct Answer: <?php echo $system_answer; ?>
                            </span>
                            <br/>
                            <?php echo $this->wpl_rd_get_saved_exercise_answer($user_ID, $exercise_detail['ID'], $section['name']); ?>
                        <?php }
                        ?>
                    </form>
                </div>
                <?php
            } elseif ($section['show'] == "poll") {
                $user_data = $this->wpl_rd_get_exercise_status_proccessed_by_user($exercise_detail['ID'], $section['name'], 'poll');
                $selected_option = array();

                if (!empty($user_data)) {
                    $selected_option = $user_data->exe_answer;
                }
                ?>
                <div class="th_question question">
                    <form class="<?php echo $section['show']; ?>_<?php echo $section['name']; ?>" method="post" action="javascript:void(0)">
                        <label>Question: <?php echo $section['question']; ?></label>
                        <div class="ans_options <?php
                        if (!empty($user_data)) {
                            echo 'question-disable-zone';
                        }
                        ?>">
                                 <?php
                                 if (count($section['options']) > 0) {

                                     foreach ($section['options'] as $option) {
                                         $selected = '';
                                         if ($option == $selected_option) {
                                             $selected = 'checked';
                                         }
                                         ?>
                                    <div class="ans_r_options">

                                        <label class="rud_check radio"> <?php echo $option ?>
                                            <input type="radio" <?php echo $selected; ?> name="rdb_poll" value="<?php echo $option ?>"/>
                                            <span class="checkmark"></span>
                                        </label>
                                                                                                                                                                                                         <!--  <input type="radio" name="rdb_poll" value="<?php echo $option ?>"/> <?php echo $option ?> --> </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <input type="hidden" value="<?php echo $section['show']; ?>" name="section_type"/>
                        <input type="hidden" value="<?php echo $section['name']; ?>" name="section_name"/>
                        <?php
                        if (empty($selected_option)) {
                            if ($user_ID > 0) {
                                ?>
                                <button class="btn-submit-exercise wptr_course-btn" exid="<?php echo $exercise_detail['ID'] ?>" section-name="<?php echo $section['name']; ?>" section-type="<?php echo $section['show']; ?>">Submit</button>
                                <?php
                            }
                        } else {
                            $poll = $this->wpl_rd_calculate_poll_review($exercise_detail['ID']);
                            if (!empty($poll)) {
                                ?>
                                <h4>
                                    Total votes: <?php echo $poll['total_votes']; ?>
                                </h4>
                                <?php
                                foreach ($poll['options'] as $index => $pl) {
                                    ?>
                                    <div class="poll-myProgress" style="width:<?php echo $pl['total_percentage'] ?>%">
                                        <div class="poll-myBar"><?php echo $pl['name'] ?> - <?php echo $pl['total_percentage'] ?>%</div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>

                    </form>
                </div>
                <?php
            } elseif ($section['show'] == "reflection") {
                $user_data = $this->wpl_rd_get_exercise_status_proccessed_by_user($exercise_detail['ID'], $section['name'], 'reflection');
                ?>
                <div class="fo_question question <?php
                if (!empty($user_data)) {
                    echo 'question-disable-zone';
                }
                ?>">
                    <form class="<?php echo $section['show']; ?>_<?php echo $section['name']; ?>" method="post" action="javascript:void(0)">
                        <label>Question: <?php echo $section['value']; ?></label>
                        <textarea name="reflection_answer" class="textarea_rdr"><?php echo isset($user_data->exe_answer) ? $user_data->exe_answer : "" ?></textarea>
                        <input type="hidden" value="<?php echo $section['show']; ?>" name="section_type"/>
                        <input type="hidden" value="<?php echo $section['name']; ?>" name="section_name"/>
                        <?php
                        if (empty($user_data)) {
                            if ($user_ID > 0) {
                                ?>
                                <button class="btn-submit-exercise wptr_course-btn" exid="<?php echo $exercise_detail['ID'] ?>" section-name="<?php echo $section['name']; ?>" section-type="<?php echo $section['show']; ?>">Submit</button>
                                <?php
                            }
                        }
                        ?>
                    </form>
                </div>
                <?php
            }
        }

        if ($user_ID > 0 && $show_next) {

            $complete_status = $this->wpl_rd_get_user_exercise_marked_status($exercise_detail['ID']);
            $status = isset($complete_status->exercise_status) ? $complete_status->exercise_status : "";

            if ($status) {
                ?>
                <div class="wptr-text-right">
                    <button class="ex-cmpl wptr_course-btn"><i class="mdi mdi-check-outline"></i> Completed</button>
                </div>

                <?php
            } else {
                ?>
                <div class="wptr-text-right">
                    <button class="wptr_course-btn rdtr-complete-exercise">Complete</button>
                </div>

                <?php
            }
        }
        ?>  

    </div>
</div>