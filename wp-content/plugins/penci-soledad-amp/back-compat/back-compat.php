<?php

// If you want to use the template that shipped with v0.3 and earlier, you can use this to force that.
// Note that this may not stick around forever, so use caution and `function_exists`.
function penci_amp_backcompat_use_v03_templates() {
	add_filter( 'penci_amp_customizer_is_enabled', '__return_false' );
	add_filter( 'penci_amp_post_template_dir', '_penci_amp_backcompat_use_v03_templates_callback', 0 ); // early in case there are other overrides
}

function _penci_amp_backcompat_use_v03_templates_callback( $templates ) {
	return PENCI_AMP_DIR. '/back-compat/templates-v0-3';
}
