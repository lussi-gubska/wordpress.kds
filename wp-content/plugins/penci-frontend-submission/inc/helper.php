<?php
add_action( 'wp_ajax_pencifts_find_ajax_post_category', 'pencifts_find_ajax_post_category' );
add_action( 'wp_ajax_nopriv_pencifts_find_ajax_post_category', 'pencifts_find_ajax_post_category' );

if ( ! function_exists( 'pencifts_find_ajax_post_category' ) ) {

	function pencifts_find_ajax_post_category() {
		if ( isset( $_REQUEST['string'] ) && ! empty( $_REQUEST['string'] ) ) {
			$string = $_REQUEST['string'];
		} else {
			return false;
		}

		$hide_empty = $_REQUEST['hide_empty'] ?: false;

		$args = array(
			'taxonomy'   => array( 'category' ),
			'orderby'    => 'id',
			'order'      => 'ASC',
			'hide_empty' => $hide_empty,
			'fields'     => 'all',
			'name__like' => urldecode( $string ),
			'number'     => 50
		);

		$terms = get_terms( $args );

		$result = array();

		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $term ) {
				$result[] = array(
					'value' => $term->term_id,
					'text'  => $term->name
				);
			}
		}

		wp_send_json( $result );
	}
}

add_action( 'wp_ajax_pencifts_find_ajax_post_tag', 'pencifts_find_ajax_post_tag' );
add_action( 'wp_ajax_nopriv_pencifts_find_ajax_post_tag', 'pencifts_find_ajax_post_tag' );

if ( ! function_exists( 'pencifts_find_ajax_post_tag' ) ) {

	function pencifts_find_ajax_post_tag() {
		if ( isset( $_REQUEST['string'] ) && ! empty( $_REQUEST['string'] ) ) {
			$string = $_REQUEST['string'];
		} else {
			return false;
		}

		$args = array(
			'taxonomy'   => array( 'post_tag' ),
			'orderby'    => 'id',
			'order'      => 'ASC',
			'hide_empty' => true,
			'fields'     => 'all',
			'name__like' => urldecode( $string )
		);

		$terms = get_terms( $args );

		$result = array();

		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $term ) {
				$result[] = array(
					'value' => $term->term_id,
					'text'  => $term->name
				);
			}
		}

		wp_send_json( $result );
	}
}

add_action( 'wp_ajax_pencifts_find_ajax_author', 'pencifts_find_ajax_author' );
add_action( 'wp_ajax_nopriv_pencifts_find_ajax_author', 'pencifts_find_ajax_author' );

if ( ! function_exists( 'pencifts_find_ajax_author' ) ) {

	function pencifts_find_ajax_author() {
		if ( isset( $_REQUEST['string'] ) && ! empty( $_REQUEST['string'] ) ) {
			$string = esc_attr( trim( $_REQUEST['string'] ) );
		} else {
			return false;
		}

		$users       = new WP_User_Query( array(
			'search'         => "*{$string}*",
			'search_columns' => array(
				'user_login',
				'user_nicename',
				'user_email',
				'user_url',
			),
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'first_name',
					'value'   => $string,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'last_name',
					'value'   => $string,
					'compare' => 'LIKE'
				)
			)
		) );
		$users_found = $users->get_results();

		$result = array();

		if ( count( $users_found ) > 0 ) {
			foreach ( $users_found as $user ) {
				$result[] = array(
					'value' => $user->ID,
					'text'  => $user->display_name
				);
			}
		}

		wp_send_json( $result );
	}
}

if ( ! function_exists( 'penci_flash_message' ) ) {
	function penci_flash_message( $name = '', $message = '', $class = 'success' ) {
		$session = \PenciFrontendSubmission\Session::getInstance();

		return $session->flash_message( $name, $message, $class );
	}
}

function penci_get_post_data( $id ) {
	$post = get_post( $id );

	$categories = get_the_terms( $post->ID, 'category' );
	$category   = array();

	if ( ! empty( $categories ) && is_array( $categories ) ) {
		foreach ( $categories as $term ) {
			$category[] = $term->term_id;
		}
	}

	$tags = get_the_terms( $post->ID, 'post_tag' );
	$tag  = array();

	if ( ! empty( $tags ) && is_array( $tags ) ) {
		foreach ( $tags as $term ) {
			$tag[] = $term->term_id;
		}
	}

	$taxonomies_data = [];

	$taxonomies = get_taxonomies( [ 'object_type' => [ $post->post_type ] ] );
	foreach ( $taxonomies as $taxonomy ) {
		$terms = get_the_terms( $post->ID, $taxonomy );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$taxonomies_data[ $taxonomy ][] = $term->term_id;
			}
		}
	}

	$post_fromat  = get_post_format( $post->ID );
	$post_video   = get_post_meta( $post->ID, '_format_video_embed', true );
	$post_audio   = get_post_meta( $post->ID, '_format_audio_embed', true );
	$post_gallery = get_post_meta( $post->ID, '_format_gallery_images', true );

	$data = array(
		'id'         => $post->ID,
		'title'      => $post->post_title,
		'subtitle'   => get_post_meta( $id, 'penci_post_sub_title', true ),
		'content'    => $post->post_content,
		'category'   => implode( ',', $category ),
		'tag'        => implode( ',', $tag ),
		'format'     => $post_fromat ? $post_fromat : 'image',
		'video'      => $post_video,
		'audio'      => $post_audio,
		'gallery'    => $post_gallery,
		'image'      => get_post_thumbnail_id( $post ),
		'taxonomies' => $taxonomies_data,
	);

	return $data;
}

if ( ! function_exists( 'penci_get_package_list' ) ) {
	function penci_get_package_list() {
		$result   = array();
		$packages = null;
		if ( class_exists( 'WooCommerce' ) ) {
			$packages = get_posts(
				array(
					'post_type'      => 'product',
					'posts_per_page' => - 1,
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => array( 'post_package' ),
						),
					),
					'orderby'        => 'menu_order title',
					'order'          => 'ASC',
					'post_status'    => 'publish',
				)
			);
		}

		if ( function_exists( 'getpaid' ) ) {
			$packages = get_posts(
				array(
					'post_type'      => 'wpi_item',
					'orderby'        => 'title',
					'order'          => 'ASC',
					'posts_per_page' => - 1,
					'post_status'    => array( 'publish' ),
					'meta_query'     => array(
						array(
							'key'     => '_wpinv_type',
							'compare' => 'IN',
							'value'   => [ 'post_package' ]
						)
					)
				)
			);
		}

		if ( $packages ) {
			foreach ( $packages as $value ) {
				$result[ $value->ID ] = $value->post_title;
			}
		} else {
			$result[''] = __( 'No Post Package Found', 'penci-frontend-submission' );
		}

		return $result;
	}
}

if ( ! function_exists( 'penci_ftsub_text' ) ) {
	function penci_ftsub_text() {
		return [
			'first_name'         => __( 'First Name', 'penci-frontend-submission' ),
			'insert_first_name'  => __( 'Insert your first name', 'penci-frontend-submission' ),
			'l_name'             => __( 'Last Name', 'penci-frontend-submission' ),
			'insert_l_name'      => __( 'Insert your last name', 'penci-frontend-submission' ),
			'd_name'             => __( 'Display Name', 'penci-frontend-submission' ),
			'cinfo'              => __( 'Contact Info', 'penci-frontend-submission' ),
			'about_yourself'     => __( 'About Yourself', 'penci-frontend-submission' ),
			'bio_info'           => __( 'Biographical Info', 'penci-frontend-submission' ),
			'fpic'               => __( 'Profile Picture', 'penci-frontend-submission' ),
			'eaccount'           => __( 'Edit Account', 'penci-frontend-submission' ),
			'oldpassword'        => __( 'Old Password', 'penci-frontend-submission' ),
			'newpassword'        => __( 'New Password', 'penci-frontend-submission' ),
			'cpassword'          => __( 'Confirm Password', 'penci-frontend-submission' ),
			'changepassword'     => __( 'Change Password', 'penci-frontend-submission' ),
			'title'              => __( 'Enter title here', 'penci-frontend-submission' ),
			'stitle'             => __( 'Enter subtitle here', 'penci-frontend-submission' ),
			'content'            => __( 'Post Content', 'penci-frontend-submission' ),
			'standard'           => __( 'Standard', 'penci-frontend-submission' ),
			'gallery'            => __( 'Gallery', 'penci-frontend-submission' ),
			'video'              => __( 'Video', 'penci-frontend-submission' ),
			'audio'              => __( 'Audio', 'penci-frontend-submission' ),
			'insert_embed'       => __( 'Insert video url or embed code', 'penci-frontend-submission' ),
			'cat'                => __( 'Categories', 'penci-frontend-submission' ),
			'ccat'               => __( 'Choose categories', 'penci-frontend-submission' ),
			'tag'                => __( 'Tags', 'penci-frontend-submission' ),
			'ctag'               => __( 'Choose tags', 'penci-frontend-submission' ),
			'spost'              => __( 'Submit Post', 'penci-frontend-submission' ),
			'epost'              => __( 'Edit Post', 'penci-frontend-submission' ),
			'vpost'              => __( 'View Post', 'penci-frontend-submission' ),
			'upost'              => __( 'Update Post', 'penci-frontend-submission' ),
			'unlimited'          => __( 'Unlimited', 'penci-frontend-submission' ),
			'rquota'             => __( 'Remaining Quota', 'penci-frontend-submission' ),
			'showing_result'     => __( 'Showing {{value}}-{{value}} of {{value}} post results', 'penci-frontend-submission' ),
			'sort_latest'        => __( 'Sort by latest', 'penci-frontend-submission' ),
			'sort_older'         => __( 'Sort by older', 'penci-frontend-submission' ),
			'no_content'         => __( 'No Content Available', 'penci-frontend-submission' ),
			'cimage'             => __( 'Choose Image', 'penci-frontend-submission' ),
			'admedia'            => __( 'Add Media', 'penci-frontend-submission' ),
			'insert'             => __( 'Insert', 'penci-frontend-submission' ),
			'cpost'              => __( 'Create New Post', 'penci-frontend-submission' ),
			'my_account'         => __( 'My Account', 'soledad' ),
			'deleted'            => __( 'Delete Post', 'soledad' ),
			'edit_account'       => __( 'Edit Account', 'soledad' ),
			'change_password'    => __( 'Change Password', 'soledad' ),
			'create_new_post'    => __( 'Create New Post', 'soledad' ),
			'my_post'            => __( 'My Posts', 'soledad' ),
			'update_notice'      => __( 'You have successfully edited your account details', 'soledad' ),
			'password_not_valid' => __( 'Your old password is not valid', 'soledad' ),
			'password_new'       => __( 'Please enter your new password', 'soledad' ),
			'password_match'     => __( 'New Password & Confirm Password do not match', 'soledad' ),
			'password_success'   => __( 'You have successfully changed your password', 'soledad' ),
			'password_e'         => __( 'Please enter your old password', 'soledad' ),
			'ptitle_n'           => __( 'Post title cannot be empty', 'soledad' ),
			'pupdate_n'          => __( 'Post updated successfully', 'soledad' ),
			'pcreated_n'         => __( 'Post created successfully', 'soledad' ),
			'bauthor'            => __( 'Become an Author', 'soledad' ),
			'edit_type'          => __( 'Edit Post Type', 'soledad' ),
			'cgagllery'          => __( 'Choose Gallery Images', 'penci-frontend-submission' ),
			'cfimages'           => __( 'Choose Featured Image', 'penci-frontend-submission' ),
			'cfconfirm'          => __( 'Are you sure you want to delete this post?', 'penci-frontend-submission' ),
			'not_allow'          => __( 'You can\'t create post at this time. Please purchase a Premium Package', 'soledad' ),
		];
	}
}
if ( ! function_exists( 'penci_ftsub_get_text' ) ) {
	function penci_ftsub_get_text( $text ) {
		$texts  = penci_ftsub_text();
		$option = 'pcfsub_' . $text;

		$default = $texts[ $text ];
		$default = str_replace( '{{value}}', '%s', $default );

		return get_theme_mod( $option ) ? do_shortcode( get_theme_mod( $option ) ) : $default;
	}
}

add_action( 'wp_ajax_pencifts_deleted_post', 'pencifts_deleted_post' );

if ( ! function_exists( 'pencifts_deleted_post' ) ) {

	function pencifts_deleted_post() {
		wp_verify_nonce( 'nonce', 'pencifts_deleted_post' );

		$post_id = $_POST['id'];
		if ( current_user_can( 'delete_post', $post_id ) ) {
			wp_trash_post( $post_id );
			wp_send_json_success( 'success' );
		} else {
			wp_send_json_error( 'nope' );
		}
	}
}