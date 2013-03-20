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
 * Testcase for tree builder
 *
 * @package Tests
 * @subpackage Tree
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_Tests_Unit_Tree_TreeBuilderTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/** @test */
	public function classExists() {
		$this->assertTrue(class_exists(Tx_PtExtbase_Tree_TreeBuilder));
	}



    /** @test */
    public function getEmptyTreeReturnsEmptyTree() {
        $repositoryMock = $this->buildRepositoryMock();
        $treeBuilder = new Tx_PtExtbase_Tree_TreeBuilder($repositoryMock);
        $tree = $treeBuilder->getEmptyTree('namespace', 'ourRootLabel');

        $this->assertTrue(is_a($tree->getRoot(), Tx_PtExtbase_Tree_NodeInterface));
        $this->assertEquals($tree->getNamespace(), 'namespace');
        $this->assertEquals($tree->getRoot()->getLabel(), 'ourRootLabel');
    }
	


	/** @test */
	public function buildTreeForNamespaceReturnsNodeTreeForNamespace() {
		$nodesObjectStorage = self::buildSetOfNodes();
		$nodesArray = $nodesObjectStorage->toArray();
		$repositoryMock = $this->buildRepositoryMock();
		$repositoryMock->expects($this->once())
		    ->method('findByNamespace')
		    ->will($this->returnValue($nodesObjectStorage));
		$treeBuilder = new Tx_PtExtbase_Tree_TreeBuilder($repositoryMock);
		$tree = $treeBuilder->buildTreeForNamespace('no_matter_what_namespace');

		$this->assertTrue(is_a($tree, Tx_PtExtbase_Tree_Tree));

        echo $tree->toString();

		// Assertions, that build tree is correct
		$this->assertEquals($tree->getRoot(), $nodesArray[5], 'Root node of tree is not root of given set of nodes');
		$this->assertTrue($tree->getRoot()->getChildren()->contains($tree->getNodeByUid(2)), 'Root node of tree does not contain child of given set of nodes');
		$this->assertTrue($tree->getRoot()->getChildren()->contains($tree->getNodeByUid(5)), 'Root node of tree does not contain child of given set of nodes');
		$this->assertEquals($tree->getNodeByUid(2)->getParent(), $nodesArray[5], 'Child of root does not have root set as its parent');
		$this->assertEquals($tree->getNodeByUid(5)->getParent(), $nodesArray[5], 'Child of root does not have root set as its parent');
		$this->assertTrue($tree->getNodeByUid(2)->getChildren()->contains($tree->getNodeByUid(3)), 'Node 2 does not contain node 3 as its child');
		$this->assertTrue($tree->getNodeByUid(2)->getChildren()->contains($tree->getNodeByUid(4)), 'Node 2 does not contain node 4 as its child');
		$this->assertEquals($tree->getNodeByUid(3)->getParent(), $tree->getNodeByUid(2), 'Node 3 does not have node 2 set as its parent');
		$this->assertEquals($tree->getNodeByUid(4)->getParent(), $tree->getNodeByUid(2), 'Node 3 does not have node 2 set as its parent');
		$this->assertTrue($tree->getNodeByUid(5)->getChildren()->contains($tree->getNodeByUid(6)), 'Node 5 does not have node 6 set as child');
		$this->assertEquals($tree->getNodeByUid(6)->getParent(), $tree->getNodeByUid(5), 'Node 6 does not have node 6 set as its parent');
	}



	/** @test */
	public function buildTreeWithExcludedInaccessibleSubTreesReturnsExpectedTree() {
		$nodesObjectStorage = self::buildSetOfNodesWithInaccessibleNodes();
		$nodesArray = $nodesObjectStorage->toArray();
		$repositoryMock = $this->buildRepositoryMock();
		$repositoryMock->expects($this->once())
		    ->method('findByNamespace')
		    ->will($this->returnValue($nodesObjectStorage));
		$treeBuilder = new Tx_PtExtbase_Tree_TreeBuilder($repositoryMock);
		$tree = $treeBuilder->buildTreeForNamespaceWithoutInaccessibleSubtrees('no_matter_what_namespace');

		$this->assertTrue(is_a($tree, Tx_PtExtbase_Tree_Tree));

        echo $tree->toString();

		// Assertions, that build tree is correct
		$this->assertEquals($nodesArray[5]->getUid(), $tree->getRoot()->getUid(), 'Root node of tree is not root of given set of nodes');
		$this->assertFalse($nodesArray[5] === $tree->getRoot());
		$this->assertEquals(1, count($tree->getRoot()->getChildren()));
		$children = $tree->getRoot()->getChildren()->toArray();
		$this->assertEquals($nodesArray[1]->getUid(), $children[0]->getUid());
		$child = $children[0];
		$this->assertEquals($tree->getRoot(), $child->getParent());
		$this->assertEquals(0, $child->getChildren()->count());
	}



	/** @test */
	public function buildTreeForNamespaceThrowsExceptionIfNodesAreNotGivenInDescendingLeftValueOrder() {
        $repositoryMock = $this->buildRepositoryMock();
        $repositoryMock->expects($this->once())
            ->method('findByNamespace')
            ->will($this->returnValue(self::buildWrongSortedSetOfNodes()));
        $treeBuilder = new Tx_PtExtbase_Tree_TreeBuilder($repositoryMock);

		try {
			$treeBuilder->buildTreeForNamespace('no_matter_what_namespace');
		} catch(Exception $e) {
			$this->assertTrue(TRUE);
			return;
		}
		$this->fail('No Exception was thrown.');
	}



	/**
	 * Returns an ordered set of nodes
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage
	 */
	protected static function buildSetOfNodes() {
		$setOfNodes = new Tx_Extbase_Persistence_ObjectStorage();
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(6,9,10,1,'6','testnamespace'));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(5,8,11,1,'5','testnamespace'));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(4,5,6,1,'4','testnamespace'));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(3,3,4,1,'3','testnamespace'));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(2,2,7,1,'2','testnamespace'));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(1,1,12,1,'1','testnamespace'));
		return $setOfNodes;
	}



	/**
	 * Returns an ordered set of nodes with inaccessible nodes
	 *
	 * Inaccessible nodes: 2, 6
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage
	 */
	protected static function buildSetOfNodesWithInaccessibleNodes() {
		$setOfNodes = new Tx_Extbase_Persistence_ObjectStorage();
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(6,9,10,1,'6','testnamespace', FALSE));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(5,8,11,1,'5','testnamespace'));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(4,5,6,1,'4','testnamespace'));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(3,3,4,1,'3','testnamespace'));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(2,2,7,1,'2','testnamespace', FALSE));
		$setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(1,1,12,1,'1','testnamespace'));
		return $setOfNodes;
	}



	/**
	 * Helper method to return a wrong sorted set of nodes
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage
	 */
	protected static function buildWrongSortedSetOfNodes() {
		$setOfNodes = new Tx_Extbase_Persistence_ObjectStorage();
        $setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(5,8,11,1,'5'));
        $setOfNodes->attach(Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(6,9,10,1,'6'));
        return $setOfNodes;
	}



	/**
	 * Helper method to create a Node object
	 *
	 * @return Tx_Yag_Domain_Repository_NodeRepository Mocked repository
	 */
	protected function buildRepositoryMock() {
		return $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('findByNamespace'), array(), '', FALSE);
	}
	
}
?>