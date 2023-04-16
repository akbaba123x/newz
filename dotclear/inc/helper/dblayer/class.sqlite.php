<?php
/**
 * @class sqliteConnection
 * @brief SQLite Database Driver
 *
 * See the {@link dbLayer} documentation for common methods.
 *
 * @package Clearbricks
 * @subpackage DBLayer
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class sqliteConnection extends dbLayer implements i_dbLayer
{
    protected $__driver        = 'sqlite';
    protected $__syntax        = 'sqlite';
    protected $utf8_unicode_ci = null;
    protected $vacuum          = false;

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
        if (!class_exists('PDO') || !in_array('sqlite', PDO::getAvailableDrivers())) {
            throw new Exception('PDO SQLite class is not available');
        }

        $link = new PDO('sqlite:' . $database);
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
        if (!class_exists('PDO') || !in_array('sqlite', PDO::getAvailableDrivers())) {
            throw new Exception('PDO SQLite class is not available');
        }

        $link = new PDO('sqlite:' . $database, null, null, [PDO::ATTR_PERSISTENT => true]);
        $this->db_post_connect($link);

        return $link;
    }

    /**
     * Post connection helper
     *
     * @param      mixed  $handle   The DB handle
     */
    private function db_post_connect($handle): void
    {
        if ($handle instanceof PDO) {
            $this->db_exec($handle, 'PRAGMA short_column_names = 1');
            $this->db_exec($handle, 'PRAGMA encoding = "UTF-8"');
            $handle->sqliteCreateFunction('now', [$this, 'now'], 0);
            if (class_exists('Collator') && method_exists($handle, 'sqliteCreateCollation')) {
                $this->utf8_unicode_ci = new Collator('root');
                if (!$handle->sqliteCreateCollation('utf8_unicode_ci', [$this->utf8_unicode_ci, 'compare'])) {
                    $this->utf8_unicode_ci = null;
                }
            }
        }
    }

    /**
     * Close DB connection
     *
     * @param      mixed  $handle  The DB handle
     */
    public function db_close($handle): void
    {
        if ($handle instanceof PDO) {
            if ($this->vacuum) {
                $this->db_exec($handle, 'VACUUM');
            }
            $handle       = null;
            $this->__link = null;
        }
    }

    /**
     * Get DB version
     *
     * @param      mixed  $handle  The handle
     *
     * @return     string
     */
    public function db_version($handle): string
    {
        return $handle instanceof PDO ? $handle->getAttribute(PDO::ATTR_SERVER_VERSION) : '';
    }

    /**
     * Get query data in a staticRecord
     *
     * There is no other way than get all selected data in a staticRecord with SQlite
     *
     * @param      string        $sql    The sql
     *
     * @return     staticRecord  The static record.
     */
    public function select(string $sql): staticRecord
    {
        $result              = $this->db_query($this->__link, $sql);
        $this->__last_result = &$result;

        $info         = [];
        $info['con']  = &$this;
        $info['cols'] = $this->db_num_fields($result);
        $info['info'] = [];

        for ($i = 0; $i < $info['cols']; $i++) {
            $info['info']['name'][] = $this->db_field_name($result, $i);
            $info['info']['type'][] = $this->db_field_type($result, $i);
        }

        $data = [];
        while ($r = $result->fetch(PDO::FETCH_ASSOC)) { // @phpstan-ignore-line
            $R = [];
            foreach ($r as $k => $v) {
                $k     = preg_replace('/^(.*)\./', '', $k);
                $R[$k] = $v;
                $R[]   = &$R[$k];
            }
            $data[] = $R;
        }

        $info['rows'] = count($data);
        $result->closeCursor(); // @phpstan-ignore-line

        return new staticRecord($data, $info);
    }

    /**
     * Execute a DB query
     *
     * @param      mixed      $handle  The handle
     * @param      string     $query   The query
     *
     * @throws     Exception
     *
     * @return     mixed
     */
    public function db_query($handle, string $query)
    {
        if ($handle instanceof PDO) {
            $res = $handle->query($query);
            if ($res === false) {
                throw new Exception($this->db_last_error($handle));
            }

            return $res;
        }

        return null;
    }

    /**
     * db_query() alias
     *
     * @param      mixed   $handle  The handle
     * @param      string  $query   The query
     *
     * @return     mixed
     */
    public function db_exec($handle, string $query)
    {
        return $this->db_query($handle, $query);
    }

    /**
     * Get number of fields in result
     *
     * @param      mixed  $res    The resource
     *
     * @return     int
     */
    public function db_num_fields($res): int
    {
        return $res instanceof PDOStatement ? $res->columnCount() : 0;
    }

    /**
     * Get number of rows in result
     *
     * @param      mixed  $res    The resource
     *
     * @return     int
     */
    public function db_num_rows($res): int
    {
        return 0;
    }

    /**
     * Get field name in result
     *
     * @param      mixed   $res       The resource
     * @param      int     $position  The position
     *
     * @return     string
     */
    public function db_field_name($res, int $position): string
    {
        if ($res instanceof PDOStatement) {
            $m = $res->getColumnMeta($position);

            return preg_replace('/^.+\./', '', $m['name']); # we said short_column_names = 1
        }

        return '';
    }

    /**
     * Get field type in result
     *
     * @param      mixed   $res       The resource
     * @param      int     $position  The position
     *
     * @return     string
     */
    public function db_field_type($res, int $position): string
    {
        if ($res instanceof PDOStatement) {
            $m = $res->getColumnMeta($position);
            switch ($m['pdo_type']) {
                case PDO::PARAM_BOOL:
                    return 'boolean';
                case PDO::PARAM_NULL:
                    return 'null';
                case PDO::PARAM_INT:
                    return 'integer';
                default:
                    return 'varchar';
            }
        }

        return '';
    }

    /**
     * Fetch result data
     *
     * @param      mixed  $res    The resource
     *
     * @return     array|false
     */
    public function db_fetch_assoc($res)
    {
        return false;
    }

    /**
     * Seek in result
     *
     * @param      mixed   $res    The resource
     * @param      int     $row    The row
     *
     * @return     bool
     */
    public function db_result_seek($res, $row): bool
    {
        return false;
    }

    /**
     * Get number of affected rows in last INSERT, DELETE or UPDATE query
     *
     * @param      mixed   $handle  The DB handle
     * @param      mixed   $res     The resource
     *
     * @return     int
     */
    public function db_changes($handle, $res): int
    {
        return $res instanceof PDOStatement ? $res->rowCount() : 0;
    }

    /**
     * Get last query error, if any
     *
     * @param      mixed       $handle  The handle
     *
     * @return     bool|string
     */
    public function db_last_error($handle)
    {
        if ($handle instanceof PDO) {
            $err = $handle->errorInfo();

            return $err[2] . ' (' . $err[1] . ')';
        }

        return false;
    }

    /**
     * Escape a string (to be used in a SQL query)
     *
     * @param      mixed   $str     The string
     * @param      mixed   $handle  The DB handle
     *
     * @return     string
     */
    public function db_escape_string($str, $handle = null): string
    {
        return $handle instanceof PDO ? trim($handle->quote($str), "'") : $str;
    }

    public function escapeSystem(string $str): string
    {
        return "'" . $this->escape($str) . "'";
    }

    public function begin(): void
    {
        if ($this->__link instanceof PDO) {
            $this->__link->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->__link instanceof PDO) {
            $this->__link->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->__link instanceof PDO) {
            $this->__link->rollBack();
        }
    }

    /**
     * Locks a table
     *
     * @param      string  $table  The table
     */
    public function db_write_lock(string $table): void
    {
        $this->execute('BEGIN EXCLUSIVE TRANSACTION');
    }

    /**
     * Unlock tables
     */
    public function db_unlock(): void
    {
        $this->execute('END');
    }

    /**
     * Optimize a table
     *
     * @param      string  $table  The table
     */
    public function vacuum(string $table): void
    {
        $this->vacuum = true;
    }

    /**
     * Get a date to be used in SQL query
     *
     * @param      string  $field    The field
     * @param      string  $pattern  The pattern
     *
     * @return     string
     */
    public function dateFormat(string $field, string $pattern): string
    {
        return "strftime('" . $this->escape($pattern) . "'," . $field . ') ';
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
                if ($v['collate']) {
                    if ($this->utf8_unicode_ci instanceof Collator) {
                        $res[] = $v['field'] . ' COLLATE utf8_unicode_ci ' . $v['order'];
                    } else {
                        $res[] = 'LOWER(' . $v['field'] . ') ' . $v['order'];
                    }
                } else {
                    $res[] = $v['field'] . ' ' . $v['order'];
                }
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
        $fmt = $this->utf8_unicode_ci instanceof Collator ? '%s COLLATE utf8_unicode_ci' : 'LOWER(%s)';
        foreach ($args as $v) {
            if (is_string($v)) {
                $res[] = sprintf($fmt, $v);
            } elseif (is_array($v)) {
                $res = array_map(fn ($i) => sprintf($fmt, $i), $v);
            }
        }

        return empty($res) ? '' : implode(',', $res);
    }

    # Internal SQLite function that adds NOW() SQL function.
    public function now()
    {
        return date('Y-m-d H:i:s');
    }
}
