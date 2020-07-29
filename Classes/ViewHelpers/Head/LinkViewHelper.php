<?php
namespace PunktDe\PtExtbase\ViewHelpers\Head;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Daniel Lienert <daniel@lienert.cc>,
 *
 *
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

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper used to render a HEAD meta tag
 *
 * @author Daniel Lienert
 * @package Viewhelpers
 * @subpackage Content/Head
 */
class LinkViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Disable the escaping interceptor because otherwise the child nodes would be escaped before this view helper
     * can decode the text's entities.
     *
     * @var boolean
     */
    protected $escapingInterceptorEnabled = false;


    /**
     * @var	string
     */
    protected $tagName = 'link';


    /**
     * @var PageRenderer
     */
    protected $pageRenderer;


    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
        $this->registerTagAttribute('type', 'string', 'Specifies the type of the link');
        $this->registerTagAttribute('title', 'string', 'Specifies the title');
        $this->registerTagAttribute('href', 'string', 'Specifies the relationship between the linked document and the current document');
    }



    /**
     * Initialize ViewHelper
     */
    public function initialize()
    {
        parent::initialize();

        if (TYPO3_MODE === 'FE' && $GLOBALS['TSFE']) {
            $this->pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
        }
    }



    /**
     * @param array $unEscapedTags
     */
    public function render($unEscapedTags = [])
    {
        $unEscapedTags[] = 'href';

        $this->markAsUnEscaped($unEscapedTags);

        if ($this->pageRenderer != null) {
            $metaTag = $this->tag->render();
            $this->pageRenderer->addMetaTag($metaTag);
        }
    }



    /**
     * @param $tagNames
     */
    protected function markAsUnEscaped($tagNames)
    {
        foreach ($tagNames as $tagName) {
            if ($this->hasArgument($tagName)) {
                $this->tag->addAttribute($tagName, $this->arguments[$tagName], false);
            }
        }
    }
}
