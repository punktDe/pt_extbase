<?php
namespace PunktDe\PtExtbase\ViewHelpers\Content;
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * View helper to render content of a page
 *
 * @package pt_dppp_base
 * @subpackage ViewHelpers\PageContent
 */
class RenderPageViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('pageUid', 'integer', 'Page Uid', true);
    }

    /**
     * Get the rendered content from a page
     *
     * @return string The output
     */
    public function render()
    {
        if (!($GLOBALS['TSFE']->cObj instanceof ContentObjectRenderer)) {
            $GLOBALS['TSFE']->cObj = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        }

        $pageUid = $this->arguments['pageUid'];

        $sysLanguageUid = intval($GLOBALS['TSFE']->sys_language_uid);

        $conf = [ // config
            'table' => 'tt_content',
            'select.' => [
                'pidInList' => $pageUid,
                'where' => 'colPos=0 AND (sys_language_uid=' . $sysLanguageUid . ' OR sys_language_uid=-1)'
            ],
        ];
        $result = $GLOBALS['TSFE']->cObj->cObjGetSingle('CONTENT', $conf);

        $result = $this->addPageCssForCssStyledContent($result);

        return $result;
    }

    /**
     * @param string $result
     * @return string
     */
    protected function addPageCssForCssStyledContent(string $result): string
    {
        $cssPageStyle = '';
        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cssstyledcontent.']['_CSS_PAGE_STYLE']) && is_array($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cssstyledcontent.']['_CSS_PAGE_STYLE'])) {
            $cssPageStyle = implode(LF, $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cssstyledcontent.']['_CSS_PAGE_STYLE']);
        }

        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cssstyledcontent.']['_CSS_PAGE_STYLE.'])) {
            $cssPageStyle = $GLOBALS['TSFE']->cObj->stdWrap($cssPageStyle, $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cssstyledcontent.']['_CSS_PAGE_STYLE.']);
        }
        if (strlen($cssPageStyle) > 0) {
            $result = '<style type="text/css">' . $cssPageStyle . '</style>' . $result;
        }
        return $result;
    }
}
