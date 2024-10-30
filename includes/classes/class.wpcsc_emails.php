<?php

class WPCSC_EMAILS
{
    public static function get_template($args, $html = '')
    {
        if (!is_array($args) || empty($html)) {
            return 'No content generated';
        }

        $wpcsc_options = get_option('wpcsc__settings');
        $email_settings = get_option('wpcsc__email');

        $defaults = [
            'site_url' => site_url(),
            'site_title' => get_bloginfo('name'),
            'current_time' => current_time('l, M d, Y  H:i a'),
            'logo_path' => $wpcsc_options['logo_path'] ?? wpcsc_plugin_url('/assets/img/logo.png'),
            'watermark' => $wpcsc_options['watermark'] ?? wpcsc_plugin_url('/assets/img/logo.png'),
        ];

        $args = wp_parse_args($args, $defaults);

        foreach ($args as $arg => $val) {
            $html = str_replace("{{" . $arg . "}}", ((!empty($val) || $val == 0) ? $val : "-"), $html);
        }

        return $html;
    }

    public static function template_for_pdf()
    {
        ob_start(); ?>

        <!DOCTYPE html>
        <html>
        <title>Child Support Estimation</title>
        <body style="font-size:16px;font-family:sans-serif !important;padding-top:0;color:#6e6e6e">

        <?php echo self::template(); ?>

        </body>
        </html>

        <?php
        return ob_get_clean();
    }

    public static function template()
    {
        ob_start(); ?>
        <div class="body">
            <style>
                table td {
                    border: none;
                    padding: 10px;
                }

                .wpcsc-footer {
                    padding: 15px 10px;
                    border: 1px solid #fff;
                    position: absolute;
                    bottom: 0;
                    width: 100%;
                }
            </style>
            <table style="width:100%">
                <tr>
                    <td style="font-size:11px"><b>WPCSC</b> <span>Child Support Estimation</span></td>
                    <td style="font-size:11px;text-align:right;">{{current_time}}</td>
                </tr>
            </table>
            <div style="margin-top:10px;">
                <table style="width:100%">
                    <tbody>
                    <tr>
                        <td><img src="{{logo_path}}" height="80px" width="auto"></td>
                        <td style="font-size:11px;text-align:right">Estimation by <a href="{{site_url}}">{{site_title}}</a></td>
                    </tr>
                    </tbody>
                </table>
                <table style="width:100%">
                    <tbody>
                    <tr>
                        <td style="text-align:center;text-transform:capitalize;font-size:30px;font-weight:bold;">
                            Child Support Estimation
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">Child(ren)</td>
                        <td style="text-align:right;font-weight:bold">{{children}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Your Monthly Gross Income</td>
                        <td style="text-align:right;font-weight:bold">$ {{income}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent's Estimated Monthly Gross Income</td>
                        <td style="text-align:right;font-weight:bold">$ {{spouse_income}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Your Overnights (%)</td>
                        <td style="text-align:right;font-weight:bold">{{over_nights}}%</td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">Your Name</td>
                        <td style="text-align:right;font-weight:bold">{{name}}<br><small>{{your_email}}</small></td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent Name</td>
                        <td style="text-align:right;font-weight:bold">{{spouse_name}}<br><small>{{spouse_email}}</small>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">Your Liability</td>
                        <td style="text-align:right;font-weight:bold">$ {{liability}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent Liability</td>
                        <td style="text-align:right;font-weight:bold">$ {{spouse_liability}}</td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 70%;">Estimated monthly child support payment you should
                            <b>{{pay_or_receive}}</b>
                        </td>
                        <td style="text-align:right;font-weight:bold">$ {{compensation}}</td>
                    </tr>
                    </tbody>
                </table>
                <div class="wpcsc-footer"
                     style="margin:70px auto 20px;text-align:center;font-size:11px;font-style:italic;">Disclaimer:
                    Please remember that these calculators are for informational and educational purposes only.
                    Results calculated from: https://wpchildsupport.com/child-support-calculator
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function admin_template()
    {
        ob_start(); ?>
        <div class="body">
            <style>
                table td {
                    border: none;
                    padding: 10px;
                }
                .wpcsc-footer {
                    padding: 15px 10px;
                    border: 1px solid #fff;
                    position: absolute;
                    bottom: 0;
                    width: 100%;
                }
            </style>
            <table style="width:100%">
                <tr>
                    <td style="font-size:11px"><b>WPCSC</b> <span>Child Support Estimation</span></td>
                    <td style="font-size:11px;text-align:right;">{{current_time}}</td>
                </tr>
            </table>
            <div style="margin-top:10px;">
                <table style="width:100%">
                    <tbody>
                    <tr>
                        <td><img src="{{logo_path}}" height="80px" width="auto"></td>
                        <td style="font-size:11px;text-align:right">Estimation by <a
                                    href="{{site_url}}">{{site_title}}</a></td>
                    </tr>
                    </tbody>
                </table>
                <table style="width:100%">
                    <tbody>
                    <tr>
                        <td style="text-align:center;text-transform:capitalize;font-size:30px;font-weight:bold;">Child
                            Support Estimation
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">Child(ren)</td>
                        <td style="text-align:right;font-weight:bold">{{children}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">First Parent Monthly Gross Income</td>
                        <td style="text-align:right;font-weight:bold">$ {{income}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent's Estimated Monthly Gross Income</td>
                        <td style="text-align:right;font-weight:bold">$ {{spouse_income}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">First Parent's Overnights (%)</td>
                        <td style="text-align:right;font-weight:bold">{{over_nights}}%</td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">First Parent</td>
                        <td style="text-align:right;font-weight:bold">{{name}}<br><small>{{your_email}}</small></td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent Name</td>
                        <td style="text-align:right;font-weight:bold">{{spouse_name}}<br><small>{{spouse_email}}</small>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">Your Liability</td>
                        <td style="text-align:right;font-weight:bold">$ {{liability}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent Liability</td>
                        <td style="text-align:right;font-weight:bold">$ {{spouse_liability}}</td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 70%;">Estimated monthly child support payment <b>First Parent</b> should
                            <b>{{pay_or_receive}}</b>
                        </td>
                        <td style="text-align:right;font-weight:bold">$ {{compensation}}</td>
                    </tr>
                    </tbody>
                </table>
                <div class="wpcsc-footer"
                     style="margin:70px auto 20px;text-align:center;font-size:11px;font-style:italic;">
                    Disclaimer: Please remember that these calculators are for informational and educational purposes
                    only.
                    Results calculated from: https://wpchildsupport.com/child-support-calculator
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function email_user()
    {
        $email_settings = get_option('wpcsc__email', []);
        $settings = get_option('wpcsc__settings', []);

        $defaults = [
            'site_url' => site_url(),
            'site_title' => get_bloginfo('name'),
            'current_time' => current_time('l, M d, Y  H:i a'),
            'logo_path' => $settings['logo_path']
        ];

        foreach ($defaults as $arg => $val) {
            $email_settings['body'] = str_replace("{{" . $arg . "}}", ((!empty($val) || $val == 0) ? $val : "-"), $email_settings['body']);
        }
        // var_dump($email_settings);

        $html = '<div class="col-md-6 col-editor">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>User Email Settings <small class="float-end cursor expand-handle"><i class="dashicons dashicons-fullscreen-alt"></i></small><h6>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        if (wpcsc_fs()->is_not_paying()) {
            $html .= '<section class="wpcsc-is__premium"><h6>' . __('Awesome Premium Features', WPCSC_TXT_DOMAIN);
            $html .= ' <a href="' . wpcsc_fs()->get_upgrade_url() . '">' . __('Upgrade Now!', WPCSC_TXT_DOMAIN) . '</a></h6>';
            $html .= '</section>';
        }

        $is_premium = wpcsc_fs()->is__premium_only();
        $fieldset_disabled = $is_premium ? '' : 'disabled';
        $fieldset_disabled = '';

        $html .= '<form class="form-ajax" data-action="action_update_settings" data-task="email">';
        $html .= '<fieldset ' . $fieldset_disabled . '>';
        $html .= ' <div class="row form-group p-2">';
        $html .= '<label class="col col-md-3" for="wpcsc_send_mail">Send Email</label>';
        $html .= '<div class="col-8 col-md-9 form-switch" style="padding-left:calc(var(--bs-gutter-x) * .5)">';
        $checked = (is_array($email_settings) && $email_settings['enable'] == 'on') ? ' checked' : '';
        $html .= '<input class="form-check-input" type="checkbox" role="switch" name="fields[enable]" id="wpcsc_send_mail"' . $checked . '>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="wpcsc_subject">Subject</label>';
        $html .= ' <div class="col-md-9">';
        $html .= ' <input type="text" class="form-control" name="fields[subject]" id="" value="' . $email_settings['subject'] . '">';
        $html .= '</div>';
        $html .= '</div>';

        if ($is_premium) {
            $html .= ' <div class="row form-group p-2">';
            $html .= ' <label class="col-md-3" for="wpcsc_mail_body">Body</label>';
            $html .= ' <div class="col-md-9">';
            // ToDo: Link with HTML Preview - CAN BE DONE AT LAST
            ob_start();
            $editor_id = 'wpcsc_mail_body';
            wp_editor($email_settings['body'], $editor_id, [
                'textarea_name' => 'fields[body]',
            ]);
            $html .= ob_get_clean();
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '<div class="row form-group p-2">';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<input type="hidden" name="wp_nonce" value="' . wp_create_nonce() . '">';

        $html .= '<button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="padding-top:3px"></i> Submit</button>';

        $html .= '</div>';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<div class="ajax-result"></div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '<span class="btn btn-link text-danger small btn-ajaxy wpcsc-reset-default" data-op="reset_default" data-handle="wpcsc" data-option="email" style="padding-left:10px;"> Reset Defaults</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6 col-preview">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Email Preview [Demo]<h6>';
        $html .= '</div>';
        $html .= '<div class="card-body wpcsc-email-preview">';

        $html .= '<div class="wpcsc-email-default">';
        $html .= '<style>.wpcsc-footer{position:unset!important;}</style>';
        $html .= $email_settings['body'];
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function email_admin()
    {
        $email_settings = get_option('wpcsc__admin_email', []);
        $settings = get_option('wpcsc__settings', []);

        $defaults = [
            'site_url' => site_url(),
            'site_title' => get_bloginfo('name'),
            'current_time' => current_time('l, M d, Y  H:i a'),
            'logo_path' => $settings['logo_path']
        ];

        foreach ($defaults as $arg => $val) {
            $email_settings['body'] = str_replace("{{" . $arg . "}}", ((!empty($val) || $val == 0) ? $val : "-"), $email_settings['body']);
        }

        $html = '<div class="col-md-6 col-editor">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Admin Email Settings <small class="float-end cursor expand-handle"><i class="dashicons dashicons-fullscreen-alt"></i></small><h6>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        if (wpcsc_fs()->is_not_paying()) {
            $html .= '<section class="wpcsc-is__premium"><h6>' . __('Awesome Premium Features', WPCSC_TXT_DOMAIN);
            $html .= ' <a href="' . wpcsc_fs()->get_upgrade_url() . '">' . __('Upgrade Now!', WPCSC_TXT_DOMAIN) . '</a></h6>';
            $html .= '</section>';
        }

        $is_premium = wpcsc_fs()->is__premium_only();
        $fieldset_disabled = $is_premium ? '' : 'disabled';
        $fieldset_disabled = '';
        // $fieldset_disabled = 'disabled';

        $html .= '<form class="form-ajax" data-action="action_update_settings" data-task="admin_email">';
        $html .= '<fieldset ' . $fieldset_disabled . '>';
        $html .= ' <div class="row form-group p-2">';
        $html .= '<label class="col col-md-3" for="wpcsc_send_mail">Send Email</label>';
        $html .= '<div class="col-8 col-md-9 form-switch" style="padding-left:calc(var(--bs-gutter-x) * .5)">';
        $checked = (is_array($email_settings) && $email_settings['enable'] == 'on') ? ' checked' : '';
        $html .= '<input class="form-check-input" type="checkbox" role="switch" name="fields[enable]" id="wpcsc_send_mail"' . $checked . '>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="wpcsc_recipient">Recipient</label>';
        $html .= ' <div class="col-md-9">';
        $html .= ' <input type="text" class="form-control" name="fields[receiver]" id="" value="' . $email_settings['receiver'] . '" required>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="wpcsc_subject">Subject</label>';
        $html .= ' <div class="col-md-9">';
        $html .= ' <input type="text" class="form-control" name="fields[subject]" id="" value="' . $email_settings['subject'] . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="row form-group p-2">';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<input type="hidden" name="wp_nonce" value="' . wp_create_nonce() . '">';

        $html .= '<button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="padding-top:3px"></i> Submit</button>';

        $html .= '</div>';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<div class="ajax-result"></div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= '</div>';

        $html .= '<span class="btn btn-link text-danger small btn-ajaxy wpcsc-reset-default" data-op="reset_default" data-handle="wpcsc" data-option="admin_email" style="padding-left:10px;"> Reset Defaults</span>';
        $html .= '</div>';
        $html .= '</div>';


        $html .= '<div class="col-md-6 col-preview">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Email Preview [Demo]<h6>';
        $html .= '</div>';
        $html .= '<div class="card-body wpcsc-email-preview">';

        $html .= '<div class="wpcsc-email-default">';
        $html .= '<style>.wpcsc-footer{position:unset!important;}</style>';
        $html .= $email_settings['body'];
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function pdf_settings()
    {
        $pdf_settings = get_option('wpcsc__pdf', []);
        $settings = get_option('wpcsc__settings', []);

        $defaults = [
            'site_url' => site_url(),
            'site_title' => get_bloginfo('name'),
            'current_time' => current_time('l, M d, Y  H:i a'),
            'logo_path' => $settings['logo_path']
        ];

        foreach ($defaults as $arg => $val) {
            $pdf_settings['body'] = str_replace("{{" . $arg . "}}", ((!empty($val) || $val == 0) ? $val : "-"), $pdf_settings['body']);
        }

        $html = '<div class="col-md-6 col-editor">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>PDF Export Settings <small class="float-end cursor expand-handle"><i class="dashicons dashicons-fullscreen-alt"></i></small><h6>';
        $html .= '</div>';

        $html .= '<div class="card-body">';
        if (wpcsc_fs()->is_not_paying()) {
            $html .= '<section class="wpcsc-is__premium"><h6>' . __('Awesome Premium Features', WPCSC_TXT_DOMAIN);
            $html .= ' <a href="' . wpcsc_fs()->get_upgrade_url() . '">' . __('Buy Now!', WPCSC_TXT_DOMAIN) . '</a></h6>';
            $html .= '</section>';
        }

        $is_premium = wpcsc_fs()->is__premium_only();
        $fieldset_disabled = $is_premium ? '' : 'disabled';
        $fieldset_disabled = '';

        $html .= '<form class="form-ajax" data-action="action_update_settings" data-task="pdf">';
        $html .= '<fieldset ' . $fieldset_disabled . '>';
        $html .= ' <div class="row form-group p-2">';
        $html .= '<label class="col col-md-3" for="wpcsc_send_mail">Export PDF</label>';
        $html .= '<div class="col-8 col-md-9 form-switch" style="padding-left:calc(var(--bs-gutter-x) * .5)">';
        $checked = (is_array($pdf_settings) && $pdf_settings['enable'] == 'on') ? ' checked' : '';
        $html .= '<input class="form-check-input" type="checkbox" role="switch" name="fields[enable]" id="wpcsc_export_pdf"' . $checked . '>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="wpcsc_recipient">File Name Prefix</label>';
        $html .= ' <div class="col-md-9">';
        $html .= ' <input type="text" class="form-control" name="fields[file_name_prefix]" id="" value="' . $pdf_settings['file_name_prefix'] . '">';
        $html .= '</div>';
        $html .= '</div>';

        if ($is_premium) {
            $html .= ' <div class="row form-group p-2">';
            $html .= ' <label class="col-md-3" for="wpcsc_mail_body">Body</label>';
            $html .= ' <div class="col-md-9">';
            // ToDo: Link with HTML Preview - CAN BE DONE AT LAST
            ob_start();
            $editor_id = 'wpcsc_mail_body';
            wp_editor($pdf_settings['body'], $editor_id, [
                'textarea_name' => 'fields[body]',
            ]);
            $html .= ob_get_clean();
            // $html .= ' <textarea type="text" class="form-control" name="wpcsc_mail_body" id="wpcsc_mail_body">'.$pdf_settings['wpcsc_mail_body'].'</textarea>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '<div class="row form-group p-2">';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<input type="hidden" name="wp_nonce" value="' . wp_create_nonce() . '">';
        $html .= '<button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="padding-top:3px"></i> Submit</button>';
        $html .= '</div>';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<div class="ajax-result"></div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= '</div>';

        $html .= '<span class="btn btn-link text-danger small btn-ajaxy wpcsc-reset-default" data-op="reset_default" data-handle="wpcsc" data-option="pdf" style="padding-left:10px;"> Reset Defaults</span>';
        $html .= '</div>';
        $html .= '</div>';


        $html .= '<div class="col-md-6 col-preview">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Email Preview [Demo]<h6>';
        $html .= '</div>';
        $html .= '<div class="card-body wpcsc-email-preview">';
        $html .= '<div class="wpcsc-email-default">';
        $html .= '<style>.wpcsc-footer{position:unset!important;}</style>';
        $html .= $pdf_settings['body'];
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function main_settings()
    {
        $settings = get_option('wpcsc__settings', []);

        $html = '<div class="col-md-8 col-editor">';
            $html .= '<div class="wpcsc-card card">';
                $html .= '<div class="card-header">';
                    $html .= '<h6>Settings<h6>';
                $html .= '</div>';

                $html .= '<div class="card-body">';
                    $html .= '<form class="form-ajax" data-action="action_update_settings" data-task="pdf">';
                        $html .= '<fieldset>';
                            $html .= ' <div class="row form-group p-2">';
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
                                    $html .= '<button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="padding-top:3px"></i> Submit</button>';
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
                    $html .= 'Copy shortcode <code>[WPCSC]</code> and then paste it in the page or section where you want to display and save it.<br>You are good to go.';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public static function get_custom_logo_url()
    {
        $custom_logo_id = get_theme_mod('custom_logo');
        $image = wp_get_attachment_image_src($custom_logo_id, 'full');
        return $image[0];
    }

}