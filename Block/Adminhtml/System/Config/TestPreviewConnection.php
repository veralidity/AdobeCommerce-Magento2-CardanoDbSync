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
class TestPreviewConnection extends \Veralidity\CardanoDbSync\Block\Adminhtml\System\Config\TestConnection
{
    /**
     * @inheritdoc
     */
    protected function _getFieldMapping(): array
    {
        $fields = [
            'host' => 'veraliditycardanodbsync_connection_preview_host',
            'port' => 'veraliditycardanodbsync_connection_preview_port',
            'dbname' => 'veraliditycardanodbsync_connection_preview_dbname',
            'username' => 'veraliditycardanodbsync_connection_preview_username',
            'password' => 'veraliditycardanodbsync_connection_preview_password'
        ];

        return array_merge(parent::_getFieldMapping(), $fields);
    }
}
