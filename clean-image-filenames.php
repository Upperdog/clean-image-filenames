<?php 
/**
 * Plugin Name: Clean Image Filenames
 * Description: Say goodbye to bad filenames like Château de Ferrières.jpg and say hello to nice and clean filenames like chateau-de-ferrieres.jpg.
 * Version: 1.0
 * Author: UPPERDOG
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

	function __construct() {
		add_action('wp_handle_upload_prefilter', array($this, 'upload_filter'));
	}

	function upload_filter($file) {

		/*
		 * Only sanitize the filename for files that has one of these mime types. 
		 * The list of mime types can be customized using the clean_image_filenames_mime_types filter.
		 * For a complete list of mime types, see http://en.wikipedia.org/wiki/Internet_media_type
		 */
		$valid_mime_types = array(
			'image/bmp', 
			'image/gif', 
			'image/jpeg', 
			'image/pjpeg', 
			'image/png', 
		);


		/*
		 * If the current file's mime type is in the array of valid mime types, the filename 
		 * will be sanitized and saved to the original $file array.
		 */
		if (in_array($file['type'], apply_filters('clean_image_filenames_mime_types', $valid_mime_types))) {
			$path = pathinfo($file['name']);
			$new_filename = preg_replace('/.' . $path['extension'] . '$/', '', $file['name']);
			$file['name'] = sanitize_title($new_filename) . '.' . $path['extension'];
		}


		/*
		 * Return the $file array. If the filename wasn't changed, the input array 
		 * is returned without any modifications.
		 */
	    return $file;
	}
}

$clean_image_filenames = new CleanImageFilenames();