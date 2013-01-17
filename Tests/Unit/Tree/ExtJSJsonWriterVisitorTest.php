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
 * Testcase for ExtJSJsonWriterVisitor
 *
 * @package Tests
 * @subpackage Domain\Model
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Tests_Unit_Tree_ExtJSJsonWriterVisitorTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {


	/**
	 * @var Tx_PtExtbase_Tree_ExtJsJsonWriterVisitor
	 */
	protected $accessibleProxy;


	public function setUp() {
		$this->accessibleProxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Tree_ExtJsJsonWriterVisitor');
		$this->accessibleProxy = new $this->accessibleProxyClass();
	}


	public function tearDown() {
		unset($this->accessibleProxy);
	}



	/** @test */
	public function classExists() {
		$this->assertTrue(class_exists('Tx_PtExtbase_Tree_ExtJsJsonWriterVisitor'));
	}


	
	/** @test */
	public function visitorCreatesCorrectExtJSCompatibleArray() {
	   $visitors[] = $this->accessibleProxy;
	   $arrayTreeWriter = new Tx_PtExtbase_Tree_ArrayTreeWriter($visitors, $this->accessibleProxy);
		
		$tree = $arrayTreeWriter->writeTree($this->getTestTree());

		$this->assertFalse($tree['leaf']);
		$this->assertTrue($tree['children'][0]['children'][0]['leaf']);
	}


	public function setSelectionDataProvider() {
		return array(
			'singleNotSelected' => array('multiple' => false, 'selection' => null, 'expected' => array()),
			'singleSelected' => array('multiple' => false, 'selection' => 1, 'expected' => array('cls' => 'selectedNode')),
			'multipleButNotChecked' => array('multiple' => true, 'selection' => array(), 'expected' => array('checked' => false)),
			'multipleAndChecked' => array('multiple' => true, 'selection' => array(1), 'expected' => array('checked' => true)),
			'multipleButNotChecked' => array('multiple' => true, 'selection' => array(), 'expected' => array('checked' => false)),
		);
	}


	/**
	 * @test
	 * @dataProvider setSelectionDataProvider
	 * @param $multiple
	 * @param $selection
	 * @param $expected
	 */
	public function setSelectionOnNodeArray($multiple, $selection, $expected) {
		$this->accessibleProxy->setMultipleSelect($multiple);
		$this->accessibleProxy->setSelection($selection);

		$node = Tx_PtExtbase_Tests_Unit_Tree_NodeMock::createNode('1', 0, 0, 1, '1');

		$nodeArray = array();
		$this->accessibleProxy->_callRef('setSelectionOnNodeArray', $node, $nodeArray);

		$this->assertEquals($expected, $nodeArray);
	}


	/** @test */
	public function visitorSetsCheckedFalseInMultipleMode() {
	   $visitors[] = $this->accessibleProxy;

	   $this->accessibleProxy->setMultipleSelect(true);

		$arrayTreeWriter = new Tx_PtExtbase_Tree_ArrayTreeWriter($visitors, $this->accessibleProxy);

		$tree = $arrayTreeWriter->writeTree($this->getTestTree());

		$this->assertEquals(false,$tree['checked']);
		$this->assertEquals(false,$tree['children'][0]['checked']);
	}


	/** @test */
	public function visitorSetsCheckedToSelectionInMultipleMode() {
	   $visitors[] = $this->accessibleProxy;

	   $this->accessibleProxy->setMultipleSelect(true);
		$this->accessibleProxy->setSelection(array(1,5));
		
		$arrayTreeWriter = new Tx_PtExtbase_Tree_ArrayTreeWriter($visitors, $this->accessibleProxy);

		$tree = $arrayTreeWriter->writeTree($this->getTestTree());
		
		$this->assertEquals(true,$tree['checked']);
		$this->assertEquals(false,$tree['children'][0]['checked']);
		$this->assertEquals(true,$tree['children'][1]['checked']);
	}




	/** @test */
	public function visitorSetsClassToSelectedInSingleMode() {
	   $visitors[] = $this->accessibleProxy;

	   $this->accessibleProxy->setMultipleSelect(false);
		$this->accessibleProxy->setSelection(5);

		$arrayTreeWriter = new Tx_PtExtbase_Tree_ArrayTreeWriter($visitors, $this->accessibleProxy);

		$tree = $arrayTreeWriter->writeTree($this->getTestTree());
		$this->assertNull($tree['cls']);
		$this->assertNull($tree['children'][0]['cls']);
		$this->assertEquals('selectedNode', $tree['children'][1]['cls']);
	}


	public function callBackSetterDataProvider() {
		return array(
			'noObject' => array('target' => '', 'method' => 'callBackSetterDataProvider', 'throwsException' => true),
			'noMethod' => array('target' => $this, 'method' => '', 'throwsException' => true),
			'correctObjectAndMethod' => array('target' => $this, 'method' => 'callBackSetterDataProvider', 'throwsException' => false),
			'correctObjectIncorrectMethod' => array('target' => $this, 'method' => 'notAvailable', 'throwsException' => true),
			'correctClassName' => array('target' => 'Tx_PtExtbase_Tests_Unit_Tree_ExtJSJsonWriterVisitorTest', 'method' => 'callBackSetterDataProvider', 'throwsException' => false),
			'inCorrectClassName' => array('target' => 'foo', 'method' => 'foo', 'throwsException' => true),
		);
	}


	/**
	 * @param $target
	 * @param $method
	 * @param $throwsException
	 * @return mixed
	 * @test
	 * @dataProvider callBackSetterDataProvider
	 */
	public function registerFirstVisitCallback($target, $method, $throwsException) {
		try {
			$this->accessibleProxy->registerFirstVisitCallback($target, $method);
		} catch (Exception $e) {
			if($throwsException) {
				return;
			} else {
				$this->fail('An Exception was thrown but should not ... ');
			}
		}

		if($throwsException) {
			$this->fail('No Exception was thrown but should ... ');
		}

		$fvcArray = $this->accessibleProxy->_get('firstVisitCallback');
		$this->assertSame($target, $fvcArray['target']);
		$this->assertSame($method, $fvcArray['method']);
	}



	/**
	 * @param $target
	 * @param $method
	 * @param $throwsException
	 * @return mixed
	 * @test
	 * @dataProvider callBackSetterDataProvider
	 */
	public function registerLastVisitCallback($target, $method, $throwsException) {
		try {
			$this->accessibleProxy->registerLastVisitCallback($target, $method);
		} catch (Exception $e) {
			if($throwsException) {
				return;
			} else {
				$this->fail('An Exception was thrown but should not ... ');
			}
		}

		if($throwsException) {
			$this->fail('No Exception was thrown but should ... ');
		}

		$fvcArray = $this->accessibleProxy->_get('lastVisitCallback');
		$this->assertSame($target, $fvcArray['target']);
		$this->assertSame($method, $fvcArray['method']);
	}


	/**
	 * @test
	 */
	public function registeredCallbackIsCalledOnFirstVisit() {

		$node = new Tx_PtExtbase_Tree_Node('test');

		$callBackObject = $this->getMockBuilder('Tx_PtExtbase_Tests_Unit_Tree_ExtJSJsonWriterVisitorTest_CallBackObject')
						->setMethods(array('callBackMethod'))
						->getMock();
		$callBackObject->expects($this->once())->method('callBackMethod');

		$this->accessibleProxy->registerFirstVisitCallback($callBackObject, 'callBackMethod');
		$index = 1;
		$this->accessibleProxy->doFirstVisit($node, $index);
	}



	/**
	 * @test
	 */
	public function registeredCallbackIsCalledOnLastVisit() {

		$node = new Tx_PtExtbase_Tree_Node('test');

		$callBackObject = $this->getMockBuilder('Tx_PtExtbase_Tests_Unit_Tree_ExtJSJsonWriterVisitorTest_CallBackObject')
						->setMethods(array('callBackMethod'))
						->getMock();
		$callBackObject->expects($this->once())->method('callBackMethod');

		$this->accessibleProxy->registerLastVisitCallback($callBackObject, 'callBackMethod');
		$index = 1;

		$this->accessibleProxy->doFirstVisit($node, $index);
		$this->accessibleProxy->doLastVisit($node, $index);
	}



	/**
	 * @return Tx_PtExtbase_Tree_Tree
	 */
	protected function getTestTree() {
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
		return $tree;
	}
	
}

class Tx_PtExtbase_Tests_Unit_Tree_ExtJSJsonWriterVisitorTest_CallBackObject {
	public function callBackMethod($node, $nodeArray) {

	}
}

?>

