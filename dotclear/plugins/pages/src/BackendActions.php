<?php
/**
 * @brief pages, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\pages;

use dcCore;
use dcPage;
use dcPostsActions;
use Exception;
use html;

class BackendActions extends dcPostsActions
{
    protected $use_render = true;

    /**
     * Constructs a new instance.
     *
     * @param      null|string  $uri            The uri
     * @param      array        $redirect_args  The redirect arguments
     */
    public function __construct(?string $uri, array $redirect_args = [])
    {
        parent::__construct($uri, $redirect_args);

        $this->redirect_fields = [];
        $this->caller_title    = __('Pages');
    }

    /**
     * Display error page
     *
     * @param      Exception  $e      { parameter_description }
     */
    public function error(Exception $e)
    {
        dcCore::app()->error->add($e->getMessage());
        $this->beginPage(
            dcPage::breadcrumb(
                [
                    html::escapeHTML(dcCore::app()->blog->name) => '',
                    __('Pages')                                 => $this->getRedirection(true),
                    __('Pages actions')                         => '',
                ]
            )
        );
        $this->endPage();
    }

    /**
     * Begins a page.
     *
     * @param      string  $breadcrumb  The breadcrumb
     * @param      string  $head        The head
     */
    public function beginPage(string $breadcrumb = '', string $head = ''): void
    {
        echo
        '<html><head><title>' . __('Pages') . '</title>' .
        dcPage::jsLoad('js/_posts_actions.js') .
        $head .
        '</script></head><body>' .
        $breadcrumb .
        '<p><a class="back" href="' . $this->getRedirection(true) . '">' . __('Back to pages list') . '</a></p>';
    }

    /**
     * Ends a page.
     */
    public function endPage(): void
    {
        echo
        '</body></html>';
    }

    /**
     * Set pages actions
     */
    public function loadDefaults(): void
    {
        // We could have added a behavior here, but we want default action to be setup first
        BackendDefaultActions::adminPagesActionsPage($this);
        dcCore::app()->callBehavior('adminPagesActions', $this);
    }

    /**
     * Proceeds action handling, if any
     *
     * This method may issue an exit() if an action is being processed.
     *  If it returns, no action has been performed
     *
     *  @return mixed
     */
    public function process()
    {
        // fake action for pages reordering
        if (!empty($this->from['reorder'])) {
            $this->from['action'] = 'reorder';
        }
        $this->from['post_type'] = 'page';

        return parent::process();
    }
}
