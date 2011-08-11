<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
class CollapsibleTree {
    const PARENT_INDEX_PREFIX = 'parent_';
    private $id = 0;
    private $separator = '';
    private $tree;
    private $path = array();
    private $filter = '';
    public function __construct()
    {
        // Get the active node
        if (isset($_REQUEST['path'])) {
            $path = explode('.', $_REQUEST['path']);
            foreach ($path as $key => $value) {
                $path[$key] = base64_decode($value);
            }
            $this->path = $path;
        }
        // Get the argument used in the WHERE clause for query optimisation
        if (isset($_REQUEST['filter'])) {
            $this->filter = $_REQUEST['filter'];
        }
        // Initialise the tree by creating a root node
        $node = new Node('root', 0, Node::CONTAINER);
        $this->tree = $node;
        return 0;
    }
    public function addList($data, $is_query, $parent = 0, $limit_pos = 0, $limit_length = false)
    {
        if ($is_query) {
            if (! empty($this->filter) && $GLOBALS['cfg']['LeftFrameLight']) {
                $this->filter = PMA_sqlAddSlashes($this->filter);
                $last_parent = substr_count($data, 'parent_');
                if ($last_parent > 0) {
                    $data .= " HAVING `parent_$last_parent`='{$this->filter}'";
                }
            } else if ($GLOBALS['cfg']['LeftFrameLight'] && $parent != $this->tree->id) {
                return false;
            }
            if ($limit_length !== false) {
                $data .= " LIMIT $limit_pos,$limit_length";
            }
            $data = PMA_DBI_fetch_result($data);
        }
        $new_id = ++$this->id;
        $parents = $this->tree->find($parent);
        foreach ($parents as $elm) { // FIXME: this loop must be more efficient
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $name = $value['name'];
                    $ancestors = $value;
                    unset($ancestors['name']);
                    $success = true;
                    $working_node = $elm;
                    $height = 1;
                    while (count($ancestors) >= $height) {
                        if ($working_node->type == Node::CONTAINER) {
                            $working_node = $working_node->parent;
                            continue;
                        }
                        $index = self::PARENT_INDEX_PREFIX . $height;
                        if (isset($ancestors[$index]) && $ancestors[$index] == $working_node->name) {
                            $working_node = $working_node->parent;
                            $height++;
                            continue;
                        } else {
                            $success = false;
                            break;
                        }
                    }
                    if (! $success) {
                        continue;
                    }
                } else {
                    $name = $value;
                }
                $node = new Node($name, $new_id, Node::OBJECT);
                $node->parent = $elm;
                $elm->addChild($node);
                unset($data[$key]);
            }
        }
        return $new_id;
    }
    public function addContainer($name, $parent = 0, $separator = '', $separator_depth = 1)
    {
        $new_id = ++$this->id;
        $parents = $this->tree->find($parent);
        foreach ($parents as $elm) {
            $node = new Node($name, $new_id, Node::CONTAINER);
            $node->separator = $separator;
            $node->separator_depth = $separator_depth;
            $node->parent = $elm;
            $elm->addChild($node);
        }
        return $new_id;
    }
    public function setRootSeparator($value, $depth = 1)
    {
        $this->tree->separator = $value;
        $this->tree->separator_depth = $depth;
    }
    public function setIcon($img, $id)
    {
        foreach ($this->tree->find($id) as $node) {
            $node->icon = $img;
        }
    }
    public function setLinks($links, $id)
    {
        foreach ($this->tree->find($id) as $node) {
            $node->links = $links;
        }
    }
    public function renderTree()
    {
        $this->groupTree();
        $retval = "<ul>\n";
        $children = $this->tree->children;
        usort($children, array('CollapsibleTree', 'sortNode'));
        $this->setVisibility();
        foreach ($children as $child) {
            $retval .= $this->renderNodeFromObject($child, true);
        }
        $retval .= "</ul>\n";
        return $retval;
    }
    public function renderPath()
    {
        $retval = false;
        $this->groupTree();
        $node = $this->tree;
        foreach ($this->path as $key => $value) {
            $child = $node->getChild($value);
            if ($child !== false) {
                $node = $child;
            }
        }
        if ($child !== false) {
            $retval = "<ul style='display: none;'>\n";
            $children = $node->children;
            usort($children, array('CollapsibleTree', 'sortNode'));
            $this->setVisibility();
            foreach ($children as $child) {
                $retval .= $this->renderNodeFromObject($child, true);
            }
            $retval .= "</ul>\n";
        }
        return $retval;
    }
    public function renderNodeFromObject($node, $recursive = -1, $indent = '  ')
    {
        if ($node->type == Node::CONTAINER && count($node->children) == 0) {
            return '';
        }
        $retval = $indent . "<li class='nowrap'>";
        $hasChildren = $node->hasChildren(false);
        if ($hasChildren || ($GLOBALS['cfg']['LeftFrameLight'] && $node->filter() == $node->real_name)) {
            $path = array();
            foreach ($node->parents(true, true) as $parent) {
                $path[] = urlencode(base64_encode($parent->name));
            }
            $path = implode('.', array_reverse($path));
            if ($filter = $node->filter()) {
                $path .= '&amp;filter=' . urlencode($filter);
            }
            $link    = "navigation.php?" . PMA_generate_common_url() . "&path=$path";
            $ajax    = '';
            if ($GLOBALS['cfg']['AjaxEnable']) {
                $ajax = ' ajax';
            }
            $loaded = '';
            if ($GLOBALS['is_ajax_request'] || $GLOBALS['cfg']['LeftFrameLight'] != true || $node->type == Node::CONTAINER) {
                $loaded = ' loaded';
            }
            $retval .= "<a class='expander$ajax$loaded' target='_self' href='$link'>";
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
        if ($recursive && $hasChildren) {
            $hide = '';
            if ($node->visible == false) {
                $hide = " style='display: none;'";
            }
            $retval .= "\n" . $indent ."  <ul$hide>\n";
            $children = $node->children;
            usort($children, array('CollapsibleTree', 'sortNode'));
            foreach ($children as $child) {
                $retval .= $this->renderNodeFromObject($child, true, $indent . '    ');
            }
            $retval .= $indent . "  </ul>\n" . $indent;
        }
        $retval .= "</li>\n";
        return $retval;
    }
    private function setVisibility($to_tree = true)
    {
        $node = $this->tree;
        foreach ($this->path as $key => $value) {
            $child = $node->getChild($value);
            if ($child !== false) {
                $child->visible = true;
                $node = $child;
            }
        }
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
                    $groups[$key] = new Node($key, $node->id + 100000, Node::CONTAINER);
                    $groups[$key]->parent = $node;
                    $groups[$key]->separator = $node->separator;
                    $groups[$key]->separator_depth = $node->separator_depth - 1;
                    $groups[$key]->icon = PMA_getIcon('b_group.png', '', false, true);
                    $node->addChild($groups[$key]);
                    foreach ($node->children as $child) { // FIXME: this could be more efficient
                        if (substr($child->name, 0, strlen($key)) == $key && $child->type == Node::OBJECT) {
                            $new_child = new Node(substr($child->name, strlen($key)), $node->id, Node::OBJECT);
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
    public function dumpTree($obj)
    {
        ob_start();
        var_dump($this->tree);
        $data = ob_get_contents();
        ob_end_clean();
        $data = str_replace(' ', '&nbsp;&nbsp;', $data);
        $data = nl2br($data);
        echo $data;
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
