<?php
/**
 * @brief dcProxyV2, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
$this->registerModule(
    'dcProxyV2',
    'Cope with function/method footprint V1 (< 2.24, 2.25)',
    'Franck Paul',
    '2.0',
    [
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_USAGE,
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
        'type'     => 'plugin',
        'priority' => 99_999_999_998,
    ]
);
