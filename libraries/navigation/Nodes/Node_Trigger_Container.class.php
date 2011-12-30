<?php

class Node_Trigger_Container extends Node {
    
    public function __construct()
    {
        parent::__construct(__('Triggers'), Node::CONTAINER);
        $this->icon = PMA_getImage('b_triggers.png');
        $this->links = array(
            'text' => 'db_triggers.php?server=' . $GLOBALS['server']
                    . '&amp;db=%3$s&amp;token=' . $GLOBALS['token'],
            'icon' => 'db_triggers.php?server=' . $GLOBALS['server']
                    . '&amp;db=%3$s&amp;token=' . $GLOBALS['token'],
        );
        $this->real_name = 'triggers';

        $new = new Node(__('New'));
        $new->icon = PMA_getImage('b_trigger_add.png', '');
        $new->links = array(
            'text' => 'db_triggers.php?server=' . $GLOBALS['server']
                    . '&amp;db=%3$s&amp;token=' . $GLOBALS['token']
                    . '&amp;add_item=1',
            'icon' => 'db_triggers.php?server=' . $GLOBALS['server']
                    . '&amp;db=%3$s&amp;token=' . $GLOBALS['token']
                    . '&amp;add_item=1',
        );
        $new->classes = 'new_trigger italics';
        $this->addChild($new);
    }

}

?>
