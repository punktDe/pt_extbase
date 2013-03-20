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
 * Class implements RBAC service based on TypoScript.
 *
 * This class is implementing a singleton by implementing rbacServiceInterface.
 *
 * RBAC settings have to be configured in TypoScript within key plugin.tx_ptextbase.settings.rbac
 *
 * See following example:
 *
plugin.tx_ptextbase.settings.rbac {

 	## We should be able to set up rbac privileges for
 	## multiple extensions, hence we need namespace for each extension here
 	extensions {

 		yag {

 			## We define objects and corresponding actions since we later
 			## want to define rules like
 			## "role A is granted all privileges on object B" via B.*
 			## which we can only do by defining objects and actions first,
 			## what might seem to be redundant on first sight.
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


 				item {
 					actions {
 						10 = create
 						20 = delete
 						30 = edit
 					}
 				}

 			}



 			## Roles can combine privileges on arbitrary objects with arbitrary actions.
 			## Use "*" as wildcard for all actions which are defined on an object.
 			roles {

 				admin {
 					privileges {
 						10 = album.*
 						20 = gallery.*
 						30 = item.*
 					}
 				}


 				editor {
 					privileges {
 						10 = album.create
 						11 = album.edit

 						20 = gallery.create
 						21 = gallery.edit

 						30 = item.create
 						31 = item.edit
 					}
 				}


 				albumManager {
 					privileges {
 						10 = album.*
 					}
 				}


 				galleryManager {
 					privileges {
 						10 = gallery.*
 					}
 				}


 				itemManager {
 					privileges {
 						10 = item.*
 					}
 				}

 			}



 			## RBAC service can be used in frontend and in backend environment,
 			## but we have different UIDs for user groups so we need to define
 			## privileges for frontend and backend separately
 			feGroups {

 				1 {
 					10 = admin
 				}

 				3 {
 					10 = editor
 				}

 				4 {
 					10 = albumManager
 				}

 				## Any logged in user has this role
 				any {
					10 = editor
 				} 

 			}



 			beGroups {

 				## Use this, if backend users should be able to do everything
 				__grantAllPrivileges = 1

 			}

 		}

 	}

}

 */
class Tx_PtExtbase_Rbac_TypoScriptRbacService implements Tx_PtExtbase_Rbac_RbacServiceInterface {

	/**
	 * Holds array of TypoScript rbac settings
	 *
	 * @var array
	 */
	protected $typoScriptRbacSettings;



	/**
	 * Holds instance of configuration manager
	 *
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;



	/**
	 * Holds FE / BE mode detector
	 *
	 * @var Tx_PtExtbase_Utility_FeBeModeDetector
	 */
	protected $feBeModeDetector;



	/**
	 * Holds user detector
	 *
	 * @var Tx_PtExtbase_Utility_UserDetector
	 */
	protected $userDetector;



	/**
	 * Holds an array, that represents rbac privileges for each group.
	 *
	 * Array has the form
	 *
	 * array (
	 * 		$extension => array (
	 * 			$groupUid1 => array (
	 *	 			$object1 => array($right1, $right2, ... $rightxy),
	 * 				$object2 => array($rightyz ...)
	 * 			)
	 * 		),
	 * 		...
	 * )
	 *
	 * @var array
	 */
	protected $groupsToObjectAndActionsArray;



	/**
	 * Array of extensions to grant all privileges for
	 *
	 * @var array
	 */
	protected $extensionsToGrantAllPrivileges = array();



	/**
	 * Helper for initializing rbac settings
	 *
	 * @var string
	 */
	private $_currentExtensionName;



	/**
	 * Helper for initializing rbac settings
	 *
	 * @var string
	 */
	private $_currentGroupUid;



	/**
	 * Injects configuration manager from which we get TS configuration
	 *
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}



	/**
	 * Injects fe/be mode detector
	 *
	 * @param Tx_PtExtbase_Utility_FeBeModeDetector $feBeModeDetector
	 */
	public function injectFeBeModeDetector(Tx_PtExtbase_Utility_FeBeModeDetector $feBeModeDetector) {
		$this->feBeModeDetector = $feBeModeDetector;
	}



	/**
	 * Injects user detector
	 *
	 * @param Tx_PtExtbase_Utility_UserDetector $userDetector
	 */
	public function injectUserDetector(Tx_PtExtbase_Utility_UserDetector $userDetector) {
		$this->userDetector = $userDetector;
	}



	/**
	 * Initializes TS rbac service (invoked from objectManager)
	 */
	public function initializeObject() {
		$fullTypoScript = Tx_PtExtbase_Compatibility_Extbase_Service_TypoScript::convertTypoScriptArrayToPlainArray($this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT));
		$this->typoScriptRbacSettings = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($fullTypoScript, 'plugin.tx_ptextbase.settings.rbac');
		$this->initGroupsToObjectAndActionsArray();
	}



	/**
	 * Returns TRUE, if currently logged in user (frontend or backend) has
	 * access for given object and action.
	 *
	 * @param string $extension Extension to grant access to
	 * @param string $object Object to grant access to
	 * @param string $action Action to grant access to
	 * @return bool
	 */
	public function loggedInUserHasAccess($extension, $object, $action) {

		$userHasPrivileges = FALSE;
		// Check, whether we grant all privileges for this extension
		if (in_array($extension, $this->extensionsToGrantAllPrivileges)) {
			$userHasPrivileges = TRUE;
		} else {
			$userGroups = $this->userDetector->getUserGroupUids();

			if(count($userGroups) > 0) {
				$userGroups[] = 'any';
			}

			foreach ($userGroups as $userGroup) {
				if (is_array($this->groupsToObjectAndActionsArray[strtolower($extension)][$userGroup][$object]) && 
					in_array($action, $this->groupsToObjectAndActionsArray[strtolower($extension)][$userGroup][$object])) {
					$userHasPrivileges = TRUE;
				}
			}
		}
		return $userHasPrivileges;
	}



	/**
	 * Initializes local privileges array from TypoScript settings
	 */
	protected function initGroupsToObjectAndActionsArray() {
		if(is_array($this->typoScriptRbacSettings['extensions'])) {
			foreach($this->typoScriptRbacSettings['extensions'] as $extensionName => $extensionRbacSettings) {
				$this->_currentExtensionName = strtolower($extensionName);
				$this->initRbacSettingsForGivenExtensionSettings($extensionRbacSettings);
			}
		}
	}



	protected function initRbacSettingsForGivenExtensionSettings($extensionRbacSettings) {
		if ($this->feBeModeDetector->getMode() == 'BE') {
			// we are in backend mode, so we use beGroups settings
			$this->initRolePrivilegesForGivenRolesAndRbacSettings($extensionRbacSettings['beGroups'], $extensionRbacSettings);
		} else {
			// we are in frontend mode, so we use feGroups settings
			$this->initRolePrivilegesForGivenRolesAndRbacSettings($extensionRbacSettings['feGroups'], $extensionRbacSettings);
		}
	}



	protected function initRolePrivilegesForGivenRolesAndRbacSettings($groupUids, $rbacSettings) {
		if ($groupUids['__grantAllPrivileges'] == 1) {
			// we have __grantAllPrivileges = 1 set in TypoScript - so nothing to do here anymore
			$this->extensionsToGrantAllPrivileges[] = $this->_currentExtensionName;
		} else {
			foreach ($groupUids as $groupUid => $groupUidPrivileges) {
				$this->_currentGroupUid = $groupUid;
				$this->initRolePrivilegesForCurrentGroupAndRbacSettings($groupUidPrivileges, $rbacSettings);
			}
		}
	}



	protected function initRolePrivilegesForCurrentGroupAndRbacSettings($groupUidPrivileges, $rbacSettings) {
		foreach($groupUidPrivileges as $key => $assignedRole) {
			$rolePrivileges = $this->getRolePrivilegesByRole($assignedRole, $rbacSettings);
			foreach($rolePrivileges as $key => $privilegeIdentifier) {
				// $privilegeIdentifier can either be "object.action" or "object.*"
				$objectActionPrivileges = $this->getObjectActionPrivilegesByPrivilegeIdentifier($privilegeIdentifier, $rbacSettings);
				/**
				 *
				 * $objectActionPrivileges = array(
				 * 		$object1 => array(
				 * 			10 => $action1,
				 * 			20 => $action2,
				 * 			...
				 * 			$actionxy
				 * 		),
				 * 		...
				 * )
				 *
				 */
				foreach ($objectActionPrivileges as $object => $actions) {
					foreach ($actions as $key => $action) {
						$this->addObjectActionPrivilegeForCurrentGroupAndCurrentExtension($object, $action);
					}
				}
			}
		}
	}



	protected function getRolePrivilegesByRole($role, $rbacSettings) {
		$roles = $rbacSettings['roles'];
		$privileges = array();
		if (array_key_exists($role, $roles)) {
			if (array_key_exists('privileges', $rbacSettings['roles'][$role])) {
				foreach($rbacSettings['roles'][$role]['privileges'] as $key => $privilege) {
					$privileges[] = $privilege;
				}
			} else {
				throw new Exception('No key "privileges" can be found for role ' . $role . ' in rbac configuration for extension ' . $this->_currentExtensionName . ' 1334831365');
			}
		} else {
			throw new Exception('Role ' . $role . ' is not configured in rbac configuration for extension ' . $this->_currentExtensionName . ' 1334831364');
		}
		return $privileges;
	}



	protected function getObjectActionPrivilegesByPrivilegeIdentifier($privilegeIdentifier, $rbacSettings) {
		if (array_key_exists('objects', $rbacSettings)) {
			list($object, $action) = explode('.', $privilegeIdentifier);
			if ($action == '*') {
				return array($object => $this->getAllActionsForObject($object, $rbacSettings));
			} else {
				return array($object => $this->getActionForObject($action, $object, $rbacSettings));
			}
		} else {
			throw new Exception('You have no section "objects" within rbac settings for extension ' . $this->_currentExtensionName . ' 1334831366');
		}
	}



	protected function getAllActionsForObject($object, $rbacSettings) {
		$actions = array();
		$objectsSettings = $rbacSettings['objects'];

		if (array_key_exists($object, $objectsSettings)) {
			foreach($objectsSettings[$object]['actions'] as $key => $action) {
				$actions[] = $action;
			}
		} else {
			throw new Exception('You have no settings for object ' . $object . ' within your rbac settings ' . ' 1334831367');
		}

		return $actions;
	}



	protected function getActionForObject($action, $object, $rbacSettings) {
		if (array_key_exists($object, $rbacSettings['objects'])) {
			foreach ($rbacSettings['objects'][$object]['actions'] as $key => $configuredAction) {
				if ($configuredAction == $action) {
					return array($action);
				}
			}
			// If we get here, we have a action set up in a privilege rule which is not configured within object section --> Exception
			throw new Exception('You have used an action ' . $action . ' within your privileges on object ' . $object . ' for which you do not have set up an action in your objects configuration! 1334831369');
		} else {
			throw new Exception('You have no object configuration for object ' . $object . ' within objects section of your rbac configuration! 1334831368');
		}
	}



	protected function addObjectActionPrivilegeForCurrentGroupAndCurrentExtension($object, $action) {
		$this->groupsToObjectAndActionsArray[$this->_currentExtensionName][$this->_currentGroupUid][$object][] = $action;
	}

}
?>