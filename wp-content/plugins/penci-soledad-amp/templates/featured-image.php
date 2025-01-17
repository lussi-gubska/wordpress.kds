<?php
$featured_image = $this->get( 'featured_image' );

if ( has_post_format( 'video' ) ) {

	$post_id = get_the_ID();
	$penci_video = get_post_meta( $post_id, '_format_video_embed', true );
	$penci_amp_content = new Penci_AMP_Content( $penci_video,
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
		), $penci_video ),
		apply_filters( 'amp_content_sanitizers', array(
			'Penci_AMP_Style_Sanitizer'             => array(),
			'Penci_AMP_Img_Sanitizer'               => array(),
			'Penci_AMP_Video_Sanitizer'             => array(),
			'Penci_AMP_Audio_Sanitizer'             => array(),
			'Penci_AMP_Playbuzz_Sanitizer'          => array(),
			'Penci_AMP_Iframe_Sanitizer'            => array(
				'add_placeholder' => true,
			),
			'AMP_Tag_And_Attribute_Sanitizer' => array(),
		), $penci_video )
	);


	$content = $penci_amp_content->get_penci_amp_content();
	echo $content;

}elseif ( has_post_format( 'gallery' ) ){
	$images = get_post_meta( get_the_ID(), '_format_gallery_images', true );
	?>
	<div class="post-image gallery-feature-img-single">
		<amp-carousel class="amp-slider amp-featured-slider" layout="responsive" type="slides" width="780" height="500" delay="3500">
		<?php
		foreach ( $images as $image ){
			$the_image = wp_get_attachment_image_src( $image, 'penci-full-thumb' );
			$image_src = isset( $the_image[0] ) ? $the_image[0] : '';
			if( empty( $image_src )  ) {
				continue;
			}
			$the_caption = get_post_field( 'post_excerpt', $image );
			echo '<div class="slider-item ' . ( ! $the_caption ? 'slider-item-not-caption' : '' ) .  '">';
			echo '<div id="penci-slider-img-holder' . $image . '" class="img-holder"></div>';
			echo $the_caption ? '<div class="content-holder"><span>' . $the_caption . '</span></div>' : '';
			echo '</div>';
		}
		?>
	</div>
	<?php

}elseif ( ! empty( $featured_image ) ) {
	$penci_amp_html = $featured_image['penci_amp_html'];
	$caption = $featured_image['caption'];
	$penci_amp_html = str_replace( ' src="', ' layout="responsive" data-hero src="', $penci_amp_html );
	?>

	<figure class="amp-wp-article-featured-image wp-caption" <?php  ?>>
		<?php echo $penci_amp_html; ?>
		<?php if ( $caption ) : ?>
			<p class="wp-caption-text">
				<?php echo wp_kses_data( $caption ); ?>
			</p>
		<?php endif; ?>
	</figure>
	<?php
}

