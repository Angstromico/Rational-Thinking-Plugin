<?php
/*
Plugin Name: Rational Thinking
Plugin URI: http://wordpress.org/plugins/rational-thinking/
Description: This is not just a plugin, it symbolizes the importance of skepticism and rational thought. When activated you will randomly see a quote from famous rationalists like Carl Sagan, Bertrand Russell, or Richard Feynman in the upper right of your admin screen on every page.
Author: Memz
Version: 1.0.0
Domain Path: /languages
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: rational-thinking
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get available languages from the languages directory.
 *
 * @return array Associative array of language codes and names.
 */
function rational_thinking_get_available_languages() {
	$languages = array(
		'en' => 'English',
	);

	$files = glob( plugin_dir_path( __FILE__ ) . 'languages/*.php' );
	if ( $files ) {
		foreach ( $files as $file ) {
			$code = basename( $file, '.php' );
			if ( 'en' === $code ) {
				continue;
			}
			// Map codes to names
			$names = array(
				'es' => 'Español (Spanish)',
				'pt' => 'Português (Portuguese)',
				'fr' => 'Français (French)',
				'de' => 'Deutsch (German)',
				'zh' => '中文 (Chinese)',
				'nl' => 'Nederlands (Dutch)',
				'fi' => 'Suomi (Finnish)',
				'it' => 'Italiano (Italian)',
				'ja' => '日本語 (Japanese)',
				'ru' => 'Русский (Russian)',
			);
			$languages[ $code ] = isset( $names[ $code ] ) ? $names[ $code ] : strtoupper( $code );
		}
	}
	return $languages;
}

/**
 * Get the data (quotes and settings) for a specific language.
 *
 * @param string $lang Language code.
 * @return array Associative array containing 'quotes' and 'settings'.
 */
function rational_thinking_get_language_data( $lang = 'en' ) {
	$file = plugin_dir_path( __FILE__ ) . 'languages/' . $lang . '.php';
	
	$data = array();
	if ( file_exists( $file ) ) {
		$data = include $file;
	} else {
		// Fallback to English
		$en_file = plugin_dir_path( __FILE__ ) . 'languages/en.php';
		if ( file_exists( $en_file ) ) {
			$data = include $en_file;
		}
	}

	// Normalize data structure (Backward compatibility for simple array files)
	if ( isset( $data[0] ) ) {
		$data = array(
			'quotes'   => $data,
			'settings' => array(), // Will be merged with defaults below
		);
	}

	// Default UI Strings (English)
	$defaults = array(
		'title'          => 'Rational Thinking Settings',
		'language_label' => 'Quote Language',
		'description'    => 'Select the language for the daily quotes. This setting is unique to your user account.',
		'save_button'    => 'Save Changes',
		'saved_message'  => 'Language preference saved.',
	);

	if ( ! isset( $data['settings'] ) ) {
		$data['settings'] = array();
	}

	$data['settings'] = wp_parse_args( $data['settings'], $defaults );

	if ( ! isset( $data['quotes'] ) ) {
		$data['quotes'] = array( "Rationality is the compass of the mind." );
	}

	return $data;
}

/**
 * Determine the current language for the user.
 *
 * Checks user preference first, then falls back to WordPress locale.
 *
 * @return string Language code (e.g., 'en', 'es').
 */
function rational_thinking_get_current_language() {
	// Check if user has a specific preference saved
	$user_id   = get_current_user_id();
	$user_lang = get_user_meta( $user_id, 'rational_thinking_lang', true );

	if ( ! empty( $user_lang ) ) {
		return $user_lang;
	}

	// Fallback to WP locale
	$locale = get_user_locale();
	if ( strpos( $locale, 'es_' ) === 0 ) {
		return 'es';
	}

	// Default to English
	return 'en';
}

function rational_thinking_get_quote() {
	$lang = rational_thinking_get_current_language();
	$data = rational_thinking_get_language_data( $lang );
	$quotes_list = $data['quotes'];

	// Randomly choose a line
	return $quotes_list[ mt_rand( 0, count( $quotes_list ) - 1 ) ];
}

// This just echoes the chosen line, we'll position it later.
function rational_thinking() {
	$chosen = rational_thinking_get_quote();
	$lang   = rational_thinking_get_current_language();
	$attr   = '';

	// Add lang attribute if not English (or generic logic)
	// If the text is in a different language than the page, it's good practice.
	// For simplicity, we can just output the lang attribute matching our internal code.
	$attr = sprintf( ' lang="%s"', esc_attr( $lang ) );

	printf(
		'<p id="rational-thinking"><span class="screen-reader-text">%s </span><span dir="ltr"%s>%s</span></p>',
		esc_html__( 'Quote from a rational thinker:', 'rational-thinking' ),
		$attr,
		wp_kses_post( wptexturize( $chosen ) )
	);
}

// Now we set that function up to execute when the admin_notices action is called.
add_action( 'admin_notices', 'rational_thinking' );

// Enqueue CSS
function rational_thinking_enqueue_scripts() {
	wp_enqueue_style( 'rational-thinking', plugin_dir_url( __FILE__ ) . 'css/rational-thinking.css', array(), '1.0.0' );
}

add_action( 'admin_enqueue_scripts', 'rational_thinking_enqueue_scripts' );

/**
 * Register the settings page.
 */
function rational_thinking_add_settings_page() {
	add_options_page(
		'Rational Thinking Settings',
		'Rational Thinking',
		'read', // Capability required
		'rational-thinking',
		'rational_thinking_render_settings_page'
	);
}
add_action( 'admin_menu', 'rational_thinking_add_settings_page' );

add_action( 'admin_init', 'rational_thinking_handle_save' );

/**
 * Handle settings save on admin_init.
 */
function rational_thinking_handle_save() {
	if ( isset( $_POST['rational_thinking_save'] ) && check_admin_referer( 'rational_thinking_options_update' ) ) {
		// Verify permissions
		if ( ! current_user_can( 'read' ) ) {
			return;
		}

		$new_lang = isset( $_POST['rational_thinking_lang'] ) ? sanitize_text_field( $_POST['rational_thinking_lang'] ) : 'en';
		update_user_meta( get_current_user_id(), 'rational_thinking_lang', $new_lang );
		
		// Optional: Redirect to avoid form resubmission? 
		// For now, we stick to the requested fix (updating immediately) without enforcing a redirect loop,
		// relying on the fact that update_user_meta clears cache for the current request.
	}
}

/**
 * Render the settings page.
 */
function rational_thinking_render_settings_page() {
	$current_lang = rational_thinking_get_current_language();
	// Load data for the *current* language to get translated UI strings
	$data         = rational_thinking_get_language_data( $current_lang );
	$ui           = $data['settings'];

	// Check if we just saved to display the message
	if ( isset( $_POST['rational_thinking_save'] ) && check_admin_referer( 'rational_thinking_options_update' ) ) {
		echo '<div class="updated"><p>' . esc_html( $ui['saved_message'] ) . '</p></div>';
	}

	$languages    = rational_thinking_get_available_languages();
	?>
	<div class="wrap">
		<h1><?php echo esc_html( $ui['title'] ); ?></h1>
		<form method="post" action="">
			<?php wp_nonce_field( 'rational_thinking_options_update' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="rational_thinking_lang"><?php echo esc_html( $ui['language_label'] ); ?></label></th>
					<td>
						<select name="rational_thinking_lang" id="rational_thinking_lang">
							<?php foreach ( $languages as $code => $label ) : ?>
								<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $current_lang, $code ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php echo esc_html( $ui['description'] ); ?></p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="rational_thinking_save" id="submit" class="button button-primary" value="<?php echo esc_attr( $ui['save_button'] ); ?>">
			</p>
		</form>
	</div>
	<?php
}
