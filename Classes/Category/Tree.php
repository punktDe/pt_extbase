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
 * Class implements Category Tree domain object
 *
 * @package Category
 * @author Michael Knoll <mimi@kaktusteam.de>
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Category_Tree implements Tx_PtExtbase_Category_TreeInterface {

	/**
	 * Holds reference of root node for this tree
	 *
	 * @var Tx_PtExtbase_Category_Node
	 */
	protected $rootNode = null;
	
	
	
	/**
	 * Holds a hashmap of nodes stored inside this tree
	 *
	 * @var array
	 */
	protected $treeMap = array();
	
	
	
	/**
	 * Holds a reference to a treewalker that updates nested set orderings
	 *
	 * @var Tx_PtExtbase_Category_TreeWalker
	 */
	protected $nsTreeWalker;
	
	
	
	/**
	 * Holds a reference to a treewalker that updates treemap
	 *
	 * @var Tx_PtExtbase_Category_TreeWalker
	 */
	protected $treeMapTreeWalker;
	
	
	
	/**
	 * Holds a list of deleted nodes
	 *
	 * @var array
	 */
	protected $deletedNodes = array();
	
	
	
	/**
	 * Holds a list of added nodes
	 *
	 * @var array
	 */
	protected $addedNodes = array();
	
	
	
	/**
	 * Factory method for instantiating a tree for a given root node
	 *
	 * @param Tx_PtExtbase_Category_Node $rootNode
	 * @return Tx_PtExtbase_Category_Tree
	 */
	public static function getInstanceByRootNode(Tx_PtExtbase_Category_Node $rootNode = null) {
		$tree = new Tx_PtExtbase_Category_Tree($rootNode);
		$nsTreeWalker = new Tx_PtExtbase_Category_TreeWalker(array(new Tx_PtExtbase_Category_NestedSetVisitor()));
		$tree->injectNsUpdateTreeWalker($nsTreeWalker);
		$tree->updateCategoryTree();
		return $tree;
	}
	
	
	
	/**
	 * Constructor for Category Tree
	 *
	 * @param Tx_PtExtbase_Category_Node $rootNode Root node for category tree
	 */
	private function __construct(Tx_PtExtbase_Category_Node $rootNode = null){
		$this->rootNode = $rootNode;
		$this->initTreeMap();
	}
	
	
	
	/**
	 * Injects a treewalker for updating nested set numbering
	 *
	 * @param Tx_PtExtbase_Category_TreeWalker $treeWalker
	 */
	public function injectNsUpdateTreeWalker(Tx_PtExtbase_Category_TreeWalker $treeWalker) {
		$this->nsTreeWalker = $treeWalker;
	}
	
	
	
	/**
	 * Returns root node of this category tree
	 *
	 * @return Tx_PtExtbase_Category_Node
	 */
	public function getRoot() {
		return $this->rootNode;
	}
	
	
	
	/**
	 * Returns node for a given uid
	 *
	 * @param int $uid Uid of node
	 * @return Tx_PtExtbase_Category_Node
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
	 * Returns a list of added nodes
	 *
	 * @return array
	 */
	public function getAddedNodes() {
		return $this->addedNodes;
	}
	
	
	
	/**
	 * Removes a node from the tree
	 *
	 * @param Tx_PtExtbase_Category_Node $node
	 */
	public function deleteNode(Tx_PtExtbase_Category_Node $node) {
		$subNodes = $node->getSubCategories();
		foreach($subNodes as $subnode) {
			$this->removeNodeFromTreeMap($subnode);
		}
		$this->removeNodeFromTreeMap($node);
		
		$node->getParent()->removeChild($node);
		
		$this->updateCategoryTree();
	}
	
	
	
	/**
	 * Moves a node given as first parameter into a node given as second parameter
	 *
	 * @param Tx_PtExtbase_Category_Node $nodeToBeMoved Node to be moved
	 * @param Tx_PtExtbase_Category_Node $targetNode Node to move moved node into
	 */
	public function moveNode(Tx_PtExtbase_Category_Node $nodeToBeMoved, Tx_PtExtbase_Category_Node $targetNode) {
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
		
		$this->updateCategoryTree();
	}
	
	
	
	/**
	 * Moves a node given as a first parameter in front of a node given as a second parameter 
	 *
	 * @param Tx_PtExtbase_Category_Node $nodeToBeMoved
	 * @param Tx_PtExtbase_Category_Node $nodeToMoveBefore
	 */
	public function moveNodeBeforeNode(Tx_PtExtbase_Category_Node $nodeToBeMoved, Tx_PtExtbase_Category_Node $nodeToMoveBefore) {
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
		$this->updateCategoryTree();
	}
	
	
	
	/**
	 * Moves a node given as first parameter after a node given as second parameter
	 *
	 * @param Tx_PtExtbase_Category_Node $nodeToBeMoved
	 * @param Tx_PtExtbase_Category_Node $nodeToMoveAfter
	 */
	public function moveNodeAfterNode(Tx_PtExtbase_Category_Node $nodeToBeMoved, Tx_PtExtbase_Category_Node $nodeToMoveAfter) {
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
        $this->updateCategoryTree();
	}
	
	
	
	/**
	 * Adds a given node into a given parent node
	 *
	 * @param Tx_PtExtbase_Category_Node $newNode Node to be added to tree
	 * @param Tx_PtExtbase_Category_Node $parentNode Node to add new node into
	 */
	public function insertNode(Tx_PtExtbase_Category_Node $newNode, Tx_PtExtbase_Category_Node $parentNode) {
		$parentNode = $this->getNodeByUid($parentNode->getUid());
		$parentNode->addChild($newNode);
		$newNode->setParent($parentNode);
		$newNode->setRoot($parentNode->getRoot());
		$this->addNodeToAddedNodes($newNode);
		$this->updateCategoryTree();
	}
	
	
	
	/**
	 * Initializes the tree map for this tree
	 */
	protected function initTreeMap() {
		$this->treeMap = array();
		if ($this->rootNode !== null) {
			$this->addNodeToTreeMap($this->rootNode);
			foreach ($this->rootNode->getSubCategories() as $node) {
				$this->addNodeToTreeMap($node);
			}
		}
	}
	
	
	
	/**
	 * Adds a node to tree map for this tree
	 *
	 * @param Tx_PtExtbase_Category_Node $node Node to be added to tree map
	 */
	protected function addNodeToTreeMap(Tx_PtExtbase_Category_Node $node) {
		$this->treeMap[$node->getUid()] = $node;
	}
	
	
	
	/**
	 * Removes a node from the tree map
	 *
	 * @param Tx_PtExtbase_Category_Node $node Node to be removed from tree map
	 */
	protected function removeNodeFromTreeMap(Tx_PtExtbase_Category_Node $node) {
		if (array_key_exists($node->getUid(), $this->treeMap)) {
			unset($this->treeMap[$node->getUid()]);
		}
		$this->addNodeToDeletedNodes($node);
	}
	
	
	
	/**
	 * Adds a node to list of deleted nodes
	 *
	 * @param Tx_PtExtbase_Category_Node $node Node to be deleted
	 */
	protected function addNodeToDeletedNodes(Tx_PtExtbase_Category_Node $node) {
		$this->deletedNodes[] = $node;
	}
	
	
	
	/**
	 * Adds a node to list of added nodes
	 *
	 * @param Tx_PtExtbase_Category_Node $node Node to be added to list of added nodes
	 */
	protected function addNodeToAddedNodes(Tx_PtExtbase_Category_Node $node) {
	     $this->addedNodes[] = $node;	
	}
	
	
	
	/**
	 * Checks whether given node is in tree
	 *
	 * @param Tx_PtExtbase_Category_Node $node Node to check for whether it's in the tree
	 * @param string $errMessage An error message to be displayed, if node is not in tree
	 */
	protected function checkForNodeBeingInTree(Tx_PtExtbase_Category_Node $node, $errMessage = 'Node is not found in current tree! 1307646533 ') {
	    if (!array_key_exists($node->getUid(), $this->treeMap)) {
            throw new Exception($errMessage . ' node UID: ' . $node->getUid() . print_r(array_keys($this->treeMap),true));
        }
	}
	
	
	
	/**
	 * Updates tree after any changes took place
	 */
	protected function updateCategoryTree() {
		if ($this->rootNode !== null) {
		    $this->nsTreeWalker->traverseTreeDfs($this);
		}
	}
	
	
	
	/**
	 * Renders a category tree to a ul html element (For debugging)
	 *
	 * @return string
	 */
	public function toString() {
		return '<ul>' . $this->rootNode->toString() . '</ul>';
	}
	
}
?>