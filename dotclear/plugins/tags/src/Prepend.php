<?php
/**
 * @brief tags, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\tags;

use dcCore;
use dcNsProcess;

class Prepend extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_RC_PATH');

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        dcCore::app()->url->register('tag', 'tag', '^tag/(.+)$', [FrontendUrl::class, 'tag']);
        dcCore::app()->url->register('tags', 'tags', '^tags$', [FrontendUrl::class, 'tags']);
        dcCore::app()->url->register('tag_feed', 'feed/tag', '^feed/tag/(.+)$', [FrontendUrl::class, 'tagFeed']);

        dcCore::app()->addBehavior('coreInitWikiPost', [BackendBehaviors::class, 'coreInitWikiPost']);

        return true;
    }
}
