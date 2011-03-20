<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
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
 * All Extbase-conform files are included automatically. 
 * So only files and classes from 'old' Extensions and files for Testing are 
 * listed here.
 */
$baseDir = t3lib_extMgm::extPath('pt_extbase');
$testsDir = $baseDir . 'Tests/';

return array(
	// ATTENTION: All classes must be written in lower case letters only!
    'tx_ptextbase_state_session_storageadapter' => $baseDir . 'Classes/State/Session/StorageAdapter.php',
    'tx_ptextbase_tests_state_stubs_sessionadaptermock' => $testsDir . 'State/Stubs/SessionAdapterMock.php',
    'tx_ptextbase_tests_state_stubs_persistableobject' => $testsDir . 'State/Stubs/PersistableObject.php',
    'tx_ptextbase_tests_state_stubs_getpostvarobject'  => $testsDir . 'State/Stubs/GetPostVarObject.php'
);
?>