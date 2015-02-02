<?php
/**
 * User: Milad
 * Date: 02/02/15
 * Time: 10:33 PM
 */

class PaypalController extends ControllerBase {

    public function initialize (){
        $this->view->setTemplateAfter('main');
        Phalcon\Tag::setTitle('Paypal Demo');
        parent::initialize();
    }

    public function indexAction(){

    }

    public function buyAction($parameter = null) {    

        // set 
        $config = new Phalcon\Config\Adapter\Ini(__DIR__ . '/../../app/config/config.ini');
        $paymentInfo['Order']['theTotal'] = $config->paypal->payment_amount;
        $paymentInfo['Order']['description'] = $config->paypal->payment_description;
        $paymentInfo['Order']['quantity'] = '1';

        $paypal = Paypal::getInstance();

        // call paypal 
        $result = $paypal->SetExpressCheckout($paymentInfo); 
        
        //Detect Errors 
        if(!$paypal->isCallSucceeded($result)){ 
            if($paypal->apiLive === true){
                //Live mode basic error message
                $error = 'We were unable to process your request. Please try again later';
            } else {
                //Sandbox output the actual error message to dive in.
                $error = $result['L_LONGMESSAGE0'];
            }
            $this->view->setVar('message', $error);
        } else { 
            // send user to paypal 
            $token = urldecode($result["TOKEN"]);
            $payPalURL = $paypal->paypalUrl.$token; 
            return $this->response->redirect($payPalURL);
        }
    }

    public function confirmAction() {

        $token = trim($_GET['token']);
        $payerId = trim($_GET['PayerID']);
        
        $paypal = Paypal::getInstance();
        $result = $paypal->GetExpressCheckoutDetails($token);

	// set 
        $config = new Phalcon\Config\Adapter\Ini(__DIR__ . '/../../app/config/config.ini');
        $result['PAYERID'] = $payerId; 
        $result['TOKEN'] = $token; 
        $result['ORDERTOTAL'] = $config->paypal->payment_amount;

        //Detect errors 
        if(!$paypal->isCallSucceeded($result)){ 
            if($paypal->apiLive === true){
                //Live mode basic error message
                $error = 'We were unable to process your request. Please try again later';
            } else {
                //Sandbox output the actual error message to dive in.
                $error = $result['L_LONGMESSAGE0'];
            }

            $this->view->setVar('message', $error);
        } else { 
            $paymentResult = $paypal->DoExpressCheckoutPayment($result);
            //Detect errors  
            if(!$paypal->isCallSucceeded($paymentResult)){
                if($paypal->apiLive === true){
                    //Live mode basic error message
                    $error = 'We were unable to process your request. Please try again later';
                } else {
                    //Sandbox output the actual error message to dive in.
                    $error = $paymentResult['L_LONGMESSAGE0'];
                }

                $this->view->setVar('message', $error);
            } else {
                //update user information to be done payment
                $company_auth = $this->session->get('company_auth');
                $company_id   = $company_auth['id'];
                $company = Company::findFirst("id='$company_id'");
                $company->upgrade();
                
                //payment was completed successfully
                $this->view->setVar('message', 'payment succeeded, congrats !!');
                $this->view->setVar('success', true);
                $this->view->setVar('company', $company);
            }
            
        }
    }
        
    public function cancelAction() {
        //The token of the cancelled payment typically used to cancel the payment within your application
        $token = $_GET['token'];
        $this->view->setVar('message', 'your payment has been cancled, try again later !!');
    }
    
} 
