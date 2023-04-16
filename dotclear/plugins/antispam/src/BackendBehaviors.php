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

namespace Dotclear\Plugin\antispam;

use ArrayObject;
use dcCore;
use dcSettings;
use form;

class BackendBehaviors
{
    /**
     * Add an antispam help ID if necessary
     *
     * @param      ArrayObject  $blocks  The blocks
     */
    public static function adminPageHelpBlock(ArrayObject $blocks): void
    {
        if (array_search('core_comments', $blocks->getArrayCopy(), true) !== false) {
            $blocks->append('antispam_comments');
        }
    }

    /**
     * Display information about spam deletion
     */
    public static function adminCommentsSpamForm(): void
    {
        $ttl = dcCore::app()->blog->settings->antispam->antispam_moderation_ttl;
        if ($ttl != null && $ttl >= 0) {
            echo '<p>' . sprintf(__('All spam comments older than %s day(s) will be automatically deleted.'), $ttl) . ' ' .
            sprintf(__('You can modify this duration in the %s'), '<a href="' . dcCore::app()->adminurl->get('admin.blog.pref') .
                '#antispam_moderation_ttl"> ' . __('Blog settings') . '</a>') .
                '.</p>';
        }
    }

    /**
     * Display fieldset for spam deletion setting
     *
     * @param      dcSettings  $settings  The settings
     */
    public static function adminBlogPreferencesForm(dcSettings $settings): void
    {
        echo
        '<div class="fieldset"><h4 id="antispam_params">Antispam</h4>' .
        '<p><label for="antispam_moderation_ttl" class="classic">' . __('Delete junk comments older than') . ' ' .
        form::number('antispam_moderation_ttl', [
            'min'     => -1,
            'max'     => 999, 
            'default' => $settings->antispam->antispam_moderation_ttl,
        ]) .
        ' ' . __('days') .
        '</label></p>' .
        '<p class="form-note">' . __('Set -1 to disabled this feature ; Leave empty to use default 7 days delay.') . '</p>' .
        '<p><a href="' . dcCore::app()->adminurl->get('admin.plugin.antispam') . '">' . __('Set spam filters.') . '</a></p>' .
        '</div>';
    }

    /**
     * Save the spam deletion setting
     *
     * @param      dcSettings  $settings  The settings
     */
    public static function adminBeforeBlogSettingsUpdate(dcSettings $settings): void
    {
        $settings->antispam->put('antispam_moderation_ttl', (int) $_POST['antispam_moderation_ttl']);
    }
}
