<?php
function wpcsc_get_current_version() {
    return apply_filters('wpcsc_current_version', WPCSC_VERSION);
}
/**
 * Store our table name in $wpdb with correct prefix
 * Prefix will vary between sites so hook onto switch_blog too
 * @since 1.0
*/
function wpcsc_register_activity_log_table(){
    global $wpdb;
    
	$wpdb->wpcsc_incomes = "{$wpdb->prefix}wpcsc_incomes";
    $wpdb->wpcsc_incomes_nc = "{$wpdb->prefix}wpcsc_incomes_nc";
    $wpdb->wpcsc_leads = "{$wpdb->prefix}wpcsc_leads";
    $wpdb->wpcsc_calculations = "{$wpdb->prefix}wpcsc_calculations";

	do_action('wpcsc_register_tables'); 
}
add_action( 'init', 'wpcsc_register_activity_log_table', 1);
add_action( 'switch_blog', 'wpcsc_register_activity_log_table');

/**
 * Update DB Check
 * @since 1.4
*/
add_action( 'admin_init', 'wpcsc_update_db_check' );
function wpcsc_update_db_check() {

	$installed_version = get_option('wpcsc_db_version');
    $current_version = WPCSC_VERSION;

	// wp_die(WPCSC_VERSION . ' - ' . $base_installed_version);

	if( !$installed_version){
	    //No installed version - we'll assume its just been freshly installed
		wpcsc_create_log_tables($current_version);

		update_option('wpcsc_db_version', $current_version);
	}
	elseif($installed_version != $current_version){
	    if( version_compare($current_version, $installed_version, '>')){
            wpcsc_create_log_tables($current_version);
		}

		if(version_compare($current_version, "1.1.2", '=')){
			(new WPCSC_SETTINGS_NC)->store_table();
		}

        // Hint: Introduced in 2.0.1 - enable_leads option
		if(version_compare($current_version, "2.0.1", '=')){
            $settings = get_option('wpcsc__settings', []);
            $settings['enable_leads'] = 'on';
            update_option('wpcsc_settings', $settings);
		}

        update_option('wpcsc_db_version', $current_version);
	}
}

/**
 * Creates our table
 * Hooked onto activate_[plugin] (via register_activation_hook)
 * @since 1.0
*/
function wpcsc_create_log_tables($current_version=''){
	
	if(empty($current_version)) return; 
	
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$installed_ver = get_option( "wpcsc_db_version" );
	
	//Call this manually as we may have missed the init hook
	wpcsc_register_activity_log_table();

	wpcsc_log_tables();

    add_option( "wpcsc_db_version", $current_version);
}

function wpcsc_log_tables() {

    $tables = [];
	
	// Create Incomes Slabs Log Table
	$tables["wpcsc_incomes"] = [
        'keys' => 'PRIMARY KEY  (id)',
        'columns' => "
            id bigint(20) unsigned NOT NULL auto_increment,
            datetime datetime NOT NULL default '0000-00-00 00:00:00',
            salary int(12) NOT NULL default 0,		
            one int(12) NOT NULL default 0,		
            two int(12) NOT NULL default 0,		
            three int(12) NOT NULL default 0,		
            four int(12) NOT NULL default 0,		
            five int(12) NOT NULL default 0,		
            six int(12) NOT NULL default 0",
    ];

	// Create Incomes Slabs for NC Log Table
	$tables["wpcsc_incomes_nc"] = [
        'keys' => 'PRIMARY KEY  (id)',
        'columns' => "
            id bigint(20) unsigned NOT NULL auto_increment,
            datetime datetime NOT NULL default '0000-00-00 00:00:00',
            salary int(12) NOT NULL default 0,		
            one int(12) NOT NULL default 0,		
            two int(12) NOT NULL default 0,		
            three int(12) NOT NULL default 0,		
            four int(12) NOT NULL default 0,		
            five int(12) NOT NULL default 0,		
            six int(12) NOT NULL default 0,
            bar int(12) NOT NULL default 0",
    ];

	// Create Leads Log Table
	$tables["wpcsc_leads"] = [
        'keys' => 'PRIMARY KEY  (id)',
        'columns' => "
            id bigint(20) unsigned NOT NULL auto_increment,
            datetime datetime NOT NULL default '0000-00-00 00:00:00',
            name varchar(200) NOT NULL default '',		
            email varchar(256) NOT NULL default '',		
            spouse_name varchar(200) NOT NULL default '',		
            spouse_email varchar(256) NOT NULL default '',		
            children int(5) NOT NULL default 0,		
            income int(50) NOT NULL default 0,		
            spouse_income int(50) NOT NULL default 0,		
            over_nights int(5) NOT NULL default 0,
            pincode int(11) NOT NULL default 0,
            city varchar(150) NOT NULL default '',
            state varchar(150) NOT NULL default '',
            country varchar(150) NOT NULL default '',
            enc varchar(150) NOT NULL default '1',
            worksheet varchar(10) default '',
            last_modified varchar(100) NOT NULL default 0",
    ];

	// Create Lead Calculations Log Table
	$tables["wpcsc_calculations"] = [
        'keys' => 'PRIMARY KEY  (id)',
        'columns' => "
            id bigint(20) unsigned NOT NULL auto_increment,
            datetime datetime NOT NULL default '0000-00-00 00:00:00',
            lead_id int(12) NOT NULL default 0,
            children int(5) NOT NULL default 0,		
            income int(50) NOT NULL default 0,		
            spouse_income int(50) NOT NULL default 0,		
            over_nights int(5) NOT NULL default 0,
            compensation int(20) NOT NULL default 0,
            liability int(20) NOT NULL default 0,
            worksheet varchar(10) NOT NULL default '',
            spouse_liability int(20) NOT NULL default 0",
    ];

	$tables = apply_filters('wpcsc_define_tables', $tables); 

	foreach($tables as $table_name=>$table) {
		$dbresults = wpcsc_create_table($table_name, $table['columns'], $table['keys']);
	}
}

function wpcsc_get_log_table_columns($table_name='', $keys=false){

	$cols = []; 

    switch($table_name) {
		case 'wpcsc_incomes':
			$cols = array(
				'id'=> '%d',
				'datetime'=>'%s',
				'salary'=>'%d',
				'one'=>'%d',
				'two'=>'%d',
				'three'=>'%d',
				'four'=>'%d',
				'five'=>'%d',
				'six'=>'%d',
			);
			break;
		case 'wpcsc_incomes_nc':
			$cols = array(
				'id'=> '%d',
				'datetime'=>'%s',
				'salary'=>'%d',
				'one'=>'%d',
				'two'=>'%d',
				'three'=>'%d',
				'four'=>'%d',
				'five'=>'%d',
				'six'=>'%d',
				'bar'=>'%d',
			);
			break;
		case 'wpcsc_leads':
			$cols = array(
				'id'=> '%d',
				'datetime'=>'%s',
				'name'=>'%s',
				'email'=>'%s',
				'spouse_name'=>'%s',
				'spouse_email'=>'%s',
				'over_nights'=>'%d',
				'children'=>'%s',
				'income'=>'%s',
				'spouse_income'=>'%s',
				'pincode'=>'%d',
				'city'=>'%s',
				'state'=>'%s',
				'country'=>'%s',
				'enc'=>'%s',
				'worksheet'=>'%s',
				'last_modified'=>'%s',
			);
			break;
		case 'wpcsc_calculations':
			$cols = array(
				'id'=> '%d',
				'datetime'=>'%s',
				'lead_id'=>'%d',
                'children'=>'%s',
                'income'=>'%s',
                'spouse_income'=>'%s',
                'over_nights'=>'%s',
				'compensation'=>'%d',
				'liability'=>'%d',
				'spouse_liability'=>'%d',
				'worksheet'=>'%s',
			);
			break;
    }

	$cols = apply_filters('wpcsc_table_columns', $cols, $table_name);
	return (($keys) ? array_keys($cols) : $cols);
}


/**
 * Inserts a log into the database
 *
 *@param $data array An array of key => value pairs to be inserted
 *@return int The log ID of the created activity log. Or WP_Error or false on failure.
*/
function wpcsc_insert_log( $data=array(), $table_name='' ){
    global $wpdb;
    
	//Set default values
    $data = wp_parse_args($data, array(
		'user_id'=> get_current_user_id(),
        'datetime'=> current_time('Y-m-d H:i:s'),
    ));
	// return $data;
	//Check date validity
	// Removed this line of code, now make sure datetime is Y:m:d H:i:s in both functions
    // if( !is_float($data['datetime']) || $data['date'] <= 0 ) return 0;
	
	if(empty($table_name)) $table_name = 'wpcsc_activity_log'; 
	
	// Get Fields Based on Table Name
	$allowed_fields = wpcsc_get_log_table_columns($table_name);		
	$column_formats = $allowed_fields; 
    $date_key = 'datetime'; 
	
	//Convert activity date from local timestamp to GMT mysql format
    //$data[$date_key] = date_i18n( 'Y-m-d H:i:s', $data['date'], true );
    
	// Set Data For Appropriate Table
	if( $table_name == 'wpcsc_certi_logs' ) {
		
	}
	else {
		
	}
	
	//Force fields to lower case
    $data = array_change_key_case ( $data );
    //White list columns
    $data = array_intersect_key($data, $column_formats);
    //Reorder $column_formats to match the order of columns given in $data
    $data_keys = array_keys($data);
    $column_formats = array_merge(array_flip($data_keys), $column_formats);
    $wpdb->insert($wpdb->$table_name, $data, $column_formats);
    //return $wpdb->insert_id;
	
	$testing = 0; 
	if($testing) {
		$jita = '';
		$jita .= '<br/><br/>Table Name<br/>';
		$jita .= $wpdb->$table_name;
		$jita .= '<br/><br/>Data<br/>';		
		$jita .= print_r($data, true);
		$jita .= '<br/><br/>Formats<br/>';
		$jita .= print_r($column_formats, true);
		
		if( $wpdb->insert_id ) { $kita = 'Good'; }
		elseif( !$wpdb->insert_id ) { $kita = 'Not Good';  }
		else $kita = 'Something bad';
		return $jita . $kita; 
	}
	else 
		return $wpdb->insert_id; 
}


/**
 * Updates an activity log with supplied data
 *
 *@param $id int ID of the activity log to be updated
 *@param $data array An array of column=>value pairs to be updated
 *@return bool Whether the log was successfully updated.
*/
function wpcsc_update_log( $id, $data=array(), $table_name='' ){
    global $wpdb;
    //Log ID must be positive integer
    $id = absint($id);
    if( empty($id) )
         return false;
    
	//Set default values
    $data = wp_parse_args($data, array(
		'user_id'=> get_current_user_id(),
		'datetime'=> current_time('Y-m-d H:i:s'),
    ));
	
	if(empty($table_name)) $table_name = 'wpcsc_activity_log'; 
	// Get Fields Based on Table Name
	$allowed_fields = wpcsc_get_log_table_columns($table_name);		
	$column_formats = $allowed_fields; 
    $date_key = 'datetime'; 
	
	//Convert activity date from local timestamp to GMT mysql format
    //$data[$date_key] = date_i18n( 'Y-m-d H:i:s', $data['datetime'], true );
	
	//Force fields to lower case
    $data = array_change_key_case ( $data );
    //White list columns
    $data = array_intersect_key($data, $column_formats);
    //Reorder $column_formats to match the order of columns given in $data
	
    $data_keys = array_keys($data);
    $column_formats = array_merge(array_flip($data_keys), $column_formats);
    if ( false === $wpdb->update($wpdb->$table_name, $data, array('id'=>$id), $column_formats) ) {
         return false;
    }
    return true;
}

function wpcsc_update_log_new( $id, $data=array(), $table_name='' ){
    global $wpdb;
    //Log ID must be positive integer
    $id = absint($id);
    if( empty($id) )
         return false;
    
	//Set default values
    $data = wp_parse_args($data, array(
		'datetime'=> current_time('Y-m-d H:i:s'),
    ));
	
	if(empty($table_name)) $table_name = 'wpcsc_activity_log'; 
	// Get Fields Based on Table Name
	$allowed_fields = wpcsc_get_log_table_columns($table_name);		
	$column_formats = $allowed_fields; 
    $date_key = 'datetime'; 
	
	//Convert activity date from local timestamp to GMT mysql format
    //$data[$date_key] = date_i18n( 'Y-m-d H:i:s', $data['datetime'], true );
	
	//Force fields to lower case
    $data = array_change_key_case ( $data );
    //White list columns
    $data = array_intersect_key($data, $column_formats);
    //Reorder $column_formats to match the order of columns given in $data
    $data_keys = array_keys($data);
    $column_formats = array_merge(array_flip($data_keys), $column_formats);
    if ( false === $wpdb->update($wpdb->$table_name, $data, array('id'=>$id), $column_formats) ) {
         return false;
    }
    return true;
}


function wpcsc_get_logs_query($sql, $table_name='') {
	global $wpdb;
    return $wpdb->query($sql); 
}

/**
 * Retrieves activity logs from the database matching $query.
 * $query is an array which can contain the following keys:
 *
 * 'fields' - an array of columns to include in returned roles. Or 'count' to count rows. Default: empty (all fields).
 * 'orderby' - datetime, user_id or id. Default: datetime.
 * 'order' - asc or desc
 * 'user_id' - user ID to match, or an array of user IDs
 * 'since' - timestamp. Return only activities after this date. Default false, no restriction.
 * 'until' - timestamp. Return only activities up to this date. Default false, no restriction.
 *
 *@param $query Query array
 *@return array Array of matching logs. False on error.
 * 'wpcsc_activity_log' set as default as required parameter should not be after optional ones as from PHP 8.0 [PS: Tanjot Singh]
*/
function wpcsc_get_logs( $query=array(), $table_name = 'wpcsc_activity_log' ) {
	
	// wpcsc_ajaxy_die('good: ' . $table_name);  

    global $wpdb;
    /* Parse defaults */
    $defaults = array(
        'fields'=>array(),'search_fields'=>array(),'orderby'=>'datetime','order'=>'desc', 'user_id'=>false,
        'since'=>false,'until'=>false,'number'=>-1,'offset'=>0,'group_by'=>0,'date_key'=>'datetime'
    );

    $query = wp_parse_args($query, $defaults);

    /* Form a cache key from the query */
    $cache_key = 'rel_logs:'.md5( serialize($query));
    $cache = wp_cache_get( $cache_key );
    if ( false !== $cache ) {
        $cache = apply_filters('rel_get_logs', $cache, $query);
        return $cache;
    }
    extract($query);

    if(empty($table_name)) $table_name = 'rel_activity_log';
    if(!isset($match_type)) $match_type = 'AND';

    /* SQL Select */
    //Whitelist of allowed fields
    // Get Fields Based on Table Name
    $allowed_fields = wpcsc_get_log_table_columns($table_name);
    $column_formats = $allowed_fields;

    if(!isset($allowed_fields[$orderby])) {
        $orderby = "";
    }

    if( is_array($fields) ){
        //Convert fields to lowercase (as our column names are all lower case - see part 1)
        $fields = array_map('strtolower',$fields);
        //Sanitize by white listing
        $fields = array_intersect($fields, $allowed_fields);
    }else{
        $fields = strtolower($fields);
    }

    //Return only selected fields. Empty is interpreted as all
    if( empty($fields) ){
        $select_sql = "SELECT * FROM {$wpdb->$table_name}";
    }elseif( 'count' == $fields ) {
        $select_sql = "SELECT COUNT(*) FROM {$wpdb->$table_name}";
    }else{
        $select_sql = "SELECT ".implode(',',$fields)." FROM {$wpdb->$table_name}";
    }

    // Add Group By Parameter
    //if($group_by) $select_sql .= " GROUP BY " . $group_by;

    /*SQL Join */
    //We don't need this, but we'll allow it be filtered (see 'rel_logs_clauses' )
    $join_sql='';

    /* SQL Where */
    //Initialise WHERE
    $where_sql = ($match_type=='OR') ? 'WHERE' : 'WHERE 1=1';
    if( !empty($id) )
        $where_sql .=  $wpdb->prepare(' AND id=%d', $id);

    if( isset($search_fields) AND is_array($search_fields) ) {
        $condition_count = 0;
        foreach($search_fields as $field=>$data) {
            if(strtoupper($data['compare'])=='LIKE') {
                $data['value'] = '%' . $data['value'] . '%';
            }
            else if(strtoupper($data['compare'])=='IN' || strtoupper($data['compare'])=='NOT IN') {
                $data['value'] = '("' . implode( '", "', $data['value']) . '")';
            }

            if($match_type=='AND') {
                if(strtoupper($data['compare'])=='IN' || strtoupper($data['compare'])=='NOT IN') {
                    $where_sql .=  $wpdb->prepare(' %1$s %2$s %3$s %4$s', $match_type, $field,$data['compare'],$data['value']);
                }
                else {
                    $where_sql .=  $wpdb->prepare(' %1$s %2$s %3$s "%4$s"', $match_type, $field,$data['compare'],$data['value']);
                }
            }
            else {
                if(strtoupper($data['compare'])=='IN' || strtoupper($data['compare'])=='NOT IN') {
                    $where_sql .=  $wpdb->prepare(' %1$s %2$s %3$s %4$s', $match_type, $field,$data['compare'],$data['value']);
                }
                else {
                    if($condition_count) {
                        $where_sql .=  $wpdb->prepare(' %1$s %2$s %3$s "%4$s"', $match_type, $field,$data['compare'],$data['value']);
                    }
                    else {
                        $where_sql .=  $wpdb->prepare(' %1$s %2$s "%3$s"', $field, $data['compare'],$data['value']);
                    }
                }
            }

            $condition_count++;
        }
    }

    //wp_die($where_sql);

    if( !empty($user_id) ){
        //Force $user_id to be an array
        if( !is_array( $user_id) )
            $user_id = array($user_id);
        $user_id = array_map('absint',$user_id); //Cast as positive integers
        $user_id__in = implode(',',$user_id);
        $where_sql .=  " AND user_id IN($user_id__in)";
    }
    $since = absint($since);
    $until = absint($until);
    if( !empty($since) ) {
        $where_sql .=  $wpdb->prepare(' AND '.$date_key.' >= %s', date_i18n( 'Y-m-d 00:00:00', $since, true));
    }
    if( !empty($until) ) {
        $where_sql .=  $wpdb->prepare(' AND '.$date_key.' <= %s', date_i18n( 'Y-m-d 23:59:59', $until, true));
        //echo '<br/><br/>Table Name: '.$table_name.' : '; print_r($where_sql);
    }

    /* SQL Order */
    //Whitelist order
    $order = strtoupper($order);
    $order = ( 'ASC' == $order ? 'ASC' : 'DESC' );

    // HINT: added to override the orberby key is date_key is not datetime
    if($date_key != 'datetime') $orderby = $date_key;

    switch( $orderby ){
        case "":
            break;
        /*case 'id':
            $order_sql = "ORDER BY id $order";
        break;
        case 'user_id':
            $order_sql = "ORDER BY user_id $order";
        break;
        case 'datetime':
             $order_sql = "ORDER BY datetime $order";
        */
        default:
            $order_sql = "ORDER BY $orderby $order";

            // ToDo: Introduce CAST as int with param [order_as_int = true]
            if($orderby=='entity_id')
                $order_sql = "ORDER BY CAST($orderby AS int) $order";
            break;
    }

    /* SQL Limit */
    $offset = absint($offset); //Positive integer
    if( $number == -1 ){
        $limit_sql = "";
    }else{
        $number = absint($number); //Positive integer
        $limit_sql = "LIMIT $offset, $number";
    }
    /* Filter SQL */
    $pieces = array( 'select_sql', 'join_sql', 'where_sql', 'order_sql', 'limit_sql' );
    $clauses = apply_filters( 'rel_logs_clauses', compact( $pieces ), $query );
    foreach ( $pieces as $piece )
        $piece = isset( $clauses[ $piece ] ) ? $clauses[ $piece ] : '';
    /* Form SQL statement */
    $sql = "$select_sql $where_sql $order_sql $limit_sql";

    $sql = str_replace("\\", "", $sql);

    if( 'count' == $fields ){
        return $wpdb->get_var($sql);
    }

    if(0 AND $table_name=='wpcsc_transactions') {
        // $sql = 'SELECT * FROM wp_sxjm_wpcsc_transactions WHERE 1=1 AND payment_mode IN ("razorpay", "Razorpay", "Google Pay") AND datetime >= "2022-02-01 00:00:00" AND datetime <= "2022-02-28 23:59:59" ORDER BY id DESC LIMIT 0, 10';

        wp_die($sql);
    }

    //  rel_ajaxy_die($sql);
    /* Perform query */
    $logs = $wpdb->get_results($sql, 'ARRAY_A');

    /* Add to cache and filter */
    wp_cache_add( $cache_key, $logs, 24*60*60 );
    $logs = apply_filters('wpcsc_get_logs', $logs, $query);
    return $logs;
}
 
 
/**
 * Deletes an activity log from the database
 *
 *@param $id int ID of the activity log to be deleted
 *@return bool Whether the log was successfully deleted.
*/
function wpcsc_delete_log( $id, $table_name ){
    global $wpdb;
    //Log ID must be positive integer
    $id = absint($id);
    if( empty($id) )
         return false;
    do_action('wpcsc_delete_log',$id);

	if(empty($table_name)) $table_name = 'wpcsc_activity_log'; 
	// Get Fields Based on Table Name
	$allowed_fields = wpcsc_get_log_table_columns($table_name);
	$column_formats = $allowed_fields; 
    $date_key = 'datetime'; 
	
	$sql = $wpdb->prepare("DELETE from {$wpdb->$table_name} WHERE id = %d", $id);

    do_action('wpcsc_deleted_log',$id);

	return ($wpdb->query($sql));
}

/**
 * Deletes an activity log from the database
 *
 *@param $id int ID of the activity log to be deleted
 *@return bool Whether the log was successfully deleted.
*/
function wpcsc_delete_log_all($table_name){
    global $wpdb;
    do_action('wpcsc_delete_log_all',$table_name);
	
	if(empty($table_name)) $table_name = 'wpcsc_activity_log'; 
	// Get Fields Based on Table Name
	$allowed_fields = wpcsc_get_log_table_columns($table_name);		
	$column_formats = $allowed_fields; 
    $date_key = 'datetime';

    $sql = $wpdb->prepare("DELETE from {$wpdb->$table_name} WHERE 1 = %d", 1);

    if( $wpdb->query( $sql ) === 0) return 0;

    do_action('wpcsc_delete_log_all',$table_name);
    return true;
}

function wpcsc_create_table($table_name, $table_columns, $table_keys = null, $db_prefix = true, $charset_collate = null) {
	
	global $wpdb;

	if($charset_collate == null)
		$charset_collate = $wpdb->get_charset_collate();
	
	$table_name = ($db_prefix) ? $wpdb->prefix.$table_name : $table_name;
	$table_columns = trim(strtolower($table_columns));

	if($table_keys)
		$table_keys =  ", $table_keys";

	$table_structure = "( $table_columns $table_keys )";

    // if($table_name=='wp_uobb_wpcsc_options') 	    wp_die($table_structure . '--');

	$search_array = array();
	$replace_array = array();

	$search_array[] = "`";
	$replace_array[] = "";

	$table_structure = str_replace($search_array, $replace_array, $table_structure);

	$sql = "CREATE TABLE $table_name $table_structure $charset_collate;";

	// Rather than executing an SQL query directly, we'll use the dbDelta function in wp-admin/includes/upgrade.php (we'll have to load this file, as it is not loaded by default)
	require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
	
	if($table_name=='wp_uobb_wpcsc_options') {
		// wp_die($sql);
	}

	// The dbDelta function examines the current table structure, compares it to the desired table structure, and either adds or modifies the table as necessary
	return dbDelta($sql);
}