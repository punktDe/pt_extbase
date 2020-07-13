<?php
namespace PunktDe\PtExtbase\Domain\Model;

/***************************************************************
*  Copyright notice
*
*  (c) 2010-2012 Daniel Lienert <daniel@lienert.cc>
*  			
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
 * Class implements READ ONLY access to sys_language
 *
 * @package Domain
 * @subpackage Model
 * @author Michael Knoll <knoll@punkt.de>
 */
class SysLanguage extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $title;



    /**
     * @var string
     */
    protected $flag;



    /**
     * @var boolean
     */
    protected $indexEnable;



    /**
     * @param string $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }



    /**
     * @return string
     */
    public function getFlag()
    {
        return $this->flag;
    }



    /**
     * @param boolean $indexEnable
     */
    public function setIndexEnable($indexEnable)
    {
        $this->indexEnable = $indexEnable;
    }



    /**
     * @return boolean
     */
    public function getIndexEnable()
    {
        return $this->indexEnable;
    }



    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }



    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
