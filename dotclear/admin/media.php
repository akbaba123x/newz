<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
require __DIR__ . '/../inc/admin/prepend.php';

class adminMedia
{
    /**
     * Initializes the page.
     */
    public static function init()
    {
        dcPage::check(dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_MEDIA,
            dcAuth::PERMISSION_MEDIA_ADMIN,
        ]));

        dcCore::app()->admin->page = new adminMediaPage();
    }

    /**
     * Processes the request(s).
     */
    public static function process()
    {
        # Zip download
        if (!empty($_GET['zipdl']) && dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_MEDIA_ADMIN,
        ]), dcCore::app()->blog->id)) {
            try {
                if (strpos(realpath(dcCore::app()->media->root . '/' . dcCore::app()->admin->page->d), (string) realpath(dcCore::app()->media->root)) === 0) {
                    // Media folder or one of it's sub-folder(s)
                    @set_time_limit(300);
                    $fp  = fopen('php://output', 'wb');
                    $zip = new fileZip($fp);
                    $zip->addExclusion('#(^|/).(.*?)_(m|s|sq|t).jpg$#');
                    $zip->addDirectory(dcCore::app()->media->root . '/' . dcCore::app()->admin->page->d, '', true);

                    header('Content-Disposition: attachment;filename=' . date('Y-m-d') . '-' . dcCore::app()->blog->id . '-' . (dcCore::app()->admin->page->d ?: 'media') . '.zip');
                    header('Content-Type: application/x-zip');
                    $zip->write();
                    unset($zip);
                    exit;
                }
                dcCore::app()->admin->page->d = null;
                dcCore::app()->media->chdir(dcCore::app()->admin->page->d);

                throw new Exception(__('Not a valid directory'));
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        # User last and fav dirs
        if (dcCore::app()->admin->page->showLast()) {
            if (!empty($_GET['fav']) && dcCore::app()->admin->page->updateFav(rtrim((string) dcCore::app()->admin->page->d, '/'), $_GET['fav'] == 'n')) {
                dcCore::app()->adminurl->redirect('admin.media', dcCore::app()->admin->page->values());
            }
            dcCore::app()->admin->page->updateLast(rtrim((string) dcCore::app()->admin->page->d, '/'));
        }

        # New directory
        if (dcCore::app()->admin->page->getDirs() && !empty($_POST['newdir'])) {
            $nd = files::tidyFileName($_POST['newdir']);
            if (array_filter(dcCore::app()->admin->page->getDirs('files'), fn ($i) => ($i->basename === $nd))
        || array_filter(dcCore::app()->admin->page->getDirs('dirs'), fn ($i) => ($i->basename === $nd))
            ) {
                dcPage::addWarningNotice(sprintf(
                    __('Directory or file "%s" already exists.'),
                    html::escapeHTML($nd)
                ));
            } else {
                try {
                    dcCore::app()->media->makeDir($_POST['newdir']);
                    dcPage::addSuccessNotice(sprintf(
                        __('Directory "%s" has been successfully created.'),
                        html::escapeHTML($nd)
                    ));
                    dcCore::app()->adminurl->redirect('admin.media', dcCore::app()->admin->page->values());
                } catch (Exception $e) {
                    dcCore::app()->error->add($e->getMessage());
                }
            }
        }

        # Adding a file
        if (dcCore::app()->admin->page->getDirs() && !empty($_FILES['upfile'])) {
            // only one file per request : @see option singleFileUploads in admin/js/jsUpload/jquery.fileupload
            $upfile = [
                'name'     => $_FILES['upfile']['name'][0],
                'type'     => $_FILES['upfile']['type'][0],
                'tmp_name' => $_FILES['upfile']['tmp_name'][0],
                'error'    => $_FILES['upfile']['error'][0],
                'size'     => $_FILES['upfile']['size'][0],
                'title'    => html::escapeHTML($_FILES['upfile']['name'][0]),
            ];

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-type: application/json');
                $message = [];

                try {
                    files::uploadStatus($upfile);
                    $new_file_id = dcCore::app()->media->uploadFile($upfile['tmp_name'], $upfile['name'], false, $upfile['title']);

                    $message['files'][] = [
                        'name' => $upfile['name'],
                        'size' => $upfile['size'],
                        'html' => dcCore::app()->admin->page->mediaLine($new_file_id),
                    ];
                } catch (Exception $e) {
                    $message['files'][] = [
                        'name'  => $upfile['name'],
                        'size'  => $upfile['size'],
                        'error' => $e->getMessage(),
                    ];
                }
                echo json_encode($message, JSON_THROW_ON_ERROR);
                exit();
            }

            try {
                files::uploadStatus($upfile);

                $f_title   = (isset($_POST['upfiletitle']) ? html::escapeHTML($_POST['upfiletitle']) : '');
                $f_private = ($_POST['upfilepriv'] ?? false);

                dcCore::app()->media->uploadFile($upfile['tmp_name'], $upfile['name'], false, $f_title, $f_private);

                dcPage::addSuccessNotice(__('Files have been successfully uploaded.'));
                dcCore::app()->adminurl->redirect('admin.media', dcCore::app()->admin->page->values());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        # Removing items
        if (dcCore::app()->admin->page->getDirs() && !empty($_POST['medias']) && !empty($_POST['delete_medias'])) {
            try {
                foreach ($_POST['medias'] as $media) {
                    dcCore::app()->media->removeItem(rawurldecode($media));
                }
                dcPage::addSuccessNotice(
                    sprintf(
                        __(
                            'Successfully delete one media.',
                            'Successfully delete %d medias.',
                            is_countable($_POST['medias']) ? count($_POST['medias']) : 0
                        ),
                        is_countable($_POST['medias']) ? count($_POST['medias']) : 0
                    )
                );
                dcCore::app()->adminurl->redirect('admin.media', dcCore::app()->admin->page->values());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        # Removing item from popup only
        if (dcCore::app()->admin->page->getDirs() && !empty($_POST['rmyes']) && !empty($_POST['remove'])) {
            $_POST['remove'] = rawurldecode($_POST['remove']);
            $forget          = false;

            try {
                if (is_dir(path::real(dcCore::app()->media->getPwd() . '/' . path::clean($_POST['remove'])))) {
                    $msg = __('Directory has been successfully removed.');
                    # Remove dir from recents/favs if necessary
                    $forget = true;
                } else {
                    $msg = __('File has been successfully removed.');
                }
                dcCore::app()->media->removeItem($_POST['remove']);
                if ($forget) {
                    dcCore::app()->admin->page->updateLast(dcCore::app()->admin->page->d . '/' . path::clean($_POST['remove']), true);
                    dcCore::app()->admin->page->updateFav(dcCore::app()->admin->page->d . '/' . path::clean($_POST['remove']), true);
                }
                dcPage::addSuccessNotice($msg);
                dcCore::app()->adminurl->redirect('admin.media', dcCore::app()->admin->page->values());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        # Build missing directory thumbnails
        if (dcCore::app()->admin->page->getDirs() && dcCore::app()->auth->isSuperAdmin() && !empty($_POST['complete'])) {
            try {
                dcCore::app()->media->rebuildThumbnails(dcCore::app()->admin->page->d);

                dcPage::addSuccessNotice(
                    sprintf(
                        __('Directory "%s" has been successfully completed.'),
                        html::escapeHTML(dcCore::app()->admin->page->d)
                    )
                );
                dcCore::app()->adminurl->redirect('admin.media', dcCore::app()->admin->page->values());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        # DISPLAY confirm page for rmdir & rmfile
        if (dcCore::app()->admin->page->getDirs() && !empty($_GET['remove']) && empty($_GET['noconfirm'])) {
            dcCore::app()->admin->page->openPage(dcCore::app()->admin->page->breadcrumb([__('confirm removal') => '']));

            echo
            '<form action="' . html::escapeURL(dcCore::app()->adminurl->get('admin.media')) . '" method="post">' .
            '<p>' . sprintf(
                __('Are you sure you want to remove %s?'),
                html::escapeHTML($_GET['remove'])
            ) . '</p>' .
            '<p><input type="submit" value="' . __('Cancel') . '" /> ' .
            ' &nbsp; <input type="submit" name="rmyes" value="' . __('Yes') . '" />' .
            dcCore::app()->adminurl->getHiddenFormFields('admin.media', dcCore::app()->admin->page->values()) .
            dcCore::app()->formNonce() .
            form::hidden('remove', html::escapeHTML($_GET['remove'])) . '</p>' .
            '</form>';

            dcCore::app()->admin->page->closePage();
            exit;
        }
    }

    /**
     * Renders the page.
     */
    public static function render()
    {
        // Recent media folders
        $last_folders = '';
        if (dcCore::app()->admin->page->showLast()) {
            $last_folders_item = '';
            $fav_url           = '';
            $fav_img           = '';
            $fav_alt           = '';
            // Favorites directories
            $fav_dirs = dcCore::app()->admin->page->getFav();
            foreach ($fav_dirs as $ld) {
                // Add favorites dirs on top of combo
                $ld_params      = dcCore::app()->admin->page->values();
                $ld_params['d'] = $ld;
                $ld_params['q'] = ''; // Reset search
                $last_folders_item .= '<option value="' . urldecode(dcCore::app()->adminurl->get('admin.media', $ld_params)) . '"' .
            ($ld == rtrim((string) dcCore::app()->admin->page->d, '/') ? ' selected="selected"' : '') . '>' .
            '/' . $ld . '</option>' . "\n";
                if ($ld == rtrim((string) dcCore::app()->admin->page->d, '/')) {
                    // Current directory is a favorite → button will un-fav
                    $ld_params['fav'] = 'n';
                    $fav_url          = urldecode(dcCore::app()->adminurl->get('admin.media', $ld_params));
                    unset($ld_params['fav']);
                    $fav_img = 'images/fav-on.png';
                    $fav_alt = __('Remove this folder from your favorites');
                }
            }
            if ($last_folders_item != '') {
                // add a separator between favorite dirs and recent dirs
                $last_folders_item .= '<option disabled>_________</option>';
            }
            // Recent directories
            $last_dirs = dcCore::app()->admin->page->getlast();
            foreach ($last_dirs as $ld) {
                if (!in_array($ld, $fav_dirs)) {
                    $ld_params      = dcCore::app()->admin->page->values();
                    $ld_params['d'] = $ld;
                    $ld_params['q'] = ''; // Reset search
                    $last_folders_item .= '<option value="' . urldecode(dcCore::app()->adminurl->get('admin.media', $ld_params)) . '"' .
                ($ld == rtrim((string) dcCore::app()->admin->page->d, '/') ? ' selected="selected"' : '') . '>' .
                '/' . $ld . '</option>' . "\n";
                    if ($ld == rtrim((string) dcCore::app()->admin->page->d, '/')) {
                        // Current directory is not a favorite → button will fav
                        $ld_params['fav'] = 'y';
                        $fav_url          = urldecode(dcCore::app()->adminurl->get('admin.media', $ld_params));
                        unset($ld_params['fav']);
                        $fav_img = 'images/fav-off.png';
                        $fav_alt = __('Add this folder to your favorites');
                    }
                }
            }
            if ($last_folders_item != '') {
                $last_folders = '<p class="media-recent hidden-if-no-js">' .
                    '<label class="classic" for="switchfolder">' . __('Goto recent folder:') . '</label> ' .
                    '<select name="switchfolder" id="switchfolder">' .
                    $last_folders_item .
                    '</select>' .
                    ' <a id="media-fav-dir" href="' . $fav_url . '" title="' . $fav_alt . '"><img src="' . $fav_img . '" alt="' . $fav_alt . '" /></a>' .
                    '</p>';
            }
        }

        $starting_scripts = '';
        if (dcCore::app()->admin->page->popup && (dcCore::app()->admin->page->plugin_id !== '')) {
            $starting_scripts .= dcCore::app()->callBehavior('adminPopupMediaManager', dcCore::app()->admin->page->plugin_id);
        }

        dcCore::app()->admin->page->openPage(
            dcCore::app()->admin->page->breadcrumb(),
            dcPage::jsModal() .
            dcCore::app()->admin->page->js(dcCore::app()->adminurl->get('admin.media', array_diff_key(dcCore::app()->admin->page->values(), dcCore::app()->admin->page->values(false, true)), '&')) .
            dcPage::jsLoad('js/_media.js') .
            $starting_scripts .
            (dcCore::app()->admin->page->mediaWritable() ? dcPage::jsUpload(['d=' . dcCore::app()->admin->page->d]) : '')
        );

        if (dcCore::app()->admin->page->popup) {
            echo
            dcPage::notices();
        }

        if (!dcCore::app()->admin->page->mediaWritable() && !dcCore::app()->error->flag()) {
            dcPage::warning(__('You do not have sufficient permissions to write to this folder.'));
        }

        if (!dcCore::app()->admin->page->getDirs()) {
            dcCore::app()->admin->page->closePage();
            exit;
        }

        if (dcCore::app()->admin->page->select) {
            // Select mode (popup or not)
            echo
            '<div class="' . (dcCore::app()->admin->page->popup ? 'form-note ' : '') . 'info"><p>';
            if (dcCore::app()->admin->page->select == 1) {
                echo
                sprintf(__('Select a file by clicking on %s'), '<img src="images/plus.png" alt="' . __('Select this file') . '" />');
            } else {
                echo
                sprintf(__('Select files and click on <strong>%s</strong> button'), __('Choose selected medias'));
            }
            if (dcCore::app()->admin->page->mediaWritable()) {
                echo
                ' ' . __('or') . ' ' . sprintf('<a href="#fileupload">%s</a>', __('upload a new file'));
            }
            echo '</p></div>';
        } else {
            if (dcCore::app()->admin->page->post_id) {
                echo
                '<div class="form-note info"><p>' . sprintf(
                    __('Choose a file to attach to entry %s by clicking on %s'),
                    '<a href="' . dcCore::app()->getPostAdminURL(dcCore::app()->admin->page->getPostType(), dcCore::app()->admin->page->post_id) . '">' . html::escapeHTML(dcCore::app()->admin->page->getPostTitle()) . '</a>',
                    '<img src="images/plus.png" alt="' . __('Attach this file to entry') . '" />'
                );
                if (dcCore::app()->admin->page->mediaWritable()) {
                    echo
                    ' ' . __('or') . ' ' . sprintf('<a href="#fileupload">%s</a>', __('upload a new file'));
                }
                echo
                '</p></div>';
            }
            if (dcCore::app()->admin->page->popup) {
                echo
                '<div class="info"><p>' . sprintf(
                    __('Choose a file to insert into entry by clicking on %s'),
                    '<img src="images/plus.png" alt="' . __('Attach this file to entry') . '" />'
                );
                if (dcCore::app()->admin->page->mediaWritable()) {
                    echo
                    ' ' . __('or') . ' ' . sprintf('<a href="#fileupload">%s</a>', __('upload a new file'));
                }
                echo
                '</p></div>';
            }
        }

        $rs         = dcCore::app()->admin->page->getDirsRecord();
        $media_list = new adminMediaList($rs, $rs->count());

        // add file mode into the filter box
        dcCore::app()->admin->page->add((new dcAdminFilter('file_mode'))->value(dcCore::app()->admin->page->file_mode)->html(
            '<p><span class="media-file-mode">' .
            '<a href="' . dcCore::app()->adminurl->get('admin.media', array_merge(dcCore::app()->admin->page->values(), ['file_mode' => 'grid'])) . '" title="' . __('Grid display mode') . '">' .
            '<img src="images/grid-' . (dcCore::app()->admin->page->file_mode == 'grid' ? 'on' : 'off') . '.png" alt="' . __('Grid display mode') . '" />' .
            '</a>' .
            '<a href="' . dcCore::app()->adminurl->get('admin.media', array_merge(dcCore::app()->admin->page->values(), ['file_mode' => 'list'])) . '" title="' . __('List display mode') . '">' .
            '<img src="images/list-' . (dcCore::app()->admin->page->file_mode == 'list' ? 'on' : 'off') . '.png" alt="' . __('List display mode') . '" />' .
            '</a>' .
            '</span></p>',
            false
        ));

        $fmt_form_media = '<form action="' . dcCore::app()->adminurl->get('admin.media') . '" method="post" id="form-medias">' .
            '<div class="files-group">%s</div>' .
            '<p class="hidden">' .
            dcCore::app()->formNonce() .
            dcCore::app()->adminurl->getHiddenFormFields('admin.media', dcCore::app()->admin->page->values()) .
            '</p>';

        if (!dcCore::app()->admin->page->popup || dcCore::app()->admin->page->select > 1) {
            // Checkboxes and action
            $fmt_form_media .= '<div class="' . (!dcCore::app()->admin->page->popup ? 'medias-delete' : '') . ' ' . (dcCore::app()->admin->page->select > 1 ? 'medias-select' : '') . '">' .
                '<p class="checkboxes-helpers"></p>' .
                '<p>';
            if (dcCore::app()->admin->page->select > 1) {
                $fmt_form_media .= '<input type="submit" class="select" id="select_medias" name="select_medias" value="' . __('Choose selected medias') . '"/> ';
            }
            if (!dcCore::app()->admin->page->popup) {
                $fmt_form_media .= '<input type="submit" class="delete" id="delete_medias" name="delete_medias" value="' . __('Remove selected medias') . '"/>';
            }
            $fmt_form_media .= '</p></div>';
        }
        $fmt_form_media .= '</form>';

        echo
        '<div class="media-list">' . $last_folders;

        // remove form filters from hidden fields
        $form_filters_hidden_fields = array_diff_key(dcCore::app()->admin->page->values(), ['nb' => '', 'order' => '', 'sortby' => '', 'q' => '']);

        // display filter
        dcCore::app()->admin->page->display('admin.media', dcCore::app()->adminurl->getHiddenFormFields('admin.media', $form_filters_hidden_fields));

        // display list
        $media_list->display(dcCore::app()->admin->page, $fmt_form_media, dcCore::app()->admin->page->hasQuery());

        echo
        '</div>';

        if ((!dcCore::app()->admin->page->hasQuery()) && (dcCore::app()->admin->page->mediaWritable() || dcCore::app()->admin->page->mediaArchivable())) {
            echo
            '<div class="vertical-separator">' .
            '<h3 class="out-of-screen-if-js">' . sprintf(__('In %s:'), (dcCore::app()->admin->page->d == '' ? '“' . __('Media manager') . '”' : '“' . dcCore::app()->admin->page->d . '”')) . '</h3>';
        }

        if ((!dcCore::app()->admin->page->hasQuery()) && (dcCore::app()->admin->page->mediaWritable() || dcCore::app()->admin->page->mediaArchivable())) {
            echo
            '<div class="two-boxes odd">';

            // Create directory
            if (dcCore::app()->admin->page->mediaWritable()) {
                echo
                '<form action="' . dcCore::app()->adminurl->getBase('admin.media') . '" method="post" class="fieldset">' .
                '<div id="new-dir-f">' .
                '<h4 class="pretty-title">' . __('Create new directory') . '</h4>' .
                dcCore::app()->formNonce() .
                '<p><label for="newdir">' . __('Directory Name:') . '</label>' .
                form::field('newdir', 35, 255) . '</p>' .
                '<p><input type="submit" value="' . __('Create') . '" />' .
                dcCore::app()->adminurl->getHiddenFormFields('admin.media', dcCore::app()->admin->page->values()) .
                '</p>' .
                '</div>' .
                '</form>';
            }

            // Rebuild directory
            if (dcCore::app()->auth->isSuperAdmin() && !dcCore::app()->admin->page->popup && dcCore::app()->admin->page->mediaWritable()) {
                echo
                '<form action="' . dcCore::app()->adminurl->getBase('admin.media') . '" method="post" class="fieldset">' .
                '<h4 class="pretty-title">' . __('Build missing thumbnails in directory') . '</h4>' .
                dcCore::app()->formNonce() .
                '<p><input type="submit" value="' . __('Build') . '" />' .
                dcCore::app()->adminurl->getHiddenFormFields('admin.media', array_merge(dcCore::app()->admin->page->values(), ['complete' => 1])) .
                '</p>' .
                '</form>';
            }

            // Get zip directory
            if (dcCore::app()->admin->page->mediaArchivable() && !dcCore::app()->admin->page->popup) {
                echo
                '<div class="fieldset">' .
                '<h4 class="pretty-title">' . sprintf(__('Backup content of %s'), (dcCore::app()->admin->page->d == '' ? '“' . __('Media manager') . '”' : '“' . dcCore::app()->admin->page->d . '”')) . '</h4>' .
                '<p><a class="button submit" href="' . dcCore::app()->adminurl->get(
                    'admin.media',
                    array_merge(dcCore::app()->admin->page->values(), ['zipdl' => 1])
                ) . '">' . __('Download zip file') . '</a></p>' .
                '</div>';
            }

            echo
            '</div>';
        }

        if (!dcCore::app()->admin->page->hasQuery() && dcCore::app()->admin->page->mediaWritable()) {
            echo
            '<div class="two-boxes fieldset even">';
            if (dcCore::app()->admin->page->showUploader()) {
                echo
                '<div class="enhanced_uploader">';
            } else {
                echo
                '<div>';
            }

            echo
            '<h4>' . __('Add files') . '</h4>' .
            '<p class="more-info">' . __('Please take care to publish media that you own and that are not protected by copyright.') . '</p>' .
            '<form id="fileupload" action="' . html::escapeURL(dcCore::app()->adminurl->get('admin.media', dcCore::app()->admin->page->values())) . '" method="post" enctype="multipart/form-data" aria-disabled="false">' .
            '<p>' . form::hidden(['MAX_FILE_SIZE'], DC_MAX_UPLOAD_SIZE) .
            dcCore::app()->formNonce() . '</p>' .
                '<div class="fileupload-ctrl"><p class="queue-message"></p><ul class="files"></ul></div>' .

            '<div class="fileupload-buttonbar clear">' .

            '<p><label for="upfile">' . '<span class="add-label one-file">' . __('Choose file') . '</span>' . '</label>' .
            '<button class="button choose_files">' . __('Choose files') . '</button>' .
            '<input type="file" id="upfile" name="upfile[]"' . (dcCore::app()->admin->page->showUploader() ? ' multiple="mutiple"' : '') . ' data-url="' . html::escapeURL(dcCore::app()->adminurl->get('admin.media', dcCore::app()->admin->page->values())) . '" /></p>' .

            '<p class="max-sizer form-note">&nbsp;' . __('Maximum file size allowed:') . ' ' . files::size(DC_MAX_UPLOAD_SIZE) . '</p>' .

            '<p class="one-file"><label for="upfiletitle">' . __('Title:') . '</label>' . form::field('upfiletitle', 35, 255) . '</p>' .
            '<p class="one-file"><label for="upfilepriv" class="classic">' . __('Private') . '</label> ' .
            form::checkbox('upfilepriv', 1) . '</p>';

            if (!dcCore::app()->admin->page->showUploader()) {
                echo
                '<p class="one-file form-help info">' . __('To send several files at the same time, you can activate the enhanced uploader in') .
                ' <a href="' . dcCore::app()->adminurl->get('admin.user.preferences', ['tab' => 'user-options']) . '">' . __('My preferences') . '</a></p>';
            }

            echo
            '<p class="clear"><button class="button clean">' . __('Refresh') . '</button>' .
            '<input class="button cancel one-file" type="reset" value="' . __('Clear all') . '"/>' .
            '<input class="button start" type="submit" value="' . __('Upload') . '"/></p>' .
            '</div>';

            echo
            '<p style="clear:both;">' .
            dcCore::app()->adminurl->getHiddenFormFields('admin.media', dcCore::app()->admin->page->values()) .
            '</p>' .
            '</form>' .
            '</div>' .
            '</div>';
        }

        # Empty remove form (for javascript actions)
        echo
        '<form id="media-remove-hide" action="' . html::escapeURL(dcCore::app()->adminurl->get('admin.media', dcCore::app()->admin->page->values())) . '" method="post" class="hidden">' .
        '<div>' .
        form::hidden('rmyes', 1) .
        dcCore::app()->adminurl->getHiddenFormFields('admin.media', dcCore::app()->admin->page->values()) .
        form::hidden('remove', '') .
        dcCore::app()->formNonce() .
        '</div>' .
        '</form>';

        if ((!dcCore::app()->admin->page->hasQuery()) && (dcCore::app()->admin->page->mediaWritable() || dcCore::app()->admin->page->mediaArchivable())) {
            echo
            '</div>';
        }

        if (!dcCore::app()->admin->page->popup) {
            echo
            '<p class="info">' . sprintf(
                __('Current settings for medias and images are defined in %s'),
                '<a href="' . dcCore::app()->adminurl->get('admin.blog.pref') . '#medias-settings">' . __('Blog parameters') . '</a>'
            ) . '</p>';

            // Go back button
            echo
            '<p><input type="button" value="' . __('Cancel') . '" class="go-back reset hidden-if-no-js" /></p>';
        }

        dcCore::app()->admin->page->closePage();
    }
}

/**
 * @brief class for admin media page
 */
#[\AllowDynamicProperties]
class adminMediaPage extends adminMediaFilter
{
    /** @var boolean Page has a valid query */
    protected $media_has_query = false;

    /** @var boolean Media dir is writable */
    protected $media_writable = false;

    /** @var boolean Media dir is archivable */
    protected $media_archivable = null;

    /** @var array Dirs and files fileItem objects */
    protected $media_dir = null;

    /** @var array User media recents */
    protected $media_last = null;

    /** @var array User media favorites */
    protected $media_fav = null;

    /** @var boolean Uses enhance uploader */
    protected $media_uploader = null;

    /**
     * Constructs a new instance.
     */
    public function __construct()
    {
        parent::__construct('media');

        $this->media_uploader = dcCore::app()->auth->user_prefs->interface->enhanceduploader;

        // try to load core media and themes
        try {
            dcCore::app()->media = new dcMedia();
            dcCore::app()->media->setFileSort($this->sortby . '-' . $this->order);

            if ($this->q != '') {
                $this->media_has_query = dcCore::app()->media->searchMedia($this->q);
            }
            if (!$this->media_has_query) {
                $try_d = $this->d;
                // Reset current dir
                $this->d = null;
                // Change directory (may cause an exception if directory doesn't exist)
                dcCore::app()->media->chdir($try_d);
                // Restore current dir variable
                $this->d = $try_d;
                dcCore::app()->media->getDir();
            } else {
                $this->d = null;
                dcCore::app()->media->chdir('');
            }
            $this->media_writable = dcCore::app()->media->writable();
            $this->media_dir      = &dcCore::app()->media->dir;

            if (dcCore::app()->themes === null) {
                # -- Loading themes, may be useful for some configurable theme --
                dcCore::app()->themes = new dcThemes();
                dcCore::app()->themes->loadModules(dcCore::app()->blog->themes_path, null);
            }
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }

    /**
     * Check if page has a valid query
     *
     * @return boolean Has query
     */
    public function hasQuery(): bool
    {
        return $this->media_has_query;
    }

    /**
     * Check if media dir is writable
     *
     * @return boolean Is writable
     */
    public function mediaWritable(): bool
    {
        return $this->media_writable;
    }

    /**
     * Check if media dir is archivable
     *
     * @return boolean Is archivable
     */
    public function mediaArchivable(): bool
    {
        if ($this->media_archivable === null) {
            $rs = $this->getDirsRecord();

            $this->media_archivable = dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
                dcAuth::PERMISSION_MEDIA_ADMIN,
            ]), dcCore::app()->blog->id)
                && !((is_countable($rs) ? count($rs) : 0) === 0 || ((is_countable($rs) ? count($rs) : 0) === 1 && $rs->parent)); // @phpstan-ignore-line
        }

        return $this->media_archivable;
    }

    /**
     * Return list of fileItem objects of current dir
     *
     * @param string $type  dir, file, all type
     *
     * @return array Dirs and/or files fileItem objects
     */
    public function getDirs(string $type = ''): array
    {
        if (!empty($type)) {
            return $this->media_dir[$type] ?? null;
        }

        return $this->media_dir;
    }

    /**
     * Return dcRecord instance of fileItem objects
     *
     * @return dcRecord Dirs and/or files fileItem objects
     */
    public function getDirsRecord(): dcRecord
    {
        $dir = $this->media_dir;
        // Remove hidden directories (unless DC_SHOW_HIDDEN_DIRS is set to true)
        if (!defined('DC_SHOW_HIDDEN_DIRS') || (!DC_SHOW_HIDDEN_DIRS)) {
            for ($i = (is_countable($dir['dirs']) ? count($dir['dirs']) : 0) - 1; $i >= 0; $i--) {
                if ($dir['dirs'][$i]->d && strpos($dir['dirs'][$i]->basename, '.') === 0) {
                    unset($dir['dirs'][$i]);
                }
            }
        }
        $items = array_values(array_merge($dir['dirs'], $dir['files']));

        // Transform each fileItem array value to associative array if necessary
        $items = array_map(fn ($v) => $v instanceof fileItem ? (array) $v : $v, $items);

        return dcRecord::newFromArray($items);
    }

    /**
     * Return HTML code of an element of list or grid items list
     *
     * @param string $file_id  The file id
     *
     * @return string The element
     */
    public function mediaLine(string $file_id): string
    {
        return adminMediaList::mediaLine($this, dcCore::app()->media->getFile((int) $file_id), 1, $this->media_has_query);
    }

    /**
     * Show enhance uploader
     *
     * @return boolean Show enhance uploader
     */
    public function showUploader(): bool
    {
        return $this->media_uploader;
    }

    /**
     * Number of recent/fav dirs to show
     *
     * @return integer Nb of dirs
     */
    public function showLast(): int
    {
        return abs((int) dcCore::app()->auth->user_prefs->interface->media_nb_last_dirs);
    }

    /**
     * Return list of last dirs
     *
     * @return array Last dirs
     */
    public function getLast(): array
    {
        if ($this->media_last === null) {
            $m = dcCore::app()->auth->user_prefs->interface->media_last_dirs;
            if (!is_array($m)) {
                $m = [];
            }
            $this->media_last = $m;
        }

        return $this->media_last;
    }

    /**
     * Update user last dirs
     *
     * @param string    $dir        The directory
     * @param boolean   $remove     Remove
     *
     * @return boolean The change
     */
    public function updateLast(string $dir, bool $remove = false): bool
    {
        if ($this->q) {
            return false;
        }

        $nb_last_dirs = $this->showLast();
        if (!$nb_last_dirs) {
            return false;
        }

        $done      = false;
        $last_dirs = $this->getLast();

        if ($remove) {
            if (in_array($dir, $last_dirs)) {
                unset($last_dirs[array_search($dir, $last_dirs)]);
                $done = true;
            }
        } else {
            if (!in_array($dir, $last_dirs)) {
                // Add new dir at the top of the list
                array_unshift($last_dirs, $dir);
                // Remove oldest dir(s)
                while (count($last_dirs) > $nb_last_dirs) {
                    array_pop($last_dirs);
                }
                $done = true;
            } else {
                // Move current dir at the top of list
                unset($last_dirs[array_search($dir, $last_dirs)]);
                array_unshift($last_dirs, $dir);
                $done = true;
            }
        }

        if ($done) {
            $this->media_last = $last_dirs;
            dcCore::app()->auth->user_prefs->interface->put('media_last_dirs', $last_dirs, 'array');
        }

        return $done;
    }

    /**
     * Return list of fav dirs
     *
     * @return array Fav dirs
     */
    public function getFav(): array
    {
        if ($this->media_fav === null) {
            $m = dcCore::app()->auth->user_prefs->interface->media_fav_dirs;
            if (!is_array($m)) {
                $m = [];
            }
            $this->media_fav = $m;
        }

        return $this->media_fav;
    }

    /**
     * Update user fav dirs
     *
     * @param string    $dir        The directory
     * @param boolean   $remove     Remove
     *
     * @return boolean The change
     */
    public function updateFav(string $dir, bool $remove = false): bool
    {
        if ($this->q) {
            return false;
        }

        $nb_last_dirs = $this->showLast();
        if (!$nb_last_dirs) {
            return false;
        }

        $done     = false;
        $fav_dirs = $this->getFav();
        if (!in_array($dir, $fav_dirs) && !$remove) {
            array_unshift($fav_dirs, $dir);
            $done = true;
        } elseif (in_array($dir, $fav_dirs) && $remove) {
            unset($fav_dirs[array_search($dir, $fav_dirs)]);
            $done = true;
        }

        if ($done) {
            $this->media_fav = $fav_dirs;
            dcCore::app()->auth->user_prefs->interface->put('media_fav_dirs', $fav_dirs, 'array');
        }

        return $done;
    }

    /**
     * The top of media page or popup
     *
     * @param string $breadcrumb    The breadcrumb
     * @param string $header        The headers
     */
    public function openPage(string $breadcrumb, string $header = ''): void
    {
        if ($this->popup) {
            dcPage::openPopup(__('Media manager'), $header, $breadcrumb);
        } else {
            dcPage::open(__('Media manager'), $header, $breadcrumb);
        }
    }

    /**
     * The end of media page or popup
     */
    public function closePage(): void
    {
        if ($this->popup) {
            dcPage::closePopup();
        } else {
            dcPage::helpBlock('core_media');
            dcPage::close();
        }
    }

    /**
     * The breadcrumb of media page or popup
     *
     * @param array $element  The additionnal element
     *
     * @return string The HTML code of breadcrumb
     */
    public function breadcrumb(array $element = []): string
    {
        $option = $param = [];

        if (empty($element) && isset(dcCore::app()->media)) {
            $param = [
                'd' => '',
                'q' => '',
            ];

            if ($this->media_has_query || $this->q) {
                $count = $this->media_has_query ? count($this->getDirs('files')) : 0;

                $element[__('Search:') . ' ' . $this->q . ' (' . sprintf(__('%s file found', '%s files found', $count), $count) . ')'] = '';
            } else {
                $bc_url   = dcCore::app()->adminurl->get('admin.media', array_merge($this->values(), ['d' => '%s']), '&amp;', true);
                $bc_media = dcCore::app()->media->breadCrumb($bc_url, '<span class="page-title">%s</span>');
                if ($bc_media != '') {
                    $element[$bc_media] = '';
                    $option['hl']       = true;
                }
            }
        }

        $elements = [
            html::escapeHTML(dcCore::app()->blog->name) => '',
            __('Media manager')                         => empty($param) ? '' :
                dcCore::app()->adminurl->get('admin.media', array_merge($this->values(), array_merge($this->values(), $param))),
        ];
        $options = [
            'home_link' => !$this->popup,
        ];

        return dcPage::breadcrumb(array_merge($elements, $element), array_merge($options, $option));
    }
}

adminMedia::init();
adminMedia::process();
adminMedia::render();
