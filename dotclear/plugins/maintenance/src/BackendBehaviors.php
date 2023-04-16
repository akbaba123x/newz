<?php
/**
 * @brief Maintenance plugin admin class.
 *
 * Group of methods used on behaviors.
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\maintenance;

use ArrayObject;
use dcAdminHelper;
use dcAuth;
use dcCore;
use dcFavorites;
use dcPage;
use dt;
use form;

class BackendBehaviors
{
    /**
     * Register default tasks.
     *
     * @param      Maintenance  $maintenance  Maintenance instance
     */
    public static function dcMaintenanceInit(Maintenance $maintenance): void
    {
        dcCore::app()->autoload->addNamespace(__NAMESPACE__ . '\Task', __DIR__ . DIRECTORY_SEPARATOR . 'Task');

        $maintenance
            ->addTab('maintenance', __('Servicing'), ['summary' => __('Tools to maintain the performance of your blogs.')])
            ->addTab('backup', __('Backup'), ['summary' => __('Tools to back up your content.')])
            ->addTab('dev', __('Development'), ['summary' => __('Tools to assist in development of plugins, themes and core.')])

            ->addGroup('optimize', __('Optimize'))
            ->addGroup('index', __('Count and index'))
            ->addGroup('purge', __('Purge'))
            ->addGroup('other', __('Other'))
            ->addGroup('zipblog', __('Current blog'))
            ->addGroup('zipfull', __('All blogs'))

            ->addGroup('l10n', __('Translations'), ['summary' => __('Maintain translations')])

            ->addTask(Task\Cache::class)
            ->addTask(Task\CSP::class)
            ->addTask(Task\IndexPosts::class)
            ->addTask(Task\IndexComments::class)
            ->addTask(Task\CountComments::class)
            ->addTask(Task\SynchPostsMeta::class)
            ->addTask(Task\Logs::class)
            ->addTask(Task\Vacuum::class)
            ->addTask(Task\ZipMedia::class)
            ->addTask(Task\ZipTheme::class)
        ;
    }

    /**
     * Favorites
     *
     * @param      dcFavorites   $favs   favs
     */
    public static function adminDashboardFavorites(dcFavorites $favs): void
    {
        $favs->register('maintenance', [
            'title'        => __('Maintenance'),
            'url'          => dcCore::app()->adminurl->get('admin.plugin.maintenance'),
            'small-icon'   => [dcPage::getPF('maintenance/icon.svg'),dcPage::getPF('maintenance/icon-dark.svg')],
            'large-icon'   => [dcPage::getPF('maintenance/icon.svg'),dcPage::getPF('maintenance/icon-dark.svg')],
            'permissions'  => dcCore::app()->auth->makePermissions([
                dcAuth::PERMISSION_ADMIN,
            ]),
            'active_cb'    => [self::class, 'adminDashboardFavoritesActive'],
            'dashboard_cb' => [self::class, 'adminDashboardFavoritesCallback'],
        ]);
    }

    /**
     * Is maintenance plugin active
     *
     * @param      string  $request  The request
     * @param      array   $params   The parameters
     *
     * @return     bool    true if maintenance plugin is active else false
     */
    public static function adminDashboardFavoritesActive($request, $params): bool
    {
        return $request == 'plugin.php' && isset($params['p']) && $params['p'] == 'maintenance';
    }

    /**
     * Favorites hack.
     *
     * This updates maintenance fav icon text
     * if there are tasks required maintenance.
     *
     * @param      arrayObject  $icon    The icon
     */
    public static function adminDashboardFavoritesCallback(ArrayObject $icon): void
    {
        // Check user option
        if (!dcCore::app()->auth->user_prefs->maintenance->dashboard_icon) {
            return;
        }

        // Check expired tasks
        $maintenance = new Maintenance();
        $count       = 0;
        foreach ($maintenance->getTasks() as $t) {
            if ($t->expired() !== false) {
                $count++;
            }
        }

        if (!$count) {
            return;
        }

        $icon['title'] .= '<br />' . sprintf(__('One task to execute', '%s tasks to execute', $count), $count);
        $icon['large-icon'] = [dcPage::getPF('maintenance/icon-update.svg'), dcPage::getPF('maintenance/icon-dark-update.svg')];
    }

    /**
     * Dashboard items stack.
     *
     * @param      arrayObject  $items  The items
     */
    public static function adminDashboardItems(ArrayObject $items): void
    {
        if (!dcCore::app()->auth->user_prefs->maintenance->dashboard_item) {
            return;
        }

        $maintenance = new Maintenance();

        $lines = [];
        foreach ($maintenance->getTasks() as $t) {
            $ts = $t->expired();
            if ($ts === false) {
                continue;
            }

            $lines[] = '<li title="' . (
                $ts === null ?
                __('This task has never been executed.')
                :
                sprintf(
                    __('Last execution of this task was on %s.'),
                    dt::dt2str(dcCore::app()->blog->settings->system->date_format, (string) $ts) . ' ' .
                    dt::dt2str(dcCore::app()->blog->settings->system->time_format, (string) $ts)
                )
            ) . '">' . $t->task() . '</li>';
        }

        if (empty($lines)) {
            return;
        }

        $items[] = new ArrayObject([
            '<div id="maintenance-expired" class="box small"><h3>' .
            dcAdminHelper::adminIcon([dcPage::getPF('maintenance/icon.svg'),dcPage::getPF('maintenance/icon-dark.svg')], true, '', '', 'icon-small') . ' ' .
            __('Maintenance') . '</h3>' .
            '<p class="warning no-margin">' . sprintf(__('There is a task to execute.', 'There are %s tasks to execute.', count($lines)), count($lines)) . '</p>' .
            '<ul>' . implode('', $lines) . '</ul>' .
            '<p><a href="' . dcCore::app()->adminurl->get('admin.plugin.maintenance') . '">' . __('Manage tasks') . '</a></p>' .
            '</div>',
        ]);
    }

    /**
     * User preferences form.
     *
     * This add options for superadmin user
     * to show or not expired taks.
     */
    public static function adminDashboardOptionsForm(): void
    {
        echo
        '<div class="fieldset">' .
        '<h4>' . __('Maintenance') . '</h4>' .

        '<p><label for="maintenance_dashboard_icon" class="classic">' .
        form::checkbox('maintenance_dashboard_icon', 1, dcCore::app()->auth->user_prefs->maintenance->dashboard_icon) .
        __('Display overdue tasks counter on maintenance dashboard icon') . '</label></p>' .

        '<p><label for="maintenance_dashboard_item" class="classic">' .
        form::checkbox('maintenance_dashboard_item', 1, dcCore::app()->auth->user_prefs->maintenance->dashboard_item) .
        __('Display overdue tasks list on dashboard items') . '</label></p>' .

            '</div>';
    }

    /**
     * User preferences update.
     *
     * @param      string  $user_id  The user identifier
     */
    public static function adminAfterDashboardOptionsUpdate(?string $user_id = null): void
    {
        if (is_null($user_id)) {
            return;
        }

        dcCore::app()->auth->user_prefs->maintenance->put('dashboard_icon', !empty($_POST['maintenance_dashboard_icon']), 'boolean');
        dcCore::app()->auth->user_prefs->maintenance->put('dashboard_item', !empty($_POST['maintenance_dashboard_item']), 'boolean');
    }

    /**
     * Build a well sorted help for tasks.
     *
     * This method is not so good if used with lot of tranlsations
     * as it grows memory usage and translations files size,
     * it is better to use help ressource files
     * but keep it for exemple of how to use behavior adminPageHelpBlock.
     * Cheers, JC
     *
     * @param      arrayObject  $blocks  The blocks
     */
    public static function adminPageHelpBlock(ArrayObject $blocks): void
    {
        if (array_search('maintenancetasks', $blocks->getArrayCopy(), true) !== false) {
            $maintenance = new Maintenance();

            $res_tab = '';
            foreach ($maintenance->getTabs() as $tab_obj) {
                $res_group = '';
                foreach ($maintenance->getGroups() as $group_obj) {
                    $res_task = '';
                    foreach ($maintenance->getTasks() as $t) {
                        if ($t->group()  != $group_obj->id()
                            || $t->tab() != $tab_obj->id()) {
                            continue;
                        }
                        if (($desc = $t->description()) != '') {
                            $res_task .= '<dt>' . $t->task() . '</dt>' .
                                '<dd>' . $desc . '</dd>';
                        }
                    }
                    if (!empty($res_task)) {
                        $desc = $group_obj->description ?: $group_obj->summary;

                        $res_group .= '<h5>' . $group_obj->name() . '</h5>' .
                            ($desc ? '<p>' . $desc . '</p>' : '') .
                            '<dl>' . $res_task . '</dl>';
                    }
                }
                if (!empty($res_group)) {
                    $desc = $tab_obj->description ?: $tab_obj->summary;

                    $res_tab .= '<h4>' . $tab_obj->name() . '</h4>' .
                        ($desc ? '<p>' . $desc . '</p>' : '') .
                        $res_group;
                }
            }
            if (!empty($res_tab)) {
                $res          = new AdminPageHelpBlockContent();
                $res->content = $res_tab;
                $blocks->append($res);
            }
        }
    }

    /**
     * Add javascript for plugin configuration.
     *
     * @param      string  $module  The module
     *
     * @return     string
     */
    public static function pluginsToolsHeaders(string $module): string
    {
        if ($module === 'maintenance') {
            return dcPage::jsModuleLoad('maintenance/js/settings.js');
        }

        return '';
    }
}
