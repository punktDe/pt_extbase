<?php
namespace PunktDe\PtExtbase\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

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
class PageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Initializes the repository.
     */
    public function initializeObject()
    {
        /** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface */
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $querySettings->setRespectSysLanguage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param $pid
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findPagesInPid($pid)
    {
        $query = $this->createQuery();

        $query->setOrderings(['sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING]);

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
    public function findByPidAndDoktypeOrderBySorting($pid, $doktype)
    {
        $query = $this->createQuery();

        $query->setOrderings(['sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING]);

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
    public function getPageTreeFromRootPageUid($uid, $respectEnableFields = true, $respectDeletedField = true)
    {
        $rootPage = $this->findByUid($uid);

        $uidTreeList = [$uid => ['pageObject' => $rootPage]];

        $uidTreeList[$uid]['subPages'] = $this->getSubpagesOfUid($uid, $respectEnableFields, $respectDeletedField);

        return $uidTreeList;
    }


    /**
     * @param $uid
     * @param array $pageTree
     *
     * @return array
     */
    protected function getSubpagesOfUid($uid, $respectEnableFields, $respectDeletedField, $pageTree = [])
    {
        $this->defaultQuerySettings->setIgnoreEnableFields(!$respectEnableFields);
        $this->defaultQuerySettings->setIncludeDeleted(!$respectDeletedField);

        $pageTree = $this->findPagesInPid($uid);
        $returnArray = [];

        foreach ($pageTree as $page) {
            $returnArray[$page->getUid()]['pageObject'] = $page;
            $returnArray[$page->getUid()]['subPages'] = $this->getSubpagesOfUid($page->getUid(), $respectEnableFields, $respectDeletedField, $pageTree);
        }

        return $returnArray;
    }
}
