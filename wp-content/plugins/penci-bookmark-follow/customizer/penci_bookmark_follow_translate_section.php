<?php
$options = [];

$options[] = array(
	'id'    => 'pencibf_header_12',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Posts Follows', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_followtext',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'default'   => __( 'Bookmark', 'penci-bookmark-follow' ),
	'label'     => esc_html__( 'Text: Bookmark', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_followingtext',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'default'   => __( 'Bookmarked', 'penci-bookmark-follow' ),
	'label'     => esc_html__( 'Text: Bookmarked', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_unfollowtext',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'default'   => __( 'Un-bookmark', 'penci-bookmark-follow' ),
	'label'     => esc_html__( 'Text: Un-bookmark', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_03',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Author Follows', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_author_followtext',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'default'   => __( 'Follow', 'penci-bookmark-follow' ),
	'label'     => esc_html__( 'Text: Follow', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_author_followingtext',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'default'   => __( 'Following', 'penci-bookmark-follow' ),
	'label'     => esc_html__( 'Text: Following', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_author_unfollowtext',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'default'   => __( 'Unfollow', 'penci-bookmark-follow' ),
	'label'     => esc_html__( 'Text: Unfollow', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_heading_02',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-header',
	'label'     => esc_html__( 'Post Popup Content', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_success_title',
	'transport' => 'postMessage',
	'default'   => __( 'Success', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Success Heading Title', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_success_mess',
	'transport' => 'postMessage',
	'default'   => __( 'Post added to Bookmark', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Success Message', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_remove_title',
	'transport' => 'postMessage',
	'default'   => __( 'Removed', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Post Remove Heading Title', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_remove_mess',
	'transport' => 'postMessage',
	'default'   => __( 'Post remove from Bookmark list', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Post Remove Message', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_heading_03',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-header',
	'label'     => esc_html__( 'Author Popup Content', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_success_author_title',
	'transport' => 'postMessage',
	'default'   => __( 'Success', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Success Heading Title', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_success_author_mess',
	'transport' => 'postMessage',
	'default'   => __( 'Successfully add author from the favorite list', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Success Content', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_remove_author_title',
	'transport' => 'postMessage',
	'default'   => __( 'Removed', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Author Remove Title', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_remove_author_mess',
	'transport' => 'postMessage',
	'default'   => __( 'Successfully remove author from the favorite list', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Author Remove Message', 'penci-bookmark-follow' ),
);


$options[] = array(
	'id'        => 'pencibf_popup_heading_term',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-header',
	'label'     => esc_html__( 'Term Popup Content', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_success_term_title',
	'transport' => 'postMessage',
	'default'   => __( 'Success', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Success Heading Title', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_success_term_mess',
	'transport' => 'postMessage',
	'default'   => __( 'Successfully add category from the favorite list', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Success Content', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_remove_term_title',
	'transport' => 'postMessage',
	'default'   => __( 'Removed', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Author Remove Title', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_remove_term_mess',
	'transport' => 'postMessage',
	'default'   => __( 'Successfully remove category from the favorite list', 'penci-bookmark-follow' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Author Remove Message', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_other',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Other Texts', 'penci-bookmark-follow' ),
);

$texts = pencibg_default_text();

foreach ( $texts as $id => $text ) {
	$options[] = array(
		'id'      => 'pencibf_text_' . $id,
		'type'    => 'soledad-fw-text',
		'default' => $text,
		'label'   => esc_html__( 'Text:' . $text, 'penci-bookmark-follow' ),
	);
}

return $options;