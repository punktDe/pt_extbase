<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <knoll@punkt.de>
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
 * Testcase for Tree PathViewhelper
 *
 * @package pt_extbase
 * @subpackage Tests\ViewHelpers\Tree
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Tests_Unit_ViewHelpers_Tree_PathViewhelperTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/**
	 * @var string
	 */
	protected $accessibleProxyClass;

	/**
	 * @var Tx_PtExtbase_ViewHelpers_Tree_PathViewHelper
	 */
	protected $accessibleProxy;


	public function setUp() {
		$this->accessibleProxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_ViewHelpers_Tree_PathViewHelper');
		$this->accessibleProxy = new $this->accessibleProxyClass();
	}

	public function tearDown() {
		unset($this->accessibleProxy);
	}

	/**
	 * @test
	 */
	public function classExists() {
		$this->assertTrue(class_exists('Tx_PtExtbase_ViewHelpers_Tree_PathViewHelper'));
	}



	/**
	 * @test
	 */
	public function getPathFromRootToNodeWithRoot() {

		$arguments = array (
			'node' => 2,
			'skipRoot' => FALSE,
		);
		
		$tree = $this->getTreeMock();

		$nodePathBuilder = new Tx_PtExtbase_Tree_NodePathBuilder();
		$nodePathBuilder->setTree($tree);
		$this->accessibleProxy->injectNodePathBuilder($nodePathBuilder);

		$this->accessibleProxy->_set('arguments', $arguments);
		$result = $this->accessibleProxy->_call('getPathFromRootToNode', $tree);


		$this->assertEquals(2, count($result));
		$this->assertEquals('root', $result[0]->getLabel());
		$this->assertEquals('firstSubNode', $result[1]->getLabel());
	}


	/**
	 * @test
	 */
	public function getPathFromRootToNodeWithoutRoot() {

		$arguments = array (
			'node' => 2,
			'skipRoot' => TRUE,
		);

		$tree = $this->getTreeMock();

		$nodePathBuilder = new Tx_PtExtbase_Tree_NodePathBuilder();
		$nodePathBuilder->setTree($tree);
		$this->accessibleProxy->injectNodePathBuilder($nodePathBuilder);

		$this->accessibleProxy->_set('arguments', $arguments);
		$result = $this->accessibleProxy->_call('getPathFromRootToNode', $tree);



		$this->assertEquals(1, count($result));
		$this->assertEquals('firstSubNode', $result[0]->getLabel());
	}


	/**
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getTreeMock() {

		$root = new Tx_PtExtbase_Tree_Node('root');
		$subNode = new Tx_PtExtbase_Tree_Node('firstSubNode');
		$root->addChild($subNode);

		$treeMock = $this->getMockBuilder('Tx_PtExtbase_Tree_Tree')
				->setMethods(array('getNodeByUid', 'getRoot'))
				->getMock();

		$treeMock->expects($this->once())
				->method('getNodeByUid')->with(2)
				->will($this->returnValue($subNode));

		$treeMock->expects($this->any())
						->method('getRoot')
						->will($this->returnValue($root));
		return $treeMock;
	}

}

?>