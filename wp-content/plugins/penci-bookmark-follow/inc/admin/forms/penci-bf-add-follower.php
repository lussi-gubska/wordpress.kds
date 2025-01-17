<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Add Followers page
 *
 * The code fro adding followers from backend
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
?>
<div class="wrap">

<?php
global $wpdb, $penci_bl_model, $penci_bl_message; // call globals to use them in this page

// model class
$model = $penci_bl_model;

// message class
$message = $penci_bl_message;

// Get prefix
$prefix = PENCI_BL_META_PREFIX;

// Get all custom post types
$post_types = get_post_types(array('public' => true), 'objects');
if (isset($post_types['attachment'])) {
	// Check attachment post type exists
	unset($post_types['attachment']);
}

$followers_msg = '';
//Get message after sent email to followers
if ($message->size('penci-bf-sent-mail-message') > 0) {
	$followers_msg = $message->messages[0]['text'];
}

?>
<!-- plugin name -->
<h2><?php esc_html_e('Add Followers', 'penci-bookmark-follow');?></h2><br />

<?php
if (!empty($followers_msg)) {
	?>
		<div class="updated fade below-h2" id="message"><p><strong><?php echo $followers_msg; ?></strong></p></div>
<?php
}
?>
<!-- beginning of the general settings meta box -->
<div id="penci-bf-general" class="post-box-container">
	<div class="metabox-holder">
		<div class="meta-box-sortables ui-sortable">
			<div id="general" class="postbox">
				<div class="handlediv" title="<?php esc_html_e('Click to toggle', 'penci-bookmark-follow');?>"><br /></div>
					<!-- Add Followers page title -->
					<h3 class="hndle">
						<span class='wps_fmbp_common_vertical_align'><?php esc_html_e('Add Followers', 'penci-bookmark-follow');?></span>
					</h3>
					<div class="inside">
					<form name="add-follower" id="pencibf_add_follower" method="POST">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="followed_type"><?php esc_html_e('Type', 'penci-bookmark-follow');?></label>
								</th>
								<td>
									<input type="radio" name="followed_type" id="followed_post" value="followed_post" class="followed_type" checked /><label for="followed_post"><?php esc_html_e('Posts', 'penci-bookmark-follow');?></label>
									<input type="radio" name="followed_type" id="followed_authors" value="followed_authors" class="followed_type" /><label for="followed_authors"><?php esc_html_e('Authors', 'penci-bookmark-follow');?></label>
								</td>
							</tr>

							<!-- All Post Types -->
							<tr valign="top" class="followed_type_post">
								<th scope="row">
									<label for="followed_type_post"><?php esc_html_e('Select Post Type', 'penci-bookmark-follow');?><span class="penci_bl_add_follower_error">*</span></label>
								</th>
								<td>
								<div class="penci-bf-post-select-wrap">
									<select name="followed_type_post" id="add_follower_type_post" class="chosen-select">
									<option value=""><?php esc_html_e('-- Select --', 'penci-bookmark-follow');?></option>
										<?php
										foreach ($post_types as $post_key => $post_type) {
											if (!empty($post_key)) { //check if not empty post name
											?>
												<option value="<?php echo $post_type->labels->name; ?>" data-posttype="<?php echo $post_key; ?>">
													<?php echo $post_type->labels->name; ?>
												</option>
											<?php
											}
										} ?>
									</select>
									</div>
									<span class="penci-bf-follow-loader penci-bf-post-follow-loader"><img src="<?php echo esc_url(PENCI_BL_IMG_URL) . '/loader.gif'; ?>" alt="..." /></span>
									<div class="clear"></div>
									<span class="description"><?php esc_html_e('Select post type in which you would like to add followers.', 'penci-bookmark-follow');?></span>
									<div class="followed_type_post_error penci_bl_add_follower_error"></div>
								</td>
							</tr>

							<!-- Post Name -->
							<tr class="penci-bf-display-none penci-bf-post-tr">
								<th scope="row">
									<label for="followed_type_post_name"><?php esc_html_e('Select Post', 'penci-bookmark-follow');?><span class="penci_bl_add_follower_error">*</span></label>
								</th>
								<td>
									<div class="penci-bf-post-select-wrap">
									<select id="followed_type_post_name" name="followed_type_post_name" data-placeholder="<?php esc_html_e('-- Select --', 'penci-bookmark-follow');?>" class="chosen-select" tabindex="2">
									</select></div>
									<span class="penci-bf-follow-loader penci-bf-post-name-loader"><img src="<?php echo esc_url(PENCI_BL_IMG_URL) . '/loader.gif'; ?>" alt="..." /></span>
									<span class="followed_type_post_name_error penci_bl_add_follower_error penci_bl_add_follower_error_second" ></span>
									<div class="clear"></div>
									<span class="description"><?php esc_html_e('Select post in which you would like to add followers.', 'penci-bookmark-follow');?></span>
								</td>
							</tr>

							<!-- All taxonomy -->
							<tr valign="top" class="followed_type_terms penci-bf-display-none">
								<th scope="row">
									<label for="followed_type_terms"><?php esc_html_e('Select Taxonomy', 'penci-bookmark-follow');?><span class="penci_bl_add_follower_error">*</span></label>
								</th>
								<td>
								<div class="penci-bf-taxonomy-select-wrap">
									<select name="followed_type_terms" id="add_follower_type_terms" class="chosen-select">
									<option value=""><?php esc_html_e('-- Select --', 'penci-bookmark-follow');?></option>
									<?php
									if (!empty($post_types)) {

										foreach ($post_types as $key => $post_type) {

											$all_taxonomy = get_object_taxonomies($key);
											// Check taxonomy is not empty
											if (!empty($all_taxonomy)) {

												echo '<optgroup label="' . $post_type->labels->name . '">';
												foreach ($all_taxonomy as $taxonomy_slug) {

													if ($taxonomy_slug != 'post_format') {

														$tax = get_taxonomy($taxonomy_slug);
														echo '<option value="' . $taxonomy_slug . '" data-posttype="' . $key . '" ' . selected(isset($_GET['penci_bl_taxonomy']) ? $_GET['penci_bl_taxonomy'] : '', $taxonomy_slug, false) . '>' . $tax->label . '</option>';
													}
												}
												echo '</optgroup>';
											}
										}
									}
									?>
									</select>
									</div>
									<span class="penci-bf-follow-loader penci-bf-term-follow-loader"><img src="<?php echo esc_url(PENCI_BL_IMG_URL) . '/loader.gif'; ?>" alt="..." /></span>
									<div class="clear"></div>
									<span class="description"><?php esc_html_e('Select a taxonomy in which you would like to add followers like category, tags.', 'penci-bookmark-follow');?></span>
									<div class="followed_type_terms_error penci_bl_add_follower_error"></div>
								</td>
							</tr>

							<!-- All Terms -->
							<tr class="penci-bf-display-none penci-bf-term-slug-tr">
								<th scope="row">
									<label for="penci_bl_term_id"><?php esc_html_e('Select Term', 'penci-bookmark-follow');?><span class="penci_bl_add_follower_error">*</span></label>
								</th>
								<td>
									<div class="penci-bf-post-select-wrap">
									<select id="penci_bl_term_id" name="penci_bl_term_id" data-placeholder="<?php esc_html_e('-- Select --', 'penci-bookmark-follow');?>" class="chosen-select" tabindex="2">
									</select></div>
									<span class="penci-bf-follow-loader penci-bf-post-name-loader"><img src="<?php echo esc_url(PENCI_BL_IMG_URL) . '/loader.gif'; ?>" alt="..." /></span>
									<span class="followed_type_term_id_error penci_bl_add_follower_error penci_bl_add_follower_error_second"></span>
									<div class="clear"></div>
									<span class="description"><?php esc_html_e('Displays the terms like category / tag based on selected taxonomy.', 'penci-bookmark-follow');?></span>
								</td>
							</tr>


							<!-- All Authors -->
							<tr valign="top" class="followed_type_author penci-bf-display-none">
								<th scope="row">
									<label for="followed_type_author"><?php esc_html_e('Select Author', 'penci-bookmark-follow');?><span class="penci_bl_add_follower_error">*</span></label>
								</th>
								<td>
									<div class="penci-bf-post-select-wrap">
									<select name="followed_type_author" id="followed_type_author" class="chosen-select">
									<option value=""><?php esc_html_e('Choose an Author...', 'penci-bookmark-follow');?></option>
										<?php
										$all_authors = $this->model->penci_bl_get_author_list();
										foreach ($all_authors as $key => $value) { ?>
											<option value="<?php echo $value->id; ?>">
												<?php echo $value->display_name; ?>
											</option>
										<?php } ?>
									</select></div>
									<span class="penci-bf-follow-loader penci-bf-post-name-loader"><img src="<?php echo esc_url(PENCI_BL_IMG_URL) . '/loader.gif'; ?>" alt="..." /></span>
									<div class="clear"></div>
									<span class="description"><?php esc_html_e('Select author in which you would like to add followers.', 'penci-bookmark-follow');?></span>
									<div class="followed_type_author_error penci_bl_add_follower_error"></div>
								</td>
							</tr>

							<!-- User Type -->
							<tr class="penci-bf-display-none penci-bf-user-type-tr">
								<th scope="row">
									<label for="followed_user_type"><?php esc_html_e('User Type', 'penci-bookmark-follow');?><span class="penci_bl_add_follower_error">*</span></label>
								</th>
								<td>
									<input type="radio" id="followed_users" name="followed_user_type" value="0" checked tabindex="3"><label for="followed_users"><?php esc_html_e('Registered User', 'penci-bookmark-follow');?></label>
									<input type="radio" id="followed_guest_users" name="followed_user_type" value="1" tabindex="3"><label for="followed_guest_users"><?php esc_html_e('Guest User', 'penci-bookmark-follow');?></label>
									<br/>
									<span class="description"><?php esc_html_e('Select type of user whom you wanted to add as follower.', 'penci-bookmark-follow');?></span>
									<div class="followed_by_user_error penci_bl_add_follower_error"></div>
								</td>
							</tr>

							<!-- User List -->
							<tr class="penci-bf-display-none penci-bf-users-tr">
								<th scope="row">
									<label for="followed_by_users"><?php esc_html_e('Select User', 'penci-bookmark-follow');?><span class="penci_bl_add_follower_error">*</span></label>
								</th>
								<td>
									<select id="followed_by_users" name="followed_by_users" data-placeholder="<?php esc_html_e('-- Select --', 'penci-bookmark-follow');?>" class="chosen-select" tabindex="4">
									</select><br/>
									<span class="description"><?php esc_html_e('Select user whom you wanted to add as follower.', 'penci-bookmark-follow');?></span>
									<div class="followed_by_user_error penci_bl_add_follower_error"></div>
								</td>
							</tr>

							<!-- Guest User Email Id -->
							<tr class="penci-bf-display-none penci-bf-guest-user-tr">
								<th scope="row">
									<label for="followed_guest_user"><?php esc_html_e('Enter Email Id', 'penci-bookmark-follow');?><span class="penci_bl_add_follower_error">*</span></label>
								</th>
								<td>
									<input type="email" id="followed_guest_user" name="followed_guest_user" data-placeholder="<?php esc_html_e('Enter Email Address', 'penci-bookmark-follow');?>" tabindex="5" class="followed_guest_user">
									<span class="followed_guest_user_error penci_bl_add_follower_error"></span>
									<br/>
									<span class="description"><?php esc_html_e('Enter Email Address of the user whom you wanted to add as follower.', 'penci-bookmark-follow');?></span>
								</td>
							</tr>

							<!-- Save Followers -->
							<tr class="penci-bf-display-none penci-bf-save-tr">
								<th scope="row"></th>
								<td>
									<div class="penci-bf-post-select-wrap">
									<?php echo apply_filters('pencibf_fb_settings_submit_button', '<input type="button" id="penci-bf-follow" class="button-primary" name="follow" value="' . esc_html__('Follow', 'penci-bookmark-follow') . '">'); ?>
									<input type="hidden" id="penci-bf-current-page" name="followed_current_page" value="<?php echo $_GET['page']; ?>">
									</div>
									<span class="penci-bf-follow-loader penci-bf-save-loader"><img src="<?php echo esc_url(PENCI_BL_IMG_URL) . '/loader.gif'; ?>" alt="..." /></span>
									<div class="clear"></div>
								</td>
							</tr>

							<!-- Success Message -->
							<tr class="penci-bf-success-msg-tr">
								<th scope="row"></th>
								<td>
									<div class="penci-bf-post-select-wrap">
										<div class="penci-bf-success-message"></div>
									</div>
									<div class="clear"></div>
								</td>
							</tr>

						</tbody>
					</table>
				  </form>
				</div><!-- .inside -->
			</div><!-- #general -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #penci-bf-general -->
</div><!--end .wrap-->