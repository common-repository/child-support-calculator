<?php

// Check If Local Host
if( ! function_exists( 'wpcsc_if_local' ) ) {
    function wpcsc_if_local() {
        $site_type = get_bloginfo( 'url' );
        $url_parts = explode('/', $site_type);
        if(sizeof($url_parts) < 2) return false;
        return ($url_parts[2] == 'localhost' );
    }
}

/**
 * @param $name
 * @param $value
 * @param string $time
 */
function wpcsc_cookie_creator($name, $value, $time='+ 30 days') {
	
	if(is_array($value)) $value = json_encode($value);

	$expiry_time = DateTime::createFromFormat('Y-m-d H:i:s', current_time('Y-m-d H:i:s'));
	$expiry_time->modify($time);
	
	setcookie($name, $value, $expiry_time->format('U'), '/');
	if(!isset($_COOKIE[$name])) {
		setcookie($name, $value, $expiry_time->format('U'));
	}
}

function wpcsc_get_cookie($name) {
    $value = false;
    if(!empty($_COOKIE[$name])) {
        $value = wpcsc_json_validate($_COOKIE[$name]);
    }

    return $value;
}

function wpcsc_json_validate($string) {
    // decode the JSON data
    $string = stripslashes($string);
    $result = json_decode($string, true);
    // switch and check possible JSON errors
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            $error = ''; // JSON is valid // No error has occurred
            break;
        case JSON_ERROR_DEPTH:
            $error = 'The maximum stack depth has been exceeded.';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            $error = 'Invalid or malformed JSON.';
            break;
        case JSON_ERROR_CTRL_CHAR:
            $error = 'Control character error, possibly incorrectly encoded.';
            break;
        case JSON_ERROR_SYNTAX:
            $error = 'Syntax error, malformed JSON.';
            break;
        // PHP >= 5.3.3
        case JSON_ERROR_UTF8:
            $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
            break;
        // PHP >= 5.5.0
        case JSON_ERROR_RECURSION:
            $error = 'One or more recursive references in the value to be encoded.';
            break;
        // PHP >= 5.5.0
        case JSON_ERROR_INF_OR_NAN:
            $error = 'One or more NAN or INF values in the value to be encoded.';
            break;
        case JSON_ERROR_UNSUPPORTED_TYPE:
            $error = 'A value of a type that cannot be encoded was given.';
            break;
        default:
            $error = 'Unknown JSON error occurred.';
            break;
    }

    if ($error !== '') {
        // throw the Exception or exit // or whatever :)
        return $error;
        return $string;
    }

    // everything is OK
    return $result;
}

function wpcsc_get_date_interval($first_date, $second_date) {
	$first_date = new DateTime($first_date);
	$second_date = new DateTime($second_date);
	return $first_date->diff($second_date);	
}

/**
 * Format an interval to show all existing components.
 * If the interval doesn't have a time component (years, months, etc)
 * That component won't be displayed.
 *
 * @param DateInterval $interval The interval
 *
 * @return string Formatted interval string.
 */
function wpcsc_format_interval(DateInterval $interval) {
    $result = "";
    if ($interval->y) { $result .= $interval->format("%y years "); }
    if ($interval->m) { $result .= $interval->format("%m months "); }
    if ($interval->d) { $result .= $interval->format("%d days "); }
    if ($interval->h) { $result .= $interval->format("%h hours "); }
    if ($interval->i) { $result .= $interval->format("%i minutes "); }
    if ($interval->s) { $result .= $interval->format("%s seconds "); }

    return $result;
}

function wpcsc_render_date_text($datetime, $format='d M, y - H:i a') {
	$formatted_date = wpcsc_get_formatted_date($datetime, $format); 
	return '<span class="rel-date-display" title="'.$datetime.'">'.$formatted_date.'</span>'; 
}

function wpcsc_modify_date($op, $val, $dimension='hours', $time='') {    	
	if(empty($time)) $time = current_time('U');	
	$date_object = DateTime::createFromFormat('U', $time);	
	$date_object->modify($op . $val . ' ' . $dimension);
	//$date_object = $date_object->format('Y-m-d H:i:s'); 
	return $date_object; 
}

function wpcsc_create_date($datetime, $format='Y-m-d H:i:s', $output='') {
	
	$date_object = DateTime::createFromFormat($format, $datetime);
	if(!is_a($date_object, 'DateTime')) { 
		return $datetime; 
	}
	
	return $date_object; 
}

/**
 * @param $datetime
 * @param string $output
 * @param string $format
 * @return string
 */
function wpcsc_get_formatted_date($datetime, $output='H:ia d M, Y', $format='Y-m-d H:i:s') {		
	$date_object = DateTime::createFromFormat($format, $datetime);		
	if(!is_a($date_object, 'DateTime')) { 	
		return $datetime; 
	}
	
	return $date_object->format($output); 	
}

/**
 * @param $dollars
 * @param $currency - if false the symbol will not be appended
 * @return string|void
 */
function wpcsc_get_formatted_currency($dollars, $currency="Rs.") {
    if(is_int($dollars)) {
        $formatted = $dollars;
    }
    else {
        $formatted = number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $dollars)), 2);
        $formatted = $dollars < 0 ? "-{$formatted}" : "{$formatted}";
    }

    return $currency ? $currency . ' ' . $formatted : $formatted;
}

function wpcsc_create_name_from_label($string) {	
	return ucwords(str_replace('_', ' ', str_replace('-', ' ', $string))); 
}

function wpcsc_create_label_from_name($string, $sep="-", $to_replace=[': ', ' ', ':']) {
	return strtolower(str_replace($to_replace, $sep, trim($string)));
}

function wpcsc_create_data_attributes_from_array($arr) {
    $data_attr = [];
    foreach($arr as $k=>$v) {
        $data_attr[] = "data-{$k}='{$v}'";
    }
    
    return implode(' ', $data_attr);
}

/**
 * wpcsc_create_options_from_array
 * @param array() - $values 
 * @param string - $value
 
 * @return null
 * Creates list of options for select field
 */
function wpcsc_create_options_from_array($values='', $value='', $multi_array=true) {
	
	if( !is_array($values) ) return; 
	if(is_array($value)) $value = implode(',', $value); 
	
	$option_list = array();
	
	foreach($values as $key => $name) {
		
		// Hint: In User ID case it is passed as integer
		// so type casting required for strpos
		$key = strval($key); 
		
		if(!empty($value) && !empty($key) && strpos($value, $key) !== false)
			$option_list[] = '<option value="'.$key.'" selected>'.$name.'</option>';
		else
			$option_list[] = '<option value="'.$key.'">'.$name.'</option>';
	}
	
	return implode('', $option_list);
}

/**
 * wpcsc_create_checkboxes_from_array
 * @param array() - $values 
 * @param string - $value
 
 * @return null
 * Creates list of checkboxes 
 */
function wpcsc_create_checkboxes_from_array($option_name, $values='', $value='', $multi_array=true) {
	
	if( !is_array($values) ) return; 
	if(is_array($value)) $value = implode(',', $value); 
	
	$option_list = array();
	
	foreach($values as $key => $name) {
		
		// Hint: In User ID case it is passed as integer
		// so type casting required for strpos
		$key = strval($key); 
		$input = '';
		$id = $option_name . '-' . $key; 
		
		if(!empty($value) && !empty($key) && strpos($value, $key) !== false) {
			$input .= '
				<div class="form-check form-check-inline">
					<input class="form-check-input all-custom-filter '.$option_name.'" type="checkbox" id="'.$id.'" name="'.$option_name.'[]" value="'.$key.'" checked>
					<label class="form-check-label" for="'.$id.'">'.$name.'</label>
				</div>';
		}		
		else {
			$input .= '
				<div class="form-check form-check-inline">
					<input class="form-check-input all-custom-filter '.$option_name.'" type="checkbox" id="'.$id.'" name="'.$option_name.'[]" value="'.$key.'">
					<label class="form-check-label" for="'.$id.'">'.$name.'</label>
				</div>';
		}
		$option_list[] = $input; 
	}
	
	$option_list[] = '<span class="label label-primary cursor clear-checkboxes" data-class="'.$option_name.'"><i class="fa fa-times-circle"></i> Clear</span>'; 
	
	return implode('', $option_list);
}

function wpcsc_click_to_copy($args=array()) {
	
	$defaults = array(
		'text' => 'Text to copy', 
		'id' => 'ele-id', 
		'icon' => 'copy',
		'class'=> 'text-primary far fa-copy',
		'extra_class' => '', 
	); 
	
	$args = wp_parse_args($args, $defaults); 
	
	$inputId = $args['id'].'-copy-me'; 
	$iconId = $args['id'].'-copy-btn'; 
	
	$breaks = array("<br />","<br>","<br/>");  
    $args['text'] = str_ireplace($breaks, "\r\n", $args['text']);  
	
	$output = ''; 
	
	$output .= '<span>';
		// $output .= '<input id="'.$inputId.'" value="'.$args['text'].'" type="text" class="copy d-none">';
		$output .= '<textarea id="'.$inputId.'" type="text" class="copy d-none" style="width:720px;max-width:100%">'.$args['text'].'</textarea>';
		$output .= '<i title="Click To Copy" id="'.$iconId.'" class="cursor '.$args['class'].' '.$args['extra_class'].'" onclick="copyToClipboard(\'#'.$inputId.'\')"></i>';
	$output .= '</span>';
	
	return $output; 
}

function wpcsc_get_ajax_field($id, $field_name, $current_value='', $type="input") {
	
	$output = '';
	
	if($type=="datepicker") {
		$output .= '<span class="rel-date-picker">';
			$output .= '<i class="fa fa-calendar datepicker float-left mr-2 mt-2 cursor">';
				$output .= '<span class="ml-1 date-text">'.$current_value.'</span>';
			$output .= '</i>';
			$output .= '<input type="hidden" name="'.$field_name.'" class="wpcsc_ajax_field datehidden" value="'.$current_value.'" data-postid="'.$id.'" data-field="'.$field_name.'">';
		$output .= '</span>';
	}	
	
	return $output; 
}

function wpcsc_get_editable_field($id, $field_name, $table_name, $current_value='', $args=array()) {
	$output = '';
	
	$defaults = array(
		'type' => 'input',
		'bold' => false, 
	); 
	
	$args = wp_parse_args($args, $defaults); 	
	
	if($args['type']=="input") {
		$output .= wpcsc_create_name_from_label($field_name) . ': '; 
		$output .= '<span class="cursor rel-editable font-weight-bold" title="Info ISSN">';
			$output .= '<span class="rel-editable-value">'.$current_value.'</span>';
			$output .= '<input style="display:none;" class="rel-editable-input wpcsc_ajax_field input-sm" value="'.$current_value.'" data-postid="'.$id.'" data-field="'.$field_name.'" data-action="action_editable_field">';
		$output .= '</span>';
	}	
	
	return $output; 
}

function wpcsc_prepare_array_for_app($array, $key_name="key", $val_name="val") {
	$return_array = array(); 
	
	foreach($array as $key=>$val) {
		$return_array[] = array(
			$key_name => $key, 
			$val_name => $val
		); 
	}
	
	return $return_array; 
}

function wpcsc_get_globals_single($values_for, $key) {
	$values = wpcsc_get_globals($values_for); 
	
	if(isset($values[$key])) {
		return [$key => $values[$key]]; 
	}
	
	return false; 
}

function wpcsc_get_globals($values_for='', $blank_option_name='Choose') {

	$return = array('no-values' => 'Values for not matched'); 
	
	switch($values_for) {

		case 'yes_no':
			$return = array(
				'' => $blank_option_name,
				'yes' => 'Yes',
				'no' => 'No',
			);
			break;

        case 'sort_order':
            $return = array (
                '' => $blank_option_name,
                'asc' => 'Ascending',
                'dsc' => 'Descending',
            );
            break;
        case 'one_to_six':
            $return = array (
                '' => $blank_option_name,
                'one' => 'One',
                'two' => 'Two',
                'three' => 'Three',
                'four' => 'Four',
                'five' => 'Five',
                'six' => 'Six',
            );
            break;
        case 'states':
            $return = [
                '' => $blank_option_name,
                'global' => 'Global',
                'NC' => 'North Carolina',
            ];
            break;
	}

	return apply_filters('wpcsc_get_globals', $return, $values_for, $blank_option_name); 
}

function wpcsc_format_date($date, $format='Y-m-d H:i:s', $output='H:ia - d M, Y') {
	$return = $date; 
	$date_object = DateTime::createFromFormat($format, $date);		
	if(is_a($date_object, 'DATETIME')) {
		if($output=='short') {
			$return = $date_object->format('d M, Y') . '<br>' . $date_object->format('H:i a'); 
		}
		else $return = $date_object->format($output); 
	}
	
	return $return; 
}

function wpcsc_user_exists($value, $key='username') {	
	$search_fields = array(); 
	$table_name = 'wpcsc_users';	
	$search_fields[$key] = array('value' => sanitize_text_field($value),'compare' => '=');
	$logs = (array)wpcsc_get_logs(array('search_fields'=>$search_fields), $table_name); 	
	return sizeof($logs); 	
}

function wpcsc_validate_phone($user_phone, $country_code="") {
    $user_phone = ltrim($user_phone);
    $user_phone = ltrim($user_phone, '0');

    //$user_phone = ltrim($user_phone, $country_code); // TODO : commented bcoz it trim any character if matched

    return str_replace([" ", $country_code], "", $user_phone);
}

function wpcsc_ajaxy_die($array=array(), $type=false) {
	
	if($array Instanceof WP_REST_Response){
        $array = $array->get_data();
        $ajaxy = array('reason' => $array['message']);
    }
	elseif(is_array($array))
		$ajaxy = $array;
	else 
		$ajaxy = array('reason' => print_r($array, true)); 	
	
	if(!$type) wp_send_json_error($ajaxy); 
	else wp_send_json_success($ajaxy); 				
	wp_die();
}

/**
 * wpcsc_get_next_string_counter($string)
 * $param: string.
 * return: string
 * Generates next string in counter
 */
function wpcsc_get_next_string_counter($starting_label, $size=4){
	$tailing_number_digits =  0;
	$i = 0;
	$from_end = -1;
	while ( $i < strlen($starting_label) ) :
		if ( is_numeric( substr( $starting_label,$from_end - $i, 1 ) ) ) :
			$tailing_number_digits++;
		else:
			// End our while if we don't find a number anymore
			break;
		endif;
		$i++;
	endwhile;
	//return $tailing_number_digits;
	
	if ($tailing_number_digits > 0) :
		$base_portion = substr( $starting_label, 0, -$tailing_number_digits );
		$digits_portion = substr( $starting_label, -$tailing_number_digits );	
	else :
		$base_portion = $starting_label;
		$digits_portion = '';
	endif;
	$format = ($size == 4) ? "%04d" : "%0{$size}d";
	$formatted_number = sprintf($format, intval($digits_portion+1));
	
	$next_label = $base_portion . $formatted_number;
	
	return $next_label; 
}

function wpcsc_get_serialized_meta($post_id, $field) {
	$value = get_post_meta($post_id, $field, true); 
	return $value; 
	$value = maybe_unserialize($value); 
	$value = !is_array($value) ? array($value) : $value; 
	return $value; 
}

/**
 * wpcsc_update_meta  
 * @param - all as update meta
 * $operator - for the append operation
 * default - string concatenate operation
 */
function wpcsc_update_meta( $post_id='', $meta_key='', $meta_value='', $operator=',', $type='posts', $overwrite = false ) {
	
	if( empty($post_id) OR empty($meta_key) OR empty($meta_value) )
		return; 
	
	$numeric_operators = ['+', '-', '*', '/']; 
	if(in_array($operator, $numeric_operators) && !is_numeric($meta_value)) {
		wpcsc_error_and_reporting(compact('post_id', 'meta_key', 'meta_value', 'operator', 'overwrite'));
		return;
	}
	
	// get already key value if already existing
    if($type == 'users'){
        $meta_existing = get_user_meta($post_id, $meta_key, true);
    }else {
        $meta_existing = get_post_meta($post_id, $meta_key, true);
    }
	
	// append, add if needed
	if( isset($meta_existing) and !empty($meta_existing) ) {
		
		switch($operator) {
			case '+': 
				$meta_value = $meta_existing + $meta_value; 
				break; 
			case '-': 
				$meta_value = $meta_existing - $meta_value; 
				break; 
			case '*': 
				$meta_value = $meta_existing * $meta_value; 
				break; 
			case 'array': 
				$meta_existing = maybe_unserialize($meta_existing);
				if(is_array($meta_existing)) {					
					// Remove $meta_value from meta_existing if overwrite=true
					if($overwrite) {
						$ct = 0; 
						foreach($meta_existing as $existing_meta) {
							if($existing_meta==$meta_value) {
								unset($meta_existing[$ct]); 
							}							
							$ct++; 
						}						
					}
					
					$meta_existing[] = $meta_value;					
				}
				else $meta_existing = array($meta_value);				
				
				// re-create meta_value as we udpate the value in the end
				$meta_value = $meta_existing; 
				break; 
			default : // string concatenate operation default
				$meta_value = $meta_existing . $operator . $meta_value; 
		}
	}
	elseif($operator=='array') {
		$meta_value = array($meta_value);
	}

	// wpcsc_ajaxy_die($meta_value); 
    if($type == 'users'){
        return update_user_meta($post_id, $meta_key, $meta_value);
    }
    else{
        return update_post_meta($post_id, $meta_key, $meta_value);
    }
	
}


/**
 * Update Last Modified Time in actions
 * @param  none 
 * Deprected: To be removed with wpcsc_update_last_modified
 
 * @return bool - result of updation true/false
 * Called in ajax POST method in ajaxmailjs.js
 */
function wpcsc_update_last_modified( $post_id ) {

	// bail out early if we don't need to update the date
	if( is_admin() || $post_id == 'new' ) {
		// return;
	}

   global $wpdb;
   $datetime = current_time("Y-m-d H:i:s");
   $datetimeGMT = date("Y-m-d H:i:s");
   $updated_columns = ''; 
   $query = "UPDATE $wpdb->posts SET
			post_modified = '$datetime'
            WHERE
            ID = '$post_id'";
	
	if( $wpdb->query( $query ) ) $updated_columns .= ' post_modified'; 
	
    $query = "UPDATE $wpdb->posts SET
			post_modified_gmt = '$datetimeGMT'
            WHERE
            ID = '$post_id'";
	
	if( $wpdb->query( $query ) ) $updated_columns .= ' post_modified_gmt'; 
	
    return 'Post last modified updated: '. $updated_columns;
}

function wpcsc_get_user_role($user_id) {
	
	if(empty($user_id) || $user_id == 0){
		return 'subscriber';
	}
	$user_meta = get_userdata($user_id);
	// $user_roles = $user_meta->roles;
	if($user_meta->roles){
		if ( in_array( 'administrator', $user_meta->roles, true ) ) {
			return 'administrator';
		}
	}
	return 'subscriber';
}

function wpcsc_console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    esc_html_e($js_code, WPCSC_TXT_DOMAIN);
}

function wpcsc_redirect_in_jquery($seconds) {
    $time = $seconds * 1000;
    $url = site_url('my-account');
    echo esc_js('<script>setTimeout(function() { window.location.href = "'.$url.'"}, '.$time.')</script>');
}

function wpcsc_curPageURL() {
	$pageURL = 'http';
	if(isset($_SERVER["HTTPS"]))
		if ($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		// $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	
	return $pageURL;
}

function wpcsc_sanitize_array($arr){
    if(is_array($arr)){
        foreach ( array_keys( $arr ) as $field ) {
            $arr[ $field ] = sanitize_text_field( $field );
        }
    }
    return $arr;
}

/**
 * Recursive sanitation for text or array
 *
 * @param $args (array|string)
 * @return mixed
 */
function wpcsc_sanitize_arr_str($args) {
    if( is_string($args) ){
        $args = sanitize_text_field($args);
    }elseif( is_array($args) ){
        foreach ( $args as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = wpcsc_sanitize_arr_str($value);
            }
            else {
                // $value = sanitize_text_field( $value );
            }
        }
    }
    return $args;
}

function wpcsc_required_fields($request, $required_fields) {
    $valid = true;
    foreach($required_fields as $field) {
        if(!isset($request[$field]) || empty($request[$field])) {
            $valid = false;
            break;
        }
    }
    return $valid;
}

function wpcsc_find_missing_field($request, $required_fields) {
    $invalid = false;
    foreach($required_fields as $field) {
        if(!isset($request[$field]) || empty($request[$field])) {
            $invalid = "field: " . $field;
            break;
        }
    }
    return $invalid;
}

function wpcsc_human_diff_time($to, $from=''){
    if(!$to) return false;

    if($from == '')
        $from = current_time('Y-m-d H:i:s');

    return human_time_diff(strtotime($from), strtotime($to));
}

function wpcsc_get_class_object($slug, $return_name=false, $table_name="") {
    $class_name = false;
    $class_object = false;;

    $slug = wpcsc_create_label_from_name($slug, "_", "-");

    if(class_exists(strtoupper($slug))) {
        $class_name = (strtoupper($slug));
        $class_object = new $class_name();
    }
    elseif(class_exists('WPCSC_' . strtoupper($slug))) {
        $class_name = ('WPCSC_' . strtoupper($slug));
        $class_object = new $class_name();
    }
    return ($return_name) ? $class_name : $class_object;
}

/**
 * Get the client's IP address
 * @return mixed|string
 */
function wpcsc_get_client_ip() {

    if (isset($_SERVER['HTTP_CLIENT_IP']) != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']) != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']) != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']) != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']) != '127.0.0.1')
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

/**
 * if this stopped giving details then need to get new url
 * or get token from site set in url
 * @return array
 */
function wpcsc_get_location($host = 'ip-api'){

    $ip = wpcsc_get_client_ip();

    switch($host){
        case 'ipinfo':
            // $url = 'https://ipinfo.io/'.$ip.'&token=PASTE_TOKEN_HERE';
            $url = 'https://ipinfo.io/'.$ip;
            break;
        case 'ap-api':
            $url = 'http://ip-api.com/json/'.$ip;
            break;
        default:
            $url = 'http://ip-api.com/json/'.$ip;

    }

    $response_body = file_get_contents($url);

    // Convert the string into object
    $location = json_decode($response_body);

    $response = [ 'city' => $location->city ];

    if($host == 'ipinfo'){
        $response['pincode'] = $location->postal;
        $response['state'] = $location->region;
        $response['country'] = rel_get_country_with_code($location->country, 'name');
    }else{
        $response['pincode'] = $location->zip;
        $response['state'] = $location->regionName;
        $response['country'] = $location->country;
    }
    return $response;
}

function wpcsc_hide_email($email)
{
    if(is_email($email))
    {
        list($first, $last) = explode('@', $email);
		$star_characters = min(strlen($first), 3);
        $first = str_replace(substr($first, $star_characters), str_repeat('*', strlen($first)-$star_characters), $first);
        $last = explode('.', $last);
        $last_domain = str_replace(substr($last['0'], '1'), str_repeat('*', strlen($last['0'])-1), $last['0']);
        $hideEmailAddress = $first.'@'.$last_domain.'.'.$last['1'];
        return $hideEmailAddress;
    }
}