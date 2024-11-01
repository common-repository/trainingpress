<?php
/**
 * This File for exercise progress view.
 * @author	Rudra Innnovative Software 
 * @package	training/partials/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;
?>
<div class="user-ex-progress">
    <div class="rdtr-send-survey-topbar"><h3 class="rdtr-send-survey-head">Exercise progress view</h3>
    </div>
    <div class="rdtr-survey-content">
        <?php
        $exercise_data = get_post_meta($exercise_id, "rd_wpl_exercise_section", true);

        $status = $this->wpl_rd_has_users_started_exercise($user_id, $exercise_id);

        if (!empty($status)) {

            if (!empty($exercise_data)) {
                $exercise_data = json_decode($exercise_data, true);
                $order = $exercise_data['order'];
                $section = $exercise_data['section'];

                if (count($order) > 0) {

                    foreach ($order as $index => $sec) {

                        if (strpos($sec, '_para_') !== false) {
                            ?>
                            <p>
                                <?php echo $section['paragraph'][$sec]['value']; ?>
                            </p>
                            <?php
                        } elseif (strpos($sec, '_file_') !== false) {
                            ?>

                            <?php
                        } elseif (strpos($sec, '_mcq_') !== false) {
                            ?>
                            <div class="mcq_area">
                                <div>

                                    <p>
                                        <label class='rd-lbl-bold'>Question:</label>
                                        <?php echo $section['mcq'][$sec]['question']; ?>
                                    </p>
                                </div>
                                <div>
                                    <span class="mcq_options rd-lbl-bold">Options</span>
                                    <ul>
                                        <?php
                                        if (count($section['mcq'][$sec]['options'])) {
                                            $count = 1;
                                            foreach ($section['mcq'][$sec]['options'] as $inx => $stx) {
                                                if (empty($stx))
                                                    continue;
                                                ?>
                                                <li><?php echo $count++; ?>. <?php echo $stx ?></li>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </ul>
                                </div>
                                <div class="ques-answer-section">
                                    <div class="question_ans">
                                        <span class="mcq_answer rd-lbl-bold">Correct Answer: </span> <span class="mcq_answer_corect_ans">
                                            <?php
                                            $ans_index = $section['mcq'][$sec]['correct_answers'];
                                            if (count($ans_index) > 0) {

                                                foreach ($ans_index as $inx => $stx) {
                                                    echo $section['mcq'][$sec]['options'][$stx] . ", ";
                                                }
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="question_ans">
                                        <span class="mcq_answer rd-lbl-bold">User's Answer: </span> 
                                        <span class="mcq_answer_corect_ans"><?php
                                            $users_data = $this->wpl_rd_get_users_answer($user_id, $sec, $exercise_id);
                                            $user_resp = explode(",", $users_data->exe_answer);
                                            if (count($user_resp) > 0) {

                                                foreach ($user_resp as $inx => $stx) {
                                                    echo $section['mcq'][$sec]['options'][$stx] . ", ";
                                                }
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } elseif (strpos($sec, '_single_') !== false) {
                            ?>
                            <div class="mcq_area">
                                <div>
                                    <p>
                                        <label class='rd-lbl-bold'>Question:</label>
                                        <?php echo $section['single'][$sec]['question']; ?>
                                    </p>
                                </div>
                                <div>
                                    <span class="mcq_options rd-lbl-bold">Options</span>
                                    <ul>
                                        <?php
                                        if (count($section['single'][$sec]['options'])) {
                                            $count = 1;
                                            foreach ($section['single'][$sec]['options'] as $inx => $stx) {
                                                if (empty($stx))
                                                    continue;
                                                ?>
                                                <li><?php echo $count++; ?>. <?php echo $stx ?></li>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </ul>
                                </div>
                                <div class="ques-answer-section">
                                    <span class="mcq_answer rd-lbl-bold">Correct Answer: </span> 

                                    <?php
                                    $correct_answer = $section['single'][$sec]['correct_answer'];
                                    
                                    // if (!empty($correct_answer))
                                    if ($users_data->exe_answer >=0){
                                        echo $section['single'][$sec]['options'][$correct_answer];
                                    }
                                    ?>
                                    <span class="mcq_answer rd-lbl-bold">User's Answer: </span> 
                                    <?php
                                    $users_data = $this->wpl_rd_get_users_answer($user_id, $sec, $exercise_id);
                                    // if (!empty($users_data->exe_answer)){
                                    if ($users_data->exe_answer >=0){
                                        echo $section['single'][$sec]['options'][$users_data->exe_answer];
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        } elseif (strpos($sec, '_poll_') !== false) {
                            ?>
                            <div class="mcq_area">
                                <div>
                                    <p>
                                        <label class='rd-lbl-bold'>Question:</label>
                                        <?php echo $section['poll'][$sec]['question']; ?>
                                    </p>
                                </div>
                                <div>
                                    <span class="mcq_options rd-lbl-bold">Options</span>
                                    <ul>
                                        <?php
                                        if (count($section['poll'][$sec]['options'])) {
                                            $count = 1;
                                            foreach ($section['poll'][$sec]['options'] as $inx => $stx) {
                                                ?>
                                                <li><?php echo $count++; ?>. <?php echo $stx ?></li>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </ul>
                                </div>
                                <div class="ques-answer-section">
                                    <span class="mcq_answer rd-lbl-bold">User's Answer: </span> 
                                    <?php
                                    $users_data = $this->wpl_rd_get_users_answer($user_id, $sec, $exercise_id);
                                    echo $users_data->exe_answer;
                                    ?>
                                </div>
                            </div>
                            <?php
                        } elseif (strpos($sec, '_reflection_') !== false) {
                            ?>
                            <div>
                                <p>
                                    <?php echo $section['reflection'][$sec]['value']; ?>
                                </p>
                            </div>
                            <div class="ques-answer-section">
                                <span class="mcq_answer">User's Answer: </span> 
                                <?php
                                $users_data = $this->wpl_rd_get_users_answer($user_id, $sec, $exercise_id);
                                echo $users_data->exe_answer;
                                ?>
                            </div>
                            <?php
                        }
                    }
                }
            }
        } else {
            echo "<h4>Exercise not yet started.</h4>";
        }
        ?>
    </div>
</div>