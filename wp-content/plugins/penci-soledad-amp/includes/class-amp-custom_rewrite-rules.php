<?php
class Penci_AMP_Custom_Rewrite_Rules {

	public $endpoints = array();

	public $startpoints = array();

	public function __construct() {
		
		add_filter( 'post_rewrite_rules', array( $this, 'post_rewrite_rules' ), 9999 );
		add_filter( 'date_rewrite_rules', array( $this, 'date_rewrite_rules' ), 9999 );
		add_filter( 'comments_rewrite_rules', array( $this, 'comments_rewrite_rules' ), 9999 );
		add_filter( 'searchs_rewrite_rules', array( $this, 'searchs_rewrite_rules' ), 9999 );
		add_filter( 'author_rewrite_rules', array( $this, 'author_rewrite_rules' ), 9999 );
		add_filter( 'page_rewrite_rules', array( $this, 'page_rewrite_rules' ), 9999 );
		add_filter( 'category_rewrite_rules', array( $this, 'category_rewrite_rules' ), 9999 );
		add_filter( 'post_tags_rewrite_rules', array( $this, 'post_tags_rewrite_rules' ), 9999 );
		add_filter( 'post_formats_rewrite_rules', array( $this, 'post_formats_rewrite_rules' ), 9999 );
		add_action( 'root_rewrite_rules', array( $this, 'root_rewrite_rules' ),9999 );

		add_action( 'product_rewrite_rules', array( $this, 'root_rewrite_rules' ),9999 );
		add_action( 'product_cat_rewrite_rules', array( $this, 'root_rewrite_rules' ),9999 );
		add_action( 'product_tag_rewrite_rules', array( $this, 'root_rewrite_rules' ),9999 );

		add_action( 'post_tag_rewrite_rules', array( $this, 'root_rewrite_rules' ),9999 );
		add_action( 'post_format_rewrite_rules', array( $this, 'root_rewrite_rules' ),9999 );

		add_action( 'root_rewrite_rules', array( $this, 'extra_hooks' ) );
	}

	public function extra_hooks( $rules ) {

		global $wp_rewrite;


		$permastruct = $wp_rewrite->extra_permastructs;

		foreach ( $permastruct as $ptype => $struct ) {

			if ( empty( $struct['walk_dirs'] ) ) {
				continue;
			}

			if ( ! has_filter( "{$ptype}_rewrite_rules", array( $this, 'root_rewrite_rules' ) ) ) {

				add_filter( "{$ptype}_rewrite_rules", array( $this, 'root_rewrite_rules' ), 9999 );
			}
		}

		return $rules;
	}
	
	/**
	 * Filters rewrite rules used for "post" archives.
	 */
	public function post_rewrite_rules( $_rewrite ) {

		$ep_mask = EP_PERMALINK;
		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}

	/**
	 * Filters rewrite rules used for date archives.
	 */
	public function date_rewrite_rules( $_rewrite ) {
		$ep_mask = EP_DATE;
		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}
	
	/**
	 * Filters rewrite rules used for comment feed archives.
	 */
	public function comments_rewrite_rules( $_rewrite ) {
		$ep_mask = EP_COMMENTS;

		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}

	/**
	 * Filters rewrite rules used for search archives.
	 */
	public function searchs_rewrite_rules( $_rewrite ) {
		$ep_mask = EP_SEARCH;

		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}

	/**
	 * Filters rewrite rules used for author archives.
	 */
	public function author_rewrite_rules( $_rewrite ) {
		$ep_mask = EP_AUTHORS;

		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}

	/**
	 * Filters rewrite rules used for "page" post type archives.
	 */
	public function page_rewrite_rules( $_rewrite ) {
		$ep_mask = EP_PAGES;

		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}

	public function category_rewrite_rules( $_rewrite ) {
		$ep_mask = EP_CATEGORIES;

		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}

	public function post_tags_rewrite_rules( $_rewrite ) {
		$ep_mask = EP_DATE;

		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}
	public function post_formats_rewrite_rules( $_rewrite ) {
		$ep_mask = EP_TAGS;

		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}

	public function root_rewrite_rules( $_rewrite ) {
		$ep_mask = EP_ROOT;;

		return $this->generate_rewrite_rules( $_rewrite, $ep_mask );
	}



	public function generate_rewrite_rules( $_rewrite, $ep_mask ) {
		global $wp_rewrite;

		$startpoints = (array) $this->start_point();
		$endpoints  = (array) $this->end_point();

		$rewrite = array();

		$permalink_structure = get_option( 'permalink_structure' );
		$prefix_link          = substr( $permalink_structure, 0, strpos( $permalink_structure, '%' ) );
		$prefix_link          = preg_quote( ltrim( $prefix_link, '/' ), '#' );


		foreach ( (array) $_rewrite as $regex => $ep ) {

			$vars = array();

			wp_parse_str( $ep, $vars );

			if ( ! isset( $vars['feed'] ) ) {

				foreach ( $startpoints as $match => $v ) {

					$v_0 = isset( $v[0] ) ? $v[0] : '';
					$v_1 = isset( $v[1] ) ? $v[1] : '';
					$v_2 = isset( $v[2] ) ? $v[2] : '';

					// Add the startpoints on if the mask fits.
					if ( $v_0 & $ep_mask ) {

						if ( empty( $v_2 ) ) {
							$pattern = preg_quote( $wp_rewrite->preg_index( 'PLACEHOLDER' ) );
							$pattern = '/' . str_replace( 'PLACEHOLDER', '(\\d+)', $pattern ) . '/';
							$ep      = preg_replace_callback( $pattern, array( $this, 'amp_preg_index' ), $ep );

							$rewrite_startpint = $ep . $v_1 . $wp_rewrite->preg_index( 1 );
						} else {
							$rewrite_startpint = $ep . $v_1 . '1';
						}

						if ( $prefix_link && preg_match( "#^($prefix_link)(.+)$#", $regex, $match ) ) {
							$rewrite[ $match[1] . $match . ltrim( $match[2], '/' ) ] = $rewrite_startpint;
						} else {
							$rewrite[ $match . ltrim( $regex, '/' ) ] = $rewrite_startpint;
						}
					}
				}


				foreach ( $endpoints as $v ) {
					if ( preg_match( '/' . preg_quote( $v[1] ) . '/', $ep ) ) {
						continue;
					}
					$v_0 = isset( $v[0] ) ? $v[0] : '';

					if ( $v_0 & $ep_mask ) {

						if ( $rule = $this->generate_end_point_rule( $regex, $ep, $v ) ) {
							$rewrite[ $rule[0] ] = $rule[1];
						}
					}
				}
			}

			$rewrite[ $regex ] = $ep;
		}


		return $rewrite;
	}

	public function generate_end_point_rule( $regex, $query, $ep ) {

		$query = $query . '&' . $ep[2] . '=1';

		if ( substr( $regex, - 3 ) === '/?$' ) {

			$match = substr( $regex, 0, - 3 );

		} else {

			$match = $regex;
		}

		if ( strstr( $regex, '([^/]+)(?:/([0-9]+))?' ) ) {


			list( $before, $after ) = explode( '([^/]+)(?:/([0-9]+))?', $regex );

			$match = $before . '([^/]+)/' . $ep[1] . '(?:/([0-9]+))?' . $after;

		} elseif ( strstr( $regex, 'page/?([0-9]{1,})' ) ) {

			list( $before, $after ) = explode( 'page/?([0-9]{1,})', $regex );

			$match = $before . $ep[1] .'/page/?([0-9]{1,})' . $after;

		} else {

			$match = rtrim( $match, '/' ) . '/' . $ep[1] . '/?$';
		}


		return array( $match, $query );
	}


	public function post_type_rewrite_rules() {
		global $wp_rewrite;

		$ep_mask    = EP_ROOT;
		$startpoints  = (array) $this->start_point();
		$post_types = get_post_types();

		if( ! $post_types ){
			return;
		}

		foreach ( (array) $post_types as $post_type ) {
			if ( isset( $wp_rewrite->extra_rules_top[ $post_type . '/?$' ] ) ) {
				$regex = $post_type . '/?$';
				$ep    = $wp_rewrite->extra_rules_top[ $post_type . '/?$' ];
			} elseif ( isset( $wp_rewrite->extra_rules_top[ '/' . $post_type . '/?$' ] ) ) {
				$regex = '/' . $post_type . '/?$';
				$ep    = $wp_rewrite->extra_rules_top[ '/' . $post_type . '/?$' ];

			} else {

				continue;
			}

			foreach ( $startpoints as $match => $v ) {

				$v_0 = isset( $v[0] ) ? $v[0] : '';
				$v_1 = isset( $v[1] ) ? $v[1] : '';
				$v_2 = isset( $v[2] ) ? $v[2] : '';

				// Add the $startpoints on if the mask fits.
				if ( $v_0 & $ep_mask ) {

					if ( empty( $v_2 ) ) {
						$pattern = preg_quote( $wp_rewrite->preg_index( 'PLACEHOLDER' ) );
						$pattern = '/' . str_replace( 'PLACEHOLDER', '(\\d+)', $pattern ) . '/';
						$ep      = preg_replace_callback( $pattern, array( $this, 'amp_preg_index' ), $ep );

						$rewrite_startpint = $ep . $v_1 . $wp_rewrite->preg_index( 1 );
					} else {
						$rewrite_startpint = $ep . $v_1 . '1';
					}

					$wp_rewrite->extra_rules_top[ $match . ltrim( $regex, '/' ) ] = $rewrite_startpint;
				}
			}
		}
	}
	/**
	 * Adds an endpoint, like /trackback/.
	 * Create a new function based on add_startpoint() core - source: https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/class-wp-rewrite.php#L0
	 *
	 * @param string      $name      Name of the endpoint.
	 * @param int         $places    Endpoint mask describing the places the endpoint should be added.
	 * @param string|bool $query_var Optional. Name of the corresponding query variable. Pass `false` to
	 *                               skip registering a query_var for this endpoint. Defaults to the
	 *                               value of `$name`.
	 */
	public function add_startpoint( $name, $places, $query_var = true ) {
		global $wp;

		// For backward compatibility, if null has explicitly been passed as `$query_var`, assume `true`.
		if ( true === $query_var || null === func_get_arg( 2 ) ) {
			$query_var = $name;
		}
		$this->startpoints[] = array( $places, $name, $query_var, true );

		if ( $query_var ) {
			$wp->add_query_var( $query_var );
		}
	}
	/**
	 * Adds an endpoint, like /trackback/.
	 * Create a new function based on add_endpoint() core - source: https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/class-wp-rewrite.php#L0
	 *
	 * @param string      $name      Name of the endpoint.
	 * @param int         $places    Endpoint mask describing the places the endpoint should be added.
	 * @param string|bool $query_var Optional. Name of the corresponding query variable. Pass `false` to
	 *                               skip registering a query_var for this endpoint. Defaults to the
	 *                               value of `$name`.
	 */
	public function add_endpoint( $name, $places, $query_var = true ) {
		global $wp;

		// For backward compatibility, if null has explicitly been passed as `$query_var`, assume `true`.
		if ( true === $query_var || null === func_get_arg( 2 ) ) {
			$query_var = $name;
		}
		$this->endpoints[] = array( $places, $name, $query_var, true );

		if ( $query_var ) {
			$wp->add_query_var( $query_var );
		}
	}

	public function start_endpoint() {

		$ep_query_append = array ();

		$endpoints = $this->endpoints;

		// Create a new function based on generate_rewrite_rules() - source: https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/class-wp-rewrite.php#L0
		// Build up an array of endpoint regexes to append => queries to append

		if ( $endpoints ) {
			$ep_query_append = array ();
			foreach ( (array) $this->endpoints as $endpoint) {

				// Match everything after the endpoint name, but allow for nothing to appear there.
				$epmatch = $endpoint[1] . ( empty( $endpoint[3] ) ? '/([^/]+)?/?' : '/' );

				// This will be appended on to the rest of the query for each dir.
				$epquery = '&' . $endpoint[2] . '=';
				$ep_query_append[$epmatch] = array ( $endpoint[0], $epquery, $endpoint[3] );
			}
		}

		return $ep_query_append;
	}

	public function start_point() {

		$ep_query_append = array ();

		$startpoints = $this->startpoints;

		// Create a new function based on generate_rewrite_rules() - source: https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/class-wp-rewrite.php#L0
		// Build up an array of endpoint regexes to append => queries to append

		if ( $startpoints ) {
			$ep_query_append = array ();
			foreach ( (array) $this->startpoints as $startpoint ) {

				// Match everything after the endpoint name, but allow for nothing to appear there.
				$epmatch = $startpoint[1] . ( empty( $startpoint[3] ) ? '/([^/]+)?/?' : '/' );

				// This will be appended on to the rest of the query for each dir.
				$epquery = '&' . $startpoint[2] . '=';
				$ep_query_append[$epmatch] = array ( $startpoint[0], $epquery, $startpoint[3] );
			}
		}

		return $ep_query_append;
	}

	public function end_point() {
		return $this->endpoints;
	}

	/**
	 * Indexes for matches for usage in preg_*() functions.
	 *
	 *
	 * @param int $number Index number.
	 * @return string
	 */
	public function amp_preg_index( $number ) {
		global $wp_rewrite;

		$number_pre = isset( $number[1] ) ? intval( $number[1] ) : '';
	
		return $wp_rewrite->preg_index( $number_pre + 1 );
	}
}
