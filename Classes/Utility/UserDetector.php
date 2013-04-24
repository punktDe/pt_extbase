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
 * Class implements detector for fe / be users.
 */
class Tx_PtExtbase_Utility_UserDetector implements t3lib_Singleton {

	/**
	 * Uid of currently logged in user
	 *
	 * @var int
	 */
	protected $userUid = null;



	/**
	 * Holds array of group uids
	 *
	 * @var array
	 */
	protected $groupUids = array();



	/**
	 * Holds instance of fe / be mode detector
	 *
	 * @var Tx_PtExtbase_Utility_FeBeModeDetector
	 */
	protected $feBeModeDetector;



	/**
	 * Injects fe/be mode detector
	 *
	 * @param Tx_PtExtbase_Utility_FeBeModeDetector $feBeModeDetector
	 */
	public function injectFeBeModeDetector(Tx_PtExtbase_Utility_FeBeModeDetector $feBeModeDetector) {
		$this->feBeModeDetector = $feBeModeDetector;
	}



	/**
	 * Initializes object when created by object manager
	 */
	public function initializeObject() {
		if ($this->feBeModeDetector->getMode() == 'BE') {
			$this->userUid = $GLOBALS['BE_USER']->user['uid'];
			$this->groupUids = t3lib_div::trimExplode(',', $GLOBALS['BE_USER']->user['usergroup']);
		} else {
			if($GLOBALS['TSFE']->fe_user->user) {
				$this->userUid = $GLOBALS['TSFE']->fe_user->user['uid'];
				$this->groupUids = t3lib_div::trimExplode(',', trim($GLOBALS['TSFE']->fe_user->user['usergroup']));
			}
		}
	}



	/**
	 * Returns UID of currently logged in user
	 */
	public function getUserUid() {
		return $this->userUid;
	}



	/**
	 * Returns array of UIDs of groups for currently logged in user
	 */
	public function getUserGroupUids() {
		return $this->groupUids;
	}

}
?>