<?php
/**
 * @brief pings, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\pings;

use ArrayObject;
use dcCore;
use Exception;
use form;
use html;

if (!defined('DC_CONTEXT_ADMIN')) {
    return false;
}

class BackendBehaviors
{
    /**
     * Add attachment fieldset in entry sidebar
     *
     * @param      ArrayObject  $main     The main part of the entry form
     * @param      ArrayObject  $sidebar  The sidebar part of the entry form
     */
    public static function pingsFormItems(ArrayObject $main, ArrayObject $sidebar)
    {
        if (!dcCore::app()->blog->settings->pings->pings_active) {
            return;
        }

        $pings_uris = dcCore::app()->blog->settings->pings->pings_uris;
        if (empty($pings_uris) || !is_array($pings_uris)) {
            return;
        }

        if (!empty($_POST['pings_do']) && is_array($_POST['pings_do'])) {
            $pings_do = $_POST['pings_do'];
        } else {
            $pings_do = [];
        }

        $item = '<h5 class="ping-services">' . __('Pings') . '</h5>';
        $i    = 0;
        foreach ($pings_uris as $k => $v) {
            $item .= '<p class="ping-services"><label for="pings_do-' . $i . '" class="classic">' .
            form::checkbox(['pings_do[]', 'pings_do-' . $i], html::escapeHTML($v), in_array($v, $pings_do), 'check-ping-services') . ' ' .
            html::escapeHTML($k) . '</label></p>';
            $i++;
        }
        $sidebar['options-box']['items']['pings'] = $item;
    }

    /**
     * Does pings.
     */
    public static function doPings()
    {
        if (empty($_POST['pings_do']) || !is_array($_POST['pings_do'])) {
            return;
        }

        if (!dcCore::app()->blog->settings->pings->pings_active) {
            return;
        }

        $pings_uris = dcCore::app()->blog->settings->pings->pings_uris;
        if (empty($pings_uris) || !is_array($pings_uris)) {
            return;
        }

        foreach ($_POST['pings_do'] as $uri) {
            if (in_array($uri, $pings_uris)) {
                try {
                    PingsAPI::doPings($uri, dcCore::app()->blog->name, dcCore::app()->blog->url);
                } catch (Exception $e) {
                }
            }
        }
    }
}
