<?php

namespace WPDataAccess\API;

use  WPDataAccess\Connection\WPDADB ;
use  WPDataAccess\Data_Dictionary\WPDA_Dictionary_Access ;
use  WPDataAccess\Data_Dictionary\WPDA_List_Columns_Cache ;
use  WPDataAccess\Data_Tables\WPDA_Inline_Search ;
use  WPDataAccess\Data_Tables\WPDA_Search_Builder ;
use  WPDataAccess\Data_Tables\WPDA_Search_Panes ;
use  WPDataAccess\Plugin_Table_Models\WPDA_Media_Model ;
use  WPDataAccess\Plugin_Table_Models\WPDA_Table_Settings_Model ;
use  WPDataAccess\Utilities\WPDA_WP_Media ;
use  WPDataAccess\WPDA ;
class WPDA_Table
{
    private static  $user_roles = null ;
    private static  $user_login = null ;
    /**
     * Perform query and return result as JSON response in jQuery DataTable format.
     *
     * @param string   $schema_name Schema name (database).
     * @param string   $table_name Table Name.
     * @param int      $start Page number (zero based).
     * @param int      $length Rows per page.
     * @param string   $order Sorting columns.
     * @param string   $search Filter.
     * @param int      $draw Internal use.
     * @param int|null $records_total Total record count from first query.
     * @param int|null $records_filtered Record filtered from previous query.
     * @return \WP_Error|\WP_REST_Response
     */
    public static function datatable(
        $schema_name,
        $table_name,
        $start,
        $length,
        $order,
        $search,
        $draw,
        $records_total,
        $records_filtered
    )
    {
        $wpdadb = WPDADB::get_db_connection( $schema_name );
        
        if ( null === $wpdadb ) {
            // Error connecting.
            return self::dterror( "Error connecting to database {$schema_name}", $draw );
        } else {
            // Connected, perform queries.
            $suppress = $wpdadb->suppress_errors( true );
            $columns = rest_sanitize_array( $_POST['columns'] );
            $columns_searchable = array();
            // Contains only columns defined as searchable.
            $columns_sp_sb = array();
            // Contains all table columns to support SP and SB searches.
            $search_panes = null;
            $server_side_processing = 0 < intval( $length );
            $where_lines = array();
            
            if ( is_array( $search ) && isset( $search['value'] ) ) {
                // Get searchable columns.
                foreach ( $columns as $column ) {
                    
                    if ( isset( $column['name'], $column['searchable'], $column['data_type'] ) && '' !== $column['name'] ) {
                        if ( 'true' === $column['searchable'] ) {
                            $columns_searchable[] = array(
                                'column_name' => $column['name'],
                                'data_type'   => $column['data_type'],
                            );
                        }
                        if ( $server_side_processing ) {
                            $columns_sp_sb[] = array(
                                'column_name' => $column['name'],
                                'data_type'   => $column['data_type'],
                            );
                        }
                    }
                
                }
                // Add search filter.
                $where_lines[] = WPDA::construct_where_clause(
                    $schema_name,
                    $table_name,
                    $columns_searchable,
                    $search['value'],
                    false,
                    true
                );
            }
            
            $where = WPDA_Table::construct_where( $where_lines );
            $sqlorder = '';
            if ( is_array( $order ) ) {
                foreach ( $order as $orderby ) {
                    
                    if ( isset( $orderby['column'], $orderby['dir'] ) ) {
                        $column = intval( $orderby['column'] ) + 1;
                        $dir = $orderby['dir'];
                        
                        if ( '' === $sqlorder ) {
                            $sqlorder = 'order by ';
                        } else {
                            $sqlorder .= ',';
                        }
                        
                        $sqlorder .= sanitize_sql_orderby( "{$column} {$dir}" );
                    }
                
                }
            }
            $sql = "\n\t\t\t\t\tselect * \n\t\t\t\t\tfrom `%1s` \n\t\t\t\t\t{$where} \n\t\t\t\t\t{$sqlorder} \n\t\t\t\t";
            if ( 0 < $length ) {
                $sql .= " limit {$length} offset {$start} ";
            }
            // Query.
            $query = $wpdadb->prepare( $sql, array( $table_name ) );
            // Get result set.
            $dataset = $wpdadb->get_results( $query, 'ARRAY_N' );
            // Handle WordPress media library columns.
            $media_columns = array();
            
            if ( isset( $_POST['media'] ) && is_array( $_POST['media'] ) ) {
                $media_array = rest_sanitize_array( $_POST['media'] );
                foreach ( $media_array as $media ) {
                    if ( isset( $media['target'], $columns[$media['target']]['name'] ) ) {
                        // Column target is stored in element data.
                        $media_columns[] = $columns[$media['target']]['data'];
                    }
                }
            }
            
            if ( 0 < count( $media_columns ) ) {
                // Update dataset: add media links
                for ( $i = 0 ;  $i < count( $dataset ) ;  $i++ ) {
                    foreach ( $media_columns as $media_column ) {
                        if ( isset( $dataset[$i][$media_column] ) ) {
                            $dataset[$i][$media_column] = WPDA_WP_Media::get_media_url( $dataset[$i][$media_column] );
                        }
                    }
                }
            }
            if ( $wpdadb->last_error ) {
                // Handle SQL errors.
                return self::dterror( $wpdadb->last_error, $draw, array(
                    'query' => str_replace( array( "\r", "\n", "\t" ), '', $sql ),
                ) );
            }
            // Count total rows.
            
            if ( $records_total !== null && is_numeric( $records_total ) ) {
                // Reuse previous count.
                $count_total = $records_total;
            } else {
                $get_count_total = $wpdadb->get_results( $wpdadb->prepare( '
							select count(1)
							from `%1s`
							', array( $table_name ) ), 'ARRAY_N' );
                if ( $wpdadb->last_error ) {
                    // Handle SQL errors.
                    return self::dterror( $wpdadb->last_error, $draw, array(
                        'query' => str_replace( array( "\r", "\n", "\t" ), '', $sql ),
                    ) );
                }
                $count_total = $get_count_total[0][0];
            }
            
            // Count rows filtered.
            
            if ( $where !== '' ) {
                
                if ( $records_filtered !== null && is_numeric( $records_filtered ) ) {
                    // Reuse previous count.
                    $count_filtered = $records_filtered;
                } else {
                    $get_count_filtered = $wpdadb->get_results( $wpdadb->prepare( "\n\t\t\t\t\t\t\t\tselect count(1)\n\t\t\t\t\t\t\t\tfrom `%1s`\n\t\t\t\t\t\t\t\t{$where}\n\t\t\t\t\t\t\t\t", array( $table_name ) ), 'ARRAY_N' );
                    if ( $wpdadb->last_error ) {
                        // Handle SQL errors.
                        return self::dterror( $wpdadb->last_error, $draw, array(
                            'query' => str_replace( array( "\r", "\n", "\t" ), '', $sql ),
                        ) );
                    }
                    $count_filtered = $get_count_filtered[0][0];
                }
            
            } else {
                $count_filtered = $count_total;
            }
            
            $wpdadb->suppress_errors( $suppress );
            $response = array(
                'data'            => $dataset,
                'draw'            => $draw,
                'error'           => '',
                'recordsTotal'    => (int) $count_total,
                'recordsFiltered' => (int) $count_filtered,
                'search'          => $search,
            );
            if ( null !== $search_panes ) {
                $response['searchPanes'] = $search_panes;
            }
            if ( 'on' === WPDA::get_option( WPDA::OPTION_PLUGIN_DEBUG ) ) {
                $response['debug'] = array(
                    'query'   => str_replace( array( "\r", "\n", "\t" ), '', $query ),
                    'where'   => $where,
                    'orderby' => $sqlorder,
                );
            }
            return new \WP_REST_Response( $response, 200 );
        }
    
    }
    
    private static function construct_where( $where_lines )
    {
        
        if ( 0 < count( array_filter( $where_lines ) ) ) {
            // Apply all searches.
            return ' where (' . implode( ') and (', array_filter( $where_lines ) ) . ') ';
        } else {
            return "";
        }
    
    }
    
    private static function dterror( $error, $draw, $debug = null )
    {
        $response = array(
            'data'            => [],
            'draw'            => $draw,
            'error'           => $error,
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
        );
        if ( null !== $debug && 'on' === WPDA::get_option( WPDA::OPTION_PLUGIN_DEBUG ) ) {
            $response['debug'] = $debug;
        }
        return new \WP_REST_Response( $response, 200 );
    }
    
    /**
     * Perform query and return result as JSON response.
     *
     * @param string $schema_name Schema name (database).
     * @param string $table_name Table Name.
     * @param array $column_name Column name.
     * @return \WP_Error|\WP_REST_Response
     */
    public static function lov( $schema_name, $table_name, $column_name )
    {
        $wpdadb = WPDADB::get_db_connection( $schema_name );
        
        if ( null === $wpdadb ) {
            // Error connecting.
            return new \WP_Error( 'error', "Error connecting to database {$schema_name}", array(
                'status' => 420,
            ) );
        } else {
            // Connected, perform queries.
            $suppress = $wpdadb->suppress_errors( true );
            $dataset = $wpdadb->get_results( $wpdadb->prepare( "select distinct `%1s` from `%1s` order by 1", array( $column_name, $table_name ) ), 'ARRAY_N' );
            $wpdadb->suppress_errors( $suppress );
            // Send response.
            
            if ( '' === $wpdadb->last_error ) {
                return WPDA_API::WPDA_Rest_Response( '', $dataset );
            } else {
                return new \WP_Error( 'error', $wpdadb->last_error, array(
                    'status' => 420,
                ) );
            }
        
        }
    
    }
    
    /**
     * Perform query and return result as JSON response.
     *
     * @param string $schema_name Schema name (database).
     * @param string $table_name Table Name.
     * @param array $primary Primary (key|value pairs.
     * @return \WP_Error|\WP_REST_Response
     */
    public static function get( $schema_name, $table_name, $primary_key )
    {
        $wpdadb = WPDADB::get_db_connection( $schema_name );
        
        if ( null === $wpdadb ) {
            // Error connecting.
            return new \WP_Error( 'error', "Error connecting to database {$schema_name}", array(
                'status' => 420,
            ) );
        } else {
            // Connected, perform queries.
            $suppress = $wpdadb->suppress_errors( true );
            // Check primary key and sanitize key values.
            $sanitized_primary_key_values = self::sanitize_primary_key( $schema_name, $table_name, $primary_key );
            if ( false === $sanitized_primary_key_values ) {
                return new \WP_Error( 'error', "Invalid arguments", array(
                    'status' => 420,
                ) );
            }
            $where = '';
            foreach ( $sanitized_primary_key_values as $primary_key_column => $sanitized_primary_key_value ) {
                $where = ( '' === $where ? ' where ' : ` {$where} and ` );
                $where .= $wpdadb->prepare( " `%1s` like %s ", array( $primary_key_column, $sanitized_primary_key_value ) );
            }
            $dataset = $wpdadb->get_results( $wpdadb->prepare( "\n\t\t\t\t\t\t\tselect *\n\t\t\t\t\t\t\tfrom `%1s`\n\t\t\t\t\t\t\t{$where}\n\t\t\t\t\t\t", array( $table_name ) ), 'ARRAY_A' );
            $wpdadb->suppress_errors( $suppress );
            // Send response.
            
            if ( 0 === $wpdadb->num_rows ) {
                return new \WP_Error( 'error', "No data found", array(
                    'status' => 420,
                ) );
            } elseif ( 1 === $wpdadb->num_rows ) {
                return WPDA_API::WPDA_Rest_Response( '', $dataset );
            } else {
                return new \WP_Error( 'error', "Invalid arguments", array(
                    'status' => 420,
                ) );
            }
        
        }
    
    }
    
    public static function insert( $schema_name, $table_name, $column_values )
    {
        $wpdadb = WPDADB::get_db_connection( $schema_name );
        
        if ( null === $wpdadb ) {
            // Error connecting.
            return new \WP_Error( 'error', "Error connecting to database {$schema_name}", array(
                'status' => 420,
            ) );
        } else {
            // Sanitize column names and values.
            $sanitized_column_values = self::sanitize_column_values( $schema_name, $table_name, $column_values );
            if ( false === $sanitized_column_values ) {
                return new \WP_Error( 'error', "Invalid arguments", array(
                    'status' => 420,
                ) );
            }
            // Insert row.
            $rows_inserted = $wpdadb->insert( $table_name, $sanitized_column_values );
            // Send response.
            
            if ( 1 === $rows_inserted ) {
                return WPDA_API::WPDA_Rest_Response( '', 'Row successfully inserted' );
            } else {
                
                if ( '' !== $wpdadb->last_error ) {
                    return new \WP_Error( 'error', $wpdadb->last_error, array(
                        'status' => 420,
                    ) );
                } else {
                    return new \WP_Error( 'error', 'Insert failed', array(
                        'status' => 420,
                    ) );
                }
            
            }
        
        }
    
    }
    
    public static function update(
        $schema_name,
        $table_name,
        $primary_key,
        $column_values
    )
    {
        $wpdadb = WPDADB::get_db_connection( $schema_name );
        
        if ( null === $wpdadb ) {
            // Error connecting.
            return new \WP_Error( 'error', "Error connecting to database {$schema_name}", array(
                'status' => 420,
            ) );
        } else {
            // Check primary key and sanitize key values.
            $sanitized_primary_key_values = self::sanitize_primary_key( $schema_name, $table_name, $primary_key );
            if ( false === $sanitized_primary_key_values ) {
                return new \WP_Error( 'error', "Invalid arguments", array(
                    'status' => 420,
                ) );
            }
            // Sanitize column names and values.
            $sanitized_column_values = self::sanitize_column_values( $schema_name, $table_name, $column_values );
            if ( false === $sanitized_column_values ) {
                return new \WP_Error( 'error', "Invalid arguments", array(
                    'status' => 420,
                ) );
            }
            // Update row.
            $rows_inserted = $wpdadb->update( $table_name, $sanitized_column_values, $sanitized_primary_key_values );
            // Send response.
            
            if ( 0 === $rows_inserted ) {
                return WPDA_API::WPDA_Rest_Response( '', 'Nothing to update' );
            } elseif ( 1 === $rows_inserted ) {
                return WPDA_API::WPDA_Rest_Response( '', 'Row successfully updated' );
            } else {
                
                if ( '' !== $wpdadb->last_error ) {
                    return new \WP_Error( 'error', $wpdadb->last_error, array(
                        'status' => 420,
                    ) );
                } else {
                    return new \WP_Error( 'error', 'Update failed', array(
                        'status' => 420,
                    ) );
                }
            
            }
        
        }
    
    }
    
    public static function delete( $schema_name, $table_name, $primary_key )
    {
        $wpdadb = WPDADB::get_db_connection( $schema_name );
        
        if ( null === $wpdadb ) {
            // Error connecting.
            return new \WP_Error( 'error', "Error connecting to database {$schema_name}", array(
                'status' => 420,
            ) );
        } else {
            // Check primary key and sanitize key values.
            $sanitized_primary_key_values = self::sanitize_primary_key( $schema_name, $table_name, $primary_key );
            if ( false === $sanitized_primary_key_values ) {
                return new \WP_Error( 'error', "Invalid arguments", array(
                    'status' => 420,
                ) );
            }
            // Delete row.
            $rows_deleted = $wpdadb->delete( $table_name, $sanitized_primary_key_values );
            // Send response.
            
            if ( 0 === $rows_deleted ) {
                return WPDA_API::WPDA_Rest_Response( '', 'No data found' );
            } elseif ( 1 === $rows_deleted ) {
                return WPDA_API::WPDA_Rest_Response( '', 'Row successfully deleted' );
            } else {
                
                if ( '' !== $wpdadb->last_error ) {
                    return new \WP_Error( 'error', $wpdadb->last_error, array(
                        'status' => 420,
                    ) );
                } else {
                    return new \WP_Error( 'error', 'Delete failed', array(
                        'status' => 420,
                    ) );
                }
            
            }
        
        }
    
    }
    
    /**
     * Perform query and return result as JSON response.
     *
     * @param string $schema_name Schema name (database).
     * @param string $table_name Table Name.
     * @param string $page Page number.
     * @param string $rows Rows per page.
     * @param string $order Sorting columns.
     * @param string $order_dir Asc (default) or desc.
     * @param string $search Filter.
     * @return \WP_Error|\WP_REST_Response
     */
    public static function select(
        $schema_name,
        $table_name,
        $page,
        $rows,
        $order,
        $order_dir,
        $search
    )
    {
        $wpdadb = WPDADB::get_db_connection( $schema_name );
        
        if ( null === $wpdadb ) {
            // Error connecting.
            return new \WP_Error( 'error', "Error connecting to database {$schema_name}", array(
                'status' => 420,
            ) );
        } else {
            // Connected, perform queries.
            $suppress = $wpdadb->suppress_errors( true );
            $where = '';
            
            if ( null !== $search ) {
                // Add search filter.
                $wpda_list_columns = WPDA_List_Columns_Cache::get_list_columns( $schema_name, $table_name );
                $where = WPDA::construct_where_clause(
                    $schema_name,
                    $table_name,
                    $wpda_list_columns->get_searchable_table_columns(),
                    $search
                );
                if ( '' !== $where ) {
                    $where = " where {$where} ";
                }
            }
            
            $sqlorder = '';
            
            if ( null !== $order ) {
                // Add order by.
                $_order = explode( ',', $order );
                //phpcs:ignore - 8.1 proof
                $_order_dir = explode( ',', (string) $order_dir );
                //phpcs:ignore - 8.1 proof
                //phpcs:ignore - 8.1 proof
                for ( $i = 0 ;  $i < count( $_order ) ;  $i++ ) {
                    // phpcs:ignore Generic.CodeAnalysis.ForLoopWithTestFunctionCall, Squiz.PHP.DisallowSizeFunctionsInLoops
                    
                    if ( '' === $sqlorder ) {
                        $sqlorder = 'order by ';
                    } else {
                        $sqlorder .= ',';
                    }
                    
                    
                    if ( isset( $_order_dir[$i] ) ) {
                        $sqlorder .= sanitize_sql_orderby( "{$_order[$i]} {$_order_dir[$i]}" );
                    } else {
                        $sqlorder .= sanitize_sql_orderby( $_order[$i] );
                    }
                
                }
            }
            
            if ( !is_numeric( $rows ) ) {
                $rows = 10;
            }
            $offset = ($page - 1) * $rows;
            // Calculate offset.
            if ( !is_numeric( $offset ) ) {
                $offset = 0;
            }
            $sql = "\n\t\t\t\t\tselect *\n\t\t\t\t\tfrom `%1s`\n\t\t\t\t\t{$where}\n\t\t\t\t\t{$sqlorder}\n\t\t\t\t";
            if ( 0 < $rows ) {
                // Show all rows (disables pagination)
                $sql .= " limit {$rows} offset {$offset} ";
            }
            // Query.
            $dataset = $wpdadb->get_results( $wpdadb->prepare( $sql, array( $table_name ) ), 'ARRAY_A' );
            if ( $wpdadb->last_error ) {
                // Handle SQL errors.
                return new \WP_Error( 'error', $wpdadb->last_error, array(
                    'status' => 420,
                ) );
            }
            // Count rows.
            $countrows = $wpdadb->get_results( $wpdadb->prepare( '
                        select count(1) as rowcount
                        from `%1s`
                    ', array( $table_name ) ), 'ARRAY_A' );
            if ( $wpdadb->last_error ) {
                // Handle SQL errors.
                return new \WP_Error( 'error', $wpdadb->last_error, array(
                    'status' => 420,
                ) );
            }
            $rowcount = ( isset( $countrows[0]['rowcount'] ) ? $countrows[0]['rowcount'] : 0 );
            
            if ( 0 < $rows ) {
                $pagecount = floor( $rowcount / $rows );
                if ( $pagecount != $rowcount / $rows ) {
                    // phpcs:ignore WordPress.PHP.StrictComparisons
                    $pagecount++;
                }
            } else {
                // Prevent division by zero
                $pagecount = 0;
            }
            
            $wpdadb->suppress_errors( $suppress );
            // Send response.
            $response = WPDA_API::WPDA_Rest_Response( '', $dataset );
            $response->header( 'X-WP-Total', $rowcount );
            // Total rows for this query.
            $response->header( 'X-WP-TotalPages', $pagecount );
            // Pages for this query.
            return $response;
        }
    
    }
    
    /**
     * Get table meta data.
     *
     * @param string $schema_name Database schema name.
     * @param string $table_name Database table name.
     * @return array\object
     */
    public static function get_table_meta_data( $schema_name, $table_name )
    {
        $access = WPDA_Table::get_table_access( $schema_name, $table_name );
        
        if ( null !== $access ) {
            $columns = WPDA_List_Columns_Cache::get_list_columns( $schema_name, $table_name );
            $settings_db = WPDA_Table_Settings_Model::query( $table_name, $schema_name );
            $settings = null;
            
            if ( isset( $settings_db[0]['wpda_table_settings'] ) ) {
                $settings = json_decode( (string) $settings_db[0]['wpda_table_settings'] );
                unset( $settings->form_labels );
                unset( $settings->list_labels );
                $settings->ui = WPDA_Settings::get_admin_settings( $schema_name, $table_name );
            }
            
            $media = array();
            foreach ( $columns->get_table_columns() as $column ) {
                $media_type = WPDA_Media_Model::get_column_media( $table_name, $column['column_name'], $schema_name );
                switch ( $media_type ) {
                    case "ImageURL":
                        $media[$column['column_name']] = $media_type;
                        break;
                    case "Hyperlink":
                        
                        if ( isset( $settings->table_settings->hyperlink_definition ) && 'text' === $settings->table_settings->hyperlink_definition ) {
                            $media[$column['column_name']] = "HyperlinkURL";
                        } else {
                            $media[$column['column_name']] = "HyperlinkObject";
                        }
                        
                        break;
                    default:
                        if ( false !== $media_type ) {
                            // Handle WordPress Media Library integration
                            $media[$column['column_name']] = "WP-{$media_type}";
                        }
                }
            }
        }
        
        return array(
            'columns'      => $columns->get_table_columns(),
            'table_labels' => $columns->get_table_header_labels(),
            'form_labels'  => $columns->get_table_column_headers(),
            'primary_key'  => $columns->get_table_primary_key(),
            'access'       => $access,
            'settings'     => $settings,
            'media'        => $media,
        );
    }
    
    private static function get_table_access( $dbs, $tbl )
    {
        
        if ( current_user_can( 'manage_options' ) ) {
            // Check administrator rights
            
            if ( is_admin() ) {
                $access = WPDA_Dictionary_Access::check_table_access_backend( $dbs, $tbl, $done );
            } else {
                $access = WPDA_Dictionary_Access::check_table_access_frontend( $dbs, $tbl, $done );
            }
            
            if ( $access ) {
                // Administrator access granted
                return array(
                    'select' => array( 'POST' ),
                    'insert' => array( 'POST' ),
                    'update' => array( 'POST' ),
                    'delete' => array( 'POST' ),
                );
            }
        }
        
        $tables = get_option( WPDA_API::WPDA_REST_API_TABLE_ACCESS );
        
        if ( false !== $tables && isset( $tables[$dbs][$tbl] ) && is_array( $tables[$dbs][$tbl] ) ) {
            $table = $tables[$dbs][$tbl];
            $table_access = new \stdClass();
            $table_access->select = WPDA_Table::get_table_access_action( $table, 'select' );
            $table_access->insert = WPDA_Table::get_table_access_action( $table, 'insert' );
            $table_access->update = WPDA_Table::get_table_access_action( $table, 'update' );
            $table_access->delete = WPDA_Table::get_table_access_action( $table, 'delete' );
            return $table_access;
        }
        
        return false;
    }
    
    private static function get_table_access_action( $table, $action )
    {
        if ( isset( $table[$action]['authorization'], $table[$action]['methods'] ) && is_array( $table[$action]['methods'] ) && 0 < count( $table[$action]['methods'] ) ) {
            
            if ( 'anonymous' === $table[$action]['authorization'] ) {
                return $table[$action]['methods'];
            } else {
                // Check authorized users
                if ( isset( $table[$action]['authorized_users'] ) && is_array( $table[$action]['authorized_users'] ) && 0 < count( $table[$action]['authorized_users'] ) && in_array( (string) WPDA_Table::get_user_login(), $table[$action]['authorized_users'] ) ) {
                    return $table[$action]['methods'];
                }
                // Check authorized roles
                if ( isset( $table[$action]['authorized_roles'] ) && is_array( $table[$action]['authorized_roles'] ) && 0 < count( $table[$action]['authorized_roles'] ) && 0 < count( array_intersect( WPDA_Table::get_user_roles(), $table[$action]['authorized_roles'] ) ) ) {
                    return $table[$action]['methods'];
                }
            }
        
        }
        return array();
    }
    
    private static function get_user_roles()
    {
        
        if ( null === WPDA_Table::$user_roles ) {
            WPDA_Table::$user_roles = WPDA::get_current_user_roles();
            if ( false === WPDA_Table::$user_roles ) {
                WPDA_Table::$user_roles = array();
            }
        }
        
        return WPDA_Table::$user_roles;
    }
    
    private static function get_user_login()
    {
        if ( null === WPDA_Table::$user_login ) {
            WPDA_Table::$user_login = WPDA::get_current_user_login();
        }
        return WPDA_Table::$user_login;
    }
    
    /**
     * Check if access is grant for requested database/table.
     *
     * @param string $dbs Remote or local database connection string.
     * @param string $tbl Database table name.
     * @param string $action Possible values: select, insert, update, delete.
     * @return bool
     */
    public static function check_table_access(
        $dbs,
        $tbl,
        $request,
        $action
    )
    {
        $tables = get_option( WPDA_API::WPDA_REST_API_TABLE_ACCESS );
        if ( false === $tables ) {
            // No tables.
            return false;
        }
        
        if ( !(isset( $tables[$dbs][$tbl][$action]['methods'] ) && is_array( $tables[$dbs][$tbl][$action]['methods'] )) ) {
            // No methods.
            return false;
        } else {
            if ( !in_array( $request->get_method(), $tables[$dbs][$tbl][$action]['methods'] ) ) {
                //phpcs:ignore - 8.1 proof
                return false;
            }
        }
        
        
        if ( !isset( $tables[$dbs][$tbl][$action]['authorization'] ) ) {
            // No authorization.
            return false;
        } else {
            if ( 'anonymous' === $tables[$dbs][$tbl][$action]['authorization'] ) {
                // Access granted to all users.
                return true;
            }
        }
        
        global  $wp_rest_auth_cookie ;
        
        if ( true !== $wp_rest_auth_cookie ) {
            // No anonymous access.
            return false;
        } else {
            if ( 'authorized' !== $tables[$dbs][$tbl][$action]['authorization'] ) {
                // Authorization check.
                return false;
            }
            // Authorized access requires a valid nonce.
            if ( !wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
                return false;
            }
            
            if ( !(isset( $tables[$dbs][$tbl][$action]['authorized_users'] ) && is_array( $tables[$dbs][$tbl][$action]['authorized_users'] )) ) {
                // No users.
                return false;
            } else {
                $requesting_user_login = WPDA_Table::get_user_login();
                if ( 0 < count( $tables[$dbs][$tbl][$action]['authorized_users'] ) && in_array( $requesting_user_login, $tables[$dbs][$tbl][$action]['authorized_users'] ) ) {
                    return true;
                }
            }
            
            
            if ( !(isset( $tables[$dbs][$tbl][$action]['authorized_roles'] ) && is_array( $tables[$dbs][$tbl][$action]['authorized_roles'] )) ) {
                // No roles.
                return false;
            } else {
                $requesting_user_roles = WPDA_Table::get_user_roles();
                if ( false === $requesting_user_roles ) {
                    $requesting_user_roles = array();
                }
                if ( 0 < count( $tables[$dbs][$tbl][$action]['authorized_roles'] ) && 0 < count( array_intersect( $requesting_user_roles, $tables[$dbs][$tbl][$action]['authorized_roles'] ) ) ) {
                    return true;
                }
            }
            
            return false;
        }
    
    }
    
    private static function sanitize_primary_key( $schema_name, $table_name, $primary_key )
    {
        $wpda_list_columns = WPDA_List_Columns_Cache::get_list_columns( $schema_name, $table_name );
        $primary_key_columns = $wpda_list_columns->get_table_primary_key();
        $sanitized_primary_key_values = [];
        foreach ( $primary_key_columns as $primary_key_column ) {
            if ( !isset( $primary_key[$primary_key_column] ) ) {
                // Invalid column name.
                return false;
            }
            $sanitized_primary_key_values[WPDA::remove_backticks( $primary_key_column )] = sanitize_text_field( wp_unslash( $primary_key[$primary_key_column] ) );
        }
        return $sanitized_primary_key_values;
    }
    
    private static function sanitize_column_values( $schema_name, $table_name, $column_values )
    {
        $wpda_list_columns = WPDA_List_Columns_Cache::get_list_columns( $schema_name, $table_name );
        $sanitized_column_values = [];
        foreach ( $column_values as $column_name => $column_value ) {
            switch ( $wpda_list_columns->get_column_data_type( $column_name ) ) {
                case null:
                    // Invalid column name.
                    return false;
                case 'tinytext':
                case 'text':
                case 'mediumtext':
                case 'longtext':
                    $sanitized_column_values[WPDA::remove_backticks( $column_name )] = sanitize_textarea_field( wp_unslash( $column_values[$column_name] ) );
                default:
                    $sanitized_column_values[WPDA::remove_backticks( $column_name )] = sanitize_text_field( wp_unslash( $column_values[$column_name] ) );
            }
        }
        return $sanitized_column_values;
    }

}