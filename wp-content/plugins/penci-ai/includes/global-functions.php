<?php

ini_set( 'max_execution_time', '500' );
function penciai_checkNonce() {
	$nonce = isset( $_POST['rc_nonce'] ) ? sanitize_key( $_POST['rc_nonce'] ) : "";
	if ( ! empty( $nonce ) ) {
		if ( ! wp_verify_nonce( $nonce, "rc-nonce" ) ) {
			wp_send_json_error( 'nonce_verify_error' );
			die();
		}
	}
}

if ( ! function_exists( 'rc_isJson' ) ) {
	function rc_isJson( $str ) {
		json_decode( $str );

		return ( json_last_error() == JSON_ERROR_NONE );
	}
}

if ( ! function_exists( 'rc_extractJson' ) ) {
	function rc_extractJson( $str ) {
		preg_match( '/({.*})/', $str, $match );
		if ( count( $match ) > 0 ) {
			return $match[0];
		} else {
			return null;
		}
	}
}

function penciai_get_post_types() {
	$exclude_types = array( 'attachment', 'revision', 'nav_menu_item', 'oembed_cache', 'user_request' );
	$args          = array(
		'public' => true,
	);
	$output        = 'names'; // names or objects, note names is the default
	$operator      = 'and'; // 'and' or 'or'
	$post_types    = get_post_types( $args, $output, $operator );
	$post_types    = array_diff( $post_types, $exclude_types );

	return $post_types;
}

function penciai_add_select_option( $name, $value = "", $isSelected = false, $id = "", $echo = true, $isDisabled = false ) {
	if ( ! empty( $name ) && empty( $value ) ) {
		$value = str_replace( array( ' ', '-' ), '', strtolower( $name ) );
	}
	$isSelected = $isSelected ? 'selected' : '';
	$isDisabled = $isDisabled ? 'disabled' : '';
	if ( $echo ) {
		if ( ! empty( $id ) ) {
			echo '<option id="penciai-' . esc_attr( $id ) . '" ' . esc_attr( $isSelected ) . ' value="' . esc_attr( $value ) . '" ' . $isDisabled . '> ' . esc_attr( $name ) . '</option>';
		} else {
			echo '<option ' . esc_attr( $isSelected ) . ' value="' . esc_attr( $value ) . '" ' . $isDisabled . '> ' . esc_attr( $name ) . '</option>';
		}
	} else {
		if ( ! empty( $id ) ) {
			return '<option id="penciai-' . esc_attr( $id ) . '" ' . esc_attr( $isSelected ) . ' value="' . esc_attr( $value ) . '" ' . $isDisabled . '> ' . esc_attr( $name ) . '</option>';
		} else {
			return '<option ' . esc_attr( $isSelected ) . ' value="' . esc_attr( $value ) . '" ' . $isDisabled . '> ' . esc_attr( $name ) . '</option>';
		}
	}

}

function pcacgHasAccess() {
	require( ABSPATH . WPINC . '/pluggable.php' );
	$capabilities = get_theme_mod( 'penci_ai_user_roles', array( 'administrator' ) );

	if ( ! empty( $capabilities ) ) {
		foreach ( $capabilities as $cap ) {
			if ( current_user_can( $cap ) ) {
				return true;
				break;
			}
		}
	}
	if ( current_user_can( 'administrator' ) ) {
		return true;
	}

	return false;
}

function penciai_get_post_type() {
	global $pagenow;

	$post_type = null;

	if ( empty( $post_type ) && isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ) {
		$post_type = sanitize_key( $_REQUEST['post_type'] );
	}

	if ( empty( $post_type ) && 'edit.php' == $pagenow ) {
		$post_type = 'post';
	}

	if ( empty( $post_type ) && 'post-new.php' == $pagenow ) {
		$post_type = 'post';
	}

	if ( empty( $post_type ) && isset( $_REQUEST['post'] ) && ! empty( $_REQUEST['post'] ) && function_exists( 'get_post_type' ) && $get_post_type = get_post_type( (int) $_REQUEST['post'] ) ) {
		$post_type = $get_post_type;
	}

	return $post_type;
}

function penciai_get_content_structure_options() {
	penciai_add_select_option( __( 'Topic-Wise', 'penci-ai' ), 'topic_wise', esc_attr( get_theme_mod( 'penci_ai_content_structure', 'topic_wise' ) ) == 'topic_wise' );
	penciai_add_select_option( __( 'Article', 'penci-ai' ), 'article', esc_attr( get_theme_mod( 'penci_ai_content_structure', 'topic_wise' ) ) == 'article' );
	penciai_add_select_option( __( 'Review', 'penci-ai' ), 'review', esc_attr( get_theme_mod( 'penci_ai_content_structure', 'topic_wise' ) ) == 'review' );
	penciai_add_select_option( __( 'Opinion', 'penci-ai' ), 'opinion', esc_attr( get_theme_mod( 'penci_ai_content_structure', 'topic_wise' ) ) == 'opinion' );
	penciai_add_select_option( __( 'FAQ', 'penci-ai' ), 'faq', esc_attr( get_theme_mod( 'penci_ai_content_structure', 'topic_wise' ) ) == 'faq' );
}


function penciai_get_topics_tag_options() {
	penciai_add_select_option( __( 'h1', 'penci-ai' ), 'h2', esc_attr( get_theme_mod( 'penci_ai_number_headings_tag', 'h3' ) ) == 'h1' );
	penciai_add_select_option( __( 'h2', 'penci-ai' ), 'h2', esc_attr( get_theme_mod( 'penci_ai_number_headings_tag', 'h3' ) ) == 'h2' );
	penciai_add_select_option( __( 'h3', 'penci-ai' ), 'h2', esc_attr( get_theme_mod( 'penci_ai_number_headings_tag', 'h3' ) ) == 'h3' );
	penciai_add_select_option( __( 'h4', 'penci-ai' ), 'h2', esc_attr( get_theme_mod( 'penci_ai_number_headings_tag', 'h3' ) ) == 'h4' );
	penciai_add_select_option( __( 'h5', 'penci-ai' ), 'h2', esc_attr( get_theme_mod( 'penci_ai_number_headings_tag', 'h3' ) ) == 'h5' );
	penciai_add_select_option( __( 'h6', 'penci-ai' ), 'h2', esc_attr( get_theme_mod( 'penci_ai_number_headings_tag', 'h3' ) ) == 'h6' );
}


function penciai_get_writing_tone_options() {
	$key = 'penci_ai__';
	penciai_add_select_option( __( 'Informative', 'penci-ai' ), 'informative', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'informative' );
	penciai_add_select_option( __( 'Professional', 'penci-ai' ), 'professional', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'professional' );
	penciai_add_select_option( __( 'Approachable', 'penci-ai' ), 'approachable', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'approachable' );
	penciai_add_select_option( __( 'Confident', 'penci-ai' ), 'confident', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'confident' );
	penciai_add_select_option( __( 'Enthusiastic', 'penci-ai' ), 'enthusiastic', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'enthusiastic' );
	penciai_add_select_option( __( 'Casual', 'penci-ai' ), 'casual', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'casual' );
	penciai_add_select_option( __( 'Respectful', 'penci-ai' ), 'respectful', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'respectful' );
	penciai_add_select_option( __( 'Sarcastic', 'penci-ai' ), 'sarcastic', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'sarcastic' );
	penciai_add_select_option( __( 'Serious', 'penci-ai' ), 'serious', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'serious' );
	penciai_add_select_option( __( 'Thoughtful', 'penci-ai' ), 'thoughtful', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'thoughtful' );
	penciai_add_select_option( __( 'Witty', 'penci-ai' ), 'witty', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'witty' );
	penciai_add_select_option( __( 'Passionate', 'penci-ai' ), 'passionate', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'passionate' );
	penciai_add_select_option( __( 'Lighthearted', 'penci-ai' ), 'lighthearted', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'lighthearted' );
	penciai_add_select_option( __( 'Hilarious', 'penci-ai' ), 'hilarious', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'hilarious' );
	penciai_add_select_option( __( 'Soothing', 'penci-ai' ), 'soothing', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'soothing' );
	penciai_add_select_option( __( 'Emotional', 'penci-ai' ), 'emotional', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'emotional' );
	penciai_add_select_option( __( 'Inspirational', 'penci-ai' ), 'inspirational', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'inspirational' );
	penciai_add_select_option( __( 'Objective', 'penci-ai' ), 'objective', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'objective' );
	penciai_add_select_option( __( 'Persuasive', 'penci-ai' ), 'persuasive', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'persuasive' );
	penciai_add_select_option( __( 'Vivid', 'penci-ai' ), 'vivid', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'vivid' );
	penciai_add_select_option( __( 'Imaginative', 'penci-ai' ), 'imaginative', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'imaginative' );
	penciai_add_select_option( __( 'Musical', 'penci-ai' ), 'musical', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'musical' );
	penciai_add_select_option( __( 'Rhythmical', 'penci-ai' ), 'rhythmical', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'rhythmical' );
	penciai_add_select_option( __( 'Humorous', 'penci-ai' ), 'humorous', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'humorous' );
	penciai_add_select_option( __( 'Critical', 'penci-ai' ), 'critical', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'critical' );
	penciai_add_select_option( __( 'Clear', 'penci-ai' ), 'clear', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'clear' );
	penciai_add_select_option( __( 'Neutral', 'penci-ai' ), 'neutral', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'neutral' );
	penciai_add_select_option( __( 'Objective', 'penci-ai' ), 'objective', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'objective' );
	penciai_add_select_option( __( 'Biased', 'penci-ai' ), 'biased', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'biased' );
	penciai_add_select_option( __( 'Passionate', 'penci-ai' ), 'passionate', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'passionate' );
	penciai_add_select_option( __( 'Argumentative', 'penci-ai' ), 'argumentative', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'argumentative' );
	penciai_add_select_option( __( 'Reflective', 'penci-ai' ), 'reflective', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'reflective' );
	penciai_add_select_option( __( 'Helpful', 'penci-ai' ), 'helpful', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'helpful' );
	penciai_add_select_option( __( 'Connective', 'penci-ai' ), 'connective', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'connective' );
	penciai_add_select_option( __( 'Assertive', 'penci-ai' ), 'assertive', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'assertive' );
	penciai_add_select_option( __( 'Energetic', 'penci-ai' ), 'energetic', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'energetic' );
	penciai_add_select_option( __( 'Relaxed', 'penci-ai' ), 'relaxed', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'relaxed' );
	penciai_add_select_option( __( 'Polite', 'penci-ai' ), 'polite', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'polite' );
	penciai_add_select_option( __( 'Clever', 'penci-ai' ), 'clever', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'clever' );
	penciai_add_select_option( __( 'Funny', 'penci-ai' ), 'funny', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'funny' );
	penciai_add_select_option( __( 'Amusing', 'penci-ai' ), 'amusing', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'amusing' );
	penciai_add_select_option( __( 'Comforting', 'penci-ai' ), 'comforting', esc_attr( get_theme_mod( 'penci_ai_writing_tone', 'informative' ) ) == 'comforting' );
}

function penciai_get_writing_styles_options() {
	penciai_add_select_option( __( 'normal', 'penci-ai' ), 'normal', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'normal' );
	penciai_add_select_option( __( 'business', 'penci-ai' ), 'business', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'business' );
	penciai_add_select_option( __( 'legal', 'penci-ai' ), 'legal', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'legal' );
	penciai_add_select_option( __( 'technical', 'penci-ai' ), 'technical', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'technical' );
	penciai_add_select_option( __( 'marketing', 'penci-ai' ), 'marketing', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'marketing' );
	penciai_add_select_option( __( 'creative', 'penci-ai' ), 'creative', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'creative' );
	penciai_add_select_option( __( 'narrative', 'penci-ai' ), 'narrative', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'narrative' );
	penciai_add_select_option( __( 'expository', 'penci-ai' ), 'expository', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'expository' );
	penciai_add_select_option( __( 'reflective', 'penci-ai' ), 'reflective', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'reflective' );
	penciai_add_select_option( __( 'persuasive', 'penci-ai' ), 'persuasive', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'persuasive' );
	penciai_add_select_option( __( 'descriptive', 'penci-ai' ), 'descriptive', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'descriptive' );
	penciai_add_select_option( __( 'instructional', 'penci-ai' ), 'instructional', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'instructional' );
	penciai_add_select_option( __( 'news', 'penci-ai' ), 'news', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'news' );
	penciai_add_select_option( __( 'personal', 'penci-ai' ), 'personal', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'personal' );
	penciai_add_select_option( __( 'travel', 'penci-ai' ), 'travel', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'travel' );
	penciai_add_select_option( __( 'recipe', 'penci-ai' ), 'recipe', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'recipe' );
	penciai_add_select_option( __( 'poetic', 'penci-ai' ), 'poetic', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'poetic' );
	penciai_add_select_option( __( 'satirical', 'penci-ai' ), 'satirical', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'satirical' );
	penciai_add_select_option( __( 'formal', 'penci-ai' ), 'formal', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'formal' );
	penciai_add_select_option( __( 'informal', 'penci-ai' ), 'informal', esc_attr( get_theme_mod( 'penci_ai_writing_style', 'normal' ) ) == 'informal' );
}

function penciai_get_languages_list() {
	return [
		'de'        => __( 'Deutsch', 'penci-ai' ),
		'en'        => __( 'English', 'penci-ai' ),
		'es'        => __( 'español', 'penci-ai' ),
		'es-419'    => __( 'español (Latinoamérica)', 'penci-ai' ),
		'fr'        => __( 'français', 'penci-ai' ),
		'hr'        => __( 'hrvatski', 'penci-ai' ),
		'it'        => __( 'italiano', 'penci-ai' ),
		'nl'        => __( 'Nederlands', 'penci-ai' ),
		'pl'        => __( 'polski', 'penci-ai' ),
		'pt-BR'     => __( 'português (Brasil)', 'penci-ai' ),
		'pt-PT'     => __( 'português (Portugal)', 'penci-ai' ),
		'vi'        => __( 'Tiếng Việt', 'penci-ai' ),
		'tr'        => __( 'Türkçe', 'penci-ai' ),
		'ru'        => __( 'русский', 'penci-ai' ),
		'ar'        => __( 'العربية', 'penci-ai' ),
		'th'        => __( 'ไทย', 'penci-ai' ),
		'ko'        => __( '한국어', 'penci-ai' ),
		'zh-CN'     => __( '中文 (简体)', 'penci-ai' ),
		'zh-TW'     => __( '中文 (繁體)', 'penci-ai' ),
		'zh-HK'     => __( '香港中文', 'penci-ai' ),
		'ja'        => __( '日本語', 'penci-ai' ),
		'ach'       => __( 'Acoli', 'penci-ai' ),
		'af'        => __( 'Afrikaans', 'penci-ai' ),
		'ak'        => __( 'Akan', 'penci-ai' ),
		'az'        => __( 'azərbaycan', 'penci-ai' ),
		'ban'       => __( 'Balinese', 'penci-ai' ),
		'su'        => __( 'Basa Sunda', 'penci-ai' ),
		'xx-bork'   => __( 'Bork, bork, bork!', 'penci-ai' ),
		'bs'        => __( 'bosanski', 'penci-ai' ),
		'br'        => __( 'brezhoneg', 'penci-ai' ),
		'ca'        => __( 'català', 'penci-ai' ),
		'ceb'       => __( 'Cebuano', 'penci-ai' ),
		'cs'        => __( 'čeština', 'penci-ai' ),
		'sn'        => __( 'chiShona', 'penci-ai' ),
		'co'        => __( 'Corsican', 'penci-ai' ),
		'ht'        => __( 'créole haïtien', 'penci-ai' ),
		'cy'        => __( 'Cymraeg', 'penci-ai' ),
		'da'        => __( 'dansk', 'penci-ai' ),
		'yo'        => __( 'Èdè Yorùbá', 'penci-ai' ),
		'et'        => __( 'eesti', 'penci-ai' ),
		'xx-elmer'  => __( 'Elmer Fudd', 'penci-ai' ),
		'eo'        => __( 'esperanto', 'penci-ai' ),
		'eu'        => __( 'euskara', 'penci-ai' ),
		'ee'        => __( 'Eʋegbe', 'penci-ai' ),
		'tl'        => __( 'Filipino', 'penci-ai' ),
		'fil'       => __( 'Filipino', 'penci-ai' ),
		'fo'        => __( 'føroyskt', 'penci-ai' ),
		'fy'        => __( 'Frysk', 'penci-ai' ),
		'gaa'       => __( 'Ga', 'penci-ai' ),
		'ga'        => __( 'Gaeilge', 'penci-ai' ),
		'gd'        => __( 'Gàidhlig', 'penci-ai' ),
		'gl'        => __( 'galego', 'penci-ai' ),
		'gn'        => __( 'Guarani', 'penci-ai' ),
		'xx-hacker' => __( 'Hacker', 'penci-ai' ),
		'ha'        => __( 'Hausa', 'penci-ai' ),
		'haw'       => __( 'ʻŌlelo Hawaiʻi', 'penci-ai' ),
		'bem'       => __( 'Ichibemba', 'penci-ai' ),
		'ig'        => __( 'Igbo', 'penci-ai' ),
		'rn'        => __( 'Ikirundi', 'penci-ai' ),
		'id'        => __( 'Indonesia', 'penci-ai' ),
		'ia'        => __( 'interlingua', 'penci-ai' ),
		'xh'        => __( 'IsiXhosa', 'penci-ai' ),
		'zu'        => __( 'isiZulu', 'penci-ai' ),
		'is'        => __( 'íslenska', 'penci-ai' ),
		'jw'        => __( 'Jawa', 'penci-ai' ),
		'rw'        => __( 'Kinyarwanda', 'penci-ai' ),
		'sw'        => __( 'Kiswahili', 'penci-ai' ),
		'tlh'       => __( 'Klingon', 'penci-ai' ),
		'kg'        => __( 'Kongo', 'penci-ai' ),
		'mfe'       => __( 'kreol morisien', 'penci-ai' ),
		'kri'       => __( 'Krio (Sierra Leone)', 'penci-ai' ),
		'la'        => __( 'Latin', 'penci-ai' ),
		'lv'        => __( 'latviešu', 'penci-ai' ),
		'to'        => __( 'lea fakatonga', 'penci-ai' ),
		'lt'        => __( 'lietuvių', 'penci-ai' ),
		'ln'        => __( 'lingála', 'penci-ai' ),
		'loz'       => __( 'Lozi', 'penci-ai' ),
		'lua'       => __( 'Luba-Lulua', 'penci-ai' ),
		'lg'        => __( 'Luganda', 'penci-ai' ),
		'hu'        => __( 'magyar', 'penci-ai' ),
		'mg'        => __( 'Malagasy', 'penci-ai' ),
		'mt'        => __( 'Malti', 'penci-ai' ),
		'mi'        => __( 'Māori', 'penci-ai' ),
		'ms'        => __( 'Melayu', 'penci-ai' ),
		'pcm'       => __( 'Nigerian Pidgin', 'penci-ai' ),
		'no'        => __( 'norsk', 'penci-ai' ),
		'nn'        => __( 'norsk nynorsk', 'penci-ai' ),
		'nso'       => __( 'Northern Sotho', 'penci-ai' ),
		'ny'        => __( 'Nyanja', 'penci-ai' ),
		'uz'        => __( 'o‘zbek', 'penci-ai' ),
		'oc'        => __( 'Occitan', 'penci-ai' ),
		'om'        => __( 'Oromoo', 'penci-ai' ),
		'xx-pirate' => __( 'Pirate', 'penci-ai' ),
		'ro'        => __( 'română', 'penci-ai' ),
		'rm'        => __( 'rumantsch', 'penci-ai' ),
		'qu'        => __( 'Runasimi', 'penci-ai' ),
		'nyn'       => __( 'Runyankore', 'penci-ai' ),
		'crs'       => __( 'Seychellois Creole', 'penci-ai' ),
		'sq'        => __( 'shqip', 'penci-ai' ),
		'sk'        => __( 'slovenčina', 'penci-ai' ),
		'sl'        => __( 'slovenščina', 'penci-ai' ),
		'so'        => __( 'Soomaali', 'penci-ai' ),
		'st'        => __( 'Southern Sotho', 'penci-ai' ),
		'sr-ME'     => __( 'srpski (Crna Gora)', 'penci-ai' ),
		'sr-Latn'   => __( 'srpski (latinica)', 'penci-ai' ),
		'fi'        => __( 'suomi', 'penci-ai' ),
		'sv'        => __( 'svenska', 'penci-ai' ),
		'tn'        => __( 'Tswana', 'penci-ai' ),
		'tum'       => __( 'Tumbuka', 'penci-ai' ),
		'tk'        => __( 'türkmen dili', 'penci-ai' ),
		'tw'        => __( 'Twi', 'penci-ai' ),
		'wo'        => __( 'Wolof', 'penci-ai' ),
		'el'        => __( 'Ελληνικά', 'penci-ai' ),
		'be'        => __( 'беларуская', 'penci-ai' ),
		'bg'        => __( 'български', 'penci-ai' ),
		'ky'        => __( 'кыргызча', 'penci-ai' ),
		'kk'        => __( 'қазақ тілі', 'penci-ai' ),
		'mk'        => __( 'македонски', 'penci-ai' ),
		'mn'        => __( 'монгол', 'penci-ai' ),
		'sr'        => __( 'српски', 'penci-ai' ),
		'tt'        => __( 'татар', 'penci-ai' ),
		'tg'        => __( 'тоҷикӣ', 'penci-ai' ),
		'uk'        => __( 'українська', 'penci-ai' ),
		'ka'        => __( 'ქართული', 'penci-ai' ),
		'hy'        => __( 'հայերեն', 'penci-ai' ),
		'yi'        => __( 'ייִדיש', 'penci-ai' ),
		'iw'        => __( 'עברית', 'penci-ai' ),
		'ug'        => __( 'ئۇيغۇرچە', 'penci-ai' ),
		'ur'        => __( 'اردو', 'penci-ai' ),
		'ps'        => __( 'پښتو', 'penci-ai' ),
		'sd'        => __( 'سنڌي', 'penci-ai' ),
		'fa'        => __( 'فارسی', 'penci-ai' ),
		'ckb'       => __( 'کوردیی ناوەندی', 'penci-ai' ),
		'ti'        => __( 'ትግርኛ', 'penci-ai' ),
		'am'        => __( 'አማርኛ', 'penci-ai' ),
		'bn'        => __( 'বাংলা', 'penci-ai' ),
		'ne'        => __( 'नेपाली', 'penci-ai' ),
		'mr'        => __( 'मराठी', 'penci-ai' ),
		'hi'        => __( 'हिन्दी', 'penci-ai' ),
		'pa'        => __( 'ਪੰਜਾਬੀ', 'penci-ai' ),
		'gu'        => __( 'ગુજરાતી', 'penci-ai' ),
		'or'        => __( 'ଓଡ଼ିଆ', 'penci-ai' ),
		'ta'        => __( 'தமிழ்', 'penci-ai' ),
		'Assamese'  => __( 'অসমীয়া', 'penci-ai' ),
		'te'        => __( 'తెలుగు', 'penci-ai' ),
		'kn'        => __( 'ಕನ್ನಡ', 'penci-ai' ),
		'ml'        => __( 'മലയാളം', 'penci-ai' ),
		'si'        => __( 'සිංහල', 'penci-ai' ),
		'lo'        => __( 'ລາວ', 'penci-ai' ),
		'my'        => __( 'မြန်မာ', 'penci-ai' ),
		'km'        => __( 'ខ្មែរ', 'penci-ai' ),
		'chr'       => __( 'ᏣᎳᎩ', 'penci-ai' ),
	];
}

function penciai_get_languages_options() {

	$current_lang = get_theme_mod( 'penci_ai_language', "en" );

	foreach ( penciai_get_languages_list() as $lang_code => $lang_name ) {
		echo '<option ' . selected( $lang_code, $current_lang ) . ' data-name="' . $lang_name . '" id="d' . $lang_code . '" value="' . $lang_code . '">' . $lang_name . '</option>';
	}
}


function penciai_upload_image_to_media_gallery( $url, $data = array() ) {
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$image_url = 'http://example.com/image.jpg';

	$tmp = download_url( $url );

	$file_array = array(
		'name'     => basename( $image_url ),
		'tmp_name' => $tmp
	);

	$id = media_handle_sideload( $file_array, 0 );

	if ( is_wp_error( $id ) ) {
		@unlink( $file_array['tmp_name'] );

		return $id;
	}
	$attachment        = array();
	$attachment['id']  = $id;
	$attachment['url'] = wp_get_attachment_url( $id );

	return $attachment;
}



