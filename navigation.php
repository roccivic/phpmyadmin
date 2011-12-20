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

// The Nodes are the building blocks for the navigation tree
require_once './libraries/navigation/Nodes/Node.class.php';
// All of the below Nodes inherit from the base Node
require_once './libraries/navigation/Nodes/Node_Column.class.php';
require_once './libraries/navigation/Nodes/Node_Database.class.php';
require_once './libraries/navigation/Nodes/Node_Event.class.php';
require_once './libraries/navigation/Nodes/Node_Function.class.php';
require_once './libraries/navigation/Nodes/Node_Index.class.php';
require_once './libraries/navigation/Nodes/Node_Procedure.class.php';
require_once './libraries/navigation/Nodes/Node_Table.class.php';
require_once './libraries/navigation/Nodes/Node_Trigger.class.php';
require_once './libraries/navigation/Nodes/Node_View.class.php';
// Containers. Also inherit from the base Node
require_once './libraries/navigation/Nodes/Node_Column_Container.class.php';
require_once './libraries/navigation/Nodes/Node_Event_Container.class.php';
require_once './libraries/navigation/Nodes/Node_Function_Container.class.php';
require_once './libraries/navigation/Nodes/Node_Index_Container.class.php';
require_once './libraries/navigation/Nodes/Node_Procedure_Container.class.php';
require_once './libraries/navigation/Nodes/Node_Table_Container.class.php';
require_once './libraries/navigation/Nodes/Node_Trigger_Container.class.php';
require_once './libraries/navigation/Nodes/Node_View_Container.class.php';

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
