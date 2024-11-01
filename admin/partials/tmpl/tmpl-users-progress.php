<div class="wptr-rud-info" id="course-info">
    <h4 class="datatable-heading">Course: <?php echo $course_name; ?></h4>
    <?php
    $total_modules = $wpdb->get_results(
            $wpdb->prepare(
                    "SELECT ID, post_title from $wpdb->posts post WHERE ID IN ( SELECT post_id from $wpdb->postmeta pmeta WHERE meta_key = %s AND meta_value = %d ) AND post.post_status = %s", 'dd_course_box_post_type', $course_id, 'publish'
            )
    );
    ?>
    <div class="chapter-information">
        <p class="total-modules text-info"><span class="static-text">Total Modules:</span> <?php echo isset($total_modules) ? count($total_modules) : 0; ?></p>      
        <p class="completion text-info"><span class="static-text">Percentage Complete:</span> <?php echo $this->wpl_rd_calculate_user_course_score($user_id, $course_id); ?>%</p>
        <p class="course-score text-info"><span class="static-text">Course Score:</span> <?php echo $this->wpl_rd_calculate_user_course_score($user_id, $course_id); ?></p>
    </div>
</div>

<?php
if (!empty($total_modules)) {

    foreach ($total_modules as $inx => $stx) {
        $module_name = $stx->post_title;
        $module_id = $stx->ID;
        ?>
        <div class="wptr-rud-info data-table-area">
            <h4 class="datatable-heading"><?php echo $module_name . " Progress view"; ?></h4>

            <table class="display modules-list-course" style="width:100%">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Chapter</th>
                        <th>Chapter Uploads</th>
                        <th>Total Exercise</th>
                        <th>Exercise Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    global $user_ID;
                    $total_chapters = $wpdb->get_results(
                            $wpdb->prepare(
                                    "SELECT ID, post_title from $wpdb->posts post WHERE ID IN ( SELECT post_id from $wpdb->postmeta pmeta WHERE meta_key = %s AND meta_value = %d ) AND post.post_status = %s", 'dd_module_id_box_post_type', $module_id, 'publish'
                            )
                    );

                    if (count($total_chapters) > 0) {

                        $chapter_count = 1;

                        foreach ($total_chapters as $index => $chapter) {

                            $total_exercise = $wpdb->get_results(
                                    $wpdb->prepare(
                                            "SELECT ID, post_title from $wpdb->posts post WHERE ID IN ( SELECT post_id from $wpdb->postmeta pmeta WHERE meta_key = %s AND meta_value = %d ) AND post.post_status = %s", 'dd_chapter_id_box_post_type', $chapter->ID, 'publish'
                                    ), ARRAY_A
                            );

                            $exercise_ids = array();
                            ?>
                            <tr>
                                <td><?php echo $chapter_count++; ?></td>
                                <td><?php echo $chapter->post_title; ?></td>
                                <td>
                                    <?php
                                    $total_uploads = $wpdb->get_results(
                                            $wpdb->prepare(
                                                    "SELECT file from " . $this->table_activator->wpl_rd_user_uploaded_files_tbl() . " WHERE user_id = %d AND chapter_id = %d AND status = %d", $user_id, $chapter->ID, 1
                                            )
                                    );
                                    $total_uploads_found = 0;
                                    if (!empty($total_uploads) && count($total_uploads) > 0) {
                                        $total_uploads_found = count($total_uploads);
                                        echo $total_uploads_found;
                                        ?>

                                        <a href="javascript:void(0)" class="ch-view-uploads" data-user="<?php echo $user_id; ?>" data-chapter="<?php echo $chapter->ID; ?>">View Uploads</a>
                                        <?php
                                    } else {
                                        echo $total_uploads_found;
                                    }
                                    ?>

                                </td>

                                <td>
                                    <?php
                                    if (!empty($total_exercise) && count($total_exercise) > 0) {
                                        echo count($total_exercise);
                                    } else {
                                        echo 0;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($total_exercise) && count($total_exercise) > 0) {
                                        ?>
                                        <a href="admin.php?page=user-progress&c=<?php echo $course_id; ?>&u=<?php echo $user_id ?>&ch=<?php echo $chapter->ID; ?>" class="button button-primary button-large">View</a>
                                        <?php
                                    } else {
                                        echo '<i>No Exercise found</i>';
                                    }
                                    ?>

                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>

                </tbody>
            </table>
        </div>
        <?php
    }
}
?>

<div id="uploads-view" class="ui-custome-modal-admin" title="Uploads view" style="display: none;">
    <button type="button" class="ui-dialog-titlebar-close custome_btn"></button>
    <div id="user-progress-content"></div>
</div>