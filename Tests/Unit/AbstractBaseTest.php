<?php
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



/**
 * Class implements a base testcase for pt_extbase testcases
 *
 * @package Tests\Unit
 * @author Michael Knoll <knoll@punkt.de>
 */
abstract class Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase extends \TYPO3\CMS\Core\Tests\UnitTestCase
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
    public function runBare()
    {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $this->objectManager =  clone $objectManager;
        parent::runBare();
    }



    /**
     * Shortcut for creating a mock with no mocked methods, no constructor call and no changed class name
     *
     * @param string $className Class name of mock to be created
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getSimpleMock($className)
    {
        return $this->getMock($className, array(), array(), '', false);
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
     * Returns a mocked \TYPO3\CMS\Fluid\View\TemplateView object with a mocked assign method.
     *
     * @return \TYPO3\CMS\Fluid\View\TemplateView The mocked view class
     */
    public function getViewMockWithMockedAssignMethod()
    {
        return $this->getMock('\TYPO3\CMS\Fluid\View\TemplateView', array('assign'), array(), '', false);
    }

    /**
     * @param array $actualErrors
     * @param array $expectedErrorCodes
     */
    protected function assertErrorCodes(array $expectedErrorCodes, array $actualErrors)
    {
        $actualErrorCodes = array();

        foreach ($actualErrors as $actualError) { /** @var \TYPO3\CMS\Extbase\Error\Error $actualError */
            $actualErrorCodes[] = $actualError->getCode();
        }

        sort($expectedErrorCodes);
        sort($actualErrorCodes);

        $this->assertEquals($expectedErrorCodes, $actualErrorCodes);
    }
}
