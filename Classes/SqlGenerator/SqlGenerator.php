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
 * SQL Generator
 *
 * @package pt_extbase
 * @subpackage SqlGenerator
 */
class Tx_PtExtbase_SqlGenerator_SqlGenerator implements Tx_PtExtbase_SqlGenerator_SqlGeneratorCommandInterface {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $sqlGenerators;

	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->sqlGenerators = array(
			'php' =>  $this->objectManager->get('Tx_PtExtbase_SqlGenerator_PhpFileSqlGenerator'),
			'sql' => $this->objectManager->get('Tx_PtExtbase_SqlGenerator_SqlFileSqlGenerator'),
		);
	}

	/**
	 * @param string $filePath
	 * @return string
	 * @throws Exception
	 */
	public function generate($filePath) {
		$extension = pathinfo($filePath, PATHINFO_EXTENSION);
		$this->checkFilePath($filePath);
		if (in_array($extension, array_keys($this->sqlGenerators))) {
			return $this->sqlGenerators[$extension]->generate($filePath);
		}
		throw new Exception('Not a valid file extension: ' . $filePath . '! 1347035058');
	}

	/**
	 * @param $filePath
	 * @throws Exception
	 */
	protected function checkFilePath($filePath) {
		if (!is_file($filePath)) {
			throw new Exception('Not a valid file: ' . $filePath . '! 1347035058');
		}
	}

}
?>