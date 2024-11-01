<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.rudrainnovative.com
 * @since      1.0.0
 *
 * @package    Rdtr
 * @subpackage Rdtr/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Rdtr
 * @subpackage Rdtr/includes
 * @author     Rudra Innovative Software Pvt Ltd <info@rudrainnovatives.com>
 */
class Rdtr_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public function __construct() {
        
    }

    public function activate() {

        global $wpdb;

        // table to store courses enrolled by user
        if ($wpdb->get_var("show tables like '" . $this->wpl_rd_user_enroll_tbl() . "'") != $this->wpl_rd_user_enroll_tbl()) {
            $sqlUserEnrol = 'CREATE TABLE ' . $this->wpl_rd_user_enroll_tbl() . ' (
		    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `course_post_id` int(11) NOT NULL,
                    `user_id` int(11) NOT NULL,
                    `course_status` INT NOT NULL DEFAULT "0",
                    `status` int(11) NOT NULL DEFAULT "1",
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sqlUserEnrol);
        }

        // table to store values of courses by exercise complete status
        if ($wpdb->get_var("show tables like '" . $this->wpl_rd_course_progress_tbl() . "'") != $this->wpl_rd_course_progress_tbl()) {
            $sqlCourseProgress = 'CREATE TABLE ' . $this->wpl_rd_course_progress_tbl() . ' (
		    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `course_post_id` int(11) NOT NULL,
                    `user_id` int(11) NOT NULL,
                    `exercise_id` INT NOT NULL,
                    `exercise_status` INT( 11 ) NOT NULL DEFAULT  "0",
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sqlCourseProgress);
        }

        // table to store uploaded file for has assignment section
        if ($wpdb->get_var("show tables like '" . $this->wpl_rd_user_uploaded_files_tbl() . "'") != $this->wpl_rd_user_uploaded_files_tbl()) {
            $sqlUserUploadedFiles = 'CREATE TABLE ' . $this->wpl_rd_user_uploaded_files_tbl() . ' (
		        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `user_id` int(11) DEFAULT NULL,
                        `chapter_id` INT NOT NULL,
                        `type` enum("file","link","image","video","audio") NOT NULL,
                        `file` varchar(500) DEFAULT NULL,
                        `status` int(11) NOT NULL DEFAULT "1",
                        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sqlUserUploadedFiles);
        }

        // table to store user progress over exercise
        if ($wpdb->get_var("show tables like '" . $this->wpl_rd_user_exercise_progress() . "'") != $this->wpl_rd_user_uploaded_files_tbl()) {
            $sqlUserExerciseProgress = 'CREATE TABLE ' . $this->wpl_rd_user_exercise_progress() . ' (
		            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `user_id` int(11) NOT NULL,
                            `exe_type` VARCHAR( 255 ) NULL,
                            `exercise_id` int(11) NOT NULL,
                            `seq_number` VARCHAR( 300 ) NULL,
                            `exe_answer` VARCHAR( 300 ) NULL,
                            `complete_status` int(11) NOT NULL DEFAULT "0",
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sqlUserExerciseProgress);
        }

        // table to store total exercise contained by a course
        if ($wpdb->get_var("show tables like '" . $this->wpl_rd_course_exercise_sequence() . "'") != $this->wpl_rd_user_uploaded_files_tbl()) {
            $sqlCourseExerciseSequence = 'CREATE TABLE ' . $this->wpl_rd_course_exercise_sequence() . ' (
		            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `course_id` int(11) NOT NULL,
                            `exercise_id` int(11) NOT NULL,
                            `sequence_number` varchar(255) NOT NULL,
                            `sequence_status` INT NOT NULL DEFAULT  "1",
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sqlCourseExerciseSequence);
        }

        // add status to db that training plugin is activated
        add_option("rdtr_training_plugin_status", 1);
    }

    public function wpl_rd_user_enroll_tbl() {
        global $wpdb;
        return $wpdb->prefix . 'rd_user_course_enroll';
    }

    public function wpl_rd_course_progress_tbl() {
        global $wpdb;
        return $wpdb->prefix . 'rd_user_course_progress';
    }

    public function wpl_rd_user_uploaded_files_tbl() {
        global $wpdb;
        return $wpdb->prefix . 'rd_user_uploaded_files';
    }

    public function wpl_rd_user_exercise_progress() {
        global $wpdb;
        return $wpdb->prefix . 'rd_user_exercise_progress';
    }

    public function wpl_rd_course_exercise_sequence() {
        global $wpdb;
        return $wpdb->prefix . 'rd_course_exercise_sequence';
    }

}