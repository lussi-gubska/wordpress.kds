<?php
$_site_url = site_url( '/amp/' );

$use_site_address_url = get_theme_mod( 'penci_amp_use_site_address_url' );
if ( $use_site_address_url ) {
	$_site_url = home_url( '/amp/' );
}
?>
<header id="#top" itemscope itemtype="https://schema.org/WPHeader" class="site-header penci-amp-wp-header">
	<div>
		<button class="fa fa-bars navbar-toggle" on="tap:penci_sidebar.toggle"></button>
		<?php echo penci_amp_default_theme_logo( ); ?>
		<a href="<?php echo esc_url( add_query_arg( 's', '', $_site_url ) ); ?>" class="navbar-search"><i class="fa fa-search" aria-hidden="true"></i></a>
	</div>
</header>