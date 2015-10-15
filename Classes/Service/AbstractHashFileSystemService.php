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
abstract class Tx_PtExtbase_Service_AbstractHashFileSystemService
{
    /**
     * @var string
     */
    protected $rootDirectory;



    /**
     * @param $rootDirectory
     */
    public function __construct($rootDirectory)
    {
        Tx_PtExtbase_Utility_Files::createDirectoryRecursively($rootDirectory);
        $this->rootDirectory = $rootDirectory;
    }


    /**
     * @param $identifier
     * @param $filePath
     * @param string $destinationFileName
     */
    public function storeFile($identifier, $filePath, $destinationFileName = '')
    {
        $destinationFileName = trim($destinationFileName) ? trim($destinationFileName) : basename($filePath);
        $targetPath = Tx_PtExtbase_Utility_Files::concatenatePaths(array($this->getHashPath($identifier, true), $destinationFileName));
        copy($filePath, $targetPath);
    }


    /**
     * @param $identifier
     * @param $fileName
     * @return mixed
     */
    public function getFilePath($identifier, $fileName)
    {
        return Tx_PtExtbase_Utility_Files::concatenatePaths(array($this->getHashPath($identifier, true), $fileName));
    }



    /**
     * @param $identifier
     * @param $fileName
     * @return bool
     */
    public function fileExists($identifier, $fileName)
    {
        return file_exists(Tx_PtExtbase_Utility_Files::concatenatePaths(array($this->getHashPath($identifier), $fileName)));
    }



    /**
     * @param $identifier
     * @param $fileName
     */
    public function removeFile($identifier, $fileName)
    {
        if (file_exists($this->getFilePath($identifier, $fileName))) {
            Tx_PtExtbase_Utility_Files::unlink($this->getFilePath($identifier, $fileName));
        }
    }



    /**
     * @param $identifier
     */
    public function removeStoreDirectory($identifier)
    {
        Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->getHashPath($identifier, true));
    }


    /**
     * @param $identifier
     * @return array
     */
    public function getDirectoryListing($identifier)
    {
        return Tx_PtExtbase_Utility_Files::readDirectoryRecursively($this->getHashPath($identifier, true));
    }


    /**
     * @param string $identifier
     * @param bool $createDirectory
     * @return string
     */
    abstract public function getHashPath($identifier, $createDirectory = false);


    /**
     * Remove the complete directory
     */
    public function removeHasFileSystemCompletely()
    {
        Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->rootDirectory);
    }
}
