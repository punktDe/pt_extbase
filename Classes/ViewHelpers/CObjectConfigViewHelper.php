<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Daniel Lienert <daniel@lienert.cc>
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
 * cObject Array viewHelper
 *
 * @package pt_extbase
 * @subpackage ViewHelpers
 */
class Tx_PtExtbase_ViewHelpers_CObjectConfigViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {

	}


	/**
	 * Renders a cObject form an extbase formatted config
	 *
	 * @param mixed $config
	 * @param array $data
	 * @return string
	 */
	public function render($config, $data = array()) {

		if(!is_array($config)) return $config;

		if($data) {
			Tx_PtExtbase_Div::getCobj()->start($data);
		}

		return Tx_PtExtbase_Div::getCobj()->cObjGetSingle($config['_typoScriptNodeValue'], Tx_Extbase_Utility_TypoScript::convertPlainArrayToTypoScriptArray($config));
	}
	
}
