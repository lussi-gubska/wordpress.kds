<?php

/**
 * Follow Terms Listing
 *
 **/

if ( ! function_exists( 'penci_get_setting' ) ) {
	return false;
}

global $penci_bl_model, $penci_bl_options;

//model class
$model = $penci_bl_model;

$prefix = PENCI_BL_META_PREFIX;

$page = isset( $_POST['paging'] ) ? $_POST['paging'] : '1';

// get all custom post types
$post_types = get_post_types( array( 'public' => true ), 'objects' );
?>
    <div class="penci-smalllist pcsl-wrapper pwsl-id-default">


        <div class="pcsl-inner penci-clearfix pcsl-grid pencipw-hd-text pcsl-imgpos-left pcsl-col-3 pcsl-tabcol-2 pcsl-mobcol-1">

			<?php
			foreach ( $followterms as $followterm ) {

				$termid = isset( $followterm['post_parent'] ) && ! empty( $followterm['post_parent'] ) ? $followterm['post_parent'] : '';

				if ( ! is_user_logged_in() ) {
					$termid           = $followterm;
					$followterm       = [];
					$followterm['ID'] = '';

				}

				if ( ! empty( $termid ) ) { // Check post parent is not empty

					// Get Follow Post Type
					$posttype = get_post_meta( $followterm['ID'], $prefix . 'post_type', true );

					// Get Follow Post Type Name
					$post_type_name = ! empty( $posttype ) && isset( $post_types[ $posttype ]->labels->singular_name ) ? $post_types[ $posttype ]->labels->singular_name : '';

					// Get Follow Taxonomy
					$taxonomy = get_post_meta( $followterm['ID'], $prefix . 'taxonomy_slug', true );

					if ( ! is_user_logged_in() ) {
						$taxonomy = get_term( $termid )->taxonomy;
					}

					// Get Follow Taxonomy Name
					$taxonomy_data = get_taxonomy( $taxonomy );
					$taxonomy_name = ! empty( $taxonomy_data ) && isset( $taxonomy_data->labels->singular_name ) ? $taxonomy_data->labels->singular_name : '';

					// Get Follow Term Name
					$term_data = get_term_by( 'id', $termid, $taxonomy );

					if ( ! empty( $term_data ) ) {


						$term_name = isset( $term_data->name ) ? $term_data->name : '';

						if ( is_user_logged_in() ) {
							// Get Follow Date
							$followdate = $model->penci_bl_get_date_format( $followterm['post_date'] );
						}


						?>


                        <div class="pcsl-item">
                            <div class="pcsl-itemin">
								<?php
								//do action to add row for orders list before
								do_action( 'penci_bl_follow_term_row_before', $followterm['ID'] );
								?>

                                <div class="pcsl-iteminer">
                                    <div class="pcsl-thumb">
										<?php
										$args = array(
											'follow_posttype' => $posttype,
											'follow_taxonomy' => $taxonomy,
											'follow_term_id'  => $termid,
											'current_post_id' => $followterm['ID'],
											'follow_message'  => '',
											'html_class'      => 'pcbf-size-small',
										);
										do_action( 'penci_bl_follow_term', $args );
										?>
                                        <a title="<?php echo wp_strip_all_tags( $term_name ); ?>"
                                           href="<?php echo get_term_link( $term_data ); ?>"
                                           class="penci-image-holder"
                                           style="background-image: url('<?php echo penci_bl_get_term_thumb_url( $term_data->term_id, 'penci-thumb' ) ?>');"></a>
                                    </div>
                                    <div class="pcsl-content">

                                        <div class="pcsl-title">
                                            <a href="<?php echo get_term_link( $term_data ); ?>"><?php echo $term_name; ?></a>
                                        </div>


                                        <div class="grid-post-box-meta pcsl-meta">

									

									<span class="sl-postcount">
									<?php
									$count       = $term_data->count;
									$text_prefix = is_numeric( $count ) && $count == 1 ? penci_get_setting( 'penci_trans_post' ) : penci_get_setting( 'penci_trans_posts' );
									echo $count . ' ' . $text_prefix;
									?>
									</span>
                                        </div>


                                    </div>
                                </div>
								<?php
								//do action to add row for orders list after
								do_action( 'penci_bl_follow_term_row_after', $followterm['ID'] );
								?>
                            </div>
                        </div>


					<?php }
				}
			} ?>

        </div>
    </div>

<?php if ( $paging && $paging->is_render() ): ?>

    <div class="penci-bf-paging penci-bf-follow-terms-paging">
        <div id="penci-bf-tablenav-pages" class="penci-bf-tablenav-pages">
			<?php echo $paging->getOutput( 'term' ); ?>
        </div><!--.penci-bf-tablenav-pages-->
    </div><!--.penci-bf-paging-->

<?php endif; ?>

<?php
$page_link = get_theme_mod( 'pencibf_custom_cat_page' );

if ( $page_link ) { ?>

    <div class="penci-bf-term-page">
		<?php
		echo sprintf( pencibf_get_text( 'term_follow' ), get_permalink( $page_link ) );
		?>
    </div>

<?php }