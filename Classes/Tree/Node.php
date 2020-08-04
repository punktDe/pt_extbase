<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Node
    extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
    implements NestedSetNodeInterface
{
    /**
     * Holds a unique temporaray UID which is decreased every time, a temp uid is requested.
     *
     * @var integer
     */
    protected static $tmpUidIndex = -1;



    /**
     * Returns a unique temporary UID for node
     *
     * @static
     * @return integer
     */
    protected static function getNewTemporaryUid()
    {
        return self::$tmpUidIndex--;
    }



    /**
     * Label for node
     *
     * @var string $label
     */
    protected $label;



    /**
     * Nested sets left number of node
     *
     * @var integer $lft
     */
    protected $lft;



    /**
     * Nested sets right number of node
     *
     * @var integer $rgt
     */
    protected $rgt;



    /**
     * Uid of root node in tree
     *
     * @var integer $root
     */
    protected $root;



    /**
     * Holds refernce to parent node (null, if root)
     *
     * @var Node
     */
    protected $parent;



    /**
     * Holds references to child-nodes
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Node>
     */
    protected $children;



    /**
     * Holds namespace of node
     *
     * @var string
     */
    protected $namespace;


    /**
     * @var boolean
     */
    protected $accessible = true;


    /**
     * The constructor.
     *
     * @param string $label Label of node
     */
    public function __construct($label = '')
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();

        if ($label != '') {
            $this->label = $label;
        }

        // We initialize lft and rgt as those values will be overwritten later, if this is not the root node
        $this->lft = 1;
        $this->rgt = 2;

        /**
         * What happens here?
         *
         * UIDs are used throughout our tree implementation to identify nodes. As we do not have a UID
         * whenever a node is created as an object and not yet added to repository, we do not have a UID
         * after creation of object. So we set a unique negative UID here which will be overwritten, when
         * node has been stored to database and restored afterwards.
         */
        $this->uid = self::getNewTemporaryUid();
    }



    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage instances.
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->children = new ObjectStorage();
    }



    /*********************************************************************************************************
     * Default getters and setters used for persistence - return database values, no objects!
     *********************************************************************************************************/

    /**
     * Getter for root node uid
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }



    /**
     * Setter for root node uid
     *
     * @param integer $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }



    /**
     * Getter nested sets right number
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }



    /**
     * Setter nested sets right number
     *
     * @param integer $rgt
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }



    /**
     * Getter nested sets left number
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }



    /**
     * Setter nested sets left number
     *
     * @param integer $lft
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    }



    /*********************************************************************************************************
     * Getters and setters for advanced domain logic. NOT USED FOR PERSISTENCE!
     *********************************************************************************************************/

    /**
     * Setter for parent node
     *
     * @param NodeInterface $node
     */
    public function setParent(NodeInterface $node)
    {
        $this->parent = $node;
        if ($node->children == null) {
            $node->children = new ObjectStorage();
        }
        $node->children->attach($this);
    }



    /**
     * Getter for parent node
     *
     * @return Node
     */
    public function getParent()
    {
        return $this->parent;
    }



    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getChildren()
    {
        if (is_null($this->children)) {
            $this->children = new ObjectStorage();
        }
        return $this->children;
    }



    /**
     * Get count of children recursively
     *
     * @return integer
     */
    public function getChildrenCount()
    {
        if (!is_null($this->children)) {
            return $this->children->count();
        } else {
            return 0;
        }
    }



    /**
     * Returns level of node (0 if node is root).
     *
     * Level is equal to depth
     * of node in tree where root has depth 0.
     *
     * @return integer
     */
    public function getLevel()
    {
        if ($this->parent == null) {
            return 0;
        } else {
            return 1 + $this->parent->getLevel();
        }
    }



    /**
     * Returns sub-nodes in a flat list. The result is ordered
     * in such a way that it reflects the structure of the tree:
     *
     * cat 1
     * - cat 1.1
     * -- cat 1.1.1
     * -- cat 1.1.2
     * - cat 1.2
     * -- cat 1.2.1
     * -- cat 1.2.2
     *
     * Will return
     *
     * cat 1
     * cat 1.1
     * cat 1.1.1
     * cat 1.1.2
     * cat 1.2
     * cat 1.2.1
     * cat 1.2.2
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getSubNodes()
    {
        $subNodes = new ObjectStorage();
        if ($this->children !== null && $this->children->count() > 0) {
            foreach ($this->children as $child) {
                $subNodes->attach($child);
                $subNodes->addAll($child->getSubNodes());
            }
        }
        return $subNodes;
    }



    /*********************************************************************************************************
     * Domain logic
     *********************************************************************************************************/

    /**
     * Adds a child node to children at end of children
     *
     * @param NodeInterface $node
     */
    public function addChild(NodeInterface $node)
    {
        // TODO this should not be necessary. Seems like this method is not invoked, if object is loaded from database
        if (is_null($this->children)) {
            $this->children = new ObjectStorage();
        }

        $this->children->attach($node);
        $node->parent = $this;
    }



    /**
     * Adds a new child node after a given child node
     *
     * @param NodeInterface $newChildNode
     * @param NodeInterface $nodeToAddAfter
     */
    public function addChildAfter(NodeInterface $newChildNode, NodeInterface $nodeToAddAfter)
    {
        $newChildren = new ObjectStorage();
        foreach ($this->children as $child) {
            $newChildren->attach($child);
            if ($child == $nodeToAddAfter) {
                $newChildren->attach($newChildNode);
            }
        }
        $this->children = $newChildren;
    }



    /**
     * Adds a new child node before a given child node
     *
     * @param NodeInterface $newChildNode
     * @param NodeInterface $nodeToAddBefore
     */
    public function addChildBefore(NodeInterface $newChildNode, NodeInterface $nodeToAddBefore)
    {
        $newChildren = new ObjectStorage();
        foreach ($this->children as $child) {
            if ($child == $nodeToAddBefore) {
                $newChildren->attach($newChildNode);
            }
            $newChildren->attach($child);
        }
        $this->children = $newChildren;
    }



    /**
     * Removes given child node
     *
     * @param NodeInterface $node
     */
    public function removeChild(NodeInterface $node)
    {
        $this->children->detach($node);
    }



    /**
     * Returns true, if node has children
     *
     * @return bool Tru, if node has children
     */
    public function hasChildren()
    {
        return ($this->children != null && $this->children->count() > 0);
    }



    /**
     * Returns true, if node has a parent
     *
     * @return bool True, if node has parent node
     */
    public function hasParent()
    {
        return !($this->parent === null);
    }



    /**
     * Returns true, if node is root
     *
     * @return boolean True, if node is root
     */
    public function isRoot()
    {
        return $this->uid == $this->root;
    }



    /**
     * Renders a node as an li-element for debugging
     *
     * @return string
     */
    public function toString()
    {
        $nodeString = '<li id=node_' . $this->uid . '>' . $this->label . ' [uid: ' . $this->uid . ' left: ' . $this->lft . '  right:' . $this->rgt . ']';

        if ($this->hasChildren()) {
            $nodeString .= '<ul>';

            foreach ($this->children as $child) {
                $nodeString .= $child->toString();
            }

            $nodeString .= '</ul>';
        }

        $nodeString .= '</li>';

        return $nodeString;
    }



    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }



    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }



    /**
     * Sets namespace of node
     *
     * @param $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }



    /**
     * Returns namespace of node
     *
     * @return string Namespace of node
     */
    public function getNamespace()
    {
        return $this->namespace;
    }


    /**
     * @param boolean $accessible
     */
    public function setAccessible($accessible)
    {
        $this->accessible = $accessible;
    }


    /**
     * @return boolean
     */
    public function isAccessible()
    {
        return $this->accessible;
    }


    /**
     * @return void
     */
    public function clearRelatives()
    {
        $this->parent = null;
        $this->children = null;
    }


    /**
     * @return void
     */
    public function markAsNew()
    {
        $this->uid = null;
    }
}
