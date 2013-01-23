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
 * Testcase for treewalker
 *
 * @package Tests
 * @subpackage Unit\Tree
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_Tests_Unit_Tree_TreeWalkerTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/** @test */
	public function classExists() {
		$this->assertTrue(class_exists('Tx_PtExtbase_Tree_TreeWalker'));
	}
	
	
	
	/** @test */
	public function constructorAcceptsVisitorsAsArguments() {
		$firstVisitor = $this->getMock('Tx_PtExtbase_Tree_NestedSetVisitor', array(), array(), '', FALSE);
		$secondVisitor = $this->getMock('Tx_PtExtbase_Tree_NestedSetVisitor', array(), array(), '', FALSE);
		$treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($firstVisitor, $secondVisitor));
	}
	
	
	
	/** @test */
	public function constructorThrowsExceptionIfWrongClassGetsInjected() {
		$wrongVisitor = $this->getMock('Tx_PtExtbase_Tests_Unit_Tree_NodeMock', array(), array(), '', FALSE);
		try {
			$treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($wrongVisitor));
		} catch (Exception $e) {
			return;
		}
		$this->fail('No Exception was thrown when trying to add a non-visitor class as a visitor.');
	}
	
	
	
	/** @test */
	public function visitorIsInvokedInCorrectOrder() {
		$tree = $this->createDemoTree();
		
		# echo "Testtree: " . $tree->toString();
		
		$visitorMock = $this->createVisitorMock();
		$treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($visitorMock));
		
        $visitorMock->expects($this->at(0))->method('doFirstVisit');#->with($node1, 1);
        $visitorMock->expects($this->at(1))->method('doFirstVisit');#->with($node2, 2);
        $visitorMock->expects($this->at(2))->method('doFirstVisit');#->with($node3, 3);
        $visitorMock->expects($this->at(3))->method('doLastVisit');#->with($node3, 4);
        $visitorMock->expects($this->at(4))->method('doFirstVisit');#->with($node4, 5);
        $visitorMock->expects($this->at(5))->method('doLastVisit');#->with($node4, 6);
        $visitorMock->expects($this->at(6))->method('doLastVisit');#->with($node2, 7);
        $visitorMock->expects($this->at(7))->method('doFirstVisit');#->with($node5, 8);
        $visitorMock->expects($this->at(8))->method('doFirstVisit');#->with($node6, 9);
        $visitorMock->expects($this->at(9))->method('doLastVisit');#->with($node6, 10);
        $visitorMock->expects($this->at(10))->method('doLastVisit');#->with($node5, 11);
        $visitorMock->expects($this->at(11))->method('doLastVisit');#->with($node1, 12);
        
        $treeWalker->traverseTreeDfs($tree);
	}



    /** @test */
    public function treeWalkerDoesNotRespectDepthIfRespectRestrictedDepthIsNotSetOnTree() {
        $tree = $this->createDemoTree();
        $tree->setRestrictedDepth(1);
        $tree->setRespectRestrictedDepth(FALSE);

        $visitorMock = $this->createVisitorMock();
        $visitorMock->expects($this->exactly(6))->method('doFirstVisit');
        $visitorMock->expects($this->exactly(6))->method('doLastVisit');

        $treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($visitorMock));
        $treeWalker->traverseTreeDfs($tree);
    }


	/** @test */
	public function treeWalkerRespectsRestrictedDepthIfSetOnTree() {
		$tree = $this->createDemoTree();
		$tree->setRestrictedDepth(2);
		$tree->setRespectRestrictedDepth(TRUE);

		$visitorMock = $this->createVisitorMock();
		$visitorMock->expects($this->exactly(3))->method('doFirstVisit');
		$visitorMock->expects($this->exactly(3))->method('doLastVisit');

		$treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($visitorMock));
		$treeWalker->traverseTreeDfs($tree);
	}


	/**
	 * @test
	 */
	public function treeWalkerRespectsNodeAccessibilityOnALeaf() {
		$tree = $this->createDemoTree();
		$tree->getNodeByUid(3)->setAccessible(false);

		$visitorMock = $this->createVisitorMock();
		$visitorMock->expects($this->exactly(5))->method('doFirstVisit');
		$visitorMock->expects($this->exactly(5))->method('doLastVisit');
		// $visitorMock->expects($this->never())->method('doFirstVisit')->with($tree->getNodeByUid(3), 3);

		$treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($visitorMock));
		$treeWalker->traverseTreeDfs($tree);
	}



	/**
	 * @test
	 */
	public function treeWalkerRespectsNodeAccessibilityOnSubtree() {
		$tree = $this->createDemoTree();
		$tree->getNodeByUid(2)->setAccessible(false);

		$visitorMock = $this->createVisitorMock();
		$visitorMock->expects($this->exactly(3))->method('doFirstVisit');
		$visitorMock->expects($this->exactly(3))->method('doLastVisit');
		// $visitorMock->expects($this->never())->method('doFirstVisit')->with($tree->getNodeByUid(3), 3);

		$treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($visitorMock));
		$treeWalker->traverseTreeDfs($tree);
	}



	/**
	 * @test
	 */
	public function treeWalkerRespectsNodeAccessibilityOnRoot() {
		$tree = $this->createDemoTree();
		$tree->getNodeByUid(1)->setAccessible(false);

		$visitorMock = $this->createVisitorMock();
		$visitorMock->expects($this->never())->method('doFirstVisit');
		$visitorMock->expects($this->never())->method('doLastVisit');

		$treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($visitorMock));
		$treeWalker->traverseTreeDfs($tree);
	}



    /**
     * @return Tx_PtExtbase_Tree_Tree
	  *
	  * A tree like
	  * . node1
	  * .. node2
	  * ... node3
	  * ... node4
	  * .. node5
	  * ... node6
     */
    protected function createDemoTree() {
        $node1 = Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode('1', 0, 0, 1, '1');
        $node2 = Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode('2', 0, 0, 1, '2');
        $node3 = Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode('3', 0, 0, 1, '3');
        $node4 = Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode('4', 0, 0, 1, '4');
        $node5 = Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode('5', 0, 0, 1, '5');
        $node6 = Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode('6', 0, 0, 1, '6');

        $node1->addChild($node2); $node2->setParent($node1);
        $node1->addChild($node5); $node5->setParent($node1);
        $node2->addChild($node3); $node3->setParent($node2);
        $node2->addChild($node4); $node4->setParent($node2);
        $node5->addChild($node6); $node6->setParent($node5);

        return Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($node1);
    }



    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function createVisitorMock() {
        return $this->getMock(Tx_PtExtbase_Tree_TreeWalkerVisitorInterface, array('doFirstVisit','doLastVisit'), array(), '', FALSE);
    }
	
}
?>