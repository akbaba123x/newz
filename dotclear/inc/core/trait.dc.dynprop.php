<?php
/**
 * @package Dotclear
 * @subpackage Core
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 *
 * @brief Dotclear dynamic properties management trait.
 *
 * Dotclear trait to add dynamic properties management to a class.
 */
trait dcTraitDynamicProperties
{
    // User-defined - experimental (may be changed in future)

    /**
     * User-defined properties
     *
     * @var        array(<string>, <mixed>)
     */
    protected $properties = [];

    /**
     * Magic function, store a property and its value
     *
     * @param      string  $identifier  The identifier
     * @param      mixed   $value       The value
     */
    public function __set(string $identifier, $value = null)
    {
        $this->properties[$identifier] = $value;
    }

    /**
     * Gets the specified property value (null if does not exist).
     *
     * @param      string  $identifier  The identifier
     *
     * @return     mixed
     */
    public function __get(string $identifier)
    {
        return array_key_exists($identifier, $this->properties) ? $this->properties[$identifier] : null;
    }

    /**
     * Test if a property exists
     *
     * @param      string  $identifier  The identifier
     *
     * @return     bool
     */
    public function __isset(string $identifier): bool
    {
        return isset($this->properties[$identifier]);
    }

    /**
     * Unset a property
     *
     * @param      string  $identifier  The identifier
     */
    public function __unset(string $identifier)
    {
        if (array_key_exists($identifier, $this->properties)) {
            unset($this->properties[$identifier]);
        }
    }
}
