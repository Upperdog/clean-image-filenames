# Clean Image Filenames

Say goodbye to bad filenames like Château de Ferrières.jpg or Smörgåsbord.png and say hello to nice and clean filenames like chateau-de-ferrieres.jpg and smargasbord.png. This WordPress plugin replaces accent characters and special characters, like Swedish or German umlauts, in the filename of files uploaded to the WordPress media library. The range of file types that the plugin reacts to can be easily extended using a filter in your theme or plugin.

## Features

* Can be also used for other file types
* Is multisite compatible
* Works with custom upload_dir setups
* Doesn't alter your database or uploads settings

## Add your own mime types

By default, this plugin works with the following file types: GIF, JPEG, PNG. However, you can easily use this plugin for any file type you want by adding more mime types using the `clean_image_filenames_mime_types` filter in your theme or plugin. For a complete list of mime types, see [Wikipedia](http://en.wikipedia.org/wiki/Internet_media_type).

Example usage: 

<pre><code>function my_clean_image_filenames_mime_types() {

	$mime_types = array(
		'application/pdf', 
		'image/jpeg', 
		'image/png', 
	);

	return $mime_types;
}
add_filter('clean_image_filenames_mime_types', 'my_clean_image_filenames_mime_types');</code></pre>

## FAQ

### Why does this plugin exist?

No matter how hard you try to teach people to name their files in a certain way before uploading, sooner or later you will end up with a media library with filenames like Château de Ferrières.jpg or Smörgåsbord.png. Sometimes browsers have a hard time displaying images with filenames like these and the images end up broken. 

### Can this plugin change the filename of files already in the media library?

No, this plugin only changes the filename when the file is uploaded to the WordPress media library for the first time.

### Why not use the remove_accents() filter?

The `remove_accents()` filter converts accent charactes to ASCII characters. While that works great, it doesn't convert periods, commas, and other special characters. You never know what weird characters might end up in a filename, so we thought it was a better idea to use the `sanitize_title()` filter that does everything we need; converts accent characters to ASCII characters and converts whitespaces and special characters to dashes. 

## Installation

1. Rename the `clean-image-filenames-master` directory to `clean-image-filenames`.
2. Upload the `clean-image-filenames` directory to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Changelog

* 1.0: Initial release.
