<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 punkt.de GmbH
 *  Authors:
 *      Oliver Klee <typo3-coding@oliverklee.de>,
 *      Michael Klapper <michael.klapper@aoemedia.de>
 *      Joachim Mathes <mathes@punkt.de>,
 *
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

/**
 * Schema importer
 *
 * This class is based on Tx_Phpunit_Database_TestCase by
 *
 * Oliver Klee <typo3-coding@oliverklee.de>,
 * Michael Klapper <michael.klapper@aoemedia.de>
 *
 * @package pt_extbase
 * @subpackage pt_extbase
 */
class Tx_PtExtbase_Selenium_FixtureFramework_SchemaImporter
{
    /**
     * @return void
     * @api
     */
    public function importSchema()
    {
        $this->importStdDb();
    }

    /**
     * Imports the data from the stddb tables.sql file.
     *
     * @return void
     * @api
     */
    public function importStdDb()
    {
        $sqlFilename = GeneralUtility::getFileAbsFileName(PATH_t3lib . 'stddb/tables.sql');
        $fileContent = GeneralUtility::getUrl($sqlFilename);
        $this->importDatabaseDefinitions($fileContent);
        $this->importCacheTables();
    }

    /**
     * @return void
     */
    protected function importCacheTables()
    {
        if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 4006000) {
            $cacheTables = \TYPO3\CMS\Core\Cache\Cache::getDatabaseTableDefinitions();
            $this->importDatabaseDefinitions($cacheTables);
        }
    }

    /**
     * Imports the ext_tables.sql statements from the given extensions.
     *
     * @param array $extensions
     *        keys of the extensions to import, may be empty
     * @param boolean $importDependencies
     *        whether to import dependency extensions on which the given extensions
     *        depend as well
     * @param array &$skipDependencies
     *        keys of the extensions to skip, may be empty, will be modified
     *
     * @return void
     */
    protected function importExtensions(
        array $extensions, $importDependencies = false, array &$skipDependencies = array()
    ) {
        $this->useTestDatabase();

        foreach ($extensions as $extensionName) {
            if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extensionName)) {
                $this->markTestSkipped(
                    'This test is skipped because the extension ' . $extensionName .
                        ' which was marked for import is not loaded on your system!'
                );
            } elseif (in_array($extensionName, $skipDependencies)) {
                continue;
            }

            $skipDependencies = array_merge($skipDependencies, array($extensionName));

            if ($importDependencies) {
                $dependencies = $this->findDependencies($extensionName);
                if (is_array($dependencies)) {
                    $this->importExtensions($dependencies, true, $skipDependencies);
                }
            }

            $this->importExtension($extensionName);
        }

        // TODO: The hook should be replaced by real clean up and rebuild the whole
        // "TYPO3_CONF_VARS" in order to have a clean testing environment.
        // hook to load additional files
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['phpunit']['importExtensions_additionalDatabaseFiles'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['phpunit']['importExtensions_additionalDatabaseFiles'] as $file) {
                $sqlFilename = GeneralUtility::getFileAbsFileName($file);
                $fileContent = GeneralUtility::getUrl($sqlFilename);

                $this->importDatabaseDefinitions($fileContent);
            }
        }
    }

    /**
     * Imports the ext_tables.sql file of the extension with the given name
     * into the test database.
     *
     * @param string $extensionName
     *        the name of the installed extension to import, must not be empty
     *
     * @return void
     */
    private function importExtension($extensionName)
    {
        $sqlFilename = GeneralUtility::getFileAbsFileName(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionName) . 'ext_tables.sql');
        $fileContent = GeneralUtility::getUrl($sqlFilename);

        $this->importDatabaseDefinitions($fileContent);
    }



    /**
     * Imports the SQL definitions from a (ext_)tables.sql file.
     *
     * @param string $definitionContent
     *        the SQL to import, must not be empty
     *
     * @return void
     */
    private function importDatabaseDefinitions($definitionContent)
    {
        if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 6002000) {
            /* @var $install \TYPO3\CMS\Install\Service\SqlSchemaMigrationService */
            $install = GeneralUtility::makeInstance('TYPO3\CMS\Install\Service\SqlSchemaMigrationService');
        } else {
            /* @var $install t3lib_install */
            $install = GeneralUtility::makeInstance('t3lib_install');
        }

        $fieldDefinitionsFile = $install->getFieldDefinitions_fileContent($definitionContent);
        if (empty($fieldDefinitionsFile)) {
            return;
        }

        // find statements to query
        $fieldDefinitionsDatabase = $install->getFieldDefinitions_fileContent($this->getTestDatabaseSchema());
        $diff = $install->getDatabaseExtra($fieldDefinitionsFile, $fieldDefinitionsDatabase);
        $updateStatements = $install->getUpdateSuggestions($diff);

        $updateTypes = array('add', 'change', 'create_table');

        foreach ($updateTypes as $updateType) {
            if (array_key_exists($updateType, $updateStatements)) {
                foreach ((array) $updateStatements[$updateType] as $string) {
                    $GLOBALS['TYPO3_DB']->admin_query($string);
                }
            }
        }
    }

    /**
     * Returns an SQL dump of the test database.
     *
     * @return string SQL dump of the test databse, might be empty
     */
    private function getTestDatabaseSchema()
    {
        $db = $this->useTestDatabase();
        $tables = $this->getDatabaseTables();

        // finds create statement for every table
        $linefeed = chr(10);

        $schema = '';
        $db->sql_query('SET SQL_QUOTE_SHOW_CREATE = 0');
        foreach ($tables as $tableName) {
            $res = $db->sql_query('show create table `' . $tableName . '`');
            $row = $db->sql_fetch_row($res);

            // modifies statement to be accepted by TYPO3
            $createStatement = preg_replace('/ENGINE.*$/', '', $row[1]);
            $createStatement = preg_replace(
                '/(CREATE TABLE.*\()/', $linefeed . '\\1' . $linefeed, $createStatement
            );
            $createStatement = preg_replace('/\) $/', $linefeed . ')', $createStatement);

            $schema .= $createStatement . ';';
        }

        return $schema;
    }

    /**
     * Finds all direct dependencies of the extension with the key $extKey.
     *
     * @param string $extKey the key of an installed extension, must not be empty
     *
     * @return array<string>|NULL
     *         the keys of all extensions on which the given extension depends,
     *         will be NULL if the dependencies could not be determined
     */
    private function findDependencies($extKey)
    {
        $path = GeneralUtility::getFileAbsFileName(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey) . 'ext_emconf.php');
        $_EXTKEY = $extKey;
        include($path);

        $dependencies = $EM_CONF[$_EXTKEY]['constraints']['depends'];
        if (!is_array($dependencies)) {
            return null;
        }

        // remove php and typo3 extension (not real extensions)
        if (isset($dependencies['php'])) {
            unset($dependencies['php']);
        }
        if (isset($dependencies['typo3'])) {
            unset($dependencies['typo3']);
        }

        return array_keys($dependencies);
    }

    /**
     * Imports a data set into the test database,
     *
     * @param string $path
     *        the absolute path to the XML file containing the data set to load
     *
     * @return void
     */
    protected function importDataSet($path)
    {
        $xml = simplexml_load_file($path);
        $db = $this->useTestDatabase();
        $foreignKeys = array();

        /** @var $table SimpleXMLElement */
        foreach ($xml->children() as $table) {
            $insertArray = array();

            /** @var $column SimpleXMLElement */
            foreach ($table->children() as $column) {
                $columnName = $column->getName();
                $columnValue = null;

                if (isset($column['ref'])) {
                    list($tableName, $elementId) = explode('#', $column['ref']);
                    $columnValue = $foreignKeys[$tableName][$elementId];
                } elseif (isset($column['is-NULL']) && ($column['is-NULL'] === 'yes')) {
                    $columnValue = null;
                } else {
                    $columnValue = $table->$columnName;
                }

                $insertArray[$columnName] = $columnValue;
            }

            $tableName = $table->getName();
            $db->exec_INSERTquery($tableName, $insertArray);

            if (isset($table['id'])) {
                $elementId = (string) $table['id'];
                $foreignKeys[$tableName][$elementId] = $db->sql_insert_id();
            }
        }
    }
}
