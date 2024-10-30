<?php

if (!class_exists('WPCSC_INCOMES')) {

    class WPCSC_INCOMES extends WPCSC_ENTITY
    {

        use IncomesDatatables;

        public function __construct()
        {
            $this->table_name = 'wpcsc_incomes';
            $this->col_validation = [
                // 'mail_name' => 'required|string',
            ];
            $this->bulk_action_buttons = [];
            $this->export_columns = $this->columns();
        }
    }
}

trait IncomesDatatables
{

    public function get_columns_titles($exclude_keys = [])
    {
        return [
            'S.No' => ['col' => 'S.No', 'width' => ''],
            'salary' => __("Salary", WPCSC_TXT_DOMAIN),
            'one' => __("One Child", WPCSC_TXT_DOMAIN),
            'two' => __("Two", WPCSC_TXT_DOMAIN),
            'three' => __("Three", WPCSC_TXT_DOMAIN),
            'four' => __("Four", WPCSC_TXT_DOMAIN),
            'five' => __("Five", WPCSC_TXT_DOMAIN),
            'six' => __("Six", WPCSC_TXT_DOMAIN),
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
            'start' => 500,
            'view_key' => $this->table_name,
            'action' => 'universal',
            'table_name' => $this->table_name,
        ];
    }

    public function get_column_html($log, $col)
    {
        $col_data = array();

        switch ($col) {
            default:
                $col_data = parent::get_column_html($log, $col);
        }

        return $col_data;
    }

    public function get_filters_html($params = [])
    {
        foreach ($params as $key => $value) {
            $params[sanitize_text_field($key)] = sanitize_text_field($value);
        }
        ob_start();
        ?>
        <div class="wpcsc-dt-containers col-md-12 mb-2">
            <span class="wpcsc-dt-filter-handle text-danger cursor mb-2"><i class="dashicons dashicons-filter"></i> <i>Search Filters</i></span>
            <div class="wpcsc-dt-filters bg-light p-2">

                <div class="row">
                    <div class="col-md-2">
                        <input type="text" name="salary" class="form-control search_col_salary all-custom-filter" placeholder="Salary Slab" value="">
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
        return ob_get_clean();
    }

}