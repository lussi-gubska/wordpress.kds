<?php
add_filter( 'theme_mod_penci_featured_slider', '__return_false');
get_header();
$template   = \PenciFrontendSubmission\AccountPage::getInstance();
$post_id    = $template->get_post_id();
$categories = $template->get_category();
$post_tags  = $template->get_tag();
$post       = penci_get_post_data( $post_id );
$post_type  = get_theme_mod( 'penci_frontend_submit_enabled_post_types', 'post' );
?>

    <div class="container container-single-page container-default-page penci_sidebar right-sidebar pcft-posteditor">
        <div class="archive-box">
            <div class="title-bar">
                <h1 class="page-title"><?php echo penci_ftsub_get_text( 'epost' ); ?></h1>
				<?php echo apply_filters( 'penci_get_message', '' ); ?>
            </div>
        </div>
        <form method="post" action="">
            <div id="main" class="penci-main-single-page-default penci-main-sticky-sidebar">
                <div class="theiaStickySidebar">
                    <div class="pcac_page_container full">
                        <!-- post title -->
                        <div class="title-field form-group">
                            <input id="title" name="title"
                                   placeholder="<?php echo penci_ftsub_get_text( 'title' ); ?>"
                                   type="text" class="form-control"
                                   value="<?php echo isset( $post['title'] ) ? $post['title'] : ''; ?>">
                        </div>

						<?php if ( 'post' == $post_type ): ?>
                            <!-- post subtitle -->
                            <div class="subtitle-field form-group">
                                <input id="subtitle" name="subtitle"
                                       placeholder="<?php echo penci_ftsub_get_text( 'stitle' ); ?>"
                                       type="text" class="form-control"
                                       value="<?php echo isset( $post['subtitle'] ) ? $post['subtitle'] : ''; ?>">
                            </div>
						<?php endif; ?>

                        <!-- post content -->
                        <div class="content-field form-group">
							<?php
							do_action( 'penci_frontend_submit_insert_after_subtitle', array() );
							?>
                            <label for="content"><?php echo penci_ftsub_get_text( 'content' ); ?></label>
                            <br>
							<?php

							echo apply_filters( 'penci_frontend_submit_enable_add_media_msg', '' );

							$wp_editor_args = array(
								'textarea_name'    => 'content',
								'drag_drop_upload' => false,
								'media_buttons'    => get_theme_mod( 'penci_frontend_submit_enable_add_media', true ),
								'textarea_rows'    => 25,
								'teeny'            => false,
								'quicktags'        => true
							);

							$post_content = isset( $post['content'] ) ? str_replace( '\"', '', $post['content'] ) : '';
							$post_content = apply_filters( 'content', $post_content );

							wp_editor( $post_content, 'content', apply_filters( 'penci_frontend_submit_wp_editor_args', $wp_editor_args ) );
							
							$custom_acf = get_theme_mod( 'penci_frontend_submit_acf_groups' );
							if ( $custom_acf && function_exists( 'acf_get_fields' ) ) {
								$_fields = acf_get_fields( $custom_acf );
								$fields = [];
								if ( $_fields ) {
									foreach ( $_fields as $_field ) {
										$fields[] = $_field;
									}
								}
								?>
								<div class="acf-fields acf-form-fields">
								<?php
								acf_render_fields( $fields, $post_id );
								?>
								</div>
							<?php } ?>
							
                        </div>

                        <!-- submit button -->
                        <div class="submit-field form-group">

							<?php if ( ! apply_filters( 'penci_disable_frontend_submit_post', false ) ): ?>
                                <input type="hidden" name="penci-action" value="edit-post"/>
                                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post['id'] ); ?>"/>
                                <input type="hidden" name="penci-editor-nonce"
                                       value="<?php echo esc_attr( wp_create_nonce( 'penci-editor' ) ); ?>"/>
                                <input type="submit"
                                       value="<?php echo penci_ftsub_get_text( 'upost' ); ?>"/>
							<?php else: ?>
								<?php echo apply_filters( 'penci_disable_frontend_submit_post_msg', '' ); ?>
							<?php endif ?>
                        </div>
                    </div>
					<?php do_action( 'penci_after_main' ); ?>
                </div>
            </div>
            <div id="sidebar" class="penci-sidebar-right penci-sidebar-content">
                <div class="theiaStickySidebar">
                    <!-- post format -->
                    <div class="format-field form-group">
                        <ul class="penci-post-tablist format-nav">
                            <li>
                                <a data-type="image" href="#"
                                   class="<?php if ( isset( $post['format'] ) ) {
									   if ( $post['format'] == 'image' ) {
										   echo 'active';
									   }
								   } else {
									   echo 'active';
								   } ?>"><?php echo penci_ftsub_get_text( 'standard' ); ?></a>
                            </li>
							<?php if ( post_type_supports( $post_type, 'post-formats' ) ): ?>
                                <li>
                                    <a data-type="gallery" href="#"
                                       class="<?php if ( isset( $post['format'] ) ) {
										   if ( $post['format'] == 'gallery' ) {
											   echo 'active';
										   }
									   } ?>"><?php echo penci_ftsub_get_text( 'gallery' ); ?></a>
                                </li>
                                <li>
                                    <a data-type="video" href="#"
                                       class="<?php if ( isset( $post['format'] ) ) {
										   if ( $post['format'] == 'video' ) {
											   echo 'active';
										   }
									   } ?>"><?php echo penci_ftsub_get_text( 'video' ); ?></a>
                                </li>
                                <li>
                                    <a data-type="audio" href="#"
                                       class="<?php if ( isset( $post['format'] ) ) {
										   if ( $post['format'] == 'audio' ) {
											   echo 'active';
										   }
									   } ?>"><?php echo penci_ftsub_get_text( 'audio' ); ?></a>
                                </li>
							<?php endif; ?>
                        </ul>
                        <div class="form-input-wrapper">
                            <!-- post format -->
                            <input type="hidden" name="format" value="image">

                            <!-- image format -->
							<?php
							$fimage = '';
							if ( isset( $post['format'] ) ) {
								if ( $post['format'] == 'image' ) {
									$fimage = 'active';
								}
							} else {
								$fimage = 'active';
							}
							load_template( get_template_directory() . '/inc/templates/upload_form.php', false, array(
								'id'      => 'featured_image',
								'class'   => $fimage,
								'name'    => 'image',
								'source'  => isset( $post['image'] ) ? [ $post['image'] ] : null,
								'button'  => 'btn-single-image',
								'multi'   => false,
								'maxsize' => apply_filters( 'penci_maxsize_upload_featured_image', '2mb' )
							) );
							?>

                            <!-- video format -->
                            <textarea id="video" name="video"
                                      placeholder="<?php echo penci_ftsub_get_text( 'insert_embed' ); ?>"
                                      class="form-control form-control-video <?php if ( isset( $_POST['format'] ) ) {
								          if ( $_POST['format'] == 'video' ) {
									          echo 'active';
								          }
							          } ?>"><?php echo isset( $post['video'] ) ? $post['video'] : ''; ?></textarea>

                            <!-- audio format -->
                            <textarea id="audio" name="audio"
                                      placeholder="<?php echo penci_ftsub_get_text( 'insert_embed' ); ?>"
                                      class="form-control form-control-audio <?php if ( isset( $_POST['format'] ) ) {
								          if ( $_POST['format'] == 'audio' ) {
									          echo 'active';
								          }
							          } ?>"><?php echo isset( $post['audio'] ) ? $post['audio'] : ''; ?></textarea>

                            <!-- gallery format -->
							<?php
							$fgallery = '';
							if ( isset( $post['format'] ) ) {
								if ( $post['format'] == 'gallery' ) {
									$fgallery = 'active';
								}
							}
							load_template( get_template_directory() . '/inc/templates/upload_form.php', false, array(
								'id'       => 'featured_image_gallery',
								'class'    => $fgallery,
								'name'     => 'gallery',
								'source'   => isset( $post['gallery'] ) ? $post['gallery'] : null,
								'button'   => 'btn-multi-image',
								'multi'    => true,
								'heading'  => penci_ftsub_get_text( 'cgagllery' ),
								'maxsize'  => apply_filters( 'penci_maxsize_upload_featured_gallery', '2mb' ),
								'maxcount' => apply_filters( 'penci_maxcount_upload_featured_gallery', 8 )
							) );
							load_template( get_template_directory() . '/inc/templates/upload_form.php', false, array(
								'id'       => 'single_featured_image_gallery',
								'class'    => $fgallery,
								'name'     => 'gallery_featured_image',
								'source'   => isset( $post['image'] ) ? [ $post['image'] ] : null,
								'button'   => 'btn-single-image-gallery',
								'wrapper'  => 'upload_preview_container_gallery_single',
								'multi'    => false,
								'maxsize'  => apply_filters( 'penci_maxsize_upload_featured_gallery', '2mb' ),
								'maxcount' => apply_filters( 'penci_maxcount_upload_featured_gallery', 8 ),
								'heading'  => penci_ftsub_get_text( 'cfimages' ),
							) );
							?>
                        </div>
                    </div>

					<?php if ( 'post' != $post_type ):
						$taxonomies = get_taxonomies( [ 'object_type' => [ $post_type ] ] );
						foreach ( $taxonomies as $taxonomy ) {
							$taxonomy_details = get_taxonomy( $taxonomy );
							$taxonomy_terms   = $template->get_terms( $taxonomy );
							?>
                            <div class="category-field form-group">
                                <label for="taxonomies[<?php echo $taxonomy; ?>]"><?php echo $taxonomy_details->label; ?></label>

								<?php
								$data       = array();
								$value      = isset( $post['taxonomies'][ $taxonomy ] ) ? $post['taxonomies'][ $taxonomy ] : '';
								$ajax_class = '';
								if ( empty( $taxonomy_terms ) ) {
									$values     = explode( ',', $value );
									$ajax_class = 'penci-ajax-load';

									foreach ( $values as $val ) {
										if ( ! empty( $val ) ) {
											$term   = get_term( $val, $taxonomy );
											$data[] = array(
												'value' => $val,
												'text'  => $term->name,
											);
										}
									}
								} else {
									foreach ( $taxonomy_terms as $key => $label ) {
										$data[] = array(
											'value' => $key,
											'text'  => $label,
										);
									}
								}

								$data = wp_json_encode( $data );
								?>

                                <input name="taxonomies[<?php echo $taxonomy; ?>]"
                                       placeholder="<?php echo $taxonomy_details->label; ?>"
                                       type="text"
                                       class="multicategory-field form-control <?php esc_attr_e( $ajax_class ); ?>"
                                       value="<?php esc_attr_e( $value ); ?>">
                                <div class="data-option" style="display: none;">
									<?php echo esc_html( $data ); ?>
                                </div>
                            </div>
							<?php
						}
					endif; ?>

					<?php if ( 'post' == $post_type ): ?>

                        <!-- post category -->
                        <div class="category-field form-group">
                            <label for="category"><?php echo penci_ftsub_get_text( 'cat' ); ?></label>

							<?php
							$data       = array();
							$value      = isset( $post['category'] ) ? $post['category'] : '';
							$ajax_class = '';
							if ( empty( $categories ) ) {
								$values     = explode( ',', $value );
								$ajax_class = 'penci-ajax-load';

								foreach ( $values as $val ) {
									if ( ! empty( $val ) ) {
										$term   = get_term( $val, 'category' );
										$data[] = array(
											'value' => $val,
											'text'  => $term->name,
										);
									}
								}
							} else {
								foreach ( $categories as $key => $label ) {
									$data[] = array(
										'value' => $key,
										'text'  => $label,
									);
								}
							}

							$data = wp_json_encode( $data );
							?>

                            <input name="category"
                                   placeholder="<?php echo penci_ftsub_get_text( 'ccat' ); ?>"
                                   type="text"
                                   class="multicategory-field form-control <?php esc_attr_e( $ajax_class ); ?>"
                                   value="<?php esc_attr_e( $value ); ?>">
                            <div class="data-option" style="display: none;">
								<?php echo esc_html( $data ); ?>
                            </div>
                        </div>

                        <!-- post tag -->
                        <div class="tags-field form-group">
                            <label for="tags"><?php echo penci_ftsub_get_text( 'tag' ); ?></label>

							<?php
							$data       = array();
							$value      = isset( $post['tag'] ) ? $post['tag'] : '';
							$ajax_class = '';

							if ( empty( $post_tags ) ) {
								$values     = explode( ',', $value );
								$ajax_class = 'penci-ajax-load';

								foreach ( $values as $val ) {
									if ( ! empty( $val ) ) {
										$term   = get_term( $val, 'post_tag' );
										$data[] = array(
											'value' => $val,
											'text'  => $term->name,
										);
									}
								}
							} else {
								foreach ( $post_tags as $key => $label ) {
									$data[] = array(
										'value' => $key,
										'text'  => $label,
									);
								}
							}

							$data = wp_json_encode( $data );
							?>

                            <input name="tag"
                                   placeholder="<?php echo penci_ftsub_get_text( 'ctag' ); ?>"
                                   type="text"
                                   class="multitag-field form-control <?php esc_attr_e( $ajax_class ); ?>"
                                   value="<?php esc_attr_e( $value ); ?>">
                            <div class="data-option" style="display: none;">
								<?php echo esc_html( $data ); ?>
                            </div>
                        </div>

					<?php endif; ?>
					<?php do_action( 'pfs_edit_post', $post_id ); ?>
                </div>
            </div>
        </form>
    </div>

<?php get_footer(); ?>