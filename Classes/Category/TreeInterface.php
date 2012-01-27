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
 * Interface for classes that implement a generic tree
 *
 * @package Category
 * @author Michael Knoll <mimi@kaktusteam.de>
 * @author Daniel Lienert <daniel@lienert.cc>
 */
interface Tx_PtExtbase_Category_TreeInterface extends Tx_PtExtbase_Category_TraversableInterface {

	/**
	 * Returns node for a given uid
	 *
	 * @param int $uid Uid of node
	 * @return Tx_PtExtbase_Category_Category
	 */
	public function getNodeByUid($uid);
	
	
	
	/**
	 * Removes a node from the tree
	 *
	 * @param Tx_PtExtbase_Category_Category $node
     * @return Tx_PtExtbase_Category_TreeInterface
	 */
	public function deleteNode(Tx_PtExtbase_Category_Category $node);



	/**
	 * Moves a node given as first parameter into a node given as second parameter
	 *
	 * @param Tx_PtExtbase_Category_Category $nodeToBeMoved Node to be moved
	 * @param Tx_PtExtbase_Category_Category $targetNode Node to move moved node into
     * @return Tx_PtExtbase_Category_TreeInterface
	 */
	public function moveNode(Tx_PtExtbase_Category_Category $nodeToBeMoved, Tx_PtExtbase_Category_Category $targetNode);
	
	
	
	/**
	 * Moves a node given as a first parameter in front of a node given as a second parameter 
	 *
	 * @param Tx_PtExtbase_Category_Category $nodeToBeMoved
	 * @param Tx_PtExtbase_Category_Category $nodeToMoveBefore
     * @return Tx_PtExtbase_Category_TreeInterface
	 */
	public function moveNodeBeforeNode(Tx_PtExtbase_Category_Category $nodeToBeMoved, Tx_PtExtbase_Category_Category $nodeToMoveBefore);
	
	
	
	/**
	 * Moves a node given as first parameter after a node given as second parameter
	 *
	 * @param Tx_PtExtbase_Category_Category $nodeToBeMoved
	 * @param Tx_PtExtbase_Category_Category $nodeToMoveAfter
     * @return Tx_PtExtbase_Category_TreeInterface
	 */
	public function moveNodeAfterNode(Tx_PtExtbase_Category_Category $nodeToBeMoved, Tx_PtExtbase_Category_Category $nodeToMoveAfter);
	
	
	
	/**
	 * Adds a given node into a given parent node
	 *
	 * @param Tx_PtExtbase_Category_Category $newNode Node to be added to tree
	 * @param Tx_PtExtbase_Category_Category $parentNode Node to add new node into
     * @return Tx_PtExtbase_Category_TreeInterface
	 */
	public function insertNode(Tx_PtExtbase_Category_Category $newNode, Tx_PtExtbase_Category_Category $parentNode);

}
?>