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
 * Interface for RBAC access control services
 */
interface Tx_PtExtbase_Rbac_RbacServiceInterface extends t3lib_Singleton {

	/**
	 * Returns TRUE, if currently logged in user (frontend or backend) has
	 * access for given object and action.
	 *
	 * @param string $extension Name of extension to grant access
	 * @param string $object Object to grant access to
	 * @param string $action Action to grant access to
	 * @return bool TRUE, if access is granted
	 */
	public function loggedInUserHasAccess($extension, $object, $action);

}
?>
