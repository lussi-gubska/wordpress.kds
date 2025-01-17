<?php
$options = array();

$options[] = array(
	'id'        => 'pencibf_disable_header_icon',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Disable Header Bookmark Icon', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_01',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Posts Type Follows', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'       => 'pencibf_prevent_type',
	'default'  => ['post'],
	'sanitize' => 'penci_sanitize_multiple_checkbox',
	'label'    => __( 'Enable Support in Post Types', 'soledad' ),
	'type'     => 'soledad-fw-select',
	'multiple' => 999,
	'choices'  => call_user_func( function () {
		$exclude    = array(
			'attachment',
			'revision',
			'nav_menu_item',
			'safecss',
			'penci-block',
			'penci_builder',
			'custom-post-template',
			'archive-template',
		);
		$registered = get_post_types( [ 'show_in_nav_menus' => true ], 'objects' );
		$types      = array();


		foreach ( $registered as $post ) {

			if ( in_array( $post->name, $exclude ) ) {

				continue;
			}

			$types[ $post->name ] = $post->label;
		}

		return $types;
	} )
);

$options[] = array(
	'id'          => 'pencibf_disable_follow_guest',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Disable Guest Followers', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'Guests (non-logged-in users) have the permission to follow any post by default. If you check this option, then Follow button would not be displayed for Guest users.', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_enable_notify_followers',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Auto Send Email Notification for Followers', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'If this option is checked, whenever the post is edited or receives new comments, the followers will receive an email notification.', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_disable_auto_follow_add_comment',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Disable Email Notify When Comment Added', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'This option allows you to disable email notifications when a post receives a new comment. It applies when you have checked the "Auto Send Email Notification for Followers" option mentioned above.', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_custom_subscribe_page',
	'transport'   => 'postMessage',
	'type'     => 'soledad-fw-ajax-select',
	'description' => __( 'After selecting a custom bookmark page, you need to insert the shortcodes <strong>[pencibf_follow_post_list]</strong> and <strong>[pencibf_follow_author_list]</strong> into the page to display the bookmarked content.','penci-bookmark-follow'),
	'choices'         => call_user_func( function () {
		$builder_layout  = [ '' => '- Select -' ];
		$builder_layouts = get_posts( [
			'post_type'      => 'page',
			'posts_per_page' => - 1,
		] );

		foreach ( $builder_layouts as $builder_builder ) {
			$builder_layout[ $builder_builder->ID ] = $builder_builder->post_title;
		}

		return $builder_layout;
	} ),
	'label'       => esc_html__( 'Custom Bookmark Page', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_custom_unsubscribe_page',
	'transport'   => 'postMessage',
	'type'     => 'soledad-fw-ajax-select',
	'description' => __( 'After selecting a custom Unsubscribe page, you need to insert the shortcodes <strong>[pencibf_unsubscribe]</strong> into the page to display the Unsubscribe Form.','penci-bookmark-follow'),
	'choices'         => call_user_func( function () {
		$builder_layout  = [ '' => '- Select -' ];
		$builder_layouts = get_posts( [
			'post_type'      => 'page',
			'posts_per_page' => - 1,
		] );

		foreach ( $builder_layouts as $builder_builder ) {
			$builder_layout[ $builder_builder->ID ] = $builder_builder->post_title;
		}

		return $builder_layout;
	} ),
	'label'       => esc_html__( 'Custom Unsubscribe Email Bookmark Page', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_custom_cat_page',
	'transport'   => 'postMessage',
	'type'     => 'soledad-fw-ajax-select',
	'description' => __( 'Select the page that you\'ve inserted the Category Listing element.','penci-bookmark-follow'),
	'choices'         => call_user_func( function () {
		$builder_layout  = [ '' => '- Select -' ];
		$builder_layouts = get_posts( [
			'post_type'      => 'page',
			'posts_per_page' => - 1,
		] );

		foreach ( $builder_layouts as $builder_builder ) {
			$builder_layout[ $builder_builder->ID ] = $builder_builder->post_title;
		}

		return $builder_layout;
	} ),
	'label'       => esc_html__( 'Custom Category Listing Page', 'penci-bookmark-follow' ),
);


$options[] = array(
	'id'    => 'pencibf_header_04',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Bookmark Page Settings', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_number_ppp',
	'transport' => 'postMessage',
	'default'   => '6',
	'type'      => 'soledad-fw-number',
	'label'     => esc_html__( 'Number of Bookmark Items Per Page', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'       => 'pencibf_show_postdate',
	'sanitize' => 'penci_sanitize_checkbox_field',
	'default'  => true,
	'type'     => 'soledad-fw-toggle',
	'label'    => esc_html__( 'Show Post Date', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'       => 'pencibf_show_author',
	'sanitize' => 'penci_sanitize_checkbox_field',
	'default'  => true,
	'type'     => 'soledad-fw-toggle',
	'label'    => esc_html__( 'Show Post Author', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'       => 'pencibf_show_comments',
	'sanitize' => 'penci_sanitize_checkbox_field',
	'default'  => false,
	'type'     => 'soledad-fw-toggle',
	'label'    => esc_html__( 'Show Post Comment Count', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'       => 'pencibf_show_views',
	'sanitize' => 'penci_sanitize_checkbox_field',
	'default'  => false,
	'type'     => 'soledad-fw-toggle',
	'label'    => esc_html__( 'Show Post Views', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'       => 'pencibf_show_reading',
	'sanitize' => 'penci_sanitize_checkbox_field',
	'default'  => false,
	'type'     => 'soledad-fw-toggle',
	'label'    => esc_html__( 'Show Post Reading Time', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_06',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Colors', 'penci-bookmark-follow' ),
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Bookmark Color',
	'id'       => 'pencibf_bm_cl',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Bookmark Border Color',
	'id'       => 'pencibf_bm_bcl',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Bookmark Background Color',
	'id'       => 'pencibf_bm_bgcl',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Bookmark Hover Color',
	'id'       => 'pencibf_bm_hcl',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Bookmark Hover Border Color',
	'id'       => 'pencibf_bm_hbcl',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Bookmark Hover Background Color',
	'id'       => 'pencibf_bm_hbgcl',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Bookmarked Color',
	'id'       => 'pencibf_bm_bmcl',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Bookmarked Border Color',
	'id'       => 'pencibf_bm_bmbcl',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Bookmarked Background Color',
	'id'       => 'pencibf_bm_bmbgcl',
);

return $options;