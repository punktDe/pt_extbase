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
 * Constant to speaking name mapper
 *
 * Maps speaking names of constants to their value and vice versa.
 *
 * @package pt_extbase
 * @subpackage Utility
 * @see Tx_PtExtbase_Tests_Unit_Utility_ConstantToSpeakingNameMapper
 */
abstract class Tx_PtExtbase_Utility_ConstantToSpeakingNameMapper implements t3lib_Singleton {

	/**
	 * @var array
	 */
	protected $originalSpeakingNameToConstantMapping;

	/**
	 * @var array
	 */
	protected $speakingNameToConstantMap = array();

	/**
	 * @var array
	 */
	protected $constantToSpeakingNameMap = array();

	/**
	 * @return Tx_PtExtbase_Utility_ConstantToSpeakingNameMapper
	 */
	public function __construct() {
		$className = $this->getClassName();
		$constantsReflection = new ReflectionClass($className);
		$this->originalSpeakingNameToConstantMapping = $constantsReflection->getConstants();
		$this->buildMaps();
	}

	/**
	 * @return void
	 */
	protected function buildMaps() {
		$this->buildSpeakingNameToConstantMap();
		$this->buildConstantToSpeakingNameMap();
	}

	/**
	 * @return array
	 */
	public function getSpeakingNameToConstantMap() {
		return $this->speakingNameToConstantMap;
	}

	/**
	 * @return array
	 */
	public function getConstantToSpeakingNameMap() {
		return $this->constantToSpeakingNameMap;
	}

	/**
	 * @param string $speakingName
	 * @return mixed
	 */
	public function getConstantFromSpeakingName($speakingName) {
		if (array_key_exists($speakingName, $this->speakingNameToConstantMap)) {
			return $this->speakingNameToConstantMap[$speakingName];
		}
		return NULL;
	}

	/**
	 * @param mixed $constant
	 * @return mixed
	 */
	public function getSpeakingNameFromConstant($constant) {
		if (array_key_exists($constant, $this->constantToSpeakingNameMap)) {
			return $this->constantToSpeakingNameMap[$constant];
		}
		return NULL;
	}

	/**
	 * @return string
	 */
	abstract protected function getClassName();

	/**
	 * return void
	 */
	abstract protected function buildSpeakingNameToConstantMap();

	/**
	 * return void
	 */
	abstract protected function buildConstantToSpeakingNameMap();

}