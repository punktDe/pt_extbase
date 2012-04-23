<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Michael Knoll <mimi@kaktusteam.de>
*  All rights reserved
*
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
 * Class implements an access viewhelper for RBAC
 * 
 * = Examples =
 *
 * <code title="Basic usage">
 * <rbac:hasAccess object="rbac_object_name" action="rbac_action_name">
 *   This is being shown in case user has access to action on object
 * </rbac:hasAccess>
 * </code>
 *
 * Everything inside the <rbac:access> tag is being displayed if the frontend user has access to action on object.
 * If no user is given, the currently logged in fe user will be used. 
 *
 * <code title="hasAccess / access / noAccess">
 * <rbac:hasAccess object="rbac_object_name" action="rbac_action_name">
 *   <f:then>
 *     This is being shown in case the user has access.
 *   </f:then>
 *   <f:else>
 *     This is being displayed in case the user has NO access.
 *   </f:else>
 * </rbac:hasAccess>
 * </code>
 *
 * Everything inside the "access" tag is displayed if the user has access to action on object.
 * Otherwise, everything inside the "noAccess"-tag is displayed.
 *
 * <code title="inline notation">
 * {rbac:hasAccess(object: 'objectName', action: 'actionName' then: 'user has access', else: 'access is denied')}
 * </code>
 *
 * The value of the "then" attribute is displayed if access is granted for user on object and action.
 * Otherwise, the value of the "else"-attribute is displayed.
 *
 * @package ViewHelpers
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_ViewHelpers_Rbac_HasAccessViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * Holds instance of rbac service
	 *
	 * @var Tx_PtExtbase_Rbac_RbacServiceInterface
	 */
	protected $rbacService;



	/**
	 * Injects rbac service
	 *
	 * @param Tx_PtExtbase_Rbac_RbacServiceInterface $rbacService
	 */
	public function injectRbacService(Tx_PtExtbase_Rbac_RbacServiceInterface $rbacService) {
		$this->rbacService = $rbacService;
	}



	/**
	 * Initialize arguments
	 */
    public function initializeArguments() {
        $this->registerArgument('object', 'string', 'Object to check if user has access rights for', TRUE);
        $this->registerArgument('action', 'string', 'Action to check if user has access rights for', TRUE);
    }

    
    
	/**
	 * Renders hasAccess viewhelper
	 * 
	 * @return string Rendered hasAccess ViewHelper
	 */
	public function render() {
		if ($this->rbacService->loggedInUserHasAccess(
				$this->controllerContext->getRequest()->getControllerExtensionName(),
				$this->arguments['object'],
				$this->arguments['action']
			)
		) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
?>