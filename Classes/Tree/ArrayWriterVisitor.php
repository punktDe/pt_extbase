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
 * Class implements a visitor for getting PHP array notation of tree
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tree_ArrayWriterVisitor implements Tx_PtExtbase_Tree_TreeWalkerVisitorInterface {

    /**
     * Holds an array representing array structure of nodes
     *
     * How should array structure look like at the end:
     *
     * array (
     *      uid         => 1,
     *      label       => 'root',
     *      children    => array (
     *          1       => array (
     *              uid         => 2,
     *              label       => 'first child',
     *              children    => array(...)
     *          ),
     *          2       => array (...),
     *          ...
     *      )
     * )
     *
     * @var array
     */
    protected $nodeArray = array();



    /**
     * Holds stack of unfinished nodes
     *
     * @var Tx_PtExtbase_Tree_Stack
     */
    protected $nodeStack;



    /**
     * Constructor for visitore
     */
    public function __construct() {
        $this->nodeStack = new Tx_PtExtbase_Tree_Stack();
    }



	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doFirstVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @param int &$index Holds the visitation index of treewalker
     * @param int &$level Holds level of visitation in tree, starting at 1
     */
    public function doFirstVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index, &$level) {
		$arrayForNode = array(
            'uid' => $node->getUid(),
            'label' => $node->getLabel(),
            'children' => array()
        );

        $this->nodeStack->push($arrayForNode);
	}


	
	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doLastVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @param int &$index Holds the visitation index of treewalker
     * @param int &$level Holds level of visitation in tree, starting at 1
     */
    public function doLastVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index, &$level) {
        $currentNode = $this->nodeStack->top();
        $this->nodeStack->pop();
        if (!$this->nodeStack->isEmpty()) {
            $parentNode = $this->nodeStack->top();
            $this->nodeStack->pop();
            $parentNode['children'][] = $currentNode;
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