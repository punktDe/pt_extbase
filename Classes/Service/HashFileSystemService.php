<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
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
 ****************************************************************/


/**
 * Class HashFileSystemService
 * @package Services
 *
 * @author Daniel Lienert
 * @see Tx_PtExtbase_Tests_Functional_Service_HashFileSystemServiceTest
 */
class Tx_PtExtbase_Service_HashFileSystemService {

	/**
	 * @var string
	 */
	protected $rootDirectory;



	/**
	 * @param $rootDirectory
	 */
	public function __construct($rootDirectory) {
		Tx_PtExtbase_Utility_Files::createDirectoryRecursively($rootDirectory);
		$this->rootDirectory = $rootDirectory;
	}


	/**
	 * @param $astId
	 * @param $filePath
	 * @param string $destinationFileName
	 */
	public function storeFile($astId, $filePath, $destinationFileName = '') {
		$destinationFileName = trim($destinationFileName) ? trim($destinationFileName) : basename($filePath);
		$targetPath = Tx_PtExtbase_Utility_Files::concatenatePaths(array($this->getHashPath($astId, TRUE), $destinationFileName));
		copy($filePath, $targetPath);
	}


	/**
	 * @param $astId
	 * @param $fileName
	 * @return mixed
	 */
	public function getFilePath($astId, $fileName) {
		return Tx_PtExtbase_Utility_Files::concatenatePaths(array($this->getHashPath($astId, TRUE), $fileName));
	}



	/**
	 * @param $astId
	 * @param $fileName
	 * @return bool
	 */
	public function fileExists($astId, $fileName) {
		return file_exists(Tx_PtExtbase_Utility_Files::concatenatePaths(array($this->getHashPath($astId), $fileName)));
	}



	/**
	 * @param $astId
	 * @param $fileName
	 */
	public function removeFile($astId, $fileName) {
		if(file_exists($this->getFilePath($astId, $fileName))) {
			Tx_PtExtbase_Utility_Files::unlink($this->getFilePath($astId, $fileName));
		}
	}



	/**
	 * @param $astId
	 */
	public function removeStoreDirectory($astId) {
		Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->getHashPath($astId, TRUE));
	}


	/**
	 * @param $astId
	 * @return array
	 */
	public function getDirectoryListing($astId) {
		return Tx_PtExtbase_Utility_Files::readDirectoryRecursively($this->getHashPath($astId, TRUE));
	}


	/**
	 * @param $astId
	 * @param bool $createDirectory
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	public function getHashPath($astId, $createDirectory = FALSE) {
		$astId = (int) $astId;
		if($astId == 0) throw new \InvalidArgumentException('The AstId must be an integer > 0', 1369816965);

		$level1 = $astId % 10;
		$level2 = $astId % 100;

		$hashPath = Tx_PtExtbase_Utility_Files::concatenatePaths(array($this->rootDirectory, $level1, $level2, $astId));

		if($createDirectory) Tx_PtExtbase_Utility_Files::createDirectoryRecursively($hashPath);

		return $hashPath;
	}


	/**
	 * Remove the complete directory
	 */
	public function removeHasFileSystemCompletely() {
		Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->rootDirectory);
	}
}