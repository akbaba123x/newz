<?php
/**
 * @brief SQL query statement builder
 *
 * dcSqlStatement is a class used to build SQL queries
 *
 * @package Dotclear
 * @subpackage Core
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class dcSqlStatement
{
    // Constants

    /**
     * Use AS for aliases anywhere (if true) else only for SQLite syntax (if false)
     *
     * @see self::alias(), self::as(), self::count(), self::avg(), self::min(), self::max(), self::sum() methods
     *
     * @var        bool
     */
    protected const VERBOSE_SQL_ALIAS = false;

    // Properties

    /**
     * DB handle
     */
    protected $con;

    /**
     * DB SQL syntax
     *
     * should be 'mysql', 'postgresql' or 'sqlite'
     *
     * @var string
     */
    protected $syntax;

    /**
     * Keyword use between name and its alias
     *
     * @var        string
     */
    protected $_AS = ' ';

    /**
     * Stack of fields
     *
     * @var        array
     */
    protected $columns = [];

    /**
     * Stack of from clauses
     *
     * @var        array
     */
    protected $from = [];

    /**
     * Stack of where clauses
     *
     * @var        array
     */
    protected $where = [];

    /**
     * Additionnal stack of where clauses
     *
     * @var        array
     */
    protected $cond = [];

    /**
     * Stack of generic SQL clauses
     *
     * @var        array
     */
    protected $sql = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->con    = dcCore::app()->con;
        $this->syntax = dcCore::app()->con->syntax();

        /* @phpstan-ignore-next-line */
        $this->_AS = ($this->syntax === 'sqlite' || self::VERBOSE_SQL_ALIAS ? ' AS ' : ' ');
    }

    /**
     * Magic getter method
     *
     * @param      string  $property  The property
     *
     * @return     mixed   property value if property exists
     */
    #[\ReturnTypeWillChange]
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        trigger_error('Unknown property ' . $property, E_USER_ERROR);
    }

    /**
     * Magic setter method
     *
     * @param      string  $property  The property
     * @param      mixed   $value     The value
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function __set(string $property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            trigger_error('Unknown property ' . $property, E_USER_ERROR);
        }

        return $this;
    }

    /**
     * Magic isset method
     *
     * @param      string  $property  The property
     *
     * @return     bool
     */
    public function __isset(string $property): bool
    {
        if (property_exists($this, $property)) {
            return isset($this->$property);
        }

        return false;
    }

    /**
     * Magic unset method
     *
     * @param      string  $property  The property
     */
    public function __unset(string $property)
    {
        if (property_exists($this, $property)) {
            unset($this->$property);
        }
    }

    /**
     * Magic invoke method
     *
     * Alias of statement()
     *
     * @return     string
     */
    public function __invoke(): string
    {
        return $this->statement();
    }

    /**
     * Returns a SQL dummy statement
     *
     * @return string the statement
     */
    public function statement(): string
    {
        return '';
    }

    /**
     * Adds column(s)
     *
     * @param mixed     $c      the column(s)
     * @param boolean   $reset  reset previous column(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function columns($c, bool $reset = false)
    {
        if ($reset) {
            $this->columns = [];
        }
        if (is_array($c)) {
            $this->columns = array_merge($this->columns, $c);
        } else {
            array_push($this->columns, $c);
        }

        return $this;
    }

    /**
     * columns() alias
     *
     * @param mixed     $c      the column(s)
     * @param boolean   $reset  reset previous column(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function fields($c, bool $reset = false)
    {
        return $this->columns($c, $reset);
    }

    /**
     * columns() alias
     *
     * @param      mixed    $c      the column(s)
     * @param      boolean  $reset  reset previous column(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function column($c, bool $reset = false)
    {
        return $this->columns($c, $reset);
    }

    /**
     * column() alias
     *
     * @param      mixed    $c      the column(s)
     * @param      boolean  $reset  reset previous column(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function field($c, bool $reset = false)
    {
        return $this->column($c, $reset);
    }

    /**
     * Adds FROM clause(s)
     *
     * @param mixed     $c      the from clause(s)
     * @param boolean   $reset  reset previous from(s) first
     * @param boolean   $first  put the from clause(s) at top of list
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function from($c, bool $reset = false, bool $first = false)
    {
        $filter = fn ($v) => trim(ltrim((string) $v, ','));
        if ($reset) {
            $this->from = [];
        }
        // Remove comma on beginning of clause(s) (legacy code)
        if (is_array($c)) {
            $c = array_map($filter, $c);   // Cope with legacy code
            if ($first) {
                $this->from = array_merge($c, $this->from);
            } else {
                $this->from = array_merge($this->from, $c);
            }
        } else {
            $c = $filter($c);   // Cope with legacy code
            if ($first) {
                array_unshift($this->from, $c);
            } else {
                array_push($this->from, $c);
            }
        }

        return $this;
    }

    /**
     * Adds WHERE clause(s) condition (each will be AND combined in statement)
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous where(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function where($c, bool $reset = false)
    {
        $filter = fn ($v) => preg_replace('/^\s*(AND|OR)\s*/i', '', $v);
        if ($reset) {
            $this->where = [];
        }
        if (is_array($c)) {
            $c           = array_map($filter, $c);  // Cope with legacy code
            $this->where = array_merge($this->where, $c);
        } else {
            $c = $filter($c);   // Cope with legacy code
            array_push($this->where, $c);
        }

        return $this;
    }

    /**
     * from() alias
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous where(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function on($c, bool $reset = false)
    {
        return $this->where($c, $reset);
    }

    /**
     * Adds additional WHERE clause condition(s) (including an operator at beginning)
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous condition(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function cond($c, bool $reset = false)
    {
        if ($reset) {
            $this->cond = [];
        }
        if (is_array($c)) {
            $this->cond = array_merge($this->cond, $c);
        } else {
            array_push($this->cond, $c);
        }

        return $this;
    }

    /**
     * Adds additional WHERE AND clause condition(s)
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous condition(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function and($c, bool $reset = false)
    {
        return $this->cond(array_map(fn ($v) => 'AND ' . $v, is_array($c) ? $c : [$c]), $reset);
    }

    /**
     * Helper to group some AND parts
     *
     * @param      mixed  $c      the parts}
     *
     * @return     string
     */
    public function andGroup($c): string
    {
        $group = '(' . implode(' AND ', is_array($c) ? $c : [$c]) . ')';

        return $group === '()' ? '' : $group;
    }

    /**
     * Adds additional WHERE OR clause condition(s)
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous condition(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function or($c, bool $reset = false)
    {
        return $this->cond(array_map(fn ($v) => 'OR ' . $v, is_array($c) ? $c : [$c]), $reset);
    }

    /**
     * Helper to group some OR parts
     *
     * @param      mixed  $c      the parts}
     *
     * @return     string
     */
    public function orGroup($c): string
    {
        $group = '(' . implode(' OR ', is_array($c) ? $c : [$c]) . ')';

        return $group === '()' ? '' : $group;
    }

    /**
     * Adds generic clause(s)
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous generic clause(s) first
     *
     * @return mixed    self instance, enabling to chain calls
     */
    #[\ReturnTypeWillChange]
    public function sql($c, bool $reset = false)
    {
        if ($reset) {
            $this->sql = [];
        }
        if (is_array($c)) {
            $this->sql = array_merge($this->sql, $c);
        } else {
            array_push($this->sql, $c);
        }

        return $this;
    }

    // Helpers

    /**
     * Escape a value
     *
     * @param      string  $value  The value
     *
     * @return     string
     */
    public function escape(string $value): string
    {
        return $this->con->escape($value);
    }

    /**
     * Quote and escape a value if necessary (type string)
     *
     * @param      mixed    $value   The value
     * @param      boolean  $escape  The escape
     *
     * @return     string
     */
    public function quote($value, bool $escape = true): string
    {
        return "'" . ($escape ? $this->con->escape($value) : $value) . "'";
    }

    /**
     * Return a SQL table/column fragment using an alias for a name
     *
     * @param      string  $name   The name (table, field)
     * @param      string  $alias  The alias
     *
     * @return     string
     */
    public function alias(string $name, string $alias): string
    {
        return $name . $this->_AS . $alias;
    }

    /**
     * alias() alias
     */
    public function as(string $name, string $alias): string
    {
        return $this->alias($name, $alias);
    }

    /**
     * Return an SQL IN (…) fragment
     *
     * @param      mixed  $list         The list of values
     * @param      string $cast         Cast given not null values to specified type
     *
     * @return     string
     */
    public function in($list, string $cast = ''): string
    {
        if ($cast !== '') {
            switch ($cast) {
                case 'int':
                    if (is_array($list)) {
                        $list = array_map(fn ($v) => is_null($v) ? $v : (int) $v, $list);
                    } else {
                        $list = is_null($list) ? null : (int) $list;
                    }

                    break;
                case 'string':
                    if (is_array($list)) {
                        $list = array_map(fn ($v) => is_null($v) ? $v : (string) $v, $list);
                    } else {
                        $list = is_null($list) ? null : (string) $list;
                    }

                    break;
            }
        }

        return ' ' . trim($this->con->in($list));
    }

    /**
     * Return an SQL IN (SELECT …) fragment
     *
     * @param      string             $field  The field
     * @param      dcSelectStatement  $sql    The sql
     *
     * @return     string
     */
    public function inSelect(string $field, dcSelectStatement $sql): string
    {
        return $field . ' IN (' . $sql->statement() . ')';
    }

    /**
     * Return an SQL formatted date
     *
     * @param   string    $field     Field name
     * @param   string    $pattern   Date format
     *
     * @return     string
     */
    public function dateFormat(string $field, string $pattern): string
    {
        return $this->con->dateFormat($field, $pattern);
    }

    /**
     * Return an SQL formatted like
     *
     * @param      string  $field    The field
     * @param      string  $pattern  The pattern
     *
     * @return     string
     */
    public function like(string $field, string $pattern): string
    {
        return $field . ' LIKE ' . $this->quote($pattern);
    }

    /**
     * Return an SQL formatted REGEXP clause
     *
     * @param      string  $value  The value
     *
     * @return     string
     */
    public function regexp(string $value): string
    {
        if ($this->syntax == 'mysql') {
            $clause = "REGEXP '^" . $this->escape(preg_quote($value)) . "[0-9]+$'";
        } elseif ($this->syntax == 'postgresql') {
            $clause = "~ '^" . $this->escape(preg_quote($value)) . "[0-9]+$'";
        } else {
            $clause = "LIKE '" .
                $this->escape(preg_replace(['/\%/', '/\_/', '/\!/'], ['!%', '!_', '!!'], $value)) . "%' ESCAPE '!'";
        }

        return ' ' . $clause;
    }

    /**
     * Return an DISTINCT clause
     *
     * @param      string       $field     The field
     *
     * @return     string
     */
    public function unique(string $field): string
    {
        return 'DISTINCT ' . $field;
    }

    /**
     * Return an COUNT(…) clause
     *
     * @param      string       $field     The field
     * @param      null|string  $as        Optional alias
     * @param      bool         $unique    Unique values only
     *
     * @return     string
     */
    public function count(string $field, ?string $as = null, bool $unique = false): string
    {
        return 'COUNT(' . ($unique ? $this->unique($field) : $field) . ')' . ($as ? $this->_AS . $as : '');
    }

    /**
     * Return an AVG(…) clause
     *
     * @param      string       $field     The field
     * @param      null|string  $as        Optional alias
     *
     * @return     string
     */
    public function avg(string $field, ?string $as = null): string
    {
        return 'AVG(' . $field . ')' . ($as ? $this->_AS . $as : '');
    }

    /**
     * Return an MAX(…) clause
     *
     * @param      string       $field     The field
     * @param      null|string  $as        Optional alias
     *
     * @return     string
     */
    public function max(string $field, ?string $as = null): string
    {
        return 'MAX(' . $field . ')' . ($as ? $this->_AS . $as : '');
    }

    /**
     * Return an MIN(…) clause
     *
     * @param      string       $field     The field
     * @param      null|string  $as        Optional alias
     *
     * @return     string
     */
    public function min(string $field, ?string $as = null): string
    {
        return 'MIN(' . $field . ')' . ($as ? $this->_AS . $as : '');
    }

    /**
     * Return an SUM(…) clause
     *
     * @param      string       $field     The field
     * @param      null|string  $as        Optional alias
     *
     * @return     string
     */
    public function sum(string $field, ?string $as = null): string
    {
        return 'SUM(' . $field . ')' . ($as ? $this->_AS . $as : '');
    }

    /**
     * Return an IS NULL clause
     *
     * @param      string       $field     The field
     *
     * @return     string
     */
    public function isNull(string $field): string
    {
        return $field . ' IS NULL';
    }

    /**
     * Return an IS NOT NULL clause
     *
     * @param      string       $field     The field
     *
     * @return     string
     */
    public function isNotNull(string $field): string
    {
        return $field . ' IS NOT NULL';
    }

    /**
     * Compare two SQL queries
     *
     * May be used for debugging purpose as:
     *
     * if (!$sql->isSame($sql->statement(), $strReq)) {
     *     trigger_error('SQL statement error: ' . $sql->statement() . ' / ' . $strReq, E_USER_ERROR);
     * }
     *
     * @param      string   $local     The local
     * @param      string   $external  The external
     *
     * @return     boolean  True if same, False otherwise.
     */
    public function isSame(string $local, string $external): bool
    {
        $filter = function ($s) {
            $s        = strtoupper($s);
            $patterns = [
                '\s+' => ' ', // Multiple spaces/tabs -> one space
                ' \)' => ')', // <space>) -> )
                ' ,'  => ',', // <space>, -> ,
                '\( ' => '(', // (<space> -> (
            ];
            foreach ($patterns as $pattern => $replace) {
                $s = preg_replace('!' . $pattern . '!', $replace, $s);
            }

            return trim((string) $s);
        };

        return ($filter($local) === $filter($external));
    }

    /**
     * Compare local statement and external one
     *
     * @param      string   $external       The external
     * @param      bool     $trigger_error  True to trigger an error if compare failsl
     * @param      bool     $dump           True to var_dump() all if compare fails
     * @param      bool     $print          True to print_r() all if compare fails
     *
     * @return     bool
     */
    public function compare(string $external, bool $trigger_error = false, bool $dump = false, bool $print = false): bool
    {
        $str = $this->statement();
        if (!$this->isSame($str, $external)) {
            if ($print) {
                print_r($str);
                print_r($external);
            } elseif ($dump) {
                var_dump($str);
                var_dump($external);
            }
            if ($trigger_error) {
                trigger_error('SQL statement error (internal/external): ' . $str . ' / ' . $external, E_USER_ERROR);
            }

            return false;
        }

        return true;
    }
}

/**
 * Select Statement : small utility to build select queries
 */
class dcSelectStatement extends dcSqlStatement
{
    protected $join;
    protected $union;
    protected $having;
    protected $order;
    protected $group;
    protected $limit;
    protected $offset;
    protected $distinct;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->join = $this->union = $this->having = $this->order = $this->group = [];

        $this->limit    = null;
        $this->offset   = null;
        $this->distinct = false;

        parent::__construct();
    }

    /**
     * Adds JOIN clause(s) (applied on first from item only)
     *
     * @param mixed     $c      the join clause(s)
     * @param boolean   $reset  reset previous join(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function join($c, bool $reset = false): dcSelectStatement
    {
        if ($reset) {
            $this->join = [];
        }
        if (is_array($c)) {
            $this->join = array_merge($this->join, $c);
        } else {
            array_push($this->join, $c);
        }

        return $this;
    }

    /**
     * Adds UNION clause(s)
     *
     * @param mixed     $c      the union clause(s)
     * @param boolean   $reset  reset previous union(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function union($c, bool $reset = false): dcSelectStatement
    {
        if ($reset) {
            $this->union = [];
        }
        if (is_array($c)) {
            $this->union = array_merge($this->union, $c);
        } else {
            array_push($this->union, $c);
        }

        return $this;
    }

    /**
     * Adds HAVING clause(s)
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous having(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function having($c, bool $reset = false): dcSelectStatement
    {
        if ($reset) {
            $this->having = [];
        }
        if (is_array($c)) {
            $this->having = array_merge($this->having, $c);
        } else {
            array_push($this->having, $c);
        }

        return $this;
    }

    /**
     * Adds ORDER BY clause(s)
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous order(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function order($c, bool $reset = false): dcSelectStatement
    {
        if ($reset) {
            $this->order = [];
        }
        if (is_array($c)) {
            $this->order = array_merge($this->order, $c);
        } else {
            array_push($this->order, $c);
        }

        return $this;
    }

    /**
     * Adds GROUP BY clause(s)
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous group(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function group($c, bool $reset = false): dcSelectStatement
    {
        if ($reset) {
            $this->group = [];
        }
        if (is_array($c)) {
            $this->group = array_merge($this->group, $c);
        } else {
            array_push($this->group, $c);
        }

        return $this;
    }

    /**
     * group() alias
     *
     * @param mixed     $c      the clause(s)
     * @param boolean   $reset  reset previous group(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function groupBy($c, bool $reset = false): dcSelectStatement
    {
        return $this->group($c, $reset);
    }

    /**
     * Defines the LIMIT for select
     *
     * @param mixed $limit (limit or [offset,limit])
     * @return self instance, enabling to chain calls
     */
    public function limit($limit): dcSelectStatement
    {
        $offset = null;
        if (is_array($limit)) {
            // Keep only values
            $limit = array_values($limit);
            // If 2 values, [0] -> offset, [1] -> limit
            // If 1 value, [0] -> limit
            if (isset($limit[1])) {
                $offset = $limit[0];
                $limit  = $limit[1];
            } else {
                $limit = $limit[0];
            }
        }
        $this->limit = $limit;
        if ($offset !== null) {
            $this->offset = $offset;
        }

        return $this;
    }

    /**
     * Defines the OFFSET for select
     *
     * @param integer $offset
     * @return self instance, enabling to chain calls
     */
    public function offset(int $offset): dcSelectStatement
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Defines the DISTINCT flag for select
     *
     * @param boolean $distinct
     * @return self instance, enabling to chain calls
     */
    public function distinct(bool $distinct = true): dcSelectStatement
    {
        $this->distinct = $distinct;

        return $this;
    }

    /**
     * Returns the select statement
     *
     * @return string the statement
     */
    public function statement(): string
    {
        # --BEHAVIOR-- coreBeforeSelectStatement
        dcCore::app()->callBehavior('coreBeforeSelectStatement', $this);

        // Check if source given
        if (!count($this->from)) {
            trigger_error(__('SQL SELECT requires a FROM source'), E_USER_ERROR);
        }

        // Query
        $query = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '');

        // Specific column(s) or all (*)
        if (count($this->columns)) {
            $query .= join(', ', $this->columns) . ' ';
        } else {
            $query .= '* ';
        }

        // Table(s) and Join(s)
        $query .= 'FROM ' . $this->from[0] . ' ';
        if (is_countable($this->join) ? count($this->join) : 0) {
            $query .= join(' ', $this->join) . ' ';
        }
        if (count($this->from) > 1) {
            $query .= ', ' . join(', ', array_slice($this->from, 1)) . ' '; // All other from(s)
        }

        // Where clause(s)
        if (count($this->where)) {
            $query .= 'WHERE ' . join(' AND ', $this->where) . ' ';
        }

        // Direct where clause(s)
        if (count($this->cond)) {
            if (!count($this->where)) {
                // Hack to cope with the operator included in top of each condition
                $query .= 'WHERE ' . ($this->syntax === 'sqlite' ? '1' : 'TRUE') . ' ';
            }
            $query .= join(' ', $this->cond) . ' ';
        }

        // Generic clause(s)
        if (count($this->sql)) {
            $query .= join(' ', $this->sql) . ' ';
        }

        // Group by clause (columns or aliases)
        if (is_countable($this->group) ? count($this->group) : 0) {
            $query .= 'GROUP BY ' . join(', ', $this->group) . ' ';
        }

        // Having clause(s)
        if (is_countable($this->having) ? count($this->having) : 0) {
            $query .= 'HAVING ' . join(' AND ', $this->having) . ' ';
        }

        // Union clause(s)
        if (is_countable($this->union) ? count($this->union) : 0) {
            $query .= 'UNION ' . join(' UNION ', $this->union) . ' ';
        }

        // Clauses applied on result
        // -------------------------

        // Order by clause (columns or aliases and optionnaly order ASC/DESC)
        if (is_countable($this->order) ? count($this->order) : 0) {
            $query .= 'ORDER BY ' . join(', ', $this->order) . ' ';
        }

        // Limit clause
        if ($this->limit !== null) {
            $query .= 'LIMIT ' . $this->limit . ' ';
        }

        // Offset clause
        if ($this->offset !== null) {
            $query .= 'OFFSET ' . $this->offset . ' ';
        }

        $query = trim($query);

        # --BEHAVIOR-- coreAfertSelectStatement
        dcCore::app()->callBehavior('coreAfterSelectStatement', $this, $query);

        return $query;
    }

    /**
     * Run the SQL select query and return result
     *
     * @return     dcRecord  record
     */
    public function select(): ?dcRecord
    {
        if ($this->con && ($sql = $this->statement())) {
            return new dcRecord($this->con->select($sql));
        }

        return null;
    }

    /**
     * select() alias
     *
     * @return     dcRecord  record
     */
    public function run(): ?dcRecord
    {
        return $this->select();
    }
}

/**
 * Join (sub)Statement : small utility to build join query fragments
 */
class dcJoinStatement extends dcSqlStatement
{
    protected $type;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->type = null;

        parent::__construct();
    }

    /**
     * Defines the type for join
     *
     * @param string $type
     * @return self instance, enabling to chain calls
     */
    public function type(string $type = ''): dcJoinStatement
    {
        $this->type = strtoupper($type);

        return $this;
    }

    /**
     * Defines LEFT join type
     *
     * @return     dcJoinStatement
     */
    public function left(): dcJoinStatement
    {
        return $this->type('LEFT');
    }

    /**
     * Defines RIGHT join type
     *
     * @return     dcJoinStatement
     */
    public function right(): dcJoinStatement
    {
        return $this->type('RIGHT');
    }

    /**
     * Defines INNER join type
     *
     * @return     dcJoinStatement
     */
    public function inner(): dcJoinStatement
    {
        return $this->type('INNER');
    }

    /**
     * Returns the join fragment
     *
     * @return string the fragment
     */
    public function statement(): string
    {
        # --BEHAVIOR-- coreBeforeDeleteStatement
        dcCore::app()->callBehavior('coreBeforeJoinStatement', $this);

        // Check if source given
        if (!count($this->from)) {
            trigger_error(__('SQL JOIN requires a source'), E_USER_ERROR);
        }

        // Query
        $query = 'JOIN ';

        if ($this->type) {
            // LEFT, RIGHT, …
            $query = $this->type . ' ' . $query;
        }

        // Table
        $query .= ' ' . $this->from[0] . ' ';

        // Where clause(s)
        if (count($this->where)) {
            $query .= 'ON ' . join(' AND ', $this->where) . ' ';
        }

        // Direct where clause(s)
        if (count($this->cond)) {
            $query .= join(' ', $this->cond) . ' ';
        }

        // Generic clause(s)
        if (count($this->sql)) {
            $query .= join(' ', $this->sql) . ' ';
        }

        $query = trim($query);

        # --BEHAVIOR-- coreAfertSelectStatement
        dcCore::app()->callBehavior('coreAfterJoinStatement', $this, $query);

        return $query;
    }
}

/**
 * Delete Statement : small utility to build delete queries
 */
class dcDeleteStatement extends dcSqlStatement
{
    /**
     * Returns the delete statement
     *
     * @return string the statement
     */
    public function statement(): string
    {
        # --BEHAVIOR-- coreBeforeDeleteStatement
        dcCore::app()->callBehavior('coreBeforeDeleteStatement', $this);

        // Check if source given
        if (!count($this->from)) {
            trigger_error(__('SQL DELETE requires a FROM source'), E_USER_ERROR);
        }

        // Query
        $query = 'DELETE ';

        // Table
        $query .= 'FROM ' . $this->from[0] . ' ';

        // Where clause(s)
        if (count($this->where)) {
            $query .= 'WHERE ' . join(' AND ', $this->where) . ' ';
        }

        // Direct where clause(s)
        if (count($this->cond)) {
            if (!count($this->where)) {
                // Hack to cope with the operator included in top of each condition
                $query .= 'WHERE ' . ($this->syntax === 'sqlite' ? '1' : 'TRUE') . ' ';
            }
            $query .= join(' ', $this->cond) . ' ';
        }

        // Generic clause(s)
        if (count($this->sql)) {
            $query .= join(' ', $this->sql) . ' ';
        }

        $query = trim($query);

        # --BEHAVIOR-- coreAfertDeleteStatement
        dcCore::app()->callBehavior('coreAfterDeleteStatement', $this, $query);

        return $query;
    }

    /**
     * Run the SQL select query and return result
     *
     * @return     bool
     */
    public function delete(): bool
    {
        if ($this->con && ($sql = $this->statement())) {
            return $this->con->execute($sql);
        }

        return false;
    }

    /**
     * delete() alias
     *
     * @return     bool
     */
    public function run(): bool
    {
        return $this->delete();
    }
}

/**
 * Update Statement : small utility to build update queries
 */
class dcUpdateStatement extends dcSqlStatement
{
    protected $set;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->set = [];

        parent::__construct();
    }

    /**
     * from() alias
     *
     * @param mixed     $c      the reference clause(s)
     * @param boolean   $reset  reset previous reference first
     *
     * @return self instance, enabling to chain calls
     */
    public function reference($c, bool $reset = false): dcUpdateStatement
    {
        $this->from($c, $reset);

        return $this;
    }

    /**
     * from() alias
     *
     * @param mixed     $c      the reference clause(s)
     * @param boolean   $reset  reset previous reference first
     *
     * @return self instance, enabling to chain calls
     */
    public function ref($c, bool $reset = false): dcUpdateStatement
    {
        return $this->from($c, $reset);
    }

    /**
     * Adds update value(s)
     *
     * @param mixed     $c      the udpate values(s)
     * @param boolean   $reset  reset previous update value(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function set($c, bool $reset = false): dcUpdateStatement
    {
        if ($reset) {
            $this->set = [];
        }
        if (is_array($c)) {
            $this->set = array_merge($this->set, $c);
        } else {
            array_push($this->set, $c);
        }

        return $this;
    }

    /**
     * set() alias
     *
     * @param      mixed    $c      the update value(s)
     * @param      boolean  $reset  reset previous update value(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function sets($c, bool $reset = false): dcUpdateStatement
    {
        return $this->set($c, $reset);
    }

    /**
     * Returns the WHERE part of update statement
     *
     * Useful to construct the where clause used with cursor->update() method
     *
     * @return string The where part of update statement
     */
    public function whereStatement(): string
    {
        # --BEHAVIOR-- coreBeforeUpdateWhereStatement
        dcCore::app()->callBehavior('coreBeforeUpdateWhereStatement', $this);

        $query = '';

        // Where clause(s)
        if (count($this->where)) {
            $query .= 'WHERE ' . join(' AND ', $this->where) . ' ';
        }

        // Direct where clause(s)
        if (count($this->cond)) {
            if (!count($this->where)) {
                // Hack to cope with the operator included in top of each condition
                $query .= 'WHERE ' . ($this->syntax === 'sqlite' ? '1' : 'TRUE') . ' ';
            }
            $query .= join(' ', $this->cond) . ' ';
        }

        // Generic clause(s)
        if (count($this->sql)) {
            $query .= join(' ', $this->sql) . ' ';
        }

        $query = trim($query);

        # --BEHAVIOR-- coreAfertUpdateWhereStatement
        dcCore::app()->callBehavior('coreAfterUpdateWhereStatement', $this, $query);

        return $query;
    }

    /**
     * Returns the update statement
     *
     * @return string the statement
     */
    public function statement(): string
    {
        # --BEHAVIOR-- coreBeforeUpdateStatement
        dcCore::app()->callBehavior('coreBeforeUpdateStatement', $this);

        // Check if source given
        if (!count($this->from)) {
            trigger_error(__('SQL UPDATE requires an INTO source'), E_USER_ERROR);
        }

        // Query
        $query = 'UPDATE ';

        // Reference
        $query .= $this->from[0] . ' ';

        // Value(s)
        if (is_countable($this->set) ? count($this->set) : 0) {
            $query .= 'SET ' . join(', ', $this->set) . ' ';
        }

        // Where
        $query .= $this->whereStatement();

        $query = trim($query);

        # --BEHAVIOR-- coreAfertUpdateStatement
        dcCore::app()->callBehavior('coreAfterUpdateStatement', $this, $query);

        return $query;
    }

    /**
     * Run the SQL update query
     *
     * @param      cursor|null  $cur    The cursor
     *
     * @return     bool
     */
    public function update(?cursor $cur = null): bool
    {
        if ($cur) {
            return $cur->update($this->whereStatement());
        }

        if ($this->con && ($sql = $this->statement())) {
            return $this->con->execute($sql);
        }

        return false;
    }

    /**
     * update() alias
     *
     * @param      cursor|null  $cur    The cursor
     *
     * @return     bool
     */
    public function run(?cursor $cur = null): bool
    {
        return $this->update($cur);
    }
}

/**
 * Insert Statement : small utility to build insert queries
 */
class dcInsertStatement extends dcSqlStatement
{
    protected $lines;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->lines = [];

        parent::__construct();
    }

    /**
     * from() alias
     *
     * @param mixed     $c      the into clause(s)
     * @param boolean   $reset  reset previous into first
     *
     * @return self instance, enabling to chain calls
     */
    public function into($c, bool $reset = false): dcInsertStatement
    {
        $this->from($c, $reset);

        return $this;
    }

    /**
     * Adds update value(s)
     *
     * @param mixed     $c      the insert values(s)
     * @param boolean   $reset  reset previous insert value(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function lines($c, bool $reset = false): dcInsertStatement
    {
        if ($reset) {
            $this->lines = [];
        }
        if (is_array($c)) {
            $this->lines = array_merge($this->lines, $c);
        } else {
            array_push($this->lines, $c);
        }

        return $this;
    }

    /**
     * line() alias
     *
     * @param      mixed    $c      the insert value(s)
     * @param      boolean  $reset  reset previous insert value(s) first
     *
     * @return self instance, enabling to chain calls
     */
    public function line($c, bool $reset = false): dcInsertStatement
    {
        return $this->lines($c, $reset);
    }

    /**
     * Returns the insert statement
     *
     * @return string the statement
     */
    public function statement(): string
    {
        # --BEHAVIOR-- coreBeforeInsertStatement
        dcCore::app()->callBehavior('coreBeforeInsertStatement', $this);

        // Check if source given
        if (!count($this->from)) {
            trigger_error(__('SQL INSERT requires an INTO source'), E_USER_ERROR);
        }

        // Query
        $query = 'INSERT ';

        // Reference
        $query .= 'INTO ' . $this->from[0] . ' ';

        // Column(s)
        if (count($this->columns)) {
            $query .= '(' . join(', ', $this->columns) . ') ';
        }

        // Value(s)
        $query .= 'VALUES ';
        if (is_countable($this->lines) ? count($this->lines) : 0) {
            $raws = [];
            foreach ($this->lines as $line) {
                $raws[] = '(' . join(', ', $line) . ')';
            }
            $query .= join(', ', $raws);
        } else {
            // Use SQL default values
            // (useful only if SQL strict mode is off or if every columns has a defined default value)
            $query .= '()';
        }

        $query = trim($query);

        # --BEHAVIOR-- coreAfertInsertStatement
        dcCore::app()->callBehavior('coreAfterInsertStatement', $this, $query);

        return $query;
    }

    /**
     * Run the SQL select query and return result
     *
     * @return     bool  true
     */
    public function insert(): bool
    {
        if ($this->con && ($sql = $this->statement())) {
            return $this->con->execute($sql);
        }

        return false;
    }

    /**
     * insert() alias
     *
     * @return     bool
     */
    public function run(): bool
    {
        return $this->insert();
    }
}

/**
 * Truncate Statement : small utility to build truncate queries
 */
class dcTruncateStatement extends dcSqlStatement
{
    /**
     * Returns the truncate statement
     *
     * @return string the statement
     */
    public function statement(): string
    {
        # --BEHAVIOR-- coreBeforeInsertStatement
        dcCore::app()->callBehavior('coreBeforeTruncateStatement', $this);

        // Check if source given
        if (!count($this->from)) {
            trigger_error(__('SQL TRUNCATE TABLE requires a table source'), E_USER_ERROR);
        }

        // Query
        $query = 'TRUNCATE ';

        // Reference
        $query .= 'TABLE ' . $this->from[0] . ' ';

        $query = trim($query);

        # --BEHAVIOR-- coreAfertInsertStatement
        dcCore::app()->callBehavior('coreAfterTruncateStatement', $this, $query);

        return $query;
    }

    /**
     * Run the SQL select query and return result
     *
     * @return     bool
     */
    public function truncate(): bool
    {
        if ($this->con && ($sql = $this->statement())) {
            return $this->con->execute($sql);
        }

        return false;
    }

    /**
     * truncate() alias
     *
     * @return     bool
     */
    public function run(): bool
    {
        return $this->truncate();
    }
}
