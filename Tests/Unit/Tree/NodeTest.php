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
 * Testcase for nested sets node object
 *
 * @package Tests
 * @subpackage Tree
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_Tests_Unit_Tree_NodeTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {
     
	/** @test */
	public function constructReturnsInitializedNode() {
		$node = new Tx_PtExtbase_Tree_Node();
		$this->assertEquals($node->getLft(), 1);
		$this->assertEquals($node->getRgt(), 2);
	}
	
	
	
	/** @test */
	public function getChildCountReturnsOneForOneAddedChild() {
		$parentNode = new Tx_PtExtbase_Tree_Node();
        $childNode1 = new Tx_PtExtbase_Tree_Node();
        $parentNode->addChild($childNode1);
        $this->assertEquals(1, $parentNode->getChildrenCount());
	}
	
	
	
	/** @test */
	public function getChildCountReturnsOneForAddedChildOfChild() {
	    $parentNode = new Tx_PtExtbase_Tree_Node();
        $childNode1 = new Tx_PtExtbase_Tree_Node();
        $childNode2 = new Tx_PtExtbase_Tree_Node();
        
        $childNode1->addChild($childNode2);
        $parentNode->addChild($childNode1);
        
        $this->assertEquals(1, $parentNode->getChildrenCount());
	}
	
	
	
	/** @test */
	public function getChildCountReturnsZeroIfThereAreNoChildren() {
		$parentNode = new Tx_PtExtbase_Tree_Node();
		$this->assertEquals(0, $parentNode->getChildrenCount());
	}
	
	
	
	/** @test */
	public function hasChildrenReturnsTrueIfNodeHasChildren() {
		$parentNode = new Tx_PtExtbase_Tree_Node();
		$childNode1 = new Tx_PtExtbase_Tree_Node();
		$parentNode->addChild($childNode1);
		$this->assertEquals(true, $parentNode->hasChildren());
	}
	
	
	
	/** @test */
	public function hasChildrenReturnsFalseIfNodeHasNoChildren() {
		$parentNode = new Tx_PtExtbase_Tree_Node();
		$this->assertEquals(false, $parentNode->hasChildren());
	}
	
	
	
    /** @test */
    public function getLevelReturnsTwoIfChildOfChild() {
        $parentNode = new Tx_PtExtbase_Tree_Node();
        $childNode1 = new Tx_PtExtbase_Tree_Node();
        $childNode2 = new Tx_PtExtbase_Tree_Node();
        
        $childNode1->addChild($childNode2);
        $parentNode->addChild($childNode1);
        
        $this->assertEquals(2, $childNode2->getLevel());
    }
    
    
    
    /** @test */
    public function getSubNodesReturnsSubNodesInCorrectOrder() {
    	$parentNode = new Tx_PtExtbase_Tree_Node('1');
        $childNode1 = new Tx_PtExtbase_Tree_Node('1.1');
        $childNode2 = new Tx_PtExtbase_Tree_Node('1.1.1');
        $childNode3 = new Tx_PtExtbase_Tree_Node('1.2');
        $childNode4 = new Tx_PtExtbase_Tree_Node('1.2.1');
        $childNode5 = new Tx_PtExtbase_Tree_Node('1.2.2');
        
        $childNode3->addChild($childNode4);
        $childNode3->addChild($childNode5);
        
        $childNode1->addChild($childNode2);
        
        $parentNode->addChild($childNode1);
        $parentNode->addChild($childNode3);
        
        $subNodes = $parentNode->getSubNodes()->toArray();
        $this->assertEquals($subNodes[0], $childNode1);
        $this->assertEquals($subNodes[1], $childNode2);
        $this->assertEquals($subNodes[2], $childNode3);
        $this->assertEquals($subNodes[3], $childNode4);
        $this->assertEquals($subNodes[4], $childNode5);
    }
    
    
    
    /** @test */
    public function addChildBeforeAddsChildBeforeGivenChild() {
    	$child1 = new Tx_PtExtbase_Tree_Node('1.1');
    	$child2 = new Tx_PtExtbase_Tree_Node('1.2');
    	$child3 = new Tx_PtExtbase_Tree_Node('1.3');
    	$parent = new Tx_PtExtbase_Tree_Node('1');
    	
    	$parent->addChild($child1);
    	$parent->addChildBefore($child2, $child1);
    	$parent->addChildBefore($child3, $child1);
    	
    	$children = $parent->getChildren()->toArray();
    	$this->assertEquals($children[0], $child2);
    	$this->assertEquals($children[1], $child3);
    	$this->assertEquals($children[2], $child1);
    }


	/** @test */
	public function addChildAfterAddChildAfterGivenChild() {
		$child1 = new Tx_PtExtbase_Tree_Node('1.1');
		$child2 = new Tx_PtExtbase_Tree_Node('1.2');
		$child3 = new Tx_PtExtbase_Tree_Node('1.3');
		$parent = new Tx_PtExtbase_Tree_Node('1');

		$parent->addChild($child1);
		$parent->addChildAfter($child2, $child1);
		$parent->addChildAfter($child3, $child1);

		$children = $parent->getChildren()->toArray();
		$this->assertEquals($children[0], $child1);
		$this->assertEquals($children[1], $child3);
		$this->assertEquals($children[2], $child2);
	}


	/**
	 * @test
	 */
	public function constructorSetsChildLabel() {
		$child = new Tx_PtExtbase_Tree_Node('test');
		$this->assertEquals('test', $child->getLabel());
	}



    /** @test */
    public function setAndGetNamespaceReturnsSetNamespace() {
        $node = new Tx_PtExtbase_Tree_Node();
        $node->setNamespace('testingNamespace');
        $this->assertEquals($node->getNamespace(), 'testingNamespace');
    }



	/** @test */
	public function clearRelativesRemovesParentAndChildren() {
		$child1 = new Tx_PtExtbase_Tree_Node('1.1');
		$child2 = new Tx_PtExtbase_Tree_Node('1.2');
		$child3 = new Tx_PtExtbase_Tree_Node('1.3');
		$parent = new Tx_PtExtbase_Tree_Node('1');

		$nodeProxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Tree_Node'); /** @var Tx_PtExtbase_Tree_Node $nodeProxy */
		$nodeProxy = new $nodeProxyClass();
		$nodeProxy->addChild($child1);
		$nodeProxy->addChildAfter($child2, $child1);
		$nodeProxy->addChildAfter($child3, $child1);
		$nodeProxy->setParent($parent);

		$this->assertNotNull($nodeProxy->_get('children'));
		$this->assertNotNull($nodeProxy->_get('parent'));

		$nodeProxy->clearRelatives();

		$this->assertNull($nodeProxy->_get('children'));
		$this->assertNull($nodeProxy->_get('parent'));
	}
	
}
?>