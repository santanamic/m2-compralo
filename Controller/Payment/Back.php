<?php

namespace Compralo\Payments\Controller\Payment;

class Back extends \Magento\Framework\App\Action\Action
{
    public $context;
    protected $_order;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\Order $_order,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->context = $context;
        $this->_order = $_order;
        $this->resultPageFactory = $resultPageFactory;
       
	   parent::__construct($context);
    }

    public function execute()
    {
		try {
            $paramData = $this->getRequest()->getParams();

            if (isset($paramData['order_id'])) {
			
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $this->_order->loadByIncrementId($paramData['order_id']);
				$orderRealId = $this->_order->getId();
				$this->_redirect('sales/order/view/order_id/' . $orderRealId);

            } else {
                $this->_redirect('/');
            }
        } catch (Exception $e) {
            echo $e;
        }
	}
}