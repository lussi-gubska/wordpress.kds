<?php

namespace PenciAIContentGenerator;

class AddMetaBoxes_ {
	private $admin;

	/**
	 * AddMetaBoxes constructor.
	 */
	public function __construct( $a ) {
		$this->admin = $a;

		if ( $a->hasAccess() && (
				$a->hasCurrentPostType()
				|| ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'penci-ai' )
				|| ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'ai-image-generator' )
			) ) {
			add_action( 'admin_footer', array( $this, 'getpromptHtml' ) );
			add_action( 'add_meta_boxes', array( $this, 'penciai_writing_assistant_metabox' ) );
		}

	}

	public function is_gutenberg_editor() {
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			return true;
		}

		$current_screen = \get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return true;
		}

		return false;
	}

	public function penciai_writing_assistant_metabox() {
		if ( self::is_gutenberg_editor() ) {
			return false;
		}
		add_meta_box(
			'penciai_ai_metabox', // Unique ID
			__( 'Penci AI SmartContent Creator', 'penci-ai' ), // Title
			array( $this, 'penciai_writing_assistant_metabox_html' ), // Callback function
			array( 'post', 'page', 'product' ), // Screen (post, page, link, attachment, or custom post type)
			'side', // Context (normal, advanced, or side)
			'high' // Priority (high, core, default, or low)
		);
	}

	public function penciai_writing_assistant_metabox_html() {
		echo '<a id="penciai-content-generator-btn" class="components-button penciai-content-generator-btn penciai-button" href="#">' . __( "Penci AI SmartContent Creator", "penci-ai" ) . ' <span class="penciai_spinner hide_spin"></span></a><style>.components-button.penciai-content-generator-btn.penciai-button{padding:10px 12px;text-decoration:none;margin-left:0!important;height:auto!important}</style>';
	}


	public function getpromptHtml() {
		$placeholders = 'The future of the AI, Will Artificial Intelligence Lead to Job Losses?, Historical places of US, Quantum computing and its applications,Cybersecurity and data privacy,Climate change and its impact on the world,The future of AI,Artificial intelligence and machine learning,Sustainable energy sources and technologies,Global economic trends and forecasting,The future of transportation and mobility,Biotechnology and genetic engineering,The impact of social media on society,Blockchain technology and its uses,The future of healthcare and medicine,Space exploration and colonization,The future of work and the impact of automation,The impact of the internet on education,Virtual reality and its applications,The future of urban development and smart cities,Gaming and its impact on society,The future of food and agriculture';
		if ( ! empty( esc_attr( get_option( 'penciai-placeholders', '' ) ) ) ) {
			$placeholders .= ',' . esc_attr( get_option( 'penciai-placeholders', 'The history of AI' ) );
		}
		$title          = __( 'AI/GPT-3 Prompt', 'penci-ai' );
		$promptBoxTitle = apply_filters( 'penciai_promptbox_title', $title );

		$settingsInclude = apply_filters( 'penciai_metabox_settings', array(
			'content_settings',
			'advanced_settings',
			'response_panel',
			'language',
			'if_previously_failed',
			'super_fast_generation_mode',
			'post_title',
			'content_structure',
			'content_length',
			'add_excerpt',
			'include_keywords',
			'keyword_bold',
			'exclude_keywords',
			'writing_style',
			'writing_tone',
			'introduction',
			'conclusion',
			'cta',
			'generate_images',
			'select_titles_before_generate',
			'save_future'
		) );

		?>
		<div id="penciai-prompt-box-holder" style="display: none">
			<div id="penciai-promptbox"
			     class="edit-post-visual-editor__penciai-wrapper penciai-hidden">
				<div class="prompt-container clearfix">

					<?php if ( empty( get_theme_mod( 'penci_ai_api_key' ) ) ): ?>
						<div
							style="background-color: #b01111; padding: 0 10px; border-radius: 5px; text-align: left; border: 2px solid #a63838;margin-bottom: 10px;">
							<p style="  color: white;font-size: 14px;margin: 0.7em 0 !important;"><?php _e( 'OpenAI API key field is empty. To generate content, you must enter a valid API key first in the ', 'penci-ai' ); ?>
								<a target="_blank"
								   href="<?php echo add_query_arg( [ 'autofocus[section]' => 'penci_ai_api_section' ], admin_url( 'customize.php' ) ); ?>"
								   style="color: #b1ff05 !important;"><?php _e( 'settings panel', 'penci-ai' ); ?></a>.
							</p>
						</div>
					<?php endif; ?>

					<label for="prompt-input"><?php echo esc_attr( $promptBoxTitle ); ?></label> <br> <input
						id="prompt-input" type="text" name="prompt" class="clearfix"
						placeholder="<?php _e( 'Ameraca\'s historical places', 'penci-ai' ); ?>">
					<div id="ai-response" class="ai_response_hidden">
						<div class="code-box code-box-dark">
							<div class="code-box-header">
								<?php
								do_action( 'penciai_bedore_code_header' );
								?>
								<button class="code-box-copy-btn"
								        title="<?php _e( 'Copy to clipboard', 'penci-ai' ); ?>">
									<svg id="Layer_1" xmlns="http://www.w3.org/2000/svg"
									     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
									     viewBox="0 0 115.77 122.88" width="24px" height="25px"
									     style="margin:0; enable-background:new 0 0 115.77 122.88" xml:space="preserve"><style
											type="text/css">.st0 {
                                                fill-rule: evenodd;
                                                clip-rule: evenodd;
                                            }</style>
										<g>
											<path class="st0"
											      d="M89.62,13.96v7.73h12.19h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02v0.02 v73.27v0.01h-0.02c-0.01,3.84-1.57,7.33-4.1,9.86c-2.51,2.5-5.98,4.06-9.82,4.07v0.02h-0.02h-61.7H40.1v-0.02 c-3.84-0.01-7.34-1.57-9.86-4.1c-2.5-2.51-4.06-5.98-4.07-9.82h-0.02v-0.02V92.51H13.96h-0.01v-0.02c-3.84-0.01-7.34-1.57-9.86-4.1 c-2.5-2.51-4.06-5.98-4.07-9.82H0v-0.02V13.96v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07V0h0.02h61.7 h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02V13.96L89.62,13.96z M79.04,21.69v-7.73v-0.02h0.02 c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01 c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v64.59v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h12.19V35.65 v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07v-0.02h0.02H79.04L79.04,21.69z M105.18,108.92V35.65v-0.02 h0.02c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01 c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v73.27v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h61.7h0.02 v0.02c0.91,0,1.75-0.39,2.37-1.01c0.61-0.61,1-1.46,1-2.37h-0.02V108.92L105.18,108.92z"/>
										</g></svg><span style="color: #333;"><?php _e( 'Copy', 'penci-ai' ); ?></span>
								</button>
								<button class="code-box-insert-btn"
								        title="<?php _e( 'Insert as content in this post', 'penci-ai' ); ?>"
								        style="display: flex;align-items: center;">
									<svg version="1.1" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve">

										<g><g><g><path d="M934.2,65.7C897.1,28.6,852.2,10,799.4,10H200.6c-52.7,0-97.7,18.6-134.8,55.7C28.6,102.9,10,147.8,10,200.6v598.9c0,52.2,18.6,97,55.7,134.4c37.1,37.4,82.1,56.1,134.8,56.1h598.9c52.7,0,97.7-18.7,134.8-56.1c37.2-37.4,55.7-82.2,55.7-134.4V200.6C990,147.8,971.4,102.9,934.2,65.7z M881.1,799.5c0,22.7-8,42-23.8,57.8c-15.9,15.9-35.2,23.8-57.8,23.8H200.6c-22.7,0-42-7.9-57.8-23.8c-15.9-15.9-23.8-35.2-23.8-57.8V200.6c0-22.7,7.9-42,23.8-57.8c15.9-15.9,35.2-23.8,57.8-23.8h598.9c22.7,0,42,7.9,57.8,23.8c15.9,15.9,23.8,35.2,23.8,57.8V799.5z"/><path d="M745,445.6H554.5V255c0-7.9-2.5-14.5-7.7-19.6c-5.1-5.1-11.6-7.7-19.6-7.7h-54.4c-7.9,0-14.5,2.5-19.6,7.7c-5.1,5.1-7.7,11.6-7.7,19.6v190.6H255c-7.9,0-14.5,2.5-19.6,7.7c-5.1,5.1-7.7,11.6-7.7,19.6v54.4c0,7.9,2.5,14.5,7.7,19.6c5.1,5.1,11.6,7.7,19.6,7.7h190.6V745c0,7.9,2.5,14.5,7.7,19.6c5.1,5.1,11.6,7.6,19.6,7.6h54.4c8,0,14.5-2.5,19.6-7.6c5.1-5.1,7.7-11.6,7.7-19.6V554.4H745c7.9,0,14.5-2.6,19.6-7.7c5.1-5.1,7.6-11.6,7.6-19.6v-54.4c0-7.9-2.5-14.5-7.6-19.6C759.5,448.1,752.9,445.6,745,445.6z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></g>
</svg>
									<span><?php _e( 'Insert to Post', 'penci-ai' ); ?></span>
								</button>

								<?php
								do_action( 'penciai_after_code_header' );
								?>
							</div>
							<pre class="code-box-pre penciai-hidden"><textarea class="code-box-code"></textarea></pre>

							<div class="penciai-blog-post"></div>
							<?php
							do_action( 'penciai_codebox' );
							?>

							<div class="titles_before_content_generate" style="display: none"></div>

						</div>
					</div>
					<div class="ai-footer-buttons clearfix">
						<div class="promptbox-left-buttons">
							<?php if ( in_array( 'content_settings', $settingsInclude ) ): ?>
								<div class="penciai-promptbox-button">
									<a href="#" id="penciai-content-settings-btn" class="penciai-settings-btn"
									   data-settings="content-settings"><?php echo apply_filters( "penciai_content_settings_text", __( 'Content settings', 'penci-ai' ) ); ?></a>
								</div>
							<?php endif; ?>
							<?php if ( in_array( 'advanced_settings', $settingsInclude ) ): ?>
								<div class="penciai-promptbox-button">
									<a href="#" id="penciai-advanced-settings-btn" class="penciai-settings-btn"
									   data-settings="advanced-settings"><?php _e( 'API Settings', 'penci-ai' ); ?></a>
								</div>
							<?php endif; ?>
							<?php do_action( 'penciai_promptbox_footer_buttons' ); ?>
						</div>

						<button id="penciai-generate-ai-content" class="penciai-button generate-ai-content"
						        role="button"
						        style="float: right;">
							<span
								class="title"><?php echo apply_filters( 'penciai_generate_button_text', __( 'Generate', 'penci-ai' ) ); ?></span>
							<span class="penciai_spinner hide_spin"></span>
						</button>
						<span
							style="font-size: 13px;font-weight: normal;float: right;margin-right: 10px;margin-top: 7px;padding: 6px 4px;"
							class="generation-complete badge badge-success penciai-hidden"><?php _e( 'Generation Completed!', 'penci-ai' ); ?></span>
						<span
							style="font-size: 13px;font-weight: normal;float: right;margin-right: 10px;margin-top: 7px;padding: 6px 4px;"
							class="empty-prompt badge badge-danger penciai-hidden"><?php _e( 'Please enter a prompt to generate contents.', 'penci-ai' ); ?></span>
						<a href="#" style="float:right;margin-right: 10px;" id="penciai-cancel-btn"
						   class="penciai-cancel-btn penciai-hidden"><?php _e( 'Cancel', 'penci-ai' ); ?></a>
						<span
							style="font-size: 13px; font-weight: normal; float: right; margin-right: 10px; margin-top: 7px; padding: 6px 4px;"
							class="featured-image-generation-complete badge badge-success penciai-hidden"><?php _e( 'Featured image generation Completed!', 'penci-ai' ); ?></span>
					</div>

					<div class="penci-ai-customizer-notice" style="font-size: 12px;">
						<?php _e( 'If you want to change the default settings, please go to <a target="_blank" href="' . add_query_arg( [ 'autofocus[panel]' => 'penci_ai_panel' ], admin_url( 'customize.php' ) ) . '">this page</a>.', 'penci-ai' ); ?>
					</div>

					<form id="penciai-ai-inputs" action="">
						<div class="ai_response_hidden prompt-settings-item" id="content-settings"
						     data-tab="content-settings"> <!--ai_response_hidden-->

							<?php if ( in_array( 'content_settings', $settingsInclude ) ) : ?>
								<div class="content-settings-box code-box-dark">
									<div class="code-box-header">
										<div class="title"><?php _e( 'Content Settings', 'penci-ai' ); ?></div>
										<button class="minimize-btn"
										        title="<?php _e( 'Minimize content settings panel', 'penci-ai' ); ?>">
											<svg fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
											     id="minus" class="icon glyph" stroke="#000000" width="24px"
											     height="24px">
												<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
												<g id="SVGRepo_tracerCarrier" stroke-linecap="round"
												   stroke-linejoin="round"></g>
												<g id="SVGRepo_iconCarrier">
													<path d="M19,13H5a1,1,0,0,1,0-2H19a1,1,0,0,1,0,2Z"></path>
												</g>
											</svg>
										</button>
									</div>
									<div class="penciai-content-settings-panel penciai-settings-panel-item">


										<?php
										if ( in_array( 'language', $settingsInclude ) ) {
											penciai_languages_settings_items();
										}
										?>

										<?php do_action( 'penciai_after_promptbox_language' ); ?>


										<?php
										if ( in_array( 'select_titles_before_generate', $settingsInclude ) ) {
											penciai_select_title_before_generate_settings_items();
										}
										?>

										<?php
										if ( in_array( 'content_structure', $settingsInclude ) ) {
											penciai_content_structure_settings_item();
										}
										?>

										<?php
										if ( in_array( 'content_length', $settingsInclude ) ) {
											penciai_content_length_settings_items();
										}
										?>
										<?php if ( in_array( 'include_keywords', $settingsInclude ) ): ?>
											<div class="settings-item">
												<label
													for="penciai-include-keywords"><span><?php _e( 'Include Keywords', 'penci-ai' ); ?></span>
													<input id="penciai-include-keywords" class="content-settings-input"
													       type="text" value="" name="include-keywords"
													       placeholder="New York, Washington, Grand Canyon">
												</label>
												<p><?php _e( 'Enter some keywords to include these in generated content. Seperated by comma (,). AI may create related keywords of these keywords strong as well.', 'penci-ai' ); ?></p>
											</div>
										<?php endif; ?>
										<?php if ( in_array( 'keyword_bold', $settingsInclude ) ): ?>
											<div class="settings-item">
												<label
													for="penciai-mark-keywords"><span><?php _e( 'Mark Keywords as bold', 'penci-ai' ); ?></span>
													<input id="penciai-mark-keywords" class="content-settings-input"
													       type="checkbox" name="bold-keyword">
												</label>
												<p><?php _e( 'If checked then above keywords will bold/strong in the content.', 'penci-ai' ); ?></p>
											</div>
										<?php endif; ?>

										<?php if ( in_array( 'keyword_bold', $settingsInclude ) ): ?>
											<div class="settings-item">
												<label
													for="penciai-exclude-keywords"><span><?php _e( 'Exclude Keywords', 'penci-ai' ); ?></span>
													<input id="penciai-exclude-keywords" class="content-settings-input"
													       type="text" value="" name="exclude-keywords"
													       placeholder="Alaska, California, Nevada">
												</label>
												<p><?php _e( 'Enter some keywords to exclude these from generated content. Seperated by comma (,).', 'penci-ai' ); ?></p>
											</div>
										<?php endif; ?>

										<?php

										if ( in_array( 'writing_style', $settingsInclude ) ) {
											penciai_writing_styles_settings_item();
										}
										if ( in_array( 'writing_tone', $settingsInclude ) ) {
											penciai_writing_tone_settings_item();
										}

										if ( in_array( 'add_excerpt', $settingsInclude ) ) {
											penciai_add_excerpt_settings_items();
										}
										if ( in_array( 'introduction', $settingsInclude ) ) {
											penciai_add_introduction_settings_items();
										}
										if ( in_array( 'conclusion', $settingsInclude ) ) {
											penciai_add_conclusion_settings_items();
										}
										if ( in_array( 'generate_images', $settingsInclude ) ) {
											penciai_auto_generate_image_settings_items();
										}
										?>


									</div>


								</div>
							<?php endif; ?>
						</div>

						<?php if ( in_array( 'save_future', $settingsInclude ) ): ?>


							<div class="ai_response_hidden prompt-settings-item" id="advanced-settings"
							     data-tab="advanced-settings">
								<div class="advanced-settings-box code-box-dark">
									<div class="code-box-header">
										<div class="title"><?php _e( 'API Settings', 'penci-ai' ); ?></div>
										<button class="minimize-btn"
										        title="<?php _e( 'Minimize API settings panel', 'penci-ai' ); ?>">
											<svg fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
											     id="minus" class="icon glyph" stroke="#000000" width="24px"
											     height="24px">
												<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
												<g id="SVGRepo_tracerCarrier" stroke-linecap="round"
												   stroke-linejoin="round"></g>
												<g id="SVGRepo_iconCarrier">
													<path d="M19,13H5a1,1,0,0,1,0-2H19a1,1,0,0,1,0,2Z"></path>
												</g>
											</svg>
										</button>
									</div>
									<div class="penciai-advanced-settings-panel penciai-settings-panel-item">
										<?php
										penciai_api_settings();
										?>


									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php do_action( 'penciai_after_promptbox_fields' ); ?>
						<input type="hidden" name="from-penciai-settings" value="0">
						<input type="hidden" name="generation_session_key" id="generation_session_key" value="">

					</form>
					<?php do_action( 'penciai_after_promptbox_form' ); ?>

				</div>


			</div>


			<div class="hidden-fields penciai-hidden">
				<input type="hidden" name="introduction_text" class="introduction_text"
				       value="<?php echo get_theme_mod( 'penci_ai_intro_heading', 'Introduction' ); ?>">
				<input type="hidden" name="conclusion_text" class="conclusion_text"
				       value="<?php echo get_theme_mod( 'penci_ai_conclusion_heading', 'Conclusion' ); ?>">

				<input type="hidden" name="penciai_running_task" id="penciai_running_task" value="">
				<input type="hidden" name="penciai_recent_task_completed" id="penciai_recent_task_completed" value="">
				<input type="hidden" name="penciai_title_requested" id="penciai_title_requested" value="0">
				<input type="hidden" name="penciai_content_structure_requested" id="penciai_content_structure_requested"
				       value="0">
				<input type="hidden" name="penciai_introduction_requested" id="penciai_introduction_requested"
				       value="0">
				<input type="hidden" name="penciai_conclusion_requested" id="penciai_conclusion_requested" value="0">
				<input type="hidden" name="penciai_call_to_action_requested" id="penciai_call_to_action_requested"
				       value="0">
				<input type="hidden" name="penciai_current_topics_running" id="penciai_current_topics_running"
				       value="0">
				<input type="hidden" name="penciai_title_completed" id="penciai_title_completed" value="0">
				<input type="hidden" name="penciai_introduction_completed" id="penciai_introduction_completed"
				       value="0">
				<input type="hidden" name="penciai_conclusion_completed" id="penciai_conclusion_completed" value="0">
				<input type="hidden" name="penciai_call_to_action_completed" id="penciai_call_to_action_completed"
				       value="0">


				<input type="hidden" name="penciai_title" id="penciai_title" value="">
				<input type="hidden" name="penciai_content_structure_completed" id="penciai_content_structure_completed"
				       value="0">
				<input type="hidden" name="penciai_is_content_scroll" id="penciai_is_content_scrollable" value="1">
				<input type="hidden" name="penciai_is_generation_cancelled" id="penciai_is_generation_cancelled"
				       value="0">

				<input type="hidden" id="penciai-placeholders" value="<?php echo esc_html( $placeholders ); ?>">
				<input type="hidden" id="penciai-excerps" value="">
			</div>

		</div>

		<?php
	}

}



