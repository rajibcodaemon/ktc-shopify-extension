<?php

/**
 * 
 */
class User_model extends CI_Model {

    public function get_user_details_by_id($user_id) {

        $this->db->where(array('user_id' => $user_id));
        return $this->db->get('user_details')->result_array();
    }

    public function update_user_details($user_id, $user_data) {

        $this->db->where('user_id', $user_id);
        $this->db->update('user_details', $user_data);

        return 1;
    }

    public function check_user_exist($access_data) {
        //$this->db->where('user_id', $access_data['associated_user']['id']);
        $this->db->where('user_id', $access_data['id']);
        return $this->db->get('user_details')->row();
    }

    public function save_user_details($user_data) {
        $this->db->insert('user_details', $user_data);
        return 1;
    }

    public function get_all_users() {        
        $this->db->order_by('id', 'DESC');
        $this->db->group_by('email');
        return $this->db->get('user_details')->result_array();
    }

}

?>