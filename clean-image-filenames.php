<?php 
/**
 * Plugin Name: Clean Image Filenames
 * Description: This plugin automatically converts language accent characters to non-accent characters in filenames when uploading to the media library.
 * Version: 1.4
 * Author: Upperdog
 * Author URI: https://upperdog.com
 * Author Email: hello@upperdog.com
 * Text Domain: clean-image-filenames
 * Domain Path: /languages
 * License: GPLv3
 *
 * @package   clean-image-filenames
 * @link      https://github.com/upperdog/clean-image-filenames
 * @author    Upperdog <hello@upperdog.com>
 * @copyright Upperdog
 * @license   GPLv3
 */

// Exit if called incorrectly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean Image Filenames.
 */
class CleanImageFilenames {

	/**
	 * Plugin settings.
	 *
	 * @since 1.1
	 *
	 * @var array $plugin_settings Plugin settings for version, default mime types.
	 */
	public $plugin_settings = array(
		'version'            => '1.4',
		'default_mime_types' => array(
			'image/gif',
			'image/jpeg',
			'image/pjpeg',
			'image/png',
			'image/tiff',
		),
	);

	/**
	 * Sets up hooks, actions and filters that the plugin responds to.
	 *
	 * @since 1.0
	 * @since 1.4 Added wp_handle_sideload_prefilter hook.
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_links' ) );
		add_action( 'wp_handle_upload_prefilter', array( $this, 'upload_filter' ) );
		add_action( 'wp_handle_sideload_prefilter', array( $this, 'upload_filter' ) );
		add_action( 'add_attachment', array( $this, 'update_attachment_title' ) );
	}

	/**
	 * Adds default plugin settings on plugin activation.
	 *
	 * @since 1.1
	 */
	public function plugin_activation() {
		$this->add_default_plugin_settings();
	}

	/**
	 * Plugins loaded hook.
	 *
	 * Checks current plugin version. If the plugin has been updated, the saved
	 * plugin version in the database is updated.
	 *
	 * Adds default plugin settings if they don't already exist. Default plugin
	 * settings didn't exist prior to version 1.1.
	 *
	 * @since 1.1
	 */
	public function plugins_loaded() {

		// Update plugin version database setting if it's not up to date.
		if ( get_option( 'clean_image_filenames_plugin_version' ) !== $this->plugin_settings['version'] ) {
			update_option( 'clean_image_filenames_plugin_version', $this->plugin_settings['version'] );
		}

		// Add default plugin settings to database.
		$this->add_default_plugin_settings();
	}

	/**
	 * Adds default plugin settings to the database.
	 *
	 * This function runs when the plugin is activated and when plugins are loaded
	 * using the plugins_loaded hook. The function updates default plugin settings
	 * in the database options table. It adds default value for mime types field
	 * if it doesn't already exist
	 *
	 * @since 1.1
	 */
	public function add_default_plugin_settings() {

		if ( false === get_option( 'clean_image_filenames_mime_types' ) ) {
			add_option( 'clean_image_filenames_mime_types', 'images' );
		}
	}

	/**
	 * Sets up plugin translations and plugin settings fields.
	 *
	 * @since 1.1
	 */
	public function admin_init() {

		// Load plugin translations.
		load_plugin_textdomain( 'clean-image-filenames', false, dirname( plugin_basename( __FILE__ ) ) . 'languages' );

		// Add settings section.
		add_settings_section( 'clean_image_filenames_settings_section', 'Clean Image Filenames', array( $this, 'clean_image_filenames_settings_section_callback' ), 'media' );

		// Add settings field.
		add_settings_field(
			'clean_image_filenames_mime_types', 
			__( 'File types', 'clean-image-filenames' ), 
			array( $this, 'clean_image_filenames_mime_types_callback' ), 
			'media',
			'clean_image_filenames_settings_section',
			array(
				'alternatives' => array(
					array(
						'value' => 'all',
						'label' => __( 'All file types', 'clean-image-filenames' ),
					),
					array(
						'value' => 'images',
						'label' => __( 'Images only', 'clean-image-filenames' ),
					),
				),
			)
		);

		// Register settings.
		register_setting( 'media', 'clean_image_filenames_mime_types' );
	}

	/**
	 * Add custom action links to the plugin's row in the plugins list.
	 *
	 * @since 1.1
	 *
	 * @param array $links Default plugin action links.
	 * @return array $links Modified plugin action links.
	 */
	public function add_action_links( $links ) {
		$plugin_action_links = array( '<a href="' . admin_url( 'options-media.php' ) . '">' . __( 'Settings' ) . '</a>' );
		return array_merge( $links, $plugin_action_links );
	}

	/**
	 * Outputs content before the settings fields.
	 *
	 * @since 1.1
	 */
	public function clean_image_filenames_settings_section_callback() {
		echo '<p>' . esc_html_e( 'Choose which file types that Clean Image Filenames shall improve the filenames for when files are uploaded.', 'clean-image-filenames' ) . '</p>';
	}

	/**
	 * Output plugin settings fields.
	 *
	 * If the plugin filter has not been used, the user selected setting of what
	 * file types to clean is used. If the plugin filter has been used in a plugin
	 * or theme, the filter overrides the settings are the settings are therefore
	 * disabled.
	 *
	 * @since 1.1
	 *
	 * @param array $args Field details.
	 */
	public function clean_image_filenames_mime_types_callback( $args ) {

		if ( apply_filters( 'clean_image_filenames_mime_types', $this->plugin_settings['default_mime_types'] ) !== $this->plugin_settings['default_mime_types'] ) {
			echo '<input name="clean_image_filenames_mime_types" id="clean_image_filenames_mime_types" type="hidden" value="' . get_option( 'clean_image_filenames_mime_types' ) . '">';
			echo '<i>' . esc_html_e( 'The setting for what file types should be cleaned is disabled since a plugin or theme has already defined what file types should be cleaned.', 'clean-image-filenames' ) . '</i>';
		} else {

			foreach ( $args['alternatives'] as $alternative ) {
				echo '<label><input name="clean_image_filenames_mime_types" id="clean_image_filenames_mime_types" type="radio" value="' . $alternative['value'] . '" ' . checked( $alternative[ 'value' ], get_option( 'clean_image_filenames_mime_types' ), false ) . '>' . $alternative['label'] . '</label><br>';
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
	 *
	 * @param array $file The file information including the filename in $file['name'].
	 * @return array $file The file information with the cleaned or original filename.
	 */
	public function upload_filter( $file ) {

		// Save original filename in transient and add to attachment post later.
		$original_filename = pathinfo( $file['name'] );
		set_transient( '_clean_image_filenames_original_filename', $original_filename['filename'], 60 );

		// Get mime type settings.
		$mime_types_setting = get_option( 'clean_image_filenames_mime_types' );
		$default_mime_types = $this->plugin_settings['default_mime_types'];
		$valid_mime_types   = apply_filters( 'clean_image_filenames_mime_types', $default_mime_types );

		if ( $valid_mime_types !== $default_mime_types ) {

			// Use mime types defined with filter.
			if ( in_array( $file['type'], $valid_mime_types, true ) ) {
				$file = $this->clean_filename( $file );
			}
		} else {

			// Use mime types as defined in settings.
			if ( 'all' === $mime_types_setting ) {
				$file = $this->clean_filename( $file );
			} elseif ( 'images' === $mime_types_setting && in_array( $file['type'], $default_mime_types, true ) ) {
				$file = $this->clean_filename( $file );
			}
		}

		// Return cleaned file, or input file file type is not set to be cleaned.
		return $file;
	}

	/**
	 * Clean filename.
	 *
	 * Performs the cleaning of filenames. It replaces whitespaces, some specific
	 * chacters and uses WordPress' remove_accents() function for the rest. It
	 * also converts filenames to lowercase.
	 *
	 * @since 1.3 Rewrote filename cleaning to better handle specific characters.
	 * @since 1.1 Added function.
	 *
	 * @param array $file Uploaded file details.
	 * @return array $file Uploaded file details with cleaned filename.
	 */
	public function clean_filename( $file ) {

		// Get file details.
		$file_pathinfo = pathinfo( $file['name'] );

		// Replace whitespaces with dashes.
		$cleaned_filename = str_replace( ' ', '-', $file_pathinfo['filename'] );

		// Specific replacements not handled at all or not handled well by remove_accents().
		$specific_replacements = array(
			'А'   => 'a',
			'Ά'   => 'a',
			'Á'   => 'a',
			'Α'   => 'a',
			'Ä'   => 'a',
			'Å'   => 'a',
			'Ã'   => 'a',
			'Â'   => 'a',
			'À'   => 'a',
			'α'   => 'a',
			'Ą'   => 'a',
			'а'   => 'a',
			'ά'   => 'a',
			'Æ'   => 'ae',
			'å'   => 'a',
			'ä'   => 'a',
			'б'   => 'b',
			'Б'   => 'b',
			'Ć'   => 'c',
			'Ç'   => 'c',
			'ц'   => 'c',
			'Ц'   => 'c',
			'Č'   => 'c',
			'Ч'   => 'ch',
			'χ'   => 'ch',
			'ч'   => 'ch',
			'Χ'   => 'ch',
			'д'   => 'd',
			'Ď'   => 'd',
			'Д'   => 'd',
			'δ'   => 'd',
			'Δ'   => 'd',
			'Ð'   => 'd',
			'ε'   => 'e',
			'έ'   => 'e',
			'Έ'   => 'e',
			'Э'   => 'e',
			'Ę'   => 'e',
			'Ε'   => 'e',
			'э'   => 'e',
			'Ê'   => 'e',
			'Ě'   => 'e',
			'É'   => 'e',
			'Е'   => 'e',
			'е'   => 'e',
			'È'   => 'e',
			'Ë'   => 'e',
			'Φ'   => 'f',
			'Ф'   => 'f',
			'φ'   => 'f',
			'ф'   => 'f',
			'Γ'   => 'g',
			'γ'   => 'g',
			'ґ'   => 'g',
			'Г'   => 'g',
			'Ґ'   => 'g',
			'г'   => 'g',
			'Х'   => 'h',
			'х'   => 'h',
			'Ή'   => 'i',
			'Ί'   => 'i',
			'І'   => 'i',
			'і'   => 'i',
			'ΐ'   => 'i',
			'Η'   => 'i',
			'Ι'   => 'i',
			'η'   => 'i',
			'ϊ'   => 'i',
			'ι'   => 'i',
			'Ï'   => 'i',
			'Ì'   => 'i',
			'ή'   => 'i',
			'Í'   => 'i',
			'Î'   => 'i',
			'ί'   => 'i',
			'Ϊ'   => 'i',
			'и'   => 'i',
			'И'   => 'i',
			'й'   => 'j',
			'Й'   => 'j',
			'Я'   => 'ja',
			'я'   => 'ja',
			'ю'   => 'ju',
			'Ю'   => 'ju',
			'Κ'   => 'k',
			'κ'   => 'k',
			'к'   => 'k',
			'К'   => 'k',
			'л'   => 'l',
			'Λ'   => 'l',
			'λ'   => 'l',
			'Ł'   => 'l',
			'Л'   => 'l',
			'Μ'   => 'm',
			'м'   => 'm',
			'М'   => 'm',
			'μ'   => 'm',
			'Ñ'   => 'n',
			'ν'   => 'n',
			'Ν'   => 'n',
			'н'   => 'n',
			'Ň'   => 'n',
			'Ń'   => 'n',
			'Н'   => 'n',
			'ώ'   => 'o',
			'Ò'   => 'o',
			'ό'   => 'o',
			'Ő'   => 'o',
			'Ώ'   => 'o',
			'Õ'   => 'o',
			'Ο'   => 'o',
			'Ø'   => 'o',
			'ο'   => 'o',
			'Ό'   => 'o',
			'Ω'   => 'o',
			'Ó'   => 'o',
			'Ö'   => 'o',
			'ö'   => 'o',
			'О'   => 'o',
			'о'   => 'o',
			'ω'   => 'o',
			'Ô'   => 'o',
			'п'   => 'p',
			'þ'   => 'p',
			'π'   => 'p',
			'Π'   => 'p',
			'П'   => 'p',
			'Þ'   => 'p',
			'Ψ'   => 'ps',
			'ψ'   => 'ps',
			'Р'   => 'r',
			'Ř'   => 'r',
			'Ρ'   => 'r',
			'р'   => 'r',
			'ρ'   => 'r',
			'С'   => 's',
			'σ'   => 's',
			'Ś'   => 's',
			'ς'   => 's',
			'Σ'   => 's',
			'Š'   => 's',
			'с'   => 's',
			'Ш'   => 'sh',
			'ш'   => 'sh',
			'щ'   => 'shch',
			'Щ'   => 'shch',
			'ß'   => 'ss',
			'Τ'   => 't',
			'τ'   => 't',
			'Ť'   => 't',
			'т'   => 't',
			'Т'   => 't',
			'θ'   => 'th',
			'Θ'   => 'th',
			'Ў'   => 'u',
			'ў'   => 'u',
			'Ű'   => 'u',
			'Ú'   => 'u',
			'У'   => 'u',
			'Ù'   => 'u',
			'Û'   => 'u',
			'Ů'   => 'u',
			'у'   => 'u',
			'ü'   => 'u',
			'Ü'   => 'u',
			'в'   => 'v',
			'В'   => 'v',
			'Β'   => 'v',
			'β'   => 'v',
			'Ξ'   => 'x',
			'×'   => 'x',
			'ξ'   => 'x',
			'Ϋ'   => 'y',
			'Ÿ'   => 'y',
			'Ý'   => 'y',
			'Υ'   => 'y',
			'υ'   => 'y',
			'Ύ'   => 'y',
			'ύ'   => 'y',
			'ΰ'   => 'y',
			'ϋ'   => 'y',
			'ы'   => 'y',
			'Ы'   => 'y',
			'є'   => 'ye',
			'Є'   => 'ye',
			'ї'   => 'yi',
			'Ї'   => 'yi',
			'ё'   => 'yo',
			'Ё'   => 'yo',
			'з'   => 'z',
			'Ź'   => 'z',
			'З'   => 'z',
			'Ž'   => 'z',
			'ζ'   => 'z',
			'Ζ'   => 'z',
			'Ż'   => 'z',
			'Ж'   => 'zh',
			'ж'   => 'zh',
			'_'   => '-',
			'%20' => '-',
		);

		// Replace specific characters.
		$cleaned_filename = str_replace( array_keys( $specific_replacements ), array_values( $specific_replacements ), $cleaned_filename );

		// Convert characters to ASCII equivalents.
		$cleaned_filename = remove_accents( $cleaned_filename );

		// Convert filename to lowercase.
		$cleaned_filename = strtolower( $cleaned_filename );

		// Remove characters that are not a-z, 0-9, or - (dash).
		$cleaned_filename = preg_replace( '/[^a-z0-9-]/', '', $cleaned_filename );

		// Remove multiple dashes in a row.
		$cleaned_filename = preg_replace( '/-+/', '-', $cleaned_filename );

		// Trim potential leftover dashes at each end of filename.
		$cleaned_filename = trim( $cleaned_filename, '-' );

		// Replace original filename with cleaned filename.
		$file['name'] = $cleaned_filename . '.' . $file_pathinfo['extension'];

		return $file;
	}

	/**
	 * Save original filename to attachment title.
	 *
	 * The original, un-cleaned filename is saved as a transient called
	 * _clean_image_filenames_original_filename just before the filename is cleaned
	 * and saved. When WordPress adds the attachment to the database, this function
	 * picks up the original filename from the transient and saves it as the
	 * attachment title.
	 *
	 * @since 1.2
	 *
	 * @param int $attachment_id Attachment post ID.
	 */
	public function update_attachment_title( $attachment_id ) {

		$original_filename = get_transient( '_clean_image_filenames_original_filename' );

		if ( $original_filename ) {

			// Update attachment post.
			wp_update_post(
				array(
					'ID'         => $attachment_id,
					'post_title' => $original_filename,
				)
			);

			// Delete transient.
			delete_transient( '_clean_image_filenames_original_filename' );
		}
	}
}

new CleanImageFilenames();
