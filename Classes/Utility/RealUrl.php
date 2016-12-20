<?php
namespace PunktDe\PtExtbase\Utility;

/***************************************************************
 *  Copyright (C) 2015 punkt.de GmbH
 *  Authors: el_equipo <opiuqe_le@punkt.de>
 *
 *  This script is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use PunktDe\PtExtbase\Utility\RealUrl\UrlDecoder;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Real URL
 *
 * @package PunktDe\PtExtbase\Utility
 */
class RealUrl implements SingletonInterface
{
    /**
     * @inject
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var UrlDecoder
     */
    protected $urlDecoder;

    /**
     * Map path to page ID
     *
     * Path is the URL
     * - without domain
     * - without file suffix
     * - separated by slash
     *
     * E.g.:
     * - URL: http://www.kubrick.co.uk/film/aclockworkorange.html
     * - Path: film/aclockworkorange
     *
     * @param string $path
     * @return integer
     */
    public function mapPathToPageId($path)
    {
        $this->initializeFrontendToMakeRealUrlWork();

        $this->urlDecoder = $this->objectManager->get(UrlDecoder::class);

        $pageId = $this->urlDecoder->decodePathAndReturnPageId($path);

        return $pageId;
    }



    /**
     * @return void
     */
    protected function initializeFrontendToMakeRealUrlWork()
    {
        $GLOBALS['TSFE'] = $this->objectManager->get(TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], 0, 0);
    }



    /**
     * Map URL to page ID
     *
     * URL can be
     * 1. with scheme: http://www.kubrick.co.uk/film/aclockworkorange.html
     * 2. without scheme: www.kubrick.co.uk/film/aclockworkorange.html
     *
     * @param string $url
     * @return integer
     */
    public function mapUrlToPageId($url)
    {
        return $this->mapPathToPageId($this->filterPathFromUrl($url));
    }



    /**
     * @param string $url
     * @return string
     */
    public function filterPathFromUrl($url)
    {
        preg_match('|^(?:[a-z][a-z0-9+\-.]*:(?://[^/?#]+)?)?(?:[^/]+)?([a-z0-9\-._~!$&()*+,;=:@/]*?)\.html?|', $url, $matches);
        return ltrim($matches[1], "/");
    }
}
