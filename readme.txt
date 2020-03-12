=== Clean Image Filenames ===
Contributors: Upperdog, Gesen
Tags: upload, images, files, media, 
Requires at least: 2.9
Tested up to: 5.4
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://paypal.me/gesen

Filenames with special characters or language accent characters can sometimes be a problem. This plugin takes care of that by cleaning the filenames.

== Description ==

Filenames with special characters or language accent characters, like Château de Ferrières.jpg and smörgåsbord.png, can sometimes be a problem for browsers or servers. This plugin takes care of that and cleans the filenames of files uploaded to the WordPress media library. 

This plugin cleans the filenames from special characters like exclamation marks, periods, and commas and accent characters like Swedish and German umlauts. Special characters are remove, accent characters are converted to their non-accent equivalent, and blank spaces are converted into dashes. 

Easily set the plugin to clean the filenames of images only or all files uploaded to the media library. Developers can take advantage of the built in filter to get really specific about what file types to clean the filenames of.

= Features = 

* Can be used for all file types, not only images
* Multisite compatible
* Works with custom upload_dir setups
* Doesn't alter your database or uploads settings
* Gutenberg ready

= Plugin filter for developers =

Developers can get really specific about what file types to clean by using the `clean_image_filenames_mime_types` filter in their plugins or themes. **When using this filter, settings saved through the settings page are overridden.** For a complete list of mime types, see [Wikipedia](http://en.wikipedia.org/wiki/Internet_media_type).

The following example would make the plugin clean the filenames for PDF, JPEG and PNG files only. 

<pre><code>function my_clean_image_filenames_mime_types() {
	$mime_types = array(
		'application/pdf', 
		'image/jpeg', 
		'image/png', 
	);
	return $mime_types;
}
add_filter('clean_image_filenames_mime_types', 'my_clean_image_filenames_mime_types');</code></pre>

== Installation ==

1. Upload the `clean-image-filenames` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions == 

= Why does this plugin exist? =

No matter how hard you try to teach people to name their files in a certain way before uploading, sooner or later you will end up with a media library with filenames like Château de Ferrières.jpg or Smörgåsbord.png. Sometimes browsers have a hard time displaying images with filenames like these and the images end up broken. 

= Can this plugin clean the filenames of existing files in the media library? =

No, this plugin only cleans the filenames of files when they are being uploaded to the media library.

== Screenshots ==

1. Easily choose between cleaning the filenames of all files or images only.

== Changelog ==

= 1.2.1 =

* Enable plugin to be translated/internationalization.

= 1.2 =

* Set original, un-cleaned filename as attachment title.

= 1.1.1 =

* Added uninstall script that deletes plugin settings when the plugin is uninstalled.

= 1.1 =

* Added plugin settings to media settings page with option to convert all file types or just image file types. 
* Added shortcut to plugin settings from the plugins list.

= 1.0 =

* Initial release.

== Upgrade Notice ==

= 1.1.1 =

This version adds plugin uninstall script that deletes plugin settings if you were to delete the plugin.

= 1.1 =

This version adds plugin settings to the media settings page which lets you select between cleaning the filenames of all files or images only. The filter from version 1.0 is still available.