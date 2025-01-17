<?php
/**
 * Comments template
 *
 * @package Wordpress
 * @since 1.0
 */

// Get numbers comments
$comment_numbers = get_comments_number();
?>
<div class="post-comments<?php if( $comment_numbers == 0 ): echo ' no-comment-yet'; endif;?>" id="comments">
	<?php
	if ( have_comments() ) :
		echo '<div class="post-title-box"><h4 class="post-box-title">';
		comments_number( '0 ' . penci_get_setting( 'penci_trans_comment' ), '1 '. penci_get_setting( 'penci_trans_comment' ), '% ' . penci_get_setting( 'penci_trans_comments' ) );
		echo '</h4></div>';

		echo "<div class='comments'>";
		wp_list_comments( array(
			'avatar_size' => 100,
			'max_depth'   => 5,
			'style'       => 'div',
			'callback'    => 'penci_amp_comments_template',
			'type'        => 'all'
		) );
		echo "</div>";

	echo "<div id='comments_pagination'>";
	paginate_comments_links( array( 'prev_text' => '&laquo;', 'next_text' => '&raquo;' ) );
	echo "</div>";

	endif;

	// If comments are closed and there are comments, let's leave a little note.
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="no-comments"><?php echo penci_get_setting( 'penci_trans_comments_closed' ); ?></p>
	<?php endif; ?>
	<?php
	$comment_link = Penci_AMP_Link_Sanitizer::__pre_url_off ( get_the_permalink() ) . '#respond';
	if( get_theme_mod( 'penci_amp_mobile_version' ) ){
		$comment_link = add_query_arg( array(
			'desktop_view' => 'show',
		), $comment_link );
	}
	?>
	<a href="<?php echo esc_url( $comment_link ); ?>" class="button add-comment"><?php echo penci_amp_get_setting( 'penci_amp_add_comment' ); ?></a>
</div> <!-- end comments div -->
