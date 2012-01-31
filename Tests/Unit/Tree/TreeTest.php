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
 * Testcase for tree
 *
 * @package Tests
 * @subpackage Tree
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_Tests_Unit_Tree_TreeTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {
     
	/** @test */
	public function treeClassExists() {
		$this->assertTrue(class_exists(Tx_PtExtbase_Tree_Tree));
	}
	
	
	
	/** @test */
	public function createInstanceByRootNodeReturnsNumberedTreeInstance() {
		$rootNode = new Tx_PtExtbase_Tree_Node('root', 'rootNode');
		$tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
		$this->assertTrue(is_a($tree, Tx_PtExtbase_Tree_Tree));
		$this->assertEquals($tree->getRoot()->getLft(), 1);
		$this->assertEquals($tree->getRoot()->getRgt(), 2);
	}
	
	
	
	/** @test */
	public function createNodeTreeReturnsEmptyTree() {
		$emptyTree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode(null);
		$this->assertEquals($emptyTree->getRoot(), null);
	}
	
	
	
	/** @test */
	public function creatingNewNodeTreeWithRootNodeSetsRootNode() {
		$rootNode = new Tx_PtExtbase_Tree_Node('root', 'rootNode');
		$tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
		$this->assertEquals($tree->getRoot(), $rootNode);
	}
	
	
	
	/** @test */
	public function creatingNewNodeTreeWithRootNodeAddsRootNodeToNodeMap() {
	    $nodeMock = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock();
	    $nodeMock->setUid(1234);
		$tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($nodeMock);
		$this->assertEquals($tree->getNodeByUid(1234), $nodeMock);
	}
	
	
	
	/** @test */
	public function addingArbitraryNodeStructureInitializesNodeMapCorrectly() {
		$rootNode = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(1);
		$firstChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(2);
		$secondChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(3);
		$thirdChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(4);
		
		$firstChild->addChild($secondChild);
		$firstChild->addChild($thirdChild);
		$rootNode->addChild($firstChild);
		
		$tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
		$this->assertEquals($tree->getRoot(), $rootNode);
		$this->assertEquals($tree->getNodeByUid(1), $rootNode);
		$this->assertEquals($tree->getNodeByUid(2), $firstChild);
		$this->assertEquals($tree->getNodeByUid(3), $secondChild);
		$this->assertEquals($tree->getNodeByUid(4), $thirdChild);
	}
	
	
	
	/** @test */
	public function deletingNodeRemovesNodeFromTreeAndMap() {
		$rootNode = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(4);
        
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
        
        $tree->deleteNode($firstChild);
        
        /* We assert that treemap is updated */
        $this->assertEquals($tree->getNodeByUid(1), $rootNode);
        $this->assertEquals($tree->getNodeByUid(2), null);
        $this->assertEquals($tree->getNodeByUid(3), null);
        $this->assertEquals($tree->getNodeByUid(4), null);
        $this->assertEquals($tree->getNodeByUid(5), null);
        
        /* We assert that parent of deleted node no longer has deleted node as a child */
        $this->assertFalse($firstChild->getParent()->getChildren()->contains($firstChild));
	}
	
	
	
	/** @test */
	public function moveNodeRemovesNodeAsChildOfOldParentAndAddsNewParent() {
		$rootNode = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(4);
        
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
        
        echo 'Before move: ' . $tree->toString();
        
        $tree->moveNode($thirdChild, $rootNode); // We want to move 3rdChild into root node
        
        echo 'After move: ' . $tree->toString();
        
        $this->assertFalse($firstChild->getChildren()->contains($thirdChild));
        $this->assertTrue($rootNode->getChildren()->contains($thirdChild));
        $this->assertTrue($thirdChild->getParent() == $rootNode);
	}
	
	
	
	/** @test */
	public function moveNodeBeforeNodeCorrectlyMovesNode() {
		$rootNode = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(4);
        $fourthChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(5);
        
        $thirdChild->addChild($fourthChild);
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
                
        $tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
        
        echo 'Before move: ' . $tree->toString();
        
        $tree->moveNodeBeforeNode($fourthChild, $firstChild); // We want to move 4th child before 1st child
        
        echo 'After move: ' . $tree->toString();
        
        $this->assertEquals($fourthChild->getParent(), $rootNode);
        $rootsChildren = $rootNode->getChildren()->toArray();
        $this->assertEquals($rootsChildren[0], $fourthChild);
        $this->assertEquals($rootsChildren[1], $firstChild);
        $this->assertFalse($thirdChild->getChildren()->contains($fourthChild));
	}
	
	
	
	/** @test */
	public function moveNodeAfterNodeCorrectlyMovesNode() {
		$rootNode = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(4);
        $fourthChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(5);
        
        $thirdChild->addChild($fourthChild);
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
        
        echo 'Before move: ' . $tree->toString();
        
        $tree->moveNodeAfterNode($fourthChild, $firstChild); // We want to move 4th child before 1st child
        
        echo 'After move: ' . $tree->toString();
        
        $this->assertEquals($fourthChild->getParent(), $rootNode);
        $rootsChildren = $rootNode->getChildren()->toArray();
        $this->assertEquals($rootsChildren[1], $fourthChild);
        $this->assertEquals($rootsChildren[0], $firstChild);
        $this->assertFalse($thirdChild->getChildren()->contains($fourthChild));
	}
	
	
	
	/** @test */
	public function insertNodeInsertsNodeInGivenParentNode() {
		$rootNode = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(4);
        $fourthChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(5);
        
        $thirdChild->addChild($fourthChild);
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
        
		$newNode = new Tx_PtExtbase_Tree_Node('test', 'test');
		
		$tree->insertNode($newNode, $rootNode);
		
		$this->assertEquals($newNode->getParent(), $rootNode);
		$this->assertTrue($rootNode->getChildren()->contains($newNode));
	}
	
	
	
	/** @test */
	public function deletingNodeFromTreeAddsDeletedNodesToListOfDeletedNodes() {
		$rootNode = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(1);
        $firstChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(2);
        $secondChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(3);
        $thirdChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(4);
        $fourthChild = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock(5);
        
        $thirdChild->addChild($fourthChild);
        $firstChild->addChild($secondChild);
        $firstChild->addChild($thirdChild);
        $rootNode->addChild($firstChild);
        
        $tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($rootNode);
        
        $tree->deleteNode($thirdChild);
        
        $this->assertTrue(in_array($thirdChild, $tree->getDeletedNodes()));
        $this->assertTrue(in_array($fourthChild, $tree->getDeletedNodes()));
	}



    /** @test */
    public function setAndGetNamespaceReturnsSetNamespace() {
        $tree = new Tx_PtExtbase_Tree_Tree();
        $tree->setNamespace('myTestNamespace');
        $this->assertEquals('myTestNamespace', $tree->getNamespace());
    }

}
?>