<?php
namespace PunktDe\PtExtbase\Tests\Unit\Migration\Code;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 ElEquipo <el_equipo@punkt.de>, punkt.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use TYPO3\CMS\Core\Tests\UnitTestCase;

class MigratedClassesTest extends UnitTestCase
{

    public function legacyClassesProvider()
    {
        return [
            ['legacyClassName' => \Tx_PtExtbase_Utility_TcaManager::class],
            ['legacyClassName' => \Tx_PtExtbase_Logger_Logger::class],
            ['legacyClassName' => \Tx_PtExtbase_Logger_LoggerConfiguration::class],
            ['legacyClassName' => \Tx_PtExtbase_Utility_ExtensionSettings::class],
            ['legacyClassName' => \Tx_PtExtbase_Utility_FakeFrontendFactory::class]
        ];
    }

    /**
     * @test
     * @dataProvider legacyClassesProvider
     *
     * @param $legacyClassName
     */
    public function legacyClassesCanBeInitiated($legacyClassName) {
        $this->assertTrue(class_exists($legacyClassName));
        new $legacyClassName();
    }
}