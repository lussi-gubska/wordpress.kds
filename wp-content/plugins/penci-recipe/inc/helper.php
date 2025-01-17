<?php
/**
 * Callback function for sanitizing checkbox settings.
 * Use for PenciDesign
 *
 * @param $input
 *
 * @return string default value if type is not exists
 */
function penci_recipe_sanitize_checkbox_field( $input ) {
	if ( $input == 1 ) {
		return true;
	}
	else {
		return false;
	}
}

if ( ! function_exists( 'penci_recipe_html_field' ) ) {
	function penci_recipe_html_field( $input ) {
		return $input;
	}
}

/**
 * Customize colors
 * @since 3.0
 */
function penci_recipe_customizer_css() {
	?>
	<style type="text/css">
		<?php if(get_theme_mod( 'penci_recipe_ratio_fimage' )) : ?>.precipe-style-2 .penci-recipe-thumb{ padding-bottom:<?php echo get_theme_mod( 'penci_recipe_ratio_fimage' ); ?>%; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_color_accent' )) : ?>.penci-recipe-tagged .prt-icon span, .penci-recipe-action-buttons .penci-recipe-button:hover{ background-color:<?php echo get_theme_mod( 'penci_color_accent' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_btn_fsize' )) : ?>.penci-recipe-action-buttons .penci-recipe-button{ font-size:<?php echo get_theme_mod( 'penci_recipe_btn_fsize' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_title_fsize' )) : ?>.post-entry .penci-recipe-heading .recipe-title-nooverlay, .post-entry .penci-recipe-heading .recipe-title-overlay{ font-size:<?php echo get_theme_mod( 'penci_recipe_title_fsize' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_title_mfsize' )) : ?>@media only screen and (max-width: 479px){.post-entry .penci-recipe-heading .recipe-title-nooverlay, .post-entry .penci-recipe-heading .recipe-title-overlay{ font-size:<?php echo get_theme_mod( 'penci_recipe_title_mfsize' ); ?>px; } }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_fsize' )) : ?>.penci-recipe-heading .penci-recipe-meta, .penci-recipe-rating{ font-size:<?php echo get_theme_mod( 'penci_recipe_meta_fsize' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_ifsize' )) : ?>.precipe-style-2 .penci-recipe-heading .penci-recipe-meta span i, .precipe-style-2 .penci-ficon.ficon-fire, .penci-recipe-heading .penci-recipe-meta span i, .penci-ficon.ficon-fire{ font-size:<?php echo get_theme_mod( 'penci_recipe_meta_ifsize' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta2_fsize' )) : ?>.precipe-style-2 .penci-recipe-heading .penci-recipe-meta span.remeta-item{ font-size:<?php echo get_theme_mod( 'penci_recipe_meta2_fsize' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_headings' )) : ?>.post-entry .penci-recipe-title{ font-size:<?php echo get_theme_mod( 'penci_recipe_headings' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_makethis_head' )) : ?>.penci-recipe-tagged .prt-span-heading{ font-size:<?php echo get_theme_mod( 'penci_recipe_makethis_head' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_makethis_desc' )) : ?>.penci-recipe-tagged .prt-span-des, .penci-recipe-tagged .prt-span-des *{ font-size:<?php echo get_theme_mod( 'penci_recipe_makethis_desc' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_upperheadings' )) : ?>.post-entry .penci-recipe-title{ text-transform:none; letter-spacing: 0; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipei_title' )) : ?>.penci-recipe-index-wrap h4.recipe-index-heading{ font-size:<?php echo get_theme_mod( 'penci_recipei_title' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipei_mtitle' )) : ?>@media only screen and (max-width: 479px){.penci-recipe-index-wrap h4.recipe-index-heading{ font-size:<?php echo get_theme_mod( 'penci_recipei_mtitle' ); ?>px; } }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipei_cat' )) : ?>.penci-recipe-index .cat > a.penci-cat-name{ font-size:<?php echo get_theme_mod( 'penci_recipei_cat' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipei_ptitle' )) : ?>.penci-recipe-index-wrap .penci-recipe-index-title a{ font-size:<?php echo get_theme_mod( 'penci_recipei_ptitle' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipei_pmtitle' )) : ?>@media only screen and (max-width: 479px){.penci-recipe-index-wrap .penci-recipe-index-title a{ font-size:<?php echo get_theme_mod( 'penci_recipei_pmtitle' ); ?>px; } }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipei_date' )) : ?>.penci-recipe-index .date{ font-size:<?php echo get_theme_mod( 'penci_recipei_date' ); ?>px; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_btn_bg' )) : ?>.penci-recipe-action-buttons .penci-recipe-button{ background-color:<?php echo get_theme_mod( 'penci_recipe_btn_bg' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_btn_color' )) : ?>.penci-recipe-action-buttons .penci-recipe-button{ color:<?php echo get_theme_mod( 'penci_recipe_btn_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_btn_hbg' )) : ?>.penci-recipe-action-buttons .penci-recipe-button:hover{ background-color:<?php echo get_theme_mod( 'penci_recipe_btn_hbg' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_btn_hcolor' )) : ?>.penci-recipe-action-buttons .penci-recipe-button:hover{ color:<?php echo get_theme_mod( 'penci_recipe_btn_hcolor' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_border_color' )) : ?>.wrapper-penci-recipe .penci-recipe, .wrapper-penci-recipe .penci-recipe-heading, .wrapper-penci-recipe .penci-recipe-ingredients, .wrapper-penci-recipe .penci-recipe-notes { border-color:<?php echo get_theme_mod( 'penci_recipe_border_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_border_4color' )) : ?>.precipe-style-4, .precipe-style-4 .penci-recipe-thumb .recipe-thumb-top{ border-color:<?php echo get_theme_mod( 'penci_recipe_border_4color' ); ?> !important; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_title_color' )) : ?>.post-entry .penci-recipe-heading .recipe-title-nooverlay { color:<?php echo get_theme_mod( 'penci_recipe_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_title_ocolor' )) : ?>.post-entry .precipe-style-4 .penci-recipe-heading .recipe-title-nooverlay, .post-entry .penci-recipe-heading .recipe-title-overlay{ color:<?php echo get_theme_mod( 'penci_recipe_title_ocolor' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_header4bg' )) : ?>.precipe-style-4 .penci-recipe-heading{ background-color:<?php echo get_theme_mod( 'penci_recipe_header4bg' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_print_button' )) : ?>.post-entry .penci-recipe-heading a.penci-recipe-print { color:<?php echo get_theme_mod( 'penci_recipe_print_button' ); ?>; } .post-entry .penci-recipe-heading a.penci-recipe-print { border-color:<?php echo get_theme_mod( 'penci_recipe_print_button' ); ?>; } .post-entry .penci-recipe-heading a.penci-recipe-print:hover, .wrapper-buttons-style4 .penci-recipe-print-btn, .wrapper-buttons-overlay .penci-recipe-print-btn { background-color:<?php echo get_theme_mod( 'penci_recipe_print_button' ); ?>; border-color:<?php echo get_theme_mod( 'penci_recipe_print_button' ); ?>; } .post-entry .penci-recipe-heading a.penci-recipe-print:hover { color:#fff; }.wrapper-buttons-overlay .penci-recipe-print-btn{ -webkit-box-shadow: 0 5px 20px <?php echo get_theme_mod( 'penci_recipe_print_button' ); ?>; box-shadow: 0 5px 20px <?php echo get_theme_mod( 'penci_recipe_print_button' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_icon_color' )) : ?>.penci-recipe-heading .penci-recipe-meta span i, .penci-ficon.ficon-fire { color:<?php echo get_theme_mod( 'penci_recipe_meta_icon_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_border4' )) : ?>.post-entry .precipe-style-4 .penci-recipe-heading .recipe-title-nooverlay:after { background-color:<?php echo get_theme_mod( 'penci_recipe_meta_border4' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_icon_4color' )) : ?>.precipe-style-4 .penci-recipe-heading .penci-recipe-meta span i, .precipe-style-4 .penci-ficon.ficon-fire { color:<?php echo get_theme_mod( 'penci_recipe_meta_icon_4color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_color' )) : ?>.penci-recipe-heading .penci-recipe-meta, .penci-recipe-rating{ color:<?php echo get_theme_mod( 'penci_recipe_meta_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_4color' )) : ?>.precipe-style-4 .penci-recipe-heading .penci-recipe-meta, .precipe-style-4 .penci-recipe-rating{ color:<?php echo get_theme_mod( 'penci_recipe_meta_4color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_2color' )) : ?>.precipe-style-2 .penci-recipe-heading .penci-recipe-meta span.remeta-item{ color:<?php echo get_theme_mod( 'penci_recipe_meta_2color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_meta_bcolor' )) : ?>.precipe-style-2 .penci-recipe-heading .penci-recipe-meta, .precipe-style-2 .penci-recipe-heading .penci-recipe-meta > span{ border-color:<?php echo get_theme_mod( 'penci_recipe_meta_bcolor' ); ?>; }@media only screen and (max-width: 767px){ .precipe-style-2 .penci-recipe-heading .penci-recipe-meta > span:nth-of-type(1), .precipe-style-2 .penci-recipe-heading .penci-recipe-meta > span:nth-of-type(2){ border-color:<?php echo get_theme_mod( 'penci_recipe_meta_bcolor' ); ?>; } }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_section_title_color' )) : ?>.post-entry .penci-recipe-title { color:<?php echo get_theme_mod( 'penci_recipe_section_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_note_title_color' )) : ?>.post-entry .penci-recipe-notes .penci-recipe-title { color:<?php echo get_theme_mod( 'penci_recipe_note_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_note_color' )) : ?>.post-entry .penci-recipe-notes, .post-entry .penci-recipe-notes p { color:<?php echo get_theme_mod( 'penci_recipe_note_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_makethis_bg' )) : ?>.penci-recipe-tagged { background-color:<?php echo get_theme_mod( 'penci_recipe_makethis_bg' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_makethis_inbg' )) : ?>.penci-recipe-tagged .prt-icon span { background-color:<?php echo get_theme_mod( 'penci_recipe_makethis_inbg' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_makethis_in' )) : ?>.penci-recipe-tagged .prt-icon span { color:<?php echo get_theme_mod( 'penci_recipe_makethis_in' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_makethis_chead' )) : ?>.penci-recipe-tagged .prt-span-heading{ color:<?php echo get_theme_mod( 'penci_recipe_makethis_chead' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_makethis_cdesc' )) : ?>.penci-recipe-tagged .prt-span-des, .penci-recipe-tagged .prt-span-des * { color:<?php echo get_theme_mod( 'penci_recipe_makethis_cdesc' ); ?> !important; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_title_color' )) : ?>.penci-recipe-index-wrap h4.recipe-index-heading { color:<?php echo get_theme_mod( 'penci_recipe_index_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_cat_color' )) : ?>.penci-recipe-index .cat > a.penci-cat-name { color:<?php echo get_theme_mod( 'penci_recipe_index_cat_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_post_title_color' )) : ?>.penci-recipe-index-wrap .penci-recipe-index-title a { color:<?php echo get_theme_mod( 'penci_recipe_index_post_title_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_post_date_color' )) : ?>.penci-recipe-index .date { color:<?php echo get_theme_mod( 'penci_recipe_index_post_date_color' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_button_bg' )) : ?>.penci-recipe-index-wrap .penci-index-more-link a { background-color:<?php echo get_theme_mod( 'penci_recipe_index_button_bg' ); ?>; }<?php endif; ?>
		<?php if(get_theme_mod( 'penci_recipe_index_button_color' )) : ?>.penci-recipe-index-wrap .penci-index-more-link a { color:<?php echo get_theme_mod( 'penci_recipe_index_button_color' ); ?>; }<?php endif; ?>
	</style>
	<?php
}
add_action( 'wp_head', 'penci_recipe_customizer_css', 15 );
