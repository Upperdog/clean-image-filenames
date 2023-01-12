=== Clean Image Filenames ===
Contributors: Upperdog, Gesen
Tags: upload, images, files, media, sanitize,
Requires at least: 2.9
Tested up to: 6.1
Stable tag: 1.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://paypal.me/gesen

This plugin automatically converts language accent characters to non-accent characters in filenames when uploading to the media library.

== Description ==

This plugin automatically converts language accent characters in filenames when uploading to the media library. Characters are converted into browser and server friendly, non-accent characters.

== Features ==

* Converts accent characters to non-accent, latin equivalents in Swedish, Danish, German, and more.
* Removes special characters like exclamation marks, periods, hashtags, and more.
* Lets you choose if you want to convert only image files, or all file types.
* Makes site and server migrations easier thanks to non-accent character filenames.
* Provides filter hook for developers who want to specify which file types to convert.

== Examples ==

* Räksmörgås.jpg → raksmorgas.jpg
* Æblegrød_FTW!.gif → aeblegrod-ftw.gif
* Château de Ferrières.png → chateau-de-ferrieres.png

== Worth noting ==

The plugin only converts filenames when the files are being uploaded. It can not convert existing files.

== Filter for developers ==

This filter provides developers a way to specify which file types the plugin should convert. This filter overrides the plugin settings on the media settings page. For a complete list of mime types, see [Wikipedia](http://en.wikipedia.org/wiki/Internet_media_type).

The following example will convert PDF, JPEG and PNG files only:

<pre><code>function my_clean_image_filenames_mime_types() {
	$mime_types = array(
		'application/pdf',
		'image/jpeg',
		'image/png',
	);
	return $mime_types;
}
add_filter( 'clean_image_filenames_mime_types', 'my_clean_image_filenames_mime_types' );</code></pre>

== Installation ==

1. Search for Clean Image Filenames in the plugins directory.
2. Install and activate the plugin.

or

1. Download and unzip the plugin and upload the `clean-image-filenames` directory to your `/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions == 

= Can this plugin convert filenames of existing files in the media library? =

No, this plugin only cleans the filenames of files when they are being uploaded to the media library.

== Screenshots ==

1. Easily choose between cleaning the filenames of all file types or images only.

== Changelog ==

= 1.4 =

* Add support for cleaning filenames when sideloaded (usually when uploaded programmatically) using the wp_handle_sideload_prefilter hook.

= 1.3 =

* Rewrite cleaning function to better handle specific characters.
* Make sure code is compliant with WordPress Coding Standards.

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