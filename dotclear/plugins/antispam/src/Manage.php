<?php
/**
 * @brief antispam, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\antispam;

use dcAuth;
use dcCore;
use dcNsProcess;
use dcPage;
use dt;
use Exception;
use form;
use html;
use http;

class Manage extends dcNsProcess
{
    public static function init(): bool
    {
        if (defined('DC_CONTEXT_ADMIN')) {
            dcPage::check(dcCore::app()->auth->makePermissions([
                dcAuth::PERMISSION_ADMIN,
            ]));

            self::$init = true;
        }

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        Antispam::initFilters();

        dcCore::app()->admin->filters     = Antispam::$filters->getFilters();
        dcCore::app()->admin->page_name   = __('Antispam');
        dcCore::app()->admin->filter_gui  = false;
        dcCore::app()->admin->default_tab = null;
        dcCore::app()->admin->filter      = null;

        try {
            // Show filter configuration GUI
            if (!empty($_GET['f'])) {
                if (!isset(dcCore::app()->admin->filters[$_GET['f']])) {
                    throw new Exception(__('Filter does not exist.'));
                }

                if (!dcCore::app()->admin->filters[$_GET['f']]->hasGUI()) {
                    throw new Exception(__('Filter has no user interface.'));
                }

                dcCore::app()->admin->filter     = dcCore::app()->admin->filters[$_GET['f']];
                dcCore::app()->admin->filter_gui = dcCore::app()->admin->filter->gui(dcCore::app()->admin->filter->guiURL());
            }

            // Remove all spam
            if (!empty($_POST['delete_all'])) {
                $ts = dt::str('%Y-%m-%d %H:%M:%S', $_POST['ts'], dcCore::app()->blog->settings->system->blog_timezone);

                Antispam::delAllSpam($ts);

                dcPage::addSuccessNotice(__('Spam comments have been successfully deleted.'));
                http::redirect(dcCore::app()->admin->getPageURL());
            }

            // Update filters
            if (isset($_POST['filters_upd'])) {
                $filters_opt = [];
                $i           = 0;
                foreach (dcCore::app()->admin->filters as $fid => $f) {
                    $filters_opt[$fid] = [false, $i];
                    $i++;
                }

                // Enable active filters
                if (isset($_POST['filters_active']) && is_array($_POST['filters_active'])) {
                    foreach ($_POST['filters_active'] as $v) {
                        $filters_opt[$v][0] = true;
                    }
                }

                // Order filters
                if (!empty($_POST['f_order']) && empty($_POST['filters_order'])) {
                    $order = $_POST['f_order'];
                    asort($order);
                    $order = array_keys($order);
                } elseif (!empty($_POST['filters_order'])) {
                    $order = explode(',', trim((string) $_POST['filters_order'], ','));
                }

                if (isset($order)) {
                    foreach ($order as $i => $f) {
                        $filters_opt[$f][1] = $i;
                    }
                }

                // Set auto delete flag
                if (isset($_POST['filters_auto_del']) && is_array($_POST['filters_auto_del'])) {
                    foreach ($_POST['filters_auto_del'] as $v) {
                        $filters_opt[$v][2] = true;
                    }
                }

                Antispam::$filters->saveFilterOpts($filters_opt);

                dcPage::addSuccessNotice(__('Filters configuration has been successfully saved.'));
                http::redirect(dcCore::app()->admin->getPageURL());
            }
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }

    public static function render(): void
    {
        if (!self::$init) {
            return;
        }

        echo
        '<html>' .
        '<head>' .
        '<title>' .
        (dcCore::app()->admin->filter_gui !== false ?
            sprintf(__('%s configuration'), dcCore::app()->admin->filter->name) . ' - ' :
            '' . dcCore::app()->admin->page_name) . '</title>' .
        dcPage::jsPageTabs(dcCore::app()->admin->default_tab);

        if (!dcCore::app()->auth->user_prefs->accessibility->nodragdrop) {
            echo
            dcPage::jsLoad('js/jquery/jquery-ui.custom.js') .
            dcPage::jsLoad('js/jquery/jquery.ui.touch-punch.js');
        }
        echo
        dcPage::jsJson('antispam', ['confirm_spam_delete' => __('Are you sure you want to delete all spams?')]) .
        dcPage::jsModuleLoad('antispam/js/antispam.js') .
        dcPage::cssModuleLoad('antispam/css/style.css') .
        '</head>' .
        '<body>';

        if (dcCore::app()->admin->filter_gui !== false) {
            echo
            dcPage::breadcrumb(
                [
                    __('Plugins')                                                              => '',
                    dcCore::app()->admin->page_name                                            => dcCore::app()->admin->getPageURL(),
                    sprintf(__('%s filter configuration'), dcCore::app()->admin->filter->name) => '',
                ]
            ) .
            dcPage::notices() .
            '<p><a href="' . dcCore::app()->admin->getPageURL() . '" class="back">' . __('Back to filters list') . '</a></p>' .

            dcCore::app()->admin->filter_gui;

            if (dcCore::app()->admin->filter->help) {
                dcPage::helpBlock(dcCore::app()->admin->filter->help);
            }
        } else {
            echo
            dcPage::breadcrumb(
                [
                    __('Plugins')                   => '',
                    dcCore::app()->admin->page_name => '',
                ]
            ) .
            dcPage::notices();

            # Information
            $spam_count      = Antispam::countSpam();
            $published_count = Antispam::countPublishedComments();
            $moderationTTL   = dcCore::app()->blog->settings->antispam->antispam_moderation_ttl;

            echo
            '<form action="' . dcCore::app()->admin->getPageURL() . '" method="post" class="fieldset">' .
            '<h3>' . __('Information') . '</h3>' .
            '<ul class="spaminfo">' .
            '<li class="spamcount"><a href="' . dcCore::app()->adminurl->get('admin.comments', ['status' => '-2']) . '">' . __('Junk comments:') . '</a> ' .
            '<strong>' . $spam_count . '</strong></li>' .
            '<li class="hamcount"><a href="' . dcCore::app()->adminurl->get('admin.comments', ['status' => '1']) . '">' . __('Published comments:') . '</a> ' .
                $published_count . '</li>' .
            '</ul>';

            if ($spam_count > 0) {
                echo
                '<p>' . dcCore::app()->formNonce() .
                form::hidden('ts', time()) .
                '<input name="delete_all" class="delete" type="submit" value="' . __('Delete all spams') . '" /></p>';
            }
            if ($moderationTTL != null && $moderationTTL >= 0) {
                echo
                '<p>' . sprintf(__('All spam comments older than %s day(s) will be automatically deleted.'), $moderationTTL) . ' ' .
                sprintf(__('You can modify this duration in the %s'), '<a href="' . dcCore::app()->adminurl->get('admin.blog.pref') .
                '#antispam_moderation_ttl"> ' . __('Blog settings') . '</a>') .
                '.</p>';
            }
            echo
            '</form>' .

            // Filters
            '<form action="' . dcCore::app()->admin->getPageURL() . '" method="post" id="filters-list-form">';

            if (!empty($_GET['upd'])) {
                dcPage::success(__('Filters configuration has been successfully saved.'));
            }

            echo
            '<div class="table-outer">' .
            '<table class="dragable">' .
            '<caption class="as_h3">' . __('Available spam filters') . '</caption>' .
            '<thead><tr>' .
            '<th>' . __('Order') . '</th>' .
            '<th>' . __('Active') . '</th>' .
            '<th>' . __('Auto Del.') . '</th>' .
            '<th class="nowrap">' . __('Filter name') . '</th>' .
            '<th colspan="2">' . __('Description') . '</th>' .
            '</tr></thead>' .
            '<tbody id="filters-list" >';

            $i = 1;
            foreach (dcCore::app()->admin->filters as $fid => $f) {
                $gui_link = '&nbsp;';
                if ($f->hasGUI()) {
                    $gui_link = '<a href="' . html::escapeHTML($f->guiURL()) . '">' .
                        '<img src="images/edit-mini.png" alt="' . __('Filter configuration') . '" ' .
                        'title="' . __('Filter configuration') . '" /></a>';
                }

                echo
                '<tr class="line' . ($f->active ? '' : ' offline') . '" id="f_' . $fid . '">' .
                '<td class="handle">' . form::number(['f_order[' . $fid . ']'], [
                    'min'        => 1,
                    'max'        => is_countable(dcCore::app()->admin->filters) ? count(dcCore::app()->admin->filters) : 0,
                    'default'    => $i,
                    'class'      => 'position',
                    'extra_html' => 'title="' . __('position') . '"',
                ]) .
                '</td>' .
                '<td class="nowrap">' . form::checkbox(
                    ['filters_active[]'],
                    $fid,
                    [
                        'checked'    => $f->active,
                        'extra_html' => 'title="' . __('Active') . '"',
                    ]
                ) . '</td>' .
                '<td class="nowrap">' . form::checkbox(
                    ['filters_auto_del[]'],
                    $fid,
                    [
                        'checked'    => $f->auto_delete,
                        'extra_html' => 'title="' . __('Auto Del.') . '"',
                    ]
                ) . '</td>' .
                '<td class="nowrap" scope="row">' . $f->name . '</td>' .
                '<td class="maximal">' . $f->description . '</td>' .
                    '<td class="status">' . $gui_link . '</td>' .
                '</tr>';
                $i++;
            }
            echo
            '</tbody></table></div>' .
            '<p>' . form::hidden('filters_order', '') .
            dcCore::app()->formNonce() .
            '<input type="submit" name="filters_upd" value="' . __('Save') . '" />' .
            ' <input type="button" value="' . __('Cancel') . '" class="go-back reset hidden-if-no-js" />' .
            '</p>' .
            '</form>';

            // Syndication
            if (DC_ADMIN_URL) {
                $ham_feed = dcCore::app()->blog->url . dcCore::app()->url->getURLFor(
                    'hamfeed',
                    Antispam::getUserCode()
                );
                $spam_feed = dcCore::app()->blog->url . dcCore::app()->url->getURLFor(
                    'spamfeed',
                    Antispam::getUserCode()
                );

                echo
                '<h3>' . __('Syndication') . '</h3>' .
                '<ul class="spaminfo">' .
                '<li class="feed"><a href="' . $spam_feed . '">' . __('Junk comments RSS feed') . '</a></li>' .
                '<li class="feed"><a href="' . $ham_feed . '">' . __('Published comments RSS feed') . '</a></li>' .
                '</ul>';
            }

            dcPage::helpBlock('antispam', 'antispam-filters');
        }

        echo
        '</body>' .
        '</html>';
    }
}
