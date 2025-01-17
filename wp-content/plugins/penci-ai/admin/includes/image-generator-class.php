<?php

// Declare a namespace for the class
namespace PenciAIContentGenerator;


class ImageGenerator {

	/**
	 * ScheduledPostGeneration constructor.
	 */
	public function __construct() {
		if ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'ai-image-generator' ) {
			add_action( 'penciai_codebox', array( $this, 'penciai_codebox' ) );
			add_filter( 'penciai_promptbox_title', array( $this, 'promptbox_title_for_auto_write' ) );
			add_filter( 'penciai_metabox_settings', array( $this, 'metabox_settings' ) );
			add_filter( 'penciai_generate_button_text', array( $this, 'penciai_generate_button_text' ) );
			add_action( 'penciai_promptbox_footer_buttons', array( $this, 'promptbox_footer_buttons' ) );
			add_action( 'penciai_after_promptbox_fields', array( $this, 'after_promptbox_fields' ) );
		}
	}

	public function promptbox_footer_buttons() {
		?>
		<div class="penciai-promptbox-button">
			<a href="#" id="penciai-image-settings-btn" class="penciai-settings-btn"
			   data-settings="image-settings"><?php _e( 'Image settings', 'penci-ai' ); ?></a>
		</div>
		<?php
	}

	public function after_promptbox_fields() {
		$image_experiments = (array) get_theme_mod( 'penci_ai_image_experiments', array(
			'realistic',
			'3D_render',
			'four_k',
			'high_resolution',
			'trending_in_artstation',
			'artstation_three',
			'digital_painting'
		) );
		$image_experiments = array_map( 'esc_attr', $image_experiments );

		?>
		<div class="ai_response_hidden prompt-settings-item" id="image-settings" data-tab="image-settings">
			<div class="image-settings-box code-box-dark">
				<div class="code-box-header">
					<div class="title"><?php _e( 'Image settings', 'penci-ai' ); ?></div>
					<button class="minimize-btn"
					        title="<?php _e( 'Minimize image settings panel', 'penci-ai' ); ?>">
						<svg fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" id="minus"
						     class="icon glyph" stroke="#000000" width="24px" height="24px">
							<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
							<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
							<g id="SVGRepo_iconCarrier">
								<path d="M19,13H5a1,1,0,0,1,0-2H19a1,1,0,0,1,0,2Z"></path>
							</g>
						</svg>
					</button>
				</div>
				<div class="penciai-image-settings-panel penciai-settings-panel-item">

					<div class="settings-item">
						<label
							for="penciai-new-images-with-existing"><span><?php _e( 'Add new images to existing images', 'penci-ai' ); ?></span>
							<input id="penciai-new-images-with-existing" class="content-settings-input" type="checkbox"
							       name="new_images_with_existing" <?php echo esc_attr( get_theme_mod( 'penci_ai_new_images_with_existing' ) ) ? 'checked' : ''; ?>>
						</label>
						<p><?php _e( 'Select this to add new images with existing generated images.', 'penci-ai' ); ?></p>
					</div>

					<div class="settings-item">
						<label
							for="penciai-number-of-image"><span><?php _e( 'How many images need to be regenerated?', 'penci-ai' ); ?></span>
							<input id="penciai-number-of-image" class="content-settings-input" type="number"
							       value="<?php echo esc_attr( get_theme_mod( 'penci_ai_number_of_image', '3' ) ); ?>"
							       name="number_of_image" placeholder="3">
						</label>
						<p><?php _e( 'Enter the number you want to generate image before save to media library.', 'penci-ai' ); ?></p>
					</div>


					<div class="settings-item">
						<label
							for="penciai-number-of-image"><span><?php _e( 'Image AI Engine', 'penci-ai' ); ?></span>
						</label>
						<select name="ai-image-engine">
							<option <?php echo esc_attr( get_theme_mod( 'penci_ai_img_type', 'dall_e' ) ) == 'dall_e' ? 'selected' : ''; ?>
								value="dall_e"><?php _e( 'DALL_E', 'penci-ai' ); ?></option>
                            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_img_type', 'dall_e' ) ) == 'midjourney' ? 'selected' : ''; ?>
								value="midjourney"><?php _e( 'Midjourney', 'penci-ai' ); ?></option>
							<option <?php echo esc_attr( get_theme_mod( 'penci_ai_img_type', 'dall_e' ) ) == 'open_journey' ? 'selected' : ''; ?>
								value="open_journey"><?php _e( 'Open Journey', 'penci-ai' ); ?></option>
							<option <?php echo esc_attr( get_theme_mod( 'penci_ai_img_type', 'dall_e' ) ) == 'stable_diffusion' ? 'selected' : ''; ?>
								value="stable_diffusion"><?php _e( 'Stable Diffusion', 'penci-ai' ); ?></option>
							<option <?php echo esc_attr( get_theme_mod( 'penci_ai_img_type', 'dall_e' ) ) == 'text-to-pokemon' ? 'selected' : ''; ?>
								value="text-to-pokemon"><?php _e( 'Text to Pokemon', 'penci-ai' ); ?></option>
							<option <?php echo esc_attr( get_theme_mod( 'penci_ai_img_type', 'dall_e' ) ) == 'anything-v3-better-vae' ? 'selected' : ''; ?>
								value="anything-v3-better-vae"><?php _e( 'VAE', 'penci-ai' ); ?></option>
							<option <?php echo esc_attr( get_theme_mod( 'penci_ai_img_type', 'dall_e' ) ) == 'anything-v4.0' ? 'selected' : ''; ?>
								value="anything-v4.0"><?php _e( 'Anything', 'penci-ai' ); ?></option>
							<option <?php echo esc_attr( get_theme_mod( 'penci_ai_img_type', 'dall_e' ) ) == 'text2image' ? 'selected' : ''; ?>
								value="text2image"><?php _e( 'text2image', 'penci-ai' ); ?></option>
						</select>
						<p><?php _e( 'Choose the Image AI Engine you want to use to generate images.', 'penci-ai' ); ?></p>
					</div>

					<div class="settings-item">
						<label>
							<span><?php _e( 'Image Size', 'penci-ai' ); ?></span>
						</label>
						<select name="ai-image-size">
							<option <?php echo esc_attr( get_theme_mod( 'penci-ai-image-size', 'large' ) ) == 'thumbnail' ? 'selected' : ''; ?>
								value="thumbnail"><?php _e( 'Thumbnail (256x256px)', 'penci-ai' ); ?></option>
							<option <?php echo esc_attr( get_theme_mod( 'penci-ai-image-size', 'large' ) ) == 'medium' ? 'selected' : ''; ?>
								value="medium"><?php _e( 'Medium (512x512px)', 'penci-ai' ); ?></option>
							<option <?php echo esc_attr( get_theme_mod( 'penci-ai-image-size', 'large' ) ) == 'large' ? 'selected' : ''; ?>
								value="large"><?php _e( 'Large (1024x1024px)', 'penci-ai' ); ?></option>
						</select>
						<p><?php _e( 'Choose the size of the image you want to generate with <a href="https://openai.com/dall-e-2/">' . __( "DALL-E", "penci-ai" ) . '</a>.', 'penci-ai' ); ?></p>
					</div>

					<div class="settings-item">
						<label>
							<span><?php _e( 'Image Presets', 'penci-ai' ); ?></span>
						</label>

						<select id="penciai_imagePresets" name="image_presets">
							<option
								value="realistic,four_k,high_resolution,trending_in_artstation, artstation_three, 3D_render, digital_painting"><?php _e( 'High Quality Art', 'penci-ai' ); ?></option>
							<option
								value="realistic,3D_render,eight_k,high_resolution,professional"><?php _e( 'Realistic', 'penci-ai' ); ?></option>
							<option
								value="amazing_art,trending_in_artstation,artstation_3,oil_painting,digital_paintinghigh_resolution"><?php _e( 'Amazing Art', 'penci-ai' ); ?></option>
							<option
								value="Expert,Stunning,Creative,Popular,Inspired,four_k,trending_in_artstationhigh_resolution"><?php _e( 'Expert', 'penci-ai' ); ?></option>
							<option
								value="surreal,abstract,fantasy,pop_art,vector"><?php _e( 'Surreal', 'penci-ai' ); ?></option>
							<option
								value="landscape,portrait,iconic,neo_expressionist,four_k"><?php _e( 'Landscape', 'penci-ai' ); ?></option>
							<option
								value="realistic,3D_render,eight_k,high-resolution,professional,trending_in_artstation, artstation_three"><?php _e( 'High Resolution', 'penci-ai' ); ?></option>
							<option
								value="amazing_art,trending_in_artstation,artstation_3,oil_painting,digital_painting,four_k"><?php _e( 'Digital Painting', 'penci-ai' ); ?></option>
							<option
								value="Expert,Stunning,Creative,Popular,Inspired"><?php _e( 'Pop Art', 'penci-ai' ); ?></option>
							<option
								value="landscape,iconic,neo_expressionist,four_k,high_resolution"><?php _e( 'Landscape Painting', 'penci-ai' ); ?></option>
							<option
								value="realistic,3D_render,four_k,high-resolution,professional"><?php _e( 'Realistic Art', 'penci-ai' ); ?></option>
							<option
								value="amazing_art,trending_in_artstation,artstation_3,oil_painting,digital_painting,four_k,high_resolution"><?php _e( 'Digital Art', 'penci-ai' ); ?></option>
							<option
								value="Expert,Stunning,Creative,Popular,Inspired,eight_k"><?php _e( 'Abstract Art', 'penci-ai' ); ?></option>
							<option
								value="surreal,abstract,fantasy,pop_art,vector"><?php _e( 'Surrealistic Art', 'penci-ai' ); ?></option>
							<option
								value="landscape,portrait,iconic,neo_expressionist,four_k"><?php _e( 'Portrait Painting', 'penci-ai' ); ?></option>
							<option
								value="neon,realistic,3D_render,eight_k,high_resolution,professional"><?php _e( 'Neon Light', 'penci-ai' ); ?></option>
						</select>

					</div>

					<div class="settings-item penciai-hidden">
						<label>
							<span><?php _e( 'Image Experiments', 'penci-ai' ); ?></span>
						</label>
						<br>
						<label for="penciai_realistic" class="image-experiments"><input
								id="penciai_realistic" <?php echo in_array( 'realistic', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox"
								name="image_experiments[realistic]"> <?php _e( 'Realistic', 'penci-ai' ); ?>
						</label>
						<label for="penciai_3D_render" class="image-experiments"><input
								id="penciai_3D_render" <?php echo in_array( '3D_render', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox"
								name="image_experiments[3D_render]"> <?php _e( '3D render', 'penci-ai' ); ?>
						</label>
						<label for="penciai_four_k" class="image-experiments"><input id="penciai_four_k"
						                                                             type="checkbox" <?php echo in_array( 'four_k', $image_experiments ) ? 'checked' : ''; ?>
						                                                             name="image_experiments[four_k]"> <?php _e( '4K', 'penci-ai' ); ?>
						</label>
						<label for="penciai_eight_k" class="image-experiments"><input id="penciai_eight_k"
						                                                              type="checkbox" <?php echo in_array( 'eight_k', $image_experiments ) ? 'checked' : ''; ?>
						                                                              name="image_experiments[eight_k]"> <?php _e( '8K', 'penci-ai' ); ?>
						</label>
						<label for="penciai_amazing_art" class="image-experiments"><input
								id="penciai_amazing_art" <?php echo in_array( 'amazing_art', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox"
								name="image_experiments[amazing_art]"> <?php _e( 'Amazing art', 'penci-ai' ); ?>
						</label>
						<label for="penciai_high_resolution" class="image-experiments"><input
								id="penciai_high_resolution" <?php echo in_array( 'high_resolution', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox"
								name="image_experiments[high_resolution]"><?php _e( 'High resolution', 'penci-ai' ); ?>
						</label>
						<br>
						<label for="penciai_trending_in_artstation" class="image-experiments"><input
								id="penciai_trending_in_artstation" <?php echo in_array( 'trending_in_artstation', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox"
								name="image_experiments[trending_in_artstation]"> <?php _e( 'Trending in artstation', 'penci-ai' ); ?>
						</label>
						<label for="penciai_artstation_3" class="image-experiments"><input id="penciai_artstation_3"
						                                                                   type="checkbox" <?php echo in_array( 'artstation_three', $image_experiments ) ? 'checked' : ''; ?>
						                                                                   name="image_experiments[artstation_three]"> <?php _e( 'Artstation 3', 'penci-ai' ); ?>
						</label>
						<label for="penciai_oil_painting" class="image-experiments"><input id="penciai_oil_painting"
						                                                                   type="checkbox" <?php echo in_array( 'oil_painting', $image_experiments ) ? 'checked' : ''; ?>
						                                                                   name="image_experiments[oil_painting]"> <?php _e( 'Oil painting', 'penci-ai' ); ?>
						</label>
						<label for="penciai_digital_painting" class="image-experiments"><input
								id="penciai_digital_painting" <?php echo in_array( 'digital_painting', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox"
								name="image_experiments[digital_painting]"> <?php _e( 'Digital painting', 'penci-ai' ); ?>
						</label>

						<br>
						<label for="penciai_professional" class="image-experiments">
							<input
								id="penciai_professional" <?php echo in_array( 'professional', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[professional]">
							<?php _e( 'Professional', 'penci-ai' ); ?>
						</label>

						<label for="penciai_Expert" class="image-experiments">
							<input
								id="penciai_Expert" <?php echo in_array( 'Expert', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[Expert]">
							<?php _e( 'Expert', 'penci-ai' ); ?>
						</label>

						<label for="penciai_Stunning" class="image-experiments">
							<input
								id="penciai_Stunning" <?php echo in_array( 'Stunning', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[Stunning]">
							<?php _e( 'Stunning', 'penci-ai' ); ?>
						</label>

						<label for="penciai_Creative" class="image-experiments">
							<input
								id="penciai_Creative" <?php echo in_array( 'Creative', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[Creative]">
							<?php _e( 'Creative', 'penci-ai' ); ?>
						</label>

						<label for="penciai_Popular" class="image-experiments">
							<input
								id="penciai_Popular" <?php echo in_array( 'Popular', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[Popular]">
							<?php _e( 'Popular', 'penci-ai' ); ?>
						</label>

						<label for="penciai_Inspired" class="image-experiments">
							<input
								id="penciai_Inspired" <?php echo in_array( 'Inspired', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[Inspired]">
							<?php _e( 'Inspired', 'penci-ai' ); ?>
						</label>

						<label for="penciai_surreal" class="image-experiments">
							<input
								id="penciai_surreal" <?php echo in_array( 'surreal', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[surreal]">
							<?php _e( 'Surreal', 'penci-ai' ); ?>
						</label>

						<label for="penciai_abstract" class="image-experiments">
							<input
								id="penciai_abstract" <?php echo in_array( 'abstract', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[abstract]">
							<?php _e( 'Abstract', 'penci-ai' ); ?>
						</label>

						<label for="penciai_fantasy" class="image-experiments">
							<input
								id="penciai_fantasy" <?php echo in_array( 'fantasy', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[fantasy]">
							<?php _e( 'Fantasy', 'penci-ai' ); ?>
						</label>

						<label for="penciai_pop_art" class="image-experiments">
							<input
								id="penciai_pop_art" <?php echo in_array( 'pop_art', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[pop_art]">
							<?php _e( 'Pop Art', 'penci-ai' ); ?>
						</label>

						<label for="penciai_neo_expressionist" class="image-experiments">
							<input
								id="penciai_neo_expressionist" <?php echo in_array( 'neo_expressionist', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[neo_expressionist]">
							<?php _e( 'Neo-expressionist', 'penci-ai' ); ?>
						</label>

						<label for="penciai_vector" class="image-experiments">
							<input
								id="penciai_vector" <?php echo in_array( 'vector', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[vector]">
							<?php _e( 'Vector', 'penci-ai' ); ?>
						</label>

						<label for="penciai_neon" class="image-experiments">
							<input
								id="penciai_neon" <?php echo in_array( 'neon', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[neon]">
							<?php _e( 'Neon', 'penci-ai' ); ?>
						</label>

						<label for="penciai_landscape" class="image-experiments">
							<input
								id="penciai_landscape" <?php echo in_array( 'land_scape', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[landscape]">
							<?php _e( 'Landscape', 'penci-ai' ); ?>
						</label>
						<label for="penciai_portrait" class="image-experiments">
							<input
								id="penciai_portrait" <?php echo in_array( 'portrait', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[portrait]">
							<?php _e( 'Portrait', 'penci-ai' ); ?>
						</label>
						<label for="penciai_iconic" class="image-experiments">
							<input
								id="penciai_iconic" <?php echo in_array( 'iconic', $image_experiments ) ? 'checked' : ''; ?>
								type="checkbox" name="image_experiments[iconic]">
							<?php _e( 'Iconic', 'penci-ai' ); ?>
						</label>

						<p><?php _e( 'Choose the above styles to generate image.', 'penci-ai' ); ?></p>
					</div>


				</div>
			</div>
		</div>

		<input type="hidden" name="image_generation_mode" value="true">

		<?php
	}

	public function metabox_settings( $settings ) {

		return array( 'generate_images' );
	}

	public function penciai_generate_button_text( $text ) {
		$text = __( "Generate Image", "penci-ai" );

		return $text;
	}

	public function promptbox_title_for_auto_write( $title ) {
		$title = __( 'Image generation prompt', 'penci-ai' );

		return $title;
	}


	public function penciai_codebox() {
		?>
		<div class="penciai-images">
			<div class="penciai-image-variation-items"></div>
		</div>
		<?php
	}

}


new ImageGenerator();