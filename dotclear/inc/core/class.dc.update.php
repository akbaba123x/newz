<?php
/**
 * @package Dotclear
 * @subpackage Core
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class dcUpdate
{
    // Constants

    public const ERR_FILES_CHANGED    = 101;
    public const ERR_FILES_UNREADABLE = 102;
    public const ERR_FILES_UNWRITALBE = 103;

    /**
     * Version file URL
     *
     * @var string
     */
    protected $url;

    /**
     * Subject to check (usually 'dotclear')
     *
     * @var string
     */
    protected $subject;

    /**
     * Version channel (stable, testing, unstable, …)
     *
     * @var string
     */
    protected $version;

    /**
     * Cache file
     *
     * @var string
     */
    protected $cache_file;

    /**
     * Version information
     *
     * @var        array
     */
    protected $version_info = [
        'version'  => null,
        'href'     => null,
        'checksum' => null,
        'info'     => null,
        'php'      => '7.4',
        'warning'  => false,
        'notify'   => true,
    ];

    /**
     * Cache TTL (negative value)
     *
     * @var        string
     */
    protected $cache_ttl = '-6 hours';

    /**
     * Stack of files to check (digest)
     *
     * @var        array
     */
    protected $forced_files = [];

    /**
     * Constructor
     *
     * @param string $url           Versions file URL
     * @param string $subject       Subject to check
     * @param string $version       Version channel
     * @param string $cache_dir     Directory cache path
     */
    public function __construct(string $url, string $subject, string $version, string $cache_dir)
    {
        $this->url        = $url;
        $this->subject    = $subject;
        $this->version    = $version;
        $this->cache_file = $cache_dir . '/' . $subject . '-' . $version;
    }

    /**
     * Checks for Dotclear updates.
     * Returns latest version if available or false.
     *
     * @param   string  $version    Current version to compare
     * @param   bool    $nocache    Force checking
     *
     * @return  mixed   Latest version if available
     */
    public function check(string $version, bool $nocache = false)
    {
        $this->getVersionInfo($nocache);
        $v = $this->getVersion();
        if ($v && version_compare($version, $v, '<')) {
            return $v;
        }

        return false;
    }

    /**
     * Gets the version information.
     *
     * @param      bool       $nocache  The no cache flag
     *
     * @throws     Exception
     *
     * @return     mixed
     */
    public function getVersionInfo(bool $nocache = false)
    {
        # Check cached file
        if (is_readable($this->cache_file) && filemtime($this->cache_file) > strtotime($this->cache_ttl) && !$nocache) {
            $c = @file_get_contents($this->cache_file);
            $c = @unserialize($c);
            if (is_array($c)) {
                $this->version_info = $c;

                return;
            }
        }

        $cache_dir = dirname($this->cache_file);
        $can_write = (!is_dir($cache_dir) && is_writable(dirname($cache_dir)))
        || (!file_exists($this->cache_file) && is_writable($cache_dir))
        || is_writable($this->cache_file);

        # If we can't write file, don't bug host with queries
        if (!$can_write) {
            return;
        }

        if (!is_dir($cache_dir)) {
            try {
                files::makeDir($cache_dir);
            } catch (Exception $e) {
                return;
            }
        }

        # Try to get latest version number
        try {
            $path   = '';
            $status = 0;

            $http_get = function ($http_url) use (&$status, $path) {
                $client = netHttp::initClient($http_url, $path);
                if ($client !== false) {
                    $client->setTimeout(DC_QUERY_TIMEOUT);
                    $client->setUserAgent($_SERVER['HTTP_USER_AGENT']);
                    $client->get($path);
                    $status = (int) $client->getStatus();
                }

                return $client;
            };

            $client = $http_get($this->url);
            if ($status >= 400) {
                // If original URL uses HTTPS, try with HTTP
                $url_parts = parse_url($client->getRequestURL());
                if (isset($url_parts['scheme']) && $url_parts['scheme'] == 'https') {
                    // Replace https by http in url
                    $this->url = preg_replace('/^https(?=:\/\/)/i', 'http', $this->url);
                    $client    = $http_get($this->url);
                }
            }
            if (!$status || $status >= 400) {
                throw new Exception();
            }
            $this->readVersion($client->getContent());
        } catch (Exception $e) {
            return;
        }

        # Create cache
        file_put_contents($this->cache_file, serialize($this->version_info));
    }

    /**
     * Gets the version.
     *
     * @return     string|null  The version.
     */
    public function getVersion(): ?string
    {
        return $this->version_info['version'];
    }

    /**
     * Gets the file url.
     *
     * @return     string|null  The file url.
     */
    public function getFileURL(): ?string
    {
        return $this->version_info['href'];
    }

    /**
     * Gets the information url.
     *
     * @return     string|null  The information url.
     */
    public function getInfoURL(): ?string
    {
        return $this->version_info['info'];
    }

    /**
     * Gets the checksum.
     *
     * @return     string|null  The checksum.
     */
    public function getChecksum(): ?string
    {
        return $this->version_info['checksum'];
    }

    /**
     * Gets the php version.
     *
     * @return     string|null  The php version.
     */
    public function getPHPVersion(): ?string
    {
        return $this->version_info['php'];
    }

    /**
     * Gets the warning flag.
     *
     * @return     bool  The warning.
     */
    public function getWarning(): bool
    {
        return $this->version_info['warning'];
    }

    /**
     * Gets the notify flag.
     *
     * @return     bool  The notify.
     */
    public function getNotify(): bool
    {
        return $this->version_info['notify'];
    }

    /**
     * Gets the forced files.
     *
     * @return     array  The forced files.
     */
    public function getForcedFiles(): array
    {
        return $this->forced_files;
    }

    /**
     * Sets the forced files.
     *
     * @param      mixed  ...$args  The arguments
     */
    public function setForcedFiles(...$args)
    {
        $this->forced_files = $args;
    }

    /**
     * Sets the notify flag.
     *
     * @param      mixed  $n      The new value
     */
    public function setNotify($n)
    {
        if (!is_writable($this->cache_file)) {
            return;
        }

        $this->version_info['notify'] = (bool) $n;
        file_put_contents($this->cache_file, serialize($this->version_info));
    }

    /**
     * Check integrity
     *
     * @param      string     $digests_file  The digests file
     * @param      string     $root          The root
     *
     * @throws     Exception    If some files have changed
     *
     * @return     bool
     */
    public function checkIntegrity(string $digests_file, string $root): bool
    {
        if (!$digests_file) {
            throw new Exception(__('Digests file not found.'));
        }

        $changes = $this->md5sum($root, $digests_file);

        if (!empty($changes)) {
            $e            = new Exception('Some files have changed.', self::ERR_FILES_CHANGED);
            $e->bad_files = $changes;   // @phpstan-ignore-line

            throw $e;
        }

        return true;
    }

    /**
     * Downloads new version to destination.
     *
     * @param      string     $dest   The destination
     *
     * @throws     Exception
     */
    public function download(string $dest): void
    {
        $url = $this->getFileURL();

        if (!$url) {
            throw new Exception(__('No file to download'));
        }

        if (!is_writable(dirname($dest))) {
            throw new Exception(__('Root directory is not writable.'));
        }

        try {
            $path   = '';
            $status = 0;

            $http_get = function ($http_url) use (&$status, $dest, $path) {
                $client = netHttp::initClient($http_url, $path);
                if ($client !== false) {
                    $client->setTimeout(DC_QUERY_TIMEOUT);
                    $client->setUserAgent($_SERVER['HTTP_USER_AGENT']);
                    $client->useGzip(false);
                    $client->setPersistReferers(false);
                    $client->setOutput($dest);
                    $client->get($path);
                    $status = (int) $client->getStatus();
                }

                return $client;
            };

            $client = $http_get($url);
            if ($status >= 400) {
                // If original URL uses HTTPS, try with HTTP
                $url_parts = parse_url($client->getRequestURL());
                if (isset($url_parts['scheme']) && $url_parts['scheme'] == 'https') {
                    // Replace https by http in url
                    $url    = preg_replace('/^https(?=:\/\/)/i', 'http', $url);
                    $client = $http_get($url);
                }
            }
            if ($status != 200) {
                @unlink($dest);

                throw new Exception();
            }
        } catch (Exception $e) {
            throw new Exception(__('An error occurred while downloading archive.'));
        }
    }

    /**
     * Check downloaded file
     *
     * @param      string  $zip    The zip
     *
     * @return     bool
     */
    public function checkDownload(string $zip): bool
    {
        $cs = $this->getChecksum();

        return $cs && is_readable($zip) && md5_file($zip) == $cs;
    }

    /**
     * Backups changed files before an update.
     *
     * @param      string     $zip_file      The zip file
     * @param      string     $zip_digests   The zip digests
     * @param      string     $root          The root
     * @param      string     $root_digests  The root digests
     * @param      string     $dest          The destination
     *
     * @throws     Exception
     *
     * @return     bool
     */
    public function backup(string $zip_file, string $zip_digests, string $root, string $root_digests, string $dest): bool
    {
        if (!is_readable($zip_file)) {
            throw new Exception(__('Archive not found.'));
        }

        if (!is_readable($root_digests)) {
            @unlink($zip_file);

            throw new Exception(__('Unable to read current digests file.'));
        }

        # Stop everything if a backup already exists and can not be overrided
        if (!is_writable(dirname($dest)) && !file_exists($dest)) {
            throw new Exception(__('Root directory is not writable.'));
        }

        if (file_exists($dest) && !is_writable($dest)) {
            return false;
        }

        $b_fp = @fopen($dest, 'wb');
        if ($b_fp === false) {
            return false;
        }

        $zip   = new fileUnzip($zip_file);
        $b_zip = new fileZip($b_fp);

        if (!$zip->hasFile($zip_digests)) {
            @unlink($zip_file);

            throw new Exception(__('Downloaded file does not seem to be a valid archive.'));
        }

        $opts        = FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES;
        $cur_digests = file($root_digests, $opts);
        $new_digests = explode("\n", $zip->unzip($zip_digests));
        $new_files   = $this->getNewFiles($cur_digests, $new_digests);
        $zip->close();
        unset($opts, $cur_digests, $new_digests, $zip);

        $not_readable = [];

        if (!empty($this->forced_files)) {
            $new_files = array_merge($new_files, $this->forced_files);
        }

        foreach ($new_files as $file) {
            if (!$file || !file_exists($root . '/' . $file)) {
                continue;
            }

            try {
                $b_zip->addFile($root . '/' . $file, $file);
            } catch (Exception $e) {
                $not_readable[] = $file;
            }
        }

        # If only one file is not readable, stop everything now
        if (!empty($not_readable)) {
            $e            = new Exception('Some files are not readable.', self::ERR_FILES_UNREADABLE);
            $e->bad_files = $not_readable;  // @phpstan-ignore-line

            throw $e;
        }

        $b_zip->write();
        fclose($b_fp);
        $b_zip->close();

        return true;
    }

    /**
     * Performs an upgrade.
     *
     * @param      string     $zip_file      The zip file
     * @param      string     $zip_digests   The zip digests
     * @param      string     $zip_root      The zip root
     * @param      string     $root          The root
     * @param      string     $root_digests  The root digests
     *
     * @throws     Exception
     */
    public function performUpgrade(string $zip_file, string $zip_digests, string $zip_root, string $root, string $root_digests): void
    {
        if (!is_readable($zip_file)) {
            throw new Exception(__('Archive not found.'));
        }

        if (!is_readable($root_digests)) {
            @unlink($zip_file);

            throw new Exception(__('Unable to read current digests file.'));
        }

        $zip = new fileUnzip($zip_file);

        if (!$zip->hasFile($zip_digests)) {
            @unlink($zip_file);

            throw new Exception(__('Downloaded file does not seem to be a valid archive.'));
        }

        $opts        = FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES;
        $cur_digests = file($root_digests, $opts);
        $new_digests = explode("\n", $zip->unzip($zip_digests));
        $new_files   = self::getNewFiles($cur_digests, $new_digests);

        if (!empty($this->forced_files)) {
            $new_files = array_merge($new_files, $this->forced_files);
        }

        $zip_files    = [];
        $not_writable = [];

        foreach ($new_files as $file) {
            if (!$file) {
                continue;
            }

            if (!$zip->hasFile($zip_root . '/' . $file)) {
                @unlink($zip_file);

                throw new Exception(__('Incomplete archive.'));
            }

            $dest = $dest_dir = $root . '/' . $file;
            while (!is_dir($dest_dir = dirname($dest_dir))) {
                // Nothing to do here (see inside loop condition)
            }

            if ((file_exists($dest) && !is_writable($dest)) || (!file_exists($dest) && !is_writable($dest_dir))) {
                $not_writable[] = $file;

                continue;
            }

            $zip_files[] = $file;
        }

        # If only one file is not writable, stop everything now
        if (!empty($not_writable)) {
            $e            = new Exception('Some files are not writable', self::ERR_FILES_UNWRITALBE);
            $e->bad_files = $not_writable;  // @phpstan-ignore-line

            throw $e;
        }

        # Everything's fine, we can write files, then do it now
        $can_touch = function_exists('touch');
        foreach ($zip_files as $file) {
            $zip->unzip($zip_root . '/' . $file, $root . '/' . $file);
            if ($can_touch) {
                @touch($root . '/' . $file);
            }
        }
        @unlink($zip_file);

        # Try to clear PHP OPcache to avoid running old code after update
        try {
            if ((extension_loaded('opcache') || extension_loaded('Zend OPcache')) && is_array(opcache_get_status())) {
                opcache_reset();
            }
        } catch (Exception $e) {
        }
    }

    /**
     * Gets the new files.
     *
     * @param      array  $cur_digests  The current digests
     * @param      array  $new_digests  The new digests
     *
     * @return     array  The new files.
     */
    protected function getNewFiles(array $cur_digests, array $new_digests): array
    {
        $cur_md5 = $cur_path = $cur_digests;
        $new_md5 = $new_path = $new_digests;

        array_walk($cur_md5, [$this, 'parseLine'], 1);
        array_walk($cur_path, [$this, 'parseLine'], 2);
        array_walk($new_md5, [$this, 'parseLine'], 1);
        array_walk($new_path, [$this, 'parseLine'], 2);

        $cur = array_combine($cur_md5, $cur_path);
        $new = array_combine($new_md5, $new_path);

        return array_values(array_diff_key($new, $cur));
    }

    /**
     * Reads a version information from string.
     *
     * @param      string  $str    The string
     */
    protected function readVersion(string $str): void
    {
        try {
            $xml = new SimpleXMLElement($str, LIBXML_NOERROR);
            $r   = $xml->xpath("/versions/subject[@name='" . $this->subject . "']/release[@name='" . $this->version . "']");

            if (!empty($r) && is_array($r)) {
                $r                              = $r[0];
                $this->version_info['version']  = isset($r['version']) ? (string) $r['version'] : null;
                $this->version_info['href']     = isset($r['href']) ? (string) $r['href'] : null;
                $this->version_info['checksum'] = isset($r['checksum']) ? (string) $r['checksum'] : null;
                $this->version_info['info']     = isset($r['info']) ? (string) $r['info'] : null;
                $this->version_info['php']      = isset($r['php']) ? (string) $r['php'] : null;
                $this->version_info['warning']  = isset($r['warning']) ? (bool) $r['warning'] : false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return list of changed files
     *
     * @param      string     $root          The root
     * @param      string     $digests_file  The digests file
     *
     * @throws     Exception
     *
     * @return     array
     */
    protected function md5sum(string $root, string $digests_file): array
    {
        if (!is_readable($digests_file)) {
            throw new Exception(__('Unable to read digests file.'));
        }

        $opts     = FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES;
        $contents = file($digests_file, $opts);

        $changes = [];

        foreach ($contents as $digest) {
            if (!preg_match('#^([\da-f]{32})\s+(.+?)$#', $digest, $m)) {
                continue;
            }

            $md5      = $m[1];
            $filename = $root . '/' . $m[2];

            // Invalid checksum
            if (!is_readable($filename) || !self::md5_check($filename, $md5)) {
                $changes[] = substr($m[2], 2);
            }
        }

        // No checksum found in digests file
        if (empty($md5)) {
            throw new Exception(__('Invalid digests file.'));
        }

        return $changes;
    }

    /**
     * Parse digests file line
     *
     * @param      mixed     $v     The value
     * @param      mixed     $k     The key/index
     * @param      mixed     $n     array_walk() 3rd arg
     */
    protected function parseLine(&$v, $k, $n)
    {
        if (!preg_match('#^([\da-f]{32})\s+(.+?)$#', $v, $m)) {
            return;
        }

        $v = $n == 1 ? md5($m[2] . $m[1]) : substr($m[2], 2);
    }

    /**
     * Check the MD5 of a file
     *
     * @param      string  $filename  The filename
     * @param      string  $md5       The MD5 checksum
     *
     * @return     bool
     */
    protected static function md5_check(string $filename, string $md5): bool
    {
        if (md5_file($filename) == $md5) {
            return true;
        }
        $filecontent = file_get_contents($filename);
        $filecontent = str_replace("\r\n", "\n", $filecontent);
        $filecontent = str_replace("\r", "\n", $filecontent);
        if (md5($filecontent) == $md5) {
            return true;
        }

        return false;
    }
}
