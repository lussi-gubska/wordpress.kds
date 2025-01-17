<?php
$user = wp_get_current_user();
if ( get_theme_mod( 'penci_frontend_submit_enable_woocommerce', false ) && ! in_array( 'administrator', (array) $user->roles ) ) {
	$post_remaining = get_user_meta( get_current_user_id(), 'listing_left', true );
	$remaining_text = $post_remaining >= 1024 ? penci_ftsub_get_text( 'unlimited' ) : (string) $post_remaining;
	if ( $remaining_text ) :
		?>
        <div class="penci_post_quota">
            <span><strong><?php echo penci_ftsub_get_text( 'rquota' ); ?> : </strong><?php echo $remaining_text ?></span>
        </div>
	<?php
	endif;
}
$post_type = get_theme_mod( 'penci_frontend_submit_enabled_post_types', 'post' );
$order     = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc';
$args      = array(
	'post_type'           => $post_type,
	'author__in'          => get_current_user_id(),
	'orderby'             => 'date',
	'order'               => $order,
	'paged'               => $paged,
	'post_status'         => 'any',
	'ignore_sticky_posts' => 1,
);
$posts     = new WP_Query( $args );

if ( $posts->have_posts() ) {
	$posts_per_page = $posts->query_vars['posts_per_page'];
	$total_post     = $posts->found_posts;

	$fpost = $posts_per_page * ( $paged - 1 );
	$lpost = $posts_per_page * $paged;

	$fpost = ( $fpost <= 0 ) ? 1 : $fpost;
	$lpost = ( $lpost > $total_post ) ? $total_post : $lpost;
	?>

    <div class="penci_account_posts">
        <div class="penci_post_list_meta">
            <div class="penci_post_list_count">
                <span><?php echo sprintf( penci_ftsub_get_text( 'showing_result' ), $fpost, $lpost, $total_post ) ?></span>
            </div>
            <div class="penci_post_list_filter">
                <input type="hidden" name="current-page-url"
                       value="<?php echo esc_url( penci_home_url_multilang( '/' . $account_slug . '/' . $posts_slug ) ); ?>">
                <select name="post-list-filter">
                    <option <?php echo ( $order === 'desc' ) ? esc_attr( 'selected' ) : ''; ?>
                            value="desc"><?php echo penci_ftsub_get_text( 'sort_latest' ); ?></option>
                    <option <?php echo ( $order === 'asc' ) ? esc_attr( 'selected' ) : ''; ?>
                            value="asc"><?php echo penci_ftsub_get_text( 'sort_older' ); ?></option>
                </select>
            </div>
        </div>
        <div class="penci-smalllist">
            <div class="pcsl-inner penci-clearfix pcsl-grid pcsl-imgpos-left pcsl-col-1 pcsl-tabcol-2 pcsl-mobcol-1">
				<?php

				while ( $posts->have_posts() ) :
					$posts->the_post();

					$post_id     = get_the_ID();
					$post_status = get_post_status_object( get_post_status( $post_id ) )->label;

					?>
                    <article <?php post_class( 'pcsl-item' ); ?>>
                        <div class="pcsl-itemin">
                            <div class="pcsl-iteminer">
                                <div class="pcsl-thumb">
                                    <a href="<?php the_permalink(); ?>"
                                       title="<?php echo wp_strip_all_tags( get_the_title() ); ?>"
                                       class="penci-image-holder penci-lazy"
                                       data-bgset="<?php echo penci_get_featured_image_size( get_the_ID(), 'penci-thumb' ); ?>">
                                    </a>
                                </div>
                                <div class="pcsl-content">
                                    <div class="pcsl-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </div>
                                    <div class="grid-post-box-meta pcsl-meta">
                                        <span class="penci_post_status <?php echo esc_attr( $post_status ) ?>"><?php echo esc_html( $post_status ); ?></span>
                                        <span class="sl-date"><?php penci_soledad_time_link(); ?></span>
                                        <span><a class="penci_post_action edit"
                                                 href="<?php echo penci_home_url_multilang( '/' . $this->endpoint['editor']['slug'] . '/' . $post_id ); ?>"><?php echo penci_ftsub_get_text( 'epost' ); ?></a></span>
                                        <span><a class="penci_post_action edit"
                                                 href="<?php the_permalink() ?>"><?php echo penci_ftsub_get_text( 'vpost' ); ?></a></span>
                                    </div>
                                    <div class="grid-post-box-meta pcsl-action">
                                        <span><a data-id="<?php echo get_the_ID();?>" class="penci_post_action deleted"
                                                 href="<?php the_permalink() ?>"><?php echo penci_ftsub_get_text( 'deleted' ); ?></a></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
				<?php
				endwhile;

				?>
            </div>
        </div>
    </div>

	<?php

	// pagination
	$big        = 999999999; // need an unlikely integer
	$pagination = paginate_links( array(
		'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format'    => '?paged=%#%',
		'current'   => $paged,
		'total'     => $posts->max_num_pages,
		'type'      => 'list',
		'prev_text' => penci_icon_by_ver( 'fas fa-angle-left' ),
		'next_text' => penci_icon_by_ver( 'fas fa-angle-right' ),
	) );
	if ( $pagination ) {
		echo '<div class="penci-pagination">' . $pagination . '</div>';
	}

} else {
	echo "<div class='penci_empty_module'>" . penci_ftsub_get_text( 'no_content' ) . "</div>";
}

wp_reset_postdata();
?>