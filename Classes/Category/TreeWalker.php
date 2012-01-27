<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
*  All rights reserved
*
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
 * Generic algorithm for traversing trees
 *
 * @package Domain
 * @subpackage Model
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_Yag_Domain_Model_TreeWalker {
	
	/**
	 * Holds a set of strategies that are invoked, whenever a node is visited
	 *
	 * @var array<Tx_Yag_Domain_Model_TreeWalkerVisitorInterface>
	 */
	protected $visitors;
	
	
	
	/**
	 * Constructor for tree walker
	 *
	 * @param unknown_type $visitors
	 */
	public function __construct($visitors) {
		foreach($visitors as $visitor) {
			if (is_a($visitor, 'Tx_Yag_Domain_Model_TreeWalkerVisitorInterface')) {
				$this->visitors[] = $visitor;
			} else {
				throw new Exception('Given visitor does not implement Tx_Yag_Domain_Model_TreeWalkerVisitorInterface. 1307902730');
			}
		}
	}
	
	
	
	/**
	 * Traverses a tree depth-first search. Applying registered visitors whenever a node is visited.
	 *
	 * @param Tx_Yag_Domain_Model_TraversableInterface $tree
	 */
	public function traverseTreeDfs(Tx_Yag_Domain_Model_TraversableInterface $tree) {
		$index = 1;
		$this->dfs($tree->getRoot(), $index);
	}
	
	
	
	/**
	 * Helper method for doing a depth-first search on a node
	 *
	 * @param Tx_Yag_Domain_Model_NodeInterface $node
	 * @param int &$index Referenced value of visitation index. Will be increased with every node visitation.
	 */
	protected function dfs(Tx_Yag_Domain_Model_NodeInterface $node, &$index) {
		$this->doFirstVisit($node, $index);
		$index = $index + 1;
		foreach ($node->getChildren() as $child) { /* @var $child Tx_Yag_Domain_Model_NodeInterface */
			$this->dfs($child, $index);
		}
		$this->doLastVisit($node, $index);
		$index = $index + 1;
	}
	
	
	
	protected function doFirstVisit(Tx_Yag_Domain_Model_NodeInterface $node, &$index) {
		foreach ($this->visitors as $visitor) {
			$visitor->doFirstVisit($node, $index);
		}
	}
	
	
	
	protected function doLastVisit(Tx_Yag_Domain_Model_NodeInterface $node, &$index) {
		foreach ($this->visitors as $visitor) {
			$visitor->doLastVisit($node, $index);
		}
	}
	
	
	
	/**
	 * Traverses a tree breadth-first search. Applying registered visitors whenever a node is visited
	 *
	 * @param Tx_Yag_Domain_Model_TraversableInterface $tree
	 */
	public function traverseTreeBfs(Tx_Yag_Domain_Model_TraversableInterface $tree) {
		
	}
	
}
 
?>