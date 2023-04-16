<?php

declare(strict_types=1);

/**
 * @class formLegend
 * @brief HTML Forms legend creation helpers
 *
 * @package Clearbricks
 * @subpackage html.form
 *
 * @since 1.2 First time this was introduced.
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class formLegend extends formComponent
{
    private const DEFAULT_ELEMENT = 'legend';

    /**
     * Constructs a new instance.
     *
     * @param      string $text     The text
     * @param      mixed  $id       The identifier
     * @param      string $element  The element
     */
    public function __construct(string $text = '', $id = null, ?string $element = null)
    {
        parent::__construct(self::class, $element ?? self::DEFAULT_ELEMENT);
        $this->text($text);
        if ($id !== null) {
            $this->setIdentifier($id);
        }
    }

    /**
     * Renders the HTML component.
     *
     * @return     string
     */
    public function render(): string
    {
        $buffer = '<' . ($this->getElement() ?? self::DEFAULT_ELEMENT) . $this->renderCommonAttributes() . '>';
        if ($this->text) {
            $buffer .= $this->text;
        }
        $buffer .= '</' . ($this->getElement() ?? self::DEFAULT_ELEMENT) . '>' . "\n";

        return $buffer;
    }

    /**
     * Gets the default element.
     *
     * @return     string  The default element.
     */
    public function getDefaultElement(): string
    {
        return self::DEFAULT_ELEMENT;
    }
}
