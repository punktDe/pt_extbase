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

require_once t3lib_extMgm::extPath('pt_extbase') . 'Classes/Configuration/AbstractConfiguration.php';

/**
 * Testcase for abstract configuration class
 *
 * @package Tests
 * @subpackage Configuration
 * @author Michael Knoll 
 */
class Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfigurationTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/**
	 * Some settings for testing
	 *
	 * @var array
	 */
	protected $settings = array(
		'key1' => array(
			'key1-1' => 'value1-1',
			'key1-2' => 'value1-2',
			'key1-3' => array(
				'key1-3-1' => 'value1-3-1'
			)
		),
		'key2' => 'value2',
		'key3' => '',
		'key4' => 'value4',
		'key6' => '',
		'key7' => '1',
		'key8' => 'value8',
		'key9' => '0',
		'cobjSetting' => array(
				'_typoScriptNodeValue' => 'TEXT',
				'value' => 'TEST',
				'wrap' => 'x|x'
			),
	);
	
	
	
	/**
	 * Holds an instance of a concrete implementation
	 * 
	 * @var Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfiguration_Stub
	 */
	protected $concreteConfiguration;
	
	
	
	/**
	 * Holds an instance of configuration builder stub defined below
	 *
	 * @var Tx_PtExtabse_Configuration_AbstractConfigurationBuilder_Stub
	 */
	protected $configurationBuilderStub;
	
	
	
	/**
	 * Sets up testcase
	 */
	public function setup() {
		$this->configurationBuilderStub = new Tx_PtExtbase_Configuration_AbstractConfigurationBuilder_Stub($this->settings);
		$this->concreteConfiguration = new Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfiguration_Stub($this->configurationBuilderStub, $this->settings);
	}
	
	
	
	/** @test */
	public function configurationForTestIsWorking() {
		$this->assertTrue(is_a($this->concreteConfiguration, 'Tx_PtExtbase_Configuration_AbstractConfiguration'));
	}
	
	
	
	/** @test */
	public function getConfigurationBuilderReturnsConfigurationBuilder() {
		$this->assertEquals($this->concreteConfiguration->getConfigurationBuilder(), $this->configurationBuilderStub);
	}
	
	
	
	/** @test */
	public function getSettingsReturnsCompleteSettingsArray() {
		$this->assertEquals($this->concreteConfiguration->getSettings(), $this->settings);
	}
	
	
	
	/** @test */
	public function getSettingsReturnsCorrectSettingsArrayForGivenTspath() {
		$this->assertEquals($this->concreteConfiguration->getSettings('key1.key1-3'), $this->settings['key1']['key1-3']);
	}
	
	
	
	/** @test */
	public function getSettingsReturnsEmptyArrayIfKeyDoesNotExist() {
		$this->assertEquals($this->concreteConfiguration->getSettings('hurzdieburz'), array());
	}
	
	
	
	/** @test */
	public function initMethodIsCalledWhenConstructorIsCalled() {
		$this->assertTrue($this->concreteConfiguration->initIsCalled);
	}
	
	
	
	/** @test */
	public function setValueIfExistsSetsCorrectValueForGivenPropertyName() {
		$this->assertEquals($this->concreteConfiguration->value2, $this->settings['key2']);
	}
	
	
	
	/** @test */
	public function setValueIfExistsSetsCorrectValueIfNoPropertyNameIsGiven() {
		$this->assertEquals($this->concreteConfiguration->key2, $this->settings['key2']);
	}
	
	
	
    /** @test */
	public function setValueIfExistsAndNotNothingSetsNoValueIfNothing() {
		$this->assertEquals($this->concreteConfiguration->key3, 'nix');
	}
	
	
	
	/** @test */
	public function setValueIfExistsAndNotNothingSetsValueIfNotNothing() {
		$this->assertEquals($this->concreteConfiguration->key4, $this->settings['key4']);
	}
	
	
	
    /** @test */
	public function setValueIfExistsAndNotNothingSetsValueForGivenPropertyIfNotNothing() {
		$this->assertEquals($this->concreteConfiguration->value1, $this->settings['key4']);
	}
	
	
	
    /** @test */
	public function setBooleanIfExistsAndNotNothingSetsNothingIfKeyDoesNotExist() {
		$this->assertEquals($this->concreteConfiguration->key5, 'nix');
	}
	
	
	
	/** @test */
	public function setBooleanIfExistsAndNotNothingSetsFalseIfKeyDoesExistButIsEmpty() {
		$this->assertEquals($this->concreteConfiguration->key6, false);
	}
	
	
	
	/** @test */
	public function setBooleanIfExistsAndNotNothingSetsTrueIfKeyDoesExistAndIsOne() {
		$this->assertEquals($this->concreteConfiguration->key7, true);
	}
	
	
	
	/** @test */
	public function setRequiredPropteryThrowsExceptionIfEmptyKey() {
		try {
		    $this->concreteConfiguration->shouldThrowException();
		} catch(Exception $e) {
			$this->assertEquals($e->getMessage(), 'fehler');
			return;
		}
		$this->fail('No Exception is thrown, if required property is not available!');
	}



	/** @test */
	public function setRequiredPropertyThrowsNoExceptionIfPropertyIsTheIntegerZero() {
		try {
			$this->concreteConfiguration->shouldNotThrowException();
		} catch(Exception $e) {
			$this->fail("No Exception should be thrown, if required property is string '0'!");
			return;
		}
	}


	
	/** @test */
	public function setRequiredPropertySetsPropertyIfNoPropertyNameIsGiven() {
		$this->assertEquals($this->concreteConfiguration->key8, $this->settings['key8']);
	}
	
	
	
	/** @test */
	public function setRequiredPropertySetsPropertyIfPropertyNameIsGiven() {
		$this->assertEquals($this->concreteConfiguration->value3, $this->settings['key8']);
	}
}



/**
 * Stub implementation of configuration for testing
 */
class Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfiguration_Stub extends Tx_PtExtbase_Configuration_AbstractConfiguration {
	
	public $value1;
	
	
	public $key2;
	
	
	public $key3 = 'nix';
	
	
	public $key4;
	
	
	public $value2;
	
	
	public $value3;
	
	
	public $key5 = 'nix';
	
	
	public $key6;
	
	
	public $key7;
	
	
	public $key8;


	public $key9;

	
	
	public $initIsCalled = false;
	
	
	
    protected function init() {
    	$this->initIsCalled = true;
    	$this->setValueIfExists('key2', 'value2');
    	$this->setValueIfExists('key2');
    	$this->setValueIfExistsAndNotNothing('key3');
    	$this->setValueIfExistsAndNotNothing('key4');
    	$this->setValueIfExistsAndNotNothing('key4', 'value1');
    	$this->setBooleanIfExistsAndNotNothing('key5');
    	$this->setBooleanIfExistsAndNotNothing('key6');
    	$this->setBooleanIfExistsAndNotNothing('key7');
    	$this->setRequiredValue('key8', '');
    	$this->setRequiredValue('key8', '', 'value3');
    }
    
    
    
    public function shouldThrowException() {
        $this->setRequiredValue('key5', 'fehler');
    }



	public function shouldNotThrowException() {
		$this->setRequiredValue('key9', 'error');
	}
}



class Tx_PtExtbase_Configuration_AbstractConfigurationBuilder_Stub extends Tx_PtExtbase_Configuration_AbstractConfigurationBuilder {
	
}

?>