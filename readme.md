# Clean Image Filenames

Say goodbye to bad filenames like Château de Ferrières.jpg or Smörgåsbord.png and say hello to nice and clean filenames like chateau-de-ferrieres.jpg and smargasbord.png. This WordPress plugin replaces accent characters and special characters, like periods and exclamation marks and Swedish or German umlauts, in the filenames of files uploaded to the WordPress media library. Easily choose if you want the plugin to clean the filenames of all file types or just images. Developers can get really specific about what file types to clean by using the plugin filter in their plugins or themes.

## Features

* Can be used for all file types, only image file types, or only specific file types
* Is multisite compatible
* Works with custom upload_dir setups
* Doesn't alter your database or uploads settings

## Plugin filter for developers

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

## FAQ

### Why does this plugin exist?

No matter how hard you try to teach people to name their files in a certain way before uploading, sooner or later you will end up with a media library with filenames like Château de Ferrières.jpg or Smörgåsbord.png. Sometimes browsers or servers have a hard time displaying images with filenames like these and the images end up broken. 

### Can this plugin clean the filenames of existing files in the media library?

No, this plugin only cleans the filenames of files when they are being uploaded to the media library.

## Installation

1. Rename the `clean-image-filenames-master` directory to `clean-image-filenames`.
2. Upload the `clean-image-filenames` directory to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Changelog

### 1.1
* Added plugin settings to media settings page with option to convert all file types or just image file types. 
* Added shortcut to plugin settings from the plugins list.

### 1.0
* Initial release.
