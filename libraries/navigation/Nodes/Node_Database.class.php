<?php

class Node_Database extends Node {
    
    public function __construct($name, $type = Node::OBJECT, $is_group = false)
    {
        $this->icon = PMA_getImage('s_db.png');
        $this->links = array(
            'text' => 'db_structure.php?server=' . $GLOBALS['server']
                    . '&amp;db=%1$s&amp;token=' . $GLOBALS['token'],
            'icon' => 'db_operations.php?server=' . $GLOBALS['server']
                    . '&amp;db=%1$s&amp;token=' . $GLOBALS['token']
        );
        parent::__construct($name, $type, $is_group);
    }
}

?>
