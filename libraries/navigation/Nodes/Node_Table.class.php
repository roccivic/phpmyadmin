<?php

class Node_Table extends Node {
    
    public function __construct($name, $type = Node::OBJECT, $is_group = false)
    {
        $this->icon = PMA_getImage('b_browse.png');
        $this->links = array(
            'text' => 'sql.php?server=' . $GLOBALS['server']
                    . '&amp;db=%2$s&amp;table=%1$s'
                    . '&amp;pos=0&amp;token=' . $GLOBALS['token'],
            'icon' => $GLOBALS['cfg']['LeftDefaultTabTable']
                    . '?server=' . $GLOBALS['server']
                    . '&amp;db=%2$s&amp;table=%1$s&amp;token=' . $GLOBALS['token']
        );
        parent::__construct($name, $type, $is_group);
    }
}

?>
