<?php
/**
 * @package Dotclear
 * @subpackage Public
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class dcTemplate extends template
{
    /**
     * Current tag
     */
    private ?string $current_tag = null;

    /**
     * Constructs a new instance.
     *
     * @param      string  $cache_dir  The cache dir
     * @param      string  $self_name  The self name (used in compiled template code)
     */
    public function __construct(string $cache_dir, string $self_name)
    {
        parent::__construct($cache_dir, $self_name);

        $this->remove_php = !dcCore::app()->blog->settings->system->tpl_allow_php;
        $this->use_cache  = dcCore::app()->blog->settings->system->tpl_use_cache;

        // Transitional tags
        $this->addValue('EntryTrackbackCount', [$this, 'EntryPingCount']);
        $this->addValue('EntryTrackbackData', [$this, 'EntryPingData']);
        $this->addValue('EntryTrackbackLink', [$this, 'EntryPingLink']);

        // l10n
        $this->addValue('lang', [$this, 'l10n']);

        // Loops test tags
        $this->addBlock('LoopPosition', [$this, 'LoopPosition']);
        $this->addValue('LoopIndex', [$this, 'LoopIndex']);

        // Archives
        $this->addBlock('Archives', [$this, 'Archives']);
        $this->addBlock('ArchivesHeader', [$this, 'ArchivesHeader']);
        $this->addBlock('ArchivesFooter', [$this, 'ArchivesFooter']);
        $this->addBlock('ArchivesYearHeader', [$this, 'ArchivesYearHeader']);
        $this->addBlock('ArchivesYearFooter', [$this, 'ArchivesYearFooter']);
        $this->addValue('ArchiveDate', [$this, 'ArchiveDate']);
        $this->addBlock('ArchiveNext', [$this, 'ArchiveNext']);
        $this->addBlock('ArchivePrevious', [$this, 'ArchivePrevious']);
        $this->addValue('ArchiveEntriesCount', [$this, 'ArchiveEntriesCount']);
        $this->addValue('ArchiveURL', [$this, 'ArchiveURL']);

        // Blog
        $this->addValue('BlogArchiveURL', [$this, 'BlogArchiveURL']);
        $this->addValue('BlogCopyrightNotice', [$this, 'BlogCopyrightNotice']);
        $this->addValue('BlogDescription', [$this, 'BlogDescription']);
        $this->addValue('BlogEditor', [$this, 'BlogEditor']);
        $this->addValue('BlogFeedID', [$this, 'BlogFeedID']);
        $this->addValue('BlogFeedURL', [$this, 'BlogFeedURL']);
        $this->addValue('BlogRSDURL', [$this, 'BlogRSDURL']);
        $this->addValue('BlogName', [$this, 'BlogName']);
        $this->addValue('BlogLanguage', [$this, 'BlogLanguage']);
        $this->addValue('BlogLanguageURL', [$this, 'BlogLanguageURL']);
        $this->addValue('BlogThemeURL', [$this, 'BlogThemeURL']);
        $this->addValue('BlogParentThemeURL', [$this, 'BlogParentThemeURL']);
        $this->addValue('BlogUpdateDate', [$this, 'BlogUpdateDate']);
        $this->addValue('BlogID', [$this, 'BlogID']);
        $this->addValue('BlogURL', [$this, 'BlogURL']);
        $this->addValue('BlogXMLRPCURL', [$this, 'BlogXMLRPCURL']);
        $this->addValue('BlogPublicURL', [$this, 'BlogPublicURL']);
        $this->addValue('BlogQmarkURL', [$this, 'BlogQmarkURL']);
        $this->addValue('BlogMetaRobots', [$this, 'BlogMetaRobots']);
        $this->addValue('BlogJsJQuery', [$this, 'BlogJsJQuery']);
        $this->addValue('BlogPostsURL', [$this, 'BlogPostsURL']);
        $this->addBlock('IfBlogStaticEntryURL', [$this, 'IfBlogStaticEntryURL']);
        $this->addValue('BlogStaticEntryURL', [$this, 'BlogStaticEntryURL']);
        $this->addValue('BlogNbEntriesFirstPage', [$this, 'BlogNbEntriesFirstPage']);
        $this->addValue('BlogNbEntriesPerPage', [$this, 'BlogNbEntriesPerPage']);

        // Categories
        $this->addBlock('Categories', [$this, 'Categories']);
        $this->addBlock('CategoriesHeader', [$this, 'CategoriesHeader']);
        $this->addBlock('CategoriesFooter', [$this, 'CategoriesFooter']);
        $this->addBlock('CategoryIf', [$this, 'CategoryIf']);
        $this->addBlock('CategoryFirstChildren', [$this, 'CategoryFirstChildren']);
        $this->addBlock('CategoryParents', [$this, 'CategoryParents']);
        $this->addValue('CategoryFeedURL', [$this, 'CategoryFeedURL']);
        $this->addValue('CategoryID', [$this, 'CategoryID']);
        $this->addValue('CategoryURL', [$this, 'CategoryURL']);
        $this->addValue('CategoryShortURL', [$this, 'CategoryShortURL']);
        $this->addValue('CategoryDescription', [$this, 'CategoryDescription']);
        $this->addValue('CategoryTitle', [$this, 'CategoryTitle']);
        $this->addValue('CategoryEntriesCount', [$this, 'CategoryEntriesCount']);

        // Comments
        $this->addBlock('Comments', [$this, 'Comments']);
        $this->addValue('CommentAuthor', [$this, 'CommentAuthor']);
        $this->addValue('CommentAuthorDomain', [$this, 'CommentAuthorDomain']);
        $this->addValue('CommentAuthorLink', [$this, 'CommentAuthorLink']);
        $this->addValue('CommentAuthorMailMD5', [$this, 'CommentAuthorMailMD5']);
        $this->addValue('CommentAuthorURL', [$this, 'CommentAuthorURL']);
        $this->addValue('CommentContent', [$this, 'CommentContent']);
        $this->addValue('CommentDate', [$this, 'CommentDate']);
        $this->addValue('CommentTime', [$this, 'CommentTime']);
        $this->addValue('CommentEmail', [$this, 'CommentEmail']);
        $this->addValue('CommentEntryTitle', [$this, 'CommentEntryTitle']);
        $this->addValue('CommentFeedID', [$this, 'CommentFeedID']);
        $this->addValue('CommentID', [$this, 'CommentID']);
        $this->addBlock('CommentIf', [$this, 'CommentIf']);
        $this->addValue('CommentIfEven', [$this, 'CommentIfEven']);
        $this->addValue('CommentIfFirst', [$this, 'CommentIfFirst']);
        $this->addValue('CommentIfMe', [$this, 'CommentIfMe']);
        $this->addValue('CommentIfOdd', [$this, 'CommentIfOdd']);
        $this->addValue('CommentIP', [$this, 'CommentIP']);
        $this->addValue('CommentOrderNumber', [$this, 'CommentOrderNumber']);
        $this->addBlock('CommentsFooter', [$this, 'CommentsFooter']);
        $this->addBlock('CommentsHeader', [$this, 'CommentsHeader']);
        $this->addValue('CommentPostURL', [$this, 'CommentPostURL']);
        $this->addBlock('IfCommentAuthorEmail', [$this, 'IfCommentAuthorEmail']);
        $this->addValue('CommentHelp', [$this, 'CommentHelp']);

        // Comment preview
        $this->addBlock('IfCommentPreview', [$this, 'IfCommentPreview']);
        $this->addBlock('IfCommentPreviewOptional', [$this, 'IfCommentPreviewOptional']);
        $this->addValue('CommentPreviewName', [$this, 'CommentPreviewName']);
        $this->addValue('CommentPreviewEmail', [$this, 'CommentPreviewEmail']);
        $this->addValue('CommentPreviewSite', [$this, 'CommentPreviewSite']);
        $this->addValue('CommentPreviewContent', [$this, 'CommentPreviewContent']);
        $this->addValue('CommentPreviewCheckRemember', [$this, 'CommentPreviewCheckRemember']);

        // Entries
        $this->addBlock('DateFooter', [$this, 'DateFooter']);
        $this->addBlock('DateHeader', [$this, 'DateHeader']);
        $this->addBlock('Entries', [$this, 'Entries']);
        $this->addBlock('EntriesFooter', [$this, 'EntriesFooter']);
        $this->addBlock('EntriesHeader', [$this, 'EntriesHeader']);
        $this->addValue('EntryAuthorCommonName', [$this, 'EntryAuthorCommonName']);
        $this->addValue('EntryAuthorDisplayName', [$this, 'EntryAuthorDisplayName']);
        $this->addValue('EntryAuthorEmail', [$this, 'EntryAuthorEmail']);
        $this->addValue('EntryAuthorEmailMD5', [$this, 'EntryAuthorEmailMD5']);
        $this->addValue('EntryAuthorID', [$this, 'EntryAuthorID']);
        $this->addValue('EntryAuthorLink', [$this, 'EntryAuthorLink']);
        $this->addValue('EntryAuthorURL', [$this, 'EntryAuthorURL']);
        $this->addValue('EntryBasename', [$this, 'EntryBasename']);
        $this->addValue('EntryCategory', [$this, 'EntryCategory']);
        $this->addValue('EntryCategoryDescription', [$this, 'EntryCategoryDescription']);
        $this->addBlock('EntryCategoriesBreadcrumb', [$this, 'EntryCategoriesBreadcrumb']);
        $this->addValue('EntryCategoryID', [$this, 'EntryCategoryID']);
        $this->addValue('EntryCategoryURL', [$this, 'EntryCategoryURL']);
        $this->addValue('EntryCategoryShortURL', [$this, 'EntryCategoryShortURL']);
        $this->addValue('EntryCommentCount', [$this, 'EntryCommentCount']);
        $this->addValue('EntryContent', [$this, 'EntryContent']);
        $this->addValue('EntryDate', [$this, 'EntryDate']);
        $this->addValue('EntryExcerpt', [$this, 'EntryExcerpt']);
        $this->addValue('EntryFeedID', [$this, 'EntryFeedID']);
        $this->addValue('EntryFirstImage', [$this, 'EntryFirstImage']);
        $this->addValue('EntryID', [$this, 'EntryID']);
        $this->addBlock('EntryIf', [$this, 'EntryIf']);
        $this->addBlock('EntryIfContentCut', [$this, 'EntryIfContentCut']);
        $this->addValue('EntryIfEven', [$this, 'EntryIfEven']);
        $this->addValue('EntryIfFirst', [$this, 'EntryIfFirst']);
        $this->addValue('EntryIfOdd', [$this, 'EntryIfOdd']);
        $this->addValue('EntryIfSelected', [$this, 'EntryIfSelected']);
        $this->addValue('EntryLang', [$this, 'EntryLang']);
        $this->addBlock('EntryNext', [$this, 'EntryNext']);
        $this->addValue('EntryPingCount', [$this, 'EntryPingCount']);
        $this->addValue('EntryPingData', [$this, 'EntryPingData']);
        $this->addValue('EntryPingLink', [$this, 'EntryPingLink']);
        $this->addBlock('EntryPrevious', [$this, 'EntryPrevious']);
        $this->addValue('EntryTitle', [$this, 'EntryTitle']);
        $this->addValue('EntryTime', [$this, 'EntryTime']);
        $this->addValue('EntryURL', [$this, 'EntryURL']);

        // Languages
        $this->addBlock('Languages', [$this, 'Languages']);
        $this->addBlock('LanguagesHeader', [$this, 'LanguagesHeader']);
        $this->addBlock('LanguagesFooter', [$this, 'LanguagesFooter']);
        $this->addValue('LanguageCode', [$this, 'LanguageCode']);
        $this->addBlock('LanguageIfCurrent', [$this, 'LanguageIfCurrent']);
        $this->addValue('LanguageURL', [$this, 'LanguageURL']);
        $this->addValue('FeedLanguage', [$this, 'FeedLanguage']);

        // Pagination
        $this->addBlock('Pagination', [$this, 'Pagination']);
        $this->addValue('PaginationCounter', [$this, 'PaginationCounter']);
        $this->addValue('PaginationCurrent', [$this, 'PaginationCurrent']);
        $this->addBlock('PaginationIf', [$this, 'PaginationIf']);
        $this->addValue('PaginationURL', [$this, 'PaginationURL']);

        // Trackbacks
        $this->addValue('PingBlogName', [$this, 'PingBlogName']);
        $this->addValue('PingContent', [$this, 'PingContent']);
        $this->addValue('PingDate', [$this, 'PingDate']);
        $this->addValue('PingEntryTitle', [$this, 'PingEntryTitle']);
        $this->addValue('PingFeedID', [$this, 'PingFeedID']);
        $this->addValue('PingID', [$this, 'PingID']);
        $this->addValue('PingIfEven', [$this, 'PingIfEven']);
        $this->addValue('PingIfFirst', [$this, 'PingIfFirst']);
        $this->addValue('PingIfOdd', [$this, 'PingIfOdd']);
        $this->addValue('PingIP', [$this, 'PingIP']);
        $this->addValue('PingNoFollow', [$this, 'PingNoFollow']);
        $this->addValue('PingOrderNumber', [$this, 'PingOrderNumber']);
        $this->addValue('PingPostURL', [$this, 'PingPostURL']);
        $this->addBlock('Pings', [$this, 'Pings']);
        $this->addBlock('PingsFooter', [$this, 'PingsFooter']);
        $this->addBlock('PingsHeader', [$this, 'PingsHeader']);
        $this->addValue('PingTime', [$this, 'PingTime']);
        $this->addValue('PingTitle', [$this, 'PingTitle']);
        $this->addValue('PingAuthorURL', [$this, 'PingAuthorURL']);

        // System
        $this->addValue('SysBehavior', [$this, 'SysBehavior']);
        $this->addBlock('SysIf', [$this, 'SysIf']);
        $this->addBlock('SysIfCommentPublished', [$this, 'SysIfCommentPublished']);
        $this->addBlock('SysIfCommentPending', [$this, 'SysIfCommentPending']);
        $this->addBlock('SysIfFormError', [$this, 'SysIfFormError']);
        $this->addValue('SysFeedSubtitle', [$this, 'SysFeedSubtitle']);
        $this->addValue('SysFormError', [$this, 'SysFormError']);
        $this->addValue('SysPoweredBy', [$this, 'SysPoweredBy']);
        $this->addValue('SysSearchString', [$this, 'SysSearchString']);
        $this->addValue('SysSelfURI', [$this, 'SysSelfURI']);

        // Generic
        $this->addValue('else', [$this, 'GenericElse']);
    }

    /**
     * Gets the template file content.
     *
     * @param      string  $________  The template filename
     *
     * @return     string  The data.
     */
    public function getData(string $________): string
    {
        # --BEHAVIOR-- tplBeforeData
        if (dcCore::app()->hasBehavior('tplBeforeData') || dcCore::app()->hasBehavior('tplBeforeDataV2')) {
            self::$_r = dcCore::app()->callBehavior('tplBeforeDataV2');
            if (self::$_r) {
                return self::$_r;
            }
        }

        parent::getData($________);

        # --BEHAVIOR-- tplAfterData
        if (dcCore::app()->hasBehavior('tplAfterData') || dcCore::app()->hasBehavior('tplAfterDataV2')) {
            dcCore::app()->callBehavior('tplAfterDataV2', self::$_r);
        }

        return self::$_r;
    }

    /**
     * Compile block node
     *
     * @param      string               $tag      The tag
     * @param      array|ArrayObject    $attr     The attributes
     * @param      string               $content  The content
     *
     * @return     string
     */
    public function compileBlockNode(string $tag, $attr, string $content): string
    {
        $this->current_tag = $tag;
        $attr              = new ArrayObject($attr);

        # --BEHAVIOR-- templateBeforeBlock
        $res = dcCore::app()->callBehavior('templateBeforeBlockV2', $this->current_tag, $attr);

        # --BEHAVIOR-- templateInsideBlock
        dcCore::app()->callBehavior('templateInsideBlockV2', $this->current_tag, $attr, [& $content]);

        $res .= parent::compileBlockNode($this->current_tag, $attr, $content);

        # --BEHAVIOR-- templateAfterBlock
        $res .= dcCore::app()->callBehavior('templateAfterBlockV2', $this->current_tag, $attr);

        return $res;
    }

    /**
     * Compile value node
     *
     * @param      string               $tag       The tag
     * @param      array|ArrayObject    $attr      The attributes
     * @param      string               $str_attr  The attributes (one string form)
     *
     * @return     string
     */
    public function compileValueNode(string $tag, $attr, string $str_attr): string
    {
        $this->current_tag = $tag;
        $attr              = new ArrayObject($attr);

        # --BEHAVIOR-- templateBeforeValue
        $res = dcCore::app()->callBehavior('templateBeforeValueV2', $this->current_tag, $attr);

        $res .= parent::compileValueNode($this->current_tag, $attr, $str_attr);

        # --BEHAVIOR-- templateAfterValue
        $res .= dcCore::app()->callBehavior('templateAfterValueV2', $this->current_tag, $attr);

        return $res;
    }

    /**
     * Return the PHP code to filter a given value.
     *
     * @param      array|ArrayObject   $attr     The attributes
     * @param      array|ArrayObject   $default  The default filters
     *
     * @return     string  The filters.
     */
    public function getFilters($attr, $default = []): string
    {
        $params = array_merge(
            [
                0             => null,  // Will receive the string to filter
                'encode_xml'  => 0,
                'encode_html' => 0,
                'cut_string'  => 0,
                'lower_case'  => 0,
                'upper_case'  => 0,
                'encode_url'  => 0,
                'remove_html' => 0,
                'capitalize'  => 0,
                'strip_tags'  => 0,
            ],
            $default
        );

        foreach ($attr as $filter => $value) {
            // attributes names must follow this rule
            $filter = preg_filter('/\w/', '$0', $filter);
            if ($filter) {
                // addslashes protect var_export, str_replace protect sprintf;
                $params[$filter] = str_replace('%', '%%', addslashes($value));
            }
        }

        return 'context::global_filters(%s,' . var_export($params, true) . ",'" . addslashes($this->current_tag) . "')";
    }

    /**
     * Gets the operator.
     *
     * - "or" (in any case) and "||"" are aliases
     * - "and" (in any case) and "&&"" are aliases
     *
     * @param      string  $op     The operation
     *
     * @return     string  The operator.
     */
    public static function getOperator(string $op): string
    {
        switch (strtolower($op)) {
            case 'or':
            case '||':
                return '||';
            case 'and':
            case '&&':
            default:
                return '&&';
        }
    }

    /**
     * Gets the sort by field depending on given table.
     *
     * @param      ArrayObject     $attr      The attributes
     * @param      string          $table     The table
     *
     * @return     string
     */
    public function getSortByStr(ArrayObject $attr, ?string $table = null): string
    {
        $res = [];

        $default_order = 'desc';

        $default_alias = [
            'post'    => [
                'title'     => 'post_title',
                'selected'  => 'post_selected',
                'author'    => 'user_id',
                'date'      => 'post_dt',
                'id'        => 'post_id',
                'comment'   => 'nb_comment',
                'trackback' => 'nb_trackback',
            ],
            'comment' => [
                'author' => 'comment_author',
                'date'   => 'comment_dt',
                'id'     => 'comment_id',
            ],
        ];

        $alias = new ArrayObject();

        # --BEHAVIOR-- templateCustomSortByAlias
        dcCore::app()->callBehavior('templateCustomSortByAlias', $alias);

        $alias = $alias->getArrayCopy();

        if (is_array($alias)) {
            foreach ($alias as $k => $v) {  // @phpstan-ignore-line
                if (!is_array($v)) {
                    $alias[$k] = [];
                }
                if (!isset($default_alias[$k]) || !is_array($default_alias[$k])) {
                    $default_alias[$k] = [];
                }
                $default_alias[$k] = array_merge($default_alias[$k], $alias[$k]);
            }
        }

        if (!array_key_exists($table, $default_alias)) {
            return implode(', ', $res);
        }

        if (isset($attr['order']) && preg_match('/^(desc|asc)$/i', (string) $attr['order'])) {
            $default_order = (string) $attr['order'];
        }
        if (isset($attr['sortby'])) {
            $sorts = explode(',', $attr['sortby']);
            foreach ($sorts as $k => $sort) {
                $order = $default_order;
                if (preg_match('/([a-z]*)\s*\?(desc|asc)$/i', $sort, $matches)) {
                    $sort  = $matches[1];
                    $order = $matches[2];
                }
                if (array_key_exists($sort, $default_alias[$table])) {
                    array_push($res, $default_alias[$table][$sort] . ' ' . $order);
                }
            }
        }

        if (count($res) === 0) {
            array_push($res, $default_alias[$table]['date'] . ' ' . $default_order);
        }

        return implode(', ', $res);
    }

    /**
     * Gets the maximum date corresponding to a given age.
     *
     * @param      ArrayObject  $attr   The attributes
     *
     * @return     string  The age.
     */
    public static function getAge(ArrayObject $attr): string
    {
        if (isset($attr['age']) && preg_match('/^(\-\d+|last).*$/i', (string) $attr['age'])) {
            if (($ts = strtotime((string) $attr['age'])) !== false) {
                return dt::str('%Y-%m-%d %H:%m:%S', $ts);
            }
        }

        return '';
    }

    /**
     * Return PHP code to display a counter
     *
     * @param      string         $variable               The variable
     * @param      array          $values                 The values
     * @param      ArrayObject    $attr                   The attributes
     * @param      bool           $count_only_by_default  Display only counter value by default
     *
     * @return     string
     */
    public function displayCounter(string $variable, array $values, ArrayObject $attr, bool $count_only_by_default = false): string
    {
        if (isset($attr['count_only']) ? (bool) $attr['count_only'] : $count_only_by_default) {
            return '<?php echo ' . $variable . '; ?>';
        }

        $patterns = $values;
        /*
                if (isset($attr['none'])) {
                    $patterns['none'] = addslashes($attr['none']);
                }
                if (isset($attr['one'])) {
                    $patterns['one'] = addslashes($attr['one']);
                }
                if (isset($attr['more'])) {
                    $patterns['more'] = addslashes($attr['more']);
                }
        */
        array_walk($patterns, function (&$v, $k) use ($attr) {
            if (isset($attr[$k])) {
                $v = addslashes($attr[$k]);
            }
        });

        return
            '<?php if (' . $variable . " == 0) {\n" .
            "  printf(__('" . $patterns['none'] . "')," . $variable . ");\n" .
            '} elseif (' . $variable . " == 1) {\n" .
            "  printf(__('" . $patterns['one'] . "')," . $variable . ");\n" .
            "} else {\n" .
            "  printf(__('" . $patterns['more'] . "')," . $variable . ");\n" .
            '} ?>';
    }

    // TEMPLATE FUNCTIONS
    // -------------------------------------------------------

    /**
     * tpl:lang [string] : Localized string (tpl value)
     *
     * attributes:
     *
     *      - string      string to localized without quotes
     *
     * @param      ArrayObject    $attr      The attributes
     * @param      string         $str_attr  The attributes (one string form)
     *
     * @return     string
     */
    public function l10n(ArrayObject $attr, string $str_attr): string
    {
        # Normalize content
        $str_attr = preg_replace('/\s+/x', ' ', $str_attr);

        return "<?php echo __('" . str_replace("'", "\\'", $str_attr) . "'); ?>";
    }

    /**
     * tpl:LoopPosition [attributes] : Display content depending on current position (tpl block)
     *
     * attributes:
     *
     *      - start       int       Start (first = 1)
     *      - length      int       Length
     *      - even        (1|0)     Even / Odd
     *      - modulo      int       Modulo
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function LoopPosition(ArrayObject $attr, string $content): string
    {
        $start  = isset($attr['start']) ? (int) $attr['start'] : '0';
        $length = isset($attr['length']) ? (int) $attr['length'] : 'null';
        $even   = isset($attr['even']) ? (int) (bool) $attr['even'] : 'null';
        $modulo = isset($attr['modulo']) ? (int) $attr['modulo'] : 'null';

        if ($start > 0) {
            // PHP array is 0 based index
            $start--;
        }

        return
            '<?php if (dcCore::app()->ctx->loopPosition(' .
            (string) $start . ',' .
            (string) $length . ',' .
            (string) $even . ',' .
            (string) $modulo . ')) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:LoopPosition [attributes] : Display current loop index (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function LoopIndex(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), '(!dcCore::app()->ctx->cur_loop ? 0 : dcCore::app()->ctx->cur_loop->index() + 1)') . '; ?>';
    }

    // Archives
    // --------

    /**
     * tpl:Archives [attributes] : Archives dates loop (tpl block)
     *
     * attributes:
     *
     *      - type           (day|month|year)        Get days, months or years, default to "month"
     *      - category       category URL            Get dates of given category
     *      - no_context     (1|0)                   Override context information
     *      - order          (asc|desc)              Sort asc or desc
     *      - post_type      type                    Get dates of given type of entries, default to "post"
     *      - post_lang      lang                    Filter on the given language
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function Archives(ArrayObject $attr, string $content): string
    {
        $params = "if (!isset(\$params)) \$params = [];\n" .
            "\$params['type'] = 'month';\n";

        if (isset($attr['type'])) {
            $params .= "\$params['type'] = '" . addslashes($attr['type']) . "';\n";
        }
        if (isset($attr['category'])) {
            $params .= "\$params['cat_url'] = '" . addslashes($attr['category']) . "';\n";
        }
        if (isset($attr['post_type'])) {
            $params .= "\$params['post_type'] = '" . addslashes($attr['post_type']) . "';\n";
        }
        if (isset($attr['post_lang'])) {
            $params .= "\$params['post_lang'] = '" . addslashes($attr['post_lang']) . "';\n";
        }
        if (empty($attr['no_context']) && !isset($attr['category'])) {
            $params .= 'if (dcCore::app()->ctx->exists("categories")) { ' .
                "\$params['cat_id'] = dcCore::app()->ctx->categories->cat_id; " .
                "}\n";
        }

        if (isset($attr['order']) && preg_match('/^(desc|asc)$/i', (string) $attr['order'])) {
            $params .= "\$params['order'] = '" . (string) $attr['order'] . "';\n ";
        }

        $res = "<?php\n" .
            $params .
             dcCore::app()->callBehavior(
                 'templatePrepareParams',
                 ['tag' => 'Archives', 'method' => 'blog::getDates'],
                 $attr,
                 $content
             ) .
            'dcCore::app()->ctx->archives = dcCore::app()->blog->getDates($params); unset($params);' . "\n" .
            "?>\n";

        $res .= '<?php while (dcCore::app()->ctx->archives->fetch()) : ?>' . $content . '<?php endwhile; dcCore::app()->ctx->archives = null; ?>';

        return $res;
    }

    /**
     * tpl:ArchivesHeader : Display content on first archive element (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function ArchivesHeader(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->archives->isStart()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:ArchivesFooter : Display content on last archive element (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function ArchivesFooter(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->archives->isEnd()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:ArchivesYearHeader : Display content on first archive element of year (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function ArchivesYearHeader(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->archives->yearHeader()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:ArchivesYearFooter : Display content on last archive element of year (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function ArchivesYearFooter(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->archives->yearFooter()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:ArchivesDate [attributes] : Display archive element date (tpl value)
     *
     * attributes:
     *
     *      - format          Date format (default %B %Y)
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function ArchiveDate(ArrayObject $attr): string
    {
        $format = '%B %Y';
        if (!empty($attr['format'])) {
            $format = addslashes($attr['format']);
        }

        return '<?php echo ' . sprintf($this->getFilters($attr), "dt::dt2str('" . $format . "',dcCore::app()->ctx->archives->dt)") . '; ?>';
    }

    /**
     * tpl:ArchivesEntriesCount [attributes] : Display archive number of entries (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function ArchiveEntriesCount(ArrayObject $attr): string
    {
        return $this->displayCounter(
            sprintf($this->getFilters($attr), 'dcCore::app()->ctx->archives->nb_post'),
            [
                'none' => 'no archive',
                'one'  => 'one archive',
                'more' => '%d archives',
            ],
            $attr,
            true
        );
    }

    /**
     * tpl:ArchiveNext [attributes] : Archives next entries (tpl block)
     *
     * attributes:
     *
     *      - type           (day|month|year)        Get days, months or years, default to "month"
     *      - post_type      type                    Get dates of given type of entries, default to "post"
     *      - post_lang      lang                    Filter on the given language
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function ArchiveNext(ArrayObject $attr, string $content): string
    {
        $params = "if (!isset(\$params)) \$params = [];\n";
        $params .= "\$params['type'] = 'month';\n";
        if (isset($attr['type'])) {
            $params .= "\$params['type'] = '" . addslashes($attr['type']) . "';\n";
        }

        if (isset($attr['post_type'])) {
            $params .= "\$params['post_type'] = '" . addslashes($attr['post_type']) . "';\n";
        }

        if (isset($attr['post_lang'])) {
            $params .= "\$params['post_lang'] = '" . addslashes($attr['post_lang']) . "';\n";
        }

        $params .= "\$params['next'] = dcCore::app()->ctx->archives->dt;";

        $res = "<?php\n";
        $res .= $params;
        $res .= dcCore::app()->callBehavior(
            'templatePrepareParams',
            ['tag' => 'ArchiveNext', 'method' => 'blog::getDates'],
            $attr,
            $content
        );
        $res .= 'dcCore::app()->ctx->archives = dcCore::app()->blog->getDates($params); unset($params);' . "\n";
        $res .= "?>\n";

        $res .= '<?php while (dcCore::app()->ctx->archives->fetch()) : ?>' . $content . '<?php endwhile; dcCore::app()->ctx->archives = null; ?>';

        return $res;
    }

    /**
     * tpl:ArchivePrevious [attributes] : Archives previous entries (tpl block)
     *
     * attributes:
     *
     *      - type           (day|month|year)        Get days, months or years, default to "month"
     *      - post_type      type                    Get dates of given type of entries, default to "post"
     *      - post_lang      lang                    Filter on the given language
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function ArchivePrevious(ArrayObject $attr, string $content): string
    {
        $params = 'if (!isset($params)) $params = [];';
        $params .= "\$params['type'] = 'month';\n";
        if (isset($attr['type'])) {
            $params .= "\$params['type'] = '" . addslashes($attr['type']) . "';\n";
        }

        if (isset($attr['post_type'])) {
            $params .= "\$params['post_type'] = '" . addslashes($attr['post_type']) . "';\n";
        }

        if (isset($attr['post_lang'])) {
            $params .= "\$params['post_lang'] = '" . addslashes($attr['post_lang']) . "';\n";
        }

        $params .= "\$params['previous'] = dcCore::app()->ctx->archives->dt;";

        $res = "<?php\n";
        $res .= dcCore::app()->callBehavior(
            'templatePrepareParams',
            ['tag' => 'ArchivePrevious', 'method' => 'blog::getDates'],
            $attr,
            $content
        );
        $res .= $params;
        $res .= 'dcCore::app()->ctx->archives = dcCore::app()->blog->getDates($params); unset($params);' . "\n";
        $res .= "?>\n";

        $res .= '<?php while (dcCore::app()->ctx->archives->fetch()) : ?>' . $content . '<?php endwhile; dcCore::app()->ctx->archives = null; ?>';

        return $res;
    }

    /**
     * tpl:ArchivesURL [attributes] : Display archive result URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function ArchiveURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->archives->url()') . '; ?>';
    }

    // Blog
    // ----

    /**
     * tpl:BlogArchiveURL [attributes] : Display blog archives URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogArchiveURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->url.dcCore::app()->url->getURLFor("archive")') . '; ?>';
    }

    /**
     * tpl:BlogCopyrightNotice [attributes] : Display blog copyright notice (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogCopyrightNotice(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->settings->system->copyright_notice') . '; ?>';
    }

    /**
     * tpl:BlogDescription [attributes] : Display blog description (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogDescription(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->desc') . '; ?>';
    }

    /**
     * tpl:BlogEditor [attributes] : Display blog editor (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogEditor(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->settings->system->editor') . '; ?>';
    }

    /**
     * tpl:BlogFeedID [attributes] : Display blog feed ID (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogFeedID(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), '"urn:md5:".dcCore::app()->blog->uid') . '; ?>';
    }

    /**
     * tpl:BlogFeedURL [attributes] : Display blog feed URL (tpl value)
     *
     * attributes:
     *
     *      - type            (rss2|atom)     Feed type, default to "atom"
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogFeedURL(ArrayObject $attr): string
    {
        $type = !empty($attr['type']) ? strtolower($attr['type']) : 'atom';
        if (!in_array($type, ['rss2', 'atom'])) {
            $type = 'atom';
        }

        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->url.dcCore::app()->url->getURLFor("feed","' . $type . '")') . '; ?>';
    }

    /**
     * tpl:BlogName [attributes] : Display blog name (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogName(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->name') . '; ?>';
    }

    /**
     * tpl:BlogLanguage [attributes] : Display blog language (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogLanguage(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->settings->system->lang') . '; ?>';
    }

    /**
     * tpl:BlogLanguageURL [attributes] : Display blog localized URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogLanguageURL(ArrayObject $attr): string
    {
        $filters = $this->getFilters($attr);

        return '<?php if (dcCore::app()->ctx->exists("cur_lang")) echo ' .
            sprintf($filters, 'dcCore::app()->blog->url.dcCore::app()->url->getURLFor("lang",dcCore::app()->ctx->cur_lang)') .
            '; else echo ' .
            sprintf($filters, 'dcCore::app()->blog->url') . '; ?>';
    }

    /**
     * tpl:BlogThemeURL [attributes] : Display blog's current theme URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogThemeURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->settings->system->themes_url."/".dcCore::app()->blog->settings->system->theme') . '; ?>';
    }

    /**
     * tpl:BlogParentThemeURL [attributes] : Display blog's current theme parent URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogParentThemeURL(ArrayObject $attr): string
    {
        $parent = 'dcCore::app()->themes->moduleInfo(dcCore::app()->blog->settings->system->theme,\'parent\')';

        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->settings->system->themes_url."/".(' . "$parent" . ' ? ' . "$parent" . ' : dcCore::app()->blog->settings->system->theme)') . '; ?>';
    }

    /**
     * tpl:BlogPublicURL [attributes] : Display blog's public directory URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogPublicURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->settings->system->public_url') . '; ?>';
    }

    /**
     * tpl:BlogUpdateDate [attributes] : Display blog last update date (tpl value)
     *
     * attributes:
     *
     *      - format                  Use dt::str() (if iso8601 nor rfc822 were specified default to %Y-%m-%d %H:%M:%S)
     *      - iso8601         (1|0)   Use dt::iso8601()
     *      - rfc822          (1|0)   Use dt::rfc822()
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogUpdateDate(ArrayObject $attr): string
    {
        $format = '';
        if (!empty($attr['format'])) {
            $format = addslashes($attr['format']);
        } else {
            $format = '%Y-%m-%d %H:%M:%S';
        }

        $iso8601 = !empty($attr['iso8601']);
        $rfc822  = !empty($attr['rfc822']);

        $filters = $this->getFilters($attr);

        if ($rfc822) {
            return '<?php echo ' . sprintf($filters, 'dt::rfc822(dcCore::app()->blog->upddt,dcCore::app()->blog->settings->system->blog_timezone)') . '; ?>';
        } elseif ($iso8601) {
            return '<?php echo ' . sprintf($filters, 'dt::iso8601(dcCore::app()->blog->upddt,dcCore::app()->blog->settings->system->blog_timezone)') . '; ?>';
        }

        return '<?php echo ' . sprintf($filters, "dt::str('" . $format . "',dcCore::app()->blog->upddt)") . '; ?>';
    }

    /**
     * tpl:BlogID [attributes] : Display blog ID (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogID(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->id') . '; ?>';
    }

    /**
     * tpl:BlogRSDURL [attributes] : Display blog RSD URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     *
     * @deprecated since 2.24
     */
    public function BlogRSDURL(ArrayObject $attr): string
    {
        return '';
    }

    /**
     * tpl:BlogXMLRPCURL [attributes] : Display blog XML-RPC URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogXMLRPCURL(ArrayObject $attr): string
    {
        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->blog->url.dcCore::app()->url->getURLFor(\'xmlrpc\',dcCore::app()->blog->id)') . '; ?>';
    }

    /**
     * tpl:BlogURL [attributes] : Display blog URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->url') . '; ?>';
    }

    /**
     * tpl:BlogQmarkURL [attributes] : Display blog URL including the question mark (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogQmarkURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->getQmarkURL()') . '; ?>';
    }

    /**
     * tpl:BlogMetaRobots [attributes] : Display blog robots policy (tpl value)
     *
     * attributes:
     *
     *      - robots          (INDEX|NOINDEX|FOLLOW|NOFOLLOW|ARCHIVE|NOARCHIVE)   will surcharge the blog's parameters
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogMetaRobots(ArrayObject $attr): string
    {
        $robots = isset($attr['robots']) ? addslashes($attr['robots']) : '';

        return "<?php echo context::robotsPolicy(dcCore::app()->blog->settings->system->robots_policy,'" . $robots . "'); ?>";
    }

    /**
     * tpl:BlogJsJQuery [attributes] : Include the jQuery javascript library (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogJsJQuery(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->blog->getJsJQuery()') . '; ?>';
    }

    /**
     * tpl:BlogPostsURL [attributes] : Display the blog's posts URL (tpl value)
     *
     * Depends on blog's setting:
     *
     * - with a static home : URL of last posts
     * - without : URL of the blog
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogPostsURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), ('dcCore::app()->blog->settings->system->static_home ? dcCore::app()->blog->url.dcCore::app()->url->getURLFor("posts") : dcCore::app()->blog->url')) . '; ?>';
    }

    /**
     * tpl:IfBlogStaticEntryURL : Test if the blog has a static home entry (URL) (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function IfBlogStaticEntryURL(ArrayObject $attr, string $content): string
    {
        return
            "<?php if (dcCore::app()->blog->settings->system->static_home_url != '') : ?>" .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:BlogStaticEntryURL [attributes] : Prepare the blog's static home URL entry (tpl value)
     *
     * Should be set before a tpl:Entries block to display the according entry (post, page, …)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogStaticEntryURL(ArrayObject $attr): string
    {
        $code = "\$params['post_type'] = array_keys(dcCore::app()->getPostTypes());\n";
        $code .= "\$params['post_url'] = " . sprintf($this->getFilters($attr), 'urldecode(dcCore::app()->blog->settings->system->static_home_url)') . ";\n";

        return "<?php\n" . $code . ' ?>';
    }

    /**
     * tpl:BlogNbEntriesFirstPage [attributes] : Display the number fo entries for home page (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogNbEntriesFirstPage(ArrayObject $attr): string
    {
        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->blog->settings->system->nb_post_for_home') . '; ?>';
    }

    /**
     * tpl:BlogNbEntriesPerPage [attributes] : Display the number fo entries per page (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function BlogNbEntriesPerPage(ArrayObject $attr): string
    {
        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->blog->settings->system->nb_post_per_page') . '; ?>';
    }

    // Categories
    // ----------

    /**
     * tpl:Categories [attributes] : Categories loop (tpl block)
     *
     * attributes:
     *
     *      - cat_url                     Restrict to a category URL
     *      - post_type   (post|page|…)   Restrict to categories containing this type of entries
     *      - level       int             Restrict to categories of this level (>= 1)
     *      - with_empty  (0|1)           Include empty categories
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function Categories(ArrayObject $attr, string $content): string
    {
        $params = "if (!isset(\$params)) \$params = [];\n";
        if (isset($attr['url'])) {
            $params .= "\$params['cat_url'] = '" . addslashes($attr['url']) . "';\n";
        }
        if (!empty($attr['post_type'])) {
            $params .= "\$params['post_type'] = '" . addslashes($attr['post_type']) . "';\n";
        }
        if (!empty($attr['level'])) {
            $params .= "\$params['level'] = " . (int) $attr['level'] . ";\n";
        }
        if (isset($attr['with_empty']) && ((bool) $attr['with_empty'])) {
            $params .= "\$params['without_empty'] = false;\n";
        }
        $params .= dcCore::app()->callBehavior(
            'templatePrepareParams',
            [
                'tag'    => 'Categories',
                'method' => 'blog::getCategories',
            ],
            $attr,
            $content
        );

        $res = "<?php\n" .
            $params .
            'dcCore::app()->ctx->categories = dcCore::app()->blog->getCategories($params);' . "\n" .
             "?>\n" .
             '<?php while (dcCore::app()->ctx->categories->fetch()) : ?>' .
             $content .
             '<?php endwhile; dcCore::app()->ctx->categories = null; unset($params); ?>';

        return $res;
    }

    /**
     * tpl:CategoriesHeader : Display content on first category element (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function CategoriesHeader(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->categories->isStart()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:CategoriesFooter : Display content on last category element (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function CategoriesFooter(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->categories->isEnd()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:CategoryIf [attributes] : Include content if category tests is true (tpl block)
     *
     * attributes:
     *
     *      - url                     Category has the given URL (see note 1)
     *      - urls                    Category has one of the given comma separated urls (see note 1 for each)
     *      - has_entries     (0|1)   Category has entries (if 1), or not (if 0)
     *      - has_description (0|1)   Category has description (if 1), or not (if 0)
     *
     *      Notes:
     *
     *      1) Use ! as prefix to inverse test, use ' sub' as suffix to includes category's sub-categories
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function CategoryIf(ArrayObject $attr, string $content): string
    {
        $if       = new ArrayObject();
        $operator = isset($attr['operator']) ? static::getOperator($attr['operator']) : '&&';

        if (isset($attr['url'])) {
            $url  = addslashes(trim((string) $attr['url']));
            $args = preg_split('/\s*[?]\s*/', $url, -1, PREG_SPLIT_NO_EMPTY);
            $url  = array_shift($args);
            $args = array_flip($args);
            if (substr($url, 0, 1) == '!') {
                $url = substr($url, 1);
                if (isset($args['sub'])) {
                    $if[] = '(!dcCore::app()->blog->IsInCatSubtree(dcCore::app()->ctx->categories->cat_url, "' . $url . '"))';
                } else {
                    $if[] = '(dcCore::app()->ctx->categories->cat_url != "' . $url . '")';
                }
            } else {
                if (isset($args['sub'])) {
                    $if[] = '(dcCore::app()->blog->IsInCatSubtree(dcCore::app()->ctx->categories->cat_url, "' . $url . '"))';
                } else {
                    $if[] = '(dcCore::app()->ctx->categories->cat_url == "' . $url . '")';
                }
            }
        }

        if (isset($attr['urls'])) {
            $urls = explode(',', addslashes(trim((string) $attr['urls'])));
            if (is_array($urls)) {
                foreach ($urls as $url) {
                    $args = preg_split('/\s*[?]\s*/', trim($url), -1, PREG_SPLIT_NO_EMPTY);
                    $url  = array_shift($args);
                    $args = array_flip($args);
                    if (substr($url, 0, 1) == '!') {
                        $url = substr($url, 1);
                        if (isset($args['sub'])) {
                            $if[] = '(!dcCore::app()->blog->IsInCatSubtree(dcCore::app()->ctx->categories->cat_url, "' . $url . '"))';
                        } else {
                            $if[] = '(dcCore::app()->ctx->categories->cat_url != "' . $url . '")';
                        }
                    } else {
                        if (isset($args['sub'])) {
                            $if[] = '(dcCore::app()->blog->IsInCatSubtree(dcCore::app()->ctx->categories->cat_url, "' . $url . '"))';
                        } else {
                            $if[] = '(dcCore::app()->ctx->categories->cat_url == "' . $url . '")';
                        }
                    }
                }
            }
        }

        if (isset($attr['has_entries'])) {
            $sign = (bool) $attr['has_entries'] ? '>' : '==';
            $if[] = 'dcCore::app()->ctx->categories->nb_post ' . $sign . ' 0';
        }

        if (isset($attr['has_description'])) {
            $sign = (bool) $attr['has_description'] ? '!=' : '==';
            $if[] = 'dcCore::app()->ctx->categories->cat_desc ' . $sign . ' ""';
        }

        dcCore::app()->callBehavior('tplIfConditions', 'CategoryIf', $attr, $content, $if);

        if (count($if)) {
            return '<?php if(' . implode(' ' . $operator . ' ', (array) $if) . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    /**
     * tpl:CategoryFirstChildren : Current category first children loop (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function CategoryFirstChildren(ArrayObject $attr, string $content): string
    {
        return
            "<?php\n" .
            'dcCore::app()->ctx->categories = dcCore::app()->blog->getCategoryFirstChildren(dcCore::app()->ctx->categories->cat_id);' . "\n" .
            'while (dcCore::app()->ctx->categories->fetch()) : ?>' . $content . '<?php endwhile; dcCore::app()->ctx->categories = null; ?>';
    }

    /**
     * tpl:CategoryParents : Current category parents loop (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function CategoryParents(ArrayObject $attr, string $content): string
    {
        return
            "<?php\n" .
            'dcCore::app()->ctx->categories = dcCore::app()->blog->getCategoryParents(dcCore::app()->ctx->categories->cat_id);' . "\n" .
            'while (dcCore::app()->ctx->categories->fetch()) : ?>' . $content . '<?php endwhile; dcCore::app()->ctx->categories = null; ?>';
    }

    /**
     * tpl:CategoryFeedURL [attributes] : Category feed URL (tpl value)
     *
     * attributes:
     *
     *      - type            (rss2|atom)     Feed type, default to "atom"
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CategoryFeedURL(ArrayObject $attr): string
    {
        $type = !empty($attr['type']) ? (string) $attr['type'] : 'atom';

        if (!preg_match('#^(rss2|atom)$#', $type)) {
            $type = 'atom';
        }

        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->blog->url.dcCore::app()->url->getURLFor("feed","category/".' .
            'dcCore::app()->ctx->categories->cat_url."/' . $type . '")') . '; ?>';
    }

    /**
     * tpl:CategoryID [attributes] : Category ID (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CategoryID(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->categories->cat_id') . '; ?>';
    }

    /**
     * tpl:CategoryURL [attributes] : Category full URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CategoryURL(ArrayObject $attr): string
    {
        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->blog->url.dcCore::app()->url->getURLFor("category",' .
            'dcCore::app()->ctx->categories->cat_url)') . '; ?>';
    }

    /**
     * tpl:CategoryShortURL [attributes] : Category short URL, relative from /category/ (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CategoryShortURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->categories->cat_url') . '; ?>';
    }

    /**
     * tpl:CategoryDescription [attributes] : Category description (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CategoryDescription(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->categories->cat_desc') . '; ?>';
    }

    /**
     * tpl:CategoryTitle [attributes] : Category title (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CategoryTitle(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->categories->cat_title') . '; ?>';
    }

    /**
     * tpl:CategoryEntriesCount [attributes] : Category number of entries (tpl value)
     *
     * attributes:
     *
     *      - count_only      (1|0)   Display only counter value
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CategoryEntriesCount(ArrayObject $attr): string
    {
        return $this->displayCounter(
            sprintf($this->getFilters($attr), 'dcCore::app()->ctx->categories->nb_post'),
            [
                'none' => 'No post',
                'one'  => 'One post',
                'more' => '%d posts',
            ],
            $attr,
            true
        );
    }

    // Entries
    // -------

    /**
     * tpl:Entries [attributes] : Entries loop (tpl block)
     *
     * attributes:
     *
     *      - lastn               int         Limit number of results to specified value
     *      - author              string      Get entries for a given user id
     *      - category            string      Get entries for specific categories only (comma-separated categories), see note 1
     *      - no_category         (1|0)       Get entries without category
     *      - with_category       (1|0)       Get entries with a category
     *      - no_context          (1|0)       Override context information
     *      - sortby              (title|selected|author|date|id)     Specify entries sort criteria (default : date), see note 2
     *      - order               (desc|asc)  specify entries order (default : desc)
     *      - no_content          (0|1)       Do not retrieve entries content
     *      - selected            (0|1)       Retrieve posts marked as selected only (if 1) or not selected only (if 0)
     *      - url                 string      Retrieve post by its url
     *      - type                (post|page|…)   Restrict to entries with this type (comma separated types)
     *      - age                 string      Retrieve posts by maximum age, see note 3
     *      - ignore_pagination   (0|1)       Ignore page number provided in URL, see note 4
     *
     *      Notes:
     *
     *      1) Use ! as prefix to inverse test
     *      2) Comma-separated sortbies can be specified. Use "?asc" or "?desc" as suffix to provide an order for each
     *      3) Examples: -2 days, last month, last week
     *      4) Useful when using multiple tpl:Entries on the same page
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function Entries(ArrayObject $attr, string $content): string
    {
        $lastn = -1;
        if (isset($attr['lastn'])) {
            $lastn = abs((int) $attr['lastn']) + 0;
        }

        $params = 'if (dcCore::app()->public->getPageNumber() === 0) { dcCore::app()->public->setPageNumber(1); }' . "\n";

        if ($lastn != 0) {
            // Set limit (aka nb of entries needed)
            if ($lastn > 0) {
                // nb of entries per page specified in template -> regular pagination
                $params .= "\$params['limit'] = " . $lastn . ";\n";
                $params .= '$nb_entry_first_page = $nb_entry_per_page = ' . $lastn . ";\n";
            } else {
                // nb of entries per page not specified -> use ctx settings
                $params .= "\$nb_entry_first_page=dcCore::app()->ctx->nb_entry_first_page; \$nb_entry_per_page = dcCore::app()->ctx->nb_entry_per_page;\n";
                $params .= "if ((dcCore::app()->url->type == 'default') || (dcCore::app()->url->type == 'default-page')) {\n";
                $params .= "    \$params['limit'] = (dcCore::app()->public->getPageNumber() === 1 ? \$nb_entry_first_page : \$nb_entry_per_page);\n";
                $params .= "} else {\n";
                $params .= "    \$params['limit'] = \$nb_entry_per_page;\n";
                $params .= "}\n";
            }
            // Set offset (aka index of first entry)
            if (!isset($attr['ignore_pagination']) || $attr['ignore_pagination'] == '0') {
                // standard pagination, set offset
                $params .= "if ((dcCore::app()->url->type == 'default') || (dcCore::app()->url->type == 'default-page')) {\n";
                $params .= "    \$params['limit'] = [(dcCore::app()->public->getPageNumber() === 1 ? 0 : (dcCore::app()->public->getPageNumber() - 2) * \$nb_entry_per_page + \$nb_entry_first_page),\$params['limit']];\n";
                $params .= "} else {\n";
                $params .= "    \$params['limit'] = [(dcCore::app()->public->getPageNumber() - 1) * \$nb_entry_per_page,\$params['limit']];\n";
                $params .= "}\n";
            } else {
                // no pagination, get all posts from 0 to limit
                $params .= "\$params['limit'] = [0, \$params['limit']];\n";
            }
        }

        if (isset($attr['author'])) {
            $params .= "\$params['user_id'] = '" . addslashes($attr['author']) . "';\n";
        }

        if (isset($attr['category'])) {
            $params .= "\$params['cat_url'] = '" . addslashes($attr['category']) . "';\n";
            $params .= "context::categoryPostParam(\$params);\n";
        }

        if (isset($attr['with_category']) && $attr['with_category']) {
            $params .= "@\$params['sql'] .= ' AND P.cat_id IS NOT NULL ';\n";
        }

        if (isset($attr['no_category']) && $attr['no_category']) {
            $params .= "@\$params['sql'] .= ' AND P.cat_id IS NULL ';\n";
            $params .= "unset(\$params['cat_url']);\n";
        }

        if (!empty($attr['type'])) {
            $params .= "\$params['post_type'] = preg_split('/\s*,\s*/','" . addslashes($attr['type']) . "',-1,PREG_SPLIT_NO_EMPTY);\n";
        }

        if (!empty($attr['url'])) {
            $params .= "\$params['post_url'] = '" . addslashes($attr['url']) . "';\n";
        }

        if (empty($attr['no_context'])) {
            if (!isset($attr['author'])) {
                $params .= 'if (dcCore::app()->ctx->exists("users")) { ' .
                    "\$params['user_id'] = dcCore::app()->ctx->users->user_id; " .
                    "}\n";
            }

            if (!isset($attr['category']) && (!isset($attr['no_category']) || !$attr['no_category'])) {
                $params .= 'if (dcCore::app()->ctx->exists("categories")) { ' .
                    "\$params['cat_id'] = dcCore::app()->ctx->categories->cat_id.(dcCore::app()->blog->settings->system->inc_subcats?' ?sub':'');" .
                    "}\n";
            }

            $params .= 'if (dcCore::app()->ctx->exists("archives")) { ' .
                "\$params['post_year'] = dcCore::app()->ctx->archives->year(); " .
                "\$params['post_month'] = dcCore::app()->ctx->archives->month(); ";
            if (!isset($attr['lastn'])) {
                $params .= "unset(\$params['limit']); ";
            }
            $params .= "}\n";

            $params .= 'if (dcCore::app()->ctx->exists("langs")) { ' .
                "\$params['post_lang'] = dcCore::app()->ctx->langs->post_lang; " .
                "}\n";

            $params .= 'if (isset(dcCore::app()->public->search)) { ' .
                "\$params['search'] = dcCore::app()->public->search; " .
                "}\n";
        }

        $params .= "\$params['order'] = '" . $this->getSortByStr($attr, 'post') . "';\n";

        if (isset($attr['no_content']) && $attr['no_content']) {
            $params .= "\$params['no_content'] = true;\n";
        }

        if (isset($attr['selected'])) {
            $params .= "\$params['post_selected'] = " . (int) (bool) $attr['selected'] . ';';
        }

        if (isset($attr['age'])) {
            $age = static::getAge($attr);
            $params .= !empty($age) ? "@\$params['sql'] .= ' AND P.post_dt > \'" . $age . "\'';\n" : '';
        }

        $res = "<?php\n";
        $res .= $params;
        $res .= dcCore::app()->callBehavior(
            'templatePrepareParams',
            ['tag' => 'Entries', 'method' => 'blog::getPosts'],
            $attr,
            $content
        );
        $res .= 'dcCore::app()->ctx->post_params = $params;' . "\n";
        $res .= 'dcCore::app()->ctx->posts = dcCore::app()->blog->getPosts($params); unset($params);' . "\n";
        $res .= "?>\n";
        $res .= '<?php while (dcCore::app()->ctx->posts->fetch()) : ?>' . $content . '<?php endwhile; ' .
            'dcCore::app()->ctx->posts = null; dcCore::app()->ctx->post_params = null; ?>';

        return $res;
    }

    /**
     * tpl:DateHeader : Displays content, if post is the first post of the given day (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function DateHeader(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->posts->firstPostOfDay()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:DateFooter : Displays content, if post is the last post of the given day (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function DateFooter(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->posts->lastPostOfDay()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:EntryIf [attributes] : Include content if entry tests is true (tpl block)
     *
     * attributes:
     *
     *      - type            (post|page|…)   Post has a given type (default: "post")
     *      - url             string          Post has given url
     *      - author          string          Post has given user_id
     *      - category        string          Post has a given category URL, see note 1
     *      - categories      string          Post has a given categories (comma separated) URL, see note 1
     *      - first           (0|1)           Post is the first post from list (if 1) or not (if 0)
     *      - odd             (0|1)           Post is in an odd position (if 1) or not (if 0)
     *      - even            (0|1)           Post is in an even position (if 1) or not (if 0)
     *      - extended        (0|1)           Post has an excerpt (if 1) or not (if 0)
     *      - selected        (0|1)           Post is selected (if 1) or not (if 0)
     *      - has_category    (0|1)           Post has a category (if 1) or not (if 0)
     *      - has_attachment  (0|1)           Post has attachments (if 1) or not (if 0)
     *      - comments_active (0|1)           Comments are active for this post (if 1) or not (if 0)
     *      - pings_active    (0|1)           Trackbacks are active for this post (if 1) or not (if 0)
     *      - has_comments    (0|1)           There are comments for this post (if 1) or not (if 0)
     *      - has_pings       (0|1)           There are trackbacks for this post (if 1) or not (if 0)
     *      - show_comments   (0|1)           Comments are enabled for this post (if 1) or not (if 0)
     *      - show_pings      (0|1)           Trackbacks are enabled for this post (if 1) or not (if 0)
     *      - republished     (0|1)           Post has been updated since publication (if 1) or not (if 0)
     *      - operator        (and|or)        Combination of conditions, if more than 1 specified (default: and)
     *
     *      Notes:
     *
     *      1) Use ! as prefix to inverse test, use ' sub' as suffix to includes category's sub-categories
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function EntryIf(ArrayObject $attr, string $content): string
    {
        $if = new ArrayObject();

        $operator = isset($attr['operator']) ? static::getOperator($attr['operator']) : '&&';

        if (isset($attr['type'])) {
            $type = trim((string) $attr['type']);
            $type = !empty($type) ? $type : 'post';
            $if[] = 'dcCore::app()->ctx->posts->post_type == "' . addslashes($type) . '"';
        }

        if (isset($attr['url'])) {
            $url = trim((string) $attr['url']);
            if (substr($url, 0, 1) == '!') {
                $url  = substr($url, 1);
                $if[] = 'dcCore::app()->ctx->posts->post_url != "' . addslashes($url) . '"';
            } else {
                $if[] = 'dcCore::app()->ctx->posts->post_url == "' . addslashes($url) . '"';
            }
        }

        if (isset($attr['category'])) {
            $category = addslashes(trim((string) $attr['category']));
            $args     = preg_split('/\s*[?]\s*/', $category, -1, PREG_SPLIT_NO_EMPTY);
            $category = array_shift($args);
            $args     = array_flip($args);
            if (substr($category, 0, 1) == '!') {
                $category = substr($category, 1);
                if (isset($args['sub'])) {
                    $if[] = '(!dcCore::app()->ctx->posts->underCat("' . $category . '"))';
                } else {
                    $if[] = '(dcCore::app()->ctx->posts->cat_url != "' . $category . '")';
                }
            } else {
                if (isset($args['sub'])) {
                    $if[] = '(dcCore::app()->ctx->posts->underCat("' . $category . '"))';
                } else {
                    $if[] = '(dcCore::app()->ctx->posts->cat_url == "' . $category . '")';
                }
            }
        }

        if (isset($attr['categories'])) {
            $categories = explode(',', addslashes(trim((string) $attr['categories'])));
            if (is_array($categories)) {
                foreach ($categories as $category) {
                    $args     = preg_split('/\s*[?]\s*/', trim((string) $category), -1, PREG_SPLIT_NO_EMPTY);
                    $category = array_shift($args);
                    $args     = array_flip($args);
                    if (substr($category, 0, 1) == '!') {
                        $category = substr($category, 1);
                        if (isset($args['sub'])) {
                            $if[] = '(!dcCore::app()->ctx->posts->underCat("' . $category . '"))';
                        } else {
                            $if[] = '(dcCore::app()->ctx->posts->cat_url != "' . $category . '")';
                        }
                    } else {
                        if (isset($args['sub'])) {
                            $if[] = '(dcCore::app()->ctx->posts->underCat("' . $category . '"))';
                        } else {
                            $if[] = '(dcCore::app()->ctx->posts->cat_url == "' . $category . '")';
                        }
                    }
                }
            }
        }

        if (isset($attr['first'])) {
            $sign = (bool) $attr['first'] ? '=' : '!';
            $if[] = 'dcCore::app()->ctx->posts->index() ' . $sign . '= 0';
        }

        if (isset($attr['odd'])) {
            $sign = (bool) $attr['odd'] ? '=' : '!';
            $if[] = '(dcCore::app()->ctx->posts->index()+1)%2 ' . $sign . '= 1';
        }

        if (isset($attr['even'])) {
            $sign = (bool) $attr['even'] ? '=' : '!';
            $if[] = '(dcCore::app()->ctx->posts->index()+1)%2 ' . $sign . '= 0';
        }

        if (isset($attr['extended'])) {
            $sign = (bool) $attr['extended'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->ctx->posts->isExtended()';
        }

        if (isset($attr['selected'])) {
            $sign = (bool) $attr['selected'] ? '' : '!';
            $if[] = $sign . '(boolean)dcCore::app()->ctx->posts->post_selected';
        }

        if (isset($attr['has_category'])) {
            $sign = (bool) $attr['has_category'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->ctx->posts->cat_id';
        }

        if (isset($attr['comments_active'])) {
            $sign = (bool) $attr['comments_active'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->ctx->posts->commentsActive()';
        }

        if (isset($attr['pings_active'])) {
            $sign = (bool) $attr['pings_active'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->ctx->posts->trackbacksActive()';
        }

        if (isset($attr['has_comment'])) {
            $sign = (bool) $attr['has_comment'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->ctx->posts->hasComments()';
        }

        if (isset($attr['has_ping'])) {
            $sign = (bool) $attr['has_ping'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->ctx->posts->hasTrackbacks()';
        }

        if (isset($attr['show_comments'])) {
            if ((bool) $attr['show_comments']) {
                $if[] = '(dcCore::app()->ctx->posts->hasComments() || dcCore::app()->ctx->posts->commentsActive())';
            } else {
                $if[] = '(!dcCore::app()->ctx->posts->hasComments() && !dcCore::app()->ctx->posts->commentsActive())';
            }
        }

        if (isset($attr['show_pings'])) {
            if ((bool) $attr['show_pings']) {
                $if[] = '(dcCore::app()->ctx->posts->hasTrackbacks() || dcCore::app()->ctx->posts->trackbacksActive())';
            } else {
                $if[] = '(!dcCore::app()->ctx->posts->hasTrackbacks() && !dcCore::app()->ctx->posts->trackbacksActive())';
            }
        }

        if (isset($attr['republished'])) {
            $sign = (bool) $attr['republished'] ? '' : '!';
            $if[] = $sign . '(boolean)dcCore::app()->ctx->posts->isRepublished()';
        }

        if (isset($attr['author'])) {
            $author = trim((string) $attr['author']);
            if (substr($author, 0, 1) == '!') {
                $author = substr($author, 1);
                $if[]   = 'dcCore::app()->ctx->posts->user_id != "' . $author . '"';
            } else {
                $if[] = 'dcCore::app()->ctx->posts->user_id == "' . $author . '"';
            }
        }

        dcCore::app()->callBehavior('tplIfConditions', 'EntryIf', $attr, $content, $if);

        if (count($if)) {
            return '<?php if(' . implode(' ' . $operator . ' ', (array) $if) . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    /**
     * tpl:EntryIfFirst [attributes] : Displays value if entry is the first one (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: first)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryIfFirst(ArrayObject $attr): string
    {
        $ret = $attr['return'] ?? 'first';
        $ret = html::escapeHTML($ret);

        return
        '<?php if (dcCore::app()->ctx->posts->index() == 0) { ' .
        "echo '" . addslashes($ret) . "'; } ?>";
    }

    /**
     * tpl:EntryIfOdd [attributes] : Displays value if entry is in odd position (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: odd)
     *      - even        string      Value to display if not (default: <empty>)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryIfOdd(ArrayObject $attr): string
    {
        $odd = $attr['return'] ?? 'odd';
        $odd = html::escapeHTML($odd);

        $even = $attr['even'] ?? '';
        $even = html::escapeHTML($even);

        return '<?php echo ((dcCore::app()->ctx->posts->index()+1)%2 ? ' .
        '"' . addslashes($odd) . '" : ' .
        '"' . addslashes($even) . '"); ?>';
    }

    /**
     * tpl:EntryIfEven [attributes] : Displays value if entry is in even position (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: even)
     *      - odd         string      Value to display if not (default: <empty>)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryIfEven(ArrayObject $attr): string
    {
        $even = $attr['return'] ?? 'even';
        $even = html::escapeHTML($even);

        $odd = $attr['odd'] ?? '';
        $odd = html::escapeHTML($odd);

        return '<?php echo ((dcCore::app()->ctx->posts->index()+1)%2+1 ? ' .
        '"' . addslashes($even) . '" : ' .
        '"' . addslashes($odd) . '"); ?>';
    }

    /**
     * tpl:EntryIfSelected [attributes] : Displays value if entry is selected (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: selected)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryIfSelected(ArrayObject $attr): string
    {
        $ret = $attr['return'] ?? 'selected';
        $ret = html::escapeHTML($ret);

        return
        '<?php if (dcCore::app()->ctx->posts->post_selected) { ' .
        "echo '" . addslashes($ret) . "'; } ?>";
    }

    /**
     * tpl:EntryContent [attributes] : Displays entry content (tpl value)
     *
     * attributes:
     *
     *      - absolute_urls   (1|0)   Transforms local URLs to absolute one
     *      - full            (1|0)   Returns full content with excerpt
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryContent(ArrayObject $attr): string
    {
        $urls = '0';
        if (!empty($attr['absolute_urls'])) {
            $urls = '1';
        }

        $filters = $this->getFilters($attr);

        if (!empty($attr['full'])) {
            return '<?php echo ' . sprintf(
                $filters,
                'dcCore::app()->ctx->posts->getExcerpt(' . $urls . ').' .
                '(strlen(dcCore::app()->ctx->posts->getExcerpt(' . $urls . ')) ? " " : "").' .
                'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
            ) . '; ?>';
        }

        return '<?php echo ' . sprintf(
            $filters,
            'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
        ) . '; ?>';
    }

    /**
     * tpl:EntryIfContentCut [attributes] : Displays ccontent if entry content has been cut (tpl block)
     *
     * attributes:
     *
     *      - cut_string      int     Cut length, see self::getFilters()
     *      - absolute_urls   (1|0)   Transforms local URLs to absolute one
     *      - full            (1|0)   Returns full content with excerpt
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function EntryIfContentCut(ArrayObject $attr, string $content): string
    {
        if (empty($attr['cut_string'])) {
            return '';
        }

        $urls = '0';
        if (!empty($attr['absolute_urls'])) {
            $urls = '1';
        }

        $short              = $this->getFilters($attr);
        $cut                = $attr['cut_string'];
        $attr['cut_string'] = 0;
        $full               = $this->getFilters($attr);
        $attr['cut_string'] = $cut;

        if (!empty($attr['full'])) {
            return '<?php if (strlen(' . sprintf(
                $full,
                'dcCore::app()->ctx->posts->getExcerpt(' . $urls . ').' .
                '(strlen(dcCore::app()->ctx->posts->getExcerpt(' . $urls . ')) ? " " : "").' .
                'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
            ) . ') > ' .
            'strlen(' . sprintf(
                $short,
                'dcCore::app()->ctx->posts->getExcerpt(' . $urls . ').' .
                '(strlen(dcCore::app()->ctx->posts->getExcerpt(' . $urls . ')) ? " " : "").' .
                'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
            ) . ')) : ?>' .
                $content .
                '<?php endif; ?>';
        }

        return '<?php if (strlen(' . sprintf(
            $full,
            'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
        ) . ') > ' .
            'strlen(' . sprintf(
                $short,
                'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
            ) . ')) : ?>' .
                $content .
                '<?php endif; ?>';
    }

    /**
     * tpl:EntryExcerpt [attributes] : Displays entry excerpt (tpl value)
     *
     * attributes:
     *
     *      - absolute_urls   (1|0)   Transforms local URLs to absolute one
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryExcerpt(ArrayObject $attr): string
    {
        $urls = '0';
        if (!empty($attr['absolute_urls'])) {
            $urls = '1';
        }

        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->getExcerpt(' . $urls . ')') . '; ?>';
    }

    /**
     * tpl:EntryAuthorCommonName [attributes] : Displays entry author common name (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryAuthorCommonName(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->getAuthorCN()') . '; ?>';
    }

    /**
     * tpl:EntryAuthorDisplayName [attributes] : Displays entry author display name (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryAuthorDisplayName(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->user_displayname') . '; ?>';
    }

    /**
     * tpl:EntryAuthorID [attributes] : Displays entry author ID (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryAuthorID(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->user_id') . '; ?>';
    }

    /**
     * tpl:EntryAuthorEmail [attributes] : Displays entry author email (tpl value)
     *
     * attributes:
     *
     *      - spam_protected  (1|0)   Protect email from spam (default: 1)
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryAuthorEmail(ArrayObject $attr): string
    {
        $protect = 'true';
        if (isset($attr['spam_protected']) && !$attr['spam_protected']) {
            $protect = 'false';
        }

        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->getAuthorEmail(' . $protect . ')') . '; ?>';
    }

    /**
     * tpl:EntryAuthorEmailMD5 [attributes] : Displays entry author email MD5 sum (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryAuthorEmailMD5(ArrayObject $attr): string
    {
        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'md5(dcCore::app()->ctx->posts->getAuthorEmail(false))') . '; ?>';
    }

    /**
     * tpl:EntryAuthorLink [attributes] : Displays entry author link (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryAuthorLink(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->getAuthorLink()') . '; ?>';
    }

    /**
     * tpl:EntryAuthorURL [attributes] : Displays entry author URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryAuthorURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->user_url') . '; ?>';
    }

    /**
     * tpl:EntryBasename [attributes] : Displays entry basename URL, relative to /post (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryBasename(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->post_url') . '; ?>';
    }

    /**
     * tpl:EntryCategory [attributes] : Displays entry fullname category (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryCategory(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->cat_title') . '; ?>';
    }

    /**
     * tpl:EntryCategoryDescription [attributes] : Displays entry category description (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryCategoryDescription(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->cat_desc') . '; ?>';
    }

    /**
     * tpl:EntryCategoriesBreadcrumb : Current entry category's parents loop (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function EntryCategoriesBreadcrumb(ArrayObject $attr, string $content): string
    {
        return
            "<?php\n" .
            'dcCore::app()->ctx->categories = dcCore::app()->blog->getCategoryParents(dcCore::app()->ctx->posts->cat_id);' . "\n" .
            'while (dcCore::app()->ctx->categories->fetch()) : ?>' . $content . '<?php endwhile; dcCore::app()->ctx->categories = null; ?>';
    }

    /**
     * tpl:EntryCategoryID [attributes] : Displays entry category ID (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryCategoryID(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->cat_id') . '; ?>';
    }

    /**
     * tpl:EntryCategoryURL [attributes] : Displays entry category URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryCategoryURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->getCategoryURL()') . '; ?>';
    }

    /**
     * tpl:EntryCategoryShortURL [attributes] : Displays entry category short URL, relative to /category/ (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryCategoryShortURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->cat_url') . '; ?>';
    }

    /**
     * tpl:EntryFeedID [attributes] : Displays entry feed ID (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryFeedID(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->getFeedID()') . '; ?>';
    }

    /**
     * tpl:EntryFirstImage [attributes] : Extracts entry first image if exists (tpl value)
     *
     * attributes:
     *
     *      - size            (sq|t|s|m|o)    Image size to extract
     *      - class           string          Class to add on image tag
     *      - with_category   (1|0)           Search in entry category description if present (default 0)
     *      - no_tag          (1|0)           Return image URL without HTML tag (default 0)
     *      - content_only    (1|0)           Search in content entry only, not in excerpt (default 0)
     *      - cat_only        (1|0)           Search in category description only (default 0)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryFirstImage(ArrayObject $attr): string
    {
        $size          = !empty($attr['size']) ? $attr['size'] : '';
        $class         = !empty($attr['class']) ? $attr['class'] : '';
        $with_category = !empty($attr['with_category']) ? 'true' : 'false';
        $no_tag        = !empty($attr['no_tag']) ? 'true' : 'false';
        $content_only  = !empty($attr['content_only']) ? 'true' : 'false';
        $cat_only      = !empty($attr['cat_only']) ? 'true' : 'false';

        return "<?php echo context::EntryFirstImageHelper('" . addslashes($size) . "'," . $with_category . ",'" . addslashes($class) . "'," .
            $no_tag . ',' . $content_only . ',' . $cat_only . '); ?>';
    }

    /**
     * tpl:EntryID [attributes] : Displays entry ID (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryID(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->post_id') . '; ?>';
    }

    /**
     * tpl:EntryLang [attributes] : Displays entry lang or blog lang if not defined (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryLang(ArrayObject $attr): string
    {
        $filters = $this->getFilters($attr);

        return
        '<?php if (dcCore::app()->ctx->posts->post_lang) { ' .
        'echo ' . sprintf($filters, 'dcCore::app()->ctx->posts->post_lang') . '; ' .
        '} else {' .
        'echo ' . sprintf($filters, 'dcCore::app()->blog->settings->system->lang') . '; ' .
            '} ?>';
    }

    /**
     * tpl:EntryNext [attributes] : Next entry block (tpl block)
     *
     * attributes:
     *
     *      - restrict_to_category    (0|1)    Find next post in the same category (default 0)
     *      - restrict_to_lang        (0|1)    Find next post in the same language (default 0)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function EntryNext(ArrayObject $attr, string $content): string
    {
        $restrict_to_category = !empty($attr['restrict_to_category']) ? '1' : '0';
        $restrict_to_lang     = !empty($attr['restrict_to_lang']) ? '1' : '0';

        return
            '<?php $next_post = dcCore::app()->blog->getNextPost(dcCore::app()->ctx->posts,1,' . $restrict_to_category . ',' . $restrict_to_lang . '); ?>' . "\n" .
            '<?php if ($next_post !== null) : ?>' .

            '<?php dcCore::app()->ctx->posts = $next_post; unset($next_post);' . "\n" .
            'while (dcCore::app()->ctx->posts->fetch()) : ?>' .
            $content .
            '<?php endwhile; dcCore::app()->ctx->posts = null; ?>' .
            "<?php endif; ?>\n";
    }

    /**
     * tpl:EntryPrevious [attributes] : Previous entry block (tpl block)
     *
     * attributes:
     *
     *      - restrict_to_category    (0|1)    Find next post in the same category (default 0)
     *      - restrict_to_lang        (0|1)    Find next post in the same language (default 0)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function EntryPrevious(ArrayObject $attr, string $content): string
    {
        $restrict_to_category = !empty($attr['restrict_to_category']) ? '1' : '0';
        $restrict_to_lang     = !empty($attr['restrict_to_lang']) ? '1' : '0';

        return
            '<?php $prev_post = dcCore::app()->blog->getNextPost(dcCore::app()->ctx->posts,-1,' . $restrict_to_category . ',' . $restrict_to_lang . '); ?>' . "\n" .
            '<?php if ($prev_post !== null) : ?>' .

            '<?php dcCore::app()->ctx->posts = $prev_post; unset($prev_post);' . "\n" .
            'while (dcCore::app()->ctx->posts->fetch()) : ?>' .
            $content .
            '<?php endwhile; dcCore::app()->ctx->posts = null; ?>' .
            "<?php endif; ?>\n";
    }

    /**
     * tpl:EntryTitle [attributes] : Displays entry title (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryTitle(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->post_title') . '; ?>';
    }

    /**
     * tpl:EntryURL [attributes] : Displays entry URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->posts->getURL()') . '; ?>';
    }

    /**
     * tpl:EntryDate [attributes] : Displays entry date (tpl value)
     *
     * attributes:
     *
     *      - format      string      Date format (see dt::str() by default if iso8601 or rfc822 not specified)
     *      - iso8601     (1|0)       If set, display date in ISO 8601 format
     *      - rfc822      (1|0)       If set, display date in RFC 822 format
     *      - upddt       (1|0)       If set, uses the post update time
     *      - creadt      (1|0)       If set, uses the post creation time
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryDate(ArrayObject $attr): string
    {
        $format = '';
        if (!empty($attr['format'])) {
            $format = addslashes($attr['format']);
        }

        $iso8601 = !empty($attr['iso8601']);
        $rfc822  = !empty($attr['rfc822']);
        $type    = (!empty($attr['creadt']) ? 'creadt' : '');
        $type    = (!empty($attr['upddt']) ? 'upddt' : $type);

        $filters = $this->getFilters($attr);

        if ($rfc822) {
            return '<?php echo ' .
                sprintf($filters, "\dcCore::app()->ctx->posts->getRFC822Date('" . $type . "')") . '; ?>';
        } elseif ($iso8601) {
            return '<?php echo ' .
                sprintf($filters, "dcCore::app()->ctx->posts->getISO8601Date('" . $type . "')") . '; ?>';
        }

        return '<?php echo ' .
            sprintf($filters, "dcCore::app()->ctx->posts->getDate('" . $format . "','" . $type . "')") . '; ?>';
    }

    /**
     * tpl:EntryTime [attributes] : Displays entry date (tpl value)
     *
     * attributes:
     *
     *      - format      string      Time format
     *      - upddt       (1|0)       If set, uses the post update time
     *      - creadt      (1|0)       If set, uses the post creation time
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryTime(ArrayObject $attr): string
    {
        $format = '';
        if (!empty($attr['format'])) {
            $format = addslashes($attr['format']);
        }

        $type = (!empty($attr['creadt']) ? 'creadt' : '');
        $type = (!empty($attr['upddt']) ? 'upddt' : $type);

        return '<?php echo ' .
            sprintf($this->getFilters($attr), "dcCore::app()->ctx->posts->getTime('" . $format . "','" . $type . "')") . '; ?>';
    }

    /**
     * tpl:EntriesHeader : First entries result container (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function EntriesHeader(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->posts->isStart()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:EntriesFooter : Last entries result container (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function EntriesFooter(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->posts->isEnd()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:EntryCommentCount [attributes] : Number of comments for entry (tpl value)
     *
     * attributes:
     *
     *      - none        string      Text to display for "no comments" (default: no comments)
     *      - one         string      Text to display for "one comment" (default: one comment)
     *      - more        string      Text to display for "more comments" (default: %s comments, see note 1)
     *      - count_all   (1|0)       Count comments plus trackbacks
     *
     *      Notes:
     *
     *      1) %s will be replaced by the number of comments
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryCommentCount(ArrayObject $attr): string
    {
        if (empty($attr['count_all'])) {
            $operation = 'dcCore::app()->ctx->posts->nb_comment';
        } else {
            $operation = '(dcCore::app()->ctx->posts->nb_comment + dcCore::app()->ctx->posts->nb_trackback)';
        }

        return $this->displayCounter(
            $operation,
            [
                'none' => 'no comments',
                'one'  => 'one comment',
                'more' => '%d comments',
            ],
            $attr,
            false
        );
    }

    /**
     * tpl:EntryPingCount [attributes] : Number of pings (see note 1) for entry (tpl value)
     *
     * attributes:
     *
     *      - none        string      Text to display for "no pings" (default: no pings)
     *      - one         string      Text to display for "one ping" (default: one ping)
     *      - more        string      Text to display for "more pings" (default: %s pings, see note 2)
     *
     *      Notes:
     *
     *      1) A ping may be a trackback, a pingback or a webmention
     *      2) %s will be replaced by the number of pings
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryPingCount(ArrayObject $attr): string
    {
        return $this->displayCounter(
            'dcCore::app()->ctx->posts->nb_trackback',
            [
                'none' => 'no trackbacks',
                'one'  => 'one trackback',
                'more' => '%d trackbacks',
            ],
            $attr,
            false
        );
    }

    /**
     * tpl:EntryPingData [attributes] : Display trackback RDF information (tpl value)
     *
     * attributes:
     *
     *      - format      (xml|html)  Format (default: html)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryPingData(ArrayObject $attr): string
    {
        $format = !empty($attr['format']) && $attr['format'] == 'xml' ? 'xml' : 'html';

        return "<?php if (dcCore::app()->ctx->posts->trackbacksActive()) { echo dcCore::app()->ctx->posts->getTrackbackData('" . $format . "'); } ?>\n";
    }

    /**
     * tpl:EntryPingLink : Display trackback link (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function EntryPingLink(ArrayObject $attr): string
    {
        return "<?php if (dcCore::app()->ctx->posts->trackbacksActive()) { echo dcCore::app()->ctx->posts->getTrackbackLink(); } ?>\n";
    }

    // Languages
    // ---------

    /**
     * tpl:Languages [attributes] : Languages loop (tpl block)
     *
     * attributes:
     *
     *      - lang        string      Restrict loop on given lang
     *      - order       (desc|asc)  Languages ordering (default: desc)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function Languages(ArrayObject $attr, string $content): string
    {
        $params = "if (!isset(\$params)) \$params = [];\n";

        if (isset($attr['lang'])) {
            $params = "\$params['lang'] = '" . addslashes($attr['lang']) . "';\n";
        }

        if (isset($attr['order']) && preg_match('/^(desc|asc)$/i', (string) $attr['order'])) {
            $params .= "\$params['order'] = '" . (string) $attr['order'] . "';\n ";
        }

        $res = "<?php\n";
        $res .= $params;
        $res .= dcCore::app()->callBehavior(
            'templatePrepareParams',
            ['tag' => 'Languages', 'method' => 'blog::getLangs'],
            $attr,
            $content
        );
        $res .= 'dcCore::app()->ctx->langs = dcCore::app()->blog->getLangs($params); unset($params);' . "\n";
        $res .= "?>\n";

        $res .= '<?php if (dcCore::app()->ctx->langs->count() > 1) : ' .
            'while (dcCore::app()->ctx->langs->fetch()) : ?>' . $content .
            '<?php endwhile; dcCore::app()->ctx->langs = null; endif; ?>';

        return $res;
    }

    /**
     * tpl:LanguagesHeader : First languages result container (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function LanguagesHeader(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->langs->isStart()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:LanguagesFooter : Last languages result container (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function LanguagesFooter(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->langs->isEnd()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:LanguageCode [attributes] : Display language code (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function LanguageCode(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->langs->post_lang') . '; ?>';
    }

    /**
     * tpl:LanguageIfCurrent : Includes content if post language is current language (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function LanguageIfCurrent(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->cur_lang == dcCore::app()->ctx->langs->post_lang) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:LanguageURL [attributes] : Display language URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function LanguageURL(ArrayObject $attr): string
    {
        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->blog->url.dcCore::app()->url->getURLFor("lang",' .
            'dcCore::app()->ctx->langs->post_lang)') . '; ?>';
    }

    /**
     * tpl:FeedLanguage [attributes] : Display feed language (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function FeedLanguage(ArrayObject $attr): string
    {
        $filters = $this->getFilters($attr);

        return
        '<?php if (dcCore::app()->ctx->exists("cur_lang")) ' . "\n" .
        '   { echo ' . sprintf($filters, 'dcCore::app()->ctx->cur_lang') . '; }' . "\n" .
        'elseif (dcCore::app()->ctx->exists("posts") && dcCore::app()->ctx->posts->exists("post_lang")) ' . "\n" .
        '   { echo ' . sprintf($filters, 'dcCore::app()->ctx->posts->post_lang') . '; }' . "\n" .
        'else ' . "\n" .
        '   { echo ' . sprintf($filters, 'dcCore::app()->blog->settings->system->lang') . '; } ?>';
    }

    // Pagination
    // ----------

    /**
     * tpl:Pagination [attributes] : Pagination container (tpl block)
     *
     * attributes:
     *
     *      - no_context  (0|1)       Override test on posts count vs number of posts per page
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function Pagination(ArrayObject $attr, string $content): string
    {
        $params = "<?php\n" .
            '$params = dcCore::app()->ctx->post_params;' . "\n" .
            dcCore::app()->callBehavior(
                'templatePrepareParams',
                [
                    'tag'    => 'Pagination',
                    'method' => 'blog::getPosts',
                ],
                $attr,
                $content
            ) .
            'dcCore::app()->ctx->pagination = dcCore::app()->blog->getPosts($params,true); unset($params);' . "\n" .
            "?>\n";

        if (isset($attr['no_context']) && $attr['no_context']) {
            return $params . $content;
        }

        return
            $params .
            '<?php if (dcCore::app()->ctx->pagination->f(0) > dcCore::app()->ctx->posts->count()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:PaginationCounter [attributes] : Display the number of pages (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PaginationCounter(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'context::PaginationNbPages()') . '; ?>';
    }

    /**
     * tpl:PaginationCurrent [attributes] : Display the number of current page (tpl value)
     *
     * attributes:
     *
     *      - offset      int     Current offset
     *      - any filters         See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PaginationCurrent(ArrayObject $attr): string
    {
        $offset = isset($attr['offset']) ? (int) $attr['offset'] : 0;

        return '<?php echo ' . sprintf($this->getFilters($attr), 'context::PaginationPosition(' . $offset . ')') . '; ?>';
    }

    /**
     * tpl:PaginationIf [attributes] : Includes content depending on pagination test (tpl block)
     *
     * attributes:
     *
     *      - start   (0|1)       First page (if 1) or not (if 0)
     *      - end     (0|1)       Last page (if 1) or not (if 0)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function PaginationIf(ArrayObject $attr, string $content): string
    {
        $if = new ArrayObject();

        if (isset($attr['start'])) {
            $sign = (bool) $attr['start'] ? '' : '!';
            $if[] = $sign . 'context::PaginationStart()';
        }

        if (isset($attr['end'])) {
            $sign = (bool) $attr['end'] ? '' : '!';
            $if[] = $sign . 'context::PaginationEnd()';
        }

        dcCore::app()->callBehavior('tplIfConditions', 'PaginationIf', $attr, $content, $if);

        if (count($if)) {
            return '<?php if(' . implode(' && ', (array) $if) . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    /**
     * tpl:PaginationURL [attributes] : Display link to previous/next page (tpl value)
     *
     * attributes:
     *
     *      - offset      int     Page offset (negative for previous pages), default: 0
     *      - any filters         See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PaginationURL(ArrayObject $attr): string
    {
        $offset = 0;
        if (isset($attr['offset'])) {
            $offset = (int) $attr['offset'];
        }

        return '<?php echo ' . sprintf($this->getFilters($attr), 'context::PaginationURL(' . $offset . ')') . '; ?>';
    }

    // Comments
    // --------

    /**
     * tpl:Comments [attributes] : Comments container (tpl block)
     *
     * attributes:
     *
     *      - with_pings  (0|1)       Include trackbacks
     *      - lastn       int         Restrict the number of comments
     *      - no_context  (0|1)       Override context information
     *      - sortby      (title|selected|author|date|id)    Specify comments sort criteria (default: date), see note 1
     *      - order       (desc|asc)  Result ordering (default: asc)
     *      - age         string      Retrieve comments by maximum age (ex: -2 days, last month, last week)
     *      - no_content  (0|1)       Do not include comments' content
     *
     * Notes:
     *
     *  1) Multiple comma-separated sortby can be specified. Use "?asc" or "?desc" as suffix to provide an order for each sorby
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function Comments(ArrayObject $attr, string $content): string
    {
        $params = '';
        if (empty($attr['with_pings'])) {
            $params .= "\$params['comment_trackback'] = false;\n";
        }

        $lastn = 0;
        if (isset($attr['lastn'])) {
            $lastn = abs((int) $attr['lastn']) + 0;
        }

        if ($lastn > 0) {
            $params .= "\$params['limit'] = " . $lastn . ";\n";
        } else {
            $params .= "if (dcCore::app()->ctx->nb_comment_per_page !== null) { \$params['limit'] = dcCore::app()->ctx->nb_comment_per_page; }\n";
        }

        if (empty($attr['no_context'])) {
            $params .= 'if (dcCore::app()->ctx->posts !== null) { ' .
                "\$params['post_id'] = dcCore::app()->ctx->posts->post_id; " .
                "dcCore::app()->blog->withoutPassword(false);\n" .
                "}\n";
            $params .= 'if (dcCore::app()->ctx->exists("categories")) { ' .
                "\$params['cat_id'] = dcCore::app()->ctx->categories->cat_id; " .
                "}\n";

            $params .= 'if (dcCore::app()->ctx->exists("langs")) { ' .
                "\$params['sql'] = \"AND P.post_lang = '\".dcCore::app()->blog->con->escape(dcCore::app()->ctx->langs->post_lang).\"' \"; " .
                "}\n";
        }

        if (!isset($attr['order'])) {
            $attr['order'] = 'asc';
        }

        $params .= "\$params['order'] = '" . $this->getSortByStr($attr, 'comment') . "';\n";

        if (isset($attr['no_content']) && $attr['no_content']) {
            $params .= "\$params['no_content'] = true;\n";
        }

        if (isset($attr['age'])) {
            $age = static::getAge($attr);
            $params .= !empty($age) ? "@\$params['sql'] .= ' AND P.post_dt > \'" . $age . "\'';\n" : '';
        }

        $res = "<?php\n";
        $res .= dcCore::app()->callBehavior(
            'templatePrepareParams',
            ['tag' => 'Comments', 'method' => 'blog::getComments'],
            $attr,
            $content
        );
        $res .= $params;
        $res .= 'dcCore::app()->ctx->comments = dcCore::app()->blog->getComments($params); unset($params);' . "\n";
        $res .= "if (dcCore::app()->ctx->posts !== null) { dcCore::app()->blog->withoutPassword(true);}\n";

        if (!empty($attr['with_pings'])) {
            $res .= 'dcCore::app()->ctx->pings = dcCore::app()->ctx->comments;' . "\n";
        }

        $res .= "?>\n";

        $res .= '<?php while (dcCore::app()->ctx->comments->fetch()) : ?>' . $content . '<?php endwhile; dcCore::app()->ctx->comments = null; ?>';

        return $res;
    }

    /**
     * tpl:CommentAuthor [attributes] : Comment author (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentAuthor(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comments->comment_author') . '; ?>';
    }

    /**
     * tpl:CommentAuthorDomain : Comment author website domain (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentAuthorDomain(ArrayObject $attr): string
    {
        return '<?php echo preg_replace("#^http(?:s?)://(.+?)/.*$#msu",\'$1\',(string) dcCore::app()->ctx->comments->comment_site); ?>';
    }

    /**
     * tpl:CommentAuthorLink [attributes] : Comment author link (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentAuthorLink(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comments->getAuthorLink()') . '; ?>';
    }

    /**
     * tpl:CommentAuthorMailMD5 : Comment author mail MD5 sum (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentAuthorMailMD5(ArrayObject $attr): string
    {
        return '<?php echo md5(dcCore::app()->ctx->comments->comment_email) ; ?>';
    }

    /**
     * tpl:CommentAuthorURL [attributes] : Comment author URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentAuthorURL($attr)
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comments->getAuthorURL()') . '; ?>';
    }

    /**
     * tpl:CommentContent [attributes] : Comment content (tpl value)
     *
     * attributes:
     *
     *      - absolute_urls   (10)        Convert URLs to absolutes URLs
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentContent(ArrayObject $attr): string
    {
        $urls = '0';
        if (!empty($attr['absolute_urls'])) {
            $urls = '1';
        }

        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comments->getContent(' . $urls . ')') . '; ?>';
    }

    /**
     * tpl:CommentDate [attributes] : Displays comment date (tpl value)
     *
     * attributes:
     *
     *      - format      string      Date format (see dt::str() by default if iso8601 or rfc822 not specified)
     *      - iso8601     (1|0)       If set, display date in ISO 8601 format
     *      - rfc822      (1|0)       If set, display date in RFC 822 format
     *      - upddt       (1|0)       If set, uses the comment update time
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentDate(ArrayObject $attr): string
    {
        $format = '';
        if (!empty($attr['format'])) {
            $format = addslashes($attr['format']);
        }

        $iso8601 = !empty($attr['iso8601']);
        $rfc822  = !empty($attr['rfc822']);
        $type    = (!empty($attr['upddt']) ? 'upddt' : '');

        $filters = $this->getFilters($attr);

        if ($rfc822) {
            return '<?php echo ' . sprintf($filters, "dcCore::app()->ctx->comments->getRFC822Date('" . $type . "')") . '; ?>';
        } elseif ($iso8601) {
            return '<?php echo ' . sprintf($filters, "dcCore::app()->ctx->comments->getISO8601Date('" . $type . "')") . '; ?>';
        }

        return '<?php echo ' . sprintf($filters, "dcCore::app()->ctx->comments->getDate('" . $format . "','" . $type . "')") . '; ?>';
    }

    /**
     * tpl:CommentTime [attributes] : Displays comment date (tpl value)
     *
     * attributes:
     *
     *      - format      string      Time format
     *      - upddt       (1|0)       If set, uses the comment update time
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentTime(ArrayObject $attr): string
    {
        $format = '';
        if (!empty($attr['format'])) {
            $format = addslashes($attr['format']);
        }
        $type = (!empty($attr['upddt']) ? 'upddt' : '');

        return '<?php echo ' .
            sprintf($this->getFilters($attr), "dcCore::app()->ctx->comments->getTime('" . $format . "','" . $type . "')") .
            '; ?>';
    }

    /**
     * tpl:CommentEmail [attributes] : Displays author email (tpl value)
     *
     * attributes:
     *
     *      - spam_protected       (1|0)      Protect email from spam (default: 1)
     *      - any filters                     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentEmail(ArrayObject $attr): string
    {
        $protect = 'true';
        if (isset($attr['spam_protected']) && !$attr['spam_protected']) {
            $protect = 'false';
        }

        return '<?php echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comments->getEmail(' . $protect . ')') . '; ?>';
    }

    /**
     * tpl:CommentEntryTitle [attributes] : Displays title of the comment entry (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentEntryTitle(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comments->post_title') . '; ?>';
    }

    /**
     * tpl:CommentFeedID [attributes] : Displays comment feed ID (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentFeedID(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comments->getFeedID()') . '; ?>';
    }

    /**
     * tpl:CommentID : Displays comment ID (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentID(ArrayObject $attr): string
    {
        return '<?php echo dcCore::app()->ctx->comments->comment_id; ?>';
    }

    /**
     * tpl:CommentIf [attributes] : Includes content depending on comment test (tpl block)
     *
     * attributes:
     *
     *      - is_ping     (0|1)       Tracckback (if 1) or not (if 0)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function CommentIf(ArrayObject $attr, string $content): string
    {
        $if = new ArrayObject();

        if (isset($attr['is_ping'])) {
            $sign = (bool) $attr['is_ping'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->ctx->comments->comment_trackback';
        }

        dcCore::app()->callBehavior('tplIfConditions', 'CommentIf', $attr, $content, $if);

        if (count($if)) {
            return '<?php if(' . implode(' && ', (array) $if) . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    /**
     * tpl:CommentIfFirst [attributes] : Displays value if comment is the first one (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: first)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentIfFirst(ArrayObject $attr): string
    {
        $ret = $attr['return'] ?? 'first';
        $ret = html::escapeHTML($ret);

        return
        '<?php if (dcCore::app()->ctx->comments->index() == 0) { ' .
        "echo '" . addslashes($ret) . "'; } ?>";
    }

    /**
     * tpl:CommentIfMe [attributes] : Displays value if comment is from the entry author (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: me)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentIfMe(ArrayObject $attr): string
    {
        $ret = $attr['return'] ?? 'me';
        $ret = html::escapeHTML($ret);

        return
        '<?php if (dcCore::app()->ctx->comments->isMe()) { ' .
        "echo '" . addslashes($ret) . "'; } ?>";
    }

    /**
     * tpl:CommentIfOdd [attributes] : Displays value if comment is at an odd position (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: odd)
     *      - even        string      Value to display if it is not the case (default: <empty>)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentIfOdd(ArrayObject $attr): string
    {
        $odd = $attr['return'] ?? 'odd';
        $odd = html::escapeHTML($odd);

        $even = $attr['even'] ?? '';
        $even = html::escapeHTML($even);

        return '<?php echo ((dcCore::app()->ctx->comments->index()+1)%2 ? ' .
        '"' . addslashes($odd) . '" : ' .
        '"' . addslashes($even) . '"); ?>';
    }

    /**
     * tpl:CommentIfEven [attributes] : Displays value if comment is at an even position (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: even)
     *      - odd         string      Value to display if it is not the case (default: <empty>)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentIfEven(ArrayObject $attr): string
    {
        $even = $attr['return'] ?? 'even';
        $even = html::escapeHTML($even);

        $odd = $attr['odd'] ?? '';
        $odd = html::escapeHTML($odd);

        return '<?php echo ((dcCore::app()->ctx->comments->index()+1)%2+1 ? ' .
        '"' . addslashes($even) . '" : ' .
        '"' . addslashes($odd) . '"); ?>';
    }

    /**
     * tpl:CommentIP : Displays comment IP (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentIP(ArrayObject $attr): string
    {
        return '<?php echo dcCore::app()->ctx->comments->comment_ip; ?>';
    }

    /**
     * tpl:CommentOrderNumber : Displays comment order in page (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentOrderNumber(ArrayObject $attr): string
    {
        return '<?php echo dcCore::app()->ctx->comments->index()+1; ?>';
    }

    /**
     * tpl:CommentsHeader : First comments result container (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function CommentsHeader(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->comments->isStart()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:CommentsFooter : Last comments result container (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function CommentsFooter(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->comments->isEnd()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:CommentPostURL [attributes] : Displays comment entry URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentPostURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comments->getPostURL()') . '; ?>';
    }

    /**
     * tpl:IfCommentAuthorEmail : Includes content if comment author email is set (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function IfCommentAuthorEmail(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->comments->comment_email) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:CommentHelp : Includes syntax localized mini help (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function CommentHelp(ArrayObject $attr, string $content): string
    {
        return
            "<?php if (dcCore::app()->blog->settings->system->wiki_comments) {\n" .
            "  echo __('Comments can be formatted using a simple wiki syntax.');\n" .
            "} else {\n" .
            "  echo __('HTML code is displayed as text and web addresses are automatically converted.');\n" .
            '} ?>';
    }

    /**
     * tpl:IfCommentPreviewOptional : Includes content if comment preview is optional or currently previewed (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function IfCommentPreviewOptional(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->blog->settings->system->comment_preview_optional || (dcCore::app()->ctx->comment_preview !== null && dcCore::app()->ctx->comment_preview["preview"])) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:IfCommentPreview : Includes content if comment is being previewed (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function IfCommentPreview(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->comment_preview !== null && dcCore::app()->ctx->comment_preview["preview"]) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:CommentPreviewName [attributes] : Displays Author name for the previewed comment (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentPreviewName(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comment_preview["name"]') . '; ?>';
    }

    /**
     * tpl:CommentPreviewEmail [attributes] : Displays Author email for the previewed comment (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentPreviewEmail(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comment_preview["mail"]') . '; ?>';
    }

    /**
     * tpl:CommentPreviewSite [attributes] : Displays Author site for the previewed comment (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentPreviewSite(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->comment_preview["site"]') . '; ?>';
    }

    /**
     * tpl:CommentPreviewContent [attributes] : Displays content of the previewed comment (tpl value)
     *
     * attributes:
     *
     *      - raw         (1|0)   Display comment in raw content
     *      - any filters         See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentPreviewContent(ArrayObject $attr): string
    {
        if (!empty($attr['raw'])) {
            $content = 'dcCore::app()->ctx->comment_preview["rawcontent"]';
        } else {
            $content = 'dcCore::app()->ctx->comment_preview["content"]';
        }

        return '<?php echo ' . sprintf($this->getFilters($attr), $content) . '; ?>';
    }

    /**
     * tpl:CommentPreviewCheckRemember : checkbox attribute for "remember me" (same value as before preview) (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function CommentPreviewCheckRemember(ArrayObject $attr): string
    {
        return
            "<?php if (dcCore::app()->ctx->comment_preview['remember']) { echo ' checked=\"checked\"'; } ?>";
    }

    // Trackbacks
    // ----------

    /**
     * tpl:PingBlogName [attributes] : Displays trackback blog name (tpl value)
     *
     * attributes:
     *
     *      - any filters         See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingBlogName(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->pings->comment_author') . '; ?>';
    }

    /**
     * tpl:PingContent [attributes] : Displays trackback content (tpl value)
     *
     * attributes:
     *
     *      - any filters         See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingContent(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->pings->getTrackbackContent()') . '; ?>';
    }

    /**
     * tpl:PingDate [attributes] : Displays trackback date (tpl value)
     *
     * attributes:
     *
     *      - format      string      Date format (see dt::str() by default if iso8601 or rfc822 not specified)
     *      - iso8601     (1|0)       If set, display date in ISO 8601 format
     *      - rfc822      (1|0)       If set, display date in RFC 822 format
     *      - upddt       (1|0)       If set, uses the ping update time
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingDate(ArrayObject $attr): string
    {
        $format = '';
        if (!empty($attr['format'])) {
            $format = addslashes($attr['format']);
        }

        $iso8601 = !empty($attr['iso8601']);
        $rfc822  = !empty($attr['rfc822']);
        $type    = (!empty($attr['upddt']) ? 'upddt' : '');

        $filters = $this->getFilters($attr);

        if ($rfc822) {
            return '<?php echo ' . sprintf($filters, "dcCore::app()->ctx->pings->getRFC822Date('" . $type . "')") . '; ?>';
        } elseif ($iso8601) {
            return '<?php echo ' . sprintf($filters, "dcCore::app()->ctx->pings->getISO8601Date('" . $type . "')") . '; ?>';
        }

        return '<?php echo ' . sprintf($filters, "dcCore::app()->ctx->pings->getDate('" . $format . "','" . $type . "')") . '; ?>';
    }

    /**
     * tpl:PingTime [attributes] : Displays trackback date (tpl value)
     *
     * attributes:
     *
     *      - format      string      Time format
     *      - upddt       (1|0)       If set, uses the ping update time
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingTime(ArrayObject $attr): string
    {
        $format = '';
        if (!empty($attr['format'])) {
            $format = addslashes($attr['format']);
        }
        $type = (!empty($attr['upddt']) ? 'upddt' : '');

        return '<?php echo ' .
            sprintf($this->getFilters($attr), "dcCore::app()->ctx->pings->getTime('" . $format . "','" . $type . "')") . '; ?>';
    }

    /**
     * tpl:PingEntryTitle [attributes] : Displays trackback entry title (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingEntryTitle(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->pings->post_title') . '; ?>';
    }

    /**
     * tpl:PingFeedID [attributes] : Displays trackback feed ID (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingFeedID(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->pings->getFeedID()') . '; ?>';
    }

    /**
     * tpl:PingID : Displays ping ID (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingID(ArrayObject $attr): string
    {
        return '<?php echo dcCore::app()->ctx->pings->comment_id; ?>';
    }

    /**
     * tpl:PingIfFirst [attributes] : Displays value if trackback is the first one (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: first)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingIfFirst(ArrayObject $attr): string
    {
        $ret = $attr['return'] ?? 'first';
        $ret = html::escapeHTML($ret);

        return
        '<?php if (dcCore::app()->ctx->pings->index() == 0) { ' .
        "echo '" . addslashes($ret) . "'; } ?>";
    }

    /**
     * tpl:PingIfOdd [attributes] : Displays value if trackback is at an odd position (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: odd)
     *      - even        string      Value to display if it is not the case (default: <empty>)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingIfOdd(ArrayObject $attr): string
    {
        $odd = $attr['return'] ?? 'odd';
        $odd = html::escapeHTML($odd);

        $even = $attr['even'] ?? '';
        $even = html::escapeHTML($even);

        return '<?php echo ((dcCore::app()->ctx->pings->index()+1)%2 ? ' .
        '"' . addslashes($odd) . '" : ' .
        '"' . addslashes($even) . '"); ?>';
    }

    /**
     * tpl:PingIfEven [attributes] : Displays value if trackback is at an even position (tpl value)
     *
     * attributes:
     *
     *      - return      string      Value to display if it is the case (default: even)
     *      - odd         string      Value to display if it is not the case (default: <empty>)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingIfEven(ArrayObject $attr): string
    {
        $even = $attr['return'] ?? 'even';
        $even = html::escapeHTML($even);

        $odd = $attr['odd'] ?? '';
        $odd = html::escapeHTML($odd);

        return '<?php echo ((dcCore::app()->ctx->pings->index()+1)%2+1 ? ' .
        '"' . addslashes($even) . '" : ' .
        '"' . addslashes($odd) . '"); ?>';
    }

    /**
     * tpl:PingIP : Displays ping author IP (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingIP(ArrayObject $attr): string
    {
        return '<?php echo dcCore::app()->ctx->pings->comment_ip; ?>';
    }

    /**
     * tpl:PingNoFollow : Displays 'rel="nofollow"' if set in blog (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingNoFollow(ArrayObject $attr): string
    {
        return
            '<?php if(dcCore::app()->blog->settings->system->comments_nofollow) { ' .
            'echo \' rel="nofollow"\';' .
            '} ?>';
    }

    /**
     * tpl:PingOrderNumber : Displays trackback order in page, 1 based (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingOrderNumber(ArrayObject $attr): string
    {
        return '<?php echo dcCore::app()->ctx->pings->index()+1; ?>';
    }

    /**
     * tpl:PingPostURL [attributes] : Displays trackback entry URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingPostURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->pings->getPostURL()') . '; ?>';
    }

    /**
     * tpl:Pings [attributes] : Pings container (tpl block)
     *
     * attributes:
     *
     *      - lastn       int         Restrict the number of pings
     *      - no_context  (0|1)       Override context information
     *      - order       (desc|asc)  Result ordering (default: asc)
     *      - no_content  (0|1)       Do not include pings' content
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function Pings(ArrayObject $attr, string $content): string
    {
        $params = 'if (dcCore::app()->ctx->posts !== null) { ' .
            "\$params['post_id'] = dcCore::app()->ctx->posts->post_id; " .
            "dcCore::app()->blog->withoutPassword(false);\n" .
            "}\n";

        $params .= "\$params['comment_trackback'] = true;\n";

        $lastn = 0;
        if (isset($attr['lastn'])) {
            $lastn = abs((int) $attr['lastn']) + 0;
        }

        if ($lastn > 0) {
            $params .= "\$params['limit'] = " . $lastn . ";\n";
        } else {
            $params .= "if (dcCore::app()->ctx->nb_comment_per_page !== null) { \$params['limit'] = dcCore::app()->ctx->nb_comment_per_page; }\n";
        }

        if (empty($attr['no_context'])) {
            $params .= 'if (dcCore::app()->ctx->exists("categories")) { ' .
                "\$params['cat_id'] = dcCore::app()->ctx->categories->cat_id; " .
                "}\n";

            $params .= 'if (dcCore::app()->ctx->exists("langs")) { ' .
                "\$params['sql'] = \"AND P.post_lang = '\".dcCore::app()->blog->con->escape(dcCore::app()->ctx->langs->post_lang).\"' \"; " .
                "}\n";
        }

        $order = 'asc';
        if (isset($attr['order']) && preg_match('/^(desc|asc)$/i', (string) $attr['order'])) {
            $order = (string) $attr['order'];
        }

        $params .= "\$params['order'] = 'comment_dt " . $order . "';\n";

        if (isset($attr['no_content']) && $attr['no_content']) {
            $params .= "\$params['no_content'] = true;\n";
        }

        $res = "<?php\n";
        $res .= $params;
        $res .= dcCore::app()->callBehavior(
            'templatePrepareParams',
            ['tag' => 'Pings', 'method' => 'blog::getComments'],
            $attr,
            $content
        );
        $res .= 'dcCore::app()->ctx->pings = dcCore::app()->blog->getComments($params); unset($params);' . "\n";
        $res .= "if (dcCore::app()->ctx->posts !== null) { dcCore::app()->blog->withoutPassword(true);}\n";
        $res .= "?>\n";

        $res .= '<?php while (dcCore::app()->ctx->pings->fetch()) : ?>' . $content . '<?php endwhile; dcCore::app()->ctx->pings = null; ?>';

        return $res;
    }

    /**
     * tpl:PingsHeader : First pings result container (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function PingsHeader(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->pings->isStart()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:PingsFooter : Last pings result container (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function PingsFooter(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->pings->isEnd()) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:PingTitle [attributes] : Displays trackback title (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingTitle(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->pings->getTrackbackTitle()') . '; ?>';
    }

    /**
     * tpl:PingAuthorURL [attributes] : Displays trackback author URL (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function PingAuthorURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'dcCore::app()->ctx->pings->getAuthorURL()') . '; ?>';
    }

    // System
    // ------

    /**
     * tpl:SysBehavior [attributes] : Call a given behavior (tpl value)
     *
     * attributes:
     *
     *      - behavior        string      Behavior to call
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function SysBehavior(ArrayObject $attr): string
    {
        if (!isset($attr['behavior'])) {
            return '';
        }

        $behavior = addslashes($attr['behavior']);

        return
            '<?php if (dcCore::app()->hasBehavior(\'' . $behavior . '\')) { ' .
            'dcCore::app()->callBehavior(\'' . $behavior . '\',dcCore::app(),dcCore::app()->ctx);' .
            '} ?>';
    }

    /**
     * tpl:SysIf [attributes] : Includes content depending on system test (tpl block)
     *
     * attributes:
     *
     *      - categories      (0|1)                   Categories are set in current context (if 1) or not (if 0)
     *      - posts           (0|1)                   Posts are set in current context (if 1) or not (if 0)
     *      - blog_lang       string                  Blog language is the one given in parameter, see note 1
     *      - current_tpl     string                  Current template is the one given in paramater, see note 1
     *      - current_mode    string                  Current URL mode is the one given in parameter, see note 1
     *      - has_tpl         string                  Named template exists, see note 1
     *      - has_tag         string                  Named template block or value exists, see note 1
     *      - blog_id         string                  Current blog ID is the one given in parameter, see note 1
     *      - comments_active (0|1)                   Comments are enabled blog-wide
     *      - pings_active    (0|1)                   Trackbacks are enabled blog-wide
     *      - wiki_comments   (0|1)                   Wiki syntax is enabled for comments
     *      - search_count    (=|!|>=|<=|>|<) int     Search count valids condition
     *      - jquery_needed   (0|1)                   jQuery javascript library is requested (if 1) or not (if 0)
     *      - operator        (and|or)                Combination of conditions, if more than 1 specifiec (default: and)
     *
     * Notes:
     *
     *  1) Prefix with a ! to reverse test
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function SysIf(ArrayObject $attr, string $content): string
    {
        $if = new ArrayObject();

        $operator = isset($attr['operator']) ? static::getOperator($attr['operator']) : '&&';

        if (isset($attr['categories'])) {
            $sign = (bool) $attr['categories'] ? '!' : '=';
            $if[] = 'dcCore::app()->ctx->categories ' . $sign . '== null';
        }

        if (isset($attr['posts'])) {
            $sign = (bool) $attr['posts'] ? '!' : '=';
            $if[] = 'dcCore::app()->ctx->posts ' . $sign . '== null';
        }

        if (isset($attr['blog_lang'])) {
            $sign = '=';
            if (substr($attr['blog_lang'], 0, 1) == '!') {
                $sign              = '!';
                $attr['blog_lang'] = substr($attr['blog_lang'], 1);
            }
            $if[] = 'dcCore::app()->blog->settings->system->lang ' . $sign . "= '" . addslashes($attr['blog_lang']) . "'";
        }

        if (isset($attr['current_tpl'])) {
            $sign = '=';
            if (substr($attr['current_tpl'], 0, 1) == '!') {
                $sign                = '!';
                $attr['current_tpl'] = substr($attr['current_tpl'], 1);
            }
            $if[] = 'dcCore::app()->ctx->current_tpl ' . $sign . "= '" . addslashes($attr['current_tpl']) . "'";
        }

        if (isset($attr['current_mode'])) {
            $sign = '=';
            if (substr($attr['current_mode'], 0, 1) == '!') {
                $sign                 = '!';
                $attr['current_mode'] = substr($attr['current_mode'], 1);
            }
            $if[] = 'dcCore::app()->url->type ' . $sign . "= '" . addslashes($attr['current_mode']) . "'";
        }

        if (isset($attr['has_tpl'])) {
            $sign = '';
            if (substr($attr['has_tpl'], 0, 1) == '!') {
                $sign            = '!';
                $attr['has_tpl'] = substr($attr['has_tpl'], 1);
            }
            $if[] = $sign . "dcCore::app()->tpl->getFilePath('" . addslashes($attr['has_tpl']) . "') !== false";
        }

        if (isset($attr['has_tag'])) {
            $sign = 'true';
            if (substr($attr['has_tag'], 0, 1) == '!') {
                $sign            = 'false';
                $attr['has_tag'] = substr($attr['has_tag'], 1);
            }
            $if[] = "dcCore::app()->tpl->tagExists('" . addslashes($attr['has_tag']) . "') === " . $sign;
        }

        if (isset($attr['blog_id'])) {
            $sign = '';
            if (substr($attr['blog_id'], 0, 1) == '!') {
                $sign            = '!';
                $attr['blog_id'] = substr($attr['blog_id'], 1);
            }
            $if[] = $sign . "(dcCore::app()->blog->id == '" . addslashes($attr['blog_id']) . "')";
        }

        if (isset($attr['comments_active'])) {
            $sign = (bool) $attr['comments_active'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->blog->settings->system->allow_comments';
        }

        if (isset($attr['pings_active'])) {
            $sign = (bool) $attr['pings_active'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->blog->settings->system->allow_trackbacks';
        }

        if (isset($attr['wiki_comments'])) {
            $sign = (bool) $attr['wiki_comments'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->blog->settings->system->wiki_comments';
        }

        if (isset($attr['search_count']) && preg_match('/^((=|!|&gt;|&lt;)=|(&gt;|&lt;))\s*\d+$/', trim((string) $attr['search_count']))) {
            $if[] = '(isset(dcCore::app()->public->search_count) && dcCore::app()->public->search_count ' . html::decodeEntities($attr['search_count']) . ')';
        }

        if (isset($attr['jquery_needed'])) {
            $sign = (bool) $attr['jquery_needed'] ? '' : '!';
            $if[] = $sign . 'dcCore::app()->blog->settings->system->jquery_needed';
        }

        dcCore::app()->callBehavior('tplIfConditions', 'SysIf', $attr, $content, $if);

        if (count($if)) {
            return '<?php if(' . implode(' ' . $operator . ' ', (array) $if) . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    /**
     * tpl:SysIfCommentPublished : Includes content if comment has been published (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function SysIfCommentPublished(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (!empty($_GET[\'pub\'])) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:SysIfCommentPending : Includes content if comment is pending after submission (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function SysIfCommentPending(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (isset($_GET[\'pub\']) && $_GET[\'pub\'] == 0) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:SysFeedSubtitle [attributes] : Displays feed subtitle (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function SysFeedSubtitle(ArrayObject $attr): string
    {
        return '<?php if (dcCore::app()->ctx->feed_subtitle !== null) { echo ' .
            sprintf($this->getFilters($attr), 'dcCore::app()->ctx->feed_subtitle') . ';} ?>';
    }

    /**
     * tpl:SysIfFormError : Includes content if an error has been detected after form submission (tpl block)
     *
     * @param      ArrayObject    $attr     The attributes
     * @param      string         $content  The content
     *
     * @return     string
     */
    public function SysIfFormError(ArrayObject $attr, string $content): string
    {
        return
            '<?php if (dcCore::app()->ctx->form_error !== null) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * tpl:SysFormError : Displays form error (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function SysFormError(ArrayObject $attr): string
    {
        return
            '<?php if (dcCore::app()->ctx->form_error !== null) { echo dcCore::app()->ctx->form_error; } ?>';
    }

    /**
     * tpl:SysPoweredBy : Displays localized powered by (tpl value)
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function SysPoweredBy(ArrayObject $attr): string
    {
        return
            '<?php printf(__("Powered by %s"),"<a href=\"https://dotclear.org/\">Dotclear</a>"); ?>';
    }

    /**
     * tpl:SysSearchString [attributes] : Displays search string if any (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function SysSearchString(ArrayObject $attr): string
    {
        $string = $attr['string'] ?? '%1$s';

        return '<?php if (isset(dcCore::app()->public->search)) { echo sprintf(__(\'' . $string . '\'),' .
            sprintf($this->getFilters($attr), 'dcCore::app()->public->search') . ',dcCore::app()->public->search_count);} ?>';
    }

    /**
     * tpl:SysSelfURI [attributes] : Displays self URI (tpl value)
     *
     * attributes:
     *
     *      - any filters     See self::getFilters()
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function SysSelfURI(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf($this->getFilters($attr), 'http::getSelfURI()') . '; ?>';
    }

    /**
     * tpl:else : Displays else: statement (tpl value)
     *
     * May be used inside a tpl:If… block
     *
     * @param      ArrayObject    $attr     The attributes
     *
     * @return     string
     */
    public function GenericElse(ArrayObject $attr): string
    {
        return '<?php else: ?>';
    }
}
