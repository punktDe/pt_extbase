<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Michael Knoll <mimi@kaktusteam.de>
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 3 of the License, or
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
 * View helper which renders the flash messages (if there are any) as an unsorted list.
 *
 * In case you need custom Flash Message HTML output, please write your own ViewHelper for the moment.
 *
 * Possible severities:
 * 	const NOTICE  = -2;
 *	const INFO    = -1;
 *	const OK      = 0;
 *	const WARNING = 1;
 *	const ERROR   = 2;
 *
 * = Examples =
 *
 * <code title="Simple">
 * <f:flashMessages />
 * </code>
 * <output>
 * An ul-list of flash messages.
 * </output>
 *
 * <code title="Output with custom css class">
 * <f:flashMessages class="specialClass" />
 * </code>
 * <output>
 * <ul class="specialClass">
 *  ...
 * </ul>
 * </output>
 *
 * <code title="TYPO3 core style">
 * <f:flashMessages renderMode="div" />
 * </code>
 * <output>
 * <div class="typo3-messages">
 *   <div class="typo3-message message-ok">
 *     <div class="message-header">Some Message Header</div>
 *     <div class="message-body">Some message body</div>
 *   </div>
 *   <div class="typo3-message message-notice">
 *     <div class="message-body">Some notice message without header</div>
 *   </div>
 * </div>
 * </output>
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class Tx_PtExtbase_ViewHelpers_FlashMessagesViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	const RENDER_MODE_UL = 'ul';
	const RENDER_MODE_DIV = 'div';

	/**
	 * Initialize arguments
	 *
	 * @return void
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @api
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
	}

	/**
	 * Render method.
	 *
	 * @param string $renderMode one of the RENDER_MODE_* constants
	 * @param array $messageCssClasses Array of message severities and corresponding CSS classes
	 * @return string rendered Flash Messages, if there are any.
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @author Michael Knoll <mimi@kaktusteam.de>
	 * @api
	 */
	public function render($renderMode = self::RENDER_MODE_UL, $messageCssClasses=array()) {
		$flashMessages = $this->controllerContext->getFlashMessageContainer()->getAllMessagesAndFlush();
		if ($flashMessages === NULL || count($flashMessages) === 0) {
			return '';
		}
		switch ($renderMode) {
			case self::RENDER_MODE_UL:
				return $this->renderUl($flashMessages, $messageCssClasses);
			case self::RENDER_MODE_DIV:
				return $this->renderDiv($flashMessages, $messageCssClasses);
			default:
				throw new Tx_Fluid_Core_ViewHelper_Exception('Invalid render mode "' . $renderMode . '" passed to FlashMessageViewhelper', 1290697924);
		}
	}

	/**
	 * Renders the flash messages as unordered list
	 *
	 * @param array $flashMessages array<t3lib_FlashMessage>
	 * @param array $messageCssClasses
	 * @return string
	 */
	protected function renderUl(array $flashMessages, array $messageCssClasses) {
		$this->tag->setTagName('ul');
		if ($this->hasArgument('class')) {
			$this->tag->addAttribute('class', $this->arguments['class']);
		}
		$tagContent = '';
		foreach ($flashMessages as $singleFlashMessage) { /* @var $singleFlashMessage t3lib_FlashMessage */
			$tagContent = '<li';

			// Set individual class for each error message
			if (array_key_exists($singleFlashMessage->getSeverity(), $messageCssClasses)) {
				$tagContent .= ' class="' .  $messageCssClasses[$singleFlashMessage->getSeverity()] . '"';
			}
			$tagContent .= '>';

			// Set title
			if ($singleFlashMessage->getTitle()) {
				$tagContent .= '<strong>' . $singleFlashMessage->getTitle() . '</strong><br>';
			}

			// Set message
			$tagContent .= htmlspecialchars($singleFlashMessage->getMessage()) . '</li>';
		}
		$this->tag->setContent($tagContent);
		return $this->tag->render();
	}

	/*
	 * Renders the flash messages as nested divs
	 *
	 * @param array $flashMessages array<t3lib_FlashMessage>
	 * @param array $messageCssClasses
	 * @return string
	 */
	protected function renderDiv(array $flashMessages, array $messageCssClasses) {
		$this->tag->setTagName('div');
		if ($this->hasArgument('class')) {
			$this->tag->addAttribute('class', $this->arguments['class']);
		} else {
			$this->tag->addAttribute('class', 'typo3-messages');
		}
		$tagContent = '';
		foreach ($flashMessages as $singleFlashMessage) {
			/* @var $singleFlashMessage t3lib_FlashMessage */
			$tagContent .= '<div';

			// Set individual css class
			if (array_key_exists($singleFlashMessage->getSeverity(), $messageCssClasses)) {
				$tagContent .= ' class="' .  $messageCssClasses[$singleFlashMessage->getSeverity()] . '"';
			}
			$tagContent .= '>';

			// Set title if there is one
			if ($singleFlashMessage->getTitle()) {
				$tagContent .= '<strong>' . $singleFlashMessage->getTitle() . '</strong><br>';
			}

			// Set message
			$tagContent .= $singleFlashMessage->getMessage();
			$tagContent .= '</div>';
		}
		$this->tag->setContent($tagContent);
		return $this->tag->render();
	}
}
?>
