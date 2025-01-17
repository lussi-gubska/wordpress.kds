<?php

class PenciPWT_Cache_Functions {

	// Little cache help
	public static $incrementor_value;

	/**
	 * Clears cache of all settings-dependent objects.
	 * Flushing general settings will result in flushing ALL users cache as well.
	 * Flushing settings cache includes flushing countint types cache.
	 *
	 * @access  public
	 *
	 * @param   $userid string userid whose settings cache needs to be flushed
	 *
	 * @since   2.720
	 */
	static function clear_settings( $userid = 'general' ) {
		wp_cache_delete( 'ppc_settings_' . $userid );
		wp_cache_delete( 'ppc_user_active_counting_types_list_post_' . $userid );
		wp_cache_delete( 'ppc_user_active_counting_types_list_author_' . $userid );
		wp_cache_delete( 'ppc_user_active_counting_types_details_post_' . $userid );
		wp_cache_delete( 'ppc_user_active_counting_types_details_author_' . $userid );

		if ( ! is_numeric( $userid ) ) {
			global $wpdb;

			$wp_all_users = get_users( array( 'fields' => array( 'ID' ) ) );

			foreach ( $wp_all_users as $user ) {
				self::clear_settings( $user->ID );
			}
		}
	}

	/**
	 * Retrieves post stats, if caching is enabled.
	 *
	 * @param    $post_id int
	 *
	 * @return    mixed cache content or false
	 * @since    2.720
	 */
	static function get_post_stats( $post_id ) {
		$cache_salt = PenciPWT_Cache_Functions::get_stats_incrementor();

		return wp_cache_get( 'ppc_stats_post_ID-' . $post_id . '-' . $cache_salt, 'ppc_stats' );
	}

	/**
	 * Stores post stats, if caching is enabled.
	 *
	 * @param    $post_id int
	 *
	 * @return    mixed cache content or false
	 * @since    2.720
	 */
	static function set_post_stats( $post_id, $data ) {
		$cache_salt = PenciPWT_Cache_Functions::get_stats_incrementor();
		wp_cache_set( 'ppc_stats_post_ID-' . $post_id . '-' . $cache_salt, $data, 'ppc_stats', 86400 );
	}

	/**
	 * Clear stats cache for given post.
	 *
	 * @param    $post_id int
	 *
	 * @return    void
	 * @since    2.720
	 */
	static function clear_post_stats( $post_id ) {
		wp_cache_delete( 'ppc_stats_post_ID-' . $post_id . '-' . self::get_stats_incrementor(), 'ppc_stats' );
	}

	/**
	 * Clear all stats cache.
	 *
	 * @return    void
	 * @since    2.720
	 */
	static function clear_stats() {
		self::get_stats_incrementor( true );
	}

	/**
	 * Gets (and updates) incrementor for invalidating stats cache group.
	 *
	 * See https://www.tollmanz.com/invalidation-schemes/ for info on how it works.
	 *
	 * @param    $refresh bool whether to refresh the incrementor
	 *
	 * @return    string incrementor current value
	 * @since    2.720
	 */
	static function get_stats_incrementor( $refresh = false ) {
		if ( self::$incrementor_value and ! $refresh ) {
			return self::$incrementor_value;
		}

		global $pencipwt_global_settings;
		$incrementor_key   = $pencipwt_global_settings['option_stats_cache_incrementor'];
		$incrementor_value = get_option( $incrementor_key );

		if ( $incrementor_value === false or $refresh === true ) {
			$incrementor_value = time();
			update_option( $incrementor_key, $incrementor_value );
		}

		self::$incrementor_value = $incrementor_value;

		return $incrementor_value;
	}

	/**
	 * Get full cache snapshot if available.
	 *
	 * @param    $slug string cache slug (also file name)
	 *
	 * @return    $cached_data array unserialized cache file content (whole of it!)
	 * @since    2.755
	 */
	static function get_full_stats( $slug ) {
		global $pencipwt_global_settings;

		$path = $pencipwt_global_settings['dir_path'] . 'cache/' . $slug;

		if ( is_file( $path ) and filesize( $path ) != 0 ) {
			$open = fopen( $path, "r" );

			$file_content = fread( $open, filesize( $path ) );
			if ( $file_content !== false ) {
				$cached_data                       = unserialize( $file_content );
				PenciPWT_Counting_Stuff::$settings = PenciPWT_General_Functions::get_settings( 'general' ); //put some settings there (hack!), since we never go through data2cash()

				return $cached_data;
			}
		}
	}

	/**
	 * Retrieve cached stats snapshot, if available.
	 *
	 * Cache snapshots can only be generated through WP-CLI command `wp ppc stats --cache-full`.
	 *
	 * @param   $time_start int
	 * @param   $time_end int
	 * @param   $author array|NULL
	 *
	 * @return  stats_array|false
	 * @since   2.770
	 */
	static function get_stats_snapshot( $time_start, $time_end, $author ) {
		// Disable cache snapshot with GET arg `no-cache`, or by hooking to this filter, or when creating snapshots through WP-CLI
		global $CLI_PPC_CACHE;
		if ( isset( $CLI_PPC_CACHE ) or apply_filters( 'ppc_cache_snapshots_default_noload', isset( $_GET['no-cache'] ) ) ) {
			return false;
		}

		global $pencipwt_global_settings, $current_user;
		$perm = new PenciPWT_Permissions();

		// Build snapshot slug, used as cache filename
		$cache_slug = 'ppc_stats-tstart_' . $time_start . '-tend_' . $time_end;
		if ( ! $perm->can_see_countings_special_settings() ) {
			$cache_slug .= '-as-user_' . $current_user->ID;
		}
		if ( is_array( $author ) ) {
			$cache_slug .= '-author_' . $author[0];
		}

		// Load cached snapshot from file
		$path = $pencipwt_global_settings['dir_path'] . 'cache/' . $cache_slug;
		if ( is_file( $path ) and filesize( $path ) != 0 ) {
			$open         = fopen( $path, "r" );
			$file_content = fread( $open, filesize( $path ) );
			if ( $file_content !== false ) {
				$cached_data                       = unserialize( $file_content );
				PenciPWT_Counting_Stuff::$settings = PenciPWT_General_Functions::get_settings( 'general' ); // put some settings there (hack!), since we never go through data2cash()
				set_transient( 'ppc_full_stats_snapshot_time', $cached_data['time'], 5 ); // for stats page header to know data is from cache, hacky

				return $cached_data;
			}
		}

		return false;
	}
}
