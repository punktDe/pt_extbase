<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Michael Knoll <knoll@punkt.de>, punkt.de GmbH
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

class Tx_PtExtbase_Tests_Unit_Rbac_TypoScriptRbacServiceTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/** @test */
	public function configurationManagerCanBeInjected() {
		$configurationManagerMock = $this->getMock('Tx_Extbase_Configuration_ConfigurationManagerInterface', array(), array(), '', FALSE);
		$tsRbacService = new Tx_PtExtbase_Rbac_TypoScriptRbacService();
		$tsRbacService->injectConfigurationManager($configurationManagerMock);
	}



	/** @test */
	public function hasAccessReturnsTrueIfAllPrivilegesForExtensionAreGranted() {
		$typoScriptConfiguration = '
		plugin.tx_ptextbase.settings.rbac {
		 	extensions {
		 		yag {
		 			objects {
		 				album {
		 					actions {
		 						10 = create
		 						20 = delete
		 						30 = edit
		 					}
		 				}
		 				gallery {
		 					actions {
		 						10 = create
		 						20 = delete
		 						30 = edit
		 					}
		 				}
		 			}
		 			beGroups {
		 				__grantAllPrivileges = 1
		 			}
		 		}
		 	}
		}
		';
		$typoScriptArray = $this->getTypoScriptArrayForGivenTypoScriptString($typoScriptConfiguration);

		$typoScriptRbacService = new Tx_PtExtbase_Rbac_TypoScriptRbacService();
		$typoScriptRbacService->injectConfigurationManager($this->getConfigurationManagerMockReturningGivenTypoScriptConfiguration($typoScriptArray));
		$typoScriptRbacService->injectFeBeModeDetector($this->getFeBeModeDetectorMockReturningGivenMode('BE'));
		$typoScriptRbacService->initializeObject();

		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'delete'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'edit'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'delete'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'edit'));
	}



	/**
	 * Returns parsed TypoScript array for given TypoScript string
	 *
	 * @param $typoScriptString
	 * @return string
	 */
	protected function getTypoScriptArrayForGivenTypoScriptString($typoScriptString) {
		$typoScriptParser = t3lib_div::makeInstance('t3lib_TSparser'); /* @var $typoScriptParser t3lib_TSparser */
		$typoScriptParser->parse($typoScriptString);
		return Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($typoScriptParser->setup);
	}



	/**
	 * Returns configuration manager mock that will return given configuration for getConfiguration()
	 *
	 * @param $tsConfiguration
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getConfigurationManagerMockReturningGivenTypoScriptConfiguration($tsConfiguration) {
		$configurationManagerMock = $this->getMock('Tx_Extbase_Configuration_ConfigurationManager', array('getConfiguration'), array(), '', FALSE);
		$configurationManagerMock->expects($this->any())->method('getConfiguration')->will($this->returnValue($tsConfiguration));
		return $configurationManagerMock;
	}



	/**
	 * Returns a fe / be mode detector returning given mode on getMode()
	 *
	 * @param $mode
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getFeBeModeDetectorMockReturningGivenMode($mode) {
		$feBeModeDetectorMock = $this->getMock('Tx_PtExtbase_Rbac_FeBeModeDetector', array('getMode'), array(), '', FALSE);
		$feBeModeDetectorMock->expects($this->any())->method('getMode')->will($this->returnValue($mode));
		return $feBeModeDetectorMock;
	}

}
?>