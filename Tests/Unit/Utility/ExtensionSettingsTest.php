<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
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
 * ExtensionSettings Testcase
 *
 * @package pt_extbase
 * @subpackage Tests\Unit\Utility
 */
class Tx_PtExtbase_Tests_Unit_Utility_ExtensionSettingsTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    protected $proxyClass;

    protected $proxy;

    public function setUp(): void
    {
        $this->proxyClass = $this->buildAccessibleProxy(\PunktDe\PtExtbase\Utility\ExtensionSettings::class);
        $this->proxy = new $this->proxyClass();
    }

    public function tearDown(): void
    {
        unset($this->proxy);
    }

    /**
     * @test
     */
    public function cacheExtensionSettingsCachesIfNecessary()
    {
        $key = 'pt_rem';
        $settings = [
            $key => [
                'Michael' => 'Stipe',
                'Peter' => 'Buck',
                'Mike' => 'Mills'
            ]
        ];

        $proxyMock = $this->getMockBuilder($this->proxyClass)
                ->setMethods(['loadExtensionSettings'])
                ->getMock();
        $proxyMock->expects($this->once())
                ->method('loadExtensionSettings')
                ->with($key)
                ->will($this->returnValue($settings[$key]));

        $expected = $settings;
        $proxyMock->_call('cacheExtensionSettings', $key);
        $actual = $proxyMock->_get('extensionSettings');
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function cacheExtensionSettingsDoesNotCacheIfNotNecessary()
    {
        $key = 'pt_rem';
        $settings = [
            $key => [
                'Michael' => 'Stipe',
                'Peter' => 'Buck',
                'Mike' => 'Mills'
            ]
        ];

        $proxyMock = $this->getMockBuilder($this->proxyClass)
                ->setMethods(['loadExtensionSettings'])
                ->getMock();
        $proxyMock->expects($this->never())
                ->method('loadExtensionSettings');

        $proxyMock->_set('extensionSettings', $settings);
        $proxyMock->_call('cacheExtensionSettings', $key);
    }

    /**
     * @test
     */
    public function getExtensionSettingsReturnsSettings()
    {
        $key = 'pt_rem';
        $settings = [
            $key => [
                'Michael' => 'Stipe',
                'Peter' => 'Buck',
                'Mike' => 'Mills'
            ]
        ];

        $proxyMock = $this->getMockBuilder($this->proxyClass)
                ->setMethods(['cacheExtensionSettings'])
                ->getMock();
        $proxyMock->expects($this->once())
                ->method('cacheExtensionSettings')
                ->with($key);

        $proxyMock->_set('extensionSettings', $settings);

        $expected = $settings[$key];
        $actual = $proxyMock->getExtensionSettings($key);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getKeyFromExtensionSettingsReturnsKeyValueIfAvailable()
    {
        $extensionKey = 'pt_rem';
        $key = 'Michael';
        $settings = [
            $extensionKey => [
                $key => 'Stipe',
                'Peter' => 'Buck',
                'Mike' => 'Mills'
            ]
        ];

        $proxyMock = $this->getMockBuilder($this->proxyClass)
                ->setMethods(['cacheExtensionSettings'])
                ->getMock();
        $proxyMock->expects($this->once())
                ->method('cacheExtensionSettings')
                ->with($extensionKey);

        $proxyMock->_set('extensionSettings', $settings);

        $expected = $settings[$extensionKey][$key];
        $actual = $proxyMock->getKeyFromExtensionSettings($extensionKey, $key);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getKeyFromExtensionSettingsThrowsExceptionIfNotSet()
    {
        $extensionKey = 'pt_rem';
        $key = 'Michael';
        $settings = [
            $extensionKey => [
                'Peter' => 'Buck',
                'Mike' => 'Mills'
            ]
        ];

        $proxyMock = $this->getMockBuilder($this->proxyClass)
                ->setMethods(['cacheExtensionSettings'])
                ->getMock();
        $proxyMock->expects($this->once())
                ->method('cacheExtensionSettings')
                ->with($extensionKey);

        $proxyMock->_set('extensionSettings', $settings);

        try {
            $proxyMock->getKeyFromExtensionSettings($extensionKey, $key);
        } catch (\Exception $e) {
            return;
        }

        $this->fail('No exception thrown!');
    }
}
