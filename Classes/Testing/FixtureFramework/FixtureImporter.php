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

/**
 * FixtureImporter
 *
 * @package pt_extbase
 * @subpackage Testing\FixtureFramework
 */
class Tx_PtExtbase_Testing_FixtureFramework_FixtureImporter implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var array
     */
    protected $fixtures;

    /**
     * @var Tx_PtExtbase_Testing_FixtureFramework_Fixture
     */
    protected $fixture;

    /**
     * @param array $fixtures
     * @return void
     */
    public function import($fixtures)
    {
        $this->fixtures = $fixtures;
        foreach ($fixtures as $fixture) { /** @var Tx_PtExtbase_Testing_FixtureFramework_Fixture $fixture */
            $this->fixture = $fixture;
            $this->setConnection();
            if ($this->fixture->getSchemaFilePath() != "") {
                $this->importSchema();
            }
            $this->importFixture();
        }
    }

    /**
     * @return void
     */
    protected function setConnection()
    {
        $this->fixture->setConnection(
            new \PunktDe\Testing\Forked\DbUnit\Database\DefaultConnection(
                new PDO(
                    $this->fixture->getCredentials()->getDsn(),
                    $this->fixture->getCredentials()->getUsername(),
                    $this->fixture->getCredentials()->getPassword()
                ),
                $this->fixture->getCredentials()->getSchema()
            )
        );
        $this->fixture->getConnection()->getConnection()->query('SET NAMES utf8')->execute();
    }

    /**
     * @return void
     * @throws \RuntimeException
     */
    protected function importSchema()
    {
        if ($this->fixture->getSchemaFilePath() != '') {
            $schemaFilePath = Environment::getPublicPath() . '/' . $this->fixture->getSchemaFilePath();
            if (file_exists($schemaFilePath)) {
                $command[] = 'mysql';
                $command[] = '-h ' . $this->fixture->getCredentials()->getHostname();
                $command[] = '-u ' . $this->fixture->getCredentials()->getUsername();
                $command[] = '-p' . $this->fixture->getCredentials()->getPassword();
                $command[] = $this->fixture->getCredentials()->getSchema();
                $command[] = '< ' . $schemaFilePath;
                $this->runCommand(implode(' ', $command));
            } else {
                throw new \RuntimeException('Invalid schema file path ' . $schemaFilePath, 1365698869);
            }
        }
    }

    /**
     * @return void
     * @throws \RuntimeException
     */
    protected function importFixture()
    {
        if (!empty($this->fixture)) {
            if ($this->fixture instanceof Tx_PtExtbase_Testing_FixtureFramework_Fixture) {
                $this->fixture->getConnection()->getConnection()->query('SET NAMES utf8')->execute();
                $this->fixture->getSetUpOperation()->execute($this->fixture->getConnection(), $this->fixture->getDataSet());
            } else {
                throw new \RuntimeException('Invalid fixture ' . get_class($this->fixture), 1365698869);
            }
        }
    }

    /**
     * @param string $command
     * @param boolean $returnOutput
     * @return array
     * @throws \RuntimeException
     */
    protected function runCommand($command, $returnOutput = false)
    {
        $output = [];
        if ($returnOutput === true) {
            exec($command, $output, $returnValue);
        } else {
            system($command, $returnValue);
        }
        if ($returnValue !== 0) {
            throw new \RuntimeException(sprintf('Command "%s" exited with exit code %s. Aborting!', $command, $returnValue));
        }
        return $output;
    }

    /**
     * @return array
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }
}
