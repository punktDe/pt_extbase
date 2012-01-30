<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2012 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
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
 * @author Daniel Lienert
 */

class Tx_PtExtbase_Controller_TreeController extends Tx_Extbase_MVC_Controller_ActionController {


	/**
	 * @var Tx_PtExtbase_Tree_TreeBuilder
	 */
	protected $treeBuilder;


	/**
	 * @var Tx_PtExtbase_Tree_NestedSetTreeStorage
	 */
	protected $nestedSetTreeStorage;


	/**
	 * @var Tx_Extbase_Persistence_Manager
	 */
	protected $persistenceManager;


	/**
	 * @var Tx_PtExtbase_Tree_NodeRepository
	 */
	protected $nodeRepository;


	/**
	 * @var string tree namespace
	 */
	protected $treeNameSpace;


	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$this->initializeSettings();

		$this->nodeRepository = t3lib_div::makeInstance('Tx_PtExtbase_Tree_NodeRepository');
		$this->treeBuilder = new Tx_PtExtbase_Tree_TreeBuilder($this->nodeRepository);
		$this->nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($this->nodeRepository);
		$this->persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
	}


	public function initializeSettings() {
		/**
		 * @todo The tree namespace should be set by the viewHelper
		 */
		$this->treeNameSpace = 'tx_ptextbase_tests_testNamespace';
	}



	/**
	 * Get tree or subtree when node is given
	 *
	 * @dontvalidate
	 * @param Tx_PtExtbase_Tree_Node $node
	 */
	public function getTreeAction(Tx_PtExtbase_Tree_Node $node = NULL) {

		if($node) {
			$tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($node);
		} else {
			$tree = $this->treeBuilder->buildTreeForNamespace($this->treeNameSpace);
		}

		echo Tx_PtExtbase_Tree_JsonTreeWriter::getInstance()->writeTree($tree);
		exit;
	}



	/**
	 * @param Tx_PtExtbase_Tree_Node $parent
	 * @param string $label
	 *
	 * @return integer id of new node or 0 if error
	 */
	public function addNodeAction(Tx_PtExtbase_Tree_Node $parent, $label) {
		
		$newNode = new Tx_PtExtbase_Tree_Node($label);
		$tree = $this->treeBuilder->buildTreeForNamespace($this->treeNameSpace);
		$tree->insertNode($newNode, $parent);
		$this->nestedSetTreeStorage->saveTree($tree);

		$this->persistenceManager->persistAll();

		echo $newNode->getUid() > 0 ? $newNode->getUid() : 0;
		exit();
	}



	/**
	 * @param Tx_PtExtbase_Tree_Node $node
	 */
	public function removeNodeAction(Tx_PtExtbase_Tree_Node $node) {
		$tree = $this->treeBuilder->buildTreeForNamespace($this->treeNameSpace);
		$tree->deleteNode($node);
		$this->nestedSetTreeStorage->saveTree($tree);

		$this->persistenceManager->persistAll();
		exit();
	}











	/**
	 * @param Tx_PtExtbase_Tree_Node $node
	 * @param string $label
	 */
	public function saveNodeAction(Tx_PtExtbase_Tree_Node $node, $label = '') {
		$node->setLabel($label);
		$this->nodeRepository->update($node);
		$this->persistenceManager->persistAll();
	}





	/**
	 * Remove Node and subnodes from tree
	 *
	 * @param int $nodeId
	 * @return string Rendered response
	 */
	public function removeCategoryAction($nodeId) {
		$nodeToBeRemoved = $this->categoryRepository->findByUid($nodeId);
		$categoryTree = $this->categoryTreeRepository->findByRootId($nodeToBeRemoved->getRoot());
		$categoryTree->deleteNode($nodeToBeRemoved);
		$this->categoryTreeRepository->update($categoryTree);
		$this->persistenceManager->persistAll();

		$this->forward('debug');
	}


	/**
	 * Moves category given by ID into category given by ID
	 *
	 * This action can be used for a drag'n'drop of a category "onto" another category.
	 *
	 * @param int $movedNodeId ID of node that is moved
	 * @param int $targetNodeId ID of category where moved node should be put into
	 * @return string Rendered response
	 */
	public function moveCategoryIntoAction($movedNodeId, $targetNodeId) {
		$categoryToBeMoved = $this->categoryRepository->findByUid($movedNodeId);
		$targetNode = $this->categoryRepository->findByUid($targetNodeId);
		$categoryTree = $this->categoryTreeRepository->findByRootId($categoryToBeMoved->getRoot());
		$categoryTree->moveNode($categoryToBeMoved, $targetNode);
		$this->categoryTreeRepository->update($categoryTree);
		$this->persistenceManager->persistAll();

		$this->forward('debug');
	}


	/**
	 * Moves category given by ID after category given by ID as subcategory of the very same category
	 *
	 * @param int $movedNodeId ID of the category that was moved
	 * @param int $targetNodeId ID of the category where moved category should be put before
	 * @return string Rendered response
	 */
	public function moveCategoryAfterAction($movedNodeId, $targetNodeId) {
		$categoryToBeMoved = $this->categoryRepository->findByUid($movedNodeId);
		$targetNode = $this->categoryRepository->findByUid($targetNodeId);
		$categoryTree = $this->categoryTreeRepository->findByRootId($categoryToBeMoved->getRoot());
		$categoryTree->moveNodeAfterNode($categoryToBeMoved, $targetNode);
		$this->categoryTreeRepository->update($categoryTree);
		$this->persistenceManager->persistAll();

		$this->forward('debug');
	}


	/**
	 * Moves category given by ID before category given by ID as subcategory of the very same category.
	 *
	 * @param int $movedNodeId ID of node that was moved
	 * @param int $targetNodeId ID of category where moved category should be put after
	 * @return string Rendered response
	 */
	public function moveCategoryBeforeAction($movedNodeId, $targetNodeId) {
		$categoryToBeMoved = $this->categoryRepository->findByUid($movedNodeId);
		$targetNode = $this->categoryRepository->findByUid($targetNodeId);
		$categoryTree = $this->categoryTreeRepository->findByRootId($categoryToBeMoved->getRoot());
		$categoryTree->moveNodeBeforeNode($categoryToBeMoved, $targetNode);
		$this->categoryTreeRepository->update($categoryTree);
		$this->persistenceManager->persistAll();

		$this->forward('debug');
	}

}
