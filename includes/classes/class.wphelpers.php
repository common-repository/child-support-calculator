<?php

if( !class_exists('WPCSC_WPHELPERS') ) {
	
	class WPCSC_WPHELPERS {
		
		private $login_url = 'login'; 
		private $signup_url = 'sign-up'; 
		private $account_page = 'my-account'; 
		private $my_admin = 'my-admin'; 
		
		public function __construct() {

			// Not used as template_redirect is doing the job
			// add_action('wp_logout', array($this, 'wp_logout'));
			// add_action('login_redirect', array($this, 'login_redirect'), 10, 1);
			// add_action('template_redirect', array($this, 'template_redirect'));
			// add_action('wp_head', array($this, 'test_me'));

			//add_filter('admin_init', array($this, 'admin_init'));	
			add_filter('show_admin_bar', array($this, 'control_admin_bar'));	
		}
		
		function admin_init() {
            if (!is_user_logged_in()) {
				return null;
			}
			if (!current_user_can('administrator') && is_admin()) {
				wp_redirect(site_url($this->account_page));
				exit();
			}
			if (! current_user_can('manage_options')) {
				show_admin_bar(false);
			}
		}

		function control_admin_bar() {
		    if ( ! current_user_can( 'manage_options' ) ) {
				return false; 
			}			
			
			return true; 
		}

		function php_alert($string) {
            echo esc_js("<script>alert('".$string."');</script>");
        }
		
//		function template_redirect() {
//            if(is_user_logged_in()) {
//                if(is_page([$this->login_url, $this->signup_url])) {
//                    // wp_die('I am redirecting you');
//					wp_redirect(site_url($this->account_page, 301));
//					exit();
//				}
//			}
//			else {
//				if(is_page([$this->account_page, $this->my_admin])) {
//					$redirect = site_url($this->login_url);
//					$redirect = add_query_arg(array('redirect'=>urlencode(curPageURL())), $redirect);
//					// wp_die($redirect); ;
//					wp_redirect($redirect, 301);
//					exit();
//				}
//			}
//		}

		// Not in use
		function login_redirect($redirect_to, $request, $user) {

			if (isset($user->roles) && is_array($user->roles)) {
				if (in_array('subscriber', $user->roles)) {
					$redirect_to =  site_url($this->account_page);
				}
			}

			return $redirect_to;
		}

        public function test_me()
        {
            wp_die('goooooooooooooooooooooooooooood');
        }

	}
}