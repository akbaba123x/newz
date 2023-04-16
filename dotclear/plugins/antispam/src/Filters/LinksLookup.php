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

namespace Dotclear\Plugin\antispam\Filters;

use Dotclear\Plugin\antispam\SpamFilter;

class LinksLookup extends SpamFilter
{
    /**
     * Filter id
     *
     * @var        string
     */
    public $id = 'dcFilterLinksLookup';

    /**
     * Filter name
     *
     * @var        string
     */
    public $name = 'Links Lookup';

    /**
     * Filter has GUI
     *
     * @var        bool
     */
    public $has_gui = false;

    /**
     * Filter help ID
     *
     * @var        string
     */
    public $help = '';

    /**
     * subrl.org URL
     */
    private string $server = 'multi.surbl.org';

    /**
     * Sets the filter description.
     */
    protected function setInfo()
    {
        $this->description = __('Checks links in comments against surbl.org');
    }

    /**
     * Gets the status message.
     *
     * @param      string  $status      The status
     * @param      int     $comment_id  The comment identifier
     *
     * @return     string  The status message.
     */
    public function getStatusMessage(string $status, ?int $comment_id): string
    {
        return sprintf(__('Filtered by %1$s with server %2$s.'), $this->guiLink(), $status);
    }

    /**
     * This method should return if a comment is a spam or not. If it returns true
     * or false, execution of next filters will be stoped. If should return nothing
     * to let next filters apply.
     *
     * @param      string   $type     The comment type (comment / trackback)
     * @param      string   $author   The comment author
     * @param      string   $email    The comment author email
     * @param      string   $site     The comment author site
     * @param      string   $ip       The comment author IP
     * @param      string   $content  The comment content
     * @param      int      $post_id  The comment post_id
     * @param      string   $status   The comment status
     *
     * @return  mixed
     */
    public function isSpam(string $type, ?string $author, ?string $email, ?string $site, ?string $ip, ?string $content, ?int $post_id, string &$status)
    {
        if (!$ip || long2ip((int) ip2long($ip)) != $ip) {
            return;
        }

        $urls = $this->getLinks($content);
        array_unshift($urls, $site);

        foreach ($urls as $u) {
            $b = parse_url($u);
            if (!isset($b['host']) || !$b['host']) {
                continue;
            }

            $domain      = preg_replace('/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/', '$1', $b['host']);
            $domain_elem = explode('.', $domain);

            $i = count($domain_elem) - 1;
            if ($i == 0) {
                // "domain" is 1 word long, don't check it
                return;
            }
            $host = $domain_elem[$i];
            do {
                $host = $domain_elem[$i - 1] . '.' . $host;
                $i--;
                $response = gethostbyname($host . '.' . $this->server);
                if (substr($response, 0, 3) === '127' && substr($response, 8) !== '1') {
                    $status = substr($domain, 0, 128);

                    return true;
                }
            } while ($i > 0);
        }
    }

    /**
     * Return the links URL in content.
     *
     * @param      string  $text   The text
     *
     * @return     array   The links.
     */
    private function getLinks(string $text): array
    {
        // href attribute on "a" tags is second match
        preg_match_all('|<a.*?href="(http.*?)"|', $text, $parts);

        return $parts[1];
    }
}
