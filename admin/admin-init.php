<?php

add_action( 'admin_menu', 'wpcsc_admin_menu' );
function wpcsc_admin_menu()
{
    $menu_slug = 'wpcsc';
    $menu_slug_leads = $menu_slug . '-leads';
    $last_choice = '';
    
    if ( !empty($last_choice) ) {
        $menu_slug .= '&state=' . $last_choice;
        $menu_slug_leads .= '&state=' . $last_choice;
    }
    
    add_menu_page(
        'Child Support Calculator',
        'Child Support Calculator',
        'read',
        $menu_slug,
        false
    );
    // add_submenu_page($menu_slug, '', 'WPCSC', 'manage_options', $menu_slug, 'wpcsc_admin_page');
    add_submenu_page(
        $menu_slug,
        '',
        'WPCSC',
        'manage_options',
        $menu_slug,
        'wpcsc_settings_page'
    );
    // add_submenu_page($menu_slug, 'Income Slabs', 'Income Slabs', 'manage_options', $menu_slug . '-incomes', 'wpcsc_backend_page');
    add_submenu_page(
        $menu_slug,
        'Leads',
        'Leads',
        'manage_options',
        $menu_slug_leads,
        'wpcsc_backend_page'
    );
    // add_submenu_page($menu_slug, 'Settings', 'Settings', 'manage_options', $menu_slug . '-settings', 'wpcsc_settings_page');
}

function wpcsc_admin_page()
{
    // WPCSC::store_table();
    $page = sanitize_text_field( $_GET['page'] );
    ?>
    <div class="wrap">
        <div class="wpcsc-layout__header">
            <div class="wpcsc-layout__header-wrapper">
                <h6><?php 
    esc_html_e( strtoupper( $page ) );
    ?></h6>
            </div>
        </div>
        <div class="wpcsc-layout__body">
            <div class="container">
                <div class="row">
                    <div class="card">
                        <?php 
    _e( get_option( 'wpcsc_table_html', '' ) );
    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
}

function wpcsc_backend_page()
{
    $page = sanitize_text_field( $_GET['page'] );
    $class_obj = wpcsc_get_class_object( $page );
    $page_title = ( $page == 'wpcsc-incomes' ? 'Income Slabs' : wpcsc_create_name_from_label( $page ) );
    ?>
    <div class="wrap">
        <div class="wpcsc-layout__header">
            <div class="wpcsc-layout__header-wrapper">
                <h6><?php 
    esc_html_e( strtoupper( $page_title ) );
    ?></h6>
                <?php 
    _e( $class_obj->bulk_actions() );
    ?>
            </div>
        </div>
        <div class="wpcsc-layout__body">
            <div class="container">
                <div class="row">
                    <div class="card">
                        <div class="wpcsc-datatable <?php 
    _e( $page );
    ?>">
                            <?php 
    $args = [
        'columns'    => $class_obj->get_columns_titles(),
        'attributes' => $class_obj->get_attributes(),
    ];
    wpcsc_page_ajax_datatable( $args );
    ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
}

function wpcsc_settings_page()
{
    $state = get_option( 'wpcsc__state' );
    $state_text = ( $state == false ? '' : '<i class="dashicons dashicons-location"></i> State: <b class="csc-state-name">' . wpcsc_create_name_from_label( $state ) . '</b>' );
    //    $state_edit_url = add_query_arg(['page' => 'wpcsc', 'edit' => 'state'], admin_url('admin.php'));
    //    $state_edit_html = (isset($_GET['edit']) == 'state') ? '' : ' <a href="'.$state_edit_url.'"><i class="dashicons dashicons-edit"></i></a>';
    $html = '<div class="wrap">';
    $html .= '<div class="wpcsc-layout__header">';
    $html .= '<div class="wpcsc-layout__header-wrapper">';
    $html .= '<h6>Child Support Calculator - Settings</h6>';
    // $html .= '<span>'.$state_text.$state_edit_html.'</span>';
    $html .= '<span class="csc-state">' . $state_text;
    $html .= '<span class="cursor text-primary">';
    $html .= '<select class="csc-state-dropdown d-none" name="state" id="state">';
    $html .= wpcsc_create_options_from_array( wpcsc_get_globals( 'states' ), $state );
    $html .= '</select>';
    $html .= ' <i class="dashicons dashicons-edit csc-state-edit"></i>';
    $html .= '</span>';
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="wpcsc-layout__body">';
    $html .= '<div class="container">';
    $wpcsc = new WPCSC();
    $page = sanitize_text_field( $_GET['page'] );
    // check if country option is set
    
    if ( $state == false || isset( $_GET['edit'] ) == 'state' ) {
        // set country first before showing any setting
        $html .= $wpcsc->set_state_page();
    } else {
        // if country is set then setting page as per country selected
        $tab = ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '' );
        $html .= $wpcsc->display_settings_page( $page, $tab );
    }
    
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    _e( $html );
}

/////////////////////////////////////////
// Add Init Actions
/////////////////////////////////////////
add_action( 'wp', 'wpcsc_admin_init_actions' );
function wpcsc_admin_init_actions()
{
}

///////////////////////////////////////
// Integrate wpcsc_auto_update class //
///////////////////////////////////////
add_action( 'admin_init', 'wpcsc_add_admin_init_actions', 99 );
function wpcsc_add_admin_init_actions()
{
    if ( current_user_can( 'wpcsc_team' ) ) {
        add_action( 'admin_bar_menu', 'wpcsc_toolbar_link_to_mypage', 999 );
    }
}
