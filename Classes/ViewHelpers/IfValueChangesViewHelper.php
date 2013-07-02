<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
 *
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
 * IfValueChangesViewHelper
 *
 * Acts like an if-ViewHelper
 * Renders the then part if the given value is something else as the last time
 * Can be used to render headlines or structure a list.
 *
 *
 * = Examples =
 *
 * <code title="Single Value Usage">
 * {namespace ptx=Tx_PtExtbase_ViewHelpers}
 * <ptx:ifValueChanges value="{value}">
 *     <f:then>
 *         <h2>{value}</h2>
 *     </f:then>
 *     <f:else>
 *         <h2>Nothing to display</h2>
 *     </f:else>
 * </ptx:ifValueChanges>
 * </code>
 * <output>
 * Everything inside the <f:then> tag is being displayed if the value changes. If nothing has changed
 * everything inside the <f:else> tag is being displayed.
 * </output>
 *
 *
 * <code title="Multi Value Usage">
 * {namespace ptx=Tx_PtExtbase_ViewHelpers}
 * <ptx:ifValueChanges value="{outer-value}" key="outer-value">
 *     <h2>{outer-value}</h2>
 *     <ptx:ifValueChanges value="{inner-value}" key="inner-value">
 *         <h3>{inner-value}</h3>
 *     </ptx:ifValueChanges>
 * </ptx:ifValueChanges>
 * </code>
 * <output>
 * Everything inside the <ptx:ifValueChanges> tag is being displayed if the value for the given key changes.
 * </output>
 *
 *
 * <code title="Reset inner value">
 * {namespace ptx=Tx_PtExtbase_ViewHelpers}
 * <ptx:ifValueChanges value="{outer-value}" key="outer-value">
 *     <h2>{outer-value}</h2>
 *     <ptx:ifValueChanges value="{inner-value}" key="inner-value">
 *         <h3>{inner-value}</h3>
 *     </ptx:ifValueChanges>
 *     <ptx:ifValueChanges value="{inner-value}" key="IMPOSSIBLE_VALUE">
 * </ptx:ifValueChanges>
 * </code>
 * <output>
 * Reset the inner value to prevent non-rendering of values, if outer value changed, but inner value did not.
 * </output>
 *
 *
 * @author Daniel Lienert
 * @author Michael Knoll
 * @package ViewHelpers
 * @see Tx_PtExtbase_Tests_Unit_ViewHelpers_IfValueChangesViewHelperTest
 */
class Tx_PtExtbase_ViewHelpers_IfValueChangesViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * @var null
	 */
	protected static $lastValue = NULL;



	/**
	 * Holds an array of key / value pairs if store change values with a key.
	 *
	 * @var array
	 */
	protected static $lastValuesArray = array();



	/**
	 * @param string $value Value to be checked for changes
	 * @param string $key (optional) A key for which to check, whether the value has changed
	 * @return string
	 */
	public function render($value, $key = NULL) {

		if ($key === NULL) {

			// We are in "SINGLE VALUE MODE"
			if ($value != self::$lastValue) {
				self::$lastValue = $value;
				return $this->renderThenChild();
			} else {
				return $this->renderElseChild();
			}

		} else {

			// We are in "MULTI VALUE MODE"
			if (empty(self::$lastValuesArray[$key]) || $value != self::$lastValuesArray[$key]) {
				self::$lastValuesArray[$key] = $value;
				return $this->renderThenChild();
			} else {
				return $this->renderElseChild();
			}

		}
	}
}