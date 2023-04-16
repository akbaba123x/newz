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
use StdClass;

class UtilsImport
{
    /**
     * Loads a file.
     *
     * @param      string  $file   The file
     *
     * @return     array|bool
     */
    public static function loadFile(string $file)
    {
        if (file_exists($file) && is_readable($file)) {
            if ($data = file_get_contents($file)) {
                if (preg_match('!<xbel(\s+version)?!', $data)) {
                    return self::parseXBEL($data);
                } elseif (preg_match('!<opml(\s+version)?!', $data)) {
                    return self::parseOPML($data);
                }

                throw new Exception(__('You need to provide a XBEL or OPML file.'));
            }
        }

        return false;
    }

    protected static function parseOPML(string $data): array
    {
        $xml = @simplexml_load_string($data);
        if (!$xml) {
            throw new Exception(__('File is not in XML format.'));
        }

        $outlines = $xml->xpath('//outline');

        $list = [];
        foreach ($outlines as $outline) {
            if (isset($outline['htmlUrl'])) {
                $link = $outline['htmlUrl'];
            } elseif (isset($outline['url'])) {
                $link = $outline['url'];
            } else {
                continue;
            }

            $entry = new StdClass();

            $entry->link  = $link;
            $entry->title = (!empty($outline['title'])) ? $outline['title'] : '';
            $entry->desc  = (!empty($outline['description'])) ? $outline['description'] : '';

            if (empty($entry->title)) {
                $entry->title = (!empty($outline['text'])) ? $outline['text'] : $entry->link;
            }

            $list[] = $entry;
        }

        return $list;
    }

    protected static function parseXBEL($data): array
    {
        $xml = @simplexml_load_string($data);
        if (!$xml) {
            throw new Exception(__('File is not in XML format.'));
        }

        $outlines = $xml->xpath('//bookmark');

        $list = [];
        foreach ($outlines as $outline) {
            if (!isset($outline['href'])) {
                continue;
            }

            $entry = new StdClass();

            $entry->link  = $outline['href'];
            $entry->title = (!empty($outline->title)) ? $outline->title : '';
            $entry->desc  = (!empty($outline->desc)) ? $outline->desc : '';

            if (empty($entry->title)) {
                $entry->title = $entry->link;
            }

            $list[] = $entry;
        }

        return $list;
    }
}
