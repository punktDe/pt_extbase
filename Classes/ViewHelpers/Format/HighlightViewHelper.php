<?php
namespace PunktDe\PtExtbase\ViewHelpers\Format;
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Daniel Lienert <daniel@lienert.cc>
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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper

/**
 * Timestamp ViewHelper
 *
 * @author Daniel Lienert
 *
 * @package pt_extbase
 * @subpackage ViewHelpers\Format
 */
class HighlightViewHelper extends AbstractViewHelper
{
    /**
     * @param string $text
     * @param mixed $highlight variant
     * @return string
     */
    public function render($text, $highlight)
    {
        if (!is_array($highlight)) {
            $highlight = [$highlight];
        }

        $highlightTemplate = '<span class="tx-extbase-highlight">$1</span>';

        foreach ($highlight as $highlightString) {
            $text = preg_replace("|($highlightString)|Ui", $highlightTemplate, $text);
        }
        
        return $text;
    }
}
