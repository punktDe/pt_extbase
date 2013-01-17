<?php
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

/**
 * Zora-Card-Id Validator
 *
 * @package pt_extbase
 * @subpackage Domain\Validator
 */
class Tx_PtExtbase_Domain_Validator_CaptchaStringValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	const CAPTCHA_SESSION_KEY = 'tx_captcha_string';

	/**
	 * @var
	 */
	protected $captchaString;

	/**
	 * @param string $captchaString The value that should be validated
	 * @return boolean TRUE if the value is valid, FALSE if an error occurred
	 */
	public function isValid($captchaString) {
		session_start();
		if ($captchaString != $_SESSION[self::CAPTCHA_SESSION_KEY]) {
			$this->addError('Captcha string does not conform to captcha.', 1340029430);
			return FALSE;
		}
		return TRUE;
	}

}
?>