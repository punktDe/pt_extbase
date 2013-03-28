<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Michael Knoll <knoll@punkt.de>, punkt.de GmbH
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
 * Viewhelper for rendering a file size e.g. 1 kb from a given bytesize 1024
 *
 * @example <ptx:format.fileSizeFromNumber unit="KB" size="100" />
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package ViewHelpers
 * @subpackage Format
 */
class Tx_PtExtbase_ViewHelpers_Format_FileSizeFromNumberViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Renders the file size from a given size.
	 *
	 * @param int $size The file size we want to format
	 * @param string $unit The unit in which file size is given. One of B | KB | MB | GB
	 * @param bool $upperCase If set to true, unit label is printed in uppercase
	 * @return string The formatted file size
	 */
	public function render($size, $unit = 'B', $upperCase = TRUE) {
		if ($unit == 'KB') {
			$size = $size * 1024;
		} else if ($unit == 'MB') {
			$size = $size * 1024 * 1024;
		} else if ($unit == 'GB') {
			$size = $size * 1024 * 1024 * 1024;
		}

		$label = $this->size($size);
		if ($upperCase) {
			$label = strtoupper($label);
		} else {
			strtolower($label);
		}

		return $label;
	}



	/**
	 * Formats given bytes. Returns formatted file size string.
	 *
	 * @param $bytes
	 * @return string
	 */
	protected function size($bytes) {
		$bytes = (double)$bytes;
	    if ($bytes > 0) {
	        $unit = intval(log($bytes, 1024));
	        $units = array('B', 'KB', 'MB', 'GB');

	        if (array_key_exists($unit, $units) === true) {
	            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
	        }
	    }

	    return $bytes;
	}

}