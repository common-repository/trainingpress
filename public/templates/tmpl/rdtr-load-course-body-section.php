<?php
/**
 * This File for course page render body section.
 * @author	Rudra Innnovative Software 
 * @package	training/templates/tmpl/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;
?>

<?php wp_enqueue_media(); ?>

<div class="my-course_contnet_area">
    <div id="wptr-rd-loader-area"></div>
    <?php
    $dataType = isset($dataType) ? trim($dataType) : "";

    global $user_ID;

    if (!empty($dataType)) {

        if ($dataType == "module") {
            ?>
            <h4 class="wptr_h4_4 padd-x-15 title_my_course"><?php echo ucwords($module_data['post_title']); ?></h4>

            <div class="wptr_cl-md-12 wptr_pt-4">
                <p>
                    <?php
                    echo do_shortcode(html_entity_decode($module_data['post_content']));
                    ?>
                </p>
            </div>
            <?php
        } elseif ($dataType == "chapter") {
            ?>
            <h4 class="wptr_h4_4 padd-x-15 title_my_course"><?php echo ucwords($chapter_data['post_title']) ?></h4>

            <div class="wptr_cl-md-12 wptr_pt-4">
                <p>
                    <?php
                    echo do_shortcode(html_entity_decode($chapter_data['post_content']));
                    ?>
                </p>
            </div>
            <?php
        } elseif ($dataType == "chapter_upload") {
            ?>
            <div class="rdtr_file_upload_dilog">
                <h4 class="wptr_h4_4 padd-x-15 title_my_course wptr-rud-ch-title">
                    <?php echo ucwords($chapter_details->post_title); ?> upload file(s)
                </h4>
                <?php
                if ($user_ID > 0) {
                    ?>
                    <div class="rdtr_file_upload_btn">
                        <button id="btn-open-users-media" ch-id="<?php echo $chapter_details->ID ?>" class="wptr_course-btn" title="Upload file(s)"><i class="mdi mdi-upload"></i></button>
                        <div id="rdtr-upload-files"></div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div id="rdtr-upload-new"></div>
            <div class="wptr_cl-md-12 wptr_pt-4 wptr_upd-top-files padd-x-15">
                <?php
                if (!empty($has_any_uploaded_files)) {

                    if (count($has_any_uploaded_files) > 0) {
                        ?>
                        <h4 class="wptr_montserrat uploaded_title">Total uploaded file(s): <?php echo count($has_any_uploaded_files); ?></h4>
                        <ul class="rdtr_uploaded_files">
                            <?php
                            foreach ($has_any_uploaded_files as $inx => $stx) {
                                
                                $ext = pathinfo($stx->file, PATHINFO_EXTENSION);
                                $valid_images = array("gif", "png", "jpeg", "jpg","webp");
                                $valid_audio = array("mp3");
                                $valid_video = array("mp4", "mkv", "3gp", "webm");
                                $valid_pdf = array("pdf");

                                $icon = '';
                                if (in_array($ext, $valid_images)) {
                                    $icon = '<i class="mdi mdi-file-image uploaded-files-icon-font"></i>';
                                } elseif (in_array($ext, $valid_audio)) {
                                    $icon = '<i class="mdi mdi-file-music uploaded-files-icon-font"></i>';
                                }
                                 elseif (in_array($ext, $valid_video)) {
                                    $icon = '<i class="mdi mdi-file-video uploaded-files-icon-font"></i>';
                                } 
                                 elseif (in_array($ext, $valid_pdf)) {
                                    $icon = '<i class="mdi mdi-file-pdf uploaded-files-icon-font"></i>';
                                } 
                                else {
                                    $icon = '<i class="mdi mdi-file uploaded-files-icon-font"></i>';
                                }
                                if($stx->type ==''){
                                    if (in_array($ext, $valid_pdf)){
                                        $stx->type = 'pdf'; 
                                    }
                                }

                                ?>
                                <li><a href="<?php echo $stx->file; ?>" download><?php echo $icon; ?></a><label><?php echo ucfirst($stx->type); ?></label></li>
                                <?php
                            }
                            ?>
                        </ul>
                        <?php
                    }
                } else {
                    ?>
                    <p><i>Note: Following are the submission you need to upload</i></p>

                    <?php echo $editor_content = get_post_meta($chapter_details->ID, "chapter_editor_has_assignment", true); ?>

                    <?php
                }
                ?>
            </div>
            <?php
        } elseif ($dataType == "course_completed") {
            ?>
            <h4 class="wptr_h4_4 padd-x-15 title_my_course rdtr-course-successful"><?php echo $message; ?></h4>

            <span class="rdtr-go-to-my-course">
                <?php
                $my_course = get_option("rdtr_training_mycourse_page"); // my course page id
                $my_course_url = 'javascript:void(0)';
                $attr_id = "open-no-page-model";
                if (!empty($my_course)) {
                    $attr_id = '';
                    $my_course_url = get_permalink($my_course);
                }
                ?>
                <a href="<?php echo $my_course_url; ?>" id="<?php echo $class; ?>" class='wptr_course-btn'>Go to My courses</a>
            </span>

            <?php
        }
    } else {
        ?>
        <h4 class="wptr_h4_4 padd-x-15 title_my_course">Invalid Url Parameters / Course not properly created</h4>
        <span class="rdtr-go-to-my-course">
            <?php
            $my_course = get_option("rdtr_training_mycourse_page"); // my course page id
            $my_course_url = 'javascript:void(0)';
            $attr_id = "open-no-page-model";
            if (!empty($my_course)) {
                $attr_id = '';
                $my_course_url = get_permalink($my_course);
            }
            ?>
            <a href="<?php echo $my_course_url; ?>" id="<?php echo isset($class) ? $class : ''; ?>" class="wptr_course-btn">Go to My courses</a>
        </span>
        <?php
    }
    ?>

</div>