<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
 *  Authors:
 *    Christian Herberger <herberger@punkt.de>,
 *    Ursula Klinger <klinger@punkt.de>,
 *    Daniel Lienert <lienert@punkt.de>,
 *    Joachim Mathes <mathes@punkt.de>
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
 * View helper to render content of a page
 *
 * @package pt_dppp_base
 * @subpackage ViewHelpers\PageContent
 */
class Tx_PtExtbase_ViewHelpers_Content_RenderPageViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('pageUid', 'integer', 'Page Uid', TRUE);
	}

	/**
	 * Get the rendered content from a page
	 *
	 * @return string The output
	 */
	public function render() {
		$pageUid = $this->arguments['pageUid'];
		$conf = array( // config
			'table' => 'tt_content',
			'select.' => array(
				'pidInList' => $pageUid,
				'where' => 'colPos=0'
			),
		);
		$result = $GLOBALS['TSFE']->cObj->CONTENT($conf);
		return $result;
	}

}

?>