<?php
/**
 * Social buttons class.
 */

echo '<span class="penci-social-buttons penci-social-share-footer">';
echo '<span class="penci-social-share-text">' . penci_amp_get_setting( 'penci-social-share-text' ) . '</span>';


$list_social = array(
	'facebook',
	'twitter', 
	'pinterest', 
	'linkedin', 
	'tumblr',
	'vk',
	'ok',
	'reddit',
	'stumbleupon',
	'whatsapp',
	'telegram',
	'pocket',
	'skype',
	'email'
) ;

$option_prefix = 'penciamp_hide_share_';

$output = '';

foreach ( $list_social as $k => $social_key ) {
	$list_social_item = penci_amp_get_setting( $option_prefix . $social_key );
	if ( $list_social_item ) {
		continue;
	}

	$link     = get_permalink( );
	$text     = str_replace( '|', '-', get_the_title() );
	$img_link = get_the_post_thumbnail_url();

	switch ( $social_key ) {
		case 'facebook':
			$facebook_share  = 'https://www.facebook.com/sharer/sharer.php?u=' . get_the_permalink();
			$output .= '<a class="penci-social-item facebook" target="_blank" href="'. esc_url( $facebook_share ) .'"><i class="fa fa-facebook"></i><span class="dt-share">'. esc_html__( 'Facebook', 'soledad' ) . '</span></a>';
			break;
		case 'twitter':
			$twitter_text = 'Check out this article';
			if( get_theme_mod( 'penci_post_twitter_share_text' ) ){
				$twitter_text = do_shortcode( get_theme_mod( 'penci_post_twitter_share_text' ) );
			}
			$twitter_text = trim( $twitter_text );
			$twitter_text_process = str_replace( ' ', '%20', $twitter_text );

			$twitter_share   = 'https://twitter.com/intent/tweet?text=' . $twitter_text_process . ':%20' . $text . '%20-%20' . get_the_permalink();
			$output .= '<a class="penci-social-item twitter" target="_blank" href="'. esc_url( $twitter_share ) .'"><i class="fa fa-twitter"></i><span class="dt-share">' . esc_html__( 'Twitter', 'soledad' ) . '</span></a>';

			break;
		case 'pinterest':
			$pinterest_image = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
			$pinterest_share = 'https://pinterest.com/pin/create/button/?url=' . get_the_permalink() . '&media=' . $pinterest_image . '&description=' . $text;
			$output .= '<a class="penci-social-item pinterest" data-pin-do="none" target="_blank" href="' . esc_url( $pinterest_share ) . '"><i class="fa fa-pinterest"></i><span class="dt-share">' . esc_html__( 'Pinterest', 'soledad' ) . '</span></a>';
			break;

		case 'linkedin':
			$link = htmlentities( add_query_arg( array(
				'url'   => rawurlencode( $link ),
				'title' => rawurlencode( $text ),
			), 'https://www.linkedin.com/shareArticle?mini=true' ) );

			$output .= '<a class="penci-social-item linkedin" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-' . $social_key . '"></i><span class="dt-share">' . esc_html__( 'Linkedin', 'soledad' ) . '</span></a>';
			break;

		case 'tumblr':
			$link = htmlentities( add_query_arg( array(
				'url'  => rawurlencode( $link ),
				'name' => rawurlencode( $text ),
			), 'https://www.tumblr.com/share/link' ) );
			$output .= '<a class="penci-social-item tumblr" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-' . $social_key . '"></i><span class="dt-share">' . esc_html__( 'Tumblr', 'soledad' ) . '</span></a>';
			break;
		
		case 'vk':
			$link = 'https://vk.com/share.php?url=' . get_the_permalink();
			$output .= '<a class="penci-social-item vk" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-' . $social_key . '"></i><span class="dt-share">' . esc_html__( 'VK', 'soledad' ) . '</span></a>';
			break;
			
		case 'ok':
			$link = 'https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl='. $link .'&amp;description='. $text .'&amp;media='. $img_link;
			$output .= '<a class="penci-social-item ok" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-odnoklassniki"></i><span class="dt-share">' . esc_html__( 'Odnoklassniki', 'soledad' ) . '</span></a>';
			break;
		
		case 'reddit':
			$link = htmlentities( add_query_arg( array(
				'url'   => rawurlencode( $link ),
				'title' => rawurlencode( $text ),
			), 'https://reddit.com/submit' ) );
			$output .= '<a class="penci-social-item reddit" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-' . $social_key . '"></i><span class="dt-share">' . esc_html__( 'Reddit', 'soledad' ) . '</span></a>';
			break;
			
		case 'stumbleupon':
			$link = htmlentities( add_query_arg( array(
				'url'   => rawurlencode( $link ),
				'title' => rawurlencode( $text ),
			), 'https://www.stumbleupon.com/submit' ) );
			$output .= '<a class="penci-social-item stumbleupon" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-' . $social_key . '"></i><span class="dt-share">' . esc_html__( 'Stumbleupon', 'soledad' ) . '</span></a>';
			break;
		case 'email':
			$link = esc_url ( 'mailto:?subject=' . $text . '&BODY=' . $link );
			$output .= '<a class="penci-social-item email" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-envelope"></i><span class="dt-share">' . esc_html__( 'Email', 'soledad' ) . '</span></a>';
			break;
		case 'telegram':
			$link = htmlentities( add_query_arg( array(
				'url'  => rawurlencode( $link ),
				'text' => rawurlencode( $text ),
			), 'https://telegram.me/share/url' ) );
			$output .= '<a class="penci-social-item telegram" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-' . $social_key . '"></i><span class="dt-share">' . esc_html__( 'Telegram', 'soledad' ) . '</span></a>';
			break;

		case 'whatsapp':
			$link = htmlentities( add_query_arg( array(
				'text' => rawurlencode( $text ) . ' %0A%0A ' . rawurlencode( $link ),
			), 'whatsapp://send' ) );
			$output .= '<a class="penci-social-item whatsapp" target="_blank" href="' . ( $link ) . '"><i class="fa fa-' . $social_key . '"></i><span class="dt-share">' . esc_html__( 'Whatsapp', 'soledad' ) . '</span></a>';
			break;
			
		case 'pocket':
			$link = 'https://getpocket.com/save?title='. $text .'&amp;url='. $link;
			$output .= '<a class="penci-social-item pocket" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-get-pocket"></i><span class="dt-share">' . esc_html__( 'Pocket', 'soledad' ) . '</span></a>';
			break;
			
		case 'skype':
			$link = 'https://web.skype.com/share?url='. $link .'&text='. $text;
			$output .= '<a class="penci-social-item skype" target="_blank" href="' . esc_url( $link ) . '"><i class="fa fa-' . $social_key . '"></i><span class="dt-share">' . esc_html__( 'Skype', 'soledad' ) . '</span></a>';
			break;	
			
		default:
			$output .= '';
			break;
	}
}
echo $output;

echo '</span>';