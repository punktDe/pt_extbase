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
 * Class implements a nested sets tree
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Tree_Tree implements Tx_PtExtbase_Tree_NestedSetTreeInterface {

	/**
	 * Holds reference of root node for this tree
	 *
	 * @var Tx_PtExtbase_Tree_Node
	 */
	protected $rootNode = null;
	
	
	
	/**
	 * Holds a hashmap of nodes stored inside this tree
	 *
	 * @var array
	 */
	protected $treeMap = array();

	
	
	/**
	 * Holds a list of deleted nodes
	 *
	 * @var array
	 */
	protected $deletedNodes = array();



    /**
     * Holds namespace of tree
     *
     * @var string
     */
    protected $namespace;



    /**
     * If set to true, restricted depth will be respected when building the tree
     *
     * @var bool
     */
    protected $respectRestrictedDepth;



    /**
     * If set to a value > 0, tree will only be build up to given level.
     *
     * Level -1 = all levels are build
     * Level 1 = means, only root node will be build
     * Level 2 = root node and its children are build
     * ...
     *
     * @var int
     */
    protected $restrictedDepth;



    /**
     * Creates a new, empty tree with given namespace and a single root node labeled by given label.
     *
     * @param $namespace Namespace for tree
     * @param string $rootLabel Label for root node
     * @return Tx_PtExtbase_Tree_Tree
     */
    public static function getEmptyTree($namespace, $rootLabel = 'root') {
        $rootNode = new Tx_PtExtbase_Tree_Node($rootLabel);
        $rootNode->setNamespace($namespace);
        $tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
        return $tree;
    }
	
	
	
	/**
	 * Factory method for instantiating a tree for a given root node
	 *
	 * @param Tx_PtExtbase_Tree_Node $rootNode
	 * @return Tx_PtExtbase_Tree_Tree
	 */
	public static function getInstanceByRootNode(Tx_PtExtbase_Tree_Node $rootNode = null) {
		$tree = new Tx_PtExtbase_Tree_Tree($rootNode);
        if ($rootNode !== null) {
            $tree->setNamespace($rootNode->getNamespace());
        }
		return $tree;
	}
	
	
	
	/**
	 * Constructor for tree
	 *
	 * @param Tx_PtExtbase_Tree_Node $rootNode Root node for tree
	 */
	public function __construct(Tx_PtExtbase_Tree_Node $rootNode = null){
      $this->rootNode = $rootNode;
		$this->initTreeMap();
	}
	
	
	
	/**
	 * Returns root node of this tree
	 *
	 * @return Tx_PtExtbase_Tree_Node
	 */
	public function getRoot() {
		return $this->rootNode;
	}
	
	
	
	/**
	 * Returns node for a given uid
	 *
	 * @param int $uid Uid of node
	 * @return Tx_PtExtbase_Tree_Node
	 */
	public function getNodeByUid($uid) {
		if (array_key_exists($uid, $this->treeMap)) {
			return $this->treeMap[$uid];
		} else {
			return null;
		}
	}
	
	

	/**
	 * Returns array of deleted nodes
	 *
	 * @return array
	 */
	public function getDeletedNodes() {
		return $this->deletedNodes;
	}
	
	
	
	/**
	 * Removes a node from the tree
	 *
	 * @param Tx_PtExtbase_Tree_Node $node
	 */
	public function deleteNode(Tx_PtExtbase_Tree_Node $node) {
		$subNodes = $node->getSubNodes();
		foreach($subNodes as $subnode) {
			$this->removeNodeFromTreeMap($subnode);
		}
		$this->removeNodeFromTreeMap($node);
		
		$node->getParent()->removeChild($node);
	}
	
	
	
	/**
	 * Moves a node given as first parameter into a node given as second parameter
	 *
	 * @param Tx_PtExtbase_Tree_Node $nodeToBeMoved Node to be moved
	 * @param Tx_PtExtbase_Tree_Node $targetNode Node to move moved node into
	 */
	public function moveNode(Tx_PtExtbase_Tree_Node $nodeToBeMoved, Tx_PtExtbase_Tree_Node $targetNode) {
		$this->checkForNodeBeingInTree($targetNode);
		$this->checkForNodeBeingInTree($nodeToBeMoved);
		
		// We remove moved node from children of its parent node
		if ($nodeToBeMoved->hasParent()) {
		    $nodeToBeMoved->getParent()->getChildren()->detach($nodeToBeMoved);
		}
		
		// We add moved node to children of target node
		$targetNode->addChild($nodeToBeMoved);
		
		// We set parent of moved node to target node
		$nodeToBeMoved->setParent($targetNode);
	}
	
	
	
	/**
	 * Moves a node given as a first parameter in front of a node given as a second parameter 
	 *
	 * @param Tx_PtExtbase_Tree_Node $nodeToBeMoved
	 * @param Tx_PtExtbase_Tree_Node $nodeToMoveBefore
	 */
	public function moveNodeBeforeNode(Tx_PtExtbase_Tree_Node $nodeToBeMoved, Tx_PtExtbase_Tree_Node $nodeToMoveBefore) {
		$this->checkForNodeBeingInTree($nodeToBeMoved);
		$this->checkForNodeBeingInTree($nodeToMoveBefore);
		
		// We remove node from children of its parent node
		if ($nodeToBeMoved->hasParent()) {
			$nodeToBeMoved->getParent()->getChildren()->detach($nodeToBeMoved);
		}
		
		// We add node to children of parent node of target node
		if ($nodeToMoveBefore->hasParent()) {
		   $parentOfTargetNode = $nodeToMoveBefore->getParent();
		   $parentOfTargetNode->addChildBefore($nodeToBeMoved, $nodeToMoveBefore);
		   $nodeToBeMoved->setParent($parentOfTargetNode);
		} else {
			throw new Exception("Trying to move a node in front of a node that doesn't have a parent node! 1307646534");
		}
	}
	
	
	
	/**
	 * Moves a node given as first parameter after a node given as second parameter
	 *
	 * @param Tx_PtExtbase_Tree_Node $nodeToBeMoved
	 * @param Tx_PtExtbase_Tree_Node $nodeToMoveAfter
	 */
	public function moveNodeAfterNode(Tx_PtExtbase_Tree_Node $nodeToBeMoved, Tx_PtExtbase_Tree_Node $nodeToMoveAfter) {
	    $this->checkForNodeBeingInTree($nodeToBeMoved);
        $this->checkForNodeBeingInTree($nodeToMoveAfter);
        
        // We remove node from children of its parent node
        if ($nodeToBeMoved->hasParent()) {
            $nodeToBeMoved->getParent()->getChildren()->detach($nodeToBeMoved);
        }
        
        // We add node to children of parent node of target node
        if ($nodeToMoveAfter->hasParent()) {
           $parentOfTargetNode = $nodeToMoveAfter->getParent();
           $parentOfTargetNode->addChildAfter($nodeToBeMoved, $nodeToMoveAfter);
           $nodeToBeMoved->setParent($parentOfTargetNode);
        } else {
            throw new Exception("Trying to move a node after a node that doesn't have a parent node! 1307646535");
        }
	}
	
	
	
	/**
	 * Adds a given node into a given parent node
	 *
	 * @param Tx_PtExtbase_Tree_Node $newNode Node to be added to tree
	 * @param Tx_PtExtbase_Tree_Node $parentNode Node to add new node into
	 */
	public function insertNode(Tx_PtExtbase_Tree_Node $newNode, Tx_PtExtbase_Tree_Node $parentNode) {
		$internalParentNode = $this->getNodeByUid($parentNode->getUid());

		if(!$internalParentNode) {
			throw new Exception('The node with uid ' . $parentNode->getUid() . ' could not be found in the internal node map.', 1329643885);
		}

		$parentNode->addChild($newNode);
		$newNode->setPid($internalParentNode->getPid());
		$newNode->setParent($internalParentNode);
		$newNode->setRoot($internalParentNode->getRoot());
		$this->addNodeToTreeMap($newNode);
	}
	
	
	
	/**
	 * Initializes the tree map for this tree
	 */
	protected function initTreeMap() {
		$this->treeMap = array();
		if ($this->rootNode !== null) {
			$this->addNodeToTreeMap($this->rootNode);
			foreach ($this->rootNode->getSubNodes() as $node) {
				$this->addNodeToTreeMap($node);
			}
		}
	}
	
	
	
	/**
	 * Adds a node to tree map for this tree
	 *
	 * @param Tx_PtExtbase_Tree_Node $node Node to be added to tree map
	 */
	protected function addNodeToTreeMap(Tx_PtExtbase_Tree_Node $node) {
		$this->treeMap[$node->getUid()] = $node;
	}
	
	
	
	/**
	 * Removes a node from the tree map
	 *
	 * @param Tx_PtExtbase_Tree_Node $node Node to be removed from tree map
	 */
	protected function removeNodeFromTreeMap(Tx_PtExtbase_Tree_Node $node) {
		if (array_key_exists($node->getUid(), $this->treeMap)) {
			unset($this->treeMap[$node->getUid()]);
		}
		$this->addNodeToDeletedNodes($node);
	}
	
	
	
	/**
	 * Adds a node to list of deleted nodes
	 *
	 * @param Tx_PtExtbase_Tree_Node $node Node to be deleted
	 */
	protected function addNodeToDeletedNodes(Tx_PtExtbase_Tree_Node $node) {
		$this->deletedNodes[] = $node;
    }
	
	
	/**
	 * Checks whether given node is in tree
	 *
	 * @param Tx_PtExtbase_Tree_Node $node Node to check for whether it's in the tree
	 * @param string $errMessage An error message to be displayed, if node is not in tree
	 */
	protected function checkForNodeBeingInTree(Tx_PtExtbase_Tree_Node $node, $errMessage = 'Node is not found in current tree! 1307646533 ') {
	    if (!array_key_exists($node->getUid(), $this->treeMap)) {
            throw new Exception($errMessage . ' node UID: ' . $node->getUid() . implode(':',array_keys($this->treeMap)));
        }
	}
	
	

	/**
	 * Renders a tree to a ul html element (For debugging)
	 *
	 * @return string
	 */
	public function toString() {
		return '<ul>' . $this->rootNode->toString() . '</ul>';
	}



    /**
     * Returns namespace of tree
     *
     * @return string namespace
     */
    public function getNamespace() {
        return $this->namespace;
    }



    /**
     * Sets namespace of tree
     *
     * @param string $namespace
     */
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }



    /**
     * Setter for restricted depth.
     *
     * If depth is restricted, tree is build only to given level by tree builder.
     *
     * @param int $restrictedDepth
     */
    public function setRestrictedDepth($restrictedDepth) {
        $this->restrictedDepth = $restrictedDepth;
    }



    /**
     * Getter for restricted depth
     *
     * @return int Restricted depth
     */
    public function getRestrictedDepth() {
        return $this->restrictedDepth;
    }



    /**
     * Sets respect restricted depth to given value.
     *
     * If set to true, tree builder will respect restricted depth, when building tree.
     *
     * @param bool $respectRestrictedDepth
     */
    public function setRespectRestrictedDepth($respectRestrictedDepth=TRUE) {
        $this->respectRestrictedDepth = $respectRestrictedDepth;
    }



    /**
     * Returns true if restricted depth should be respected
     *
     * @return bool
     */
    public function getRespectRestrictedDepth() {
        return $this->respectRestrictedDepth;
    }

}
?>