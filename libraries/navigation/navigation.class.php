<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
class navigation {
    /**
     * Variables
     */
    private $buffer;

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
        if ($GLOBALS['is_ajax_request'] && $_REQUEST['recent_table']) {
            PMA_ajaxResponse('', true, array('options' => PMA_RecentTable::getInstance()->getHtmlSelectOption()));
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
//        $retval .= PMA_includeJS('functions.js');
//        $retval .= PMA_includeJS('messages.php');
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
        if ($GLOBALS['cfg']['MainPageIconic']) {
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
                if ($GLOBALS['cfg']['MainPageIconic']) {
                       $retval .= '<img class="icon ic_s_loggoff" src="themes/dot.gif" alt="' . __('Log out') . '" /></a>' . PHP_EOL;
                } else {
                    $retval .= __('Log out') . '</a>' . PHP_EOL;
                    $retval .= '    <br />' . PHP_EOL;
                }
            }
            $retval .= '    <a href="querywindow.php?' . PMA_generate_common_url($GLOBALS['db'], $GLOBALS['table']) . '&amp;no_js=true"';
            $retval .= ' title="' . __('Query window') . '"';
            $retval .= ' onclick="javascript:if (window.parent.open_querywindow()) return false;">';
            if ($GLOBALS['cfg']['MainPageIconic']) {
                $retval .= '<img class="icon ic_b_selboard" src="themes/dot.gif" alt="' . __('Query window') . '" /></a>' . PHP_EOL;
            } else {
                $retval .= __('Query window') . '</a>' . PHP_EOL;
                $retval .= '    <br />' . PHP_EOL;
            }
        }
        $retval .= '    <a href="Documentation.html" target="documentation"';
        $retval .= ' title="' . __('phpMyAdmin documentation') . '" >';
        if ($GLOBALS['cfg']['MainPageIconic']) {
            $retval .= '<img class="icon ic_b_docs" src="themes/dot.gif"';
            $retval .= ' alt="' . __('phpMyAdmin documentation') . '" /></a>' . PHP_EOL;
        } else {
            $retval .= __('phpMyAdmin documentation') . '</a>' . PHP_EOL;
            $retval .= '    <br />' . PHP_EOL;
        }
        if ($GLOBALS['cfg']['MainPageIconic']) {
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
        if ($GLOBALS['cfg']['MainPageIconic']) {
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

    private function serverChoice()
    {
        /**
         * Displays the MySQL servers choice form
         */
        // FIXME
        return '';
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

    private function tree()
    {
        global $server, $token;

        /* Init */
        $tree      = new CollapsibleTree();
        $tree->setRootSeparator('_', 10);

        /* Databases */
        $db_list   = PMA_DBI_fetch_result("SELECT `SCHEMA_NAME` AS `name` FROM `INFORMATION_SCHEMA`.`SCHEMATA` #WHERE `SCHEMA_NAME` = 'test'");
        $dbs       = $tree->addList($db_list);
                     $tree->setIcon(PMA_getIcon('s_db.png'), $dbs);
                     $tree->setLinks(
                        array(
                            'text' => 'db_structure.php?server=' . $server . '&db=%1$s&token=' . $token,
                            'icon' => 'db_operations.php?server=' . $server . '&db=%1$s&token=' . $token
                        ),
                        $dbs
                    );

        /* Tables */
        $tb_list   = PMA_DBI_fetch_result("SELECT `TABLE_NAME` AS `name`,`TABLE_SCHEMA` AS `parent_1` FROM `INFORMATION_SCHEMA`.`TABLES`");
        $tbl_cont  = $tree->addContainer(__('Tables'), $dbs, '_');
                     $tree->setIcon(PMA_getIcon('b_browse.png'), $tbl_cont);
                     $tree->setLinks(
                        array('text' => 'db_structure.php?server=' . $server . '&db=%1$s&token=' . $token),
                        $tbl_cont
                    );
        $tables    = $tree->addList($tb_list, $tbl_cont);
                     $tree->setIcon(PMA_getIcon('b_browse.png'), $tables);
                     $tree->setLinks(
                        array(
                            'text' => 'sql.php?server=' . $server . '&db=%2$s&table=%1$s&pos=0&token=' . $token,
                            'icon' => 'tbl_structure.php?server=' . $server . '&db=%2$s&table=%1$s&token=' . $token
                        ),
                        $tables
                    );

        /* Views */
        $vw_list   = PMA_DBI_fetch_result("SELECT `TABLE_NAME` AS `name`,`TABLE_SCHEMA` AS `parent_1` FROM `INFORMATION_SCHEMA`.`VIEWS`");
        $view_cont = $tree->addContainer(__('Views'), $dbs, '_');
                     $tree->setIcon(PMA_getIcon('b_views.png'), $view_cont);
                     $tree->setLinks(
                        array('text' => 'db_structure.php?server=' . $server . '&db=%1$s&token=' . $token),
                        $view_cont
                    );
        $views     = $tree->addList($vw_list, $view_cont);
                     $tree->setIcon(PMA_getIcon('b_views.png'), $views);
                     $tree->setLinks(
                        array(
                            'text' => 'sql.php?server=' . $server . '&db=%2$s&table=%1$s&pos=0&token=' . $token,
                            'icon' => 'tbl_structure.php?server=' . $server . '&db=%2$s&table=%1$s&token=' . $token
                        ),
                        $views
                    );

        /* Routines */
        $rt_list   = PMA_DBI_fetch_result("SELECT `ROUTINE_NAME` AS `name`,`ROUTINE_SCHEMA` AS `parent_1` FROM `INFORMATION_SCHEMA`.`ROUTINES`");
        $rout_cont = $tree->addContainer(__('Routines'), $dbs);
                     $tree->setIcon(PMA_getIcon('b_routines.png'), $rout_cont);
                     $tree->setLinks(
                        array('text' => 'db_routines.php?server=' . $server . '&db=%1$s&token=' . $token),
                        $rout_cont
                    );
        $routines  = $tree->addList($rt_list, $rout_cont);
                     $tree->setIcon(PMA_getIcon('b_routines.png'), $routines);
                     $tree->setLinks(
                        array(
                            'text' => 'db_routines.php?server=' . $server . '&db=%2$s&item_name=%1$s&edit_item=1&token=' . $token,
                            'icon' => 'db_routines.php?server=' . $server . '&db=%2$s&item_name=%1$s&export_item=1&token=' . $token,
                        ),
                        $routines
                    );

        /* Events */
        $ev_list   = PMA_DBI_fetch_result("SELECT `EVENT_NAME` AS `name`,`EVENT_SCHEMA` AS `parent_1` FROM `INFORMATION_SCHEMA`.`EVENTS`");
        $evn_cont  = $tree->addContainer(__('Events'), $dbs);
                     $tree->setIcon(PMA_getIcon('b_events.png'), $evn_cont);
                     $tree->setLinks(
                        array('text' => 'db_events.php?server=' . $server . '&db=%1$s&token=' . $token),
                        $evn_cont
                    );
        $events    = $tree->addList($ev_list, $evn_cont);
                     $tree->setIcon(PMA_getIcon('b_events.png'), $events);
                     $tree->setLinks(
                        array(
                            'text' => 'db_events.php?server=' . $server . '&db=%2$s&item_name=%1$s&edit_item=1&token=' . $token,
                            'icon' => 'db_events.php?server=' . $server . '&db=%2$s&item_name=%1$s&export_item=1&token=' . $token,
                        ),
                        $events
                    );

        /* Triggers */
        $tr_list   = PMA_DBI_fetch_result("SELECT `TRIGGER_NAME` AS `name`,`EVENT_OBJECT_SCHEMA` AS `parent_1` "
                                        . "FROM `INFORMATION_SCHEMA`.`TRIGGERS`");
        $tri_cont  = $tree->addContainer(__('Triggers'), $dbs);
                     $tree->setIcon(PMA_getIcon('b_triggers.png'), $tri_cont);
                     $tree->setLinks(
                        array('text' => 'db_triggers.php?server=' . $server . '&db=%1$s&token=' . $token),
                        $tri_cont
                    );
        $triggers  = $tree->addList($tr_list, $tri_cont);
                     $tree->setIcon(PMA_getIcon('b_triggers.png'), $triggers);
                     $tree->setLinks(
                        array(
                            'text' => 'db_triggers.php?server=' . $server . '&db=%2$s&item_name=%1$s&edit_item=1&token=' . $token,
                            'icon' => 'db_triggers.php?server=' . $server . '&db=%2$s&item_name=%1$s&export_item=1&token=' . $token,
                        ),
                        $triggers
                    );

        /* Render the tree */
        $retval  = '<!-- NAVIGATION TREE START -->' . PHP_EOL;
        $retval .= "<div id='navigation_tree'>\n";
        $retval .= $tree->renderTree();
        $retval .= "</div>\n";
        $retval .= '<!-- NAVIGATION TREE END -->' . PHP_EOL;

        return $retval;
    }
}
?>
