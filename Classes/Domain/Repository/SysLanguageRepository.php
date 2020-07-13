<?php
namespace PunktDe\PtExtbase\Domain\Repository;


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
 * @author Michael Knoll <knoll@punkt.de>
 */
class SysLanguageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Constructor of the repository.
     * Sets the respect storage page to false.
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function __construct(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager)
    {
        parent::__construct($objectManager);
        $this->defaultQuerySettings = new \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings();
        $this->defaultQuerySettings->setRespectStoragePage(false);
        $this->defaultQuerySettings->setRespectSysLanguage(false);
    }
}
