<?php
	class Maintenance_model extends CI_Model{

		public function get_user_details_by_id() {

	        //$this->db->where(array('user_id' => $user_id));
	        return $this->db->get('user_details')->result_array();
    	}

    	public function get_order_details_by_id() {

	        //$this->db->where(array('user_id' => $user_id));
	        return $this->db->get('order_details')->result_array();
    	}

    	public function get_store_info_by_domain($store_domain){
            $this->db->where('store_domain', $store_domain);
    		return $this->db->from('user_details')->get()->row_array();
    	}

    	public function delete_store_users($params=[]){
    		
    		$this->db->update('user_details', ['signup_flag' => 0], ['id' => $params['id']]);
    		return 1;
    	}

        public function delete_store_orders($params=[]){
            //get all orders from the store
            $orders = $this->get_all_store_orders($params['user_id']);

            if (!empty($orders)){

                foreach($orders as $row){
                    //delete all customer details for order
                    $this->delete_customer_details($row['order_id']);

                    //delete all order description for order
                    $this->delete_order_description($row['order_id']);

                    //delete the order    
                    $this->delete_order_details($row['order_id']);

                }
                
                return 1;
            }else{
                return 0;
            }

        }

        public function get_all_store_orders($store_id){
            $this->db->where(['user_id' => $store_id]);
            return $this->db->get('order_details')->result_array();   
        }

        public function delete_customer_details($order_id){
            $this->db->delete('customer_details', ['order_id' => $order_id]);
            return 1;
        }

        public function delete_order_description($order_id){
            $this->db->delete('order_description', ['order_id' => $order_id]);
            return 1;
        }

        public function delete_order_details($order_id){
            $this->db->delete('order_details', ['order_id' => $order_id]);
            return 1;
        }

	}
?>