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
 * @author Sebastian Helzle <helzle@punkt.de>
 */
class Tx_PtExtbase_Tree_JSTreeJsonWriterVisitor extends Tx_PtExtbase_Tree_ArrayWriterVisitor {

	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doFirstVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @param int &$index Holds the visitation index of treewalker
     * @param int &$level Holds level of visitation in tree, starting at 1
     */
    public function doFirstVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index, &$level) {

        $nodeUid = $node->getUid();
        $metadata = '';


		$arrayForNode = array(
            'data' => $node->getLabel(),
            'attr' => array(
                'id' => $node->getUid(),
                'data-meta' => trim($metadata)
            ),
            'children' => array()
        );

        $this->nodeStack->push($arrayForNode);
	}
	
}
?>