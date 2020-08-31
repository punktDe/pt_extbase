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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Testcase for abstract configuration builder class
 *
 * @package Tests
 * @subpackage Configuration
 * @author Michael Knoll 
 */
class Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfigurationBuilderTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * Holds an array of settings for testing
     *
     * @var array
     */
    protected $settings = [
        'testKey' => ['key1' => 'value1'],
        'cobjSetting' => [
            '_typoScriptNodeValue' => 'TEXT',
            'value' => 'TEST',
            'wrap' => 'x|x'
        ],
        'key2' => 'val2'
    ];
    
    
    
    /**
     * Holds a dummy implementation of abstract configuration builder for testing
     *
     * @var Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfigurationBuilder_Stub
     */
    protected $fixture;
    

    public function setUp(): void
    {
        $this->fixture = new Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfigurationBuilder_Stub($this->settings);
    }
    
    
    
    /** @test */
    public function genericCallReturnsConfigurationObjectForGivenConfiguration()
    {
        $configurationObject = $this->fixture->buildDummyConfiguration();
        $this->assertTrue(is_a($configurationObject, 'Tx_PtExtbase_Tests_Unit_Configuration_DummyConfigurationObject'));
        $this->assertEquals($configurationObject->getSettings(), $this->settings['testKey']);
    }
}



/**
 * Stub implementation of configuration builder for testing
 */
class Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfigurationBuilder_Stub extends \PunktDe\PtExtbase\Configuration\AbstractConfigurationBuilder
{
    /**
     * Set up configuration array for abstract configuration builder
     *
     * @var array
     */
    protected $configurationObjectSettings = [
        'dummy' => [
            'factory' => 'Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfigurationBuilder_DummyConfigurationObjectfactory',
        ]
    ];
}



/**
 * Stub implementation of a configuration object
 */
class Tx_PtExtbase_Tests_Unit_Configuration_DummyConfigurationObject extends \PunktDe\PtExtbase\Configuration\AbstractConfiguration
{
}



/**
 * Stub implementation of a configuration object factory
 */
class Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfigurationBuilder_DummyConfigurationObjectfactory
{
    public function getInstance(Tx_PtExtbase_Tests_Unit_Configuration_AbstractConfigurationBuilder_Stub $configurationBuilder)
    {
        $configObject = new Tx_PtExtbase_Tests_Unit_Configuration_DummyConfigurationObject($configurationBuilder, ['key1' => 'value1']);
        return $configObject;
    }
}
