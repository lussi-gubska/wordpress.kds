<?php
if ( ! function_exists( 'pencipdc_podcast_add_media_menu' ) ) {
	/**
	 * Add media menu
	 *
	 * @param $id
	 * @param string $type
	 * @param string $more
	 *
	 * @return string
	 */
	function pencipdc_podcast_add_media_menu( $id, $type = 'podcast', $more = 'ellipsis' ) {
		$output     = '';
		$attribute  = '';
		$can_render = false;
		$content    = '';
		$more_icon  = 'plus' === $more ? 'fa fa-plus' : 'fa fa-ellipsis-v';
		switch ( $type ) {
			case 'podcast':
			case 'podcast_subscribe':
				$can_render   = true;
				$is_subscribe = ( 'podcast_subscribe' === $type );

				$main_button_class = $is_subscribe ? 'pencipdc_media_button subscribe' : 'pencipdc_media_button play';
				$main_button_url   = $is_subscribe ? pencipdc_podcast_feed_link( $id, 'podcast-series' ) : '#';
				$main_button_icon  = $is_subscribe ? '' : '<span class="initial"><i class="fa fa-play" aria-hidden="true"></i></span><span class="loader"></span>';
				$main_button_text  = $is_subscribe ? pencipdc_translate( 'subscribe' ) : pencipdc_translate( 'play' );

				$attribute        = "class=\"pencipdc_media_option {$type}\" data-id=\"{$id}\"";
				$wide_button      = '<a href="' . $main_button_url . '" class="' . $main_button_class . '">' . $main_button_icon . '<span class="wide-button-text">' . $main_button_text . '</span></a>';
				$subscribe_button = '';
				if ( ! $is_subscribe ) {
					$subscribe_button = '
					<li>
						<a href="' . pencipdc_podcast_feed_link( $id, 'podcast-series' ) . '"><i class="fa fa-rss"></i><span>' . pencipdc_translate( 'subscribe' ) . '</span></a>
					</li>';
				}

				$content =
					$wide_button .
					'<a href="#" class="pencipdc_media_button more" >' .
					'<i class="' . $more_icon . '"></i>' .
					'</a>' .
					'<ul class="pencipdc_moreoption">
								' . $subscribe_button . '
								<li>
									<a class="add_to_queue" href="#">
										<span class="initial"><i class="fa fa-plus-square-o"></i></span>
										<span class="loader"></span>
										<span>' . pencipdc_translate( 'add_to_queue' ) . '</span>
									</a>
								</li>
					</ul>';
				if ( ! get_theme_mod( 'pencipodcast_podcast_global_player', true ) ) {
					$can_render = false;
				}
				break;
			case 'episode':
			case 'episode_overlay':
			case 'episode_overlay_more':
			case 'episode_block':
			case 'episode play_btn':
			case 'single_episode':
				$can_render = true;
				$play       =
					'<a href="#" class="pencipdc_media_button play">
						<span class="initial"><i class="fa fa-play" aria-hidden="true"></i></span><span class="loader"></span>
					</a>';

				if ( 'single_episode' !== $type ) {
					$more_button =
						'<a href="#" class="pencipdc_media_button more ' . $type . '" >' .
						'<i class="' . $more_icon . '"></i>' .
						'</a>' .
						'<ul class="pencipdc_moreoption">
									<li>
										<a class="add_to_queue" href="#">
											<span class="initial"><i class="fa fa-plus-square-o"></i></span>
											<span class="loader"></span>
											<span>' . pencipdc_translate( 'add_to_queue' ) . '</span>
										</a>
									</li>
						</ul>';

				} else {
					$more_button = '<a class="pencipdc_media_button add_to_queue" href="#">
										<span class="initial"><i class="fa fa-plus"></i></span>
										<span class="loader"></span>
										<span class="success"><i class="fa fa-check" aria-hidden="true"></i></span>
									</a>';
				}

				if ( 'episode_block' === $type ) {
					$play = '<a href="#" class="pencipdc_media_button play">' .
					        '<span class="initial"><i class="fa fa-play" aria-hidden="true"></i></span>' .
					        '<span class="loader"></span>' .
					        '<span>' . pencipdc_translate( 'play' ) . '</span>' .
					        '</a>';
				}

				if ( 'single_episode' === $type ) {
					$play = '<a href="#" class="pencipdc_media_button play">' .
					        '<span class="initial"><i class="fa fa-play" aria-hidden="true"></i></span>' .
					        '<span class="loader"></span>' .
					        '<span>' . pencipdc_translate( 'play' ) . '</span>' .
					        pencipdc_podcast_get_duration( $id, true, false, 'span' ) .
					        '</a>';
				}
				if ( 'episode play_btn' === $type ) {
					$more_button = '';
				}

				$content   = $play . $more_button;
				$attribute = "class=\"pencipdc_media_option {$type}\" data-id=\"{$id}\"";
				if ( 'episode_overlay' === $type || 'episode_overlay_more' === $type ) {
					$content         = 'episode_overlay' === $type ? $play : $more_button;
					$attribute_class = 'episode_overlay' === $type ? 'overlay' : '';
					$type            = 'episode';
					$attribute       = "class=\"pencipdc_media_option {$type} {$attribute_class}\" data-id=\"{$id}\"";
				}

				if ( ! get_theme_mod( 'pencipodcast_podcast_enable_player', true ) ) {
					$can_render = false;
				}
				break;
		}

		if ( $can_render ) {
			$output .= '<div ' . $attribute . '>
							' . $content . '
						</div>';
		}

		return $output;
	}
}

if ( ! function_exists( 'pencipdc_podcast_attribute' ) ) {
	/**
	 * Get most user podcast series
	 *
	 * @param array|int $podcast_id
	 * @param array $args
	 *
	 * @return array
	 */
	function pencipdc_podcast_attribute( $podcast_id, $args = array() ) {
		$result   = array();
		$defaults = array(
			'fields' => array( 'author' ),
		);
		$_args    = wp_parse_args( $args, $defaults );
		foreach ( $_args['fields'] as $field ) {
			switch ( $field ) {
				case 'author':
					$episodes = pencipdc_podcast_posts( $podcast_id );
					$is_empty = empty( $episodes ) ? true : false;
					$user     = false;
					if ( ! $is_empty ) {
						$authors = array();
						foreach ( $episodes as $episode_obj ) {
							$authors[] = $episode_obj->post_author;
						}
						if ( ! empty( $authors ) ) {
							$values = array_count_values( $authors );
							arsort( $values );
							$most_users = array_slice( array_keys( $values ), 0, 1, true );
							foreach ( $most_users as $index => $users ) {
								$user = $users;
							}
						}
					}
					$result[ $field ] = $user;
					break;
				case 'category':
					$query_hash = 'query_hash_' . md5( serialize( 'category_podcast_' . $podcast_id ) );
					if ( ! $category = pencipdc_podcast_cache( $query_hash ) ) {
						$episodes = pencipdc_podcast_posts( $podcast_id );
						$is_empty = empty( $episodes ) ? true : false;
						$category = false;
						if ( ! $is_empty ) {
							$categories      = array();
							$temp_categories = array();
							foreach ( $episodes as $episode_obj ) {
								$cat = get_the_category( $episode_obj->ID );
								if ( ! empty( $cat ) ) {
									$categories[]                        = $cat[0]->term_id;
									$temp_categories[ $cat[0]->term_id ] = $cat[0];
								}
							}
							if ( ! empty( $categories ) ) {
								$values = array_count_values( $categories );
								arsort( $values );
								$most_cat = array_slice( array_keys( $values ), 0, 1, true );
								foreach ( $most_cat as $index => $categories ) {
									$category = $temp_categories[ $categories ];
								}
							}
						}
						$category = pencipdc_podcast_cache( $query_hash, $category );
					}
					$result[ $field ] = $category;
					break;
				case 'image':
					$result[ $field ] = pencipdc_get_series_image_id( $podcast_id );
					break;
				case 'count_series':
					if ( is_array( $podcast_id ) ) {
						$result[ $field ] = empty( $podcast_id ) ? 0 : count( $podcast_id );
					} elseif ( is_int( $podcast_id ) ) {
						$episode_count    = pencipdc_get_series( array( 'term_taxonomy_id' => $podcast_id ) );
						$episode_count    = is_array( $episode_count ) && ! empty( $episode_count ) ? $episode_count[0]->count : 0;
						$result[ $field ] = $episode_count;
					}

					break;
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'pencipdc_podcast_feed_link' ) ) {
	/**
	 * @param bool $term_id
	 * @param bool $taxonomy
	 *
	 * @return false|string|void
	 */
	function pencipdc_podcast_feed_link( $term_id = false, $taxonomy = false ) {

		$custom_feed_link = get_theme_mod( 'custom_feed_url' );
		if ( $custom_feed_link ) {
			return esc_attr( $custom_feed_link );
		}

		if ( false === $term_id || false === $taxonomy ) {
			if ( is_archive() ) {
				global $wp_query;
				$taxonomy = $wp_query->get_queried_object();

				return get_term_feed_link( $taxonomy->term_id, $taxonomy->taxonomy );
			}

			return '';
		}

		return get_term_feed_link( $term_id, $taxonomy );
	}
}

if ( ! function_exists( 'pencipdc_podcast_cache' ) ) {
	/**
	 * Podcast Cache
	 *
	 * @param $query_hash
	 * @param bool $value
	 *
	 * @return bool|false|mixed
	 */
	function pencipdc_podcast_cache( $query_hash, $value = false ) {
		if ( ! $value ) {
			return wp_cache_get( $query_hash, 'penci-podcast' );
		}
		wp_cache_set( $query_hash, $value, 'penci-podcast' );

		return $value;
	}
}

if ( ! function_exists( 'pencipdc_get_series' ) ) {
	function pencipdc_get_series( $args = '' ) {
		$defaults   = array( 'taxonomy' => 'podcast-series' );
		$args       = wp_parse_args( $args, $defaults );
		$query_hash = 'query_hash_' . md5( serialize( $args ) );
		if ( ! $podcast = pencipdc_podcast_cache( $query_hash ) ) {
			$podcast = pencipdc_podcast_cache(
				$query_hash,
				call_user_func(
					static function () use ( $args ) {
						return get_terms( $args );
					}
				)
			);
		}

		if ( empty( $podcast ) ) {
			return array();
		}

		/**
		 * Filters the array of term objects returned for the 'post_tag' taxonomy.
		 *
		 * @param WP_Term[]|int $tags Array of 'post_tag' term objects, or a count thereof.
		 * @param array $args An array of arguments. @see get_terms()
		 *
		 * @since 7.5.0
		 */
		$podcast = apply_filters( 'pencipdc_get_series', $podcast, $args );

		return $podcast;
	}
}

function pencipdc_get_series_image_id( $id ) {
	$image    = get_option( "pencipdc_series_$id" );
	$image_id = '';
	if ( isset( $image['featured_img'] ) && $image['featured_img'] ) {
		$image_id = $image['featured_img'][0];
	}

	return $image_id;
}

function pencipdc_get_category_image_id( $id ) {
	$image    = get_option( "pencipdc_category_$id" );
	$image_id = '';
	if ( isset( $image['featured_img'] ) && $image['featured_img'] ) {
		$image_id = $image['featured_img'][0];
	}

	return $image_id;
}

if ( ! function_exists( 'pencipdc_podcast_get_duration' ) ) {
	/**
	 * Get podcast Length
	 *
	 * @param $post_id
	 *
	 * @return string
	 */
	function pencipdc_podcast_get_duration( $post_id, $human_readable = false, $icon = false, $wrapper = 'div' ) {
		$output   = '';
		$duration = get_post_meta( $post_id, 'pencipc_media_duration', true );
		if ( $duration ) {
			$output .= "<$wrapper class='pencipdc_episode_length'>";
			if ( $human_readable ) {
				$duration_divided = explode( ':', $duration );
				$count_duration   = count( $duration_divided );
				$h                = '00';
				$m                = '00';
				$s                = '00';
				if ( $count_duration <= 3 && $count_duration > 0 ) {
					switch ( $count_duration ) {
						case 3:
							list( $h, $m, $s ) = $duration_divided;
							break;
						case 2:
							$h = '00';
							list( $m, $s ) = $duration_divided;
							break;
						case 1:
							$h = '00';
							$m = '00';
							list( $s ) = $duration_divided;
							break;
					}
				} else {
					if ( $count_duration > 0 ) {
						$s = end( $duration_divided );
						$m = prev( $duration_divided );
						$h = prev( $duration_divided );
					}
				}
				$h      = is_int( (int) $h ) ? ltrim( $h, '0' ) : '0';
				$m      = is_int( (int) $m ) ? ltrim( $m, '0' ) : '0';
				$s      = is_int( (int) $s ) ? ltrim( $s, '0' ) : '0';
				$time_h = ( $h && $h !== '00' && ! empty( $h ) ? "$h " . pencipdc_translate( 'min' ) . ' ' : '' );
				$time_m = ( $m && $m !== '00' && ! empty( $m ) ? "$m " : '0 ' ) . pencipdc_translate( 'min' );
				$time_s = '';
				if ( (int) $h < 1 && (int) $m < 2 ) {
					if ( (int) $m > 0 ) {
						$time_m .= ' ';
					} else {
						$time_m = '';
					}
					$time_s = ( $s && $s !== '00' && ! empty( $s ) ? "$s " : '0 ' ) . pencipdc_translate( 'sec' );
				}
				$time   = $time_h . $time_m . $time_s;
				$icon   = $icon ? "<i class='fa fa-clock-o' aria-hidden='true'></i> " : '';
				$output .= $icon . $time;
			} else {
				$output .= normalize_duration( $duration );
			}
			$output .= "</$wrapper>";
		}

		return $output;
	}
}
if ( ! function_exists( 'normalize_duration' ) ) {
	/**
	 * @param $duration
	 *
	 * @return false|string
	 */
	function normalize_duration( $duration ) {
		$string = '00:00:00';

		for ( $i = ( strlen( $string ) - 4 ); $i > 0; $i -- ) {
			$comparator = substr( $string, 0, $i );
			if ( 0 === strpos( $duration, $comparator ) ) {
				break;
			}
		}

		return substr( $duration, $i );
	}
}

function pencipdc_translate( $text = '' ) {
	$options = [
		'previous'        => __( 'Previous', 'penci_podcast' ),
		'play'            => __( 'Play', 'penci_podcast' ),
		'pause'           => __( 'Pause', 'penci_podcast' ),
		'next'            => __( 'Next', 'penci_podcast' ),
		'shuffle'         => __( 'Shuffle', 'penci_podcast' ),
		'repeat'          => __( 'Repeat', 'penci_podcast' ),
		'toggle_play'     => __( 'Toggle Player', 'penci_podcast' ),
		'toggle_playlist' => __( 'Toggle PlayList', 'penci_podcast' ),
		'queue'           => __( 'Queue', 'penci_podcast' ),
		'mute'            => __( 'Mute', 'penci_podcast' ),
		'unmute'          => __( 'Unmute', 'penci_podcast' ),
		'volume'          => __( 'Volume', 'penci_podcast' ),
		'subscribe'       => __( 'Subscribe', 'penci_podcast' ),
		'add_to_queue'    => __( 'Add to Queue', 'penci_podcast' ),
		'wrong'           => __( 'There\'s something wrong', 'penci_podcast' ),
		'min'             => __( 'min', 'penci_podcast' ),
		'sec'             => __( 'sec', 'penci_podcast' ),
		'sh'              => __( 'Show/Hide Player', 'penci_podcast' ),
	];

	if ( $text && isset( $options[ $text ] ) ) {
		$text_option = 'pencipdc_translate_' . $text;

		return get_theme_mod( $text_option ) ? get_theme_mod( $text_option ) : $options[ $text ];
	} else {
		return $options;
	}
}