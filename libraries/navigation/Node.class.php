<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * The Node is the building block for the collapsible navigation tree
 *
 * @package phpMyAdmin-Navigation
 */
class Node {
    const CONTAINER = 0;
    const OBJECT = 1;
    private $children = array();
    private $icon;
    private $links;
    private $name;
    private $parent;
    private $real_name;
    private $separator = '';
    private $separator_depth = 1;
    private $type;
    private $is_group;
    private $visible = false;
    public function __construct($name, $type, $is_group = false)
    {
        $this->name = $name;
        $this->real_name = $name;
        $this->type = $type;
        $this->is_group = $is_group;
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
        case 'visible':
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
    public function getChild($name, $real_name = false)
    {
        if ($real_name) {
            foreach ($this->children as $child) {
                if ($child->real_name == $name) {
                    return $child;
                }
            }
        } else {
            foreach ($this->children as $child) {
                if ($child->name == $name) {
                    return $child;
                }
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
    public function parents($self = false, $containers = false, $groups = false)
    {
        $parents = array();
        if ($self && ($this->type != Node::CONTAINER || $containers) && ($this->is_group != true || $groups)) {
            $parents[] = $this;
            $self = false;
        }
        $parent = $this->parent;
        while (isset($parent)) {
            if (($parent->type != Node::CONTAINER || $containers) && ($parent->is_group != true || $groups)) {
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
                if ($child->type == Node::OBJECT || $child->hasChildren(false)) {
                    $retval = true;
                    break;
                }
            }
        }
        return $retval;
    }
    public function numChildren()
    {
        $retval = 0;
        foreach ($this->children as $child) {
            if ($child->type == Node::OBJECT) {
                $retval++;
            } else {
                $retval += $child->numChildren();
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
