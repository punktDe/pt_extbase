<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 punkt.de GmbH
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
 * User Agent Information
 */
class Tx_PtExtbase_Utility_UserAgent implements t3lib_Singleton {

	/**
	 * @var array
	 */
	protected $knownOperatingSystems = array(
		'/windows nt 6.3/i'     =>  'Windows 8.1',
		'/windows nt 6.2/i'     =>  'Windows 8',
		'/windows nt 6.1/i'     =>  'Windows 7',
		'/windows nt 6.0/i'     =>  'Windows Vista',
		'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
		'/windows nt 5.1/i'     =>  'Windows XP',
		'/windows xp/i'         =>  'Windows XP',
		'/windows nt 5.0/i'     =>  'Windows 2000',
		'/windows me/i'         =>  'Windows ME',
		'/win98/i'              =>  'Windows 98',
		'/win95/i'              =>  'Windows 95',
		'/win16/i'              =>  'Windows 3.11',
		'/macintosh|mac os x/i' =>  'Mac OS X',
		'/mac_powerpc/i'        =>  'Mac OS 9',
		'/linux/i'              =>  'Linux',
		'/ubuntu/i'             =>  'Ubuntu',
		'/iphone/i'             =>  'iPhone',
		'/ipod/i'               =>  'iPod',
		'/ipad/i'               =>  'iPad',
		'/android/i'            =>  'Android',
		'/blackberry/i'         =>  'BlackBerry',
		'/webos/i'              =>  'Mobile',
	);



	/**
	 * @var array
	 */
	protected $knownBrowsers = array(
		'/msie/i'       =>  'Internet Explorer',
		'/firefox/i'    =>  'Firefox',
		'/safari/i'     =>  'Safari',
		'/chrome/i'     =>  'Chrome',
		'/opera/i'      =>  'Opera',
		'/netscape/i'   =>  'Netscape',
		'/maxthon/i'    =>  'Maxthon',
		'/konqueror/i'  =>  'Konqueror',
		'/mobile/i'     =>  'Handheld Browser',
	);



	/**
	 * @return string
	 */
	public function getOperatingSystem() {
		$operatingSystem = $this->findValueByMapping($this->knownOperatingSystems);
		return $operatingSystem ? $operatingSystem : 'No known operating system found in HTTP_USER_AGENT: ' . $this->getUserAgentData();
    }



	/**
	 * @return string
	 */
	public function getBrowser() {
		$browser = $this->findValueByMapping($this->knownBrowsers);
		return $browser ? $browser : 'No known browser found in HTTP_USER_AGENT: ' . $this->getUserAgentData();
    }



	/**
	 * @param array $mapping
	 * @return string|NULL
	 */
	protected function findValueByMapping($mapping) {
		$result = '';
		foreach ($mapping as $regex => $value) {
			if (preg_match($regex, $this->getUserAgentData())) {
				$result = $value;
			}
		}
		return $result;
	}



	/**
	 * @return string
	 */
	protected function getUserAgentData() {
		return t3lib_div::getIndpEnv('HTTP_USER_AGENT');
	}

}
