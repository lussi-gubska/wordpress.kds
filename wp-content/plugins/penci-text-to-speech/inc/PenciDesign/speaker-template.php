<?php
/**
 * Template Name: Penci Text To Speech Template
 * File: speaker-template.php
 **/


/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

get_header();

?>
    <div class="penci-texttospeech-content-start"></div><?php
if ( have_posts() ) {

	while ( have_posts() ) {

		the_post();

		$subtitle = get_post_meta( get_the_ID(), 'penci_post_sub_title', true );

		/** Include title in audio version? */
		if ( get_theme_mod( 'penci_texttospeech_read_title', true ) ) {
			?><h1><?php the_title(); ?></h1>
            <break time="1s"></break><?php

			if ( $subtitle ) {
				?>
                <h2><?php echo esc_attr( $subtitle ); ?></h2>
                <break time="1s"></break>
				<?php
			}
		}


		add_filter( 'strip_shortcodes_tagnames', function () {
			$default_shortcodes = [
				'penci_video',
				'inline_related_posts',
				'portfolio',
				'penci_recipe',
				'penci_index',
				'penci_end_smartlists'
			];

			$shortcodes = get_theme_mod( 'penci_texttospeech_excluded_shortcode' );

			return $shortcodes ? array_merge( explode( ',', $shortcodes ), $default_shortcodes ) : $default_shortcodes;
		}, 10 );

		$content = strip_shortcodes( get_the_content() );
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		remove_filter( 'the_content', 'penci_insert_post_content_ads' );
		echo $content;

	}

}
?>
    <div class="penci-texttospeech-content-end"></div><?php

get_footer();
