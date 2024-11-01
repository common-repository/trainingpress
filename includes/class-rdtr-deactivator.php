<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.rudrainnovative.com
 * @since      1.0.0
 *
 * @package    Rdtr
 * @subpackage Rdtr/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Rdtr
 * @subpackage Rdtr/includes
 * @author     Rudra Innovative Software Pvt Ltd <info@rudrainnovatives.com>
 */
class Rdtr_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public function deactivate() {

        // delete status of training from db
        delete_option("rdtr_training_plugin_status");
    }

}
