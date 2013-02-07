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
class Tx_PtExtbase_Testing_Selenium_Backend_Security {

	public $testClass;

	public function __construct(PHPUnit_Extensions_SeleniumTestCase $testClass) {
		$this->testClass = $testClass;
	}

	/**
	 * @param $username string
	 * @param $password string
	 */
	public function backendLogin($username, $password) {
		$this->testClass->open($GLOBALS['SeleniumUrl']);
		$this->testClass->waitForPageToLoad();

		$this->testClass->open($GLOBALS['SeleniumUrl']."/typo3/");
		$this->testClass->waitForPageToLoad();
		
		$this->checkForLogoutButton();
		
		$this->testClass->open($GLOBALS['SeleniumUrl']."/typo3/");
		$this->testClass->waitForPageToLoad();

		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->testClass->fail("timeout");
			try {
				if ($this->testClass->isElementPresent("id=t3-username")) break;
			} catch (Exception $e) {}
			sleep(1);
		}

		$this->testClass->type("id=t3-username", $username);
		$this->testClass->type("id=t3-password", $password);
		$this->testClass->click("id=t3-login-submit");
		$this->testClass->waitForPageToLoad("30000");
		try {
			$this->testClass->assertTrue($this->testClass->isTextPresent($username));
		} catch (PHPUnit_Framework_AssertionFailedError $e) {
			$this->testClass->fail('No login possible in backend');
		}
	}
	
	protected function checkForLogoutButton() {
		if ($this->testClass->isElementPresent("//div[@id='logout-button']")) {
			$this->testClass->clickAndWait("//div[@id='logout-button']/form/input");
		}
	}

	public function backendLogout() {
		$this->testClass->selectFrame("relative=top");
		$this->testClass->click("//div[@id='logout-button']/form/input");
		$this->testClass->waitForPageToLoad("30000");
		try {
			$this->testClass->assertTrue($this->testClass->isElementPresent("id=t3-username"));
			$this->testClass->assertTrue($this->testClass->isElementPresent("id=t3-password"));
		} catch (PHPUnit_Framework_AssertionFailedError $e) {
			$this->testClass->fail('No logout possible in backend');
		}
	}

}

?>