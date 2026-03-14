<?php
/**
 * Plugin Name: AVSTube Video Manager
 * Description: Advanced video upload, streaming, metadata management, and REST API endpoints for AVSTube theme.
 * Version: 1.0.0
 * Author: AVSTube
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: avstube-video-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AVSTUBE_VM_VERSION', '1.0.0' );
define( 'AVSTUBE_VM_PLUGIN_FILE', __FILE__ );
define( 'AVSTUBE_VM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'AVSTUBE_VM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once AVSTUBE_VM_PLUGIN_PATH . 'includes/class-video-post-type.php';
require_once AVSTUBE_VM_PLUGIN_PATH . 'includes/class-video-upload.php';
require_once AVSTUBE_VM_PLUGIN_PATH . 'includes/class-video-api.php';
require_once AVSTUBE_VM_PLUGIN_PATH . 'includes/class-video-player.php';

/**
 * Bootstrap plugin classes.
 *
 * @return void
 */
function avstube_vm_init_plugin() {
	new AVSTube_VM_Video_Post_Type();
	new AVSTube_VM_Video_Upload();
	new AVSTube_VM_Video_API();
	new AVSTube_VM_Video_Player();
}
add_action( 'plugins_loaded', 'avstube_vm_init_plugin' );

/**
 * Flush rewrite rules on activation.
 *
 * @return void
 */
function avstube_vm_activate() {
	$video_post_type = new AVSTube_VM_Video_Post_Type();
	$video_post_type->register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'avstube_vm_activate' );

/**
 * Flush rewrite rules on deactivation.
 *
 * @return void
 */
function avstube_vm_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'avstube_vm_deactivate' );
