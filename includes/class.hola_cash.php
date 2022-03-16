<?php
    
    namespace HOLA_WC;

    defined('ABSPATH')||die('No Script Kiddies Please');

    class HOLA_CASH{
        public function __construct(){
            add_action('plugins_loaded',[$this,'init_gateway']);
        }

        function init_gateway(){
            require_once(HOLA_WC_DIR.'/includes/functions.php');
            require_once(HOLA_WC_DIR.'/includes/class.hola_cash_wc_gateway.php');
            require_once(HOLA_WC_DIR.'/includes/class.hola_cash_api.php');
            add_filter('woocommerce_payment_gateways', [$this,'add_gateway'],10,1);
        }

        function add_gateway($gateways){
            $gateways[]='\HOLA_WC\HOLA_CASH_WC_GATEWAY';
            return $gateways;
        }
    }

?>