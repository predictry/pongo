<?php

/**
 * The Predictry Engine WordPress Plugin
 *
 *
 * @package		PredictryEngine
 * @author 		Wong Ban Korh <me@bankorh.com>
 * @license             GNU General Public License v3.0
 * @link 		http://www.predictry.com
 * @copyright           2014 Predictry.com
 *
 * Plugin Name: 	Predictry Engine
 * Plugin URI: 		http://www.predictry.com/
 * Description: 	A recommendation engine by Predictry
 * Version: 		0.1.0
 * Author: 		Wong Ban Korh
 * Author URI: 		http://www.bankorh.com/
 * Licence: 		GNU General Public License v3.0
 * License URI: 	http://www.gnu.org/licenses/gpl-3.0.html
 */
// File is called directly, operation terminated.
if (!defined('WPINC'))
    die;

/**
 * Public Facing Functionality
 */
require_once( plugin_dir_path(__FILE__) . 'public/class-predictry-engine.php' );
require_once( plugin_dir_path(__FILE__) . 'public/class-predictry-data-collector.php' );
require_once( plugin_dir_path(__FILE__) . 'public/class-predictry-recommendation.php' );

/**
 * Register hooks that are fired when the plugin is activated or deactivated
 * When the plugin is deleted, the uninstall.php file is loaded
 */
register_activation_hook(__FILE__, array('PredictryEngine', 'activate'));
register_deactivation_hook(__FILE__, array('PredictryEngine', 'deactivate'));

add_action('plugins_loaded', array('PredictryEngine', 'get_instance'));
add_action('plugins_loaded', array('PredictryDataCollector', 'get_instance'));
add_action('plugins_loaded', array('PredictryRecommendation', 'get_instance'));

/**
 * load the css 
 * so that everything looks good
 **/

wp_register_style('predictryStylesheet', '//d2gq0qsnoi5tbv.cloudfront.net/latest/p.css');
wp_enqueue_style( 'predictryStylesheet');

/**
 * Admin Page
 */
if (is_admin()) {

    require_once( plugin_dir_path(__FILE__) . 'admin/class-predictry-engine-admin.php' );

    add_action('plugins_loaded', array('PredictryEngineAdmin', 'get_instance'));
}
