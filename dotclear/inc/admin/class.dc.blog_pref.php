<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class dcAdminBlogPref
{
    /**
     * JS Popup helper for static home linked to an entry
     *
     * @param      string  $plugin_id  Plugin id (or admin URL)
     *
     * @return     mixed
     */
    public static function adminPopupPosts(string $plugin_id = '')
    {
        if (empty($plugin_id) || $plugin_id != 'admin.blog_pref') {
            return;
        }

        return
        dcPage::jsJson('admin.blog_pref', [
            'base_url' => dcCore::app()->blog->url,
        ]) .
        dcPage::jsLoad('js/_blog_pref_popup_posts.js');
    }
}
