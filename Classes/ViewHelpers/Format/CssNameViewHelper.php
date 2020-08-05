<?php
namespace PunktDe\PtExtbase\ViewHelpers\Format;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Joachim Mathes <mathes@punkt.de>
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
 * StringToLower ViewHelper
 *
 * @package pt_extbase
 * @subpackage ViewHelpers\Format
 */
class CssNameViewHelper extends AbstractViewHelper
{
    /**
     * Render
     * 
     * Renders a valid CSS class/id name
     *
     * @return string
     */
    public function render()
    {
        $name = $this->renderChildren();

        $name = trim($name);
        $name = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($name);
        $name = strtolower($name);
        $name = str_replace([' ', '_'], '-', $name);

        return $name;
    }
}
