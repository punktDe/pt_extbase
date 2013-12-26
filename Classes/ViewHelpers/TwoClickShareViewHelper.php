<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 punkt.de <el_equipo@punkt.de>
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
 * Request Arguments ViewHelper
 *
 * @package pt_extbase
 * @subpackage ViewHelpers
 */
class Tx_PtExtbase_ViewHelpers_TwoClickShareViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_MVC_Request
	 */
	protected $request;

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('options', 'string', 'The options for the javascript call', FALSE);
	}

	/**
	 * Render
	 *
	 * @return string
	 */
	public function render() {
		$shareDiv = '<div id="two-click-share"></div>';

		$javascriptCode = '
jQuery(document).ready(function($){
	if($("#two-click-share").length > 0) {
		if(jQuery.fn.socialSharePrivacy !== undefined) {
			$("#two-click-share").socialSharePrivacy(' . $this->arguments['options'] . ');
		} else {
			$("#two-click-share").html("<div class=\"error\">Please activate socialSharePrivacy() by including the proper js file!</div>");
		}
	}
});';

		$html = $shareDiv . "\n" . '<script type="text/javascript">' . $javascriptCode . '</script>';

		return $html;
	}

}
