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


/**
 * Class implements testcase for TypoScriptRbacService
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package Tests
 * @subpackage Unit\Rbac
 */
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



	/** @test */
	public function hasAccessReturnsTrueIfAnyUserIsLoggedIn() {
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
						roles {

							admin {
								privileges {
									10 = album.*
									20 = gallery.*
								}
							}
							editor {
								privileges {
									10 = album.create
									11 = album.edit

									20 = gallery.create
									21 = gallery.edit
								}
							}
						}
						beGroups {
							1 {
								10 = admin
							}

							any {
								10 = editor
							}
						}
					}
				}
			}
		';
		$typoScriptArray = $this->getTypoScriptArrayForGivenTypoScriptString($typoScriptConfiguration);

		$typoScriptRbacService = new Tx_PtExtbase_Rbac_TypoScriptRbacService();
		$typoScriptRbacService->injectConfigurationManager($this->getConfigurationManagerMockReturningGivenTypoScriptConfiguration($typoScriptArray));
		$typoScriptRbacService->injectFeBeModeDetector($this->getFeBeModeDetectorMockReturningGivenMode('BE'));

		// Set up rbac service for admin user
		$typoScriptRbacService->injectUserDetector($this->getUserDetectorMockReturningGivenUserUidAndGroupUids(1, array(1)));
		$typoScriptRbacService->initializeObject();

		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'edit'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'delete'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'edit'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'delete'));

		// Set up rbac service for editor user
		$typoScriptRbacService->injectUserDetector($this->getUserDetectorMockReturningGivenUserUidAndGroupUids(1, array(2)));
		$typoScriptRbacService->initializeObject();

		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'edit'));
		$this->assertFalse($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'delete'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'edit'));
		$this->assertFalse($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'delete'));
	}



	/** @test */
	public function hasAccessReturnsExpectedResultsForGivenRbacSettingsInBeMode() {
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
						roles {

							admin {
								privileges {
									10 = album.*
									20 = gallery.*
								}
							}
							editor {
								privileges {
									10 = album.create
									11 = album.edit

									20 = gallery.create
									21 = gallery.edit
								}
							}
						}
						beGroups {
							1 {
								10 = admin
							}

							2 {
								10 = editor
							}
						}
					}
				}
			}
		';
		$typoScriptArray = $this->getTypoScriptArrayForGivenTypoScriptString($typoScriptConfiguration);

		$typoScriptRbacService = new Tx_PtExtbase_Rbac_TypoScriptRbacService();
		$typoScriptRbacService->injectConfigurationManager($this->getConfigurationManagerMockReturningGivenTypoScriptConfiguration($typoScriptArray));
		$typoScriptRbacService->injectFeBeModeDetector($this->getFeBeModeDetectorMockReturningGivenMode('BE'));

		// Set up rbac service for admin user
		$typoScriptRbacService->injectUserDetector($this->getUserDetectorMockReturningGivenUserUidAndGroupUids(1, array(1)));
		$typoScriptRbacService->initializeObject();

		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'edit'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'delete'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'edit'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'delete'));

		// Set up rbac service for editor user
		$typoScriptRbacService->injectUserDetector($this->getUserDetectorMockReturningGivenUserUidAndGroupUids(1, array(2)));
		$typoScriptRbacService->initializeObject();

		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'edit'));
		$this->assertFalse($typoScriptRbacService->loggedInUserHasAccess('yag', 'album', 'delete'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'create'));
		$this->assertTrue($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'edit'));
		$this->assertFalse($typoScriptRbacService->loggedInUserHasAccess('yag', 'gallery', 'delete'));
	}



	/** @test */
	public function initializeObjectThrowsExceptionIfRoleHasNoPrivilegesSet() {
		$typoScriptConfiguration = '
			plugin.tx_ptextbase.settings.rbac {
				extensions {
					yag {
						roles {
							admin {
									10 = album.*
									20 = gallery.*
							}
						}
						beGroups {
							## Exception should be thrown, as we forgot to set key "privileges" right here!
							1 {
								10 = admin
							}
						}
					}
				}
			}
		';

		try {
			$this->runInitializeObjectWithGivenTypoScriptConfiguration($typoScriptConfiguration);
		} catch(Exception $e) {
			$this->assertTrue(TRUE);
			return;
		}
		$this->fail('No Exception was thrown.');
	}



	/** @test */
	public function initializeObjectThrowsExceptionIfRoleAssignedToGroupIsNotSetUp() {
		$typoScriptConfiguration = '
			plugin.tx_ptextbase.settings.rbac {
				extensions {
					yag {
						roles {
							admin {
								privileges {
									10 = album.*
									20 = gallery.*
								}
							}
						}
						beGroups {
							1 {
								## Exception should be thrown, as we did not set up this role!
								10 = baggerfahrer
							}
						}
					}
				}
			}
		';

		try {
			$this->runInitializeObjectWithGivenTypoScriptConfiguration($typoScriptConfiguration);
		} catch(Exception $e) {
			$this->assertTrue(TRUE);
			return;
		}
		$this->fail('No Exception was thrown.');
	}



	/** @test */
	public function initializeObjectThrowsExceptionIfPrivilegeUsesObjectThatIsNotConfigured() {
		$typoScriptConfiguration = '
			plugin.tx_ptextbase.settings.rbac {
				extensions {
					yag {
						objects {
							album {
								actions {
									10 = create
									20 = delete
								}
							}
						}
						roles {
							admin {
								privileges {
									## Exception should be thrown, as we do not have this object set up above!!
									10 = bagger.*
									20 = gallery.*
								}
							}
						}
						beGroups {
							1 {
								10 = admin
							}
						}
					}
				}
			}
		';

		try {
			$this->runInitializeObjectWithGivenTypoScriptConfiguration($typoScriptConfiguration);
		} catch(Exception $e) {
			$this->assertTrue(TRUE);
			return;
		}
		$this->fail('No Exception was thrown.');
	}



	/** @test */
	public function initializeObjectThrowsExceptionIfNoSectionObjectsIsSetInTsConfiguration() {

		$typoScriptConfiguration = '
			plugin.tx_ptextbase.settings.rbac {
				extensions {
					## Exception should be thrown, as we do not have section "objects" here!!


					yag {
						roles {
							admin {
								privileges {
									10 = album.*
									20 = gallery.*
								}
							}
						}
						beGroups {
							1 {
								10 = admin
							}
						}
					}
				}
			}
		';

		try {
			$this->runInitializeObjectWithGivenTypoScriptConfiguration($typoScriptConfiguration);
		} catch(Exception $e) {
			$this->assertTrue(TRUE);
			return;
		}
		$this->fail('No Exception was thrown.');
	}



	/** @test */
	public function initializeObjectThrowsExceptionIfAnActionIsUsedInPrivilegeThatIsNotSetUpInObjects() {

		$typoScriptConfiguration = '
			plugin.tx_ptextbase.settings.rbac {
				extensions {
					yag {
						objects {
							album {
								actions {
									10 = create
								}
							}
						}
						roles {
							admin {
								privileges {
									## Exception should be thrown because we do not have "furz" action set up for object album!!
									10 = album.furz
								}
							}
						}
						beGroups {
							1 {
								10 = admin
							}
						}
					}
				}
			}
		';

		try {
			$this->runInitializeObjectWithGivenTypoScriptConfiguration($typoScriptConfiguration);
		} catch(Exception $e) {
			$this->assertTrue(TRUE);
			return;
		}
		$this->fail('No Exception was thrown.');
	}



	/** @test */
	public function initializeObjectThrowsExceptionIfWeUseObjectInPrivilegeThatIsNotConfiguredInObjectsSection() {

		$typoScriptConfiguration = '
			plugin.tx_ptextbase.settings.rbac {
				extensions {
					yag {
						objects {
							album {
								actions {
									10 = create
								}
							}
						}
						roles {
							admin {
								privileges {
									## Exception should be thrown because we do not have "hirn" object set up in objects!!
									10 = hirn.egal
								}
							}
						}
						beGroups {
							1 {
								10 = admin
							}
						}
					}
				}
			}
		';

		try {
			$this->runInitializeObjectWithGivenTypoScriptConfiguration($typoScriptConfiguration);
		} catch(Exception $e) {
			$this->assertTrue(TRUE);
			return;
		}
		$this->fail('No Exception was thrown.');
	}



	/************************************************************************************************
	 * Helper methods
	 ************************************************************************************************/

	protected function runInitializeObjectWithGivenTypoScriptConfiguration($typoScriptConfiguration) {
		$typoScriptArray = $this->getTypoScriptArrayForGivenTypoScriptString($typoScriptConfiguration);
		$typoScriptRbacService = new Tx_PtExtbase_Rbac_TypoScriptRbacService();
		$typoScriptRbacService->injectConfigurationManager($this->getConfigurationManagerMockReturningGivenTypoScriptConfiguration($typoScriptArray));
		$typoScriptRbacService->injectFeBeModeDetector($this->getFeBeModeDetectorMockReturningGivenMode('BE'));

		// Set up rbac service for admin user
		$typoScriptRbacService->injectUserDetector($this->getUserDetectorMockReturningGivenUserUidAndGroupUids(1, array(1)));
		$typoScriptRbacService->initializeObject();
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
		return Tx_PtExtbase_Compatibility_Extbase_Service_TypoScript::convertTypoScriptArrayToPlainArray($typoScriptParser->setup);
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
		$feBeModeDetectorMock = $this->getMock('Tx_PtExtbase_Utility_FeBeModeDetector', array('getMode'), array(), '', FALSE);
		$feBeModeDetectorMock->expects($this->any())->method('getMode')->will($this->returnValue($mode));
		return $feBeModeDetectorMock;
	}



	/**
	 * Returns user detector mock that will return given userUid and given userGroupUids on getUserUid() and getUserGroupUids()
	 *
	 * @param $userUid
	 * @param array $groupUids
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getUserDetectorMockReturningGivenUserUidAndGroupUids($userUid, $groupUids = array()) {
		$userDetectorMock = $this->getMock('Tx_PtExtbase_Utility_UserDetector', array('getUserUid', 'getUserGroupUids'), array(), '', FALSE);
		$userDetectorMock->expects($this->any())->method('getUserUid')->will($this->returnValue($userUid));
		$userDetectorMock->expects($this->any())->method('getUserGroupUids')->will($this->returnValue($groupUids));
		return $userDetectorMock;
	}

}
?>