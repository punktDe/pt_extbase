<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Michael Knoll <mimi@kaktusteam.de>
 *			  Daniel Lienert <daniel@lienert.cc>
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
 * Class implements node domain object
 *
 * Nodes are implemented as nested sets. Each node has a left and a right number, given
 * by a depth-first treewalk through the tree. Left is the number of first visit when traversing the tree,
 * right is the number of last visit when traversing the tree.
 *
 * You can now do some simple selects, when processing queries on tree:
 *
 * 1. Select all child-nodes from node with left = LEFT and right = RIGHT:
 *	 ... WHERE child-nodes.left > nodes.left AND child-nodes.right < node.RIGHT
 *
 * 2. Number of child-nodes
 *	 ... COUNT(*) ... WHERE --> see above
 *
 * For a detailed explanation of what's possible with nested sets see http://www.klempert.de/nested_sets/
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
	 * Holds a unique temporaray UID which is decreased every time, a temp uid is requested.
	 *
	 * @var int
	 */
	protected static $tmpUidIndex = -1;



	/**
	 * Returns a unique temporary UID for node
	 *
	 * @static
	 * @return int
	 */
	protected static function getNewTemporaryUid() {
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
	 * @var int $lft
	 */
	protected $lft;



	/**
	 * Nested sets right number of node
	 *
	 * @var int $rgt
	 */
	protected $rgt;



	/**
	 * Uid of root node in tree
	 *
	 * @var int $root
	 */
	protected $root;



	/**
	 * Holds refernce to parent node (null, if root)
	 *
	 * @var Tx_PtExtbase_Tree_Node
	 */
	protected $parent;



	/**
	 * Holds references to child-nodes
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_PtExtbase_Tree_Node>
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
	protected $accessible = TRUE;


	/**
	 * The constructor.
	 *
	 * @param string $label Label of node
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
	 * Getter for root node uid
	 *
	 * @return int
	 */
	public function getRoot() {
		return $this->root;
	}



	/**
	 * Setter for root node uid
	 *
	 * @param int $root
	 */
	public function setRoot($root) {
		$this->root = $root;
	}



	/**
	 * Getter nested sets right number
	 *
	 * @return int
	 */
	public function getRgt() {
		return $this->rgt;
	}



	/**
	 * Setter nested sets right number
	 *
	 * @param int $rgt
	 */
	public function setRgt($rgt) {
		$this->rgt = $rgt;
	}



	/**
	 * Getter nested sets left number
	 *
	 * @return int
	 */
	public function getLft() {
		return $this->lft;
	}



	/**
	 * Setter nested sets left number
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
	 * Setter for parent node
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
	 */
	public function setParent(Tx_PtExtbase_Tree_NodeInterface $node) {
		$this->parent = $node;
		if ($node->children == null)
			$node->children = new Tx_Extbase_Persistence_ObjectStorage();
		$node->children->attach($this);
	}



	/**
	 * Getter for parent node
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
		if (is_null($this->children)) {
			$this->children = new Tx_Extbase_Persistence_ObjectStorage();
		}
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
	 * Returns level of node (0 if node is root).
	 *
	 * Level is equal to depth
	 * of node in tree where root has depth 0.
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
	 * @return Tx_Extbase_Persistence_ObjectStorage
	 */
	public function getSubNodes() {
		$subNodes = new Tx_Extbase_Persistence_ObjectStorage();
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
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
	 */
	public function addChild(Tx_PtExtbase_Tree_NodeInterface $node) {
		// TODO this should not be necessary. Seems like this method is not invoked, if object is loaded from database
		if (is_null($this->children)) {
			$this->children = new Tx_Extbase_Persistence_ObjectStorage();
		}

		$this->children->attach($node);
		$node->parent = $this;
	}



	/**
	 * Adds a new child node after a given child node
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $newChildNode
	 * @param Tx_PtExtbase_Tree_NodeInterface $nodeToAddAfter
	 */
	public function addChildAfter(Tx_PtExtbase_Tree_NodeInterface $newChildNode, Tx_PtExtbase_Tree_NodeInterface $nodeToAddAfter) {
		$newChildren = new Tx_Extbase_Persistence_ObjectStorage();
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
	 * @param Tx_PtExtbase_Tree_NodeInterface $newChildNode
	 * @param Tx_PtExtbase_Tree_NodeInterface $nodeToAddBefore
	 */
	public function addChildBefore(Tx_PtExtbase_Tree_NodeInterface $newChildNode, Tx_PtExtbase_Tree_NodeInterface $nodeToAddBefore) {
		$newChildren = new Tx_Extbase_Persistence_ObjectStorage();
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
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
	 */
	public function removeChild(Tx_PtExtbase_Tree_NodeInterface $node) {
		$this->children->detach($node);
	}



	/**
	 * Returns true, if node has children
	 *
	 * @return bool Tru, if node has children
	 */
	public function hasChildren() {
		return ($this->children != null && $this->children->count() > 0);
	}



	/**
	 * Returns true, if node has a parent
	 *
	 * @return bool True, if node has parent node
	 */
	public function hasParent() {
		return !($this->parent === null);
	}



	/**
	 * Returns true, if node is root
	 *
	 * @return boolean True, if node is root
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
		$nodeString = '<li id=tx_ptextbase_tree_node_' . $this->uid . '>' . $this->label . ' [uid: ' . $this->uid . ' left: ' . $this->lft . '  right:' . $this->rgt . ']';

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
	public function setLabel($label) {
		$this->label = $label;
	}



	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}



	/**
	 * Sets namespace of node
	 *
	 * @param $namespace
	 */
	public function setNamespace($namespace) {
		$this->namespace = $namespace;
	}



	/**
	 * Returns namespace of node
	 *
	 * @return string Namespace of node
	 */
	public function getNamespace() {
		return $this->namespace;
	}


	/**
	 * @param boolean $accessible
	 */
	public function setAccessible($accessible) {
		$this->accessible = $accessible;
	}


	/**
	 * @return boolean
	 */
	public function isAccessible() {
		return $this->accessible;
	}


	/**
	 * @return void
	 */
	public function clearRelatives() {
		$this->parent = NULL;
		$this->children = NULL;
	}

}
?>