<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Custom_fields_categories_model extends App_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single custom field category
     */
    public function get($id = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix().'customfields_categories')->row();
        }

        return $this->db->get(db_prefix().'customfields_categories')->result_array();
    }

    /**
     * Add new custom field
     * @param mixed $data All $_POST data
     * @return  boolean
     */
    public function add($data)
    {
        $this->db->insert(db_prefix().'customfields_categories', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Custom Field Category Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update custom field
     * @param mixed $data All $_POST data
     * @return  boolean
     */
    public function update($data, $id)
    {
        $original_field = $this->get($id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix().'customfields_categories', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Custom Field Updated [' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer
     * @return boolean
     * Delete Custom fields
     * All values for this custom field will be deleted from database
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'customfields_categories');

        return false;
    }
}
