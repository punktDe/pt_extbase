<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Joachim Mathes <mathes@punkt.de>
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
 * Captcha
 *
 * @package pt_extbase
 * @subpackage ViewHelpers
 */
class Tx_PtExtbase_ViewHelpers_CaptchaViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	protected $tagName = 'img';

	/**
	 * @var string
	 */
	protected $captchaGeneratorPath;

	/**
	 * Initialize ViewHelper
	 *
	 * @return void
	 */
	public function initialize() {
		parent::initialize();
		$this->registerUniversalTagAttributes();
		$this->captchaGeneratorPath = t3lib_extMgm::siteRelPath('captcha') . 'captcha/captcha.php';
	}

	/**
	 * Render
	 *
	 * @return string
	 */
	public function render() {
		$this->tag->addAttribute('src', $this->captchaGeneratorPath);
		return $this->tag->render();
	}
	
}
