<?php

/**
 * View helper check if given value is array or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_PtExtbase_ViewHelpers_Condition_IsArrayViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * View helper check if given value is array or not
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function render($value = '') {
		return is_array($value);
	}
}