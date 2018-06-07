<?php
namespace PunktDe\PtExtbase\ViewHelpers\Format;
/***************************************************************
* Copyright notice
*
*   2011 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @package ViewHelpers
 */
class FileSizeViewHelper extends AbstractViewHelper
{
    /**
     * @param string $labels Labels in format  "B| KB| MB| GB"
     * @param string $useIecLabels
     * @return string The formated filesize
     */
    public function render($labels = '', $useIecLabels = false)
    {
        $numberToFormat = (int) trim($this->renderChildren());
        $value = GeneralUtility::formatSize($numberToFormat, $labels);
        if (!$useIecLabels && substr($value, -1) === 'i') {
            $value = substr($value, 0, -1);
        }
        return $value;
    }
}
