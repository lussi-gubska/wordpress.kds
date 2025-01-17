<?php

function penciai_is_json( $string ) {
	$data = json_decode( $string );

	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return false;
	} else {
		return true;
	}
}

function penciai_remove_first_br( $content ) {
	$index = strpos( $content, "<br><br>" );

	return substr( $content, $index + 2 );
}

function penci_ai_cdata() {
	if ( pcacgHasAccess() ) {
		?>
		<script>
            /* <![CDATA[ */
            var pcacg = {
                "ajax_url": "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                "nonce": "<?php echo wp_create_nonce( 'rc-nonce' ); ?>",
                "home_url": "<?php echo home_url(); ?>",
            };
            /* ]]\> */
		</script>
		<?php
	}

}

add_action( 'admin_print_scripts', 'penci_ai_cdata' );

function penciai_title_suggestion() {
	?>
	<div class="penciai_modal_wrap" style="display: none">
		<div id="suggestion_title_modal">
			<div class="penciai_modal">
				<h1 class="select_suggested_title"
				    style="display: none"><?php _e( "Select a title which you like", "penci-ai" ); ?></h1>
				<h2><?php _e( "New title for ", "penci-ai" ); ?>"<span class='title_for_suggestion'></span>"
				</h2>

				<div class="penciai_suggested_titles">
					<span class="suggest_titles penciai_spinner"></span>
				</div>
			</div>
		</div>
	</div>
	<?php
}

add_action( "admin_footer", "penciai_title_suggestion" );


function penciai_save_image_to_gallery() {
	?>
    <div class="penciai_modal_wrap" style="display: none">
        <div id="save-image-to-gallery">
            <div class="penciai_modal">
                <div class="penciai_single_variation_image">
                    <div class="theSingleImage"><img src=""></div>
                    <div class="image-form-container">
                        <form action="" method="post">
                            <div class="settings-item">
                                <label for="title"><span>Title</span></label>
                                <textarea name="title" id="title" class="form-control"></textarea>
                            </div>
                            <div class="settings-item">
                                <label for="alternative_text"><span>Alternative Text</span></label>
                                <textarea name="alternative_text" id="alternative_text" class="form-control"></textarea>
                            </div>
                            <div class="settings-item">
                                <label for="caption"><span>Caption</span></label>
                                <textarea name="caption" id="caption" class="form-control"></textarea>
                            </div>
                            <div class="settings-item">
                                <label for="description"><span>Description</span></label>
                                <textarea name="description" id="description" class="form-control"></textarea>
                            </div>
                            <div class="settings-item">
                                <label for="file_name"><span>File name</span></label>
                                <input type="text" name="file_name" id="file_name" class="form-control">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php
}

add_action( "admin_footer", "penciai_save_image_to_gallery" );