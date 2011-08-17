<?php
/* vim: set expandtab sw=4 ts=4 sts=4:
/**
 * the navigation frame - displays server, db and table selection tree
 *
 * @package PhpMyAdmin-Navigation
 */

// Include common functionalities
require_once './libraries/common.inc.php';
require_once './libraries/common.lib.php';

// Output buffering
require_once './libraries/ob.lib.php';

// Recent Tables List
require_once './libraries/RecentTable.class.php';

// The Node is the building block for the navigation tree
require_once './libraries/navigation/Node.class.php';

// Contains data necessary to generate the collapsible tree
// such as SQL queries, links and icons
require_once './libraries/navigation/TreeData.class.php';

// Generates a collapsible tree of database objects
require_once './libraries/navigation/CollapsibleTree.class.php';

// Generates the header, logo, links, server choice.
// Also initialises the recent table and the collapsible tree classes
require_once './libraries/navigation/navigation.class.php';

// Send out the HTTP headers
require_once './libraries/header_http.inc.php';

// Start output buffering
PMA_outBufferPre();

// Do the magic
new Navigation();

?>
