<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
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
 * Class implements a testcase for namespace utility class
 *
 * @package Tests
 * @subpackage Utility
 * @author Daniel Lienert 
 * @author Michael Knoll
 */
class Tx_PtExtbase_Tests_Unit_Utility_NameSpaceArrayTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {
	
	/**
	 * Holds some sample data for testing
	 *
	 * @var array
	 */
	protected $varArray;
	
	
	
    /** @test */
	public function setUp() {
		$this->varArray = array('key1' => array(
		    'key2' => array(
		        'key3' => array(
		            'key4' => 'value1',
		            'key5' => 'value2'		
		         )
		    )
		)
		);
	}
	
	
	
    /** @test */
	public function extractingArrayContentByNamespaceReturnsCorrectValue() {
		$extractedValue = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($this->varArray,'key1.key2.key3.key4');
		$this->assertEquals($extractedValue, 'value1', 'The extracted Value should be Value 1');
	}
	
	
	
    /** @test */
	public function extractingNamespaceOnEmptyArrayReturnsEmptyArray() {
		$extractedValue = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace(array(),'key1.key2.key3.key4');
		$this->assertEquals($extractedValue, array(), 'The method should return an empty array');
	}
	
	
	
    /** @test */
	public function extractingNamespaceWithEmptyNamespaceReturnsWholeArray() {
		$extractedValue = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($this->varArray,'');
		$this->assertEquals($extractedValue,$this->varArray, 'The method should return teh complete var array');
	}
	
	
	
    /** @test */
	public function storingDataInArrayByNamespaceAndArrayOverwritesExistingValues() {
		$testArray['key1']['key2']['key3'] = 'test';
		$testArray2['key1']['key2']['key4'] = 'test4';
		
		$testArray = Tx_PtExtbase_Utility_NameSpace::saveDataInNamespaceTree('key1.key2.key3', $testArray, 'test2');
		
		$refArray['key1']['key2']['key3'] = 'test2';
		$refArray2['key1']['key2']['key4'] = 'test4';
		$this->assertEquals($testArray, $refArray);
		$this->assertEquals($testArray2, $refArray2);
	}
	
	
	
	/** @test */
	public function storingDataInEmptyArrayByNamespaceSetsData() {
		$testArray = array();
		$testArray = Tx_PtExtbase_Utility_NameSpace::saveDataInNamespaceTree('key1.key2.key3', $testArray, 'test2');
		
		$refArray['key1']['key2']['key3'] = 'test2';
		$this->assertEquals($testArray, $refArray);
	}

	
	
    /** @test */
    public function removeDataFromNamespaceTree() {
        $sampleArray = array('key1' => array(
                                'key2' => array(
                                    'key3' => 'testData1',
                                    'key4' => 'testData2'
                                ),
                                'key5' => 'testData3'
                            )
                        );
        
        $testArray = $sampleArray;
        unset($testArray['key1']['key2']['key3']);              
                        
        $nameSpaceString = 'key1.key2.key3';
        $alteredArray = Tx_PtExtbase_Utility_NameSpace::removeDataFromNamespaceTree($nameSpaceString, $sampleArray);
        
        $this->assertEquals($alteredArray, $testArray);     
    }
	
}

?>