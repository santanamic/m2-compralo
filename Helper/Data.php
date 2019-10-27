<?php
namespace Compralo\Payments\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

use \Compralo\Payments\Api\Payments\Api;

/*

DEBUG PARA A API Key

DEVE RETORNAR OS CAMPOS DE CONGIGURAÇÕES


*/

class Data extends AbstractHelper
{
    protected $_scopeConfig;
    protected $_objectManager;
    protected $_urlBuilder;
	protected $_logger;
	protected $_orderRepository;
	protected $_checkoutSession;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $_objectManager,
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Checkout\Model\Session $checkoutSession

    ) {
        $this->_scopeConfig      = $context->getScopeConfig();
        $this->_objectManager    = $_objectManager;
		$this->_urlBuilder       = $context->getUrlBuilder();
		$this->_orderRepository  = $orderRepository;
		$this->_checkoutSession  = $checkoutSession;
    }
	
	public function dataPayment($orderId, $amount)
    {
		$description = $this->descriptionOrder($orderId);
		$orderAmount = $this->treatAmount($amount);
		$storeName   = $this->_getStoreName();
		$postback    = $this->_getPostBackUrl($orderId);
		$backUrl     = $this->_getBackUrl($orderId);

		$dataRequest = [
			'value'        => $orderAmount,
			'store_name'   => $storeName,
			'description'  => $description,
			'postback_url' => $postback,
			'back_url'     => $backUrl
		];
		
        return $dataRequest;
    }

	public function descriptionOrder($orderId)
	{
        return 'Order ID: ' . $orderId;
	}

	public function treatAmount(float $amount)
	{
        return number_format($amount, 2, '.', '');
	}
	
    public function getRequest()
    {
        $request = new Api();
		$apiKey  = $this->_getApiKey();
        $request->setApiKey($apiKey);
		
        return $request;
    }
	
	protected function _getApiKey()
    {
        $apiKey = $this->_scopeConfig->getValue(
            'payment/compraloredirect/api_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		return $apiKey;
	}
	
	protected function _getStoreName()
	{
		$storeManager  = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStore()->getName();
	}
	
    protected function _getPostBackUrl($orderId)
    {
        return $this->_urlBuilder->getUrl('compralo/payment/postback/', ['order_id' => $orderId]);
    }
	
    protected function _getBackUrl($orderId)
    {
        return $this->_urlBuilder->getUrl('compralo/payment/back/', ['order_id' => $orderId]);
    }

	public function getLastRealOrderId()
    {
        $lastOrderId = $this->_checkoutSession->getLastOrderId();
		
        return $lastOrderId;
    }
	
	public function getOrder($orderId = null)
	{
		if($orderId === null) {
			$orderId = $this->getLastRealOrderId();
		}
		if ($orderId) {
			$order = $this->_orderRepository->get($orderId);
			return $order;
		}
	}

    public function getPostUrl($orderId = null)
    {
		$order                 = $this->getOrder($orderId);
		$additionalInformation = $order->getPayment()->getAdditionalInformation();
		
		return $additionalInformation['COMPLARO_PAYMENT_URL'];
    }
}