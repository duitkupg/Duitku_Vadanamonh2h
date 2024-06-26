<?php
/**
 * Copyright (c) 2017. All rights reserved Duitku Vadanamon Host to Host.
 *
 * This program is free software. You are allowed to use the software but NOT allowed to modify the software.
 * It is also not legal to do any changes to the software and distribute it in your own name / brand.
 *
 * All use of the payment modules happens at your own risk. We offer a free test account that you can use to test the module.
 *
 * @author    Duitku Vadanamonh2h
 * @copyright Duitku Vadanamonh2h (http://duitku.com)
 * @license   Duitku Vadanamonh2h
 *
 */
namespace Duitku\Vadanamonh2h\Controller\Epayvadanamonh2h;
use Magento\Framework\Controller\ResultFactory;

class Checkout extends \Duitku\Vadanamonh2h\Controller\AbstractActionController
{
    /**
     * Checkout Action
     */
    public function execute()
    {
    	 $obj = \Magento\Framework\App\ObjectManager::getInstance();
    	
    	  $paymentmode = $this->_scopeConfig->getValue('payment/duitku_vadanamonepay/payment_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    	 if($paymentmode =='1')
    	 {
		 	  $url = 'https://passport.duitku.com/webapi';
		 }else{
		 	$url = 'https://sandbox.duitku.com/webapi';
		 	
		 }
        $order = $this->_getOrder();
        $this->setOrderDetails($order);
        $result = $this->getEPayPaymentWindowRequest($order);
        $helper = $obj->get('Duitku\Vadanamonh2h\Helper\Data');
        $DuitkuCore = $helper->getDuitkuCore();
      	$redirUrl = $DuitkuCore->getRedirectionUrl($url,$result);
      	$resultarr = array();
      	$resultarr['url']=$redirUrl;
        $resultJson = json_encode($resultarr);
        return $this->_resultJsonFactory->create()->setData($resultJson);
	  
    }

    /**
     * Get the Epay Payment window url
     *
     * @param \Magento\Sales\Model\Order
     * @return string|null
     */
    public function getEPayPaymentWindowRequest($order)
    {
        try {
            /** @var \Duitku\Vadanamonh2h\Model\Method\Epay\Payment */
            $epayMethod = $this->_getPaymentMethodInstance($order->getPayment()->getMethod());
            $response = $epayMethod->getPaymentWindow($order);
			
			$this->_duitkuLogger->addEpayInfo($order->getId(), json_encode($response));
            return $response;
        } catch (\Exception $ex) {
            $this->_duitkuLogger->addEpayError($order->getId(), $ex->getMessage());
            return null;
        }
    }
}
