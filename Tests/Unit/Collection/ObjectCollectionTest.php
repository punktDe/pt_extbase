<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Unit test for object collection class
 * 
 * @author Michael Knoll 
 * @author Fabrizio Branca
 * @package Tests
 * @subpackage Collection
 */
class Tx_PtExtbase_Collection_ObjectCollectionTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var Tx_PtExtbase_Tests_Unit_Collection_ObjectCollectionMock
     */
    private $fixture;

    
    
    /**
     * Setting up the fixture for the tests.
     * This will be called before each single test
     */
    protected function setUp(): void
    {
        $this->fixture = new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollectionMock();
    }

    
    
    /**
     * Cleaning up after each single test
     */
    protected function tearDown(): void
    {
        unset($this->fixture);
    }
    
    
    
    /** @test */
    public function addingAnObjectOfTheCorrectType()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock, 1);
        $this->assertTrue($this->fixture->count() === 1, 'Collection does not contain 1 item!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(1), 'Object has not the right type!');
    }
    
    
    
    /** @test */
    public function addingAnObjectOfTheWrongTypeThrowsException()
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'] = false;
        }
        $this->expectException(PunktDe\PtExtbase\Exception\Exception::class);
        $this->fixture->addItem(new StdClass('hello', 'world'));
    }
    
    
    
    /** @test */
    public function test_appendingAnObjectWithArrayAccess()
    {
        $this->fixture[1] = new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock();
        $this->assertTrue($this->fixture->count() === 1, 'Collection does not contain 1 item!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(1), 'Object has not the right type!');
    }
    
    
    
    /** @test */
    public function test_setNonExistingIdAsSelected()
    {

        $this->expectException(PunktDe\PtExtbase\Exception\Exception::class);
        $this->fixture->setSelectedId(5);
    }
    
    
    
    /** @test */
    public function test_setExistingIdAsSelected()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 5);
        $this->fixture->setSelectedId(5);
        $this->assertTrue($this->fixture->getSelectedId() === 5);
    }
    
    
    
    /** @test */
    public function test_pushAndPopAnObject()
    {
        $this->fixture->push(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock());
        $this->assertTrue($this->fixture->count() === 1, 'Collection does not contain 1 item!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->pop(), 'Object has not the right type!');
    }
    
    
    
    /** @test */
    public function test_unshiftAndShiftAnObject()
    {
        $this->fixture->unshift(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock());
        $this->assertTrue($this->fixture->count() === 1, 'Collection does not contain 1 item!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->shift(), 'Object has not the right type!');
    }
    
    
    
    /** @test */
    public function test_integerishIdsChangeAfterShift()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 5);
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 6);
        $this->assertTrue($this->fixture->count() === 2, 'Collection does not contain 2 items!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->shift(), 'Object has not the right type!');
        $this->assertTrue($this->fixture->count() === 1, 'Collection does not contain 1 item!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(0));
    }
    
    
    
    /** @test */
    public function test_integerishIdsDoNotChangeAfterPop()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 5);
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 6);
        $this->assertTrue($this->fixture->count() === 2, 'Collection does not contain 2 items!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->pop(), 'Object has not the right type!');
        $this->assertTrue($this->fixture->count() === 1, 'Collection does not contain 1 item!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(5));
    }
    
    
    
    /** @test */
    public function test_integerishIdsChangeAfterUnshift()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 6);
        $this->fixture->unshift(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock());
        $this->assertTrue($this->fixture->count() === 2, 'Collection does not contain 2 items!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(0));
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(1));
    }
    
    
    
    /** @test */
    public function test_integerishIdsDoNotChangeAfterPush()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 6);
        $this->fixture->push(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock());
        $this->assertTrue($this->fixture->count() === 2, 'Collection does not contain 2 items!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(6));
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(7));
    }



    /** @test */
    public function test_seletectedIdIsClearedWhenSelectedItemIsPopped()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 6);
        $this->fixture->setSelectedId(6);
        $this->fixture->pop();
        $this->assertNull($this->fixture->getSelectedId());
    }



    /** @test */
    public function test_seletectedIdIsClearedWhenSelectedItemIsShifted()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 6);
        $this->fixture->setSelectedId(6);
        $this->fixture->shift();
        $this->assertNull($this->fixture->getSelectedId());
    }



    /** @test */
    public function test_integerishIdsDoNotChangeAfterShiftWithParameterTrue()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 5);
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 6);
        $this->assertTrue($this->fixture->count() === 2, 'Collection does not contain 2 items!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->shift(true), 'Object has not the right type!');
        $this->assertTrue($this->fixture->count() === 1, 'Collection does not contain 1 item!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(6));
    }
    
    
    
    /** @test */
    public function test_integerishIdsDoNotChangeAfterUnshiftWithSecondParameterTrue()
    {
        $this->fixture->addItem(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), 6);
        $this->fixture->unshift(new Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock(), true);
        $this->assertTrue($this->fixture->count() === 2, 'Collection does not contain 2 items!');
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(6));
        $this->assertInstanceOf('Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock', $this->fixture->getItemById(0));
    }
}



/**
 * Simple test object
 */
class Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock
{
}


/**
 * Test collection, because \PunktDe\PtExtbase\Collection\ObjectCollection is an abstract class
 */
class Tx_PtExtbase_Tests_Unit_Collection_ObjectCollectionMock extends \PunktDe\PtExtbase\Collection\ObjectCollection
{
    protected $restrictedClassName = 'Tx_PtExtbase_Tests_Unit_Collection_ObjectCollection_TestObjectMock';
    
    public function get_itemsArr()
    {
        return $this->itemsArr;
    }
}
