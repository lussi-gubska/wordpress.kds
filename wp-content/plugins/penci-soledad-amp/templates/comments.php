<div class="penci-amp-comment">
<?php
if ( ! get_theme_mod( 'penci_post_hide_comments' ) && ( comments_open() || get_comments_number() ) ) :
	comments_template();
endif;
?>
</div>
