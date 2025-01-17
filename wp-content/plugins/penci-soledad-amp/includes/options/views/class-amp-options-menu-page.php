<?php

class Penci_AMP_Options_Menu_Page {
	public function render() {
		?>
		<div class="ampoptions-admin-page">
			<h1><?php echo __( 'AMP Plugin Options', 'penci-amp' ) ?></h1>
				<p>
					<?php
						__( 'This admin panel menu contains configuration options for the AMP Plugin.',
							'penci-amp' );
					?>
				</p>
		</div>
		<?php
	}
}
