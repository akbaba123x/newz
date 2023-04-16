<?php
/**
 * @brief pages, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\pages;

use dcBlog;
use dcCore;
use dcNsProcess;

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

        if (dcCore::app()->getVersion('pages') == null) {
            // Create a first pending page, only on a new installation of this plugin
            $params = [
                'post_type'  => 'page',
                'no_content' => true,
            ];
            $counter = dcCore::app()->blog->getPosts($params, true);

            if ($counter->f(0) == 0 && dcCore::app()->blog->settings->pages->firstpage == null) {
                dcCore::app()->blog->settings->pages->put('firstpage', true, 'boolean');

                $cur                     = dcCore::app()->con->openCursor(dcCore::app()->prefix . dcBlog::POST_TABLE_NAME);
                $cur->user_id            = dcCore::app()->auth->userID();
                $cur->post_type          = 'page';
                $cur->post_format        = 'xhtml';
                $cur->post_lang          = dcCore::app()->blog->settings->system->lang;
                $cur->post_title         = __('My first page');
                $cur->post_content       = '<p>' . __('This is your first page. When you\'re ready to blog, log in to edit or delete it.') . '</p>';
                $cur->post_content_xhtml = $cur->post_content;
                $cur->post_excerpt       = '';
                $cur->post_excerpt_xhtml = $cur->post_excerpt;
                $cur->post_status        = dcBlog::POST_PENDING; // Pending status
                $cur->post_open_comment  = 0;
                $cur->post_open_tb       = 0;
                $post_id                 = dcCore::app()->blog->addPost($cur);
            }
        }

        return true;
    }
}
