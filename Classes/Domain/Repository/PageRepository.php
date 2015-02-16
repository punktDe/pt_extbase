<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2012 Daniel Lienert <daniel@lienert.cc>
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
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Repository for Pages
 *
 * @package Domain
 * @subpackage Repository
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Domain_Repository_PageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {


	/**
	 * Constructor of the repository.
	 * Sets the respect storage page to false.
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 */
	public function __construct() {
		 parent::__construct(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager'));

		 $this->defaultQuerySettings = new \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings();
		 $this->defaultQuerySettings->setRespectStoragePage(FALSE);
		 $this->defaultQuerySettings->setRespectSysLanguage(FALSE);
	}


	/**
	 * @param $pid
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findPagesInPid($pid) {
		$query = $this->createQuery();

		$query->setOrderings(array('sorting' => QueryInterface::ORDER_ASCENDING));

		$pages = $query->matching(
			$query->equals('pid', $pid)
		)
		->execute();
		return $pages;
	}


	/**
	 * @param $pid
	 * @param $doktype
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByPidAndDoktypeOrderBySorting($pid, $doktype) {
		$query = $this->createQuery();

		$query->setOrderings(array('sorting' => QueryInterface::ORDER_ASCENDING));

		$pages = $query->matching(
			$query->logicalAnd(
				$query->equals('pid', $pid),
				$query->equals('doktype', $doktype)
			)
		)->execute();
		return $pages;
	}


	/**
	 * @param $uid
	 * @return array
	 */
	public function getPageTreeFromRootPageUid($uid, $respectEnableFields = TRUE, $respectDeletedField = TRUE) {
		$rootPage = $this->findByUid($uid);

		$uidTreeList = array($uid => array('pageObject' => $rootPage));

		$uidTreeList[$uid]['subPages'] = $this->getSubpagesOfUid($uid, $respectEnableFields, $respectDeletedField);

		return $uidTreeList;
	}


	/**
	 * @param $uid
	 * @param array $pageTree
	 *
	 * @return array
	 */
	protected function getSubpagesOfUid($uid, $respectEnableFields, $respectDeletedField, $pageTree = array()) {
		$this->defaultQuerySettings->setIgnoreEnableFields(!$respectEnableFields);
		$this->defaultQuerySettings->setIncludeDeleted(!$respectDeletedField);

		$pageTree = $this->findPagesInPid($uid);
		$returnArray = array();

		foreach ($pageTree as $page) {
			$returnArray[$page->getUid()]['pageObject'] = $page;
			$returnArray[$page->getUid()]['subPages'] = $this->getSubpagesOfUid($page->getUid(), $respectEnableFields, $respectDeletedField, $pageTree);
		}

		return $returnArray;
	}
}