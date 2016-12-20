<?php
 /***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lienert <lienert@punkt.de>
 *
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

namespace PunktDe\PtExtbase\Tests\Utility\Wget;

use PunktDe\PtExtbase\Utility\Files;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class WgetLogTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Wget\WgetLog
     */
    protected $wgetLog;


    public function setUp()
    {
        $wgetLogProxyClass = $this->buildAccessibleProxy('PunktDe\PtExtbase\Utility\Wget\WgetLog');
        $this->wgetLog = $this->objectManager->get($wgetLogProxyClass);
    }


    public function tearDown()
    {
    }


    public function logEntryDataProvider()
    {
        return [
            'sandwich400' => ['codes' => [200,404,200], 'hasError' => true],
            'sandwich500' => ['codes' => [200,503,200], 'hasError' => true],
            'twoErrors' => ['codes' => [200,503,400], 'hasError' => true],
            'noErrors' => ['codes' => [200,200], 'hasError' => false]
        ];
    }


    /**
     * @test
     * @dataProvider logEntryDataProvider
     *
     * @param $codes
     * @param $hasErrors
     */
    public function hasErrors($codes, $hasErrors)
    {
        foreach ($codes as $code) {
            $logEntry = $this->objectManager->get('PunktDe\PtExtbase\Utility\Wget\WgetLogEntry'); /** @var \PunktDe\PtExtbase\Utility\Wget\WgetLogEntry $logEntry */
            $logEntry->setStatus($code);
            $this->wgetLog->addLogEntry($logEntry);
        }

        $this->assertEquals($hasErrors, $this->wgetLog->hasErrors());
    }


    /**
     * @test
     * @dataProvider logEntryDataProvider
     *
     * @param $codes
     * @param $hasErrors
     */
    public function getErrors($codes, $hasErrors)
    {
        foreach ($codes as $code) {
            $logEntry = $this->objectManager->get('PunktDe\PtExtbase\Utility\Wget\WgetLogEntry'); /** @var \PunktDe\PtExtbase\Utility\Wget\WgetLogEntry $logEntry */
            $logEntry->setStatus($code);
            $this->wgetLog->addLogEntry($logEntry);
        }

        $logEntries = $this->wgetLog->getErrors();

        foreach ($logEntries as $logEntry) {
            $this->assertTrue($logEntry->isError());
        }
    }
}
