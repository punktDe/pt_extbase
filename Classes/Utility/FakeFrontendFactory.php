<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Daniel Lienert <lienert@punkt.de>
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
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
* Utility to create a fake frontend
* Used by pt_extlist to use cObj for rendering
*
*
* @package pt_extbase
* @subpackage Utility
* @author Daniel Lienert <daniel@lienert.cc>
*/
class Tx_PtExtbase_Utility_FakeFrontendFactory implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected $fakeFrontend = NULL;


	/**
	 * Create a fake frontend
	 *
	 * @param integer $pageUid
	 * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 * @throws InvalidArgumentException
	 */
	public function createFakeFrontEnd($pageUid = 0) {

		if($this->fakeFrontend && $this->fakeFrontend === $GLOBALS['TSFE']) return $this->fakeFrontend;

		if ($pageUid < 0) {
			throw new InvalidArgumentException('$pageUid must be >= 0.');
		}

		$GLOBALS['TT'] = GeneralUtility::makeInstance('TYPO3\CMS\Core\TimeTracker\NullTimeTracker');

		/** @var $this->fakeFrontend \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
		$this->fakeFrontend = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 0);

		// simulates a normal FE without any logged-in FE or BE user
		$this->fakeFrontend->beUserLogin = FALSE;
		$this->fakeFrontend->workspacePreview = '';
		$this->fakeFrontend->initFEuser();
		$this->fakeFrontend->sys_page = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Page\PageRepository');
		$this->fakeFrontend->page = $pageUid;
		$this->fakeFrontend->initTemplate();
		$this->fakeFrontend->config = array();

		$this->fakeFrontend->tmpl->getFileName_backPath = PATH_site;

		$this->fakeFrontend->newCObj();

		$GLOBALS['TSFE'] = $this->fakeFrontend;

		return $this->fakeFrontend;
	}
}
