<?php
namespace NjtDuplicate\Classes;

defined( 'ABSPATH' ) || exit;
use NjtDuplicate\Helper\Utils;

class CreateDuplicate {
	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {

	}

	public function createDuplicate( $post, $parentId = '' ) {

		if ( ! Utils::checkPostTypeDuplicate( $post->post_type ) && 'attachment' !== $post->post_type ) {
			wp_die( esc_html__( 'Copy features for this post type are not enabled in setting page', 'njt_duplicate' ) );
		}

		$newPostAuthor   = wp_get_current_user();
		$newPostAuthorId = $newPostAuthor->ID;

		if ( 'attachment' !== $post->post_type ) {

			$title = trim( $post->post_title );
			// empty title
			if ( '' === $title ) {
				$title = __( 'Untitled', 'njt_duplicate' );
			}
		}

		$newPost = array(
			'menu_order'            => $post->menu_order,
			'comment_status'        => $post->comment_status,
			'ping_status'           => $post->ping_status,
			'post_author'           => $newPostAuthorId,
			'post_content'          => $post->post_content,
			'post_content_filtered' => $post->post_content_filtered,
			'post_excerpt'          => $post->post_excerpt,
			'post_mime_type'        => $post->post_mime_type,
			'post_parent'           => empty( $parentId ) ? $post->post_parent : $parentId,
			'post_password'         => $post->post_password,
			'post_status'           => 'draft',
			'post_title'            => $title,
			'post_type'             => $post->post_type,
			'post_name'             => $post->post_name,
			'post_date'             => $post->post_date,
			'post_date_gmt'         => get_gmt_from_date( $post->post_date ),
		);

		$newPostId = wp_insert_post( wp_slash( $newPost ) );

		// Duplicate postmeta, comment, attachment, children, taxonomies,
		if ( 0 !== $newPostId && ! is_wp_error( $newPostId ) ) {
			$this->duplicateDetails( $newPostId, $post );
		}

		return $newPostId;
	}

	// Run all function to copy details
	private function duplicateDetails( $newPostId, $post ) {
		$this->copyPostMeta( $newPostId, $post );
		$this->copyChildrens( $newPostId, $post );
		$this->copyComments( $newPostId, $post );
		$this->copyTaxonomies( $newPostId, $post );
	}
	// Duplicate post meta
	private function copyPostMeta( $newPostId, $post ) {
		$metaKeys = get_post_custom_keys( $post->ID );
		if ( empty( $metaKeys ) ) {
			return;
		}

		foreach ( $metaKeys as $metaKey ) {
			$metaValues = get_post_custom_values( $metaKey, $post->ID );
			foreach ( $metaValues as $metaValue ) {
				$metaValue = maybe_unserialize( $metaValue );
				add_post_meta( $newPostId, $metaKey, wp_slash( $metaValue ) );
			}
		}
	}
	// Duplicate all children post
	private function copyChildrens( $newPostId, $post ) {
		$postChildren = get_posts(
			array(
				'post_type'   => 'any',
				'numberposts' => -1,
				'post_status' => 'any',
				'post_parent' => $post->ID,
			)
		);
		foreach ( $postChildren as $children ) {
			if ( 'attachment' === $children->post_type ) {
				continue;
			}
			$this->createDuplicate( $children, $newPostId );
		}
	}
	// Duplicate all comments of post
	private function copyComments( $newPostId, $post ) {
		$comments = get_comments(
			array(
				'post_id' => $post->ID,
				'order'   => 'ASC',
				'orderby' => 'comment_date_gmt',
			)
		);

		$parentId = array();
		foreach ( $comments as $comment ) {
			// do not copy pingbacks or trackbacks
			if ( 'pingback' === $comment->comment_type || 'trackback' === $comment->comment_type ) {
				continue;
			}
			$parent                           = ( $comment->comment_parent && $parentId[ $comment->comment_parent ] ) ? $parentId[ $comment->comment_parent ] : 0;
			$newComment                       = array(
				'comment_post_ID'      => $newPostId,
				'comment_author'       => $comment->comment_author,
				'comment_author_email' => $comment->comment_author_email,
				'comment_author_url'   => $comment->comment_author_url,
				'comment_content'      => $comment->comment_content,
				'comment_type'         => $comment->comment_type,
				'comment_parent'       => $parent,
				'user_id'              => $comment->user_id,
				'comment_author_IP'    => $comment->comment_author_IP,
				'comment_agent'        => $comment->comment_agent,
				'comment_karma'        => $comment->comment_karma,
				'comment_approved'     => $comment->comment_approved,
				'comment_date'         => $comment->comment_date,
				'comment_date_gmt'     => get_gmt_from_date( $comment->comment_date ),
			);
			$newCommentId                     = wp_insert_comment( $newComment );
			$parentId[ $comment->comment_ID ] = $newCommentId;
		}
	}
	// Duplicate post taxonomies
	private function copyTaxonomies( $newPostId, $post ) {
		global $wpdb;
		if ( isset( $wpdb->terms ) ) {
			wp_set_object_terms( $newPostId, null, 'category' );
			$taxonomies = get_object_taxonomies( $post->post_type );

			if ( post_type_supports( $post->post_type, 'post-formats' ) && ! in_array( 'post_format', $taxonomies ) ) {
				$taxonomies[] = 'post_format';
			}

			foreach ( $taxonomies as $taxonomy ) {
				$postTerms = wp_get_object_terms( $post->ID, $taxonomy, array( 'orderby' => 'term_order' ) );
				$terms     = array();
				for ( $i = 0; $i < count( $postTerms ); $i++ ) {
					$terms[] = $postTerms[ $i ]->slug;
				}
				wp_set_object_terms( $newPostId, $terms, $taxonomy );
			}
		}
	}
}
