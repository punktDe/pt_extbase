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

class Tx_PtExtbase_ViewHelpers_Widget_Controller_TreeController extends Tx_Fluid_Core_Widget_AbstractWidgetController {

	/**
		 * @var Tx_Yag_Domain_Repository_CategoryRepository
		 */
		protected $categoryRepository;



		/**
		 * Holds an instance of category tree repository
		 *
		 * @var Tx_Yag_Domain_Repository_CategoryTreeRepository
		 */
		protected $categoryTreeRepository;



		/**
		 * Holds an instance of persistence manager
		 *
		 * @var Tx_Extbase_Persistence_Manager
		 */
		protected $persistenceManager;



		/**
		 * Initializes the current action
		 *
		 * @return void
		 */
		protected function postInitializeAction() {
			$this->categoryRepository = t3lib_div::makeInstance('Tx_Yag_Domain_Repository_CategoryRepository');
			$this->categoryTreeRepository = new Tx_Yag_Domain_Repository_CategoryTreeRepository();
			$this->persistenceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
		}


		/**
		 * 
		 */
		public function indexAction() {

		}


		/**
		 * TODO test method
		 */
		public function showAction() {
			/*
			$rootCategory = $this->categoryRepository->findByUid(1);
			echo $rootCategory->getName() . '<br>';
			foreach($rootCategory->getChildren() as $child) {
				echo "-" . $child->getName() . '<br>';
				foreach($child->getChildren() as $subchild) {
					echo "--" . $subchild->getName() . '<br>';
				}
			}
			die();
			*/
		}



		/**
		 * TODO test method
		 */
		public function debugAction() {
			$categoryTree = $this->categoryTreeRepository->findByRootId(1);
			$this->view->assign('categoryList', $categoryTree->toString());
		}



		/**
		 * Get subtree for node ID given by GP vars
		 */
		public function getSubTreeAction() {
			$categoryTree = $this->categoryTreeRepository->findByCategoryId(intval(t3lib_div::_GP('node')));
			$childCategoryArray = array();
			foreach($categoryTree->getNodeByUid(intval(t3lib_div::_GP('node')))->getChildren() as $childCategory) { /* @var $childCategory Tx_Yag_Domain_Model_Category */
				$childCategoryArray[] = array(
					'id' => $childCategory->getUid(),
					'text' => $childCategory->getName(),
					'description' => $childCategory->getDescription(),
					'leaf' => $childCategory->hasChildren() ? '': 'true'
				);

			}
			return json_encode($childCategoryArray);
		}



		/**
		 * Save Title / Description of a given Node
		 *
		 * @param Tx_Yag_Domain_Model_Category $category
		 * @param string $categoryTitle
		 * @param string $categoryDescription
		 */
		public function saveCategoryAction(Tx_Yag_Domain_Model_Category $category, $categoryTitle, $categoryDescription='') {
			$category->setName($categoryTitle);
			$category->setDescription($categoryDescription);
			$this->categoryRepository->update($category);
			$this->persistenceManager->persistAll();
		}


		/**
		 * Adds a new category to the tree
		 *
		 * @param int $parentNodeId
		 * @param string $nodeTitle
		 * @param string $nodeDescription
		 * @dontvalidate
		 */
		public function addNewCategoryAction($parentNodeId, $nodeTitle, $nodeDescription = '') {
			$parentNode = $this->categoryRepository->findByUid($parentNodeId);
			$newNode = new Tx_Yag_Domain_Model_Category($nodeTitle, $nodeDescription);
			$categoryTree = $this->categoryTreeRepository->findByRootId($parentNode->getRoot());
			$categoryTree->insertNode($newNode, $parentNode);
			$this->categoryTreeRepository->update($categoryTree);
			$this->persistenceManager->persistAll();

			$this->forward('debug');
		}


		/**
		 * Add a new category to the tree
		 *
		 * @param int $parentNodeId
		 * @param string $nodeTitle
		 * @param string $nodeDescription
		 * @return string Rendered HTML
		 * @dontvalidate
		 */
		public function addCategoryAction($parentNodeId, $nodeTitle='', $nodeDescription='') {



	        // this is currently not working, use addNewCategoryAction or fix this (action is not called from debug form - dunno why)

			// TODO replace whole code here by functionality of "addNewCategoryAction"!

			$parentCategory = $this->categoryRepository->findByUid($parentNodeId);
			if($parentCategory !== NULL) {
				$newCategory = new Tx_Yag_Domain_Model_Category();
				$newCategory->setRoot($parentCategory);
				$newCategory->setName($nodeTitle);
				$newCategory->setDescription($nodeDescription);

				$this->categoryRepository->add($newCategory);
				$newCategory->setParent($parentCategory);
				//$parentCategory->addChild($newCategory); // Warum das nicht ??

				$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();
				return $newCategory->getUid();
			}
			// TODO add error-handling, if parentCategory = null or an error occured
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
