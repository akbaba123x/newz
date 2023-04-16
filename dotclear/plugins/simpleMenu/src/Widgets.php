<?php
/**
 * @brief simpleMenu, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\simpleMenu;

use Dotclear\Plugin\widgets\WidgetsStack;

class Widgets
{
    /**
     * Add simple menu widget
     *
     * @param      WidgetsStack  $widgets  The widgets
     */
    public static function initWidgets(WidgetsStack $widgets): void
    {
        $widgets
            ->create('simplemenu', __('Simple menu'), [FrontendTemplate::class, 'simpleMenuWidget'], null, 'List of simple menu items')
            ->addTitle(__('Menu'))
            ->setting(
                'description',
                __('Item description'),
                0,
                'combo',
                [
                    __('Displayed in link')                   => 0, // span
                    __('Used as link title')                  => 1, // title
                    __('Displayed in link and used as title') => 2, // both
                    __('Not displayed nor used')              => 3, // none
                ]
            )
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();
    }
}
