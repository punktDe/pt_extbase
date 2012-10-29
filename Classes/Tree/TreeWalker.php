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
 * TreeWalker itself is doing nothing but traversing a tree in given order (depth-first or breadth-first).
 * You have to register one ore more visitors which are called whenever a node is visited for the first or last
 * time, all node-manipulation logic is implemented within those visitors.
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tree_TreeWalker {
	
	/**
	 * Holds a set of strategies that are invoked, whenever a node is visited
	 *
	 * @var array<Tx_PtExtbase_Tree_TreeWalkerVisitorInterface>
	 */
	protected $visitors;



    /**
     * If set to a value different to -1, we stop traversing tree, if we pass given depth
     *
     * @var int
     */
    protected $restrictedDepth = -1;
	
	
	
	/**
	 * Constructor for tree walker
	 *
	 * @param unknown_type $visitors
	 */
	public function __construct($visitors) {
		foreach($visitors as $visitor) {
			if (is_a($visitor, 'Tx_PtExtbase_Tree_TreeWalkerVisitorInterface')) {
				$this->visitors[] = $visitor;
			} else {
				throw new Exception('Given visitor does not implement Tx_PtExtbase_Tree_TreeWalkerVisitorInterface. 1307902730');
			}
		}
	}
	
	
	
	/**
	 * Traverses a tree depth-first search. Applying registered visitors whenever a node is visited.
	 *
	 * @param Tx_PtExtbase_Tree_TraversableInterface $tree
	 */
	public function traverseTreeDfs(Tx_PtExtbase_Tree_TraversableInterface $tree) {
		$index = 1;

        // If we should respect depth-restriction for tree traversal, we set property
        if ($tree->getRespectRestrictedDepth()) {
            $this->restrictedDepth = $tree->getRestrictedDepth();
        }

        $level = 1;
        if ($this->restrictedDepth === -1 || $level <= $this->restrictedDepth) {
		    $this->dfs($tree->getRoot(), $index, $level);
        }
	}
	
	
	
	/**
	 * Helper method for doing a depth-first search on a node
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
	 * @param int &$index Referenced value of visitation index. Will be increased with every node visitation.
     * @param int &$level Current level of visit in the tree starting at 1
	 */
	protected function dfs(Tx_PtExtbase_Tree_NodeInterface $node, &$index, &$level = 1) {

		if($node->isAccessible()) {
			$this->doFirstVisit($node, $index, $level);
			$index = $index + 1;

			if ($node->getChildrenCount() > 0) {
				$level = $level + 1;
				if ($this->restrictedDepth === -1 || $level <= $this->restrictedDepth) {
					foreach ($node->getChildren() as $child) {
						/* @var $child Tx_PtExtbase_Tree_NodeInterface */
						$this->dfs($child, $index, $level);
					}
				}
				$level = $level - 1;
			}

			$this->doLastVisit($node, $index, $level);
			$index = $index + 1;
		}
	}



    /**
     * Returns true, if given level is NOT deeper than restricted depth set in treewalker.
     *
     * @param $level Level to be compared with restricted depth
     * @return bool True, if level is not deeper than restricted depth
     */
    protected function levelIsBiggerThanRestrictedDepth($level) {
        error_log( "level: " . $level . " restricted depth: " . $this->restrictedDepth );
        if ($this->restrictedDepth === -1) {
            return false;
        } elseif ($level > $this->restrictedDepth) {
            return true;
        } else {
            return false;
        }
    }
	
	

    /**
     * Calls registered visitors whenever a node is visited for the first time
     *
     * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @param $index
     */
	protected function doFirstVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index) {
		foreach ($this->visitors as $visitor) {
			$visitor->doFirstVisit($node, $index);
		}
	}
	
	

    /**
     * Calls registered visitors whenever a node is visited for the last time
     *
     * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @param $index
     */
	protected function doLastVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index) {
		foreach ($this->visitors as $visitor) {
			$visitor->doLastVisit($node, $index);
		}
	}
	
	
	
	/**
	 * Traverses a tree breadth-first search. Applying registered visitors whenever a node is visited
	 *
	 * @param Tx_PtExtbase_Tree_TraversableInterface $tree
	 */
	public function traverseTreeBfs(Tx_PtExtbase_Tree_TraversableInterface $tree) {
		// TODO implement me!
        throw new Exception('Traversing tree BFS is not yet implemented!');
	}
	
}
?>