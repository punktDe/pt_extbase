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
 * Interface for repositories that handle nodes in a nested set tree
 *
 * @package Category
 * @author Michael Knoll <mimi@kaktusteam.de>
 * @author Daniel Lienert <daniel@lienert.cc>
 */
interface Tx_PtExtbase_Category_NodeRepositoryInterface {

	/**
	 * Returns ancestors of the root node of given node.
	 * 
	 * Nodes are ordered by left-value 
	 *
	 * @param Tx_PtExtbase_Category_NodeInterface $node
     * @return Tx_Extbase_Persistence_ObjectStorage<Tx_PtExtbase_Category_NodeInterface>
	 */
	public function findByRootOfGivenNodeUid(Tx_PtExtbase_Category_NodeInterface $node);
	
}
?>