<?php
    defined('ABSPATH')||die('No Script Kiddies Please');
    
    /**
     * Miscellaneous functions
     */

    /**
     * Returns formatted purchases in cart to pass in orders API
     */
     function hola_get_cart_items_array(){
        $purchases=array();
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            //print_r($cart_item);
            $product = $cart_item['data'];
            
            $product_id = $cart_item['product_id'];
            $quantity = $cart_item['quantity'];
            $price = WC()->cart->get_product_price( $product );
            $subtotal = $cart_item['line_total'];
            $purchases[]=array(
                'item_total_amount'=>array(
                    'amount'=>($subtotal)*100,
                    'currency_code'=>'MXN'
                ),
                'description'=>$product->get_title(),
                'id'=>"{$product_id}",
                'unit_amount'=>array(
                    'amount'=>($product->price)*100,
                    'currency_code'=>'MXN'
                ),
                'quantity'=>$cart_item['quantity']
            );
            }

        return apply_filters('hola_purchased_products_array',$purchases);
     }

     /**
      * Returns total order data to pass in Orders API
      */

     function hola_get_orders_array(){
        $hola_order=array(
            'order_total_amount'=>array(
                'amount'=>absint((WC()->cart->total)*100),
                'currency_code'=>'MXN'
            ),
            'description'=>apply_filters('hola_cash_purchase_title',__("Purchase on ".get_bloginfo('name')."")),
            'purchases'=>hola_get_cart_items_array()
           
        );

        return apply_filters('hola_before_configuration_order_array',$hola_order);

     }
?>