<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Range Class
 *
 * @author    Eli Van Zoeren <eli@elivz.com>
 * @copyright Copyright (c) 2012 Eli Van Zoeren
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

class Vz_range_ft extends EE_Fieldtype {

    public $info = array(
        'name'      => 'VZ Range',
        'version'   => '1.0.1',
    );

    /**
     * Fieldtype Constructor
     */
    function Vz_range_ft()
    {
        parent::EE_Fieldtype();

        if (!isset($this->EE->session->cache['vz_range']))
        {
            $this->EE->session->cache['vz_range'] = array('css' => FALSE, 'countries' => array());
        }
        $this->cache =& $this->EE->session->cache['vz_range'];
    }

    /**
     * Include the CSS styles, but only once
     */
    private function _include_css()
    {
        if ( !$this->cache['css'] )
        {
            $this->EE->cp->add_to_head('<style type="text/css">
    .vz_range { padding-bottom:0.5em; }
    .vz_range input { width:97%; padding:4px; }
    .vz_range_min { float:left; width:46%; }
    .vz_range_max { float:right; width:46%; }
    .vz_range_sep { float:left; width:8%; text-align:center; line-height:2; }
</style>');

            $this->cache['css'] = TRUE;
        }
    }


    // --------------------------------------------------------------------


    /**
     * Field settings UI
     */
    function display_settings($settings)
    {
        $this->EE->load->library('table');
        $this->EE->lang->loadfile('vz_range');

        $precision = !empty($settings['precision']) ? $settings['precision'] : '0';
        $this->EE->table->add_row(array(
            '<strong>'.lang('precision').'</strong>',
            form_input('vz_range_precision', $precision, 'id="vz_range_precision"')
        ));
    }

    /**
     * Save Field Settings
     */
    function save_settings()
    {
        return array(
            'precision' => $this->EE->input->post('vz_range_precision')
        );
    }

    /**
     * Cell settings UI
     */
    function display_cell_settings($settings)
    {
        $this->EE->lang->loadfile('vz_range');

        $precision = isset($settings['precision']) ? $settings['precision'] : '0';
        return array(
            array(lang('precision'), form_input('vz_range_precision', $precision, 'id="vz_range_precision" style="width: 3em;"'))
        );
    }


    // --------------------------------------------------------------------


    /**
     * Generate the publish page UI
     */
    private function _range_form($name, $data, $is_cell=FALSE)
    {
        $this->EE->load->helper('form');
        $this->EE->lang->loadfile('vz_range');
        $this->_include_css();

        // Set default values
        $data = is_string($data) ? $data : '';
        list($range['min'], $range['max']) = explode(' - ', $data) + Array(null, null);

        // Generate fields markup
        $form = '';
        $form .= '<div class="vz_range vz_range_min">';
        $form .= form_input($name.'[min]', $range['min'], 'id="'.$name.'_min" class="vz_range_min"');
        $form .= '</div>';
        $form .= '<div class="vz_range_sep">' . lang('to') . '</div>';
        $form .= '<div class="vz_range vz_range_max">';
        $form .= form_input($name.'[max]', $range['max'], 'id="'.$name.'_max" class="vz_range_max"');
        $form .= '</div>';

        return $form;
    }

    /**
     * Display Field
     */
    function display_field($field_data)
    {
        return $this->_range_form($this->field_name, $field_data);
    }

    /**
     * Display Cell
     */
    function display_cell($cell_data)
    {
        return $this->_range_form($this->cell_name, $cell_data, TRUE);
    }


    // --------------------------------------------------------------------


    /**
     * Save Field
     */
    function save($data)
    {
        $precision = (int) $this->settings['precision'];
        return number_format($data['min'], $precision, '.', '') . ' - ' . number_format($data['max'], $precision, '.', '');
    }

    /**
     * Save Cell
     */
    function save_cell($data)
    {
        return $this->save($data);
    }


    // --------------------------------------------------------------------


    /**
     * Unserialize the data
     */
    function pre_process($data)
    {
        list($range['min'], $range['max']) = explode(' - ', $data);
        return $range;
    }

    /**
     * Display Tag
     */
    function replace_tag($range, $params=array())
    {
        // Get parameters
        $precision = isset($params['precision']) ? $params['precision'] : $this->settings['precision'];
        $separator = isset($params['separator']) ? $params['separator'] : '-';
        $steps = isset($params['steps']) && $params['steps'] == 'yes';
        $reverse = isset($params['reverse']) && $params['reverse'] == 'yes';

        if ($reverse)
        {
            // Switch the limits
            $range = array('min'=>$range['max'], 'max'=>$range['min']);
        }

        if ($steps)
        {
            // Output every step along the way
            if ($precision > 0)
            {
                $step = 1 / pow(10, $precision);
            }
            else
            {
                $step = 1;
            }

            $stepped = range($range['min'], $range['max'], $step);
            $output = implode($separator, $stepped);
        }
        else
        {
            // Output just the limits
            $output = $range['min'] . $separator . $range['max'];
        }

        return $output;
    }

    /*
     * Individual range pieces
     */
    function replace_min($range, $params=array(), $tagdata=FALSE)
    {
        return $range['min'];
    }
    function replace_max($range, $params=array(), $tagdata=FALSE)
    {
        return $range['max'];
    }
}

/* End of file ft.vz_range.php */