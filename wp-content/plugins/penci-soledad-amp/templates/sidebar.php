<amp-sidebar id="penci_sidebar" class="mobile-sidebar" layout="nodisplay" side="<?php echo ( is_rtl() ? 'right' : 'left' ); ?>">
	<button id="close-sidebar-nav" on="tap:penci_sidebar.close" class="ampstart-btn caps m2"><i class="fa fa-close"></i></button>
	<div id="sidebar-nav-logo">
		<?php
		$sidebar_logo = penci_amp_get_branding_info( 'sidebar' );

		if ( empty( $sidebar_logo['logo-tag'] ) && $sidebar_logo['name'] ) {
			$sidebar_logo = penci_amp_get_branding_info( $pos = 'header' );
		}

		?>
		<a href="<?php echo esc_url( penci_amp_get_site_url() ); ?>"
		   class="sidebar-branding <?php echo ! empty( $sidebar_logo['logo-tag'] ) ? 'penci-amp-site-icon sidebar-image-logo' : 'text-logo'; ?> ">
			<?php
			if ( ! empty( $sidebar_logo['logo-tag'] ) ) {
				echo $sidebar_logo['logo-tag']; // escaped before
			} else {
				echo $sidebar_logo['name']; // escaped before
			}
			?>
		</a>
	</div>
	<?php
	$penci_facebook = function_exists( 'penci_get_setting' ) ?  penci_get_setting( 'penci_facebook' ) :  get_theme_mod( 'penci_facebook' );
	$penci_twitter  = function_exists( 'penci_get_setting' ) ?  penci_get_setting( 'penci_twitter' ) :  get_theme_mod( 'penci_twitter' );
	if ( get_theme_mod( 'penci_email_me' ) || get_theme_mod( 'penci_vk' ) || $penci_facebook || $penci_twitter || get_theme_mod( 'penci_google' ) || get_theme_mod( 'penci_instagram' ) || get_theme_mod( 'penci_pinterest' ) || get_theme_mod( 'penci_linkedin' ) || get_theme_mod( 'penci_flickr' ) || get_theme_mod( 'penci_behance' ) || get_theme_mod( 'penci_tumblr' ) || get_theme_mod( 'penci_youtube' ) || get_theme_mod( 'penci_rss' ) ) : ?>
		<div class="header-social sidebar-nav-social">
			<div class="inner-header-social">
				<?php if ( $penci_facebook ) : ?>
					<a href="<?php echo esc_attr( $penci_facebook ); ?>" rel="nofollow" target="_blank"><i class="fa fa-facebook"></i></a>
				<?php endif; ?>
				<?php if ( $penci_twitter ) : ?>
					<a href="<?php echo esc_attr( $penci_twitter ); ?>" rel="nofollow" target="_blank"><i class="fa fa-twitter"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_instagram' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_instagram' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-instagram"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_pinterest' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_pinterest' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-pinterest"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_linkedin' ) ) : ?>
					<a href="<?php echo esc_url( get_theme_mod( 'penci_linkedin' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-linkedin"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_flickr' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_flickr' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-flickr"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_behance' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_behance' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-behance"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_tumblr' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_tumblr' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-tumblr"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_youtube' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_youtube' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-youtube-play"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_email_me' ) ) : ?>
					<a href="<?php echo get_theme_mod( 'penci_email_me' ); ?>"><i class="fa fa-envelope-o"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_vk' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_vk' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-vk"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_bloglovin' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_bloglovin' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-heart"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_vine' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_vine' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-vine"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_soundcloud' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_soundcloud' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-soundcloud"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_snapchat' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_snapchat' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-snapchat-ghost"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_spotify' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_spotify' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-spotify"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_github' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_github' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-github"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_stack' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_stack' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-stack-overflow"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_twitch' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_twitch' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-twitch"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_vimeo' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_vimeo' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-vimeo"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_steam' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_steam' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-steam"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_xing' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_xing' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-xing"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_whatsapp' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_whatsapp' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-whatsapp"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_telegram' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_telegram' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-telegram"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_reddit' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_reddit' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-reddit-alien"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_ok' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_ok' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-odnoklassniki"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_500px' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_500px' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-500px"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_stumbleupon' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_stumbleupon' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-stumbleupon"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_wechat' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_wechat' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-weixin"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_weibo' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_weibo' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-weibo"></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_line' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_line' ) ); ?>" rel="nofollow" target="_blank"><i class="penci-icon-line"><?php echo penci_svg_social('line'); ?></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_viber' ) ) : ?>
					<a href="<?php echo esc_attr( get_theme_mod( 'penci_viber' ) ); ?>" rel="nofollow" target="_blank"><i class="penci-icon-viber"><?php echo penci_svg_social('viber'); ?></i></a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'penci_rss' ) ) : ?>
					<a href="<?php echo esc_url( get_theme_mod( 'penci_rss' ) ); ?>" rel="nofollow" target="_blank"><i class="fa fa-rss"></i></a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php
	$theme_location = '';
	if ( has_nav_menu( 'penci-amp-sidebar-nav' ) ) :
		$theme_location = 'penci-amp-sidebar-nav';
	elseif( has_nav_menu( 'main-menu' ) ):
		$theme_location = 'main-menu';
	endif;

	if( $theme_location ) {
		wp_nav_menu( array(
			'theme_location' => $theme_location,
			'container'      => '',
			'items_wrap'     => '<nav id="%1$s" itemscope itemtype="http://schema.org/SiteNavigationElement" class="mobile-navigation %2$s">%3$s</nav>',
			'fallback_cb'    => 'penci_amp_menu_fallback',
			'walker'         => class_exists( 'Penci_AMP_Menu_Walker' ) ? new Penci_AMP_Menu_Walker() : '',
			'menu_id'        => 'primary-menu-mobile',
			'menu_class'     => 'primary-menu-mobile penci-amp-menu',
		) );
	}
	?>
</amp-sidebar>

