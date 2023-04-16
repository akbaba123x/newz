<?php
/**
 * @brief dcCKEditor, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\dcCKEditor;

use ArrayObject;
use dcPage;
use dcCore;

class BackendBehaviors
{
    /**
     * Plugin URL
     *
     * @var        string
     */
    protected static $p_url = 'index.php?pf=dcCKEditor';

    /**
     * PLugin config URL
     *
     * @var        string
     */
    protected static $config_url = 'plugin.php?p=dcCKEditor&config=1';

    /**
     * adminPostEditor add javascript to the DOM to load ckeditor depending on context
     *
     * @param      string  $editor   The wanted editor
     * @param      string  $context  The page context (post,page,comment,event,...)
     * @param      array   $tags     The array of ids to inject editor
     * @param      string  $syntax   The wanted syntax (wiki,markdown,...)
     *
     * @return     string
     */
    public static function adminPostEditor(string $editor = '', string $context = '', array $tags = [], string $syntax = 'xhtml'): string
    {
        if (empty($editor) || $editor !== 'dcCKEditor' || $syntax !== 'xhtml') {
            return '';
        }

        $config_js = self::$config_url;
        if (!empty($context)) {
            $config_js .= '&context=' . $context;
        }

        return
        dcPage::jsJson('ck_editor_ctx', [
            'ckeditor_context'      => $context,
            'ckeditor_tags_context' => [$context => $tags],
            'admin_base_url'        => DC_ADMIN_URL,
            'base_url'              => dcCore::app()->blog->host,
            'dcckeditor_plugin_url' => DC_ADMIN_URL . self::$p_url,
            'user_language'         => dcCore::app()->auth->getInfo('user_lang'),
        ]) .
        dcPage::jsJson('ck_editor_var', [
            'CKEDITOR_BASEPATH' => DC_ADMIN_URL . self::$p_url . '/js/ckeditor/',
        ]) .
        dcPage::jsJson('ck_editor_msg', [
            'img_select_title'     => __('Media chooser'),
            'img_select_accesskey' => __('m'),
            'post_link_title'      => __('Link to an entry'),
            'link_title'           => __('Link'),
            'link_accesskey'       => __('l'),
            'img_title'            => __('External image'),
            'url_cannot_be_empty'  => __('URL field cannot be empty.'),
        ]) .
        dcPage::jsLoad(self::$p_url . '/js/_post_editor.js') .
        dcPage::jsLoad(self::$p_url . '/js/ckeditor/ckeditor.js') .
        dcPage::jsLoad(self::$p_url . '/js/ckeditor/adapters/jquery.js') .
        dcPage::jsLoad($config_js);
    }

    /**
     * Load additional script for media insertion popup
     *
     * @param      string  $editor  The editor
     *
     * @return     string
     */
    public static function adminPopupMedia(string $editor = ''): string
    {
        if (empty($editor) || $editor !== 'dcCKEditor') {
            return '';
        }

        return dcPage::jsLoad(self::$p_url . '/js/popup_media.js');
    }

    /**
     * Load additional script for link insertion popup
     *
     * @param      string  $editor  The editor
     *
     * @return     string
     */
    public static function adminPopupLink(string $editor = ''): string
    {
        if (empty($editor) || $editor !== 'dcCKEditor') {
            return '';
        }

        return dcPage::jsLoad(self::$p_url . '/js/popup_link.js');
    }

    /**
     * Load additional script for entry link insertion popup
     *
     * @param      string  $editor  The editor
     *
     * @return     string
     */
    public static function adminPopupPosts(string $editor = ''): string
    {
        if (empty($editor) || $editor !== 'dcCKEditor') {
            return '';
        }

        return dcPage::jsLoad(self::$p_url . '/js/popup_posts.js');
    }

    /**
     * Add some CSP headers
     *
     * CKEditor uses inline CSS styles, inline JS scripts and even uses eval() javascript function, so…
     *
     * @param      ArrayObject  $csp    The csp
     */
    public static function adminPageHTTPHeaderCSP(ArrayObject $csp): void
    {
        // add 'unsafe-inline' for CSS, add 'unsafe-eval' for scripts as far as CKEditor 4.x is used
        if (strpos($csp['style-src'], 'unsafe-inline') === false) {
            $csp['style-src'] .= " 'unsafe-inline'";
        }
        if (strpos($csp['script-src'], 'unsafe-inline') === false) {
            $csp['script-src'] .= " 'unsafe-inline'";
        }
        if (strpos($csp['script-src'], 'unsafe-eval') === false) {
            $csp['script-src'] .= " 'unsafe-eval'";
        }
    }
}
