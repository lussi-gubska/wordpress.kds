<?php
function penciai_content_structure_settings_item() {
	?>

    <div class="settings-item generate-title">
        <label for="penciai-generate-title"><span><?php _e( 'Randomely generate post title', 'penci-ai' ); ?></span>
            <input id="penciai-generate-title" class="content-settings-input" type="checkbox"
                   name="generate-title" <?php echo esc_attr( get_theme_mod( 'penci_ai_random_post_title' ) ) ? 'checked' : ''; ?>>
        </label>
        <p><?php _e( 'Select this to generate blog post title.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item content-structure">
        <label for="penciai-content-structure">
            <span><?php _e( 'Content Structure', 'penci-ai' ); ?></span>
        </label>
        <select name="ai-content-structure" id="penciai-content-structure" data-has-subsettings="">
			<?php
			penciai_get_content_structure_options();
			?>
        </select>

        <p><?php _e( 'Choose the content type of your blog post which fit your need!', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item <?php echo esc_attr( get_theme_mod( 'penci_ai_content_structure', 'topic-wise' ) ) != 'topic_wise' ? 'penciai-hidden' : ''; ?>"
         data-subsettings-of="penciai-content-structure" data-sub-settings-key="topic_wise">
        <label for="penciai-how-many-topics"><span><?php _e( 'How many topics', 'penci-ai' ); ?></span>
            <input id="penciai-how-many-topics" class="content-settings-input" type="number" name="topics-count"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_number_headings', 5 ) ); ?>" placeholder="5">
        </label>
        <p><?php _e( 'Enter a number of topics you want to add to your blog post.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item <?php echo esc_attr( get_theme_mod( 'penci_ai_content_structure', 'topic-wise' ) ) != 'topic_wise' ? 'penciai-hidden' : ''; ?>"
         data-subsettings-of="penciai-content-structure" data-sub-settings-key="topic_wise">
        <label for="penciai-topics-tag"><span><?php _e( 'Topics heading tag', 'penci-ai' ); ?></span></label>
        <select name="penciai-topics-tag" id="penciai-topics-tag">
			<?php
			penciai_get_topics_tag_options();
			?>
        </select>
        <p><?php _e( 'Topics will automatically be wrapped by the selected heading tag.', 'penci-ai' ); ?></p>
    </div>


    <div class="settings-item sub-settings-item <?php echo esc_attr( get_theme_mod( 'penci_ai_content_structure', 'topic-wise' ) ) != 'article' ? 'penciai-hidden' : ''; ?>"
         data-subsettings-of="penciai-content-structure" data-sub-settings-key="article">
        <label for="penciai-how-many-article-paragraphs"><span><?php _e( 'How many paragraphs', 'penci-ai' ); ?></span>
            <input id="penciai-how-many-article-paragraphs" class="content-settings-input" type="number"
                   name="article-paragraphs-count"
                   value="3"
                   placeholder="3">
        </label>
        <p><?php _e( 'Enter a number of paragraphs you want to add on your blog post article.', 'penci-ai' ); ?></p>
    </div>

	<?php
}

function penciai_api_settings() {
	?>

    <div class="settings-item temperature-setting">
        <div class="range-input">
            <label for="temperature"><span><?php _e( 'Temperature', 'penci-ai' ); ?></span>
                <input id="temperature-input" class="input-box" style="width: 50px;" type="text"
                       value="<?php echo esc_attr( get_theme_mod( 'penci_ai_temperature', '0.8' ) ); ?>">
            </label>
            <input type="range" min="0" max="1"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_temperature', '0.8' ) ); ?>" step="0.01"
                   id="temperature" class="slider" name="temperature">
        </div>

        <p><?php _e( 'Control randomness: Lowering results in less random completions.  As the temperature approaches zero, the model will become deterministic and repetitive. If it approaches one, the model will become more randomness and creative.', 'penci-ai' ); ?></p>
    </div>


    <!-- Max tokens input field with tooltip and description -->
    <div class="settings-item max-tokens">
        <!--<input type="number" name="max-tokens" value="256">-->
        <div class="range-input">
            <label for="max-tokens"><span><?php _e( 'Max Tokens', 'penci-ai' ); ?></span>
                <input id="max-tokens-input" class="input-box" style="width: 50px;" type="text"
                       value="<?php echo esc_attr( get_theme_mod( 'penci_ai_max_tokens', '2000' ) ); ?>">
            </label>

            <input type="range" min="5" max="4000"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_max_tokens', '2000' ) ); ?>" step="1"
                   id="max-tokens"
                   class="slider" name="max-tokens">
        </div>
        <p><?php _e( 'Set the maximum number of tokens to generate in a single request.', 'penci-ai' ); ?></p>
    </div>

    <!-- Top-P input field with tooltip and description -->
    <div class="settings-item top-p-input">
        <div class="range-input">
            <label for="top-p"><span><?php _e( 'Top Prediction (Top-P)', 'penci-ai' ); ?></span>
                <input id="top-p-input" class="input-box" style="width: 50px;" type="text"
                       value="<?php echo esc_attr( get_theme_mod( 'penci_ai_top_p', '0.5' ) ); ?>">
            </label>
            <input type="range" min="0" max="1"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_top_p', '0.5' ) ); ?>"
                   step="0.01" id="top-p" class="slider" name="top-p">
        </div>

        <p><?php _e( 'Adjust the Top-P (Top Prediction) parameter to control the diversity of the generated text.', 'penci-ai' ); ?></p>
    </div>

    <!-- "Best of" input field with tooltip and description -->
    <div class="settings-item best-of">
        <div class="range-input">
            <label for="best-of"><span><?php _e( 'Best of', 'penci-ai' ); ?></span>
                <input id="best-of-input" class="input-box" style="width: 50px;" type="text"
                       value="<?php echo esc_attr( get_theme_mod( 'penci_ai_best_of', '1' ) ); ?>">
            </label>
            <input type="range" min="0" max="1"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_best_of', '1' ) ); ?>"
                   step="0.01" id="best-of" class="slider" name="best-of">
        </div>
        <p><?php _e( 'Set the number of generated sequences to return.', 'penci-ai' ); ?></p>
    </div>

    <!-- "Frequency penalty" input field with tooltip and description -->
    <div class="settings-item frequency-penalty">
        <div class="range-input">
            <label for="frequency-penalty"><span><?php _e( 'Frequency Penalty', 'penci-ai' ); ?></span>
                <input id="frequency-penalty-input" class="input-box" style="width: 50px;" type="text"
                       value="<?php echo esc_attr( get_theme_mod( 'penci_ai_frequency_penalty', '0' ) ); ?>">
            </label>
            <input type="range" min="0" max="2"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_frequency_penalty', '0' ) ); ?>" step="0.01"
                   id="frequency-penalty" class="slider" name="frequency-penalty">
        </div>
        <p><?php _e( 'Adjust the frequency penalty to control the frequency of words in the generated text.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item presence-penalty">
        <div class="range-input">
            <label for="presence-penalty"><span><?php _e( 'Presence Penalty', 'penci-ai' ); ?></span>
                <input id="presence-penalty-input" class="input-box" style="width: 50px;" type="text"
                       value="<?php echo esc_attr( get_theme_mod( 'penci_ai_presence_penalty', '0' ) ); ?>"></label>
            <input type="range" min="0" max="2"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_presence_penalty', '0' ) ); ?>" step="0.01"
                   id="presence-penalty" class="slider" name="presence-penalty">
        </div>
        <p><?php _e( 'Adjust the presence penalty to control the presence of words in the generated text.', 'penci-ai' ); ?></p>
    </div>

	<?php
}

function penciai_writing_styles_settings_item() {
	?>
    <div class="settings-item writing-styles">
        <label for="penciai-writing-style">
            <span><?php _e( 'Writing Style', 'penci-ai' ); ?></span>
        </label>
        <select id="penciai-writing-style" name="writing-style">
			<?php
			penciai_get_writing_styles_options();
			?>
        </select>

        <p><?php _e( 'Choose the writing style of your blog post which fit your need!', 'penci-ai' ); ?></p>
    </div>
	<?php
}

function penciai_writing_tone_settings_item() {
	?>
    <div class="settings-item writing-tone">
        <label for="penciai-writing-tone">
            <span><?php _e( 'Writing Tone', 'penci-ai' ); ?></span>
        </label>
        <select id="penciai-writing-tone" name="writing-tone">
			<?php
			penciai_get_writing_tone_options();
			?>
        </select>

        <p><?php _e( 'Choose the writing tone of your blog post which fit your need!', 'penci-ai' ); ?></p>
    </div>
	<?php
}

function penciai_add_introduction_settings_items() {
	?>

    <div class="settings-item add-introduction">
        <label for="penciai-add-introduction"><span><?php _e( 'Add introduction', 'penci-ai' ); ?></span>
            <input id="penciai-add-introduction" class="content-settings-input" name="add-introduction" type="checkbox"
                   data-has-subsettings="" <?php echo esc_attr( get_theme_mod( 'penci_ai_add_intro', true ) ) ? 'checked' : ''; ?>>
        </label>
        <p><?php _e( 'Add an introduction beginning of the topics.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item <?php echo esc_attr( get_theme_mod( 'penci_ai_add_intro', true ) ) ? '' : 'penciai-hidden'; ?>"
         data-subsettings-of="penciai-add-introduction">
        <label for="penciai-add-introduction-text"><span><?php _e( 'Add "Introduction" text', 'penci-ai' ); ?></span>
            <input id="penciai-add-introduction-text" class="content-settings-input" name="add-introduction-text"
                   type="text"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_intro_heading', 'Introduction' ) ); ?>">
        </label>
        <p><?php _e( 'Select to add "Introduction:" text before the introduction content.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item <?php echo esc_attr( get_theme_mod( 'penci_ai_add_intro', true ) ) ? '' : 'penciai-hidden'; ?>"
         data-subsettings-of="penciai-add-introduction">
        <label for="penciai-introduction-size"><span><?php _e( 'Introduction text size', 'penci-ai' ); ?></span></label>
        <select name="introduction-size" id="penciai-introduction-size">
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_intro_size', 'short' ) ) == 'short' ? 'selected' : 'short'; ?>
                    value=""><?php _e( 'Short', 'penci-ai' ); ?></option>
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_intro_size', 'short' ) ) == 'medium' ? 'selected' : 'medium'; ?>
                    value=""><?php _e( 'Medium', 'penci-ai' ); ?></option>
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_intro_size', 'short' ) ) == 'long' ? 'selected' : 'long'; ?>
                    value=""><?php _e( 'Long', 'penci-ai' ); ?></option>
        </select>
        <p><?php _e( 'Select a size to set how long your introduction size is needed.', 'penci-ai' ); ?></p>
    </div>

	<?php
}

function penciai_add_conclusion_settings_items() {
	?>

    <div class="settings-item add-conclusion">
        <label for="penciai-add-conclusion"><span><?php _e( 'Add conclusion', 'penci-ai' ); ?></span>
            <input id="penciai-add-conclusion" class="content-settings-input" type="checkbox" name="add-conclusion"
                   data-has-subsettings="" <?php echo get_theme_mod( 'penci_ai_add_conclusion' ) ? 'checked' : ''; ?>>
        </label>
        <p><?php _e( 'Add conclusion end of the topics.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item  <?php echo get_theme_mod( 'penci_ai_add_conclusion' ) ? '' : 'penciai-hidden'; ?>"
         data-subsettings-of="penciai-add-conclusion">
        <label for="penciai-add-conclusion-text"><span><?php _e( 'Add "Conclusion" text', 'penci-ai' ); ?></span>
            <input id="penciai-add-conclusion-text" class="content-settings-input" name="add-conclusion-text"
                   type="text"
                   data-has-subsettings=""
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_conclusion_heading', 'Conclusion' ) ); ?>">
        </label>
        <p><?php _e( 'Select to add "Conclusion:" text before the conclusion content.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item <?php echo get_theme_mod( 'penci_ai_add_conclusion' ) ? '' : 'penciai-hidden'; ?>"
         data-subsettings-of="penciai-add-conclusion">
        <label for="penciai-conclusion-size"><span><?php _e( 'Conclusion text size', 'penci-ai' ); ?></span></label>
        <select name="conclusion-size" id="penciai-conclusion-size">
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_conclusion_size', 'short' ) ) == 'short' ? 'selected' : 'short'; ?>
                    value=""><?php _e( 'Short', 'penci-ai' ); ?></option>
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_conclusion_size', 'short' ) ) == 'medium' ? 'selected' : 'medium'; ?>
                    value=""><?php _e( 'Medium', 'penci-ai' ); ?></option>
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_conclusion_size', 'short' ) ) == 'long' ? 'selected' : 'long'; ?>
                    value=""><?php _e( 'Long', 'penci-ai' ); ?></option>
        </select>
        <p><?php _e( 'Select a size for how long your conclusion size is needed.', 'penci-ai' ); ?></p>
    </div>

	<?php
}

function penciai_add_excerpt_settings_items() {
	?>

    <div class="settings-item add-excerpt">
        <label for="penciai-add-excerpt"><span><?php _e( 'Add excerpt', 'penci-ai' ); ?></span>
            <input id="penciai-add-excerpt" class="content-settings-input" type="checkbox" name="add-excerpt"
                   data-has-subsettings="" <?php echo esc_attr( get_theme_mod( 'penci_ai_add_excerpt', true ) ) ? 'checked' : ''; ?>>
        </label>
        <p><?php _e( 'Add conclusion end of the topics.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item  <?php echo esc_attr( get_theme_mod( 'penci_ai_add_excerpt', true ) ) ? '' : 'penciai-hidden'; ?>"
         data-subsettings-of="penciai-add-excerpt">
        <label for="penciai-excerpt-number-of-words"><span><?php _e( 'Number of excerpt words', 'penci-ai' ); ?></span>
            <input id="penciai-excerpt-number-of-words" class="content-settings-input" name="excerpt_number_of_words"
                   type="number"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_excerpt_words', '50' ) ); ?>">
        </label>
        <p><?php _e( 'Enter a number of words to get the excerpt content withing the number of words.', 'penci-ai' ); ?></p>
    </div>

	<?php
}

function penciai_content_length_settings_items() {
	?>

    <div class="settings-item content-length">
        <label for="penciai-content-length"><span><?php _e( 'Content length', 'penci-ai' ); ?></span> </label>
        <select name="content-length" id="penciai-content-length">
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_content_length', 'long' ) ) == 'long' ? 'selected' : ''; ?>
                    value="long">Long
            </option>
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_content_length', 'long' ) ) == 'medium' ? 'selected' : ''; ?>
                    value="medium">Medium
            </option>
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_content_length', 'long' ) ) == 'short' ? 'selected' : ''; ?>
                    value="short">Short
            </option>
        </select>
        <p><?php _e( 'Select a content length that fit your need.', 'penci-ai' ); ?></p>
    </div>

	<?php
}

function penciai_auto_generate_image_settings_items() {
	$image_experiments = (array) get_theme_mod( 'penci_ai_img_experiments', array(
		'realistic',
		'four_k',
		'high_resolution',
		'trending_in_artstation',
		'artstation_three'
	) );
	$image_experiments = array_map( 'esc_attr', $image_experiments );
	?>

    <div class="settings-item generate-image-settings penciai-hidden">
        <label for="penciai-auto-generate-image"><span><?php _e( 'Auto generate featured image', 'penci-ai' ); ?></span>
        </label>
        <input id="penciai-auto-generate-image" class="content-settings-input" name="auto-generate-image"
               type="checkbox"
               data-has-subsettings="" <?php echo esc_attr( get_theme_mod( 'penci_ai_auto_featured_image', true ) ) ? 'checked' : ''; ?>>
        <p><?php _e( 'Select this to auto-generate the thumbnail image. It will generate from your main prompt.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item"
         data-subsettings-of="penciai-auto-generate-image">
        <label>
            <span><?php _e( 'Image Size', 'penci-ai' ); ?></span>
        </label>
        <select name="ai-image-size">
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_featured_image_size', 'large' ) ) == 'thumbnail' ? 'selected' : ''; ?>
                    value="thumbnail"><?php _e( 'Thumbnail (256x256px)', 'penci-ai' ); ?></option>
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_featured_image_size', 'large' ) ) == 'medium' ? 'selected' : ''; ?>
                    value="medium"><?php _e( 'Medium (512x512px)', 'penci-ai' ); ?></option>
            <option <?php echo esc_attr( get_theme_mod( 'penci_ai_featured_image_size', 'large' ) ) == 'large' ? 'selected' : ''; ?>
                    value="large"><?php _e( 'Large (1024x1024px)', 'penci-ai' ); ?></option>
        </select>
        <p><?php _e( 'Choose the size of the image you want to generate with <a href="https://openai.com/dall-e-2/">' . __( "DALL-E", "penci-ai" ) . '</a>.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item"
         data-subsettings-of="penciai-auto-generate-image">
        <label>
            <span><?php _e( 'Image Presets', 'penci-ai' ); ?></span>
        </label>

        <select id="penciai_imagePresets" name="image_presets">
            <option value="realistic,four_k,high_resolution,trending_in_artstation, artstation_three, 3D_render, digital_painting"><?php _e( 'High Quality Art', 'penci-ai' ); ?></option>
            <option value="realistic,3D_render,eight_k,high_resolution,professional"><?php _e( 'Realistic', 'penci-ai' ); ?></option>
            <option value="amazing_art,trending_in_artstation,artstation_3,oil_painting,digital_paintinghigh_resolution"><?php _e( 'Amazing Art', 'penci-ai' ); ?></option>
            <option value="Expert,Stunning,Creative,Popular,Inspired,four_k,trending_in_artstationhigh_resolution"><?php _e( 'Expert', 'penci-ai' ); ?></option>
            <option value="surreal,abstract,fantasy,pop_art,vector"><?php _e( 'Surreal', 'penci-ai' ); ?></option>
            <option value="landscape,portrait,iconic,neo_expressionist,four_k"><?php _e( 'Landscape', 'penci-ai' ); ?></option>
            <option value="realistic,3D_render,eight_k,high-resolution,professional,trending_in_artstation, artstation_three"><?php _e( 'High Resolution', 'penci-ai' ); ?></option>
            <option value="amazing_art,trending_in_artstation,artstation_3,oil_painting,digital_painting,four_k"><?php _e( 'Digital Painting', 'penci-ai' ); ?></option>
            <option value="Expert,Stunning,Creative,Popular,Inspired"><?php _e( 'Pop Art', 'penci-ai' ); ?></option>
            <option value="landscape,iconic,neo_expressionist,four_k,high_resolution"><?php _e( 'Landscape Painting', 'penci-ai' ); ?></option>
            <option value="realistic,3D_render,four_k,high-resolution,professional"><?php _e( 'Realistic Art', 'penci-ai' ); ?></option>
            <option value="amazing_art,trending_in_artstation,artstation_3,oil_painting,digital_painting,four_k,high_resolution"><?php _e( 'Digital Art', 'penci-ai' ); ?></option>
            <option value="Expert,Stunning,Creative,Popular,Inspired,eight_k"><?php _e( 'Abstract Art', 'penci-ai' ); ?></option>
            <option value="surreal,abstract,fantasy,pop_art,vector"><?php _e( 'Surrealistic Art', 'penci-ai' ); ?></option>
            <option value="landscape,portrait,iconic,neo_expressionist,four_k"><?php _e( 'Portrait Painting', 'penci-ai' ); ?></option>
            <option value="neon,realistic,3D_render,eight_k,high_resolution,professional"><?php _e( 'Neon Light', 'penci-ai' ); ?></option>
        </select>

    </div>

    <div class="settings-item sub-settings-item penciai-hidden"
         data-subsettings-of="penciai-auto-generate-image">
        <label>
            <span><?php _e( 'Image Experiments', 'penci-ai' ); ?></span>
        </label>
        <br>
        <label for="penciai_realistic" class="image-experiments"><input
                    id="penciai_realistic" <?php echo in_array( 'realistic', $image_experiments ) ? 'checked' : ''; ?>
                    type="checkbox"
                    name="image_experiments[realistic]"> <?php _e( 'Realistic', 'penci-ai' ); ?></label>
        <label for="penciai_3D_render" class="image-experiments"><input
                    id="penciai_3D_render" <?php echo in_array( '3D_render', $image_experiments ) ? 'checked' : ''; ?>
                    type="checkbox"
                    name="image_experiments[3D_render]"> <?php _e( '3D render', 'penci-ai' ); ?></label>
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
                    name="image_experiments[amazing_art]"> <?php _e( 'Amazing art', 'penci-ai' ); ?></label>
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
            <input id="penciai_professional" <?php echo in_array( 'professional', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[professional]">
			<?php _e( 'Professional', 'penci-ai' ); ?>
        </label>

        <label for="penciai_Expert" class="image-experiments">
            <input id="penciai_Expert" <?php echo in_array( 'Expert', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[Expert]">
			<?php _e( 'Expert', 'penci-ai' ); ?>
        </label>

        <label for="penciai_Stunning" class="image-experiments">
            <input id="penciai_Stunning" <?php echo in_array( 'Stunning', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[Stunning]">
			<?php _e( 'Stunning', 'penci-ai' ); ?>
        </label>

        <label for="penciai_Creative" class="image-experiments">
            <input id="penciai_Creative" <?php echo in_array( 'Creative', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[Creative]">
			<?php _e( 'Creative', 'penci-ai' ); ?>
        </label>

        <label for="penciai_Popular" class="image-experiments">
            <input id="penciai_Popular" <?php echo in_array( 'Popular', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[Popular]">
			<?php _e( 'Popular', 'penci-ai' ); ?>
        </label>

        <label for="penciai_Inspired" class="image-experiments">
            <input id="penciai_Inspired" <?php echo in_array( 'Inspired', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[Inspired]">
			<?php _e( 'Inspired', 'penci-ai' ); ?>
        </label>

        <label for="penciai_surreal" class="image-experiments">
            <input id="penciai_surreal" <?php echo in_array( 'surreal', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[surreal]">
			<?php _e( 'Surreal', 'penci-ai' ); ?>
        </label>

        <label for="penciai_abstract" class="image-experiments">
            <input id="penciai_abstract" <?php echo in_array( 'abstract', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[abstract]">
			<?php _e( 'Abstract', 'penci-ai' ); ?>
        </label>

        <label for="penciai_fantasy" class="image-experiments">
            <input id="penciai_fantasy" <?php echo in_array( 'fantasy', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[fantasy]">
			<?php _e( 'Fantasy', 'penci-ai' ); ?>
        </label>

        <label for="penciai_pop_art" class="image-experiments">
            <input id="penciai_pop_art" <?php echo in_array( 'pop_art', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[pop_art]">
			<?php _e( 'Pop Art', 'penci-ai' ); ?>
        </label>

        <label for="penciai_neo_expressionist" class="image-experiments">
            <input id="penciai_neo_expressionist" <?php echo in_array( 'neo_expressionist', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[neo_expressionist]">
			<?php _e( 'Neo-expressionist', 'penci-ai' ); ?>
        </label>

        <label for="penciai_vector" class="image-experiments">
            <input id="penciai_vector" <?php echo in_array( 'vector', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[vector]">
			<?php _e( 'Vector', 'penci-ai' ); ?>
        </label>

        <label for="penciai_neon" class="image-experiments">
            <input id="penciai_neon" <?php echo in_array( 'neon', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox"
                   name="image_experiments[neon]">
			<?php _e( 'Neon', 'penci-ai' ); ?>
        </label>

        <label for="penciai_landscape" class="image-experiments">
            <input id="penciai_landscape" <?php echo in_array( 'land_scape', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[landscape]">
			<?php _e( 'Landscape', 'penci-ai' ); ?>
        </label>
        <label for="penciai_portrait" class="image-experiments">
            <input id="penciai_portrait" <?php echo in_array( 'portrait', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[portrait]">
			<?php _e( 'Portrait', 'penci-ai' ); ?>
        </label>
        <label for="penciai_iconic" class="image-experiments">
            <input id="penciai_iconic" <?php echo in_array( 'iconic', $image_experiments ) ? 'checked' : ''; ?>
                   type="checkbox" name="image_experiments[iconic]">
			<?php _e( 'Iconic', 'penci-ai' ); ?>
        </label>

        <p><?php _e( 'Choose the above styles to generate image.', 'penci-ai' ); ?></p>
    </div>

	<?php
}

function penciai_languages_settings_items() {
	?>

    <div class="settings-item penciai-language penciai-hidden">
        <label>
            <span><?php _e( 'Select Language', 'penci-ai' ); ?></span>
        </label>
        <select name="penciai-language" id="penciai-language">
			<?php
			penciai_get_languages_options();
			?>
        </select>
        <input type="hidden" name="penciai_language_text" id="penciai_language_text"
               value="<?php echo esc_attr( get_theme_mod( 'penci_ai_language' ) ); ?>">

        <p><?php _e( 'Select a language to generate contents with the language.', 'penci-ai' ); ?></p>
    </div>


	<?php
}


function penciai_select_title_before_generate_settings_items() {
	?>

    <div class="settings-item title-before-generate">
        <label for="penciai-select-title-before-generate"><span><?php _e( 'Select Post Title Before Generate', 'penci-ai' ); ?></span>
            <input id="penciai-select-title-before-generate" class="content-settings-input" type="checkbox"
                   name="select-title-before-generate"
                   data-has-subsettings="" <?php echo esc_attr( get_theme_mod( 'penci_ai_select_post_title', true ) ) ? 'checked' : ''; ?>>
        </label>
        <p><?php _e( 'Check this if you want to select the post title first before the content generation.', 'penci-ai' ); ?></p>
    </div>

    <div class="settings-item sub-settings-item  <?php echo esc_attr( get_theme_mod( 'penci_ai_select_post_title', true ) ) ? '' : 'penciai-hidden'; ?>"
         data-subsettings-of="penciai-select-title-before-generate">
        <label for="penciai-how-many-titles-show-first"><span><?php _e( 'How many titles you want to show to select?', 'penci-ai' ); ?></span>
            <input id="penciai-how-many-titles-show-first" class="content-settings-input"
                   name="how-many-titles-show-first"
                   type="number"
                   value="<?php echo esc_attr( get_theme_mod( 'penci_ai_number_titles', '3' ) ); ?>"
                   placeholder="<?php echo esc_attr( get_theme_mod( 'penci_ai_number_titles', '3' ) ); ?>">
        </label>
        <p><?php _e( '<p>Enter a number of titles you want to show first to select before the content generate.</p><strong>For the free plan, you are limited to displaying a maximum of three titles per request.</strong>', 'penci-ai' ); ?></p>
    </div>


    <input type="hidden" name="penciai_is_title_selected" value="0">
    <input type="hidden" name="penciai_selected_title" class="penciai_selected_title" value="">


	<?php
}