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
 * Class implements a visitor for getting an extJs tree compatible array
 *
 * @package Tree
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Tree_ExtJsJsonWriterVisitor implements  Tx_PtExtbase_Tree_TreeWalkerVisitorInterface {



	protected $nodeArray = array();


	/**
	 * Holds stack of unfinished nodes
	 *
	 * @var Tx_PtExtbase_Tree_Stack
	 */
	protected $nodeStack;


	/**
	 * Constructor for visitor
	 */
	public function __construct() {
		$this->nodeStack = new Tx_PtExtbase_Tree_Stack();
	}



	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doFirstVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
	 * @param int &$index Visitation index of treewalker
	 */
	public function doFirstVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index) {
		$arrayForNode = array(
            'id' => $node->getUid(),
            'text' => $node->getLabel(),
            'children' => array(),
            'leaf' => !$node->hasChildren()
        );

        $this->nodeStack->push($arrayForNode);
	}


	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doLastVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
	 * @param int &$index Visitation index of treewalker
	 */
	public function doLastVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index) {
		$currentNode = $this->nodeStack->top();
		$this->nodeStack->pop();

		if (!$this->nodeStack->isEmpty()) {
			$parentNode = $this->nodeStack->top();
			$this->nodeStack->pop();
			$parentNode['children'][] = $currentNode;
			$currentNode['leaf'] = 'false';
			$this->nodeStack->push($parentNode);
		} else {
			$this->nodeArray = $currentNode;
		}
	}


	/**
	 * Returns array structure for visited nodes
	 *
	 * @return array
	 */
	public function getNodeArray() {
		return $this->nodeArray;
	}

}
?>