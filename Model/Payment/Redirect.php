<?php

namespace Compralo\Payments\Model\Payment;

use \Magento\Payment\Model\Method\AbstractMethod;
Use Magento\Framework\Debug;

class Redirect extends AbstractMethod
{
    protected $_code         = 'compraloredirect';
	protected $_canAuthorize = true;
    protected $_isOffline    = false;
    protected $_isGateway    = true;
	protected $_helper       = null;
	protected $_logger       = null;
	
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Compralo\Payments\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
		$this->_logger = $logger;
		$this->_helper = $helper;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
	}
	
   public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
	{
		parent::authorize($payment, $amount);
	
		$order              = $payment->getOrder();
		$transaction        = $this->_helper->getRequest();
		$orderId            = $order->getIncrementId();
		$transactionData    = $this->_helper->dataPayment($orderId, $amount);
		$value              = $transactionData['value'];
		$storeName          = $transactionData['store_name'];
		$description        = $transactionData['description'];
		$postBackUrl        = $transactionData['postback_url'];
		$backUrl            = $transactionData['back_url'];
		
		$paymentRequest     = $transaction->create($storeName, $value, $description, $postBackUrl, $backUrl);
		
		//$this->_logger->debug(var_export($paymentRequest, true));

		if( isset( $paymentRequest['status'] ) && 
			true == $paymentRequest['status'] ) {
			
			$compraloUrl   = $paymentRequest['checkout_url'];
			$compraloToken = $paymentRequest['token'];
			
			$payment->setAdditionalInformation(
				array(
					'COMPLARO_PAYMENT_URL'   => $compraloUrl,
					'COMPLARO_PAYMENT_TOKEN' => $compraloToken
				)
			);
		}

		$stateObject = \Magento\Sales\Model\Order::STATE_NEW;
		$payment->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
		$payment->setStatus('pending_payment');		
		$payment->setIsNotified(false);

		$this->_logger->debug('Complaro Payment');

		return $this;
	}  
}