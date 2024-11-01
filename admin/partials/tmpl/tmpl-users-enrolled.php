<div class="wptr-rud-info" id="course-info">
    <div class="inner-info-content">
        <h4 class="course-heading">Course Details</h4>
        <div class="course-detail">
            <p class="course"><span class="course-name">Course:</span> <?php echo $course_name; ?></p>
            <?php
            $enrolled_count = $wpdb->get_var(
                    $wpdb->prepare(
                            "SELECT count(id) from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE course_post_id = %d", $course_id
                    )
            );
            ?>
            <p class="user-enrolled"><span class="user">Users Enrolled:</span> <?php echo $enrolled_count; ?></p>
        </div>

    </div>
</div>

<div class="wptr-rud-info data-table-area">
    <h4 class="datatable-heading">Users List</h4>
    <table id="course-enrolled-users" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Sr No</th>
                <th>User</th>
                <th>Email</th>
                <th>Course Score</th>
                <th>Enrolled on</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $total_users_enrolled = $wpdb->get_results(
                    $wpdb->prepare(
                            "SELECT * from " . $this->table_activator->wpl_rd_user_enroll_tbl() . " WHERE course_post_id = %d ORDER by id DESC", $course_id
                    )
            );

            if (count($total_users_enrolled) > 0) {
                $count = 1;
                foreach ($total_users_enrolled as $inx => $stx) {

                    // get user details
                    $user_details = get_user_by("ID", $stx->user_id);
                    ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo $user_details->user_login ?></td>
                        <td><?php echo $user_details->user_email ?></td>
                        <td><?php echo $this->wpl_rd_calculate_user_course_score($stx->user_id, $course_id); ?></td>
                        <td><?php echo $stx->created_at ?></td>
                        <!-- view progress of user by user_id on that course -->
                        <td><a href="admin.php?page=user-progress&c=<?php echo $stx->course_post_id ?>&u=<?php echo $user_details->ID ?>" class="button button-primary button-large" type="button">View Progress</a></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>