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
 * @author Daniel Lienert <lienert@punkt.de>
 */
class Tx_PtExtbase_Tests_Unit_Tree_NodeRepositoryTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {
     
	/**
	 * @var Tx_PtExtbase_Tree_NodeRepository
	 */
	protected $accessibleProxyClass;

	/**
	 * @var Tx_PtExtbase_Tree_NodeRepository
	 */
	protected $accessibleProxy;


	public function setUp() {
		$this->accessibleProxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Tree_NodeRepository');
		$this->accessibleProxy = new $this->accessibleProxyClass();
	}

	public function tearDown() {
		unset($this->accessibleProxy);
	}

	/**
	 * @test
	 */
	public function classExists() {
		$this->assertTrue(class_exists('Tx_PtExtbase_Tree_NodeRepository'));
	}


	/**
	 * @test
	 */
	public function markNodesAccessible() {
		$nodes[] = $this->getNodeProxy(1, 'node1');
		$nodes[] = $this->getNodeProxy(2, 'node2');

		$accessibleNodes[] = $this->getNodeProxy(2, 'node2');

		$this->accessibleProxy->_callRef('markNodesAccessible', $nodes, $accessibleNodes);

		$this->assertFalse($nodes[0]->isAccessible());
		$this->assertTrue($nodes[1]->isAccessible());
	}



	/**
	 * @param $uid
	 * @param $label
	 * @return Tx_PtExtbase_Tree_Node
	 */
	protected function getNodeProxy($uid, $label) {
		$accessibleProxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Tree_Node');
		$accessibleNode = new $accessibleProxyClass();

		$accessibleNode->_set('uid', $uid);
		$accessibleNode->setLabel($label);

		return $accessibleNode;
	}
	
}
?>