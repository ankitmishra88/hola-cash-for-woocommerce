<?php
    
    namespace HOLA_WC;

    defined('ABSPATH')||die('No Script Kiddies Please');

    class HOLA_CASH{
        public function __construct(){
            add_action('plugins_loaded',[$this,'init_gateway']);
            add_action('wp_head',[$this,'hola_cash_css']);
        }

        function init_gateway(){
            require_once(HOLA_WC_DIR.'/includes/functions.php');
            require_once(HOLA_WC_DIR.'/includes/class.hola_cash_wc_gateway.php');
            require_once(HOLA_WC_DIR.'/includes/class.hola_cash_api.php');
            add_filter('woocommerce_payment_gateways', [$this,'add_gateway'],10,1);
            add_action('wp_ajax_nopriv_hola_cash_wc_listen',[$this,'process_webhook']);
			add_action('wp_ajax_hola_cash_wc_listen',[$this,'process_webhook']);
        }
		
		function process_webhook(){
			$api=new HOLA_WC_API();
			$api->process_webhook();
			wp_send_json(array());
		}

        function add_gateway($gateways){
            $gateways[]='\HOLA_WC\HOLA_CASH_WC_GATEWAY';
            return $gateways;
        }

        function hola_cash_css(){
            wp_enqueue_style('hola-cash-css',HOLA_WC_URL.'/includes/assets/hola-cash-styles.css');
        }
    }

?>