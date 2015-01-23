<?php
 /***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lienert <lienert@punkt.de>
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

namespace PunktDe\PtExtbase\Utility;


class TimeTracker {

	/**
	 * @var array
	 */
	protected static $startDates;


	/**
	 * @param $trackIdentifier
	 */
	public static function start($trackIdentifier) {
		self::$startDates[$trackIdentifier] = microtime(TRUE);
	}


	/**
	 * @param $trackIdentifier
	 * @return float Measured time in milliseconds
	 */
	public static function stop($trackIdentifier) {
		if(!array_key_exists($trackIdentifier, self::$startDates)) {
			return -1;
		} else {
			$startDate = self::$startDates[$trackIdentifier];
			unset(self::$startDates[$trackIdentifier]);

			return (microtime(TRUE) - $startDate) * 1000;
		}
	}
} 