=== Sanitize Image Filenames ===
Contributors: Upperdog, Gesen
Tags: upload, images, files, media, 
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Say goodbye to bad image filenames like Château-de-Ferrières.jpg or Smörgåsbord.png and say hello to nice and clean filenames like chateau-de-ferrieres.jpg and smargasbord.png. This plugins sanitizes filenames for select mime types when the file is being uploaded to the WordPress media library. 

== Description ==

Say goodbye to bad image filenames like Château-de-Ferrières.jpg or Smörgåsbord.png and say hello to nice and clean filenames like chateau-de-ferrieres.jpg and smargasbord.png. This plugins sanitizes filenames for select mime types when the file is being uploaded to the WordPress media library. 

= Features = 

* Add your own mime types
* Multisite compatible
* Works with custom upload_dir setups
* Doesn't alter your database or uploads settings

= Add your own mime types =

You can easily add you own mime types by using the sif_valid_mime_types filter in your theme or plugin. Example usage: 

`function custom_sif_valid_mime_types() {`
``
`	$valid_mime_types = array(`
`		'application/pdf', `
`		'image/jpeg', `
`		'image/png', `
`	);`
``
`	return $valid_mime_types;`
`}`
`add_filter('sif_valid_mime_types', 'custom_sif_valid_mime_types');`

== Installation ==

1. Upload the `sanitize-image-filenames` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= 1.0 =
* Initial release.