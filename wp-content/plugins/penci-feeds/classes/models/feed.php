<?php

namespace PenciFeeds;

use DOMDocument;
use DOMXPath;
use PicoFeed\Reader\Reader;
use PicoFeed\Config\Config;
use PicoFeed\Scraper\Scraper;
use PicoFeed\PicoFeedException;

require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/libs/autoload.php' );

/**
 * Class FeedModel
 * @package PenciFeeds
 */
class FeedModel {

	// Custom post type
	const post_type = 'pcfds-feed';

	// post ID
	public $id = null;

	// post title
	public $title;

	// other post properties stored as post_meta
	private $properties = array();

	/**
	 * Init new feed
	 *
	 * @param array|int $data
	 */
	public function __construct( $data = array() ) {
		if ( is_numeric( $data ) ) {
			$this->id = $data;
			$this->load();
		} else {
			if ( is_array( $data ) && count( $data ) ) {
				$this->setValues( $data );
			}
		}
	}

	/**
	 * Magic get method for feed properties
	 *
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function __get( $name ) {
		if ( array_key_exists( $name, $this->properties ) ) {
			return $this->properties[ $name ];
		}

		return null;
	}

	/**
	 * Magic set method for feed properties
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return mixed|null
	 */
	public function __set( $name, $value ) {
		$this->properties[ $name ] = $value;
	}

	/**
	 * Init feed with information from DB
	 */
	private function load() {
		if ( $this->id ) {
			$post = get_post( $this->id );
			if ( ! $post ) {
				$this->id = null;

				return;
			}
			$this->title = $post->post_title;
			$this->id    = $post->ID;
			$meta        = get_post_meta( $post->ID );
			foreach ( $meta as $key => $item ) {
				$newKey                      = substr( $key, 1 );
				$this->properties[ $newKey ] = $item[0];
				if ( $newKey == 'post_category' ) {
					$this->properties[ $newKey ] = unserialize( $item[0] );
				}
			}
		}
	}

	/**
	 * Save feed information
	 * @return int|\WP_Error
	 */
	public function save() {
		if ( ! $this->id ) {
			$postId = wp_insert_post(
				array(
					'post_type'   => self::post_type,
					'post_status' => 'publish',
					'post_title'  => $this->title
				)
			);
		} else {
			// Delete post meta
			$meta = get_post_meta( $this->id );
			foreach ( $meta as $key => $item ) {
				delete_post_meta( $this->id, $key );
			}

			$postId = wp_update_post(
				array(
					'ID'          => (int) $this->id,
					'post_status' => 'publish',
					'post_title'  => $this->title,
					'post_type'   => self::post_type,
				)
			);

		}

		if ( $postId ) {

			foreach ( $this->properties as $key => $value ) {
				update_post_meta( $postId, '_' . $key, $value );
			}
		}

		return $postId;
	}

	/**
	 * Delete feed
	 * @return bool
	 */
	public function delete() {
		if ( $this->id ) {
			if ( wp_delete_post( $this->id, true ) ) {
				$this->id = 0;

				return true;
			}
		}

		return false;
	}

	/**
	 * Set model properties
	 *
	 * @param array $data
	 */
	public function setValues( $data ) {
		if ( isset( $data['id'] ) && $data['id'] ) {
			$this->id = $data['id'];
		}

		if ( isset( $data['title'] ) && $data['title'] ) {
			$this->title = $data['title'];
		}

		if ( isset( $data['url'] ) && $data['url'] ) {
			$data['url'] = trim( str_replace( 'feed://', 'http://', $data['url'] ) );
		}

		$fields = array(
			'url',
			'author',
			'status',
			'display_readmore',
			'readmore_template',
			'update_frequency',
			'enable_scrapper',
			'thumbnail',
			'post_category',
			'use_date',
			'type',
			'download_images',
			'add_canonical',
			'last_update',
			'last_modified',
			'etag',
			'campaign_status',
			'content_extractor_rule',
			'content_extractor_ignore_rule',
			'enable_filters',
			'filter_keywords_must',
			'filter_keywords_block',
			'posts_limit',
			'overwrite_posts',
			'translate',
			'dont_add_excerpt',
			'limit_excerpt',
			'add_more_tag',
			'paragraphs_num',
			'remove_links',
			'upload_featured',
			// Version 1.5.0 options:
			'content_wrapper',
			'upload_fallback',
			'extract_categories',
			'extract_tags',
			'upload_featured_id',
			'upload_fallback_id'
		);

		foreach ( $fields as $field ) {
			if ( isset( $data[ $field ] ) ) {
				$this->properties[ $field ] = $data[ $field ];
			}
		}
	}

	/**
	 * Get first page from news feed
	 */
	public function getFirstPage( $downloader ) {
		try {
			$reader   = new Reader;
			$resource = $reader->download( $this->url );

			$parser = $reader->getParser(
				$resource->getUrl(),
				$resource->getContent(),
				$resource->getEncoding()
			);

			if ($parser !== null) {
				$feed = $parser->execute();
			} else {
				$feed = []; // or handle the error as needed
			}

			$article = null;

			if ( !empty( $feed->items ) ){

				foreach ( $feed->items as $item ) {
					$article = $this->grabContent( $item->getUrl(), $downloader );

					return $article;
				}

			}

			return $article;
		} catch ( PicoFeedException $e ) {
			return false;
		}
	}

	private function grabContent( $url, $downloader ) {
		if ( ! function_exists( 'file_get_html' ) ) {
			require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/libs/simplehtml/simple_html_dom.php' );
		}

		$parts  = parse_url( $url );
		$domain = $parts['scheme'] . '://' . $parts['host'];

		if ( isset( $parts['port'] ) && $parts['port'] && ( $parts['port'] != '80' ) ) {
			$domain .= ':' . $parts['port'];
		}

		// Relative path URL
		$relativeUrl = $domain;
		if ( isset( $parts['path'] ) && $parts['path'] ) {
			$pathParts = explode( '/', $parts['path'] );
			if ( count( $pathParts ) ) {
				unset( $pathParts[ count( $pathParts ) - 1 ] );
				$relativeUrl = $domain . '/' . implode( '/', $pathParts );
			}
		}

		$html = file_get_html( $url, false, null, 0 );

		if ( ! $html || ! is_object( $html ) ) {
			return 'Error loading HTML';
		}

		// Remove all script tags
		foreach ( $html->find( 'script' ) as $element ) {
			$element->outertext = '';
		}

		// Remove meta
		foreach ( $html->find( 'meta[http-equiv*=refresh]' ) as $meta ) {
			$meta->outertext = '';
		}

		// Remove meta x-frame
		foreach ( $html->find( 'meta[http-equiv*=x-frame-options]' ) as $meta ) {
			$meta->outertext = '';
		}

		// Modify image and CSS URL's adding domain name if needed
		foreach ( $html->find( 'img' ) as $element ) {
			$src = trim( $element->src );
			if ( strlen( $src ) > 2 && ( substr( $src, 0, 1 ) == '/' ) && ( ( substr( $src, 0, 2 ) != '//' ) ) ) {
				$src = $domain . $src;
			} elseif ( substr( $src, 0, 2 ) == '//' ) {
				$src = 'http:' . $src;
			} elseif ( substr( $src, 0, 4 ) != 'http' ) {
				$src = $relativeUrl . '/' . $src;
			}
			if ( strpos( $downloader, '?' ) ) {
				$element->src = $downloader . '&url=' . base64_encode( $src );
			} else {
				$element->src = $downloader . '?url=' . base64_encode( $src );
			}
		}

		// Replace all styles URL’s
		foreach ( $html->find( 'link' ) as $element ) {
			$src = trim( $element->href );
			if ( strlen( $src ) > 2 && ( substr( $src, 0, 1 ) == '/' ) && ( ( substr( $src, 0, 2 ) != '//' ) ) ) {
				$src = $domain . $src;
			} elseif ( ( substr( $src, 0, 4 ) != 'http' ) && ( substr( $src, 0, 2 ) != '//' ) ) {
				$src = $relativeUrl . '/' . $src;
			}
			$element->href = $src;
		}

		// Append our JavaScript and CSS
		//$head = $html->find("head", 0);
		$scripts = '<script type="text/javascript" src="' . pencifeeds_plugin_url( 'admin/js/jquery.js' ) . '"></script>';
		$scripts .= '<script type="text/javascript" src="' . pencifeeds_plugin_url( 'admin/js/extractor.js' ) . '?' . time() . '"></script>';
		$scripts .= '<link rel="stylesheet" type="text/css" href="' . pencifeeds_plugin_url( 'admin/css/extractor.css' ) . '">';

		//$head->innertext .= $scripts;

		$html = str_replace( '</body>', $scripts . '</body>', $html );

		return $html;
	}

	/**
	 * Get news list
	 *
	 * @param int $limit
	 *
	 * @return bool
	 */
	public function getNews( $limit = 0, $fullHtml = false ) {
		try {
			$reader   = new Reader;
			$resource = $reader->download( $this->url );

			$parser = $reader->getParser(
				$resource->getUrl(),
				$resource->getContent(),
				$resource->getEncoding()
			);

			$feed = $parser->execute();

			$list = array();
			$i    = 0;

			$config = new Config();

			if ( $this->enable_scrapper && ( $this->content_extractor_rule || $this->content_extractor_ignore_rule ) ) {
				$rules = array(
					'grabber' => array(
						'%.*%' => array(
							'body'  => array(),
							'strip' => array(),
						)
					)
				);

				if ( $this->content_extractor_rule ) {
					$rules['grabber']['%.*%']['body'][] = stripslashes( $this->content_extractor_rule );
				}

				if ( $this->content_extractor_ignore_rule ) {
					$ignoreRules = explode( ',', $this->content_extractor_ignore_rule );
					foreach ( $ignoreRules as $ignoreRule ) {
						if ( trim( $ignoreRule ) ) {
							$rules['grabber']['%.*%']['strip'][] = stripslashes( trim( $ignoreRule ) );
						}
					}

				}

				$config->setGrabberRules( $rules );
			}

			$grabber = new Scraper( $config );

			if ( $this->enable_scrapper && $this->content_extractor_rule ) {
				$grabber->setInternalRules( $rules );
			}

			foreach ( $feed->items as $item ) {
				if ( $limit && ( $i >= $limit ) ) {
					break;
				}

				$row = array(
					'title'   => $item->getTitle(),
					'content' => $item->getContent(),
					'url'     => $item->getUrl()
				);

				if ( $this->extract_categories ) {
					$row['categories'] = @$item->getTag( 'category' );
				}

				if ( $this->enable_scrapper || $fullHtml ) {
					$grabber->setUrl( $row['url'] );
					$grabber->execute();

					if ( $fullHtml ) {
						$row['content'] = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $grabber->getRawContent() );
					} elseif ( $grabber->hasRelevantContent() ) {
						$row['content'] = $grabber->getFilteredContent();
					}
				}

				if ( $row['content'] ) {
					if ( $this->thumbnail == 'feed' ) {
						$row['thumbnail'] = $this->getImageFromFeed( @$item->getTag( 'media:content', 'url' ) );

						if ( ! $row['thumbnail'] || ! is_string( $row['thumbnail'] ) ) {
							$row['thumbnail'] = $this->getImageFromFeed( @$item->getTag( 'media:thumbnail', 'url' ) );
						}

						if ( ! $row['thumbnail'] || ! is_string( $row['thumbnail'] ) ) {
							$row['thumbnail'] = $this->getImageFromFeed( @$item->getTag( 'enclosure', 'url' ) );
						}

						if ( ! $row['thumbnail'] || ! is_string( $row['thumbnail'] ) ) {
							$row['thumbnail'] = $this->getImageFromFeed( @$item->getTag( 'img' ) );
						}

						if ( ! $row['thumbnail'] || ! is_string( $row['thumbnail'] ) ) {
							$descriptionWithImage = $this->getImageFromFeed( @$item->getTag( 'description' ) );
							if ( $descriptionWithImage && is_string( $descriptionWithImage ) ) {

								$DOMDocument = new \DOMDocument();
								$DOMDocument->loadHTML( $descriptionWithImage );
								$DOMXPath = new \DOMXPath( $DOMDocument );

								$row['thumbnail'] = $DOMXPath->evaluate( "string(//img/@src)" );
							}
						}

						if ( $row['thumbnail'] && is_string( $row['thumbnail'] ) && substr( trim( $row['thumbnail'] ), 0, 4 ) != 'http' ) {

							$DOMDocument = new \DOMDocument();
							$DOMDocument->loadHTML( $row['thumbnail'] );
							$DOMXPath = new \DOMXPath( $DOMDocument );

							$row['thumbnail'] = $DOMXPath->evaluate( "string(//img/@src)" );
						}
					} elseif ( $this->thumbnail == 'content' ) {
						$pageImages = $this->getImages( $row['content'] );
						if ( is_array( $pageImages ) && count( $pageImages ) ) {
							$row['thumbnail'] = $pageImages[0];
						} else {
							$videoID = $this->getYouTubeVideoID( $row['content'] );
							if ( $videoID ) {
								$row['thumbnail'] = 'https://img.youtube.com/vi/' . $videoID . '/0.jpg';
							}
						}
					} elseif ( $this->thumbnail == 'content_delete' ) {
						$pageImages = $this->getImages( $row['content'] );
						if ( is_array( $pageImages ) && count( $pageImages ) ) {
							$row['thumbnail'] = $pageImages[0];
							$row['content']   = $this->removeFirstImage( $row['content'] );
						} else {
							$videoID = $this->getYouTubeVideoID( $row['content'] );
							if ( $videoID ) {
								$row['thumbnail'] = 'https://img.youtube.com/vi/' . $videoID . '/0.jpg';
							}
						}
					} elseif ( $this->thumbnail == 'media' ) {
						if ( $this->upload_featured ) {
							$row['thumbnail'] = $this->upload_featured;
						}
					}

					// Fallback featured image
					if ( $this->upload_fallback && ( ! isset( $row['thumbnail'] ) || ! $row['thumbnail'] ) ) {
						$row['thumbnail'] = $this->upload_fallback;
					}
				} else {
					//continue;
				}

				if ( $this->remove_links ) {
					$row['content'] = $this->removeLinks( $row['content'] );
				}

				// Remove excerpt if needed
				if ( ! $this->dont_add_excerpt ) {
					$row['excerpt'] = $this->cleanUpExcerpt( $row['content'] );

					// Cut excerpt if set
					if ( $this->limit_excerpt ) {
						if ( mb_strlen( $row['excerpt'] ) > intval( $this->limit_excerpt ) ) {
							$row['excerpt'] = mb_substr( $row['excerpt'], 0, intval( $this->limit_excerpt ) ) . '..';
						}
					}
				} else {
					$row['excerpt'] = '';
				}

				$list[] = $row;
				$i ++;
			}

			return $list;
		} catch ( PicoFeedException $e ) {
			return false;
		}
	}

	/**
	 * Update news
	 * @return bool
	 */
	public function updateNews() {
		try {
			$etag          = $this->etag;
			$last_modified = $this->last_modified;

			$reader = new Reader;

			$resource = $reader->download( $this->url );

			// ToDo: Investigate strange issue with some feeds and uncomment next lines
			//$resource = $reader->download($this->url, $last_modified, $etag);

			pencifeeds_add_log( 'URL downloaded: ' . $this->url );

			if ( $resource->isModified() || true ) {
				$parser = $reader->getParser(
					$resource->getUrl(),
					$resource->getContent(),
					$resource->getEncoding()
				);

				$feed = $parser->execute();

				pencifeeds_add_log( 'Items to add: ' . count( $feed->items ) );


				$config  = new Config();
				$grabber = new Scraper( $config );

				if ( $this->enable_scrapper && ( $this->content_extractor_rule || $this->content_extractor_ignore_rule ) ) {
					$rules = array(
						'grabber' => array(
							'%.*%' => array(
								'body'  => array(),
								'strip' => array(),
							)
						)
					);

					if ( $this->content_extractor_rule ) {
						$rules['grabber']['%.*%']['body'][] = stripslashes( $this->content_extractor_rule );
					}

					if ( $this->content_extractor_ignore_rule ) {
						$ignoreRules = explode( ',', $this->content_extractor_ignore_rule );
						foreach ( $ignoreRules as $ignoreRule ) {
							if ( trim( $ignoreRule ) ) {
								$rules['grabber']['%.*%']['strip'][] = stripslashes( trim( $ignoreRule ) );
							}
						}

					}

					$grabber->setInternalRules( $rules );
				}

				$importedNum = 0;
				$maxNum      = (int) ( $this->posts_limit ? $this->posts_limit : 50 );
				foreach ( $feed->items as $item ) {
					if ( $importedNum >= $maxNum ) {
						break;
					}

					// Set main info
					$title      = $item->getTitle();
					$url        = $item->getUrl();
					$content    = $item->getContent();
					$excerpt    = trim( strip_tags( $content ) );
					$thumbnail  = '';
					$categories = $categoryIDs = array();

					// Additional fields
					$pubDate  = $item->getDate();
					$language = $item->getLanguage();              // Item language
					$author   = $item->getAuthor();                  // Item author
					$isRTL    = $item->isRTL();

					pencifeeds_add_log( 'Loading ' . $title );

					// Check if record with such URL (was by title prior to V 1.5.0) already exists
					global $wpdb;
					$query   = $wpdb->prepare( "SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE meta_key = '_rss_original_url' AND meta_value = %s", $url
					);
					$results = $wpdb->get_results( $query, ARRAY_A );

					if ( $results && count( $results ) ) {
						$results = $results[0];
					}

					$oldPostID = 0;

					if ( $this->overwrite_posts ) {
						if ( isset( $results['post_id'] ) && $results['post_id'] ) {
							$oldPostID = $results['post_id'];
						}

						if ( $oldPostID ) {
							// Remove post images
							$attachments = get_attached_media( 'image', $oldPostID );
							foreach ( $attachments as $attachment ) {
								wp_delete_attachment( $attachment->ID, true );
							}
						}
					} else {
						if ( isset( $results['post_id'] ) && $results['post_id'] ) {
							pencifeeds_add_log( 'Found: ' . $title );
							continue;
						} else {
							pencifeeds_add_log( 'Not found: ' . $title );
						}
					}

					// Collect all info in a row
					if ( $this->enable_scrapper ) {

						$grabber->setUrl( $url );
						$grabber->execute();

						if ( $grabber->hasRelevantContent() ) {
							pencifeeds_add_log( 'Page downloaded' );
							$content = $grabber->getFilteredContent();
						}
					}

					// Extract categories
					if ( $this->extract_categories || $this->extract_tags ) {
						$categories = @$item->getTag( 'category' );
					}

					// Check content and title against filters
					if ( $this->enable_filters ) {
						if ( $this->filter_keywords_must ) {
							if (
								( $this->containsWords( $title, $this->filter_keywords_must ) )
								|| ( $this->containsWords( $excerpt, $this->filter_keywords_must ) )
								|| ( $this->containsWords( $content, $this->filter_keywords_must ) )
							) {
								pencifeeds_add_log( 'Required keywords found' );
							} else {
								pencifeeds_add_log( 'Required keywords not found' );
								continue;
							}
						}

						if ( $this->filter_keywords_block ) {
							if (
								( $this->containsWords( $title, $this->filter_keywords_block ) )
								|| ( $this->containsWords( $excerpt, $this->filter_keywords_block ) )
								|| ( $this->containsWords( $content, $this->filter_keywords_block ) )
							) {
								pencifeeds_add_log( 'Blocked keywords found' );
								continue;
							} else {
								pencifeeds_add_log( 'Block keywords not found' );
							}
						}
					}

					$thumbnailFeaturedID = $thumbnailFallbackID = null;

					if ( $content ) {
						// Set thumbnail
						if ( $this->thumbnail == 'feed' ) {
							$thumbnail = $this->getImageFromFeed( @$item->getTag( 'media:content', 'url' ) );

							if ( $thumbnail && is_string( $thumbnail ) ) {
							} else {
								$thumbnail = $this->getImageFromFeed( @$item->getTag( 'media:thumbnail', 'url' ) );
							}

							if ( ! $thumbnail || ! is_string( $thumbnail ) ) {
								$thumbnail = $this->getImageFromFeed( @$item->getTag( 'enclosure', 'url' ) );
							}

							if ( ! $thumbnail || ! is_string( $thumbnail ) ) {
								$thumbnail = $this->getImageFromFeed( @$item->getTag( 'img' ) );
							}

							if ( ! $thumbnail || ! is_string( $thumbnail ) ) {
								$descriptionWithImage = $this->getImageFromFeed( @$item->getTag( 'description' ) );
								if ( $descriptionWithImage && is_string( $descriptionWithImage ) ) {

									$DOMDocument = new \DOMDocument();
									$DOMDocument->loadHTML( $descriptionWithImage );
									$DOMXPath = new \DOMXPath( $DOMDocument );

									$thumbnail = $DOMXPath->evaluate( "string(//img/@src)" );
								}
							}

							if ( $thumbnail && is_string( $thumbnail ) && substr( trim( $thumbnail ), 0, 4 ) != 'http' ) {
								$DOMDocument = new \DOMDocument();
								$DOMDocument->loadHTML( $thumbnail );
								$DOMXPath  = new \DOMXPath( $DOMDocument );
								$thumbnail = $DOMXPath->evaluate( "string(//img/@src)" );
							}
						} elseif ( $this->thumbnail == 'content' ) {
							// Parse images, get first one
							$pageImages = $this->getImages( $content );
							if ( is_array( $pageImages ) && count( $pageImages ) ) {
								$thumbnail = $pageImages[0];
							} else {
								$videoID = $this->getYouTubeVideoID( $content );
								if ( $videoID ) {
									$thumbnail = 'https://img.youtube.com/vi/' . $videoID . '/0.jpg';
								}
							}
						} elseif ( $this->thumbnail == 'content_delete' ) {
							// Delete the first image from dom
							$pageImages = $this->getImages( $content );
							if ( is_array( $pageImages ) && count( $pageImages ) ) {
								$thumbnail = $pageImages[0];
								$content   = $this->removeFirstImage( $content );
							} else {
								$videoID = $this->getYouTubeVideoID( $content );
								if ( $videoID ) {
									$thumbnail = 'https://img.youtube.com/vi/' . $videoID . '/0.jpg';
								}
							}
						} elseif ( $this->thumbnail == 'media' ) {
							if ( $this->upload_featured ) {
								if ( $this->upload_featured_id ) {
									$thumbnailFeaturedID = $this->upload_featured_id;

									// To preserve backward compatibility with thumbnails stored as URLs
									$thumbnail = $this->upload_featured;
								}
							}
						}

						// Fallback featured image
						if ( $this->upload_fallback_id && ( ! isset( $thumbnail ) || ! $thumbnail ) ) {
							$thumbnailFallbackID = $this->upload_fallback_id;
						}

						pencifeeds_add_log( 'Expected thumbnail: ' . $thumbnail );
					} else {
						//continue;
					}

					if ( $this->remove_links ) {
						$content = $this->removeLinks( $content );
					}

					// Translate if selected

					if ( $this->dont_add_excerpt ) {
						$excerpt = '';
					} else {
						$excerpt = $this->cleanUpExcerpt( $content );

						// Cut excerpt if needed
						if ( $this->limit_excerpt ) {
							if ( mb_strlen( $excerpt ) > intval( $this->limit_excerpt ) ) {
								$excerpt = mb_substr( $excerpt, 0, intval( $this->limit_excerpt ) ) . '..';
							}
						}
					}

					// Append read more link
					if ( $this->display_readmore && $this->readmore_template ) {
						$content .= str_replace( '%LINK%', $url, $this->readmore_template );
					}

					// Add <!--more--> tag if set
					if ( $this->add_more_tag && ( intval( $this->paragraphs_num ) > 0 ) ) {
						$content = $this->addMoreTag( $content, intval( $this->paragraphs_num ) );
					}

					// Create categories
					if ( count( $categories ) && $this->extract_categories ) {
						foreach ( $categories as $category ) {
							$categoryIDs[] = wp_create_category( $category );
						}
						if ( $this->post_category ) {
							$this->post_category = array_merge( $this->post_category, $categoryIDs );
						} else {
							$this->post_category = $categoryIDs;
						}
					}

					// Create post
					$post = array(
						'post_content'  => $content,
						'post_title'    => $title,
						'post_status'   => $this->status,
						'post_type'     => 'post',
						'post_author'   => $this->author,
						'post_excerpt'  => $excerpt,
						'post_category' => $this->post_category
					);

					if ( $this->type ) {
						$post['post_type'] = $this->type;
					}

					if ( $this->use_date && $pubDate ) {
						$post['post_date'] = $pubDate->format( 'Y-m-d H:i:s' );
					}

					pencifeeds_add_log( 'Saving ' . $title );

					if ( ! $oldPostID ) {
						$postId = wp_insert_post( $post );
					} else {
						$postId     = $oldPostID;
						$post['ID'] = $oldPostID;

						wp_update_post( $post );
					}

					pencifeeds_add_log( 'Saved ' . $title );

					// Create tags
					if ( count( $categories ) && $this->extract_tags ) {
						wp_set_post_tags( $postId, $categories, true );
					}

					// Create post meta
					if ( $postId ) {
						if ( $pubDate ) {
							update_post_meta( $postId, '_rss_pub_date', $pubDate );
						}

						if ( $author ) {
							update_post_meta( $postId, '_rss_author', $author );
						}

						if ( $language ) {
							update_post_meta( $postId, '_rss_language', $language );
						}

						if ( $isRTL ) {
							update_post_meta( $postId, '_rss_is_rtl', $isRTL );
						}

						update_post_meta( $postId, '_rss_original_url', $url );

						update_post_meta( $postId, '_rss_feed_id', $this->id );
					}

					if ( $postId ) {
						$parts  = parse_url( $url );
						$domain = $parts['scheme'] . '://' . $parts['host'];

						if ( isset( $parts['port'] ) && $parts['port'] && ( $parts['port'] != '80' ) ) {
							$domain .= ':' . $parts['port'];
						}

						// Relative path URL
						$relativeUrl = $domain;
						if ( isset( $parts['path'] ) && $parts['path'] ) {
							$pathParts = explode( '/', $parts['path'] );
							if ( count( $pathParts ) ) {
								unset( $pathParts[ count( $pathParts ) - 1 ] );
								$relativeUrl = $domain . '/' . implode( '/', $pathParts );
							}
						}
					}

					// Create image if any
					if ( $postId && $thumbnailFeaturedID ) {
						\set_post_thumbnail( $postId, $thumbnailFeaturedID );
					} elseif ( $postId && $thumbnailFallbackID ) {
						\set_post_thumbnail( $postId, $thumbnailFallbackID );
					} elseif ( $thumbnail && $postId ) {
						if ( strlen( $thumbnail ) > 2 && ( substr( $thumbnail, 0, 1 ) == '/' ) && ( ( substr( $thumbnail, 0, 2 ) != '//' ) ) ) {
							$thumbnail = $domain . $thumbnail;
						} elseif ( ( substr( trim( $thumbnail ), 0, 4 ) != 'http' ) && ( substr( $thumbnail, 0, 2 ) != '//' ) ) {
							$thumbnail = $relativeUrl . '/' . $thumbnail;
						}

						pencifeeds_add_log( 'Thumbnail found: ' . $thumbnail );
						$tmp        = $this->magicDownload( $thumbnail );
						$file_array = array();

						if ( is_wp_error( $tmp ) ) {
							$errors = $tmp->get_error_messages();
							if ( is_array( $errors ) ) {
								$errors = implode( ', ', $errors );
							}
							pencifeeds_add_log( 'Error: ' . $errors );
						}

						preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $thumbnail, $matches );

						if ( ! isset( $matches[0] ) || ( ! $matches[0] ) ) {
							$parts   = explode( '/', $thumbnail );
							$matches = array( $parts[ count( $parts ) - 1 ] . '.jpg' );
						}

						if ( $matches[0] ) {
							pencifeeds_add_log( 'File name matched' );
							$file_array['name']     = basename( $matches[0] );
							$file_array['tmp_name'] = $tmp;

							if ( is_wp_error( $tmp ) ) {
								pencifeeds_add_log( 'Error downloading file' );
								@unlink( $file_array['tmp_name'] );
								$file_array['tmp_name'] = '';
							} else {
								// do the validation and storage stuff
								$thumbnailId = $this->downloadFile( $file_array, $postId );
								//\media_handle_sideload( $file_array, $postId, '', array('post_author' => $this->author));

								// If error storing permanently, unlink
								if ( is_wp_error( $thumbnailId ) ) {
									pencifeeds_add_log( 'Error with SideLoad: ' . $thumbnailId->get_error_messages() );
									@unlink( $file_array['tmp_name'] );
								} else {
									pencifeeds_add_log( 'Saving thumbnail: ' . $thumbnailId );
									\set_post_thumbnail( $postId, $thumbnailId );
									pencifeeds_add_log( 'Thumbnail saved' );

									// Delete thumbnail from content
								}
							}
						} else {
							pencifeeds_add_log( 'File name mismatched' );
						}
					}

					// Download images
					if ( $postId && $this->download_images ) {
						pencifeeds_add_log( 'Downloading images' );

						$pageImages = $this->getImages( $content );
						if ( is_array( $pageImages ) && count( $pageImages ) ) {

							foreach ( $pageImages as $pageImage ) {
								$origSrc = $src = $pageImage;
								$newSrc  = '';

								if ( strlen( $src ) > 2 && ( substr( $src, 0, 1 ) == '/' ) && ( ( substr( $src, 0, 2 ) != '//' ) ) ) {
									$src = $domain . $src;
								} elseif ( ( substr( $src, 0, 4 ) != 'http' ) && ( substr( $src, 0, 2 ) != '//' ) ) {
									$src = $relativeUrl . '/' . $src;
								}
								pencifeeds_add_log( 'Image: ' . $src );

								$tmp = $this->magicDownload( $src );

								if ( is_wp_error( $tmp ) ) {
									$errors = $tmp->get_error_messages();
									if ( is_array( $errors ) ) {
										$errors = implode( ', ', $errors );
									}
									pencifeeds_add_log( 'Error: ' . $errors );
								}

								$file_array = array();

								preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $src, $matches );

								if ( ! isset( $matches[0] ) || ( ! $matches[0] ) ) {
									$parts   = explode( '/', $src );
									$matches = array( $parts[ count( $parts ) - 1 ] . '.jpg' );
								}

								if ( $matches[0] ) {
									$file_array['name']     = basename( $matches[0] );
									$file_array['tmp_name'] = $tmp;

									if ( is_wp_error( $tmp ) ) {
										@unlink( $file_array['tmp_name'] );
										$file_array['tmp_name'] = '';
									} else {
										// do the validation and storage stuff
										$imageId = $this->downloadFile( $file_array, $postId );
										//\media_handle_sideload( $file_array, $postId, '', array('post_author' => $this->author));

										// If error storing permanently, unlink
										if ( is_wp_error( $imageId ) ) {
											pencifeeds_add_log( 'Error with SideLoad: ' . $imageId->get_error_messages() );
											@unlink( $file_array['tmp_name'] );
										} else {
											$newSrc = wp_get_attachment_url( $imageId );
											pencifeeds_add_log( 'New Image URL: ' . $newSrc );
										}
									}
								} else {
									@unlink( $tmp );
								}

								if ( $newSrc ) {
									$content = str_replace( $origSrc, $newSrc, $content );
								}
							}

							$updatedPost = array(
								'ID'           => $postId,
								'post_content' => $content,
							);

							// Update the post into the database
							wp_update_post( $updatedPost );
						}

						pencifeeds_add_log( 'Download complete' );
					}

					if ( $this->content_wrapper ) {
						$content     = str_replace( '%CONTENT%', $content, $this->content_wrapper );
						$updatedPost = array(
							'ID'           => $postId,
							'post_content' => $content,
						);

						// Update the post in the database
						wp_update_post( $updatedPost );
					}

					$importedNum ++;
				}

				$this->etag          = $resource->getEtag();
				$this->last_modified = $resource->getLastModified();
			}
		} catch ( PicoFeedException $e ) {
			return false;
		}
	}

	private function getHttpResponseCode( $url ) {
		$headers = get_headers( $url );

		return substr( $headers[0], 9, 3 );
	}

	/**
	 * Downloads file using one of available method
	 *
	 * @param string url
	 *
	 * @return string temp file name
	 */
	private function magicDownload( $url ) {
		$url = str_replace( ' ', '%20', $url );
		$url = htmlspecialchars_decode( $url );
		$tmp = \download_url( $url );

		// Alternative download methods
		if ( is_wp_error( $tmp ) ) {
			$tmpfname = wp_tempnam( $url );

			if ( ! $tmpfname ) {
				return new \WP_Error( 'http_no_file', __( 'Could not create Temporary file.' ) );
			}

			$contents = @file_get_contents( $url, false );
			if ( $contents !== false ) {
				file_put_contents( $tmpfname, $contents );

				return $tmpfname;
			}

			// try with curl
			$headers   = array();
			$headers[] = "User-Agent:Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13";
			$headers[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
			$headers[] = "Accept-Language:en-us,en;q=0.5";
			$headers[] = "Accept-Encoding:gzip,deflate";
			$headers[] = "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.7";
			$headers[] = "Keep-Alive:115";
			$headers[] = "Connection:keep-alive";
			$headers[] = "Cache-Control:max-age=0";

			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $curl, CURLOPT_ENCODING, "gzip" );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
			$contents = curl_exec( $curl );
			curl_close( $curl );

			if ( $contents ) {
				file_put_contents( $tmpfname, $contents );

				return $tmpfname;
			}

			return new \WP_Error( 'http_404', 'Unable to download file' );
		} else {
			return $tmp;
		}
	}

	private function downloadFile( $file_array, $postId ) {
		$wp_uploads = wp_upload_dir();
		$tmpname    = wp_unique_filename( $wp_uploads['path'], $file_array['name'] );
		$new_file   = $wp_uploads['path'] . '/' . $tmpname;
		pencifeeds_add_log( 'Temp File name: ' . $file_array['tmp_name'] );
		pencifeeds_add_log( 'New file: ' . $new_file );
		file_put_contents( $new_file, file_get_contents( $file_array['tmp_name'] ) );
		@unlink( $file_array['tmp_name'] );
		pencifeeds_add_log( 'File saved successfully' );

		$wp_filetype = wp_check_filetype( $file_array['name'], null );

		//$wp_filetype = array('type'=>'image/jpeg');

		$attachment_data = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_array['name'] ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_author'    => $this->author,
		);
		$thumbnailId     = wp_insert_attachment( $attachment_data, $new_file, $postId );

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment, and update the database record.
		$attach_data = wp_generate_attachment_metadata( $thumbnailId, $new_file );
		wp_update_attachment_metadata( $thumbnailId, $attach_data );

		return $thumbnailId;
	}

	/**
	 * Check if text contains any of specified words
	 *
	 * @param $text
	 * @param $words
	 *
	 * @return bool
	 */
	private function containsWords( $text, $words ) {
		$wordsList = explode( ',', $words );

		if ( $wordsList && count( $wordsList ) ) {
			foreach ( $wordsList as $word ) {
				if ( $word && ( strpos( strtolower( $text ), strtolower( $word ) ) !== false ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get all page images
	 *
	 * @param $html string
	 *
	 * @return array with images
	 */
	private function getImages( $content ) {
		if ( ! function_exists( 'file_get_html' ) ) {
			require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/libs/simplehtml/simple_html_dom.php' );
		}

		$html = str_get_html( $content );

		if ( ! $html || ! is_object( $html ) ) {
			return 'Error loading HTML';
		}

		$images = array();

		// Find all images
		foreach ( $html->find( 'img' ) as $element ) {
			$images[] = trim( $element->src );
		}

		return $images;
	}

	/**
	 * Return YouTubeVideo ID if any available
	 * Return null if no ID found
	 *
	 * @param content
	 * return string VideoID
	 */
	private function getYouTubeVideoID( $content ) {
		if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $content, $match ) ) {
			return $match[1];
		} else {
			return null;
		}
	}

	/**
	 * Removes first image found in an HTML document
	 *
	 * @param content
	 *
	 * @return HTML
	 */
	private function removeFirstImage( $content ) {
		if ( ! function_exists( 'file_get_html' ) ) {
			require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/libs/simplehtml/simple_html_dom.php' );
		}

		$html = str_get_html( $content );

		if ( ! $html || ! is_object( $html ) ) {
			return 'Error loading HTML';
		}

		// Find all images and remove the first one
		foreach ( $html->find( 'img' ) as $element ) {
			$element->outertext = '';
			break;
		}

		$str = $html->save();

		return $str;
	}

	private function removeLinks( $content ) {
		if ( ! function_exists( 'file_get_html' ) ) {
			require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/libs/simplehtml/simple_html_dom.php' );
		}

		$html = str_get_html( $content );

		if ( ! $html || ! is_object( $html ) ) {
			return 'Error loading HTML';
		}

		// Find all links and leave only text
		foreach ( $html->find( 'a' ) as $element ) {
			$element->outertext = $element->innertext;
		}

		$str = $html->save();

		return $str;
	}

	/**
	 * Replace N-th occurrence of substring
	 *
	 * @param string content
	 * @param int occurrence number
	 *
	 * @return string new HTML
	 */
	private function addMoreTag( $content, $number ) {
		if ( ! function_exists( 'file_get_html' ) ) {
			require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/libs/simplehtml/simple_html_dom.php' );
		}

		$html = str_get_html( $content );

		if ( ! $html || ! is_object( $html ) ) {
			return 'Error loading HTML';
		}

		// Find all paragraphas
		$i = 0;
		foreach ( $html->find( 'p' ) as $element ) {
			$i ++;
			if ( $i == $number ) {
				$element->outertext = $element->makeup() . $element->innertext . '<!--more--></p>';
				break;
			}
		}

		$str = $html->save();

		return $str;
	}

	/**
	 * Replace duplicated spaces
	 */
	private function removeSpaces( $content ) {
		$content = preg_replace( "/[\r\n]\s*[\r\n]/", "\n\n", $content );

		return preg_replace( '/[ ]+/', ' ', $content );
	}

	private function cleanUpExcerpt( $content ) {
		return $this->removeSpaces(
			htmlspecialchars_decode(
				trim(
					strip_tags(
						str_replace(
							array( '</p>', '<br>', '<br/>', '<br />' ),
							array( "\n</p>", "\n<br>", "\n<br>", "\n<br>" ),
							$content
						)
					)
				)
			)
		);
	}

	private function getImageFromFeed( $misc ) {
		if ( is_array( $misc ) ) {
			return reset( $misc );
		} else {
			return $misc;
		}
	}

	// private function getBaseURL($content)
	// {
	//     if (!function_exists('file_get_html')) {
	//         require_once(PENCIFEEDS_PLUGIN_DIR.'/classes/libs/simplehtml/simple_html_dom.php');
	//     }

	//     $html = str_get_html($content);

	//     if (!$html || !is_object($html)) {
	//         return 'Error loading HTML';
	//     }

	//     $baseURL = $html->find('base', 0);
	//     if ($baseURL) {
	//         return $baseURL->href;
	//     } else {
	//         return null;
	//     }
	// }

	// private function rebuildImageURLs($url, $baseURL, $content)
	// {
	//     if (!function_exists('file_get_html')) {
	//         require_once(PENCIFEEDS_PLUGIN_DIR.'/classes/libs/simplehtml/simple_html_dom.php');
	//     }

	//     $html = str_get_html($content);

	//     if (!$html || !is_object($html)) {
	//         return 'Error loading HTML';
	//     }

	//     $parts = parse_url($url);
	//     $domain = $parts['scheme'].'://'.$parts['host'];

	//     if (isset($parts['port']) && $parts['port'] && ($parts['port'] != '80')) {
	//         $domain .= ':'.$parts['port'];
	//     }

	//     // Relative path URL
	//     $relativeUrl = $domain;
	//     if (isset($parts['path']) && $parts['path']) {
	//         $pathParts = explode('/', $parts['path']);
	//         if (count($pathParts)) {
	//             unset($pathParts[count($pathParts)-1]);
	//             $relativeUrl = $domain.'/'.implode('/',$pathParts);
	//         }
	//     }

	//     if ($baseURL) {
	//         if (substr($baseURL, strlen($baseURL)-1, 1) == '/') {
	//             $baseURL = substr($baseURL, 0, strlen($baseURL)-1);
	//         }
	//         $relativeUrl = $baseURL;
	//     }

	//     echo $relativeUrl.'<br>';

	//     // Modify image and CSS URL's adding domain name if needed
	//     foreach($html->find('img') as $element) {
	//         $src = trim($element->src);
	//         if (strlen($src)>2 && (substr($src, 0, 1) == '/') && ((substr($src, 0, 2) != '//'))) {
	//             $src = $domain . $src;
	//         } elseif (substr($src, 0, 2) == '//') {
	//             $src = $parts['scheme'].':'.$src;
	//         } elseif (substr($src, 0, 4) != 'http') {
	//             $src = $relativeUrl .'/'.$src;
	//         }
	//         echo $element->src.' - ';
	//         echo $src.'<br>';
	//         $element->src = $src;

	//     }
	//     echo $html->save();
	//     exit;

	//     $str = $html->save();

	//     return $str;
	// }

}

?>