<?php

class Node_View_Container extends Node {
    
    public function __construct()
    {
        parent::__construct(__('Views'), Node::CONTAINER);
        $this->icon = PMA_getImage('b_views.png', '');
        $this->links = array(
            'text' => 'db_structure.php?server=' . $GLOBALS['server']
                    . '&amp;db=%1$s&amp;token=' . $GLOBALS['token'],
            'icon' => 'db_structure.php?server=' . $GLOBALS['server']
                    . '&amp;db=%1$s&amp;token=' . $GLOBALS['token'],
        );
        $this->real_name = 'views';
    }
}

?>
