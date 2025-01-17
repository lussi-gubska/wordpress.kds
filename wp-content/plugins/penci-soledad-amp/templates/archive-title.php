<?php
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {

		if ( is_product_category() ) {
			$title = single_term_title( '', false );
		} elseif ( is_product_tag() ) {
			$title = single_term_title( '', false );
		} else {
			$title = penci_amp_get_setting( 'penci_amp_product-shop' );
		}
	} else if ( is_category() || is_tag() ) {
		$title = single_term_title( '', false );
	}elseif ( is_author() ) {
		$title = '<span class="vcard author author_name">' . get_the_author() . '</span>';
	} elseif ( is_year() ) {
		$title = get_the_date( 'Y' );
	} elseif ( is_month() ) {
		$title = get_the_date( 'F Y' );
	} elseif ( is_day() ) {
		$title = get_the_date( 'F j, Y' );
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_tax() ) {
		$term = get_queried_object();
		if ( $term ) {
			$tax   = get_taxonomy( $term->taxonomy );
			$title = single_term_title( $tax->labels->name . '%WP_TITLE_SEP%', false );
		}
	} else {
		$title = penci_amp_get_setting( 'penci_amp_archive' );
	}
?>
<header class="penci-amp-archive-page-header">
	<?php
	echo '<h1 class="archive-title">' . $title . '</h1>';
	$description = get_the_archive_description();
	if( $description  ){

		$dis_desc_sanitizer = penci_amp_get_setting( 'penci_amp_dis_desc_sanitizer' );
		if( ! $dis_desc_sanitizer ) {
			$penci_amp_content = new Penci_AMP_Content( $description,
				apply_filters( 'penci_amp_content_embed_handlers', array(
					'Penci_AMP_Twitter_Embed_Handler'     => array(),
					'Penci_AMP_YouTube_Embed_Handler'     => array(),
					'Penci_AMP_DailyMotion_Embed_Handler' => array(),
					'Penci_AMP_Vimeo_Embed_Handler'       => array(),
					'Penci_AMP_SoundCloud_Embed_Handler'  => array(),
					'Penci_AMP_Instagram_Embed_Handler'   => array(),
					'Penci_AMP_Vine_Embed_Handler'        => array(),
					'Penci_AMP_Facebook_Embed_Handler'    => array(),
					'Penci_AMP_Pinterest_Embed_Handler'   => array(),
					'Penci_AMP_Gallery_Embed_Handler'     => array(),
				), $description ),
				apply_filters( 'amp_content_sanitizers', array(
					'Penci_AMP_Style_Sanitizer'       => array(),
					'Penci_AMP_Img_Sanitizer'         => array(),
					'Penci_AMP_Video_Sanitizer'       => array(),
					'Penci_AMP_Audio_Sanitizer'       => array(),
					'Penci_AMP_Form_Sanitizer'        => array(),
					'Penci_AMP_Playbuzz_Sanitizer'    => array(),
					'Penci_AMP_Iframe_Sanitizer'      => array(
						'add_placeholder' => true,
					),
					'AMP_Tag_And_Attribute_Sanitizer' => array(),
				), $description )
			);
			$description = $penci_amp_content->get_penci_amp_content();
		}

		echo '<div class="penci-amp-taxonomy-description">';
		echo $description;
		echo '</div>';
	}


	?>
</header>
