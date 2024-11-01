<?php
/**
 * This File for Training setting.
 * @author	Rudra Innnovative Software 
 * @package	training/partials/ 
 * @version	1.0.0
 */
if (!defined("ABSPATH"))
    exit;

wp_enqueue_media();
?>

<div class="training-settings-panel wptr-rud-training-pg" id="admin_mail-training-settings">
    <h2 class="heading_setting">Settings panel</h2>
    <div class="wpl-settings">
        <form action="javascript:void(0)" method="post" id="frm-settings">
            <div class="training-settings_image">
                <h2 class="txt-heading">Default Course Image</h2>
                <span id="show-img-prev">
                    <?php
                    $setting_image = RDTR_TRAINING_PLUGIN_URL . "assets/images/no-image.png";
                    if (@getimagesize(RDTR_COURSE_DEFAULT_IMAGE)) {
                        $setting_image = RDTR_COURSE_DEFAULT_IMAGE;
                    }
                    ?>
                    <img class="course-img-prev" src="<?php echo $setting_image; ?>"/>
                </span>
                <input type="hidden" name="course_img" id="rpl_course_img"/>
                <button class="button button-primary wpl-upload-image">Upload from Media</button>
            </div>
            <div class="shortcode_fields">

                <div class="wptr-row-section">
                    <div class="shortcode_select">
                        <label class="lbl-ex-bold">Training Shortcode:</label>
                        <input type="text" value="[training-courses]" class="txt-training-shortcode" readonly=""/>
                        <i>Note*: Use this shortcode on wordpress page to display 'All Courses' section.</i>
                    </div>

                    <div class="shortcode_select">
                        <label class="lbl-ex-bold">Select All Courses page:</label>
                        <select name='dd-all-courses-page' class="select-training-select">
                            <?php
                            $all_pages = $this->wpl_rd_get_wp_pages();

                            if (count($all_pages) > 0) {
                                ?>
                                <option value='-1'>Select page</option>
                                <?php
                                $saved_my_course_page = get_option("rdtr_training_allcourses_page");
                                foreach ($all_pages as $inx => $stx) {
                                    if (empty($stx->post_title)) {
                                        continue;
                                    }
                                    $selected = "";
                                    if ($stx->ID == $saved_my_course_page) {
                                        $selected = 'selected="selected"';
                                    }
                                    ?>
                                    <option value='<?php echo $stx->ID; ?>' <?php echo $selected; ?>><?php echo ucwords($stx->post_title); ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                        <i>Note*: Select page where you have pasted [training-courses] shortcode.</i>
                    </div>
                </div>
                <div class="wptr-row-section">
                    <div class="shortcode_select">
                        <label class="lbl-ex-bold">My Course Shortcode:</label>
                        <input type="text" value="[training-my-course]" class="txt-training-shortcode" readonly=""/>
                        <i>Note*: Use this shortcode on wordpress page to display 'My Courses' section.</i>
                    </div>

                    <div class="shortcode_select">
                        <label class="lbl-ex-bold">Select My Course page:</label>
                        <select name='dd-my-course-page' class="select-training-select">
                            <?php
                            $all_pages = $this->wpl_rd_get_wp_pages();

                            if (count($all_pages) > 0) {
                                ?>
                                <option value='-1'>Select page</option>
                                <?php
                                $saved_my_course_page = get_option("rdtr_training_mycourse_page");
                                foreach ($all_pages as $inx => $stx) {
                                    if (empty($stx->post_title)) {
                                        continue;
                                    }
                                    $selected = "";
                                    if ($stx->ID == $saved_my_course_page) {
                                        $selected = 'selected="selected"';
                                    }
                                    ?>
                                    <option value='<?php echo $stx->ID; ?>' <?php echo $selected; ?>><?php echo ucwords($stx->post_title); ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                        <i>Note*: Select page where you have pasted [training-my-course] shortcode.</i>
                    </div>
                </div>

                <?php
                if (has_action("training_pay_setttings")) {
                    do_action("training_pay_setttings");
                }
                ?>
             <div class="wptr-row-section">
                <div class="shortcode_select selection_check">
                    <?php
                    $disable = get_option("training_disable_comments");
                    $checked = '';
                    if ($disable == 1) {
                        $checked = 'checked=""';
                    }
                    ?>

                    <label class="lbl-ex-bold dis-comment">Disable Comment & Rating:</label>
                    <div class="inner_select">
                        <input type="checkbox" <?php echo $checked ?> value="1" name='want_chk_rating' id='want_chk_rating' class="select-training-check"/>
                        <i> Check to disable Rating & Comment from all courses.</i>
                    </div>
                </div>

                <div class="shortcode_select">
                    <div id="rdtr-course-import-section">
                        <div class="wptr-teamplate-course">
                            <label class="lbl-ex-bold">Install Sample Course:</label>
                            <button class="button" id="btn-install-sample-course">Install</button>
                        </div>
                    </div>
                </div>
            </div>

            </div>
            <hr/>
            <div class="email_setting_sub_btn">
                <button class="button button-primary" type="button" id="wpl-save-settings">Save Settings</button>
            </div>
        </form>
    </div>
</div>