<?php

add_action('admin_enqueue_scripts', 'ajax_js_enqueue');
function ajax_js_enqueue()
{

    if (empty($_GET['page']) || strpos($_GET['page'], 'wpcsc') === false) {
        return;
    }
    wp_enqueue_style('wpcsc-bootstrap', wpcsc_plugin_url("/assets/css/bootstrap.min.css"), [], WPCSC_JS_VERSION);
    wp_enqueue_style('wpcsc-daterangepicker', wpcsc_plugin_url("/assets/libs/bootstrap-daterangepicker/css/daterangepicker.css"), [], WPCSC_JS_VERSION);
    wp_enqueue_style('wpcsc-datatable', wpcsc_plugin_url("/assets/css/jquery.dataTables.min.css"), [], WPCSC_JS_VERSION);
    wp_enqueue_style('wpcsc-dt-bootstrap5', wpcsc_plugin_url("/assets/css/dataTables.bootstrap5.min.css"), [], WPCSC_JS_VERSION);

    wp_enqueue_script('wpcsc-popper', wpcsc_plugin_url("/assets/js/popper.min.js"), [], WPCSC_JS_VERSION, true);
    wp_enqueue_script('wpcsc-bootstrap', wpcsc_plugin_url("/assets/js/bootstrap.min.js"), [], WPCSC_JS_VERSION, true);
    wp_enqueue_script('wpcsc-datatable', wpcsc_plugin_url("/assets/js/jquery.dataTables.min.js"), ['jquery', ], WPCSC_JS_VERSION, true);
    wp_enqueue_script('wpcsc-dt-bootstrap', wpcsc_plugin_url("/assets/js/dataTables.bootstrap5.min.js"), [], WPCSC_JS_VERSION, true);
    wp_enqueue_script('wpcsc-sweetalert', wpcsc_plugin_url("/assets/js/sweetalert.min.js"), [], WPCSC_JS_VERSION, true);
    wp_enqueue_script('wpcsc-notify', wpcsc_plugin_url("/assets/js/bootstrap-notify.min.js"), [], WPCSC_JS_VERSION, true);

    wp_enqueue_script('wpcsc-daterangepicker', wpcsc_plugin_url("/assets/libs/bootstrap-daterangepicker/js/daterangepicker.js"), ['moment'], WPCSC_JS_VERSION, true);
    wp_enqueue_script('wpcsc-ajaxy', wpcsc_plugin_url("/assets/js/ajaxactions.js"), [], WPCSC_JS_VERSION, true);

    $localize = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'is_premium' => json_encode(wpcsc_fs()->can_use_premium_code()),
        'is_wpcscpro' => json_encode(wpcsc_fs()->is_plan('wpcscpro', $exact = false )),
    );
    wp_localize_script('wpcsc-ajaxy', 'WPCSC_AJAX', $localize);

    // Enqueue Scripts for my-admin section
    wp_register_script('datatables', wpcsc_plugin_url("/assets/js/dt-datatables.js"), [], WPCSC_JS_VERSION);
    wp_enqueue_script('datatables');
}

/**
 * Common function to add all types of datatables
 */
function wpcsc_page_ajax_datatable($args = [])
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    $output = "";
    $wpcsc_obj = wpcsc_get_class_object($args['attributes']['view_key']);
    // ['slug'=> $slug, 'view_key' => $view_key, 'columns' => $columns, 'extra' => $extra, 'filter' => $filter, 'action' => $action, 'attributes'=> $attributes]

    $output .= '<div class="row p-3 rel-dt-html rel-dt-html-' . $args['attributes']['action'] . '">';
        $output .= $wpcsc_obj->get_filters_html($_REQUEST);
        $data_attr = wpcsc_create_data_attributes_from_array($args['attributes']);

        $output .= '<div class="col-md-12">';

            $output .= '<table id="dt-ajax-data-tables" class="rel-dt-common table table-striped table-bordered table-dark git dataTable responsive dtr-inline" style="width: 100%" ' . $data_attr . '>';
                $output .= '<thead><tr>';

                foreach($args['columns'] as $key=>$col) {

                    if(strtoupper($key)=='S.NO') {
                        $output .= '<th><input type="checkbox" name="select_all" value="1" class="dt-master-select"></th>';
                    }
                    else if(! is_array($col)) {
                        $name = strtoupper(str_replace('_', ' ', $col));
                        $output .= '<th>' . strtoupper(str_replace('_', ' ', $name)) . '</th>';
                    }
                    else {
                        $style = '';
                        $name = $col['col'] ?? 'Unknown';
                        $class = $col['class'] ?? '';

                        if(!empty($col['width'])) {
                            $style = 'width:' . $col['width'] . '%';
                        }
                        $output .= '<th class="' . $class . '" style="'.$style.'">' . strtoupper(str_replace('_', ' ', $name)) . '</th>';
                    }
                }

                $output .= '</tr></thead>';
            $output .= '</table>';
        $output .= '</div>';
    $output .= '</div>';

    // TODO: causing some issue to print html below the table directly visible in page view
    // $output .= $args['extra'];

    _e($output);
}

// ODB Options Table
add_action('wp_ajax_wpcsc_dt_universal_callback', 'wpcsc_dt_universal_callback');
function wpcsc_dt_universal_callback()
{

    header("Content-Type: application/json");
    $request = wpcsc_sanitize_arr_str($_GET);

    $search_fields = array();

    // Set up search args for wpcsc_get_logs
    $search_args = array(
        'number' => $request['length'],
        'offset' => $request['start'],
        'order' => $request['order'] ?? 'DESC',
        'orderby' => ($request['data']['date_key'] ?? 'id'),
        'date_key' => ($request['data']['date_key'] ?? 'datetime'),
        'search_keys' => $search_fields,
    );
    // unset date_key to prevent
    unset($request['data']['date_key']);

    $search_args['meta_query'] = [];

    // Datatable default search - Default search disabled in datatable
    if (isset($request['data'])) {
        foreach ($request['data'] as $key => $value) {
            $compare = in_array($key, ['user_id']) ? '=' : 'LIKE';

            if (!empty($value)) {
                if (strpos($key, 'meta_') === 0) {
                    $search_args['meta_query'][] = [
                        'key' => str_replace('meta_', '', $key),
                        'value' => $value,
                        'compare' => $compare
                    ];
                } else {
                    $search_args['search_keys'][$key] = array(
                        'value' => sanitize_text_field($value),
                        'compare' => $compare,
                    );
                }
            }
        }
    }

    $class_object = wpcsc_get_class_object($request['view_key'], false, $request['table_name']);

    if (!is_object($class_object)) {
        wp_die('You dont have this object available.'.$request['view_key']. ' '. $request['table_name']);
    }

    $search_args = $class_object->prepare_datatable_args($search_args);
    $logs = $class_object->get($search_args);

    $count = $request['start'];
    $columns = array_keys($class_object->get_columns_titles());

    $data = [];
    $i = $search_args['offset'];
    foreach ($logs as $log) {
        $i++;

        $nestedData = array();
        $nestedData['DT_RowId'] = 'row_' . $log['id'];
        $nestedData['DT_RowClass'] = $request['view_key'] . '-row';
        $nestedData['DT_RowAttr'] = array(
            'data-id' => $log['id']
        );

        foreach ($columns as $col) {
            $first_columns = array();
            $col_data = array();
            switch ($col) {
                case 'Sr. No.':
                case 'S.No':
                case 'No':
                    $col_data[] = $log['id'];
                    break;
                default:
                    $col_data = $class_object->get_column_html($log, $col);
                    break;
            }

            $col_data = apply_filters('wpcsc_universal_col_data_filter', $col_data, $request['view_key'], $col, $log);

            if (is_array($col_data) and isset($col_data['col_data'])) {
                $nestedData['DT_RowClass'] = ($col_data['row_class']) ?? '';
                $nestedData['DT_RowAttr'] = ($col_data['row_attr']) ?? '';
                $nestedData[] = implode("\n", $col_data['col_data']);
            } else {
                $nestedData[] = implode("\n", $col_data);
            }
        }

        $data[] = $nestedData;
    }

    // ToDo: Used for getting total records seems like double-execution
    $search_args['number'] = -1;
    $search_args = $class_object->prepare_datatable_args($search_args);
    $logs = $class_object->get($search_args);
    $totalData = count($logs);

    $json_data = array(
        "draw" => intval($request['draw']),
        "recordsTotal" => $totalData,
        "recordsFiltered" => $totalData,
        "data" => $data,
    );

    _e(json_encode($json_data));
    wp_die();
}