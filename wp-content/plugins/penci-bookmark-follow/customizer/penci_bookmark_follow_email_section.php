<?php
$options = [];

$options[] = array(
	'id'          => 'pencibf_from_email',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'From Email Address', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'When enable, Registered users will be sent a confirmation email to subscribe, and will only be added once they confirmed the subscription', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_enable_unsubscribe_url',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Add Unsubscribe link to email Message', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_unsubscribe_message',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-textarea',
	'value'       => 'If you want to unsubscribe, click on {unsubscribe_url}',
	'label'       => esc_html__( 'Unsubscribe Message', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'Enter the custom Unsubscribe Message. Available template tags for unsubscribe message are: {unsubscribe_url} - displays the unsubscribe url for unsubscribe email', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_notify_01',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Edit Post Subscription Email Template', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_email_subject',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-text',
	'default'     => __( 'Post {post_name} updated at {site_name}', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Subject', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'This is the subject of the email that will be sent to the followers of that post when post is updated. Available template tags for subject fields are :{post_name} - displays the title of the post, {site_name} - displays the name of your site ', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_email_body',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-textarea',
	'default'     => __( 'Post {post_name} updated If you want to see page click below link {post_link} for {site_link}', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Body', 'penci-bookmark-follow' ),
	'description' => __( 'This is the body, main content of the email that will be sent to the followers of that post when post is updated.<br>The available tags are:
<code>{post_name}</code> - displays the title of the post<br>
<code>{post_link}</code> - displays the post title with link<br>
<code>{site_name}</code> - displays the name of your site<br>
<code>{site_link}</code> - displays the site name with link', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_notify_03',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'New Post Author Subscription Email Template', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_author_email_subject',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-text',
	'default'     => __( 'Post {post_name} updated at {site_name}', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Subject', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'This is the subject of the email that will be sent to the followers of that post when post is updated. Available template tags for subject fields are :{post_name} - displays the title of the post, {site_name} - displays the name of your site ', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_author_email_body',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-textarea',
	'default'     => __( 'New post added by the author "{author_name}": {post_name} {post_description} If you want to see page click below link {post_link} for {site_link}', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Body', 'penci-bookmark-follow' ),
	'description' => __( 'This is the body, main content of the email that will be sent to the followers of that post when post is updated.<br>The available tags are:
<code>{post_name}</code> - displays the title of the post<br>
<code>{post_link}</code> - displays the post title with link<br>
<code>{site_name}</code> - displays the name of your site<br>
<code>{site_link}</code> - displays the site name with link', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_notify_04',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Comment Subscription Email Template', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_comment_email_subject',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-text',
	'default'     => __( 'New comment on "{post_name}" by {user_name}', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Subject', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'This is the subject of the email that will be sent to the followers of that post when post is updated. Available template tags for subject fields are :{post_name} - displays the title of the post, {site_name} - displays the name of your site ', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_comment_email_body',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-textarea',
	'default'     => __( 'New comment added on the post "{post_name}" by {user_name}, see below : {comment_text}', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Body', 'penci-bookmark-follow' ),
	'description' => __( 'This is the body, main content of the email that will be sent to the followers of that post when post is updated.<br>The available tags are:
<code>{post_name}</code> - displays the title of the post<br>
<code>{post_link}</code> - displays the post title with link<br>
<code>{site_name}</code> - displays the name of your site<br>
<code>{site_link}</code> - displays the site name with link', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_notify_05',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Posts Confirmation Email Template', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_confirm_email_subject',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-text',
	'default'     => __( 'Follow {post_name} - {site_name}', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Subject', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'This is the subject of the email that will be sent to the followers of that post when post is updated. Available template tags for subject fields are :{post_name} - displays the title of the post, {site_name} - displays the name of your site ', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_confirm_email_body',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-textarea',
	'default'     => __( 'Hello You recently followed below blog post. This means you will receive an email when post is updated. Blog Post URL: {post_link} To activate, click confirm below. If you did not request this, please feel free to disregard this notice! {subscribe_url} Thanks', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Body', 'penci-bookmark-follow' ),
	'description' => __( 'This is the body, main content of the email that will be sent to the followers of that post when post is updated.<br>The available tags are:
<code>{post_name}</code> - displays the title of the post<br>
<code>{post_link}</code> - displays the post title with link<br>
<code>{site_name}</code> - displays the name of your site<br>
<code>{site_link}</code> - displays the site name with link', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_notify_07',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Author Confirmation Email Template', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_author_confirm_email_subject',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-text',
	'default'     => __( 'Follow {author_name} - {site_name}', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Subject', 'penci-bookmark-follow' ),
	'description' => esc_html__( ' 	
This is the subject of the email that will be sent to the user for confirming his email address for subscription of any authors. Available template tags for subject fields are :
{author_name} - displays the name of author
{site_name} - displays the name of your site', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_author_confirm_email_body',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-textarea',
	'default'     => __( 'Hello You recently followed the author "{author_name}". This means you will receive an email when any new post is published by the author "{author_name}". To activate, click confirm below. If you did not request this, please feel free to disregard this notice! {subscribe_url} Thanks', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Body', 'penci-bookmark-follow' ),
	'description' => __( 'This is the body, main content of the email that will be sent to the user for confirming his email address for subscription of any authors. The available tags are:
{author_name} - displays the name of the author
{site_name} - displays the name of your site
{site_link} - displays the site name with link
{subscribe_url} - displays the subscribe url for confirm email subscription.', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_notify_08',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Unsubscribe Confirmation Email Template', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_unsubscribe_confirm_email_subject',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-text',
	'default'     => __( '[{site_name}] Please confirm your unsubscription request', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Subject', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'This is the subject of the email that will be sent to the user for confirming his email address for unsubscription. Available template tags for subject fields are :
{email} - displays the follower\'s email
{site_name} - displays the name of your site', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'          => 'pencibf_unsubscribe_confirm_email_body',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-textarea',
	'default'     => __( '{site_name} has received a request to unsubscribe for this email address. To complete your request please click on the link below: {confirm_url} If you did not request this, please feel free to disregard this notice!". To activate, click confirm below. If you did not request this, please feel free to disregard this notice! {subscribe_url} Thanks', 'penci-bookmark-follow' ),
	'label'       => esc_html__( 'Email Body', 'penci-bookmark-follow' ),
	'description' => __( 'This is the body, main content of the email that will be sent to the user for confirming his email address for unsubscription. The available tags are:
{email} - displays the follower\'s email
{site_name} - displays the name of your site
{site_link} - displays the site name with link
{confirm_url} - displays the confirm url for confirm email unsubscription.', 'penci-bookmark-follow' ),
);


return $options;