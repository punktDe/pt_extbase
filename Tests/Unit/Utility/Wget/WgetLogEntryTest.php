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

class WgetLogEntryTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Wget\WgetLogEntry
     */
    protected $wgetLogEntry;


    public function setUp(): void
    {
        $wgetLogEntryProxyClass = $this->buildAccessibleProxy(\PunktDe\PtExtbase\Utility\Wget\WgetLogEntry::class);
        $this->wgetLogEntry = new $wgetLogEntryProxyClass();
    }


    public function tearDown(): void
    {
    }


    /**
     * @test
     */
    public function wgetLogEntryCanBeIdentifiedAsError()
    {
        $this->wgetLogEntry->setStatus(500);
        $this->assertTrue($this->wgetLogEntry->isError());
    }
}
