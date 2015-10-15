<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Christian Herberger <herberger@punkt.de>
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
 * Login test
 *
 * @package Pt_Extbase
 * @subpackage Testing\Selenium\Backend
 */
class Tx_PtExtbase_Testing_Selenium_Backend_Modules
{
    public $testClass;

    public function __construct(PHPUnit_Extensions_SeleniumTestCase $testClass)
    {
        $this->testClass = $testClass;
    }

    /**
     * @param string $selector
     * @param string $elementToWaitFor
     * @return void
     */
    public function openBackendModule($selector, $elementToWaitFor = 'id=typo3-docbody')
    {
        for ($second = 0; ; $second++) {
            if ($second >= 60) {
                $this->testClass->fail("timeout");
            }
            try {
                if ($this->testClass->isElementPresent($selector)) {
                    break;
                }
            } catch (Exception $e) {
            }
            sleep(1);
        }
        $this->testClass->click($selector);
        $this->testClass->selectFrame("content");
        for ($second = 0; ; $second++) {
            if ($second >= 60) {
                $this->testClass->fail("timeout");
            }
            try {
                if ($this->testClass->isElementPresent($elementToWaitFor)) {
                    break;
                }
            } catch (Exception $e) {
            }
            sleep(1);
        }
    }
}
