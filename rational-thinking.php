<?php
/**
 * @package Rational_Thinking
 * @version 1.0.0
 */
/*
Plugin Name: Rational Thinking
Plugin URI: http://wordpress.org/plugins/rational-thinking/
Description: This is not just a plugin, it symbolizes the importance of skepticism and rational thought. When activated you will randomly see a quote from famous rationalists like Carl Sagan, Bertrand Russell, or Richard Feynman in the upper right of your admin screen on every page.
Author: Memz
Version: 1.0.0

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: rational-thinking
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the list of available quotes.
 *
 * @return array
 */
function rational_thinking_get_quotes_list() {
	return array(
		'en' => array(
			"For me, it is far better to grasp the Universe as it really is than to persist in delusion, however satisfying and reassuring. - Carl Sagan",
			"Extraordinary claims require extraordinary evidence. - Carl Sagan",
			"We live in a society exquisitely dependent on science and technology, in which hardly anyone knows anything about science and technology. - Carl Sagan",
			"Who is more humble? The scientist who looks at the universe with an open mind and accepts whatever the universe has to teach us, or somebody who says everything in this book must be considered the literal truth and never mind the fallibility of all the human beings involved? - Carl Sagan",
			"The first principle is that you must not fool yourself and you are the easiest person to fool. - Richard Feynman",
			"I would rather have questions that can't be answered than answers that can't be questioned. - Richard Feynman",
			"Religion is a culture of faith; science is a culture of doubt. - Richard Feynman",
			"There is no harm in doubt and skepticism, for it is through these that new discoveries are made. - Richard Feynman",
			"The whole problem with the world is that fools and fanatics are always so certain of themselves, and wiser people so full of doubts. - Bertrand Russell",
			"Do not fear to be eccentric in opinion, for every opinion now accepted was once eccentric. - Bertrand Russell",
			"I would never die for my beliefs because I might be wrong. - Bertrand Russell",
			"In all affairs it's a healthy thing now and then to hang a question mark on the things you have long taken for granted. - Bertrand Russell",
			"Belief is the death of intelligence. - Robert Anton Wilson",
			"That which can be asserted without evidence, can be dismissed without evidence. - Christopher Hitchens",
			"We must respect the other fellow's religion, but only in the sense and to the extent that we respect his theory that his wife is beautiful and his children smart. - H.L. Mencken",
			"Skepticism is the chastity of the intellect, and it is shameful to surrender it too soon to the first comer. - George Santayana",
			"Science is the best tool we have for understanding the world. - Julia Galef",
			"Soldier mindset vs Scout mindset: The soldier wants to defend their beliefs, the scout wants to see things as they really are. - Julia Galef",
			"It is not enough to have a good mind; the main thing is to use it well. - Rene Descartes",
			"Honesty is the first chapter in the book of wisdom. - Thomas Jefferson",
		),
		'es' => array(
			"Para mí, es mucho mejor comprender el Universo tal como es realmente que persistir en el engaño, por muy satisfactorio y tranquilizador que sea. - Carl Sagan",
			"Las afirmaciones extraordinarias requieren pruebas extraordinarias. - Carl Sagan",
			"Vivimos en una sociedad exquisitamente dependiente de la ciencia y la tecnología, en la cual casi nadie sabe nada sobre ciencia y tecnología. - Carl Sagan",
			"¿Quién es más humilde? ¿El científico que mira el universo con una mente abierta y acepta lo que el universo tiene para enseñarnos, o alguien que dice que todo en este libro debe considerarse la verdad literal y no importa la falibilidad de todos los seres humanos involucrados? - Carl Sagan",
			"El primer principio es que no debes engañarte a ti mismo y tú eres la persona más fácil de engañar. - Richard Feynman",
			"Prefiero tener preguntas que no pueden ser respondidas que respuestas que no pueden ser cuestionadas. - Richard Feynman",
			"La religión es una cultura de fe; la ciencia es una cultura de duda. - Richard Feynman",
			"No hay daño en la duda y el escepticismo, porque es a través de estos que se hacen nuevos descubrimientos. - Richard Feynman",
			"El problema con el mundo es que los estúpidos están seguros de todo y los inteligentes están llenos de dudas. - Bertrand Russell",
			"No temas ser excéntrico en tu opinión, porque cada opinión aceptada ahora fue una vez excéntrica. - Bertrand Russell",
			"Nunca moriría por mis creencias porque podría estar equivocado. - Bertrand Russell",
			"En todos los asuntos es algo saludable de vez en cuando poner un signo de interrogación en las cosas que has dado por sentado durante mucho tiempo. - Bertrand Russell",
			"La creencia es la muerte de la inteligencia. - Robert Anton Wilson",
			"Lo que puede afirmarse sin pruebas puede desestimarse sin pruebas. - Christopher Hitchens",
			"Debemos respetar la religión del prójimo, pero sólo en el sentido y en la medida en que respetamos su teoría de que su esposa es hermosa y sus hijos inteligentes. - H.L. Mencken",
			"El escepticismo es la castidad del intelecto, y es vergonzoso rendirse demasiado pronto al primer llegado. - George Santayana",
			"La ciencia es la mejor herramienta que tenemos para entender el mundo. - Julia Galef",
			"Mentalidad de soldado vs mentalidad de explorador: El soldado quiere defender sus creencias, el explorador quiere ver las cosas como realmente son. - Julia Galef",
			"No basta con tener una buena mente; lo principal es usarla bien. - Rene Descartes",
			"La honestidad es el primer capítulo en el libro de la sabiduría. - Thomas Jefferson",
		),
	);
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
	$lang   = rational_thinking_get_current_language();
	$quotes = rational_thinking_get_quotes_list();

	// Fallback to English if language not found
	if ( ! isset( $quotes[ $lang ] ) ) {
		$lang = 'en';
	}

	$quotes_list = $quotes[ $lang ];

	// Randomly choose a line
	return wptexturize( $quotes_list[ mt_rand( 0, count( $quotes_list ) - 1 ) ] );
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
		__( 'Quote from a rational thinker:', 'rational-thinking' ),
		$attr,
		$chosen
	);
}

// Now we set that function up to execute when the admin_notices action is called.
add_action( 'admin_notices', 'rational_thinking' );

// We need some CSS to position the paragraph.
function rational_thinking_css() {
	echo "
	<style type='text/css'>
	#rational-thinking {
		float: right;
		padding: 5px 10px;
		margin: 0;
		font-size: 12px;
		line-height: 1.6666;
	}
	.rtl #rational-thinking {
		float: left;
	}
	.block-editor-page #rational-thinking {
		display: none;
	}
	@media screen and (max-width: 782px) {
		#rational-thinking,
		.rtl #rational-thinking {
			float: none;
			padding-left: 0;
			padding-right: 0;
		}
	}
	</style>
	";
}

add_action( 'admin_head', 'rational_thinking_css' );

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

/**
 * Render the settings page.
 */
function rational_thinking_render_settings_page() {
	// Handle form submission
	if ( isset( $_POST['rational_thinking_save'] ) && check_admin_referer( 'rational_thinking_options_update' ) ) {
		$new_lang = isset( $_POST['rational_thinking_lang'] ) ? sanitize_text_field( $_POST['rational_thinking_lang'] ) : 'en';
		update_user_meta( get_current_user_id(), 'rational_thinking_lang', $new_lang );
		echo '<div class="updated"><p>' . esc_html__( 'Language preference saved.', 'rational-thinking' ) . '</p></div>';
	}

	$current_lang = rational_thinking_get_current_language();
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Rational Thinking Settings', 'rational-thinking' ); ?></h1>
		<form method="post" action="">
			<?php wp_nonce_field( 'rational_thinking_options_update' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="rational_thinking_lang"><?php echo esc_html__( 'Quote Language', 'rational-thinking' ); ?></label></th>
					<td>
						<select name="rational_thinking_lang" id="rational_thinking_lang">
							<option value="en" <?php selected( $current_lang, 'en' ); ?>>English</option>
							<option value="es" <?php selected( $current_lang, 'es' ); ?>>Español (Spanish)</option>
						</select>
						<p class="description"><?php echo esc_html__( 'Select the language for the daily quotes. This setting is unique to your user account.', 'rational-thinking' ); ?></p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="rational_thinking_save" id="submit" class="button button-primary" value="<?php echo esc_attr__( 'Save Changes', 'rational-thinking' ); ?>">
			</p>
		</form>
	</div>
	<?php
}
