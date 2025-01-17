<div class="wrap pcfds-form">

    <h2><?php echo esc_html( __( 'Edit Feed', 'penci-feeds' ) ); ?></h2>

    <form method="post" action="<?php echo $this->menuUrl( 'pcfds-feeds', 'edit', array( 'post' => $feed->id ) ); ?>"
          id="pcfds-add-source-form" class="edit">
        <input type="hidden" value="<?php echo $this->menuUrl( 'pcfds-feeds', 'preview' ); ?>"
               id="pcfds-load-preview-url"/>
        <input type="hidden" value="<?php echo $this->menuUrl( 'pcfds-feeds', 'extract' ); ?>"
               id="pcfds-content-extractor-url"/>
		<?php wp_nonce_field( 'pcfds-save-penci-feeds' ); ?>
        <div id="pcfds-add-source-form-container" class="metabox-holder">
            <div id="titlediv" class="pcfds-field-container">
                <input type="text" name="title" size="80" value="<?php echo esc_attr( $feed->title ); ?>" id="title"
                       spellcheck="true" autocomplete="off"
                       placeholder="<?php esc_attr_e( 'Enter source title', 'penci-feeds' ); ?>"/>
            </div>
            <div id="pcfds-types-list" class="meta-box-sortables">
                <div class="postbox">
                    <div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'penci-feeds' ); ?>"><br></div>
                    <h3 class="hndle ui-sortable-handle">
                        <span><?php esc_html_e( 'General Options', 'penci-feeds' ); ?></span></h3>
                    <div class="inside">
                        <label for="pcfds-url"><b><?php esc_html_e( 'Feed URL', 'penci-feeds' ); ?></b></label>
                        <div class="field pcfds-field-container">
                            <input id="pcfds-url" name="url" value="<?php echo esc_attr( $feed->url ); ?>" size="100"/>
                        </div>
                        <table width="100%">
                            <tr>
                                <td>
                                    <div>
                                        <label for="pcfds-author"><b><?php esc_html_e( 'Default Author', 'penci-feeds' ); ?></b></label>
                                        <div class="field pcfds-field-container">
											<?php wp_dropdown_users( array(
												'selected' => $feed->author,
												'name'     => 'author',
												'id'       => 'pcfds-author'
											) ); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="pcfds-status"><b><?php esc_html_e( 'Default Status', 'penci-feeds' ); ?></b></label>
                                        <div class="field pcfds-field-container">
                                            <select id="pcfds-status" name="status">
												<?php foreach ( $statuses as $k => $status ) : ?>
                                                    <option value="<?php echo esc_attr( $k ); ?>"
													        <?php if ( $k == $feed->status ) : ?>selected="selected"<?php endif; ?>><?php echo esc_html( $status ); ?></option>
												<?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="pcfds-type"><b><?php esc_html_e( 'Post type', 'penci-feeds' ); ?></b></label>
                                        <div class="field pcfds-field-container">
                                            <select id="pcfds-type" name="type">
												<?php foreach ( $postTypes as $type ) : ?>
                                                    <option value="<?php echo esc_attr( $type ); ?>"
													        <?php if ( $type == $feed->type ) : ?>selected="selected"<?php endif; ?>><?php echo esc_html( $type ); ?></option>
												<?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="pcfds-update-frequency"><b><?php esc_html_e( 'Update frequency', 'penci-feeds' ); ?></b></label>
                                        <div class="field pcfds-field-container">
                                            <select id="pcfds-update-frequency" name="update_frequency">
												<?php foreach ( $updateFrequences as $k => $frequency ) : ?>
                                                    <option value="<?php echo esc_attr( $k ); ?>"
													        <?php if ( $k == $feed->update_frequency ) : ?>selected="selected"<?php endif; ?>><?php echo esc_html( $frequency ); ?></option>
												<?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="field pcfds-field-container">
                                        <p><label for="pcfds-use-date"><input type="checkbox" value="1"
										                                      <?php if ( $feed->use_date ) : ?>checked="checked"<?php endif; ?>
                                                                              name="use_date"
                                                                              id="pcfds-use-date"/> <?php esc_html_e( 'Use date from feed', 'penci-feeds' ); ?>
                                            </label><span class="rss-tooltip"
                                                          title="<?php esc_html_e( 'By default post date and time are set to current on script execution', 'penci-feeds' ); ?>">?</span>
                                        </p>
                                    </div>

                                    <div class="field pcfds-field-container">
                                        <p><label for="pcfds-download-images"><input type="checkbox" value="1"
										                                             <?php if ( $feed->download_images ) : ?>checked="checked"<?php endif; ?>
                                                                                     name="download_images"
                                                                                     id="pcfds-download-images"/> <?php esc_html_e( 'Download images', 'penci-feeds' ); ?>
                                            </label><span class="rss-tooltip"
                                                          title="<?php esc_html_e( 'By default images are referring to original website. You can download them to your server with this option.', 'penci-feeds' ); ?>">?</span>
                                        </p>
                                    </div>

                                    <div class="field pcfds-field-container">
                                        <p><label for="pcfds-remove-links"><input type="checkbox" value="1"
										                                          <?php if ( $feed->remove_links ) : ?>checked="checked"<?php endif; ?>
                                                                                  name="remove_links"
                                                                                  id="pcfds-remove-links"/> <?php esc_html_e( 'Remove Links', 'penci-feeds' ); ?>
                                            </label><span class="rss-tooltip"
                                                          title="<?php esc_html_e( 'Remove all links from the content', 'penci-feeds' ); ?>">?</span>
                                        </p>
                                    </div>

                                    <div class="field pcfds-field-container">
                                        <p><label for="pcfds-overwrite-posts"><input type="checkbox" value="1"
										                                             <?php if ( $feed->overwrite_posts ) : ?>checked="checked"<?php endif; ?>
                                                                                     name="overwrite_posts"
                                                                                     id="pcfds-overwrite-posts"/> <?php esc_html_e( 'Overwrite posts', 'penci-feeds' ); ?>
                                            </label><span class="rss-tooltip"
                                                          title="<?php esc_html_e( 'Overwrite existing posts on every run', 'penci-feeds' ); ?>">?</span>
                                        </p>
                                    </div>

                                    <div>
                                        <label for="pcfds-posts-limit"><b><?php esc_html_e( 'Limit number of posts', 'penci-feeds' ); ?></b>
                                            <span class="rss-tooltip"
                                                  title="<?php esc_html_e( 'Limit number of posts for a single run', 'penci-feeds' ); ?>">?</span></label>
                                        <div class="field pcfds-field-container">
                                            <input type="number" size="3" id="pcfds-posts-limit" name="posts_limit"
                                                   value="<?php echo $feed->posts_limit ? $feed->posts_limit : 50; ?>"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>

                <div class="postbox">
                    <div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'penci-feeds' ); ?>"><br></div>
                    <h3 class="hndle ui-sortable-handle">
                        <span><?php esc_html_e( 'Categories', 'penci-feeds' ); ?></span></h3>
                    <div class="inside">
                        <p><label for="pcfds-extract-categories"><input
									<?php if ( $feed->extract_categories ) : ?>checked="checked"<?php endif; ?>
                                    type="checkbox" value="1" name="extract_categories"
                                    id="pcfds-extract-categories"/> <?php esc_html_e( 'Extract categories from feed (create categories automatically)', 'penci-feeds' ); ?>
                            </label></p>
                        <p><label for="pcfds-extract-tags"><input
									<?php if ( $feed->extract_tags ) : ?>checked="checked"<?php endif; ?>type="checkbox"
                                    value="1" name="extract_tags"
                                    id="pcfds-extract-tags"/> <?php esc_html_e( 'Extract categories from feed as tags', 'penci-feeds' ); ?>
                            </label></p>
                        <ul class="pcfds-categories-list">
							<?php wp_category_checklist( 0, 0, $feed->post_category, false, null, true ); ?>
                        </ul>
                        <div class="clear"></div>
                    </div>
                </div>

                <div class="postbox">
                    <div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'penci-feeds' ); ?>"><br></div>
                    <h3 class="hndle ui-sortable-handle">
                        <span><?php esc_html_e( 'Content Options', 'penci-feeds' ); ?></span></h3>
                    <div class="inside">
                        <table width="100%">
                            <tr>
                                <td>
                                    <p><label for="pcfds-dont-add-excerpt"><input
												<?php if ( $feed->dont_add_excerpt ) : ?>checked="checked"<?php endif; ?>
                                                type="checkbox" value="1" name="dont_add_excerpt"
                                                id="pcfds-dont-add-excerpt"/> <?php esc_html_e( 'Don\'t add excerpt', 'penci-feeds' ); ?>
                                        </label></p>
                                </td>
                                <td id="pcfds-limit-excerpt-block">
                                    <label for="pcfds-limit-excerpt"><?php esc_html_e( 'Limit number of characters to', 'penci-feeds' ); ?></label>
                                    <input type="number" size="4" name="limit_excerpt" id="pcfds-limit-excerpt"
                                           value="<?php echo $feed->limit_excerpt ? $feed->limit_excerpt : 1000; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><label for="pcfds-add-more-tag"><input
												<?php if ( $feed->add_more_tag ) : ?>checked="checked"<?php endif; ?>
                                                type="checkbox" value="1" name="add_more_tag"
                                                id="pcfds-add-more-tag"/> <?php esc_html_e( 'Add <!--more--> tag', 'penci-feeds' ); ?>
                                        </label></p>
                                </td>
                                <td id="pcfds-add-more-tag-block">
                                    <label for="pcfds-paragraphs-num"><?php esc_html_e( 'after number of paragraphs', 'penci-feeds' ); ?></label>
                                    <input type="number" size="4" name="paragraphs_num" id="pcfds-paragraphs-num"
                                           value="<?php echo $feed->paragraphs_num ? $feed->paragraphs_num : 1; ?>"/>
                                </td>
                            </tr>
                        </table>

                        <hr>
                        <p><b><?php esc_html_e( 'Post Content', 'penci-feeds' ); ?></b></p>
                        <div class="field pcfds-field-container">
                            <p><label for="pcfds-display-readmore"><input type="checkbox"
							                                              <?php if ( $feed->display_readmore ) : ?>checked="checked"<?php endif; ?>
                                                                          value="1" name="display_readmore"
                                                                          id="pcfds-display-readmore"/> <?php esc_html_e( 'Display link to source', 'penci-feeds' ); ?>
                                </label></p>
                            <input type="text" size="50" name="readmore_template"
                                   value="<?php echo esc_attr( $feed->readmore_template ); ?>"/>
                            <span title="<?php esc_html_e( 'HTML allowed. Use %LINK% placeholder to add URL', 'penci-feeds' ); ?>"
                                  class="rss-tooltip">?</span>
                        </div>

                        <div class="field pcfds-field-container">
                            <p><label for="pcfds-enable-scrapper"><input type="checkbox"
							                                             <?php if ( $feed->enable_scrapper ) : ?>checked="checked"<?php endif; ?>
                                                                         name="enable_scrapper" value="1"
                                                                         id="pcfds-enable-scrapper"/> <?php esc_html_e( 'Enable web scraper', 'penci-feeds' ); ?>
                                </label><span class="rss-tooltip"
                                              title="<?php esc_html_e( 'You can enable web scraper to download full article contents. Please note, it takes more time to download.', 'penci-feeds' ); ?>">?</span>
                            </p>
                            <div class="content-extractor-options">
                                <p><a id="content-extractor-btn" href="#content-extractor"
                                      class="button"><?php esc_html_e( 'Configure content extractor', 'penci-feeds' ); ?></a>
                                </p>
                                <textarea rows="4" cols="30" name="content_extractor_rule" id="content-extractor-rule"
                                          placeholder="<?php esc_attr_e( 'XPath selector can be set here', 'penci-feeds' ); ?>"><?php echo esc_html( $feed->content_extractor_rule ); ?></textarea>
                                <textarea rows="4" cols="30" name="content_extractor_ignore_rule"
                                          id="content-extractor-ignore-rule"
                                          placeholder="<?php esc_attr_e( 'XPath selectors for ignored elements can be set here', 'penci-feeds' ); ?>"><?php echo esc_html( $feed->content_extractor_ignore_rule ); ?></textarea>
                            </div>
                        </div>

                        <hr>
                        <p><b><?php esc_html_e( 'Content wrapper', 'penci-feeds' ); ?></b></p>
                        <div>
                            <span><?php echo __( 'Add text or HTML before and after the article. Use %CONTENT% as a placeholder for the imported article contents. Leave empty to ignore that option. Example: &lt;h1&gt;Import from domain.com&lt;/h1&gt;%CONTENT%', 'penci-feeds' ); ?></span>
                        </div>
                        <div class="field pcfds-field-container">
                            <textarea rows="8" cols="80" name="content_wrapper" id="content-wrapper"
                                      placeholder="<?php esc_attr_e( '%CONTENT%', 'penci-feeds' ); ?>"><?php $content_html = $feed->content_wrapper ? $feed->content_wrapper : '%CONTENT%';
								echo $content_html; ?></textarea>
                        </div>
                        <hr>

                        <div class="field pcfds-field-container">
                            <p><b><?php esc_html_e( 'Set featured image', 'penci-feeds' ); ?></b></p>
                            <p><label><input type="radio" name="thumbnail" value=""
							                 <?php if ( ! $feed->thumbnail ) : ?>checked="checked"<?php endif; ?> /> <?php esc_html_e( 'None', 'penci-feeds' ); ?>
                                </label></p>
                            <p><label><input type="radio" name="thumbnail" value="feed"
							                 <?php if ( $feed->thumbnail == 'feed' ) : ?>checked="checked"<?php endif; ?> /> <?php esc_html_e( 'Thumbnail from feed', 'penci-feeds' ); ?>
                                </label><span class="rss-tooltip"
                                              title="<?php esc_attr_e( 'Set featured image from feed file', 'penci-feeds' ); ?>">?</span>
                            </p>
                            <p><label><input type="radio" name="thumbnail" value="content"
							                 <?php if ( $feed->thumbnail == 'content' ) : ?>checked="checked"<?php endif; ?> /> <?php esc_html_e( 'First Image from content', 'penci-feeds' ); ?>
                                </label><span class="rss-tooltip"
                                              title="<?php esc_attr_e( 'Set featured image from article summary or article content (if content extractor is in use)', 'penci-feeds' ); ?>">?</span>
                            </p>
                            <p><label><input type="radio" name="thumbnail" value="content_delete"
							                 <?php if ( $feed->thumbnail == 'content_delete' ) : ?>checked="checked"<?php endif; ?> /> <?php esc_html_e( 'First Image from content (and delete)', 'penci-feeds' ); ?>
                                </label><span class="rss-tooltip"
                                              title="<?php esc_attr_e( 'Set featured image from article summary or article content (if content extractor is in use) and delete it from content - useful for themes that display featured images on post page', 'penci-feeds' ); ?>">?</span>
                            </p>
                            <p><label><input type="radio" name="thumbnail" value="media" id="featured-upload-option"
							                 <?php if ( $feed->thumbnail == 'media' ) : ?>checked="checked"<?php endif; ?> /> <?php esc_html_e( 'Set image from media library', 'penci-feeds' ); ?>
                                </label><span class="rss-tooltip"
                                              title="<?php esc_attr_e( 'Set static featured image for all articles from media library', 'penci-feeds' ); ?>">?</span>
                            </p>

                            <div style="display:none" id="featured-upload-block">
                                <input type="text" id="upload-featured" name="upload_featured"
                                       value="<?php echo esc_attr( $feed->upload_featured ); ?>" size="50"
                                       readonly="readonly">
                                <input type="hidden" id="upload-featured-id" name="upload_featured_id"
                                       value="<?php echo esc_attr( $feed->upload_featured_id ); ?>">
                                <input type="button" value="<?php esc_attr_e( 'Upload file', 'penci-feeds' ); ?>"
                                       class="button rss-upload" data-type="image" data-target-field="upload-featured"
                                       data-target-hidden-field="upload-featured-id"
                                       data-dialog-title="<?php esc_attr_e( 'Upload Featured file', 'penci-feeds' ); ?>"
                                       data-button-text="<?php esc_attr_e( 'Use as featured', 'penci-feeds' ); ?>">
                            </div>
                        </div>


                        <div>
                            <p><b><?php esc_html_e( 'Fallback featured image', 'penci-feeds' ); ?></b></p>
                            <div id="fallback-upload-block">
                                <input type="text" id="upload-fallback" name="upload_fallback"
                                       value="<?php echo esc_attr( $feed->upload_fallback ); ?>" size="50"
                                       readonly="readonly">
                                <input type="hidden" id="upload-fallback-id" name="upload_fallback_id"
                                       value="<?php echo esc_attr( $feed->upload_fallback_id ); ?>">
                                <input type="button" value="<?php esc_attr_e( 'Upload file', 'penci-feeds' ); ?>"
                                       class="button rss-upload" data-type="image" data-target-field="upload-fallback"
                                       data-target-hidden-field="upload-fallback-id"
                                       data-dialog-title="<?php esc_attr_e( 'Upload Fallback Featured file', 'penci-feeds' ); ?>"
                                       data-button-text="<?php esc_attr_e( 'Use as fallback featured', 'penci-feeds' ); ?>">
                            </div>
                        </div>


                        <hr>
                        <div class="field pcfds-field-container">
                            <p><label for="pcfds-enable-filters"><input type="checkbox" name="enable_filters" value="1"
                                                                        id="pcfds-enable-filters"
							                                            <?php if ( $feed->enable_filters ) : ?>checked="checked"<?php endif; ?> /> <?php esc_html_e( 'Enable content filters', 'penci-feeds' ); ?>
                                </label><span class="rss-tooltip"
                                              title="<?php esc_html_e( 'Using this option you can filter articles by specified keywords', 'penci-feeds' ); ?>">?</span>
                            </p>
                            <div class="content-filter-options">
                                <p>
                                    <label for="pcfds-filter-must"><?php esc_html_e( 'Specify words any of which MUST be presented in a content in order to be added (comma separated):', 'penci-feeds' ); ?></label>
                                </p>
                                <textarea rows="3" cols="60" name="filter_keywords_must" id="pcfds-filter-must"
                                          placeholder="<?php esc_attr_e( 'Comma separated words', 'penci-feeds' ); ?>"><?php echo esc_html( $feed->filter_keywords_must ); ?></textarea>

                                <p>
                                    <label for="pcfds-filter-block"><?php esc_html_e( 'Don\'t add articles containing any of following words (comma separated):', 'penci-feeds' ); ?></label>
                                </p>
                                <textarea rows="3" cols="60" name="filter_keywords_block" id="pcfds-filter-block"
                                          placeholder="<?php esc_attr_e( 'Comma separated words', 'penci-feeds' ); ?>"><?php echo esc_html( $feed->filter_keywords_block ); ?></textarea>
                            </div>
                        </div>


                        <div class="field pcfds-field-container">
                            <p><label for="pcfds-add-canonical"><input type="checkbox" name="add_canonical" value="1"
                                                                       id="pcfds-add-canonical"
							                                           <?php if ( $feed->add_canonical ) : ?>checked="checked"<?php endif; ?> /> <?php esc_html_e( 'Add canonical URL to source', 'penci-feeds' ); ?>
                                </label></p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="pcfds-preview" class="pcfds-meta-box-container meta-box-sortables">
                <p><a href="#"
                      class="button feed-preview-btn"><?php esc_html_e( 'Preview', 'penci-feeds' ); ?></a></p>
                <div class="postbox">
                    <div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'penci-feeds' ); ?>"><br></div>
                    <h3 class="hndle"><span><?php esc_html_e( 'Preview', 'penci-feeds' ); ?></span></h3>
                    <div class="inside"></div>
                </div>
            </div>

            <div class="save-penci-feeds-form">
                <input type="submit" class="button-primary" name="save"
                       value="<?php echo esc_attr( __( 'Save', 'penci-feeds' ) ); ?>"/>
            </div>
        </div>
    </form>
</div>
<div id="content-extractor" style="display:none;">
    <iframe id="content-extractor-iframe"></iframe>
</div>