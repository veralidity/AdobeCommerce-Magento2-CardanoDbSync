<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Veralidity\CardanoDbSync\Block\Adminhtml\System\Config;

/**
 * OpenSearch test connection block
 */
class TestMainnetConnection extends \Veralidity\CardanoDbSync\Block\Adminhtml\System\Config\TestConnection
{
    /**
     * @inheritdoc
     */
    protected function _getFieldMapping(): array
    {
        $fields = [
            'host' => 'veraliditycardanodbsync_connection_mainnet_host',
            'port' => 'veraliditycardanodbsync_connection_mainnet_port',
            'dbname' => 'veraliditycardanodbsync_connection_mainnet_dbname',
            'username' => 'veraliditycardanodbsync_connection_mainnet_username',
            'password' => 'veraliditycardanodbsync_connection_mainnet_password'
        ];

        return array_merge(parent::_getFieldMapping(), $fields);
    }
}
