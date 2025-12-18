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
Author URI: http://example.com/
*/

function rational_thinking_get_quote() {
	/** These are the quotes for Rational Thinking */
	$quotes = "For me, it is far better to grasp the Universe as it really is than to persist in delusion, however satisfying and reassuring. - Carl Sagan
Extraordinary claims require extraordinary evidence. - Carl Sagan
We live in a society exquisitely dependent on science and technology, in which hardly anyone knows anything about science and technology. - Carl Sagan
Who is more humble? The scientist who looks at the universe with an open mind and accepts whatever the universe has to teach us, or somebody who says everything in this book must be considered the literal truth and never mind the fallibility of all the human beings involved? - Carl Sagan
The first principle is that you must not fool yourself and you are the easiest person to fool. - Richard Feynman
I would rather have questions that can't be answered than answers that can't be questioned. - Richard Feynman
Religion is a culture of faith; science is a culture of doubt. - Richard Feynman
There is no harm in doubt and skepticism, for it is through these that new discoveries are made. - Richard Feynman
The whole problem with the world is that fools and fanatics are always so certain of themselves, and wiser people so full of doubts. - Bertrand Russell
Do not fear to be eccentric in opinion, for every opinion now accepted was once eccentric. - Bertrand Russell
I would never die for my beliefs because I might be wrong. - Bertrand Russell
In all affairs it's a healthy thing now and then to hang a question mark on the things you have long taken for granted. - Bertrand Russell
Belief is the death of intelligence. - Robert Anton Wilson
That which can be asserted without evidence, can be dismissed without evidence. - Christopher Hitchens
We must respect the other fellow's religion, but only in the sense and to the extent that we respect his theory that his wife is beautiful and his children smart. - H.L. Mencken
Skepticism is the chastity of the intellect, and it is shameful to surrender it too soon to the first comer. - George Santayana
Science is the best tool we have for understanding the world. - Julia Galef
Soldier mindset vs Scout mindset: The soldier wants to defend their beliefs, the scout wants to see things as they really are. - Julia Galef
It is not enough to have a good mind; the main thing is to use it well. - Rene Descartes
Honesty is the first chapter in the book of wisdom. - Thomas Jefferson";

	// Here we split it into lines.
	$quotes = explode( "\n", $quotes );

	// And then randomly choose a line.
	return wptexturize( $quotes[ mt_rand( 0, count( $quotes ) - 1 ) ] );
}

// This just echoes the chosen line, we'll position it later.
function rational_thinking() {
	$chosen = rational_thinking_get_quote();
	$lang   = '';
	if ( 'en_' !== substr( get_user_locale(), 0, 3 ) ) {
		$lang = ' lang="en"';
	}

	printf(
		'<p id="rational-thinking"><span class="screen-reader-text">%s </span><span dir="ltr"%s>%s</span></p>',
		__( 'Quote from a rational thinker:', 'rational-thinking' ),
		$lang,
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
