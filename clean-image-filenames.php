<?php 
/**
 * Plugin Name: Clean Image Filenames
 * Description: Filenames with special characters or language accent characters can sometimes be a problem. This plugin takes care of that by cleaning the filenames.
 * Version: 1.1.1
 * Author: Upperdog
 * Author URI: http://upperdog.com
 * Author Email: hello@upperdog.com
 * License: GPL2
 */

/*  Copyright 2014 Upperdog (email : hello@upperdog.se)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!defined('ABSPATH')) {
	exit;
}

class CleanImageFilenames {

	/**
	 * Plugin settings.
	 * 
	 * @var array Plugin settings for version, default mime types.
	 * @since 1.1
	 */

	public $plugin_settings = array(
		'version' 				=> '1.1', 
		'default_mime_types' 	=> array(
			'image/gif',
			'image/jpeg',
			'image/pjpeg',
			'image/png',
			'image/tiff'
		)
	);


	/**
	 * Sets up hooks, actions and filters that the plugin responds to.
	 *
	 * @since 1.0
	 */

	function __construct() {

		register_activation_hook(__FILE__, array($this, 'plugin_activation'));
		add_action('plugins_loaded', array($this, 'plugins_loaded'));
		add_action('admin_init', array($this, 'admin_init'));
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
		add_action('wp_handle_upload_prefilter', array($this, 'upload_filter'));
	}


	/**
	 * Adds default plugin settings on plugin activation.
	 *
	 * @since 1.1
	 */

	function plugin_activation() {
		$this->add_default_plugin_settings();
	}


	/**
	 * Updates plugin version database setting and calls default settings function.
	 * 
	 * Checks current plugin version. If the plugin has been updated, the saved 
	 * plugin version in the database is updated. 
	 * 
	 * Adds default plugin settings if they don't already exist. Default plugin 
	 * settings didn't exist prior to version 1.1. 
	 *
	 * @since 1.1
	 */

	function plugins_loaded() {

		if ($this->plugin_settings['version'] !== get_option('clean_image_filenames_plugin_version')) {
			update_option('clean_image_filenames_plugin_version', $this->plugin_settings['version']);
		}

		$this->add_default_plugin_settings();
	}


	/**
	 * Adds default plugin settings in the database.
	 *
	 * This function runs when the plugin is activated and when plugins are loaded 
	 * using the plugins_loaded hook. The function updates default plugin settings 
	 * in the database options table. 
	 * 
	 * Adds default value for mime types field if it doesn't already exist
	 *
	 * @since 1.1
	 */

	function add_default_plugin_settings() {

		if (FALSE === get_option('clean_image_filenames_mime_types')) {
			add_option('clean_image_filenames_mime_types', 'images');
		}
	}


	/**
	 * Sets up plugin translations and plugin settings fields.
	 *
	 * @since 1.1
	 */

	function admin_init() {

		// Load plugin translations
		load_plugin_textdomain('clean_image_filenames', false, dirname(plugin_basename(__FILE__)) . '/languages/');

		// Add settings section
		add_settings_section('clean_image_filenames_settings_section', 'Clean Image Filenames', array($this, 'clean_image_filenames_settings_section_callback'), 'media');

		// Add settings field
		add_settings_field(
			'clean_image_filenames_mime_types', 
			__('File types', 'clean_image_filenames'), 
			array($this, 'clean_image_filenames_mime_types_callback'), 
			'media', 
			'clean_image_filenames_settings_section', 
			array(
				'alternatives' => array(
					array(
						'value' 	=> 'all', 
						'label'		=> __('All file types', 'clean_image_filenames')
					), 
					array(
						'value' 	=> 'images', 
						'label'		=> __('Images only', 'clean_image_filenames')
					)
				)
			)
		);

		// Register settings
		register_setting('media', 'clean_image_filenames_mime_types');
	}


	/**
	 * Add custom action links to the plugin's row in the plugins list.
	 * 
	 * @since 1.1
	 * @param array Original action links. 
	 * @return array Action links with new addition. 
	 */

	function add_action_links($links) {
		$plugin_action_links = array('<a href="' . admin_url('options-media.php') . '">' . __('Settings') . '</a>');
		return array_merge($links, $plugin_action_links);
	}


	/**
	 * Outputs content before the settings fields.
	 *
	 * @since 1.1
	 */

	function clean_image_filenames_settings_section_callback() {

		echo '<p>' . __('Choose which file types that Clean Image Filenames shall improve the filenames for when files are uploaded.', 'clean_image_filenames') . '</p>';
	}


	/**
	 * Outputs the settings fields.
	 * 
	 * If the plugin filter has been used in a plugin or theme, the filter 
	 * overrides the settings are the settings are therefore disabled.
	 *
	 * If the plugin filter has not been used, the user selected setting of what 
	 * file types to clean is used.
	 * 
	 * @since 1.1
	 * @param array Field defails.
	 */

	function clean_image_filenames_mime_types_callback($args) {

		if (apply_filters('clean_image_filenames_mime_types', $this->plugin_settings['default_mime_types']) !== $this->plugin_settings['default_mime_types']) {
			
			echo '<input name="clean_image_filenames_mime_types" id="clean_image_filenames_mime_types" type="hidden" value="' . get_option('clean_image_filenames_mime_types') . '">';
			echo '<i>' . __('The setting for what file types should be cleaned is disabled since a plugin or theme has already defined what file types should be cleaned.', 'clean_image_filenames') . '</i>';

		} else {

			foreach ($args['alternatives'] as $alternative) {
				echo '<label><input name="clean_image_filenames_mime_types" id="clean_image_filenames_mime_types" type="radio" value="' . $alternative['value'] . '" ' . checked($alternative['value'], get_option('clean_image_filenames_mime_types'), false) . '>' . $alternative['label'] . '</label><br>';
			}
		}
	}


	/**
	 * Checks whether or not the current file should be cleaned.
	 *
	 * This function runs when files are being uploaded to the WordPress media 
	 * library. The function checks if the clean_image_filenames_mime_types filter 
	 * has been used and overrides other settings if it has. Otherwise, the plugin 
	 * settings are used. 
	 *
	 * If a file shall be cleaned or not is checked by comparing the current file's 
	 * mime type to the list of mime types to be cleaned.
	 *
	 * @since 1.1 Added more complex checks and moved the actual cleaning to clean_filename().
	 * @since 1.0
	 * @param array The file information including the filename in $file['name'].
	 * @return array The file information with the cleaned or original filename.
	 */

	function upload_filter($file) {

		$mime_types_setting = get_option('clean_image_filenames_mime_types');
		$default_mime_types = $this->plugin_settings['default_mime_types'];
		$valid_mime_types = apply_filters('clean_image_filenames_mime_types', $default_mime_types);

		if ($valid_mime_types !== $default_mime_types) {

			if (in_array($file['type'], $valid_mime_types)) {
				$file = $this->clean_filename($file);
			}

		} else {

			if ('all' == $mime_types_setting) {
				$file = $this->clean_filename($file);
			} elseif ('images' == $mime_types_setting && in_array($file['type'], $default_mime_types)) {
				$file = $this->clean_filename($file);
			}
		}

		// Return cleaned file or input file if it didn't match
	    return $file;
	}


	/**
	 * Performs the filename cleaning.
	 *
	 * This function performs the actual cleaning of the filename. It takes an 
	 * array with the file information, cleans the filename and sends the file 
	 * information back to where the function was called from. 
	 *
	 * @since 1.1
	 * @param array File details including the filename in $file['name'].
	 * @return array The $file array with cleaned filename.
	 */

	function clean_filename($file) {

		$path = pathinfo($file['name']);
		$new_filename = preg_replace('/.' . $path['extension'] . '$/', '', $file['name']);
		$file['name'] = sanitize_title($new_filename) . '.' . $path['extension'];

		return $file;
	}
}

$clean_image_filenames = new CleanImageFilenames();