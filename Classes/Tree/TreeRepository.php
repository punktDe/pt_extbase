<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Michael Knoll <mimi@kaktusteam.de>
*           Daniel Lienert <daniel@lienert.cc>
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
 * Repository for Tx_PtExtbase_Tree_Tree
 *
 * Handles all actions for persisting trees
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tree_TreeRepository {

    /**
     * Holds instance of node repository
     *
     * @var Tx_PtExtbase_Tree_NodeRepositoryInterface
     */
    protected $nodeRepository;



    /**
     * Holds instance of tree builder
     *
     * @var Tx_PtExtbase_Tree_TreeBuilder
     */
    protected $treeBuilder;



    /**
     * Holds instance of tree storage
     *
     * @var Tx_PtExtbase_Tree_TreeStorageInterface
     */
    protected $treeStorage;



    /**
     * Constructor for tree repository
     *
     * @param Tx_PtExtbase_Tree_NodeRepositoryInterface $nodeRepository
     * @param Tx_PtExtbase_Tree_TreeBuilder $treeBuidler
     * @param Tx_PtExtbase_Tree_TreeStorageInterface $treeStorage
     */
    public function __construct(Tx_PtExtbase_Tree_NodeRepositoryInterface $nodeRepository, Tx_PtExtbase_Tree_TreeBuilder $treeBuidler, Tx_PtExtbase_Tree_TreeStorageInterface $treeStorage) {
        $this->nodeRepository = $nodeRepository;
        $this->treeBuilder = $treeBuidler;
        $this->treeStorage = $treeStorage;
    }


	/**
	 * Loads tree for a given namespace
	 *
	 * @param $namespace string Namespace to build tree for
	 * @param bool $respectEnableFields
	 * @param bool $removeInaccessibleSubTrees
	 * @return Tx_PtExtbase_Tree_Tree Tree build for given namespace
	 */
    public function loadTreeByNamespace($namespace, $respectEnableFields = TRUE, $removeInaccessibleSubTrees = FALSE) {
	    if ($removeInaccessibleSubTrees) {
		    return $this->treeBuilder->buildTreeForNamespaceWithoutInaccessibleSubtrees($namespace);
	    }
        return $this->treeBuilder->buildTreeForNamespace($namespace, $respectEnableFields);
    }



    /**
     * Updates given tree
     *
     * @param $tree Tx_PtExtbase_Tree_Tree Tree to be updated
     */
    public function update($tree) {
        $this->treeStorage->saveTree($tree);
    }



    /**
     * Returns an empty tree for given namespace and root label
     *
     * @param $namespace
     * @param string $rootLabel
     * @return Tx_PtExtbase_Tree_Tree Empty tree for given namespace and root label
     */
    public function getEmptyTree($namespace, $rootLabel = 'root') {
        return $this->treeBuilder->getEmptyTree($namespace, $rootLabel);
    }



    /**
     * Setter for respectRestrictedDepth.
     *
     * If set to true, respect restricted depth is set to true in trees returned by this repository
     *
     * @param bool $respectRestrictedDepth
     */
    public function setRespectRestrictedDepth($respectRestrictedDepth = TRUE) {
        $this->treeBuilder->setRespectRestrictedDepth($respectRestrictedDepth);
    }



	/**
	 * @param boolean $respectEnableFields
	 */
	public function setRespectEnableFields($respectEnableFields) {
		$this->treeBuilder->setRespectEnableFields($respectEnableFields);
	}
	
}
?>