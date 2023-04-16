<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class adminGenericList extends adminGenericListV2
{
    /**
     * Constructs a new instance.
     *
     * @param      mixed   $rs        The record
     * @param      mixed   $rs_count  The rs count
     */
    public function __construct(dcCore $core, $rs, $rs_count)   // @phpstan-ignore-line
    {
        if ($rs && !($rs instanceof dcRecord)) {
            $rs = new dcRecord($rs);
        }
        parent::__construct($rs, $rs_count);
    }
}
