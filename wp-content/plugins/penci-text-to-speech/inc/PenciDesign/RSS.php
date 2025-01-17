<?php


namespace PenciDesign;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

final class RSS {

	/**
	 * @var RSS
	 */
	private static $instance;

	/**
	 * Add feeds on init
	 */
	private function __construct() {

		add_action( 'init', [ $this, 'add_rss' ] );

		add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

	}

	/**
	 * Add all feeds
	 * @return void
	 */
	public function add_rss() {


		if ( get_theme_mod( 'penci_texttospeech_podcasts_rss' ) ) {

			add_feed( 'penci-text-to-speech-podcast', [ $this, 'podcasts_rss_feed' ] );

		}

	}

	/**
	 * Remove pagination from query
	 *
	 * @param $query
	 *
	 * @return mixed
	 */
	public function pre_get_posts( $query ) {

		if ( $query->is_main_query() && $query->is_feed( 'google-podcasts' ) ) {

			$query->set( 'nopaging', 1 );

		}

		return $query;

	}

	/**
	 * Add Google Podcasts RSS feed
	 *
	 * IMPORTANT: DON'T TOUCH TABULATION and NEW LINES IN THIS METHOD!!!
	 * @return void
	 */
	public function podcasts_rss_feed() {

		header( 'Content-Type: ' . feed_content_type( 'rss' ) . '; charset=' . get_option( 'blog_charset' ), true );
		status_header( 200 );

		echo '<?xml version="1.0" encoding="UTF-8"?>';
		?>

        <rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
             xmlns:content="http://purl.org/rss/1.0/modules/content/">
            <channel>
				<?php
				$header_by_line = preg_split( "/\r\n|\n|\r/", RSS::get_instance()->get_podcast_header() );
				foreach ( $header_by_line as $line ) {
					if ( empty( $line ) ) {
						continue;
					}
					?>
					<?php print $line; ?>

				<?php } ?>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<?php
					$post_id = get_the_ID();
					if ( ! SpeechCaster::get_instance()->audio_exists( $post_id ) ) {
						continue;
					}
					$item_by_line = preg_split( "/\r\n|\n|\r/", RSS::get_instance()->get_podcast_item( $post_id ) );

					?>
                    <item>
						<?php foreach ( $item_by_line as $line ) {
							if ( empty( $line ) ) {
								continue;
							}
							?>
							<?php print $line; ?>

						<?php } ?>
                    </item>
				<?php endwhile; endif;
				?>
            </channel>
        </rss>
		<?php
		exit;

	}

	/**
	 * Get podcast header
	 *
	 *
	 * @return mixed|null
	 */
	private function get_podcast_header() {

		$default_postcast_header = '<title>[site-title]</title>
<description>[site-tagline]</description>
<language>[site-lang]</language>
<link>[site-url]</link>
<copyright>&#169; 2022 [site-title]</copyright>
<itunes:owner>
    <itunes:name>[site-title]</itunes:name>
    <itunes:email>[site-email]</itunes:email>
</itunes:owner>
<itunes:author>[site-title]</itunes:author>
<itunes:image href="[site-logo]"/>';

		$podcast_header = apply_filters( 'penci_tts_podcast_header_raw', $default_postcast_header );

		$podcast_header = str_replace(
			[
				'[site-title]',
				'[site-tagline]',
				'[site-url]',
				'[site-lang]',
				'[site-logo]',
				'[site-email]'
			],
			[
				get_bloginfo( 'name' ),
				get_bloginfo( 'description' ),
				get_bloginfo( 'url' ),
				get_bloginfo( 'language' ),
				$this->get_custom_logo_url(),
				get_bloginfo( 'admin_email' ),
			],
			$podcast_header );

		return apply_filters( 'penci_tts_podcast_header', $podcast_header );

	}

	private function get_podcast_item( $post_id ) {

		$default_postcast_item = '
<title>[item_title]</title>
<description>[item_excerpt]</description>
<pubDate>[item_date]</pubDate>
<enclosure url="[item_url]" type="[item_type]" length="[item_length]"></enclosure>
<itunes:duration>[item_duration]</itunes:duration>
<itunes:image href="[item_thumbnail]"/>
<guid>[item_guid]</guid>
';

		$podcast_item = apply_filters( 'penci_tts_podcast_item_raw', $default_postcast_item );

		$podcast_item = str_replace(
			[
				'[item_id]',
				'[item_title]',
				'[item_excerpt]',
				'[item_date]',
				'[item_url]',
				'[item_type]',
				'[item_length]',
				'[item_duration]',
				'[item_thumbnail]',
				'[item_guid]'
			],
			[
				$post_id,
				get_the_title( $post_id ),
				get_the_excerpt( $post_id ),
				get_the_date( 'l, d F Y H:i:s T', $post_id ),
				explode( '?', SpeechCaster::get_instance()->get_audio_url( $post_id ) )[0],
				SpeechCaster::get_instance()->get_audio_meta( $post_id, 'mime_type' ),
				SpeechCaster::get_instance()->get_audio_meta( $post_id, 'filesize' ),
				SpeechCaster::get_instance()->get_audio_meta( $post_id, 'length_formatted' ),
				get_the_post_thumbnail_url( $post_id ) ?? '',
				$this->get_guid( $post_id )
			],
			$podcast_item );

		return apply_filters( 'penci_tts_podcast_item', $podcast_item );

	}

	/**
	 * Get post guid
	 * @return mixed|null
	 */
	private function get_guid( $post_id ) {

		$site_uid = str_replace( '/', '', get_bloginfo( 'url' ) );
		$site_uid = str_replace( 'https:', '', $site_uid );
		$site_uid = str_replace( 'http:', '', $site_uid );
		$guid     = 'penci-text-to-speech' . '-' . $site_uid . '-' . $post_id;

		return apply_filters( 'penci_tts_podcast_guid', $guid );

	}

	/**
	 * Get custom logo url from customizer
	 * @return mixed|string
	 */
	private function get_custom_logo_url() {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( ! $custom_logo_id ) {
			return '';
		}

		$image = wp_get_attachment_image_src( $custom_logo_id, 'full' );

		return $image[0];
	}

	/**
	 * Main RSS Instance.
	 *
	 * Insures that only one instance of RSS exists in memory at any one time.
	 *
	 * @static
	 * @return RSS
	 * @since 3.0.0
	 **/
	public static function get_instance() {

		/** @noinspection SelfClassReferencingInspection */
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof RSS ) ) {

			/** @noinspection SelfClassReferencingInspection */
			self::$instance = new RSS;

		}

		return self::$instance;

	}

}
