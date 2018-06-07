<?php
namespace PunktDe\PtExtbase\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 punkt.de <el_equipo@punkt.de>
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

use ApacheSolrForTypo3\Solr\ViewHelpers\Backend\AbstractSolrTagBasedViewHelper;

/**
 * Request Arguments ViewHelper
 *
 * @package pt_extbase
 * @subpackage ViewHelpers
 */
class ShortLinkViewHelper extends AbstractSolrTagBasedViewHelper
{
    /**
     * @param integer $length
     * @param string $indexScriptUrl
     *
     * @return string
     */
    public function render($length = 0, $indexScriptUrl = '')
    {
        $link = $this->renderChildren();

        $shortLink = \TYPO3\CMS\Core\Utility\GeneralUtility::makeRedirectUrl($link, $length, $indexScriptUrl);

        return $shortLink;
    }
}
