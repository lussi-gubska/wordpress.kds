<?php
if ( ! function_exists( 'add_action' ) ) {
	die( 'This file is not meant to be called directly.' );
}

require_once( 'classes/pencipwt_general_functions_class.php' );
require_once( 'classes/pencipwt_stats_class.php' );
require_once( 'classes/pencipwt_generate_stats_class.php' );
require_once( 'classes/pencipwt_counting_stuff_class.php' );
require_once( 'classes/pencipwt_html_functions_class.php' );
require_once( 'classes/pencipwt_ajax_functions_class.php' );
require_once( 'classes/pencipwt_permissions_class.php' );
require_once( 'classes/pencipwt_counting_types_class.php' );
require_once( 'classes/pencipwt_cache_class.php' );
require_once( 'classes/pencipwt_payment_class.php' );
require_once( 'classes/pencipwt_paypal_class.php' );
require_once( 'classes/pencipwt_payment_history_class.php' );
require_once( 'classes/pencipwt_wp_list_table_authors_class.php' );
require_once( 'classes/pencipwt_wp_list_table_posts_class.php' );

class penci_paywriter_dashboard {
	public static $options_page_settings;

	function __construct() {
		global $pencipwt_global_settings;

		$pencipwt_global_settings['option_name']                    = 'ppc_settings';
		$pencipwt_global_settings['option_errors']                  = 'ppc_errors';
		$pencipwt_global_settings['option_stats_cache_incrementor'] = 'ppc_stats_cache_incrementor';
		$pencipwt_global_settings['folder_path']                    = plugins_url( '/', __FILE__ );
		$pencipwt_global_settings['dir_path']                       = plugin_dir_path( __FILE__ );
		$pencipwt_global_settings['file_errors']                    = $pencipwt_global_settings['dir_path'] . 'errors.log';
		$pencipwt_global_settings['current_page']                   = '';
		$pencipwt_global_settings['stats_menu_link']                = 'admin.php?page=penci-pay-writer-stats';
		$pencipwt_global_settings['cap_access_stats']               = 'manage_options';
		$pencipwt_global_settings['payment_history_field']          = '_ppcp_payment_history';
		$pencipwt_global_settings['meta_payment_bonus']             = '_ppcp_payment_bonus';
		$pencipwt_global_settings['paypal_email']                   = 'ppcp_paypal_email';
		$pencipwt_global_settings['temp']                           = array( 'settings' => array() );
        $pencipwt_global_settings['counting_types_object'] = new PenciPWT_Counting_Types();


		//Add left menu entries for both stats and options pages
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
        add_action( 'admin_head', array( $this, 'admin_head' ) );

		//Add custom times
		add_filter( 'cron_schedules', array( $this, 'cron_add_times' ) );
        add_action( 'ppc_before_stats_html', array( 'PenciPWT_HTML_Functions', 'paypal_messages' ) );

		//On load plugin pages
		add_action( 'load-toplevel_page_penci-pay-writer-stats', array( $this, 'on_load_stats_page' ) );
		add_action( 'load-' . sanitize_title( apply_filters( "ppc_admin_menu_name", "Penci Pay Writer" ) ) . '_page_ppc-options', array(
			$this,
			'on_load_options_page_get_settings'
		), 1 );
		//add_action( 'load-toplevel_page_post_pay_counter_show_network_stats', array( &$this, 'on_load_stats_page' ) );
		add_filter( 'set-screen-option', array( $this, 'handle_stats_pagination_values' ), 10, 3 );

		//Manage AJAX calls
		add_action( 'wp_ajax_ppc_save_counting_settings', array(
			'PenciPWT_Ajax_Functions',
			'save_counting_settings'
		) );
		add_action( 'wp_ajax_ppc_save_permissions', array( 'PenciPWT_Ajax_Functions', 'save_permissions' ) );
		add_action( 'wp_ajax_ppc_save_misc_settings', array( 'PenciPWT_Ajax_Functions', 'save_misc_settings' ) );
		add_action( 'wp_ajax_ppc_personalize_fetch_users_by_roles', array(
			'PenciPWT_Ajax_Functions',
			'personalize_fetch_users_by_roles'
		) );
		add_action( 'wp_ajax_ppc_import_settings', array( 'PenciPWT_Ajax_Functions', 'import_settings' ) );
		add_action( 'wp_ajax_ppc_clear_error_log', array( 'PenciPWT_Ajax_Functions', 'clear_error_log' ) );
		add_action( 'wp_ajax_ppc_dismiss_notification', array( 'PenciPWT_Ajax_Functions', 'dismiss_notification' ) );
		add_action( 'wp_ajax_ppc_stats_get_users_by_role', array(
			'PenciPWT_Ajax_Functions',
			'stats_get_users_by_role'
		) );

		//Paypal IPN listener
		if ( pencipwt_get_setting( 'paypal_ipn' ) ) {
			add_filter( 'query_vars', array( 'PenciPWT_PayPal_Functions', 'ipn_query_vars' ) );
			add_action( 'init', array( 'PenciPWT_PayPal_Functions', 'ipn_add_rewrite_rule' ) );
			add_action( 'wp_loaded', array( 'PenciPWT_PayPal_Functions', 'ipn_add_rewrite_rule' ) );
			add_action( 'parse_request', array( 'PenciPWT_PayPal_Functions', 'ipn_listener' ) );
		}

		//Add currency symbol to payments
		add_filter( 'ppc_format_payment', array( 'PenciPWT_HTML_Functions', 'add_currency_symbol' ) );

		//Add due pay col to formatted stats and its sorting
		add_filter( 'ppc_general_stats_format_stats_after_cols_default', array(
			'PenciPWT_Stats',
			'format_stats_for_output_implement_general_payment_data_cols'
		) );
		add_filter( 'ppc_stats_general_sortable_columns', array(
			'PenciPWT_Stats',
			'implement_general_sortable_columns'
		) );
		add_filter( 'ppc_author_stats_format_stats_after_cols_default', array(
			'PenciPWT_Stats',
			'format_stats_for_output_implement_author_payment_data_cols'
		) );
		add_filter( 'ppc_stats_author_sortable_columns', array(
			'PenciPWT_Stats',
			'implement_author_sortable_columns'
		) );

		//Add due pay data to formatted stats
		add_filter( 'ppc_general_stats_format_stats_after_each_default', array(
			'PenciPWT_Stats',
			'format_stats_for_output_implement_general_payment_data'
		), 10, 3 );
		add_filter( 'ppc_author_stats_format_stats_after_each_default', array(
			'PenciPWT_Stats',
			'format_stats_for_output_implement_author_payment_data'
		), 10, 3 );

		//Add pay history, paid total, due pay to countings details (post & author)
		add_filter( 'ppc_post_counting_payment_data', array( 'PenciPWT_Stats', 'get_post_payment_details' ), 10, 2 );
		add_filter( 'ppc_sort_stats_by_author_foreach_author', array(
			'PenciPWT_Stats',
			'get_author_due_payment'
		), 10, 2 );

		//Add abbr tag in HTML and shortcode
		add_filter( 'ppc_general_stats_html_each_field_value', array(
			'PenciPWT_HTML_Functions',
			'get_html_stats_general_each_field'
		), 10, 3 );
		add_filter( 'ppc_general_stats_shortcode_each_field_value', array(
			'PenciPWT_HTML_Functions',
			'get_html_stats_general_each_field'
		), 10, 3 );
		add_filter( 'ppc_author_stats_html_each_field_value', array(
			'PenciPWT_HTML_Functions',
			'get_html_stats_author_each_field'
		), 10, 3 );
		add_filter( 'ppc_author_stats_shortcode_each_field_value', array(
			'PenciPWT_HTML_Functions',
			'get_html_stats_author_each_field'
		), 10, 3 );

		//Add pay field & payment history cols to html stats
		add_action( 'ppc_general_stats_html_cols_after_default', array(
			'PenciPWT_HTML_Functions',
			'get_html_stats_implement_payment_data_cols'
		), 8 );
		add_action( 'ppc_author_stats_html_cols_after_default', array(
			'PenciPWT_HTML_Functions',
			'get_html_stats_implement_payment_data_cols'
		), 8 );

		//Add pay field & payment history data to html stats
		add_action( 'ppc_general_stats_html_after_each_default', array(
			'PenciPWT_HTML_Functions',
			'get_html_stats_implement_general_payment_data'
		), 10, 3 );
		add_action( 'ppc_author_stats_html_after_each_default', array(
			'PenciPWT_HTML_Functions',
			'get_html_stats_implement_author_payment_data'
		), 10, 3 );

		//Add payment form after stats table
		add_action( 'ppc_html_stats_author_after_stats_form', array(
			'PenciPWT_HTML_Functions',
			'show_payment_form_author_stats'
		) );
		add_action( 'ppc_html_stats_general_after_stats_form', array(
			'PenciPWT_HTML_Functions',
			'show_payment_form_general_stats'
		) );

		//Add currency symbol, due payment total to overall stats
		add_filter( 'ppc_overall_stats', array( 'PenciPWT_Stats', 'get_overall_stats_implement' ), 10, 2 );
		add_filter( 'ppc_html_overall_stats', array( 'PenciPWT_HTML_Functions', 'print_overall_stats_implement' ) );

        add_action( 'load-post.php', array( $this, 'on_load_post_page' ) );

		/**
		 * AJAX CALLS
		 */
		add_action( 'wp_ajax_ppcp_show_post_payment_history', array(
			'PenciPWT_Ajax_Functions',
			'show_post_payment_history'
		) );
		add_action( 'wp_ajax_ppcp_show_author_payment_history', array(
			'PenciPWT_Ajax_Functions',
			'show_author_payment_history'
		) );
		add_action( 'wp_ajax_ppcp_show_transaction', array( 'PenciPWT_Ajax_Functions', 'show_transaction' ) );
		add_action( 'wp_ajax_ppcp_delete_transaction', array( 'PenciPWT_Ajax_Functions', 'delete_transaction' ) );
		add_action( 'wp_ajax_ppcp_author_payment_history_delete', array(
			'PenciPWT_Ajax_Functions',
			'payment_history_author_delete'
		) );
		add_action( 'wp_ajax_ppcp_post_payment_history_delete', array(
			'PenciPWT_Ajax_Functions',
			'payment_history_post_delete'
		) );
		add_action( 'wp_ajax_ppcp_mark_as_paid', array( 'PenciPWT_Ajax_Functions', 'mark_as_paid' ) );
		add_action( 'wp_ajax_ppcp_paypal_payment', array( 'PenciPWT_Ajax_Functions', 'paypal_payment' ) );

		//Clear post stats cache on post update
		add_action( 'post_updated', array( 'PenciPWT_Cache_Functions', 'clear_post_stats' ), 10, 1 );

		add_action( 'load-' . sanitize_title( apply_filters( "ppc_admin_menu_name", "Penci Pay Writer" ) ) . '_page_ppcp-confirm-payment', array(
			$this,
			'on_load_confirm_payment_page_enqueue'
		) );
        
         if( ! get_option( $pencipwt_global_settings['payment_history_field'] ) ) {
             add_option( $pencipwt_global_settings['payment_history_field'], '', '', 'no' );
         }

         add_action('pfs_edit_post',[$this,'post_page_metabox_content']);
	}

	/**
	 * Adds "every two weeks" as schedule time (ppcp activation check).
	 *
	 * @access  public
	 *
	 * @param    $schedules array shedules already
	 *
	 * @return    array schedules
	 * @since   2.511
	 */
	function cron_add_times( $schedules ) {
		$schedules['weekly2'] = array(
			'interval' => 3600 * 24 * 14,
			'display'  => 'Once every two weeks'
		);

		return $schedules;
	}

	/**
	 * Loads plugin's css and js in confirm payment editing page.
	 *
	 * @access  public
	 * @since   1.1
	 */
	function on_load_confirm_payment_page_enqueue() {
		global $pencipwt_global_settings;
		$perm = new PenciPWT_Permissions();

		if ( $perm->can_mark_as_paid() or $perm->can_see_paypal_functions() ) {
			wp_enqueue_style( 'ppc_stats_style', $pencipwt_global_settings['folder_path'] . 'style/ppc_stats_style.css' );
			wp_enqueue_style( 'ppcp_stats_style', $pencipwt_global_settings['folder_path'] . 'style/ppc_stats_style.css' );
			wp_enqueue_script( 'ppcp_functions', $pencipwt_global_settings['folder_path'] . 'js/ppc_functions.js', array( 'jquery' ) );
			wp_enqueue_script( 'ppcp_payment_confirm', $pencipwt_global_settings['folder_path'] . 'js/ppc_payment_confirm.js', array( 'jquery' ), filemtime( $pencipwt_global_settings['dir_path'] . 'js/ppc_payment_confirm.js' ) );
			wp_localize_script( 'ppcp_payment_confirm', 'ppcp_payment_confirm_vars', array(
				'nonce_ppcp_confirm_payment'          => wp_create_nonce( 'ppcp_confirm_payment' ),
				'localized_paypal_payment_successful' => __( 'You are being redirected to PayPal, wait...', 'penci-pay-writer' )
			) );
		}
	}

	/**
	 * Adds first level side menu "Penci Pay Writer"
	 *
	 * @access  public
	 * @since   2.0
	 */
	function admin_menus() {
		global $pencipwt_global_settings;

		add_menu_page( apply_filters( "ppc_admin_menu_name", "Penci Pay Writer" ), apply_filters( "ppc_admin_menu_name", "Penci Pay Writer" ), $pencipwt_global_settings['cap_access_stats'], 'penci-pay-writer-stats', array(
			$this,
			'show_stats'
		), plugin_dir_url( __FILE__ ) . 'style/images/dollar.png' );
		add_submenu_page( 'penci-pay-writer-stats', apply_filters( "ppc_admin_menu_name", "Penci Pay Writer" ) . ' - ' . __( 'Stats', 'penci-pay-writer' ), __( 'Stats', 'penci-pay-writer' ), $pencipwt_global_settings['cap_access_stats'], 'penci-pay-writer-stats', array(
			$this,
			'show_stats'
		) );
        add_submenu_page( 'penci-pay-writer-stats', 'Penci Pay Writer - '.__( 'Confirm payment', 'penci-pay-writer' ), 'Confirm', $pencipwt_global_settings['cap_access_stats'], 'ppcp-confirm-payment', array( $this, 'page_confirm_payment' ) );
        if ( current_user_can( 'administrator', get_current_user_id() ) ){
            add_submenu_page( 'penci-pay-writer-stats', 'Settings', 'Settings', 'manage_options', add_query_arg( ['autofocus[section]' => 'penci_pay_writer_posts_section'], admin_url( 'customize.php' ) ) );
        }
	}

    /**
     * Hides "Confirm payment" page under plugin menu.
     *
     * @access  public
     * @since   1.3
     */
    function admin_head() {
        remove_submenu_page( 'penci-pay-writer-stats', 'ppcp-confirm-payment' );
    }

    /**
	 * Loads stats metabox in all allowed post-types editing pages.
     *
	 * @access  public
     * @since   1.0
     */
	function post_page_metabox() {
		$perm = new PenciPWT_Permissions();
		if( ! $perm->can_see_stats_box_in_post_edit_page() ) {return;}

		foreach( pencipwt_get_setting('counting_allowed_post_types') as $post_type ){
            add_meta_box( 'ppc_stats', 'Penci Pay Writer - '.__( 'Stats', 'penci-pay-writer' ), array( $this, 'post_page_metabox_content' ), $post_type, 'side', 'default' );
		}
	}
    
    /**
     * Displays the metabox "Penci Pay Writer Stats" in the post editing page.
	 *
     * @access  public
     *
     * @param   $post object WP post object
     *
     *@since   1.0
     */
    static function post_page_metabox_content( $post ) {
        global $pencipwt_global_settings;

        $post = is_object($post) ? $post : get_post($post);


        //Initiliaze counting types
		$pencipwt_global_settings['counting_types_object'] = new PenciPWT_Counting_Types();
		$pencipwt_global_settings['counting_types_object']->register_built_in_counting_types();
        $counting_types = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' );

		//Generates stats only for current post id, override default args
		$pencipwt_global_settings['temp']['get_requested_posts_post_ids'] = array( $post->ID ); //Store this in temp global var so that hook function can use it
		add_filter( 'ppc_get_requested_posts_args', array( 'PenciPWT_Stats', 'get_requested_posts_by_id' ), 1, 10 );
		add_filter( 'ppc_get_settings', array( 'PenciPWT_General_Functions', 'get_settings_allow_all_statuses' ), 4, 10 );
		$stats = PenciPWT_Generate_Stats::produce_stats( 0, 0 ); //time range is useless, stats are generated on post id

		if( is_wp_error( $stats ) ) {
			echo $stats->get_error_message();
			return;
		}
		remove_filter( 'ppc_get_requested_posts_args', array( 'PenciPWT_Stats', 'get_requested_posts_by_id' ) );
		remove_filter( 'ppc_get_settings', array( 'PenciPWT_General_Functions', 'get_settings_allow_all_statuses' ) );

		$post = isset($stats['raw_stats'][$post->post_author][$post->ID]) ? $stats['raw_stats'][$post->post_author][$post->ID] : null;

        echo '<div class="pencipwt-frontend-stats">';
        echo '<h4 style="display:none">'.__('Penci Pay Writer','penci-pay-writer').'</h4>';
		?>

            <table class="widefat fixed">
                <thead>
                    <tr>
                        <th width="40%"><?php _e( 'Type', 'penci-pay-writer' ); ?></th>
                        <th><?php _e( 'Cnt.', 'penci-pay-writer' ); ?></th>
                        <th><?php _e( 'Due pay', 'penci-pay-writer' ); ?></th>
                    </tr>
                </thead>
                <tbody>
            
                        <?php
                        if ( isset($post->ppc_payment['due_payment']) && is_array($post->ppc_payment['due_payment'])){
                            foreach( $post->ppc_payment['due_payment'] as $id => $value ) {
                                if( $id == 'total' ) {continue;}

                                if( isset( $counting_types[$id] ) ) {
                                    switch( $counting_types[$id]['display'] ) {
                                        case 'payment':
                                        case 'none':
                                            ?>
                                            <tr>
                                            <td><?php echo $counting_types[$id]['label'] ?></td>
                                            <td>-</td>
                                            <td><?php echo PenciPWT_General_Functions::format_payment( sprintf( '%.2f', $post->ppc_payment['due_payment'][$id] ) ); ?></td>
                                            </tr>
                                            <?php
                                            break;

                                        default:
                                            ?>
                                            <tr>
                                            <td><?php echo $counting_types[$id]['label'] ?></td>
                                            <td><?php echo $post->ppc_count['normal_count'][$id]['to_count']; ?></td>
                                            <td><?php echo PenciPWT_General_Functions::format_payment( sprintf( '%.2f', $post->ppc_payment['due_payment'][$id] ) ); ?></td>
                                            </tr>
                                            <?php
                                            break;
                                    }
                                }
                            }
                        }
                        ?>
            
                </tbody>
            </table>

        <?php do_action( 'ppcp_edit_metabox_stats_bottom', $post );?>
        <div class="clear"></div>
        <?php
        if( pencipwt_get_setting('enable_post_stats_caching') )
			{echo '<div style="margin-top: 10px; font-style: italic;">'.__( 'Displayed data is cached. You may have to wait 24 hours for updated data.', 'post-pay-counter' ).'</div>';}
        echo '</div>';
        //}
    }

     /**
	 * Loads plugin's css and js in post editing page, and the PRO stats metabox.
     *
	 * @access  public
     * @since   1.1
     */
	function on_load_post_page() {

        //Load Metaboxes in post.php
		add_action( 'add_meta_boxes', array( $this, 'post_page_metabox' ) );

	}


	/**
	 * Reponsible of the datepicker's files, plugin's js and css loading in the stats page
	 *
	 * @access  public
	 * @since   2.0
	 */
	function on_load_stats_page() {
		global $pencipwt_global_settings;

		add_thickbox();

		//If an author is given, put that in an array
		if ( isset( $_REQUEST['author'] ) and is_numeric( $_REQUEST['author'] ) and get_userdata( $_REQUEST['author'] ) ) {
			$pencipwt_global_settings['current_page'] = 'stats_detailed';
			$this->author                             = array( $_REQUEST['author'] );
		} else {
			$pencipwt_global_settings['current_page'] = 'stats_general';
			$this->author                             = null;
		}

		//Store and maybe_redirect to ordered stats
		PenciPWT_General_Functions::default_stats_order();

		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );

		//Initiliaze counting types
		$pencipwt_global_settings['counting_types_object'] = new PenciPWT_Counting_Types();
		$pencipwt_global_settings['counting_types_object']->register_built_in_counting_types();

		//Also populates $pencipwt_global_settings['first_available_post_time'] and $pencipwt_global_settings['last_available_post_time']
		PenciPWT_General_Functions::get_default_stats_time_range( $general_settings );

		$time_end_now        = date( 'Y-m-d', strtotime( '23:59:59' ) );
		$time_start_end_week = get_weekstartend( current_time( 'mysql' ) );

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery.ui.theme', $pencipwt_global_settings['folder_path'] . 'style/ui-lightness/jquery-ui-1.8.15.custom.css' );
		wp_enqueue_style( 'ppc_header_style', $pencipwt_global_settings['folder_path'] . 'style/ppc_header_style.css', array( 'wp-admin' ) );
		wp_enqueue_style( 'ppc_stats_style', $pencipwt_global_settings['folder_path'] . 'style/ppc_stats_style.css' );
		wp_enqueue_script( 'ppc_stats_effects', $pencipwt_global_settings['folder_path'] . 'js/ppc_stats_effects.js', array( 'jquery' ) );
		wp_localize_script( 'ppc_stats_effects', 'ppc_stats_effects_vars', array(
			'datepicker_mindate'                => date( 'Y-m-d', $pencipwt_global_settings['first_available_post_time'] ),
			'datepicker_maxdate'                => date( 'Y-m-d', $pencipwt_global_settings['last_available_post_time'] ),
			'time_start_this_month'             => date( 'Y-m-d', strtotime( 'first day of this month' ) ),
			'time_end_this_month'               => $time_end_now,
			'time_start_this_year'              => date( 'Y-m-d', strtotime( 'first day of january this year' ) ),
			'time_end_this_year'                => $time_end_now,
			'time_start_this_week'              => date( 'Y-m-d', $time_start_end_week['start'] ),
			'time_end_this_week'                => $time_end_now,
			'time_start_last_month'             => date( 'Y-m-d', strtotime( 'first day of last month' ) ),
			'time_end_last_month'               => date( 'Y-m-d', strtotime( 'first day of this month' ) - 86400 ),
			//go to first day of current month and back of one day
			'time_start_all_time'               => $pencipwt_global_settings['first_available_post_time'],
			'time_end_all_time'                 => $time_end_now,
			'nonce_ppc_stats_get_users_by_role' => wp_create_nonce( 'ppc_stats_get_users_by_role' )
		) );

		wp_enqueue_style( 'ppcp_stats_style', $pencipwt_global_settings['folder_path'] . 'style/ppc_stats_style.css' );

		$perm = new PenciPWT_Permissions();
		if ( $perm->can_mark_as_paid() or $perm->can_see_paypal_functions() ) {
			wp_enqueue_script( 'ppcp_payment_stuff', $pencipwt_global_settings['folder_path'] . 'js/ppc_payment_stuff.js', array( 'jquery' ) );
			wp_localize_script( 'ppcp_payment_stuff', 'ppcp_payment_stuff_vars', array(
				'nonce_ppcp_paid_update'      => wp_create_nonce( 'ppcp_paid_update' ),
				'confirm_payment_form_action' => 'admin.php?page=ppcp-confirm-payment',
				'localized_no_selection'      => __( 'You must select at least one item.', 'penci-pay-writer' )
			) );
		}

		if ( $perm->can_delete_payment_history() ) {
			wp_enqueue_script( 'ppcp_payment_history', $pencipwt_global_settings['folder_path'] . 'js/ppc_payment_history.js', array( 'jquery' ) );
			wp_localize_script( 'ppcp_payment_history', 'ppcp_payment_history_vars', array(
				'nonce_ppcp_payment_history_delete'        => wp_create_nonce( 'ppcp_payment_history_delete' ),
				'nonce_ppcp_transaction_delete'            => wp_create_nonce( 'ppcp_transaction_delete' ),
				'localized_confirm_payment_history_delete' => __( 'This record will be permanently deleted. Sure?', 'penci-pay-writer' ),
				'localized_confirm_transaction_delete'     => __( 'This record will be permanently deleted. Sure?', 'penci-pay-writer' ) . ' ' . __( 'This includes the transaction details and all posts and authors payment history records.', 'penci-pay-writer' )
			) );
		}

		$this->initialize_stats();
	}

	/**
	 * Selects the correct settings for the Options page.
	 *
	 * Acts depending on the given $_GET['userid']: general, trial or user-personalized.
	 * If a valid user id is asked which does not have any personalized settings, get general ones and set the userid field to the user's one, unsetting all only-general options.
	 *
	 * @access  public
	 * @since   2.0
	 */
	function on_load_options_page_get_settings() {
		//Numeric userid
		if ( isset( $_GET['userid'] ) and is_numeric( $_GET['userid'] ) ) {

			if ( ! get_userdata( (int) $_GET['userid'] ) ) {
				echo '<strong>' . __( 'The requested user does not exist.', 'penci-pay-writer' ) . '</strong>';

				return;
			}

			$settings = PenciPWT_General_Functions::get_settings( (int) $_GET['userid'], true );

			//User who never had personalized settings is being set, get rid of only-general settings
			if ( pencipwt_get_setting( 'userid' ) == 'general' ) {
				$settings['userid'] = (int) $_GET['userid'];
			}

			/**
			 * Filters general settings on new user's custom settings.
			 *
			 * When a user's settings are customized for the first time, general settings are taken and stripped of the only general ones (i.e. non-customizable options, such as the Miscellanea box).
			 * It's crucial that all non-personalizable settings indexes are unset before handling/saving the user's settings.
			 *
			 *  ~ This was changed in 2.516, with only user settings different from general ones are stored. ~
			 *
			 * @param    $settings array PPC general settings
			 *
			 * @since    2.0
			 */

			//$settings = apply_filters( 'ppc_unset_only_general_settings_personalize_user', $settings );

			//General
		} else {
			$settings = PenciPWT_General_Functions::get_settings( 'general' );
		}

		/**
		 * Filters selected options page settings, final.
		 *
		 * They are stored in a class var and used throghout all the functions that need to know **what** settings we are displaying and using in the plugin options page.
		 *
		 * @param    $settings PPC options settings
		 *
		 * @since    2.0
		 */
		$settings = apply_filters( 'ppc_selected_options_settings', $settings );

		self::$options_page_settings = $settings; //store in class var
	}

	/**
	 * Initilizes stats page (defines time range and stuff like that).
	 * Needed to be done before actual HTML loading because the WP_List_Table object needs to be loaded early, or it won't be possible to hide columns.
	 *
	 * @access  public
	 * @since   2.700
	 */
	function initialize_stats() {
		global $current_user, $pencipwt_global_settings, $wp_roles;
		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );
		$perm             = new PenciPWT_Permissions();

		//Validate time range values (start and end), if set. They must be isset, numeric and positive. If something's wrong, start and end time are taken from the default publication time range
		if ( ( isset( $_REQUEST['tstart'] ) and ( ! is_numeric( $_REQUEST['tstart'] ) or $_REQUEST['tstart'] < 0 ) )
		     or ( isset( $_REQUEST['tend'] ) and ( ! is_numeric( $_REQUEST['tend'] ) or $_REQUEST['tend'] < 0 ) ) ) {
			$_REQUEST['tstart'] = strtotime( $_REQUEST['tstart'] . ' 00:00:00' );
			$_REQUEST['tend']   = strtotime( $_REQUEST['tend'] . ' 23:59:59' );
		} else if ( ! isset( $_REQUEST['tstart'] ) or ! isset( $_REQUEST['tend'] ) ) {
			$_REQUEST['tstart'] = $pencipwt_global_settings['stats_tstart'];
			$_REQUEST['tend']   = $pencipwt_global_settings['stats_tend'];
		}
		//else the values are correct and valid

		//If empty role, or any role, or invalid role => get rid of role param
		if ( isset( $_REQUEST['role'] ) and ( $_REQUEST['role'] == 'ppc_any' or $_REQUEST['role'] == '' or ! isset( $wp_roles->role_names[ $_REQUEST['role'] ] ) ) ) {
			unset( $_REQUEST['role'] );
		}

		//Assign to global var
		$pencipwt_global_settings['stats_tstart'] = sanitize_text_field( $_REQUEST['tstart'] );
		$pencipwt_global_settings['stats_tend']   = sanitize_text_field( $_REQUEST['tend'] );

		if ( isset( $_REQUEST['role'] ) ) {
			$pencipwt_global_settings['stats_role'] = sanitize_text_field( $_REQUEST['role'] );
		}

		//If filtered by user role, add filter to stats generation args and complete page permalink
		if ( isset( $pencipwt_global_settings['stats_role'] ) ) {
			add_filter( 'ppc_get_requested_posts_args', function ( $grp_args ) {
				global $pencipwt_global_settings;
				$grp_args['ppc_allowed_user_roles'] = array( $pencipwt_global_settings['stats_role'] );

				return $grp_args;
			} );
		}

		if ( is_array( $this->author ) ) {

			if ( ! $perm->can_see_others_detailed_stats() and $current_user->ID != $this->author[0] ) {
				wp_die( __( 'You do not have sufficient permissions to access this page' ) );
			}

			$this->stats = PenciPWT_Generate_Stats::produce_stats( $pencipwt_global_settings['stats_tstart'], $pencipwt_global_settings['stats_tend'], $this->author );

			if ( ! is_wp_error( $this->stats ) ) {
				$option = 'per_page';
				$args   = array(
					'label'   => 'Posts',
					'default' => 500,
					'option'  => 'ppc_posts_per_page'
				);
				add_screen_option( $option, $args );

				$this->stats_table = new PenciPWT_Posts_List_Table( $this->stats );
			}

		} else {
			$this->stats = PenciPWT_Generate_Stats::produce_stats( $pencipwt_global_settings['stats_tstart'], $pencipwt_global_settings['stats_tend'] );

			if ( ! is_wp_error( $this->stats ) ) {
				$option = 'per_page';
				$args   = array(
					'label'   => 'Authors',
					'default' => 50,
					'option'  => 'ppc_authors_per_page'
				);
				add_screen_option( $option, $args );

				$this->stats_table = new PenciPWT_Authors_List_Table( $this->stats );
			}
		}
	}

	/**
	 * Saves pagination value in Screen Options.
	 *
	 * @access  public
	 * @since   2.700
	 */
	function handle_stats_pagination_values( $status, $option, $value ) {
		return $value;
	}

	/**
	 * Shows the Stats page.
	 *
	 * @access  public
	 * @since   2.0
	 */
	function show_stats() {
		global $current_user, $pencipwt_global_settings, $wp_roles;
		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );

		/**
		 * Fires before any HTML has been output in the stats page.
		 *
		 * @param mixed $author author for which stats are displayed. If given, is the only index of an array, NULL means general stats are being requested.
		 *
		 * @since    2.0
		 */
		do_action( 'ppc_before_stats_html', $this->author );
		?>

        <div class="wrap">
        <h2><?php echo apply_filters( "ppc_admin_menu_name", "Penci Pay Writer" ) . ' - ' . __( 'Stats', 'penci-pay-writer' ); ?></h2>

		<?php
		//AUTHOR STATS
	if ( is_array( $this->author ) ) {
		$userdata = get_userdata( $this->author[0] );

		PenciPWT_HTML_Functions::show_stats_page_header( $userdata->display_name, PenciPWT_General_Functions::get_the_author_link( $this->author[0] ) );

		/**
		 * Fires before the *author* stats page form and table been output.
		 *
		 * @param array $stats a PenciPWT_Generate_Stats::produce_stats() result - current stats.
		 *
		 * @since    2.0
		 */

		do_action( 'ppc_html_stats_author_before_stats_form', $this->stats );

		if ( is_wp_error( $this->stats ) ) {
			echo $this->stats->get_error_message();
			echo '</div>';

			return;
		}

		?>

        <form method="post" id="ppc_stats" accesskey="<?php echo $this->author[0]; //accesskey holds author id ?>">
        <div id="ppc_stats_table"> <!-- PRO mark as paid retrocompatibility -->

		<?php

		if ( isset( $this->stats_table ) and ! is_wp_error( $this->stats_table ) ) {
			$this->stats_table->prepare_items();
			$this->stats_table->display();
		}
		?>

		<?php
		/**
		 * Fires after the *author* stats page form and table been output.
		 *
		 * @since    2.0
		 */
		do_action( 'ppc_html_stats_author_after_stats_form' );

		//GENERAL STATS
		} else {
		$page_permalink = $pencipwt_global_settings['stats_menu_link'] . '&amp;tstart=' . $pencipwt_global_settings['stats_tstart'] . '&amp;tend=' . $pencipwt_global_settings['stats_tend'];

		if ( isset( $_REQUEST['ppc-time-range'] ) ) {
			$page_permalink .= '&amp;ppc-time-range=' . $_REQUEST['ppc-time-range'];
		}
		if ( isset( $_REQUEST['orderby'] ) ) {
			$page_permalink .= '&amp;orderby=' . $_REQUEST['orderby'];
		}
		if ( isset( $_REQUEST['order'] ) ) {
			$page_permalink .= '&amp;order=' . $_REQUEST['order'];
		}

		//If filtered by user role, add filter to stats generation args and complete page permalink
		if ( isset( $_REQUEST['role'] ) ) {
			$page_permalink .= '&amp;role=' . $pencipwt_global_settings['stats_role'];
		}

		//If filtered by category, add filter to stats generation args and complete page permalink
		if ( isset( $_REQUEST['category'] ) ) {
			$page_permalink .= '&amp;category=' . $pencipwt_global_settings['stats_category'];
		}

		PenciPWT_HTML_Functions::show_stats_page_header( __( 'General', 'penci-pay-writer' ), admin_url( $page_permalink ) );

		/**
		 * Fires before the *general* stats page form and table been output.
		 *
		 * @param array $stats a PenciPWT_Generate_Stats::produce_stats() result - current stats.
		 *
		 * @since    2.0
		 */
		do_action( 'ppc_html_stats_general_before_stats_form', $this->stats );

		if ( is_wp_error( $this->stats ) ) {
			echo $this->stats->get_error_message();
			echo '</div>';

			return;
		}
		?>

        <form method="post" id="ppc_stats">
        <div id="ppc_stats_table">

		<?php


		if ( isset( $this->stats_table ) and ! is_wp_error( $this->stats_table ) ) {
			$this->stats_table->prepare_items();
			$this->stats_table->display();
		}

		/**
		 * Fires after the *general* stats page form and table been output.
		 *
		 * @since    2.0
		 */
		do_action( 'ppc_html_stats_general_after_stats_form' );
	}
		?>

        </div>
        </form>
        <div class="ppc_table_divider"></div>

		<?php
		if ( pencipwt_get_setting('display_overall_stats') ) {
			$overall_stats = PenciPWT_Generate_Stats::get_overall_stats( $this->stats['raw_stats'] );
			PenciPWT_HTML_Functions::print_overall_stats( $overall_stats );

			/**
			 * Fires after the overall stats table been output.
			 *
			 * @since    2.0
			 */
			do_action( 'ppc_html_stats_after_overall_stats' );
		}
		?>

        </div>

		<?php
	}

    /**
     * Shows the "Confirm payment" page.
     *
     * @access  public
     * @since   1.3
     */
    function page_confirm_payment() {
        global $pencipwt_global_settings;
        require_once( 'classes/pencipwt_payment_class.php' );

        check_admin_referer( 'ppcp_confirm_payment' );

        $pencipwt_global_settings['current_page'] = 'confirm_payment';
        $perm = new PenciPWT_Permissions();

		if( ! ( $perm->can_mark_as_paid() or $perm->can_see_paypal_functions() ) )
			{return _e( 'Error: you are not allowed to see this page.' , 'penci-pay-writer' );}

		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );

        //Define requested action
        if( isset( $_POST['ppcp_mark_as_paid_post'] ) or isset( $_POST['ppcp_mark_as_paid_author'] ) )
            {$action = 'ppcp_mark_as_paid';}
        else if ( isset( $_POST['ppcp_paypal_payment_post'] ) or isset( $_POST['ppcp_paypal_payment_author'] ) )
            {$action = 'ppcp_paypal_payment';}
        else
            {return _e( 'Error: could not determine payment action.', 'penci-pay-writer' );}

		//Need to set global time range values or other addons may have issues
		$pencipwt_global_settings['stats_tstart'] = $_POST['ppcp_stats_tstart'];
		$pencipwt_global_settings['stats_tend'] = $_POST['ppcp_stats_tend'];

        //Initiliaze counting types
		$pencipwt_global_settings['counting_types_object'] = new PenciPWT_Counting_Types();
		$pencipwt_global_settings['counting_types_object']->register_built_in_counting_types();

		$posts_ids = array();

        //POST PAYMENT
        if( isset( $_POST['ppcp_mark_as_paid_post'] ) or isset( $_POST['ppcp_paypal_payment_post'] ) ) {
    		foreach( $_POST as $key => $value ) {
    			//Skip not-post-ids
                if( strpos( $key, 'ppcp_paid_update_post_' ) !== 0 ) {continue;}

                $posts_ids[] = substr( $key, 22 );
    		}

            $raw_stats = PenciPWT_Stats::get_stats_by_post_ids( $posts_ids );
            if( is_wp_error( $raw_stats ) ) {die( $raw_stats->get_error_message() );}
        }

        //AUTHOR PAYMENT
        if( isset( $_POST['ppcp_mark_as_paid_author'] ) or isset( $_POST['ppcp_paypal_payment_author'] ) ) {
    		$n = 0;
            $authors = array();

            foreach( $_POST as $key => $value ) {
                //Skip not-author-ids
                if( strpos( $key, 'ppcp_paid_update_author_' ) !== 0 ) {continue;}

                //Don't exceed max number of receivers according to PayPal Adaptive Payments
                if( $action == 'ppcp_paypal_payment' ) {

                    if( $n > 6 ) {
                        echo '<div id="message" class="updated fade"><p>'.sprintf ( __( 'You have selected more receivers than your chosen PayPal method can handle. Only the first %d have been selected. You are still in time to go back and edit your selection.', 'penci-pay-writer' ), --$n ).'</p></div>';
                        break;
                    }

                    ++$n;
                }

                $value = unserialize( base64_decode( $value ) );
                $authors[] = $value['author'];
    		}

            $raw_stats = PenciPWT_Generate_Stats::produce_stats( $_POST['ppcp_stats_tstart'], $_POST['ppcp_stats_tend'], $authors, false );
            if( is_wp_error( $raw_stats ) ) {die( $raw_stats->get_error_message() );}

            foreach( $raw_stats['raw_stats'] as $author => $stats )
                {$posts_ids = array_merge( $posts_ids, array_keys( $stats ) );}
        }

        if( $total_key = array_search( 'total', $posts_ids ) )
            {unset( $posts_ids[$total_key] );}

        $payment_data = PenciPWT_Payment::prepare_payment( $raw_stats );
        if( is_wp_error( $payment_data ) ) {die( $payment_data->get_error_message() );}

        $raw_stats = $payment_data['stats']['raw_stats'];

        do_action( 'ppcp_html_before_confirm_payment' );
        ?>

        <div class="wrap">
        <h2>Penci Pay Writer - <?php _e( 'Confirm payment' , 'penci-pay-writer' ); ?></h2>
        <p><?php _e( 'Review the payment you are about to make and make sure everything is fine. For each user, a summary and a detailed view is shown. When you are done, you can confirm the payment through the buttons at the bottom of the page. You are still in time to go back and edit your selection: nothing will be done.', 'penci-pay-writer' ); ?></p>

        <?php
        //We show first a summary of an author, and then their detailed posts list
        $n = 0;
        foreach( $raw_stats as $author => &$stats ) {
            $user = get_userdata( $author );
            $author_cols = array();
            $detailed_cols = array();
            $author_stats_data = array();
            $detailed_stats_data = array();

            $post_counting_types = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' );
            $author_counting_types = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'author' );

            //Author cols
            foreach( $stats['total']['ppc_payment']['due_payment'] as $id => $value ) {
				if( isset( $post_counting_types[$id] ) and ( ! isset( $post_counting_types[$id]['other_params']['not_to_pay'] ) or $counting_types[$id]['other_params']['not_to_pay'] ) )
					{$author_cols[$id] = $post_counting_types[$id]['label'];}
                else if( isset( $author_counting_types[$id] ) )
					{$author_cols[$id] = $author_counting_types[$id]['label'];}
			}

            $author_cols['author_total_payment'] = __( 'Payment', 'penci-pay-writer' );

            $author_cols = apply_filters( 'ppcp_confirm_payment_author_cols_after_default', $author_cols, $author, $stats );

            //Detailed cols
            $detailed_cols['post_title'] = __( 'Post Title', 'penci-pay-writer' );

            foreach( $stats['total']['ppc_payment']['due_payment'] as $id => $value ) {
				if( isset( $post_counting_types[$id] ) )
					{$detailed_cols[$id] = $post_counting_types[$id]['label'];}
			}

            $detailed_cols['post_total_payment'] = __( 'Payment', 'penci-pay-writer' );

            $detailed_cols = apply_filters( 'ppcp_confirm_payment_detailed_cols_after_default', $detailed_cols, $stats );
            ?>

            <form action="#" method="post" id="ppcp_confirm_payment_form">
            <h3 style="text-align: center;"><?php _e( 'User', 'penci-pay-writer' ); ?>: <?php echo $user->display_name; ?></h3>

            <h4><?php _e( 'Summary', 'penci-pay-writer' ); ?></h4>
            <table class="widefat fixed">
                <thead>
                    <tr>

            <?php
            foreach( $author_cols as $col )
                {echo '<th scope="col">'.$col.'</th>';}
            ?>

                    </tr>
                </thead>

                <tbody>
                    <tr>

            <?php
            foreach( $stats['total']['ppc_payment']['due_payment'] as $id => $value ) {
				if( isset( $post_counting_types[$id] ) or isset( $author_counting_types[$id] ) ) {

                    if( isset( $post_counting_types[$id] ) )
    					{$counting_types[$id] = $post_counting_types[$id];}
                    else if( isset( $author_counting_types[$id] ) )
    					{$counting_types[$id] = $author_counting_types[$id];}

					switch( $counting_types[$id]['display'] ) {
						case 'count':
							$author_stats_data[$id] = $stats['total']['ppc_count']['due_payment'][$id]['to_count'];
							break;

						case 'payment':
							$author_stats_data[$id] = PenciPWT_General_Functions::format_payment( $value );
							break;

                        case 'both':
                        case 'none':
                        default:
							$author_stats_data[$id] = $stats['total']['ppc_count']['due_payment'][$id]['to_count'].' ('.PenciPWT_General_Functions::format_payment( $value ).')';
							break;
					}
				}
			}

            $author_stats_data['total'] = PenciPWT_General_Functions::format_payment( $stats['total']['ppc_payment']['due_payment']['total'] );

            $author_stats_data = apply_filters( 'ppcp_confirm_payment_author_tr_after_default', $author_stats_data, $author, $stats );

            foreach( $author_stats_data as $id => $single )
                {echo '<td class="'.$id.'">'.$single.'</td>';}
            ?>

                    </tr>
                </tbody>
            </table>
            <br />

            <h4 style="display: inline;"><?php _e( 'Detailed', 'penci-pay-writer' ); ?></h4> --
            <button class="ppcp-confirm-payment-display-detailed" accesskey="<?php echo $author; ?>" id="ppcp-confirm-payment-display-detailed_<?php echo $author; ?>">Display</button>
            <button class="ppcp-confirm-payment-hide-detailed" accesskey="<?php echo $author; ?>" id="ppcp-confirm-payment-hide-detailed_<?php echo $author; ?>">Hide</button>
            <table class="widefat fixed" id="ppcp-confirm-detailed-table_<?php echo $author; ?>" style="margin-top: 20px;">
                <thead>
                    <tr>

            <?php
            foreach( $detailed_cols as $col )
                {echo '<th scope="col">'.$col.'</th>';}
            ?>

                    </tr>
                </thead>

                <tbody>

            <?php
            foreach( $stats as $post_id => &$post_stats ) {
                if( ! is_int( $post_id ) ) {continue;}
                if( $post_stats->ppc_payment['due_payment']['total'] == 0 ) {continue;}

                echo '<tr>';
                echo '<td>'.$post_stats->post_title.'</td>';

                foreach( $stats['total']['ppc_payment']['due_payment'] as $id => $value ) {
    				if( isset( $post_counting_types[$id] ) ) {

						if( ! isset( $post_stats->ppc_payment['due_payment'][$id] ) ) //happens in prepare_payment()
							{$post_stats->ppc_payment['due_payment'][$id] = 0;}

    				    switch( $post_counting_types[$id]['display'] ) {
    						case 'count':
    							$detailed_stats_data[$id] = isset( $post_stats->ppc_count['due_payment'][$id]['to_count'] ) ? $post_stats->ppc_count['due_payment'][$id]['to_count'] : 0;
    							break;

    						case 'payment':
    							$detailed_stats_data[$id] = isset( $post_stats->ppc_payment['due_payment'][$id] ) ? PenciPWT_General_Functions::format_payment( $post_stats->ppc_payment['due_payment'][$id] ) : 0;
    							break;

                            case 'both':
                            case 'none':
                            default:
    							$detailed_stats_data[$id] = isset( $post_stats->ppc_count['due_payment'][$id]['to_count'] ) ? $post_stats->ppc_count['due_payment'][$id]['to_count'].' ('.PenciPWT_General_Functions::format_payment( $post_stats->ppc_payment['due_payment'][$id] ).')' : 0;
    							break;
    					}
    				}
    			}

                $detailed_stats_data['total'] = PenciPWT_General_Functions::format_payment( $post_stats->ppc_payment['due_payment']['total'] );

                do_action( 'ppcp_confirm_payment_detailed_stats', $detailed_stats_data, $post_stats );

                foreach( $detailed_stats_data as $single )
                    {echo '<td>'.$single.'</td>';}

                echo '</tr>';
            }
            ?>

                </tbody>
            </table>
            <hr class="ppc_hr_divider" style="margin-top: 20px;" />

            <?php
        }
        ?>


            <textarea name="ppcp_payment_note" id="ppcp_payment_note" style="width: 100%; margin-bottom: 15px;" rows="2" placeholder="You may want to add a note to this payment..."></textarea>
        <?php
        if( $action != 'ppcp_paypal_payment' and 1==0 ) {
        ?>
            <label for="ppcp_payment_method" id="ppcp_payment_method_container">Payment method (optional)
                <select name="ppcp_payment_method" id="ppcp_payment_method">
                    <option value="mark"><?php echo ucfirst( __( 'none', 'penci-pay-writer' ) ); ?></option>
            <?php
            foreach( pencipwt_get_setting('payment_methods_list') as $text )
                {echo '<option value="'.$text.'">'.$text.'</option>';}
            ?>
                </select>
            </label>
            <?php
            }
            ?>
            <input type="hidden" name="ppcp_payment_posts_ids" id="ppcp_payment_posts_ids" value="<?php echo base64_encode( serialize( (array) $posts_ids ) ); ?>" />
            <input type="hidden" name="ppcp_payment_action" id="ppcp_payment_action" value="<?php echo $action; ?>" />
            <input type="hidden" name="ppcp_stats_tstart" id="ppcp_stats_tstart" value="<?php echo (int) $_POST['ppcp_stats_tstart']; ?>" />
            <input type="hidden" name="ppcp_stats_tend" id="ppcp_stats_tend" value="<?php echo (int) $_POST['ppcp_stats_tend']; ?>" />

			<?php
            do_action( 'ppcp_confirm_payment_form_before_submit' );
            ?>

            <input type="submit" class="button-primary" style="float: right;" name="ppcp_confirm_payment" id="ppcp_confirm_payment" value="<?php _e( 'Confirm payment', 'penci-pay-writer' ); ?>" />
        </form>

        <div id="ppcp_confirm_payment_loading" class="ppc_ajax_loader"><img src="<?php echo $pencipwt_global_settings['folder_path'].'style/images/ajax-loader.gif'; ?>" alt="<?php _e( 'Loading', 'penci-pay-writer'); ?>..." title="<?php _e( 'Loading', 'penci-pay-writer'); ?>..." /></div>
		<div id="ppcp_confirm_payment_error" class="ppc_error"></div>
		<div id="ppcp_confirm_payment_success" class="ppc_success"><?php _e( 'Selected items have been marked as paid successfully.', 'penci-pay-writer'); ?></div>

        <?php
        do_action( 'ppcp_html_after_confirm_payment' );
        ?>

        </div>

        <?php
    }
}

/**
 * Loads localization files
 *
 * @access  public
 * @since   2.0
 */

global $pencipwt_global_settings;
$pencipwt_global_settings = array();
new penci_paywriter_dashboard();