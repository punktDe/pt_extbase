<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Daniel Lienert <daniel@lienert.cc>
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
 * Define the Tree Context
 *
 * @package Tree
 * @author Daniel Lienert
 */
class Tx_PtExtbase_Tree_TreeContext implements t3lib_Singleton {

	/**
	 * @var $bool
	 */
	protected $writable = FALSE;



	public function initializeObject() {
		if(TYPO3_MODE === 'BE' || TYPO3_AJAX) {
			$this->writable = TRUE;
		}
	}



	/**
	 * @param  $writable
	 */
	public function setWritable($writable) {
		$this->writable = $writable;
	}



	/**
	 * @return boolean
	 */
	public function isWritable() {
		return $this->writable;
	}



	/**
	 * @return bool
	 */
	public function respectEnableFields() {
		return !$this->isWritable();
	}

}
?>