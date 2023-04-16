<?php
/**
 * @brief widgets, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\widgets;

use dcCore;
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        $module     = basename(dirname(__DIR__));
        self::$init = defined('DC_CONTEXT_ADMIN') && dcCore::app()->newVersion($module, dcCore::app()->plugins->moduleInfo($module, 'version'));

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        if (class_exists(__NAMESPACE__ . '\Widgets')) {
            Widgets::init();
        } else {
            throw new Exception(__('Unable to initialize default widgets.'));
        }

        $s = dcCore::app()->blog->settings->get('widgets');
        if ($s->widgets_nav != null) {
            $s->put('widgets_nav', WidgetsStack::load($s->widgets_nav)->store());
        } else {
            $s->put('widgets_nav', '', 'string', 'Navigation widgets', false);
        }
        if ($s->widgets_extra != null) {
            $s->put('widgets_extra', WidgetsStack::load($s->widgets_extra)->store());
        } else {
            $s->put('widgets_extra', '', 'string', 'Extra widgets', false);
        }
        if ($s->widgets_custom != null) {
            $s->put('widgets_custom', WidgetsStack::load($s->widgets_custom)->store());
        } else {
            $s->put('widgets_custom', '', 'string', 'Custom widgets', false);
        }

        return true;
    }
}
