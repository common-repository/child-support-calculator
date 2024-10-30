<?php

class WPCSC_CALC
{

    use CALCActions, CALCHtml;

    public function __contruct()
    {
        // add_shortcode('WPCSC', array($this, 'display_calc'));
    }

    public function get_settings_class_object($state = "")
    {
        return wpcsc_get_class_object($this->get_settings_class_name($state));
    }

    public function get_settings_class_name($state = "")
    {
        return ($state == '' || $state == 'global') ? 'WPCSC_SETTINGS' : strtoupper('WPCSC_SETTINGS_' . $state);
    }

    public function display_calc($atts, $content = NULL)
    {

        extract(shortcode_atts(array(
            'tabs' => true,
            'state' => ''
        ), $atts));

        if ($state == '' || $state == 'global') {
            // pass the attributes to create_calc functions and render the display
            return $this->global_calc();
        } else {
            // return state specific calculator
            $method = 'generate_calc_' . strtolower($state) . '_html';
            return $this->$method();
        }
    }

    /**
     * @param $post
     * @return mixed|string
     * Return $post array after validation
     * or, a string error if invalid values found
     */
    public function validate_calc_args($post)
    {
        foreach ($post as $key => $val) {
            switch ($key) {
                case 'no_of_children':
                    if (is_numeric($val)) {
                        $val = $this->get_word_from_number($val);

                        if ($val === false)
                            return "Number of children should be in between 1 to 6";

                        $post[$key] = $val;
                    }
                    break;
                case 'over_nights':
                    if (!is_array($val)) {
                        return "Invalid over nights value: " . $val;
                    }
                    break;
                default:
                    if (strpos($val, ",") !== false) {
                        $post[$key] = str_replace(",", "", $val);
                    }
            }
        }

        return $post;
    }
}

trait CALCHtml
{

    public function global_calc()
    {
        $form_settings = get_option('wpcsc__form', []);
        $select_styles = !empty($form_settings['select_field_styles']) ? ' style="' . $form_settings['select_field_styles'] . '"' : '';
        $input_styles = !empty($form_settings['input_field_styles']) ? $form_settings['input_field_styles'] : '';
        ob_start();

        $values = [
            'no_of_children' => WPCSC_DEV_MODE ? "one" : '',
            'your_income' => WPCSC_DEV_MODE ? 2500 : '',
            'spouse_income' => WPCSC_DEV_MODE ? 5000 : '',
            'your_overnights' => WPCSC_DEV_MODE ? 25 : '',
        ]
        ?>
        <style>.wpcsc-form-group input {
            <?php _e($input_styles, WPCSC_TXT_DOMAIN); ?>
            }</style>
        <div class="wpcsc-row wpcsc" style="row-gap: 0px;">
            <div class="wpcsc-col wpcsc-col-12 text-center"><?php _e($form_settings['form_title'], WPCSC_TXT_DOMAIN); ?></div>
            <div class="wpcsc-divider"></div>
            <div class="wpcsc-col wpcsc-col-12 text-muted text-center small"><?php _e($form_settings['form_text'], WPCSC_TXT_DOMAIN); ?></div>
            <form class="wpcsc-form" style="margin-top:1rem">
                <div class="wpcsc-row">
                    <div class="wpcsc-col-12">
                        <div class="wpcsc-form-group">
                            <label for="no_of_children">How many children do you have from this marriage?</label>
                            <select name="no_of_children"
                                    class="wpcsc-form-select no_of_children"<?php echo __($select_styles, WPCSC_TXT_DOMAIN); ?>
                                    required>
                                <?php echo wpcsc_create_options_from_array(wpcsc_get_globals("one_to_six", "Number of Children"), $values['no_of_children'], true); ?>
                            </select>
                        </div>
                    </div>
                    <div class="wpcsc-col-6 wpcsc-col-sm-12">
                        <div class="wpcsc-form-group" style="margin-right:3px">
                            <label for="your_income">Your Monthly Income?</label>
                            <input type="number" name="your_income" class="wpcsc-form-control your_income"
                                   placeholder="Enter Your Income"
                                   value="<?php echo esc_attr($values['your_income']); ?>" required>
                        </div>
                    </div>
                    <div class="wpcsc-col-6 wpcsc-col-sm-12">
                        <div class="wpcsc-form-group" style="margin-left:3px">
                            <label for="spouse_income">Income of other parent?</label>
                            <input type="number" name="spouse_income" class="wpcsc-form-control spouse_income"
                                   placeholder="Enter Spouse Income"
                                   value="<?php echo esc_attr($values['spouse_income']); ?>" required>
                        </div>
                    </div>
                    <div class="wpcsc-col-12">
                        <div class="wpcsc-form-group">
                            <label for="your_overnights">How much time do you spend?</label>
                            <div class="wpcsc-input-group">
                                <input type="number" max="100" name="your_overnights"
                                       class="wpcsc-form-control your_overnights" placeholder="Time Spent (in %)"
                                       value="<?php echo esc_attr($values['your_overnights']); ?>" required>
                                <div class="wpcsc-input-right">%</div>
                            </div>
                        </div>
                    </div>

                    <div class="wpcsc-col-12 text-end" style="margin-top:5px">
                        <button type="submit" class="wpcsc-btn wpcsc-start">Calculate</button>
                    </div>
                </div>
            </form>
            <div class="result-area wpcsc-col"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * @return false|string
     * NC -> North Carolina
     */
    public function generate_calc_nc_html()
    {
        $form_settings = get_option('wpcsc__form-nc', []);
        $mandate_zero = $form_settings['mandate_zero'] == 'on' ? '' : 'disabled-zeros' ;

        ob_start(); ?>

        <div class="wpcsc-row wpcsc-calc">

            <h3 class="wpcsc-calc-title">North Carolina Child Support Obligation Calculator</h3>

            <form class="wpcsc-nc-form form-ajax <?php echo $mandate_zero; ?>" id="wpcsc-nc-form" data-action="wpcsc_action_cal_child_support">
                <div class="wpcsc-row csc-form-rows">

                    <!-- Number of Child(ren) -->
                    <fieldset class="wpcsc-tab">
                        <?php if (!empty($form_settings['form_title'])) { ?>
                            <div class="wpcsc-col wpcsc-form-title"><?php _e($form_settings['form_title'], WPCSC_TXT_DOMAIN); ?></div>
                        <?php } ?>

                        <?php if (!empty($form_settings['form_text'])) { ?>
                            <div class="wpcsc-col wpcsc-form-text"><?php _e($form_settings['form_text'], WPCSC_TXT_DOMAIN); ?></div>
                        <?php } ?>

                        <div class="wpcsc-fieldset-heading">Information About Your Children</div>

                        <div class="wpcsc-form-group">
                            <label class="wpcsc-question-label"
                                   for="no_of_children"><?php _e($form_settings['no_of_children'], WPCSC_TXT_DOMAIN); ?></label>
                            <select name="no_of_children" class="wpcsc-form-select no_of_children" id="no_of_children"
                                    required>
                                <?php echo wpcsc_create_options_from_array(wpcsc_get_globals("one_to_six", "Number of Children"), ''); ?>
                            </select>
                            <span class="wpcsc-error-msg"></span>
                        </div>
                    </fieldset>

                    <!-- Overnights -->
                    <fieldset class="wpcsc-tab wpcsc-on">
                        <div class="wpcsc-fieldset-heading">Information About Your Children</div>
                        <label class="wpcsc-question-label"
                               for="over_nights"><?php _e($form_settings['over_nights'], WPCSC_TXT_DOMAIN); ?></label>
                        <div class="csc-over-nights"></div>
                    </fieldset>

                    <!-- Gross Income -->
                    <fieldset class="wpcsc-tab">
                        <div class="wpcsc-fieldset-heading">Income Information</div>
                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group">
                            <label class="wpcsc-question-label"
                                   for="gross_income"><?php _e($form_settings['gross_income'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control gross_income" name="gross_income"
                                   placeholder="Enter value, if not applicable enter 0" id="gross_income" value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group">
                            <label class="wpcsc-question-label"
                                   for="other_gross_income"><?php _e($form_settings['other_gross_income'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control other_gross_income" name="other_gross_income"
                                   placeholder="Enter value, if not applicable enter 0" id="other_gross_income"
                                   value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                    </fieldset>

                    <!-- Child Support -->
                    <fieldset class="wpcsc-tab">
                        <div class="wpcsc-fieldset-heading">Child Support for Other Children</div>
                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group">
                            <label class="wpcsc-question-label"
                                   for="child_support"><?php _e($form_settings['child_support'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control child_support" name="child_support"
                                   placeholder="Enter value, if not applicable enter 0" id="child_support" value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group">
                            <label class="wpcsc-question-label"
                                   for="other_child_support"><?php _e($form_settings['other_child_support'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control other_child_support"
                                   name="other_child_support" placeholder="Enter value, if not applicable enter 0"
                                   id="other_child_support" value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                    </fieldset>

                    <!-- Prev Relations Biological Child(ren) -->
                    <fieldset class="wpcsc-tab">
                        <div class="wpcsc-fieldset-heading">Information Related to Other Child(ren)</div>
                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group">
                            <label class="wpcsc-question-label"
                                   for="prev_rel_children"><?php _e($form_settings['prev_rel_children'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control prev_rel_children" name="prev_rel_children"
                                   placeholder="Enter value, if not applicable enter 0" id="prev_rel_children"
                                   value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group">
                            <label class="wpcsc-question-label"
                                   for="other_prev_rel_children"><?php _e($form_settings['other_prev_rel_children'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control other_prev_rel_children"
                                   name="other_prev_rel_children" placeholder="Enter value, if not applicable enter 0"
                                   id="other_prev_rel_children" value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                    </fieldset>

                    <!-- Child Related Expenses Information -->
                    <fieldset class="wpcsc-tab">
                        <div class="wpcsc-fieldset-heading">Work Related Childcare Expenses</div>

                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group csc-over-nights">
                            <label class="wpcsc-question-label"
                                   for="child_care"><?php _e($form_settings['child_care'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control child_care" name="child_care"
                                   placeholder="Enter value, if not applicable enter 0" id="child_care" value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group csc-over-nights">
                            <label class="wpcsc-question-label"
                                   for="other_child_care"><?php _e($form_settings['other_child_care'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control other_child_care" name="other_child_care"
                                   placeholder="Enter value, if not applicable enter 0" id="other_child_care"
                                   value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                    </fieldset>

                    <!-- Child Related Expenses Information -->
                    <fieldset class="wpcsc-tab">
                        <div class="wpcsc-fieldset-heading">Health Insurance Information</div>

                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group csc-over-nights">
                            <label class="wpcsc-question-label"
                                   for="child_insurance"><?php _e($form_settings['child_insurance'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control child_insurance" name="child_insurance"
                                   placeholder="Enter value, if not applicable enter 0" id="child_insurance" value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group csc-over-nights">
                            <label class="wpcsc-question-label"
                                   for="other_child_insurance"><?php _e($form_settings['other_child_insurance'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control other_child_insurance"
                                   name="other_child_insurance" placeholder="Enter value, if not applicable enter 0"
                                   id="other_child_insurance" value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                    </fieldset>

                    <!-- Child Related Expenses Information -->
                    <fieldset class="wpcsc-tab">
                        <div class="wpcsc-fieldset-heading">Extraordinary Expenses</div>

                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group csc-over-nights">
                            <label class="wpcsc-question-label"
                                   for="child_extraordinary"><?php _e($form_settings['child_extraordinary'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control child_extraordinary"
                                   name="child_extraordinary" placeholder="Enter value, if not applicable enter 0"
                                   id="child_extraordinary" v value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                        <div class="wpcsc-col-12 wpcsc-col-sm-12 wpcsc-form-group csc-over-nights">
                            <label class="wpcsc-question-label"
                                   for="other_child_extraordinary"><?php _e($form_settings['other_child_extraordinary'], WPCSC_TXT_DOMAIN); ?></label>
                            <input type="number" class="wpcsc-form-control other_child_extraordinary"
                                   name="other_child_extraordinary"
                                   placeholder="Enter value, if not applicable enter 0" id="other_child_extraordinary"
                                   value="">
                            <span class="wpcsc-error-msg"></span>
                        </div>
                    </fieldset>

                    <!-- Form Navigation Buttons -->
                    <div class="wpcsc-col-12 wpcsc-form-navigation">
                        <span class="wpcsc-prev-btn">
                            <button class="wpcsc-btn wpcsc-start" type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                        </span>
                        <span class="wpcsc-nxt-btn">
                            <button class="wpcsc-btn wpcsc-start" type="button" id="nextBtn"
                                    onclick="nextPrev(1)">Next</button>
                        </span>
                        <input type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce() ?>">
                        <input type="hidden" name="handle" value="calc">
                        <input type="hidden" name="task" value="NC">
                        <?php if (isset($_GET['debug'])) { ?>
                            <input type="hidden" name="debug" value="on">
                        <?php } ?>
                    </div>

                    <!-- Circles which indicates the steps of the form: -->
                    <div class="wpcsc-col-12 wpcsc-tcenter wpcsc-navigation-bullets">
                        <span class="wpcsc-step"></span>
                        <span class="wpcsc-step"></span>
                        <span class="wpcsc-step"></span>
                        <span class="wpcsc-step"></span>
                        <span class="wpcsc-step"></span>
                        <span class="wpcsc-step"></span>
                        <span class="wpcsc-step"></span>
                        <span class="wpcsc-step"></span>
                        <div class="wpcsc-disclaimer"><?php _e($form_settings['form_disclaimer'], WPCSC_TXT_DOMAIN); ?></div>
                    </div>
                </div>
            </form>

            <div class="wpcsc-col-12">
                <div class="result-area"></div>
            </div>
        </div>

        <script>
            var currentTab = 0; // Current tab is set to be the first tab (0)
            showTab(currentTab); // Display the current tab
        </script>

        <?php
        return ob_get_clean();
    }

    public function get_export_form($post, $enable_leads=true)
    {
        $form_settings = get_option('wpcsc__form-nc', []);

        $pay_or_receive = $post['child_support_order'] ? 'Pay' : 'Receive';
        $compensation = max($post['child_support_order'], $post['other_child_support_order']);
        $over_nights = is_array($post['over_nights']) ? array_sum($post['over_nights']) : $post['over_nights'];
        $download_button_class = $enable_leads ? 'wpcsc-export' : 'wpcsc-export-directly';
        $action = $enable_leads ? 'wpcsc_export_result' : 'wpcsc_export_result_only_pdf';

        ob_start(); ?>
        <div class="wpcsc-box wpcsc-export-box wpcsc-row">
            <div class="wpcsc-col-8">
                <?php if ($post['worksheet'] == "a") { ?>
                    <span class="wpcsc-compensation d-block">The estimated child support is $<?php echo number_format(round($post['other_child_support_order'], 2), 2); ?>.</span>
                <?php } else { ?>
                    <span class="wpcsc-compensation d-block">The estimated child support is $<?php echo number_format(round($compensation, 2), 2); ?>.</span>
                <?php } ?>
            </div>
            <div class="wpcsc-col-4 text-end">
                <button class="wpcsc-btn <?php echo $download_button_class; ?>" type="button">Download PDF</button>
                <div class="calculate-again">
                    <small><a href="<?php echo get_permalink(get_the_ID()); ?>">Calculate Again</a></small>
                </div>
            </div>
            <div class="wpcsc-col-12 wpcsc-export-area" style="display: none">
                <div class="wpcsc-divider"></div>

                <h5><?php echo $form_settings['export_form_title']; ?></h5>
                <span class="text-muted"><?php echo $form_settings['export_form_text']; ?></span>

                <form class="wpcsc-export-form">
                    <?php if($enable_leads) { ?>
                        <div class="wpcsc-form-group wpcsc-row">
                            <label for="your_name" class="wpcsc-col-sm-12 wpcsc-col-4 wpcsc-export-form-label">Your Name<i
                                        style="color:red">*</i></label>
                            <div class="wpcsc-col-sm-12 wpcsc-col-8">
                                <input type="text" name="your_name" class="wpcsc-col-8 wpcsc-form-control" required>
                            </div>
                        </div>
                        <div class="wpcsc-form-group wpcsc-row">
                            <label for="your_email" class="wpcsc-col-sm-12 wpcsc-col-4 wpcsc-export-form-label">Your Email<i
                                        style="color:red">*</i></label>
                            <div class="wpcsc-col-sm-12 wpcsc-col-8">
                                <input type="email" name="your_email" class="wpcsc-form-control" required>
                            </div>
                        </div>
                        <div class="wpcsc-form-group wpcsc-row">
                            <label for="spouse_name" class="wpcsc-col-sm-12 wpcsc-col-4 wpcsc-export-form-label">Spouse
                                Name</label>
                            <div class="wpcsc-col-sm-12 wpcsc-col-8">
                                <input type="text" name="spouse_name" class="wpcsc-form-control">
                            </div>
                        </div>
                        <div class="wpcsc-form-group wpcsc-row">
                            <label for="spouse_email" class="wpcsc-col-sm-12 wpcsc-col-4 wpcsc-export-form-label">Spouse
                                Email</label>
                            <div class="wpcsc-col-sm-12 wpcsc-col-8">
                                <input type="email" name="spouse_email" class="wpcsc-form-control">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="wpcsc-form-group wpcsc-row">
                        <div class="wpcsc-col-sm-12 wpcsc-col-12 text-end">
                            <input type="hidden" name="pay_or_receive" value="<?php echo $pay_or_receive; ?>"
                                   class="wpcsc-form-control">
                            <input type="hidden" name="children"
                                   value="<?php echo $this->get_numeric_value($post['no_of_children']); ?>"
                                   class="wpcsc-form-control">
                            <input type="hidden" name="your_income" value="<?php echo $post['gross_income']; ?>"
                                   class="wpcsc-form-control">
                            <input type="hidden" name="spouse_income" value="<?php echo $post['other_gross_income']; ?>"
                                   class="wpcsc-form-control">
                            <input type="hidden" name="over_nights" value="<?php echo $over_nights; ?>"
                                   class="wpcsc-form-control">
                            <input type="hidden" name="compensation" value="<?php echo round($compensation, 2); ?>"
                                   class="wpcsc-form-control">
                            <input type="hidden" name="liability"
                                   value="<?php echo round($post['adjusted_obligation'], 2); ?>"
                                   class="wpcsc-form-control">
                            <input type="hidden" name="spouse_liability"
                                   value="<?php echo round($post['other_adjusted_obligation'], 2); ?>"
                                   class="wpcsc-form-control">
                            <input type="hidden" name="worksheet" value="<?php echo $post['worksheet']; ?>"
                                   class="wpcsc-form-control">
                            <input type="hidden" name="action" value="<?php echo $action; ?>"
                                   class="wpcsc-form-control">
                            <?php
                            foreach ($post as $name => $value) {
                                if ($name == 'over_nights' || is_array($value)) continue;
                                echo '<input type="hidden" name="' . $name . '" value="' . $value . '" class="wpcsc-form-control">';
                            }
                            ?>
                            <button type="submit" class="wpcsc-btn"><i class="dashicons dashicons-update d-none"></i>
                                Export
                            </button>
                        </div>
                    </div>
                    <div class="wpcsc-form-group wpcsc-row">
                        <div class="wpcsc-col-12 ajax-result"></div>
                    </div>
                </form>
            </div>
        </div>
        <?php return ob_get_clean();
    }

}

trait CALCActions
{

    public function calculate($post)
    {
        $api_url = 'https://wpchildsupport.com/wp-json/wca/v1/hook/get/result';
        $args = [
            'sslverify' => false,
            'headers' => [
                'content-type' => 'application/json'
            ],
            'body' => json_encode([
                'referrer'=>'ijariit',
                'data' => $post
            ])
        ];

        $api_response = wp_remote_post($api_url, $args);
        if (is_wp_error($api_response)){
            return [
                'success' => false,
                'reason' => $api_response->get_error_messages(),
            ];
        }

        $status = wp_remote_retrieve_response_code($api_response);
        if ($status !== 200){
            return [
                'success' => false,
                'reason' => 'Wrong error code received.',
            ];
        }

        $post = json_decode( wp_remote_retrieve_body( $api_response ), true );
        $post = $post['data'];

        $settings_option = get_option('wpcsc__settings');
        $enable_leads = $settings_option['enable_leads'] == 'on';
        $enable_worksheet = $settings_option['enable_worksheet'] == 'on';

        // create class object of state class
        $state_obj = $this->get_settings_class_object($post['task']);

        // get worksheet Html
        $worksheet_html = $state_obj->get_worksheet_html($post['worksheet'], $post);

        // enable government standard worksheet results or not
        if($enable_worksheet != 'on'){
            // TODO : create beautiful result display html
            $worksheet_html = $state_obj->get_result_html($post);
        }

        $export_html = $this->get_export_form($post, $enable_leads);

        if ($post['worksheet'] == "a") {
            $post['child_support_order'] = 0;
        }

        $response['worksheet'] = $worksheet_html;
        $response['export'] = $export_html;
        $response['reason'] = $worksheet_html . $export_html;
        $response['success'] = true;

        return $response;
    }

    public function get_numeric_value($value)
    {
        if (is_numeric($value)) return $value;

        $array = [
            "one" => 1,
            "two" => 2,
            "three" => 3,
            "four" => 4,
            "five" => 5,
            "six" => 6,
        ];

        return $array[$value];
    }

    public function get_word_from_number($value)
    {
        $array = [
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
        ];

        return $array[$value] ?? false;
    }

    public function get_formatted_values($post, $excludes = [])
    {
        foreach ($post as $key => $val) {
            if (!in_array($key, $excludes)) {
                if (is_float($val)) {
                    $post[$key] = number_format(round($val, 2), 2);
                } else {
                    $post[$key] = number_format(floatval($val), 2);
                }
            }
        }
        return $post;
    }
}