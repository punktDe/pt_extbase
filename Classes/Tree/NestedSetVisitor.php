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
 * Class implements a visitor for doing nested set enumeration on a tree.
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tree_NestedSetVisitor implements Tx_PtExtbase_Tree_TreeWalkerVisitorInterface {

    /**
     * Holds an array of nodes that has already been visited
     *
     * @var array<Tx_PtExtbase_Tree_NestedSetNodeInterface>
     */
    protected $visitedNodes = array();



	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doFirstVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @param int &$index Holds the visitation index of treewalker
     * @param int &$level Holds level of visitation in tree, starting at 1
     */
    public function doFirstVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index, &$level) {
		$node->setLft($index);
	}


	
	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doLastVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @param int &$index Holds the visitation index of treewalker
     * @param int &$level Holds level of visitation in tree, starting at 1
     */
    public function doLastVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index, &$level) {
		$node->setRgt($index);
        $this->visitedNodes[] = $node;
	}



    /**
     * Returns array of visited nodes
     *
     * @return array<Tx_PtExtbase_Tree_NestedSetNodeInterface>
     */
    public function getVisitedNodes() {
        return $this->visitedNodes;
    }
	
}
?>