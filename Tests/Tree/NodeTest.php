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
 * Testcase for pt_extbase category
 *
 * @package Tests
 * @subpackage Category
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_Tests_Tree_NodeTest extends Tx_PtExtbase_Tests_AbstractBaseTestcase {
     
	/** @test */
	public function constructReturnsInitializedCategory() {
		$category = new Tx_PtExtbase_Tree_Node();
		$this->assertEquals($category->getLft(), 1);
		$this->assertEquals($category->getRgt(), 2);
	}
	
	
	
	/** @test */
	public function getChildCountReturnsOneForOneAddedChild() {
		$parentCategory = new Tx_PtExtbase_Tree_Node();
        $childCategory1 = new Tx_PtExtbase_Tree_Node();
        $parentCategory->addChild($childCategory1);
        $this->assertEquals(1, $parentCategory->getChildrenCount());
	}
	
	
	
	/** @test */
	public function getChildCountReturnsOneForAddedChildOfChild() {
	    $parentCategory = new Tx_PtExtbase_Tree_Node();
        $childCategory1 = new Tx_PtExtbase_Tree_Node();
        $childCategory2 = new Tx_PtExtbase_Tree_Node();
        
        $childCategory1->addChild($childCategory2);
        $parentCategory->addChild($childCategory1);
        
        $this->assertEquals(1, $parentCategory->getChildrenCount());
	}
	
	
	
	/** @test */
	public function getChildCountReturnsZeroIfThereAreNoChildren() {
		$parentCategory = new Tx_PtExtbase_Tree_Node();
		$this->assertEquals(0, $parentCategory->getChildrenCount());
	}
	
	
	
	/** @test */
	public function hasChildrenReturnsTrueIfCategoryHasChildren() {
		$parentCategory = new Tx_PtExtbase_Tree_Node();
		$childCategory1 = new Tx_PtExtbase_Tree_Node();
		$parentCategory->addChild($childCategory1);
		$this->assertEquals(true, $parentCategory->hasChildren());
	}
	
	
	
	/** @test */
	public function hasChildrenReturnsFalseIfCategoryHasNoChildren() {
		$parentCategory = new Tx_PtExtbase_Tree_Node();
		$this->assertEquals(false, $parentCategory->hasChildren());
	}
	
	
	
    /** @test */
    public function getLevelReturnsTwoIfChildOfChild() {
        $parentCategory = new Tx_PtExtbase_Tree_Node();
        $childCategory1 = new Tx_PtExtbase_Tree_Node();
        $childCategory2 = new Tx_PtExtbase_Tree_Node();
        
        $childCategory1->addChild($childCategory2);
        $parentCategory->addChild($childCategory1);
        
        $this->assertEquals(2, $childCategory2->getLevel());
    }
    
    
    
    /** @test */
    public function getSubCategoriesReturnsSubCategoriesInCorrectOrder() {
    	$parentCategory = new Tx_PtExtbase_Tree_Node('1');
        $childCategory1 = new Tx_PtExtbase_Tree_Node('1.1');
        $childCategory2 = new Tx_PtExtbase_Tree_Node('1.1.1');
        $childCategory3 = new Tx_PtExtbase_Tree_Node('1.2');
        $childCategory4 = new Tx_PtExtbase_Tree_Node('1.2.1');
        $childCategory5 = new Tx_PtExtbase_Tree_Node('1.2.2');
        
        $childCategory3->addChild($childCategory4);
        $childCategory3->addChild($childCategory5);
        
        $childCategory1->addChild($childCategory2);
        
        $parentCategory->addChild($childCategory1);
        $parentCategory->addChild($childCategory3);
        
        $subCategories = $parentCategory->getSubCategories()->toArray();
        $this->assertEquals($subCategories[0], $childCategory1);
        $this->assertEquals($subCategories[1], $childCategory2);
        $this->assertEquals($subCategories[2], $childCategory3);
        $this->assertEquals($subCategories[3], $childCategory4);
        $this->assertEquals($subCategories[4], $childCategory5);
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
	
}
?>