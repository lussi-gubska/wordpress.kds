<?php

namespace PenciAIContentGenerator;

class AjaxRequests {

	/**
	 * AjaxRequests constructor.
	 */
	public function __construct() {
		$this->require_ajax_files();
		$this->initAjaxClasses();
	}

	public function require_ajax_files() {

		require 'ajax-requests/get-ai-data.php';
		require 'ajax-requests/generate-placeholders.php';
		require 'ajax-requests/generate-image.php';
		require 'ajax-requests/get-intro-and-conc.php';
		require 'ajax-requests/suggest-post-titles.php';
		require 'ajax-requests/replace-post-titles.php';
		require 'ajax-requests/generate-variation-images.php';
		require 'ajax-requests/save-image-to-media-library.php';
	}

	public function initAjaxClasses() {
		new AjaxRequests\GetAIDATA( $this );
		new AjaxRequests\GeneratePlaceholders( $this );
		new AjaxRequests\GenerateImage( $this );
		new AjaxRequests\GetIntroAndConc( $this );
		new AjaxRequests\SuggestPostTitles( $this );
		new AjaxRequests\ReplacePostTitles( $this );
		new AjaxRequests\GenerateVariationImages( $this );
		new AjaxRequests\SaveMediaImageToMedia( $this );
	}


	public function is_response_has_error( $obj ) {
		if ( isset( $obj->error ) ) {
//            if ($obj->error->code == 'invalid_api_key'){
//                return __("API key is invalid. You can find your API key at https://platform.openai.com/account/api-keys", "penci-ai");
//            }elseif ($obj->error->type == "insufficient_quota"){
//                return __("You exceeded your OpenAI's current quota, please check your OpenAI plan and billing details.", "penci-ai");
//            }
//            elseif ($obj->error->type == "server_error"){
//                return __("The OpenAI server had an error while processing your request. Sorry about that!", "penci-ai");
//            }
//            else{
			return __( 'OpenAI says: ', 'penci-ai' ) . $obj->error->message;
//            }

		} else {
			return false;
		}
	}


}

new AjaxRequests();
