<?php

use  Box\Spout\Writer\Common\Creator\WriterEntityFactory ;
use  Box\Spout\Writer\Common\Creator\Style\StyleBuilder ;
use  Box\Spout\Common\Entity\Style\CellAlignment ;
use  Box\Spout\Common\Entity\Style\Color ;
if ( !class_exists( 'WPCSC_ENTITY' ) ) {
    class WPCSC_ENTITY
    {
        protected  $table_name ;
        protected  $logs_cache ;
        protected  $is_updated ;
        protected  $date_key ;
        protected  $export_columns ;
        protected  $col_validation ;
        public  $bulk_action_buttons ;
        public function __construct( $table_name = "" )
        {
            add_action( 'wp_ajax_ajax_handler', [ $this, 'ajax_handler' ] );
            // $this->col_validation = array_fill_keys(array_keys($this->columns_defaults_for_create()), 'required');
            $this->col_validation = array_keys( $this->columns_defaults_for_create() );
            $this->bulk_action_buttons = [];
            if ( !empty($table_name) ) {
                $this->set_table_name( $table_name );
            }
        }
        
        public function set_option_names()
        {
            $this->logs_cache = $this->table_name . '_logs';
            $this->is_updated = $this->table_name . '_is_updated';
        }
        
        public function set_logs_cache( $value = false )
        {
            update_option( $this->logs_cache, $value );
        }
        
        public function set_is_updated( $value = false )
        {
            update_option( $this->is_updated, $value );
        }
        
        protected function set_table_name( $table_name )
        {
            $this->table_name = $table_name;
        }
        
        /**
         *  check validation such are required field, number, string field etc
         *  @param $data - to sanitize
         *  @param $validations - keys to validate
         *  $validations default format should be ['key1', 'key2', 'key3']
         *  extended validations format should be
         *  @param [ 'key1' => 'required|number', 'key1' => 'required|float', 'key3' => 'required|string|length' ]
         *  etc
         */
        public function validate( $data, $validation_data = array() )
        {
            // check validation first
            if ( !empty($validation_data) ) {
                foreach ( $validation_data as $key => $validations ) {
                    $validations = explode( '|', $validations );
                    foreach ( $validations as $validation ) {
                        
                        if ( 'required' == $validation ) {
                            if ( !isset( $data[$key] ) ) {
                                return wpcsc_create_name_from_label( $key ) . ' should not be empty';
                            }
                        } elseif ( 'number' == $validation ) {
                            if ( !is_numeric( $data[$key] ) ) {
                                return wpcsc_create_name_from_label( $key ) . ' should be numeric';
                            }
                        } elseif ( 'float' == $validation ) {
                            if ( !is_float( $data[$key] ) ) {
                                return wpcsc_create_name_from_label( $key ) . ' should be numeric';
                            }
                        } elseif ( 'string' == $validation ) {
                            if ( !is_string( $data[$key] ) ) {
                                return wpcsc_create_name_from_label( $key ) . ' should be numeric';
                            }
                        } elseif ( 'unique' == $validation and !isset( $data['id'] ) ) {
                            $logs = $this->get_with( $key, $data[$key] );
                            if ( sizeof( $logs ) ) {
                                return wpcsc_create_name_from_label( $key ) . ' with value *' . $data[$key] . '* already exists.';
                            }
                        } elseif ( 'unslash' == $validation ) {
                            $data[$key] = wp_unslash( $data[$key] );
                        } elseif ( 'enc' == $validation ) {
                            if ( !empty($data[$key]) ) {
                                $data[$key] = $this->enc_email( $data[$key] );
                            }
                        } elseif ( 'email' == $validation ) {
                            if ( !filter_var( $data[$key], FILTER_VALIDATE_EMAIL ) ) {
                                return 'Incorrect email format';
                            }
                        }
                    
                    }
                }
            }
            // sanitize field values
            $sanitized_data = [];
            foreach ( $data as $key => $val ) {
                // $sanitized_data[$key] = sanitize_text_field($val);
                // TODO: implement proper sanitize to prevent HTML strips
                $sanitized_data[$key] = $val;
            }
            return $sanitized_data;
        }
        
        /**
         *  universal create function
         *  to create entries in database
         */
        public function create( $args )
        {
            $columns = array_keys( $this->columns_defaults_for_create() );
            // If cloning, skip validation
            
            if ( empty($args['clone']) ) {
                // Sanitize and validate the args
                $args = $this->validate( $args, $this->col_validation );
                if ( is_string( $args ) ) {
                    return [
                        'success' => false,
                        'reason'  => $args,
                    ];
                }
            }
            
            $data = [];
            foreach ( $columns as $col ) {
                
                if ( $args[$col] === 0 || $args[$col] === '0' ) {
                    $data[$col] = 0;
                } else {
                    $data[$col] = ( !empty($args[$col]) ? $args[$col] : '' );
                }
            
            }
            // Hint: was used for app, to maintain cache [or say if updated flag]
            $this->set_is_updated( true );
            return wpcsc_insert_log( $data, $this->table_name );
        }
        
        public function create_bulk( $data )
        {
            $created = 0;
            foreach ( $data as $row ) {
                if ( $this->create( $row ) ) {
                    $created++;
                }
            }
            if ( !$created ) {
                return false;
            }
            return count( $data ) == $created;
        }
        
        /**
         *  universal update functions
         *  $this->update - update row using id and array of data
         *  $this->update_row - same sa above but execute if there is existing data
         *  $this->update_col - update based on column
         */
        public function update( $id, $log )
        {
            // Sanitize and validate the args
            $log = $this->validate( $log, $this->col_validation );
            if ( is_string( $log ) ) {
                return [
                    'success' => false,
                    'reason'  => $log,
                ];
            }
            $updated = wpcsc_update_log( $id, $log, $this->table_name );
            $this->set_is_updated( true );
            return $updated;
        }
        
        public function update_row( $id, $log )
        {
            // $log['datetime'] = current_time('Y-m-d H:i:s');
            $args = array(
                'search_keys' => array(
                'id' => $id,
            ),
            );
            $existing = $this->get( $args );
            if ( !sizeof( $existing ) ) {
                return false;
            }
            $existing = $existing[0];
            foreach ( $log as $key => $value ) {
                $existing[$key] = $value;
            }
            $updated = wpcsc_update_log( $id, $existing, $this->table_name );
            $this->set_is_updated( true );
            return $updated;
        }
        
        public function update_col( $id, $column, $value )
        {
            global  $wpdb ;
            $table_name = $this->table_name;
            $sql = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->{$table_name}} SET {$column} = '{$value}' WHERE id = %d", $id ) );
            return $sql;
        }
        
        /**
         *  universal removal function
         *  $this->remove        - remove with id
         *  $this->remove_all    - remove all the records in database
         *  $this->remove_where  - remove from particular location
         */
        public function remove( $id )
        {
            $removed = wpcsc_delete_log( $id, $this->table_name );
            if ( $removed ) {
                $this->set_is_updated( true );
            }
            return $removed;
        }
        
        public function remove_all( $args = array() )
        {
            $logs = wpcsc_delete_log_all( $this->table_name );
            $this->set_is_updated( true );
            return $logs;
        }
        
        public function remove_where( $column, $value )
        {
            global  $wpdb ;
            $table_name = $this->table_name;
            $sql = $wpdb->prepare( "DELETE from {$wpdb->{$table_name}} WHERE {$column} = %s", $value );
            $result = wpcsc_get_logs_query( $sql, $this->table_name );
            if ( $result ) {
                $this->set_is_updated( true );
            }
            return $result;
        }
        
        /**
         *  universal GET function
         *  $this->get           - get all the data based on search args
         *  $this->get_with      - get data based on particular column
         *  $this->get_with_id   - get single data row using ID
         */
        public function get( $args = array() )
        {
            
            if ( !($is_updated = get_option( $this->is_updated )) ) {
                $logs = get_option( $this->logs_cache );
                // ToDo: Temporarily taken of cache part
                // if($logs) return $logs;
            }
            
            $search_params = $this->prepare_search_fields( $args );
            $logs = (array) wpcsc_get_logs( $search_params, $this->table_name );
            $this->set_logs_cache( $logs );
            $this->set_is_updated( false );
            return $logs;
        }
        
        public function get_with( $field, $value )
        {
            $search_params = $this->prepare_search_fields( array(
                'search_keys' => array(
                $field => $value,
            ),
            ) );
            $logs = (array) wpcsc_get_logs( $search_params, $this->table_name );
            return $logs;
        }
        
        public function get_with_id( $id )
        {
            $search_args = array(
                'search_keys' => array(
                'id' => $id,
            ),
            );
            $logs = $this->get( $search_args );
            return ( sizeof( $logs ) ? $logs[0] : false );
        }
        
        public function prepare_search_fields( $args )
        {
            $search_fields = array();
            $search_params = array();
            if ( !empty($args['orderby']) ) {
                $search_params['orderby'] = $args['orderby'];
            }
            if ( !empty($args['order']) ) {
                $search_params['order'] = $args['order'];
            }
            if ( $this->date_key != null ) {
                $search_params['date_key'] = $this->date_key;
            }
            if ( !empty($args['date_key']) ) {
                $search_params['date_key'] = $args['date_key'];
            }
            if ( !empty($args['number']) && is_numeric( $args['number'] ) ) {
                $search_params['number'] = $args['number'];
            }
            if ( !empty($args['offset']) && is_numeric( $args['offset'] ) ) {
                $search_params['offset'] = $args['offset'];
            }
            if ( !empty($args['since']) ) {
                $search_params['since'] = $args['since'];
            }
            if ( !empty($args['until']) ) {
                $search_params['until'] = $args['until'];
            }
            // remove date from search_keys as date received in string format
            // and assign it to $args['date'] to cut into part for sql query
            
            if ( !empty($args['search_keys']['date']) ) {
                $args['date'] = $args['search_keys']['date']['value'];
                unset( $args['search_keys']['date'] );
            }
            
            
            if ( !empty($args['search_keys']['sort_by']) ) {
                $search_params['orderby'] = $args['search_keys']['sort_by']['value'];
                unset( $args['search_keys']['sort_by'] );
            } elseif ( !empty($args['sort_by']) ) {
                $search_params['orderby'] = $args['sort_by'];
            }
            
            
            if ( !empty($args['search_keys']['sort_order']) ) {
                $search_params['order'] = $args['search_keys']['sort_order']['value'];
                unset( $args['search_keys']['sort_order'] );
            } elseif ( !empty($args['sort_order']) ) {
                $search_params['orderby'] = $args['sort_order'];
            }
            
            
            if ( !empty($args['date']) ) {
                $date_range_parts = explode( '-', $args['date'] );
                $fromdate = trim( $date_range_parts[0] );
                $todate = trim( $date_range_parts[1] );
                //				unset($args['date']);
            } elseif ( !empty($args['from_date']) ) {
                $fromdate = $args['from_date'];
                $todate = current_time( 'd M, Y' );
            }
            
            if ( !empty($fromdate) and !empty($todate) ) {
                
                if ( $fromdate == $todate ) {
                    $search_params['since'] = wpcsc_get_formatted_date( $fromdate, 'U', 'M d, Y' );
                    $search_params['until'] = wpcsc_get_formatted_date( $fromdate, 'U', 'M d, Y' );
                } else {
                    $search_params['since'] = wpcsc_get_formatted_date( $fromdate, 'U', 'M d, Y' );
                    $search_params['until'] = wpcsc_get_formatted_date( $todate, 'U', 'M d, Y' );
                }
            
            }
            if ( !empty($args['search_keys']) ) {
                if ( is_array( $args['search_keys'] ) ) {
                    foreach ( $args['search_keys'] as $key => $value ) {
                        
                        if ( !is_array( $value ) ) {
                            $search_fields[$key] = array(
                                'value'   => sanitize_text_field( $value ),
                                'compare' => '=',
                            );
                        } else {
                            if ( !isset( $value['compare'] ) ) {
                                $value['compare'] = '=';
                            }
                            $final_value = ( in_array( $value['compare'], [ 'IN', 'NOT IN' ] ) ? $value['value'] : sanitize_text_field( $value['value'] ) );
                            $search_fields[$key] = array(
                                'value'   => $final_value,
                                'compare' => $value['compare'],
                            );
                            if ( !isset( $value['value'] ) ) {
                                error_log( 'WPCSC_LOGS: ' . print_r( $args['search_keys'], true ) );
                            }
                        }
                    
                    }
                }
            }
            $search_params['search_fields'] = $search_fields;
            return $search_params;
        }
        
        // ToDo: These should be optimized for the next blank plugin
        public function columns( $request = array() )
        {
            return wpcsc_get_log_table_columns( $this->table_name, true );
        }
        
        public function columns_defaults()
        {
            return array_fill_keys( $this->columns(), "" );
        }
        
        public function columns_defaults_for_create()
        {
            $columns = $this->columns_defaults();
            unset( $columns['id'] );
            unset( $columns['datetime'] );
            return $columns;
        }
        
        public function columns_default_keys( $exclude_keys = array() )
        {
            $cols = array_keys( $this->columns_defaults_for_create() );
            return array_diff( $cols, $exclude_keys );
        }
        
        /**
         * Get All Key Names for Columns
         * Used in get_columns function
         * @param array $exclude_keys
         * @return array
         */
        public function get_columns_titles( $exclude_keys = array() )
        {
            $cols = array_keys( $this->columns_defaults() );
            $cols = array_diff( $cols, $exclude_keys );
            $return = [];
            foreach ( $cols as $col ) {
                $return[$col] = ucwords( $col );
            }
            return $return;
        }
        
        public function get_column_html( $log, $col )
        {
            $col_data = array();
            switch ( $col ) {
                default:
                    $col_data[] = $log[$col];
            }
            return $col_data;
        }
        
        public function prepare_datatable_args( $search_args )
        {
            return $search_args;
        }
        
        /**
         *  callable function to create editable field
         *  make a call with corresponding class
         */
        public function editable_columns( $args = array() )
        {
            $args['table_name'] = $this->table_name;
            return $this->setup_editable( $args );
        }
        
        /**
         *  helper function for editable column
         */
        public function setup_editable( $args = array() )
        {
            $required_fields = array( 'id', 'field' );
            // Check required_fields if not available then return return empty output
            
            if ( !wpcsc_required_fields( $args, $required_fields ) ) {
                $invalid = wpcsc_find_missing_field( $args, $required_fields );
                return "One or more fields are missing: " . $invalid;
            }
            
            $defaults = array(
                'type'   => 'input',
                'bold'   => false,
                'action' => true,
            );
            $args = wp_parse_args( $args, $defaults );
            $output = $this->render_editable( $args );
            return $output;
        }
        
        /**
         *  helper function for setup_editable to render html
         */
        public function render_editable( $args )
        {
            $tname = str_replace( strtolower( ABBR ) . '_', '', $this->table_name );
            $tname = str_replace( 'tjp_', '', $tname );
            $output = '';
            $action = ( !empty($args['action']) ? 'data-action="action_editable_field"' : '' );
            $style = $args['style'] ?? '';
            $field_readable_name = wpcsc_create_name_from_label( $args['field'] );
            $spanText = ( !empty($args['value']) ? $args['value'] : 'Add ' . $field_readable_name );
            $span_class = ( !empty($args['value']) ? 'has-value' : 'no-value' );
            $icon = ( !empty($args['icon']) ? '<i class="' . $args['icon'] . ' mr-2"></i>' : '' );
            
            if ( $args['type'] == "datepicker" ) {
                $output .= "<div class='rel-date-picker' title='Edit {$field_readable_name}'>";
                $output .= '<i class="fa fa-calendar datepicker float-left mr-2 mt-2 cursor">';
                $output .= '<span class="ml-1 date-text">' . $args['value'] . '</span>';
                $output .= '</i>';
                $output .= '<input type="hidden" name="' . $args['field'] . '" class="wpcsc_ajax_field datehidden" data-tname="' . $tname . '" value="' . $args['value'] . '" data-postid="' . $args['id'] . '" data-old_value="' . $args['value'] . '" data-type="date" data-field="' . $args['field'] . '">';
                $output .= '</div>';
            } else {
                
                if ( $args['type'] == "select" ) {
                    $output .= "<div class='rel-editable' title='Edit {$field_readable_name}'>";
                    $classes = get_select_class_for_status( $args['value'] );
                    $display_name = $args['list'][$spanText] ?? $spanText;
                    // get readable name [value instead of key] from array list
                    $output .= '<span class="rel-editable-value ' . $classes . '" ' . $style . '>' . $display_name . '</span>';
                    $output .= '<select style="display:none;" class="' . $classes . ' form-control wpcsc_ajax_field" name="' . $args['field'] . '" data-tname="' . $tname . '" data-field="' . $args['field'] . '" ' . $action . ' data-old_value="' . $args['value'] . '" data-type="select" data-postid="' . $args['id'] . '">';
                    $output .= wpcsc_create_options_from_array( $args['list'], $args['value'], true );
                    $output .= '</select>';
                    $output .= '</div>';
                } else {
                    
                    if ( $args['type'] == "select_searchable" ) {
                        $output .= "<div class='rel-editable {$args['type']}' title='Edit {$field_readable_name}'>";
                        $output .= '<span class="rel-editable-value" ' . $style . '>' . $spanText . '</span>';
                        $output .= '<select style="display:none;" class="form-control js-select-plain wpcsc_ajax_field" name="' . $args['field'] . '" data-tname="' . $tname . '" data-field="' . $args['field'] . '" ' . $action . ' data-old_value="' . $args['value'] . '" data-type="select" data-postid="' . $args['id'] . '">';
                        $output .= wpcsc_create_options_from_array( $args['list'], $args['value'], true );
                        $output .= '</select>';
                        $output .= '</div>';
                    } else {
                        $class = ( $args['bold'] ? ' font-weight-bold' : '' );
                        $input_field = '';
                        
                        if ( $args['type'] == "input" ) {
                            $input_field = '<input style="display:none;" class="rel-editable-input wpcsc_ajax_field input-sm form-control" value="' . $args['value'] . '" data-tname="' . $tname . '" data-postid="' . $args['id'] . '" data-old_value="' . $args['value'] . '" data-field="' . $args['field'] . '" ' . $action . '>';
                        } else {
                            
                            if ( $args['type'] == "textarea" ) {
                                $class .= ' rel-editable-textarea';
                                $spanText = ( (!empty($args['value']) and strlen( trim( $args['value'] ) ) >= 1) ? str_replace( '\\r\\n', '<br />', $args['value'] ) : '<a class="text-primary">Add ' . $field_readable_name . '</a>' );
                                $input_field = '<textarea style="display:none; width:100% !important;" class="rel-editable-input wpcsc_ajax_field input-sm form-control" data-postid="' . $args['id'] . '" data-old_value="' . $args['value'] . '" data-tname="' . $tname . '" data-type="textarea" data-field="' . $args['field'] . '" ' . $action . '>' . $args['value'] . '</textarea>';
                            } else {
                                
                                if ( $args['type'] == "number" ) {
                                    $class .= ' rel-editable-textarea';
                                    $input_field = '<input type="number" style="display:none; width:100%;" class="rel-editable-input wpcsc_ajax_field input-sm" data-tname="' . $tname . '" data-postid="' . $args["id"] . '" data-old_value="' . $args['value'] . '" data-field="' . $args['field'] . '">';
                                }
                            
                            }
                        
                        }
                        
                        if ( isset( $args['label'] ) ) {
                            $output .= wpcsc_create_name_from_label( $args['field'] ) . ': ';
                        }
                        $output .= "<div class='rel-editable {$class}' title='Edit {$field_readable_name}'>";
                        $output .= $icon;
                        // $output .= '<span class="fa fa-trash rel-editable-actions rel-editable-delete cursor" data-postid="'.$args['id'].'" value="'.$args['value'].'" data-field="'.$args['field'].'" data-action="action_editable_field" title="Delete field_name"></span>';
                        // $output .= '<span class="fa fa-edit rel-editable-actions rel-editable-edit cursor" data-postid="'.$args['id'].'" value="'.$args['value'].'" data-field="'.$args['field'].'" '.$action.' title="Edit field_name"></span>';
                        $output .= '<span class="rel-editable-value ' . $span_class . '" ' . $style . '>' . $spanText . '</span>';
                        $output .= $input_field;
                        $output .= '</div>';
                    }
                
                }
            
            }
            
            return $output;
        }
        
        /**
         * @param $args
         * @return bool
         */
        public function editable_action( $args )
        {
            extract( $args );
            $deleting_options = array( 'delete' );
            if ( in_array( $value, $deleting_options ) ) {
                $value = '';
            }
            $log = array(
                $field     => $value,
                'admin_id' => ( !empty($admin_id) ? $admin_id : '' ),
            );
            $updated = $this->update_row( $post_id, $log );
            return $updated;
        }
        
        /**
         *  helper function for sync_with_live
         */
        public function truncate_table()
        {
            global  $wpdb ;
            $table_name = $this->table_name;
            $sql = 'Truncate table ' . $wpdb->{$table_name};
            return wpcsc_get_logs_query( $sql );
        }
        
        /**
         *  execute SQL commands
         */
        public function execute( $sql )
        {
            global  $wpdb ;
            $sql = $wpdb->query( $wpdb->prepare( $sql ) );
            return $sql;
        }
        
        public function export_row_value( $column, $log )
        {
            return ( !empty($log[$column]) ? $log[$column] : '-' );
        }
        
        public function get_spout_export_row( $row )
        {
            return array_values( $row );
        }
        
        public function export_spout( $args = array() )
        {
            $wp_uploads_dir = wp_get_upload_dir();
            $folder_path = $wp_uploads_dir['basedir'] . '/' . $args['fields']['table'] . '/';
            $folder_url = $wp_uploads_dir['basedir'] . '/' . $args['fields']['table'] . '/';
            if ( !file_exists( $folder_path ) ) {
                mkdir( $folder_path, 0777, true );
            }
            $filename = $args['fields']['table'] . '-export-' . current_time( 'ymd-his' ) . '.xlsx';
            $file_excel = $folder_url . $filename;
            $file_path_excel = $folder_path . $filename;
            // ToDo: PDF export
            $file_pdf = '';
            $file_path_pdf = '';
            $writer = WriterEntityFactory::createXLSXWriter();
            // initiate the xls writer
            $writer->openToFile( $file_path_excel );
            // write data to a file or to a PHP stream
            // ToDo: use it later
            //$writer->openToBrowser($fileName); // stream data directly to the browser
            // create heading row with columns
            $columns = ( ($this->export_columns == null or !is_array( $this->export_columns )) ? $this->columns_default_keys() : $this->export_columns );
            array_walk( $columns, function ( &$v ) {
                $v = wpcsc_create_name_from_label( $v );
            } );
            /** Create a style with the StyleBuilder */
            $heading_style = ( new StyleBuilder() )->setFontBold()->setFontSize( 12 )->setFontColor( Color::BLUE )->setBackgroundColor( Color::LIGHT_GREEN )->setShouldWrapText( false )->setCellAlignment( CellAlignment::CENTER )->build();
            $heading_row = WriterEntityFactory::createRowFromArray( $columns );
            $heading_row->setStyle( $heading_style );
            $writer->addRow( $heading_row );
            $fields = ( !empty($args['fields']) ? $args['fields'] : '' );
            // Set up search args
            $search_fields = [];
            if ( $fields != '' ) {
                foreach ( $fields as $key => $value ) {
                    
                    if ( !empty($value) ) {
                        $compare = '=';
                        $search_fields[$key] = array(
                            'value'   => sanitize_text_field( $value ),
                            'compare' => $compare,
                        );
                    }
                    
                    if ( in_array( $key, [ 'date', 'table' ] ) ) {
                        unset( $search_fields[$key] );
                    }
                }
            }
            // $search_args = [ 'date' => $fields['date'] ];
            $search_args = [
                'date'        => $fields['date'],
                'search_keys' => $search_fields,
            ];
            $logs = $this->get( $search_args );
            $row_style = ( new StyleBuilder() )->setFontSize( 12 )->setShouldWrapText()->build();
            foreach ( $logs as $log ) {
                // $row = array_values($log);
                $row = $this->get_spout_export_row( $log );
                $rowFromValues = WriterEntityFactory::createRowFromArray( $row, $row_style );
                $writer->addRow( $rowFromValues );
            }
            $writer->close();
            return [
                'total_posts' => count( $logs ),
                'table'       => $args['fields']['table'],
                'excel'       => [
                'file_name' => $file_excel,
                'file_path' => $file_path_excel,
            ],
                'pdf'         => [
                'file_name' => $file_pdf,
                'file_path' => $file_path_pdf,
            ],
            ];
        }
        
        /**
         * this function allow to copy the whole row of any database table
         * @required id to clone from
         * $params are optional, will be used if available
         * @compatible with only custom database tables
         */
        public function clone( $existing_id, $args = array() )
        {
            if ( !$existing_id ) {
                return [
                    'success' => false,
                    'reason'  => 'Data Not Received.',
                ];
            }
            $from_clone_data = $this->get_with_id( $existing_id );
            // create method will automatically these columns
            unset( $from_clone_data['id'] );
            unset( $from_clone_data['datetime'] );
            // parse args if available
            $args = wp_parse_args( $args, $from_clone_data );
            // clone param sent to sent to create method to handle clone function
            $args['clone'] = 'yes';
            // this first calls to child method
            return $this->create( $args );
        }
        
        /**
         * @return string
         * used for adding extra html or modal in the datatables for extended operations
         */
        public function get_extras( $key = '', $post = array() )
        {
            if ( $key == '' ) {
                return 'No Key Defined';
            }
            return '';
        }
        
        /**
         * Extra array items for table data attributes
         * @return array
         */
        public function get_attributes()
        {
            return [];
        }
        
        public function edit_form_html( $id, $handle )
        {
            
            if ( current_user_can( 'manage_options' ) ) {
                return 'Create <code class="font-weight-bold"> public function edit_form_html($id, $handle){}</code> in 
                        <code class="font-weight-bold">' . wpcsc_get_class_object( $handle, true ) . '</code> class for custom edit form otherwise use <br>
                        <code class="font-weight-bold">data-op="edit"</code>.';
            } else {
                return 'Edit form not found';
            }
        
        }
        
        public function bulk_actions()
        {
            $handle = str_replace( "wpcsc_", "", $this->table_name );
            return '
                <div class="btn-group float-end" role="group" aria-label="Basic example">
                    ' . implode( "", $this->bulk_action_buttons ) . '              
                    <a class="btn btn-danger btn-sm btn-ajaxy selected_bulk" title="Bulk Delete" data-op="ajax_action" data-ajax="bulk_delete" data-handle="' . $handle . '" data-title="Bulk: Delete"><i class="dashicons dashicons-trash"></i></a>
                
            </div>';
        }
        
        public function include_modal_html()
        {
            return '
                <!-- Modal to view rendered html -->
                <div class="modal fade" id="action_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="exampleModalLabel">Modal Actions</h5>
                                <button type="button" class="close wpcsc-modal-close" data-dismiss="modal" aria-label="Close" style="border: unset;background: unset;line-height: 1;">
                                    <span aria-hidden="true" style="font-size: 22px;font-weight: 700;color: #dcdcdc;">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="modalSpinner text-center">
                                    <span class="dashicons dashicons-update wpcsc-spin"></span>
                                </div>
                                <div class="modal-data"></div>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }
        
        public function ajax_handler( $post )
        {
            $response = [
                'success' => false,
            ];
            
            if ( empty($post['ajax']) ) {
                $response['reason'] = '<div class="alert alert-danger">No action found</div>';
                return $response;
            }
            
            switch ( $post['ajax'] ) {
                case 'bulk_delete':
                    
                    if ( empty($post['selected_bulk']) ) {
                        $response['reason'] = '<div class="alert alert-danger">Select leads first</div>';
                        return $response;
                    }
                    
                    $leads_ids = explode( ',', $post['selected_bulk'] );
                    
                    if ( count( $leads_ids ) ) {
                        $deleted = [];
                        foreach ( $leads_ids as $leads_id ) {
                            $deleted[] = $this->remove( $leads_id );
                        }
                        $response['success'] = true;
                        $response['reason'] = count( $deleted ) . ' deleted out of ' . count( $leads_ids ) . ' successfully';
                    } else {
                        $response['reason'] = 'No Data';
                    }
                    
                    break;
                case 'clean':
                    $response['success'] = true;
                    
                    if ( !empty($post['clean']) and $post['clean'] == 'pdf' ) {
                        $wp_uploads_dir = wp_get_upload_dir();
                        $path = $wp_uploads_dir['basedir'] . '/wpcsc-estimations/*';
                        array_map( 'unlink', array_filter( (array) glob( $path ) ) );
                        $response['reason'] = 'Removed All Estimation PDFs';
                    } elseif ( !empty($post['clean']) and $post['clean'] == 'excel' ) {
                        $wp_uploads_dir = wp_get_upload_dir();
                        $path = $wp_uploads_dir['basedir'] . '/wpcsc_leads/*';
                        array_map( 'unlink', array_filter( (array) glob( $path ) ) );
                        $response['reason'] = 'Removed All Export Data';
                    }
                    
                    break;
                default:
                    $response['reason'] = 'No task found';
                    break;
            }
            return $response;
        }
        
        public function cipher()
        {
            return "BF-CBC";
        }
        
        public function iv()
        {
            return "WPCHDSPT";
        }
        
        public function digest()
        {
            return openssl_digest( php_uname(), 'MD5', TRUE );
        }
        
        public function enc( $str )
        {
            $cipher = $this->cipher();
            $digest = $this->digest();
            $iv = $this->iv();
            return openssl_encrypt(
                $str,
                $cipher,
                $digest,
                0,
                $iv
            );
        }
        
        public function enc_email( $email )
        {
            list( $first, $last ) = explode( '@', $email );
            $enc = $this->enc( $first );
            return $enc . '@' . $last;
        }
    
    }
}