<?php

/**
 * 
 */
class Order_model extends CI_Model {

    public function get_order_details_order_id($order) {
        return $this->db->get_where('order_details', array('order_id' => $order['id']))->row();
    }

    public function update_order_description($order) {
        $json_order = json_encode($order);

        $this->db->where('order_id', $order['id']);
        $this->db->update('order_description', array('description' => $json_order));

        return 1;
    }

    public function save_order_description($order) {
        $json_order = json_encode($order);

        $this->db->insert('order_description', array('order_id' => $order['id'], 'description' => $json_order));

        return 1;
    }

    public function save_order_details($order,$cust_details_id) {
        if(is_null($order['contact_email'])) {
            $order['contact_email'] = 'NA';
        }
        $order_data = array(
          'order_id' => $order['id'],
          'order_no' => $order['order_number'],
          'total_price' => $order['total_price'],
          'subtotal_price' => $order['subtotal_price'],
          'total_tax' => $order['total_tax'],
          'currency' => $order['currency'],
          'total_discounts' => $order['total_discounts'],
          'contact_email' => $order['contact_email'],
          'no_of_items' => count($order['line_items']),
          'cust_details_id' => $cust_details_id,
          'user_id' => $_SESSION['user_id'],
          'created_at' => $order['created_at'],
          'updated_at' => $order['updated_at'],
          'payment_status' => $order['financial_status'],
          'fullfillment_status' => $order['fulfillment_status'],
        );
        $this->db->insert('order_details', $order_data);

        return 1;
    }

    public function update_order_details($order) {
        $order_data = array(
          'total_price' => $order['total_price'],
          'subtotal_price' => $order['subtotal_price'],
          'total_tax' => $order['total_tax'],
          'currency' => $order['currency'],
          'total_discounts' => $order['total_discounts'],
          'contact_email'   => $order['contact_email'],
          'no_of_items' => count($order['line_items']),
          'updated_at' => $order['updated_at'],
          'payment_status' => $order['financial_status'],
          'fullfillment_status' => $order['fulfillment_status'],
        );
        $this->db->where('order_id', $order['id']);
        $this->db->update('order_details', $order_data);
        
        return 1;
    }

    public function save_order_customer_details($order) {
        
        $cust_detail_id = 0;
        
        //echo '<pre>';print_r($order);
        if (isset($order['shipping_address'])) {
            $this->db->where(array('cust_id' => $order['customer']['id'], 's_city' => $order['shipping_address']['city'], 's_zip' => $order['shipping_address']['zip']));
            $all_cust = $this->db->get('customer_details')->result_array();
            
            if (empty($all_cust)) {
                $cust_data = array(
                  'cust_id' => $order['customer']['id'],
                  'order_id' => $order['id'],
                  'email' => (isset($order['customer']['email'])?$order['customer']['email']:$order['email']),
                  's_first_name' => $order['shipping_address']['first_name'],
                  's_last_name' => $order['shipping_address']['last_name'],
                  's_address1' => $order['shipping_address']['address1'],
                  's_address2' => $order['shipping_address']['address2'],
                  's_phone' => $order['shipping_address']['phone'],
                  's_city' => $order['shipping_address']['city'],
                  's_zip' => $order['shipping_address']['zip'],
                  's_province' => $order['shipping_address']['province'],
                  's_country' => $order['shipping_address']['country'],
                );

                if (isset($order['billing_address'])) {
                    $cust_data['b_first_name'] = $order['billing_address']['first_name'];
                    $cust_data['b_last_name'] = $order['billing_address']['last_name'];
                    $cust_data['b_address1'] = $order['billing_address']['address1'];
                    $cust_data['b_address2'] = $order['billing_address']['address2'];
                    $cust_data['b_phone'] = $order['billing_address']['phone'];
                    $cust_data['b_city'] = $order['billing_address']['city'];
                    $cust_data['b_zip'] = $order['billing_address']['zip'];
                    $cust_data['b_province'] = $order['billing_address']['province'];
                    $cust_data['b_country'] = $order['billing_address']['country'];
                }

                //print_r($cust_data);die();
                if (isset($order['customer']['id'])){
                  $this->db->insert('customer_details', $cust_data);
                  $cust_detail_id = $this->db->insert_id();
                }
            }
            else {
                $cust_data = array(
                  's_first_name' => $order['shipping_address']['first_name'],
                  's_last_name' => $order['shipping_address']['last_name'],
                  's_address1' => $order['shipping_address']['address1'],
                  's_address2' => $order['shipping_address']['address2'],
                  's_phone' => $order['shipping_address']['phone'],
                  's_city' => $order['shipping_address']['city'],
                  's_zip' => $order['shipping_address']['zip'],
                  's_province' => $order['shipping_address']['province'],
                  's_country' => $order['shipping_address']['country'],
                );

                if (isset($order['billing_address'])) {
                    $cust_data['b_first_name'] = $order['billing_address']['first_name'];
                    $cust_data['b_last_name'] = $order['billing_address']['last_name'];
                    $cust_data['b_address1'] = $order['billing_address']['address1'];
                    $cust_data['b_address2'] = $order['billing_address']['address2'];
                    $cust_data['b_phone'] = $order['billing_address']['phone'];
                    $cust_data['b_city'] = $order['billing_address']['city'];
                    $cust_data['b_zip'] = $order['billing_address']['zip'];
                    $cust_data['b_province'] = $order['billing_address']['province'];
                    $cust_data['b_country'] = $order['billing_address']['country'];
                }

                $cust_detail_id = $all_cust[0]['id'];
                $this->db->where('id', $cust_detail_id);
                $this->db->update('customer_details', $cust_data);
            }
        }
        return $cust_detail_id;
    }
    
    public function get_all_orders_userid($userid) {
        $this->db->distinct();
        $this->db->where('user_id', $userid);
        $this->db->from('order_details');
        return $this->db->get()->result_array();
    }
    
    public function get_paginated_orders($userid,$paginate, $nav='') {
        $this->db->distinct();
        $this->db->select('od.order_id, od.order_no, od.created_at, od.payment_status, od.fullfillment_status, od.total_price,od.contact_email,'
                . 'cd.s_first_name, cd.s_last_name, cd.s_address1, cd.s_phone, cd.s_city, cd.s_province, cd.s_country, cd.s_zip,'
                . 'cd.b_first_name, cd.b_last_name, cd.b_address1,cd.b_phone, cd.b_city, cd.b_province, cd.b_country, cd.b_zip, ode.description');
        $this->db->from('order_details od');
        $this->db->join('order_description ode', 'ode.order_id = od.order_id', 'left');
        $this->db->join('customer_details cd', 'cd.id = od.cust_details_id', 'left');
        $this->db->where('od.user_id', $userid);
        $this->db->order_by('od.created_at', 'DESC');
        if($nav == '') {
            $this->db->limit(50, 0);
        } else {
            $this->db->limit(50, $paginate);
        }
        $orders = $this->db->get()->result_array();
        return $orders;        
    }
    
    public function get_order_byid($orderid) {
        $this->db->select('od.contact_email,cd.s_first_name, cd.s_last_name, cd.s_address1,cd.s_phone,cd.s_city, cd.s_zip,cd.s_country, cd.s_province,'
                . 'cd.b_first_name, cd.b_last_name, cd.b_address1,cd.b_phone, cd.b_city, cd.b_province, cd.b_country, cd.b_zip, ode.description');
        $this->db->from('order_details od');
        $this->db->join('order_description ode', 'ode.order_id = od.order_id');
        $this->db->join('customer_details cd', 'cd.id = od.cust_details_id');
        $this->db->where('od.order_id', $orderid);
        return $this->db->get()->result_array();
    }
}

?>