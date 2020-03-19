<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Custom_fields_categories extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('custom_fields_categories_model');
        if (!is_admin()) {
            access_denied('Access Custom Fields Categories');
        }
    }

    /* List all custom fields categories*/
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('customfields_categories');
        }
        $data['title'] = _l('custom_fields_categories');
        $this->load->view('admin/custom_fields_categories/manage', $data);
    }

    public function field($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->custom_fields_categories_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('custom_field_category')));
                    redirect(admin_url('custom_fields_categories/field/' . $id));
                }
            } else {
                $success = $this->custom_fields_categories_model->update($this->input->post(), $id);
                if (is_array($success) && isset($success['cant_change_option_custom_fields_category'])) {
                    set_alert('warning', _l('cf_option_in_use'));
                } elseif ($success === true) {
                    set_alert('success', _l('updated_successfully', _l('custom_field_category')));
                }
                redirect(admin_url('custom_fields_categories/field/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('custom_field_category_lowercase'));
        } else {
            $data['custom_field_category'] = $this->custom_fields_categories_model->get($id);
            $title                = _l('edit', _l('custom_field_category_lowercase'));
        }

        $data['title']                  = $title;
        $this->load->view('admin/custom_fields_categories/customfield_category', $data);
    }

    /* Delete announcement from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('custom_fields_categories'));
        }
        $response = $this->custom_fields_categories_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('custom_field_categories')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('custom_field_category_lowercase')));
        }
        redirect(admin_url('custom_fields_categories'));
    }

    /* Change custom field status active or inactive
    public function change_custom_field_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->custom_fields_model->change_custom_field_status($id, $status);
        }
    } */
}


/* Tem minor edit */
