<?php
namespace PunktDe\PtExtbase\Utility\RealUrl;

use DmitryDulepov\Realurl\Decoder\UrlDecoder as RealUrlDecoder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright (C) 2016 punkt.de GmbH
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

class UrlDecoder extends RealUrlDecoder
{

    public function __construct()
    {
        parent::__construct();
        unset($this->siteScript);

        if ($this->tsfe === null) {
            $this->tsfe = $GLOBALS['TSFE'];
        }
        if ($this->emptySegmentValue === null) {
            $this->initialize();
        }
    }

    /**
     * @param string $path
     * @return integer
     */
    public function decodePathAndReturnPageId($path)
    {
        $result = $this->doDecoding($path);

        return $result->getPageId();
    }

}