<?php

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

/**
 * ViewHelper used to render a HEAD meta tag
 *
 * @author Daniel Lienert
 * @package Viewhelpers
 * @subpackage Content/Head
 */
class Tx_PtExtbase_ViewHelpers_Head_MetaViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {


	/**
	 * Disable the escaping interceptor because otherwise the child nodes would be escaped before this view helper
	 * can decode the text's entities.
	 *
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;


	/**
	 * @var	string
	 */
	protected $tagName = 'meta';


	/**
	 * @var t3lib_PageRenderer
	 */
	protected $pageRenderer;


	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerTagAttribute('property', 'string', 'Property key');
		$this->registerTagAttribute('name', 'string', 'Name property of meta tag');
		$this->registerTagAttribute('http-equiv', 'string', 'Property: http-equiv');
		$this->registerTagAttribute('scheme', 'string', 'Property: scheme');
		$this->registerTagAttribute('lang', 'string', 'Property: lang');
		$this->registerTagAttribute('dir', 'string', 'Property: dir');
		$this->registerTagAttribute('content', 'string', 'Content of meta tag');
	}



	/**
	 * Initialize ViewHelper
	 */
	public function initialize() {
		parent::initialize();

		if (TYPO3_MODE === 'FE' && $GLOBALS['TSFE']) {
			$this->pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
		}
	}



	/**
	 * @param array $unEscapedTags
	 */
	public function render($unEscapedTags = array()) {
		$this->markAsUnEscaped($unEscapedTags);

		if($this->pageRenderer != NULL) {
			$metaTag = $this->tag->render();
			$this->pageRenderer->addMetaTag($this->tag->render($metaTag));
		}
	}



	/**
	 * @param $tagNames
	 */
	protected function markAsUnEscaped($tagNames) {
		foreach($tagNames as $tagName) {
			if($this->hasArgument($tagName)) {
				$this->tag->addAttribute($tagName, $this->arguments[$tagName],FALSE);
			}
		}
	}

}
