<?php

namespace WPDataAccess\Utilities {

	class WPDA_WP_Media {

		public static function get_media_url( $media_column ) {

			if ( null === $media_column ) {
				return null;
			}

			$media_ids = explode( ',', $media_column );
			$media_src = array();

			foreach ( $media_ids as $media_id ) {
				$url = wp_get_attachment_url( esc_attr( $media_id ) );
				if ( false !== $url ) {
					$media_object = array(
						'url'       => $url,
						'mime_type' => get_post_mime_type( $media_id ),
						'title'     => get_the_title( esc_attr( $media_id ) )
					);

					$media_src[] = json_encode( $media_object );
				} else {
					$media_src[] = $media_id; // Forces error in browser.
				}
			}

			return $media_src;

		}

	}

}