<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * the navigation frame - displays server, db and table selection tree
 *
 * @package PhpMyAdmin
 */

/**
 * Gets a core script and starts output buffering work
 */
require_once './libraries/common.inc.php';
require_once './libraries/common.lib.php';
require_once './libraries/ob.lib.php';
require_once './libraries/RecentTable.class.php';
require_once './libraries/navigation/Node.class.php';
require_once './libraries/navigation/CollapsibleTree.class.php';
require_once './libraries/navigation/navigation.class.php';
require_once './libraries/header_http.inc.php';

PMA_outBufferPre();

new navigation();

?>
