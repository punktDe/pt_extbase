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
 ***************************************************************/


/**
 * TestCase for hashFileSystemService
 *
 * @author Daniel Lienert
 * @see Tx_PtExtbase_Service_HashFileSystemService
 */
class Tx_PtExtbase_Tests_Functional_Service_HashFileSystemServiceTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var string
     */
    protected $testDirectoryRoot;


    /**
     * @var Tx_PtExtbase_Service_HashFileSystemService
     */
    protected $hashFileSystemService;



    /**
     * Initialize the test data
     */
    public function setUp()
    {
        parent::setUp();

        $this->testDirectoryRoot = Tx_PtExtbase_Utility_Files::concatenatePaths([PATH_site, 'typo3temp', 'HashFileSystemServiceTest']);
        if (file_exists($this->testDirectoryRoot)) {
            Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->testDirectoryRoot);
        }
        $this->hashFileSystemService = new Tx_PtExtbase_Service_HashFileSystemService($this->testDirectoryRoot);
    }



    public function tearDown()
    {
        if (file_exists($this->testDirectoryRoot)) {
            Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->testDirectoryRoot);
        }
    }



    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function getDirectoryHashPathThrowsExceptionIfNoAstIdIsGiven()
    {
        $this->hashFileSystemService->getHashPath(0);
    }



    public function hashFileSystemDataProvider()
    {
        return [
            '1Digit' => ['astId' => 1, 'expectedPath' => '/1/1/1'],
            'multipleDigits' => ['astId' => 79784646, 'expectedPath' => '/6/46/79784646'],
        ];
    }



    /**
     * @test
     * @dataProvider hashFileSystemDataProvider
     * @param $astId
     * @param $expectedPath
     */
    public function getHashPath($astId, $expectedPath)
    {
        $expectedPath = $this->testDirectoryRoot . $expectedPath;

        $path = $this->hashFileSystemService->getHashPath($astId, true);
        $this->assertEquals($expectedPath, $path);

        $this->assertFileExists($expectedPath);
    }



    /**
     * @test
     */
    public function getDirectoryListing()
    {
        $astId = 123456;
        $path = $this->hashFileSystemService->getHashPath($astId, true);

        $expectedFileNames = ['file 1.txt', 'Gedoens.xls', 'file3.pdf'];
        sort($expectedFileNames);
        $expectedFullPathFileNames = [];

        foreach ($expectedFileNames as $key => $fileName) {
            $expectedFullPathFileNames[$key] = $path . '/' . $fileName;
            touch($expectedFullPathFileNames[$key]);
        }

        $directoryListing = $this->hashFileSystemService->getDirectoryListing($astId);
        sort($directoryListing);

        $this->assertEquals($expectedFullPathFileNames, $directoryListing);
    }



    /**
     * @test
     */
    public function fileExistsTest()
    {
        $path = $this->hashFileSystemService->getHashPath(123456, true);
        touch($path . '/' . 'test.pdf');

        $this->assertFalse($this->hashFileSystemService->fileExists(123, 'test.pdf'));
        $this->assertTrue($this->hashFileSystemService->fileExists(123456, 'test.pdf'));
    }


    /**
     * @test
     */
    public function getFilePath()
    {
        $path = $this->hashFileSystemService->getHashPath(123456, true);
        touch($path . '/' . 'test.pdf');
        
        $actual = $this->hashFileSystemService->getFilePath(123456, 'test.pdf');
        $this->assertEquals($this->testDirectoryRoot . '/6/56/123456/test.pdf', $actual);
    }
    
    
    /**
     * @test
     */
    public function storeFile()
    {
        $testFile = Tx_PtExtbase_Utility_Files::concatenatePaths([PATH_site, 'typo3temp', 'test.pdf']);
        touch($testFile);
        $this->hashFileSystemService->storeFile(1234, $testFile);

        $expectedFileLocation = $this->hashFileSystemService->getHashPath(1234) . '/test.pdf';

        $this->assertFileExists($expectedFileLocation);
    }



    /**
     * @test
     */
    public function removeFile()
    {
        $expectedFileLocation = $this->hashFileSystemService->getHashPath(1234) . '/test.pdf';
        $this->assertFileNotExists($expectedFileLocation);

        $testFile = Tx_PtExtbase_Utility_Files::concatenatePaths([PATH_site, 'typo3temp', 'test.pdf']);
        touch($testFile);

        $this->hashFileSystemService->storeFile(1234, $testFile);
        $this->assertFileExists($expectedFileLocation);

        $this->hashFileSystemService->removeFile(1234, 'test.pdf');
        $this->assertFileNotExists($expectedFileLocation);
    }


    /**
     * @test
     */
    public function removeStoreDirectory()
    {
        $path = $this->hashFileSystemService->getHashPath(123456, true);
        $this->assertFileExists($path);
        $this->hashFileSystemService->removeStoreDirectory(123456);
        $this->assertFileNotExists($path);
    }


    /**
     * @test
     */
    public function removeHasFileSystemCompletely()
    {
        $this->hashFileSystemService->removeHasFileSystemCompletely();
        $this->assertFalse(is_dir($this->testDirectoryRoot));
    }
}
