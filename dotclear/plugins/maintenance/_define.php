<?php
/**
 * @brief maintenance, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
$this->registerModule(
    'Maintenance',                            // Name
    'Maintain your installation',             // Description
    'Olivier Meunier & Association Dotclear', // Author
    '2.0',                                    // Version
    [
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_ADMIN,
        ]),
        'type'        => 'plugin',
        'settings'    => [
            'self' => '#settings',
        ],
    ]
);
