<?php
/**
 * @brief importExport, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\importExport;

use Exception;
use dcBlog;
use dcCore;
use dcMeta;
use dcPage;
use feedReader;
use dt;
use form;
use html;
use http;
use text;

class ModuleImportFeed extends Module
{
    /**
     * Current status
     *
     * @var        bool
     */
    protected $status = false;

    /**
     * Feed URL
     *
     * @var        string
     */
    protected $feed_url = '';

    /**
     * get AAAA record for $host
     *
     * If $try_a is true, if AAAA fails, it tries for A
     * The first match found is returned otherwise returns false
     *
     * IPv6 functions (from https://gist.github.com/tbaschak/7866688)
     *
     * @param      string  $host   The host
     * @param      bool    $try_a  The try a
     *
     * @return     bool|string
     */
    private function gethostbyname6(string $host, bool $try_a = false)
    {
        $dns = $this->gethostbynamel6($host, $try_a);
        if (!$dns) {
            return false;
        }

        return $dns[0];
    }

    /**
     * get AAAA records for $host
     *
     * If $try_a is true, if AAAA fails, it tries for A
     * Results are returned in an array of ips found matching type otherwise returns false
     *
     * @param      string      $host   The host
     * @param      bool        $try_a  The try a
     *
     * @return     array|bool
     */
    private function gethostbynamel6(string $host, bool $try_a = false)
    {
        $dns6 = dns_get_record($host, DNS_AAAA);
        if ($try_a) {
            $dns4 = dns_get_record($host, DNS_A);
            $dns  = array_merge($dns4, $dns6);
        } else {
            $dns = $dns6;
        }
        $ip6 = [];
        $ip4 = [];
        foreach ($dns as $record) {
            if ($record['type'] == 'A') {
                $ip4[] = $record['ip'];
            }
            if ($record['type'] == 'AAAA') {
                $ip6[] = $record['ipv6'];
            }
        }
        if (!count($ip6)) {
            if ($try_a) {
                if (!count($ip4)) {
                    return false;
                }

                return $ip4;
            }

            return false;
        }

        return $ip6;
    }

    /**
     * Sets the module information.
     */
    public function setInfo(): void
    {
        $this->type        = 'import';
        $this->name        = __('RSS or Atom feed import');
        $this->description = __('Add a feed content to the blog.');
    }

    /**
     * Processes the import/export.
     *
     * @param      string  $do     action
     */
    public function process(string $do): void
    {
        if ($do === 'ok') {
            $this->status = true;

            return;
        }

        if (empty($_POST['feed_url'])) {
            return;
        }

        $this->feed_url = $_POST['feed_url'];

        // Check feed URL
        if (dcCore::app()->blog->settings->system->import_feed_url_control) {
            // Get IP from URL
            $bits = parse_url($this->feed_url);
            if (!$bits || !isset($bits['host'])) {
                throw new Exception(__('Cannot retrieve feed URL.'));
            }
            $ip = gethostbyname($bits['host']);
            if ($ip == $bits['host']) {
                $ip = $this->gethostbyname6($bits['host']);
                if (!$ip) {
                    throw new Exception(__('Cannot retrieve feed URL.'));
                }
            }
            // Check feed IP
            $flag = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6;
            if (dcCore::app()->blog->settings->system->import_feed_no_private_ip) {
                $flag |= FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
            }
            if (!filter_var($ip, $flag)) {
                throw new Exception(__('Cannot retrieve feed URL.'));
            }
            // IP control (white list regexp)
            if (dcCore::app()->blog->settings->system->import_feed_ip_regexp != '') {
                if (!preg_match(dcCore::app()->blog->settings->system->import_feed_ip_regexp, $ip)) {
                    throw new Exception(__('Cannot retrieve feed URL.'));
                }
            }
            // Port control (white list regexp)
            if (dcCore::app()->blog->settings->system->import_feed_port_regexp != '' && isset($bits['port'])) {
                if (!preg_match(dcCore::app()->blog->settings->system->import_feed_port_regexp, (string) $bits['port'])) {
                    throw new Exception(__('Cannot retrieve feed URL.'));
                }
            }
        }

        $feed = feedReader::quickParse($this->feed_url);
        if ($feed === false) {
            throw new Exception(__('Cannot retrieve feed URL.'));
        }
        if ((is_countable($feed->items) ? count($feed->items) : 0) == 0) {  // @phpstan-ignore-line
            throw new Exception(__('No items in feed.'));
        }

        $cur = dcCore::app()->con->openCursor(dcCore::app()->prefix . dcBlog::POST_TABLE_NAME);
        dcCore::app()->con->begin();
        foreach ($feed->items as $item) {
            $cur->clean();
            $cur->user_id      = dcCore::app()->auth->userID();
            $cur->post_content = $item->content ?: $item->description;
            $cur->post_title   = $item->title ?: text::cutString(html::clean($cur->post_content), 60);
            $cur->post_format  = 'xhtml';
            $cur->post_status  = dcBlog::POST_PENDING;
            $cur->post_dt      = dt::strftime('%Y-%m-%d %H:%M:%S', $item->TS);

            try {
                $post_id = dcCore::app()->blog->addPost($cur);
            } catch (Exception $e) {
                dcCore::app()->con->rollback();

                throw $e;
            }

            foreach ($item->subject as $subject) {
                dcCore::app()->meta->setPostMeta($post_id, 'tag', dcMeta::sanitizeMetaID($subject));
            }
        }

        dcCore::app()->con->commit();
        http::redirect($this->getURL() . '&do=ok');
    }

    /**
     * GUI for import/export module
     */
    public function gui(): void
    {
        if ($this->status) {
            dcPage::success(__('Content successfully imported.'));
        }

        echo
        '<form action="' . $this->getURL(true) . '" method="post">' .
        '<p>' . sprintf(__('Add a feed content to the current blog: <strong>%s</strong>.'), html::escapeHTML(dcCore::app()->blog->name)) . '</p>' .

        '<p><label for="feed_url">' . __('Feed URL:') . '</label>' .
        form::url('feed_url', 50, 300, html::escapeHTML($this->feed_url)) . '</p>' .

        '<p>' .
        dcCore::app()->formNonce() .
        form::hidden(['do'], 1) .
        '<input type="submit" value="' . __('Import') . '" /></p>' .

        '</form>';
    }
}
