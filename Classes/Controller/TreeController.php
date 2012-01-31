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

		echo Tx_PtExtbase_Tree_ExtJsJsonTreeWriter::getInstance()->writeTree($tree);
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
	 * Moves node into node
	 *
	 * This action can be used for a drag'n'drop of a node "onto" another node.
	 *
	 * @param Tx_PtExtbase_Tree_Node $node Node to be moved
	 * @param Tx_PtExtbase_Tree_Node $targetNode Node where moved node should be put into
	 */
	public function moveNodeIntoAction(Tx_PtExtbase_Tree_Node $node, Tx_PtExtbase_Tree_Node $targetNode) {
		$tree = $this->treeBuilder->buildTreeForNamespace($this->treeNameSpace);
		$tree->moveNode($node, $targetNode);
		$this->nestedSetTreeStorage->saveTree($tree);

		$this->persistenceManager->persistAll();
		exit();
	}



	/**
	 * Moves node given by ID after node given by ID as child of the very same node
	 *
	 * @param Tx_PtExtbase_Tree_Node $node Node that has to be moved
	 * @param Tx_PtExtbase_Tree_Node $targetNode Node where moved node should be put before
	 */
	public function moveNodeAfterAction(Tx_PtExtbase_Tree_Node $node, Tx_PtExtbase_Tree_Node $targetNode) {
		$tree = $this->treeBuilder->buildTreeForNamespace($this->treeNameSpace);
		$tree->moveNodeAfterNode($node, $targetNode);
		$this->nestedSetTreeStorage->saveTree($tree);

		$this->persistenceManager->persistAll();
		exit();
	}



	/**
	 * Moves node before targetNode as child of the very same node.
	 *
	 * @param Tx_PtExtbase_Tree_Node $node ID of node that was moved
	 * @param Tx_PtExtbase_Tree_Node $targetNode ID of category where moved category should be put after
	 */
	public function moveNodeBeforeAction(Tx_PtExtbase_Tree_Node $node, Tx_PtExtbase_Tree_Node $targetNode) {
		$tree = $this->treeBuilder->buildTreeForNamespace($this->treeNameSpace);
		$tree->moveNodeBeforeNode($node, $targetNode);
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
		exit();
	}
}
