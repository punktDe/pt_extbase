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
class Tx_PtExtbase_ViewHelpers_Content_RenderPageViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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

		if (!($GLOBALS['TSFE']->cObj instanceof \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer)) {
			$GLOBALS['TSFE']->cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		}

		$pageUid = $this->arguments['pageUid'];

		$sysLanguageUid = intval($GLOBALS['TSFE']->sys_language_uid);

		$conf = array( // config
			'table' => 'tt_content',
			'select.' => array(
				'pidInList' => $pageUid,
				'where' => 'colPos=0 AND (sys_language_uid=' . $sysLanguageUid . ' OR sys_language_uid=-1)'
			),
		);
		$result = $GLOBALS['TSFE']->cObj->CONTENT($conf);
		return $result;
	}

}
