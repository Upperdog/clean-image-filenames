<?php
/**
 * Uninstall plugin.
 *
 * This file is executed when the plugin is uninstalled and deletes plugin settings.
 *
 * @package clean-image-filenames
 *
 * @since 1.1.1
 */

// Exit if uninstall is not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete plugin settings.
delete_option( 'clean_image_filenames_plugin_version' );
delete_option( 'clean_image_filenames_mime_types' );
