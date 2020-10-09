<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 punkt.de GmbH
 *  Authors:
 *    Joachim Mathes <mathes@punkt.de>,
 *    Sascha Dörr <doerr@punkt.de>
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

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Container\Container as ExtbaseContainer;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Testbase;

/**
 * Database Test Case
 *
 * This class is based on the Extbase base test case.
 *
 * @package pt_extbase
 * @subpackage Testing\FixtureFramework
 */
abstract class Tx_PtExtbase_Testing_FixtureFramework_DatabaseTestCase extends \PHPUnit\DbUnit\TestCase
{
    /**
     * This array contains strings of domains, which are allowed to run database tests on.
     * The domain should be used to choose the configuration to be loaded. Thus a dedicated
     * database connection can be loaded.
     *
     * @var array
     */
    protected $allowedDomains = [];

    /**
     * This array contains strings of TYPO3_CONTEXTs, which are allowed to run database tests on.
     * The TYPO3_CONTEXT should be used to choose the configuration to be loaded. Thus a dedicated
     * database connection can be loaded.
     *
     * @var array
     */
    protected $allowedApplicationContexts = [];

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface The object manager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $fixtures = [];

    /**
     * Set up
     *
     * This setUp() does not call its parent implementation to avoid database cleaning
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (!(in_array(Environment::getContext(), $this->allowedApplicationContexts)
                || in_array($_SERVER['HOSTNAME'], $this->allowedDomains)
                || in_array($_SERVER['HTTP_HOST'], $this->allowedDomains))) {
            $this->fail(sprintf('This test is only allowed in contexts "%s" used %s or on domains "%s" used %s', implode(', ', $this->allowedApplicationContexts), GeneralUtility::getApplicationContext(), implode(', ', $this->allowedDomains), $_SERVER['HOSTNAME'] . ' ' . $_SERVER['HTTP_HOST']));
        }
        $fixtureImporter = GeneralUtility::makeInstance('Tx_PtExtbase_Testing_FixtureFramework_FixtureImporter'); /** @var Tx_PtExtbase_Testing_FixtureFramework_FixtureImporter $fixtureImporter */
        $fixtureImporter->import($this->getFixtures());
    }

    /**
     * Injects an untainted clone of the object manager and all its referencing
     * objects for every test.
     * @throws Throwable
     */
    public function runBare(): void
    {
        $instancePath = Environment::getCurrentScript();
        $testbase = new Testbase();
        $container = $testbase->setUpBasicTypo3Bootstrap($instancePath);
        $extbaseContainer = GeneralUtility::getContainer()->get(ExtbaseContainer::class);

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class, $container, $extbaseContainer);

        parent::runBare();
    }

    /**
     * Returns a mock object which allows for calling protected methods and access
     * of protected properties.
     *
     * @param $originalClassName
     * @param array $methods
     * @param array $arguments
     * @param string $mockClassName
     * @param boolean $callOriginalConstructor
     * @param boolean $callOriginalClone
     * @param boolean $callAutoload
     * @internal param string $className Full qualified name of the original class
     * @return object
     * @author Robert Lemke <robert@typo3.org>
     * @api
     */
    protected function getAccessibleMock($originalClassName, $methods = [], array $arguments = [], $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true)
    {
        return $this->getMock($this->buildAccessibleProxy($originalClassName), $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
    }


    /**
     * Creates a proxy class of the specified class which allows
     * for calling even protected methods and access of protected properties.
     *
     * @param string $className Full qualified name of the original class
     * @return string Full qualified name of the built class
     */
    protected function buildAccessibleProxy($className)
    {
        $accessibleClassName = uniqid('AccessibleTestProxy');
        $class = new ReflectionClass($className);
        $abstractModifier = $class->isAbstract() ? 'abstract ' : '';
        eval('
			' . $abstractModifier . 'class ' . $accessibleClassName . ' extends ' . $className . ' {
				public function _call($methodName) {
					$args = func_get_args();
					return call_user_func_array(array($this, $methodName), array_slice($args, 1));
				}
				public function _callRef($methodName, &$arg1 = NULL, &$arg2 = NULL, &$arg3 = NULL, &$arg4 = NULL, &$arg5= NULL, &$arg6 = NULL, &$arg7 = NULL, &$arg8 = NULL, &$arg9 = NULL) {
					switch (func_num_args()) {
						case 0 : return $this->$methodName();
						case 1 : return $this->$methodName($arg1);
						case 2 : return $this->$methodName($arg1, $arg2);
						case 3 : return $this->$methodName($arg1, $arg2, $arg3);
						case 4 : return $this->$methodName($arg1, $arg2, $arg3, $arg4);
						case 5 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5);
						case 6 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5, $arg6);
						case 7 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7);
						case 8 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7, $arg8);
						case 9 : return $this->$methodName($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7, $arg8, $arg9);
					}
				}
				public function _set($propertyName, $value) {
					$this->$propertyName = $value;
				}
				public function _setRef($propertyName, &$value) {
					$this->$propertyName = $value;
				}
				public function _get($propertyName) {
					return $this->$propertyName;
				}
			}
		');
        return $accessibleClassName;
    }

    /**
     * @return array
     */
    abstract protected function getFixtures();



    /**
     * @param $object
     */
    protected function cleanObjectState($object)
    {
        if ($object !== null) {
            $identityMap = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\Session');
            $identityMap->unregisterObject($object);
        }
    }
}
