<div id="top-search" class="penci-top-search pcheader-icon top-search-classes">
    <a href="#" class="search-click" aria-label="Search">
        <i class="penciicon-magnifiying-glass"></i>
    </a>
    <div class="show-search pcbds-<?php echo get_theme_mod( 'penci_topbar_search_style','default' ); ?>">
		<?php penci_search_form( [ 'innerclass' => true, 'innerclass_css' => 'pc-searchform-inner pc-eajxsearch' ] ); ?>
        <a href="#" aria-label="Search" class="search-click close-search"><i class="penciicon-close-button"></i></a>
    </div>
</div>
