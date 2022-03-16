console.log(holacashwc);


// Listener to detect changes in main url (3DS or Dynamic CVV)

window.addEventListener('onhashchange',function(data){
    console.log(data)
})

let orderId=false

// payment charge callbacks

const callbacks = {
    onSuccess: (res) => {
    //   setSuccessResponse(JSON.parse(res))
      //setReceiptVisible(true)
      console.log('onSuccess', JSON.parse(res))
    },
    onAbort: () => console.log('onAbort callback'),
    onError: (err) => console.log(JSON.stringify(err))
  }; 


jQuery(document.body).on('payment_method_selected',function(e){
    let payment_method=jQuery('input[name="payment_method"]').val()
    if(payment_method=='hola_cash_wc_gateway'){
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
                jQuery('#checkout-button').attr('data-disabled',false)
            })
            
            .catch(err=>console.log("I got an error",err))
        }
    }
})