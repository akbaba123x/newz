<?php
/**
 * @brief blogroll, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\blogroll;

use Exception;
use dcCore;
use dcUrlHandlers;
use html;
use http;

class FrontendUrl extends dcUrlHandlers
{
    /**
     * Get blogroll XBEL
     *
     * @param      array  $args   The arguments
     */
    public static function xbel(?array $args)
    {
        $blogroll = new Blogroll(dcCore::app()->blog);

        try {
            $links = $blogroll->getLinks();
        } catch (Exception $e) {
            self::p404();
        }

        if ($args) {
            // We don't expect any URL query
            self::p404();
        }

        http::cache(dcCore::app()->cache['mod_files'], dcCore::app()->cache['mod_ts']);

        header('Content-Type: text/xml; charset=UTF-8');

        echo
        '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
        '<!DOCTYPE xbel PUBLIC "+//IDN python.org//DTD XML Bookmark Exchange Language 1.0//EN//XML"' . "\n" .
        '"http://www.python.org/topics/xml/dtds/xbel-1.0.dtd">' . "\n" .
        '<xbel version="1.0">' . "\n" .
        '<title>' . html::escapeHTML(dcCore::app()->blog->name) . ' blogroll</title>' . "\n";

        $i = 1;
        foreach ($blogroll->getLinksHierarchy($links) as $cat_title => $links) {
            if ($cat_title != '') {
                echo
                '<folder>' . "\n" .
                '<title>' . html::escapeHTML($cat_title) . '</title>' . "\n";
            }

            foreach ($links as $v) {
                $lang = $v['link_lang'] ? ' xml:lang="' . $v['link_lang'] . '"' : '';

                echo
                '<bookmark href="' . $v['link_href'] . '"' . $lang . '>' . "\n" .
                '<title>' . html::escapeHTML($v['link_title']) . '</title>' . "\n";

                if ($v['link_desc']) {
                    echo
                    '<desc>' . html::escapeHTML($v['link_desc']) . '</desc>' . "\n";
                }

                if ($v['link_xfn']) {
                    echo
                    '<info>' . "\n" .
                    '<metadata owner="http://gmpg.org/xfn/">' . $v['link_xfn'] . '</metadata>' . "\n" .
                    '</info>' . "\n";
                }

                echo
                '</bookmark>' . "\n";
            }

            if ($cat_title != '') {
                echo
                '</folder>' . "\n";
            }

            $i++;
        }

        echo
        '</xbel>';
    }
}
