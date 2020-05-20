<?php
namespace PunktDe\PtExtbase\Utility;

/***************************************************************
* Copyright notice
*
*   2012 Daniel Lienert <daniel@lienert.cc>
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

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Utility to include defined frontend libraries as jQuery and related CSS
 *
 *
 * @package pt_extbase
 * @subpackage Utility
 * @author Daniel Lienert <daniel@lienert.cc>
 * @author Joachim Mathes <mathes@punkt.de>
 */

class HeaderInclusion implements SingletonInterface
{

    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * @param PageRenderer $pageRenderer
     */
    public function injectPageRenderer(PageRenderer $pageRenderer): void
    {
        $this->pageRenderer = $pageRenderer;
    }


    /**
     * Add JS inline code
     *
     * @param string $name
     * @param string $block
     * @param boolean $compress
     * @param boolean $forceOnTop
     */
    public function addJSInlineCode($name, $block, $compress = true, $forceOnTop = false)
    {
        $this->pageRenderer->addJsInlineCode($name, $block, $compress, $forceOnTop);
    }



    /**
     * Add a CSS file
     *
     * @param $file
     * @param string $rel
     * @param string $media
     * @param string $title
     * @param bool $compress
     * @param bool $forceOnTop
     * @param string $allWrap
     */
    public function addCSSFile($file, $rel = 'stylesheet', $media = 'all', $title = '', $compress = false, $forceOnTop = false, $allWrap = '')
    {
        $this->pageRenderer->addCSSFile($this->getFileRelFileName($file), $rel, $media, $title, $compress, $forceOnTop, $allWrap);
    }



    /**
     * Expand the EXT to a relative path
     *
     * @param string $filename
     * @return string Relative filename
     */
    public function getFileRelFileName($filename)
    {
        if (substr($filename, 0, 4) == 'EXT:') { // extension
            list($extKey, $local) = explode('/', substr($filename, 4), 2);
            $filename = '';
            if (strcmp($extKey, '') && ExtensionManagementUtility::isLoaded($extKey) && strcmp($local, '')) {
                if (TYPO3_MODE === 'FE') {
                    $filename = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath($extKey)) . $local;
                } else {
                    $filename = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath($extKey)) . $local;
                }
            }
        }

        return $filename;
    }
}
