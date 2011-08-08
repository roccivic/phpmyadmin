<?
/* vim: set expandtab sw=4 ts=4 sts=4: */
class Node {
    const CONTAINER = 0;
    const OBJECT = 1;
    private $id;
    private $icon;
    private $links;
    private $name;
    private $real_name;
    private $type;
    private $parent;
    private $separator = '';
    private $separator_depth = 1;
    private $children = array();
    public function __construct($name, $id, $type)
    {
        $this->name = $name;
        $this->real_name = $name;
        $this->id = $id;
        $this->type = $type;
    }
    public function __get($a)
    {
        return $this->$a;
    }
    public function __set($a, $b)
    {
        switch ($a) {
        case 'icon':
        case 'links':
        case 'parent':
        case 'real_name':
        case 'separator':
        case 'separator_depth':
            $this->$a = $b;
            return true;
        default:
            return false;
        }
    }
    public function addChild($child)
    {
        $this->children[] = $child;
    }
    public function getChild($name)
    {
        foreach ($this->children as $child) {
            if ($child->name == $name) {
                return $child;
            }
        }
        return false;
    }
    public function removeChild($name)
    {
        foreach ($this->children as $key => $child) {
            if ($child->name == $name) {
                unset($this->children[$key]);
                break;
            }
        }
    }
    public function find($id)
    {
        $retval = array();
        if ($id == $this->id) {
            $retval[] = $this;
        }
        foreach ($this->children as $key => $child) {
            $match = $child->find($id);
            if ($match) {
                $retval = array_merge($match, $retval);
            }
        }
        return $retval;
    }
    public function depth()
    {
        $depth = 0;
        $parent = $this->parent;
        while (isset($parent)) {
            $depth++;
            $parent = $parent->parent;
        }
        return $depth;
    }
    public function parents($self = false)
    {
        $parents = array();
        if ($self && $this->type != Node::CONTAINER) {
            $parents[] = $this;
            $self = false;
        }
        $parent = $this->parent;
        while (isset($parent)) {
            if ($parent->type != Node::CONTAINER) {
                $parents[] = $parent;
            }
            $parent = $parent->parent;
        }
        return $parents;
    }
    public function hasChildren($count_empty_containers = true)
    {
        $retval = false;
        if ($count_empty_containers) {
            if (count($this->children)) {
                $retval = true;
            }
        } else {
            foreach ($this->children as $child) {
                if ($child->type == Node::OBJECT) {
                    $retval = true;
                    break;
                } else if ($child->hasChildren(false)) {
                    $retval = true;
                    break;
                }
            }
        }
        return $retval;
    }
    public function siblings()
    {
        return $this->parent->children;
    }
}
?>
