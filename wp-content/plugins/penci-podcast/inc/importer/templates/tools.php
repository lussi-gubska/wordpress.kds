<?php
$current_tab = ( $_GET['tab'] ?? null );
$tabs        = [
	'import'         => [
		'title'    => __( "Import Feed", 'penci-podcast' ),
		'template' => 'importer-form.php'
	],
	'scheduled-list' => [
		'title'    => __( "Scheduled Imports", 'penci-podcast' ),
		'template' => 'importer-scheduled.php'
	],
];

if ( isset( $_GET['post_id'] ) && $current_tab === 'edit' ) {
	$tabs['edit'] = [
		'title'    => sprintf( __( "Edit Feed %s", 'penci-podcast' ), get_the_title( intval( $_GET['post_id'] ) ) ),
		'template' => 'importer-form.php'
	];
}


$tabs = apply_filters( PENCI_PODCAST_IMPORTER_ALIAS . '_tools_tabs', $tabs );

if ( ! isset( $tabs[ $current_tab ] ) ) {
	$current_tab = array_key_first( $tabs );
}

?>
<div class="wrap pencipdc-importer">
    <h1>
        <span><?php echo esc_html__( 'Import a Podcast', 'penci-podcast' ); ?></span>
    </h1>

    <nav class="nav-tab-wrapper">
		<?php foreach ( $tabs as $tab_alias => $tab_information ) : ?>
            <a href="edit.php?post_type=podcast&page=<?php echo PENCI_PODCAST_IMPORTER_PREFIX; ?>&tab=<?php echo esc_attr( $tab_alias ) . ( $tab_alias === 'edit' ? '&post_id=' . intval( $_GET['post_id'] ) : '' ); ?>"
               class="nav-tab<?php echo $tab_alias === $current_tab ? ' nav-tab-active' : '' ?>">
				<?php echo esc_html( $tab_information['title'] ); ?>
            </a>
		<?php endforeach; ?>
    </nav>

	<?php
	if ( isset( $tabs[ $current_tab ]['template'] ) ) {
		pencipdc_importer_load_template( $tabs[ $current_tab ]['template'] );
	} else if ( isset( $tabs[ $current_tab ]['content'] ) ) {
		echo( $tabs[ $current_tab ]['content'] );
	}
	?>
</div>