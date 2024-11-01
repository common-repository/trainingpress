<div class="wptr-rud-info" id="course-info">
    <h4 class="datatable-heading">Chapter: <?php echo $course_name; ?></h4>
    <div class="chapter-information">
        <?php
        global $user_ID;
        $total_exercise = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT ID, post_title from $wpdb->posts post WHERE ID IN ( SELECT post_id from $wpdb->postmeta pmeta WHERE meta_key = %s AND meta_value = %d ) AND post.post_status = %s", 'dd_chapter_id_box_post_type', $chapter_id, 'publish'
                )
        );
        ?>
        <p class="total-modules text-info">Total Exercise: <?php echo isset($total_exercise) ? count($total_exercise) : 0; ?></p>
        <?php
        $total_uploads = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT file from " . $this->table_activator->wpl_rd_user_uploaded_files_tbl() . " WHERE user_id = %d AND chapter_id = %d AND status = %d", $user_id, $chapter_id, 1
                )
        );
        $total_uploads_found = 0;
        if (!empty($total_uploads) && count($total_uploads) > 0) {
            $total_uploads_found = count($total_uploads);
        }
        ?>
        <p class="completion text-info">Total Uploads: <?php echo $total_uploads_found; ?></p>
    </div>
</div>

<div class="wptr-rud-info data-table-area">

    <?php if (!empty($total_exercise)) { ?>
        <table id="" class="display modules-list-course" style="width:100%">
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th>Exercise</th>
                    <th>Exercise Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $exercise_count = 1;
                foreach ($total_exercise as $inx => $stx) {
                    $exercise_name = $stx->post_title;
                    $exercise_id = $stx->ID;
                    ?>

                    <tr>
                        <td><?php echo $exercise_count++; ?></td>
                        <td><?php echo $exercise_name; ?></td>
                        <td>
                            <?php
                            $exercise_status = $wpdb->get_row(
                                    "SELECT * from " . $this->table_activator->wpl_rd_course_progress_tbl() . " WHERE user_id = $user_id AND exercise_id = $exercise_id AND exercise_status = 1", ARRAY_A
                            );

                            if (!empty($exercise_status)) {
                                echo 'Completed';
                            } else {
                                echo 'In Progress';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $status = $this->wpl_rd_has_users_started_exercise($user_id, $exercise_id);
                            if (empty($status)) {
                                $exercise_status = $this->rdtr_check_exercise_status($exercise_id, $user_id);
                                if (!empty($exercise_status)) {

                                    if ($exercise_status->exercise_status == 1) {

                                        echo 'No Preview available';
                                    } else {
                                        ?>
                                        <button class="button button-primary button-large show-exercise-progress-dialog" data-id="<?php echo $stx->ID; ?>" data-user="<?php echo $user_id; ?>" type="button">View</button>
                                        <?php
                                    }
                                }
                            } else {
                                ?>
                                <button class="button button-primary button-large show-exercise-progress-dialog" data-id="<?php echo $stx->ID; ?>" data-user="<?php echo $user_id; ?>" type="button">View</button>
                                <?php
                            }
                            ?>

                        </td>
                    </tr>
                    <?php
                }
                ?>

            </tbody>
        </table>
    <?php } ?>
</div>

<div id="exercise-progress-view" class="ui-custome-modal-admin" title="Exercise Progress view" style="display: none;">
    <button type="button" class="ui-dialog-titlebar-close custome_btn"></button>
    <div id="rdtr-exercise-content"></div>
</div>