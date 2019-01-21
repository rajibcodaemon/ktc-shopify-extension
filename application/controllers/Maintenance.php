<?php
	
	/**
	* Controller for maintenance of
	* shopify application
	***/
	class Maintenance extends CI_Controller{
		var $__store_list = [];

		public function __construct()
		{
			# code...
			parent::__construct();
			$this->load->helper('url');
			$this->load->helper('cookie');

        	$this->load->model('order_model', 'order');
        	$this->load->model('user_model', 'user');
			$this->load->model('Maintenance_model', 'maintain');

			$this->__store_list = [
									'shopify-pwd-test.myshopify.com',
									'ktc-store-pass-check2.myshopify.com',
									'ktc-test-v7.myshopify.com',
									'ktc-test-v5.myshopify.com',
									'ktc-test-v6.myshopify.com',
									'ktc-test-v4.myshopify.com',
									'ktc-test-v3.myshopify.com',
									'ktc-test-v2.myshopify.com',
									'test-ktc-app-store.myshopify.com',
									'ktc-shopify-error-check.myshopify.com',
									'ktc-test-store.myshopify.com',
									'testktc.myshopify.com',
									'ktctest.myshopify.com'
								];
		}

		public function index(){
			$users = $this->maintain->get_user_details_by_id();
			$orders = $this->maintain->get_order_details_by_id();

			echo '<pre>';
			print_r($users);
			print_r($orders);
			//show_404();
		}

		public function check_cookie_set(){
			
			//$this->load->helper('cookie');
            $cookie = array(
			        'name'   => '_beenverified3_session',
			        'value'  => 'TVFmMjIrM0twYkVnTmlXVzZoL0dxMHoxYWY2Syt5Zmdxby9LMEZUczRtQ2xLS1Avd2pOeXk4U3pVWHEyeU41UnNlamJlUHFmQVZ4TXhza283WmZNWVFJUXVWR1NVa3JhM0xhVURrTzR4eStvN0JxMnRmYi9DeDkwUnFXL1hDbi9qWDhlVnBwWXZTbGUvWHZUQmFKWDZVUG9UM3pnczBTc3hrdGIwbitOdUtuNW81ejhRcjJIOUQ5YU5LS0gwQ3FiYm01NllBcG1Yc3pGTzJWTmNJRXM1dz09LS1vT0cyUFpaUEQzWi9HR0lXK0I5S013PT0%3D--dfed4309997ee25282c47d21709c4d4669cad12c',
			        'expire' => '3600',
			        'domain' => '.knowthycustomer.com',
			        'path'   => '/',
			        'prefix' => '',
			        'secure' => TRUE
			);

           echo $this->input->set_cookie($cookie);

		}

		public function check_cookie_get(){
			
			//$this->load->helper('cookie');
            
           	//$cookie_info = $this->input->get_cookie('KTCSHOPIFYCK');
           	//$cookie_info = get_cookie('ktcshopify_KTCSHOPIFYCK');
           	$cookie_info = get_cookie('_beenverified3_session');
           	var_dump($cookie_info);

		}

		/**
	    * Function to clear
	    * the order data from
	    * test store
	    * Method: GET
	    * Params:
	    * @store_context: String
	    **/
		public function maintain_store_orders($store_domain = 'ktc-store-pass-check2.myshopify.com'){

			$flag = 0;

			//get the store info by store domain
			$store_domain = urldecode($store_domain);

			if (!empty($store_domain)){

				//Loop through the global dev list
				//to check if a dev list is sent
				//as in parameter
				foreach ($this->__store_list as $list) {
					# code...
					if (strcmp($list, $store_domain) == 0){
						//echo 'Store Matched';
						$flag = 1;
						break;
					}
				}
			}

			if ($flag === 1){
				$store_info = $this->maintain->get_store_info_by_domain($store_domain);

				if (!empty($store_info)){
					$this->maintain->delete_store_orders($store_info);
					echo 'Store Orders Deleted';
				}else{
					echo 'Invalid store domain or store does not exist!';
				}
			}else{
				echo 'Invalid store domain or store does not exist!';
			}

		}

		/**
	    * Function to clear
	    * the user data from
	    * test store
	    * Method: GET
	    * Params:
	    * @store_domain: String
	    **/
		public function maintain_store_users($store_domain = 'ktc-store-pass-check2.myshopify.com'){
			$flag = 0;

			//get the store info by store domain
			echo $store_domain = urldecode($store_domain);
			
			if (!empty($store_domain)){

				//Loop through the global dev list
				//to check if a dev list is sent
				//as in parameter
				foreach ($this->__store_list as $list) {
					# code...
					if (strcmp($list, $store_domain) == 0){
						//echo 'Store Matched';
						$flag = 1;
						break;
					}
				}
			}

			if ($flag === 1){
				$store_info = $this->maintain->get_store_info_by_domain($store_domain);
				print_r($store_info);
				
				if (!empty($store_info)){
					$this->maintain->delete_store_users($store_info);
					echo 'Store Users Deleted';
				}else{
					echo 'Invalid store domain or store does not exist!';
				}
			}else{
				echo 'Invalid store domain or store does not exist!';
			}
			
		}

		public function encode_keys(){
			echo 'shopify: '.base64_encode('shopify_api').' '.base64_encode('gxuYRu2!Ns!vTaMErpW^QY!2uy5mWr&MswrBdyAcRfBh*7D&TK46');

			echo '<br>magento commerce: '.base64_encode('magento_api').' '.base64_encode('q$DhQUjcy6Sr3Y@uBZQV4J&NHYWeDXjw24^3#JP9m6qd4xzzmgZ8');

			echo '<br>big commerce: '.base64_encode('big_commerce_api').' '.base64_encode('eCq%8x2vwfVErC@uyu33C5B&B*K6F2XzwqE9*P*q@EmheN!NgpGx');
		}

	}

?>