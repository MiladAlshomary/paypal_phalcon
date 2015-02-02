Paypal  on Php Phalcon 
==================
paypal library class and controller helper to integrate paypal payment with php phalcon framework in an easy way.

Actually its taking from [ paypal yii extension](https://github.com/stdevteam/yii-paypal)  with some modification to be integrated with Phalcon framework.

Usage :
----------
In config.ini :
 

 

      [paypal]
       apiUsername = XXXXXXX
        apiPassword = XXXXXXXX
        apiSignature = XXXXXXXX
        apiLive = false
        returnUrl = yourdomain/paypal/confirm/
        cancelUrl = yourdomain/paypal/cancel/
        payment_description = description sent to paypal about the payment
        payment_amount = payment amount

In Payment controller you can find code for example for calling paypal api with handling of cancel and confirm cases.
