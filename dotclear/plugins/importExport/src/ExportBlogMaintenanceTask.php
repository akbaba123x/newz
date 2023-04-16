<?php
/**
 * @brief importExport, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\importExport;

use dcCore;
use Dotclear\Plugin\maintenance\MaintenanceTask;
use form;
use html;

class ExportBlogMaintenanceTask extends MaintenanceTask
{
    protected $perm  = 'admin';
    protected $tab   = 'backup';
    protected $group = 'zipblog';

    protected $export_name;
    protected $export_type;

    /**
     * Initialize task object.
     */
    protected function init(): void
    {
        $this->name = __('Database export');
        $this->task = __('Download database of current blog');

        $this->export_name = html::escapeHTML(dcCore::app()->blog->id . '-backup.txt');
        $this->export_type = 'export_blog';
    }

    /**
     * Execute task.
     *
     * @return    mixed
     *    - FALSE on error,
     *    - TRUE if task is finished
     *    - INT if task required a next step
     */
    public function execute()
    {
        // Create zip file
        if (!empty($_POST['file_name'])) {
            if (empty($_POST['your_pwd']) || !dcCore::app()->auth->checkPassword($_POST['your_pwd'])) {
                $this->error = __('Password verification failed');

                return false;
            }

            // This process make an http redirect
            $ie = new ExportFlatMaintenanceTask();
            $ie->setURL($this->id);
            $ie->process($this->export_type);
        }
        // Go to step and show form
        else {
            return 1;
        }
    }

    /**
     * Get step message.
     *
     * This message is displayed during task step execution.
     *
     * @return    mixed    Message or null
     */
    public function step()
    {
        // Download zip file
        if (isset($_SESSION['export_file']) && file_exists($_SESSION['export_file'])) {
            // Log task execution here as we sent file and stop script
            $this->log();

            // This process send file by http and stop script
            $ie = new ExportFlatMaintenanceTask();
            $ie->setURL($this->id);
            $ie->process('ok');
        } else {
            return
            '<p><label for="file_name">' . __('File name:') . '</label>' .
            form::field('file_name', 50, 255, date('Y-m-d-H-i-') . $this->export_name) .
            '</p>' .
            '<p><label for="file_zip" class="classic">' .
            form::checkbox('file_zip', 1) . ' ' .
            __('Compress file') . '</label>' .
            '</p>' .
            '<p><label for="your_pwd" class="required">' .
            '<abbr title="' . __('Required field') . '">*</abbr> ' . __('Your password:') . '</label>' .
            form::password(
                'your_pwd',
                20,
                255,
                [
                    'extra_html'   => 'required placeholder="' . __('Password') . '"',
                    'autocomplete' => 'current-password',
                ]
            ) . '</p>';
        }
    }
}
