console.log(holacashwc);


// Listener to detect changes in main url (3DS or Dynamic CVV)

window.addEventListener('onhashchange',function(data){
    console.log(data)
})

let orderId=false

// payment charge callbacks

const callbacks = {
    onSuccess: (res) => {
        if(!JSON.parse(res)){
            return
        }
        console.log('onSuccess', JSON.parse(res))
        jQuery('#hola_cash_wc_wrapper').append(`<input type='hidden' id='hola_success_response' name='hola_success_response' value='${res}' />`)
        jQuery('#hola_cash_wc_wrapper').closest('form').submit()
    },

    onAbort: () => console.log('onAbort callback'),

    onError: (err) => console.log(JSON.stringify(err))
  }; 


jQuery(document.body).on('payment_method_selected',function(e){
    let payment_method=jQuery('input[name="payment_method"]:checked').val()
    console.log(payment_method)
    if(payment_method=='hola_cash_wc_gateway'){
        console.log('hiding',jQuery('#place_order'))
        jQuery(document.body).removeClass('hola-cash-selected')
        jQuery(document.body).addClass('hola-cash-selected')
        if(orderId){
            HolaCashCheckout.configure(
                {order_id: orderId},
                callbacks
            )
        }
        else{
            fetch(holacashwc.create_order,{
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-Api-Client-Key':holacashwc.public_key,
                },
                body:JSON.stringify(holacashwc.order_body)
            })
            .then(res=>res.json())
            .then(data=>{
                orderId=data.order_information.order_id
                HolaCashCheckout.configure(
                      {order_id: orderId},
                      callbacks
                );
                jQuery('#hola_cash_wc_wrapper').append(`<input type='hidden' value='${orderId}' name='holacash_order_id' /> `);
                jQuery('#checkout-button').attr('data-disabled',!isValidToProceed())
            })
            
            .catch(err=>console.log("I got an error",err))
        }
    }
    else{
        console.log('showing',jQuery('#place_order'))
        jQuery(document.body).removeClass('hola-cash-selected')
      
    }
})



jQuery(document).on('change','input,select,textarea',function(){
    jQuery('#checkout-button').attr('data-disabled',!isValidToProceed())
})

const isValidToProceed=function(){
    return true
}
