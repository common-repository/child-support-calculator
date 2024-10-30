<?php

class WPCSC
{
    public static $options = [
            'wpcsc__email'
    ];

    public function __construct()
    {
        $this->init_hook();
        add_shortcode('WPCSC', array((new WPCSC_CALC()), 'display_calc'));
        add_shortcode('WPCSC_TABLE', array($this, 'display_table'));
    }

    public function store_defaults()
    {
        $options = ['form', 'email', 'admin_email', 'pdf', 'settings', 'form-nc'];

        foreach($options as $option) {
            $this->reset_default($option);
        }
    }

    public function delete_option($option_name='') {
        delete_option($option_name);
    }

    /**
     * @param $option_name
     * @return bool
     */
    public function reset_default($option_name='') {
        if(empty($option_name)) return false;

        self::delete_option('wpcsc__' . $option_name);
        $option_defaults = $this->get_defaults($option_name);

        if(update_option('wpcsc__' . $option_name, $option_defaults))
            return __("Updated successfully", WPCSC_TXT_DOMAIN);
        else return __("Already same", WPCSC_TXT_DOMAIN);
    }

    public function get_defaults($option_name) {

        switch($option_name) {
            case 'settings':
                $option = [
                    'logo_path' => wpcsc_plugin_url('/assets/img/logo.png'),
                    'watermark' => wpcsc_plugin_url('/assets/img/icon.png'),
                    'enable_worksheet' => '',
                    'enable_leads' => 'on',
                    'delete_data' => ''
                ];
                break;
            case 'form':
                $option = [
                    'form_title' => 'Use this child support calculator to estimate your child support payments based on your state’s guidelines*',
                    'form_text' => 'Before we can get started we need a little of information from you. Please provide the following information to complete your child support calculation.',
                    'export_form_title' => 'EXPORT RESULT: WORDPRESS CHILD SUPPORT CALCULATOR',
                    'export_form_text' => 'Please Fill the details below and report will be automatically download.',
                ];
                break;
            case 'pdf':
                $option = [
                    'enable' => 'on',
                    'file_name_prefix' => 'child-support',
                    'body' => WPCSC_SETTINGS::template_for_pdf(),
                ];
                break;
            case 'email':
                $option = [
                    'enable' => 'on',
                    'subject' => 'Child Support Estimation',
                    'body' => WPCSC_SETTINGS::template(),
                ];
                break;
            case 'email-nc':
                $option = [
                    'enable' => 'on',
                    'subject' => 'Child Support Estimation',
                    'body' => WPCSC_SETTINGS::template('nc'),
                ];
                break;
            case 'admin_email':
                $option = [
                    'enable' => 'on',
                    'receiver' => get_bloginfo('admin_email'),
                    'subject' => 'Child Support Estimation',
                    'body' => WPCSC_SETTINGS::admin_template(),
                ];
                break;
            case 'admin_email-nc':
                $option = [
                    'enable' => 'on',
                    'receiver' => get_bloginfo('admin_email'),
                    'subject' => 'Child Support Estimation',
                    'body' => WPCSC_SETTINGS::admin_template('nc'),
                ];
                break;
            case 'form-nc':
                $option = [
                    'form_title' => 'Use this child support calculator to estimate your child support payments based on your state’s guidelines*',
                    'form_text' => 'Before we can get started we need a little of information from you. Please provide the following information to complete your child support calculation.',
                    'form_disclaimer' => 'Disclaimer: This calculator is an estimate only and does not constitute legal advice. Use of this calculator does not create an attorney-client relationship.',
                    'export_form_title' => 'EXPORT RESULT: WORDPRESS CHILD SUPPORT CALCULATOR',
                    'export_form_text' => 'Please fill the details below and report will be automatically downloaded.',
                    'no_of_children' => 'How many children do you have from this marriage?',
                    'over_nights' => 'Overnights',
                    'gross_income' => 'Your Monthly Gross Income (in $)',
                    'other_gross_income' => 'Other Parent Monthly Gross Income (in $)',
                    'child_support' => 'Pre-Existing Child Support (in $)',
                    'other_child_support' => 'Other Parent Pre-Existing Child Support (in $)',
                    'prev_rel_children' => 'Number of other Child(ren) you have responsibility for:',
                    'other_prev_rel_children' => 'Number of other Child(ren) other Parent responsible for:',
                    'child_care' => 'Child Care Cost',
                    'other_child_care' => 'Other Parent Child Care Cost',
                    'child_insurance' => 'Child health Insurance',
                    'other_child_insurance' => 'Other Parent Child health Insurance',
                    'child_extraordinary' => 'Extraordinary Expenses',
                    'other_child_extraordinary' => 'Other Parent Extraordinary Expenses',
                    'mandate_zero' => 'on'
                ];
                break;
            default:
                $option = [];
        }

        return $option;
    }

    public function init_hook()
    {
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
    }

    /*
    * Enqueue scripts on frontend, is_admin hook is just for safety
    */
    public function enqueue_scripts()
    {
        global $wpcsc_opts;
        wp_enqueue_script( 'jquery' );
        wp_register_style('wpcsc', wpcsc_plugin_url('assets/css/style.css'), dirname(__FILE__), WPCSC_JS_VERSION, null, true);
        wp_enqueue_style('wpcsc');
        wp_register_style('wpcsc-testing', wpcsc_plugin_url('assets/css/style.css'), dirname(__FILE__), WPCSC_JS_VERSION, null, true);
        wp_enqueue_style('wpcsc');

        // wp_register_script('wpcsc', wpcsc_plugin_url('assets/js/wpcsc.js'), dirname(__FILE__), WPCSC_JS_VERSION, true);
        wp_register_script('wpcsc', wpcsc_plugin_url('assets/js/wpcsc.js'), ['jquery'], WPCSC_JS_VERSION);
        wp_enqueue_script('wpcsc');
        $export_form = get_option('wpcsc__form', []);
        $localize = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'is_premium' => json_encode(wpcsc_fs()->can_use_premium_code()),
            'export_form_title' => $export_form['export_form_title'] ?? '',
            'export_form_text' => $export_form['export_form_text'] ?? '',
        );
        wp_localize_script( 'wpcsc', 'WPCSC_AJAX', $localize );
    }

    /*
    * Enqueue scripts on backend
    */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_style('wpcsc-admin', wpcsc_plugin_url('assets/css/wpcsc-admin.css'), dirname(__FILE__), WPCSC_JS_VERSION, null);
    }

    public function display_table($atts, $content = NULL)
    {

        extract(shortcode_atts(array(
            'tabs' => true,
        ), $atts));

        // pass the attributes to create_calc functions and render the display
        return get_option('wpcsc_table_html');
    }

    public static function get_percentage($a, $b)
    {
        return number_format((float)($a / $b) * 100, 3);
    }

    public function set_state_page(){
        $state = get_option('wpcsc__state', '');
        return '
            <div class="row w-lg-50 w-sm-100" style="display:block;margin:0px auto;">
                <div class="col-md-12">
                    <div class="wpcsc-card card">
                        <div class="card-header">
                            <h5>Set preferred country for Child Support Calculator</h5>
                        </div>
                        <div class="card-body">
                            <form class="form-ajax" data-action="action_set_state">
                                <div class="row form-group">
                                    <label for="state">Select Country</label>
                                    <select name="state" id="state" class="wpcsc-form-control" style="max-width:95%;margin:10px;padding:5px 24px 5px 8px;">
                                        '.wpcsc_create_options_from_array(wpcsc_get_globals('states'), $state).'
                                    </select>
                                </div>
                                <div class="row form-group p-2">
                                    <div class="col-md-12 p-2">
                                        <input type="hidden" name="wp_nonce" value="'.wp_create_nonce().'">
                                        <button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="margin-top:3px"></i> Submit</button>
                                    </div>
                                    <div class="col-md-12 p-2">
                                        <div class="ajax-result"></div>
                                    </div>
                                </div>
                            </form>
                        </div>  
                    </div>    
                </div>
            </div>
              
        ';
    }

    public function display_settings_page($page, $current_tab='settings'){

        $state = get_option('wpcsc__state', '');

        $class_obj = (new WPCSC_CALC())->get_settings_class_object($state);

        $tabs = $class_obj->get_tabs();

        $html = '<div id="tabs">';
            $html .= '<div class="nav-tab-wrapper">';
            foreach( $tabs as $tab => $name ){
                $class = ( $tab == $current_tab ) ? ' nav-tab-active' : '';
                $html .= "<a class='nav-tab{$class}' href='?page={$page}&tab={$tab}'>{$name}</a>";
            }
            $html .= '</h2>';
        $html .= '</div>';

        $html .= '<div id="tab-content" class="row tab-content">';
            switch ($current_tab){
                case 'settings':
                case '':
                    $html .= $this->tab_settings($state);
                    break;
                default:
                    $html .= $class_obj->get_settings_html($current_tab);
            }
        $html .= '</div>';

        return $html;
    }

    public static function tab_settings($state = '')
    {
        $settings = get_option('wpcsc__settings', []);
        $state = get_option('wpcsc__state', '');
        $state_attr = ($state !== '' && $state != 'global') ? ' state="'.wpcsc_create_name_from_label($state).'"' : '';

        $html = '<div class="col-md-8 col-editor">';
            $html .= '<div class="wpcsc-card card">';
                $html .= '<div class="card-header">';
                    $html .= '<h6>Settings<h6>';
                $html .= '</div>';

                $html .= '<div class="card-body">';
                    $html .= '<form class="form-ajax" data-action="action_update_settings" data-task="settings">';
                        $html .= '<fieldset>';
                            $html .= '<div class="row form-group p-2">';
                                $html .= ' <label class="col-md-3" for="wpcsc_recipient">Logo Path</label>';
                                $html .= ' <div class="col-md-9">';
                                    $html .= ' <input type="text" class="form-control" name="fields[logo_path]" id="" value="' . $settings['logo_path'] . '">';
                                    if (!empty($settings['logo_path'])) {
                                        $html .= '<a class="small" href="' . str_replace(' ', '/', $settings['logo_path']) . '" target="_blank">View Image</a>';
                                    }
                                $html .= '</div>';
                            $html .= '</div>';
                            $html .= ' <div class="row form-group p-2">';
                                $html .= ' <label class="col-md-3" for="wpcsc_recipient">Watermark</label>';
                                $html .= ' <div class="col-md-9">';
                                    $html .= ' <input type="text" class="form-control" name="fields[watermark]" id="" value="' . $settings['watermark'] . '">';
                                    if (!empty($settings['watermark'])) {
                                        $html .= '<a class="small" href="' . str_replace(' ', '/', $settings['watermark']) . '" target="_blank">View Image</a>';
                                    }
                                $html .= '</div>';
                            $html .= '</div>';

                            $html .= ' <div class="row form-group p-2">';
                                $html .= '<label class="col col-md-3" for="wpcsc_delete_data">Worksheet Result</label>';
                                $html .= '<div class="col-8 col-md-9 form-switch" style="padding-left:calc(var(--bs-gutter-x) * .5)">';
                                    $checked = (is_array($settings) && $settings['enable_worksheet'] == 'on') ? ' checked' : '';
                                    $html .= '<input class="form-check-input" type="checkbox" role="switch" name="fields[enable_worksheet]" id="enable_worksheet"' . $checked . '>';
                                    $html .= '<span class="ml-2 small text-danger" style="margin:left:10px;">Hint: Disabling this will hide the government standard worksheet detailed result.</span>';
                                $html .= '</div>';
                            $html .= '</div>';

                            $html .= ' <div class="row form-group p-2">';
                                $html .= '<label class="col col-md-3" for="wpcsc_delete_data">Enable Leads</label>';
                                $html .= '<div class="col-8 col-md-9 form-switch" style="padding-left:calc(var(--bs-gutter-x) * .5)">';
                                    $checked = (is_array($settings) && $settings['enable_leads'] == 'on') ? ' checked' : '';
                                    $html .= '<input class="form-check-input" type="checkbox" role="switch" name="fields[enable_leads]" id="wpcsc_enable_leads"' . $checked . '>';
                                    $html .= '<span class="ml-2 small text-danger" style="margin:left:10px;">Hint: Disabling this will disable lead generation while exporting the PDF.</span>';
                                $html .= '</div>';
                            $html .= '</div>';

                            $html .= ' <div class="row form-group p-2">';
                                $html .= '<label class="col col-md-3" for="wpcsc_delete_data">Delete Data</label>';
                                $html .= '<div class="col-8 col-md-9 form-switch" style="padding-left:calc(var(--bs-gutter-x) * .5)">';
                                    $checked = (is_array($settings) && $settings['delete_data'] == 'on') ? ' checked' : '';
                                    $html .= '<input class="form-check-input" type="checkbox" role="switch" name="fields[delete_data]" id="wpcsc_delete_data"' . $checked . '>';
                                    $html .= '<span class="ml-2 small text-danger" style="margin:left:10px;">Hint: Enabling this will delete all the data when the plugin is uninstalled.</span>';
                                $html .= '</div>';
                            $html .= '</div>';

                            $html .= '<div class="row form-group p-2">';
                                $html .= '<div class="col-md-12 p-2">';
                                    $html .= '<input type="hidden" name="wp_nonce" value="' . wp_create_nonce() . '">';
                                    $html .= '<input type="hidden" name="state" value="' . $state . '">';
                                    $html .= '<button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="margin-top:3px"></i> Submit</button>';
                                $html .= '</div>';
                                $html .= '<div class="col-md-12 p-2">';
                                    $html .= '<div class="ajax-result"></div>';
                                $html .= '</div>';
                            $html .= '</div>';
                        $html .= '</fieldset>';
                    $html .= '</form>';
                $html .= '</div>';
                $html .= '<span class="btn btn-link text-danger small btn-ajaxy wpcsc-reset-default" data-op="reset_default" data-handle="wpcsc" data-option="settings" style="padding-left:10px;"> Reset Defaults</span>';
            $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-4">';
            $html .= '<div class="wpcsc-card card">';
                $html .= '<div class="card-header">';
                    $html .= '<h6>Instructions<h6>';
                $html .= '</div>';
                $html .= '<div class="card-body">';
                    $html .= 'Copy shortcode <code>[WPCSC'.$state_attr.']</code> and then paste it in the page or section where you want to display and save it.<br>You are good to go.';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * @param $task
     * @param $data
     * @return array
     */
    public function update_options($task, $data){
        $option_name = 'wpcsc__' . $task;

        // $wpcsc_settings = get_option($option_name);

        $option_defaults = $this->get_defaults($task);

        // to prevent the default values to update everytime [in case of checkbox input field]
        array_walk_recursive($option_defaults, function(&$value) { $value = "" ; });

        $data['fields'] = wp_parse_args($data['fields'], $option_defaults);

        $data['fields'] = array_map('wp_unslash', $data['fields']);

        $updated = update_option($option_name, $data['fields']);

        $message = wpcsc_create_name_from_label($task).' settings are updated';

        return ['success' => true, 'reason' => $message, 'redirect' => ''];
    }

    public static function get_custom_logo_url()
    {
        $custom_logo_id = get_theme_mod('custom_logo');
        $image = wp_get_attachment_image_src($custom_logo_id, 'full');
        return $image[0];
    }
}

$wpcsc = new WPCSC();