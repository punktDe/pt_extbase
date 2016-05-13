<?php
namespace PunktDe\PtExtbase\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2016 punkt.de GmbH <extensions@punkt.de>
 *  Authors: Ursula Klinger, Joachim Mathes, Peter Bolch
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

use TYPO3\CMS\Core\SingletonInterface;

class TcaManager implements SingletonInterface
{
    /**
     * @var array
     */
    protected $deletedColumnConfiguration = [
        'deleted' => [
            'config' =>[
                'type' => 'passthrough',
            ],
        ],
    ];

    /**
     * returns the value of [TCA][$tableName][ctrl][delete] to reset the field later on with the activateDeletedFlag
     *
     * @param $tableName
     * @param null $columnConfiguration
     *
     * @return string
     */
    public function deactivateDeletedFlag($tableName)
    {
        $deleteField = $GLOBALS['TCA'][$tableName]['ctrl']['delete'];
        $GLOBALS['TCA'][$tableName]['ctrl']['delete'] = '';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($tableName, $this->deletedColumnConfiguration, 1);

        return $deleteField;
    }



    /**
     *
     *
     * @param $tableName
     * @param $deleteField
     */
    public function activateDeletedFlag($tableName, $deleteField)
    {
        $GLOBALS['TCA'][$tableName]['ctrl']['delete'] = $deleteField;
    }



    /**
     * @param $tableName
     * @param $enableColumns
     *
     * @return array
     */
    public function deactivateEnableColumns($tableName, $enableColumns)
    {
        $enableColumnsAndValues = array();

        foreach ($enableColumns as $column) {
            $enableColumnsAndValues[$column] = $GLOBALS['TCA'][$tableName]['ctrl']['enablecolumns'][$column];
            $GLOBALS['TCA'][$tableName]['ctrl']['enablecolumns'][$column] = '';
        }

        return $enableColumnsAndValues;
    }



    /**
     * @param $tableName
     * @param $enableColumns
     */
    public function setEnableColumns($tableName, $enableColumns)
    {
        foreach ($enableColumns as $column => $value) {
            $GLOBALS['TCA'][$tableName]['ctrl']['enablecolumns'][$column] = $value;
        }
    }
}
