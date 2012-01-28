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
 * Storage for nested set trees
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tree_NestedSetTreeStorage implements Tx_PtExtbase_Tree_TreeStorageInterface {

	/**
	 * Holds an instance of category repository
	 *
	 * @var Tx_PtExtbase_Tree_NodeRepository
	 */
	protected $nodeRepository;
	
	
	
	/**
	 * Constructor for nested set tree storage
     *
     * @param Tx_PtExtbase_Tree_NodeRepository $nodeRepository Node repository to store nodes in
	 */
	public function __construct(Tx_PtExtbase_Tree_NodeRepository $nodeRepository) {
        $this->nodeRepository = $nodeRepository;
	}



	/**
	 * Removes deleted nodes of a given tree from node repository
	 *
	 * @param Tx_PtExtbase_Tree_NestedSetTreeInterface $tree Tree whose deleted nodes should be removed from repository
	 */
	protected function removeDeletedNodesOfGivenTree(Tx_PtExtbase_Tree_NestedSetTreeInterface $tree) {
		foreach ($tree->getDeletedNodes() as $deletedNode) {
			$this->nodeRepository->remove($deletedNode);
		}
	}
	
	
	
	/**
	 * Adds added nodes of a given tree to node repository
	 *
	 * @param Tx_PtExtbase_Tree_NestedSetTreeInterface $tree
	 */
	protected function addAddedNodesOfGivenTree(Tx_PtExtbase_Tree_NestedSetTreeInterface $tree) {
		foreach ($tree->getAddedNodes() as $addedNode) {
			$this->nodeRepository->add($addedNode);
		}
	}



    /**
     * Saves a tree to storage
     *
     * @param Tx_PtExtbase_Tree_TreeInterface $tree
     */
    public function saveTree(Tx_PtExtbase_Tree_TreeInterface $tree) {

        if (!is_a($tree, Tx_PtExtbase_Tree_NestedSetTreeInterface)) {
            throw new Exception('Tx_PtExtbase_Tree_NestedSetTreeStorage can only persist trees that implement Tx_PtExtbase_Tree_NestedSetTreeInterface! 1327695444');
        }

        $this->removeDeletedNodesOfGivenTree($tree);
        $this->addAddedNodesOfGivenTree($tree);
        $nodes = $tree->getRoot()->getSubCategories();
        foreach ($nodes as $node) { /* @var $node Tx_PtExtbase_Tree_NodeInterface */
            if ($node->getUid() > 0) {
                // we only update categories, that have been persisted!
                $this->nodeRepository->update($node);
            }
        }

        $this->nodeRepository->updateOrAdd($tree->getRoot());

    }

}
?>