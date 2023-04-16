<?php
/**
 * @package Dotclear
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

/**
 * Helper to autoload class using php namespace.
 *
 * Based on PSR-4 Autoloader
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
 *
 * A root prefix and base directory can be added to all ns
 * to work with non full standardized project.
 *
 * @ingroup  Helper Autoload Stack
 */
class Autoloader
{
    /** Directory separator */
    public const DIR_SEP = DIRECTORY_SEPARATOR;

    /** Namespace separator */
    public const NS_SEP = '\\';

    /**
     * @var string $root_prefix
     *             Root namespace prepend to added ns
     */
    private $root_prefix = '';

    /**
     * @var string $root_base_dir
     *             Root directory prepend to added ns
     */
    private $root_base_dir = '';

    /**
     * @var array<string,array> $prefixes
     *                          Array of registered namespace [prefix=[base dir]]
     */
    private $prefixes = [];

    /**
     * @var int $loads_count
     *          Keep track of loads count
     */
    private $loads_count = 0;

    /**
     * @var int $request_count
     *          Keep track of request count
     */
    private $request_count = 0;

    /**
     * Register loader with SPL autoloader stack.
     *
     * @param string $root_prefix   Common ns prefix
     * @param string $root_base_dir Common dir prefix
     * @param bool   $prepend       Add loader on top of stack
     */
    public function __construct(string $root_prefix = '', string $root_base_dir = '', bool $prepend = false)
    {
        if (!empty($root_prefix)) {
            $this->root_prefix = $this->normalizePrefix($root_prefix);
        }
        if (!empty($root_base_dir)) {
            $this->root_base_dir = $this->normalizeBaseDir($root_base_dir);
        }

        // @phpstan-ignore-next-line (Failed to see array as callable but works great)
        spl_autoload_register([$this, 'loadClass'], true, $prepend);
    }

    /**
     * Get root prefix.
     *
     * @return string Root prefix
     */
    public function getRootPrefix(): string
    {
        return $this->root_prefix;
    }

    /**
     * Get root base directory.
     *
     * @return string Root base directory
     */
    public function getRootBaseDir(): string
    {
        return $this->root_base_dir;
    }

    /**
     * Normalize namespace prefix.
     *
     * @param string $prefix Ns prefix
     *
     * @return string Prefix with only right namesapce separator
     */
    public function normalizePrefix(string $prefix): string
    {
        return ucfirst(trim($prefix, self::NS_SEP)) . self::NS_SEP;
    }

    /**
     * Normalize base directory.
     *
     * @param string $base_dir Dir prefix
     *
     * @return string Base dir with right directory separator
     */
    public function normalizeBaseDir(string $base_dir): string
    {
        return rtrim($base_dir, self::DIR_SEP) . self::DIR_SEP;
    }

    /**
     * Clean up a string into namespace part.
     *
     * @param string $str string to clean
     *
     * @return null|string Cleaned string or null if empty
     */
    public function qualifyNamespace(string $str): ?string
    {
        $str = preg_replace(
            [
                '/[^a-zA-Z0-9_' . preg_quote(self::NS_SEP) . ']/',
                '/[' . preg_quote(self::NS_SEP) . ']{2,}/',
            ],
            [
                '',
                self::NS_SEP,
            ],
            $str
        );

        return empty($str) ? null : $this->normalizePrefix($str);
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix   the namespace prefix
     * @param string $base_dir a base directory for class files in the namespace
     * @param bool   $prepend  if true, prepend the base directory to the stack
     *                         instead of appending it; this causes it to be searched first rather
     *                         than last
     */
    public function addNamespace(string $prefix, string $base_dir, bool $prepend = false): void
    {
        $prefix   = $this->root_prefix . $this->normalizePrefix($prefix);
        $base_dir = $this->root_base_dir . $this->normalizeBaseDir($base_dir);

        if (false === isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = [];
        }

        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }

    /**
     * Get list of registered namespace.
     *
     * @return array List of namesapce prefix / base dir
     */
    public function getNamespaces(): array
    {
        return $this->prefixes;
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class the fully-qualified class name
     *
     * @return null|string the mapped file name on success, or null on failure
     */
    public function loadClass(string $class): ?string
    {
        ++$this->request_count;
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, self::NS_SEP)) {
            $prefix         = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);

            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            $prefix = rtrim($prefix, self::NS_SEP);
        }

        return null;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $prefix         the namespace prefix
     * @param string $relative_class the relative class name
     *
     * @return null|string null if no mapped file can be loaded, or the
     *                     name of the mapped file that was loaded
     */
    private function loadMappedFile(string $prefix, string $relative_class): ?string
    {
        if (false === isset($this->prefixes[$prefix])) {
            return null;
        }

        foreach ($this->prefixes[$prefix] as $base_dir) {
            $file = $base_dir
                  . str_replace(self::NS_SEP, self::DIR_SEP, $relative_class)
                  . '.php';

            if ($this->requireFile($file)) {
                return $file;
            }
        }

        return null;
    }

    /**
     * If a file exists, require it from the file system.
     *
     * @param string $file the file to require
     *
     * @return bool true if the file exists, false if not
     */
    private function requireFile(string $file): bool
    {
        if (is_file($file)) {
            ++$this->loads_count;

            require $file;

            return true;
        }

        return false;
    }

    /**
     * Get number of loads on this autoloader.
     *
     * @return int Number of loads
     */
    public function getLoadsCount(): int
    {
        return $this->loads_count;
    }

    /**
     * Get number of requests on this autoloader.
     *
     * @return int Number of requests
     */
    public function getRequestsCount(): int
    {
        return $this->request_count;
    }
}
