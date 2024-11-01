<?php
$space = '&nbsp;&nbsp;';
$marker = '&#187;';
$course_name = get_the_title($course_id);
$user_data = array();
if ($user_id > 0) {
    $user_data = get_user_by("ID", $user_id);
}
?>

<div class="notice notice-success wpl-crumb">
    <p>
        <a href='edit.php?post_type=training'><?php echo $course_name; ?></a><?php echo $space . $marker . $space; ?>
        <?php
        if ($user_id > 0 && $chapter_id == 0) {
            if (!empty($user_data)) {
                ?>
                <a href='admin.php?page=user-progress&c=<?php echo $course_id ?>'><?php echo $user_data->user_email; ?></a><?php echo $space . $marker . $space; ?>
                User Progress
                <?php
            }
        } elseif ($course_id > 0 && $user_id == 0 && $chapter_id == 0) {
            ?>
            Users Enrolled
            <?php
        } elseif ($chapter_id > 0 && $course_id > 0 && $user_id > 0) {
            $chapter_name = get_the_title($chapter_id);
            ?>
            <a href='admin.php?page=user-progress&c=<?php echo $course_id ?>'><?php echo $user_data->user_email; ?></a><?php echo $space . $marker . $space; ?>
            <a href='admin.php?page=user-progress&c=<?php echo $course_id ?>&u=<?php echo $user_id; ?>'><?php echo $chapter_name; ?></a><?php echo $space . $marker . $space; ?>
            Chapter Progress
            <?php
        }
        ?>  

    </p>
</div>

<div class="rdtr-course-info">

    <div classs="progress-body">
        <?php
        if ($user_id > 0 && $chapter_id == 0) {
            // template include for user's progress
            ob_start();

            include_once RDTR_TRAINING_DIR_PATH . 'admin/partials/tmpl/tmpl-users-progress.php';
            $template = ob_get_contents();
            ob_end_clean();

            echo $template;
        } elseif ($course_id > 0 && $user_id == 0 && $chapter_id == 0) {
            // template include for total users enrolled
            ob_start();

            include_once RDTR_TRAINING_DIR_PATH . 'admin/partials/tmpl/tmpl-users-enrolled.php';
            $template = ob_get_contents();
            ob_end_clean();

            echo $template;
        } elseif ($chapter_id > 0 && $course_id > 0 && $user_id > 0) {

            // template include for total users enrolled
            ob_start();

            include_once RDTR_TRAINING_DIR_PATH . 'admin/partials/tmpl/tmpl-user-chapters-progress.php';
            $template = ob_get_contents();
            ob_end_clean();

            echo $template;
        }
        ?>
    </div>

</div>

