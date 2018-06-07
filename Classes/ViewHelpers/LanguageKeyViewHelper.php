<?php
namespace PunktDe\PtExtbase\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
 *  Authors:
 *    Christian Herberger <herberger@punkt.de>,
 *    Ursula Klinger <klinger@punkt.de>,
 *    Daniel Lienert <lienert@punkt.de>,
 *    Joachim Mathes <mathes@punkt.de>
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

use \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
/**
 * Viewhelper to get the language key (de, en) for a language id. Depends on correct
 * configuration in TypoScript.
 *
 * You can use the upperCase-parameter to return the key in complete upper case (DE, EN...)
 *
 */
class LanguageKeyViewHelper extends AbstractViewHelper
{
    /**
     * Get the language key for the current language
     *
     * @return string
     */
    protected function getLanguage()
    {
        if (TYPO3_MODE === 'FE') {
            if (isset($GLOBALS['TSFE']->config['config']['language'])) {
                return $GLOBALS['TSFE']->config['config']['language'];
            }
        } elseif (strlen($GLOBALS['BE_USER']->uc['lang']) > 0) {
            return $GLOBALS['BE_USER']->uc['lang'];
        }
        return 'en'; //default
    }

    /**
     * Return language key
     *
     * can be upper case by giving the upperCase-parameter
     *
     * @param bool $upperCase
     * @return string
     */
    public function render($upperCase = false)
    {
        $languageKey = $this->getLanguage();
        if ($upperCase) {
            $languageKey = strtoupper($languageKey);
        }

        return $languageKey;
    }
}
