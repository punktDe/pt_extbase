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
 * Unit tests for a get / post var adapter
 *
 * @package Tests
 * @subpackage State/GpVars
 * @author Michael Knoll
 */
class Tx_PtExtbase_Tests_Unit_State_GpVarsAdapterTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {
	
	/**
	 * Holds an array of get vars for testing
	 *
	 * @var array
	 */
	protected $getVars;
	
	
	
	/**
	 * Hols an array of post vars for testing
	 *
	 * @var array
	 */
	protected $postVars;
	
	
	
	/**
	 * Instance of getPostVar Adapger
	 *
	 * @var Tx_PtExtbase_State_GpVars_GpVarsAdapter
	 */
	protected $gpVarAdapter;
	
	
	
	/**
	 * Sets up the testcase and its test data
	 */
	public function setup() {
		$this->getVars = array('key1' => array(
		    'key2' => array(
		        'key3' => array(
		            'key4' => 'value1',
		            'key5' => 'value2'		
		         )
		    )
		)
		);

		$this->postVars = array('key1' => array(
            'key2' => array(
                'key3' => array(
                    'key4' => 'value3',
                    'key5' => 'value4'      
                 )
            )
        )
        );
	
        $this->gpVarAdapter = new Tx_PtExtbase_State_GpVars_GpVarsAdapter();
	    $this->gpVarAdapter->injectGetVars($this->getVars);
	    $this->gpVarAdapter->injectPostVars($this->postVars);
	}
	
	
	
	/** @test */
	public function requiredClassesExist() {
		$this->assertTrue(class_exists('Tx_PtExtbase_State_GpVars_GpVarsAdapter'));
		$this->assertTrue(class_exists('Tx_PtExtbase_Tests_Unit_State_Stubs_PersistableObject'));
	}
	
	
	
	/** @test */
	public function getGetVarsByNamespaceReturnsCorrectValues() {
		$extractedValue = $this->gpVarAdapter->getGetVarsByNamespace('key1.key2.key3.key4');
		$this->assertEquals($extractedValue, 'value1');
	}
	
	
	
	/** @test */
	public function getPostVarsByNamespaceReturnsCorrectValues() {
		$extractedValue = $this->gpVarAdapter->getPostVarsByNamespace('key1.key2.key3.key4');
		$this->assertEquals($extractedValue, 'value3');
	}
	
	
	
	/** @test */
	public function getGpVarsByNamespaceReturnsCorrectValues() {
		$extractedValue = $this->gpVarAdapter->extractGpVarsByNamespace('key1.key2.key3.key4');
		$this->assertEquals($extractedValue, 'value1');
	}
	
	
	
	/** @test */
	public function getPgVarsByNamespaceReturnsCorrectValue() {
		$extractedValue = $this->gpVarAdapter->extractPgVarsByNamespace('key1.key2.key3.key4');
        $this->assertEquals($extractedValue, 'value3');
	}
	
	
	
	/** @test */
    public function getPgVarsByNamespaceReturnsCorrectArray() {
        $extractedValue = $this->gpVarAdapter->extractPgVarsByNamespace('key1.key2.key3');
        $this->assertEquals($extractedValue, $this->postVars['key1']['key2']['key3']);
    }
    
    
    
    /** @test */
    public function getGpVarsByNamespaceReturnsCorrectArray() {
        $extractedValue = $this->gpVarAdapter->extractGpVarsByNamespace('key1.key2.key3');
        $this->assertEquals($extractedValue, $this->getVars['key1']['key2']['key3']);
    }
    
    
    
    /** @test */
    public function parametersCanBeInjectedIntoObject() {

    	$object = new Tx_PtExtbase_Tests_Unit_State_Stubs_GetPostVarObject();
    	$object->setObjectNamespace('key1.key2.key3');
    	
    	
    	$this->gpVarAdapter->injectParametersInObject($object);
    	
    	$injectedValues = $object->getValues();
    	$this->assertEquals($injectedValues, $this->postVars['key1']['key2']['key3']);
    }
	
}

?>