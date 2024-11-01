<?php
/**
 * @link              https://www.rudrainnovative.com
 * @since             1.0.2
 * @package           Rdtr
 *
 * @wordpress-plugin
 * Plugin Name:       TrainingPress
 * Plugin URI:        https://www.rudrainnovative.com/
 * Description:       TrainingPress is a comprehensive Learning management system Plugin for WordPress. This TrainingPress Plugin can be used to easily create courses. Each course curriculum can be made with modules, lessons and exercises which can be managed by anyone.
 * Version:           1.0.2
 * Author:            Rudra Innovative Software Pvt Ltd
 * Author URI:        https://www.rudrainnovative.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rdtr
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.2 and use SemVer 
 * Rename this for your plugin and update it as you release new versions.
 */
define('RDTR_TRAINING_NAME_VERSION', '1.0.2');
define('RDTR_TRAINING_POST_TYPE', 'training');
define('RDTR_TRAINING_BASEPATH', plugin_basename(__FILE__));
define("RDTR_TRAINING_DIR_PATH", plugin_dir_path(__FILE__));
define('RDTR_TRAINING_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rdtr-activator.php
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-rdtr-activator.php';
$table_activator = new Rdtr_Activator();

function activate_rdtr() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-rdtr-activator.php';
    $table_activator = new Rdtr_Activator();
    $table_activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rdtr-deactivator.php
 */
function deactivate_rdtr() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-rdtr-deactivator.php';
    $deactivator = new Rdtr_Deactivator();
    $deactivator->deactivate();
}

function wpl_rtr_define_vars() {
    global $wpdb;

    if ( is_user_logged_in() ) 
    {
        $role_object = get_role('subscriber');
        $role_object->add_cap('upload_files');
    }
}

add_action('init', 'wpl_rtr_define_vars');

register_activation_hook(__FILE__, 'activate_rdtr');
register_deactivation_hook(__FILE__, 'deactivate_rdtr');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-rdtr.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.2
 */
function run_rdtr() {

    $plugin = new Rdtr();
    $plugin->run();
}

run_rdtr();
