<?php
namespace Veralidity\CardanoDbSync\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Veralidity\CardanoDbSync\Model\TestConnection2;

class Index extends Action
{
    protected $pageFactory;
    protected $testConnection2;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        TestConnection2 $testConnection2
    ) {
        $this->pageFactory = $pageFactory;
        $this->testConnection2 = $testConnection2;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Cardano DbSync PostgreSQL - PREPROD'));

        // Call your model class methods and pass the result to the view
        //$testConnectionResult = $this->testConnection2->test();
        $resultPage->getLayout()->getBlock('cardano.testconnection');
        //$resultPage->setData('test_connection_result', $testConnectionResult);

        return $resultPage;
    }
}
