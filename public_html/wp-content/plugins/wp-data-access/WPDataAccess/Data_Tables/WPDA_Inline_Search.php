<?php

namespace WPDataAccess\Data_Tables {

	use WPDataAccess\WPDA;

	// Add Inline Search support.
	class WPDA_Inline_Search {

		public static function is( $wpdadb, $columns ) {

			$inline_where = [];

			foreach ( $columns as $column ) {
				if (
					isset(
						$column['name'],
						$column['search'],
						$column['search']['value'],
						$column['data_type']
					) &&
					'' !== $column['search']['value']
				) {
					switch ( $column['data_type'] ) {
						case 'number':
							$inline_where[] = $wpdadb->prepare( "`" . WPDA::remove_backticks( $column['name'] ) . "` = '%s'", esc_sql( $column['search']['value'] ) ); // phpcs:ignore Standard.Category.SniffName.ErrorCode
						case 'date':
							$inline_where[] = $wpdadb->prepare( "`" . WPDA::remove_backticks( $column['name'] ) . "` like '%s'", esc_sql( $column['search']['value'] ) . '%' ); // phpcs:ignore Standard.Category.SniffName.ErrorCode
						default:
							$inline_where[] = $wpdadb->prepare( "`" . WPDA::remove_backticks( $column['name'] ) . "` like '%s'", '%' . esc_sql( $column['search']['value'] ) . '%' ); // phpcs:ignore Standard.Category.SniffName.ErrorCode
					}
				}
			}

			if ( 0 < count( $inline_where ) ) {
				return implode( 'and', $inline_where );
			} else {
				return "";
			}

		}

	}

}