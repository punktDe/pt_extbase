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


use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class implements read only access to tt_pages table
 *
 * @package Domain
 * @subpackage Model
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Page extends AbstractEntity
{
    /**
     * @var string the module key
     */
    protected $module;

    /**
     * @var string page title
     */
    protected $title;

    /**
     * @var integer
     */
    protected $sorting;

    /**
     * @var integer
     */
    protected $doktype;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }
    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
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

    /**
     * @param integer $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return integer
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param integer $doktype
     */
    public function setDoktype($doktype)
    {
        $this->doktype = $doktype;
    }

    /**
     * @return integer
     */
    public function getDoktype()
    {
        return $this->doktype;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Page
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }
}
