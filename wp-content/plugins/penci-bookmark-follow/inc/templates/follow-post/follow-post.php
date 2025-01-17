<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$class = isset( $args['display_type'] ) && $args['display_type'] ? $args['display_type'] : 'normal';
if ( isset( $args['follow_pos_class'] ) && $args['follow_pos_class'] ) {
	$class .= ' pcbf-size-' . $args['follow_pos_class'];
}
?>

<span class="penci-bf-follow-post-wrapper penci-bf-follow-btn-wrapper penci-pf-display-<?php echo esc_attr( $class ); ?>">
		<?php
		do_action( 'penci_bl_follow_post_content', $args );
		?>
	</span><!-- penci-bf-follow-post-wrapper -->