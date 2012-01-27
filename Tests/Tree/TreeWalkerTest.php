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
 * @subpackage Domain\Model
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_Yag_Tests_Domain_Model_TreeWalkerTest extends Tx_PtExtbase_Tests_AbstractBaseTestcase {

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
		$wrongVisitor = $this->getMock('Tx_PtExtbase_Tests_Tree_NodeMock', array(), array(), '', FALSE);
		try {
			$treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($wrongVisitor));
		} catch (Exception $e) {
			return;
		}
		$this->fail('No Exception was thrown when trying to add a non-visitor class as a visitor.');
	}
	
	
	
	/** @test */
	public function visitorIsInvokedInCorrectOrder() {
		$node1 = Tx_PtExtbase_Tests_Tree_NodeMock::createCategory('1', 0, 0, 1, '1');
		$node2 = Tx_PtExtbase_Tests_Tree_NodeMock::createCategory('2', 0, 0, 1, '2');
		$node3 = Tx_PtExtbase_Tests_Tree_NodeMock::createCategory('3', 0, 0, 1, '3');
		$node4 = Tx_PtExtbase_Tests_Tree_NodeMock::createCategory('4', 0, 0, 1, '4');
		$node5 = Tx_PtExtbase_Tests_Tree_NodeMock::createCategory('5', 0, 0, 1, '5');
		$node6 = Tx_PtExtbase_Tests_Tree_NodeMock::createCategory('6', 0, 0, 1, '6');
		
		$node1->addChild($node2); $node2->setParent($node1);
		$node1->addChild($node5); $node5->setParent($node1);
		$node2->addChild($node3); $node3->setParent($node2);
		$node2->addChild($node4); $node4->setParent($node2);
		$node5->addChild($node6); $node6->setParent($node5);
		
		$tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($node1);
		
		echo "Testtree: " . $tree->toString();
		
		$visitorMock = $this->getMock(Tx_PtExtbase_Tree_TreeWalkerVisitorInterface, array('doFirstVisit','doLastVisit'), array(), '', FALSE);
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
	
}
?>