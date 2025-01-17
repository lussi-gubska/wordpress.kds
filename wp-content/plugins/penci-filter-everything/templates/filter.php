<?php
wp_enqueue_script( 'penci-post-filter-widget' );
if ( isset( $elementor_preset ) && $elementor_preset ) {
	$post_filter_data = $elementor_filter_data;
	$ajax_filter      = ! ( isset( $post_filter_data['filter_type'] ) && $post_filter_data['filter_type'] === 'button' );
	$reset_button     = isset( $post_filter_data['reset_button'] ) && $post_filter_data['reset_button'];
} else {
	$post_filter_data = get_post_meta( $filter, '_penci_filter_set', true );
	$post_filter_grn  = get_post_meta( $filter, '_penci_filter_options', true );
	$ajax_filter      = ! ( isset( $post_filter_grn['filter_type'] ) && $post_filter_grn['filter_type'] === 'button' );
	$reset_button     = isset( $post_filter_grn['reset_button'] ) && $post_filter_grn['reset_button'];
}

if ( empty( $post_filter_data ) ) {
	echo __( 'No filter found', 'penci-filter-everyting' );
}
$has_filter  = false;
$filter_type = isset( $post_filter_data['post_type'] ) && $post_filter_data['post_type'] ? $post_filter_data['post_type'] : 'post';
$filter_sets = isset( $post_filter_data['filter_set'] ) && $post_filter_data['filter_set'] ? $post_filter_data['filter_set'] : '';
$showcount   = isset( $post_filter_grn['show_counter'] ) && $post_filter_grn['show_counter'] ? $post_filter_grn['show_counter'] : false;


echo '<div class="penci-fte-groups">';
foreach ( $filter_sets as $filter_id => $filter_data ) {
	$has_filter   = true;
	$showcount    = isset( $filter_data['show_counter'] ) && $filter_data['show_counter'] ? $filter_data['show_counter'] : $showcount;
	$filter_title = isset( $filter_data['filter_title'] ) && $filter_data['filter_title'] ? $filter_data['filter_title'] : '';
	?>
    <div class="penci-fte-group">
		<?php if ( $filter_title ) : ?>
            <div class="penci-fte-title"><?php echo esc_html( $filter_title ); ?></div>
		<?php
		endif;
		$type = $filter_data['filter_by'];
		$view = $filter_data['filter_view'];
		if ( 'tax' == $type ) {
			$tax = $filter_data['filter_tax'];
			if ( isset( $elementor_preset ) && $elementor_preset ) {
				$term_args = [
					'taxonomy'   => $tax,
					'hide_empty' => isset( $filter_data['hide_empty'] ) ? $filter_data['hide_empty'] : false,
					'order'      => isset( $filter_data['tax_order'] ) ? $filter_data['tax_order'] : 'DESC',
					'order_by'   => isset( $filter_data['tax_order_by'] ) ? $filter_data['tax_order_by'] : 'ID',
					'number'     => isset( $filter_data['number'] ) ? $filter_data['number'] : 0,
					'title_li'   => '',
				];
			} else {
				$term_args = [
					'taxonomy'   => $tax,
					'hide_empty' => isset( $post_filter_grn['hide_empty'] ) ? $post_filter_grn['hide_empty'] : false,
					'order'      => isset( $post_filter_grn['tax_order'] ) ? $post_filter_grn['tax_order'] : 'DESC',
					'order_by'   => isset( $post_filter_grn['tax_order_by'] ) ? $post_filter_grn['tax_order_by'] : 'ID',
					'number'     => isset( $post_filter_grn['number'] ) ? $post_filter_grn['number'] : 0,
					'title_li'   => '',
				];
			}
			?>
            <div class="pc-widget-advanced-tax pcft-tax view-<?php echo $view; ?>">
				<?php
				$terms          = get_terms( $term_args );
				$current_filter = isset( $_GET['pcmtt'] ) && $_GET['pcmtt'] ? $_GET['pcmtt'] : '';
				$activeFilters  = explode( '|', $current_filter );
				if ( ! empty( $terms ) ) {

					if ( $view == 'select' || $view == 'multi-select' ) {
						$multi        = $view == 'multi-select' ? ' multiple="multiple"' : '';
						$parent_class = $ajax_filter ? ' pmfa-ajax' : ' pmfa-no-ajax';
						echo '<select class="pc-advanced-cat pmfa-chosen-select pmfa-t pcfe-ds-' . $view . $parent_class . '" name="' . $tax . '"' . $multi . '>';

						if ( ! $multi ) {
							echo '<option value="">' . get_theme_mod( 'penci_fte_selectopt', __( 'Select the option', 'penci-filter-everything' ) ) . '</option>';
						}

						foreach ( $terms as $index => $terms_data ) {
							$filterString = $terms_data->taxonomy . ':' . $terms_data->term_id;
							$class        = [];
							$class[]      = in_array( $filterString, $activeFilters ) ? 'pmfa pmfa-t added' : 'pmfa pmfa-t';
							$class[]      = 'action-' . $view;
							$class[]      = $ajax_filter ? 'pmfa-ajax' : 'pmfa-no-ajax';
							$count        = $showcount ? '<span class="count">(' . $terms_data->count . ')</span>' : '';
							echo '<option class="' . implode( ' ', $class ) . '" aria-label="Penci Filter" data-filter-key="' . $terms_data->taxonomy . '" data-filter-value="' . $terms_data->term_id . '" value="' . $terms_data->term_id . '">' . $terms_data->name . $count . '</option>';
						}
						echo '</select>';
					} else {
						echo '<ul class="pc-advanced-cat pcfe-ds-' . $view . '">';
						foreach ( $terms as $index => $terms_data ) {
							$filterString = $terms_data->taxonomy . ':' . $terms_data->term_id;
							$class        = [];
							$class[]      = in_array( $filterString, $activeFilters ) ? 'pmfa pmfa-t added' : 'pmfa pmfa-t';
							$class[]      = 'action-' . $view;
							$class[]      = $ajax_filter ? 'pmfa-ajax' : 'pmfa-no-ajax';
							$count        = $showcount ? '<span class="count">(' . $terms_data->count . ')</span>' : '';
							echo '<li><a class="' . implode( ' ', $class ) . '" aria-label="Penci Filter" data-filter-key="' . $terms_data->taxonomy . '" data-filter-value="' . $terms_data->term_id . '" href="' . get_term_link( $terms_data->term_id ) . '">' . $terms_data->name . $count . '</a></li>';
						}
						echo '</ul>';
					}
				} else {
					echo '<div class="pcft-notice">' . __( 'No data found', 'penci-filter-everything' ) . '</div>';
				}
				?>
            </div>
			<?php
		} else {
			$meta_key       = $filter_data['filter_custom_field'];
			$meta_fields    = $meta_key ? penci_fe_meta_values( $meta_key ) : '';
			$current_filter = isset( $_GET['pcmtf'] ) && $_GET['pcmtf'] ? $_GET['pcmtf'] : '';
			$activeFilters  = explode( '|', $current_filter );
			if ( ! empty( $meta_fields ) ) {
				echo '<div class="pc-widget-advanced-tax pcft-tax view-' . $view . '">';

				if ( $view == 'select' || $view == 'multi-select' ) {
					$multi        = $view == 'multi-select' ? ' multiple="multiple"' : '';
					$parent_class = $ajax_filter ? ' pmfa-ajax' : ' pmfa-no-ajax';
					echo '<select class="pc-advanced-cat pmfa-chosen-select pcfe-ds-' . $view . $parent_class . '" name="' . $meta_key . '"' . $multi . '>';

					if ( ! $multi ) {
						echo '<option value="">' . get_theme_mod( 'penci_fte_selectopt', __( 'Select the option', 'penci-filter-everything' ) ) . '</option>';
					}

					foreach ( $meta_fields as $meta_field ) {
						$filterString = $meta_key . ':' . $meta_field['meta_value'];
						$class        = [];
						$class[]      = in_array( $filterString, $activeFilters ) ? 'pmfa added' : 'pmfa';
						$class[]      = $meta_field['total'] ? 'has-post' : 'no-post';
						$count        = $showcount ? '<span class="count">(' . $meta_field['total'] . ')</span>' : '';
						echo '<option class="' . implode( ' ', $class ) . '" aria-label="Penci Filter" data-filter-key="' . $meta_key . '" data-filter-value="' . $meta_field['meta_value'] . '" value="' . $meta_field['meta_value'] . '">' . $meta_field['meta_value'] . $count . '</option>';
					}
					echo '</select>';
				} else {
					echo '<ul class="pc-advanced-cat pcfe-ds-' . $view . '">';
					foreach ( $meta_fields as $meta_field ) {
						$filterString = $meta_key . ':' . $meta_field['meta_value'];
						$class        = [];
						$class[]      = in_array( $filterString, $activeFilters ) ? 'pmfa added' : 'pmfa';
						$class[]      = $meta_field['total'] ? 'has-post' : 'no-post';
						$class[]      = $ajax_filter ? 'pmfa-ajax' : 'pmfa-no-ajax';
						$count        = $showcount ? '<span class="count">(' . $meta_field['total'] . ')</span>' : '';
						echo '<li><a class="' . implode( ' ', $class ) . '" aria-label="Penci Filter" data-filter-key="' . $meta_key . '" data-filter-value="' . $meta_field['meta_value'] . '" href="#">' . $meta_field['meta_value'] . $count . '</a></li>';
					}
					echo '</ul>';
				}

				echo '</div>';
			} else {
				echo '<div class="pcft-notice">' . get_theme_mod( 'penci_fte_nodata', __( 'No data found', 'penci-filter-everything' ) ) . '</div>';
			}
		}
		?>
    </div>
	<?php
}

$button_html = '';

if ( ! $ajax_filter ) {
	$button_html .= '<input class="filter_url" type="hidden" value="' . get_permalink() . '" name="filter_url" />';
	$button_html .= '<div class="pcft-button pcft-filter-btn">' . do_shortcode( get_theme_mod( 'penci_fte_apply', __( 'Filter', 'penci-filter-everything' ) ) ) . '</div>';
}

if ( $reset_button ) {
	$btn_class   = $ajax_filter ? 'pcft-ajax' : 'pcft-no-ajax';
	$button_html .= '<div class="pcft-button pcft-reset-btn ' . $btn_class . '">' . do_shortcode( get_theme_mod( 'penci_fte_reset', __( 'Reset', 'penci-filter-everything' ) ) ) . '</div>';
}

if ( $button_html && $has_filter ) {
	echo '<div class="pcft-buttons">' . $button_html . '</div>';
}
$filter_type = is_array($filter_type) ? implode('.', $filter_type) : ($filter_type ?? 'post');
echo '<input type="hidden" class="post_types" name="post_types" value="' . $filter_type . '">';
echo '</div>';