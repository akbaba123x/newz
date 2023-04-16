<?php
/**
 * @class mysqlimb4Connection
 * @brief MySQLi utf8-mb4 Database Driver
 *
 * See the {@link dbLayer} documentation for common methods.
 *
 * @package Clearbricks
 * @subpackage DBLayer
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */

/**
 * Need to load parent class before extending it, may be use another technique in the future?
 */
require_once __DIR__ . '/class.mysqli.php';

class mysqlimb4Connection extends mysqliConnection
{
    /**
     * Driver name
     *
     * @var        string
     */
    protected $__driver = 'mysqlimb4';

    /**
     * Open a DB connection
     *
     * @param      string     $host      The host
     * @param      string     $user      The user
     * @param      string     $password  The password
     * @param      string     $database  The database
     *
     * @throws     Exception
     *
     * @return     mixed
     */
    public function db_connect(string $host, string $user, string $password, string $database)
    {
        if (!function_exists('mysqli_connect')) {
            throw new Exception('PHP MySQLi functions are not available');
        }

        $port   = abs((int) ini_get('mysqli.default_port'));
        $socket = '';
        if (strpos($host, ':') !== false) {
            // Port or socket given
            $bits   = explode(':', $host);
            $host   = array_shift($bits);
            $socket = array_shift($bits);
            if (abs((int) $socket) > 0) {
                // TCP/IP connection on given port
                $port   = abs((int) $socket);
                $socket = '';
            } else {
                // Socket connection
                $port = 0;
            }
        }
        if (($link = @mysqli_connect($host, $user, $password, $database, $port, $socket)) === false) {
            throw new Exception('Unable to connect to database');
        }

        $this->db_post_connect($link);

        return $link;
    }

    /**
     * Open a persistant DB connection
     *
     * @param      string  $host      The host
     * @param      string  $user      The user
     * @param      string  $password  The password
     * @param      string  $database  The database
     *
     * @return     mixed
     */
    public function db_pconnect(string $host, string $user, string $password, string $database)
    {
        // No pconnect wtih mysqli, below code is for comatibility
        return $this->db_connect($host, $user, $password, $database);
    }

    /**
     * Post connection helper
     *
     * @param      mixed  $handle   The DB handle
     */
    private function db_post_connect($handle): void
    {
        if (version_compare($this->db_version($handle), '5.7.7', '>=')) {
            $this->db_query($handle, 'SET NAMES utf8mb4');
            $this->db_query($handle, 'SET CHARACTER SET utf8mb4');
            $this->db_query($handle, "SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
            $this->db_query($handle, "SET COLLATION_SERVER = 'utf8mb4_unicode_ci'");
            $this->db_query($handle, "SET CHARACTER_SET_SERVER = 'utf8mb4'");
            if (version_compare($this->db_version($handle), '8.0', '<')) {
                // Setting CHARACTER_SET_DATABASE is obosolete for MySQL 8.0+
                $this->db_query($handle, "SET CHARACTER_SET_DATABASE = 'utf8mb4'");
            }
            $handle->set_charset('utf8mb4');
        } else {
            throw new Exception('Unable to connect to an utf8mb4 database');
        }
    }

    /**
     * Get an ORDER BY fragment to be used in a SQL query
     *
     * @param      mixed  ...$args  The arguments
     *
     * @return     string
     */
    public function orderBy(...$args): string
    {
        $default = [
            'order'   => '',
            'collate' => false,
        ];
        foreach ($args as $v) {
            if (is_string($v)) {
                $res[] = $v;
            } elseif (is_array($v) && !empty($v['field'])) {
                $v          = array_merge($default, $v);
                $v['order'] = (strtoupper($v['order']) == 'DESC' ? 'DESC' : '');
                $res[]      = $v['field'] . ($v['collate'] ? ' COLLATE utf8mb4_unicode_ci' : '') . ' ' . $v['order'];
            }
        }

        return empty($res) ? '' : ' ORDER BY ' . implode(',', $res) . ' ';
    }

    /**
     * Get fields concerned by lexical sort
     *
     * @param      mixed  ...$args  The arguments
     *
     * @return     string
     */
    public function lexFields(...$args): string
    {
        $fmt = '%s COLLATE utf8mb4_unicode_ci';
        foreach ($args as $v) {
            if (is_string($v)) {
                $res[] = sprintf($fmt, $v);
            } elseif (is_array($v)) {
                $res = array_map(fn ($i) => sprintf($fmt, $i), $v);
            }
        }

        return empty($res) ? '' : implode(',', $res);
    }
}
