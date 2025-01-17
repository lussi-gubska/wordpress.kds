<?php 

/**
 * Template For Html Email Template
 * 
 * Handles to return design for html email template
 * 
 * Override this template by copying it to yourtheme/follow-my-blog-post/emailtemplate/htmlemail.php
 *
 * @package Penci Bookmark Follow
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div style="background: #E6E6E6; width: 100%;">

	<div style="width: 80%; margin:0 auto; overflow:hidden; padding: 5px 0 10px 0;">
		
		<div style="background-color: #43A4D0; max-height: 8px !important; width: 100%; display: block;">&nbsp;</div>
		
		<div style="background: #EFEFEF; padding: 10px 20px; border-bottom: 1px solid #D0D0D0;">
			<strong><?php echo $site_name; ?></strong>
		</div>
		
		<div style="background: #ffffff; padding: 10px 20px;">
			<?php echo $message; ?>
		</div>
		
		<?php if( !empty( $unsubscribe_message ) ) { ?>
			
			<div style="background: #EFEFEF; padding: 10px 20px; border-top: 1px solid #D0D0D0; font-size: 11px;">
				<?php echo $unsubscribe_message; ?>
			</div>
			
		<?php } ?>
				
		<div style="background-color: #43A4D0; max-height: 4px !important; width: 100%; display: block;">&nbsp;</div>
		
	</div>
	
</div>