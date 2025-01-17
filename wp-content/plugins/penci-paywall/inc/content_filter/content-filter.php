<?php

namespace PenciPaywall;

class ContentFilter {
	/**
	 * @var ContentFilter
	 */
	private static $instance;

	/**
	 * @var string
	 */
	private $content_data;
	private $result;
	private $tag;

	/**
	 * @var boolean
	 */
	private $show_button = false;

	/**
	 * @var int
	 */
	private $total;

	/**
	 * ContentFilter constructor.
	 */
	public function __construct() {
		// filters
		add_filter( 'body_class', array( $this, 'add_body_class' ) );
		add_filter( 'the_content', array( $this, 'start_truncate' ), 90 );
		add_filter( 'the_title', array( $this, 'get_the_title' ), 10, 2 );
		add_filter( 'wp_trim_words', array( $this, 'fix_the_title' ), 90, 4 );
	}

	/**
	 * @return ContentFilter
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Add new class to body tag
	 *
	 * @param $value
	 *
	 * @return array
	 */
	public function add_body_class( $value ) {
		if ( $this->check_status() ) {
			global $post;
			$post_id = $post->ID;

			$value[] = 'penci-truncate';

			if ( get_theme_mod( 'pencipw_hide_comment', false ) ) {
				$value[] = 'penci-no-comment';

				add_filter( 'penci_single_show_comment', '__return_false' );
			}
			if ( '' != get_post_meta( 'penci_paywall_preview_video', false, $post_id ) ) {
				if ( get_post_format() === 'video' ) {
					$value[] = 'penci_paywall_preview_video';
				}
			}
		}

		return $value;
	}

	/**
	 * Check user status
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function check_status( $post_id = null ) {

		if ( ( ( is_single() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) && 'post' === get_post_type() ) || null !== $post_id ) {
			if ( null === $post_id ) {
				global $post;
				$post_id = $post->ID;
			}

			$subscribe_status = is_user_logged_in() ? get_user_option( 'pencipw_subscribe_status', get_current_user_id() ) : false;
			$user_post_lists  = get_user_option( 'pencipw_unlocked_post_list', get_current_user_id() ) ? get_user_option( 'pencipw_unlocked_post_list', get_current_user_id() ) : array();

			if ( in_array( (int) $post_id, $user_post_lists, true ) ) {
				$unlocked = true;
				if ( get_theme_mod( 'pencipw_unlock_ads' ) ) {
					add_filter( 'penci_show_ads', '__return_false' );
				}
			} else {
				$unlocked = false;
			}

			$this->total = get_theme_mod( 'pencipw_limit', 2 );
			if ( get_post_meta( $post_id, 'pencipw_paragraph_limit', true ) ) {
				$this->total = get_post_meta( $post_id, 'pencipw_paragraph_limit', true );
			}

			if ( get_theme_mod( 'pencipw_block_all', false ) ) {
				if ( 'enable' == get_post_meta( $post_id, 'pencpw_enable_free_post', true ) ) {
					$do_truncate = false;
				} else {
					$do_truncate = true;
				}
			} elseif ( 'enable' == get_post_meta( $post_id, 'pencipw_enable_premium_post', true ) ) {
				$do_truncate = true;
			} else {
				$do_truncate = false;
			}

			if ( $this->is_guest_mode() ) {
				return true;
			} else if ( $this->exclude_unaffected_user( $post_id ) && $do_truncate && ! $subscribe_status ) {

				if ( $unlocked ) {
					return false;
				} else {
					return true;
				}
			}
		}
	}

	public function is_guest_mode() {
		$post_id    = get_the_ID();
		$guest_mode = get_theme_mod( 'pencipw_guest_mode' );

		// If the function exists, get the post's primary category mode or use the default guest mode
		if ( function_exists( 'penci_get_post_pri_cat' ) ) {
			$post_cat_id = penci_get_post_pri_cat( $post_id );
			$guest_term_mode  = get_term_meta( $post_cat_id , 'penci_guest_mode', true );
			if ( $guest_term_mode ) {
				$guest_mode = ( $guest_term_mode === 'enable' );
			}
		}

		// Get the guest mode setting from post meta
		$guest_meta_mode = get_post_meta( $post_id, 'pencipw_enable_guest_mode_post', true );

		// If post meta is set, override the guest mode based on its value
		if ( $guest_meta_mode ) {
			$guest_mode = ( $guest_meta_mode === 'enable' );
		}

		return ! is_user_logged_in() && is_single() && $guest_mode;
	}

	/**
	 * Check user roles that are not affected by subscription
	 *
	 * @return bool
	 */
	private function exclude_unaffected_user( $post_id ) {
		$roles = apply_filters( 'penci_paywall_unaffected_role_list', array( 'administrator' ) );
		$user  = wp_get_current_user();
		$post  = get_post( $post_id );

		if ( (int) $user->ID === (int) $post->post_author ) {
			return false;
		}

		foreach ( $roles as $role ) {
			if ( in_array( $role, $user->roles ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Start Truncate
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function start_truncate( $content ) {
		global $post;
		if ( ! $post ) {
			return;
		}
		$post_id            = $post->ID;
		$this->content_data = strip_shortcodes( $content );

		if ( $this->check_status() ) {

			$this->tag       = new Content_Tag( $this->content_data );
			$total_paragraph = $this->tag->total( 'p' );

			$video_preview = get_post_meta( $post_id, 'pencipw_video_preview_url', true );
			$video_preview = is_array( $video_preview ) ? $video_preview[0] : $video_preview;
			$preview_text  = get_post_meta( $post_id, 'pencipw_preview_textbox', true );

			if ( $preview_text ) {
				$this->content_data = wpautop( $preview_text );

				if ( '' != $video_preview ) {
					$this->content_data .= wp_oembed_get( $video_preview );
				}

				$this->content_data .= $this->get_button();

				return $this->content_data;
			} elseif ( $total_paragraph >= $this->total ) {
				$position     = $this->tag->find_end( 'p', $this->total );
				$this->result = $this->get_truncated_content( 0, $position );
				$this->result .= $this->add_end_tag();

				if ( '' != $video_preview ) {
					$this->result .= wp_oembed_get( $video_preview );
				}

				$this->result .= $this->get_button();

				return $this->result;
			} else {

				if ( '' != $video_preview ) {
					$this->content_data .= wp_oembed_get( $video_preview );
				}

				if ( $this->show_button ) {
					$this->content_data .= $this->get_button();
				}

				return $this->content_data;
			}
		} else {
			return $this->content_data;
		}
	}

	/**
	 * Get button
	 *
	 * @return string
	 */
	private function get_button() {
		$subscribe_url    = get_theme_mod( 'pencipw_subscribe_url', 'none' ) === 'none' ? '#' : get_permalink( get_theme_mod( 'pencipw_subscribe_url', 'none' ) );
		$unlock_url       = get_theme_mod( 'pencipw_unlock_url', 'none' ) === 'none' ? '#' : get_permalink( get_theme_mod( 'pencipw_unlock_url', 'none' ) );
		$show_header_text = get_theme_mod( 'pencipw_show_header_text', true );
		$article_button   = get_theme_mod( 'pencipw_show_button', 'both_btn' );
		$unlock_remaining = get_user_option( 'pencipw_unlock_remaining', get_current_user_id() ) ? get_user_option( 'pencipw_unlock_remaining', get_current_user_id() ) : 0;
		$classes          = '';

		$subscribe_title       = esc_html( get_theme_mod( 'pencipw_subscribe_title', 'Subscribe' ) );
		$subscribe_description = wp_kses( get_theme_mod( 'pencipw_subscribe_description', 'Gain access to all our Premium contents. <br/><strong>More than 100+ articles.</strong>' ), wp_kses_allowed_html() );
		$subscribe_button_text = esc_html( get_theme_mod( 'pencipw_subscribe_button_text', 'Subscribe Now' ) );

		$unlock_title       = esc_html( get_theme_mod( 'pencipw_unlock_title', 'Buy Article' ) );
		$unlock_description = wp_kses( get_theme_mod( 'pencipw_unlock_description', 'Unlock this article and gain permanent access to read it.' ), wp_kses_allowed_html() );
		$unlock_button_text = esc_html( get_theme_mod( 'pencipw_unlock_button_text', 'Unlock Now' ) );

		$header_title       = esc_html( get_theme_mod( 'pencipw_header_title', 'Support authors and subscribe to content' ) );
		$header_description = esc_html( get_theme_mod( 'pencipw_header_description', 'This is premium stuff. Subscribe to read the entire article.' ) );

		if ( $this->is_guest_mode() ) {
			$show_header_text   = true;
			$header_title       = esc_html( get_theme_mod( 'pencipw_guest_mode_header_title', 'Login to view the full content' ) );
			$header_description = esc_html( get_theme_mod( 'pencipw_guest_mode_header_description', 'You need to log in to view the full post content.' ) );
		}

		if ( $unlock_remaining > 0 ) {
			$classes    = 'pencipw_paywall_unlock_post';
			$unlock_url = get_permalink( get_the_ID() );
		}

		/* Buttons */

		$subscribe_attr = array(
			'type'        => 'subscribe',
			'title'       => $subscribe_title,
			'description' => $subscribe_description,
			'url'         => $subscribe_url,
			'button_text' => $subscribe_button_text,
			'column'      => 2,
		);
		$unlock_attr    = array(
			'type'        => 'unlock',
			'title'       => $unlock_title,
			'description' => $unlock_description,
			'url'         => $unlock_url,
			'url_classes' => $classes,
			'button_text' => $unlock_button_text,
			'column'      => 2,
		);

		if ( ! is_user_logged_in() ) {
			$url = $this->is_amp() ? get_post_permalink( get_the_ID() ) : '#penci-login-popup';
			if ( $this->is_guest_mode() ) {
				$login_text = get_theme_mod( 'pencipw_guest_mode_btn_txt', 'Login' );
				$login      = '<div class="penci_login penci-login-popup-btn guest-mode-btn"><span>' . sprintf( wp_kses( __( '<a href="%s"> ' . $login_text . ' </a>', 'penci-paywall' ), wp_kses_allowed_html() ), $url ) . '</span></div>';
			} else {
				$login = '<div class="penci_login penci-login-popup-btn"><span>' . sprintf( wp_kses( __( '<a href="%s">Login</a> if you have purchased', 'penci-paywall' ), wp_kses_allowed_html() ), $url ) . '</span></div>';

			}
		} else {
			$login = '';
		}

		if ( $show_header_text ) {
			$header_text = '<div class="penci-truncate-header">';
			$header_text .= '<h2>' . $header_title . '</h2>';
			$header_text .= '<p>' . $header_description . '</p>';
			$header_text .= $login;
			$header_text .= '</div>';
		} else {
			$header_text = '';
		}

		$button_wrapper = '';

		if ( ! $this->is_guest_mode() ) {

			if ( 'unl_btn' === $article_button ) {
				$subscribe_button      = '';
				$unlock_attr['column'] = 1;
				$unlock_button         = $this->create_button( $unlock_attr );
			} elseif ( 'sub_btn' === $article_button ) {
				$unlock_button            = '';
				$subscribe_attr['column'] = 1;
				$subscribe_button         = $this->create_button( $subscribe_attr );
			} else {
				$unlock_button    = $this->create_button( $unlock_attr );
				$subscribe_button = $this->create_button( $subscribe_attr );
			}

			$button_wrapper = '<div class="penci_btn_wrapper">' . $subscribe_button . $unlock_button . '</div>';
		}


		$buttons = '<div class="penci-truncate-btn">';
		$buttons .= $header_text . $button_wrapper;
		$buttons .= '</div>';

		return $buttons;
	}

	/**
	 * Detect is AMP page
	 *
	 * @return bool
	 */
	private function is_amp() {
		$is_amp = false;
		if ( function_exists( 'is_amp_endpoint' ) ) {
			$is_amp = is_amp_endpoint();
		}

		return $is_amp;
	}

	/**
	 * Create truncate button
	 *
	 * @param array $attr Button Attribute.
	 *
	 * @return string
	 */
	private function create_button( $attr ) {
		$url = 'unlock' === $attr['type'] ? '<a href="' . $attr['url'] . '" class="btn ' . $attr['url_classes'] . '" data-id="' . get_the_ID() . '">' . $attr['button_text'] . '</a>' : '<a href="' . $attr['url'] . '" class="btn">' . $attr['button_text'] . '</a>';

		return '<div class="penci_' . $attr['type'] . '">
					<div class="penci_btn_inner_wrapper">
						<h3>' . $attr['title'] . '</h3>
						<span>' . $attr['description'] . '</span>
						<div class="btn_wrapper">
							' . $url . '
						</div>
					</div>
				</div>';
	}

	/**
	 * Get Content between range
	 *
	 * @param $begin
	 * @param $end
	 *
	 * @return bool|string
	 */
	private function get_truncated_content( $begin, $end ) {
		return substr( $this->content_data, $begin, $end );
	}

	/**
	 * Add end tag
	 *
	 * @return bool|string
	 */
	private function add_end_tag() {
		$end_tag = '';

		foreach ( array_reverse( $this->tag->get_end_tag() ) as $tag ) {
			$end_tag .= '</' . $tag . '>';
		}

		return $end_tag;
	}

	/**
	 * @param $boolean
	 */
	public function show_button( $boolean ) {
		$this->show_button = $boolean;
	}

	/**
	 * Get Value of Truncated Content
	 *
	 * @return mixed
	 */
	public function get_result() {
		return $this->result;
	}

	public function get_the_title( $title, $id ) {
		if ( $this->check_global_status( $id ) && ! is_admin() ) {
			$style = get_theme_mod( 'pencipw_premium_heading_style', 'text' );
			$title = '<span class="pc-premium-post ' . $style . '"></span>' . $title;
		}

		return $title;
	}

	public static function check_global_status( $post_id = null ) {
		if ( get_theme_mod( 'pencipw_block_all', false ) ) {

			if ( 'enable' == get_post_meta( $post_id, 'pencpw_enable_free_post', true ) ) {
				$show_markup = false;
			} else {

				$show_markup = true;
			}
		} elseif ( 'enable' == get_post_meta( $post_id, 'pencipw_enable_premium_post', true ) ) {

			$show_markup = true;
		} else {
			$show_markup = false;
		}

		return $show_markup;
	}

	public function fix_the_title( $text, $num_words, $more, $original_text ) {
		$post_title    = wp_strip_all_tags( get_the_title() );
		$original_text = wp_strip_all_tags( $original_text );
		if ( $post_title == $original_text && $this->check_global_status( get_the_ID() ) && ! is_admin() ) {
			$style = get_theme_mod( 'pencipw_premium_heading_style', 'text' );
			$text  = '<span class="pc-premium-post ' . $style . '"></span>' . $text;
		}

		return $text;
	}
}
