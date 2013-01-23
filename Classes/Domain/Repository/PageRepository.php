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

/**
 * Repository for Pages
 *
 * @package Domain
 * @subpackage Repository
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Domain_Repository_PageRepository extends Tx_Extbase_Persistence_Repository {


	/**
	 * Constructor of the repository.
	 * Sets the respect storage page to false.
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 */
	public function __construct(Tx_Extbase_Object_ObjectManagerInterface $objectManager = NULL) {
		 parent::__construct($objectManager);
		 $this->defaultQuerySettings = new Tx_Extbase_Persistence_Typo3QuerySettings();
		 $this->defaultQuerySettings->setRespectStoragePage(FALSE);
		 $this->defaultQuerySettings->setRespectSysLanguage(FALSE);
	}


	/**
	 * @param $pid
	 * @return array|Tx_Extbase_Persistence_QueryResultInterface
	 */
	public function findPagesInPid($pid) {
		$query = $this->createQuery();
		$pages = $query->matching(
			$query->equals('pid', $pid)
		)
		->execute();
		return $pages;
	}
}
?>