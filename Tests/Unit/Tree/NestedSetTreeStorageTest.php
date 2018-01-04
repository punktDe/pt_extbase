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
class Tx_PtExtbase_Tests_Unit_Tree_NestedSetTreeStorageTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /** @test */
    public function classExists()
    {
        $this->assertTrue(class_exists(Tx_PtExtbase_Tree_NestedSetTreeStorage::class));
    }



    /** @test */
    public function saveTreeThrowsExceptionWhenTryingToSaveTreeThatDoesNotImplementInterface()
    {
        $this->markTestSkipped('Functionaltest');
        $nodeRepositoryMock = $this->getMockBuilder(Tx_PtExtbase_Tree_NodeRepository::class)
            ->setMethods(['remove', 'update', 'updateOrAdd'])
            ->getMock();
        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);
        $wrongTree = $this->getMock('Tx_PtExtbase_Tree_TreeInterface', [], [], '', false);


        try {
            $nestedSetTreeStorage->saveTree($wrongTree);
        } catch (Exception $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail('No Exception was thrown.');
    }



    /** @test */
    public function saveTreeCallsRemoveInRepositoryIfNodesShouldBeRemoved()
    {
        $this->markTestSkipped('Functionaltest');
        $rootNodeMock = $this->getMockBuilder(Tx_PtExtbase_Tree_Node::class)
            ->setMethods(['getSubNodes'])
            ->getMock();
        $rootNodeMock->expects($this->any())->method('getSubNodes')->will($this->returnValue([]));

        $nodeMockUncloned = Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode(1, 2, 1, 1);
        $nodeMock = clone $nodeMockUncloned;

        $treeMock = $this->getMock('Tx_PtExtbase_Tree_Tree', ['getDeletedNodes', 'getRoot', 'getNamespace'], [], '', false);
        $treeMock->expects($this->any())->method('getNamespace')->will($this->returnValue('namespace'));
        $treeMock->expects($this->any())->method('getDeletedNodes')->will($this->returnValue([$nodeMock]));
        $treeMock->expects($this->any())->method('getRoot')->will($this->returnValue($rootNodeMock));

        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', ['remove', 'update', 'updateOrAdd'], [], '', false);
        $nodeRepositoryMock->expects($this->once())->method('remove')->with($nodeMock);
        $nodeRepositoryMock->expects($this->any())->method('updateOrAdd');

        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);

        $nestedSetTreeStorage->saveTree($treeMock);
    }



    /** @test */
    public function saveTreeCallsUpdateForAllNonAddedNonDeletedNodes()
    {
        $this->markTestSkipped('Functionaltest');
        $unclonedRootNodeMock = $this->getMockBuilder(Tx_PtExtbase_Tree_Node::class)
            ->setMethods(['getSubNodes'])
            ->getMock();
        $unclonedRootNodeMock->expects($this->any())->method('getSubNodes')->will($this->returnValue([]));
        $rootNodeMock = clone $unclonedRootNodeMock;

        $treeMock = $this->getMockBuilder(Tx_PtExtbase_Tree_Tree::class)
            ->setMethods(['getRoot', 'getNamespace'])
            ->getMock();
        $treeMock->expects($this->any())->method('getNamespace')->will($this->returnValue('namespace'));
        $treeMock->expects($this->any())->method('getRoot')->will($this->returnValue($rootNodeMock));

        // TODO this is not, what we actually want to test
        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', ['add', 'update'], [], '', false);
        $nodeRepositoryMock->expects($this->any())->method('update');

        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);

        $nestedSetTreeStorage->saveTree($treeMock);
    }



    /** @test */
    public function saveTreeCallsUpdateForRootNode()
    {
        $this->markTestSkipped('Functionaltest');
        $unclonedRootNodeMock = $this->getMockBuilder(Tx_PtExtbase_Tree_Node::class)
            ->getMock();
        $rootNodeMock = clone $unclonedRootNodeMock;

        $treeMock = $this->getMockBuilder(Tx_PtExtbase_Tree_Tree::class)
            ->setMethods(['getRoot', 'getNamespace'])
            ->getMock();
        $treeMock->expects($this->any())->method('getNamespace')->will($this->returnValue('namespace'));
        $treeMock->expects($this->any())->method('getRoot')->will($this->returnValue($rootNodeMock));

        // TODO this is not, what we actually want to test
        $nodeRepositoryMock = $this->getMock('Tx_PtExtbase_Tree_NodeRepository', ['add', 'update', 'updateOrAdd'], [], '', false);
        $nodeRepositoryMock->expects($this->any())->method('updateOrAdd');

        $nestedSetTreeStorage = new Tx_PtExtbase_Tree_NestedSetTreeStorage($nodeRepositoryMock);

        $nestedSetTreeStorage->saveTree($treeMock);
    }


    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildRepositoryMock()
    {
        return $this->getMockBuilder(Tx_PtExtbase_Tree_NodeRepository::class)
            ->setMethods(['findByRootUid'])
            ->getMock();
    }
}
