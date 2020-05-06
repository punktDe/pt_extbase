<?php
namespace PunktDe\PtExtbase;

/***************************************************************
* Copyright notice
*
*   2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
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

/**
* Interface for Context object (to configure used object via typoscrit)
* 
* @author Daniel Lienert
*/

interface ContextInterface extends \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * Defines if the extension act as it is in cached mode
     * @return bool
     */
    public function isInCachedMode();
    
    
    
    /**
     * Set the cached mode for the complete extension.
     * This is autmatically set when extlsit is used as standalone cached extension
     * 
     * @param bool $inCachedMode
     */
    public function setInCachedMode($inCachedMode);

    
    
    /**
     * @return \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext
     */
    public function getControllerContext();
    
    
    
    /**
     * @return string
     */
    public function getExtensionName();
    
    
    
    /**
     * @return string
     */
    public function getExtensionNamespace();
}
