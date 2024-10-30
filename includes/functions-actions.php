<?php

add_action( 'wp_ajax_wpcsc_export_result', 'wpcsc_export_result' );
add_action( 'wp_ajax_nopriv_wpcsc_export_result', 'wpcsc_export_result' );
function wpcsc_export_result()
{
    if ( empty($_POST['your_name']) ) {
        wpcsc_ajaxy_die( 'Please enter your name' );
    }
    if ( empty($_POST['your_email']) ) {
        wpcsc_ajaxy_die( 'Please enter Your email' );
    }
    if ( !is_email( $_POST['your_email'] ) ) {
        wpcsc_ajaxy_die( 'Email format is incorrect' );
    }
    $default = [];
    // if found existing lead from database
    $wpcsc_leads = new WPCSC_LEADS();
    $lead = $wpcsc_leads->get_with( 'email', $wpcsc_leads->enc_email( sanitize_email( $_POST['your_email'] ) ) );
    $args = [
        'name'          => sanitize_text_field( $_POST['your_name'] ),
        'email'         => sanitize_email( $_POST['your_email'] ),
        'your_name'     => sanitize_text_field( $_POST['your_name'] ),
        'your_email'    => sanitize_email( $_POST['your_email'] ),
        'spouse_name'   => sanitize_text_field( $_POST['spouse_name'] ),
        'spouse_email'  => sanitize_email( $_POST['spouse_email'] ),
        'children'      => sanitize_text_field( $_POST['children'] ),
        'income'        => sanitize_text_field( $_POST['your_income'] ),
        'spouse_income' => sanitize_text_field( $_POST['spouse_income'] ),
        'over_nights'   => sanitize_text_field( $_POST['over_nights'] ),
        'worksheet'     => sanitize_text_field( $_POST['worksheet'] ),
        'last_modified' => current_time( 'Y-m-d H:i:s' ),
    ];
    $args = array_merge( $args, $default );
    $location = wpcsc_get_location();
    $args['pincode'] = $location['pincode'];
    $args['city'] = $location['city'];
    $args['state'] = $location['state'];
    $args['country'] = $location['country'];
    
    if ( count( $lead ) ) {
        $args = wp_parse_args( $args, $lead[0] );
        $wpcsc_leads->update( $lead[0]['id'], $args );
        $lead_id = $lead[0]['id'];
    } else {
        $lead_id = $wpcsc_leads->create( $args );
    }
    
    if ( is_array( $lead_id ) ) {
        wpcsc_ajaxy_die( $lead_id );
    }
    // store calculation data in calculation table with lead id
    $calc_args = [
        'lead_id'          => $lead_id,
        'compensation'     => sanitize_text_field( $_POST['compensation'] ),
        'liability'        => sanitize_text_field( $_POST['liability'] ),
        'spouse_liability' => sanitize_text_field( $_POST['spouse_liability'] ),
    ];
    $calc_args = array_merge( $args, $calc_args );
    $calc = $wpcsc_leads->add_calculation( $calc_args );
    // generate PDF for calculation results
    $export_args = $calc_args;
    $export_args['pay_or_receive'] = sanitize_text_field( $_POST['pay_or_receive'] );
    
    if ( !empty($_POST['worksheet']) ) {
        $pdf_args = [];
        foreach ( $_POST as $key => $value ) {
            $pdf_args[$key] = $value;
        }
        $export_args = array_merge( $export_args, $pdf_args );
    }
    
    $export_pdf = wpcsc_create_store_pdf_file( $export_args );
    $export_pdf['html'] = '<div style="display:flex;background:#c1c1c1"><div style="max-width:650px;width:100%;margin:30px auto;padding:25px;background:aliceblue">' . $export_pdf['html'] . '</div></div>';
    $headers = wpcsc_get_mail_headers();
    $user_email_encrypted = wpcsc_hide_email( $args['email'] );
    $spouse_email_encrypted = '';
    if ( !empty($args['spouse_email']) ) {
        $spouse_email_encrypted = wpcsc_hide_email( $args['spouse_email'] );
    }
    $user_email_settings = get_option( 'wpcsc__email', [] );
    
    if ( is_array( $user_email_settings ) && $user_email_settings['enable'] == 'on' ) {
        $user_mail_body = WPCSC_SETTINGS::get_template( $export_args, WPCSC_SETTINGS::template() );
        $user_mail_body = str_replace( $args['email'], $user_email_encrypted, $user_mail_body );
        if ( !empty($args['spouse_email']) ) {
            $user_mail_body = str_replace( $args['spouse_email'], $spouse_email_encrypted, $user_mail_body );
        }
        $subject = ( empty($user_email_settings['subject']) ? 'Child Support Estimation' : $user_email_settings['subject'] );
        $mail = wp_mail(
            $args['email'],
            $subject,
            $user_mail_body,
            $headers,
            [ $export_pdf['file_path'] ]
        );
    }
    
    $admin_email_settings = get_option( 'wpcsc__admin_email', [] );
    
    if ( is_array( $admin_email_settings ) && $admin_email_settings['enable'] == 'on' && !empty($admin_email_settings['receiver']) ) {
        $admin_email_body = WPCSC_SETTINGS::get_template( $export_args, WPCSC_SETTINGS::admin_template() );
        $admin_email_body = str_replace( $args['email'], $user_email_encrypted, $admin_email_body );
        if ( !empty($args['spouse_email']) ) {
            $admin_email_body = str_replace( $args['spouse_email'], $spouse_email_encrypted, $admin_email_body );
        }
        $subject = ( empty($user_email_settings['subject']) ? 'Child Support Estimation' : $user_email_settings['subject'] );
        $mail = wp_mail(
            $admin_email_settings['receiver'],
            $subject,
            $admin_email_body,
            $headers
        );
    }
    
    
    if ( $calc ) {
        $ajaxy = [
            'reason' => 'PDF report will automatically download and in case missed, you will also shortly receive an email with estimation.<a href="' . $export_pdf['exp_url'] . '" download="' . $export_pdf['file_name'] . '" style="color:#fff"><span class="wpcsc-export-pdf d-none">link</span></a>',
            'export' => $export_pdf,
        ];
        wpcsc_ajaxy_die( $ajaxy, true );
    }
    
    wpcsc_ajaxy_die( [
        'reason' => 'Something went wrong, please try again.',
        'export' => $export_pdf,
    ] );
}

add_action( 'wp_ajax_wpcsc_export_result_only_pdf', 'wpcsc_export_result_only_pdf' );
add_action( 'wp_ajax_nopriv_wpcsc_export_result_only_pdf', 'wpcsc_export_result_only_pdf' );
function wpcsc_export_result_only_pdf()
{
    $pdf_args = [
        'lead_id'        => current_time( 'ymd-his' ),
        'pay_or_receive' => sanitize_text_field( $_POST['pay_or_receive'] ),
    ];
    if ( !empty($_POST['worksheet']) ) {
        foreach ( $_POST as $key => $value ) {
            $pdf_args[$key] = sanitize_text_field( $value );
        }
    }
    $export_pdf = wpcsc_create_store_pdf_file( $pdf_args );
    $export_pdf['html'] = '<div style="display:flex;background:#c1c1c1"><div style="max-width:650px;width:100%;margin:30px auto;padding:25px;background:aliceblue">' . $export_pdf['html'] . '</div></div>';
    $ajaxy = [
        'reason' => 'PDF report will download soon. <a href="' . $export_pdf['exp_url'] . '" download="' . $export_pdf['file_name'] . '" style="color:#fff"><span class="wpcsc-export-pdf d-none">link</span></a>',
        'export' => $export_pdf,
    ];
    wpcsc_ajaxy_die( $ajaxy, true );
    wpcsc_ajaxy_die( [
        'reason' => 'Something went wrong, please try again.',
        'export' => $export_pdf,
    ] );
}

function wpcsc_get_base_for_pdf( $args )
{
    $template = WPCSC_SETTINGS::template_for_pdf();
    return WPCSC_SETTINGS::get_template( $args, $template );
}

add_action( 'wp_ajax_action_update_settings', 'action_update_settings' );
function action_update_settings()
{
    $handle = sanitize_text_field( $_POST['handle'] );
    $task = sanitize_text_field( $_POST['task'] );
    if ( !wp_verify_nonce( $_POST['wp_nonce'] ) ) {
        wpcsc_ajaxy_die( 'Request timed out, reload page and try again.' );
    }
    unset(
        $_POST['handle'],
        $_POST['action'],
        $_POST['task'],
        $_POST['wp_nonce']
    );
    $class_obj = wpcsc_get_class_object( $handle );
    $post = wpcsc_sanitize_arr_str( $_POST );
    $response = $class_obj->update_options( $task, $post );
    wpcsc_ajaxy_die( $response, $response['success'] );
}

add_action( 'wp_ajax_action_ajax_handler', 'action_ajax_handler' );
function action_ajax_handler()
{
    $response = [
        'reason' => 'Nothing is done, please try again',
    ];
    $handle = sanitize_text_field( $_POST['handle'] );
    $task = sanitize_text_field( $_POST['op'] );
    $class_obj = wpcsc_get_class_object( $handle );
    $success = true;
    $redirect = false;
    $message = 'skipped conditions';
    unset(
        $_POST['task'],
        $_POST['handle'],
        $_POST['op'],
        $_POST['action']
    );
    switch ( $task ) {
        case 'reset_default':
            $response = $class_obj->reset_default( sanitize_text_field( $_POST['option'] ) );
            $message = $response;
            $redirect = '';
            break;
        case 'add':
            $post = wpcsc_sanitize_arr_str( $_POST );
            $response = $class_obj->create( $post );
            $success = ( is_numeric( $response ) ? true : $response['success'] );
            $message = ( is_array( $response ) ? $response['reason'] : wpcsc_create_name_from_label( $handle ) . ' added successfully' );
            break;
        case 'edit':
            $post = wpcsc_sanitize_arr_str( $_POST );
            $response = $class_obj->update( $_POST['id'], $post );
            $success = ( is_array( $response ) ? $response['success'] : true );
            $message = ( is_array( $response ) ? $response['reason'] : wpcsc_create_name_from_label( $handle ) . ' updated successfully' );
            break;
        case 'trash':
            $response = $class_obj->remove( $_POST['id'] );
            $success = $response;
            $message = wpcsc_create_name_from_label( $handle ) . ' row deleted successfully';
            break;
        case 'ajax_action':
            $post = wpcsc_sanitize_arr_str( $_POST );
            $response = $class_obj->ajax_handler( $post );
            $success = ( is_array( $response ) ? $response['success'] : true );
            $message = ( is_array( $response ) ? $response['reason'] : wpcsc_create_name_from_label( $handle ) . ' deleted successfully' );
            break;
        default:
    }
    if ( !$response ) {
        $message = 'Nothing is done, please try again.';
    }
    $ajaxy = [
        'reason' => $message,
        'notify' => $message,
    ];
    if ( $redirect !== false ) {
        $ajaxy['redirect'] = $redirect;
    }
    wpcsc_ajaxy_die( $ajaxy, $success );
    // wpcsc_ajaxy_die('This feature is only available in Premium Plugin <a href="'.wpcsc_fs()->get_upgrade_url().'">'. __('Upgrade Now!', WPCSC_TXT_DOMAIN).'</a>', true);
}

/**
 * Universal action to html format for all classes for any form
 */
add_action( 'wp_ajax_action_get_universal_html', 'action_get_universal_html' );
function action_get_universal_html()
{
    $request = wpcsc_sanitize_arr_str( $_POST['data'] );
    $class_object = wpcsc_get_class_object( $request['handle'] );
    
    if ( in_array( $request['op'], [ 'add', 'edit' ] ) ) {
        $id = ( !empty($request['id']) ? $request['id'] : '' );
        $output = $class_object->form_html( $request['op'], $request['handle'], $id );
    } elseif ( $request['op'] == 'get' ) {
        $output = $class_object->get_extras( $request['get'], $request );
    }
    
    wpcsc_ajaxy_die( $output, true );
}

add_action( 'wp_ajax_action_export_data_to_excel', 'action_export_data_to_excel' );
function action_export_data_to_excel()
{
    $ajaxy = array();
    $success = false;
    $fields = wpcsc_sanitize_arr_str( $_POST['fields'] );
    $table = ( !empty($fields['table']) ? $fields['table'] : '' );
    $class_object = wpcsc_get_class_object( $table );
    $ajaxy['reason'] = '<div class="alert bg-danger text-center text-white fw-600">Premium only feature, <a href="' . wpcsc_fs()->get_upgrade_url() . '">upgrade plan</a> to get this EXPORT to EXCEL feature.</div>';
    if ( wpcsc_fs()->is_premium() ) {
        
        if ( wpcsc_fs()->can_use_premium_code() ) {
            $exported = $class_object->export_spout( [
                'fields' => $fields,
            ] );
            $result = '<div class="alert bg-success text-white text-center border py-2">';
            $result .= $exported['total_posts'] . ' ' . strtoupper( wpcsc_create_name_from_label( $exported['table'] ) ) . ' data exported to excel successfully.';
            $result .= '<br><a class="btn btn-sm btn-primary mx-1" href="' . site_url( $exported['excel']['file_name'] ) . '" target="_blank">Download Excel</a>';
            $result .= '</div>';
            $ajaxy['exported'] = $exported;
            $ajaxy['reason'] = $result;
            $success = true;
        }
    
    }
    wpcsc_ajaxy_die( $ajaxy, $success );
}

add_action( 'wp_ajax_action_set_state', 'action_set_state' );
function action_set_state()
{
    $_POST = $_POST['data'] ?? $_POST;
    if ( empty($_POST['state']) ) {
        wpcsc_ajaxy_die( 'Please select state.' );
    }
    $update = update_option( 'wpcsc__state', sanitize_text_field( $_POST['state'] ) );
    
    if ( $update ) {
        $class_obj = ( new WPCSC_CALC() )->get_settings_class_object( $_POST['state'] );
        $class_obj->store_table();
        $redirect = add_query_arg( [
            'page' => 'wpcsc',
        ], admin_url( 'admin.php' ) );
        $message = 'State is set now, please wait for the page to refresh.';
        $ajax = [
            'reason'   => $message,
            'notify'   => $message,
            'redirect' => $redirect,
        ];
        wpcsc_ajaxy_die( $ajax, true );
    }
    
    wpcsc_ajaxy_die( 'State is already set to: ' . $_POST['state'] );
}

add_action( 'wp_ajax_nopriv_wpcsc_action_cal_child_support', 'wpcsc_action_cal_child_support' );
add_action( 'wp_ajax_wpcsc_action_cal_child_support', 'wpcsc_action_cal_child_support' );
function wpcsc_action_cal_child_support()
{
    $post = $_POST;
    if ( !wp_verify_nonce( $post['wp_nonce'] ) ) {
        wpcsc_ajaxy_die( 'Request timed out, reload page and try again.' );
    }
    // Hint: Unset wp_nonce as it is conflicting with validation (is_numeric() part)
    unset( $post['wp_nonce'] );
    if ( empty($post['handle']) || empty($post['task']) ) {
        wpcsc_ajaxy_die( 'Some data missing, please refresh page and try again.' );
    }
    unset( $post['action'] );
    $handle = strtoupper( $post['handle'] );
    $class_obj = wpcsc_get_class_object( $handle );
    $response = $class_obj->calculate( $post );
    if ( isset( $post['debug'] ) ) {
        wpcsc_ajaxy_die( $response );
    }
    
    if ( $response['success'] ) {
        wpcsc_ajaxy_die( $response, true );
    } else {
        wpcsc_ajaxy_die( $response['reason'] );
    }
    
    wpcsc_ajaxy_die( $post );
}
