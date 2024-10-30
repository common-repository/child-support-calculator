<?php

if ( !class_exists( 'WPCSC_LEADS' ) ) {
    class WPCSC_LEADS extends WPCSC_ENTITY
    {
        use  LeadsDatatables ;
        public function __construct()
        {
            $this->table_name = 'wpcsc_leads';
            $this->date_key = 'last_modified';
            $this->col_validation = [
                'name'         => 'required|string',
                'email'        => 'required|string|enc|unique|email',
                'spouse_email' => 'string|enc',
                'children'     => 'required|string',
            ];
            $this->bulk_action_buttons = [ '<a class="btn btn-primary btn-sm modal_actions" title="Add New Lead" data-op="add" data-handle="leads" data-title="Add New Lead"><i class="dashicons dashicons-plus align-bottom"></i></a>', '<a class="btn btn-success btn-sm btn-ajaxy selected_bulk d-none" title="Mail: Resend Last Calculation Mail" data-op="send_mail" data-handle="leads" data-title="Resend Last Calculation Mail"><i class="dashicons dashicons-email align-bottom"></i></a>' ];
            $this->export_columns = $this->columns();
        }
        
        public function add_calculation( $args )
        {
            $data = [
                'lead_id'          => $args['lead_id'],
                'children'         => $args['children'],
                'income'           => $args['income'],
                'spouse_income'    => $args['spouse_income'],
                'over_nights'      => $args['over_nights'],
                'compensation'     => $args['compensation'],
                'liability'        => $args['liability'],
                'spouse_liability' => $args['spouse_liability'],
                'worksheet'        => $args['worksheet'] ?? '',
            ];
            return wpcsc_insert_log( $data, 'wpcsc_calculations' );
        }
        
        public function get_calculations( $lead_id )
        {
            $search_args = [
                'date_key'    => 'datetime',
                'orderby'     => 'datetime',
                'search_keys' => [
                'lead_id' => $lead_id,
            ],
            ];
            $search_params = $this->prepare_search_fields( $search_args );
            return wpcsc_get_logs( $search_params, 'wpcsc_calculations' );
        }
        
        public function form_html( $op, $handle, $id = '' )
        {
            $id_field = '';
            $lead = [];
            return '<div class="alert alert-danger"> You can use this feature in Premium version only. <a href="' . wpcsc_fs()->get_upgrade_url() . '">Upgrade now!!</a></div>';
        }
        
        public function get_extras( $key = '', $post = array() )
        {
            if ( $key == '' ) {
                return 'Nothing to show';
            }
            switch ( $key ) {
                case 'calculations':
                    $calculations = $this->get_calculations( $post['lead_id'] );
                    if ( !count( $calculations ) ) {
                        return '<div class="text-center fw-600">No related activities found</div>';
                    }
                    $html = '<table class="table table-hover">';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th>#</th>';
                    $html .= '<th title="Date">Date</th>';
                    $html .= '<th title="Child(ren)">Child(ren)</th>';
                    $html .= '<th title="Income">Income</th>';
                    $html .= '<th title="Spouse Income">S.Income</th>';
                    $html .= '<th title="Over Nights">ONs</th>';
                    $html .= '<th title="Compensation">CMP</th>';
                    $html .= '<th title="Liability">Liability</th>';
                    $html .= '<th title="Spouse Liability">S.Liability</th>';
                    $html .= '<th title="Worksheet">Worksheet</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';
                    $counter = 1;
                    foreach ( $calculations as $cal ) {
                        $per_or_day = ( !empty($cal['worksheet']) ? ' Days' : '%' );
                        $html .= '<tr>';
                        $html .= '<td class="text-center align-middle">' . $counter . '</td>';
                        $html .= '<td class="text-center align-middle" title="Date: ' . $cal['datetime'] . '">' . wpcsc_get_formatted_date( $cal['datetime'], 'jS M, Y H:i A' ) . '</td>';
                        $html .= '<td class="text-center align-middle">' . $cal['children'] . '</td>';
                        $html .= '<td class="text-center align-middle">' . $cal['income'] . '</td>';
                        $html .= '<td class="text-center align-middle">' . $cal['spouse_income'] . '</td>';
                        $html .= '<td class="text-center align-middle">' . $cal['over_nights'] . $per_or_day . '</td>';
                        $html .= '<td class="text-center align-middle">' . $cal['compensation'] . '</td>';
                        $html .= '<td class="text-center align-middle">' . $cal['liability'] . '</td>';
                        $html .= '<td class="text-center align-middle">' . $cal['spouse_liability'] . '</td>';
                        $html .= '<td class="text-center align-middle">' . $cal['worksheet'] . '</td>';
                        $html .= '</tr>';
                        $counter++;
                    }
                    $html .= '</tbody>';
                    $html .= '</table>';
                    break;
                default:
                    $html = parent::get_extras( $key, $post );
                    break;
            }
            return $html;
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
                case 'send_mail':
                    break;
                case 'clean':
                    
                    if ( !empty($post['clean']) and $post['clean'] == 'leads' ) {
                        $is_cleaned = $this->remove_all();
                        $response['success'] = $is_cleaned !== false;
                        $response['reason'] = ( $is_cleaned === 0 ? "No {$post['clean']} to be deleted" : "All {$post['clean']} deleted" );
                    } else {
                        $response = parent::ajax_handler( $post );
                    }
                    
                    break;
                case 'update_leads':
                    $leads = $this->get_with( 'enc', 1 );
                    
                    if ( count( $leads ) ) {
                        foreach ( $leads as $lead ) {
                            $data = [
                                'email'        => $this->dec_email__premium_only( $lead['email'] ),
                                'spouse_email' => ( !empty($lead['spouse_email']) ? $this->dec_email__premium_only( $lead['spouse_email'] ) : '' ),
                                'enc'          => '0',
                            ];
                            $this->update_row( $lead['id'], $data );
                        }
                        $response['success'] = true;
                        $response['reason'] = 'Leads data transformed successfully, you can now check the previous leads data now.';
                    } else {
                        $response['reason'] = 'Nothing to transform';
                    }
                    
                    break;
                default:
                    $response = parent::ajax_handler( $post );
                    break;
            }
            return $response;
        }
        
        public function remove( $id )
        {
            $is_deleted = parent::remove( $id );
            
            if ( $is_deleted ) {
                $settings = get_option( 'wpcsc__pdf' );
                $file_prefix = $settings['file_name_prefix'];
                $file_name = $file_prefix . '-' . $id . '.pdf';
                $wp_uploads_dir = wp_get_upload_dir();
                $folder_name = 'wpcsc-estimations';
                $folder_path = $wp_uploads_dir['basedir'] . '/' . $folder_name . '/' . $file_name;
                // wpcsc_ajaxy_die($folder_path);
                unlink( $folder_path );
            }
            
            return $is_deleted;
        }
    
    }
}
trait LeadsDatatables
{
    public function get_columns_titles( $exclude_keys = array() )
    {
        return [
            'S.No'        => 'S.No',
            'name'        => __( "Name", WPCSC_TXT_DOMAIN ),
            'spouse_name' => __( "Spouse Name", WPCSC_TXT_DOMAIN ),
            'details'     => __( "Details", WPCSC_TXT_DOMAIN ),
            'location'    => __( "Location", WPCSC_TXT_DOMAIN ),
            'actions'     => __( "Actions", WPCSC_TXT_DOMAIN ),
        ];
    }
    
    /**
     * this is used for return extra required parameters for table initialization.
     * @start used to set start time to get data from.
     * @return string[]
     */
    public function get_attributes()
    {
        return [
            'start'      => current_time( "d" ) - 1,
            'view_key'   => $this->table_name,
            'action'     => 'universal',
            'table_name' => $this->table_name,
        ];
    }
    
    public function get_column_html( $log, $col )
    {
        $col_data = array();
        switch ( $col ) {
            case 'name':
                $col_data[] = "<span class='d-block fs-5'>{$log['name']}</span>";
                $col_data[] = "<span class='badge bg-secondary wpcsc-pr-only' title='Premium Only'>" . wpcsc_hide_email( $log['email'] ) . "</span>";
                $col_data[] = '<span class="d-block">' . wpcsc_get_formatted_date( $log['last_modified'] ) . '</span>';
                break;
            case 'spouse_name':
                $col_data[] = "<span class='d-block fs-5'>{$log['spouse_name']}</span>";
                if ( !empty($log['spouse_email']) ) {
                    $col_data[] = "<span class='badge bg-secondary wpcsc-pr-only' title='Premium Only'>" . wpcsc_hide_email( $log['spouse_email'] ) . "</span>";
                }
                break;
            case 'details':
                $col_data[] = "<span class='d-block'>Income: \$ {$log['income']}</span>";
                $col_data[] = "<span class='d-block'>Spouse Income: \$ {$log['spouse_income']}</span>";
                $col_data[] = "<span class='d-block'>Worksheet <span style='text-uppercase'>{$log['worksheet']}</span></span>";
                break;
            case 'location':
                $col_data[] = '<span class="badge bg-primary wpcsc-pr-only">Premium only</span>';
                break;
            case 'actions':
                $col_data[] = '<span class="badge bg-info modal_actions" data-op="get" data-get="calculations" data-modal_size="lg" data-lead_id="' . $log['id'] . '" data-handle="leads" data-title="Related Activities" title="View Calculations"><i class="dashicons dashicons-analytics"></i></span>';
                $col_data[] = '<span class="badge bg-primary modal_actions" data-op="edit" data-id="' . $log['id'] . '" data-handle="leads" data-title="Edit Lead"><i class="dashicons dashicons-edit"></i></span>';
                $col_data[] = '<span class="badge bg-danger btn-ajaxy" data-op="trash" data-id="' . $log['id'] . '" data-handle="leads"><i class="dashicons dashicons-trash"></i></span>';
                break;
            default:
                $col_data = parent::get_column_html( $log, $col );
        }
        return $col_data;
    }
    
    public function get_filters_html( $params = array() )
    {
        foreach ( $params as $key => $value ) {
            $params[sanitize_text_field( $key )] = sanitize_text_field( $value );
        }
        ob_start();
        ?>
        <div class="wpcsc-dt-containers col-md-12 mb-2">
            <span class="wpcsc-dt-filter-handle text-danger cursor mb-2"><i class="dashicons dashicons-filter"></i> <i>Search Filters</i></span>
            <div class="wpcsc-dt-filters bg-light p-2">

                <div class="row">
                    <div class="col-md-4">
                        <div id="devon-daterangepicker" class="all-custom-filter">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar text-primary"></i>&nbsp;
                            <span class="search_col_date"></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="name" class="form-control all-custom-filter" placeholder="Name" value="">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="email" class="form-control all-custom-filter" placeholder="Email" value="">
                    </div>
                    <div class="col-md-4">
                        <span class="btn btn-dark btn-sm btn-refresh-filter cursor mr-1"><i class="dashicons dashicons-update text-white"></i> Refresh</span>
                        <button class="btn btn-sm btn-warning btn-reset-filter">Reset</button>
                        <span class="btn btn-primary btn-sm btn-export-excel cursor ml-2">Excel<i class="dashicons dashicons-update ml-2 d-none"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 ajax-result-area"></div>
        <?php 
        $html = $this->include_modal_html();
        return ob_get_clean() . $html;
    }

}