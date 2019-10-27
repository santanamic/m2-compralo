<?php

namespace Compralo\Payments\Controller\Payment;

//use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class Postback extends \Magento\Framework\App\Action\Action //implements HttpPostActionInterface
{
    protected $_context;
	protected $_helper;
    protected $_order;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\Order $order,
		\Compralo\Payments\Helper\Data $helper,
		
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
       \Magento\Framework\App\Cache\StateInterface $cacheState,
       \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
       \Magento\Framework\View\Result\PageFactory $resultPageFactory,
       \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_context = $context;
        $this->_order   = $order;
		$this->_helper  = $helper;
        
		$this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;

		parent::__construct($context);
		
    }
	
    public function execute()
    {
		try {
            $paramsData = $this->getRequest()->getParams();
			$body      = $this->getRequest()->getContent(); 
			
            if ( isset($paramsData['order_id']) ) {
				
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$this->_order->loadByIncrementId($paramsData['order_id']);
				
				$orderRealId           = $this->_order->getId();
				$order                 = $this->_helper->getOrder($orderRealId);
				$additionalInformation = $order->getPayment()->getAdditionalInformation();
				$orderToken            = $additionalInformation['COMPLARO_PAYMENT_TOKEN'];
				$body                  = json_decode($body, true);
				
				if( $orderToken === $body['token']
					&& ( @$body['status_id'] == 1 || @$body['status'] == 1 ) ){
						$order->addStatusHistoryComment(__('Payment confirmed by Compralo.'));
						$order->setStatus('payment_review');
						$order->save();
						
					// send order email
					$emailSender = $objectManager->create('\Magento\Sales\Model\Order\Email\Sender\OrderSender');
					$emailSender->send($this->_order);
				}
				exit;
            } else {
               $this->_redirect('/');
            }
        } catch (Exception $e) {
            echo $e;
        }
	}
}