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

namespace PunktDe\PtExtbase\SqlGenerator\Generator;

use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Abstract Sql Generator
 *
 * @package pt_dppp_pbsurvey
 * @subpackage Domain\SqlGenerator\AbstractSqlGenerator
 */

abstract class AbstractTemporaryTablePhpGenerator implements \Tx_PtExtbase_SqlGenerator_SqlGeneratorInterface
{
    /**
     * @const TEMP_PREFIX
     */
    const TEMP_PREFIX = 'zzzz_temp_';

    /**
     * @const BACKUP_PREFIX
     */
    const BACKUP_PREFIX = 'zzzz_bck_';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $extensionName = '';

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $temporaryTableName = '';

    /**
     * @var string
     */
    protected $backupTableName = '';

    /**
     * @var string
     */
    protected $columnDefinitions;

    /**
     * @var string
     */
    protected $dropTableQueryTemplate = "
		DROP TABLE IF EXISTS %s;
	";

    /**
     * @var string
     */
    protected $tableCreationQueryTemplate = "
		CREATE TABLE IF NOT EXISTS `%s` (
			`uid` integer(11) NOT NULL AUTO_INCREMENT,
			%s
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

    /**
     * @var string
     */
    protected $switchTablesQueryTemplate = "RENAME TABLE %s TO %s, %s TO %s;";

    /**
     * @param ObjectManager $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return void
     */
    public function initializeObject()
    {
        if ($this->extensionName === '') {
            throw new \Exception('Please provide an extension name in the generator', 1388661061);
        }
        $this->initializeExtbase();
        $this->setTableNames();
    }

    /**
     * @return void
     */
    protected function initializeExtbase()
    {
        $configuration['extensionName'] = $this->extensionName;
        $configuration['pluginName'] = 'dummy';
        $extbaseBootstrap = $this->objectManager->get((\TYPO3\CMS\Extbase\Core\Bootstrap::class)); /** @var \TYPO3\CMS\Extbase\Core\Bootstrap $extbaseBootstrap  */
        $extbaseBootstrap->initialize($configuration);
    }

    /**
     * @return void
     */
    protected function setTableNames()
    {
        $this->temporaryTableName = self::TEMP_PREFIX . $this->tableName;
        $this->backupTableName = self::BACKUP_PREFIX . $this->tableName;
    }

    /**
     * @return string
     */
    protected function buildCreateTableSql()
    {
        return sprintf($this->tableCreationQueryTemplate, $this->tableName, $this->columnDefinitions);
    }

    /**
     * @return string
     */
    protected function buildDropTemporaryTableSql()
    {
        return sprintf($this->dropTableQueryTemplate, $this->temporaryTableName);
    }

    /**
     * @return string
     */
    protected function buildDropBackupTableSql()
    {
        return sprintf($this->dropTableQueryTemplate, $this->backupTableName);
    }

    /**
     * @return string
     */
    protected function buildSwitchTableSql()
    {
        return sprintf($this->switchTablesQueryTemplate, $this->tableName, $this->backupTableName, $this->temporaryTableName, $this->tableName);
    }

    /**
     * @return string
     */
    protected function buildCreateTemporaryTableSql()
    {
        return sprintf($this->tableCreationQueryTemplate, $this->temporaryTableName, $this->columnDefinitions);
    }

    /**
     * @return array
     */
    public function generate()
    {
        $sqls = [];
        $this->columnDefinitions = $this->buildColumnDefinitions();
        $sqls[] = $this->buildCreateTableSql();
        $sqls[] = $this->buildDropTemporaryTableSql();
        $sqls[] = $this->buildCreateTemporaryTableSql();
        $sqls[] = $this->buildFillTemporaryTableSqls();
        $sqls[] = $this->buildDropBackupTableSql();
        $sqls[] = $this->buildSwitchTableSql();
        return $sqls;
    }

    /**
     * @return string
     */
    abstract protected function buildColumnDefinitions();

    /**
     * @return string
     */
    abstract protected function buildFillTemporaryTableSqls();
}
