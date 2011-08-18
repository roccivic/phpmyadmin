<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Displays a collapsible of database objects in the navigation frame
 *
 * @package phpMyAdmin-Navigation
 */
class CollapsibleTree {
    private $separator = '';
    private $tree;
    private $a_path = array();
    private $v_path = array();
    private $pos;

    public function __construct($pos)
    {
        // Save the position at which we are in the database list
        $this->pos = $pos;
        // Get the active node
        if (isset($_REQUEST['a_path'])) {
            $a_path = explode('.', $_REQUEST['a_path']);
            foreach ($a_path as $key => $value) {
                $a_path[$key] = base64_decode($value);
            }
            $this->a_path = $a_path;
        }
        if (isset($_REQUEST['v_path'])) {
            $v_path = explode('.', $_REQUEST['v_path']);
            foreach ($v_path as $key => $value) {
                $v_path[$key] = base64_decode($value);
            }
            $this->v_path = $v_path;
        }
        // Initialise the tree by creating a root node
        $node = new Node('root', 0, Node::CONTAINER);
        $this->tree = $node;
        if ($GLOBALS['cfg']['LeftFrameDBTree']) {
            $this->tree->separator = $GLOBALS['cfg']['LeftFrameDBSeparator'];
            $this->tree->separator_depth = 10000;
        }
    }

    private function buildTree()
    {
        foreach (TreeData::getData('databases', null, null, 0) as $db) {
            $this->addObject($db, $this->tree, TreeData::getOptions('databases'));
        }
        foreach ($this->tree->children as $child) {
            $containers = $this->addDbContainers($child);
            foreach ($containers as $key => $value) {
                foreach (TreeData::getData($key, $child->real_name) as $item) {
                    $this->addObject($item, $value, TreeData::getOptions($key));
                }
            }
        }
    }

    private function buildPath()
    {
        $retval = $this->tree;
        foreach (TreeData::getData('databases', null, null, 0) as $db) {
            $this->addObject($db, $this->tree, TreeData::getOptions('databases'));
        }
        if (count($this->a_path) > 1) {
            array_shift($this->a_path); // remove 'root'
            $db = $this->tree->getChild($this->a_path[0]);
            $retval = $db;
            $containers = $this->addDbContainers($db);
            array_shift($this->a_path); // remove db
            if (count($this->a_path) > 0 && array_key_exists($this->a_path[0], $containers)) {
                $container = $db->getChild($this->a_path[0], true);
                $retval = $container;
                foreach (TreeData::getData($this->a_path[0], $db->real_name) as $item) {
                    $this->addObject($item, $container, TreeData::getOptions($this->a_path[0]));
                }
                if (count($this->a_path) > 1 && $this->a_path[0] != 'tables') {
                    $retval = false;
                } else {
                    array_shift($this->a_path); // remove container
                    if (count($this->a_path) > 0) {
                        $table = $container->getChild($this->a_path[0], true);
                        $retval = $table;
                        $containers = $this->addTableContainers($db, $table);
                        array_shift($this->a_path); // remove table
                        if (count($this->a_path) > 0 && array_key_exists($this->a_path[0], $containers)) {
                            $container = $table->getChild($this->a_path[0], true);
                            $retval = $container;
                            foreach (TreeData::getData($this->a_path[0], $db->real_name, $table->real_name) as $item) {
                                $this->addObject($item, $container, TreeData::getOptions($this->a_path[0]));
                            }
                            if (count($this->a_path) > 1) {
                                $retval = false;
                            }
                        }
                    }
                }
            }
        }
        return $retval;
    }

    private function addObject($name, $parent, $options = array())
    {
        $node = new Node($name, Node::OBJECT);
        $node->parent = $parent;
        $parent->addChild($node);
        if (isset($options['icon'])) {
            $node->icon = $options['icon'];
        }
        if (isset($options['links'])) {
            $node->links = $options['links'];
        }
        return $node;
    }

    private function addContainer($name, $parent, $icon = null, $separator = '', $separator_depth = 1)
    {
        $node = new Node($name, Node::CONTAINER);
        $node->separator = $separator;
        $node->separator_depth = $separator_depth;
        $node->parent = $parent;
        $parent->addChild($node);
        if (isset($icon)) {
            $node->icon = $icon;
        }
        return $node;
    }

    private function addTableContainers($db, $table)
    {
        $retval = array();
        // Columns
        if (TreeData::getPresence('columns', $db->real_name, $table->real_name)) {
            $container = $this->addContainer(
                __('Columns'),
                $table,
                PMA_getIcon('s_vars.png', '', false, true)
            );
            $container->real_name = 'columns';
            $retval['columns'] = $container;
        }
        if (TreeData::getPresence('indexes', $db->real_name, $table->real_name)) {
            // Indexes
            $container = $this->addContainer(
                __('Indexes'),
                $table,
                PMA_getIcon('b_primary.png', '', false, true)
            );
            $container->real_name = 'indexes';
            $retval['indexes'] = $container;
        }

        return $retval;
    }

    private function addDbContainers($db)
    {
        $retval = array();
        if (TreeData::getPresence('tables', $db->real_name)) {
            // Tables
            $container = $this->addContainer(
                __('Tables'),
                $db,
                PMA_getIcon('b_browse.png'),
                $GLOBALS['cfg']['LeftFrameTableSeparator'],
                (int)($GLOBALS['cfg']['LeftFrameTableLevel'])
            );
            $container->real_name = 'tables';
            $retval['tables'] = $container;
        }
        if (TreeData::getPresence('views', $db->real_name)) {
            // Views
            $container = $this->addContainer(
                __('Views'),
                $db,
                PMA_getIcon('b_views.png')
            );
            $container->real_name = 'views';
            $retval['views'] = $container;
        }
        if (TreeData::getPresence('functions', $db->real_name)) {
            // Functions
            $container = $this->addContainer(
                __('Functions'),
                $db,
                PMA_getIcon('b_routines.png')
            );
            $container->real_name = 'functions';
            $retval['functions'] = $container;
        }
        if (TreeData::getPresence('procedures', $db->real_name)) {
            // Procedures
            $container = $this->addContainer(
                __('Procedures'),
                $db,
                PMA_getIcon('b_routines.png')
            );
            $container->real_name = 'procedures';
            $retval['procedures'] = $container;
        }
        if (TreeData::getPresence('triggers', $db->real_name)) {
            // Triggers
            $container = $this->addContainer(
                __('Triggers'),
                $db,
                PMA_getIcon('b_triggers.png')
            );
            $container->real_name = 'triggers';
            $retval['triggers'] = $container;
        }
        if (TreeData::getPresence('events', $db->real_name)) {
            // Events
            $container = $this->addContainer(
                __('Events'),
                $db,
                PMA_getIcon('b_events.png')
            );
            $container->real_name = 'events';
            $retval['events'] = $container;
        }

        return $retval;
    }

    public function groupTree($node = null)
    {
        if (! isset($node)) {
            $node = $this->tree;
        }
        $this->groupNode($node);
        foreach ($node->children as $child) {
            $this->groupNode($child);
            $this->groupTree($child);
        }
    }

    public function groupNode($node)
    {
        if ($node->type == Node::CONTAINER) {
            $prefixes = array();
            foreach ($node->children as $child) {
                if (strlen($node->separator) && $node->separator_depth > 0) {
                    $separator = $node->separator;
                    $sep_pos = strpos($child->name, $separator);
                    if ($sep_pos != false && $sep_pos != strlen($child->name)) {
                        $sep_pos++;
                        $prefix = substr($child->name, 0, $sep_pos);
                        if (! isset($prefixes[$prefix])) {
                            $prefixes[$prefix] = 1;
                        } else {
                            $prefixes[$prefix]++;
                        }
                    }
                }
            }
            foreach ($prefixes as $key => $value) {
                if ($value == 1) {
                    unset($prefixes[$key]);
                }
            }
            if (count($prefixes)) {
                $groups = array();
                foreach ($prefixes as $key => $value) {
                    $groups[$key] = new Node($key, Node::CONTAINER, true);
                    $groups[$key]->parent = $node;
                    $groups[$key]->separator = $node->separator;
                    $groups[$key]->separator_depth = $node->separator_depth - 1;
                    $groups[$key]->icon = $GLOBALS['cfg']['NavigationBarIconic'] ? PMA_getIcon('b_group.png', '', false, true) : '';
                    $node->addChild($groups[$key]);
                    foreach ($node->children as $child) { // FIXME: this could be more efficient
                        if (substr($child->name, 0, strlen($key)) == $key && $child->type == Node::OBJECT) {
                            $new_child = new Node(substr($child->name, strlen($key)), Node::OBJECT);
                            $new_child->real_name = $child->real_name;
                            $new_child->icon = $child->icon;
                            $new_child->links = $child->links;
                            $new_child->parent = $groups[$key];
                            $groups[$key]->addChild($new_child);
                            foreach ($child->children as $elm) {
                                $new_child->addChild($elm);
                                $elm->parent = $new_child;
                            }
                            $node->removeChild($child->name);
                        }
                    }
                }
                foreach ($prefixes as $key => $value) {
                    $this->groupNode($groups[$key]);
                }
            }
        }
    }

    public function renderTree()
    {
        $this->buildTree();
        $this->groupTree();
        $retval = "<ul>\n";
        $children = $this->tree->children;
        usort($children, array('CollapsibleTree', 'sortNode'));
        $this->setVisibility();
        foreach ($children as $child) {
            $retval .= $this->renderNode($child, true);
        }
        $retval .= "</ul>\n";
        return $retval;
    }

    public function renderState()
    {
        $node = $this->buildPath();
        if ($node === false) {
            $retval = false;
        } else {
            $this->groupTree();
            $retval = "<ul>\n";
            $children = $this->tree->children;
            usort($children, array('CollapsibleTree', 'sortNode'));
        $this->setVisibility();
            foreach ($children as $child) {
                $retval .= $this->renderNode($child, true);
            }
            $retval .= "</ul>\n";
        }
        return $retval;
    }

    public function renderPath()
    {
        $node = $this->buildPath();
        if ($node === false) {
            $retval = false;
        } else {
            $this->groupTree();
            $retval = "<ul style='display: none;'>\n";
            $children = $node->children;
            usort($children, array('CollapsibleTree', 'sortNode'));
            foreach ($children as $child) {
                $retval .= $this->renderNode($child, true);
            }
            $retval .= "</ul>\n";
        }
        return $retval;
    }

    public function renderNode($node, $recursive = -1, $indent = '  ')
    {
        if (   $node->type == Node::CONTAINER
            && count($node->children) == 0
            && $GLOBALS['is_ajax_request'] != true
            && $GLOBALS['cfg']['LeftFrameLight'] != true
        ) {
            return '';
        }
        $retval = $indent . "<li class='nowrap'>";
        $hasChildren = $node->hasChildren(false);
        if ($GLOBALS['is_ajax_request'] || $hasChildren || $GLOBALS['cfg']['LeftFrameLight']) {
            $a_path = array();
            foreach ($node->parents(true, true, false) as $parent) {
                $a_path[] = urlencode(base64_encode($parent->real_name));
            }
            $a_path = implode('.', array_reverse($a_path));
            $v_path = array();
            foreach ($node->parents(true, true, true) as $parent) {
                $v_path[] = urlencode(base64_encode($parent->name));
            }
            $v_path = implode('.', array_reverse($v_path));
            $link    = "navigation.php?" . PMA_generate_common_url() . "&a_path=$a_path&v_path=$v_path&XDEBUG_PROFILE";
            $ajax    = '';
            if ($GLOBALS['cfg']['AjaxEnable']) {
                $ajax = ' ajax';
            }
            $loaded = '';
            if ($node->is_group || $GLOBALS['cfg']['LeftFrameLight'] != true) {
                $loaded = ' loaded';
            }
            $container = '';
            if ($node->type == Node::CONTAINER) {
                $container = ' container';
            }
            $retval .= "<a class='expander$ajax$loaded$container' target='_self' href='$link'>";
            $retval .= PMA_getIcon('b_plus.png');
            $retval .= "</a>";
        } else {
            $retval .= PMA_getIcon('null.png');
        }
        $retval .= str_replace('class="', 'style="display:none;" class="throbber ', PMA_getIcon('ajax_clock_small.gif', '', false, true));
        if ($node->type == Node::CONTAINER) {
            $retval .= "<i>";
        }
        if ($GLOBALS['cfg']['NavigationBarIconic']) {
            if (isset($node->links['icon'])) {
                $args = array();
                foreach ($node->parents(true) as $parent) {
                    $args[] = urlencode($parent->real_name);
                }
                $link = vsprintf($node->links['icon'], $args);
                $retval .= "<a href='$link'>{$node->icon}</a>";
            } else {
                $retval .= "{$node->icon}";
            }
        }
        if (isset($node->links['text'])) {
            $args = array();
            foreach ($node->parents(true) as $parent) {
                $args[] = urlencode($parent->real_name);
            }
            $link = vsprintf($node->links['text'], $args);
            $retval .= "<a href='$link'>" . htmlspecialchars($node->real_name) . "</a>";
        } else {
            $retval .= "{$node->name}";
        }
        if ($node->type == Node::CONTAINER) {
            $retval .= "</i>";
        }
        if ($recursive) {
            $hide = '';
            if ($node->visible == false) {
                $hide = " style='display: none;'";
            }
            $children = $node->children;
            usort($children, array('CollapsibleTree', 'sortNode'));
            $buffer = '';
            foreach ($children as $child) {
                $buffer .= $this->renderNode($child, true, $indent . '    ');
            }
            if (! empty($buffer)) {
                $retval .= "\n" . $indent ."  <ul$hide>\n";
                $retval .= $buffer;
                $retval .= $indent . "  </ul>\n" . $indent;
            }
        }
        $retval .= "</li>\n";
        return $retval;
    }

    private function setVisibility()
    {
        $node = $this->tree;
        foreach ($this->v_path as $key => $value) {
            $child = $node->getChild($value);
            if ($child !== false) {
                $child->visible = true;
                $node = $child;
            }
        }
    }

    static public function sortNode($a, $b) {
        if ($GLOBALS['cfg']['NaturalOrder']) {
            return strnatcmp($a->name, $b->name);
        } else {
            return strcmp($a->name, $b->name);
        }
    }
}
?>
