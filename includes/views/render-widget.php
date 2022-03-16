<?php

    defined('ABSPATH')||die('No Script Kiddies Please');
    if($this->test_mode){
        $cdn_url='https://widget.connect.sandbox.holacash.mx';
        $base_url='https://sandbox.api.holacash.mx/v2/checkout/button';
        
    }
    else{
        $cdn_url='https://widget.connect.holacash.mx';
        $base_url='https://live.api.holacash.mx/v2/checkout/button';
        
    }
    $public_api_key=$this->public_api_key;
    if(empty($public_api_key)):
        echo "<p>".__("This method is unavailable currently",'hola-cash-wc')."</p>";
    
    else:
    ?>
    <div id="hola_cash_wc_wrapper">
        <!--Required HTML Elements to render Hola.Cash-->
        <div id="instant-holacash-checkout-button">
            <object
                id="checkout-button"
                data='<?php echo "{$base_url}?public_key={$public_api_key}"; ?>'
                data-disabled='true'
            ></object>
        </div>    

    </div>

        <!--Let's include connect.js -->
        <script
            async
            id="holacash-connect"
            type="text/javascript"
            src="<?php echo $cdn_url; ?>/connect.min.js"
            data-public-key="<?php echo $public_api_key; ?>">
        </script>

        

    <?php
        


        $hola_api=new \HOLA_WC\HOLA_WC_API();
        wp_register_script('hola-cash-wc',HOLA_WC_URL.'/includes/assets/hola-cash-gateway.js',array(),'1.0.0');
        $localized_data=array(
            'ajaxUrl'=>admin_url('admin-ajax.php'),
            'public_key'=>$public_api_key,
            'ip'=>$hola_api->getIPAddress(),
            'initial_total'=>(WC()->cart->total)*100,
            'create_order'=>$hola_api->getEndpoint('create_order'),
            'order_body'=>hola_get_orders_array(),
            'purchase_description'=>apply_filters('hola_cash_purchase_title',__("Purchase on ".get_bloginfo('name').""))
        );
        wp_localize_script('hola-cash-wc','holacashwc',$localized_data);
        wp_enqueue_script('hola-cash-wc');
        endif;
    ?>