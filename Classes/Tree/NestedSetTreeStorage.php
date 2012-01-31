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
	 * Holds an instance of node repository
	 *
	 * @var Tx_PtExtbase_Tree_NodeRepository
	 */
	protected $nodeRepository;



    /**
     * Holds instance of nested sets tree walker
     *
     * @var Tx_PtExtbase_Tree_NestedSetTreeWalker
     */
    protected $nestedSetTreeWalker;
	
	
	
	/**
	 * Constructor for nested set tree storage
     *
     * @param Tx_PtExtbase_Tree_NodeRepository $nodeRepository Node repository to store nodes in
	 */
	public function __construct(Tx_PtExtbase_Tree_NodeRepository $nodeRepository) {
        $this->nodeRepository = $nodeRepository;
        // TODO put this into creation method
        $this->nestedSetTreeWalker = Tx_PtExtbase_Tree_NestedSetTreeWalker::getInstance();
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

        $nodes = $this->nestedSetTreeWalker->traverseTreeAndGetNodes($tree);

        foreach ($nodes as $node) { /* @var $node Tx_PtExtbase_Tree_NodeInterface */
            $this->setTreeNamespaceOnNode($tree, $node);
            $this->nodeRepository->updateOrAdd($node);
        }

        $this->setTreeNamespaceOnNode($tree, $tree->getRoot());
        $this->nodeRepository->updateOrAdd($tree->getRoot());

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
     * Sets namespace of given tree on given node
     *
     * @param Tx_PtExtbase_Tree_NestedSetTreeInterface $tree
     * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @throws Exception
     */
    protected function setTreeNamespaceOnNode(Tx_PtExtbase_Tree_NestedSetTreeInterface $tree, Tx_PtExtbase_Tree_NodeInterface $node) {
        $namespace = $tree->getNamespace();
        if ($namespace !== null && $namespace !== '') {
            $node->setNamespace($namespace);
        } else {
            throw new Exception('Trying to store a node of a tree that has no namespace set! Namespace is required on a tree to be stored! 1327756309');
        }
    }

}
?>