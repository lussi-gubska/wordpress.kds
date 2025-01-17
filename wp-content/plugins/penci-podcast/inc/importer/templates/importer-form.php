<?php
$post_id          = ( isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : null );
$render_data_list = PenciPodcast\Helper\FeedForm::get_for_render( $post_id );
$has_any_advanced = false;// Will be changed during first loop.
?>
<div class="main-container-pencipdc">
    <form method="POST" class="pencipdc_import_form">
        <div class="pencipdc_import_notifications" style="display:none;"></div>
        <div class="pencipdc_import_wrapper">
			<?php if ( $post_id !== null ) : ?>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ) ?>">
			<?php endif; ?>
			<?php foreach ( $render_data_list as $render_data ) : ?>
				<?php if ( isset( $render_data['is_advanced'] ) && $render_data['is_advanced'] ) {
					$has_any_advanced = true;
					continue;
				} ?>
				<?php pencipdc_importer_load_template( '_form-field.php', [ 'data' => $render_data ] ); ?>
			<?php endforeach; ?>

			<?php if ( $has_any_advanced ) : ?>
                <div class="pencipdc_import_advanced_settings_container">
                    <h3 class="pencipdc_import_advanced_settings_toggle">
                        <i></i><?php echo esc_html__( 'Advanced Options', 'penci-podcast' ); ?></h3>
                    <div class="pencipdc_import_advanced_settings">
						<?php foreach ( $render_data_list as $render_data ) : ?>
							<?php if ( ! isset( $render_data['is_advanced'] ) || ! $render_data['is_advanced'] ) {
								continue;
							} ?>

							<?php pencipdc_importer_load_template( '_form-field.php', [ 'data' => $render_data ] ); ?>
						<?php endforeach; ?>
                    </div>
                </div>
			<?php endif; ?>

			<?php if ( $post_id !== null ) : ?>
                <button class="button button-primary pencipdc_import_form_submit"><?php echo esc_html__( "Update", 'penci-podcast' ); ?></button>
			<?php else : ?>
                <button class="button button-primary pencipdc_import_form_submit"><?php echo esc_html__( "Import", 'penci-podcast' ); ?></button>
			<?php endif; ?>
        </div>
    </form>
</div>  