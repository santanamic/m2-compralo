<?php
namespace Compralo\Payments\Block\Payment;
class Redirect extends \Magento\Framework\View\Element\Template
{
    public $customerSession;
    public $logger;
    protected $_objectManager;
    protected $_helper;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context    $context,
        \Magento\Customer\Model\Session                     $customerSession,
        \Magento\Framework\ObjectManagerInterface           $_objectManager,
        \Compralo\Payments\Helper\Data                      $_helper,
        \Psr\Log\LoggerInterface                            $logger,
        array                                               $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession  = $customerSession;
        $this->_objectManager   = $_objectManager;
        $this->_helper          = $_helper;
        $this->logger           = $logger;
    }
    public function getPostUrl()
    {
        return $this->_helper->getPostUrl();
    }
}