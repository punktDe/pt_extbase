<?php

class Tx_PtExtbase_ViewHelpers_StringComparisonViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param string $input
	 * @param string $expected
	 * @return integer
	 */
	public function render($input, $expected) {
		if ($input == $expected) {
			return 1;
		} else {
			return 0;
		}
	}
}