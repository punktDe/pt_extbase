<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
*  Authors: Daniel Lienert, Michael Knoll, Joachim Mathes
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
 * SortableObjectCollection Testcase
 *
 * @package pt_extbase
 * @subpackage Tests\Collection
 */
class Tx_PtExtbase_Collection_SortableObjectCollectionTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	protected $sortableObjectCollectionProxyClass;



	protected $sortableObjectCollectionProxyMock;



	public function setUp() {
		$this->sortableObjectCollectionProxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Collection_SortableObjectCollection');
		$this->sortableObjectCollectionProxyMock = $this->getMockForAbstractClass($this->sortableObjectCollectionProxyClass);
	}



	public function tearDown() {
		unset($this->sortableObjectCollectionProxyMock);
	}



	public function testCompareItems() {
		$items = array(
			new Tx_PtExtbase_Collection_SortableObjectMock(5),
			new Tx_PtExtbase_Collection_SortableObjectMock(4)
		);
		$this->assertEquals(1, $this->sortableObjectCollectionProxyMock->compareItems($items[0], $items[1]));
		$this->assertEquals(-1, $this->sortableObjectCollectionProxyMock->compareItems($items[1], $items[0]));
		$this->assertEquals(0, $this->sortableObjectCollectionProxyMock->compareItems($items[1], $items[1]));
	}



	public function testSort() {
		$items = array(
			new Tx_PtExtbase_Collection_SortableObjectMock(4),
			new Tx_PtExtbase_Collection_SortableObjectMock(5),
			new Tx_PtExtbase_Collection_SortableObjectMock(2),
			new Tx_PtExtbase_Collection_SortableObjectMock(4),
			new Tx_PtExtbase_Collection_SortableObjectMock(3)
		);
		$this->sortableObjectCollectionProxyMock->_set('itemsArr', $items);
		$this->sortableObjectCollectionProxyMock->sort();
		$actual = $this->sortableObjectCollectionProxyMock->_get('itemsArr');

		$this->assertEquals(2, $actual[0]->getSortingValue());
		$this->assertEquals(3, $actual[1]->getSortingValue());
		$this->assertEquals(4, $actual[2]->getSortingValue());
		$this->assertEquals(4, $actual[3]->getSortingValue());
		$this->assertEquals(5, $actual[4]->getSortingValue());
	}

}



require_once t3lib_extMgm::extPath('pt_extbase') . 'Classes/Collection/SortableEntityInterface.php';

/**
 * Sortable object mock implementing the SortableEntityInterface
 */
class Tx_PtExtbase_Collection_SortableObjectMock implements Tx_PtExtbase_Collection_SortableEntityInterface {

	protected $sortingValue;

	public function __construct($sortingValue) {
		$this->sortingValue = $sortingValue;
	}

	public function getSortingValue() {
		return $this->sortingValue;
	}

}