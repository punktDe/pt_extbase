<?php
namespace PunktDe\PtExtbase\Testing\Unit;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\TemplateView;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;


/**
 * Class implements a base testcase for pt_extbase testcases
 */
abstract class AbstractBaseTestcase extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface The object manager
     */
    protected $objectManager;


    /**
     * Injects an untainted clone of the object manager and all its referencing
     * objects for every test.
     *
     * @return void
     */
    public function runBare(): void
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->objectManager =  clone $objectManager;
        parent::runBare();
    }



    /**
     * Shortcut for creating a mock with no mocked methods, no constructor call and no changed class name
     *
     * @param string $className Class name of mock to be created
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getSimpleMock($className)
    {
        return $this->getMockBuilder($className)
            ->getMock();
    }



    /**
     * Asserts that a class with given class name exists.
     *
     * @param string $className
     * @param string $message
     */
    protected function assertClassExists($className, $message = '')
    {
        if ($message === '') {
            $message = 'Failed asserting that class ' . $className . ' exists.';
        }
        $this->assertTrue(class_exists($className), $message);
    }



    /**
     * Asserts that given object is of a given class.
     *
     * @param object $object
     * @param string $className
     * @param string $message
     */
    protected function assertIsA($object, $className, $message = '')
    {
        if ($message === '') {
            $message = 'Failed asserting that ' . get_class($object) . ' is a ' . $className;
        }
        $this->assertTrue(is_a($object, $className), $message);
    }

    /**
     * @param array $actualErrors
     * @param array $expectedErrorCodes
     */
    protected function assertErrorCodes(array $expectedErrorCodes, array $actualErrors)
    {
        $actualErrorCodes = [];

        foreach ($actualErrors as $actualError) { /** @var \TYPO3\CMS\Extbase\Error\Error $actualError */
            $actualErrorCodes[] = $actualError->getCode();
        }

        sort($expectedErrorCodes);
        sort($actualErrorCodes);

        $this->assertEquals($expectedErrorCodes, $actualErrorCodes);
    }
}
