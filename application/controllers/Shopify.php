<?php
error_reporting(E_ERROR);
//error_reporting(E_ERROR);
//header('X-Frame-Options: GOFORIT');
//https://shopify.knowthycustomer.com/index.php/shopify/order_to_ktc => ktc_order

defined('BASEPATH') OR exit('No direct script access allowed');
require_once("shopify.php");
require_once(APPPATH.'libraries/lib/curl.php');
require_once(APPPATH.'libraries/lib/curl_response.php');


class Shopify extends CI_Controller {
    public $secret;
    public $shop;
    public $scope;
    public $api_key;

    public function __construct() {
        parent::__construct();        
        $this->load->helper('url');
        $this->load->helper('cookie');

        $this->load->model('order_model', 'order');
        $this->load->model('user_model', 'user');
    }

    public function index(){

        //show_404();
        //truncate the database tables;
        // $this->db->query('DELETE FROM `customer_details`');
        // $this->db->query('DELETE FROM `order_description`');
        // $this->db->query('DELETE FROM `order_details`');
        // $this->db->query('DELETE FROM `user_details`');
        //$this->db->query('ALTER TABLE `user_details` MODIFY COLUMN first_name varchar(100)');

        //$all_users = $this->user->get_all_users();

        //echo '<pre>';print_r($all_users);

        // foreach ($all_users as $value) {
        //     # code...
        //     //print_r($value);
        //     echo "Password: " . $this->my_simple_crypt($value['password'], 'd');
        // }
        // die();

        // $url = 'https://knowthycustomer.com/api/v4/session.json';
        // $sign_up['user']['email'] = 'akar@knowthycustomer.com';
        // $sign_up['user']['password'] = '';

        // $res = $this->_call_api($url, 'POST', $sign_up);
        // $row = json_decode($res, true);
        // print_r($row);
    }
    
    /**
    * Function to check the merchant    
    * preference    
    **/
    public function preference() {
        $this->_data['api_key'] = $_SESSION['api_key'];
        $this->_data['shop'] = $_SESSION['shop'];

        $this->load->view('layout/preference', $this->_data);
    }
            
    /**
    * Landing page for the merchant
    * user always comes here for the 
    * after redirecting from shopify app    
    **/
    public function welcome_app() {
        // echo '<h2>The website is currently in development mode. Sorry for the inconvenience.</h2>
        //     <p>Please contact the administrator <a href="mailto:akar@beenverified.com">akar@beenverified.com</a></p>';

        ////Use session library
        ////Unset the seesion data
        //session_unset();
        unset($_SESSION['shop']);
        unset($_SESSION['oid']);
        $shop = $this->input->get('shop');
        $scope = $this->input->get('scope');
        $api_key = $this->input->get('api');
        $secret = $this->input->get('secret');

        if (isset($_GET['id']) && !empty($_GET['id'])){

            $_SESSION['oid'] = $this->input->get('id');
        }

        ////Load shopify API related files                
        $shopifyClient = new ShopifyClient($shop, "", $api_key, $secret);

        // Now, request the token and store it in your session.
        // Url to redirect and get the app authorise key/access token
        $auth_url = 'https://shopify.knowthycustomer.com/index.php/shopify/get_shopify_orders?api_key='.$api_key.'&secret='.$secret;

        ///// redirect to authorize url /////        
        redirect($shopifyClient->getAuthorizeUrl($scope, $auth_url, ' '), 'location');
        exit;        
    }
                 
    /**
    * Redirection page for the merchant
    * Generate access tokens for the shopify
    * api calls
    **/
    public function get_shopify_orders() { 

        $code = $this->input->get('code');
        $hmac = $this->input->get('hmac');
        $_SESSION['api_key'] = $this->input->get('api_key');
        $_SESSION['secret'] = $this->input->get('secret');
        $shop = $_SESSION['shop'] = $this->input->get('shop');
        
        ////Load shopify API related files        
        ///// call shopify API to get access token /////

        if (isset($_GET['code']) && !empty($_GET['code'])){
            try{
                $shopifyClient = new ShopifyClient($shop, "", $_SESSION['api_key'], $_SESSION['secret']);
                $access_data = $shopifyClient->getAccessToken($code);
                
                //echo '<pre>';
                //print_r($access_data);

                //Shopify client call to fetch merchant info
                $sc_shop            = new ShopifyClient($shop, $access_data['access_token'], $_SESSION['api_key'], $_SESSION['secret']);

                // Get shop info            
                $shop_info          = $sc_shop->call('GET', '/admin/shop.json');
                $shop_info['tokens']= $access_data;

                //print_r($shop_info);

                $_SESSION['token'] = $access_data['access_token'];
                //$_SESSION['user_id'] = $access_data['associated_user']['id'];
                $_SESSION['user_id'] = $shop_info['id'];

                // $user_data = array(
                //     'user_id' => $access_data['associated_user']['id'],
                //     'first_name' => $access_data['associated_user']['first_name'],
                //     'last_name' => $access_data['associated_user']['last_name'],
                //     'email' => $access_data['associated_user']['email'],
                //     'access_token' => $access_data['access_token'],
                // );

                $user_data = array(
                    'user_id' => $shop_info['id'],
                    'first_name' => explode(' ', $shop_info['shop_owner'])[0],
                    'last_name' => explode(' ', $shop_info['shop_owner'])[1],
                    'email' => $shop_info['email'],
                    'access_token' => $access_data['access_token'],
                    'store_domain' => $shop
                );

            }catch(Exception $e){
                echo "Error creating token";

            }
        }

        /*if (empty($access_data)){
            $redirect_url = "https://".$shop."/admin/apps/know-thy-customer-1/index.php/shopify/welcome_app?api=4ede6c02beb0886538bac12a4f6335e6&secret=0420ed9cefe9287f4585a55ce57b81c6&scope=read_orders,read_customers,read_shipping";            
            redirect($redirect_url, 'location');
            exit;
        }*/
        
        ////Get the user details////
        $user_exists = $this->user->check_user_exist($shop_info);        

        if(empty($user_exists)){
            //echo 'Inside Not exist';
            ////Save the user details////

            if (!empty($user_data)){          
                $this->user->save_user_details($user_data);
            }

        }

        // if ($_SESSION['token'] != '')
        //     $_SESSION['shop'] = $shop;        
        /*if(!$shopifyClient->validateSignature($_GET))
            die('Error: invalid signature.');*/
        
        ///// call view function /////
        $this->show_view();
    }
    
    /**
    * Checks if the merchant is signed up or not with KTC
    * If the merchant is not signed up then display signup page
    * Else display the orders of the shopify store to the merchant
    **/
    public function show_view() {
        $user_id = $_SESSION['user_id'];
        
        ///Get the user details////        
        $user_details = $this->user->get_user_details_by_id($user_id);

        $this->_data['user'] = $user_details[0];
        $signup = $user_details[0]['signup_flag'];        

        if($signup) { 
            //if already signed up, redirect to order listing page
            $this->get_orders();
        } else { 
            // if new user, redirect for sign up
            $this->load->view('layout/register', $this->_data);
        }        
    }
    
    /** Function to test the signup user **/
    public function test_signup_user(){
        echo '<pre>';
        print_r($_POST);

        echo '<script type="text/javascript">';
        echo 'window.close();';
        echo '</script>';
    }

    /**
    * Function to register the shopify
    * merchant to KTC app
    * Method : POST
    **/
    public function signup_user() {
        $userdata = $this->input->post();
        $user_id = $userdata['userid'];

        $sign_up['account']['tos'] = "1";
        $sign_up['account']['first_name'] = $userdata['fn'];
        $sign_up['account']['last_name'] = $userdata['ln'];
        $sign_up['user']['email'] = $userdata['email'];
        $sign_up['user']['password'] = $userdata['pwd'];
        $sign_up['user']['password_confirmation'] = $userdata['rpwd'];
        $sign_up['user']['password_confirmation'] = $userdata['rpwd'];
        $sign_up['subscription_plan_name'] = '0_1_month_freemium_limit_10_shopify_knowthycustomer';
        $sign_up['business_contact']['contact_phone'] = $userdata['phone'];
        $sign_up['business_contact']['company'] = $userdata['cmpny'];
        $sign_up['business_contact']['job_title'] = $userdata['jobt'];
        
        ////Get the user details/////
        $signed_up = $this->user->get_user_details_by_id($user_id);

        if($signed_up['signup_flag'] != 1) {

            ///// Sign up new user on knowthycustomer site using API /////
            $url = 'https://www.knowthycustomer.com/api/v4/account.json';
            $res = $this->_call_api($url, 'POST', $sign_up, []);
            $raw_return = $res;
            $res = json_decode($res);
                        
            ///// Sign up - end /////
            if($res->meta->status == 200) {

                $user_data = array(
                  'first_name' => $userdata['fn'],
                  'last_name' => $userdata['ln'],
                  'email' => $userdata['email'],
                  'contact' => $userdata['phone'],
                  'password' => $this->my_simple_crypt($userdata['pwd'], 'e'),
                  'company' => $userdata['cmpny'],
                  'job_title' => $userdata['jobt'],
                  'user_code' => $res->account->user_info->user_code,
                  'signup_flag' => 1,
                  'user_subs_info' => serialize($raw_return)
                );
                
                ///Update the user details /////
                $this->user->update_user_details($user_id, $user_data);

                $url = 'https://knowthycustomer.com/api/v4/session.json';
                $sess_up['user']['email'] = $userdata['email'];
                $sess_up['user']['password'] = $userdata['pwd'];

                /*$res = $this->_call_api($url, 'POST', $sign_up);
                $session_data = json_decode($res, true);*/

                $curl = new Curl();
                $response = $curl->post($url, $sess_up);
                $body = json_decode($response->body, true); # A string containing everything in the response except for the headers
                $headers = $response->headers;

                //echo '<pre>';
                //print_r($response);
                //print_r($body);
                //print_r($headers);
                //$tmp = explode(';', $headers['Set-Cookie']);
                //print_r($tmp);                
                //$hash = explode('=', $tmp[0]);
                //print_r($hash);

                echo '<script type="text/javascript">';
                echo 'window.open("https://www.knowthycustomer.com/", "_blank")';
                echo '</script>';

                die();
                
                // echo 'Cookie Set: '.setcookie('_beenverified3_session', $hash[1], 86400, "/", '.knowthycustomer.com', false, false);
                // echo '<br>Count Cookie: '.count($_COOKIE);
                // print_r($_COOKIE['_beenverified3_session']);

                $cookie3sess = array(
                    'name'   => '_beenverified3_session',
                    'value'  => $hash[1],
                    'expire' => '86400',
                    'domain' => '.knowthycustomer.com',
                    'path'   => '/',
                    'prefix' => '',
                    'secure' => TRUE
                );
                //echo 'Set Cookie: '.$this->input->set_cookie($cookie3sess);

                //Set the session info
                //in the cookies
                // $cookielin = array(
                //     'name'   => 'loggedin',
                //     'value'  => 'true',
                //     'expire' => '86400',
                //     'domain' => '.knowthycustomer.com',
                //     'path'   => '/',
                //     'prefix' => '',
                //     'secure' => TRUE
                // );

                // $this->input->set_cookie($cookielin);

                // $cookielas = array(
                //     'name'   => 'loggedinas',
                //     'value'  => urlencode($body['account']['user_info']['email']),
                //     'expire' => '86400',
                //     'domain' => '.knowthycustomer.com',
                //     'path'   => '/',
                //     'prefix' => '',
                //     'secure' => TRUE
                // );

                // $this->input->set_cookie($cookielas);

                // $cookie3sess = array(
                //     'name'   => '_beenverified3_session',
                //     'value'  => $hash[1],
                //     'expire' => '86400',
                //     'domain' => '.knowthycustomer.com',
                //     'path'   => '/',
                //     'prefix' => '',
                //     'secure' => TRUE
                // );

                // $this->input->set_cookie($cookie3sess);

                // $cookiebvt = array(
                //     'name'   => 'bv_time',
                //     'value'  => $body['account']['user_info']['join_date'],
                //     'expire' => '86400',
                //     'domain' => '.knowthycustomer.com',
                //     'path'   => '/',
                //     'prefix' => '',
                //     'secure' => TRUE
                // );

                // $this->input->set_cookie($cookiebvt);

                // $cookiellin = array(
                //     'name'   => 'last_login',
                //     'value'  => $body['account']['user_info']['join_date'],
                //     'expire' => '86400',
                //     'domain' => '.knowthycustomer.com',
                //     'path'   => '/',
                //     'prefix' => '',
                //     'secure' => TRUE
                // );

                // $this->input->set_cookie($cookiellin);
            }
        }

        ///// Redirect to the orders page after signup //////
        $this->get_orders();
    }

    public function check_cookie_get(){
            
        //$this->load->helper('cookie');
        
        //$cookie_info = $this->input->get_cookie('KTCSHOPIFYCK');
        //$cookie_info = get_cookie('ktcshopify_KTCSHOPIFYCK');
        $cookie_info = get_cookie('_beenverified3_session');
        var_dump($cookie_info);

    }

    public function check_cookie_del(){
        delete_cookie('_beenverified3_session', '.knowthycustomer.com', '/', '');
    }
    
    /**
    * Function to get all the orders
    * from the shopify api    
    **/
    public function get_orders() {
        
        $user_id = $_SESSION['user_id'];
        
        //print_r($this->seesion->all_userdata());

        ///Get the user details////        
        $user_details = $this->user->get_user_details_by_id($user_id);

        $sc = new ShopifyClient($_SESSION['shop'], $_SESSION['token'], $_SESSION['api_key'], $_SESSION['secret']);

        ///// Get all products /////
        $orders = $sc->call('GET', '/admin/orders.json?limit=250');
        //$orders = $sc->call('GET', '/admin/customers.json');
        
        if(!empty($orders)) {
            ///// update order data in order_details table /////

            foreach ($orders as $order) {
                $fraudArray = [];

                $orderexist = $this->order->get_order_details_order_id($order);

                //// Update order description ////
                $this->order->update_order_description($order);

                ///// update addresses in customer_details table /////
                $cust_details_id =  $this->save_addresses($order);

                if(is_null($cust_details_id)) {
                    $cust_details_id = 1;
                }

                if(empty($orderexist)) {

                    /////insert JSON order in order_description /////
                    $this->order->save_order_description($order);

                    ////Save the order details ////
                    $this->order->save_order_details($order,$cust_details_id);

                    //populate the ktc fraud array
                    $fraudArray['platform'] = 'shopify';

                    //User code sent by KTC registration api
                    $fraudArray['user_code'] = $user_details[0]['user_code'];

                    //store/user id assigned by shopify.knowthycustomer.com
                    $fraudArray['platform_user_id'] = $user_details[0]['id'];

                    //user id from shopify api
                    $fraudArray['merchant_account_id'] = $user_details[0]['user_id'];

                    $fraudArray['order_number'] = $order['order_number'];
                    $fraudArray['amount'] = $order['total_price'];
                    $fraudArray['received_at'] = $order['created_at'];
                    $fraudArray['cc_bin'] = '';
                    $fraudArray['cc_last_4'] = '';
                    $fraudArray['cc_company'] = '';                

                    if (isset($order['billing_address'])){
                        $fraudArray['billing_first_name'] = $order['billing_address']['first_name'];
                        $fraudArray['billing_middle_name'] = '';
                        $fraudArray['billing_last_name'] = $order['billing_address']['last_name'];

                        $billing_address = $order['billing_address']['address1'].','.$order['billing_address']['city'].','.$order['billing_address']['province'].
                                            ','.$order['billing_address']['country'].' '.$order['billing_address']['zip'];

                        $fraudArray['billing_address'] = (string)$billing_address;
                        $fraudArray['billing_phone'] = $order['billing_address']['phone'];
                        $fraudArray['billing_email'] = $order['email'];
                        $fraudArray['billing_ip_address'] = $order['browser_ip'];
                    }

                    if (isset($order['shipping_address'])){
                        $fraudArray['shipping_first_name'] = $order['shipping_address']['first_name'];
                        $fraudArray['shipping_middle_name'] = '';
                        $fraudArray['shipping_last_name'] = $order['shipping_address']['last_name'];

                        $billing_address = $order['shipping_address']['address1'].','.$order['shipping_address']['city'].','.$order['shipping_address']['province'].
                                            ','.$order['shipping_address']['country'].' '.$order['shipping_address']['zip'];

                        $fraudArray['shipping_address'] = (string)$billing_address;
                        $fraudArray['shipping_phone'] = $order['billing_address']['phone'];
                        $fraudArray['shipping_email'] = $order['email'];
                    }

                    //Submit the data to KTC fraud order api
                    $response = $this->_fraud_order_api($fraudArray);

                } else {

                    ////Update the order details ////
                    $this->order->update_order_details($order);                
                }
                
            }
            ///// order_details - end /////
        }

        ///// redirect to view /////
        if (isset($_SESSION['oid'])){
            $this->order_to_ktc();
        }else{
            $this->goto_order_list();
        }
    }
    
    /**
    * Function to get all orders from DB
    * Creating pagination
    * $lastid : last order id displayed
    * $nav : String, 'prev/next'
    * Contains the order details of 
    * a single order
    **/
    public function goto_order_list($nav = '',$count = 0) {
        $userid = $_SESSION['user_id'];
        $allorders = $this->order->get_all_orders_userid($userid);
        $ocount = count($allorders);
        ///// manage pagination limit /////
        if($nav == 'next') {
            $count += 50;
        } else if($nav == 'prev') {
            $count = abs($count - 50);
        }
        ///// pagiation code here /////
        $orders = $this->order->get_paginated_orders($userid,$count, $nav);
        ///////////////////////////////
        $this->_data['orders'] = $orders;
        
        $this->_data['ocount'] = $ocount;
        $this->_data['count'] = $count;
        $this->_data['ip_address'] = $this->get_client_ip();
        //echo '<pre>'; print_r($this->_data); exit;
        if ($this->input->is_ajax_request()) {
            echo $this->load->view('layout/order_listing_table',$this->_data, TRUE);
        } else {
            $this->load->view('layout/order_listing',$this->_data);
        }
    }
    
    /**
    * Function to save all the order customer
    * details we get the shopify store to
    * our local server
    * $order : array
    * Contains the order details of 
    * a single order
    **/
    public function save_addresses($order) {
        $cust_detail_id = 0;
        //// Call the order model /////
        $cust_detail_id = $this->order->save_order_customer_details($order);
        return $cust_detail_id;
    }
    
    /*
     * Function to redirect to KTC address deep link
     * from order details page using view
     */
    public function order_to_ktc() {
        //$order_id = $this->input->get('id');
        $order_id = $_SESSION['oid'];
        $order_details = $this->order->get_order_byid($order_id);
        //$address = $order_details[0];
        //$check_link = 'https://www.knowthycustomer.com/f/search/property?address='.$address['s_address1'].'&city='.$address['s_city'].'&state=&zipcode='.$address['s_zip'];
        
        $order = $order_details[0];
        //echo '<pre>'; print_r($order); exit;
        $ip_address = $this->get_client_ip();
        $b_address = $order['b_address1'].' '.$order['b_city'].' '.$order['b_province'].' '.$order['b_zip'].' '.$order['b_country'];
        $s_address = $order['s_address1'].' '.$order['s_city'].' '.$order['s_province'].' '.$order['s_zip'].' '.$order['s_country'];
        // $check_link = 'https://www.knowthycustomer.com/f/generate/fraud?billing_first_name='.((isset($order['b_first_name']) && !empty($order['b_first_name']))?$order['b_first_name']:'Not Available').'&billing_middle_name=&billing_last_name='.((isset($order['b_last_name']) && !empty($order['b_last_name']))?$order['b_last_name']:'Not Available').'&billing_address='.((isset($b_address) && !empty($b_address))?$b_address:'Not Available').'&billing_ip_address='.$ip_address.'&billing_email='.((isset($order['contact_email']) && !empty($order['contact_email']))?$order['contact_email']:'Not Available').'&billing_phone='.((isset($order['contact_email']) && !empty($order['b_phone']))?$order['b_phone']:'Not Available').'&shipping_first_name='.((isset($order['s_first_name']) && !empty($order['s_first_name']))?$order['s_first_name']:'Not Available').'&shipping_middle_name=&shipping_last_name='.((isset($order['s_last_name']) && !empty($order['s_last_name']))?$order['s_last_name']:'Not Available').'&shipping_address='.((isset($s_address) && !empty($s_address))?$s_address:'Not Available').'&shipping_email='.((isset($order['contact_email']) && !empty($order['contact_email']))?$order['contact_email']:'Not Available').'&shipping_phone='.((isset($order['s_phone']) && !empty($order['s_phone']))?$order['s_phone']:'Not Available');
        
        $check_link = 'https://www.knowthycustomer.com/f/generate/fraud?billing_first_name='.$order['b_first_name'].'&billing_middle_name=&billing_last_name='.((isset($order['b_last_name']) && !empty($order['b_last_name']))?$order['b_last_name']:'Not Available').'&billing_address='.((isset($b_address) && !empty(trim($b_address)))?$b_address:'Not Available').'&billing_ip_address='.$ip_address.'&billing_email='.$order['contact_email'].'&billing_phone='.$order['b_phone'].'&shipping_first_name='.$order['s_first_name'].'&shipping_middle_name=&shipping_last_name='.$order['s_last_name'].'&shipping_address='.$s_address.'&shipping_email='.$order['contact_email'].'&shipping_phone='.$order['s_phone'];

        $this->_data['newurl'] = $check_link;
        $this->load->view('layout/goto_ktc',$this->_data);
    }
    
    /**
    * Function to manualy delete user
    * when uninstall app from store
    **/
    public function delete_users() {
        /////to reset all the tables
        /*$this->db->truncate('user_details');
        $this->db->truncate('order_details');
        $this->db->truncate('customer_details');
        $this->db->truncate('order_description');*/
        /////to delete perticular store details - when we uninstall app
        /*$this->db->where('user_id', $_SESSION['user_id']);
        $result = $this->db->delete('user_details');
        echo '<br>Result : '.$result.'<br>Q : '.$this->db->last_query(); die; */
    }

    /**
    * Function to call the curl
    * for the api
    * $url : string
    * $method : string
    * $data : array
    **/
    private function _call_api($url, $method, $data = array(), $auth = array()) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        if($method == 'POST')
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        if (!empty($auth)){
            curl_setopt($curl, CURLOPT_USERPWD, "".$auth['username'].":".$auth['password']."");
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        curl_close ($curl);

        return $response;
    }
    
    // Function to get the client IP address
    public function get_client_ip() {
           $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        $ip = substr($ipaddress, 0, strpos($ipaddress, ','));
        return $ip;
    }

    /**
    * Function to call the curl
    * for the fraud order api    
    * $data : array
    **/
    private function _fraud_order_api($data = []){
        $url = 'https://knowthycustomer.com/api/v4/fraud_orders';
        
        $auth['username'] = base64_decode($this->config->item('fcapiky_usrnm'));
        $auth['password'] = base64_decode($this->config->item('fcapiky_usrpwd'));

        $res = $this->_call_api($url, 'POST', $data, $auth);
        $session_data = json_decode($res, true);
        //echo '<pre>';
        //print_r(json_decode($res));

        return json_decode($res, true);
    }

    public function test_fraud_report_api(){
        $url = 'https://knowthycustomer.com/api/v4/fraud_orders';
        $sess_up['platform'] = 'shopify';
        
        $auth['username'] = base64_decode($this->config->item('fcapiky_usrnm'));
        $auth['password'] = base64_decode($this->config->item('fcapiky_usrpwd'));

        $res = $this->_call_api($url, 'POST', $sess_up, $auth);
        $session_data = json_decode($res, true);
        echo '<pre>';
        print_r(json_decode($res));
    }

    /**
     * Encrypt and decrypt
     * 
     * @author Nazmul Ahsan <n.mukto@gmail.com>
     * @link http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
     *
     * @param string $string string to be encrypted/decrypted
     * @param string $action what to do with this? e for encrypt, d for decrypt
     */
    private function my_simple_crypt($string, $action = 'e') {
        // you may change these values to your own
        $secret_key = 'C22TSmwH1KKivzPCF4g0KR2FLQyzflnV';
        $secret_iv = 'BWb4U9lg6is9QUUc5DgnJrmRisXUrvMD';

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

        if ($action == 'e'){
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }elseif( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }

        return $output;
    }

    /**
     * Test function to check encryption/decryption
    **/
    public function check_encrypt_decrypt(){        
        $encrypted = $this->my_simple_crypt( 'Hello World!', 'e' );
        $decrypted = $this->my_simple_crypt( $encrypted, 'd' );

        echo 'Encrypted: ' . $encrypted . '<br>Decrypted: ' . $decrypted;
    }
}   
