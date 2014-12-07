<?php 
/**
 * Plugin Name: Clean Image Filenames
 * Description: Say goodbye to bad filenames like Château de Ferrières.jpg and say hello to nice and clean filenames like chateau-de-ferrieres.jpg.
 * Version: 1.1
 * Author: Upperdog
 * Author URI: http://upperdog.com
 * Author Email: hello@upperdog.se
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

	public $new_plugin_version = '1.1';

	function __construct() {

		register_activation_hook(__FILE__, array($this, 'plugin_activation'));
		add_action('plugins_loaded', array($this, 'plugins_loaded'));
		add_action('admin_init', array($this, 'admin_init'));
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
		add_action('wp_handle_upload_prefilter', array($this, 'upload_filter'));
	}

	function plugin_activation() {
		$this->add_default_plugin_settings();
	}

	function plugins_loaded() {

		// Check current plugin version
		if ($this->new_plugin_version !== get_option('clean_image_filenames_plugin_version')) {
			update_option('clean_image_filenames_plugin_version', $this->new_plugin_version);
		}

		// Add default plugin settings if they don't already exist
		$this->add_default_plugin_settings();
	}

	function add_default_plugin_settings() {

		// Add default value for mime types field if it doesn't already exist
		if (FALSE === get_option('clean_image_filenames_mime_types')) {
			add_option('clean_image_filenames_mime_types', 'images');
		}
	}

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
						'value' 	=> 'image/gif,image/jpeg,image/pjpeg,image/png,image/tiff', 
						'label'		=> __('Images only', 'clean_image_filenames')
					)
				)
			)
		);

		// Register settings
		register_setting('media', 'clean_image_filenames_mime_types');
	}

	function add_action_links($links) {

		$plugin_action_links = array('<a href="' . admin_url('options-media.php') . '">' . __('Settings') . '</a>');
		return array_merge($links, $plugin_action_links);
	}

	function clean_image_filenames_settings_section_callback($args) {

		echo '<p>' . __('Choose which file types that Clean Image Filenames shall improve the filenames for when files are uploaded.', 'clean_image_filenames') . '</p>';
	}

	function clean_image_filenames_mime_types_callback($args) {

		foreach ($args['alternatives'] as $alternative) {
			echo '<label><input name="clean_image_filenames_mime_types" id="clean_image_filenames_mime_types" type="radio" value="' . $alternative['value'] . '" ' . checked($alternative['value'], get_option('clean_image_filenames_mime_types'), false) . '>' . $alternative['label'] . '</label><br>';
		}
	}

	function upload_filter($file) {

		$mime_types_setting = get_option('clean_image_filenames_mime_types');
		$valid_mime_types = array();

		if ('all' !== $mime_types_setting) {
			$valid_mime_types = explode(',', $mime_types_setting);
		}

		if ('all' == $mime_types_setting || in_array($file['type'], apply_filters('clean_image_filenames_mime_types', $valid_mime_types))) {
			$path = pathinfo($file['name']);
			$new_filename = preg_replace('/.' . $path['extension'] . '$/', '', $file['name']);
			$file['name'] = sanitize_title($new_filename) . '.' . $path['extension'];
		}

	    return $file;
	}
}

$clean_image_filenames = new CleanImageFilenames();