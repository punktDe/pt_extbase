<?php
/***************************************************************
* Copyright notice
*
*   2012 Daniel Lienert <daniel@lienert.cc>
* All rights reserved
*
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Utility to include defined frontend libraries as jQuery and related CSS
 *
 *
 * @package pt_extbase
 * @subpackage Utility
 * @author Daniel Lienert <daniel@lienert.cc>
 * @author Joachim Mathes <mathes@punkt.de>
 */

class Tx_PtExtbase_Utility_HeaderInclusion implements \TYPO3\CMS\Core\SingletonInterface {
	
	/**
	* @var t3lib_PageRenderer
	*/
	protected $pageRenderer;
	

	
	/**
	 * Initialize the object (called by objectManager)
	 * 
	 */
	public function initializeObject() {
		if (TYPO3_MODE === 'BE') {
         	$this->initializeBackend();
         } else {
         	$this->initializeFrontend();
         }
	}



	/**
	 * Initialize Backend specific variables
	 */
	protected function initializeBackend() {

		if (!isset($GLOBALS['SOBE']->doc)) {
			$GLOBALS['SOBE']->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Backend\Template\DocumentTemplate');
			$GLOBALS['SOBE']->doc->backPath = $GLOBALS['BACK_PATH'];
		}

		$this->pageRenderer = $GLOBALS['SOBE']->doc->getPageRenderer();
	}



	/**
	 * Initialize Frontend specific variables
	 */
	protected function initializeFrontend() {
		$GLOBALS['TSFE']->backPath = TYPO3_mainDir;
		$this->pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
	}



	/**
	 * Add JS inline code
	 *
	 * @param string $name
	 * @param string $block
	 * @param boolean $compress
	 * @param boolean $forceOnTop
	 */
	public function addJSInlineCode($name, $block, $compress = TRUE, $forceOnTop = FALSE) {
		$this->pageRenderer->addJsInlineCode($name, $block, $compress, $forceOnTop);
	}



	/**
	 * Add a CSS file
	 *
	 * @param $file
	 * @param string $rel
	 * @param string $media
	 * @param string $title
	 * @param bool $compress
	 * @param bool $forceOnTop
	 * @param string $allWrap
	 */
	public function addCSSFile($file, $rel = 'stylesheet', $media = 'all', $title = '', $compress = FALSE, $forceOnTop = FALSE, $allWrap = '') {
		$this->pageRenderer->addCSSFile($this->getFileRelFileName($file), $rel, $media, $title, $compress, $forceOnTop , $allWrap);
	}



	/**
	 * Expand the EXT to a relative path
	 *
	 * @param string $filename
	 * @return string Relative filename
	 */
	public function getFileRelFileName($filename) {

		if (substr($filename, 0, 4) == 'EXT:') { // extension
			list($extKey, $local) = explode('/', substr($filename, 4), 2);
			$filename = '';
			if (strcmp($extKey, '') && ExtensionManagementUtility::isLoaded($extKey) && strcmp($local, '')) {
				if(TYPO3_MODE === 'FE') {
					$filename = ExtensionManagementUtility::siteRelPath($extKey) . $local;
				} else {
					$filename = ExtensionManagementUtility::extRelPath($extKey) . $local;
				}
			}
		}

		return $filename;
	}

}
