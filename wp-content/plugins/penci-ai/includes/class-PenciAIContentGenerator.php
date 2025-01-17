<?php

namespace PenciAIContentGenerator;

class PenciAIContentGenerator {
	public function run() {
		$this->require_dependencies();
	}

	public function require_dependencies() {
		//This class is dependent for all admin functionalities
		require PENCI_AI_DIR_PATH . 'includes/global-functions.php';
		require PENCI_AI_DIR_PATH . 'includes/OpenAi.php';
		require PENCI_AI_DIR_PATH . 'admin/admin.php';
		new PenciAI_Admin();
	}
}