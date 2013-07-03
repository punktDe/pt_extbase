<?php

class Tx_PtExtbase_ViewHelpers_ClassConstantViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @param string $className
	 * @param string $constantName
	 * @return mixed
	 */
	public function render($className, $constantName) {
		return constant(sprintf('%s::%s', $className, $constantName));
	}
}