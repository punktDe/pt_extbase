<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2012 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Sebastian Helzle
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
 * Class implements actions for tree manipulation with ajax calls
 *
 * @author Daniel Lienert
 * @author Michael Knoll
 * @author Sebastian Helzle
 */
class Tx_PtExtbase_Controller_TreeController extends Tx_Extbase_MVC_Controller_ActionController {

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
	protected $treeNameSpace = 'tx_ptextbase_tree_default';


	/**
	 * @var string
	 */
	protected $nodeRepositoryClassName = 'Tx_PtExtbase_Tree_NodeRepository';


    /**
     * @var Tx_PtExtbase_Tree_TreeRepository
     */
    protected $treeRepository;

	/**
	 * @var boolean
	 */
	protected $treeRespectEnableFields;


	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {

		if (!TYPO3_AJAX) {
			die();
		}

		$this->restoreTreeSettingsFromSession();

		$treeRepositoryBuilder = Tx_PtExtbase_Tree_TreeRepositoryBuilder::getInstance();
		$treeRepositoryBuilder->setNodeRepositoryClassName($this->nodeRepositoryClassName);
		$this->treeRepository = $treeRepositoryBuilder->buildTreeRepository();

		$this->nodeRepository = $this->objectManager->get($this->nodeRepositoryClassName);

		$this->persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
	}



	/**
	 * @todo If several tree widgets are needed on the same page, provide an identifier and select the right repo from session
	 *
	 * Restore the repository settings from namespace
	 */
	public function restoreTreeSettingsFromSession() {

		$settings = Tx_PtExtbase_State_Session_Storage_SessionAdapter::getInstance()->read('Tx_PtExtbase_Tree_Configuration');
		$settings = array(
			'repository' => 'Tx_PtCertification_Domain_Repository_CategoryRepository',
			'namespace' => 'tx_ptcertification_domain_model_category',
			'respectEnableFields' => FALSE,
		);

		if(array_key_exists('repository', $settings)) {
			$nodeRepositoryClassName = $settings['repository'];
			if($nodeRepositoryClassName && class_exists($nodeRepositoryClassName)) {
				$this->nodeRepositoryClassName = $nodeRepositoryClassName;
			}
		}

		if(array_key_exists('namespace', $settings)) {
			$this->treeNameSpace = $settings['namespace'];
		}

		if(array_key_exists('respectEnableFields', $settings)) {
			$this->treeRespectEnableFields = $settings['respectEnableFields'];
		}
	}


    /**
     * Initializes settings for this controller
     */
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
	 * @param int $node
	 * @param int $restrictedDepth
	 */
	public function getTreeAction($node=NULL, $restrictedDepth=0) {
		if($node) {
            $tree = $this->treeRepository->getEmptyTree($this->treeNameSpace);
		} else {
			$tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace, $this->treeRespectEnableFields);
			if ($restrictedDepth > 0) {
	            $tree->setRestrictedDepth($restrictedDepth);
	            $tree->setRespectRestrictedDepth(TRUE);
			}
		}

		echo Tx_PtExtbase_Tree_JSTreeJsonTreeWriter::getInstance()->writeTree($tree);
		exit();
	}



	/**
     * Adds new node into given parent node with given label
     *
	 * @param int $parent
	 * @param string $label
	 *
	 * @return integer id of new node or 0 if error
	 */
	public function addNodeAction($parent, $label) {
		//$newNode = new Tx_PtExtbase_Tree_Node($label);
		// TODO: get correct class name from tree namespace
		$newNode = new Tx_PtCertification_Domain_Model_Category($label);

		$tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $parent = $tree->getNodeByUid($parent);

		$tree->insertNode($newNode, $parent);
        $this->treeRepository->update($tree);

		$this->persistenceManager->persistAll();

		$newNodeUid = $newNode->getUid() > 0 ? $newNode->getUid() : 0;

		// Create json response
		$response = '{ "status": ' . ($newNodeUid > 0 ? 'true' : 'false') . ', "id": ' . $newNodeUid . ' }';

		echo $response;
		exit();
	}



	/**
     * Removes given node from tree
     *
	 * @param int $node
	 */
	public function removeNodeAction($node) {
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);

		$tree->deleteNode($node);
        $this->treeRepository->update($tree);

		$this->persistenceManager->persistAll();

		echo '{ "status": true }';
		exit();
	}



	/**
	 * Moves node into node
	 *
	 * This action can be used for a drag'n'drop of a node "onto" another node.
	 *
	 * @param int $node Node to be moved
	 * @param int $targetNode Node where moved node should be put into
	 */
	public function moveNodeIntoAction($node, $targetNode) {
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);
        $targetNode = $tree->getNodeByUid($targetNode);

		$tree->moveNode($node, $targetNode);
        $this->treeRepository->update($tree);

		$this->persistenceManager->persistAll();

		echo '{ "status": true }';
		exit();
	}



	/**
	 * Moves node given by ID after node given by ID as child of the very same node
	 *
	 * @param int $node Node that has to be moved
	 * @param int $targetNode Node where moved node should be put before
	 */
	public function moveNodeAfterAction($node, $targetNode) {
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);
        $targetNode = $tree->getNodeByUid($targetNode);

		$tree->moveNodeAfterNode($node, $targetNode);
        $this->treeRepository->update($tree);

		$this->persistenceManager->persistAll();

		echo '{ "status": true }';
		exit();
	}



	/**
	 * Moves node before targetNode as child of the very same node.
	 *
	 * @param int $node ID of node that was moved
	 * @param int $targetNode ID of node where moved node should be put after
	 */
	public function moveNodeBeforeAction($node, $targetNode) {
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);
        $targetNode = $tree->getNodeByUid($targetNode);

		$tree->moveNodeBeforeNode($node, $targetNode);
		$this->treeRepository->update($tree);

		$this->persistenceManager->persistAll();

		echo '{ "status": true }';
		exit();
	}



	/**
     * Saves given node object with given label
     *
     * TODO Warning: As we do not check any properties set on the node here, user could manipulate lft and rgt values and hence crash the tree!
     *
	 * @param int $node
	 * @param string $label
	 */
	public function saveNodeAction($node, $label = '') {
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);

		$node->setLabel($label);
		$this->nodeRepository->update($node);

		$this->persistenceManager->persistAll();

		echo '{ "status": true }';
		exit();
	}

}
?>