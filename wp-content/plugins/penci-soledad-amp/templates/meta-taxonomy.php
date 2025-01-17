<?php $categories = get_the_category_list( ' ', '', $this->ID ); ?>
<?php if ( $categories ) : ?>
	<div class="penci-amp-tax-category amp-wp-meta amp-wp-tax-category">
		<?php echo $categories; ?>
	</div>
<?php endif; ?>