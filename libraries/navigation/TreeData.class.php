<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Data for the navigation tree in the left frame
 *
 * @package phpMyAdmin-Navigation
 */
/**
 * Provides the data required for generating the navigation tree
 */
class TreeData {
    /**
     * Options provider
     *
     * @param string $type The type of item for which to get options
     *
     * @return array An array of options
     */
    static public function getOptions($type)
    {
        switch ($type) {
        case 'databases':
            $retval = array(
                'links' => array(
                    'text' => 'db_structure.php?server=' . $GLOBALS['server']
                            . '&amp;db=%1$s&amp;token=' . $GLOBALS['token'],
                    'icon' => 'db_operations.php?server=' . $GLOBALS['server']
                            . '&amp;db=%1$s&amp;token=' . $GLOBALS['token']
                ),
                'icon' => PMA_getIcon('s_db.png')
            );
            break;
        case 'tables':
            $retval = array(
                'links' => array(
                    'text' => 'sql.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;table=%1$s'
                            . '&amp;pos=0&amp;token=' . $GLOBALS['token'],
                    'icon' => $GLOBALS['cfg']['LeftDefaultTabTable']
                            . '?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;table=%1$s&amp;token=' . $GLOBALS['token']
                ),
                'icon' => PMA_getIcon('b_browse.png')
            );
            break;
        case 'views':
            $retval = array(
                'links' => array(
                    'text' => 'sql.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;table=%1$s&amp;pos=0'
                            . '&amp;token=' . $GLOBALS['token'],
                    'icon' => 'tbl_structure.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;table=%1$s'
                            . '&amp;token=' . $GLOBALS['token']
                ),
                'icon' => PMA_getIcon('b_views.png')
            );
            break;
        case 'functions':
            $retval = array(
                'links' => array(
                    'text' => 'db_routines.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;item_name=%1$s&amp;item_type=FUNCTION'
                            . '&amp;edit_item=1&amp;token=' . $GLOBALS['token'],
                    'icon' => 'db_routines.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;item_name=%1$s&amp;item_type=FUNCTION'
                            . '&amp;export_item=1&amp;token=' . $GLOBALS['token']
                ),
                'icon' => PMA_getIcon('b_routines.png')
            );
            break;
        case 'procedures':
            $retval = array(
                'links' => array(
                    'text' => 'db_routines.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;item_name=%1$s&amp;item_type=PROCEDURE'
                            . '&amp;edit_item=1&amp;token=' . $GLOBALS['token'],
                    'icon' => 'db_routines.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;item_name=%1$s&amp;item_type=PROCEDURE'
                            . '&amp;export_item=1&amp;token=' . $GLOBALS['token']
                ),
                'icon' => PMA_getIcon('b_routines.png')
            );
            break;
        case 'triggers':
            $retval = array(
                'links' => array(
                    'text' => 'db_triggers.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;item_name=%1$s&amp;edit_item=1'
                            . '&amp;token=' . $GLOBALS['token'],
                    'icon' => 'db_triggers.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;item_name=%1$s&amp;export_item=1'
                            . '&amp;token=' . $GLOBALS['token']
                ),
                'icon' => PMA_getIcon('b_triggers.png')
            );
            break;
        case 'events':
            $retval = array(
                'links' => array(
                    'text' => 'db_events.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;item_name=%1$s&amp;edit_item=1'
                            . '&amp;token=' . $GLOBALS['token'],
                    'icon' => 'db_events.php?server=' . $GLOBALS['server']
                            . '&amp;db=%2$s&amp;item_name=%1$s&amp;export_item=1'
                            . '&amp;token=' . $GLOBALS['token']
                ),
                'icon' => PMA_getIcon('b_events.png')
            );
            break;
        case 'columns':
            $retval = array(
                'links' => array(
                    'text' => 'tbl_alter.php?server=' . $GLOBALS['server']
                            . '&amp;db=%3$s&amp;table=%2$s&amp;field=%1$s'
                            . '&amp;token=' . $GLOBALS['token'],
                    'icon' => 'tbl_alter.php?server=' . $GLOBALS['server']
                            . '&amp;db=%3$s&amp;table=%2$s&amp;field=%1$s'
                            . '&amp;token=' . $GLOBALS['token']
                ),
                'icon' => PMA_getIcon('s_vars.png', '', false, true)
            );
            break;
        case 'indexes':
            $retval = array(
                'links' => array(
                    'text' => 'tbl_indexes.php?server=' . $GLOBALS['server']
                            . '&amp;db=%3$s&amp;table=%2$s&amp;index=%1$s'
                            . '&amp;token=' . $GLOBALS['token'],
                    'icon' => 'tbl_indexes.php?server=' . $GLOBALS['server']
                            . '&amp;db=%3$s&amp;table=%2$s&amp;index=%1$s'
                            . '&amp;token=' . $GLOBALS['token']
                ),
                'icon' => PMA_getIcon('b_primary.png', '', false, true)
            );
            break;
        default:
            break;
        }
        return $retval;
    }

    /**
     * Checks if a particular child of a table or of a database has children
     *
     * @param string $type  The type of item to look for
     * @param string $db    The name of the database where to look for items
     * @param string $table The name of the table where to look for items
     *
     * @return int 0, if no children are found, a positive number otherwise
     */
    static public function getPresence($type, $db = null, $table = null)
    {
        $retval = 0;
        switch ($type) {
        case 'tables':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `TABLE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TABLES` ";
                $query .= "WHERE `TABLE_SCHEMA`='$db' ";
                $query .= "AND `TABLE_TYPE`='BASE TABLE' ";
                $query .= "LIMIT 1";
                $retval = PMA_DBI_fetch_value($query) === false ? 0 : 1;
            } else {
                $db     = PMA_backquote($db);
                $query  = "SHOW FULL TABLES FROM $db ";
                $query .= "WHERE `Table_type`='BASE TABLE'";
                $retval = PMA_DBI_num_rows(PMA_DBI_try_query($query));
            }
            break;
        case 'views':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `TABLE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TABLES` ";
                $query .= "WHERE `TABLE_SCHEMA`='$db' ";
                $query .= "AND `TABLE_TYPE`!='BASE TABLE' ";
                $query .= "LIMIT 1";
                $retval = PMA_DBI_fetch_value($query) === false ? 0 : 1;
            } else {
                $db     = PMA_backquote($db);
                $query  = "SHOW FULL TABLES FROM $db ";
                $query .= "WHERE `Table_type`!='BASE TABLE'";
                $retval = PMA_DBI_num_rows(PMA_DBI_try_query($query));
            }
            break;
        case 'procedures':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `ROUTINE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`ROUTINES` ";
                $query .= "WHERE `ROUTINE_SCHEMA`='$db'";
                $query .= "AND `ROUTINE_TYPE`='PROCEDURE' ";
                $query .= "LIMIT 1";
                $retval = PMA_DBI_fetch_value($query) === false ? 0 : 1;
            } else {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SHOW PROCEDURE STATUS WHERE `Db`='$db'";
                $retval = PMA_DBI_num_rows(PMA_DBI_try_query($query));
            }
            break;
        case 'functions':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `ROUTINE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`ROUTINES` ";
                $query .= "WHERE `ROUTINE_SCHEMA`='$db' ";
                $query .= "AND `ROUTINE_TYPE`='FUNCTION' ";
                $query .= "LIMIT 1";
                $retval = PMA_DBI_fetch_value($query) === false ? 0 : 1;
            } else {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SHOW FUNCTION STATUS WHERE `Db`='$db'";
                $retval = PMA_DBI_num_rows(PMA_DBI_try_query($query));
            }
            break;
        case 'triggers':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `TRIGGER_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TRIGGERS` ";
                $query .= "WHERE `EVENT_OBJECT_SCHEMA`='$db' ";
                $query .= "LIMIT 1";
                $retval = PMA_DBI_fetch_value($query) === false ? 0 : 1;
            } else {
                $db     = PMA_backquote($db);
                $query  = "SHOW TRIGGERS FROM $db";
                $retval = PMA_DBI_num_rows(PMA_DBI_try_query($query));
            }
            break;
        case 'events':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `EVENT_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`EVENTS` ";
                $query .= "WHERE `EVENT_SCHEMA`='$db' ";
                $query .= "LIMIT 1";
                $retval = PMA_DBI_fetch_value($query) === false ? 0 : 1;
            } else {
                $db     = PMA_backquote($db);
                $query  = "SHOW EVENTS FROM $db";
                $retval = PMA_DBI_num_rows(PMA_DBI_try_query($query));
            }
            break;
        case 'columns':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $table  = PMA_sqlAddSlashes($table);
                $query  = "SELECT `COLUMN_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`COLUMNS` ";
                $query .= "WHERE `TABLE_NAME`='$table' ";
                $query .= "AND `TABLE_SCHEMA`='$db' ";
                $query .= "LIMIT 1";
                $retval = PMA_DBI_fetch_value($query) === false ? 0 : 1;
            } else {
                $db     = PMA_backquote($db);
                $table  = PMA_backquote($table);
                $query  = "SHOW COLUMNS FROM $table FROM $db";
                $retval = PMA_DBI_num_rows(PMA_DBI_try_query($query));
            }
            break;
        case 'indexes':
            $db     = PMA_backquote($db);
            $table  = PMA_backquote($table);
            $query  = "SHOW INDEXES FROM $table FROM $db";
            $retval = PMA_DBI_num_rows(PMA_DBI_try_query($query));
            break;
        default:
            break;
        }
        return $retval;
    }

    /**
     * Returns data for a container in the navigation tree
     *
     * @param string $type  The type of item
     * @param string $db    The name of the database where to look for items
     * @param string $table The name of the table where to look for items
     * @param int    $pos   List offset, only used for retreiving databases
     *
     * @return array An array of items
     */
    static public function getData($type, $db = null, $table = null, $pos = null)
    {
        // TODO: some input validation
        $retval = array();
        switch ($type) {
        case 'databases':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $query  = "SELECT `SCHEMA_NAME` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`SCHEMATA` ";
                $query .= "LIMIT $pos, {$GLOBALS['cfg']['MaxDbList']}";
                $retval = PMA_DBI_fetch_result($query);
            } else {
                $query = "SHOW DATABASES";
                $temp = PMA_DBI_fetch_result($query);
                $num = min($GLOBALS['cfg']['MaxDbList'], count($temp));
                for ($i=$pos; $i<$num; $i++) {
                    $retval[] = $temp[$i];
                }
            }
            break;
        case 'tables':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `TABLE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TABLES` ";
                $query .= "WHERE `TABLE_SCHEMA`='$db' ";
                $query .= "AND `TABLE_TYPE`='BASE TABLE'";
                $retval = PMA_DBI_fetch_result($query);
            } else {
                $db     = PMA_backquote($db);
                $query  = "SHOW FULL TABLES FROM $db ";
                $query .= "WHERE `Table_type`='BASE TABLE'";
                $handle = PMA_DBI_try_query($query);
                if ($handle !== false) {
                    while ($arr = PMA_DBI_fetch_array($handle)) {
                        $retval[] = $arr[0];
                    }
                }
            }
            break;
        case 'views':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `TABLE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TABLES` ";
                $query .= "WHERE `TABLE_SCHEMA`='$db' ";
                $query .= "AND `TABLE_TYPE`!='BASE TABLE'";
                $retval = PMA_DBI_fetch_result($query);
            } else {
                $db     = PMA_backquote($db);
                $query  = "SHOW FULL TABLES FROM $db ";
                $query .= "WHERE `Table_type`!='BASE TABLE'";
                $handle = PMA_DBI_try_query($query);
                if ($handle !== false) {
                    while ($arr = PMA_DBI_fetch_array($handle)) {
                        $retval[] = $arr[0];
                    }
                }
            }
            break;
        case 'procedures':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `ROUTINE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`ROUTINES` ";
                $query .= "WHERE `ROUTINE_SCHEMA`='$db'";
                $query .= "AND `ROUTINE_TYPE`='PROCEDURE'";
                $retval = PMA_DBI_fetch_result($query);
            } else {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SHOW PROCEDURE STATUS WHERE `Db`='$db'";
                $handle = PMA_DBI_try_query($query);
                if ($handle !== false) {
                    while ($arr = PMA_DBI_fetch_assoc($handle)) {
                        $retval[] = $arr['Name'];
                    }
                }
            }
            break;
        case 'functions':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `ROUTINE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`ROUTINES` ";
                $query .= "WHERE `ROUTINE_SCHEMA`='$db' ";
                $query .= "AND `ROUTINE_TYPE`='FUNCTION'";
                $retval = PMA_DBI_fetch_result($query);
            } else {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SHOW FUNCTION STATUS WHERE `Db`='$db'";
                $handle = PMA_DBI_try_query($query);
                if ($handle !== false) {
                    while ($arr = PMA_DBI_fetch_assoc($handle)) {
                        $retval[] = $arr['Name'];
                    }
                }
            }
            break;
        case 'triggers':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `TRIGGER_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TRIGGERS` ";
                $query .= "WHERE `EVENT_OBJECT_SCHEMA`='$db'";
                $retval = PMA_DBI_fetch_result($query);
            } else {
                $db     = PMA_backquote($db);
                $query  = "SHOW TRIGGERS FROM $db";
                $handle = PMA_DBI_try_query($query);
                if ($handle !== false) {
                    while ($arr = PMA_DBI_fetch_assoc($handle)) {
                        $retval[] = $arr['Trigger'];
                    }
                }
            }
            break;
        case 'events':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $query  = "SELECT `EVENT_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`EVENTS` ";
                $query .= "WHERE `EVENT_SCHEMA`='$db'";
                $retval = PMA_DBI_fetch_result($query);
            } else {
                $db     = PMA_backquote($db);
                $query  = "SHOW EVENTS FROM $db";
                $handle = PMA_DBI_try_query($query);
                if ($handle !== false) {
                    while ($arr = PMA_DBI_fetch_assoc($handle)) {
                        $retval[] = $arr['Name'];
                    }
                }
            }
            break;
        case 'columns':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_sqlAddSlashes($db);
                $table  = PMA_sqlAddSlashes($table);
                $query  = "SELECT `COLUMN_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`COLUMNS` ";
                $query .= "WHERE `TABLE_NAME`='$table' ";
                $query .= "AND `TABLE_SCHEMA`='$db'";
                $retval = PMA_DBI_fetch_result($query);
            } else {
                $db     = PMA_backquote($db);
                $table  = PMA_backquote($table);
                $query  = "SHOW COLUMNS FROM $table FROM $db";
                $handle = PMA_DBI_try_query($query);
                if ($handle !== false) {
                    while ($arr = PMA_DBI_fetch_assoc($handle)) {
                        $retval[] = $arr['Field'];
                    }
                }
            }
            break;
        case 'indexes':
            $db     = PMA_backquote($db);
            $table  = PMA_backquote($table);
            $query  = "SHOW INDEXES FROM $table FROM $db";
            $handle = PMA_DBI_try_query($query);
            if ($handle !== false) {
                while ($arr = PMA_DBI_fetch_assoc($handle)) {
                    if (! in_array($arr['Key_name'], $retval)) {
                        $retval[] = $arr['Key_name'];
                    }
                }
            }
            break;
        default:
            break;
        }
        return $retval;
    }
}
?>
