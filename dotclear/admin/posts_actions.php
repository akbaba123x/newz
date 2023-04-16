<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 *
 * @deprecated It is only used for plugins compatibility
 */

/* ### THIS FILE IS DEPRECATED                     ### */
/* ### IT IS ONLY USED FOR PLUGINS COMPATIBILITY ### */

require __DIR__ . '/../inc/admin/prepend.php';

class adminPostsActions
{
    /**
     * Initializes the page.
     */
    public static function init()
    {
        $args = [];
        dcPage::check(dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_USAGE,
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]));

        if (isset($_REQUEST['redir'])) {
            $url_parts = explode('?', $_REQUEST['redir']);
            $base_url  = $url_parts[0];
            if (isset($url_parts[1])) {
                parse_str($url_parts[1], $args);
            }
            $args['redir'] = $_REQUEST['redir'];
        } else {
            $base_url = dcCore::app()->adminurl->get('admin.posts');
            $args     = [];
        }

        $posts_actions_page = new dcPostsActions($base_url, $args);
        $posts_actions_page->setEnableRedirSelection(false);
        $posts_actions_page->process();
    }
}

adminPostsActions::init();
