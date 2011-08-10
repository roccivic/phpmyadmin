<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
class navigation {
    /**
     * Variables
     */
    private $buffer;
    private $pos;

    /**
     * Public methods
     */
    public function __construct()
    {
        $this->buffer = '';
        // Select the database if there is only one on current server
        if ($GLOBALS['server'] && ! strlen($GLOBALS['db'])) {
            $GLOBALS['db'] = $GLOBALS['pma']->databases->getSingleItem();
        }
        if (empty($GLOBALS['query_url'])) {
            // avoid putting here $db because it could display a db name
            // to which the next user does not have access
            $GLOBALS['query_url'] = PMA_generate_common_url();
        }
        // Keep the offset of the db list in session before closing it
        if (! isset($_SESSION['tmp_user_values']['navi_limit_offset'])) {
            $_SESSION['tmp_user_values']['navi_limit_offset'] = 0;
        }
        $this->pos = $_SESSION['tmp_user_values']['navi_limit_offset'];
        if (isset($_REQUEST['pos'])) {
            $pos = (int) $_REQUEST['pos'];
            $_SESSION['tmp_user_values']['navi_limit_offset'] = $pos;
            $this->pos = $pos;
        }
        // free the session file, for the other frames to be loaded
        // but only if debugging is not enabled
        if (empty($_SESSION['debug'])) {
            session_write_close();
        }
        $this->render();
    }

    public function __set($a, $b)
    {
        return false;
    }

    public function __get($a)
    {
        return false;
    }

    public function render()
    {
        $this->requests();
        $this->buffer .= $this->header();
        $this->buffer .= $this->logo();
        $this->buffer .= $this->links();
        $this->buffer .= $this->serverChoice();
        $this->buffer .= $this->recent();
        $this->buffer .= $this->tree();
        echo $this->buffer;
        echo '</body></html>';
        exit;
    }

    /**
     * Private methods
     */
    private function requests()
    {
        // Check if it is an ajax request to reload the recent tables list.
        if ($GLOBALS['is_ajax_request'] && isset($_REQUEST['recent_table'])) {
            PMA_ajaxResponse('', true, array('options' => PMA_RecentTable::getInstance()->getHtmlSelectOption()));
        }
        // Check if it is an ajax request to load a part of the navigation tree
        if ($GLOBALS['is_ajax_request'] && isset($_REQUEST['getTree'])) {
            $this->tree(true);
        }
    }

    private function header()
    {
        // Display the frame
        // xml declaration moves IE into quirks mode, making much trouble with CSS
        /* echo '<?xml version="1.0" encoding="utf-8"?>'; */
        $retval  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"' . PHP_EOL;
        $retval .= '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . PHP_EOL;
        $retval .= '<html xmlns="http://www.w3.org/1999/xhtml"' . PHP_EOL;
        $retval .= '      xml:lang="' . $GLOBALS['available_languages'][$GLOBALS['lang']][1] . '"' . PHP_EOL;
        $retval .= '      lang="' . $GLOBALS['available_languages'][$GLOBALS['lang']][1] . '"' . PHP_EOL;
        $retval .= '      dir="' . $GLOBALS['text_dir'] . '">' . PHP_EOL;
        $retval .= '<head>' . PHP_EOL;
        $retval .= '    <link rel="icon" href="./favicon.ico" type="image/x-icon" />' . PHP_EOL;
        $retval .= '    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />' . PHP_EOL;
        $retval .= '    <title>phpMyAdmin</title>' . PHP_EOL;
        $retval .= '    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . PHP_EOL;
        $retval .= '    <base target="frame_content" />' . PHP_EOL;
        $retval .= '    <link rel="stylesheet" type="text/css"' . PHP_EOL;
        $retval .= '          href="phpmyadmin.css.php?' . PMA_generate_common_url('', '');
        $retval .= '&amp;js_frame=left&amp;nocache=' . $GLOBALS['PMA_Config']->getThemeUniqueValue() . '" />' . PHP_EOL;
        $retval .= PMA_includeJS('jquery/jquery-1.6.2.js');
        $retval .= PMA_includeJS('jquery/jquery-ui-1.8.custom.js');
        $retval .= PMA_includeJS('navigation.js');
        $retval .= PMA_includeJS('functions.js');
        $retval .= PMA_includeJS('messages.php');
        // remove horizontal scroll bar bug in IE 6 by forcing a vertical scroll bar
        $retval .= '    <!--[if IE 6]>' . PHP_EOL;
        $retval .= '    <style type="text/css">' . PHP_EOL;
        $retval .= '        /* <![CDATA[ */' . PHP_EOL;
        $retval .= '        html { overflow-y: scroll; }' . PHP_EOL;
        $retval .= '        /* ]]> */' . PHP_EOL;
        $retval .= '    </style>' . PHP_EOL;
        $retval .= '    <![endif]-->' . PHP_EOL;
        $retval .= '</head>' . PHP_EOL;
        $retval .= '<body id="body_leftFrame">' . PHP_EOL;
        return $retval;
    }

    private function logo()
    {
        $retval = '<!-- LOGO START -->' . PHP_EOL;
        // display Logo, depending on $GLOBALS['cfg']['LeftDisplayLogo']
        if ($GLOBALS['cfg']['LeftDisplayLogo']) {
            $logo = 'phpMyAdmin';
            if (@file_exists($GLOBALS['pmaThemeImage'] . 'logo_left.png')) {
                $logo = '<img src="' . $GLOBALS['pmaThemeImage'] . 'logo_left.png" '
                    . 'alt="' . $logo . '" id="imgpmalogo" />';
            } elseif (@file_exists($GLOBALS['pmaThemeImage'] . 'pma_logo2.png')) {
                $logo = '<img src="' . $GLOBALS['pmaThemeImage'] . 'pma_logo2.png" '
                    . 'alt="' . $logo . '" id="imgpmalogo" />';
            }
            $retval .= '<div id="pmalogo">' . PHP_EOL;
            if ($GLOBALS['cfg']['LeftLogoLink']) {
                $retval .= '    <a href="' . htmlspecialchars($GLOBALS['cfg']['LeftLogoLink']);
                switch ($GLOBALS['cfg']['LeftLogoLinkWindow']) {
                    case 'new':
                        $retval .= '" target="_blank"';
                        break;
                    case 'main':
                        // do not add our parameters for an external link
                        if (substr(strtolower($GLOBALS['cfg']['LeftLogoLink']), 0, 4) !== '://') {
                            $retval .= '?' . $GLOBALS['query_url'] . '" target="frame_content"';
                        } else {
                            $retval .= '" target="_blank"';
                        }
                }
                $retval .= '>' . PHP_EOL;
                $retval .= '        ' . $logo . PHP_EOL;
                $retval .= '    </a>' . PHP_EOL;
            } else {
                $retval .= $logo . PHP_EOL;
            }
            $retval .= '</div>' . PHP_EOL;
        }
        $retval .= '<!-- LOGO END -->' . PHP_EOL;
        return $retval;
    }

    private function links()
    {
        $retval = '<!-- LINKS START -->' . PHP_EOL;
        $retval .= '<div id="leftframelinks">' . PHP_EOL;
        $retval .= '    <a href="main.php?' . $GLOBALS['query_url'] . '" title="' . __('Home') . '">';
        if ($GLOBALS['cfg']['NavigationBarIconic']) {
            $retval .= '<img class="icon ic_b_home" src="themes/dot.gif" alt="' . __('Home') . '" /></a>' . PHP_EOL;
        } else {
            $retval .= __('Home') . '</a>' . PHP_EOL;
            $retval .= '    <br />' . PHP_EOL;
        }
        // if we have chosen server
        if ($GLOBALS['server'] != 0) {
            // Logout for advanced authentication
            if ($GLOBALS['cfg']['Server']['auth_type'] != 'config') {
                $retval .= '    <a href="index.php?' . $GLOBALS['query_url'] . '&amp;old_usr=';
                $retval .= urlencode($GLOBALS['PHP_AUTH_USER']) . '" target="_parent"';
                $retval .= ' title="' . __('Log out') . '" >';
                if ($GLOBALS['cfg']['NavigationBarIconic']) {
                       $retval .= '<img class="icon ic_s_loggoff" src="themes/dot.gif" alt="' . __('Log out') . '" /></a>' . PHP_EOL;
                } else {
                    $retval .= __('Log out') . '</a>' . PHP_EOL;
                    $retval .= '    <br />' . PHP_EOL;
                }
            }
            $retval .= '    <a href="querywindow.php?' . PMA_generate_common_url($GLOBALS['db'], $GLOBALS['table']) . '&amp;no_js=true"';
            $retval .= ' title="' . __('Query window') . '"';
            $retval .= ' onclick="javascript:if (window.parent.open_querywindow()) return false;">';
            if ($GLOBALS['cfg']['NavigationBarIconic']) {
                $retval .= '<img class="icon ic_b_selboard" src="themes/dot.gif" alt="' . __('Query window') . '" /></a>' . PHP_EOL;
            } else {
                $retval .= __('Query window') . '</a>' . PHP_EOL;
                $retval .= '    <br />' . PHP_EOL;
            }
        }
        $retval .= '    <a href="Documentation.html" target="documentation"';
        $retval .= ' title="' . __('phpMyAdmin documentation') . '" >';
        if ($GLOBALS['cfg']['NavigationBarIconic']) {
            $retval .= '<img class="icon ic_b_docs" src="themes/dot.gif"';
            $retval .= ' alt="' . __('phpMyAdmin documentation') . '" /></a>' . PHP_EOL;
        } else {
            $retval .= __('phpMyAdmin documentation') . '</a>' . PHP_EOL;
            $retval .= '    <br />' . PHP_EOL;
        }
        if ($GLOBALS['cfg']['NavigationBarIconic']) {
            $retval .= '    ' . PMA_showMySQLDocu('', '', true) . PHP_EOL;
        } else {
            // PMA_showMySQLDocu always spits out an icon,
            // we just replace it with some perl regexp.
            $link = preg_replace(
                '/<img[^>]+>/i',
                __('Documentation'),
                PMA_showMySQLDocu('', '', true)
            );
            $retval .= '    ' . $link . PHP_EOL;
            $retval .= '    <br />' . PHP_EOL;
        }
        $params = array('uniqid' => uniqid());
        if (!empty($GLOBALS['db'])) {
            $params['db'] = $GLOBALS['db'];
        }
        $retval .= '    <a href="navigation.php?' . PMA_generate_common_url($params) . '" target="frame_navigation">';
        if ($GLOBALS['cfg']['NavigationBarIconic']) {
            $retval .= '<img class="icon ic_s_reload" src="themes/dot.gif"';
            $retval .= ' title="' . __('Reload navigation frame') . '"';
            $retval .= ' alt="' . __('Reload navigation frame') . '" /></a>' . PHP_EOL;
        } else {
            $retval .= __('Reload navigation frame') . '</a>' . PHP_EOL;
            $retval .= '    <br />' . PHP_EOL;
        }
        $retval .= '</div>' . PHP_EOL;
        $retval .= '<!-- LINKS ENDS -->' . PHP_EOL;
        return $retval;
    }

    /**
     * Displays the MySQL servers choice form
     */
    private function serverChoice()
    {
        $retval = '';
        if ($GLOBALS['cfg']['LeftDisplayServers']) {
            require_once './libraries/select_server.lib.php';
            $retval .= '<!-- SERVER CHOICE START -->' . PHP_EOL;
            $retval .= '<div id="serverChoice">' . PHP_EOL;
            $retval .= PMA_select_server(true, true) . PHP_EOL;
            $retval .= '</div>' . PHP_EOL;
            $retval .= '<!-- SERVER CHOICE END -->' . PHP_EOL;
        }
        return $retval;
    }

    private function recent()
    {
        $retval = '';
        // display recently used tables
        if ($GLOBALS['cfg']['LeftRecentTable'] > 0) {
            $retval .= '<!-- RECENT START -->' . PHP_EOL;
            $retval .= '<div id="recentTableList">' . PHP_EOL;
            $retval .= '    <form method="post" action="index.php" target="_parent">' . PHP_EOL;
            $retval .= '        ' . PMA_generate_common_hidden_inputs() . PHP_EOL;
            $retval .= PMA_RecentTable::getInstance()->getHtmlSelect() . PHP_EOL;
            $retval .= '        <noscript>' . PHP_EOL;
            $retval .= '            <input type="submit" name="Go" value="' . __('Go') . '" />' . PHP_EOL;
            $retval .= '        </noscript>' . PHP_EOL;
            $retval .= '    </form>' . PHP_EOL;
            $retval .= '</div>' . PHP_EOL;
            $retval .= '<!-- RECENT END -->' . PHP_EOL;
        }
        return $retval;
    }

    private function tree($ajax = false)
    {
        global $server, $token;

        /* Init */
        $tree      = new CollapsibleTree();
        $separator = ! empty($GLOBALS['cfg']['LeftFrameDBTree']) ? $GLOBALS['cfg']['LeftFrameDBSeparator'] : '';
        $tree->setRootSeparator($separator, 1000);

        /* Databases */
        $query = "SELECT `SCHEMA_NAME` AS `name` FROM `INFORMATION_SCHEMA`.`SCHEMATA`";
        if ($ajax) {
            $databases = $tree->addList($query, true);
        } else {
            $databases = $tree->addList($query, true, 0, $this->pos, $GLOBALS['cfg']['MaxDbList']);
        }
        $tree->setIcon(PMA_getIcon('s_db.png'), $databases);
        $tree->setLinks(
            array(
                'text' => 'db_structure.php?server=' . $server . '&db=%1$s&token=' . $token,
                'icon' => 'db_operations.php?server=' . $server . '&db=%1$s&token=' . $token
            ),
            $databases
        );

        /* Tables */
        $table_container = $tree->addContainer(
            __('Tables'),
            $databases,
            $GLOBALS['cfg']['LeftFrameTableSeparator'],
            (int)($GLOBALS['cfg']['LeftFrameTableLevel'])
        );
        $tree->setIcon(PMA_getIcon('b_browse.png'), $table_container);
        $tree->setLinks(
            array(
                'text' => 'db_structure.php?server=' . $server . '&db=%1$s&token=' . $token,
                'icon' => 'db_structure.php?server=' . $server . '&db=%1$s&token=' . $token,
            ),
            $table_container
        );
        $query = "SELECT `TABLE_NAME` AS `name`,`TABLE_SCHEMA` AS `parent_1` FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_TYPE`!='VIEW'";
        $tables = $tree->addList($query, true, $table_container);
        $tree->setIcon(PMA_getIcon('b_browse.png'), $tables);
        $tree->setLinks(
        array(
                'text' => 'sql.php?server=' . $server . '&db=%2$s&table=%1$s&pos=0&token=' . $token,
                'icon' => 'tbl_structure.php?server=' . $server . '&db=%2$s&table=%1$s&token=' . $token
            ),
            $tables
        );

        /* Views */
        $views_container = $tree->addContainer(
            __('Views'),
            $databases,
            $GLOBALS['cfg']['LeftFrameTableSeparator'],
            (int)($GLOBALS['cfg']['LeftFrameTableLevel'])
        );
        $tree->setIcon(PMA_getIcon('b_views.png'), $views_container);
        $tree->setLinks(
            array(
                'text' => 'db_structure.php?server=' . $server . '&db=%1$s&token=' . $token,
                'icon' => 'db_structure.php?server=' . $server . '&db=%1$s&token=' . $token
            ),
            $views_container
        );
        $query = "SELECT `TABLE_NAME` AS `name`,`TABLE_SCHEMA` AS `parent_1` FROM `INFORMATION_SCHEMA`.`VIEWS`";
        $views = $tree->addList($query, true, $views_container);
        $tree->setIcon(PMA_getIcon('b_views.png'), $views);
        $tree->setLinks(
            array(
                'text' => 'sql.php?server=' . $server . '&db=%2$s&table=%1$s&pos=0&token=' . $token,
                'icon' => 'tbl_structure.php?server=' . $server . '&db=%2$s&table=%1$s&token=' . $token
            ),
            $views
        );

        /* Routines */
        $routines_container = $tree->addContainer(__('Routines'), $databases);
        $tree->setIcon(PMA_getIcon('b_routines.png'), $routines_container);
        $tree->setLinks(
            array(
                'text' => 'db_routines.php?server=' . $server . '&db=%1$s&token=' . $token,
                'icon' => 'db_routines.php?server=' . $server . '&db=%1$s&token=' . $token
            ),
            $routines_container
        );
        $query = "SELECT `ROUTINE_NAME` AS `name`,`ROUTINE_SCHEMA` AS `parent_1` FROM `INFORMATION_SCHEMA`.`ROUTINES`";
        $routines = $tree->addList($query, true, $routines_container);
        $tree->setIcon(PMA_getIcon('b_routines.png'), $routines);
        $tree->setLinks(
            array(
                'text' => 'db_routines.php?server=' . $server . '&db=%2$s&item_name=%1$s&edit_item=1&token=' . $token,
                'icon' => 'db_routines.php?server=' . $server . '&db=%2$s&item_name=%1$s&export_item=1&token=' . $token,
            ),
            $routines
        );

        /* Events */
        $events_container = $tree->addContainer(__('Events'), $databases);
        $tree->setIcon(PMA_getIcon('b_events.png'), $events_container);
        $tree->setLinks(
            array(
                'text' => 'db_events.php?server=' . $server . '&db=%1$s&token=' . $token,
                'icon' => 'db_events.php?server=' . $server . '&db=%1$s&token=' . $token
            ),
            $events_container
        );
        $query = "SELECT `EVENT_NAME` AS `name`,`EVENT_SCHEMA` AS `parent_1` FROM `INFORMATION_SCHEMA`.`EVENTS`";
        $events = $tree->addList($query, true, $events_container);
        $tree->setIcon(PMA_getIcon('b_events.png'), $events);
        $tree->setLinks(
            array(
                'text' => 'db_events.php?server=' . $server . '&db=%2$s&item_name=%1$s&edit_item=1&token=' . $token,
                'icon' => 'db_events.php?server=' . $server . '&db=%2$s&item_name=%1$s&export_item=1&token=' . $token,
            ),
            $events
        );

        /* Triggers */
        $triggers_container = $tree->addContainer(__('Triggers'), $databases);
        $tree->setIcon(PMA_getIcon('b_triggers.png'), $triggers_container);
        $tree->setLinks(
            array(
                'text' => 'db_triggers.php?server=' . $server . '&db=%1$s&token=' . $token,
                'icon' => 'db_triggers.php?server=' . $server . '&db=%1$s&token=' . $token
            ),
            $triggers_container
        );
        $query = "SELECT `TRIGGER_NAME` AS `name`,`EVENT_OBJECT_SCHEMA` AS `parent_1` "
               . "FROM `INFORMATION_SCHEMA`.`TRIGGERS`";
        $triggers = $tree->addList($query, true, $triggers_container);
        $tree->setIcon(PMA_getIcon('b_triggers.png'), $triggers);
        $tree->setLinks(
            array(
                'text' => 'db_triggers.php?server=' . $server . '&db=%2$s&item_name=%1$s&edit_item=1&token=' . $token,
                'icon' => 'db_triggers.php?server=' . $server . '&db=%2$s&item_name=%1$s&export_item=1&token=' . $token,
            ),
            $triggers
        );

        /* Table Columns */
        if ($GLOBALS['cfg']['LeftFrameLight'] && $GLOBALS['is_ajax_request']) {
            $column_container = $tree->addContainer(
                __('Columns'),
                $tables
            );
            $tree->setIcon(PMA_getIcon('s_vars.png', '', false, true), $column_container);
            $query = "SELECT `COLUMN_NAME` AS `name`, `TABLE_NAME` AS `parent_1`, `TABLE_SCHEMA` AS `parent_2` FROM `INFORMATION_SCHEMA`.`COLUMNS`";
            $columns = $tree->addList($query, true, $column_container);
            $tree->setIcon(PMA_getIcon('s_vars.png', '', false, true), $columns);
            $tree->setLinks(
            array(
                    'text' => 'tbl_alter?server=' . $server . '&db=%3$s&table=%2$s&field=%1$s&token=' . $token,
                    'icon' => 'tbl_alter?server=' . $server . '&db=%3$s&table=%2$s&field=%1$s&token=' . $token,
                ),
                $columns
            );
        }

        /* Table Indexes */
        if ($GLOBALS['cfg']['LeftFrameLight'] && $GLOBALS['is_ajax_request']) {
            $index_container = $tree->addContainer(
                __('Indexes'),
                $tables
            );
            $tree->setIcon(PMA_getIcon('b_primary.png', '', false, true), $index_container);
            $query = "SELECT DISTINCT `CONSTRAINT_NAME` AS `name`, `TABLE_NAME` AS `parent_1`, `TABLE_SCHEMA` AS `parent_2` FROM `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`";
            $indexes = $tree->addList($query, true, $index_container);
            $tree->setIcon(PMA_getIcon('b_primary.png', '', false, true), $indexes);
            $tree->setLinks(
            array(
                    'text' => 'tbl_indexes.php?server=' . $server . '&db=%3$s&table=%2$s&index=%1$s&token=' . $token,
                    'icon' => 'tbl_indexes.php?server=' . $server . '&db=%3$s&table=%2$s&index=%1$s&token=' . $token
                ),
                $indexes
            );
        }

        /* Render the tree */
        if ($ajax) {
            if ($retval = $tree->renderPath()) {
                PMA_ajaxResponse($retval, true);
            } else {
                PMA_ajaxResponse('', false);
            }
        } else {
            $light = '';
            if ($GLOBALS['cfg']['LeftFrameLight']) {
                $light = " class='light'";
            }
            $_url_params = array('pos' => $this->pos);
            $num_db = PMA_DBI_fetch_value("SELECT COUNT(*) FROM `INFORMATION_SCHEMA`.`SCHEMATA`");
            ob_start();
            PMA_listNavigator(
                $num_db,
                $this->pos,
                $_url_params,
                'navigation.php',
                'frame_navigation',
                $GLOBALS['cfg']['MaxDbList']
            );
            $list = ob_get_contents();
            ob_end_clean();
            $retval  = '<!-- NAVIGATION TREE START -->' . PHP_EOL;
            $retval .= '<!-- DATABASE PAGINATION START -->' . PHP_EOL;
            $retval .= $list;
            $retval .= '<!-- DATABASE PAGINATION END -->' . PHP_EOL;
            $retval .= "<div id='navigation_tree'$light>\n";
            $retval .= $tree->renderTree();
            $retval .= "</div>\n";
            $retval .= '<!-- NAVIGATION TREE END -->' . PHP_EOL;
        }

        return $retval;
    }
}
?>
