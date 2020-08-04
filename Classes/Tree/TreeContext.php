<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Core\SingletonInterface;

class TreeContext implements SingletonInterface
{
    /**
     * @var $bool
     */
    protected $writable = false;

    /**
     * @var bool
     */
    protected $includeDeleted = false;

    /**
     * @return void
     */
    public function initializeObject()
    {
        $this->resetToDefault();
    }



    /**
     * @return void
     */
    public function resetToDefault()
    {
        if (TYPO3_MODE === 'BE' || $GLOBALS['TYPO3_AJAX']) {
            $this->writable = true;
        }
    }



    /**
     * @param  $writable
     */
    public function setWritable($writable)
    {
        $this->writable = $writable;
    }



    /**
     * @return boolean
     */
    public function isWritable()
    {
        return $this->writable;
    }


    /**
     * @return bool
     */
    public function isIncludeDeleted()
    {
        return $this->includeDeleted;
    }


    /**
     * @param bool $includeDeleted
     */
    public function setIncludeDeleted($includeDeleted)
    {
        $this->includeDeleted = $includeDeleted;
    }


    /**
     * @return bool
     */
    public function respectEnableFields()
    {
        return !$this->isWritable();
    }



    /**
     * @param boolean $respectEnableFields
     */
    public function setRespectEnableFields($respectEnableFields)
    {
        $this->writable = !$respectEnableFields;
    }
}
