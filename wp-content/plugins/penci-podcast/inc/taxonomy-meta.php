<?php
/**
 * Hook to create meta box in categories edit screen
 *
 * @since 1.0
 */

// Create markup
if ( ! function_exists( 'pencipdc_category_fields_meta' ) ) {
	add_action( 'podcast-series_edit_form_fields', 'pencipdc_category_fields_meta' );
	add_action( 'podcast-category_edit_form_fields', 'pencipdc_category_fields_meta' );
	function pencipdc_category_fields_meta( $tag ) {
		$t_id             = $tag->term_id;
		$t_tax            = $tag->taxonomy;
		$penci_categories = get_option( "pencipdc_series_$t_id" );

		if ( 'podcast-category' == $t_tax ) {
			$penci_categories = get_option( "pencipdc_category_$t_id" );
		}

		$featured_img = isset( $penci_categories['featured_img'] ) ? $penci_categories['featured_img'] : '';
		?>
        <tr class="form-field">
            <th scope="row">
                <label for="featured_img"><?php esc_html_e( 'Featured Image', 'soledad' ); ?></label>
            </th>
            <td>
                <div class="form-group">
                    <label for="photo"><?php _e( 'Upload Image', 'penci-frontend-submission' ); ?></label>
                    <div class="form-input-wrapper">
						<?php
						load_template( get_template_directory() . '/inc/templates/upload_form.php', true, array(
							'id'      => 'featured_img',
							'class'   => '',
							'name'    => 'featured_img',
							'source'  => $featured_img,
							'button'  => 'btn-single-image',
							'multi'   => false,
							'maxsize' => apply_filters( 'penci_maxsize_upload_profile_picture', '2mb' )
						) );
						?>
                    </div>
                    <style>
                        #featured_img img {
                            max-width: 150px;
                            height: auto;
                        }
                    </style>
                </div>
            </td>
        </tr>
		<?php
	}
}

// Save data
if ( ! function_exists( 'pencipdc_save_series_fileds_meta' ) ) {
	add_action( 'edited_podcast-series', 'pencipdc_save_series_fileds_meta' );
	function pencipdc_save_series_fileds_meta( $term_id ) {
		if ( isset( $_POST['featured_img'] ) ) {
			$t_id                             = $term_id;
			$penci_categories                 = get_option( "pencipdc_series_$t_id" );
			$penci_categories['featured_img'] = $_POST['featured_img'];
			//save the option array
			update_option( "pencipdc_series_$t_id", $penci_categories );
		}
	}
}

if ( ! function_exists( 'pencipdc_save_category_fileds_meta' ) ) {
	add_action( 'edited_podcast-category', 'pencipdc_save_category_fileds_meta' );
	function pencipdc_save_category_fileds_meta( $term_id ) {
		if ( isset( $_POST['featured_img'] ) ) {
			$t_id                             = $term_id;
			$penci_categories                 = get_option( "pencipdc_category_$t_id" );
			$penci_categories['featured_img'] = $_POST['featured_img'];
			//save the option array
			update_option( "pencipdc_category_$t_id", $penci_categories );
		}
	}
}
