<?php

class Penci_PodCast_Player {

	/**
	 * Instance of Player
	 *
	 * @var Penci_PodCast_Player
	 */
	private static $instance;

	protected $shortcode = array(
		'player' => 'pencipdc_player',
		'track'  => 'pencipdc_player_track',
	);

	protected $total = 0;

	protected $types = array(
		'audio',
	);

	protected $type;

	/**
	 * Player constructor.
	 */
	private function __construct() {
		add_action( 'template_redirect', array( $this, 'render_player' ) );
		add_action( 'penci_action_before_the_content', array( $this, 'render_player_in_post' ), 99 );
		add_shortcode( $this->shortcode['player'], array( $this, 'player_shortcode' ) );
		add_shortcode( $this->shortcode['track'], array( $this, 'player_track_shortcode' ) );
		add_filter( 'wp_print_styles', array( $this, 'add_single_episode_to_playlist' ) );

		add_action( 'wp_ajax_get_episode_data', array( $this, 'get_episode_data_action' ) );
		add_action( 'wp_ajax_nopriv_get_episode_data', array( $this, 'get_episode_data_action' ) );
		add_action( 'wp_ajax_get_episode_data_by_series', array( $this, 'get_episode_data_by_series_action' ) );
		add_action( 'wp_ajax_nopriv_get_episode_data_by_series', array( $this, 'get_episode_data_by_series_action' ) );
	}

	/**
	 * Singleton page of Player class
	 *
	 * @return Penci_PodCast_Player
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Callback for [pencipdc_player] shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function player_shortcode( $atts = array(), $content = '' ) {
		$html = '';
		if ( defined( 'PENCI_SOLEDAD_VERSION' ) ) {
			static $instance = 0;
			$instance ++;
			wp_enqueue_script( 'pencipdc-jplayer', PENCI_PODCAST_URL . '/assets/js/jquery.jplayer.js', array( 'jquery' ), PENCI_PODCAST_VERSION, true );
			wp_enqueue_script( 'pencipdc-jplayer-playlist', PENCI_PODCAST_URL . '/assets/js/pencipdc.playlist.js', array( 'jquery' ), PENCI_PODCAST_VERSION, true );
			$cover_item          = '<div class="pencipdc_player_current_item__cover"></div>';
			$item_title_wrapper  = '<div class="pencipdc_player_current_item__title_wrapper">
								       <div class="pencipdc_player_current_item__title pencipdc_post_title"><span>-</span></div>
								   </div>';
			$pencipdc_player_bar = '<div class="pencipdc_player_bar">
										<div class="pencipdc_player_bar__current_time"><span>00:00</span></div>
										<div class="pencipdc_progress_bar">
											<div class="pencipdc_progress_bar__seek">
												<div class="pencipdc_progress_bar__play"><div tabindex="-1" class="pencipdc_progress_bar__ball"></div></div>
											</div>
										</div>
										<div class="pencipdc_player_bar__duration"><span>00:00</span></div>
									</div>';
			$pencipdc_playlist   = '<div class="pencipdc_playlist">
										<ul class="pencipdc_playlist_inner">
											<li></li>
										</ul>
									</div>';
			$main_control        =
				'<a href="#" class="pencipdc_player_control__previous disabled" tabindex="1" title="' . pencipdc_translate( 'previous' ) . '"><i class="fa fa-step-backward"></i></a>' .
				'<a href="#" class="pencipdc_player_control__play" tabindex="1" title="' . pencipdc_translate( 'play' ) . '"><i class="fa fa-play"></i></a>' .
				'<a href="#" class="pencipdc_player_control__pause" tabindex="1" title="' . pencipdc_translate( 'pause' ) . '"><i class="fa fa-pause"></i></a>' .
				'<a href="#" class="pencipdc_player_control__next" tabindex="1" title="' . pencipdc_translate( 'next' ) . '"><i class="fa fa-step-forward"></i></a>';
			$shuffle_btn         =
				'<a href="#" class="pencipdc_player_control__shuffle_off" tabindex="1" title="' . pencipdc_translate( 'shuffle' ) . '"><i class="fa fa-random"></i></a>' .
				'<a href="#" class="pencipdc_player_control__shuffle" tabindex="1" title="' . pencipdc_translate( 'shuffle' ) . '"><i class="fa fa-random"></i></a>';
			$repeat_btn          =
				'<a href="#" class="pencipdc_player_control__repeat_off" tabindex="1" title="' . pencipdc_translate( 'repeat' ) . '"><i class="fa fa-repeat"></i></a>' .
				'<a href="#" class="pencipdc_player_control__repeat" tabindex="1" title="' . pencipdc_translate( 'repeat' ) . '"><i class="fa fa-repeat"></i></a>';

			// Get Track
			$track = '';
			if ( ! empty( $content ) ) {
				$content = wp_strip_all_tags( nl2br( do_shortcode( $content ) ) );

				// Replace last comma
				if ( false !== ( $pos = strrpos( $content, ',' ) ) ) {
					$content = substr_replace( $content, '', $pos, 1 );
				}
				$track = '<script class="pencipdc_player_playlist_script" type="application/json">';
				$track .= $content;
				$track .= '</script>';
			}

			$html = '
			<div class="pencipdc_player audio_player style_' . $instance . '">
			    <div class="pencipdc_player_wrapper">
			        <div id="pencipdc-player-' . $instance . '" class="pencipdc_jplayer"></div>
			        <div id="pencipdc-player-container-' . $instance . '" class="pencipdc_audio">
			            <div class="pencipdc_player_inner">
			                <div class="pencipdc_player_controls_wrap">
			                    <div class="pencipdc_control_bar_left">
									<!-- player-control -->
									<div class="pencipdc_player_control">
										' . $main_control . '
									</div>
			                    </div>
			                    <div class="pencipdc_control_bar_center">
				                    <div class="pencipdc_player_current_item">
				                        ' . $cover_item . '
				                        <!-- player-progress -->
					                    <div class="pencipdc_player_current_item__content">
										   ' . $item_title_wrapper . $pencipdc_player_bar . '
					                    </div>
									</div>
								</div>
								<div class="pencipdc_control_bar_toggle_player">
									<a href="#" class="pencipdc_player_control__toggle_player" tabindex="1" title="' . pencipdc_translate('toggle_play') . '"><i class="fa fa-angle-up"></i></a>
								</div>
			                    <div class="pencipdc_control_bar_right">
				                    <!-- control-last -->
									<div class="pencipdc_player_control last">
											' . $shuffle_btn . $repeat_btn . '
				                            <a href="#" class="pencipdc_player_control__playlist_toggle" tabindex="1" title="" aria-label="' . pencipdc_translate('toggle_playlist') . '">
				                            	<i class="fa fa-list-ul"></i>
												<div class="pencipdc_player_control__playlist">
													
													<div class="pencipdc_block_heading">
														<h3 class="pencipdc_block_title">
															<span>' . pencipdc_translate('queue') . '</span>
														</h3>
														<span class="pencipdc_player_control__close_player">
														<i class="fa fa-angle-down"></i>
													</span>
													</div>
				                            		<ul class="pencipdc_player_control__playlist_inner">
								                        <li></li>
								                    </ul>
												</div>
				                            </a>
				                            <div class="pencipdc_player_bar__volume_icon">
					                            <a href="#" class="pencipdc_player_control__mute" tabindex="1" title="' . pencipdc_translate('mute') . '"><i class="fa fa-volume-up"></i></a>
					                            <a href="#" class="pencipdc_player_control__unmute" tabindex="1" title="' . pencipdc_translate('unmute') . '"><i class="fa fa-volume-off"></i></a>
					                        </div>
				                    </div>
				                    <div class="pencipdc_player_bar volume">
				                    	<div class="pencipdc_volume_bar_wrapper">
						                    <div title="' . pencipdc_translate('volume') . '" class="pencipdc_volume_bar">
						                        <div class="pencipdc_volume_bar__value"><div tabindex="-1" class="pencipdc_progress_bar__ball"></div></div>
						                    </div>
					                    </div>
				                    </div>
								</div>
			                </div>
			                ' . $pencipdc_playlist . '
			                <div class="pencipdc_no_solution">
			                    <span>Update Required</span>
			                    <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>
			                </div>
						</div>
						<div class="pencipdc_mobile_player_wrapper">
							<span class="pencipdc_player_control__close_player" data-playeropen>
								<i class="fa fa-angle-down"></i>
							</span>
							<div class="pencipdc_player_current_item_cover_container">
								' . $cover_item . '
							</div>
							' . $item_title_wrapper . '
							<div class="pencipdc_player_bar_container">
								' . $pencipdc_player_bar . '
							</div>
							' . $pencipdc_playlist . '
							<div class="pencipdc_player_control">
								' . $shuffle_btn . $main_control . $repeat_btn . '
							</div> 
						</div>
			        </div>
				</div>
				' . $track . '
			</div>';

		}

		return $html;
	}

	/**
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function player_track_shortcode( $atts = array(), $content = '' ) {
		$atts = shortcode_atts(
			array(
				'series'    => '',
				'thumbnail' => sprintf( '%s/wp-includes/images/media/%s.png', get_site_url(), 'audio' ),
				'title'     => '',
				'href'      => '',
				'src'       => '',
			),
			$atts,
			$this->shortcode['track']
		);

		$data['series_name']    = sanitize_text_field( $atts['series'] );
		$data['post_title']     = sanitize_text_field( $atts['title'] );
		$data['post_thumbnail'] = esc_url( $atts['thumbnail'] );
		$data['post_url']       = esc_url( $atts['href'] );
		$data['upload']         = esc_url( $atts['src'] );

		return wp_json_encode( $data ) . ',';
	}

	/**
	 *  Render Player
	 */
	public function render_player() {
		if ( get_theme_mod( 'pencipodcast_podcast_enable_player', true ) ) {
			add_action( 'wp_footer', array( $this, 'player' ) );
			add_filter( 'body_class', array( $this, 'add_body_class' ) );
		}
	}

	/**
	 * @param $classes
	 *
	 * @return array
	 */
	public function add_body_class( $classes ) {
		$classes[] = 'pencipdc_global_player';

		return $classes;
	}

	/**
	 * @param $post_id
	 */
	public function render_player_in_post( $post_id ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		$result      = $this->is_single_episode();
		$lock_player = false;
		$player      = '';
		if ( class_exists( '\PenciPaywall\ContentFilter' ) ) {
			$paywall_truncater = \PenciPaywall\ContentFilter::instance();
			if ( $paywall_truncater->check_status() ) {
				$paywall_truncater->show_button( true );
				$lock_player = true;
			}
		}
		if ( ! empty( $result ) && ! $lock_player && get_theme_mod( 'pencipodcast_podcast_enable_player', true ) ) {
			$data   = $this->get_episode_data( $post_id );
			$track  = "[pencipdc_player_track series='{$data['series_name']}' thumbnail='{$data['post_thumbnail']}' title='{$data['post_title']}' href='{$data['post_url']}' src='{$data['episode_upload']}' ]";
			$player = do_shortcode( '[pencipdc_player]' . $track . '[/pencipdc_player]' );
		}

		if ( ! empty( $result ) && ! $lock_player && get_theme_mod( 'pencipodcast_podcast_enable_player', true ) ) {
			$player = pencipdc_podcast_add_media_menu( $post_id, 'single_episode', 'plus' );
		}

		echo $player;
	}

	/**
	 * @return array|bool
	 */
	public function is_single_episode() {
		$result = false;

		if ( is_singular( 'podcast' ) ) {
			$post_id = get_the_ID();
			$args    = array(
				'post_id' => $post_id,
			);
			$result  = $this->set_player_data( $args );
		}

		return $result;
	}

	/**
	 * Set Player Data
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function set_player_data( $data ) {
		$multiple_data = false;
		$result        = array();
		if ( is_array( $data ) ) {
			if ( ! isset( $data['post_id'] ) ) {
				$multiple_data = true;
			}
			if ( $multiple_data ) {
				foreach ( $data as $key => $value ) {
					if ( isset( $value['post_id'] ) ) {
						$episode_data = $this->get_episode_data( $value['post_id'] );
						if ( ! empty( $episode_data ) ) {
							$result[] = array(
								'series_name'    => $episode_data['series_name'],
								'post_thumbnail' => $episode_data['post_thumbnail'],
								'post_title'     => $episode_data['post_title'],
								'post_url'       => $episode_data['post_url'],
								'upload'         => $episode_data['episode_upload'],
								'post_type'      => $episode_data['post_type'],
							);
						}
					}
				}
			} else {
				$episode_data = $this->get_episode_data( $data['post_id'] );
				if ( ! empty( $episode_data ) ) {
					$result = array(
						'series_name'    => $episode_data['series_name'],
						'post_thumbnail' => $episode_data['post_thumbnail'],
						'post_title'     => $episode_data['post_title'],
						'post_url'       => $episode_data['post_url'],
						'upload'         => $episode_data['episode_upload'],
						'post_type'      => $episode_data['post_type'],
					);
				}
			}
		}

		return $result;
	}

	/**
	 * Get episode data
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	public function get_episode_data( $post_id ) {
		$data   = array();
		$upload = get_post_meta( $post_id, 'pencipc_media_url', true );
		$enable = get_theme_mod( 'pencipodcast_podcast_enable_player', true );
		if ( $enable && $upload ) {
			$series = wp_get_post_terms( $post_id, 'podcast-series' );

			$series = is_wp_error( $series ) ? '' : $series;
			$series = is_array( $series ) ? ( ! empty( $series ) ? $series[0] : $series ) : $series;
			if ( has_post_thumbnail( $post_id ) ) {
				$image = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
			} else {
				$image = sprintf( '%s/wp-includes/images/media/%s.png', get_site_url(), 'audio' );
				if ( ! empty( $series ) ) {
					$attribute = pencipdc_podcast_attribute( $series->term_id, array( 'fields' => array( 'image' ) ) );
					if ( $attribute['image'] ) {
						$image = wp_get_attachment_image_url( $attribute['image'], 'post-thumbnail' );
					}
				}
			}
			if ( ! empty( $series ) ) {
				$data = array(
					'series_name'    => $series->name,
					'post_thumbnail' => $image,
					'post_title'     => get_the_title( $post_id ),
					'post_url'       => get_post_permalink( $post_id ),
					'episode_upload' => $upload,
					'post_type'      => get_post_type( $post_id ),
				);
				if ( class_exists( '\PenciPaywall\ContentFilter' ) ) {
					$paywall_truncater = \PenciPaywall\ContentFilter::instance();
					if ( $paywall_truncater->check_status( $post_id ) ) {
						$data['episode_upload'] = '';
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Player Template
	 */
	public function player() {
		load_template( PENCI_PODCAST_DIR . '/templates/player.php' );
	}

	/**
	 * add single to playlist
	 */
	public function add_single_episode_to_playlist() {
		$result = $this->is_single_episode();
		if ( ! empty( $result ) ) {
			wp_localize_script( 'penci-podcast', 'single_podcast_data', $result );
		}
	}

	/**
	 * ajax get podcast data
	 *
	 * @return mixed
	 */
	public function get_episode_data_action() {
		if ( isset( $_POST['post_id'] ) ) {
			$post_id = (int) sanitize_text_field( $_POST['post_id'] );
			$args    = array(
				'post_id' => $post_id,
			);
			$result  = $this->set_player_data( $args );
			if ( ! empty( $result ) ) {
				wp_send_json( $result );
			}
		}
		wp_send_json( false );
		die();
	}

	/**
	 * ajax get podcast data by series
	 *
	 * @return mixed
	 */
	public function get_episode_data_by_series_action() {
		if ( isset( $_POST['post_id'] ) ) {
			$data    = array();
			$post_id = (int) sanitize_text_field( $_POST['post_id'] );
			$podcast = get_posts(
				array(
					'post_type'   => 'podcast',
					'numberposts' => - 1,
					'tax_query'   => array(
						array(
							'taxonomy' => 'podcast-series',
							'field'    => 'term_id',
							'terms'    => $post_id,
						),
					),
				)
			);
			if ( $podcast ) {
				foreach ( $podcast as $key => $value ) {
					$data[] = array(
						'post_id' => $value->ID,
					);
				}
				$result = $this->set_player_data( $data );
				if ( ! empty( $result ) ) {
					wp_send_json( $result );
				}
			}
		}
		wp_send_json( false );
		die();
	}
}
