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
class Tx_PtExtbase_Tests_Unit_Tree_NestedSetTreeStorageTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/** @test */
	public function classExists() {
		$this->assertTrue(class_exists(Tx_PtExtbase_Tree_NestedSetTreeStorage));
	}



    /** @test */
    public function saveTreeThrowsExceptionWhenTryingToSaveTreeThatDoesNotImplementInterface() {
        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('remove', 'update', 'updateOrAdd'), array(), '', FALSE);
        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);
        $wrongTree = $this->getMock('Tx_PtExtbase_Tree_TreeInterface', array(), array(), '', FALSE);


		try {
			$nestedSetTreeStorage->saveTree($wrongTree);
		} catch(Exception $e) {
			$this->assertTrue(TRUE);
			return;
		}
		$this->fail('No Exception was thrown.');
    }



    /** @test */
    public function saveTreeCallsRemoveInRepositoryIfNodesShouldBeRemoved() {

        $rootNodeMock = $this->getMock('Tx_PtExtbase_Tree_Node', array('getSubNodes'), array(), '', FALSE);
        $rootNodeMock->expects($this->any())->method('getSubNodes')->will($this->returnValue(array()));

        $nodeMockUncloned = Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(1,2,1,1);
        $nodeMock = clone $nodeMockUncloned;

        $treeMock = $this->getMock('Tx_PtExtbase_Tree_Tree', array('getDeletedNodes', 'getRoot', 'getNamespace'), array(), '', FALSE);
        $treeMock->expects($this->any())->method('getNamespace')->will($this->returnValue('namespace'));
        $treeMock->expects($this->any())->method('getDeletedNodes')->will($this->returnValue(array($nodeMock)));
        $treeMock->expects($this->any())->method('getRoot')->will($this->returnValue($rootNodeMock));

        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('remove', 'update', 'updateOrAdd'), array(), '', FALSE);
        $nodeRepositoryMock->expects($this->once())->method('remove')->with($nodeMock);
        $nodeRepositoryMock->expects($this->any())->method('updateOrAdd');

        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);

        $nestedSetTreeStorage->saveTree($treeMock);
    }



    /** @test */
    public function saveTreeCallsUpdateForAllNonAddedNonDeletedNodes() {

        $unclonedRootNodeMock = $this->getMock('Tx_PtExtbase_Tree_Node', array('getSubNodes'), array(), '', FALSE);
        $unclonedRootNodeMock->expects($this->any())->method('getSubNodes')->will($this->returnValue(array()));
        $rootNodeMock = clone $unclonedRootNodeMock;

        $treeMock = $this->getMock('Tx_PtExtbase_Tree_Tree', array('getRoot', 'getNamespace'), array(), '', FALSE);
        $treeMock->expects($this->any())->method('getNamespace')->will($this->returnValue('namespace'));
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

        $treeMock = $this->getMock('Tx_PtExtbase_Tree_Tree', array('getRoot', 'getNamespace'), array(), '', FALSE);
        $treeMock->expects($this->any())->method('getNamespace')->will($this->returnValue('namespace'));
        $treeMock->expects($this->any())->method('getRoot')->will($this->returnValue($rootNodeMock));

        // TODO this is not, what we actually want to test
        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('add', 'update', 'updateOrAdd'), array(), '', FALSE);
        $nodeRepositoryMock->expects($this->any())->method('updateOrAdd');

        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);

        $nestedSetTreeStorage->saveTree($treeMock);
    }



	/**
	 * Helper method to create a node repository mock object
	 *
	 * @return Tx_PtExtbase_Tree_NodeRepository Mocked node repository
	 */
	protected function buildRepositoryMock() {
		return $this->getMock('Tx_PtExtbase_Tree_NodeRepository', array('findByRootUid'), array(), '', FALSE);
	}

}
?>