<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Veralidity\CardanoDbSync\Controller\Adminhtml\Cardanodbsync\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Veralidity\CardanoDbSync\Helper\Data as Helper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filter\StripTags;

class TestConnection extends Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Veralidity_CardanoDbSync::config_integrations_cardanodbsync';

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var StripTags
     */
    private $tagFilter;

    /**
     * @param Context           $context
     * @param Helper            $helper
     * @param JsonFactory       $resultJsonFactory
     * @param StripTags         $tagFilter
     */
    public function __construct(
        Context $context,
        Helper $helper,
        JsonFactory $resultJsonFactory,
        StripTags $tagFilter
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->tagFilter = $tagFilter;
    }

    /**
     * Check for connection to server
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = [
            'success' => false,
            'errorMessage' => '',
        ];
        $options = $this->getRequest()->getParams();

        try {
            //var_dump($options);
            if (empty($options['host'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missing database host parameter.')
                );
            }
            if (empty($options['dbname'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missing database name parameter.')
                );
            }
            if (empty($options['username'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missing database username parameter.')
                );
            }
            if (empty($options['password'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missing database password parameter.')
                );
            }

            $connection = $this->helper->testConnection($options);
            if ($connection) {
                $result['success'] = true;
                $result['cardano_dbsync'] = $this->helper->getSyncPercent($connection);
                $result['cardano_blocks'] = $this->helper->getBlocksLatest($connection);
            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result['errorMessage'] = $e->getMessage();
        } catch (\Exception $e) {
            $message = __($e->getMessage());
            $result['errorMessage'] = $this->tagFilter->filter($message);
        }

        if (!empty($result['errorMessage'])) {
            if (strpos($result['errorMessage'], 'Name or service not known') !== false) {
                $result['errorMessage'] = "The database host cannot be found or is unknown.";
            }
            if (strpos($result['errorMessage'], 'no such user') !== false) {
                $result['errorMessage'] = "The database username does not exist or does not have access to the database.";
            }
            if (strpos($result['errorMessage'], 'SASL authentication failed') !== false) {
                $result['errorMessage'] = "The database  password is incorrect.";
            }
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
