<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert <daniel@liener.cc>, 
*           Michael Knoll <mimi@kaktusteam.de>
*  All rights reserved
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
 * Testcase for nested set visitor for treewalker.
 *
 * @package Tests
 * @subpackage Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tests_Unit_Tree_NestedSetVisitorTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/** @test */
	public function visitorSetsLeftAndRightValuesCorrectly() {
		$visitor = new Tx_PtExtbase_Tree_NestedSetVisitor();
		$node = new Tx_PtExtbase_Tree_Node();
		$index = 1;
		$visitor->doFirstVisit($node, $index);
		$this->assertEquals($index, 1);
		$index = 6;
		$visitor->doLastVisit($node, $index);
		$this->assertEquals($index, 6);
		$this->assertEquals($node->getLft(), 1);
		$this->assertEquals($node->getRgt(), 6);
	}
	
	
	
	/** @test */
	public function visitorCorrectlyCreatesLeftRightEnumerationOnTree() {
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
        
        $tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($node1);
        
        $visitor = new Tx_PtExtbase_Tree_NestedSetVisitor();
        $treeWalker = new Tx_PtExtbase_Tree_TreeWalker(array($visitor));
        $treeWalker->traverseTreeDfs($tree);
        
        echo $tree->toString();
        
        $this->assertEquals($node1->getLft(), 1); $this->assertEquals($node1->getRgt(), 12);
        $this->assertEquals($node2->getLft(), 2); $this->assertEquals($node2->getRgt(), 7);
        $this->assertEquals($node3->getLft(), 3); $this->assertEquals($node3->getRgt(), 4);
        $this->assertEquals($node4->getLft(), 5); $this->assertEquals($node4->getRgt(), 6);
        $this->assertEquals($node5->getLft(), 8); $this->assertEquals($node5->getRgt(), 11);
        $this->assertEquals($node6->getLft(), 9); $this->assertEquals($node6->getRgt(), 10);
	}
	
}
?>