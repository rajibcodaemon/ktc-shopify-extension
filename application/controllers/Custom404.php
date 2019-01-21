<?php

	class Custom404 extends CI_Controller{

		public function show_missing(){
			$this->load->view('error_page', []);
		}
	}
?>