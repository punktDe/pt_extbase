<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Michael Knoll <mimi@kaktusteam.de>
*  			Daniel Lienert <daniel@lienert.cc>
*  			
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Class implements Category domain object
 * 
 * Categories are implemented as nested sets. Each category has a left and a right number, given
 * by a depth-first treewalk through the category tree. Left is the number of first visit when traversing the tree, 
 * right is the number of last visit when traversing the tree.
 * 
 * You can now do some simple selects, when processing queries on category tree:
 * 
 * 1. Select all subcategories from category with left = LEFT and right = RIGHT:
 *    ... WHERE subcategories.left > category.left AND subcategories.right < category.RIGHT
 * 
 * 2. Number of subcategories
 *    ... COUNT(*) ... WHERE --> see above
 * 
 * For a detailed explanation of what's possible with nested sets see http://www.klempert.de/nested_sets/
 * 
 * 
 * The tricky part here is covering this functionality with standard Extbase persistence stuff. Therefore
 * we have kind of an advanced repository which handles setting up nested category object-structure from
 * flat SQL table response.
 * 
 * 
 * Some conventions:
 * 
 * 1. Root: Every category has a root. If root == UID, then category is root.
 * 
 *
 * @package Domain
 * @subpackage Model
 * @author Michael Knoll <mimi@kaktusteam.de>
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Tree_Node
    extends Tx_Extbase_DomainObject_AbstractEntity
    implements Tx_PtExtbase_Tree_NestedSetNodeInterface {
	
	/**
     * Label for category
     *
     * @var string $label
     */
    protected $label;

    

    /**
     * Number of first visit of node in category tree
     *
     * @var int $lft
     */
    protected $lft;
    
    

    /**
     * Number of second visit of node in category tree
     *
     * @var int $rgt
     */
    protected $rgt;
    
    

    /**
     * ID of root node in category tree
     *
     * @var int $root
     */
    protected $root;
    
    
    
    /**
     * Holds refernce to parent category (null, if root)
     *
     * @var Tx_PtExtbase_Tree_Node
     */
    protected $parent;
    
    
    
    /**
     * Holds references to child categories
     *
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_PtExtbase_Tree_Node>
     */
    protected $children;


	/**
	 * The constructor.
	 *
	 * @param string $label Label of category
	 * @return void
	 */
	public function __construct($label = '') {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();

		if ($label != '') {
			$this->label = $label;
		}

		// We initialize lft and rgt as those values will be overwritten later, if this is not the root node
		$this->lft = 1;
		$this->rgt = 2;
	}

    
    
    /**
     * Initializes all Tx_Extbase_Persistence_ObjectStorage instances.
     *
     * @return void
     */
    protected function initStorageObjects() {
        $this->children = new Tx_Extbase_Persistence_ObjectStorage();
    }
    
    

    /*********************************************************************************************************
     * Default getters and setters used for persistence - return database values, no objects!
     *********************************************************************************************************/

    
    
    
    /**
     * Getter for root category id
     *
     * @return int
     */
    public function getRoot() {
    	return $this->root;
    }
    
    
    
    /**
     * Setter for root category id
     *
     * @param int $root
     */
    public function setRoot($root) {
    	$this->root = $root;
    }
    
    
    
    /**
     * Getter for second visit in category tree
     *
     * @return int
     */
    public function getRgt() {
    	return $this->rgt;
    }
    
    
    
    /**
     * Setter for second visit in category tree
     *
     * @param int $rgt
     */
    public function setRgt($rgt) {
    	$this->rgt = $rgt; 
    }
    
    
    
    /**
     * Getter for first visit in category tree
     *
     * @return int
     */
    public function getLft() {
    	return $this->lft;
    }
    
    
    
    /**
     * Setter for first visit in category tree
     *
     * @param int $lft
     */
    public function setLft($lft) {
    	$this->lft = $lft;
    }
    
    
    
    /*********************************************************************************************************
     * Getters and setters for advanced domain logic. NOT USED FOR PERSISTENCE!
     *********************************************************************************************************/
    
    
    /**
     * Setter for parent category
     *
     * @param Tx_PtExtbase_Tree_NodeInterface $category
     */
    public function setParent(Tx_PtExtbase_Tree_NodeInterface $category) {
    	$this->parent = $category;
    	if ($category->children == null)
    	   $category->children = new Tx_Extbase_Persistence_ObjectStorage();
    	$category->children->attach($this);
    }
    
    
    
    /**
     * Getter for parent category
     *
     * @return Tx_PtExtbase_Tree_Node
     */
    public function getParent() {
    	return $this->parent;	
    }


	/**
	 * @return Tx_Extbase_Persistence_ObjectStorage
	 */
	public function getChildren() {
		return $this->children;
	}


	/**
     * Get count of children recursively
     *
     * @return int
     */
    public function getChildrenCount() {
    	if (!is_null($this->children)) {
    	   return $this->children->count();
    	} else {
    		return 0;
    	}
    }
    
    
    
    /**
     * Returns level of category (0 if category is root). 
     * 
     * Level is equal to depth
     * of category in tree where root has depth 0.
     *
     * @return int
     */
    public function getLevel() {
        if ($this->parent == null) {
            return 0;
        } else {
            return 1 + $this->parent->getLevel();
        }
    }
    
    
    
    /**
     * Returns sub-categories in a flat list. The result is ordered 
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
     * @return Tx_Extbase_Persistence_ObjectStorage
     */
    public function getSubCategories() {
        $subCategories = new Tx_Extbase_Persistence_ObjectStorage();
        if ($this->children !== null && $this->children->count() > 0) {
           foreach ($this->children as $child) {
               $subCategories->attach($child);
               $subCategories->addAll($child->getSubCategories());
           }
        }
        return $subCategories;
    }
    
    
    
    /*********************************************************************************************************
     * Domain logic
     *********************************************************************************************************/
    
    /**
     * Adds a child category to children at end of children
     *
     * @param Tx_PtExtbase_Tree_NodeInterface $category
     */
    public function addChild(Tx_PtExtbase_Tree_NodeInterface $category) {
    	// TODO this should not be necessary. Seems like this method is not invoked, if object is loaded from database
    	if (is_null($this->children)) {
    		$this->children = new Tx_Extbase_Persistence_ObjectStorage();
    	}
    	
    	$this->children->attach($category);
    	$category->parent = $this;
    }
    
    
    
    /**
     * Adds a new child category after a given child category
     *
     * @param Tx_PtExtbase_Tree_NodeInterface $newChildCategory
     * @param Tx_PtExtbase_Tree_NodeInterface $categoryToAddAfter
     */
    public function addChildAfter(Tx_PtExtbase_Tree_NodeInterface $newChildCategory, Tx_PtExtbase_Tree_NodeInterface $categoryToAddAfter) {
    	$newChildren = new Tx_Extbase_Persistence_ObjectStorage();
    	foreach ($this->children as $child) {
    		$newChildren->attach($child);
    		if ($child == $categoryToAddAfter) {
    			$newChildren->attach($newChildCategory);
    		}
    	}
    	$this->children = $newChildren;
    }
    
    
    
    /**
     * Adds a new child category before a given child category
     *
     * @param Tx_PtExtbase_Tree_NodeInterface $newChildCategory
     * @param Tx_PtExtbase_Tree_NodeInterface $categoryToAddBefore
     * @param bool $updateLeftRight
     */
    public function addChildBefore(Tx_PtExtbase_Tree_NodeInterface $newChildCategory, Tx_PtExtbase_Tree_NodeInterface $categoryToAddBefore) {
    	$newChildren = new Tx_Extbase_Persistence_ObjectStorage();
    	foreach($this->children as $child) {
    		if ($child == $categoryToAddBefore) {
    			$newChildren->attach($newChildCategory);
    		}
    		$newChildren->attach($child);
    	}
    	$this->children = $newChildren;
    }
    
    
    
    /**
     * Removes given child category
     *
     * @param Tx_PtExtbase_Tree_NodeInterface $child
     */
    public function removeChild(Tx_PtExtbase_Tree_NodeInterface $child) {
    	$this->children->detach($child);
    }
    
    
    
    /**
     * Returns true, if category has children
     *
     * @return bool
     */
    public function hasChildren() {
    	return ($this->children != null && $this->children->count() > 0);
    }
    
    
    
    /**
     * Returns true, if category has a parent
     *
     * @return bool True, if category has parent category
     */
    public function hasParent() {
    	return !($this->parent === null);
    }
    
    
    
    /**
     * Returns true, if category is root
     *
     * @return boolean True, if category is root
     */
    public function isRoot() {
    	return $this->uid == $this->root;
    }
    
    
    
    /**
     * Renders a node as an li-element for debugging
     *
     * @return string
     */
    public function toString() {
    	$categoryString = '<li>(' . $this->uid . ') ' . $this->label . '[left: ' . $this->lft . '  right:' . $this->rgt . ']';
    	if ($this->hasChildren()) {
    		$categoryString .= '<ul>';
	    	foreach ($this->children as $child) {
	    		$categoryString .= $child->toString();
	    	}
	    	$categoryString .= '</ul>';
    	}
    	$categoryString .= '</li>';
    	return $categoryString;
    }
    
    
    
    /**
     * Returns sub nodes of this node
     *
     * @return Tx_Extbase_Persistence_ObjectStorage
     */
    public function getSubNodes() {
    	return $this->getSubCategories();
    }

	/**
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

}
?>