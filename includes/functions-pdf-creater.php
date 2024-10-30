<?php

function wpcsc_create_store_pdf_file($args)
{
    $settings = get_option('wpcsc__pdf', []);

    $defaults = array(
        'Abbv' => 'WPCSC',
        'Title' => 'Child Support Calculator Calculator',
        'Gmail' => 'support@wpchildsupport.com',
        'generate' => $settings['file_name_prefix'],
        'name' => ($args['name'] ?? ""),
        'email' => ($args['email'] ?? ""),
        'spouse_name' => ($args['spouse_name'] ?? ""),
        'spouse_email' => ($args['spouse_email'] ?? ""),
        'income' => ($args['income'] ?? $args['your_income']),
        'spouse_income' => $args['spouse_income'],
        'compensation' => $args['compensation'],
        'liability' => $args['liability'],
        'spouse_liability' => $args['spouse_liability'],
        'children' => $args['children'],
        'over_nights' => $args['over_nights'],
        'worksheet' => $args['worksheet'],
    );

	$args = wp_parse_args($args, $defaults);
    extract($args);

    $generate_file = $args['generate'];
    $folder_name = 'wpcsc-estimations';
    $generate_file_capitalize = '<span style="text-transform:capitalize;">' . wpcsc_create_name_from_label($generate_file) . '</span>';

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    $wp_uploads_dir = wp_get_upload_dir();

    $folder_path = $wp_uploads_dir['basedir'] . '/' . $folder_name . '/';
    $folder_url = $wp_uploads_dir['baseurl'] . '/' . $folder_name . '/';

    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0755, true);
    }
    $wpcsc_calc = new WPCSC_CALC();
    $state_obj = $wpcsc_calc->get_settings_class_object($args['task']);

    if(!empty($args['worksheet'])){

        $worksheet_html = $state_obj->get_worksheet_html($args['worksheet'], $args);
        $data = $worksheet_html;
    }else {
        $base = wpcsc_get_base_for_pdf($args);
        $data = $base;
    }

    $results = include_once('generate_pdf.php');

    $file_name = $generate_file . '-' . $args['lead_id'] . '.pdf';
    $store_at = $folder_path . $file_name;
    $exp_url = $folder_url . $file_name;

    if (file_put_contents($store_at, $output)) {
        $html_return = $generate_file_capitalize . ' is ready. <a class="btn btn-info" href="' . $exp_url . '" target="_blank">View Now</a>';
    } else {
        $ajaxy['reason'] = '<br>' . $generate_file_capitalize . '  was not created, please re-try. <a class="btn btn-info" href="' . $exp_url . '" target="_blank">Check if already created</a>';
        wpcsc_ajaxy_die($ajaxy);
    }

    $new_variables = array('generate_file', 'generate_file_capitalize', 'store_at', 'exp_url');
    $new_array = compact($new_variables);
    $args = array_merge($args, $new_array);

    return ['html' => $data, 'html_return' => $html_return, 'exp_url' => $exp_url, 'file_path' => $store_at, 'file_name' => $file_name];
}