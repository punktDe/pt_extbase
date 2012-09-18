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
 * PHP File Sql Generator
 *
 * @package pt_extbase
 * @subpackage SqlGenerator
 */
class Tx_PtExtbase_SqlGenerator_PhpFileSqlGenerator implements Tx_PtExtbase_SqlGenerator_SqlGeneratorCommandInterface {

	/**
	 * @var array
	 */
	protected $classNames;

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param string $filePath
	 * @return array
	 */
	public function generate($filePath) {
		$this->getClassNames($filePath);
		return $this->generateSqls();
	}

	/**
	 * @param $filePath
	 * @return void
	 */
	protected function getClassNames($filePath) {
		$this->classNames = array();
		$tokens = token_get_all(file_get_contents($filePath));
		$classToken = FALSE;
		foreach ($tokens as $token) {
			if (is_array($token)) {
				if ($token[0] == T_CLASS) {
					$classToken = TRUE;
				} else if ($classToken && $token[0] == T_STRING) {
					$classToken = FALSE;
					$this->classNames[] = $token[1];
				}
			}
		}
	}

	/**
	 * @return array
	 */
	protected function generateSqls() {
		$sqls = array();
		foreach ($this->classNames as $className) {
			$sqlGenerator = $this->objectManager->get($className); /** @var Tx_PtExtbase_SqlGenerator_SqlGeneratorCommandInterface $sqlGenerator */
			$sqls = array_merge($sqls, $sqlGenerator->generate());
		}
		return $sqls;
	}

}
?>