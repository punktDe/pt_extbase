<?php

class Tx_PtExtbase_ViewHelpers_StringComparisonViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

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