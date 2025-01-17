<form role="search" class="penci-search-form" action="<?php echo esc_url( penci_amp_get_site_url() ) ?>/" target="_top">
	<div class="search-label"><?php echo penci_amp_get_setting( 'penci_amp_search_on_site' ); ?></div>
	<div class="search-input-submit">
		<input type="search" name="s" class="search-field" id="s" placeholder="<?php echo penci_amp_get_setting( 'penci_amp_search_input_placeholder' ); ?>" value="<?php the_search_query() ?>" />
		<input type="submit" class="search-submit button" value="<?php echo penci_amp_get_setting( 'penci_amp_search_button' ); ?>"/>
	</div>
</form>