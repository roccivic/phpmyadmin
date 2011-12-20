<?php

class Node_Table_Container extends Node {
    
    public function __construct()
    {
        parent::__construct(__('Tables'), Node::CONTAINER);
        $this->icon = PMA_getImage('b_browse.png', '');
        $this->links = array(
            'text' => 'db_structure.php?server=' . $GLOBALS['server']
                    . '&amp;db=%1$s&amp;token=' . $GLOBALS['token'],
            'icon' => 'db_structure.php?server=' . $GLOBALS['server']
                    . '&amp;db=%1$s&amp;token=' . $GLOBALS['token'],
        );
        $this->separator = $GLOBALS['cfg']['LeftFrameTableSeparator'];
        $this->separator_depth = (int)($GLOBALS['cfg']['LeftFrameTableLevel']);
        $this->real_name = 'tables';
    }
}

?>
