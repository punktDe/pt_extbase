<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Michael Knoll <mimi@kaktusteam.de>
*           Daniel Lienert <daniel@lienert.cc>
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
 * Testcase for category tree
 *
 * @package Tests
 * @subpackage Category
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_Tests_Category_CategoryTreeTest extends Tx_PtExtbase_Tests_AbstractBaseTestcase {
     
	/** @test */
	public function categoryTreeClassExists() {
		$this->assertTrue(class_exists(Tx_PtExtbase_Category_CategoryTree));
	}
	
	
	
	/** @test */
	public function createInstanceByRootNodeReturnsNumberedTreeInstance() {
		$rootNode = new Tx_PtExtbase_Category_Category('root', 'rootNode');
		$tree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
		$this->assertTrue(is_a($tree, Tx_PtExtbase_Category_CategoryTree));
		$this->assertEquals($tree->getRoot()->getLft(), 1);
		$this->assertEquals($tree->getRoot()->getRgt(), 2);
	}
	
	
	
	/** @test */
	public function createCategoryTreeReturnsEmptyTree() {
		$emptyTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode(null);
		$this->assertEquals($emptyTree->getRoot(), null);
	}
	
	
	
	/** @test */
	public function creatingNewCategoryTreeWithRootNodeSetsRootNode() {
		$rootNode = new Tx_PtExtbase_Category_Category('root', 'rootNode');
		$categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
		$this->assertEquals($categoryTree->getRoot(), $rootNode);
	}
	
	
	
	/** @test */
	public function creatingNewCategoryTreeWithRootNodeAddsRootNodeToNodeMap() {
	    $nodeMock = new Tx_PtExtbase_Tests_Category_CategoryMock();
	    $nodeMock->setUid(1234);
		$categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($nodeMock);
		$this->assertEquals($categoryTree->getNodeByUid(1234), $nodeMock);
	}
	
	
	
	/** @test */
	public function addingArbitraryCategoryStructureInitializesNodeMapCorrectly() {
		$rootNode = new Tx_PtExtbase_Tests_Category_CategoryMock(1);
		$firstChild = new Tx_PtExtbase_Tests_Category_CategoryMock(2);
		$secondChild = new Tx_PtExtbase_Tests_Category_CategoryMock(3);
		$thirdChild = new Tx_PtExtbase_Tests_Category_CategoryMock(4);
		
		$firstChild->addChild($secondChild);
		$firstChild->addChild($thirdChild);
		$rootNode->addChild($firstChild);
		
		$categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
		$this->assertEquals($categoryTree->getRoot(), $rootNode);
		$this->assertEquals($categoryTree->getNodeByUid(1), $rootNode);
		$this->assertEquals($categoryTree->getNodeByUid(2), $firstChild);
		$this->assertEquals($categoryTree->getNodeByUid(3), $secondChild);
		$this->assertEquals($categoryTree->getNodeByUid(4), $thirdChild);
	}
	
	
	
	/** @test */
	public function deletingNodeRemovesNodeFromTreeAndMap() {
		$rootNode = new Tx_PtExtbase_Tests_Category_CategoryMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Category_CategoryMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Category_CategoryMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Category_CategoryMock(4);
        
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
        
        $categoryTree->deleteNode($firstChild);
        
        /* We assert that treemap is updated */
        $this->assertEquals($categoryTree->getNodeByUid(1), $rootNode);
        $this->assertEquals($categoryTree->getNodeByUid(2), null);
        $this->assertEquals($categoryTree->getNodeByUid(3), null);
        $this->assertEquals($categoryTree->getNodeByUid(4), null);
        $this->assertEquals($categoryTree->getNodeByUid(5), null);
        
        /* We assert that parent of deleted node no longer has deleted node as a child */
        $this->assertFalse($firstChild->getParent()->getChildren()->contains($firstChild));
	}
	
	
	
	/** @test */
	public function moveNodeRemovesNodeAsChildOfOldParentAndAddsNewParent() {
		$rootNode = new Tx_PtExtbase_Tests_Category_CategoryMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Category_CategoryMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Category_CategoryMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Category_CategoryMock(4);
        
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
        
        var_dump('Before move: ' . $categoryTree->toString());
        
        $categoryTree->moveNode($thirdChild, $rootNode); // We want to move 3rdChild into root node
        
        var_dump('After move: ' . $categoryTree->toString());
        
        $this->assertFalse($firstChild->getChildren()->contains($thirdChild));
        $this->assertTrue($rootNode->getChildren()->contains($thirdChild));
        $this->assertTrue($thirdChild->getParent() == $rootNode);
	}
	
	
	
	/** @test */
	public function moveNodeBeforeNodeCorrectlyMovesNode() {
		$rootNode = new Tx_PtExtbase_Tests_Category_CategoryMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Category_CategoryMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Category_CategoryMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Category_CategoryMock(4);
        $fourthChild = new Tx_PtExtbase_Tests_Category_CategoryMock(5);
        
        $thirdChild->addChild($fourthChild);
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
                
        $categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
        
        var_dump('Before move: ' . $categoryTree->toString());
        
        $categoryTree->moveNodeBeforeNode($fourthChild, $firstChild); // We want to move 4th child before 1st child
        
        var_dump('After move: ' . $categoryTree->toString());
        
        $this->assertEquals($fourthChild->getParent(), $rootNode);
        $rootsChildren = $rootNode->getChildren()->toArray();
        $this->assertEquals($rootsChildren[0], $fourthChild);
        $this->assertEquals($rootsChildren[1], $firstChild);
        $this->assertFalse($thirdChild->getChildren()->contains($fourthChild));
	}
	
	
	
	/** @test */
	public function moveNodeAfterNodeCorrectlyMovesNode() {
		$rootNode = new Tx_PtExtbase_Tests_Category_CategoryMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Category_CategoryMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Category_CategoryMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Category_CategoryMock(4);
        $fourthChild = new Tx_PtExtbase_Tests_Category_CategoryMock(5);
        
        $thirdChild->addChild($fourthChild);
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
        
        var_dump('Before move: ' . $categoryTree->toString());
        
        $categoryTree->moveNodeAfterNode($fourthChild, $firstChild); // We want to move 4th child before 1st child
        
        var_dump('After move: ' . $categoryTree->toString());
        
        $this->assertEquals($fourthChild->getParent(), $rootNode);
        $rootsChildren = $rootNode->getChildren()->toArray();
        $this->assertEquals($rootsChildren[1], $fourthChild);
        $this->assertEquals($rootsChildren[0], $firstChild);
        $this->assertFalse($thirdChild->getChildren()->contains($fourthChild));
	}
	
	
	
	/** @test */
	public function insertNodeInsertsNodeInGivenParentNode() {
		$rootNode = new Tx_PtExtbase_Tests_Category_CategoryMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Category_CategoryMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Category_CategoryMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Category_CategoryMock(4);
        $fourthChild = new Tx_PtExtbase_Tests_Category_CategoryMock(5);
        
        $thirdChild->addChild($fourthChild);
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
        
		$newNode = new Tx_PtExtbase_Category_Category('test', 'test');
		
		$categoryTree->insertNode($newNode, $rootNode);
		
		$this->assertEquals($newNode->getParent(), $rootNode);
		$this->assertTrue($rootNode->getChildren()->contains($newNode));
	}
	
	
	
	/** @test */
	public function insertingNodeAddsInsertedNodeToListOfAddedNodes() {
		$rootNode = new Tx_PtExtbase_Tests_Category_CategoryMock(1);
		$nodeToBeAdded = new Tx_PtExtbase_Tests_Category_CategoryMock(2);
		$categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
		$categoryTree->insertNode($nodeToBeAdded, $rootNode);
		
		$this->assertTrue(in_array($nodeToBeAdded, $categoryTree->getAddedNodes()));
	}
	
	
	
	/** @test */
	public function deletingNodeFromTreeAddsDeletedNodesToListOfDeletedNodes() {
		$rootNode = new Tx_PtExtbase_Tests_Category_CategoryMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Category_CategoryMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Category_CategoryMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Category_CategoryMock(4);
        $fourthChild = new Tx_PtExtbase_Tests_Category_CategoryMock(5);
        
        $thirdChild->addChild($fourthChild);
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $categoryTree = Tx_PtExtbase_Category_CategoryTree::getInstanceByRootNode($rootNode);
        
        $categoryTree->deleteNode($thirdChild);
        
        $this->assertTrue(in_array($thirdChild, $categoryTree->getDeletedNodes()));
        $this->assertTrue(in_array($fourthChild, $categoryTree->getDeletedNodes()));
	}
	
}
?>