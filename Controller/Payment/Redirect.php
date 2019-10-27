<?php
namespace Compralo\Payments\Controller\Payment;
class Redirect extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $orderRepository;
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
		$this->resultPageFactory = $resultPageFactory;
		$this->orderRepository = $orderRepository;
		
        parent::__construct($context);
    }
	
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Loading...'));

        $block = $resultPage->getLayout()
                ->createBlock('Compralo\Payments\Block\Payment\Redirect')
                ->setTemplate('Compralo_Payments::payment/redirect.phtml')
                ->toHtml();
        //$this->getResponse()->setBody($block);

        return $this->resultPageFactory->create();	
    }
}