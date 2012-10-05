<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2012 Daniel Lienert <daniel@lienert.cc>
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
 * Class implements read only access to tt_pages table
 *
 * @package Domain
 * @subpackage Model
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Domain_Model_Page extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * @var string the module key
	 */
	protected $module;


	/**
	 * @param string $module
	 */
	public function setModule($module) {
		$this->module = $module;
	}

	/**
	 * @return string
	 */
	public function getModule() {
		return $this->module;
	}
}
?>