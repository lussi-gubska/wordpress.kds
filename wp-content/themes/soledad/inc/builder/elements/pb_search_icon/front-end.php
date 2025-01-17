<div id="top-search"
     class="pc-builder-element penci-top-search pcheader-icon top-search-classes <?php echo penci_get_builder_mod( 'penci_header_builder_search_icon_css_class' ); ?>">
    <a href="#" aria-label="Search" class="search-click pc-button-define-<?php echo penci_get_builder_mod( 'penci_header_search_icon_btn_style', 'customize' ); ?>">
        <i class="penciicon-magnifiying-glass"></i>
    </a>
    <div class="show-search pcbds-<?php echo penci_get_builder_mod( 'penci_header_search_style' ); ?>">
		<?php penci_search_form( [ 'innerclass' => true, 'innerclass_css' => 'pc-searchform-inner pc-eajxsearch' ] ); ?>
        <a href="#" aria-label="Close" class="search-click close-search"><i class="penciicon-close-button"></i></a>
    </div>
</div>
