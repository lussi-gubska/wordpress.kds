<?php 

/**
 * Template For Unsubscribe Page
 * 
 * Handles to return design of unsubscribe
 * page
 * 
 * Override this template by copying it to yourtheme/follow-my-blog-post/unsubscribe.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
get_header();

	global $post;

	while ( have_posts() ) : the_post(); ?>

		<div class="site-content" id="primary">
		
			<div itemscope itemtype="http://schema.org/Product" id="content" role="main">
			
				<article class="post-<?php echo $post->ID;?> page type-page status-publish hentry" id="post-<?php echo $post->ID;?>">
				
					<header class="entry-header">
						<h1 itemprop="name" class="entry-title"><?php echo get_the_title( $post->ID );?></h1>
					</header> 
					
					<div class="entry-content">
					
						<?php 
								//do action to add in top of unsubscribe page
								do_action( 'penci_bl_unsubscribe_top' );
						?>
					
						<div class="penci_bl_inner_content">
						
							<?php 
								
									//do action to add unsubscribe content
									do_action( 'penci_bl_unsubscribe_content' );
							?>
							
						</div><!--.row-fluid-->
						
						<?php 
								//do action to add in top of unsubscribe
								do_action( 'penci_bl_unsubscribe_bottom' );
						?>
						
					</div><!--entry-content-->
					
				</article>
				
			</div><!--#content-->
			
		</div><!--site-content-->
	
<?php endwhile; // end of the loop.
	
	//get sidebar
	get_sidebar();
	
	//get footer
	get_footer();