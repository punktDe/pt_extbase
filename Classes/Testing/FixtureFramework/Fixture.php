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

/**
 * Fixture
 *
 * @package pt_extbase
 * @subpackage Testing\FixtureFramework
 */
class Tx_PtExtbase_Testing_FixtureFramework_Fixture
{
    /**
     * @var Tx_PtExtbase_Testing_FixtureFramework_Credentials
     */
    protected $credentials;

    /**
     * @var \PunktDe\Testing\Forked\DbUnit\Database\Connection
     */
    protected $connection;

    /**
     * @var \PunktDe\Testing\Forked\DbUnit\DataSet\IDataSet
     */
    protected $dataSet;

    /**
     * @var \PunktDe\Testing\Forked\DbUnit\Operation\Operation
     */
    protected $setUpOperation;

    /**
     * @var string
     */
    protected $schemaFilePath;


    public function __construct()
    {
        $this->setUpOperation = \PunktDe\Testing\Forked\DbUnit\Operation\Factory::CLEAN_INSERT();
    }

    /**
     * Returns the test database connection.
     *
     * @return \PunktDe\Testing\Forked\DbUnit\Database\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param \PunktDe\Testing\Forked\DbUnit\Database\Connection $connection
     * @return Tx_PtExtbase_Testing_FixtureFramework_Fixture
     */
    public function setConnection(\PunktDe\Testing\Forked\DbUnit\Database\Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Returns the test dataset.
     *
     * @return \PunktDe\Testing\Forked\DbUnit\DataSet\IDataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * @param \PunktDe\Testing\Forked\DbUnit\DataSet\IDataSet $dataSet
     * @return Tx_PtExtbase_Testing_FixtureFramework_Fixture
     */
    public function setDataSet(\PunktDe\Testing\Forked\DbUnit\DataSet\IDataSet $dataSet)
    {
        $this->dataSet = $dataSet;
        return $this;
    }

    /**
     * Returns the database operation executed in test setup.
     *
     * @return \PunktDe\Testing\Forked\DbUnit\Operation\Operation
     */
    public function getSetUpOperation()
    {
        return $this->setUpOperation;
    }

    /**
     * @param \PunktDe\Testing\Forked\DbUnit\Operation\Operation $setUpOperation
     * @return Tx_PtExtbase_Testing_FixtureFramework_Fixture
     */
    public function setSetUpOperation(\PunktDe\Testing\Forked\DbUnit\Operation\Operation $setUpOperation)
    {
        $this->setUpOperation = $setUpOperation;
        return $this;
    }

    /**
     * @return string
     */
    public function getSchemaFilePath()
    {
        return $this->schemaFilePath;
    }

    /**
     * @param string $schemaFilePath
     * @return Tx_PtExtbase_Testing_FixtureFramework_Fixture
     */
    public function setSchemaFilePath($schemaFilePath)
    {
        $this->schemaFilePath = $schemaFilePath;
        return $this;
    }

    /**
     * @return \Tx_PtExtbase_Testing_FixtureFramework_Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param \Tx_PtExtbase_Testing_FixtureFramework_Credentials $credentials
     * @return Tx_PtExtbase_Testing_FixtureFramework_Fixture
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
        return $this;
    }
}
