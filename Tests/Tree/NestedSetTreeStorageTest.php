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
 * Testcase for nested set tree storage
 *
 * @package Tests
 * @subpackage Tree
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_Tests_Tree_NestedSetTreeStorageTest extends Tx_PtExtbase_Tests_AbstractBaseTestcase {

	/** @test */
	public function classExists() {
		$this->assertTrue(class_exists(Tx_PtExtbase_Tree_NestedSetTreeStorage));
	}



    /** @test */
    public function saveTreeThrowsExceptionWhenTryingToSaveTreeThatDoesNotImplementInterface() {
        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('remove', 'update', 'updateOrAdd'), array(), '', FALSE);
        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);
        $wrongTree = $this->getMock('Tx_PtExtbase_Tree_TreeInterface', array(), array(), '', FALSE);
        $this->setExpectedException('Exception');
        $nestedSetTreeStorage->saveTree($wrongTree);
    }



    /** @test */
    public function saveTreeCallsRemoveInRepositoryIfNodesShouldBeRemoved() {

        $rootNodeMock = $this->getMock('Tx_PtExtbase_Tree_Node', array('getSubCategories'), array(), '', FALSE);
        $rootNodeMock->expects($this->any())->method('getSubCategories')->will($this->returnValue(array()));

        $nodeMockUncloned = Tx_PtExtbase_Tests_Tree_NodeMock::createCategory(1,2,1,1);
        $nodeMock = clone $nodeMockUncloned;

        $treeMock = $this->getMock('Tx_PtExtbase_Tree_Tree', array('getDeletedNodes', 'getRoot'), array(), '', FALSE);
        $treeMock->expects($this->any())->method('getDeletedNodes')->will($this->returnValue(array($nodeMock)));
        $treeMock->expects($this->any())->method('getRoot')->will($this->returnValue($rootNodeMock));

        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('remove', 'update', 'updateOrAdd'), array(), '', FALSE);
        $nodeRepositoryMock->expects($this->once())->method('remove')->with($nodeMock);
        $nodeRepositoryMock->expects($this->once())->method('updateOrAdd');

        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);

        $nestedSetTreeStorage->saveTree($treeMock);
    }



    /** @test */
    public function saveTreeCallsAddInRepositoryIfNodesShouldBeAdded() {

        $rootNodeMock = $this->getMock('Tx_PtExtbase_Tree_Node', array('getSubCategories'), array(), '', FALSE);
        $rootNodeMock->expects($this->any())->method('getSubCategories')->will($this->returnValue(array()));

        $nodeMockUncloned = Tx_PtExtbase_Tests_Tree_NodeMock::createCategory(1,2,1,1);
        $nodeMock = clone $nodeMockUncloned;

        $treeMock = $this->getMock('Tx_PtExtbase_Tree_Tree', array('getAddedNodes', 'getRoot'), array(), '', FALSE);
        $treeMock->expects($this->any())->method('getAddedNodes')->will($this->returnValue(array($nodeMock)));
        $treeMock->expects($this->any())->method('getRoot')->will($this->returnValue($rootNodeMock));

        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('add', 'update', 'updateOrAdd'), array(), '', FALSE);
        $nodeRepositoryMock->expects($this->once())->method('add')->with($nodeMock);
        $nodeRepositoryMock->expects($this->once())->method('updateOrAdd');

        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);

        $nestedSetTreeStorage->saveTree($treeMock);
    }



    /** @test */
    public function saveTreeCallsUpdateForAllNonAddedNonDeletedNodes() {

        $unclonedRootNodeMock = $this->getMock('Tx_PtExtbase_Tree_Node', array('getSubCategories'), array(), '', FALSE);
        $unclonedRootNodeMock->expects($this->any())->method('getSubCategories')->will($this->returnValue(array()));
        $rootNodeMock = clone $unclonedRootNodeMock;

        $nodeMockUncloned = Tx_PtExtbase_Tests_Tree_NodeMock::createCategory(1,2,1,1);
        $nodeMock = clone $nodeMockUncloned;

        $treeMock = $this->getMock('Tx_PtExtbase_Tree_Tree', array('getRoot'), array(), '', FALSE);
        $treeMock->expects($this->any())->method('getRoot')->will($this->returnValue($rootNodeMock));

        // TODO this is not, what we actually want to test
        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('add', 'update'), array(), '', FALSE);
        $nodeRepositoryMock->expects($this->any())->method('update');

        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);

        $nestedSetTreeStorage->saveTree($treeMock);
    }



    /** @test */
    public function saveTreeCallsUpdateForRootNode() {
        $unclonedRootNodeMock = $this->getMock('Tx_PtExtbase_Tree_Node', array(), array(), '', FALSE);
        $rootNodeMock = clone $unclonedRootNodeMock;

        $treeMock = $this->getMock('Tx_PtExtbase_Tree_Tree', array('getRoot'), array(), '', FALSE);
        $treeMock->expects($this->any())->method('getRoot')->will($this->returnValue($rootNodeMock));

        // TODO this is not, what we actually want to test
        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('add', 'update', 'updateOrAdd'), array(), '', FALSE);
        $nodeRepositoryMock->expects($this->once())->method('updateOrAdd')->with($rootNodeMock);

        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);

        $nestedSetTreeStorage->saveTree($treeMock);
    }



	/**
	 * Helper method to create a category object
	 *
	 * @return Tx_Yag_Domain_Repository_CategoryRepository Mocked repository
	 */
	protected function buildRepositoryMock() {
		return $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('findByRootUid'), array(), '', FALSE);
	}

}
?>