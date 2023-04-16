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
declare(strict_types=1);

namespace Dotclear\Plugin\maintenance;

use dcAdmin;
use dcAuth;
use dcCore;
use dcNsProcess;
use dcPage;

class Backend extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_CONTEXT_ADMIN');

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        // Sidebar menu
        dcCore::app()->menu[dcAdmin::MENU_PLUGINS]->addItem(
            __('Maintenance'),
            dcCore::app()->adminurl->get('admin.plugin.maintenance'),
            dcPage::getPF('maintenance/icon.svg'),
            preg_match('/' . preg_quote(dcCore::app()->adminurl->get('admin.plugin.maintenance')) . '(&.*)?$/', $_SERVER['REQUEST_URI']),
            dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
                dcAuth::PERMISSION_ADMIN,
            ]), dcCore::app()->blog->id)
        );

        // Admin behaviors
        dcCore::app()->addBehaviors([
            'dcMaintenanceInit'                => [BackendBehaviors::class, 'dcMaintenanceInit'],
            'adminDashboardFavoritesV2'        => [BackendBehaviors::class, 'adminDashboardFavorites'],
            'adminDashboardContentsV2'         => [BackendBehaviors::class, 'adminDashboardItems'],
            'adminDashboardOptionsFormV2'      => [BackendBehaviors::class, 'adminDashboardOptionsForm'],
            'adminAfterDashboardOptionsUpdate' => [BackendBehaviors::class, 'adminAfterDashboardOptionsUpdate'],
            'adminPageHelpBlock'               => [BackendBehaviors::class, 'adminPageHelpBlock'],
            'pluginsToolsHeadersV2'            => [BackendBehaviors::class, 'pluginsToolsHeaders'],
        ]);

        // Rest method
        dcCore::app()->rest->addFunction('dcMaintenanceStep', [Rest::class, 'step']);

        return true;
    }
}
