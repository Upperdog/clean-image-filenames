# Sanitize Image Filenames

Say goodbye to bad image filenames like Château de Ferrières.jpg or Smörgåsbord.png and say hello to nice and clean filenames like chateau-de-ferrieres.jpg and smargasbord.png. This plugins sanitizes filenames for select mime types when the file is being uploaded to the WordPress media library. 

## Features

* Can be used for more than images
* Is multisite compatible
* Works with custom upload_dir setups
* Doesn't alter your database or uploads settings

## Add your own mime types

You can easily use this plugin for any file type you want by adding mroe mime types using the `cifn_valid_mime_types` filter in your theme or plugin. Example usage: 

<pre><code>function custom_cifn_valid_mime_types() {

	$valid_mime_types = array(
		'application/pdf', 
		'image/jpeg', 
		'image/png', 
	);

	return $valid_mime_types;
}
add_filter('cifn_valid_mime_types', 'custom_cifn_valid_mime_types');</code></pre>

## Installation

1. Upload the `clean-image-filenames` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Changelog

* 1.0: Initial release.