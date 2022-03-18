<?php
namespace HOLA_WC;
/**
 * Class HOLA_CASH_WC_GATEWAY file.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Hola.Cash Payment Gateway.
 *
 * Provides Hola.Cash payment gateway.
 *
 * @class       HOLA_CASH_WC_GATEWAY
 * @extends     WC_Payment_Gateway
 */


class HOLA_CASH_WC_GATEWAY extends \WC_Payment_Gateway{

	/**
	 * Array of locales
	 *
	 * @var array
	 */
	public $locale;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		$this->id                 = 'hola_cash_wc_gateway';
		$this->icon               = apply_filters( 'hola_cash_icon', '' );
		$this->has_fields         = true;
		$this->method_title       = __( 'Hola.Cash', 'hola-cash-wc' );
		$this->method_description = __( 'Card, cash & Transfer payments', 'hola-cash-wc' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->method_title;
		$this->description  = $this->method_description;

        $this->public_api_key = $this->get_option('public_api_key');
        $this->private_api_key = $this->get_option('private_api_key');
        $this->test_mode       = $this->get_option('test_mode')=='yes';
		$this->webhook_key	   = $this->get_option('webhook_key');

        
		
		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}


	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'         => array(
				'title'   => __( 'Enable', 'hola-cash-wc' ),
                'description'=>__('To create an account send us a message and you we will respond as soon as possible', 'hola-cash-wc'),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Hola.Cash', 'hola-cash-wc' ),
				'default' => 'no',
			),

            'test_mode'=>array(
                'title'   => __( 'Test Mode', 'hola-cash-wc' ),
                'description'=>__("To create a trial account (sandbox) <a href='https://developers.holacash.mx/access/en'>click here</a>", 'hola-cash-wc'),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Test Mode', 'hola-cash-wc' ),
				'default' => 'yes',
            ),
            'public_api_key'=>array(
                'title'=>__('Public API Key','hola-cash-wc'),
                'type'=>'text',
                'default'=>''
            ),
            'private_api_key'=>array(
                'title'=>__('Private API Key','hola-cash-wc'),
                'type'=>'password',
                'default'=>''
			),
			'webhook_key'=>array(
				'title'=>__('Webhook Key','hola-cash-wc'),
				'type'=>'text',
				'default'=>'',
				'description'=>__('This can be configured in your Hola.cash porta','hola-cash-wc')
			)
		);

	}

    //load gateway setting options
    public function admin_options(){
        include_once(HOLA_WC_DIR.'/includes/views/hola-cash-admin-settings.php');
    }

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		$holacash_success_response=json_decode(stripslashes($_POST['hola_success_response']),true);
		$holacash_order_id=sanitize_text_field($_POST['holacash_order_id']);

		
		// var_dump($holacash_success_response);
		// exit();

		$charge_id=$holacash_success_response['id'];
		// print_r(array($charge_id,$holacash_order_id));
		// exit();
		

		if ( $order->get_total() > 0 && !empty($charge_id) ) {
			// Mark as on-hold (we're awaiting the payment).
			$order->update_status( apply_filters( 'woocommerce_holacash_process_payment_order_status', 'on-hold', $order ), __( 'Awaiting payment confirmation', 'hola-cash-wc' ) );
			update_post_meta($order->get_id(),'holacash_order_id',$holacash_order_id);
			update_post_meta($order->get_id(),'holacash_charge_id',$charge_id);
		}
		elseif(empty($charge_id)){
			$order->update_status('cancelled',"Couldn't create transaction");
		}
		else {
			$order->payment_complete();
		}

		//exit();
		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);

	}

    /**
     * Render Payment Fields using hola.cash connect.js
     */

    function payment_fields(){
        include_once(HOLA_WC_DIR.'/includes/views/render-widget.php');
    }

}
